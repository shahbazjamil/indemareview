<div class="modal-header">
<!--    <button type="button" class="close close-line-md" aria-hidden="true">×</button>-->
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Groups</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Group Name</th>
                    <th>Group Description</th>
                </tr>
                </thead>
                <tbody>
                @forelse($groups as $key=>$group)
                    <tr id="group-{{ $group->id }}">
                        <td>{{ $key+1 }}</td>
                        <td>{{ ucwords($group->group_name) }}</td>
                        <td>{{ $group->group_desc }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">@lang('messages.noRecordFound')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <hr>
        {!! Form::open(['id'=>'createGroup','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-6 ">
                    <div class="form-group">
                        <label>Group Name</label>
                        <input type="text" name="group_name" id="group_name" class="form-control">
                    </div>
                </div>
                <div class="col-xs-6 ">
                    <div class="form-group">
                        <label>Group Description</label>
                        <input type="text" name="group_desc" id="group_desc" class="form-control">
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
    $('#createGroup').submit(function () {
        $.easyAjax({
            url: '{{route('admin.line-tem-groups.store')}}',
            container: '#createGroup',
            type: "POST",
            data: $('#createGroup').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
        return false;
    })

    $('.group-tax').click(function () {
        var id = $(this).data('group-id');
        var url = "{{ route('admin.line-tem-groups.destroy',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token, '_method': 'DELETE'},
            success: function (response) {
                if (response.status == "success") {
                    $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                    $('#group-'+id).fadeOut();
                }
            }
        });
    });

    $('#save-group').click(function () {
        $.easyAjax({
            url: '{{route('admin.line-tem-groups.store')}}',
            container: '#createGroup',
            type: "POST",
            data: $('#createGroup').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
    
    $('.close-line-md').on('click', function (event) {
            $('#taxModal').hide()
    });
    
</script>