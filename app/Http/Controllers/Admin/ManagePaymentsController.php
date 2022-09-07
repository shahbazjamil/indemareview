<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\DataTables\Admin\PaymentsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Payments\ImportPayment;
use App\Http\Requests\Payments\StorePayment;
use App\Http\Requests\Payments\UpdatePayments;
use App\Invoice;
use App\Payment;
use App\Project;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\Files;

use App\PurchaseOrder;
use App\PurchaseOrderItems;
use App\ClientVendorDetails;
use App\Scopes\CompanyScope;
use App\Product;
use App\Expense;

class ManagePaymentsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.payments';
        $this->pageIcon = 'fa fa-money';
        $this->middleware(function ($request, $next) {
            if (!in_array('payments', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(PaymentsDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/finance/payments'));
        
        $this->projects = Project::all();
        return $dataTable->render('admin.payments.index', $this->data);
    }

    public function create()
    {
        $this->projects = Project::orderBy('project_name')->get();
        $this->currencies = Currency::all();
//        adding vendors by Aqeel
        $this->vendors = Vendor::orderBy('name')->get();
        
        if (request()->get('project')) {
            $this->projectId = request()->get('project');
        }

        $this->paymentGateways = [
            'Authorize.net' => 'Authorize.net',
            'Bank Transfer' => 'Bank Transfer',
            'Check' => 'Check',
            'Cash or others' => 'Cash or others',
            'Paypal' => 'Paypal',
            'Stripe' => 'Stripe'
        ];

        return view('admin.payments.create', $this->data);
    }

    public function store(StorePayment $request)
    {
        $payment = new Payment();
        if(!is_null($request->currency_id)){
            $payment->currency_id = $request->currency_id;
        }
        else{
            $payment->currency_id = $this->global->currency_id;
        }
        if ($request->has('invoice_id')) {
            $invoice = Invoice::findOrFail($request->invoice_id);
            $payment->project_id = $invoice->project_id;
            $payment->invoice_id = $invoice->id;
            $payment->currency_id = $invoice->currency->id;
            
        } else if ($request->project_id != '') {
            $payment->project_id = $request->project_id;
        }

        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on =  Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');

        $payment->remarks = $request->remarks;
        $payment->vendor_id = $request->vendor_id;

        if ($request->hasFile('bill')) {
            $payment->bill = $request->bill->hashName();
            $request->bill->store('payment-receipt');
        }

        if ($request->has('invoice_id')) {
            $paidAmount = $invoice->amountPaid();
            if (($paidAmount + $request->amount) >= $invoice->total) {
                $invoice->status = 'paid';
                $this->createInvoiceExpense($invoice);
                 //$this->createInvoicePurchaseOrder($invoice);
                $payment->markup_amount = $invoice->markup_total;
                //restartPM2();
                // Payment Received Automation Mail
                if (($payment->project_id && $payment->project->client_id != null) || ($payment->invoice_id && $payment->invoice->client_id != null)) {
                    $clientId = ($payment->project_id && $payment->project->client_id != null) ? $payment->project->client_id : $payment->invoice->client_id;
                    $user = User::withoutGlobalScopes(['active', 'company'])->findOrFail($clientId);
                    if($user){
                        paymentReceivedAutomationMail($user);
                    }
                }
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();
        }
        
        $payment->save();



        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        // change invoice status if exists
        if ($payment->invoice) {
            $due = $payment->invoice->amountDue() + $payment->amount;
            if ($due <= 0) {
                $payment->invoice->status = 'paid';
                $this->createInvoiceExpense($payment->invoice);
                //$this->createInvoicePurchaseOrder($payment->invoice);
            } else if ($due >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            } else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        $payment->delete();

        return Reply::success(__('messages.paymentDeleted'));
    }

    public function edit($id)
    {
        $this->projects = Project::orderBy('project_name')->get();
        $this->currencies = Currency::all();
        $this->payment = Payment::findOrFail($id);
        return view('admin.payments.edit', $this->data);
    }

    public function update(UpdatePayments $request, $id)
    {

        $payment = Payment::findOrFail($id);
        if ($request->project_id != '') {
            $payment->project_id = $request->project_id;
        }
        $payment->currency_id = $request->currency_id;
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');
        $payment->status = $request->status;
        $payment->remarks = $request->remarks;
        if ($request->hasFile('bill')) {
            Files::deleteFile($payment->bill, 'payment-receipt');
            $payment->bill = $request->bill->hashName();
            $request->bill->store('payment-receipt');
        }
        $payment->save();

        // change invoice status if exists
        if ($payment->invoice) {
            if ($payment->invoice->amountDue() <= 0) {
                $payment->invoice->status = 'paid';
                $this->createInvoiceExpense($payment->invoice);
                //$this->createInvoicePurchaseOrder($payment->invoice);
                $payment->markup_amount = $payment->invoice->markup_total;

                // Payment Received Automation Mail
                if (($payment->project_id && $payment->project->client_id != null) || ($payment->invoice_id && $payment->invoice->client_id != null)) {
                    $clientId = ($payment->project_id && $payment->project->client_id != null) ? $payment->project->client_id : $payment->invoice->client_id;
                    $user = User::withoutGlobalScopes(['active', 'company'])->find($clientId);
                    if($user){
                        paymentReceivedAutomationMail($user);
                    }
                }

            } else if ($payment->invoice->amountDue() >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            } else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }

    public function payInvoice($invoiceId)
    {
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->paidAmount = $this->invoice->amountPaid();


        if ($this->invoice->status == 'paid') {
            return "Invoice already paid";
        }

        return view('admin.payments.pay-invoice', $this->data);
    }

    public function importExcel(ImportPayment $request)
    {
        if ($request->hasFile('import_file')) {
            $path = $request->file('import_file')->getRealPath();
            $data = Excel::load($path)->get();

            if ($data->count()) {

                foreach ($data as $key => $value) {

                    if ($request->currency_character) {
                        $amount = substr($value->amount, 1);
                    } else {
                        $amount = substr($value->amount, 0);
                    }

                    $amount = str_replace(',', '', $amount);
                    $amount = str_replace(' ', '', $amount);

                    $arr[] = [
                        'paid_on' => Carbon::parse($value->date)->format('Y-m-d'),
                        'amount' => $amount,
                        'currency_id' => $this->global->currency_id,
                        'status' => 'complete'
                    ];
                }

                if (!empty($arr)) {
                    DB::table('payments')->insert($arr);
                }
            }
        }

        return Reply::redirect(route('admin.payments.index'), __('messages.importSuccess'));
    }

    public function downloadSample()
    {
        return response()->download(public_path() . '/sample/payment-sample.csv');
    }

    public function show($id)
    {
        $this->payment = Payment::with('invoice', 'project', 'currency')->find($id);
        return view('admin.payments.show', $this->data);
    }
    
     // on paid invoices create prurchase order automatically
    function createInvoicePurchaseOrder($invoice){
        $vendor_pro = [];
        
        foreach ($invoice->items as $key => $item){
            if (!is_null($item) && !is_null($item->product_id)) {
                $product = Product::find($item->product_id);
                if(!is_null($product) && !is_null($product->vendor_id)) {
                    $vendor_pro[$product->vendor_id][] = $item;
                    
                }
            }
        }
        if($vendor_pro) {
            foreach ($vendor_pro as $key => $items) {
                
                $vendor = ClientVendorDetails::find($key);
                
                if(!is_null($vendor)) {
                    
                    $po_order = PurchaseOrder::withoutGlobalScopes([CompanyScope::class])->latest()->first();
                    $po = new PurchaseOrder();
                    $po->purchase_order_number =  date('Y').'-'.sprintf("%06d",$po_order->id+1);
                    $po->vendor_id = $vendor->id;
                    $po->address = $vendor->company_address;
                    $po->email = $vendor->vendor_email;
                    $po->contact = $vendor->vendor_mobile;
                    $po->company = $vendor->company_name;
                    $po->shipping_address = $vendor->vendor_shipping_address;
                    //$po->account_no = '';
                    $po->purchase_order_date = Carbon::now()->format('Y-m-d');
                    //$po->terms = $terms;
                    $po->memo_order = $invoice->note;
                    $po->product_subtotal = $invoice->sub_total;
                    $po->total_amount  = round($invoice->total , 2);
                    //$po->discount = round($invoice->discount_value, 2);
                    //$po->discount_type = $invoice->discount_type;
                    $po->project_id = $invoice->project_id;
                    $po->invoice_id = $invoice->id;
                    //$po->status_id = 2;
                    $po->save();   
                    
                    // was created auto some items, so del forst then create
                    PurchaseOrderItems::where('purchase_order_id', $po->id)->delete();
                    $sub_total = 0;
                    $total = 0;
                    
                    foreach ($items as $key1 => $item) {
                        
                        $itm = new PurchaseOrderItems();
                        $itm->purchase_order_id = $po->id;
                        $itm->item_name = $item->item_name;
                        $itm->item_summary = $item->item_summary;
                        $itm->type = 'item';
                        $itm->quantity = $item->quantity;
                        $itm->unit_price = $item->unit_price;
                        $itm->amount = $item->quantity*$item->unit_price; //$item->amount;
                        $itm->product_id = $item->product_id;
                        //$itm->taxes = $item->taxes;
                        $itm->save(); 
                        
                        $sub_total = $sub_total + ($item->quantity*$item->unit_price);
                        $total = $total + ($item->quantity*$item->unit_price);
                        
                    }
//                    if($po->discount > 0) {
//                        $total = $total - $po->discount;
//                    }
                     $po_up = PurchaseOrder::find($po->id);
                     $po_up->product_subtotal = round($sub_total , 2);
                     $po_up->total_amount = round($total , 2);
                     $po_up->save();  
                    
                }
               
            }
        }
        
    }
    
      // on paid invoices create expenses  automatically
    function createInvoiceExpense($invoice){
        $expense = new Expense();
        $expense->item_name = $invoice->invoice_number;
        $expense->purchase_date = $invoice->due_date;
        $expense->price = $invoice->total;
        $expense->currency_id = $invoice->currency_id;
        $expense->expenses_type = null;
        $expense->project_id = $invoice->project_id;
        $expense->user_id = $this->user->id;
        $expense->status = 'approved';
        $expense->created_type = 'auto';
        $expense->save();
        
        
    }

}
