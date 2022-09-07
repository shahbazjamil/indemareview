<?php

namespace App;

use App\Observers\ProjectRoomObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProjectRoom extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(ProjectRoomObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
