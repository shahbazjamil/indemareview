<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Sales Categories</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Category Markup %</th>
                        <th>@lang('app.action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($salescategories as $key=>$salescategory)
                        <tr id="status-{{ $salescategory->id }}">
                            <td>{{ ($key+1) }}</td>
                             <td>{{ ucwords($salescategory->salescategory_name) }}</td>
                             <td>{{ $salescategory->salescategory_markup ?? "0" }}</td>
                            <td><a href="javascript:;" data-status-id="{{ $salescategory->id }}" class="btn btn-sm btn-danger btn-rounded delete-status">@lang("app.remove")</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td>
                               No Sales Category added.
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
                        <label>Sales Category Code</label>
                        <input type="text" name="salescategory_code" id="salescategory_code" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Sales Category Name</label>
                        <input type="text" name="salescategory_name" id="salescategory_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Sales Category Markup %</label>
                        <input type="text" name="salescategory_markup" id="salescategory_markup" class="form-control">
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
        var url = "{{ route('admin.salescategoryTypes.destroy',':id') }}";
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
                        selectData = '<option value="'+value.salescategory_code+'">'+value.salescategory_name+'</option>';
                        options.push(selectData);
                    });

                    $('#salesCategory').html(options);
                    //$('#status').selectpicker('refresh');
                    
                }
            }
        });
    });
    
    $('#save-status').click(function () {
        $.easyAjax({
            url: '{{route('admin.salescategoryTypes.store-category')}}',
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
                            selectData = '<option value="'+value.salescategory_code+'">'+value.salescategory_name+'</option>';
                            options.push(selectData);
                        });

                        $('#salesCategory').html(options);
                        //$('#status').selectpicker('refresh');
                        $('#purchaseOrderStatusModal').modal('hide');
                    }
                }
            }
        })
    });
</script>