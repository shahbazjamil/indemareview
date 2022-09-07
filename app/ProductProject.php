<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductProject extends BaseModel
{
    protected $table = 'product_projects';

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
}
