<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AllTasksDataTable;
use App\Events\TaskReminderEvent;
use App\FileStorage;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Tasks\StoreTask;
use App\Notifications\TaskReminder;
use App\Project;
use App\ProjectMember;
use App\Task;
use App\TaskboardColumn;
use App\TaskCategory;
use App\TaskFile;
use App\TaskTag;
use App\TaskTagList;
use App\TaskUser;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\TaskTemplate;
use App\Pinned;
use App\Role;
use App\ClientDetails;
use Illuminate\Support\Facades\Hash;
use App\RoleUser;
use Illuminate\Support\Facades\File;
use App\ProjectTimeLog;
use App\ProjectMilestone;

class ManageAllTasksController extends AdminBaseController
{
    use ProjectProgress;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.tasks';
        $this->pageIcon = 'ti-layout-list-thumb';
        $this->middleware(function ($request, $next) {
            if (!in_array('tasks', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(AllTasksDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/task/taskboard'));
        $this->projects = Project::all();
        $this->clients = User::allClients();
        $this->employees = User::allEmployees();
        $this->taskBoardStatus = TaskboardColumn::all();
        $this->startDate = Carbon::today()->subDays(30)->format($this->global->date_format);
        $this->endDate = Carbon::today()->addDays(30)->format($this->global->date_format);
        
        $this->totalRecords = Task::count();

        // return view('admin.tasks.index', $this->data);
        return $dataTable->render('admin.tasks.index', $this->data);
    }

    public function edit($id)
    {
        $this->task = Task::with(['tags'])->findOrFail($id);
        $this->projects = Project::orderBy('project_name')->get();
        $this->employees = User::allEmployees();
        $this->categories = TaskCategory::all();
        $this->taskBoardColumns = TaskboardColumn::all();
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('id', '!=', $id);

            if ($this->task->project_id != '') {
                $this->allTasks = $this->allTasks->where('project_id', $this->task->project_id);
            }

            $this->allTasks = $this->allTasks->get();
        } else {
            $this->allTasks = [];
        }

        $this->upload = can_upload();

        return view('admin.tasks.edit', $this->data);
    }

    public function update(StoreTask $request, $id)
    {
        $task = Task::findOrFail($id);
        $oldStatus = TaskboardColumn::findOrFail($task->board_column_id);

        $task->heading = $request->heading;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->board_column_id = $request->status;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable == 'true' ? 1 : 0;

        $taskBoardColumn = TaskboardColumn::findOrFail($request->status);

        if ($taskBoardColumn->slug == 'completed') {
            $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            $task->completed_on = null;
        }

        if ($request->project_id != "all") {
            $task->project_id = $request->project_id;
        } else {
            $task->project_id = null;
        }
        $task->save();

        // save tags
        $tags = $request->tags;

        if ($tags) {
            TaskTag::where('task_id', $task->id)->delete();
            foreach ($tags as $tag) {
                $tag = TaskTagList::firstOrCreate([
                    'tag_name' => $tag
                ]);

                TaskTag::create([
                    'tag_id' => $tag->id,
                    'task_id' => $task->id
                ]);
            }
        }

        // Sync task users
        $task->users()->sync($request->user_id);

        $this->calculateProjectProgress($request->project_id);

        return Reply::dataOnly(['taskID' => $task->id]);
        //        return Reply::redirect(route('admin.all-tasks.index'), __('messages.taskUpdatedSuccessfully'));
    }
    
     public function liveUpdate(Request $request, $id) {
        if ($request->ajax()) {
            $task = Task::findOrFail($id);
           

//            if (!is_null($request->name)) {
//                        $task->name = $request->name;
//            }
            if ($request->has('status')) {
                if ($request->status == 1) {
                    $taskColumn = TaskboardColumn::where('slug', '=', 'completed')->first();
                    if ($taskColumn) {
                        $task->board_column_id = $taskColumn->id;
                        $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
                    }
                } else {
                    $taskColumn = TaskboardColumn::where('slug', '=', 'incomplete')->first();
                    if ($taskColumn) {
                        $task->board_column_id = $taskColumn->id;
                        $task->completed_on = null;
                    }
                }
            }
            
            if ($request->has('board_column_id')) {
                $task->board_column_id = $request->board_column_id; 
                $taskColumn = TaskboardColumn::findOrFail($request->board_column_id);

                if ($taskColumn->slug == 'completed') {
                    $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $task->completed_on = null;
                }
            }
            
            if ($request->has('start_date') && $request->has('due_date')) {
              
                $start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date);
                $due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date);
                
                if ($due_date->lt($start_date)){
                    return Reply::error('The due date must be a date after or equal to start date.');
                }
                $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
                $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
            }
            if ($request->has('priority')) {
                 $task->priority = $request->priority;
             }
             if ($request->has('is_client')) {
                 $task->is_client = $request->is_client;
             }
             
             if ($request->has('project_id')) {
                 $task->project_id = $request->project_id;
                 $task->milestone_id = null;
             }
             
             if ($request->has('category_id')) {
                 $task->task_category_id = $request->category_id;
             }
            if ($request->has('milestone_id')) {
                 $task->milestone_id = $request->milestone_id;
             }
             
             
             if ($request->has('tags')) {
                    // save tags
                    $tags = $request->tags;
                    if ($tags) {
                        TaskTag::where('task_id', $task->id)->delete();
                        foreach ($tags as $tag) {
                            $tag = TaskTagList::firstOrCreate([
                                'tag_name' => $tag
                            ]);
                            TaskTag::create([
                                'tag_id' => $tag->id,
                                'task_id' => $task->id
                            ]);
                        }
                    }
                 
             }
             
             if ($request->has('description')) {
                $task->description = $request->description;
             }
             
            $task->save();
            
            if ($request->has('user_id')) {
               // Sync task users
                if(count($request->user_id) > 0) {
                    $task->users()->sync($request->user_id);
                }
            }
            
            $tagsArr = [];
            if($task->tags) {
               foreach ($task->tags as $tag) {
                   $tagsArr[] = $tag->tag->tag_name;
               }
            }
            
            $assigned_users = '';
            if($task->users) {
                foreach ($task->users as $member){
                    if($member->image && $member->image !=''){
                        $assigned_users .= '<img data-toggle="tooltip" data-original-title="'.$member->name.'" src="'.$member->image_url.'" />';
                    } else {
                        $assigned_users .= '<span data-toggle="tooltip" data-original-title="'.$member->name.'" class="nameletter">'.company_initials().'</span>';
                    }
                }
            }
            
            $milestone_select = '<option value="">--</option>';;
            $milestones = [];
            if(isset($task->project_id) && !is_null($task->project_id)) {
                $milestones = ProjectMilestone::with('currency')->where('project_id', $task->project_id)->get();
                if($milestones) {
                    foreach ($milestones as $milestone) {
                         $milestone_select .= '<option value="' . $milestone->id . '">' . $milestone->milestone_title . '</option>';
                    }
                }
            }
            
            $view = '';
            return Reply::successWithData(__('messages.taskUpdatedSuccessfully'), ['html' => $view, 'tagsArr' => $tagsArr, 'description' => $task->description , 'assigned_users' => $assigned_users , 'milestone_select' => $milestone_select]);
        }
    }
    
    
    public function liveTimeLog(Request $request, $id)
    {
        $activeTimer = ProjectTimeLog::with('user')
            ->whereNull('end_time')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id')
            ->where('user_id', $this->user->id)->select('project_time_logs.id')->first();
        
        // if already started then stop
        if($activeTimer){
            $timeId = $activeTimer->id;
            $timeLog = ProjectTimeLog::findOrFail($timeId);
            $timeLog->end_time = Carbon::now();
            $timeLog->save();

            $timeLog->total_hours = $timeLog->end_time->diff($timeLog->start_time)->format('%d') * 24 + $timeLog->end_time->diff($timeLog->start_time)->format('%H');
            $timeLog->total_minutes = ($timeLog->total_hours * 60) + ($timeLog->end_time->diff($timeLog->start_time)->format('%i'));
            $timeLog->edited_by_user = $this->user->id;
            $timeLog->save();
        }
        
        $timeLog = new ProjectTimeLog();
        
        $taskId = $request->task_id;
        
        $project_id = '';
        $task = Task::findOrFail($taskId);
        if($task) {
            if(isset($task->project_id)) {
                $project_id = $task->project_id;
            }
        }
        
        if ($request->project_id != "") {
            $project_id = $request->project_id;
        }
        
        if($project_id != "") {
            $timeLog->project_id = $project_id;
        }

        $timeLog->task_id = $taskId;
        $timeLog->user_id = $this->user->id;
        $timeLog->start_time = Carbon::now();
        $timeLog->memo = 'task time';
        $timeLog->save();

        $this->logUserActivity($this->user->id, __('messages.timerStartedTask') . ucwords($timeLog->task->heading));
        if ($project_id != "") {
            $this->logProjectActivity($project_id, __('messages.timerStartedBy') . ' ' . ucwords($timeLog->user->name));
            $this->logUserActivity($this->user->id, __('messages.timerStartedProject') . ucwords($timeLog->project->project_name));
        }
       $activeTimer = ProjectTimeLog::taskActiveTimer($id);
        
        $view = '';
        return Reply::successWithData(__('messages.timerStartedSuccessfully'), ['html' => $view, 'activeTimer' => $activeTimer->timer, 'activeTimerID' => $activeTimer->id]);
    }
    
    public function liveTimeLogStop(Request $request, $id)
    {
        
        $timeId = $request->timeId;
        $timeLog = ProjectTimeLog::findOrFail($timeId);
        $timeLog->end_time = Carbon::now();
        $timeLog->save();

        $timeLog->total_hours = $timeLog->end_time->diff($timeLog->start_time)->format('%d') * 24 + $timeLog->end_time->diff($timeLog->start_time)->format('%H');
        $timeLog->total_minutes = ($timeLog->total_hours * 60) + ($timeLog->end_time->diff($timeLog->start_time)->format('%i'));
        $timeLog->edited_by_user = $this->user->id;
        $timeLog->save();
        
        
        
        $timeLog = ProjectTimeLog::with('task', 'project')->join('users', 'users.id', '=', 'project_time_logs.user_id')
        ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
        ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
        ->leftJoin('projects', 'projects.id', '=', 'project_time_logs.project_id')
        ->where('tasks.id', '=', $id);

        $timeLog = $timeLog->select('project_time_logs.id', 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.total_hours', 'project_time_logs.total_minutes', 'project_time_logs.memo', 'project_time_logs.user_id', 'project_time_logs.project_id', 'project_time_logs.task_id', 'users.name', 'employee_details.hourly_rate', 'project_time_logs.earnings', 'project_time_logs.approved');
        $timeLog = $timeLog->get();
        
        $total_minutes = 0;
        $earning = 0;
        if($timeLog) {
            foreach ($timeLog as $tl) {
                $total_minutes = $total_minutes + $tl->total_minutes;
                if (!is_null($tl->hourly_rate)) {
                    $hours = intdiv($tl->total_minutes, 60);
                    $minuteRate = $tl->hourly_rate / 60;
                    $earning = $earning + round($tl->total_minutes * $minuteRate);
                    
                }
                
            }
        }
        
        $init = $total_minutes*60;
        $hours = floor($init / 3600);
        $mins = floor(($init / 60) % 60);
        $secs = $init % 60;

        if($hours < 10){
            $hours = '0'.$hours;
        }
        if($mins < 10){
            $mins = '0'.$mins;
        }
        if($secs < 10){
            $secs = '0'.$secs;
        }

        $total_time_format = $hours.':'.$mins.':'.$secs;
        
        $total_time = intdiv($total_minutes, 60) . ' hrs ';
        if (($total_minutes % 60) > 0) {
            $total_time  .= ($total_minutes % 60) . ' mins';
        }
        
        $earning = $this->global->currency->currency_symbol . $earning . ' (' . $this->global->currency->currency_code . ')';
        
        $view = '';
        
        return Reply::successWithData(__('messages.timerStoppedSuccessfully'), ['html' => $view, 'total_time_format' => $total_time_format ,'total_time' => $total_time ,'earning' => $earning]);
        
    }

    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // If it is recurring and allowed by user to delete all its recurring tasks
        if ($request->has('recurring') && $request->recurring == 'yes') {
            Task::where('recurring_task_id', $id)->delete();
        }

        $taskFiles = TaskFile::where('task_id', $id)->get();

        foreach ($taskFiles as $file) {
            Files::deleteFile($file->hashname, 'task-files/' . $file->task_id);
            $file->delete();
        }

        Task::destroy($id);
        //calculate project progress if enabled
        $this->calculateProjectProgress($task->project_id);

        return Reply::success(__('messages.taskDeletedSuccessfully'));
    }

    public function create()
    {
        $this->projects = Project::orderBy('project_name')->get();
        $this->employees = User::allEmployees();
        $this->categories = TaskCategory::all();
        $this->templates = TaskTemplate::orderBy('template_name')->get();
        
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)->get();
        } else {
            $this->allTasks = [];
        }

        $this->upload = can_upload();

        return view('admin.tasks.create', $this->data);
    }

    public function membersList($projectId)
    {
        $this->members = ProjectMember::byProject($projectId);
        $list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }
    public function templateData($projectId)
    {
        $this->template = TaskTemplate::findOrFail($projectId);
        $this->template->tags = $this->template->tags? json_decode($this->template->tags) : array();
        //$list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $this->template]);
    }

    public function dependentTaskLists($projectId, $taskId = null)
    {
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('project_id', $projectId);

            if ($taskId != null) {
                $this->allTasks = $this->allTasks->where('id', '!=', $taskId);
            }

            $this->allTasks = $this->allTasks->get();
        } else {
            $this->allTasks = [];
        }

        $list = view('admin.tasks.dependent-task-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function store(StoreTask $request)
    {
        $ganttTaskArray = [];
        $gantTaskLinkArray = [];
        $taskBoardColumn = TaskboardColumn::where('slug', 'incomplete')->first();
        $task = new Task();
        $task->heading = $request->heading;
        if ($request->description != '') {
            $task->description = $request->description;
        }

        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $task->project_id = $request->project_id;
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->board_column_id = $taskBoardColumn->id;
        $task->created_by = $this->user->id;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable == 'true' ? 1 : 0;

        if ($request->board_column_id) {
            $task->board_column_id = $request->board_column_id;
        }

        if ($taskBoardColumn->slug == 'completed') {
            $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            $task->completed_on = null;
        }

        $task->save();
        
        $newData = collect($task)->toArray();
        
        // create & initialize a curl session
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://hooks.zapier.com/hooks/catch/10950838/b6al0i2/");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($newData));
        $output = curl_exec($curl);
        curl_close($curl);
        

        // save tags
        $tags = $request->tags;

        if ($tags) {
            foreach ($tags as $tag) {
                $tag = TaskTagList::firstOrCreate([
                    'tag_name' => $tag
                ]);

                TaskTag::create([
                    'tag_id' => $tag->id,
                    'task_id' => $task->id
                ]);
            }
        }

        // For gantt chart
        if ($request->page_name && $request->page_name == 'ganttChart') {
            $parentGanttId = $request->parent_gantt_id;

            $taskDuration = $task->due_date->diffInDays($task->start_date);
            $taskDuration = $taskDuration + 1;

            $ganttTaskArray[] = [
                'id' => $task->id,
                'text' => $task->heading,
                'start_date' => $task->start_date->format('Y-m-d'),
                'duration' => $taskDuration,
                'parent' => $parentGanttId,
                'taskid' => $task->id
            ];

            $gantTaskLinkArray[] = [
                'id' => 'link_' . $task->id,
                'source' => $parentGanttId,
                'target' => $task->id,
                'type' => 1
            ];
        }

        // Add repeated task
        if ($request->has('repeat') && $request->repeat == 'yes') {
            $repeatCount = $request->repeat_count;
            $repeatType = $request->repeat_type;
            $repeatCycles = $request->repeat_cycles;
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
            $dueDate = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');


            for ($i = 1; $i < $repeatCycles; $i++) {
                $repeatStartDate = Carbon::createFromFormat('Y-m-d', $startDate);
                $repeatDueDate = Carbon::createFromFormat('Y-m-d', $dueDate);

                if ($repeatType == 'day') {
                    $repeatStartDate = $repeatStartDate->addDays($repeatCount);
                    $repeatDueDate = $repeatDueDate->addDays($repeatCount);
                } else if ($repeatType == 'week') {
                    $repeatStartDate = $repeatStartDate->addWeeks($repeatCount);
                    $repeatDueDate = $repeatDueDate->addWeeks($repeatCount);
                } else if ($repeatType == 'month') {
                    $repeatStartDate = $repeatStartDate->addMonths($repeatCount);
                    $repeatDueDate = $repeatDueDate->addMonths($repeatCount);
                } else if ($repeatType == 'year') {
                    $repeatStartDate = $repeatStartDate->addYears($repeatCount);
                    $repeatDueDate = $repeatDueDate->addYears($repeatCount);
                }

                $newTask = new Task();
                $newTask->heading = $request->heading;
                if ($request->description != '') {
                    $newTask->description = $request->description;
                }
                $newTask->start_date = $repeatStartDate->format('Y-m-d');
                $newTask->due_date = $repeatDueDate->format('Y-m-d');
                $newTask->project_id = $request->project_id;
                $newTask->task_category_id = $request->category_id;
                $newTask->priority = $request->priority;
                $newTask->board_column_id = $taskBoardColumn->id;
                $newTask->created_by = $this->user->id;
                $newTask->recurring_task_id = $task->id;

                if ($request->board_column_id) {
                    $newTask->board_column_id = $request->board_column_id;
                }

                if ($taskBoardColumn->slug == 'completed') {
                    $newTask->completed_on = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $newTask->completed_on = null;
                }

                $newTask->save();

                if ($tags) {
                    foreach ($tags as $tag) {
                        $tag = TaskTagList::firstOrCreate([
                            'tag_name' => $tag
                        ]);

                        TaskTag::create([
                            'tag_id' => $tag->id,
                            'task_id' => $newTask->id
                        ]);
                    }
                }

                // For gantt chart
                if ($request->page_name && $request->page_name == 'ganttChart') {
                    $parentGanttId = $request->parent_gantt_id;
                    $taskDuration = $newTask->due_date->diffInDays($newTask->start_date);
                    $taskDuration = $taskDuration + 1;

                    $ganttTaskArray[] = [
                        'id' => $newTask->id,
                        'text' => $newTask->heading,
                        'start_date' => $newTask->start_date->format('Y-m-d'),
                        'duration' => $taskDuration,
                        'parent' => $parentGanttId,
                        'taskid' => $newTask->id
                    ];

                    $gantTaskLinkArray[] = [
                        'id' => 'link_' . $newTask->id,
                        'source' => $parentGanttId,
                        'target' => $newTask->id,
                        'type' => 1
                    ];
                }

                $startDate = $newTask->start_date->format('Y-m-d');
                $dueDate = $newTask->due_date->format('Y-m-d');
            }
        }

        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);

        if (!is_null($request->project_id)) {
            $this->logProjectActivity($request->project_id, __('messages.newTaskAddedToTheProject'));
        }

        //log search
        $this->logSearchEntry($task->id, 'Task ' . $task->heading, 'admin.all-tasks.edit', 'task');

        if ($request->page_name && $request->page_name == 'ganttChart') {

            return Reply::successWithData(
                'messages.taskCreatedSuccessfully',
                [
                    'tasks' => $ganttTaskArray,
                    'links' => $gantTaskLinkArray
                ]
            );
        }

        if ($request->board_column_id) {
            return Reply::redirect(route('admin.taskboard.index'), __('messages.taskCreatedSuccessfully'));
        }
        
        $this->mixPanelTrackEvent('task_created', array('page_path' => '/admin/task/all-tasks/create'));

        return Reply::dataOnly(['taskID' => $task->id]);
        //        return Reply::redirect(route('admin.all-tasks.index'), __('messages.taskCreatedSuccessfully'));
    }

    public function ajaxCreate($columnId)
    {
        $this->projects = Project::all();
        $this->columnId = $columnId;
        $this->employees = User::allEmployees();
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)->get();
        } else {
            $this->allTasks = [];
        }
        return view('admin.tasks.ajax_create', $this->data);
    }

    public function remindForTask($taskID)
    {
        $task = Task::with('users')->findOrFail($taskID);

        // Send  reminder notification to user
        event(new TaskReminderEvent($task));

        return Reply::success('messages.reminderMailSuccess');
    }

    public function show($id)
    {
        
        $this->task = Task::with('board_column', 'subtasks', 'project', 'users', 'files', 'comments', 'tags')->findOrFail($id);
        $this->taskBoardColumns = TaskboardColumn::all();
        $this->taskFiles = TaskFile::where('task_id', $id)->get();
        
        $this->milestones = [];
        if(isset($this->task->project_id) && !is_null($this->task->project_id)) {
            $this->milestones = ProjectMilestone::with('currency')->where('project_id', $this->task->project_id)->get();
        }
        
        
        
        $timeLog = ProjectTimeLog::with('task', 'project')->join('users', 'users.id', '=', 'project_time_logs.user_id')
        ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
        ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
        ->leftJoin('projects', 'projects.id', '=', 'project_time_logs.project_id')
        ->where('tasks.id', '=', $id);

        $timeLog = $timeLog->select('project_time_logs.id', 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.total_hours', 'project_time_logs.total_minutes', 'project_time_logs.memo', 'project_time_logs.user_id', 'project_time_logs.project_id', 'project_time_logs.task_id', 'users.name', 'employee_details.hourly_rate', 'project_time_logs.earnings', 'project_time_logs.approved');
        $timeLog = $timeLog->get();
        
        $total_minutes = 0;
        $earning = 0;
        if($timeLog) {
            foreach ($timeLog as $tl) {
                $total_minutes = $total_minutes + $tl->total_minutes;
                if (!is_null($tl->hourly_rate)) {
                    $hours = intdiv($tl->total_minutes, 60);
                    $minuteRate = $tl->hourly_rate / 60;
                    $earning = $earning + round($tl->total_minutes * $minuteRate);
                    
                }
                
            }
        }
        
        $init = $total_minutes*60;
        $hours = floor($init / 3600);
        $mins = floor(($init / 60) % 60);
        $secs = $init % 60;

        if($hours < 10){
            $hours = '0'.$hours;
        }
        if($mins < 10){
            $mins = '0'.$mins;
        }
        if($secs < 10){
            $secs = '0'.$secs;
        }

        $total_time_format = $hours.':'.$mins.':'.$secs;
        
        $total_time = intdiv($total_minutes, 60) . ' hrs ';
        if (($total_minutes % 60) > 0) {
            $total_time  .= ($total_minutes % 60) . ' mins';
        }
        
        $earning = $this->global->currency->currency_symbol . $earning . ' (' . $this->global->currency->currency_code . ')';
        
        
   
        $this->projects = Project::all();
        $this->employees = User::allEmployees();
        $this->categories = TaskCategory::all();
        
        $this->total_time = $total_time;
        $this->total_time_format = $total_time_format;
        
        $this->earning = $earning;
        
        $this->company_initials = company_initials();
        
        $activeTimerID = null;
        $activeTimer = '00:00:00';
        
        
        $activeTimerObj = ProjectTimeLog::taskActiveTimer($id);
        if($activeTimerObj && isset($activeTimerObj->id)){
            $activeTimerID = $activeTimerObj->id;
            $activeTimer = $activeTimerObj->timer;
        }
        
        $this->activeTimerID = $activeTimerID;
        $this->activeTimer = $activeTimer;
        
        
        $view = view('admin.tasks.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function showFiles($id)
    {
        $this->taskFiles = TaskFile::where('task_id', $id)->get();
        return view('admin.tasks.ajax-file-list', $this->data);
    }

    public function history($id)
    {
        $this->task = Task::with('board_column', 'history', 'history.board_column')->findOrFail($id);
        $view = view('admin.tasks.history', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function getTaskDetail($id)
    {
        $result = Task::with(["project"])->where("id","=",$id)->first();
        return response()->json(["task" => $result]);
    }

    public function updateProjectDuration(Request $request, $id)
    {
        $task = Project::findOrFail($id);
        $task->start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $task->deadline = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
        $task->save();

        return Reply::success('Project updated successfuly');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createTaskboard()
    {
        return view('admin.tasks.create-board', $this->data);
    }
    
    /**
     * @return mixed
     */
    public function pinnedItem()
    {
        $this->pinnedItems = Pinned::join('tasks', 'tasks.id', '=', 'pinned.task_id')
            ->where('pinned.user_id','=',user()->id)
            ->select('tasks.id', 'heading')
            ->get();

        return view('admin.tasks.pinned-task', $this->data);
    }
    
    public function downloadTemplate()
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=tasks-smaple-template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $records = array();
        $records[] = array('title' => 'Task 1', 'project'=>'Project 1', 'task_category' => 'Category 1', 'description'=> 'xyz', 'start_date'=>'19-08-2021', 'due_date'=>'26-09-2021', 'assignee_email'=>'abc@gmail.con', 'assignee_name'=>'ABC', 'make_private'=>'no', 'billable'=>'yes', 'priority'=>'high');
        $records[] = array('title' => 'Task 2', 'project'=>'Project 2',  'task_category' => 'Category 2', 'description'=> 'abc', 'start_date'=>'20-08-2021', 'due_date'=>'27-09-2021', 'assignee_email'=>'xyz@gmail.con', 'assignee_name'=>'XYZ', 'make_private'=>'yes','billable'=>'no', 'priority'=>'low');
        
        $records[] = array('title' => 'Note', 'project'=>'Date format must be DD-MM-YYYY',  'task_category' => '', 'description'=> '', 'start_date'=>'', 'due_date'=>'', 'assignee_email'=>'', 'assignee_name'=>'', 'make_private'=>'','billable'=>'', 'priority'=>'');
        
       
        $columns = array('Title', 'Project', 'Task Category', 'Description', 'Start Date', 'Due Date', 'Assignee Email', 'Assignee Name', 'Make Private', 'Billable', 'Priority');
        
        $callback = function() use ($records, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($records as $record) {
                fputcsv($file, array($record['title'], $record['project'], $record['task_category'], $record['description'], $record['start_date'], $record['due_date'], $record['assignee_email'], $record['assignee_name'], $record['make_private'], $record['billable'], $record['priority']));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function import(Request $request) {
        
        
        
         $directory = "user-uploads/import-csv/".company()->id;
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }
        
        $file = $request->file('csv_file');
        if($file) {
        // File Details 
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");
        // 2MB in Bytes
        $maxFileSize = 2097152;
        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {
            // Check file size
            if ($fileSize <= $maxFileSize) {
                
                $fileName = time().".csv";
                // Upload file
                $file->move(public_path($directory), $fileName);
                // Import CSV to Database
                $filepath = public_path($directory . "/" . $fileName);
                // Reading file
                $file = fopen($filepath, "r");
                $importData_arr = array();
                $i = 0;
                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata);
                    // Skip first row (Remove below comment if you want to skip the first row)
                   if($i == 0){$i++; continue;} 
                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                fclose($file);
                
//                // Insert to MySQL database
                foreach ($importData_arr as $importData) {
                    // mandotry only name field
                    if(!empty($importData[0])) {
                        $this->addImported($importData);
                    }
                }
                
                \Session::put('success', 'Import Successful.');
                return redirect(route('admin.all-tasks.index'));
            } else {
                \Session::put('error', 'File too large. File must be less than 2MB.');
                return redirect(route('admin.all-tasks.index'));
            }
        } else {
            \Session::put('error', 'Invalid File Extension.');
            return redirect(route('admin.all-tasks.index'));
        }
        } else {
            \Session::put('error', 'Select File.');
            return redirect(route('admin.all-tasks.index'));
        }
    }
    
    public function addImported($req){
        
        //$req[0] // Title
        //$req[1] // Project
        //$req[2] // Task Category
        //$req[3] // Description
        //$req[4] // Start Date
        //$req[5] // Due Date
        //$req[6] // Assignee Email
        //$req[7] // Assignee Name
        //$req[8] // Make Private
        //$req[9] // Billable
        //$req[10] // Priority
        
        $userID = '';
        $categoryID = null;
        $projectID = null;
        
        // check if client is set 
        if(isset($req[6]) && !empty($req[6])) {
            $email = trim($req[6]);
            
            $existing_user = User::withoutGlobalScope(CompanyScope::class)->select('id', 'email')->where('email', $email)->first();
            if(!$existing_user) {
                $password = str_random(8);
                // create new user
                $user = new User();
                $user->name = isset($req[7]) ? $req[7] : 'no name';
                $user->email = $email;
                $user->password = Hash::make($password);
                $user->save();
                $userID = $user->id;
                // create employee 
                $user->employeeDetail()->create([
                    'qbo_id' => '',
                ]);
                // attach role
                $role = Role::where('name', 'employee')->first();
                $user->attachRole($role->id);
                
            } else {
                $userID = $existing_user->id;
            }
        }
        // client script end
        
        // check if project exit;
        if(isset($req[1]) && !empty($req[1])) {
            $project = Project::where('project_name', 'like', $req[1])->first();
            if($project) {
                $projectID = $project->id;
            }
            
        }
        
        // task category
        
        if(isset($req[2]) && !empty($req[2])) {
            $category = TaskCategory::where('category_name', 'like', $req[2])->first();
            if(!$category) {
                $category = new TaskCategory();
                $category->category_name = $req[2];
                $category->save();
            }
            $categoryID = $category->id;
        }
        
        // task category end   
        $task = new Task();
        
        $task->heading = isset($req[0]) ? $req[0] : '';
        $task->project_id = $projectID;
        $task->task_category_id = $categoryID;
        $task->description = isset($req[3]) ? $req[3] : null;
        
        if(isset($req[4]) && !empty($req[4])) {
            $task->start_date = Carbon::createFromFormat('d-m-Y', $req[4])->format('Y-m-d');
        }
        
        if(isset($req[5]) && !empty($req[5])) {
            $task->due_date = Carbon::createFromFormat('d-m-Y', $req[5])->format('Y-m-d');
        }
        
        if(isset($req[8]) && !empty($req[8]) && $req[8] == 'yes') {
            $task->is_private = 1;
        } else {
            $task->is_private = 0;
        }
        
        if(isset($req[9]) && !empty($req[9]) && $req[9] == 'yes') {
            $task->billable = 1;
        } else {
            $task->billable = 0;
        }
        
        if(isset($req[10]) && !empty($req[10])) {
            $task->priority = strtolower($req[10]);
        } else {
            $task->priority = 'low';
        }
        
        $task->created_by = $this->user->id;
        $task->save();
        
        if(!empty($userID)) {
            $task->users()->sync(array($userID));
        }
        if($projectID) {
            $this->calculateProjectProgress($projectID);
        }
        
    }

}
