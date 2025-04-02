<?php

namespace App\Services;

use App\Models\PeriodicTicket;
use App\Repositories\ComplianceEntryRepository;
use App\Repositories\PeriodicTicketRepository;
use App\Traits\CrudTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class PeriodicTicketService extends ApiBaseService
{

    use CrudTrait;

    protected $complianceEntryRepository;
    protected $ticketCreationApiService;
    protected $complianceEntryService;
    protected $periodicTicketRepository;
    protected $activityLogService;

    /**
     * PeriodicTicketService constructor.
     * @param ComplianceEntryRepository $complianceEntryRepository
     * @param TicketCreationApiService $ticketCreationApiService
     * @param ComplianceEntryService $complianceEntryService
     * @param PeriodicTicketRepository $periodicTicketRepository
     * @param ActivityLogService $activityLogService
     */
    public function __construct(ComplianceEntryRepository $complianceEntryRepository,
                                TicketCreationApiService  $ticketCreationApiService,
                                ComplianceEntryService    $complianceEntryService,
                                PeriodicTicketRepository  $periodicTicketRepository,
                                ActivityLogService        $activityLogService)
    {
        $this->complianceEntryRepository = $complianceEntryRepository;
        $this->ticketCreationApiService = $ticketCreationApiService;
        $this->complianceEntryService = $complianceEntryService;
        $this->periodicTicketRepository = $periodicTicketRepository;
        $this->activityLogService = $activityLogService;
    }

    /**
     * @param array $requestData
     * @return \App\Repositories\Collection|
     * \App\Repositories\Contracts\Collection|
     * \Illuminate\Contracts\Pagination\LengthAwarePaginator|
     * \Illuminate\Database\Eloquent\Builder[]|
     * \Illuminate\Database\Eloquent\Model[]
     */
    public function index(array $requestData)
    {
        if (!empty($requestData['search'])) {
            $search = trim($requestData['search']);
            $query = PeriodicTicket::query();
            $query->where(function ($query) use ($search) {
                $query->whereHas('complianceOwner', function ($q) use ($search) {
                    $q->where('compliance_owner', 'like', "%{$search}%")
                        ->orWhere('compliance_group_id', 'like', "%{$search}%");
                })
                    ->orWhere('periodic_ticket_id', 'like', "%{$search}%")
                    ->orWhere('compliance_entry_id', 'like', "%{$search}%")
                    ->orWhere('due_date', 'like', "%{$search}%");
            });

            $query->with(['complianceOwner']);
            $query->orderBy('id', 'DESC');
            return $query->paginate(20);
        } else {
            return $this->periodicTicketRepository->findAll(20, 'complianceEntry', ['column' => 'updated_at', 'direction' => 'desc']);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create($request)
    {
        try {
            if (isset($request['ticket_id']) && !empty($request['ticket_id'])) {
                $complianceEntryes = $this->complianceEntryRepository->findByProperties(['ticket_id' => $request['ticket_id']]);
            } else {
                $complianceEntryes = $this->complianceEntryRepository->getComplianceEntry();
            }

            if (count($complianceEntryes) > 0) {
                foreach ($complianceEntryes as $complianceEntrye) {
                    $response = $this->ticketCreationApiService->createTicket($complianceEntrye);
                    $activityLog['title'] = 'DWE ticket create';
                    $activityLog['type'] = 'success';
                    $activityLog['message'] = 'DWE periodic ticket created successfully!.';
                    $activityLog['payload'] = json_encode($response);
                    $activityLog['compliance_entry_id'] = $complianceEntrye->id;
                    if (isset($response['status']) && $response['status'] == 'SUCCESS' && $response['status_code'] == 200) {
                        $complianceEntrye['ticket_id_pr'] = $response['data']['ticket_id'];
                        $dweCreatedTicket = $response['data'];
                        $activityLog['ticket_id'] = $dweCreatedTicket['ticket_id'];
                        $this->activityLogService->store($activityLog);
                        $this->createPeriodicTicket($dweCreatedTicket, $complianceEntrye);
                    } else {
                        $activityLog['title'] = 'DWE ticket create';
                        $activityLog['type'] = 'error';
                        $activityLog['message'] = 'DWE ticket create failed!.';
                        $this->activityLogService->store($activityLog);
                    }
                }
                return $this->sendSuccessResponse([], 'Periodic Ticket created Successfully!');
            } else {
                return $this->sendSuccessResponse([], 'No Compliance Entry available for Periodic Ticket!');
            }
        } catch (Exception $exception) {
            Log::info('Periodicticker service : crete =' . json_encode($exception->getMessage()));
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $dweCreatedTicket
     * @param $complianceEntrye
     * @throws Exception
     */
    public function createPeriodicTicket($dweCreatedTicket, $complianceEntrye)
    {
        $periodicTicket['compliance_entry_id'] = $complianceEntrye['id'];
        $periodicTicket['periodic_ticket_id'] = $dweCreatedTicket['serial'];
        $periodicTicket['due_date'] = $complianceEntrye['next_due_date'];
        if (isset($complianceEntrye['ticket_id_pr']) && !empty($complianceEntrye['ticket_id_pr'])) {
            $periodicTicket['ticket_id'] = $complianceEntrye['ticket_id_pr'];
            unset($complianceEntrye['ticket_id_pr']);
        }
        $activityLog['title'] = 'RCMS  periodic Ticket';
        $activityLog['type'] = 'success';
        $activityLog['message'] = 'RCMS periodic ticket created successfully!.';
        $activityLog['compliance_entry_id'] = $complianceEntrye['id'];
        $periodicTicket = $this->periodicTicketRepository->save($periodicTicket);
        $activityLog['payload'] = json_encode($periodicTicket);
        if (!$periodicTicket) {
            $activityLog['type'] = 'error';
            $activityLog['massage'] = 'RCMS periodic ticket create failed!.';
        }
        $this->activityLogService->store($activityLog);
        $this->updateNextDueDate($complianceEntrye);
    }

    /**
     * @param $input
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTicketStatus($input)
    {
        try {
            $id = $input['ticket_id'];
            $status = $input['status'];
            if (!empty($id) && !empty($status)) {
                $periodicTicket = $this->periodicTicketRepository->findOneBy(['periodic_ticket_id' => $id], null);
                if (!empty($periodicTicket->id)) {
                    $periodicTicket->status = $status;
                    if ($periodicTicket->save()) {
                        return $this->sendSuccessResponse([], 'Periodic Ticket status update successfully!');
                    } else {
                        return $this->sendErrorResponse('Something went wrong. Please try again');
                    }
                } else {
                    return $this->sendErrorResponse('Periodic ticket not fund', [], 404);
                }
            } else {
                return $this->sendErrorResponse('Input data validation error');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param array $requestData
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePeriodicTicketDueDate(array $requestData)
    {
        try {
            if (!isset($requestData['ticket_id']) || empty($requestData['ticket_id'])) {
                throw new Exception('Ticket Id Not Found In Request');
            }

            $complianceEntry = $this->periodicTicketRepository->findOneBy(['periodic_ticket_id' => $requestData['ticket_id']], null);
            if (!$complianceEntry) {
                throw new Exception('No Periodic Ticket Found With This Ticket Id: ' . $requestData['ticket_id']);
            }
//            $requestData['due_date'] = $requestData['due_date'];
            $complianceEntry->due_date = $requestData['due_date'];
            $model = $complianceEntry->update();
            return $this->sendSuccessResponse($complianceEntry, 'Periodic Ticket Updated Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $complianceEntrye
     * @throws Exception
     */
    public function updateNextDueDate($complianceEntrye)
    {
        $nextDueDate = $this->complianceEntryService->getNextDueDate($complianceEntrye->next_due_date, $complianceEntrye->frequency, $complianceEntrye->due_month, $complianceEntrye->due_date);
        $complianceEntrye->next_due_date = $nextDueDate;
        $complianceEntrye->update();
        $activityLog['title'] = 'Next Due Date Update';
        $activityLog['type'] = 'success';
        $activityLog['message'] = 'Next Due Date Updated successfully!.';
        $activityLog['compliance_entry_id'] = $complianceEntrye->id;
        $activityLog['payload'] = $complianceEntrye;
        if (!$complianceEntrye->update()) {
            $activityLog['type'] = 'error';
            $activityLog['massage'] = 'Next Due Date Update failed!.';
        }
        $this->activityLogService->store($activityLog);

    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getToken()
    {
        return $this->ticketCreationApiService->getToken();
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function getTokenInfo($input)
    {
        return $this->ticketCreationApiService->getTokenInfo($input);
    }


}
