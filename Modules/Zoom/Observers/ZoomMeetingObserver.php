<?php

namespace Modules\Zoom\Observers;

use Modules\Zoom\Entities\ZoomMeeting;

class ZoomMeetingObserver
{

    public function saving(ZoomMeeting $meeting)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $meeting->company_id = company()->id;
        }
    }

}
