
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><i class="ti-eye"></i> @lang('modules.projects.milestones') @lang('app.details')</h4>
</div>
<div class="modal-body">
    <div class="modal-body">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="form-group">
                            <label>@lang('modules.projects.milestoneTitle')</label>
                            <p>
                                {{ $milestone->milestone_title }}
                            </p>
                        </div>
                    </div>
    
                </div>
    
                <div class="row">
                    <div class="col-xs-12 ">
                        <div class="form-group">
                            <label>@lang('modules.projects.milestoneSummary')</label>
                            <p>{{ $milestone->summary }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if(!is_null($milestone->currency_id))
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>@lang('modules.projects.milestoneCost')</label>
                                <p>
                                    {{ $milestone->currency->currency_symbol.$milestone->cost }}
                                </p>
                            </div>
                        </div>
                                
                    @endif

                    <div class="col-xs-6 col-md-3">
                        <div class="form-group">
                            <label>@lang('app.status')</label>
                            <p>
                                @if($milestone->status == 'incomplete') 
                                    <label class="label label-danger">@lang('app.incomplete')</label>
                                @else
                                    <label class="label label-success">@lang('app.complete')</label>
                                @endif
                            </p>
                        </div>
                    </div>
                    
    
                </div>
            </div>
    
        </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">Close</button>
</div>

