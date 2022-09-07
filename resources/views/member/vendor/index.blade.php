@extends('layouts.member-app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="border-bottom col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}
            <span @if($totalRecords == 0) style="display: none;"  @endif class="text-info- b-l p-l-10 m-l-5">{{ $totalVendors }}</span>
        </h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div @if($totalRecords == 0) style="display: none;"  @endif class="border-bottom col-xs-12 p-t-10 p-b-10">
        <a  href="{{ route('member.vendor.create') }}" class="btn btn-outline btn-success btn-sm">+ Add New Vendor </a>
<!--        <div style="display: none;" class="btn-group dropdown doverlay">
                <button aria-expanded="true" data-toggle="dropdown" class="dt-button btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md" type="button"><i class="fa fa-upload"></i> &nbsp;Import <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">
                    <li>
                        <a href="{{ route('member.vendor.download-template') }}" target="_blank">
                            <i class="fa fa-download"></i> &nbsp;Download Template
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="$('#importCSVModal').modal('show')"><i class="fa fa-file-excel-o"></i> &nbsp;Import CSV</a>
                    </li>
                </ul>
        </div>-->
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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">


@endpush

@section('filter-section')
<div class="row" id="ticket-filters">
    <form action="" id="filter-form">
        <div class="col-md-12">
            <h5>@lang('app.selectDateRange')</h5>
            <div class="input-daterange input-group" id="date-range">
                <input type="text" class="form-control" autocomplete="off" id="start-date"
                    placeholder="@lang('app.startDate')" value="" />
                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                <input type="text" class="form-control" id="end-date" autocomplete="off"
                    placeholder="@lang('app.endDate')" value="" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <h5>Status</h5>
                <select class="form-control" name="status" id="status" data-style="form-control">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">InActive</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group p-t-10">
                <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i>
                    @lang('app.apply')</button>
                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i
                        class="fa fa-refresh"></i> @lang('app.reset')</button>
            </div>
        </div>
    </form>
</div>
@endsection


@section('content')
<div @if($totalRecords == 0) style="display: none;"  @endif class="row">
    <div class="col-md-12">
         @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                   <i class="fa fa-check"></i> {!! $message !!}
                </div>
                <?php Session::forget('success');?>
            @endif
            
            @if ($message = Session::get('error'))
                <div class="custom-alerts alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php Session::forget('error');?>
            @endif
        <div class="white-box p-0">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default
                footable-loaded footable']) !!}
            </div>
        </div>
    </div>

    {{-- Email Modal --}}
    <div id="emailModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="emailModalHeader"></h3>
                </div>
                <div class="modal-body">
                    <form method="post" name="email_form" action="{{ route('member.vendor.sendEmail') }}"
                        onsubmit="return SendEmailSMTP()" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input id="send_Email_To" name="send_Email_To" hidden>
                        <div class="form-group">
                            <label for="subject" class="col-form-label">Subject:</label>
                            <input class="form-control" id="subject" name="subject" required />
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Message:</label>
                            <textarea class="form-control" id="message-text" rows="5" name="messageText"
                                required></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="modal-dismiss" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Email Modal --}}
</div>

<div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content">
		<div class="col-md-7"><img src="{{ asset('img/vendor-management.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-5">
			<h1 class="page-title m-b-30">Vendor Management</h1>
			<p class="m-b-30">Vendor management is really important when procuring products for your projects. Keep everything centralized down to the login credentials to the vendor client portal!</p>
			<a href="{{ route('member.vendor.create') }}" class="btn-black">+ Add New Vendor</a>
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
					<h4 class="modal-title">Vendor Management</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>


    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="editVendorModal" role="dialog" aria-labelledby="myModalLabel"
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
    
        <div class="modal fade bs-modal-md in" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Import Vendors</h4>
                </div>
                    <div class="modal-body">
                        <div class="portlet-body">
                        
                        <form method='post' id="importCSVFrm" action='{{ route('member.vendor.import') }}' enctype='multipart/form-data' >
                            {{ csrf_field() }}
                            <div class="form-body">
                                <div class="row ">
                                    <div class="col-xs-12 m-b-10">
                                        <div class="form-group">
                                            <label class="col-xs-3">Select File</label>
                                            <div class="col-xs-9">
                                                <input type="file" name="csv_file" id="csv_file" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void()"  type="button" class="btn btn-success" onclick="$('#importCSVFrm').submit()">Import</a>
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
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
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
            language: '{{ $global->locale }}',
            autoclose: true,
            weekStart:'{{ $global->week_start }}',
        });

        var table;
        $(function () {
            $('body').on('click', '.sa-params', function () {
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
                        var url = "{{ route('member.vendor.destroy',':id') }}";
                        url = url.replace(':id', id);
                        var token = "{{ csrf_token() }}";
                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.easyBlockUI('#vendors-table');
                                    window.LaravelDataTables["vendors-table"].draw();
                                    $.easyUnblockUI('#vendors-table');
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

        $('#apply-filters').click(function () {
            $('#vendors-table').on('preXhr.dt', function (e, settings, data) {
                var startDate = $('#start-date').val();
                if (startDate == '') {
                    startDate = null;
                }
                var endDate = $('#end-date').val();

                if (endDate == '') {
                    endDate = null;
                }

                var status = $('#status').val();
                var client = $('#client').val();
                data['startDate'] = startDate;
                data['endDate'] = endDate;
                data['status'] = status;
                data['client'] = client;
            });
            $.easyBlockUI('#vendors-table');
            window.LaravelDataTables["vendors-table"].draw();
            $.easyUnblockUI('#vendors-table');
        });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('#status').val('all');
            $('.select2').val('all');
            $('#filter-form').find('select').select2();

            $.easyBlockUI('#vendors-table');
            window.LaravelDataTables["vendors-table"].draw();
            $.easyUnblockUI('#vendors-table');
        })

        function exportData(){
            var client = $('#client').val();
            var status = $('#status').val();

            var url = '{{ route('member.vendor.export', [':status', ':client']) }}';
            url = url.replace(':client', client);
            url = url.replace(':status', status);

            window.location.href = url;
        }

        function sendEmail(email){
                //     var url = '{{ route('member.vendor.showVendor', [':UrlId']) }}';
                //     url = url.replace(':UrlId', id);
                //     $.ajax({
                //     type: "GET",
                //     url: url,
                //     success: function (data) {
                //         $('#emailModalHeader').text('Email To ' + data.vendor_email);
                //         $('#send_Email_To').val(data.vendor_email);
                //         $('#emailModal').modal('show');
                //     }
                // });
                $('#emailModalHeader').text('Email To ' + email);
                $('#send_Email_To').val(email);
                $('#emailModal').modal('show');
        }

        function SendEmailSMTP(){
            // alert('Email Sent.');
        }

        $('#modal-dismiss').click(function()
        {
            document.getElementById("subject").value = "";
            document.getElementById("message-text").value = "";
        });
        
        $('body').on('click', '.vendor-detail', function () {
            var id = $(this).data('vendor-id');
            var url = '{{ route('member.vendor.showVendor', ":id")}}';
            url = url.replace(':id', id);
            $('#modelHeading').html('Vendor Details');
            $.ajaxModal('#editVendorModal',url);
        });
        
         $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/iwvDmX_ycXU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    }); 

</script>
@endpush