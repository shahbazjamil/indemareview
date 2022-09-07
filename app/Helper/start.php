<?php

use App\AuditTrail;
use App\Company;
use App\EmailAutomation;
use App\EmailConfiguration;
use App\EmailAutomationMaster;
use App\EmailTemplate;
use App\FileStorage;
use App\Invoice;
use App\Jobs\SendEmailJob;
use App\Mail\FirstStepAutomation;
use App\Mail\LastStepAutomation;
use App\Mail\PaymentReceived;
use App\Mail\ProjectEndDate;
use App\Mail\ProjectStartDate;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\User;

if (!function_exists('superAdmin')) {
    function superAdmin()
    {
        if (session()->has('user')) {
            return session('user');
        }

        $user = auth()->user();

        if ($user) {
            session(['user' => $user]);
            return session('user');
        }

        return null;
    }
}

if (!function_exists('user')) {
    function user()
    {
        if (session()->has('user')) {
            return session('user');
        }
        $user = auth()->user();

        if ($user) {
            session(['user' => $user]);
            return session('user');
        }

        return null;
    }
}

if (!function_exists('company')) {
    function company()
    {

        if (session()->has('company')) {
            return session('company');
        }

        if (auth()->user()) {
            $companyId = auth()->user()->company_id;
            if (!is_null($companyId)) {
                $company = \App\Company::find($companyId);
                session(['company' => $company]);
            }
            return session('company');
        }

        return false;
    }
}

if (!function_exists('company_initials')) {
    function company_initials()
    {
        if (session()->has('company')) {
            $company =  session('company');
        }

        if (auth()->user()) {
            $companyId = auth()->user()->company_id;
            if (!is_null($companyId)) {
                $company = \App\Company::find($companyId);
            }
        }
        
        $company_name  = $company->company_name; 
        $words = explode(" ", $company_name);
        $initials = '';
        foreach ($words as $w) {
            $initials .= $w[0];    
        }
        
        return strtoupper($initials);
        
    }
}

if (!function_exists('asset_url')) {

    // @codingStandardsIgnoreLine
    function asset_url($path)
    {
        if (config('filesystems.default') == 's3') {
            //            return "https://" . config('filesystems.disks.s3.bucket') . ".s3.amazonaws.com/".$path;
        }

        $path = 'user-uploads/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }
}

if (!function_exists('worksuite_plugins')) {

    function worksuite_plugins()
    {

        if (!session()->has('worksuite_plugins')) {
            $plugins = \Nwidart\Modules\Facades\Module::allEnabled();
            // dd(array_keys($plugins));

            // foreach ($plugins as $plugin) {
            //     Artisan::call('module:migrate', array($plugin, '--force' => true));
            // }

            session(['worksuite_plugins' => array_keys($plugins)]);
        }
        return session('worksuite_plugins');
    }
}

if (!function_exists('isSeedingData')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isSeedingData()
    {
        // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
        return config('app.seeding');
    }
}
if (!function_exists('isRunningInConsoleOrSeeding')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isRunningInConsoleOrSeeding()
    {
        // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
        return app()->runningInConsole() || isSeedingData();
    }
}


if (!function_exists('asset_url_local_s3')) {

    // @codingStandardsIgnoreLine
    function asset_url_local_s3($path)
    {
        if (config('filesystems.default') == 's3') {
            return "https://" . config('filesystems.disks.s3.bucket') . ".s3.amazonaws.com/" . $path;
        }

        $path = 'user-uploads/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }
}

if (!function_exists('download_local_s3')) {

    // @codingStandardsIgnoreLine
    function download_local_s3($file, $path)
    {
        if (config('filesystems.default') == 's3') {
            $ext = pathinfo($file->filename, PATHINFO_EXTENSION);
            $fs = Storage::getDriver();
            $stream = $fs->readStream($path);

            return Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $ext,
                "Content-Length" => $file->size,
                "Content-disposition" => "attachment; filename=\"" . basename($file->filename) . "\"",
            ]);
        }

        $path = 'user-uploads/' . $path;
        return response()->download($path, $file->filename);
    }
}
if (!function_exists('module_enabled')) {
    function module_enabled($moduleName)
    {
        return \Nwidart\Modules\Facades\Module::collections()->has($moduleName);
    }
}

if (!function_exists('getDomainSpecificUrl')) {
    function getDomainSpecificUrl($url, $company = false)
    {
        if (module_enabled('Subdomain')) {
            // If company specific

            if ($company) {
                $url = str_replace(request()->getHost(), $company->sub_domain, $url);
                $url = str_replace('www.', '', $url);
                // Replace https to http for sub-domain to
                return $url = str_replace('https', 'http', $url);
            }

            // If there is no company and url has login means
            // New superadmin is created
            return $url = str_replace('login', 'super-admin-login', $url);
        }

        return $url;
    }
}

if (!function_exists('getSubdomainSchema')) {
    function getSubdomainSchema()
    {

        if (!session()->has('subdomain_schema')) {
            if (\Illuminate\Support\Facades\Schema::hasTable('sub_domain_module_settings')) {
                $data = \Illuminate\Support\Facades\DB::table('sub_domain_module_settings')->first();
            }
            session(['subdomain_schema' => isset($data->schema) ? $data->schema : 'http']);
        }
        return session('subdomain_schema');
    }
}

if (!function_exists('global_settings')) {

    function global_settings()
    {
//        if (!session()->has('global_settings')) {
        session(['global_settings' => \App\GlobalSetting::with('currency')->first()]);
//        }

        return session('global_settings');
    }
}

if (!function_exists('company_setting')) {

    function company_setting()
    {
        if (!session()->has('company_setting')) {
            session(['company_setting' => \App\Company::with('currency', 'package')->withoutGlobalScope('active')->where('id', auth()->user()->company_id)->first()]);
        }

        return session('company_setting');
    }
}

if (!function_exists('check_migrate_status')) {

    function check_migrate_status()
    {
//        if (!session()->has('check_migrate_status')) {

        $status = Artisan::call('migrate:check');

        if ($status && !request()->ajax()) {
            Artisan::call('migrate', array('--force' => true)); //migrate database
            Artisan::call('optimize:clear');
        }
//            session(['check_migrate_status' => true]);
//        }

//        return session('check_migrate_status');
    }
}

if (!function_exists('time_log_setting')) {

    function time_log_setting()
    {
        if (!session()->has('time_log_setting')) {
            session(['time_log_setting' => \App\LogTimeFor::first()]);
        }

        return session('time_log_setting');
    }
}

if (!function_exists('package_setting')) {

    function package_setting()
    {
        if (!session()->has('package_setting')) {
            session(['package_setting' => \App\PackageSetting::first()]);
        }

        return session('package_setting');
    }
}

if (!function_exists('invoice_setting')) {

    function invoice_setting()
    {
        if (!session()->has('invoice_setting')) {
            session(['invoice_setting' => \App\InvoiceSetting::first()]);
        }

        return session('invoice_setting');
    }
}

if (!function_exists('language_setting')) {

    function language_setting()
    {
        if (!session()->has('language_setting')) {
            session(['language_setting' => \App\LanguageSetting::where('status', 'enabled')->get()]);
        }

        return session('language_setting');
    }
}

if (!function_exists('push_setting')) {

    function push_setting()
    {
        if (!session()->has('push_setting')) {
            session(['push_setting' => \App\PushNotificationSetting::first()]);
        }

        return session('push_setting');
    }
}

if (!function_exists('admin_theme')) {

    function admin_theme()
    {
        if (!session()->has('admin_theme')) {
            session(['admin_theme' => \App\ThemeSetting::where('panel', 'admin')->first()]);
        }

        return session('admin_theme');
    }
}

if (!function_exists('employee_theme')) {

    function employee_theme()
    {
        if (!session()->has('employee_theme')) {
            session(['employee_theme' => \App\ThemeSetting::where('panel', 'employee')->first()]);
        }

        return session('employee_theme');
    }
}

if (!function_exists('superadmin_theme')) {

    function superadmin_theme()
    {
        if (!session()->has('superadmin_theme')) {
            session(['superadmin_theme' => \App\ThemeSetting::where('panel', 'superadmin')->first()]);
        }

        return session('superadmin_theme');
    }
}

if (!function_exists('storage_setting')) {

    function storage_setting()
    {
        if (!session()->has('storage_setting')) {
            session(['storage_setting' => \App\StorageSetting::where('status', 'enabled')
                ->first()]);
        }
        return session('storage_setting');
    }
}

if (!function_exists('user_modules')) {
    function user_modules()
    {
        $user = auth()->user();
        $user_modules = $user->modules;

        if ($user) {
            session(['user_modules' => $user_modules]);
            return session('user_modules');
        }

        return null;
    }
}

if (!function_exists('get_domain')) {

    function get_domain($host = false)
    {
        if (!$host) {
            $host = $_SERVER['SERVER_NAME'];
        }

        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if ($count === 2) {
            if (strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
        } else if ($count > 2) {
            $myhost = get_domain(explode('.', $myhost, 2)[1]);
        }
        return $myhost;
    }
}

if (!function_exists('global_currency_position')) {

    function global_currency_position($currency_symbol)
    {

        if (!session()->has('global_currency_position')) {
            $currency = \App\GlobalCurrency::where('currency_symbol', $currency_symbol)->first();

            session(['global_currency_position' => !is_null($currency) ? $currency->currency_position : null]);
        }
        return session('global_currency_position');
    }
}

if (!function_exists('company_currency_position')) {

    function company_currency_position($currency_symbol)
    {

        if (!session()->has('company_currency_position')) {
            $currency = \App\Currency::where('currency_symbol', $currency_symbol)->first();
            session(['company_currency_position' => !is_null($currency) ? $currency->currency_position : null]);
        }

        return session('company_currency_position');
    }
}

if (!function_exists('currency_position')) {

    function currency_position($amount = null, $symbol = null)
    {
        $position = 'front';

        if (!is_null(company_currency_position($symbol)) && company()) {
            $position = company_currency_position($symbol);

        } elseif (!is_null(company_currency_position($symbol))) {
            $position = global_currency_position($symbol);
        }

        // FOR PRICING PAGE
        if (is_null($amount)) {
            return $position;
        }
        return ($position == 'front') ? ($symbol . $amount) : ($amount . $symbol);
    }
}

if (!function_exists('can_upload')) {

    function can_upload($size = 0)
    {

        session()->forget(['company_setting', 'company']);
        $user = auth()->user()->super_admin;
        if (!$user == '1') {
            // Return true for unlimited file storage
            if (company()->package->max_storage_size == -1) {
                return true;
            }

            // Total Space in package in MB
            $totalSpace = (company()->package->storage_unit == 'mb') ? company()->package->max_storage_size : company()->package->max_storage_size * 1024;

            // Used space in mb
            $fileStorage = FileStorage::all();
            $usedSpace = $fileStorage->count() > 0 ? round($fileStorage->sum('size') / (1000 * 1024), 4) : 0;

            $remainingSpace = $totalSpace - $usedSpace;

            if ($usedSpace > $totalSpace || $size > $remainingSpace) {
                return false;
            }

            return false;
        }
    }
}

// Added By SB

if (!function_exists('contract_variable_replacer')) {

    function contract_variable_replacer($contract_id) {
        
       // $fileStorage = Contract::whereRaw('md5(id) = ?', $contract_id);
        
        $contract = \App\Contract::where('id', $contract_id)->first();
        
        $client_detail = \App\ClientDetails::where('user_id', $contract->client_id)->first();

        $contract_detail_raw =$contract->contract_detail;
        $replacers = [
            '{{client.name}}' => $client_detail->name,
            '{{client.email}}' => $client_detail->email,
            '{{client.mobile}}' => $client_detail->mobile,
            '{{client.company_name}}' => $client_detail->company_name,
            '{{client.address}}' => $client_detail->address,
            '{{client.shipping_address}}' => $client_detail->shipping_address,
            '{{client.website}}' => $client_detail->website,
            '{{client.gst_number}}' => $client_detail->gst_number,
            '{{designer.name}}' => $contract->user->name,
            '{{designer.email}' => $contract->user->email
        ];

        $contract_detail = str_replace(
                array_keys($replacers), array_values($replacers), $contract_detail_raw
        );

        return $contract_detail;
    
    }

}

if (!function_exists('company_setting_by_id')) {
    function company_setting_by_id($company_id)
    {
        $company_setting = \App\Company::with('currency', 'package')->withoutGlobalScope('active')->where('id', $company_id)->first();
        return $company_setting;
    }
}

if(!function_exists('encode')){
    function encode($string,$key) {
        $key = sha1($key);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $j = 0;
        $hash = '';
        for ($i = 0; $i < $strLen; $i++) {
            $ordStr = ord(substr($string,$i,1));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key,$j,1));
            $j++;
            $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
        }
        return $hash;
    }
}

if(!function_exists('decode')){
    function decode($string,$key) {
        $key = sha1($key);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        for ($i = 0; $i < $strLen; $i+=2) {
            $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key,$j,1));
            $j++;
            $hash .= chr($ordStr - $ordKey);
        }
        return $hash;
    }
}



if (!function_exists('getMailAutomationTimePeriod')) {
    /**
     * @param $emailAutomation
     *
     * @return string
     */
    function getMailAutomationTimePeriod($emailAutomation): string
    {
        $timeType = '';
        if ($emailAutomation['time_unit'] == 'minute'){
            if($emailAutomation['time_type'] == 'after'){
                $timeType = 'minuteAfter';
            }else{
                $timeType = 'minuteBefore';
            }
        }else if($emailAutomation['time_unit'] == 'hour'){
            if($emailAutomation['time_type'] == 'after'){
                $timeType = 'hourAfter';
            }else{
                $timeType = 'hourBefore';
            }
        }else if($emailAutomation['time_unit'] == 'day'){
            if($emailAutomation['time_type'] == 'after'){
                $timeType = 'dayAfter';
            }else{
                $timeType = 'dayBefore';
            }
        }else if($emailAutomation['time_unit'] == 'week'){
            if($emailAutomation['time_type'] == 'after'){
                $timeType = 'weekAfter';
            }else{
                $timeType = 'weekBefore';
            }
        }

        return $timeType;
    }
}

if (!function_exists('projectStartAutomationMail')) {
    /**
     * @param $emailAutomations
     *
     * @param $project
     *
     * @param $timePeriod
     *
     * @param $projectStartDateTime
     *
     * @return array
     */
    function projectStartAutomationMail($emailAutomations, $project, $timePeriod, $projectStartDateTime): array
    {
        foreach ($emailAutomations as $key => $emailAutomation) {
            $mailType = getMailAutomationTimePeriod($emailAutomation);
            $company =  company()->toArray();
            $companyId = !empty($company) ? $company['id'] : null;
            $emailAutomation['company'] = null;
            if (!empty($companyId)){
                $emailAutomation['company'] = Company::find($companyId)->toArray();
            }
            $timePeriod = $timePeriod + intval($emailAutomation['time_period']);
            if ($mailType == 'minuteAfter') {
                $projectStartDateTime = $projectStartDateTime->addMinutes($timePeriod);
                foreach ($project->clients as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                            }
                        }
                    }
                }
            } else if ($mailType == 'minuteBefore') {
                $projectStartDateTime = $projectStartDateTime->subMinutes($timePeriod);
                foreach ($project->clients as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                            }
                        }
                    }
                }
            } else if ($mailType == 'hourAfter') {
                $projectStartDateTime = $projectStartDateTime->addHours($timePeriod);
                foreach ($project->clients as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                            }
                        }
                    }
                }
            } else if ($mailType == 'hourBefore') {
                $projectStartDateTime = $projectStartDateTime->subHours($timePeriod);
                foreach ($project->clients as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($projectStartDateTime);
                            }
                        }
                    }
                }
            } else if ($mailType == 'dayAfter') {
                $project->start_date = $project->start_date->addDays($timePeriod);
                foreach ($project->members as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                            }
                        }
                    }
                }
            } else if ($mailType == 'dayBefore') {
                $project->start_date = $project->start_date->subDays($timePeriod);
                foreach ($project->clients as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                            }
                        }
                    }
                }
            } else if ($mailType == 'weekAfter') {
                $project->start_date = $project->start_date->addWeeks($timePeriod);
                foreach ($project->clients as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                            }
                        }
                    }
                }
            } else if ($mailType == 'weekBefore') {
                $project->start_date = $project->start_date->subWeeks($timePeriod);
                foreach ($project->clients as $client) {
                    $user = User::where('id', $client->client_id)->first();
                    if ($user) {
                        $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_start'];
                        if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                            $jobData['type'] = 'last_step';
                            dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                        } else {
                            if ($emailAutomation['automation_event'] == EmailAutomation::START_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                dispatch(new SendEmailJob($jobData))->delay($project->start_date);
                            }
                        }
                    }
                }
            }
        }

        return [
            'timePeriod' => $timePeriod,
            'projectStartDateTime' => $projectStartDateTime,
            'projectStartDate' => $project->start_date,
        ];
    }
}

if (!function_exists('projectEndAutomationMail')) {
    /**
     * @param $emailAutomations
     *
     * @param $project
     *
     * @param $timePeriod
     *
     * @param $projectDeadLineTime
     *
     * @return array
     */
    function projectEndAutomationMail($emailAutomations, $project, $timePeriod, $projectDeadLineTime): array
    {
        foreach ($emailAutomations as $key => $emailAutomation) {
            $mailType = getMailAutomationTimePeriod($emailAutomation);
            $company =  company()->toArray();
            $companyId = !empty($company) ? $company['id'] : null;
            $emailAutomation['company'] = null;
            if (!empty($companyId)){
                $emailAutomation['company'] = Company::find($companyId)->toArray();
            }
            $timePeriod = $timePeriod + intval($emailAutomation['time_period']);
            if ($mailType == 'minuteAfter') {
                if ($project->deadline_time != null) {
                    $projectDeadLineTime = $projectDeadLineTime->addMinutes($timePeriod);
                    foreach ($project->clients as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                                }
                            }
                        }
                    }
                }
            } else if ($mailType == 'minuteBefore') {
                if ($project->deadline_time != null) {
                    $projectDeadLineTime = $projectDeadLineTime->subMinutes($timePeriod);
                    foreach ($project->clients as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                                }
                            }
                        }
                    }
                }
            } else if ($mailType == 'hourAfter') {
                if ($project->deadline_time != null) {
                    $projectDeadLineTime = $projectDeadLineTime->addHours($timePeriod);
                    foreach ($project->clients as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                                }
                            }
                        }
                    }
                }
            } else if ($mailType == 'hourBefore') {
                if ($project->deadline_time != null) {
                    $projectDeadLineTime = $projectDeadLineTime->subHours($timePeriod);
                    foreach ($project->clients as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($projectDeadLineTime);
                                }
                            }
                        }
                    }
                }
            } else if ($mailType == 'dayAfter') {
                if ($project->deadline != null) {
                    $project->deadline = $project->deadline->addDays($timePeriod);
                    foreach ($project->members as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                                }
                            }
                        }
                    }
                }
            } else if ($mailType == 'dayBefore') {
                if ($project->deadline != null) {
                    $project->deadline = $project->deadline->subDays($timePeriod);
                    foreach ($project->clients as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                                }
                            }
                        }
                    }
                }
            } else if ($mailType == 'weekAfter') {
                if ($project->deadline != null) {
                    $project->deadline = $project->deadline->addWeeks($timePeriod);
                    foreach ($project->clients as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                                }
                            }
                        }
                    }
                }
            } else if ($mailType == 'weekBefore') {
                if ($project->deadline != null) {
                    $project->deadline = $project->deadline->subWeeks($timePeriod);
                    foreach ($project->clients as $client) {
                        $user = User::where('id', $client->client_id)->first();
                        if ($user) {
                            $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'project_end'];
                            if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                                $jobData['type'] = 'last_step';
                                dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                            } else {
                                if ($emailAutomation['automation_event'] == EmailAutomation::END_PROJECT && $emailAutomation['is_manual'] == EmailAutomation::IS_AUTOMATIC) {
                                    dispatch(new SendEmailJob($jobData))->delay($project->deadline);
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'timePeriod' => $timePeriod,
            'projectDeadLineTime' => $projectDeadLineTime,
            'projectDeadLine' => $project->deadline,
        ];
    }
}

if (!function_exists('getGroupItems')) {

    function getGroupItems($data) {
        
       
        
        $groups = array();
        foreach ($data as $item) {
            
            $key = random_int(100000, 999999);
            $is_group = 0;
            if(!empty($item->group_id)) {
                 $key = $item->group_id;
                 $is_group = 1;
            }
           
            if (!array_key_exists($key, $groups)) {
                
                $product_url = '';
                if($item->picture != '') {
                    $product_url = asset('user-uploads/products/'.$item->product_id.'/'.$item->picture.'');
                } else if($item->invoice_item_type == 'product') {
                    if($item->product_url == 'https://app.indema.co/img/img-dummy.jpg') {
                        $product_url = asset('img/default-product.png');
                    } else {
                        $product_url = $item->product_url;
                    }
                }
                
                $groups[$key] = array(
                    'id' => $item->id,
                    'group_id' => $item->group_id,
                    'is_group' => $is_group,
                    'item_name' => $item->item_name,
                    'item_summary' => $item->item_summary,
                    //'group_name' => (isset($item->group_id) && isset($item->group))?$item->group->group_name:'',
                    'group_name' => $item->item_name,
                    'product_url' => $product_url,
                    'status' => $item->status,
                    //'quantity' => $item->quantity,
                    'quantity' => 1,
                    'sale_price' => $item->sale_price,
                    'shipping_price' => $item->shipping_price,
                    'amount' => $item->amount,
                    
                );
            } else {
                //$groups[$key]['quantity'] = $groups[$key]['quantity'] + $item['quantity'];
                $groups[$key]['sale_price'] = $groups[$key]['sale_price'] + $item['sale_price'];
                $groups[$key]['shipping_price'] = $groups[$key]['shipping_price'] + $item['shipping_price'];
                $groups[$key]['amount'] = $groups[$key]['amount'] + $item['amount'];
            }
        }
        return $groups;
    }
}

if (!function_exists('paymentReceivedAutomationMail')) {
    /**
     * @param $user
     */
    function paymentReceivedAutomationMail($user)
    {
        $timePeriod = 0;
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailAutomations = EmailAutomation::where('company_id', $companyId)->where('automation_event', EmailAutomation::PAYMENT_RECEIVED)->get();
        if($emailAutomations && !empty($user)) {
            $emailMasterAutomationIds = $emailAutomations->pluck('email_automation_id')->toArray();
            $getAllEmailAutomations = EmailAutomation::where('company_id', $companyId)->with(['emailTemplate', 'emailAutomationMaster'])
                                        ->whereIn('email_automation_id', $emailMasterAutomationIds)
                                        ->orderBy('step')
                                        ->get();
            $getAllEmailAutomations = $getAllEmailAutomations->toArray();
            foreach ($getAllEmailAutomations as $key => $emailAutomation) {
                $mailType = getMailAutomationTimePeriod($emailAutomation);
                $company = company()->toArray();
                $companyId = !empty($company) ? $company['id'] : null;
                $emailAutomation['company'] = null;
                if (!empty($companyId)) {
                    $emailAutomation['company'] = Company::find($companyId)->toArray();
                }
                $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'payment_received'];
                $timePeriod = $timePeriod + intval($emailAutomation['time_period']);
                if ($mailType == 'minuteAfter') {
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                        $jobData['type'] = 'last_step';
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addMinutes($timePeriod));
                    } else {
                        if ($emailAutomation['automation_event'] == EmailAutomation::PAYMENT_RECEIVED) {
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addMinutes($timePeriod));
                        }
                    }
                } else if ($mailType == 'hourAfter') {
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                        $jobData['type'] = 'last_step';
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addHours($timePeriod));
                    } else {
                        if ($emailAutomation['automation_event'] == EmailAutomation::PAYMENT_RECEIVED) {
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addHours($timePeriod));
                        }
                    }
                } else if ($mailType == 'dayAfter') {
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                        $jobData['type'] = 'last_step';
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addDays($timePeriod));
                    } else {
                        if ($emailAutomation['automation_event'] == EmailAutomation::PAYMENT_RECEIVED) {
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addDays($timePeriod));
                        }
                    }
                } else if ($mailType == 'weekAfter') {
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION) {
                        $jobData['type'] = 'last_step';
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addWeeks($timePeriod));
                    } else {
                        if ($emailAutomation['automation_event'] == EmailAutomation::PAYMENT_RECEIVED) {
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addWeeks($timePeriod));
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('getAdminUser')) {
    /**
     * @return mixed
     */
    function getAdminUser()
    {
        $user = User::where('email', 'timothy1@indema.co')->first();
        if ($user){
            return $user;
        }else{
            $user = User::firstAdmin();

            return  $user;
        }
    }
}

if (!function_exists('storeAuditTrail')) {
    /**
     * @param $emailAutomation
     *
     * @param $companyId
     *
     * @param $user
     *
     * @param $type
     *
     * @param $date
     *
     * @param $step
     */
    function storeAuditTrail($emailAutomation, $companyId, $user, $type , $date, $step)
    {
        if($step == AuditTrail::LEFT_STEP){
            foreach ($emailAutomation as $value){
                $getAutomationMaster = EmailAutomationMaster::find($value);
                $title = AuditTrail::TITLE[$step];
                $title = str_replace('automation_name', $getAutomationMaster->name, $title);
                if ($type == AuditTrail::LEAD){
                    AuditTrail::create([
                        'lead_id' => $user->id,
                        'company_id' => $companyId,
                        'type' => $type,
                        'title' => $title,
                        'icon' => 'hourglass-start orange',
                        'deliver_at' => $date,
                    ]);
                }else{
                    AuditTrail::create([
                        'client_id' => $user->id,
                        'company_id' => $companyId,
                        'type' => $type,
                        'title' => $title,
                        'icon' => 'hourglass-start orange',
                        'deliver_at' => $date,
                    ]);
                }
            }
        }elseif ($step == AuditTrail::ENTERED_STEP){
            $getAutomationMaster = EmailAutomationMaster::find($emailAutomation['email_automation_id']);
            $title = AuditTrail::TITLE[$step];
            $title = str_replace('email_type', EmailAutomation::EMAIL_TYPE[$emailAutomation['email_type']], $title);
            $title = str_replace('automation_name', $getAutomationMaster->name, $title);
            if ($type == AuditTrail::LEAD){
                AuditTrail::create([
                    'lead_id' => $user->id,
                    'company_id' => $companyId,
                    'email_automation_id' => $emailAutomation['id'],
                    'type' => $type,
                    'title' => $title,
                    'icon' => 'envelope green',
                    'deliver_at' => $date,
                ]);
            }else{
                AuditTrail::create([
                    'client_id' => $user->id,
                    'company_id' => $companyId,
                    'email_automation_id' => $emailAutomation['id'],
                    'type' => $type,
                    'title' => $title,
                    'icon' => 'envelope green',
                    'deliver_at' => $date,
                ]);
            }
        }elseif ($step == AuditTrail::RECEIVED_STEP){
            $getEmailTemplate = EmailTemplate::find($emailAutomation['email_template_id']);
            $getAutomationMaster = EmailAutomationMaster::find($emailAutomation['email_automation_id']);
            $title = AuditTrail::TITLE[$step];
            $title = str_replace('email_subject', $getEmailTemplate->subject, $title);
            $title = str_replace('automation_name', $getAutomationMaster->name, $title);
            if ($type == AuditTrail::LEAD){
                $audit = AuditTrail::create([
                    'lead_id' => $user->id,
                    'company_id' => $companyId,
                    'email_automation_id' => $emailAutomation['id'],
                    'type' => $type,
                    'title' => $title,
                    'icon' => 'envelope green',
                    'deliver_at' => $date,
                ]);
                return $audit;
            }else{
                $audit = AuditTrail::create([
                    'client_id' => $user->id,
                    'company_id' => $companyId,
                    'email_automation_id' => $emailAutomation['id'],
                    'type' => $type,
                    'title' => $title,
                    'icon' => 'envelope green',
                    'deliver_at' => $date,
                ]);
                return $audit;
            }
        } elseif ($step == AuditTrail::OPENED_STEP){
            $getEmailTemplate = EmailTemplate::find($emailAutomation['email_template_id']);
            $getAutomationMaster = EmailAutomationMaster::find($emailAutomation['email_automation_id']);
            $title = AuditTrail::TITLE[$step];
            $title = str_replace('email_subject', $getEmailTemplate->subject, $title);
            $title = str_replace('automation_name', $getAutomationMaster->name, $title);
            if ($type == AuditTrail::LEAD){
                AuditTrail::create([
                    'lead_id' => $user->id,
                    'company_id' => $companyId,
                    'email_automation_id' => $emailAutomation['id'],
                    'type' => $type,
                    'title' => $title,
                    'icon' => 'envelope green',
                    'deliver_at' => $date,
                ]);
            }else{
                AuditTrail::create([
                    'client_id' => $user->id,
                    'company_id' => $companyId,
                    'email_automation_id' => $emailAutomation['id'],
                    'type' => $type,
                    'title' => $title,
                    'icon' => 'envelope green',
                    'deliver_at' => $date,
                ]);
            }
        }
    }
}

if (!function_exists('restartPM2'))
{
    function restartPM2()
    {
        exec('pm2 restart 0', $output, $return);
    }
}


if (!function_exists('cal_percentage')) {

    function cal_percentage($num_amount, $num_total) {
        $count1 = $num_amount / $num_total;
        $count2 = $count1 * 100;
        //$count = number_format((float)$count2, 2, '.', '');
        $count = floor($count2);
        return $count;
    }
}
