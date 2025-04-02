<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\CxoNotificationConfig;
use Illuminate\Http\Request;
use App\Models\Setting;
use Session;
use Illuminate\Support\Facades\Schema;
use App\Enums\WeekDays;

class CxoNotificationConfigController extends Controller
{
    /**
     * CxoNotificationConfigController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $setting = new Setting();
        $columnName = $this->getTableColumns('user_roles');
        $removeField = array ('created_at', 'updated_at', 'id');
        $columnList = $this->removeExtraFieldFormArray($removeField,$columnName);
        $days = WeekDays::DAYS;

        return view('notification-configs.create_cxo_page',['data'=>$setting,'column_list'=>$columnList,'days'=>$days]);
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

        $setting = CxoNotificationConfig::create($input);
        if($setting){
            Session::flash('message', 'CXO Config has been saved successfully!');
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
        $setting = CxoNotificationConfig::findOrFail($id);
        if(!empty( $setting->email_receiver)) {
            $setting->email_receiver = explode(",", $setting->email_receiver);
        }
        if(!empty( $setting->email_receiver_cc)) {
            $setting->email_receiver_cc = explode(",", $setting->email_receiver_cc);
        }
        $columnName = $this->getTableColumns('user_roles');
        $removeField = array ('created_at', 'updated_at', 'id');
        $columnList = $this->removeExtraFieldFormArray($removeField,$columnName);
        $days = WeekDays::DAYS;

        return view('notification-configs.create_cxo_page',['data'=>$setting,'column_list'=>$columnList,'days'=>$days]);
    }

    /**
     * @param SettingRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SettingRequest $request, $id)
    {
        $setting = CxoNotificationConfig::findOrFail($id);
        $input=$request->all();
        if(!empty($input['email_receiver'])){
            $input['email_receiver'] =  implode(',', $input['email_receiver']);
        }
        if(!empty($input['email_receiver_cc'])){
            $input['email_receiver_cc'] =  implode(',', $input['email_receiver_cc']);
        }else{
            $input['email_receiver_cc']='';
        }

        if ($setting->update($input)) {
            Session::flash('message', 'Notification config has been updated successfully!');

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
        $setting = CxoNotificationConfig::destroy($id);
        if ($setting) {
            Session::flash('message', 'Notification config has been delete successfully!');

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
        return Schema::getColumnListing($table);

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

}
