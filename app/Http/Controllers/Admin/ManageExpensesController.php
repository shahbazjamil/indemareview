<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\DataTables\Admin\ExpensesDataTable;
use App\Expense;
use App\Helper\Reply;
use App\Http\Requests\Expenses\StoreExpense;
use App\Invoice;
use App\Payment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\Files;
use App\ClientVendorDetails;

// bitsclan code start here
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Purchase;
use App\QuickbooksSettings;
// bitsclan code end here

class ManageExpensesController extends AdminBaseController
{
    // bitsclan code start here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    // bitsclan code end here
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.expenses';
        $this->pageIcon = 'ti-shopping-cart';
        $this->middleware(function ($request, $next) {
            if (!in_array('expenses', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });

        // bitsclan code end here
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

    public function index(ExpensesDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/finance/expenses'));
        
        $this->employees = User::allEmployees();
        return $dataTable->render('admin.expenses.index', $this->data);
    }


    //Created By Adil
    public function dataSource()
    {
        return DB::table('vendors')->select('id', 'name')->get();
    }

    public function create()
    {
        $this->currencies = Currency::all();
        $this->employees = User::allEmployees();

        $employees = $this->employees->toArray();
        foreach ($employees as $key => $employee) {
            $user_arr = [
                'id' => $employee['id'],
                'name' => $employee['name']
            ];
            $employee = array_add($employee, 'user', $user_arr);
            $employees[$key] = $employee;
        }
        foreach ($this->employees as $employee) {
            $filtered_array = array_filter($employees, function ($item) use ($employee) {
                return $item['user']['id'] == $employee->id;
            });
            $projects = [];

            foreach ($employee->member as $member) {
                if (!is_null($member->project)) {
                    array_push($projects, $member->project()->select('id', 'project_name')->first()->toArray());
                }
            }
            
            $keys = array_column($projects, 'project_name');
            array_multisort($keys, SORT_ASC, $projects);
            
            $employees[key($filtered_array)]['user'] = array_add(reset($filtered_array)['user'], 'projects', $projects);
        }
        $this->employees = $employees;
        //Added by Aqeel

//        $this->vendorInvoices =  Invoice::all()->where('status' ,'=', 'paid')->get();
        //$vendorInvoices =  Payment::where('status' ,'=', 'complete')->where('invoice_type','=','vendor')->get();
        
        $vendorInvoices = ClientVendorDetails::all();
        $this->__set('vendorInvoices', $vendorInvoices);



        return view('admin.expenses.create', $this->data);
    }

    public function store(StoreExpense $request)
    {

        // bitsclan code start here
        $qbo_id = '';
        $this->quickbook = $this->QuickbookSettings();
        if($this->quickbook){
            try {
            $adminSetting = User::where('email', ($this->user->email))->first();
            $theResourceObj = Purchase::create([
                "TotalAmt"=> round($request->price, 2),
                "PaymentType"=> "Cash", 
                "Line"=> array(
                    "DetailType"=> "AccountBasedExpenseLineDetail", 
                    "Amount"=> round($request->price, 2),
                    "AccountBasedExpenseLineDetail"=> [
                        "AccountRef"=> [
                            "value"=> $adminSetting->income_account
                        ], 
                        "BillableStatus"=> "NotBillable"
                    ]
                ),
                "AccountRef"=> [
                    "value"=> $adminSetting->bank_account
                ]
            ]);

            $resultingObj = $this->quickbook->Add($theResourceObj);  
            $error =  $this->quickbook->getLastError();
            if($error){
                //return Reply::error(__($error->getOAuthHelperError()));
            }
            $qbo_id = isset($resultingObj->Id) ? $resultingObj->Id : '';
            
             } catch (\Exception $e) {
                
            }
        }
        // bitsclan code start here
        $expense = new Expense();
        $expense->item_name = $request->item_name;
        $expense->purchase_date = Carbon::createFromFormat($this->global->date_format, $request->purchase_date)->format('Y-m-d');
        $expense->purchase_from = $request->purchase_from;
        $expense->price = round($request->price, 2);
        $expense->currency_id = $request->currency_id;
        // bitsclan code start here
        $expense->qbo_id = $qbo_id;
        // bitsclan code end here
        $expense->user_id = $request->user_id;
        $expense->expenses_type = $request->expenses_type ? $request->expenses_type :null;

        if ($request->project_id > 0) {
            $expense->project_id = $request->project_id;
        }

        if ($request->hasFile('bill')) {
            //$expense->bill = Files::upload($request->bill, 'expense-invoice');
            
            $filename = Files::uploadLocalOrS3($request->bill,'expense-invoice');
            $expense->bill = $filename;
            
            //$expense->bill = $request->bill->hashName();
            //$request->bill->store('expense-invoice');
            //            dd($expense->bill);
            // $img = Image::make('user-uploads/expense-invoice/' . $expense->bill);
            // $img->resize(500, null, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $img->save();
        }

        $expense->status = 'approved';
        $expense->save();

        return Reply::redirect(route('admin.expenses.index'), __('messages.expenseSuccess'));
    }

    public function edit($id)
    {
        $this->expense = Expense::findOrFail($id);
        $this->employees = User::allEmployees();

        $employees = $this->employees->toArray();
        foreach ($employees as $key => $employee) {
            $user = User::select('id', 'name')->where('id', $employee['id'])->withoutGlobalScope('active')->first();
            $user_arr = [
                'id' => $user->id,
                'name' => $user->name
            ];
            $employee = array_add($employee, 'user', $user_arr);
            $employees[$key] = $employee;
        }
        foreach ($this->employees as $employee) {
            $filtered_array = array_filter($employees, function ($item) use ($employee) {
                return $item['user']['id'] == $employee->id;
            });
            $projects = [];

            foreach ($employee->member as $member) {
                if (!is_null($member->project)) {
                    array_push($projects, $member->project()->select('id', 'project_name')->first()->toArray());
                }
            }
            
            $keys = array_column($projects, 'project_name');
            array_multisort($keys, SORT_ASC, $projects);
            
            $employees[key($filtered_array)]['user'] = array_add(reset($filtered_array)['user'], 'projects', $projects);
        }

        $this->employees = $employees;
        $this->currencies = Currency::all();

        return view('admin.expenses.edit', $this->data);
    }

    public function update(StoreExpense $request, $id)
    {
        $expense = Expense::findOrFail($id);
        // bitsclan code start here
        $this->quickbook = $this->QuickbookSettings();
        if($this->quickbook){
            try {
            $adminSetting = User::where('email', ($this->user->email))->first();
            if(!empty($expense->qbo_id)){
                $entities = $this->quickbook->Query("SELECT * FROM Purchase where Id='".$expense->qbo_id."'");
                $thePurchase = reset($entities);
                $theResourceObj = Purchase::update($thePurchase,[
                    "TotalAmt"=> round($request->price, 2),
                    "PaymentType"=> "Cash", 
                    "Line"=> array(
                        "DetailType"=> "AccountBasedExpenseLineDetail", 
                        "Amount"=> round($request->price, 2),
                        "AccountBasedExpenseLineDetail"=> [
                            "AccountRef"=> [
                                "value"=> $adminSetting->income_account
                            ], 
                            "BillableStatus"=> "NotBillable"
                        ]
                    ),
                    "AccountRef"=> [
                        "value"=> $adminSetting->bank_account
                    ]
                ]);

                $resultingObj = $this->quickbook->update($theResourceObj);  
            }else{
                $theResourceObj = Purchase::create([
                    "TotalAmt"=> round($request->price, 2),
                    "PaymentType"=> "Cash", 
                    "Line"=> array(
                        "DetailType"=> "AccountBasedExpenseLineDetail", 
                        "Amount"=> round($request->price, 2),
                        "AccountBasedExpenseLineDetail"=> [
                            "AccountRef"=> [
                                "value"=> $adminSetting->income_account
                            ], 
                            "BillableStatus"=> "NotBillable"
                        ]
                    ),
                    "AccountRef"=> [
                        "value"=> $adminSetting->bank_account
                    ]
                ]);
                $resultingObj = $this->quickbook->Add($theResourceObj);  
            }
            
            $error =  $this->quickbook->getLastError();
            if($error){
                //return Reply::error(__($error->getOAuthHelperError()));
            }
                 $qbo_id = isset($resultingObj->Id) ? $resultingObj->Id : '';
                 
            } catch (\Exception $e) {
                
            }
        }
        // bitsclan code end here
        $expense = Expense::findOrFail($id);
        $expense->item_name = $request->item_name;
        $expense->purchase_date = Carbon::createFromFormat($this->global->date_format, $request->purchase_date)->format('Y-m-d');
        $expense->purchase_from = $request->purchase_from;
        $expense->price = round($request->price, 2);
        $expense->currency_id = $request->currency_id;
        $expense->user_id = $request->user_id;
        $expense->expenses_type = $request->expenses_type ? $request->expenses_type :null;

        if ($request->project_id > 0) {
            $expense->project_id = $request->project_id;
        } else {
            $expense->project_id = null;
        }

        if ($request->hasFile('bill')) {
            //File::delete(public_path() . '/user-uploads/expense-invoice/' . $expense->bill);
            
            $filename = Files::uploadLocalOrS3($request->bill,'expense-invoice');
            $expense->bill = $filename;
            //$expense->bill = $request->bill->hashName();
            //$request->bill->store('expense-invoice');
            
            //$expense->bill = Files::upload($request->bill, 'expense-invoice');
            
            // $img = Image::make('user-uploads/expense-invoice/' . $expense->bill);
            // $img->resize(500, null, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $img->save();
        }

        $previousStatus = $expense->status;

        $expense->status = $request->status;
        $expense->save();

        return Reply::redirect(route('admin.expenses.index'), __('messages.expenseUpdateSuccess'));
    }

    public function destroy($id)
    {
        Expense::destroy($id);

        return Reply::success(__('messages.expenseDeleted'));
    }

    public function show($id)
    {
        $this->expense = Expense::with('user')->findOrFail($id);
        return view('admin.expenses.show', $this->data);
    }


    public function changeStatus(Request $request)
    {
        $expenseId = $request->expenseId;
        $status = $request->status;
        $expense = Expense::findOrFail($expenseId);
        $expense->status = $status;
        $expense->save();
        return Reply::success(__('messages.updateSuccess'));
    }
}
