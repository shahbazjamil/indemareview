<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\EmailNotificationSetting;
use App\GdprSetting;
use App\LanguageSetting;
use App\ModuleSetting;
use App\Notification;
use App\Notifications\LicenseExpire;
use App\Package;
use App\PackageSetting;
use App\ProjectActivity;
use App\PushNotificationSetting;
use App\StickyNote;
use App\Traits\FileSystemSettingTrait;
use App\UniversalSearch;
use App\UserActivity;
use App\UserChat;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use App\ThemeSetting;
use Illuminate\Support\Facades\Auth;
use App\GlobalSetting;
use App\TaskHistory;
use App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;
use App\MixpanelSettings;


//bitsclan code
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use App\QuickbooksSettings;
use App\Currency;
use App\ProductSetting;

use QuickBooksOnline\API\Facades\Account;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_Calendar;

class AdminBaseController extends Controller
{
    use FileSystemSettingTrait;

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
        
        // Inject currently logged in user object into every view of user dashboard
        $this->middleware(function ($request, $next) {
            
             if(Auth::user()->company->status == 'inactive'){
                Auth::logout();
                return redirect('logout');
            }
            
            if(!ProductSetting::first()) {
                    $prodSetting = new ProductSetting();
                    $prodSetting->company_id = user()->company_id;
                    $prodSetting->save();
            }

            $this->global = $this->company = company_setting();

            $this->superadmin = global_settings();

            $this->pushSetting = push_setting();
            $this->companyName = $this->global->company_name;

            $this->adminTheme = admin_theme();
            $this->languageSettings = language_setting();

            App::setLocale($this->global->locale);
            Carbon::setLocale($this->global->locale);
            setlocale(LC_TIME, $this->global->locale . '_' . strtoupper($this->global->locale));
            $this->setFileSystemConfigs();

            
            $this->isClient = User::withoutGlobalScope(CompanyScope::class)
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.id', 'users.name', 'users.email', 'users.created_at')
                ->where('roles.name', 'client')
                ->where('role_user.user_id', user()->id)
                ->where('users.company_id', user()->company_id)
                ->first();

            $this->user = user();
            $this->unreadNotificationCount = count($this->user->unreadNotifications);

            // For GDPR
            try {
                $this->gdpr = GdprSetting::first();

                if (!$this->gdpr) {
                    $gdpr = new GdprSetting();
                    $gdpr->company_id = Auth::user()->company_id;
                    $gdpr->save();

                    $this->gdpr = $gdpr;
                }
            } catch (\Exception $e) {
            }

            $company = $this->global;
            $expireOn = $company->licence_expire_on;
            $currentDate = Carbon::now();

            $packageSettingData = package_setting();
            $this->packageSetting = ($packageSettingData->status == 'active') ? $packageSettingData : null;
            
            if(!str_contains(url()->current(), '/billing') && !str_contains(url()->current(), '/account-setup')) {
                  $this->checkTrialPackage($company);
            }
            
            if(is_null($company->currency_id)) {
                $this->updateCurrency($company);
            }

            if (!is_null($expireOn)) {
                
                $expireOn->addDays(11); // added 11 days extra 
                if($expireOn->lessThan($currentDate)) {
                    $this->checkLicense($company);
                }
            }
            
            // booking add ons expire on
            //$bookingExpireOn = $company->booking_expire_on;
            $bookingExpireOn = Carbon::parse($company->booking_expire_on);
            
            if ((!is_null($bookingExpireOn) && $bookingExpireOn->lessThan($currentDate))) {
                 $this->checkBookingAddOns($company);
            }

            // Social add ons expire on
            $socialExpireOn = Carbon::parse($company->social_expire_on);
            if ((!is_null($socialExpireOn) && $socialExpireOn->lessThan($currentDate))) {
                 $this->checkSocialAddOns($company);
            }

            // Additional users add ons expire on
            //$additionalUserExpireOn = $company->additional_user_expire_on;
            $additionalUserExpireOn = Carbon::parse($company->additional_user_expire_on);
            if ((!is_null($additionalUserExpireOn) && $additionalUserExpireOn->lessThan($currentDate))) {
                 $this->checkBookingAddOns($company);
            }

            
            

            $this->modules = $this->user->modules;
            
//            if($this->user->id == '982') {
//                echo '<pre>';
//                print_r($this->modules);
//                exit();
//            }

            $this->unreadMessageCount = UserChat::where('to', $this->user->id)->where('message_seen', 'no')->count();
            $this->unreadTicketCount = Notification::where('notifiable_id', $this->user->id)
                ->where('type', 'App\Notifications\NewTicket')
                ->whereNull('read_at')
                ->count();

            $this->unreadExpenseCount = Notification::where('notifiable_id', $this->user->id)
                ->where('type', 'App\Notifications\NewExpenseAdmin')
                ->whereNull('read_at')
                ->count();

            $this->stickyNotes = StickyNote::where('user_id', $this->user->id)
                ->orderBy('updated_at', 'desc')
                ->get();

            $this->worksuitePlugins = worksuite_plugins();

            if (config('filesystems.default') == 's3') {
                $this->url = "https://" . config('filesystems.disks.s3.bucket') . ".s3.amazonaws.com/";
            }

            return $next($request);
        });
    }



    public function QuickbookSettings()
    {
            // bitsclan code start here
        $setting = QuickbooksSettings::first();

        $adminSetting = User::where('email', ($this->user->email))->first();
        
        $app_rand = rand(10,1000);
        if(isset($adminSetting->company) && !is_null($adminSetting->company->company_name)) {
            $app_company_name  = $adminSetting->company->company_name;
        }

        if(!empty($setting->client_secret) && !empty($setting->client_id)){

            if(!empty($adminSetting->access_token) &&  !empty($adminSetting->refresh_token)){
                try {
                    $quickbook = DataService::Configure(array(
                        'auth_mode' => 'oauth2',
                        'ClientID' => $setting->client_id,
                        'ClientSecret' => $setting->client_secret,
                        'accessTokenKey' =>  $adminSetting->access_token,
                        'refreshTokenKey' => $adminSetting->refresh_token,
                        'QBORealmID' => $adminSetting->realmid,
                        'baseUrl' => 'https://quickbooks.api.intuit.com'
                    ));


                    $OAuth2LoginHelper = $quickbook->getOAuth2LoginHelper();

                    $accessToken = $OAuth2LoginHelper->refreshToken();
                    $error = $OAuth2LoginHelper->getLastError();
                    $quickbook->updateOAuth2Token($accessToken);


                    $quickbook->throwExceptionOnError(true);
                    $CompanyInfo = $quickbook->getCompanyInfo();
                    $nameOfCompany = $CompanyInfo->CompanyName;





                    if(empty($adminSetting->income_account)){

                        $account = Account::create([
                            //"Name" => "Sales - Indema APP Company Service", 
                            "Name" => "Sales - Indema Company Service - $app_company_name $app_rand", 
                            "AccountType" => "Income"
                        ]);


                        $resultObj = $quickbook->Add($account);
                        $error =  $quickbook->getLastError();
                        if($error){
                            return Reply::error(__($error->getResponseBody()));
                        }

                        $account_qbo_id = $resultObj->Id;
                    }else{
                        $account_qbo_id = $adminSetting->income_account;
                    }


                    if(empty($adminSetting->bank_account)){
                        $bank_account = Account::create([
                            //"Name" => "Credit Indema APP Company", 
                            "Name" => "Credit Indema Company $app_company_name $app_rand", 
                            "AccountType" => "Bank"
                        ]);

                        $resultObj = $quickbook->Add($bank_account);
                        $error =  $quickbook->getLastError();
                        if($error){
                            return Reply::error(__($error->getResponseBody()));
                        }

                        $bank_accont_id = $resultObj->Id;
                    }else{
                        $bank_accont_id = $adminSetting->bank_account;
                    }


                    // if(empty($adminSetting->payable_account)){
                    //     $payable_account = Account::create([
                    //         "Name" => "Indema Account Payable", 
                    //         "AccountType" => "Accounts Payable"
                    //     ]);

                    //     $resultObj = $quickbook->Add($payable_account);
                    //     $error =  $quickbook->getLastError();
                    //     if($error){
                    //         return Reply::error(__($error->getResponseBody()));
                    //     }

                    //     $payable_account_id = $resultObj->Id;
                    // }else{
                    //     $payable_account_id = $adminSetting->payable_account;
                    // }


                    User::where('email', ($this->user->email))->update([
                        'refresh_token' => $accessToken->getRefreshToken(),
                        'access_token' => $accessToken->getAccessToken(),
                        'income_account' => $account_qbo_id,
                        'bank_account' => $bank_accont_id,
                        //'payable_account' => $payable_account_id
                    ]);
                    return $quickbook;
                } catch (\Exception $e) {
                    return false;
                }
            }
            return false;
        }
        return false;
        // bitsclan code end here
    }



    public function QuickbookCompany()
    {
            // bitsclan code start here
        $setting = QuickbooksSettings::first();

        $adminSetting = User::where('email', ($this->user->email))->first();


        if(!empty($setting->client_secret) && !empty($setting->client_id)){

            if(!empty($adminSetting->access_token) &&  !empty($adminSetting->refresh_token)){
                
                try {
                    $this->gdpr = GdprSetting::first();

                    if (!$this->gdpr) {
                        $gdpr = new GdprSetting();
                        $gdpr->company_id = Auth::user()->company_id;
                        $gdpr->save();

                        $this->gdpr = $gdpr;
                    }

                    $quickbook = DataService::Configure(array(
                        'auth_mode' => 'oauth2',
                        'ClientID' => $setting->client_id,
                        'ClientSecret' => $setting->client_secret,
                        'accessTokenKey' =>  $adminSetting->access_token,
                        'refreshTokenKey' => $adminSetting->refresh_token,
                        'QBORealmID' => $adminSetting->realmid,
                        'baseUrl' => 'https://quickbooks.api.intuit.com'
                    ));

                    $OAuth2LoginHelper = $quickbook->getOAuth2LoginHelper();

                    $accessToken = $OAuth2LoginHelper->refreshToken();
                    $error = $OAuth2LoginHelper->getLastError();
                    $quickbook->updateOAuth2Token($accessToken);


                    $quickbook->throwExceptionOnError(true);
                    $CompanyInfo = $quickbook->getCompanyInfo();
                    $nameOfCompany = $CompanyInfo->CompanyName;

                    User::where('email', ($this->user->email))->update([
                        'refresh_token' => $accessToken->getRefreshToken(),
                        'access_token' => $accessToken->getAccessToken(),
                    ]);

                    return $nameOfCompany;

                } catch (\Exception $e) {
                    return false;
                }
            }
            return false;
        }
        return false;
        // bitsclan code end here
    }

    public function logProjectActivity($projectId, $text)
    {
        $activity = new ProjectActivity();
        $activity->project_id = $projectId;
        $activity->activity = $text;
        $activity->save();
    }

    public function logUserActivity($userId, $text)
    {
        $activity = new UserActivity();
        $activity->user_id = $userId;
        $activity->activity = $text;
        $activity->save();
    }

    public function logSearchEntry($searchableId, $title, $route, $type)
    {
        $search = new UniversalSearch();
        $search->searchable_id = $searchableId;
        $search->title = $title;
        $search->route_name = $route;
        $search->module_type = $type;
        $search->save();
    }

    public function logTaskActivity($taskID, $userID, $text, $boardColumnId, $subTaskId = null)
    {
        $activity = new TaskHistory();
        $activity->task_id = $taskID;

        if (!is_null($subTaskId)) {
            $activity->sub_task_id = $subTaskId;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->board_column_id = $boardColumnId;
        $activity->save();
    }
    // if package is trial then redirect to billing page to change package.
    public function checkTrialPackage($company)
    {
        $currentPackage = $company->package;
        if ($currentPackage->id == 2){
            $url = url("/admin/billing/packages");
            echo '<script>window.location = "'.$url.'";</script>';exit;
            //return redirect()->route('admin.billing');
        }
    }
    
    // if currency not set then set default currency doallar
     public function updateCurrency($company) {
        $currency = Currency::where('currency_name', 'Dollars')->first();
        if($currency) {
            $company->currency_id = $currency->id;
            $company->save();
        }
     }
    
    

    public function checkLicense($company)
    {
        $packageSettingData = PackageSetting::first();
        $packageSetting = ($packageSettingData->status == 'active') ? $packageSettingData : null;
        $packages = Package::all();

        $trialPackage = $packages->filter(function ($value, $key) {
            return $value->default == 'trial';
        })->first();

        $defaultPackage = $packages->filter(function ($value, $key) {
            return $value->default == 'yes';
        })->first();

        $otherPackage = $packages->filter(function ($value, $key) {
            return $value->default == 'no';
        })->first();

        if ($packageSetting && !is_null($trialPackage)) {
            $selectPackage = $trialPackage;
        } elseif ($defaultPackage)
            $selectPackage = $defaultPackage;
        else {
            $selectPackage = $otherPackage;
        }
        
        $company->licence_expire_on = null;
        $company->status = 'license_expired';
        $company->save();
        
        if ($company->company_email) {
            //$companyUser = auth()->user();
            
             $companyUser = User::join('role_user', 'role_user.user_id', '=', 'users.id')
                            ->join('roles', 'roles.id', '=', 'role_user.role_id')
                            ->select('users.*')
                            ->where('users.company_id', $company->id)
                            ->where('users.email', $company->company_email) ////added by SB
                            ->where('roles.name', 'admin')->first();
             
             if($companyUser) {
                 $companyUser->notify(new LicenseExpire(($companyUser)));
             }
        }
        
        // set defualt package functionality commented no more needed
        // Set default package for license expired companies.
        /*if ($selectPackage) {
            $currentPackage = $company->package;
            ModuleSetting::where('company_id', $company->id)->delete();

            $moduleInPackage = (array) json_decode($selectPackage->module_in_package);
            $clientModules = ['projects', 'tickets', 'invoices', 'estimates', 'events', 'tasks', 'messages', 'payments', 'contracts', 'notices'];
            if ($moduleInPackage) {
                foreach ($moduleInPackage as $module) {

                    if (in_array($module, $clientModules)) {
                        $moduleSetting = new ModuleSetting();
                        $moduleSetting->company_id = $company->id;
                        $moduleSetting->module_name = $module;
                        $moduleSetting->status = 'active';
                        $moduleSetting->type = 'client';
                        $moduleSetting->save();
                    }

                    $moduleSetting = new ModuleSetting();
                    $moduleSetting->company_id = $company->id;
                    $moduleSetting->module_name = $module;
                    $moduleSetting->status = 'active';
                    $moduleSetting->type = 'employee';
                    $moduleSetting->save();

                    $moduleSetting = new ModuleSetting();
                    $moduleSetting->company_id = $company->id;
                    $moduleSetting->module_name = $module;
                    $moduleSetting->status = 'active';
                    $moduleSetting->type = 'admin';
                    $moduleSetting->save();
                }
            }

            if ($currentPackage->default == 'trial' && !is_null($packageSetting) && !is_null($defaultPackage)) {
                $company->package_id = $defaultPackage->id;
                $company->licence_expire_on = null;
            } elseif ($packageSetting && !is_null($trialPackage)) {
                $company->package_id = $selectPackage->id;
                $noOfDays = (!is_null($packageSetting->no_of_days) && $packageSetting->no_of_days != 0) ? $packageSetting->no_of_days : 30;
                $company->licence_expire_on = Carbon::now()->addDays($noOfDays)->format('Y-m-d');
            } elseif (is_null($packageSetting) && !is_null($defaultPackage)) {
                $company->package_id = $defaultPackage->id;
                $company->licence_expire_on = null;
            }
            $company->status = 'license_expired';
            $company->save();

            if ($company->company_email) {
                $companyUser = auth()->user();
                $companyUser->notify(new LicenseExpire(($companyUser)));
            }
        }*/
    }



    public function checkBookingAddOns($company)
    {
        $company->is_booking = 0;
        $company->save();
    }
    public function checkSocialAddOns($company)
    {
        $company->is_social = 0;
        $company->save();
    }
    public function additionalUserExpireOn($company)
    {
        $company->is_additional_user = 0;
        $company->additional_number_users = 0;
        $company->save();
    }

    public function GoogleSettings ($user_id = 0){
        
        if($user_id != 0) {
            $adminSetting = User::where('id', ($user_id))->first();
        } else {
            $adminSetting = User::where('email', ($this->user->email))->first();
        }
        
        $client = false;
        try {
            if(isset($adminSetting->google_token) && !empty($adminSetting->google_token)){
                $client = new Google_Client();
                $client->setAuthConfig('client_secret.json');
                if($client) {
                    // Refresh the token if it's expired.
                    $client->setAccessToken($adminSetting->google_token);
                    
                    if ($client->isAccessTokenExpired()) {
                        $accessToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                        $newAccessToken = $client->getAccessToken();
                        User::where('email', ($this->user->email))
                            ->update(['google_token' => json_encode($newAccessToken)]);
                    }
                }
            }
        } catch (\Exception $e) {
            //var_dump('admin : '.$e->getMessage());exit;
        }
        return $client;
    }
    
    public function getGoogleClient() {

        try {
            $client = new Google_Client();
            
//            $client->setClientId('785362369026-umnphkuublfm522b4gr4hq3plhirp034.apps.googleusercontent.com');
//            $client->setClientSecret('MBK0B8K9eFTH654GTyOy6bwc');
//            $client->setRedirectUri(route('admin.settings.google-oauth'));
//            $client->setScopes('email,profile,https://www.googleapis.com/auth/calendar');
//            $client->setApprovalPrompt("force");
//            $client->setAccessType("offline");
//            $client->setDeveloperKey('AIzaSyDXdYFQ7y_HYacigsJ_QTMqee6zugw0PeY');
            
            $client->setAuthConfig('client_secret.json');
            $client->setApprovalPrompt("force");
            $client->setAccessType('offline');
            $client->setScopes(Google_Service_Calendar::CALENDAR);
            $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
            $client->setHttpClient($guzzleClient);
            
            return $client;
        } catch (\Exception $e) {
            //var_dump($e->getMessage());exit;
            return false;
        }
    }
    
    public function mixPanelTrackEvent (string $name, array $options){
        
        // micpanel tracking
        $options['host'] = 'app.indema.co';
        
        $setting = MixpanelSettings::first();
        $mp = \Mixpanel::getInstance($setting->project_token);
        
        $user = user();
        
        $user_id = encode($user->id,'epgjhev4');
        $mp->people->set($user_id, array(
            '$company_id' => $user->company_id,
            '$email' => $user->email,
            '$company_name' => $user->company->company_name,
            '$company_email' => $user->company->company_email,
            '$hear_about' => $user->company->hear_about
        ));
        // associate user to all subsequent track calls
        $mp->identify($user_id);
        
        $mp->track($name, $options);
    }
}
