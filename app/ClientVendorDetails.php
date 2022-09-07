<?php

namespace App;

use App\Observers\ClientVendorDetailObserver;
use App\Scopes\CompanyScope;
use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ClientVendorDetails extends BaseModel
{
    
    
    use Notifiable;
    use CustomFieldsTrait;

    protected $table = 'client_vendor_details';
    //protected $fillable = ['company_name','user_id','address','website','note','skype','facebook','twitter','linkedin','gst_number', 'shipping_address', 'email_notifications'];
    //protected $default = ['id','company_name','address','website','note','skype','facebook','twitter','linkedin','gst_number'];
    //protected $appends = ['image_url'];
    protected static function boot()
    {
        parent::boot();

        static::observe(ClientVendorDetailObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
