<?php

use App\Module;
use App\ModuleSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoomModulePermissionsAndModuleSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create module and permissions
        $permissions = [
            [
                'name' => 'add_zoom_meetings',
                'display_name' => 'Add Meetings'
            ],
            [
                'name' => 'view_zoom_meetings',
                'display_name' => 'View Meetings'
            ],
            [
                'name' => 'edit_zoom_meetings',
                'display_name' => 'Edit Meetings'
            ],
            [
                'name' => 'delete_zoom_meetings',
                'display_name' => 'Delete Meetings'
            ]
        ];

        $module = new Module();
        $module->module_name = 'Zoom';
        $module->description = 'User can view the meetings assigned to him as default even without any permission.';
        $module->save();

        $module->permissions()->createMany($permissions);

        $companies =  \App\Company::withoutGlobalScope('active')->get();
        $roles = ['admin', 'employee', 'client'];

        foreach ($companies as $company) {
             // create admin, employee and client module settings
            foreach ($roles as $role) {
                $moduleSetting = new ModuleSetting();

                $moduleSetting->module_name = 'Zoom';
                $moduleSetting->status = 'active';
                $moduleSetting->type = $role;
                $moduleSetting->company_id = $company->id;

                $moduleSetting->save();
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Module::where('module_name', 'Zoom')->delete();
    }
}
