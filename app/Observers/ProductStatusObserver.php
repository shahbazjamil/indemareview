<?php

namespace App\Observers;

use App\ProductStatus;

class ProductStatusObserver
{

    public function saving(ProductStatus $type)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $type->company_id = company()->id;
        }
    }

}
