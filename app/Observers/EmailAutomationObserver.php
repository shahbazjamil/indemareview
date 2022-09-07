<?php

namespace App\Observers;

use App\EmailAutomation;

class EmailAutomationObserver
{

    public function saving(EmailAutomation $type)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $type->company_id = company()->id;
        }
    }

}
