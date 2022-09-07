<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AllTimeLogsDataTable;
use App\Helper\Reply;
use App\Project;
use App\ProjectMember;
use App\ProjectTimeLog;
use App\Task;
use App\TaskUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ManageAllTimeLogController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Time Logs';
        $this->pageIcon = 'icon-clock';
        $this->middleware(function ($request, $next) {
            if (!in_array('timelogs', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(AllTimeLogsDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/all-time-logs'));
        
        if (!request()->ajax()) {
            $this->employees = User::allEmployees();
            
            $this->projects = Project::orderBy('project_name')->get();
            $this->timeLogProjects = $this->projects;
            
            $this->tasks = Task::orderBy('heading')->get();
            $this->timeLogTasks = $this->tasks;

            $this->activeTimers = ProjectTimeLog::with('user')
                ->whereNull('end_time')
                ->join('users', 'users.id', '=', 'project_time_logs.user_id')
                ->select('project_time_logs.*', 'users.name')
                ->count();

            $this->startDate = Carbon::today()->subDays(15)->format($this->global->date_format);
            $this->endDate = Carbon::today()->addDays(15)->format($this->global->date_format);

        }
        return $dataTable->render('admin.time-logs.index', $this->data);
        // return view('admin.time-logs.index', $this->data);
    }


    public function byEmployee()
    {
        $this->employees = User::allEmployees();
        $this->projects = Project::all();
        $this->timeLogProjects = $this->projects;
        $this->tasks = Task::all();
        $this->timeLogTasks = $this->tasks;

        $this->activeTimers = ProjectTimeLog::with('user')
            ->whereNull('end_time')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id')
            ->select('project_time_logs.*', 'users.name')
            ->count();

        $this->startDate = Carbon::today()->subDays(7)->format($this->global->date_format);
        $this->endDate = Carbon::today()->format($this->global->date_format);
        return view('admin.time-logs.by_employee', $this->data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showActiveTimer()
    {
        $this->activeTimers = ProjectTimeLog::with('user')
            ->whereNull('end_time')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id');

        $this->activeTimers = $this->activeTimers
            ->select('project_time_logs.*', 'users.name', 'users.image')
            ->get();

        return view('admin.time-logs.show-active-timer', $this->data);
    }
    
    public function calculateTime(Request $request)
    {
        
        $start_time = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('H:i:s');
        $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $start_time, $this->global->timezone)->setTimezone('UTC');
        $end_time   = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('H:i:s');
        $end_time   = Carbon::createFromFormat('Y-m-d H:i:s', $end_time, $this->global->timezone)->setTimezone('UTC');
        
        $total_hours = $end_time->diff($start_time)->format('%d') * 24 + $end_time->diff($start_time)->format('%H');
        $total_minutes = ($end_time->diff($start_time)->format('%i'));
        
        return Reply::dataOnly(['total_hours' => $total_hours, 'total_minutes' => $total_minutes]);
    }

    public function destroy($id)
    {
        ProjectTimeLog::destroy($id);
        return Reply::success(__('messages.timeLogDeleted'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function stopTimer(Request $request)
    {
        $timeId = $request->timeId;
        $timeLog = ProjectTimeLog::findOrFail($timeId);
        $timeLog->end_time = Carbon::now();
        $timeLog->edited_by_user = $this->user->id;
        $timeLog->save();

        $timeLog->total_hours = ($timeLog->end_time->diff($timeLog->start_time)->format('%d') * 24) + ($timeLog->end_time->diff($timeLog->start_time)->format('%H'));

        if ($timeLog->total_hours == 0) {
            $timeLog->total_hours = round(($timeLog->end_time->diff($timeLog->start_time)->format('%i') / 60), 2);
        }
        $timeLog->total_minutes = ($timeLog->total_hours * 60) + ($timeLog->end_time->diff($timeLog->start_time)->format('%i'));

        $timeLog->save();

        $this->activeTimers = ProjectTimeLog::whereNull('end_time')
            ->get();
        $view = view('admin.projects.time-logs.active-timers', $this->data)->render();
        return Reply::successWithData(__('messages.timerStoppedSuccessfully'), ['html' => $view, 'activeTimers' => count($this->activeTimers)]);
    }
    /**
     * @param $projectId
     * @return mixed
     * @throws \Throwable
     */
    public function membersList($projectId)
    {
        if ($projectId == "0") {
            $this->employees = User::allEmployees();
            $this->tasks = Task::orderBy('heading')->get();
        } else {
            $this->members = ProjectMember::byProject($projectId);
            $this->tasks = Task::where('project_id', $projectId)->orderBy('heading')->get();
        }

        $list = view('admin.tasks.members-list', $this->data)->render();
        $tasks = view('admin.tasks.tasks-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list, 'tasks' => $tasks]);
    }

    public function taskMembersList($taskId)
    {

        $this->members = TaskUser::where('task_id', $taskId)->get();

        $list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function data(Request $request)
    {
        $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
        $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
        $employee = $request->employee;
        $projectId = $request->projectID;
        $taskId = $request->taskID;

        $this->employees = User::join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('project_time_logs', 'project_time_logs.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id');

        $where = '';

        if ($projectId != "all") {
            $where .= ' and project_time_logs.project_id="' . $projectId . '" ';
        }

        if ($taskId != "all") {
            $where .= ' and project_time_logs.task_id="' . $taskId . '" ';
        }

        $this->employees = $this->employees->select(
            'users.name',
            'users.image',
            'users.id',
            'designations.name as designation_name',
            DB::raw(
                "(SELECT SUM(project_time_logs.total_minutes) FROM project_time_logs WHERE project_time_logs.user_id = users.id and DATE(project_time_logs.start_time) >= '" . $startDate . "' and DATE(project_time_logs.start_time) <= '" . $endDate . "' $where GROUP BY project_time_logs.user_id) as total_minutes"
            ),
            DB::raw(
                "(SELECT SUM(project_time_logs.earnings) FROM project_time_logs WHERE project_time_logs.user_id = users.id and DATE(project_time_logs.start_time) >= '" . $startDate . "' and DATE(project_time_logs.start_time) <= '" . $endDate . "' $where GROUP BY project_time_logs.user_id) as earnings"
            )
        );

        if (!is_null($employee) && $employee !== 'all') {
            $this->employees = $this->employees->where('project_time_logs.user_id', $employee);
        }

        if (!is_null($projectId) && $projectId !== 'all') {
            $this->employees = $this->employees->where('project_time_logs.project_id', '=', $projectId);
        }

        if (!is_null($taskId) && $taskId !== 'all') {
            $this->employees = $this->employees->where('project_time_logs.task_id', '=', $taskId);
        }

        $this->employees = $this->employees->groupBy('project_time_logs.user_id')
            ->orderBy('users.name')
            ->get();
        $html = view('admin.time-logs.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $html]);
    }

    public function userTimelogs(Request $request)
    {
        $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
        $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
        $employee = $request->employee;
        $projectId = $request->projectID;
        $taskId = $request->taskID;

        $this->timelogs = ProjectTimeLog::with('project', 'task')->select('*', DB::raw('DATE_FORMAT(start_time,\'%d/%M/%y\') as date'))
            ->whereDate('start_time', '>=', $startDate)
            ->whereDate('start_time', '<=', $endDate)
            ->where('user_id', $employee);

        if ($projectId != "all") {
            $this->timelogs = $this->timelogs->where('project_id', $projectId);
        }

        if ($taskId != "all") {
            $this->timelogs = $this->timelogs->where('task_id', $taskId);
        }

        $this->timelogs = $this->timelogs->orderBy('end_time', 'desc')
            ->get();

        $html = view('admin.time-logs.user-timelogs', $this->data)->render();
        return Reply::dataOnly(['html' => $html]);
    }

    public function approveTimelog(Request $request)
    {
        ProjectTimeLog::where('id', $request->id)->update(
            [
                'approved' => 1,
                'approved_by' => user()->id
            ]
        );
        return Reply::dataOnly(['status' => 'success']);
    }


}
