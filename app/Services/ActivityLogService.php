<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;


class ActivityLogService extends ApiBaseService
{

    protected $activityLogRepository;


    /**
     * Activity Log Repository
     */
    public function __construct(ActivityLogRepository $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
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
        return $this->activityLogRepository->findAll(20, null, array('column' => 'updated_at', 'direction' => 'desc'));
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
        return $this->activityLogRepository->findOrFail($id);
    }
    /**
     * Create Activity Log
     */
    public function store(array $requestData)
    {
        try {
            $activityLog =  $this->activityLogRepository->save($requestData);
            return $activityLog;
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


}
