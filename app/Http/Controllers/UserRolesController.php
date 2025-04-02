<?php

namespace App\Http\Controllers;

use App\Services\UserRoleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UserRolesController extends Controller
{
    protected $service;

    /**
     * contructor method
     *
     * @param UserRoleService $service
     */
    public function __construct(UserRoleService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     *  index data
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $data = $this->service->index($request->all());
        return view('user-role.index', ['data' => $data]);
    }

    public function create()
    {
        $method = 'post';
        $header = 'Create User Role';
        $formUrl = route('user-role-store');
        $formData = $this->service->getFormData();
        return view('user-role.create-edit', compact('method', 'header', 'formUrl', 'formData'));
    }

    public function edit($id)
    {
        $method = 'put';
        $header = 'Edit User Role';
        $formUrl = route('user-role-update', $id);
        $formData = $this->service->getFormData($id);
        return view('user-role.create-edit', compact('method', 'header', 'formUrl', 'formData'));
    }

    public function store(Request $request)
    {
        try {
            $this->service->store($request->all());
            $request->session()->flash('success', 'Entry Created Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->service->update($request->all(), $id);
            $request->session()->flash('success', 'Entry Updated Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }

    public function import()
    {
        // replace old data with new data
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
            $this->service->delete($id);
            $request->session()->flash('success', 'Entry Deleted Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }

    public function sync(Request $request)
    {
        try {
            $this->service->sync();
            $request->session()->flash('success', 'Sync Completed Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }
}
