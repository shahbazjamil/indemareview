<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\LeadForm;
use App\Company;
use App\Http\Requests\LeadSetting\StoreLeadDataPublic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\LeadPublicEmail;
use App\Lead;
use App\Task;

class ZapierController extends BaseController {

    public function leads_google(Request $request) {
        Lead::create($request->all());
    }

    public function tasks_google(Request $request) {

        Task::create($request->all());
    }

    public function lead_dubsado(Request $request) {
        Lead::create($request->all());
    }

    public function task_form(Request $request) {

        Task::create($request->all());
    }

    public function type_form(Request $request) {
        Task::create($request->all());
    }

    public function trello_board(Request $request) {
        print_r($request->all());
        exit;
    }

}
