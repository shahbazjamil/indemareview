<!--{!! Form::open(['id'=>'tab-status','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="status">
<div class="form-body">
  <div class="row">
    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">@lang('app.status') :</label>
        <select name="status" id="status" class="form-control">
          @foreach (Config::get('products.status') as $status)
          <option value="{{$status}}">{{ucfirst($status)}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group m-t-6">
        <div class="checkbox checkbox-info">
          <input id="pricing_inactive" name="pricing_inactive" value="no" type="checkbox">
          <label for="pricing_inactive">@lang('app.product.status.inactive')</label>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group m-t-6">
        <div class="checkbox checkbox-info">
          <input id="pricing_completed" name="pricing_completed" value="no" type="checkbox">
          <label for="pricing_completed">@lang('app.product.status.completed')</label>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group m-t-6">
        <div class="checkbox checkbox-info">
          <input id="pricing_bypassWip" name="pricing_bypassWip" value="no" type="checkbox">
          <label for="pricing_bypassWip">@lang('app.product.status.bypassWip')</label>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <h3>@lang('app.product.status.proposal')</h3>
      <table class="table">
        <thead>
          <tr>
            <th>@lang('app.product.status.proposalNo')</th>
            <th>@lang('app.name')</th>
            <th>@lang('app.date')</th>
            <th>@lang('app.product.status.recDeposit')</th>
            <th>@lang('app.product.status.clientCheckNo')</th>
            <th>@lang('app.product.status.checkDate')</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <h3>@lang('app.invoice')</h3>
      <table class="table">
        <thead>
          <tr>
            <th>@lang('app.product.status.invNo')</th>
            <th>@lang('app.date')</th>
            <th>@lang('app.product.status.qty')</th>
            <th>@lang('app.amount')</th>
            <th>@lang('app.product.status.depositApp')</th>
            <th>@lang('app.product.status.salesTax')</th>
            <th>@lang('app.total')</th>
            <th>@lang('app.product.status.payment')</th>
            <th>@lang('app.product.status.clientCkNo')</th>
            <th>@lang('app.product.status.balDue')</th>
            <th>@lang('app.product.status.merchandise')</th>
            <th>@lang('app.product.status.freight')</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<!--{!! Form::close() !!}-->