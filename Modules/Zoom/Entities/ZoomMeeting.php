<?php

namespace Modules\Zoom\Entities;

use App\Scopes\CompanyScope;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Zoom\Observers\ZoomMeetingObserver;

class ZoomMeeting extends Model
{
    protected $table = 'zoom_meetings';
    protected $guarded = ['id'];

    protected $dates = ['start_date_time', 'end_date_time'];

    protected static function boot()
    {
        parent::boot();

        static::observe(ZoomMeetingObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class);
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function setStartDateTimeAttribute($value)
    {
        $global = company_setting();
        $date_time = explode('T', $value);

        $this->attributes['start_date_time'] = Carbon::createFromFormat($global->date_format, $date_time[0])->format('Y-m-d') . ' ' . Carbon::createFromFormat($global->time_format, $date_time[1])->format('H:i:s');
    }

    public function setEndDateTimeAttribute($value)
    {
        $global = company_setting();
        $date_time = explode('T', $value);

        $this->attributes['end_date_time'] = Carbon::createFromFormat($global->date_format, $date_time[0])->format('Y-m-d') . ' ' . Carbon::createFromFormat($global->time_format, $date_time[1])->format('H:i:s');
    }
}
