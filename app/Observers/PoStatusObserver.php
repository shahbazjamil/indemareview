<?php

namespace App\Observers;

use App\PoStatus;

class PoStatusObserver
{

    public function saving(PoStatus $status)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $status->company_id = company()->id;
        }
    }

}
