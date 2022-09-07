<?php

namespace App\Http\Controllers\Member;

use App\Helper\Reply;
use App\Http\Requests\TemplateTasks\StoreTask;
use App\ProjectTemplate;
use App\ProjectTemplateTask;
use App\Traits\ProjectProgress;
use App\ProjectTemplateTaskUser;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\User;




class ProjectTemplateTaskController extends MemberBaseController
{

    use ProjectProgress;

    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = 'app.menu.projectTemplateTask';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param StoreTask $request
     * @return array
     */
    public function store(StoreTask $request)
    {

        $task = new ProjectTemplateTask();
        $task->heading = $request->heading;
        if($request->description != ''){
            $task->description = $request->description;
        }
        $task->milestone_id = $request->milestone_id;
       
        
        $task->project_template_id = $request->project_id;
        $task->save();

        // Sync task users
        $task->users_many()->sync($request->user_id);


        return Reply::success(__('messages.templateTaskCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->project = ProjectTemplate::findOrFail($id);
        $this->employees = User::allEmployees();
        return view('member.project-template.tasks.show', $this->data);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function taskDetail($id)
    {
        $this->task = ProjectTemplateTask::with('projectTemplate')->findOrFail($id);
        return view('member.project-template.tasks.task-detail', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->task = ProjectTemplateTask::findOrFail($id);
        $this->employees = User::allEmployees();
        $view = view('member.project-template.tasks.edit', $this->data)->render();
        return Reply::dataOnly(['html' => $view]);
    }

    /**
     * @param StoreTask $request
     * @param $id
     * @return array
     */
    public function update(StoreTask $request, $id)
    {
        $task = ProjectTemplateTask::findOrFail($id);
        $task->heading = $request->heading;

        if($request->description != ''){
            $task->description = $request->description;
        }
       
        $task->milestone_id = $request->milestone_id;

        $task->save();
        
        $task->users_many()->sync($request->user_id);

        return Reply::success(__('messages.templateTaskUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete task
        ProjectTemplateTask::destroy($id);

        return Reply::success(__('messages.taskDeletedSuccessfully'));
    }

    public function data(Request $request, $templateId = null) {

        $tasks = ProjectTemplateTask::where('project_template_id', $templateId);

        // $tasks = ProjectTemplateTask::leftJoin('project_templates', 'project_templates.id', '=', 'project_template_tasks.project_template_id')
        //     ->join('users', 'users.id', '=', 'project_template_tasks.user_id')
        //     ->select('project_template_tasks.id', 'project_templates.project_name', 'project_template_tasks.heading', 'users.image', 'project_template_tasks.project_template_id')
        //     ->where('project_templates.id', $templateId);

        $tasks->get();

        return DataTables::of($tasks)
            ->addColumn('action', function($row){
                return '<a href="javascript:;" class="btn btn-info btn-circle edit-task"
                      data-toggle="tooltip" data-task-id="'.$row->id.'" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        &nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-task-id="'.$row->id.'" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn('heading', function($row){
                return ucfirst($row->heading);
            })

             ->addIndexColumn()
            ->rawColumns(['action', 'heading'])
            ->removeColumn('project_template_id')
            ->make(true);
    }

}
