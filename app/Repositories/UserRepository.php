<?php

namespace App\Repositories;

use App\Http\Requests\UserNotificationRequest;
use App\Models\User;
use App\Http\Requests\DeviceTokenRequest;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class UserRepository extends BaseRepository
{
    protected $modelName = User::class;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'email',
        'is_verified',
        'status',
        'locked_at',
        'locked_end',
        'account_type'
    ];


    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }


}
