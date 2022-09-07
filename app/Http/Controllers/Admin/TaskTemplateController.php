<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaskTemplate\StoreTask;
use App\TaskTemplate;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskTemplateController extends AdminBaseController
{

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.taskTemplate';
        $this->pageIcon = 'icon-layers';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin.task-template.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.task-template.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTask $request) {
        $task = new TaskTemplate();
        $task->template_name = $request->template_name;
        $task->heading = $request->heading;
        $task->description = $request->description ? $request->description : '';
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable == 'true' ? 1 : 0;
        $task->priority = $request->priority;
        $task->tags = $request->tags ? json_encode($request->tags) : null;
        
        $task->save();

        return Reply::redirect(route('admin.task-template.index'), __('modules.taskTemplate.projectUpdated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $this->template = TaskTemplate::findOrFail($id);
        return view('admin.task-template.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $this->template = TaskTemplate::findOrFail($id);
        $this->template->tags = $this->template->tags? json_decode($this->template->tags) : array();
        return view('admin.task-template.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTask $request, $id) {
        $task = TaskTemplate::findOrFail($id);
        
        $task->template_name = $request->template_name;
        $task->heading = $request->heading;
        $task->description = $request->description ? $request->description : '';
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable == 'true' ? 1 : 0;
        $task->priority = $request->priority;
        $task->tags = $request->tags ? json_encode($request->tags) : null;
        $task->save();

        return Reply::redirect(route('admin.task-template.index', $id), __('messages.taskTemplateUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        TaskTemplate::destroy($id);
        return Reply::success(__('messages.taskTemplateDeleted'));
    }

    public function data(Request $request) {
        $tasks = TaskTemplate::all();

        return DataTables::of($tasks)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.task-template.edit', [$row->id]) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                      <a href="' . route('admin.task-template.show', [$row->id]) . '" class="btn btn-success btn-circle"
                      data-toggle="tooltip" data-original-title="View Project Details"><i class="fa fa-search" aria-hidden="true"></i></a>

                      <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn('template_name', function ($row) {
                return '<a href="' . route('admin.task-template.show', $row->id) . '">' . ucfirst($row->template_name) . '</a>';
            })
            ->rawColumns(['template_name', 'action'])
            ->make(true);
    }
}
