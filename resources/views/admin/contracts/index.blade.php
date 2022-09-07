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
            <a href="{{ route('admin.contract-template.index') }}"  class="btn btn-outline btn-primary btn-sm">+ @lang('app.contractTemplate') </a>
            <a href="{{ route('admin.contracts.create') }}" class="btn btn-outline btn-success btn-sm">+ @lang('modules.contracts.createContract')</a>

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
    <div @if($totalRecords == 0) style="display: none;"  @endif class="row dashboard-stats front-dashboard">
        <div class="col-md-12 m-b-30">
            <div class="white-box p-0 border-0">
                <div class="col-sm-4 text-center">
                    <h4 class="white-box"><span class="text-dark-" id="totalProjects">{{ $contractCounts }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.contracts.totalContracts')</span></h4>
                </div>
                <div class="col-sm-4 text-center">
                    <h4 class="white-box"><span class="text-warning-" id="daysPresent">{{ $aboutToExpireCounts }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.contracts.aboutToExpire')</span></h4>
                </div>
                <div class="col-sm-4 text-center">
                    <h4 class="white-box"><span class="text-danger-" id="overdueProjects">{{ $expiredCounts }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.contracts.expired')</span></h4>
                </div>
                
            </div>
        </div>

    </div>

    <div @if($totalRecords == 0) style="display: none;"  @endif class="row">
        <div class="col-md-12">
            <div class="white-box p-0">
                
                @section('filter-section')                    
                    <div class="row"  id="ticket-filters">
                        
                        <form action="" id="filter-form">
                            <div class="col-md-12">
                                <h5 >@lang('app.selectDateRange')</h5>
                                <div class="input-daterange input-group" id="date-range">
                                    <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                        value=""/>
                                    <span style="display: none;" class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                                    <input style="display: none;" type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                        value=""/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5 >@lang('app.client')</h5>
                                <div class="form-group">
                                    <div>
                                        <select class="select2 form-control" data-placeholder="@lang('app.client')" name="client" id="clientID">
                                            <option value="all">@lang('app.all')</option>
                                            @foreach($clients as $client)

                                                <option value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                    <label class="control-label col-xs-12">&nbsp;</label>
                                    <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endsection

                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    <div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content">
		<div class="col-md-7"><img src="{{ asset('img/create-contract.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-5">
			<h1 class="page-title m-b-30">Contracts + Simple E-Signing</h1>
			<p class="m-b-30">In a few simple steps, use a standardize contract or create a contract template for different types of agreements. Enter the client's information, and send for it to be e-sgined.</p>
			<a href="{{ route('admin.contracts.create') }}" class="btn-black">+ @lang('modules.contracts.createContract')</a>
			<a href="javascript:;" onclick="$('#video-modal').modal('show')" class="btn-black">See how it works <i class="fa fa-play"></i></a>
		</div><!--end of col-5-->
		<div class="col-md-12 text-right">
			Have Questions? <a href="mailto:support@indema.co">Contact Support</a>
		</div><!--end of col-12-->
	</div><!--end of row-->
    
    <div class="modal fade bs-modal-md in" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="video-modal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
				<div class="modal-header p-t-15 p-b-15 p-r-15">
					<h4 class="modal-title">Contracts + Simple E-Signing</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>

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
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}
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
            weekStart:'{{ $global->week_start }}',
            language: '{{ $global->locale }}',
            autoclose: true,
            format: '{{ $global->date_picker_format }}',
        });

        loadTable();

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('contract-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted contract!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.contracts.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                loadTable();
                            }
                        }
                    });
                }
            });
        });

    });


    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })
    $('#contracts-table').on('preXhr.dt', function (e, settings, data) {
        var startDate = $('#start-date').val();
        if (startDate == '') {
            startDate = null;
        }
        var endDate = $('#end-date').val();
        if (endDate == '') {
            endDate = null;
        }
        var clientID = $('#clientID').val();
        var contractType = $('#contractType').val();
        var status = $('#status').val();
        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['status'] = status;
        data['clientID'] = clientID;
        data['contractType'] = contractType;
    });

    $('#apply-filters').click(function () {
        loadTable();
    });

    function loadTable(){
        window.LaravelDataTables["contracts-table"].draw();
    }

    $('#reset-filters').click(function () {
        console.log('hii from ');
        $('#filter-form')[0].reset();
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        loadTable();
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

        var status = $('#status').val();

        var url = '{{ route('admin.estimates.export', [':startDate', ':endDate', ':status']) }}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':status', status);

        window.location.href = url;
    }

     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/_7bNGezWiqM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    });   
</script>
@endpush