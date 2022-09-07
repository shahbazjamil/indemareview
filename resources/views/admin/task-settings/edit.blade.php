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
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">{{ __($pageTitle) }}</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.admin_setting_menu')

                
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="white-box p-0">

                                <div class="alert alert-info- p-0 ">
                                    <i class="fa fa-info-circle"></i> @lang('messages.taskSettingNote')
                                </div>
                                {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'POST']) !!}

                                <div class="form-group">
                                    <div class="checkbox checkbox-info  col-md-12">
                                        <input id="self_task" name="self_task" value="yes"
                                                @if($global->task_self == "yes") checked
                                                @endif
                                                type="checkbox">
                                        <label for="self_task">@lang('messages.employeeSelfTask')</label>
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <label class="control-label">@lang('modules.tasks.defaultTaskStatus')</label>
                                    <select name="default_task_status" class="form-control" id="default_task_status">
                                        @foreach ($taskboardColumns as $item)
                                            <option
                                            @if ($item->id == $global->default_task_status)
                                                selected
                                            @endif 
                                            value="{{ $item->id }}">{{ $item->column_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                
                                <div class="form-group">
                                    <button class="btn btn-success" id="save-form" type="button">@lang('app.save')</button>
                                </div>

                                {!! Form::close() !!}

                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

                            
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->

@endsection

@push('footer-script')

<script>
    // change task Setting For Setting
    $('#save-form').click(function () {

        $.easyAjax({
            url: '{{route('admin.task-settings.store')}}',
            container: '#editSettings',
            type: "POST",
            data: $('#editSettings').serialize()               
        })

    });

</script>
@endpush

