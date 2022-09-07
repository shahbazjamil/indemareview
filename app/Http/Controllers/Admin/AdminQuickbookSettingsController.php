<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use App\Helper\Reply;

use App\EmployeeDetails;
use App\User;
use App\Calendar;
use App\GoogleSettings;
use App\QuickbooksSettings;
use Illuminate\Support\Facades\DB;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_Calendar;
#use Google_Service_Plus;

class AdminQuickbookSettingsController extends AdminBaseController
{   
    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'app.menu.integrations';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->company = company();
        
        $qbo_company = $this->QuickbookCompany();
        $this->data['admin_settings'] = $user = User::where('email', ($this->user->email))->first();
        if($qbo_company){
            $this->data['qbo_company'] = $qbo_company;
        }
        $this->data['qbo'] = QuickbooksSettings::first();
        $this->data['ggl'] = GoogleSettings::first();
        $client = $this->getGoogleClient();
        $this->data['client'] = $client;
        
        $google_token = false;
        if(!empty($user->google_token)) {
            $google_token = true;
        }
        $this->data['google_token'] = $google_token;
        
        return view('admin.quickbook.index', $this->data);
    }


    // save Quickbook token
    public function save_token(Request $request)
    {  
        $qbo_setting = QuickbooksSettings::first();
        $quickbook_conn = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $qbo_setting->client_id,
            'ClientSecret' =>$qbo_setting->client_secret,
            'RedirectURI' => 'https://app.indema.co/admin/settings/quickbooks-connect',
            'scope' => "com.intuit.quickbooks.accounting",
            'baseUrl' => 'https://quickbooks.api.intuit.com'
        ));

        $OAuth2LoginHelper = $quickbook_conn->getOAuth2LoginHelper();

        if($request->has('code')){

            $accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->input('code'), $request->input('realmId'));


            $quickbook_conn->updateOAuth2Token($accessTokenObj);

            $quickbook_conn->throwExceptionOnError(true);
            $CompanyInfo = $quickbook_conn->getCompanyInfo();
            $nameOfCompany = $CompanyInfo->CompanyName;

            if(isset($nameOfCompany)){

                //check realmId in other user accounts
                $check =  DB::table('users')->where('realmid', $_GET['realmId'])->get()->first();
                if(!empty($check)){
                    if($check->realmid != $_GET['realmId']) {
                        $request->session()->flash('qb_error', 'This Quickbook Account is already connected with other user account.');
                        return redirect(route('admin.quickbooks-settings.index'));
                    }
                }
                
                //update in quickbook_settings
                User::where('email', ($this->user->email))
                ->update(['refresh_token' => $accessTokenObj->getRefreshToken(), 'access_token' => $accessTokenObj->getAccessToken(), 'realmid' => $_GET['realmId']]);

                // redirect with alert
                $request->session()->flash('message', 'Quickbooks Connected Successfully.');
                return redirect(route('admin.quickbooks-settings.index'));
            }
            


        }else{
            $authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
            redirect()->to($authorizationCodeUrl)->send();
        }

    }
    
    // save Google token
    public function google_oauth(Request $request) {
        
        
        try {
            if ($request->has('code')) {
                
                $client = $this->getGoogleClient();
                $client->authenticate($request->get('code'));
                $token = $client->getAccessToken();
                //$client->setAccessToken($token);
                
                
                $google_user = '';
                $google_user_id = '';
                $google_email = '';
                // will check later
//                $plus = new \Google_Service_Plus($client);
//                $google_user = $plus->people->get('me');
//                $google_user_id = $google_user['id'];
//                $google_email = $google_user['emails'][0]['value'];
                //$first_name = $google_user['name']['givenName'];
                //$last_name = $google_user['name']['familyName'];
                
                $cal = new Google_Service_Calendar($client);
                
                $title = 'Indema Calendar';
                $timezone = $this->global->timezone;
                $google_calendar = new Google_Service_Calendar_Calendar($client);
                $google_calendar->setSummary($title);
                $google_calendar->setTimeZone($timezone);
                $created_calendar = $cal->calendars->insert($google_calendar);
                $calendar_id = $created_calendar->getId();
                
                $calendar = Calendar::where('user_id', ($this->user->id))->first();
                if(!$calendar) {
                    $calendar = new Calendar();
                }
                $calendar->user_id = $this->user->id;
                $calendar->title = $title;
                $calendar->calendar_id = $calendar_id;
                $calendar->sync_token = '';
                $calendar->save();
                
                 User::where('email', ($this->user->email))
                ->update(['google_user_id' => $google_user_id, 'google_token' => json_encode($token), 'google_email' => $google_email]);
                
                // redirect with alert
                $request->session()->flash('message', 'Google Calendar Connected Successfully.');
                return redirect(route('admin.quickbooks-settings.index'));
               
            } else {
                
                $request->session()->flash('error_message', 'Something went wrong please try again.');
                return redirect(route('admin.quickbooks-settings.index'));
            }
        } catch (\Exception $e) {
            $request->session()->flash('error_message', 'Something went wrong please try later. '.$e->getMessage());
           return redirect(route('admin.quickbooks-settings.index'));
        }
    }
    
    public function google_disconnect(Request $request) {
         User::where('email', ($this->user->email))
                ->update(['google_user_id' => '', 'google_token' => '', 'google_email' => '']);
         // redirect with alert
        //$request->session()->flash('message', 'Google Calendar Disconnected Successfully.');
        return Reply::success('Google Calendar Disconnected Successfully.');
        //return redirect(route('admin.quickbooks-settings.index'));
    }
}
