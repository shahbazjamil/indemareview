@extends('layouts.super-admin')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('super-admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/image-picker/image-picker.css') }}">
    <style>
        .thumbnail{
            color: black;
            font-weight: 600;
            text-align: center;
        }
        .thumbnail.selected{
            background-color: #f8c234 !important;
        }
        a{
            color:yellow;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel">

                <div class="vtabs customvtab p-t-10">
                    @if($global->front_design == 1)
                        @include('sections.front_setting_new_theme_menu')
                    @else
                        @include('sections.front_setting_menu')
                    @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info ">
                                    <h4 class="text-white">Favicon</h4>
                                    <i class="fa fa-info-circle" ></i> @lang('messages.faviconNote')
                                </div>

                            </div>
                            <div class="col-sm-12">
                                <div class="white-box">
                                    <h3 class="box-title m-b-10">@lang('app.selectTheme') </h3>
                                    {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'POST']) !!}
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="form-group" >
                                                <select name="theme" id="theme" class="image-picker show-labels show-html" style="color: white">
                                                    <option
                                                            data-img-src="{{ asset('img/old-design.jpg') }}"
                                                            @if($global->front_design == 0) selected @endif
                                                            value="0">
                                                        Theme 1
                                                    </option>

                                                    <option data-img-src="{{ asset('img/new-design.jpg') }}"
                                                            data-toggle="tooltip" data-original-title="Edit"
                                                            @if($global->front_design == 1) selected @endif
                                                            value="1">Theme 2
                                                    </option>

                                                </select>

                                            </div>
                                        </div>
                                        @if($global->front_design == 1 && !module_enabled('Subdomain'))
                                            <h3 class="box-title m-b-10">@lang('app.login') @lang('app.theme')</h3>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="form-group" >
                                                    <select name="login_ui" id="login_ui" class="image-picker show-labels show-html" style="color: white">
                                                        <option
                                                                data-img-src="{{ asset('img/old-login.jpg') }}"
                                                                @if($global->login_ui == 0) selected @endif
                                                                value="0">
                                                            Theme 1
                                                        </option>

                                                        <option data-img-src="{{ asset('img/new-login.jpg') }}"
                                                                data-toggle="tooltip" data-original-title="Edit"
                                                                @if($global->login_ui == 1) selected @endif
                                                                value="1">Theme 2
                                                        </option>

                                                    </select>

                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-sm-12">
                                        <button type="submit" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                                            @lang('app.update')
                                        </button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>    <!-- .row -->
                </div>

            </div>
        </div>


    </div>
{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="seoDetailModel" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
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
{{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
<script src="{{ asset('plugins/image-picker/image-picker.min.js') }}"></script>

<script>
    $("body").tooltip({
        selector: '[data-toggle="tooltip"]'
    });
    $(".image-picker").imagepicker({
        show_label: true
    });
    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('super-admin.theme-update')}}',
            container: '#editSettings',
            type: "POST",
            redirect: true,
            data: $('#editSettings').serialize()
        })
    });

</script>
@endpush
