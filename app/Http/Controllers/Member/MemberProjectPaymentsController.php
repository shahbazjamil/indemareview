<?php

namespace App\Http\Controllers\Member;

use App\Project;

class MemberProjectPaymentsController extends MemberBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';
        $this->middleware(function ($request, $next) {
            if (!in_array('payments', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function show($id)
    {
        $this->project = Project::with('payments', 'payments.currency')->findorFail($id);
        return view('member.projects.payments.show', $this->data);
    }

   

   
}
