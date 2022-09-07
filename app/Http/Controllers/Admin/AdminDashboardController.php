<?php

namespace App\Http\Controllers\Admin;


use App\AttendanceSetting;
use App\Currency;
use App\DashboardWidget;
use App\Helper\Reply;
use App\LeadFollowUp;
use App\Leave;
use App\Project;
use App\ProjectActivity;
use App\ProjectTimeLog;
use App\Task;
use App\TaskboardColumn;
use App\Ticket;
use App\Traits\CurrencyExchange;
use App\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Event;
use App\Lead;
use App\User;
use App\Attendance;
use App\Holiday;
use App\LanguageSetting;
use App\Invoice;

//use Illuminate\Support\Facades\Mail;
//use App\Mail\TestEmail;


class AdminDashboardController extends AdminBaseController
{
    use CurrencyExchange;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.dashboard';
        $this->pageIcon = 'icon-speedometer';
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        
        
        // Added by SB
        
        $customVendor = DB::table('custom_field_groups')->where('company_id', company()->id)->where('name','Vendor')->count();
        if($customVendor == 0) {
            DB::table('custom_field_groups')->insert([
                'name' => 'Vendor',
                'model' => 'App\ClientVendorDetails',
                'company_id' =>  company()->id
            ]);
        }
        
        $customLead = DB::table('custom_field_groups')->where('company_id', company()->id)->where('name','Lead')->count();
        if($customLead == 0) {
            DB::table('custom_field_groups')->insert([
                'name' => 'Lead',
                'model' => 'App\Lead',
                'company_id' =>  company()->id
            ]);
        }
         // Added by SB
        
        
        //Mail::to('shahbazjamil@gmail.com')->send(new TestEmail());
        
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/dashboard'));
        
        // clock in out start
        // Getting Attendance setting data
        $this->attendanceSettings = AttendanceSetting::first();
        //Getting Maximum Check-ins in a day
        $this->maxAttandenceInDay = $this->attendanceSettings->clockin_in_day;
        
        // Getting Current Clock-in if exist
        $this->currenntClockIn = Attendance::where(DB::raw('DATE(clock_in_time)'), Carbon::today()->format('Y-m-d'))
            ->where('user_id', $this->user->id)->whereNull('clock_out_time')->first();
        //        dd($this->currenntClockIn);
        // Getting Today's Total Check-ins
        $this->todayTotalClockin = Attendance::where(DB::raw('DATE(clock_in_time)'), Carbon::today()->format('Y-m-d'))
            ->where('user_id', $this->user->id)->where(DB::raw('DATE(clock_out_time)'), Carbon::today()->format('Y-m-d'))->count();
        
        $currentDate = Carbon::now()->format('Y-m-d');

        // Check Holiday by date
        $this->checkTodayHoliday = Holiday::where('date', $currentDate)->first();
        
        //check office time passed
        $officeEndTime = Carbon::createFromFormat('H:i:s', $this->attendanceSettings->office_end_time, $this->global->timezone)->timestamp;
        $currentTime = Carbon::now()->timezone($this->global->timezone)->timestamp;
        if ($officeEndTime < $currentTime) {
            $this->noClockIn = true;
        }
        
        // clock in out end
        
        
        $taskBoardColumn = TaskboardColumn::all();
        $this->employees = User::all();
        
        $this->timer = ProjectTimeLog::memberActiveTimer($this->user->id); // By PM

        $incompletedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'incomplete';
        })->first();

        $completedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'completed';
        })->first();
        

        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(client_details.id) from `client_details` inner join role_user on role_user.user_id=client_details.user_id inner join users on client_details.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client" AND roles.company_id = ' . $this->user->company_id . ' AND client_details.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalClients'),
                DB::raw('(select count(DISTINCT(users.id)) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" AND users.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                DB::raw('(select count(attendances.id) from `attendances` inner join users as atd_user on atd_user.id=attendances.user_id where DATE(attendances.clock_in_time) = CURDATE()  AND attendances.company_id = ' . $this->user->company_id . ' and atd_user.status = "active") as totalTodayAttendance'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending") AND tickets.company_id = ' . $this->user->company_id . ') as totalUnResolvedTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed") AND tickets.company_id = ' . $this->user->company_id . ') as totalResolvedTickets')
                
                //DB::raw('(select sum(invoices.total) from `invoices` WHERE status = "paid" and invoices.company_id = ' . $this->user->company_id . ') as invoiceRevenue'),
                //DB::raw('(select sum(invoices.total) from `invoices` WHERE status != "paid" and invoices.company_id = ' . $this->user->company_id . ') as  outstandingInvoices'),
                //DB::raw('(select sum(products.markup_fix) from `products` WHERE products.company_id = ' . $this->user->company_id . ') as  productMarkupFix')
            )
            ->first();
        
        // today stats
        $startDate = Carbon::now()->timezone($this->global->timezone);
        $endDate = Carbon::now()->timezone($this->global->timezone);
        
         $this->countsToday = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.status = "in progress" AND projects.company_id = ' . $this->user->company_id . ') as openProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "finished" AND projects.company_id = ' . $this->user->company_id . ') as finishedProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "on hold" AND projects.company_id = ' . $this->user->company_id . ') as holdProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.deadline >= '.$startDate->format('Y-m-d').' AND projects. status != "finished" AND projects.company_id = ' . $this->user->company_id . ') as delayedProjects'),
                
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
               
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <="'.$endDate->format('Y-m-d').'" AND tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <= "'.$endDate->format('Y-m-d').'" AND tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                    
                DB::raw('(select count(leads.id) from `leads` WHERE  leads.created_at >= "'.$startDate->format('Y-m-d').'" AND leads.created_at <= "'.$endDate->format('Y-m-d').'" AND leads.company_id = ' . $this->user->company_id . ') as newLeads'),
                    
                DB::raw('(select sum(payments.amount) from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ') as totalPayments'),
                DB::raw('(select sum(expenses.price) from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ') as totalExpenses'),
                    
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status = "paid" AND invoices.company_id = ' . $this->user->company_id . ') as invoiceRevenue'),
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status != "paid" AND invoices.company_id = ' . $this->user->company_id . ') as outstandingInvoices'),
                DB::raw('(select sum(products.markup_fix) from `products` WHERE  products.created_at  >= "'.$startDate->format('Y-m-d').'" AND products.created_at  <= "'.$endDate->format('Y-m-d').'" AND products.company_id = ' . $this->user->company_id . ') as productMarkupFix'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE  project_time_logs.created_at  >= "'.$startDate->format('Y-m-d').'" AND project_time_logs.created_at  <= "'.$endDate->format('Y-m-d').'" AND project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged')
    
            )
            ->first();
         
         if($this->countsToday->totalProjects > 0) {
            $this->countsToday->onTimeP =  round(($this->countsToday->finishedProjects / $this->countsToday->totalProjects)*100);
            $this->countsToday->delayedP = round(($this->countsToday->delayedProjects / $this->countsToday->totalProjects)*100);
            $this->countsToday->onHoldP  = round(($this->countsToday->holdProjects / $this->countsToday->totalProjects)*100);
         } else {
            $this->countsToday->onTimeP = 0;
            $this->countsToday->delayedP = 0;
            $this->countsToday->onHoldP  = 0;
         }
        
         
        // this week stats
        $startDate = Carbon::now()->timezone($this->global->timezone)->startOfWeek();
        $endDate = Carbon::now()->timezone($this->global->timezone);
        
         $this->countsWeek = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects. status = "in progress" AND projects.company_id = ' . $this->user->company_id . ') as openProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "finished" AND projects.company_id = ' . $this->user->company_id . ') as finishedProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "on hold" AND projects.company_id = ' . $this->user->company_id . ') as holdProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.deadline >= '.$startDate->format('Y-m-d').' AND projects. status != "finished" AND projects.company_id = ' . $this->user->company_id . ') as delayedProjects'),
                
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
               
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <="'.$endDate->format('Y-m-d').'" AND tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <= "'.$endDate->format('Y-m-d').'" AND tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                    
                DB::raw('(select count(leads.id) from `leads` WHERE  leads.created_at >= "'.$startDate->format('Y-m-d').'" AND leads.created_at <= "'.$endDate->format('Y-m-d').'" AND leads.company_id = ' . $this->user->company_id . ') as newLeads'),
                DB::raw('(select sum(payments.amount) from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ') as totalPayments'),
                DB::raw('(select sum(expenses.price) from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ') as totalExpenses'),
                    
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status = "paid" AND invoices.company_id = ' . $this->user->company_id . ') as invoiceRevenue'),
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status != "paid" AND invoices.company_id = ' . $this->user->company_id . ') as outstandingInvoices'),
                DB::raw('(select sum(products.markup_fix) from `products` WHERE  products.created_at  >= "'.$startDate->format('Y-m-d').'" AND products.created_at  <= "'.$endDate->format('Y-m-d').'" AND products.company_id = ' . $this->user->company_id . ') as productMarkupFix'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE  project_time_logs.created_at  >= "'.$startDate->format('Y-m-d').'" AND project_time_logs.created_at  <= "'.$endDate->format('Y-m-d').'" AND project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged')
            )
            ->first();
         if($this->countsWeek->totalProjects > 0) {
             
            $this->countsWeek->onTimeP =  round(($this->countsWeek->finishedProjects / $this->countsWeek->totalProjects)*100);
             $this->countsWeek->delayedP = round(($this->countsWeek->delayedProjects / $this->countsWeek->totalProjects)*100);
             $this->countsWeek->onHoldP  = round(($this->countsWeek->holdProjects / $this->countsWeek->totalProjects)*100);
             
         } else {
             
             $this->countsWeek->onTimeP =  0;
             $this->countsWeek->delayedP = 0;
             $this->countsWeek->onHoldP  = 0;
             
         }
         
         
          // this month stats
        $startDate = Carbon::now()->timezone($this->global->timezone)->startOfMonth();
        $endDate = Carbon::now()->timezone($this->global->timezone);
        
         $this->countsMonth = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects. status = "in progress" AND projects.company_id = ' . $this->user->company_id . ') as openProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "finished" AND projects.company_id = ' . $this->user->company_id . ') as finishedProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "on hold" AND projects.company_id = ' . $this->user->company_id . ') as holdProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.deadline >= '.$startDate->format('Y-m-d').' AND projects. status != "finished" AND projects.company_id = ' . $this->user->company_id . ') as delayedProjects'),
                
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
               
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <="'.$endDate->format('Y-m-d').'" AND tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <= "'.$endDate->format('Y-m-d').'" AND tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                    
                DB::raw('(select count(leads.id) from `leads` WHERE  leads.created_at >= "'.$startDate->format('Y-m-d').'" AND leads.created_at <= "'.$endDate->format('Y-m-d').'" AND leads.company_id = ' . $this->user->company_id . ') as newLeads'),
                DB::raw('(select sum(payments.amount) from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ') as totalPayments'),
                DB::raw('(select sum(expenses.price) from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ') as totalExpenses'),
                    
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status = "paid" AND invoices.company_id = ' . $this->user->company_id . ') as invoiceRevenue'),
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status != "paid" AND invoices.company_id = ' . $this->user->company_id . ') as outstandingInvoices'),
                DB::raw('(select sum(products.markup_fix) from `products` WHERE  products.created_at  >= "'.$startDate->format('Y-m-d').'" AND products.created_at  <= "'.$endDate->format('Y-m-d').'" AND products.company_id = ' . $this->user->company_id . ') as productMarkupFix'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE  project_time_logs.created_at  >= "'.$startDate->format('Y-m-d').'" AND project_time_logs.created_at  <= "'.$endDate->format('Y-m-d').'" AND project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged')
            )
            ->first();
         
         
        // this year stats
        $startDate = Carbon::now()->timezone($this->global->timezone)->startOfYear();
        $endDate = Carbon::now()->timezone($this->global->timezone);
        
         $this->countsYear = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects. status = "in progress" AND projects.company_id = ' . $this->user->company_id . ') as openProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "finished" AND projects.company_id = ' . $this->user->company_id . ') as finishedProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "on hold" AND projects.company_id = ' . $this->user->company_id . ') as holdProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.deadline >= '.$startDate->format('Y-m-d').' AND projects. status != "finished" AND projects.company_id = ' . $this->user->company_id . ') as delayedProjects'),
                
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
               
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <="'.$endDate->format('Y-m-d').'" AND tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <= "'.$endDate->format('Y-m-d').'" AND tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                    
                DB::raw('(select count(leads.id) from `leads` WHERE  leads.created_at >= "'.$startDate->format('Y-m-d').'" AND leads.created_at <= "'.$endDate->format('Y-m-d').'" AND leads.company_id = ' . $this->user->company_id . ') as newLeads'),
                DB::raw('(select sum(payments.amount) from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ') as totalPayments'),
                DB::raw('(select sum(expenses.price) from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ') as totalExpenses'),
                    
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status = "paid" AND invoices.company_id = ' . $this->user->company_id . ') as invoiceRevenue'),
                DB::raw('(select sum(invoices.total) from `invoices` WHERE  invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.status != "paid" AND invoices.company_id = ' . $this->user->company_id . ') as outstandingInvoices'),
                DB::raw('(select sum(products.markup_fix) from `products` WHERE  products.created_at  >= "'.$startDate->format('Y-m-d').'" AND products.created_at  <= "'.$endDate->format('Y-m-d').'" AND products.company_id = ' . $this->user->company_id . ') as productMarkupFix'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE  project_time_logs.created_at  >= "'.$startDate->format('Y-m-d').'" AND project_time_logs.created_at  <= "'.$endDate->format('Y-m-d').'" AND project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged')
            )
            ->first();
         
         // current year stats monthly
        $startDate = Carbon::now()->timezone($this->global->timezone)->startOfYear();
        $endDate = Carbon::now()->timezone($this->global->timezone)->endOfYear();
        
        $paymentsData = DB::select('select sum(payments.amount) AS toatl_amount  , DATE_FORMAT(payments.paid_on,"%m-%Y") AS paid_on_date  from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ' GROUP BY paid_on_date ORDER BY paid_on_date ASC');
        $expensesData = DB::select('select sum(expenses.price) AS toatl_price, DATE_FORMAT(expenses.purchase_date,"%m-%Y") AS purchased_date  from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ' GROUP BY purchased_date ORDER BY purchased_date ASC');
        
        $year = $startDate->year;
        $paymentsArr = array("01-$year" => 0, "02-$year" => 0, "03-$year" => 0, "04-$year" => 0, "05-$year" => 0, "06-$year" => 0, "07-$year" => 0, "08-$year" => 0, "09-$year" => 0, "10-$year" => 0, "11-$year" => 0, "12-$year" => 0);
        $expensesArr = array("01-$year" => 0, "02-$year" => 0, "03-$year" => 0, "04-$year" => 0, "05-$year" => 0, "06-$year" => 0, "07-$year" => 0, "08-$year" => 0, "09-$year" => 0, "10-$year" => 0, "11-$year" => 0, "12-$year" => 0);
        
        if($paymentsData) {
            foreach ($paymentsData as $payment) {
                $paymentsArr[$payment->paid_on_date] = $payment->toatl_amount;
            }
        }
        
        if($expensesData) {
            foreach ($expensesData as $expense) {
                $expensesArr[$expense->purchased_date] = $expense->toatl_price;
            }
        }
        
        $this->paymentsArr = $paymentsArr;
        $this->expensesArr = $expensesArr;
        
         if($this->countsMonth->totalProjects > 0) {
             $this->countsMonth->onTimeP =  round(($this->countsMonth->finishedProjects / $this->countsMonth->totalProjects)*100);
         $this->countsMonth->delayedP = round(($this->countsMonth->delayedProjects / $this->countsMonth->totalProjects)*100);
         $this->countsMonth->onHoldP  = round(($this->countsMonth->holdProjects / $this->countsMonth->totalProjects)*100);
             
             
         } else {
             
             $this->countsMonth->onTimeP =  0;
         $this->countsMonth->delayedP = 0;
         $this->countsMonth->onHoldP  = 0;
             
         }
         

        $timeLog = intdiv($this->counts->totalHoursLogged, 60) . ' ' . __('modules.hrs');
        if (($this->counts->totalHoursLogged % 60) > 0) {
            $timeLog .= ($this->counts->totalHoursLogged % 60) . ' ' . __('modules.mins');
        }
        $this->counts->totalHoursLogged = $timeLog;

        $this->pendingTasks = Task::with('project')
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->where(DB::raw('DATE(due_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->select('tasks.*')
            ->limit(15)
            ->get();

        $this->pendingLeadFollowUps = LeadFollowUp::with('lead')->where(DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.next_follow_up', 'yes')
            ->where('leads.company_id', company()->id)
            ->get();

        $this->newTickets = Ticket::where('status', 'open')
            ->orderBy('id', 'desc')->get();

        $this->projectActivities = ProjectActivity::with('project')
            ->join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->whereNull('projects.deleted_at')->select('project_activity.*')
            ->limit(15)->orderBy('id', 'desc')->get();
        $this->userActivities = UserActivity::with('user')->limit(15)->orderBy('id', 'desc')->get();

        $this->feedbacks = Project::with('client')->whereNotNull('feedback')->limit(5)->get();


        // earning chart

        $this->fromDate = Carbon::today()->timezone($this->global->timezone)->subDays(60);
        $this->toDate = Carbon::today()->timezone($this->global->timezone);
        $invoices = DB::table('payments')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->whereDate('paid_on', '>=', $this->fromDate->format('Y-m-d'))
            ->whereDate('paid_on', '<=', $this->toDate->format('Y-m-d'))
            ->where('payments.status', 'complete')
            ->where('payments.company_id', company()->id)
            ->groupBy('paid_on')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%Y-%m-%d") as date'),
                DB::raw('sum(amount) as total'),
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            ]);

        $chartData = array();
        foreach ($invoices as $chart) {
            if ($chart->currency_code != $this->global->currency->currency_code) {
                if ($chart->is_cryptocurrency == 'yes') {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $usdTotal = ($chart->total * $chart->usd_price);
                            $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                        }
                    } else {
                        $usdTotal = ($chart->total * $chart->usd_price);
                        $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                    }
                } else {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                        }
                    } else {
                        $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                    }
                }
            } else {
                $chartData[] = ['date' => $chart->date, 'total' => round($chart->total, 2)];
            }
        }

        $this->chartData = json_encode($chartData);
        $this->leaves = Leave::with('user', 'type')->where('status', '<>', 'rejected')
            ->whereYear('leave_date', Carbon::now()->timezone($this->global->timezone)->format('Y'))
            ->get();
        
        $this->events = Event::all();

        $this->widgets       = DashboardWidget::all();

        $this->activeWidgets = DashboardWidget::where('status', 1)->get()->pluck('widget_name')->toArray();
        
        $startDate = Carbon::now()->subWeek(4)->timezone($this->global->timezone);
        $this->newLeads = Lead::where(DB::raw('DATE(created_at)'), '>=', $startDate->format('Y-m-d'))
            ->orderBy('created_at', 'ASC')
            ->select('leads.*')
            ->get();
        
        
        $this->cur_time = Carbon::now()->timezone($this->global->timezone)->format('h:i A');
        
        
        $dueTaskArr = array();
        
        $dueTasks = Task::with('project')
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->where(DB::raw('DATE(due_date)'), '>=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->select('tasks.*')
            ->limit(100)
            ->get();
        if($dueTasks) {
            foreach ($dueTasks as $task){
                
                
                $timeLog = ProjectTimeLog::with('task', 'project')->join('users', 'users.id', '=', 'project_time_logs.user_id')
                ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
                ->leftJoin('tasks', 'tasks.id', '=', 'project_time_logs.task_id')
                ->leftJoin('projects', 'projects.id', '=', 'project_time_logs.project_id')
                ->where('tasks.id', '=', $task->id);
                //'employee_details.hourly_rate'

                $timeLog = $timeLog->select('project_time_logs.id', 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.total_hours', 'project_time_logs.total_minutes', 'project_time_logs.memo', 'project_time_logs.user_id', 'project_time_logs.project_id', 'project_time_logs.task_id', 'users.name', 'project_time_logs.earnings', 'project_time_logs.approved');
                $timeLog = $timeLog->get();
                
                $total_minutes = 0;
                if($timeLog) {
                    foreach ($timeLog as $tl) {
                        $total_minutes = $total_minutes + $tl->total_minutes;

                    }
                }
                
                $init = $total_minutes*60;
                $hours = floor($init / 3600);
                $mins = floor(($init / 60) % 60);
                $secs = $init % 60;
                
                if($hours < 10){
                    $hours = '0'.$hours;
                }
                if($mins < 10){
                    $mins = '0'.$mins;
                }
                if($secs < 10){
                    $secs = '0'.$secs;
                }
                
                $total_time = $hours.':'.$mins.':'.$secs;
                
                
                $diff = now()->diffInDays($task->due_date);
                
                $tData = array();
                $tData['id'] = $task->id;
                $tData['title'] = $task->heading ? ucfirst($task->heading) : '';
                $tData['project_name'] = $task->project_id ? ucfirst($task->project_name) : '';
                $tData['project_id'] = $task->project_id ? $task->project_id : '';
                $tData['is_completed'] =false;
                if(isset($task->board_column_id) && $task->board_column && $task->board_column->slug == 'completed') {
                    $tData['is_completed'] = true;
                }
                $tData['project_id'] = $task->project_id ? $task->project_id : '';
                
                $tData['due_date'] = '';
                $tData['due_day'] = '';
                if($diff > 5) {
                    $tData['due_date'] = $task->due_date->format($this->global->date_format);;
                } else {
                     $tData['due_day'] = $diff;
                }
                
                $tData['logged_time'] = $total_time;
                
                $dueTaskArr[] = $tData;
                
            }
        }
        $this->dueTaskArr = $dueTaskArr;
    
        $this->projects = Project::where('status', '<>', 'finished')
            //->where(DB::raw('DATE(due_date)'), '>=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            //->orderBy('due_date', 'desc')
            ->select('*')
            //->limit(5)
            ->get();
        
        $this->invoices = Invoice::where('status', '=', 'unpaid')
            //->orderBy('due_date', 'desc')
            ->select('*')
            ->limit(4)
            ->get();
        
        $this->invoiceRevenueToday = number_format($this->countsToday->invoiceRevenue);
        $this->invoiceRevenueWeek = number_format($this->countsWeek->invoiceRevenue);
        $this->invoiceRevenueMonth = number_format($this->countsMonth->invoiceRevenue);
        $this->invoiceRevenueYear = number_format($this->countsYear->invoiceRevenue);
        
        $this->outstandingInvoicesToday = number_format($this->countsToday->outstandingInvoices);
        $this->outstandingInvoicesWeek = number_format($this->countsWeek->outstandingInvoices);
        $this->outstandingInvoicesMonth = number_format($this->countsMonth->outstandingInvoices);
        $this->outstandingInvoicesYear= number_format($this->countsYear->outstandingInvoices);
        
        $this->productMarkupFixToday = number_format($this->countsToday->productMarkupFix);
        $this->productMarkupFixWeek = number_format($this->countsWeek->productMarkupFix);
        $this->productMarkupFixMonth = number_format($this->countsMonth->productMarkupFix);
        $this->productMarkupFixYear = number_format($this->countsYear->productMarkupFix);
        
        
        
        
       $this->totalHoursLoggedToday = $timeLog = intdiv($this->countsToday->totalHoursLogged, 60);
       $this->totalHoursLoggedWeek = $timeLog = intdiv($this->countsWeek->totalHoursLogged, 60);
       $this->totalHoursLoggedMonth = $timeLog = intdiv($this->countsMonth->totalHoursLogged, 60);
       $this->totalHoursLoggedYear = $timeLog = intdiv($this->countsYear->totalHoursLogged, 60);
       
        $this->projects = Project::all();
       
        
        
    //var_dump($this->newLeads);exit;

    return view('admin.dashboard.new', $this->data);
        
    }
    public function index_bk()
    {
        $taskBoardColumn = TaskboardColumn::all();
        
        $this->timer = ProjectTimeLog::memberActiveTimer($this->user->id); // By PM

        $incompletedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'incomplete';
        })->first();

        $completedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'completed';
        })->first();


        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(client_details.id) from `client_details` inner join role_user on role_user.user_id=client_details.user_id inner join users on client_details.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client" AND roles.company_id = ' . $this->user->company_id . ' AND client_details.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalClients'),
                DB::raw('(select count(DISTINCT(users.id)) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" AND users.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                DB::raw('(select count(attendances.id) from `attendances` inner join users as atd_user on atd_user.id=attendances.user_id where DATE(attendances.clock_in_time) = CURDATE()  AND attendances.company_id = ' . $this->user->company_id . ' and atd_user.status = "active") as totalTodayAttendance'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending") AND tickets.company_id = ' . $this->user->company_id . ') as totalUnResolvedTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed") AND tickets.company_id = ' . $this->user->company_id . ') as totalResolvedTickets')
            )
            ->first();

        $timeLog = intdiv($this->counts->totalHoursLogged, 60) . ' ' . __('modules.hrs');

        if (($this->counts->totalHoursLogged % 60) > 0) {
            $timeLog .= ($this->counts->totalHoursLogged % 60) . ' ' . __('modules.mins');
        }

        $this->counts->totalHoursLogged = $timeLog;

        $this->pendingTasks = Task::with('project')
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->where(DB::raw('DATE(due_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->select('tasks.*')
            ->limit(15)
            ->get();

        $this->pendingLeadFollowUps = LeadFollowUp::with('lead')->where(DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.next_follow_up', 'yes')
            ->where('leads.company_id', company()->id)
            ->get();

        $this->newTickets = Ticket::where('status', 'open')
            ->orderBy('id', 'desc')->get();

        $this->projectActivities = ProjectActivity::with('project')
            ->join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->whereNull('projects.deleted_at')->select('project_activity.*')
            ->limit(15)->orderBy('id', 'desc')->get();
        $this->userActivities = UserActivity::with('user')->limit(15)->orderBy('id', 'desc')->get();

        $this->feedbacks = Project::with('client')->whereNotNull('feedback')->limit(5)->get();



        // earning chart

        $this->fromDate = Carbon::today()->timezone($this->global->timezone)->subDays(60);
        $this->toDate = Carbon::today()->timezone($this->global->timezone);
        $invoices = DB::table('payments')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->whereDate('paid_on', '>=', $this->fromDate->format('Y-m-d'))
            ->whereDate('paid_on', '<=', $this->toDate->format('Y-m-d'))
            ->where('payments.status', 'complete')
            ->where('payments.company_id', company()->id)
            ->groupBy('paid_on')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%Y-%m-%d") as date'),
                DB::raw('sum(amount) as total'),
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            ]);

        $chartData = array();
        foreach ($invoices as $chart) {
            if ($chart->currency_code != $this->global->currency->currency_code) {
                if ($chart->is_cryptocurrency == 'yes') {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $usdTotal = ($chart->total * $chart->usd_price);
                            $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                        }
                    } else {
                        $usdTotal = ($chart->total * $chart->usd_price);
                        $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                    }
                } else {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                        }
                    } else {
                        $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                    }
                }
            } else {
                $chartData[] = ['date' => $chart->date, 'total' => round($chart->total, 2)];
            }
        }

        $this->chartData = json_encode($chartData);
        $this->leaves = Leave::with('user', 'type')->where('status', '<>', 'rejected')
            ->whereYear('leave_date', Carbon::now()->timezone($this->global->timezone)->format('Y'))
            ->get();
        
        $this->events = Event::all();

        $this->widgets       = DashboardWidget::all();

        $this->activeWidgets = DashboardWidget::where('status', 1)->get()->pluck('widget_name')->toArray();
        
        $startDate = Carbon::now()->subWeek(4)->timezone($this->global->timezone);
        $this->newLeads = Lead::where(DB::raw('DATE(created_at)'), '>=', $startDate->format('Y-m-d'))
            ->orderBy('created_at', 'ASC')
            ->select('leads.*')
            ->get();
        
        
    //var_dump($this->newLeads);exit;

        return view('admin.dashboard.index', $this->data);
    }
    
    public function dashboard_new()
    {
        $taskBoardColumn = TaskboardColumn::all();
        $this->employees = User::all();
        
        $this->timer = ProjectTimeLog::memberActiveTimer($this->user->id); // By PM

        $incompletedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'incomplete';
        })->first();

        $completedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'completed';
        })->first();
        

        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(client_details.id) from `client_details` inner join role_user on role_user.user_id=client_details.user_id inner join users on client_details.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client" AND roles.company_id = ' . $this->user->company_id . ' AND client_details.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalClients'),
                DB::raw('(select count(DISTINCT(users.id)) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" AND users.company_id = ' . $this->user->company_id . ' and users.status = "active") as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` WHERE project_time_logs.company_id = ' . $this->user->company_id . ') as totalHoursLogged'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                DB::raw('(select count(attendances.id) from `attendances` inner join users as atd_user on atd_user.id=attendances.user_id where DATE(attendances.clock_in_time) = CURDATE()  AND attendances.company_id = ' . $this->user->company_id . ' and atd_user.status = "active") as totalTodayAttendance'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending") AND tickets.company_id = ' . $this->user->company_id . ') as totalUnResolvedTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed") AND tickets.company_id = ' . $this->user->company_id . ') as totalResolvedTickets')
            )
            ->first();
        
        // today stats
        $startDate = Carbon::now()->timezone($this->global->timezone);
        $endDate = Carbon::now()->timezone($this->global->timezone);
        
         $this->countsToday = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.status = "in progress" AND projects.company_id = ' . $this->user->company_id . ') as openProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "finished" AND projects.company_id = ' . $this->user->company_id . ') as finishedProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "on hold" AND projects.company_id = ' . $this->user->company_id . ') as holdProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.deadline >= '.$startDate->format('Y-m-d').' AND projects. status != "finished" AND projects.company_id = ' . $this->user->company_id . ') as delayedProjects'),
                
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
               
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <="'.$endDate->format('Y-m-d').'" AND tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <= "'.$endDate->format('Y-m-d').'" AND tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                    
                DB::raw('(select count(leads.id) from `leads` WHERE  leads.created_at >= "'.$startDate->format('Y-m-d').'" AND leads.created_at <= "'.$endDate->format('Y-m-d').'" AND leads.company_id = ' . $this->user->company_id . ') as newLeads'),
                    
                DB::raw('(select sum(payments.amount) from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ') as totalPayments'),
                DB::raw('(select sum(expenses.price) from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ') as totalExpenses')
            )
            ->first();
         
         if($this->countsToday->totalProjects > 0) {
            $this->countsToday->onTimeP =  round(($this->countsToday->finishedProjects / $this->countsToday->totalProjects)*100);
            $this->countsToday->delayedP = round(($this->countsToday->delayedProjects / $this->countsToday->totalProjects)*100);
            $this->countsToday->onHoldP  = round(($this->countsToday->holdProjects / $this->countsToday->totalProjects)*100);
         } else {
            $this->countsToday->onTimeP = 0;
            $this->countsToday->delayedP = 0;
            $this->countsToday->onHoldP  = 0;
         }
        
         
        // this week stats
        $startDate = Carbon::now()->timezone($this->global->timezone)->startOfWeek();
        $endDate = Carbon::now()->timezone($this->global->timezone);
        
         $this->countsWeek = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects. status = "in progress" AND projects.company_id = ' . $this->user->company_id . ') as openProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "finished" AND projects.company_id = ' . $this->user->company_id . ') as finishedProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "on hold" AND projects.company_id = ' . $this->user->company_id . ') as holdProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.deadline >= '.$startDate->format('Y-m-d').' AND projects. status != "finished" AND projects.company_id = ' . $this->user->company_id . ') as delayedProjects'),
                
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
               
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <="'.$endDate->format('Y-m-d').'" AND tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <= "'.$endDate->format('Y-m-d').'" AND tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                    
                DB::raw('(select count(leads.id) from `leads` WHERE  leads.created_at >= "'.$startDate->format('Y-m-d').'" AND leads.created_at <= "'.$endDate->format('Y-m-d').'" AND leads.company_id = ' . $this->user->company_id . ') as newLeads'),
                DB::raw('(select sum(payments.amount) from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ') as totalPayments'),
                DB::raw('(select sum(expenses.price) from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ') as totalExpenses')
            )
            ->first();
         if($this->countsWeek->totalProjects > 0) {
             
            $this->countsWeek->onTimeP =  round(($this->countsWeek->finishedProjects / $this->countsWeek->totalProjects)*100);
             $this->countsWeek->delayedP = round(($this->countsWeek->delayedProjects / $this->countsWeek->totalProjects)*100);
             $this->countsWeek->onHoldP  = round(($this->countsWeek->holdProjects / $this->countsWeek->totalProjects)*100);
             
         } else {
             
             $this->countsWeek->onTimeP =  0;
             $this->countsWeek->delayedP = 0;
             $this->countsWeek->onHoldP  = 0;
             
         }
         
         
          // this month stats
        $startDate = Carbon::now()->timezone($this->global->timezone)->startOfMonth();
        $endDate = Carbon::now()->timezone($this->global->timezone);
        
        
        
        //echo '(select sum(expenses.price), DATE_FORMAT(expenses.purchase_date,"%m-%Y") AS purchased_date  from `expenses` WHERE  expenses.purchase_date >= "'.$startDateHealth->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDateHealth->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ' GROUP BY purchased_date ORDER BY purchased_date ASC) as totalExpensesHealth';exit;
        
        
         $this->countsMonth = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND  projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.company_id = ' . $this->user->company_id . ') as totalProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.status = "in progress" AND projects.company_id = ' . $this->user->company_id . ') as openProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "finished" AND projects.company_id = ' . $this->user->company_id . ') as finishedProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects. status = "on hold" AND projects.company_id = ' . $this->user->company_id . ') as holdProjects'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.deleted_at IS NULL AND projects.start_date >= "'.$startDate->format('Y-m-d').'" AND projects.start_date <= "'.$endDate->format('Y-m-d').'" AND projects.deadline >= '.$startDate->format('Y-m-d').' AND projects. status != "finished" AND projects.company_id = ' . $this->user->company_id . ') as delayedProjects'),
                
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid" AND invoices.due_date >= "'.$startDate->format('Y-m-d').'" AND invoices.due_date <= "'.$endDate->format('Y-m-d').'" AND invoices.company_id = ' . $this->user->company_id . ') as totalUnpaidInvoices'),
               
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <="'.$endDate->format('Y-m-d').'" AND tasks.board_column_id=' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` WHERE tasks.due_date >= "'.$startDate->format('Y-m-d').'" AND tasks.due_date <= "'.$endDate->format('Y-m-d').'" AND tasks.board_column_id != ' . $completedTaskColumn->id . ' AND tasks.company_id = ' . $this->user->company_id . ') as totalPendingTasks'),
                    
                DB::raw('(select count(leads.id) from `leads` WHERE  leads.created_at >= "'.$startDate->format('Y-m-d').'" AND leads.created_at <= "'.$endDate->format('Y-m-d').'" AND leads.company_id = ' . $this->user->company_id . ') as newLeads'),
                DB::raw('(select sum(payments.amount) from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ') as totalPayments'),
                DB::raw('(select sum(expenses.price) from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ') as totalExpenses')
            )
            ->first();
         
         // current year stats monthly
        $startDate = Carbon::now()->timezone($this->global->timezone)->startOfYear();
        $endDate = Carbon::now()->timezone($this->global->timezone)->endOfYear();
        
        $paymentsData = DB::select('select sum(payments.amount) AS toatl_amount  , DATE_FORMAT(payments.paid_on,"%m-%Y") AS paid_on_date  from `payments` WHERE  payments.paid_on >= "'.$startDate->format('Y-m-d').'" AND payments.paid_on <= "'.$endDate->format('Y-m-d').'" AND payments.status = "complete" AND payments.company_id = ' . $this->user->company_id . ' GROUP BY paid_on_date ORDER BY paid_on_date ASC');
        $expensesData = DB::select('select sum(expenses.price) AS toatl_price, DATE_FORMAT(expenses.purchase_date,"%m-%Y") AS purchased_date  from `expenses` WHERE  expenses.purchase_date >= "'.$startDate->format('Y-m-d').'" AND expenses.purchase_date <= "'.$endDate->format('Y-m-d').'" AND expenses.status = "approved" AND expenses.company_id = ' . $this->user->company_id . ' GROUP BY purchased_date ORDER BY purchased_date ASC');
        
        $year = $startDate->year;
        $paymentsArr = array("01-$year" => 0, "02-$year" => 0, "03-$year" => 0, "04-$year" => 0, "05-$year" => 0, "06-$year" => 0, "07-$year" => 0, "08-$year" => 0, "09-$year" => 0, "10-$year" => 0, "11-$year" => 0, "12-$year" => 0);
        $expensesArr = array("01-$year" => 0, "02-$year" => 0, "03-$year" => 0, "04-$year" => 0, "05-$year" => 0, "06-$year" => 0, "07-$year" => 0, "08-$year" => 0, "09-$year" => 0, "10-$year" => 0, "11-$year" => 0, "12-$year" => 0);
        
        if($paymentsData) {
            foreach ($paymentsData as $payment) {
                $paymentsArr[$payment->paid_on_date] = $payment->toatl_amount;
            }
        }
        
        if($expensesData) {
            foreach ($expensesData as $expense) {
                $expensesArr[$expense->purchased_date] = $expense->toatl_price;
            }
        }
        
        $this->paymentsArr = $paymentsArr;
        $this->expensesArr = $expensesArr;
        
         if($this->countsMonth->totalProjects > 0) {
             $this->countsMonth->onTimeP =  round(($this->countsMonth->finishedProjects / $this->countsMonth->totalProjects)*100);
         $this->countsMonth->delayedP = round(($this->countsMonth->delayedProjects / $this->countsMonth->totalProjects)*100);
         $this->countsMonth->onHoldP  = round(($this->countsMonth->holdProjects / $this->countsMonth->totalProjects)*100);
             
             
         } else {
             
             $this->countsMonth->onTimeP =  0;
         $this->countsMonth->delayedP = 0;
         $this->countsMonth->onHoldP  = 0;
             
         }
         

        $timeLog = intdiv($this->counts->totalHoursLogged, 60) . ' ' . __('modules.hrs');

        if (($this->counts->totalHoursLogged % 60) > 0) {
            $timeLog .= ($this->counts->totalHoursLogged % 60) . ' ' . __('modules.mins');
        }

        $this->counts->totalHoursLogged = $timeLog;

        $this->pendingTasks = Task::with('project')
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->where(DB::raw('DATE(due_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->select('tasks.*')
            ->limit(15)
            ->get();

        $this->pendingLeadFollowUps = LeadFollowUp::with('lead')->where(DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.next_follow_up', 'yes')
            ->where('leads.company_id', company()->id)
            ->get();

        $this->newTickets = Ticket::where('status', 'open')
            ->orderBy('id', 'desc')->get();

        $this->projectActivities = ProjectActivity::with('project')
            ->join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->whereNull('projects.deleted_at')->select('project_activity.*')
            ->limit(15)->orderBy('id', 'desc')->get();
        $this->userActivities = UserActivity::with('user')->limit(15)->orderBy('id', 'desc')->get();

        $this->feedbacks = Project::with('client')->whereNotNull('feedback')->limit(5)->get();


        // earning chart

        $this->fromDate = Carbon::today()->timezone($this->global->timezone)->subDays(60);
        $this->toDate = Carbon::today()->timezone($this->global->timezone);
        $invoices = DB::table('payments')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->whereDate('paid_on', '>=', $this->fromDate->format('Y-m-d'))
            ->whereDate('paid_on', '<=', $this->toDate->format('Y-m-d'))
            ->where('payments.status', 'complete')
            ->where('payments.company_id', company()->id)
            ->groupBy('paid_on')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%Y-%m-%d") as date'),
                DB::raw('sum(amount) as total'),
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            ]);

        $chartData = array();
        foreach ($invoices as $chart) {
            if ($chart->currency_code != $this->global->currency->currency_code) {
                if ($chart->is_cryptocurrency == 'yes') {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $usdTotal = ($chart->total * $chart->usd_price);
                            $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                        }
                    } else {
                        $usdTotal = ($chart->total * $chart->usd_price);
                        $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                    }
                } else {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                        }
                    } else {
                        $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                    }
                }
            } else {
                $chartData[] = ['date' => $chart->date, 'total' => round($chart->total, 2)];
            }
        }

        $this->chartData = json_encode($chartData);
        $this->leaves = Leave::with('user', 'type')->where('status', '<>', 'rejected')
            ->whereYear('leave_date', Carbon::now()->timezone($this->global->timezone)->format('Y'))
            ->get();
        
        $this->events = Event::all();

        $this->widgets       = DashboardWidget::all();

        $this->activeWidgets = DashboardWidget::where('status', 1)->get()->pluck('widget_name')->toArray();
        
        $startDate = Carbon::now()->subWeek(4)->timezone($this->global->timezone);
        $this->newLeads = Lead::where(DB::raw('DATE(created_at)'), '>=', $startDate->format('Y-m-d'))
            ->orderBy('created_at', 'ASC')
            ->select('leads.*')
            ->get();
        
        
        $this->cur_time = Carbon::now()->timezone($this->global->timezone)->format('h:i A');
        
        
    //var_dump($this->newLeads);exit;

    return view('admin.dashboard.new', $this->data);
    }
    
    public function timestamp(){
        $cur_time = Carbon::now()->timezone($this->global->timezone)->format('h:i A');
        echo $cur_time;
    }

    public function widget(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        DashboardWidget::where('status', 1)->update(['status' => 0]);

        foreach ($data as $key => $widget) {
            DashboardWidget::where('widget_name', $key)->update(['status' => 1]);
        }

        return Reply::redirect(route('admin.dashboard'), __('messages.updatedSuccessfully'));
    }
}
