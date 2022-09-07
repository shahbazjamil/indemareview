@extends('layouts.app')

@section('page-title')
    <div class="row bg-title p-b-0">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __('app.menu.email_connection_setting') }}</h4>
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
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">{{ __('app.menu.email_connection') }}</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.email_connection_setting_menu')

                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                @if(!$emailConfiguration)
                                    <div class="">
                                        <div class="alert alert-danger">
                                            {{ __('messages.smtpConfigNotSet')}}
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-12">
                                    <div class="" style="justify-content: space-between; display: flex;">
                                        <h3 class="box-title m-b-0">SMTP</h3>
                                        <button type="button" class="btn btn-primary test-email">
                                            Test Mail
                                        </button>
                                    </div>
                                    <div class="row b-t m-t-20 p-10">
                                        <div class="col-md-12">
                                            {!! Form::open(['id'=>'editSettings', 'class'=>'ajax-form','method'=>'POST']) !!}
                                            <label for="">Mail Driver</label>
                                            <div class="form-group">
                                                <input type="text" name="driver" required value="{{ isset($emailConfiguration) ? $emailConfiguration->driver : '' }}" class="form-control">
                                            </div>
                                            <hr>
                                            <label for="">Mail Host</label>
                                            <div class="form-group">
                                                <input type="text" name="host" required value="{{ isset($emailConfiguration) ? $emailConfiguration->host : '' }}" class="form-control">
                                            </div>

                                            <hr>
                                            <label for="">Mail Port</label>
                                            <div class="form-group">
                                                <input type="text" name="port" required value="{{ isset($emailConfiguration) ? $emailConfiguration->port : '' }}" class="form-control">
                                            </div>

                                            <hr>
                                            <label for="">Mail Username</label>
                                            <div class="form-group">
                                                <input type="text" name="user_name" required value="{{ isset($emailConfiguration) ? $emailConfiguration->user_name : '' }}" class="form-control">
                                            </div>

                                            <hr>
                                            <label for="">Mail Password</label>
                                            <div class="form-group">
                                                <input type="password" name="password" value="{{ isset($emailConfiguration) ? $emailConfiguration->password : '' }}" class="form-control">
                                            </div>

                                            <hr>
                                            <label for="">Mail Encryption</label>
                                            <div class="form-group">
                                                <input type="text" name="encryption" required value="{{ isset($emailConfiguration) ? $emailConfiguration->encryption : '' }}" class="form-control">
                                            </div>

                                            <hr>
                                            <label for="">Mail From Address</label>
                                            <div class="form-group">
                                                <input type="email" name="sender_email" required value="{{ isset($emailConfiguration) ? $emailConfiguration->sender_email : '' }}" class="form-control">
                                            </div>

                                            <hr>
                                            <label for="">Mail From Name</label>
                                            <div class="form-group">
                                                <input type="text" name="sender_name" required value="{{ isset($emailConfiguration) ? $emailConfiguration->sender_name : '' }}" class="form-control">
                                            </div>

                                            <button type="button" onclick="submitForm();" class="btn btn-primary">
                                                Submit
                                            </button>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.row -->

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-md in testEmailCredential" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
                    </button>
                    <span class="caption-subject font-red-sunglo bold uppercase"
                          id="modelHeading">Test Email</span>
                </div>
                <div class="modal-body">
                    {!! Form::open(['id'=>'testEmailForm', 'class'=>'ajax-form', 'method'=>'POST', 'enctype' => 'multipart/form-data']) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="edit-email-modal" style="padding: 15px;">
                                <label for="">Email</label>
                                <div class="form-group">
                                    <input type="email" required name="email" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="testEmailBtn">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>

    <!-- .row -->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script>
        $('.summernote').summernote({
            height: 200,                 // set editor height
            minHeight: null,             // set minimum height of editor
            maxHeight: null,             // set maximum height of editor
            focus: false,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ["view", ["fullscreen"]]
            ]
        });

        function submitForm() {
            $.easyAjax({
                url: '{{route('admin.email-connection-setting.update')}}',
                container: '#editSettings',
                type: "POST",
                data: $('#editSettings').serialize(),
                success:function (data){
                    if (data.status == 'success'){
                        setTimeout(function (){
                            window.location.href = '{{ route('admin.email-connection-setting.index') }}'
                        }, 800);
                    }
                },
            });
        }

        // Test Email
        $(document).on('click', '.test-email', function (){
            $('.testEmailCredential').modal('show');
        });

        $(document).on('click', '#testEmailBtn', function (){
            $.easyAjax({
                url: '{{ route('admin.email-connection-setting.test-mail') }}',
                type: "POST",
                data: $('#testEmailForm').serialize(),
                success: function (data) {
                    if (data.status == 'success') {
                        $('.testEmailCredential').modal('hide');
                        $('.testEmailCredential').on('hidden.bs.modal', function () {
                            $(this).find('form').trigger('reset');
                        });
                    }
                },
            });
        });

        $('.testEmailCredential').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
        });
    </script>
@endpush

