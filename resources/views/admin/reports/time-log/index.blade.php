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
                <li><a href="{{ route('admin.dashboard') }}">@lang("app.menu.home")</a></li>
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

<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <style>
/*        #all-time-logs-table_wrapper .dt-buttons{
            display: none !important;
        }*/
    </style>
@endpush

@section('content')



    @section('filter-section')
        <div class="row border-bottom">
            {!! Form::open(['id'=>'filter-form','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="col-md-12">
                <div class="example">
                    <h5 class="box-title">@lang("app.selectDateRange")</h5>

                    <div class="input-daterange input-group" id="date-range">
                        <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                               value="{{ $fromDate->format($global->date_format) }}"/>
                        <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                        <input type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                               value="{{ $toDate->format($global->date_format) }}"/>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <h5 class="box-title">@lang('app.selectTask')</h5>
        
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <select class="select2 form-control" data-placeholder="@lang('app.selectTask')" id="task_id">
                                <option value="">@lang('app.all')</option>
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
                    <button type="button" id="filter-results" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    @endsection


    <div style="display:none;" class="row">
        <div class="col-lg-12 border-bottom">
            <div class="white-box p-0">
                <div id="morris-bar-chart"></div>
            </div>
        </div>

    </div>

    <div class="white-box p-0">

        <div class="row">
            <div class="table-responsive m-t-10">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
            </div>
        </div>
    </div>

@endsection

@push('footer-script')


<script src="{{ asset('plugins/bower_components/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/morrisjs/morris.js') }}"></script>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}
<script>

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: '{{ $global->date_picker_format }}',
    });

    $('#filter-results').click(function () {
        var token = '{{ csrf_token() }}';
        var url = '{{ route('admin.time-log-report.store') }}';

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var projectID = $('#project_id').val();

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {_token: token, startDate: startDate, endDate: endDate, projectId: projectID},
            success: function (response) {
                if(response.status == 'success'){
                    chartData = $.parseJSON(response.chartData);
                    $('#morris-bar-chart').html('');
                    $('#morris-bar-chart').empty();
                    barChart();
                    showTable();
                }
            }
        });
    })

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#status').val('all');
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        $('#filter-results').trigger("click");
    })

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
        var employee = $('#employee').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['projectId'] = projectID;
        data['employee'] = employee;
    });

    function showTable() {
        window.LaravelDataTables["all-time-logs-table"].draw();
    }

</script>

<script>
    var chartData = {!!  $chartData !!};
    function barChart() {
        if (chartData != '[]' && chartData != '') {
            Morris.Bar({
                element: 'morris-bar-chart',
                data: chartData,
                xkey: 'date',
                ykeys: ['total_hours'],
                labels: ['Hours Logged'],
                barColors:['#3594fa'],
                hideHover: 'auto',
                gridLineColor: '#ccccccc',
                resize: true
            });
        }

    }

    @if($chartData != '[]')
    barChart();
    @endif


</script>
@endpush