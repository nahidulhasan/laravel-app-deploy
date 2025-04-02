<?php

namespace App\Http\Controllers\API\V1;

use App\Models\CeoCxoReport;
use App\Services\CeoCxoReportService;
use Illuminate\Http\Request;
use URL;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

class CeoCxoReportController extends Controller
{

    protected $service;


    /**
     * PeriodicTicketController constructor.
     * @param PeriodicTicketService $periodicTicketService
     */
    public function __construct(CeoCxoReportService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        return $this->service->getCeoCxoReport($request->all());
    }

    public function show($id)
    {
        return $this->service->getCeoCxoReportView($id);
    }

    public function updateEmailStatus(Request $request)
    {

        return $this->service->updateEmailStatus($request->all());
    }

}
