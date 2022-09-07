<?php

namespace App;

use App\Observers\SalescategoryTypeObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SalescategoryType extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(SalescategoryTypeObserver::class);

        static::addGlobalScope(new CompanyScope);
        
        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('salescategory_name', 'asc');
        });
        
    }
}
