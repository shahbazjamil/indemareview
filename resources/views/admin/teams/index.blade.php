@extends('layouts.app')

@section('page-title')
    <div class="row bg-title p-b-0">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="border-bottom col-xs-12 p-t-10 p-b-10">
            @if(!$groups->isEmpty())
            <a href="{{ route('admin.teams.create') }}" class="btn btn-outline btn-success btn-sm">+ @lang('app.add') @lang('app.department') </a>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')



    <div class="row">
        <div class="col-md-12">
           <div class="panel panel-inverse">
               <div class="vtabs customvtab m-t-10">
                   @include('sections.team_settings_menu')
                   <div class="tab-content p-0 p-t-20">
                       <div id="vhome3" class="tab-pane active">
                           <div class="row">
                               <div class="col-md-12">
                                   <h3 class="box-title m-b-0"> @lang('app.department')</h3>
                                   <div class="row">
                                       <div class="col-sm-12 col-xs-12 b-t p-t-20">
                                           
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="users-table">
                                                    <thead>
                                                    <tr>
                                                        <th>@lang('app.id')</th>
                                                        <th>@lang('app.department')</th>
                                                        <th>@lang('app.action')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>


                                                    @forelse($groups as $group)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $group->team_name }} <label class="label label-success">{{ sizeof($group->member) }} @lang('modules.projects.members')</label></td>
                                                            <td>

                                                                <div class="btn-group dropdown m-r-10">
                                                                    <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                                                    <ul role="menu" class="dropdown-menu pull-right">
                                                                        <li><a href="{{ route('admin.teams.edit', [$group->id]) }}"><i class="icon-settings"></i> @lang('app.manage')</a></li>
                                                                        <li><a href="javascript:;"  data-group-id="{{ $group->id }}" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> @lang('app.delete') </a></li>

                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">
                                                                <div class="empty-space" style="height: 200px;">
                                                                    <div class="empty-space-inner">
                                                                        <div class="icon" style="font-size:30px"><i
                                                                                    class="icon-layers"></i>
                                                                        </div>
                                                                        <div class="title m-b-15">@lang('messages.noDepartment')
                                                                        </div>
                                                                        <div class="subtitle">
                                                                            <a href="{{ route('admin.teams.create') }}" class="btn btn-outline btn-success btn-sm">@lang('app.add') @lang('app.team') <i class="fa fa-plus" aria-hidden="true"></i></a>

                                                                        </div>
                                                                    </div>
                                                                </div>
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

                
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
    <script>
        $(function() {


            $('body').on('click', '.sa-params', function(){
                var id = $(this).data('group-id');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted team!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {

                        var url = "{{ route('admin.teams.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'DELETE',
                            url: url,
                            data: {'_token': token},
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



        });

    </script>
@endpush
