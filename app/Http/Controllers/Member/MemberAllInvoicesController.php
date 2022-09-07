<?php

namespace App\Http\Controllers\Member;

use App\CreditNotes;
use App\Currency;
use App\Estimate;
use App\Helper\Reply;
use App\Http\Requests\Admin\Client\StoreShippingAddressRequest;
use App\Http\Requests\InvoiceFileStore;
use App\Http\Requests\Invoices\StoreInvoice;
use App\Http\Requests\Invoices\UpdateInvoice;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\Notifications\NewInvoice;
use App\Product;
use App\Project;
use App\Proposal;
use App\Tax;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Yajra\DataTables\Facades\DataTables;
use App\PurchaseOrder;
use App\PurchaseOrderItems;
use App\ClientVendorDetails;
use App\Scopes\CompanyScope;
use App\Expense;
use App\Payment;
use App\SalescategoryType;
use App\CodeType;
use App\LineItemGroup;

class MemberAllInvoicesController extends MemberBaseController
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
        if (!$this->user->can('view_invoices')) {
            abort(403);
        }
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/all-invoices'));
        $this->projects = Project::all();
        $this->totalRecords = Invoice::count();
        return view('member.invoices.index', $this->data);
    }

    public function data(Request $request)
    {
        $firstInvoice = Invoice::latest()->first();
        $invoices = Invoice::with(['project' => function ($q) {
            $q->withTrashed();
            $q->select('id', 'project_name', 'client_id');
        }, 'currency:id,currency_symbol,currency_code', 'project.client'])
            ->select('id', 'project_id', 'client_id', 'invoice_number', 'currency_id', 'total', 'status', 'issue_date', 'credit_note', 'show_shipping_address', 'send_status', 'invoices.refund_status', 'invoices.tags');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '>=', $request->startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '<=', $request->endDate);
        }

        if ($request->status != 'all' && !is_null($request->status)) {
            $invoices = $invoices->where('invoices.status', '=', $request->status);
        }

        if ($request->projectID != 'all' && !is_null($request->projectID)) {
            $invoices = $invoices->where('invoices.project_id', '=', $request->projectID);
        }

        $invoices = $invoices->whereHas('project', function ($q) {
            $q->whereNull('deleted_at');
        }, '>=', 0)->orderBy('invoices.id', 'desc')->get();

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($firstInvoice) {
                $action = '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">' . __('app.action') . ' <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">';

                if ($this->user->can('view_invoices')) {
                    $action .= '<li><a href="' . route("member.all-invoices.download", $row->id) . '"><i class="fa fa-download"></i> ' . __('app.download') . '</a></li>';
                }
                if ($row->status == 'paid') {
                    $action .= ' <li><a href="javascript:" data-invoice-id="' . $row->id . '" class="invoice-upload" data-toggle="modal" data-target="#invoiceUploadModal"><i class="fa fa-upload"></i> ' . __('app.upload') . ' </a></li>';
                }

                if ($row->status != 'draft') {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sendButton"><i class="fa fa-send"></i> ' . __('app.send') . '</a></li>';
                }

                if (($row->status == 'unpaid' || $row->status == 'draft') && $this->user->can('edit_invoices')) {
                    $action .= '<li><a href="' . route("member.all-invoices.edit", $row->id) . '"><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                }

                if ($row->status != 'paid') {
                    if (in_array('payments', $this->user->modules)  && $this->user->can('add_payments') && $row->status != 'draft') {
                        $action .= '<li><a href="' . route("member.payments.payInvoice", [$row->id]) . '" data-toggle="tooltip" ><i class="fa fa-plus"></i> ' . __('modules.payments.addPayment') . '</a></li>';
                    }
                }

                if ($row->clientdetails) {
                    if (!is_null($row->clientdetails->shipping_address)) {
                        if ($row->show_shipping_address === 'yes') {
                            $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye-slash"></i> ' . __('app.hideShippingAddress') . '</a></li>';
                        } else {
                            $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye"></i> ' . __('app.showShippingAddress') . '</a></li>';
                        }
                    } else {
                        $action .= '<li><a href="javascript:addShippingAddress(' . $row->id . ');"><i class="fa fa-plus"></i> ' . __('app.addShippingAddress') . '</a></li>';
                    }
                } else {
                    if ($row->project->clientdetails) {
                        if (!is_null($row->project->clientdetails->shipping_address)) {
                            if ($row->show_shipping_address === 'yes') {
                                $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye-slash"></i> ' . __('app.hideShippingAddress') . '</a></li>';
                            } else {
                                $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye"></i> ' . __('app.showShippingAddress') . '</a></li>';
                            }
                        } else {
                            $action .= '<li><a href="javascript:addShippingAddress(' . $row->id . ');"><i class="fa fa-plus"></i> ' . __('app.addShippingAddress') . '</a></li>';
                        }
                    }
                }

                if ($this->user->can('delete_invoices') && $firstInvoice->id == $row->id) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sa-params"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }
                if ($firstInvoice->id != $row->id && $row->status == 'unpaid' && $this->user->can('edit_invoices')) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip" title="' . __('app.cancel') . '"  data-invoice-id="' . $row->id . '" class="sa-cancel"><i class="fa fa-times"></i> ' . __('app.cancel') . '</a></li>';
                }
                
                if(in_array('invoicesrefund',$this->user->modules)){
                    if ($row->status == 'paid') {
                        $action .= '<li><a href="javascript:;" class="invoice-refund" data-toggle="tooltip" data-invoice-id="' . $row->id . '"  data-original-title="Refund"><i class="fa fa-undo" aria-hidden="true"></i> Refund </a></li>';
                    }
                }
               
                
                $action .= '</ul>
              </div>
              ';

                return $action;
            })
            ->editColumn('project_name', function ($row) {
                if ($row->project_id) {
                    return '<a href="' . route('member.projects.show', $row->project_id) . '">' . ucfirst($row->project->project_name) . '</a>';
                }

                return '--';
            })
            ->editColumn('invoice_number', function ($row) {
                return '<a href="' . route('member.all-invoices.show', $row->id) . '">' . ucfirst($row->invoice_number) . '</a>';
            })
            ->editColumn('status', function ($row) {
                $status = '';
                if ($row->credit_note) {
                    $status .= '<label class="label label-warning">' . strtoupper(__('app.credit-note')) . '</label>';
                } else {
                    if ($row->status == 'unpaid') {
                        $status .= '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                    } elseif ($row->status == 'paid') {
                        $status .= '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                    } elseif ($row->status == 'draft') {
                        $status .= '<label class="label label-primary">' . strtoupper($row->status) . '</label>';
                    } elseif ($row->status == 'canceled') {
                        $status .= '<label class="label label-danger">' . strtoupper(__('app.canceled')) . '</label>';
                    } elseif ($row->status == 'review') {
                        return '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                    } else {
                        $status .= '<label class="label label-info">' . strtoupper(__('modules.invoices.partial')) . '</label>';
                    }
                }
                if (!$row->send_status && $row->status != 'draft') {
                    $status .= '<br><br><label class="label label-inverse">' . strtoupper(__('modules.invoices.notSent')) . '</label>';
                }
                
                if ($row->refund_status == 'refund') {
                    $status.= '<label class="label label-success">Refund</label>';
                } else if($row->refund_status == 'partial_refund'){
                     $status.= '<label class="label label-success">Partial Refund</label>';
                }
                
                return $status;
            })
            ->editColumn('total', function ($row) {
                $currencyCode = ' (' . $row->currency->currency_code . ') ';
                $currencySymbol = $row->currency->currency_symbol;
                
                return '<div class="text-right">' . __('app.total') . ': ' . currency_position($row->total, $currencySymbol). '<br><span class="text-danger"> Deposit Request :</span> ' . currency_position($row->deposit_req, $currencySymbol) . '<br><span class="text-success">' . __('app.paid') . ':</span> ' . currency_position($row->amountPaid(), $currencySymbol)  . '<br><span class="text-danger">' . __('app.unpaid') . ':</span> ' . currency_position($row->amountDue(), $currencySymbol) . '<br><span class="text-danger"> Refund :</span> ' . currency_position($row->amountRefund(), $currencySymbol) . '</div>';

                //return '<div class="text-right">Total: ' . currency_position($row->total, $currencySymbol) . $currencyCode . '<br>Paid: ' . currency_position($row->amountPaid(), $currencySymbol) . $currencyCode . '<br>Due: ' . currency_position($row->amountDue(), $currencySymbol) . $currencyCode . '</div>';
            })
            ->editColumn(
                'issue_date',
                function ($row) {
                    return $row->issue_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
                
                ->editColumn('name', function ($row) {
                if ($row->client_id){
                    if($row->client && $row->client->image) {
                         $image = '<img src="' . $row->client->image_url . '"alt="user" class="img-circle" width="30" height="30"> ';
                    } else {
                         $image = '<span class="nameletter">'.company_initials().'</span>';
                    }

                    if($row->client) {
                        return  '<div class="row truncate"><div class="col-sm-3 col-xs-4">' . $image . '</div><div class="col-sm-9 col-xs-8">' . ucwords($row->client->name) . '</div></div>';
                    } else {
                        return '--';
                    }
                    
                    //return $row->client->name;
                }
//                if ($row->project && $row->project->client) {
//                    return ucfirst($row->project->client->name);
//                }
//                if ($row->client_id != null && $row->client_id !=0) {
//                    $client = User::withoutGlobalScope(CompanyScope::class)->find($row->client_id);
//                    if ($client) {
//                        return ucfirst($client->name);
//                    }
//
//                    return '--';
//                }
//                if ($row->estimate && $row->estimate->client) {
//                    return ucfirst($row->estimate->client->name);
//                }
                return '--';
            })
            ->editColumn('vendor_name', function ($row) {
                if ($row->vendor_id) {
                    return $row->vendor->name;
                }

                return '--';
            })
            
            ->editColumn('tags', function ($row) {
                $tags = '';
                if($row->tags) {
                    $tags = $row->tags ? json_decode($row->tags) : array();
                    if($tags) {
                        $tags = implode(', ', $tags);
                    }
                    
                }
                return $tags;
            })
            ->rawColumns(['name','project_name', 'action', 'status', 'invoice_number', 'total'])
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code')
            ->removeColumn('project_id')
            ->make(true);
    }

    public function download($id)
    {
        //        header('Content-type: application/pdf');

        $this->invoice = Invoice::findOrFail($id);
        $this->paidAmount = $this->invoice->getPaidAmount();
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
            if ($this->invoice->discount > 0 && $this->invoice->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->invoice->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                } else {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->global;
        
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
        

        $this->invoiceSetting = InvoiceSetting::first();
        //        return view('invoices.'.$this->invoiceSetting->template, $this->data);

        $pdf = app('dompdf.wrapper');
        $this->company = $this->invoice->company;
        //$pdf->loadView('invoices.' . $this->invoiceSetting->template, $this->data);
        // genral invoice pdf for all
        $pdf->loadView('invoices.invoice-general', $this->data);
        $filename = $this->invoice->invoice_number;
        //       return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }

    public function destroy($id)
    {
        $firstInvoice = Invoice::orderBy('id', 'desc')->first();
        if ($firstInvoice->id == $id) {
            if (CreditNotes::where('invoice_id', $id)->exists()) {
                CreditNotes::where('invoice_id', $id)->update(['invoice_id' => null]);
            }
            Invoice::destroy($id);
            return Reply::success(__('messages.invoiceDeleted'));
        } else {
            return Reply::error(__('messages.invoiceCanNotDeleted'));
        }
    }

    public function create()
    {
        if (!$this->user->can('add_invoices')) {
            abort(403);
        }

        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->vendors = Vendor::all();
        
        $lastInvoice = Invoice::count();
        do {
            $lastInvoice += 1;
            $exists = false;
            $invoice = Invoice::where('invoice_number', $lastInvoice)->first();
            if($invoice) {
                $exists = true;
            }
        } while ($exists);
        $this->lastInvoice = $lastInvoice;
        
        //$this->lastInvoice = Invoice::count() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        
        $default_project_id = 0;
        if(session()->get('project_id')) {
            $default_project_id = session()->get('project_id');
        }
        
        $review_products = [];
        if(session()->get('review_products')) {
            $review_products = session()->get('review_products');
        }
        
        session()->forget('project_id');
        session()->forget('review_products');
        
        $this->default_project_id = $default_project_id;
        $this->review_products = $review_products;
        
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        
        $this->clients = User::allClients();
        if (request('type') == "timelog") {
            $this->startDate = Carbon::now($this->global->timezone)->subDays(7);
            $this->endDate = Carbon::now($this->global->timezone);
            return view('admin.invoices.create-invoice', $this->data);
        }
        
         //$this->projects = Project::projectNames();
        $this->salescategories = $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        return view('member.invoices.create', $this->data);
    }

    public function store(StoreInvoice $request)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
        $productImage = $request->file('product_img');
        $product_ids = $request->input('product_id');
        $cost_per_item = $request->input('cost_per_item');
        $amount = $request->input('amount');
        $quantity = $request->input('quantity');
        $tax = request()->input('taxes');
        $group = request()->input('groups');
        $markups = $request->input('markup');
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        $invoice_item_type = $request->input('invoice_item_type');
        
        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && (intval($qty) < 1)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }
        
        $amount = $request->input('amount') ? $request->input('amount') : [];
        $tax = $request->input('taxes');
        $group = $request->input('groups');
        $invoice_type = $request->input('invoice_type');
        $vendor_id = $request->input('vendor_id');
        
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
        
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        
        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }


        $invoice = new Invoice();
        $invoice->project_id = $request->project_id ?? null;
        //$invoice->client_id = $request->project_id == '' && $request->has('client_id') ? $request->client_id : null;
        $invoice->client_id =  $request->client_id ? $request->client_id : null;
        //$invoice->invoice_number = Invoice::count() + 1;
        $invoice->invoice_number = $request->invoice_number;
        
        $invoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total = round($request->sub_total, 2);
        $invoice->discount = round($request->discount_value, 2);
        $invoice->discount_type = $request->discount_type;
        
        $invoice->card_processing_value = round($request->card_processing_value, 2);
        $invoice->card_processing_type = $request->card_processing_type ? $request->card_processing_type :'percent';
        
        $invoice->total = round($request->total, 2);
        $invoice->total_tax = round($request->total_tax, 2);
        
        $invoice->currency_id = $request->currency_id;
        $invoice->recurring = $request->recurring_payment;
        $invoice->billing_frequency = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        $invoice->billing_interval = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        $invoice->billing_cycle = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        $invoice->note = $request->note;
        $invoice->show_shipping_address = $request->show_shipping_address;
        $invoice->vendor_id = $vendor_id;
        $invoice->invoice_type = $invoice_type;
        
        $invoice->tax_on_total = $request->tax_on_total ? json_encode($request->tax_on_total) : null;
        $invoice->shipping_total = round($request->shipping_total, 2);
        
        $invoice->deposit_request = round($request->deposit_request, 2);
        $invoice->deposit_request_type = $request->deposit_request_type ? $request->deposit_request_type : 'percent';
        $invoice->deposit_req = round($request->deposit_req, 2);
        //$invoice->tags = json_encode($request->tags);
        $invoice->combine_line_items = $combine_line_items;
        
        
        $invoice->tags = json_encode(array());
        if($request->tags) {
            $invoice->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        $invoice->save();
        
        $markup_total = 0;
        
        
        foreach ($items as $key => $item) :
            
             if(isset($sale_prices[$key]) && isset($markup_fix[$key]) && $markup_fix[$key] > 0) {
                        $markup_total += ($sale_prices[$key] + $markup_fix[$key]);
                } else if (isset($sale_prices[$key]) && isset($markups[$key]) && $markups[$key] > 0){
                        $markup_total += ($sale_prices[$key]/((100 + $markups[$key])/100));
                }

            $fileName = null;
            $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
            if ($existKey){
                $file = $productImage[$key]->getClientOriginalName();
                $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                $fileName = time().mt_rand().".".$extension;
            }
            $invoiceItem = InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'picture' => $pictures[$key],
                    'product_id' => $product_ids[$key],
                    'type' => 'item',
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                    'group_id' => $group[$key]?$group[$key]:null,
                    'markup' => $markups[$key]?$markups[$key]:'0.00',
                    'markup_fix' => $markup_fix[$key]?$markup_fix[$key]:'0.00',
                    'sale_price' => $sale_prices[$key]?$sale_prices[$key]:'0.00',
                    'shipping_price' => $shipping_prices[$key]?$shipping_prices[$key]:'0.00',
                    'invoice_item_type' => $invoice_item_type[$key]?$invoice_item_type[$key]:'product',
                    'product_image' => $fileName,
                ]);

            if ($existKey) {
                $directory = "user-uploads/invoice-items/products/$invoiceItem->id";
                if (!File::exists(public_path($directory))) {
                    $result = File::makeDirectory(public_path($directory), 0775, true);
                }
                $imageFilePath = "$directory/$fileName";

                File::move($productImage[$key], public_path($imageFilePath));
                $invoiceItem->save();
            }


        endforeach;
        
        $this->createInvoicePurchaseOrder($invoice);
        
        if ($request->estimate_id) {
            $estimate = Estimate::findOrFail($request->estimate_id);
            $estimate->status = 'accepted';
            $estimate->save();
        }

        if ($request->proposal_id) {
            $proposal = Proposal::findOrFail($request->proposal_id);
            $proposal->invoice_convert = 1;
            $proposal->save();
        }

        if ($request->has('shipping_address')) {
            $client = $invoice->clientdetails;
            $client->shipping_address = $request->shipping_address;

            $client->save();
        }
        
        //set milestone paid if converted milestone to invoice
        if ($request->milestone_id != '') {
            $milestone = ProjectMilestone::findOrFail($request->milestone_id);
            $milestone->invoice_created = 1;
            $milestone->invoice_id = $invoice->id;
            $milestone->save();
        }

        //log search
        $this->logSearchEntry($invoice->id, 'Invoice ' . $invoice->invoice_number, 'admin.all-invoices.show', 'invoice');

        return Reply::redirect(route('member.all-invoices.index'), __('messages.invoiceCreated'));
    }

    public function edit($id)
    {
        if (!$this->user->can('edit_invoices')) {
            abort(403);
        }

        $this->invoice = Invoice::findOrFail($id);
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        
        $tags = $this->invoice->tags ? json_decode($this->invoice->tags) : array();
        $this->invoice->tags = $tags;
        
        if($tags) {
            $this->invoice->tags = array_values(array_unique($tags));
        }

        if ($this->invoice->status == 'paid') {
            abort(403);
        }

        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        $this->clients = User::allClients();
        if ($this->invoice->project_id != '') {
            $companyName = Project::where('id', $this->invoice->project_id)->with('clientdetails')->first();
            $this->companyName = $companyName->clientdetails ? $companyName->clientdetails->company_name : '';
        }
        
        $this->salescategories = $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        $this->invoiceSetting = InvoiceSetting::first();

        return view('member.invoices.edit', $this->data);
    }

    public function update(UpdateInvoice $request, $id)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
        $productImage = $request->file('product_img');
        $product_ids = $request->input('product_id');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        //$type = $request->input('type');
        $tax = $request->input('taxes');
        $group = request()->input('groups');
        
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
        
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        $invoice_item_type = $request->input('invoice_item_type');

        $old_items = $request->input('old_items') ?? [];
        
        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && $qty < 1) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }

        $invoice = Invoice::findOrFail($id);

        if ($invoice->status == 'paid') {
            return Reply::error(__('messages.invalidRequest'));
        }
        
        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }

        $invoice->project_id = $request->project_id ?? null;
        //$invoice->client_id = $request->project_id == '' && $request->has('client_id') ? $request->client_id : null;
        $invoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total = round($request->sub_total, 2);
        $invoice->discount = round($request->discount_value, 2);
        $invoice->discount_type = $request->discount_type;
        
        $invoice->card_processing_value = round($request->card_processing_value, 2);
        $invoice->card_processing_type = $request->card_processing_type ? $request->card_processing_type : 'percent';
        
        $invoice->total = round($request->total, 2);
        $invoice->total_tax = round($request->total_tax, 2);
        $invoice->currency_id = $request->currency_id;
        $invoice->status = $request->status;
        
        //$invoice->recurring = $request->recurring_payment;
        //$invoice->billing_frequency = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        //$invoice->billing_interval = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        //$invoice->billing_cycle = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        
        $invoice->note = $request->note;
        $invoice->tax_on_total = $request->tax_on_total ? json_encode($request->tax_on_total) : null;
        $invoice->shipping_total = round($request->shipping_total, 2);
        //$invoice->show_shipping_address = $request->show_shipping_address;
        $invoice->deposit_request = round($request->deposit_request, 2);
        $invoice->deposit_request_type = $request->deposit_request_type ? $request->deposit_request_type : 'percent';
        $invoice->deposit_req = round($request->deposit_req, 2);
        //$invoice->tags = json_encode($request->tags);
        
        $invoice->combine_line_items = $combine_line_items;
        
        $invoice->tags = json_encode(array());
        if($request->tags) {
            $invoice->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        $invoice->save();

        $invoiceItemQuery = InvoiceItems::query()->where('invoice_id', $invoice->id)->where('product_image','!=',null);
        $oldInvoiceItemImg = $invoiceItemQuery->pluck('product_image','id')->toArray();
        try {
            DB::beginTransaction();
            // delete and create new
            InvoiceItems::where('invoice_id', $invoice->id)->delete();

            foreach ($items as $key => $item) :

                $fileName = null;
                $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                if ($existKey){
                    $file = $productImage[$key]->getClientOriginalName();
                    $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);

                    $fileName = time().mt_rand().".".$extension;
                }

                $fname = isset($old_items[$key]) && !empty($old_items[$key]) ? $old_items[$key] : 'N/A';
                $oldPath = public_path('user-uploads/invoice-items/products/'.$fname);
                if(file_exists($oldPath)){
                    $fileName = $oldInvoiceItemImg[$old_items[$key]];
                }

                $invoiceItem = InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'picture' => $pictures[$key],
                    'product_id' => $product_ids[$key],
                    'type' => 'item',
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                    'group_id' => $group[$key]?$group[$key]:null,
                    'markup' => $markups[$key]?$markups[$key]:'0.00',
                    'markup_fix' => $markup_fix[$key]?$markup_fix[$key]:'0.00',

                    'sale_price' => $sale_prices[$key]?$sale_prices[$key]:'0.00',
                    'shipping_price' => $shipping_prices[$key]?$shipping_prices[$key]:'0.00',
                    'invoice_item_type' => $invoice_item_type[$key]?$invoice_item_type[$key]:'product',
                    'product_image' => $oldInvoiceItemImg[$key] ?? $fileName,
                ]);

                if(file_exists($oldPath)){
                    $newPath = public_path('user-uploads/invoice-items/products/'.$invoiceItem->id);
                    File::moveDirectory($oldPath,$newPath);
                }

                if ($existKey) {
                    $directory = "user-uploads/invoice-items/products/$invoiceItem->id";
                    if (!File::exists(public_path($directory))) {
                        $result = File::makeDirectory(public_path($directory), 0775, true);
                    }
                    $imageFilePath = "$directory/$fileName";

                    File::move($productImage[$key], public_path($imageFilePath));
                    $invoiceItem->save();
                }
            endforeach;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage() . 'On Line NO: ' . $e->getLine());
        }
        
        

        if ($request->has('shipping_address')) {
            $client = $invoice->clientdetails;
            $client->shipping_address = $request->shipping_address;

            $client->save();
        }

        return Reply::redirect(route('member.all-invoices.index'), __('messages.invoiceUpdated'));
    }

    public function show($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->invoiceSetting = InvoiceSetting::first();
        
        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }
        $this->taxes = InvoiceItems::where('type', 'tax')
            ->where('invoice_id', $this->invoice->id)
            ->get();
        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();

    foreach ($items as $item) {
            if ($this->invoice->discount > 0 && $this->invoice->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->invoice->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {

                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        if ($this->invoiceSetting->shipping_taxed == 'no') {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price);
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                        }
                    } else {
                        if ($this->invoiceSetting->shipping_taxed == 'no') {
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
        $this->invoiceSetting = InvoiceSetting::first();
        return view('member.invoices.show', $this->data);
    }

    public function convertEstimate($id)
    {
        $this->pageTitle = 'Convert Estimate to Invoice';
        $this->estimateId = $id;
        $this->invoice = Estimate::with('items')->findOrFail($id);
        $this->vendors = Vendor::orderBy('name')->get();
        $this->lastInvoice = Invoice::count() + 1;
        
        $this->projects = Project::orderBy('project_name')->get();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        $this->clients = User::allClients();
        $this->salescategories = SalescategoryType::all();
        $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        $tags = $this->invoice->tags ? json_decode($this->invoice->tags) : array();
        $this->invoice->tags = $tags;
        
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        
        $default_project_id = 0;
        if(session()->get('project_id')) {
            $default_project_id = session()->get('project_id');
        }
        
        $review_products = [];
        if(session()->get('review_products')) {
            $review_products = session()->get('review_products');
        }
        
        session()->forget('project_id');
        session()->forget('review_products');
        
        $this->default_project_id = $default_project_id;
        $this->review_products = $review_products;
        
        $discount = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'discount';
        });

        $tax = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'tax';
        });

        $this->totalTax = $tax->sum('amount');
        $this->totalDiscount = $discount->sum('amount');

        
        if ($this->invoice->project_id != '') {
            $companyName = Project::where('id', $this->invoice->project_id)->with('clientdetails')->first();
            $this->companyName = $companyName->clientdetails ? $companyName->clientdetails->company_name : '';
        }
        return view('member.invoices.convert_estimate', $this->data);
    }

    public function addItems(Request $request)
    {
        //$this->items = Product::with('tax')->find($request->id);
        
        $this->items = Product::find($request->id);
        $this->items->afterLoad();
        $exchangeRate = Currency::find($request->currencyId);
        $this->cal_from = isset($request->cal_from)?$request->cal_from:'';

//        if ($this->items->total_amount != "") {
//            $this->items->price = floor($this->items->total_amount * $exchangeRate->exchange_rate);
//        } else {
//            $this->items->price = floor($this->items->price * $exchangeRate->exchange_rate);
//        }
        
         // Added By SB
        if(isset($this->items->cost_per_unit) && !empty($this->items->cost_per_unit)) {
            //$this->items->price = floor($this->items->cost_per_unit * $exchangeRate->exchange_rate);
            $price = $this->items->cost_per_unit * $exchangeRate->exchange_rate;
            $this->items->price = number_format((float)$price, 2, '.', '');
        } else {
            $this->items->price = 0;
        }
        // default image
        
        $this->fileUrl = asset('img/img-dummy.jpg');
        $this->fileName = '';
        
        if (!empty($this->items->picture)) {
            $pictures = json_decode($this->items->picture);
            if($pictures) {
                 $this->fileUrl = asset('user-uploads/products/'.$this->items->id.'/'.$pictures[0].'');
                 $this->fileName = $pictures[0];
            }
        }
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $view = view('member.invoices.add-item', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function paymentDetail($invoiceID)
    {
        $this->invoice = Invoice::findOrFail($invoiceID);

        return View::make('member.invoices.payment-detail', $this->data);
    }

    /**
     * @param InvoiceFileStore $request
     * @return array
     */
    public function storeFile(InvoiceFileStore $request)
    {
        $invoiceId = $request->invoice_id;
        $file = $request->file('file');

        $newName = $file->hashName(); // setting hashName name

        // Getting invoice data
        $invoice = Invoice::find($invoiceId);

        if ($invoice != null) {

            if ($invoice->file != null) {
                unlink(storage_path('app/public/invoice-files') . '/' . $invoice->file);
            }

            $file->move(storage_path('app/public/invoice-files'), $newName);

            $invoice->file = $newName;
            $invoice->file_original_name = $file->getClientOriginalName(); // Getting uploading file name;

            $invoice->save();
            return Reply::success(__('messages.fileUploadedSuccessfully'));
        }

        return Reply::error(__('messages.fileUploadIssue'));
    }

    public function checkShippingAddress()
    {
        if (request()->has('clientId')) {
            $user = User::findOrFail(request()->clientId);
            if (request()->showShipping == 'yes' && (is_null($user->client_details->shipping_address) || $user->client_details->shipping_address === '')) {
                $view = view('admin.invoices.show_shipping_address_input')->render();
                return Reply::dataOnly(['view' => $view]);
            } else {
                return Reply::dataOnly(['show' => 'false']);
            }
        } else {
            return Reply::dataOnly(['switch' => 'off']);
        }
    }

    public function toggleShippingAddress(Invoice $invoice)
    {
        if ($invoice->show_shipping_address === 'yes') {
            $invoice->show_shipping_address = 'no';
        } else {
            $invoice->show_shipping_address = 'yes';
        }

        $invoice->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function shippingAddressModal(Invoice $invoice)
    {
        $clientId = $invoice->clientdetails ? $invoice->clientdetails->user_id : $invoice->project->clientdetails->user_id;

        return view('sections.add_shipping_address', ['clientId' => $clientId]);
    }

    public function addShippingAddress(StoreShippingAddressRequest $request, User $user)
    {
        $user->client_details->shipping_address = $request->shipping_address;

        $user->client_details->save();

        return Reply::success(__('messages.addedSuccessfully'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function destroyFile(Request $request)
    {
        $invoiceId = $request->invoice_id;

        $invoice = Invoice::find($invoiceId);

        if ($invoice != null) {

            if ($invoice->file != null) {
                unlink(storage_path('app/public/invoice-files') . '/' . $invoice->file);
            }

            $invoice->file = null;
            $invoice->file_original_name = null;

            $invoice->save();
        }

        return Reply::success(__('messages.fileDeleted'));
    }

    public function getClientOrCompanyName($projectID = '')
    {
        $this->projectID = $projectID;

        if ($projectID == '') {
            $this->clients = User::allClients();
        } else {
            $companyName = Project::where('id', $projectID)->with('clientdetails')->first();
            $this->companyName = $companyName->clientdetails ? $companyName->clientdetails->company_name : '';
            $this->clientId = $companyName->clientdetails ? $companyName->clientdetails->user_id : '';
        }

        $list = view('member.invoices.client_or_company_name', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function cancelStatus(Request $request)
    {
        $invoice = Invoice::find($request->invoiceID);
        $invoice->status = 'canceled'; // update status as canceled
        $invoice->save();

        return Reply::success(__('messages.invoiceUpdated'));
    }

    public function sendInvoice($invoiceID)
    {
        $invoice = Invoice::with(['project', 'project.client'])->findOrFail($invoiceID);
        if ($invoice->project_id != null && $invoice->project_id != '') {
            $notifyUser = $invoice->project->client;
        } elseif ($invoice->client_id != null && $invoice->client_id != '') {
            $notifyUser = $invoice->client;
        }
        if (!is_null($notifyUser)) {
            $notifyUser->notify(new NewInvoice($invoice));
        }

        $invoice->send_status = 1;
        if ($invoice->status == 'draft') {
            $invoice->status = 'unpaid';
        }
        $invoice->save();
        return Reply::success(__('messages.updateSuccess'));
    }
    
    public function convertMilestone($id)
    {
        $this->pageTitle = 'app.menu.invoices';
        $this->invoice = ProjectMilestone::findOrFail($id);
        $this->lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->lastInvoice = Invoice::count() + 1;
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        return view('member.invoices.convert_milestone', $this->data);
    }

    //Aqeel Code
    public function getVendorName(){
        $vendors = Vendor::all();
        return response()->json($vendors);
    }
    
    // on invoices creation prurchase order automatically
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
    
    
    public function refund(Request $request, $id)
    {
        $this->invoice = Invoice::findOrFail($id);   
        $this->project_id = $request->project_id ? $request->project_id : '';
        
        
        return view('member.invoices.refund', $this->data);
    }
    
    public function refundUpdate(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id); 
        
        $amount = $request->amount ? $request->amount: 0;
        $gateway = $request->gateway ? $request->gateway: '';
        $paid_on = $request->paid_on ? $request->paid_on: '';
        $project_id = $request->project_id ? $request->project_id: '';
        
        
        
        if (is_numeric($amount) && $amount < 1) {
                return Reply::error('Amount should be a number.');
        }
        
        if (empty($gateway)) {
                return Reply::error('Please select gateway.');
        }
        
        $paidAmount = $invoice->amountPaid();
        $refundAmount = $invoice->amountRefund();
        
        if (($refundAmount + $request->amount) > $paidAmount) {
            return Reply::error('Refund amount should be less than paid amount.');
        }
        
        $payment = new Payment();
        
        $payment->project_id = $invoice->project_id;
        $payment->invoice_id = $invoice->id;
        $payment->currency_id = $invoice->currency->id;
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        //$payment->transaction_id = '';
        $payment->paid_on =  Carbon::createFromFormat('d/m/Y H:i', $paid_on)->format('Y-m-d H:i:s');
        $payment->remarks = $request->remarks;
        $payment->payment_type = 'refund';
        $payment->status = 'complete';
        
        $payment->save();
        
        if (($refundAmount + $request->amount) >= $paidAmount) {
            $invoice->refund_status = 'refund';
        } else {
            $invoice->refund_status = 'partial_refund';
        }
        
        $invoice->save();
        
        if($project_id != '') {
            return Reply::redirect(route('member.invoices-project.data', $project_id), 'Invoice Refunded.');
        } else {
            return Reply::redirect(route('member.client-invoice.index'), 'Invoice Refunded.');
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
