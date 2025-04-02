<?php

namespace App\Repositories;

use App\Models\Group;


/**
 * Class GroupRepository
 * @package App\Repositories
 */
class GroupRepository  extends BaseRepository
{
    protected $modelName = Group::class;


    /**
     * GroupRepository constructor.
     * @param Group $model
     */
    public function __construct(Group $model)
    {
        $this->model = $model;
    }



}
