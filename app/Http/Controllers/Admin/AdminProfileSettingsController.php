<?php

namespace App\Http\Controllers\Admin;

use App\EmployeeDetails;
use App\User;

class AdminProfileSettingsController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-user';
        $this->pageTitle = 'app.menu.profileSettings';
    }

    public function index(){
        //$this->userDetail = User::where('email', ($this->user->email))->first();
        $this->userDetail = $this->user;
        $this->employeeDetail = EmployeeDetails::where('user_id', '=', $this->userDetail->id)->first();

        return view('admin.profile.index', $this->data);
    }

}
