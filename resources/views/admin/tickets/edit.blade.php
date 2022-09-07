@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="border-bottom col-xs-12 p-t-10 p-b-10">
            <span id="ticket-status" class="m-r-5">
                    <label class="label
                        @if($ticket->status == 'open')
                            label-danger
                    @elseif($ticket->status == 'pending')
                            label-warning
                    @elseif($ticket->status == 'resolved')
                            label-info
                    @elseif($ticket->status == 'closed')
                            label-success
                    @endif
                            ">{{ $ticket->status }}</label>
            </span>
            <span class="text-info- text-uppercase font-bold">@lang('modules.tickets.ticket') # {{ $ticket->id }}</span>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.tickets.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.update')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
    <style>
        .list-group-item a {
            color: #FFFFFF !important;
        }
    </style>
@endpush


@section('content')

    {!! Form::open(['id'=>'updateTicket2','class'=>'ajax-form updateTicket','method'=>'PUT', 'files' => true]) !!}
    <div class="form-body">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box p-0">
                    <div class="row">

                        <div class="col-md-12 m-b-10">
                            <h4 class="text-capitalize text-info">{{ $ticket->subject }}</h4>

                            <div class="font-12">{{ $ticket->created_at->timezone($global->timezone)->format($global->date_format.' '.$global->time_format) }} &bull; {{ ucwords($ticket->requester->name). ' <'.$ticket->requester->email.'>' }}</div>
                        </div>

                        {!! Form::hidden('status', $ticket->status, ['id' => 'status']) !!}

                    </div>
                    <!--/row-->

                    <div id="ticket-messages">

                        @forelse($ticket->reply as $reply)
                            <div class="panel-body p-0 @if($reply->user->id == $user->id) bg-owner-reply @else bg-other-reply @endif "
                                 id="replyMessageBox_{{$reply->id}}">

                                <div class="row m-b-5">

                                    <div class="col-xs-2 col-md-1">
                                        <img src="{{ $reply->user->image_url }}" alt="user" class="img-circle"
                                             width="40" height="40">
                                    </div>
                                    <div class="col-xs-8 col-md-10">
                                        <h5 class="m-t-0 font-bold">
                                            <a
                                                    @if($reply->user->hasRole('employee'))
                                                    href="{{ route('admin.employees.show', $reply->user_id) }}"
                                                    @elseif($reply->user->hasRole('client'))
                                                    href="{{ route('admin.clients.show', $reply->user_id) }}"
                                                    @endif
                                                    class="text-inverse">{{ ucwords($reply->user->name) }}
                                                <span class="text-muted font-12 font-normal">{{ $reply->created_at->timezone($global->timezone)->format($global->date_format.' '.$global->time_format) }}</span>
                                            </a>
                                        </h5>

                                        <div class="font-light">
                                            {!! ucfirst(nl2br($reply->message)) !!}
                                        </div>
                                    </div>

                                    <div class="col-xs-2 col-md-1">
                                        <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete"
                                           data-file-id="{{ $reply->id }}"
                                           class="btn btn-inverse btn-outline sa-params" data-pk="list"><i
                                                    class="fa fa-trash"></i></a>
                                    </div>


                                </div>
                                @if(sizeof($reply->files) > 0)
                                    <div class="row bg-white" id="list">
                                        <ul class="list-group" id="files-list">
                                            @forelse($reply->files as $file)
                                                <li class="list-group-item b-none col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            {{ $file->filename }}
                                                        </div>
                                                        <div class="col-md-4 text-right">

                                                            <a target="_blank" href="{{ $file->file_url }}"
                                                               data-toggle="tooltip" data-original-title="View"
                                                               class="btn btn-info btn-sm btn-outline"><i
                                                                        class="fa fa-search text-info"></i></a>


                                                            @if(is_null($file->external_link))
                                                                &nbsp;&nbsp;
                                                                <a href="{{ route('admin.ticket-files.download', $file->id) }}"
                                                                   data-toggle="tooltip" data-original-title="Download"
                                                                   class="btn btn-inverse btn-sm btn-outline"><i
                                                                            class="fa fa-download"></i></a>
                                                            @endif


                                                            <span class="clearfix font-12 text-muted">{{ $file->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            @lang('messages.noFileUploaded')
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforelse

                                        </ul>
                                    </div>
                                    <!--/row-->
                                @endif
                            </div>
                        @empty
                            <div class="panel-body p-0">

                                <div class="row">

                                    <div class="col-md-12">
                                        @lang('messages.noMessage')
                                    </div>

                                </div>
                                <!--/row-->

                            </div>
                        @endforelse
                    </div>

                    <div class="row m-t-10">
                        <div class="col-md-12">
                            <button class="btn btn-default btn-sm waves-effect waves-light" id="reply-toggle" type="button"><i class="fa fa-mail-reply"></i> @lang('app.reply')
                            </button>
                        </div>
                    </div>

                    <div id="reply-section" style="display: none;">

                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.tickets.reply') </label></label>
                                    <textarea class="textarea_editor form-control" rows="10" name="message"
                                              id="message"></textarea>
                                </div>
                            </div>
                            <!--/span-->
                        </div>
                        <!--/row-->
                        <div class="row m-b-20">
                            <div class="col-md-12">
                                @if($upload)
                                    <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                                    <div id="file-upload-box" >
                                        <div class="row" id="file-dropzone">
                                            <div class="col-md-12">
                                                <div class="dropzone"
                                                     id="file-upload-dropzone">

                                                    <div class="fallback">
                                                        <input name="file" type="file" multiple/>
                                                    </div>
                                                    <input name="image_url" id="image_url"type="hidden" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="ticketIDField" id="ticketIDField">
                                @else
                                    <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <div class="btn-group dropup m-r-10">
                                    <button aria-expanded="true" data-toggle="dropdown"
                                            class="btn btn-info btn-outline dropdown-toggle waves-effect waves-light"
                                            type="button"><i class="fa fa-bolt"></i> @lang('modules.tickets.applyTemplate')
                                        <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                        @forelse($templates as $template)
                                            <li><a href="javascript:;" data-template-id="{{ $template->id }}"
                                                   class="apply-template">{{ ucfirst($template->reply_heading) }}</a>
                                            </li>
                                        @empty
                                            <li>@lang('messages.noTemplateFound')</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="btn-group dropup">
                                    <button aria-expanded="true" data-toggle="dropdown"
                                            class="btn btn-success dropdown-toggle waves-effect waves-light"
                                            type="button">@lang('app.submit') <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu pull-right">
                                        <li>
                                            <a href="javascript:;" class="submit-ticket" data-status="open">@lang('app.submit')
                                                as Open
                                                <span style="width: 15px; height: 15px;"
                                                      class="btn btn-danger btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" class="submit-ticket"
                                               data-status="pending">@lang('app.submit') as @lang('app.pending')
                                                <span style="width: 15px; height: 15px;"
                                                      class="btn btn-warning btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" class="submit-ticket"
                                               data-status="resolved">@lang('app.submit') as Resolved
                                                <span style="width: 15px; height: 15px;"
                                                      class="btn btn-info btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" class="submit-ticket"
                                               data-status="closed">@lang('app.submit') as Closed
                                                <span style="width: 15px; height: 15px;"
                                                      class="btn btn-success btn-small btn-circle">&nbsp;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
        <!-- .row -->
    </div>
    {!! Form::close() !!}

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="ticketModal" role="dialog" aria-labelledby="myModalLabel"
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
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>

    <script>

        //Dropzone class
        @if($upload)
            Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("div#file-upload-dropzone", {
            url: "{{ route('admin.ticket-files.store') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*,application/pdf",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            init: function () {
                myDropzone = this;
                this.on("success", function (file, response) {
                    if(response.status == 'fail') {
                        $.showToastr(response.message, 'error');
                        return;
                    }
                })
            }
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            console.log(myDropzone.getAddedFiles().length,'sending');
            var ids = $('#ticketIDField').val();
            formData.append('ticket_reply_id', ids);
        });

        myDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.taskCreatedSuccessfully')";
            $.showToastr(msgs, 'success');
            window.location.reload();
        });
        @endif
        $('.textarea_editor').wysihtml5();

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#reply-toggle').click(function () {
            $('#reply-toggle').hide();
            $('#reply-section').show();
        })

        $('.apply-template').click(function () {
            var templateId = $(this).data('template-id');
            var token = '{{ csrf_token() }}';

            $.easyAjax({
                url: '{{route('admin.replyTemplates.fetchTemplate')}}',
                type: "POST",
                data: {_token: token, templateId: templateId},
                success: function (response) {
                    if (response.status == "success") {
                        var editorObj = $("#message").data('wysihtml5');
                        var editor = editorObj.editor;
                        editor.setValue(response.replyText);
                    }
                }
            })
        })


        $('.submit-ticket').click(function () {

            var status = $(this).data('status');
            $('#status').val(status);

            $.easyAjax({
                url: '{{route('admin.tickets.update', $ticket->id)}}',
                container: '#updateTicket2',
                type: "POST",
                data: $('#updateTicket2').serialize(),
                file: true,
                success: function (response) {
                    var dropzone = 0;
                    @if($upload)
                        dropzone = myDropzone.getQueuedFiles().length;
                    @endif

                    if(dropzone > 0){
                        $('#ticketIDField').val(response.ticketReplyID);
                        myDropzone.processQueue();
                    }
                    else{
                        var msgs = "@lang('messages.ticketAddSuccess')";
                        $.showToastr(msgs, 'success');
                    }

                    $('#scroll-here').remove();

                    if(response.lastMessage != null){
                        $('#ticket-messages').append(response.lastMessage);
                    }
                    $('#message').data("wysihtml5").editor.clear();

                    // update status on top
                    if(status == 'open')
                        $('#ticket-status').html('<label class="label label-danger">'+status+'</label>');
                    else if(status == 'pending')
                        $('#ticket-status').html('<label class="label label-warning">'+status+'</label>');
                    else if(status == 'resolved')
                        $('#ticket-status').html('<label class="label label-info">'+status+'</label>');
                    else if(status == 'closed')
                        $('#ticket-status').html('<label class="label label-success">'+status+'</label>');

                    scrollChat();

                }
            })
        });

        $('.submit-ticket-2').click(function () {

            $.easyAjax({
                url: '{{route('admin.tickets.updateOtherData', $ticket->id)}}',
                container: '#updateTicket1',
                type: "POST",
                data: $('#updateTicket1').serialize()
            })
        });

        $('#add-type').click(function () {
            var url = '{{ route("admin.ticketTypes.createModal")}}';
            $('#modelHeading').html("{{ __('app.addNew').' '.__('modules.tickets.ticketTypes') }}");
            $.ajaxModal('#ticketModal', url);
        })

        $('#add-channel').click(function () {
            var url = '{{ route("admin.ticketChannels.createModal")}}';
            $('#modelHeading').html("{{ __('app.addNew').' '.__('modules.tickets.ticketTypes') }}");
            $.ajaxModal('#ticketModal', url);
        })

        function setValueInForm(id, data) {
            $('#' + id).html(data);
            $('#' + id).selectpicker('refresh');
        }

        function scrollChat() {
            $('#ticket-messages').animate({
                scrollTop: $('#ticket-messages')[0].scrollHeight
            }, 'slow');
        }

        scrollChat();

        $('body').on('click', '.sa-params', function () {
            var id = $(this).data('file-id');
            var deleteView = $(this).data('pk');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted reply!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.tickets.reply-delete',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'GET',
                        url: url,
                        success: function (response) {
                            console.log(response);
                            if (response.status == "success") {
                                $.unblockUI();
                                $('#replyMessageBox_' + id).fadeOut();
                                /* if(response.lastMessage != null){
                                     $('#ticket-messages').append(response.lastMessage);
                                 }*/

                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush