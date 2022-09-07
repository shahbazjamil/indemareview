<?php $data = $product->itemObj; ?>

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
@endpush

<!--{!! Form::open(['id'=>'tab-item','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="item">
<div class="form-body">
  <!-- <h3 class="box-title">@lang('app.menu.products') @lang('app.details')</h3>
  <hr> -->
  
   <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Vendor <a href="javascript:;" id="addVendor" class="btn btn-sm btn-outline btn-success">+ @lang('app.add') Vendor </a></label>
        <select name="vendor_id" id="vendor_id" class="form-control" required="">
          @foreach ($clientVendors as $clientVendor)
          <option value="{{$clientVendor->id}}" {{ $clientVendor->id == $product->vendor_id ? 'selected' : '' }}>{{ucfirst($clientVendor->vendor_name)}}</option>
          @endforeach
        </select>
      </div>	
	</div>
	<div class="col-md-6">	  
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.name')</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ $product->name }}" placeholder="@lang('app.product.item.name')" required>
      </div>
    </div>
    <div class="col-md-6">
        @if($product->id != 0 && $copy==0)
        <div id="file-upload-box" >
                <div class="row" id="file-dropzone">
                    <div class="col-md-12">
                        <div class="dropzone" id="file-upload-dropzone">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                        </div>
                    </div>
                </div>
            <div class="row">
            <div class="col-md-12">
              <div class="image-list">
              </div>
            </div>
        </div>
        </div>
        
        @endif
		
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Client-Facing Description </label>
        <textarea name="description" id="description" cols="30" rows="4" class="form-control" placeholder="@lang('app.product.item.descriptionPlaceholder')">{{$data->description}}</textarea>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Use a vendor-facing description on Purchase Orders?</label>
        <div class="radio-list">
          <label class="radio-inline p-0">
            <div class="radio radio-info">
                <input type="radio" name="vendor_description_type" onclick="fn_vendor_description('client')" id="vendor_description_client" value="client" {{$product->vendor_description == '' ? 'checked' : ''}}>
              <label for="vendor_description_client">Use the client-facing description for purchase orders.</label>
            </div>
          </label>
          <label class="radio-inline p-0">
            <div class="radio radio-info">
              <input type="radio" name="vendor_description_type" onclick="fn_vendor_description('vendor')" id="vendor_description_venodr" value="vendor" {{$product->vendor_description != '' ? 'checked' : ''}}>
              <label for="vendor_description_venodr">Add a vendor description</label>
            </div>
          </label>
        </div>
      </div>
    </div>
      
       
      
    <div id='vendor_description_wrp' class="col-md-12" @if($product->vendor_description == '') style="display: none" @endif >
      <div class="form-group">
        <label class="control-label">Description for purchase orders </label>
        <textarea name="vendor_description" id="vendor_description" cols="30" rows="4" class="form-control" placeholder="Description for purchase orders">{{$product->vendor_description}}</textarea>
      </div>
    </div>
      
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Notes</label>
        <textarea name="notes" id="notes" cols="30" rows="4" class="form-control" placeholder="">{{$product->notes}}</textarea>
      </div>
    </div>
   
    <div class="col-md-6">
      <div class="form-group">
        <label class="control-label">Link</label>
        <input type="url" id="link" name="link" class="form-control" value="{{ $product->link }}">
      </div>
    </div>
     
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Tags</label>
            <select multiple data-role="tagsinput" name="tags[]" id="tags">
                @if(!empty($product->tags))
                    @foreach($product->tags as $tag)
                        <option value="{{ $tag }}">{{ $tag }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>          
      
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.locationCode') :</label>
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
    </div>
      <div class="col-md-4">
        <div class="form-group">
        <label class="control-label">@lang('app.product.item.salesCategory') :</label>
        
        <select onchange="getSalesCategoryDetail(this)" name="salesCategory" id="salesCategory" class="form-control">
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
      </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">@lang('app.project') :</label>
        <select multiple="multiple" name="project_id[]" id="project_id" class="select2 select2-multiple">
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
          <option value="{{$project->id}}" >{{ucfirst($project->project_name)}}</option>
          <?php } ?>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  <div class="row">
    <!--<div class="col-md-2">
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.quantity') :</label>
        <input type="text" id="quantity" name="quantity" class="form-control" value="{{ $data->quantity }}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-3">
        
      
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.clientDeposit') :</label>
        <div class="input-group">
          <span class="input-group-addon"><b>%</b>
            <!-- <i class="fa fa-dollar"></i> 
          </span>
          <input type="text" id="clientDeposit" name="clientDeposit" class="form-control" value="{{ $data->clientDeposit }}" placeholder="@lang('app.typeHere')">
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.depositRequested') :</label>
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-dollar"></i>
          </span>
          <input type="text" id="depositRequested" name="depositRequested" value="{{ $data->depositRequested }}" class="form-control" placeholder="@lang('app.typeHere')">
        </div>
      </div>
    </div>-->	
    <div class="col-md-12">
      <div class="form-group">
        <label class="control-label">Product Number</label>
        <input type="text" id="product_number" name="product_number" class="form-control" value="{{ $product->product_number }}" placeholder="Product Number">
      </div>
    </div>
    
  </div>

  <div class="row">
    
      <!--<div class="col-md-4">
        <div class="form-group">
        <label class="control-label">@lang('app.product.item.units') :</label>
        <div class="row">
            <div class="col-md-6 m-b-2">
                <select name="select_unit" id="select_unit" class="form-control">
                    @foreach (Config::get('products.units') as $unit)
                     <?php $selected = isset($data->unit->{$unit}) ? 'selected' : ''; ?>
                    <option value="{{$unit}}" {{$selected}}>{{ucfirst($unit)}}</option>
                    @endforeach
                </select>
            </div>
          @foreach (Config::get('products.units') as $unit)
          <?php $value = isset($data->unit->{$unit}) ? $data->unit->{$unit} : ''; ?>
          <div class="col-md-6 m-b-2 wrp-unit" id="wrp-unit-{{$unit}}" style="display: none;">
            <input type="text" id="unit-{{$unit}}" name="unit[{{$unit}}]" class="form-control unit-val" value="{{ $value }}" placeholder="{{ucfirst($unit)}}">
          </div>
          @endforeach
        </div>
      </div>
      </div>
    <div class="col-md-2">
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.unitBudget') :</label>
        <input type="text" id="unitBudget" name="unitBudget" class="form-control" value="{{ $data->unitBudget }}" placeholder="@lang('app.typeHere')">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.totalEstimatedCost') :</label>
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-dollar"></i>
          </span>
          <input type="text" id="totalEstimatedCost" name="totalEstimatedCost" class="form-control" value="{{ $data->totalEstimatedCost }}" placeholder="@lang('app.typeHere')">
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">@lang('app.product.item.totalSalesPrice') :</label>
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-dollar"></i>
          </span>
          <input type="text" id="totalSalesPrice" name="totalSalesPrice" class="form-control" value="{{ $data->totalSalesPrice }}" placeholder="@lang('app.typeHere')">
        </div>
      </div>
    </div>-->
  </div>
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
<script>


$(document).ready(function () {
        Dropzone.autoDiscover = false;
        $("div#file-upload-dropzone").dropzone({
            url: "{{route('member.products.uploadImage', [$product->id])}}",
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
        $('#vendor_description').val('');
   } else {
       $('#vendor_description_wrp').show();
   }
}

function getSalesCategoryDetail(par){
            var code = par.value;
            var url = "{{ route('member.products.get-sales-category-detail',':id') }}";
            url = url.replace(':id', code);
            $.easyAjax({
                type: 'GET',
                url: url,
                async : false,
                success: function (response) {
                    var category = response.category;
                    $('#default_markup').val(category.salescategory_markup).change();
                    
                }
            });
        }
        
        
    $('#tab-item').on('click', '#addVendor', function () {
        var url = '{{ route('member.vendor.create-vendor')}}';
        $('#modelHeading').html('Add Vendor');
        $.ajaxModal('#purchaseOrderStatusModal', url);
    })

</script>

@endpush
