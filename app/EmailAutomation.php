<?php

namespace App;

use App\Observers\EmailAutomationObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmailAutomation extends Model
{
    /**
     * @var string
     */
    public $table = 'email_automations';

    /**
     * @var array
     */
    public $fillable = [
        'email_automation_id',
        'email_template_id',
        'company_id',
        'client_id',
        'project_id',
        'email_type',
        'time_period',
        'time_unit',
        'time_type',
        'automation_event',
        'step',
        'is_manual'
    ];

    public const SEND_AN_EMAIL = 1;
    public const SEND_FILE_VIA_EMAIL = 2;

    public const EMAIL_TYPE = [
        self::SEND_AN_EMAIL => 'Send Email',
        self::SEND_FILE_VIA_EMAIL => 'Send File Via Email',
    ];

    public const IS_MANUAL = 1;
    public const IS_AUTOMATIC = 0;

    public const LEAD_CREATED = 2;
    public const CLIENT_CREATED = 3;
    public const START_PROJECT = 4;
    public const END_PROJECT = 5;
    public const LAST_STEP_AUTOMATION = 6;
    public const PAYMENT_RECEIVED = 7;

    public const AUTOMATION_EVENTS = [
        self::LEAD_CREATED => 'when a Lead is entered into indema',
        self::CLIENT_CREATED => 'when a client is added to indema',
        self::START_PROJECT => 'the start date of a project.',
        self::END_PROJECT => 'the end date of a project.',
        self::PAYMENT_RECEIVED => 'when a payment is received.',
        self::LAST_STEP_AUTOMATION => 'last automation step completed',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::observe(EmailAutomationObserver::class);

        static::addGlobalScope(new CompanyScope);
        
//        // Order by name ASC
//        static::addGlobalScope('order', function (Builder $builder) {
//            $builder->orderBy('location_name', 'asc');
//        });
        
    }

    /**
     * @return HasOne
     */
    public function emailTemplate(): HasOne
    {
        return $this->hasOne(EmailTemplate::class, 'id', 'email_template_id');
    }

    /**
     * @return HasOne
     */
    public function emailAutomationMaster(): HasOne
    {
        return $this->hasOne(EmailAutomationMaster::class, 'id', 'email_automation_id');
    }
}
