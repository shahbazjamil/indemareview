<?php

namespace Modules\Zoom\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\Member\MemberBaseController;
use App\Http\Controllers\Admin\AdminBaseController;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MacsiDigital\Zoom\Facades\Zoom;
use Modules\Zoom\Entities\ZoomMeeting;
use Modules\Zoom\DataTables\Member\MeetingDataTable;
use Modules\Zoom\Entities\ZoomSetting;
use Modules\Zoom\Events\MeetingInviteEvent;
use Modules\Zoom\Http\Requests\ZoomMeeting\StoreMeeting;
use Modules\Zoom\Http\Requests\ZoomMeeting\UpdateMeeting;
use Modules\Zoom\Http\Requests\ZoomMeeting\UpdateOccurrence;
use Yajra\DataTables\Facades\DataTables;


class EmployeeZoomMeetingController extends MemberBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('zoom::app.menu.zoomMeeting');
        $this->pageIcon = 'fa fa-video-camera';

        $this->middleware(function ($request, $next) {
            ZoomSetting::setZoom();
            if (!in_array('Zoom', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(MeetingDataTable $dataTable)
    {
        if ($this->user->cans('add_zoom_meetings')) {
            $this->employees = User::allEmployees();
        }

        return $dataTable->render('zoom::employee-meeting.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('zoom::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(StoreMeeting $request)
    {
        if (!$this->user->cans('add_zoom_meetings')) {
            abort(403);
        }
        $this->createOrUpdateMeetings($request);
        return Reply::success(__('messages.meetingCreateSuccess'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->event = ZoomMeeting::with('attendees')->findOrFail($id);
        $this->zoomSetting = ZoomSetting::first();

        return view('zoom::meeting-calendar.show', $this->data);
    }


    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->event = ZoomMeeting::with('attendees')->findOrFail($id);
        $this->employees = User::allEmployees();
        $this->clients = User::allClients();

        if (!is_null($this->event->occurrence_id)) {
            return view('zoom::meeting-calendar.employee.edit_occurrence', $this->data);
        }

        return view('zoom::meeting-calendar.employee.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateMeeting $request, $id)
    {
        if (!$this->user->cans('edit_zoom_meetings')) {
            abort(403);
        }
        $this->createOrUpdateMeetings($request, $id);

        return Reply::success(__('messages.meetingUpdateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $meeting = ZoomMeeting::findOrFail($id);

        // destroy meeting via zoom api
        if (!is_null($meeting->occurrence_id)) {
            $zoomMeeting =  Zoom::meeting()->find($meeting->meeting_id);

            if (request()->has('recurring') && request('recurring') == "yes") {
                //delete all occurrences
                $occurrence = $zoomMeeting->occurrences()->delete();
            } else {
                //delete single occurrence
                $occurrence = $zoomMeeting->occurrences()->find($meeting->occurrence_id);
                $occurrence->delete();
            }
        } else {
            $zoomMeeting = Zoom::user()->find('me')->meetings()->find($meeting->meeting_id);
            if ($zoomMeeting) {
                $zoomMeeting->delete();
            }
        }

        $meeting->attendees()->detach();
        $meeting->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function createMeeting($user, ZoomMeeting $meeting, $id, $meetingId = null)
    {
        // create meeting using zoom API
        $commonSettings = [
            'type' => 2,
            'topic' => $meeting->meeting_name,
            'start_time' => $meeting->start_date_time,
            'duration' => $meeting->end_date_time->diffInMinutes($meeting->start_date_time),
            'timezone' => $this->global->timezone,
            'agenda' => $meeting->description,
            'settings' => [
                'host_video' => $meeting->host_video == 1,
                'participant_video' => $meeting->participant_video == 1,
            ]
        ];

        if (is_null($id)) {
            $zoomMeeting = $user->meetings()->make($commonSettings);
            $savedMeeting = $user->meetings()->save($zoomMeeting);

            $meeting->meeting_id = $savedMeeting->id;
            $meeting->start_link = $savedMeeting->start_url;
            $meeting->join_link = $savedMeeting->join_url;
            $meeting->password = $savedMeeting->password;

            $meeting->save();
        } else {
            $user->meetings()->find($meeting->meeting_id)->update($commonSettings);
        }

        return $meeting;
    }

    public function createOrUpdateMeetings($request, $id = null)
    {
        $user = Zoom::user()->find('me');

        if ($request->has('repeat') && $request->repeat) {
            $this->createRepeatMeeting($user, $request, $id);
        } else {
            $meeting = is_null($id) ? new ZoomMeeting() : ZoomMeeting::find($id);
            $data = $request->all();
            $data['meeting_name'] = $request->meeting_title;
            $data['start_date_time'] = $request->start_date . 'T' . $request->start_time;
            $data['end_date_time'] = $request->end_date . 'T' . $request->end_time;

            if (is_null($id)) {
                $meeting = $meeting->create($data);
                $this->syncAttendees($request, $meeting, 'yes');
            } else {
                $meeting->update($data);
                $this->syncAttendees($request, $meeting);
            }

            $this->createMeeting($user, $meeting, $id);
        }
    }

    public function syncAttendees($request, $meeting, $sendInvitation = null)
    {
        $attendees = [];
        if ($request->all_employees) {
            $attendees = User::allEmployees();
        } else {
            $attendees = User::whereIn('id', $request->employee_id)->get();
        }
        if ($request->all_clients) {
            $attendees = User::allClients()->merge($attendees);
        } elseif ($request->has('client_id')) {
            $attendees = User::whereIn('id', $request->client_id)->get()->merge($attendees);
        }
        $meeting->attendees()->sync($attendees);

        if ($sendInvitation === 'yes') {
            event(new MeetingInviteEvent($meeting, $attendees));
        }
    }

    /**
     * cancel meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelMeeting()
    {
        if (!$this->user->cans('edit_zoom_meetings')) {
            abort(403);
        }

        $id = request('id');
        ZoomMeeting::where('id', $id)->update([
            'status' => 'canceled'
        ]);

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * end meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function endMeeting()
    {
        if (!$this->user->cans('edit_zoom_meetings')) {
            abort(403);
        }

        $id = request('id');
        $meeting = ZoomMeeting::findOrFail($id);

        $zoomMeeting = Zoom::meeting()->find($meeting->meeting_id);
        if ($zoomMeeting) {
            $zoomMeeting->endMeeting();
        }

        $meeting->status = 'finished';
        $meeting->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * create repeated meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function createRepeatMeeting($user, $request, $id)
    {
        //create first record in db
        $meeting = new ZoomMeeting();
        $data = $request->all();
        $data['meeting_name'] = $request->meeting_title;
        $data['start_date_time'] = $request->start_date . 'T' . $request->start_time;
        $data['end_date_time'] = $request->end_date . 'T' . $request->end_time;
        $meeting = $meeting->create($data);
        $meeting->source_meeting_id = $meeting->id;
        $meeting->occurrence_order = 1;
        $meeting->save();
        $meetingId = $meeting->id;

        $this->syncAttendees($request, $meeting, 'yes');

        //create other records with reference to first
        $repeatCount = $request->repeat_every;
        $repeatType = $request->repeat_type;
        $repeatCycles = $request->repeat_cycles;
        $startDate = Carbon::createFromFormat($this->global->date_format, $request->start_date);
        $dueDate = Carbon::createFromFormat($this->global->date_format, $request->end_date);

        for ($i = 1; $i < $repeatCycles; $i++) {
            $startDate = $startDate->add($repeatCount, str_plural($repeatType));
            $dueDate = $dueDate->add($repeatCount, str_plural($repeatType));

            $otherMeeting = new ZoomMeeting();
            $data['start_date_time'] = $startDate->format($this->global->date_format) . 'T' . $request->start_time;
            $data['end_date_time'] = $dueDate->format($this->global->date_format) . 'T' . $request->end_time;
            $data['source_meeting_id'] = $meetingId;
            $data['occurrence_order'] = $i + 1;
            $otherMeeting = $otherMeeting->create($data);

            $this->syncAttendees($request, $otherMeeting);
        }

        //create meeting on zoom
        $startDate = Carbon::createFromFormat($this->global->date_format . ' ' . $this->global->time_format, $request->start_date . ' ' . $request->start_time);

        $zoomMeeting = Zoom::meeting()->make([
            'topic' => $request->meeting_title,
            'type' => 8,
            'start_time' => $startDate, // best to use a Carbon instance here.
            'agenda' => $request->description,
            'settings' => [
                'host_video' => $request->host_video == 1,
                'participant_video' => $request->participant_video == 1,
            ]
        ]);

        $repeatInterval = $request->repeat_every;
        $repeatCycles = $request->repeat_cycles;

        if ($request->repeat_type == "day") {
            $repeatType = 1;
        } elseif ($request->repeat_type == "week") {
            $repeatType = 2;
        } else {
            $repeatType = 3;
        }

        $repeatData = [
            'type' => $repeatType,
            'repeat_interval' => intval($repeatInterval),
            'end_times' => intval($repeatCycles)
        ];

        if ($repeatType == 2) {
            $repeatData['weekly_days'] = $startDate->dayOfWeek + 1;
        }

        $zoomMeeting->recurrence()->make($repeatData);
        $savedMeeting = $user->meetings()->save($zoomMeeting);


        //save zoom response data
        $meeting->meeting_id = $savedMeeting->id;
        $meeting->start_link = $savedMeeting->start_url;
        $meeting->join_link = $savedMeeting->join_url;
        $meeting->password = $savedMeeting->password;
        $meeting->save();

        $repeatCycles = $request->repeat_cycles;
        $meetingId = $meeting->id;

        for ($i = 1; $i < $repeatCycles; $i++) {
            ZoomMeeting::where('source_meeting_id', $meetingId)->update(
                [
                    'meeting_id' => $savedMeeting->id,
                    'start_link' => $savedMeeting->start_url,
                    'join_link' => $savedMeeting->join_url,
                    'password' => $savedMeeting->password,
                ]
            );
        }
    }

    /**
     * update meeting occurrence
     *
     * @return \Illuminate\Http\Response
     */
    public function updateOccurrence(UpdateOccurrence $request, $id)
    {
        $zoomMeeting = ZoomMeeting::find($id);
        $data = $request->all();
        $data['start_date_time'] = $request->start_date . 'T' . $request->start_time;
        $data['end_date_time'] = $request->end_date . 'T' . $request->end_time;
        $zoomMeeting->update($data);

        $meeting =  Zoom::meeting()->find($zoomMeeting->meeting_id);
        $occurrence = $meeting->occurrences()->find($zoomMeeting->occurrence_id);
        $occurrence->start_time = $zoomMeeting->start_date_time;
        $occurrence->save();
        return Reply::success(__('messages.meetingUpdateSuccess'));
    }
    /**
     * start zoom meeting in app
     *
     * @return \Illuminate\Http\Response
     */
    public function startMeeting($id)
    {
        $this->zoomSetting = ZoomSetting::first();
        $this->meeting = ZoomMeeting::findOrFail($id);
        $this->zoomMeeting = Zoom::meeting()->find($this->meeting->meeting_id);
        return view('zoom::meeting-calendar.start_meeting', $this->data);
    }
}
