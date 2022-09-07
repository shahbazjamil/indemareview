<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Product;
use App\User;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Company;
use App\Package;
use App\ChargebeeInvoice;
use App\StripeInvoice;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CompanyUpdatedPlan;


class ChargebeeController extends Controller
{
    
    public function subscriptionCancel(Request $request, $key)
    {
        if($key == 'RO2AuPfKjakvzIkwSHOAs0VIfbKLwNZj') {
            $today = Carbon::now();
            
            $company = Company::where('chargebee_customer_id', $request->content['customer']['id'])->first();
            
            if($request->content['subscription']['billing_period_unit'] == 'month') {
                $package = Package::where('chargebee_monthly_plan_id', $request->content['subscription']['plan_id'])->first();
            } else {
                $package = Package::where('chargebee_annual_plan_id', $request->content['subscription']['plan_id'])->first();
            }
            
            if($company && $package) {
                if($package->is_addons == 1) {

                    $chargebee_plan_id = $request->content['subscription']['plan_id'];

                    if($chargebee_plan_id == 'social-free' || $chargebee_plan_id == 'social-starter' || $chargebee_plan_id == 'social-pro'){

                        $company->is_social = 0;
                        $company->social_expire_on = $today->format('Y-m-d');

                    } else if($chargebee_plan_id == 'additional-user'){
                        $company->is_additional_user = 0;
                        $company->additional_number_users = 0;
                        $company->additional_user_expire_on = $today->format('Y-m-d');
                    } else {

                        $company->is_booking = 0;
                        $company->booking_expire_on = $today->format('Y-m-d');

                    }



                    $company->cancelled_by = 'chargebee_addons';
                    $company->status = 'license_expired';
                    $company->save();
                } else {
                    $company->licence_expire_on = $today->format('Y-m-d');
                    $company->cancelled_by = 'chargebee_subscription';
                    $company->status = 'license_expired';
                    $company->save();
                }
                //return response()->json(['success' => true], 200);
            }
            //return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => true], 200);
        
    }
    
    public function subscriptionRenew(Request $request, $key) {
        
        if ($key == 'RO2AuPfKjakvzIkwSHOAs0VIfbKLwNZj') {
            $today = Carbon::now();

            $company = Company::where('chargebee_customer_id', $request->content['customer']['id'])->first();

            if ($company) {
                
                $request_data = json_encode($request);

                if (isset($request->content['customer']['payment_method']['status']) && strtolower($request->content['customer']['payment_method']['status']) == 'valid') {

                    if (strtolower($request->content['subscription']['billing_period_unit']) == 'year') {
                        $package = Package::where('chargebee_annual_plan_id', $request->content['subscription']['plan_id'])->first();
                    } else {
                        $package = Package::where('chargebee_monthly_plan_id', $request->content['subscription']['plan_id'])->first();
                    }

                    if ($package) {
                        
                        $ch_invoice_id = '';
                        if(isset($request->content['invoice'])){
                            $ch_invoice_id = $request->content['invoice']['id'];
                        }

                        if ($package->is_addons == 1) {
                            
                            $chargebee_plan_id = $request->content['subscription']['plan_id'];

                            // create Chargebee invoice
                            $chargebeeInvoice = new ChargebeeInvoice();
                            $chargebeeInvoice->company_id = $company->id;
                            $chargebeeInvoice->package_id = $package->id;
                            $chargebeeInvoice->transaction_id = $ch_invoice_id;
                            $chargebeeInvoice->invoice_id = $ch_invoice_id;
                            $chargebeeInvoice->amount = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $package->annual_price : $package->monthly_price;
                            $chargebeeInvoice->pay_date = $today->format('Y-m-d');
                            $chargebeeInvoice->request_data = $request_data;
                            
                            //$chargebeeInvoice->next_pay_date = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');
                            $chargebeeInvoice->save();

                            // create  Stripe invoice , its created due to default functionality of payment m will check later its affect
                            $stripeInvoice = new StripeInvoice();
                            $stripeInvoice->company_id = $company->id;
                            $stripeInvoice->package_id = $package->id;
                            $stripeInvoice->transaction_id = $ch_invoice_id;
                            $stripeInvoice->invoice_id = $ch_invoice_id;
                            $stripeInvoice->payment_via = 'chargebee';
                            $stripeInvoice->amount = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $package->annual_price : $package->monthly_price;
                            $stripeInvoice->pay_date = $today->format('Y-m-d');
                            //$stripeInvoice->next_pay_date = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') :$today->addMonth()->format('Y-m-d');
                            $stripeInvoice->save();

                            if($chargebee_plan_id == 'social-free' || $chargebee_plan_id == 'social-starter' || $chargebee_plan_id == 'social-pro'){

                                $company->is_social = 1; // Set booking module status active
                                $company->social_expire_on = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');

                            } else if($chargebee_plan_id == 'additional-user'){

                                $quantity = 1;
                                if(isset($request->content['subscription']['quantity']) && $request->content['subscription']['quantity'] > 0) {
                                    $quantity = $request->content['subscription']['quantity'];
                                }

                                $company->is_additional_user = 1;
                                $company->additional_user_expire_on = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');
                                //$company->additional_number_users = ($company->additional_number_users+$quantity);

                        } else {

                                $company->is_booking = 1; // Set booking module status active
                                $company->booking_expire_on = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');

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
                            $chargebeeInvoice->transaction_id = $ch_invoice_id;
                            $chargebeeInvoice->invoice_id = $ch_invoice_id;
                            $chargebeeInvoice->amount = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $package->annual_price : $package->monthly_price;
                            $chargebeeInvoice->pay_date = $today->format('Y-m-d');
                            $chargebeeInvoice->next_pay_date = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');
                            $chargebeeInvoice->request_data = $request_data;
                            $chargebeeInvoice->save();

                            // create  Stripe invoice , its created due to default functionality of payment m will check later its affect
                            $stripeInvoice = new StripeInvoice();
                            $stripeInvoice->company_id = $company->id;
                            $stripeInvoice->package_id = $package->id;
                            $stripeInvoice->transaction_id = $ch_invoice_id;
                            $stripeInvoice->invoice_id = $ch_invoice_id;
                            $stripeInvoice->payment_via = 'chargebee';
                            $stripeInvoice->amount = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $package->annual_price : $package->monthly_price;
                            $stripeInvoice->pay_date = $chargebeeInvoice->pay_date;
                            $stripeInvoice->next_pay_date = $chargebeeInvoice->next_pay_date;
                            $stripeInvoice->save();

                            $company->package_id = $package->id;
                            $company->package_type = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? 'annual' : 'monthly';
                            $company->status = 'active'; // Set company status active
                            $company->licence_expire_on = null;
                            //$company->licence_expire_on = strtolower($request->period_unit) == 'year' ? $today->addYear()->format('Y-m-d') :$today->addMonth()->format('Y-m-d');
                            $company->save();

                            //send superadmin notification
                            $generatedBy = User::whereNull('company_id')->get();
                            Notification::send($generatedBy, new CompanyUpdatedPlan($company, $chargebeeInvoice->package_id));
                        }
                    }
                }
            }

            return response()->json(['success' => true], 200);
        }
    }

}