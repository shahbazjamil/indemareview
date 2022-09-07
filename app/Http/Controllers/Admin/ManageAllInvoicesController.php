<?php

namespace App\Http\Controllers\Admin;

use App\ClientPayment;
use App\CreditNotes;
use App\Currency;
use App\DataTables\Admin\InvoicesDataTable;
use App\Estimate;
use App\Helper\Reply;
use App\Http\Requests\Admin\Client\StoreShippingAddressRequest;
use App\Http\Requests\InvoiceFileStore;
use App\Http\Requests\Invoices\StoreInvoice;
use App\Invoice;
use App\InvoiceItems;
use App\EstimateItem;
use App\InvoiceSetting;
use App\Notifications\PaymentReminder;
use App\OfflineInvoicePayment;
use App\Product;
use App\Project;
use App\Proposal;
use App\Tax;
use App\LineItemGroup;
use App\User;
use App\Vendor;
use App\VendorInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Invoices\UpdateInvoice;
use App\Notifications\NewInvoice;
use App\ProjectMilestone;
use App\ProjectTimeLog;
use App\Payment;
use App\Expense;

// bitsclan code start here
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Invoice as QuickbookInvoices;
use QuickBooksOnline\API\Facades\Customer;
use App\QuickbooksSettings;
use App\ClientDetails;
// bitsclan code end here

use App\PurchaseOrder;
use App\PurchaseOrderItems;
use App\ClientVendorDetails;
use App\Scopes\CompanyScope;

use App\SalescategoryType;
use App\CodeType;
use App\Mail\ClientInvoiceEmail;
use Illuminate\Support\Facades\Mail;

class ManageAllInvoicesController extends AdminBaseController
{
    // bitsclan code start here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    // bitsclan code end here

    public function __construct()
    {
        parent::__construct();
        // $this->pageTitle = 'app.menu.invoices';
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            if (!in_array('invoices', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });

        // Code Added By Adil.
        $this->__set('invoiceType', array(1 => "Client", 2 => "Vendor"));

        // bitsclan code start here
        // $this->setting = QuickbooksSettings::first(); 

        // if($this->setting->baseurl == 1){
        //     $this->envoirment = 'Development';
        // }else if($this->setting->baseurl == 2){
        //     $this->envoirment = 'Production';
        // }

        // if(!empty($this->setting->access_token)){
        //     $this->quickbook = DataService::Configure(array(
        //         'auth_mode' => 'oauth2',
        //         'ClientID' => $this->setting->client_id,
        //         'ClientSecret' => $this->setting->client_secret,
        //         'accessTokenKey' =>  $this->setting->access_token,
        //         'refreshTokenKey' => $this->setting->refresh_token,
        //         'QBORealmID' => $this->setting->realmid,
        //         'baseUrl' => $this->envoirment
        //     ));

        //     $OAuth2LoginHelper = $this->quickbook->getOAuth2LoginHelper();
        //     $accessToken = $OAuth2LoginHelper->refreshToken();
        //     $error = $OAuth2LoginHelper->getLastError();
        //     $this->quickbook->updateOAuth2Token($accessToken);

        //     QuickbooksSettings::where('id', $this->setting->id)->update([
        //         'refresh_token' => $accessToken->getRefreshToken(),
        //         'access_token' => $accessToken->getAccessToken()
        //     ]);
        // }

        // bitsclan code end here
    }

    // Client Invoice.
    public function index(InvoicesDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/finance/client-invoice'));
        
        $this->pageTitle = 'Client Invoice';
        $this->projects = Project::all();
        $this->clients = User::allClients();
        
        $this->totalRecords = Invoice::count();
        
        return $dataTable->render('admin.invoices.index', $this->data);
    }


    // Vendor Invoice.
    public function vendorIndex(InvoicesDataTable $dataTable)
    {
        $this->pageTitle = ' Vendor Invoice';
        $this->projects = Project::all();
        $this->clients = User::allClients(); // Take All Vendors Here.
        return $dataTable->render('admin.invoices.index', $this->data);
    }

    // Changes Datasource of dropdown
    public function dataSource($type)
    {
        if ($type == 'Vendor') {
            return DB::table('vendors')->select('id', 'name')->get();
        } else {
            return User::allClients();
        }
    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->invoiceSetting = InvoiceSetting::first();
        
        $this->creditNote = 0;
        if ($this->invoice->credit_note) {
            $this->creditNote = CreditNotes::where('invoice_id', $id)
                ->select('cn_number')
                ->first();
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

        $this->settings = $this->invoice->company;

        

        $pdf = app('dompdf.wrapper');
        $this->company = $this->invoice->company;
        // dynamic removed
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

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }
   

    public function download($id)
    {

        $this->invoice = Invoice::findOrFail($id);

        // Download file uploaded
        if ($this->invoice->file != null) {
            return response()->download(storage_path('app/public/invoice-files') . '/' . $this->invoice->file);
        }

        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

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

    //Aqeel Code
    public function getVendorName()
    {
        return DB::table('vendors')->select('id', 'name')->get();
    }

    // Returns Client Invoice.
    public function create()
    {
        $this->pageTitle = ' Create Client Invoice';
        $this->projects = Project::orderBy('project_name')->get();
        $this->currencies = Currency::all();
        $this->vendors = Vendor::orderBy('name')->get();
        
        
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
        
        $latestInvoice = DB::table('invoices')->where('company_id', company()->id)->orderByRaw('CONVERT(invoice_number, SIGNED) desc')->first();
        if($latestInvoice) {
            $invoice_number = trim($latestInvoice->invoice_number);
           if(is_numeric($invoice_number)) {
               $this->lastInvoice = $invoice_number + 1;
           }
        }
        
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
        $this->sel_project_id = 0;
        
        $this->products = Product::all();
        $this->clients = User::allClients();
        if (request('type') == "timelog") {
            
            $this->sel_project_id = request('sel_project_id') ? request('sel_project_id') : 0;
            
            $this->startDate = Carbon::now($this->global->timezone)->subDays(7);
            $this->endDate = Carbon::now($this->global->timezone);
            return view('admin.invoices.create-invoice', $this->data);
        }
        
        //$this->projects = Project::projectNames();
        $this->salescategories = $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
         
        
        return view('admin.invoices.create', $this->data);
    }

    //Aqeel Code for Vendor
    public function createVendor()
    {
        $this->pageTitle = 'Create Vendor Invoice';
        $this->__set(
            'paymentGateways',
            array(
                'Paypal' => 'Paypal',
                'Authorize.net' => 'Authorize.net',
                'Stripe' => 'Stripe',
                'Bank Transfer' => 'Bank Transfer',
                'Check' => 'Check',
                'Cash or others' => 'Cash or others',
            )
        );
        //$this->projects = Project::all();
        $this->currencies = Currency::all();
        // Need Vendor Invoice
        $this->lastInvoice = VendorInvoice::count() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }

        $this->taxes = Tax::all();
        //        $this->products = Product::all();
        $this->vendors = Vendor::all();
        if (request('type') == "timelog") {
            $this->startDate = Carbon::now($this->global->timezone)->subDays(7);
            $this->endDate = Carbon::now($this->global->timezone);
            return view('admin.invoices.create-invoice', $this->data);
        }
        
        
        
        
        return view('admin.invoices.createVendor', $this->data);
    }


    public function store(StoreInvoice $request)
    {

        $items = $request->input('item_name') ? $request->input('item_name') : [];   
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
        $productImage = $request->file('product_img');
        $product_ids = $request->input('product_id');
        $cost_per_item = $request->input('cost_per_item') ? $request->input('cost_per_item'): [];
        $amount = $request->input('amount') ? $request->input('amount') : [];
        $quantity = $request->input('quantity') ? $request->input('quantity') : [];
        $tax = request()->input('taxes');
        $group = request()->input('groups');
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
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
        
        
        if($request->deposit_req > $request->total) {
            return Reply::error('The deposit request amount should be less than the total amount.');
        }
        
        

        $items_array = array();


        // bitsclan code start here
        $qbo_id = null;


        $Line1                          = '';
        $City                           = '';
        $CountrySubDivisionCode         = '';
        $PostalCode                     = '';
        $Ship_Line1                     = '';
        $Ship_City                      = '';
        $ship_CountrySubDivisionCode    = '';
        $ship_PostalCode                = '';

        $this->quickbook = $this->QuickbookSettings();
        if($this->quickbook){
            try {

            $adminSetting = User::where('email', ($this->user->email))->first();
            $quickbook_items = array();
            foreach ($items as $key => $item) {
                
                $taxable = false;
                if($tax && array_key_exists($key, $tax)) {
                    $taxable = true;
                }
                
                $item_detail = Product::where('name', $item)->first();
                $invoice_item_detail = InvoiceItems::where('item_name', $item)->first();
                $estimate_detail = EstimateItem::where('item_name', $item)->first();

                if(!empty($item_detail)){
                    if(!empty($item_detail->qbo_id)){
                        $item_qbo_id = $item_detail->qbo_id;
                    }else{
                        $unitPrice = $sale_prices[$key] / $quantity[$key];
                        $shipping_line = 0;
                        if($shipping_prices[$key]) {
                            $shipping_line = $shipping_prices[$key];
                            //$unitPrice += $shipping_prices[$key];
                        }

                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => substr($item , 0, 100),
                            "Description" => $itemsSummary[$key],
                            "Active" => true,
                            "FullyQualifiedName" => $item,
                            "Taxable" => $taxable,
                            "UnitPrice" => $unitPrice,
                            "Type" => "NonInventory",
                            "IncomeAccountRef"=> [
                                "name" => "Sales - Company Service", 
                                "value" => $adminSetting->income_account
                            ],
                            "PurchaseDesc"=> $itemsSummary[$key],
                            "PurchaseCost"=> $unitPrice,
                            "TrackQtyOnHand" => false,
                            "InvStartDate"=> $dateTime
                        ]);

                        $resultingItemObj = $this->quickbook->Add($Items);
                        $error =  $this->quickbook->getLastError();
                        if($error){
                            //return Reply::error(__($error->getResponseBody()));
                        } 

                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                        $item_detail->qbo_id =  $item_qbo_id;
                        $item_detail->save();
                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $unitPrice,'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));
                    
                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item , 0, 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key]) ? $group[$key] : null,
                        'qbo_id' =>  $item_qbo_id,
                        'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                        'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                        'markup' => isset($markups[$key])?$markups[$key]:'0.00',
                        'markup_fix' => isset($markup_fix[$key])?$markup_fix[$key]:'0.00',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00',
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'services'
                    );

                    array_push($items_array, $item_arr);
                }

                elseif(!empty($invoice_item_detail)){

                    if(!empty($invoice_item_detail->qbo_id)){
                        $invoices_item_qbo = $invoice_item_detail->qbo_id;
                    }else{
                        
                        $unitPrice = $sale_prices[$key] / $quantity[$key];
                        $shipping_line = 0;
                        if(isset($shipping_prices[$key])) {
                            $shipping_line = $shipping_prices[$key];
                            //$unitPrice += $shipping_prices[$key];
                        }

                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => substr($item, 0, 100),
                            "Description" => $itemsSummary[$key],
                            "Active" => true,
                            "FullyQualifiedName" => $item,
                            "Taxable" => $taxable,
                            "UnitPrice" => $unitPrice,
                            "Type" => "NonInventory",
                            "IncomeAccountRef"=> [
                                "name" => "Sales - Company Service", 
                                "value" => $adminSetting->income_account
                            ],
                            "PurchaseDesc"=> $itemsSummary[$key],
                            "PurchaseCost"=> $unitPrice,
                            "TrackQtyOnHand" => false,
                            "InvStartDate"=> $dateTime
                        ]);

                        $resultingItemObj = $this->quickbook->Add($Items);
                        $error =  $this->quickbook->getLastError();
                        if($error){
                            //return Reply::error(__($error->getResponseBody()));
                        }
                        $invoices_item_qbo = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                        $invoice_item_detail->qbo_id =  $invoices_item_qbo;
                        $invoice_item_detail->save();

                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $unitPrice,'ItemRef' => array('value' => $invoices_item_qbo, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));
                    

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item , 0 , 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key]) ? $group[$key] : null,
                        'qbo_id' =>  $invoices_item_qbo,
                        'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                        'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                        'markup' => isset($markups[$key])?$markups[$key]:'0.00',
                        'markup_fix' => isset($markup_fix[$key])?$markup_fix[$key]:'0.00',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00',
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'services'
                    );

                    array_push($items_array, $item_arr);

                }

                elseif (!empty($estimate_detail)) {

                    if(!empty($estimate_detail->qbo_id)){
                        $item_qbo_id = $estimate_detail->qbo_id;
                    }else{
                        
                        $unitPrice = $sale_prices[$key] / $quantity[$key];
                        $shipping_line = 0;
                        if(isset($shipping_prices[$key])) {
                            $shipping_line = $shipping_prices[$key];
                            //$unitPrice += $shipping_prices[$key];
                        }

                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => substr($item, 0, 100) ,
                            "Description" => $itemsSummary[$key],
                            "Active" => true,
                            "FullyQualifiedName" => $item,
                            "Taxable" => $taxable,
                            "UnitPrice" => $unitPrice,
                            "Type" => "NonInventory",
                            "IncomeAccountRef"=> [
                                "name" => "Sales - Company Service", 
                                "value" => $adminSetting->income_account
                            ],
                            "PurchaseDesc"=> $itemsSummary[$key],
                            "PurchaseCost"=> $unitPrice,
                            "TrackQtyOnHand" => false,
                            "InvStartDate"=> $dateTime
                        ]);

                        $resultingItemObj = $this->quickbook->Add($Items);
                        $error =  $this->quickbook->getLastError();
                        if($error){
                            //return Reply::error(__($error->getResponseBody()));
                        }       
                        $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                        $estimate_detail->qbo_id = $product_qbo_id;
                        $estimate_detail->save();

                        $item_qbo_id = $resultingItemObj->Id;
                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $unitPrice,'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item, 0 , 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key]) ? $group[$key] : null,
                        'qbo_id' =>  $item_qbo_id,
                        'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                        'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                        'markup' => isset($markups[$key])?$markups[$key]:'0.00',
                        'markup_fix' => isset($markup_fix[$key])?$markup_fix[$key]:'0.00',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00',
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'services'
                    );

                    array_push($items_array, $item_arr);
                    
                }

                else{
                    
                    $unitPrice = $sale_prices[$key] / $quantity[$key];
                    $shipping_line = 0;
                       if(isset($shipping_prices[$key])) {
                            $shipping_line = $shipping_prices[$key];
                            //$unitPrice += $shipping_prices[$key];
                        }

                    $dateTime = new \DateTime('NOW');
                    $Items = Item::create([
                        "Name" => substr($item, 0, 100),
                        "Description" => $itemsSummary[$key],
                        "Active" => true,
                        "FullyQualifiedName" => $item,
                        "Taxable" => $taxable,
                        "UnitPrice" => $unitPrice,
                        "Type" => "NonInventory",
                        "IncomeAccountRef"=> [
                            "name" => "Sales - Company Service", 
                            "value" => $adminSetting->income_account
                        ],
                        "PurchaseDesc"=> $itemsSummary[$key],
                        "PurchaseCost"=> $unitPrice,
                        "TrackQtyOnHand" => false,
                        "InvStartDate"=> $dateTime
                    ]);

                    $resultingItemObj = $this->quickbook->Add($Items);
                    $error =  $this->quickbook->getLastError();
                    if($error){
                        //return Reply::error(__($error->getResponseBody()));
                    }
                    
                    $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : ''; 

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $unitPrice,'ItemRef' => array('value' => $product_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));

                    array_push($quickbook_items, $item_to_be_pushed);


                    $item_arr = array(
                        'item_name' => substr($item, 0, 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key]) ? $group[$key] : null,
                        'qbo_id' =>  $product_qbo_id,
                        'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                        'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                        'markup' => isset($markups[$key])?$markups[$key]:'0.00',
                        'markup_fix' => isset($markup_fix[$key])?$markup_fix[$key]:'0.00',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00',
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'services'
                    );

                    array_push($items_array, $item_arr);

                }
            
            }

            $id = $request->input('client_id');
            $client_qbo = ClientDetails::where('user_id', $id)->first();
            $client_email = $client_qbo->emai;

            if(!empty($client_qbo->qbo_id)){
                $client_qbo_id = $client_qbo->qbo_id;
                
            }else{

                if(!empty($client_qbo->address)){
                    $address = preg_split('/\r\n|\r|\n/', $client_qbo->address);
                    $Line1 = isset($address[0]) ? $address[0] : '';
                    if(isset($address[1])){
                        $address2 = explode(',',trim($address[1]));
                        $City = isset($address2[0]) ? $address2[0] : '';
                    }
                    if(isset($address2[1])){
                        $address3 = explode(' ',trim($address2[1]));
                    }
                    $CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
                    $PostalCode = isset($address3[1]) ? $address3[1] : '';

                    $BillAddr = [
                        "Line1"=> $Line1,
                        "City"=> $City,
                        "CountrySubDivisionCode"=> $CountrySubDivisionCode,
                        "PostalCode"=> $PostalCode,
                    ];
                }

                if(!empty($client_qbo->shipping_address)){

                    $ship_address = preg_split('/\r\n|\r|\n/', $client_qbo->shipping_address);
                    $Ship_Line1 = isset($ship_address[0]) ? $ship_address[0] : '';
                    if(isset($ship_address[1])){
                        $ship_address2 = explode(',',trim($ship_address[1]));
                        $Ship_City = isset($ship_address2[0]) ? $ship_address2[0] : '';
                    }
                    if(isset($ship_address[1]) && isset($ship_address2[1])){
                        $ship_address3 = explode(' ',trim($ship_address2[1]));
                    }
                    $ship_CountrySubDivisionCode = isset($ship_address3[0]) ? $ship_address3[0] : '';
                    $ship_PostalCode = isset($ship_address3[1]) ? $ship_address3[1] : '';

                    $ShipAddr = [
                        "Line1"=> $Ship_Line1,
                        "City"=> $Ship_City,
                        "CountrySubDivisionCode"=> $ship_CountrySubDivisionCode,
                        "PostalCode"=> $ship_PostalCode,
                    ];
                }


                $Addr = [
                    "Line1"=> '',
                    "City"=> '',
                    "CountrySubDivisionCode"=> '',
                    "PostalCode"=> '',
                ];

                $customerObj = Customer::create([
                    "BillAddr" => isset($BillAddr) ? $BillAddr : '',
                    "ShipAddr" => isset($ShipAddr) ? $ShipAddr : '',
                    "GivenName"=> $client_qbo->name,
                    "FullyQualifiedName"=> $client_qbo->name,
                    "CompanyName"=> isset($client_qbo->company_name) ? $client_qbo->company_name : $client_qbo->name,
                    "DisplayName"=> $client_qbo->name,
                    "PrimaryPhone"=> [
                        "FreeFormNumber"=> $client_qbo->mobile
                    ],
                    "PrimaryEmailAddr"=> [
                        "Address" => $client_qbo->email
                    ]
                ]);

                $resultingCustomerObj = $this->quickbook->Add($customerObj);
                $error =  $this->quickbook->getLastError();
                if($error){
                    //return Reply::error(__($error->getResponseBody()));
                }

                $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
                $client_qbo->qbo_id = $qbo_id;
                $client_qbo->save();
                $client_qbo_id = $qbo_id;
            }
        
            $theResourceObj = QuickbookInvoices::create([
                "Line" => $quickbook_items,
                "CustomerRef"=> [
                  "value"=> $client_qbo_id,
                ],
                "BillEmail" => [
                    "Address" => $client_email
                ],
                "BillEmailCc" => [
                    "Address" => $this->user->email
                ]
//                "BillEmailBcc" => [
//                    "Address" => "v@intuit.com"
//                ]
            ]);

            $resultingObj = $this->quickbook->Add($theResourceObj);
            $error =  $this->quickbook->getLastError();
            if($error){
                //return Reply::error(__($error->getResponseBody()));
            }

            $qbo_id = isset($resultingObj->Id) ? $resultingObj->Id : '';
             } catch (\Exception $e) {
                 //echo $e->getMessage();exit;
             }

        }
        //bitsclan code ends here
       
       
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

        //        $invoice->client_id = $request->project_id == '' && $request->has('client_id') ? $request->client_id : null;


        //$invoice->client_id =  $request->vendor_id ? null : $request->client_id;
        $invoice->client_id =  $request->client_id ? $request->client_id : null;
        
        //$invoice->invoice_number = Invoice::count() + 1;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total = round($request->sub_total, 2);
        $invoice->discount = round($request->discount_value, 2);
        $invoice->discount_type = $request->discount_type ? $request->discount_type : 'percent';
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
        $invoice->combine_line_items = $combine_line_items;
        //$invoice->tags = json_encode($request->tags);
        
        
        $invoice->tags = json_encode(array());
        if($request->tags) {
            $invoice->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        // bitsclan code start here
        $invoice->qbo_id = isset($qbo_id) ? $qbo_id : '';
        // bitsclan code end here
        $invoice->save();
        $markup_total = 0;
        

        // invoice Code
        
        
        if($this->quickbook && $qbo_id != ''){

            foreach ($items_array as $key => $item) :
                if (!is_null($item)) {
                    
                    if($item['markup_fix'] > 0) {
                         $markup_total += ($item['sale_price'] + $item['markup_fix']);
                    } else if ($item['markup'] > 0) {
                        $markup_total += ($item['sale_price']/((100 + $item['markup'])/100));
                    }

                    $fileName = null;
                    $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                    if ($existKey){
                        $file = $productImage[$key]->getClientOriginalName();
                        $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                        $extension = pathinfo($file, PATHINFO_EXTENSION);

                        $fileName = time().mt_rand().".".$extension;
                    }

                    try {
                        $invoiceItem = InvoiceItems::create([
                                    'invoice_id' => $invoice->id,
                                    'item_name' => $item['item_name'],
                                    'item_summary' => $item['item_summary'] ? $item['item_summary'] : '',
                                    'type' => 'item',
                                    'quantity' => $item['quantity'],
                                    'unit_price' => $item['unit_price'],
                                    'amount' => $item['amount'],
                                    'taxes' => $item['taxes'],
                                    'group_id' => $item['group_id'],
                                    'qbo_id' => $item['qbo_id'],
                                    'picture' => $item['picture'],
                                    'product_id' => $item['product_id'],
                                    'markup' => $item['markup'],
                                    'markup_fix' => $item['markup_fix'],
                                    'sale_price' => $item['sale_price'],
                                    'shipping_price' => $item['shipping_price'],
                                    'invoice_item_type' => $item['invoice_item_type'],
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

                    } catch (\Exception $e) {
                        
                    }
                }
            endforeach;
        } else {
            
            // Added by SB
            
            
            foreach ($items as $key => $item) :
                if (isset($sale_prices[$key]) && isset($markup_fix[$key]) && $markup_fix[$key] > 0) {
                    $markup_total += ($sale_prices[$key] + $markup_fix[$key]);
                } else if (isset($sale_prices[$key]) && isset($markups[$key]) && $markups[$key] > 0) {
                    $markup_total += ($sale_prices[$key] / ((100 + $markups[$key]) / 100));
                }

                $fileName = null;
                $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                if ($existKey){
                    $file = $productImage[$key]->getClientOriginalName();
                    $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);

                    $fileName = time().mt_rand().".".$extension;
                }

                try {
                    $invoiceItem = InvoiceItems::create([
                                'invoice_id' => $invoice->id,
                                'item_name' => $item,
                                'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                                'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                                'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                                'type' => 'item',
                                'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                                'unit_price' => isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                                'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                                'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                                'group_id' => isset($group[$key]) ? $group[$key] : null,
                                'markup' => isset($markups[$key]) ? $markups[$key] : '0.00',
                                'markup_fix' => isset($markup_fix[$key]) ? $markup_fix[$key] : '0.00',
                                'sale_price' => isset($sale_prices[$key]) ? $sale_prices[$key] : '0.00',
                                'shipping_price' => isset($shipping_prices[$key]) ? $shipping_prices[$key] : '0.00',
                                'invoice_item_type' => isset($invoice_item_type[$key]) ? $invoice_item_type[$key] : 'services',
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
                    
                } catch (\Exception $e) {
                    
                }

            endforeach;
            
            // end SB
            
        }
        
        // UPDATE markup
//        if($markup_total > 0 && $request->sub_total > 0) {
//            $markup_total = $request->sub_total-$markup_total;
//        }
        $inv = Invoice::findOrFail($invoice->id);
        $inv->markup_total = $markup_total;
        $inv->save();
        
        
            
        //Invoice Code ends here

        
        if($invoice->status == 'paid') {
            $this->createInvoiceExpense($invoice);
           // $this->createInvoicePurchaseOrder($invoice);
        }
        
        // create purchase order
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
            if ($invoice->project_id != null && $invoice->project_id != '') {
                $client = $invoice->project->clientdetails;
            } elseif ($invoice->client_id != null && $invoice->client_id != '') {
                $client = $invoice->clientdetails;
            }
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
        $this->logSearchEntry($invoice->id, 'Invoice ' . $invoice->invoice_number, 'admin.client-invoice.show', 'invoice');
        
        $project_id = $invoice->project_id;
        
         if($project_id) {
            return Reply::redirect(route('admin.invoices-project.show', $project_id), __('messages.invoiceCreated'));
        }

        return Reply::redirect(route('admin.client-invoice.index'), __('messages.invoiceCreated'));
    }

    public function remindForPayment($taskID)
    {
        $invoice = Invoice::with(['project', 'project.client'])->findOrFail($taskID);
        // Send  reminder notification to user

        $userClient = User::findOrFail($invoice->project ? $invoice->project->client->user_id : $invoice->client_id);
        $notifyUser = $userClient;
        $notifyUser->notify(new PaymentReminder($invoice));

        return Reply::success('messages.reminderMailSuccess');
    }

    public function edit($id)
    {
        $this->pageTitle = ' Edit Invoice';
        $this->invoice = Invoice::findOrFail($id);
        $this->projects = Project::orderBy('project_name')->get();
        $this->currencies = Currency::all();
        $this->invoiceSetting = InvoiceSetting::first();
        
        $tags = $this->invoice->tags ? json_decode($this->invoice->tags) : array();
        $this->invoice->tags = $tags;
        
        if($tags) {
            $this->invoice->tags = array_values(array_unique($tags));
        }
        

        if ($this->invoice->status == 'paid') {
            //abort(403);
        }
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        $this->clients = User::allClients();
        if ($this->invoice->project_id != '') {
            $companyName = Project::where('id', $this->invoice->project_id)->with('clientdetails')->first();
            $this->companyName = $companyName->clientdetails ? $companyName->clientdetails->company_name : '';
            if($this->companyName == '') {
                $this->companyName = $companyName->clientdetails ? $companyName->clientdetails->name : '';
            }
            $this->clientId = $companyName->clientdetails ? $companyName->clientdetails->user_id : '';
        }
        
        $this->salescategories = $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();

        return view('admin.invoices.edit', $this->data);
    }
    
    public function refund(Request $request, $id)
    {
        $this->invoice = Invoice::findOrFail($id);   
        $this->project_id = $request->project_id ? $request->project_id : '';
        
        
        return view('admin.invoices.refund', $this->data);
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
            return Reply::redirect(route('admin.invoices-project.data', $project_id), 'Invoice Refunded.');
        } else {
            return Reply::redirect(route('admin.client-invoice.index'), 'Invoice Refunded.');
        }
        
    }

    public function update(UpdateInvoice $request, $id)
    {
        //getting previous invoice ids
        //InvoiceItems::where('invoice_id', $invoice->id)->get();

        $invoice = Invoice::findOrFail($id);
        
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
        $product_ids = $request->input('product_id');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');
        
        $markups = $request->input('markup');
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        
 // invoice quickbook edit code
        $items_array = array();
        
        // invoice quickbook code edit ends here

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

        $invoice->project_id            = $request->project_id ?? null;
        //$invoice->client_id             = $request->project_id == '' && $request->has('client_id') ? $request->client_id : null;
        $invoice->client_id             = $request->has('client_id') ? $request->client_id : null;
        $invoice->issue_date            = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date              = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total             = round($request->sub_total, 2);
        $invoice->discount              = round($request->discount_value, 2);
        $invoice->discount_type         = $request->discount_type;
        $invoice->total                 = round($request->total, 2);
        $invoice->currency_id           = $request->currency_id;
        $invoice->status                = $request->status;
        $invoice->recurring             = $request->recurring_payment;
        $invoice->billing_frequency     = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        $invoice->billing_interval      = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        $invoice->billing_cycle         = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        $invoice->note                  = $request->note;
        $invoice->show_shipping_address = $request->show_shipping_address;
        
        //$invoice->tags = json_encode($request->tags);
        
        
        $invoice->tags = json_encode(array());
        if($request->tags) {
            $invoice->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        $invoice->save();

        // delete and create new
        InvoiceItems::where('invoice_id', $invoice->id)->delete();

        foreach ($items as $key => $item) :
            InvoiceItems::create(
                [
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
                    'markup' => $markups[$key]?$markups[$key]:'0.00',
                    'sale_price' => $sale_prices[$key]?$sale_prices[$key]:'0.00',
                    'shipping_price' => $shipping_prices[$key]?$shipping_prices[$key]:'0.00'
                ]
            );
        endforeach;

        if ($request->has('shipping_address')) {
            if ($invoice->project_id != null && $invoice->project_id != '') {
                $client = $invoice->project->clientdetails;
            } elseif ($invoice->client_id != null && $invoice->client_id != '') {
                $client = $invoice->clientdetails;
            }
            $client->shipping_address = $request->shipping_address;

            $client->save();
        }

        return Reply::redirect(route('admin.client-invoice.index'), __('messages.invoiceUpdated'));
    }

    public function show($id)
    {
        $this->pageTitle = 'Invoice';
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
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * ($item->amount);
                        }
                    } else {
                        if ($this->invoiceSetting->shipping_taxed == 'no') {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price));
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount));
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
        

        $this->settings = $this->company;
        $this->invoiceSetting = InvoiceSetting::first();
        
        if($this->invoice->combine_line_items == 1) {
            
            $allItems = InvoiceItems::where('invoice_id', $this->invoice->id)->get();
            $this->groupItems = getGroupItems($allItems);
            
            return view('admin.invoices.show_combine', $this->data);
            
        } else {
            
            return view('admin.invoices.show', $this->data);
        }
        
        
        
        
        
    }
    
    public function view($id)
    {
        $this->pageTitle = 'Invoice';
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
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * ($item->amount);
                        }
                    } else {
                        if ($this->invoiceSetting->shipping_taxed == 'no') {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount - $item->shipping_price));
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount));
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
        

        $this->settings = $this->company;
        $this->invoiceSetting = InvoiceSetting::first();
        return view('admin.invoices.view', $this->data);
    }

    public function appliedCredits(Request $request, $id)
    {
        $this->invoice = Invoice::findOrFail($id);

        $this->creditNotes = $this->invoice->credit_notes()->orderBy('date', 'DESC')->get();

        return view('admin.invoices.applied_credits', $this->data);
    }

    public function deleteAppliedCredit(Request $request, $id)
    {
        $this->invoice = Invoice::findOrFail($request->invoice_id);

        // delete from credit_notes_invoice_table
        $invoiceCreditNote = $this->invoice->credit_notes()->wherePivot('id', $id);
        $creditNote = $invoiceCreditNote->first();
        $invoiceCreditNote->detach();

        // change invoice status
        $this->invoice->status = 'partial';
        if ($this->invoice->amountPaid() == $this->invoice->total) {
            $this->invoice->status = 'paid';
        }
        if ($this->invoice->amountPaid() == 0) {
            $this->invoice->status = 'unpaid';
        }
        $this->invoice->save();

        // change credit note status
        if ($creditNote->status == 'closed') {
            $creditNote->status = 'open';
            $creditNote->save();
        }

        $this->creditNotes = $this->invoice->credit_notes()->orderBy('date', 'DESC')->get();
        if ($this->creditNotes->count() > 0) {
            $view = view('admin.invoices.applied_credits', $this->data)->render();

            return Reply::successWithData(__('messages.creditedInvoiceDeletedSuccessfully'), ['view' => $view]);
        }
        return Reply::redirect(route('admin.all-invoices.show', [$this->invoice->id]), __('messages.creditedInvoiceDeletedSuccessfully'));
    }
    
    
   
        
        
        

    public function convertEstimate($id)
    {
        $this->pageTitle = 'Convert Estimate to Invoice';
        $this->estimateId = $id;
        $this->invoice = Estimate::with('items')->findOrFail($id);
        $this->vendors = Vendor::orderBy('name')->get();
        $this->lastInvoice = Invoice::count() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->projects = Project::orderBy('project_name')->get();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        $this->clients = User::allClients();
        $this->salescategories = SalescategoryType::all();
        $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        $tags = $this->invoice->tags ? json_decode($this->invoice->tags) : array();
        $this->invoice->tags = $tags;
        if($tags) {
            $this->invoice->tags = array_values(array_unique($tags));
        }
        
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
        
        
        //        foreach ($this->invoice->items as $items)

        $discount = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'discount';
        });

        $tax = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'tax';
        });

        $this->totalTax = $tax->sum('amount');
        $this->totalDiscount = $discount->sum('amount');
        
        return view('admin.invoices.convert_estimate', $this->data);
    }

    public function convertProposal($id)
    {
        $this->invoice = Proposal::findOrFail($id);
        $this->lastInvoice = Invoice::withoutGlobalScope(CompanyScope::class)->orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        return view('admin.invoices.convert_estimate', $this->data);
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
//        if(isset($this->items->itemObj->totalEstimatedCost) && !empty($this->items->itemObj->totalEstimatedCost)) {
//            $this->items->price = floor($this->items->itemObj->totalEstimatedCost * $exchangeRate->exchange_rate);
//        } else {
//            $this->items->price = 0;
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
        $view = view('admin.invoices.add-item', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }


    public function paymentDetail($invoiceID)
    {
        $this->invoice = Invoice::with(['payment', 'payment.offlineMethod', 'currency', 'approved_offline_invoice_payment'])->findOrFail($invoiceID);

        return View::make('admin.invoices.payment-detail', $this->data);
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

    /**
     * @param $startDate
     * @param $endDate
     * @param $status
     * @param $projectID
     */
    public function export($startDate, $endDate, $status, $projectID)
    {

        $invoices = Invoice::with(['project:id,project_name', 'currency:id,currency_symbol']);

        if ($startDate !== null && $startDate != 'null' && $startDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '>=', $startDate);
        }

        if ($endDate !== null && $endDate != 'null' && $endDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '<=', $endDate);
        }

        if ($status != 'all' && !is_null($status)) {
            $invoices = $invoices->where('invoices.status', '=', $status);
        }

        if ($projectID != 'all' && !is_null($projectID)) {
            $invoices = $invoices->where('invoices.project_id', '=', $projectID);
        }

        $invoices = $invoices->orderBy('id', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'project_name' => $invoice->project->project_name,
                    'status' => $invoice->status,
                    'total' => currency_position($invoice->total, $invoice->currency->currency_symbol),
                    'amount_used' => currency_position($invoice->amountPaid(), $invoice->currency->currency_symbol),
                    'amount_remaining' => currency_position($invoice->amountDue(), $invoice->currency->currency_symbol),
                    'issue_date' => $invoice->issue_date ? $invoice->issue_date->format($this->global->date_format) : ''
                ];
            })->toArray();

        // Define the Excel spreadsheet headers
        $headerRow = ['ID', 'Invoice #', 'Project Name', 'Status', 'Total Amount', 'Amount Paid', 'Amount Due', 'Invoice Date'];

        array_unshift($invoices, $headerRow);

        // Generate and return the spreadsheet
        Excel::create('invoice', function ($excel) use ($invoices) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Invoice');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('invoice file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($invoices) {
                $sheet->fromArray($invoices, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));
                });
            });
        })->download('xlsx');
    }

    public function getClient($projectID)
    {
        $companyName = Project::with('client')->find($projectID);
        return $companyName->client->company_name;
    }

    public function getClientOrCompanyName($projectID = '')
    {
        $this->projectID = $projectID;

        if ($projectID == '') {
            $this->clients = User::allClients();
        } else {
            $companyName = Project::where('id', $projectID)->with('clientdetails')->first();
            $this->companyName = $companyName->clientdetails ? $companyName->clientdetails->company_name : '';
            if($this->companyName == '') {
                $this->companyName = $companyName->clientdetails ? $companyName->clientdetails->name : '';
            }
            $this->clientId = $companyName->clientdetails ? $companyName->clientdetails->user_id : '';
        }

        $list = view('admin.invoices.client_or_company_name', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
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
        return view('admin.invoices.convert_milestone', $this->data);
    }

    public function verifyOfflinePayment($id)
    {
        $this->invoice = Invoice::with('offline_invoice_payment', 'offline_invoice_payment.payment_method')->findOrFail($id);
        return view('admin.invoices.verify-payment-detail', $this->data);
    }

    public function verifyPayment(Request $request, $id)
    {
        $offlineRequest = OfflineInvoicePayment::findOrFail($id);
        $invoice = Invoice::findOrFail($offlineRequest->invoice_id);

        // Change the status of payment request
        $offlineRequest->status = 'approve';
        $offlineRequest->save();

        // change the status of payment to paid
        
       //if($offlineRequest->is_offline_deposit == 0) {
            $payment = ClientPayment::where('invoice_id', $invoice->id)->where('status', 'pending')->first();
            $payment->status = 'complete';
            $payment->save();
       //}
        
       
        //Change the status of invoice
        $invoice->status = 'paid';
        $invoice->save();
        
       if($invoice->amountPaid() < $invoice->total){
            $invoice->status = 'review';
            $invoice->save();
       }

        restartPM2();
       // Payment Received Automation Mail
        if ($invoice->status == 'paid') {
           if (($invoice->project_id && $invoice->project->client_id != null) || ($invoice->client_id && $invoice->client_id != null)) {
               $clientId = ($invoice->project_id && $invoice->project->client_id != null) ? $invoice->project->client_id : $invoice->client_id;
               $user = User::withoutGlobalScopes(['active', 'company'])->find($clientId);
               if($user){
                   paymentReceivedAutomationMail($user);
               }
           }
       }

        return Reply::success('Successfully verified');
    }

    public function rejectPayment(Request $request, $id)
    {
        $offlineRequest = OfflineInvoicePayment::findOrFail($id);
        $invoice = Invoice::findOrFail($offlineRequest->invoice_id);

        $offlineRequest->status = 'reject';
        $offlineRequest->save();

        //Change the status of invoice
        $invoice->status = 'unpaid';
        
        if($offlineRequest->is_offline_deposit == 1) {
            $invoice->is_deposit = 0;
        }
        $invoice->save();

        return Reply::success('Successfully rejected');
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
    
    public function fetchTimelogs(Request $request)
    {
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        
        $this->invoiceSetting = InvoiceSetting::first();
        $projectId = $request->projectId;
        $timelogFrom = Carbon::createFromFormat($this->global->date_format, $request->timelogFrom)->format('Y-m-d 00:00:00');
        $timelogTo = Carbon::createFromFormat($this->global->date_format, $request->timelogTo)->format('Y-m-d 23:59:59');
        $this->timelogs = ProjectTimeLog::with('task')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id')
            ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
            //->groupBy('project_time_logs.task_id')
            ->where('project_time_logs.project_id', $projectId)
            //->where('project_time_logs.earnings', '>', 0)
            ->where(
                function ($query) {
                    $query->where('tasks.billable', 1)
                        ->orWhereNull('tasks.billable');
                }
            )
            ->whereDate('project_time_logs.start_time', '>=', $timelogFrom)
            ->whereDate('project_time_logs.end_time', '<=', $timelogTo)
            //->selectRaw('project_time_logs.id, project_time_logs.task_id, project_time_logs.memo , sum(project_time_logs.total_minutes) as total_minutes, employee_details.hourly_rate,  sum(project_time_logs.earnings) as sum')
                    ->selectRaw('project_time_logs.id, project_time_logs.task_id, project_time_logs.memo, project_time_logs.user_id, project_time_logs.project_id, project_time_logs.total_minutes as total_minutes, employee_details.hourly_rate,  project_time_logs.earnings as sum')
            ->get();
        $html = view('admin.invoices.timelog-item', $this->data)->render();
        return Reply::dataOnly(['html' => $html]);
    }

    public function fetchTimelogs_bk(Request $request)
    {
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        
        $this->invoiceSetting = InvoiceSetting::first();
        $projectId = $request->projectId;
        $timelogFrom = Carbon::createFromFormat($this->global->date_format, $request->timelogFrom)->format('Y-m-d');
        $timelogTo = Carbon::createFromFormat($this->global->date_format, $request->timelogTo)->format('Y-m-d');
        $this->timelogs = ProjectTimeLog::with('task')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id')
            ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
            //->groupBy('project_time_logs.task_id')
            ->groupBy('StartDate')
            ->where('project_time_logs.project_id', $projectId)
            ->where('project_time_logs.earnings', '>', 0)
            ->where(
                function ($query) {
                    $query->where('tasks.billable', 1)
                        ->orWhereNull('tasks.billable');
                }
            )
            ->whereDate('project_time_logs.start_time', '>=', $timelogFrom)
            ->whereDate('project_time_logs.end_time', '<=', $timelogTo)
            ->selectRaw('DATE(project_time_logs.start_time) StartDate, project_time_logs.id, project_time_logs.task_id, project_time_logs.memo , sum(project_time_logs.total_minutes) as total_minutes, employee_details.hourly_rate,  sum(project_time_logs.earnings) as sum')
            ->get();
        $html = view('admin.invoices.timelog-item', $this->data)->render();
        return Reply::dataOnly(['html' => $html]);
    }
    
    
    
    // send email with PDF
    public function sendInvoice($invoiceID){
        
        $invoice = $this->invoice = Invoice::with(['project', 'project.client'])->findOrFail($invoiceID);
        $this->company = company();
        $pdfOption = $this->domPdfObjectForDownload($invoiceID);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];
        
        $objDemo = new \stdClass();
        $objDemo->Message = __('email.invoice.text_new');
        $objDemo->Subject = __('email.invoice.subject').' ['.$this->invoice->invoice_number .']';
        $objDemo->thankyouNote = __('email.thankyouNote');
        $objDemo->FromEmail = $this->user->email;
        $objDemo->FromName = $this->user->name;
        $objDemo->pdf = $pdf;
        $objDemo->filename = $filename;
        $objDemo->invoiceID = $invoiceID;
        
        
        if (isset($invoice->client_id) && isset($invoice->client)) {
            Mail::to($invoice->client->email)->send(new ClientInvoiceEmail($objDemo));
        } else if ($invoice->project_id != null && $invoice->project_id != '') {
            if (isset($invoice->project->client)) {
                Mail::to($invoice->project->client->email)->send(new ClientInvoiceEmail($objDemo));
            }
        }
        $invoice->send_status = 1;
        if ($invoice->status == 'draft') {
            $invoice->status = 'unpaid';
        }
        $invoice->save();
        return Reply::success(__('messages.updateSuccess'));
        
        //return Reply::success('PDF sent successfully');
        
    }

    // old method for send email
    public function sendInvoice_bk($invoiceID)
    {
        
        $invoice = Invoice::with(['project', 'project.client'])->findOrFail($invoiceID);
        
        if ($invoice->project_id != null && $invoice->project_id != '') {
            if (isset($invoice->project->client)) {
                $notifyUser = User::withoutGlobalScope(CompanyScope::class)->find($invoice->project->client_id);
            }
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
