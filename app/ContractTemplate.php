<?php

namespace App;

use App\Observers\ContractTemplateObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ContractTemplate extends BaseModel
{
    

    protected static function boot()
    {
        parent::boot();

        static::observe(ContractTemplateObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
