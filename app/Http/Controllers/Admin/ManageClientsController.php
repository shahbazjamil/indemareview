<?php

namespace App\Http\Controllers\Admin;

use App\AuditTrail;
use App\ClientDetails;
use App\Company;
use App\DataTables\Admin\ClientsDataTable;
use App\EmailAutomation;
use App\Events\EmailSentEvent;
use App\Helper\Reply;
use App\Http\Requests\Admin\Client\StoreClientRequest;
use App\Http\Requests\Admin\Client\UpdateClientRequest;
use App\Http\Requests\Gdpr\SaveConsentUserDataRequest;
use App\Invoice;
use App\Jobs\SendEmailJob;
use App\Lead;
use App\Mail\ClientCreated;
use App\Mail\FirstStepAutomation;
use App\Mail\LastStepAutomation;
use App\Notifications\NewUser;
use App\Observers\MailTracker;
use App\Payment;
use App\PurposeConsent;
use App\PurposeConsentUser;
use App\Role;
use App\Scopes\CompanyScope;
use App\SentEmail;
use App\UniversalSearch;
use App\User;
use App\ClientNote;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\ProjectClient;
use App\Project;


// bitsclan code start here

use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use App\QuickbooksSettings;
// bitsclan code end here

use Illuminate\Foundation\Auth\AuthenticatesUsers;

class ManageClientsController extends AdminBaseController
{

    use AuthenticatesUsers;
    // bitsclan code start here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    // bitsclan code end here

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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ClientsDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/clients'));
        
        $this->clients = User::allClients();
        $this->totalClients = count($this->clients);
        $this->totalRecords = $this->totalClients;

        // return view('admin.clients.index', $this->data);
        return $dataTable->render('admin.clients.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($leadID = null)
    {
        if ($leadID) {
            $this->leadDetail = Lead::findOrFail($leadID);
        }

        $client = new ClientDetails();
        $this->fields = $client->getCustomFieldGroupsWithFields() ? $client->getCustomFieldGroupsWithFields()->fields : [];
        return view('admin.clients.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreClientRequest $request)
    {
        $existing_user = User::withoutGlobalScope(CompanyScope::class)->select('id', 'email')->where('email', $request->input('email'))->first();
        
        
        // already user can't added again 
        if($existing_user) {
            return Reply::error('Provided email is already registered. Try with different email.');
        }

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
                //Lead::destroy($request->lead);
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

           
            // bitsclan code start here

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
            
            $this->quickbook = $this->QuickbookSettings();
            if($this->quickbook){

            try {

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

                    $BillAddr =  [
                        "Line1"=>  $Line1,
                        "City"=>  $City,
                        "CountrySubDivisionCode"=>  $CountrySubDivisionCode,
                        "PostalCode"=>  $PostalCode,
                    ];

                }

                if($request->has('shipping_address'))
                {   
                
                    $ship_address = preg_split('/\r\n|\r|\n/', $request->shipping_address);
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
                
                
                $entities = $this->quickbook->Query("SELECT * FROM Customer WHERE PrimaryEmailAddr LIKE '%".$request->input('email')."%'");
                $error =  $this->quickbook->getLastError();
                if($error){
                    //return Reply::error(__($error->getOAuthHelperError()));
                }
                if($entities) {
                    $theCustomer = reset($entities);
                    $updateCustomer = Customer::update($theCustomer, [
                        "BillAddr" => isset($BillAddr) ? $BillAddr : $Addr,
                        "ShipAddr" => isset($ShipAddr) ? $ShipAddr : $Addr,
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
                        //return Reply::error(__($error->getOAuthHelperError()));
                    }
                $qbo_id = isset($resultingCustomerUpdatedObj->Id) ? $resultingCustomerUpdatedObj->Id : '';
                    
                } else {
                    
                    $customerObj = Customer::create([
                        "BillAddr" => isset($BillAddr) ? $BillAddr : $Addr,
                        "ShipAddr" => isset($ShipAddr) ? $ShipAddr : $Addr,
                        "GivenName"=>  $request->input('name'),
                        "FullyQualifiedName"=>  $request->input('name'),
                        "CompanyName"=>  isset($request->company_name) ? $request->company_name : $request->input('name'),
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
                        //return Reply::error(__($error->getOAuthHelperError()));
                    }
                    $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
                    
                    
                }
                } catch (\Exception $e) {}
                
                
            }

       

           // bitsclan code end here

            $client = new ClientDetails();
            $client->user_id = $existing_user ? $existing_user->id : $user->id;
            $client->name = $request->input('name');
            $client->email = $request->input('email');
            $client->mobile = $request->input('mobile');
            //$client->company_name = $request->company_name;
            $client->company_name = $request->input('name');
            $client->address = $request->address;
            //$client->website = $request->website;
            $client->note = $request->note;
            
            $client->skype = $request->skype ? $request->skype: null;
            $client->facebook = $request->facebook ? $request->facebook: null;
            $client->twitter = $request->twitter ? $request->twitter : null;
            $client->linkedin = $request->linkedin ? $request->linkedin : null;
            $client->gst_number = $request->gst_number ? $request->gst_number : null;
            $client->shipping_address = $request->shipping_address;
            $client->email_notifications = $request->email_notifications;
            $client->payments_on_portal = $request->payments_on_portal;
            
            $client->sales_code = $request->sales_code;
            $client->secondary_email = $request->secondary_email;
            $client->reffered_by = $request->reffered_by;
            $client->product_default_tax = $request->product_default_tax;
            
            $client->tags = json_encode(array());
            if($request->tags) {
                $client->tags =   json_encode(array_values(array_unique($request->tags)));
            }


            // bitsclan code start here
            $client->qbo_id = $qbo_id;
            
            /// bitsclan code end here


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

        if (!$existing_user && $request->sendMail == 'yes') {
            //send welcome email notification
            $user->notify(new NewUser($password));
        }

        //restartPM2();
        $timePeriod = 0;
        $increment = 0;
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        // Automation Mail Send
        $emailAutomations = EmailAutomation::where('company_id', $companyId)->where('automation_event', EmailAutomation::CLIENT_CREATED)->get();
        if($emailAutomations) {
            $emailMasterAutomationIds = $emailAutomations->pluck('email_automation_id')->toArray();
            $getAllEmailAutomations = EmailAutomation::where('company_id', $companyId)->with(['emailTemplate', 'emailAutomationMaster'])
                                    ->whereIn('email_automation_id', $emailMasterAutomationIds)
                                    ->orderBy('step')
                                    ->get();
            $getAllEmailAutomations = $getAllEmailAutomations->toArray();
            $company =  company()->toArray();
            $companyId = !empty($company) ? $company['id'] : Auth::user()->company->id;
            storeAuditTrail(array_unique($emailMasterAutomationIds), $companyId, $user, AuditTrail::CLIENT, Carbon::now(), AuditTrail::LEFT_STEP);
            foreach ($getAllEmailAutomations as $key => $emailAutomation) {
                $keyVariable = [
                    '{{client.first.name}}'
                ];
                $value = [
                    $user->name,
                ];
                $body = str_replace($keyVariable, $value, $emailAutomation['email_template']['body']);
                $emailAutomation['email_template']['body'] = $body;
                $mailType = getMailAutomationTimePeriod($emailAutomation);
                $company =  company()->toArray();
                $companyId = !empty($company) ? $company['id'] : Auth::user()->company->id;
                $emailAutomation['company'] = null;
                if (!empty($companyId)){
                    $emailAutomation['company'] = Company::find($companyId)->toArray();
                }
                $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomation, 'companyId' => $companyId, 'type' => 'client'];
                $timePeriod = $timePeriod + intval($emailAutomation['time_period']);
                if ($mailType == 'minuteAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addMinutes($increment), AuditTrail::ENTERED_STEP);
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addMinutes($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addMinutes($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::CLIENT_CREATED) {
                            $audit = storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addMinutes($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addMinutes($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                } else if ($mailType == 'hourAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addHours($increment), AuditTrail::ENTERED_STEP);
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addHours($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addHours($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::CLIENT_CREATED) {
                            $audit =  storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addHours($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addHours($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                } else if ($mailType == 'dayAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addDays($increment), AuditTrail::ENTERED_STEP);
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addDays($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addDays($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::CLIENT_CREATED) {
                            $audit =  storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addDays($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addDays($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                } else if ($mailType == 'weekAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addWeeks($increment), AuditTrail::ENTERED_STEP);
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addWeeks($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addWeeks($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::CLIENT_CREATED) {
                            $audit =  storeAuditTrail($emailAutomation, $companyId, $user, AuditTrail::CLIENT, Carbon::now()->addWeeks($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addWeeks($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                }
            }
        }
        
        $this->mixPanelTrackEvent('contract_created', array('client_created' => '/admin/clients/create'));
        
        
        return Reply::redirect(route('admin.clients.edit', $client->id));
        //return Reply::redirect(route('admin.clients.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->client = User::findClient($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();
        $this->clientStats = $this->clientStats($id);

        if (!is_null($this->clientDetail)) {
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }
        return view('admin.clients.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->userDetail = ClientDetails::join('users', 'client_details.user_id', '=', 'users.id')
            ->where('client_details.id', $id)
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

        return view('admin.clients.edit', $this->data);
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
        $client = ClientDetails::find($id);

        // if(empty($client)){
        //     $client = new ClientDetails();
        //     $client->user_id = $user->id;
        // }

        // bitsclan code start here
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

         $this->quickbook = $this->QuickbookSettings();
            if($this->quickbook){
                try {
                
                
            if(!empty($client->qbo_id)){
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

                    $BillAddr =  [
                        "Line1"=>  $Line1,
                        "City"=>  $City,
                        "CountrySubDivisionCode"=>  $CountrySubDivisionCode,
                        "PostalCode"=>  $PostalCode,
                    ];
                }

                if($request->has('shipping_address'))
                {   
                
                    $ship_address = preg_split('/\r\n|\r|\n/', $request->shipping_address);
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

                $entities = $this->quickbook->Query("SELECT * FROM Customer where Id='".$client->qbo_id."'");
                $error =  $this->quickbook->getLastError();
                if($error){
                    //return Reply::error(__($error->getOAuthHelperError()));
                }

                if($entities) {
                    $theCustomer = reset($entities);
                    $updateCustomer = Customer::update($theCustomer, [
                        "BillAddr" => isset($BillAddr) ? $BillAddr : $Addr,
                        "ShipAddr" => isset($ShipAddr) ? $ShipAddr : $Addr,
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
                        //return Reply::error(__($error->getOAuthHelperError()));
                    }
                    $qbo_id = isset($resultingCustomerUpdatedObj->Id) ? $resultingCustomerUpdatedObj->Id : '';
                }
                
                
            }else{
                if($request->has('address')){
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

                    $BillAddr =  [
                        "Line1"=>  $Line1,
                        "City"=>  $City,
                        "CountrySubDivisionCode"=>  $CountrySubDivisionCode,
                        "PostalCode"=>  $PostalCode,
                    ];
                }

                if($request->has('shipping_address'))
                {   

                    $ship_address = preg_split('/\r\n|\r|\n/', $request->shipping_address);
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


                    "GivenName"=>  $request->input('name'),
                    "FullyQualifiedName"=>  $request->input('name'),
                    "CompanyName"=>  isset($request->company_name) ? $request->company_name : $request->input('name'),
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
                    //return Reply::error(__($error->getOAuthHelperError()));
                }

                $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
                if(!empty($request->input('address'))){
                    $qbo_bill_addr_id = isset($resultingCustomerObj->BillAddr) ? $resultingCustomerObj->BillAddr->Id : '';
                }
                if(!empty($request->input('shipping_address'))){
                    $qbo_ship_addr_id = isset($resultingCustomerObj->ShipAddr) ? $resultingCustomerObj->ShipAddr->Id : '';
                }

            }
             } catch (\Exception $e) {}
        }

            // bitsclan code end here

        $client->company_name = $request->company_name;
        $client->name = $request->input('name');
        $client->email = $request->input('email');
        $client->mobile = $request->input('mobile');
        $client->address = $request->address;
        //$client->website = $request->website;
        $client->note = $request->note;
        
        $client->skype = $request->skype ? $request->skype : null;
        $client->facebook = $request->facebook ? $request->facebook: null;
        $client->twitter = $request->twitter ? $request->twitter : null;
        $client->linkedin = $request->linkedin ? $request->linkedin: null;
        
        $client->gst_number = $request->gst_number ? $request->gst_number : null;
        $client->shipping_address = $request->shipping_address;
        $client->email_notifications = $request->email_notifications;
        $client->payments_on_portal = $request->payments_on_portal;
        
        
        $client->sales_code = $request->sales_code;
        $client->secondary_email = $request->secondary_email;
        $client->reffered_by = $request->reffered_by;
        $client->product_default_tax = $request->product_default_tax;

        // bitsclan code start here
        $client->qbo_id = $qbo_id;
        $client->qbo_bill_addr_id = $qbo_bill_addr_id;
        $client->qbo_ship_addr_id = $qbo_ship_addr_id;
        // bitsclan code end here
        
        
        $client->tags = json_encode(array());
        if($request->tags) {
            $client->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        
        $client->save();
        
        
        $user = User::where('id', '=', $client->user_id)->first();
        if($user) {
            $user->name = $request->input('name');
            $user->save();
        }
        

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $client->updateCustomFieldData($request->get('custom_fields_data'));
        }

        $user = User::withoutGlobalScopes(['active', CompanyScope::class])->findOrFail($client->user_id);
        $user->email = $request->input('email');
        
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }

        $user->status = $request->input('status');
        // $user->email = $request->input('email');
        $user->save();

        return Reply::redirect(route('admin.clients.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        $clients_count = ClientDetails::withoutGlobalScope(CompanyScope::class)->where('user_id', $id)->count();

        if ($clients_count > 1) {
            $client_builder = ClientDetails::where('user_id', $id);
            $client = $client_builder->first();

            $user_builder = User::where('id', $id);
            $user = $user_builder->first();
            if ($user) {
                $other_client = $client_builder->withoutGlobalScope(CompanyScope::class)
                    ->where('company_id', '!=', $client->company_id)
                    ->first();

                request()->request->add(['company_id' => $other_client->company_id]);

                $user->save();
            }
            $role = Role::where('name', 'client')->first();
            $user_role = $user_builder->withoutGlobalScope(CompanyScope::class)->first();
            $user_role->detachRoles([$role->id]);
            $universalSearches = UniversalSearch::where('searchable_id', $id)->where('module_type', 'client')->get();
            if ($universalSearches) {
                foreach ($universalSearches as $universalSearch) {
                    UniversalSearch::destroy($universalSearch->id);
                }
            }
            $client->delete();
        } else {
            // $client = ClientDetails::where('user_id', $id)->first();
            // $client->delete();
            $universalSearches = UniversalSearch::where('searchable_id', $id)->where('module_type', 'client')->get();
            if ($universalSearches) {
                foreach ($universalSearches as $universalSearch) {
                    UniversalSearch::destroy($universalSearch->id);
                }
            }
            User::destroy($id);
        }
        DB::commit();
        return Reply::success(__('messages.clientDeleted'));
    }

    public function showProjects($id)
    {
        

        $this->client = User::fromQuery('SELECT users.id, client_details.company_id FROM users JOIN client_details ON client_details.user_id = users.id WHERE client_details.user_id = ' . $id)->first();

        if (!$this->client) {
            abort(404);
        }

        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();
        $this->clientStats = $this->clientStats($id);
        $this->projects = Project::clientProjects($id);

        if (!is_null($this->clientDetail)) {
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }

        return view('admin.clients.projects', $this->data);
    }

    public function showInvoices($id)
    {

        $this->client = User::with('client_detail')->fromQuery('SELECT users.id, client_details.company_id FROM users JOIN client_details ON client_details.user_id = users.id WHERE client_details.user_id = ' . $id)->first();

        $this->clientDetail = $this->client ? $this->client->client_details : abort(404);
        $this->clientStats = $this->clientStats($id);

        if (!is_null($this->clientDetail)) {
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }

        $this->invoices = Invoice::select('invoices.invoice_number', 'invoices.total', 'currencies.currency_symbol', 'invoices.issue_date', 'invoices.id')
            ->leftJoin('projects', 'projects.id', '=', 'invoices.project_id')
            ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
            ->where(function ($query) use ($id) {
                $query->where('projects.client_id', $id)
                    ->orWhere('invoices.client_id', $id);
            })
            ->get();

        return view('admin.clients.invoices', $this->data);
    }

    public function showPayments($id)
    {
        $this->client = User::findClient($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();
        $this->clientStats = $this->clientStats($id);

        if (!is_null($this->clientDetail)) {
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }

        $this->payments = Payment::with(['project:id,project_name', 'currency:id,currency_symbol,currency_code', 'invoice'])
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('projects', 'projects.id', '=', 'payments.project_id')
            ->select('payments.id', 'payments.project_id', 'payments.currency_id', 'payments.invoice_id', 'payments.amount', 'payments.status', 'payments.paid_on', 'payments.remarks' ,'payments.payment_type')
            ->where('payments.status', '=', 'complete')
            ->where(function ($query) use ($id) {
                $query->where('projects.client_id', $id)
                    ->orWhere('invoices.client_id', $id);
            })
            ->orderBy('payments.id', 'desc')
            ->get();
        return view('admin.clients.payments', $this->data);
    }

    /**
     * @param $id
     *
     * @return Application|Factory|View
     */
    public function showAudits($id)
    {
        $company =  company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $this->client = User::findClient($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();
        $this->clientStats = $this->clientStats($id);

        $this->audits = AuditTrail::where('company_id', $companyId)->where('type', AuditTrail::CLIENT)->where('client_id', $id)->where('deliver_at', '<=', Carbon::now())->orderBy('id', 'desc')->get();

        return view('admin.clients.audits', $this->data);
    }

    public function gdpr($id)
    {
        $this->client = User::findClient($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();
        $this->allConsents = PurposeConsent::with(['user' => function ($query) use ($id) {
            $query->where('client_id', $id)
                ->orderBy('created_at', 'desc');
        }])->get();

        return view('admin.clients.gdpr', $this->data);
    }

    public function consentPurposeData($id)
    {
        $purpose = PurposeConsentUser::select('purpose_consent.name', 'purpose_consent_users.created_at', 'purpose_consent_users.status', 'purpose_consent_users.ip', 'users.name as username', 'purpose_consent_users.additional_description')
            ->join('purpose_consent', 'purpose_consent.id', '=', 'purpose_consent_users.purpose_consent_id')
            ->leftJoin('users', 'purpose_consent_users.updated_by_id', '=', 'users.id')
            ->where('purpose_consent_users.client_id', $id);

        return DataTables::of($purpose)
            ->editColumn('status', function ($row) {
                if ($row->status == 'agree') {
                    $status = __('modules.gdpr.optIn');
                } else if ($row->status == 'disagree') {
                    $status = __('modules.gdpr.optOut');
                } else {
                    $status = '';
                }

                return $status;
            })
            ->make(true);
    }

    public function saveConsentLeadData(SaveConsentUserDataRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $consent = PurposeConsent::findOrFail($request->consent_id);

        if ($request->consent_description && $request->consent_description != '') {
            $consent->description = $request->consent_description;
            $consent->save();
        }

        // Saving Consent Data
        $newConsentLead = new PurposeConsentUser();
        $newConsentLead->client_id = $user->id;
        $newConsentLead->purpose_consent_id = $consent->id;
        $newConsentLead->status = trim($request->status);
        $newConsentLead->ip = $request->ip();
        $newConsentLead->updated_by_id = $this->user->id;
        $newConsentLead->additional_description = $request->additional_description;
        $newConsentLead->save();

        $url = route('admin.clients.gdpr', $user->id);

        return Reply::redirect($url);
    }

    public function clientStats($id)
    {
        
        $project_ids[] = -1;
        $projectClients = ProjectClient::where('client_id', $id)->get();
        if($projectClients) {
            foreach ($projectClients as $projectClient) {
                $project_ids[] = $projectClient->project_id;
            }
        }
        $project_ids = implode(",",$project_ids);
        
        return DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE (projects.client_id = ' . $id . ' OR projects.id IN ('.$project_ids.') ) and projects.company_id = ' . company()->id . ') as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` left join projects on projects.id=invoices.project_id WHERE invoices.status != "paid" and invoices.status != "canceled" and (projects.client_id = ' . $id . ' or invoices.client_id = ' . $id . ') and invoices.company_id = ' . company()->id . ') as totalUnpaidInvoices'),
                DB::raw('(select sum(payments.amount) from `payments` left join projects on projects.id=payments.project_id WHERE payments.status = "complete" and projects.client_id = ' . $id . ' and payments.company_id = ' . company()->id . ') as projectPayments'),
                DB::raw('(select sum(payments.amount) from `payments` inner join invoices on invoices.id=payments.invoice_id  WHERE payments.status = "complete" and invoices.client_id = ' . $id . ' and payments.company_id = ' . company()->id . ') as invoicePayments'),
                DB::raw('(select count(contracts.id) from `contracts` WHERE contracts.client_id = ' . $id . ' and contracts.company_id = ' . company()->id . ') as totalContracts')
            )
            ->first();
    }
    public function downloadTemplate()
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=client-smaple-template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $records = array();
        $records[] = array('company_name' => 'xyz', 'company_website'=>'https://www.google.com.pk/', 'company_address'=>'xyz', 'client_name'=>'abc', 'client_email'=>'abc@gmail.com', 'client_mobile'=>'123', 'skype'=>'', 'linkedin'=>'', 'twitter'=>'', 'fcebook'=>'', 'gst_number'=>'123', 'shipping_address'=>'', 'note'=>'', 'email_notifications'=>'1', 'send_mail'=>'yes');
        $records[] = array('company_name' => 'xyz', 'company_website'=>'https://www.google.com.pk/', 'company_address'=>'xyz', 'client_name'=>'abc', 'client_email'=>'abc@gmail.com', 'client_mobile'=>'123', 'skype'=>'', 'linkedin'=>'', 'twitter'=>'', 'fcebook'=>'', 'gst_number'=>'123', 'shipping_address'=>'', 'note'=>'', 'email_notifications'=>'0', 'send_mail'=>'no');
        $columns = array('Company Name', 'Company Website', 'Company Address', 'Client Name', 'Client Email', 'Client Mobile', 'Skype', 'LinkedIn', 'Twitter', 'Fcebook' , 'GST Number', 'Shipping Address', 'Note', 'email Notifications', 'Send Mail');

        $callback = function() use ($records, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($records as $record) {
                fputcsv($file, array($record['company_name'], $record['company_website'], $record['company_address'], $record['client_name'], $record['client_email'], $record['client_mobile'], $record['skype'], $record['linkedin'], $record['twitter'], $record['fcebook'], $record['gst_number'], $record['shipping_address'], $record['note'], $record['email_notifications'], $record['send_mail']));
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
                return redirect(route('admin.clients.index'));
            } else {
                \Session::put('error', 'File too large. File must be less than 2MB.');
                return redirect(route('admin.clients.index'));
            }
        } else {
            \Session::put('error', 'Invalid File Extension.');
            return redirect(route('admin.clients.index'));
        }
        } else {
            \Session::put('error', 'Select File.');
            return redirect(route('admin.clients.index'));
        }
    }
    
    public function addImported($req){
        
        $existing_user = User::withoutGlobalScope(CompanyScope::class)->select('id', 'email')->where('email', $req[4])->first();

        // if no user found create new user with random password
        if (!$existing_user) {
            $password = str_random(8);
            // create new user
            $user = new User();
            $user->name = isset($req[3]) ? $req[3] : '';
            $user->email = isset($req[4]) ? $req[4] : '';
            $user->password = Hash::make($password);
            $user->mobile = isset($req[4]) ? $req[5] : '';
            $user->save();
            // attach role
            $role = Role::where('name', 'client')->first();
            $user->attachRole($role->id);
        }

        $existing_client_count = ClientDetails::select('id', 'email', 'company_id')
            ->where(
                [
                    'email' => $req[4]
                ]
            )->count();

        if ($existing_client_count === 0) {
            $client = new ClientDetails();
            $client->user_id = $existing_user ? $existing_user->id : $user->id;
            $client->name = isset($req[3]) ? $req[3] : '';
            $client->email = isset($req[4]) ? $req[4] : '';
            $client->mobile = isset($req[5]) ? $req[5] : '';
            $client->company_name = isset($req[0]) ? $req[0] : '';
            $client->address = isset($req[2]) ? $req[2] : '';
            $client->website = isset($req[1]) ? $req[1] : '';
            $client->note = isset($req[12]) ? $req[12] : '';
            $client->skype = isset($req[6]) ? $req[6] : '';
            $client->facebook = isset($req[9]) ? $req[9] : '';
            $client->twitter = isset($req[8]) ? $req[8] : '';
            $client->linkedin = isset($req[7]) ? $req[7] : '';
            $client->gst_number = isset($req[10]) ? $req[10] : '';
            $client->shipping_address = isset($req[11]) ? $req[11] : '';
            $client->email_notifications = isset($req[13]) ? $req[13] : 0;
            $client->save();

            // attach role
            if ($existing_user) {
                $role = Role::where('name', 'client')->where('company_id', $client->company_id)->first();
                $existing_user->attachRole($role->id);
            }

            // log search
            if (!is_null($client->company_name)) {
                $user_id = $existing_user ? $existing_user->id : $user->id;
                $this->logSearchEntry($user_id, $client->company_name, 'admin.clients.edit', 'client');
            }
            //log search
            $this->logSearchEntry($client->id, $req[3], 'admin.clients.edit', 'client');
            $this->logSearchEntry($client->id, $req[4], 'admin.clients.edit', 'client');
        }

        if (!$existing_user && isset($req[14]) && $req[14] == 'yes') {
            //send welcome email notification
            $user->notify(new NewUser($password));
        }
    }
    
    public function showNotes($id)
    {

        $this->client = User::fromQuery('SELECT users.id, client_details.company_id FROM users JOIN client_details ON client_details.user_id = users.id WHERE client_details.user_id = ' . $id)->first();

        if (!$this->client) {
            abort(404);
        }
        
        $this->clientNotes = ClientNote::where('client_id', '=', $this->client->id)->orderBy('id', 'desc')->get();

        return view('admin.clients.notes', $this->data);
    }
    
    
    public function createClient()
    {
        return view('admin.clients.create-client-modal', $this->data);
    }
    
    public function storeClient(StoreClientRequest $request) {

        $existing_user = User::withoutGlobalScope(CompanyScope::class)->select('id', 'email')->where('email', $request->input('email'))->first();

        // already user can't added again 
        if ($existing_user) {
            return Reply::error('Provided email is already registered. Try with different email.');
        }

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

            $client = new ClientDetails();
            $client->user_id = $existing_user ? $existing_user->id : $user->id;
            $client->name = $request->input('name');
            $client->email = $request->input('email');

            $client->save();

            // attach role
            if ($existing_user) {
                $role = Role::where('name', 'client')->where('company_id', $client->company_id)->first();
                $existing_user->attachRole($role->id);
            }
        } else {
            return Reply::error('Provided email is already registered. Try with different email.');
        }
        
        $clients = User::allClients();
        
        $select = '<option value="">Select Client</option>';
        foreach ($clients as $row) {
            $selected = '';
            if($row->id == $user->id) {
                $selected = 'selected=""';
            }
            $select .= '<option '.$selected.' value="' . $row->id . '">' . ucwords($row->name) . '</option>';
        }
        
        return Reply::successWithData('Client Added Successfully', ['optionData' => $select]);
    }
    public function loginView($id) {

        $this->client = User::findClient($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();


        return view('admin.clients.login-view-modal', $this->data);
    }

    public function connectAsClient($id) {

        $user = User::withoutGlobalScope(CompanyScope::class)->where('id', $id)->first();
        if ($user &&  $user->status == 'active') {
            //$this->guard()->logout();
            //session()->invalidate();
           \Auth::loginUsingId($user->id, true);
            return redirect(route('client.dashboard.index'));
            //return 'client/dashboard';
        }
        return redirect(route('login'));

    }

}
