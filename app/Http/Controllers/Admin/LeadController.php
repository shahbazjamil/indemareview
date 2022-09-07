<?php

namespace App\Http\Controllers\Admin;

use App\AuditTrail;
use App\Company;
use App\DataTables\Admin\LeadsDataTable;
use App\DataTables\Admin\LeadsArchiveDataTable;
use App\EmailAutomation;
use App\Helper\Reply;
use App\Http\Requests\Admin\Vendor\StoreClientVendorRequest;
use App\Http\Requests\CommonRequest;
use App\Http\Requests\Gdpr\SaveConsentLeadDataRequest;
use App\Http\Requests\Lead\StoreRequest;
use App\Http\Requests\Lead\UpdateRequest;
use App\Jobs\SendEmailJob;
use App\Lead;
use App\LeadAgent;
use App\LeadFollowUp;
use App\LeadSource;
use App\LeadStatus;
use App\Mail\FirstStepAutomation;
use App\Mail\LastStepAutomation;
use App\Mail\LeadCreated;
use App\PurposeConsent;
use App\PurposeConsentLead;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Mime\Email;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LeadController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = __('icon-people');
        $this->pageTitle = trans('app.menu.lead');
        $this->middleware(function ($request, $next) {
            if (!in_array('leads', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(LeadsDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/leads'));
        
        $this->totalLeads = Lead::all();

        $this->totalClientConverted = $this->totalLeads->filter(function ($value, $key) {
            return $value->client_id != null;
        });
        $this->totalLeads = Lead::all()->count();
        $this->totalClientConverted = $this->totalClientConverted->count();

        $this->pendingLeadFollowUps = LeadFollowUp::where(\DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::today()->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.company_id', company()->id)
            ->where('leads.next_follow_up', 'yes')
            ->get();
        $this->pendingLeadFollowUps = $this->pendingLeadFollowUps->count();
        $this->leadAgents = LeadAgent::with('user')->get();
        
        $this->totalRecords = $this->totalLeads;
        
        return $dataTable->render('admin.lead.index', $this->data);
    }
    
    public function archive(LeadsArchiveDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/archive'));
        
        return $dataTable->render('admin.lead.archive', $this->data);
    }

    public function show($id)
    {
       
        $this->lead = Lead::findOrFail($id);
        
        $this->lead = $this->lead->withCustomFields();
        $this->fields = $this->lead->getCustomFieldGroupsWithFields()->fields;
        
        return view('admin.lead.show', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $order = 'asc';
        $this->leadAgents = LeadAgent::with(['user' => function ($q) use ($order) {
            $q->orderBy('name', $order);
        }])->get();
        //$this->leadAgents = LeadAgent::with('user')->get();
        
        $lead = new Lead();
        $this->fields = $lead->getCustomFieldGroupsWithFields() ? $lead->getCustomFieldGroupsWithFields()->fields : [];
        
        $this->sources = LeadSource::orderBy('type')->get();
        $this->status = LeadStatus::orderBy('type')->get();
        return view('admin.lead.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $lead = new Lead();
        //$lead->company_name = $request->company_name;
        $lead->company_name = $request->client_email;
        //$lead->website = $request->website;
        $lead->address = $request->address;
        $lead->client_name = $request->client_name;
        $lead->client_email = $request->client_email;
        $lead->mobile = $request->mobile;
        //$lead->note = $request->note;
        //$lead->next_follow_up = $request->next_follow_up;
        $lead->agent_id = $request->agent_id;
        $lead->source_id = $request->source_id;
        
        
        $lead->lead_status = $request->lead_status;
        $lead->sales_code = $request->sales_code;
        $lead->lead_value = $request->lead_value;
        $lead->person_name_2 = $request->person_name_2;
        $lead->person_email_2 = $request->person_email_2;
        $lead->person_mobile_2 = $request->person_mobile_2;
        $lead->lot_address = $request->lot_address;
        $lead->square_feet = $request->square_feet;
        $lead->gate_code = $request->gate_code;
        $lead->shipping_address = $request->shipping_address;
        
        
        $lead->tags = json_encode(array());
        if($request->tags) {
            $lead->tags =   json_encode(array_values(array_unique($request->tags)));
        }

        $lead->save();
        
        // To add custom fields data
            if ($request->get('custom_fields_data')) {
                $lead->updateCustomFieldData($request->get('custom_fields_data'));
            }
        
         //$newData = collect($lead)->toArray();
        // $array = array('name'=>'Bhavesh');
        // create & initialize a curl session
         
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, "https://hooks.zapier.com/hooks/catch/10893938/b6ka6q0/");
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($newData));
//        $output = curl_exec($curl);
//        curl_close($curl);
        

        //log search
        $this->logSearchEntry($lead->id, $lead->client_name, 'admin.leads.show', 'lead');
        $this->logSearchEntry($lead->id, $lead->client_email, 'admin.leads.show', 'lead');
        if (!is_null($lead->company_name)) {
            $this->logSearchEntry($lead->id, $lead->company_name, 'admin.leads.show', 'lead');
        }

        //restartPM2();
        $timePeriod = 0;
        $increment = 0;
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        // Automation Mail Send
        $emailAutomations = EmailAutomation::where('company_id', $companyId)->where('automation_event', EmailAutomation::LEAD_CREATED)->get();
        if($emailAutomations) {
            $emailMasterAutomationIds = $emailAutomations->pluck('email_automation_id')->toArray();
            $getAllEmailAutomations = EmailAutomation::where('company_id', $companyId)->with(['emailTemplate', 'emailAutomationMaster'])->whereIn('email_automation_id', $emailMasterAutomationIds)
                                    ->orderBy('step')
                                    ->get();
            $getAllEmailAutomations = $getAllEmailAutomations->toArray();
            $company =  company()->toArray();
            $companyId = !empty($company) ? $company['id'] : Auth::user()->company->id;
            storeAuditTrail(array_unique($emailMasterAutomationIds), $companyId, $lead, AuditTrail::LEAD, Carbon::now(), AuditTrail::LEFT_STEP);
            foreach ($getAllEmailAutomations as $key => $emailAutomation) {
                $keyVariable = [
                    '{{lead.name}}'
                ];
                $value = [
                    $lead->client_name,
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
                $jobData = ['email' => $lead->client_email,'emailAutomation' => $emailAutomation,'companyId' => $companyId,'type' => 'lead'];
                $timePeriod = $timePeriod + intval($emailAutomation['time_period']);
                if ($mailType == 'minuteAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addMinutes($increment), AuditTrail::ENTERED_STEP);
                    if ($key == $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addMinutes($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addMinutes($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::LEAD_CREATED){
                            $audit = storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addMinutes($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addMinutes($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                } else if ($mailType == 'hourAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addHours($increment), AuditTrail::ENTERED_STEP);
                    if ($key ==  $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addHours($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addHours($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::LEAD_CREATED){
                            $audit =  storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addHours($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;

                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addHours($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                } else if ($mailType == 'dayAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addDays($increment), AuditTrail::ENTERED_STEP);
                    if ($key ==  $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addDays($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addDays($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::LEAD_CREATED){
                            $audit = storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addDays($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addDays($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                } else if ($mailType == 'weekAfter') {
                    storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addWeeks($increment), AuditTrail::ENTERED_STEP);
                    if ($key ==  $emailAutomation['step'] && $emailAutomation['automation_event'] == EmailAutomation::LAST_STEP_AUTOMATION){
                        $audit = storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addWeeks($timePeriod), AuditTrail::RECEIVED_STEP);
                        $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                        $jobData['type'] = 'last_step';
                        $jobData['emailAutomation'] = $emailAutomation;
                        dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addWeeks($timePeriod));
                        $increment = $timePeriod;
                    }else{
                        if ($emailAutomation['automation_event'] == EmailAutomation::LEAD_CREATED){
                            $audit = storeAuditTrail($emailAutomation, $companyId, $lead, AuditTrail::LEAD, Carbon::now()->addWeeks($timePeriod), AuditTrail::RECEIVED_STEP);
                            $emailAutomation['audit_id'] = isset($audit) ? $audit->id : null;
                            $jobData['emailAutomation'] = $emailAutomation;
                            dispatch(new SendEmailJob($jobData))->delay(Carbon::now()->addWeeks($timePeriod));
                            $increment = $timePeriod;
                        }
                    }
                }
            }
        }
        
        $this->mixPanelTrackEvent('lead_created', array('page_path' => '/admin/leads/create'));
        
        return Reply::redirect(route('admin.leads.edit', $lead->id));

        //return Reply::redirect(route('admin.leads.index'), __('messages.LeadAddedUpdated'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $order = "asc";
        $this->leadAgents = LeadAgent::with(['user' => function ($q) use ($order) {
            $q->orderBy('name', $order);
        }])->get();
        //$this->leadAgents = LeadAgent::with('user')->get();
        
        $this->lead = Lead::findOrFail($id);
        $this->sources = LeadSource::orderBy('type')->get();
        $this->status = LeadStatus::orderBy('type')->get();
        
        $tags = $this->lead->tags ? json_decode($this->lead->tags) : array();
        $this->lead->tags = $tags;
        if($tags) {
            $this->lead->tags = array_values(array_unique($tags));
        }
        
        $this->lead = $this->lead->withCustomFields();
        $this->fields = $this->lead->getCustomFieldGroupsWithFields()->fields;
        
        
        return view('admin.lead.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);
        //$lead->company_name = $request->company_name;
        $lead->company_name = $request->client_email;
        //$lead->website = $request->website;
        $lead->address = $request->address;
        $lead->client_name = $request->client_name;
        $lead->client_email = $request->client_email;
        $lead->mobile = $request->mobile;
        //$lead->note = $request->note;
        $lead->status_id = $request->status;
        $lead->source_id = $request->source;
        //$lead->next_follow_up = $request->next_follow_up;
        $lead->agent_id = $request->agent_id;
        
        $lead->lead_status = $request->lead_status;
        $lead->sales_code = $request->sales_code;
        $lead->lead_value = $request->lead_value;
        $lead->person_name_2 = $request->person_name_2;
        $lead->person_email_2 = $request->person_email_2;
        $lead->person_mobile_2 = $request->person_mobile_2;
        $lead->lot_address = $request->lot_address;
        $lead->square_feet = $request->square_feet;
        $lead->gate_code = $request->gate_code;
        $lead->shipping_address = $request->shipping_address;
        
        
        
        $lead->tags = json_encode(array());
        if($request->tags) {
            $lead->tags =   json_encode(array_values(array_unique($request->tags)));
        }
        

        $lead->save();
        
        
        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $lead->updateCustomFieldData($request->get('custom_fields_data'));
        }

        return Reply::redirect(route('admin.leads.index'), __('messages.LeadUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
//        $lead = Lead::withTrashed()->findOrFail($id);
//        $lead->forceDelete();
        Lead::destroy($id);
        return Reply::success(__('messages.LeadDeleted'));
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteFollow($id)
    {
        LeadFollowUp::destroy($id);
        return Reply::success(__('messages.followUp.deletedSuccess'));
    }

    /**
     * @param CommonRequest $request
     * @return array
     */
    public function changeStatus(CommonRequest $request)
    {
        $lead = Lead::findOrFail($request->leadID);
        $lead->status_id = $request->statusID;
        $lead->save();

        return Reply::success(__('messages.leadStatusChangeSuccess'));
    }

    public function gdpr($leadID)
    {
        $this->lead = Lead::findOrFail($leadID);
        $this->allConsents = PurposeConsent::with(['lead' => function ($query) use ($leadID) {
            $query->where('lead_id', $leadID)
                ->orderBy('created_at', 'desc');
        }])->get();

        return view('admin.lead.gdpr.show', $this->data);
    }

    public function consentPurposeData($id)
    {
        $purpose = PurposeConsentLead::select('purpose_consent.name', 'purpose_consent_leads.created_at', 'purpose_consent_leads.status', 'purpose_consent_leads.ip', 'users.name as username', 'purpose_consent_leads.additional_description')
            ->join('purpose_consent', 'purpose_consent.id', '=', 'purpose_consent_leads.purpose_consent_id')
            ->leftJoin('users', 'purpose_consent_leads.updated_by_id', '=', 'users.id')
            ->where('purpose_consent_leads.lead_id', $id);

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

    public function saveConsentLeadData(SaveConsentLeadDataRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $consent = PurposeConsent::findOrFail($request->consent_id);

        if ($request->consent_description && $request->consent_description != '') {
            $consent->description = $request->consent_description;
            $consent->save();
        }

        // Saving Consent Data
        $newConsentLead = new PurposeConsentLead();
        $newConsentLead->lead_id = $lead->id;
        $newConsentLead->purpose_consent_id = $consent->id;
        $newConsentLead->status = trim($request->status);
        $newConsentLead->ip = $request->ip();
        $newConsentLead->updated_by_id = $this->user->id;
        $newConsentLead->additional_description = $request->additional_description;
        $newConsentLead->save();

        $url = route('admin.leads.gdpr', $lead->id);

        return Reply::redirect($url);
    }


    /**
     * @param $leadID
     * @return Factory|View
     */
    public function followUpCreate($leadID)
    {
        $this->leadID = $leadID;
        return view('admin.lead.follow_up', $this->data);
    }

    /**
     * @param CommonRequest $request
     * @return array
     */
    public function followUpStore(\App\Http\Requests\FollowUp\StoreRequest $request)
    {

        $followUp = new LeadFollowUp();
        $followUp->lead_id = $request->lead_id;
        $followUp->next_follow_up_date = Carbon::createFromFormat($this->global->date_format, $request->next_follow_up_date)->format('Y-m-d');
        $followUp->remark = $request->remark;
        $followUp->save();
        $this->lead = Lead::findOrFail($request->lead_id);

        $view = view('admin.lead.followup.task-list-ajax', $this->data)->render();

        return Reply::successWithData(__('messages.leadFollowUpAddedSuccess'), ['html' => $view]);
    }

    public function followUpShow($leadID)
    {
        $this->leadID = $leadID;
        $this->lead = Lead::findOrFail($leadID);
        return view('admin.lead.followup.show', $this->data);
    }

    public function editFollow($id)
    {
        $this->follow = LeadFollowUp::findOrFail($id);
        $view = view('admin.lead.followup.edit', $this->data)->render();
        return Reply::dataOnly(['html' => $view]);
    }

    public function UpdateFollow(\App\Http\Requests\FollowUp\StoreRequest $request)
    {
        $followUp = LeadFollowUp::findOrFail($request->id);
        $followUp->lead_id = $request->lead_id;
        $followUp->next_follow_up_date = Carbon::createFromFormat($this->global->date_format, $request->next_follow_up_date)->format('Y-m-d');
        $followUp->remark = $request->remark;
        $followUp->save();

        $this->lead = Lead::findOrFail($request->lead_id);

        $view = view('admin.lead.followup.task-list-ajax', $this->data)->render();

        return Reply::successWithData(__('messages.leadFollowUpUpdatedSuccess'), ['html' => $view]);
    }

    public function followUpSort(CommonRequest $request)
    {
        $leadId = $request->leadId;
        $this->sortBy = $request->sortBy;

        $this->lead = Lead::findOrFail($leadId);
        if ($request->sortBy == 'next_follow_up_date') {
            $order = "asc";
        } else {
            $order = "desc";
        }

        $follow = LeadFollowUp::where('lead_id', $leadId)->orderBy($request->sortBy, $order);


        $this->lead->follow = $follow->get();

        $view = view('admin.lead.followup.task-list-ajax', $this->data)->render();

        return Reply::successWithData(__('messages.followUpFilter'), ['html' => $view]);
    }


    public function export($followUp, $client)
    {
        $currentDate = Carbon::today()->format('Y-m-d');
        $lead = Lead::select('leads.id', 'client_name', 'website', 'client_email', 'company_name', 'lead_status.type as statusName', 'leads.created_at', 'lead_sources.type as source', \DB::raw("(select next_follow_up_date from lead_follow_up where lead_id = leads.id and leads.next_follow_up  = 'yes' and DATE(next_follow_up_date) >= {$currentDate} ORDER BY next_follow_up_date asc limit 1) as next_follow_up_date"))
            ->leftJoin('lead_status', 'lead_status.id', 'leads.status_id')
            ->leftJoin('lead_sources', 'lead_sources.id', 'leads.source_id');
        if ($followUp != 'all' && $followUp != '') {
            $lead = $lead->leftJoin('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->where('leads.next_follow_up', 'yes')
                ->where('lead_follow_up.next_follow_up_date', '<', $currentDate);
        }
        if ($client != 'all' && $client != '') {
            if ($client == 'lead') {
                $lead = $lead->whereNull('client_id');
            } else {
                $lead = $lead->whereNotNull('client_id');
            }
        }

        $lead = $lead->GroupBy('leads.id')->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Client Name', 'Website', 'Email', 'Company Name', 'Status', 'Created On', 'Source', 'Next Follow Up Date'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($lead as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('leads', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Leads');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('leads file');

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
    
    public function downloadTemplate()
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=lead-smaple-template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $records = array();
        $records[] = array('company_name' => 'xyz', 'company_website'=>'https://www.google.com.pk/', 'company_address'=>'xyz', 'client_name'=>'abc', 'client_email'=>'abclead@gmail.com', 'client_mobile'=>'123', 'next_follow_up'=>'no', 'agent_email'=>'', 'lead_source'=>'email', 'note'=>'');
        $records[] = array('company_name' => 'xyz', 'company_website'=>'https://www.google.com.pk/', 'company_address'=>'xyz', 'client_name'=>'abc', 'client_email'=>'abclead2@gmail.com', 'client_mobile'=>'123', 'next_follow_up'=>'yes', 'agent_email'=>'', 'lead_source'=>'facebook', 'note'=>'');
        
        $columns = array('Company Name', 'Company Website', 'Company Address', 'Client Name', 'Client Email', 'Client Mobile', 'Next Follow Up', 'Agent Email', 'Lead Source', 'Note');

        $callback = function() use ($records, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($records as $record) {
                fputcsv($file, array($record['company_name'], $record['company_website'], $record['company_address'], $record['client_name'], $record['client_email'], $record['client_mobile'], $record['next_follow_up'], $record['agent_email'], $record['lead_source'], $record['note']));
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
                return redirect(route('admin.leads.index'));
            } else {
                \Session::put('error', 'File too large. File must be less than 2MB.');
                return redirect(route('admin.leads.index'));
            }
        } else {
            \Session::put('error', 'Invalid File Extension.');
            return redirect(route('admin.leads.index'));
        }
        } else {
            
            \Session::put('error', 'Select File.');
            return redirect(route('admin.leads.index'));
            
        }
    }
    
    public function addImported($req){
        
        
       
        $this->sources = LeadSource::all();
        
        
        
        $lead = new Lead();
        $lead->company_name = isset($req[0]) ? $req[0]:'';
        $lead->website = isset($req[1]) ? $req[1]:'';
        $lead->address = isset($req[2]) ? $req[2]:'';
        $lead->client_name = isset($req[3]) ? $req[3]:'';
        $lead->client_email = isset($req[4]) ? $req[4]:'';
        $lead->mobile = isset($req[5]) ? $req[5]:'';
        $lead->note = isset($req[9]) ? $req[9]:'';
        $lead->next_follow_up = isset($req[6]) ? $req[6]:'';
        
//        if(isset($req[7]) && !empty($req[7])) {
//            $leadAgent = LeadAgent::with('user')->where('email', $req[7])->first();
//            if($leadAgent) {
//                $lead->agent_id = $leadAgent->id;
//            }
//        }
        
//        $value = 'hello@indema.co';
//        $dd = LeadAgent::with(['user' => function($q) use($value) {
//            $q->where('email', $value); // '=' is optional
//        }])->first();
//        if($dd) {
//            echo $dd->id;exit;
//        }
        
        
        
        if(isset($req[8]) && !empty($req[8])) {
            $leadSource = LeadSource::where('type', $req[8])->first();
            if($leadSource) {
                $lead->source_id = $leadSource->id;
            }
        }
        $lead->save();

        //log search
        $this->logSearchEntry($lead->id, $lead->client_name, 'admin.leads.show', 'lead');
        $this->logSearchEntry($lead->id, $lead->client_email, 'admin.leads.show', 'lead');
        if (!is_null($lead->company_name)) {
            $this->logSearchEntry($lead->id, $lead->company_name, 'admin.leads.show', 'lead');
        }
       
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
        $this->lead = Lead::findOrFail($id);
        $this->audits = AuditTrail::where('company_id', $companyId)->where('type', AuditTrail::LEAD)->where('lead_id', $id)->where('deliver_at', '<=', Carbon::now())->orderBy('id', 'desc')->get();

        return view('admin.lead.audits', $this->data);
    }
}
