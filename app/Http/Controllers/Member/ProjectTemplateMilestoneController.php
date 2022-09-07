<?php

namespace App\Http\Controllers\Member;

use App\Helper\Reply;
use App\Http\Requests\Milestone\StoreMilestone;
use App\ProjectTemplate;
use App\ProjectTemplateMilestone;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Currency;


class ProjectTemplateMilestoneController extends MemberBaseController
{


    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = 'modules.projects.milestones';
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
    public function store(StoreMilestone $request)
    {
        
        $milestone = new ProjectTemplateMilestone();
        $milestone->project_template_id = $request->project_id;
        $milestone->milestone_title = $request->milestone_title;
        $milestone->summary = $request->summary;
        $milestone->cost = ($request->cost == '') ? '0' : $request->cost;
        $milestone->currency_id = $request->currency_id;
        $milestone->status = $request->status;
        $milestone->save();
        
        return Reply::success(__('messages.milestoneSuccess'));
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
        $this->currencies = Currency::all();
        return view('member.project-template.milestone.show', $this->data);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function milestoneDetail($id)
    {
        $this->milestone = ProjectTemplateMilestone::with('projectTemplate')->findOrFail($id);
        return view('member.project-template.milestone.milestone-detail', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->milestone = ProjectTemplateMilestone::findOrFail($id);
        $this->currencies = Currency::all();
        $view = view('member.project-template.milestone.edit', $this->data)->render();
        return Reply::dataOnly(['html' => $view]);
    }

    /**
     * @param StoreTask $request
     * @param $id
     * @return array
     */
    public function update(StoreMilestone $request, $id)
    {    
        $milestone = ProjectTemplateMilestone::findOrFail($id);
        $milestone->milestone_title = $request->milestone_title;
        $milestone->summary = $request->summary;
        $milestone->cost = ($request->cost == '') ? '0' : $request->cost;
        $milestone->currency_id = $request->currency_id;
        $milestone->status = $request->status;
        $milestone->save();

        return Reply::success(__('messages.milestoneSuccess'));
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
        ProjectTemplateMilestone::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function data(Request $request, $templateId = null) {
        
        $milestones = ProjectTemplateMilestone::with('currency')->where('project_template_id', $templateId)->get();
        
        return DataTables::of($milestones)
            ->addColumn('action', function($row){
                return '<a href="javascript:;" class="btn btn-info btn-circle edit-milestone"
                      data-toggle="tooltip" data-milestone-id="'.$row->id.'" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        &nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-milestone-id="'.$row->id.'" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            
             ->editColumn('status', function ($row) {
                if ($row->status == 'complete') {
                    return '<label class="label label-success">' . __('app.'.$row->status) . '</label>';
                } else {
                    return '<label class="label label-danger">' . __('app.'.$row->status) . '</label>';
                }
            })
            ->editColumn('cost', function ($row) {
                if (!is_null($row->currency_id)) {
                    return $row->currency->currency_symbol . $row->cost;
                }
                return $row->cost;
            })
            ->editColumn('milestone_title', function ($row) {
                return '<a href="javascript:;" class="show-task-detail" data-milestone-id="' . $row->id . '">' . ucfirst($row->milestone_title) . '</a>';
            })
            ->addIndexColumn()
             ->rawColumns(['status', 'action', 'milestone_title'])
            ->removeColumn('project_template_id')
            ->make(true);
    }

}
