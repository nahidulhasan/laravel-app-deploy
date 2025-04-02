<?php

namespace App\Services;

use App\Models\ComplianceEntry;
use App\Models\PeriodicTicket;
use App\Repositories\ComplianceEntryRepository;

use App\Traits\CrudTrait;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ComplianceEntryService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var ComplianceEntryRepository
     */
    protected $repository;

    /**
     * @var FormFieldMappingService
     */
    protected $formFieldMappingService;
    protected $ticketCreationApiService;

    /**
     * Compliance Entry Repository
     */
    public function __construct(ComplianceEntryRepository $repository, FormFieldMappingService $formFieldMappingService, TicketCreationApiService $ticketCreationApiService)
    {
        $this->repository = $repository;
        $this->formFieldMappingService = $formFieldMappingService;
        $this->ticketCreationApiService = $ticketCreationApiService;
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
        if (isset($requestData['search']) && !empty($requestData['search'])) {
            $inputValue = $requestData['search'];
            return ComplianceEntry::where(['ticket_id' => $inputValue])
                ->orWhere(['regulatory_body' => $inputValue])
                ->orWhere(['compliance_point_no' => $inputValue])
                ->orWhere(['compliance_level' => $inputValue])
                ->orWhere(['compliance_category' => $inputValue])
                ->orWhere(['compliance_sub_category' => $inputValue])
                ->orWhere(['compliance_point_description' => $inputValue])
                ->orWhere(['instruction_type' => $inputValue])
                ->orWhere(['document_subject' => $inputValue])
                ->orWhere(['document_date' => $inputValue])
                ->orWhere(['start_date' => $inputValue])
                ->orWhere(['frequency' => $inputValue])
                ->orWhere(['due_date' => $inputValue])
                ->orWhere(['due_month' => $inputValue])
                ->orWhere(['next_due_date' => $inputValue])
                ->orWhere(['compliance_owner' => $inputValue])
                ->orWhere(['status' => $inputValue])
                ->orderBy('id', 'DESC')
                ->paginate(20);

        } else {
            return $this->repository->findAll(20, null, array('column' => 'updated_at', 'direction' => 'desc'));
        }

    }

    /**
     * @param $id
     * @return \App\Repositories\Collection|
     * \Illuminate\Database\Eloquent\Builder|
     * \Illuminate\Database\Eloquent\Builder[]|
     * \Illuminate\Database\Eloquent\Model|
     * \Illuminate\Database\Eloquent\Model[]|mixed
     */
    public function view($id)
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * store method
     *
     * @param array $requestData
     * @return void
     */
    public function store(array $requestData)
    {
        $requestData['is_reopen'] = false;
        try {
            if (!isset($requestData['ticket_id']) || empty($requestData['ticket_id'])) {
                throw new Exception('Ticket Id Not Found In Request');
            }

            $this->formFieldMappingService->checkForNewFields($requestData);

            $model = $this->repository->getModel()->updateOrCreate(
                ['ticket_id' => $requestData['ticket_id']],
                $requestData
            );
            return $this->sendSuccessResponse($model, 'Compliance Entry Saved Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * update next due date
     * @param array $requestData
     * @return void
     */
    public function updateNextDueDate(array $requestData)
    {
        try {
            if (!isset($requestData['ticket_id']) || empty($requestData['ticket_id'])) {
                throw new Exception('Ticket Id Not Found In Request');
            }

            $complianceEntry = $this->repository->getLastComplianceEntry($requestData['ticket_id']);

            if (!$complianceEntry) {
                throw new Exception('No Compliance Entry Found With This Ticket Id: ' . $requestData['ticket_id']);
            }
            if (!isset($requestData['frequency']) || empty($requestData['frequency'])) {
                throw new Exception('Frequency Not Found In Compliance Entry');
            }
            $startDate = $requestData['start_date'];
            $changedStartDate = $startDate;
            $requestData['is_reopen'] = false;
            if (isset($requestData['changed_start_date']) && !empty($requestData['changed_start_date'])) {
                $changedStartDate = $requestData['changed_start_date'];
                $requestData['is_reopen'] = true;
                unset($requestData['changed_start_date']);
            }
            $frequency = $requestData['frequency'];
            $dueDate = $requestData['due_date'];
            $dueMonth = $requestData['due_month'];
            $requestData['next_due_date'] = $this->getNextDueDate($changedStartDate, $frequency, $dueMonth, $dueDate);
            $model = $complianceEntry->update($requestData);
            return $this->sendSuccessResponse($model, 'Compliance Entry Updated Successfully!');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            Log::eror($exception->getTraceAsString());
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $regulatoryBody
     * @return \Illuminate\Http\JsonResponse
     */
    public function compliancePointNo($regulatoryBody)
    {
        try {
            if (!isset($regulatoryBody) || !isset($regulatoryBody)) {
                throw new Exception('Regulatory Body is Required');
            }
            $compliancePointNo = $this->generateCompliancePointNo($regulatoryBody);
            return $this->sendSuccessResponse($compliancePointNo, 'Regulatory Body point generated Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $regulatoryBody
     * @return mixed
     */
    private function generateCompliancePointNo($regulatoryBody)
    {
        $count = $this->countComplianceEntry($regulatoryBody);
        $id = str_pad($count + 1, 5, '0', STR_PAD_LEFT);
        $year = date('y');
        $month = date('m');
        $day = date('d');
        $compliancePointNo['compliancePointNo'] = sprintf('%s-%s%s%s-%s', strtoupper($regulatoryBody), $day, $month, $year, $id);
        return $compliancePointNo;
    }

    /**
     * @param string|null $startDate
     * @param string $frequency
     * @param string|null $dueMonth
     * @param string|null $dueDate
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function calculateNextDueDate(string $startDate = null, string $frequency, string $dueMonth = null, string $dueDate = null)
    {
        $nextDueDate = $this->getNextDueDate($startDate, $frequency, $dueMonth, $dueDate);
        return $this->sendSuccessResponse($nextDueDate, 'Next Due Date Calculated !');
    }

    /**
     * @param string|null $startDate
     * @param string $frequency
     * @param int|null $dueMonth
     * @param int|null $dueDate
     * @return string|void
     * @throws Exception
     */
    public function getNextDueDate(string $startDate = null, string $frequency, int $dueMonth = null, int $dueDate = null)
    {
        $today = $this->getToday($startDate);
        $frequency = strtolower($frequency);
        switch ($frequency) {
            case 'monthly':
                if (is_null($dueDate)) {
                    throw new Exception('For Monthly Frequency, Due Date Is Required !');
                }
                return $this->getMonthlyNextDueDate($today, $dueDate);
            case 'quarterly':
                if (is_null($dueDate) || is_null($dueMonth)) {
                    throw new Exception('For Quarterly Frequency, Due Date and Due Month Is Required !');
                }
                return $this->getNextQuarterDate($today, $dueMonth, $dueDate);
            case 'yearly':
                if (is_null($dueDate) || is_null($dueMonth)) {
                    throw new Exception('For Yearly Frequency, Due Date and Due Month Is Required !');
                }
                return $this->getYearlyNextDueDate($today, $dueDate, $dueMonth);
            case 'daily':
                $nextDueDate = $today->add(1, 'day');
                $month = $nextDueDate->month;
                $year = $nextDueDate->year;
                $day = $nextDueDate->day;
                return $this->getDate($day, $month, $year);
            case 'weekly':
                if (is_null($dueDate)) {
                    throw new Exception('For Weekly Frequency, Due Date Is Required !');
                }
                return $this->getWeeklyNextDueDate($today, $dueDate, $dueMonth);
            case 'fortnightly':
                if (is_null($dueDate)) {
                    throw new Exception('For Fortnightly Frequency, Due Date Is Required !');
                }
                return $this->getFortnightlyNextDueDate($today, $dueDate);
            default:
                return Carbon::now()->format('Y-m-d');
        }
    }

    /**
     * @param Carbon $today
     * @param int|null $dueMonth
     * @param int|null $dueDate
     */
    public function getNextQuarterDate(Carbon $today, int $dueMonth = null, int $dueDate = null)
    {
        // as 1,2,3 will be actually 0,1,2 we are substracting a month
        return $this->calculateNextQuarterDate($today, $dueMonth - 1, $dueDate);
    }

    /**
     * check compliance point no
     * @param array $data
     * @return void
     */
    public function checkCompliancePointNo(array $data)
    {
        try {
            $count = $this->repository->getCompliancePointCount($data);
            if ($count >= 1) {
                $newPoint = $this->generateCompliancePointNo($data['regulatory_body']);
                return $this->sendSuccessResponse($newPoint, 'Compliance Point No Updated Successfully!');
            }
            return $this->sendErrorResponse('Compliance Point No Not Updated!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     *  calculate the next quarter date
     *
     * @param Carbon $currentDate
     * @param integer $dueMonth
     * @param integer $dueDate
     * @return void
     */
    private function calculateNextQuarterDate(Carbon $currentDate, int $dueMonth = null, int $dueDate = 1)
    {
        $originalDate = clone $currentDate;
        $currentQuarterMonth = $currentDate->startOfQuarter()->firstOfMonth()->add($dueMonth, 'month');
        $currentQuarterDate = Carbon::create($currentQuarterMonth->year, $currentQuarterMonth->month, $dueDate, 0, 0, 0);
        if ($currentQuarterDate->greaterThan($originalDate)) {
            return $this->getDate($dueDate, $currentQuarterDate->month, $currentQuarterDate->year);
        } else {
            $nextQuarter = $originalDate->addQuarter()->startOfQuarter();
            $firstDayOfNextQuarter = $nextQuarter->firstOfMonth();
            $nextDueDate = $firstDayOfNextQuarter->add($dueMonth, 'month');
            $year = $nextDueDate->year;
            $month = $nextDueDate->month;
            return $this->getDate($dueDate, $month, $year);
        }
    }

    /**
     * get formatted date
     *
     * @param integer $day
     * @param integer $month
     * @param integer $year
     * @return void
     */
    private function getDate(int $day, int $month, int $year)
    {
        if (checkdate($month, $day, $year)) {
            return Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
        }
        // if it doesn't exist get to the next month, and sub 1 day to get the last date of a month
        $nextMonthDay = Carbon::createFromDate($year, $month + 1, 1);
        return $nextMonthDay->subDay(1)->format('Y-m-d');
    }

    /**
     * Undocumented function
     *
     * @param string|null $today
     * @return Carbon
     */
    private function getToday(string $today = null): Carbon
    {
        if (is_null($today)) {
            return Carbon::now();
        }
        return Carbon::parse($today);
    }

    /**
     * @param string $regulatoryBody
     * @return int
     */
    private function countComplianceEntry(string $regulatoryBody)
    {
        $query = $this->repository->getModel()->newQuery();
        $query->where('regulatory_body', $regulatoryBody);
        $complianceEntry = $query->get()->count();
        return $complianceEntry;
    }


    /**
     * calculate next due date for weekly
     *
     * @param $today
     * @param $dueDate
     * @param $dueMonth
     * @return string
     */
    private function getWeeklyNextDueDate($today, $dueDate, $dueMonth)
    {
        $days = [
            'Sunday' => 1,
            'Monday' => 2,
            'Tuesday' => 3,
            'Wednesday' => 4,
            'Thursday' => 5,
            'Friday' => 6,
            'Saturday' => 7
        ];
        $expectedDay = array_search($dueDate, $days);
        return $today->modify("next " . $expectedDay)->format('Y-m-d');
    }

    /**
     * calculatge fortnightly next due date
     *
     * @param $today
     * @param $dueDate
     * @return void
     */
    private function getFortnightlyNextDueDate($today, $dueDate)
    {
        $currentDate = $today->day;
        $quarterDate = $dueDate; // range 1-15
        $year = $today->year;
        $month = $today->month;
        if ($quarterDate > $currentDate) {
            // that means its ahead
            return $this->getDate($quarterDate, $month, $year);
        } else {
            // that means we already past it
            if ($currentDate < 15) {
                return $this->getDate(15 + $quarterDate, $month, $year);
            }
            // now we are in the second quarter of the month
            if ($quarterDate + 15 > $currentDate) {
                return $this->getDate(15 + $quarterDate, $month, $year);
            }
            // its in the next month
            return $this->getDate($quarterDate, $month + 1, $year);
        }
    }

    /**
     * get monthly next due date
     *
     * @param [type] $today
     * @param [type] $dueDate
     * @return void
     */
    private function getMonthlyNextDueDate($today, $dueDate)
    {
        $date = $today->day;
        if ($dueDate > $date) {
            return $this->getDate($dueDate, $today->month, $today->year);
        }
        $nextDueDate = $today->addMonthNoOverflow(1, 'month');
        return $this->getDate($dueDate, $nextDueDate->month, $nextDueDate->year);
    }

    /**
     * Undocumented function
     *
     * @param [type] $today
     * @param [type] $dueDate
     * @return void
     */
    private function getYearlyNextDueDate($today, $dueDate, $dueMonth)
    {
        $month = $today->month;

        if ($dueMonth > $month) {
            return $this->getDate($dueDate, $dueMonth, $today->year);
        }

        if ($dueMonth < $month) {
            $nextDueDate = $today->add(1, 'year');
            return $this->getDate($dueDate, $dueMonth, $nextDueDate->year);
        }
        // if its in the same month, then just check the dates
        if ($dueDate > $today->day) {
            return $this->getDate($dueDate, $dueMonth, $today->year);
        }
        $nextDueDate = $today->add(1, 'year');
        return $this->getDate($dueDate, $nextDueDate->month, $nextDueDate->year);
    }

    /**
     * @param $owners
     * @return array
     */
    public function ownerGroup($owners,$type=null)
    {
        $groupIds = [];
        $response = $this->ticketCreationApiService->getUsersGroup($owners,$type);
        if (isset($response['status']) && $response['status'] == 'SUCCESS' && $response['status_code'] == 200) {
            if (!empty($response['data'])) {
                $groupIds = array_column($response['data'], 'id');
            }
        }
        return $groupIds;

    }

    /**
     * @param array $owners
     * @return array
     */
    public function getTicketIdByComplienceOwners(array $owners, $data)
    {
        $dueDateType = $data['due_date'];
        if ($data['ticket_status'] == 'null') {
            $data['ticket_status'] = '';
        }
        $ticketStatus = $data['ticket_status'];
        $workflow_type = $data['workflow_type'];
        $groupId = $data['group_id'];
        $regulatoryBody = $data['regulatory_body'];
        $query = $this->repository->getModel()->newQuery();
        $query->select(['id', 'ticket_id', 'compliance_group_id', 'compliance_owner', 'regulatory_body', 'status', 'due_date']);
        if ($workflow_type == 'compliance_entry' && !empty($ticketStatus) && $ticketStatus != 'All') {
            $query->where('status', $ticketStatus);
        }
        if (!empty($ticketStatus)) {
//            $data['type'] = null;
//            $data['role'] = null;
        }
        if ($workflow_type == 'periodic_tickets') {
            $query->with(['periodicTicket' => function ($q) use ($dueDateType) {
                $q->select(['periodic_tickets.compliance_entry_id', 'periodic_tickets.due_date', 'periodic_tickets.periodic_ticket_id']);
                $today = date('Y-m-d');
                if ($dueDateType == 'after') {
                    $q->where('due_date', '<', $today);
                }
                if ($dueDateType == 'before') {
                    $q->where('due_date', '>=', $today);
                }
            }]);
        }
        if ($data['type'] == 'group' && $data['role'] == 'compliance_owner') {
            if (!empty($groupId)) {
                $query->where('compliance_group_id', $groupId);
            } else {
                $getOwnerGroup = $this->ownerGroup($owners);
                if (!empty($getOwnerGroup)) {
                    $query->orwhereIn('compliance_group_id', $getOwnerGroup);
                }
            }
        } elseif ($data['type'] == 'group' && $data['role'] == 'FAP') {
            if (!empty($groupId)) {
                $query->where('fap_group_id', $groupId);
            } else {
                $getOwnerGroup = $this->ownerGroup($owners,'FAP');
                Log::info('$getOwnerGroup');
                Log::info(\GuzzleHttp\json_encode($getOwnerGroup));
                if (!empty($getOwnerGroup)) {
                    $query->whereIn('fap_group_id', $getOwnerGroup);
//                    $query->whereNotNull('fap_group_id');
                }
            }
        } elseif ($data['type'] == 'group' && $data['role'] == 'line_manager') {
            if (!empty($groupId)) {
                $query->where('compliance_group_id', $groupId);
            } else {
                $getOwnerGroup = $this->ownerGroup($owners);
                if (!empty($getOwnerGroup)) {
                    $query->orwhereIn('compliance_group_id', $getOwnerGroup);
                }
                $query->orwhereIn('compliance_owner', $owners);
            }
        } elseif ($data['type'] == 'department' && $data['role'] == 'emt') {
            if (!empty($groupId)) {
                $query->where('compliance_group_id', $groupId);
            } else {
                $getOwnerGroup = $this->ownerGroup($owners);
                if (!empty($getOwnerGroup)) {
                    $query->orwhereIn('compliance_group_id', $getOwnerGroup);
                }
                $query->orwhereIn('compliance_owner', $owners);
            }
        } else {
            if (!empty($data['type'])) {
                $query->whereIn('compliance_owner', $owners);
            }
        }
        if (!empty($regulatoryBody)) {
            $query->where('regulatory_body', $regulatoryBody);
        }
        $complianceEntry = $query->get();
        $periodicTickets = [];
        if ($workflow_type == 'periodic_tickets') {
            if (!empty($complianceEntry)) {
                foreach ($complianceEntry as $compliance) {
                    if (empty($compliance->periodicTicket)) {
                        continue;
                    }
                    foreach ($compliance->periodicTicket as $item) {
                        $periodicTickets[] = $item->periodic_ticket_id;
                    }
                }
            }
        }
        $compliance_entry = [];
        if ($workflow_type == 'compliance_entry') {
            $compliance_entry = array_column($complianceEntry->toArray(), 'ticket_id');
        }
        $result['compliance_entry_count'] = count($compliance_entry);
        $result['compliance_entry'] = $compliance_entry;
        $result['periodic_tickets_count'] = count($periodicTickets);
        $result['periodic_tickets'] = $periodicTickets;
        return $result;
    }

    /**
     * @param $request
     * @return string
     */
    public function updateFields($request)
    {
        try {
            $result = $this->callOneGpLoginApi($request);
            if (!$result) {
                return 'One Gp Login API Not Response';
            }
            $response = $this->callOneGpEmployeeApi($request, $result);
            $userByEmailKey = $this->getUserByEmailKey($response);
            // update complianceEntries
            $this->updateComplianceEntries($userByEmailKey);
            return 'Compliance Entry Data updated Successfully !!!';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $request
     * @return bool|mixed
     */
    private function callOneGpLoginApi($request)
    {
        $oneGpLoginApi = Http::withHeaders([
            'Accept' => 'application/json',
        ])->withoutVerifying()->post($request['base_url'] . "/api/v1/users/token", [
            'client_id' => $request['client_id'],
            'client_secret' => $request['client_secret'],
            'grant_type' => 'client_credentials',
        ]);
        if ($oneGpLoginApi->successful()) {
            return json_decode($oneGpLoginApi->body());
        }
        Log::error('OneGp API Login Log');
        Log::error(json_decode($oneGpLoginApi->body()));
        return false;
    }

    /**
     * @param $request
     * @param $result
     * @return bool|mixed
     */
    private function callOneGpEmployeeApi($request, $result)
    {
        $oneGpEmployeeApi = Http::withHeaders([
            'Accept' => 'application/json',
            "Authorization" => $result->data->token_type . " " . $result->data->access_token,
        ])->withoutVerifying()->post($request['base_url'] . "/api/v1/employees/_bulk", []);
        if ($oneGpEmployeeApi->successful()) {
            return $oneGpEmployeeApi->json();
        }
        Log::error('OneGp Employee api log');
        Log::error($oneGpEmployeeApi->json());
        return false;
    }

    /**
     * @param $response
     */
    private function getUserByEmailKey($response)
    {
        $userByEmailKey = [];
        foreach ($response['data'] as $user) {
            $userByEmailKey[$user['email_address']] = $user;
        }
        return $userByEmailKey;
    }

    /**
     * @param $userByEmailKey
     */
    private function updateComplianceEntries($userByEmailKey)
    {
        // Retrieve compliance entries in chunks
        ComplianceEntry::whereNotNull('compliance_owner')->chunk(500, function ($complianceEntries) use ($userByEmailKey) {
            foreach ($complianceEntries as $complianceEntry) {
                if (isset($userByEmailKey[$complianceEntry->compliance_owner])) {
                    $userData = $userByEmailKey[$complianceEntry->compliance_owner];
                    $complianceEntry->division_name = $userData['division_name'];
                    $complianceEntry->department_name = $userData['department_name'];
                    $complianceEntry->division_id = $userData['division_id'];
                    $complianceEntry->department_id = $userData['department_id'];
                    $complianceEntry->save(); // Use save instead of update

                    foreach ($complianceEntry->periodicTicket as $periodicTicket) {
                        $periodicTicket->division_name = $userData['division_name'];
                        $periodicTicket->department_name = $userData['department_name'];
                        $periodicTicket->division_id = $userData['division_id'];
                        $periodicTicket->department_id = $userData['department_id'];
                        $periodicTicket->save(); // Use save instead of update
                    }
                }
            }
        });
    }
}
