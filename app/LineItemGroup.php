<?php

namespace App;

use App\Observers\LineItemGroupObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LineItemGroup extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(LineItemGroupObserver::class);

        static::addGlobalScope(new CompanyScope);
        
        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('group_name', 'asc');
        });
        
        
    }
}
