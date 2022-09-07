<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargebeeInvoice extends BaseModel
{
    protected $table = 'chargebee_invoices';
    protected $dates = ['pay_date', 'next_pay_date'];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id')->withoutGlobalScopes(['active']);
    }

    public function package() {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
