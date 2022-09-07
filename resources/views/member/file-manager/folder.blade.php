@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} : {{ $folder_name }}</h4>
        </div>
        <div class="col-sm-9 text-right">
            <div class="form-group">
                <a href="{{ route('member.add_file', ['folder_id'=>$folder_id]) }}" class="btn btn-info btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Add File</a>
            </div>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
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

    <div class="row dashboard-stats">
        <div class="col-md-12 m-t-20">
            <div class="white-box">
                <div class="col-md-6 col-sm-6 text-right">
                    <h4><span class="text-warning" id="daysPresent">{{ sizeof($files) }}</span> <span class="font-12 text-muted m-l-5"> Files</span></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 m-t-25">
                <div class="white-box" style="padding-left: 5rem; padding-right: 5rem">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover responsive nowrap display" id="table_0">
                            <thead>
                                <tr>
                                    <th colspan="2">File</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($counter=1)
                                @foreach($files as $folder)
                                    <tr>
                                        <td>{{ $counter }}</td>
                                        <td><a href="{{ '/filemanager/'.$folder_name.'/'.$folder->file_name }}" target="_blank"> {{ $folder->file_name }}</a></td>
                                        <td>{{ $folder->created_by }}</td>
                                        <td>{{ $folder->created_date }}</td>
                                        <td class=" text-center"><div class="btn-group dropdown m-r-10">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn " type="button">Action <span class="caret"></span> </button>
                                                <ul role="menu" class="dropdown-menu pull-right">
                                                    <li><a href="{{ route('member.delete_file', ['folder_id'=>$folder_id, 'file_id'=>$folder->id]) }}"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
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
//        $(function () {
//            $('#table_0').DataTable();
//        });

    </script>
@endpush