<?php

namespace App\Repositories;

use App\Models\CxoNotificationConfig;

/**
 * Class GroupRepository
 * @package App\Repositories
 */
class CxoNotificationConfigRepository  extends BaseRepository
{
    protected $modelName = CxoNotificationConfig::class;

    public function __construct(CxoNotificationConfig $model)
    {
        $this->model = $model;
    }


}
