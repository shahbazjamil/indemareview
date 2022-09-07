@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-xs-12 p-t-10 p-b-10">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">

@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="white-box p-0">
             
                @section('filter-section')
                <div class="row" id="ticket-filters">
                   
                    <form action="" id="filter-form">
                        <div class="col-md-12">
                            <h5 >@lang('app.selectDateRange')</h5>
                            <div class="input-daterange input-group" id="date-range">
                                <input type="text" class="form-control" autocomplete="off" id="start-date" placeholder="@lang('app.startDate')"
                                       value=""/>
                                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                                <input type="text" class="form-control" id="end-date"  autocomplete="off" placeholder="@lang('app.endDate')"
                                       value=""/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h5 >@lang('app.project')</h5>
                            <div class="form-group">
                                {{--<label class="control-label">@lang('app.status')</label>--}}
                                <select class="form-control select2" name="projectID" id="projectID" data-style="form-control">
                                    <option value="all">@lang('app.all')</option>
                                    @forelse($projects as $project)
                                        <option value="{{$project->id}}">{{ ucfirst($project->project_name) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label col-xs-12">&nbsp;</label>
                                <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </form>
                </div>
                @endsection

                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="creditNoteUploadModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">@lang('modules.credit-notes.uploadInvoice')</span>
                </div>
                <div class="modal-body">
                    <div class="row" id="file-dropzone">
                        <div class="row m-b-20" id="file-dropzone">
                            <div class="col-md-12">
                                <form action="{{route('admin.creditNoteFile.store')}}"
                                      class="dropzone"
                                      id="file-upload-dropzone">
                                    {{ csrf_field() }}
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn blue" data-dismiss="modal">@lang('app.save')</button>
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
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    var table;
    var table;
    $(function() {
        $('#allCreditNote-table').on('preXhr.dt', function (e, settings, data) {
            var startDate = $('#start-date').val();

            if (startDate == '') {
                startDate = null;
            }

            var endDate = $('#end-date').val();

            if (endDate == '') {
                endDate = null;
            }

            // var status = $('#status').val();
            var projectID = $('#projectID').val();

            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['projectID'] = projectID;
        });
        jQuery('#date-range').datepicker({
            toggleActive: true,
            language: '{{ $global->locale }}',
            autoclose: true,
            weekStart:'{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('credit-notes-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted credit notes!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.all-credit-notes.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                window.LaravelDataTables["allCreditNote-table"].draw();
                            }
                        }
                    });
                }
            });
        });

        $('.table-responsive').on('click', '.credit-notes-upload', function(){
            var creditNoteId = $(this).data('credit-notes-id');
            console.log(creditNoteId);
            $('#file-upload-dropzone').prepend('<input name="credit_note_id", value="' + creditNoteId + '" type="hidden">');
        });
    });

    function loadTable(){
        window.LaravelDataTables["allCreditNote-table"].draw();
    }
    //    $('#file-upload-dropzone').dropzone({
    Dropzone.options.fileUploadDropzone = {
        paramName: "file", // The name that will be used to transfer the file
        dictDefaultMessage: "@lang('modules.projects.dropFile')",
        uploadMultiple: false,
        dictCancelUpload: "Cancel",
        accept: function (file, done) {
            done();
        },
        init: function () {
            this.on('addedfile', function(){
                if(this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
            this.on("success", function (file, response) {
            });
            var newDropzone = this;

            $('#creditNotesUploadModal').on('hide.bs.modal', function(){
                newDropzone.removeAllFiles(true);
            });

        }
    };
    //    });

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    });

    $('#apply-filters').click(function () {
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#projectID').val('all');
        // $('#status').selectpicker('render');
        $('#projectID').select2();

        loadTable();
    })

    function exportData(){

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        // var status = $('#status').val();
        var projectID = $('#projectID').val();

        var url = '{{ route('admin.all-credit-notes.export', [':startDate', ':endDate', ':projectID']) }}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':projectID', projectID);

        window.location.href = url;
    }

</script>
@endpush