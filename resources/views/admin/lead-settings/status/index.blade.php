@extends('layouts.app')

@section('page-title')
    <div class="row bg-title p-b-0">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
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

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">@lang('app.menu.leadStatus')</div>

                <div class="vtabs customvtab m-t-10">

                    @include('sections.lead_setting_menu')

     

                    <div class="row">

                        <div class="col-md-12">
                            <div class="white-box p-0">
                                <h3>@lang('app.addNew') @lang('modules.lead.leadStatus')</h3>

                                {!! Form::open(['id'=>'createTypes','class'=>'ajax-form','method'=>'POST']) !!}

                                <div class="form-body">

                                    <div class="form-group">
                                        <label>@lang('modules.lead.leadStatus')</label>
                                        <input type="text" name="type" id="type" class="form-control">
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" id="save-type" class="btn btn-success"><i
                                                    class="fa fa-check"></i> @lang('app.save')
                                        </button>
                                    </div>
                                </div>

                                {!! Form::close() !!}

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="white-box">
                                <h3>@lang('app.menu.leadStatus')</h3>


                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('app.name')</th>
                                            <th>@lang('app.action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($leadStatus as $key=>$status)
                                            <tr>
                                                <td>{{ ($key+1) }}</td>
                                                <td>{{ ucwords($status->type) }}</td>
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
                                                <td>
                                                    @lang('messages.noLeadStatusAdded')
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                            
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->


    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="leadStatusModal" role="dialog" aria-labelledby="myModalLabel"
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
<script type="text/javascript">


    //    save project members
    $('#save-type').click(function () {
        $.easyAjax({
            url: '{{route('admin.lead-status-settings.store')}}',
            container: '#createTypes',
            type: "POST",
            data: $('#createTypes').serialize(),
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
            text: "This will remove the lead status from the list.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.lead-status-settings.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });


    $('.edit-type').click(function () {
        var typeId = $(this).data('type-id');
        var url = '{{ route("admin.lead-status-settings.edit", ":id")}}';
        url = url.replace(':id', typeId);

        $('#modelHeading').html("{{  __('app.edit')." ".__('modules.lead.leadStatus') }}");
        $.ajaxModal('#leadStatusModal', url);
    })


</script>


@endpush

