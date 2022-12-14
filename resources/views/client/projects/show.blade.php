@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="border-bottom col-xs-12 p-t-10 p-b-10">
            @php
            if ($project->status == 'in progress') {
                $statusText = __('app.inProgress');
                $statusTextColor = 'text-info';
                $btnTextColor = 'label-info';
            } else if ($project->status == 'on hold') {
                $statusText = __('app.onHold');
                $statusTextColor = 'text-warning';
                $btnTextColor = 'label-warning';
            } else if ($project->status == 'not started') {
                $statusText = __('app.notStarted');
                $statusTextColor = 'text-warning';
                $btnTextColor = 'label-warning';
            } else if ($project->status == 'canceled') {
                $statusText = __('app.canceled');
                $statusTextColor = 'text-danger';
                $btnTextColor = 'label-danger';
            } else if ($project->status == 'finished') {
                $statusText = __('app.finished');
                $statusTextColor = 'text-success';
                $btnTextColor = 'label-success';
            }
            @endphp

            <label class="label {{ $btnTextColor }}">{{ $statusText }}</label>

            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('client.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.project')</li>
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
<style>
    #section-line-1 .col-in{
        padding:0 10px;
    }

    #section-line-1 .col-in h3{
        font-size: 15px;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line client-ptabs">
                    <div class="white-box p-0">
                        <nav>
                            <ul class="showProjectTabs">
                                <li class="tab-current"><a href="{{ route('client.projects.show', $project->id) }}"><span>@lang('modules.projects.overview')</span></a></li>

                                @if(in_array('employees',$modules))
                                <li><a href="{{ route('client.project-members.show', $project->id) }}"><span>@lang('modules.projects.members')</span></a></li>
                                @endif

                                @if(in_array('tasks',$modules))
                                    <li><a href="{{ route('client.tasks.edit', $project->id) }}"><span>@lang('app.menu.tasks')</span></a></li>
                                @endif

                                <li><a href="{{ route('client.files.show', $project->id) }}"><span>@lang('modules.projects.files')</span></a></li>
                                @if(in_array('timelogs',$modules))
<!--                                <li><a href="{{ route('client.time-log.show', $project->id) }}"><span>@lang('app.menu.timeLogs')</span></a></li>-->
                                @endif
                                
                                <li ><a href="{{ route('client.product-review-project.show', $project->id) }}"><span>Product Review</span></a></li>

                                @if(in_array('invoices',$modules))
                                <li><a href="{{ route('client.project-invoice.show', $project->id) }}"><span>@lang('app.menu.invoices')</span></a></li>
                                @endif

                                <li>
                                    <a href="{{ route('client.projects.discussion', $project->id) }}"></i>
                                        <span>@lang('modules.projects.discussion')</span></a>
                                </li>
                                
                            </ul>
                        </nav>
                    </div>


                    <div class="white-box p-0">
                        <div class="row">

                            <div class="col-md-6">


                                <div class="row">
                                     @if(1!=1)
								<div class="col-md-12">
                                        <div class="panel panel-inverse">
											<div class="panel-heading">PROJECT SUMMERY</div>  
                                            <div class="panel-wrapper collapse in">
                                                <div class="panel-body dashboard-stats">
                                                   <div class="row">
                                                       <div class="col-md-12 m-b-5 project-stats">
                                                            @lang('modules.projects.openTasks'): <span class="text-danger-">{{ count($openTasks) }}</span> 
                                                       </div>
                                                       <div class="col-md-12 m-b-5 project-stats">
                                                            @lang('modules.projects.daysLeft'):<span class="text-info-">{{ $daysLeft }}</span>
                                                       </div>
                                                      
                                                       <div  class="col-md-12 m-b-5 project-stats">
                                                            @lang('modules.projects.hoursLogged'): <span class="text-success-">{{ $hoursLogged }}</span>
                                                       </div>
                                                       
                                                   </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                      @endif
                                    <div class="col-md-12" style="max-height: 400px; overflow-y: auto;">
										<div class="panel panel-inverse">
											<div class="panel-heading">@lang('app.project') @lang('app.details') </div>                                        
											<div class="panel-body">{!! $project->project_summary !!}</div>
										</div>
                                    </div>
                                      
                                      
                                </div>

                                <div class="row">
                                    
                                    
                                    
                                    
                                    @if(1!=1)

                                    @if(in_array('timelogs',$modules))
                                    <div class="col-md-12">
                                        <div class="panel panel-inverse">
                                            <div class="panel-heading">@lang('modules.projects.activeTimers')</div>
                                            <div class="panel-wrapper collapse in">
                                                <div class="panel-body" id="timer-list">

                                                    @forelse($activeTimers as $key=>$time)
                                                    <div class="row m-b-10">
                                                        <div class="col-xs-12 m-b-5">
                                                            {{ ucwords($time->user->name) }}
                                                        </div>
                                                        <div class="col-xs-12 font-12">
                                                            {{ $time->duration }}
                                                        </div>

                                                    </div>

                                                    @empty
                                                        @lang('messages.noActiveTimer')
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif


                                </div>

                            </div>
                            
                            <div class="col-md-6" >
                                
                                 <div class="row">
                                <div class="col-md-12">
                                        <div class="panel panel-inverse">
                                            <div class="panel-heading">@lang('modules.client.clientDetails') </div>
                                            <div class="panel-wrapper collapse in">
                                                <div class="panel-body">
                                                    @if(!is_null($userDetail->client))
                                                    <dl>
                                                        @if(!is_null($userDetail->client[0]->company_name))
                                                        <dt>@lang('modules.client.companyName')</dt>
                                                        <dd class="m-b-10">{{ $userDetail->client[0]->company_name }}</dd>
                                                        @endif
                                                        <dt>@lang('modules.client.clientName')</dt>
                                                        <dd class="m-b-10">{{ ucwords($userDetail->client[0]->name) }}</dd>

                                                        <dt>@lang('modules.client.clientEmail')</dt>
                                                        <dd class="m-b-10">{{ $userDetail->client[0]->email }}</dd>
                                                    </dl>
                                                    @else @lang('messages.noClientAddedToProject') @endif {{--Custom fields data--}} @if(isset($fields))
                                                    <dl>
                                                        @foreach($fields as $field)
                                                        <dt>{{ ucfirst($field->label) }}</dt>
                                                        <dd class="m-b-10">
                                                            @if( $field->type == 'text') {{$project->custom_fields_data['field_'.$field->id] ?? '-'}} @elseif($field->type == 'password')
                                                            {{$project->custom_fields_data['field_'.$field->id] ?? '-'}}
                                                            @elseif($field->type == 'number') {{$project->custom_fields_data['field_'.$field->id]
                                                            ?? '-'}} @elseif($field->type == 'textarea') {{$project->custom_fields_data['field_'.$field->id]
                                                            ?? '-'}} @elseif($field->type == 'radio') {{ !is_null($project->custom_fields_data['field_'.$field->id])
                                                            ? $project->custom_fields_data['field_'.$field->id] : '-' }}
                                                            @elseif($field->type == 'select') {{ (!is_null($project->custom_fields_data['field_'.$field->id])
                                                            && $project->custom_fields_data['field_'.$field->id] != '') ?
                                                            $field->values[$project->custom_fields_data['field_'.$field->id]]
                                                            : '-' }} @elseif($field->type == 'checkbox') {{ !is_null($project->custom_fields_data['field_'.$field->id])
                                                            ? $field->values[$project->custom_fields_data['field_'.$field->id]]
                                                            : '-' }} @elseif($field->type == 'date')
                                                                {{ \Carbon\Carbon::parse($project->custom_fields_data['field_'.$field->id])->format($global->date_format)}}
                                                            @endif
                                                        </dd>
                                                        @endforeach
                                                    </dl>
                                                    @endif {{--custom fields data end--}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                
                                
                                
                            </div>

                             @if(1!=1)
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 d-none">
                                        <div class="panel panel-inverse">
                                            <div class="panel-heading">@lang('modules.projects.members')
                                                <span class="label label-rouded label-custom pull-right">{{ count($project->members) }}</span>
                                            </div>
                                            <div class="panel-wrapper collapse in">
                                                <div class="panel-body">
                                                    @forelse($project->members as $member)
                                                        <img src="{{ asset($member->user->image_url) }}"
                                                        data-toggle="tooltip" data-original-title="{{ ucwords($member->user->name) }}"

                                                        alt="user" class="img-circle" width="25" height="25" height="25" height="25">
                                                    @empty
                                                        @lang('messages.noMemberAddedToProject')
                                                    @endforelse

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="col-md-12"   id="project-timeline">
                                        <div class="panel panel-inverse">
                                            <div class="panel-heading">@lang('modules.projects.activityTimeline')</div>

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
                             @endif

                        </div>

                    </div>


                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
//    (function () {
//
//        [].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
//            new CBPFWTabs(el);
//        });
//
//    })();

    // $('#timer-list').on('click', '.stop-timer', function () {
    //    var id = $(this).data('time-id');
    //     var url = '{{route('admin.time-logs.stopTimer', ':id')}}';
    //     url = url.replace(':id', id);
    //     var token = '{{ csrf_token() }}'
    //     $.easyAjax({
    //         url: url,
    //         type: "POST",
    //         data: {timeId: id, _token: token},
    //         success: function (data) {
    //             $('#timer-list').html(data.html);
    //         }
    //     })

    // });
    $('ul.showProjectTabs .projects').addClass('tab-current');

</script>
@endpush
