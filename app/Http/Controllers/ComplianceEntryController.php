<?php

namespace App\Http\Controllers;

use App\Services\ComplianceEntryService;
use Illuminate\Http\Request;

class ComplianceEntryController extends Controller
{

    protected $service;

    /**
     * ComplianceEntryController constructor.
     * @param ComplianceEntryService $service
     */
    public function __construct(ComplianceEntryService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * index method
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $requestData=$request->all();
        $data = $this->service->index($request->all());
        return view('compliance-entry.index', ['data' => $data]);
//        if(isset($requestData['search']) && !empty($requestData['search'])){
//            return view('compliance-entry.search_result', ['data' => $data]);
//        }else {
//            return view('compliance-entry.index', ['data' => $data]);
//        }
    }

    /**
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view($id = null)
    {
        $data = $this->service->view($id);

        return view('compliance-entry.view', ['data' => $data]);
    }

}
