<?php

namespace App;

use App\Observers\CalendarObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Calendar extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(CalendarObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
    
}
