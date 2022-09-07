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
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('client.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('modules.projects.discussion')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<style>
    #projects-table_wrapper .dt-buttons, #projects-table thead{
        display: none !important;
    }

    #projects-table tbody td{
        /* border: none; */
        padding: 15px 8px;
    }

    #projects-table.table-hover>tbody>tr:hover {
        background-color: #f7fafc!important;
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

                                <li><a href="{{ route('client.files.show', $project->id) }}"><span>@lang('modules.projects.files')</span></a></li>

                                @if(in_array('timelogs',$modules))
<!--                                <li><a href="{{ route('client.time-log.show', $project->id) }}"><span>@lang('app.menu.timeLogs')</span></a></li>-->
                                @endif
                                
                                <li ><a href="{{ route('client.product-review-project.show', $project->id) }}"><span>Product Review</span></a></li>

                                @if(in_array('invoices',$modules))
                                <li><a href="{{ route('client.project-invoice.show', $project->id) }}"><span>@lang('app.menu.invoices')</span></a></li>
                                @endif
                                <li class="tab-current">
                                    <a href="{{ route('client.projects.discussion', $project->id) }}"></i>
                                        <span>@lang('modules.projects.discussion')</span></a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div class="content-wrap">
                        <section class="show" id="discussion">
                            <div class="white-box p-0">
                                <div class="row">
                                    <div class="col-md-3 ">


                                        <div class="row" id="categories-area">
                                            <div class="col-xs-12">
                                                <ul class="discussion-categories">
                                                    <li class="active">
                                                        <a href="javascript:;" class="text-dark" data-category-id="">
                                                            <i class="fa fa-circle-o"></i> 
                                                            @lang('app.all') @lang('modules.projects.discussion')
                                                        </a>
                                                    </li>
                                                    @foreach ($discussionCategories as $item)
                                                        <li>
                                                            <a href="javascript:;" data-category-id="{{ $item->id }}" style="color: {{ $item->color }}"><i class="fa fa-circle-o"></i> {{ ucwords($item->name) }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-9">
                                      

                                        <div class="row">
                                            <div class="col-md-12">
                                                    <input type="hidden" id="project_id" value="{{ $project->id }}">
                                                    <input type="hidden" id="category_id">
                                    
                                                    <div class="table-responsive">
                                                        {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                                                    </div>
                                            </div>
                                        </div>
                                        <!-- .row -->

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
    <div class="modal fade bs-modal-md in" id="editTimeLogModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
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
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>


    function showData() {
        $('#projects-table').on('preXhr.dt', function (e, settings, data) {
            var projectId = $('#project_id').val();
            var categoryId = $('#category_id').val();

            data['project_id'] = projectId;
            data['category_id'] = categoryId;
        });

        window.LaravelDataTables["projects-table"].draw();
    }

    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('milestone-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted milestone!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.milestones.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '#add-discussion', function () {
        var url = '{{ route('client.discussion.create', ['id' => $project->id]) }}';

        $('#modelHeading').html('{{ __('app.edit') }} {{ __('modules.projects.milestones') }}');
        $.ajaxModal('#editTimeLogModal', url);

    });

    $('body').on('click', '.edit-category', function () {
        var categoryId = $(this).data('category-id');
        var url = '{{ route('admin.discussion-category.edit', ':id') }}';
        url = url.replace(':id', categoryId);

        $('#modelHeading').html('{{ __('app.edit') }} {{ __('modules.projects.discussion') }}');
        $.ajaxModal('#editTimeLogModal', url);
    });

    $('.discussion-categories li a').click(function () {
        $('.discussion-categories li').removeClass('active');
        $(this).closest('li').addClass('active');

        var categoryId = $(this).data('category-id');
        $('#category_id').val(categoryId);
        showData();
    });

   
    $('ul.showProjectTabs .discussion').addClass('tab-current');
</script>
@endpush
