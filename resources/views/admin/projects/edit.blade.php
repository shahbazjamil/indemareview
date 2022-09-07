@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.edit')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.skinModern.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datetime-picker/datetimepicker.css') }}">
<style>
    .panel-black .panel-heading a, .panel-inverse .panel-heading a {
        color: unset!important;
    }
</style>
@endpush

@section('content')


    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading p-b-10 m-b-20"> @lang('modules.projects.updateTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'updateProject','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body ">
                            <h3 class="box-title m-b-10">@lang('modules.projects.projectInfo')</h3>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.projectName')</label>
                                        <input type="text" name="project_name" id="project_name" class="form-control"
                                               value="{{ $project->project_name }}">
                                    </div>
                                </div>
                      
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectCategory') <a
                                            href="javascript:;" id="addProjectCategory"><i class="ti-settings text-info"></i></a>
                                        </label>
                                        <select class="select2 form-control" name="category_id" id="category_id"
                                                data-style="form-control">
                                            @forelse($categories as $category)
                                                <option value="{{ $category->id }}"
                                                        @if($project->category_id == $category->id)
                                                        selected
                                                        @endif
                                                >{{ ucwords($category->category_name) }}</option>
                                            @empty
                                                <option value="">@lang('messages.noProjectCategoryAdded')</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.startDate')</label>
                                        <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control"
                                               value="{{ $project->start_date->format($global->date_format) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.startDateTime')</label>
                                        <input type="text" name="start_date_time" id="start_date_time" autocomplete="off" class="form-control"
                                               value="{{ \Carbon\Carbon::parse($project->start_date_time)->format('Y-m-d HH:mm') }}">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-4" id="deadlineBox">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.deadline')</label>
                                        <input type="text" name="deadline" id="deadline" autocomplete="off" class="form-control"
                                               value="@if($project->deadline){{ $project->deadline->format($global->date_format) }}@else {{ \Carbon\Carbon::now()->format($global->date_format) }} @endif">
                                    </div>
                                </div>
                                <div class="col-md-4 deadlineBox">
                                    <div class="form-group">
                                        <label>@lang('modules.projects.deadline')</label>
{{--                                        <input type="text" name="deadline_time" id="deadline_time" autocomplete="off" class="form-control"--}}
{{--                                               value="@if($project->deadline_time){{ \Carbon\Carbon::parse($project->deadline_time)->format('m/d/Y H:m a') }}@else {{ \Carbon\Carbon::now()->format('m/d/Y H:m a') }} @endif">--}}
                                        <input type="text" name="deadline_time" id="deadline_time" autocomplete="off" class="form-control"
                                               value="@if($project->deadline_time){{ \Carbon\Carbon::parse($project->deadline_time)->format('Y-m-d HH:mm') }}@else {{ \Carbon\Carbon::now()->format('Y-m-d HH:mm') }} @endif">
                                    </div>
                                </div>
                                <!--/span-->

                                <div class="col-md-4">
                                    <div class="form-group" style="padding-top: 25px;">
                                        <div class="checkbox checkbox-info">
                                            <input id="without_deadline" @if($project->deadline == null) checked @endif name="without_deadline" value="true"
                                                   type="checkbox">
                                            <label for="without_deadline">@lang('modules.projects.withoutDeadline')</label>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info">
                                            <input id="manual_timelog" name="manual_timelog" value="true"
                                                @if($project->manual_timelog == "enable") checked @endif
                                                type="checkbox">
                                            <label for="manual_timelog">@lang('modules.projects.manualTimelog')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/row-->


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectSummary')</label>
                                        <textarea name="project_summary" id="project_summary"
                                                  class="summernote">{{ $project->project_summary }}</textarea>
                                    </div>
                                </div>

                            </div>
                            <!--/span-->

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.note')</label>
                                        <textarea name="notes" id="notes" rows="3"
                                                  class="form-control">{{ $project->notes }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectCompletionStatus')</label>

                                        <div id="range_01"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group last">
                                        <div class="checkbox checkbox-info">
                                            <input id="calculate-task-progress" name="calculate_task_progress" value="true"
                                                   @if($project->calculate_task_progress == "true") checked @endif
                                                   type="checkbox">
                                            <label for="calculate-task-progress">@lang('modules.projects.calculateTasksProgress')</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <input type="hidden" name="completion_percent" id="completion_percent"
                                   value="{{ $project->completion_percent }}">

                            <h3 class="box-title m-b-10">@lang('modules.projects.clientInfo')
                                <a style="display: none;" href="javascript:;" id="addClient" class="btn btn-sm btn-outline btn-success">+ @lang('app.add') Client </a>
                            </h3>
                            <div class="row">
                                <div class="col-md-4 ">
                                    <div class="form-group">
                                        <select class="select2 select2-multiple" multiple="" name="client_id[]" id="client_id"
                                                data-style="form-control">
                                            <option value="null">--</option>
                                            @forelse($clients as $client)
                                                <?php if(in_array($client->id, $selected_clients)) { ?>
                                                <option value="{{ $client->id }}" selected >{{ ucwords($client->name) }}</option>
                                                <?php } else { ?>
                                                <option value="{{ $client->id }}" >{{ ucwords($client->name) }}</option>
                                                <?php } ?>
                                            @empty
                                                <option value="">@lang('modules.projects.selectClient')</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info">
                                            <input id="client_view_task" name="client_view_task" value="true" onchange="checkTask()"
                                                   @if($project->client_view_task == "enable") checked @endif
                                                   type="checkbox">
                                            <label for="client_view_task">@lang('modules.projects.clientViewTask')</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4" id="clientNotification">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-info  col-md-10">
                                            <input id="client_task_notification" name="client_task_notification" value="true"  @if($project->allow_client_notification == "enable") checked @endif
                                                   type="checkbox">
                                            <label for="client_task_notification">@lang('modules.projects.clientTaskNotification')</label>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.clientFeedback')</label>
                                        <textarea name="feedback" id="feedback" rows="5"
                                                  class="form-control">{{ $project->feedback }}</textarea>
                                    </div>
                                </div>

                            </div>
                            <!--/span-->

                            <h3 class="box-title m-b-30">@lang('modules.projects.budgetInfo')</h3>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.projectBudget')</label>
                                        <input type="text" class="form-control" name="project_budget" value="{{ $project->project_budget }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.invoices.currency')</label>
                                        <select name="currency_id" id="" class="form-control select2">
                                            <option value="">--</option>
                                            @foreach ($currencies as $item)
                                                <option
                                                @if($item->id == $project->currency_id) selected @endif
                                                value="{{ $item->id }}">{{ $item->currency_name }} ({{ $item->currency_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.projects.hours_allocated')</label>
                                        <input type="text" name="hours_allocated" class="form-control" value="{{ $project->hours_allocated }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Max Weekly Hours</label>
                                        <input type="text" name="max_weekly_hours" class="form-control" value="{{ $project->max_weekly_hours }}">
                                    </div>
                                </div>


                            </div>
                            <!--/span-->

                        
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.project') @lang('app.status')</label>
                                        <select name="status" id="" class="form-control">
                                            <option
                                                    @if($project->status == 'not started') selected @endif
                                            value="not started">@lang('app.notStarted')
                                            </option>
                                            <option
                                                    @if($project->status == 'in progress') selected @endif
                                            value="in progress">@lang('app.inProgress')
                                            </option>
                                            <option
                                                    @if($project->status == 'on hold') selected @endif
                                            value="on hold">@lang('app.onHold')
                                            </option>
                                            <option
                                                    @if($project->status == 'canceled') selected @endif
                                            value="canceled">@lang('app.canceled')
                                            </option>
                                            <option
                                                    @if($project->status == 'finished') selected @endif
                                            value="finished">@lang('app.finished')
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->



                            <!--Dropzone  -->
                            <div class="row m-b-20">
                                <div class="col-md-12">
                                    {{--@if($upload)--}}
                                        <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                                        <div id="file-upload-box" >
                                            <div class="row" id="file-dropzone">
                                                <div class="col-md-12">
                                                    <div class="dropzone"
                                                         id="file-upload-dropzone">
                                                        {{ csrf_field() }}
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple/>
                                                        </div>
                                                        <input name="image_url" id="image_url"type="hidden" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="taskID" id="taskID">
                                    {{--@else--}}
                                        {{--<div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>--}}
                                    {{--@endif--}}
                                </div>
                            </div>
                            <!-- Dropdown -->

                            <div class="row">
                                @foreach($fields as $field)
                                    <div class="col-md-6">
                                        <label>{{ ucfirst($field->label) }}</label>
                                        <div class="form-group">
                                            @if( $field->type == 'text')
                                                <input type="text" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$project->custom_fields_data['field_'.$field->id] ?? ''}}">
                                            @elseif($field->type == 'password')
                                                <input type="password" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$project->custom_fields_data['field_'.$field->id] ?? ''}}">
                                            @elseif($field->type == 'number')
                                                <input type="number" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" placeholder="{{$field->label}}" value="{{$project->custom_fields_data['field_'.$field->id] ?? ''}}">

                                            @elseif($field->type == 'textarea')
                                                <textarea name="custom_fields_data[{{$field->name.'_'.$field->id}}]" class="form-control" id="{{$field->name}}" cols="3">{{$project->custom_fields_data['field_'.$field->id] ?? ''}}</textarea>

                                            @elseif($field->type == 'radio')
                                                <div class="radio-list">
                                                    @foreach($field->values as $key=>$value)
                                                        <label class="radio-inline @if($key == 0) p-0 @endif">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="custom_fields_data[{{$field->name.'_'.$field->id}}]" id="optionsRadios{{$key.$field->id}}" value="{{$value}}" @if(isset($project) && $project->custom_fields_data['field_'.$field->id] == $value) checked @elseif($key==0) checked @endif>>
                                                                <label for="optionsRadios{{$key.$field->id}}">{{$value}}</label>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif($field->type == 'select')
                                                {!! Form::select('custom_fields_data['.$field->name.'_'.$field->id.']',
                                                        $field->values,
                                                         isset($project)?$project->custom_fields_data['field_'.$field->id]:'',['class' => 'form-control gender'])
                                                 !!}

                                            @elseif($field->type == 'checkbox')
                                                <div class="mt-checkbox-inline">
                                                    @foreach($field->values as $key => $value)
                                                        <label class="mt-checkbox mt-checkbox-outline">
                                                            <input name="custom_fields_data[{{$field->name.'_'.$field->id}}][]" type="checkbox" value="{{$key}}"> {{$value}}
                                                            <span></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif($field->type == 'date')
                                            <input type="text" class="form-control date-picker" size="16" name="custom_fields_data[{{$field->name.'_'.$field->id}}]"
                                            value="{{ ($project->custom_fields_data['field_'.$field->id] != '') ? \Carbon\Carbon::parse($project->custom_fields_data['field_'.$field->id])->format($global->date_format) : \Carbon\Carbon::now()->format($global->date_format)}}">
                                            @endif
                                            <div class="form-control-focus"> </div>
                                            <span class="help-block"></span>

                                        </div>
                                    </div>
                                @endforeach

                            </div>

                        </div>
                        <div class="form-actions m-t-15">
                            <button type="submit" id="save-form" class="btn btn-success"><i
                                        class="fa fa-check"></i> @lang('app.update')</button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel"
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
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/ion-rangeslider/js/ion-rangeSlider/ion.rangeSlider.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/datetime-picker/datetimepicker.js') }}"></script>

<script>


    Dropzone.autoDiscover = false;
    //Dropzone class
    myDropzone = new Dropzone("div#file-upload-dropzone", {
        url: "{{ route('admin.files.store') }}",
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        paramName: "file",
        maxFilesize: 10,
        maxFiles: 10,
        acceptedFiles: "image/*,application/pdf",
        autoProcessQueue: false,
        uploadMultiple: true,
        addRemoveLinks:true,
        parallelUploads:10,
        init: function () {
            myDropzone = this;
            this.on("success", function (file, response) {
                if(response.status == 'fail') {
                    $.showToastr(response.message, 'error');
                    return;
                }
            })
        }
    });

    myDropzone.on('sending', function(file, xhr, formData) {
        console.log(myDropzone.getAddedFiles().length,'sending');
        var ids = '{{ $project->id }}';
        formData.append('project_id', ids);
    });

    myDropzone.on('completemultiple', function () {
        var msgs = "@lang('messages.projectUpdatedSuccessfully')";
        $.showToastr(msgs, 'success');
        window.location.href = '{{ route('admin.projects.index') }}'

    });

    myDropzone.on('error', function (file,errorMessage) {
        console.log(errorMessage)
        //indow.location.href = '{{ route('admin.projects.index') }}'

    });


    $(".date-picker").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}'
    });


    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    checkTask();
    function checkTask()
    {
        var chVal = $('#client_view_task').is(":checked") ? true : false;
        if(chVal == true){
            $('#clientNotification').show();
        }
        else{
            $('#clientNotification').hide();
        }

    }
    @if($project->deadline == null)
        $('#deadlineBox').hide();
        $('.deadlineBox').hide();
    @endif
    $('#without_deadline').click(function () {
        var check = $('#without_deadline').is(":checked") ? true : false;
        if(check == true){
            $('#deadlineBox').hide();
            $('.deadlineBox').hide();
        }
        else{
            $('#deadlineBox').show();
            $('.deadlineBox').show();
        }
    });

    $("#start_date_time").datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#deadline_time').data('DateTimePicker').minDate(selected.date);
    });

    $("#start_date").datepicker({
        todayHighlight: true,
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#deadline').datepicker('setStartDate', minDate);
    });

    $("#deadline").datepicker({
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    }).on('changeDate', function (selected) {
                var maxDate = new Date(selected.date.valueOf());
                $('#start_date').datepicker('setEndDate', maxDate);
            });

    $("#deadline_time").datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
    }).on('changeDate', function (selected) {
        var maxDate = new Date(selected.date.valueOf());
        $('#start_date_time').data('DateTimePicker').maxDate(selected.date);
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.projects.update', [$project->id])}}',
            container: '#updateProject',
            type: "POST",
            //redirect: true,
            dataType : "json",
            async : false,
            data: $('#updateProject').serialize(),
            success: function(response){
                var dropzone = 0;
                {{--@if($upload)--}}
                    {{--dropzone = myDropzone.getQueuedFiles().length;--}}
                {{--@endif--}}

                    dropzone = myDropzone.getQueuedFiles().length;

                if(dropzone > 0){
                    //taskID = response.taskID;
                    $('#projectID').val(response.ProjectID);
                    myDropzone.processQueue();
                }
                else{
                    var msgs = "@lang('messages.projectCreatedSuccessfully')";
                    $.showToastr(msgs, 'success');
                    //window.location.href = '{{ route('admin.projects.index') }}'
                }
            },
            error : function (err) {
                console.log(err);
            }

        })
    });

    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
    });

    var completion = $('#completion_percent').val();

    $("#range_01").ionRangeSlider({
        grid: true,
        min: 0,
        max: 100,
        from: parseInt(completion),
        postfix: "%",
        onFinish: saveRangeData
    });

    var slider = $("#range_01").data("ionRangeSlider");

    $('#calculate-task-progress').change(function () {
        if($(this).is(':checked')){
            slider.update({"disable": true});
        }
        else{
            slider.update({"disable": false});
        }
    })

    function saveRangeData(data) {
        var percent = data.from;
        $('#completion_percent').val(percent);
    }

    $(':reset').on('click', function(evt) {
        evt.preventDefault()
        $form = $(evt.target).closest('form')
        $form[0].reset()
        $form.find('select').select2()
    });

    @if($project->calculate_task_progress == "true")
        slider.update({"disable": true});
    @endif
</script>

<script>
     $('#updateProject').on('click', '#addClient', function () {
        var url = '{{ route('admin.clients.create-client')}}';
        $('#modelHeading').html('Add Client');
        $.ajaxModal('#projectCategoryModal', url);
    })
    $('#updateProject').on('click', '#addProjectCategory', function () {
        var url = '{{ route('admin.projectCategory.create-cat')}}';
        $('#modelHeading').html('Manage Project Category');
        $.ajaxModal('#projectCategoryModal', url);
    })
</script>
@endpush
