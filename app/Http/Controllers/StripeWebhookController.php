<?php

namespace App\Http\Controllers;

use App\ClientPayment;
use App\Company;
use App\Invoice;
use App\Notifications\CompanyPurchasedPlan;
use App\Notifications\CompanyUpdatedPlan;
use App\Payment;
use App\PaymentGatewayCredentials;
use App\StripeInvoice;
use App\Subscription;
use App\Traits\StripeSettings;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controller;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller {

    use StripeSettings;

    public function verifyStripeWebhook(Request $request) {
        $this->setStripConfigs();

        $stripeCredentials = PaymentGatewayCredentials::first();

        Stripe::setApiKey(config('cashier.secret'));

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $stripeCredentials->stripe_webhook_secret;

        $payload = @file_get_contents("php://input");
        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                            $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid Payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        $payload = json_decode($request->getContent(), true);

        $eventId = $payload['id'];
        $eventCount = ClientPayment::where('event_id', $eventId)->count();

        // Do something with $event
        if ($payload['type'] == 'invoice.payment_succeeded' && $eventCount == 0) {
            $planId = $payload['data']['object']['lines']['data'][0]['plan']['id'];
            $customerId = $payload['data']['object']['customer'];
            $amount = $payload['data']['object']['lines']['data'][0]['amount'];
            $transactionId = $payload['data']['object']['lines']['data'][0]['id'];
            $invoiceId = $payload['data']['object']['lines']['data'][0]['plan']['metadata']['invoice_id'];

            $previousClientPayment = ClientPayment::where('plan_id', $planId)
                    ->where('transaction_id', $transactionId)
//                                                    ->where('customer_id', $customerId)
                    ->whereNull('event_id')
                    ->first();
            if ($previousClientPayment) {
                $previousClientPayment->event_id = $eventId;
                $previousClientPayment->save();
            } else {
                $invoice = Invoice::find($invoiceId);

                $payment = new Payment();
                $payment->project_id = $invoice->project_id;
                $payment->currency_id = $invoice->currency_id;
                $payment->amount = $amount / 100;
                $payment->event_id = $eventId;
                $payment->gateway = 'Stripe';
                $payment->paid_on = Carbon::now();
                $payment->status = 'complete';
                $payment->save();
            }
        }

        return response('Webhook Handled', 200);
    }

    public function saveInvoices(Request $request) {
        $this->setStripConfigs();
        $today = Carbon::now();

        $stripeCredentials = config('cashier.webhook.secret');

        Stripe::setApiKey(config('cashier.secret'));

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $stripeCredentials;

        $payload = @file_get_contents("php://input");
        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        $event = null;

        try {

            $event = Webhook::constructEvent(
                            $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid Payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        $payload = json_decode($request->getContent(), true);
        \Log::debug($payload);
        // Do something with $event
        if ($payload['type'] == 'invoice.payment_succeeded') {
            $planId = $payload['data']['object']['lines']['data'][0]['plan']['id'];
            $customerId = $payload['data']['object']['customer'];
            $amount = $payload['data']['object']['amount_paid'];
            $transactionId = $payload['data']['object']['lines']['data'][0]['id'];
//            $invoiceId = $payload['data']['object']['number'];
            $invoiceRealId = $payload['data']['object']['id'];

            $company = Company::where('stripe_id', $customerId)->first();

            $package = \App\Package::where(function ($query) use($planId) {
                        $query->where('stripe_annual_plan_id', '=', $planId)
                                ->orWhere('stripe_monthly_plan_id', '=', $planId);
                    })->first();

            if ($company) {

                $paymentMethod = $company->defaultPaymentMethod();

                if ($package->is_addons == 1) {

                    $plan_type = $package->chargebee_monthly_plan_id;


                    // Store invoice details
                    $stripeInvoice = new StripeInvoice();
                    $stripeInvoice->company_id = $company->id;
                    $stripeInvoice->invoice_id = $invoiceRealId;
                    $stripeInvoice->transaction_id = $transactionId;
                    $stripeInvoice->amount = $amount / 100;
                    $stripeInvoice->package_id = $package->id;
                    $stripeInvoice->pay_date = \Carbon\Carbon::now()->format('Y-m-d');
                    $stripeInvoice->next_pay_date = \Carbon\Carbon::createFromTimeStamp($company->upcomingInvoice()->next_payment_attempt)->format('Y-m-d');

                    $stripeInvoice->last4 = $paymentMethod->card->last4;
                    $stripeInvoice->brand = $paymentMethod->card->brand;
                    $stripeInvoice->country = $paymentMethod->card->country;
                    $stripeInvoice->exp_month = $paymentMethod->card->exp_month;
                    $stripeInvoice->exp_year = $paymentMethod->card->exp_year;

                    $stripeInvoice->save();


                    if ($plan_type == 'social-free' || $plan_type == 'social-starter' || $plan_type == 'social-pro') {

                        $company->is_social = 1; // Set booking module status active
                        //$company->social_expire_on = strtolower($request->content['subscription']['billing_period_unit']) == 'year' ? $today->addYear()->format('Y-m-d') : $today->addMonth()->format('Y-m-d');

                        $company->social_expire_on = $today->addMonth()->format('Y-m-d');
                    } else if ($plan_type == 'additional-user') {

                        $company->is_additional_user = 1;
                        $company->additional_user_expire_on = $today->addMonth()->format('Y-m-d');
                        //$company->additional_number_users = ($company->additional_number_users+$quantity);
                    } else {

                        $company->is_booking = 1; // Set booking module status active
                        $company->booking_expire_on = $today->addMonth()->format('Y-m-d');
                    }

                    return response('Webhook addons Handled', 200);
                } else {

                    // Store invoice details
                    $stripeInvoice = new StripeInvoice();
                    $stripeInvoice->company_id = $company->id;
                    $stripeInvoice->invoice_id = $invoiceRealId;
                    $stripeInvoice->payment_via = 'stripe';
                    $stripeInvoice->transaction_id = $transactionId;
                    $stripeInvoice->amount = $amount / 100;
                    $stripeInvoice->package_id = $package->id;
                    $stripeInvoice->pay_date = \Carbon\Carbon::now()->format('Y-m-d');
                    $stripeInvoice->next_pay_date = \Carbon\Carbon::createFromTimeStamp($company->upcomingInvoice()->next_payment_attempt)->format('Y-m-d');

                    $stripeInvoice->last4 = $paymentMethod->card->last4;
                    $stripeInvoice->brand = $paymentMethod->card->brand;
                    $stripeInvoice->country = $paymentMethod->card->country;
                    $stripeInvoice->exp_month = $paymentMethod->card->exp_month;
                    $stripeInvoice->exp_year = $paymentMethod->card->exp_year;

                    $stripeInvoice->save();

                    $company->package_type = 'monthly';
                    if ($package->stripe_annual_plan_id == $planId) {
                        $company->package_type = 'annual';
                    }

                    $company->card_brand = $paymentMethod->card->brand;
                    $company->card_last_four = $paymentMethod->card->last4;
                    $company->card_exp_month = $paymentMethod->card->exp_month;
                    $company->card_exp_year = $paymentMethod->card->exp_year;

                    // Change company status active after payment
                    $company->status = 'active';
                    $company->save();

//                dd($stripeInvoice);

                    $generatedBy = User::whereNull('company_id')->get();
                    $lastInvoice = StripeInvoice::where('company_id')->first();

                    if ($lastInvoice) {
                        Notification::send($generatedBy, new CompanyUpdatedPlan($company, $package->id));
                    } else {
                        Notification::send($generatedBy, new CompanyPurchasedPlan($company, $package->id));
                    }

                    return response('Webhook Handled', 200);
                }
            }

            return response('Customer not found', 200);
        } elseif ($payload['type'] == 'invoice.payment_failed') {

            $planId = '';
            $plan_type = '';
            $is_addons = 0;

            if (isset($payload['data']['object']['lines']) && isset($payload['data']['object']['lines']['data'][0]) && isset($payload['data']['object']['lines']['data'][0]['plan'])) {
                $planId = $payload['data']['object']['lines']['data'][0]['plan']['id'];
            }
            if ($planId !== '') {
                $package = \App\Package::where(function ($query) use($planId) {
                            $query->where('stripe_annual_plan_id', '=', $planId)
                                    ->orWhere('stripe_monthly_plan_id', '=', $planId);
                        })->first();

                if ($package) {
                    $plan_type = $package->chargebee_monthly_plan_id;
                    $is_addons = $package->is_addons;
                }
            }

            $customerId = $payload['data']['object']['customer'];
            $company = Company::where('stripe_id', $customerId)->first();

            if ($company) {

                if ($is_addons == 1) {

                    if ($plan_type == 'social-free' || $plan_type == 'social-starter' || $plan_type == 'social-pro') {

                        $company->is_social = 0;
                        $company->social_expire_on = $today->format('Y-m-d');
                    } else if ($plan_type == 'additional-user') {

                        $company->is_additional_user = 0;
                        $company->additional_number_users = 0;
                        $company->additional_user_expire_on = $today->format('Y-m-d');
                    } else {

                        $company->is_booking = 0;
                        $company->booking_expire_on = $today->format('Y-m-d');
                    }
                    $company->save();

                    return response('Addons subscription canceled', 200);
                } else {

                    $subscription = Subscription::where('company_id', $company->id)->first();
                    if ($subscription) {
                        //$subscription->ends_at = \Carbon\Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                        $subscription->ends_at = $today->format('Y-m-d');
                        $subscription->save();
                    }

                    //$company->licence_expire_on = \Carbon\Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');
                    $company->licence_expire_on = $today->format('Y-m-d');
                    $company->cancelled_by = 'stripe_subscription';
                    $company->status = 'license_expired';
                    $company->save();
                }

                return response('Company subscription canceled', 200);
            }
            return response('Customer not found', 200);
        } elseif ($payload['type'] == 'customer.subscription.deleted') {

            $planId = '';
            $plan_type = '';
            $is_addons = 0;

            if (isset($payload['data']['object']['lines']) && isset($payload['data']['object']['lines']['data'][0]) && isset($payload['data']['object']['lines']['data'][0]['plan'])) {
                $planId = $payload['data']['object']['lines']['data'][0]['plan']['id'];
            }
            if ($planId !== '') {
                $package = \App\Package::where(function ($query) use($planId) {
                            $query->where('stripe_annual_plan_id', '=', $planId)
                                    ->orWhere('stripe_monthly_plan_id', '=', $planId);
                        })->first();

                if ($package) {
                    $plan_type = $package->chargebee_monthly_plan_id;
                    $is_addons = $package->is_addons;
                }
            }


            $customerId = $payload['data']['object']['customer'];
            $company = Company::where('stripe_id', $customerId)->first();

            if ($company) {
                if ($is_addons == 1) {

                    if ($plan_type == 'social-free' || $plan_type == 'social-starter' || $plan_type == 'social-pro') {

                        $company->is_social = 0;
                        $company->social_expire_on = $today->format('Y-m-d');
                    } else if ($plan_type == 'additional-user') {

                        $company->is_additional_user = 0;
                        $company->additional_number_users = 0;
                        $company->additional_user_expire_on = $today->format('Y-m-d');
                    } else {

                        $company->is_booking = 0;
                        $company->booking_expire_on = $today->format('Y-m-d');
                    }
                    $company->save();

                    return response('Addons subscription canceled', 200);
                } else {

                    $subscription = Subscription::where('company_id', $company->id)->first();

                    if ($subscription) {
                        $subscription->ends_at = $today->format('Y-m-d');
                        $subscription->save();
                    }
                    $company->licence_expire_on = $today->format('Y-m-d');
                    $company->cancelled_by = 'stripe_subscription';
                    $company->status = 'license_expired';
                    $company->save();

                    return response('Company subscription canceled', 200);
                }
            }

            return response('Customer not found', 200);
        }
    }

}
