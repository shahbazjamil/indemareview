<?php

namespace App\Observers;

use App\Notification;


class NotificationObserver
{
    public function saving(Notification $notification)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $notification->company_id = company()->id;
        }
    }
}
