<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ComplianceEntryRequest;
use App\Services\ComplianceEntryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComplianceEntryController extends Controller
{
    
    protected $service;


    /**
     * GroupController constructor.
     * @param GroupService $groupService
     */
    public function __construct(ComplianceEntryService $service)
    {
        $this->service = $service;
    }
    /**
     * store method
     *
     * @param ComplianceEntryRequest $request
     * @return void
     */
    public function store(ComplianceEntryRequest $request)
    {
        return $this->service->store($request->all());
    }

    /**
     * @param $regulatoryBody
     * @return \Illuminate\Http\JsonResponse
     */
    public function compliancePointNo($regulatoryBody)
    {
        return $this->service->compliancePointNo($regulatoryBody);
    }
    /**
     * update next due date
     *
     * @param Request $request
     * @return void
     */
    public function updateNextDueDate(Request $request)
    {
        return $this->service->updateNextDueDate($request->all());
    }
    /**
     * get next quarter date
     *
     * @param Request $request
     * @return void
     */
    public function getNextQuarterDate(Request $request)
    {
        $today = Carbon::now();
        return $this->service->getNextQuarterDate($today);
    }

    /**
     *  calculate next due date
    */
    public function calculateNextDueDate(Request $request)
    {
        $startDate = $request->start_date;
        $frequency = $request->frequency;
        $dueMonth = $request->due_month;
        $dueDate = $request->due_date;
        return $this->service->calculateNextDueDate($startDate,$frequency,$dueMonth,$dueDate);
    }
    /**
     * @param Request $request
     * @return void
     */
    public function checkCompliancePointNo(Request $request)
    {
        return $this->service->checkCompliancePointNo($request->all());
    }

    /**
     *
     */
    public function updateFields(Request $request)
    {
        return $this->service->updateFields($request->all());
    }
}
