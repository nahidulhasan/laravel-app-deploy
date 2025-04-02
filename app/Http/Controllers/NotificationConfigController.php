<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Models\CxoNotificationConfig;
use App\Services\PeriodicTicketService;
use Session;
use Illuminate\Support\Facades\Schema;
use App\Services\TicketCreationApiService;

class NotificationConfigController extends Controller
{

    protected $service;

    /**
     * SettingController constructor.
     * @param TicketCreationApiService $service
     */
    public function __construct(TicketCreationApiService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $settings = Setting::latest()->paginate(20);
        $cxo_notification = CxoNotificationConfig::latest()->paginate(20);
        return view('notification-configs.index', ['data' => $settings,'cxo_notification'=>$cxo_notification]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create()
    {
        $setting = new Setting();
        $columnName = $this->getTableColumns('user_roles');
        $removeField = array ('created_at', 'updated_at', 'id');
        $columnList = $this->removeExtraFieldFormArray($removeField,$columnName);
        $url =  env('CHT_HOST').'/api/v1/notification/email-template';
       $result=   $this->service->get($url);
        $template = [];
       if($result['status_code'] && !empty($result['data'])){
           $template =$result['data'];
       }
        return view('notification-configs.create',['data'=>$setting,'column_list'=>$columnList,'template'=>$template]);
    }

    /**
     * Author by eng. Md Ahsan habib
     * Email <habib.cst@gmail.com>
     * @param $removeField
     * @param $columnName
     * @return mixed
     *
     */
    private function removeExtraFieldFormArray($removeField, $columnName)
    {
        foreach ($removeField as $key => $value) {
            $pos = array_search($value, $columnName);
            unset($columnName[$pos]);
        }
        return array_map('trim', $columnName);
    }

    /**
     * @param SettingRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(SettingRequest $request)
    {
        $input = $request->all();
        if(!empty($input['email_receiver'])){
            $input['email_receiver'] =  implode(',', $input['email_receiver']);
        }
        if(!empty($input['email_receiver_cc'])){
            $input['email_receiver_cc'] =  implode(',', $input['email_receiver_cc']);
        }

        $setting = Setting::create($input);
        if($setting){
            Session::flash('message', 'Setting has been save successfully!');
            return redirect()->route('notification-configs.index');
        }else{
            return Redirect::back()->withInput(Input::all());
        }

        return response(['data' => $setting], 201);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        if(!empty( $setting->email_receiver)) {
            $setting->email_receiver = explode(",", $setting->email_receiver);
        }

        if(!empty( $setting->email_receiver_cc)) {
            $setting->email_receiver_cc = explode(",", $setting->email_receiver_cc);
        }
        $url =  env('CHT_HOST').'/api/v1/notification/email-template';
        $result=   $this->service->get($url);
        $template = [];
        if($result['status_code'] && !empty($result['data'])){
            $template =$result['data'];
        }
        $columnName = $this->getTableColumns('user_roles');
        $removeField = array ('created_at', 'updated_at', 'id');
        $columnList = $this->removeExtraFieldFormArray($removeField,$columnName);
        return view('notification-configs.create',['data'=>$setting,'column_list'=>$columnList,'template'=>$template]);
    }

    /**
     * @param SettingRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SettingRequest $request, $id)
    {
        $setting = Setting::findOrFail($id);
        $input=$request->all();
        if(!empty($input['email_receiver'])){
            $input['email_receiver'] =  implode(',', $input['email_receiver']);
        }
        if(!empty($input['email_receiver_cc'])){
            $input['email_receiver_cc'] =  implode(',', $input['email_receiver_cc']);
        }else{
            $input['email_receiver_cc'] = null;
        }

        if ($setting->update($input)) {
            Session::flash('message', 'Setting has been updated successfully!');

        } else {
            Session::flash('message', 'Something went wrong try again later!');
        }
        return redirect()->route('notification-configs.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $setting = Setting::destroy($id);
        if ($setting) {
            Session::flash('message', 'Setting has been delete successfully!');

        } else {
            Session::flash('message', 'Something went wrong try again later!');
        }
        return redirect()->route('notification-configs.index');
    }

    /**
     * Author by eng. Md Ahsan habib
     * Email <habib.cst@gmail.com>
     * @param $table
     * @return array
     */
    public function getTableColumns($table)
    {
        $list = Schema::getColumnListing($table);

        if (($key = array_search("role", $list)) !== false) {
            unset($list[$key]);
            $list = array_values($list);
        }

       /* $index = array_search("role", $list);
        if ($index !== false) {
            $list[$index] = "FAP";
        }*/

        return $list;

    }
}
