@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-xs-12">
            <div class="col-md-12 pull-right hidden-xs hidden-sm">
                @if ($company_details->count() > 1)
                    <select class="selectpicker company-switcher margin-right-auto" data-width="fit" name="companies" id="companies">
                        @foreach ($company_details as $company_detail)
                            <option {{ $company_detail->company->id === $global->id ? 'selected' : '' }} value="{{ $company_detail->company->id }}">{{ ucfirst($company_detail->company->company_name) }}</option>
                        @endforeach
                    </select>
                @endif
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="white-box p-0">
        

                @section('filter-section')                    
                    <div class="row" id="ticket-filters">
                       
                        <form action="" id="filter-form">
                            <div class="col-md-12">
                                <h5 >@lang('app.selectDateRange')</h5>
                                <div class="input-daterange input-group" id="date-range">
                                    <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                        value=""/>
                                    <span style="display:none" class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                                    <input style="display:none" type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                        value=""/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5 >@lang('modules.contracts.contractType')</h5>
                                <div class="form-group">
                                    <div>
                                        <select class="select2 form-control" data-placeholder="@lang('modules.contracts.contractType')" name="contractType" id="contractType">
                                            <option value="all">@lang('app.all')</option>
                                            @foreach($contractType as $type)

                                                <option value="{{ $type->id }}">{{ ucwords($type->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-t-10">
                                    <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endsection

                <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="invoice-table">
                        <thead>
                        <tr>
                            <th>@lang('app.id')</th>
                            <th>@lang('app.subject')</th>
                            <th>@lang('modules.contracts.contractType')</th>
                            <th>@lang('app.amount')</th>
                            <th>@lang('app.startDate')</th>
<!--                            <th>@lang('app.endDate')</th>-->
                            <th>@lang('modules.estimates.signature')</th>
                            <th>@lang('app.action')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@if($global->locale == 'en')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
@else
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
@endif
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script>
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    var table;
    $(function() {
        jQuery('#date-range').datepicker({
            toggleActive: true,
            language: '{{ $global->locale }}',
            autoclose: true,
            format: '{{ $global->date_picker_format }}',
        });

        loadTable();

    });

    function loadTable () {

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var contractType = $('#contractType').val();


        table = $('#invoice-table').dataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!! route('client.contracts.data') !!}?startDate=' + startDate + '&endDate=' + endDate + '&contractType=' + contractType,
            "order": [[ 0, "desc" ]],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },

            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'subject', name: 'subject' },
                { data: 'contract_type.name', name: 'contract_type.name' },
                { data: 'amount', name: 'amount' },
                { data: 'start_date', name: 'start_date' },
//                { data: 'end_date', name: 'end_date' },
                { data: 'signature', name: 'signature', sortable:false, searchable:false},
                { data: 'action', name: 'action', width: '5%' }
            ]
        });

    }

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        loadTable();
    })

    function exportData(){

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var status = $('#status').val();

        var url = '{{ route('admin.estimates.export', [':startDate', ':endDate', ':status']) }}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':status', status);

        window.location.href = url;
    }

</script>
@endpush