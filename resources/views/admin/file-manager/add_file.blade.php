@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} : {{$folder_name}}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

<style>
    .panel-black .panel-heading a, .panel-inverse .panel-heading a {
        color: unset!important;
    }
    .bootstrap-select.btn-group .dropdown-menu li a span.text {
        color: #000;
    }
    .panel-black .panel-heading a:hover, .panel-inverse .panel-heading a:hover {
        color: #000 !important;
    }
    .panel-black .panel-heading a, .panel-inverse .panel-heading a {
        color: #000 !important;
    }
    .btn-info.active, .btn-info:active, .open>.dropdown-toggle.btn-info {
        background-color:unset !important; ;
        border-color: #269abc;
    }
    .note-editor{
        border: 1px solid #e4e7ea !important;
    }
    .btn-info.active.focus, .btn-info.active:focus, .btn-info.active:hover, .btn-info.focus, .btn-info.focus:active, .btn-info:active:focus, .btn-info:active:hover, .btn-info:focus, .open>.dropdown-toggle.btn-info.focus, .open>.dropdown-toggle.btn-info:focus, .open>.dropdown-toggle.btn-info:hover {
        background-color: #03a9f3;
        border: 1px solid #03a9f3;
        color: #000;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-wrapper collapse in" aria-expanded="true">

                    <div class="panel-body p-0">
                        <form method="POST" enctype="multipart/form-data" action="{!! route('admin.storeFile') !!}">
                        <div class="form-body">
                            <h3 class="box-title m-b-10">Add Files</h3>

                            <div class="row m-b-20">
                                <div class="col-md-12">
                                    @if($upload)
                                        <div class="col-lg-12 m--align-right row">
                                            <div class="m-dropzone m-dropzone--custom dropzone col-lg-12" action="<?php echo URL::to('/scripts/dzyfidwenaolpu.php') ?>" id="m-dropzone-file">
                                                <div class="m-dropzone__msg dz-message needsclick">
                                                    <h3 class="m-dropzone__msg-title">Drop files here or click to upload.</h3>
                                                    <h3 class="m-dropzone__msg-desc">Max Files: 10</h3>
                                                    <h3 class="m-dropzone__msg-desc">Max Size: 2 MB</h3>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="form-actions">
                            <button type="submit" id="" class="btn btn-success"><i class="fa fa-check"></i>
                                @lang('app.save')
                            </button>
                        </div>

                            @csrf
                        {{ Form::hidden('up_docs', '', ['id' => 'up_docs']) }}
                        {{ Form::hidden('folder_id', $folder_id, ['id' => 'up_docs']) }}

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel"
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
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>

<script type="text/javascript">
    var docFiles = [];
    var sub_folder = '{{ $folder_name }}';
    var docs = '';
    var fileType = "<?php echo 'image/*,application/pdf'; ?>";
    var maxSize = 2;
    var maxFiles = 10;
    var upDocSelector = '#up_docs';

    var DropzoneFile= {
        init:function() {
            Dropzone.options.mDropzoneFile= {
                paramName:"Filedata",
                params: { 'sub_folder' : sub_folder },
                maxFiles:maxFiles,
                maxFilesize:maxSize,
                addRemoveLinks:0,
                acceptedFiles:fileType,
                init: function() {
                    this.on('success', function( file, response ){
                        var result = JSON.parse(response);
                        if (result.code == '200') {
                            var fileName = result.file_name;

                            docFiles.push(fileName);
                            docs = docFiles.join(':|:');

                            $(upDocSelector).val(docs);
                        }
                    });
                }
            }
        }
    };

    DropzoneFile.init();
</script>
@endpush

