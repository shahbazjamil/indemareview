<?php $data = $product->workroomObj; ?>
<!--{!! Form::open(['id'=>'tab-workroom','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="workroom">
<div style="display: none;" class="form-body">
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">@lang('app.product.workroom.vendorInstruction')</label>
        <textarea name="vendorInstruction" id="vendorInstruction" cols="30" rows="4" class="form-control" placeholder="@lang('app.product.item.descriptionPlaceholder')">{{$data->vendorInstruction}}</textarea>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">@lang('app.product.workroom.workroomVendor')</label>
        <input type="text" id="workroomVendor" name="workroomVendor" class="form-control" value="{{$data->workroomVendor}}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">@lang('app.product.workroom.shipFinishedProductTo')</label>
        <input type="text" id="shipFinishedProductTo" name="shipFinishedProductTo" class="form-control" value="{{$data->shipFinishedProductTo}}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">@lang('app.product.workroom.sidemark')</label>
        <input type="text" id="sidemark" name="sidemark" class="form-control" value="{{$data->sidemark}}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">@lang('app.product.workroom.workroomInstructions')</label>
        <textarea name="workroomInstructions" id="workroomInstructions" cols="30" rows="4" class="form-control" placeholder="@lang('app.product.item.descriptionPlaceholder')">{{$data->workroomInstructions}}</textarea>
      </div>
    </div>
  </div>
</div>
<!--{!! Form::close() !!}-->
