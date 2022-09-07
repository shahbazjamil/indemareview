<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.addNew') Client</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'addClientForm','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label class="required">@lang('modules.client.clientName')</label>
                        <input type="text" name="name" id="name"  value=""   class="form-control">
                    </div>
                </div>
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label class="required">@lang('modules.client.clientEmail')</label>
                        <input type="email" name="email" id="email" value=""  class="form-control">
                        <span class="help-block">@lang('modules.client.emailNote')</span>
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

    // Store lead source
    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.clients.store-client')}}',
            container: '#addClientForm',
            type: "POST",
            data: $('#addClientForm').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    var options = [];
                    var rData = [];
                    rData = response.optionData;
                    $('#client_id').html(rData);
                    $("#client_id").select2();
                    $('#projectCategoryModal').modal('hide');
                }
            }
        })
    });
</script>