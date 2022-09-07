@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <div class="col-sm-12 p-t-10 p-b-10 border-bottom">
            <div class="form-group m-0">
                <button type="button"  id="add-folder"
                   class="btn btn-info btn-sm">+ Add new Folder</button>
            </div>
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
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
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

    {{--<iframe src="{{ url('laravel-filemanager') }}" style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>--}}

    <div class="row dashboard-stats front-dashboard">
        @if ($message = Session::get('error'))
        <div class="alert alert-danger"> {!! $message !!}</div>
                <?php Session::forget('error');?>
        @endif
        <div class="col-md-12 border-bottom m-b-10 p-b-10">
            <div class="">
                <div class="col-md-6 col-sm-6 text-right">
                    <h4 class="white-box"><span class="text-info-" id="totalWorkingDays">{{ sizeof($folders) }}</span> <span class="font-12 text-muted m-l-5"> Folders</span></h4>
                </div>
                <div class="col-md-6 col-sm-6 text-left">
                    <h4 class="white-box"><span class="text-warning-" id="daysPresent">{{ $files }}</span> <span class="font-12 text-muted m-l-5"> Files</span></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover responsive nowrap display" id="table_0">
                            <thead>
                                <tr>
                                    <th>Folders</th>
                                    <th>Created By</th>
                                    <th>User Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($counter=1)
                                @foreach($folders as $folder)
                                    <tr>
                                        <td><span style="margin-right: 3rem">{{ $counter }}</span> 
                                            
                                            @if (!is_null($folder->folder_password) && $folder->folder_password!='')
                                            <a href="javascript:void(0)" data-folder-id="{{ $folder->id }}" class="preview-folder" ><i class="fa fa-folder" aria-hidden="true"></i> {{ $folder->folder_name }}</a>
                                            @else
                                                <a href="{{ route('admin.preview_folder', ['folder_id'=>$folder->id]) }}"><i class="fa fa-folder" aria-hidden="true"></i> {{ $folder->folder_name }}</a>
                                            @endif
                                            
                                            
                                        </td>
                                        <td>{{ $folder->created_by }}</td>
                                        <td>{{ '' }}</td>
                                        <td class=" text-center"><div class="btn-group dropdown m-r-10">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn " type="button"><i class="ti-more"></i> </button>
                                                <ul role="menu" class="dropdown-menu pull-right">
                                                    <li><a href="{{ route('admin.delete_folder', ['folder_id'=>$folder->id]) }}"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
                                                    <li><a href="javascript:void(0)" data-folder-idd="{{ $folder->id }}" class="change-password"><i class="fa fa-lock" aria-hidden="true"></i> Change Password</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @php($counter++)
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">Create Folder</span>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.create_folder') }}">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Folder Name</label>
                                    <input required="" type="text" name="folder_name" id="folder_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Folder Password</label>
                                    <input type="password" name="folder_password" id="folder_password" autocomplete="password-new" class="form-control">
                                    <span class="help-block"> Leave blank if you don't want to change it. </span>
                                    
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
    
     {{--Ajax Modal--}}
     
     
    <div class="modal fade bs-modal-md in" id="previewFolderModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">View Folder</span>
                </div>
                 {!! Form::open(['id'=>'previewFolderSource','class'=>'ajax-form','method'=>'POST']) !!}
                    <div class="modal-body">
                            <input type="hidden" name="folder_id" id="folder_id" class="form-control">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Folder Password</label>
                                        <input type="password" name="folder_password" id="folder_password" autocomplete="password-new" class="form-control">
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="save-group">View</button>
                    </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
    
    
    {{--Ajax Modal--}}
     
     
    <div class="modal fade bs-modal-md in" id="changePasswordModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">Change Folder</span>
                </div>
                 {!! Form::open(['id'=>'passwordFolderSource','class'=>'ajax-form','method'=>'POST']) !!}
                    <div class="modal-body">
                            <input type="hidden" name="folder_idd" id="folder_idd" class="form-control">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Folder Password</label>
                                        <input type="password" name="folder_passwordd" id="folder_passwordd" autocomplete="password-new" class="form-control">
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="change-group">Change</button>
                    </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}

@endsection

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

    <script>
        $(function () {
            $('#add-folder').click(function(){
                $('#projectCategoryModal').modal('show');
            });
            
            $('.preview-folder').click(function(){
                var folderID = $(this).data('folder-id');
                $('#folder_id').val(folderID);
                $('#previewFolderModal').modal('show');
            });
            
            $('#save-group').click(function () {
                    $.easyAjax({
                        url: '{{route('admin.check_password')}}',
                        container: '#previewFolderSource',
                        type: "POST",
                        redirect: true,
                        data: $('#previewFolderSource').serialize(),
                        success: function (response) {
                            if(response.status == 'success'){
                                $('#previewFolderModal').modal('hide');
                            }
                        }
                    })
                });
                
                
                
                
            $('.change-password').click(function(){
                var folderIDD = $(this).data('folder-idd');
                $('#folder_idd').val(folderIDD);
                $('#changePasswordModal').modal('show');
            });
            
            $('#change-group').click(function () {
                    $.easyAjax({
                        url: '{{route('admin.change_password')}}',
                        container: '#passwordFolderSource',
                        type: "POST",
                        redirect: true,
                        data: $('#passwordFolderSource').serialize(),
                        success: function (response) {
                            if(response.status == 'success'){
                                $('#changePasswordModal').modal('hide');
                            }
                        }
                    })
                });

            $(document).on('click', '#save', function () {
                $('#save_submit').click();
            })

            $('#table_0').DataTable();

        })

    </script>
@endpush