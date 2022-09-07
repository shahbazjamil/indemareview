@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div @if($totalRecords == 0) style="display: none;"  @endif class="border-bottom col-xs-12 p-t-10 p-b-10">
            <a href="javascript:;" class="btn btn-outline btn-info btn-sm pinnedItem">@lang('app.pinnedTask') <i class="icon-pin icon-2"></i></a>
            <a href="javascript:void(0)" id="createTaskboard" class="btn btn-outline btn-success btn-sm">+ @lang('modules.tasks.addStatus') </a>
            <a href="{{ route('admin.task-template.index') }}"  class="btn btn-outline btn-primary btn-sm">+ Add Task Templates</a>
            <a href="{{ route('admin.all-tasks.create') }}" class="btn btn-outline btn-success btn-sm">+ @lang('modules.tasks.newTask') </a>
            
            <div class="btn-group dropdown doverlay">
                        <button aria-expanded="true" data-toggle="dropdown" class="dt-button btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md" type="button"><i class="fa fa-upload"></i> &nbsp;Import <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('admin.all-tasks.download-template') }}" target="_blank">
                                    <i class="fa fa-download"></i> &nbsp;Download Template
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" onclick="$('#importCSVModal').modal('show')"><i class="fa fa-file-excel-o"></i> &nbsp;Import CSV</a>
                            </li>
                        </ul>
            </div>
            
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<style>
    .swal-footer {
        text-align: center !important;
    }
</style>
@endpush


@section('filter-section')
    <div class="row m-b-10">
        {!! Form::open(['id'=>'storePayments','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="col-md-12">
            <div class="example">
                <h5 class="box-title">@lang('app.selectDateRange')</h5>

                <div class="input-daterange input-group" id="date-range">
                    <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                            value=""/>
                    <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                    <input type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                            value=""/>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <h5 class="box-title">@lang('app.selectProject')</h5>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <select class="select2 form-control" data-placeholder="@lang('app.selectProject')" id="project_id">
                            <option value="all">@lang('app.all')</option>
                            @foreach($projects as $project)
                                <option
                                        value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <h5 class="box-title">@lang('app.select') @lang('app.client')</h5>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <select class="select2 form-control" data-placeholder="@lang('app.client')" id="clientID">
                            <option value="all">@lang('app.all')</option>
                            @foreach($clients as $client)
                                <option
                                        value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <h5 class="box-title">@lang('app.select') @lang('modules.tasks.assignTo')</h5>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <select class="select2 form-control" data-placeholder="@lang('modules.tasks.assignTo')" id="assignedTo">
                            <option value="all">@lang('app.all')</option>
                            @foreach($employees as $employee)
                                <option
                                        value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <h5 class="box-title">@lang('app.select') @lang('modules.tasks.assignBy')</h5>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <select class="select2 form-control" data-placeholder="@lang('modules.tasks.assignBy')" id="assignedBY">
                            <option value="all">@lang('app.all')</option>
                            @foreach($employees as $employee)
                                <option
                                        value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <h5 class="box-title">@lang('app.select') @lang('app.status')</h5>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <select class="select2 form-control" data-placeholder="@lang('status')" id="status">
                            <option value="all">@lang('app.all')</option>
                            @foreach($taskBoardStatus as $status)
                                <option value="{{ $status->id }}">{{ ucwords($status->column_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">

            <div class="checkbox checkbox-info">
                <input type="checkbox" id="hide-completed-tasks">
                <label for="hide-completed-tasks">@lang('app.hideCompletedTasks')</label>
            </div>
        </div>

        <div class="col-md-12">
            </button>
            <div class="form-group">
                <label class="control-label col-xs-12">&nbsp;</label>
                <button type="button" id="filter-results" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
            </div>
        </div>
        {!! Form::close() !!}

    </div>
@endsection

@section('content')

    <div @if($totalRecords == 0) style="display: none;"  @endif class="row">
        <div class="col-md-12">
            
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                       <i class="fa fa-check"></i> {!! $message !!}
                    </div>
                    <?php Session::forget('success');?>
            @endif

            @if ($message = Session::get('error'))
                <div class="custom-alerts alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php Session::forget('error');?>
            @endif
            
            <div class="white-box p-0">

                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>

            </div>
        </div>

    </div>
    <!-- .row -->
	
    <div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/task-management.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Task Management</h1>
			<p class="m-b-30">Create tasks for your projects with details and due dates. Automatically track your time and bill your clinets later from the time tracked.</p>
			<a href="{{ route('admin.all-tasks.create') }}" class="btn-black">+ @lang('modules.tasks.newTask') </a>
			<a href="javascript:;" onclick="$('#video-modal').modal('show')" class="btn-black">See how it works <i class="fa fa-play"></i></a>
		</div><!--end of col-8-->
		<div class="col-md-12 text-right">
			Have Questions? <a href="mailto:support@indema.co">Contact Support</a>
		</div><!--end of col-12-->
	</div><!--end of row-->
    
    <div class="modal fade bs-modal-md in" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="video-modal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
				<div class="modal-header p-t-15 p-b-15 p-r-15">
					<h4 class="modal-title">Task Management</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>

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

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
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
    
    
    <div class="modal fade bs-modal-md in" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Import Projects</h4>
                </div>
                    <div class="modal-body">
                        <div class="portlet-body">
                        
                        <form method='post' id="importCSVFrm" action='{{ route('admin.all-tasks.import') }}' enctype='multipart/form-data' >
                            {{ csrf_field() }}
                            <div class="form-body">
                                <div class="row ">
                                    <div class="col-xs-12 m-b-10">
                                        <div class="form-group">
                                            <label class="col-xs-3">Select File</label>
                                            <div class="col-xs-9">
                                                <input type="file" name="csv_file" id="csv_file" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void()"  type="button" class="btn btn-success" onclick="$('#importCSVFrm').submit()">Import</a>
                </div>
            </div>
        </div>
    </div>
    
    
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@if($global->locale == 'en')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
@else
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
@endif
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    
     $('.pinnedItem').click(function(){
        var url = '{{ route('admin.all-tasks.pinned-task')}}';
        $('#modelHeading').html('Pinned Task');
        $.ajaxModal('#taskCategoryModal',url);
    });

    $('#allTasks-table').on('preXhr.dt', function (e, settings, data) {
        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var projectID = $('#project_id').val();
        if (!projectID) {
            projectID = 0;
        }
        var clientID = $('#clientID').val();
        var assignedBY = $('#assignedBY').val();
        var assignedTo = $('#assignedTo').val();
        var status = $('#status').val();
        var category_id = $('#category_id').val();

        if ($('#hide-completed-tasks').is(':checked')) {
            var hideCompleted = '1';
        } else {
            var hideCompleted = '0';
        }

        data['clientID'] = clientID;
        data['assignedBY'] = assignedBY;
        data['assignedTo'] = assignedTo;
        data['status'] = status;
        data['category_id'] = category_id;
        data['hideCompleted'] = hideCompleted;
        data['projectId'] = projectID;
        data['startDate'] = startDate;
        data['endDate'] = endDate;
    });

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: '{{ $global->date_picker_format }}',
        language: '{{ $global->locale }}',
        autoclose: true,
        weekStart:'{{ $global->week_start }}',
    });

    table = '';

    function showTable() {
        window.LaravelDataTables["allTasks-table"].draw();
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
    })


    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('task-id');
        var recurring = $(this).data('recurring');

        var buttons = {
            cancel: "No, cancel please!",
            confirm: {
                text: "Yes, delete it!",
                value: 'confirm',
                visible: true,
                className: "danger",
            }
        };

        if(recurring == 'yes')
        {
            buttons.recurring = {
                text: "{{ trans('modules.tasks.deleteRecurringTasks') }}",
                value: 'recurring'
            }
        }

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted task!",
            dangerMode: true,
            icon: 'warning',
            buttons: buttons
        }).then(function (isConfirm) {
            if (isConfirm == 'confirm' || isConfirm == 'recurring') {

                var url = "{{ route('admin.all-tasks.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";
                var dataObject = {'_token': token, '_method': 'DELETE'};

                if(isConfirm == 'recurring')
                {
                    dataObject.recurring = 'yes';
                }

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: dataObject,
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            window.LaravelDataTables["allTasks-table"].draw();
                        }
                    }
                });
            }
        });
    });

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

    $('#allTasks-table').on('click', '.change-status', function () {
        var url = "{{route('admin.tasks.changeStatus')}}";
        var token = "{{ csrf_token() }}";
        var id =  $(this).data('task-id');
        var status =  $(this).data('status');

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, taskId: id, status: status, sortBy: 'id'},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    })
    
    $('#allTasks-table').on('click', '.update-task-detail', function () {
        var id =  $(this).data('task-id');
        var token = "{{ csrf_token() }}";
        var status =  $('#task_id_'+id).val();
        
        var url = "{{route('admin.all-tasks.live-update',':id')}}";
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, status: status},
            success: function (data) {
                if (data.status == "success") {
                    window.LaravelDataTables["allTasks-table"].draw();
                }
            }
        })
    })
    
     $('body').on('click', '.task-timer-start-click', function () {
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
                    window.LaravelDataTables["allTasks-table"].draw();
                    //updateTimer();
                }
            })
      });
      
      $('body').on('click', '.task-timer-stop-click', function () {
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
                    window.LaravelDataTables["allTasks-table"].draw();
                    //updateTimer();
                }
            })
      });

    
  
    
    

    //showTable();
    $('#createTaskCategory').click(function(){
        var url = '{{ route('admin.taskCategory.create')}}';
        $('#modelHeading').html("@lang('modules.taskCategory.manageTaskCategory')");
        $.ajaxModal('#taskCategoryModal',url);
    });
    $('#createTaskboard').click(function(){
        var url = '{{ route('admin.all-tasks.create-taskboard')}}';
        $('#modelHeading').html("@lang('modules.taskCategory.manageTaskCategory')");
        $.ajaxModal('#taskCategoryModal',url);
    });
    
   
    
    function exportData(){

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var projectID = $('#project_id').val();
        if (!projectID) {
            projectID = 0;
        }

        if ($('#hide-completed-tasks').is(':checked')) {
            var hideCompleted = '1';
        } else {
            var hideCompleted = '0';
        }

        var url = '{!!  route('admin.all-tasks.export', [':startDate', ':endDate', ':projectId', ':hideCompleted']) !!}';

        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':hideCompleted', hideCompleted);
        url = url.replace(':projectId', projectID);

        window.location.href = url;
    }
    
        function updateTimer() {
            var $worked = $("#active-timer-task");
            if ($worked.length){
                var myTime = $worked.html();
                var ss = myTime.split(":");
    //            console.log(ss);

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
                setTimeout(updateTimer, 1000);
            }
        }
		
     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/GBHr8f75w1o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    });
</script>
@endpush
