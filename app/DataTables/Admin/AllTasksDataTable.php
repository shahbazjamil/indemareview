<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Task;
use App\TaskboardColumn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\ProjectTimeLog;

class AllTasksDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $taskBoardColumns = TaskboardColumn::orderBy('column_name', 'asc')->get();

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<a href="' . route('admin.all-tasks.edit', $row->id) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                $recurringTaskCount = Task::where('recurring_task_id', $row->id)->count();
                $recurringTask = $recurringTaskCount > 0 ? 'yes' : 'no';

                $action .= '&nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-task-id="' . $row->id . '" data-recurring="' . $recurringTask . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';

                return $action;
            })
            ->editColumn('due_date', function ($row) {
                if ($row->due_date->endOfDay()->isPast()) {
                    return '<span class="text-danger">' . $row->due_date->format($this->global->date_format) . '</span>';
                }
                return '<span class="text-success">' . $row->due_date->format($this->global->date_format) . '</span>';
            })
            ->editColumn('users', function ($row) {
                $members = '';
                foreach ($row->users as $member) {
                    $members.= '<a href="' . route('admin.employees.show', [$member->id]) . '">';
                    if($member->image) {
                        $members .= '<img data-toggle="tooltip" data-original-title="' . ucwords($member->name) . '" src="' . $member->image_url . '"
                    alt="user" class="img-circle" width="25" height="25"> ';
                    } else {
                        $members .= '<span data-toggle="tooltip" data-original-title="' . ucwords($member->name) . '" class="nameletter">'.company_initials().'</span>';
                    }
                    
                    $members.= '</a>';
                }
                return $members;
            })
            ->addColumn('name', function ($row) {
                $members = [];
                foreach ($row->users as $member) {
                    $members[] = $member->name;
                }
                return implode(',', $members);
            })
            ->editColumn('clientName', function ($row) {
                return ($row->clientName) ? ucwords($row->clientName) : '-';
            })
            ->editColumn('created_by', function ($row) {
                $nameletter = '<span class="nameletter">'.company_initials().'</span>';
                if (!is_null($row->created_by)) {
                    
                    if($row->created_image) {
                         return '<img src="' . asset_url('avatar/' . $row->created_image) . '"alt="user" class="img-circle" width="30" height="30"> ' . ucwords($row->created_by);
                     } else {
                         return $nameletter.' '. ucwords($row->created_by);
                     }
                }
                return '-';
            })
            ->editColumn('heading', function ($row) {
                $pin = '';
                
                $cls ='';
                $is_completed = false;
                if(isset($row->board_column_id) && $row->board_column && $row->board_column->slug == 'completed') {
                    $is_completed = true;
                    $cls = 'cutting-line';
                }
                
                $name = '<input data-task-id="' . $row->id . '" class="form-control update-task-detail" id="task_id_'.$row->id.'" name="task['.$row->id.']" value="1" type="checkbox">';
                
                if($is_completed) {
                    $name = '<input data-task-id="' . $row->id . '" class="form-control update-task-detail" id="task_id_'.$row->id.'" name="task['.$row->id.']" checked="" value="0" type="checkbox">';
                }
                
                
                 if(($row->pinned_task) ){
                    $pin = '<br><span class="font-12"  data-toggle="tooltip" data-original-title="'.__('app.pinned').'"><i class="icon-pin icon-2"></i></span>';
                }
                
                $name .= '<a href="javascript:;" data-task-id="' . $row->id . '" class="show-task-detail '.$cls.'">' . ucfirst($row->heading) . '</a>'.$pin;
                
                if ($row->is_private) {
                    $name.= ' <i data-toggle="tooltip" data-original-title="' . __('app.private') . '" class="fa fa-lock" style="color: #ea4c89"></i>';
                }
                return $name;
            })
            ->editColumn('column_name', function ($row) use ($taskBoardColumns) {
                $status = '<div class="btn-group dropdown">';
                $status .= '<button aria-expanded="true" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light btn-xs"  style="border-color: ' . $row->label_color . '; color: ' . $row->label_color . '" type="button">' . $row->column_name . ' <span class="caret"></span></button>';
                $status .= '<ul role="menu" class="dropdown-menu pull-right">';
                foreach ($taskBoardColumns as $key => $value) {
                    $status .= '<li><a href="javascript:;" data-task-id="' . $row->id . '" class="change-status" data-status="' . $value->slug . '">' . $value->column_name . '  <span style="width: 15px; height: 15px; border-color: ' . $value->label_color . '; background: ' . $value->label_color . '"
                    class="btn btn-warning btn-small btn-circle">&nbsp;</span></a></li>';
                }
                $status .= '</ul>';
                $status .= '</div>';
                return $status;
            })
            ->addColumn('total_time', function ($row) {
                
                $is_completed = false;
                if(isset($row->board_column_id) && $row->board_column && $row->board_column->slug == 'completed') {
                    $is_completed = true;
                }
                
                $timeLog = ProjectTimeLog::with('task', 'project')->join('users', 'users.id', '=', 'project_time_logs.user_id')
                ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
                ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
                ->leftJoin('projects', 'projects.id', '=', 'project_time_logs.project_id')
                ->where('tasks.id', '=', $row->id);
                //'employee_details.hourly_rate'

                $timeLog = $timeLog->select('project_time_logs.id', 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.total_hours', 'project_time_logs.total_minutes', 'project_time_logs.memo', 'project_time_logs.user_id', 'project_time_logs.project_id', 'project_time_logs.task_id', 'users.name', 'project_time_logs.earnings', 'project_time_logs.approved');
                $timeLog = $timeLog->get();
                
                $total_minutes = 0;
                if($timeLog) {
                    foreach ($timeLog as $tl) {
                        $total_minutes = $total_minutes + $tl->total_minutes;

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
                
                $total_time = $hours.':'.$mins.':'.$secs;
                
                $project_id = $row->project_id ? $row->project_id : '';
                
                $activeTimer = ProjectTimeLog::taskActiveTimer($row->id);
                if($is_completed) {
                    $total_time = '<span class="task-timer" >'.$total_time.'</span>';
                } else {
                    if($activeTimer) {
                        $total_time = '<span class="task-timer" id="active-timer-task">'.$activeTimer->timer.'</span><a href="javascript:;" class="task-timer-stop-click"  data-task-id="' . $row->id . '" data-timelog-id="' . $activeTimer->id . '" ><span class="task-timer-stop-icon" ></span></a>';
                    } else {
                        $total_time = '<span class="task-timer" >'.$total_time.'</span><a href="javascript:;" class="task-timer-start-click"  data-task-id="' . $row->id . '" data-project-id="' . $project_id . '" ><span class= "task-timer-start-icon" ></span></a>';
                    }
                }
                
                
                
                
               
                
                return $total_time;
                
               
            })
            ->editColumn('project_name', function ($row) {
                if (is_null($row->project_id)) {
                    return "";
                }
                
                $cls ='';
                if(isset($row->board_column_id) && $row->board_column && $row->board_column->slug == 'completed') {
                    $cls = 'cutting-line';
                }
                
                return '<a class = "'.$cls.'" href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project_name) . '</a>';
            })
            
             ->addColumn('tags', function ($row) {
                 $tags = '';
                 if($row->tags) {
                     foreach ($row->tags as $tag) {
                         if($tags == '') {
                             $tags = $tag->tag->tag_name;
                         } else {
                              $tags .= ' ,'.$tag->tag->tag_name;
                         }
                     }
                     
                 }
                return $tags;
            })
            ->rawColumns(['column_name', 'action', 'project_name', 'clientName', 'due_date', 'users', 'created_by', 'heading', 'total_time' ,'tags'])
            ->removeColumn('project_id')
            ->removeColumn('image')
            ->removeColumn('created_image')
            ->removeColumn('label_color');

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Task $model)
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;
        
        $projectId =  $request->projectId;
        $hideCompleted = $request->hideCompleted;
        $taskBoardColumn = TaskboardColumn::where('slug', 'completed')->first();

        $model = $model->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('users as client', 'client.id', '=', 'projects.client_id')
            ->join('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->join('task_users', 'task_users.task_id', '=', 'tasks.id')
            ->join('users as member', 'task_users.user_id', '=', 'member.id')
            ->leftJoin('users as creator_user', 'creator_user.id', '=', 'tasks.created_by')
            //->select('tasks.id', 'projects.project_name', 'tasks.heading', 'client.name as clientName', 'creator_user.name as created_by', 'creator_user.image as created_image', 'tasks.due_date', 'taskboard_columns.column_name', 'taskboard_columns.label_color', 'tasks.project_id', 'tasks.is_private')
            //->select('tasks.id', 'projects.project_name', 'tasks.heading', 'client.name as clientName', 'creator_user.name as created_by', 'creator_user.image as created_image', 'tasks.due_date', 'taskboard_columns.column_name', 'taskboard_columns.label_color', 'tasks.project_id', 'tasks.is_private' ,'( select count("id") from pinned where pinned.task_id = tasks.id and pinned.user_id = '.user()->id.') as pinned_task')
            ->selectRaw('tasks.id, projects.project_name, tasks.heading, tasks.board_column_id, client.name as clientName, creator_user.name as created_by, creator_user.image as created_image, tasks.due_date, taskboard_columns.column_name, taskboard_columns.label_color, tasks.project_id, tasks.is_private ,( select count("id") from pinned where pinned.task_id = tasks.id and pinned.user_id = '.user()->id.') as pinned_task') 
            ->whereNull('projects.deleted_at')
            ->with('users')
            ->orderBy('pinned_task', 'DESC')
            ->groupBy('tasks.id');
            
        if (($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') && $request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(tasks.`due_date`)'), [$startDate, $endDate]);
                $q->orWhereBetween(DB::raw('DATE(tasks.`start_date`)'), [$startDate, $endDate]);
            });
        } else {
            
//            $date = Carbon::now();
//            $startDate = $date->subDays(30)->format($this->global->date_format);
//            $endDate = Carbon::now()->addDays(30)->format($this->global->date_format);
//            $startDateFilter = Carbon::createFromFormat($this->global->date_format, $startDate)->format('Y-m-d');
//            $endDateFilter = Carbon::createFromFormat($this->global->date_format, $endDate)->format('Y-m-d');
//            
//            $model->where(function ($q) use ($startDateFilter, $endDateFilter) {
//                $q->whereBetween(DB::raw('DATE(tasks.`due_date`)'), [$startDateFilter, $endDateFilter]);
//                $q->orWhereBetween(DB::raw('DATE(tasks.`start_date`)'), [$startDateFilter, $endDateFilter]);
//            });
        }
        if ($projectId != 0 && $projectId !=  null && $projectId !=  'all') {
            $model->where('tasks.project_id', '=', $projectId);
        }
        if ($request->clientID != '' && $request->clientID !=  null && $request->clientID !=  'all') {
            $model->where('projects.client_id', '=', $request->clientID);
        }
        if ($request->assignedTo != '' && $request->assignedTo !=  null && $request->assignedTo !=  'all') {
            $model->where('task_users.user_id', '=', $request->assignedTo);
        }

        if ($request->assignedBY != '' && $request->assignedBY !=  null && $request->assignedBY !=  'all') {
            $model->where('creator_user.id', '=', $request->assignedBY);
        }
        if ($request->status != '' && $request->status !=  null && $request->status !=  'all') {
            $model->where('tasks.board_column_id', '=', $request->status);
        }
        if ($hideCompleted == '1') {
            $model->where('tasks.board_column_id', '<>', $taskBoardColumn->id);
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
            ->setTableId('allTasks-table')
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
                   window.LaravelDataTables["allTasks-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                    updateTimer();
                    //setTimeout(updateTimer, 1000);
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
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false ],
            __('app.task') => ['data' => 'heading', 'name' => 'heading'],
            __('app.project')  => ['data' => 'project_name', 'name' => 'projects.project_name'],
            __('modules.tasks.assigned') => ['data' => 'name', 'name' => 'name', 'visible' => false],
            __('modules.tasks.assignTo') => ['data' => 'users', 'name' => 'member.name', 'exportable' => false],
            'Tags' => ['data' => 'tags', 'name' => 'tags'],
            __('app.dueDate') => ['data' => 'due_date', 'name' => 'due_date'],
            'total Time' => ['data' => 'total_time', 'name' => 'total_time'],
            
            __('app.status') => ['data' => 'column_name', 'name' => 'taskboard_columns.column_name'],
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
        return 'All_Task_' . date('YmdHis');
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
