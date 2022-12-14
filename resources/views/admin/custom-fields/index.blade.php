@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
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

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('app.menu.customFields')</div>

                <div class="vtabs customvtab m-t-10">

                    @include('sections.admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="btn-group">
                                            <button id="add-field" class="btn btn-success btn-outline"><i class="fa fa-plus"></i> @lang('modules.customFields.addField')
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            
                            
                           
                            
                            
                            <hr>
                            <div style="border-bottom:none;padding-bottom : 0px" class="panel-heading">Global Client Custom Field</div>
                                <div class="table-responsive">
                                    <table class="table  table-bordered table-hover table-checkable order-column dataTable no-footer">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('modules.customFields.label')</th>
                                            <th>Type</th>
                                            <th>@lang('app.action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($clientCustomFields as $key=> $field)
                                            <tr>
                                                <td>{{ ($key+1) }}</td>
                                                <td>{{ $field->label }}</td>
                                                <td>{{ $field->type }}</td>
                                                <td>
                                                    
                                                <a href="javascript:;" class="btn btn-info btn-outline edit-custom-field" data-toggle="tooltip" data-user-id="{{$field->id}}" data-original-title="Edit">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-danger btn-circle sa-params" data-toggle="tooltip" data-user-id="{{$field->id}}" data-original-title="Delete">
                        <i class="fa fa-times" aria-hidden="true"></i></a>
                                                   
                                                </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td>
                                                        No Client Custom Field Added.
                                                    </td>
                                                </tr>
                                            @endforelse
                                            
                                        </tbody>
                                    </table>
                                </div>
                            <div class="clearfix"></div>
                            
                            
                            <hr>
                            <div style="border-bottom:none;padding-bottom : 0px" class="panel-heading">Global Vendor Custom Field</div>
                                <div class="table-responsive">
                                    <table class="table  table-bordered table-hover table-checkable order-column dataTable no-footer">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('modules.customFields.label')</th>
                                            <th>Type</th>
                                            <th>@lang('app.action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($vendorCustomFields as $key=> $field)
                                            <tr>
                                                <td>{{ ($key+1) }}</td>
                                                <td>{{ $field->label }}</td>
                                                <td>{{ $field->type }}</td>
                                                <td>
                                                    
                                                <a href="javascript:;" class="btn btn-info btn-outline edit-custom-field" data-toggle="tooltip" data-user-id="{{$field->id}}" data-original-title="Edit">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-danger btn-circle sa-params" data-toggle="tooltip" data-user-id="{{$field->id}}" data-original-title="Delete">
                        <i class="fa fa-times" aria-hidden="true"></i></a>
                                                   
                                                </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td>
                                                        No Vendor Custom Field Added.
                                                    </td>
                                                </tr>
                                            @endforelse
                                            
                                        </tbody>
                                    </table>
                                </div>
                            <div class="clearfix"></div>
                            
                            
                            <hr>
                            <div style="border-bottom:none;padding-bottom : 0px" class="panel-heading">Global Lead Custom Field</div>
                                <div class="table-responsive">
                                    <table class="table  table-bordered table-hover table-checkable order-column dataTable no-footer">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('modules.customFields.label')</th>
                                            <th>Type</th>
                                            <th>@lang('app.action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($leadCustomFields as $key=> $field)
                                            <tr>
                                                <td>{{ ($key+1) }}</td>
                                                <td>{{ $field->label }}</td>
                                                <td>{{ $field->type }}</td>
                                                <td>
                                                    
                                                <a href="javascript:;" class="btn btn-info btn-outline edit-custom-field" data-toggle="tooltip" data-user-id="{{$field->id}}" data-original-title="Edit">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="btn btn-danger btn-circle sa-params" data-toggle="tooltip" data-user-id="{{$field->id}}" data-original-title="Delete">
                        <i class="fa fa-times" aria-hidden="true"></i></a>
                                                   
                                                </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td>
                                                        No Lead Custom Field Added.
                                                    </td>
                                                </tr>
                                            @endforelse
                                            
                                        </tbody>
                                    </table>
                                </div>
                            <div class="clearfix"></div>
                            
                            
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->

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

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/jquery.repeater/jquery.repeater.js') }}"></script>
<script>
    $(function() {
//        var table = $('#custom_fields').dataTable({
//            responsive: true,
//            processing: true,
//            serverSide: true,
//            ajax: '{!! route('admin.custom-fields.data') !!}',
//            "order": [[ 0, "desc" ]],
//            deferRender: true,
//            language: {
//                "url": "<?php echo __("app.datatable") ?>"
//            },
//            "fnDrawCallback": function( oSettings ) {
//                $("body").tooltip({
//                    selector: '[data-toggle="tooltip"]'
//                });
//            },
//            columns: [
//                {data: 'id', name: 'id', orderable: false, searchable: false, visible:false},
//                {data: 'module', name: 'custom_field_groups.name', orderable: true, searchable: true},
//                {data: 'label', name: 'label', orderable: true, searchable: true},
//                {data: 'name', name: 'name', orderable: true, searchable: true},
//                {data: 'type', name: 'type', orderable: true, searchable: true},
//                {data: 'values', name: 'values', orderable: true, searchable: true},
//                {data: 'required', name: 'required', orderable: true, searchable: true},
//                {data: 'action', name: 'action', orderable: false, searchable: false}
//            ]
//        });

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('user-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted field!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.custom-fields.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                window.location.reload();
                                //$.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                //table._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        $('#add-field').click(function(){
            var url = '{{ route('admin.custom-fields.create')}}';
            $('#modelHeading').html('Add Field');
            $.ajaxModal('#projectCategoryModal',url);
        })

        $('body').on('click', '.edit-custom-field', function() {
            var id = $(this).data('user-id');
            var url = "{{ route('admin.custom-fields.edit',':id') }}";
            url = url.replace(':id', id);
            $('#modelHeading').html('Edit Field');
            $.ajaxModal('#projectCategoryModal',url);
        })

    });
</script>

@endpush

