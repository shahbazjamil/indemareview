@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> Product Statuses</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.settings.index') }}">@lang('app.menu.settings')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">Product Status</div>

                <div class="vtabs customvtab m-t-10">


@include('sections.admin_setting_menu')

                    <div class="tab-content p-0 p-t-10">
                        <div id="vhome3" class="tab-pane active">

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="white-box p-0">
                                        <h3>@lang('app.addNew') Product Status</h3>

                                        {!! Form::open(['id'=>'createCategory','class'=>'ajax-form','method'=>'POST']) !!}

                                        <div class="form-body">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    
                                                    <div class="form-group">
                                                        <label>Status Name</label>
                                                        <input type="text" name="status_name" id="status_name" class="form-control">
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                           
                                            <div class="row">
                                                
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Status Color</label>
                                                    <input type="text" name="status_color" id="status_color" class="form-control colorpicker">
                                                </div>
                                                
                                            </div>
                                                
                                            </div>
                                            
                                            <div class="row">
                                                
                                                <div class="col-md-4">

                                                    <div class="form-actions">
                                                        <button type="submit" id="save-code" class="btn btn-success"><i
                                                                    class="fa fa-check"></i> @lang('app.save')
                                                        </button>
                                                    </div>

                                                </div>
                                                
                                            </div>
                                            
                                            
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="white-box p-0">
                                        <h3 class="m-t-20">Product Status</h3>


                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Status Name</th>
                                                    <th>Status Color</th>
                                                    <th>@lang('app.action')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($statuses as $key=>$status)
                                                    <tr>
                                                        <td>{{ ($key+1) }}</td>
                                                        <td>{{ $status->status_name }}</td>
                                                        <td>{{ $status->status_color}}</td>
                                                        <td>
                                                            <a href="javascript:;" data-type-id="{{ $status->id }}"
                                                               class="btn btn-sm btn-info btn-rounded btn-outline edit-type"><i
                                                                        class="fa fa-edit"></i> @lang('app.edit')</a>
                                                                        
                                                            <a href="javascript:;" data-type-id="{{ $status->id }}"
                                                               class="btn btn-sm btn-danger btn-rounded btn-outline delete-type"><i
                                                                        class="fa fa-times"></i> @lang('app.remove')</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">
                                                            No Product Status added.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

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
    <div class="modal fade bs-modal-md in" id="categoryModal" role="dialog" aria-labelledby="myModalLabel"
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

@endsection

@push('footer-script')

<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>

<script type="text/javascript">

        $(".colorpicker").asColorPicker();

    //    save project members
    $('#save-code').click(function () {
        $.easyAjax({
            url: '{{route('admin.product-status.store')}}',
            container: '#createCategory',
            type: "POST",
            data: $('#createCategory').serialize(),
            success: function (response) {
                if (response.status == "success") {
                    $.unblockUI();
                    window.location.reload();
                }
            }
        })
    });


    $('body').on('click', '.delete-type', function () {
        var id = $(this).data('type-id');
        swal({
            title: "Are you sure?",
            text: "This will remove the Product Status from the list.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.product-status.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });

    $('.edit-type').click(function () {
        var typeId = $(this).data('type-id');
        var url = '{{ route("admin.product-status.edit", ":id")}}';
        url = url.replace(':id', typeId);

        $('#modelHeading').html("Edit Product Status");
        $.ajaxModal('#categoryModal', url);
    })


</script>


@endpush

