@extends('layouts.app')

@section('page-title')
    <div class="row bg-title p-b-0">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.designations.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">@lang('app.add') @lang('app.menu.designation')</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.team_settings_menu')
                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-sm12 col-xs-12 b-t p-t-20">
                                            
                                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                                <div class="panel-body p-0">
                                                    <div class="row">
                                                        <div class="col-sm-12 col-xs-12">
                                                            {!! Form::open(['id'=>'createCurrency','class'=>'ajax-form','method'=>'POST']) !!}
                                                            <div class="form-group">
                                                                <label for="company_name">@lang('app.menu.designation')</label>
                                                                <input type="text" class="form-control" id="designation_name" name="designation_name">
                                                            </div>


                                                            <button type="submit" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                                                                @lang('app.save')
                                                            </button>
                                                            <a href="{{ route('admin.designations.index') }}" class="btn btn-default waves-effect waves-light">@lang('app.back')</a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
    <script>
        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.designations.store')}}',
                container: '#createCurrency',
                type: "POST",
                redirect: true,
                data: $('#createCurrency').serialize()
            })
        });
    </script>
@endpush

