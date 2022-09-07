@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}
                <span @if($totalRecords == 0) style="display: none;"  @endif class="text-info- b-l p-l-10 m-l-5">{{ $totalProducts }}</span> <span @if($totalRecords == 0) style="display: none;"  @endif class="font-12 text-muted m-l-5">@lang('app.total') @lang('app.menu.products')</span>
            </h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div @if($totalRecords == 0) style="display: none;"  @endif class="border-bottom col-xs-12 p-t-10 p-b-10">
            <a href="{{ route('admin.products.edit', 0) }}" class="btn btn-outline btn-success btn-sm">+ @lang('app.addNew') @lang('app.menu.products')</a>
            <a href="{{ route('admin.codeTypes.index') }}" target="_blank"  class="btn btn-outline btn-success btn-sm">+ Add Location Codes </a>
            <a href="{{ route('admin.salescategoryTypes.index') }}" target="_blank"  class="btn btn-outline btn-success btn-sm">+ Add Sales Categories </a>
            
        <div class="btn-group dropdown doverlay pull-right">
                <button aria-expanded="true" data-toggle="dropdown" class="dt-button btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md" type="button"><i class="fa fa-upload"></i> &nbsp;Import <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">
                    <li>
                        <a href="{{ route('admin.products.downloadTemplate') }}" target="_blank">
                            <i class="fa fa-download"></i> &nbsp;Download Template
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="$('#importCSVModal').modal('show')"><i class="fa fa-file-excel-o"></i> &nbsp;Import CSV</a>
                    </li>
                </ul>
        </div>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
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
				.d-i-new .col-auto.status select{background:#F7EEEF;color:#D04E55;border:1px solid #D04E55 !important;border-radius:7px}
				.d-i-new .status.approved{color:#37EA7C;text-transform:uppercase;font-size:16px}
				.d-i-new b.notes{
					white-space:normal;
				}
				.col-auto>div, .col>div{margin:5px 0}
				.col-auto.img img {
					margin: 15px 0;
				}
				.col-auto.name {
					max-width: 250px;
				}
				.col-auto.price {
					display: flex;
					flex-wrap: wrap;
					max-width: 280px;
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
		<div class="col-md-12 indema-web-clipper">
			<div class="row border-bottom m-b-10">
				<div class="col-md-4">
					<h2>Indema Web Clipper</h2>
					<a href="javascript:if(document.readyState==='complete'){var element = document.createElement('input');element.type = 'hidden';element.value = '{{$uuid}}';element.id = 'idemia_uuid'; with(document)(body.appendChild(createElement('script')).src='https://app.indema.co/js/parsing/plugins.js', body.appendChild(element))}else{alert('Please wait until the page loads.')}" class="p-button">Add to Indema</a>
				</div><!--end of col-md-3-->
				<div class="col-md-8 b-l">
					<h2>How To Use</h2>
					<ol type="1">
						<li>Drag and drop the button to your left to your bookmarks.</li>
						<li>Make sure you stay logged into indema, and go to a website you want to clip from.</li>
						<li>Click on the bookmarked button and start clipping the product! All clipped items will show below when you refresh.</li>
					</ol>
				</div><!--end of col-9-->
			</div><!--end of row-->
		</div><!--end of col-12-->
		<div class="col-md-12 p-b-20 p-t-10 m-b-20 border-bottom">
					<a href="javascript:void(0)" class="p-button" id="view-d">View Details</a>
					<a href="javascript:void(0)" class="p-button m-l-10"id="view-o">View Other</a>
		</div><!--end of col-12-->
		<div class="col-md-12 d-i-new" id="view-others" style="display:none">
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto price">					
					<div><b contenteditable="true">$123.00</b> Per Unit Price</div>
					<div><b contenteditable="true">6</b> Quantity</div>
					<div><b contenteditable="true">$123.00</b> Markup $</div>
					<div><b contenteditable="true">15</b> Markup %</div>
					<div><b contenteditable="true">$123.00</b> Markup % Total</div>
					<div><b contenteditable="true">$123.00</b> Freight</div>
					<div><b contenteditable="true">$123.00</b> MSRP</div>
					<div><b contenteditable="true">$123.00</b> Total Sale</div>
				</div>
				<div class="col-auto date">					
					<div><input type="text" class="datepicker" value="05/24/2022"> PO Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> CFA Approved</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Act Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> RFQ Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Ordered</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Received Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Quote Received</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Install Date</div>
				</div>
				<div class="col-auto received">					
					<div><input type="text" class="datepicker" value="05/24/2022"> Received</div>
					<div><b contenteditable="true">Jack. B</b> Received By</div>
					<div><b contenteditable="true">Warehouse B</b> Location</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto price">					
					<div><b contenteditable="true">$123.00</b> Per Unit Price</div>
					<div><b contenteditable="true">6</b> Quantity</div>
					<div><b contenteditable="true">$123.00</b> Markup $</div>
					<div><b contenteditable="true">15</b> Markup %</div>
					<div><b contenteditable="true">$123.00</b> Markup % Total</div>
					<div><b contenteditable="true">$123.00</b> Freight</div>
					<div><b contenteditable="true">$123.00</b> MSRP</div>
					<div><b contenteditable="true">$123.00</b> Total Sale</div>
				</div>
				<div class="col-auto date">					
					<div><input type="text" class="datepicker" value="05/24/2022"> PO Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> CFA Approved</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Act Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> RFQ Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Ordered</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Received Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Quote Received</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Install Date</div>
				</div>
				<div class="col-auto received">					
					<div><input type="text" class="datepicker" value="05/24/2022"> Received</div>
					<div><b contenteditable="true">Jack. B</b> Received By</div>
					<div><b contenteditable="true">Warehouse B</b> Location</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto price">					
					<div><b contenteditable="true">$123.00</b> Per Unit Price</div>
					<div><b contenteditable="true">6</b> Quantity</div>
					<div><b contenteditable="true">$123.00</b> Markup $</div>
					<div><b contenteditable="true">15</b> Markup %</div>
					<div><b contenteditable="true">$123.00</b> Markup % Total</div>
					<div><b contenteditable="true">$123.00</b> Freight</div>
					<div><b contenteditable="true">$123.00</b> MSRP</div>
					<div><b contenteditable="true">$123.00</b> Total Sale</div>
				</div>
				<div class="col-auto date">					
					<div><input type="text" class="datepicker" value="05/24/2022"> PO Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> CFA Approved</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Act Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> RFQ Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Ordered</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Received Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Quote Received</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Install Date</div>
				</div>
				<div class="col-auto received">					
					<div><input type="text" class="datepicker" value="05/24/2022"> Received</div>
					<div><b contenteditable="true">Jack. B</b> Received By</div>
					<div><b contenteditable="true">Warehouse B</b> Location</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto price">					
					<div><b contenteditable="true">$123.00</b> Per Unit Price</div>
					<div><b contenteditable="true">6</b> Quantity</div>
					<div><b contenteditable="true">$123.00</b> Markup $</div>
					<div><b contenteditable="true">15</b> Markup %</div>
					<div><b contenteditable="true">$123.00</b> Markup % Total</div>
					<div><b contenteditable="true">$123.00</b> Freight</div>
					<div><b contenteditable="true">$123.00</b> MSRP</div>
					<div><b contenteditable="true">$123.00</b> Total Sale</div>
				</div>
				<div class="col-auto date">					
					<div><input type="text" class="datepicker" value="05/24/2022"> PO Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> CFA Approved</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Act Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> RFQ Sent</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Ordered</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Received Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Quote Received</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Ship Date</div>
					<div><input type="text" class="datepicker" value="05/24/2022"> Est Install Date</div>
				</div>
				<div class="col-auto received">					
					<div><input type="text" class="datepicker" value="05/24/2022"> Received</div>
					<div><b contenteditable="true">Jack. B</b> Received By</div>
					<div><b contenteditable="true">Warehouse B</b> Location</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
		</div><!--end of col-12-->
		
		<div class="col-md-12 d-i-new" id="view-details">
                    <h3 style="display:none;">BedRoom 2 <span> Total Sale:$13,334</span></h3>
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto status">					
					<div><select><option value="deciding">Deciding</option></select> Product Status</div>
					<div><b contenteditable="true" class="status approved">Approved</b> Client Status</div>
				</div>
				<div class="col-auto other">					
					<div><b contenteditable="true">Slawson</b> Project</div>
					<div><select><option value="deciding">Vendor</option></select> Vendor</div>
					<div><b contenteditable="true">Metal</b> Material</div>
					<div><b contenteditable="true">Approved</b> Acknowledgement</div>
					<div><b contenteditable="true">Furniture</b> Product Category</div>
					<div><b contenteditable="true">Menu-Name</b> Manufacturer</div>
					<div><b contenteditable="true">123456</b> Product Number</div>
					<div><b contenteditable="true">Red</b> Finish/Color</div>
					<div><b contenteditable="true">Bedroom 2</b> Location Code</div> 
					<div><b contenteditable="true">123 x 123</b> Dimensions</div>
					<div><b contenteditable="true">Bedroom 2</b> Tracking Number</div>
					<div><b contenteditable="true">2-6 Weeks</b> Lead Time</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
			<h3 style="display:none;">BedRoom 2 <span> Total Sale:$13,334</span></h3>
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto status">					
					<div><select><option value="deciding">Deciding</option></select> Product Status</div>
					<div><b contenteditable="true" class="status approved">Approved</b> Client Status</div>
				</div>
				<div class="col-auto other">					
					<div><b contenteditable="true">Slawson</b> Project</div>
					<div><select><option value="deciding">Vendor</option></select> Vendor</div>
					<div><b contenteditable="true">Metal</b> Material</div>
					<div><b contenteditable="true">Approved</b> Acknowledgement</div>
					<div><b contenteditable="true">Furniture</b> Product Category</div>
					<div><b contenteditable="true">Menu-Name</b> Manufacturer </div>
					<div><b contenteditable="true">123456</b> Product Number</div>
					<div><b contenteditable="true">Red</b> Finish/Color</div>
					<div><b contenteditable="true">Bedroom 2</b> Location Code</div> 
					<div><b contenteditable="true">123 x 123</b> Dimensions</div>
					<div><b contenteditable="true">Bedroom 2</b> Tracking Number</div>
					<div><b contenteditable="true">2-6 Weeks</b> Lead Time</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
			<h3 style="display:none;">BedRoom 2 <span> Total Sale:$13,334</span></h3>
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto status">					
					<div><select><option value="deciding">Deciding</option></select> Product Status</div>
					<div><b contenteditable="true" class="status approved">Approved</b> Client Status</div>
				</div>
				<div class="col-auto other">					
					<div><b contenteditable="true">Slawson</b> Project</div>
					<div><select><option value="deciding">Vendor</option></select> Vendor</div>
					<div><b contenteditable="true">Metal</b> Material</div>
					<div><b contenteditable="true">Approved</b> Acknowledgement</div>
					<div><b contenteditable="true">Furniture</b> Product Category</div>
					<div><b contenteditable="true">Menu-Name</b> Manufacturer</div>
					<div><b contenteditable="true">123456</b> Product Number</div>
					<div><b contenteditable="true">Red</b> Finish/Color</div>
					<div><b contenteditable="true">Bedroom 2</b> Location Code</div> 
					<div><b contenteditable="true">123 x 123</b> Dimensions</div>
					<div><b contenteditable="true">Bedroom 2</b> Tracking Number</div>
					<div><b contenteditable="true">2-6 Weeks</b> Lead Time</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
			<h3 style="display:none;">BedRoom 2 <span> Total Sale:$13,334</span></h3>
			<div class="d-flex border">
				<div class="col-auto img">
					<img src="https://stagin.indema.co/user-uploads/products/3208/16367439731982245617.jpg" alt="product" width="75" height="75">
				</div>
				<div class="col-auto name">					
					<div><b contenteditable="true">Calacatta Gold Quartz</b> Product Name</div>
					<div><b contenteditable="true">https://www.silestoneusa.com/color/et-calacatta-gold/</b> Product Url</div>
					<div><b contenteditable="true">LIV 0397</b> Spec Number</div>
				</div>
				<div class="col-auto status">					
					<div><select><option value="deciding">Deciding</option></select> Product Status</div>
					<div><b contenteditable="true" class="status approved">Approved</b> Client Status</div>
				</div>
				<div class="col-auto other">					
					<div><b contenteditable="true">Slawson</b> Project</div>
					<div><select><option value="deciding">Vendor</option></select> Vendor</div>
					<div><b contenteditable="true">Metal</b> Material</div>
					<div><b contenteditable="true">Approved</b> Acknowledgement</div>
					<div><b contenteditable="true">Furniture</b> Product Category</div>
					<div><b contenteditable="true">Menu-Name</b> Manufacturer</div>
					<div><b contenteditable="true">123456</b> Product Number</div>
					<div><b contenteditable="true">Red</b> Finish/Color</div>
					<div><b contenteditable="true">Bedroom 2</b> Location Code</div> 
					<div><b contenteditable="true">123 x 123</b> Dimensions</div>
					<div><b contenteditable="true">Bedroom 2</b> Tracking Number</div>
					<div><b contenteditable="true">2-6 Weeks</b> Lead Time</div>
				</div>
				<div class="col note">					
					<div>Notes<b contenteditable="true" class="notes">Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet Lorem Ipsum dolore sit amet </b></div>
				</div>
				
					<div class="col-auto last-action">
						<div class="btn-group dropdown m-r-10">
						<button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
						<ul role="menu" class="dropdown-menu pull-right">
						  <li><a href="https://stagin.indema.co/admin/products/8934/edit"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
						<li><a href="https://stagin.indema.co/admin/products/8934/edit?copy=1"><i class="fa fa-copy" aria-hidden="true"></i> Copy</a></li>
						  <li><a href="javascript:;" data-user-id="8934" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
						  <li><a href="https://stagin.indema.co/admin/products/download/8934"><i class="fa fa-file-pdf-o"></i> Download</a></li> <li><a href="javascript:" data-user-id="8934" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li></ul> </div>
					<img src="https://stagin.indema.co/img/flag-icn.jpg" alt="product" width="50" height="50">
					</div>
			</div><!--end of d-flex-->
		</div><!--end of col-12-->
        <div class="col-md-12 d-none">
            <div class="white-box p-0">
                    @section('filter-section')
                    <div  class="row" id="ticket-filters">

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
                                <h5 >Project</h5>
                                <div class="form-group">
                                    <select name="salesCategory" id="salesCategory" class="form-control">
                                        <option value="all">Select Project</option>
                                        <option value="" ></option>
                                        <option value="">No Category Added</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5>Status</h5>
                                <div class="form-group">
                                    <select name="salesCategory" id="salesCategory" class="form-control">
                                        <option value="all">Select Status</option>
                                        <option value="" ></option>
                                        <option value="">No Category Added</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 m-b-20">
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items without a order date. </label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with an order date.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items RFQ was sent.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items RFQ was NOT sent.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items Quote was received.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items Quote was NOT received.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where CFA was approved.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where CFA was NOT approved.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items that were ordered.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items that NOT ordered.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show all flagged items.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items without a estimated ship date.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with an estimated ship date.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items without ship date.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with a ship date.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with a estimated received date.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with a received date.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items with tracking number.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where client approved.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where client declined.</label>
								<label class="check-container d-block"><input id="" name="" type="checkbox" class="toggle-vis" /><span class="checkmark"></span> Show items where client did not approve/decline.</label>
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
                                    <button type="button" id="download-all-pdf" class="btn btn-success col-md-4 "><i class="fa fa-download"></i> <i class="fa fa-file-pdf-o"></i> @lang('app.download')</button>
                                    <button type="button" id="apply-filters" class="btn btn-success col-md-3 col-md-offset-1"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-3 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endsection
					<button class="btn btn-inverse btn-outline" id="mobile-filter-toggle-product" style="display:none;float:right"><i class="fa fa-sliders"></i></button>
                <div @if($totalRecords == 0) style="display: none;"  @endif class="table-responsive-">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    <div @if($totalRecords > 0) style="display: none;"  @endif class="row flex-row flex-wrap nolist-content flex-align-start">
		<div class="col-md-4"><img src="{{ asset('img/product-management.jpg') }}" class="img-responsive" alt="" /></div>
		<div class="col-md-8">
			<h1 class="page-title m-b-30">Product management</h1>
			<p class="m-b-30">Having all of your products in one place is nice. But also being able to assign those to a project, or manage it directly from the main table is even nicer. Send RFQ, and use our web clipper to easily import from the web!</p>
			<a href="{{ route('admin.products.edit', 0) }}" class="btn-black">+ @lang('app.addNew') @lang('app.menu.products')</a>
                        <a href="javascript:;" onclick="$('#video-modal').modal('show')" class="btn-black">See how it works <i class="fa fa-play"></i></a>
		</div><!--end of col-8-->
		<div class="col-md-12 text-right">
			Have Questions? <a href="mailto:support@indema.co">Contact Support</a>
		</div><!--end of col-12-->
	</div><!--end of row-->
    
    <div class="modal fade bs-modal-md in" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="video-modal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
				<div class="modal-header p-t-15 p-b-15 p-r-15">
					<h4 class="modal-title">Product management</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
                <div class="modal-body p-2"></div>
            </div>
        </div>
    </div>
    
    
    <div class="modal fade bs-modal-md in" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Import Leads</h4>
                </div>
                    <div class="modal-body">
                        <div class="portlet-body">
                        
                        <form method='post' id="importCSVFrm" action='{{ route('admin.products.import') }}' enctype='multipart/form-data' >
                            {{ csrf_field() }}
                            <div class="form-body">
                                <div class="row ">
                                    <div class="col-xs-12 m-b-10">
                                        <div class="form-group">
                                            <label class="col-xs-3 required">Select File</label>
                                            <div class="col-xs-9">
                                                <input required="" type="file" name="csv_file" id="csv_file" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void()"  type="button" class="btn btn-success" onclick="$('#importCSVFrm').submit()">Import</a>
                </div>
            </div>
        </div>
    </div>
    
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

    {!! $dataTable->scripts() !!}
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
                                    $.unblockUI();
                                    LaravelDataTables["products-table"].draw();
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
            var table =  window.LaravelDataTables["products-table"];
            
            // Get the column API object
            var column = table.column( $(this).attr('data-column') );

            // Toggle the visibility
            column.visible( ! column.visible() );
            //table.column(0).visible(false); 
        } );
        
        
            
            
        });
        
        function loadTable (){
            window.LaravelDataTables["products-table"].draw();
//                setTimeout(function(){  
//                loadTabledit();
//                }, 3000);
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
         
        url = url+'?locationCode='+locationCode+'&salesCategory='+salesCategory;
        if($('#name_fl').is(':checked')){ url = url+'&name_fl=1'; }
        
        if($('#spec_num_fl').is(':checked')){ url = url+'&spec_num_fl=1'; }
        if($('#qty_fl').is(':checked')){ url = url+'&qty_fl=1'; }
        
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
        
        
     $(function() {
         
        $.ajaxSetup({
            headers:{
              'X-CSRF-Token' : "{{ csrf_token() }}"
            }
        });
        
//       setTimeout(function(){  
//        loadTabledit();
//        }, 3000);

    });
    
   $('.datepicker').datepicker();
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
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, order : order, project_id : 0},
                success: function (response) {
                    if (response.status == "success") {
                        //$.unblockUI();
                        //LaravelDataTables["products-table"].draw();
                    }
                }
            });
        }
        
   
        
        
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
	setTimeout(function(){  jQuery("#mobile-filter-toggle-product").insertBefore("#products-table_wrapper .dt-buttons"); jQuery(".btn-group.dropdown.doverlay").insertBefore("#products-table_wrapper .dt-buttons");}, 2000);
	
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

     $('#video-modal').on('show.bs.modal', function (e) {
      var idVideo = $(e.relatedTarget).data('id');
      $('#video-modal .modal-body').html('<div class="embed-responsive embed-responsive-16by9"><iframe width="560" height="315" src="https://www.youtube.com/embed/jnwHYWFyZCM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;" allowfullscreen></iframe></div>');
    });
    //on close remove
    $('#video-modal').on('hidden.bs.modal', function () {
       $('#video-modal .modal-body').empty();
    }); 
</script>
    
   
@endpush