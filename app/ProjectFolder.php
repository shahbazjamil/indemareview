<?php

namespace App;

use App\Observers\ProjectFolderObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectFolder extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(ProjectFolderObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    
}
