<div class="col-xs-12 item-row margin-top-5 d-flex-border">
    
    <div class="col-md-1">
		<div class="hd">@lang('app.product.tabs.picture')</div>
        <div class="form-group">
            <label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>
            <p class="form-control-static">
                <label>
                    <input type="file" class="product_img" style="display: none" name="product_img[]">
                    <img src="{{ $fileUrl }}" alt="product">
                </label>
            </p>
            <input type="hidden" class="form-control" data-item-id="{{ $items->id }}" value="{{ $fileName }}" name="picture[]" >
            <input type="hidden" class="form-control" data-item-id="{{ $items->id }}" value="{{ $items->id }}" name="product_id[]" >
        </div>
    </div>
    
    <div class="@if($cal_from == 'invoice') col-md-1  @else col-md-2  @endif">
		<div class="hd">@lang('modules.invoices.item')</div>
        <div class="row">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>
                <div class="input-group">
                    <div class="input-group-addon"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>
                    <input type="text" class="form-control item_name" name="item_name[]"
                           value="{{ $items->name }}" >
                </div>
            </div>
            <div class="form-group">
                <textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2">{{ $items->itemObj->description ?? "" }}</textarea>
            </div>
        </div>
    </div>
    @if($cal_from == 'invoice')
        <div class="col-md-1">
		<div class="hd">Type</div>
            <div class="row">
                <div class="form-group">
                    <label class="control-label hidden-md hidden-lg">Type</label>
                    <input type="text" readonly="" class="form-control" name="invoice_item_type[]" data-item-id="{{ $items->id }}" value="product" placeholder="{{ $items->invoice_item_type }}">
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-1 text-right visible-md visible-lg border-right-0">
	<div class="hd">Action</div>
        <button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>
    </div>
	<div class="w-100"></div>
    <div class="col-md-1">
		<div class="hd">@lang('modules.invoices.qty')</div>
        <div class="form-group">
            <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>
            <input type="number" min="1" class="form-control quantity" data-item-id="{{ $items->id }}" value="{{ $items->quantity }}" name="quantity[]" >
        </div>
    </div>

    <div class="col-md-1">
		<div class="hd">@lang('modules.invoices.unitPrice')</div>
        <div class="row">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>
                <input type="text" class="form-control cost_per_item" name="cost_per_item[]" data-item-id="{{ $items->id }}" value="{{ $items->price }}">
            </div>
        </div>
    </div>
    
    <div class="col-md-1">
		<div class="hd">Markup %</div>
        <div class="row">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">Markup %</label>
                <input type="number" step=any class="form-control markup" name="markup[]" data-item-id="{{ $items->id }}" value="{{ $items->default_markup }}" placeholder="0.00">
                <span class="help-block markup_txt" data-item-id="{{ $items->id }}" > 
                    @if ($items->default_markup_fix)
                    {{$global->currency->currency_symbol}} ({{ $items->default_markup_fix }})
                    @endif
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-1">
	<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>
        <div class="row">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">Markup fix</label>
                <input type="number" step=any class="form-control markup_fix" name="markup_fix[]" data-item-id="{{ $items->id }}" value="{{ $items->markup_fix }}" placeholder="0.00">
            </div>
        </div>
    </div>
    
    <div class="col-md-1">
	<div class="hd">Sale Price</div>
        <div class="row">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">Sale Price</label>
                <input type="number" step=any class="form-control sale_price" name="sale_price[]" data-item-id="{{ $items->id }}" value="" placeholder="0.00">
            </div>
        </div>
    </div>
    <div class="col-md-1">
	<div class="hd">Shipping</div>
        <div class="row">
            <div class="form-group">
                <label class="control-label hidden-md hidden-lg">Shipping Price</label>
                <input type="number" step=any class="form-control shipping_price" name="shipping_price[]" data-item-id="{{ $items->id }}" value="{{ $items->freight ? $items->freight : ''  }}" placeholder="0.00">
            </div>
        </div>
    </div>

    <div class="col-md-1">
	<div class="hd">@lang('modules.invoices.tax')</div>

        <div class="form-group">
            <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.type')</label>
            <select id="" name=""  multiple="multiple" class="selectpicker form-control type">
                @foreach($taxes as $tax)
                    <option data-rate="{{ $tax->rate_percent }}"
                            @if (isset($items->taxes) && array_search($tax->id, json_decode($items->taxes)) !== false)
                            selected
                            @endif
                            value="{{ $tax->id }}">{{ $tax->tax_name }}: {{ $tax->rate_percent }}%</option>
                @endforeach
            </select>
        </div>
    </div>
        
    <div class="col-md-1">
    <div class="hd">Group</div>
        <div class="form-group">
            <label class="control-label hidden-md hidden-lg">Group</label>
            <select id="" name="" class="selectpicker form-control type type2">
                <option value="">Nothing selected</option>
                @foreach($groups as $group)
                    <option @if (isset($items->group_id) && $group->id == $items->group_id)
                            selected
                            @endif
                            value="{{ $group->id }}">{{ $group->group_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-1 border-dark  text-center">
	<div class="hd">@lang('modules.invoices.amount')</div>
        <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>

        <p class="form-control-static"><span class="amount-html" data-item-id="{{ $items->id }}">0</span></p>
        <input type="hidden" class="amount" name="amount[]" data-item-id="{{ $items->id }}">
    </div>

    <div class="col-xs-12 text-center hidden-md hidden-lg">
        <div class="row">
            <button type="button" class="btn btn-circle remove-item btn-danger"><i class="fa fa-remove"></i></button>
        </div>
    </div>

    <script>
        $(function () {
//            var quantity = parseInt($('#sortable').find('.quantity[data-item-id="{{ $items->id }}"]').val());
//            var perItemCost = parseFloat($('#sortable').find('.cost_per_item[data-item-id="{{ $items->id }}"]').val());
//            var amount = (quantity*perItemCost);
//            $('#sortable').find('.amount[data-item-id="{{ $items->id }}"]').val(amount);
//            $('#sortable').find('.amount-html[data-item-id="{{ $items->id }}"]').html(amount);


            
            var quantity = parseInt($('#sortable').find('.quantity[data-item-id="{{ $items->id }}"]').val());
            var perItemCost = parseFloat($('#sortable').find('.cost_per_item[data-item-id="{{ $items->id }}"]').val());
            
            var markup = 0;
            if($('#sortable').find('.markup[data-item-id="{{ $items->id }}"]').val()) {
                markup = $('#sortable').find('.markup[data-item-id="{{ $items->id }}"]').val();
            }
            
            var markup_fix = 0;
            if($('#sortable').find('.markup_fix[data-item-id="{{ $items->id }}"]').val()) {
                markup_fix = $('#sortable').find('.markup_fix[data-item-id="{{ $items->id }}"]').val();
                markup_fix = markup_fix*1;
            }
            
            
            
            var shipping_price = 0;
            if($('#sortable').find('.shipping_price[data-item-id="{{ $items->id }}"]').val()) {
                shipping_price = parseFloat($('#sortable').find('.shipping_price[data-item-id="{{ $items->id }}"]').val());
            }

            var amount_1 = (quantity*perItemCost);  
            
            if(markup_fix != '' && markup_fix != 0 && markup_fix != '0.00') {
                var sale_price_cal = decimalupto2(amount_1+markup_fix).toFixed(2);
            } else {
                var sale_price_cal = decimalupto2(amount_1+((markup/100)*amount_1)).toFixed(2); // Sale = Qty + markup + unit cost 
            }
            
            
            $('#sortable').find('.sale_price[data-item-id="{{ $items->id }}"]').val(sale_price_cal)
            var sale_price = parseFloat($('#sortable').find('.sale_price[data-item-id="{{ $items->id }}"]').val());
            
            //var amount = (quantity*perItemCost);
            var amount = (sale_price + shipping_price);
            
            $('#sortable').find('.amount[data-item-id="{{ $items->id }}"]').val(decimalupto2(amount).toFixed(2));
            $('#sortable').find('.amount-html[data-item-id="{{ $items->id }}"]').html(decimalupto2(amount).toFixed(2));
            
            

            calculateTotal();
        });
    </script>
</div>
