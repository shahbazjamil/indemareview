<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.update') Code Type</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'editCodeType','class'=>'ajax-form','method'=>'PUT']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>Location Code</label>
                        <input type="text" name="location_code" id="location_code" value="{{ $code->location_code }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Location Name</label>
                        <input type="text" name="location_name" id="location_name" value="{{ $code->location_name }}" class="form-control">
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

<script>

    $('#editCodeType').on('submit', function(e) {
        return false;
    })

    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.codeTypes.update', $code->id)}}',
            container: '#editCodeType',
            type: "PUT",
            data: $('#editCodeType').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
</script>