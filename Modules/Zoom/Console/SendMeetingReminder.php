<?php

namespace Modules\Zoom\Console;

use App\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Zoom\Entities\ZoomMeeting;
use Modules\Zoom\Events\MeetingReminderEvent;

class SendMeetingReminder extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'send-zoom-meeting-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send meeting reminder to the attendees before time specified in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->globalSettings = Setting::first();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $events = ZoomMeeting::select('id', 'meeting_name', 'label_color', 'description', 'start_date_time', 'end_date_time', 'repeat', 'send_reminder', 'remind_time', 'remind_type')
            ->where('start_date_time', '>=', Carbon::now($this->globalSettings->timezone))
            ->where('send_reminder', 1)
            ->get();

        if ($events->count() > 0) {
            foreach ($events as $event) {
                $reminderDateTime = $this->calculateReminderDateTime($event);
                if ($reminderDateTime->equalTo(Carbon::now()->timezone($this->globalSettings->timezone)->startOfMinute())) {
                    event(new MeetingReminderEvent($event));
                }
            }
        }
    }

    public function calculateReminderDateTime(ZoomMeeting $event)
    {
        $time = $event->remind_time;
        $type = $event->remind_type;

        $reminderDateTime = '';

        switch ($type) {
            case 'day':
                $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date_time, $this->globalSettings->timezone)->subDays($time);
                break;
            case 'hour':
                $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date_time, $this->globalSettings->timezone)->subHours($time);
                break;
            case 'minute':
                $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date_time, $this->globalSettings->timezone)->subMinutes($time);
                break;
        }

        return $reminderDateTime;
    }
}
