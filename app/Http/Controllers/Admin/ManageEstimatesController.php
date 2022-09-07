<?php

namespace App\Http\Controllers\Admin;

use App\ClientDetails;
use App\Currency;
use App\DataTables\Admin\EstimatesDataTable;
use App\Estimate;
use App\EstimateItem;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\StoreEstimate;
use App\Http\Requests\UpdateEstimate;
use App\InvoiceSetting;
use App\Notifications\NewEstimate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Tax;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use App\Company;
use App\LineItemGroup;

// bitsclan code start here
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Estimate as QuickbookEstimate;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Customer;

use App\QuickbooksSettings;
use App\Invoice;
use App\InvoiceItems;
use App\Project;
use App\SalescategoryType;
use App\CodeType;
use App\ClientVendorDetails;
use App\Mail\ClientInvoiceEmail;
use Illuminate\Support\Facades\Mail;




// bitsclan code end here

class ManageEstimatesController extends AdminBaseController
{
    // bitsclan code start here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    // bitsclan code end here

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.estimates';
        $this->pageIcon = 'ti-file';
        $this->middleware(function ($request, $next) {
            if (!in_array('estimates', $this->user->modules)) {
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

    public function index(EstimatesDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/finance/estimates'));
        
        $this->totalRecords = Estimate::count();
        
        return $dataTable->render('admin.estimates.index', $this->data);
    }
    
    public function view($id)
    {
        
        $pageTitle = __('app.menu.estimates');
        $pageIcon = 'icon-people';
        $estimate = Estimate::findOrFail($id);
        $company = Company::find($estimate->company_id);
        $this->invoiceSetting = InvoiceSetting::first();
        
        
        if ($estimate->discount > 0) {
            if ($estimate->discount_type == 'percent') {
                $discount = (($estimate->discount / 100) * $estimate->sub_total);
            } else {
                $discount = $estimate->discount;
            }
        } else {
            $discount = 0;
        }

        $taxList = array();

        $items = EstimateItem::whereNotNull('taxes')
            ->where('estimate_id', $estimate->id)
            ->get();

    foreach ($items as $item) {
            if ($estimate->discount > 0 && $estimate->discount_type == 'percent') {
                $item->amount = $item->amount - (($estimate->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();
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
        
        $taxes = $taxList;
        
        // bottom tax names 
        
        $individual_tax = $estimate->total - ($estimate->sub_total+$estimate->total_tax);
        $individual_tax_name = '';
       
        if($estimate->tax_on_total) {
            foreach (json_decode($estimate->tax_on_total) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();
                if ($this->tax) {
                    if($individual_tax_name == '') {
                         $individual_tax_name = $this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    } else {
                        $individual_tax_name .= ' ,'.$this->tax->tax_name.'('.$this->tax->rate_percent.'%)';
                    }
                }
            }
        }
         // bottom tax names end
        
        

       

        $settings = $company;
        
        return view('admin.estimates.view', [
            'estimate' => $estimate,
            'taxes' => $taxes,
            'individual_tax' => $individual_tax,
            'individual_tax_name' => $individual_tax_name,
            'settings' => $settings,
            'discount' => $discount,
            'setting' => $settings,
            'global' => $this->global,
            'companyName' => $settings->company_name,
            'pageTitle' => $pageTitle,
            'pageIcon' => $pageIcon,
            'company' => $company,
            'invoiceSetting' => $this->invoiceSetting,
        ]);
    }

    public function create()
    {
        $default_project_id = 0;
        if(isset($request->project_id) && $request->project_id!='') {
            $default_project_id = $request->project_id;
        }
        
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
        
        $this->clients = ClientDetails::orderBy('name')->get();
        $this->currencies = Currency::all();
          
        $lastEstimate = Estimate::count();
        do {
            $lastEstimate += 1;
            $exists = false;
            $estimate = Estimate::where('estimate_number', $lastEstimate)->first();
            if($estimate) {
                $exists = true;
            }
        } while ($exists);
        $this->lastEstimate = $lastEstimate;
        
        //$latestEstimate =  Estimate::latest()->first();
        //$latestEstimate = Estimate::orderBy('estimate_number', 'desc')->first();
        $latestEstimate = DB::table('estimates')->where('company_id', company()->id)->orderByRaw('CONVERT(estimate_number, SIGNED) desc')->first();
        if($latestEstimate) {
            $estimate_number = trim($latestEstimate->estimate_number);
           if(is_numeric($estimate_number)) {
               $this->lastEstimate = $estimate_number + 1;
           }
        }
        
        
        //$this->lastEstimate = Estimate::count() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastEstimate) < $this->invoiceSetting->estimate_digit) {
            for ($i = 0; $i < $this->invoiceSetting->estimate_digit - strlen($this->lastEstimate); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        $this->groups = LineItemGroup::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        $this->review_products = $review_products;
        
        $this->projects = Project::orderBy('project_name')->get();
        
        $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        return view('admin.estimates.create', $this->data);
    }
    
    public function createEstimate(Request $request)
    {
        
        $default_project_id = $request->id;
        $this->default_project_id = $default_project_id;
        $this->project = Project::findOrFail($default_project_id);
        
        $this->clients = ClientDetails::all();
        $this->currencies = Currency::all();
        $this->lastEstimate = Estimate::count() + 1;
        
        
        //$latestEstimate =  Estimate::latest()->first();
        //$latestEstimate = Estimate::orderBy('estimate_number', 'desc')->first();
        $latestEstimate = DB::table('estimates')->where('company_id', company()->id)->orderByRaw('CONVERT(estimate_number, SIGNED) desc')->first();
        if($latestEstimate) {
            $estimate_number = trim($latestEstimate->estimate_number);
           if(is_numeric($estimate_number)) {
               $this->lastEstimate = $estimate_number + 1;
           }
        }
        
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastEstimate) < $this->invoiceSetting->estimate_digit) {
            for ($i = 0; $i < $this->invoiceSetting->estimate_digit - strlen($this->lastEstimate); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        $this->taxes = Tax::all();
        $this->groups = LineItemGroup::all();
        $this->products = Product::all();
        
        
        $this->projects = Project::all();
        $this->salescategories = $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        //return view('admin.projects.estimates.create', $this->data);
        
        
        // for not modal
        $html = view('admin.projects.estimates.create', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $html]);
        
    }

    public function store(StoreEstimate $request)
    {
        DB::beginTransaction();
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $pictures = $request->input('picture');
        $productImage = $request->file('product_img');
        $product_ids = $request->input('product_id');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $group =  $request->input('groups');
        $tax = $request->input('taxes');
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
        
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        $project_id = $request->input('project_id')?$request->input('project_id'):null;
        $invoice_item_type = $request->input('invoice_item_type');
        $show_shipping_address = $request->show_shipping_address ? 'yes' : 'no';
        
        
        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
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
      
        // bitsclan code start here
       

        $qbo_id = '';
        $items_array = array();
        
        $this->quickbook = $this->QuickbookSettings();
        if($this->quickbook){
            try {
            $quickbook_items = array();
            $adminSetting = User::where('email', ($this->user->email))->first();
            
            foreach ($items as $key => $item){
                
                $taxable = false;
                if($tax && array_key_exists($key, $tax)) {
                    $taxable = true;
                }
                
                $invoice_item_detail = InvoiceItems::where('item_name', $item)->first();
                $estimate_detail = EstimateItem::where('item_name', $item)->first();

                if(!empty($item_detail)){
                    if(!empty($item_detail->qbo_id)){
                        $item_qbo_id = $item_detail->qbo_id;                       
                    }else{
                        // echo 'item details';
                        
                        $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
                        }

                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => $item,
                            "Description" => $itemsSummary[$key],
                            "Active" => true,
                            "FullyQualifiedName" => $item,
                            "Taxable" => $taxable,
                            "UnitPrice" =>$unitPrice,
                            "Type" => "NonInventory",
                            "IncomeAccountRef"=> [
                                "name" => "Sales - Company Service", 
                                "value" => $adminSetting->income_account
                            ],
                            "PurchaseDesc"=> $itemsSummary[$key],
                            "PurchaseCost"=> $cost_per_item[$key],
                            "TrackQtyOnHand" => false,
                            "InvStartDate"=> $dateTime
                        ]);

                        $resultingItemObj = $this->quickbook->Add($Items); 
                        $error =  $this->quickbook->getLastError();
                        if($error){
                            //return Reply::error(__($error->getResponseBody()));
                        }      
                        $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                        $item_detail->qbo_id = $product_qbo_id;
                        $item_detail->save();
                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                    }

                    $item_to_be_pushed = array(  
                        'Description' => $itemsSummary[$key],
                        'DetailType' => "SalesItemLineDetail",
                        'SalesItemLineDetail' => array(
                            'Qty' => $quantity[$key],
                            'UnitPrice' => $cost_per_item[$key],
                            'ItemRef' => array(
                                'value' => $item_qbo_id,
                                'name' => $item
                            )
                        ),
                        'LineNum'=> $key+1, 
                        'Amount'=> $quantity[$key]*$cost_per_item[$key]
                    );
                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => $item,
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
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );
                    array_push($items_array, $item_arr);

                    // echo "<pre>";
                    // print_r($items_array);
                    // exit();

                    

                }

                else if(!empty($invoice_item_detail)){

                    if(!empty($invoice_item_detail->qbo_id)){
                         $item_qbo_id = $invoice_item_detail->qbo_id;
                    }else{
                         //echo 'invoice item  details';
                        
                        $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
                        }
                     
                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => $item,
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
                            "PurchaseCost"=> $cost_per_item[$key],
                            "TrackQtyOnHand" => false,
                            "InvStartDate"=> $dateTime
                        ]);

                        $resultingItemObj = $this->quickbook->Add($Items);
                        $error =  $this->quickbook->getLastError();
                        if($error){
                            //return Reply::error(__($error->getResponseBody()));
                        }   

                        $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                        $invoice_item_detail->qbo_id = $product_qbo_id;
                        $invoice_item_detail->save();

                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> $quantity[$key]*$cost_per_item[$key]);

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => $item,
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
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );

                    array_push($items_array, $item_arr);
                    //  echo "<pre>";
                    // print_r($items_array);
                    // exit();

                }

                elseif (!empty($estimate_detail)) {

                    if(!empty($estimate_detail->qbo_id)){
                        $item_qbo_id = $estimate_detail->qbo_id;
                    }else{
                        // echo 'estimate item details';
                        
                        $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
                        }
                    
                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => $item,
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
                            "PurchaseCost"=> $cost_per_item[$key],
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

                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> $quantity[$key]*$cost_per_item[$key]);

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => $item,
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
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );

                    array_push($items_array, $item_arr);
                    //  echo "<pre>";
                    // print_r($items_array);
                    // exit();
                }

                else{
                     //echo 'else ';
                    
                    $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
                        }
                  
                    $dateTime = new \DateTime('NOW');
                    $Items = Item::create([
                        "Name" => $item,
                        "Description" => $itemsSummary[$key],
                        "Active" => true,
                        "FullyQualifiedName" => $item,
                        "Taxable" => $taxable,
                        "UnitPrice" =>$unitPrice,
                        "Type" => "NonInventory",
                        "IncomeAccountRef"=> [
                            "name" => "Sales - Company Service", 
                            "value" => $adminSetting->income_account
                        ],
                        "PurchaseDesc"=> $itemsSummary[$key],
                        "PurchaseCost"=> $cost_per_item[$key],
                        "TrackQtyOnHand" => false,
                        "InvStartDate"=> $dateTime
                    ]);

                    $resultingItemObj = $this->quickbook->Add($Items);
                    $error =  $this->quickbook->getLastError();
                    if($error){
                        //return Reply::error(__($error->getResponseBody()));
                    }

                    $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';


                    $item_to_be_pushed = array(
                        'Description' => $itemsSummary[$key],
                        "DetailType" => "SalesItemLineDetail",
                        "SalesItemLineDetail" => array(
                            'Qty' => $quantity[$key],
                            'UnitPrice' => $cost_per_item[$key],
                            'ItemRef' => array(
                                'value' => $product_qbo_id,
                                'name' => $item
                            )
                        ),
                        "LineNum"=> $key+1, 
                        "Amount"=> $quantity[$key]*$cost_per_item[$key]
                    );

                    array_push($quickbook_items, $item_to_be_pushed);


                    $item_arr = array(
                        'item_name' => $item,
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key])?$group[$key]:null,
                        'qbo_id' =>  $product_qbo_id,
                        'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                        'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                        'markup' => isset($markups[$key])?$markups[$key]:'0.00',
                        'markup_fix' => isset($markup_fix[$key])?$markup_fix[$key]:'0.00',
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );

                    array_push($items_array, $item_arr);
                    

                }

            }


            $id = $request->client_id ? $request->client_id : '';
            $client_qbo = ClientDetails::where('user_id', $id)->first();
            
            $client_name = ''; 
            $client_qbo_id = '';
            
            if ($client_qbo) {
                    if (!empty($client_qbo->qbo_id)) {
                        $client_qbo_id = isset($client_qbo->qbo_id) ? $client_qbo->qbo_id : '';
                        $client_name = isset($client_qbo->name) ? $client_qbo->name : '';
                    } else {

                        if (!empty($client_qbo->address)) {
                            $address = preg_split('/\r\n|\r|\n/', $client_qbo->address);
                            $Line1 = isset($address[0]) ? $address[0] : '';
                            if (isset($address[1])) {
                                $address2 = explode(',', trim($address[1]));
                                $City = isset($address2[0]) ? $address2[0] : '';
                            }
                            if (isset($address2[1])) {
                                $address3 = explode(' ', trim($address2[1]));
                            }
                            $CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
                            $PostalCode = isset($address3[1]) ? $address3[1] : '';

                            $BillAddr = [
                                "Line1" => $Line1,
                                "City" => $City,
                                "CountrySubDivisionCode" => $CountrySubDivisionCode,
                                "PostalCode" => $PostalCode,
                            ];
                        }

                        if (!empty($client_qbo->shipping_address)) {

                            $ship_address = preg_split('/\r\n|\r|\n/', $client_qbo->shipping_address);
                            $Ship_Line1 = isset($ship_address[0]) ? $ship_address[0] : '';
                            if (isset($ship_address[1])) {
                                $ship_address2 = explode(',', trim($ship_address[1]));
                                $Ship_City = isset($ship_address2[0]) ? $ship_address2[0] : '';
                            }
                            if (isset($ship_address[1]) && isset($ship_address2[1])) {
                                $ship_address3 = explode(' ', trim($ship_address2[1]));
                            }
                            $ship_CountrySubDivisionCode = isset($ship_address3[0]) ? $ship_address3[0] : '';
                            $ship_PostalCode = isset($ship_address3[1]) ? $ship_address3[1] : '';

                            $ShipAddr = [
                                "Line1" => $Ship_Line1,
                                "City" => $Ship_City,
                                "CountrySubDivisionCode" => $ship_CountrySubDivisionCode,
                                "PostalCode" => $ship_PostalCode,
                            ];
                        }

                        $Addr = [
                            "Line1" => '',
                            "City" => '',
                            "CountrySubDivisionCode" => '',
                            "PostalCode" => '',
                        ];

                        $customerObj = Customer::create([
                                    "BillAddr" => isset($BillAddr) ? $BillAddr : '',
                                    "ShipAddr" => isset($ShipAddr) ? $ShipAddr : '',
                                    "GivenName" => $client_qbo->name,
                                    "FullyQualifiedName" => $client_qbo->name,
                                    "CompanyName" => isset($client_qbo->company_name) ? $client_qbo->company_name : $client_qbo->name,
                                    "DisplayName" => $client_qbo->name,
                                    "PrimaryPhone" => [
                                        "FreeFormNumber" => $client_qbo->mobile
                                    ],
                                    "PrimaryEmailAddr" => [
                                        "Address" => $client_qbo->email
                                    ]
                        ]);

                        $resultingCustomerObj = $this->quickbook->Add($customerObj);
                        $error = $this->quickbook->getLastError();
                        if ($error) {
                            //return Reply::error(__($error->getResponseBody()));
                        }

                        $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
                        $client_qbo->qbo_id = $qbo_id;
                        $client_qbo->save();
                        $client_name = isset($client_qbo->name) ? $client_qbo->name : '';
                        $client_qbo_id = $qbo_id;
                    }
                }

                //all invoices code here
            $estimateObj = QuickbookEstimate::create([

                "DocNumber" => rand(1111,9999), 
                "SyncToken" => "0", 
                "domain" => "QBO", 
                "TxnStatus" => "Pending", 
                "TxnDate" => Date('Y-m-d'),
                "TotalAmt" => round($amount[$key], 2),
                "CustomerRef"=> [
                    "name"=> $client_name, 
                    "value"=> $client_qbo_id
                ], 

                "PrintStatus" => "NeedToPrint", 
                "Line"=> $quickbook_items, 
                "ApplyTaxAfterDiscount"=> false, 
            ]);

            $resultingCustomerObj = $this->quickbook->Add($estimateObj);
            $error =  $this->quickbook->getLastError();
            if($error){
                //return Reply::error(__($error->getResponseBody()));
            }
            $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
            } catch (\Exception $e) {}
        }


        // bitsclan code end here
        
        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }


        $estimate = new Estimate();
        $estimate->client_id = $request->client_id ? $request->client_id : '';
        //$estimate->estimate_number = Estimate::count() + 1;
        $estimate->estimate_number = $request->estimate_number;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->note = $request->note;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->status = 'waiting';
        $estimate->tax_on_total = $request->tax_on_total ? json_encode($request->tax_on_total) : null;
        $estimate->qbo_id = isset($qbo_id) ? $qbo_id : ''; /////////////////changes Required
        $estimate->shipping_total = round($request->shipping_total, 2);
        $estimate->project_id = $project_id;
        /*dd($estimate->estimate_number);*/
        
        
        $estimate->total_tax = round($request->total_tax, 2);
        
        $estimate->card_processing_value = $request->card_processing_value ? round($request->card_processing_value, 2) : 0;
        $estimate->card_processing_type = $request->card_processing_type ? $request->card_processing_type : 'percent';
        
        $estimate->deposit_request = round($request->deposit_request, 2);
        $estimate->deposit_request_type = $request->deposit_request_type ? $request->deposit_request_type : 'percent';
        $estimate->deposit_req = round($request->deposit_req, 2);
        $estimate->combine_line_items = $combine_line_items;
        
        //$estimate->tags = json_encode($request->tags);
        $estimate->tags = json_encode(array());
        if($request->tags) {
            $estimate->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        
        
        $estimate->show_shipping_address = $show_shipping_address;
        $estimate->shipping_address = $request->shipping_address;
        
        
        
        $estimate->save();
        
        $this->invoiceSetting = InvoiceSetting::first();
        $status = 'pending';
        if($this->invoiceSetting->line_item_approval == 'no') {
            $status = 'approved';
        }

        if($this->quickbook && $qbo_id != '') {
            if (!empty($items_array)) {

                foreach ($items_array as $key => $item) :
                    if (!is_null($item)) {
                        $fileName = null;
                        $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                        if ($existKey){
                            $file = $productImage[$key]->getClientOriginalName();
                            $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                            $extension = pathinfo($file, PATHINFO_EXTENSION);

                            $fileName = time().mt_rand().".".$extension;
                        }
                        
                        try {
                            $estimateItem = EstimateItem::create([
                                        'estimate_id' => $estimate->id,
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
                                        'invoice_item_type' => $item['invoice_item_type'],
                                        'sale_price' => $item['sale_price'],
                                        'shipping_price' => $item['shipping_price'],
                                        'status' => $status,
                                        'product_image'=> $fileName, 
                                    ]);

                            if ($existKey) {
                                $directory = "user-uploads/estimates/products/$estimateItem->id";
                                if (!File::exists(public_path($directory))) {
                                    $result = File::makeDirectory(public_path($directory), 0775, true);
                                }
                                $imageFilePath = "$directory/$fileName";

                                File::move($productImage[$key], public_path($imageFilePath));
                                $estimateItem->save();
                            }
                        } catch (\Exception $e) {
                            
                        }
                    }
                endforeach;
            }
        } else {
            // added by SB
            foreach ($items as $key => $item) :
                if (!is_null($item)) {

                    $fileName = null;
                    $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                    if ($existKey){
                        $file = $productImage[$key]->getClientOriginalName();
                        $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                        $extension = pathinfo($file, PATHINFO_EXTENSION);

                        $fileName = time().mt_rand().".".$extension;
                    }

                    
                    try {
                        $estimateItem = EstimateItem::create([
                                    'estimate_id' => $estimate->id,
                                    'item_name' => $item,
                                    'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                                    'type' => 'item',
                                    'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                                    'unit_price' => isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                                    'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                                    'group_id' => isset($group[$key])?$group[$key]:null,
                                    'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                                    'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                                    'markup' => isset($markups[$key]) ? $markups[$key] : '0.00',
                                    'markup_fix' => isset($markup_fix[$key]) ? $markup_fix[$key] : '0.00',
                                    'invoice_item_type' => isset($invoice_item_type[$key]) ? $invoice_item_type[$key] : 'product',
                                    'sale_price' => isset($sale_prices[$key]) ? $sale_prices[$key] : '0.00',
                                    'shipping_price' => isset($shipping_prices[$key]) ? $shipping_prices[$key] : '0.00',
                                    'status' => $status,
                                    'product_image'=> $fileName, 
                                ]);

                        if ($existKey) {
                            $directory = "user-uploads/estimates/products/$estimateItem->id";
                            if (!File::exists(public_path($directory))) {
                                $result = File::makeDirectory(public_path($directory), 0775, true);
                            }
                            $imageFilePath = "$directory/$fileName";

                            File::move($productImage[$key], public_path($imageFilePath));
                            $estimateItem->save();
                        }

                        
                    } catch (\Exception $e) {
                        
                    }
                }
            endforeach;
            // end SB
        }

        $this->logSearchEntry($estimate->id, 'Estimate #' . $estimate->id, 'admin.estimates.edit', 'estimate');
        DB::commit();
        
        if(isset($request->cal_from) && $request->cal_from == 'project') {
             return Reply::successWithData(__('messages.estimateCreated'), ['html' => '']);
        }

        return Reply::redirect(route('admin.estimates.index'), __('messages.estimateCreated'));
    }

    public function edit($id)
    {
        $this->estimate = Estimate::findOrFail($id);
        $this->invoiceSetting = InvoiceSetting::first();
        
        $tags = $this->estimate->tags ? json_decode($this->estimate->tags) : array();
        $this->estimate->tags = $tags;
        
        if($tags) {
            $this->estimate->tags = array_values(array_unique($tags));
        }
        
        $this->clients = ClientDetails::orderBy('name')->get();
        $this->currencies = Currency::all();
        $this->groups = LineItemGroup::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        
        $this->projects = Project::orderBy('project_name')->get();
        $this->salescategories = SalescategoryType::all();
        $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        return view('admin.estimates.edit', $this->data);
    }

    public function update(UpdateEstimate $request, $id)
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
        $group = $request->input('groups');
        
        $markups = $request->input('markup');
        $markup_fix = $request->input('markup_fix');
        
        $sale_prices = $request->input('sale_price');
        $shipping_prices = $request->input('shipping_price');
        $project_id = $request->input('project_id')?$request->input('project_id'):null;
        $invoice_item_type = $request->input('invoice_item_type');
        
        $show_shipping_address = $request->show_shipping_address ? 'yes' : 'no';

        $old_items = $request->input('old_items') ?? [];
        
        
        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

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
        
        
        //bitsclan code here

        $estimate_id = $id;
        $items_array = array();
        $qbo_id = null;

        $this->quickbook = $this->QuickbookSettings();
        if($this->quickbook){
            try {
            
                //all invoices code
            $quickbook_items = array();
            $adminSetting = User::where('email', ($this->user->email))->first();
            foreach ($items as $key => $item){
                
                $taxable = false;
                if($tax && array_key_exists($key, $tax)) {
                    $taxable = true;
                }
                
               $item_detail = Product::where('name', $item)->first();
               $invoice_item_detail = InvoiceItems::where('item_name', $item)->first();
               $estimate_item_detail = EstimateItem::where('item_name', $item)->first();

                if(!empty($item_detail)){

                    if(!empty($item_detail->qbo_id)){
                        $item_qbo_id = $item_detail->qbo_id;                                           
                    }
                    else{
                        
                         $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
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
                            "PurchaseCost"=> $cost_per_item[$key],
                            "TrackQtyOnHand" => false,
                            "InvStartDate"=> $dateTime
                        ]);

                        $resultingItemObj = $this->quickbook->Add($Items);
                        $error =  $this->quickbook->getLastError();
                        if($error){
                            //return Reply::error(__($error->getResponseBody()));
                        }    
                        $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                        $item_detail->qbo_id = $product_qbo_id;
                        $item_detail->save();

                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';

                    }

                    $item_to_be_pushed = array(
                        'Description' => $itemsSummary[$key],
                        "DetailType" => "SalesItemLineDetail",
                        'SalesItemLineDetail' => array(
                            'Qty' => $quantity[$key],
                            'UnitPrice' => $cost_per_item[$key],
                            'ItemRef' => array(
                                'value' => $item_qbo_id,
                                'name' => $item
                            )
                        ),
                        "LineNum"=> $key+1, 
                        "Amount"=> $quantity[$key]*$cost_per_item[$key]
                     );
                

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => $item,
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
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );

                    array_push($items_array, $item_arr);


                }

                else if(!empty($invoice_item_detail)){

                    if(!empty($invoice_item_detail->qbo_id)){
                        $item_qbo_id = $invoice_item_detail->qbo_id;
                    }else{
                        
                        $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
                        }
                        
                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => $item,
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
                            "PurchaseCost"=> $cost_per_item[$key],
                            "TrackQtyOnHand" => false,
                            "InvStartDate"=> $dateTime
                        ]);

                        $resultingItemObj = $this->quickbook->Add($Items);
                        $error =  $this->quickbook->getLastError();
                        if($error){
                            //return Reply::error(__($error->getResponseBody()));
                        }      
                        $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                        $invoice_item_detail->qbo_id = $product_qbo_id;
                        $invoice_item_detail->save();

                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> $quantity[$key]*$cost_per_item[$key]);

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => $item,
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
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );
                    array_push($items_array, $item_arr);


                }

                else if(!empty($estimate_item_detail)){

                    if(!empty($estimate_item_detail->qbo_id)){
                        $item_qbo_id = $estimate_item_detail->qbo_id;
                    }else{
                        
                        $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
                        }
                        
                        $dateTime = new \DateTime('NOW');
                        $Items = Item::create([
                            "Name" => $item,
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
                            "PurchaseCost"=> $cost_per_item[$key],
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

                        $item_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                    }

                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $item_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> $quantity[$key]*$cost_per_item[$key]);

                    array_push($quickbook_items, $item_to_be_pushed);

                    $item_arr = array(
                        'item_name' => $item,
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
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );
                    array_push($items_array, $item_arr);

                }
                else{
                    
                    $unitPrice = $sale_prices[$key];
                        if($shipping_prices[$key]) {
                            $unitPrice += $shipping_prices[$key];
                        }

                    $dateTime = new \DateTime('NOW');
                    $Items = Item::create([
                        "Name" => $item,
                        "Description" => $itemsSummary[$key],
                        "Active" => true,
                        "FullyQualifiedName" => $item,
                        "Taxable" => $taxable,
                        "UnitPrice" => round($unitPrice, 2),
                        "Type" => "NonInventory",
                        "IncomeAccountRef"=> [
                            "name" => "Sales - Company Service", 
                            "value" => $adminSetting->income_account
                        ],
                        "PurchaseDesc"=> $itemsSummary[$key],
                        "PurchaseCost"=> round($cost_per_item[$key], 2),
                        "TrackQtyOnHand" => false,
                        "InvStartDate"=> $dateTime
                    ]);

                    $resultingItemObj = $this->quickbook->Add($Items);      
                    $error =  $this->quickbook->getLastError();
                    if($error){
                        //return Reply::error(__($error->getResponseBody()));
                    }
                    $product_qbo_id = isset($resultingItemObj->Id) ? $resultingItemObj->Id : '';
                


                    $item_to_be_pushed = array('Description' => $itemsSummary[$key],"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $quantity[$key],'UnitPrice' => $cost_per_item[$key],'ItemRef' => array('value' => $product_qbo_id, 'name' => $item)),"LineNum"=> $key+1, "Amount"=> $quantity[$key]*$cost_per_item[$key]);

                    array_push($quickbook_items, $item_to_be_pushed);


                    $item_arr = array(
                        'item_name' => $item,
                        'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                        'type' => 'item', 
                        'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                        'unit_price' =>   isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                        'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                        'group_id' => isset($group[$key])?$group[$key]:null,
                        'qbo_id' =>  $product_qbo_id,
                        'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                        'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                        'markup' => isset($markups[$key])?$markups[$key]:'0.00',
                        'markup_fix' => isset($markup_fix[$key])?$markup_fix[$key]:'0.00',
                        'invoice_item_type' => isset($invoice_item_type[$key])?$invoice_item_type[$key]:'product',
                        'sale_price' => isset($sale_prices[$key])?$sale_prices[$key]:'0.00',
                        'shipping_price' => isset($shipping_prices[$key])?$shipping_prices[$key]:'0.00'
                    );

                    array_push($items_array, $item_arr);
                }
            
            }
            $client_id = $request->client_id;
            $client_qbo = ClientDetails::where('user_id', $client_id)->first();
            if(!empty($client_qbo->qbo_id)){
                $client_qbo_id = isset($client_qbo->qbo_id) ? $client_qbo->qbo_id : '';
                $client_name = isset($client_qbo->name) ? $client_qbo->name : '';
            }else{
                if(!empty($client_qbo->address)){
                    $address = preg_split('/\r\n|\r|\n/', $client_qbo->address); 
                    $Line1 = isset($address[0]) ? $address[0] : '';
                    if(isset($address[1])){
                        $address2 =  explode(',',trim($address[1]));
                        $City = isset($address2[0]) ? $address2[0] : '';
                    }
                    if(isset($address2[1])){
                        $address3 =  explode(' ',trim($address2[1]));
                    } 
                    $CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
                    $PostalCode = isset($address3[1]) ? $address3[1] : '';

                    $BillAddr =  [
                        "Line1"=>  $Line1,
                        "City"=>  $City,
                        "CountrySubDivisionCode"=>  $CountrySubDivisionCode,
                        "PostalCode"=>  $PostalCode,
                    ];

                }

                if(!empty($client_qbo->shipping_address))
                {   
                
                    $ship_address = preg_split('/\r\n|\r|\n/', $client_qbo->shipping_address);
                    $Ship_Line1 = isset($ship_address[0]) ? $ship_address[0] : ''; 
                    if(isset($ship_address[1])){
                        $ship_address2 =  explode(',',trim($ship_address[1]));
                        $Ship_City = isset($ship_address2[0]) ? $ship_address2[0] : '';
                    }
                    if(isset($ship_address[1]) && isset($ship_address2[1])){
                        $ship_address3 =  explode(' ',trim($ship_address2[1]));
                    }
                    $ship_CountrySubDivisionCode = isset($ship_address3[0]) ? $ship_address3[0] : '';
                    $ship_PostalCode = isset($ship_address3[1]) ? $ship_address3[1] : '';

                    $ShipAddr = [
                        "Line1"=>  $Ship_Line1,
                        "City"=>  $Ship_City,
                        "CountrySubDivisionCode"=>  $ship_CountrySubDivisionCode,
                        "PostalCode"=>  $ship_PostalCode,
                    ];
                }

                $Addr = [
                    "Line1"=>  '',
                    "City"=> '',
                    "CountrySubDivisionCode"=> '',
                    "PostalCode"=>  '',
                ];

                $customerObj = Customer::create([
                    "BillAddr" => isset($BillAddr) ? $BillAddr : '',
                    "ShipAddr" => isset($ShipAddr) ? $ShipAddr : '',
                    "GivenName"=>  $client_qbo->name,
                    "FullyQualifiedName"=>  $client_qbo->name,
                    "CompanyName"=>  isset($client_qbo->company_name) ? $client_qbo->company_name : $client_qbo->name,
                    "DisplayName"=>  $client_qbo->name,
                    "PrimaryPhone"=>  [
                        "FreeFormNumber"=>  $client_qbo->mobile
                    ],
                    "PrimaryEmailAddr"=>  [
                        "Address" => $client_qbo->email
                    ]
                ]);
                $resultingCustomerObj = $this->quickbook->Add($customerObj);
                $error =  $this->quickbook->getLastError();
                if($error){
                    //return Reply::error(__($error->getResponseBody()));
                }
                $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
                $client_qbo->qbo_id =  $qbo_id;
                $client_qbo->save();
                $client_name = isset($client_qbo->name) ? $client_qbo->name : '';
                $client_qbo_id =  $qbo_id;

            }

        
            
            //all invoices code here
            $estimate = Estimate::findOrFail($estimate_id);
            if(!empty($estimate->qbo_id))
            {
                $entities = $this->quickbook->Query("SELECT * FROM Estimate where Id='".$estimate->qbo_id."'");
                $theEstimate = reset($entities);
                    //dd($theEstimate);

                $estimateObj = QuickbookEstimate::Update($theEstimate,[

                "TotalAmt" => round($amount[$key], 2),
                "CustomerRef"=> [
                    "name"=> $client_name, 
                    "value"=> $client_qbo_id
                 ], 
                "Line"=> $quickbook_items, 
                "ApplyTaxAfterDiscount"=> false, 
                ]);

               $resultingCustomerObj = $this->quickbook->update($estimateObj);
                    //all invoices code here
            }else{

                $estimateObj = QuickbookEstimate::create([

                "DocNumber" => rand(1111,9999), 
                "SyncToken" => "0", 
                "domain" => "QBO", 
                "TxnStatus" => "Pending", 
                "TxnDate" => Date('Y-m-d'),
                "TotalAmt" => round($amount[$key], 2),
                "CustomerRef"=> [
                    "name"=> $client_name, 
                    "value"=> $client_qbo_id
                ], 

                "PrintStatus" => "NeedToPrint", 
                "Line"=> $quickbook_items, 
                "ApplyTaxAfterDiscount"=> false, 
            ]);

            $resultingCustomerObj = $this->quickbook->Add($estimateObj);

            }

            $error =  $this->quickbook->getLastError();
            if($error){
                //return Reply::error(__($error->getResponseBody()));
            }
            $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
    } catch (\Exception $e) {
    }
    
    
            }
            
        $combine_line_items = 0;
        if($request->combine_line_items == 'on') {
            $combine_line_items = 1;
        }


        $estimate = Estimate::findOrFail($id);
        $estimate->client_id = $request->client_id;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->status = $request->status;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->note = $request->note;
        $estimate->tax_on_total = $request->tax_on_total ? json_encode($request->tax_on_total) : null;
        $estimate->shipping_total = round($request->shipping_total, 2);
        $estimate->qbo_id = $qbo_id;
        $estimate->project_id = $project_id;
        
        $estimate->total_tax = round($request->total_tax, 2);
        
        $estimate->card_processing_value = $request->card_processing_value ? round($request->card_processing_value, 2) : 0;
        $estimate->card_processing_type = $request->card_processing_type ? $request->card_processing_type : 'percent';
        
        $estimate->deposit_request = round($request->deposit_request, 2);
        $estimate->deposit_request_type = $request->deposit_request_type ? $request->deposit_request_type : 'percent';
        $estimate->deposit_req = round($request->deposit_req, 2);
        $estimate->combine_line_items = $combine_line_items;
        
        //$estimate->tags = json_encode($request->tags);
        
        $estimate->tags = json_encode(array());
        if($request->tags) {
            $estimate->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        
        $estimate->show_shipping_address = $show_shipping_address;
        $estimate->shipping_address = $request->shipping_address;
        
        $estimate->save();
        
        $this->invoiceSetting = InvoiceSetting::first();
        $status = 'pending';
        if($this->invoiceSetting->line_item_approval == 'no') {
            $status = 'approved';
        }

        $estimateItemQuery = EstimateItem::query()->where('estimate_id', $estimate->id)->where('product_image','!=',null);
        $oldEstimateItemImg = $estimateItemQuery->pluck('product_image','id')->toArray();

        // delete and create new
        EstimateItem::where('estimate_id', $estimate->id)->delete();
        
       if ($this->quickbook && $qbo_id != '') {
            foreach ($items_array as $key => $item) :

                $fileName = null;
                $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                if ($existKey){
                    $file = $productImage[$key]->getClientOriginalName();
                    $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);

                    $fileName = time().mt_rand().".".$extension;
                }
                $fname = isset($old_items[$key]) && !empty($old_items[$key]) ? $old_items[$key] : 'N/A';
                $oldPath = public_path('user-uploads/estimates/products/'.$fname);
                if(file_exists($oldPath)){
                    $fileName = $oldEstimateItemImg[$old_items[$key]];
                }
                
                try {
                    $estimateItem = EstimateItem::create([
                                'estimate_id' => $estimate->id,
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
                                'invoice_item_type' => $item['invoice_item_type'],
                                'sale_price' => $item['sale_price'],
                                'shipping_price' => $item['shipping_price'],
                                'status' => $status,
                                 'product_image'=> $fileName,
                            ]);

                    if(file_exists($oldPath)){
                        $newPath = public_path('user-uploads/estimates/products/'.$estimateItem->id);
                        File::moveDirectory($oldPath,$newPath);
                    }

                    if ($existKey) {
                        $directory = "user-uploads/estimates/products/$estimateItem->id";
                        if (!File::exists(public_path($directory))) {
                            $result = File::makeDirectory(public_path($directory), 0775, true);
                        }
                        $imageFilePath = "$directory/$fileName";

                        File::move($productImage[$key], public_path($imageFilePath));
                        $estimateItem->save();
                    }

                } catch (\Exception $e) {
                    
                }

            endforeach;
        } else {

            // added by SB
            foreach ($items as $key => $item) :
                if (!is_null($item)) {

                    $fileName = null;
                    $existKey = is_array($productImage) && in_array($key, array_keys($productImage));
                    if ($existKey){
                        $file = $productImage[$key]->getClientOriginalName();
                        $orgFileName = pathinfo($file, PATHINFO_FILENAME);
                        $extension = pathinfo($file, PATHINFO_EXTENSION);

                        $fileName = time().mt_rand().".".$extension;
                    }
                    $fname = isset($old_items[$key]) && !empty($old_items[$key]) ? $old_items[$key] : 'N/A';
                    $oldPath = public_path('user-uploads/estimates/products/'.$fname);
                    if(file_exists($oldPath)){
                        $fileName = $oldEstimateItemImg[$old_items[$key]];
                    }
                    
                    try {
                        $estimateItem = EstimateItem::create([
                                    'estimate_id' => $estimate->id,
                                    'item_name' => $item,
                                    'item_summary' => isset($itemsSummary[$key]) ? $itemsSummary[$key] : '',
                                    'type' => 'item',
                                    'quantity' => isset($quantity[$key]) ? $quantity[$key] : '0',
                                    'unit_price' => isset($cost_per_item[$key]) ? round($cost_per_item[$key], 2) : '0.00',
                                    'amount' => isset($amount[$key]) ? round($amount[$key], 2) : '0.00',
                                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null,
                                    'group_id' => isset($group[$key])?$group[$key]:null,
                                    'picture' => isset($pictures[$key]) ? $pictures[$key] : '',
                                    'product_id' => isset($product_ids[$key]) ? $product_ids[$key] : '',
                                    'markup' => isset($markups[$key]) ? $markups[$key] : '0.00',
                                    'markup_fix' => isset($markup_fix[$key]) ? $markup_fix[$key] : '0.00',
                                    'invoice_item_type' => isset($invoice_item_type[$key]) ? $invoice_item_type[$key] : 'product',
                                    'sale_price' => isset($sale_prices[$key]) ? $sale_prices[$key] : '0.00',
                                    'shipping_price' => isset($shipping_prices[$key]) ? $shipping_prices[$key] : '0.00',
                                    'status' => $status,
                                    'product_image'=> $fileName,
                        ]);

                        if(file_exists($oldPath)){
                            $newPath = public_path('user-uploads/estimates/products/'.$estimateItem->id);
                            File::moveDirectory($oldPath,$newPath);
                        }

                        if ($existKey) {
                            $directory = "user-uploads/estimates/products/$estimateItem->id";
                            if (!File::exists(public_path($directory))) {
                                $result = File::makeDirectory(public_path($directory), 0775, true);
                            }
                            $imageFilePath = "$directory/$fileName";

                            File::move($productImage[$key], public_path($imageFilePath));
                            $estimateItem->save();
                        }

                    } catch (\Exception $e) {
                        
                    }
                }
            endforeach;
            // end SB
        }

        // foreach ($items as $key => $item) :
        //     EstimateItem::create(
        //         [
        //             'estimate_id' => $estimate->id,
        //             'item_name' => $item,
        //             'item_summary' => $itemsSummary[$key],
        //             'quantity' => $quantity[$key],
        //             'unit_price' => round($cost_per_item[$key], 2),
        //             'amount' => round($amount[$key], 2),
        //             'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
        //         ]
        //     );
        // endforeach;

        return Reply::redirect(route('admin.estimates-project.show', $project_id), __('messages.estimateUpdated'));
        //return Reply::redirect(route('admin.estimates.index'), __('messages.estimateUpdated'));
    }

    public function destroy($id)
    {
        $firstEstimate = Estimate::orderBy('id', 'desc')->first();
        //if ($firstEstimate->id == $id) {
            Estimate::destroy($id);
            return Reply::success(__('messages.estimateDeleted'));
//        } else {
//            return Reply::error(__('messages.estimateCanNotDeleted'));
//        }
    }

    public function domPdfObjectForDownload($id)
    {
        $this->estimate = Estimate::findOrFail($id);
        $this->company = company();
        $this->invoiceSetting = InvoiceSetting::first();
        
        if ($this->estimate->discount > 0) {
            if ($this->estimate->discount_type == 'percent') {
                $this->discount = (($this->estimate->discount / 100) * $this->estimate->sub_total);
            } else {
                $this->discount = $this->estimate->discount;
            }
        } else {
            $this->discount = 0;
        }
        $taxList = array();

        $items = EstimateItem::whereNotNull('taxes')
            ->where('estimate_id', $this->estimate->id)
            ->get();

        foreach ($items as $item) {
            if ($this->estimate->discount > 0 && $this->estimate->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->estimate->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();
                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        if($this->invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * ($item->amount-$item->shipping_price);
                        } else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                        }
                        
                    } else {
                         if($this->invoiceSetting->shipping_taxed == 'no'){
                             $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * ($item->amount-$item->shipping_price));
                         } else {
                             $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                         }
                        
                    }
                }
            }
        }

        $this->taxes = $taxList;
        
        $this->individual_tax = $this->estimate->total - ($this->estimate->sub_total+$this->estimate->total_tax);
        
        $individual_tax_name = '';
       
        if($this->estimate->tax_on_total) {
            foreach (json_decode($this->estimate->tax_on_total) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();
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

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.estimates.estimate-pdf', $this->data);
        $filename = $this->estimate->estimate_number;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function download($id)
    {
        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return $pdf->download($filename . '.pdf');
    }
    
    // send with pdf
    public function sendEstimate($id)
    {
        $this->company = company();
        
        $estimate = Estimate::findOrFail($id);
        
        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];
        
        $objDemo = new \stdClass();
        $objDemo->Message = __('email.estimate.text_new');
        $objDemo->Subject = __('email.estimate.subject').' ['.$estimate->estimate_number .']';
        $objDemo->thankyouNote = __('email.thankyouNote');
        $objDemo->FromEmail = $this->user->email;
        $objDemo->FromName = $this->user->name;
        $objDemo->pdf = $pdf;
        $objDemo->filename = $filename;
        $objDemo->estimateID = $id;
        
        if (isset($estimate->client)) {
            Mail::to($estimate->client->email)->send(new ClientInvoiceEmail($objDemo));
        }
        
        $estimate->send_status = 1;
        $estimate->save();
        return Reply::success(__('messages.updateSuccess'));

    }
    
    //old method

    public function sendEstimate_bk($id)
    {
        $estimate = Estimate::findOrFail($id);
        $estimate->client->notify(new NewEstimate($estimate));
        
        $estimate->send_status = 1;
        $estimate->save();
        return Reply::success(__('messages.updateSuccess'));

    }
    
}
