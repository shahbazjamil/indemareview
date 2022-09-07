<?php

namespace App;

use App\Observers\PoStatusObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PoStatus extends BaseModel
{
    protected $table = 'po_status';

    protected static function boot()
    {
        parent::boot();

        static::observe(PoStatusObserver::class);

        static::addGlobalScope(new CompanyScope);
        
        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('type', 'asc');
        });
        
    }
}
