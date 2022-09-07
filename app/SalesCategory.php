<?php

namespace App;

use App\Observers\SalesCategoryObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SalesCategory extends BaseModel
{
    protected $table = 'sales_categories';

    protected static function boot()
    {
        parent::boot();

        static::observe(SalesCategoryObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
}
