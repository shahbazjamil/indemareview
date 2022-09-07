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
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
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

<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box p-0">
                        <nav>
                            <ul>
                                <li class="tab-current"><a href="{{ route('admin.leads.show', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.profile')</span></a>
                                </li>
                                <!--<li><a href="{{ route('admin.proposals.show', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.proposal')</span></a></li>-->
                                <li ><a href="{{ route('admin.lead-files.show', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.file')</span></a></li>
                                <li><a href="{{ route('admin.leads.followup', $lead->id) }}" class="btn-default"><span>@lang('modules.lead.followUp')</span></a></li>
                                @if($gdpr->enable_gdpr)
                                    <li><a href="{{ route('admin.leads.gdpr', $lead->id) }}" class="btn-default"><span>GDPR</span></a></li>
                                @endif
                                <li><a href="{{ route('admin.leads.audit', $lead->id) }}" class="btn-default"><span>@lang('modules.audit.audit')</span></a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="files-list-panel">
                                    <div class="white-box p-0">
                                        <h2 class="border-bottom">@lang('modules.lead.leadDetail')</h2>

                                        <div class="white-box p-0">
                                            <div class="row">
                                                <div class="col-md-2 b-r"> <strong>Lead Status</strong> <br>
                                                    <p class="text-muted">{{ $lead->lead_status ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-2 b-r"> <strong>Sales Code</strong> <br>
                                                    <p class="text-muted">{{ $lead->sales_code ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-2 b-r"> <strong>Lead Value</strong> <br>
                                                    <p class="text-muted">{{ $lead->lead_value ?? '-'}}</p>
                                                </div>
                                                @if($lead->source_id != null)
                                                <div class="col-md-6"> <strong>Lead Source</strong> <br>
                                                    <p class="text-muted">{{ $lead->lead_source->type ?? '-'}}</p>
                                                </div>
                                                @endif
                                               
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6 b-r"> <strong>Shipping Address</strong> <br>
                                                    <p class="text-muted">{{ $lead->shipping_address ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-6"> <strong>Billing Address</strong> <br>
                                                    <p class="text-muted">{{ $lead->address ?? '-'}}</p>
                                                </div>
                                            </div>
											
                                        <h2 class="border-bottom">Lot Detail</h2>
                                            
                                            <div class="row">
                                                <div class="col-md-6 b-r"> <strong>Lot Address</strong> <br>
                                                    <p class="text-muted">{{ $lead->lot_address ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-3 b-r"> <strong>Square Feet</strong> <br>
                                                    <p class="text-muted">{{ $lead->square_feet ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-3"> <strong>Gate Code</strong> <br>
                                                    <p class="text-muted">{{ $lead->gate_code ?? '-'}}</p>
                                                </div>
                                            </div>
                                        <h2 class="border-bottom">CONTACTS</h2>
                        
                                            <div class="row">
                                                <div class="col-md-6 b-r"> <strong>Person 1 Name</strong> <br>
                                                    <p class="text-muted">{{ $lead->client_name ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-3 b-r"> <strong>Person 1 Email</strong> <br>
                                                    <p class="text-muted">{{ $lead->client_email ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-3"> <strong>Person 1 Phone</strong> <br>
                                                    <p class="text-muted">{{ $lead->mobile ?? '-'}}</p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6 b-r"> <strong>Person 2 Name</strong> <br>
                                                    <p class="text-muted">{{ $lead->person_name_2 ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-3 b-r"> <strong>Person 2 Email</strong> <br>
                                                    <p class="text-muted">{{ $lead->person_email_2 ?? '-'}}</p>
                                                </div>
                                                <div class="col-md-3"> <strong>Person 2 Phone</strong> <br>
                                                    <p class="text-muted">{{ $lead->person_mobile_2 ?? '-'}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                         {{--Custom fields data--}}
                                        @if(isset($fields))
                                            <div class="row">
                                                <hr>
                                                @foreach($fields as $field)
                                                    <div class="col-md-4">
                                                        <strong>{{ ucfirst($field->label) }}</strong> <br>
                                                        <p class="text-muted">
                                                            @if( $field->type == 'text')
                                                                {{$lead->custom_fields_data['field_'.$field->id] ?? '-'}}
                                                            @elseif($field->type == 'password')
                                                                {{$lead->custom_fields_data['field_'.$field->id] ?? '-'}}
                                                            @elseif($field->type == 'number')
                                                                {{$lead->custom_fields_data['field_'.$field->id] ?? '-'}}

                                                            @elseif($field->type == 'textarea')
                                                                {{$lead->custom_fields_data['field_'.$field->id] ?? '-'}}

                                                            @elseif($field->type == 'radio')
                                                                {{ !is_null($lead->custom_fields_data['field_'.$field->id]) ? $lead->custom_fields_data['field_'.$field->id] : '-' }}
                                                            @elseif($field->type == 'select')
                                                                {{ (!is_null($lead->custom_fields_data['field_'.$field->id]) && $lead->custom_fields_data['field_'.$field->id] != '') ? $field->values[$lead->custom_fields_data['field_'.$field->id]] : '-' }}
                                                            @elseif($field->type == 'checkbox')
                                                                {{ !is_null($lead->custom_fields_data['field_'.$field->id]) ? $field->values[$lead->custom_fields_data['field_'.$field->id]] : '-' }}
                                                            @elseif($field->type == 'date')
                                                            
                                                                {{ (isset($lead->custom_fields_data['field_'.$field->id]) && $lead->custom_fields_data['field_'.$field->id] !='') ? \Carbon\Carbon::createFromFormat('d, M Y', $lead->custom_fields_data['field_'.$field->id])->format($global->date_format) : ''}}
                                                            @endif
                                                        </p>

                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{--custom fields data end--}}
                                        
                                        
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
                console.log(response);
                $('#files-list-panel ul.list-group').html(response.html);
            })
        }
    };

    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('file-id');
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

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            $('#files-list-panel ul.list-group').html(response.html);

                        }
                    }
                });
            }
        });
    });

</script>
@endpush
