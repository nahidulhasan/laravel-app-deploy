<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Services\UserService;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use Mail;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * PurchaseController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(LoginRequest $request)
    {
        return $this->userService->authenticate($request);
    }





    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchAccount(Request $request)
    {

        return $this->userService->switchAccount($request);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        return $this->userService->logout($token);
    }
    /**
     * user dropdown 
     * @return void
     */
    public function getUserDropdown(Request $request)
    {
        $query = $request->query();
        return $this->userService->dropdown($query);
    }


}
