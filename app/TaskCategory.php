<?php

namespace App;

use App\Observers\TaskCategoryObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskCategory extends BaseModel
{
    protected $table = 'task_category';

    protected static function boot()
    {
        parent::boot();

        static::observe(TaskCategoryObserver::class);

        static::addGlobalScope(new CompanyScope);
        
        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('category_name', 'asc');
        });
        
    }
}
