<?php

namespace App\Http\Controllers\Member;

use App\Project;
use App\Helper\Reply;
use Yajra\DataTables\Facades\DataTables;
use App\Currency;
use App\PurchaseOrder;
use App\ClientVendorDetails;
use App\Estimate;
use Carbon\Carbon;

class MemberProjectEstimatesController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.estimates';
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            if (!in_array('estimates', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->project = Project::findorFail($id);
        $this->currencies = Currency::all();
        return view('member.projects.estimates.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//        $this->milestone = ProjectMilestone::findOrFail($id);
//        $this->currencies = Currency::all();
//        return view('member.projects.milestones.edit', $this->data);
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(StoreMilestone $request, $id)
//    {
//        $milestone = ProjectMilestone::findOrFail($id);
//        $milestone->project_id = $request->project_id;
//        $milestone->milestone_title = $request->milestone_title;
//        $milestone->summary = $request->summary;
//        $milestone->cost = ($request->cost == '') ? '0' : $request->cost;
//        $milestone->currency_id = $request->currency_id;
//        $milestone->status = $request->status;
//        $milestone->save();
//
//        return Reply::success(__('messages.milestoneSuccess'));
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        ProjectMilestone::destroy($id);
//        return Reply::success(__('messages.deleteSuccess'));
//    }

    public function data($id)
    {
        
        $firstEstimate = Estimate::latest()->first();
        $estimates = Estimate::where('project_id', $id)->get();

        return DataTables::of($estimates)
            ->addColumn('action', function ($row) use ($firstEstimate) {
              $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">';
                $action .= '<li><a href="' . route("member.estimates.download", $row->id) . '"><i class="fa fa-download"></i> ' . __('app.download') . '</a></li>';
                $action .= '<li><a href="' . route("front.estimate.show", md5($row->id)) . '" target="_blank"><i class="fa fa-eye"></i> ' . __('app.view') . '</a></li>';
                $action .= '<li><a class="sendPDF" data-po-id="'.$row->id.'" href="javascript:void(0)"><i class="fa fa-envelope"></i> Send PDF</a></li>';
                
                if (!$row->send_status && $row->status != 'draft') {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-estimate-id="' . $row->id . '" class="sendButton"><i class="fa fa-send"></i> ' . __('app.send') . '</a></li>';
                }

                if ($row->status == 'waiting' || $row->status == 'draft') {
                    $action .= '<li><a href="' . route("member.estimates.edit", $row->id) . '" ><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                }
                if ($firstEstimate->id == $row->id) {
                    $action .= '<li><a class="sa-params" href="javascript:;" data-estimate-id="' . $row->id . '"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }
                if ($row->status == 'waiting') {
                    $action .= '<li><a href="' . route("member.all-invoices.convert-estimate", $row->id) . '" ><i class="ti-receipt"></i> ' . __('app.create') . ' ' . __('app.invoice') . '</a></li>';
                }

                $action .= '</ul> </div>';
                return $action;
            })
             ->addColumn('original_estimate_number', function ($row) {
                return $row->original_estimate_number;
            })
             ->editColumn('name', function ($row) {
                 $nameletter = '<span class="nameletter">'.company_initials().'</span>';
                 
                    return  '<div class="row truncate"><div class="col-sm-3 col-xs-4">' . $nameletter . '</div><div class="col-sm-9 col-xs-8"><a href="' . route('member.clients.show', $row->client_id) . '">' . ucwords($row->name) . '</a></div></div>';
                //return '<a href="' . route('member.clients.projects', $row->client_id) . '">' . ucwords($row->name) . '</a>';
            })
             ->editColumn('status', function ($row) {
                $status = '';
                if ($row->status == 'waiting') {
                    $status .= '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                } else if ($row->status == 'draft') {
                    $status .= '<label class="label label-primary">' . strtoupper($row->status) . '</label>';
                } else if ($row->status == 'declined') {
                    $status .= '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                } else {
                    $status .= '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }

                if (!$row->send_status && $row->status != 'draft') {
                    $status .= '<br><br><label class="label label-inverse">' . strtoupper(__('modules.invoices.notSent')) . '</label>';
                }
                return $status;
            })
             ->editColumn('total', function ($row) {
                return currency_position($row->total, $row->currency_symbol);
            })
            ->editColumn(
                'valid_till',
                function ($row) {
                    return Carbon::parse($row->valid_till)->format($this->global->date_format);
                }
            )
            ->addIndexColumn()
           ->rawColumns(['name', 'action', 'status'])
            ->removeColumn('currency_symbol')
            ->removeColumn('client_id')
            ->make(true);
    }

    /////
}
