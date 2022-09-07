@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">Purchase Orders</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('member.projects.show_project_menu')
                    <div class="content-wrap" style="min-height: 800px;">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="issues-list-panel">
                                    <div class="white-box p-0">
                                        <h2 class="border-bottom">@lang('app.menu.invoices')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12 border-bottom p-b-10">
                                                <a href="{{ route('member.all-invoices.create') }}" class="btn btn-outline btn-success btn-sm">+ @lang('modules.invoices.addInvoice') </a>
                                            </div>
                                        </div>

                                        <div class="table-responsive m-t-15">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                   id="invoices-table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('app.invoice')</th>
                                                    <th>@lang('app.project')</th>
                                                    <th>@lang('app.client')</th>
                                                    <th>Tags</th>
                                                    <th>@lang('modules.invoices.total')</th>
                                                     <th>@lang('modules.invoices.invoiceDate')</th>
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

<script>
    
    function loadTable(){
        window.LaravelDataTables["invoices-table"].draw();
    }
    
    var table = $('#invoices-table').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('member.invoices-project.data', $project->id) !!}',
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
            {data: 'DT_RowIndex', name: '#'},
            {data: 'invoice_number', name: 'invoice_number'},
            {data: 'project_name', name: 'project_name'},
            {data: 'name', name: 'project.client.name'},
            { data: 'tags', name: 'tags' },
            {data: 'total', name: 'total'},
            {data: 'issue_date', name: 'issue_date'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action'}
        ]
    });
    
     $('body').on('click', '.sendButton', function(){
        var id = $(this).data('invoice-id');
        var url = "{{ route('member.all-invoices.send-invoice',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token},
            success: function (response) {
                if (response.status == "success") {
                    //window.LaravelDataTables["invoices-table"].draw();
                }
            }
        });
    });
    
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
                var url = "{{ route('member.all-invoices.update-status',':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                        if (response.status == "success") {
                            location.reload();
                           // $.unblockUI();
                            //loadTable();
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

                    var url = "{{ route('member.all-credit-notes.convert-invoice',':id') }}";
                    url = url.replace(':id', id);

                    location.href = url;
                }
            });
        });
        
        $(function() {
        $('body').on('click', '.invoice-refund', function () {
            var id = $(this).data('invoice-id');
            var project_id = '{{$project->id}}';
            var url = '{{ route('member.all-invoices.refund', ':id')}}';
            url = url.replace(':id', id);
            url = url+'?project_id='+project_id;
            //$('#modelHeading').html('Refund Invpice');
            $.ajaxModal('#offlinePaymentDetails', url);
        });
    });
         
    
    $('ul.showProjectTabs .projectInvoices').addClass('tab-current');
    
    
    
    
</script>
@endpush
