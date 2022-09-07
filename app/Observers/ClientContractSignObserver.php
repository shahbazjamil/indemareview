<?php

namespace App\Observers;

use App\ClientContractSign;

class ClientContractSignObserver
{
    public function saving(ClientContractSign $sign)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $sign->company_id = company()->id;
        }
    }
}
