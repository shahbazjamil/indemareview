<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends BaseModel
{
    protected $table = 'short_links';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'link'
    ];

}
