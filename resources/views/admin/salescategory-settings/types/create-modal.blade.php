<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.addNew') Sales Category</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'addCategory','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>Sales Category Code</label>
                        <input type="text" name="salescategory_code" id="salescategory_code" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Sales Category Name</label>
                        <input type="text" name="salescategory_name" id="salescategory_name" class="form-control">
                    </div>
                     <div class="form-group">
                        <label>Sales Category Markup%</label>
                        <input type="text" name="salescategory_markup" id="salescategory_markup" class="form-control">
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

    $('#addCategory').on('submit', function(e) {
        return false;
    })

    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.salescategoryTypes.store')}}',
            container: '#addCategory',
            type: "POST",
            data: $('#addCategory').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    setValueInForm('code_id', response.optionData);
                    $.unblockUI();
                    $('#categoryModal').modal('hide');
                }
            }
        })
    });
</script>