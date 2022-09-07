<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class VendorDetails extends BaseModel
{
    use Notifiable;
    use CustomFieldsTrait;

    protected $table = 'client_vendor_details';
    protected $fillable = ['company_name','company_website','company_address','vendor_name','vendor_email','vendor_mobile','vendor_skype','vendor_linkedIn','vendor_twitter','vendor_facebook', 'vendor_gst_number', 'vendor_shipping_address','vendor_note','status'];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

}
