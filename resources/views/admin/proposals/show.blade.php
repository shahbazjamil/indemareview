@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $lead->id }} - <span
                        class="font-bold">{{ ucwords($lead->company_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.leads.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('modules.projects.files')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box p-0">
                        <nav>
                            <ul>
                                <li ><a href="{{ route('admin.leads.show', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.profile')</span></a>
                                </li>
                                <li class="tab-current"><a href="{{ route('admin.proposals.show', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.proposal')</span></a></li>
                                <li ><a href="{{ route('admin.lead-files.show', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.file')</span></a></li>
                                <li ><a href="{{ route('admin.leads.followup', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.followUp')</span></a></li>
                                @if($gdpr->enable_gdpr)
                                    <li><a href="{{ route('admin.leads.gdpr', $lead->id) }}" class="btn-default"><span>GDPR</span></a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="files-list-panel">
                                    <div class="white-box p-0">
                                        <h2 class="border-bottom">@lang('modules.proposal.title')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12">
                                                <div class="white-box p-0">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group border-bottom p-b-10">
                                                                <a href="{{ route('admin.proposals.create') }}/{{$lead->id}}" class="btn btn-outline btn-success btn-sm">+ @lang('modules.proposal.createTitle') </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="proposal-table">
                                                            <thead>
                                                            <tr>
                                                                <th>@lang('app.id')</th>
                                                                <th>@lang('app.lead')</th>
                                                                <th>@lang('modules.invoices.total')</th>
                                                                <th>@lang('modules.estimates.validTill')</th>
                                                                <th>@lang('app.status')</th>
                                                                <th>@lang('app.action')</th>
                                                            </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
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

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script>

    $("body").tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    var table = $('#proposal-table').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.proposals.data', [$lead->id]) !!}',
        "order": [[ 0, "desc" ]],
        language: {
            "url": "<?php echo __("app.datatable") ?>"
        },
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'company_name', name: 'company_name' },
            { data: 'total', name: 'total' },
            { data: 'valid_till', name: 'valid_till' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', width: '5%' }
        ]
    });

    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('proposal-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted proposal!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.proposals.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });

</script>
@endpush