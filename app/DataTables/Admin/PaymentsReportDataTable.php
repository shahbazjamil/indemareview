<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;

class PaymentsReportDataTable extends BaseDataTable
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

            ->editColumn('remarks', function ($row) {
                return ucfirst($row->remarks);
            })

            ->editColumn('project_id', function ($row) {
                if (!is_null($row->project)) {
                    return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project->project_name) . '</a>';
                } else {
                    return '--';
                }
            })
            ->editColumn('invoice_number', function ($row) {
                if ($row->invoice_id != null) {
                    return $row->invoice->invoice_number;
                } else {
                    return '--';
                }
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'pending') {
                    return '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                } else {
                    return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }
            })
            ->editColumn('amount', function ($row) {
                //return (float) $row->amount;
                $symbol = (!is_null($row->currency)) ? $row->currency->currency_symbol : '';
                $code = (!is_null($row->currency)) ? $row->currency->currency_code : '';
                return currency_position((float) $row->amount, $symbol).' (' . $code . ')';
            })
            ->editColumn('markup_amount', function ($row) {
                $symbol = (!is_null($row->currency)) ? $row->currency->currency_symbol : '';
                $code = (!is_null($row->currency)) ? $row->currency->currency_code : '';
                return currency_position((float) $row->invoice->markup_total, $symbol).' (' . $code . ')';
            })
            ->editColumn('total_tax', function ($row) {
                $symbol = (!is_null($row->currency)) ? $row->currency->currency_symbol : '';
                $code = (!is_null($row->currency)) ? $row->currency->currency_code : '';
                return currency_position((float) $row->invoice->total_tax, $symbol).' (' . $code . ')';
            })
            
            
            ->editColumn(
                'paid_on',
                function ($row) {
                    if (!is_null($row->paid_on)) {
                        return $row->paid_on->format($this->global->date_format . ' ' . $this->global->time_format);
                    }
                }
            )
            ->addIndexColumn()
            ->rawColumns(['invoice', 'status', 'project_id'])
            ->removeColumn('invoice_id')
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code')
            ->removeColumn('project_name');

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Payment $model)
    {
        $request = $this->request();

        $model = $model->with(['project:id,project_name', 'currency:id,currency_symbol,currency_code', 'invoice'])
            ->leftJoin('projects', 'projects.id', '=', 'payments.project_id')
            ->select('payments.id', 'payments.project_id', 'payments.currency_id', 'payments.invoice_id', 'payments.amount' , 'payments.markup_amount', 'payments.status', 'payments.paid_on', 'payments.remarks');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(payments.`paid_on`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(payments.`paid_on`)'), '<=', $endDate);
        }

        $model = $model->where('payments.status', '=', 'complete');

        if ($request->project != 'all' && !is_null($request->project)) {
            $model = $model->where('payments.project_id', '=', $request->project);
        }

        if ($request->client != 'all' && !is_null($request->client)) {
            $model = $model->where('projects.client_id', '=', $request->client);
        }

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
            ->setTableId('payments-table')
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
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            )
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["payments-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
                'footerCallback' => 'function ( row, data, start, end, display ) {
                    document.getElementById("payments-table").deleteTFoot()
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === "string" ?
                            i.replace(/[\$,]/g, "")*1 :
                            typeof i === "number" ?
                                i : 0;
                    };
                    
                    totalAmount = api
                        .column( 4, { page: "current"} )
                        .data()
                        .reduce( function (a, b) {
                            a = a.toString().replace("$", "").replace("(USD)", "");
                            b = b.toString().replace("$", "").replace("(USD)", "");
                        
                            return intVal(a) + intVal(b);
                        }, 0 );
                    totalTax = api
                        .column( 5, { page: "current"} )
                        .data()
                        .reduce( function (a, b) {
                            a = a.toString().replace("$", "").replace("(USD)", "");
                            b = b.toString().replace("$", "").replace("(USD)", "");
                        
                            return intVal(a) + intVal(b);
                        }, 0 );
                        totalMarkup = api
                        .column( 6, { page: "current"} )
                        .data()
                        .reduce( function (a, b) {
                            a = a.toString().replace("$", "").replace("(USD)", "");
                            b = b.toString().replace("$", "").replace("(USD)", "");
                        
                            return intVal(a) + intVal(b);
                        }, 0 );

                    $("#payments-table").append(
                        $("<tfoot/>").append("<tr><td></td><td><b>Grand Total</b></td><td></td><td>$"+totalAmount.toFixed(2)+" (USD)</td><td>$"+totalTax.toFixed(2)+" (USD)</td><td>$"+totalMarkup.toFixed(2)+" (USD)</td><td></td><td></td><td></td></tr>")
                    );
                    
                }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false ],
            __('app.project')  => ['data' => 'project_id', 'name' => 'project_id'],
            __('app.invoice'). '#' => ['data' => 'invoice_number', 'name' => 'invoice.invoice_number'],
            __('modules.invoices.amount') => ['data' => 'amount', 'name' => 'amount'],
            'tax' => ['data' => 'total_tax', 'name' => 'total_tax'],
           'markup' => ['data' => 'markup_amount', 'name' => 'markup_amount'],
            __('modules.payments.paidOn') => ['data' => 'paid_on', 'name' => 'paid_on'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
            __('app.remark') => ['data' => 'remarks', 'name' => 'remarks']

        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Payments_' . date('YmdHis');
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
