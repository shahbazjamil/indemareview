<?php

namespace Modules\Zoom\Providers;

use App\Events\CompanyRegistered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Zoom\Events\MeetingInviteEvent;
use Modules\Zoom\Events\MeetingReminderEvent;
use Modules\Zoom\Listeners\MeetingInviteListener;
use Modules\Zoom\Listeners\MeetingReminderListener;
use Modules\Zoom\Listeners\NewZoomSettingListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MeetingInviteEvent::class => [MeetingInviteListener::class],
        MeetingReminderEvent::class => [MeetingReminderListener::class],
        CompanyRegistered::class => [NewZoomSettingListener::class],
    ];
}
