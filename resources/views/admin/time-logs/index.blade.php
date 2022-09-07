@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="border-bottom col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="border-bottom col-xs-12 p-t-10 p-b-10">
        <a href="javascript:;" class="btn btn-primary btn-sm  timer-logs-modal"><i class="fa fa-clock-o"></i>
            @lang('modules.projects.activeTimers')
            <span class="badge badge-purple">{{ $activeTimers }}</span>
        </a>
        <a href="{{ route('admin.invoices.client', ['type' => 'timelog']) }}"
            class="btn btn-inverse btn-outline btn-sm">+ @lang('app.create')
            @lang('app.invoice')
        </a>
        <a href="{{ route('admin.all-time-logs.by-employee') }}" class="btn btn-primary btn-outline btn-sm"><i
                class="fa fa-user"></i> @lang('app.employee') @lang('app.menu.timeLogs')
        </a>
        <a href="javascript:;" id="show-add-form" class="btn btn-success btn-sm btn-outline"><i
                class="fa fa-clock-o"></i> @lang('modules.timeLogs.logTime')
        </a>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li class="active">{{ __($pageTitle) }}</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush


@section('filter-section')
<div class="row m-b-10">
    {!! Form::open(['id'=>'storePayments','class'=>'ajax-form','method'=>'POST']) !!}
    <div class="col-md-12">
        <div class="example">
            <h5 class="box-title m-t-30">@lang('app.selectDateRange')</h5>
            <div class="input-daterange input-group" id="date-range">
                <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')" value="" />
                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                <input type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')" value="" />
            </div>
        </div>
    </div>

    <div class="col-md-12 m-t-20">
        <h5 class="box-title">@lang('app.selectProject')</h5>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <select class="select2 form-control" data-placeholder="@lang('app.selectProject')" id="project_id">
                        <option value="all">@lang('modules.client.all')</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <h5 class="box-title">
            @lang('app.selectTask')
        </h5>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <select class="select2 form-control" data-placeholder="@lang('app.selectTask')" id="task_id">
                        <option value="all">@lang('modules.client.all')</option>
                        @foreach($tasks as $task)
                        <option value="{{ $task->id }}">{{ ucwords($task->heading) }}</option>
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <h5 class="box-title">@lang('modules.employees.title')</h5>
            <select class="form-control select2" name="employee" id="employee" data-style="form-control">
                <option value="all">@lang('modules.client.all')</option>
                @forelse($employees as $employee)
                <option value="{{$employee->id}}">{{ ucfirst($employee->name) }}</option>
                @empty
                @endforelse
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label col-xs-12">&nbsp;</label>
            <button type="button" id="filter-results" class="btn btn-success col-md-6"><i class="fa fa-check"></i>
                @lang('app.apply')</button>
            <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i
                    class="fa fa-refresh"></i> @lang('app.reset')</button>
        </div>
    </div>
    {!! Form::close() !!}

</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="white-box p-0">

            <div class="row">
                <div class="col-md-12 hide" id="hideShowTimeLogForm">
                    {!! Form::open(['id'=>'logTime','class'=>'ajax-form','method'=>'POST']) !!}
                    <div class="form-body">
                        <div class="row m-t-30">
                            <div class="col-md-3 ">
                                <div class="form-group">
                                    <label class="required">@lang('app.selectProject')</label>
                                    <select class="select2 form-control" name="project_id"
                                        data-placeholder="@lang('app.selectProject')" id="project_id2">
                                        <option value="">--</option>
                                        @foreach($timeLogProjects as $project)
                                        <option value="{{ $project->id }}">{{ ucwords($project->project_name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 ">
                                <div class="form-group">

                                    <label>Project Tasks</label>
                                    <select class="select2 form-control" name="task_id"
                                        data-placeholder="@lang('app.selectTask')" id="task_id2">
                                        <option value=""></option>
                                        @foreach($timeLogTasks as $task)
                                        <option value="{{ $task->id }}">{{ ucwords($task->heading) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 ">
                                <div class="form-group">
                                    <label class="required">All Tasks</label>
                                    <select class="select2 form-control" name="all_task_id"
                                        data-placeholder="@lang('app.selectTask')" id="all_task_id">
                                        <option value=""></option>
                                        @foreach($timeLogTasks as $task)
                                        <option value="{{ $task->id }}">{{ ucwords($task->heading) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 " id="employeeBox">
                                <div class="form-group">
                                    <label>@lang('modules.timeLogs.employeeName')</label>
                                    <select class="form-control" name="user_id" id="user_id" data-style="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label>@lang('modules.timeLogs.startDate')</label>
                                    <input id="start_date" name="start_date" type="text" class="form-control"
                                        value="{{ \Carbon\Carbon::today()->format($global->date_format) }}">
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label>@lang('modules.timeLogs.endDate')</label>
                                    <input id="end_date" name="end_date" type="text" class="form-control"
                                        value="{{ \Carbon\Carbon::today()->format($global->date_format) }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="dateAlertBox" style="display:none" class="alert alert-danger" data-alert="alert">
                                Start Date & End Date Are Not In Correct Format.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group bootstrap-timepicker timepicker">
                                    <label>@lang('modules.timeLogs.startTime')</label>
                                    <input type="text" name="start_time" id="start_time" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group bootstrap-timepicker timepicker">
                                    <label>@lang('modules.timeLogs.endTime')</label>
                                    <input type="text" name="end_time" id="end_time" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label style="width: 100%" for="">@lang('modules.timeLogs.totalHours')</label>
<!--                                <p id="total_time" class="form-control-static">0 Hrs</p>-->
                                <input style="width: 39%" type="number" name="total_hours" id="total_hours" value="0" class="form-control-sm">Hrs
                                <input style="width: 39%" type="number" name="total_minutes" id="total_minutes" value="0" class="form-control-sm">Mins
                                
                            </div>
                        </div>
                        <div class="row">
                            <div id="timeAlertBox" style="display:none" class="alert alert-danger" data-alert="alert">
                                Start Time & End Time Are Not In Correct Format.</div>
                        </div>
                        <div class="row m-t-20">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="memo">@lang('modules.timeLogs.memo')</label>
                                    <input type="text" name="memo" id="memo" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions m-t-30">
                        <button type="button" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
                            @lang('app.save')</button>
                    </div>
                    {!! Form::close() !!}
                    <hr>
                </div>
            </div>
            <div class="table-responsive m-t-30">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default
                footable-loaded footable']) !!}
            </div>

        </div>
    </div>

</div>
<!-- .row -->

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="editTimeLogModal" role="dialog" aria-labelledby="myModalLabel"
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
{{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>
    // $('#employeeBox').hide();

    $('#save-form').click(function () {
        if(validateTime() == false)
        {
            return;
        }

        $.easyAjax({
            url: '{{route('admin.time-logs.store')}}',
            container: '#logTime',
            type: "POST",
            data: $('#logTime').serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    showTable();
                    $('#hideShowTimeLogForm').toggleClass('hide', 'show');
                }
            }
        })
    });

    $('#project_id2').change(function () {
        var id = $(this).val();
        var url = '{{route('admin.all-time-logs.members', ':id')}}';
        url = url.replace(':id', id);
        // $('#employeeBox').show();
        $.easyAjax({
            url: url,
            type: "GET",
            redirect: true,
            success: function (data) {
                $('#user_id').html(data.html);
                $('#task_id2').html(data.tasks);
                $('#user_id, #task_id2').select2();
            }
        })
    });
    
    $('#task_id2').change(function () {
         var id = $(this).val();
         $('#all_task_id').select2("val", id);
        
    });
    
    $('#all_task_id').change(function () {
         $('#task_id2').select2("val", '');
        
    });

//    $('#task_id2').change(function () {
//        var id = $(this).val();
//        var url = '{{route('admin.all-time-logs.task-members', ':id')}}';
//        url = url.replace(':id', id);
//        // $('#employeeBox').show();
//        $.easyAjax({
//            url: url,
//            type: "GET",
//            redirect: true,
//            success: function (data) {
//                $('#user_id').html(data.html);
//                $('#user_id, #task_id2').select2();
//            }
//        })
//    });

    $('#show-add-form').click(function () {
        $('#hideShowTimeLogForm').toggleClass('hide', 'show');
    });
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    jQuery('#date-range').datepicker({
        toggleActive: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    var table;

    $('#all-time-logs-table').on('preXhr.dt', function (e, settings, data) {
        var startDate = $('#start-date').val();

        if(startDate == ''){
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if(endDate == ''){
            endDate = null;
        }

        var projectID = $('#project_id').val();
        var taskId = $('#task_id').val();
        var employee = $('#employee').val();
        var approved = $('#approved').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['projectId'] = projectID;
        data['taskId'] = taskId;
        data['employee'] = employee;
        data['approved'] = approved;
    });

    function showTable(){
        window.LaravelDataTables["all-time-logs-table"].draw();
    }

    $('#filter-results').click(function () {
        showTable();
    });

    $('#reset-filters').click(function () {
        $('.select2').val('all');
        $('.select2').trigger('change');

        $('#start-date').val('{{ $startDate }}');
        $('#end-date').val('{{ $endDate }}');

        showTable();
    });

    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('time-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted time log!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.all-time-logs.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            window.LaravelDataTables["all-time-logs-table"].draw();
                        }
                    }
                });
            }
        });
    });


    $('body').on('click', '.approve-timelog', function(){
        var id = $(this).data('time-id');
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: '@lang("app.yes")',
            cancelButtonText: '@lang("app.no")',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.all-time-logs.approve-timelog') }}";

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, id: id},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            showTable();
                        }
                    }
                });
            }
        });
    });

    $('#timer-list').on('click', '.stop-timer', function () {
        var id = $(this).data('time-id');
        var url = '{{route('admin.all-time-logs.stopTimer', ':id')}}';
        url = url.replace(':id', id);
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: url,
            type: "POST",
            data: {timeId: id, _token: token},
            success: function (data) {
                $('#timer-list').html(data.html);
                $('#activeCurrentTimerCount').html(data.activeTimers);
            }
        })

    });

    $('body').on('click', '.edit-time-log', function () {
        var id = $(this).data('time-id');

        var url = '{{ route('admin.time-logs.edit', ':id')}}';
        url = url.replace(':id', id);

        $('#modelHeading').html('Update Time Log');
        $.ajaxModal('#editTimeLogModal', url);

    });

    function exportTimeLog(){

        var startDate = $('#start-date').val();

        if(startDate == ''){
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if(endDate == ''){
            endDate = null;
        }

        var projectID = $('#project_id').val();
        var employee = $('#employee').val();

        var url = '{{ route('admin.all-time-logs.export', [':startDate', ':endDate', ':projectId', ':employee']) }}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':projectId', projectID);
        url = url.replace(':employee', employee);

        window.location.href = url;
    }

    $('#start_time, #end_time').timepicker({
        @if($global->time_format == 'H:i')
        showMeridian: false
        @endif
    }).on('hide.timepicker', function (e) {
        calculateTime();
    });

    jQuery('#start_date, #end_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    }).on('hide', function (e) {
        calculateTime();
    });
    function calculateTime() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var startTime = $("#start_time").val();
        var endTime = $("#end_time").val();
        var token = "{{ csrf_token() }}";
        var url = '{{route('admin.all-time-logs.calculate-time')}}';
        
        $.easyAjax({
            type: 'POST',
                    url: url,
                    data: {'_token': token, 'start_date': startDate, 'end_date' : endDate , 'start_time' : startTime , 'end_time' : endTime},
            success: function (response) {
                //$('#total_time').html(hours + "Hrs " + minutes + "Mins");
                $('#total_hours').val(response.total_hours);
                $('#total_minutes').val(response.total_minutes);
                
            }
        });
    }

//    function calculateTime() {
//        var format = '{{ $global->date_picker_format }}';
//        var startDate = $('#start_date').val();
//        var endDate = $('#end_date').val();
//        var startTime = $("#start_time").val();
//        var endTime = $("#end_time").val();
//
//        startDate = moment(startDate, format.toUpperCase()).format('YYYY-MM-DD');
//        endDate = moment(endDate, format.toUpperCase()).format('YYYY-MM-DD');
//
//        var timeStart = new Date(startDate + " " + convertTime12To24(startTime));
//        var timeEnd = new Date(endDate + " " + convertTime12To24(endTime));
//
//        var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
//
//        var minutes = diff % 60;
//        var hours = (diff - minutes) / 60;
//
//        if (hours < 0 || minutes < 0) {
//            var numberOfDaysToAdd = 1;
//            timeEnd.setDate(timeEnd.getDate() + numberOfDaysToAdd);
//            var dd = timeEnd.getDate();
//
//            if (dd < 10) {
//                dd = "0" + dd;
//            }
//
//            var mm = timeEnd.getMonth() + 1;
//
//            if (mm < 10) {
//                mm = "0" + mm;
//            }
//
//            var y = timeEnd.getFullYear();
//
//            // $('#end_date').val(mm + '/' + dd + '/' + y);
//            calculateTime();
//        } else {
//            //$('#total_time').html(hours + "Hrs " + minutes + "Mins");
//            $('#total_hours').val(hours);
//            $('#total_minutes').val(minutes);
//            
//        }
//    }
    
     function convertTime12To24(time){
        
            var hours = Number(time.match(/^(\d+)/)[1]);
            var minutes = Number(time.match(/:(\d+)/)[1]);
            var AMPM = time.match(/\s(.*)$/)[1];
            if(AMPM == "PM" && hours<12) hours = hours+12;
            if(AMPM == "AM" && hours==12) hours = hours-12;
            var sHours = hours.toString();
            var sMinutes = minutes.toString();
            if(hours<10) sHours = "0" + sHours;
            if(minutes<10) sMinutes = "0" + sMinutes;
            return sHours + ":" + sMinutes+ ":00";
    }

    // Added By Adil.
    Validation();

    function Validation()
    {
        $('#start_date').on('change', function() {
            DatePickerStatus('end_date', false);
            if ($('#end_date').is('[readonly]') == false) {
                validateDate();
            }
        });

        $('#end_date').on('change', function() {
            validateDate();
        });
    }

    function validateDate()
    {
        let startDate =  new Date(changeFormatofDate('start_date'));
        let endDate = new Date(changeFormatofDate('end_date'));
        if(startDate.getTime() > endDate.getTime()) {
            $("#dateAlertBox").fadeIn();
            closeAlertBox('dateAlertBox');
            DatePickerStatus('start_time', true);
            DatePickerStatus('end_time', true);
            return false;
        }
            DatePickerStatus('start_time', false);
            return true;
    }

    function validateTime()
    {
        let startTime =  timeFormatter(changeFormatofDate('start_date'),$('#start_time').val());
        let endTime = timeFormatter(changeFormatofDate('end_date'),$('#end_time').val());
        if(startTime.getTime() > endTime.getTime()) {
            $("#timeAlertBox").fadeIn();
            closeAlertBox('timeAlertBox');
            return false;
        }
        return true;
    }

    $('#start_time').focusout(function(){
        DatePickerStatus('end_time', false);
    });

    $('#end_time').focusout(function(){
        validateTime();
    });


function timeFormatter(date,time)
{
    return new Date(date + " " + time);
}


function DatePickerStatus(id, status) {
    $('#' + id).prop('readonly', status);
}

function changeFormatofDate(id) {
    let format = '{{ $global->date_picker_format }}';
    return moment($('#' + id).val(), format.toUpperCase()).format('YYYY-MM-DD');
}

function closeAlertBox(id){
    window.setTimeout(function () {
        $("#" + id).fadeOut(1000)
    }, 3000)
}

</script>
@endpush