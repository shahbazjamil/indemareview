<?php

namespace Modules\Payroll\Http\Controllers;

use App\Attendance;
use App\AttendanceSetting;
use App\Designation;
use App\EmployeeDetails;
use App\Expense;
use App\Helper\Reply;
use App\Holiday;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Leave;
use App\ProjectTimeLog;
use App\Team;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\Entities\EmployeeMonthlySalary;
use Modules\Payroll\Entities\EmployeeSalaryGroup;
use Modules\Payroll\Entities\PayrollSetting;
use Modules\Payroll\Entities\SalaryPaymentMethod;
use Modules\Payroll\Entities\SalarySlip;
use Modules\Payroll\Entities\SalaryTds;
use Modules\Payroll\Notifications\SalaryStatusEmail;
use Yajra\DataTables\Facades\DataTables;

class PayrollController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('payroll::app.menu.payroll');
        $this->pageIcon = 'icon-wallet';
        $this->middleware(function ($request, $next) {
            if (!in_array('payroll', $this->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->departments = Team::all();
        $this->designations = Designation::all();
        
        $now = Carbon::now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');
        $this->salaryPaymentMethods = SalaryPaymentMethod::all();
   
        return view('payroll::payroll.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('payroll::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->salarySlip = SalarySlip::with('user', 'user.employeeDetail', 'salary_group', 'salary_payment_method')->findOrFail($id);
        $salaryJson = json_decode($this->salarySlip->salary_json, true);
        $this->earnings = $salaryJson['earnings'];
        $this->deductions = $salaryJson['deductions'];
        $extraJson = json_decode($this->salarySlip->extra_json, true);
        $this->earningsExtra = $extraJson['earnings'];
        if ($this->earningsExtra == "") {
            $this->earningsExtra = array();
        }
        $this->deductionsExtra = $extraJson['deductions'];
        if ($this->deductionsExtra == "") {
            $this->deductionsExtra = array();
        }
        $view = view('payroll::payroll.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->salarySlip = SalarySlip::with('user', 'user.employeeDetail', 'salary_group', 'salary_payment_method')->findOrFail($id);
        $salaryJson = json_decode($this->salarySlip->salary_json, true);
        $this->earnings = $salaryJson['earnings'];
        $this->deductions = $salaryJson['deductions'];
        $extraJson = json_decode($this->salarySlip->extra_json, true);
        $this->earningsExtra = $extraJson['earnings'];
        $this->deductionsExtra = $extraJson['deductions'];
        if ($this->earningsExtra == "") {
            $this->earningsExtra = array();
        }
        $this->deductionsExtra = $extraJson['deductions'];
        if ($this->deductionsExtra == "") {
            $this->deductionsExtra = array();
        }
        $this->salaryPaymentMethods = SalaryPaymentMethod::all();
        return view('payroll::payroll.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $grossEarning = $request->basic_salary;
        $totalDeductions = 0;
        $reimbursement = $request->expense_claims;
        $earningsName = $request->earnings_name;
        $earnings = $request->earnings;
        $deductionsName = $request->deductions_name;
        $deductions = $request->deductions;
        $extraEarningsName = $request->extra_earnings_name;
        $extraEarnings = $request->extra_earnings;
        $extraDeductionsName = $request->extra_deductions_name;
        $extraDeductions = $request->extra_deductions;

        $earningsArray = array();
        $deductionsArray = array();
        $extraEarningsArray = array();
        $extraDeductionsArray = array();

        if ($earnings != "") {
            foreach ($earnings as $key => $value) {
                $earningsArray[$earningsName[$key]] = floatval($value);
                $grossEarning = $grossEarning + $earningsArray[$earningsName[$key]];
            }    
        }

        foreach ($deductions as $key => $value) {
            $deductionsArray[$deductionsName[$key]] = floatval($value);
            $totalDeductions = $totalDeductions + $deductionsArray[$deductionsName[$key]];
        }

        $salaryComponents = [
            'earnings' => $earningsArray,
            'deductions' => $deductionsArray
        ];
        $salaryComponentsJson = json_encode($salaryComponents);

        if ($extraEarnings != "") {
            foreach ($extraEarnings as $key => $value) {
                $extraEarningsArray[$extraEarningsName[$key]] = floatval($value);
                $grossEarning = $grossEarning + $extraEarningsArray[$extraEarningsName[$key]];
            }    
        }

        if ($extraDeductions != "") {
            foreach ($extraDeductions as $key => $value) {
                $extraDeductionsArray[$extraDeductionsName[$key]] = floatval($value);
                $totalDeductions = $totalDeductions + $extraDeductionsArray[$extraDeductionsName[$key]];
            }
        }

        $extraSalaryComponents = [
            'earnings' => $extraEarningsArray,
            'deductions' => $extraDeductionsArray
        ];
        $extraSalaryComponentsJson = json_encode($extraSalaryComponents);

        $netSalary = $grossEarning - $totalDeductions + $reimbursement;

        $salarySlip = SalarySlip::findOrFail($id);

        if ($request->paid_on != "") {
            $salarySlip->paid_on = Carbon::createFromFormat($this->global->date_format, $request->paid_on)->format('Y-m-d');
        }

        if ($request->salary_payment_method_id != "") {
            $salarySlip->salary_payment_method_id = $request->salary_payment_method_id;
        }

        $salarySlip->status = $request->status;
        $salarySlip->expense_claims = $request->expense_claims;
        $salarySlip->basic_salary = $request->basic_salary;
        $salarySlip->salary_json = $salaryComponentsJson;
        $salarySlip->extra_json = $extraSalaryComponentsJson;
        $salarySlip->tds = $deductionsArray['TDS'];
        $salarySlip->total_deductions = round(($totalDeductions), 2);
        $salarySlip->net_salary = round(($netSalary), 2);
        $salarySlip->gross_salary = round(($grossEarning), 2);
        $salarySlip->save();
        
        return Reply::redirect(route('admin.payroll.index'), __('messages.updateSuccess'));

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        SalarySlip::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function data(Request $request)
    {

        $users = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->join('salary_slips', 'salary_slips.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.image', 'designations.name as designation_name', 'salary_slips.net_salary', 'salary_slips.paid_on', 'salary_slips.status as salary_status', 'salary_slips.id as salary_slip_id')
            ->where('roles.name', '<>', 'client')
            ->where('salary_slips.month', $request->month)
            ->where('salary_slips.year', $request->year)
            ->groupBy('users.id')
            ->orderBy('users.id', 'asc')
            ->get()
            ->makeHidden('unreadNotifications');

        return DataTables::of($users)

            ->addColumn('action', function ($row) {
                return '
                    <a href="javascript:;" data-salary-slip-id="'.$row->salary_slip_id.'" class="btn btn-success btn-circle show-salary-slip"
                    ><i class="fa fa-search" aria-hidden="true"></i></a> 

                    <a href="' . route('admin.payroll.edit', $row->salary_slip_id) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                      <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-salary-id="' . $row->salary_slip_id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn('name', function ($row) {

                $image = '<img src="' . $row->image_url . '"alt="user" class="img-circle" width="30"> ';

                $designation = ($row->designation_name) ? ucwords($row->designation_name) : ' ';

                return  '<div class="row"><div class="col-sm-3 col-xs-4">' . $image . '</div><div class="col-sm-9 col-xs-8"><a href="' . route('admin.employees.show', $row->id) . '" >' . ucwords($row->name) . '</a><br><span class="text-muted font-12">' . $designation . '</span></div></div>';
            })
            ->editColumn('id', function ($row) {
                return '<input type="checkbox" data-user-id="'. $row->id .'" name="salary_ids[]" value="' . $row->salary_slip_id . '" />';
            })
            ->editColumn('net_salary', function ($row) {
                return $this->global->currency->currency_symbol . sprintf('%0.2f', $row->net_salary);
            })
            ->editColumn('salary_status', function ($row) {
                if ($row->salary_status == 'generated') {
                    return '<label class="label label-inverse">' . __('payroll::modules.payroll.generated') . '</label>';
                } elseif ($row->salary_status == 'review') {
                    return '<label class="label label-info">' . __('payroll::modules.payroll.review') . '</label>';
                } elseif ($row->salary_status == 'locked') {
                    return '<label class="label label-danger">' . __('payroll::modules.payroll.locked') . '</label>';
                } elseif ($row->salary_status == 'paid') {
                    return '<label class="label label-success">' . __('payroll::modules.payroll.paid') . '</label>';
                }
                return ucwords($row->salary_status);
            })
            ->editColumn('paid_on', function ($row) {
                if (!is_null($row->paid_on)) {
                    return Carbon::parse($row->paid_on)->format($this->global->date_format);
                } else {
                    return "--";
                }
            })
            ->rawColumns(['name', 'action', 'id', 'salary_status'])
            ->make(true);
    }

    public function generatePaySlip(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $useAttendance = $request->useAttendance;
        $markApprovedLeavesPaid = $request->markLeavesPaid;
        $markAbsentUnpaid = $request->markAbsentUnpaid;
        $includeExpenseClaims = $request->includeExpenseClaims;
        $addTimelogs = $request->addTimelogs;

        $daysInMonth = Carbon::parse('01-' . $month . '-' . $year)->daysInMonth;
        $startDate = Carbon::parse(Carbon::parse('01-' . $month . '-' . $year))->startOfMonth();
        $endDate = Carbon::parse(Carbon::parse('01-' . $month . '-' . $year))->endOfMonth();

        if ($request->userIds) {
            $users = User::with('employeeDetail')->whereIn('users.id', $request->userIds)->get();
        } else {
            $users = User::allEmployees();
        }

        foreach ($users as $user) {

            $userId = $user->id;
            $employeeDetails = EmployeeDetails::where('user_id', $userId)->first();
            $joiningDate = Carbon::parse($employeeDetails->joining_date)->setTimezone($this->global->timezone);

            if ($endDate->greaterThan($joiningDate)) {
            
                if ($useAttendance) {
                    $holidays = Holiday::getHolidayByDates($startDate->toDateString(), $endDate->toDateString())->count(); // Getting Holiday Data

                    $totalWorkingDays = $daysInMonth - $holidays;
            
                    $presentCount = Attendance::countDaysPresentByUser($startDate, $endDate, $userId);// Getting Attendance Data
                    $absentCount = $totalWorkingDays - $presentCount;
            
                    $leaveCount = Leave::where('user_id', $userId)
                        ->where('leave_date', '>=', $startDate)
                        ->where('leave_date', '<=', $endDate)
                        ->where('status', 'approved')
                        ->count();
            
                    if ($markAbsentUnpaid) {
                        if ($markApprovedLeavesPaid) {
                            $presentCount = $presentCount + $leaveCount;
                        }
                    } else {
                        if ($markApprovedLeavesPaid) {
                            $presentCount = $presentCount + $absentCount;
                        } else {
                            $presentCount = $presentCount + $absentCount - $leaveCount;
                        }
                    }
                    
                    $payDays = $presentCount + $holidays;
            
                } else {
                    $payDays = $daysInMonth;
                }

                $monthlySalary = EmployeeMonthlySalary::employeeNetSalary($userId, $endDate); 
                $perDaySalary = $monthlySalary['netSalary'] / $daysInMonth;
                $payableSalary = $perDaySalary * $payDays;
                $basicSalary = $payableSalary;

                $salaryGroup = EmployeeSalaryGroup::with('salary_group.components', 'salary_group.components.component')->where('user_id', $userId)->first();
                
                $earnings = array();
                $earningsTotal = 0;
                $deductions = array();
                $deductionsTotal = 0;

                if (!is_null($salaryGroup)) {
                    
                    foreach ($salaryGroup->salary_group->components as $components) {
                        // calculate earnings
                        if ($components->component->component_type == 'earning') {
                            if ($components->component->value_type == 'fixed') {
                                $basicSalary = $basicSalary - $components->component->component_value;
                                $earnings[$components->component->component_name] = floatval($components->component->component_value);
                            } else {
                                $componentValue = ($components->component->component_value/100) * $payableSalary;
                                $basicSalary = $basicSalary - $componentValue;
                                $earnings[$components->component->component_name] = round(floatval($componentValue), 2);
                            }
                            $earningsTotal = $earningsTotal + $earnings[$components->component->component_name];
                        } else { // calculate deductions
                            if ($components->component->value_type == 'fixed') {
                                // $basicSalary = $basicSalary + $components->component->component_value;
                                $deductions[$components->component->component_name] = floatval($components->component->component_value);
                            } else {
                                $componentValue = ($components->component->component_value/100) * $payableSalary;
                                // $basicSalary = $basicSalary + $componentValue;
                                $deductions[$components->component->component_name] = round(floatval($componentValue), 2);
                            }
                            $deductionsTotal = $deductionsTotal + $deductions[$components->component->component_name];
                        }
                    }
                }

                $salaryTdsTotal = 0;
                $payrollSetting = PayrollSetting::firstOrCreate(['company_id' => company()->id]);

                $today = Carbon::now()->timezone($this->global->timezone);
                $financialyearStart = Carbon::parse($today->year.'-'.$payrollSetting->finance_month.'-01')->setTimezone($this->global->timezone);
                $financialyearEnd = Carbon::parse($today->year.'-'.$payrollSetting->finance_month.'-01')->addYear()->subDays(1)->setTimezone($this->global->timezone);

                $deductions['TDS'] = 0;
                if ($payrollSetting->tds_status) {

                    $annualSalary = $this->calculateTdsSalary($userId, $joiningDate, $financialyearStart, $financialyearEnd, $endDate);
                    
                    if ($payrollSetting->tds_salary < $annualSalary) {
                        $salaryTds = SalaryTds::orderBy('salary_from', 'asc')->get();
                        $taxableSalary = $annualSalary;
                        $previousLimit = 0;
            
                        foreach ($salaryTds as $tds) {
                            if ($annualSalary >= $tds->salary_from && $annualSalary <= $tds->salary_to) {
                                $tdsValue = ($tds->salary_percent/100) * $taxableSalary;
                                $salaryTdsTotal = $salaryTdsTotal + $tdsValue;
                            } elseif ($annualSalary >= $tds->salary_from && $annualSalary >= $tds->salary_to) {
                                $previousLimit = $tds->salary_to - $previousLimit;
                                $taxableSalary = $taxableSalary - $previousLimit;
                                // echo $taxableSalary.'<br>';
            
                                $tdsValue = ($tds->salary_percent/100) * $previousLimit;
                                $salaryTdsTotal = $salaryTdsTotal + $tdsValue;
                            }
                            
                        }
                        // die;
                        // return $salaryTdsTotal;
                        $tdsAlreadyPaid = SalarySlip::where('user_id', $userId)->sum('tds');
                        $tdsToBePaid = $salaryTdsTotal - $tdsAlreadyPaid;
                        $monthDiffFromFinYrEnd = $financialyearEnd->diffInMonths($startDate, true) + 1;
                        $deductions['TDS'] = floatval($tdsToBePaid)/$monthDiffFromFinYrEnd;
                        // $basicSalary = $basicSalary + $deductions['TDS'];
                        $deductionsTotal = $deductionsTotal + $deductions['TDS'];    
                        $deductions['TDS'] = round($deductions['TDS'], 2);
                    }
                }

                // return $deductions;

                $expenseTotal = 0;
                if ($includeExpenseClaims) {
                    $expenseTotal = Expense::where(DB::raw('DATE(purchase_date)'), '>=', $startDate)
                        ->where(DB::raw('DATE(purchase_date)'), '<=', $endDate)
                        ->where('user_id', $userId)
                        ->where('status', 'approved')
                        ->sum('price');
                    $payableSalary = $payableSalary + $expenseTotal;
                }

                if ($addTimelogs) {
                    $earnings['Time Logs'] = ProjectTimeLog::where(DB::raw('DATE(start_time)'), '>=', $startDate)
                        ->where(DB::raw('DATE(start_time)'), '<=', $endDate)
                        ->where('user_id', $userId)
                        ->sum('earnings');
                    $payableSalary = $payableSalary + $earnings['Time Logs'];  
                    $earnings['Time Logs'] = round($earnings['Time Logs'], 2);
                }

                $salaryComponents = [
                    'earnings' => $earnings,
                    'deductions' => $deductions
                ];

                $salaryComponentsJson = json_encode($salaryComponents);

                // return $deductions;
                // return $earnings;
                // return $earningsTotal;
                // return $deductionsTotal;
                // return $salaryComponents;
                // return $basicSalary;
                // return $payableSalary;
                
                $data = [
                    'user_id' => $userId,
                    'salary_group_id' => (($salaryGroup) ? $salaryGroup->salary_group_id : null),
                    'basic_salary' => round($basicSalary, 2),
                    'monthly_salary' => round($monthlySalary['netSalary'], 2),
                    'net_salary' => round(($payableSalary - $deductionsTotal), 2),
                    'gross_salary' => round(($payableSalary - $expenseTotal), 2),
                    'total_deductions' => round(($deductionsTotal), 2),
                    'month' => $month,
                    'year' => $year,
                    'salary_json' => $salaryComponentsJson,
                    'expense_claims' => $expenseTotal,
                    'pay_days' => $payDays,
                    'tds' => $deductions['TDS']
                ];

                // return $data;

                SalarySlip::where('user_id', $userId)->where('month', $month)
                    ->where('year', $year)->delete();

                SalarySlip::create($data);
            }

        }

        return Reply::dataOnly(['status' => 'success']);
    }

    protected function calculateTdsSalary($userId, $joiningDate, $financialyearStart, $financialyearEnd, $payrollMonthEndDate)
    {
        $totalEarning = 0;

        if ($joiningDate->greaterThan($financialyearStart)) {
            $monthlySalary = EmployeeMonthlySalary::employeeNetSalary($userId); 
            $currentSalary = $initialSalary = $monthlySalary['initialSalary']; 
        } else {
            $monthlySalary = EmployeeMonthlySalary::employeeNetSalary($userId, $financialyearStart);
            $currentSalary = $initialSalary = $monthlySalary['netSalary']; 
        }

        $increments = EmployeeMonthlySalary::employeeIncrements($userId);
        $lastIncrement = null;

        foreach ($increments as $increment) {
            $incrementDate = Carbon::parse($increment->date);
            if ($payrollMonthEndDate->greaterThan($incrementDate)) {
                if (is_null($lastIncrement)) {
                    $payDays = $incrementDate->diffInDays($joiningDate, true);
                    $perDaySalary = ($initialSalary / 30); //30 is taken as no of days in a month
                    $totalEarning = $payDays * $perDaySalary;
                    $lastIncrement = $incrementDate;
                    $currentSalary = $increment->amount + $initialSalary;
                } else {
                    $payDays = $incrementDate->diffInDays($lastIncrement, true);
                    $perDaySalary = ($currentSalary / 30);
                    $totalEarning = $totalEarning + ($payDays * $perDaySalary);
                    $lastIncrement = $incrementDate;
                    $currentSalary = $increment->amount + $currentSalary;
                }
            }
            
        }

        if (!is_null($lastIncrement)) {
            $payDays = $financialyearEnd->diffInDays($lastIncrement, true);
            $perDaySalary = ($currentSalary / 30);
            $totalEarning = $totalEarning + ($payDays * $perDaySalary);
        } else {
            $payDays = $financialyearEnd->diffInDays($joiningDate, true);
            $perDaySalary = ($initialSalary / 30); //30 is taken as no of days in a month
            $totalEarning = $payDays * $perDaySalary;
        }

        return $totalEarning;

    }

    public function updateStatus(Request $request)
    {
        $salarySlips = SalarySlip::whereIn('id', $request->salaryIds)->get();
        $data = [
            "status" => $request->status
        ];

        if ($request->status == "paid") {
            $data['salary_payment_method_id'] = $request->paymentMethod;
            $data['paid_on'] = Carbon::createFromFormat($this->global->date_format, $request->paidOn)->toDateString();
        } else {
            $data['salary_payment_method_id'] = null;
            $data['paid_on'] = null;
        }

        foreach ($salarySlips as $key => $value) {
            $salary = SalarySlip::find($value->id);
            $salary->update($data);

            if ($request->status != 'generated') {
                $notifyUser = User::find($salary->user_id);
                $notifyUser->notify(new SalaryStatusEmail($salary));
            }
        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function downloadPdf($id)
    {
        $this->salarySlip = SalarySlip::with('user', 'user.employeeDetail', 'salary_group', 'salary_payment_method')->whereRaw('md5(id) = ?', $id)->firstOrFail();
        $salaryJson = json_decode($this->salarySlip->salary_json, true);
        $this->earnings = $salaryJson['earnings'];
        $this->deductions = $salaryJson['deductions'];
        $extraJson = json_decode($this->salarySlip->extra_json, true);
        $this->earningsExtra = $extraJson['earnings'];
        if ($this->earningsExtra == "") {
            $this->earningsExtra = array();
        }
        $this->deductionsExtra = $extraJson['deductions'];
        if ($this->deductionsExtra == "") {
            $this->deductionsExtra = array();
        }
        
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('payroll::payroll.pdfview', $this->data);
            //   return $pdf->stream();
        return $pdf->download($this->salarySlip->user->employeeDetail->employee_id .'-'.date('F', mktime(0, 0, 0, $this->salarySlip->month, 10)) . "-" . $this->salarySlip->year . '.pdf');
       
    }

}
