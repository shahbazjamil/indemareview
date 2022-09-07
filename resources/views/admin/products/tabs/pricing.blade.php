<?php $data = $product->pricingObj; ?>
<!--{!! Form::open(['id'=>'tab-pricing','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="pricing">
<div class="form-body">
<!--    <div style="display: none;" class="row">
    <div class="col-md-6">
      <div class="form-group">
        <div class="radio-list">
          <label class="radio-inline p-0">
            <div class="radio radio-info">
              <input type="radio" name="method" id="radio-estimate" value="estimate" {{$data->method == 'estimated' ? 'checked' : ''}}>
              <label for="radio-estimate">@lang('app.product.pricing.estimate')</label>
            </div>
          </label>
          <label class="radio-inline p-0">
            <div class="radio radio-info">
              <input type="radio" name="method" id="radio-actual" value="actual" {{$data->method == 'actual' ? 'checked' : ''}}>
              <label for="radio-actual">@lang('app.product.pricing.actual')</label>
            </div>
          </label>
          <label class="radio-inline p-0">
            <div class="radio radio-info">
              <input type="radio" name="method" id="radio-billing" value="billing" {{$data->method == 'billing' ? 'checked' : ''}}>
              <label for="radio-billing">@lang('app.product.pricing.billing')</label>
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>-->
    
<!--<div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="budgetAmount">Cost Per Unit {{ $global->currency->currency_symbol }}</label>
        <input type="text" id="cost_per_unit" name="cost_per_unit" class="form-control" value="{{$product->cost_per_unit ?$product->cost_per_unit :0}}" placeholder="">
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group">
        <label for="default_markup">Default Markup 
			<i class="fa fa-calculator"  data-toggle="collapse" href="#calculate-markup" aria-expanded="false" aria-controls="calculate-markup"></i>
			<div class="collapse" id="calculate-markup">
			  <button type="button" class="close" data-dismiss="collapse" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <div class="well">
				<h3>Calculate Markup</h3>
				<div class="form-group">
					<label for="budgetQty">Target price per unit</label>
					<input type="text" id="markup-price" name="markup-price" class="form-control" value="" placeholder="{{ $global->currency->currency_symbol }}">
					<button type="button" class="btn btn-primary">Set % Markup</button>
				</div>
			  </div>
			</div>
		</label> %
        <input type="text" id="default_markup" name="default_markup" class="form-control" value="{{$product->default_markup ? $product->default_markup: 0}}" placeholder="">
        <input type="hidden" id="default_markup_fix" name="default_markup_fix" value="{{$product->default_markup_fix ? $product->default_markup_fix: 0}}" placeholder="">
        <span class="help-block" id="default_markup_txt"> {{ $global->currency->currency_symbol }} ({{$product->default_markup_fix ? $product->default_markup_fix: 0}})  </span>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <label for="budgetQty">Total Price {{ $global->currency->currency_symbol }}</label>
        <input type="text" id="msrp" name="msrp" class="form-control" value="{{$product->msrp}}" placeholder="">
      </div>
    </div>
</div>-->
    
<!--<div class="col-md-12">
    <div class="form-group">
        <label class="control-label" >Taxable</label>
            <input name="taxable" id="taxable" type="checkbox" @if($product->taxable == "yes") checked @endif"  />
    </div>
</div>-->


<!--    <div style="display: none;" class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="budgetAmount">@lang('app.product.pricing.budgetAmount')</label>
        <input type="text" id="budgetAmount" name="budgetAmount" class="form-control" value="{{$data->budgetAmount}}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="budgetQty">@lang('app.product.pricing.budgetQty')</label>
        <input type="text" id="budgetQty" name="budgetQty" class="form-control" value="{{$data->budgetQty}}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
  </div>-->
</div>
<!--{!! Form::close() !!}-->