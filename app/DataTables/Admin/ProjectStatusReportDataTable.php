<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Project;
use App\User;
use Yajra\DataTables\Html\Button;

class ProjectStatusReportDataTable extends BaseDataTable
{

    /**
     * @param $query
     * @return \Yajra\DataTables\CollectionDataTable|\Yajra\DataTables\DataTableAbstract
     */
//    public function dataTable($query)
//    {
//        return datatables()
//            ->collection($this->query($query))
//            ->addIndexColumn();
//
//    }

    public function dataTable($query)
    {

        //select('id', 'project_name', 'start_date', 'deadline', 'status','completion_percent','client_id', 'created_at');

        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn " type="button"><i class="ti-more"></i> </button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('admin.projects.edit', [$row->id]) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                  <li><a href="' . route('admin.projects.show', [$row->id]) . '"><i class="fa fa-search" aria-hidden="true"></i> ' . trans('app.view') . ' ' . trans('app.details') . '</a></li>
                  <li><a href="' . route('admin.projects.gantt', [$row->id]) . '"><i class="fa fa-bar-chart" aria-hidden="true"></i> ' . trans('modules.projects.viewGanttChart') . '</a></li>
                  <li><a href="' . route('front.gantt', [md5($row->id)]) . '" target="_blank"><i class="fa fa-line-chart" aria-hidden="true"></i> ' . trans('modules.projects.viewPublicGanttChart') . '</a></li>
                  <li><a href="javascript:;" data-user-id="' . $row->id . '" class="archive"><i class="fa fa-archive" aria-hidden="true"></i> ' . trans('app.archive') . '</a></li>
                  <li><a href="javascript:;" data-user-id="' . $row->id . '" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>';

                $action .= '</ul> </div>';

                return $action;
            })
            ->addColumn('project_name', function ($row) {
                return $row->project_name;
            })
            ->addColumn('start_date', function ($row) {
                return $row->start_date->format($this->global->date_format);
            })
            ->addColumn('deadline', function ($row) {
                if ($row->deadline) {
                    return $row->deadline->format($this->global->date_format);
                }
            })
            ->addColumn('status', function ($row) {
                return $row->status;
            })
            ->addColumn('completion_percent', function ($row) {
                return $row->completion_percent;
            })
            ->addColumn('client_name', function ($row) {
                return $row->client->name;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format($this->global->date_format);
            })
            ->addIndexColumn()
            ->rawColumns(['project_name', 'start_date', 'deadline', 'status', 'completion_percent', 'client_id', 'created_at']);
//            ->removeColumn('project_summary')
//            ->removeColumn('notes')
//            ->removeColumn('category_id')
//            ->removeColumn('feedback')
//            ->removeColumn('start_date');
    }

    /**
     * @param User $model
     * @return \Illuminate\Support\Collection
     */
    public function query(Project $model)
    {
//        set_time_limit(0);
//        $request = $this->request();
//        $allProjects = $this->projects = Project::where('company_id', auth()->user()->company_id);
//        if ($request->projects != 'all') {
//            $allProjects = Project::where('id', $request->project_id)->get();
//        }
//
//        $this->startDate = $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate);
//        $this->endDate = $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate);
//        $period = CarbonPeriod::create($this->startDate, $this->endDate);
//
//
//        $summaryData = array();
//
//        foreach ($allProjects as $key => $project) {
//
//            $summaryData[$key]['project_id'] = $project->id;
//            $summaryData[$key]['name'] = $project->name;
//
//
//        }
//
//        return collect($summaryData);

        $model = $model->with(['client'])->select('id', 'project_name', 'start_date', 'deadline', 'status', 'completion_percent', 'client_id', 'created_at');
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
            ->setTableId('projectstatus-report-table')
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
                   window.LaravelDataTables["projectstatus-report-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
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
            ' #' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            __('Project Name') => ['data' => 'project_name', 'name' => 'project_name'],
            __('Project Start Date') => ['data' => 'start_date', 'name' => 'start_date'],
            __('Project Deadline') => ['data' => 'deadline', 'name' => 'deadline'],
            __('Project Status') => ['data' => 'status', 'name' => 'status'],
            __('Completion Percent') => ['data' => 'completion_percent', 'name' => 'completion_percent'],
            __('Client') => ['data' => 'client_name', 'name' => 'client_name'],
            __('Date') => ['data' => 'created_at', 'name' => 'created_at'],
//            Column::computed('action', __('app.action'))
//                ->exportable(false)
//                ->printable(false)
//                ->orderable(false)
//                ->searchable(false)
//                ->width(150)
//                ->addClass('text-center')

        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Attendance_report_' . date('YmdHis');
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
