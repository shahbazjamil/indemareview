<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.addNew') Purchase Order Status</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'addLeadStatus','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>Purchase Order Status</label>
                        <input type="text" name="type" id="type" class="form-control">
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

    $('#addLeadStatus').on('submit', function(e) {
        return false;
    })

    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.purchase-order-settings.store')}}',
            container: '#addLeadStatus',
            type: "POST",
            data: $('#addLeadStatus').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    setValueInForm('type_id', response.optionData);
                    $.unblockUI();
                    $('#ticketModal').modal('hide');
                }
            }
        })
    });
</script>