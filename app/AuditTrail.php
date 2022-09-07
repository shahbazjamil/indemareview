<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuditTrail extends Model
{
    /**
     * @var string
     */
    public $table = 'audit_trail';

    /**
     * @var array
     */
    public $fillable = [
        'company_id',
        'client_id',
        'email_automation_id',
        'lead_id',
        'email',
        'type',
        'title',
        'icon',
        'opens',
        'deliver_at',
    ];

    public const CLIENT = 1;
    public const LEAD = 2;

    public const LEFT_STEP = 1;
    public const ENTERED_STEP = 2;
    public const RECEIVED_STEP = 3;
    public const OPENED_STEP = 4;
    public const ERROR_MESSAGE = 5;
    public const TITLE = [
        self::LEFT_STEP => 'Left <b> Wait </b> step in <b> automation_name </b> flow.',
        self::ENTERED_STEP => 'Entered <b> email_type </b> step in <b> automation_name </b> flow.',
        self::RECEIVED_STEP => 'Received the <b> email_subject </b> email in <b> automation_name </b> flow.',
        self::OPENED_STEP => 'Opened the <b> email_subject </b> email sent from <b> automation_name </b> flow.',
        self::ERROR_MESSAGE => '<b>The recipients email returned undeliverable.</b>'
    ];

    /**
     * @return HasOne
     */
    public function client(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'client_id');
    }

    /**
     * @return HasOne
     */
    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class, 'id', 'lead_id');
    }
}
