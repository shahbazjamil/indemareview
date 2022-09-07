@extends('layouts.app')

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
                <li><a href="{{ route('admin.project-template.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.edit')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.css') }}">
<link rel="stylesheet"
      href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.skinModern.css') }}">
<style>
    .panel-black .panel-heading a, .panel-inverse .panel-heading a {
        color: unset!important;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading p-b-10 m-b-20"> Task Template Detail</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'updateProject','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body ">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Template Name</label>
                                        <p>{{ $template->template_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.title')</label>
                                        <p>{{ $template->heading }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                   <div class="form-group">
                                       <label class="control-label">@lang('app.description')</label>
                                       <p>{!! $template->description !!}</p>
                                   </div>
                               </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <div class="checkbox-info">
                                            <label for="private-task">@lang('modules.tasks.makePrivate')</label>
                                            @if ($template->is_private)
                                                <p>Yes</p>
                                            @else
                                                <p>No</p>
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                            <div class="col-md-3">
                                <div class="form-group">

                                    <div class="checkbox-info">
                                        <label for="billable-task">@lang('modules.tasks.billable')</label>
                                        @if ($template->billable)
                                            <p>Yes</p>
                                        @else
                                            <p>No</p>
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>
                                
                            </div>
                            
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.tasks.priority')</label>

                                    <div class="radio-danger">
                                        @if($template->priority == 'high')
                                        <label for="radio13" class="text-danger">@lang('modules.tasks.high') </label>
                                        @elseif($template->priority == 'medium')
                                         <label for="radio14" class="text-warning">
                                                @lang('modules.tasks.medium') </label>
                                        @else
                                        <label for="radio15" class="text-success">
                                                @lang('modules.tasks.low') </label>
                                        @endif
                                    </div>
                                   
                                </div>
                            </div>
                           
                            <!--/span-->
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

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
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/ion-rangeslider/js/ion-rangeSlider/ion.rangeSlider.min.js') }}"></script>
<script>
    checkTask();
    function checkTask()
    {
        var chVal = $('#client_view_task').is(":checked") ? true : false;
        if(chVal == true){
            $('#clientNotification').show();
        }
        else{
            $('#clientNotification').hide();
        }

    }

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.task-template.update', [$template->id])}}',
            container: '#updateProject',
            type: "POST",
            redirect: true,
            data: $('#updateProject').serialize()
        })
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

    $(':reset').on('click', function(evt) {
        evt.preventDefault()
        $form = $(evt.target).closest('form')
        $form[0].reset()
        $form.find('select').selectpicker('render')
    });

</script>

<script>
    $('#updateProject').on('click', '#addProjectCategory', function () {
        var url = '{{ route('admin.projectCategory.create-cat')}}';
        $('#modelHeading').html('Manage Project Category');
        $.ajaxModal('#projectCategoryModal', url);
    })
</script>
@endpush
