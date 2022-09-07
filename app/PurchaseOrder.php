<?php

namespace App;

use App\Observers\PurchaseOrderObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends BaseModel
{
    use Notifiable;
    use CustomFieldsTrait; use SoftDeletes;
    protected $fillable = ['vendor_id'];

    protected $dates = ['purchase_order_date'];
    //protected $appends = ['total_amount', 'issue_on', 'invoice_number'];

    protected static function boot()
    {
        parent::boot();

        static::observe(PurchaseOrderObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

   

    public function vendor()
    {
        return $this->belongsTo(ClientVendorDetails::class, 'vendor_id');
    }
    
    

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items(){
        return $this->hasMany(PurchaseOrderItems::class, 'purchase_order_id');
    }

    public function getOriginalInvoiceNumberAttribute(){
        $invoiceSettings = InvoiceSetting::select('invoice_digit')->first();
        $zero = '';
        if (strlen($this->attributes['invoice_number']) < $invoiceSettings->invoice_digit){
            for ($i=0; $i<$invoiceSettings->invoice_digit-strlen($this->attributes['invoice_number']); $i++){
                $zero = '0'.$zero;
            }
        }
        $zero = '#'.$zero.$this->attributes['invoice_number'];
        return $zero;
    }

    public function getInvoiceNumberAttribute($value){
        if(!is_null($value)){
            $invoiceSettings = InvoiceSetting::select('invoice_prefix', 'invoice_digit')->first();
            $zero = '';
            if (strlen($value) < $invoiceSettings->invoice_digit){
                for ($i=0; $i<$invoiceSettings->invoice_digit-strlen($value); $i++){
                    $zero = '0'.$zero;
                }
            }
            $zero = $invoiceSettings->invoice_prefix.'#'.$zero.$value;
            return $zero;
        }
        return "";
    }
}
