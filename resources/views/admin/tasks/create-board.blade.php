<link rel="stylesheet" href="{{ asset('plugins/bower_components/lobipanel/dist/css/lobipanel.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<style>
div#asColorPicker-dropdown{max-width: 260px}
.asColorPicker-trigger{position:absolute;width:37px;height:30px}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.tasks.addBoardColumn')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        {!! Form::open(['id'=>'addColumn','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row" id="add-column-form">
                
                <div class="col-md-12">
                    <hr>
                    <div class="form-group">
                        <label class="control-label">@lang("modules.tasks.columnName")</label>
                        <input type="text" name="column_name" class="form-control">
                    </div>
                </div>
                <!--/span-->

                <div class="col-md-4">
                    <div class="form-group">
                        <label>@lang("modules.tasks.labelColor")</label><br>
                        <input type="text" class="colorpicker form-control"  name="label_color" value="#ff0000" />
                    </div>
                </div>
                <!--/span-->

            
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="save-column" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script src="{{ asset('plugins/bower_components/lobipanel/dist/js/lobipanel.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<!--slimscroll JavaScript -->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>

<script>
    $(".colorpicker").asColorPicker();
//    $('#addColumn').submit(function () {
//        $.easyAjax({
//            url: '{{route('admin.taskboard.store')}}',
//            container: '#addColumn',
//            type: "POST",
//            data: $('#addColumn').serialize(),
//            success: function (response) {
//                if(response.status == 'success'){
//                    window.location.reload();
//                }
//            }
//        })
//        return false;
//    })

    $('#save-column').click(function () {
        $.easyAjax({
            url: '{{route('admin.taskboard.store')}}',
            container: '#addColumn',
            type: "POST",
            data: $('#addColumn').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
</script>