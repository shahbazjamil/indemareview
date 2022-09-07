<?php

namespace Modules\Zoom\Http\Controllers;

use App\Http\Controllers\Client\ClientBaseController;
use App\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use MacsiDigital\Zoom\Facades\Zoom;
use Modules\Zoom\Entities\ZoomMeeting;
use Modules\Zoom\DataTables\Client\MeetingDataTable;
use Modules\Zoom\Entities\ZoomSetting;
use Yajra\DataTables\Facades\DataTables;


class ClientZoomMeetingController extends ClientBaseController
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
        return $dataTable->render('zoom::client-meeting.index', $this->data);
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
    public function store(Request $request)
    {
        //
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
        return view('zoom::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
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
