<?php

namespace App\Repositories;

use App\Models\FormFieldMapping;

/**
 * Class GroupRepository
 * @package App\Repositories
 */
class FormFieldMappingRepository  extends BaseRepository
{
    protected $modelName = FormFieldMapping::class;
    
    public function __construct(FormFieldMapping $model)
    {
        $this->model = $model;
    }

}
