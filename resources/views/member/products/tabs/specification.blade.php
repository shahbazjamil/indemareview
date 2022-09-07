<?php $data = $product->specificationObj; ?>
<!--{!! Form::open(['id'=>'tab-specification','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="specification">
<div class="form-body">
  <div class="row">
      <div style="display: none;" class="col-md-12">
      <div class="form-group">
        <label class="control-label">SKU</label>
        <input type="text" id="sku" name="sku" class="form-control" value="{{ $product->sku }}" >
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Manufacturer</label>
        <input type="text" id="manufacturer" name="manufacturer" class="form-control" value="{{ $product->manufacturer }}" >
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Materials</label>
        <input type="text" id="materials" name="materials" class="form-control" value="{{ $product->materials }}" >
      </div>
    </div>
      <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Dimensions</label>
        <input type="text" id="dimensions" name="dimensions" class="form-control" value="{{ $product->dimensions }}" >
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Finish / Color</label>
        <input type="text" id="finish_color" name="finish_color" class="form-control" value="{{ $product->finish_color }}" >
      </div>
    </div>
      
    <div class="col-md-6">
      <div class="form-group">
          <label for="spec_number" class="control-label">Spec #</label>
        <input type="text" id="spec_number" name="spec_number" class="form-control" value="{{ $product->spec_number }}" >
      </div>
    </div>
      
    <div class="col-md-6">
      <div class="form-group">
          <label for="quantity" class="control-label">Quantity</label>
          <input type="number" id="quantity" name="quantity" class="form-control" value="{{ $product->quantity ? $product->quantity : 1 }}" >
      </div>
    </div>
    
      
    <div style="display: none" class="col-md-6">
      <div class="form-group">
        <label class="control-label">@lang('app.product.specification.specTemplateNumber') :</label>
        <input type="text" id="specTemplateNumber" name="specTemplateNumber" class="form-control" value="{{ $data->specTemplateNumber }}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
    <div style="display: none;" class="col-md-6">
      <div class="form-group">
        <label class="control-label">@lang('app.product.specification.source') :</label>
        <input type="text" id="source" name="source" class="form-control" value="{{ $data->source }}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
      
    <div style="display: none" class="col-md-6">
      <div class="form-group">
        <label class="control-label">@lang('app.product.specification.planNumber') :</label>
        <input type="text" id="planNumber" name="planNumber" class="form-control" value="{{ $data->planNumber }}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
  </div>

<div style="display: none;">

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="checkbox checkbox-info">
          <input id="material" name="material" value="true" type="checkbox" {{ $data->material ? 'checked' : '' }}>
          <label for="material">@lang('app.product.specification.material')</label>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">@lang('app.product.specification.attributes') :</label>
      </div>
    </div>
  </div>
  <hr />

  <div class="row">
    @foreach (array(0, 1) as $col)
    <div class="col-md-6">
      <table class="table">
        <tr>
          <th><label class="control-label">@lang('app.product.specification.title') :</label></th>
          <th><label class="control-label">@lang('app.product.specification.description') :</label></th>
        </tr>
        @foreach (array(0, 1, 2, 3, 4) as $index)
        <tr>
          <td>
            <input type="text"
              id="attributes-{{$index * 2 + $col}}-title"
              name="attrs[{{$index * 2 + $col}}][title]"
              class="form-control"
              value="{{$data->attrs->{$index * 2 + $col}->title}}"
              placeholder="@lang('app.typeHere')">
          </td>
          <td>
            <input type="text"
              id="attributes-{{$index * 2 + $col}}-description"
              name="attrs[{{$index * 2 + $col}}][description]"
              class="form-control"
              value="{{$data->attrs->{$index * 2 + $col}->description}}"
              placeholder="@lang('app.typeHere')">
          </td>
        </tr>
        @endforeach
      </table>
    </div>
    @endforeach
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">@lang('app.product.specification.specialInstruction') :</label>
        <textarea name="instruction" id="instruction" cols="30" rows="4" class="form-control" placeholder="@lang('app.typeHere')">{{ $data->instruction }}</textarea>
      </div>
    </div>
  </div>
</div>
</div>
<!--{!! Form::close() !!}-->