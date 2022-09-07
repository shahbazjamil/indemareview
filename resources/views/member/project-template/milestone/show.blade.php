@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">Home</a></li>
                <li><a href="{{ route('member.project-template.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">Tasks</li>
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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">

@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box">

                        <nav>
                            <ul>
                                <li ><a href="{{ route('member.project-template.show', $project->id) }}"><span>@lang('modules.projects.overview')</span></a>
                                </li>
<!--                                <li><a href="{{ route('member.project-template-member.show', $project->id) }}"><span>@lang('modules.projects.members')</span></a></li>-->
                                <li class="tab-current"><a href="{{ route('member.project-template-milestone.show', $project->id) }}"><span>@lang('modules.projects.milestones')</span></a></li>
                                <li ><a href="{{ route('member.project-template-task.show', $project->id) }}"><span>@lang('app.menu.tasks')</span></a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="milestone-list-panel">
                                    {{--<div class="white-box">--}}
                                    <div class="row m-b-10">
                                        <div class="col-md-12 hide" id="new-milestone-panel">
                                            <div class="panel panel-default">
                                                <div class="panel-heading "><i class="ti-plus"></i> @lang('modules.projects.createMilestone')
                                                    <div class="panel-action">
                                                        <a href="javascript:;" id="hide-new-milestone-panel"><i class="ti-close"></i></a>
                                                    </div>
                                                </div>
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body">
                                                        {!! Form::open(['id'=>'createMilestone','class'=>'ajax-form','method'=>'POST']) !!}

                                                        {!! Form::hidden('project_id', $project->id) !!}

                                                        <div class="form-body">
                                                    <div class="row m-t-10">
                                                        
                                                        <div class="col-md-6 ">
                                                            <div class="form-group">
                                                                <label>@lang('modules.projects.milestoneTitle')</label>
                                                                <input id="milestone_title" name="milestone_title" type="text"
                                                                       class="form-control" >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 ">
                                                                <div class="form-group">
                                                                    <label>@lang('app.status')</label>
                                                                    <select name="status" id="status" class="form-control">
                                                                        <option value="incomplete">@lang('app.incomplete')</option>
                                                                        <option value="complete">@lang('app.complete')</option>
                                                                    </select>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-6 ">
                                                            <div class="form-group">
                                                                <label>@lang('modules.invoices.currency')</label>
                                                                <select name="currency_id" id="currency_id" class="form-control">
                                                                    <option value="">--</option>
                                                                    @foreach ($currencies as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->currency_code.' ('.$item->currency_symbol.')' }}</option>           
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 ">
                                                            <div class="form-group">
                                                                <label>@lang('modules.projects.milestoneCost')</label>
                                                                <input id="cost" name="cost" type="number"
                                                                       class="form-control" value="0" min="0" step=".01">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    

                                                    <div class="row m-t-20">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="memo">@lang('modules.projects.milestoneSummary')</label>
                                                                <textarea name="summary" id="" rows="4" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
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

                                        <div class="col-md-12 hide" id="edit-milestone-panel">
                                        </div>
                                    </div>
                                    {{--</div>--}}
                                    <div class="white-box">
                                        <h2>@lang('modules.projects.milestones')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-6">
                                                <a href="javascript:;" id="show-new-milestone-panel" class="btn btn-success btn-outline btn-sm">
                                                    <i class="fa fa-plus"></i>
                                                    @lang('modules.projects.createMilestone')
                                                </a>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                   id="milestones-table">
                                                <thead>
                                                <tr>
                                                    <th>@lang('app.id')</th>
                                                    <th>@lang('modules.projects.milestoneTitle')</th>
                                                    <th>@lang('modules.projects.milestoneCost')</th>
                                                    <th>@lang('app.status')</th>
                                                    <th>@lang('app.action')</th>
                                                </tr>
                                                </thead>
                                            </table>
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
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="edit-column-form" role="dialog" aria-labelledby="myModalLabel"
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
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script type="text/javascript">
    var newTaskpanel = $('#new-milestone-panel');
    var taskListPanel = $('#milestone-list-panel');
    var editTaskPanel = $('#edit-milestone-panel');
    showTable();
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    //    save new task
    $('#save-task').click(function () {
        $.easyAjax({
            url: '{{route('member.project-template-milestone.store')}}',
            container: '#createMilestone',
            type: "POST",
            data: $('#createMilestone').serialize(),
            formReset: true,
            success: function (data) {
                $('#createMilestone').trigger("reset");
                $('.summernote').summernote('code', '');
                $('#milestone-list-panel ul.list-group').html(data.html);
                newTaskpanel.switchClass("show", "hide", 300, "easeInOutQuad");
                showTable();
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }
        })
    });

    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('milestone-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted milestone!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('member.project-template-milestone.destroy',':id') }}";
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
                            showTable();
                        }
                    }
                });
            }
        });
    });

    //    save new task
    taskListPanel.on('click', '.edit-milestone', function () {
        var id = $(this).data('milestone-id');
        var url = "{{route('member.project-template-milestone.edit', ':id')}}";
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            container: '#milestone-list-panel',
            data: {milestoneId: id},
            success: function (data) {
                editTaskPanel.html(data.html);
                // taskListPanel.switchClass("col-md-12", "col-md-6", 1000, "easeInOutQuad");
                newTaskpanel.addClass('hide').removeClass('show');
                editTaskPanel.switchClass("hide", "show", 300, "easeInOutQuad");
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('html, body').animate({
                    scrollTop: $("#milestone-list-panel").offset().top
                }, 1000);
            }
        })
    });

    // Show Task Detail Modal
    taskListPanel.on('click', '.show-task-detail', function () {
        var id = $(this).data('milestone-id');
        var url = "{{route('member.project-template-milestone.detail', ':id')}}";
        url = url.replace(':id', id);
        $.ajaxModal('#edit-column-form', url);
    });

    $('#show-new-milestone-panel').click(function () {
//    taskListPanel.switchClass('col-md-12', 'col-md-8', 1000, 'easeInOutQuad');
//         taskListPanel.switchClass("col-md-12", "col-md-6", 1000, "easeInOutQuad");
        editTaskPanel.addClass('hide').removeClass('show');
        newTaskpanel.switchClass("hide", "show", 300, "easeInOutQuad");

        $('html, body').animate({
            scrollTop: $("#milestone-list-panel").offset().top
        }, 1000);
    });

    $('#hide-new-milestone-panel').click(function () {
        newTaskpanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

    editTaskPanel.on('click', '#hide-edit-milestone-panel', function () {
        editTaskPanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");
    });

    jQuery('#due_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
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

    var table = '';

    function showTable() {
        var url = '{!!  route('member.project-template-milestone.data', [':templateId']) !!}?_token={{ csrf_token() }}';

        url = url.replace(':templateId', '{{ $project->id }}');

        table = $('#milestones-table').dataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                "url": url,
                "type": "GET"
            },
            deferRender: true,
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function (oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            "order": [[0, "desc"]],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                {data: 'milestone_title', name: 'milestone_title'},
                {data: 'cost', name: 'cost'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action'}
            ]
        });
    }

</script>
@endpush