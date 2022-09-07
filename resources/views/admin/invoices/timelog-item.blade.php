@forelse ($timelogs as $item)

<?php

    $hourly_rate = $item->hourly_rate;
    if (is_null($item->hourly_rate)) {
           $hourly_rate = 0;           
    }
    
    if (!is_null($item->project_id) && !is_null($item->user_id)) {
        $projectMember = \App\ProjectMember::where('user_id', $item->user_id)->where('project_id', $item->project_id)->first();
        if ($projectMember) {
            $hourly_rate = $projectMember->hourly_rate;
        }
    }

    $hours = intdiv($item->total_minutes, 60);
    $minutes = 0;
    if (($item->total_minutes % 60) > 0) {
        $minutes = ($item->total_minutes % 60);
    }
    
    $minuteRate = $hourly_rate / 60;
    $earning = round($item->total_minutes * $minuteRate);
   
    $hours = $hours + round($minutes / 60, 2);
    $memo = $item->memo ? $item->memo : '';
  
?>

<?php if($earning > 0) { ?>

    <div class="col-xs-12 item-row margin-top-5 d-flex-border">
        @if (!is_null($item->task_id))
            <div class="col-md-2">
                <div class="row">
                    <div class="form-group">
                        <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>
                        <div class="input-group">
                            <div class="input-group-addon"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>
                            <input type="text" value="{{ $item->task->heading }}" readonly class="form-control item_name" name="item_name[]">
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2">{{ $memo }}</textarea>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-2">
                <div class="row">
                    <div class="form-group">
                        <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>
                        <div class="input-group">
                            <div class="input-group-addon"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>
                            <input type="text" value="{{ __('app.others') }}" readonly class="form-control item_name" name="item_name[]">
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2"></textarea>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>
                <input type="number" min="1" class="form-control quantity" value="{{ $hours }}" name="quantity[]" >
            </div>
        </div>
        <div class="col-md-2">
            <div class="row">
                <div class="form-group">
                    <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>
                    @if ($invoiceSetting->hide_amount_per_hour == 'yes')
                        <input style="display: none;" type="text"  class="form-control cost_per_item" name="cost_per_item[]" value="{{ $hourly_rate }}" >
                     @else
                        <input type="text"  class="form-control cost_per_item" name="cost_per_item[]" value="{{ $hourly_rate }}" >
                    @endif
                    
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.type')</label>
                <select id="multiselect" name="taxes[0][]"  multiple="multiple" class="selectpicker customSequence form-control type">
                    @foreach($taxes as $tax)
                        <option data-rate="{{ $tax->rate_percent }}" value="{{ $tax->id }}">{{ $tax->tax_name }}: {{ $tax->rate_percent }}%</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">Group</label>
                <select id="groupselect" name="groups[0]" class="selectpicker customSequenceGroup form-control type2">
                    <option value="">Nothing selected</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-md-2 border-dark  text-center">
            <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>
            <p class="form-control-static"><span class="amount-html">{{ $earning }}</span></p>
            <input type="hidden" class="amount" name="amount[]" value="{{ $earning }}">
        </div>
        <div class="col-md-1 text-right visible-md visible-lg">
            <button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>
        </div>
        <div class="col-md-1 hidden-md hidden-lg">
            <div class="row">
                <button type="button" class="btn remove-item btn-danger"><i class="fa fa-remove"></i> @lang('app.remove')</button>
            </div>
        </div>
    </div>    
<?php } ?>
@empty
@endforelse