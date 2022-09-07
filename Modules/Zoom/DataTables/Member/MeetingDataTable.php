<?php

namespace Modules\Zoom\DataTables\Member;

use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Modules\Zoom\Entities\ZoomMeeting;
use Modules\Zoom\Entities\ZoomSetting;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class MeetingDataTable extends BaseDataTable
{

    public function __construct()
    {
        parent::__construct();
        $this->zoomSetting = ZoomSetting::first();
    }

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
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                if ($this->zoomSetting->meeting_app == 'in_app') {
                    $url = route('member.zoom-meeting.startMeeting', $row->id);
                } else {
                    $url = $this->user->id == $row->created_by ? $row->start_link : $row->end_link;
                }
                
                $btnText = ($this->user->id == $row->created_by && $row->status == 'waiting') ? __('zoom::modules.zoommeeting.startUrl') : __('zoom::modules.zoommeeting.joinUrl');

                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle waves-effect waves-light" type="button">
                    <i class="fa fa-gears "></i>
                </button>
                <ul role="menu" class="dropdown-menu">';

                $action.= '<li>
                    <a href="javascript:;" onclick="getEventDetail('.$row->id.')" >
                        <i class="fa fa-eye"></i> '.__('app.view').'
                    </a>
                </li>';

                if ($row->status == 'waiting') {
                    $nowDate = now(company_setting()->timezone)->toDateString();
                    $meetingDate = $row->start_date_time->toDateString();

                    if (is_null($row->occurrence_id) || $nowDate == $meetingDate) {
                        $action.= '<li>
                            <a target="_blank" href="' . $url . '" >
                                <i class="fa fa-play"></i> ' . $btnText . '
                            </a>
                        </li>';
                    }

                    if ($this->user->cans('edit_zoom_meetings')) {
                        $action.= '<li>
                            <a href="javascript:;" class="cancel-meeting" data-meeting-id="'.$row->id.'" >
                                <i class="fa fa-times"></i> '.__('zoom::modules.zoommeeting.cancelMeeting').'
                            </a>
                        </li>';
                        $action.= '<li>
                            <a href="javascript:;" class="btnedit" data-id="' . $row->id . '"  >
                                <i class="fa fa-pencil"></i> ' . __('app.edit') . '
                            </a>
                        </li>';
                    }
                }

                if ($row->status == 'live' && ($this->user->id != $row->created_by)) {
                    $nowDate = now(company_setting()->timezone)->toDateString();
                    $meetingDate = $row->start_date_time->toDateString();

                    if (is_null($row->occurrence_id) || $nowDate == $meetingDate) {
                        $action.= '<li>
                            <a target="_blank" href="' . $url . '" >
                                <i class="fa fa-play"></i> ' . $btnText . '
                            </a>
                        </li>';
                    }
                }

                if ($row->status == 'live' && $this->user->cans('edit_zoom_meetings')) {
                    $action.= '<li>
                        <a href="javascript:;" class="end-meeting" data-meeting-id="'.$row->id.'" >
                            <i class="fa fa-stop"></i> '.__('zoom::modules.zoommeeting.endMeeting').'
                        </a>
                    </li>';
                }

                if ($row->status != 'live' && $this->user->cans('delete_zoom_meetings')) {
                    $action.= '<li>
                        <a href="javascript:;" class="sa-params" data-occurrence="'.$row->occurrence_order.'" data-meeting-id="' . $row->id . '">
                            <i class="fa fa-trash"></i> ' . __('app.delete') . '
                        </a>
                    </li>';
                }

                return $action;
                
            })
            ->editColumn('meeting_id', function ($row)
            {
                $meetingId = $row->meeting_id;

                if (!is_null($row->occurrence_id)) {
                    $meetingId.= '<br><span class="text-muted">' . __('zoom::modules.zoommeeting.occurrence') . ' - ' . $row->occurrence_order . '</span>';
                }
                return $meetingId;
            })
            ->editColumn('meeting_name', function ($row)
            {
                return '<span style="width: 15px; height: 15px;"
                class="btn ' . $row->label_color . ' btn-small btn-circle">&nbsp;</span> ' . ucfirst($row->meeting_name) . '</span>';
            })
            ->editColumn('start_date_time', function ($row)
            {
                return $row->start_date_time->format($this->global->date_format . ' ' . $this->global->time_format);
            })
            ->editColumn('end_date_time', function ($row)
            {
                return $row->end_date_time->format($this->global->date_format . ' ' . $this->global->time_format);
            })
            ->editColumn('status', function ($row) {

                if ($row->status == 'waiting') {
                    $status = '<label class="label label-warning">' . __('zoom::modules.zoommeeting.waiting') . '</label>';
                } else if ($row->status == 'live') {
                    $status = '<i class="fa fa-circle Blink" style="color: red"></i> <span class="font-semi-bold">' . __('zoom::modules.zoommeeting.live') .'</span>';
                } else if ($row->status == 'canceled') {
                    $status = '<label class="label label-danger">' . __('app.canceled') . '</label>';
                } else if ($row->status == 'finished') {
                    $status = '<label class="label label-success">' . __('app.finished') . '</label>';
                }
                return $status;
            })
            ->rawColumns(['action', 'status', 'meeting_name', 'meeting_id']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ZoomMeeting $model)
    {
        $request = $this->request();
        $model = $model->select('id', 'meeting_id', 'created_by', 'meeting_name', 'start_date_time', 'end_date_time', 'start_link', 'join_link', 'status', 'label_color', 'occurrence_id', 'source_meeting_id', 'occurrence_order')
        ->whereHas('attendees', function ($query) {
            if (!$this->user->cans('view_zoom_meetings')) {
                return $query->where('user_id', $this->user->id);
            }
        });
        if (request()->has('startDate') && $request->startDate != 0) {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $model->whereDate('start_date_time', '>=', $startDate);
        }
        
        if (request()->has('endDate') && $request->endDate != 0) {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model->whereDate('end_date_time', '<=', $endDate);
        }
        
        if (request()->has('status') && $request->status != 'all') {
            if ($request->status == 'not finished') {
                $model->where('status', '<>', 'finished');
            } else {
                $model->where('status', $request->status);
            }
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
            ->setTableId('meeting-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->orderBy(0)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->processing(true)
            ->language(__("app.datatable"))
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            )
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["meeting-table"].buttons().container()
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
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            __('zoom::modules.meetings.meetingId') => ['data' => 'meeting_id', 'name' => 'meeting_id' ],
            __('zoom::modules.meetings.meetingName') => ['data' => 'meeting_name', 'name' => 'meeting_name' ],
            __('zoom::modules.meetings.startOn')  => ['data' => 'start_date_time', 'name' => 'start_date_time'],
            __('zoom::modules.meetings.endOn')  => ['data' => 'end_date_time', 'name' => 'end_date_time'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
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
        return 'Tickets_' . date('YmdHis');
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
