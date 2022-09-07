<div class="rpanel-title"> @lang('payroll::modules.payroll.salarySlip') <span><i class="ti-close right-side-toggle"></i></span> </div>
<div class="r-panel-body">
    <div class="row">
        <div class="col-xs-12">
        
            <div class="row">
                <div class="col-sm-8"><h4>@lang('payroll::modules.payroll.salarySlipHeading') {{ \Carbon\Carbon::parse($salarySlip->year.'-'.$salarySlip->month.'-01')->format('F Y') }}</h4></div>
                @if ($user->hasRole('admin'))
                    <div class="col-sm-4 text-right">
                        <a href="{{ route('admin.payroll.downloadPdf', md5($salarySlip->id)) }}" class="btn btn-success  btn-sm"><i class="fa fa-download"></i> @lang('app.download')</a>&nbsp;
                        <a href="{{ route('admin.payroll.edit', $salarySlip->id) }}" class="btn btn-info  btn-sm"><i class="fa fa-edit"></i> @lang('app.edit')</a>&nbsp;
                    </div>                    
                @else
                    <div class="col-sm-4 text-right">
                        <a href="{{ route('member.payroll.downloadPdf', md5($salarySlip->id)) }}" class="btn btn-success btn-sm"><i class="fa fa-download"></i> @lang('app.download')</a>&nbsp;
                    </div> 
                @endif
                <div class="col-sm-12">
                    <h5 class="text-info">@lang('payroll::modules.payroll.salaryDetails')</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 m-b-10">

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th>@lang('app.employee') @lang('app.name')</th>
                                    <td>{{ ucwords($salarySlip->user->name) }}</td>
                                    <th>@lang('modules.employees.employeeId')</th>
                                    <td>{{ $salarySlip->user->employeeDetail->employee_id }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('app.designation')</th>
                                    <td>{{ (!is_null($salarySlip->user->employeeDetail->designation)) ? $salarySlip->user->employeeDetail->designation->name : '-' }}</td>
                                    <th>@lang('modules.employees.joiningDate')</th>
                                    <td>{{ $salarySlip->user->employeeDetail->joining_date->format($global->date_format) }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('payroll::modules.payroll.salaryGroup')</th>
                                    <td>{{ (!is_null($salarySlip->salary_group)) ? $salarySlip->salary_group->group_name : '-' }}</td>
                                    <th>@lang('modules.payments.paidOn')</th>
                                    <td>{{ ($salarySlip->paid_on) ? $salarySlip->paid_on->format($global->date_format) : '--' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('payroll::modules.payroll.salaryPaymentMethod')</th>
                                    <td>{{ ($salarySlip->salary_payment_method_id) ? $salarySlip->salary_payment_method->payment_method : '--' }}</td>
                                    <th>@lang('app.status')</th>
                                    <td>
                                        @if ($salarySlip->status == 'generated')
                                            <label class="label label-inverse">{{ __('payroll::modules.payroll.generated') }}</label>
                                        @elseif ($salarySlip->status == 'review')
                                            <label class="label label-info">{{ __('payroll::modules.payroll.review') }}</label>
                                        @elseif ($salarySlip->status == 'locked')
                                            <label class="label label-danger">{{ __('payroll::modules.payroll.locked') }}</label>
                                        @elseif ($salarySlip->status == 'paid')
                                            <label class="label label-success">{{ __('payroll::modules.payroll.paid') }}</label>
                                        @endif
                                    </td>
                                </tr>
                                

                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="col-md-3 p-t-15">
                    <div class="text-center b-all p-t-15">
                        <small>@lang('payroll::modules.payroll.employeeNetPay')</small>
                        <h4 class="text-info">{{ $global->currency->currency_symbol.sprintf('%0.2f', $salarySlip->net_salary) }}</h4>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr class="active">
                                    <th class="text-uppercase">@lang('payroll::modules.payroll.earning')</th>
                                    <th class="text-right text-uppercase">@lang('app.amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>@lang('payroll::modules.payroll.basicPay')</td>
                                    <td class="text-right text-uppercase">{{ $global->currency->currency_symbol.$salarySlip->basic_salary }}</td>
                                </tr>
                                @foreach ($earnings as $key=>$item)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td class="text-right">{{ $global->currency->currency_symbol.$item }}</td>
                                    </tr>
                                @endforeach
                                
                                @forelse ($earningsExtra as $key=>$item)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td class="text-right">{{ $global->currency->currency_symbol.$item }}</td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr class="active">
                                    <th class="text-uppercase">@lang('payroll::modules.payroll.deduction')</th>
                                    <th class="text-right text-uppercase">@lang('app.amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($deductions as $key=>$item)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td class="text-right">{{ $global->currency->currency_symbol.$item }}</td>
                                </tr>
                            @endforeach
                            @foreach ($deductionsExtra as $key=>$item)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td class="text-right">{{ $global->currency->currency_symbol.$item }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-3 b-t">
                    <h5><strong>@lang('payroll::modules.payroll.grossEarning')</strong></h5>
                </div>
                <div class="col-md-3 text-right b-t">
                    <h5><strong>{{ $global->currency->currency_symbol.$salarySlip->gross_salary }}</strong></h5>
                </div>

                <div class="col-md-3 b-t">
                    <h5><strong>@lang('payroll::modules.payroll.totalDeductions')</strong></h5>
                </div>
                <div class="col-md-3 text-right b-t">
                    <h5><strong>{{ $global->currency->currency_symbol.$salarySlip->total_deductions }}</strong></h5>
                </div>

                <div class="col-md-12 m-t-10">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr class="active">
                                    <th class="text-uppercase">@lang('payroll::modules.payroll.reimbursement')</th>
                                    <th class="text-right text-uppercase">@lang('app.amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>@lang('payroll::modules.payroll.expenseClaims')</td>
                                <td class="text-right">{{ $global->currency->currency_symbol.$salarySlip->expense_claims }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6 b-t">
                    <h5><strong>@lang('app.total') @lang('payroll::modules.payroll.reimbursement')</strong></h5>
                </div>
                <div class="col-md-6 text-right b-t">
                        <h5><strong>{{ $global->currency->currency_symbol.$salarySlip->expense_claims }}</strong></h5>
                    </div>
                </div>

                <div class="col-sm-12 b-all m-t-30">
                    <h3 class="text-center">
                        <strong class="text-uppercase m-r-20">@lang('payroll::modules.payroll.netSalary'):</strong>
                        {{ $global->currency->currency_symbol.sprintf('%0.2f', $salarySlip->net_salary) }}
                    </h3>
                    <h5 class="text-center text-muted">@lang('payroll::modules.payroll.netSalary') = (@lang('payroll::modules.payroll.grossEarning') - @lang('payroll::modules.payroll.totalDeductions') + @lang('payroll::modules.payroll.reimbursement'))</h5>
                </div>
            </div>
        
        </div>
    
    </div>
</div>