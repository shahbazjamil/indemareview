@extends('layouts.client-app')
@push('head-script')
<style>
    .d-none {
        display: none;
    }
</style>
@endpush
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
<link rel="stylesheet" href="{{ asset('css/datatables/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/datatables/responsive.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/datatables/buttons.dataTables.min.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

@endpush

@section('filter-section')
<div class="row" id="ticket-filters">
    <form action="" id="filter-form">
        <div class="col-md-12">
            <h5>@lang('app.selectDateRange')</h5>
            <div class="input-daterange input-group" id="date-range">
                <input type="text" class="form-control" autocomplete="off" id="filter-start-date"
                       placeholder="@lang('app.startDate')"
                       value=""/>
                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                <input type="text" class="form-control" autocomplete="off" id="filter-end-date"
                       placeholder="@lang('app.endDate')"
                       value=""/>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <h5>@lang('app.status')</h5>
                <select class="form-control select2" name="status" id="filter-status" data-style="form-control">
                    <option 
                        value="not finished">@lang('zoom::modules.zoommeeting.hideFinishedMeetings')
                    </option>
                    <option value="all">@lang('app.all')</option>
                    <option value="waiting">@lang('zoom::modules.zoommeeting.waiting')</option>
                    <option value="live">@lang('zoom::modules.zoommeeting.live')</option>
                    <option value="canceled">@lang('app.canceled')</option>
                    <option value="finished">@lang('app.finished')</option>
                </select>
            </div>
        </div>   
        <div class="col-md-12">
            <div class="form-group p-t-10">
                <button type="button" id="apply-filters" class="btn btn-success btn-sm col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 btn-sm col-md-offset-1">
                    <i class="fa fa-refresh"></i> @lang('app.reset')</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('content')

<div class="row">

    <div class="col-md-12">
        <div class="white-box">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
            </div>
        </div>
    </div>
</div>

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="meetingDetailModal" role="dialog" aria-labelledby="myModalLabel"
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
<script src="{{ asset('js/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/datatables/responsive.bootstrap.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('js/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: '{{ $global->date_picker_format }}',
        language: '{{ $global->locale }}',
        autoclose: true
    });
    
    $('#start_date, #end_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: '{{ $global->date_picker_format }}',
    })

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#meeting-table').on('preXhr.dt', function (e, settings, data) {
        var status   = $('#filter-status').val();
        var startDate = $('#filter-start-date').val();

        if (startDate == '') {
            startDate = 0;
        }

        var endDate = $('#filter-end-date').val();

        if (endDate == '') {
            endDate = 0;
        }

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['status'] = status;
    });


    $('#apply-filters').click(function () {
        window.LaravelDataTables["meeting-table"].draw();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('.select2').val('not finished');
        $('#filter-form').find('select').select2();
        loadTable();
    })

    $(function() {
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('user-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted user!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('client.zoom-meeting.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                window.LaravelDataTables["meeting-table"].draw();
                            }
                        }
                    });
                }
            });
        });
    })
    function loadTable(){
        window.LaravelDataTables["meeting-table"].draw();
    }
    
    var getEventDetail = function (id) {
        var url = "{{ route('client.zoom-meeting.show', ':id')}}";
        url = url.replace(':id', id);

        $('#modelHeading').html('Meeting');
        $.ajaxModal('#meetingDetailModal', url);
    }

</script>
@endpush