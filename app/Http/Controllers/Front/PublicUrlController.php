<?php

namespace App\Http\Controllers\Front;

use App\AcceptEstimate;
use App\Company;
use App\Contract;
use App\ContractSign;
use App\Estimate;
use App\EstimateItem;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Front\FrontBaseController;
use App\Http\Requests\Admin\Contract\SignRequest;
use App\Http\Requests\EstimateAcceptRequest;
use App\Product;
use App\Invoice;
use App\InvoiceItems;
use App\Notifications\ContractSigned;
use App\Notifications\NewInvoice;
use App\Notifications\NewNotice;
use App\ProjectMilestone;
use App\Scopes\CompanyScope;
use App\Setting;
use App\UniversalSearch;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstimateAcceptEmail;
use App\LineItemGroup;

// Added by BitsClan
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Invoice as QuickbookInvoices;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Account;
use App\QuickbooksSettings;
use App\ClientDetails;
use App\InvoiceSetting;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

//End Here

class PublicUrlController extends FrontBaseController
{
     use AuthenticatesUsers;
     
    public function estimateView(Request $request, $id)
    {
        $pageTitle = __('app.menu.estimates');
        $pageIcon = 'icon-people';
        $estimate = Estimate::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $company = Company::find($estimate->company_id);
        $this->invoiceSetting = InvoiceSetting::first();

        // public url company session set.
        session(['company' => $company]);
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
        $settings = $company;
        
        $this->taxes = $taxList;
        
        $individual_tax = $estimate->total - ($estimate->sub_total + $estimate->total_tax);
        
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
        
        
        if($estimate->combine_line_items == 1) {
            
            $allItems = EstimateItem::where('estimate_id', $estimate->id)->get();
            $groupItems = getGroupItems($allItems);
            
            
            
            return view('estimate_show_combine', [
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
                'groupItems' => $groupItems
            ]);
            
            
            
        } else {
            
            return view('estimate', [
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
                'invoiceSetting' => $this->invoiceSetting
            ]);
            
        }
        
        
        
    }

    public function decline(Request $request, $id)
    {
        $estimate = Estimate::find($id);
        $estimate->status = 'declined';
        $estimate->save();

        return Reply::dataOnly(['status' => 'success']);
    }
    
    public function updateLineIteme(Request $request, $id)
    {
        if(!empty($request->lineID) && !empty($request->status)) {
            $item = EstimateItem::find($request->lineID);
            $item->status = $request->status;
            $item->save();
            
            $estimate = Estimate::find($id);
            if($estimate->combine_line_items == 1 && isset($item->group_id) && !empty($item->group_id)) {
                EstimateItem::where('group_id', ($item->group_id))->update([
                        'status' => $request->status,
                ]);
            }
            
            return Reply::dataOnly(['status' => 'success']);
        }
        return Reply::dataOnly(['error' => 'Invalid data!']);
    }
    
    

    public function acceptModal(Request $request, $id)
    {
        $item =  EstimateItem::where('estimate_id', $id)->where('status', 'pending')->first();
        
        if($item) {
            return view('update-item', ['id' => $id]);
        } else {
            return view('accept-estimate', ['id' => $id]);
        }
        
       
        
    }

    public function accept(EstimateAcceptRequest $request, $id)
    {
        DB::beginTransaction();

        $estimate = Estimate::whereRaw('md5(id) = ?', $id)->firstOrFail();
        //dd($estimate);
        $company = Company::find($estimate->company_id);
        // public url company session set.
        //session(['company' => $company]);
        
        if (!$estimate) {
            return Reply::error('you are not authorized to access this.');
        }

        $accept = new AcceptEstimate();
        $accept->full_name = $request->first_name . ' ' . $request->last_name;
        $accept->estimate_id = $estimate->id;
        $accept->email = $request->email;

        $image = $request->signature;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(32) . '.' . 'jpg';

        if (!\File::exists(public_path('user-uploads/' . 'estimate/accept'))) {
            $result = \File::makeDirectory(public_path('user-uploads/estimate/accept'), 0775, true);
        }

        \File::put(public_path() . '/user-uploads/estimate/accept/' . $imageName, base64_decode($image));

        $accept->signature = $imageName;
        $accept->save();

        $estimate->status = 'accepted';
        $estimate->save();


        // Added by BitsClan
        //$company_info = session('company_setting'); // wronh commented by SB
        $company_info = $company; // corrected by SB

        $setting = QuickbooksSettings::first();
        $adminSetting = User::where('email', ($company_info->company_email))->get()->first();

        $qbo_id = null;
        $qbo = '';

        $Line1                          = '';
        $City                           = '';
        $CountrySubDivisionCode         = '';
        $PostalCode                     = '';
        $Ship_Line1                     = '';
        $Ship_City                      = '';
        $ship_CountrySubDivisionCode    = '';
        $ship_PostalCode                = '';

        if(!empty($setting->client_secret) && !empty($setting->client_id)){

            if(!empty($adminSetting->access_token) &&  !empty($adminSetting->refresh_token)){
                
                try {
                    
                    $quickbook = DataService::Configure(array(
                        'auth_mode' => 'oauth2',
                        'ClientID' => $setting->client_id,
                        'ClientSecret' => $setting->client_secret,
                        'accessTokenKey' =>  $adminSetting->access_token,
                        'refreshTokenKey' => $adminSetting->refresh_token,
                        'QBORealmID' => $adminSetting->realmid,
                        'baseUrl' => 'https://quickbooks.api.intuit.com'
                    ));


                    $OAuth2LoginHelper = $quickbook->getOAuth2LoginHelper();

                    $accessToken = $OAuth2LoginHelper->refreshToken();
                    $error = $OAuth2LoginHelper->getLastError();
                    $quickbook->updateOAuth2Token($accessToken);


                    $quickbook->throwExceptionOnError(true);
                    $CompanyInfo = $quickbook->getCompanyInfo();
                    $nameOfCompany = $CompanyInfo->CompanyName;


                    User::where('email', ($company_info->company_email))->update([
                        'refresh_token' => $accessToken->getRefreshToken(),
                        'access_token' => $accessToken->getAccessToken()
                    ]);

                    $qbo =  $quickbook;
                    
                } catch (\Exception $e) {}

            }
        }
        $quickbook_items = array();
        $items_array = array();
        
        
        if(!empty($qbo)){

            foreach ($estimate->items as $key => $item) :
                if (!is_null($item)) {
                    if($item->status == 'approved') {
                        
                        $taxable = false;
                        if($item->taxes) {
                            $taxable = true;
                        }
                        

                        $item_detail = Product::where('name', $item->item_name)->first();
                        $invoice_item_detail = InvoiceItems::where('item_name', $item->item_name)->first();
                        $estimate_detail = EstimateItem::where('item_name', $item->item_name)->first();


                        if(!empty($item_detail)){
                            if(!empty($item_detail->qbo_id)){
                                $item_qbo_id = $item_detail->qbo_id;
                            }else{

                            try {
                                
                                $dateTime = new \DateTime('NOW');
                                $Items = Item::create([
                                    "Name" => $item->item_name,
                                    "Description" => $item->item_summary ? $item->item_summary : '',
                                    "Active" => true,
                                    "FullyQualifiedName" => $item->item_name,
                                    "Taxable" => $taxable,
                                    "UnitPrice" => round($item->amount, 2),
                                    "Type" => "NonInventory",
                                    "IncomeAccountRef"=> [
                                        "name" => "Sales - Company Service", 
                                        "value" => $adminSetting->income_account
                                    ],
                                    "PurchaseDesc"=> $item->item_summary ? $item->item_summary : '',
                                    "PurchaseCost"=> round($item->amount, 2),
                                    "TrackQtyOnHand" => false,
                                    "InvStartDate"=> $dateTime
                                ]);

                                $resultingItemObj = $qbo->Add($Items);
                                //$error =  $qbo->getLastError();
//                                if($error){
//                                    //return Reply::error(__($error->getResponseBody()));
//                                }
                                
                            } catch (\Exception $e) {}
                                
                                $item_qbo_id  = 0;
                                if(isset($resultingItemObj->Id)) {
                                    
                                    $item_qbo_id = $resultingItemObj->Id;
                                    $item_detail->qbo_id =  $item_qbo_id;
                                    
                                }
                                
                                $item_detail->save();
                            }

                            $item_to_be_pushed = array('Description' => $item->item_summary ? $item->item_summary : '',"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $item->quantity,'UnitPrice' => round($item->amount, 2),'ItemRef' => array('value' => $item_qbo_id, 'name' => $item->item_name)),"LineNum"=> $key+1, "Amount"=> $item->quantity*round($item->amount, 2));

                            array_push($quickbook_items, $item_to_be_pushed);

                            $item_arr = array(
                                'item_name' => $item->item_name,
                                'item_summary' => $item->item_summary ? $item->item_summary : '',
                                'type' => 'item', 
                                'quantity' => $item->quantity,
                                'unit_price' =>   round($item->amount, 2),
                                'amount' => round($item->amount, 2),
                                'taxes' => $item->taxes,
                                'group_id' => $item->group_id,
                                'qbo_id' =>  $item_qbo_id,
                                'picture' => $item->picture,
                                'product_id' => $item->product_id,
                                'markup' => $item->markup,
                                'markup_fix' => $item->markup_fix,
                                'invoice_item_type' => $item->invoice_item_type,
                                'sale_price' => $item->sale_price ? round($item->sale_price,2) : '0.00',
                                'shipping_price' => $item->shipping_price ? round($item->shipping_price,2) : '0.00'
                            );

                            array_push($items_array, $item_arr);
                        }

                        elseif(!empty($invoice_item_detail)){

                            if(!empty($invoice_item_detail->qbo_id)){
                                $invoices_item_qbo = $invoice_item_detail->qbo_id;
                            }else{

                                try {
                                    $dateTime = new \DateTime('NOW');
                                    $Items = Item::create([
                                        "Name" => $item->item_name,
                                        "Description" => $item->item_summary ? $item->item_summary : '',
                                        "Active" => true,
                                        "FullyQualifiedName" => $item->item_name,
                                        "Taxable" => $taxable,
                                        "UnitPrice" => round($item->amount, 2),
                                        "Type" => "NonInventory",
                                        "IncomeAccountRef"=> [
                                            "name" => "Sales - Company Service", 
                                            "value" => $adminSetting->income_account
                                        ],
                                        "PurchaseDesc"=> $item->item_summary ? $item->item_summary : '',
                                        "PurchaseCost"=> round($item->amount, 2),
                                        "TrackQtyOnHand" => false,
                                        "InvStartDate"=> $dateTime
                                    ]);

                                    $resultingItemObj = $qbo->Add($Items);
    //                                $error =  $qbo->getLastError();
    //                                if($error){
    //                                    //return Reply::error(__($error->getResponseBody()));
    //                                }
                                } catch (\Exception $e) {}
                                
                                $invoices_item_qbo = 0;
                                
                                if(isset($resultingItemObj->Id)) {
                                    $invoices_item_qbo = $resultingItemObj->Id;
                                    $invoice_item_detail->qbo_id =  $invoices_item_qbo;
                                }
                               
                                $invoice_item_detail->save();
                            }

                            $item_to_be_pushed = array('Description' => $item->item_summary ? $item->item_summary : '',"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $item->quantity,'UnitPrice' => round($item->amount, 2),'ItemRef' => array('value' => $invoices_item_qbo, 'name' => $item->item_name)),"LineNum"=> $key+1, "Amount"=> $item->quantity*round($item->amount, 2));

                            array_push($quickbook_items, $item_to_be_pushed);

                            $item_arr = array(
                                'item_name' => $item->item_name,
                                'item_summary' => $item->item_summary ? $item->item_summary : '',
                                'type' => 'item', 
                                'quantity' => $item->quantity,
                                'unit_price' =>   round($item->amount, 2),
                                'amount' => round($item->amount, 2),
                                'taxes' => $item->taxes,
                                'group_id' => $item->group_id,
                                'qbo_id' =>  $invoices_item_qbo,
                                'picture' => $item->picture,
                                'product_id' => $item->product_id,
                                'markup' => $item->markup,
                                'markup_fix' => $item->markup_fix,
                                'invoice_item_type' => $item->invoice_item_type,
                                'sale_price' => $item->sale_price ? round($item->sale_price,2) : '0.00',
                                'shipping_price' => $item->shipping_price ? round($item->shipping_price,2) : '0.00'
                            );

                            array_push($items_array, $item_arr);
                        }

                        elseif (!empty($estimate_detail)) {

                            if(!empty($estimate_detail->qbo_id)){
                                $item_qbo_id = $estimate_detail->qbo_id;
                            }else{
                            try {
                                $dateTime = new \DateTime('NOW');
                                $Items = Item::create([
                                    "Name" => $item->item_name,
                                    "Description" => $item->item_summary ? $item->item_summary : '',
                                    "Active" => true,
                                    "FullyQualifiedName" => $item->item_name,
                                    "Taxable" => $taxable,
                                    "UnitPrice" => round($item->amount, 2),
                                    "Type" => "NonInventory",
                                    "IncomeAccountRef"=> [
                                        "name" => "Sales - Company Service", 
                                        "value" => $adminSetting->income_account
                                    ],
                                    "PurchaseDesc"=> $item->item_summary ? $item->item_summary : '',
                                    "PurchaseCost"=> round($item->amount, 2),
                                    "TrackQtyOnHand" => false,
                                    "InvStartDate"=> $dateTime
                                ]);

                                $resultingItemObj = $qbo->Add($Items);
                                
                                } catch (\Exception $e) {}
//                                $error =  $qbo->getLastError();
//                                if($error){
//                                    //return Reply::error(__($error->getResponseBody()));
//                                }       
                                
                                $product_qbo_id = 0;
                                $item_qbo_id = 0;
                                
                                if(isset($resultingItemObj->Id)) {
                                    $product_qbo_id = $resultingItemObj->Id;
                                    $estimate_detail->qbo_id = $product_qbo_id;
                                    $item_qbo_id = $resultingItemObj->Id;
                                }
                                
                                $estimate_detail->save();

                                
                            }

                            $item_to_be_pushed = array('Description' => $item->item_summary ? $item->item_summary : '',"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $item->quantity,'UnitPrice' => round($item->amount, 2),'ItemRef' => array('value' => $item_qbo_id, 'name' => $item->item_name)),"LineNum"=> $key+1, "Amount"=> $item->quantity*round($item->amount, 2));

                            array_push($quickbook_items, $item_to_be_pushed);

                            $item_arr = array(
                                'item_name' => $item->item_name,
                                'item_summary' => $item->item_summary ? $item->item_summary : '',
                                'type' => 'item', 
                                'quantity' => $item->quantity,
                                'unit_price' =>   round($item->amount, 2),
                                'amount' => round($item->amount, 2),
                                'taxes' => $item->taxes,
                                'group_id' => $item->group_id,
                                'qbo_id' =>  $item_qbo_id,
                                'picture' => $item->picture,
                                'product_id' => $item->product_id,
                                'markup' => $item->markup,
                                'markup_fix' => $item->markup_fix,
                                'invoice_item_type' => $item->invoice_item_type,
                                'sale_price' => $item->sale_price ? round($item->sale_price,2) : '0.00',
                                'shipping_price' => $item->shipping_price ? round($item->shipping_price,2) : '0.00'
                            );

                            array_push($items_array, $item_arr);
                        }

                        else{

                        try {
                            
                            $dateTime = new \DateTime('NOW');
                            $Items = Item::create([
                                "Name" => $item->item_name,
                                "Description" => $item->item_summary ? $item->item_summary : '',
                                "Active" => true,
                                "FullyQualifiedName" => $item->item_name,
                                "Taxable" => $taxable,
                                "UnitPrice" => round($item->amount, 2),
                                "Type" => "NonInventory",
                                "IncomeAccountRef"=> [
                                    "name" => "Sales - Company Service", 
                                    "value" => $adminSetting->income_account
                                ],
                                "PurchaseDesc"=> $item->item_summary ? $item->item_summary : '',
                                "PurchaseCost"=> round($item->amount, 2),
                                "TrackQtyOnHand" => false,
                                "InvStartDate"=> $dateTime
                            ]);

                            $resultingItemObj = $qbo->Add($Items);
                            
                            } catch (\Exception $e) {}
//                            $error =  $qbo->getLastError();
//                            if($error){
//                                //return Reply::error(__($error->getResponseBody()));
//                            }
                            
                            $product_qbo_id = 0; 
                            if(isset($resultingItemObj->Id)) {
                                 $product_qbo_id = $resultingItemObj->Id;
                            }

                            $item_to_be_pushed = array('Description' => $item->item_summary ? $item->item_summary : '',"DetailType" => "SalesItemLineDetail",'SalesItemLineDetail' => array('Qty' => $item->quantity,'UnitPrice' => round($item->amount, 2),'ItemRef' => array('value' => $product_qbo_id, 'name' => $item->item_name)),"LineNum"=> $key+1, "Amount"=> $item->quantity*round($item->amount, 2));

                            array_push($quickbook_items, $item_to_be_pushed);

                            $item_arr = array(
                                'item_name' => $item->item_name,
                                'item_summary' => $item->item_summary ? $item->item_summary : '',
                                'type' => 'item', 
                                'quantity' => $item->quantity,
                                'unit_price' =>   round($item->amount, 2),
                                'amount' => round($item->amount, 2),
                                'taxes' => $item->taxes,
                                'group_id' => $item->group_id,
                                'qbo_id' =>  $product_qbo_id,
                                'picture' => $item->picture,
                                'product_id' => $item->product_id,
                                'markup' => $item->markup,
                                'markup_fix' => $item->markup_fix,
                                'invoice_item_type' => $item->invoice_item_type,
                                'sale_price' => $item->sale_price ? round($item->sale_price,2) : '0.00',
                                'shipping_price' => $item->shipping_price ? round($item->shipping_price,2) : '0.00'
                            );

                            array_push($items_array, $item_arr);
                        }
                    }
                }
            endforeach;

            $client_qbo = ClientDetails::where('user_id', $estimate->client_id)->first();
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
                    if(isset($ship_address[1])){
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

                $resultingCustomerObj = $qbo->Add($customerObj);
                $error =  $qbo->getLastError();
                if($error){
                    // return Reply::error(__($error->getResponseBody()));
                }

                $qbo_id = 0;
                $client_qbo_id = 0;
                
                if(isset($resultingCustomerObj->Id)) {
                    $qbo_id = $resultingCustomerObj->Id;
                    $client_qbo->qbo_id = $qbo_id;
                    $client_qbo_id = $qbo_id;
                }
               
                $client_qbo->save();
                
            }


            if($client_qbo_id) {
                
                $theResourceObj = QuickbookInvoices::create([
                    "Line" => $quickbook_items,
                    "CustomerRef"=> [
                      "value"=> $client_qbo_id,
                    ],
                    "BillEmail" => [
                        "Address" => $client_email
                    ],
                    "BillEmailCc" => [
                        "Address" => $company_info->company_email
                    ]
    //                "BillEmailBcc" => [
    //                    "Address" => "v@intuit.com"
    //                ]
                ]);

                $resultingObj = $qbo->Add($theResourceObj);
                $error =  $qbo->getLastError();
                if($error){
                   // return Reply::error(__($error->getResponseBody()));
                }
                
            }
            
            $qbo_id = 0;
            
            if(isset($resultingObj->Id)) {
                $qbo_id = $resultingObj->Id;
            }

            
        }

        // End Here
        
        
        $invoiceSetting = InvoiceSetting::first();
        
        if ($invoiceSetting->estimate_to_invoice == 'yes') {


            $invoice = new Invoice();

            $invoice->invoice_number = Invoice::count() + 1;
            $invoice->company_id = $estimate->company_id;
            $invoice->client_id = $estimate->client_id;
            $invoice->project_id = $estimate->project_id ? $estimate->project_id : null;
            $invoice->issue_date = Carbon::now()->format('Y-m-d');
            $invoice->due_date = Carbon::now()->addDays(7)->format('Y-m-d');
            $invoice->sub_total = round($estimate->sub_total, 2);
            $invoice->discount = round($estimate->discount, 2);
            $invoice->discount_type = $estimate->discount_type;
            $invoice->total = round($estimate->total, 2);
            $invoice->currency_id = $estimate->currency_id;
            $invoice->note = $estimate->note;
            $invoice->status = 'unpaid';
            $invoice->estimate_id = $estimate->id;
            $invoice->qbo_id = $qbo_id;
            $invoice->combine_line_items = $estimate->combine_line_items;
            
            $invoice->save();

            $markup_total = 0;
            $rejected_items_amount = 0;



            if (!empty($qbo)) {
                foreach ($items_array as $key => $item) :
                    if (!is_null($item)) {
                        if ($item->status == 'approved') {

                            if ($item['markup'] > 0) {
                                $markup_total += ($item['sale_price'] / ((100 + $item['markup']) / 100));
                            }

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
                                        'group_id' => $item->group_id,
                                        'qbo_id' => $item['qbo_id'],
                                        'picture' => $item['picture'],
                                        'product_id' => $item['product_id'],
                                        'markup' => $item['markup'],
                                        'markup_fix' => $item['markup_fix'],
                                        'invoice_item_type' => $item['invoice_item_type'],
                                        'sale_price' => $item['sale_price'],
                                        'shipping_price' => $item['shipping_price']
                            ]);
                        } else if($item->status == 'rejected') {
                            $rejected_items_amount += $item['amount'];
                        }
                    }
                endforeach;
            } else {

                foreach ($estimate->items as $key => $item) :
                    if (!is_null($item)) {
                        if ($item->status == 'approved') {

                            if ($item->markup > 0) {
                                $markup_total += ($item->sale_price / ((100 + $item->markup) / 100));
                            }

                            InvoiceItems::create(
                                    [
                                        'invoice_id' => $invoice->id,
                                        'item_name' => $item->item_name,
                                        'item_summary' => $item->item_summary ? $item->item_summary : '',
                                        'type' => 'item',
                                        'quantity' => $item->quantity,
                                        'unit_price' => round($item->unit_price, 2),
                                        'amount' => round($item->amount, 2),
                                        'taxes' => $item->taxes,
                                        'group_id' => $item->group_id,
                                        'picture' => $item->picture,
                                        'product_id' => $item->product_id,
                                        'markup' => $item->markup,
                                        'markup_fix' => $item->markup_fix,
                                        'invoice_item_type' => $item->invoice_item_type,
                                        'sale_price' => $item->sale_price ? round($item->sale_price, 2) : '0.00',
                                        'shipping_price' => $item->shipping_price ? round($item->shipping_price, 2) : '0.00'
                                    ]
                            );
                        }else if($item->status == 'rejected') {
                            $rejected_items_amount += $item->amount;
                        }
                    }
                endforeach;
            }
            
            // UPDATE markup
            if ($markup_total > 0 && $estimate->sub_total > 0) {
                $markup_total = $estimate->sub_total - $markup_total;
            }
            
            $inv = Invoice::findOrFail($invoice->id);
            $inv->markup_total = $markup_total;
            $inv->total = ($inv->total - $rejected_items_amount);
            $inv->sub_total = ($inv->sub_total - $rejected_items_amount);
            $inv->save();
            
            $company = Company::find($estimate->company_id);

            $objDemo = new \stdClass();
            $objDemo->Message = ' The new invoice has been genrated '.$invoice->invoice_number.' For EST#'.$estimate->estimate_number.' By '.$estimate->client->name;
            $objDemo->Subject = 'The client has been Accepted Estimate. New Invoice Generated! ['.$invoice->invoice_number.']';
            $objDemo->FromEmail = $estimate->client->email;
            $objDemo->FromName = $estimate->client->name;

            try {
                Mail::to($company->company_email)->send(new EstimateAcceptEmail($objDemo));
            } catch (\Exception $e) {
            }
        }

       
        //log search
        $this->logSearchEntry($invoice->id, 'Invoice ' . $invoice->invoice_number, 'admin.all-invoices.show', 'invoice');

        DB::commit();
        return Reply::redirect(route('front.invoice', md5($invoice->id)), 'Estimate successfully accepted.');
    }

    public function logSearchEntry($searchableId, $title, $route, $type)
    {
        $search = new UniversalSearch();
        $search->searchable_id = $searchableId;
        $search->title = $title;
        $search->route_name = $route;
        $search->module_type = $type;
        $search->save();
    }

    /* Contract */
    public function contractView(Request $request, $id)
    {
        $pageTitle = __('app.menu.contracts');
        $pageIcon = 'fa fa-file';
        $contract = Contract::whereRaw('md5(id) = ?', $id)
            ->with('client', 'contract_type', 'signature', 'discussion', 'discussion.user')->withoutGlobalScope(CompanyScope::class)
            ->firstOrFail();
        $company = Company::find($contract->company_id);
        return view('contract', ['contract' => $contract, 'global' => $company, 'pageTitle' => $pageTitle, 'pageIcon' => $pageIcon]);
    }

    public function contractDownload($id)
    {
        $this->contract = Contract::findOrFail($id);
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.contracts.contract-pdf', $this->data);

        $filename = 'contract-' . $this->contract->id;

        return $pdf->download($filename . '.pdf');
    }

    public function contractDownloadView($id)
    {
        $this->contract = Contract::findOrFail($id);
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.contracts.contract-pdf', $this->data);

        $filename = 'contract-' . $this->contract->id;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function contractSignModal($id)
    {
        $this->contract = Contract::find($id);
        return view('contracts-accept', $this->data);
    }

    public function contractSign(SignRequest $request, $id)
    {
        $this->contract = Contract::whereRaw('md5(id) = ?', $id)->firstOrFail();

        if (!$this->contract) {
            return Reply::error('you are not authorized to access this.');
        }

        $ip_address = \Request::ip();
        $sign = new ContractSign();
        $sign->full_name = $request->first_name . ' ' . $request->last_name;
        $sign->contract_id = $this->contract->id;
        $sign->email = $request->email;
        $sign->ip_address = $ip_address;

        $image = $request->signature;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(32) . '.' . 'jpg';

        if (!\File::exists(public_path('user-uploads/' . 'contract/sign'))) {
            $result = \File::makeDirectory(public_path('user-uploads/contract/sign'), 0775, true);
        }

        \File::put(public_path() . '/user-uploads/contract/sign/' . $imageName, base64_decode($image));

        $sign->signature = $imageName;
        $sign->save();

        $allAdmins =  User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', 'admin')
            ->where('users.company_id', $this->contract->company_id)
            ->get();

        Notification::send($allAdmins, new ContractSigned($this->contract, $sign));

        return Reply::redirect(route('front.contract.show', md5($this->contract->id)));
    }

    public function estimateDomPdfObjectForDownload($id)
    {
        $estimate = Estimate::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $company = Company::find($estimate->company_id);
        $invoiceSetting = InvoiceSetting::first();

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
                $tax = EstimateItem::taxbyid($tax)->first();
                if ($tax) {
                    if (!isset($taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'])) {
                        if($invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = ($tax->rate_percent / 100) * ($item->amount-$item->shipping_price);
                        } else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = ($tax->rate_percent / 100) * $item->amount;
                        }
                        
                    } else {
                        if($invoiceSetting->shipping_taxed == 'no'){
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + (($tax->rate_percent / 100) * ($item->amount-$item->shipping_price));
                        } else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + (($tax->rate_percent / 100) * $item->amount);
                        }
                        
                    }
                }
            }
        }

        $taxes = $taxList;

        //        return $this->invoice->project->client->client[0]->address;
        $settings = $company;

        $pdf = app('dompdf.wrapper');
        
        $this->taxes = $taxList;
        
        $individual_tax = $estimate->total - ($estimate->sub_total + $estimate->total_tax);
        
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
        
        
        if($estimate->combine_line_items == 1) {
            
            $allItems = EstimateItem::where('estimate_id', $estimate->id)->get();
            $groupItems = getGroupItems($allItems);
            
            $pdf->loadView('admin.estimates.estimate-combine-pdf', [
                'estimate' => $estimate,
                'taxes' => $taxes,
                'individual_tax' => $individual_tax,
                'individual_tax_name' => $individual_tax_name,
                'settings' => $settings,
                'discount' => $discount,
                'setting' => $settings,
                'global' => $settings,
                'companyName' => $settings->company_name,
                'company' => $company,
                'invoiceSetting' => $invoiceSetting,
                'groupItems' => $groupItems
             ]);
            
        } else {
        
        
            $pdf->loadView('admin.estimates.estimate-pdf', [
                'estimate' => $estimate,
                'taxes' => $taxes,
                'individual_tax' => $individual_tax,
                'individual_tax_name' => $individual_tax_name,
                'settings' => $settings,
                'discount' => $discount,
                'setting' => $settings,
                'global' => $settings,
                'companyName' => $settings->company_name,
                'company' => $company,
                'invoiceSetting' => $invoiceSetting
            ]);
        }
        
        
        
        $filename = 'estimate-' . $estimate->id;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function estimateDownload($id)
    {
        $pdfOption = $this->estimateDomPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return $pdf->download($filename . '.pdf');
    }
    
    public function backSuperAsAdmin() {
        if (session()->get('super_admin_id')) {
            $superuser = User::withoutGlobalScope(CompanyScope::class)->where('id', session()->get('super_admin_id'))->where('super_admin', '1')->first();
            if ($superuser) {
                session()->forget('super_admin_id');
                $this->guard()->logout();
                session()->invalidate();
                \Auth::loginUsingId($superuser->id, true);
                return redirect(route('super-admin.companies.index'));
                //return Reply::redirect(route('super-admin.dashboard'), 'Return back to Super Admin successfully.');
            }
        }
        return redirect(route('admin.dashboard'));
        //return Reply::redirectWithError(route('admin.dashboard'), 'Return back to Super Admin failed');
    }

    public function customlogout(Request $request)
    {
        $user = auth()->user();
        $this->guard()->logout();
        //\Auth::logout();
        // added by sb
        $user_up = User::find($user->id);
        $user_up->session_id = null;
        $user_up->save();
        // added by sb end

        $request->session()->invalidate();
        if (module_enabled('Subdomain')) {
            if ($user->super_admin == 1) {
                return $this->loggedOut($request) ?: redirect(route('front.super-admin-login'));
            }
        }

        return $this->loggedOut($request) ?: redirect('/login');
    }
}
