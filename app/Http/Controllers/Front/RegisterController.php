<?php

namespace App\Http\Controllers\Front;

use App\Company;
use App\Helper\Reply;
use App\Http\Requests\Front\Register\StoreRequest;
use App\Notifications\EmailVerificationSuccess;
use App\Role;
use App\SeoDetail;
use App\User;
use App\Package;
use App\GlobalSetting;
use App\StripeInvoice;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use Carbon\Carbon;

class RegisterController extends FrontBaseController
{
    public function index(Request $request)
    {    

        if (\user()) {
            return redirect(getDomainSpecificUrl(route('login'), \user()->company));
        }
        
        $plan_name = 'SINGLE';
        $is_annual = 0;
        
        if(isset($request->plan) && !empty($request->plan)) {
           if($request->plan == 'single_annual') {
               $plan_name = 'SINGLE';
               $is_annual = 1;
           } else if ($request->plan == 'group' ){
               $plan_name = 'GROUP';
               $is_annual = 0;
           } else if ($request->plan == 'group_annual' ){
               $plan_name = 'GROUP';
               $is_annual = 1;
           } else if ($request->plan == 'hub' ){
               $plan_name = 'HUB';
               $is_annual = 0;
               
           } else if ($request->plan == 'hub_annual' ){
               $plan_name = 'HUB';
               $is_annual = 1;
           }
        } else {
            header("Location: https://indema.co/plans/");
            exit();
        }
        
        $package = Package::where('name', '=' , $plan_name)->first();
        if(!$package) {
            header("Location: https://indema.co/plans/");
            exit();
        }
        
        
        $this->seoDetail = SeoDetail::where('page_name', 'home')->first();
        $this->pageTitle = 'Sign Up';

        $view = ($this->setting->front_design == 1) ? 'saas.register' : 'front.register';
                
        $this->intent = (new Company)->createSetupIntent();
        $this->package = $package;
        $this->is_annual = $is_annual;
        
        $this->due_date = Carbon::now()->addDays(15)->format('F d, Y');
        $this->plan = $request->plan;
        
        return view($view, $this->data);
    }

    //public function store(StoreRequest $request)
    
    
    public function store(Request $request)
    {
        $global = GlobalSetting::first();
        $redirectRoute = route('front.signup.index').'?plan='.$request->plan;
        Stripe::setApiKey(config('cashier.secret'));
        
        $rules = [
            'company_name' => 'required',
            'company_email' => 'required|email|unique:companies',
            'sub_domain' => module_enabled('Subdomain') ? 'required|min:4|unique:companies,sub_domain|max:50|sub_domain' : '',
            'password' => 'required||confirmed',
            'password_confirmation' => 'required',
            'accept' => 'required',
            'indema_terms' => 'required',
            'cancel_subscription' => 'required',
            //'pricing_automatically' => 'required',
            'hear_about' => 'required',
            'stripeToken' => 'required',
            //'card_holder_name' => 'required',
            'is_annual' => 'required',
            'package_id' => 'required',
        ];
        
//        if (!is_null($global->google_recaptcha_key)) {
//            $rules['g-recaptcha-response'] = 'required';
//        }
        
       

        $user = User::where('users.email', $request->company_email)->first();
        if ($user) {
            $user->hasRole('employee') ?  $rules['company_email'] = 'required|email|unique:users,email' : '';
        }
        
        $this->validate($request, $rules);
        
        $coupon_code = '';
        if(isset($request->coupon) && !empty($request->coupon)) {
            
            try {
                $coupon = \Stripe\Coupon::retrieve($request->coupon);
                if (!$coupon->valid) {
                    \Session::put('error',"No such Promo code : '$request->coupon'");
                    return redirect($redirectRoute);
                }
                $coupon_code = $request->coupon;
             } catch(\Exception $e) {
                 \Session::put('error',$e->getMessage());
                return redirect($redirectRoute);
             }
        }
        
        
        $company = new Company();
        $request->email = $request->company_email;
        $request->lm_data = $request->lm_data?$request->lm_data:null;
        

//        if (!$company->recaptchaValidate($request)) {
//             \Session::put('error','Recaptcha not validated.');
//            return redirect($redirectRoute);
//            
//        }
        
        $package = Package::where('id', '=' , $request->package_id)->first();
        if (!$package) {
             \Session::put('error','Plan not validated.');
            return redirect($redirectRoute);
        }
        
        $is_annual = $request->is_annual ? $request->is_annual : 0;
        $plan_id = $package->stripe_monthly_plan_id;
        if($is_annual == 1) {
            $plan_id = $package->stripe_annual_plan_id;
        }
        
        DB::beginTransaction();
        try {
            $company->company_name = $request->company_name;
            $company->company_email = $request->company_email;
            $company->hear_about = $request->hear_about;
            
            $company->lm_data = $request->lm_data;

            if (module_enabled('Subdomain')) {
                $company->sub_domain = $request->sub_domain;
            }
            
            $company->is_final_setup = 1;
            $company->address = 'No set yet.';
            
            $company->save();
            
            $user = $company->addUser($company, $request);
            $message = $company->addEmployeeDetails($user);
            $company->assignRoles($user);
            
            $stripeCustomer = $company->createOrGetStripeCustomer([
                    'name' => $request->company_name,
                    'email' => $request->company_email
            ]);
            
            $company->updateDefaultPaymentMethod($request->stripeToken); 
            $company->updateDefaultPaymentMethodFromStripe();
            $paymentMethod = $company->defaultPaymentMethod();
            
//            if($coupon_code != '') {
//                $company->newSubscription('default', $plan_id)->trialDays(15)->withCoupon($coupon_code)->create($paymentMethod->id, [
//                       'email' => $request->company_email
//                ]);
//                 $company->stripe_coupon = $coupon_code;
//            } else {
//                $company->newSubscription('default', $plan_id)->trialDays(15)->create($paymentMethod->id, [
//                       'email' => $request->company_email
//                ]);
//            }
            
            if($coupon_code != '') {
                $company->newSubscription('default', $plan_id)->withCoupon($coupon_code)->create($paymentMethod->id, [
                       'email' => $request->company_email
                ]);
                 $company->stripe_coupon = $coupon_code;
            } else {
                $company->newSubscription('default', $plan_id)->create($paymentMethod->id, [
                       'email' => $request->company_email
                ]);
            }

            $subscription = $company->subscriptions;
            $subscription = $subscription[0];
            
            //$subscription = $company->subscription('default')->items->first();            
            
            $lastInvoice = $company->invoices()->last();
            //$invoiceNumber = $lastInvoice->asStripeInvoice()->number;
            $stripeInvoice = new StripeInvoice();
            $stripeInvoice->company_id = $company->id;
            $stripeInvoice->package_id = $package->id;
            $stripeInvoice->transaction_id = $lastInvoice->id;
            $stripeInvoice->invoice_id = $lastInvoice->id;
            $stripeInvoice->payment_via = 'stripe';
            $stripeInvoice->amount = ($is_annual == '1') ? $package->annual_price : $package->monthly_price;
            $stripeInvoice->pay_date =\Carbon\Carbon::now()->format('Y-m-d');
            $stripeInvoice->next_pay_date = \Carbon\Carbon::createFromTimeStamp($company->upcomingInvoice()->next_payment_attempt)->format('Y-m-d');
            
            $stripeInvoice->last4 = $paymentMethod->card->last4;
            $stripeInvoice->brand = $paymentMethod->card->brand;
            $stripeInvoice->country = $paymentMethod->card->country;
            $stripeInvoice->exp_month =  $paymentMethod->card->exp_month;
            $stripeInvoice->exp_year = $paymentMethod->card->exp_year;
            $stripeInvoice->save();
            
            $company->stripe_id = $stripeCustomer->id;
            $company->stripe_subscription_id = $subscription->stripe_id;
            
            $company->card_brand = $paymentMethod->card->brand;
            $company->card_last_four = $paymentMethod->card->last4;
            $company->card_exp_month = $paymentMethod->card->exp_month;
            $company->card_exp_year = $paymentMethod->card->exp_year;
            
            
            
            $company->package_id = $package->id;
            $company->package_type = ($is_annual == '1') ? 'annual' : 'monthly';
            $company->status = 'active'; // Set company status active
            $company->licence_expire_on = null;
            
            $company->save();
            
            
            
            DB::commit();
        } catch (\Swift_TransportException $e) {
            DB::rollback();
            \Session::put('error',$e->getMessage());
            return redirect($redirectRoute);
            //return Reply::error('Please contact administrator to set SMTP details to add company', 'smtp_error');
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            \Session::put('error',$e->getMessage());
            return redirect($redirectRoute);
            //return Reply::error('Some error occurred when inserting the data. Please try again or contact support');
        }
        
        $this->mixPanelTrackEvent('user_sign_up', array('page_path' => '/signup'), $user);
        
        \Auth::loginUsingId($user->id, true);
        return redirect(route('admin.dashboard'));
         
        //\Session::put('message','Thank you for signing up. Please login to get started.');
        //return redirect(route('login'));
        //return Reply::success($message);
    }

    public function getEmailVerification($code)
    {
        $this->pageTitle = 'modules.accountSettings.emailVerification';
        $this->message = User::emailVerify($code);
        $this->mixPanelTrackEvent('email_verified', array('page_path' => '/email-verification'), false);
        return view('auth.email-verification', $this->data);
    }
}
