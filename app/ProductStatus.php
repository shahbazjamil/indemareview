<?php

namespace App;

use App\Observers\ProductStatusObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductStatus extends BaseModel
{
    protected $table = 'product_status';

    protected static function boot()
    {
        parent::boot();

        static::observe(ProductStatusObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
}
