<?php

namespace Modules\Zoom\Entities;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Modules\Zoom\Observers\ZoomSettingObserver;
use Illuminate\Support\Facades\Config;

class ZoomSetting extends Model
{
    protected $table = 'zoom_setting';

    protected $fillable = ['api_key', 'secret_key', 'meeting_app', 'company_id', 'id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(ZoomSettingObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
    
    protected static function setZoom()
    {
        $zoomSetting = ZoomSetting::first();

        if ($zoomSetting) {
            Config::set('zoom.api_key', $zoomSetting->api_key);
            Config::set('zoom.api_secret', $zoomSetting->secret_key);
        }
    }
}
