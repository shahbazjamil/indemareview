@extends('layouts.app')

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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.products')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <style>
        .datepicker {
            z-index: 999 !important;
        }
    </style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

<style>
			div#view-details{max-height: 1200px; overflow-y: auto;}
				.d-i-new .d-flex.border{
					display: flex;
					border: 1px solid #e3e3e3;
					border-radius: 5px;
					padding: 5px;
					font-size: 11px;
					color: #666;
					margin-bottom: 10px;width:100%;box-shadow: 0 0px 10px #ddd;
				}
				.d-i-new .d-flex.border>div + div{border-left:1px solid #eee;align-content:flex-start}
				.col{flex-basis:0;flex-grow:1;max-width:100%;padding:0 15px}
				.col-auto{flex:0 0 auto;max-width:100%;padding:0 15px}
				.d-i-new b, .d-i-new input, .d-i-new Select{
					display: block;
					font-weight: 400;
					font-size: 14px;
					color: #000;
					box-shadow: none !important;
					white-space: nowrap;
					max-width: 100%;
					border: none !important;
					padding: 0 !important;
					text-overflow: ellipsis;
					overflow: hidden;
				}
				.d-i-new .col-auto.status select{color:#D04E55;border:1px solid #D04E55;border-radius:7px}
                                
				.d-i-new .status.approved{color:#37EA7C;text-transform:uppercase;font-size:16px}
                                .d-i-new .status.pending{color:#F7CB73;text-transform:uppercase;font-size:16px}
                                .d-i-new .status.declined{color:#D9512C;text-transform:uppercase;font-size:16px}
                                
				.d-i-new b.notes{
					white-space:normal;word-break: break-all;
				}
				.col-auto>div, .col>div{margin:5px 0}
				.col-auto.img img {
					margin: 15px 0;
				}
				.col-auto.name {
					width: 250px;
				}
				.col-auto.price {
					display: flex;
					flex-wrap: wrap;
					width: 280px;
				}
				.col-auto.price>div {
					width: 33.33%;
				}
				.col-auto.date {
					display: flex;
					flex-wrap: wrap;
					width: 332px;align-content: flex-start;
				}
				.col-auto.other {
					display: flex;
					flex-wrap: wrap;
					width: 472px;align-content: flex-start;
				}
				.col-auto.date>div {
					width: 100px;
					flex: 0 0 100px;
					overflow: hidden;
				}
				.col-auto.other>div {
					width: 110px;
					flex: 0 0 110px;
					overflow: hidden;
				}
				.col-auto.received input {
					width: 100px;
				}
				.col-auto.status {
					align-self:center;border:none
				}
				.d-i-new>h3{font-size:18px;text-transform:uppercase;margin:20px 0 0;}
				.d-i-new h3 span{text-transform:capitalize}
				.d-i-new .last-action{
					display: flex;
					flex-direction: column;
					justify-content: space-between;
				}
				.filter-section[style="display: block;"] + div .col-auto.date{
					width: 231px;
				}
				.filter-section[style="display: block;"] + div .col-auto.price>div:nth-child(odd) {width: 90px;}
				.filter-section[style="display: block;"] + div .col-auto.price>div:nth-child(even) {width: 70px;}
				.filter-section[style="display: block;"] + div .col-auto.price{max-width: 191px}
				@media screen and (max-width:1750px){
					.col-auto.date{width: 232px}
					.filter-section[style="display: block;"] + div .d-i-new .d-flex.border{flex-wrap:wrap;}
					.filter-section[style="display: block;"] + div .col-auto.name,.filter-section[style="display: block;"] + div .col-auto.price {
						max-width: 100%;
						width: calc(50% - 53px);
					}
					.filter-section[style="display: block;"] + div .col-auto.date{
						max-width: 100%;
						width: calc(50% - 79px);border-left:0 !important;
					}
					.filter-section[style="display: block;"] + div .last-action{width:100%;flex-direction:row !important;border-left:0 !important;border-top:1px solid #eee;align-items:center}
				}
				
				@media screen and (max-width:1650px){
					.col-auto.price{align-content: flex-start;max-width: 192px}
					.col-auto.price>div:nth-child(odd) {width: 90px;}
					.col-auto.price>div:nth-child(even) {width: 70px;}
				}
				@media screen and (max-width:1470px){
					.d-i-new .d-flex.border{flex-wrap:wrap;}
					.col-auto.name,.col-auto.price {
						max-width: 100%;
						width: calc(50% - 53px);
					}
					.col-auto.date{
						max-width: 100%;
						width: calc(50% - 79px);border-left:0 !important;
					}
					.last-action{width:100%;flex-direction:row !important;border-left:0 !important;border-top:1px solid #eee;align-items:center}
				}
				@media screen and (max-width:991px){
					.col-auto.date{width: calc(100% - 131px);}
					.col.note{width:100%;flex:0 0 100%;}
				}
				@media screen and (max-width:767px){
					.d-i-new .d-flex.border > div{width: 100%;
					flex: 0 0 100%;
					border-left: 0;
					border-top: 1px solid #eee;}
				}
			</style>


@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('admin.projects.show_project_menu')
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="issues-list-panel">
                                    <div class="white-box p-0">
                                         
                                        <h2 class="border-bottom">@lang('app.menu.products')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12 border-bottom p-b-10">
                                                <a href="{{ route('admin.products.edit', 0) }}" class="btn btn-success btn-outline"><i class="fa fa-flag"></i> @lang('app.addNew') @lang('app.menu.products')</a>
                                            </div>
                                        </div>
                                        
                                        @section('filter-section')
                                        <div class="row" id="ticket-filters">

                                            <form action="" id="filter-form">

                                                <div class="col-md-12">
                                                    <h5 >@lang('app.product.item.locationCode')</h5>
                                                    <div class="form-group">
                                                        <select name="locationCode" id="locationCode" class="form-control">
                                                        <option value="all">Select Location CODE</option>
                                                          @forelse($codetypes as $codetype)
                                                          <option value="{{ $codetype->id }}" >{{ucfirst($codetype->location_name)}}</option>
                                                          @empty
                                                            <option value="">No Location Added</option>
                                                        @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5 >@lang('app.product.item.salesCategory')</h5>
                                                    <div class="form-group">
                                                        <select name="salesCategory" id="salesCategory" class="form-control">
                                                            <option value="all">Select Category</option>
                                                            @forelse($salescategories as $salescategory)
                                                            <option value="{{ $salescategory->salescategory_code }}" >{{ucfirst($salescategory->salescategory_name)}}</option>
                                                            @empty
                                                            <option value="">No Category Added</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <h5>Status</h5>
                                                    <div class="form-group">
                                                        <select name="fl_status_id" id="fl_status_id" class="form-control">
                                                            <option value="all">Select Status</option>
                                                            @forelse($productStatuses as $productStatus)
                                                                <option value="{{ $productStatus->id }}" >{{$productStatus->status_name}}</option>
                                                             @empty
                                                                <option value="">No Status Added</option>
                                                             @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12 m-b-20">
								<label class="check-container d-block"><input id="without_order_date_fl" name="without_order_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items without a order date. </label>
								<label class="check-container d-block"><input id="with_order_date_fl" name="with_order_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with an order date.</label>
								<label class="check-container d-block"><input id="rfq_sent_fl" name="rfq_sent_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items RFQ was sent.</label>
								<label class="check-container d-block"><input id="rfq_not_sent_fl" name="rfq_not_sent_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items RFQ was NOT sent.</label>
								<label class="check-container d-block"><input id="quote_received_fl" name="quote_received_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items Quote was received.</label>
								<label class="check-container d-block"><input id="quote_not_received_fl" name="quote_not_received_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items Quote was NOT received.</label>
								<label class="check-container d-block"><input id="cfa_approved_fl" name="cfa_approved_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where CFA was approved.</label>
								<label class="check-container d-block"><input id="cfa_not_approved_fl" name="cfa_not_approved_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where CFA was NOT approved.</label>
								<label class="check-container d-block"><input id="ordered_fl" name="ordered_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items that were ordered.</label>
								<label class="check-container d-block"><input id="not_ordered_fl" name="not_ordered_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items that NOT ordered.</label>
								<label class="check-container d-block"><input id="flagged_items_fl" name="flagged_items_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show all flagged items.</label>
								<label class="check-container d-block"><input id="without_estimated_ship_date_fl" name="without_estimated_ship_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items without a estimated ship date.</label>
								<label class="check-container d-block"><input id="with_estimated_ship_date_fl" name="with_estimated_ship_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with an estimated ship date.</label>
								<label class="check-container d-block"><input id="without_ship_date_fl" name="without_ship_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items without ship date.</label>
								<label class="check-container d-block"><input id="with_ship_date_fl" name="with_ship_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with a ship date.</label>
								<label class="check-container d-block"><input id="with_estimated_received_date_fl" name="with_estimated_received_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with a estimated received date.</label>
								<label class="check-container d-block"><input id="with_received_date_fl" name="with_received_date_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with a received date.</label>
								<label class="check-container d-block"><input id="with_tracking_number_fl" name="with_tracking_number_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with tracking number.</label>
								<label class="check-container d-block"><input id="client_approved_fl" name="client_approved_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where client approved.</label>
								<label class="check-container d-block"><input id="client_declined_fl" name="client_declined_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where client declined.</label>
								<label class="check-container d-block"><input id="not_approve_decline_fl" name="not_approve_decline_fl" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where client did not approve/decline.</label>
                            </div>
                                                <h3 class="m-b-0">Download Sec Sheet Package:</h3>
                                                
                                                    <div class="col-md-12">
                                                        <h5 >Click to hide data in table + PDF</h5>
                                                                <ul class="check-columns">
                                    
                                    
                                    <li> <label class="check-container"><input id="spec_num_fl" name="spec_num_fl" type="checkbox" data-column="3" class="toggle-vis" /><span class="checkmark"></span> Spec # </label></li>
                                    <li> <label class="check-container"><input id="total_sale_fl" name="total_sale_fl" type="checkbox" data-column="21" class="toggle-vis" /><span class="checkmark"></span> Total Sale </label></li>
                                    <li> <label class="check-container"><input id="name_fl" name="name_fl" type="checkbox" data-column="6" class="toggle-vis" /><span class="checkmark"></span> Project Name </label></li>
                                    <li> <label class="check-container"><input id="msrp_fl" name="msrp_fl" type="checkbox" data-column="22" class="toggle-vis" /><span class="checkmark"></span> MSRP </label></li>
                                    <li> <label class="check-container"><input id="location_code_fl" name="location_code_fl" type="checkbox" data-column="7" class="toggle-vis" /><span class="checkmark"></span> Location </label></li>
                                    <li> <label class="check-container"><input id="acknowledgement_fl" name="acknowledgement_fl" type="checkbox" data-column="23" class="toggle-vis" /><span class="checkmark"></span> Acknowledgement </label></li>
                                    <li> <label class="check-container"><input id="sales_category_fl" name="sales_category_fl" type="checkbox" data-column="8" class="toggle-vis" /><span class="checkmark"></span> Category </label></li>
                                    <li> <label class="check-container"><input id="est_ship_date_fl" name="est_ship_date_fl" type="checkbox" data-column="24" class="toggle-vis" /><span class="checkmark"></span> Est. Ship Date </label></li>
                                    <li> <label class="check-container"><input id="vendor_id_fl" name="vendor_id_fl" type="checkbox" data-column="9" class="toggle-vis" /><span class="checkmark"></span> Vendor </label></li>
                                    <li> <label class="check-container"><input id="act_ship_date_fl" name="act_ship_date_fl" type="checkbox" data-column="25" class="toggle-vis" /><span class="checkmark"></span> Act. Ship Date </label></li>
                                    <li> <label class="check-container"><input id="manufacturer_fl" name="manufacturer_fl" type="checkbox" data-column="10" class="toggle-vis" /><span class="checkmark"></span> Manufacturer </label></li>
                                    <li> <label class="check-container"><input id="est_receive_date_fl" name="est_receive_date_fl" type="checkbox" data-column="26" class="toggle-vis" /><span class="checkmark"></span> Est. Receive Date </label></li>
                                    <li> <label class="check-container"><input id="notes_fl" name="notes_fl" type="checkbox" data-column="11" class="toggle-vis" /><span class="checkmark"></span> Notes </label></li>
                                    <li> <label class="check-container"><input id="act_receive_date_fl" name="act_receive_date_fl" type="checkbox" data-column="27" class="toggle-vis" /><span class="checkmark"></span> Act. Receive Date </label></li>
                                    <li> <label class="check-container"><input id="url_fl" name="url_fl" type="checkbox" data-column="12" class="toggle-vis" /><span class="checkmark"></span> URL </label></li>
                                    <li> <label class="check-container"><input id="received_by_fl" name="received_by_fl" type="checkbox" data-column="28" class="toggle-vis" /><span class="checkmark"></span> Received By </label></li>
                                    <li> <label class="check-container"><input id="dimensions_fl" name="dimensions_fl" type="checkbox" data-column="13" class="toggle-vis" /><span class="checkmark"></span> Dimensions </label></li>
                                    <li> <label class="check-container"><input id="est_install_date_fl" name="est_install_date_fl" type="checkbox" data-column="29" class="toggle-vis" /><span class="checkmark"></span> Est. Install Date </label></li>
                                    <li> <label class="check-container"><input id="materials_fl" name="materials_fl" type="checkbox" data-column="14" class="toggle-vis" /><span class="checkmark"></span> Material </label></li>
                                    <li> <label class="check-container"><input id="act_install_date_fl" name="act_install_date_fl" type="checkbox" data-column="30" class="toggle-vis" /><span class="checkmark"></span> Act. Install Date </label></li>
                                    <li> <label class="check-container"><input id="qty_fl" name="spec_num_fl" type="checkbox" data-column="15" class="toggle-vis" /><span class="checkmark"></span> QTY </label></li>
                                    <li> <label class="check-container"><input id="product_number_fl" name="product_number_fl" type="checkbox" data-column="31" class="toggle-vis" /><span class="checkmark"></span> Product Number </label></li>
                                    <li> <label class="check-container"><input id="cost_per_unit_fl" name="cost_per_unit_fl" type="checkbox"  data-column="16" class="toggle-vis" /><span class="checkmark"></span> Cost Per Unit </label></li>
                                    <li> <label class="check-container"><input id="finish_color_fl" name="finish_color_fl" type="checkbox" data-column="32" class="toggle-vis" /><span class="checkmark"></span> Finish/Color </label></li>
                                    <li> <label class="check-container"><input id="default_markup_fl" name="default_markup_fl" type="checkbox" data-column="17" class="toggle-vis" /><span class="checkmark"></span> Markup $ </label></li>
                                    <li> <label class="check-container"><input id="freight_fl" name="freight_fl" type="checkbox" data-column="20" class="toggle-vis" /><span class="checkmark"></span> Freight </label></li>
                                    <li> <label class="check-container"><input id="default_markup_per_fl" name="default_markup_per_fl" type="checkbox" class="toggle-vis" data-column="18" /><span class="checkmark"></span> Markup % </label></li>
                    
                                    
                                </ul>
                                                    </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-xs-12">&nbsp;</label>
                                                        <button type="button" id="download-all-pdf" class="btn btn-success col-md-4 "><i class="fa fa-download"></i> @lang('app.download')</button>
                                                        <button type="button" id="apply-filters" class="btn btn-success col-md-3 col-md-offset-1"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                                        <button type="button" id="reset-filters" class="btn btn-inverse col-md-3 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endsection
                                    
                                    <div class="col-md-12 p-b-20 p-t-10 m-b-20 border-bottom">
					<a href="javascript:void(0)" class="p-button" id="view-d">View Details</a>
					<a href="javascript:void(0)" class="p-button m-l-10"id="view-o">View Other</a>
		</div><!--end of col-12-->
                                    
                                    <div id="products-wrp">
                    
                    
                    
                    <div class="col-md-12 d-i-new" id="view-details">
                
                     @foreach($products as $row)
                     
                        <?php

                            $image = asset('img/default-product.png');
                            if(!empty($row->picture)) {
                                $pictures = json_decode($row->picture);
                                if($pictures) {
                                    $image =  asset('user-uploads/products/'.$row->id.'/'.$pictures[0].'');
                                }
                            }
                            
                                $name = '';
                                if(isset($row->name) && !empty($row->name)) {
                                    $name = ucfirst($row->name);
                                }
                                $url = '';
                                if(isset($row->url) && !empty($row->url)) {
                                    $url = $row->url;
                                }
                                
                               
                        $style = '';
                        
                        if(isset($row->product_status_id) && !empty($row->product_status_id)) {
                            $style = 'color: '.$row->productStatus->status_color.' !important; border: 1px solid '.$row->productStatus->status_color.' !important;';
                        }
                        
//                        $client_status_cls = 'pending';
//                        $client_status_text = 'Pending';
                        $client_status_cls = '';
                        $client_status_text = ' ';
                        if($row->is_approved == 1) {
                            $client_status_text = 'Approved';
                            $client_status_cls = 'approved';
                        } else if (!is_null($row->is_approved) && $row->is_approved == 0){
                            $client_status_text = 'Declined';
                            $client_status_cls = 'declined';
                        }
                        
                        
                        // new logic assign multiple projects to products
                        $project_name = ' ';
                        if($row->projects) { 
                             foreach ($row->projects as $project) {
                                 $projectDT = \App\Project::where('id', $project->project_id)->first();
                                 if($projectDT) {
                                   $project_name = ucfirst($projectDT->project_name);
                                   break;
                                }
                             }
                         }
                         
                        $category = ' ';
                        $item = json_decode($row->item);
                        if(isset($item->salesCategory) && !empty($item->salesCategory)) {
                            $salesCategory = \App\SalescategoryType::where('salescategory_code', $item->salesCategory)->first();
                            if($salesCategory) {
                                $category =  ucfirst($salesCategory->salescategory_name);
                            }
                        }
                        
                        $locationCode = ' ';
                        if($row->codes) {
                            foreach ($row->codes as $code) {
                                if($code->code) {
                                    $locationCode = ucfirst($code->code->location_name);
                                    break;
                                }
                            }
                        }
                        
                       
                        ?>
                     
                        <div class="d-flex border">
                                   <div class="col-auto img">
                                           <img src="{{$image}}" alt="product" width="75" height="75">
                                   </div>
                                   <div class="col-auto name">					
                                           <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'name');" contenteditable="true">{{$name}}</b> Product Name</div>
                                           <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'url');" contenteditable="true">{{$url}}</b> Product Url</div>
                                           <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'spec_number');" contenteditable="true">{{$row->spec_number}}</b> Spec Number</div>
                                   </div>
                                   <div class="col-auto status">					
                                           <div>
                                               <select id="product-status-color-<?php echo $row->id; ?>"  onchange="saveData(this, '<?php echo $row->id; ?>', 'product_status_id');" style="<?php echo $style ?>">
                                                   <option value="">Select</option>
                                                    <?php foreach ($productStatuses as $status) {
                                                        $selected = '';
                                                        if($status->id == $row->product_status_id) {
                                                            $selected = 'selected="selected"';
                                                        }
                                                        
                                                        ?>
                                                        <option data-color="{{$status->status_color}}" <?php echo $selected; ?> value="{{$status->id}}">{{$status->status_name}}</option>
                                                    <?php } ?>
                                               </select> Product Status
                                           </div>
                                       <div><b contenteditable="false" class="status <?php echo $client_status_cls; ?>">{{$client_status_text}}</b> Client Status</div>
                                   </div>
                                   <div class="col-auto other">					
                                       <div><b contenteditable="false">{{$project_name}}</b> Project</div>
                                           
                                           <div>
                                               <select onchange="saveData(this, '<?php echo $row->id; ?>', 'vendor_id');">
                                                   <option value="">Select</option>
                                                   <?php foreach ($clientVendors as $vendor) {
                                                        $selected = '';
                                                        if($vendor->id == $row->vendor_id) {
                                                            $selected = 'selected="selected"';
                                                        }
                                                        ?>
                                                        <option <?php echo $selected; ?> value="{{$vendor->id}}">{{$vendor->company_name}}</option>
                                                    <?php } ?>
                                                   
                                                   <option value="deciding">Vendor</option>
                                               </select> 
                                               Vendor
                                           </div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'materials');" contenteditable="true">{{$row->materials}}</b> Material</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'acknowledgement');" contenteditable="true">{{$row->acknowledgement}}</b> Acknowledgement</div>
                                           <div><b  contenteditable="false">{{$category}}</b> Product Category</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'manufacturer');" contenteditable="true">{{ucfirst($row->manufacturer)}}</b> Manufacturer</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'product_number');" contenteditable="true">{{$row->product_number}}</b> Product Number</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'finish_color');" contenteditable="true">{{ucfirst($row->finish_color)}}</b> Finish/Color</div>
                                           <div><b  contenteditable="false">{{$locationCode}}</b> Location Code</div> 
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'dimensions');" contenteditable="true">{{$row->dimensions}}</b> Dimensions</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'tracking_number');" contenteditable="true">{{$row->tracking_number}}</b> Tracking Number</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'lead_time');" contenteditable="true">{{$row->lead_time}}</b> Lead Time</div>
                                           
                                           
                                   </div>
                                   <div class="col note">					
                                           <div>Notes<b  onblur="saveData(this, '<?php echo $row->id; ?>', 'notes');" contenteditable="true" class="notes">{{$row->notes}}</b></div>
                                   </div>

                                           <div class="col-auto last-action">
                                               <?php
                                               
                                                    $action = '<div class="btn-group dropdown m-r-10">
                                                    <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                                    <ul role="menu" class="dropdown-menu pull-right">
                                                      <li><a href="' . route('admin.products.edit', [$row->id]). '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                                                        <li><a href="' . route('admin.products.edit', [$row->id, "copy"=>1]). '"><i class="fa fa-copy" aria-hidden="true"></i> ' . trans('app.copy') . '</a></li>
                                                      <li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                                                      <li><a href="'. route("admin.products.download", $row->id).'"><i class="fa fa-file-pdf-o"></i> ' . trans('app.download') . '</a></li>';

                                                    $action .= ' <li><a href="javascript:" data-user-id="' . $row->id . '" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li>';


                                                    $action .= '</ul> </div>';
                                                    echo $action;
                                               ?>
                                           
                                           </div>
                           </div>
                      @endforeach
                      
                      <div id="paginationAll">
              {{ $products->links() }}
            </div>
                     
                    
			
			
		</div><!--end of col-12-->
               
                
                <div class="col-md-12 d-i-new" id="view-others" style="display:none">
                    
                     @foreach($products as $row)
                     
                        <?php

                            $image = asset('img/default-product.png');
                            if(!empty($row->picture)) {
                                $pictures = json_decode($row->picture);
                                if($pictures) {
                                    $image =  asset('user-uploads/products/'.$row->id.'/'.$pictures[0].'');
                                }
                            }
                            
                            $name = '';
                            if(isset($row->name) && !empty($row->name)) {
                                $name = ucfirst($row->name);
                            }
                            $url = '';
                            if(isset($row->url) && !empty($row->url)) {
                                $url = $row->url;
                            }
                            
                            $cost_per_unit = '';
                            if(isset($row->cost_per_unit) && !empty($row->cost_per_unit) && is_numeric($row->cost_per_unit)) {
                            $cost_per_unit =  $row->cost_per_unit? number_format($row->cost_per_unit, 2) : '';
                            }
                            $markup_fix = '';
                            if(isset($row->markup_fix) && !empty($row->markup_fix) && is_numeric($row->markup_fix)) {
                                $markup_fix =  $row->markup_fix? number_format($row->markup_fix, 2) : '';
                            }
                            
                            $markup_per = '';
                            if(isset($row->markup_per) && !empty($row->markup_per) && is_numeric($row->markup_per)) {
                                $markup_per = number_format($row->markup_per, 2);
                            }
                            
                            $default_markup_fix = '-';
                            if(isset($row->default_markup_fix) && !empty($row->default_markup_fix) && is_numeric($row->default_markup_fix)) {
                                $default_markup_fix = $row->default_markup_fix ? number_format($row->default_markup_fix, 2) : '';
                            }
                            
                            $freight = '';
                            if(isset($row->freight) && !empty($row->freight) && is_numeric($row->freight)) {
                                $freight =  $row->freight ? number_format($row->freight, 2) : '';
                            }
                            
                            $msrp = '';
                            if(isset($row->msrp) && !empty($row->msrp) && is_numeric($row->msrp)) {
                                $msrp =  $row->msrp ? number_format($row->msrp, 2) : '';
                            }
                            
                            $total_sale = ' ';
                            if(isset($row->total_sale) && !empty($row->total_sale) && is_numeric($row->total_sale)) {
                                $total_sale = $row->total_sale? number_format($row->total_sale, 2) : '';
                            }
                            
                            $est_ship_date = '';
                            if(!is_null($row->est_ship_date)) {
                                $est_ship_date = \Carbon\Carbon::parse($row->est_ship_date)->format($global->date_format);
                            }
                            $act_ship_date = '';
                            if(!is_null($row->act_ship_date)) {
                                $act_ship_date =  \Carbon\Carbon::parse($row->act_ship_date)->format($global->date_format);
                            }
                            $est_receive_date = '';
                            if(!is_null($row->est_receive_date)) {
                                $est_receive_date = \Carbon\Carbon::parse($row->est_receive_date)->format($global->date_format);
                            }
                            
                            $act_receive_date = '';
                            if(!is_null($row->act_receive_date)) {
                                $act_receive_date  = \Carbon\Carbon::parse($row->act_receive_date)->format($global->date_format);
                            }
                            $est_install_date = '';
                            if(!is_null($row->est_ship_date)) {
                                $est_install_date = \Carbon\Carbon::parse($row->est_install_date)->format($global->date_format);
                            }
                            $act_Install_date = '';
                            if(!is_null($row->act_Install_date)) {
                                $act_Install_date  = \Carbon\Carbon::parse($row->act_Install_date)->format($global->date_format);
                            }
                            
                            $po_sent_date = '';
                            if(!is_null($row->po_sent_date)) {
                                $po_sent_date  = \Carbon\Carbon::parse($row->po_sent_date)->format($global->date_format);
                            }
                            $cfa_approved_date = '';
                            if(!is_null($row->cfa_approved_date)) {
                                $cfa_approved_date  = \Carbon\Carbon::parse($row->cfa_approved_date)->format($global->date_format);
                            }
                            
                            $rfq_sent_date = '';
                            if(!is_null($row->rfq_sent_date)) {
                                $rfq_sent_date  = \Carbon\Carbon::parse($row->rfq_sent_date)->format($global->date_format);
                            }
                            
                            $received_date = '';
                            if(!is_null($row->received_date)) {
                                $received_date  = \Carbon\Carbon::parse($row->received_date)->format($global->date_format);
                            }
                            $ordered_date = '';
                            if(!is_null($row->ordered_date)) {
                                $ordered_date  = \Carbon\Carbon::parse($row->ordered_date)->format($global->date_format);
                            }
                            
                            $quote_received = '';
                            if(!is_null($row->quote_received)) {
                                $quote_received  = \Carbon\Carbon::parse($row->quote_received)->format($global->date_format);
                            }
                            
                            $locationCode = ' ';
                            if($row->codes) {
                                foreach ($row->codes as $code) {
                                    if($code->code) {
                                        $locationCode = ucfirst($code->code->location_name);
                                        break;
                                    }
                                }
                            }
                            
                           
                            

                        ?>
			
                            <div class="d-flex border">
                                    <div class="col-auto img">
                                            <img src="{{$image}}" alt="product" width="75" height="75">
                                    </div>
                                    <div class="col-auto name">					
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'name');" contenteditable="true">{{$name}}</b> Product Name</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'url');" contenteditable="true">{{$url}}</b> Product Url</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'spec_number');" contenteditable="true">{{$row->spec_number}}</b> Spec Number</div>
                                    </div>
                                    <div class="col-auto price">					
                                            <div><b id="cost-per-unit-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'cost_per_unit');" contenteditable="true">{{$cost_per_unit}}</b> Per Unit Price</div>
                                            <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'quantity');" contenteditable="true">{{$row->quantity}}</b> Quantity</div>
                                            <div><b id="markup-fix-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'markup_fix');" contenteditable="true">{{$markup_fix}}</b> Markup {{$global->currency->currency_symbol}}</div>
                                            <div><b id="markup-per-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'markup_per');" contenteditable="true">{{$markup_per}}</b> Markup %</div>
                                            <div><b id="default-markup_fix-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'spec_number');" contenteditable="false">{{$default_markup_fix}}</b> Markup % Total</div>
                                            <div><b id="freight-fix-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'freight');" contenteditable="true">{{$freight}}</b> Freight</div>
                                            <div><b id="msrp-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'msrp');" contenteditable="true">{{$msrp}}</b> MSRP</div>
                                            <div><b id="total-sale-<?php echo $row->id; ?>" contenteditable="false">{{$total_sale}}</b> Total Sale</div>
                                    </div>

                                    <div class="col-auto date">					
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'po_sent_date');" type="text" class="datepicker" value="{{$po_sent_date}}"> PO Sent</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'cfa_approved_date');" type="text" class="datepicker" value="{{$cfa_approved_date}}"> CFA Approved</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'act_ship_date');" type="text" class="datepicker" value="{{$act_ship_date}}"> Act Ship Date</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'rfq_sent_date');" type="text" class="datepicker" value="{{$rfq_sent_date}}"> RFQ Sent</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'ordered_date');" type="text" class="datepicker" value="{{$ordered_date}}"> Ordered</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'est_receive_date');" type="text" class="datepicker" value="{{$est_receive_date}}"> Est Received Date</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'quote_received');" type="text" class="datepicker" value="{{$quote_received}}"> Quote Received</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'est_ship_date');" type="text" class="datepicker" value="{{$est_ship_date}}"> Est Ship Date</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'est_install_date');" type="text" class="datepicker" value="{{$est_install_date}}"> Est Install Date</div>
                                    </div>
                                    <div class="col-auto received">					
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'received_date');" type="text" class="datepicker" value="{{$received_date}}"> Received</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'received_by');" contenteditable="true">{{$row->received_by}}</b> Received By</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'warehouse');"  contenteditable="true">{{ucfirst($row->warehouse)}}</b> Warehouse</div> 
                                    </div>
                                    <div class="col note">					
                                            <div>Expediting<b onblur="saveData(this, '<?php echo $row->id; ?>', 'expediting');" contenteditable="true" class="notes">{{$row->expediting}}</b></div>
                                    </div>

                                    <div class="col-auto last-action">
                                        <?php

                                            $action = '<div class="btn-group dropdown m-r-10">
                                            <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                            <ul role="menu" class="dropdown-menu pull-right">
                                              <li><a href="' . route('admin.products.edit', [$row->id]). '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                                                <li><a href="' . route('admin.products.edit', [$row->id, "copy"=>1]). '"><i class="fa fa-copy" aria-hidden="true"></i> ' . trans('app.copy') . '</a></li>
                                              <li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                                              <li><a href="'. route("admin.products.download", $row->id).'"><i class="fa fa-file-pdf-o"></i> ' . trans('app.download') . '</a></li>';

                                            $action .= ' <li><a href="javascript:" data-user-id="' . $row->id . '" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li>';


                                            $action .= '</ul> </div>';
                                            echo $action;
                                       ?>
                                        
                                    </div>
                            </div>
                        
                        @endforeach
		    
		</div><!--end of col-12-->
                    
                    
                    
                    
                </div>

                                    </div>
                                </div>

                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="editTimeLogModal" role="dialog" aria-labelledby="myModalLabel"
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
    
    {{--Ajax Modal--}}
    <div style="z-index: 10000;" class="modal fade bs-modal-md in" id="productRFQModal" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase"
                        id="modelHeading">Request For Quote</span>
                </div>
                
                <div class="modal-body">
                    <div class="portlet-body">

                    {!! Form::open(['id'=>'productRFQForm','class'=>'ajax-form','method'=>'POST']) !!}
                    <input type="hidden" value="" name="product_id" id="product_id" />
                        <div class="form-body">
                            <div class="row">
                                <div class="col-xs-12 ">
                                    <h3>Select Email</h3>
                                    <div class="form-group">
                                        <div class="radio-list">      
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input checked="" type="radio" name="select_rfq" id="vendor" value="vendor" >
                                                    <label for="vendor">Vendor</label>
                                                </div>
                                                <div class="radio radio-info">
                                                    <input type="radio" name="select_rfq" id="agent" value="agent" >
                                                    <label for="agent">Indema Purchase Agent</label>
                                                </div>
                                                <div class="radio radio-info">
                                                    <input type="radio" name="select_rfq" id="other" value="other" >
                                                    <label for="other">Other</label>
                                                </div>
                                            </label> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 " id="other_email_wrp" style="display:none;">
                                    <div class="form-group">
                                        <label class="required">Other Email</label>
                                        <input id="other_email" class="form-control" name="other_email" value="" />
                                    </div>
                                </div>
                                <div class="col-xs-12 ">
                                    <div class="form-group">
                                        <label class="required">Additional Information</label>
                                        <textarea style="min-height: 100px;" name="additional_info" id="additional_info" class="form-control" >
Please be as descriptive as possible, and provide any additional call outs needed for this quote, as well as shipping city and Zip code
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-xs-12 ">
                                    <div class="form-group">
                                        <p><strong>PLEASE NOTE:</strong> Indema purchase agent will not provide any quotes for retail items. They will only provide information for trade-only product</p>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" id="save-group" class="btn btn-success"> <i class="fa fa-check"></i>Send</button>
                        </div>
                    {!! Form::close() !!}
                    </div>
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
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('plugins/tabledit/jquery.tabledit.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
    
    var show_spec_number = '{{$productSettings->show_spec_number}}';
        var show_project_name = '{{$productSettings->show_project_name}}';
        var show_location = '{{$productSettings->show_location}}';
        var show_category = '{{$productSettings->show_category}}';
        var show_vendor = '{{$productSettings->show_vendor}}';
        var show_manufacturer = '{{$productSettings->show_manufacturer}}';
        var show_notes = '{{$productSettings->show_notes}}';
        var show_url = '{{$productSettings->show_url}}';
        var show_dimensions = '{{$productSettings->show_dimensions}}';
        var show_materials = '{{$productSettings->show_materials}}';
        var show_quantity = '{{$productSettings->show_quantity}}';
        var show_cost_per_unit = '{{$productSettings->show_cost_per_unit}}';
        var show_markup_fix = '{{$productSettings->show_markup_fix}}';
        var show_markup_per = '{{$productSettings->show_markup_per}}';
        var show_freight = '{{$productSettings->show_freight}}';
        var show_total_sale = '{{$productSettings->show_total_sale}}';
        var show_msrp = '{{$productSettings->show_msrp}}';
        var show_acknowledgement = '{{$productSettings->show_acknowledgement}}';
        var show_est_ship_date = '{{$productSettings->show_est_ship_date}}';
        var show_act_ship_date = '{{$productSettings->show_act_ship_date}}';
        var show_est_receive_date = '{{$productSettings->show_est_receive_date}}';
        var show_act_receive_date = '{{$productSettings->show_act_receive_date}}';
        var show_received_by = '{{$productSettings->show_received_by}}';
        var show_est_install_date = '{{$productSettings->show_est_install_date}}';
        var show_act_install_date = '{{$productSettings->show_act_install_date}}';
        var show_product_number = '{{$productSettings->show_product_number}}';
        var show_finish_color = '{{$productSettings->show_finish_color}}';
        
        function autoToggleFilter(){
            if(show_spec_number == 'no') {$('#spec_num_fl').trigger("click");}
            if(show_project_name == 'no') {$('#name_fl').trigger("click");}
            if(show_location == 'no') {$('#location_code_fl').trigger("click");}
            if(show_category == 'no') {$('#sales_category_fl').trigger("click");}
            if(show_vendor == 'no') {$('#vendor_id_fl').trigger("click");}
            if(show_manufacturer == 'no') {$('#manufacturer_fl').trigger("click");}
            if(show_notes == 'no') {$('#notes_fl').trigger("click");}
            if(show_url == 'no') {$('#url_fl').trigger("click");}
            if(show_dimensions == 'no') {$('#dimensions_fl').trigger("click");}
            if(show_materials == 'no') {$('#materials_fl').trigger("click");}
            if(show_quantity == 'no') {$('#qty_fl').trigger("click");}
            if(show_cost_per_unit == 'no') {$('#cost_per_unit_fl').trigger("click");}
            if(show_markup_fix == 'no') {$('#default_markup_fl').trigger("click");}
            if(show_markup_per == 'no') {$('#default_markup_per_fl').trigger("click");}
            if(show_freight == 'no') {$('#freight_fl').trigger("click");}
            if(show_total_sale == 'no') {$('#total_sale_fl').trigger("click");}
            if(show_msrp == 'no') {$('#msrp_fl').trigger("click");}
            if(show_acknowledgement == 'no') {$('#acknowledgement_fl').trigger("click");}
            if(show_est_ship_date == 'no') {$('#est_ship_date_fl').trigger("click");}
            if(show_act_ship_date == 'no') {$('#act_ship_date_fl').trigger("click");}
            if(show_est_receive_date == 'no') {$('#est_receive_date_fl').trigger("click");}
            if(show_act_receive_date == 'no') {$('#act_receive_date_fl').trigger("click");}
            if(show_received_by == 'no') {$('#received_by_fl').trigger("click");}
            if(show_est_install_date == 'no') {$('#est_install_date_fl').trigger("click");}
            if(show_act_install_date == 'no') {$('#act_install_date_fl').trigger("click");}
            if(show_product_number == 'no') {$('#product_number_fl').trigger("click");}
            if(show_finish_color == 'no') {$('#finish_color_fl').trigger("click");}
        }
    
    $('#save-group').click(function () {
            $.easyAjax({
                url: '{{route('admin.products.send-rfq')}}',
                container: '#productRFQForm',
                type: "POST",
                data: $('#productRFQForm').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $('#productRFQModal').modal('hide');
                    }
                }
            })
        });
        
        $('input[type=radio][name=select_rfq]').change(function() {
        if (this.value == 'other') {
           $('#other_email_wrp').show();
        } else{
            $('#other_email_wrp').hide();
        }
    });
    
  
    
    $(function() {
        //loadTable();
        
         $('body').on('click', '.pa-rfq', function(){
                var id = $(this).data('user-id');
                $('#product_id').val(id);
            });
        
            $('body').on('click', '.sa-params', function(){
                var id = $(this).data('user-id');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted product!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {

                        var url = "{{ route('admin.products.destroy',':id') }}";
                        url = url.replace(':id', id);
                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    loadTable();
                                    //location.reload();
                                    //$.unblockUI();
                                    //LaravelDataTables["products-table"].draw();
                                }
                            }
                        });
                    }
                });
            });
            
        $('#products-table').on('preXhr.dt', function (e, settings, data) {
            var locationCode = $('#locationCode').val();
            var salesCategory = $('#salesCategory').val();

            if (locationCode == '') {
                locationCode = null;
            }
            
            if (salesCategory == '') {
                salesCategory = null;
            }

            data['locationCode'] = locationCode;
            data['salesCategory'] = salesCategory;
            
        });
        
        $('.toggle-vis').on( 'click', function (e) {
            //e.preventDefault();
            //
            //table.columns.adjust().draw( false );
            //var table =  window.LaravelDataTables["products-table"];
            
            // Get the column API object
            //var column = table.column( $(this).attr('data-column') );

            // Toggle the visibility
            //column.visible( ! column.visible() );
            //table.column(0).visible(false); 
        } );
            
            $( "#products-table tr" ).sortable();
            
            
            
            $(document).on("click", ".pagination a", function() {
        	var pag_block_id = $(this).closest('div').attr('id');

	        //get url and make final url for ajax 
	        var url = $(this).attr("href");

        	var append = url.indexOf("?") == -1 ? "?" : "&";
        	var finalURL = url + append + $("#filter-form").serialize();

	        $.get(finalURL, function(response) {
        		 console.log(response);
    		 	$('#products-wrp').html(response.view);

	        });

	        return false;
      	})
            
        });
        
       function loadTable (){
            
            var url = '{{route('admin.products.download-all')}}';
            
            var projectId = "{{ $project_id }}";
            var salesCategory = $('#salesCategory').val();
            var locationCode = $('#locationCode').val();
            var statusId = $('#fl_status_id').val();
            
            var dataObj = {
                    'project_id' : projectId,
                    'salesCategory': salesCategory,
                    'locationCode' : locationCode,
                    'statusId' : statusId
            };
            
            if($('#without_order_date_fl').is(':checked')){
                dataObj['without_order_date_fl'] = 1;
            }
            if($('#with_order_date_fl').is(':checked')){
                dataObj['with_order_date_fl'] = 1;
            }
            if($('#rfq_sent_fl').is(':checked')){
                dataObj['rfq_sent_fl'] = 1;
            }
            if($('#rfq_not_sent_fl').is(':checked')){
                dataObj['rfq_not_sent_fl'] = 1;
            }
             if($('#quote_received_fl').is(':checked')){
                dataObj['quote_received_fl'] = 1;
            }
            if($('#quote_not_received_fl').is(':checked')){
                dataObj['quote_not_received_fl'] = 1;
            }
            if($('#cfa_approved_fl').is(':checked')){
                dataObj['cfa_approved_fl'] = 1;
            }
            if($('#cfa_not_approved_fl').is(':checked')){
                dataObj['cfa_not_approved_fl'] = 1;
            }
            if($('#ordered_fl').is(':checked')){
                dataObj['ordered_fl'] = 1;
            }
            if($('#not_ordered_fl').is(':checked')){
                dataObj['not_ordered_fl'] = 1;
            }
            if($('#flagged_items_fl').is(':checked')){
                dataObj['flagged_items_fl'] = 1;
            }
            if($('#without_estimated_ship_date_fl').is(':checked')){
                dataObj['without_estimated_ship_date_fl'] = 1;
            }
            if($('#with_estimated_ship_date_fl').is(':checked')){
                dataObj['with_estimated_ship_date_fl'] = 1;
            }
            if($('#without_ship_date_fl').is(':checked')){
                dataObj['without_ship_date_fl'] = 1;
            }
            if($('#without_order_date_fl').is(':checked')){
                dataObj['without_order_date_fl'] = 1;
            }
            if($('#with_ship_date_fl').is(':checked')){
                dataObj['with_ship_date_fl'] = 1;
            }
            if($('#with_estimated_received_date_fl').is(':checked')){
                dataObj['with_estimated_received_date_fl'] = 1;
            }
            if($('#with_received_date_fl').is(':checked')){
                dataObj['with_received_date_fl'] = 1;
            }
            if($('#with_tracking_number_fl').is(':checked')){
                dataObj['with_tracking_number_fl'] = 1;
            }
            if($('#client_approved_fl').is(':checked')){
                dataObj['client_approved_fl'] = 1;
            }
            if($('#client_declined_fl').is(':checked')){
                dataObj['client_declined_fl'] = 1;
            }
            if($('#not_approve_decline_fl').is(':checked')){
                dataObj['not_approve_decline_fl'] = 1;
            }
        

            $.easyAjax({
                url:'{{ route('admin.products.filter-products-v3') }}',
                type: "GET",
                data: dataObj,
                success: function(response) {
                    $('#products-wrp').html(response.view);

                }
            });
        }
        
        $('#apply-filters').click(function () {
            loadTable();
        });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            loadTable();
        });
        
        
        $('#download-all-pdf').click(function () {
         
        var url = '{{route('admin.products.download-all')}}';
        var locationCode = $('#locationCode').val();
        var salesCategory = $('#salesCategory').val();
        var project_id = "{{ $project_id }}";
         
        url = url+'?project_id='+project_id+'&locationCode='+locationCode+'&salesCategory='+salesCategory;
        if($('#name_fl').is(':checked')){ url = url+'&name_fl=1'; }
        
        if($('#spec_num_fl').is(':checked')){ url = url+'&spec_num_fl=1'; }
        if($('#qty_fl').is(':checked')){ url = url+'&qty_fl=1'; }
        
        if($('#sales_tax_fl').is(':checked')){ url = url+'&sales_tax_fl=1'; }
        if($('#location_code_fl').is(':checked')){ url = url+'&location_code_fl=1'; }
        if($('#freight_fl').is(':checked')){ url = url+'&freight_fl=1'; }
        if($('#sales_category_fl').is(':checked')){ url = url+'&sales_category_fl=1'; }
        if($('#total_sale_fl').is(':checked')){ url = url+'&total_sale_fl=1'; }
        if($('#vendor_id_fl').is(':checked')){ url = url+'&vendor_id_fl=1'; }
        if($('#msrp_fl').is(':checked')){ url = url+'&msrp_fl=1'; }
        if($('#manufacturer_fl').is(':checked')){ url = url+'&manufacturer_fl=1'; }
        if($('#acknowledgement_fl').is(':checked')){ url = url+'&acknowledgement_fl=1'; }
        if($('#notes_fl').is(':checked')){ url = url+'&notes_fl=1'; }
        if($('#est_ship_date_fl').is(':checked')){ url = url+'&est_ship_date_fl=1'; }
        if($('#url_fl').is(':checked')){ url = url+'&url_fl=1'; }
        if($('#act_ship_date_fl').is(':checked')){ url = url+'&act_ship_date_fl=1'; }
        if($('#dimensions_fl').is(':checked')){ url = url+'&dimensions_fl=1'; }
        if($('#est_receive_date_fl').is(':checked')){ url = url+'&est_receive_date_fl=1'; }
        if($('#materials_fl').is(':checked')){ url = url+'&materials_fl=1'; }
        if($('#act_receive_date_fl').is(':checked')){ url = url+'&act_receive_date_fl=1'; }
        if($('#default_markup_per_fl').is(':checked')){ url = url+'&default_markup_per_fl=1'; }
        if($('#lead_time_fl').is(':checked')){ url = url+'&lead_time_fl=1'; }
        if($('#received_by_fl').is(':checked')){ url = url+'&received_by_fl=1'; }
        if($('#cost_per_unit_fl').is(':checked')){ url = url+'&cost_per_unit_fl=1'; }
        if($('#est_install_date_fl').is(':checked')){ url = url+'&est_install_date_fl=1'; }
        if($('#default_markup_fl').is(':checked')){ url = url+'&default_markup_fl=1'; }
        if($('#act_install_date_fl').is(':checked')){ url = url+'&act_install_date_fl=1'; }
        if($('#product_number_fl').is(':checked')){ url = url+'&product_number_fl=1'; }
        if($('#finish_color_fl').is(':checked')){ url = url+'&finish_color_fl=1'; }
         
         window.location.href = url;
    })
        
        function exportData(){
            var url = '{{ route('admin.products.export') }}';
            window.location.href = url;
        }


//    $('#save-form').click(function () {
//        $.easyAjax({
//            url: '{{route('admin.milestones.store')}}',
//            container: '#logTime',
//            type: "POST",
//            data: $('#logTime').serialize(),
//            success: function (data) {
//                if (data.status == 'success') {
//                    $('#logTime').trigger("reset");
//                    $('#logTime').toggleClass('hide', 'show');
//                    table._fnDraw();
//                }
//            }
//        })
//    });

//    $('#show-add-form, #close-form').click(function () {
//        $('#logTime').toggleClass('hide', 'show');
//    });

 $(function() {
         
        $.ajaxSetup({
            headers:{
              'X-CSRF-Token' : "{{ csrf_token() }}"
            }
        });
        
        
        
//       setTimeout(function(){  
//        loadTabledit();
//        }, 2000);

    });
    
    
    function saveData(obj, id, column){
        var url = '{{ route('admin.products.live-update') }}';
        var token = "{{ csrf_token() }}";
        var col_name = column;
        if(col_name == 'product_status_id' || col_name == 'vendor_id'
                || col_name == 'po_sent_date' || col_name == 'cfa_approved_date' || col_name == 'act_ship_date' || col_name == 'rfq_sent_date'
                || col_name == 'ordered_date' || col_name == 'est_receive_date' || col_name == 'quote_received' || col_name == 'est_ship_date' || col_name == 'est_install_date' || col_name == 'received_date') {
            var col_val = obj.value;
        } else {
            var col_val = obj.innerHTML;
        }
        
        
        if(col_name == 'product_status_id'){
            var product_status_color =  $('#product-status-color-'+id).find('option:selected').data('color');
            $('#product-status-color-'+id).css({'color': product_status_color+' !important;', 'border-color' : product_status_color+' !important;'});
        }
        
        var dataObj = {
            '_token' : token,
            'id': id,
            'action' : 'edit'
        };
        dataObj[col_name] = col_val;
        
        $.easyAjax({
            type: 'POST',
            url: url,
            data: dataObj,
            success: function (response) {
                if (response.status == "success") {
                    
                    var res_product_id = response.product_id;
                    var res_total_sale = response.total_sale;
                    var res_cost_per_unit = response.cost_per_unit;
                    var res_markup_fix = response.markup_fix;
                    var res_markup_per = response.markup_per;
                    var res_default_markup_fix = response.default_markup_fix;
                    var res_freight = response.freight;
                    var res_msrp = response.msrp;
                    
                    $('#total-sale-'+res_product_id).html(res_total_sale);
                    $('#cost-per-unit-'+res_product_id).html(res_cost_per_unit);
                    $('#markup-fix-'+res_product_id).html(res_markup_fix);
                    $('markup-per-'+res_product_id).html(res_markup_per);
                    $('#msrp-'+res_product_id).html(res_msrp);
                    $('#freight-fix-'+res_product_id).html(res_freight);
                    $('#default-markup_fix-'+res_product_id).html(res_default_markup_fix);
                }
            }
        });
        
    }
    
   $('.datepicker').datepicker({
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}'
    });

 function loadTabledit(){
        
        // visible all columns
        $('.toggle-vis:input:checkbox').attr("checked", false);
        var table =  window.LaravelDataTables["products-table"];
        table.columns([0,1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]).visible(true);
        
            $('#products-table').Tabledit({
                    url: '{{ route('admin.products.live-update') }}',
                    eventType: 'click',
                    editButton: false,
                    deleteButton: false,
                    rowIdentifier:'id',
                    hideIdentifier:true,
                    columns: {
                        identifier: [0, 'id'],
                        editable: [[3, 'spec_number'], [4, 'name'], [8, 'salesCategory', '{!!$salescategoriesData!!}'], [10, 'manufacturer'], [11, 'notes'],[12, 'url'], [13, 'dimensions'], [14, 'materials'],  [15, 'quantity'],  [16, 'cost_per_unit'], [17, 'markup_fix'], [18, 'markup_per'], [20, 'freight'], [22, 'msrp'], [23, 'acknowledgement'], [24, 'est_ship_date'], [25, 'act_ship_date'], [26, 'est_receive_date'], [27, 'act_receive_date'], [28, 'received_by'], [29, 'est_install_date'], [30, 'act_Install_date']]
                    },
                    onDraw: function() {
                    // Select all inputs of second column and apply datepicker each of them
                    $('table tr td:nth-child(25) input').each(function() {
                      $(this).datepicker({
                        todayHighlight: true,
                        autoclose: true,
                        weekStart:'{{ $global->week_start }}',
                        format: '{{ $global->date_picker_format }}'
                        
                      });
                    });
                    $('table tr td:nth-child(26) input').each(function() {
                      $(this).datepicker({
                        todayHighlight: true,
                        autoclose: true,
                        weekStart:'{{ $global->week_start }}',
                        format: '{{ $global->date_picker_format }}'
                        
                      });
                    });
                    $('table tr td:nth-child(27) input').each(function() {
                      $(this).datepicker({
                        todayHighlight: true,
                        autoclose: true,
                        weekStart:'{{ $global->week_start }}',
                        format: '{{ $global->date_picker_format }}'
                        
                      });
                    });
                    $('table tr td:nth-child(28) input').each(function() {
                      $(this).datepicker({
                        todayHighlight: true,
                        autoclose: true,
                        weekStart:'{{ $global->week_start }}',
                        format: '{{ $global->date_picker_format }}'
                        
                      });
                    });
                    $('table tr td:nth-child(30) input').each(function() {
                      $(this).datepicker({
                        todayHighlight: true,
                        autoclose: true,
                        weekStart:'{{ $global->week_start }}',
                        format: '{{ $global->date_picker_format }}'
                        
                      });
                    });
                    $('table tr td:nth-child(31) input').each(function() {
                      $(this).datepicker({
                        todayHighlight: true,
                       
                        weekStart:'{{ $global->week_start }}',
                        format: '{{ $global->date_picker_format }}'
                        
                      });
                    });
                    
                  },
                 onSuccess: function(data, textStatus, jqXHR) {
                   $('#'+data.product_id+' td:nth-child(22)').html(data.total_sale)
                   
                   $('#'+data.product_id+' td:nth-child(17) span').html(data.cost_per_unit)
                   $('#'+data.product_id+' td:nth-child(18) span').html(data.markup_fix)
                   $('#'+data.product_id+' td:nth-child(19) span').html(data.markup_per)
                   
                   $('#'+data.product_id+' td:nth-child(20)').html(data.default_markup_fix)
                   
                   $('#'+data.product_id+' td:nth-child(21) span').html(data.freight)
                   $('#'+data.product_id+' td:nth-child(23) span').html(data.msrp)
                   
                }
            });
            
        }
        
          function updatePageOrder() {
            
            var order = [];
            $('tr.row-st').each(function(index,element) {
              order.push({
                id: $(this).closest('tr').attr('id'),
                position: index+1
              });
            });
            var url = '{{ route('admin.products.upadte-order') }}';
            var token = "{{ csrf_token() }}";
            var project_id = "{{ $project_id }}";
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, order : order, project_id : project_id},
                success: function (response) {
                    if (response.status == "success") {
                        loadTable();
                        //$.unblockUI();
                        //LaravelDataTables["products-table"].draw();
                    }
                }
            });
        }
        
       
        
    $('ul.showProjectTabs .projectProducts').addClass('tab-current');
    
    
   
    
</script>
 <script type="text/javascript">
var sc;
jQuery(document).ready(function(){
    //constantly update the scroll position:
    sc=setInterval(scrollDown,200);

    //optional:stop the updating if it gets a click
    jQuery('#products-table_wrapper .row .col-sm-12').mousedown(function(e){
        clearInterval(sc);            
    });
	setTimeout(function(){ jQuery("#mobile-filter-toggle-product").insertBefore("#products-table_wrapper .dt-buttons"); }, 2000);
	
	jQuery( "body" ).addClass("products");
	jQuery('#mobile-filter-toggle-product').click(function () {
        jQuery('.filter-section').toggle();
    });
	jQuery(document).on("click", '#products-table_wrapper .dropdown-toggle[data-toggle="dropdown"]', function(){

if(jQuery(this).parents('td').hasClass('show'))
{
jQuery("#products-table_wrapper td.show").removeClass('show'); 
}
else{
   jQuery("#products-table_wrapper td.show").removeClass('show'); 
   jQuery(this).parents('td').addClass('show');
}

});
	jQuery(document).on("click", 'body', function(){

if(jQuery("#products-table_wrapper .btn-group").hasClass('open'))
{
jQuery(this).parents('td').addClass('show');  
}
else{
   jQuery("#products-table_wrapper td.show").removeClass('show');   
}

});
jQuery(document).on("click", '.btn-group button', function(){

if(jQuery("#products-table_wrapper .btn-group").hasClass('open'))
{
jQuery(this).parents('td').addClass('show');   
}
else{
  jQuery("#products-table_wrapper td.show").removeClass('show');  
}

});

jQuery("#view-o").click(function(){
	jQuery("#view-others").css("display", "block");
	jQuery("#view-details").css("display", "none");
});
jQuery("#view-d").click(function(){
	jQuery("#view-details").css("display", "block");
	jQuery("#view-others").css("display","none")
});



});
function scrollDown(){
    //find every div with class "mydiv" and apply the fix
    for(i=0;i<=jQuery('#products-table_wrapper .row .col-sm-12').length;i++){
        try{
            var g=jQuery('#products-table_wrapper .row .col-sm-12')[i];
            g.scrollTop+=1;
            g.scrollTop-=1;
        } catch(e){
            //eliminates errors when no scroll is needed
        }
    }
}
</script>
@endpush
