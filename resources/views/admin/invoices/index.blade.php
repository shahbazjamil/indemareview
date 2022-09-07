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
        <a href="{{ route('admin.invoices.client', ['type' => 'timelog']) }}"
            class="btn btn-outline btn-inverse btn-sm">+ @lang('app.create') @lang('app.timeLog') @lang('app.invoice') </a>

        <a href="{{ route('admin.invoices.client') }}"
            class="btn btn-outline btn-success btn-sm">+ @lang('modules.invoices.addInvoice') </a>
            <a href="{{ route('admin.codeTypes.index') }}" target="_blank"  class="btn btn-outline btn-success btn-sm">+ Add Location Codes </a>
            <a href="{{ route('admin.salescategoryTypes.index') }}" target="_blank"  class="btn btn-outline btn-success btn-sm">+ Add Sales Categories </a>

        {{-- <a href="{{ url('/admin/finance/invoices/createVendor') }}" class="btn btn-outline btn-success btn-sm">Add
            Vendor Invoice <i class="fa fa-plus" aria-hidden="true"></i></a> --}}
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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<style>
    /*#invoices-table_wrapper .dt-buttons{*/
    /*    display: none !important;*/
    /*}*/
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="white-box p-0">

            @section('filter-section')
            <div class="row" id="ticket-filters">

                <form action="" id="filter-form">
                    <div class="col-md-12">
                        <h5>@lang('app.selectDateRange')</h5>
                        <div class="input-daterange input-group" id="date-range">
                            <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                value="" />
                            <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                            <input type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                value="" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h5>@lang('app.project')</h5>
                        <div class="form-group">
                            {{--<label class="control-label">@lang('app.status')</label>--}}
                            <select class="form-control select2" name="projectID" id="projectID"
                                data-style="form-control">
                                <option value="all">@lang('app.all')</option>
                                @forelse($projects as $project)
                                <option value="{{$project->id}}">{{ ucfirst($project->project_name) }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <h5>@lang('app.status')</h5>
                        <div class="form-group">
                            {{--<label class="control-label">@lang('app.status')</label>--}}
                            <select class="form-control selectpicker" name="status" id="status"
                                data-style="form-control">
                                <option value="all">@lang('app.all')</option>
                                <option value="unpaid">@lang('app.unpaid')</option>
                                <option value="paid">@lang('app.paid')</option>
                                <option value="partial">@lang('app.partial')</option>
                            </select>
                        </div>
                    </div>

                    {{-- editing code here by Adil--}}
                    <div class="col-md-12">
                        <h5>Invoice Type</h5>
                        <div class="form-group">
                            <select class="form-control select2" name="invoiceType" id="invoiceTypeId"
                                data-style="form-control">
                                @foreach ($invoiceType as $key => $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <h5 id="heading">@lang('app.client')</h5>
                        <div class="form-group">
                            {{--<label class="control-label">@lang('app.status')</label>--}}
                            <select class="form-control select2" name="clientID" id="clientID"
                                data-style="form-control">
                                <option value="all">@lang('app.all')</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ ucwords($client->name) }}
                                    @if($client->company_name != '') {{ '('.$client->company_name.')' }} @endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-xs-12">&nbsp;</label>
                            <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i
                                    class="fa fa-check"></i> @lang('app.apply')</button>
                            <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i
                                    class="fa fa-refresh"></i> @lang('app.reset')</button>
                        </div>
                    </div>
                </form>
            </div>
            @endsection

            <div @if($totalRecords == 0) style="display: none;"  @endif class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default
                footable-loaded footable']) !!}
            </div>
        </div>
    </div>
</div>
<!-- .row -->
<div  @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/create-invoice.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Easy Invoicing + Payments</h1>
			<p class="m-b-30">Send invoices and accept online payments through Stripe. Auto-sync that to your Quickbooks account with ease, and even auto-convert those invoices into Purchase Orders.</p>
			<a href="{{ route('admin.invoices.client') }}" class="btn-black">+ @lang('modules.invoices.addInvoice') </a>
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
					<h4 class="modal-title">Easy Invoicing + Payments</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="invoiceUploadModal" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase"
                    id="modelHeading">@lang('modules.invoices.uploadInvoice')</span>
            </div>
            <div class="modal-body">
                <div class="row" id="file-dropzone">
                    <div class="row m-b-20" id="file-dropzone">
                        <div class="col-md-12">
                            <form action="{{route('admin.invoiceFile.store')}}" class="dropzone"
                                id="file-upload-dropzone">
                                {{ csrf_field() }}
                                <div class="fallback">
                                    <input name="file" type="file" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
                <button type="button" class="btn blue" data-dismiss="modal">@lang('app.save')</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Ajax Modal Ends--}}
<div class="modal fade bs-modal-md in" id="offlinePaymentDetails" role="dialog" aria-labelledby="myModalLabel"
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
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
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

    $('#invoices-table').on('preXhr.dt', function (e, settings, data) {
        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var status = $('#status').val();
        var projectID = $('#projectID').val();
        var clientID = $('#clientID').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['status'] = status;
        data['projectID'] = projectID;
        data['clientID'] = clientID;
    });

    $('body').on('click', '.reminderButton', function(){
        var id = $(this).data('invoice-id');
        swal({
            title: "Are you sure?",
            text: "Do you want to send reminder to assigned client?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, send it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.all-invoices.payment-reminder',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'GET',
                    url: url,
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

    $('body').on('click', '.verify', function() {
        var id = $(this).data('invoice-id');

        var url = '{{ route('admin.all-invoices.payment-verify', ':id') }}'
        url = url.replace(':id', id);

        $.ajaxModal('#offlinePaymentDetails', url);
    });

    var table;
    $(function() {
        loadTable();
        jQuery('#date-range').datepicker({
            toggleActive: true,
            format: '{{ $global->date_picker_format }}',
            language: '{{ $global->locale }}',
            autoclose: true,
            weekStart:'{{ $global->week_start }}',
        });
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('invoice-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted invoice!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.all-invoices.destroy',':id') }}";
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

        $('body').on('click', '.unpaidAndPartialPaidCreditNote', function(){
            var id = $(this).data('invoice-id');
            swal({
                title: "Are you sure that you want to create the credit note?",
                text: "When creating credit note from non paid invoice, the credit note amount will get applied for this invoice.",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, create it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.all-credit-notes.convert-invoice',':id') }}";
                    url = url.replace(':id', id);

                    location.href = url;
                }
            });
        });

        $('.table-responsive').on('click', '.invoice-upload', function(){
            var invoiceId = $(this).data('invoice-id');
            $('#file-upload-dropzone').prepend('<input name="invoice_id", value="' + invoiceId + '" type="hidden">');
        });
        
        
    $('body').on('click', '.invoice-refund', function () {
        var id = $(this).data('invoice-id');
        var url = '{{ route('admin.all-invoices.refund', ':id')}}';
        url = url.replace(':id', id);
        url = url+"?project_id=''";

        //$('#modelHeading').html('Refund Invpice');
        $.ajaxModal('#offlinePaymentDetails', url);

    });
        
        
    });

    function loadTable(){
        window.LaravelDataTables["invoices-table"].draw();
    }

    function toggleShippingAddress(invoiceId) {
        let url = "{{ route('admin.all-invoices.toggleShippingAddress', ':id') }}";
        url = url.replace(':id', invoiceId);

        $.easyAjax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    loadTable();
                }
            }
        })
    }

    function addShippingAddress(invoiceId) {
        let url = "{{ route('admin.all-invoices.shippingAddressModal', ':id') }}";
        url = url.replace(':id', invoiceId);

        $.ajaxModal('#invoiceUploadModal', url);
    }

    //    $('#file-upload-dropzone').dropzone({
    Dropzone.options.fileUploadDropzone = {
        paramName: "file", // The name that will be used to transfer the file
        dictDefaultMessage: "@lang('modules.projects.dropFile')",
        uploadMultiple: false,
        dictCancelUpload: "Cancel",
        accept: function (file, done) {
            done();
        },
        init: function () {
            this.on('addedfile', function(){
                if(this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
            this.on("success", function (file, response) {
            });
            var newDropzone = this;

            $('#invoiceUploadModal').on('hide.bs.modal', function(){
                newDropzone.removeAllFiles(true);
            });

        }
    };
    //    });

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#projectID').val('all');
        $('#clientID').val('all');
        $('#status').selectpicker('render');
        $('#projectID').select2();
        $('#clientID').select2();

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
        var projectID = $('#projectID').val();

        var url = '{{ route('admin.all-invoices.export', [':startDate', ':endDate', ':status', ':projectID']) }}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':status', status);
        url = url.replace(':projectID', projectID);

        window.location.href = url;
    }


    // Change Status As cancelled
    $('body').on('click', '.sa-cancel', function(){
        var id = $(this).data('invoice-id');
        swal({
            title: "Are you sure?",
            text: "Do you want to change invoice in canceled !",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, do it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                var url = "{{ route('admin.all-invoices.update-status',':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'GET',
                    url: url,
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

    $('body').on('click', '.sendButton', function(){
        var id = $(this).data('invoice-id');
        var url = "{{ route('admin.all-invoices.send-invoice',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token},
            success: function (response) {
                if (response.status == "success") {
                    window.LaravelDataTables["invoices-table"].draw();
                }
            }
        });
    });

    $("#invoiceTypeId").change(function ()
    {
        let val = $("#invoiceTypeId").val();
        $("#invoiceTypeId").empty();
        $('#heading').text(val);
        GetDropDownData(val);
    });

    function GetDropDownData(type)
    {
        $.ajax({
            type: "GET",
            url: "all-invoices/show/" + type,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data)
                    {
                        $.each(data, function (key, value){
                            $("#clientID").append($("<option     />").val(value.id).text(value.name));
                        });
                    },
            failure: function () {
                alert("Failed!");
            }
        });
    }
	
     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/fc_mNPRHbws" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    });   
</script>
@endpush
