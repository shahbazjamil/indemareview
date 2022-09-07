<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">

<div class="panel panel-default">
    <div class="panel-heading "><i class="ti-pencil"></i> @lang('app.update') @lang('modules.projects.milestones')
        <div class="panel-action">
            <a href="javascript:;" class="close" id="hide-edit-milestone-panel" data-dismiss="modal"><i class="ti-close"></i></a>
        </div>
    </div>
    <div class="panel-wrapper collapse in">
        <div class="panel-body">
            {!! Form::open(['id'=>'updateMilestone','class'=>'ajax-form','method'=>'PUT']) !!}
            {!! Form::hidden('project_id', $milestone->project_template_id) !!}

            <div class="form-body">
                                        <div class="row m-t-30">
                                            
                                            <div class="col-md-6 ">
                                                <div class="form-group">
                                                    <label>@lang('modules.projects.milestoneTitle')</label>
                                                    <input id="milestone_title" name="milestone_title" type="text"
                                                class="form-control" value="{{ $milestone->milestone_title }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 ">
                                                    <div class="form-group">
                                                        <label>@lang('app.status')</label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option 
                                                            @if($milestone->status == 'incomplete') selected @endif
                                                            value="incomplete">@lang('app.incomplete')</option>
                                                            <option 
                                                            @if($milestone->status == 'complete') selected @endif
                                                            value="complete">@lang('app.complete')</option>
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="col-md-3 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.invoices.currency')</label>
                                                        <select name="currency_id" id="currency_id" class="form-control">
                                                            <option value="">--</option>
                                                            @foreach ($currencies as $item)
                                                                <option 
                                                                @if($item->id == $milestone->currency_id) selected @endif
                                                                value="{{ $item->id }}">{{ $item->currency_code.' ('.$item->currency_symbol.')' }}</option>           
                                                            @endforeach
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="col-md-3 ">
                                                <div class="form-group">
                                                    <label>@lang('modules.projects.milestoneCost')</label>
                                                    <input id="cost" name="cost" type="number" value="{{ $milestone->cost }}"
                                                           class="form-control" value="0" min="0" step=".01">
                                                </div>
                                            </div>
                                            
                                        </div>
                                        

                                        <div class="row m-t-20">
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label for="memo">@lang('modules.projects.milestoneSummary')</label>
                                                    <textarea name="summary" id="" rows="4" class="form-control">{{ $milestone->summary }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
            <div class="form-actions">
                <button type="button" id="update-task" onclick="updateMilestone(); return false;" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script>

    $("select.select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    //    update task
    function updateMilestone(){
        $.easyAjax({
            url: '{{route('member.project-template-milestone.update', [$milestone->id])}}',
            container: '#updateMilestone',
            type: "POST",
            data: $('#updateMilestone').serialize(),
            success: function (data) {
                $('#edit-milestone-panel').switchClass("show", "hide", 300, "easeInOutQuad");
                showTable();
            }
        })
    }

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
            ["view", ["fullscreen"]]
        ]
    });
</script>
