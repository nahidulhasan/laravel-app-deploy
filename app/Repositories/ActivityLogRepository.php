<?php

namespace App\Repositories;

use App\Models\ActivityLog;

/**
 * Class ActivityLogRepository
 * @package App\Repositories
 */
class ActivityLogRepository  extends BaseRepository
{
    protected $modelName = ActivityLog::class;


    
    public function __construct(ActivityLog $model)
    {
        $this->model = $model;
    }

}
