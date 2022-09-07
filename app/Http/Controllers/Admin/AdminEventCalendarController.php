<?php

namespace App\Http\Controllers\Admin;

use App\Event;
use App\EventAttendee;
use App\Helper\Reply;
use App\Http\Requests\Events\StoreEvent;
use App\Http\Requests\Events\UpdateEvent;
use App\Notifications\EventInvite;
use App\User;
use App\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Scopes\CompanyScope;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_EventAttendee;
use Illuminate\Support\Facades\DB;
#use Google_Service_Plus;

class AdminEventCalendarController extends AdminBaseController
{
    protected $googleclient = '';
    
    public function __construct()
    {
        parent::__construct();
//        $this->pageTitle = 'app.menu.Events';
        $this->pageTitle = 'Schedules';
        $this->pageIcon = 'icon-calender';
        $this->middleware(function ($request, $next) {
            if (!in_array('events', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/events'));
        
        $this->employees = User::all();
        $this->events = Event::all();
        return view('admin.event-calendar.index', $this->data);
    }

    public function store(StoreEvent $request)
    {
        $event = new Event();
        $event->event_name = $request->event_name;
        $event->where = $request->where ?  $request->where : '';
        $event->description = $request->description ? $request->description : '';
        $event->start_date_time = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('H:i:s');
        $event->end_date_time = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('H:i:s');

        $event->start_time = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('h:i A');
        $event->end_time = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('h:i A');
                
        if ($request->repeat) {
            $event->repeat = $request->repeat;
        } else {
            $event->repeat = 'no';
        }

        $event->repeat_every = $request->repeat_count;
        $event->repeat_cycles = $request->repeat_cycles;
        $event->repeat_type = $request->repeat_type;
        $event->label_color = $request->label_color;
        
        $event->save();

        if ($request->all_employees) {
            $attendees = User::allEmployees();
            foreach ($attendees as $attendee) {
                EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);
            }

            Notification::send($attendees, new EventInvite($event));
        }

        if ($request->user_id) {
            foreach ($request->user_id as $userId) {
                EventAttendee::firstOrCreate(['user_id' => $userId, 'event_id' => $event->id]);
            }
            $attendees = User::whereIn('id', $request->user_id)->get();
            Notification::send($attendees, new EventInvite($event));
        }
        
        
        // create an even on Google
        
        $this->googleclient = $this->GoogleSettings();
         if ($this->googleclient) {

            $calendarObj = Calendar::where('user_id', ($this->user->id))->first();

            if ($calendarObj) {
                try {


                    $s_y = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y');
                    $s_mm = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('n');
                    $s_d = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('j');
                    $s_h = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('G');
                    $s_m = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('i');
                    $s_s = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('s');

                    $e_y = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y');
                    $e_mm = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('n');
                    $e_d = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('j');
                    $e_h = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('G');
                    $e_m = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('i');
                    $e_s = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('s');

                    $tz = new \DateTimeZone($this->global->timezone);
                    $startTime = Carbon::create($s_y, $s_mm, $s_d, $s_h, $s_m, $s_s, $tz);
                    $endTime = Carbon::create($e_y, $e_mm, $e_d, $e_h, $e_m, $e_s, $tz);


                    $cal = new Google_Service_Calendar($this->googleclient);

                    $service = new Google_Service_Calendar_Event();
                    $service->setSummary($event->event_name);
                    if(!empty($event->description)) {
                        $service->setDescription($event->description);
                    }
                    

                    $start = new \Google_Service_Calendar_EventDateTime();
                    $start->setTimeZone($tz);
                    $start->setDateTime($startTime->format('c'));
                    $service->setStart($start);

                    $end = new \Google_Service_Calendar_EventDateTime();
                    $end->setTimeZone($tz);
                    $end->setDateTime($endTime->format('c'));
                    $service->setEnd($end);
                    if(!empty($event->where)) {
                        $service->setLocation($event->where);
                    }
                    
                    
                   

                    //attendee
                    if ($request->all_employees) {
                        $attendeeUsers = User::allEmployees();
                        $attendees = [];
                        foreach ($attendeeUsers as $index => $row) {
                            $attendee = new Google_Service_Calendar_EventAttendee();
                            $attendee->setEmail($row->email);
                            $attendee->setDisplayName($row->name);
                            $attendees[] = $attendee;
                        }

                        $service->attendees = $attendees;
                    }
                    
                    if ($request->user_id) {
                        foreach ($request->user_id as $userId) {
                            $row = User::where('id', ($userId))->first();
                            if($row) {
                                $attendee = new Google_Service_Calendar_EventAttendee();
                                $attendee->setEmail($row->email);
                                $attendee->setDisplayName($row->name);
                                $attendees[] = $attendee;
                            }
                        }
                    }

                    //echo $calendarObj->calendar_id;exit;
                    $created_event = $cal->events->insert($calendarObj->calendar_id, $service);
                    // save event ID in DB
                    $event->google_calendar_id = $created_event->id;
                    $event->save();
                } catch (\Exception $e) {
//                    echo 'ERR<pre>';
//                    print_r($e->getMessage());
//                    exit;
                }
            }
        }



        return Reply::success(__('messages.eventCreateSuccess'));
    }

    public function edit($id)
    {
        $this->employees = User::doesntHave('attendee', 'and', function ($query) use ($id) {
            $query->where('event_id', $id);
        })
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->get();
        $this->event = Event::findOrFail($id);
        $view = view('admin.event-calendar.edit', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function update(UpdateEvent $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->event_name = $request->event_name;
        $event->where = $request->where ? $request->where : '';
        $event->description = $request->description ? $request->description : '';
        $event->start_date_time = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('H:i:s');
        $event->end_date_time = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('H:i:s');

        $event->start_time = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('h:i A');
        $event->end_time = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('h:i A');
        
        if ($request->repeat) {
            $event->repeat = $request->repeat;
        } else {
            $event->repeat = 'no';
        }

        $event->repeat_every = $request->repeat_count;
        $event->repeat_cycles = $request->repeat_cycles;
        $event->repeat_type = $request->repeat_type;
        $event->label_color = $request->label_color;
        $event->save();

        if ($request->all_employees) {
            $attendees = User::allEmployees();
            foreach ($attendees as $attendee) {
                $checkExists = EventAttendee::where('user_id', $attendee->id)->where('event_id', $event->id)->first();
                if (!$checkExists) {
                    EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);

                    //      Send notification to user
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($attendee->id);
                    $notifyUser->notify(new EventInvite($event));
                }
            }
        }

        if ($request->user_id) {
            foreach ($request->user_id as $userId) {
                $checkExists = EventAttendee::where('user_id', $userId)->where('event_id', $event->id)->first();
                if (!$checkExists) {
                    EventAttendee::create(['user_id' => $userId, 'event_id' => $event->id]);

                    //      Send notification to user
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($userId);
                    $notifyUser->notify(new EventInvite($event));
                }
            }
        }
        
        
        // update an even on Google
        
        $this->googleclient = $this->GoogleSettings();
         if ($this->googleclient) {
             

            $calendarObj = Calendar::where('user_id', ($this->user->id))->first();

            if (isset($event->google_calendar_id) && !empty($event->google_calendar_id) && $calendarObj) {
                try {

                    $s_y = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y');
                    $s_mm = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('n');
                    $s_d = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('j');
                    $s_h = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('G');
                    $s_m = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('i');
                    $s_s = Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('s');

                    $e_y = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y');
                    $e_mm = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('n');
                    $e_d = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('j');
                    $e_h = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('G');
                    $e_m = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('i');
                    $e_s = Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('s');

                    $tz = new \DateTimeZone($this->global->timezone);
                    $startTime = Carbon::create($s_y, $s_mm, $s_d, $s_h, $s_m, $s_s, $tz);
                    $endTime = Carbon::create($e_y, $e_mm, $e_d, $e_h, $e_m, $e_s, $tz);

                   

                    $cal = new Google_Service_Calendar($this->googleclient);
                    
                     
                    $service = $cal->events->get($calendarObj->calendar_id, $event->google_calendar_id);
                    
                    $service->setSummary($event->event_name);
                    if(!empty($event->description)) {
                        $service->setDescription($event->description);
                    }
                    
                    $start = new \Google_Service_Calendar_EventDateTime();
                    $start->setTimeZone($tz);
                    $start->setDateTime($startTime->format('c'));
                    $service->setStart($start);
                    
                    

                    $end = new \Google_Service_Calendar_EventDateTime();
                    $end->setTimeZone($tz);
                    $end->setDateTime($endTime->format('c'));
                    $service->setEnd($end);
                    
                    if(!empty($event->where)) {
                        $service->setLocation($event->where);
                    }
                    
                    
                    
                    
                    //attendee
                    if ($request->all_employees) {
                        $attendeeUsers = User::allEmployees();
                        $attendees = [];
                        foreach ($attendeeUsers as $index => $row) {
                            $attendee = new Google_Service_Calendar_EventAttendee();
                            $attendee->setEmail($row->email);
                            $attendee->setDisplayName($row->name);
                            $attendees[] = $attendee;
                        }

                        $service->attendees = $attendees;
                    }
                    
                    if ($request->user_id) {
                        foreach ($request->user_id as $userId) {
                            $row = User::where('id', ($userId))->first();
                            if($row) {
                                $attendee = new Google_Service_Calendar_EventAttendee();
                                $attendee->setEmail($row->email);
                                $attendee->setDisplayName($row->name);
                                $attendees[] = $attendee;
                            }
                        }
                    }
                   

                    //echo $calendarObj->calendar_id;exit;
                    $updated_event = $cal->events->update($calendarObj->calendar_id, $service->getId(), $service);
                } catch (\Exception $e) {
//                    echo 'ERR<pre>';
//                    print_r($e->getMessage());
//                    exit;
                }
            }
        }

        return Reply::success(__('messages.eventCreateSuccess'));
    }

    public function show($id)
    {
        $this->event = Event::findOrFail($id);
        $this->start = Carbon::createFromFormat('Y-m-d H:i:s', request()->start);
        $this->end = Carbon::createFromFormat('Y-m-d H:i:s', request()->end);

        return view('admin.event-calendar.show', $this->data);
    }

    public function removeAttendee(Request $request)
    {
        EventAttendee::destroy($request->attendeeId);
        $id = $request->event_id;
        $employees = User::doesntHave('attendee', 'and', function ($query) use ($id) {
            $query->where('event_id', $id);
        })
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->get();

        $employeesArray = [];

        foreach ($employees as $key => $employee) {
            $employeesArray[$key]['id'] = $employee->id;
            $employeesArray[$key]['text'] = (auth()->user()->id == $employee->id) ? $employeesArray[$key]['text'] = $employee->name . ' (You)' : $employeesArray[$key]['text'] = $employee->name;
        }

        return Reply::dataOnly(['status' => 'success', 'employees' => $employeesArray]);
    }

    public function destroy($id) {

        $this->googleclient = $this->GoogleSettings();
        if ($this->googleclient) {
            $event = Event::find($id);
            $calendarObj = Calendar::where('user_id', ($this->user->id))->first();
            if (isset($event->google_calendar_id) && !empty($event->google_calendar_id) && $calendarObj) {
                try {
                    $cal = new Google_Service_Calendar($this->googleclient);
                    $cal->events->delete($calendarObj->calendar_id, $event->google_calendar_id);
                } catch (\Exception $e) {
//                    echo 'ERR<pre>';
//                    print_r($e->getMessage());
//                    exit;
                }
            }
        }
        Event::destroy($id);

        return Reply::success(__('messages.eventDeleteSuccess'));
    }
    
    
    public function doSyncGoogleCalendar() {

        $calendars = Calendar::all();
        $base_timezone = $this->global->timezone;
        if ($calendars) {
            foreach ($calendars as $calendar) {
                try {

                    $sync_token = $calendar->sync_token;
                    $calendar_id = $calendar->id;
                    $company_id = $calendar->company_id;
                    $g_calendar_id = $calendar->calendar_id;
                    $this->googleclient = $this->GoogleSettings($calendar->user_id);

                    $g_cal = new Google_Service_Calendar($this->googleclient);
                    $g_calendar = $g_cal->calendars->get($g_calendar_id);
                    $calendar_timezone = $g_calendar->getTimeZone();

                    $events = DB::table('events')->where('company_id', '=', $company_id)->whereNotNull('google_calendar_id')->pluck('google_calendar_id')->all();
                    //$events = Event::withoutGlobalScope(CompanyScope::class)->where('company_id', '=', $company_id)->whereNotNull('google_calendar_id')->lists('google_calendar_id')->toArray();

                    $params = [
                        'showDeleted' => true,
                        'timeMin' => Carbon::now()
                                ->setTimezone($calendar_timezone)
                                ->toAtomString()
                    ];

                    if (!empty($sync_token)) {
                        $params = [
                            'syncToken' => $sync_token
                        ];
                    }
                    $googlecalendar_events = $g_cal->events->listEvents($g_calendar_id, $params);


                    while (true) {

                        foreach ($googlecalendar_events->getItems() as $g_event) {

                            $g_event_id = $g_event->id;
                            $g_event_title = $g_event->getSummary();
                            $g_event_description = $g_event->getDescription();
                            if (empty($g_event_description)) {
                                $g_event_description = $g_event_title;
                            }
                            $g_event_location = $g_event->getLocation();
                            $g_status = $g_event->status;

                            if ($g_status != 'cancelled') {

                                $g_datetime_start = Carbon::parse($g_event->getStart()->getDateTime())
                                        ->tz($calendar_timezone)
                                        ->setTimezone($base_timezone)
                                        ->format('Y-m-d H:i:s');

                                $g_datetime_end = Carbon::parse($g_event->getEnd()->getDateTime())
                                        ->tz($calendar_timezone)
                                        ->setTimezone($base_timezone)
                                        ->format('Y-m-d H:i:s');

                                $g_time_start = Carbon::parse($g_event->getStart()->getDateTime())
                                        ->tz($calendar_timezone)
                                        ->setTimezone($base_timezone)
                                        ->format('h:i A');

                                $g_time_end = Carbon::parse($g_event->getEnd()->getDateTime())
                                        ->tz($calendar_timezone)
                                        ->setTimezone($base_timezone)
                                        ->format('h:i A');

                                //check if event id is already in the events table
                                if (in_array($g_event_id, $events)) {
                                    //update event
                                    $event = Event::where('google_calendar_id', '=', $g_event_id)->first();
                                    $event->event_name = $g_event_title;
                                    $event->description = $g_event_description;
                                    $event->where = $g_event_location;
                                    $event->google_calendar_id = $g_event_id;
                                    $event->start_date_time = $g_datetime_start;
                                    $event->end_date_time = $g_datetime_end;
                                    $event->start_time = $g_time_start;
                                    $event->end_time = $g_time_end;
                                    $event->save();
                                } else {
                                    //add event
                                    $event = new Event;
                                    $event->event_name = $g_event_title;
                                    $event->description = $g_event_description;
                                    $event->where = $g_event_location;
                                    $event->google_calendar_id = $g_event_id;
                                    $event->start_date_time = $g_datetime_start;
                                    $event->end_date_time = $g_datetime_end;
                                    $event->start_time = $g_time_start;
                                    $event->end_time = $g_time_end;
                                    $event->save();
                                }
                            } else {
                                //delete event
                                if (in_array($g_event_id, $events)) {
                                    Event::where('google_calendar_id', '=', $g_event_id)->delete();
                                }
                            }
                        }

                        $page_token = $googlecalendar_events->getNextPageToken();
                        if ($page_token) {
                            $params['pageToken'] = $page_token;
                            $googlecalendar_events = $g_cal->events->listEvents($g_calendar_id, $params);
                        } else {
                            $next_synctoken = str_replace('=ok', '', $googlecalendar_events->getNextSyncToken());
                            //update next sync token
                            $calendar = Calendar::find($calendar_id);
                            $calendar->sync_token = $next_synctoken;
                            $calendar->save();
                            break;
                        }
                    }
                } catch (\Exception $e) {
//                    echo 'ERR<pre>';
//                    print_r($e->getMessage());
//                    exit;
                }
            }
        }

        echo 'Done';
    }

}
