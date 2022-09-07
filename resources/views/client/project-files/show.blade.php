@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('client.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('modules.projects.files')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')

<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<style>
    .file-bg {
        height: 150px;
        overflow: hidden;
        position: relative;
    }
    .file-bg .overlay-file-box {
        opacity: .9;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        text-align: center;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line client-ptabs">
                    <div class="white-box p-0">
                        <nav>
                            <ul class="showProjectTabs">
                                <li><a href="{{ route('client.projects.show', $project->id) }}"><span>@lang('modules.projects.overview')</span></a></li>
                                @if(in_array('employees',$modules))
                                <li><a href="{{ route('client.project-members.show', $project->id) }}"><span>@lang('modules.projects.members')</span></a></li>
                                @endif

                                @if(in_array('tasks',$modules))
                                    <li><a href="{{ route('client.tasks.edit', $project->id) }}"><span>@lang('app.menu.tasks')</span></a></li>
                                @endif

                                <li class="tab-current"><a href="{{ route('client.files.show', $project->id) }}"><span>@lang('modules.projects.files')</span></a></li>

                                @if(in_array('timelogs',$modules))
<!--                                <li><a href="{{ route('client.time-log.show', $project->id) }}"><span>@lang('app.menu.timeLogs')</span></a></li>-->
                                @endif
                                
                                <li ><a href="{{ route('client.product-review-project.show', $project->id) }}"><span>Product Review</span></a></li>

                                @if(in_array('invoices',$modules))
                                <li><a href="{{ route('client.project-invoice.show', $project->id) }}"><span>@lang('app.menu.invoices')</span></a></li>
                                @endif
                                <li>
                                    <a href="{{ route('client.projects.discussion', $project->id) }}"></i>
                                        <span>@lang('modules.projects.discussion')</span></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="files-list-panel">
                                    <div class="white-box p-0">
                                        <h2 class="border-bottom">@lang('modules.projects.files')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12 border-bottom p-b-10">
                                                <a href="javascript:;" id="show-dropzone"
                                                   class="btn btn-success btn-outline"><i class="ti-upload"></i> @lang('modules.projects.uploadFile')</a>
                                            </div>
                                        </div>

                                        <div class="row m-b-20 hide" id="file-dropzone">
                                            <div class="col-md-12">
                                                @if($upload)
                                                    <form action="{{ route('client.files.store') }}" class="dropzone"
                                                          id="file-upload-dropzone">
                                                        {{ csrf_field() }}

                                                        {!! Form::hidden('project_id', $project->id) !!}

                                                        <input name="view" type="hidden" id="view" value="list">

                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple/>
                                                        </div>
                                                    </form>
                                                @else
                                                    <div class="alert alert-danger">@lang('messages.storageLimitExceedContactAdmin')</div>
                                                @endif
                                            </div>
                                        </div>

                                        <ul class="nav nav-tabs" role="tablist" id="list-tabs">
                                            <li role="presentation" class="active nav-item" data-pk="list"><a href="#list" class="nav-link" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> List</span></a></li>
                                            <li role="presentation" class="nav-item" data-pk="thumbnail"><a href="#thumbnail" class="nav-link thumbnail" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Thumbnail</span></a></li>
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="list">
                                                <ul class="list-group" id="files-list">
                                                    @forelse($project->files as $file)
                                                        @if(is_null($file->folder_id))
                                                            <li class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-md-9">
                                                                        {{ $file->filename }}
                                                                    </div>
                                                                    <div class="col-md-3">

                                                                            <a target="_blank" href="{{ asset_url_local_s3('project-files/'.$project->id.'/'.$file->hashname) }}"
                                                                               data-toggle="tooltip" data-original-title="View"
                                                                               class="btn btn-info btn-circle"><i
                                                                                        class="fa fa-search"></i></a>


                                                                        @if(is_null($file->external_link))
                                                                        <a href="{{ route('client.files.download', $file->id) }}"
                                                                           data-toggle="tooltip" data-original-title="Download"
                                                                           class="btn btn-default btn-circle"><i
                                                                                    class="fa fa-download"></i></a>


                                                                        @if($file->user_id == $user->id)
                                                                            &nbsp;
                                                                            <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}" class="btn btn-danger btn-circle sa-params" data-pk="list"><i class="fa fa-times"></i></a>
                                                                        @endif
                                                                        @endif

                                                                        <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @empty
                                                        <li class="list-group-item">
                                                            <div class="row">
                                                                <div class="col-md-10">
                                                                    @lang('messages.noFileUploaded')
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforelse

                                                </ul>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="thumbnail">

                                            </div>
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

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script>
    $('#show-dropzone').click(function () {
        $('#file-dropzone').toggleClass('hide show');
    });

    $("body").tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    @if($upload)
        // "myAwesomeDropzone" is the camelized version of the HTML element's ID
        Dropzone.options.fileUploadDropzone = {
        paramName: "file", // The name that will be used to transfer the file
//        maxFilesize: 2, // MB,
        dictDefaultMessage: "@lang('modules.projects.dropFile')",
        accept: function (file, done) {
            done();
        },
        init: function () {
            this.on("success", function (file, response) {
                var viewName = $('#view').val();

                if(response.status == 'fail') {
                    $.showToastr(response.message, 'error');
                    return;
                }

                if(viewName == 'list') {
                    $('#files-list-panel ul.list-group').html(response.html);
                } else {
                    $('#thumbnail').empty();
                    $(response.html).hide().appendTo("#thumbnail").fadeIn(500);
                }
            })
        }
    };
    @endif
    $('.thumbnail').on('click', function(event) {
        event.preventDefault();
        $('#thumbnail').empty();
        var projectID = "{{ $project->id }}";
        $.easyAjax({
            type: 'GET',
            url: "{{ route('client.files.thumbnail') }}",
            data: {
                id: projectID
            },
            success: function (response) {
                $(response.view).hide().appendTo("#thumbnail").fadeIn(500);
            }
        });
    });


    $('#list-tabs').on("shown.bs.tab",function(event){
        var tabSwitch = $('#list').hasClass('active');
        if(tabSwitch == true) {
            $('#view').val('list');
        } else {
            $('#view').val('thumbnail');
        }
    });

    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('file-id');
        var deleteView = $(this).data('pk');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('client.files.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE', 'view': deleteView},
                    success: function (response) {
                        console.log(response);
                        if (response.status == "success") {
                            $.unblockUI();
                            if(deleteView == 'list') {
                                $('#files-list-panel ul.list-group').html(response.html);
                            } else {
                                $('#thumbnail').empty();
                                $(response.html).hide().appendTo("#thumbnail").fadeIn(500);
                            }
                        }
                    }
                });
            }
        });
    });

</script>
@endpush
