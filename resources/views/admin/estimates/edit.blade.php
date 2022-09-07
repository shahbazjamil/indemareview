@extends('layouts.app')

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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.estimates.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.update')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
<style>
    .dropdown-content {
        width: 250px;
        max-height: 250px;
        overflow-y: scroll;
        overflow-x: hidden;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading p-b-10"> @lang('modules.estimates.updateEstimate')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body p-0 p-t-20">
                        {!! Form::open(['id'=>'updatePayments','class'=>'ajax-form','method'=>'PUT', 'enctype' => 'multipart/form-data']) !!}
                        <div class="form-body">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.estimates.estimatesNumber')</label>
                                        <div class="input-icon">
                                            <input type="text" readonly class="form-control" name="estimate_number" id="estimate_number" value="{{ $estimate->estimate_number }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.project')</label>
                                        <select name="project_id" id="project_id" class="select2 form-control" data-placeholder="Project Project">
<!--                                            <option value="">Choose Project</option>-->
                                            <?php $default_project_id = $estimate->project_id ? $estimate->project_id : 0;?>
                                            @foreach ($projects as $project)
                                            
                                            <option client_id_attr="{{ $project->client_id }}" value="{{$project->id}}" {{ $project->id == $default_project_id ? 'selected' : '' }}>{{ucfirst($project->project_name)}}</option>
                                           
                                            
                                            @endforeach
                                          </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.client')</label>
                                        <select class="select2 form-control" data-placeholder="Choose Client" name="client_id" id="client_company_id">
                                            @foreach($clients as $client)
                                                <option data-shippingadd="{!! $client->shipping_address !!}"
                                                        @if($estimate->client_id == $client->user_id) selected
                                                        @endif
                                                        value="{{ $client->user_id }}">{{ ucwords(!is_null($client->company_name) ? $client->name . ' (' . $client->company_name .')' : $client->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.invoices.currency')</label>
                                        <select class="form-control" name="currency_id" id="currency_id">
                                            @foreach($currencies as $currency)
                                                <option
                                                        @if($estimate->currency_id == $currency->id) selected
                                                        @endif
                                                        value="{{ $currency->id }}">{{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.estimates.validTill')</label>
                                        <div class="input-icon">
                                            <input type="text" class="form-control" name="valid_till" id="valid_till"
                                                   value="{{ $estimate->valid_till->format($global->date_format) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.status')</label>
                                        <select class="form-control" name="status" id="status">
                                            <option
                                                    @if($estimate->status == 'accepted') selected @endif
                                            value="accepted">@lang('modules.estimates.accepted')
                                            </option>
                                            <option
                                                    @if($estimate->status == 'waiting') selected @endif
                                            value="waiting">@lang('modules.estimates.waiting')
                                            </option>
                                            <option
                                                    @if($estimate->status == 'declined') selected @endif
                                            value="declined">@lang('modules.estimates.declined')
                                            </option>
                                            @if($estimate->status == 'draft')
                                            <option
                                                    @if($estimate->status == 'draft') selected @endif
                                            value="draft">@lang('modules.invoices.draft')
                                            </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Tags</label>
                                <select multiple data-role="tagsinput" name="tags[]" id="tags">
                                    @if(!empty($estimate->tags))
                                        @foreach($estimate->tags as $tag)
                                            <option value="{{ $tag }}">{{ $tag }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div> 
                            
                        <div class="row">
                            <div class="col-md-2">

                                    <div class="form-group" >
                                        <label class="control-label">Show combined version to client</label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="combine_line_items" name="combine_line_items"
                                                        class="js-switch " data-color="#00c292" data-secondary-color="#f96262" @if($estimate->combine_line_items == 1) checked
                                                   @endif />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.invoices.showShippingAddress')
                                        <a class="mytooltip" href="javascript:void(0)">
                                            <i class="fa fa-info-circle"></i>
                                            <span class="tooltip-content5">
                                                <span class="tooltip-text3">
                                                    <span class="tooltip-inner2">
                                                        @lang('modules.invoices.showShippingAddressInfo')
                                                    </span>
                                                </span>
                                            </span>
                                        </a>
                                    </label>
                                    <div class="switchery-demo">
                                            <input type="checkbox" id="show_shipping_address" name="show_shipping_address"
                                                   @if($estimate->show_shipping_address == 'yes') checked
                                                   @endif class="js-switch " data-color="#00c292"
                                                   data-secondary-color="#f96262"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="shippingAddress" @if($estimate->show_shipping_address == 'no') style="display: none;" @endif>
                                    
                                    <div class="form-group">
                                            <label class="control-label">Shipping Address</label>
                                            <div class="input-icon">
                                                    <textarea name="shipping_address" id="shipping_address" class="form-control">{{ $estimate->shipping_address }}</textarea>

                                            </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-12 border-bottom">
                                    <div class="btn-group m-b-10">
                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button" onclick="$('#produbctsModal').show();">@lang('app.menu.products') <span class=""> + </span></button>
                                        <ul style="display:none;" role="menu" class="dropdown-menu dropdown-content">
                                            @foreach($products as $product)
                                                <li class="m-b-10">
                                                    <div class="row m-t-10">
                                                        <div class="col-md-6" style="padding-left: 30px">
                                                            {{ $product->name }}
                                                        </div>
                                                        <div class="col-md-6" style="text-align: right;padding-right: 30px;">
                                                            <a href="javascript:;" data-pk="{{ $product->id }}" class="btn btn-success btn btn-outline btn-xs waves-effect add-product">@lang('app.add') <i class="fa fa-plus" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    
                                   
                                    <div class="btn-group m-b-10">
                                        <button aria-expanded="false" data-toggle="dropdown"
                                            class="btn btn-info dropdown-toggle waves-effect waves-light"
                                            type="button">@lang('modules.invoices.addItem') <span class="caret"></span>
                                        </button>
                                        <ul role="menu" class="dropdown-menu dropdown-content">
                                            <li class="m-b-10">
                                                <div class="row m-t-10">
                                                    <div class="col-md-6" style="padding-left: 30px">
                                                        Product
                                                    </div>
                                                    <div class="col-md-6" style="text-align: right;padding-right: 30px;">
                                                        <a href="javascript:;" id="add-item" class="btn btn-success btn btn-outline btn-xs waves-effect">@lang('app.add')
                                                            <i class="fa fa-plus" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="m-b-10">
                                                <div class="row m-t-10">
                                                    <div class="col-md-6" style="padding-left: 30px">
                                                        Services
                                                    </div>
                                                    <div class="col-md-6" style="text-align: right;padding-right: 30px;">
                                                        <a href="javascript:;" id="add-services" class="btn btn-success btn btn-outline btn-xs waves-effect">@lang('app.add')
                                                            <i class="fa fa-plus" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="m-b-10">
                                                <div class="row m-t-10">
                                                    <div class="col-md-6" style="padding-left: 30px">
                                                        Proposed
                                                    </div>
                                                    <div class="col-md-6" style="text-align: right;padding-right: 30px;">
                                                        <a href="javascript:;" id="add-proposed" class="btn btn-success btn btn-outline btn-xs waves-effect">@lang('app.add')
                                                            <i class="fa fa-plus" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                            
                                    
                                    
                                </div>
                            </div>
                            
                            <div class="row">

                                <div class="col-xs-12  visible-md visible-lg d-flex-border d-none">
                                    
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                           @lang('app.product.tabs.picture')
                                       </div>

                                        <div class="col-md-2 font-bold" style="padding: 8px 15px">
                                            @lang('modules.invoices.item')
                                        </div>
    
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                            @lang('modules.invoices.qty')
                                        </div>
    
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                            @lang('modules.invoices.unitPrice')
                                        </div>
                                    
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                           Markup %
                                        </div>
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                           Markup {{ $global->currency->currency_symbol }}
                                        </div>
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                            Sale Price
                                        </div>
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                            Shipping
                                        </div>
    
                                        <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                            @lang('modules.invoices.tax') <a href="javascript:;" class="tax-settings" ><i class="ti-settings text-info"></i></a>
                                        </div>
    
                                        <div class="col-md-1 text-center font-bold" style="padding: 8px 15px">
                                            @lang('modules.invoices.amount')
                                        </div>
    
                                        <div class="col-md-1" style="padding: 8px 15px">
                                            &nbsp;
                                        </div>

                                </div>

                                <div id="sortable" class="col-md-12 p-0">
                                @foreach($estimate->items as $key => $item)
                                    <div class="col-xs-12 item-row margin-top-5 d-flex-border">
                                        
                                        <div class="col-md-1">
											<div class="hd">@lang('app.product.tabs.picture')</div>
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>
                                                    @if($item->picture != '')
                                                        <p class="form-control-static">
                                                            <label>
                                                                <input type="file" class="product_img" style="display: none" name="product_img[]">
                                                                <img src="{{ asset('user-uploads/products/'.$item->product_id.'/'.$item->picture.'') }}" alt="product">
                                                            </label>
                                                        </p>
                                                        <input type="hidden" class="form-control" value="{{ $item->picture }}" name="picture[]">
                                                        <input type="hidden" class="form-control" value="{{ $item->product_id }}" name="product_id[]" >
                                                     @else
                                                        <p class="form-control-static">
                                                            <label>
                                                                <input type="file" class="product_img" style="display: none" name="product_img[]">
                                                                <img src="{{ $item->product_url }}" alt="product">
                                                            </label>
                                                        </p>
                                                        <input type="hidden" class="form-control" value="" name="picture[]">
                                                        <input type="hidden" class="form-control" value="" name="product_id[]" >
                                                    @endif
                                                    <input name="old_items[]" type="hidden" value="{{ $item->id }}">
                                                </div>

                                            </div>
                                        
                                        
                                        <div class="col-md-1">
										<div class="hd">@lang('modules.invoices.item')</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>
                                                        <input type="text" class="form-control item_name" name="item_name[]"
                                                            value="{{ $item->item_name }}" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                <textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2">{{ $item->item_summary }}</textarea>

                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-1">
											<div class="hd">Type</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Type</label>
                                                            <input type="text" readonly="" class="form-control" name="invoice_item_type[]" value="{{ $item->invoice_item_type }}" placeholder="{{ $item->invoice_item_type }}">
                                                        </div>
                                                    </div>
                                            </div>
                                        <div class="col-md-1 text-right visible-md visible-lg">
											<div class="hd">Action</div>
                                            <button type="button" class="btn remove-item btn-circle btn-danger"><i
                                                        class="fa fa-remove"></i></button>
                                        </div>
											<div class="w-100"></div>
                                        <div class="col-md-1">
											<div class="hd">@lang('modules.invoices.qty')</div>
                                            <div class="form-group">
                                                <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>
                                                <input type="number" min="1" class="form-control quantity"
                                                    value="{{ $item->quantity }}" name="quantity[]"
                                                    >
                                            </div>


                                        </div>

                                        <div class="col-md-1">
											<div class="hd">@lang('modules.invoices.unitPrice')</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>
                                                    <input type="text" min="" class="form-control cost_per_item"
                                                        name="cost_per_item[]" value="{{ $item->unit_price }}"
                                                        >
                                                </div>
                                            </div>

                                        </div>
                                        
                                        <div class="col-md-1">
											<div class="hd">Markup %</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Markup %</label>
                                                            <input type="number" step=any class="form-control markup" name="markup[]" value="{{ $item->markup }}" placeholder="0.00">
                                                            <span class="help-block markup_txt"></span>
                                                        </div>
                                                    </div>
                                            </div>
                                        <div class="col-md-1">
											<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Markup fix</label>
                                                            <input type="number" step=any class="form-control markup_fix" name="markup_fix[]" value="{{ $item->markup_fix }}" placeholder="0.00">
                                                            <span class="help-block markup_fix_txt"></span>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="col-md-1">
											<div class="hd">Sale Price</div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="control-label hidden-md hidden-lg">Sale Price</label>
                                                        <input type="number" step=any class="form-control sale_price" name="sale_price[]" value="{{ $item->sale_price }}" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
											<div class="hd">Shipping</div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="control-label hidden-md hidden-lg">Shipping Price</label>
                                                        <input type="number" step=any class="form-control shipping_price" name="shipping_price[]" value="{{ $item->shipping_price }}" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>

                                        <div class="col-md-1">
											<div class="hd">@lang('modules.invoices.tax') <a href="javascript:;" class="tax-settings" ><i class="ti-settings text-info"></i></a></div>
                                            <div class="form-group">
                                                <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.type')</label>
                                                <select id="multiselect" name="taxes[{{ $key }}][]"  multiple="multiple" class="selectpicker form-control type">
                                                    @foreach($taxes as $tax)
                                                        <option data-rate="{{ $tax->rate_percent }}"
                                                                @if (isset($item->taxes) && array_search($tax->id, json_decode($item->taxes)) !== false)
                                                                selected
                                                                @endif
                                                                value="{{ $tax->id }}">{{ $tax->tax_name }}: {{ $tax->rate_percent }}%</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                        </div>
                                                                                        
                                                                                        <div class="col-md-1">
                                                <div class="hd">Group <a href="javascript:;" class="group-settings" ><i class="ti-settings text-info"></i></a></div>

                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">Group</label>
                                                    <select id="groupselect" name="groups[{{ $key }}]" class="selectpicker form-control type">
                                                        <option value="">Nothing selected</option>
                                                        @foreach($groups as $group)
                                                            <option 
                                                                    @if (isset($item->group_id) && $group->id == $item->group_id)
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
                                            <p class="form-control-static"><span
                                                        class="amount-html">{{ number_format((float)$item->amount, 2, '.', '') }}</span></p>
                                            <input type="hidden" value="{{ $item->amount }}" class="amount"
                                                name="amount[]">
                                        </div>

                                        <div class="col-md-1 hidden-md hidden-lg">
                                            <div class="row">
                                                <button type="button" class="btn btn-circle remove-item btn-danger"><i
                                                            class="fa fa-remove"></i> @lang('app.remove')
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                                </div>

                               

                                

                                <div class="col-xs-12 ">
                                    
                                        <div class="row">
                                            <div class="col-md-offset-9 col-xs-6 col-md-1 text-right p-t-10">
                                                Shipping Total</div>

                                            <p class="form-control-static col-xs-6 col-md-2">
                                                <span class="shipping-total">{{ number_format((float)$estimate->shipping_total, 2, '.', '') }}</span>
                                            </p>

                                            <input type="hidden" class="shipping-total-field" name="shipping_total" value="{{ $estimate->shipping_total }}">
                                        </div>
                                    
                                        <div class="row">
                                            <div class="col-md-offset-9 col-xs-6 col-md-1 text-right p-t-10">@lang('modules.invoices.subTotal')</div>
    
                                            <p class="form-control-static col-xs-6 col-md-2">
                                                <span class="sub-total">{{ number_format((float)$estimate->sub_total, 2, '.', '') }}</span>
                                            </p>
    
    
                                            <input type="hidden" class="sub-total-field" name="sub_total" value="{{ $estimate->sub_total }}">
                                        </div>
    
                                        <div class="row">
                                            <div class="col-md-offset-9 col-md-1 text-right p-t-10">
                                                @lang('modules.invoices.discount')
                                            </div>
                                            <div class="form-group col-xs-6 col-md-1" >
                                                <input type="number" min="0" value="{{ $estimate->discount }}" name="discount_value" class="form-control discount_value" >
                                            </div>
                                            <div class="form-group col-xs-6 col-md-1" >
                                                <select class="form-control" name="discount_type" id="discount_type">
                                                    <option
                                                            @if($estimate->discount_type == 'percent') selected @endif
                                                            value="percent">%</option>
                                                    <option
                                                            @if($estimate->discount_type == 'fixed') selected @endif
                                                    value="fixed">@lang('modules.invoices.amount')</option>
                                                </select>
                                            </div>
                                        </div>
                                    
                                    <div class="row">
                                        <div class="col-md-offset-9 col-md-1 text-right p-t-10">
                                            Credit Card Processing
                                        </div>
                                        <div class="form-group col-xs-6 col-md-1" >
                                            <input type="number" min="0" value="{{ $estimate->card_processing_value }}" name="card_processing_value" class="form-control card_processing_value" >
                                        </div>
                                        <div class="form-group col-xs-6 col-md-1" >
                                            <select class="form-control" name="card_processing_type" id="card_processing_type">
                                                <option
                                                        @if($estimate->card_processing_type == 'percent') selected @endif
                                                        value="percent">%</option>
                                                <option
                                                        @if($estimate->card_processing_type == 'fixed') selected @endif
                                                value="fixed">@lang('modules.invoices.amount')</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-offset-9 col-md-1 text-right p-t-10">
                                            Deposit Request
                                        </div>
                                        <div class="form-group col-xs-6 col-md-1" >
                                            <input type="number" min="0" value="{{ $estimate->deposit_request }}" name="deposit_request" class="form-control deposit_request" >
                                        </div>
                                        <div class="form-group col-xs-6 col-md-1" >
                                            <select class="form-control" name="deposit_request_type" id="deposit_request_type">
                                                <option
                                                        @if($estimate->deposit_request_type == 'percent') selected @endif
                                                        value="percent">%</option>
                                                <option
                                                        @if($estimate->deposit_request_type == 'fixed') selected @endif
                                                value="fixed">@lang('modules.invoices.amount')</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                    <div class="col-md-offset-9 col-md-1 text-right p-t-5">
                                        Tax <a href="javascript:;" class="tax-settings"><i
                                            class="ti-settings text-info"></i></a>
                                    </div>
                                    <div class="form-group col-xs-6 col-md-2">
                                        <select id="multiselect" name="tax_on_total[]" multiple="multiple"
                                                    class="selectpicker form-control type tax_on_total">
                                                    @foreach($taxes as $tax)
                                                    <option data-rate="{{ $tax->rate_percent }}"
                                                                    @if (isset($estimate->tax_on_total) && array_search($tax->id, json_decode($estimate->tax_on_total)) !== false)
                                                                    selected
                                                                    @endif
                                                                    value="{{ $tax->id }}">{{ $tax->tax_name }}: {{ $tax->rate_percent }}%</option>
                                                    @endforeach
                                        </select>
                                    </div>
                                </div>
    
                                        <div class="row m-t-5" id="invoice-taxes">
                                            <div class="col-md-offset-9 col-md-1 text-right p-t-10">
                                                @lang('modules.invoices.tax')
                                            </div>
    
                                            <p class="form-control-static col-xs-6 col-md-2" >
                                                <span class="tax-percent">0</span>
                                            </p>
                                        </div>
    
                                        <div class="row m-t-5 font-bold">
                                            <div class="col-md-offset-9 col-md-1 col-xs-6 text-right p-t-10">@lang('modules.invoices.total')</div>
    
                                            <p class="form-control-static col-xs-6 col-md-2">
                                                <span class="total">{{ number_format((float)$estimate->total, 2, '.', '') }}</span>
                                            </p>
                                            
                                             <div class="col-md-offset-9 col-md-1 col-xs-6 text-right p-t-10">Deposit Request Total</div>

                                            <p class="form-control-static col-xs-6 col-md-2">
                                                <span class="deposit-text">{{ number_format((float)$estimate->deposit_req, 2, '.', '') }}</span>
                                            </p>
    
    
                                            <input type="hidden" class="total-field" name="total"
                                                    value="{{ round($estimate->total, 2) }}">
                                             <input type="hidden" class="total-tax-field" name="total_tax"
                                               value="{{ round($estimate->total_tax, 2) }}">
                                        
                                        <input type="hidden" class="deposit-field" name="deposit_req"
                                               value="{{ round($estimate->deposit_req, 2) }}">
                                        </div>
    
                                    </div>

                            </div>
                            <div class="row">

                                <div class="col-sm-12">

                                    <div class="form-group">
                                        <label class="control-label">@lang('app.note')</label>

                                        <textarea name="note" class="form-control" rows="5">{{ $estimate->note }}</textarea>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="form-actions" style="margin-top: 70px">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="save-form" class="btn btn-success"><i
                                                class="fa fa-check"></i> @lang('app.save')
                                    </button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->
    
    
     {{--Ajax Modal--}}
<div class="modal fade bs-modal-lg in" id="produbctsModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingP">Products</span>
            </div>
            <div class="modal-body" style="overflow-x: auto;height: 500px;">
                
                <div class="row m-b-30">
                    <div class="col-md-12">
                        <h5 class="pull-left">FILTER RESULTS</h5>
                    </div>
                    <div class="col-md-3">
                         <select name="flt_project_id" id="flt_project_id" class="select2 form-control filter-products" data-placeholder="Choose Project">
                            <option value="">Select Project</option>
                            @foreach ($projects as $project)
                                <option value="{{$project->id}}"  >{{ucfirst($project->project_name)}}</option>
                            @endforeach
                        </select>
                        
                    </div>
                    <div class="col-md-3">
                        <select  name="flt_salesCategory" id="flt_salesCategory" class="select2 form-control filter-products">
                            <option value="">Select Category</option>
                            @forelse($salescategories as $salescategory)
                                <option value="{{ $salescategory->salescategory_code }}" >{{ucfirst($salescategory->salescategory_name)}}</option>
                            @empty
                                <option value="">No Category Added</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="flt_locationCode" id="flt_locationCode" class="select2 form-control filter-products">
                            <option value="">Select location CODE</option>
                            @forelse($codetypes as $codetype)
                            <option value="{{ $codetype->location_code }}" >{{ucfirst($codetype->location_name)}}</option>
                            @empty
                            <option value="">No Location Added</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="flt_vendor_id" id="flt_vendor_id" class="select2 form-control filter-products" >
                            <option value="">Select Vendor</option>
                          @foreach ($clientVendors as $clientVendor)
                          <option value="{{$clientVendor->id}}" >{{ucfirst($clientVendor->company_name)}}</option>
                          @endforeach
                        </select>
                    </div>
                </div>
                
             
                <div class="table-responsive" id="produbcts_table_data">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Select</th>
                            <th>@lang('app.product.tabs.picture')</th>
                            <th>@lang('modules.invoices.item')</th>
                            <th>QTY</th>
                            <th>Project Name</th>
                            <th>Vendor</th>
                        </tr>
                        </thead>
                        <tbody>
                         @forelse($products as $item)
                         <?php 
                         
                         $project_name = '';
                         foreach ($item->projects as $project) {
                                if($project_name == ''){
                                    if(isset($project->project)) {
                                        $project_name .= ucfirst($project->project->project_name);
                                    }
                                }else {
                                    if(isset($project->project)) {
                                        $project_name .=', '.ucfirst($project->project->project_name);
                                    }
                                }
                         }
                         $vendor_name = '';
                         if(!is_null($item->vendor_id)) {
                             if($item->vendor) {
                                $vendor_name =  $item->vendor->company_name;
                             }
                         }
                         ?>
                            <tr>
                                <td width="5%" class="al-center bt-border">
                                    <input type="checkbox" value="{{$item->id}}" name="select_product" id="select_product" class="form-control">
                                </td>

                                 <td width="10%" class="al-center bt-border">
                                    
                                    <?php if(!empty($item->picture)) { 
                                         $pictures = json_decode($item->picture);
                                        ?>
                                     <?php if(isset($pictures[0])) {?>
                                        <p class="form-control-static"><img src="{{ asset('user-uploads/products/'.$item->id.'/'.$pictures[0].'') }}" alt="product" width="100" height="100"></p>
                                     <?php } else { ?>
                                        <p class="form-control-static"><img src="{{ asset('img/img-dummy.jpg') }}" alt="product" width="100" height="100"></p>
                                     <?php } ?>
                                        
                                         
                                     <?php } else { ?>
                                         <p class="form-control-static"><img src="{{ asset('img/img-dummy.jpg') }}" alt="product" width="100" height="100"></p>
                                     <?php }
                                     ?>
                                
                                </td>

                                <td width="25%" class="al-center bt-border">
                                    {{ $item->name }}
                                </td>
                                <td width="10%" class="al-center bt-border">
                                    {{ $item->quantity }}
                                </td>
                                 <td width="25%" class="al-center bt-border">
                                    {{ $project_name }}
                                </td>
                                <td width="25%" class="al-center bt-border">
                                    {{ $vendor_name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No Products</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default close-md" data-dismiss="modal">@lang('app.close')</button>
                <button type="button" class="btn blue" id="sel_product">@lang('app.add')</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Ajax Modal Ends--}}

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taxModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    @lang('app.loading')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn blue">@lang('app.save') @lang('app.changes')</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

<script>
    
    var shipping_taxed = '<?php echo $invoiceSetting->shipping_taxed; ?>';
   
       // Switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function () {
        new Switchery($(this)[0], $(this).data());
    });
        
   
        
    var showShippingSwitch = document.getElementById('show_shipping_address');
    showShippingSwitch.onchange = function () {
        if (showShippingSwitch.checked) {
             $('#shippingAddress').show();
        } else {
            $('#shippingAddress').hide();
        }
    }
        
    $(function() {
        $("#client_company_id").change(function(){ 
            var element = $(this).find('option:selected'); 
            $('#shipping_address').val(element.data("shippingadd"));
        }); 
    });
    
    
    
    var cur_symbol = '<?php echo $global->currency->currency_symbol ?>';
    var selectProject = document.getElementById('project_id');
        selectProject.onchange = function () {
            var option = $('option:selected', this).attr('client_id_attr');
            console.log(option);
            
            $("#client_company_id").val(option).change();
        };
    $('.tax-settings').click(function () {
        var url = '{{ route('admin.taxes.create')}}';
        $('#modelHeading').html('Manage Project Category');
        $.ajaxModal('#taxModal', url);
    })
    
    $(document).on('click', 'a.group-settings', function(event) {
            var url = '{{ route('admin.line-tem-groups.create')}}';
            $('#modelHeading').html('Manage Groups');
            $.ajaxModal('#taxModal', url);
    });
    
    $(function () {
        $( "#sortable" ).sortable();
    });
    
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    jQuery('#valid_till').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $('#save-form').click(function () {
        let form = $('#updatePayments');
            $.ajax({
                url: '{{route('admin.estimates.update', $estimate->id)}}',
                container:'#updatePayments',
                type: "POST",
                redirect: true,
                data : new FormData(form[0]),
                processData: false,
                contentType: false,
                success: function (data){
                    if(data.status == 'success'){
                        $.showToastr(data.message, 'success');
                        setTimeout( function (){
                            window.location.href = data.url;
                        }, 2000);
                    }
                    if(data.status == 'fail'){
                        $.showToastr(data.message, 'error');
                    }
                },
                error:function (data){
                    $.showToastr(data.responseJSON.message, 'error');
                },
            // data: $('#updatePayments').serialize()
        });
    });

    $('#add-item').click(function () {
        var i = $(document).find('.item_name').length;
        var item = '<div class="col-xs-12 item-row margin-top-5 d-flex-border">'
        
            + '<div class="col-md-1">'
			+ '<div class="hd">@lang('app.product.tabs.picture')</div>'
           + '<div class="form-group">'
           + '<label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>'
           + '<p class="form-control-static"><label><input type="file" class="product_img" style="display: none" name="product_img[]">'
            +'<img src="{{ asset('img/img-dummy.jpg') }}" alt="product"> </label></p>'
           + '<input type="hidden" class="form-control" value="" name="picture[]" >'
           + '<input type="hidden" class="form-control" value="" name="product_id[]" >'
           + '</div>'
           + '</div>'

            +'<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.item')</div>'
            +'<div class="row">'
            +'<div class="form-group">'
            +'<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>'
            +'<div class="input-group">'
            +'<div class="input-group-addon"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>'
            +'<input type="text" class="form-control item_name" name="item_name[]" >'
            +'</div>'

            +'</div>'
            +'<div class="form-group">'
            +'<textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2"></textarea>'
            +'</div>'
            +'</div>'
            +'</div>'
    
            + '<div class="col-md-1">'
			+'<div class="hd">Type</div>'
            + '<div class="row">'
            + '<div class="form-group">'
            + '<label class="control-label hidden-md hidden-lg">Type</label>'
            + '<input type="text" readonly="" class="form-control" value="product" placeholder="product" name="invoice_item_type[]">'
            + '</div>'
            + '</div>'
            + '</div>'

            +'<div class="col-md-1 text-right visible-md visible-lg">'
			+'<div class="hd">Action</div>'
            +'<button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>'
            +'</div>'
			+'<div class="w-100"></div>'

            +'<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.qty')</div>'
            +'<div class="form-group">'
            +'<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>'
            +'<input type="number" min="1" class="form-control quantity" value="1" name="quantity[]" >'
            +'</div>'


            +'</div>'
            +'<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.unitPrice')</div>'
            +'<div class="row">'
            +'<div class="form-group">'
            +'<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>'
            +'<input type="text" min="0" class="form-control cost_per_item" value="0" name="cost_per_item[]">'
            +'</div>'
            +'</div>'
            +'</div>'
    
            + '<div class="col-md-1">'
			+'<div class="hd">Markup %</div>'
            + '<div class="row">'
            + '<div class="form-group">'
            + '<label class="control-label hidden-md hidden-lg">Markup %</label>'
            + '<input type="number" step=any class="form-control markup" value="" placeholder="0.00" name="markup[]">'
            + '<span class="help-block markup_txt"></span>'
            + '</div>'
            + '</div>'
            + '</div>'
    
            + '<div class="col-md-1">'
			+'<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>'
            + '<div class="row">'
            + '<div class="form-group">'
            + '<label class="control-label hidden-md hidden-lg">Markup fix</label>'
            + '<input type="number" step=any class="form-control markup_fix" value="" placeholder="0.00" name="markup_fix[]">'
            + '<span class="help-block markup_fix_txt"></span>'
            + '</div>'
            + '</div>'
            + '</div>'

            + '<div class="col-md-1">'
			+'<div class="hd">Sale Price</div>'
            + '<div class="row">'
            + '<div class="form-group">'
            + '<label class="control-label hidden-md hidden-lg">Sale Price</label>'
            + '<input type="number" step=any class="form-control sale_price" value="" placeholder="0.00" name="sale_price[]">'
            + '</div>'
            + '</div>'
            + '</div>'

            + '<div class="col-md-1">'
			+'<div class="hd">Shipping</div>'
            + '<div class="row">'
            + '<div class="form-group">'
            + '<label class="control-label hidden-md hidden-lg">Shipping Price</label>'
            + '<input type="number" step=any class="form-control shipping_price" value="" placeholder="0.00" name="shipping_price[]">'
            + '</div>'
            + '</div>'
            + '</div>'


            +'<div class="col-md-1">'
            +'<div class="hd">@lang('modules.invoices.tax')</div>'
            +'<div class="form-group">'
            +'<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.tax')</label>'
            +'<select id="multiselect'+i+'" name="taxes['+i+'][]" value="null"  multiple="multiple" class="selectpicker form-control type">'
                @foreach($taxes as $tax)
            +'<option data-rate="{{ $tax->rate_percent }}" value="{{ $tax->id }}">{{ $tax->tax_name.': '.$tax->rate_percent }}%</option>'
                @endforeach
            +'</select>'
            +'</div>'
            +'</div>'
    
                + '<div class="col-md-1">'
                + '<div class="hd">Group <a href="javascript:;" class="group-settings" ><i class="ti-settings text-info"></i></a></div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Group</label>'
                + '<select id="groupselect' + i + '"  name="groups[' + i + ']" class="selectpicker form-control type">'
                + '<option value="">Nothing selected</option>'
                    @foreach($groups as $group)
                + '<option value="{{ $group->id }}">{{ $group->group_name}}</option>'
                    @endforeach
                + '</select>'
                + '</div>'
                + '</div>'

            +'<div class="col-md-1 text-center">'
			+'<div class="hd">@lang('modules.invoices.amount')</div>'
            +'<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>'
            +'<p class="form-control-static"><span class="amount-html">0.00</span></p>'
            +'<input type="hidden" class="amount" name="amount[]">'
            +'</div>'

            +'<div class="col-md-1 hidden-md hidden-lg">'
            +'<div class="row">'
            +'<button type="button" class="btn remove-item btn-danger"><i class="fa fa-remove"></i> @lang('app.remove')</button>'
            +'</div>'
            +'</div>'

            +'</div>';

        $(item).hide().appendTo("#sortable").fadeIn(500);
        $('#multiselect'+i).selectpicker();
        $('#groupselect' + i).selectpicker()
    });
    
    $('#add-services').click(function () {
            var i = $(document).find('.item_name').length;
            var item = '<div class="col-xs-12 item-row margin-top-5 d-flex-border">'
            
                + '<div class="col-md-1 invisible">'
			+ '<div class="hd">@lang('app.product.tabs.picture')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>'
                + '<p class="form-control-static"><img src="{{ asset('img/img-dummy.jpg') }}" alt="product" width="100" height="100"></p>'
                + '<input type="hidden" class="form-control" value="" name="product_id[]" >'
                + '<input type="hidden" class="form-control" value="" name="picture[]" >'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.item')</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>'
                + '<div class="input-group">'
                + '<div class="input-group-addon"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>'
                + '<input type="text" class="form-control item_name" name="item_name[]" >'
                + '</div>'
                + '</div>'
                + '<div class="form-group">'
                + '<textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2"></textarea>'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
			+'<div class="hd">Type</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Type</label>'
                + '<input type="text" readonly="" class="form-control" value="services" placeholder="services" name="invoice_item_type[]">'
                + '</div>'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1 text-right visible-md visible-lg">'
			+'<div class="hd">Action</div>'
                + '<button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>'
                + '</div>'
				+'<div class="w-100"></div>'

                + '<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.qty')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>'
                + '<input type="number" min="1" class="form-control quantity" value="1" name="quantity[]" >'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.unitPrice')</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>'
                + '<input type="text" min="0" class="form-control cost_per_item" value="0" name="cost_per_item[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
			+'<div class="hd">Markup %</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup</label>'
                + '<input type="number" step=any class="form-control markup" value="" placeholder="0.00" name="markup[]">'
                + '<span class="help-block markup_txt"></span>'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
			+'<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup fix</label>'
                + '<input type="number" step=any class="form-control markup_fix" value="" placeholder="0.00" name="markup_fix[]">'
                + '<span class="help-block markup_fix_txt"></span>'
                + '</div>'
                + '</div>'
                + '</div>'
        
        
        
                + '<div class="col-md-1">'
			+'<div class="hd">Sale Price</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Sale Price</label>'
                + '<input type="number" step=any class="form-control sale_price" value="" placeholder="0.00" name="sale_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
			+'<div class="hd">Shipping</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Shipping Price</label>'
                + '<input type="number" step=any class="form-control shipping_price" value="" placeholder="0.00" name="shipping_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'


                + '<div class="col-md-1">'
                +'<div class="hd">@lang('modules.invoices.tax')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.tax')</label>'
                + '<select id="multiselect' + i + '" name="taxes[' + i + '][]"  multiple="multiple" class="selectpicker form-control type">'
                    @foreach($taxes as $tax)
                + '<option data-rate="{{ $tax->rate_percent }}" value="{{ $tax->id }}">{{ $tax->tax_name.': '.$tax->rate_percent }}%</option>'
                    @endforeach
                + '</select>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
                + '<div class="hd">Group <a href="javascript:;" class="group-settings" ><i class="ti-settings text-info"></i></a></div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Group</label>'
                + '<select id="groupselect' + i + '"  name="groups[' + i + ']" class="selectpicker form-control type">'
                + '<option value="">Nothing selected</option>'
                    @foreach($groups as $group)
                + '<option value="{{ $group->id }}">{{ $group->group_name}}</option>'
                    @endforeach
                + '</select>'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1 text-center">'
			+'<div class="hd">@lang('modules.invoices.amount')</div>'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>'
                + '<p class="form-control-static"><span class="amount-html">0.00</span></p>'
                + '<input type="hidden" class="amount" name="amount[]">'
                + '</div>'

                + '<div class="col-md-1 hidden-md hidden-lg">'
                + '<div class="row">'
                + '<button type="button" class="btn remove-item btn-danger"><i class="fa fa-remove"></i> @lang('app.remove')</button>'
                + '</div>'
                + '</div>'

                + '</div>';

            $(item).hide().appendTo("#sortable").fadeIn(500);
            $('#multiselect' + i).selectpicker();
            $('#groupselect' + i).selectpicker()
        });
        
        $('#add-proposed').click(function () {
            var i = $(document).find('.item_name').length;
            var item = '<div class="col-xs-12 item-row margin-top-5 d-flex-border">'
            
                + '<div class="col-md-1 invisible">'
			+ '<div class="hd">@lang('app.product.tabs.picture')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>'
                + '<p class="form-control-static"><img src="{{ asset('img/img-dummy.jpg') }}" alt="product" width="100" height="100"></p>'
                + '<input type="hidden" class="form-control" value="" name="product_id[]" >'
                + '<input type="hidden" class="form-control" value="" name="picture[]" >'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.item')</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>'
                + '<div class="input-group">'
                + '<div class="input-group-addon"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>'
                + '<input type="text" class="form-control item_name" name="item_name[]" >'
                + '</div>'
                + '</div>'
                + '<div class="form-group">'
                + '<textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2"></textarea>'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
			+'<div class="hd">Type</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Type</label>'
                + '<input type="text" readonly="" class="form-control" value="proposed" placeholder="proposed" name="invoice_item_type[]">'
                + '</div>'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1 text-right visible-md visible-lg">'
			+'<div class="hd">Action</div>'
                + '<button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>'
                + '</div>'
				+'<div class="w-100"></div>'

                + '<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.qty')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>'
                + '<input type="number" min="1" class="form-control quantity" value="1" name="quantity[]" >'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
			+'<div class="hd">@lang('modules.invoices.unitPrice')</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>'
                + '<input type="text" min="0" class="form-control cost_per_item" value="0" name="cost_per_item[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
			+'<div class="hd">Markup %</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup</label>'
                + '<input type="number" step=any class="form-control markup" value="" placeholder="0.00" name="markup[]">'
                + '<span class="help-block markup_txt"></span>'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
			+'<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup fix</label>'
                + '<input type="number" step=any class="form-control markup_fix" value="" placeholder="0.00" name="markup_fix[]">'
                + '<span class="help-block markup_fix_txt"></span>'
                + '</div>'
                + '</div>'
                + '</div>'
        
        
        
                + '<div class="col-md-1">'
			+'<div class="hd">Sale Price</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Sale Price</label>'
                + '<input type="number" step=any class="form-control sale_price" value="" placeholder="0.00" name="sale_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
			+'<div class="hd">Shipping</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Shipping Price</label>'
                + '<input type="number" step=any class="form-control shipping_price" value="" placeholder="0.00" name="shipping_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'


                + '<div class="col-md-1">'
                +'<div class="hd">@lang('modules.invoices.tax')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.tax')</label>'
                + '<select id="multiselect' + i + '" name="taxes[' + i + '][]"  multiple="multiple" class="selectpicker form-control type">'
                    @foreach($taxes as $tax)
                + '<option data-rate="{{ $tax->rate_percent }}" value="{{ $tax->id }}">{{ $tax->tax_name.': '.$tax->rate_percent }}%</option>'
                    @endforeach
                + '</select>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
                + '<div class="hd">Group <a href="javascript:;" class="group-settings" ><i class="ti-settings text-info"></i></a></div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Group</label>'
                + '<select id="groupselect' + i + '"  name="groups[' + i + ']" class="selectpicker form-control type">'
                + '<option value="">Nothing selected</option>'
                    @foreach($groups as $group)
                + '<option value="{{ $group->id }}">{{ $group->group_name}}</option>'
                    @endforeach
                + '</select>'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1 text-center">'
			+'<div class="hd">@lang('modules.invoices.amount')</div>'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>'
                + '<p class="form-control-static"><span class="amount-html">0.00</span></p>'
                + '<input type="hidden" class="amount" name="amount[]">'
                + '</div>'

                + '<div class="col-md-1 hidden-md hidden-lg">'
                + '<div class="row">'
                + '<button type="button" class="btn remove-item btn-danger"><i class="fa fa-remove"></i> @lang('app.remove')</button>'
                + '</div>'
                + '</div>'

                + '</div>';

            $(item).hide().appendTo("#sortable").fadeIn(500);
            $('#multiselect' + i).selectpicker();
            $('#groupselect' + i).selectpicker()
        });

    $('#updatePayments').on('click', '.remove-item', function () {
        $(this).closest('.item-row').fadeOut(300, function () {
            $(this).remove();
            calculateTotal();
        });
    });
    
    $('#updatePayments').on('keyup change', '.markup , .cost_per_item', function () {
        var quantity = 0;
            if($(this).closest('.item-row').find('.quantity').val()) {
                 quantity = $(this).closest('.item-row').find('.quantity').val();
            }

            var perItemCost = 0;
            if($(this).closest('.item-row').find('.cost_per_item').val()) {
                perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            }
            
            
            var markup = 0;
            if($(this).closest('.item-row').find('.markup').val()) {
                markup = $(this).closest('.item-row').find('.markup').val();
            }
            
            $(this).closest('.item-row').find('.markup_fix').val('0.00');
            $(this).closest('.item-row').find('.markup_fix_txt').html('');
            
            var shipping_price = 0;
            if($(this).closest('.item-row').find('.shipping_price').val()) {
                shipping_price = parseFloat($(this).closest('.item-row').find('.shipping_price').val());
            }
            
            var amount_1 = (quantity * perItemCost);
            var sale_price_cal = decimalupto2(amount_1+((markup/100)*amount_1)).toFixed(2); // Sale = Qty + markup + unit cost 
            $(this).closest('.item-row').find('.sale_price').val(sale_price_cal);
            
            $(this).closest('.item-row').find('.markup_txt').html(cur_symbol + ' (' + decimalupto2(((markup/100)*amount_1)).toFixed(2) +')');
            
            var sale_price = parseFloat($(this).closest('.item-row').find('.sale_price').val());
            
            //var amount = (quantity * perItemCost);
            var amount = (sale_price + shipping_price);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount).toFixed(2));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount).toFixed(2));

            calculateTotal();


    });
    
    $('#updatePayments').on('keyup change', '.markup_fix , .cost_per_item', function () {
        var quantity = 0;
            if($(this).closest('.item-row').find('.quantity').val()) {
                 quantity = $(this).closest('.item-row').find('.quantity').val();
            }

            var perItemCost = 0;
            if($(this).closest('.item-row').find('.cost_per_item').val()) {
                perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            }
            
            
            $(this).closest('.item-row').find('.markup').val('0.00');
            $(this).closest('.item-row').find('.markup_txt').html('');
             
            var markup_fix = 0
            if($(this).closest('.item-row').find('.markup_fix').val()) {
                markup_fix = $(this).closest('.item-row').find('.markup_fix').val();
                markup_fix = markup_fix*1;
            }
            
            var shipping_price = 0;
            if($(this).closest('.item-row').find('.shipping_price').val()) {
                shipping_price = parseFloat($(this).closest('.item-row').find('.shipping_price').val());
            }
            
            var amount_1 = (quantity * perItemCost);
            var sale_price_cal = decimalupto2(amount_1+markup_fix).toFixed(2);
            $(this).closest('.item-row').find('.sale_price').val(sale_price_cal);
            
            $(this).closest('.item-row').find('.markup_fix_txt').html(' (' + decimalupto2((markup_fix/amount_1)*100).toFixed(2) +'%)');
            
            var sale_price = parseFloat($(this).closest('.item-row').find('.sale_price').val());
            
            //var amount = (quantity * perItemCost);
            var amount = (sale_price + shipping_price);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount).toFixed(2));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount).toFixed(2));

            calculateTotal();


    });

    $('#updatePayments').on('keyup change', '.quantity,.cost_per_item,.item_name, .discount_value, .card_processing_value, .deposit_request,  .tax_on_total , .sale_price, .shipping_price', function () {
            var quantity = 0;
            if($(this).closest('.item-row').find('.quantity').val()) {
                 quantity = $(this).closest('.item-row').find('.quantity').val();
            }

            var perItemCost = 0;
            if($(this).closest('.item-row').find('.cost_per_item').val()) {
                perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            }
            
            
            var markup = 0;
            if($(this).closest('.item-row').find('.markup').val()) {
                markup = $(this).closest('.item-row').find('.markup').val();
            }
            
            var markup_fix = 0
            if($(this).closest('.item-row').find('.markup_fix').val()) {
                markup_fix = $(this).closest('.item-row').find('.markup_fix').val();
                markup_fix = markup_fix*1
            }
            
            var shipping_price = 0;
            if($(this).closest('.item-row').find('.shipping_price').val()) {
                shipping_price = parseFloat($(this).closest('.item-row').find('.shipping_price').val());
            }
            
            var amount_1 = (quantity * perItemCost);
            
            if(markup_fix != '' && markup_fix != 0 && markup_fix != '0.00') {
                var sale_price_cal = decimalupto2(amount_1+markup_fix).toFixed(2);
            } else {
                var sale_price_cal = decimalupto2(amount_1+((markup/100)*amount_1)).toFixed(2); // Sale = Qty + markup + unit cost 
            }
            
            $(this).closest('.item-row').find('.sale_price').val(sale_price_cal);
            
            var sale_price = parseFloat($(this).closest('.item-row').find('.sale_price').val());
            
            //var amount = (quantity * perItemCost);
            var amount = (sale_price + shipping_price);
            

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount).toFixed(2));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount).toFixed(2));

            calculateTotal();



    });

   $('#updatePayments').on('change','.type, #discount_type , #card_processing_type, #deposit_request_type', function () {
        var quantity = 0;
            if($(this).closest('.item-row').find('.quantity').val()) {
                 quantity = $(this).closest('.item-row').find('.quantity').val();
            }

            var perItemCost = 0;
            if($(this).closest('.item-row').find('.cost_per_item').val()) {
                perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            }
            
            
            var markup = 0;
            if($(this).closest('.item-row').find('.markup').val()) {
                markup = $(this).closest('.item-row').find('.markup').val();
            }
            
            var markup_fix = 0;
            if($(this).closest('.item-row').find('.markup_fix').val()) {
                markup_fix = $(this).closest('.item-row').find('.markup_fix').val();
                markup_fix = markup_fix*1;
            }
            
            var shipping_price = 0;
            if($(this).closest('.item-row').find('.shipping_price').val()) {
                shipping_price = parseFloat($(this).closest('.item-row').find('.shipping_price').val());
            }
            
            var amount_1 = (quantity * perItemCost);
            
            if(markup_fix != '' && markup_fix != 0 && markup_fix != '0.00') {
                var sale_price_cal = decimalupto2(amount_1+markup_fix).toFixed(2);
            } else {
                var sale_price_cal = decimalupto2(amount_1+((markup/100)*amount_1)).toFixed(2); // Sale = Qty + markup + unit cost 
            }
            
            $(this).closest('.item-row').find('.sale_price').val(sale_price_cal);
            
            var sale_price = parseFloat($(this).closest('.item-row').find('.sale_price').val());
            
            //var amount = (quantity * perItemCost);
            var amount = (sale_price + shipping_price);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount).toFixed(2));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount).toFixed(2));

            calculateTotal();


    });

    function calculateTotal()
    {
        var subtotal = 0;
        var discount = 0;
        var tax = '';
        var taxList = new Object();
        var taxTotal = 0;
        var discountType = $('#discount_type').val();
        var discountValue = $('.discount_value').val();
        
        var cardProcessingAmount = 0;
        var cardProcessingType = $('#card_processing_type').val();
        var cardProcessingValue = $('.card_processing_value').val();
        
        var depositRequestAmount = 0;
        var depositRequestType = $('#deposit_request_type').val();
        var depositRequest = $('.deposit_request').val();
        
        
        

        $(".quantity").each(function (index, element) {
            var itemTax = [];
            var itemTaxName = [];
            var discountedAmount = 0;
            

            $(this).closest('.item-row').find('select.type option:selected').each(function (index) {
                itemTax[index] = $(this).data('rate');
                itemTaxName[index] = $(this).text();
            });
            var itemTaxId = $(this).closest('.item-row').find('select.type').val();

            var amount = parseFloat($(this).closest('.item-row').find('.amount').val());
            var shipping_amount = parseFloat($(this).closest('.item-row').find('.shipping_price').val());
            
            if(discountType == 'percent' && discountValue != ''){
                discountedAmount = parseFloat(amount - ((parseFloat(amount)/100)*parseFloat(discountValue)));
            }

            if(isNaN(amount)){ amount = 0; }
            if(isNaN(shipping_amount)){ shipping_amount = 0; }
            
            

            subtotal = (parseFloat(subtotal)+parseFloat(amount)).toFixed(2);

            if(itemTaxId != ''){
                for(var i = 0; i<=itemTaxName.length; i++)
                {
                    if(typeof (taxList[itemTaxName[i]]) === 'undefined'){
                        if (discountedAmount > 0) {
                            if(shipping_taxed == 'no') {
                                taxList[itemTaxName[i]] = ((parseFloat(itemTax[i])/100)*parseFloat((discountedAmount-shipping_amount)));  
                            } else {
                                taxList[itemTaxName[i]] = ((parseFloat(itemTax[i])/100)*parseFloat((discountedAmount)));  
                            }
                                                   
                        } else {
                            if(shipping_taxed == 'no') {
                                taxList[itemTaxName[i]] = ((parseFloat(itemTax[i])/100)*parseFloat((amount-shipping_amount)));
                            } else {
                                taxList[itemTaxName[i]] = ((parseFloat(itemTax[i])/100)*parseFloat((amount)));
                            }
                            
                        }
                    }
                    else{
                        if (discountedAmount > 0) {
                             if(shipping_taxed == 'no') {
                                 taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i])/100)*parseFloat((discountedAmount-shipping_amount))); 
                             } else {
                                 taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i])/100)*parseFloat((discountedAmount))); 
                             }
                              
                            //console.log(taxList[itemTaxName[i]]);
                         
                        } else {
                            if(shipping_taxed == 'no') {
                                taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i])/100)*parseFloat((amount-shipping_amount)));
                            } else {
                                taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i])/100)*parseFloat((amount)));
                            }
                        }
                    }
                }
            }
        });


        $.each( taxList, function( key, value ) {
            if(!isNaN(value)){
                tax = tax+'<div class="col-md-offset-8 col-md-2 text-right p-t-10">'
                    +key
                    +'</div>'
                    +'<p class="form-control-static col-xs-6 col-md-2" >'
                    +'<span class="tax-percent">'+(decimalupto2(value)).toFixed(2)+'</span>'
                    +'</p>';
                taxTotal = taxTotal+decimalupto2(value);
            }
        });

        if(isNaN(subtotal)){  subtotal = 0; }

        $('.sub-total').html(decimalupto2(subtotal).toFixed(2));
        $('.sub-total-field').val(decimalupto2(subtotal));

        

        if(discountValue != ''){
            if(discountType == 'percent'){
                discount = ((parseFloat(subtotal)/100)*parseFloat(discountValue));
            }
            else{
                discount = parseFloat(discountValue);
            }

        }
        
            var total_shipping_price = 0;
            $(".shipping_price").each(function (index) {
                total_shipping_price = total_shipping_price + parseFloat($(this).val());
            });
            
            var total_amount = 0;
            $(".amount").each(function (index) {
                total_amount = total_amount + parseFloat($(this).val());
            });
            
            if (isNaN(total_shipping_price)) {
                total_shipping_price = 0;
            }
            
            if (isNaN(total_amount)) {
                total_amount = 0;
            }
            
            
            
            var total_amount_without_shipp = parseFloat(total_amount - total_shipping_price);
            
            var total_tax_on_total = 0;
            $('select.tax_on_total option:selected').each(function (index) {
                var tax_on_total_rate = $(this).data('rate');
                total_tax_on_total = total_tax_on_total +  ((parseFloat(total_amount_without_shipp) / 100) * parseFloat(tax_on_total_rate));
            });
            
            if (isNaN(total_tax_on_total)) {
                total_tax_on_total = 0;
            }
            
            $('.shipping-total').html(decimalupto2(total_shipping_price).toFixed(2));
            $('.shipping-total-field').val(decimalupto2(total_shipping_price));
        
        if (cardProcessingValue != '') {
                if (cardProcessingType == 'percent') {
                    cardProcessingAmount = ((parseFloat(subtotal) / 100) * parseFloat(cardProcessingValue));
                } else {
                    cardProcessingAmount = parseFloat(cardProcessingValue);
                }
            }

        $('#invoice-taxes').html(tax);

        var totalAfterDiscount = decimalupto2(subtotal-discount);

        totalAfterDiscount = (totalAfterDiscount < 0) ? 0 : totalAfterDiscount;

        var total = decimalupto2(totalAfterDiscount + cardProcessingAmount + total_tax_on_total +taxTotal);
        
        if (depositRequest != '') {
            if (depositRequestType == 'percent') {
                depositRequestAmount = ((parseFloat(total) / 100) * parseFloat(depositRequest));
            } else {
                depositRequestAmount = parseFloat(depositRequest);
            }
        }
        
        if (isNaN(depositRequestAmount)) {
                depositRequestAmount = 0;
        }
        
        $('.total').html(total.toFixed(2));
        $('.total-field').val(total.toFixed(2));
        $('.total-tax-field').val(taxTotal.toFixed(2));
        
        $('.deposit-text').html(depositRequestAmount.toFixed(2));
        $('.deposit-field').val(depositRequestAmount.toFixed(2));

    }

    calculateTotal();

    function decimalupto2(num) {
        var amt =  Math.round(num * 100) / 100;
        return parseFloat(amt.toFixed(2));
    }
    
    $('.close-md').on('click', function (event) {
            $('#produbctsModal').hide()
        });
        
        $('#sel_product').on('click', function (event) {
            event.preventDefault();
            $.each($("input[name='select_product']:checked"), function(){
                var id = $(this).val();
                var currencyId = $('#currency_id').val();
                var cal_from = 'invoice';
                $.easyAjax({
                    url: '{{ route('admin.all-invoices.update-item') }}',
                    type: "GET",
                    data: {id: id, currencyId: currencyId, cal_from:cal_from},
                    success: function (response) {
                        $(response.view).hide().appendTo("#sortable").fadeIn(500);
                        var noOfRows = $(document).find('#sortable .item-row').length;
                        var i = $(document).find('.item_name').length - 1;
                        var itemRow = $(document).find('#sortable .item-row:nth-child(' + noOfRows + ') select.type');
                        itemRow.attr('id', 'multiselect' + i);
                        itemRow.attr('name', 'taxes[' + i + '][]');
                        $(document).find('#multiselect' + i).selectpicker();
                        
                        var itemRow2 = $(document).find('#sortable .item-row:nth-child(' + noOfRows + ') select.type2');
                        itemRow2.attr('id', 'groupselect' + i);
                        itemRow2.attr('name', 'groups[' + i + ']');
                        $(document).find('#groupselect' + i).selectpicker();
                        
                        calculateTotal();
                    }
                });
                
            });
            
            $('input[name="select_product"]').each(function() {
			this.checked = false;
            });
            $('#produbctsModal').hide()
        });

    $('.add-product').on('click', function(event) {
        event.preventDefault();
        var id = $(this).data('pk');
        var currencyId = $('#currency_id').val();
        $.easyAjax({
            url:'{{ route('admin.all-invoices.update-item') }}',
            type: "GET",
            data: { id: id, currencyId: currencyId },
            success: function(response) {
                $(response.view).hide().appendTo("#sortable").fadeIn(500);
                var noOfRows = $(document).find('#sortable .item-row').length;
                var i = $(document).find('.item_name').length-1;
                var itemRow = $(document).find('#sortable .item-row:nth-child('+noOfRows+') select.type');
                itemRow.attr('id', 'multiselect'+i);
                itemRow.attr('name', 'taxes['+i+'][]');
                $(document).find('#multiselect'+i).selectpicker();
                calculateTotal();
            }
        });
    });
    
    $('.filter-products').on('change', function(event) {
        event.preventDefault();
        var projectId = $('#flt_project_id').val();
        var salesCategory = $('#flt_salesCategory').val();
        var locationCode = $('#flt_locationCode').val();
        var vendorId = $('#flt_vendor_id').val();

        $.easyAjax({
            url:'{{ route('admin.products.filter-products') }}',
            type: "GET",
            data: { project_id: projectId, salesCategory: salesCategory , locationCode: locationCode  , vendor_id: vendorId},
            success: function(response) {
                $('#produbcts_table_data').html(response.view);
                
            }
        });
    });

       $(document).on('change','.product_img',function (){
           let $ele = $(this).closest('.form-control-static').find('img');
           displayImg(this,$ele);
       });
       function displayImg(input,$ele) {
           if (input.files && input.files[0]) {
               var reader = new FileReader();
               reader.onload = function(event) {
                   $ele.attr('src', event.target.result);
               }
               reader.readAsDataURL(input.files[0]);
           }
       }
    
</script>
@endpush

