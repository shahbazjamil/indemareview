<?php

namespace App;

use App\Observers\TaskTemplateObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(TaskTemplateObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
