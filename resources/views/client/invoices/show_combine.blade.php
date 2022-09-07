@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang("app.menu.home")</a></li>
                <li><a href="{{ route('client.invoices.index') }}">@lang("app.menu.invoices")</a></li>
                <li class="active">@lang('app.invoice')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>


<style>
    .ribbon-wrapper {
        background: #ffffff !important;
    }
    .displayNone {
        display: none;
    }
    .takeLeft {
        float: left;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                   <i class="fa fa-check"></i> {!! $message !!}
                </div>
                <?php Session::forget('success');?>
            @endif

            @if ($message = Session::get('error'))
                <div class="custom-alerts alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php Session::forget('error');?>
            @endif
            <div id="error_ach" style="display: none;" class="payment-errors"></div>


            <div class="white-box printableArea ribbon-wrapper">
                <div class="ribbon-content " id="invoice_container">
                    @if($invoice->status == 'paid')
                        <div class="ribbon ribbon-bookmark ribbon-default">@lang('modules.invoices.paid')</div>
                    @elseif($invoice->status == 'partial')
                        <div class="ribbon ribbon-bookmark ribbon-default">@lang('modules.invoices.partial')</div>
                    @elseif($invoice->status == 'review')
                        <div class="ribbon ribbon-bookmark ribbon-default">@lang('modules.invoices.review')</div>
                    @else
                        <div class="ribbon ribbon-bookmark ribbon-default">@lang('modules.invoices.unpaid')</div>
                    @endif

                    <h3><span class="pull-right">{{ $invoice->invoice_number }}</span></h3>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="pull-left">
                                <address>
                                    <h3> &nbsp;<b class="text-danger">{{ ucwords($global->company_name) }}</b></h3>
                                    @if(!is_null($settings))
                                        <p class="text-muted m-l-5">{!! nl2br($global->address) !!}</p>
                                    @endif
                                    @if($invoiceSetting->show_gst == 'yes' && !is_null($invoiceSetting->gst_number))
                                        <p class="text-muted m-l-5"><b>@lang('app.gstIn')
                                                :</b>{{ $invoiceSetting->gst_number }}</p>
                                    @endif
                                </address>
                            </div>
                            <div class="pull-right text-right">
                                <address>
                                    @if(!is_null($invoice->project_id) && !is_null($invoice->project->client))
                                        <h3>@lang('app.to'),</h3>
                                        <h4 class="font-bold">{{ ucwords($invoice->project->client->name) }}</h4>
                                        <p class="m-l-30">
                                            <b>@lang('app.address') :</b>
                                            <span class="text-muted">
                                                {!! nl2br($invoice->project->client->address) !!}
                                            </span>
                                        </p>
                                        @if($invoice->show_shipping_address === 'yes')
                                            <p class="m-t-5">
                                                <b>@lang('app.shippingAddress') :</b>
                                                <span class="text-muted">
                                                    {!! nl2br($invoice->project->client->shipping_address) !!}
                                                </span>
                                            </p>
                                        @endif
                                        @if($invoiceSetting->show_gst == 'yes' && !is_null($invoice->project->client->gst_number))
                                            <p class="m-t-5"><b>@lang('app.gstIn')
                                                    :</b>  {{ $invoice->project->client->gst_number }}
                                            </p>
                                        @endif
                                    @elseif(!is_null($invoice->client_id))
                                        <h3>@lang('app.to'),</h3>
                                        <h4 class="font-bold">{{ ucwords($invoice->client->name) }}</h4>
                                        <p class="m-l-30">
                                            <b>@lang('app.address') :</b>
                                            <span class="text-muted">
                                                {!! nl2br($invoice->clientdetails->address) !!}
                                            </span>
                                        </p>
                                        @if($invoice->show_shipping_address === 'yes')
                                            <p class="m-t-5">
                                                <b>@lang('app.shippingAddress') :</b>
                                                <span class="text-muted">
                                                    {!! nl2br($invoice->clientdetails->shipping_address) !!}
                                                </span>
                                            </p>
                                        @endif
                                        @if($invoiceSetting->show_gst == 'yes' && !is_null($invoice->clientdetails->gst_number))
                                            <p class="m-t-5"><b>@lang('app.gstIn')
                                                    :</b>  {{ $invoice->clientdetails->gst_number }}
                                            </p>
                                        @endif
                                    @endif
                                    
                                    @if($invoiceSetting->show_vat == 'yes' && !is_null($invoiceSetting->vat_number))
                                            <p class="m-t-5"><b>VAT number #: </b>  {{ $invoiceSetting->vat_number }}</p>
                                    @endif

                                    <p class="m-t-30"><b>@lang('modules.invoices.invoiceDate') :</b> <i
                                                class="fa fa-calendar"></i> {{ $invoice->issue_date->format($global->date_format) }}
                                    </p>

                                    <p><b>@lang('modules.dashboard.dueDate') :</b> <i
                                                class="fa fa-calendar"></i> {{ $invoice->due_date->format($global->date_format) }}
                                    </p>
                                    @if($invoice->recurring == 'yes')
                                        <p><b class="text-danger">@lang('modules.invoices.billingFrequency') : </b> {{ $invoice->billing_interval . ' '. ucfirst($invoice->billing_frequency) }} ({{ ucfirst($invoice->billing_cycle) }} cycles)</p>
                                    @endif
                                </address>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive m-t-40" style="clear: both;">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>@lang('app.product.tabs.picture')</th>
                                        <th>@lang("modules.invoices.item")</th>
                                        <th class="text-right">@lang("modules.invoices.qty")</th>
                                         <th class="text-right">Sale Price</th>
                                        <th class="text-right">Shipping Price</th>
                                        <th class="text-right">@lang("modules.invoices.price")</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = 0; ?>
                                    @foreach($groupItems as $item)
                                       
                                            <tr>
                                                <td class="text-center">{{ ++$count }}</td>
                                                @if($item['product_url'] != '')
                                                     @if($item['product_url'] == 'https://app.indema.co/img/default-product.png')
                                                     <td class="text-center">&nbsp;</td>
                                                     @else
                                                     <td class="text-center"><img src="{{ $item['product_url'] }}" alt="product" width="75" height="75"></td>
                                                     @endif
                                                     
                                                    
                                                @else
                                                    <td class="text-center">&nbsp;</td>
                                                @endif
                                                
                                                @if($item['group_name'] != '')
                                                <td>{{ ucfirst($item['group_name']) }}</td>
                                                @else
                                                <td>{{ ucfirst($item['item_name']) }}
                                                    @if(!is_null($item['item_summary']))
                                                        <p class="font-12">{{ $item['item_summary'] }}</p>
                                                    @endif
                                                </td>
                                                @endif
                                                <td class="text-right">{{ $item['quantity'] }}</td>
                                                <td class="text-right"> {!! currency_position($item['sale_price'], $invoice->currencycurrency_symbol) !!} </td>
                                                <td class="text-right"> {!! currency_position($item['shipping_price'], $invoice->currencycurrency_symbol) !!} </td>
                                                <td class="text-right"> {!! currency_position($item['amount'], $invoice->currency->currency_symbol) !!} </td>
                                            </tr>
                                       
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-right m-t-30 text-right">
                                <p>@lang("modules.invoices.subTotal")
                                    : {!! currency_position($invoice->sub_total,htmlentities($invoice->currency->currency_symbol)) !!}</p>

                                <p>@lang("modules.invoices.discount")
                                    : {!! currency_position($discount, htmlentities($invoice->currency->currency_symbol)) !!} </p>
                                @foreach($taxes as $key=>$tax)
                                    <p>{{ strtoupper($key) }}
                                        : {!! currency_position($tax, htmlentities($invoice->currency->currency_symbol)) !!} </p>
                                @endforeach
                                @if($individual_tax > 0)
                                    <p>{{ $individual_tax_name }} : {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $individual_tax }}</p>
                                @endif
                                <hr>
                                <h3><b>@lang("modules.invoices.total")
                                        :</b> {!! currency_position($invoice->total, htmlentities($invoice->currency->currency_symbol)) !!}
                                </h3>
                                @if ($invoice->credit_notes()->count() > 0)
                                    <p>
                                        @lang('modules.invoices.appliedCredits'): {!! currency_position($invoice->appliedCredits(), htmlentities($invoice->currency->currency_symbol)) !!}
                                    </p>
                                @endif
                                <p>
                                    @lang('modules.invoices.amountPaid'): {{ currency_position($invoice->amountPaid(), $invoice->currency->currency_symbol) }}
                                </p>
                                <p class="@if ($invoice->amountDue() > 0) text-danger @endif">
                                    @lang('modules.invoices.amountDue'): {{ currency_position($invoice->amountDue(), $invoice->currency->currency_symbol) }}
                                </p>
                            </div>
                            @if(!is_null($invoice->note))
                                <div class="col-md-12 border-bottom">
                                    <p><strong>@lang('app.note')</strong>: {{ $invoice->note }}</p>
                                </div>
                            @endif
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12 border-bottom p-b-10">
                                    @if($invoice->status == 'unpaid' || $invoice->status == 'review' || $invoice->status == 'partial')

                                    <div class="form-group">
                                        <div class="radio-list border-bottom m-b-10">
                                            @if(($credentials->show_pay))
                                                <label class="radio-inline p-0">
                                                    <div class="radio radio-info-">
                                                        <input checked onchange="showButton('online')" type="radio" name="method" id="radio13" value="high">
                                                        <label for="radio13">@lang('modules.client.online')</label>
                                                    </div>
                                                </label>
                                            @endif
                                            @if($methods->count() > 0)
                                                <label class="radio-inline">
                                                    <div class="radio radio-info-">
                                                        <input type="radio" onchange="showButton('offline')"  name="method" id="radio15">
                                                        <label for="radio15">@lang('modules.client.offline')</label>
                                                    </div>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    {{--<div class="clearfix"></div>--}}
                                    <div class="col-md-12 p-l-0 text-left">
                                        @if(($credentials->show_pay))
                                            <div class="btn-group displayNone" id="onlineBox">
                                                <div class="dropup">
                                                <button type="button" class="btn btn-info- dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    @lang('modules.invoices.payNow') <span class="caret"></span>
                                                </button>
                                                <ul role="menu" class="dropdown-menu">
                                                    @if($credentials->paypal_status == 'active')
                                                        <li>
                                                            <a href="{{ route('client.paypal', [$invoice->id]) }}"><i
                                                                        class="fa fa-paypal"></i> @lang('modules.invoices.payPaypal') </a>
                                                        </li>
                                                    @endif
                                                    @if($credentials->stripe_status == 'active')
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#stripeModal"><i
                                                                class="fa fa-cc-stripe"></i> Credit Card </a>
                                                            <a style="display:none;" href="javascript:void(0);" id="stripePaymentButton"><i class="fa fa-cc-stripe"></i> Credit Card </a>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($credentials->plaid_status == 'active' && $credentials->stripe_status == 'active')
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="javascript:void(0);" id="link-button"><i class="fa fa-cc-stripe"></i> ACH </a>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($credentials->razorpay_status == 'active')
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="javascript:void(0);" id="razorpayPaymentButton"><i
                                                                        class="fa fa-credit-card"></i> @lang('modules.invoices.payRazorpay') </a>
                                                        </li>
                                                    @endif
                                                    @if($credentials->paystack_status == 'active')
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="{{ route('client.paystack-public', [$invoice->id]) }}">
                                                                <img height="15px" id="company-logo-img" src="https://s3-eu-west-1.amazonaws.com/pstk-integration-logos/paystack.jpg"> @lang('modules.invoices.payPaystack')</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                                </div>

                                            </div>
                                        @endif
                                        @if($methods->count() > 0)
                                            <div class="form-group displayNone m-0" id="offlineBox">
                                                <div class="radio-list border-bottom m-b-10">
                                                    @forelse($methods as $key => $method)
                                                        <label class="radio-inline @if($key == 0) p-0 @endif">
                                                            <div class="radio radio-info-" >
                                                                <input @if($key == 0) checked @endif onchange="showDetail('{{ $method->id }}')" type="radio" name="offlineMethod" id="offline{{$key}}"
                                                                    value="{{ $method->id }}">
                                                                <label for="offline{{$key}}" class="text-info-" >
                                                                    {{ ucfirst($method->name) }} </label>
                                                            </div>
                                                            <div class="displayNone" id="method-desc-{{ $method->id }}">
                                                                {!! $method->description !!}
                                                            </div>
                                                        </label>
                                                    @empty
                                                    @endforelse
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 displayNone" id="methodDetail">
                                                    </div>                                               
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-12 p-t-10 p-b-10 border-bottom">
									 @if(count($methods) > 0)
                                                        <button type="button" class="btn btn-info- save-offline" onclick="offlinePayment(); return false;">@lang('app.uploadReceipt')</button>
                                                    @endif
                                    <a class="btn btn-default btn-outline"
                                       href="{{ route('client.invoices.download', $invoice->id) }}"> <span><i
                                                    class="fa fa-file-pdf-o"></i> @lang('modules.invoices.downloadPdf')</span> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <h3 class="box-title m-0 p-t-10 p-b-10">@lang('modules.invoices.OfflinePaymentRequest')</h3>
                            <div class="table-responsive">
                                <table class="table color-table info-table-" id="users-table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('app.menu.offlinePaymentMethod')</th>
                                        <th>@lang('app.status')</th>
                                        <th>@lang('app.description')</th>
                                        <th>@lang('app.action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @php
                                        $status = ['pending' => 'warning', 'approve' => 'success', 'reject' => 'danger'];
                                        $statusString = ['pending' => 'Pending', 'approve' => 'approved', 'reject' => 'rejected'];
                                    @endphp

                                    @forelse($invoice->offline_invoice_payment as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ $request->payment_method->name }}</td>
                                            <td><label class="label label-{{$status[$request->status]}}">{{ ucwords($statusString[$request->status]) }}</label></td>
                                            <td>{{ $request->description }}</td>
                                            <td><a class="btn btn-primary btn-sm btn-circle" target="_blank" href="{{ $request->slip }}"><i class="fa fa-eye"></i></a></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="5">@lang('messages.noRecordFound')</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-md in" id="package-offline" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="stripeModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="stripeAddress">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('modules.stripeCustomerAddress.details')</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>@lang('modules.stripeCustomerAddress.name')</label>
                                    <input type="text" required name="clientName" id="clientName" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>@lang('modules.stripeCustomerAddress.line1')</label>
                                    <input type="text" required name="line1" id="line1" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>@lang('modules.stripeCustomerAddress.postalCode')</label>
                                    <input type="text" required name="postal_code" id="postal_code" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>@lang('modules.stripeCustomerAddress.city')</label>
                                    <input type="text" required name="city" id="city" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>@lang('modules.stripeCustomerAddress.state')</label>
                                    <input type="text" required name="state" id="state" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>@lang('modules.stripeCustomerAddress.country')</label>
                                    <input type="text" required name="country" maxlength="2" id="country" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <small>* Address country must be a valid <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank">2-alphabet ISO-3166 code</a></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary stripeAddressSubmit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
@if($credentials->plaid_status == 'active' && $credentials->stripe_status == 'active')

<script type="text/javascript">
(async function() {

  const configs = {
    // Pass the link_token generated in step 2.
    token: '{{ $credentials->plaid_link_token  }}',
    onLoad: function() {
      // The Link module finished loading.
    },
    onSuccess: function(public_token, metadata) {
     
        // The onSuccess function is called when the user has
        // successfully authenticated and selected an account to
        // use.
        //
        // When called, you will send the public_token
        // and the selected account ID, metadata.accounts,
        // to your backend app server.
        //
        // sendDataToBackendServer({
        //   public_token: public_token,
        //   account_id: metadata.accounts[0].id
        // });
      
      console.log('Public Token: ' + public_token);
      switch (metadata.accounts.length) {
        case 0:
          alert('Select Account is disabled: https://dashboard.plaid.com/link/account-select');
          break;
        case 1:
            console.log('Customer-selected account ID: ' + metadata.accounts[0].id);
            
            var post_data=[];
            post_data.push({ name: "action", value: 'get_client' });        
            post_data.push({ name: "token", value: public_token });        
            post_data.push({ name: "account_id", value: metadata.account_id });
            post_data.push({ name: "_token", value: "{{ csrf_token() }}" });
            
             $.easyAjax({
                url: '{{route('client.stripe-plaid', [$invoice->id])}}',
                type: "POST",
                redirect: true,
                data: post_data,
                success: function(result){}
            })
        //dataType: "json",
               
          
          break;
        default:
            alert('Multiple Accounts is enabled: https://dashboard.plaid.com/link/account-select');
      }
    },
    onExit: async function(err, metadata) {
      // The user exited the Link flow.
      if (err != null) {
           //alert('Something wrong, Please try later');
          // The user encountered a Plaid API error
          // prior to exiting.
      }
    },
  };
  var linkHandler = Plaid.create(configs);

  document.getElementById('link-button').onclick = function() {
    linkHandler.open();
  };
})();
</script>

@endif



<script>
    $(function () {
        @if(($credentials->show_pay))
            showButton('online');
        @else
                @if($methods->count() > 0)
        showButton('offline');
                @endif
        @endif
                if ($("#radio15").prop("checked")) {
                    showButton('offline');
                }

        var table = $('#invoices-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route('client.invoices.create') }}',
            deferRender: true,
            "order": [[0, "desc"]],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function (oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'project_name', name: 'projects.project_name'},
                {data: 'invoice_number', name: 'invoice_number'},
                {data: 'currency_symbol', name: 'currencies.currency_symbol'},
                {data: 'total', name: 'total'},
                {data: 'issue_date', name: 'issue_date'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

    });

    @if($credentials->stripe_status == 'active')
        var name = '';
        var line1 = '';
        var postal_code = '';
        var city = '';
        var state = '';
        var country = '';

        var handler = StripeCheckout.configure({
            key: '{{ $credentials->stripe_client_id }}',
            image: '{{ $global->logo_url }}',
            locale: 'auto',
            token: function(token) {
                // You can access the token ID with `token.id`.
                // Get the token ID to your server-side code for use.
                $.easyAjax({
                    url: '{{route('client.stripe', [$invoice->id])}}',
                    container: '#invoice_container',
                    type: "POST",
                    redirect: true,
                    data: {token: token, "_token" : "{{ csrf_token() }}", name: name, line1: line1, postal_code: postal_code, city: city, state: state, country: country }
                })
            }
        });

        $('#stripeAddress').submit(function(e){
            e.preventDefault();

            name = $('#clientName').val();
            line1 = $('#line1').val();
            postal_code = $('#postal_code').val();
            city = $('#city').val();
            state = $('#state').val();
            country = $('#country').val();

            $('#stripeModal').modal('toggle');
            handler.open({
                name: '{{ $companyName }}',
                amount: {{ $invoice->total*100 }},
                currency: '{{ $invoice->currency->currency_code }}',
                email: "",
            });
        })

        // Close Checkout on page navigation:
        window.addEventListener('popstate', function() {
            handler.close();
        });



    @endif

    // Show offline method detail
    function showDetail(id){
        var detail = $('#method-desc-'+id).html();
        $('#methodDetail').html(detail);
        $('#methodDetail').show();
    }

    // Payment mode
    function showButton(type){

        if(type == 'online'){
            $('#methodDetail').hide();
            $('#offlineBox').hide();
            $('#onlineBox').show();
        }else{
            $('#offline0').change();
            $('#offlineBox').show();
            $('#onlineBox').hide();
        }
    }

     function offlinePayment() {

        let offlineId = $("input[name=offlineMethod]").val();

        $.ajaxModal('#package-offline', '{{ route('client.invoices.offline-payment')}}?offlineId='+offlineId+'&invoiceId='+'{{$invoice->id}}');

        {{--$.easyAjax({--}}
        {{--    url: '{{ route('client.invoices.store') }}',--}}
        {{--    type: "POST",--}}
        {{--    redirect: true,--}}
        {{--    data: {invoiceId: "{{ $invoice->id }}", "_token" : "{{ csrf_token() }}", "offlineId": offlineId}--}}
        {{--})--}}

    }

    @if($credentials->razorpay_status == 'active')
        $('#razorpayPaymentButton').click(function() {
            console.log('{{ $invoice->currency->currency_code }}');
                var amount = {{ $invoice->total*100 }};
                var invoiceId = {{ $invoice->id }};
                var clientEmail = "{{ $user->email }}";

                var options = {
                    "key": "{{ $credentials->razorpay_key }}",
                    "amount": amount,
                    "currency": 'INR',
                    "name": "{{ $companyName }}",
                    "description": "Invoice Payment",
                    "image": "{{ $global->logo_url }}",
                    "handler": function (response) {
                        confirmRazorpayPayment(response.razorpay_payment_id,invoiceId,response);
                    },
                    "modal": {
                        "ondismiss": function () {
                            // On dismiss event
                        }
                    },
                    "prefill": {
                        "email": clientEmail
                    },
                    "notes": {
                        "purchase_id": invoiceId //invoice ID
                    }
                };
                var rzp1 = new Razorpay(options);

                rzp1.open();

            })

            //Confirmation after transaction
            function confirmRazorpayPayment(id,invoiceId,rData) {
                $.easyAjax({
                    type:'POST',
                    url:'{{route('client.pay-with-razorpay')}}',
                    data: {paymentId: id,invoiceId: invoiceId,rData: rData,_token:'{{csrf_token()}}'}
                })
            }

    @endif
</script>
@endpush
