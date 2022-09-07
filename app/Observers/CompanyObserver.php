<?php

namespace App\Observers;

use App\Company;
use App\Currency;
use App\Events\CompanyRegistered;
use App\GdprSetting;
use App\GlobalCurrency;
use App\LeadSource;
use App\LeadStatus;
use App\LeaveType;
use App\LogTimeFor;
use App\MessageSetting;
use App\ModuleSetting;
use App\Package;
use App\PackageSetting;
use App\Role;
use App\TaskboardColumn;
use App\ThemeSetting;
use App\TicketChannel;
use App\TicketGroup;
use App\TicketType;
use App\GlobalSetting;
use App\ProjectSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

Use App\CodeType;
Use App\SalescategoryType;
Use App\EmailTemplate;
use App\ProductSetting;
use App\ProductStatus;

class CompanyObserver
{

    public function created(Company $company)
    {

        // Package setting for get trial package active or not
        $packageSetting = PackageSetting::where('status', 'active')->first();
        $packages = Package::all();

        // get trial package data
        $trialPackage = $packages->filter(function ($value, $key) {
            return $value->default == 'trial';
        })->first();

        // get default package data
        $defaultPackage = $packages->filter(function ($value, $key) {
            return $value->default == 'yes';
        })->first();

        // get another  package data if trial and default package not found
        $otherPackage = $packages->filter(function ($value, $key) {
            return $value->default == 'no';
        })->first();

        // if trial package is active set package to company
        if ($packageSetting && !is_null($trialPackage)) {
            $company->package_id = $trialPackage->id;
            // set company license expire date
            $noOfDays = (!is_null($packageSetting->no_of_days) && $packageSetting->no_of_days != 0) ? $packageSetting->no_of_days : 30;
            $company->licence_expire_on = Carbon::now()->addDays($noOfDays)->format('Y-m-d');
        }

        // if trial package is not active set default package to company
        elseif (!is_null($defaultPackage))
            $company->package_id = $defaultPackage->id;
        else {
            $company->package_id = $otherPackage->id;
        }

        if ($company->date_picker_format == '') {
            $company->date_picker_format = 'dd-mm-yyyy';
        }

        $company->save();

        $this->addTaskBoard($company);
        $this->addTicketChannel($company);
        $this->addTicketType($company);
        $this->addTicketGroup($company);
        $this->addLeaveType($company);
        $this->addEmailNotificationSettings($company);
        $this->addDefaultCurrencies($company);
        $this->addDefaultThemeSettings($company);
        $this->addPaymentGatewaySettings($company);
        $this->addInvoiceSettings($company);
        $this->addSlackSettings($company);
        $this->addProjectSettings($company);
        $this->addAttendanceSettings($company);
        $this->addCustomFieldGroup($company);
        $this->addRoles($company);
        $this->addMessageSetting($company);
        $this->addLogTImeForSetting($company);
        $this->addLeadSourceAndLeadStatus($company);
        $this->addProjectCategory($company);
        $this->addDashboardWidget($company);
        $this->insertGDPR($company);
        $this->addLocationCodeAndSalesCategory($company);
        
        $this->addDefaultTimezone($company);
        
        $this->addDefaultEmailTemplate($company);
        
        $this->addProductSetting($company);

        event(new CompanyRegistered($company));
    }

    public function addDefaultTimezone($company){
        $globalSetting = GlobalSetting::first();
        $company->timezone = $globalSetting->timezone;
        $company->save();
    }

    public function updated(Company $company)
    {

        if ($company->isDirty('package_id')) {
            ModuleSetting::where('company_id', $company->id)->delete();
            ModuleSetting::whereNull('company_id')->delete();
            $package = Package::findOrFail($company->package_id);

            $moduleInPackage = (array) json_decode($package->module_in_package);
            $clientModules = ['projects', 'tickets', 'invoices', 'estimates', 'events', 'products', 'tasks', 'messages', 'payments', 'contracts', 'notices', 'timelogs', 'productReview', 'discussions', 'visionboard'];
            foreach ($moduleInPackage as $module) {

                if (in_array($module, $clientModules)) {
                    $moduleSetting = new ModuleSetting();
                    $moduleSetting->company_id = $company->id;
                    $moduleSetting->module_name = $module;
                    $moduleSetting->status = 'active';
                    $moduleSetting->type = 'client';
                    $moduleSetting->save();
                }

                $moduleSetting = new ModuleSetting();
                $moduleSetting->company_id = $company->id;
                $moduleSetting->module_name = $module;
                $moduleSetting->status = 'active';
                $moduleSetting->type = 'employee';
                $moduleSetting->save();

                $moduleSetting = new ModuleSetting();
                $moduleSetting->company_id = $company->id;
                $moduleSetting->module_name = $module;
                $moduleSetting->status = 'active';
                $moduleSetting->type = 'admin';
                $moduleSetting->save();
            }
        }
        session()->forget('company_setting');
    }

    public function updating(Company $company)
    {

        $user = user();

        if ($user) {
            $company->last_updated_by = $user->id;
        }

        if ($company->isDirty('date_format')) {
            switch ($company->date_format) {
                case 'd-m-Y':
                    $company->date_picker_format = 'dd-mm-yyyy';
                    break;
                case 'm-d-Y':
                    $company->date_picker_format = 'mm-dd-yyyy';
                    break;
                case 'Y-m-d':
                    $company->date_picker_format = 'yyyy-mm-dd';
                    break;
                case 'd.m.Y':
                    $company->date_picker_format = 'dd.mm.yyyy';
                    break;
                case 'm.d.Y':
                    $company->date_picker_format = 'mm.dd.yyyy';
                    break;
                case 'Y.m.d':
                    $company->date_picker_format = 'yyyy.mm.dd';
                    break;
                case 'd/m/Y':
                    $company->date_picker_format = 'dd/mm/yyyy';
                    break;
                case 'Y/m/d':
                    $company->date_picker_format = 'yyyy/mm/dd';
                    break;
                case 'd-M-Y':
                    $company->date_picker_format = 'dd-M-yyyy';
                    break;
                case 'd/M/Y':
                    $company->date_picker_format = 'dd/M/yyyy';
                    break;
                case 'd.M.Y':
                    $company->date_picker_format = 'dd.M.yyyy';
                    break;
                case 'd M Y':
                    $company->date_picker_format = 'dd M yyyy';
                    break;
                case 'd, M Y':
                    $company->date_picker_format = 'dd, M yyyy';
                    break;
                case 'd F, Y':
                    $company->date_picker_format = 'dd MM, yyyy';
                    break;
                case 'D/M/Y':
                    $company->date_picker_format = 'D/M/yyyy';
                    break;
                case 'D.M.Y':
                    $company->date_picker_format = 'D.M.yyyy';
                    break;
                case 'D-M-Y':
                    $company->date_picker_format = 'D-M-yyyy';
                    break;
                case 'D M Y':
                    $company->date_picker_format = 'D M yyyy';
                    break;
                case 'd D M Y':
                    $company->date_picker_format = 'dd D M yyyy';
                    break;
                case 'D d M Y':
                    $company->date_picker_format = 'D dd M yyyy';
                    break;
                default:
                    $company->date_picker_format = 'mm/dd/yyyy';
                    break;
            }
        }
    }

    public function deleting(Company $company)
    {
        $projects = \App\Project::where('company_id', $company->id)->get();

        foreach ($projects as $project) {
            File::deleteDirectory('user-uploads/project-files/' . $project->id);
            $project->forceDelete();
        }

        $expenses = \App\Expense::where('company_id', $company->id)->get();
        foreach ($expenses as $expense) {
            File::delete('user-uploads/expense-invoice/' . $expense->bill);
        }

        $users = \App\User::where('company_id', $company->id)->get();
        foreach ($users as $user) {
            File::delete('user-uploads/avatar/' . $user->image);
        }

        File::delete('user-uploads/app-logo/' . $company->logo);
    }

    public function addTaskBoard($company)
    {

        $uncatColumn = new TaskboardColumn();
        $uncatColumn->company_id = $company->id;
        $uncatColumn->column_name = 'Incomplete';
        $uncatColumn->slug = 'incomplete';
        $uncatColumn->label_color = '#d21010';
        $uncatColumn->label_color = '#d21010';
        $uncatColumn->priority = 1;
        $uncatColumn->save();

        $completeColumn = new TaskboardColumn();
        $completeColumn->company_id = $company->id;
        $completeColumn->column_name = 'Completed';
        $completeColumn->slug = 'completed';
        $completeColumn->label_color = '#679c0d';
        $completeColumn->priority = $uncatColumn->priority + 1;
        $completeColumn->save();
    }
    
    

    public function addTicketChannel($company)
    {
        $channel = new TicketChannel();
        $channel->company_id = $company->id;
        $channel->channel_name = 'Email';
        $channel->save();

        $channel = new TicketChannel();
        $channel->company_id = $company->id;
        $channel->channel_name = 'Phone';
        $channel->save();

        $channel = new TicketChannel();
        $channel->company_id = $company->id;
        $channel->channel_name = 'Twitter';
        $channel->save();

        $channel = new TicketChannel();
        $channel->company_id = $company->id;
        $channel->channel_name = 'Facebook';
        $channel->save();
    }

    public function addTicketType($company)
    {
        $type = new TicketType();
        $type->company_id = $company->id;
        $type->type = 'Question';
        $type->save();

        $type = new TicketType();
        $type->company_id = $company->id;
        $type->type = 'Problem';
        $type->save();

        $type = new TicketType();
        $type->company_id = $company->id;
        $type->type = 'Incident';
        $type->save();

        $type = new TicketType();
        $type->company_id = $company->id;
        $type->type = 'Feature Request';
        $type->save();
    }

    public function addTicketGroup($company)
    {
        $group = new TicketGroup();
        $group->company_id = $company->id;
        $group->group_name = 'Sales';
        $group->save();

        $group = new TicketGroup();
        $group->company_id = $company->id;
        $group->group_name = 'Code';
        $group->save();

        $group = new TicketGroup();
        $group->company_id = $company->id;
        $group->group_name = 'Management';
        $group->save();
    }

    public function addLeaveType($company)
    {
        $category = new LeaveType();
        $category->company_id = $company->id;
        $category->type_name = 'Casual';
        $category->color = 'success';
        $category->save();

        $category = new LeaveType();
        $category->company_id = $company->id;
        $category->type_name = 'Sick';
        $category->color = 'danger';
        $category->save();

        $category = new LeaveType();
        $category->company_id = $company->id;
        $category->type_name = 'Earned';
        $category->color = 'info';
        $category->save();
    }

    public function addEmailNotificationSettings($company)
    {
        // When new expense added by member
        \App\EmailNotificationSetting::create([
            'setting_name' => 'New Expense/Added by Admin',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When new expense added by member
        \App\EmailNotificationSetting::create([
            'setting_name' => 'New Expense/Added by Member',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When expense status changed
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Expense Status Changed',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // New Support Ticket Request
        \App\EmailNotificationSetting::create([
            'setting_name' => 'New Support Ticket Request',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When new user registers or added by admin
        \App\EmailNotificationSetting::create([
            'setting_name' => 'User Registration/Added by Admin',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When employee is added to project
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Employee Assign to Project',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When notice published by admin
        \App\EmailNotificationSetting::create([
            'setting_name' => 'New Notice Published',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When user is assigned to a task
        \App\EmailNotificationSetting::create([
            'setting_name' => 'User Assign to Task',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When new leave application added
        \App\EmailNotificationSetting::create([
            'setting_name' => 'New Leave Application',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When task completed
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Task Completed',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When task completed
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Invoice Create/Update Notification',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // When task completed
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Payment Create/Update Notification',
            'send_email' => 'yes',
            'send_push' => 'yes',
            'company_id' => $company->id
        ]);

        // New 
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Discussion Reply',
            'send_push' => 'yes',
            'send_email' => 'yes',
            'company_id' => $company->id
        ]);
        
         // When files added // By SB
        
        \App\EmailNotificationSetting::create([
            'setting_name' => 'When files added',
            'send_push' => 'no',
            'send_email' => 'yes',
            'company_id' => $company->id
        ]);
        
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Estimate approved by client',
            'send_push' => 'no',
            'send_email' => 'yes',
            'company_id' => $company->id
        ]);
        \App\EmailNotificationSetting::create([
            'setting_name' => 'Invoice paid by client',
            'send_push' => 'no',
            'send_email' => 'yes',
            'company_id' => $company->id
        ]);
        \App\EmailNotificationSetting::create([
            'setting_name' => 'When contract is signed by client',
            'send_push' => 'no',
            'send_email' => 'yes',
            'company_id' => $company->id
        ]);
        \App\EmailNotificationSetting::create([
            'setting_name' => 'When discussion created on contract',
            'send_push' => 'no',
            'send_email' => 'yes',
            'company_id' => $company->id
        ]);
        \App\EmailNotificationSetting::create([
            'setting_name' => 'When note added to product review',
            'send_push' => 'no',
            'send_email' => 'yes',
            'company_id' => $company->id
        ]);
    }

    /**
     * @param $company
     */
    public function addDashboardWidget($company)
    {
        // When new widget added
        \App\DashboardWidget::create([
            'widget_name' => 'total_clients',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_employees',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_projects',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_unpaid_invoices',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_hours_logged',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_pending_tasks',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_today_attendance',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_unresolved_tickets',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'total_resolved_tickets',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'recent_earnings',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'settings_leaves',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'new_tickets',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'overdue_tasks',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'completed_tasks',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'client_feedbacks',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'pending_follow_up',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'project_activity_timeline',
            'status' => 1,
            'company_id' => $company->id
        ]);

        \App\DashboardWidget::create([
            'widget_name' => 'user_activity_timeline',
            'status' => 1,
            'company_id' => $company->id
        ]);
    }

    public function addDefaultCurrencies($company)
    {
        $globalCurrencies = GlobalCurrency::all();
        $globalSetting = GlobalSetting::first();

        foreach ($globalCurrencies as $globalCurrency) {
            $currency = new Currency();
            $currency->company_id = $company->id;
            $currency->currency_name = $globalCurrency->currency_name;
            $currency->currency_symbol = $globalCurrency->currency_symbol;
            $currency->currency_code = $globalCurrency->currency_code;
            $currency->currency_position = $globalCurrency->currency_position;
            $currency->save();

            if ($globalSetting->currency_id == $globalCurrency->id) {
                $company->currency_id = $currency->id;
                $company->save();
            }
        }
    }

    public function addDefaultThemeSettings($company)
    {
        $theme = new ThemeSetting();
        $theme->company_id = $company->id;
        $theme->panel = "admin";
        $theme->header_color = "#ed4040";
        $theme->sidebar_color = "#292929";
        $theme->sidebar_text_color = "#cbcbcb";
        $theme->save();

        // project admin panel
        $theme = new ThemeSetting();
        $theme->company_id = $company->id;
        $theme->panel = "project_admin";
        $theme->header_color = "#5475ed";
        $theme->sidebar_color = "#292929";
        $theme->sidebar_text_color = "#cbcbcb";
        $theme->save();

        // employee panel
        $theme = new ThemeSetting();
        $theme->company_id = $company->id;
        $theme->panel = "employee";
        $theme->header_color = "#f7c80c";
        $theme->sidebar_color = "#292929";
        $theme->sidebar_text_color = "#cbcbcb";
        $theme->save();

        // client panel
        $theme = new ThemeSetting();
        $theme->company_id = $company->id;
        $theme->panel = "client";
        $theme->header_color = "#00c292";
        $theme->sidebar_color = "#292929";
        $theme->sidebar_text_color = "#cbcbcb";
        $theme->save();
    }

    public function addPaymentGatewaySettings($company)
    {
        $credential = new \App\PaymentGatewayCredentials();
        $credential->company_id = $company->id;
        $credential->paypal_client_id = null;
        $credential->paypal_secret = null;
        $credential->save();
    }

    public function addInvoiceSettings($company)
    {
        $invoice = new \App\InvoiceSetting();
        $invoice->company_id = $company->id;
        $invoice->invoice_prefix = 'INV';
        $invoice->estimate_prefix = 'EST';
        $invoice->credit_note_prefix = 'CN';
        $invoice->template = 'invoice-1';
        $invoice->due_after = 15;
        $invoice->invoice_terms = 'Thank you for your business. Please process this invoice within the due date.';
        $invoice->save();
    }

    public function addSlackSettings($company)
    {
        $slack = new \App\SlackSetting();
        $slack->company_id = $company->id;
        $slack->slack_webhook = null;
        $slack->slack_logo = null;
        $slack->save();
    }

    public function addProjectSettings($company)
    {
        $project_setting = new ProjectSetting();

        $project_setting->company_id = $company->id;
        $project_setting->send_reminder = 'no';
        $project_setting->remind_time = 5;
        $project_setting->remind_type = 'days';

        $project_setting->save();
    }

    public function addAttendanceSettings($company)
    {
        $attendance = new \App\AttendanceSetting();
        $attendance->company_id = $company->id;
        $attendance->office_start_time = '09:00:00';
        $attendance->office_end_time = '18:00:00';
        $attendance->late_mark_duration = '20';
        $attendance->save();
    }

    public function addCustomFieldGroup($company)
    {
        \DB::table('custom_field_groups')->insert([
            'name' => 'Client',
            'model' => 'App\ClientDetails',
            'company_id' => $company->id
        ]);
        
        \DB::table('custom_field_groups')->insert([
            'name' => 'Employee',
            'model' => 'App\EmployeeDetails',
            'company_id' => $company->id
        ]);

        \DB::table('custom_field_groups')->insert([
            'name' => 'Project',
            'model' => 'App\Project',
            'company_id' => $company->id
        ]);
        
        // Added by SB
        \DB::table('custom_field_groups')->insert([
            'name' => 'Vendor',
            'model' => 'App\ClientVendorDetails',
            'company_id' => $company->id
        ]);

        \DB::table('custom_field_groups')->insert([
            'name' => 'Lead',
            'model' => 'App\Lead',
            'company_id' => $company->id
        ]);
        // Added by SB
        


    }

    public function addRoles($company)
    {
        $admin = new Role();
        $admin->company_id = $company->id;
        $admin->name = 'admin';
        $admin->display_name = 'App Administrator'; // optional
        $admin->description = 'Admin is allowed to manage everything of the app.'; // optional
        $admin->save();

        $employee = new Role();
        $employee->company_id = $company->id;
        $employee->name = 'employee';
        $employee->display_name = 'Employee'; // optional
        $employee->description = 'Employee can see tasks and projects assigned to him.'; // optional
        $employee->save();

        $client = new Role();
        $client->company_id = $company->id;
        $client->name = 'client';
        $client->display_name = 'Client'; // optional
        $client->description = 'Client can see own tasks and projects.'; // optional
        $client->save();
    }

    public function addMessageSetting($company)
    {
        $setting = new MessageSetting();
        $setting->company_id = $company->id;
        $setting->allow_client_admin = 'yes';
        $setting->allow_client_employee = 'yes';
        $setting->save();
    }
    
    public function addProductSetting($company)
    {
        $setting = new ProductSetting();
        $setting->company_id = $company->id;
        $setting->save();
    }
    
    


    public function addLogTImeForSetting($company)
    {
        $storage = new LogTimeFor();
        $storage->company_id = $company->id;
        $storage->log_time_for = 'project';
        $storage->save();
    }

    public function addLeadSourceAndLeadStatus($company)
    {
        $sources = [
            ['type' => 'email', 'company_id' => $company->id],
            ['type' => 'google', 'company_id' => $company->id],
            ['type' => 'facebook', 'company_id' => $company->id],
            ['type' => 'friend', 'company_id' => $company->id],
            ['type' => 'direct visit', 'company_id' => $company->id],
            ['type' => 'tv ad', 'company_id' => $company->id]
        ];

        LeadSource::insert($sources);

        $status = [
            ['type' => 'pending', 'company_id' => $company->id],
            ['type' => 'inprocess', 'company_id' => $company->id],
            ['type' => 'converted', 'company_id' => $company->id]
        ];

        LeadStatus::insert($status);
    }

    public function addProjectCategory($company)
    {
        $category = new \App\ProjectCategory();
        $category->category_name = 'Renovation';
        $category->company_id = $company->id;
        $category->save();

        $category = new \App\ProjectCategory();
        $category->category_name = 'Kitchen Renovation';
        $category->company_id = $company->id;
        $category->save();
        
        $category = new \App\ProjectCategory();
        $category->category_name = 'Bathroom Renovation';
        $category->company_id = $company->id;
        $category->save();
        
        $category = new \App\ProjectCategory();
        $category->category_name = 'General Design';
        $category->company_id = $company->id;
        $category->save();
        
        $category = new \App\ProjectCategory();
        $category->category_name = 'Whole Home Design';
        $category->company_id = $company->id;
        $category->save();
        
        $category = new \App\ProjectCategory();
        $category->category_name = 'E-Design';
        $category->company_id = $company->id;
        $category->save();
    }
    
    public function addDefaultEmailTemplate($company)
    {
        $template = new EmailTemplate();
        $template->company_id = $company->id;
        $template->template_name = 'Initial interest / Schedule Phone Call (Send 0 Mins after lead entered into indema)';
        $template->subject = "So lovely to e-meet you! Let's chat more."; 
        
        $template->body = "<p>Hello&nbsp;<span style='color: rgb(4, 23, 49);'>{{lead.name}}</span>,
</p><p>Thank you so much for reaching out to <u style=''><i style=''><b>[Insert design firm name]</b></i></u> about your
project!
</p><p>We're so thrilled to chat with you more about your needs, and would love to set up
the first phone call. </p><p>During this call, we'll quickly chat about what you're looking for,
and see when's a good time to schedule our first design consultation.
Looking at my schedule, I have <u><b><i>[Insert day], [insert month, year] at [insert time]</i></b></u>,
or, I can do <u><i><b>[Insert day], [insert month, year] at [insert time]</b></i></u>. Which option works
best for you?
</p><p>Looking forward to the amazing things we can do for your project!
</p><p>Best,&nbsp;<br><i><b><u>[Insert Signature]</u></b></i></p>";
        
        
        $template->email_type = 1;
        $template->save();
        
        $template = new EmailTemplate();
        $template->company_id = $company->id;
        $template->template_name = 'Need to Schedule Consultation (Send 5 Mins after lead entered into indema)';
        $template->subject = "We'd love to work with you!";
        
        $template->body = "<p>Hello {{lead.name}},</p><p>Thank you so much for reaching out to [Insert design firm name] about your
project! </p><p>We would be honored to work with you on your project, and the first step
is to schedule a design consultation.
With our design consultation, it allows us to get a deeper understanding of your
space and what you want to accomplish! </p><p>This consultation is [insert length of time.
Ex: 1 hour], and we will be discussing the following:
</p><p>1. Review every room you want to design and talk about your style<br>2. Explore your expectations for your project<br>3. Discuss the next steps and our design process
</p><p>There is a small investment of <u><i><b>[insert dollar amount]</b></i></u> to schedule which can be paid
by check or cash during our meeting or prior.
I have <b><i><u>[Insert day], [insert month, year] at [insert time]</u></i></b>, or, I can do <b><i><u>[Insert day],
[insert month, year] at [insert time]</u></i></b>. Which option works best for you?
</p><p><br></p><p>Best,&nbsp;</p><p><i><u><b>[Insert Signature]</b></u></i></p>";
        
       
        $template->email_type = 1;
        $template->save();
        
        $template = new EmailTemplate();
        $template->company_id = $company->id;
        $template->template_name = 'SALES- FAQ Email (Send 4 hours after lead entered into indema)';
        $template->subject = "We get it... There are questions! We got you!";
        
        $template->body = "<p>Hello&nbsp;<span style='color: rgb(4, 23, 49);'>{{lead.name}}</span>,
</p><p>Hiring a designer can sometimes be daunting. We get it! Which is exactly why I
wanted to share these top 3 FAQs that come from past client's we've had. While
there maybe many more (and we're happy to answer them!), these are the top 3 we
get on a consistent basis! Of course, if you have anymore questions you are more
than welcome to reach out to us!
</p><p><b>1. How much does a designer cost, anyway!?</b>
Every designer works differently, but it's important to know that we charge based
on the scope of work and size of your project! Not every project will cost the same.
But we will take our time to ensure that you are in the know!
<br><br><b>2. Will you design based off my style?</b>
YES! Absolutely! This, of course, your space and sanctuary! Everything we do is to
enhance the way you live in your own environment.
<br><br><b>3. How long does a typical project last?</b>
This is a tough question! Each project is so unique and has timelines that can
sometimes change. We do, however, try to estimate as accurately as possible and
will relay that information to you so you can plan ahead!
<br><br>Got a burning question?! Shoot us an email by replying! We're happy to answer any
other questions you have about working with a designer.
<br><br>Best,<br><b><i><u>[Insert Signature]</u></i></b></p>";
        
        
        $template->email_type = 1;
        $template->save();
        
        $template = new EmailTemplate();
        $template->company_id = $company->id;
        $template->template_name = 'Welcome to the Family!  (Send 0 mins after start date of project)';
        $template->subject = "Welcoming our new family member!!";
        
        $template->body = "<p>Hello&nbsp;<span style='color: rgb(4, 23, 49);'>{{client.first.name}}</span>,
<br><br>I'm so delighted to be the first to welcome you to the<b><i> [insert design firm name]</i></b>
family!! </p><p>We are beyond excited to be working with you and are honored you chose
us to work with you!
So, here's what's next!
<u><i><b>[Provide a paragraph about what is to be expected moving on with this client.
Maybe it is the contract, or getting a deposit. Explain in detail here]</b></i></u>
</p><p>So, once that is complete we can hit the ground running! Looking forward to this
partnership to making your house a home.
</p><p><br></p><p>Best,&nbsp;<br><b><i><u>[Insert Signature]</u></i></b></p>";
        
        
        $template->email_type = '';
        $template->save();
        
        $template = new EmailTemplate();
        $template->company_id = $company->id;
        $template->template_name = 'Thank you for hiring us + referral request. (Send 3 days after end date of a project)';
        $template->subject = 'I already miss working with you!';
        
        $template->body = "Hello&nbsp;<span style='color: rgb(4, 23, 49);'>{{client.first.name}},</span>&nbsp;<br><br>I wanted to sincerely thank you from the bottom of my heart for such an amazing
project! One of my favorite parts of the process was<u><i><b> [insert something memorable
about the project]</b></i></u>. <br><br>I could relive that moment everyday!
As you know, we thrive on helping clients all over. If there are any friends or family
members looking to refresh their home, I would love nothing more than to help
them, too! It would mean so much to us!<br><br>&nbsp;Thank you again for such an amazing experience for us!
<br><br>Best,<br><b style=''><i style=''><u>[Insert Signature]</u>&nbsp;</i></b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
        
        
        $template->email_type = 1;
        $template->save();
        
        $template = new EmailTemplate();
        $template->company_id = $company->id;
        $template->template_name = 'Feedback (Send 1 week after end date of project)';
        $template->subject = 'How did we REALLY do?';
        
        $template->body = "Hello&nbsp;<span style='color: rgb(4, 23, 49);'>{{client.first.name}}</span>,
<br><br>I can't even believe the project is over! It FLEW by so darn quick! And, I would do it
again in a heartbeat! You were such a pleasure to work with!!
I wanted to ask for some feedback! If it's not too much to ask, I have three
questions that will really help us improve our services for other clients, and even you
if you hire us again!
<br><br>1. Was there anything that was unexpected about the design process that you wish
you knew before you began?
<br><br>2. What was your favorite part about the project?
<br><br>3. What was your least favorite part about the project?
<br><br>4. Would you recommend us to a friend or family? If not, why? <br><br>Don't worry! We
don't get offended!
Thank you so much for this feedback! We truly appreciate it so much.
<br><br>Best,<br><b><i><u>[Insert Signature]</u></i></b>&nbsp;";
        
        
        $template->email_type = 1;
        $template->save();
        
        $template = new EmailTemplate();
        $template->company_id = $company->id;
        $template->template_name = 'Review Request (Send 1 month after project is complete)';
        $template->subject = 'Can I ask a favor??';
        
        $template->body = "Hello&nbsp;<span style='color: rgb(4, 23, 49);'>{{client.first.name}}</span>,
<br><br>I hope you are doing absolutely amazing!
I wanted to ask a favor. If it's ok, would you be able to write us a review about your
experience working with us? <br>It would mean so much for you to do that. We thrive
on reviews for our business as it helps us be able to reach more clients to help them
with their homes.
<br><br>Here's the link: <u><i><b>[insert link]</b></i></u>.
<br>You're the best! Thank you so much!
<br><br>Best,<br><b><i><u>[Insert Signature]&nbsp; </u></i></b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
        
        
        $template->email_type = 1;
        $template->save();

    }

    private function insertGDPR($company)
    {
        $gdpr = new GdprSetting();
        $gdpr->company_id = $company->id;
        $gdpr->save();
    }
    
    public function addLocationCodeAndSalesCategory($company)
    {
        $locationCode = [
            ['location_code' => 'Kitchen', 'location_name' => 'Kitchen' , 'company_id' => $company->id],
            ['location_code' => 'Diningroom', 'location_name' => 'Dining room' , 'company_id' => $company->id],
            ['location_code' => 'Familyroom', 'location_name' => 'Family room' , 'company_id' => $company->id],
            ['location_code' => 'Livingroom', 'location_name' => 'Living room' , 'company_id' => $company->id],
            ['location_code' => 'Kitchennook', 'location_name' => 'Kitchen nook' , 'company_id' => $company->id],
            ['location_code' => 'Homegym', 'location_name' => 'Home gym' , 'company_id' => $company->id],
            ['location_code' => 'Office1', 'location_name' => 'Office 1' , 'company_id' => $company->id],
            ['location_code' => 'Office2', 'location_name' => 'Office 2' , 'company_id' => $company->id],
            ['location_code' => 'Butlerpantry', 'location_name' => 'Butler pantry' , 'company_id' => $company->id],
            ['location_code' => 'Masterbedroom1', 'location_name' => 'Master bedroom 1' , 'company_id' => $company->id],
            ['location_code' => 'Masterbedroom2', 'location_name' => 'Master bedroom 2' , 'company_id' => $company->id],
            ['location_code' => 'Masterbathroom1', 'location_name' => 'Master bathroom 1' , 'company_id' => $company->id],
            ['location_code' => 'Masterbathroom2', 'location_name' => 'Master bathroom 2' , 'company_id' => $company->id],
            ['location_code' => 'Bedroom1', 'location_name' => 'Bedroom 1' , 'company_id' => $company->id],
            ['location_code' => 'Bedroom2', 'location_name' => 'Bedroom 2' , 'company_id' => $company->id],
            ['location_code' => 'Bedroom3', 'location_name' => 'Bedroom 3' , 'company_id' => $company->id],
            ['location_code' => 'Bedroom4', 'location_name' => 'Bedroom 4' , 'company_id' => $company->id],
            ['location_code' => 'Bathroom1', 'location_name' => 'Bathroom 1' , 'company_id' => $company->id],
            ['location_code' => 'Bathroom2', 'location_name' => 'Bathroom 2' , 'company_id' => $company->id],
            ['location_code' => 'Bathroom3', 'location_name' => 'Bathroom 3' , 'company_id' => $company->id],
            ['location_code' => 'Bathroom4', 'location_name' => 'Bathroom 4' , 'company_id' => $company->id],
            ['location_code' => 'Laundryroom', 'location_name' => 'Laundry room' , 'company_id' => $company->id],
            ['location_code' => 'Garage1', 'location_name' => 'Garage 1' , 'company_id' => $company->id],
            ['location_code' => 'Garage2', 'location_name' => 'Garage 2' , 'company_id' => $company->id],
            ['location_code' => 'Mudroom', 'location_name' => 'Mud room' , 'company_id' => $company->id],
            ['location_code' => 'Basement', 'location_name' => 'Basement' , 'company_id' => $company->id],
        ];
        
        CodeType::insert($locationCode);

        $SalesCategory = [
            ['salescategory_code' => 'Furniture', 'salescategory_name' => 'Furniture' , 'company_id' => $company->id],
            ['salescategory_code' => 'Accessories', 'salescategory_name' => 'Accessories' , 'company_id' => $company->id],
            ['salescategory_code' => 'Fabrics', 'salescategory_name' => 'Fabrics' , 'company_id' => $company->id],
            ['salescategory_code' => 'Wallcovering', 'salescategory_name' => 'Wall covering' , 'company_id' => $company->id],
            ['salescategory_code' => 'Antiques', 'salescategory_name' => 'Antiques' , 'company_id' => $company->id],
            ['salescategory_code' => 'Artwork', 'salescategory_name' => 'Artwork' , 'company_id' => $company->id],
            ['salescategory_code' => 'Metalwork', 'salescategory_name' => 'Metal work' , 'company_id' => $company->id],
            ['salescategory_code' => 'Equipmentfixtures', 'salescategory_name' => 'Equipment + fixtures' , 'company_id' => $company->id],
            ['salescategory_code' => 'FinishesTrim', 'salescategory_name' => 'Finishes + Trim' , 'company_id' => $company->id],
            ['salescategory_code' => 'Floorcoverings', 'salescategory_name' => 'Floor coverings' , 'company_id' => $company->id],
            ['salescategory_code' => 'Freight', 'salescategory_name' => 'Freight' , 'company_id' => $company->id],
            ['salescategory_code' => 'Lighting', 'salescategory_name' => 'Lighting' , 'company_id' => $company->id],
            ['salescategory_code' => 'Finishelectrical', 'salescategory_name' => 'Finish electrical' , 'company_id' => $company->id],
            ['salescategory_code' => 'Finishplumbing', 'salescategory_name' => 'Finish plumbing' , 'company_id' => $company->id],
            ['salescategory_code' => 'Plantsplanting', 'salescategory_name' => 'Plants + planting' , 'company_id' => $company->id],
            ['salescategory_code' => 'Walltreatments', 'salescategory_name' => 'Wall treatments' , 'company_id' => $company->id],
        ];
       
        SalescategoryType::insert($SalesCategory);
    }
    
    public function addProductStatus($company)
    {

        $statuses = [
            ['status_name' => 'Proposed', 'status_color' => '#4600ff' , 'company_id' => $company->id],
            ['status_name' => 'Ordered', 'status_color' => '#ba5a5a' , 'company_id' => $company->id],
            ['status_name' => 'Backordered', 'status_color' => '#ff0000' , 'company_id' => $company->id],
            ['status_name' => 'Shipped', 'status_color' => '#00b46b' , 'company_id' => $company->id],
            ['status_name' => 'Received', 'status_color' => '#ffea00' , 'company_id' => $company->id],
            ['status_name' => 'Installed', 'status_color' => '#fe00ff' , 'company_id' => $company->id]
        ];
        ProductStatus::insert($statuses);
    }

}
