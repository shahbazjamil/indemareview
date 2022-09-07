<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectClient extends BaseModel
{
    protected $table = 'project_clients';

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function client(){
        return $this->belongsTo(User::class, 'client_id');
    }
}
