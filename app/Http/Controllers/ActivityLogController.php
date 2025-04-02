<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{

    protected $service;

    /**
     * ActivityLogController constructor.
     * @param ActivityLogService $service
     */

    public function __construct(ActivityLogService $service)
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
        return view('activity-log.index', ['data' => $data]);
    }

    /**
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view($id = null)
    {
        $data = $this->service->view($id);

        return view('activity-log.view', ['data' => $data]);
    }
}
