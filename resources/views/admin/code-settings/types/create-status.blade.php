<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Location Codes</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Location Name</th>
                        <th>@lang('app.action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($codeTypes as $key=>$codeType)
                        <tr id="status-{{ $codeType->id }}">
                            <td>{{ ($key+1) }}</td>
                            <td>{{ ucwords($codeType->location_name) }}</td>
                            <td><a href="javascript:;" data-status-id="{{ $codeType->id }}" class="btn btn-sm btn-danger btn-rounded delete-status">@lang("app.remove")</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td>
                                No Location Code added.
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
            <button type="button" id="save-status" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>

    $('.delete-status').click(function () {
        var id = $(this).data('status-id');
        var url = "{{ route('admin.codeTypes.destroy',':id') }}";
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
                        selectData = '<option value="'+value.location_code+'">'+value.location_name+'</option>';
                        options.push(selectData);
                    });

                    $('#locationCode').html(options);
                    //$('#status').selectpicker('refresh');
                    
                }
            }
        });
    });
    
    $('#save-status').click(function () {
        $.easyAjax({
            url: '{{route('admin.codeTypes.store-type')}}',
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
                            selectData = '<option value="'+value.location_code+'">'+value.location_name+'</option>';
                            options.push(selectData);
                        });

                        $('#locationCode').html(options);
                        //$('#status').selectpicker('refresh');
                        $('#purchaseOrderStatusModal').modal('hide');
                    }
                }
            }
        })
    });
</script>