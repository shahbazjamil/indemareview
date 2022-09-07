<?php

namespace App\Observers;

use App\ProjectRoom;

class ProjectRoomObserver
{

    public function saving(ProjectRoom $room)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $room->company_id = company()->id;
        }
    }

}
