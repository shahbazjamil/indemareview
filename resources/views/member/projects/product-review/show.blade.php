@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">Purchase Orders</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('member.projects.show_project_menu')
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-3 product-sidebar" id="issues-list-panel">
                                    
                                    <div class="category-box location-code loc-active" data-location-id="location-code">
                                            <a href="javascript:void(0)"><h3>All</h3></a>
                                            <div class="summary-area">
                                                    <span>
                                                            &nbsp;
                                                    </span>
                                                    <span>
                                                            &nbsp;
                                                    </span>
                                            </div>
                                    </div>
                                    
                                    @if($locationCodesData)
                                        @foreach ($locationCodesData as $key=>$locationCode)
                                            <div class="category-box location-code" data-location-id="{{ $key }}">
                                                <a href="javascript:void(0)"><h3>{{$locationCode['name']}}</h3></a>
                                                    <div class="summary-area">
                                                            <span>
                                                                     
                                                            </span>
                                                            <span>
                                                                <a href="javascript:void(0)" class="view-location-notes" data-code-type="{{$locationCode['id']}}"><img src="{{ asset('img/commets-icon.png') }}" alt=""> </a> {{$locationCode['total_notes']}}
                                                            </span>
                                                    </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="category-box location-code" data-location-id="uncategorized">
                                            <a href="javascript:void(0)"><h3>Uncategorized</h3></a>
                                            <div class="summary-area">
                                                    <span>
                                                            &nbsp; 
                                                    </span>
                                                    <span>
                                                        &nbsp;
                                                    </span>
                                            </div>
                                    </div>	
									
                                </div><!--end of product-sidebar-->
                                
                                    <div class="product-con-area col-md-9">
                                        
                                        
                                        <div class="w-100 product-review-f">
												<div class="row">
													<div class="col-md-6">
														<div>
																<label>Select Status</label>
																<select class="select-status" name="select_status" id="select_status">
																		<option value="all">All</option>
																		<option value="approved">Approved</option>
																		<option value="declined">Declined</option>
																</select>
																
														</div>
																<a href="javascript:void(0);" class="btn btn-outline btn-success btn-sm select-all-products"><i class="fa fa-check-circle"></i> Select / Unselect All</a>
																<a href="javascript:void(0);" class="btn btn-outline btn-success btn-sm update-product-lock" data-lock-flag="1"><i class="fa fa-unlock"></i> Unlock from Client Portal</a>
																<a href="javascript:void(0);" class="btn btn-outline btn-success btn-sm update-product-lock" data-lock-flag="0"><i class="fa fa-lock"></i> Lock from Client Portal</a>
																<a href="javascript:void(0);" class="btn btn-outline btn-success btn-sm create-finance" data-create-type="estimate" >+ Create Estimate of Approved</a>
																<a href="javascript:void(0);" class="btn btn-outline btn-success btn-sm create-finance" data-create-type="invoice" >+ Create Invoice of Approved</a>
													</div><!--end of col-6-->
													<div class="col-md-6 portal-area">
														<label>Show on Portal</label>
														<div>
                                                                                                                    <span><input @if($project->show_product_name == 1) checked="" @endif  type="checkbox" name="show_product_name" id="show_product_name" onclick="saveSettings('show_product_name')" /> Product Name</span>
                                                                                                                    <span><input @if($project->show_finish_color == 1) checked="" @endif type="checkbox" name="show_finish_color" id="show_finish_color" onclick="saveSettings('show_finish_color')" /> Finish/Color</span>
                                                                                                                    <span><input @if($project->show_url == 1) checked="" @endif type="checkbox" name="show_url" id="show_url" onclick="saveSettings('show_url')" /> URL</span>
                                                                                                                    <span><input @if($project->show_description == 1) checked="" @endif type="checkbox" name="show_description" id="show_description" onclick="saveSettings('show_description')" /> Description</span>
                                                                                                                    <span><input @if($project->show_dimensions == 1) checked="" @endif type="checkbox" name="show_dimensions" id="show_dimensions" onclick="saveSettings('show_dimensions')" /> Dimensions</span>
                                                                                                                    <span><input @if($project->show_sale_price == 1) checked="" @endif type="checkbox" name="show_sale_price" id="show_sale_price" onclick="saveSettings('show_sale_price')" /> Sale Price</span>
                                                                                                                    <span><input @if($project->show_material == 1) checked="" @endif type="checkbox" name="show_material" id="show_material" onclick="saveSettings('show_material')" /> Material</span>
                                                                                                                    <span><input @if($project->show_location == 1) checked="" @endif type="checkbox" name="show_location" id="show_location" onclick="saveSettings('show_location')" /> Location</span>
														</div>
													</div><!--end of col-6-->
												</div><!--end of row-->
                                            </div>
                                        
                                            
                                            <div class="row">
                                                
                                                @if($products)
                                                    @foreach ($products as $key=>$product)
                                                    
                                                <?php
                                                    $picture = 'img/default-product.png';
                                                    if(!empty($product->picture)) {
                                                        $pictures = json_decode($product->picture);
                                                        if(isset($pictures[0])) {
                                                            $picture = 'user-uploads/products/'.$product->id.'/'.$pictures[0];
                                                        }
                                                    }
                                                    
                                                    //$itemObj = json_decode($product->item);
                                                    $codes = $product->codes;
                                                    
                                                    $locationCodeCLS = 'uncategorized-cls';
                                                    if($codes) {
                                                        foreach ($codes as $code) {
                                                            
                                                            if($locationCodeCLS == 'uncategorized-cls') {
                                                                $locationCodeCLS = $code->code->location_code.'-cls';
                                                            } else {
                                                                $locationCodeCLS .= ' '.$code->code->location_code.'-cls';
                                                            }
                                                        }
                                                    }
                                                    
                                                    $approve_cls = 'declined-cls';
                                                    if($product->is_approved == 1){
                                                        $approve_cls = 'approved-cls';
                                                    }
                                                    
                                                    $total_notes = 0;
                                                    if($product->comments){
                                                        $total_notes = $product->comments->count();
                                                    }
                                                    
                                                ?>
                                                    
                                                        <div class="col-md-3 location-code-cls {{$locationCodeCLS}} {{$approve_cls}}">
                                                                <div class="category-box">
                                                                        <div>
                                                                            <a href="javascript:void(0)" ><img id="{{$product->id}}" data-product-id="{{ $product->id }}" src="{{ asset($picture) }}" class="img-responsive p-image" alt="{{$product->name}}"></a>
                                                                        </div>
                                                                        <div class="summary-area">
                                                                                <span>
                                                                                    <input name="product_id" value="{{$product->id}}" type="checkbox" />
                                                                                    @if($product->is_locked == 1)
                                                                                    <a href="javascript:void(0)" class="unlock-clicked" id="unlock-clicked-{{$product->id}}" data-pp-id="{{$product->id}}"><i id="p-lock-{{$product->id}}" class="fa fa-unlock"></i></a>
                                                                                    <a style="display: none;" href="javascript:void(0)" class="lock-clicked" id="lock-clicked-{{$product->id}}" data-pp-id="{{$product->id}}"><i id="p-lock-{{$product->id}}" class="fa fa-lock"></i></a>
                                                                                    @else
                                                                                    <a style="display: none" href="javascript:void(0)" class="unlock-clicked" id="unlock-clicked-{{$product->id}}" data-pp-id="{{$product->id}}"><i id="p-lock-{{$product->id}}" class="fa fa-unlock"></i></a>
                                                                                        <a href="javascript:void(0)" class="lock-clicked" id="lock-clicked-{{$product->id}}" data-pp-id="{{$product->id}}"><i id="p-lock-{{$product->id}}" class="fa fa-lock"></i></a>
                                                                                    @endif
                                                                                </span>
                                                                                <span>
                                                                                    @if($product->is_approved == 1)
                                                                                    <i class="fa fa-check-circle"></i>
                                                                                    @else
                                                                                    <i class="fa fa-times-circle"></i>
                                                                                    @endif
                                                                                        
                                                                                    
                                                                                    <a href="javascript:void(0)" class="view-product-notes" data-npp-id="{{$product->id}}"><img src="{{ asset('img/commets-icon.png') }}" alt=""> </a> {{$total_notes}}
                                                                                </span>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                    
                                                    @endforeach
                                                @endif
                                                
                                                    
                                                    
                                                   
                                                    
                                            </div>
                                    </div>
                            </div>
                        </section>

                    </div>
                </div>
            </section>
        </div>


    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    
    <div class="modal fade bs-modal-md in" id="myModalImagePreview" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    
    <div class="modal fade bs-modal-md in" id="addNotesModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
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
    
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script>
    var token = "{{ csrf_token() }}";
    
    $('.location-code').click(function () {
        
        $('.location-code').removeClass("loc-active");
        $(this).addClass("loc-active");
        
        var location_id = $(this).data('location-id');
        $('.location-code-cls').hide();
        $('.'+location_id+'-cls').show();
        $("#select_status").val('all');
        $("input[name='product_id']:checkbox").prop('checked',false);
        
    });
    
    $("#select_status").change(function(){
        var status_val = $("#select_status").val();
        
        if(status_val == 'approved'){
            $('.approved-cls').show();
            $('.declined-cls').hide();
        } else if(status_val == 'declined') {
            $('.declined-cls').show();
            $('.approved-cls').hide();
        } else {
            $('.declined-cls').show();
            $('.approved-cls').show();
        }
        
        $("input[name='product_id']:checkbox").prop('checked',false);
    });
    
    $('.select-all-products').click(function () {
        var checkBoxes = $("input[name='product_id']");
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
        //$("input[name='product_id']:checkbox").prop('checked',true);
    });
    
    $('body').on('click', '.p-image', function () {
            var projectID = '<?php echo $project->id; ?>';
            var id = $(this).data('product-id');
            var url = "{{ route('member.product-review-project.detail',[':id',':pid']) }}";
            url = url.replace(':id', id);
            url = url.replace(':pid', projectID);
            $.ajaxModal('#myModalImagePreview', url);
            
    });
     
     
    $('.lock-clicked').click(function () {
        console.log('1');
        var is_locked = 1;
        var pp_id = $(this).data('pp-id');
        var productIDS = [];
        productIDS.push(pp_id);
        updateStatus(productIDS, is_locked)
    });
    
    $('.unlock-clicked').click(function () {
        console.log('2');
        var is_locked = 0;
        var pp_id = $(this).data('pp-id');
        var productIDS = [];
        productIDS.push(pp_id);
        updateStatus(productIDS, is_locked)
        
    });
     
     
     $('.update-product-lock').click(function () {
         var is_locked = $(this).data('lock-flag');
         var productIDS = [];
                 
        $('input[name="product_id"]:checked').each(function() {
           productIDS.push(this.value);
        });
        
        updateStatus(productIDS,is_locked);
         
        
    });
    
     function saveSettings(field_name){
        var field_val = 0;
        if($('#' + field_name).is(":checked")) {
            field_val = 1;
        }
         var projectID = '<?php echo $project->id; ?>';
         $.easyAjax({
            url: '{{route('member.product-review-project.updateSetting', '1')}}',
            type: "POST",
            data: {'_token': token,'projectID': projectID, 'field_name' : field_name, 'field_val' : field_val},
            success: function(response){
                if(response.status == 'success'){
                }
            }
        })
    }
    
    function updateStatus(productIDS, is_locked){
        
        $.easyAjax({
            url: '{{route('member.product-review-project.update', '1')}}',
            type: "PUT",
            data: {'_token': token,'productIDS': productIDS, 'is_locked' : is_locked},
            success: function(response){
                if(response.status == 'success'){
                    var res =  response.productIDS
                    res.forEach(function(id) {
                        if(response.is_locked == 1) {
                            $('#unlock-clicked-'+id).show();
                            $('#lock-clicked-'+id).hide();
                           //$('#p-lock-'+id).addClass('fa-unlock').removeClass('fa-lock');
                           //$('#p-lock-'+id).parent().addClass('unlock-clicked').removeClass('lock-clicked');
                        } else {
                            $('#unlock-clicked-'+id).hide();
                            $('#lock-clicked-'+id).show();
                           //$('#p-lock-'+id).addClass('fa-lock').removeClass('fa-unlock');
                           //$('#p-lock-'+id).parent().addClass('lock-clicked').removeClass('unlock-clicked');
                        }
                    });
                }
            }
        })
        
    }
    
    
    $('.create-finance').click(function () {
        
        var create_type = $(this).data('create-type');
        var productIDS = [];
        var projectID = '<?php echo $project->id; ?>';
         
        $('input[name="product_id"]:checked').each(function() {
           productIDS.push(this.value);
        });
        
        $.easyAjax({
            url: '{{route('member.product-review-project.create-finances')}}',
            type: "POST",
            redirect: true,
            data: {'_token': token,'productIDS': productIDS, 'project_id': projectID, 'create_type': create_type},
            success: function(response){
                if(response.status == 'success'){
                    
                }
            }
        })
         
        
    });
    
    $('body').on('click', '.view-product-notes', function () {
        var npp_id = $(this).data('npp-id');
        var url = "{{ route('member.product-review-project.view-product-notes',':id') }}";
        url = url.replace(':id', npp_id);
        
        $('#modelHeading').html('Product Notes');
        $.ajaxModal('#addNotesModal', url);

    });
    
     $('body').on('click', '.view-location-notes', function () {
        var code_type = $(this).data('code-type');
        var url = "{{ route('member.product-review-project.view-location-notes',':id') }}";
        url = url.replace(':id', code_type);
        
        $('#modelHeading').html('Location Notes');
        $.ajaxModal('#addNotesModal', url);

    });


  
    $('ul.showProjectTabs .projectProductReview').addClass('tab-current');
    
    
</script>
@endpush
