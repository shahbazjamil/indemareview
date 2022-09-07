<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\EmployeesDataTable;
use App\Designation;
use App\EmployeeDetails;
use App\EmployeeDocs;
use App\EmployeeSkill;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Admin\Employee\StoreRequest;
use App\Http\Requests\Admin\Employee\UpdateRequest;
use App\Leave;
use App\LeaveType;
use App\Project;
use App\ProjectMember;
use App\ProjectTimeLog;
use App\Role;
use App\RoleUser;
use App\Skill;
use App\Task;
use App\TaskboardColumn;
use App\Team;
use App\UniversalSearch;
use App\User;
use App\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Company;
use App\Package;

// bitsclan code start here
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Employee;
use App\QuickbooksSettings;

// bitsclan code end here

class ManageEmployeesController extends AdminBaseController
{
    // bitsclan code start here
    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';
    // bitsclan code end here

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.employees';
        $this->pageIcon = 'icon-user';
        $this->middleware(function ($request, $next) {
            if (!in_array('employees', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });

        // bitsclan code start here
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(EmployeesDataTable $dataTable)
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/admin/employees'));
        
        $this->employees = User::allEmployees();
        $this->skills = Skill::all();
        $this->departments = Team::all();
        $this->designations = Designation::all();
        $this->totalEmployees = count($this->employees);
        $this->roles = Role::where('roles.name', '<>', 'client')->get();
        
        $company = Company::findOrFail(company()->id);
        $this->maxEmployees = ($company->package->number_users + $company->additional_number_users);
        
        $this->isMaxLimit = 0;
        
        // For VIP package only
        if($company->package->id == 11 && $company->employees->count() >= $this->maxEmployees) {
            $this->isMaxLimit = 1;
        }
        
        $whoseProjectCompleted = ProjectMember::join('projects', 'projects.id', '=', 'project_members.project_id')
            ->join('users', 'users.id', '=', 'project_members.user_id')
            ->select('users.*')
            ->groupBy('project_members.user_id')
            ->havingRaw("min(projects.completion_percent) = 100 and max(projects.completion_percent) = 100")
            ->orderBy('users.id')
            ->get();

        $notAssignedProject = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name')->whereNotIn('users.id', function ($query) {

                $query->select('user_id as id')->from('project_members');
            })
            ->where('roles.name', '<>', 'client')
            ->get();

        $this->freeEmployees = $whoseProjectCompleted->merge($notAssignedProject)->count();
        $this->company = $company;
        
        $this->totalRecords = $this->totalEmployees;
        
        $this->additionalUserPlan = Package::find(18);

        // return view('admin.employees.index', $this->data);
        return $dataTable->render('admin.employees.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employee = new EmployeeDetails();
        $this->fields = $employee->getCustomFieldGroupsWithFields()->fields;
        $this->skills = Skill::all()->pluck('name')->toArray();
        $this->teams = Team::orderBy('team_name')->get();
        $this->designations = Designation::orderBy('name')->get();
        return view('admin.employees.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $company = Company::findOrFail(company()->id);
        
        $existing_user = User::withoutGlobalScope(CompanyScope::class)->select('id', 'email')->where('email', $request->input('email'))->first();
        // already user can't added again 
        if($existing_user) {
            return Reply::error('Provided email is already registered. Try with different email.');
        }
        
        $this->maxEmployees = ($company->package->number_users + $company->additional_number_users);
        // For VIP package only
        if($company->package->id == 11 && $company->employees->count() >= $this->maxEmployees) {
            return Reply::error('The employees limit exceeded. Please purchase additional users add-ons for add employees.');
        }
        
        // if (!is_null($company->employees) && $company->employees->count() >= $company->package->max_employees) {
        //     return Reply::error(__('messages.upgradePackageForAddEmployees', ['employeeCount' => company()->employees->count(), 'maxEmployees' => $company->package->max_employees]));
        // }

        // if (!is_null($company->employees) && $company->package->max_employees < $company->employees->count()) {
        //     return Reply::error(__('messages.downGradePackageForAddEmployees', ['employeeCount' => company()->employees->count(), 'maxEmployees' => $company->package->max_employees]));
        // }
        $data = $request->all();
        DB::beginTransaction();
        try {
            
            $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('image')) {
            $data['image'] = Files::upload($data['image'], 'avatar', 300);
        }
        $user = User::create($data);


        //bisclan code here

        $qbo_id                         = '';
        $Line1                          = '';
        $City                           = '';
        $CountrySubDivisionCode         = '';
        $PostalCode                     = '';
        $Ship_Line1                     = '';
        $Ship_City                      = '';
        $ship_CountrySubDivisionCode    = '';
        $ship_PostalCode                = '';

        $this->quickbook = $this->QuickbookSettings();
        if ($this->quickbook) {
                try {
                    if (!empty($data['address'])) {
                        $address = preg_split('/\r\n|\r|\n/', $data['address']);
                        $Line1 = isset($address[0]) ? $address[0] : '';
                        if (isset($address[1])) {
                            $address2 = explode(',', trim($address[1]));
                            $City = isset($address2[0]) ? $address2[0] : '';
                        }
                        if (isset($address2[1])) {
                            $address3 = explode(' ', trim($address2[1]));
                        }
                        $CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
                        $PostalCode = isset($address3[1]) ? $address3[1] : '';
                    }

                    $name = explode(' ', $data['name']);
                    $GivenName = isset($name[0]) ? $name[0] : $data['name'];
                    $FamilyName = isset($name[1]) ? $name[1] : $data['name'];

                    $employee = Employee::create([
                                "GivenName" => $GivenName,
                                "PrimaryAddr" => [
                                    "CountrySubDivisionCode" => $CountrySubDivisionCode,
                                    "City" => $City,
                                    "PostalCode" => $PostalCode,
                                    "Line1" => $Line1
                                ],
                                "PrimaryPhone" => [
                                    "FreeFormNumber" => $data['mobile'],
                                ],
                                "FamilyName" => $FamilyName,
                    ]);

                    $resultingCustomerObj = $this->quickbook->Add($employee);
                    //$error =  $this->quickbook->getLastError();
                    if ($error) {
                        //return Reply::error(__($error->getOAuthHelperError()));
                    }
                    $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
                } catch (\Exception $e) {
                    
                }
            }
            // code end here

        $user->employeeDetail()->create([
            'employee_id' => $data['employee_id'],
            'address' => $data['address'],
            'hourly_rate' => $data['hourly_rate'],
            'slack_username' => $data['slack_username'],
            'joining_date' => Carbon::createFromFormat($this->global->date_format,$data['joining_date'])->format('Y-m-d'),
            'last_date' => ($data['last_date'] != '') ? Carbon::createFromFormat($this->global->date_format, $data['last_date'])->format('Y-m-d') : null,
            'department_id' => $data['department'],
            'designation_id' => $data['designation'],
            'salary' => $data['salary'],
            'qbo_id' => $qbo_id,
        ]);

            $tags = json_decode($data['tags']);
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    // check or store skills
                    $skillData = Skill::firstOrCreate(['name' => strtolower($tag->value)]);

                    // Store user skills
                    $skill = new EmployeeSkill();
                    $skill->user_id = $user->id;
                    $skill->skill_id = $skillData->id;
                    $skill->save();
                }
            }

            // To add custom fields data
            if ($request->get('custom_fields_data')) {
                $user->employeeDetail->updateCustomFieldData($request->get('custom_fields_data'));
            }

            $role = Role::where('name', 'employee')->first();
            $user->attachRole($role->id);
            DB::commit();

        } catch (\Swift_TransportException $e) {
            DB::rollback();
            return Reply::error('Please configure SMTP details to add employee. Visit Settings -> Email setting to set SMTP','smtp_error');
        } catch (\Exception $e) {
            DB::rollback();
            return Reply::error('Some error occured when inserting the data. Please try again or contact support.'.$e->getMessage());
        }
        $this->logSearchEntry($user->id, $user->name, 'admin.employees.show', 'employee');

        return Reply::redirect(route('admin.employees.index'), __('messages.employeeAdded'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->employee = User::with(['employeeDetail', 'employeeDetail.designation', 'employeeDetail.department'])->withoutGlobalScope('active')->findOrFail($id);
        $this->employeeDetail = EmployeeDetails::where('user_id', '=', $this->employee->id)->first();
        $this->employeeDocs = EmployeeDocs::where('user_id', '=', $this->employee->id)->get();

        if (!is_null($this->employeeDetail)) {
            $this->employeeDetail = $this->employeeDetail->withCustomFields();
            $this->fields = $this->employeeDetail->getCustomFieldGroupsWithFields()->fields;
        }

        $completedTaskColumn = TaskboardColumn::where('slug', 'completed')->first();

        $this->taskCompleted = Task::join('task_users', 'task_users.task_id', '=', 'tasks.id')
            ->where('task_users.user_id', $id)
            ->where('tasks.board_column_id', $completedTaskColumn->id)
            ->count();

        $hoursLogged = ProjectTimeLog::where('user_id', $id)->sum('total_minutes');

        $timeLog = intdiv($hoursLogged, 60) . ' hrs ';

        if (($hoursLogged % 60) > 0) {
            $timeLog .= ($hoursLogged % 60) . ' mins';
        }

        $this->hoursLogged = $timeLog;

        $this->activities = UserActivity::where('user_id', $id)->orderBy('id', 'desc')->get();
        $this->projects = Project::select('projects.id', 'projects.project_name', 'projects.deadline', 'projects.completion_percent')
            ->join('project_members', 'project_members.project_id', '=', 'projects.id')
            ->where('project_members.user_id', '=', $id)
            ->get();
        $this->leaves = Leave::byUser($id);
        $this->leavesCount = Leave::byUserCount($id);

        $this->leaveTypes = LeaveType::byUser($id);
        $this->allowedLeaves = LeaveType::sum('no_of_leaves');

        return view('admin.employees.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->userDetail = User::withoutGlobalScope('active')->findOrFail($id);
        
        $this->employeeDetail = EmployeeDetails::where('user_id', '=', $this->userDetail->id)->first();
        $this->skills = Skill::all()->pluck('name')->toArray();
        
        $this->teams = Team::orderBy('team_name')->get();
        $this->designations = Designation::orderBy('name')->get();
        
        if (!is_null($this->employeeDetail)) {
            $this->employeeDetail = $this->employeeDetail->withCustomFields();
            $this->fields = $this->employeeDetail->getCustomFieldGroupsWithFields()->fields;
        }

        return view('admin.employees.edit', $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $user->name = $request->input('name');
        
        $user->email = $request->input('email');
        
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }
        $user->mobile = $request->input('mobile');
        $user->gender = $request->input('gender');
        $user->status = $request->input('status');
        $user->login = $request->login;
        $user->email_notifications = $request->email_notifications;

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image, 'avatar',300);
        }

        $user->save();

        $tags = json_decode($request->tags);
        if (!empty($tags)) {
            EmployeeSkill::where('user_id', $user->id)->delete();
            foreach ($tags as $tag) {
                // check or store skills
                $skillData = Skill::firstOrCreate(['name' => strtolower($tag->value)]);

                // Store user skills
                $skill = new EmployeeSkill();
                $skill->user_id = $user->id;
                $skill->skill_id = $skillData->id;
                $skill->save();
            }
        }


        $employee = EmployeeDetails::where('user_id', '=', $user->id)->first();

        // bitsclan code start here
        $qbo_id                         = '';
        $Line1                          = '';
        $City                           = '';
        $CountrySubDivisionCode         = '';
        $PostalCode                     = '';
        $Ship_Line1                     = '';
        $Ship_City                      = '';
        $ship_CountrySubDivisionCode    = '';
        $ship_PostalCode                = '';

        $this->quickbook = $this->QuickbookSettings();
        if ($this->quickbook) {
            try {
                if ($request->has('address')) {
                    $address = preg_split('/\r\n|\r|\n/', $request->address);
                    $Line1 = isset($address[0]) ? $address[0] : '';
                    if (isset($address[1])) {
                        $address2 = explode(',', trim($address[1]));
                        $City = isset($address2[0]) ? $address2[0] : '';
                    }
                    if (isset($address2[1])) {
                        $address3 = explode(' ', trim($address2[1]));
                    }
                    $CountrySubDivisionCode = isset($address3[0]) ? $address3[0] : '';
                    $PostalCode = isset($address3[1]) ? $address3[1] : '';
                }

                $name = explode(' ', $request->input('name'));
                $GivenName = isset($name[0]) ? $name[0] : '';
                $FamilyName = isset($name[1]) ? $name[1] : '';

                if (!empty($employee->qbo_id)) {

                    $entities = $this->quickbook->Query("SELECT * FROM Employee where Id='" . $employee->qbo_id . "'");
                    $theEmployee = reset($entities);

                    $employeee = Employee::update($theEmployee, [
                                "GivenName" => $GivenName,
                                "PrimaryAddr" => [
                                    "CountrySubDivisionCode" => $CountrySubDivisionCode,
                                    "City" => $City,
                                    "PostalCode" => $PostalCode,
                                    "Line1" => $Line1
                                ],
                                "PrimaryPhone" => [
                                    "FreeFormNumber" => $request->input('mobile')
                                ],
                                "FamilyName" => $FamilyName,
                    ]);
                    $resultingCustomerObj = $this->quickbook->update($employeee);
                } else {
                    $employeee = Employee::create([
                                "GivenName" => $GivenName,
                                "PrimaryAddr" => [
                                    "CountrySubDivisionCode" => $CountrySubDivisionCode,
                                    "City" => $City,
                                    "PostalCode" => $PostalCode,
                                    "Line1" => $Line1
                                ],
                                "PrimaryPhone" => [
                                    "FreeFormNumber" => $request->input('mobile')
                                ],
                                "FamilyName" => $FamilyName,
                    ]);

                    $resultingCustomerObj = $this->quickbook->Add($employeee);
                }


                //$error = $this->quickbook->getLastError();
                if ($error) {
                    //return Reply::error(__($error->getOAuthHelperError()));
                }

                $qbo_id = isset($resultingCustomerObj->Id) ? $resultingCustomerObj->Id : '';
                $employee->qbo_id = $qbo_id;
                $employee->save();
            } catch (\Exception $e) {
                
            }
        }

        // bitsclan code end here

        if (empty($employee)) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
        }
        $employee->employee_id = $request->employee_id;
        $employee->address = $request->address;
        $employee->hourly_rate = $request->hourly_rate;
        $employee->slack_username = $request->slack_username;
        $employee->joining_date = Carbon::createFromFormat($this->global->date_format, $request->joining_date)->format('Y-m-d');

        $employee->last_date = null;

        if ($request->last_date != '') {
            $employee->last_date = Carbon::createFromFormat($this->global->date_format, $request->last_date)->format('Y-m-d');
        }

        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->save();

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $employee->updateCustomFieldData($request->get('custom_fields_data'));
        }

        session()->forget('user');

        return Reply::redirect(route('admin.employees.index'), __('messages.employeeUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $company = company();

        if ($user->id == 1) {
            return Reply::error(__('messages.adminCannotDelete'));
        }
        
        if ($user->email == $company->company_email) {
            return Reply::error(__('messages.adminCannotDelete'));
        }
        

        $universalSearches = UniversalSearch::where('searchable_id', $id)->where('module_type', 'employee')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
        User::destroy($id);
        return Reply::success(__('messages.employeeDeleted'));
    }

    public function tasks($userId, $hideCompleted)
    {
        $taskBoardColumn = TaskboardColumn::where('slug', 'incomplete')->first();

        $tasks = Task::join('task_users', 'task_users.task_id', '=', 'tasks.id')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->join('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->select('tasks.id', 'projects.project_name', 'tasks.heading', 'tasks.due_date', 'tasks.status', 'tasks.project_id', 'taskboard_columns.column_name', 'taskboard_columns.label_color')
             ->whereNull('projects.deleted_at')
            ->where('task_users.user_id', $userId);

        if ($hideCompleted == '1') {
            $tasks->where('tasks.board_column_id', $taskBoardColumn->id);
        }

        $tasks->get();

        return DataTables::of($tasks)
            ->editColumn('due_date', function ($row) {
                if ($row->due_date->isPast()) {
                    return '<span class="text-danger">' . $row->due_date->format($this->global->date_format) . '</span>';
                }
                return '<span class="text-success">' . $row->due_date->format($this->global->date_format) . '</span>';
            })
            ->editColumn('heading', function ($row) {
                $name = '<a href="javascript:;" data-task-id="' . $row->id . '" class="show-task-detail">' . ucfirst($row->heading) . '</a>';

                if ($row->is_private) {
                    $name.= ' <i data-toggle="tooltip" data-original-title="' . __('app.private') . '" class="fa fa-lock" style="color: #ea4c89"></i>';
                }
                return $name;
            })
            ->editColumn('column_name', function($row){
                return '<label class="label" style="background-color: '.$row->label_color.'">'.$row->column_name.'</label>';
             })
            ->editColumn('project_name', function ($row) {
                if (!is_null($row->project_name)) {
                    return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project_name) . '</a>';
                }
            })
            ->rawColumns(['column_name', 'project_name', 'due_date', 'heading'])
            ->removeColumn('project_id')
            ->make(true);
    }

    public function timeLogs($userId)
    {
        $timeLogs = ProjectTimeLog::join('projects', 'projects.id', '=', 'project_time_logs.project_id')
            ->select('project_time_logs.id', 'projects.project_name', 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.total_hours', 'project_time_logs.memo', 'project_time_logs.project_id', 'project_time_logs.total_minutes')
            ->where('project_time_logs.user_id', $userId);
        $timeLogs->get();

        return DataTables::of($timeLogs)
            ->editColumn('start_time', function ($row) {
                return $row->start_time->timezone($this->global->timezone)->format($this->global->date_format . ' ' . $this->global->time_format);
            })
            ->editColumn('end_time', function ($row) {
                if (!is_null($row->end_time)) {
                    return $row->end_time->timezone($this->global->timezone)->format($this->global->date_format . ' ' . $this->global->time_format);
                } else {
                    return "<label class='label label-success'>Active</label>";
                }
            })
            ->editColumn('project_name', function ($row) {
                return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project_name) . '</a>';
            })
            ->editColumn('total_hours', function ($row) {
                $timeLog = intdiv($row->total_minutes, 60) . ' hrs ';

                if (($row->total_minutes % 60) > 0) {
                    $timeLog .= ($row->total_minutes % 60) . ' mins';
                }

                return $timeLog;
            })
            ->rawColumns(['end_time', 'project_name'])
            ->removeColumn('project_id')
            ->make(true);
    }

    public function export($status, $employee, $role)
    {
        if ($role != 'all' && $role != '') {
            $userRoles = Role::findOrFail($role);
        }
        $rows = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->withoutGlobalScope('active')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', '<>', 'client')
            ->leftJoin('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id')

            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.mobile',
                'designations.name as designation_name',
                'employee_details.address',
                'employee_details.hourly_rate',
                'users.created_at',
                'roles.name as roleName'
            );
        if ($status != 'all' && $status != '') {
            $rows = $rows->where('users.status', $status);
        }

        if ($employee != 'all' && $employee != '') {
            $rows = $rows->where('users.id', $employee);
        }

        if ($role != 'all' && $role != '' && $userRoles) {
            if ($userRoles->name == 'admin') {
                $rows = $rows->where('roles.id', $role);
            } elseif ($userRoles->name == 'employee') {
                $rows =  $rows->where(\DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1)"), $role)
                    ->having('roleName', '<>', 'admin');
            } else {
                $rows = $rows->where(\DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1)"), $role);
            }
        }
        $attributes =  ['roleName'];
        $rows = $rows->groupBy('users.id')->get()->makeHidden($attributes);

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Name', 'Email', 'Mobile', 'Designation', 'Address', 'Hourly Rate', 'Created at', 'Role'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($rows as $row) {
            $exportArray[] = [
                "id" => $row->id,
                "name" => $row->name,
                "email" => $row->email,
                "mobile" => $row->mobile,
                "Designation" => $row->designation_name,
                "address" => $row->address,
                "hourly_rate" => $row->hourly_rate,
                "created_at" => $row->created_at->format('Y-m-d h:i:s a'),
                "roleName" => $row->roleName
            ];
        }

        // Generate and return the spreadsheet
        Excel::create('Employees', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Employees');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('Employees file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold' => true
                    ));
                });
            });
        })->download('xlsx');
    }

    public function assignRole(Request $request)
    {
        $userId = $request->userId;
        $roleId = $request->role;
        $employeeRole = Role::where('name', 'employee')->first();
        $user = User::withoutGlobalScope('active')->findOrFail($userId);

        RoleUser::where('user_id', $user->id)->delete();
        $user->roles()->attach($employeeRole->id);
        if ($employeeRole->id != $roleId) {
            $user->roles()->attach($roleId);
        }

        return Reply::success(__('messages.roleAssigned'));
    }

    public function assignProjectAdmin(Request $request)
    {
        $userId = $request->userId;
        $projectId = $request->projectId;
        $project = Project::findOrFail($projectId);
        $project->project_admin = $userId;
        $project->save();

        return Reply::success(__('messages.roleAssigned'));
    }

    public function docsCreate(Request $request, $id)
    {
        $this->employeeID = $id;
        $this->upload = can_upload();
        return view('admin.employees.docs-create', $this->data);
    }

    public function freeEmployees()
    {
        if (\request()->ajax()) {

            $whoseProjectCompleted = ProjectMember::join('projects', 'projects.id', '=', 'project_members.project_id')
                ->join('users', 'users.id', '=', 'project_members.user_id')
                ->select('users.*')
                ->groupBy('project_members.user_id')
                ->havingRaw("min(projects.completion_percent) = 100 and max(projects.completion_percent) = 100")
                ->orderBy('users.id')
                ->get();

            $notAssignedProject = User::join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*')
                ->whereNotIn('users.id', function ($query) {

                    $query->select('user_id as id')->from('project_members');
                })
                ->where('roles.name', '<>', 'client')
                ->get();

            $freeEmployees = $whoseProjectCompleted->merge($notAssignedProject);

            return DataTables::of($freeEmployees)
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.employees.edit', [$row->id]) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                      <a href="' . route('admin.employees.show', [$row->id]) . '" class="btn btn-success btn-circle"
                      data-toggle="tooltip" data-original-title="View Employee Details"><i class="fa fa-search" aria-hidden="true"></i></a>

                      <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                })
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
                ->editColumn('name', function ($row) {
                    
                    if($row->image){
                        $image = '<img src="' . $row->image_url . '" alt="user" class="img-circle" width="30" height="30"> ';
                    } else {
                        $image = '<span class="nameletter">'.company_initials().'</span>';
                    }
                    
                    return '<a href="' . route('admin.employees.show', $row->id) . '">' . $image . ' ' . ucwords($row->name) . '</a>';
                })
                ->rawColumns(['name', 'action', 'role', 'status'])
                ->removeColumn('roleId')
                ->removeColumn('roleName')
                ->removeColumn('current_role')
                ->make(true);
        }

        return view('admin.employees.free_employees', $this->data);
    }
    
//    public function chargebeeView($qty) {
//        
//        $this->qty = $qty;
//        
//        return view('admin.employees.chargebee-view-modal', $this->data);
//    }
}
