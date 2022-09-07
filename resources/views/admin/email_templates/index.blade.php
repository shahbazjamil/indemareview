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
                <div class="panel-heading p-t-10 p-b-10">{{ __($pageTitle) }}</div>
                <div class="vtabs customvtab m-t-10">

                    @include('sections.email_template_setting_menu')

                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row b-t m-t-20 p-10">
                                        <div class="col-md-12">
                                            <p class="border-bottom p-b-10 p-t-10">
                                                <a type="button" href="{{ route('admin.email-template.create') }}"
                                                   class="btn btn-success"><i
                                                            class="icon-plus"></i> Add new email template
                                                </a>
                                            </p>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                       id="emailTemplateTbl">
                                                    <thead>
                                                    <tr>
                                                        <th>@lang('app.id')</th>
                                                        <th>Template Name</th>
                                                        <th>@lang('app.date')</th>
                                                        <th>@lang('app.action')</th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
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
    <!-- .row -->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        let emailTemplateUrl = "{{ route('admin.email-template.index') }}";

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

        let table = $('#emailTemplateTbl').dataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            // destroy: true,
            'order': [[0, 'desc']],
            ajax: {
                url: '{!! route('admin.email-template.index') !!}',
            },
            columnDefs: [
                {
                    'targets': [0],
                },
                {
                    targets: '_all',
                    defaultContent: 'N/A',
                },
            ],
            columns: [
                {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'template_name',
                    name: 'template_name',
                },
                {
                    data: function (row){
                      return moment(row.created_at).format('Y-M-D');
                    },
                    name: 'created_at',
                },
                {
                    data: function (row) {
                        let url = emailTemplateUrl + '/' + row.id + '/edit';
                        return `
                                    <a href="${url}"  class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                                <a href="javascript:void();" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-email-template-id="${row.id}" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>`;
                    }, name: 'id',
                },
            ],
            "fnDrawCallback": function() {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
        });


        $('body').on('click', '.sa-params', function () {
            var id = $(this).data('email-template-id');
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
                    var url = "{{ route('admin.email-template.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'DELETE',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE', 'id' : id},
                        success: function (response) {
                            if (response.status === "success") {
                                $.unblockUI();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush

