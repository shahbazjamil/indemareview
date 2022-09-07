<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use App\Currency;
use App\GlobalSetting;
use App\Http\Requests\Currency\StoreCurrency;
use App\Http\Requests\Currency\StoreCurrencyExchangeKey;
use App\Http\Requests\Currency\UpdateCurrency;
use App\Traits\CurrencyExchange;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Event;
use App\EventAttendee;
use App\User;
use App\Calendar;
use App\Scopes\CompanyScope;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_EventAttendee;
use Illuminate\Support\Facades\DB;

class CronController extends BaseController
{
    protected $googleclient = '';
    protected $global = '';

    public function __construct()
    {
        //parent::__construct();
    }
    /**
     * @return array
     */
    public function updateExchangeRate()
    {
        
        try {
            $this->updateExchangeRates();
        } catch (\Throwable $th) {
            //throw $th;
        }
        echo 'Done';
    }
    
    public function doSyncGoogleCalendar() {

        
        $calendars = Calendar::all();
        if ($calendars) {
            foreach ($calendars as $calendar) {
                try {

                    $sync_token = $calendar->sync_token;
                    $calendar_id = $calendar->id;
                    $company_id = $calendar->company_id;
                    $g_calendar_id = $calendar->calendar_id;
                    
                    
                    $this->googleclient = $this->GoogleSettings($calendar->user_id);
                    if ($this->googleclient) {
                    $this->global = company_setting_by_id($company_id);
                    $base_timezone = $this->global->timezone;

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
                                    $event->company_id = $company_id;
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
                                    $event->company_id = $company_id;
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
    
    public function GoogleSettings($user_id = 0) {

        if ($user_id != 0) {
            $adminSetting = User::where('id', ($user_id))->first();
        } else {
            $adminSetting = User::where('email', ($this->user->email))->first();
        }

        $client = false;

        if (isset($adminSetting->google_token) && !empty($adminSetting->google_token)) {
            try {
                $client = new Google_Client();
                $client->setAuthConfig('client_secret.json');
                if ($client) {
                    // Refresh the token if it's expired.
                    $client->setAccessToken($adminSetting->google_token);

                    if ($client->isAccessTokenExpired()) {
                        $accessToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                        $newAccessToken = $client->getAccessToken();
                        User::where('id', ($user_id))
                                ->update(['google_token' => json_encode($newAccessToken)]);
                    }
                }
            } catch (\Exception $e) {
                //var_dump('admin : ' . $e->getMessage());exit;
            }
        }
        return $client;
    }

    public function getGoogleClient() {

        try {
            $client = new Google_Client();
            
//            $client->setClientId('785362369026-umnphkuublfm522b4gr4hq3plhirp034.apps.googleusercontent.com');
//            $client->setClientSecret('MBK0B8K9eFTH654GTyOy6bwc');
//            $client->setRedirectUri(route('admin.settings.google-oauth'));
//            $client->setScopes('email,profile,https://www.googleapis.com/auth/calendar');
//            $client->setApprovalPrompt("force");
//            $client->setAccessType("offline");
//            $client->setDeveloperKey('AIzaSyDXdYFQ7y_HYacigsJ_QTMqee6zugw0PeY');
            
            $client->setAuthConfig('client_secret.json');
            $client->setApprovalPrompt("force");
            $client->setAccessType('offline');
            $client->setScopes(Google_Service_Calendar::CALENDAR);
            $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
            $client->setHttpClient($guzzleClient);
            
            return $client;
        } catch (\Exception $e) {
            //var_dump($e->getMessage());exit;
            return false;
        }
    }

}
