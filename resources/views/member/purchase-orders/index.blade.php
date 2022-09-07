@extends('layouts.member-app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="border-bottom col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div @if($totalRecords == 0) style="display: none;"  @endif class="border-bottom col-xs-12 p-t-10 p-b-10">       

        <a href="{{ route('member.purchase-orders.create') }}" class="btn btn-outline btn-success btn-sm">+ Create Purchase Order</a>
        
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
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

<div @if($totalRecords == 0) style="display: none;"  @endif class="row">
    <div class="col-md-12">
        <div class="white-box p-0">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default
                footable-loaded footable']) !!}
            </div>
        </div>
    </div>
</div>
<!-- .row -->

<div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/purchase-order.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Purchase Orders</h1>
			<p class="m-b-30">Your purchase orders automatically create when your clients approve the estimates. Manage each PO and even send the PO as an attched PDF to your vendor.</p>
			<a href="{{ route('member.purchase-orders.create') }}" class="btn-black">+ Create Purchase Order </a>
			<a href="javascript:;" onclick="$('#video-modal').modal('show')" class="btn-black">See how it works <i class="fa fa-play"></i></a>
		</div><!--end of col-8-->
		<div class="col-md-12 text-right">
			Have Questions? <a href="mailto:support@indema.co">Contact Support</a>
		</div><!--end of col-12-->
	</div><!--end of row-->
    
    <div class="modal fade bs-modal-md in" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="video-modal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
				<div class="modal-header p-t-15 p-b-15 p-r-15">
					<h4 class="modal-title">Purchase Orders</h4>
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
                            <form action="{{route('member.invoiceFile.store')}}" class="dropzone"
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
    
        var table;
        function tableLoad() {
            window.LaravelDataTables["vendors-table"].draw();
        }
    
    function changeStatus(poID, statusID){
           var url = "{{ route('member.purchase-orders.change-status') }}";
           var token = "{{ csrf_token() }}";

           $.easyAjax({
               type: 'POST',
               url: url,
               data: {'_token': token,'poID': poID,'statusID': statusID},
               success: function (response) {
                   if (response.status == "success") {
                    $.easyBlockUI('#vendors-table');
                    tableLoad();
                    $.easyUnblockUI('#vendors-table');
                   }
               }
           });
        }
        
        
        $('body').on('click', '.archive', function(){
            var id = $(this).data('user-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.archiveMessage')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('messages.confirmArchive')",
                cancelButtonText: "@lang('messages.confirmNoArchive')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('member.purchase-orders.archive-delete',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'GET',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.easyBlockUI('#vendors-table');
                                tableLoad();
                                $.easyUnblockUI('#vendors-table');
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

                    var url = "{{ route('member.purchase-orders.destroy',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.easyBlockUI('#vendors-table');
                                tableLoad();
                                $.easyUnblockUI('#vendors-table');
                            }
                        }
                    });
                }
            });
        });
        
    $('body').on('click', '.sendPDF', function(){
        var poID = $(this).data('po-id');
        var url = "{{ route('member.purchase-orders.sendpdf',':id') }}";
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
    
     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/2GRuwatq-4k" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    }); 

   

</script>
@endpush
