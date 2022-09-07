<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Location Notes</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'createLocationNotes','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            {!! Form::hidden('locationt_id', $locationtId) !!}
            <div class="row"> 
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Notes</label>
                        <textarea id="note" name="note" class="form-control summernote"></textarea>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-notes" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script>

  
    
    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]],
        ]
    });

    $('#save-notes').click(function () {
        $.easyAjax({
            url: '{{route('client.product-review-project.store-location-notes')}}',
            container: '#createLocationNotes',
            type: "POST",
            data: $('#createLocationNotes').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
</script>