<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\ModuleSetting;
use Illuminate\Http\Request;

class ModuleSettingsController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.moduleSettings';
        $this->pageIcon = 'icon-settings';
    }

    public function index(Request $request) {
        
        
        
         $check = ModuleSetting::where('type', 'client')->where('module_name', 'visionboard')->count();
        if($check == 0) {
            $moduleSetting = new ModuleSetting();
            $moduleSetting->company_id = company()->id;
            $moduleSetting->module_name = 'visionboard';
            $moduleSetting->status = 'active';
            $moduleSetting->type = 'client';
            $moduleSetting->save();

        }

        $moduleInPackage = (array)json_decode(company()->package->module_in_package);
        if($request->has('type')){
            if($request->get('type') == 'employee'){
                $this->modulesData = ModuleSetting::where('type', 'employee')->whereIn('module_name', $moduleInPackage)->groupBy('module_name')->get();
                $this->type = 'employee';
            }
            elseif($request->get('type') == 'client'){
                $this->modulesData = ModuleSetting::where('type', 'client')->whereIn('module_name', $moduleInPackage)->groupBy('module_name')->get();
                $this->type = 'client';
            }
        }
        else{
            $this->modulesData = ModuleSetting::where('type', 'admin')->whereIn('module_name', $moduleInPackage)->groupBy('module_name')->get();
            $this->type = 'admin';
        }
        

        return view('admin.module-settings.index', $this->data);
    }

    public function update(Request $request){
        $setting = ModuleSetting::findOrFail($request->id);

        switch ($setting->type) {
            case "admin":
                if($setting->module_name == 'timelogs' && $request->status == 'active') {
                    if(in_array('tasks', $this->modules) == false) {
                        return Reply::error(__('messages.enableTimeLogModuleMessage'), 'module_dependent');
                    }
                }

                if($setting->module_name == 'tasks' && $request->status == 'deactive') {
                    if(in_array('timelogs', $this->modules)) {
                        return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
                    }
                }
                break;
            case "employee":
                $empoyeeModules = ModuleSetting::where('type', 'employee')->where('status', 'active')->pluck('module_name')->toArray();

                if($setting->module_name == 'timelogs' && $request->status == 'active') {
                    if(in_array('tasks', $empoyeeModules) == false) {
                        return Reply::error(__('messages.enableTimeLogModuleMessage'), 'module_dependent');
                    }
                }

                if($setting->module_name == 'tasks' && $request->status == 'deactive') {
                    if(in_array('timelogs', $empoyeeModules)) {
                        return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
                    }
                }
                break;
            case "client":
                $clientModules = ModuleSetting::where('type', 'client')->where('status', 'active')->pluck('module_name')->toArray();

                if($setting->module_name == 'timelogs' && $request->status == 'active') {
                    if(in_array('tasks', $clientModules) == false) {
                        return Reply::error(__('messages.enableTimeLogModuleMessage'), 'module_dependent');
                    }
                }

                if($setting->module_name == 'tasks' && $request->status == 'deactive') {
//                    if(in_array('timelogs', $clientModules)) {
//                        return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
//                    }
                }
                break;
            default:
                return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
        }


        $setting->status = $request->status;
        $setting->save();
        
        ModuleSetting::where('module_name', '=', $setting->module_name)->where('company_id', '=', $setting->company_id)->where('type', $setting->type)->update(array('status' =>  $request->status));

        return Reply::success(__('messages.settingsUpdated'));
    }
}
