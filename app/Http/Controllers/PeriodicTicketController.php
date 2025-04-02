<?php

namespace App\Http\Controllers;

use App\Services\PeriodicTicketService;
use Illuminate\Http\Request;
use URL;
use Illuminate\Support\Facades\Input;

class PeriodicTicketController extends Controller
{

    protected $service;


    /**
     * PeriodicTicketController constructor.
     * @param PeriodicTicketService $periodicTicketService
     */
    public function __construct(PeriodicTicketService $service)
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
        return view('periodic-ticket.index', ['data' => $data])->withInput($request->all());
    }
}
