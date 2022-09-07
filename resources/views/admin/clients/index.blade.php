@extends('layouts.app')
<style>
	#clients-table_wrapper .row:first-child{display:flex;flex-wrap:wrap;}
	#clients-table_wrapper .row div.col-md-6{width:auto !important;flex:0 0 auto;}
	.add-new-client-area{possition:relative;z-index:15;margin-bottom:-42px;border-top:1px solid rgb(227, 227, 227);}
</style>
@section('page-title')
    <div class="row bg-title">

        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i>Contacts <!--{ { __($pageTitle) } }-->
                <!--<span @if($totalRecords == 0) style="display: none;"  @endif xt-info- b-l p-l-10 m-l-5">{{ $totalClients }}</span> 
                <span @if($totalRecords == 0) style="display: none;"  @endif class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalClients')</span>-->
            </h4>
        </div></div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="border-bottom col-xs-12 p-b-10">
            
            @if(in_array('clients',$modules))
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline btn-success btn-sm" >View clients</a>
            @endif
            @if(in_array('leads',$modules))
                <a href="{{ route('admin.leads.index') }}" class="btn btn-outline btn-success btn-sm" >View Leads</a>
            @endif
            @if(in_array('vendor',$modules))
                <a href="{{ route('admin.vendor.index') }}" class="btn btn-outline btn-success btn-sm" >View Vendors</a>
            @endif
            
<!--            <a href="#clients" class="btn btn-outline btn-success btn-sm" aria-controls="clients" role="tab" data-toggle="tab">View clients</a>
            <a href="#leads" class="btn btn-outline btn-success btn-sm" aria-controls="leads" role="tab" data-toggle="tab">View Leads</a>
            <a href="#vendors" class="btn btn-outline btn-success btn-sm" aria-controls="vendors" role="tab" data-toggle="tab">View Vendors</a>-->
        </div>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="clients">
	
        <div @if($totalRecords == 0) style="display: none;"  @endif class="col-xs-6 p-t-10 p-b-10 text-right add-new-client-area pull-right">
            <a href="{{ route('admin.clients.create') }}"
            class="btn btn-outline btn-success btn-sm" id="addclient">+ @lang('modules.client.addNewClient')</a>
            <div class="btn-group dropdown doverlay" id="import">
                <button aria-expanded="true" data-toggle="dropdown" class="dt-button btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md" type="button"><i class="fa fa-upload"></i> &nbsp;Import <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">
                    <li>
                        <a href="{{ route('admin.clients.download-template') }}" target="_blank">
                            <i class="fa fa-download"></i> &nbsp;Download Template
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="$('#importCSVModal').modal('show')"><i class="fa fa-file-excel-o"></i> &nbsp;Import CSV</a>
                    </li>
                </ul>
            </div>
            <ol class="breadcrumb d-none">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
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
<div class="row"  id="ticket-filters">
    
    <form action="" id="filter-form">
        <div class="col-md-12">
            <h5 >@lang('app.selectDateRange')</h5>
            <div class="input-daterange input-group" id="date-range">
                <input type="text" class="form-control" autocomplete="off" id="start-date" placeholder="@lang('app.startDate')"
                       value=""/>
                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                <input type="text" class="form-control" id="end-date"  autocomplete="off" placeholder="@lang('app.endDate')"
                       value=""/>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <h5 >@lang('app.status')</h5>
                <select class="form-control" name="status" id="status" data-style="form-control">
                    <option value="all">@lang('modules.client.all')</option>
                    <option value="active">@lang('app.active')</option>
                    <option value="deactive">@lang('app.inactive')</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <h5 >@lang('app.client')</h5>
                <select class="form-control select2" name="client" id="client" data-style="form-control">
                    <option value="all">@lang('modules.client.all')</option>
                    @forelse($clients as $client)
                        <option value="{{$client->id}}">{{ $client->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group p-t-10">
                <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
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
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    <div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/client-manage-img.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Client Manegement</h1>
			<p class="m-b-30">Managing your clients is easy in indema! When you add a client, you can then see information about them, an email audit trail, project they hired you for, and even past due invoices and payments.</p>
			<a href="{{ route('admin.clients.create') }}" class="btn-black">+ @lang('modules.client.addNewClient')</a>
			<a href="javascript:;" onclick="$('#video-modal').modal('show')" class="btn-black">See how it works <i class="fa fa-play"></i></a>
		</div><!--end of col-5-->
		<div class="col-md-12 text-right">
			Have Questions? <a href="mailto:support@indema.co">Contact Support</a>
		</div><!--end of col-12-->
	</div><!--end of row-->
    
	</div><!--end of clients-->
    <div role="tabpanel" class="tab-pane" id="leads">
	Leads
	
	</div>
    <div role="tabpanel" class="tab-pane" id="vendors">Venders</div>

</div>
    <div class="modal fade bs-modal-md in" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="video-modal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
				<div class="modal-header p-t-15 p-b-15 p-r-15">
					<h4 class="modal-title">Client Manegement</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-md in" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Import Clients</h4>
                </div>
                    <div class="modal-body">
                        <div class="portlet-body">
                        
                        <form method='post' id="importCSVFrm" action='{{ route('admin.clients.import') }}' enctype='multipart/form-data' >
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
    
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-lg in" id="clientLoginModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingS"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}

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

                        var url = "{{ route('admin.clients.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.easyBlockUI('#clients-table');
                                    window.LaravelDataTables["clients-table"].draw();
                                    $.easyUnblockUI('#clients-table');
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
            $('#clients-table').on('preXhr.dt', function (e, settings, data) {
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
            $.easyBlockUI('#clients-table');
            window.LaravelDataTables["clients-table"].draw();
            $.easyUnblockUI('#clients-table');
        });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('#status').val('all');
            $('.select2').val('all');
            $('#filter-form').find('select').select2();

            $.easyBlockUI('#clients-table');
            window.LaravelDataTables["clients-table"].draw();
            $.easyUnblockUI('#clients-table');
        })

        function exportData(){

            var client = $('#client').val();
            var status = $('#status').val();

            var url = '{{ route('admin.clients.export', [':status', ':client']) }}';
            url = url.replace(':client', client);
            url = url.replace(':status', status);

            window.location.href = url;
        }
        
    $('#clients-table').on('click', '.login-view', function () {
        var id = $(this).data('user-id');
        var url = "{{ route('admin.clients.login-view',':id') }}";
        url = url.replace(':id', id);
        
        $('#modelHeading').html('Client Login');
        $.ajaxModal('#clientLoginModal', url);
    })
     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/fwXnoBcZ1VU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    });  
    </script>
@endpush