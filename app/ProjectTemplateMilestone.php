<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProjectTemplateMilestone extends BaseModel
{
    

//    public function routeNotificationForMail()
//    {
//        return $this->user->email;
//    }

    public function projectTemplate(){
        return $this->belongsTo(ProjectTemplate::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

//    public function user(){
//        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
//    }
    
}
