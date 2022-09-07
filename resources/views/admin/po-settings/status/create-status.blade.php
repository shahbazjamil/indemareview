<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Purchase Order Status</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('app.name')</th>
                        <th>@lang('app.action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($poStatus as $key=>$status)
                        <tr id="status-{{ $status->id }}">
                            <td>{{ ($key+1) }}</td>
                            <td>{{ ucwords($status->type) }}</td>
                            <td><a href="javascript:;" data-status-id="{{ $status->id }}" class="btn btn-sm btn-danger btn-rounded delete-status">@lang("app.remove")</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td>
                                @lang('messages.nopoStatusAdded')
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
        </div>

        <hr>
        {!! Form::open(['id'=>'createTypes','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                         <label>Status</label>
                        <input type="text" name="type" id="type" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-status" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>

    $('.delete-status').click(function () {
        var id = $(this).data('status-id');
        var url = "{{ route('admin.purchase-order-settings.destroy',':id') }}";
        url = url.replace(':id', id);
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token, '_method': 'DELETE'},
            success: function (response) {
                if (response.status == "success") {
                    $.unblockUI();
                    $('#status-'+id).fadeOut();
                    
                    var options = [];
                    var rData = [];
                    rData = response.data;
                    $.each(rData, function( index, value ) {
                        var selectData = '';
                        selectData = '<option value="'+value.id+'">'+value.type+'</option>';
                        options.push(selectData);
                    });

                    $('#status').html(options);
                    //$('#status').selectpicker('refresh');
                    
                }
            }
        });
    });
    
    $('#save-status').click(function () {
        $.easyAjax({
            url: '{{route('admin.purchase-order-settings.store-status')}}',
            container: '#createTypes',
            type: "POST",
            data: $('#createTypes').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    if(response.status == 'success'){
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        $.each(rData, function( index, value ) {
                            var selectData = '';
                            selectData = '<option value="'+value.id+'">'+value.type+'</option>';
                            options.push(selectData);
                        });

                        $('#status').html(options);
                        //$('#status').selectpicker('refresh');
                        $('#purchaseOrderStatusModal').modal('hide');
                    }
                }
            }
        })
    });
</script>