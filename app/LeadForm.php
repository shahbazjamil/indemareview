<?php

namespace App;

use App\Observers\LeadFormObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LeadForm extends BaseModel
{
    protected $table = 'lead_form';

    protected static function boot()
    {
        parent::boot();

        static::observe(LeadFormObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
}
