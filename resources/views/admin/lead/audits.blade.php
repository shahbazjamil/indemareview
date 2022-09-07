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
<style>
    .activities .activity .activity-detail{
        padding: 6px 12px;
        background: #FAFAFA;
        border: 1px solid rgb(227, 227, 227);
        border-radius: 10px;
        margin-bottom: 20px;
        position: relative;
    }
    /*.activities .activity .activity-detail:before{*/
    /*    content: ' ';*/
    /*    position: absolute;*/
    /*    left: 296px;*/
    /*    top: 63px;*/
    /*    width: 2px;*/
    /*    height: 33%;*/
    /*    background-color: #ccc !important;*/
    /*}*/
    .activities .activity .activity-col{
        display: flex;
        align-items: center;
    }
    .activities .activity .activity-time-div{
        padding: 5px;
        padding-left: 30px;
    }
    .activities .activity .activity-icon-div .activity-icon i{
        font-size: 30px;
    }
    .activities .activity .activity-icon-div-arrow{
        border: 1px solid #ccc;
        width: 22px;
        transform: rotate(90deg);
        position: absolute;
        margin-top: 83px;
        margin-left: 3px;
    }
    .activities .activity .green{
        color: #34d517;
    }
    .activities .activity .orange{
        color: orange;
    }
</style>
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
                                        <div class="white-box p-0">
                                            <div class="row justify-content-start">
                                                <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                    <div class="activities">
                                                        <div class="activity-div">
                                                            <div class="activity">
                                                                @forelse($audits as $audit)
                                                                    <div class="activity-detail d-flex">
                                                                        <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 d-flex">
                                                                            <div class="activity-time-div">
                                                                                @php
                                                                                    $deliverAt = new \DateTime($audit->deliver_at, new \DateTimeZone("UTC"));
                                                                                    $deliverAt->setTimezone(new \DateTimeZone("Asia/Kolkata"));
                                                                                    $deliverAt = $deliverAt->format("Y-m-d H:i:s");
                                                                                    $deliverAt = \Carbon\Carbon::parse($deliverAt);
                                                                                    $convertDeliverAt = date("g:i:s A", strtotime($deliverAt));
                                                                                @endphp
                                                                                <span>{{ \Carbon\Carbon::parse($audit->deliver_at)->format('l, F d') }},</span><br>
                                                                                <span>{{ \Carbon\Carbon::parse($audit->deliver_at)->format('Y') }}, {{ $convertDeliverAt }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 activity-col">
                                                                            <div class="activity-icon-div">
                                                                                <span class="activity-icon">
                                                                                    <i class="fa fa-{{ isset($audit->icon) && !is_null($audit->icon) ? $audit->icon : 'envelope green' }}"></i>
                                                                                </span>
                                                                            </div>
                                                                            @if(!$loop->last)
                                                                                <div class="activity-icon-div-arrow"></div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-xs-6 col-sm-6 col-md-9 col-lg-9 activity-col">
                                                                            <div class="activity-title-div">
                                                                                <span class="activity-title">{!! $audit->title !!}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <div class="text-center">
                                                                        <h3>No audit yet...</h3>
                                                                    </div>
                                                                @endforelse
                                                            </div>
                                                        </div>
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
