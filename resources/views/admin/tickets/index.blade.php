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
            {{--<a href="{{ route('admin.ticket-agents.index') }}" class="btn btn-sm btn-inverse btn-outline"><i class="fa fa-gear"></i> @lang('app.menu.ticketSettings')</a>--}}
            <a href="{{ route('admin.tickets.create') }}"
                               class="btn btn-success btn-outline btn-sm">+ @lang('modules.tickets.addTicket') </a>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    {{--<div class="row">--}}
    {{--<h1>aqeel</h1>--}}
    {{--<div class="col-md-12">--}}
    {{--<div class="white-box p-b-0 m-b-0">--}}
    {{--<div class="row">--}}
    {{--<div class="col-sm-4">--}}
    {{--<label class="control-label">@lang('app.selectDateRange')</label>--}}

    {{--<div class="form-group">--}}
    {{--<input class="form-control input-daterange-datepicker" type="text" name="daterange"--}}
    {{--value="{{ $startDate.' - '.$endDate }}"/>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="col-md-12">--}}
    {{--<div class="row">--}}
    {{--<div class="col-md-12">--}}
    {{--<div class="row dashboard-stats">--}}
    {{--<div class="col-md-12">--}}
    {{--<div class="white-box">--}}
    {{--<div class="col-md-4 col-sm-6">--}}
    {{--<h4>--}}
    {{--<span class="text-dark" id="totalTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalTickets')</span>--}}
    {{--<a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.totalTicketInfo')</span></span></span></a>--}}
    {{--</h4>--}}
    {{--</div>--}}
    {{--<div class="col-md-4 col-sm-6">--}}
    {{--<h4>--}}
    {{--<span class="text-success" id="closedTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalClosedTickets')</span>--}}
    {{--<a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.closedTicketInfo')</span></span></span></a>--}}
    {{--</h4>--}}
    {{--</div>--}}
    {{--<div class="col-md-4 col-sm-6">--}}
    {{--<h4>--}}
    {{--<span class="text-danger" id="openTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalOpenTickets')</span>--}}
    {{--<a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.openTicketInfo')</span></span></span></a>--}}
    {{--</h4>--}}
    {{--</div>--}}
    {{----}}
    {{--<div class="col-md-4 col-sm-6">--}}
    {{--<h4>--}}
    {{--<span class="text-warning" id="pendingTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalPendingTickets')</span>--}}
    {{--<a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.pendingTicketInfo')</span></span></span></a>--}}
    {{--</h4>--}}
    {{--</div>--}}
    {{----}}
    {{--<div class="col-md-4 col-sm-6">--}}
    {{--<h4>--}}
    {{--<span class="text-info" id="resolvedTickets">0</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.totalResolvedTickets')</span>--}}
    {{--<a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tickets.resolvedTicketInfo')</span></span></span></a>--}}
    {{--</h4>--}}
    {{--</div>--}}
    {{----}}
    {{--</div>--}}
    {{--</div>--}}
    {{----}}
    {{--</div>--}}

    {{--</div>--}}
    {{--<div class="col-md-12">--}}
    {{--<div class="white-box p-t-10 p-b-10">--}}
    {{--<h3 class="box-title"><i class="icon-graph"></i> @lang('modules.tickets.ticketTrendGraph') </h3>--}}
    {{--<ul class="list-inline text-right">--}}
    {{--<li>--}}
    {{--<h5><i class="fa fa-circle m-r-5" style="color: #4c5667;"></i>@lang('modules.invoices.total')</h5>--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<h5><i class="fa fa-circle m-r-5" style="color: #5475ed;"></i>@lang('modules.issues.resolved')</h5>--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<h5><i class="fa fa-circle m-r-5" style="color: #f1c411;"></i>@lang('modules.tickets.totalUnresolvedTickets')</h5>--}}
    {{--</li>--}}
    {{--</ul>--}}
    {{--<div id="morris-area-chart" style="height: 225px;"></div>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--</div>--}}

    {{--</div>--}}
    {{--</div>--}}

    <div class="row">
        <div class="col-md-12">
            <div class="white-box p-0">


                <div class="row">
                    <div class="col-sm-12 m-t-20">
                        <div class="table-responsive">
                            {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/morrisjs/morris.js') }}"></script>

<script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>

<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}
<script>
    var startDate = '{{ \Carbon\Carbon::createFromFormat('m/d/Y', $startDate)->format('Y-m-d') }}';
    var endDate = '{{ \Carbon\Carbon::createFromFormat('m/d/Y', $endDate)->format('Y-m-d') }}';

    $('.input-daterange-datepicker').daterangepicker({
        buttonClasses: ['btn', 'btn-sm'],
        cancelClass: 'btn-inverse',

        "locale": {
            "applyLabel": "{{ __('app.apply') }}",
            "cancelLabel": "{{ __('app.cancel') }}",
            "daysOfWeek": [
                "{{ __('app.su') }}",
                "{{ __('app.mo') }}",
                "{{ __('app.tu') }}",
                "{{ __('app.we') }}",
                "{{ __('app.th') }}",
                "{{ __('app.fr') }}",
                "{{ __('app.sa') }}"
            ],
            "monthNames": [
                "{{ __('app.january') }}",
                "{{ __('app.february') }}",
                "{{ __('app.march') }}",
                "{{ __('app.april') }}",
                "{{ __('app.may') }}",
                "{{ __('app.june') }}",
                "{{ __('app.july') }}",
                "{{ __('app.august') }}",
                "{{ __('app.september') }}",
                "{{ __('app.october') }}",
                "{{ __('app.november') }}",
                "{{ __('app.december') }}"
            ],
            "firstDay": {{ $global->week_start }},
        }
    });

    $('.input-daterange-datepicker').on('apply.daterangepicker', function (ev, picker) {
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
        showTable();
    });

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    var dataVal = {!! json_encode($chartData) !!};

    var ticketGraph =  Morris.Area({
            element: 'morris-area-chart',
            data: dataVal,
            xkey: 'date',
            ykeys: ['total', 'resolved', 'unresolved'],
            labels: ['Total', 'Resolved', 'Unresolved'],
            pointSize: 3,
            fillOpacity: 0.3,
            pointStrokeColors: ['#4c5667', '#5475ed', '#f1c411'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 3,
            hideHover: 'auto',
            lineColors: ['#4c5667', '#5475ed', '#f1c411'],
            resize: true

    });

    $('#ticket-table').on('preXhr.dt', function (e, settings, data) {
        var agentId = $('#agent_id').val();
        if (agentId == "") {
            agentId = 0;
        }

        var status = $('#status').val();
        if (status == "") {
            status = 0;
        }

        var priority = $('#priority').val();
        if (priority == "") {
            priority = 0;
        }

        var channelId = $('#channel_id').val();
        if (channelId == "") {
            channelId = 0;
        }

        var typeId = $('#type_id').val();
        if (typeId == "") {
            typeId = 0;
        }

        var tagId = $('#tag_id').val();
        if (tagId == "") {
            tagId = 0;
        }

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['agentId'] = agentId;
        data['priority'] = priority;
        data['channelId'] = channelId;
        data['typeId'] = typeId;
        data['tagId'] = tagId;
        data['status'] = status;
    });

    var table;

    function showTable() {

        var agentId = $('#agent_id').val();
        if (agentId == "") {
            agentId = 0;
        }

        var status = $('#status').val();
        if (status == "") {
            status = 0;
        }

        var priority = $('#priority').val();
        if (priority == "") {
            priority = 0;
        }

        var channelId = $('#channel_id').val();
        if (channelId == "") {
            channelId = 0;
        }

        var typeId = $('#type_id').val();
        if (typeId == "") {
            typeId = 0;
        }


        //refresh counts and chart
        var url = '{!!  route('admin.tickets.refreshCount', [':startDate', ':endDate', ':agentId', ':status', ':priority', ':channelId', ':typeId']) !!}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':agentId', agentId);
        url = url.replace(':status', status);
        url = url.replace(':priority', priority);
        url = url.replace(':channelId', channelId);
        url = url.replace(':typeId', typeId);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                $('#totalTickets').html(response.totalTickets);
                $('#closedTickets').html(response.closedTickets);
                $('#openTickets').html(response.openTickets);
                $('#pendingTickets').html(response.pendingTickets);
                $('#resolvedTickets').html(response.resolvedTickets);
                initConter();
                ticketGraph.setData(JSON.parse(response.chartData));
            }
        });

        window.LaravelDataTables["ticket-table"].draw();
    }

    $('#apply-filters').click(function () {
        showTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#filter-form').find('select').selectpicker('render');
        showTable();
    })


    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('ticket-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted ticket!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.tickets.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            window.LaravelDataTables["ticket-table"].draw();
                        }
                    }
                });
            }
        });
    });

    function initConter() {
        $(".counter").counterUp({
            delay: 100,
            time: 1200
        });
    }

    showTable();

    function exportData(){

        var agentId = $('#agent_id').val();
        if (agentId == "") {
            agentId = 0;
        }

        var status = $('#status').val();
        if (status == "") {
            status = 0;
        }

        var priority = $('#priority').val();
        if (priority == "") {
            priority = 0;
        }

        var channelId = $('#channel_id').val();
        if (channelId == "") {
            channelId = 0;
        }

        var typeId = $('#type_id').val();
        if (typeId == "") {
            typeId = 0;
        }


        //refresh counts and chart
        var url = '{!!  route('admin.tickets.export', [':startDate', ':endDate', ':agentId', ':status', ':priority', ':channelId', ':typeId']) !!}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':agentId', agentId);
        url = url.replace(':status', status);
        url = url.replace(':priority', priority);
        url = url.replace(':channelId', channelId);
        url = url.replace(':typeId', typeId);

        window.location.href = url;
    }
</script>
@endpush
