<?php

namespace App\Http\Controllers\Admin;

use App\EmailConfiguration;
use App\Helper\Reply;
use App\Http\Requests\UpdateEmailConnectionSettingRequest;
use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EmailConnectionSetting extends AdminBaseController
{
    /**
     * EmailConnectionSetting constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Email Connection Setting';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        //$emailConfiguration = EmailConfiguration::first();
        $emailConfiguration = EmailConfiguration::where('user_id', $this->user->id)->first(); // where condition added by SB
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailConfiguration = EmailConfiguration::where('company_id', $companyId)->first();
        $this->data['emailConfiguration'] = $emailConfiguration;

        return view('admin.email_connection_settings.index', $this->data);
    }

    /**
     * @param UpdateEmailConnectionSettingRequest $request
     *
     * @return array
     *
     */
    public function update(UpdateEmailConnectionSettingRequest $request): array
    {
        $input = $request->all();
        $emailConfiguration = EmailConfiguration::where('user_id', $this->user->id)->first(); // where condition added by SB

        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailConfiguration = EmailConfiguration::where('company_id', $companyId)->first();
        
        if ($emailConfiguration){
            $emailConfiguration->update([
                "user_id"       =>      Auth::id(),
                "company_id"    =>      $companyId,
                "driver"        =>      $input['driver'],
                "host"          =>      $input['host'],
                "port"          =>      $input['port'],
                "encryption"    =>      $input['encryption'],
                "user_name"     =>      $input['user_name'],
                "password"      =>      $input['password'],
                "sender_name"   =>      $input['sender_name'],
                "sender_email"  =>      $input['sender_email'],
            ]);
        }else{
            $emailConfiguration = EmailConfiguration::create([
                "user_id"       =>      Auth::id(),
                "company_id"    =>      $companyId,
                "driver"        =>      $input['driver'],
                "host"          =>      $input['host'],
                "port"          =>      $input['port'],
                "encryption"    =>      $input['encryption'],
                "user_name"     =>      $input['user_name'],
                "password"      =>      $input['password'],
                "sender_name"   =>      $input['sender_name'],
                "sender_email"  =>      $input['sender_email'],
            ]);
        }
        $response = $emailConfiguration->verifySmtpConnection();
        if ($response['success']) {
            //restartPM2();
            return Reply::success(__('messages.emailConnectionSettingUpdated'));
        }

        if(isset($response['message'])) {
            return Reply::error($response['message']);
        }
        return Reply::error('Something wrong with the SMPT settings.');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function testEmail(Request $request): array
    {
        //restartPM2();
        $input = $request->all();
        $emailConfiguration = EmailConfiguration::where('user_id', $this->user->id)->first(); // where condition added by SB
        
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailConfiguration = EmailConfiguration::where('company_id', $companyId)->first();
        $response = $emailConfiguration->verifySmtpConnection();
        if (!$response['success']) {
            if(isset($response['message'])) {
                return Reply::error($response['message']);
            } else {
                return Reply::error('Something wrong with the SMPT settings.');
            }
            
        }
        
        if ($emailConfiguration){
            $request->validate([
                'email' => 'required|email',
            ]);
            $jobData = ['email' => $input['email'],'emailAutomation' => [],'companyId' => $companyId, 'type' => 'test_mail'];
            dispatch(new SendEmailJob($jobData));

            return Reply::success(__('messages.testMailEmailConnectionSetting'));
        }else{
            return Reply::error(__('messages.smtpConfigNotSet'));
        }
    }
}
