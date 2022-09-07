<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.addNew') Code Type</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'addCodeType','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>Location Code</label>
                        <input type="text" name="location_code" id="location_code" class="form-control">
                    </div>
                     <div class="form-group">
                        <label>Location Name</label>
                        <input type="text" name="location_name" id="location_name" class="form-control">
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

    $('#addCodeType').on('submit', function(e) {
        return false;
    })

    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.codeTypes.store')}}',
            container: '#addCodeType',
            type: "POST",
            data: $('#addCodeType').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    setValueInForm('code_id', response.optionData);
                    $.unblockUI();
                    $('#codeModal').modal('hide');
                }
            }
        })
    });
</script>