@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <div @if($totalRecords == 0) style="display: none;"  @endif class="col-sm-12 p-t-10 p-b-10 border-bottom d-flex" style="justify-content: space-between;">
            <div class="">
                <h4 style="font-size: 20px;width: 80%;padding: 0;">Cue up a custom sequence of steps to keep your client process moving along even when you’re logged out.
                    Need help with your automations? Visit the help center or reach out!</h4>
            </div>
            <div class="form-group m-0 " style="float: right;">
                <a type="button" href="{{ route('admin.email-automation.create') }}"
                   class="btn btn-info btn-sm">+ Add new Automation</a>
            </div>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div @if($totalRecords == 0) style="display: none;"  @endif class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
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
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
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
    <div @if($totalRecords == 0) style="display: none;"  @endif class="row dashboard-stats front-dashboard">
        @if ($message = Session::get('error'))
            <div class="alert alert-danger"> {!! $message !!}</div>
            <?php Session::forget('error');?>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="box-shadow" style="height: 80%;">
                    <div class="automation-table">
                        @forelse($automations as $automation)
                            <div class="automation-table__row">
                                <div class="automation-table__col">
                                    <div class="automation-table__col-name" data-id="{{ $automation->id }}">
                                        <a href="{{ route('admin.email-automation.edit', $automation->id) }}" style="color: #666 !important;">
                                            {{ $automation->name }}
                                        </a>
                                    </div>
                                    <div class="automation-table__col-steps">
                                        <span>{{ $automation->step }}</span>
                                        <span>step</span>
                                    </div>
                                </div>
                                <div class="automation-table__col">
                                    <div class="automation-table__col-delete">
                                        <a href="javascript:;" class="automation-delete"
                                           data-email-automation-id="{{ $automation->id }}"><i
                                                    class="fa fa-trash icon-color" aria-hidden="true"></i></a>
                                        <a href="javascript:;" class="automation-copy"
                                           data-email-automation-id="{{ $automation->id }}"><i
                                                    class="fa fa-copy icon-color m-r-5" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center">
                                <h3>No automation yet...</h3>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
<div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/email-automation.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Email Automation</h1>
			<p class="m-b-30">Why spent time writing an email when you can automate it? Send automated email series to clients, leads, and even existing clients. All equipped with pre-made templates to get you started.</p>
			<a type="button" href="{{ route('admin.email-automation.create') }}" class="btn-black">+ Add new Automation</a>
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
					<h4 class="modal-title">Email Automation</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-modal-md in automationCopy" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                class="fa fa-times"></i></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">Copy  Automation</span>
                </div>
                <div class="modal-body">
                    {!! Form::open(['id'=>'copyAutomationForm', 'class'=>'ajax-form', 'method'=>'POST']) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="edit-email-modal" style="padding: 15px;">
                                <input type="hidden" name="automation_id" class="form-control copy-automation-id">
                                <label for="">Please name your automation template</label>
                                <div class="form-group" style="margin: 0 !important">
                                    <input type="text" name="name" value="Untitled Automation" required class="form-control copy-email-automation-name">
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveCopyAutomationBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection
<style>
    .box-shadow {
        padding: 30px 12px;
        background: #FAFAFA;
        border: 1px solid rgb(227, 227, 227);
        border-radius: 10px;
    }

    .automation-table {
        max-width: 100%;
        /*margin-top: 35px;*/
        /*margin-bottom: 100px;*/
    }

    .automation-table__row {
        display: flex;
        -webkit-flex-direction: row;
        align-items: center;
        height: 60px;
        border-radius: 2px;
        /*background-color: #333;*/
        background: #FAFAFA;
        border: 1px solid rgb(227, 227, 227);
        margin-bottom: 2px;
        padding-left: 20px;
        padding-right: 20px;
        cursor: pointer;
    }

    .automation-table__row:hover{
        background-color: #fcfcfc !important;
    }

    .automation-table__col {
        width: 50%;
        display: flex;
        -webkit-flex-direction: row;
        align-items: center;
    }

    .automation-table__col-name {
        width: 70%;
        font-size: 16px;
        font-weight: 700;
        color: #666;
        cursor: pointer;
    }

    .automation-table__col-steps {
        width: 30%;
        font-size: 13px;
        color: #999;
    }

    .automation-table__col-delete {
        width: 50%;
    }

    .icon-color {
        font-size: 16px !important;
        float: right;
        color: #666;
    }
</style>
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
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

    <script>
        $(function () {
            $('body').on('click', '.automation-delete', function () {
                let id = $(this).attr('data-email-automation-id');
                let Ele = $(this);
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted notice!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        var url = "{{ route('admin.email-automation.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'DELETE',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE', 'id': id},
                            success: function (response) {
                                if (response.status === "success") {
                                    $.unblockUI();
                                    Ele.parent().parent().parent().remove();
                                }
                            }
                        });
                    }
                });
            });
        });

        $(document).on('click', '.automation-copy', function () {
            let id = $(this).attr('data-email-automation-id');
            $.ajax({
                url: "{{ route('admin.email-automation.get-automation-template') }}",
                type: "POST",
                data: {'_token': "{{ csrf_token() }}", 'id': id},
                success: function (data) {
                    if (data.status == 'success') {
                        $.unblockUI();
                        $('.automationCopy').modal('show');
                        if(data.automationMaster){
                            $('.copy-email-automation-name').val(data.automationMaster.name);
                            $('.copy-automation-id').val(id);
                        }
                    }
                    if (data.status == 'fail') {
                        $.showToastr(data.message, 'error');
                    }
                },
                error: function (data) {
                    $.showToastr(data.responseJSON.message, 'error');
                },
            });
        });

        $(document).on('click','.edit-email-automation', function (){
            let id = $(this).data('id');
            let url = "{{ route('admin.email-automation.edit', ':id') }}";
            url = url.replace(':id', id);
            window.location.href = url;
        });

        $(document).on('click', '#saveCopyAutomationBtn', function (){
            if ($('.copy-email-automation-name').val() == null || $('.copy-email-automation-name').val() == '') {
                $.showToastr('Please enter automation name', 'error');

                return false;
            }
            $.ajax({
                url: "{{ route('admin.email-automation.make-it-duplicate') }}",
                type: "POST",
                data: $('#copyAutomationForm').serialize(),
                success: function (data) {
                    if (data.status == 'success') {
                        $.unblockUI();
                        $.showToastr(data.message, 'success');
                        $('.automationCopy').modal('hide');
                        $('.automationCopy').on('hidden.bs.modal', function () {
                            $(this).find('form').trigger('reset');
                        });
                        $('.automation-table:last').append(`
                                    <div class="automation-table__row">
                                            <div class="automation-table__col">
                                                <div class="automation-table__col-name edit-email-automation" data-id=${data.automation.id}>
                                                    ${data.automation.name}
                                    </div>
                                    <div class="automation-table__col-steps">
                                        <span>${data.automation.step}</span>
                                                    <span>step</span>
                                                </div>
                                            </div>
                                            <div class="automation-table__col">
                                                <div class="automation-table__col-delete">
                                                    <a href="javascript:;"  class="automation-delete" data-email-automation-id="${data.automation.id}"><i class="fa fa-trash icon-color"></i></a>
                                                    <a href="javascript:;"  class="automation-copy" data-email-automation-id="${data.automation.id}"><i class="fa fa-copy icon-color m-r-5"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                    }
                    if (data.status == 'fail') {
                        $.showToastr(data.message, 'error');
                    }
                },
                error: function (data) {
                    $.showToastr(data.responseJSON.message, 'error');
                },
            });
        });
		
     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/HRmYAcM6Iwg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    }); 

    </script>
@endpush