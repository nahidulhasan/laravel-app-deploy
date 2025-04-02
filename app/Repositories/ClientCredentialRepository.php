<?php

namespace App\Repositories;

use App\Models\ClientCredential;


/**
 * Class ClientCredentialRepository
 * @package App\Repositories
 */
class ClientCredentialRepository  extends BaseRepository
{
    protected $modelName = ClientCredential::class;


    /**
     * ClientCredentialRepository constructor.
     * @param ClientCredential $model
     */
    public function __construct(ClientCredential $model)
    {
        $this->model = $model;
    }



}
