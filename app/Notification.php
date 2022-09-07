<?php

namespace App;

use App\Observers\NotificationObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class Notification extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(NotificationObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }
}
