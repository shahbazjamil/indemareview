<?php

namespace App\Observers;

use App\ContractTemplate;

class ContractTemplateObserver
{

    public function saving(ContractTemplate $contractTemplate)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $contractTemplate->company_id = company()->id;
        }
    }

}
