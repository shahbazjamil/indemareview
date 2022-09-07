<?php $data = $product->pricingObj; ?>
<!--{!! Form::open(['id'=>'tab-pricing','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="pricing">
<div class="form-body">
    
    
<div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label for="budgetAmount">Cost Per Unit {{ $global->currency->currency_symbol }}</label>
        <input type="text" id="cost_per_unit" name="cost_per_unit" class="form-control" value="{{$product->cost_per_unit ?$product->cost_per_unit :0}}" placeholder="">
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="form-group">
        <label for="default_markup">Default Markup 
<!--			<i class="fa fa-calculator"  data-toggle="collapse" href="#calculate-markup" aria-expanded="false" aria-controls="calculate-markup"></i>-->
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
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="budgetQty">Total Price {{ $global->currency->currency_symbol }}</label>
        <input type="text" id="msrp" name="msrp" class="form-control" value="{{$product->msrp}}" placeholder="">
      </div>
    </div>
</div>
    
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label" >Taxable</label>
            <input name="taxable" id="taxable" type="checkbox" @if($product->taxable == "yes") checked @endif"  />
    </div>
</div>

    

    
</div>
<!--{!! Form::close() !!}-->