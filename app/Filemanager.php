<?php

namespace App;

use App\Observers\FileStorageObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class Filemanager extends BaseModel
{
    protected $table = 'filemanager';

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::observe(FileStorageObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
}
