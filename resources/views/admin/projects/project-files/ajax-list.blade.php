@forelse($files as $file)
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-8">
                {{ $file->filename }}
            </div>
            <div class="col-md-4">
            <?php
                $show_hide_cls = 'fa-eye';
                if ($file->file_hide == 1) {
                    $show_hide_cls = 'fa-eye-slash';
                }
            ?>
                <a data-fls="list" data-idd="{{$file->id}}" data-toggle="tooltip" data-original-title="Show/Hide" class="btn btn-default btn-circle show_hide_file"  href="javascript:void(0);">
                                                                        <i class="fa {{$show_hide_cls}}" aria-hidden="true"></i></a>

                    <a target="_blank" href="{{ $file->file_url }}"
                       data-toggle="tooltip" data-original-title="View"
                       class="btn btn-info btn-circle"><i
                                class="fa fa-search"></i></a>


                @if(is_null($file->external_link))
                <a href="{{ route('admin.files.download', $file->id) }}"
                   data-toggle="tooltip" data-original-title="Download"
                   class="btn btn-default btn-circle"><i
                            class="fa fa-download"></i></a>
                @endif

                <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}"
                   data-pk="list" class="btn btn-danger btn-circle sa-params"><i class="fa fa-times"></i></a>
                <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </li>
@empty
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-10">
                @lang('messages.noFileUploaded')
            </div>
        </div>
    </li>
@endforelse

<script>
    
    
     $('.show_hide_file').click(function () {
        var id = $(this).data('idd');
        var deleteView = $(this).data('fls');

        var url = "{{ route('admin.files.show-hide',':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";
        var folderID = '<?php echo $folder_id; ?>';
        
        $.easyAjax({
            type: 'POST',
                    url: url,
                    data: {'_token': token, 'view': deleteView , 'folder_id': folderID},
            success: function (response) {
                if (response.status == "success") {
                    $.unblockUI();
                    if(deleteView == 'list') {
                        $('#files-list-panel ul.list-group').html(response.html);
                    } else {
                        $('#thumbnail').empty();
                        $(response.html).hide().appendTo("#thumbnail").fadeIn(500);
                    }
                }
            }
        });
    });
    
</script>
