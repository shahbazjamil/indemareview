<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\LogTimeFor;
use App\ProjectTimeLog;
use App\ProjectMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class AllTimeLogsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    protected $timeLogFor;
    protected $isTask;
    public function __construct()
    {
        parent::__construct();
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->filterColumn('project_name', function($query, $keyword) {
                $sql = "projects.project_name  like ? or tasks.heading like ?";
                $query->whereRaw($sql, ["%{$keyword}%","%{$keyword}%"]);
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu pull-right">
                <li><a href="javascript:;" class="edit-time-log"
                data-toggle="tooltip" data-time-id="' . $row->id . '"  data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                <li> <a href="javascript:;" class="sa-params"
                data-toggle="tooltip" data-time-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>';

                if (!$row->approved) {
                    $action.= '<li> <a href="javascript:;" class="approve-timelog"
                    data-time-id="' . $row->id . '"><i class="fa fa-check" aria-hidden="true"></i> ' . trans('app.approve') . '</a></li>';
                }

                $action .= '</ul> </div>';
                return $action;
            })
            ->editColumn('name', function ($row) {
                
                $nameletter = '<span class="nameletter">'.company_initials().'</span>';
                    return  '<div class="row truncate"><div class="col-sm-3 col-xs-4">' . $nameletter . '</div><div class="col-sm-9 col-xs-8"><a href="' . route('admin.employees.show', $row->user_id) . '">' . ucwords($row->name) . '</a></div></div>';
                
                //return '<a href="' . route('admin.employees.show', $row->user_id) . '" target="_blank" >' . ucwords($row->name) . '</a>';
            })
            ->editColumn('start_time', function ($row) {
                return $row->start_time->timezone($this->global->timezone)->format($this->global->date_format . ' ' . $this->global->time_format);
            })
            ->editColumn('end_time', function ($row) {
                if (!is_null($row->end_time)) {
                    return $row->end_time->timezone($this->global->timezone)->format($this->global->date_format . ' ' . $this->global->time_format);
                } else {
                    return "<label class='label label-success'>" . __('app.active') . "</label>";
                }
            })
            ->editColumn('total_hours', function ($row) {
                $timeLog = intdiv($row->total_minutes, 60) . ' hrs ';

                if (($row->total_minutes % 60) > 0) {
                    $timeLog .= ($row->total_minutes % 60) . ' mins';
                }

                if ($row->approved) {
                    $timeLog.= ' <i data-toggle="tooltip" data-original-title="' . __('app.approved') . '" class="fa fa-check-circle text-success"></i>';
                } else {
                    $timeLog.= ' <i data-toggle="tooltip" data-original-title="' . __('app.pending') . '" class="fa fa-check-circle text-muted" ></i>';
                }

                return $timeLog;
            })
            ->addColumn('total_minutes', function ($row) {
               return  $row->total_minutes ? $row->total_minutes : 0;
            })
            
            
            ->addColumn('earnings', function ($row) {
//                if (is_null($row->hourly_rate)) {
//                    return '--';
//                }
                $hourly_rate = $row->hourly_rate ? $row->hourly_rate : '';
                
                //$hours = intdiv($row->total_minutes, 60);
                //$earning = round($hours * $row->hourly_rate);
                if (!is_null($row->project_id) && !is_null($row->user_id)) {
                    $projectMember = ProjectMember::where('user_id', $row->user_id)->where('project_id', $row->project_id)->first();
                    if($projectMember) {
                        $hourly_rate = $projectMember->hourly_rate;
                    }
                }
                
                if ($hourly_rate == '') {
                    return '--';
                }
                
                $hours = intdiv($row->total_minutes, 60);
                $minuteRate = $hourly_rate / 60;
                $earning = round($row->total_minutes * $minuteRate);

                return $this->global->currency->currency_symbol . $earning . ' (' . $this->global->currency->currency_code . ')';
            })
            
            ->editColumn('project_name', function ($row) {
                $name = '';
                if (!is_null($row->project_id) && !is_null($row->task_id)) {
                    if($row->project) {
                        $name.= '<span class="font-semi-bold">' . $row->task->heading . '</span><br><span class="text-muted">' . $row->project->project_name . '</span>';
                    }
                } else if (!is_null($row->project_id)) {
                    if($row->project) {
                        $name.= '<span class="font-semi-bold">' . $row->project->project_name . '</span>';
                    }
                } else if (!is_null($row->task_id)) {
                    $name.= '<span class="font-semi-bold">' . $row->task->heading . '</span>';
                }
                
                return $name;
            })
            ->rawColumns(['end_time', 'action', 'project_name', 'name', 'total_hours'])
            ->removeColumn('project_id')
            //->removeColumn('total_minutes')
            ->removeColumn('task_id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ProjectTimeLog $model)
    {
        $request = $this->request();
        $projectId = $request->projectId;
        $employee = $request->employee;
        $taskId = $request->taskId;
        $approved = $request->approved;

        $model = $model->with('task', 'project')->join('users', 'users.id', '=', 'project_time_logs.user_id')
            ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
            ->leftJoin('projects', 'projects.id', '=', 'project_time_logs.project_id');


        $model = $model->select('project_time_logs.id', 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.total_hours', 'project_time_logs.total_minutes', 'project_time_logs.memo', 'project_time_logs.user_id', 'project_time_logs.project_id', 'project_time_logs.task_id', 'users.name', 'employee_details.hourly_rate', 'project_time_logs.earnings', 'project_time_logs.approved');

        if (!is_null($request->startDate)) {
            //$model->where(DB::raw('DATE(project_time_logs.`start_time`)'), '>=', Carbon::createFromFormat($this->global->date_format, $request->startDate));
            $model->where(DB::raw('DATE(project_time_logs.`start_time`)'), '>=', Carbon::createFromFormat($this->global->date_format, $request->startDate)->format('Y-m-d 00:00:00'));
        }

        if (!is_null($request->endDate)) {
            //$model->where(DB::raw('DATE(project_time_logs.`end_time`)'), '<=', Carbon::createFromFormat($this->global->date_format, $request->endDate));
            $model->where(DB::raw('DATE(project_time_logs.`end_time`)'), '<=', Carbon::createFromFormat($this->global->date_format, $request->endDate)->format('Y-m-d 00:00:00'));
        }

        if (!is_null($request->employee) && $request->employee !== 'all') {
            $model->where('project_time_logs.user_id', $request->employee);
        }

        if (!is_null($projectId) && $projectId !== 'all') {
            $model->where('project_time_logs.project_id', '=', $projectId);
        }

        if (!is_null($taskId) && $taskId !== 'all') {
            $model->where('project_time_logs.task_id', '=', $taskId);
        }

        if (!is_null($approved) && $approved !== 'all') {
            $model->where('project_time_logs.approved', '=', $approved);
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
            ->setTableId('all-time-logs-table')
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
                   window.LaravelDataTables["all-time-logs-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
                'footerCallback' => 'function ( row, data, start, end, display ) {
                    document.getElementById("all-time-logs-table").deleteTFoot()
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === "string" ?
                            i.replace(/[\$,]/g, "")*1 :
                            typeof i === "number" ?
                                i : 0;
                    };
                    
                    totalAmount = api
                        .column( 7, { page: "current"} )
                        .data()
                        .reduce( function (a, b) {
                            a = a.toString().replace("'.$this->global->currency->currency_symbol.'", "").replace("('.$this->global->currency->currency_code.')", "").replace("--", "");
                            b = b.toString().replace("'.$this->global->currency->currency_symbol.'", "").replace("('.$this->global->currency->currency_code.')", "").replace("--", "");
                        
                        
                            return intVal(a) + intVal(b);
                        }, 0 );
                        
                    totalMinutes = api
                        .column( 8, { page: "current"} )
                        .data()
                        .reduce( function (a, b) {
                        
                            return intVal(a) + intVal(b);
                        }, 0 );
                        
                        var timeLog = Math.floor(totalMinutes / 60) + " hrs ";          
                        var minutes = totalMinutes % 60;
                        if(minutes > 0) {
                        timeLog = timeLog + minutes + " mins";
                        }
                        
                  

                    $("#all-time-logs-table").append(
                        $("<tfoot/>").append("<tr> <td></td> <td><b>Grand Total</b></td> <td></td> <td></td> <td></td> <td>"+timeLog+"</td><td>$"+totalAmount.toFixed(2)+" ('.$this->global->currency->currency_code.')</td> <td></td> <td></td> </tr>")
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
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            __('app.task') => ['data' => 'project_name', 'name' => 'project_name'],
            __('app.menu.employees')  => ['data' => 'name', 'name' => 'users.name'],
            __('modules.timeLogs.startTime') => ['data' => 'start_time', 'name' => 'start_time'],
            __('modules.timeLogs.endTime') => ['data' => 'end_time', 'name' => 'end_time'],
            __('modules.timeLogs.totalHours') => ['data' => 'total_hours', 'name' => 'total_hours'],
            __('app.earnings') => ['data' => 'earnings', 'name' => 'earnings'],
            'Total Minutes' => ['data' => 'total_minutes', 'name' => 'total_minutes'],
            //'Total SSSS' => ['data' => 'total_minutes', 'name' => 'total_minutes'],
            
            
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
        return 'All_time_log_' . date('YmdHis');
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
