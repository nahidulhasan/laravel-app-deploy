<?php
namespace App\Http\Controllers;

use App\Services\UserRoleService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    protected $service;
    protected  $userService;


    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
    }

    /**
     *  index data
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $data = $this->userService->index($request->all());
        return view('users.index', ['data' => $data]);
    }

    public function create()
    {
        $method = 'post';
        $header = 'Create User';
        $formUrl = route('users-store');
        $formData = $this->userService->getFormData();

        return view('users.create-edit', compact('method', 'header', 'formUrl', 'formData'));
    }

    public function edit($id)
    {
        $method = 'put';
        $header = 'Edit User';
        $formUrl = route('users-update', $id);
        $formData = $this->userService->getFormData($id);
        return view('users.create-edit', compact('method', 'header', 'formUrl', 'formData'));
    }

    public function store(Request $request)
    {
       // try {
            $response = $this->userService->store($request->all());
            $request->session()->flash('success', 'Entry Created Successfully !');
            return redirect()->action([UserController::class, 'index']);
       /* } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserController::class, 'index']);
        }*/
    }

    public function update(Request $request, $id)
    {
        try {
            $this->userService->update($request->all(), $id);
            $request->session()->flash('success', 'Entry Updated Successfully !');
            return redirect()->action([UserController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserController::class, 'index']);
        }
    }

    /**
     * user role entry delation
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function destroy(Request $request, $id)
    {
        try {
            $this->userService->delete($id);
            $request->session()->flash('success', 'Entry Deleted Successfully !');
            return redirect()->action([UserController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserController::class, 'index']);
        }
    }

}
