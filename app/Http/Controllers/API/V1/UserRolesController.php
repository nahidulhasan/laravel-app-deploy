<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\UserRoleService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserRolesController extends Controller
{
    protected $service;
    protected $userService;

    public function __construct(UserRoleService $userRoleService, UserService $userService)
    {
        $this->service = $userRoleService;
        $this->userService = $userService;
    }

    public function getUserRole(Request $request, $email = null)
    {
        $request = $request->all();
        $data = [
            'email' => $email
        ];
        if (isset($request['group_id']) && !empty($request['group_id'])) {
            $query['group_id'] = $request['group_id'];
            return $this->userService->dropdownForEmail($query);
        }
        return $this->service->getUserRole($data);
    }
    public function getRoleTicketsType(Request $request)
    {
        $request = $request->all();
        $data = [
            'type' => $request['type'],
            'role' => $request['role'],
            'login_users' => $request['login_users'],
            'due_date' => $request['due_date'],
            'regulatory_body' => $request['regulatory_body'],
            'group_id' => $request['group_id'],
            'workflow_type' => $request['workflow_type'],
            'ticket_status' => $request['ticket_status'],
        ];
        return $this->service->getRoleTicketsType($data);
    }

    public function sync()
    {
        return $this->service->sync();
    } public function syncApi()
    {
        return $this->service->syncApi();
    }
}
