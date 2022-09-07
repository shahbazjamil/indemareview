<?php

namespace App;

use App\Observers\ContractObserver;
use App\Observers\ClientContractSignObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ClientContractSign extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(ClientContractSignObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
    public function getSignatureAttribute()
    {
        return asset_url('contract/sign/'.$this->attributes['signature']);
    }
}
