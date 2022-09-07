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
                <div class="panel-heading p-t-10 p-b-10">{{ __('Edit Email Template') }}</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.email_template_setting_menu')

                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row b-t m-t-20 p-10">
                                        <div class="col-md-12">
                                            {!! Form::open(['id'=>'editSettings', 'class'=>'ajax-form', 'method'=>'POST', 'enctype' => 'multipart/form-data']) !!}
                                            <label for="">Template Name</label>
                                            <input type="hidden"
                                                   value="{{ isset($emailTemplate) ? $emailTemplate->id : ''}}"
                                                   name="emailTemplateId">
                                            <div class="form-group">
                                                <input type="text" required name="template_name"
                                                       value="{{ isset($emailTemplate) ? $emailTemplate->template_name : '' }}"
                                                       class="form-control">
                                            </div>
                                            <hr>
                                            <label for="">Subject</label>
                                            <div class="form-group">
                                                <input type="text" required name="subject"
                                                       value="{{ isset($emailTemplate) ? $emailTemplate->subject : '' }}"
                                                       class="form-control">
                                            </div>
                                            <hr>

                                            <label for="">Select email type</label>
                                            <div class="form-group">
                                                <select name="email_type" class="form-control select-email">
                                                    <option value="1" {{ isset($emailTemplate) ? ($emailTemplate->email_type == '1')  ? 'selected' : '' : '' }}>
                                                        Send email
                                                    </option>
                                                    <option value="2" {{ isset($emailTemplate) ? ($emailTemplate->email_type == '2') ? 'selected' : '' : '' }}>
                                                        Send file via email
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="display-none email-file-div">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <label for="">File</label>
                                                        <div class="form-group">
                                                            <input type="file" class="file form-control" name="file">
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <a class="form-group">
                                                            @if($emailTemplate->file_extension == 'pdf')
                                                                <a href="{{ $emailTemplate->file_url }}" target="_blank">
                                                                <img src="{{ asset('img/pdf.png') }}"
                                                                     class="display-file-preview" alt="file" width="100"
                                                                     height="100">
                                                                </a>
                                                            @else
                                                                <img src="{{ $emailTemplate->file_url }}"
                                                                     class="display-file-preview" alt="file" width="100"
                                                                     height="100">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                            <p class="text-muted m-b-30 font-13">You can use following variables in your
                                                email template.</p>
                                        <div class="display:block">&lbrace;&lbrace;client.first.name&rbrace;&rbrace;</div>
{{--                                        <div class="display:block">&lbrace;&lbrace;client.email&rbrace;&rbrace;</div>--}}
{{--                                        <div class="display:block">&lbrace;&lbrace;client.password&rbrace;&rbrace;</div>--}}
{{--                                        <div class="display:block">&lbrace;&lbrace;client.login_to_dashboard&rbrace;&rbrace;</div>--}}
                                        <div class="display:block m-b-15">&lbrace;&lbrace;lead.name&rbrace;&rbrace;</div>                                        <label for="">Body</label>
                                            <div class="form-group">
                                                <textarea name="body" required id="" cols="30" rows="10"
                                                          class="summernote">
                                                    {{ isset($emailTemplate) ? $emailTemplate->body : '' }}
                                                </textarea>

                                            </div>

                                            <button type="button" onclick="submitForm();" class="btn btn-primary">
                                                Update
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
    <!-- .row -->

@endsection
<style>
    .display-none {
        display: none;
    }
</style>
@push('footer-script')
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script>
        let pdfIcon = "{{ asset('img/pdf.png') }}";

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
                ['link', ['linkDialogShow', 'unlink']],
                ["view", ["fullscreen"]],
            ],
        });

        if ($('.select-email').val() == '2') {
            $('.email-file-div').removeClass('display-none');
        }
        $(document).on('change', '.select-email', function () {
            if ($(this).val() == '2') {
                $('.email-file-div').removeClass('display-none');
            } else {
                $('.email-file-div').addClass('display-none');
            }
        });

        function submitForm() {

            let form = $('#editSettings');
            $.ajax({
                url: '{{ route('admin.email-template.update', $emailTemplate->id) }}',
                container: '#editSettings',
                type: "POST",
                data: new FormData(form[0]),
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == 'success') {
                        $.showToastr(data.message, 'success');
                        window.location.href = '{{ route('admin.email-template.index') }}';
                    }
                    if (data.status == 'fail') {
                        $.showToastr(data.message, 'error');
                    }
                },
                error: function (data) {
                    $.showToastr(data.responseJSON.message, 'error');
                },
            })
        }

        $(document).on('change', '.file', function () {
            let $ele = $('.display-file-preview');
            displayImg(this, $ele);
        });

        function displayImg(input, $ele) {
            if (input.files && input.files[0]) {
                let ext = $(input).val().split('.').pop().toLowerCase();
                var reader = new FileReader();
                reader.onload = function (event) {
                    if(ext == 'pdf'){
                        $ele.attr('src', pdfIcon);
                    }else {
                        $ele.attr('src', event.target.result);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush

