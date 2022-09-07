<?php

namespace App\Observers;

use App\LeadForm;

class LeadFormObserver
{

    public function saving(LeadForm $form)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $form->company_id = company()->id;
        }
    }

}
