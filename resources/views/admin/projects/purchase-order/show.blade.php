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
                                        <h2 class="border-bottom p-b-10">Purchase Orders</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12 border-bottom p-b-10">
                                                
                                                <a href="{{ route('admin.purchase-orders.archive', $project->id) }}"  class="btn btn-outline btn-danger btn-sm">@lang('app.menu.viewArchive') <i class="fa fa-trash" aria-hidden="true"></i></a>
                                                <a href="{{ route('admin.purchase-orders.create', ['project_id' => $project->id]) }}" class="btn btn-success btn-outline"><i class="fa fa-flag"></i> Create Purchase Order</a>
                                                <a href="{{ route('admin.purchase-order-settings.index') }}" target="_blank"  class="btn btn-outline btn-success btn-sm">+ @lang('modules.tasks.addStatus') </a>
                                            </div>
                                        </div>

                                        <div class="table-responsive m-t-10">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                   id="timelog-table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item</th>
                                                    <th>Vendor</th>
                                                    <th>Project Name</th>
                                                    <th>Document Tags</th>
                                                    <th>PO Date</th>
                                                    <th>Order Status</th>
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

@endsection

@push('footer-script')

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script>
    var table = $('#timelog-table').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.purchase-orders-project.data', $project->id) !!}',
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
            {data: 'purchase_order_number', name: 'purchase_order_number'},
            {data: 'vendor_name', name: 'vendor_name'},
            {data: 'project_name', name: 'project_name'},
            {data: 'document_tags', name: 'document_tags'},
            {data: 'purchase_order_date', name: 'purchase_order_date'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action'}
        ]
    });
    
    function loadTable(){
        window.LaravelDataTables["timelog-table"].draw();
    }
    
    function changeStatus(poID, statusID){
           var url = "{{ route('admin.purchase-orders.change-status') }}";
           var token = "{{ csrf_token() }}";

           $.easyAjax({
               type: 'POST',
               url: url,
               data: {'_token': token,'poID': poID,'statusID': statusID},
               success: function (response) {
                   if (response.status == "success") {
//                    $.easyBlockUI('#vendors-table');
//                    tableLoad();
//                    $.easyUnblockUI('#vendors-table');
                   }
               }
           });
        }
    



//    $('#save-form').click(function () {
//        $.easyAjax({
//            url: '{{route('admin.milestones.store')}}',
//            container: '#logTime',
//            type: "POST",
//            data: $('#logTime').serialize(),
//            success: function (data) {
//                if (data.status == 'success') {
//                    $('#logTime').trigger("reset");
//                    $('#logTime').toggleClass('hide', 'show');
//                    table._fnDraw();
//                }
//            }
//        })
//    });

//    $('#show-add-form, #close-form').click(function () {
//        $('#logTime').toggleClass('hide', 'show');
//    });


$('body').on('click', '.archive', function(){
            var id = $(this).data('user-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "Do you want to archive this purchase order?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.confirmArchive')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.purchase-orders-project.archive-delete',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'GET',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                //$.unblockUI();
                                //loadTable();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
        
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('user-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted purchase order!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.purchase-orders-project.destroy',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                //$.unblockUI();
                                //loadTable();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
        
        
        
    $('body').on('click', '.sendPDF', function(){
        var poID = $(this).data('po-id');
        var url = "{{ route('admin.purchase-orders.sendpdf',':id') }}";
        url = url.replace(':id', poID);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token},
            success: function (response) {
                if (response.status == "success") {
                }
            }
        });
    });
  
    $('ul.showProjectTabs .projectPurchaseOrders').addClass('tab-current');
    
</script>
@endpush
