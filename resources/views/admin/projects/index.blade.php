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
            <a href="{{ route('admin.projects.archive') }}"  class="btn btn-outline btn-danger btn-sm">@lang('app.menu.viewArchive') <i class="fa fa-trash" aria-hidden="true"></i></a>
                        
            <a href="{{ route('admin.project-template.index') }}"  class="btn btn-outline btn-primary btn-sm">+ @lang('app.menu.addProjectTemplate')</a>

            <a href="{{ route('admin.projects.create') }}" class="btn btn-outline btn-success btn-sm">+ @lang('modules.projects.addNewProject')</a>
            
            <div class="btn-group dropdown doverlay">
                        <button aria-expanded="true" data-toggle="dropdown" class="dt-button btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md" type="button"><i class="fa fa-upload"></i> &nbsp;Import <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('admin.projects.download-template') }}" target="_blank">
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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <style>
        .custom-action a {
            margin-right: 15px;
            margin-bottom: 15px;
        }
        .custom-action a:last-child {
            margin-right: 0px;
            float: right;
        }

        .dashboard-stats .white-box .list-inline {
            margin-bottom: 0;
        }

        .dashboard-stats .white-box {
            padding: 10px;
        }

        .dashboard-stats .white-box .box-title {
            font-size: 13px;
            text-transform: capitalize;
            font-weight: 300;
        }
		div.table-responsive>div.dataTables_wrapper>div.row:nth-child(1){display:flex;align-items:center;}
        @media all and (max-width: 767px) {
            .custom-action a {
                margin-right: 0px;
            }

            .custom-action a:last-child {
                margin-right: 0px;
                float: none;
            }
        }
    </style>
@endpush

@section('content')

<!--<div @if($totalRecords == 0) style="display: none;"  @endif class="row dashboard-stats front-dashboard d-none">
    <div class="col-md-12 m-t-20">
        <div class="">
            <div class="col-md-4 col-sm-6">
                <h4 class="white-box"><span class="text-dark-" id="totalWorkingDays">{{ $totalProjects }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalProjects')</span></h4>
            </div>
            <div class="col-md-4 col-sm-6">
                <h4 class="white-box"><span class="text-danger-" id="daysPresent">{{ $overdueProjects }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.overDueProjects')</span></h4>
            </div>
            <div class="col-md-4 col-sm-6">
                <h4 class="white-box"><span class="text-warning-" id="daysLate">{{ $notStartedProjects }}</span> <span class="font-12 text-muted m-l-5"> @lang('app.notStarted') @lang('app.menu.projects')</span></h4>
            </div>
            <div class="col-md-4 col-sm-6">
                <h4 class="white-box"><span class="text-success-" id="halfDays">{{ $finishedProjects }}</span> <span class="font-12 text-muted m-l-5"> @lang('modules.tickets.completedProjects')</span></h4>
            </div>
            <div class="col-md-4 col-sm-6">
                <h4 class="white-box"><span class="text-info-" id="absentDays">{{ $inProcessProjects }}</span> <span class="font-12 text-muted m-l-5"> @lang('app.inProgress') @lang('app.menu.projects')</span></h4>
            </div>
            <div class="col-md-4 col-sm-6">
                <h4 class="white-box"><span class="text-primary-" id="holidayDays">{{ $canceledProjects }}</span> <span class="font-12 text-muted m-l-5">@lang('app.canceled') @lang('app.menu.projects')</span></h4>
            </div>
        </div>
    </div>

</div>-->

    <div class="row">
        
        <div class="col-md-12">
            <div class="white-box p-0">
                
                @section('filter-section')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">@lang('app.menu.projects') @lang('app.status')</label>
                            <select class="select2 form-control" data-placeholder="@lang('app.menu.projects') @lang('app.status')" id="status">
                                <option selected value="all">@lang('app.all')</option>
                                <option
                                        value="not started">@lang('app.notStarted')
                                </option>
                                <option
                                        value="in progress">@lang('app.inProgress')
                                </option>
                                <option
                                        value="on hold">@lang('app.onHold')
                                </option>
                                <option
                                        value="canceled">@lang('app.canceled')
                                </option>
                                <option
                                        value="finished">@lang('app.finished')
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">@lang('app.clientName')</label>
                            <select class="select2 form-control" data-placeholder="@lang('app.clientName')" id="client_id">
                                <option selected value="all">@lang('app.all')</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.projects.projectCategory')</label>
                            <select class="select2 form-control" data-placeholder="@lang('modules.projects.projectCategory')" id="category_id">
                                <option selected value="all">@lang('app.all')</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endsection
                
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

                <div @if($totalRecords == 0) style="display: none;"  @endif class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    <div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/project-management.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Project Management</h1>
			<p class="m-b-30">Manage everything relating to your projects within indema. From tasks, expenses, estimates, invoices, PO's, to name a few! Auto-send clients their login to their portal and even add your trade pro's.</p>
			<a href="{{ route('admin.projects.create') }}" class="btn-black">+ @lang('modules.projects.addNewProject')</a>
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
					<h4 class="modal-title">Project Management</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
    
    
    <div class="modal fade bs-modal-md in" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Import Projects</h4>
                </div>
                    <div class="modal-body">
                        <div class="portlet-body">
                        
                        <form method='post' id="importCSVFrm" action='{{ route('admin.projects.import') }}' enctype='multipart/form-data' >
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

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}
<script>
    var table;
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    $('.select2').val('all');
    $(function() {

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

                    var url = "{{ route('admin.projects.archive-delete',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'GET',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                showData();
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
                text: "You will not be able to recover the deleted project!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.projects.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                showData();
                            }
                        }
                    });
                }
            });
        });

        $('#createProject').click(function(){
            var url = '{{ route('admin.projectCategory.create')}}';
            $('#modelHeading').html('Manage Project Category');
            $.ajaxModal('#projectCategoryModal',url);
        })

    });

    function initCounter() {
        $(".counter").counterUp({
            delay: 100,
            time: 1200
        });
    }

    function showData() {
        $('#projects-table').on('preXhr.dt', function (e, settings, data) {
            var status = $('#status').val();
            var clientID = $('#client_id').val();
            var categoryID = $('#category_id').val();
            var teamID = $('#team_id').val();

            data['status'] = status;
            data['client_id'] = clientID;
            data['category_id'] = categoryID;
            data['team_id'] = teamID;
        });
        window.LaravelDataTables["projects-table"].draw();
    }

    $('#status').on('change', function(event) {
        event.preventDefault();
        showData();
    });

    $('#client_id').on('change', function(event) {
        event.preventDefault();
        showData();
    });

    $('#category_id').on('change', function(event) {
        event.preventDefault();
        showData();
    });

    initCounter();


     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/kLalPLwS5Ig" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    });
</script>
@endpush