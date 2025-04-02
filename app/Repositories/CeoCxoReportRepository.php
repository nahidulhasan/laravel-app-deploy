<?php

namespace App\Repositories;

use App\Models\CeoCxoReport;

/**
 * Class GroupRepository
 * @package App\Repositories
 */
class CeoCxoReportRepository  extends BaseRepository
{
    protected $modelName = CeoCxoReport::class;

    public function __construct(CeoCxoReport $model)
    {
        $this->model = $model;
    }


}
