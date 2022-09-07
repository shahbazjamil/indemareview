@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.project-template.index') }}">{{ __($pageTitle) }}</a></li>
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
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box p-0">
                        <nav>
                            <ul>
                                <li class="tab-current"><a href="{{ route('admin.project-template.show', $project->id) }}"><span>@lang('modules.projects.overview')</span></a>
                                </li>
                                @if(in_array('employees',$modules))
<!--                                <li><a href="{{ route('admin.project-template-member.show', $project->id) }}" class="btn-default"><span>@lang('modules.projects.members')</span></a></li>-->
                                @endif
                                <li><a href="{{ route('admin.project-template-milestone.show', $project->id) }}"><span>@lang('modules.projects.milestones')</span></a></li>

                                @if(in_array('tasks',$modules))
                                <li><a href="{{ route('admin.project-template-task.show', $project->id) }}"><span>@lang('app.menu.tasks')</span></a></li>
                                @endif

                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-1" class="show">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="white-box p-0">
                                        <h3 class="p-b-10 border-bottom">@lang('app.projectTemplate') #{{ $project->id }} - <span
                                                    class="font-bold">{{ ucwords($project->project_name) }}</span> </h3>
													<div class="border-bottom p-b-10"><a
                                                    href="{{ route('admin.project-template.edit', $project->id) }}" class="btn btn-info btn-outline btn-rounded" style="font-size: small"><i class="icon-note"></i> @lang('app.edit')</a></div>
                                        <div>{!!  $project->project_summary !!}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        {{-- project members --}}
                                        <div class="col-md-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">@lang('modules.projectTemplate.members')</div>
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body">
                                                        <div class="message-center">
                                                            @forelse($project->members as $member)
                                                            <a href="#">
                                                                <div class="user-img">
                                                                    {!!  '<img src="'.$member->user->image_url.'"
                                                            alt="user" class="img-circle" width="40" height="40">' !!}
                                                                </div>
                                                                <div class="mail-contnet">
                                                                    <h5>{{ ucwords($member->user->name) }}</h5>
                                                                    <span class="mail-desc">{{ $member->user->email }}</span>
                                                                </div>
                                                            </a>
                                                            @empty
                                                                @lang('messages.noMemberAddedToProject')
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div><!-- /content -->
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

</script>
@endpush
