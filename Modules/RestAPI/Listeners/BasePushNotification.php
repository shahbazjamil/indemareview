<?php
namespace Modules\RestAPI\Listeners;

use Edujugon\PushNotification\PushNotification;
use Modules\RestAPI\Entities\RestAPISetting;
use Modules\RestAPI\Entities\User;

class BasePushNotification
{
    protected $push;
    public function __construct()
    {
        $this->push = new PushNotification('fcm');
    }

    public function devices($user)
    {
        if (!$user) {
            return [];
        }
        $authUser = api_user();

         if (!$authUser) {
            return [];
        }
        // Ignore for self devices
        if ($user->id === $authUser->id) {
            return [];
        }
        $userRestAPI =  User::find($user->id);
        $devices = array_column($userRestAPI->devices->toArray(), 'registration_id');
        return $devices;
    }

    public function sendNotification($user)
    {
        $this->setKey();
        $this->push->setDevicesToken($this->devices($user))->send();
    }

    // Function to set the FCM_KEY key before sending message.
    public function setKey()
    {
        $setting =  RestAPISetting::first();
        $fcm_key =  !is_null($setting->fcm_key)? $setting->fcm_key:config('pushnotification.fcm.apiKey');
        $this->push->setApiKey($fcm_key);
    }
}
