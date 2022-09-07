@extends('layouts.app')

<style>
	#leads-table_wrapper>.row:first-child{display:flex;flex-wrap:wrap;margin-bottom:150px;}
	#leads-table_wrapper>.row div.col-md-6{width:auto !important;flex:0 0 auto;}
	.add-new-lead-area{z-index:5;margin-bottom:-50px;}
	.row.dashboard-stats.front-dashboard{position:relative;z-index:4;margin:0 !important;}
	.row.dashboard-stats.front-dashboard>div{position:absolute;top:20px;left:0;}
</style>
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
		
        <div class="border-bottom col-xs-12 p-t-10 p-b-10">
            @if(in_array('clients',$modules))
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline btn-success btn-sm" >View clients</a>
            @endif
            @if(in_array('leads',$modules))
                <a href="{{ route('admin.leads.index') }}" class="btn btn-outline btn-success btn-sm" >View Leads</a>
            @endif
            @if(in_array('vendor',$modules))
                <a href="{{ route('admin.vendor.index') }}" class="btn btn-outline btn-success btn-sm" >View Vendors</a>
            @endif
        </div>
        <div @if($totalRecords == 0) style="display: none;"  @endif class="col-xs-6 p-t-10 p-b-10 add-new-lead-area text-right pull-right">
            <a href="{{ route('admin.leads.archive') }}"  class="btn btn-outline btn-danger btn-sm">@lang('app.menu.viewArchive') <i class="fa fa-trash" aria-hidden="true"></i></a>
            
            <a href="{{ route('admin.leads.create') }}" class="btn btn-outline btn-success btn-sm">+ @lang('modules.lead.addNewLead')</a>
        <div class="btn-group dropdown doverlay">
                <button aria-expanded="true" data-toggle="dropdown" class="dt-button btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md" type="button"><i class="fa fa-upload"></i> &nbsp;Import <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">
                    <li>
                        <a href="{{ route('admin.leads.download-template') }}" target="_blank">
                            <i class="fa fa-download"></i> &nbsp;Download Template
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="$('#importCSVModal').modal('show')"><i class="fa fa-file-excel-o"></i> &nbsp;Import CSV</a>
                    </li>
                </ul>
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
@endpush

@section('content')
    <div @if($totalRecords == 0) style="display: none;"  @endif class="row dashboard-stats front-dashboard">
        <div class="col-md-12">
            <div class="white-box p-0 border-0">
                <div class="col-md-4 text-center">
                    <h4 class="white-box">
						<span class="text-dark-">{{ $totalLeads }}</span> 
						<span class="font-12 text-muted- m-l-5"> @lang('modules.dashboard.totalLeads')</span>
					</h4>
                </div>
                <div class="col-md-4 text-center">
                    <h4 class="white-box"><span class="text-info-">{{ $totalClientConverted }}</span> <span
                                class="font-12 text-muted- m-l-5"> @lang('modules.dashboard.totalConvertedClient')</span>
                    </h4>
                </div>
                <div class="col-md-4 text-center">
                    <h4 class="white-box"><span class="text-warning-">{{ $pendingLeadFollowUps }}</span> <span
                                class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalPendingFollowUps')</span>
                    </h4>
                </div>
            </div>
        </div>

    </div>

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

                @section('filter-section')
                <div class="row" id="ticket-filters">

                    <form action="" id="filter-form">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.lead.client')</label>
                                <select class="form-control selectpicker" name="client" id="client" data-style="form-control">
                                    <option value="all">@lang('modules.lead.all')</option>
                                    <option value="lead">@lang('modules.lead.lead')</option>
                                    <option value="client">@lang('modules.lead.client')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">@lang('modules.tickets.chooseAgents')</label>
                                <select class="selectpicker form-control" data-placeholder="@lang('modules.tickets.chooseAgents')" id="agent_id" name="agent_id">
                                    <option value="all">@lang('modules.lead.all')</option>
                                    @foreach($leadAgents as $emp)
                                        <option value="{{ $emp->id }}">{{ ucwords($emp->user->name) }} @if($emp->user->id == $user->id)
                                                (YOU) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.lead.followUp')</label>
                                <select class="form-control selectpicker" name="followUp" id="followUp" data-style="form-control">
                                    <option value="all">@lang('modules.lead.all')</option>
                                    <option value="pending">@lang('modules.lead.pending')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
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
	
<div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/lead-management.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Lead Manegement</h1>
			<p class="m-b-30">Keeping up with leads is crucial to ensure that you don't lose the lead or potential project. Adding tags, seeing an audit trail for the email automation, and even keeping track of the status of the lead are just a few things you can do!</p>
			<a href="{{ route('admin.leads.create') }}" class="btn-black">+ @lang('modules.lead.addNewLead')</a>
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
					<h4 class="modal-title">Lead Manegement</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>
    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="followUpModal" role="dialog" aria-labelledby="myModalLabel"
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
    {{--Ajax Modal Ends--}}
    
    <div class="modal fade bs-modal-md in" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Import Leads</h4>
                </div>
                    <div class="modal-body">
                        <div class="portlet-body">
                        
                        <form method='post' id="importCSVFrm" action='{{ route('admin.leads.import') }}' enctype='multipart/form-data' >
                            {{ csrf_field() }}
                            <div class="form-body">
                                <div class="row ">
                                    <div class="col-xs-12 m-b-10">
                                        <div class="form-group">
                                            <label class="col-xs-3 required">Select File</label>
                                            <div class="col-xs-9">
                                                <input required="" type="file" name="csv_file" id="csv_file" class="form-control">
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

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="//cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

    {!! $dataTable->scripts() !!}
    <script>
        var table;
        function tableLoad() {
            window.LaravelDataTables["leads-table"].draw();
        }
        $(function() {
            tableLoad();
            $('#reset-filters').click(function () {
                $('#filter-form')[0].reset();
                $('#filter-form').find('select').selectpicker('render');
                $.easyBlockUI('#leads-table');
                tableLoad();
                $.easyUnblockUI('#leads-table');
            })
            var table;
            $('#apply-filters').click(function () {
                $('#leads-table').on('preXhr.dt', function (e, settings, data) {
                    var client = $('#client').val();
                    var agent = $('#agent_id').val();
                    var followUp = $('#followUp').val();
                    data['client'] = client;
                    data['agent'] = agent;
                    data['followUp'] = followUp;
                });
                $.easyBlockUI('#leads-table');
                tableLoad();
                $.easyUnblockUI('#leads-table');
            });

            $('body').on('click', '.sa-params', function(){
                var id = $(this).data('user-id');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted lead!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {

                        var url = "{{ route('admin.leads.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.easyBlockUI('#leads-table');
                                    tableLoad();
                                    $.easyUnblockUI('#leads-table');
                                }
                            }
                        });
                    }
                });
            });
        });

       function changeStatus(leadID, statusID){
           var url = "{{ route('admin.leads.change-status') }}";
           var token = "{{ csrf_token() }}";

           $.easyAjax({
               type: 'POST',
               url: url,
               data: {'_token': token,'leadID': leadID,'statusID': statusID},
               success: function (response) {
                   if (response.status == "success") {
                    $.easyBlockUI('#leads-table');
                    tableLoad();
                    $.easyUnblockUI('#leads-table');
                   }
               }
           });
        }

        $('.edit-column').click(function () {
            var id = $(this).data('column-id');
            var url = '{{ route("admin.taskboard.edit", ':id') }}';
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#edit-column-form').html(response.view);
                    $(".colorpicker").asColorPicker();
                    $('#edit-column-form').show();
                }
            })
        })

        function followUp (leadID) {

            var url = '{{ route('admin.leads.follow-up', ':id')}}';
            url = url.replace(':id', leadID);

            $('#modelHeading').html('Add Follow Up');
            $.ajaxModal('#followUpModal', url);
        }
        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        })
        function exportData(){

            var client = $('#client').val();
            var followUp = $('#followUp').val();

            var url = '{{ route('admin.leads.export', [':followUp', ':client']) }}';
            url = url.replace(':client', client);
            url = url.replace(':followUp', followUp);

            window.location.href = url;
        }
		
     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/wFV1r-vEkeU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    }); 
    </script>
@endpush