<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCodeType extends BaseModel
{
    protected $table = 'product_code_types';

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function code(){
        return $this->belongsTo(CodeType::class, 'code_type_id');
    }
}
