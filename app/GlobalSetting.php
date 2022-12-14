<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends BaseModel
{
    protected $table = 'global_settings';
    protected $default = ['id'];
    protected $appends = ['login_background_url','logo_url','logo_front_url','show_public_message', 'gmail_login_background_url', 'gmail_logo_url'];

    public function getLoginBackgroundUrlAttribute()
    {
        if (is_null($this->login_background) || $this->login_background == 'login-background.jpg') {
            return asset('img/login-bg.jpg');
        }

        return asset_url('login-background/' . $this->login_background);
    }

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('img/worksuite-logo.png');
        }

        return asset_url('app-logo/' . $this->logo);
    }

    public function getGmailLoginBackgroundUrlAttribute()
    {
        if (is_null($this->login_background) || $this->login_background == 'login-background.jpg') {
            return public_path().'/img/login-bg.jpg';
        }

        return public_path().'/user-uploads/login-background/' . $this->login_background;
    }

    public function getGmailLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return public_path().'/img/worksuite-logo.png';
        }

        return public_path().'/user-uploads/app-logo/' . $this->logo;
    }

    public function getLogoFrontUrlAttribute()
    {
        if (is_null($this->logo_front)) {
            if (is_null($this->logo)) {
                return asset('front/img/worksuite-logo.png');
            }
            return $this->logo_url;
        }
        return asset_url('app-logo/'.$this->logo_front);
    }

    public function currency()
    {
        return $this->belongsTo(GlobalCurrency::class, 'currency_id')->withTrashed();
    }

    public function getShowPublicMessageAttribute()
    {
        if (strpos(request()->url(), request()->getHost().'/public') !== false){
            return true;
        }
        return false;
    }
}
