<?php

namespace App\Http\Controllers\Client;

use App\ClientPayment;
use App\CreditNotes;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Invoices\OfflinePaymentRequest;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\Notifications\OfflineInvoicePaymentRequest;
use App\OfflineInvoicePayment;
use App\OfflinePaymentMethod;
use App\PaymentGatewayCredentials;
use App\LineItemGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClientInvoicesController extends ClientBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.invoices';
        $this->pageIcon = 'ti-receipt';

        $this->middleware(function ($request, $next) {
            if (!in_array('invoices', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('client.invoices.index', $this->data);
    }

    public function create()
    {
        $invoices = Invoice::leftJoin('projects', 'projects.id', '=', 'invoices.project_id')
            ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
            ->select('invoices.id', 'projects.project_name', 'invoices.invoice_number', 'currencies.currency_symbol', 'currencies.currency_code', 'invoices.total', 'invoices.deposit_req' ,'invoices.is_deposit', 'invoices.issue_date', 'invoices.status')
            ->where(function ($query) {
                $query->where('projects.client_id', $this->user->id)
                    ->orWhere('invoices.client_id', $this->user->id);
            })
            ->where('invoices.status', '<>', 'draft')
            ->where('invoices.send_status', 1)
            ->where('invoices.status', '!=', 'canceled');

        return DataTables::of($invoices)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('client.invoices.download', $row->id) . '" data-toggle="tooltip" data-original-title="Download" class="btn  btn-sm btn-outline btn-info"><i class="fa fa-download"></i> '.__('app.download').'</a>';
            })
            ->editColumn('project_name', function ($row) {
                return $row->project_name != '' ? $row->project_name : '--';
            })
            ->editColumn('invoice_number', function ($row) {
                return '<a style="text-decoration: underline" href="' . route('client.invoices.show', $row->id) . '">' . $row->invoice_number . '</a>';
            })
            ->editColumn('currency_symbol', function ($row) {
                return $row->currency_symbol . ' (' . $row->currency_code . ')';
            })
            ->editColumn('issue_date', function ($row) {
                return $row->issue_date->format($this->global->date_format);
            })
             ->editColumn('deposit_req', function ($row) {
                 $deposit_req = $row->deposit_req;
                 
                 if($row->deposit_req > 0 && $row->is_deposit == 0 && $row->status == 'unpaid') {
                     $deposit_req =  '<a style="text-decoration: underline" href="' . route('client.invoices.deposit', $row->id) . '">' . $row->deposit_req . '</a>';
                 }
                 
                 return $deposit_req;
                
            })
            
            
            ->editColumn('status', function ($row) {
                if ($row->status == 'unpaid') {
                    return '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                } else if($row->status == 'review') {
                    return '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                } else {
                    return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }
            })
            ->rawColumns(['action', 'status', 'invoice_number', 'deposit_req'])
            ->removeColumn('currency_code')
            ->make(true);
    }

    public function download($id)
    {

        $this->invoice    = Invoice::findOrFail($id);
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->creditNote = 0;

        if ($this->invoice->credit_note) {
            $this->creditNote = CreditNotes::where('invoice_id', $id)
                ->select('cn_number')
                ->first();
        }

        // Download file uploaded
        if ($this->invoice->file != null) {
            return response()->download(storage_path('app/public/invoice-files') . '/' . $this->invoice->file);
        }

        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }


        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();


        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax){
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        if($this->invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price);
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                        }
                        
                    } else {
                        if($this->invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price));
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                        }
                        
                    }
                }
            }
        }

        $this->taxes = $taxList;
        $this->individual_tax = $this->invoice->total - ($this->invoice->sub_total + $this->invoice->total_tax);
        
        $individual_tax_name = '';
       
        if($this->invoice->tax_on_total) {
            foreach (json_decode($this->invoice->tax_on_total) as $tax) {
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax) {
                    if($individual_tax_name == '') {
                         $individual_tax_name = $this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    } else {
                        $individual_tax_name .= ' ,'.$this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    }
                }
            }
        }
        
        $this->individual_tax_name = $individual_tax_name;
        

        $this->settings = $this->global;

        

        $this->company = $this->invoice->company;
        $pdf = app('dompdf.wrapper');
        //$pdf->loadView('invoices.' . $this->invoiceSetting->template, $this->data);
        
         if($this->invoice->combine_line_items == 1) {
            $allItems = InvoiceItems::where('invoice_id', $this->invoice->id)->get();
            $this->groupItems = getGroupItems($allItems);
            // genral combine invoice pdf for all
            $pdf->loadView('invoices.invoice-general-combine', $this->data);
        } else {
            
            // genral invoice pdf for all
            $pdf->loadView('invoices.invoice-general', $this->data);
        }
        
        
        $filename = $this->invoice->invoice_number;

        return $pdf->download($filename . '.pdf');
    }

    public function show($id)
    {
        $this->invoice = Invoice::with('offline_invoice_payment', 'offline_invoice_payment.payment_method')->where([
            'id' => $id,
            'credit_note' => 0
        ])
        ->whereHas('project', function ($q) {
            $q->where('client_id', $this->user->id);
        }, '>=', 0)
        ->firstOrFail();

        $this->paidAmount = $this->invoice->getPaidAmount();

        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }
        
        $this->invoiceSetting = InvoiceSetting::first();

        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax){
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        if($this->invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * ($item->amount- $item->shipping_price);
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                        }
                        
                    } else {
                        if($this->invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price));

                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                        }
                        
                    }
                }
            }
        }

        $this->taxes = $taxList;
        
        $this->individual_tax = $this->invoice->total - ($this->invoice->sub_total + $this->invoice->total_tax);
        
        $individual_tax_name = '';
       
        if($this->invoice->tax_on_total) {
            foreach (json_decode($this->invoice->tax_on_total) as $tax) {
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax) {
                    if($individual_tax_name == '') {
                         $individual_tax_name = $this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    } else {
                        $individual_tax_name .= ' ,'.$this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    }
                }
            }
        }
        
        $this->individual_tax_name = $individual_tax_name;

        $this->settings = $this->global;
        $stripeCredentials =  PaymentGatewayCredentials::first();
        $this->methods = OfflinePaymentMethod::activeMethod();
        
        if($stripeCredentials->plaid_status == 'active') {
            
            $headers[] = 'Content-Type: application/json';
            $params = [
                'client_id' => $stripeCredentials->plaid_client_id ,
                'secret' => $stripeCredentials->plaid_secret,
                'user' => ['client_user_id' => ''.$this->user->id.''],
                'client_name' => $this->invoice->client->name,
                'products' => ['auth'],
                'country_codes' => ['US'],
                'language' =>  'en',
                'webhook' => route('client.plaid-webhook'),
            ];
            
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://production.plaid.com/link/token/create");
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
            
            //var_dump($jsonParsed);exit;
            
            
            if(isset($jsonParsed->link_token)) {
                $stripeCredentials->plaid_link_token = $jsonParsed->link_token;
                $stripeCredentials->save();
            }
        }
        
        $this->credentials = PaymentGatewayCredentials::first();
        
        
        if($this->invoice->combine_line_items == 1) {
            
            $allItems = InvoiceItems::where('invoice_id', $this->invoice->id)->get();
            $this->groupItems = getGroupItems($allItems);
            
            return view('client.invoices.show_combine', $this->data);
            
        } else {
            return view('client.invoices.show', $this->data);
        }
        
        

        

        
    }
    
     public function deposit($id)
    {
        $this->invoice = Invoice::with('offline_invoice_payment', 'offline_invoice_payment.payment_method')->where([
            'id' => $id,
            'credit_note' => 0
        ])
        ->whereHas('project', function ($q) {
            $q->where('client_id', $this->user->id);
        }, '>=', 0)
        ->firstOrFail();

        $this->paidAmount = $this->invoice->getPaidAmount();

        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }
        
        $this->invoiceSetting = InvoiceSetting::first();

        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax){
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        if($this->invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price);
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                        }
                        
                    } else {
                        if($this->invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price));
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                        }
                        
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->global;
        $this->credentials = PaymentGatewayCredentials::first();
        $this->methods = OfflinePaymentMethod::activeMethod();

        

        return view('client.invoices.deposit', $this->data);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $invoiceId = $request->invoiceId;
        $invoice   = Invoice::findOrFail($invoiceId);

        $clientPayment = new ClientPayment();
        $clientPayment->currency_id       = $invoice->currency_id;
        $clientPayment->company_id        = $invoice->company_id;
        $clientPayment->invoice_id        = $invoice->id;
        $clientPayment->project_id        = $invoice->project_id;
        $clientPayment->amount            = $invoice->total;
        $clientPayment->offline_method_id = $request->offlineId;
        $clientPayment->transaction_id    = Carbon::now()->timestamp;
        $clientPayment->gateway           = 'Offline';
        $clientPayment->status            = 'complete';
        $clientPayment->paid_on           = Carbon::now();
        $clientPayment->save();

        $invoice->status = 'paid';
        $invoice->save();

        return Reply::redirect(route('client.invoices.show', $invoiceId), __('messages.paymentSuccess'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function offlinePayment(Request $request)
    {
        $this->offlineId = $request->offlineId;
        $this->invoiceId = $request->invoiceId;
        $this->is_deposit = $request->is_deposit ? $request->is_deposit : 0;

        return \view('client.invoices.offline-payment', $this->data);
    }

    public function offlinePaymentSubmit(OfflinePaymentRequest $request) {
        
        if (isset($request->is_deposit) && $request->is_deposit == 1) {

            $checkAlreadyRequest = Invoice::with(['offline_invoice_payment' => function($q) {
                            //$q->where('is_deposit', '0');
                        }])->where('id', $request->invoice_id)->where('is_deposit', 0)->first();
                        
            if (!$checkAlreadyRequest) {
                return Reply::error('You have already raised a request.');
            }
            
            $checkAlreadyRequest->status = 'review';
            $checkAlreadyRequest->is_deposit = 1;
            $checkAlreadyRequest->save();

            // create offline payment request
            $offlinePayment = new OfflineInvoicePayment();
            $offlinePayment->invoice_id = $checkAlreadyRequest->id;
            $offlinePayment->client_id = $this->user->id;
            $offlinePayment->payment_method_id = $request->offline_id;
            $offlinePayment->description = $request->description;
            $offlinePayment->is_offline_deposit = 1;
            


            if ($request->hasFile('slip')) {
                $offlinePayment->slip = Files::upload($request->slip, 'offline-payment-files', null, null, false);
            }


            $offlinePayment->save();

            $clientPayment = new ClientPayment();
            $clientPayment->currency_id = $checkAlreadyRequest->currency_id;
            $clientPayment->company_id = $checkAlreadyRequest->company_id;
            $clientPayment->invoice_id = $checkAlreadyRequest->id;
            $clientPayment->project_id = $checkAlreadyRequest->project_id;
            $clientPayment->amount = $checkAlreadyRequest->deposit_req;
            $clientPayment->offline_method_id = $request->offline_id;
            $clientPayment->transaction_id = Carbon::now()->timestamp;
            $clientPayment->gateway = 'Offline';
            $clientPayment->status = 'pending';
            $clientPayment->paid_on = Carbon::now();
            $clientPayment->save();
        } else {

            $checkAlreadyRequest = Invoice::with(['offline_invoice_payment' => function($q) {
                            $q->where('status', 'pending');
                        }])->where('id', $request->invoice_id)->first();

            if ($checkAlreadyRequest->offline_invoice_payment->count() > 1) {
                return Reply::error('You have already raised a request.');
            }

            $checkAlreadyRequest->status = 'review';
            $checkAlreadyRequest->save();
            
            $total = $checkAlreadyRequest->total;
            if($checkAlreadyRequest->is_deposit == 1) {
                $total = $checkAlreadyRequest->total - $checkAlreadyRequest->deposit_req;
                //$checkAlreadyRequest->status = 'partial';
            }
            

            // create offline payment request
            $offlinePayment = new OfflineInvoicePayment();
            $offlinePayment->invoice_id = $checkAlreadyRequest->id;
            $offlinePayment->client_id = $this->user->id;
            $offlinePayment->payment_method_id = $request->offline_id;
            $offlinePayment->description = $request->description;


            if ($request->hasFile('slip')) {
                $offlinePayment->slip = Files::upload($request->slip, 'offline-payment-files', null, null, false);
            }


            $offlinePayment->save();

            $clientPayment = new ClientPayment();
            $clientPayment->currency_id = $checkAlreadyRequest->currency_id;
            $clientPayment->company_id = $checkAlreadyRequest->company_id;
            $clientPayment->invoice_id = $checkAlreadyRequest->id;
            $clientPayment->project_id = $checkAlreadyRequest->project_id;
            $clientPayment->amount = $total;
            $clientPayment->offline_method_id = $request->offline_id;
            $clientPayment->transaction_id = Carbon::now()->timestamp;
            $clientPayment->gateway = 'Offline';
            $clientPayment->status = 'pending';
            $clientPayment->paid_on = Carbon::now();
            $clientPayment->save();
        }


        return Reply::redirect(route('client.invoices.show', $checkAlreadyRequest->id));
    }

}
