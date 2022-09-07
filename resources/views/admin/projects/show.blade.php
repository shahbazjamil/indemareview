@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@section('page-title')
<div class="row bg-title d-flex">
    <!-- .page title -->
    <div class="border-bottom col-xs-6">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> @lang('app.project') - {{ ucwords($project->project_name) }}</h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="border-bottom col-xs-6 p-t-10 p-b-10 text-right">
    <!--<a href="{{ route('admin.payments.create', ['project' => $project->id]) }}" class="btn btn-sm btn-primary btn-outline" >+ @lang('modules.payments.addPayment')</a>-->

        @php
            if ($project->status == 'in progress') {
                $statusText = __('app.inProgress');
                $statusTextColor = 'text-info';
                $btnTextColor = 'btn-info';
            } else if ($project->status == 'on hold') {
                $statusText = __('app.onHold');
                $statusTextColor = 'text-warning';
                $btnTextColor = 'btn-warning';
            } else if ($project->status == 'not started') {
                $statusText = __('app.notStarted');
                $statusTextColor = 'text-warning';
                $btnTextColor = 'btn-warning';
            } else if ($project->status == 'canceled') {
                $statusText = __('app.canceled');
                $statusTextColor = 'text-danger';
                $btnTextColor = 'btn-danger';
            } else if ($project->status == 'finished') {
                $statusText = __('app.finished');
                $statusTextColor = 'text-success';
                $btnTextColor = 'btn-success';
            }
        @endphp

        <div class="btn-group dropdown p-status">
            <button aria-expanded="true" data-toggle="dropdown"
                    class="btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md p-t-0 p-b-0 text-center"
                    type="button">{{ $statusText }} <span style="width: 0px;min-width:0; height: 15px;"
                    class="btn {{ $btnTextColor }} btn-small btn-circle"><span class="caret"></span></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="in progress">@lang('app.inProgress')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-info btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="on hold">@lang('app.onHold')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-warning btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="not started">@lang('app.notStarted')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-warning btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="canceled">@lang('app.canceled')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-danger btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="finished">@lang('app.finished')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-success btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
            </ul>
        </div>

        <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-success btn-outline" ><i class="icon-note"></i> Edit Project</a>

        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
            <li class="active">@lang('app.details')</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection
@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/icheck/skins/all.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">

<style>
    #section-line-1 .col-in{
        padding:0 10px;
    }

    #section-line-1 .col-in h3{
        font-size: 15px;
    }

    #project-timeline .panel-body {
        max-height: 389px !important;
    }

    #milestones .panel-body {
        max-height: 189px;
        overflow: auto;
    }
    .panel-body{
        overflow-wrap:break-word;
    }
    .t-25 {
        margin-bottom: 25px !important;
    }
    .min-h-290{
        min-height: 290px !important;
    }
</style>
@endpush
@section('content')

<div class="row">
    <div class="col-md-12">

        <section>
            <div class="sttabs tabs-style-line">

                @include('admin.projects.show_project_menu')

                <div class="white-box p-0">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="panel project-top-stats p-b-10 d-none">
                                <div class="panel-heading m-b-15">Project Summary</div>
                                <div class="p-l-15 p-r-15 m-b-5" style="max-height: 200px; overflow-y: hidden;">
                                    <span class="text-primary-">
                                             {!! $project->project_summary !!}
                                    </span>
                                </div>
                                <div class="col-xs-12 text-center expand-div" style="position: relative; ">
                                                            <a href="javascript:;" class="btn p-t-15 text-info" data-toggle="modal" data-target="#project-summary-modal">&gt; </a>
                                </div>

                            </div>

                            <div class="row m-t-20 d-none">
                                <div class="col-md-12">
                                    <div class="panel ">
									<div class="panel-heading">@lang('app.project') @lang('app.details')</div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body b-all border-radius">
                                                <div class="row">
                                                    <div class="col-xs-12" style="max-height: 100px; overflow-y: hidden;">

                                                        {!! $project->project_summary !!}

                                                    </div>
                                                    <div class="col-xs-12 text-center">
                                                        <a href="javascript:;" class="btn p-t-15 text-info" data-toggle="modal" data-target="#project-summary-modal"><i class="ti-arrows-vertical"></i> @lang('app.expand')</a>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row m-t-10 t-25">
                                <div class="col-md-12">
                                    <div class="panel ">
									<div class="panel-heading">Details</div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body b-all border-radius">
                                            @if(!is_null($project->client))
                                                <div class="row">
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Client Name</label><br>
                                                        <p>
                                                            {{ ucwords($project->client->name) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Client Email</label><br>
                                                        <p>
                                                            {{ $project->client->email }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Project Address</label><br>
                                                        <p>
                                                            {{ $project->client->address }}
                                                        </p>
                                                    </div>
                                                </div>
                                            <div class="row">&nbsp;</div>
                                                <div class="row">
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Client Phone</label><br>
                                                        <p>
                                                            {{ $project->client->mobile }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Project Start Date</label><br>
                                                        <p>
                                                            {{ $project->start_date->format($global->date_format) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Project Completion</label><br>
                                                        <p>
                                                            {{ (!is_null($project->deadline) ? $project->deadline->format($global->date_format) : '-') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                                                        
                                    <?php if($project->clients) { 
                                        
                                        foreach ($project->clients as $client) {
                                        if ($client->client && $client->client->id != $project->client_id) {
                                        ?>            
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body b-all border-radius">
                                                <div class="row">
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Client Name</label><br>
                                                        <p>
                                                            {{ ucwords($client->client->name) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Client Email</label><br>
                                                        <p>
                                                            {{ $client->client->email }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Project Address</label><br>
                                                        <p>
                                                            {{ $client->client->address }}
                                                        </p>
                                                    </div>
                                                </div>
                                            <div class="row">&nbsp;</div>
                                                <div class="row">
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Client Phone</label><br>
                                                        <p>
                                                            {{ $client->client->mobile }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Project Start Date</label><br>
                                                        <p>
                                                            {{ $project->start_date->format($global->date_format) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <label class="font-semi-bold">Project Completion</label><br>
                                                        <p>
                                                            {{ (!is_null($project->deadline) ? $project->deadline->format($global->date_format) : '-') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }}} ?>
                                                     
                                                          
                                    </div>
                                </div>

                            </div>
                            <div class="row m-t-10">
                                <div class="col-md-12">
                                    <div class="panel ">
									<div class="panel-heading">PROJECT FINANCIALS</div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body b-all border-radius project-financials min-h-290">
                                                <div class="row">
													<div class="col-md-12 d-none">														
														<div class="progress">
														  <div class="progress-bar progress-bar-primary" style="width: {{$project_budget_per}}%">
															<span class="sr-only">{{$project_budget_per}}%</span>
														  </div>
														  <div class="progress-bar progress-bar-warning" style="width: {{$invoices_total_per}}%">
															<span class="sr-only">{{$invoices_total_per}}%</span>
														  </div>
														  <div class="progress-bar progress-bar-danger" style="width: {{$current_profit_per}}%">
															<span class="sr-only">{{$current_profit_per}}%</span>
														  </div>
														</div>
													</div><!--end of col-12-->
													<div class="col-md-4">
														@if(!is_null($project_budget))
                                                                                                                {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$project_budget : $project_budget }}
                                                                                                                @else
                                                                                                                --
                                                                                                                @endif <span class="d-block">Project Budget</span>
													</div><!--end of col-3-->
													<div class="col-md-4">
														@if(!is_null($invoices_total))
                                                                                                                {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$invoices_total : $invoices_total }}
                                                                                                                @else
                                                                                                                --
                                                                                                                @endif <span class="d-block">Total Invoiced</span>
													</div><!--end of col-3-->
													<div class="col-md-4">
														 @if(!is_null($current_profit))
                                                                                                                {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$current_profit : $current_profit }}
                                                                                                                @else
                                                                                                                --
                                                                                                                @endif <span class="d-block">Current Profit</span>
													</div><!--end of col-3-->
													<div class="col-md-6">
														Product Revenue
													</div><!--end of col-6-->
													<div class="col-md-6 text-right">
                                                                                                            @if(!is_null($all_products_unit_cost))
                                                                                                                {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$all_products_unit_cost : $all_products_unit_cost }}
                                                                                                            @else
                                                                                                                --
                                                                                                            @endif
													</div><!--end of col-6-->
													<div class="col-md-6">
														Expenses
													</div><!--end of col-6-->
													<div class="col-md-6 text-right">
														{{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$expenses : $expenses }}
													</div><!--end of col-6-->
													<div class="col-md-6">
														Budget Remaining
													</div><!--end of col-6-->
													<div class="col-md-6 text-right">
                                                                                                        @if(!is_null($remaining_budget))
                                                                                                            {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$remaining_budget : $remaining_budget }}
                                                                                                        @else
                                                                                                                --
                                                                                                        @endif
													</div><!--end of col-6-->
													<div class="col-md-6">
														Time Billing
													</div><!--end of col-6-->
													<div class="col-md-6 text-right">
                                                                                                            @if(!is_null($logged_hours_value))
                                                                                                                {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$logged_hours_value : $logged_hours_value }}
                                                                                                            @else
                                                                                                                --
                                                                                                            @endif
													</div><!--end of col-6-->
													<div class="col-md-6">
														Total in purchase orders
													</div><!--end of col-6-->
													<div class="col-md-6 text-right">
                                                                                                            @if(!is_null($total_purchase_order))
                                                                                                                {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$total_purchase_order : $total_purchase_order }}
                                                                                                            @else
                                                                                                                --
                                                                                                            @endif
													</div><!--end of col-6-->
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Project Budget:</label>
                                                        <p>
                                                        @if(!is_null($project_budget))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$project_budget : $project_budget }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Current Profit:</label>
                                                        <p>
                                                        @if(!is_null($current_profit))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$current_profit : $current_profit }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Current Budget:</label>
                                                        <p>
                                                        @if(!is_null($current_budget))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$current_budget : $current_budget }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        &nbsp;
                                                        <p>
                                                            &nbsp;
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Budget Remaining:</label>
                                                        <p>
                                                        @if(!is_null($remaining_budget))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$remaining_budget : $remaining_budget }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Total Shipping:</label>
                                                        <p>
                                                        @if(!is_null($all_products_shipping))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$all_products_shipping : $all_products_shipping }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Total Products (Your Cost):</label>
                                                        <p>
                                                        @if(!is_null($all_products_unit_cost))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$all_products_unit_cost : $all_products_unit_cost }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Total in Markups:</label>
                                                        <p>
                                                        @if(!is_null($all_products_markup))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$all_products_markup : $all_products_markup }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    
                                                    
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Total Products (Sale):</label>
                                                        <p>
                                                        @if(!is_null($all_products_sale_cost))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$all_products_sale_cost : $all_products_sale_cost }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Total Invoiced:</label>
                                                        <p>
                                                        @if(!is_null($invoices_total))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$invoices_total : $invoices_total }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Current Project Hours:</label>
                                                        <p>
                                                            {{ $logged_hours }}
                                                        </p>
                                                    </div>
                                                     <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Total Past Due:</label>
                                                        <p>
                                                        @if(!is_null($invoices_total_due))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$invoices_total_due : $invoices_total_due }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Value of Project Hours:</label>
                                                        <p>
                                                        @if(!is_null($logged_hours_value))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$logged_hours_value : $logged_hours_value }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6 d-none">
                                                        <label class="font-semi-bold">Total Invoice Paid:</label>
                                                        <p>
                                                        @if(!is_null($invoices_total_paid))
                                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$invoices_total_paid : $invoices_total_paid }}
                                                        @else
                                                        --
                                                        @endif
                                                        </p>
                                                    </div>
                                                    <div style="display: none;" class="col-xs-6">
                                                        <label class="font-semi-bold">Earnings:</label>
                                                        <p>
                                                            {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$earnings : $earnings }}
                                                        </p>
                                                    </div>

                                                    <div class="col-xs-6" style="display: none;">
                                                        <label class="font-semi-bold">Hours Logged:</label>
                                                        <p>
                                                            {{ $hoursLogged }}
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-6" style="display: none;">
                                                        <label class="font-semi-bold">Expenses:</label>
                                                        <p>
                                                            {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$expenses : $expenses }}
                                                        </p>
                                                    </div>
                                                  
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
</div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">

                                <div class="col-md-12 d-none">
                                    <div class="panel">
									<div class="panel-heading">Members</div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                @forelse($project->members as $member)
                                                    <img src="{{ asset($member->user->image_url) }}"
                                                    data-toggle="tooltip" data-original-title="{{ ucwords($member->user->name) }}"

                                                    alt="user" class="img-circle" width="25" height="25" height="25">
                                                @empty
                                                    @lang('messages.noMemberAddedToProject')
                                                @endforelse

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-none">
                                    <div class="panel">
									<div class="panel-heading">UPCOMING DUE TASKS</div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body upcoming-tasks">
                                                <table class="table table-bordered table-hover toggle-circle default footable-loaded footable dataTable no-footer dtr-inline">
													<thead>
														<tr>
															<th>#</th>
															<th>TASK</th>
															<th>ASSIGNED TO</th>
															<th>DUE DATE</th>
															<th>STATUS</th>
															<th>GO</th>
														</tr>
													</thead>
													<tbody>
                                                                                                            @if(!is_null($tasks))
                                                                                                                @foreach($tasks as $task)
                                                                                                                <?php
                                                                                                                    $members_str = '';
                                                                                                                
                                                                                                                    if(isset($task->users) && !is_null($task->users)){
                                                                                                                        foreach ($task->users as $member) {
                                                                                                                            if($members_str == '') {
                                                                                                                                $members_str .= ucwords($member->name);
                                                                                                                            } else {
                                                                                                                                $members_str .= ','.ucwords($member->name);
                                                                                                                            }

                                                                                                                        }
                                                                                                                    }
                                                                                                                    
                                                                                                                    ?>

                                                                                                                    <tr>
                                                                                                                        <td>{{$loop->index}}</td>
                                                                                                                        <td>{{ ucfirst($task->heading )}}</td>
                                                                                                                        <td>{{$members_str}}</td>
                                                                                                                        <td>{{ (!is_null($task->due_date) ? $task->due_date->format($global->date_format) : '-') }}</td>
                                                                                                                        <td>{{  (!is_null($task->board_column) ? $task->board_column->column_name : '-')  }}</td>
                                                                                                                        <td><a href="{{route('admin.all-tasks.edit', $task->id)}}" class="go-lnk">&gt;</a></td>
                                                                                                                    </tr>

                                                                                                                    @endforeach
                                                                                                            @else 
                                                                                                            <tr><td colspan="6">No Due task(s)</td></tr>
                                                                                                            @endif
													    
													</tbody>
												</table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-12 d-none">
									<div class="panel b-all border-radius" id="milestones">
                                        <div class="panel-heading b-b">@lang('modules.projects.milestones')</div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                @forelse ($milestones as $key=>$item)
                                                <div class="row m-b-10">
                                                    <div class="col-xs-12 m-b-5">
                                                        <a href="javascript:;" class="milestone-detail" data-milestone-id="{{ $item->id }}">
                                                            <h6>{{ ucfirst($item->milestone_title )}}</h6>
                                                        </a>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        @if($item->status == 'complete')
                                                            <label class="label label-success">@lang('app.complete')</label>
                                                        @else
                                                            <label class="label label-danger">@lang('app.incomplete')</label>
                                                        @endif
                                                    </div>
                                                    <div class="col-xs-6 text-right">
                                                        @if($item->cost > 0)
                                                            {{ $item->currency->currency_symbol.$item->cost
                                                        }}
                                                        @endif
                                                    </div>
                                                </div>
                                                @empty
                                                    @lang('messages.noRecordFound')
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
								</div>
                                <div class="col-md-12 sttabs  m-t-10" id="project-timeline">
                                    <div class="panel border-radius">
                                        <div class="panel-heading b-b">Projects Notes</div>

                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <div class="steamline">
                                                    <div class="row">


                                                        <div class="col-md-12">
                                                            <div class="white-box p-0">
                                                                <h3 class="box-title b-b"><i class="fa fa-layers"></i> @lang('app.notes')</h3>
                                                                    <div class="col-xs-12" id="note-container">
                                                                        <div id="note-list">
                                                                            @forelse($projectNotes as $note)
                                                                                <div class="row b-b m-b-5 font-12">
                                                                                <div class="col-xs-12 m-b-5">
                                                                                    <span class="font-semi-bold">{{ ucwords($note->user->name) }}</span> <span class="text-muted font-12">{{ ucfirst($note->created_at->diffForHumans()) }}</span>
                                                                                </div>
                                                                                <div class="col-xs-10">
                                                                                    {!! ucfirst($note->note)  !!}
                                                                                </div>
                                                                                <div class="col-xs-2 text-right">
                                                                                    <a href="javascript:;" data-comment-id="{{ $note->id }}" class="btn btn-xs  btn-outline btn-default" onclick="deleteNote('{{ $note->id }}');return false;"><i class="fa fa-trash"></i> @lang('app.delete')</a>
                                                                                </div>
                                                                            </div>
                                                                            @empty
                                                                                <div class="col-xs-12">
                                                                                    @lang('messages.noNoteFound')
                                                                                </div>
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group" id="note-box">
                                                                        <div class="col-xs-12 m-t-10">
                                                                            <textarea name="note" id="task-note" class="summernote" placeholder="@lang('app.notes')"></textarea>
                                                                        </div>
                                                                        <div class="col-xs-12">
                                                                            <a href="javascript:;" id="submit-note" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</a>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel border-radius performance-metrics">
                                        <div class="panel-heading b-b">Performance Metrics</div>

                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body b-all border-radius min-h-290">
                                                <div class="steamline">
                                                    <div class="row">


                                                        <div class="col-md-12">
                                                            <div class="white-box p-0 row">
															
																<div class="col-md-12">														
																	<div class="progress">
																	  <div class="progress-bar progress-bar-primary" style="max-width: 100%; width: {{$hours_used_per}}%">
																		<span class="sr-only">Total Used: {{$total_hours_used}}({{$hours_used_per}}%)</span>
																	  </div>
																	  <div class="progress-bar progress-bar-danger" style="max-width: 100%; width: {{$total_hours_allocated_per}}%">
																		<span class="sr-only">Allocated: {{$total_hours_allocated}}({{$total_hours_allocated_per}}%)</span>
																	  </div>
<!--																	  <div class="progress-bar progress-bar-warning" style="width: {{$hours_left_per}}%">
																		<span class="sr-only">Remaining:{{$hours_left}}({{$hours_left_per}}%)</span>
																	  </div>-->
																	</div>
																</div><!--end of col-12-->
																<div class="col-md-6">Total hours allocated for project</div>
																<div class="col-md-6 text-right"><span class="text-left">{{$total_hours_allocated}}</span></div>
																<div class="col-md-6">Total hours used already</div>
																<div class="col-md-6 text-right"><span class="text-left">{{$total_hours_used}}</span></div>
																<div class="col-md-6">Hours left before threshold reached</div>
																<div class="col-md-6 text-right"><span class="text-left">{{$hours_left}}</span></div>
																<div class="col-md-6"># of hours over allocated</div>
																<div class="col-md-6 text-right"><span class="text-left">{{$hours_over_allocated}}</span></div>
																<div class="col-md-6">% of hours used based on anticipated hours</div>
																<div class="col-md-6 text-right"><span class="text-left">{{$hours_used_per}}%</span></div>
																<div class="col-md-6">Current value of billable hours</div>
																<div class="col-md-6 text-right"><span class="text-left">{{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$billable_hours_value : $billable_hours_value }}</span></div>
                                                                                                                                
															</div>

                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-none" id="project-timeline">
                                    <div class="panel b-all border-radius">
                                        <div class="panel-heading b-b">Activity</div>

                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <div class="steamline">
                                                    @foreach($activities as $activ)
                                                    <div class="sl-item">
                                                        <div class="sl-left"><i class="fa fa-circle text-primary-"></i>
                                                        </div>
                                                        <div class="sl-right">
                                                            <div>
                                                                <h6>{{ $activ->activity }}</h6> <span class="sl-date">{{ $activ->created_at->diffForHumans() }}</span></div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

<!--                    <div class="row d-none">
                        @if(in_array('tasks',$modules))
                            <div class="col-md-4">
                                <div class="panel b-all border-radius">
                                    <div class="panel-heading b-b">@lang('app.menu.tasks')</div>
                                    <div class="panel-wrapper collapse in">
                                        <div class="panel-body">
                                            @if(!empty($taskStatus))
                                                <canvas id="chart3" height="150"></canvas>
                                            @else
                                                @lang('messages.noRecordFound')
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="panel b-all border-radius">
                                <div class="panel-heading b-b">@lang('app.earnings')</div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        @if($chartData != '[]')
                                            <div id="morris-bar-chart" style="height: 191px;"></div>
                                        @else
                                            @lang('messages.noRecordFound')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="panel b-all border-radius">
                                <div class="panel-heading b-b">@lang('app.menu.timeLogs')</div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        @if($timechartData != '[]')
                                            <div id="morris-bar-timelogbarChart" style="height: 191px;"></div>
                                        @else
                                            @lang('messages.noRecordFound')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->

                </div>
                <!-- /content -->
            </div>
            <!-- /tabs -->
        </section>
    </div>


</div>
<!-- .row -->

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
    <!-- /.modal-dialog -->.
</div>
{{--Ajax Modal Ends--}}

{{--Ajax Modal--}}
<div class="modal fade bs-modal-lg in" id="project-summary-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><i class="icon-layers"></i> @lang('modules.projects.projectSummary')</h4>
            </div>
            <div class="modal-body">
                {!! $project->project_summary !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>
{{--Ajax Modal Ends--}}


@endsection
 @push('footer-script')
 <script src="{{ asset('plugins/bower_components/Chart.js/Chart.min.js') }}"></script>

 <script src="{{ asset('plugins/bower_components/raphael/raphael-min.js') }}"></script>
 <script src="{{ asset('plugins/bower_components/morrisjs/morris.js') }}"></script>

 <script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">

    function pieChart(taskStatus) {
        var ctx3 = document.getElementById("chart3").getContext("2d");
        var data3 = new Array();
        $.each(taskStatus, function(key,val){
            // console.log("key : "+key+" ; value : "+val);
            data3.push(
                {
                    value: parseInt(val.count),
                    color: val.color,
                    highlight: "#57ecc8",
                    label: val.label
                }
            );
        });

        // console.log(data3);

        var myPieChart = new Chart(ctx3).Pie(data3,{
            segmentShowStroke : true,
            segmentStrokeColor : "#fff",
            segmentStrokeWidth : 0,
            animationSteps : 100,
            tooltipCornerRadius: 0,
            animationEasing : "easeOutBounce",
            animateRotate : true,
            animateScale : false,
            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
            responsive: true
        });
    }

    @if(!empty($taskStatus))
        pieChart(jQuery.parseJSON('{!! $taskStatus !!}'));
    @endif

    var chartData = {!!  $chartData !!};
    function barChart() {

        Morris.Bar({
            element: 'morris-bar-chart',
            data: chartData,
            xkey: 'date',
            ykeys: ['total'],
            labels: ['Earning'],
            barColors:['#00c292'],
            hideHover: 'auto',
            gridLineColor: '#eef0f2',
            resize: true
        });

    }

    @if($chartData != '[]')
    barChart();
    @endif

    var chartData = {!!  $timechartData !!};
    function timelogbarChart() {

        Morris.Bar({
            element: 'morris-bar-timelogbarChart',
            data: chartData,
            xkey: 'date',
            ykeys: ['total_hours'],
            labels: ['Hours Logged'],
            barColors:['#3594fa'],
            hideHover: 'auto',
            gridLineColor: '#ccccccc',
            resize: true
        });

    }

    @if($timechartData != '[]')
    timelogbarChart();
    @endif
</script>

<script type="text/javascript">

    $('#timer-list').on('click', '.stop-timer', function () {
       var id = $(this).data('time-id');
        var url = '{{route('admin.time-logs.stopTimer', ':id')}}';
        url = url.replace(':id', id);
        var token = '{{ csrf_token() }}'
        $.easyAjax({
            url: url,
            type: "POST",
            data: {timeId: id, _token: token},
            success: function (data) {
                $('#timer-list').html(data.html);
            }
        })

    });

    $('.milestone-detail').click(function(){
        var id = $(this).data('milestone-id');
        var url = '{{ route('admin.milestones.detail', ":id")}}';
        url = url.replace(':id', id);
        $('#modelHeading').html('@lang('app.update') @lang('modules.projects.milestones')');
        $.ajaxModal('#projectCategoryModal',url);
    })

    $('.submit-ticket').click(function () {

        const status = $(this).data('status');
        const url = '{{route('admin.projects.updateStatus', $project->id)}}';
        const token = '{{ csrf_token() }}'

        $.easyAjax({
            url: url,
            type: "POST",
            data: {status: status, _token: token},
            success: function (data) {
                window.location.reload();
            }
        })
    });
    $('ul.showProjectTabs .projects').addClass('tab-current');
</script>

<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
        
    
        
    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,                 // set focus to editable area after initializing summernote,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen", "codeview"]]
        ]
    });
        
    function deleteNote (id) {

        var url = '{{ route("admin.project-note.destroy", ':id') }}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': '{{ csrf_token() }}', '_method': 'DELETE'},
            success: function (response) {
                if (response.status == "success") {
                    $('#note-list').html(response.view);
                }
            }
        })
    }
        
    $('#submit-note').click(function () {
        var note = $('#task-note').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: '{{ route("admin.project-note.store") }}',
            type: "POST",
            data: {'_token': token, note: note, projectId: '{{ $project->id }}'},
            success: function (response) {
                if (response.status == "success") {
                    $('#note-list').html(response.view);
                    $('.summernote').summernote("reset");
                    $('.note-editable').html('');
                    $('#task-note').val('');
                }
            }
        })
    })
        
    </script>

@endpush
