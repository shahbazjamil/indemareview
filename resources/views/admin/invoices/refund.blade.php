<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datetime-picker/datetimepicker.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="fa fa-undo"></i> Refund {{ucfirst($invoice->invoice_number)}}</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                @if($invoice->status == 'paid')
                {!! Form::open(['id'=>'invoiceRefund','class'=>'ajax-form','method'=>'POST']) !!}
                <div class="form-body">
                    <div class="row m-t-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('modules.payments.amount')</label>
                                    <input type="number" name="amount" id="amount" value="{{ max(($invoice->total),0) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label>Method of refund</label>
                                    <select class="form-control select2" name="gateway" data-placeholder="Select Gateway"  id="gateway">
                                        <option value="">--</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Check">Check</option>
                                        <option value="ACH">ACH</option>
                                        <option value="Credit on account">Credit on account</option>
                                        <option value="Original payment method">Original payment method</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label">Refund On</label>
                                    <input type="text" class="form-control" name="paid_on" id="paid_on" value="{{ Carbon\Carbon::now($global->timezone)->format('d/m/Y H:i') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="control-label">Remarks</label>
                                    <input type="text" class="form-control" name="remarks" id="remarks" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <input type="hidden" name="project_id" id="project_id" value="{{$project_id}}" >
                <div class="form-actions m-t-30">
                    <button type="button" id="update-form" class="btn btn-success"><i class="fa fa-check"></i> Submit
                    </button>
                </div>
                {!! Form::close() !!}
                @else
                
                    <div class="custom-alerts alert alert-danger fade in">
                        You can only refund paid invoices.
                    </div>
                
                @endif

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/datetime-picker/datetimepicker.js') }}"></script>


<script>
    
    $('#gateway').select2();
    $("#gateway").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    
    jQuery('#paid_on').datetimepicker({
        format: 'D/M/Y HH:mm',
    });

    $('#update-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.all-invoices.refund-update', $invoice->id)}}',
            container: '#invoiceRefund',
            type: "POST",
            data: $('#invoiceRefund').serialize(),
            success: function (response) {
                //$('#offlinePaymentDetails').modal('hide');
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
</script>