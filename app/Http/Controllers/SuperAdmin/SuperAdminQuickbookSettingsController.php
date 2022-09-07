<?php

namespace App\Http\Controllers\SuperAdmin;


use Illuminate\Http\Request;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;

use App\EmployeeDetails;
use App\User;
use App\QuickbooksSettings;

class SuperAdminQuickbookSettingsController extends SuperAdminBaseController
{   
    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'app.menu.quickbooksSettings';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $this->data['qbo'] = QuickbooksSettings::first();
        return view('super-admin.quickbook.index', $this->data);
    }


    public function save_token(Request $request)
    {   

        $qbo_setting = QuickbooksSettings::first();

        if($qbo_setting->baseurl == 1){
            $envoirment = 'Development';
        }else if($qbo_setting->baseurl == 2){
            $envoirment = 'Production';
        }

        $quickbook_conn = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $qbo_setting->client_id,
            'ClientSecret' => $qbo_setting->client_secret,
            'RedirectURI' => $qbo_setting->redirect_url,
            'scope' => "com.intuit.quickbooks.accounting",
            'baseUrl' => $envoirment
        ));

        $OAuth2LoginHelper = $quickbook_conn->getOAuth2LoginHelper();

        if($request->has('code')){

            $accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->input('code'), $request->input('realmId'));
            $quickbook_conn->updateOAuth2Token($accessTokenObj);
            
            //update in quickbook_settings
            QuickbooksSettings::where('id', $qbo_setting->id)
            ->update(['refresh_token' => $accessTokenObj->getRefreshToken(), 'access_token' => $accessTokenObj->getAccessToken(), 'realmid' => $_GET['realmId']]);

            // redirect with alert
            $request->session()->flash('message', 'Quickbooks Connected Successfully.');
            return redirect(route('super-admin.quickbooks-settings.index'));


        }else{
            $authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
            redirect()->to($authorizationCodeUrl)->send();
        }

    }

}
