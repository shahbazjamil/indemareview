<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\EmailAutomation;
use App\EmailAutomationMaster;
use App\EmailTemplate;
use App\Helper\Reply;
use App\Jobs\SendEmailJob;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class EmailAutomationController extends AdminBaseController
{
    /**
     * EmailAutomationController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'Automation';
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/email-automation'));
        
        $automations = EmailAutomationMaster::with('emailAutomations')->get();
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $automations = EmailAutomationMaster::where('company_id', $companyId)->with('emailAutomations')->get();
        
        $this->data['automations'] = $automations;
        
        $this->totalRecords = EmailAutomationMaster::count();

        return view('admin.automation.index', $this->data);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailTemplates = EmailTemplate::where('company_id', $companyId)->where('email_type', '1')->get();
        $this->data['emailTemplates'] = $emailTemplates;
        $this->data['clients'] = User::allClients();

        return view('admin.automation.create', $this->data);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function store(Request $request): array
    {
        //restartPM2();
        $input = $request->all();
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        try {
            DB::beginTransaction();
            if (isset($input['edit_automation_master_id']) && !empty($input['edit_automation_master_id'])){
                $emailAutomationMaster = EmailAutomationMaster::find($input['edit_automation_master_id']);
                $emailAutomationMaster->update([
                    'name'  => $input['automation_name'],
                    'user_id'  => Auth::check() ? Auth::id() : null,
                    'company_id' => $companyId,
                    'step'  => $input['step'],
                ]);

                if (isset($input['edit_automation_id']) && !empty($input['edit_automation_id'])) {
                    // Delete Automation
                    $emailAutomation = EmailAutomation::where('company_id', $companyId)->where('email_automation_id', $emailAutomationMaster->id);
                    $emailAutomationIds = $emailAutomation->pluck('id')->toArray();
                    $deleteAutomationIds = array_diff($emailAutomationIds, $input['edit_automation_id']);
                    if ($deleteAutomationIds > 0) {
                        foreach ($deleteAutomationIds as $deleteAutomationId) {
                            $automation = EmailAutomation::find($deleteAutomationId);
                            $automation->delete();
                        }
                    }

                    // Update Automation
                    foreach ($input['edit_automation_id'] as $key => $value) {
                        $emailAutomation = $emailAutomation->where('id', $input['edit_automation_id'][$key])->first();
                        if ($emailAutomation) {
                            $emailAutomation->update([
                                'email_automation_id' => $emailAutomationMaster->id,
                                'email_template_id' => $input['edit_email_template_id'][$key],
                                'client_id'  => $input['edit_client_id'][$key],
                                'project_id'  => $input['edit_project_id'][$key],
                                'email_type' => $input['edit_action_type'][$key],
                                'time_period' => !empty($input['edit_time_period'][$key]) ? $input['edit_time_period'][$key] : 0,
                                'time_unit' => $input['edit_time_unit'][$key],
                                'time_type' => $input['edit_time_type'][$key],
                                'automation_event' => $input['edit_automation_event'][$key],
                                'step' => $key,
                                'is_manual' => isset($input['edit_is_manual'][$key]) ? $input['edit_is_manual'][$key] : 0,
                            ]);
                        }
                    }


                    // Create New Automation
                    if (isset($input['action_type']) && !empty($input['action_type'])) {
                        $getLastStep = EmailAutomation::where('company_id', $companyId)->where('email_automation_id', $emailAutomationMaster->id)->orderByDesc('step')->value('step');
                        foreach ($input['action_type'] as $key => $value) {
                            EmailAutomation::create([
                                'email_automation_id' => $emailAutomationMaster->id,
                                'email_template_id' => $input['email_template_id'][$key],
                                'company_id' => $companyId,
                                'client_id'  => $input['client_id'][$key],
                                'project_id'  => $input['project_id'][$key],
                                'email_type' => $input['action_type'][$key],
                                'time_period' => !empty($input['time_period'][$key]) ? $input['time_period'][$key] : 0,
                                'time_unit' => $input['time_unit'][$key],
                                'time_type' => $input['time_type'][$key],
                                'automation_event' => $input['automation_event'][$key],
                                'step' => $getLastStep + 1,
                                'is_manual' => isset($input['is_manual'][$key]) ? $input['is_manual'][$key] : 0,
                            ]);
                        }
                    }
                }else{
                    // Delete All Old Automation
                    $emailAutomationIds = EmailAutomation::where('company_id', $companyId)->where('email_automation_id', $emailAutomationMaster->id)->pluck('id')->toArray();
                    EmailAutomation::whereIn('id', $emailAutomationIds)->delete();

                    // Create New Automation
                    if (isset($input['action_type']) && !empty($input['action_type'])) {
                        foreach ($input['action_type'] as $key => $value) {
                            EmailAutomation::create([
                                'email_automation_id' => $emailAutomationMaster->id,
                                'email_template_id' => $input['email_template_id'][$key],
                                'company_id' => $companyId,
                                'client_id'  => $input['client_id'][$key],
                                'project_id'  => $input['project_id'][$key],
                                'email_type' => $input['action_type'][$key],
                                'time_period' => !empty($input['time_period'][$key]) ? $input['time_period'][$key] : 0,
                                'time_unit' => $input['time_unit'][$key],
                                'time_type' => $input['time_type'][$key],
                                'automation_event' => $input['automation_event'][$key],
                                'step' => $key,
                                'is_manual' => isset($input['is_manual'][$key]) ? $input['is_manual'][$key] : 0,
                            ]);
                        }
                    }
                }

                $message = __('messages.emailAutomationUpdated');
            }else{
                $emailAutomationMaster = EmailAutomationMaster::create([
                    'name'  => $input['automation_name'],
                    'user_id'  => Auth::check() ? Auth::id() : null,
                    'company_id' => $companyId,
                    'step'  => $input['step'],
                ]);

                $flag = false;
                foreach($input['action_type'] as $key => $value){
                    if (isset($input['is_manual'][$key]) && !empty($input['client_id'][$key]) && !empty($input['project_id'][$key]) && $input['is_manual'][$key] == EmailAutomation::IS_MANUAL) {
                        $flag = true;
                    }
                    EmailAutomation::create([
                        'email_automation_id'  => $emailAutomationMaster->id,
                        'email_template_id'  => $input['email_template_id'][$key],
                        'company_id' => $companyId,
                        'client_id'  => $input['client_id'][$key],
                        'project_id'  => $input['project_id'][$key],
                        'email_type'  => $input['action_type'][$key],
                        'time_period'  => !empty($input['time_period'][$key]) ? $input['time_period'][$key] : 0,
                        'time_unit'  => $input['time_unit'][$key],
                        'time_type'  => $input['time_type'][$key],
                        'automation_event'  => $input['automation_event'][$key],
                        'step' => $key,
                        'is_manual' => isset($input['is_manual'][$key]) ? $input['is_manual'][$key] : 0,
                    ]);
                }

                if ($flag) {
                    $emailAutomationMaster = $emailAutomationMaster->toArray();
                    $company = company()->toArray();
                    $companyId = !empty($company) ? $company['id'] : Auth::user()->company->id;
                    $emailAutomationMaster['company'] = null;
                    if (!empty($companyId)) {
                        $emailAutomationMaster['company'] = Company::find($companyId)->toArray();
                    }

                    $user = getAdminUser();
                    $jobData = ['email' => $user->email, 'emailAutomation' => $emailAutomationMaster,'companyId' => $companyId, 'type' => 'step_attention'];
                    dispatch(new SendEmailJob($jobData));
                }

                $message = __('messages.emailAutomationAdded');
            }
            DB::commit();

            return Reply::successWithData($message, ['url' => route('admin.email-automation.index')]);

        }catch (\Exception $e) {
            DB::rollback();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailAutomationMaster = EmailAutomationMaster::where('company_id', $companyId)->with('emailAutomations.emailTemplate')->find($id);
        $emailTemplates = EmailTemplate::where('company_id', $companyId)->where('email_type', '1')->get();
        $emailTemplatesNames = EmailTemplate::where('company_id', $companyId)->pluck('template_name', 'id')->toArray();
        $this->data['emailTemplates'] = $emailTemplates;
        $this->data['emailAutomation'] = $emailAutomationMaster;
        $this->data['emailTemplatesNames'] = $emailTemplatesNames;
        $this->data['clients'] = User::allClients();

        return view('admin.automation.create', $this->data);
    }

    /**
     * @param $id
     *
     * @return array
     *
     * @throws \Exception
     */
    public function destroy($id): array
    {
        $emailAutomationMaster = EmailAutomationMaster::find($id);
        $emailAutomationMaster->emailAutomations()->delete();
        $emailAutomationMaster->delete();

        return Reply::successWithData(__('messages.emailAutomationDeleted'), ['url' => route('admin.email-automation.index')]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getEmailTemplateDetail(Request $request): array
    {
        $input = $request->all();
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailTemplate = EmailTemplate::where('company_id', $companyId)->where('id', $input['emailTemplate'])->first();

        return Reply::successWithData(__('messages.emailTemplateRetried'), ['emailTemplate' => $emailTemplate]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getEmailFileTemplate(Request $request)
    {
        $input = $request->all();
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailTemplates = EmailTemplate::where('company_id', $companyId)->where('email_type', $input['email_type'])->get();

        return Reply::successWithData(__('messages.emailTemplateRetried'), ['emailTemplates' => $emailTemplates]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function updateEmailTemplate(Request $request): array
    {
        $input = $request->all();
        $emailTemplate = EmailTemplate::where('id', $input['edit_email_template_id'])->first();
        $fileName = null;
        $extension = null;
        if (isset($input['file']) && !empty($input['file'])) {
            $file = $input['file']->getClientOriginalName();
            $orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time() . mt_rand() . "." . $extension;

            $directory = "user-uploads/email-templates/$emailTemplate->id";
            if (File::exists(public_path($directory))) {
                $result = File::deleteDirectory(public_path($directory));
            }
        }

        $emailTemplate->update([
            'subject' => $input['edit_email_template_subject'],
            'body' => $input['edit_email_template_body'],
        ]);

        if (isset($input['file']) && !empty($input['file'])) {
            $emailTemplate->update([
                'file_name' => $fileName,
                'file_extension'  => $extension,
            ]);

            $directory = "user-uploads/email-templates/$emailTemplate->id";
            if (!File::exists(public_path($directory))) {
                $result = File::makeDirectory(public_path($directory), 0775, true);
            }
            $imageFilePath = "$directory/$fileName";

            File::move($input['file'], public_path($imageFilePath));
            $emailTemplate->save();
        }

        return Reply::successWithData(__('messages.emailTemplateUpdated'), ['emailTemplate' => $emailTemplate]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAutomationTemplate(Request $request): array
    {
        $input = $request->all();
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        $emailAutomationMaster = EmailAutomationMaster::where('company_id', $companyId)->find($input['id']);

        return Reply::successWithData(__('messages.emailAutomationRetried'), ['automationMaster' => $emailAutomationMaster]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function duplicateAutomation(Request $request): array
    {
        $input = $request->all();
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        try {
            DB::beginTransaction();
            $emailAutomationMaster = EmailAutomationMaster::find($input['automation_id']);
            $emailAutomationMaster = $emailAutomationMaster->replicate();
            $emailAutomationMaster->name = $input['name'];
            $emailAutomationMaster->created_at = Carbon::now();
            $emailAutomationMaster->updated_at = Carbon::now();
            $emailAutomationMaster->save();

            $emailAutomations = EmailAutomation::where('company_id', $companyId)->where('email_automation_id', $input['automation_id'])->get();
            foreach($emailAutomations as $emailAutomation){
                $emailAutomation = $emailAutomation->replicate();
                $emailAutomation->email_automation_id = $emailAutomationMaster->id;
                $emailAutomation->created_at = Carbon::now();
                $emailAutomation->updated_at = Carbon::now();
                $emailAutomation->save();
            }

            DB::commit();

            return Reply::successWithData(__('messages.emailAutomationCopied'), ['automation' => $emailAutomationMaster]);
        }catch (\Exception $e) {
            DB::rollback();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public  function getProjects(Request $request): array
    {
        $input = $request->all();
        /** @var User $user */
        $user = User::find($input['client_id']);
        $projects = null;
        if (!is_null($user)){
            $projects = $user->projects()->pluck('project_name', 'id')->toArray();
        }

        return Reply::successWithData(__('messages.projectsRetried') , ['projects' => $projects]);
    }
}
