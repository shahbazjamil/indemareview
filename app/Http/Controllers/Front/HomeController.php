<?php

namespace App\Http\Controllers\Front;

use App\Company;
use App\CreditNotes;
use App\Feature;
use App\FooterMenu;
use App\FrontClients;
use App\FrontDetail;
use App\FrontFaq;
use App\GlobalSetting;
use App\Helper\Reply;
use App\Http\Requests\Front\ContactUs\ContactUsRequest;
use App\Invoice;
use App\InvoiceItems;
use App\Notifications\ContactUsMail;
use App\Package;
use App\PackageSetting;
use App\Project;
use App\Setting;
use App\Task;
use App\Testimonials;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Notification;
use App\Module;
use App\InvoiceSetting;
use App\OfflinePaymentMethod;
use App\PaymentGatewayCredentials;
use App\SeoDetail;
use App\TaskFile;
use App\LineItemGroup;

class HomeController extends FrontBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param null $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($slug = null)
    {
        return redirect(route('login'));
        
        $this->seoDetail = SeoDetail::where('page_name', 'home')->first();

        $this->pageTitle = $this->seoDetail ? $this->seoDetail->seo_title : __('app.menu.home');
        $this->packages = Package::where('default', 'no')->where('is_private', 0)->get();

        $features = Feature::get();
        $this->featureWithImages = $features->filter(function ($value, $key) {
            return $value->type == 'image';
        });

        $this->featureWithIcons = $features->filter(function ($value, $key) {
            return $value->type == 'icon';
        });

        $this->frontClients = FrontClients::all();
        $this->frontDetail  = FrontDetail::first();
        $this->testimonials = Testimonials::all();

        $this->packageFeatures = Module::get()->pluck('module_name')->toArray();

        // Check if trail is active
        $this->packageSetting = PackageSetting::where('status', 'active')->first();
        $this->trialPackage = Package::where('default','trial')->first();


        if ($slug) {
            $this->slugData = FooterMenu::where('slug', $slug)->first();
            $this->pageTitle = ucwords($this->slugData->name);
            return view('saas.footer-page', $this->data);
        }
        if ($this->setting->front_design == 1) {
            return view('saas.home', $this->data);
        }
        return view('front.home', $this->data);
    }

    public function feature()
    {

        $this->seoDetail = SeoDetail::where('page_name', 'feature')->first();

        $this->pageTitle = isset($this->seoDetail) ? $this->seoDetail->seo_title : __('app.menu.features');
        $features = Feature::all();

        $this->featureTasks = $features->filter(function ($value, $key) {
            return $value->type == 'task';
        });

        $this->featureBills = $features->filter(function ($value, $key) {
            return $value->type == 'bills';
        });

        $this->featureTeams = $features->filter(function ($value, $key) {
            return $value->type == 'team';
        });

        $this->featureApps = $features->filter(function ($value, $key) {
            return $value->type == 'apps';
        });

        $this->frontClients = FrontClients::all();

        if ($this->setting->front_design != 1) {
            abort(403);
        }

        return view('saas.feature', $this->data);
    }

    public function pricing()
    {
        $this->seoDetail = SeoDetail::where('page_name', 'pricing')->first();
        $this->pageTitle = isset($this->seoDetail) ? $this->seoDetail->seo_title : __('app.menu.pricing');
        $this->packages  = Package::where('default', 'no')->where('is_private', 0)
            ->orderBy('monthly_price')
            ->get();

        $this->frontFaqs = FrontFaq::all();

        $this->packageFeatures = Module::get()->pluck('module_name')->toArray();
        // Check if trail is active
        $this->packageSetting = PackageSetting::where('status', 'active')->first();
        $this->trialPackage = Package::where('default','trial')->first();


        if ($this->setting->front_design != 1) {
            abort(403);
        }

        return view('saas.pricing', $this->data);
    }

    public function contact()
    {
        $this->seoDetail = SeoDetail::where('page_name', 'contact')->first();
        $this->pageTitle = $this->seoDetail ? $this->seoDetail->seo_title : __('app.menu.contact');

        if ($this->setting->front_design != 1) {
            abort(403);
        }
        return view('saas.contact', $this->data);
    }

    public function page($slug = null)
    {
        $this->slugData = FooterMenu::where('slug', $slug)->first();
        $this->seoDetail = SeoDetail::where('page_name', $this->slugData->slug)->first();
        $this->pageTitle = isset($this->seoDetail) ? $this->seoDetail->seo_title : __('app.menu.contact');

        if ($this->setting->front_design == 1) {
            return view('saas.footer-page', $this->data);
        }
        return view('front.footer-page', $this->data);
    }

    public function contactUs(ContactUsRequest $request)
    {

        $this->pageTitle = 'app.menu.contact';
        $generatedBys = User::allSuperAdmin();

        $this->table = '<table><tbody style="color:#0000009c;">
        <tr>
            <td><p>Name : </p></td>
            <td><p>' . ucwords($request->name) . '</p></td>
        </tr>
        <tr>
            <td><p>Email : </p></td>
            <td><p>' . $request->email . '</p></td>
        </tr>
        <tr>
            <td style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;min-width: 98px;vertical-align: super;"><p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Message : </p></td>
            <td><p>' . $request->message . '</p></td>
        </tr>
</tbody>
        
</table><br>';

        Notification::route('mail', $generatedBys)
            ->notify(new ContactUsMail($this->data));


        return Reply::success('Thanks for contacting us. We will catch you soon.');
    }

    public function invoice($id)
    {
        $this->pageTitle = __('app.menu.invoices');
        $this->pageIcon = 'icon-people';

        $this->invoice = Invoice::whereRaw('md5(id) = ?', $id)->with('payment')->firstOrFail();
        // public url company session set.
        session(['company' => $this->invoice->company]);
        $this->paidAmount = $this->invoice->getPaidAmount();

        $this->discount = 0;
        if ($this->invoice->discount > 0) {
            $this->discount = $this->invoice->discount;

            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            }
        }

        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();

    foreach ($items as $item) {
            if ($this->invoice->discount > 0 && $this->invoice->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->invoice->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = Company::findOrFail($this->invoice->company_id);
        $this->credentials = PaymentGatewayCredentials::where('company_id', $this->invoice->company_id)->first();
        $this->methods = OfflinePaymentMethod::activeMethod();
        $this->invoiceSetting = InvoiceSetting::first();
        
        if($this->invoice->combine_line_items == 1) {
            
            $allItems = InvoiceItems::where('invoice_id', $this->invoice->id)->get();
            $this->groupItems = getGroupItems($allItems);
            
            
            return view('invoice_show_combine', [
                'companyName' => $this->settings->company_name,
                'pageTitle' => $this->pageTitle,
                'pageIcon' => $this->pageIcon,
                'global' => $this->settings,
                'setting' => $this->settings,
                'settings' => $this->settings,
                'invoice' => $this->invoice,
                'paidAmount' => $this->paidAmount,
                'discount' => $this->discount,
                'credentials' => $this->credentials,
                'taxes' => $this->taxes,
                'methods' => $this->methods,
                'invoiceSetting' => $this->invoiceSetting,
                'groupItems' => $this->groupItems
            ]);
            
            
        } else {
            
            return view('invoice', [
                'companyName' => $this->settings->company_name,
                'pageTitle' => $this->pageTitle,
                'pageIcon' => $this->pageIcon,
                'global' => $this->settings,
                'setting' => $this->settings,
                'settings' => $this->settings,
                'invoice' => $this->invoice,
                'paidAmount' => $this->paidAmount,
                'discount' => $this->discount,
                'credentials' => $this->credentials,
                'taxes' => $this->taxes,
                'methods' => $this->methods,
                'invoiceSetting' => $this->invoiceSetting,
            ]);
            
        }
        

        
    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoice = Invoice::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->creditNote = 0;
        if ($this->invoice->credit_note) {
            $this->creditNote = CreditNotes::where('invoice_id', $id)
                ->select('cn_number')
                ->first();
        }

        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }

        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();

        foreach ($items as $item) {
            if ($this->invoice->discount > 0 && $this->invoice->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->invoice->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                } else {
                    $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->global;

        $this->invoiceSetting = InvoiceSetting::where('company_id', $this->invoice->company_id)->first();
        //        return view('invoices.'.$this->invoiceSetting->template, $this->data);

        $pdf = app('dompdf.wrapper');
        $this->company = $this->invoice->company;
        // dd($this->company->address);
        //$pdf->loadView('invoices.' . $this->invoiceSetting->template, $this->data);
        $pdf->loadView('invoices.invoice-general', $this->data);
        $filename = $this->invoice->invoice_number;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function downloadInvoice($id)
    {

        $this->invoice = Invoice::whereRaw('md5(id) = ?', $id)->firstOrFail();

        // Download file uploaded
        if ($this->invoice->file != null) {
            return response()->download(storage_path('app/public/invoice-files') . '/' . $this->invoice->file);
        }

        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return $pdf->download($filename . '.pdf');
    }

    public function app()
    {
        return ['data' => GlobalSetting::select('id', 'company_name')->first()];
    }

    public function gantt($ganttProjectId)
    {
        $this->project = Project::whereRaw('md5(id) = ?', $ganttProjectId)->firstOrFail();
        $this->settings = Setting::findOrFail($this->project->company_id);
        $this->ganttProjectId = $ganttProjectId;

        return view('gantt', [
            'ganttProjectId' => $this->ganttProjectId,
            'global' => $this->settings,
            'project' => $this->project
        ]);
    }

    public function ganttData($ganttProjectId)
    {

        $data = array();
        $links = array();

        $projects = Project::select('id', 'project_name', 'start_date', 'deadline', 'completion_percent')
            ->whereRaw('md5(id) = ?', $ganttProjectId)
            ->get();

        $id = 0; //count for gantt ids
        foreach ($projects as $project) {
            $id = $id + 1;
            $projectId = $id;

            // TODO::ProjectDeadline to do
            $projectDuration = 0;
            if ($project->deadline) {
                $projectDuration = $project->deadline->diffInDays($project->start_date);
            }

            $data[] = [
                'id' => $projectId,
                'text' => ucwords($project->project_name),
                'start_date' => $project->start_date->format('Y-m-d H:i:s'),
                'duration' => $projectDuration,
                'progress' => $project->completion_percent / 100,
                'project_id' => $project->id
            ];

            $tasks = Task::projectOpenTasks($project->id);

            foreach ($tasks as $key => $task) {
                $id = $id + 1;

                $taskDuration = $task->due_date->diffInDays($task->start_date);
                $taskDuration = $taskDuration + 1;

                $data[] = [
                    'id' => $task->id,
                    'text' => ucfirst($task->heading),
                    'start_date' => (!is_null($task->start_date)) ? $task->start_date->format('Y-m-d') : $task->due_date->format('Y-m-d'),
                    'duration' => $taskDuration,
                    'parent' => $projectId,
                    'taskid' => $task->id
                ];

                $links[] = [
                    'id' => $id,
                    'source' => $task->dependent_task_id != '' ? $task->dependent_task_id : $projectId,
                    'target' => $task->id,
                    'type' => $task->dependent_task_id != '' ? 0 : 1
                ];
            }
        }

        $ganttData = [
            'data' => $data,
            'links' => $links
        ];

        return response()->json($ganttData);
    }

    public function changeLanguage($lang)
    {
        $cookie = Cookie::forever('language', $lang);
        return redirect()->back()->withCookie($cookie);
    }

    public function taskShare($id)
    {
        $this->pageTitle = __('app.task');

        $this->settings = cache()->remember(
            'global-setting',
            60 * 60 * 24,
            function () {
                return \App\Setting::first();
            }
        );
        $this->task = Task::with('board_column', 'subtasks', 'project', 'users')->whereRaw('md5(id) = ?', $id)->firstOrFail();

        return view('task-share', [
            'task' => $this->task,
            'global' => $this->settings
        ]);
    }
   

    public function taskFiles($id)
    {
        $this->taskFiles = TaskFile::where('task_id', $id)->get();
        return view('task-files', ['taskFiles' => $this->taskFiles]);
    }
}


