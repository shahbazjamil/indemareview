<?php

namespace App;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;

class EstimateItem extends BaseModel
{
    protected $guarded = ['id'];

    public static function taxbyid($id) {
        return Tax::where('id', $id);
    }


    protected $appends = ['product_url'];

    /**
     * @return Application|UrlGenerator|string
     */
    public function getProductUrlAttribute()
    {
        return ($this->product_image) ? asset_url('estimates/products/'.$this->id .'/'. $this->product_image) : asset('img/img-dummy.jpg');
    }
    public function group()
    {
        return $this->belongsTo(LineItemGroup::class, 'group_id');
    }
}
