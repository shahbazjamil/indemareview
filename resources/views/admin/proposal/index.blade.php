@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="border-bottom col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-4 col-sm-6 col-md-7 col-xs-12 text-right">
        <a href="javascript:AddScope()" class="btn btn-outline btn-success btn-sm">Add New Scope <i class="fa fa-plus"
                aria-hidden="true"></i></a>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li class="active">{{ __($pageTitle) }}</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>



{{-- Scope Modal --}}
<div id="ScopeModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write out a scope of work.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.proposal.store') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="task">Select Project:</label>
                        <input list="projects" class="form-control" name="project" /></label>
                        <datalist id="projects">
                            @foreach($projects as $project)
                            <option value="{{$project->project_name}}">
                                @endforeach
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label for="task">Proposal Name:</label>
                        <input type="text" name="taskField" class="form-control" id="task" required>
                    </div>
                    <div class="form-group">
                        <label for="hour">Amount of hours anticipated for task:</label>
                        <input type="number" name="hourField" min="0" class="form-control" id="hour" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">Estimated cost associated with task:</label>
                        <input type="number" name="costField" min="0.00" max="10000.00" class="form-control" id="cost"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="cost">Project Scope:</label>
                        <textarea class="form-control" name="project_scope" id="project_scope" cols="30"
                            rows="5"></textarea>

                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                {{-- <button type="submit" class="btn btn-primary">Send</button> --}}
            </div>
        </div>
    </div>
</div>
{{-- End Scope Modal --}}
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

<div class="row">
    <!-- List of Proposals -->
    <div class="col-md-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                    id="project-table">
                    <thead>
                        <tr>
                            @foreach($Columns as $i)
                            <th>{{ $i }}</th>
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Line Break -->
    <div class="col-md-12">
        <hr>
    </div>
</div>
<!-- .row -->

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
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script>
    fillDataTable();
    function fillDataTable()
    {
       $('#project-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.proposal.data') }}',
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            deferRender: true,
            columns: [
                { data: 'id'},
                { data: 'proposal_name'},
                { data: 'proposal_hour'},
                { data: 'proposal_task_cost'},
                { data: 'proposal_total_cost'},
                { data: 'project'},
            ]
        });
    }


function AddScope()
{
    $('#ScopeModal').modal('show');
}
</script>
@endpush