<?php

namespace App\Http\Controllers\Client;

use App\Issue;
use App\ModuleSetting;
use App\ProjectActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Scopes\CompanyScope;
use App\User;
use Illuminate\Support\Facades\DB;
use App\ProjectClient;

class ClientDashboardController extends ClientBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = "app.menu.dashboard";
        $this->pageIcon = 'icon-speedometer';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_ids[] = -1;
        $projectClients = ProjectClient::where('client_id', auth()->user()->id)->get();
        if($projectClients) {
            foreach ($projectClients as $projectClient) {
                $project_ids[] = $projectClient->project_id;
            }
        }
        $project_ids = implode(",",$project_ids);
        
        $this->counts = DB::table('client_details')
            ->select(
                DB::raw('(select count(projects.id) from `projects` where (client_id = ' . $this->user->id . ' OR projects.id IN ('.$project_ids.') ) and (company_id = ' . company()->id . ')) as totalProjects'),
                // DB::raw('(select count(issues.id) from `issues` where status="pending" and user_id = '.$this->user->id.') as totalPendingIssues'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending") and user_id = ' . $this->user->id . ' and company_id = ' . company()->id . ') as totalUnResolvedTickets'),
                DB::raw('(select IFNULL(sum(invoices.total),0) from `invoices` inner join projects on projects.id = invoices.project_id where invoices.status="paid" and (projects.client_id = ' . $this->user->id . ' OR projects.id IN ('.$project_ids.') ) and invoices.company_id = ' . company()->id . ') as totalPaidAmount'),
                DB::raw('(select IFNULL(sum(invoices.total),0) from `invoices` inner join projects on projects.id = invoices.project_id where invoices.status="unpaid" and (projects.client_id = ' . $this->user->id . ' OR projects.id IN ('.$project_ids.') ) and invoices.company_id = ' . company()->id . ') as totalUnpaidAmount')
            )
            ->first();

        $this->projectActivities = ProjectActivity::join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->where('projects.client_id', '=', $this->user->id)
            ->where('project_activity.activity', 'NOT LIKE', '%Timer stopped by%') //remove the "timer started", "timer stopped" notifications for the project activity. 
            ->where('project_activity.activity', 'NOT LIKE', '%Timer started by%') ////remove the "timer started", "timer stopped" notifications for the project activity.
            ->whereNull('projects.deleted_at')
            ->select('projects.project_name', 'project_activity.created_at', 'project_activity.activity', 'project_activity.project_id')
            ->limit(15)
            ->orderBy('project_activity.id', 'desc')
            ->get();

        return view('client.dashboard.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
