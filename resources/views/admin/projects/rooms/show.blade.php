@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">Rooms</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('admin.projects.show_project_menu')
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="issues-list-panel">
                                    <div class="white-box p-0">
                                        <h2 class="border-bottom p-b-10">Rooms</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12 border-bottom p-b-10">
                                                <a href="javascript:;" id="show-add-form"
                                                   class="btn btn-success btn-outline"><i
                                                            class="fa fa-flag"></i> Create Room
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! Form::open(['id'=>'projectRoom','class'=>'ajax-form hide','method'=>'POST']) !!}

                                                {!! Form::hidden('project_id', $project->id) !!}

                                                <div class="form-body">
                                                    <div class="row m-t-30">
                                                        
                                                        <div class="col-md-3 ">
                                                            <div class="form-group">
                                                                <label>Room Title</label>
                                                                <input id="room_title" name="room_title" type="text"
                                                                       class="form-control" >
                                                            </div>
                                                        </div>
                                                        
                                                         <div class="col-md-3 ">
                                                            <div class="form-group">
                                                                <label>Products</label>
                                                                 <select id="product_id" name="product_id[]"  multiple="multiple" class="selectpicker form-control">
                                                                    <option value="">--</option>
                                                                    @foreach ($products as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>           
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>               
<!--                                                        <div class="col-md-4 ">
                                                            <div class="form-group">
                                                                <label>Room Total Cost</label>
                                                                <input id="total_cost" name="total_cost" type="number"
                                                                       class="form-control" value="0" min="0" step=".01">
                                                            </div>
                                                        </div>-->
                                                    </div>
                                                    <div class="row m-t-20">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="memo">Room Summary</label>
                                                                <textarea name="summary" id="" rows="4" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions m-t-30">
                                                    <button type="button" id="save-form" class="btn btn-success"><i
                                                                class="fa fa-check"></i> @lang('app.save')</button>
                                                    <button type="button" id="close-form" class="btn btn-default"><i
                                                                class="fa fa-times"></i> @lang('app.close')</button>
                                                </div>
                                                {!! Form::close() !!}

                                               
                                            </div>
                                        </div>

                                        <div class="table-responsive m-t-10">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                   id="projectroom-table">
                                                <thead>
                                                <tr>
                                                    <th>@lang('app.id')</th>
                                                    <th>Room Title</th>
                                                    <th>Room Cost</th>
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
    <div class="modal fade bs-modal-md in" id="editProjectRoomModal" role="dialog" aria-labelledby="myModalLabel"
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

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script>
    var table = $('#projectroom-table').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.rooms.data', $project->id) !!}',
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
            {data: 'room_title', name: 'room_title'},
            {data: 'total_cost', name: 'total_cost'},
            {data: 'action', name: 'action'}
        ]
    });


    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.rooms.store')}}',
            container: '#projectRoom',
            type: "POST",
            data: $('#projectRoom').serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    $('#projectRoom').trigger("reset");
                    $('#projectRoom').toggleClass('hide', 'show');
                    table._fnDraw();
                }
            }
        })
    });

    $('#show-add-form, #close-form').click(function () {
        $('#projectRoom').toggleClass('hide', 'show');
    });


    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('room-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted room!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.rooms.destroy',':id') }}";
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

    $('body').on('click', '.edit-room', function () {
        var id = $(this).data('room-id');

        var url = '{{ route('admin.rooms.edit', ':id')}}';
        url = url.replace(':id', id);

        $('#modelHeading').html('{{ __('app.edit') }} {{ __('modules.projects.rooms') }}');
        $.ajaxModal('#editProjectRoomModal', url);

    });

    $('body').on('click', '.room-detail', function () {
        var id = $(this).data('room-id');
        var url = '{{ route('admin.rooms.detail', ":id")}}';
        url = url.replace(':id', id);
        $('#modelHeading').html('@lang('app.update') Room');
        $.ajaxModal('#editProjectRoomModal',url);
    })
    $('ul.showProjectTabs .projectRooms').addClass('tab-current');
</script>
@endpush
