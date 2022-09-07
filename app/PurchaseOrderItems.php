<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItems extends BaseModel
{
    protected $guarded = ['id'];

    public static function taxbyid($id) {
        return Tax::where('id', $id);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function productbyid($id)
    {
        return Product::where('id', $id);
    }

}
