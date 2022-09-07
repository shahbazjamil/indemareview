<?php

namespace App\Http\Controllers\Member;

use App\DataTables\Member\MemberDiscussionDataTable;
use App\Discussion;
use App\DiscussionCategory;
use App\DiscussionReply;
use App\Helper\Reply;
use App\Http\Requests\Project\StoreProject;
use App\Project;
use App\ProjectActivity;
use App\ProjectCategory;
use App\ProjectFile;
use App\ProjectMember;
use App\ProjectTemplate;
use App\ProjectTimeLog;
use App\Task;
use App\TaskboardColumn;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Currency;
use App\TaskUser;
use App\ProjectClient;
use App\ProjectMilestone;

/**
 * Class MemberProjectsController
 * @package App\Http\Controllers\Member
 */
class MemberProjectsController extends MemberBaseController
{
    use ProjectProgress;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';

        $this->middleware(function ($request, $next) {
            if (!in_array('projects', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/projects'));
        
        $this->clients = User::allClients();

        $allProject = Project::select('status', 'id', 'project_budget')->get();

        // Total Projects
        $this->totalProjects = $allProject->count();

        // OverDue Projects
        $this->overdueProjects = $allProject->filter(function ($value, $key) {
            return $value->completion_percent <> '100' && $value->deadline <  Carbon::today()->timezone($this->global->timezone);
        })->count();

        // Completed Or Finished Projects
        $this->finishedProjects = $allProject->filter(function ($value, $key) {
            return $value->completion_percent == '100';
        })->count();

        // IN Process Projects
        $this->inProcessProjects = $allProject->filter(function ($value, $key) {
            return $value->status == 'in progress';
        })->count();

        // On Hold pROJECTS
        $this->onHoldProjects = $allProject->filter(function ($value, $key) {
            return $value->status == 'on hold';
        })->count();

        // Canceled Projects
        $this->canceledProjects = $allProject->filter(function ($value, $key) {
            return $value->status == 'canceled';
        })->count();

        // Not Started projects
        $this->notStartedProjects = $allProject->filter(function ($value, $key) {
            return $value->status == 'not started';
        })->count();

        //Budget Total
        $this->projectBudgetTotal = $allProject->sum('project_budget');
        
        $this->totalRecords = $this->totalProjects;
        
        return view('member.projects.index', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->project = Project::findOrFail($id)->withCustomFields();

//        if (!$this->project->isProjectAdmin && !$this->user->can('edit_projects')) {
//            abort(403);
//        }

        $this->clients = User::allClients();
        $this->categories = ProjectCategory::all();
        $this->fields = $this->project->getCustomFieldGroupsWithFields()->fields;
         $this->currencies = Currency::all();
         
         $selected_clients = [];
        if($this->project->client_id) {
            $selected_clients[] = $this->project->client_id;
        }
        
        if($this->project->clients) {
            foreach ($this->project->clients as $client) {
                $selected_clients[] = $client->client_id;
            }
        }
        $this->selected_clients = $selected_clients;
         

        return view('member.projects.edit', $this->data);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $this->userDetail = auth()->user();

        $this->project = Project::findOrFail($id)->withCustomFields();
        $this->fields = $this->project->getCustomFieldGroupsWithFields()->fields;

        $isMember = ProjectMember::checkIsMember($id, $this->user->id);

        // Check authorised user

        if ($this->project->isProjectAdmin || $this->user->can('view_projects') || $isMember) {
            $this->activeTimers = ProjectTimeLog::projectActiveTimers($this->project->id);

            $this->openTasks = Task::projectOpenTasks($this->project->id, $this->userDetail->id);
            $this->openTasksPercent = (count($this->openTasks) == 0 ? '0' : (count($this->openTasks) / count($this->project->tasks)) * 100);

            // TODO::ProjectDeadline to do
            $this->daysLeft = 0;
            $this->daysLeftFromStartDate = 0;
            $this->daysLeftPercent = 0;

            if ($this->project->deadline) {
                $this->daysLeft = $this->project->deadline->diff(Carbon::now())->format('%d') + ($this->project->deadline->diff(Carbon::now())->format('%m') * 30) + ($this->project->deadline->diff(Carbon::now())->format('%y') * 12);
                $this->daysLeftFromStartDate = $this->project->deadline->diff($this->project->start_date)->format('%d') + ($this->project->deadline->diff($this->project->start_date)->format('%m') * 30) + ($this->project->deadline->diff($this->project->start_date)->format('%y') * 12);
                $this->daysLeftPercent = ($this->daysLeftFromStartDate == 0 ? "0" : (($this->daysLeft / $this->daysLeftFromStartDate) * 100));
            }

            $this->hoursLogged = ProjectTimeLog::projectTotalMinuts($this->project->id);
            $minute = 0;
            $hour = intdiv($this->hoursLogged, 60);

            if (($this->hoursLogged % 60) > 0) {
                $minute = ($this->hoursLogged % 60);
                $this->hoursLogged = $hour . ':' . $minute;
            } else {
                $this->hoursLogged = $hour;
            }

            $this->recentFiles = ProjectFile::where('project_id', $this->project->id)->orderBy('id', 'desc')->limit(10)->get();
            $this->activities = ProjectActivity::getProjectActivities($id, 10, $this->userDetail->id);

            return view('member.projects.show', $this->data);
        } else {
            // If not authorised user
            abort(403);
        }
    }

    public function data(Request $request)
    {
        $this->userDetail = auth()->user();
        $projects = Project::select('projects.id', 'projects.project_name', 'projects.project_admin', 'projects.project_summary', 'projects.start_date', 'projects.deadline', 'projects.notes', 'projects.category_id', 'projects.client_id', 'projects.feedback', 'projects.completion_percent', 'projects.created_at', 'projects.updated_at', 'projects.status');

        if (!$this->user->can('view_projects')) {
            $projects = $projects->join('project_members', 'project_members.project_id', '=', 'projects.id');
            $projects = $projects->where('project_members.user_id', '=', $this->userDetail->id);
        }

        if (!is_null($request->status) && $request->status != 'all') {
            if ($request->status == 'incomplete') {
                $projects->where('completion_percent', '<', '100');
            } elseif ($request->status == 'complete') {
                $projects->where('completion_percent', '=', '100');
            }
        }


        if (!is_null($request->client_id) && $request->client_id != 'all') {
            $projects->where('client_id', $request->client_id);
        }

        $projects->get();

        return DataTables::of($projects)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light btn-default" type="button">'.trans('app.action').' <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">';

                if ($row->project_admin == $this->userDetail->id || $this->user->can('edit_projects')) {
                    $action .= '<li><a href="' . route('member.projects.edit', [$row->id]) . '"><i class="fa fa-pencil" aria-hidden="true"></i> '.trans('app.edit').'</a></li>';
                }
                $action .= '<li><a href="' . route('member.projects.show', [$row->id]) . '"><i class="fa fa-search" aria-hidden="true"></i> View Project Details</a></li>';
                $action .= '<li><a href="' . route('member.projects.gantt', [$row->id]) . '"><i class="fa fa-bar-chart" aria-hidden="true"></i> '.trans('modules.projects.viewGanttChart').'</a></li>';
                $action .= '<li><a href="' . route('front.gantt', [md5($row->id)]) . '" target="_blank"><i class="fa fa-line-chart" aria-hidden="true"></i> '.trans('modules.projects.viewPublicGanttChart').'</a></li>';

                if ($this->user->can('delete_projects')) {
                    $action .= '<li><a href="javascript:;" data-user-id="' . $row->id . '" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> '.trans('app.delete').'</a></li>';
                }

                $action .= '</ul> </div>';

                return $action;
            })
            ->addColumn('members', function ($row) {
                $members = '';

                if (count($row->members) > 0) {
                    foreach ($row->members as $member) {
                        if($member->user->image) {
                            $members .= '<img data-toggle="tooltip" data-original-title="' . ucwords($member->user->name) . '" src="' . $member->user->image_url . '"
                        alt="user" class="img-circle" width="30" height="30"> ';
                        } else {
                            $members .= '<span class="nameletter">'.company_initials().'</span>';
                        }
                        
                    }
                } else {
                    $members .= __('messages.noMemberAddedToProject');
                }

                //if ($this->user->can('add_projects')) {
                    $members .= '<br><br><a class="font-12" href="' . route('member.project-members.show', $row->id) . '"><i class="fa fa-plus"></i> ' . __('modules.projects.addMemberTitle') . '</a>';
                //}
                return $members;
            })

            ->editColumn('project_name', function ($row) {
                return '<a href="' . route('member.projects.show', $row->id) . '">' . ucfirst($row->project_name) . '</a>';
            })
            ->addColumn('start_date', function ($row) {
                return $row->start_date->format($this->global->date_format);
            })
            ->addColumn('deadline', function ($row) {
                if ($row->deadline) {
                    return $row->deadline->format($this->global->date_format);
                }

                return '-';
            })
            ->addColumn('client_id', function ($row) {
                if (!is_null($row->client_id)) {
                    return ucwords($row->client->name);
                } else {
                    return '--';
                }
            })
            ->editColumn('completion_percent', function ($row) {
                if ($row->completion_percent < 50) {
                    $statusColor = 'danger';
                    $status = __('app.progress');
                } elseif ($row->completion_percent >= 50 && $row->completion_percent < 75) {
                    $statusColor = 'warning';
                    $status = __('app.progress');
                } else {
                    $statusColor = 'success';
                    $status = __('app.progress');

                    if ($row->completion_percent >= 100) {
                        $status = __('app.completed');
                    }
                }

                return '<h5>' . $status . '<span class="pull-right">' . $row->completion_percent . '%</span></h5><div class="progress">
                  <div class="progress-bar progress-bar-' . $statusColor . '" aria-valuenow="' . $row->completion_percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $row->completion_percent . '%" role="progressbar"> <span class="sr-only">' . $row->completion_percent . '% Complete</span> </div>
                </div>';
            })
            ->editColumn('status', function ($row) {

                if ($row->status == 'in progress') {
                    $status = '<label class="label label-info">' . __('app.inProgress') . '</label>';
                } else if ($row->status == 'on hold') {
                    $status = '<label class="label label-warning">' . __('app.onHold') . '</label>';
                } else if ($row->status == 'not started') {
                    $status = '<label class="label label-warning">' . __('app.notStarted') . '</label>';
                } else if ($row->status == 'canceled') {
                    $status = '<label class="label label-danger">' . __('app.canceled') . '</label>';
                } else if ($row->status == 'finished') {
                    $status = '<label class="label label-success">' . __('app.finished') . '</label>';
                }
                return $status;
            })
            ->rawColumns(['project_name', 'action', 'members', 'completion_percent', 'status'])
            ->removeColumn('project_summary')
            ->removeColumn('notes')
            ->removeColumn('category_id')
            ->removeColumn('feedback')
            ->removeColumn('start_date')
            ->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProject $request, $id)
    {
        //echo 'this is update';
        $project = Project::findOrFail($id);
        $project->project_name = $request->project_name;
        if ($request->project_summary != '') {
            $project->project_summary = $request->project_summary;
        }
        $project->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        if (!$request->has('without_deadline')) {
            $project->deadline = Carbon::parse($request->deadline)->format('Y-m-d');
        } else {
            $project->deadline = null;
        }
        if ($request->notes != '') {
            $project->notes = $request->notes;
        }
        if ($request->category_id != '') {
            $project->category_id = $request->category_id;
        }
        
        if ($request->client_view_task) {
            $project->client_view_task = 'enable';
        } else {
            $project->client_view_task = "disable";
        }
        if (($request->client_view_task) && ($request->client_task_notification)) {
            $project->allow_client_notification = 'enable';
        } else {
            $project->allow_client_notification = "disable";
        }
        
        if ($request->manual_timelog) {
            $project->manual_timelog = 'enable';
        } else {
            $project->manual_timelog = "disable";
        }
        
        // old method
        $project->client_id = isset($request->client_id[0]) ? $request->client_id[0] : null;
        $project->feedback = $request->feedback;

        if ($request->calculate_task_progress) {
            $project->calculate_task_progress = $request->calculate_task_progress;
            $project->completion_percent = $this->calculateProjectProgress($id);
        } else {
            $project->calculate_task_progress = "false";
            $project->completion_percent = $request->completion_percent;
        }

        

        $project->project_budget = $request->project_budget;
        $project->currency_id = $request->currency_id;
        $project->hours_allocated = $request->hours_allocated;
        $project->status = $request->status;

        $project->save();
        
        // new method assign more than one client to project
        ProjectClient::where('project_id', $project->id)->delete();
        $client_ids = $request->client_id?$request->client_id:[];
        if(count($client_ids) > 0) {
            foreach ($client_ids as $client_id) {
                $projectClient = new ProjectClient();
                $projectClient->project_id = $project->id;
                $projectClient->client_id = $client_id;
                $projectClient->save();
            }
        }
        
         // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $project->updateCustomFieldData($request->get('custom_fields_data'));
        }

        $this->logProjectActivity($project->id, ucwords($project->project_name) . __('modules.projects.projectUpdated'));
        return Reply::redirect(route('member.projects.edit', $id), __('messages.projectUpdated'));
    }

    public function create()
    {
//        if (!$this->user->can('add_projects')) {
//            abort(403);
//        }

        if(company()->projects->count() >= company()->package->max_projects) {
            return redirect(route('admin.billing'));
        }
        
        $this->clients = User::allClients();
        $this->categories = ProjectCategory::all();
        $this->templates = ProjectTemplate::all();
        $this->currencies = Currency::all();
        $this->employees = User::allEmployees();

        $project = new Project();
        $this->fields = $project->getCustomFieldGroupsWithFields()->fields;
        $this->upload = can_upload();
        return view('member.projects.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProject $request)
    {
        $project = new Project();
        $project->project_name = $request->project_name;
        if ($request->project_summary != '') {
            $project->project_summary = $request->project_summary;
        }
        //$project->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        
        $project->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        
        
        if (!$request->has('without_deadline')) {
            //$project->deadline = Carbon::parse($request->deadline)->format('Y-m-d');
            $project->deadline = Carbon::createFromFormat($this->global->date_format, $request->deadline)->format('Y-m-d');
        }

        if ($request->notes != '') {
            $project->notes = $request->notes;
        }
        if ($request->category_id != '') {
            $project->category_id = $request->category_id;
        }
        // old method
        $project->client_id = isset($request->client_id[0])?$request->client_id[0]:null;

        if ($request->client_view_task) {
            $project->client_view_task = 'enable';
        } else {
            $project->client_view_task = "disable";
        }
        if (($request->client_view_task) && ($request->client_task_notification)) {
            $project->allow_client_notification = 'enable';
        } else {
            $project->allow_client_notification = "disable";
        }

        if ($request->manual_timelog) {
            $project->manual_timelog = 'enable';
        } else {
            $project->manual_timelog = "disable";
        }
        
        $project->project_budget = $request->project_budget;
        $project->currency_id = $request->currency_id;
        if (!$request->currency_id) {
            $project->currency_id = $this->global->currency_id;
        }

        $project->hours_allocated = $request->hours_allocated;
        $project->status = $request->status;

        $project->save();
        
        // new method assign more than one client to project
        ProjectClient::where('project_id', $project->id)->delete();
        $client_ids = $request->client_id?$request->client_id:[];
        if(count($client_ids) > 0) {
            foreach ($client_ids as $client_id) {
                $projectClient = new ProjectClient();
                $projectClient->project_id = $project->id;
                $projectClient->client_id = $client_id;
                $projectClient->save();
            }
        }
        

        $templateMembers = [];
        $memberExistsInTemplate = false;
        
        if ($request->template_id) {
            $template = ProjectTemplate::findOrFail($request->template_id);
            
            
            foreach ($template->milestones as $milestoneD) {
                
                
                $milestone = new ProjectMilestone();
                $milestone->project_id = $project->id;
                $milestone->milestone_title = $milestoneD->milestone_title;
                $milestone->summary = $milestoneD->summary;
                $milestone->cost = ($milestoneD->cost == '') ? '0' : $milestoneD->cost;
                $milestone->currency_id = $milestoneD->currency_id;
                $milestone->status = $milestoneD->status;
                $milestone->project_template_milestone_id = $milestoneD->id;
                $milestone->save();
                
            }
            
            
            foreach ($template->members as $member) {
                $templateMembers[] = $member->user_id;
                $projectMember = new ProjectMember();

                $projectMember->user_id    = $member->user_id;
                $projectMember->project_id = $project->id;
                $projectMember->save();
                
                if ($member->user_id == $this->user->id) {
                    $memberExistsInTemplate = true;
                }
            }
            foreach ($template->tasks as $task) {
                $projectTask = new Task();

                $projectTask->project_id  = $project->id;
                $projectTask->heading     = $task->heading;
                $projectTask->description = $task->description;
                $projectTask->due_date    = Carbon::now()->addDay()->format('Y-m-d');
                $projectTask->status      = 'incomplete';
                $projectTask->created_by      = $this->user->id;
                
                if(isset($task->milestone_id) && !empty($task->milestone_id)) {
                    $projectMilestone = ProjectMilestone::where('project_template_milestone_id', '=', $task->milestone_id)->first();
                    if($projectMilestone) {
                        $projectTask->milestone_id = $projectMilestone->id;
                    }
                }
                
                $projectTask->save();
                
                foreach ($task->users_many as $key => $value) {
                    TaskUser::create(
                        [
                            'user_id' => $value->id,
                            'task_id' => $projectTask->id
                        ]
                    );
                }
                
                
            }
        }

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $project->updateCustomFieldData($request->get('custom_fields_data'));
        }
        
//        $users = $request->user_id;
//        foreach ($users as $user) {
//            $member = new ProjectMember();
//            $member->user_id = $user;
//            $member->project_id = $project->id;
//            $member->save();
//
//            $this->logProjectActivity($project->id, ucwords($member->user->name) . ' ' . __('messages.isAddedAsProjectMember'));
//        }
        
        if(!$memberExistsInTemplate) {
            
            $member = new ProjectMember();
            $member->user_id = $this->user->id;
            $member->project_id = $project->id;
            $member->save();
            $this->logProjectActivity($project->id, ucwords($member->user->name) . ' ' . __('messages.isAddedAsProjectMember'));
            
        }
        
        $this->mixPanelTrackEvent('project_created', array('page_path' => '/member/projects'));
        

        $this->logSearchEntry($project->id, 'Project: ' . $project->project_name, 'admin.projects.show', 'project');

        $this->logProjectActivity($project->id, ucwords($project->project_name) . ' ' . __("messages.addedAsNewProject"));

        return Reply::dataOnly(['projectID' => $project->id]);

        //        return Reply::redirect(route('member.projects.index'), __('modules.projects.projectUpdated'));
    }

    public function destroy($id)
    {
        Project::destroy($id);
        return Reply::success(__('messages.projectDeleted'));
    }

    public function gantt($ganttProjectId = '')
    {

        $data = array();
        $links = array();

        $projects = Project::select('projects.id', 'projects.project_name', 'projects.start_date', 'projects.deadline', 'projects.completion_percent');

        if (!$this->user->can('view_projects')) {
            $projects = $projects->join('project_members', 'project_members.project_id', '=', 'projects.id');
            $projects = $projects->where('project_members.user_id', '=', $this->user->id);
        }

        if($ganttProjectId != '')
        {
            $projects = $projects->where('projects.id', '=', $ganttProjectId);
        }

        $projects = $projects->get();

        $id = 0; //count for gantt ids
        foreach ($projects as $project) {
            $id = $id + 1;
            $projectId = $id;

            // TODO::ProjectDeadline to do
            $projectDuration = 0;
            if ($project->deadline) {
                $projectDuration = $project->deadline->diffInDays($project->start_date);
            }

            $data[] = [
                'id' => $projectId,
                'text' => ucwords($project->project_name),
                'start_date' => $project->start_date->format('Y-m-d H:i:s'),
                'duration' => $projectDuration,
                'progress' => $project->completion_percent / 100
            ];

            $tasks = Task::projectOpenTasks($project->id);

            foreach ($tasks as $key => $task) {
                $id = $id + 1;

                $taskDuration = $task->due_date->diffInDays($task->start_date);
                $data[] = [
                    'id' => $id,
                    'text' => ucfirst($task->heading),
                    'start_date' => (!is_null($task->start_date)) ? $task->start_date->format('Y-m-d H:i:s') : $task->due_date->format('Y-m-d H:i:s'),
                    'duration' => $taskDuration,
                    'parent' => $projectId
                ];

                $links[] = [
                    'id' => $id,
                    'source' => $project->id,
                    'target' => $task->id,
                    'type' => 1
                ];
            }

            $ganttData = [
                'data' => $data,
                'links' => $links
            ];
        }
        
        $this->project = Project::findorFail($ganttProjectId);

        $this->ganttProjectId = $ganttProjectId;
        return view('member.projects.gantt', $this->data);
    }
    
    public function freeFlowGantt()
    {
        

        $data = array();
        $links = array();
        
        $ganttProjectId = '';
        $projects = Project::select('projects.id', 'projects.project_name', 'projects.start_date', 'projects.deadline', 'projects.completion_percent');

        if (!$this->user->can('view_projects')) {
            $projects = $projects->join('project_members', 'project_members.project_id', '=', 'projects.id');
            $projects = $projects->where('project_members.user_id', '=', $this->user->id);
        }
        
        
        if($ganttProjectId != '')
        {
            $projects = $projects->where('projects.id', '=', $ganttProjectId);
        }

        $projects = $projects->get();

        $id = 0; //count for gantt ids
        foreach ($projects as $project) {
            $id = $id + 1;
            $projectId = $id;

            // TODO::ProjectDeadline to do
            $projectDuration = 0;
            if ($project->deadline) {
                $projectDuration = $project->deadline->diffInDays($project->start_date);
            }

            $data[] = [
                'id' => $projectId,
                'text' => ucwords($project->project_name),
                'start_date' => $project->start_date->format('Y-m-d H:i:s'),
                'duration' => $projectDuration,
                'progress' => $project->completion_percent / 100
            ];

            $tasks = Task::projectOpenTasks($project->id);

            foreach ($tasks as $key => $task) {
                $id = $id + 1;

                $taskDuration = $task->due_date->diffInDays($task->start_date);
                $data[] = [
                    'id' => $id,
                    'text' => ucfirst($task->heading),
                    'start_date' => (!is_null($task->start_date)) ? $task->start_date->format('Y-m-d H:i:s') : $task->due_date->format('Y-m-d H:i:s'),
                    'duration' => $taskDuration,
                    'parent' => $projectId
                ];

                $links[] = [
                    'id' => $id,
                    'source' => $project->id,
                    'target' => $task->id,
                    'type' => 1
                ];
            }

            $ganttData = [
                'data' => $data,
                'links' => $links
            ];
        }
        
        $this->projects = $projects;
        
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/projects/free-flow-gantt'));

        $this->ganttProjectId = $ganttProjectId;
        return view('member.projects.free-flow-gantt', $this->data);
    }

    public function ganttData($ganttProjectId = '')
    {

        $data = array();
        $links = array();

        $projects = Project::select('projects.id', 'projects.project_name', 'projects.start_date', 'projects.deadline', 'projects.completion_percent');

        if (!$this->user->can('view_projects')) {
            $projects = $projects->join('project_members', 'project_members.project_id', '=', 'projects.id');
            $projects = $projects->where('project_members.user_id', '=', $this->user->id);
        }

        if($ganttProjectId != '')
        {
            $projects = $projects->where('projects.id', '=', $ganttProjectId);
        }

        $projects = $projects->get();

        $id = 0; //count for gantt ids
        foreach ($projects as $project) {
            $id = $id + 1;
            $projectId = $id;

            // TODO::ProjectDeadline to do
            $projectDuration = 0;
            if ($project->deadline) {
                $projectDuration = $project->deadline->diffInDays($project->start_date);
            }

            $data[] = [
                'id' => $projectId,
                'text' => ucwords($project->project_name),
                'start_date' => $project->start_date->format('Y-m-d H:i:s'),
                'duration' => $projectDuration,
                'progress' => $project->completion_percent / 100,
                'project_id' => $project->id,
                'dependent_task_id' => null
            ];

            $tasks = Task::projectOpenTasks($project->id);

            foreach ($tasks as $key => $task) {
                $id = $id + 1;

                $taskDuration = $task->due_date->diffInDays($task->start_date);
                $taskDuration = $taskDuration + 1;

                $data[] = [
                    'id' => $task->id,
                    'text' => ucfirst($task->heading),
                    'start_date' => (!is_null($task->start_date)) ? $task->start_date->format('Y-m-d') : $task->due_date->format('Y-m-d'),
                    'duration' => $taskDuration,
                    'parent' => $projectId,
                    'taskid' => $task->id,
                    'dependent_task_id' => $task->dependent_task_id
                ];

                $links[] = [
                    'id' => $id,
                    'source' => $task->dependent_task_id != '' ? $task->dependent_task_id : $projectId,
                    'target' => $task->id,
                    'type' => $task->dependent_task_id != '' ? 0 : 1
                ];
            }
        }

        $ganttData = [
            'data' => $data,
            'links' => $links
        ];

        return response()->json($ganttData);
    }

    public function updateTaskDuration(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
        $task->save();

        return Reply::success('messages.taskUpdatedSuccessfully');
    }

    public function ajaxCreate(Request $request, $projectId)
    {
        $this->projectId = $projectId;
        $projects = Project::select('projects.*');

        if (!$this->user->can('view_projects')) {
            $projects = $projects->join('project_members', 'project_members.project_id', '=', 'projects.id');
            $projects = $projects->where('project_members.user_id', '=', $this->user->id);
        }

        $projects = $projects->get();
        $this->projects = $projects;
        $this->employees = ProjectMember::byProject($projectId);
        $this->pageName = 'ganttChart';
        $this->parentGanttId = $request->parent_gantt_id;
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('project_id', $projectId)
                ->get();
        } else {
            $this->allTasks = [];
        }
        return view('member.all-tasks.ajax_create', $this->data);
    }

    /**
     * Project discussions
     *
     * @param  int $projectId
     * @return \Illuminate\Http\Response
     */
    public function discussion(MemberDiscussionDataTable $dataTable, $projectId)
    {
        $this->project = Project::findOrFail($projectId);
        $this->discussionCategories = DiscussionCategory::orderBy('order', 'asc')->get();
        return $dataTable->with('project_id', $projectId)->render('member.projects.discussion.show', $this->data);
    }

    /**
     * Project discussions
     *
     * @param  int $projectId
     * @param  int $discussionId
     * @return \Illuminate\Http\Response
     */
    public function discussionReplies($projectId, $discussionId)
    {
        $this->project = Project::findOrFail($projectId);
        $this->discussion = Discussion::with('category')->findOrFail($discussionId);
        $this->discussionReplies = DiscussionReply::with('user')->where('discussion_id', $discussionId)->orderBy('id', 'asc')->get();
        return view('member.projects.discussion.replies', $this->data);
    }
    
    public function burndownChart(Request $request, $id)
    {

        $this->project = Project::with(['tasks' => function ($query) use ($request) {
            if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
                $query->where(DB::raw('DATE(`start_date`)'), '>=', Carbon::createFromFormat($this->global->date_format, $request->startDate));
            }

            if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
                $query->where(DB::raw('DATE(`due_date`)'), '<=', Carbon::createFromFormat($this->global->date_format, $request->endDate));
            }
        }])->find($id);

        $this->totalTask = $this->project->tasks->count();
        $datesArray = [];
        $startDate = $request->startDate ? Carbon::createFromFormat($this->global->date_format, $request->startDate) : Carbon::parse($this->project->start_date);
        if ($this->project->deadline) {
            $endDate = $request->endDate ? Carbon::createFromFormat($this->global->date_format, $request->endDate) : Carbon::parse($this->project->deadline);
        } else {
            //$endDate = $request->endDate ? Carbon::parse($request->endDate) : Carbon::now();
            $endDate = $request->endDate ? Carbon::createFromFormat($this->global->date_format, $request->endDate) : Carbon::now();
        }

        for ($startDate; $startDate <= $endDate; $startDate->addDay()) {
            $datesArray[] = $startDate->format($this->global->date_format);
        }

        $uncompletedTasks = [];
        $createdTasks = [];
        $deadlineTasks = [];
        $deadlineTasksCount = [];
        $this->datesArray = json_encode($datesArray);
        foreach ($datesArray as $key => $value) {
            if (Carbon::createFromFormat($this->global->date_format, $value)->lessThanOrEqualTo(Carbon::now())) {
                $uncompletedTasks[$key] = $this->project->tasks->filter(function ($task) use ($value) {
                    if (is_null($task->completed_on)) {
                        return true;
                    }
                    return $task->completed_on ? $task->completed_on->greaterThanOrEqualTo(Carbon::createFromFormat($this->global->date_format, $value)) : false;
                })->count();
                $createdTasks[$key] = $this->project->tasks->filter(function ($task) use ($value) {
                    return Carbon::createFromFormat($this->global->date_format, $value)->startOfDay()->equalTo($task->created_at->startOfDay());
                })->count();
                if ($key > 0) {
                    $uncompletedTasks[$key] += $createdTasks[$key];
                }
            }
            $deadlineTasksCount[] = $this->project->tasks->filter(function ($task) use ($value) {
                return Carbon::createFromFormat($this->global->date_format, $value)->startOfDay()->equalTo($task->due_date->startOfDay());
            })->count();
            if ($key == 0) {
                $deadlineTasks[$key] = $this->totalTask - $deadlineTasksCount[$key];
            } else {
                $newKey = $key - 1;
                $deadlineTasks[$key] = $deadlineTasks[$newKey] - $deadlineTasksCount[$key];
            }
        }

        $this->uncompletedTasks = json_encode($uncompletedTasks);
        $this->deadlineTasks = json_encode($deadlineTasks);
        if ($request->ajax()) {
            return $this->data;
        }

        $this->startDate = $request->startDate ? Carbon::parse($request->startDate)->format($this->global->date_format) : Carbon::parse($this->project->start_date)->format($this->global->date_format);
        $this->endDate = $endDate->format($this->global->date_format);

        return view('member.projects.burndown', $this->data);
    }





}
