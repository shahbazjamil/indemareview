<?php

namespace App\Http\Controllers\Front;

use App\FooterMenu;
use App\FrontDetail;
use App\FrontMenu;
use App\FrontWidget;
use App\GlobalSetting;
use App\Http\Controllers\Controller;
use App\LanguageSetting;
use App\Social;
use App\SocialAuthSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use App\MixpanelSettings;

class FrontBaseController extends Controller
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        try {
            rename(public_path("front-uploads"), public_path("user-uploads/front"));
        } catch (\Exception $e) {
        }
        
        

        $this->setting = GlobalSetting::first();
        
        

        $this->languages = LanguageSetting::where('status', 'enabled')->get();
        $this->socialAuthSettings = SocialAuthSetting::first();
        $this->global = $this->setting;
        $this->frontDetail    = FrontDetail::first();

        if (Cookie::get('language')) {
            $this->locale = Crypt::decrypt(Cookie::get('language'), false);
            App::setLocale($this->locale);
        } else {
            $this->locale = $this->frontDetail->locale;
            App::setLocale('en');
        }

        Carbon::setLocale($this->locale);

        $this->footerSettings = FooterMenu::whereNotNull('slug')->get();

        $this->frontMenu = FrontMenu::first();



        $this->frontWidgets    = FrontWidget::all();

        setlocale(LC_TIME, $this->locale . '_' . strtoupper($this->locale));

        $this->detail = $this->frontDetail;

    }
    
    public function mixPanelTrackEvent (string $name, array $options , $user = false){
        // micpanel tracking
        $options['host'] = 'app.indema.co';
        $setting = MixpanelSettings::first();
        $mp = \Mixpanel::getInstance($setting->project_token);
        $mp->track($name, $options);
        if($user) {
            $user_id = encode($user->id,'epgjhev4');
            $mp->people->set($user_id, array(
                '$company_id'       => $user->company_id,
                '$email'        => $user->email,
                '$company_name' => $user->company->company_name,
                '$company_email' => $user->company->company_email,
                '$hear_about' => $user->company->hear_about
            ));
        }
    }
}