<?php

namespace App\Http\Controllers\Member;

use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Payments\StorePayment;
use App\Http\Requests\Payments\UpdatePayments;
use App\Invoice;
use App\Payment;
use App\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Product;
use App\PurchaseOrder;
use App\PurchaseOrderItems;
use App\ClientVendorDetails;
use App\Scopes\CompanyScope;
use App\Expense;

class MemberPaymentsController extends MemberBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.payments';
        $this->pageIcon = 'fa fa-money';
        $this->middleware(function ($request, $next) {
            if(!in_array('payments',$this->user->modules)){
                abort(403);
            }
            return $next($request);
        });

    }

    public function index() {
        if(!$this->user->can('view_payments')){
            abort(403);
        }
        
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/payments'));
        $this->projects = Project::all();
        return view('member.payments.index', $this->data);
    }

    public function data(Request $request) {
        $payments = Payment::leftJoin('projects', 'projects.id', '=', 'payments.project_id')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->select('payments.id', 'payments.project_id', 'payments.amount','projects.project_name', 'currencies.currency_symbol', 'currencies.currency_code', 'payments.status', 'payments.paid_on');

        if($request->startDate !== null && $request->startDate != 'null' && $request->startDate != ''){
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->format('Y-m-d');
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '>=', $startDate);
        }

        if($request->endDate !== null && $request->endDate != 'null' && $request->endDate != ''){
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->format('Y-m-d');
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '<=', $endDate);
        }

        if($request->status != 'all' && !is_null($request->status)){
            $payments = $payments->where('payments.status', '=', $request->status);
        }

        if($request->project != 'all' && !is_null($request->project)){
            $payments = $payments->where('payments.project_id', '=', $request->project);
        }

        $payments = $payments->orderBy('payments.id', 'desc')->get();

        return DataTables::of($payments)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '';
                if($this->user->can('edit_payments')){
                    $action.= '<a href="' . route("member.payments.edit", $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-info btn-circle"><i class="fa fa-pencil"></i></a>';
                }
                if($this->user->can('delete_payments')) {
                    $action .= '&nbsp;&nbsp;<a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-payment-id="' . $row->id . '" class="btn btn-danger btn-circle sa-params"><i class="fa fa-times"></i></a>';
                }
                return $action;
            })

            ->editColumn('project_id', function($row) {
                if($row->project_id != null){
                    return ucfirst($row->project->project_name);
                }
                else{
                    return '--';
                }

            })
            ->editColumn('status', function ($row) {
                if($row->status == 'pending'){
                    return '<label class="label label-warning">'.strtoupper($row->status).'</label>';
                }else{
                    return '<label class="label label-success">'.strtoupper($row->status).'</label>';
                }
            })
            ->editColumn('amount', function ($row) {
                return $row->currency_symbol . number_format((float)$row->amount, 2, '.', ''). ' ('.$row->currency_code.')';
            })
            ->editColumn(
                'paid_on',
                function ($row) {
                    if(!is_null($row->paid_on)){
                        return $row->paid_on->format($this->global->date_format .' '. $this->global->time_format);
                    }
                }
            )
            ->rawColumns(['action', 'status'])
            ->removeColumn('invoice_id')
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code')
            ->removeColumn('project_name')
            ->make(true);
    }

    public function create(){
        if(!$this->user->can('add_payments')){
            abort(403);
        }
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        return view('member.payments.create', $this->data);
    }

    public function store(StorePayment $request){
        $payment = new Payment();
        if($request->project_id != ''){
            $payment->project_id = $request->project_id;
            $payment->currency_id = $request->currency_id;
        }

        elseif($request->has('invoice_id') ){
            $invoice = Invoice::findOrFail($request->invoice_id);
            $payment->project_id = $invoice->project_id;
            $payment->invoice_id = $invoice->id;
            $payment->currency_id = $invoice->currency->id;
            $paidAmount = $invoice->amountPaid();
        }
        else{
            $currency = Currency::first();
            $payment->currency_id = $currency->id;
        }

        $payment->amount = round($request->amount,2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');
        $payment->remarks = $request->remarks;
        

        if($request->has('invoice_id') ){

            if(($paidAmount+$request->amount) >= $invoice->total){
                $invoice->status = 'paid';
                $payment->markup_amount = $invoice->markup_total;
                $this->createInvoiceExpense($invoice);
                //$this->createInvoicePurchaseOrder($invoice);
            }
            else{
                $invoice->status = 'partial';
            }
            $invoice->save();

        }
        
        $payment->save();


        return Reply::redirect(route('member.payments.index'), __('messages.paymentSuccess'));
    }

    public function destroy($id) {
        $payment = Payment::find($id);
        
        // change invoice status if exists
        if ($payment->invoice) {
            $due = $payment->invoice->amountDue() + $payment->amount;
            if ($due <= 0) {
                $payment->invoice->status = 'paid';
                $this->createInvoiceExpense($payment->invoice);
                //$this->createInvoicePurchaseOrder($payment->invoice);
            }
            else if ($due >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            }
            else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        $payment->delete();

        return Reply::success(__('messages.paymentDeleted'));
    }

    public function edit($id){
        if(!$this->user->can('edit_payments')){
            abort(403);
        }
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->payment = Payment::findOrFail($id);
        return view('member.payments.edit', $this->data);
    }

    public function update(UpdatePayments $request, $id){
        $payment = Payment::findOrFail($id);
        if($request->project_id != ''){
            $payment->project_id = $request->project_id;
        }
        $payment->currency_id = $request->currency_id;
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');
        $payment->status = $request->status;
        $payment->remarks = $request->remarks;
        $payment->save();

        // change invoice status if exists
        if ($payment->invoice) {
            if ($payment->invoice->amountDue() <= 0) {
                $payment->invoice->status = 'paid';
                $payment->markup_amount = $payment->invoice->markup_total;
                $this->createInvoiceExpense($payment->invoice);
                //$this->createInvoicePurchaseOrder( $payment->invoice);
            }
            else if ($payment->invoice->amountDue() >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            }
            else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        return Reply::redirect(route('member.payments.index'), __('messages.paymentSuccess'));
    }

    public function payInvoice($invoiceId){
        if (!$this->user->can('add_payments')) {
            abort(403);            
        }
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->paidAmount = $this->invoice->amountPaid();

        if($this->invoice->status == 'paid'){
            return "Invoice already paid";
        }

        return view('member.payments.pay-invoice', $this->data);
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
