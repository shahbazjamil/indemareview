<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.addNew') Vendor</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'addVendorForm','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label class="required">Company Name</label>
                        <input type="text" name="company_name" id="company_name"  value=""   class="form-control">
                    </div>
                </div>
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label class="required">Vendor Name</label>
                        <input type="text" name="vendor_name" id="vendor_name" value=""  class="form-control">
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
            url: '{{route('member.vendor.store-vendor')}}',
            container: '#addVendorForm',
            type: "POST",
            data: $('#addVendorForm').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    var options = [];
                    var rData = [];
                    rData = response.optionData;
                    $('#vendor_id').html(rData);
                    $("#vendor_id").change();
                    $('#purchaseOrderStatusModal').modal('hide');
                }
            }
        })
    });
</script>