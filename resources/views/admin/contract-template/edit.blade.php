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
                <li><a href="{{ route('admin.contract-template.index') }}">{{ __($pageTitle) }}</a></li>
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
                <div class="panel-heading p-b-10 m-b-20"> @lang('modules.projectTemplate.updateTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0">
                        {!! Form::open(['id'=>'updateTemplate','class'=>'ajax-form','method'=>'PUT']) !!}
                        <div class="form-body ">
                            
                            <div class="row">
                                <div class="col-xs-12 ">
                                     
                                    <div class="form-group">
                                        
                                        <label>@lang('modules.contractTemplate.contractName')</label>
                                        <input type="text" name="template_name" id="template_name" class="form-control"
                                               value="{{ $template->template_name }}">
                                    </div>
                                </div>
                            </div>
                            
                            


                            <div class="row">
                                <div class="col-xs-12">

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
                                    
                                    <div class="form-group">
<!--                                        <label class="control-label">@lang('modules.contractTemplate.contractSummary')</label>-->
                                        <textarea name="template_summary" id="template_summary"
                                                  class="summernote">{{ $template->template_summary }}</textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="form-actions m-t-15">
                            <button type="submit" id="save-form" class="btn btn-success"><i
                                        class="fa fa-check"></i> @lang('app.update')</button>

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
   

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.contract-template.update', [$template->id])}}',
            container: '#updateTemplate',
            type: "POST",
            redirect: true,
            data: $('#updateTemplate').serialize()
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
@endpush
