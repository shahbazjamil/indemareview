<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.update') Sales Category</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'editCategory','class'=>'ajax-form','method'=>'PUT']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="form-group">
                                <label>Status Name</label>
                                <input type="text" name="status_name" id="status_name" value="{{ $status->status_name }}" class="form-control">
                            </div>
                            
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            
                            <div class="form-group">
                                <label>Status Color</label>
                                <input type="text" name="status_color" id="status_color" value="{{ $status->status_color }}" class="form-control colorpicker">
                            </div>
                            
                        </div>
                        
                    </div>
                    
                    
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-group" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>


<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>

<script>
    
    $(".colorpicker").asColorPicker();

    $('#editCategory').on('submit', function(e) {
        return false;
    })

    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.product-status.update', $status->id)}}',
            container: '#editCategory',
            type: "PUT",
            data: $('#editCategory').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
</script>