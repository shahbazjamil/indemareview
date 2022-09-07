<?php

namespace App\Http\Controllers\Admin;

use App\CreditNotes;
use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Invoices\StoreInvoice;
use App\Http\Requests\Invoices\UpdateInvoice;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\Notifications\NewInvoice;
use App\Project;
use App\PurchaseOrder;
use App\PurchaseOrderItems;
use App\ClientVendorDetails;
use App\Scopes\CompanyScope;

use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Product;
use App\EstimateItem;
use App\SalescategoryType;
use App\CodeType;
use App\Expense;
use App\LineItemGroup;


// bitsclan code start here
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Invoice as QuickbookInvoices;
use App\QuickbooksSettings;
use App\ClientDetails;
use QuickBooksOnline\API\Facades\Customer;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

// bitsclan code end here

class ManageInvoicesController extends AdminBaseController
{
    // bitsclan code start here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    // bitsclan code end here

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Project';
        $this->pageIcon = 'layers';
        $this->middleware(function ($request, $next) {
            if (!in_array('invoices',$this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->project = Project::findOrFail();
        $this->currencies = Currency::all();
        $this->lastInvoice = Invoice::count()+1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->taxes = Tax::all();
        
        $latestInvoice = DB::table('invoices')->where('company_id', company()->id)->orderByRaw('CONVERT(invoice_number, SIGNED) desc')->first();
        if($latestInvoice) {
            $invoice_number = trim($latestInvoice->invoice_number);
           if(is_numeric($invoice_number)) {
               $this->lastInvoice = $invoice_number + 1;
           }
        }
        
        
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit){
            for ($i=0; $i<$this->invoiceSetting->invoice_digit-strlen($this->lastInvoice); $i++){
                $this->zero = '0'.$this->zero;
            }
        }

        return view('admin.projects.invoices.create', $this->data);
    }

    public function createInvoice(Request $request)
    {
        $this->project = Project::findOrFail($request->id);
        $this->currencies = Currency::all();
        $this->lastInvoice = Invoice::count()+1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        
        $latestInvoice = DB::table('invoices')->where('company_id', company()->id)->orderByRaw('CONVERT(invoice_number, SIGNED) desc')->first();
        if($latestInvoice) {
            $invoice_number = trim($latestInvoice->invoice_number);
           if(is_numeric($invoice_number)) {
               $this->lastInvoice = $invoice_number + 1;
           }
        }
        
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit){
            for ($i=0; $i<$this->invoiceSetting->invoice_digit-strlen($this->lastInvoice); $i++){
                $this->zero = '0'.$this->zero;
            }
        }
        
        $this->projects = Project::all();
        $this->salescategories = SalescategoryType::all();
        $this->codetypes =  CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        //return view('admin.projects.invoices.create', $this->data);
        
        // for not modal
        $html = view('admin.projects.invoices.create', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $html]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(StoreInvoice $request)
    {

        $items = $request->input('item_name') ? $request->input('item_name') : [];   
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
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
        
        $vendor_id = $request->input('vendor_id');
        $invoice_type = $request->input('invoice_type');
        if(empty($invoice_type)){
            $invoice_type = 'client';
//            if($request->vendor_id) {
//                $invoice_type = 'vendor';
//            }
        }
        
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

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));
                    
                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item , 0, 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key])?$group[$key]:null,
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
                        if($shipping_prices[$key]) {
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

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $invoices_item_qbo, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));
                    

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item , 0 , 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key]: '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key])?$group[$key]:null,
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
                        if($shipping_prices[$key]) {
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

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item, 0 , 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key])?$group[$key]:null,
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
                        if($shipping_prices[$key]) {
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

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $product_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));

                    array_push($quickbook_items, $item_to_be_pushed);


                    $item_arr = array(
                        'item_name' => substr($item, 0, 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset(group[$key])?$group[$key]:null,
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
            } catch (\Exception $e) {}

        }
        //bitsclan code ends here
        
        $amount = $request->input('amount') ? $request->input('amount') : [];
        $tax = $request->input('taxes');
        $group = $request->input('groups');
        
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
        
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        
        
        $project = Project::findOrFail($request->project_id);
        
        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }

        $invoice = new Invoice();
        $invoice->project_id = $request->project_id ?? null;
        $invoice->client_id =  $request->client_id ? $request->client_id : null;
        
        //$invoice->invoice_number = Invoice::count() + 1;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total = round($request->sub_total, 2);
        $invoice->discount = round($request->discount_value, 2);
        $invoice->discount_type = $request->discount_type;
        $invoice->card_processing_value = $request->card_processing_value ? round($request->card_processing_value, 2) : 0;
        $invoice->card_processing_type = $request->card_processing_type ? $request->card_processing_type : 'percent';
        $invoice->total = round($request->total, 2);
        $invoice->total_tax = round($request->total_tax, 2);
        $invoice->currency_id = $request->currency_id;
        $invoice->recurring = $request->recurring_payment ? $request->recurring_payment : 'no';
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
        
        
        if ($this->quickbook && $qbo_id != '') {

            foreach ($items_array as $key => $item) :
                if (!is_null($item)) {

                    if ($item['markup_fix'] > 0) {
                        $markup_total += ($item['sale_price'] + $item['markup_fix']);
                    } else if ($item['markup'] > 0) {
                        $markup_total += ($item['sale_price'] / ((100 + $item['markup']) / 100));
                    }

                    try {
                        InvoiceItems::create(
                                [
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
                                    'invoice_item_type' => $item['invoice_item_type']
                                ]
                        );
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

                try {
                    InvoiceItems::create([
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
                                'group_id' => isset($group[$key])?$group[$key]:null,
                                'markup' => isset($markups[$key]) ? $markups[$key] : '0.00',
                                'markup_fix' => isset($markup_fix[$key]) ? $markup_fix[$key] : '0.00',
                                'sale_price' => isset($sale_prices[$key]) ? $sale_prices[$key] : '0.00',
                                'shipping_price' => isset($shipping_prices[$key]) ? $shipping_prices[$key] : '0.00',
                                'invoice_item_type' => isset($invoice_item_type[$key]) ? $invoice_item_type[$key] : 'services'
                            ]);
                } catch (\Exception $e) {
                    
                }

            endforeach;

            // end SB
        }

        // UPDATE markup
        if($markup_total > 0 && $request->sub_total > 0) {
            $markup_total = $request->sub_total-$markup_total;
        }
        $inv = Invoice::findOrFail($invoice->id);
        $inv->markup_total = $markup_total;
        $inv->save();
        
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
        
        $this->logSearchEntry($invoice->id, 'Invoice #'.$invoice->invoice_number, 'admin.client-invoice.show', 'invoice');

        $this->project = $project;  //Project::findOrFail($request->project_id);
        $view = view('admin.projects.invoices.invoice-ajax', $this->data)->render();
        return Reply::successWithData(__('messages.invoiceCreated'), ['html' => $view]);
        
        //return Reply::redirect(route('admin.all-invoices.index'), __('messages.invoiceCreated'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->project = Project::findOrFail($id);
        return view('admin.projects.invoices.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $tax = $request->input('taxes');
        $group = request()->input('groups');
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
        
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        $invoice_item_type = $request->input('invoice_item_type');
        $old_items = $request->input('old_items') ?? [];

        $items_array = array();

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
        
        if($request->deposit_req > $request->total) {
            return Reply::error('The deposit request amount should be less than the total amount.');
        }
        
        

        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status == 'paid') {
            $old_total = $invoice->total;
            $new_total = round($request->total, 2);
            if($old_total != $new_total) {
                $request->status = 'partial';
            }
        }
//        if ($invoice->status == 'paid') {
//            return Reply::error(__('messages.invalidRequest'));
//        }


        // bitsclan code start here


        // echo '<pre>';
        // print_r($invoice);
        // exit();
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
            $qbo_arr = $request->all();
            $quickbook_items = array();
            $adminSetting = User::where('email', ($this->user->email))->first();
            foreach ($items as $key => $item) {
                
                $taxable = false;
                if($tax && array_key_exists($key, $tax)) {
                    $taxable = true;
                }

                $item_detail = Product::where('name', $item)->first();
                $invoice_item_detail = InvoiceItems::where(['item_name' => $item, 'invoice_id' => $invoice->id])->first();
                $estimate_detail = EstimateItem::where('item_name', $item)->first();

                if(!empty($item_detail)){

                    if(!empty($item_detail->qbo_id)){
                        $item_qbo_id = $item_detail->qbo_id;
                    }else{
                        $dateTime = new \DateTime('NOW');
                        
                        $unitPrice = $sale_prices[$key] / $quantity[$key];
                        $shipping_line = 0;
                        if($shipping_prices[$key]) {
                            $shipping_line = $shipping_prices[$key];
                            //$unitPrice += $shipping_prices[$key];
                        }

                        $Items = Item::create([
                            "Name" => substr($item , 1, 100),
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
                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id: '';
                        $item_detail->qbo_id =  $item_qbo_id;
                        $item_detail->save();

                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));
                

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item, 0, 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '',
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

                else if(!empty($invoice_item_detail)){

                    if(!empty($invoice_item_detail->qbo_id)){
                        $invoices_item_qbo = $invoice_item_detail->qbo_id;
                    }else{
                        
                       $unitPrice = $sale_prices[$key] / $quantity[$key];
                        $shipping_line = 0;
                        if($shipping_prices[$key]) {
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

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $invoices_item_qbo, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));


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
                        if($shipping_prices[$key]) {
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
                        $estimate_detail->qbo_id = $product_qbo_id;
                        $estimate_detail->save();

                        $item_qbo_id = $resultingItemObj->Id;
                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => substr($item, 0, 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '',
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
                        if($shipping_prices[$key]) {
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
                    

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $product_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> ($quantity[$key]*$unitPrice));

                    $item_arr = array(
                        'item_name' => substr($item, 0, 100),
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '',
                        'unit_price' =>   isset($cost_per_item[$key])? round($cost_per_item[$key], 2) : '0.00',
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
                    array_push($quickbook_items, $item_to_be_pushed);

                }
            
            }
         
            $client_qbo = ClientDetails::where('user_id', $invoice->client_id)->first();
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

                if(!empty($client_qbo->shipping_address))
                {

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

            if(!empty($invoice->qbo_id)){
                $entities = $this->quickbook->Query("SELECT * FROM Invoice where Id='".$invoice->qbo_id."'");
                $theInvoice = reset($entities);
                $theResourceObj = QuickbookInvoices::update($theInvoice,[
                    "Line" => $quickbook_items,
                    "CustomerRef"=> [
                        "value"=> $client_qbo_id,
                    ]
                ]);

                $resultingObj = $this->quickbook->Update($theResourceObj);
            }else{
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
//                    "BillEmailBcc" => [
//                        "Address" => "v@intuit.com"
//                    ]
                ]);

                $resultingObj = $this->quickbook->Add($theResourceObj);       
            }
        
            $error =  $this->quickbook->getLastError();
            if($error){
                //return Reply::error(__($error->getResponseBody()));
            }
            $qbo_id = isset($resultingObj->Id) ? $resultingObj->Id : '';
             } catch (\Exception $e) {}

        }
        // bitsclan code ends here

        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }
        
        $invoice->project_id = $request->project_id ? $request->project_id : null;
        //$invoice->client_id  = $request->project_id == '' && $request->has('client_id') ? $request->client_id : null;
        
        $invoice->client_id  = $request->client_id ? $request->client_id : null;
        $invoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total = $request->sub_total;
        $invoice->total = $request->total;
        $invoice->discount = round($request->discount_value, 2);
        $invoice->discount_type = $request->discount_type ? $request->discount_type : 'percent';
        $invoice->card_processing_value = round($request->card_processing_value, 2);
        $invoice->card_processing_type = $request->card_processing_type ? $request->card_processing_type : 'percent';
        $invoice->total = round($request->total, 2);
        $invoice->total_tax = round($request->total_tax, 2);
        $invoice->currency_id = $request->currency_id;
        $invoice->status                = $request->status;
        $invoice->show_shipping_address = $request->show_shipping_address;
        $invoice->note = $request->note;
        $invoice->tax_on_total = $request->tax_on_total ? json_encode($request->tax_on_total) : null;
        $invoice->shipping_total = round($request->shipping_total, 2);
        
        $invoice->deposit_request = round($request->deposit_request, 2);
        $invoice->deposit_request_type = $request->deposit_request_type ? $request->deposit_request_type : 'percent';
        $invoice->deposit_req = round($request->deposit_req, 2);
        $invoice->combine_line_items = $combine_line_items;
        
        $invoice->tags = json_encode(array());
        if($request->tags) {
            $invoice->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        
        $invoice->qbo_id =  isset($qbo_id) ? $qbo_id : '';
        
        $invoice->save();
        $markup_total = 0;
        $invoiceItemQuery = InvoiceItems::query()->where('invoice_id', $invoice->id)->where('product_image','!=',null);
        $oldInvoiceItemImg = $invoiceItemQuery->pluck('product_image','id')->toArray();
        try{
            DB::beginTransaction();
            // delete and create new
            InvoiceItems::where('invoice_id', $invoice->id)->delete();

            // foreach ($items as $key => $item) :
            //     InvoiceItems::create(
            //         [
            //             'invoice_id' => $invoice->id,
            //             'item_name' => $item,
            //             'item_summary' => $itemsSummary[$key],
            //             'type' => 'item',
            //             'quantity' => $quantity[$key],
            //             'unit_price' => round($cost_per_item[$key], 2),
            //             'amount' => round($amount[$key], 2),
            //             'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
            //         ]
            //     );
            // endforeach;
            if($this->quickbook && $qbo_id != ''){
                if (!empty($items_array)) {
                    foreach ($items_array as $key => $item) :
                        if (!is_null($item)) {
                            if ($item['markup_fix'] > 0) {
                                $markup_total += ($item['sale_price'] + $item['markup_fix']);
                            } else if ($item['markup'] > 0) {
                                $markup_total += ($item['sale_price'] / ((100 + $item['markup']) / 100));
                            }

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


                            } catch (\Exception $e) {

                            }
                        }
                    endforeach;
                }
            } else {
                // Added by SB

                foreach ($items as $key => $item) :
                    if(isset($sale_prices[$key]) && isset($markup_fix[$key]) && $markup_fix[$key] > 0) {
                        $markup_total += ($sale_prices[$key] + $markup_fix[$key]);
                    } else if(isset($sale_prices[$key]) && isset($markups[$key]) && $markups[$key] > 0) {
                        $markup_total += ($sale_prices[$key]/((100 + $markups[$key])/100));
                    }

                    $fileName = null;
                    $newImg = is_array($productImage) && in_array($key, array_keys($productImage));
                    if ($newImg){
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
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'picture' => isset($pictures[$key]) ? $pictures[$key]: '',
                        'product_id' => isset($product_ids[$key]) ? $product_ids[$key]: '',
                        'type' => 'item',
                        'quantity' => isset($quantity[$key]) ? $quantity[$key]: 0,
                        'unit_price' => isset($cost_per_item[$key])? round($cost_per_item[$key], 2):'0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key])?$group[$key]:null,
                        'markup' => isset($markups[$key])?$markups[$key]:'0.00',
                        'markup_fix' => isset($markup_fix[$key])?$markup_fix[$key]:'0.00',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00',
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'services',
                        'product_image' => $fileName,
                    ]);

                    if(file_exists($oldPath)){
                        $newPath = public_path('user-uploads/invoice-items/products/'.$invoiceItem->id);
                        File::moveDirectory($oldPath,$newPath);
                    }

                    if ($newImg) {
                        $directory = "user-uploads/invoice-items/products/$invoiceItem->id";
                        if (!File::exists(public_path($directory))) {
                            $result = File::makeDirectory(public_path($directory), 0775, true);
                        }
                        $imageFilePath = "$directory/$fileName";

                        File::move($productImage[$key], public_path($imageFilePath));
                        $invoiceItem->save();
                    }

                endforeach;
                // end SB
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage().'On Line NO: '.$e->getLine());
        }
        
        
         // UPDATE markup
        if($markup_total > 0 && $request->sub_total > 0) {
            $markup_total = $request->sub_total-$markup_total;
        }
        $inv = Invoice::findOrFail($invoice->id);
        $inv->markup_total = $markup_total;
        $inv->save();
        
        // create purchase order for paid invoices
        
        $user = Auth::user();
        
        if($invoice->status == 'paid') {
            $this->createInvoiceExpense($invoice);
            //$this->createInvoicePurchaseOrder($invoice);
        }
        

        $project_id = $invoice->project_id;

        if ($request->has('shipping_address')) {
            if ($invoice->project_id != null && $invoice->project_id != '') {
                $client = $invoice->project->clientdetails;
            } elseif ($invoice->client_id != null && $invoice->client_id != '') {
                $client = $invoice->clientdetails;
            }
            $client->shipping_address = $request->shipping_address;

            $client->save();
        }
        
        if($project_id) {
            return Reply::redirect(route('admin.invoices-project.show', $project_id), __('messages.invoiceUpdated'));
        }
        return Reply::redirect(route('admin.client-invoice.index'), __('messages.invoiceUpdated'));
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        Invoice::destroy($id);
        $this->project = Project::findOrFail($invoice->project_id);
        $view = view('admin.projects.invoices.invoice-ajax', $this->data)->render();
        return Reply::successWithData(__('messages.invoiceDeleted'), ['html' => $view]);
    }

    public function download($id) {
        //header('Content-type: application/pdf');

        $this->invoice = Invoice::findOrFail($id);
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->creditNote = 0;
        if ($this->invoice->credit_note){
            $this->creditNote = CreditNotes::where('invoice_id', $id)
                ->select('cn_number')
                ->first();
        }

        // Download file uploaded
        if($this->invoice->file != null){
            return response()->download(storage_path('app/public/invoice-files').'/'.$this->invoice->file);
        }

        if($this->invoice->discount > 0){
            if($this->invoice->discount_type == 'percent'){
                $this->discount = (($this->invoice->discount/100)*$this->invoice->sub_total);
            }
            else{
                $this->discount = $this->invoice->discount;
            }
        }
        else{
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
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->company;

        $this->invoiceSetting = InvoiceSetting::first();
        //        return view('invoices.'.$this->invoiceSetting->template, $this->data);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('invoices.'.$this->invoiceSetting->template, $this->data);
        $filename = $this->invoice->invoice_number;
        //       return $pdf->stream();
        return $pdf->download($filename . '.pdf');
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
