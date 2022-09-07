<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\CreateEmailTemplateRequest;
use App\Http\Requests\UpdateEmailTemplateRequest;
use App\EmailTemplate;
use App\Queries\EmailTemplateDataTable;
use App\Repositories\EmailTemplateRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class EmailTemplateController
 */
class EmailTemplateController extends AdminBaseController
{
    /**
     * @var EmailTemplateRepository
     */
    public $emailTemplateRepository;

    /**
     * EmailTemplateController constructor.
     * @param  EmailTemplateRepository  $emailTemplateRepository
     */
    public function __construct(EmailTemplateRepository $emailTemplateRepository)
    {
        parent::__construct();
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->pageTitle = 'Email Template';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View|Application
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new EmailTemplateDataTable())->get())->make(true);
        }

        return view('admin.email_templates.index', $this->data);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.email_templates.create', $this->data);
    }

    /**
     * @param CreateEmailTemplateRequest $request
     *
     * @return array
     */
    public function store(CreateEmailTemplateRequest $request)
    {
        $input = $request->all();
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;

        $fileName = null;
        $extension = null;
        if (isset($input['file']) && !empty($input['file'])) {
            $file = $input['file']->getClientOriginalName();
            $orgFileName = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $fileName = time() . mt_rand() . "." . $extension;
        }

        $emailTemplate = EmailTemplate::create([
            'template_name' => $input['template_name'],
            'user_id' => Auth::check() ? Auth::id() : null,
            'company_id' => $companyId,
            'subject' => $input['subject'],
            'body' => $input['body'],
            'email_type' => $input['email_type'],
            'file_name' => $fileName,
            'file_extension'  => $extension,
        ]);

        if (isset($input['file']) && !empty($input['file'])) {
            $directory = "user-uploads/email-templates/$emailTemplate->id";
            if (!File::exists(public_path($directory))) {
                $result = File::makeDirectory(public_path($directory), 0775, true);
            }
            $imageFilePath = "$directory/$fileName";

            File::move($input['file'], public_path($imageFilePath));
            $emailTemplate->save();
        }

        return Reply::success(__('messages.emailTemplateAdded'));
    }

    /**
     * @param  EmailTemplate  $emailTemplate
     *
     * @return Application|Factory|View
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        $this->data['emailTemplate'] = $emailTemplate;

        return view('admin.email_templates.edit', $this->data);
    }

    /**
     * @param UpdateEmailTemplateRequest $request
     *
     * @param EmailTemplate $emailTemplate
     *
     * @return array
     */
    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $emailTemplate): array
    {
        $input = $request->all();

        $this->emailTemplateRepository->update($input, $input['emailTemplateId']);

        return Reply::success(__('messages.emailTemplateUpdated'));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function destroy(Request $request): array
    {
        $input = $request->all();
        $directory = "user-uploads/email-templates/".$input['id'];
        if (File::exists(public_path($directory))) {
            $result = File::deleteDirectory(public_path($directory));
        }
        EmailTemplate::destroy($input['id']);

        return Reply::success(__('messages.emailTemplateDeleted'));
    }
}
