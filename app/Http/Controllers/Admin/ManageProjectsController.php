<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Currency;
use App\DataTables\Admin\DiscussionDataTable;
use App\DataTables\Admin\ProjectsDataTable;
use App\Discussion;
use App\DiscussionCategory;
use App\DiscussionReply;
use App\EmailAutomation;
use App\Expense;
use App\Helper\Reply;
use App\Http\Requests\Project\StoreProject;
use App\Http\Requests\Project\UpdateProject;
use App\Mail\FirstStepAutomation;
use App\Mail\LastStepAutomation;
use App\Mail\ProjectEndDate;
use App\Mail\ProjectStartDate;
use App\Payment;
use App\ProjectActivity;
use App\ProjectCategory;
use App\ProjectFile;
use App\ProjectMember;
use App\ProjectTemplate;
use App\ProjectTimeLog;
use App\Task;
use App\TaskboardColumn;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Project;
use App\ProjectMilestone;
use App\TaskUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ProjectProgress;
use App\ProjectNote;
use App\Invoice;
use App\InvoiceSetting;
use App\Product;
use App\ProjectClient;
use Illuminate\Support\Facades\File;
use App\Role;
use App\ClientDetails;
use Illuminate\Support\Facades\Hash;
use App\RoleUser;


class ManageProjectsController extends AdminBaseController
{

    use ProjectProgress;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';
        $this->middleware(function ($request, $next) {
            if (!in_array('projects', $this->user->modules)) {
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
    public function index(ProjectsDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/projects'));
        
        $this->clients = User::allClients();
        $this->totalProjects = Project::count();
        $this->finishedProjects = Project::finished()->count();
        $this->inProcessProjects = Project::inProcess()->count();
        $this->onHoldProjects = Project::onHold()->count();
        $this->canceledProjects = Project::canceled()->count();
        $this->notStartedProjects = Project::notStarted()->count();
        $this->overdueProjects = Project::overdue()->count();

        //Budget Total
        $this->projectBudgetTotal = Project::sum('project_budget');
        $this->categories = ProjectCategory::all();

        $this->projectEarningTotal = Payment::join('projects', 'projects.id', '=', 'payments.project_id')
            ->where('payments.status', 'complete')
            ->whereNotNull('projects.project_budget')
            ->whereNotNull('payments.project_id')
            ->sum('payments.amount');
        
        $this->totalRecords = $this->totalProjects;

        return $dataTable->render('admin.projects.index', $this->data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function archive()
    {
        $this->totalProjects = Project::onlyTrashed()->count();
        $this->clients = User::allClients();
        return view('admin.projects.archive', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(company()->projects->count() >= company()->package->max_projects) {
            return redirect(route('admin.billing'));
        }
        $this->clients = User::allClients();
        $this->categories = ProjectCategory::all();
        $this->templates = ProjectTemplate::orderBy('project_name')->get();
        $this->currencies = Currency::all();
        $this->employees = User::allEmployees();

        $project = new Project();
        $this->upload = can_upload();
        $this->fields = $project->getCustomFieldGroupsWithFields()->fields;
        return view('admin.projects.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProject $request)
    {
        $memberExistsInTemplate = false;

        $project = new Project();
        $project->project_name = $request->project_name;
        if ($request->project_summary != '') {
            $project->project_summary = $request->project_summary;
        }
        $project->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        
        $startDate = new \DateTime($request->start_date_time, new \DateTimeZone($this->global->timezone));
        $startDate->setTimezone(new \DateTimeZone("UTC"));
        $projectStartDate = $startDate->format("Y-m-d H:i:s");
        $projectStartDateTime = Carbon::parse($projectStartDate);
        
        $project->start_date_time = $request->start_date_time;

        $projectDeadLineTime = null;
        if (!$request->has('without_deadline')) {
            $project->deadline = Carbon::createFromFormat($this->global->date_format, $request->deadline)->format('Y-m-d');
            
            $endDate = new \DateTime($request->deadline_time, new \DateTimeZone($this->global->timezone));
            $endDate->setTimezone(new \DateTimeZone("UTC"));
            $projectEndDate = $endDate->format("Y-m-d H:i:s");
            $projectDeadLineTime = Carbon::parse($projectEndDate);
            
            $project->deadline_time = $request->deadline_time;
        }

        if ($request->notes != '') {
            $project->notes = $request->notes;
        }
        if ($request->category_id != '') {
            $project->category_id = $request->category_id;
        }
        // old method
        $project->client_id = isset($request->client_id[0])?$request->client_id[0]:null;

        if ($request->client_view_task) {
            $project->client_view_task = 'enable';
        } else {
            $project->client_view_task = "disable";
        }
        if (($request->client_view_task) && ($request->client_task_notification)) {
            $project->allow_client_notification = 'enable';
        } else {
            $project->allow_client_notification = "disable";
        }

        if ($request->manual_timelog) {
            $project->manual_timelog = 'enable';
        } else {
            $project->manual_timelog = "disable";
        }

        $project->project_budget = $request->project_budget;

        $project->currency_id = $request->currency_id;
        if (!$request->currency_id) {
            $project->currency_id = $this->global->currency_id;
        }

        $project->hours_allocated = $request->hours_allocated ? $request->hours_allocated : 0;
        $project->max_weekly_hours = $request->max_weekly_hours ? $request->max_weekly_hours : 0;
        $project->status = $request->status;

        $project->save();

        
        // new method assign more than one client to project
        ProjectClient::where('project_id', $project->id)->delete();
        $client_ids = $request->client_id?$request->client_id:[];
        if(count($client_ids) > 0) {
            foreach ($client_ids as $client_id) {
                $projectClient = new ProjectClient();
                $projectClient->project_id = $project->id;
                $projectClient->client_id = $client_id;
                $projectClient->save();
            }
        }
        
        $templateMembers = [];
        
        if ($request->template_id) {
            $template = ProjectTemplate::findOrFail($request->template_id);
            
            foreach ($template->milestones as $milestoneD) {
                
                
                $milestone = new ProjectMilestone();
                $milestone->project_id = $project->id;
                $milestone->milestone_title = $milestoneD->milestone_title;
                $milestone->summary = $milestoneD->summary;
                $milestone->cost = ($milestoneD->cost == '') ? '0' : $milestoneD->cost;
                $milestone->currency_id = $milestoneD->currency_id;
                $milestone->status = $milestoneD->status;
                $milestone->project_template_milestone_id = $milestoneD->id;
                $milestone->save();
                
            }

            foreach ($template->members as $member) {
                $projectMember = new ProjectMember();
                $templateMembers[] = $member->user_id;

                $projectMember->user_id    = $member->user_id;
                $projectMember->project_id = $project->id;
                $projectMember->save();

                if ($member->user_id == $this->user->id) {
                    $memberExistsInTemplate = true;
                }
            }
            foreach ($template->tasks as $task) {
                $projectTask = new Task();

                $projectTask->project_id  = $project->id;
                $projectTask->heading     = $task->heading;
                $projectTask->description = $task->description;
                $projectTask->due_date    = Carbon::now()->addDay()->format('Y-m-d');
                $projectTask->status      = 'incomplete';
                $projectTask->created_by      = $this->user->id;
                
                if(isset($task->milestone_id) && !empty($task->milestone_id)) {
                    $projectMilestone = ProjectMilestone::where('project_template_milestone_id', '=', $task->milestone_id)->where('project_id', '=', $project->id)->first();
                    if($projectMilestone) {
                        $projectTask->milestone_id = $projectMilestone->id;
                    }
                }
                
                $projectTask->save();

                foreach ($task->users_many as $key => $value) {
                    
                    $taskUser = TaskUser::where('user_id', '=', $value->id)->where('task_id', '=', $projectTask->id)->first();
                    if(!$taskUser) {
                        TaskUser::create(
                            [
                                'user_id' => $value->id,
                                'task_id' => $projectTask->id
                            ]
                        );
                    }
                }
            }
        }

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $project->updateCustomFieldData($request->get('custom_fields_data'));
        }

        $users = $request->user_id;

        if($users) {
            foreach ($users as $user) {

                if (!in_array($user , $templateMembers)) {
                    $member = new ProjectMember();
                    $member->user_id = $user;
                    $member->project_id = $project->id;
                    $member->save();
                    $this->logProjectActivity($project->id, ucwords($member->user->name) . ' ' . __('messages.isAddedAsProjectMember'));

                }

            }
        }

        $this->logSearchEntry($project->id, 'Project: ' . $project->project_name, 'admin.projects.show', 'project');

        $this->logProjectActivity($project->id, ucwords($project->project_name) . ' ' . __("messages.addedAsNewProject"));

        //restartPM2();
        $timePeriod = 0;
        $company = company();
        $companyId = !empty($company) ? $company->id : Auth::user()->company->id;
        // Start Date Automation Mail send
        $emailAutomations = EmailAutomation::where('company_id', $companyId)->where('automation_event', EmailAutomation::START_PROJECT)->get();
        if($emailAutomations){
            $emailMasterAutomationIds = $emailAutomations->pluck('email_automation_id')->toArray();
            $getAllEmailAutomations = EmailAutomation::where('company_id', $companyId)->with(['emailTemplate', 'emailAutomationMaster'])->whereIn('email_automation_id', $emailMasterAutomationIds)
                                    ->orderBy('step')->get();
            $getAllEmailAutomations = $getAllEmailAutomations->toArray();
            $data = projectStartAutomationMail($getAllEmailAutomations, $project, $timePeriod, $projectStartDateTime);
            $timePeriod = $data['timePeriod'];
            $projectStartDateTime = $data['projectStartDateTime'];
            $project->start_date = $data['projectStartDate'];
        }

        if (!$request->has('without_deadline')) {
            // End Date Automation Mail send
            $emailAutomations = EmailAutomation::where('company_id', $companyId)->where('automation_event', EmailAutomation::END_PROJECT)->get();
            if ($emailAutomations) {
                $emailMasterAutomationIds = $emailAutomations->pluck('email_automation_id')->toArray();
                $getAllEmailAutomations = EmailAutomation::where('company_id', $companyId)->with(['emailTemplate', 'emailAutomationMaster'])->whereIn('email_automation_id', $emailMasterAutomationIds)
                                        ->orderBy('step')->get();
                $getAllEmailAutomations = $getAllEmailAutomations->toArray();
                $data = projectEndAutomationMail($getAllEmailAutomations, $project, $timePeriod, $projectDeadLineTime);
                $timePeriod = $data['timePeriod'];
                $projectDeadLineTime = $data['projectDeadLineTime'];
                $project->deadline = $data['projectDeadLine'];
            }
        }
        
        $this->mixPanelTrackEvent('project_created', array('page_path' => 'admin/projects/create'));

        return Reply::dataOnly(['projectID' => $project->id]);

        //return Reply::redirect(route('admin.projects.index'), __('modules.projects.projectUpdated'));
    }
    
//    Project Budget: What the user input when creating a project. => Done
//Current Budget: The project budget, minus PAID invoices. (updates live) => Done
//Budget Remaining: The current budget, minus project budget. => Done
//Total products (your cost): Total UNIT cost of all products in project => Done
//Total products (sale): Total SALE cost of all products incl. Markup. => Done
//Current project hours: Total hours already logged on project. => Done
//Value of project hours: Takes from the hour rate of members on project. => Done
//Current profit: TOTAL Time log value of project hours + Total markup for all products, and TOTAL invoices specific to services not products
//Total Shipping: TOTAL for shipping/Freight column in products.  => Done
//Total in markups: Total markup ($ AND %) totaled. => Done
//Total Invoices: Total amount invoiced to client. => Done
//Total Past Due: Total amount of invoiced past due. => Done
//Total invoice paid: Total invoices with PAID status. $ amount. => Done

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    
    
    public function show($id)
    {
        $this->project = Project::findOrFail($id)->withCustomFields();
        $this->fields = $this->project->getCustomFieldGroupsWithFields()->fields;
        $this->activeTimers = ProjectTimeLog::projectActiveTimers($this->project->id);
        
        $startDate = Carbon::now()->timezone($this->global->timezone);
        
        $project_budget =  $this->project->project_budget ? $this->project->project_budget : 0;
        $current_budget = 0;
        $remaining_budget = 0; 
        $all_products_unit_cost = 0;
        $all_products_sale_cost = 0;
        $logged_hours = 0;
        $logged_hours_value = 0;
        $invoices_specific_total = 0;
        $all_products_shipping = 0;
        $all_products_markup = 0;
        $invoices_total = 0;
        $invoices_total_due = 0;
        $invoices_total_paid = 0;
        $current_profit = 0;
        
       
        
        

        if (is_null($this->project->deadline)) {
            $this->daysLeft = 0;
        } else {
            if ($this->project->deadline->isPast()) {
                $this->daysLeft = 0;
            } else {
                $this->daysLeft = $this->project->deadline->diff(Carbon::now())->format('%d') + ($this->project->deadline->diff(Carbon::now())->format('%m') * 30) + ($this->project->deadline->diff(Carbon::now())->format('%y') * 12);
            }

            $this->daysLeftFromStartDate = $this->project->deadline->diff($this->project->start_date)->format('%d') + ($this->project->deadline->diff($this->project->start_date)->format('%m') * 30) + ($this->project->deadline->diff($this->project->start_date)->format('%y') * 12);

            $this->daysLeftPercent = ($this->daysLeftFromStartDate == 0 ? "0" : (($this->daysLeft / $this->daysLeftFromStartDate) * 100));
        }
        
        $projectMembers = $this->project->members;
        
        if($projectMembers) {
            foreach ($projectMembers as $members) {
                if($members->user) {
                    $userProjectTimeLog = ProjectTimeLog::where('user_id', $members->user->id)->where('project_id', $id)->sum('total_minutes');
                    $userProjectTimeLog = intdiv($userProjectTimeLog, 60); 
                    if($members->user->employeeDetail && !is_null($members->user->employeeDetail->hourly_rate)) {
                        $logged_hours_value = $logged_hours_value + ($userProjectTimeLog * $members->user->employeeDetail->hourly_rate);
                    }
                }
            }
        }

        $this->hoursLogged = $this->project->times()->sum('total_minutes');

        $this->hoursLogged = intdiv($this->hoursLogged, 60);

        $this->activities = ProjectActivity::getProjectActivities($id, 10);
        $this->earnings = Payment::where('status', 'complete')
            ->where('project_id', $id)
            ->sum('amount');
        $this->expenses = Expense::where(['project_id' => $id, 'status' => 'approved'])->sum('price');
        $this->expenses = number_format((float)$this->expenses, 2, '.', '');
        $this->milestones = ProjectMilestone::with('currency')->where('project_id', $id)->get();

        $this->taskBoardStatus = TaskboardColumn::all();

        $this->taskStatus = array();
        $projectTasksCount = Task::where('project_id', $id)->count();
        if ($projectTasksCount > 0) {
            foreach ($this->taskBoardStatus as $key => $value) {
                $totalTasks = Task::leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
                    ->where('tasks.board_column_id', $value->id)
                    ->where('tasks.project_id', $id);
                $taskStatus[$value->slug] = [
                    'count' => $totalTasks->count(),
                    'label' => $value->column_name,
                    'color' => $value->label_color
                ];
            }
            $this->taskStatus = json_encode($taskStatus);
        }


        $incomes = [];
        $graphData = [];
        $invoices = Payment::join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->where('payments.status', 'complete')
            ->where('payments.project_id', $id)
            // ->groupBy('year', 'month')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%M/%y") as date'),
                DB::raw('YEAR(paid_on) year, MONTH(paid_on) month'),
                DB::raw('amount as total'),
                'currencies.id as currency_id',
                'currencies.exchange_rate'
            ]);

        foreach ($invoices as $invoice) {
            if (!isset($incomes[$invoice->date])) {
                $incomes[$invoice->date] = 0;
            }

            if ($invoice->currency_id != $this->global->currency->id) {
                $incomes[$invoice->date] += floor($invoice->total / $invoice->currency->exchange_rate);
            } else {
                $incomes[$invoice->date] += round($invoice->total, 2);
            }
        }

        $dates = array_keys($incomes);

        foreach ($dates as $date) {
            $graphData[] = [
                'date' =>  $date,
                'total' =>  isset($incomes[$date]) ? round($incomes[$date], 2) : 0,
            ];
        }

        usort($graphData, function ($a, $b) {
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);
            return $t1 - $t2;
        });

        $this->chartData = json_encode($graphData);

        $this->timechartData = DB::table('project_time_logs');

        $this->timechartData = $this->timechartData->where('project_time_logs.project_id', $id)
            ->groupBy('date', 'month')
            ->orderBy('start_time', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(start_time,"%M/%y") as date'),
                DB::raw('YEAR(start_time) year, MONTH(start_time) month'),
                // DB::raw('DATE_FORMAT(start_time,\'%d/%M/%y\') as date'),
                DB::raw('FLOOR(sum(total_minutes/60)) as total_hours')
            ])
            ->toJSON();
        
        
        
        $this->tasks = Task::projectOpenTasks($this->project->id, null, $startDate);
        $this->projectNotes = ProjectNote::where('project_id', '=', $this->project->id)->orderBy('id', 'desc')->get();
        
        
        // SB added 
        
        $invoicesData = Invoice::where('project_id', $id)->get();
        if($invoicesData) {
            foreach ($invoicesData as $invoice) {
                
                if($invoice->status == 'paid') {
                    if ($invoice->currency_id != $this->global->currency->id) {
                        $invoices_total_paid += floor($invoice->total / $invoice->currency->exchange_rate);
                    } else {
                        $invoices_total_paid += round($invoice->total, 2);
                    }
                }
                
                if($invoice->status != 'paid' && $invoice->status != 'canceled' && $invoice->due_date < $startDate->format('Y-m-d')) {
                    if ($invoice->currency_id != $this->global->currency->id) {
                        $invoices_total_due += floor($invoice->total / $invoice->currency->exchange_rate);
                    } else {
                        $invoices_total_due += round($invoice->total, 2);
                    }
                }
                
                
                if($invoice->status == 'paid') {
                    if($invoice->items) {
                        foreach ($invoice->items as $item) {
                            if($item->invoice_item_type == 'services') {
                                
                                if(!is_null($item->amount)) {
                                    if ($invoice->currency_id != $this->global->currency->id) {
                                        $invoices_specific_total += floor($item->amount / $invoice->currency->exchange_rate);
                                    } else {
                                        $invoices_specific_total += round($item->amount, 2);
                                    }
                                }
                            }
                        }
                    }
                }
                
                
                
                if ($invoice->currency_id != $this->global->currency->id) {
                    $invoices_total += floor($invoice->total / $invoice->currency->exchange_rate);
                } else {
                    $invoices_total += round($invoice->total, 2);
                }
                
                
                
                
            }
        }
        
         $products = Product::join('product_projects', 'product_projects.product_id', '=', 'products.id')
                 ->where('product_projects.project_id',$this->project->id)->where('products.is_approved',1)
            ->select('products.*');
         $products = $products->get();
       
        
        if($products) {
            foreach ($products as $product) {
                
                
                if(!is_null($product->cost_per_unit) && is_numeric($product->cost_per_unit) && $product->cost_per_unit > 0) {
                    $all_products_unit_cost +=$product->cost_per_unit;
                }
                
                if(!is_null($product->total_sale) && $product->total_sale > 0) {
                    $all_products_sale_cost +=$product->total_sale;
                }
                
                if(!is_null($product->freight) && $product->freight >0) {
                    $all_products_shipping +=$product->freight;
                }
                
                if(!is_null($product->default_markup_fix) && $product->default_markup_fix > 0) {
                    $all_products_markup +=$product->default_markup_fix;
                }
                
                if(!is_null($product->markup_fix) && $product->markup_fix > 0) {
                    $all_products_markup +=$product->markup_fix;
                }
                
            }
        }
        
       
        
        //$current_budget = $project_budget - $invoices_total_paid;
        $current_budget =  $invoices_total_paid;
        //$remaining_budget = $current_budget - $project_budget;
        $remaining_budget = $project_budget - $invoices_total_paid;
        $logged_hours = $this->hoursLogged;
        $current_profit = $logged_hours_value + $all_products_markup + $invoices_specific_total;
        
        
        $project_budget_per = 0;
        $invoices_total_per = 0;
        $current_profit_per = 0;
        
       
        $g_total = $project_budget + $invoices_total + $current_profit;
        
        if($g_total > 0) {
            $project_budget_per =  cal_percentage($project_budget, $g_total);
            $invoices_total_per =  cal_percentage($invoices_total, $g_total);
            $current_profit_per =  cal_percentage($current_profit, $g_total);
        }
        
        $total_hours_allocated = $this->project->hours_allocated ? $this->project->hours_allocated : 0;
        $total_hours_used = $logged_hours ;
        $hours_left = $total_hours_allocated - $logged_hours;
        $hours_over_allocated = 0;
        $billable_hours_value = $logged_hours_value;
        
        $hours_used_per = 0;
        $total_hours_allocated_per = 0;
        $hours_left_per = 0;
        
        if($total_hours_allocated > 0) {
            $hours_used_per = cal_percentage($total_hours_used, $total_hours_allocated);
            $hours_left_per = cal_percentage($hours_left, $total_hours_allocated);
            $total_hours_allocated_per = (100 - $hours_used_per);
        }
        
        if($total_hours_used > $hours_over_allocated) {
            $hours_over_allocated = ($total_hours_used - $total_hours_allocated);
        }
        
        
        $this->project_budget = $project_budget;
        $this->invoices_total_paid = $invoices_total_paid;
        $this->current_budget = $current_budget;
        $this->remaining_budget = $remaining_budget;
        $this->logged_hours = $logged_hours;
        $this->logged_hours_value = number_format((float)$logged_hours_value, 2, '.', '');
        $this->invoices_total = $invoices_total;
        $this->invoices_total_due = $invoices_total_due;
        $this->all_products_unit_cost = number_format((float)$all_products_unit_cost, 2, '.', '');
        $this->all_products_sale_cost = $all_products_sale_cost;
        $this->all_products_shipping = $all_products_shipping;
        $this->all_products_markup = $all_products_markup;
        $this->current_profit = $current_profit;
        
        $this->project_budget_per = $project_budget_per;
        $this->invoices_total_per = $invoices_total_per;
        $this->current_profit_per = $current_profit_per;
        $this->total_purchase_order = $this->project->getTotalPurchaseOrder();
        
        $this->total_hours_allocated = $total_hours_allocated;
        $this->total_hours_used = $total_hours_used;
        $this->hours_left = $hours_left;
        $this->hours_over_allocated = $hours_over_allocated;
        $this->billable_hours_value = $billable_hours_value;
        $this->hours_used_per = $hours_used_per;
        $this->total_hours_allocated_per = $total_hours_allocated_per;
        $this->hours_left_per = $hours_left_per;
        
        // SB end

        return view('admin.projects.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->clients = User::allClients();
        $this->categories = ProjectCategory::all();
        $this->project = Project::findOrFail($id)->withCustomFields();
        $this->fields = $this->project->getCustomFieldGroupsWithFields()->fields;
        $this->currencies = Currency::all();
        
        $selected_clients = [];
        if($this->project->client_id) {
            $selected_clients[] = $this->project->client_id;
        }
        
        if($this->project->clients) {
            foreach ($this->project->clients as $client) {
                $selected_clients[] = $client->client_id;
            }
        }
        
        $this->selected_clients = $selected_clients;
        
        return view('admin.projects.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProject $request, $id)
    {
        //echo "This function";
        $project = Project::findOrFail($id);
        $project->project_name = $request->project_name;
        if ($request->project_summary != '') {
            $project->project_summary = $request->project_summary;
        }
        $project->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        $project->start_date_time = $request->start_date_time;

        if (!$request->has('without_deadline')) {
            $project->deadline = Carbon::createFromFormat($this->global->date_format, $request->deadline)->format('Y-m-d');
            $project->deadline_time = $request->deadline_time;
        } else {
            $project->deadline = null;
            $project->deadline_time = null;
        }

        if ($request->notes != '') {
            $project->notes = $request->notes;
        }
        if ($request->category_id != '') {
            $project->category_id = $request->category_id;
        }

        if ($request->client_view_task) {
            $project->client_view_task = 'enable';
        } else {
            $project->client_view_task = "disable";
        }
        if (($request->client_view_task) && ($request->client_task_notification)) {
            $project->allow_client_notification = 'enable';
        } else {
            $project->allow_client_notification = "disable";
        }

        if ($request->manual_timelog) {
            $project->manual_timelog = 'enable';
        } else {
            $project->manual_timelog = "disable";
        }

        // old method
        $project->client_id = isset($request->client_id[0]) ? $request->client_id[0] : null;
        $project->feedback = $request->feedback;

        if ($request->calculate_task_progress) {
            $project->calculate_task_progress = $request->calculate_task_progress;
            $project->completion_percent = $this->calculateProjectProgress($id);
        } else {
            $project->calculate_task_progress = "false";
            $project->completion_percent = $request->completion_percent;
        }


        $project->project_budget = $request->project_budget;
        $project->currency_id = $request->currency_id;
        $project->hours_allocated = $request->hours_allocated ? $request->hours_allocated : 0;
        $project->max_weekly_hours = $request->max_weekly_hours ? $request->max_weekly_hours : 0;
        
        $project->status = $request->status;

        $project->save();
        
        // new method assign more than one client to project
        ProjectClient::where('project_id', $project->id)->delete();
        $client_ids = $request->client_id?$request->client_id:[];
        if(count($client_ids) > 0) {
            foreach ($client_ids as $client_id) {
                $projectClient = new ProjectClient();
                $projectClient->project_id = $project->id;
                $projectClient->client_id = $client_id;
                $projectClient->save();
            }
        }

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $project->updateCustomFieldData($request->get('custom_fields_data'));
        }

        $this->logProjectActivity($project->id, ucwords($project->project_name) . __('modules.projects.projectUpdated'));
        //$this->data = [ statusCode => "00"];

        return Reply::dataOnly(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $project = Project::withTrashed()->findOrFail($id);
        $project->forceDelete();

        return Reply::success(__('messages.projectDeleted'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function archiveDestroy($id)
    {

        Project::destroy($id);

        return Reply::success(__('messages.projectArchiveSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function archiveRestore($id)
    {

        $project = Project::withTrashed()->findOrFail($id);
        $project->restore();

        return Reply::success(__('messages.projectRevertSuccessfully'));
    }

    public function archiveData(Request $request)
    {
        $projects = Project::select('id', 'project_name', 'start_date', 'deadline', 'client_id', 'completion_percent');

        if (!is_null($request->status) && $request->status != 'all') {
            if ($request->status == 'incomplete') {
                $projects->where('completion_percent', '<', '100');
            } elseif ($request->status == 'complete') {
                $projects->where('completion_percent', '=', '100');
            }
        }

        // old method
//        if (!is_null($request->client_id) && $request->client_id != 'all') {
//            $projects->where('client_id', $request->client_id);
//        }
        
         // new method
        if (!is_null($request->client_id) && $request->client_id != 'all') {
             $projects->join('project_clients', 'project_clients.project_id', '=', 'projects.id');
             $projects->where('project_clients.client_id', $request->client_id);
        }

        $projects->onlyTrashed()->get();

        return DataTables::of($projects)
            ->addColumn('action', function ($row) {
                return '
                      <a href="javascript:;" class="btn btn-info btn-circle revert"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Restore"><i class="fa fa-undo" aria-hidden="true"></i></a>
                       <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->addColumn('members', function ($row) {
                $members = '';

                if (count($row->members) > 0) {
                    foreach ($row->members as $member) {
                        if($member->user->image) {
                            $members .= '<img data-toggle="tooltip" data-original-title="' . ucwords($member->user->name) . '" src="' . $member->user->image_url . '"
                        alt="user" class="img-circle" width="30" height="30"> ';
                        } else {
                            $members .= '<span class="nameletter">'.company_initials().'</span>';
                        }
                        
                    }
                } else {
                    $members .= __('messages.noMemberAddedToProject');
                }
                return $members;
            })
            ->editColumn('project_name', function ($row) {
                return ucfirst($row->project_name);
            })
            ->editColumn('start_date', function ($row) {
                return $row->start_date->format('d M, Y');
            })
            ->editColumn('deadline', function ($row) {
                if ($row->deadline) {
                    return $row->deadline->format($this->global->date_format);
                }

                return '-';
            })
            ->editColumn('client_id', function ($row) {
                if (is_null($row->client_id)) {
                    return "";
                }
                return ucwords($row->client->name);
            })
            ->editColumn('completion_percent', function ($row) {
                if ($row->completion_percent < 50) {
                    $statusColor = 'danger';
                    $status = __('app.progress');
                } elseif ($row->completion_percent >= 50 && $row->completion_percent < 75) {
                    $statusColor = 'warning';
                    $status = __('app.progress');
                } else {
                    $statusColor = 'success';
                    $status = __('app.progress');

                    if ($row->completion_percent >= 100) {
                        $status = __('app.completed');
                    }
                }

                return '<h5>' . $status . '<span class="pull-right">' . $row->completion_percent . '%</span></h5><div class="progress">
                  <div class="progress-bar progress-bar-' . $statusColor . '" aria-valuenow="' . $row->completion_percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $row->completion_percent . '%" role="progressbar"> <span class="sr-only">' . $row->completion_percent . '% Complete</span> </div>
                </div>';
            })
            ->removeColumn('project_summary')
            ->removeColumn('notes')
            ->removeColumn('category_id')
            ->removeColumn('feedback')
            ->removeColumn('start_date')
            ->rawColumns(['project_name', 'action', 'completion_percent', 'members'])
            ->make(true);
    }

    public function export($status = null, $clientID = null)
    {
        $projects = Project::leftJoin('users', 'users.id', '=', 'projects.client_id')
            ->leftJoin('project_category', 'project_category.id', '=', 'projects.category_id')
            ->select(
                'projects.id',
                'projects.project_name',
                'users.name',
                'project_category.category_name',
                'projects.start_date',
                'projects.deadline',
                'projects.completion_percent',
                'projects.created_at'
            );
        if (!is_null($status) && $status != 'all') {
            if ($status == 'incomplete') {
                $projects = $projects->where('completion_percent', '<', '100');
            } elseif ($status == 'complete') {
                $projects = $projects->where('completion_percent', '=', '100');
            }
        }

        // old method
//        if (!is_null($clientID) && $clientID != 'all') {
//            $projects = $projects->where('client_id', $clientID);
//        }
        
        // new method
        if (!is_null($clientID) && $clientID != 'all') {
             $projects->join('project_clients', 'project_clients.project_id', '=', 'projects.id');
             $projects->where('project_clients.client_id', $clientID);
        }

        $projects = $projects->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Project Name', 'Client Name', 'Category', 'Start Date', 'Deadline', 'Completion Percent', 'Created at'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($projects as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('Projects', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Projects');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('Projects file');

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

    public function gantt($ganttProjectId = '')
    {

        $this->ganttProjectId = $ganttProjectId;
        //        return $ganttData;
        if ($ganttProjectId != '') {
            $this->project = Project::find($ganttProjectId);
        }
        return view('admin.projects.gantt', $this->data);
    }
    
    public function freeFlowGantt()
    {   
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/free-flow-gantt'));
        
        $this->ganttProjectId = '';
        $this->projects = Project::select('id', 'project_name')->get();
        
        return view('admin.projects.free-flow-gantt', $this->data);
    }

    public function ganttData($ganttProjectId = '')
    {

        $data = array();
        $links = array();

        $projects = Project::select('id', 'project_name', 'start_date', 'deadline', 'completion_percent');

        if ($ganttProjectId != '') {
            $projects = $projects->where('id', '=', $ganttProjectId);
        }

        $projects = $projects->get();

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
                'project_id' => $project->id,
                'dependent_task_id' => null
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
                    'taskid' => $task->id,
                    'dependent_task_id' => $task->dependent_task_id
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

    public function updateTaskDuration(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
        $task->save();

        return Reply::success('messages.taskUpdatedSuccessfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $project = Project::find($id)
            ->update([
                'status' => $request->status
            ]);

        return Reply::dataOnly(['status' => 'success']);
    }

    public function ajaxCreate(Request $request, $projectId)
    {
        $this->pageName = 'ganttChart';

        $this->projectId = $projectId;
        $this->projects = Project::all();
        $this->employees = ProjectMember::byProject($projectId);

        $this->parentGanttId = $request->parent_gantt_id;
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        $this->allTasks = [];
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('project_id', $projectId)
                ->get();
        }

        return view('admin.tasks.ajax_create', $this->data);
    }

    public function burndownChart(Request $request, $id)
    {

        $this->project = Project::with(['tasks' => function ($query) use ($request) {
            if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
                $query->where(DB::raw('DATE(`start_date`)'), '>=', Carbon::createFromFormat($this->global->date_format, $request->startDate));
            }

            if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
                $query->where(DB::raw('DATE(`due_date`)'), '<=', Carbon::createFromFormat($this->global->date_format, $request->endDate));
            }
        }])->find($id);

        $this->totalTask = $this->project->tasks->count();
        $datesArray = [];
        $startDate = $request->startDate ? Carbon::createFromFormat($this->global->date_format, $request->startDate) : Carbon::parse($this->project->start_date);
        if ($this->project->deadline) {
            $endDate = $request->endDate ? Carbon::createFromFormat($this->global->date_format, $request->endDate) : Carbon::parse($this->project->deadline);
        } else {
            //$endDate = $request->endDate ? Carbon::parse($request->endDate) : Carbon::now();
            $endDate = $request->endDate ? Carbon::createFromFormat($this->global->date_format, $request->endDate) : Carbon::now();
        }

        for ($startDate; $startDate <= $endDate; $startDate->addDay()) {
            $datesArray[] = $startDate->format($this->global->date_format);
        }

        $uncompletedTasks = [];
        $createdTasks = [];
        $deadlineTasks = [];
        $deadlineTasksCount = [];
        $this->datesArray = json_encode($datesArray);
        foreach ($datesArray as $key => $value) {
            if (Carbon::createFromFormat($this->global->date_format, $value)->lessThanOrEqualTo(Carbon::now())) {
                $uncompletedTasks[$key] = $this->project->tasks->filter(function ($task) use ($value) {
                    if (is_null($task->completed_on)) {
                        return true;
                    }
                    return $task->completed_on ? $task->completed_on->greaterThanOrEqualTo(Carbon::createFromFormat($this->global->date_format, $value)) : false;
                })->count();
                $createdTasks[$key] = $this->project->tasks->filter(function ($task) use ($value) {
                    return Carbon::createFromFormat($this->global->date_format, $value)->startOfDay()->equalTo($task->created_at->startOfDay());
                })->count();
                if ($key > 0) {
                    $uncompletedTasks[$key] += $createdTasks[$key];
                }
            }
            $deadlineTasksCount[] = $this->project->tasks->filter(function ($task) use ($value) {
                return Carbon::createFromFormat($this->global->date_format, $value)->startOfDay()->equalTo($task->due_date->startOfDay());
            })->count();
            if ($key == 0) {
                $deadlineTasks[$key] = $this->totalTask - $deadlineTasksCount[$key];
            } else {
                $newKey = $key - 1;
                $deadlineTasks[$key] = $deadlineTasks[$newKey] - $deadlineTasksCount[$key];
            }
        }

        $this->uncompletedTasks = json_encode($uncompletedTasks);
        $this->deadlineTasks = json_encode($deadlineTasks);
        if ($request->ajax()) {
            return $this->data;
        }

        $this->startDate = $request->startDate ? Carbon::parse($request->startDate)->format($this->global->date_format) : Carbon::parse($this->project->start_date)->format($this->global->date_format);
        $this->endDate = $endDate->format($this->global->date_format);

        return view('admin.projects.burndown', $this->data);
    }

    /**
     * Project discussions
     *
     * @param  int $projectId
     * @return \Illuminate\Http\Response
     */
    public function discussion(DiscussionDataTable $dataTable, $projectId)
    {
        $this->project = Project::findOrFail($projectId);
        $this->discussionCategories = DiscussionCategory::orderBy('order', 'asc')->get();
        return $dataTable->with('project_id', $projectId)->render('admin.projects.discussion.show', $this->data);
    }

    /**
     * Project discussions
     *
     * @param  int $projectId
     * @param  int $discussionId
     * @return \Illuminate\Http\Response
     */
    public function discussionReplies($projectId, $discussionId)
    {
        $this->project = Project::findOrFail($projectId);
        $this->discussion = Discussion::with('category')->findOrFail($discussionId);
        $this->discussionReplies = DiscussionReply::with('user')->where('discussion_id', $discussionId)->orderBy('id', 'asc')->get();
        return view('admin.projects.discussion.replies', $this->data);
    }
    
    public function showNotes($id)
    {
        $this->project = Project::findOrFail($id)->withCustomFields();
        
        $this->projectNotes = ProjectNote::where('project_id', '=', $id)->orderBy('id', 'desc')->get();

        return view('admin.projects.notes', $this->data);
    }
    
     public function downloadTemplate()
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=projects-smaple-template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $records = array();
        $records[] = array('project_name' => 'Project 1', 'project_category'=>'General Design', 'start_date'=>'19-08-2021', 'deadline'=>'26-09-2021', 'without_deadline'=>'no', 'allow_manual_time'=>'yes', 'project_summary'=> 'abc', 'note' => 'test note 1', 'client_email'=>'abc@gmail.con', 'client_name'=>'ABC', 'client_can_manage_tasks'=>'yes', 'project_budget'=>'1000', 'hours_allocated'=>'500', 'project_status'=>'In Progress');
        $records[] = array('project_name' => 'Project 2', 'project_category'=>'Home Decorate', 'start_date'=>'20-08-2021', 'deadline'=>'', 'without_deadline'=>'yes', 'allow_manual_time'=>'no', 'project_summary'=> 'xtz', 'note' => 'test note 1', 'client_email'=>'xyz@gmail.con', 'client_name'=>'XYZ', 'client_can_manage_tasks'=>'no', 'project_budget'=>'800', 'hours_allocated'=>'700', 'project_status'=>'On Hold');
        
        $records[] = array('project_name' => 'Note', 'project_category'=>'Date format must be DD-MM-YYYY', 'start_date'=>'', 'deadline'=>'', 'without_deadline'=>'', 'allow_manual_time'=>'', 'project_summary'=> '', 'note' => '', 'client_email'=>'', 'client_name'=>'', 'client_can_manage_tasks'=>'', 'project_budget'=>'', 'hours_allocated'=>'', 'project_status'=>'');
       
        $columns = array('Project Name', 'Project Category', 'Start Date', 'Deadline', 'Without Deadline', 'Allow manual time', 'Project Summary', 'Note', 'Client Email', 'Client Name', 'Client can manage tasks', 'Project Budget' , 'Hours Allocated', 'Project Status');
        
        $callback = function() use ($records, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($records as $record) {
                fputcsv($file, array($record['project_name'], $record['project_category'], $record['start_date'], $record['deadline'], $record['without_deadline'], $record['allow_manual_time'], $record['project_summary'], $record['note'], $record['client_email'], $record['client_name'], $record['client_can_manage_tasks'], $record['project_budget'], $record['hours_allocated'], $record['project_status']));
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
                    // mandotry only name field
                    if(!empty($importData[0])) {
                        $this->addImported($importData);
                    }
                }
                
                \Session::put('success', 'Import Successful.');
                return redirect(route('admin.projects.index'));
            } else {
                \Session::put('error', 'File too large. File must be less than 2MB.');
                return redirect(route('admin.projects.index'));
            }
        } else {
            \Session::put('error', 'Invalid File Extension.');
            return redirect(route('admin.projects.index'));
        }
        } else {
            \Session::put('error', 'Select File.');
            return redirect(route('admin.projects.index'));
        }
    }
    
    public function addImported($req){
        
        //$req[0] // Project Name
        //$req[1] // Project Category
        //$req[2] // Start Date
        //$req[3] // Deadline
        //$req[4] // Without Deadline
        //$req[5] // Allow manual time
        //$req[6] // Project Summary
        //$req[7] // Note
        //$req[8] // Client Email
        //$req[9] // Client Name
        //$req[10] // Client can manage tasks
        //$req[11] // Project Budget
        //$req[12] // Hours Allocated
        //$req[13] // Project Status
        
        $userID = '';
        $categoryID = null;
        
        // check if client is set 
        if(isset($req[8]) && !empty($req[8])) {
            $email = trim($req[8]);
            
            $existing_user = User::withoutGlobalScope(CompanyScope::class)->select('id', 'email')->where('email', $email)->first();
           
            if(!$existing_user) {
                $password = str_random(8);
                // create new user
                $user = new User();
                $user->name = isset($req[9]) ? $req[9] : 'no name';
                $user->email = $email;
                $user->password = Hash::make($password);
                $user->save();
                $userID = $user->id;
                
                // attach role
                $role = Role::where('name', 'client')->first();
                $user->attachRole($role->id);
            } else {
                $userID = $existing_user->id;
            }
            
            $existing_client_count = ClientDetails::select('id', 'email', 'company_id')->where(['email' => $email])->count();
            if ($existing_client_count === 0) {
                $client = new ClientDetails();
                $client->user_id = $existing_user ? $existing_user->id : $user->id;
                $client->email = isset($req[8]) ? $req[8] : '';
                $client->name = isset($req[9]) ? $req[9] : '';
                $client->save();
                 // attach role
                if ($existing_user) {
                    $role = Role::where('name', 'client')->first();
                    $roleUser = RoleUser::where('role_id', $role->id)->where('user_id', $existing_user->id)->first();
                    if(!$roleUser) {
                        $existing_user->attachRole($role->id);
                    }
                }
            }
        }
        // client script end
        
        // project category
        
        if(isset($req[1]) && !empty($req[1])) {
            $category = ProjectCategory::where('category_name', 'like', $req[1])->first();
            if(!$category) {
                $category = new ProjectCategory();
                $category->category_name = $req[1];
                $category->save();
            }
            $categoryID = $category->id;
        }
        
        // project category end 
        
        $project = new Project();
        $project->project_name = isset($req[0]) ? $req[0] : '';
        $project->category_id = $categoryID;
        
        if(isset($req[2]) && !empty($req[2])) {
            $project->start_date = Carbon::createFromFormat('d-m-Y', $req[2])->format('Y-m-d');
        } else {
             $project->start_date = Carbon::now()->format('Y-m-d');
        }
        
        if(isset($req[4]) && !empty($req[4]) && $req[4] == 'no') {
            if(isset($req[3]) && !empty($req[3])) {
                $project->deadline = Carbon::createFromFormat('d-m-Y', $req[3])->format('Y-m-d');
            }
        }
        
        if(isset($req[5]) && !empty($req[5]) && $req[5] == 'yes') {
            $project->manual_timelog = 'enable';
        } else {
            $project->manual_timelog = 'disable';
        }

        $project->project_summary = isset($req[6]) ? $req[6] : '';
        $project->notes = isset($req[7]) ? $req[7] : '';
        
        if(isset($req[10]) && !empty($req[10]) && $req[10] == 'yes') {
            $project->client_view_task = 'enable';
        } else {
            $project->client_view_task = "disable";
        }
        
        // old method
        $project->client_id = $userID? $userID: null;
        $project->project_budget = isset($req[11]) ? $req[11] : '';
        $project->currency_id = $this->global->currency_id;
        $project->hours_allocated = isset($req[12]) ? $req[12] : '';
        $project->status = isset($req[13]) ?  strtolower($req[13]) : '';
        $project->save();
        
        $client_ids = [];
        if($userID != ''){
            $client_ids[] = $userID;
        }
        // new method assign more than one client to project
        ProjectClient::where('project_id', $project->id)->delete();
        if(count($client_ids) > 0) {
            foreach ($client_ids as $client_id) {
                $projectClient = new ProjectClient();
                $projectClient->project_id = $project->id;
                $projectClient->client_id = $client_id;
                $projectClient->save();
            }
        }
    }
}
