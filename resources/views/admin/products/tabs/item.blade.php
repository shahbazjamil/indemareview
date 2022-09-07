<?php $data = $product->itemObj; ?>

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

@endpush

<!--{!! Form::open(['id'=>'tab-item','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="item">
<input type="hidden" name="vendor_default_markup" id="vendor_default_markup" value="0">
<input type="hidden" name="salescategory_default_markup" id="salescategory_default_markup" value="0">

<div class="form-body">
  <!-- <h3 class="box-title">@lang('app.menu.products') @lang('app.details')</h3>
  <hr> -->
  <style>
    .datepicker {
          z-index: 999 !important;
      }
  .image-list{margin-bottom:15px;}
  .image-list .image-item{margin:0 5px;}
  .selected-img{padding: 15px;background: #f8f8f8;margin-bottom: 15px;}.selected-img img{max-width:90%;}
  .form-inner-heading{background:#474747;color:#FFF !important;padding:5px 15px}
  .form-group .d-flex{width:100%;}
  .form-group .d-flex>div{flex-grow:1}
  .form-group label{line-height:20px !important;}
  .form-group .btn{height:40px !important;line-height:40px !important;}
  .wa-new-changes .panel-tab-body > .panel-tab-item::before, .form-body ~ .panel-tab-item {display:none}
  </style>
                        <div class="form-body">
						<div class="row">
							<div class="col-md-8">								
								<div class="row">	
									<div class="col-md-12">                                     
										<h3 class="form-inner-heading">Product Information</h3>
									</div><!--end of col-6-->							
									<div class="col-md-6">                                     
									  <div class="form-group">
										<label class="control-label">@lang('app.product.item.name')</label>
										<input type="text" id="name" name="name" class="form-control" value="{{ $product->name }}" placeholder="@lang('app.product.item.name')" >
									  </div>
									</div><!--end of col-6-->
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Vendor  </label>											
											<div class="d-flex">	
											<div class="col m-r-10">
											<select name="vendor_id" id="vendor_id" class="form-control" onchange="getVendorDetail(this)" >
												<option value="">Select Vendor</option>
											  @foreach ($clientVendors as $clientVendor)
											  <option value="{{$clientVendor->id}}" {{ $clientVendor->id == $product->vendor_id ? 'selected' : '' }}>{{ucfirst($clientVendor->company_name)}}</option>
											  @endforeach
											</select>
											</div>
											<a href="javascript:;" id="addVendor" class="btn btn-sm btn-outline btn-success">+ @lang('app.add') Vendor </a>
											</div>
										</div>
									</div><!--end of col-->								
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">Client-Facing Description </label>
										<textarea name="description" id="description" cols="30" rows="4" class="form-control" placeholder="@lang('app.product.item.descriptionPlaceholder')">{{$data->description}}</textarea>
									  </div>
									</div><!--end of col-->
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Description for purchase orders </label>
											<textarea name="vendor_description" id="vendor_description" cols="30" rows="4" class="form-control" placeholder="Description for purchase orders">{{$product->vendor_description}}</textarea>
										</div>
									</div><!--end of col-6-->				
									<div class="col-md-3">                                     
									  <div class="form-group">
										<label for="spec_number" class="control-label">Spec #</label>
										<input type="text" id="spec_number" name="spec_number" class="form-control" value="{{ $product->spec_number }}" placeholder="" >
									  </div>
									</div><!--end of col-6-->			
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label  for="sku" class="control-label">Product # / Sku</label>
										<input type="text" id="sku" name="sku" class="form-control" value="{{ $product->sku }}" placeholder="" >
									  </div>
									</div><!--end of col-6-->		
									<div class="col-md-6">                                     
									  <div class="form-group">
										<label class="control-label">Location/Room</label>
											<div class="d-flex">	
											<div class="col m-r-10">
                                                                                            
                                                                                        <?php
                                                                                            $default_code_type_id = [];
                                                                                            if($product->codes) {
                                                                                                foreach ($product->codes as $code) {
                                                                                                    $default_code_type_id[] = $code->code_type_id;
                                                                                                }
                                                                                            }
                                                                                          ?>
                                                                                            
											<select name="code_type_id[]" id="code_type_id" multiple="multiple" class="select2 select2-multiple">
                                                                                          @forelse($codetypes as $codetype)
                                                                                            <?php if(in_array($codetype->id, $default_code_type_id)) { ?>
                                                                                                    <option value="{{ $codetype->id }}" selected>{{ucfirst($codetype->location_name)}}</option>
                                                                                            <?php } else { ?>
                                                                                                    <option value="{{ $codetype->id }}">{{ucfirst($codetype->location_name)}}</option>
                                                                                            <?php } ?>
                                                                                          @empty
                                                                                                <option value="">No Location Added</option>
                                                                                        @endforelse
											</select>
											</div>
											<a href="javascript:;" id="createLocationCode" class="btn btn-sm btn-outline btn-success">+ Add Location Code </a>
											</div>
									  </div>
                                                                            
									</div><!--end of col-6-->		
									<div class="col-md-6">                                     
									  <div class="form-group">
										<label class="control-label">URL</label>
										<input type="text" id="url" name="url" class="form-control" value="{{ $product->url }}" placeholder="" >
									  </div>
									</div><!--end of col-6-->	
									<div class="col-md-6">                                     
									  <div class="form-group">
										<label class="control-label">Select Category</label>
											<div class="d-flex">	
											<div class="col m-r-10">
											<select onchange="getSalesCategoryDetail(this)" name="salesCategory" id="salesCategory" class="form-control">
                                                                                            <option value="">Select Category</option>
                                                                                            @forelse($salescategories as $salescategory)
                                                                                                <option value="{{ $salescategory->salescategory_code }}" 
                                                                                                         @if($data->salesCategory == $salescategory->salescategory_code)
                                                                                                                                  selected
                                                                                                                                  @endif
                                                                                                        >{{ucfirst($salescategory->salescategory_name)}}</option>
                                                                                                @empty
                                                                                                  <option value="">No Category Added</option>
                                                                                            @endforelse
											</select>
											</div>
											<a href="javascript:;" id="createsalesCategory" class="btn btn-sm btn-outline btn-success">+ Add Sales Category </a>
											</div>
									  </div>
                                                                            
                                                                            
									</div><!--end of col-6-->	
									<div class="col-md-6">                                     
									  <div class="form-group">
                                                                            <label class="control-label">Product Tags</label>
                                                                            <select multiple data-role="tagsinput" name="tags[]" id="tags">
                                                                                @if(!empty($product->tags))
                                                                                    @foreach($product->tags as $tag)
                                                                                        <option value="{{ $tag }}">{{ $tag }}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
									  </div>
									</div><!--end of col-6-->	
                                                                        
                                                                        
                                                                        
									<div class="col-md-6">                                     
									  <div class="form-group">
										<label class="control-label">Project(s)</label>
                                                                                <select  multiple="multiple" name="project_id[]" id="project_id" class="select2 select2-multiple">
                                                                                        <option value="">Select Project</option>
                                                                                      <?php //$default_project_id = $product->project_id ? $product->project_id : 0; 

                                                                                      $default_project_id = [];
                                                                                      if($product->projects) {
                                                                                          foreach ($product->projects as $project) {
                                                                                              $default_project_id[] = $project->project_id;
                                                                                          }
                                                                                      }

                                                                                      ?>
                                                                                      @foreach ($projects as $project)
                                                                                      <?php if(in_array($project->id, $default_project_id)) { ?>
                                                                                      <option value="{{$project->id}}" selected >{{ucfirst($project->project_name)}}</option>
                                                                                      <?php } else { ?>
                                                                                            <option value="{{$project->id}}"   >{{ucfirst($project->project_name)}}</option>
                                                                                      <?php } ?>
                                                                                      @endforeach
                                                                                </select>
									  </div>
									</div><!--end of col-6-->	
                                                                        
   
                                                                        
									<div class="col-md-12">                                     
										<h3 class="form-inner-heading">Cost Details</h3>
									</div><!--end of col-6-->		
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="cost_per_unit" class="control-label">Per Unit Cost ({{ $global->currency->currency_symbol }})</label>
										<input type="text" id="cost_per_unit" name="cost_per_unit" class="form-control" value="{{$product->cost_per_unit ?$product->cost_per_unit :0}}" placeholder="">
									  </div>
									</div><!--end of col-6-->	
                                                                        
                                                                        
                                                                        
									<div class="col-md-3">                                     
									  <div class="form-group">
										<label class="control-label">QTY</label>
										<input type="number" id="quantity" name="quantity" class="form-control" value="{{ $product->quantity ? $product->quantity : 1 }}" >
									  </div>
									</div><!--end of col-6-->
                                                                        
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="default_markup_fix" class="control-label">Markup Flat Rate ({{ $global->currency->currency_symbol }})</label>
										<input type="text" id="default_markup_fix" name="default_markup_fix" class="form-control" value="{{$product->default_markup_fix ? $product->default_markup_fix: 0}}" placeholder="" >
									  </div>
									</div><!--end of col-6-->	
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="default_markup" class="control-label">Markup Percent (%)</label>
										<input type="text" id="default_markup" name="default_markup" class="form-control" value="{{$product->default_markup ? $product->default_markup: 0}}" placeholder="" >
									  </div>
									</div><!--end of col-6-->	
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="freight" class="control-label">Freight Cost</label>
										<input type="text" id="freight" name="freight" class="form-control" value="{{$product->freight ? $product->freight: 0}}" placeholder="" >
									  </div>
									</div><!--end of col-6-->	
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="msrp" class="control-label">MSRP</label>
                                                                                <input type="text" id="msrp" name="msrp" class="form-control" value="{{$product->msrp}}" placeholder="">
									  </div>
									</div><!--end of col-6-->
									<div class="col-md-6">                                     
									  <div class="form-group">
										<input id="addTax" name="addTax" value="true" type="checkbox" {{ $product->addTax ? 'checked' : '' }}> <label for="addTax" class="control-label">Use Default Tax</label>
									  </div>
                                                                            
                                                                            
									</div><!--end of col-6-->		
									<div class="col-md-12">                                     
										<h3 class="form-inner-heading">Addional Details</h3>
									</div><!--end of col-6-->	
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="dimensions" class="control-label">Dimensions</label>
										<input type="text" id="dimensions" name="dimensions" class="form-control" value="{{ $product->dimensions }}" >
									  </div>
									</div><!--end of col-6-->
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="materials" class="control-label">Material</label>
										<input type="text" id="materials" name="materials" class="form-control" value="{{ $product->materials }}" >
									  </div>
									</div><!--end of col-6-->
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="finish_color" class="control-label">Color</label>
										<input type="text" id="finish_color" name="finish_color" class="form-control" value="{{ $product->finish_color }}" >
									  </div>
									</div><!--end of col-6-->
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="product_color" class="control-label">Finish</label>
										<input type="text" id="product_color" name="product_color" class="form-control" value="{{ $product->product_color }}" >
									  </div>
									</div><!--end of col-6-->
                                                                        
                                                                       
                                                                        
									<div class="col-md-3">                                     
									  <div class="form-group">
                                                                              <label for="lead_time" class="control-label">Lead Time</label>
                                                                                <input type="text" id="lead_time" name="lead_time" class="form-control" value="{{ $product->lead_time }}" >
										
									  </div>
									</div><!--end of col-6-->
								</div><!--end of row-->
							</div><!--end of col-8-->
							<div class="col-md-4">								
							   <div class="row">
								<div class="col-md-12">
									@if($product->id != 0 && $copy==0)
									<div id="file-upload-box" >
										<div class="row">
										<div class="col-md-12">
										  <div class="selected-img">
<!--											<img src="https://stagin.indema.co/user-uploads/app-logo/1W53ukr6mDlFfpfBILsJhdEXPLPlKatrJYDkzx4p.jpeg" alt="home" class=" admin-logo">-->
										  </div>
										</div>
										<div class="col-md-12">
										  <div class="image-list">
										  </div>
										</div>
									</div>
											<div class="row" id="file-dropzone">
												<div class="col-md-12">
													<div class="dropzone" id="file-upload-dropzone">
														<div class="fallback">
															<input name="file" type="file" />
														</div>
													</div>
												</div>
											</div>
									</div>
									@endif									
								</div><!--end of col-12-->	
									<div class="col-md-12 m-t-20">                                     
										<h3 class="form-inner-heading">Attachments</h3>
									</div><!--end of col-6-->
									<div class="col-md-12">
										<div class="form-group">
											<div class="d-flex">	
												<div class="col m-r-10">
                                                                                                    <input type="file" id="attachment1" name="attachment1" class="form-control file"  >
												</div>
												
                                                                                                @if($product->attachment1)
                                                                                                    <a target="_blank" href="{{ asset('user-uploads/products/'.$product->id.'/'.$product->attachment1.'') }}" id="attachment1_select" class="btn btn-sm btn-outline btn-success">View</a>
                                                                                                 @endif
                                                                                               
                                                        
                                                                                                
											</div>
										</div>
										<div class="form-group">
											<div class="d-flex">	
												<div class="col m-r-10">
                                                                                                    <input type="file" id="attachment2" name="attachment2" class="form-control file" >
												</div>
												@if($product->attachment2)
                                                                                                    <a target="_blank" href="{{ asset('user-uploads/products/'.$product->id.'/'.$product->attachment2.'') }}" id="attachment1_select" class="btn btn-sm btn-outline btn-success">View</a>
                                                                                                 @endif
                                                                                                
											</div>
										</div>
										<div class="form-group">
											<div class="d-flex">	
												<div class="col m-r-10">
                                                                                                    <input type="file" id="attachment3" name="attachment3" class="form-control file" >
												</div>
												@if($product->attachment3)
                                                                                                    <a target="_blank" href="{{ asset('user-uploads/products/'.$product->id.'/'.$product->attachment3.'') }}" id="attachment1_select" class="btn btn-sm btn-outline btn-success">View</a>
                                                                                                 @endif
                                                                                                
											</div>
										</div>
									</div><!--end of col-12-->
							  </div><!--end of row-->
							</div>
						</div><!--end of row-->
  

  <hr />

  
</div>
<!--{!! Form::close() !!}-->


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

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
    
    $('.datepicker').datepicker({
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}'
    });


$(document).ready(function () {
        Dropzone.autoDiscover = false;
        $("div#file-upload-dropzone").dropzone({
            url: "{{route('admin.products.uploadImage', [$product->id])}}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "image",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*",
            //autoProcessQueue: false,
            uploadMultiple: false,
            addRemoveLinks:true,
            //parallelUploads:10,
            success: function (file, response) {
                $(".dz-remove").hide();
                //var imgName = response;
                //file.previewElement.classList.add("dz-success");
                toastr.success("@lang('app.product.picture.imageUploaded')");
                setImageList(JSON.parse(response));
                //console.log("Successfully uploaded :" + imgName);
                //
                //$(".dz-remove").html("");
            },
            error: function (file, response) {
                file.previewElement.classList.add("dz-error");
            }
        });
    });
    
     $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

function fn_vendor_description(val) {
   if(val == 'client') {
        $('#vendor_description_wrp').hide();
        //$('#vendor_description').val('');
   } else {
       $('#vendor_description_wrp').show();
   }
}


function getSalesCategoryDetail(par){
            var code = par.value;
            if(code != '') {
                var url = "{{ route('admin.products.get-sales-category-detail',':id') }}";
                url = url.replace(':id', code);
                $.easyAjax({
                    type: 'GET',
                    url: url,
                    async : false,
                    success: function (response) {
                        var category = response.category;
                        if($('#vendor_default_markup').val() == 0){
                            $('#salescategory_default_markup').val(category.salescategory_markup);
                            $('#default_markup').val(category.salescategory_markup).change();
                        } else if(category.salescategory_markup!=0) {
                            toastr.error("Vendor default markup already set.");
                        }
                    }
                });
            } else {
                $('#default_markup').val(0).change();
            }
        }
        function getVendorDetail(par){
            var vendorID = par.value;
            if(vendorID != '') {
                var url = "{{ route('admin.products.get-vendor-detail',':id') }}";
                url = url.replace(':id', vendorID);
                $.easyAjax({
                    type: 'GET',
                    url: url,
                    async : false,
                    success: function (response) {
                        var vendor = response.vendor;
                        if($('#salescategory_default_markup').val() == 0) {
                            $('#vendor_default_markup').val(vendor.vendor_markup);
                            $('#default_markup').val(vendor.vendor_markup).change();
                        } else if(vendor.vendor_markup!=0){
                            toastr.error("Sales Category default markup already set.");
                        }
                    }
                });
            } else {
                $('#default_markup').val(0).change();
            }
        }

</script>

<script>
        $('#createLocationCode').click(function () {
            var url = '{{ route('admin.codeTypes.create-type')}}';
            $('#modelHeadingS').html("Manag Location Codes");
            $.ajaxModal('#purchaseOrderStatusModal', url);
        })
</script>

<script>
        $('#createsalesCategory').click(function () {
            var url = '{{ route('admin.salescategoryTypes.create-category')}}';
            $('#modelHeadingS').html("Manag Sales Categories");
            $.ajaxModal('#purchaseOrderStatusModal', url);
        })
        
        
    $('#tab-item').on('click', '#addVendor', function () {
        var url = '{{ route('admin.vendor.create-vendor')}}';
        $('#modelHeading').html('Add Vendor');
        $.ajaxModal('#purchaseOrderStatusModal', url);
    })
        
</script>

@endpush
