<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectRoomProduct extends Model {

    protected $guarded = ['id'];

    public function room() {
        return $this->belongsTo(ProjectRoom::class, 'room_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
