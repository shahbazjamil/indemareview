<?php

namespace App\Observers;

use App\ProductSetting;

class ProductSettingObserver
{

    public function saving(ProductSetting $message)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $message->company_id = company()->id;
        }
    }

}
