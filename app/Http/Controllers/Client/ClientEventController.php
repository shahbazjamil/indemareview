<?php

namespace App\Http\Controllers\Client;

use App\Event;
use Carbon\Carbon;

class ClientEventController extends ClientBaseController
{
    public function __construct() {
        parent::__construct();
        // aqeel changed $this->pageTitle = 'app.menu.Events';
        $this->pageTitle = 'Schedules';
        $this->pageIcon = 'icon-calender';
        $this->middleware(function ($request, $next) {
            if(!in_array('events',$this->user->modules)){
                abort(403);
            }
            return $next($request);
        });

    }

    public function index(){
        $this->events = Event::join('event_attendees', 'event_attendees.event_id', '=', 'events.id')
            ->where('event_attendees.user_id', $this->user->id)
            ->select('events.*')
            ->get();

        return view('client.event-calendar.index', $this->data);
    }

    public function show($id){
        $this->event = Event::findOrFail($id);
        $this->start = Carbon::createFromFormat('Y-m-d H:i:s', request()->start);
        $this->end = Carbon::createFromFormat('Y-m-d H:i:s', request()->end);

        return view('client.event-calendar.show', $this->data);
    }
}
