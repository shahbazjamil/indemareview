<?php
namespace App\Http\Controllers\Client;

use App\ClientPayment;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Invoice;
use App\Payment;
use App\PaymentGatewayCredentials;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Subscription;
use Validator;
use URL;
use Session;
use Redirect;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;

class StripeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'Stripe';
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentWithStripe(Request $request, $invoiceId)
    {
        $redirectRoute = 'client.invoices.show';
        $id = $invoiceId;

        return $this->makeStripePayment($request, $invoiceId, $redirectRoute, $id);
    }

    public function paymentWithStripePublic(Request $request, $invoiceId)
    {
        $redirectRoute = 'front.invoice';
        $id = md5($invoiceId);

        return $this->makeStripePayment($request, $invoiceId, $redirectRoute, $id);
    }

    private function makeStripePayment($request, $invoiceId, $redirectRoute, $id)
    {
        
        $invoice = Invoice::findOrFail($invoiceId);
        
        if($invoice->status == 'paid') {
            \Session::put('success','Invoice has already paid');
            return Reply::redirect(route($redirectRoute, $id), 'Invoice has already paid');
        }

        $stripeCredentials = PaymentGatewayCredentials::withoutGlobalScope(CompanyScope::class)
                                                      ->where('company_id', $invoice->company_id)
                                                      ->first();

        /** setup Stripe credentials **/
        Stripe::setApiKey($stripeCredentials->stripe_secret);

        $tokenObject  = $request->get('token');
        $token  = $tokenObject['id'];
        $email  = $tokenObject['email'];
        $name = $request->name;
        $line1 = $request->line1;
        $postal_code = $request->postal_code;
        $city = $request->city;
        $state = $request->state;
        $country = $request->country;
        
        $invoice_tatus = 'paid';
        $total = $invoice->total;
        // deposit already paid then 
        if($invoice->is_deposit == 1) {
            $total = $invoice->total - $invoice->deposit_req;
        }
   //
           
        if($request->has('is_deposit') && $request->get('is_deposit') == 1) {
            $total = $invoice->deposit_req;
            $invoice_tatus = 'partial';
        }

        if($invoice->recurring == 'no')
        {
            $lm_data = '';
            if(isset($invoice->company) && isset($invoice->company->lm_data) && !empty($invoice->company->lm_data)) {
                $lm_data = $invoice->company->lm_data;
            }
            
            
            try {
                $customer = Customer::create(array(
                    'name' => $name,
                    'email' => $email,
                    'address' => [
                        'line1' => $line1,
                        'postal_code' => $postal_code,
                        'city' => $city,
                        'state' => $state,
                        'country' => $country,
                    ],
                    'source'  => $token,
                ));

                $charge = Charge::create(array(
                    'customer' => $customer->id,
                    'amount'   => $total*100,
                    'currency' => $invoice->currency->currency_code,
                    'description' => $invoice->invoice_number. ' Payment',
                    'metadata' => array('lm_data' => $lm_data)
                ));

            } catch (\Exception $ex) {
                \Session::put('error',$ex->getMessage());
                return Reply::redirect(route($redirectRoute, $id), 'Payment fail');
            }

            $payment = new Payment();
            $payment->project_id = $invoice->project_id;
            $payment->invoice_id = $invoice->id;
            $payment->company_id = $invoice->company_id;
            $payment->currency_id = $invoice->currency_id;
            $payment->amount = $total;
            $payment->gateway = 'Stripe';
            $payment->transaction_id = $charge->id;
            $payment->paid_on = Carbon::now();
            $payment->status = 'complete';
            $payment->save();

        } else {

            $plan = Plan::create(array(
                "amount" => $total*100,
                "currency" => $invoice->currency->currency_code,
                "interval" => $invoice->billing_frequency,
                "product" => ['name' => $invoice->invoice_number],
                "id" => 'plan-'.$invoice->id.'-'.str_random('10'),
                "interval_count" => $invoice->billing_interval,
                "metadata" => [
                    "invoice_id" => $invoice->id
                ],
            ));

            try {

                $customer = Customer::create(array(
                    'name' => $name,
                    'email' => $email,
                    'address' => [
                        'line1' => $line1,
                        'postal_code' => $postal_code,
                        'city' => $city,
                        'state' => $state,
                        'country' => $country,
                    ],
                    'source'  => $token,
                ));

                $subscription = Subscription::create(array(
                    "customer" => $customer->id,
                    "items" => array(
                        array(
                            "plan" => $plan->id,
                        ),
                    ),
                    "metadata" => [
                        "invoice_id" => $invoice->id
                    ],
                ));

            } catch (\Exception $ex) {
                \Session::put('error',$ex->getMessage());
                return Reply::redirect(route($redirectRoute, $id), 'Payment fail');
            }

            // Save details in database
            $payment = new Payment();
            $payment->project_id = $invoice->project_id;
            $payment->invoice_id = $invoice->id;
            $payment->company_id = $invoice->company_id;
            $payment->currency_id = $invoice->currency_id;
            $payment->amount = $total;
            $payment->gateway = 'Stripe';
            $payment->plan_id = $plan->id;
            $payment->transaction_id = $subscription->id;
            $payment->paid_on = Carbon::now();
            $payment->status = 'complete';
            $payment->save();
        }

        $invoice->status = $invoice_tatus;
        
        // for designer deposit request
        if($request->has('is_deposit') && $request->get('is_deposit') == 1) {
            $invoice->is_deposit = 1;
        }
        
        $invoice->save();

        \Session::put('success','Payment success');
        return Reply::redirect(route($redirectRoute, $id), 'Payment success');
    }
    
    public function makeStripePaymentWithPlaid(Request $request, $invoiceId){

        $redirectRoute = 'client.invoices.show';
        $id = $invoiceId;

        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->status == 'paid') {
            \Session::put('success', 'Invoice has already paid');
            return Reply::redirect(route($redirectRoute, $id), 'Invoice has already paid');
        }

        $stripeCredentials = PaymentGatewayCredentials::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', $invoice->company_id)
                ->first();




        if (isset($request->action) && $request->action == "get_client") {


            $invoice_tatus = 'paid';
            $total = $invoice->total;
            // deposit already paid then 
            if ($invoice->is_deposit == 1) {
                $total = $invoice->total - $invoice->deposit_req;
            }

            if ($request->has('is_deposit') && $request->get('is_deposit') == 1) {
                $total = $invoice->deposit_req;
                $invoice_tatus = 'partial';
            }

            $lm_data = '';
            if (isset($invoice->company) && isset($invoice->company->lm_data) && !empty($invoice->company->lm_data)) {
                $lm_data = $invoice->company->lm_data;
            }



            $headers[] = 'Content-Type: application/json';
            $params = [
                'client_id' => '' . $stripeCredentials->plaid_client_id . '',
                'secret' => '' . $stripeCredentials->plaid_secret . '',
                'public_token' => '' . $request->token . '',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://production.plaid.com/item/public_token/exchange");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 80);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if (!$result = curl_exec($ch)) {
                trigger_error(curl_error($ch));
            }
            curl_close($ch);

            $jsonParsed = json_decode($result);
            $btok_params = [
                'client_id' => '' . $stripeCredentials->plaid_client_id . '',
                'secret' => '' . $stripeCredentials->plaid_secret . '',
                'access_token' => $jsonParsed->access_token,
                'account_id' => '' . $request->account_id . ''
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://production.plaid.com/processor/stripe/bank_account_token/create");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($btok_params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 80);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if (!$result = curl_exec($ch)) {
                trigger_error(curl_error($ch));
            }
            curl_close($ch);
            $btok_parsed = json_decode($result);

            /** setup Stripe credentials * */
            Stripe::setApiKey($stripeCredentials->stripe_secret);

            try {
                $customer = Customer::create(array(
                            "source" => '' . $btok_parsed->stripe_bank_account_token . '',
                            "description" => 'adding client'
                ));

                $charge = Charge::create(array(
                            'customer' => $customer->sources->data[0]['customer'],
                            'amount' => $total * 100,
                            'currency' => $invoice->currency->currency_code,
                            'description' => $invoice->invoice_number . ' Payment',
                            'metadata' => array('lm_data' => $lm_data)
                ));
            } catch (\Exception $ex) {
                \Session::put('error', $ex->getMessage());
                return Reply::redirect(route($redirectRoute, $id), 'Payment fail');
            }

            //$arr = array('name' => $customer->sources->data[0]['account_holder_name'], 'acount_no' => $customer->sources->data[0]['last4'], 'routing_no' => $customer->sources->data[0]['routing_number'], 'bank_status' => $customer->sources->data[0]['status'], 'id' => $customer->sources->data[0]['id'], 'customer' => $customer->sources->data[0]['customer']);
            /* These are few details which can be fetched once customer has been created and can be used in payment form or stripe form also client id can be saved in session and on click pay \Stripe\Charge::create function can be used */

            $payment = new Payment();
            $payment->project_id = $invoice->project_id;
            $payment->invoice_id = $invoice->id;
            $payment->company_id = $invoice->company_id;
            $payment->currency_id = $invoice->currency_id;
            $payment->amount = $total;
            $payment->gateway = 'Stripe';
            $payment->transaction_id = $charge->id;
            $payment->paid_on = Carbon::now();
            $payment->status = 'complete';
            $payment->plaid_response = json_encode($customer);
            $payment->save();

            $invoice->status = $invoice_tatus;

            // for designer deposit request
            if ($request->has('is_deposit') && $request->get('is_deposit') == 1) {
                $invoice->is_deposit = 1;
            }

            $invoice->save();

            \Session::put('success', 'Payment success');
            return Reply::redirect(route($redirectRoute, $id), 'Payment success');
        }
        
        \Session::put('error', $ex->getMessage());
        return Reply::redirect(route($redirectRoute, $id), 'Something wrong, Please try again');
    }
    
        public function stripePlaidWebhook(Request $request) {
            
        }

}
