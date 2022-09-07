@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="border-bottom col-xs-12 p-t-10">
            <div class="col-md-12 hidden-xs hidden-sm">

                <select class="selectpicker language-switcher  pull-right" data-width="fit">
                    <option value="en" @if($user->locale == "en") selected @endif data-content='<span class="flag-icon flag-icon-gb"></span>'>En</option>
                    @foreach($languageSettings as $language)
                        <option value="{{ $language->language_code }}"
                                @if($user->locale == $language->language_code) selected
                                @endif  data-content='<span class="flag-icon @if($language->language_code == 'zh-CN') flag-icon-cn @elseif($language->language_code == 'zh-TW') flag-icon-tw @else flag-icon-{{ $language->language_code }} @endif"></span>'>{{ $language->language_code }}</option>
                    @endforeach
                </select>
                @if ($company_details->count() > 1)
                    <select class="selectpicker company-switcher" data-width="fit" name="companies" id="companies">
                        @foreach ($company_details as $company_detail)
                            <option {{ $company_detail->company->id === $global->id ? 'selected' : '' }} value="{{ $company_detail->company->id }}">{{ ucfirst($company_detail->company->company_name) }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<style>
    .col-in {
        padding: 0 20px !important;

    }

    .fc-event{
        font-size: 10px !important;
    }

    @media (min-width: 769px) {
        .panel-wrapper{
            height: 530px;
            overflow-y: auto;
        }
    }

</style>
@endpush

@section('content')
<div class="white-box">

    <div class="row dashboard-stats front-dashboard">
        @if(in_array('projects',$modules))
        <div class="col-md-4 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3 d-none">
                        <div>
                            <span class="bg-info-gradient"><i class="icon-layers"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-12">					
                        <span class="counter">{{ $counts->totalProjects }}</span>
                        <span class="widget-title"> @lang('modules.dashboard.totalProjects')</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('tickets',$modules))
<!--        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3">
                        <div>
                            <span class="bg-warning-gradient"><i class="ti-ticket"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-9 text-right">
                        <span class="widget-title"> @lang('modules.tickets.totalUnresolvedTickets')</span><br>
                        <span class="counter">{{ $counts->totalUnResolvedTickets }}</span>
                    </div>
                </div>
            </div>
        </div>-->
        @endif

        @if(in_array('invoices',$modules))
        <div class="col-md-4 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3 d-none">
                        <div>
                            <span class="bg-success-gradient"><i class="ti-ticket"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <span class="counter">{{ floor($counts->totalPaidAmount) }}</span>
                        <span class="widget-title"> @lang('modules.dashboard.totalPaidAmount')</span>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3 d-none">
                        <div>
                            <span class="bg-danger-gradient"><i class="ti-ticket"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <span class="widget-title"> @lang('modules.dashboard.totalOutstandingAmount')</span><br>
                        <span class="counter">{{ floor($counts->totalUnpaidAmount) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
    <!-- .row -->

    <div class="row" >

        @if(in_array('projects',$modules) && 1!=1)
        <div class="col-md-12" id="project-timeline">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang("modules.dashboard.projectActivityTimeline")</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body p-0 p-t-20">
                        <div class="steamline">
                            @foreach($projectActivities as $activity)
                                <div class="sl-item">
                                    <div class="sl-left"><i class="fa fa-circle text-info-"></i>
                                    </div>
                                    <div class="sl-right">
                                        <div><h6><a href="{{ route('client.projects.show', $activity->project_id) }}" class="text-danger">{{ ucwords($activity->project_name) }}:</a> {{ $activity->activity }}</h6> <span class="sl-date">{{ $activity->created_at->timezone($global->timezone)->diffForHumans() }}</span></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
