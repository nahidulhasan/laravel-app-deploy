<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardService $dashboardService){
        $this->service = $dashboardService;
    }
    public function index($userId)
    {
        return $this->service->index($userId);
    }

    public function verifyCxoCeoUser(Request $request)
    {
        return $this->service->verifyCxoCeoUser($request->all());
    }

    public function getDashboardTickets(Request $request)
    {
        return $this->service->getDashboardTickets($request->all());
    }
    public function getDashboardReports(Request $request)
    {
        return $this->service->getDashboardReports($request->all());
    }
}
