<?php

namespace App;

use App\Observers\ProductSettingObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductSetting extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(ProductSettingObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
}
