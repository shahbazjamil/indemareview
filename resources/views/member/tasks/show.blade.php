@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} - <span class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.tasks')</li>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">

@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('member.projects.show_project_menu')

                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            {{-- <div class="row"> --}}
                                <div class="col-md-12" id="task-list-panel">
                                    {{--<div class="white-box">--}}
                                    <div class="row m-b-10">
                                        <div class="col-md-12 hide" id="new-task-panel">
                                            <div class="panel panel-default border-0">
                                                <div class="panel-heading"><i class="ti-plus"></i> @lang('modules.tasks.newTask')
                                                    <div class="panel-action">
                                                        <a href="javascript:;" id="hide-new-task-panel"><i class="ti-close"></i></a>
                                                    </div>
                                                </div>
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body p-0">
                                                        {!! Form::open(['id'=>'createTask','class'=>'ajax-form','method'=>'POST']) !!}

                                                        {!! Form::hidden('project_id', $project->id) !!}

                                                        <div class="form-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('app.title')</label>
                                                                        <input type="text" id="heading" name="heading"
                                                                               class="form-control">
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('modules.tasks.taskCategory') </label>
                                                                        <select class="selectpicker form-control" name="category_id" id="category_id"
                                                                                data-style="form-control">
                                                                            @forelse($categories as $category)
                                                                                <option value="{{ $category->id }}">{{ ucwords($category->category_name) }}</option>
                                                                            @empty
                                                                                <option value="">@lang('messages.noTaskCategoryAdded')</option>
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('app.description')</label>
                                                                        <textarea id="description" name="description"
                                                                                  class="form-control summernote"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('app.startDate')</label>
                                                                        <input type="text" name="start_date" id="start_date2" class="form-control" value="">
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('app.dueDate')</label>
                                                                        <input type="text" name="due_date" id="due_date"
                                                                               class="form-control">
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                
                                                                        <div class="checkbox checkbox-info">
                                                                            <input id="billable-task" checked name="billable" value="true" type="checkbox">
                                                                            <label for="billable-task">@lang('modules.tasks.billable') 
                                                                                <a class="mytooltip font-12" href="javascript:void(0)"> <i
                                                                                class="fa fa-info-circle"></i>
                                                                                    <span class="tooltip-content5">
                                                                                        <span class="tooltip-text3">
                                                                                            <span class="tooltip-inner2">
                                                                                                @lang('modules.tasks.billableInfo')
                                                                                            </span>
                                                                                        </span>
                                                                                    </span>
                                                                                </a>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <label class="control-label required">@lang('modules.tasks.assignTo')</label>
                                                                    <div class="form-group">
                                                                        <select class="select2 select2-multiple " multiple="multiple" data-placeholder="@lang('modules.tasks.chooseAssignee')"  name="user_id[]" id="user_id">
                                                                            <option value=""></option>
                                                                            @foreach($project->members as $member)
                                                                                <option value="{{ $member->user->id }}">{{ $member->user->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label">@lang('modules.tasks.priority')</label>

                                                                        <div class="radio radio-danger">
                                                                            <input type="radio" name="priority" id="radio13"
                                                                                   value="high">
                                                                            <label for="radio13" class="text-danger">
                                                                                @lang('modules.tasks.high') </label>
                                                                        </div>
                                                                        <div class="radio radio-warning">
                                                                            <input type="radio" name="priority" checked
                                                                                   id="radio14" value="medium">
                                                                            <label for="radio14" class="text-warning">
                                                                                @lang('modules.tasks.medium') </label>
                                                                        </div>
                                                                        <div class="radio radio-success">
                                                                            <input type="radio" name="priority" id="radio15"
                                                                                   value="low">
                                                                            <label for="radio15" class="text-success">
                                                                                @lang('modules.tasks.low') </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                            </div>
                                                            <!--/row-->

                                                        </div>
                                                        <div class="form-actions">
                                                            <button type="submit" id="save-task" class="btn btn-success"><i
                                                                        class="fa fa-check"></i> @lang('app.save')
                                                            </button>
                                                        </div>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 hide" id="edit-task-panel">
                                        </div>
                                    </div>
                                    {{--</div>--}}
                                    <div class="white-box p-0">
                                        <h2	class="border-bottom">@lang('app.menu.tasks')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12 border-bottom p-b-10">
                                                <a href="javascript:;" id="show-new-task-panel" class="btn btn-success btn-outline btn-sm">
                                                    + @lang('modules.tasks.newTask')
                                                </a>
                                            </div>
                                        </div>


                                    <div class="table-responsive">
                                        {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                                    </div>
                                    </div>
                                </div>
                            {{-- </div> --}}
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

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}


<script type="text/javascript">
    var newTaskpanel = $('#new-task-panel');
    var taskListPanel = $('#task-list-panel');
    var editTaskPanel = $('#edit-task-panel');

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    
      $('.pinnedItem').click(function(){
        var url = '{{ route('member.all-tasks.pinned-task')}}';
        $('#modelHeading').html('Pinned Task');
        $.ajaxModal('#taskCategoryModal',url);
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
    
     $('#allTasks-table').on('preXhr.dt', function (e, settings, data) {
        
        startDate = null;
        endDate = null;

        var projectID = '{{$project->id}}';

        data['clientID'] = '';
        data['assignedBY'] = '';
        data['assignedTo'] = '';
        data['status'] = '';
        data['category_id'] = '';
        data['hideCompleted'] = 0;
        data['projectId'] = projectID;
        data['startDate'] = '';
        data['endDate'] = '';
    });

    var table = '';

    function showTable() {
        
        window.LaravelDataTables["allTasks-table"].draw();
        
    }
    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('task-id');

        var buttons = {
            cancel: "No, cancel please!",
            confirm: {
                text: "Yes, delete it!",
                value: 'confirm',
                visible: true,
                className: "danger",
            }
        };

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted task!",
            dangerMode: true,
            icon: 'warning',
            buttons: buttons
        }).then(function (isConfirm) {
            if (isConfirm == 'confirm') {

                var url = "{{ route('member.all-tasks.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });

    $('#allTasks-table').on('click', '.show-task-detail', function () {
        $(".right-sidebar").slideDown(50).addClass("shw-rside");

        var id = $(this).data('task-id');
        var url = "{{ route('member.all-tasks.show',':id') }}";
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

    jQuery('#start_date2').datepicker({
        format: '{{ $global->date_picker_format }}',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (selected) {
        $('#due_date').datepicker({
            format: '{{ $global->date_picker_format }}',
            autoclose: true,
            todayHighlight: true
        });
        var minDate = new Date(selected.date.valueOf());
        $('#due_date').datepicker("update", minDate);
        $('#due_date').datepicker('setStartDate', minDate);
    });
    
    $('body').on('click', '.task-timer-start-click', function () {
          var id = $(this).data('task-id');
          var project_id = $(this).data('project-id');
          var url = "{{route('member.all-tasks.live-timeLog',':id')}}";
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
          var url = "{{route('member.all-tasks.live-timeLog-stop',':id')}}";
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

    //    save new task
    $('#save-task').click(function () {
        $.easyAjax({
            url: '{{route('member.tasks.store')}}',
            container: '#section-line-3',
            type: "POST",
            data: $('#createTask').serialize(),
            success: function (data) {
                $('#createTask').trigger("reset");
                $('.summernote').summernote('code', '');
                $('#task-list-panel ul.list-group').html(data.html);
                newTaskpanel.switchClass("show", "hide", 300, "easeInOutQuad");
                showTable();
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }
        })
    });

    //    save new task
    taskListPanel.on('click', '.edit-task', function () {
        var id = $(this).data('task-id');
        var url = "{{route('member.tasks.edit', ':id')}}";
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            container: '#task-list-panel',
            data: {taskId: id},
            success: function (data) {
                editTaskPanel.html(data.html);
                newTaskpanel.addClass('hide').removeClass('show');
                editTaskPanel.switchClass("hide", "show", 300, "easeInOutQuad");
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('html, body').animate({
                    scrollTop: $("#task-list-panel").offset().top
                }, 1000);
            }
        })
    });

    //    change task status
    taskListPanel.on('click', '.task-check', function () {
        if ($(this).is(':checked')) {
            var status = 'completed';
        }else{
            var status = 'incomplete';
        }

        var sortBy = $('#sort-task').val();

        var id = $(this).data('task-id');

        if(status == 'completed'){
            var checkUrl = '{{route('member.tasks.checkTask', ':id')}}';
            checkUrl = checkUrl.replace(':id', id);
            $.easyAjax({
                url: checkUrl,
                type: "GET",
                container: '#task-list-panel',
                data: {},
                success: function (data) {
                    console.log(data.taskCount);
                    if(data.taskCount > 0){
                        swal({
                            title: "Are you sure?",
                            text: "There is a incomplete sub-task in this task do you want to mark complete!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, complete it!",
                            cancelButtonText: "No, cancel please!",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                updateTask(id,status,sortBy)
                            }
                        });
                    }
                    else{
                        updateTask(id,status,sortBy)
                    }

                }
            });
        }
        else{
            updateTask(id,status,sortBy)
        }


    });

    // Update Task
    function updateTask(id,status,sortBy){
        var url = "{{route('member.tasks.changeStatus')}}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            container: '#section-line-3',
            data: {'_token': token, taskId: id, status: status, sortBy: sortBy},
            success: function (data) {
                $('#task-list-panel ul.list-group').html(data.html);
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }
        })
    }

    //    save new task
    $('#sort-task, #hide-completed-tasks').change(function() {
        var sortBy = $('#sort-task').val();
        var id = $('#sort-task').data('project-id');

        var url = "{{route('member.tasks.sort')}}";
        var token = "{{ csrf_token() }}";

        if ($('#hide-completed-tasks').is(':checked')) {
            var hideCompleted = '1';
        }else {
            var hideCompleted = '0';
        }

        $.easyAjax({
            url: url,
            type: "POST",
            container: '#task-list-panel',
            data: {'_token': token, projectId: id, sortBy: sortBy, hideCompleted: hideCompleted},
            success: function (data) {
                $('#task-list-panel ul.list-group').html(data.html);
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }
        })
    });

    $('#show-new-task-panel').click(function () {
        editTaskPanel.addClass('hide').removeClass('show');
        newTaskpanel.switchClass("hide", "show", 300, "easeInOutQuad");

        $('html, body').animate({
            scrollTop: $("#task-list-panel").offset().top
        }, 1000);
    });

    $('#hide-new-task-panel').click(function () {
        newTaskpanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

    editTaskPanel.on('click', '#hide-edit-task-panel', function () {
        editTaskPanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

    $('#dependent-task').change(function () {
        if($(this).is(':checked')){
            $('#dependent-fields').show();
        }
        else{
            $('#dependent-fields').hide();
        }
    })
    
    
     $('#allTasks-table').on('click', '.change-status', function () {
        var url = "{{route('member.tasks.changeStatus')}}";
        var token = "{{ csrf_token() }}";
        var id =  $(this).data('task-id');
        var status =  $(this).data('status');

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, taskId: id, status: status, sortBy: 'id'},
            success: function (data) {
                if (data.status == "success") {                    
                    $('#priority_btn_'+data.taskId).html(data.column+' <span class="caret"></span>');
                    $('#priority_btn_'+data.taskId).css('color',data.textColor);
                }
            }
        })
    })
    
    $('#allTasks-table').on('click', '.update-task-detail', function () {
        var id =  $(this).data('task-id');
        var token = "{{ csrf_token() }}";
        var status =  $('#task_id_'+id).val();
        
        var url = "{{route('member.all-tasks.live-update',':id')}}";
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

    $('ul.showProjectTabs .projectTasks').addClass('tab-current');

</script>
@endpush
