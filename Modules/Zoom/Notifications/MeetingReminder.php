<?php

namespace Modules\Zoom\Notifications;

use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Zoom\Entities\ZoomMeeting;

class MeetingReminder extends Notification
{
    use Queueable,SmtpSettings;

    private $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ZoomMeeting $event)
    {
        $this->setMailConfigs();
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = array();
        if ($notifiable->email_notifications) {
            array_push($via, 'mail');
        }
        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('zoom::email.meetingReminder.subject') . ' - ' . config('app.name'))
            ->greeting(__('zoom::email.hello') . ' ' . ucwords($notifiable->name) . '!')
            ->line(__('zoom::email.meetingReminder.text'))
            ->line(__('zoom::modules.zoommeeting.meetingName') . ': ' . $this->event->meeting_name)
            ->line(__('zoom::app.meetings.time') . ': ' . $this->event->start_date_time->toDayDateTimeString())
            ->action(__('zoom::email.loginDashboard'), url('/'))
            ->line(__('zoom::email.thankyouNote'));
    }
}
