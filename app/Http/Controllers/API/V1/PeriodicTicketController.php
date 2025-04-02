<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ComplianceEntryRequest;
use App\Models\ComplianceEntry;
use App\Services\ComplianceEntryService;
use App\Services\PeriodicTicketService;
use App\Services\TicketCreationApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PeriodicTicketController extends Controller
{

    protected $service;
    protected $ticketCreationApiService;

    /**
     * PeriodicTicketController constructor.
     * @param PeriodicTicketService $periodicTicketService
     */
    public function __construct(PeriodicTicketService $service,TicketCreationApiService $ticketCreationApiService)
    {
        $this->service = $service;
        $this->ticketCreationApiService = $ticketCreationApiService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPeriodicTicket(Request $request)
    {
        return $this->service->create($request->all());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function periodicTicketStatus(Request $request)
    {
        return $this->service->updateTicketStatus($request->all());
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getToken(Request $request)
    {
        return $this->service->getToken();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getTokenInfo(Request $request)
    {
        return $this->service->getTokenInfo($request->all());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function updatePeriodicTicketDueDate(Request $request)
    {
        return $this->service->updatePeriodicTicketDueDate($request->all());
    }

    /**
     * @return string
     */
    public function updatePeriodicTicketTicketId()
    {
        \Artisan::call("update-periodic-ticket-ticket-id");
        return 'done';
    }
    public function period($frequency,$dueDate)
    {
        $complianceEntry=ComplianceEntry::find(917);
        return $this->ticketCreationApiService->ticketPeriod($complianceEntry);
//        return $this->ticketCreationApiService->getPeriodText($frequency,$dueDate);
    }
}
