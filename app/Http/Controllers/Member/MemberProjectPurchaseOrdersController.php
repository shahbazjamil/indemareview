<?php

namespace App\Http\Controllers\Member;

use App\Project;
use App\Helper\Reply;
use Yajra\DataTables\Facades\DataTables;
use App\Currency;
use App\PurchaseOrder;
use App\ClientVendorDetails;
use App\PoStatus;

class MemberProjectPurchaseOrdersController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            if (!in_array('purchaseOrders', $this->user->modules)) {
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(StoreMilestone $request)
//    {
//        $milestone = new ProjectMilestone();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->project = Project::findorFail($id);
        $this->currencies = Currency::all();
        return view('member.projects.purchase-order.show', $this->data);
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
    
    
      /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $project = PurchaseOrder::withTrashed()->findOrFail($id);
        $project->forceDelete();
        
        return Reply::success('Purchase Order deleted successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function archiveDestroy($id)
    {
        PurchaseOrder::destroy($id);
        return Reply::success('Purchase Order archive successfully.');
    }

    public function data($id)
    {
        $purchaseOrders = PurchaseOrder::where('project_id', $id)->get();

        return DataTables::of($purchaseOrders)
            ->addColumn('action', function ($row) {
              $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('member.purchase-orders.edit', $row->id) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>';
              
                $action .= '<li><a href="javascript:;" data-user-id="' . $row->id . '" class="archive"><i class="fa fa-archive" aria-hidden="true"></i> ' . trans('app.archive') . '</a></li>';
                $action .= '<li><a href="javascript:;" data-user-id="' . $row->id . '" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>';
                
                $action .= '<li><a href="' . route("member.purchase-orders.download", $row->id) . '"><i class="fa fa-download"></i> ' . __('app.download') . '</a></li>';
                $action .= '<li><a class="sendPDF" data-po-id="'.$row->id.'" href="javascript:void(0)"><i class="fa fa-envelope"></i> Send PDF</a></li>';

                $action .= '</ul> </div>';
                return $action;
            })
             ->editColumn(
                'purchase_order_number',
                function ($row) {
                return 'PO-'.$row->purchase_order_number;
                }
            )
            ->editColumn('vendor_name', function ($row) {
                if(!empty($row->vendor_id)) {
                    $vendor = ClientVendorDetails::where('id', $row->vendor_id)->first();
                    if($vendor) {
                        return ucfirst($vendor->vendor_name);
                    }
                    return '--';
                }
               return '--';
                //return $row->project_id;
            })
            ->editColumn('project_name', function ($row) {                
                if(!empty($row->project_id)) {
                    $project = Project::where('id', $row->project_id)->first();
                    if($project) {
                        return ucfirst($project->project_name);
                    }
                    return '--';
                }
                return '--';
                //                if($row->project_id){
//                        return ucfirst($row->project->project_name);
//                }
                //return $row->project_id;
            })
           ->editColumn(
                'document_tags',
                function ($row) {
                        $str_tags = '';
                        $document_tags = json_decode($row->document_tags);
                        
                        if($document_tags) {
                            foreach ($document_tags as $document_tag) {
                                if($str_tags == '') {
                                    $str_tags .=$document_tag;
                                } else {
                                    $str_tags .=','.$document_tag;
                                }
                                
                            }
                        }
                        return $str_tags;
                }
            )
           ->editColumn(
                'purchase_order_date',
                function ($row) {
                    return $row->purchase_order_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
           ->editColumn(
                'status',
                function ($row) {
                    //return ucfirst($row->status);
                    $status = PoStatus::all();
                    $statusLi = '--';
                    foreach ($status as $st) {
                        if ($row->status_id == $st->id) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        $statusLi .= '<option ' . $selected . ' value="' . $st->id . '">' . $st->type . '</option>';
                    }

                    $action = '<select class="form-control statusChange" name="statusChange" onchange="changeStatus( ' . $row->id . ', this.value)">
                        ' . $statusLi . '
                    </select>';


                    return $action;
                
                }
            )
            ->addIndexColumn()
           ->rawColumns(['purchase_order_number', 'action', 'status'])
            ->make(true);
    }

//    public function detail($id)
//    {
//        $this->milestone = ProjectMilestone::findOrFail($id);
//        return view('member.projects.milestones.detail', $this->data);
//    }

    /////
}
