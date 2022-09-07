@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.all-invoices.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
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
            <div class="panel-heading p-b-10 m-b-20"> @lang('modules.invoices.addInvoice')</div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body p-0">
                    {!! Form::open(['id'=>'storePayments','class'=>'ajax-form','method'=>'POST','enctype' => 'multipart/form-data']) !!}
                    <div class="form-body">
                        <div class="row">

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.invoice') #</label>
                                   
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="invoicePrefix"
                                                    data-prefix="{{ $invoiceSetting->invoice_prefix }}">{{ $invoiceSetting->invoice_prefix }}</span>#<span
                                                    class="noOfZero"
                                                    data-zero="{{ $invoiceSetting->invoice_digit }}">{{ $zero }}</span>
                                            </div>
                                            <input type="text" class="form-control readonly-background"
                                                name="invoice_number" id="invoice_number"
                                                value="@if(is_null($lastInvoice))1 @else{{ ($lastInvoice) }}@endif">
                                        </div>
                                   
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Select Invoice Type</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="text" value="client" name="invoice_type" id="invoice_type"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(in_array('projects', $modules))
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.project')</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <select class="select2 form-control" data-placeholder="Choose Project"
                                                id="project_id" name="project_id">
                                                <option value="">--</option>
                                                @foreach($projects as $project)
                                                <option client_id_attr="{{ $project->client_id }}" value="{{ $project->id }}" {{ $project->id == $default_project_id ? 'selected' : ''}} >{{ ucwords($project->project_name) }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @endif

                            <div class="col-md-4">
                                <div class="form-group" id="client_name">
                                    <label class="control-label" id="companyClientName">@lang('app.client_name')</label>
                                    <div class="row">
                                        <div class="col-md-12" id="client_company_div">
                                            <div class="input-icon">

                                            </div>
                                            <input type="text" readonly class="form-control" name="" id="company_name"
                                                value="">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.invoices.invoiceDate')</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" name="issue_date"
                                                    id="invoice_date"
                                                    value="{{ Carbon\Carbon::today()->format($global->date_format) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.dueDate')</label>
                                    <div class="input-icon">
                                        <input type="text" class="form-control" name="due_date" id="due_date"
                                            value="{{ Carbon\Carbon::today()->addDays($invoiceSetting->due_after)->format($global->date_format) }}">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.invoices.currency')</label>
                                    <select class="form-control" name="currency_id" id="currency_id">
                                        @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" @if($global->currency_id == $currency->id)
                                            selected
                                            @endif>{{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div style="display:none;" class="col-md-3">

                                <div class="form-group">
                                    <label class="control-label">@lang('modules.invoices.isRecurringPayment') </label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <select class="form-control" name="recurring_payment" id="recurring_payment"
                                                onchange="recurringPayment()">
                                                <option value="no">@lang('app.no')</option>
                                                <option value="yes">@lang('app.yes')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 recurringPayment" style="display: none;">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.invoices.billingFrequency')</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <select class="form-control" name="billing_frequency"
                                                id="billing_frequency">
                                                <option value="day">@lang('app.day')</option>
                                                <option value="week">@lang('app.week')</option>
                                                <option value="month">@lang('app.month')</option>
                                                <option value="year">@lang('app.year')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 recurringPayment" style="display: none;">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.invoices.billingInterval')</label>
                                    <div class="input-icon">
                                        <input type="text" class="form-control" name="billing_interval"
                                            id="billing_interval" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 recurringPayment" style="display: none;">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.invoices.billingCycle')</label>
                                    <div class="input-icon">
                                        <input type="text" class="form-control" name="billing_cycle" id="billing_cycle"
                                            value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Tags</label>
                                <select multiple data-role="tagsinput" name="tags[]" id="tags">
                                </select>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Show combined version to client</label>
                                            <div class="input-icon">
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="combine_line_items" name="combine_line_items"
                                                        class="js-switch " data-color="#00c292" data-secondary-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                            </div>
                            <div class="col-md-4">
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
                                            class="js-switch " data-color="#00c292" data-secondary-color="#f96262" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div id="shippingAddress">

                                </div>
                            </div>
                        </div>
                        
                          
                        
                        <hr>
                        <div class="row">
                            <div class="col-md-12 border-bottom">
                                <div class="btn-group m-b-10">
                                    <button aria-expanded="false" data-toggle="dropdown"
                                        class="btn btn-info dropdown-toggle waves-effect waves-light"
                                        type="button" onclick="$('#produbctsModal').show();">+ @lang('app.menu.addProducts')</span>
                                    </button>
                                    <ul style="display:none;" role="menu" class="dropdown-menu dropdown-content">
                                        @foreach($products as $product)
                                        <li class="m-b-10">
                                            <div class="row m-t-10">
                                                <div class="col-md-6" style="padding-left: 30px">
                                                    {{ $product->name }}
                                                </div>
                                                <div class="col-md-6" style="text-align: right;padding-right: 30px;">
                                                    <a href="javascript:;" data-pk="{{ $product->id }}"
                                                        class="btn btn-success btn btn-outline btn-xs waves-effect add-product">@lang('app.add')
                                                        <i class="fa fa-plus" aria-hidden="true"></i></a>
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
                                <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                    @lang('modules.invoices.item')
                                </div>
                                <div class="col-md-1 font-bold" style="padding: 8px 15px">
                                    Type
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
                                    @lang('modules.invoices.tax')
                                </div>

                                <div class="col-md-1 text-center font-bold" style="padding: 8px 15px">
                                    @lang('modules.invoices.amount')
                                </div>

                                <div class="col-md-1" style="padding: 8px 15px">
                                    &nbsp;
                                </div>

                            </div>


                            <div id="sortable" class="col-md-12 border-0 p-0">
                                
                                @if($review_products)
                                
                                @foreach($products as $key => $item)

                                        @if(in_array($item->id, $review_products))
                                        
                                        <?php

                                                $fileUrl = asset('img/img-dummy.jpg');
                                                if (!empty($item->picture)) {
                                                    $pictures = json_decode($item->picture);
                                                    if($pictures[0]) {
                                                         $fileUrl = asset('user-uploads/products/'.$item->id.'/'.$pictures[0].'');
                                                         $fileName = $pictures[0];
                                                    }
                                                }

                                        ?>
                                        
                                        
                                        
                                        <div class="col-xs-12 item-row margin-top-5 d-flex-border">
                                            
                                            <div class="col-md-1">
												<div class="hd">@lang('app.product.tabs.picture')</div>
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>
                                                    
                                                    <p class="form-control-static"><img src="{{ $fileUrl }}" alt="product"></p>
                                                    <input type="hidden" class="form-control" value="{{ $fileName }}" name="picture[]">
                                                    <input type="hidden" class="form-control" value="{{ $item->id }}" name="product_id[]" >
                                                    
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
                                                                   value="{{ $item->name }}" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="item_summary[]" class="form-control" placeholder="@lang('app.description')" rows="2">{{ $item->description }}</textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            
                                            <div class="col-md-1">
												<div class="hd">Type</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Type</label>
                                                            <input type="text" readonly="" class="form-control" name="invoice_item_type[]" value="product" placeholder="product">
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
                                                           value="1" name="quantity[]"
                                                           >
                                                </div>


                                            </div>

                                            <div class="col-md-1">
												<div class="hd">@lang('modules.invoices.unitPrice')</div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>
                                                        <input type="text" min="" class="form-control cost_per_item"
                                                               name="cost_per_item[]" value="{{ $item->cost_per_unit }}"
                                                               >
                                                    </div>
                                                </div>

                                            </div>
                                            
                                            <div class="col-md-1">
												<div class="hd">Markup %</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Markup</label>
                                                            <input type="number" step=any class="form-control markup" name="markup[]" value="{{ $item->default_markup }}" placeholder="0.00">
                                                            <span class="help-block markup_txt"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="col-md-1 ">
												<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Markup</label>
                                                            <input type="number" step=any class="form-control markup_fix" name="markup_fix[]" value="{{ $item->markup_fix }}" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
												<div class="hd">Sale Price</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Sale Price</label>
                                                            <input type="number" step=any class="form-control sale_price" name="sale_price[]" value="" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 @if($item->invoice_item_type == 'services') invisible @endif">
												<div class="hd">Shipping</div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <label class="control-label hidden-md hidden-lg">Shipping Price</label>
                                                            <input type="number" step=any class="form-control shipping_price" name="shipping_price[]" value="" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="col-md-1">
												<div class="hd">@lang('modules.invoices.tax')</div>

                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">@lang('modules.invoices.tax')</label>
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
                                                <div class="hd">Group</div>

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
                                        
                                        
                                        
                                        

                                        @endif
                                @endforeach
                                
                                
                                @else
                                
                                    <div class="col-xs-12 item-row margin-top-5 d-flex-border">
                                        <div class="col-md-1">
												<div class="hd">@lang('app.product.tabs.picture')</div>
                                            <div class="form-group">
                                                <label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>
                                                <p class="form-control-static">
                                                    <label>
                                                        <input type="file" class="product_img" style="display: none" name="product_img[]">
                                                        <img src="{{ asset('img/img-dummy.jpg') }}" alt="product">
                                                    </label>
                                                </p>
                                                <input type="hidden" class="form-control" value="" name="picture[]">
                                                <input type="hidden" class="form-control" value="" name="product_id[]" >
                                            </div>

                                        </div>

                                        <div class="col-md-1">
												<div class="hd">@lang('modules.invoices.item')</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label
                                                        class="control-label hidden-md hidden-lg">@lang('modules.invoices.item')</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><span
                                                                class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                                        </div>
                                                        <input type="text" class="form-control item_name"
                                                            name="item_name[]">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <textarea name="item_summary[]" class="form-control"
                                                        placeholder="@lang('app.description')" rows="2"></textarea>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-1">
												<div class="hd">Type</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">Type</label>
                                                    <input type="text" readonly=""  class="form-control" name="invoice_item_type[]" value="product" placeholder="product">
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
                                                <label
                                                    class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>
                                                <input type="number" min="1" class="form-control quantity" value="1"
                                                    name="quantity[]">
                                            </div>


                                        </div>

                                        <div class="col-md-1">
												<div class="hd">@lang('modules.invoices.unitPrice')</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label
                                                        class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>
                                                    <input type="text" class="form-control cost_per_item"
                                                        name="cost_per_item[]" value="0">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-1">
												<div class="hd">Markup %</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">Markup %</label>
                                                    <input type="number" step=any class="form-control markup" name="markup[]" value="" placeholder="0.00">
                                                    <span class="help-block markup_txt"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
												<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">Markup fix</label>
                                                    <input type="number" step=any class="form-control markup_fix" name="markup_fix[]" value="" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
												<div class="hd">Sale Price</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">Sale Price</label>
                                                    <input type="number" step=any class="form-control sale_price" name="sale_price[]" value="" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
												<div class="hd">Shipping</div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <label class="control-label hidden-md hidden-lg">Shipping Price</label>
                                                    <input type="number" step=any class="form-control shipping_price" name="shipping_price[]" value="" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
												<div class="hd">@lang('modules.invoices.tax')</div>

                                            <div class="form-group">
                                                <label
                                                    class="control-label hidden-md hidden-lg">@lang('modules.invoices.tax')</label>
                                                <select id="multiselect" name="taxes[0][]" multiple="multiple"
                                                    class="selectpicker form-control type">
                                                    @foreach($taxes as $tax)
                                                    <option data-rate="{{ $tax->rate_percent }}" value="{{ $tax->id }}">
                                                        {{ $tax->tax_name }}
                                                        : {{ $tax->rate_percent }}%
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                                                                
                                        <div class="col-md-1">
                                        <div class="hd">Group</div>

                                            <div class="form-group">
                                                <label
                                                    class="control-label hidden-md hidden-lg">Group</label>
                                                    <select id="groupselect" name="groups[0]" class="selectpicker form-control type">
                                                    <option value="">Nothing selected</option>
                                                    @foreach($groups as $group)
                                                    <option value="{{ $group->id }}">
                                                        {{ $group->group_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-md-1 border-dark  text-center">
												<div class="hd">@lang('modules.invoices.amount')</div>
                                            <label
                                                class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>

                                            <p class="form-control-static"><span class="amount-html">0.00</span></p>
                                            <input type="hidden" class="amount" name="amount[]" value="0">
                                        </div>
                                        <div class="col-md-1 hidden-md hidden-lg">
                                            <div class="row">
                                                <button type="button" class="btn btn-circle remove-item btn-danger"><i
                                                        class="fa fa-remove"></i> @lang('app.remove')</button>
                                            </div>
                                        </div>

                                    </div>
                                @endif
                            </div>
                            <div class="col-xs-12 m-t-5">
                                
                            </div>
                            
                            <div class="col-xs-12 ">
                                
                                <div class="row">
                                    <div class="col-md-offset-9 col-xs-6 col-md-1 text-right p-t-10">
                                        Shipping Total</div>

                                    <p class="form-control-static col-xs-6 col-md-2">
                                        <span class="shipping-total">0.00</span>
                                    </p>

                                    <input type="hidden" class="shipping-total-field" name="shipping_total" value="0">
                                </div>


                                <div class="row">
                                    <div class="col-md-offset-9 col-xs-6 col-md-1 text-right p-t-10">
                                        @lang('modules.invoices.subTotal')</div>

                                    <p class="form-control-static col-xs-6 col-md-2">
                                        <span class="sub-total">0.00</span>
                                    </p>


                                    <input type="hidden" class="sub-total-field" name="sub_total" value="0">
                                </div>

                                <div class="row">
                                    <div class="col-md-offset-9 col-md-1 text-right p-t-10">
                                        @lang('modules.invoices.discount')
                                    </div>
                                    <div class="form-group col-xs-6 col-md-1">
                                        <input type="number" min="0" value="0" name="discount_value"
                                            class="form-control discount_value">
                                    </div>
                                    <div class="form-group col-xs-6 col-md-1">
                                        <select class="form-control" name="discount_type" id="discount_type">
                                            <option value="percent">%</option>
                                            <option value="fixed">@lang('modules.invoices.amount')</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-offset-9 col-md-1 text-right">
                                        Credit Card Processing
                                    </div>
                                    <div class="form-group col-xs-6 col-md-1">
                                        <input type="number" min="0" value="0" name="card_processing_value"
                                            class="form-control card_processing_value">
                                    </div>
                                    <div class="form-group col-xs-6 col-md-1">
                                        <select class="form-control" name="card_processing_type" id="card_processing_type">
                                            <option value="percent">%</option>
                                            <option value="fixed">@lang('modules.invoices.amount')</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-offset-9 col-md-1 text-right">
                                        Deposit Request
                                    </div>
                                    <div class="form-group col-xs-6 col-md-1">
                                        <input type="number" min="0" value="0" name="deposit_request"
                                            class="form-control deposit_request">
                                    </div>
                                    <div class="form-group col-xs-6 col-md-1">
                                        <select class="form-control" name="deposit_request_type" id="deposit_request_type">
                                            <option value="percent">%</option>
                                            <option value="fixed">@lang('modules.invoices.amount')</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-offset-9 col-md-1 text-right p-t-5">
                                        Tax
                                    </div>
                                    <div class="form-group col-xs-6 col-md-2">
                                        <select id="multiselect" name="tax_on_total[]" multiple="multiple"
                                                    class="selectpicker form-control type tax_on_total">
                                                    @foreach($taxes as $tax)
                                                    <option data-rate="{{ $tax->rate_percent }}" value="{{ $tax->id }}">
                                                        {{ $tax->tax_name }}
                                                        : {{ $tax->rate_percent }}%
                                                    </option>
                                                    @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row m-t-5" id="invoice-taxes">
                                    <div class="col-md-offset-9 col-md-1 text-right p-t-10">
                                        @lang('modules.invoices.tax')
                                    </div>

                                    <p class="form-control-static col-xs-6 col-md-2">
                                        <span class="tax-percent">0.00</span>
                                    </p>
                                </div>

                                <div class="row m-t-5 font-bold">
                                    <div class="col-md-offset-9 col-md-1 col-xs-6 text-right p-t-10">
                                        @lang('modules.invoices.total')</div>

                                    <p class="form-control-static col-xs-6 col-md-2">
                                        <span class="total">0.00</span>
                                    </p>
                                    
                                    <div class="col-md-offset-9 col-md-1 col-xs-6 text-right p-t-10">
                                        Deposit Request Total</div>

                                    <p class="form-control-static col-xs-6 col-md-2">
                                        <span class="deposit-text">0.00</span>
                                    </p>


                                    <input type="hidden" class="total-field" name="total" value="0">
                                    <input type="hidden" class="total-tax-field" name="total_tax" value="0">
                                    <input type="hidden" class="deposit-field" name="deposit_req" value="0">
                                    
                                </div>

                            </div>

                        </div>


                        <div class="col-md-12">

                            <div class="form-group">
                                <label class="control-label">@lang('app.note')</label>
                                <textarea class="form-control" name="note" id="note" rows="5"></textarea>
                            </div>

                        </div>


                    </div>
                    <div class="form-actions" style="margin-top: 70px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="dropup">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        @lang('app.save') <span class="caret"></span>
                                    </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" class="save-form" data-type="save">
                                                <i class="fa fa-save"></i> @lang('app.save')
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="javascript:;" class="save-form" data-type="draft">
                                                <i class="fa fa-file"></i> @lang('app.saveDraft')
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="javascript:void(0);" class="save-form" data-type="send">
                                                <i class="fa fa-send"></i> @lang('app.saveSend')
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div> <!-- .row -->

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
                                     <?php if(isset($pictures[0])) { ?>
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
<div class="modal fade bs-modal-md in" id="taxModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                <button type="button" class="btn blue">@lang('app.save') @lang('changes')</button>
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
        getCompanyName();
        
        var cur_symbol = '<?php echo $global->currency->currency_symbol ?>';
        var shipping_taxed = '<?php echo $invoiceSetting->shipping_taxed; ?>';

        function getCompanyName() {
            var projectID = $('#project_id').val();
            var url = "{{ route('member.all-invoices.get-client-company') }}";
            if (projectID != '' && projectID !== undefined) {
                url = "{{ route('member.all-invoices.get-client-company',':id') }}";
                url = url.replace(':id', projectID);
            }
            $.ajax({
                type: 'GET',
                url: url,
                success: function (data) {
                    if (projectID != '') {
                        $('#companyClientName').text('{{ __('app.company_name') }}');
                    } else {
                        $('#companyClientName').text('{{ __('app.client_name') }}');
                    }

                    $('#client_company_div').html(data.html);
                    if ($('#show_shipping_address').prop('checked') === true) {
                        checkShippingAddress();
                    }
                }
            });
        }

            function getVendorList() {
                var url = "{{ url('member/finance/all-invoices/getVendors') }}";

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function (data) {

                        var html = "";
                        var vendor_dropdown = $("#_vendorList");
                        vendor_dropdown.empty();

                        $.each(data,function(i,item){
                            html += "<option value='" + item.id  + "'>" + item.name + "</option>"
                        })

                        vendor_dropdown.append(html);

                    }
                });
            }


        function checkShippingAddress() {
            var projectId = $('#project_id').val();
            var clientId = $('#client_company_id').length > 0 ? $('#client_company_id').val() : $('#client_id').val();
            var showShipping = $('#show_shipping_address').prop('checked') === true ? 'yes' : 'no';

            var url = `{{ route('member.all-invoices.checkShippingAddress') }}?showShipping=${showShipping}`;
            if (clientId !== '') {
                url += `&clientId=${clientId}`;
            }

            $.ajax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response) {
                        if (response.switch === 'off') {
                            showShippingSwitch.click();
                        } else {
                            if (response.show !== undefined) {
                                $('#shippingAddress').html('');
                            } else {
                                $('#shippingAddress').html(response.view);
                            }
                        }
                    }
                }
            });
        }

        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());
        });

        var showShippingSwitch = document.getElementById('show_shipping_address');

        showShippingSwitch.onchange = function () {
            if (showShippingSwitch.checked) {
                checkShippingAddress();
            } else {
                $('#shippingAddress').html('');
            }
        }

        $(function () {
            $("#sortable").sortable();
            $('.cost_per_item').change()
        });

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        jQuery('#invoice_date, #due_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart: '{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });

        $('.save-form').click(function () {
            //debugger;
            var type = $(this).data('type');
            calculateTotal();

            var discount = $('.discount-amount').html();
            var total = $('.total-field').val();

            if (parseFloat(discount) > parseFloat(total)) {
                $.toast({
                    heading: 'Error',
                    text: "{{ __('messages.discountMoreThenTotal') }}",
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 3500
                });
                return false;
            }

            let form = $('#storePayments');
            $.ajax({
                url: '{{route('member.all-invoices.store')}}',
                container: '#storePayments',
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
                // data: $('#storePayments').serialize() + "&type=" + type
            });
        });

        $('#add-item').click(function () {
            var i = $(document).find('.item_name').length;
            var item = '<div class="col-xs-12 item-row margin-top-5 d-flex-border">'
            
                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('app.product.tabs.picture')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>'
                + '<p class="form-control-static"><label> <input type="file" class="product_img" style="display: none" name="product_img[]">'+
                '<img src="{{ asset('img/img-dummy.jpg') }}" alt="product"> </label></p>'
                + '<input type="hidden" class="form-control" value="" name="product_id[]" >'
                + '<input type="hidden" class="form-control" value="" name="picture[]" >'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.item')</div>'
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
				+ '<div class="hd">Type</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Type</label>'
                + '<input type="text" readonly="" class="form-control" value="product" placeholder="product" name="invoice_item_type[]">'
                + '</div>'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1 text-right visible-md visible-lg">'
				+ '<div class="hd">Action</div>'
                + '<button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>'
                + '</div>'
				+ '<div class="w-100"></div>'

                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.qty')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>'
                + '<input type="number" min="1" class="form-control quantity" value="1" name="quantity[]" >'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.unitPrice')</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>'
                + '<input type="text" min="0" class="form-control cost_per_item" value="0" name="cost_per_item[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
				+ '<div class="hd">Markup %</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup %</label>'
                + '<input type="number" step=any class="form-control markup" value="" placeholder="0.00" name="markup[]">'
                + '<span class="help-block markup_txt"></span>'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
				+ '<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup fix</label>'
                + '<input type="number" step=any class="form-control markup_fix" value="" placeholder="0.00" name="markup_fix[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
        
        
        
                + '<div class="col-md-1">'
				+ '<div class="hd">Sale Price</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Sale Price</label>'
                + '<input type="number" step=any class="form-control sale_price" value="" placeholder="0.00" name="sale_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
				+ '<div class="hd">Shipping</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Shipping Price</label>'
                + '<input type="number" step=any class="form-control shipping_price" value="" placeholder="0.00" name="shipping_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'


                + '<div class="col-md-1">'
                + '<div class="hd">@lang('modules.invoices.tax')</div>'
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
                + '<div class="hd">Group</div>'
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
				+ '<div class="hd">@lang('modules.invoices.amount')</div>'
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
            $('#groupselect' + i).selectpicker();
        });
        
        $('#add-services').click(function () {
            var i = $(document).find('.item_name').length;
            var item = '<div class="col-xs-12 item-row margin-top-5 d-flex-border">'
            
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">@lang('app.product.tabs.picture')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>'
                + '<p class="form-control-static"><img src="{{ asset('img/img-dummy.jpg') }}" alt="product"></p>'
                + '<input type="hidden" class="form-control" value="" name="product_id[]" >'
                + '<input type="hidden" class="form-control" value="" name="picture[]" >'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.item')</div>'
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
				+ '<div class="hd">Type</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Type</label>'
                + '<input type="text" readonly="" class="form-control" value="services" placeholder="services" name="invoice_item_type[]">'
                + '</div>'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1 text-right visible-md visible-lg">'
				+ '<div class="hd">Action</div>'
                + '<button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>'
                + '</div>'
				+ '<div class="w-100"></div>'

                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.qty')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>'
                + '<input type="number" min="1" class="form-control quantity" value="1" name="quantity[]" >'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.unitPrice')</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>'
                + '<input type="text" min="0" class="form-control cost_per_item" value="0" name="cost_per_item[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">Markup %</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup %</label>'
                + '<input type="number" step=any class="form-control markup" value="" placeholder="0.00" name="markup[]">'
                + '<span class="help-block markup_txt"></span>'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup fix</label>'
                + '<input type="number" step=any class="form-control markup_fix" value="" placeholder="0.00" name="markup_fix[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
        
        
                + '<div class="col-md-1">'
				+ '<div class="hd">Sale Price</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Sale Price</label>'
                + '<input type="number" step=any class="form-control sale_price" value="" placeholder="0.00" name="sale_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">Shipping</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Shipping Price</label>'
                + '<input type="number" step=any class="form-control shipping_price" value="" placeholder="0.00" name="shipping_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'


                + '<div class="col-md-1">'
                + '<div class="hd">@lang('modules.invoices.tax')</div>'
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
                + '<div class="hd">Group</div>'
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
				+ '<div class="hd">@lang('modules.invoices.amount')</div>'
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
            $('#groupselect' + i).selectpicker();
        });
        
        $('#add-proposed').click(function () {
            var i = $(document).find('.item_name').length;
            var item = '<div class="col-xs-12 item-row margin-top-5 d-flex-border">'
            
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">@lang('app.product.tabs.picture')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('app.product.tabs.picture')</label>'
                + '<p class="form-control-static"><img src="{{ asset('img/img-dummy.jpg') }}" alt="product"></p>'
                + '<input type="hidden" class="form-control" value="" name="product_id[]" >'
                + '<input type="hidden" class="form-control" value="" name="picture[]" >'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.item')</div>'
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
				+ '<div class="hd">Type</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Type</label>'
                + '<input type="text" readonly="" class="form-control" value="proposed" placeholder="proposed" name="invoice_item_type[]">'
                + '</div>'
                + '</div>'
                + '</div>'

                + '<div class="col-md-1 text-right visible-md visible-lg">'
				+ '<div class="hd">Action</div>'
                + '<button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>'
                + '</div>'
				+ '<div class="w-100"></div>'

                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.qty')</div>'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>'
                + '<input type="number" min="1" class="form-control quantity" value="1" name="quantity[]" >'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1">'
				+ '<div class="hd">@lang('modules.invoices.unitPrice')</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>'
                + '<input type="text" min="0" class="form-control cost_per_item" value="0" name="cost_per_item[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">Markup %</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup %</label>'
                + '<input type="number" step=any class="form-control markup" value="" placeholder="0.00" name="markup[]">'
                + '<span class="help-block markup_txt"></span>'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">Markup {{ $global->currency->currency_symbol }}</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Markup fix</label>'
                + '<input type="number" step=any class="form-control markup_fix" value="" placeholder="0.00" name="markup_fix[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
        
        
                + '<div class="col-md-1">'
				+ '<div class="hd">Sale Price</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Sale Price</label>'
                + '<input type="number" step=any class="form-control sale_price" value="" placeholder="0.00" name="sale_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'
        
                + '<div class="col-md-1 invisible">'
				+ '<div class="hd">Shipping</div>'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">Shipping Price</label>'
                + '<input type="number" step=any class="form-control shipping_price" value="" placeholder="0.00" name="shipping_price[]">'
                + '</div>'
                + '</div>'
                + '</div>'


                + '<div class="col-md-1">'
                + '<div class="hd">@lang('modules.invoices.tax')</div>'
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
                + '<div class="hd">Group</div>'
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
				+ '<div class="hd">@lang('modules.invoices.amount')</div>'
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
            $('#groupselect' + i).selectpicker();
        });

        $('#storePayments').on('click', '.remove-item', function () {
            $(this).closest('.item-row').fadeOut(300, function () {
                $(this).remove();
                $('.item-row').each(function (index) {
                    $(this).find('.selectpicker').attr('name', 'taxes[' + index + '][]');
                    $(this).find('.selectpicker').attr('id', 'multiselect' + index);
                    $(this).find('.selectpicker').attr('id', 'groupselect' + index);
                });
                calculateTotal();
            });
        });
        
        $('#storePayments').on('keyup change', '.markup', function () {
            
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
        
        $('#storePayments').on('keyup change', '.markup_fix', function () {
            
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
            
            var sale_price = parseFloat($(this).closest('.item-row').find('.sale_price').val());
            
            //var amount = (quantity * perItemCost);
            var amount = (sale_price + shipping_price);
            

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount).toFixed(2));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount).toFixed(2));

            calculateTotal();


        });

        $('#storePayments').on('keyup change', '.quantity,.cost_per_item,.item_name, .discount_value, .card_processing_value, .deposit_request, .tax_on_total , .sale_price, .shipping_price', function () {
            
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

        $('#storePayments').on('change', '.type, #discount_type, #card_processing_type, #deposit_request_type', function () {
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

        function calculateTotal() {
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
                
                if (discountType == 'percent' && discountValue != '') {
                    discountedAmount = parseFloat(amount - ((parseFloat(amount) / 100) * parseFloat(discountValue)));
                }

                if (isNaN(amount)) {
                    amount = 0;
                }
                if(isNaN(shipping_amount)){ shipping_amount = 0; }
                

                subtotal = (parseFloat(subtotal) + parseFloat(amount)).toFixed(2);

                if (itemTaxId != '') {
                    for (var i = 0; i <= itemTaxName.length; i++) {
                        if (typeof (taxList[itemTaxName[i]]) === 'undefined') {
                            if (discountedAmount > 0) {
                                if(shipping_taxed == 'no') {
                                    taxList[itemTaxName[i]] = ((parseFloat(itemTax[i]) / 100) * parseFloat((discountedAmount-shipping_amount)));
                                } else {
                                    taxList[itemTaxName[i]] = ((parseFloat(itemTax[i]) / 100) * parseFloat((discountedAmount)));
                                }
                                
                            } else {
                                 if(shipping_taxed == 'no') {
                                     taxList[itemTaxName[i]] = ((parseFloat(itemTax[i]) / 100) * parseFloat((amount-shipping_amount)));
                                 } else {
                                     taxList[itemTaxName[i]] = ((parseFloat(itemTax[i]) / 100) * parseFloat((amount)));
                                 }
                                
                            }
                        } else {
                            if (discountedAmount > 0) {
                                if(shipping_taxed == 'no') {
                                    taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i]) / 100) * parseFloat((discountedAmount-shipping_amount)));
                                } else {
                                    taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i]) / 100) * parseFloat((discountedAmount)));
                                }
                                //console.log(taxList[itemTaxName[i]]);

                            } else {
                                if(shipping_taxed == 'no') {
                                    taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i]) / 100) * parseFloat((amount-shipping_amount)));
                                } else {
                                    taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i]) / 100) * parseFloat((amount)));
                                }
                                
                            }
                        }
                    }
                }
            });
            
            


            $.each(taxList, function (key, value) {
                if (!isNaN(value)) {
                    tax = tax + '<div class="col-md-offset-8 col-md-2 text-right p-t-10">'
                        + key
                        + '</div>'
                        + '<p class="form-control-static col-xs-6 col-md-2" >'
                        + '<span class="tax-percent">' + (decimalupto2(value)).toFixed(2) + '</span>'
                        + '</p>';
                    taxTotal = taxTotal + decimalupto2(value);
                }
            });

            if (isNaN(subtotal)) {
                subtotal = 0;
            }

            $('.sub-total').html(decimalupto2(subtotal).toFixed(2));
            $('.sub-total-field').val(decimalupto2(subtotal));


            if (discountValue != '') {
                if (discountType == 'percent') {
                    discount = ((parseFloat(subtotal) / 100) * parseFloat(discountValue));
                } else {
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

            var totalAfterDiscount = decimalupto2(subtotal - discount);
            totalAfterDiscount = (totalAfterDiscount < 0) ? 0 : totalAfterDiscount;

            var total = decimalupto2(totalAfterDiscount + cardProcessingAmount + total_tax_on_total + taxTotal);
            
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

        function recurringPayment() {
            var recurring = $('#recurring_payment').val();

            if (recurring == 'yes') {
                $('.recurringPayment').show().fadeIn(300);
            } else {
                $('.recurringPayment').hide().fadeOut(300);
            }
        }

</script>

<script>

        function decimalupto2(num) {
            var amt = Math.round(num * 100) / 100;
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
                    url: '{{ route('member.all-invoices.update-item') }}',
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
        

        $('.add-product').on('click', function (event) {
            event.preventDefault();
            var id = $(this).data('pk');
            var currencyId = $('#currency_id').val();
            var cal_from = 'invoice';
            $.easyAjax({
                url: '{{ route('member.all-invoices.update-item') }}',
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
            url:'{{ route('member.products.filter-products') }}',
            type: "GET",
            data: { project_id: projectId, salesCategory: salesCategory , locationCode: locationCode  , vendor_id: vendorId},
            success: function(response) {
                $('#produbcts_table_data').html(response.view);
                
            }
        });
    });
        
        var selectProject = document.getElementById('project_id');
        selectProject.onchange = function () {
            var option = $('option:selected', this).attr('client_id_attr');
            $("#client_company_id").val(option).change();
        };

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

