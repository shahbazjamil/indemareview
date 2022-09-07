<?php

namespace App;

use App\Observers\CodeTypeObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CodeType extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(CodeTypeObserver::class);

        static::addGlobalScope(new CompanyScope);
        
        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('location_name', 'asc');
        });
        
    }
    
    public function comments()
    {
        return $this->hasMany(LocationNote::class, 'code_type_id')->orderBy('id', 'desc');
    }
}
