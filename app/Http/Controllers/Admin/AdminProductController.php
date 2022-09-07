<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ProductsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreProductImageRequest;
// use App\Http\Requests\Product\UpdateProductRequest;
use App\Product;
use App\Project;
use App\Tax;
use App\CodeType;
use App\SalescategoryType;
use App\ClientVendorDetails;
use App\User;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Scopes\CompanyScope;
use App\InvoiceSetting;

use App\ShortLink;
// bitsclan code start here

use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Item;
use App\QuickbooksSettings;

use App\ProductProject;
use App\ProductCodeType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\RFQEmail;
use App\ProductSetting;
use App\ProductStatus;
use App\FileStorage;
use App\Helper\Files;
use Illuminate\Pagination\Paginator;

// bitsclan code end here

class AdminProductController extends AdminBaseController
{

    // bitsclan code start here

    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';

    // bitsclan code end here

    /**
     * AdminProductController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.products';
        $this->pageIcon = 'icon-basket';

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
    
     public function index(Request $request)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/products'));
        
        $user = Auth::user();
        if (!$user['uuid']) {
            $user['uuid'] = Str::random(15);
            $user->save();
        }
        $uuid = $user['uuid'];
        $this->uuid = $uuid;
        
        //insert default statsues if not exist
        $statusesArr = [
            ['status_name' => 'Proposed', 'status_color' => '#4600ff'],
            ['status_name' => 'Ordered', 'status_color' => '#ba5a5a'],
            ['status_name' => 'Backordered', 'status_color' => '#ff0000'],
            ['status_name' => 'Shipped', 'status_color' => '#00b46b'],
            ['status_name' => 'Received', 'status_color' => '#ffea00'],
            ['status_name' => 'Installed', 'status_color' => '#fe00ff']
        ];
        
       foreach ($statusesArr as $status) {
            $getStatus = ProductStatus::where('status_name', $status['status_name'])->first();
            if(!$getStatus) {
                $saveStatus = new ProductStatus();
                $saveStatus->status_name = $status['status_name'];
                $saveStatus->status_color = $status['status_color'];
                $saveStatus->company_id = company()->id;
                $saveStatus->save();
            }
       }
       // end
       
        $products = Product::orderBy('id', 'desc')->paginate(25);
       
        $salescategories = $this->salescategories = SalescategoryType::all();
        $salescategoriesArr = [];
        $salescategoriesArr[] = 'Select Category';
        if($salescategories) {
            foreach ($salescategories as $category) {
                $salescategoriesArr[$category->salescategory_code] = $category->salescategory_name;
            }
        }
        $this->salescategoriesData = json_encode($salescategoriesArr);
        
        $codetypes = $this->codetypes = CodeType::all();
        $codetypesArr = [];
        $codetypesArr[] = 'Select Location';
        if($codetypes) {
            foreach ($codetypes as $codetype) {
                $codetypesArr[$codetype->location_code] = $codetype->location_name;
            }
        }
        
        $this->products = $products;
        
        $this->codetypesData = json_encode($codetypesArr);
        
        $this->totalProducts = Product::count();
        
        $this->totalRecords = $this->totalProducts;
        
        $this->productSettings = ProductSetting::first();
        $this->productStatuses = ProductStatus::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        $this->salescategories = SalescategoryType::all();
        $this->projects = Project::all();
        
        if($request->ajax()){
            $view = view('admin.products.filter-products-v3', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
            // return view('admin.products.filter-products-v3', $this->data); 
        }
        return view('admin.products.index', $this->data);
    }
    
    public function index_bk(ProductsDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/products'));
        
        $user = Auth::user();
        if (!$user['uuid']) {
            $user['uuid'] = Str::random(15);
            $user->save();
        }
        $uuid = $user['uuid'];
        $this->uuid = $uuid;
        
        
        
//         if (isset($_REQUEST['sb_debug']) && $_REQUEST['sb_debug'] == 1) {
//            $products = Product::all();
//            
//            if ($products) {
//                foreach ($products as $product) {
//                     $itemObj = json_decode($product->item);
//                    if(isset($itemObj->locationCode) && !empty($itemObj->locationCode)) {
//                        $locationCode = CodeType::where('location_code', $itemObj->locationCode)->first();
//                        if($locationCode) {
//                            ProductCodeType::where('product_id', $product->id)->delete();
//                            $productProject = new ProductCodeType();
//                            $productProject->product_id = $product->id;
//                            $productProject->code_type_id = $locationCode->id;
//                            $productProject->save();
//                        }
//                    }
//                }
//            }
//            echo 'Done';
//            exit;
//        }
        
        
        

        $salescategories = $this->salescategories = SalescategoryType::all();
        $salescategoriesArr = [];
        $salescategoriesArr[] = 'Select Category';
        if($salescategories) {
            foreach ($salescategories as $category) {
                $salescategoriesArr[$category->salescategory_code] = $category->salescategory_name;
            }
        }
        $this->salescategoriesData = json_encode($salescategoriesArr);
        
        $codetypes = $this->codetypes = CodeType::all();
        $codetypesArr = [];
        $codetypesArr[] = 'Select Location';
        if($codetypes) {
            foreach ($codetypes as $codetype) {
                $codetypesArr[$codetype->location_code] = $codetype->location_name;
            }
        }
        $this->codetypesData = json_encode($codetypesArr);
        
        $this->totalProducts = Product::count();
        
        $this->totalRecords = $this->totalProducts;
        
        $this->productSettings = ProductSetting::first();
        
        return $dataTable->render('admin.products.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->taxes = Tax::all();
        // return view('admin.products.create', $this->data);
        return redirect(route('admin.products.edit', 0));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $cost_per_unit = $request->cost_per_unit? str_replace(',', '', $request->cost_per_unit) : 0;
        if($cost_per_unit !=0 && !is_numeric($request->cost_per_unit)) {
            return Reply::error('Cost per unit should be numerical.');
        }
        
        $default_markup = $request->default_markup? str_replace(',', '', $request->default_markup) : 0;
        if($default_markup !=0 && !is_numeric($request->default_markup)) {
            return Reply::error('Default Markup should be numerical.');
        }
        
        $default_markup_fix = $request->default_markup_fix? str_replace(',', '', $request->default_markup_fix) : 0;
        if($default_markup_fix !=0 && !is_numeric($request->default_markup_fix)) {
            return Reply::error('Default Markup should be numerical.');
        }
        
        $msrp = $request->msrp? str_replace(',', '', $request->msrp) : 0;
        if($msrp !=0 && !is_numeric($request->msrp)) {
            return Reply::error('Total Price should be numerical.');
        }
        
        $products = new Product();
        // bitsclan code start here

        $qbo_id = '';
        

        $this->quickbook = $this->QuickbookSettings();
        if ($this->quickbook && $this->company->sync_products_to_qb == 1) {

            try {
                $adminSetting = User::where('email', ($this->user->email))->first();

                $dateTime = new \DateTime('NOW');
                $Item = Item::create([
                            "Name" => $request->name,
                            "Description" => $request->description ? $request->description : '',
                            "Active" => true,
                            "FullyQualifiedName" => $request->name,
                            "Taxable" => $request->taxable ? true : false,
                            "UnitPrice" => $msrp,
                            "Type" => "NonInventory",
                            "IncomeAccountRef" => [
                                "name" => "Sales - Company Service",
                                "value" => $adminSetting->income_account
                            ],
                            "PurchaseDesc" => $request->description ? $request->description : '',
                            "PurchaseCost" => $cost_per_unit,
                            "TrackQtyOnHand" => false,
                            "InvStartDate" => $dateTime
                ]);

                $resultingCustomerObj = $this->quickbook->Add($Item);
                $error = $this->quickbook->getLastError();
                if ($error) {
                    //return Reply::error(__('messages.itemAllreadyExist'));
                }
                $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
            } catch (\Exception $e) {
                
            }
        }
        // bitsclan code end here
        
        $products->name = $request->name;
        //$products->project_id = $request->project_id;
        $products->vendor_id = $request->vendor_id;
        if($request->vendor_description_type == 'vendor') {
            $products->vendor_description = $request->vendor_description ? $request->vendor_description : '';
        } else {
            $products->vendor_description = '';
        }
        
        $products->vendor_description = $request->vendor_description ? $request->vendor_description : '';
        //$products->description = $request->description ? $request->description : '';
        
        $products->notes = $request->notes ? $request->notes : '';
        $products->link = $request->link ? $request->link : '';
        //$products->tags = json_encode($request->tags);
        
        $products->cost_per_unit = $cost_per_unit;
        $products->msrp = $msrp;
        $products->total_sale = $msrp; // msrp is total sale price
        $products->default_markup = $default_markup;
        $products->default_markup_fix = $default_markup_fix;
        
        $products->markup_per = $default_markup;
        $products->taxable = $request->taxable?'yes':'no';
        
        $products->sku = $request->sku?$request->sku:'';
        $products->finish_color = $request->finish_color?$request->finish_color:'';
        
        
        $products->materials = $request->materials ? $request->materials : '';
        $products->product_number = $request->product_number ? $request->product_number : '';
        $products->dimensions = $request->dimensions ? $request->dimensions : '';
        $products->manufacturer = $request->manufacturer ? $request->manufacturer : '';
        
        $products->spec_number = $request->spec_number ? $request->spec_number : '';
        $products->quantity = $request->quantity ? $request->quantity : 1;
        
        $products->url = $request->url ? $request->url : '';
        $products->freight = $request->freight ? $request->freight : '';
        $products->product_color = $request->product_color ? $request->product_color : '';
        $products->lead_time = $request->lead_time ? $request->lead_time : '';
        
        $products->tags = json_encode(array());
        if($request->tags) {
            $products->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        

        // bitsclan code start here

        $products->qbo_id = $qbo_id;

        // bitsclan code end here
        
        
        // $products->price = $request->price;
        // $products->taxes = $request->tax ? json_encode($request->tax) : null;
        $products->item = json_encode(array(
            'description' => $request->description?$request->description:'',
            'locationCode' => $request->locationCode?$request->locationCode:'',
            'quantity' => $request->quantity?$request->quantity:'',
            'salesCategory' => $request->salesCategory?$request->salesCategory:'',
            'clientDeposit' => $request->clientDeposit?$request->clientDeposit:'',
            'depositRequested' => $request->depositRequested?$request->depositRequested:'',
            'unit' => $request->unit?$request->unit:'',
            'totalEstimatedCost' => $request->totalEstimatedCost?$request->totalEstimatedCost:'',
            'totalSalesPrice' => $msrp,
            'unitBudget' => $request->unitBudget?$request->unitBudget:'',
        ));
        
        
        $products->purchaseOrder = json_encode(array(
                    'type' => $request->type ? $request->type :'',
                    'vendor' => $request->vendor ? $request->vendor: '',
                    'shipTo' => $request->shipTo ? $request->shipTo: '',
                    'purchaseType' => $request->purchaseType ? $request->purchaseType :'',
                    'calcValue1' => $request->calcValue1 ?  $request->calcValue1 : '',
                    'calcValue2' => $request->calcValue2 ? $request->calcValue2 :'',
                    'calcValue3' => $request->calcValue3 ? $request->calcValue3 :'',
                    'calcValue4' => $request->calcValue4 ? $request->calcValue4 :'',
                    'calcTotal' => $request->calcTotal ? $request->calcTotal:'',
                    'addTax' => $request->addTax ? $request->addTax : '',
                    'freight' => $request->freight ? $request->freight :'', 
                    'designFee' => $request->designFee ? $request->designFee :'',
                    'additionalCharges' => $request->additionalCharges ? $request->additionalCharges :'',
                    'total1' => $request->total1 ? $request->total1 :'',
                    'total2' => $request->total2 ? $request->total2 : '',
                ));
        
        
            $products->specification = json_encode(array(
                        'specTemplateNumber' => $request->specTemplateNumber ? $request->specTemplateNumber : '',
                        'manufacturer' => $request->manufacturer ? $request->manufacturer : '',
                        'source' => $request->source ? $request->source : '',
                        'planNumber' => $request->planNumber ? $request->planNumber :'',
                        'refNumber' => $request->refNumber ? $request->refNumber :'',
                        'material' => $request->material ? $request->material : '',
                        'attrs' => $request->attrs ? $request->attrs : array(),
                        'instruction' => $request->instruction ? $request->instruction: '',
            ));
        
            $products->pricing = json_encode(array(
                'method' => $request->method ? $request->method : '',
                'pricing' => $request->pricing ? $request->pricing :'',
                'budgetAmount' => $request->budgetAmount ? $request->budgetAmount :'',
                'budgetQty' => $request->budgetQty ? $request->budgetQty : '',
            ));
//              
            $products->workroom = json_encode(array(
                'vendorInstruction' => $request->vendorInstruction ? $request->vendorInstruction:'',
                'workroomVendor' => $request->workroomVendor ? $request->workroomVendor :'',
                'shipFinishedProductTo' => $request->shipFinishedProductTo ? $request->shipFinishedProductTo :'',
                'workroomInstructions' => $request->workroomInstructions ? $request->workroomInstructions :'',
                'sidemark' => $request->sidemark ? $request->sidemark :''
            ));
        
        // $products->allow_purchase = ($request->purchase_allow == 'no')? true : false ;
        $products->save();
        
        $directory = "user-uploads/products/$products->id";
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }
        
        if($request->attachment1) {
            $file = $request->attachment1->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment1, public_path($imageFilePath));
            $products->attachment1 = $fileName;
            $products->save();
        }
        
        if($request->attachment2) {
            $file = $request->attachment2->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment2, public_path($imageFilePath));
            $products->attachment2 = $fileName;
            $products->save();
        }
        
        if($request->attachment3) {
            $file = $request->attachment3->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment3, public_path($imageFilePath));
            $products->attachment3 = $fileName;
            $products->save();
        }
        
        
//        if(!empty($products->link)) {
//            $shortLink = new ShortLink();
//            $shortLink->link = $products->link;
//            $shortLink->code = str_random(6);
//            $shortLink->product_id = $products->id;
//            $shortLink->save();
//            
//            $products->short_code = $shortLink->code;
//            $products->save();
//        }
        //$products->project_id = $request->project_id;
        
        // new logic assign multiple projects to products
        
        /// delete and create new
        ProductProject::where('product_id', $products->id)->delete();
        
        $project_ids = $request->project_id?$request->project_id:[];
        if(count($project_ids) > 0) {
            foreach ($project_ids as $project_id) {
                $productProject = new ProductProject();
                $productProject->product_id = $products->id;
                $productProject->project_id = $project_id;
                $productProject->save();
            }
        }
        
        // delete and create new
        ProductCodeType::where('product_id', $products->id)->delete();
        $code_type_ids = $request->code_type_id?$request->code_type_id:[];
        if(count($code_type_ids) > 0) {
            foreach ($code_type_ids as $code_type_id) {
                $productProject = new ProductCodeType();
                $productProject->product_id = $products->id;
                $productProject->code_type_id = $code_type_id;
                $productProject->save();
            }
        }
        
        if(count($project_ids) > 0 && !empty($request->totalEstimatedCost)) {
            foreach ($project_ids as $project_id) {
                $project = Project::find($project_id);
                if($project) {
                    $project->project_budget = $project->project_budget + $request->totalEstimatedCost;
                    $project->save();
                }
            }
        }

        $id = $products->id;
       
        return Reply::redirect(route('admin.products.edit', $id), __('messages.productAdded'));
        
        //return redirect(route('admin.products.edit', $id));
        //return Reply::redirect(route('admin.products.edit', $id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        if ($id != 0) $this->product = Product::find($id);
        else $this->product = new Product();
        // $this->taxes = Tax::all();
        if ($this->product == null) 
            return redirect(route('admin.products.create'));

        $this->projects = Project::orderBy('project_name')->get();
        $this->product->afterLoad();
        $this->id = $id;
        
        $tags = $this->product->tags ? json_decode($this->product->tags) : array();
        
        $this->product->tags = $tags;
        if($tags) {
            $this->product->tags = array_values(array_unique($tags));
        }
                
        
        $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        $this->salescategories = SalescategoryType::all();
        // for copy
        
        $this->copy = 0;
        if(isset($request->copy) && $request->copy == 1) {
            $this->copy = 1;
        }
        
        
        return view('admin.products.create', $this->data);
        // return view('admin.products.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    // old method , used productUpdate function 
    public function update(StoreProductRequest $request, $id)
    {
        $cost_per_unit = $request->cost_per_unit? str_replace(',', '', $request->cost_per_unit) : 0;
        if($cost_per_unit !=0 && !is_numeric($request->cost_per_unit)) {
            return Reply::error('Cost per unit should be numerical.');
        }
        
        $default_markup = $request->default_markup? str_replace(',', '', $request->default_markup) : 0;
        if($default_markup !=0 && !is_numeric($request->default_markup)) {
            return Reply::error('Default Markup should be numerical.');
        }
        
        $default_markup_fix = $request->default_markup_fix? str_replace(',', '', $request->default_markup_fix) : 0;
        if($default_markup_fix !=0 && !is_numeric($request->default_markup_fix)) {
            return Reply::error('Default Markup should be numerical.');
        }
        
        $msrp = $request->msrp? str_replace(',', '', $request->msrp) : 0;
        if($msrp !=0 && !is_numeric($request->msrp)) {
            return Reply::error('Total Price should be numerical.');
        }
        
        $products = Product::find($id);
        // $products->price = $request->price;
        // $products->taxes = $request->tax ? json_encode($request->tax) : null;
        // $products->description = $request->description;
        // $products->allow_purchase = ($request->purchase_allow == 'no')? true : false ;
        //        switch ($request->tabId) {
        //            case "item":

        // bitsclan code start here
        $qbo_id = '';
        

        $this->quickbook = $this->QuickbookSettings();
        if ($this->quickbook && $this->company->sync_products_to_qb == 1) {

            try {

                $adminSetting = User::where('email', ($this->user->email))->first();
                if (!empty($products->qbo_id)) {
                    $entities = $this->quickbook->Query("SELECT * FROM Item where Id='" . $products->qbo_id . "'");
                    $theItem = reset($entities);
                    $dateTime = new \DateTime('NOW');
                    $Item = Item::update($theItem, [
                                "Name" => $request->name,
                                "Description" => $request->description ? $request->description : '',
                                "Active" => true,
                                "FullyQualifiedName" => $request->name,
                                "Taxable" => $request->taxable ? true : false,
                                "UnitPrice" => $msrp,
                                "Type" => "NonInventory",
                                "IncomeAccountRef" => [
                                    "name" => "Sales - Company Service",
                                    "value" => $adminSetting->income_account
                                ],
                                "PurchaseDesc" => $request->description ? $request->description : '',
                                "PurchaseCost" => $cost_per_unit,
                    ]);

                    $resultingCustomerObj = $this->quickbook->update($Item);
                } else {

                    $dateTime = new \DateTime('NOW');
                    $Item = Item::create([
                                "Name" => $request->name,
                                "Description" => $request->description ? $request->description : '',
                                "Active" => true,
                                "FullyQualifiedName" => $request->name,
                                "Taxable" => $request->taxable ? true : false,
                                "UnitPrice" => $msrp,
                                "Type" => "NonInventory",
                                "IncomeAccountRef" => [
                                    "name" => "Sales - Company Service",
                                    "value" => $adminSetting->income_account
                                ],
                                "PurchaseDesc" => $request->description ? $request->description : '',
                                "PurchaseCost" => $cost_per_unit,
                    ]);

                    $resultingCustomerObj = $this->quickbook->Add($Item);
                }
                $error = $this->quickbook->getLastError();
                if ($error) {
                    //return Reply::error(__('messages.itemAllreadyExist'));
                }
                $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
            } catch (\Exception $e) {
                
            }
        }
        //Bitslan code end here

        $totalEstimatedCostOld = 0;
        $item = json_decode($products->item);
        if(!empty($item) && isset($item->totalEstimatedCost) && !empty($item->totalEstimatedCost)){
            $totalEstimatedCostOld = $item->totalEstimatedCost;
        }

        $products->name = $request->name;
        //$products->project_id = $request->project_id;
        $products->vendor_id = $request->vendor_id;
        
        
        if($request->vendor_description_type == 'vendor') {
            $products->vendor_description = $request->vendor_description ? $request->vendor_description : '';
        } else {
            $products->vendor_description = '';
        }
        
        $products->vendor_description = $request->vendor_description ? $request->vendor_description : '';
        //$products->description = $request->description ? $request->description : '';
        
        $products->notes = $request->notes ? $request->notes : '';
        $products->link = $request->link ? $request->link : '';
        //$products->tags = json_encode($request->tags);

        $products->cost_per_unit = $cost_per_unit;
        $products->msrp = $msrp;
        $products->total_sale = $msrp;
        $products->default_markup = $default_markup;
        $products->default_markup_fix = $default_markup_fix;
        $products->markup_per = $default_markup;
        $products->taxable = $request->taxable?'yes':'no';

        $products->sku = $request->sku?$request->sku:'';
        $products->finish_color = $request->finish_color?$request->finish_color:'';

        $products->materials = $request->materials ? $request->materials : '';
        $products->product_number = $request->product_number ? $request->product_number : '';
        $products->dimensions = $request->dimensions ? $request->dimensions : '';
        $products->manufacturer = $request->manufacturer ? $request->manufacturer : '';
        
        $products->spec_number = $request->spec_number ? $request->spec_number : '';
        $products->quantity = $request->quantity ? $request->quantity : 1;
        
        $products->url = $request->url ? $request->url : '';
        $products->freight = $request->freight ? $request->freight : '';
        $products->product_color = $request->product_color ? $request->product_color : '';
        $products->lead_time = $request->lead_time ? $request->lead_time : '';
        
        $products->tags = json_encode(array());
        if($request->tags) {
            $products->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        // bitsclan code start here

        $products->qbo_id = $qbo_id;
       
            
          
        // bitsclan code end here
                $products->item = json_encode(array(
                    'description' => $request->description ?$request->description:'',
                    'locationCode' => $request->locationCode ? $request->locationCode:'',
                    'quantity' => $request->quantity? $request->quantity:'',
                    'salesCategory' => $request->salesCategory?$request->salesCategory:'',
                    'clientDeposit' => $request->clientDeposit?$request->clientDeposit:'',
                    'depositRequested' => $request->depositRequested ? $request->depositRequested: '',
                    'unit' => $request->unit ? $request->unit: '', 
                    'totalEstimatedCost' => $request->totalEstimatedCost ? $request->totalEstimatedCost : '',
                    'totalSalesPrice' => $msrp,
                    'unitBudget' => $request->unitBudget ? $request->unitBudget: '',
                ));
                
                if(!empty($request->project_id) && !empty($request->totalEstimatedCost)) {
                    $project = Project::find($request->project_id);
                    if($project) {
                        $project->project_budget = ($project->project_budget + $request->totalEstimatedCost) - $totalEstimatedCostOld;
                        $project->save();
                    }
                }
                
                
//                break;
//            case "purchaseOrder":
                $products->purchaseOrder = json_encode(array(
                    'type' => $request->type ? $request->type :'',
                    'vendor' => $request->vendor ? $request->vendor: '',
                    'shipTo' => $request->shipTo ? $request->shipTo: '',
                    'purchaseType' => $request->purchaseType ? $request->purchaseType :'',
                    'calcValue1' => $request->calcValue1 ?  $request->calcValue1 : '',
                    'calcValue2' => $request->calcValue2 ? $request->calcValue2 :'',
                    'calcValue3' => $request->calcValue3 ? $request->calcValue3 :'',
                    'calcValue4' => $request->calcValue4 ? $request->calcValue4 :'',
                    'calcTotal' => $request->calcTotal ? $request->calcTotal:'',
                    'addTax' => $request->addTax ? $request->addTax : '',
                    'freight' => $request->freight ? $request->freight :'', 
                    'designFee' => $request->designFee ? $request->designFee :'',
                    'additionalCharges' => $request->additionalCharges ? $request->additionalCharges :'',
                    'total1' => $request->total1 ? $request->total1 :'',
                    'total2' => $request->total2 ? $request->total2 : '',
                ));
//                break;
//            case "specification":
                $products->specification = json_encode(array(
                    'specTemplateNumber' => $request->specTemplateNumber ? $request->specTemplateNumber : '',
                    'manufacturer' => $request->manufacturer ? $request->manufacturer : '',
                    'source' => $request->source ? $request->source : '',
                    'planNumber' => $request->planNumber ? $request->planNumber :'',
                    'refNumber' => $request->refNumber ? $request->refNumber :'',
                    'material' => $request->material ? $request->material : '',
                    'attrs' => $request->attrs ? $request->attrs : array(),
                    'instruction' => $request->instruction ? $request->instruction: '',
                ));
//                break;
//            case "pricing":
                $products->pricing = json_encode(array(
                    'method' => $request->method ? $request->method : '',
                    'pricing' => $request->pricing ? $request->pricing :'',
                    'budgetAmount' => $request->budgetAmount ? $request->budgetAmount :'',
                    'budgetQty' => $request->budgetQty ? $request->budgetQty : '',
                ));
//                break;
//            case "workroom":
                $products->workroom = json_encode(array(
                    'vendorInstruction' => $request->vendorInstruction ? $request->vendorInstruction:'',
                    'workroomVendor' => $request->workroomVendor ? $request->workroomVendor :'',
                    'shipFinishedProductTo' => $request->shipFinishedProductTo ? $request->shipFinishedProductTo :'',
                    'workroomInstructions' => $request->workroomInstructions ? $request->workroomInstructions :'',
                    'sidemark' => $request->sidemark ? $request->sidemark :''
                ));
                //break;
        //};
        
        $products->save();
        
        $directory = "user-uploads/products/$products->id";
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }
        
        
        if($request->attachment1) {
            $file = $request->attachment1->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment1, public_path($imageFilePath));
            $products->attachment1;
            $products->save();
        }
        
        if($request->attachment2) {
            $file = $request->attachment2->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment2, public_path($imageFilePath));
            $products->attachment2 = $fileName;
            $products->save();
        }
        
        if($request->attachment3) {
            $file = $request->attachment3->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment3, public_path($imageFilePath));
            $products->attachment3 = $fileName;
            $products->save();
        }
        
//        if(!empty($products->link)) {
////            $shortLink = ShortLink::where('product_id', $products->id)->first();
////            if(!$shortLink) {              
////            }
//            $shortLink = new ShortLink();
//            $shortLink->link = $products->link;
//            $shortLink->code = str_random(6);
//            $shortLink->product_id = $products->id;
//            $shortLink->save();
//            $products->short_code = $shortLink->code;
//            $products->save();
//        }
        
        
        
        //new logic assign multiple projects to products
        /// delete and create new
        ProductProject::where('product_id', $products->id)->delete();
        
        $project_ids = $request->project_id?$request->project_id:[];
        if(count($project_ids) > 0) {
            foreach ($project_ids as $project_id) {
                $productProject = new ProductProject();
                $productProject->product_id = $products->id;
                $productProject->project_id = $project_id;
                $productProject->save();
            }
        }
        
        // delete and create new
        ProductCodeType::where('product_id', $products->id)->delete();
        $code_type_ids = $request->code_type_id?$request->code_type_id:[];
        if(count($code_type_ids) > 0) {
            foreach ($code_type_ids as $code_type_id) {
                $productProject = new ProductCodeType();
                $productProject->product_id = $products->id;
                $productProject->code_type_id = $code_type_id;
                $productProject->save();
            }
        }

        $id = $products->id;
        return Reply::success(__('messages.productUpdated'));
        // return Reply::redirect(route('admin.products.edit', $id), __('messages.productUpdated'));
    }
    
    // used this function instead of above
    public function productUpdate(StoreProductRequest $request, $id)
    {
        $cost_per_unit = $request->cost_per_unit? str_replace(',', '', $request->cost_per_unit) : 0;
        if($cost_per_unit !=0 && !is_numeric($request->cost_per_unit)) {
            return Reply::error('Cost per unit should be numerical.');
        }
        
        $default_markup = $request->default_markup? str_replace(',', '', $request->default_markup) : 0;
        if($default_markup !=0 && !is_numeric($request->default_markup)) {
            return Reply::error('Default Markup should be numerical.');
        }
        
        $default_markup_fix = $request->default_markup_fix? str_replace(',', '', $request->default_markup_fix) : 0;
        if($default_markup_fix !=0 && !is_numeric($request->default_markup_fix)) {
            return Reply::error('Default Markup should be numerical.');
        }
        
        $msrp = $request->msrp? str_replace(',', '', $request->msrp) : 0;
        if($msrp !=0 && !is_numeric($request->msrp)) {
            return Reply::error('Total Price should be numerical.');
        }
        
        $products = Product::find($id);
        // $products->price = $request->price;
        // $products->taxes = $request->tax ? json_encode($request->tax) : null;
        // $products->description = $request->description;
        // $products->allow_purchase = ($request->purchase_allow == 'no')? true : false ;
        //        switch ($request->tabId) {
        //            case "item":

        // bitsclan code start here
        $qbo_id = '';
        

        $this->quickbook = $this->QuickbookSettings();
        if ($this->quickbook && $this->company->sync_products_to_qb == 1) {

            try {

                $adminSetting = User::where('email', ($this->user->email))->first();
                if (!empty($products->qbo_id)) {
                    $entities = $this->quickbook->Query("SELECT * FROM Item where Id='" . $products->qbo_id . "'");
                    $theItem = reset($entities);
                    $dateTime = new \DateTime('NOW');
                    $Item = Item::update($theItem, [
                                "Name" => $request->name,
                                "Description" => $request->description ? $request->description : '',
                                "Active" => true,
                                "FullyQualifiedName" => $request->name,
                                "Taxable" => $request->taxable ? true : false,
                                "UnitPrice" => $msrp,
                                "Type" => "NonInventory",
                                "IncomeAccountRef" => [
                                    "name" => "Sales - Company Service",
                                    "value" => $adminSetting->income_account
                                ],
                                "PurchaseDesc" => $request->description ? $request->description : '',
                                "PurchaseCost" => $cost_per_unit,
                    ]);

                    $resultingCustomerObj = $this->quickbook->update($Item);
                } else {

                    $dateTime = new \DateTime('NOW');
                    $Item = Item::create([
                                "Name" => $request->name,
                                "Description" => $request->description ? $request->description : '',
                                "Active" => true,
                                "FullyQualifiedName" => $request->name,
                                "Taxable" => $request->taxable ? true : false,
                                "UnitPrice" => $msrp,
                                "Type" => "NonInventory",
                                "IncomeAccountRef" => [
                                    "name" => "Sales - Company Service",
                                    "value" => $adminSetting->income_account
                                ],
                                "PurchaseDesc" => $request->description ? $request->description : '',
                                "PurchaseCost" => $cost_per_unit,
                    ]);

                    $resultingCustomerObj = $this->quickbook->Add($Item);
                }
                $error = $this->quickbook->getLastError();
                if ($error) {
                    //return Reply::error(__('messages.itemAllreadyExist'));
                }
                $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
            } catch (\Exception $e) {
                
            }
        }
        //Bitslan code end here

        $totalEstimatedCostOld = 0;
        $item = json_decode($products->item);
        if(!empty($item) && isset($item->totalEstimatedCost) && !empty($item->totalEstimatedCost)){
            $totalEstimatedCostOld = $item->totalEstimatedCost;
        }

        $products->name = $request->name;
        //$products->project_id = $request->project_id;
        $products->vendor_id = $request->vendor_id;
        
        
        if($request->vendor_description_type == 'vendor') {
            $products->vendor_description = $request->vendor_description ? $request->vendor_description : '';
        } else {
            $products->vendor_description = '';
        }
        
        $products->vendor_description = $request->vendor_description ? $request->vendor_description : '';
        //$products->description = $request->description ? $request->description : '';
        
        $products->notes = $request->notes ? $request->notes : '';
        $products->link = $request->link ? $request->link : '';
        //$products->tags = json_encode($request->tags);

        $products->cost_per_unit = $cost_per_unit;
        $products->msrp = $msrp;
        $products->total_sale = $msrp;
        $products->default_markup = $default_markup;
        $products->default_markup_fix = $default_markup_fix;
        $products->markup_per = $default_markup;
        $products->taxable = $request->taxable?'yes':'no';

        $products->sku = $request->sku?$request->sku:'';
        $products->finish_color = $request->finish_color?$request->finish_color:'';

        $products->materials = $request->materials ? $request->materials : '';
        $products->product_number = $request->product_number ? $request->product_number : '';
        $products->dimensions = $request->dimensions ? $request->dimensions : '';
        $products->manufacturer = $request->manufacturer ? $request->manufacturer : '';
        
        $products->spec_number = $request->spec_number ? $request->spec_number : '';
        $products->quantity = $request->quantity ? $request->quantity : 1;
        
        $products->url = $request->url ? $request->url : '';
        $products->freight = $request->freight ? $request->freight : '';
        $products->product_color = $request->product_color ? $request->product_color : '';
        $products->lead_time = $request->lead_time ? $request->lead_time : '';
        
        $products->tags = json_encode(array());
        if($request->tags) {
            $products->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        // bitsclan code start here

        $products->qbo_id = $qbo_id;
       
            
          
        // bitsclan code end here
                $products->item = json_encode(array(
                    'description' => $request->description ?$request->description:'',
                    'locationCode' => $request->locationCode ? $request->locationCode:'',
                    'quantity' => $request->quantity? $request->quantity:'',
                    'salesCategory' => $request->salesCategory?$request->salesCategory:'',
                    'clientDeposit' => $request->clientDeposit?$request->clientDeposit:'',
                    'depositRequested' => $request->depositRequested ? $request->depositRequested: '',
                    'unit' => $request->unit ? $request->unit: '', 
                    'totalEstimatedCost' => $request->totalEstimatedCost ? $request->totalEstimatedCost : '',
                    'totalSalesPrice' => $msrp,
                    'unitBudget' => $request->unitBudget ? $request->unitBudget: '',
                ));
                
                if(!empty($request->project_id) && !empty($request->totalEstimatedCost)) {
                    $project = Project::find($request->project_id);
                    if($project) {
                        $project->project_budget = ($project->project_budget + $request->totalEstimatedCost) - $totalEstimatedCostOld;
                        $project->save();
                    }
                }
                
                
//                break;
//            case "purchaseOrder":
                $products->purchaseOrder = json_encode(array(
                    'type' => $request->type ? $request->type :'',
                    'vendor' => $request->vendor ? $request->vendor: '',
                    'shipTo' => $request->shipTo ? $request->shipTo: '',
                    'purchaseType' => $request->purchaseType ? $request->purchaseType :'',
                    'calcValue1' => $request->calcValue1 ?  $request->calcValue1 : '',
                    'calcValue2' => $request->calcValue2 ? $request->calcValue2 :'',
                    'calcValue3' => $request->calcValue3 ? $request->calcValue3 :'',
                    'calcValue4' => $request->calcValue4 ? $request->calcValue4 :'',
                    'calcTotal' => $request->calcTotal ? $request->calcTotal:'',
                    'addTax' => $request->addTax ? $request->addTax : '',
                    'freight' => $request->freight ? $request->freight :'', 
                    'designFee' => $request->designFee ? $request->designFee :'',
                    'additionalCharges' => $request->additionalCharges ? $request->additionalCharges :'',
                    'total1' => $request->total1 ? $request->total1 :'',
                    'total2' => $request->total2 ? $request->total2 : '',
                ));
//                break;
//            case "specification":
                $products->specification = json_encode(array(
                    'specTemplateNumber' => $request->specTemplateNumber ? $request->specTemplateNumber : '',
                    'manufacturer' => $request->manufacturer ? $request->manufacturer : '',
                    'source' => $request->source ? $request->source : '',
                    'planNumber' => $request->planNumber ? $request->planNumber :'',
                    'refNumber' => $request->refNumber ? $request->refNumber :'',
                    'material' => $request->material ? $request->material : '',
                    'attrs' => $request->attrs ? $request->attrs : array(),
                    'instruction' => $request->instruction ? $request->instruction: '',
                ));
//                break;
//            case "pricing":
                $products->pricing = json_encode(array(
                    'method' => $request->method ? $request->method : '',
                    'pricing' => $request->pricing ? $request->pricing :'',
                    'budgetAmount' => $request->budgetAmount ? $request->budgetAmount :'',
                    'budgetQty' => $request->budgetQty ? $request->budgetQty : '',
                ));
//                break;
//            case "workroom":
                $products->workroom = json_encode(array(
                    'vendorInstruction' => $request->vendorInstruction ? $request->vendorInstruction:'',
                    'workroomVendor' => $request->workroomVendor ? $request->workroomVendor :'',
                    'shipFinishedProductTo' => $request->shipFinishedProductTo ? $request->shipFinishedProductTo :'',
                    'workroomInstructions' => $request->workroomInstructions ? $request->workroomInstructions :'',
                    'sidemark' => $request->sidemark ? $request->sidemark :''
                ));
                //break;
        //};
        
        $products->save();
        
        $directory = "user-uploads/products/$products->id";
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }
        
       
        
        if($request->attachment1) {
            $file = $request->attachment1->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment1, public_path($imageFilePath));
            $products->attachment1 = $fileName;
            $products->save();
        }
        
        if($request->attachment2) {
            $file = $request->attachment2->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment2, public_path($imageFilePath));
            $products->attachment2 = $fileName;
            $products->save();
        }
        
        if($request->attachment3) {
            $file = $request->attachment3->getClientOriginalName();
            //$orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time().mt_rand().".".$extension;
            $imageFilePath = "$directory/$fileName";
            File::move($request->attachment3, public_path($imageFilePath));
            $products->attachment3 = $fileName;
            $products->save();
        }
        
//        if(!empty($products->link)) {
////            $shortLink = ShortLink::where('product_id', $products->id)->first();
////            if(!$shortLink) {              
////            }
//            $shortLink = new ShortLink();
//            $shortLink->link = $products->link;
//            $shortLink->code = str_random(6);
//            $shortLink->product_id = $products->id;
//            $shortLink->save();
//            $products->short_code = $shortLink->code;
//            $products->save();
//        }
        
        
        
        //new logic assign multiple projects to products
        /// delete and create new
        ProductProject::where('product_id', $products->id)->delete();
        
        $project_ids = $request->project_id?$request->project_id:[];
        if(count($project_ids) > 0) {
            foreach ($project_ids as $project_id) {
                $productProject = new ProductProject();
                $productProject->product_id = $products->id;
                $productProject->project_id = $project_id;
                $productProject->save();
            }
        }
        
        // delete and create new
        ProductCodeType::where('product_id', $products->id)->delete();
        $code_type_ids = $request->code_type_id?$request->code_type_id:[];
        if(count($code_type_ids) > 0) {
            foreach ($code_type_ids as $code_type_id) {
                $productProject = new ProductCodeType();
                $productProject->product_id = $products->id;
                $productProject->code_type_id = $code_type_id;
                $productProject->save();
            }
        }

        $id = $products->id;
        //return Reply::success(__('messages.productUpdated'));
        return Reply::redirect(route('admin.products.edit', $id), __('messages.productUpdated'));
        
        // return Reply::redirect(route('admin.products.edit', $id), __('messages.productUpdated'));
    }
    
    public function liveUpdate(Request $request) {
        if ($request->ajax()) {
            $total_sale = 0;
            $product_id = 0;
            if (!is_null($request->id) && $request->action == 'edit') {
                $product = Product::find($request->id);
                $product_id = $product->id;
                if ($product) {
                    if (!is_null($request->name)) {
                        $product->name = $request->name;
                    }
                    if ($request->has('manufacturer')) {
                        $product->manufacturer = $request->manufacturer;
                    }
                    if($request->has('notes')){
                        $product->notes = $request->notes;
                    }
                    if($request->has('expediting')){
                        $product->expediting = $request->expediting;
                    }
                    if($request->has('warehouse')){
                        $product->warehouse = $request->warehouse;
                    }
                    
                    if ($request->has('dimensions')) {
                        $product->dimensions = $request->dimensions;
                    }
                    if ($request->has('materials')) {
                        $product->materials = $request->materials;
                    }
                    if ($request->has('cost_per_unit')) {
                        $cost_per_unit = 0;
                        if(is_numeric(str_replace(",","",$request->cost_per_unit))){
                            $cost_per_unit = str_replace(",","",$request->cost_per_unit);
                        }
                        $product->cost_per_unit = $cost_per_unit;
                    }
                    if ($request->has('msrp')) {
                        $msrp = '';
                        if(is_numeric(str_replace(",","",$request->msrp))){
                            $msrp = str_replace(",","",$request->msrp);
                        }
                        $product->msrp = $msrp;
                    }
                   
                    if ($request->has('url')) {
                        $product->url = $request->url;
                        if(empty($product->url)) {
                            ShortLink::where('product_id', $product->id)->delete();
                        }
                    }
                    
                    
                    if(isset($product->url) && !empty($product->url)) {
                        //$shortLink = ShortLink::where('product_id', $products->id)->first();
                        //if(!$shortLink) {
                        $shortLink = new ShortLink();
                        //}
                        $shortLink->link = $product->url;
                        $shortLink->code = str_random(6);
                        $shortLink->product_id = $product->id;
                        $shortLink->save();
                        $product->short_code = $shortLink->code;
                    }
                    
                    
//                    $product->markup_fix = isset($request->markup_fix)? $request->markup_fix: $product->markup_fix;
//                    $product->markup_per = isset($request->markup_per)? $request->markup_per: $product->markup_per;
                    // if fix set then percentage null vise versa
                    if($request->has('markup_fix')){
                        $markup_fix = '';
                        if(is_numeric(str_replace(",","",$request->markup_fix))){
                            $markup_fix =  str_replace(",","",$request->markup_fix);
                        }
                        
                        $product->markup_fix = $markup_fix;
                        $product->default_markup = null;
                        $product->markup_per = null;
                        
                    }
                    if($request->has('markup_per')){
                        $markup_per = '';
                        
                        if(is_numeric(str_replace(",","",$request->markup_per))){
                            $markup_per =  str_replace(",","",$request->markup_per);
                        }
                        $product->markup_per = $markup_per;
                        $product->default_markup =   $markup_per;
                        $product->markup_fix = null;
                    }
                    
                    // if fix set then percentage null vise versa
                    
                    if($request->has('sales_tax_fix')){
                        $sales_tax_fix = '';
                        if(is_numeric(str_replace(",","",$request->sales_tax_fix))){
                            $sales_tax_fix =  str_replace(",","",$request->sales_tax_fix);
                        }
                        
                        $product->sales_tax_fix = $sales_tax_fix;
                        $product->sales_tax_per = null;
                    }
                    if($request->has('sales_tax_per')){
                        $sales_tax_per = '';
                        
                        if(is_numeric(str_replace(",","",$request->sales_tax_per))){
                            $sales_tax_fix =  str_replace(",","",$request->sales_tax_per);
                        }
                        
                        $product->sales_tax_per = $sales_tax_per;
                        $product->sales_tax_fix = null;
                    }
                    
                    if($request->has('freight')){
                        $freight = '';
                        if(is_numeric(str_replace(",","",$request->freight))){
                            $freight =  str_replace(",","",$request->freight);
                        }
                        
                        $product->freight = $freight;
                    }
                    if($request->has('acknowledgement')){
                        $product->acknowledgement = isset($request->acknowledgement)? $request->acknowledgement: '';
                    }
                    if($request->has('received_by')){
                        $product->received_by = isset($request->received_by)? $request->received_by: '';
                    }
//                    if($request->has('lead_time')){
//                        $product->lead_time = isset($request->lead_time)? $request->lead_time: '';
//                    }
                    
                    if($request->has('lead_time')){
                         $product->lead_time = $request->lead_time ? $request->lead_time : '';
                    }
                    
                    if($request->has('est_ship_date')){
                        $product->est_ship_date = !empty($request->est_ship_date) ? Carbon::createFromFormat($this->global->date_format, $request->est_ship_date)->format('Y-m-d') : '';
                    }
                    if($request->has('act_ship_date')){
                        $product->act_ship_date = !empty($request->act_ship_date)? Carbon::createFromFormat($this->global->date_format, $request->act_ship_date)->format('Y-m-d') : '';
                    }
                    if($request->has('est_receive_date')){
                        $product->est_receive_date = !empty($request->est_receive_date)? Carbon::createFromFormat($this->global->date_format, $request->est_receive_date)->format('Y-m-d') : '';
                    }
                    if($request->has('act_receive_date')){
                        $product->act_receive_date = !empty($request->act_receive_date)? Carbon::createFromFormat($this->global->date_format, $request->act_receive_date)->format('Y-m-d') : '';
                    }
                    if($request->has('est_install_date')){
                        $product->est_install_date = !empty($request->est_install_date)? Carbon::createFromFormat($this->global->date_format, $request->est_install_date)->format('Y-m-d') : '';
                    }
                    if($request->has('act_Install_date')){
                         $product->act_Install_date = !empty($request->act_Install_date)? Carbon::createFromFormat($this->global->date_format, $request->act_Install_date)->format('Y-m-d') : '';
                    }
                    
                    if($request->has('po_sent_date')){
                         $product->po_sent_date = !empty($request->po_sent_date)? Carbon::createFromFormat($this->global->date_format, $request->po_sent_date)->format('Y-m-d') : '';
                    }
                    if($request->has('cfa_approved_date')){
                         $product->cfa_approved_date = !empty($request->cfa_approved_date)? Carbon::createFromFormat($this->global->date_format, $request->cfa_approved_date)->format('Y-m-d') : '';
                    }
                    if($request->has('rfq_sent_date')){
                         $product->rfq_sent_date = !empty($request->rfq_sent_date)? Carbon::createFromFormat($this->global->date_format, $request->rfq_sent_date)->format('Y-m-d') : '';
                    }
                    if($request->has('quote_received')){
                         $product->quote_received = !empty($request->quote_received)? Carbon::createFromFormat($this->global->date_format, $request->quote_received)->format('Y-m-d') : '';
                    }
                    if($request->has('received_date')){
                         $product->received_date = !empty($request->received_date)? Carbon::createFromFormat($this->global->date_format, $request->received_date)->format('Y-m-d') : '';
                    }
                    if($request->has('ordered_date')){
                         $product->ordered_date = !empty($request->ordered_date)? Carbon::createFromFormat($this->global->date_format, $request->ordered_date)->format('Y-m-d') : '';
                    }
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    if ($request->has('spec_number')) {
                        $product->spec_number = $request->spec_number;
                    }
                    if ($request->has('quantity')) {
                        if(is_numeric($request->quantity)){
                            $product->quantity = $request->quantity;
                        }
                    }
                    
                    if ($request->has('product_status_id')) {
                        $product->product_status_id = $request->product_status_id;
                    }
                    if ($request->has('vendor_id')) {
                        $product->vendor_id = $request->vendor_id;
                    }
                    if ($request->has('product_number')) {
                        $product->product_number = $request->product_number;
                    }
                    if ($request->has('finish_color')) {
                        $product->finish_color = $request->finish_color;
                    }
                    if ($request->has('tracking_number')) {
                        $product->tracking_number = $request->tracking_number;
                    }
                    
                    
                   
                    $result_arr = $this->calculateSale($product,$request);
                    
                    $total_sale = $result_arr['total_sale'];
                    $default_markup_fix = $result_arr['default_markup_fix'];
                    
                    $product->total_sale = $total_sale;
                    $product->default_markup_fix = $default_markup_fix;
                    
                    $item = json_decode($product->item);
                     
                    $description = isset($item->description) ?$item->description:'';
                    $locationCode = isset($item->locationCode) ?$item->locationCode:'';
                    $quantity = isset($item->quantity) ?$item->quantity:'';
                    $salesCategory = isset($item->salesCategory) ?$item->salesCategory:'';
                    $clientDeposit = isset($item->clientDeposit) ?$item->clientDeposit:'';
                    $depositRequested = isset($item->depositRequested) ?$item->depositRequested:'';
                    $unit = isset($item->unit) ?$item->unit:'';
                    $totalEstimatedCost = isset($item->totalEstimatedCost) ?$item->totalEstimatedCost:'';
                    $totalSalesPrice = isset($item->totalSalesPrice) ?$item->totalSalesPrice:'';
                    $unitBudget = isset($item->unitBudget) ?$item->unitBudget:'';
                    
                    if($request->has('salesCategory')){
                        $salesCategory = $request->salesCategory;
                    }
                    if($request->has('locationCode')){
                        $locationCode = $request->locationCode;
                    }
                    
                    $product->item = json_encode(array(
                        'description' => $description,
                        'locationCode' => $locationCode,
                        'quantity' => $quantity,
                        'salesCategory' => $salesCategory,
                        'clientDeposit' => $clientDeposit,
                        'depositRequested' => $depositRequested,
                        'unit' => $unit, 
                        'totalEstimatedCost' => $totalEstimatedCost,
                        'totalSalesPrice' => $total_sale,
                        'unitBudget' => $unitBudget
                    ));
                   
                    $product->save();
                    
                }
            }
            
            $total_sale = $total_sale ? number_format($total_sale, 2) : $total_sale;
            $default_markup_fix = $default_markup_fix ?  number_format($default_markup_fix, 2) : $default_markup_fix;

            $markup_fix = $product->markup_fix? number_format($product->markup_fix, 2) : $product->markup_fix;
            $markup_per = $product->markup_per ? number_format($product->markup_per, 2) : $product->markup_per;
            $sales_tax_fix = $product->sales_tax_fix ? number_format($product->sales_tax_fix, 2) : $product->sales_tax_fix;
            $sales_tax_per = $product->sales_tax_per ? number_format($product->sales_tax_per, 2) : $product->sales_tax_per;
            $freight = $product->freight ? number_format($product->freight, 2) : $product->freight;
            $cost_per_unit = $product->cost_per_unit? number_format($product->cost_per_unit, 2) : $product->cost_per_unit;
            $msrp = $product->msrp? number_format($product->msrp, 2) : $product->msrp;
            
            
            return response()->json(['status' => 'success','success' => true, 'total_sale' => $total_sale, 'default_markup_fix' => $default_markup_fix , 'markup_fix' => $markup_fix , 'markup_per' => $markup_per , 'sales_tax_fix' => $sales_tax_fix , 'sales_tax_per' => $sales_tax_per, 'freight' => $freight ,'cost_per_unit' => $cost_per_unit, 'msrp' => $msrp, 'product_id' => $product_id]);
        }
        

    }
    
    private function calculateSale($product, $request){
         //(Unit cost + Markup $/% = Sale), plus the shipping cost ($/%), plus, sales tax ($/%). 
        
                $cost_per_unit = $product->cost_per_unit;
                $freight = $product->freight;
                $markup_per = $product->markup_per;
                $markup_fix = $product->markup_fix;
                $sales_tax_fix = $product->sales_tax_fix;
                $sales_tax_per = $product->sales_tax_per;
                $quantity = $product->quantity;
                $default_markup_fix = null;
                
                
                
                if(!is_null($request->cost_per_unit)) {
                    $cost_per_unit = str_replace(",","",$request->cost_per_unit);
                }
                if(!is_null($request->freight)) {
                    $freight = str_replace(",","",$request->freight);
                }
                 if(!is_null($request->markup_per)) {
                    $markup_per = str_replace(",","",$request->markup_per);
                }
                if(!is_null($request->markup_fix)) {
                    $markup_fix = str_replace(",","",$request->markup_fix);
                }
                
                if(!is_null($request->sales_tax_fix)) {
                    $sales_tax_fix = str_replace(",","",$request->sales_tax_fix);
                }
                
                if(!is_null($request->sales_tax_per)) {
                    $sales_tax_per = str_replace(",","",$request->sales_tax_per);
                }
                    
                $total_sale =  0;
                if (is_numeric($cost_per_unit)){
                    if (is_numeric($quantity)){
                        $cost_per_unit =  $quantity * $cost_per_unit;
                    }
                    $total_sale =  $cost_per_unit;
                }
                
                
                if (is_numeric($cost_per_unit) && is_numeric($markup_fix) && $markup_fix > 0) {
                    $total_sale = $cost_per_unit + $markup_fix;
                }

                if (is_numeric($cost_per_unit) && is_numeric($markup_per) && $markup_per > 0) {
                    $total_sale = $cost_per_unit + (($markup_per/100)*$cost_per_unit);
                    $default_markup_fix = (($markup_per/100)*$cost_per_unit);
                }
                
                
                if(is_numeric($freight) && $freight  > 0) {
                    $total_sale = $total_sale + $freight;
                }
                
                if (is_numeric($sales_tax_fix) && $sales_tax_fix > 0) {
                    $total_sale = $total_sale + $sales_tax_fix;
                }

                if (is_numeric($cost_per_unit) && is_numeric($sales_tax_per) && $sales_tax_per > 0) {
                    $total_sale = $total_sale + (($sales_tax_per/100)*$cost_per_unit);
                }
                
                return array('total_sale' => round($total_sale, 2), 'default_markup_fix' => round($default_markup_fix, 2));
                     
    }

    public function uploadImage(StoreProductImageRequest $request, $id) {
        $directory = "user-uploads/products/$id";
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }
        
        $file = $request->image->getClientOriginalName();
        $orgFileName = pathinfo($file, PATHINFO_FILENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        //$orgFileName = str_replace(' ', '',  strtolower($orgFileName));
        
        $fileName = time().mt_rand().".".$extension;
        $imageFilePath = "$directory/$fileName";
        
        File::move($request->image, public_path($imageFilePath));

        $products = Product::find($id);
        $pictureArr = array();
        if ($products->picture != null)
            $pictureArr = json_decode($products->picture);
        array_push($pictureArr, $fileName);

        $products->picture = json_encode($pictureArr);
        $products->save();

        return json_encode($pictureArr);
    }

    public function removeImage(StoreProductImageRequest $request, $id) {
        $fileName = $request->fileName;
        $products = Product::find($id);
        $pictureArr = json_decode($products->picture);
        if ($pictureArr == null) return;

        if (($key = array_search($fileName, $pictureArr)) !== false) {
            array_splice($pictureArr, $key, 1);
            $filePath = public_path("user-uploads/products/$id/$fileName");
            if (File::exists($filePath)) File::delete($filePath);
        }
        $products->picture = json_encode($pictureArr);
        $products->save();

        return json_encode($pictureArr);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::destroy($id);
        return Reply::success(__('messages.productDeleted'));
    }
    
    public function updateOrder(Request $request) {
        
        $query = Product::where('products.id', '!=', 0)->select('products.*');
        if (!is_null($request->project_id) && $request->project_id != 0) {
            $query->join('product_projects', 'product_projects.product_id', '=', 'products.id');
            $query->where('product_projects.project_id', $request->project_id);
            $products = $query->get();     
        } else {
            $products = $query->get();
        }
        foreach ($products as $product) {
            $id = $product->id;
            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $product->order_page = $order['position'];
                    $product->save();
                }
            }
        }
        return response()->json(['success' => true]);
    }

    public function export() {
        $attributes =  ['tax', 'taxes', 'price'];
        $products = Product::select('id', 'name', 'price')
            ->get()->makeHidden($attributes);

            // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Name', 'Price'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($products as $row) {
            $rowArrayData = $row->toArray();
            $rowArrayData['total_amount'] = $this->global->currency->currency_symbol.$rowArrayData['total_amount'];
            $exportArray[] = $rowArrayData;
        }

        // Generate and return the spreadsheet
        Excel::create('Product', function($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Product');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('Product file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));

                });

            });



        })->download('xlsx');
    }
    
    public function download($id)
    {
        //$this->product = Product::findOrFail($id);
        $this->product = Product::find($id);
        $this->projects = Project::projectNames();
        $this->product->afterLoad();
        $this->id = $id;
        $this->image = asset('img/default-product.png');
        $this->image_two = '';
        $this->image_three = '';
        $this->locationCode = '';
        $this->salesCategory = '';
        $this->projectName = '';
        $this->manufacturer = '';
        $this->workroomVendor = '';
        $this->unitName = '';
        $this->unitVal = '';
        $this->company = company();
        $this->currency_symbol = $this->global->currency->currency_symbol;
        $this->invoiceSetting = InvoiceSetting::first();
        
        
        
        
        
        $attrs = array();
        
        
        
        
        
        if(!empty($this->product->picture)) {
            $pictures = json_decode($this->product->picture);
            if($pictures) {
                $product_id = $id;
                
                if(isset($pictures[0])) {
                     $this->image =  asset('user-uploads/products/'.$id.'/'.$pictures[0].'');
                     
                     $extension = pathinfo($this->image, PATHINFO_EXTENSION);
                     // webp images convert into jpeg
                    if($extension == 'webp') {
                            $directory = "user-uploads/products/$product_id";
                            $org_imageFilePath = "$directory/".$pictures[0];
                            $imageFilePath = "$directory/".str_replace('.webp', '.jpeg', $pictures[0]);
                            $im = imagecreatefromwebp($org_imageFilePath);
                            // Convert it to a jpeg file with 100% quality
                            imagejpeg($im, $imageFilePath, 100);
                            $this->image = str_replace('.webp', '.jpeg', $this->image); 
                    }
                     
                     
                }
                if(isset($pictures[1])) {
                     $this->image_two =  asset('user-uploads/products/'.$id.'/'.$pictures[1].'');
                     
                     $extension = pathinfo($this->image_two, PATHINFO_EXTENSION);
                     // webp images convert into jpeg
                    if($extension == 'webp') {
                            $directory = "user-uploads/products/$product_id";
                            $org_imageFilePath = "$directory/".$pictures[1];
                            $imageFilePath = "$directory/".str_replace('.webp', '.jpeg', $pictures[1]);
                            $im = imagecreatefromwebp($org_imageFilePath);
                            // Convert it to a jpeg file with 100% quality
                            imagejpeg($im, $imageFilePath, 100);
                            $this->image_two = str_replace('.webp', '.jpeg', $this->image_two); 
                    }
                     
                }
                if(isset($pictures[2])) {
                     $this->image_three =  asset('user-uploads/products/'.$id.'/'.$pictures[2].'');
                     
                        $extension = pathinfo($this->image_three, PATHINFO_EXTENSION);
                         // webp images convert into jpeg
                        if($extension == 'webp') {
                                $directory = "user-uploads/products/$product_id";
                                $org_imageFilePath = "$directory/".$pictures[2];
                                $imageFilePath = "$directory/".str_replace('.webp', '.jpeg', $pictures[2]);
                                $im = imagecreatefromwebp($org_imageFilePath);
                                // Convert it to a jpeg file with 100% quality
                                imagejpeg($im, $imageFilePath, 100);
                                $this->image_three = str_replace('.webp', '.jpeg', $this->image_three); 
                        }
                     
                }
                
            }
        }
        // old method for location 
//        if(isset($this->product->itemObj->locationCode) && !empty($this->product->itemObj->locationCode)) {
//            $locationCode = CodeType::where('location_code', $this->product->itemObj->locationCode)->first();
//            if($locationCode) {
//                $this->locationCode =  $locationCode->location_name;
//            }
//        }
        
        // new method for location
        if($this->product->codes) {
            foreach ($this->product->codes as  $code) {
                if($code->code) {
                    if(empty($this->locationCode)) {
                        $this->locationCode .= ucfirst($code->code->location_name);
                    } else {
                        $this->locationCode .=','.ucfirst($code->code->location_name);
                    }
                }
            }
        }
        
        if(isset($this->product->itemObj->salesCategory) && !empty($this->product->itemObj->salesCategory)) {
            $salesCategory = SalescategoryType::where('salescategory_code', $this->product->itemObj->salesCategory)->first();
            if($salesCategory) {
                $this->salesCategory = $salesCategory->salescategory_name;
            }
        }
        
       
            foreach (config('products.units') as $unit) {
                
                if(isset($this->product->itemObj->unit->{$unit}) && !empty($this->product->itemObj->unit->{$unit})) {
                    $this->unitName = $unit;
                    $this->unitVal = $this->product->itemObj->unit->{$unit};
                    break;
                }
            }
        
        if(!empty($this->product->project_id)) {
            $project = Project::where('id', $this->product->project_id)->first();
            if($project) {
                $this->projectName = $project->project_name;
            }
        }
        if(isset($this->product->specificationObj->manufacturer) && !empty($this->product->specificationObj->manufacturer)) {
            $this->manufacturer = $this->product->specificationObj->manufacturer;
        }
        
        if(isset($this->product->specificationObj->attrs)) {
            foreach ($this->product->specificationObj->attrs as $item) {
                if(isset($item->title) && !empty($item->title)) {
                    $attrs[$item->title] = $item->description;
                }
            }
        }
        $this->attrs = $attrs;
        
        
        if(isset($this->product->workroomObj->workroomVendor) && !empty($this->product->workroomObj->workroomVendor)) {
            $this->workroomVendor = $this->product->workroomObj->workroomVendor;
        }
        
        
        
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.products.product-pdf', $this->data);
        $filename = 'product-' . $this->product->id;
        
//        if($id == 730) {
//            return view('admin.products.product-pdf', $this->data);
//        }
        
       

        return $pdf->download($filename . '.pdf');
    }
    
    
     public function downloadAll(Request $request)
    { 
        
        $this->company = company();
        $this->invoiceSetting = InvoiceSetting::first();
        $query = Product::where('products.id', '!=', 0)->select('products.*');
        
        //$query = DB::table('products')->select('products.*');
        
         if (isset($request->project_id) && !is_null($request->project_id)) {
             $query->join('product_projects', 'product_projects.product_id', '=', 'products.id');
             $query->where('product_projects.project_id', $request->project_id);
        }
        
        // old method
//        if ($request->locationCode != 'all' && !is_null($request->locationCode)) {
//            $query->where('item->locationCode', '=', $request->locationCode);
//        }
//        
        // new method
         if ($request->locationCode != 'all' && !is_null($request->locationCode)) {
             $query->join('product_code_types', 'product_code_types.product_id', '=', 'products.id');
             $query->where('product_code_types.code_type_id', $request->locationCode);
        }
        
        if ($request->salesCategory != 'all' && !is_null($request->salesCategory)) {
           $query->where('item->salesCategory', '=', $request->salesCategory);
        }
        
        $query->orderBy('order_page', 'asc');
        
        $products = $query->get();
        
        
        $productsArr = [];
        
        $cnt = 1;
        
        $grand_cost_per_unit = 0;
        $grand_total_sale = 0;
        
        if($products) {
            foreach  ($products as $product) {
                
                $product->afterLoad();
                $image = asset('img/default-product.png');
                if(!empty($product->picture)) {
                    $pictures = json_decode($product->picture);
                    if($pictures) {
                        $product_id = $product->id;
                        if(isset($product->product_id)) {
                            $product_id = $product->product_id;
                        }
                        
                        $image =  asset('user-uploads/products/'.$product_id.'/'.$pictures[0].'');
                        $extension = pathinfo($image, PATHINFO_EXTENSION);
                        // webp images convert into jpeg
                        if($extension == 'webp') {
                                $directory = "user-uploads/products/$product_id";
                                $org_imageFilePath = "$directory/".$pictures[0];
                                $imageFilePath = "$directory/".str_replace('.webp', '.jpeg', $pictures[0]);
                                $im = imagecreatefromwebp($org_imageFilePath);
                                // Convert it to a jpeg file with 100% quality
                                imagejpeg($im, $imageFilePath, 100);
                                $image = str_replace('.webp', '.jpeg', $image); 
                        }
                        
                    }
                }
                $project_name = '';
                foreach ($product->projects as $project) {
                       if($project_name == ''){
                           if(isset($project->project) && isset($project->project->project_name)) {
                               $project_name .= ucfirst($project->project->project_name);
                           }
                           
                       }else {
                           if(isset($project->project) && isset($project->project->project_name)) {
                                $project_name .=', '.ucfirst($project->project->project_name);
                           }
                       }
                }
                
                // old method
//                $location = '';
//                if(isset($product->itemObj->locationCode) && !empty($product->itemObj->locationCode)) {
//                    $locationCode = CodeType::where('location_code', $product->itemObj->locationCode)->first();
//                    if($locationCode) {
//                        $location =  ucfirst($locationCode->location_name);
//                    }
//                }
                
                // new method
                 $location = '';
                if($product->codes) {
                    foreach ($product->codes as  $code) {
                        if($code->code) {
                            if(empty($location)) {
                                $location .= ucfirst($code->code->location_name);
                            } else {
                                $location .=', '.ucfirst($code->code->location_name);
                            }
                        }
                    }
                }
                
                
                $category = '';
                if(isset($product->itemObj->salesCategory) && !empty($product->itemObj->salesCategory)) {
                    $salesCategory = SalescategoryType::where('salescategory_code', $product->itemObj->salesCategory)->first();
                    if($salesCategory) {
                        $category = ucfirst($salesCategory->salescategory_name);
                    }
                }
                
                $vendor_name = '';
                if(!is_null($product->vendor_id)) {
                    if($product->vendor) {
                       $vendor_name =  $product->vendor->company_name;
                    }
                }
                $product_name = $product->name;
                $manufacturer = $product->manufacturer;
                $notes = $product->notes;
                $url = $product->url;
                $short_code = $product->short_code ? $product->short_code : '';
                $dimensions = $product->dimensions;
                $materials = $product->materials;
                $lead_time = $product->lead_time;
                $cost_per_unit = $this->global->currency->currency_symbol.$product->cost_per_unit;
                //$default_markup = $this->global->currency->currency_symbol.$product->default_markup; // percentage
                $default_markup = $this->global->currency->currency_symbol.$product->default_markup_fix; // fixed 
                $sales_tax_fix = $this->global->currency->currency_symbol.$product->sales_tax_fix;
                $freight = $this->global->currency->currency_symbol.$product->freight;
                $total_sale = $this->global->currency->currency_symbol.$product->total_sale;
                $acknowledgement = $product->acknowledgement;
                $received_by = $product->received_by;
                
                $est_ship_date = $product->est_ship_date;
                $act_ship_date = $product->act_ship_date;
                $est_receive_date = $product->est_receive_date;
                $act_receive_date = $product->act_receive_date;
                $est_install_date = $product->est_install_date;
                $act_Install_date = $product->act_Install_date;
                $finish_color = $product->finish_color;
                $product_number = $product->product_number;
                
                $spec_number = $product->spec_number;
                $quantity = $product->quantity;
                
                
                $grand_cost_per_unit += $product->cost_per_unit;  
                $grand_total_sale += $product->total_sale;  
                
                
                
                
                if(!is_null($est_ship_date)) { $est_ship_date = Carbon::parse($est_ship_date)->format($this->global->date_format); }
                if(!is_null($act_ship_date)) { $act_ship_date = Carbon::parse($act_ship_date)->format($this->global->date_format); }
                if(!is_null($est_receive_date)) { $est_receive_date = Carbon::parse($est_receive_date)->format($this->global->date_format); }
                if(!is_null($act_receive_date)) { $act_receive_date = Carbon::parse($act_receive_date)->format($this->global->date_format); }
                if(!is_null($est_install_date)) { $est_install_date = Carbon::parse($est_install_date)->format($this->global->date_format); }
                if(!is_null($act_Install_date)) { $act_Install_date = Carbon::parse($act_Install_date)->format($this->global->date_format);}
                
                if(isset($request->name_fl) && $request->name_fl == 1) { $project_name = ''; }
                //if(isset($request->sales_tax_fl) && $request->sales_tax_fl == 1) { $sales_tax_fix = ''; }
                if(isset($request->location_code_fl) && $request->location_code_fl == 1) { $location = ''; }
                if(isset($request->freight_fl) && $request->freight_fl == 1) { $freight = ''; }
                if(isset($request->sales_category_fl) && $request->sales_category_fl == 1) { $category = ''; }
                if(isset($request->total_sale_fl) && $request->total_sale_fl == 1) { $total_sale = ''; }
                if(isset($request->vendor_id_fl) && $request->vendor_id_fl == 1) { $vendor_name = ''; }
                if(isset($request->msrp_fl) && $request->msrp_fl == 1) { $msrp = ''; }
                if(isset($request->manufacturer_fl) && $request->manufacturer_fl == 1) { $manufacturer = ''; }
                if(isset($request->acknowledgement_fl) && $request->acknowledgement_fl == 1) { $acknowledgement = ''; }
                if(isset($request->notes_fl) && $request->notes_fl == 1) { $notes = ''; }
                if(isset($request->est_ship_date_fl) && $request->est_ship_date_fl == 1) { $est_ship_date = ''; }
                if(isset($request->url_fl) && $request->url_fl == 1) { $url = ''; $short_code = ''; }
                if(isset($request->act_ship_date_fl) && $request->act_ship_date_fl == 1) { $act_ship_date = ''; }
                if(isset($request->dimensions_fl) && $request->dimensions_fl == 1) { $dimensions = ''; }
                if(isset($request->est_receive_date_fl) && $request->est_receive_date_fl == 1) { $est_receive_date = ''; }
                if(isset($request->materials_fl) && $request->materials_fl == 1) { $materials = ''; }
                if(isset($request->act_receive_date_fl) && $request->act_receive_date_fl == 1) { $act_receive_date = ''; }
                if(isset($request->received_by_fl) && $request->received_by_fl == 1) { $received_by = ''; }
                if(isset($request->cost_per_unit_fl) && $request->cost_per_unit_fl == 1) { $cost_per_unit = ''; }
                if(isset($request->est_install_date_fl) && $request->est_install_date_fl == 1) { $est_install_date = ''; }
                if(isset($request->default_markup_fl) && $request->default_markup_fl == 1) { $default_markup = ''; }
                if(isset($request->act_install_date_fl) && $request->act_install_date_fl == 1) { $act_Install_date = ''; }
                
                //if(isset($request->default_markup_per_fl) && $request->default_markup_per_fl == 1) { $act_Install_date = ''; }
                if(isset($request->product_number_fl) && $request->product_number_fl == 1) { $product_number = ''; }
                if(isset($request->finish_color_fl) && $request->finish_color_fl == 1) { $finish_color = ''; }
                
                if(isset($request->spec_num_fl) && $request->spec_num_fl == 1) { $spec_number = ''; }
                if(isset($request->qty_fl) && $request->qty_fl == 1) { $quantity = ''; }
                
                
                $location_search = '';
                if ($request->locationCode != 'all' && !is_null($request->locationCode)) {
                    $location_search = $location;
                }
                $project_name_search = '';
                if (isset($request->project_id) && !is_null($request->project_id)) {
                    $project_name_search = $project_name;
                }
                
                
                
                $row = [];
                $row['sq_num'] = $cnt;
                $row['name'] = $product_name;
                $row['image'] = $image;
                $row['project'] = $project_name;
                $row['location'] = $location;
                $row['category'] = $category;
                $row['vendor'] = $vendor_name;
                $row['manufacturer'] = $manufacturer;
                $row['notes'] = $notes;
                $row['url'] = $url;
                $row['short_code'] = $short_code;
                $row['dimensions'] = $dimensions;
                $row['materials'] = $materials;
                $row['lead_time'] = $lead_time;
                $row['cost_per_unit'] = $cost_per_unit;
                $row['default_markup'] = $default_markup;
                $row['sales_tax'] = '';
                $row['freight'] = $freight;
                $row['total'] = $total_sale;
                $row['acknowledgement'] = $acknowledgement;
                $row['receive_by'] = $received_by;
                $row['est_ship_date'] = $est_ship_date;
                $row['act_ship_date'] = $act_ship_date;
                $row['est_receive'] = $est_receive_date;
                $row['act_receive'] = $act_receive_date;
                $row['est_install'] = $est_install_date;
                $row['act_install'] = $act_Install_date;
                $row['finish_color'] = $finish_color;
                $row['product_number'] = $product_number;
                $row['spec_number'] = $spec_number;
                $row['quantity'] = $quantity;
                
                
                $cnt++;
                $productsArr[] = $row;
            }
        }
//         if($this->company->id == 113) {
//             
//             echo '<pre>';
//             print_r($productsArr);exit;
//             
//         }
        
        $this->products = $productsArr;
        $this->location_search = $location_search;
        $this->project_name_search = $project_name_search;
        
        
        $this->grand_cost_per_unit = $this->global->currency->currency_symbol.$grand_cost_per_unit;  
        $this->grand_total_sale = $this->global->currency->currency_symbol.$grand_total_sale;
        
        $pdf = app('dompdf.wrapper');
        
//        if($this->company->id == 113) {
//            $contxt = stream_context_create([
//                'ssl' => [
//                    'verify_peer' => FALSE,
//                    'verify_peer_name' => FALSE,
//                    'allow_self_signed' => TRUE,
//                ]
//            ]);
//            $pdf = \PDF::setOptions(['isHTML5ParserEnabled' => true, 'isRemoteEnabled' => true]);
//            $pdf->getDomPDF()->setHttpContext($contxt);
//        }
        
        
        $pdf->loadView('admin.products.product-all-pdf', $this->data);
        $filename = 'product-all';
        
//         if (isset($request->project_id) && $request->project_id == '84') {
//             return view('admin.products.product-all-pdf', $this->data);
//         }
        
//        if($this->company->id == 113) {
//            return view('admin.products.product-all-pdf', $this->data);
//        }
        
        

        return $pdf->download($filename . '.pdf');
    }
    
    
    public function getSalesCategoryDetail($code)
    {
        $result = SalescategoryType::where("salescategory_code","=",$code)->first();
        $result->salescategory_markup = $result->salescategory_markup?$result->salescategory_markup:0;
        return response()->json(["category" => $result]);
    }
    public function getVendorDetail($vendorID)
    {
        $result = ClientVendorDetails::where("id","=",$vendorID)->first();
        $result->vendor_markup = $result->vendor_markup?$result->vendor_markup:0;
        return response()->json(["vendor" => $result]);
    }
    
    // filter products
    
     public function filterProducts(Request $request)
    {
        
        $query = Product::where('products.id', '!=', 0)->select('products.*');
        if (isset($request->project_id) && !is_null($request->project_id)) {
             $query->join('product_projects', 'product_projects.product_id', '=', 'products.id');
             $query->where('product_projects.project_id', $request->project_id);
        }
        if (isset($request->vendor_id) && !is_null($request->vendor_id)) {
             $query->where('products.vendor_id', $request->vendor_id);
        }
        if ($request->locationCode != 'all' && !is_null($request->locationCode)) {
            $query->where('item->locationCode', '=', $request->locationCode);
        }
        if ($request->salesCategory != 'all' && !is_null($request->salesCategory)) {
           $query->where('item->salesCategory', '=', $request->salesCategory);
        }
        
        $this->products = $query->get();
        
        $view = view('admin.products.filter-products', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
    
    // filter products
    

    public function filterProductsV3(Request $request)
    {
        
        $query = Product::where('products.id', '!=', 0)->select('products.*');
        if (isset($request->project_id) && $request->project_id != 'all' && !is_null($request->project_id)) {
             $query->join('product_projects', 'product_projects.product_id', '=', 'products.id');
             $query->where('product_projects.project_id', $request->project_id);
        }
        if (isset($request->vendor_id) && !is_null($request->vendor_id)) {
             $query->where('products.vendor_id', $request->vendor_id);
        }
        
        if (isset($request->locationCode) && $request->locationCode != 'all' && !is_null($request->locationCode)) {
             $query->join('product_code_types', 'product_code_types.product_id', '=', 'products.id');
             $query->where('product_code_types.code_type_id', $request->locationCode);
        }
        
        if (isset($request->salesCategory) && $request->salesCategory != 'all' && !is_null($request->salesCategory)) {
           $query->where('item->salesCategory', '=', $request->salesCategory);
        }
        
        if (isset($request->statusId) && $request->statusId != 'all' && !is_null($request->statusId)) {
             $query->where('products.product_status_id', $request->statusId);
        }
        
        
        if(isset($request->without_order_date_fl) && $request->without_order_date_fl == 1) { 
            $query->whereNull('ordered_date');
        }
         if(isset($request->with_order_date_fl) && $request->with_order_date_fl == 1) { 
            $query->whereNotNull('ordered_date');
        }
        
        if(isset($request->rfq_sent_fl) && $request->rfq_sent_fl == 1) { 
            $query->whereNull('rfq_sent_date');
        }
         if(isset($request->rfq_not_sent_fl) && $request->rfq_not_sent_fl == 1) { 
            $query->whereNotNull('rfq_sent_date');
        }
        
        if(isset($request->quote_received_fl) && $request->quote_received_fl == 1) { 
            $query->whereNull('quote_received');
        }
         if(isset($request->quote_not_received_fl) && $request->quote_not_received_fl == 1) { 
            $query->whereNotNull('quote_received');
        }
        
        if(isset($request->cfa_approved_fl) && $request->cfa_approved_fl == 1) { 
            $query->whereNull('cfa_approved_date');
        }
         if(isset($request->cfa_not_approved_fl) && $request->cfa_not_approved_fl == 1) { 
            $query->whereNotNull('cfa_approved_date');
        }
        
         if(isset($request->ordered_fl) && $request->ordered_fl == 1) { 
            $query->whereNull('ordered_date');
        }
         if(isset($request->not_ordered_fl) && $request->not_ordered_fl == 1) { 
            $query->whereNotNull('ordered_date');
        }
        
//        if(isset($request->flagged_items_fl) && $request->flagged_items_fl == 1) { 
//            $query->whereNull('ordered_date');
//        }
        
        if(isset($request->without_estimated_ship_date_fl) && $request->without_estimated_ship_date_fl == 1) { 
            $query->whereNull('est_ship_date');
        }
         if(isset($request->with_estimated_ship_date_fl) && $request->with_estimated_ship_date_fl == 1) { 
            $query->whereNotNull('est_ship_date');
        }
        
         if(isset($request->without_ship_date_fl) && $request->without_ship_date_fl == 1) { 
            $query->whereNull('act_ship_date');
        }
         if(isset($request->with_ship_date_fl) && $request->with_ship_date_fl == 1) { 
            $query->whereNotNull('act_ship_date');
        }
        
         if(isset($request->with_estimated_received_date_fl) && $request->with_estimated_received_date_fl == 1) { 
            $query->whereNull('received_date');
        }
         if(isset($request->with_received_date_fl) && $request->with_received_date_fl == 1) { 
            $query->whereNotNull('received_date');
        }
        
        if(isset($request->with_tracking_number_fl) && $request->with_tracking_number_fl == 1) { 
            $query->whereNotNull('tracking_number');
        }
        
        if(isset($request->client_approved_fl) && $request->client_approved_fl == 1) { 
            $query->where('products.is_approved', 1);
        }
        if(isset($request->client_declined_fl) && $request->client_declined_fl == 1) { 
            $query->where('products.is_approved', 0);
        }
        if(isset($request->not_approve_decline_fl) && $request->not_approve_decline_fl == 1) { 
            $query->whereNull('is_approved');
        }
        
        $query->orderBy('id', 'desc');
        
        
        $this->productStatuses = ProductStatus::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        $this->salescategories = SalescategoryType::all();
        
        $this->products = $query->paginate(25);
        
        $view = view('admin.products.filter-products-v3', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
    
    
    
    public function downloadTemplate()
    {
        
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=product-smaple-template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        //name, picture, project_name, location_code, sales_category, vendor_name,manufacturer,notes,url,dimensions,materials,cost_per_unit,markup_fix,markup_percentage,freight,total_sale,msrp,acknowledgement,est_ship_date,act_ship_date,est_receive_date,act_receive_date,received_by,est_install_date,act_Install_date,product_number,finish_color

        $records = array();
        $records[] = array('name' => 'xyz', 'picture'=>'https://app.indema.co/user-uploads/avatar/3ef0256162aca7514a206b47a7f93aa5.png', 'project_name'=>'Project-1', 'location_code'=>'Bathroom', 'sales_category'=>'Material', 'vendor_name'=>'ABC', 'manufacturer'=>'test', 'notes'=> 'test', 'url'=>'https://indema.co/', 'dimensions'=>'Width: 15', 'materials'=>'Ceramic', 'cost_per_unit'=>'10', 'markup_fix'=>'5', 'markup_percentage'=>'0', 'freight'=>'5', 'total_sale'=>'20', 'msrp'=>'5', 'acknowledgement'=>'sample', 'est_ship_date'=>'05, Nov 2021', 'act_ship_date'=>'05, Nov 2021', 'est_receive_date'=>'05, Nov 2021', 'act_receive_date'=>'5, Nov 2021', 'received_by'=>'dev', 'est_install_date'=>'5, Nov 2021', 'act_Install_date'=>'5, Nov 2021', 'product_number'=>'123', 'finish_color'=> 'red');
        $records[] = array('name' => 'abc', 'picture'=>'https://app.indema.co/user-uploads/avatar/3ef0256162aca7514a206b47a7f93aa5.png', 'project_name'=>'Project-2', 'location_code'=>'Bedroom', 'sales_category'=>'Accessories', 'vendor_name'=>'ABc', 'manufacturer'=>'test', 'notes'=> 'test', 'url'=>'https://indema.co/', 'dimensions'=>'Width: 15', 'materials'=>'Ceramic', 'cost_per_unit'=>'15', 'markup_fix'=>'5', 'markup_percentage'=>'0', 'freight'=>'5', 'total_sale'=>'20', 'msrp'=>'5', 'acknowledgement'=>'sample', 'est_ship_date'=>'05, Nov 2021', 'act_ship_date'=>'05, Nov 2021', 'est_receive_date'=>'05, Nov 2021', 'act_receive_date'=>'5, Nov 2021', 'received_by'=>'dev', 'est_install_date'=>'5, Nov 2021', 'act_Install_date'=>'5, Nov 2021', 'product_number'=>'123', 'finish_color' => 'brown');
        
        $columns = array('Name', 'Picture', 'Project Name', 'Location Code', 'Sales Category', 'Vendor Name', 'Manufacturer', 'Notes', 'URL', 'Dimensions', 'Materials', 'Cost Per Unit', 'Markup Fix', 'Markup Percentage', 'Freight', 'Total Sale', 'MSRP', 'Acknowledgement', 'Est Ship Date', 'Act Ship Date', 'Est Receive Date', 'Act Receive_date', 'Received By', 'Est Install Date', 'Act Install Date', 'Product Number', 'Finish Color');

        
        $callback = function() use ($records, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($records as $record) {
                fputcsv($file, array($record['name'], $record['picture'], $record['project_name'], $record['location_code'], $record['sales_category'], $record['vendor_name'], $record['manufacturer'], $record['notes'], $record['url'], $record['dimensions'], $record['materials'], $record['cost_per_unit'], $record['markup_fix'], $record['markup_percentage'], $record['freight'], $record['total_sale'], $record['msrp'], $record['acknowledgement'], $record['est_ship_date'], $record['act_ship_date'], $record['est_receive_date'], $record['act_receive_date'], $record['received_by'], $record['est_install_date'], $record['act_Install_date'], $record['product_number'], $record['finish_color']));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    
    public function import(Request $request) {
        
         $directory = "user-uploads/import-csv/".company()->id;
        if (!File::exists(public_path($directory))) {
            $result = File::makeDirectory(public_path($directory), 0775, true);
        }
        
        $file = $request->file('csv_file');
        if($file) {
        // File Details 
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");
        // 2MB in Bytes
        $maxFileSize = 2097152;
        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {
            // Check file size
            if ($fileSize <= $maxFileSize) {
                
                $fileName = time().".csv";
                // Upload file
                $file->move(public_path($directory), $fileName);
                // Import CSV to Database
                $filepath = public_path($directory . "/" . $fileName);
                // Reading file
                $file = fopen($filepath, "r");
                $importData_arr = array();
                $i = 0;
                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata);
                    // Skip first row (Remove below comment if you want to skip the first row)
                   if($i == 0){$i++; continue;} 
                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                fclose($file);
                
//                // Insert to MySQL database
                foreach ($importData_arr as $importData) {
                    // $importData[3] name
                    // $importData[4] email
                    if(!empty($importData[3]) && !empty($importData[4])) {
                        $this->addImported($importData);
                    }
                }
                
                \Session::put('success', 'Import Successful.');
                return redirect(route('admin.products.index'));
            } else {
                \Session::put('error', 'File too large. File must be less than 2MB.');
                return redirect(route('admin.products.index'));
            }
        } else {
            \Session::put('error', 'Invalid File Extension.');
            return redirect(route('admin.products.index'));
        }
        } else {
            
            \Session::put('error', 'Select File.');
            return redirect(route('admin.products.index'));
            
        }
    }
    
    
//    (
//            0 [name] => xyz
//            1 [picture] => https://app.indema.co/user-uploads/avatar/3ef0256162aca7514a206b47a7f93aa5.png
//            2 [project_name] => Project-1
//            3 [location_code] => Bathroom
//            4 [sales_category] => Material
//            5 [vendor_name] => ABC
//            6 [manufacturer] => test
//            7 [notes] => test
//            8 [url] => https://indema.co/
//            9 [dimensions] => Width: 15
//            10 [materials] => Ceramic
//            11 [cost_per_unit] => 10
//            12 [markup_fix] => 5
//            13 [markup_percentage] => 0
//            14 [freight] => 5
//            15 [total_sale] => 20
//            16 [msrp] => 5
//            17 [acknowledgement] => sample
//            18 [est_ship_date] => 05, Nov 2021
//            19 [act_ship_date] => 05, Nov 2021
//            20 [est_receive_date] => 05, Nov 2021
//            21 [act_receive_date] => 5, Nov 2021
//            22 [received_by] => dev
//            23 [est_install_date] => 5, Nov 2021
//            24 [act_Install_date] => 5, Nov 2021
//            25 [product_number] => 123
//            26 [finish_color] => red
//        )
    
    public function addImported($req) {


        $product = new Product();
        $product->name = isset($req[0]) ? $req[0] : '';
        

        if (isset($req[2]) && !empty($req[2])) {
            $project = Project::where('project_name', $req[2])->first();
            if ($project) {
                $product->project_id = $project->id;
            } else {
                $project = new Project();
                $project->project_name = $req[2];
                $project->start_date = Carbon::now()->format('Y-m-d');
                $product->project_id = $project->id;
            }
        }

        $locationCode = isset($req[3]) ? $req[3] : '';
        $salesCategory = isset($req[4]) ? $req[4] : '';

        if (isset($req[5]) && !empty($req[5])) {
            $vendor = ClientVendorDetails::where('vendor_name', $req[5])->first();
            if ($vendor) {
                $product->vendor_id = $vendor->id;
            } else {
                $vendor = new ClientVendorDetails();
                $vendor->company_name = $req[2];
                $vendor->vendor_name = $req[2];
                $product->vendor_id = $vendor->id;
            }
        }

        $product->manufacturer = isset($req[6]) ? $req[6] : '';
        $product->notes = isset($req[7]) ? $req[7] : '';

        if (isset($req[8]) && !empty($req[8])) {
            $product->url = $req[8];
            if (isset($product->url) && !empty($product->url)) {
                $shortLink = new ShortLink();
                $shortLink->link = $product->url;
                $shortLink->code = str_random(6);
                $shortLink->product_id = $product->id;
                $shortLink->save();
                $product->short_code = $shortLink->code;
            }
        }

        $product->dimensions = isset($req[9]) ? $req[9] : '';
        $product->materials = isset($req[10]) ? $req[10] : '';

        if (isset($req[11]) && !empty($req[11])) {
            $cost_per_unit = 0;
            if (is_numeric(str_replace(",", "", $req[11]))) {
                $cost_per_unit = str_replace(",", "", $req[11]);
            }
            $product->cost_per_unit = $cost_per_unit;
        }

        // if fix set then percentage null vise versa
        if (isset($req[12]) && !empty($req[12])) {
            $markup_fix = '';
            if (is_numeric(str_replace(",", "", $req[12]))) {
                $markup_fix = str_replace(",", "", $req[12]);
            }
            $product->markup_fix = $markup_fix;
            $product->default_markup = null;
            $product->markup_per = null;
        }

        if (isset($req[13]) && !empty($req[13])) {
            $markup_per = '';

            if (is_numeric(str_replace(",", "", $req[13]))) {
                $markup_per = str_replace(",", "", $req[13]);
            }
            $product->markup_per = $markup_per;
            $product->default_markup = $markup_per;
            $product->markup_fix = null;
        }

        if (isset($req[14]) && !empty($req[14])) {
            $freight = '';
            if (is_numeric(str_replace(",", "", $req[14]))) {
                $freight = str_replace(",", "", $req[14]);
            }

            $product->freight = $freight;
        }

        if (isset($req[15]) && !empty($req[15])) {
            $total_sale = 0;
            if (is_numeric(str_replace(",", "", $req[15]))) {
                $total_sale = str_replace(",", "", $req[15]);
            }
            $product->total_sale = $total_sale;
        }

        if (isset($req[16]) && !empty($req[16])) {
            $msrp = '';
            if (is_numeric(str_replace(",", "", $req[16]))) {
                $msrp = str_replace(",", "", $req[16]);
            }
            $product->msrp = $msrp;
        }

        $product->acknowledgement = isset($req[17]) ? $req[17] : '';


        if (isset($req[18]) && !empty($req[18])) {
            $product->est_ship_date = isset($req[18]) ? Carbon::createFromFormat($this->global->date_format, $req[18])->format('Y-m-d') : '';
        }
        if (isset($req[19]) && !empty($req[19])) {
            $product->act_ship_date = isset($req[19]) ? Carbon::createFromFormat($this->global->date_format, $req[19])->format('Y-m-d') : '';
        }
        if (isset($req[20]) && !empty($req[20])) {
            $product->est_receive_date = isset($req[20]) ? Carbon::createFromFormat($this->global->date_format, $req[20])->format('Y-m-d') : '';
        }
        if (isset($req[21]) && !empty($req[21])) {
            $product->act_receive_date = isset($req[21]) ? Carbon::createFromFormat($this->global->date_format, $req[21])->format('Y-m-d') : '';
        }
        $product->received_by = isset($req[22]) ? $req[22] : '';
        if (isset($req[23]) && !empty($req[23])) {
            $product->est_install_date = isset($req[23]) ? Carbon::createFromFormat($this->global->date_format, $req[23])->format('Y-m-d') : '';
        }
        if (isset($req[24]) && !empty($req[24])) {
            $product->act_Install_date = isset($req[24]) ? Carbon::createFromFormat($this->global->date_format, $req[24])->format('Y-m-d') : '';
        }

        $product->product_number = isset($req[25]) ? $req[25] : '';
        $product->finish_color = isset($req[26]) ? $req[26] : '';


        $description = isset($item->description) ? $item->description : '';
        $quantity = isset($item->quantity) ? $item->quantity : '';
        $clientDeposit = isset($item->clientDeposit) ? $item->clientDeposit : '';
        $depositRequested = isset($item->depositRequested) ? $item->depositRequested : '';
        $unit = isset($item->unit) ? $item->unit : '';
        $totalEstimatedCost = isset($item->totalEstimatedCost) ? $item->totalEstimatedCost : '';
        $totalSalesPrice = isset($item->totalSalesPrice) ? $item->totalSalesPrice : '';
        $unitBudget = isset($item->unitBudget) ? $item->unitBudget : '';

        $product->item = json_encode(array(
            'description' => $description,
            'locationCode' => $locationCode,
            'quantity' => $quantity,
            'salesCategory' => $salesCategory,
            'clientDeposit' => $clientDeposit,
            'depositRequested' => $depositRequested,
            'unit' => $unit,
            'totalEstimatedCost' => $totalEstimatedCost,
            'totalSalesPrice' => $total_sale,
            'unitBudget' => $unitBudget
        ));

        $product->save();
        
        
        // save image from URL
        
        if ($product) {

            if (isset($req[1]) && !empty($req[1])) {

                $url = $req[1];
                $extension = pathinfo($url, PATHINFO_EXTENSION);

                
                $directory = "user-uploads/products/$product->id";
                
                if (!File::exists(public_path($directory))) {
                    $result = File::makeDirectory(public_path($directory), 0775, true);
                }
                
                $fileName = time() . mt_rand() . "." . $extension;
                $imageFilePath = "$directory/$fileName";
                
                $content = file_get_contents($url);
                Image::make($content)->save(public_path($imageFilePath));
                $product->picture = json_encode(array(0 => $fileName));
                $product->save();
                //Storage::put($imageFilePath, $contents);
            }
        }
    }
    
    public function sendRFQ(Request $request) {

        if (empty($request->product_id)) {
            return Reply::error('The product does not exist. Try with different product.');
        } else if ($request->select_rfq == 'other' && (empty($request->other_email) || !filter_var($request->other_email, FILTER_VALIDATE_EMAIL) )) {
            return Reply::error('Please provide a valid other email.');
        } else if (empty($request->additional_info)) {
            return Reply::error('Please provide an additional information.');
        }

        $product = Product::find($request->product_id);

        // already user can't added again 
        if (!$product) {
            return Reply::error('The product does not exist. Try with different product.');
        }

        $vendor_email = '';
        $vendor_name = '';
        if (!is_null($product->vendor_id)) {
            if ($product->vendor) {
                $vendor_email = $product->vendor->vendor_email ? $product->vendor->vendor_email : '';
                $vendor_name = $product->vendor->vendor_name ? $product->vendor->vendor_name : '';
            }
        }

        if ($request->select_rfq == 'vendor' && empty($vendor_email)) {
            return Reply::error('The vendor does have email.');
        }
        
        $send_email_to = '';
        if ($request->select_rfq == 'vendor') {
            $send_email_to = $vendor_email;
        } else if ($request->select_rfq == 'agent') {
            $send_email_to = 'info@mydesignassist.com';
        } else {
            $send_email_to = $request->other_email;
        }
        
        $objDemo = new \stdClass();
        
        $objDemo->ProductName = $product->name;
        $objDemo->ProductNumber = $product->product_number;
        $objDemo->Vendor = $vendor_name;
        $objDemo->URL = $product->url;
        $objDemo->Dimensions = $product->dimensions;
        $objDemo->FinishColor = $product->finish_color;
        
        $objDemo->Message =  $request->additional_info;
        
        $objDemo->Subject = 'Request For Quote';
        $objDemo->FromEmail = $this->user->email;
        $objDemo->FromName = $this->user->name;
        
        Mail::to($send_email_to)->send(new RFQEmail($objDemo));

        return Reply::successWithData('RFQ Sent Successfully', ['optionData' => '']);
    }

}
