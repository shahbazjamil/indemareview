<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactProject extends BaseModel
{
    protected $table = 'contact_projects';

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function contact(){
        return $this->belongsTo(ClientContact::class, 'contact_id');
    }
}
