<?php

namespace App\Http\Controllers\Member;

use App\ClientDetails;
use App\Helper\Reply;
use App\Http\Requests\Admin\Client\StoreClientRequest;
use App\Http\Requests\Admin\Client\UpdateClientRequest;
use App\Invoice;
use App\Lead;
use App\Notifications\NewUser;
use App\Role;
use App\Scopes\CompanyScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

// bitsclan code here

use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use App\QuickbooksSettings;

class MemberClientsController extends MemberBaseController
{
    //bitsclan code here
        protected $setting = '';
        protected $envoirment = '';
        protected $quickbook = '';
    //code end here
        
    public function __construct()
    {
        parent::__construct();
        
         
        $this->pageTitle = 'app.menu.clients';
        $this->pageIcon = 'icon-people';

        $this->middleware(function ($request, $next) {
            if (!in_array('clients', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });

               //bitsclan code here
        $this->setting = QuickbooksSettings::first(); 

        if($this->setting->baseurl == 1){
            $this->envoirment = 'Development';
        }else if($this->setting->baseurl == 2){
            $this->envoirment = 'Production';
        }

        if(!empty($this->setting->access_token)){
            $this->quickbook = DataService::Configure(array(
                'auth_mode' => 'oauth2',
                'ClientID' => $this->setting->client_id,
                'ClientSecret' => $this->setting->client_secret,
                'accessTokenKey' =>  $this->setting->access_token,
                'refreshTokenKey' => $this->setting->refresh_token,
                'QBORealmID' => $this->setting->realmid,
                'baseUrl' => $this->envoirment
            ));

            $OAuth2LoginHelper = $this->quickbook->getOAuth2LoginHelper();
            $accessToken = $OAuth2LoginHelper->refreshToken();
            $error = $OAuth2LoginHelper->getLastError();
            $this->quickbook->updateOAuth2Token($accessToken);

            QuickbooksSettings::where('id', $this->setting->id)->update([
                'refresh_token' => $accessToken->getRefreshToken(),
                'access_token' => $accessToken->getAccessToken()
            ]);
        }

        //code end here
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$this->user->can('view_clients')) {
            abort(403);
        }
        
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/clients'));
        $this->clients = User::allClients();
        
        $this->totalClients = count($this->clients);
        $this->totalRecords = $this->totalClients;

        return view('member.clients.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($leadID = null)
    {
        if (!$this->user->can('add_clients')) {
            abort(403);
        }

        if ($leadID) {
            $this->leadDetail = Lead::findOrFail($leadID);
        }

        $client = new ClientDetails();
        $this->fields = $client->getCustomFieldGroupsWithFields()->fields;
        return view('member.clients.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        $existing_user = User::withoutGlobalScope(CompanyScope::class)->select('id', 'email')->where('email', $request->input('email'))->first();

        // if no user found create new user with random password
        if (!$existing_user) {
            $password = str_random(8);
            // create new user
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($password);
            $user->mobile = $request->input('mobile');

            $user->save();

            // attach role
            $role = Role::where('name', 'client')->first();
            $user->attachRole($role->id);

            if ($request->has('lead')) {
                $lead = Lead::findOrFail($request->lead);
                $lead->client_id = $user->id;
                $lead->save();

                //return Reply::redirect(route('admin.leads.index'), __('messages.leadClientChangeSuccess'));
            }
        }

        $existing_client_count = ClientDetails::select('id', 'email', 'company_id')
            ->where(
                [
                    'email' => $request->input('email')
                ]
            )->count();

            if ($existing_client_count === 0) {

    
            //bisclan code here

            $qbo_id                         = '';
            $qbo_bill_addr_id               = '';
            $qbo_ship_addr_id               = '';
            $Line1                          = '';
            $City                           = '';
            $CountrySubDivisionCode         = '';
            $PostalCode                     = '';
            $Ship_Line1                     = '';
            $Ship_City                      = '';
            $ship_CountrySubDivisionCode    = '';
            $ship_PostalCode                = '';
            
            if(!empty($this->quickbook))
            {
                if($request->has('address'))
                {
                    $address = preg_split('/\r\n|\r|\n/', $request->address); 
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
                }

                if($request->has('shipping_address'))
                {   
                    $ship_address = preg_split('/\r\n|\r|\n/', $request->shipping_address);
                    $Ship_Line1 = isset($address[0]) ? $address[0] : ''; 
                    if(isset($address[1])){
                        $ship_address2 =  explode(',',trim($ship_address[1]));
                        $Ship_City = isset($ship_address2[0]) ? $ship_address2[0] : '';
                    }
                    if(isset($address2[1])){
                        $ship_address3 =  explode(' ',trim($ship_address2[1]));
                    }
                    $ship_CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
                    $ship_PostalCode = isset($address3[1]) ? $address3[1] : '';
                }

                $customerObj = Customer::create([
                    "BillAddr" => [
                        "Line1"=>  $Line1,
                        "City"=>  $City,
                        "CountrySubDivisionCode"=>  $CountrySubDivisionCode,
                        "PostalCode"=>  $PostalCode,
                    ],

                    "ShipAddr" => [
                        "Line1"=>  $Ship_Line1,
                        "City"=>  $Ship_City,
                        "CountrySubDivisionCode"=>  $ship_CountrySubDivisionCode,
                        "PostalCode"=>  $ship_PostalCode,
                    ],


                    "GivenName"=>  $request->input('name'),
                    "FullyQualifiedName"=>  $request->input('name'),
                    "CompanyName"=>  $request->company_name,
                    "DisplayName"=>  $request->input('name'),
                    "PrimaryPhone"=>  [
                        "FreeFormNumber"=>  $request->input('mobile')
                    ],
                    "PrimaryEmailAddr"=>  [
                        "Address" => $request->input('email')
                    ]
                ]);

                $resultingCustomerObj = $this->quickbook->Add($customerObj);
                $error =  $this->quickbook->getLastError();
                if($error){
                    return Reply::error(__($error->getOAuthHelperError()));
                }   

                $qbo_id = $resultingCustomerObj->Id;
                $qbo_bill_addr_id = $resultingCustomerObj->BillAddr->Id;
                $qbo_ship_addr_id = $resultingCustomerObj->ShipAddr->Id;


             }
            // code end here



            $client = new ClientDetails();
            $client->user_id = $existing_user ? $existing_user->id : $user->id;
            $client->name = $request->input('name');
            $client->email = $request->input('email');
            $client->mobile = $request->input('mobile');
            $client->company_name = $request->company_name;
            $client->address = $request->address;
            $client->website = $request->website;
            $client->note = $request->note;
            
            $client->skype = $request->skype ? $request->skype: null;
            $client->facebook = $request->facebook ? $request->facebook: null;
            $client->twitter = $request->twitter ? $request->twitter : null;
            $client->linkedin = $request->linkedin ? $request->linkedin : null;
            $client->gst_number = $request->gst_number;
            $client->shipping_address = $request->shipping_address;
            $client->email_notifications = $request->email_notifications;

            //bitsclan code here
            $client->qbo_id = $qbo_id;
            $client->qbo_bill_addr_id = $qbo_bill_addr_id;
            $client->qbo_ship_addr_id = $qbo_ship_addr_id;
            // code end here
            
            $client->tags = json_encode(array());
            if($request->tags) {
                $client->tags =   json_encode(array_values(array_unique($request->tags)));
            }

            $client->save();

            // attach role
            if ($existing_user) {
                $role = Role::where('name', 'client')->where('company_id', $client->company_id)->first();
                $existing_user->attachRole($role->id);
            }

            // To add custom fields data
            if ($request->get('custom_fields_data')) {
                $client->updateCustomFieldData($request->get('custom_fields_data'));
            }

            // log search
            if (!is_null($client->company_name)) {
                $user_id = $existing_user ? $existing_user->id : $user->id;
                $this->logSearchEntry($user_id, $client->company_name, 'admin.clients.edit', 'client');
            }
            //log search
            $this->logSearchEntry($client->id, $request->name, 'admin.clients.edit', 'client');
            $this->logSearchEntry($client->id, $request->email, 'admin.clients.edit', 'client');
        } else {
            return Reply::error('Provided email is already registered. Try with different email.');
        }

        //if (!$existing_user && $this->emailSetting[0]->send_email == 'yes' && $request->sendMail == 'yes') {
        if (!$existing_user && $request->sendMail == 'yes') {
            //send welcome email notification
            $user->notify(new NewUser($password));
        }
        
        $this->mixPanelTrackEvent('client_created', array('page_path' => '/member/clients'));

        return Reply::redirect(route('member.clients.index'), __('messages.clientAdded'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$this->user->can('view_clients')) {
            abort(403);
        }

        $this->client = User::findClient($id);
         $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();
        $this->clientStats = $this->clientStats($id);
        
        if (!is_null($this->clientDetail)) {
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }
        
        return view('member.clients.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!$this->user->can('edit_clients')) {
            abort(403);
        }

       $this->userDetail = ClientDetails::join('users', 'client_details.user_id', '=', 'users.id')
            ->where('client_details.user_id', $id)
            ->select('client_details.id', 'client_details.name', 'client_details.email', 'client_details.user_id', 'client_details.mobile', 'users.status', 'users.login')
            ->first();

        $this->clientDetail = ClientDetails::where('user_id', '=', $this->userDetail->user_id)->first();
        
        $tags = $this->clientDetail->tags ? json_decode($this->clientDetail->tags) : array();
        $this->clientDetail->tags = $tags;
        
        if($tags) {
            $this->clientDetail->tags = array_values(array_unique($tags));
        }

        if (!is_null($this->clientDetail)) {
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }

        return view('member.clients.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, $id)
    {
        //$client = ClientDetails::where('user_id', '=', $id)->first();
        $client = ClientDetails::find($id);

          // if(empty($client)){
        //     $client = new ClientDetails();
        //     $client->user_id = $user->id;
        // }



        //bitsclan code here
        $qbo_id           = '';
        $qbo_bill_addr_id = '';
        $qbo_ship_addr_id = '';
        $Line1                          = '';
        $City                           = '';
        $CountrySubDivisionCode         = '';
        $PostalCode                     = '';
        $Ship_Line1                     = '';
        $Ship_City                      = '';
        $ship_CountrySubDivisionCode    = '';
        $ship_PostalCode                = '';

        if(!empty($this->quickbook)){

            if($request->has('address'))
            {
                $address = preg_split('/\r\n|\r|\n/', $request->address); 
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
            }

            if($request->has('shipping_address'))
            {   
                $ship_address = preg_split('/\r\n|\r|\n/', $request->shipping_address);
                $Ship_Line1 = isset($address[0]) ? $address[0] : ''; 
                if(isset($address[1])){
                    $ship_address2 =  explode(',',trim($ship_address[1]));
                    $Ship_City = isset($ship_address2[0]) ? $ship_address2[0] : '';
                }
                if(isset($address2[1])){
                    $ship_address3 =  explode(' ',trim($ship_address2[1]));
                }
                $ship_CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
                $ship_PostalCode = isset($address3[1]) ? $address3[1] : '';
            }

            $entities = $this->quickbook->Query("SELECT * FROM Customer where Id='".$client->qbo_id."'");
            $theCustomer = reset($entities);

            $updateCustomer = Customer::update($theCustomer, [
                          "BillAddr" => [
                        "Line1"=>  $Line1,
                        "City"=>  $City,
                        "CountrySubDivisionCode"=>  $CountrySubDivisionCode,
                        "PostalCode"=>  $PostalCode,
                    ],

                    "ShipAddr" => [
                        "Line1"=>  $Ship_Line1,
                        "City"=>  $Ship_City,
                        "CountrySubDivisionCode"=>  $ship_CountrySubDivisionCode,
                        "PostalCode"=>  $ship_PostalCode,
                    ],


                    "GivenName"=>  $request->input('name'),
                    "FullyQualifiedName"=>  $request->input('name'),
                    "CompanyName"=>  $request->company_name,
                    "DisplayName"=>  $request->input('name'),
                    "PrimaryPhone"=>  [
                        "FreeFormNumber"=>  $request->input('mobile')
                    ],
                    "PrimaryEmailAddr"=>  [
                        "Address" => $request->input('email')
                    ]
            ]);
            $resultingCustomerUpdatedObj = $this->quickbook->Update($updateCustomer);
            $error =  $this->quickbook->getLastError();
            if($error){
                return Reply::error(__($error->getOAuthHelperError()));
            }   

            $qbo_id = $resultingCustomerUpdatedObj->Id;
            $qbo_bill_addr_id = $resultingCustomerUpdatedObj->BillAddr->Id;
            $qbo_ship_addr_id = $resultingCustomerUpdatedObj->ShipAddr->Id;

        }

        //code end here


        $client->company_name = $request->company_name;
        $client->address = $request->address;
        $client->name = $request->input('name');
        $client->email = $request->input('email');
        $client->mobile = $request->input('mobile');
        $client->website = $request->website;
        $client->note = $request->note;
        
        $client->skype = $request->skype ? $request->skype : null;
        $client->facebook = $request->facebook ? $request->facebook: null;
        $client->twitter = $request->twitter ? $request->twitter : null;
        $client->linkedin = $request->linkedin ? $request->linkedin: null;
        
        $client->gst_number = $request->gst_number;
        $client->shipping_address = $request->shipping_address;
        $client->email_notifications = $request->email_notifications;
        
        //bitsclan code here
        $client->qbo_id = $qbo_id;
        $client->qbo_bill_addr_id = $qbo_bill_addr_id;
        $client->qbo_ship_addr_id = $qbo_ship_addr_id;
        //code end here
        
        
        $client->tags = json_encode(array());
        if($request->tags) {
            $client->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        
        $client->save();

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $client->updateCustomFieldData($request->get('custom_fields_data'));
        }
        
        $user = User::withoutGlobalScopes(['active', CompanyScope::class])->findOrFail($client->user_id);
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }
        
        $user->status = $request->input('status');
         $user->save();

        return Reply::redirect(route('member.clients.index'), __('messages.clientUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        return Reply::success(__('messages.clientDeleted'));
    }

    public function data(Request $request)
    {
        $users = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'client_details.name', 'client_details.company_name', 'client_details.email', 'client_details.tags', 'users.created_at')
            ->where('roles.name', 'client')
            ->groupBy('users.id');
        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $users = $users->where(DB::raw('DATE(users.`created_at`)'), '>=', $request->startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $users = $users->where(DB::raw('DATE(users.`created_at`)'), '<=', $request->endDate);
        }
        if ($request->client != 'all' && $request->client != '') {
            $users = $users->where('users.id', $request->client);
        }

        $users = $users->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '';
                if ($this->user->can('edit_clients')) {
                    $action .= '<a href="' . route('member.clients.edit', [$row->id]) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if ($this->user->can('view_clients')) {
                    $action .= ' <a href="' . route('member.clients.projects', [$row->id]) . '" class="btn btn-success btn-circle"
                      data-toggle="tooltip" data-original-title="View Client Details"><i class="fa fa-search" aria-hidden="true"></i></a>';
                }

                if ($this->user->can('delete_clients')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }

                return $action;
            })
            ->editColumn(
                'name',
                function ($row) {
                    return '<a href="' . route('member.clients.projects', $row->id) . '">' . ucfirst($row->name) . '</a>';
                }
            )
            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->editColumn(
                'status',
                function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="label label-success">' . __('app.active') . '</label>';
                    } else {
                        return '<label class="label label-danger">' . __('app.inactive') . '</label>';
                    }
                }
            )
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
            ->rawColumns(['name', 'status', 'action'])
            ->make(true);
    }

    public function showProjects($id)
    {


        $this->client = User::findClient($id);
        return view('member.clients.projects', $this->data);
    }

    public function showInvoices($id)
    {
        if (!$this->user->can('view_invoices')) {
            abort(403);
        }
        $this->client = User::findClient($id);
        $this->invoices = Invoice::leftJoin('projects', 'projects.id', '=', 'invoices.project_id')
            ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
            ->join('users', 'users.id', '=', 'projects.client_id')
            ->select('invoices.invoice_number', 'invoices.total', 'currencies.currency_symbol', 'invoices.issue_date', 'invoices.id')
            ->where(function ($query) use ($id) {
                $query->where('projects.client_id', $id)
                    ->orWhere('invoices.client_id', $id);
            })
            ->get();

        return view('member.clients.invoices', $this->data);
    }

    public function export()
    {
        $rows = User::leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.mobile',
                'client_details.company_name',
                'client_details.address',
                'client_details.website',
                'users.created_at'
            )
            ->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Name', 'Email', 'Mobile', 'Company Name', 'Address', 'Website', 'Created at'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($rows as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('clients', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Clients');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('clients file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));
                });
            });
        })->download('xlsx');
    }
    public function clientStats($id)
    {
        return DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.client_id = ' . $id . ' and projects.company_id = ' . company()->id . ') as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` left join projects on projects.id=invoices.project_id WHERE invoices.status != "paid" and invoices.status != "canceled" and (projects.client_id = ' . $id . ' or invoices.client_id = ' . $id . ') and invoices.company_id = ' . company()->id . ') as totalUnpaidInvoices'),
                DB::raw('(select sum(payments.amount) from `payments` left join projects on projects.id=payments.project_id WHERE payments.status = "complete" and projects.client_id = ' . $id . ' and payments.company_id = ' . company()->id . ') as projectPayments'),
                DB::raw('(select sum(payments.amount) from `payments` inner join invoices on invoices.id=payments.invoice_id  WHERE payments.status = "complete" and invoices.client_id = ' . $id . ' and payments.company_id = ' . company()->id . ') as invoicePayments'),
                DB::raw('(select count(contracts.id) from `contracts` WHERE contracts.client_id = ' . $id . ' and contracts.company_id = ' . company()->id . ') as totalContracts')
            )
            ->first();
    }
    
}
