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
                                        <h2 class="border-bottom p-b-10">@lang('app.menu.estimates')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12 border-bottom p-b-10">
                                                <a href="javascript:;" id="show-estimate-modal" class="btn btn-outline btn-success btn-sm">+ @lang('modules.estimates.createEstimate')</a>
                                                <a href="{{ route('admin.codeTypes.index') }}" target="_blank"  class="btn btn-outline btn-success btn-sm">+ Add Location Codes </a>
                                                <a href="{{ route('admin.salescategoryTypes.index') }}" target="_blank"  class="btn btn-outline btn-success btn-sm">+ Add Sales Categories </a>
                                                
                                            </div>
                                        </div>
                                        <div style="display: none;" id="add-estimate-content"> </div>

                                        <div class="table-responsive m-t-10" id="estimate-listing">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                   id="timelog-table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('app.estimate')#</th>
<!--                                                    <th>@lang('app.client')</th>-->
                                                    <th>Tags</th>
                                                    <th>@lang('modules.invoices.total')</th>
                                                    <th>Valid Until</th>
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
    
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-lg in" id="add-estimate-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingE"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Save changes</button>
                </div>
            </div>
             /.modal-content 
        </div>
         /.modal-dialog 
    </div>
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script>
    
    $('#show-estimate-modal').click(function(){
        var url = '{{ route('admin.estimates.createEstimate', $project->id)}}';
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#add-estimate-content').html(response.html);
                }
            }
        });
        $('#add-estimate-content').toggle();
        $('#estimate-listing').toggle();
        
    });
    
    
    
//    $('#show-estimate-modal').click(function(){
//        var url = '{{ route('admin.estimates.createEstimate', $project->id)}}';
//        $('#modelHeadingE').html('Add Estimate');
//        $.ajaxModal('#add-estimate-modal',url);
//    })

function loadTable (){
        window.LaravelDataTables["timelog-table"].draw();
    }
    
    var table = $('#timelog-table').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.estimates-project.data', $project->id) !!}',
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
            {data: 'original_estimate_number', name: 'original_estimate_number'},
//            {data: 'name', name: 'name'},
            {data: 'tags', name: 'tags'},
            
            {data: 'total', name: 'total'},
            {data: 'valid_till', name: 'valid_till'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action'}
        ]
    });
    
    $('body').on('click', '.sendButton', function(){
        var id = $(this).data('estimate-id');
        var url = "{{ route('admin.estimates.send-estimate',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token},
            success: function (response) {
                if (response.status == "success") {
                    loadTable();
                }
            }
        });
    });
    
      $('body').on('click', '.sa-params', function(){
            var id = $(this).data('estimate-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted estimate!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.estimates.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                location.reload();
                                //$.unblockUI();
                                //loadTable();
                            }
                        }
                    });
                }
            });
        });
    
    $('ul.showProjectTabs .projectEstimates').addClass('tab-current');
    
    
    
    
</script>
@endpush
