<?php

namespace App\DataTables\Member;

use App\ClientDetails;
use App\DataTables\BaseDataTable;
use App\VendorDetails;
use App\PurchaseOrder;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Project;
use App\ClientVendorDetails;
use App\PoStatus;

class MemberPurchaseOrdersDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('member.purchase-orders.edit', $row->id) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>';

                //<li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                // $action = '<div class="btn-group dropdown m-r-10">
                //   <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                //   <ul role="menu" class="dropdown-menu pull-right">
                //     <li><a href="' . route('member.vendor.edit', $row->id) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                //     <li><a href="' . route('member.vendor.showVendor', $row->id) . '"><i class="fa fa-search" aria-hidden="true"></i> ' . __('app.view') . '</a></li>
                //     <li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                //     <li><a href="javascript:sendEmail();"><i class="fa fa-envelope" aria-hidden="true"></i> Email</a></li>';
                
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
                 
//            ->editColumn('vendor_name', function ($row) {
//                if ($row->vendor_id) {
//                    return $row->vendor->name;
//                }
//                return $row->vendor_id;
//            })
            ->addIndexColumn()
            ->rawColumns(['purchase_order_number', 'action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseOrder $model)
    {
        $model =  $model->select('id', 'purchase_order_number', 'vendor_id', 'status_id', 'project_id', 'document_tags', 'purchase_order_date', 'status');
        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('vendors-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->orderBy(0)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->language(__("app.datatable"))
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["vendors-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                    $(".statusChange").selectpicker();
                }',
            ])
            ->buttons(Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>']))
                ;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            'Item' => ['data' => 'purchase_order_number', 'name' => 'purchase_order_number'],
            'Vendor' => ['data' => 'vendor_name', 'name' => 'vendor_name'],
            'projectName' => ['data' => 'project_name', 'name' => 'project_name'],
            'Document Tags' => ['data' => 'document_tags', 'name' => 'document_tags'],
            'PO Date' => ['data' => 'purchase_order_date', 'name' => 'purchase_order_date'],
            'Order Status' => ['data' => 'status', 'name' => 'status'],
            
            
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-center')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'vendors_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }
}
