@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
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
                <li><a href="{{ route('admin.clients.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.projects')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection


@section('content')

    <div class="row">


        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('admin.clients.tabs')

                    <div class="content-wrap">
                        <section id="section-line-1" class="show">
                            <div class="row">


                                <div class="col-md-12">
                                    <div class="white-box p-0">
                                        <h3 class="box-title b-b"><i class="fa fa-layers"></i> @lang('app.notes')</h3>
                                            <div class="col-xs-12" id="note-container">
                                                <div id="note-list">
                                                    @forelse($clientNotes as $note)
                                                        <div class="row b-b m-b-5 font-12">
                                                            <div class="col-xs-12 m-b-5">
                                                                <span class="font-semi-bold">{{ ucwords($note->user->name) }}</span> <span class="text-muted font-12">{{ ucfirst($note->created_at->diffForHumans()) }}</span>
                                                            </div>
                                                            <div class="col-xs-10">
                                                                {!! ucfirst($note->note)  !!}
                                                            </div>
                                                            <div class="col-xs-2 text-right">
                                                                <a href="javascript:;" data-comment-id="{{ $note->id }}" class="btn btn-xs  btn-outline btn-default" onclick="deleteNote('{{ $note->id }}');return false;"><i class="fa fa-trash"></i> @lang('app.delete')</a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-xs-12">
                                                            @lang('messages.noNoteFound')
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div class="form-group" id="note-box">
                                                <div class="col-xs-12 m-t-10">
                                                    <textarea name="note" id="task-note" class="summernote" placeholder="@lang('app.notes')"></textarea>
                                                </div>
                                                <div class="col-xs-12">
                                                    <a href="javascript:;" id="submit-note" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</a>
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
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        
    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,                 // set focus to editable area after initializing summernote,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen", "codeview"]]
        ]
    });
        
    function deleteNote (id) {

        var url = '{{ route("admin.client-note.destroy", ':id') }}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': '{{ csrf_token() }}', '_method': 'DELETE'},
            success: function (response) {
                if (response.status == "success") {
                    $('#note-list').html(response.view);
                }
            }
        })
    }
        
    $('#submit-note').click(function () {
        var note = $('#task-note').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: '{{ route("admin.client-note.store") }}',
            type: "POST",
            data: {'_token': token, note: note, clientId: '{{ $client->id }}'},
            success: function (response) {
                if (response.status == "success") {
                    $('#note-list').html(response.view);
                    $('.summernote').summernote("reset");
                    $('.note-editable').html('');
                    $('#task-note').val('');
                }
            }
        })
    })
        
        
        $('ul.showClientTabs .clientNotes').addClass('tab-current');
    </script>
@endpush