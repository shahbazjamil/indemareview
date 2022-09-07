<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Issue;
use App\ModuleSetting;
use App\Project;
use App\ProjectActivity;
use App\ProjectFile;
use App\ProjectTimeLog;
use App\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminProposalController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Proposals';
        $this->pageIcon = 'icon-briefcase';
        $this->middleware(function ($request, $next) {
            if (!in_array('proposal', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });

        $this->__set('Columns', array(
            'ID', 'Proposal', 'Project','Task Estimated Time', 'Task Estimated Cost', 'Total Cost',
        ));
    }


    public function index()
    {
        $this->projects = Project::all();
        return view('admin.proposal.index', $this->data);
    }

    public function show()
    {
        $data = DB::table('client_proposal')->select('id','project','proposal_name','proposal_hour','proposal_task_cost','proposal_total_cost')->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $data = [
            'proposal_name' => $request->taskField,
            'proposal_hour' => $request->hourField,
            'proposal_task_cost' => $request->costField,
            'proposal_total_cost' => $request->hourField * $request->costField,
            'project'    => $request->project_id,
            'project_scope' => $request->project_scope
        ];
        DB::table('client_proposal')->insert($data);
        return redirect('/admin/proposal');
    }
}
