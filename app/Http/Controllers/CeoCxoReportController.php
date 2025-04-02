<?php

namespace App\Http\Controllers;

use App\Models\CeoCxoReport;
use App\Services\CeoCxoReportService;
use Illuminate\Http\Request;
use URL;
use Illuminate\Support\Facades\Input;

class CeoCxoReportController extends Controller
{

    protected $service;


    /**
     * PeriodicTicketController constructor.
     * @param PeriodicTicketService $periodicTicketService
     */
    public function __construct(CeoCxoReportService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $data = $this->service->index($request->all());
        return view('ceoCxoReport.index', ['data' => $data])->withInput($request->all());
    }

    public function show($id)
    {
        $email_details = CeoCxoReport::findOrFail($id);
        return view('ceoCxoReport.view',['data'=>$email_details]);
    }

}
