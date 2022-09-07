<?php $data = $product->purchaseOrderObj; ?>
<div class="modal fade bs-modal-md in" id="item-purchase-order" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">Purchase Order Components</span>
      </div>
      <div class="modal-body">
        <h3 class="text-center">Purchase Order Description</h3>
        <hr />
<!--        {!! Form::open(['id'=>'tab-purchaseOrder','class'=>'ajax-form','method'=>'POST']) !!}-->
        <input type="hidden" name="tabId" value="purchaseOrder">
        <div class="form-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">@lang('app.type') :</label>
                <select name="type" id="type" class="form-control">
                  @foreach (Config::get('products.salesCategory') as $value => $category)
                  <option value="{{$value}}" {{ $data->type == $value ? 'selected' : '' }}>{{ucfirst($category)}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">@lang('app.product.item.vendor') :</label>
                <input type="text" id="vendor" name="vendor" class="form-control" value="{{ $data->vendor }}" placeholder="@lang('app.typeHere')">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">@lang('app.product.item.shipTo') :</label>
                <input type="text" id="shipTo" name="shipTo" class="form-control" value="{{ $data->shipTo }}" placeholder="@lang('app.typeHere')">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 m-b-2">
              <label class="control-label">% @lang('app.type') :</label>
              <select name="purchaseType" id="purchaseType" class="form-control">
                @foreach (Config::get('products.purchaseType') as $type)
                <option value="{{$type}}" {{ $data->purchaseType == $value ? 'selected' : '' }}>{{ucfirst($type)}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-2">
              <input type="text" id="calcValue1" name="calcValue1" class="form-control" value="{{ $data->calcValue1 }}" placeholder="% ???">
            </div>
            <div class="col-md-1">
              <h4 class="text-center">x</h4>
            </div>
            <div class="col-md-2">
              <input type="text" id="calcValue2" name="calcValue2" class="form-control" value="{{ $data->calcValue2 }}" placeholder="@lang('app.product.item.qty')">
            </div>
            <div class="col-md-1">
              <h4 class="text-center">=</h4>
            </div>
            <div class="col-md-3">
              <input type="text" id="calcValue3" name="calcValue3" class="form-control" value="{{ $data->calcValue3 }}" placeholder="@lang('app.product.item.estCost')">
            </div>
            <div class="col-md-1">
              <h4 class="text-center">+</h4>
            </div>
            <div class="col-md-2">
              <input type="text" id="calcValue4" name="calcValue4" class="form-control" value="{{ $data->calcValue4 }}" placeholder="% ???">
            </div>
          </div>

          <div class="row">
            <div class="col-md-11">
              <hr />
            </div>
            <div class="col-md-1">
              <h4 class="text-center">=</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-dollar"></i>
                  </span>
                  <input type="text" id="calcTotal" name="calcTotal" class="form-control" value="{{ $data->calcTotal }}">
                </div>
              </div>
            </div>
          </div>

<!--          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <div class="checkbox checkbox-info">
                  <input id="addTax" name="addTax" value="true" type="checkbox" {{ $data->addTax ? 'checked' : '' }}>
                  <label for="addTax">@lang('app.product.item.addTax')</label>
                </div>
              </div>
            </div>
          </div>-->

          <div class="row">
            <div class="col-md-12">
              <table class="table table-bordered">
                <tr>
                  <td>@lang('app.product.item.freight')</td>
                  <td class="no-padding"><input type="text" id="freight" name="freight" class="form-control" value="{{ $data->freight }}"></td>
                </tr>
                <tr>
                  <td>@lang('app.product.item.designFee')</td>
                  <td class="no-padding"><input type="text" id="designFee" name="designFee" class="form-control" value="{{ $data->designFee }}"></td>
                </tr>
                <tr>
                  <td>@lang('app.product.item.additionalCharges')</td>
                  <td class="no-padding"><input type="text" id="additionalCharges" name="additionalCharges" class="form-control" value="{{ $data->additionalCharges }}"></td>
                </tr>
                <tr>
                  <td rowspan="2"> </td>
                  <td class="no-padding"><input type="text" id="total1" name="total1" class="form-control" value="{{ $data->total1 }}"></td>
                </tr>
                <tr>
                  <td class="no-padding">
                    <div class="input-group">
                      <span class="input-group-addon">
                        @lang('app.product.item.total') <i class="fa fa-dollar"></i>
                      </span>
                      <input type="text" id="total2" name="total2" class="form-control" value="{{ $data->total2 }}">
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
<!--        {!! Form::close() !!}-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn default" id="submit-modal">@lang('app.save')</button>
        <button type="button" class="btn blue" data-dismiss="modal" >@lang('app.cancel')</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>