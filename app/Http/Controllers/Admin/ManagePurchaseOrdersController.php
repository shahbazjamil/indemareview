<?php

namespace App\Http\Controllers\Admin;

use App\ClientPayment;
use App\CreditNotes;
use App\Currency;
use App\DataTables\Admin\InvoicesDataTable;
use App\DataTables\Admin\PurchaseOrdersDataTable;
use App\Estimate;
use App\Helper\Reply;
use App\Http\Requests\Admin\Client\StoreShippingAddressRequest;
use App\Http\Requests\InvoiceFileStore;
use App\Http\Requests\Invoices\StoreInvoice;
use App\Http\Requests\PurchaseOrders\StorePurchaseOrder;
use App\Invoice;
use App\PurchaseOrder;
use App\PurchaseOrderItems;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\Notifications\PaymentReminder;
use App\OfflineInvoicePayment;
use App\Product;
use App\Project;
use App\Proposal;
use App\Scopes\CompanyScope;
use App\Tax;
use App\User;
use App\Vendor;
use App\ClientVendorDetails;
use App\VendorInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Invoices\UpdateInvoice;
use App\Http\Requests\PurchaseOrders\UpdatePurchaseOrder;
use App\Notifications\NewInvoice;
use App\ProjectMilestone;
use App\ProjectTimeLog;
use App\SalescategoryType;
use App\CodeType;
use Illuminate\Support\Facades\File;
use App\Helper\Files;
use Yajra\DataTables\Facades\DataTables;

// bitsclan code start here
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\PurchaseOrder as QuickbooksPurchaseOrder;
use App\QuickbooksSettings;
use QuickBooksOnline\API\Facades\Vendor as QuickbooksVendor; 
use QuickBooksOnline\API\Facades\Item;
use App\Http\Requests\CommonRequest;
use App\PoStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\PurchaseOrderEmail;
// bitsclan code end here

class ManagePurchaseOrdersController extends AdminBaseController
{
    //bitsclan code here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    //code end here

    public function __construct()
    {
        parent::__construct();
        // $this->pageTitle = 'app.menu.invoices';
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            if (!in_array('purchaseOrders', $this->user->modules)) {
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

        //code end here


    }

    public function index(PurchaseOrdersDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/purchase-orders'));
        
        $this->pageTitle = 'Purchase Orders';
        $this->projects = Project::all();
        $this->clients = User::allClients();
        
        $this->totalRecords = PurchaseOrder::count();
        
        return $dataTable->render('admin.purchase-orders.index', $this->data);
    }
    
//    public function destroy($id)
//    {
//        $firstInvoice = Invoice::orderBy('id', 'desc')->first();
//        if ($firstInvoice->id == $id) {
//            if (CreditNotes::where('invoice_id', $id)->exists()) {
//                CreditNotes::where('invoice_id', $id)->update(['invoice_id' => null]);
//            }
//            Invoice::destroy($id);
//            return Reply::success(__('messages.invoiceDeleted'));
//        } else {
//            return Reply::error(__('messages.invoiceCanNotDeleted'));
//        }
//    }
    public function getVendorName()
    {
        return DB::table('vendors')->select('id', 'name')->get();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function archive($id = 0)
    {
        $this->pageTitle = 'Purchase Orders';
        $this->project_id = $id;
        //$this->totalPurchaseOrders = PurchaseOrder::onlyTrashed()->count();
        return view('admin.purchase-orders.archive', $this->data);
    }
    
    public function archiveData(Request $request, $id = 0)
    {
        if($id != 0) {
            $purchaseOrders = PurchaseOrder::select('id', 'purchase_order_number', 'vendor_id', 'status_id', 'project_id', 'document_tags', 'purchase_order_date', 'status')->where('project_id', $id);
        } else {
            $purchaseOrders = PurchaseOrder::select('id', 'purchase_order_number', 'vendor_id', 'status_id', 'project_id', 'document_tags', 'purchase_order_date', 'status');
        }
        
        
        $purchaseOrders->onlyTrashed()->get();

        return DataTables::of($purchaseOrders)
            ->addColumn('action', function ($row) {
                return '
                      <a href="javascript:;" class="btn btn-info btn-circle revert"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Restore"><i class="fa fa-undo" aria-hidden="true"></i></a>
                       <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
             ->editColumn('purchase_order_number',function ($row) {
                return 'PO-'.$row->purchase_order_number;
                })
            ->editColumn('vendor_name', function ($row) {
                if(!empty($row->vendor_id)) {
                    $vendor = ClientVendorDetails::where('id', $row->vendor_id)->first();
                    if($vendor) {
                        return ucfirst($vendor->vendor_name);
                    }
                    return '--';
                }
               return '--';
                //return $row->project_id;
            })
            
            ->editColumn('project_name', function ($row) {                
                if(!empty($row->project_id)) {
                    $project = Project::where('id', $row->project_id)->first();
                    if($project) {
                        return ucfirst($project->project_name);
                    }
                    return '--';
                }
                return '--';
                //                if($row->project_id){
//                        return ucfirst($row->project->project_name);
//                }
                //return $row->project_id;
            })
            ->editColumn(
                'document_tags',
                function ($row) {
                        $str_tags = '';
                        $document_tags = json_decode($row->document_tags);
                        
                        if($document_tags) {
                            foreach ($document_tags as $document_tag) {
                                if($str_tags == '') {
                                    $str_tags .=$document_tag;
                                } else {
                                    $str_tags .=','.$document_tag;
                                }
                                
                            }
                        }
                        return $str_tags;
                }
            )
            ->editColumn(
                'purchase_order_date',
                function ($row) {
                    return $row->purchase_order_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
            ->editColumn(
                'status',
                function ($row) {
                    //return ucfirst($row->status);
                    $status = PoStatus::all();
                    $statusLi = '--';
                    foreach ($status as $st) {
                        if ($row->status_id == $st->id) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        $statusLi .= '<option ' . $selected . ' value="' . $st->id . '">' . $st->type . '</option>';
                    }

                    $action = '<select class="form-control statusChange" name="statusChange" onchange="changeStatus( ' . $row->id . ', this.value)">
                        ' . $statusLi . '
                    </select>';


                    return $action;
                
                }
            )
           
            ->rawColumns(['purchase_order_number', 'action', 'status'])
            ->make(true);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function archiveRestore($id)
    {

        $project = PurchaseOrder::withTrashed()->findOrFail($id);
        $project->restore();
        return Reply::success('Purchase Order reverted successfully.');
    }
    
    public function create(Request $request)
    {
        $default_project_id = 0;
        if(isset($request->project_id) && $request->project_id!='') {
            $default_project_id = $request->project_id;
        }
        
        $this->default_project_id = $default_project_id;
        

        $this->pageTitle = ' Create Purchase Order';
        $this->vendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        $this->currencies = Currency::all();
        
        
        //$this->lastInvoice = PurchaseOrder::count() + 1;
        $po_order = PurchaseOrder::withoutGlobalScopes([CompanyScope::class])->latest()->first();
        $this->lastInvoice = date('Y').'-'.sprintf("%06d",$po_order->id+1);
        
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        $this->taxes = Tax::all();
        $this->products = Product::all();
        
        $this->status = PoStatus::all();
        
        $this->projects = Project::orderBy('project_name')->get();
        $this->salescategories = SalescategoryType::all();
        $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        
        return view('admin.purchase-orders.create', $this->data);
        
    }


    public function store(StorePurchaseOrder $request)
    {
        
        // dd($request->all());
        $items = $request->input('item_name');
        $product_ids = $request->input('product_id');
        $quantity = $request->input('quantity');
        $cost_per_item = $request->input('cost_per_item');
        
        $vendor_id = $request->input('vendor_id');
        $address = $request->input('address');
        $email = $request->input('email');
        $contact = $request->input('contact');
        $company = $request->input('company');
        $account_no = $request->input('account_no');
        
        $shipping_address = $request->input('shipping_address');
        $purchase_order_date = $request->input('purchase_order_date');
        $terms = $request->input('terms');
        //$document_owner = $request->input('document_owner');
        $memo_order = $request->input('memo_order');
        $product_subtotal= $request->input('product_subtotal');
        
        
        
        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }

        // bitsclan code start here
        $items_array = array();
        $qbo_id = '';
        $this->quickbook = $this->QuickbookSettings();
        // if($this->quickbook){

        //    $Line1                               = '';
        //    $City                               = '';
        //    $CountrySubDivisionCode             = '';
        //    $PostalCode                        = '';
          

        //     $adminSetting = User::where('email', ($this->user->email))->first();
        //     $qb_vendor_detail = ClientVendorDetails::where('id', $vendor_id)->first();
        //     if(!empty($qb_vendor_detail->qbo_id)){
        //         $quickbook_vendor_id = isset($qb_vendor_detail->qbo_id) ? $qb_vendor_detail->qbo_id : '';
        //     }else{
        //         if($qb_vendor_detail->company_address)
        //         {
        //             $address = preg_split('/\r\n|\r|\n/', $qb_vendor_detail->company_address); 
        //             $Line1 = isset($address[0]) ? $address[0] : '';
        //             if(isset($address[1])){
        //                 $address2 =  explode(',',trim($address[1]));
        //                 $City = isset($address2[0]) ? $address2[0] : '';
        //             }
        //             if(isset($address2[1])){
        //                 $address3 =  explode(' ',trim($address2[1]));
        //             } 
        //             $CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
        //             $PostalCode = isset($address3[1]) ? $address3[1] : '';
        //         }

        //         $theResourceObj = QuickbooksVendor::create([
        //             "BillAddr" => [
        //                 "Line1"=> $Line1,
        //                 "City"=> $City,
        //                 "CountrySubDivisionCode"=>$CountrySubDivisionCode,
        //                 "PostalCode"=> $PostalCode,
        //             ],
        //             "GivenName"=> $qb_vendor_detail->vendor_rep_name,
        //             "FamilyName"=> $qb_vendor_detail->company_name,
        //             "CompanyName"=> $qb_vendor_detail->company_name,
        //             "DisplayName"=> $qb_vendor_detail->company_name,
        //             "PrintOnCheckName"=> $qb_vendor_detail->vendor_rep_name,
        //             "PrimaryPhone"=> [
        //                 "FreeFormNumber"=> $qb_vendor_detail->rep_phone
        //             ],
        //             "Mobile"=> [
        //                 "FreeFormNumber"=> $qb_vendor_detail->vendor_number,
        //             ],
        //             "WebAddr" => [
        //                 "URI" => $qb_vendor_detail->company_website ? $qb_vendor_detail->company_website : null,
        //             ],
        //             "PrimaryEmailAddr"=> [
        //                 "Address"=> $qb_vendor_detail->rep_email
        //             ]
        //         ]);

        //         $resultingCustomerObj = $this->quickbook->Add($theResourceObj);
        //         $error =  $this->quickbook->getLastError();

        //         if($error){
        //             return Reply::error(__($error->getOAuthHelperError()));
        //         }   


        //         $quickbook_vendor_id = $resultingCustomerObj->Id;
        //         $qb_vendor_detail->qbo_id = $quickbook_vendor_id;
        //         $qb_vendor_detail->save();

        //     }
            
        //     $quickbook_items = array();
        //     foreach ($items as $key => $item) {
        //         $item_detail = Product::where('name', $item)->first();

        //         if(!empty($item_detail)){

        //             if(!empty($item_detail->qbo_id)){
        //                 $item_qbo_id =  $item_detail->qbo_id;
        //             }else{
        //                 $dateTime = new \DateTime('NOW');
        //                 $Item = Item::create([
        //                     "Name" => $item_detail->name,
        //                     "Description" => $item_detail->name,
        //                     "Active" => true,
        //                     "FullyQualifiedName" => $item_detail->name,
        //                     "Taxable" => false,
        //                     "UnitPrice" => $item_detail->msrp,
        //                     "Type" => "NonInventory",
        //                     "IncomeAccountRef"=> [
        //                         "name" => "Sales - Company Service", 
        //                         "value" => $adminSetting->income_account
        //                     ],
        //                     "PurchaseDesc"=> $item_detail->name,
        //                     "PurchaseCost"=> $item_detail->cost_per_unit,
        //                     "TrackQtyOnHand" => false,
        //                     "InvStartDate"=> $dateTime
        //                 ]);

        //                 $ResultRreponse = $this->quickbook->Add($Item);
        //                 $error =  $this->quickbook->getLastError();
        //                 if($error){
        //                     return Reply::error(__($error->getResponseBody()));
        //                 }


        //                 $item_qbo_id = $ResultRreponse->Id;
        //                 $item_detail->qbo_id =  $item_qbo_id;
        //                 $item_detail->save();
        //             }

        //             $item_to_be_pushed = array(
        //                 "DetailType" => "ItemBasedExpenseLineDetail",
        //                 "Amount" =>  $cost_per_item[$key],
        //                 'ItemBasedExpenseLineDetail' => array(
        //                     'ItemRef' => array(
        //                         'value' => $item_qbo_id,
        //                         'name' => $item
        //                     ),
        //                     'Qty' => $quantity[$key],
        //                     'UnitPrice' => $cost_per_item[$key]
        //                 ),
        //             );
        //             array_push($quickbook_items, $item_to_be_pushed);
        //         }
        //     }

        //     $theResourceObj = QuickbooksPurchaseOrder::create([
        //         "TotalAmt" => $product_subtotal,
        //         "Line" => $quickbook_items,
        //         "APAccountRef" => [
        //             "name" => "Account Pay Indema1", 
        //             "value" => $adminSetting->payable_account
        //         ], 
        //         "VendorRef" =>  [
        //             'value' => $quickbook_vendor_id,
        //             'name' => $qb_vendor_detail->vendor_name,
        //         ],

        //     ]);

        //     // $resultingObj = $this->quickbook->Add($theResourceObj);
        //     // $error =  $this->quickbook->getLastError();
        //     // if($error){
        //     //    return Reply::error(__($error->getOAuthHelperError()));
        //     // }   
        //     // $qbo_id = $resultingObj->Id;

        // }


        //bitsclan code ends here


        //PurchaseOrderItems;
        
        $po_order = PurchaseOrder::withoutGlobalScopes([CompanyScope::class])->latest()->first();
        $po = new PurchaseOrder();
        //$po->purchase_order_number =  PurchaseOrder::count() + 1;
        //$po->purchase_order_number =  date('Y').'-'.sprintf("%06d",$po_order->id+1);
        $po->purchase_order_number =  $request->purchase_order_number;
        $po->vendor_id = $vendor_id;
        $po->address = $address;
        $po->email = $email;
        $po->contact = $contact;
        $po->company = $company;
        $po->account_no = $account_no;
        $po->shipping_address = $shipping_address;
        $po->purchase_order_date = Carbon::createFromFormat($this->global->date_format, $request->purchase_order_date)->format('Y-m-d');
        $po->terms = $terms;
        //$po->document_owner = $document_owner;
        $po->memo_order = $memo_order;
        //$po->document_tags = json_encode($request->document_tags);
        
        $po->product_subtotal = $product_subtotal;
        $po->total_amount  = round($request->total_amount , 2);
        
        $po->discount = round($request->discount_value, 2);
        $po->discount_type = $request->discount_type;
        
        $po->freight_value = round($request->freight_value, 2);
        $po->freight_type = $request->freight_type;
        
        $po->project_id = $request->project_id;
        $po->qbo_id = $qbo_id;
        $po->status_id = $request->status;
        
        if(isset($request->invoice_id)) {
            $po->invoice_id = $request->invoice_id;
        }
        
        
        $po->document_tags = json_encode(array());
        if($request->document_tags) {
            $po->document_tags =   json_encode(array_values(array_unique($request->document_tags)));
        }
        
        
        
        if ($request->hasFile('specification_file')) {
            $po->specification_file = Files::upload($request->specification_file, 'purchase-orders' ,null,null,false); 
        }
        
        
        $po->save();
        

        //log search
        $this->logSearchEntry($po->id, 'PurchaseOrder ' . $po->purchase_order_number, 'admin.purchase-orders.show', 'purchase_order');
        
        
        return Reply::redirect(route('admin.purchase-orders.index'), 'Purchase Order Created');
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
        $this->pageTitle = ' Edit Purchase Order';
        $this->po = PurchaseOrder::findOrFail($id);
        
        $document_tags = $this->po->document_tags ? json_decode($this->po->document_tags) : array();
        $this->po->document_tags = $document_tags;
        
        if($document_tags) {
            $this->po->document_tags = array_values(array_unique($document_tags));
        }
        
        $this->vendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        
        $this->status = PoStatus::all();
        
        $this->projects = Project::orderBy('project_name')->get();
        $this->salescategories = $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();

        return view('admin.purchase-orders.edit', $this->data);
    }

    public function update(UpdatePurchaseOrder $request, $id)
    {
        $items = $request->input('item_name');
        $product_ids = $request->input('product_id');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');

        if($quantity) {
            foreach ($quantity as $qty) {
                if (!is_numeric($qty) && $qty < 1) {
                    return Reply::error(__('messages.quantityNumber'));
                }
            }
        }
        

        if($cost_per_item) {
            foreach ($cost_per_item as $rate) {
                if (!is_numeric($rate)) {
                    return Reply::error(__('messages.unitPriceNumber'));
                }
            }
        }
        

        if($amount) {
            foreach ($amount as $amt) {
                if (!is_numeric($amt)) {
                    return Reply::error(__('messages.amountNumber'));
                }
            }
        }
        
        if($items) {
            foreach ($items as $itm) {
                if (is_null($itm)) {
                    return Reply::error(__('messages.itemBlank'));
                }
            }
        }
        $po = PurchaseOrder::findOrFail($id);



        // bitsclan code start here
        $items_array = array();
        $qbo_id = '';
        // if(!empty($this->quickbook)  && !empty($po->qbo_id)){

        //     $vendor_id           = $request->vendor_id;
        //     $product_subtotal = $request->product_subtotal;
        //     $qb_vendor_detail = ClientVendorDetails::where('id', $vendor_id)->first();
        //     $quickbook_vendor_id = isset($qb_vendor_detail->qbo_id) ? $qb_vendor_detail->qbo_id : '';


        //     $quickbook_items = array();
        //     $adminSetting = User::where('email', ($this->user->email))->first();
        //     foreach ($items as $key => $item) {
        //         $item_detail = Product::where('name', $item)->first();
        //         $invoice_item_detail = InvoiceItems::where(['item_name' => $item])->first();

        //         if(!empty($item_detail)){

        //             if(!empty($item_detail->qbo_id)){
        //                 $item_qbo_id =  $item_detail->qbo_id;
        //             }else{
        //                 $dateTime = new \DateTime('NOW');
        //                 $Item = Item::create([
        //                     "Name" => $item_detail->name,
        //                     "Description" => $item_detail->name,
        //                     "Active" => true,
        //                     "FullyQualifiedName" => $item_detail->name,
        //                     "Taxable" => false,
        //                     "UnitPrice" => $item_detail->msrp,
        //                     "Type" => "NonInventory",
        //                     "IncomeAccountRef"=> [
        //                         "name" => "Sales - Company Service", 
        //                         "value" => $adminSetting->income_account
        //                     ],
        //                     "PurchaseDesc"=> $item_detail->name,
        //                     "PurchaseCost"=> $item_detail->cost_per_unit,
        //                     "TrackQtyOnHand" => false,
        //                     "InvStartDate"=> $dateTime
        //                 ]);

        //                 $ResultRreponse = $this->quickbook->Add($Item);
        //                 $error =  $this->quickbook->getLastError();
        //                 if($error){
        //                     return Reply::error(__($error->getResponseBody()));
        //                 }

        //                 $item_qbo_id = $ResultRreponse->Id;
        //                 $item_detail->qbo_id =  $item_qbo_id;
        //                 $item_detail->save();
        //             }


        //             $item_to_be_pushed = array(
        //                 "DetailType" => "ItemBasedExpenseLineDetail",
        //                 "Amount" =>  $cost_per_item[$key],
        //                 'ItemBasedExpenseLineDetail' => array(
        //                     'ItemRef' => array(
        //                         'value' => $item_qbo_id,
        //                         'name' => $item
        //                     ),
        //                     'Qty' => $quantity[$key],
        //                     'UnitPrice' => $cost_per_item[$key]
        //                 ),
        //             );
        //             array_push($quickbook_items, $item_to_be_pushed);
        //         }

        //         else if(!empty($invoice_item_detail)){

        //             if(!empty($invoice_item_detail->qbo_id)){
        //                 $item_qbo_id = $invoice_item_detail->qbo_id;
        //             }else{
        //                 $dateTime = new \DateTime('NOW');
        //                 $Item = Item::create([
        //                     "Name" => $item,
        //                     "Description" => $invoice_item_detail->item_summary,
        //                     "Active" => true,
        //                     "FullyQualifiedName" => $item,
        //                     "Taxable" => false,
        //                     "UnitPrice" => 0,
        //                     "Type" => "NonInventory",
        //                     "IncomeAccountRef"=> [
        //                         "name" => "Sales - Company Service", 
        //                         "value" => $adminSetting->income_account
        //                     ],
        //                     "PurchaseDesc"=> $item,
        //                     "PurchaseCost"=> $invoice_item_detail->unit_price,
        //                     "TrackQtyOnHand" => false,
        //                     "InvStartDate"=> $dateTime
        //                 ]);

        //                 $ResultRreponse = $this->quickbook->Add($Item);
        //                 $error =  $this->quickbook->getLastError();
        //                 if($error){
        //                     return Reply::error(__($error->getResponseBody()));
        //                 }
        //                 $item_qbo_id = $ResultRreponse->Id;
        //                 $invoice_item_detail->qbo_id =  $item_qbo_id;
        //                 $invoice_item_detail->save();
        //             }


        //             $item_to_be_pushed = array(
        //                 "DetailType" => "ItemBasedExpenseLineDetail",
        //                 "Amount" =>  $cost_per_item[$key],
        //                 'ItemBasedExpenseLineDetail' => array(
        //                     'ItemRef' => array(
        //                         'value' => $item_qbo_id,
        //                         'name' => $item
        //                     ),
        //                     'Qty' => $quantity[$key],
        //                     'UnitPrice' => $cost_per_item[$key]
        //                 ),
        //             );
        //             array_push($quickbook_items, $item_to_be_pushed);
        //         }
        //     }
        //     // $entities = $this->quickbook->Query("SELECT * FROM PurchaseOrder where Id='".$po->qbo_id."'");
        //     // $thePurchaseOrder = reset($entities);



        //     // $theResourceObj = QuickbooksPurchaseOrder::update($thePurchaseOrder,[
        //     //     "TotalAmt" => $product_subtotal,
        //     //     "Line" => $quickbook_items,
        //     //     "APAccountRef" => [
        //     //         "name" => "Accounts Payable (A/P)", 
        //     //         "value" => "33"
        //     //     ], 
        //     //     "VendorRef" =>  [
        //     //         'value' => $quickbook_vendor_id,
        //     //         'name' => $qb_vendor_detail->vendor_name,
        //     //     ],

        //     // ]);

        //     // $resultingObj = $this->quickbook->update($theResourceObj);
        //     // $error =  $this->quickbook->getLastError();
        //     // if($error){
        //     //     return Reply::error(__($error->getOAuthHelperError()));
        //     // }         
        //     // $qbo_id = $resultingObj->Id;

            

        // }


        //bitsclan code ends here
       

        $po = PurchaseOrder::findOrFail($id);
        
        $po->vendor_id           = $request->vendor_id;
        $po->address           = $request->address;
        $po->email = $request->email;
        $po->contact           = $request->contact;
        $po->company           = $request->company;
        $po->account_no           = $request->account_no;
        $po->shipping_address           = $request->shipping_address;
        $po->purchase_order_date = Carbon::createFromFormat($this->global->date_format, $request->purchase_order_date)->format('Y-m-d');
        $po->terms           = $request->terms;
        //$po->document_owner           = $request->document_owner;
        $po->memo_order           = $request->memo_order;
        //$po->document_tags = json_encode($request->document_tags);
        $po->product_subtotal = $request->product_subtotal;
        $po->total_amount  = round($request->total_amount , 2);
        $po->discount = round($request->discount_value, 2);
        $po->discount_type = $request->discount_type;
        
        $po->freight_value = round($request->freight_value, 2);
        $po->freight_type = $request->freight_type;
        
        $po->project_id = $request->project_id;
        $po->status_id = $request->status;
        
        $po->document_tags = json_encode(array());
        if($request->document_tags) {
            $po->document_tags =   json_encode(array_values(array_unique($request->document_tags)));
        }
        
        
        if ($request->hasFile('specification_file')) {
            File::delete(public_path() . '/user-uploads/purchase-orders/' . $po->specification_file);
            $po->specification_file = Files::upload($request->specification_file, 'purchase-orders' ,null,null,false); 
        }
        
        $po->save();

        // delete and create new
        PurchaseOrderItems::where('purchase_order_id', $po->id)->delete();

        if($items) {
            foreach ($items as $key => $item) :
                PurchaseOrderItems::create(
                    [
                        'purchase_order_id' => $po->id,
                        'item_name' => $item,
                        'item_summary' => $itemsSummary[$key],
                        'product_id' => $product_ids[$key],
                        'type' => 'item',
                        'quantity' => $quantity[$key],
                        'unit_price' => round($cost_per_item[$key], 2),
                        'amount' => round($amount[$key], 2),
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                    ]
                );
            endforeach;
        }

        return Reply::redirect(route('admin.purchase-orders.index'), 'Purchase Order Updated');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $project = PurchaseOrder::withTrashed()->findOrFail($id);
        $project->forceDelete();
        
        return Reply::success('Purchase Order deleted successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function archiveDestroy($id)
    {
        PurchaseOrder::destroy($id);
        return Reply::success('Purchase Order archive successfully.');
    }

//    public function show($id)
//    {
//        $this->pageTitle = 'Invoice';
//        $this->invoice = Invoice::findOrFail($id);
//        $this->paidAmount = $this->invoice->getPaidAmount();
//
//
//        if ($this->invoice->discount > 0) {
//            if ($this->invoice->discount_type == 'percent') {
//                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
//            } else {
//                $this->discount = $this->invoice->discount;
//            }
//        } else {
//            $this->discount = 0;
//        }
//
//        $taxList = array();
//
//        $items = InvoiceItems::whereNotNull('taxes')
//            ->where('invoice_id', $this->invoice->id)
//            ->get();
//        foreach ($items as $item) {
//            if ($this->invoice->discount > 0 && $this->invoice->discount_type == 'percent') {
//                $item->amount = $item->amount - (($this->invoice->discount / 100) * $item->amount);
//            }
//            foreach (json_decode($item->taxes) as $tax) {
//                $this->tax = InvoiceItems::taxbyid($tax)->first();
//                if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
//                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
//                } else {
//                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
//                }
//            }
//        }
//
//        $this->taxes = $taxList;
//
//        $this->settings = $this->company;
//        $this->invoiceSetting = InvoiceSetting::first();
//        return view('admin.purchase-orders.show', $this->data);
//    }


    

 

    public function addItems(Request $request)
    {
        //$this->items = Product::with('tax')->find($request->id);
        
        $this->items = Product::find($request->id);
        $this->items->afterLoad();
        $exchangeRate = Currency::find($request->currencyId);

//        if ($this->items->total_amount != "") {
//            $this->items->price = floor($this->items->total_amount * $exchangeRate->exchange_rate);
//        } else {
//            $this->items->price = floor($this->items->price * $exchangeRate->exchange_rate);
//        }
        // Added By SB
        if(isset($this->items->itemObj->totalEstimatedCost) && !empty($this->items->itemObj->totalEstimatedCost)) {
            $this->items->price = floor($this->items->itemObj->totalEstimatedCost * $exchangeRate->exchange_rate);
        } else {
            $this->items->price = 0;
        }
        
        $this->taxes = Tax::all();
        $view = view('admin.purchase-orders.add-item', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
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
    
    public function getVendorDetail($id)
    {
        $result = ClientVendorDetails::where("id","=",$id)->first();
        return response()->json(["vendor" => $result]);
    }
    
    
      public function edit_1($id)
    {
        $this->pageTitle = ' Edit Purchase Order';
        $this->po = PurchaseOrder::findOrFail($id);
        
        $this->po->document_tags = $this->po->document_tags ? json_decode($this->po->document_tags) : array();
        
        $this->vendors = ClientVendorDetails::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();

        return view('admin.purchase-orders.edit', $this->data);
    }
    
    public function download($id)
    {

        $this->po = PurchaseOrder::findOrFail($id);
        $this->company = company();
        //$this->company->logo_url;

        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return $pdf->download($filename . '.pdf');
    }
    
    
    public function sendPdf($id){
        
        $this->po = PurchaseOrder::findOrFail($id);
        $this->company = company();
        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];
        $specification_file = '';
        
        if(!is_null($this->po->specification_file)) {
            $specification_file = public_path() . '/user-uploads/purchase-orders/' . $this->po->specification_file;
        }
        
        $objDemo = new \stdClass();
        $objDemo->Message = __('email.purchaseOrder.text');
        $objDemo->Subject = __('email.purchaseOrder.subject').'['.$this->po->purchase_order_number.']';
        $objDemo->thankyouNote = __('email.thankyouNote');
        $objDemo->FromEmail = $this->user->email;
        $objDemo->FromName = $this->user->name;
        $objDemo->pdf = $pdf;
        $objDemo->filename = $filename;
        $objDemo->specification_file = $specification_file;
       
        if(isset($this->po->vendor)) {
            if(isset($this->po->vendor->vendor_email) && !empty($this->po->vendor->vendor_email)) {
                if($id == 89) {
                    Mail::to('shahbazjamil@gmail.com')->send(new PurchaseOrderEmail($objDemo));
                } else {
                    Mail::to($this->po->vendor->vendor_email)->send(new PurchaseOrderEmail($objDemo));
                }
            } else {
                 return Reply::error('PDF will not send because Vendor email is not set.');
            }
        } else {
            return Reply::error('PDF will not send because Vendor email is not set.');
        }
        
      return Reply::success('Sent successfully');
        
       
    }
    
    public function domPdfObjectForDownload($id)
    {
        $this->po = PurchaseOrder::findOrFail($id);
        $this->company = company();
        
        if ($this->po->discount > 0) {
            if ($this->po->discount_type == 'percent') {
                $this->discount = (($this->po->discount / 100) * $this->po->product_subtotal);
            } else {
                $this->discount = $this->po->discount;
            }
        } else {
            $this->discount = 0;
        }
        
        $taxList = array();

        $items = PurchaseOrderItems::whereNotNull('taxes')
            ->where('purchase_order_id', $this->po->id)
            ->get();

        foreach ($items as $item) {
            if ($this->po->discount > 0 && $this->po->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->po->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = PurchaseOrderItems::taxbyid($tax)->first();
                if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                } else {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                }
            }
        }
        $this->taxes = $taxList;
        
        $this->invoiceSetting = InvoiceSetting::first();
        
        //echo $this->invoiceSetting->id;exit;
        
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.purchase-orders.po-pdf', $this->data);
        //$pdf->loadView('invoices.' . $this->invoiceSetting->template, $this->data);
        $filename = $this->po->purchase_order_number;
        
        
        
        
        
        

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }
    
    /**
     * @param CommonRequest $request
     * @return array
     */
    public function changeStatus(CommonRequest $request)
    {
        $po = PurchaseOrder::findOrFail($request->poID);
        $po->status_id = $request->statusID;
        $po->save();

        return Reply::success(__('messages.poStatusChangeSuccess'));
    }
    
    public function convertPurchaseOrder($id)
    {
        $this->pageTitle = ' Convert Invoice to Purchase Order';
        $this->po = Invoice::findOrFail($id);
        
        $po_order = PurchaseOrder::withoutGlobalScopes([CompanyScope::class])->latest()->first();
        $this->lastInvoice = date('Y').'-'.sprintf("%06d",$po_order->id+1);
        
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        
        $document_tags = $this->po->document_tags ? json_decode($this->po->document_tags) : array();
        $this->po->document_tags = $document_tags;
        
        if($document_tags) {
            $this->po->document_tags = array_values(array_unique($document_tags));
        }
        
        $this->vendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        
        $this->status = PoStatus::all();
        
        $this->projects = Project::orderBy('project_name')->get();
        $this->salescategories = $this->salescategories = SalescategoryType::all();
        $this->codetypes = $this->codetypes = CodeType::all();
        $this->clientVendors = ClientVendorDetails::orderBy('company_name', 'ASC')->get();

        return view('admin.purchase-orders.convert_invoice', $this->data);
    }

//    public function sendInvoice($invoiceID)
//    {
//        $invoice = Invoice::with(['project', 'project.client'])->findOrFail($invoiceID);
//        if ($invoice->project_id != null && $invoice->project_id != '') {
//            if (isset($invoice->project->client)) {
//                $notifyUser = User::withoutGlobalScope(CompanyScope::class)->find($invoice->project->client_id);
//            }
//        } elseif ($invoice->client_id != null && $invoice->client_id != '') {
//            $notifyUser = $invoice->client;
//        }
//
//        if (!is_null($notifyUser)) {
//            $notifyUser->notify(new NewInvoice($invoice));
//        }
//
//        $invoice->send_status = 1;
//        if ($invoice->status == 'draft') {
//            $invoice->status = 'unpaid';
//        }
//        $invoice->save();
//        return Reply::success(__('messages.updateSuccess'));
//    }
}
