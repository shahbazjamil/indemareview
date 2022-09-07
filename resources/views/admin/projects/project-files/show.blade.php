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
                <div class="sttabs tabs-style-line">
                    @include('admin.projects.show_project_menu')
                    <div class="content-wrap">
                        <div class="row">
                                <div class="col-md-12">
                                    <?php if(empty($folder_id)) { ?>
                                    
                                    
                                    <div class="col-sm-12 p-b-10">
                                            <div class="form-group m-0">
                                                <button type="button"  id="add-folder"
                                                   class="btn btn-info btn-sm">+ Add new Folder</button>
                                            </div>
                                    </div>
                                    
                                    
                                        <div class="">
                                            <table class="table table-striped table-bordered table-hover responsive nowrap display">
                                                <thead>
                                                    <tr>
                                                        <th>Folder</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php($counter=1)
                                                    @foreach($folders as $folder)
                                                        <tr id="folderRow{{ $folder->id }}">
                                                            <td><span style="margin-right: 3rem">{{ $counter }}</span> 
                                                            <a href="{{ route('admin.files.show', [$project->id, 'folder_id'=>$folder->id]) }}"><i class="fa fa-folder" aria-hidden="true"></i> {{ $folder->folder_name }}</a>
                                                            </td>
                                                            <td class=" text-center"><div class="btn-group dropdown m-r-10">
                                                                    <button aria-expanded="false" data-toggle="dropdown" class="btn " type="button"><i class="ti-more"></i> </button>
                                                                    <ul role="menu" class="dropdown-menu pull-right">
                                                                        <li><a onclick="deleteFolder({{$folder->id}})" href="javascript:void(0)"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @php($counter++)
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } else { ?>
                                    <?php if(isset($folders[0])) { ?>
                                        <h2 class="border-bottom">{{ $folders[0]->folder_name }}</h2>
                                    <?php } ?>
                                    <a href="{{route('admin.files.show', [$project->id])}}" class="btn btn-default waves-effect waves-light">@lang('app.back')</a>
                                    
                                    <?php } ?>
                                    
                                </div>
                            </div>
                        <section id="section-line-3" class="show">
                            
                            
                            
                            
                            <div class="row">
                                <div class="col-md-12" id="files-list-panel">
                                    <div class="white-box p-0">
                                        <h2 class="border-bottom p-b-10">@lang('modules.projects.files')</h2>

                                        <div class="row m-b-10">

                                                <div class="col-md-2 hide">
                                                    <a href="javascript:;" id="show-dropzone"
                                                       class="btn btn-success btn-outline"><i class="ti-upload"></i> @lang('modules.projects.uploadFile')</a>
                                                </div>

                                            <div class="col-md-2">
                                                <a href="javascript:;" id="show-link-form"
                                                   class="btn btn-success btn-outline"><i class="ti-link"></i> @lang('modules.projects.addFileLink')</a>
                                            </div>
                                        </div>

                                        <div class="row m-b-20" id="file-dropzone">
                                            <div class="col-md-12">
                                                @if($upload)
                                                    <form action="{{ route('admin.files.store') }}" class="dropzone"
                                                          id="file-upload-dropzone">
                                                        {{ csrf_field() }}

                                                        {!! Form::hidden('project_id', $project->id) !!}
                                                        {!! Form::hidden('folder_id', $folder_id) !!}
                                                        

                                                        <input name="view" type="hidden" id="view" value="list">

                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple/>
                                                        </div>
                                                    </form>

                                                @else
                                                    <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row m-b-20 hide" id="file-link">
                                            {!! Form::open(['id'=>'file-external-link','class'=>'ajax-form','method'=>'POST']) !!}


                                                {!! Form::hidden('project_id', $project->id) !!}
                                                {!! Form::hidden('folder_id', $folder_id) !!}

                                                <input name="view" type="hidden" id="view" value="list">
                                                <div class="col-md-6">

                                                        <div class="form-group">
                                                            <label for="">@lang('app.name')</label>
                                                            <input type="text" name="filename" class="form-control">
                                                        </div>
                                                </div>
                                                <div class="col-md-6">

                                                    <div class="form-group">
                                                        <label for="">@lang('modules.projects.addFileLink')</label>
                                                        <input type="text" name="external_link" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                <div class="form-group">
                                                    <button class="btn btn-success" id="save-link">@lang('app.submit')</button>
                                                </div>
                                                </div>

                                            {!! Form::close() !!}
                                        </div>

                                        <!--<ul class="nav nav-tabs" role="tablist" id="list-tabs">
                                            <li role="presentation" class="active nav-item" data-pk="thumbnail"><a href="#thumbnail" class="nav-link thumbnail" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">@lang('app.thumbnail')</span></a></li>
                                            <li role="presentation" class="nav-item" data-pk="list"><a href="#list" class="nav-link" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs">  @lang('app.list')</span></a></li>
                                        </ul>-->
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="list">
                                                <ul class="list-group" id="files-list">
                                                    @forelse($files as $file)
                                                        <li class="list-group-item">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    {{ $file->filename }}
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <?php
                                                                     $show_hide_cls = 'fa-eye';
                                                                    if($file->file_hide == 1) {
                                                                        $show_hide_cls = 'fa-eye-slash';
                                                                    }
                                                                   
                                                                    ?>
                                                                    <a data-fls="list" data-idd="{{$file->id}}" data-toggle="tooltip" data-original-title="Show/Hide" class="btn btn-default btn-circle show_hide_file"  href="javascript:void();">
                                                                        <i class="fa {{$show_hide_cls}}" aria-hidden="true"></i></a>
                                                                    
                                                                    
                                                                    
                                                                        <a target="_blank" href="{{ $file->file_url }}"
                                                                           data-toggle="tooltip" data-original-title="View"
                                                                           class="btn btn-info btn-circle"><i
                                                                                    class="fa fa-search"></i></a>

                                                                    @if(is_null($file->external_link))
                                                                    &nbsp;&nbsp
                                                                    <a href="{{ route('admin.files.download', $file->id) }}"
                                                                       data-toggle="tooltip" data-original-title="Download"
                                                                       class="btn btn-default btn-circle"><i
                                                                                class="fa fa-download"></i></a>
                                                                    @endif
                                                                    &nbsp;&nbsp;
                                                                    <a href="javascript:;" data-toggle="tooltip"
                                                                       data-original-title="Delete"
                                                                       data-file-id="{{ $file->id }}"
                                                                       class="btn btn-danger btn-circle sa-params" data-pk="list"><i
                                                                                class="fa fa-times"></i></a>
                                                                    <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
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
                                                
                                                    <div class="row">
                                                        @foreach($files as $file)
                                                            <div class="col-md-2 m-b-10">
                                                                <div class="card">
                                                                        <div class="file-bg">
                                                                            <div class="overlay-file-box">
                                                                                <div class="user-content">
                                                                                    @if($file->icon == 'images')
                                                                                    <img class="card-img-top img-responsive" src="{{ $file->file_url }}" alt="Card image cap">
                                                                                    @else
                                                                                        <i class="fa {{$file->icon}}" style="font-size: -webkit-xxx-large; padding-top: 65px;"></i>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <div class="card-block">
                                                                        <h6 class="card-title">{{ $file->filename }}</h6>

                                                                            <a target="_blank" href="{{ $file->file_url }}"
                                                                               data-toggle="tooltip" data-original-title="View"
                                                                               class="btn btn-info btn-circle"><i
                                                                                        class="fa fa-search"></i></a>


                                                                        @if(is_null($file->external_link))
                                                                        <a href="{{ route('admin.files.download', $file->id) }}"
                                                                           data-toggle="tooltip" data-original-title="Download"
                                                                           class="btn btn-default btn-circle"><i
                                                                                    class="fa fa-download"></i></a>
                                                                        @endif
                                                                        <a href="javascript:;" data-toggle="tooltip"
                                                                           data-original-title="Delete"
                                                                           data-file-id="{{ $file->id }}"
                                                                           class="btn btn-danger btn-circle sa-params" data-pk="thumbnail"><i
                                                                                    class="fa fa-times"></i></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

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
    
    
    
     {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectFolderModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">Create Folder</span>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.files.create_folder') }}">
                        {!! Form::hidden('project_id', $project->id) !!}
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Folder Name</label>
                                    <input required="" type="text" name="folder_name" id="folder_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <input type="submit" class="btn blue hide" id="save_submit" name="save">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue" id="save">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
    
    

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script>

//$(function(){ 
//   $(".thumbnail").trigger('click'); 
//});


 $('.show_hide_file').click(function () {
        var id = $(this).data('idd');
        var deleteView = $(this).data('fls');

        var url = "{{ route('admin.files.show-hide',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";
        var folderID = '<?php echo $folder_id; ?>';
        
        $.easyAjax({
            type: 'POST',
                    url: url,
                    data: {'_token': token, 'view': deleteView , 'folder_id': folderID},
            success: function (response) {
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
    });
    
    $('#show-dropzone').click(function () {
        $('#file-dropzone').toggleClass('hide show');
    });

    $('#show-link-form').click(function () {
        $('#file-link').toggleClass('hide show');
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

                if(response.status == 'fail') {
                    $.showToastr(response.message, 'error');
                    return;
                }

                var viewName = $('#view').val();
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

                var url = "{{ route('admin.files.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";
                var folderID = '<?php echo $folder_id; ?>';

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE', 'view': deleteView , 'folder_id': folderID},
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

//    $('.thumbnail').on('click', function(event) {
//        event.preventDefault();
//        $('#thumbnail').empty();
//        var projectID = "{{ $project->id }}";
//        $.easyAjax({
//            type: 'GET',
//            url: "{{ route('admin.files.thumbnail') }}",
//            data: {
//              id: projectID
//            },
//            success: function (response) {
//                $(response.view).hide().appendTo("#thumbnail").fadeIn(500);
//            }
//        });
//    });

    $('#save-link').click(function () {
        $.easyAjax({
            url: '{{route('admin.files.storeLink')}}',
            container: '#file-external-link',
            type: "POST",
            redirect: true,
            data: $('#file-external-link').serialize(),
            success: function () {
                window.location.reload();
            }
        })
    });

    $('#list-tabs').on("shown.bs.tab",function(event){
        var tabSwitch = $('#list').hasClass('active');
        if(tabSwitch == true) {
            $('#view').val('list');
        } else {
            $('#view').val('thumbnail');
        }
    });
    
    function deleteFolder(id){
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted folder!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                var url = "{{ route('admin.files.delete_folder',':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    //redirect: true,
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#folderRow'+id).remove();

                        }
                    }
                });
            }
        });
    }
    
    
    
    $(function () {
            $('#add-folder').click(function(){
                $('#projectFolderModal').modal('show');
            });

            $(document).on('click', '#save', function () {
                $('#save_submit').click();
            })

    })
    
    
    $('ul.showProjectTabs .projectFiles').addClass('tab-current');
</script>
@endpush
