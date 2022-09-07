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
            <li><a href="{{ route('admin.invoices.index') }}">{{ __($pageTitle) }}</a></li>
            <li class="active">@lang('app.addNew')</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
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

<div class="row wa-new-changes">
    <div class="col-md-12">

        <div class="panel panel-inverse">
            <div class="panel-heading p-b-0 p-b-20"> @lang('modules.invoices.addInvoice')</div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body p-0">
                    {!! Form::open(['id'=>'storePayments','class'=>'ajax-form','method'=>'POST']) !!}
                    <div class="form-body panel-tab-body">
					<div class="panel-tab-item" tab="Vendor">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label"> Purchase Order #</label>
                                    
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="invoicePrefix"
                                                    data-prefix="PO">PO</span>#<span
                                                    class="noOfZero"
                                                    data-zero="{{ $invoiceSetting->invoice_digit }}">{{ $zero }}</span>
                                            </div>
                                            <input type="text" class="form-control readonly-background"
                                                name="purchase_order_number" id="purchase_order_number"
                                                value="@if(is_null($lastInvoice))1 @else{{ ($lastInvoice) }}@endif">
                                        </div>
                                    
                                </div>
                            </div>
                        </div>
                        
<!--                        Vendor-->
                        
                        <div class="row">
                            <div class="col-md-6">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label class="control-label">Vendor</label>
											<div class="row">
												<div class="col-md-12">
                                                                                                    <select onchange="getVendorDetail(this)" class="select2 form-control" data-placeholder="Choose Vendor"
														id="vendor_id" name="vendor_id">
														<option value="">--</option>
														@foreach($vendors as $vendor)
														<option  value="{{ $vendor->id }}">{{ ucwords($vendor->vendor_name) }} - {{ ucwords($vendor->company_name) }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Contact</label>
											<div class="input-icon">
												<input type="text" class="form-control" name="contact" id="contact" value="">
											</div>
										</div>
									</div>	
									<div class="col-md-6">								
										<div class="form-group">
											<label class="control-label">Company</label>
											<div class="input-icon">
												<input type="text" class="form-control" name="company" id="company" value="">
											</div>
										</div>
									</div>
								</div>
                            </div>                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <div class="input-icon">
                                        <textarea name="address" id="address" class="form-control"></textarea>
                                        
                                    </div>
                                </div>
                            </div>
							<div class="col-md-3">			
										<div class="form-group">
											<label class="control-label">Email</label>
											<div class="input-icon">
												<input type="email" class="form-control" name="email" id="email" value="">
											</div>
										</div>
										<div class="form-group">
											<label class="control-label">Account #</label>
											<div class="input-icon">
												<input type="text" class="form-control" name="account_no" id="account_no" value="">
											</div>
										</div>
							</div>
                            <div class="col-md-4" style="display:none;">
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
                        </div>
<!--                        Purchase Order Details-->
                        
						<div class="panel-tab-item" tab="Purchase Order Details">
							<div class="row">								
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label">Shipping Address</label>
										<div class="input-icon">
											<textarea name="shipping_address" id="shipping_address" class="form-control"></textarea>
											
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Purchase Order Date</label>
										<div class="row">
											<div class="col-md-12">
												<div class="input-icon">
													<input type="text" class="form-control" name="purchase_order_date"
														id="purchase_order_date"
														value="{{ Carbon\Carbon::today()->format($global->date_format) }}">
												</div>
											</div>
										</div>
									</div>
								</div>


								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Terms</label>
										<select class="form-control" name="terms" id="terms">
											<option value="due_on_receipt" >Due on receipt</option>
											<option value="net_15" >Net 15</option>
											<option value="net_30" >Net 30</option>
											<option value="net_60" >Net 60</option>
											<option value="custom" >Custom</option>
										</select>
									</div>
								</div>
                                                                <div class="col-md-2">
                                                                  <div class="form-group">
                                                                    <label class="control-label">@lang('app.project') :</label>
                                                                    <select name="project_id" id="project_id" class="form-control">
                                                                      @foreach ($projects as $project)
                                                                      <option value="{{$project->id}}" {{ $project->id == $default_project_id ? 'selected' : ''}}  >{{ucfirst($project->project_name)}}</option>
                                                                      @endforeach
                                                                    </select>
                                                                  </div>
                                                                </div>
                                                            <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>@lang('app.status')
                                                                    <a href="javascript:;" id="createPurchaseOrderStatus" class="btn btn-xs btn-outline btn-success"><i class="fa fa-plus"></i> @lang('modules.tasks.addStatus')</a>
                                                                </label>
                                                                <select name="status" id="status" class="form-control">
                                                                    @forelse($status as $sts)
                                                                        <option value="{{ $sts->id }}"> {{ ucfirst($sts->type) }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                        </div>

							</div>
                        </div>                     
                        <div class="panel-tab-item" tab="Items">

							<div class="row">
								
								<div class="col-md-12 border-bottom">
									<div class="btn-group m-b-10">
										<button aria-expanded="false" data-toggle="dropdown"
											class="btn btn-info dropdown-toggle waves-effect waves-light"
											type="button" onclick="$('#produbctsModal').show();">+ @lang('app.menu.addProducts')</span>
										</button>
										<ul style="display:none;" role="menu" class="dropdown-menu dropdown-content">
											@foreach($products as $product)
											<li class="m-b-10 product-all-items product-vendor-items-{{$product->vendor_id}}">
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
								</div>
                                                            <div class="col-xs-12  visible-md visible-lg d-flex-border">

									<div class="col-md-4 font-bold" style="padding: 8px 15px">
										@lang('modules.invoices.item')
									</div>

									<div class="col-md-1 font-bold" style="padding: 8px 15px">
										@lang('modules.invoices.qty')
									</div>

									<div class="col-md-2 font-bold" style="padding: 8px 15px">
										@lang('modules.invoices.unitPrice')
									</div>

									<div class="col-md-2 font-bold" style="padding: 8px 15px">
										@lang('modules.invoices.tax') <a href="javascript:;" id="tax-settings"><i
												class="ti-settings text-info"></i></a>
									</div>

									<div class="col-md-2 text-center font-bold" style="padding: 8px 15px">
										@lang('modules.invoices.amount')
									</div>

									<div class="col-md-1" style="padding: 8px 15px">
										&nbsp;
									</div>

								</div>


								<div id="sortable" class="col-md-12">
<!--									<div class="col-xs-12 item-row margin-top-5 d-flex-border">

										<div class="col-md-4">
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

											<div class="form-group">
												<label
													class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>
												<input type="number" min="1" class="form-control quantity" value="1"
													name="quantity[]">
											</div>


										</div>

										<div class="col-md-2">
											<div class="row">
												<div class="form-group">
													<label
														class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>
													<input type="text" class="form-control cost_per_item"
														name="cost_per_item[]" value="0">
												</div>
											</div>

										</div>

										<div class="col-md-2">

											<div class="form-group">
												<label
													class="control-label hidden-md hidden-lg">@lang('modules.invoices.type')</label>
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

										<div class="col-md-2 border-dark  text-center">
											<label
												class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>

											<p class="form-control-static"><span class="amount-html">0.00</span></p>
											<input type="hidden" class="amount" name="amount[]" value="0">
										</div>

										<div class="col-md-1 text-right visible-md visible-lg">
											<button type="button" class="btn remove-item btn-circle btn-danger"><i
													class="fa fa-remove"></i></button>
										</div>
										<div class="col-md-1 hidden-md hidden-lg">
											<div class="row">
												<button type="button" class="btn btn-circle remove-item btn-danger"><i
														class="fa fa-remove"></i> @lang('app.remove')</button>
											</div>
										</div>

									</div>-->
								</div>


                                                            <div style="display : none;" class="col-xs-12 m-t-5">
									<button type="button" class="btn btn-info" id="add-item"><i class="fa fa-plus"></i>
										@lang('modules.invoices.addItem')</button>
								</div>
								<div class="col-xs-8 m-t-20">
									<div class="form-group">
													<label class="control-label">Tags</label>
													<select multiple data-role="tagsinput" name="document_tags[]" id="document_tags">
													</select>
									</div>
									<div class="form-group">
										<label class="control-label">Memo to be displayed on the purchase order</label>
										<textarea class="form-control" name="memo_order" id="memo_order" rows="5"></textarea>
									</div>
								</div>
								<div class="col-xs-4">
									<div class="row">
										<div class="col-md-12 text-right p-t-10">
											@lang('modules.invoices.subTotal') 
											<p class="form-control-static inline-p">
												<span class="sub-total">0.00</span>
											</p>
										</div>
										<input type="hidden" class="sub-total-field" name="product_subtotal" value="0">
									</div>
									<div class="row">
										<div class="col-md-4 text-right p-t-10">
											@lang('modules.invoices.discount')
										</div>
										<div class="form-group col-md-4">
											<input type="number" min="0" value="0" name="discount_value"
												class="form-control discount_value">
										</div>
										<div class="form-group col-md-4">
											<select class="form-control" name="discount_type" id="discount_type">
												<option value="percent">%</option>
												<option value="fixed">@lang('modules.invoices.amount')</option>
											</select>
										</div>
									</div>
                                                                    
                                                                        <div class="row">
										<div class="col-md-4 text-right p-t-10">
											Freight
										</div>
										<div class="form-group col-md-4">
											<input type="number" min="0" value="0" name="freight_value"
												class="form-control freight_value">
										</div>
										<div class="form-group col-md-4">
											<select class="form-control" name="freight_type" id="freight_type">
												<option value="percent">%</option>
												<option value="fixed">@lang('modules.invoices.amount')</option>
											</select>
										</div>
									</div>

									<div class="row m-t-5" id="invoice-taxes">
										<div class="col-md-12 text-right p-t-10">
											@lang('modules.invoices.tax')
											<p class="form-control-static inline-p">
												<span class="tax-percent">0.00</span>
											</p>
										</div>

										
									</div>

									<div class="row m-t-5 font-bold">
										<div class="col-md-12 text-right p-t-10">
											@lang('modules.invoices.total')
											<p class="form-control-static inline-p">
												<span class="total">0.00</span>
											</p>
										</div>


										<input type="hidden" class="total-field" name="total_amount" value="0">
									</div>

								</div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label">Specifications File</label>
                                                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                                            <div class="form-control" data-trigger="fileinput"> <i
                                                                                    class="glyphicon glyphicon-file fileinput-exists"></i> <span
                                                                                    class="fileinput-filename"></span></div>
                                                                            <span class="input-group-addon btn btn-default btn-file"> <span
                                                                                    class="fileinput-new">@lang('app.selectFile')</span> <span
                                                                                    class="fileinput-exists">@lang('app.change')</span>
                                                                                <input type="file" name="specification_file" id="specification_file">
                                                                            </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists"
                                                                                data-dismiss="fileinput">@lang('app.remove')</a>
                                                                        </div>
                                                                    </div>
                                                                </div>

							</div>
                        </div>
                        

                    </div>
                    <div class="form-actions" style="margin-top: 70px">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="save-form" class="btn btn-success save-form"><i
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

{{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="purchaseOrderStatusModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingS"></span>
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
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>

<script>
    
     $(function() {
         $('.product-all-items').hide();
     });
     
    function getVendorDetail(par){
            var vendorId = par.value;
            $('.product-all-items').hide();
            $('.product-vendor-items-'+vendorId).show();
            
            var url = "{{ route('admin.purchase-orders.get-vendor-detail',':id') }}";
            url = url.replace(':id', vendorId);
            $.easyAjax({
                type: 'GET',
                url: url,
                async : false,
                success: function (response) {
                    var vendor = response.vendor;
                    $('#contact').val(vendor.vendor_mobile);
                    $('#company').val(vendor.company_name);
                    $('#address').val(vendor.company_address);
                    $('#email').val(vendor.vendor_email);
                    //$('#account_no').val(vendor.); vendor have not account no.
                    $('#shipping_address').val(vendor.vendor_shipping_address);
                }
            });
        }
      

        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());
        });

        $(function () {
            $("#sortable").sortable();
        });

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        jQuery('#purchase_order_date').datepicker({
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

            $.easyAjax({
                url: '{{route('admin.purchase-orders.store')}}',
                container: '#storePayments',
                type: "POST",
                redirect: true,
                file: (document.getElementById("specification_file").files.length == 0) ? false : true,
                data: $('#storePayments').serialize()



            })
        });

        $('#add-item').click(function () {
            var i = $(document).find('.item_name').length;
            var item = '<div class="col-xs-12 item-row margin-top-5 d-flex-border">'

                + '<div class="col-md-4">'
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

                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.qty')</label>'
                + '<input type="number" min="1" class="form-control quantity" value="1" name="quantity[]" >'
                + '</div>'


                + '</div>'

                + '<div class="col-md-2">'
                + '<div class="row">'
                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.unitPrice')</label>'
                + '<input type="text" min="0" class="form-control cost_per_item" value="0" name="cost_per_item[]">'
                + '</div>'
                + '</div>'

                + '</div>'


                + '<div class="col-md-2">'

                + '<div class="form-group">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.tax')</label>'
                + '<select id="multiselect' + i + '" name="taxes[' + i + '][]"  multiple="multiple" class="selectpicker form-control type">'
                    @foreach($taxes as $tax)
                + '<option data-rate="{{ $tax->rate_percent }}" value="{{ $tax->id }}">{{ $tax->tax_name.': '.$tax->rate_percent }}%</option>'
                    @endforeach
                + '</select>'
                + '</div>'


                + '</div>'

                + '<div class="col-md-2 text-center">'
                + '<label class="control-label hidden-md hidden-lg">@lang('modules.invoices.amount')</label>'
                + '<p class="form-control-static"><span class="amount-html">0.00</span></p>'
                + '<input type="hidden" class="amount" name="amount[]">'
                + '</div>'

                + '<div class="col-md-1 text-right visible-md visible-lg">'
                + '<button type="button" class="btn remove-item btn-circle btn-danger"><i class="fa fa-remove"></i></button>'
                + '</div>'

                + '<div class="col-md-1 hidden-md hidden-lg">'
                + '<div class="row">'
                + '<button type="button" class="btn remove-item btn-danger"><i class="fa fa-remove"></i> @lang('app.remove')</button>'
                + '</div>'
                + '</div>'

                + '</div>';

            $(item).hide().appendTo("#sortable").fadeIn(500);
            $('#multiselect' + i).selectpicker();
        });

        $('#storePayments').on('click', '.remove-item', function () {
            $(this).closest('.item-row').fadeOut(300, function () {
                $(this).remove();
                $('.item-row').each(function (index) {
                    $(this).find('.selectpicker').attr('name', 'taxes[' + index + '][]');
                    $(this).find('.selectpicker').attr('id', 'multiselect' + index);
                });
                calculateTotal();
            });
        });

        $('#storePayments').on('keyup change', '.quantity,.cost_per_item,.item_name, .discount_value, .freight_value', function () {
            var quantity = $(this).closest('.item-row').find('.quantity').val();

            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();

            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount).toFixed(2));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount).toFixed(2));

            calculateTotal();


        });

        $('#storePayments').on('change', '.type, #discount_type, #freight_type', function () {
            var quantity = $(this).closest('.item-row').find('.quantity').val();

            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();

            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount).toFixed(2));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount).toFixed(2));

            calculateTotal();


        });

        function calculateTotal() {
            var subtotal = 0;
            var discount = 0;
            var freight = 0;
            var tax = '';
            var taxList = new Object();
            var taxTotal = 0;
            
            var discountType = $('#discount_type').val();
            var discountValue = $('.discount_value').val();
            
            var freightType = $('#freight_type').val();
            var freightValue = $('.freight_value').val();

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
                if (discountType == 'percent' && discountValue != '') {
                    discountedAmount = parseFloat(amount - ((parseFloat(amount) / 100) * parseFloat(discountValue)));
                }

                if (isNaN(amount)) {
                    amount = 0;
                }

                subtotal = (parseFloat(subtotal) + parseFloat(amount)).toFixed(2);

                if (itemTaxId != '') {
                    for (var i = 0; i <= itemTaxName.length; i++) {
                        if (typeof (taxList[itemTaxName[i]]) === 'undefined') {
                            if (discountedAmount > 0) {
                                taxList[itemTaxName[i]] = ((parseFloat(itemTax[i]) / 100) * parseFloat(discountedAmount));
                            } else {
                                taxList[itemTaxName[i]] = ((parseFloat(itemTax[i]) / 100) * parseFloat(amount));
                            }
                        } else {
                            if (discountedAmount > 0) {
                                taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i]) / 100) * parseFloat(discountedAmount));
                                console.log(taxList[itemTaxName[i]]);

                            } else {
                                taxList[itemTaxName[i]] = parseFloat(taxList[itemTaxName[i]]) + ((parseFloat(itemTax[i]) / 100) * parseFloat(amount));
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
            
            if (freightValue != '') {
                if (freightType == 'percent') {
                    freight = ((parseFloat(subtotal) / 100) * parseFloat(freightValue));
                } else {
                    freight = parseFloat(freightValue);
                }

            }

            $('#invoice-taxes').html(tax);

            var totalAfterDiscount = decimalupto2(subtotal - discount);

            totalAfterDiscount = (totalAfterDiscount < 0) ? 0 : totalAfterDiscount;

            var total = decimalupto2(totalAfterDiscount + taxTotal + freight);

            $('.total').html(total.toFixed(2));
            $('.total-field').val(total.toFixed(2));

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
    $('#tax-settings').click(function () {
            var url = '{{ route('admin.taxes.create')}}';
            $('#modelHeading').html('Manage Project Category');
            $.ajaxModal('#taxModal', url);
        });

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
                    url: '{{ route('admin.purchase-orders.update-item') }}',
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
            
            $('input[name="select_product"]').each(function() {
			this.checked = false;
            });
            $('#produbctsModal').hide()
        });

        $('.add-product').on('click', function (event) {
            event.preventDefault();
            var id = $(this).data('pk');
            var currencyId = $('#currency_id').val();
            $.easyAjax({
                url: '{{ route('admin.purchase-orders.update-item') }}',
                type: "GET",
                data: {id: id, currencyId: currencyId},
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
            url:'{{ route('admin.products.filter-products') }}',
            type: "GET",
            data: { project_id: projectId, salesCategory: salesCategory , locationCode: locationCode  , vendor_id: vendorId},
            success: function(response) {
                $('#produbcts_table_data').html(response.view);
                
            }
        });
    });
   
        
//        var selectProject = document.getElementById('project_id');
//        selectProject.onchange = function () {
//            var option = $('option:selected', this).attr('client_id_attr');
//            $("#client_company_id").val(option).change();
//        };
        
</script>

<script>
        $('#createPurchaseOrderStatus').click(function () {
            var url = '{{ route('admin.purchase-order-settings.create-status')}}';
            $('#modelHeadingS').html("Manag Purchase Order Status");
            $.ajaxModal('#purchaseOrderStatusModal', url);
        })
</script>
@endpush