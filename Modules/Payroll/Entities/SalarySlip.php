<?php

namespace Modules\Payroll\Entities;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Payroll\Observers\SalarySlipObserver;

class SalarySlip extends Model
{
    protected $guarded = ['id'];
    protected $dates = ['paid_on'];

    protected static function boot()
    {
        parent::boot();

        static::observe(SalarySlipObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function salary_group()
    {
        return $this->belongsTo(SalaryGroup::class, 'salary_group_id');
    }

    public function salary_payment_method()
    {
        return $this->belongsTo(SalaryPaymentMethod::class, 'salary_payment_method_id');
    }
}
