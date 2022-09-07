<?php

namespace App\DataTables\Member;

use App\ClientDetails;
use App\DataTables\BaseDataTable;
use App\VendorDetails;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class VendorsDataTable extends BaseDataTable
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
                //return '';
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('member.vendor.edit', $row->id) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>';
                  
                //<li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                //<li><a href="javascript:sendEmail(\''. $row->vendor_email .'\');"><i class="fa fa-envelope" aria-hidden="true"></i> Email</a></li>';

                // $action = '<div class="btn-group dropdown m-r-10">
                //   <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                //   <ul role="menu" class="dropdown-menu pull-right">
                //     <li><a href="' . route('admin.vendor.edit', $row->id) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                //     <li><a href="' . route('admin.vendor.showVendor', $row->id) . '"><i class="fa fa-search" aria-hidden="true"></i> ' . __('app.view') . '</a></li>
                //     <li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                //     <li><a href="javascript:sendEmail();"><i class="fa fa-envelope" aria-hidden="true"></i> Email</a></li>';

                $action .= '</ul> </div>';

                return $action;
            })
            ->editColumn(
                'name',
                function ($row) {
                
                    return '<a href="javascript:;" class="vendor-detail" data-vendor-id="' . $row->id . '">' . ucwords($row->vendor_name) . '</a>';
                     $nameletter = '<span class="nameletter">'.company_initials().'</span>';
                    //return  '<div class="row truncate"><div class="col-sm-3 col-xs-4">' . $nameletter . '</div><div class="col-sm-9 col-xs-8"><a href="javascript:;" class="vendor-detail" data-vendor-id="' . $row->id . '">' . ucwords($row->vendor_name) . '</a></div></div>';
                    //return '<a href="' . route('admin.vendor.showVendor', $row->id) . '">' . ucfirst($row->vendor_name) . '</a>';
                }
            )
            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->addColumn(
                'credentials',
                function ($row) {
                $return = ' ';
                if(isset($row->url) && !empty($row->url)) {
                    $url = $row->url;
                    if (!preg_match("~^(?:f|ht)tps?://~i", $row->url)) {
                        $url = "http://" . $row->url;
                    }
                    //$url = strpos($row->url, 'http') === false ? 'http://' . $row->url : $row->url;
                    $return = '<a href="'.$url.'" target = "_blank" title="Click Here" >Login</a>' .' <a href="javascript:;"  data-toggle="popover" data-html="true" data-placement="top" title="Credentials" data-content="Username :'.$row->user.' <br> Password :'.$row->password.'" ><i title="Click Here" class="fa fa-key" aria-hidden="true"></i></a>';
                }
                return $return;
                    
                }
            )
            ->editColumn(
                'status',
                function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="label label-success">' . __('app.active') . '</label>';
                    } else {
                        return '<label class="label label-danger">' . __('app.inactive') . '</label>';
                    }
                }
            )
            ->editColumn('tags', function ($row) {
                $tags = '';
                if($row->tags) {
                    $tags = $row->tags ? json_decode($row->tags) : array();
                    if($tags) {
                        $tags = implode(', ', $tags);
                    }
                    
                }
                return $tags;
            })
            ->addIndexColumn()
            ->rawColumns(['name', 'action', 'status', 'credentials']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(VendorDetails $model) // ClientDetails
    {
        $model =  $model->select('id', 'vendor_name', 'company_name', 'vendor_email', 'vendor_category', 'vendor_markup', 'url', 'user','password',  'status' ,'tags', 'created_at');
        return $model->orderBy('company_name', 'asc');
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
                 "paging" => false,
                'initComplete' => 'function () {
                   window.LaravelDataTables["vendors-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                    $(\'[data-toggle="popover"]\').popover();
                }',
            ])
            ->buttons(Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>']));
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
            __('modules.client.companyName') => ['data' => 'company_name', 'name' => 'company_name'],
             __('app.name') => ['data' => 'name', 'name' => 'name'],
            __('app.email') => ['data' => 'vendor_email', 'name' => 'vendor_email'],
            'Category' => ['data' => 'vendor_category', 'name' => 'vendor_category'],
            'Markup %' => ['data' => 'vendor_markup', 'name' => 'vendor_markup'],
             'Tags'  => ['data' => 'tags', 'name' => 'tags'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
             'Credentials' => ['data' => 'credentials', 'name' => 'credentials'],
            __('app.createdAt') => ['data' => 'created_at', 'name' => 'created_at'],
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
