@extends('layouts.app')
@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

    <style>
        .img-width {
            width: 185px;
        }
        .tabs-style-line nav a {
            box-shadow: unset !important;
        }
        .steamline .sl-left {
            margin-left: -7px !important;
        }
        .history-remove {
            display: none;
        }
        .sl-item:hover .history-remove {
            display: block;
        }
    </style>
@endpush
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.contracts.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('content')

    <div class="row">
        {!! Form::open(['id'=>'createContract','class'=>'ajax-form','method'=>'PUT']) !!}
        <div class="col-md-12">
            <div class="white-box p-0">
                <h3 class="box-title m-b-0 border-bottom p-b-10">{{ $contract->subject }}
                </h3>
				<div class="border-bottom p-b-10 p-t-10 m-b-10">
					<a href="{{ route('admin.contracts.show', md5($contract->id)) }}" target="_blank" class="btn btn-sm btn-default">View Contract</a>
				</div>
                <div class="sttabs tabs-style-line" id="invoice_container">
                    <nav>
                        <ul class="customtab" role="tablist" id="myTab">
                            <li class="nav-item active"><a class="nav-link btn-default" href="#summery" data-toggle="tab" role="tab"><span><i class="glyphicon glyphicon-file"></i> @lang('app.menu.contract')</span></a>
                            </li>

                            <li class="nav-item"><a class="nav-link btn-default" href="#renewval" data-toggle="tab" role="tab"><span><i class="fa fa-history"></i> @lang('modules.contracts.contractRenewalHistory')</span></a></li>
                        </ul>
                    </nav>
                    <div class="tab-content tabcontent-border">
                        <div class="tab-pane active" id="summery" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted m-b-30 font-13">You can use following variables in your contract template.</p>
									<div class="display:block">&lbrace;&lbrace;client.name&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;client.email&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;client.mobile&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;client.company_name&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;client.address&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;client.shipping_address&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;client.website&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;client.gst_number&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;designer.name&rbrace;&rbrace;</div>
									<div class="display:block">&lbrace;&lbrace;designer.email&rbrace;&rbrace;</div>
                                    <div class="form-group logo">
									<img src="{{ $global->logo_url }}" style="margin:0 auto 20px;width:122px;display:block"/>
                                <textarea name="contract_detail" id="contract_detail"
                                          class="summernote">{{ $contract->contract_detail }}</textarea>
                                    </div>
                                   
                                </div>
                            </div>
                            
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="text-left" id="signature-box">
                                            <h2 class="box-title">Signature (Client)</h2>
                                             @if($contract->client_signature)
                                             <img src="{{ $contract->client_signature->signature }}" class="img-width">
                                             @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-right" id="signature-box">
                                            <h2 class="box-title">Signature (Designer)</h2>
                                            @if($contract->signature)
                                            <img src="{{ $contract->signature->signature }}" class="img-width">
                                            @endif
                                        </div>
                                    </div>
                                   
                                </div>
                            
                        </div>
                        <div class="tab-pane" id="renewval" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" onclick="renewContract();return false;"><i class="fa fa-refresh"></i> @lang('modules.contracts.renewContract')</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row  slimscrolltab">
                                <div class="col-md-12">
                                    <div class="steamline">
                                        @foreach($contract->renew_history as $history)
                                            <div class="sl-item">
                                                <div class="sl-left"><i class="fa fa-circle text-info"></i>
                                                </div>
                                                <div class="sl-right">
                                                    <div class="pull-right history-remove">
                                                        <button class="btn btn-danger btn-circle btn-sm" onclick="removeHistory('{{ $history->id }}')"><i class="fa fa-trash"></i></button>
                                                    </div>
                                                    <div><h6><a href="javascript:;" class="text-danger">{{ $history->renewedBy->name }}
                                                                :</a> @lang('modules.contracts.renewedThisContract'): ({{ $history->created_at->timezone($global->timezone)->format($global->date_format) }} {{ $history->created_at->timezone($global->timezone)->format($global->time_format) }})</h6>
                                                        <span class="sl-date">@lang('modules.contracts.newStartDate'): {{ $history->start_date->timezone($global->timezone)->format($global->date_format) }}</span><br>
                                                        <span class="sl-date">@lang('modules.contracts.newEndDate') : {{ $history->end_date->timezone($global->timezone)->format($global->date_format) }}</span><br>
                                                        <span class="sl-date">@lang('modules.contracts.newAmount') : {{ $history->amount }}</span><br>
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
            </div>
        </div>

        <div class="col-md-12">
            <div class="white-box p-0">
                <h2 class="box-title- m-b-0 border-bottom">@lang('app.edit') @lang('app.menu.contract')</h2>

                <p class="text-muted m-b-30 font-13"></p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name">@lang('app.client')</label>
                            <div>
                                <select class="select2 form-control" data-placeholder="@lang('app.client')" name="client" id="clientID">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @if($client->id == $contract->client_id) selected @endif>{{ ucwords($client->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="subject">@lang('app.subject')</label>
                            <input type="text" class="form-control" id="subject" name="subject"  value="{{ $contract->subject ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="subject">@lang('app.amount') ({{ $global->currency->currency_symbol }})</label>
                            <input type="number" class="form-control" id="amount" name="amount" value="{{ $contract->amount ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.contracts.contractType')
                                <a href="javascript:;"
                                   id="createContractType"
                                   class="btn btn-sm btn-outline btn-success">
                                    + @lang('modules.contracts.addContractType')
                                </a>
                            </label>
                            <div>
                                <select class="select2 form-control" data-placeholder="@lang('app.client')" id="contractType" name="contract_type">
                                    @foreach($contractType as $type)
                                        <option
                                                value="{{ $type->id }}">{{ ucwords($type->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('modules.timeLogs.startDate')</label>
                            <input id="start_date" name="start_date" type="text"
                                   class="form-control"
                                   value="{{ $contract->start_date->timezone($global->timezone)->format($global->date_format) }}">
                        </div>
                    </div>
                    <div style="display: none" class="col-md-6">
                        <div class="form-group">
                            <label>@lang('modules.timeLogs.endDate')</label>
                            <input id="end_date" name="end_date" type="text"
                                   class="form-control"
                                   value="{{ $contract->start_date->timezone($global->timezone)->format($global->date_format) ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('modules.contracts.notes')</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ $contract->description ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                 
            </div>
        </div>
        <div class="col-md-12 text-center m-t-15 m-b-15">
            <a href="{{ route('admin.contracts.index') }}" class="btn btn-inverse waves-effect waves-light m-r-10">@lang('app.back')</a>
            <button type="submit" id="save-form" class="btn btn-success waves-effect waves-light">
                @lang('app.update')
            </button>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            let url = location.href.replace(/\/$/, "");

            if (location.hash) {
                const hash = url.split("#");
                $('#myTab a[href="#'+hash[1]+'"]').tab("show");
                url = location.href.replace(/\/#/, "#");
                history.replaceState(null, null, url);
                setTimeout(() => {
                    $(window).scrollTop(0);
                }, 400);
            }

            $('a[data-toggle="tab"]').on("click", function() {
                let newUrl;
                const hash = $(this).attr("href");
                if(hash == "#summery") {
                    newUrl = url.split("#")[0];
                } else {
                    newUrl = url.split("#")[0] + hash;
                }
                // newUrl += "/";
                history.replaceState(null, null, newUrl);
            });

            $('.slimscrolltab').slimScroll({
                height: '283px'
                , position: 'right'
                , size: "5px"
                , color: '#dcdcdc'
                , });
        });

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

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

        $("#start_date").datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart:'{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        }).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('#end_date').datepicker('setStartDate', maxDate);
        });

        jQuery('#end_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart: '{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        }).on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#start_date').datepicker('setEndDate', minDate);
        });

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.contracts.update', $contract->id)}}',
                container: '#createContract',
                type: "POST",
                redirect: true,
                data: $('#createContract').serialize()
            })
        });

        $('#createContractType').click(function(){
            var url = '{{ route('admin.contract-type.create-contract-type')}}';
            $('#modelHeading').html("@lang('modules.contracts.manageContractType')");
            $.ajaxModal('#taskCategoryModal', url);
        })

        function  renewContract() {
            var url = '{{ route('admin.contracts.renew', $contract->id)}}';
            $.ajaxModal('#taskCategoryModal',url);
        }

        function  removeHistory(id) {

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted renewal!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = '{{ route('admin.contracts.renew-remove', ':id') }}';
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token},
                        success: function (response) {
                            if(response.status == 'success') {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush

