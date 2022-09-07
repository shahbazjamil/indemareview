@extends('layouts.app')

@push('head-script')

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/calendar/dist/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}"><!--Owl carousel CSS -->
<link rel="stylesheet" href="{{ asset('plugins/bower_components/owl.carousel/owl.carousel.min.css') }}"><!--Owl carousel CSS -->
<link rel="stylesheet" href="{{ asset('plugins/bower_components/owl.carousel/owl.theme.default.css') }}"><!--Owl carousel CSS -->
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.css') }}">

<style>
    .fc-event{
        font-size: 10px !important;
    }
    #calendar .fc-view-container .fc-view .fc-more-popover{
        top: 136px !important;
        left: 105px !important;
    }
.col-in {
    padding: 0 20px !important;

}

.fc-event {
    font-size: 10px !important;
}
.d-none{display:none !important;}

@media (min-width: 769px) {
    #wrapper .panel-wrapper {
/*        height: 530px;*/
        overflow-y: auto;
    }
}
.finance-table .d-flex, .task-table .d-flex{align-items:center;justify-content:space-between;}
</style>
@endpush
@section('page-title')
<section class="new-dashboard p-t-15">
	<div class="flex-row head d-none"> 
		<div class="col-sm-6"><h2 class="welcome">Welcome!</h2></div>
                <div class="col-sm-6 text-right" id="cur-time">{{$cur_time}}</div>
	</div><!--end of flex-row-->
	<div class="flex-row link-boxes">
            @if(in_array('timelogs',$modules))
                <div class="col" id="timer-section">
                        @if(!is_null($timer))
                            <a href="javascript:;" class="white-box d-flex stop-timer-modal" data-timer-id="{{ $timer->id }}">
                                    <span class="icon">
                                            <img src="{{ asset('img/timer-icon.png') }}" alt="">
                                    </span>
                                    <div>
                                            <h3 id="active-timer">{{ $timer->timer }}</h3>
                                            <span>@lang("app.stop")</span>
                                    </div>
                            </a>
                        @else
                            <a href="javascript:;" class="white-box d-flex timer-modal">
                                        <span class="icon">
                                                <img src="{{ asset('img/timer-icon.png') }}" alt="">
                                        </span>
                                        <div>
                                                <h3>Timer</h3>
                                                <span>Track your work</span>
                                        </div>
                            </a>
                        @endif
                </div>
            @endif
                 @if(in_array('clients',$modules))
                    <div class="col">
                            <a href="{{ route('admin.clients.create') }}" class="white-box d-flex">
                                    <span class="icon">
                                            <img src="{{ asset('img/client-icon.png') }}" alt="">
                                    </span>
                                    <div>
                                            <h3>Client</h3>
                                            <span>Add a new Client</span>
                                    </div>
                            </a><!--end of white-box-->
                    </div><!--end of col-->
                 @endif
                 @if(in_array('vendor',$modules))
		<div class="col">
			<a href="{{ route('admin.vendor.create') }}" class="white-box d-flex">
				<span class="icon">
					<img src="{{ asset('img/partner-icon.png') }}" alt="">
				</span>
				<div>
					<h3>Partner</h3>
					<span>Add a new partner</span>
				</div>
			</a><!--end of white-box-->
		</div><!--end of col-->
                @endif
                @if(in_array('employees',$modules))
		<div class="col"> 
			<a href="{{ route('admin.employees.create') }}" class="white-box d-flex">
				<span class="icon">
					<img src="{{ asset('img/team-icon.png') }}" alt="">
				</span>
				<div>
					<h3>Team</h3>
					<span>Add your team</span>
				</div>
			</a><!--end of white-box-->
		</div><!--end of col-->
                 @endif
                @if(in_array('projects',$modules))
		<div class="col">
			<a href="{{ route('admin.projects.create') }}" class="white-box d-flex">
				<span class="icon">
					<img src="{{ asset('img/project-icon.png') }}" alt="">
				</span>
				<div>
					<h3>Projects</h3>
					<span>Add a new project</span>
				</div>
			</a><!--end of white-box-->
		</div><!--end of col-->
                @endif
                @if(in_array('tasks',$modules))
		<div class="col">
			<a href="{{ route('admin.all-tasks.create') }}" class="white-box d-flex">
				<span class="icon">
					<img src="{{ asset('img/task-icon.png') }}" alt="">
				</span>
				<div>
					<h3>Tasks</h3>
					<span>Add a new task</span>
				</div>
			</a><!--end of white-box-->
		</div><!--end of col-->
                @endif
                @if(in_array("attendance", $modules))
		<div class="col">
                    <a href="javascript:void(0)" id="attendance-div" class="white-box d-flex">
				<span class="icon">
					<img src="{{ asset('img/support-icon.png') }}" alt="">
				</span>
				<div>
					<h3>@lang('app.menu.attendance')</h3>
                                        
                                        @if(is_null($currenntClockIn))
                                            <span>@lang('modules.attendance.clock_in')</span>
                                        @endif
                                        @if(!is_null($currenntClockIn) && is_null($currenntClockIn->clock_out_time))
                                            <span>@lang('modules.attendance.clock_out')</span>
                                        @endif
   
				</div>
			</a><!--end of white-box-->
		</div><!--end of col-->
                 @endif
	</div><!--end of flex-row-->
	<div class="flex-row" style="align-items:unset">
		<div class="col-lg-4 col-xs-12 task-table">
			<div class="white-box">
				<div class="d-flex">
					<h4>My Tasks</h4>
                    <div>
                        
                        <select name="fl_project_id" id="fl_project_id">
                            <option selected=""  value="all">Select Project</option>
                             @forelse($projects as $project)
                                <option value="{{ $project->id }}" >{{$project->project_name}}</option>
                             @empty
                                <option value="">No Project Added</option>
                             @endforelse
                        </select>
					</div>
				</div>
                <table id="allTasks-table">
					<thead>
						<tr>
							<th>Title</th>
							<th class="center">Due</th>
							<th>Log Time</th>
						</tr>
					</thead>
					<tbody>
                                            @forelse($dueTaskArr as $task)
                                            
                                            <tr class="tasks-wrp task-row-{{$task['project_id']}}">
                                                    
                                                    <td><a href="javascript:;" data-task-id="{{$task['id']}}" class="show-task-detail">{{$task['title']}}<span>{{$task['project_name']}}</span></a></td>
                                                    @if($task['due_day'] != '')
                                                        <td><span class='day'>{{$task['due_day']}}</span></td>
                                                    @else
                                                        <td><span class='date'>{{$task['due_date']}}</span></td>
                                                    @endif 
                                                
                                                    <?php
                                                    
                                                       $total_time = $task['logged_time'];
                                                       
                                                       $activeTimer = \App\ProjectTimeLog::taskActiveTimer($task['id']);
                                                        if($task['is_completed']) {
                                                            $total_time = '<span class="time" >'.$total_time.'</span>';
                                                        } else {
                                                            if($activeTimer) {
                                                                $total_time = '<span class="time" id="active-timer-task">'.$activeTimer->timer.'</span><a href="javascript:;" class="task-timer-stop-click"  data-task-id="' . $task['id'] . '" data-timelog-id="' . $activeTimer->id . '" ><span class="task-timer-stop-icon" ></span></a>';
                                                            } else {
                                                                $total_time = '<span class="time" >'.$total_time.'</span><a href="javascript:;" class="task-timer-start-click"  data-task-id="' . $task['id'] . '" data-project-id="' . $task['project_id'] . '" ><span class= "task-timer-start-icon" ></span></a>';
                                                            }
                                                        }
                                                    ?>     
                                                    
                                                    
                                                    
                                                    
                                                     <td><?php echo $total_time; ?></td>
                                                    
<!--                                                    <td><span class="time">{{$task['logged_time']}}</span><i class="fa fa-play-circle"></i></td>-->
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">No Task</td>
                                                </tr>
                                            @endforelse
					</tbody>
				</table>
			</div><!--end of white-box-->
		</div><!--end of col-4-->
		<div class="col-lg-8 col-xs-12 projects-table">
			<div class="white-box">
				<h4>Projects <a href="{{ route('admin.projects.index') }}">Go to Projects</a></h4>
				<table>
					<thead>
						<tr>
							<th class="center">Project Name | Client</th>
							<th class="center">Due Date</th>
							<th class="center">Estimated Duration</th>
							<th class="center">Week / Max</th>
							<th class="center">Total Done</th>
							<th class="center">Progress</th>
							<th class="center">Status</th>
							<th class="">Action</th>
						</tr>
					</thead>
					<tbody>
                                            
                                            
                                            @forelse($projects as $row)
                                            
                                                <?php 

                                                $hoursLogged = $row->times()->sum('total_minutes');
                                                $hoursLogged = intdiv($hoursLogged, 60);
                                                
                                                 $status = '';
                                                    if ($row->status == 'in progress') {
                                                        $status =  __('app.inProgress');
                                                    } else if ($row->status == 'on hold') {
                                                        $status = __('app.onHold');
                                                    } else if ($row->status == 'not started') {
                                                        $status = __('app.notStarted');
                                                    } else if ($row->status == 'canceled') {
                                                        $status =  __('app.canceled');
                                                    } else if ($row->status == 'finished') {
                                                        $status =  __('app.finished');
                                                    }
                                                    

                                                ?>
                                                <tr>
                                                        <td> <a href="{{route('admin.projects.show', $row->id)}}"> {{ucfirst($row->project_name)}}</a> <span> <?php if (!is_null($row->client_id)) { echo ucwords($row->client->name); }else { echo ''; } ?> </span></td>
                                                        <td class="center"> <?php if ($row->deadline) { echo $row->deadline->format($global->date_format);} else { echo ''; } ?></td>
                                                        <td class="center"> <?php if ($row->hours_allocated) { echo $row->hours_allocated; } else { echo  '0'; } ?> h</td>
                                                        <td class="center"><?php if ($row->getTotalWeeklyLogedHours()) { echo $row->getTotalWeeklyLogedHours(); } else { echo '0'; } ?> h/<?php if ($row->max_weekly_hours) { echo $row->max_weekly_hours; } else { echo '0'; } ?> h</td>
							<td class="center">{{$hoursLogged}} h</td>
							<td>
								<div class="progress">
                                                                    
                                                                    <?php
                                                                        if ($row->completion_percent < 50) {
                                                                            echo '<div class="progress-bar progress-bar-danger" style="width: '.$row->completion_percent.'%"><span class="sr-only">'.$row->completion_percent.'% Complete (danger)</span></div>';
                                                                        } elseif ($row->completion_percent >= 50 && $row->completion_percent < 75) {
                                                                            echo '<div class="progress-bar progress-bar-warning" style="width: '.$row->completion_percent.'%"><span class="sr-only">'.$row->completion_percent.'% Complete (warning)</span></div>';
                                                                        } else {
                                                                            echo '<div class="progress-bar progress-bar-primary" style="width: '.$row->completion_percent.'%"><span class="sr-only">'.$row->completion_percent.'% Complete (success)</span></div>';
                                                                        }
                                                                    
                                                                    ?>
    
								</div>
							</td>
							<td><span class="label label-success"><?php echo $status; ?></span></td>
							<td>
								<div class="btn-group dropdown m-r-10">
									<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
									<ul role="menu" class="dropdown-menu pull-right">
									  
                                                                          
                                                                        <li><a href="{{ route('admin.projects.edit', [$row->id]) }}"><i class="fa fa-pencil" aria-hidden="true"></i> {{ trans('app.edit') }}</a></li>
                                                                        <li><a href="{{ route('admin.projects.show', [$row->id]) }}"><i class="fa fa-search" aria-hidden="true"></i> {{trans('app.view') . ' ' . trans('app.details') }}</a></li>
                                                                        <li><a href="{{ route('admin.projects.gantt', [$row->id]) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> {{ trans('modules.projects.viewGanttChart') }}</a></li>
                                                                        <li><a href="{{ route('front.gantt', [md5($row->id)]) }}" target="_blank"><i class="fa fa-line-chart" aria-hidden="true"></i> {{ trans('modules.projects.viewPublicGanttChart') }}</a></li>
                                                                          
                                                                          
									</ul>
								</div>
							</td>
                                                        
						</tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8">No Project</td>
                                                </tr>
                                            @endforelse
                                            
                                            
						
						
						
					</tbody>
				</table>
			</div><!--end of white-box-->
		</div><!--end of col-8-->
		<div class="col-lg-4 col-xs-12 finance-table">
			<div class="white-box">
				<div class="d-flex">
				<h4>Finance Overview</h4>
                                <div>
                                        <select id="finance_sl" name="finance_sl">
                                                        <option selected="" value="today">Today</option>
							<option value="week">Week</option>
							<option value="month">Month</option>
                                                        <option value="yeartodate">Year to Date</option>
                                        </select>
					</div>
					</div>
                            <table class="finance_wrp" id="finance_today">
					<tbody>
						<tr>
							<td>Total outstanding invoices<span>Invoice total that has not been paid.</span></td>
							<td>${{$invoiceRevenueToday}}</td>
						</tr>
						<tr>
							<td>Invoice revenue<span>Total service based fees, and markup combined</span></td>
							<td>${{$outstandingInvoicesToday}}</td>
						</tr>
						<tr>
							<td>Total markup revenue<span>Total of markup with all products for all projects.</span></td>
							<td>${{$productMarkupFixToday}}</td>
						</tr>
						<tr>
							<td>Total hours billed<span>Total hours billed to the client - paid</span></td>
							<td>{{$totalHoursLoggedToday}}</td>
						</tr>
						<tr>
							<td>Expense<span>Total value of the expenses.</span></td>
							<td>${{ $countsToday->totalExpenses ? number_format($countsToday->totalExpenses) : 0 }}</td>
						</tr>	
					</tbody>
				</table>
                            <table class="finance_wrp" id="finance_week" style="display :none;">
					<tbody>
						<tr>
							<td>Total outstanding invoices<span>Invoice total that has not been paid.</span></td>
							<td>${{$invoiceRevenueWeek}}</td>
						</tr>
						<tr>
							<td>Invoice revenue<span>Total service based fees, and markup combined</span></td>
							<td>${{$outstandingInvoicesWeek}}</td>
						</tr>
						<tr>
							<td>Total markup revenue<span>Total of markup with all products for all projects.</span></td>
							<td>${{$productMarkupFixWeek}}</td>
						</tr>
						<tr>
							<td>Total hours billed<span>Total hours billed to the client - paid</span></td>
							<td>{{$totalHoursLoggedWeek}}</td>
						</tr>
						<tr>
							<td>Expense<span>Total value of the expenses.</span></td>
							<td>${{ $countsWeek->totalExpenses ? number_format($countsWeek->totalExpenses) : 0 }}</td>
								
						</tr>	
					</tbody>
				</table>
                            
                                <table class="finance_wrp" id="finance_month" style="display :none;">
					<tbody>
						<tr>
							<td>Total outstanding invoices<span>Invoice total that has not been paid.</span></td>
							<td>${{$invoiceRevenueMonth}}</td>
						</tr>
						<tr>
							<td>Invoice revenue<span>Total service based fees, and markup combined</span></td>
							<td>${{$outstandingInvoicesMonth}}</td>
						</tr>
						<tr>
							<td>Total markup revenue<span>Total of markup with all products for all projects.</span></td>
							<td>${{$productMarkupFixMonth}}</td>
						</tr>
						<tr>
							<td>Total hours billed<span>Total hours billed to the client - paid</span></td>
							<td>{{$totalHoursLoggedMonth}}</td>
						</tr>
						<tr>
							<td>Expense<span>Total value of the expenses.</span></td>
							<td>${{ $countsMonth->totalExpenses ? number_format($countsMonth->totalExpenses) : 0 }}</td>
						</tr>	
					</tbody>
				</table>
                            
                                <table class="finance_wrp" id="finance_yeartodate" style="display :none;">
					<tbody>
						<tr>
							<td>Total outstanding invoices<span>Invoice total that has not been paid.</span></td>
							<td>${{$invoiceRevenueYear}}</td>
						</tr>
						<tr>
							<td>Invoice revenue<span>Total service based fees, and markup combined</span></td>
							<td>${{$outstandingInvoicesYear}}</td>
						</tr>
						<tr>
							<td>Total markup revenue<span>Total of markup with all products for all projects.</span></td>
							<td>${{$productMarkupFixYear}}</td>
						</tr>
						<tr>
							<td>Total hours billed<span>Total hours billed to the client - paid</span></td>
							<td>{{$totalHoursLoggedYear}}</td>
						</tr>
						<tr>
							<td>Expense<span>Total value of the expenses.</span></td>
							<td>${{ $countsYear->totalExpenses ? number_format($countsYear->totalExpenses) : 0 }}</td>
						</tr>	
					</tbody>
				</table>
                            
			</div><!--end of white-box-->
		</div><!--end of col-4-->
		<div class="col-lg-8 col-xs-12 overdue-invoices-table">
			<div class="white-box">
				<h4>Overdue Invoices</h4>
				<table>
				<thead>
					<tr>
						<th>Invoice Number</th>
						<th>Project</th>
						<th>Total</th>
						<th>Due</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
					<tbody>
                                            @forelse($invoices as $row)
                                            <?php 
                                            $currencySymbol = $row->currency->currency_symbol; 
                                            $due_date = '';
                                            if( $row->due_date) {
                                                $due_date = $row->due_date->format($global->date_format);
                                            }
                                            
                                            
                                             $status = '';
                                            if ($row->credit_note) {
                                                $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . strtoupper(__('app.credit-note')) . '</a> ';
                                            } else {
                                                if ($row->status == 'unpaid') {
                                                    $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . __('app.'.$row->status) . '</a> ';
                                                } elseif ($row->status == 'paid') {
                                                    $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . __('app.'.$row->status) . '</a> ';
                                                } elseif ($row->status == 'draft') {
                                                    $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . __('app.'.$row->status) . '</a> ';
                                                } elseif ($row->status == 'canceled') {
                                                    $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . __('app.'.$row->status) . '</a> ';
                                                } elseif ($row->status == 'review') {
                                                    return '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . __('app.'.$row->status) . '</a> ';
                                                } else {
                                                    $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . strtoupper(__('modules.invoices.partial')) . '</a> ';
                                                }

                                            }
                                            if (!$row->send_status && $row->status != 'draft') {
                                                $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">' . strtoupper(__('modules.invoices.notSent')) . '</a> ';
                                            }

                                            if ($row->refund_status == 'refund') {
                                                $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">Refund</a> ';
                                            } else if($row->refund_status == 'partial_refund'){
                                                 $status.= '<a href="javascript:void(0)" class="btn btn-outline btn-success btn-sm">Partial Refund</a> ';
                                            }
                                            
                                            
                                            $action = '<li><a href="' . route("admin.client-invoice.download", $row->id) . '"><i class="fa fa-download"></i> ' . __('app.download') . '</a></li>';
                                            $action .= '<li><a href="' . route("admin.client-invoice.edit", $row->id) . '"><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                                            
                                            if ($row->status != 'paid' && $row->credit_note == 0 && $row->status != 'draft') {
                                                $action .= '<li><a href="' . route("front.invoice", [md5($row->id)]) . '" target="_blank" data-toggle="tooltip" ><i class="fa fa-link"></i> ' . __('modules.payments.paymentLink') . '</a></li>';
                                            }
                                            if ($row->status != 'paid') {
                                                $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="reminderButton"><i class="fa fa-money"></i> ' . __('app.paymentReminder') . '</a></li>';
                                            }
                                            $action .= '<li><a href="' . route("admin.client-invoice.view", $row->id) . '"><i class="fa fa-eye"></i> View </a></li>';
                                            $action .= '<li><a href="' . route("admin.purchase-orders.convert-purchase-order", $row->id) . '" ><i class="ti-receipt"></i> ' . __('app.create') . ' Purchase Order </a></li>';

                
                                            
                                            
                                            
                                            ?>
						<tr>
							<td>{{ ucfirst($row->invoice_number)}}</td>
                                                        <td><?php if($row->project_id != null && $row->project) { echo  ucfirst($row->project->project_name); } else { echo ''; }   ?></td>
							<td><?php echo currency_position($row->total, $currencySymbol); ?></td>
							<td>{{$due_date}}</td>
							<td>
                                                            <?php echo $status; ?>

							</td>
							<td>
								<div class="btn-group dropdown m-r-10">
									<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
									<ul role="menu" class="dropdown-menu pull-right">
									  <?php echo $action; ?>
									</ul>
								</div>
							</td>
						</tr>
                                            @empty
                                                <tr><td colspan="6">No Invoice</td></tr>
                                            
                                            @endforelse
						
					</tbody>
				</table>
			</div><!--end of white-box-->
		</div><!--end of col-8-->
		<div class="col-md-4 d-none">
			<div class="white-box">				
               <div class="flex-row justify-between c-perform">
					<h4>Company Performance</h4>					
					<ul class="nav nav-tabs fc-button-group" role="tablist">
						<li role="presentation" class="active"><a href="#month" id="month-btn" class="fc-button" aria-controls="month" role="tab" data-toggle="tab">Month</a></li>
						<li role="presentation"><a href="#week" class="fc-button" id="week-btn" aria-controls="week" role="tab" data-toggle="tab">Week</a></li>
						<li role="presentation"><a href="#today" class="fc-button" id="today-btn" aria-controls="today" role="tab" data-toggle="tab">Today</a></li>
					</ul>
				</div><!--end of flex-row-->
				 <div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="month">					
					   <div class="flex-row justify-between dgrey open-projects">
							<span><label>{{ $countsMonth->openProjects }}</label> Open Projects</span>
							<a href="{{ route('admin.projects.index') }}">Manage Projects <i class="fa fa-angle-right"></i></a>
						</div><!--end of flex-row-->				
					   <div class="flex-row">
							<div class="col">
								<a href="{{ route('admin.client-invoice.index') }}" class="white-box border-link">
									<b>{{ $countsMonth->totalUnpaidInvoices }}</b>
									<i class="fa fa-angle-right"></i>
									Invoices Due
								</a>
							</div><!--end of col-->
							<div class="col">
								<a href="{{ route('admin.all-tasks.index') }}" class="white-box border-link">
									<b>{{ $countsMonth->totalPendingTasks }}</b>
									<i class="fa fa-angle-right"></i>
									Due Tasks
								</a>
							</div><!--end of col-->
							<div class="col">
								<a href="{{ route('admin.leads.index') }}" class="white-box border-link">
									<b>{{ $countsMonth->newLeads }}</b>
									<i class="fa fa-angle-right"></i>
									New Leads
								</a>
							</div><!--end of col-->
						</div><!--end of flex-row-->
                                                <div style="display: none;" class="flex-row">
							<div class="col-md-3">
								<figure class="highcharts-figure">
									<div id="cpmcontainer"></div>
								</figure>
							</div><!--end of col-3-->
							<div class="col-md-9">
								<div class="d-flex detailbars">
									<div class="title">On Time ({{ $countsMonth->onTimeP }}%)</div>
									<div class="p-bar ontime"><span style="width:{{ $countsMonth->onTimeP }}%"></span></div>
									<div class="num">{{ $countsMonth->onTimeP }}</div>
								</div>
								<div class="d-flex detailbars">
									<div class="title">Delayed ({{ $countsMonth->delayedP }}%)</div>
									<div class="p-bar delayed"><span style="width:{{ $countsMonth->delayedP }}%"></span></div>
									<div class="num">{{ $countsMonth->delayedP }}</div>
								</div>
								<div class="d-flex detailbars">
									<div class="title">On Hold ({{ $countsMonth->onHoldP }}%)</div>
									<div class="p-bar onhold"><span style="width:{{ $countsMonth->onHoldP }}%"></span></div>
									<div class="num">{{ $countsMonth->onHoldP }}</div>
								</div>
							</div><!--end of col-9-->
						</div><!--end of flex-row-->
					</div>
					<div role="tabpanel" class="tab-pane" id="week">				
					   <div class="flex-row justify-between dgrey open-projects">
							<span><label>{{ $countsWeek->openProjects }}</label> Open Projects</span>
							<a href="{{ route('admin.projects.index') }}">Manage Projects <i class="fa fa-angle-right"></i></a>
						</div><!--end of flex-row-->				
					   <div class="flex-row">
							<div class="col">
								<a href="{{ route('admin.client-invoice.index') }}" class="white-box border-link">
									<b>{{ $countsWeek->totalUnpaidInvoices }}</b>
									<i class="fa fa-angle-right"></i>
									Invoices Due
								</a>
							</div><!--end of col-->
							<div class="col">
								<a href="{{ route('admin.all-tasks.index') }}" class="white-box border-link">
									<b>{{ $countsWeek->totalPendingTasks }}</b>
									<i class="fa fa-angle-right"></i>
									Due Tasks
								</a>
							</div><!--end of col-->
							<div class="col">
								<a href="{{ route('admin.leads.index') }}" class="white-box border-link">
									<b>{{ $countsWeek->newLeads }}</b>
									<i class="fa fa-angle-right"></i>
									New Leads
								</a>
							</div><!--end of col-->
						</div><!--end of flex-row-->
						<div style="display: none;" class="flex-row">
							<div class="col-md-3">
								<figure class="highcharts-figure">
									<div id="cpwcontainer"></div>
								</figure>
							</div><!--end of col-3-->
							<div class="col-md-9">
								<div class="d-flex detailbars">
									<div class="title">On Time ({{ $countsWeek->onTimeP }}%)</div>
									<div class="p-bar ontime"><span style="width:{{ $countsWeek->onTimeP }}%"></span></div>
									<div class="num">{{ $countsWeek->onTimeP }}</div>
								</div>
								<div class="d-flex detailbars">
									<div class="title">Delayed ({{ $countsWeek->delayedP }}%)</div>
									<div class="p-bar delayed"><span style="width:{{ $countsWeek->delayedP }}%"></span></div>
									<div class="num">{{ $countsWeek->delayedP }}</div>
								</div>
								<div class="d-flex detailbars">
									<div class="title">On Hold ({{ $countsWeek->onHoldP }}%)</div>
									<div class="p-bar onhold"><span style="width:{{ $countsWeek->onHoldP }}%"></span></div>
									<div class="num">{{ $countsWeek->onHoldP }}</div>
								</div>
							</div><!--end of col-9-->
						</div><!--end of flex-row-->
					</div>
					<div role="tabpanel" class="tab-pane" id="today">					
					   <div class="flex-row justify-between dgrey open-projects">
							<span><label>{{ $countsToday->openProjects }}</label> Open Projects</span>
							<a href="{{ route('admin.projects.index') }}">Manage Projects <i class="fa fa-angle-right"></i></a>
						</div><!--end of flex-row-->				
					   <div class="flex-row">
							<div class="col">
								<a href="{{ route('admin.client-invoice.index') }}" class="white-box border-link">
									<b>{{ $countsToday->totalUnpaidInvoices }}</b>
									<i class="fa fa-angle-right"></i>
									Invoices Due
								</a>
							</div><!--end of col-->
							<div class="col">
								<a href="{{ route('admin.all-tasks.index') }}" class="white-box border-link">
									<b>{{ $countsToday->totalPendingTasks }}</b>
									<i class="fa fa-angle-right"></i>
									Due Tasks
								</a>
							</div><!--end of col-->
							<div class="col">
								<a href="{{ route('admin.leads.index') }}" class="white-box border-link">
									<b>{{ $countsToday->newLeads }}</b>
									<i class="fa fa-angle-right"></i>
									New Leads
								</a>
							</div><!--end of col-->
						</div><!--end of flex-row-->
						<div style="display: none;" class="flex-row">
							<div class="col-md-3">
								<figure class="highcharts-figure">
									<div id="cpdcontainer"></div>
								</figure>
							</div><!--end of col-3-->
							<div class="col-md-9">
								<div class="d-flex detailbars">
									<div class="title">On Time ({{ $countsToday->onTimeP }}%)</div>
									<div class="p-bar ontime"><span style="width:{{ $countsToday->onTimeP }}%"></span></div>
									<div class="num">{{ $countsToday->onTimeP }}</div>
								</div>
								<div class="d-flex detailbars">
									<div class="title">Delayed ({{ $countsToday->delayedP }}%)</div>
									<div class="p-bar delayed"><span style="width:{{ $countsToday->delayedP }}%"></span></div>
									<div class="num">{{ $countsToday->delayedP }}</div>
								</div>
								<div class="d-flex detailbars">
									<div class="title">On Hold ({{ $countsToday->onHoldP }}%)</div>
									<div class="p-bar onhold"><span style="width:{{ $countsToday->onHoldP }}%"></span></div>
									<div class="num">{{ $countsToday->onHoldP }}</div>
								</div>
							</div><!--end of col-9-->
						</div><!--end of flex-row-->
					</div>
				</div>
			</div><!--end of white-box-->
			<div class="white-box m-0">
               <div class="flex-row justify-between c-perform month">
					<h4>Revenue Health</h4>
					<div>
						<select>
							<option value="Monthly">Monthly</option>
							<option value="Weekly">Weekly</option>
							<option value="Daily">Daily</option>
						</select>
					</div>	
				</div><!--end of flex-row-->
				<div class="revenue-data month">
      				<div class="flex-row justify-between graph-summary">				
						<div class="col">
							<span class="square-icon blue"></span>Earnings
						</div>				
						<div class="col">
							<span class="square-icon grey"></span>Expense
						</div>			
						<div class="col">
							<span class="img-icon"><img src="{{ asset('img/graph-icon.png') }}" alt=""></span>
							<div>
								<span>Earnings</span>
								${{ $countsMonth->totalPayments ? $countsMonth->totalPayments: 0 }}
							</div>
						</div>		
						<div class="col">
							<span class="img-icon grey"><img src="{{ asset('img/graph-icon.png') }}" alt=""></span>
							<div>
								<span>Expense</span>
								${{ $countsMonth->totalExpenses ? $countsMonth->totalExpenses: 0 }}
							</div>
						</div>	
					</div><!--end of flex-row-->
					<figure class="highcharts-figure">
						<div id="rmcontainer"></div>
					</figure>
				</div><!--end of revenue-data-->
				<div class="revenue-data week">
      				<div class="flex-row justify-between graph-summary">				
						<div class="col">
							<span class="square-icon blue"></span>Earnings
						</div>				
						<div class="col">
							<span class="square-icon grey"></span>Expense
						</div>			
						<div class="col">
							<span class="img-icon"><img src="{{ asset('img/graph-icon.png') }}" alt=""></span>
							<div>
								<span>Earnings</span>
								${{ $countsWeek->totalPayments ?$countsWeek->totalPayments : 0 }}
							</div>
						</div>		
						<div class="col">
							<span class="img-icon grey"><img src="{{ asset('img/graph-icon.png') }}" alt=""></span>
							<div>
								<span>Expense</span>
								${{ $countsWeek->totalExpenses ?$countsWeek->totalExpenses : 0 }}
							</div>
						</div>	
					</div><!--end of flex-row-->
					<figure class="highcharts-figure">
						<div id="rwcontainer"></div>
					</figure>
				</div><!--end of revenue-data-->
				<div class="revenue-data daily">
      				<div class="flex-row justify-between graph-summary">				
						<div class="col">
							<span class="square-icon blue"></span>Earnings
						</div>				
						<div class="col">
							<span class="square-icon grey"></span>Expense
						</div>			
						<div class="col">
							<span class="img-icon"><img src="{{ asset('img/graph-icon.png') }}" alt=""></span>
							<div>
								<span>Earnings</span>
								${{ $countsToday->totalPayments ?$countsToday->totalPayments : 0 }}
							</div>
						</div>		
						<div class="col">
							<span class="img-icon grey"><img src="{{ asset('img/graph-icon.png') }}" alt=""></span>
							<div>
								<span>Expense</span>
								${{ $countsToday->totalExpenses ? $countsToday->totalExpenses : 0 }}
							</div>
						</div>	
					</div><!--end of flex-row-->
					<figure class="highcharts-figure">
						<div id="rdcontainer"></div>
					</figure>
				</div><!--end of revenue-data-->
			</div><!--end of col-4-->
		</div><!--end of col-4-->
                <div class="col-md-12">
			<div class="white-box h-100">
                            @if(in_array('events',$modules)  && in_array('settings_leaves',$activeWidgets))
                                    <div class="panel panel-inverse">
                                        <div class="panel-heading">CALENDAR</div>
                                        <div class="panel-wrapper collapse in" style="overflow: auto">
                                            <div class="panel-body">
                                                <div id="calendar_events"></div>
                                            </div>
                                        </div>
                                    </div>
                            @endif
			</div><!--end of col-4-->
		</div><!--end of col-8-->
	</div><!--end of flex-row-->
</section>


<div class="modal fade bs-modal-md in" id="my-event" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    {{--<h4 class="modal-title"><i class="icon-plus"></i> @lang('modules.events.addEvent')</h4>--}}
                    <h4 class="modal-title"><i class="icon-plus"></i> Add Schedule</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['id'=>'createEvent','class'=>'ajax-form','method'=>'POST']) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    {{--<label>@lang('modules.events.eventName')</label>--}}
                                    <label>Schedule</label>
                                    <input type="text" name="event_name" id="event_name" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <label>@lang('modules.sticky.colors')</label>
                                    <select id="colorselector" name="label_color">
                                        <option value="bg-info" data-color="#5475ed" selected>Blue</option>
                                        <option value="bg-warning" data-color="#f1c411">Yellow</option>
                                        <option value="bg-purple" data-color="#ab8ce4">Purple</option>
                                        <option value="bg-danger" data-color="#ed4040">Red</option>
                                        <option value="bg-success" data-color="#00c292">Green</option>
                                        <option value="bg-inverse" data-color="#4c5667">Grey</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label>@lang('modules.events.where')</label>
                                    <input type="text" name="where" id="where" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 ">
                                <div class="form-group">
                                    <label>@lang('app.description')</label>
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-md-3 ">
                                <div class="form-group">
                                    <label>@lang('modules.events.startOn')</label>
                                    <input type="text" name="start_date" id="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-5 col-md-3">
                                <div class="form-group input-group bootstrap-timepicker timepicker">
                                    <label>&nbsp;</label>
                                    <input type="text" name="start_time" id="start_time"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-xs-6 col-md-3">
                                <div class="form-group">
                                    <label>@lang('modules.events.endOn')</label>
                                    <input type="text" name="end_date" id="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-5 col-md-3">
                                <div class="form-group input-group bootstrap-timepicker timepicker">
                                    <label>&nbsp;</label>
                                    <input type="text" name="end_time" id="end_time"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12"  id="attendees">
                                <div class="form-group">
                                    <label class="col-xs-3 m-t-10">@lang('modules.events.addAttendees')</label>
                                    <div class="col-xs-7">
                                        <div class="checkbox checkbox-info">
                                            <input id="all-employees" name="all_employees" value="true"
                                                   type="checkbox">
                                            <label for="all-employees">@lang('modules.events.allEmployees')</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <select class="select2 m-b-10 select2-multiple " multiple="multiple"
                                            data-placeholder="@lang('modules.messages.chooseMember'), @lang('modules.projects.selectClient')" name="user_id[]">
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ ucwords($emp->name) }} @if($emp->id == $user->id)
                                                    (YOU) @endif</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <div class="checkbox checkbox-info">
                                        <input id="repeat-event" name="repeat" value="yes"
                                               type="checkbox">
                                        <label for="repeat-event">@lang('modules.events.repeat')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="repeat-fields" style="display: none">
                            <div class="col-xs-6 col-md-3 ">
                                <div class="form-group">
                                    <label>@lang('modules.events.repeatEvery')</label>
                                    <input type="number" min="1" value="1" name="repeat_count" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <select name="repeat_type" id="" class="form-control">
                                        <option value="day">Day(s)</option>
                                        <option value="week">Week(s)</option>
                                        <option value="month">Month(s)</option>
                                        <option value="year">Year(s)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-6 col-md-3">
                                <div class="form-group">
                                    <label>@lang('modules.events.cycles') <a class="mytooltip" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.events.cyclesToolTip')</span></span></span></a></label>
                                    <input type="text" name="repeat_cycles" id="repeat_cycles" class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                    {!! Form::close() !!}

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn btn-success save-event waves-effect waves-light">@lang('app.submit')</button>
                </div>
            </div>
        </div>
    </div>


 <div class="modal fade bs-modal-md in" id="eventDetailModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

{{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in"  id="attendanceModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-attendance-data-application">
            <div class="modal-content">
                <div class="modal-header">@lang('app.menu.attendance')</div>
                <div class="modal-body">
                        @if(in_array('attendance',$modules))
                            
                                <div class="panel panel-inverse">
                                    <div class="panel-wrapper collapse in">
                                        <div class="panel-body d-flex-border">
                                            <input type="hidden" id="current-latitude">
                                            <input type="hidden" id="current-longitude">
                                            @if (!isset($noClockIn))

                                                @if(!$checkTodayHoliday)
                                                    @if($todayTotalClockin < $maxAttandenceInDay)
                                                        <div class="col-xs-6">
                                                            <h3 class="m-0">@lang('modules.attendance.clock_in')</h3>
                                                        </div>
                                                        <div class="col-xs-6 border-right-0">
                                                            <h3 class="m-0">@lang('modules.attendance.clock_in') IP</h3>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            @if(is_null($currenntClockIn))
                                                                {{ \Carbon\Carbon::now()->timezone($global->timezone)->format($global->time_format) }}
                                                            @else
                                                                {{ $currenntClockIn->clock_in_time->timezone($global->timezone)->format($global->time_format) }}
                                                            @endif
                                                        </div>
                                                        <div class="col-xs-6 border-right-0">
                                                            {{ $currenntClockIn->clock_in_ip ?? request()->ip() }}
                                                        </div>

                                                        @if(!is_null($currenntClockIn) && !is_null($currenntClockIn->clock_out_time))
                                                            <div class="col-xs-6 m-t-20">
                                                                <label for="">@lang('modules.attendance.clock_out')</label>
                                                                <br>{{ $currenntClockIn->clock_out_time->timezone($global->timezone)->format($global->time_format) }}
                                                            </div>
                                                            <div class="col-xs-6 m-t-20">
                                                                <label for="">@lang('modules.attendance.clock_out') IP</label>
                                                                <br>{{ $currenntClockIn->clock_out_ip }}
                                                            </div>
                                                        @endif

                                                        <div class="col-xs-12 m-t-20 truncate border-0">
                                                            <label for="">@lang('modules.attendance.working_from')</label>
                                                            @if(is_null($currenntClockIn))
                                                                <input type="text" class="form-control" id="working_from" name="working_from">
                                                            @else
                                                                <br> {{ $currenntClockIn->working_from }}
                                                            @endif
                                                            @if(is_null($currenntClockIn))
                                                                <button class="btn btn-success btn-sm margin-top-10" id="clock-in">@lang('modules.attendance.clock_in')</button>
                                                            @endif
                                                            @if(!is_null($currenntClockIn) && is_null($currenntClockIn->clock_out_time))
                                                                <button class="btn btn-danger btn-sm m-t-10" id="clock-out">@lang('modules.attendance.clock_out')</button>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="col-xs-12 border-right-0">
                                                            <div class="alert alert-info- m-0">@lang('modules.attendance.maxColckIn')</div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="col-xs-12">
                                                        <div class="alert alert-info- alert-dismissable m-0 border-right-0">
                                                            <b>@lang('modules.dashboard.holidayCheck') {{ ucwords($checkTodayHoliday->occassion) }}.</b> </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="col-xs-12 text-center">
                                                    <h4><i class="ti-alert text-danger"></i></h4>
                                                    <h4>@lang('messages.officeTimeOver')</h4>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            
                            @endif
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}


<?php 

$paymentVar = implode(',', $paymentsArr); 
$expensesVar = implode(',', $expensesArr); 

?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/variable-pie.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    
    
    var finance_sl = document.getElementById('finance_sl');
    finance_sl.onchange = function () {
        var finance_val = $('#finance_sl').val()
        $('.finance_wrp').hide();
        $('#finance_'+finance_val).show();
    }
    
    var fl_project_id = document.getElementById('fl_project_id');
    fl_project_id.onchange = function () {
        var fl_project_id = $('#fl_project_id').val()
        if(fl_project_id == 'all') {
            $('.tasks-wrp').show();
        } else {
            $('.tasks-wrp').hide();
            $('.task-row-'+fl_project_id).show();
        }
       
    }
    
    
    
  
    
Highcharts.chart('rmcontainer', {
    chart: {
        type: 'area'
    },navigation: {
                buttonOptions: {
                    enabled: false
                }
            },
    accessibility: {
        description: 'Image description: An area chart compares the nuclear stockpiles of the USA and the USSR/Russia between 1945 and 2017. The number of nuclear weapons is plotted on the Y-axis and the years on the X-axis. The chart is interactive, and the year-on-year stockpile levels can be traced for each country. The US has a stockpile of 6 nuclear weapons at the dawn of the nuclear age in 1945. This number has gradually increased to 369 by 1950 when the USSR enters the arms race with 6 weapons. At this point, the US starts to rapidly build its stockpile culminating in 32,040 warheads by 1966 compared to the USSR’s 7,089. From this peak in 1966, the US stockpile gradually decreases as the USSR’s stockpile expands. By 1978 the USSR has closed the nuclear gap at 25,393. The USSR stockpile continues to grow until it reaches a peak of 45,000 in 1986 compared to the US arsenal of 24,401. From 1986, the nuclear stockpiles of both countries start to fall. By 2000, the numbers have fallen to 10,577 and 21,000 for the US and Russia, respectively. The decreases continue until 2017 at which point the US holds 4,018 weapons compared to Russia’s 4,500.'
    },
    title: {
        text: ''
    },
    subtitle: {
        
    },
    xAxis: {
        allowDecimals: false,
        labels: {
            formatter: function () {
                return this.value; // clean, unformatted number for year
            }
        }
    },
    yAxis: {
        title: {
            text: ''
        },
        labels: {
            formatter: function () {
                return this.value / 1000 + 'k';
            }
        }
    },
    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },
    tooltip: {
        pointFormat: '{series.name} <b>{point.y:,.0f}</b><br/>'
    },
    plotOptions: {
        area: {
            marker: {
                enabled: false,
                symbol: 'circle',
                radius: 2,
                states: {
                    hover: {
                        enabled: true
                    }
                }
            }
        }
    },
    series: [{
    fillColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [
                    [0, Highcharts.getOptions().colors[0]],
                    [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                ]
            },
        name: 'Earnings',
        data: [<?php echo $paymentVar; ?>],
        color: '#2E3FD9'
    }, {
    fillColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [
                    [0, Highcharts.getOptions().colors[1]],
                    [1, Highcharts.color(Highcharts.getOptions().colors[1]).setOpacity(0).get('rgba')]
                ]
            },
        name: 'Expense',
        data: [<?php echo $expensesVar; ?>],
        color: '#B7B8BA'
    }]
});
Highcharts.chart('rwcontainer', {
    chart: {
        type: 'area'
    },navigation: {
                buttonOptions: {
                    enabled: false
                }
            },
    accessibility: {
        description: 'Image description: An area chart compares the nuclear stockpiles of the USA and the USSR/Russia between 1945 and 2017. The number of nuclear weapons is plotted on the Y-axis and the years on the X-axis. The chart is interactive, and the year-on-year stockpile levels can be traced for each country. The US has a stockpile of 6 nuclear weapons at the dawn of the nuclear age in 1945. This number has gradually increased to 369 by 1950 when the USSR enters the arms race with 6 weapons. At this point, the US starts to rapidly build its stockpile culminating in 32,040 warheads by 1966 compared to the USSR’s 7,089. From this peak in 1966, the US stockpile gradually decreases as the USSR’s stockpile expands. By 1978 the USSR has closed the nuclear gap at 25,393. The USSR stockpile continues to grow until it reaches a peak of 45,000 in 1986 compared to the US arsenal of 24,401. From 1986, the nuclear stockpiles of both countries start to fall. By 2000, the numbers have fallen to 10,577 and 21,000 for the US and Russia, respectively. The decreases continue until 2017 at which point the US holds 4,018 weapons compared to Russia’s 4,500.'
    },
    title: {
        text: ''
    },
    subtitle: {
        
    },
    xAxis: {
        allowDecimals: false,
        labels: {
            formatter: function () {
                return this.value; // clean, unformatted number for year
            }
        }
    },
    yAxis: {
        title: {
            text: ''
        },
        labels: {
            formatter: function () {
                return this.value / 1000 + 'k';
            }
        }
    },
    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },
    tooltip: {
        pointFormat: '{series.name} <b>{point.y:,.0f}</b><br/>'
    },
    plotOptions: {
        area: {
            marker: {
                enabled: false,
                symbol: 'circle',
                radius: 2,
                states: {
                    hover: {
                        enabled: true
                    }
                }
            }
        }
    },
    series: [{
    fillColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [
                    [0, Highcharts.getOptions().colors[0]],
                    [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                ]
            },
        name: 'Earnings',
        data: [<?php echo $paymentVar; ?>],
        color: '#2E3FD9'
    }, {
    fillColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [
                    [0, Highcharts.getOptions().colors[1]],
                    [1, Highcharts.color(Highcharts.getOptions().colors[1]).setOpacity(0).get('rgba')]
                ]
            },
        name: 'Expense',
        data: [<?php echo $expensesVar; ?>],
        color: '#B7B8BA'
    }]
});
Highcharts.chart('rdcontainer', {
    chart: {
        type: 'area'
    },navigation: {
                buttonOptions: {
                    enabled: false
                }
            },
    accessibility: {
        description: 'Image description: An area chart compares the nuclear stockpiles of the USA and the USSR/Russia between 1945 and 2017. The number of nuclear weapons is plotted on the Y-axis and the years on the X-axis. The chart is interactive, and the year-on-year stockpile levels can be traced for each country. The US has a stockpile of 6 nuclear weapons at the dawn of the nuclear age in 1945. This number has gradually increased to 369 by 1950 when the USSR enters the arms race with 6 weapons. At this point, the US starts to rapidly build its stockpile culminating in 32,040 warheads by 1966 compared to the USSR’s 7,089. From this peak in 1966, the US stockpile gradually decreases as the USSR’s stockpile expands. By 1978 the USSR has closed the nuclear gap at 25,393. The USSR stockpile continues to grow until it reaches a peak of 45,000 in 1986 compared to the US arsenal of 24,401. From 1986, the nuclear stockpiles of both countries start to fall. By 2000, the numbers have fallen to 10,577 and 21,000 for the US and Russia, respectively. The decreases continue until 2017 at which point the US holds 4,018 weapons compared to Russia’s 4,500.'
    },
    title: {
        text: ''
    },
    subtitle: {
        
    },
    xAxis: {
        allowDecimals: false,
        labels: {
            formatter: function () {
                return this.value; // clean, unformatted number for year
            }
        }
    },
    yAxis: {
        title: {
            text: ''
        },
        labels: {
            formatter: function () {
                return this.value / 1000 + 'k';
            }
        }
    },
    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },
    tooltip: {
        pointFormat: '{series.name} <b>{point.y:,.0f}</b><br/>'
    },
    plotOptions: {
        area: {
            marker: {
                enabled: false,
                symbol: 'circle',
                radius: 2,
                states: {
                    hover: {
                        enabled: true
                    }
                }
            }
        }
    },
    series: [{
    fillColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [
                    [0, Highcharts.getOptions().colors[0]],
                    [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                ]
            },
        name: 'Earnings',
        data: [<?php echo $paymentVar; ?>],
        color: '#2E3FD9'
    }, {
    fillColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [
                    [0, Highcharts.getOptions().colors[1]],
                    [1, Highcharts.color(Highcharts.getOptions().colors[1]).setOpacity(0).get('rgba')]
                ]
            },
        name: 'Expense',
        data: [<?php echo $expensesVar; ?>],
        color: '#B7B8BA'
    }]
});
Highcharts.chart('cpmcontainer', {
    chart: {
		height: 150,
		width: 110,
        type: 'variablepie'
    },navigation: {
                buttonOptions: {
                    enabled: false
                }
            },
    title: {
        text: ''
    },plotOptions: {
        variablepie: {
            borderWidth: 3,allowPointSelect: true,
        cursor: 'pointer',
            dataLabels: {
                enabled: false,
            }
        }
    },
    tooltip: {
        headerFormat: '',
        pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
            'Area (square km): <b>{point.y}</b><br/>' +
            'Population density (people per square km): <b>{point.z}</b><br/>'
    },
    series: [{
        minPointSize: 0,
        innerSize: '50%',
        zMin: 0,
        name: 'countries',
        data: [{
            name: 'On Time',
            y: {{$countsMonth->onTimeP}},
            z: 100,
            color:'#878787',
        }, {
            name: 'Delayed',
            y: {{$countsMonth->delayedP}},
            z: 100,
            color:'#CDCDCD',
        }, {
            name: 'On Hold',
            y: {{$countsMonth->onHoldP}},
            z: 100,
            color:'#3D4956'
        }]
    }]
});
Highcharts.chart('cpwcontainer', {
    chart: {
		height: 150,
		width: 110,
        type: 'variablepie'
    },navigation: {
                buttonOptions: {
                    enabled: false
                }
            },
    title: {
        text: ''
    },plotOptions: {
        variablepie: {
            borderWidth: 3,allowPointSelect: true,
        cursor: 'pointer',
            dataLabels: {
                enabled: false,
            }
        }
    },
    tooltip: {
        headerFormat: '',
        pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
            'Area (square km): <b>{point.y}</b><br/>' +
            'Population density (people per square km): <b>{point.z}</b><br/>'
    },
    series: [{
        minPointSize: 0,
        innerSize: '50%',
        zMin: 0,
        name: 'countries',
        data: [{
            name: 'On Time',
            y: {{$countsWeek->onTimeP}},
            z: 100,
            color:'#878787',
        }, {
            name: 'Delayed',
            y: {{$countsWeek->delayedP}},
            z: 100,
            color:'#CDCDCD',
        }, {
            name: 'On Hold',
            y: {{$countsWeek->onHoldP}},
            z: 100,
            color:'#3D4956'
        }]
    }]
});
Highcharts.chart('cpdcontainer', {
    chart: {
		height: 150,
		width: 110,
        type: 'variablepie'
    },navigation: {
                buttonOptions: {
                    enabled: false
                }
            },
    title: {
        text: ''
    },plotOptions: {
        variablepie: {
            borderWidth: 3,allowPointSelect: true,
        cursor: 'pointer',
            dataLabels: {
                enabled: false,
            }
        }
    },
    tooltip: {
        headerFormat: '',
        pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
            'Area (square km): <b>{point.y}</b><br/>' +
            'Population density (people per square km): <b>{point.z}</b><br/>'
    },
    series: [{
        minPointSize: 0,
        innerSize: '50%',
        zMin: 0,
        name: 'countries',
        data: [{
            name: 'On Time',
            y: {{$countsToday->onTimeP}},
            z: 100,
            color:'#878787',
        }, {
            name: 'Delayed',
            y: {{$countsToday->delayedP}},
            z: 100,
            color:'#CDCDCD',
        }, {
            name: 'On Hold',
            y: {{$countsToday->onHoldP}},
            z: 100,
            color:'#3D4956'
        }]
    }]
});
	
</script>
@endsection


@push('footer-script')
<script>
    
    
    $('#allTasks-table').on('click', '.show-task-detail', function () {
        $(".right-sidebar").slideDown(50).addClass("shw-rside");

        var id = $(this).data('task-id');
        var url = "{{ route('admin.all-tasks.show',':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                }
            }
        });
    })
    
    $('.task-timer-start-click').click(function () {
          var id = $(this).data('task-id');
          var project_id = $(this).data('project-id');
          var url = "{{route('admin.all-tasks.live-timeLog',':id')}}";
          url = url.replace(':id', id);
          var token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                type: "POST",
                data: {'_token': token, 'task_id' : id, 'project_id' : project_id},
                success: function (data) {
                    window.location.reload();
                }
            })
      });
      
    $('.task-timer-stop-click').click(function () {
          var id = $(this).data('task-id');
          var timeId = $(this).data('timelog-id');
          var url = "{{route('admin.all-tasks.live-timeLog-stop',':id')}}";
          url = url.replace(':id', id);
          var token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                type: "POST",
                data: {'_token': token, 'task_id' : id, 'timeId' : timeId},
                success: function (data) {
                    window.location.reload();
                }
            })
      });
      
    $(document).ready(function(e) {
        updateTimerTask();
    });
    
    function updateTimerTask() {
            var $worked = $("#active-timer-task");
            if ($worked.length){
                var myTime = $worked.html();
                var ss = myTime.split(":");

                var hours = ss[0];
                var mins = ss[1];
                var secs = ss[2];
                secs = parseInt(secs)+1;

                if(secs > 59){
                    secs = '00';
                    mins = parseInt(mins)+1;
                }

                if(mins > 59){
                    secs = '00';
                    mins = '00';
                    hours = parseInt(hours)+1;
                }

                if(hours.toString().length < 2) {
                    hours = '0'+hours;
                }
                if(mins.toString().length < 2) {
                    mins = '0'+mins;
                }
                if(secs.toString().length < 2) {
                    secs = '0'+secs;
                }
                var ts = hours+':'+mins+':'+secs;

                $worked.html(ts);
                setTimeout(updateTimerTask, 1000);
            }
        }
    
//    $(function () {
//        setInterval(timestamp, 10000);
//    });
    function timestamp() {
        var url = '{{ route('admin.dashboard.timestamp') }}';
        $.ajax({
            type: 'GET',
            url: url,
            success: function (response) {
                $('#cur-time').html(response);
            }
        });
    }
    var taskEvents = [
        @foreach($leaves as $leave)
        @if($leave->status == 'approved')
        {
            id: '{{ ucfirst($leave->id) }}',
            title: '{{ ucfirst($leave->user->name) }}',
            start: '{{ $leave->leave_date }}',
            end: '{{ $leave->leave_date }}',
            className: 'bg-{{ $leave->type->color }}'
        },
        @else
        {
            id: '{{ ucfirst($leave->id) }}',
            title: '<i class="fa fa-warning"></i> {{ ucfirst($leave->user->name) }}',
            start: '{{ $leave->leave_date }}',
            end: '{{ $leave->leave_date }}',
            className: 'bg-{{ $leave->type->color }}'
        },
        @endif
        @endforeach
    ];

    var getEventDetail = function (id) {
        var url = '{{ route('admin.leaves.show', ':id')}}';
        url = url.replace(':id', id);

        $('#modelHeading').html('Event');
        $.ajaxModal('#eventDetailModal', url);
    }

   

    $('.leave-action').click(function () {
        var action = $(this).data('leave-action');
        var leaveId = $(this).data('leave-id');
        var url = '{{ route("admin.leaves.leaveAction") }}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: { 'action': action, 'leaveId': leaveId, '_token': '{{ csrf_token() }}' },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        });
    })
    
    
    
    var taskEvents1 = [];
    var s_time = '';
    var s_time = '';
    let events = {!! $events !!};

    events.forEach(event => {
        let taskEvent1 = {
            id: event.id,
            title: event.event_name+' '+event.start_time+'-'+event.end_time,
            start:  event.start_date_time,
            end:  event.end_date_time,
            className: event.label_color,
            repeat: event.repeat,
            repeat_time: event.repeat_every,
            repeat_type: event.repeat_type,
            repeat_cycles: event.repeat_cycles
        };
        taskEvents1.push(taskEvent1);
    });

    
    var options = {
        dayRender: function( date, cell ) {
            // Get all events
            // var events = $('#calendar').fullCalendar('clientEvents').length ? $('#calendar').fullCalendar('clientEvents') : taskEvents1;
            var events = taskEvents1;
                // Start of a day timestamp
            var dateTimestamp = date.startOf('day');
            var recurringEvents = new Array();
            
            // find all events with monthly repeating flag, having id, repeating at that day few months ago  
            var dailyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'day' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'days', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });

            // find all events with monthly repeating flag, having id, repeating at that day few months ago  
            var weeklyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'week' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'weeks', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });

            // find all events with monthly repeating flag, having id, repeating at that day few months ago  
            var monthlyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'month' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'months', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });
            
            // find all events with monthly repeating flag, having id, repeating at that day few years ago  
            var yearlyEvents = events.filter(function (event) {
            return event.repeat === 'yes' && event.repeat_type === 'year' &&
                event.id &&
                moment(event.start).hour(0).minutes(0).diff(dateTimestamp, 'years', true) % event.repeat_time == 0
                && moment(event.start).startOf('day').isSameOrBefore(dateTimestamp);
            });
            recurringEvents = [ ...monthlyEvents, ...yearlyEvents, ...weeklyEvents, ...dailyEvents ];

            $.each(recurringEvents, function(key, event) {
                if (event.repeat_cycles !== null) {
                    if(event.repeat_cycles > 0) {
                        event.repeat_cycles--;
                    }else {
                        return false;
                    }
                }
                var timeStart = moment(event.start).utc();
                var timeEnd = moment(event.end).utc();
                var diff = timeEnd.diff(timeStart, 'days', true);

                // Refething event fields for event rendering 
                var eventData = {
                    id: event.id,
                    title: event.title,
                    start: date.hour(timeStart.hour()).minutes(timeStart.minutes()).format("YYYY-MM-DD HH:mm:ss"),
                    end: event.end && diff >= 1 ? date.clone().add(diff, 'days').hour(timeEnd.hour()).minutes(timeEnd.minutes()).format("YYYY-MM-DD HH:mm:ss") : date.hour(timeEnd.hour()).minutes(timeEnd.minutes()).format("YYYY-MM-DD HH:mm:ss"),
                    className: event.className,
                    repeat: event.repeat,
                    repeat_time: event.repeat_time,
                    repeat_type: event.repeat_type,
                    repeat_cycles: event.repeat_cycles
                };
                
                // Removing events to avoid duplication
                $('#calendar_events').fullCalendar( 'removeEvents', function (event) {
                    return eventData.id === event.id &&
                    moment(event.start).isSame(date, 'day');      
                });
                // Render event
                $('#calendar_events').fullCalendar('renderEvent', eventData, true);
            });
        }
    }
    
    var getEventDetail1 = function (id, duration) {
        var url = `{{ route('admin.events.show', ':id')}}?start=${duration.start.format('YYYY-MM-DD+HH:mm:ss')}&end=${duration.end.format('YYYY-MM-DD+HH:mm:ss')}`;

        url = url.replace(':id', id);

        $('#modelHeading').html('Event');
        $.ajaxModal('#eventDetailModal', url);
    }
    
    
    var calendarLocale = '{{ $global->locale }}';
    var firstDay = '{{ $global->week_start }}';
    
        $('#month-btn').click(function () {
            $(".c-perform select").val("Monthly").change();
        });
        $('#week-btn').click(function () {
            $(".c-perform select").val("Weekly").change();
        });
        $('#today-btn').click(function () {
            $(".c-perform select").val("Daily").change();
        });
    
	$(".c-perform select").change(function(){
		if($(".c-perform select").val()=="Monthly"){
                    $(".c-perform").addClass("month");$(".c-perform").removeClass("week");$(".c-perform").removeClass("daily");
                    $("#month-btn").click();
                };
		if($(".c-perform select").val()=="Weekly"){
                    $(".c-perform").addClass("week");$(".c-perform").removeClass("month");$(".c-perform").removeClass("daily");
                    $("#week-btn").click();
                };
		if($(".c-perform select").val()=="Daily"){
                    $(".c-perform").addClass("daily");$(".c-perform").removeClass("month");$(".c-perform").removeClass("week");
                    $("#today-btn").click();
                };
	});
        

        
        
        
    
</script>


<script src="{{ asset('plugins/bower_components/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/morrisjs/morris.js') }}"></script>

<script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>

<!-- jQuery for carousel -->
<script src="{{ asset('plugins/bower_components/owl.carousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/owl.carousel/owl.custom.js') }}"></script>

<!--weather icon -->
<script src="{{ asset('plugins/bower_components/skycons/skycons.js') }}"></script>

<script src="{{ asset('plugins/bower_components/calendar/jquery-ui.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('plugins/bower_components/calendar/dist/locale-all.js') }}"></script>
<script src="{{ asset('js/event-calendar.js') }}"></script>
<script src="{{ asset('js/event-calendar_d.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>

<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.js') }}"></script>


<script>
    
        
        
    jQuery('#start_date, #end_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    })

    $('#colorselector').colorselector();

    $('#start_time, #end_time').timepicker({
        @if($global->time_format == 'H:i')
        showMeridian: false
        @endif
    });

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    function addEventModal1(start, end, allDay){
        
        if(start){
            var sd = new Date(start);
            var curr_date = sd.getDate();
            if(curr_date < 10){
                curr_date = '0'+curr_date;
            }
            var curr_month = sd.getMonth();
            curr_month = curr_month+1;
            if(curr_month < 10){
                curr_month = '0'+curr_month;
            }
            var curr_year = sd.getFullYear();

            $('#start_date').val('{{ \Carbon\Carbon::now()->format($global->date_format) }}');

            var ed = new Date(start);
            var curr_date = sd.getDate();
            if(curr_date < 10){
                curr_date = '0'+curr_date;
            }
            var curr_month = sd.getMonth();
            curr_month = curr_month+1;
            if(curr_month < 10){
                curr_month = '0'+curr_month;
            }
            var curr_year = ed.getFullYear();
            $('#end_date').val('{{ \Carbon\Carbon::now()->format($global->date_format) }}');

            $('#start_date, #end_date').datepicker('destroy');
            jQuery('#start_date, #end_date').datepicker({
                autoclose: true,
                todayHighlight: true,
                weekStart:'{{ $global->week_start }}',
                format: '{{ $global->date_picker_format }}',
            })
        }

        $('#my-event').modal('show');

    }

    $('.save-event').click(function () {
        $.easyAjax({
            url: '{{route('admin.events.store')}}',
            container: '#createEvent',
            type: "POST",
            data: $('#createEvent').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })

    $('#repeat-event').change(function () {
        if($(this).is(':checked')){
            $('#repeat-fields').show();
        }
        else{
            $('#repeat-fields').hide();
        }
    })
    
$(function () {
    $('#attendance-div').click(function(){
                $('#attendanceModal').modal('show');
    });
});


    $('#clock-in').click(function () {
            var workingFrom = $('#working_from').val();

            var currentLatitude = document.getElementById("current-latitude").value;
            var currentLongitude = document.getElementById("current-longitude").value;

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                url: '{{route('admin.clockinout.store')}}',
                type: "POST",
                data: {
                    working_from: workingFrom,
                    currentLatitude: currentLatitude,
                    currentLongitude: currentLongitude,
                    _token: token
                },
                success: function (response) {
                    if(response.status == 'success'){
                        window.location.reload();
                    }
                }
            })
    })

    @if(!is_null($currenntClockIn))
        $('#clock-out').click(function () {

            var token = "{{ csrf_token() }}";
            var currentLatitude = document.getElementById("current-latitude").value;
            var currentLongitude = document.getElementById("current-longitude").value;

            $.easyAjax({
                url: '{{route('admin.clockinout.update', $currenntClockIn->id)}}',
                type: "PUT",
                data: {
                    currentLatitude: currentLatitude,
                    currentLongitude: currentLongitude,
                    _token: token
                },
                success: function (response) {
                    if(response.status == 'success'){
                        window.location.reload();
                    }
                }
            })
        })
    @endif


@if ($attendanceSettings->radius_check == 'yes')
<script>
    var currentLatitude = document.getElementById("current-latitude");
    var currentLongitude = document.getElementById("current-longitude");
    var x = document.getElementById("current-latitude");
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
           // x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        // x.innerHTML = "Latitude: " + position.coords.latitude +
        // "<br>Longitude: " + position.coords.longitude;

        currentLatitude.value = position.coords.latitude;
        currentLongitude.value = position.coords.longitude;
    }
    getLocation();
    
    
   
    
    
    
</script>
@endif

</script>

@endpush

