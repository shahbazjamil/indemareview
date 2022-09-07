<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Settings\UpdateOrganisationSettings;
use App\Http\Requests\UpdateInvoiceSetting;
use App\InvoiceSetting;
use App\Setting;
use App\Traits\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use App\Helper\Files;

use App\Notifications\OfflinePackageChangeRequest;
use App\Package;
use App\Module;
use App\StripeSetting;
use App\Traits\StripeSettings;
use App\OfflinePaymentMethod;
use App\ModuleSetting;
use Illuminate\Http\Request;
use App\ChargebeeInvoice;
use App\StripeInvoice;
use App\Notifications\CompanyUpdatedPlan;
use App\User;
use Illuminate\Support\Facades\Notification;

class ManageAccountSetupController extends AdminBaseController
{
    use CurrencyExchange;

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.accountSetup';
        $this->pageIcon = 'icon-gear';
    }

    public function index(Request $request) {
        
        
        $invoiceSetting = InvoiceSetting::first();
        if ($this->company->is_final_setup == 1 && $this->company->company_name && $this->company->company_email &&  $this->company->address  && $invoiceSetting->invoice_prefix && $invoiceSetting->estimate_prefix && $invoiceSetting->credit_note_prefix && $invoiceSetting->template && $invoiceSetting->due_after && $invoiceSetting->invoice_terms){
            return Redirect::route('admin.dashboard');
        }
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->currencies = Currency::all();
        $this->dateObject = Carbon::now();
        $this->invoiceSetting = InvoiceSetting::first();
        
        // package data
        
        $this->packages = Package::where('default', 'no')->where('is_private', 0)->where('is_addons', 0)->get();
        $this->modulesData = Module::all();
        $this->stripeSettings = StripeSetting::first();
        $this->offlineMethods = OfflinePaymentMethod::withoutGlobalScope(CompanyScope::class)->whereNull('company_id')->where('status', 'yes')->count();
        
        // END package data
        
        
        // Module data
        
        
        $moduleInPackage = (array)json_decode(company()->package->module_in_package);
        if($request->has('type')){
            if($request->get('type') == 'employee'){
                $this->modulesData = ModuleSetting::where('type', 'employee')->whereIn('module_name', $moduleInPackage)->groupBy('module_name')->get();
                $this->type = 'employee';
            }
            elseif($request->get('type') == 'client'){
                $this->modulesData = ModuleSetting::where('type', 'client')->whereIn('module_name', $moduleInPackage)->groupBy('module_name')->get();
                $this->type = 'client';
            }
        }
        else{
            $this->modulesData = ModuleSetting::where('type', 'admin')->whereIn('module_name', $moduleInPackage)->groupBy('module_name')->get();
            $this->type = 'admin';
        }
        
        // END module data
        
        
        return view('admin.account-setup.edit', $this->data);
        
        
        
//        if($this->company->package_id == '2' && 1!=1) {
//            return view('admin.account-setup.package', $this->data);
//        } else {
//            
//        }
        
    }

    public function update(UpdateOrganisationSettings $request, $id) {
        config(['filesystems.default' => 'user-uploads']);
        
        if($request->input('currency_id')) {
            $currency_id = $request->input('currency_id');
        } else {
            $currency = Currency::first();
            if($currency) {
                $currency_id = $currency->id;
            }
        }
        
        $this->mixPanelTrackEvent('company_setup_completed', array('page_path' => 'admin/account-setup'));
        

        $setting = Company::findOrFail($id);
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->currency_id = $currency_id;
        $setting->timezone = $request->input('timezone');
        $setting->locale = $request->input('locale');
        $setting->date_format = $request->input('date_format');
        $setting->time_format = $request->input('time_format');

        if ($request->hasFile('logo')) {
             $setting->logo = Files::upload($request->logo, 'app-logo');
            //$setting->logo = $request->logo->hashName();
            //$request->logo->store('app-logo');
        }
        $setting->last_updated_by = $this->user->id;

        $setting->save();

        $this->updateExchangeRates();

        return Reply::success(__('messages.settingsUpdated'));
    }

    public function updateInvoice(UpdateInvoiceSetting $request, $id) {
        
        $setting = InvoiceSetting::first();
        $setting->invoice_prefix = $request->invoice_prefix;
        $setting->invoice_digit = $request->invoice_digit;
        $setting->estimate_prefix = $request->estimate_prefix;
        $setting->estimate_digit = $request->estimate_digit;
        $setting->credit_note_prefix = $request->credit_note_prefix;
        $setting->credit_note_digit = $request->credit_note_digit;
        $setting->template       = $request->template;
        $setting->due_after      = $request->due_after;
        $setting->invoice_terms  = $request->invoice_terms;
        $setting->gst_number     = $request->gst_number;
        $setting->show_gst       = $request->has('show_gst') ? 'yes' : 'no';
        $setting->save();
        
         $this->mixPanelTrackEvent('finance_settings_completed', array('page_path' => 'admin/account-setup'));
        
//        $company = Company::findOrFail($this->global->id);
//        $company->is_final_setup = 1;
//        $company->save();
//        
        return Reply::success(__('messages.settingsUpdated'));

        //return Reply::redirect(route('admin.dashboard'), __('messages.settingsUpdated'));
    }
    
    public function completed(Request $request, $id) {
        $company = Company::findOrFail($id);
        $company->is_final_setup = 1;
        $company->save();
        
        $this->mixPanelTrackEvent('customization_completed', array('page_path' => 'admin/account-setup'));
        $this->mixPanelTrackEvent('onboarding_demo_booked', array('page_path' => 'admin/account-setup'));
        $this->mixPanelTrackEvent('onboarding_demo_finished', array('page_path' => 'admin/account-setup'));
        $this->mixPanelTrackEvent('subscription_started', array('page_path' => 'admin/account-setup'));
        
        return Reply::redirect(route('admin.dashboard'), 'The account setup has been completed');
    }
    
    
    
    
    public function chargebeePaymentSubmit(Request $request) {

        $company = company();
        $today = Carbon::now();

        $companyData = Company::findOrFail($company->id);

        if (isset($request->payment_status) && strtolower($request->payment_status) == 'valid') {

            if (strtolower($request->period_unit) == 'year') {
                $package = Package::where('chargebee_annual_plan_id', $request->plan_id)->first();
            } else {
                $package = Package::where('chargebee_monthly_plan_id', $request->plan_id)->first();
            }

            if ($package) {

                $request_data = json_encode($request);

                if (isset($request->customer_id) && !is_null($request->customer_id)) {
                    $company->chargebee_customer_id = $request->customer_id;
                }

                $chargebee_plan_id = $request->plan_id;

                if ($package->is_addons == 1) {

                    // create Chargebee invoice
                    $chargebeeInvoice = new ChargebeeInvoice();
                    $chargebeeInvoice->company_id = $company->id;
                    $chargebeeInvoice->package_id = $package->id;
                    $chargebeeInvoice->transaction_id = $request->invoice_id;
                    $chargebeeInvoice->invoice_id = $request->invoice_id;
                    $chargebeeInvoice->amount = strtolower($request->period_unit) == 'year' ? $package->annual_price : $package->monthly_price;
                    $chargebeeInvoice->pay_date = $today->format('Y-m-d');
                    $chargebeeInvoice->request_data = $request_data;
                    //$chargebeeInvoice->next_pay_date = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') :$today->addMonth()->format('Y-m-d');
                    $chargebeeInvoice->save();

                    // create  Stripe invoice , its created due to default functionality of payment m will check later its affect
                    $stripeInvoice = new StripeInvoice();
                    $stripeInvoice->company_id = $company->id;
                    $stripeInvoice->package_id = $package->id;
                    $stripeInvoice->transaction_id = $request->invoice_id;
                    $stripeInvoice->invoice_id = $request->invoice_id;
                    $stripeInvoice->payment_via = 'chargebee';
                    $stripeInvoice->amount = strtolower($request->period_unit) == 'year' ? $package->annual_price : $package->monthly_price;
                    $stripeInvoice->pay_date = $today->format('Y-m-d');

                    //$stripeInvoice->next_pay_date = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') :$today->addMonth()->format('Y-m-d');
                    $stripeInvoice->save();

                    if($chargebee_plan_id == 'social-free' || $chargebee_plan_id == 'social-starter' || $chargebee_plan_id == 'social-pro'){

                        $company->social_plan_id = 1;
                        if($chargebee_plan_id == 'social-starter') {
                            $company->social_plan_id = 5;
                        }
                        if($chargebee_plan_id == 'social-pro') {
                            $company->social_plan_id = 6;
                        }

                        $company->is_social = 1; // Set booking module status active
                        $company->social_expire_on = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');

                    } else if($chargebee_plan_id == 'additional-user'){

                        $quantity = 1;
                        if(isset($request->quantity) && $request->quantity > 0) {
                            $quantity = $request->quantity;
                        }

                        $company->is_additional_user = 1;
                        $company->additional_user_expire_on = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');
                        $company->additional_number_users = ($company->additional_number_users+$quantity);

                    } else {

                        $company->is_booking = 1; // Set booking module status active
                        $company->booking_expire_on = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');
                    }


                    $company->save();

                    //send superadmin notification
//                    $generatedBy = User::whereNull('company_id')->get();
//                    Notification::send($generatedBy, new CompanyUpdatedPlan($company, $chargebeeInvoice->package_id));
                } else {


                    // create Chargebee invoice
                    $chargebeeInvoice = new ChargebeeInvoice();
                    $chargebeeInvoice->company_id = $company->id;
                    $chargebeeInvoice->package_id = $package->id;
                    $chargebeeInvoice->transaction_id = $request->invoice_id;
                    $chargebeeInvoice->invoice_id = $request->invoice_id;
                    $chargebeeInvoice->amount = strtolower($request->period_unit) == 'year' ? $package->annual_price : $package->monthly_price;
                    $chargebeeInvoice->pay_date = $today->format('Y-m-d');
                    $chargebeeInvoice->next_pay_date = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');
                    $chargebeeInvoice->request_data = $request_data;
                    $chargebeeInvoice->save();

                    // create  Stripe invoice , its created due to default functionality of payment m will check later its affect
                    $stripeInvoice = new StripeInvoice();
                    $stripeInvoice->company_id = $company->id;
                    $stripeInvoice->package_id = $package->id;
                    $stripeInvoice->transaction_id = $request->invoice_id;
                    $stripeInvoice->invoice_id = $request->invoice_id;
                    $stripeInvoice->payment_via = 'chargebee';
                    $stripeInvoice->amount = strtolower($request->period_unit) == 'year' ? $package->annual_price : $package->monthly_price;
                    $stripeInvoice->pay_date = $chargebeeInvoice->pay_date;
                    $stripeInvoice->next_pay_date = $chargebeeInvoice->next_pay_date;
                    $stripeInvoice->save();

                    $company->package_id = $package->id;
                    $company->package_type = strtolower($request->period_unit) == 'year' ? 'annual' : 'monthly';
                    $company->status = 'active'; // Set company status active
                    $company->licence_expire_on = null;
                    //$company->licence_expire_on = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') :$today->addMonth()->format('Y-m-d');
                    $company->save();

                    //send superadmin notification
                    $generatedBy = User::whereNull('company_id')->get();
                    Notification::send($generatedBy, new CompanyUpdatedPlan($company, $chargebeeInvoice->package_id));
                }
                
                $this->mixPanelTrackEvent('trial_started', array('page_path' => 'admin/account-setup', 'plan_type' => $package->name, 'period' => $request->period_unit));

                $request->session()->flash('message', 'Payment successfully done.');

                if($chargebee_plan_id == 'additional-user') {
                    return redirect(route('admin.employees.index'));
                } else if ($companyData->is_final_setup == 1) {
                    return redirect(route('admin.billing'));
                } else {
                    return redirect(route('admin.account-setup.index'));
                }
            }
        }
        
        $request->session()->flash('message', 'Payment failed.');
        
        if ($companyData->is_final_setup == 1) {
            return redirect(route('admin.billing'));
        } else {
            return redirect(route('admin.account-setup.index'));
        }
    }
    
     public function moduleSettingUpdate(Request $request){
         
        
        $setting = ModuleSetting::findOrFail($request->id);

        switch ($setting->type) {
            case "admin":
                if($setting->module_name == 'timelogs' && $request->status == 'active') {
                    if(in_array('tasks', $this->modules) == false) {
                        return Reply::error(__('messages.enableTimeLogModuleMessage'), 'module_dependent');
                    }
                }

                if($setting->module_name == 'tasks' && $request->status == 'deactive') {
                    if(in_array('timelogs', $this->modules)) {
                        return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
                    }
                }
                break;
            case "employee":
                $empoyeeModules = ModuleSetting::where('type', 'employee')->where('status', 'active')->pluck('module_name')->toArray();

                if($setting->module_name == 'timelogs' && $request->status == 'active') {
                    if(in_array('tasks', $empoyeeModules) == false) {
                        return Reply::error(__('messages.enableTimeLogModuleMessage'), 'module_dependent');
                    }
                }

                if($setting->module_name == 'tasks' && $request->status == 'deactive') {
                    if(in_array('timelogs', $empoyeeModules)) {
                        return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
                    }
                }
                break;
            case "client":
                $clientModules = ModuleSetting::where('type', 'client')->where('status', 'active')->pluck('module_name')->toArray();

                if($setting->module_name == 'timelogs' && $request->status == 'active') {
                    if(in_array('tasks', $clientModules) == false) {
                        return Reply::error(__('messages.enableTimeLogModuleMessage'), 'module_dependent');
                    }
                }

                if($setting->module_name == 'tasks' && $request->status == 'deactive') {
//                    if(in_array('timelogs', $clientModules)) {
//                        return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
//                    }
                }
                break;
            default:
                return Reply::error(__('messages.disableTasksModuleMessage'), 'module_dependent');
        }


        $setting->status = $request->status;
        $setting->save();
        
        $this->mixPanelTrackEvent('customization_switch_change', array('page_path' => 'admin/account-setup'));
        
        ModuleSetting::where('module_name', '=', $setting->module_name)->where('company_id', '=', $setting->company_id)->update(array('status' =>  $request->status));

        return Reply::success(__('messages.settingsUpdated'));
    }
    
}
