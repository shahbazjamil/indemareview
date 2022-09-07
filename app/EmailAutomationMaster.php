<?php

namespace App;

use App\Observers\EmailAutomationMasterObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailAutomationMaster extends Model
{
    /**
     * @var string
     */
    public $table = 'email_automation_master';

    /**
     * @var array
     */
    public $fillable = [
        'name',
        'company_id',
        'user_id',
        'step',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::observe(EmailAutomationMasterObserver::class);

        static::addGlobalScope(new CompanyScope);
        
//        // Order by name ASC
//        static::addGlobalScope('order', function (Builder $builder) {
//            $builder->orderBy('location_name', 'asc');
//        });
        
    }

    /**
     * @return HasMany
     */
    public function emailAutomations(): HasMany
    {
        return $this->hasMany(EmailAutomation::class, 'email_automation_id', 'id');
    }
}
