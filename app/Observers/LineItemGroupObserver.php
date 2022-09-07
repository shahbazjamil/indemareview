<?php

namespace App\Observers;

use App\LineItemGroup;

class LineItemGroupObserver
{

    public function saving(LineItemGroup $lineItemGroup)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $lineItemGroup->company_id = company()->id;
        }
    }

}
