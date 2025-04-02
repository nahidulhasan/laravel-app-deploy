<?php

namespace App\Repositories;

use App\Models\UserRole;
use Exception;
use Illuminate\Support\Facades\Log;

class UserRoleRepository extends BaseRepository
{
    protected $modelName = UserRole::class;


    public function __construct(UserRole $model)
    {
        $this->model = $model;
    }

    /**
     * @param $role
     * @return mixed
     */
    public function uniqueRecipientByRole($role)
    {
        return $this->model->whereNotNull($role)->distinct()->pluck($role);
    }

    public function getUsers($where = '', $whereIn = '')
    {
        if (!empty($where) || !empty($whereIn)) {
            $query = $this->model->newQuery();
            if (!empty($where)) {
                $query->where($where);
            }
            if (!empty($whereIn)) {
                $query->whereIn('compliance_owner', $whereIn);
            }
            return $query->get();
        }
    }

    public function sync($data, $xpertUsers = [], $rcmsFapGroupIds = [])
    {
        $users = [];
        foreach ($data as $user) {
            if (!array_key_exists($user['email_address'], $xpertUsers)) {
                continue;
            }

            $lineManager = $user['section']['section_manager']['email_address'] ?? null;
            $emt = $user['division']['division_manager']['email_address'];
            $cxo = $user['department']['manager']['email_address'];
            $ceo = 'yasir.azman@grameenphone.com';
            $users[] = [
                'compliance_owner' => $user['email_address'],
                'line_manager' => $lineManager ? $lineManager : $cxo ?? null,
                'emt' => $emt ?? null,
                'cxo' => $cxo ?? null,
                'ceo' => $ceo ?? null,
            ];
        }
        $result = [];
        // now insert
        foreach ($users as $user) {
            try {
                // Find the user by email
                $existingUser = $this->model->where('compliance_owner', $user['compliance_owner'])->first();
                $result[$user['compliance_owner']] = 'Already Exists';

                $xperUser = $xpertUsers[$user['compliance_owner']];
                $intersection = array_intersect($rcmsFapGroupIds, array_map('strval', $xperUser));

                if ($existingUser) {
                    // Define the new data
                    $newData = [
                        'line_manager' => $user['line_manager'] ?? null,
                        'emt' => $user['emt'] ?? null,
                        'cxo' => $user['cxo'] ?? null
                    ];
                    // Check for mismatches and update the user if necessary
                    $updated = false;

                    if ($existingUser->line_manager !== $newData['line_manager']) {
                        $existingUser->line_manager = $newData['line_manager'];
                        $updated = true;
                    }

                    if ($existingUser->emt !== $newData['emt']) {
                        $existingUser->emt = $newData['emt'];
                        $updated = true;
                    }

                    if ($existingUser->cxo !== $newData['cxo']) {
                        $existingUser->cxo = $newData['cxo'];
                        $updated = true;
                    }

                    if(!empty($intersection)){
                        $updated = true;
                        $existingUser->role = 'FAP';
                    }
                    // Save the changes if any updates were made
                    if ($updated) {
                        $result[$user['compliance_owner']] = 'updated';
                        $existingUser->save();
                    }
                } else {
                    $result[$user['compliance_owner']] = 'created';
                    if(!empty($intersection)){
                        $user['role'] = 'FAP';
                    }
                    $this->model->create($user);
                }
            } catch (Exception $e) {
                $result[$user['compliance_owner']] = $e->getMessage();
            }
        }
        return $result;
    }
}
