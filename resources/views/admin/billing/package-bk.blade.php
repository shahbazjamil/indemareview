@extends('layouts.app')

@push('head-script')

    <style>
        .text-danger {
            color: red !important;
        }

        h3 {
            line-height: 30px;
            font-size: 10px;
        }

        .display-small {
            display: block;
            width: fit-content;
        }

        .display-big {
            display: none;
        }

        .price {
            font-size: 1em;
        }

        body {
            background: #4f5467;
            font-family: Poppins, sans-serif;
            margin: 0;
            overflow-x: hidden;
            color: #686868;
            font-weight: 300;
            font-size: 5px;
            line-height: 1.42857143;
        }

        @media (min-width: 767px) {
            .display-small {
                display: none;
            }

            .display-big {
                display: block;
            }

            .price {
                font-size: 3em;
            }

            body {
                font-size: 14px;
            }
        }

        @media (min-width: 1200px) {
            h3 {
                line-height: 30px;
                font-size: 21px;
            }
        }

        .selected-plan, body .table > tbody > tr.active > th.selected-plan {
            background-color: #a6ebff5e !important;
            font-weight: 600;
        }
		.pricing_title {
    font-size: 20px;color: #010a44;letter-spacing:-0.01em;line-height:1.1;font-weight:bold;
    margin: 0px 0px 10px;font-family: "Space Grotesk Bold", -apple-system, BlinkMacSystemFont, Roboto, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
}.pricing_subtitle {
    margin-top: 5px;
    font-size: 13px;color:rgba(1,10,68,0.75);font-weight:normal;line-height:1.65;
	font-family: -apple-system, BlinkMacSystemFont, Roboto, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
}
.pricing_price_title {
    display: block;
    font-size: -webkit-calc(46px + 26 * ( (100vw - 576px) / 1024));
    font-size: calc(46px + 26 * ( (100vw - 576px) / 1024));
    color: #010a44;letter-spacing:-0.01em;line-height:1.1;font-weight:bold;
    margin: 0;
    position: relative;
    -webkit-transition: all cubic-bezier(0.4, 0, 0.2, 1) 0.4s;
    -o-transition: all cubic-bezier(0.4, 0, 0.2, 1) 0.4s;
    transition: all cubic-bezier(0.4, 0, 0.2, 1) 0.4s;
	font-family: "Space Grotesk Bold", -apple-system, BlinkMacSystemFont, Roboto, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
}
th:hover .pricing_price_title{color:#d90a2c}
.pricing_price_time {
    -webkit-border-radius: 4px;
    border-radius: 4px;
    padding: 0px 10px;
    height: 26px;
    line-height: 26px;
    display: inline-block;
    vertical-align: top;
    font-size: 14.5px !important;
    font-weight: 500;
    color: #010a44;margin-top:12px;
    background-color: rgba(136, 136, 136, 0.1);text-transform:none;
}
.pricing_price_time:hover{background:#d90a2c;color:#FFF;}
td{font-size:16px !important;text-align:left;font-family:-apple-system, BlinkMacSystemFont, Roboto, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";font-weight:normal;line-height:1.65 !important;border:none !important;}
.table .btn-success{
    color: #010a44 !important;
    padding: 14px 18px;
    border: 1px solid #17161A;
    font-family: "Space Grotesk Bold", -apple-system, BlinkMacSystemFont, Roboto, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    font-weight: 600;
    font-size: 16px !important;
}
.table .btn-success:hover, .table .btn-success:focus{background:#d90a2c;color:#FFF !important;border-color:#d90a2c}
.table th{background:none !important;}
.tab-switch{float:right;margin-right:50px;}
.tab-switch span{font-weight:bold;color:#010a44 !important;display:inline-block;vertical-align:middle;line-height:34px;}
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;margin:0;vertical-align:middle;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #010a44;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
    </style>
    
    <script src="https://js.chargebee.com/v2/chargebee.js" data-cb-site="indema" ></script>
@endpush

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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
			<div class="tab-switch">
			<span>MONTHLY</span>
			<label class="switch">
			  <input type="checkbox" id="check">
			  <span class="slider round"></span>
			</label>
			<span>ANNUALLY</span>
			</div>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')

@endpush


@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                <?php Session::forget('success');?>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                <?php Session::forget('error');?>
            @endif
            @if($stripeSettings->paypal_status == 'inactive'  && $stripeSettings->stripe_status == 'inactive'  && $stripeSettings->razorpay_status == 'deactive' &&  $stripeSettings->paystack_status == 'deactive')
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        {{__('messages.noPaymentGatewayEnabled')}}
                    </div>
                </div>
            @endif


            <div class="white-box ">
                <!--<h3>@lang('app.monthly') @lang('app.menu.packages')</h3>-->
                <div class="table-responsive table-responsive-froid monthly-div">
					<table class="table table-hover table-bordered text-center">
						<thead>
							<tr class="active">
								<th style="min-width:80px;">
									<h3 class="pricing_title">SINGLE</h3>
									<p class="subtitle pricing_subtitle">$385/Yr</p>
									<h2 class="pricing_price_title">$35</h2>
									<div class="pricing_price_time uppercase">Monthly</div>
								</th>
								<th style="min-width:80px;">
									<h3 class="pricing_title">GROUP</h3>
									<p class="subtitle pricing_subtitle">$550/Yr</p>
									<h2 class="pricing_price_title">$55</h2>
									<div class="pricing_price_time uppercase">Monthly</div>
								</th>
								<th style="min-width:80px;">
									<h3 class="pricing_title">HUB</h3>
									<p class="subtitle pricing_subtitle">$675/Yr</p>
									<h2 class="pricing_price_title">$75</h2>
									<div class="pricing_price_time uppercase">Monthly</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<p>For the interior designers who conquer their projects themselves.</p>
								</td>
								<td>
									<p>For small teams who just need a bit more organization in their lives.</p>
								</td>
								<td>
									<p>For the larger teams who need all the bells and whistles.</p>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Users: </b>1</span>
								</td>
								<td>
									<span class="title"><b>Users: </b>3</span>
								</td>
								<td>
									<span class="title"><b>Users: </b>Unlimited</span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Projects:</b> Unlimited</span>
								</td>
								<td>
									<span class="title"><b>Projects:</b> Unlimited</span>
								</td>
								<td>
									<span class="title"><b>Projects:</b> Unlimited</span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									
								</td>
								<td>
									<span class="title"><b>Team Members</b></span>
								</td>
								<td>
									<span class="title"><b>Team Members</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
								</td>
								<td>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
								</td>
								<td>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
								</td>
								<td>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
								</td>
								<td>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
								</td>
								<td>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
								</td>
								<td>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Manage Products</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Products</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Products</b></span>	
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
								</td>
								<td>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
								</td>
								<td>
									<span class="title"><b>Moodboards (Coming soon)</b></span>	
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Reporting</b></span>
								</td>
								<td>
									<span class="title"><b>Reporting</b></span>
								</td>
								<td>
									<span class="title"><b>Reporting</b></span>	
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Zoom + QB Integrations</b></span>
								</td>
								<td>
									<span class="title"><b>Zoom + QB Integrations</b></span>
								</td>
								<td>
									<span class="title"><b>Zoom + QB Integrations</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Support: Email</b></span>
								</td>
								<td>
									<span class="title"><b>Support: Email, Live Chat</b></span>
								</td>
								<td>
									<span class="title"><b>Support: Email, Live Chat</b></span>
								</td>								
							</tr>
							<tr>

								@foreach($packages as $package)
									<td>
										@if(round($package->monthly_price) > 0)
											@if(!($package->id == $company->package->id && $company->package_type == 'monthly')  && ($stripeSettings->paypal_status == 'active' || $stripeSettings->stripe_status == 'active' || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                                                                        <button style="display:none;" type="button" data-package-id="{{ $package->id }}"
														data-package-type="monthly"
														class="btn btn-success waves-effect waves-light selectPackage"
														title="Choose Plan"><i class="icon-anchor display-small"></i><span
															class="display-big">@lang('modules.billing.choosePlan')</span>
												</button>
<a href="javascript:void(0)" class="btn btn-success waves-effect waves-light" data-cb-type="checkout" data-cb-plan-id="{{ $package->chargebee_monthly_plan_id }}" >Select</a>
											@endif
										@endif
									</td>
								@endforeach
							</tr>
						</tbody>
					</table>
                    <!--<table class="table table-hover table-bordered text-center">
                        <thead>
                        <tr class="active">
                            <th style="background:#fff !important; min-width:80px;">
								<h3 class="pricing_title">LITE</h3>
								<p class="subtitle pricing_subtitle">Monthly Packages</p>
								<h2 class="pricing_price_title">$0</h2>
								<div class="pricing_price_time uppercase">Monthly Packages</div>
							</th>
                            @foreach($packages as $package)
                                <!--<th style="@if(($package->id == $company->package->id && $company->package_type == 'monthly')) @endif">
                                 <th style="background:#FFF !important;">   
                                        <h3 class="pricing_title">{{ucfirst($package->name)}}</h3>
										<p class="subtitle pricing_subtitle">Monthly Packages</p>
										<h2 class="pricing_price_title">{{ currency_position(round($package->monthly_price),$package->currency->currency_symbol) }}</h2>
										<div class="pricing_price_time uppercase">Monthly Packages</div>
                                    
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <!--<tr>
                            <td><br>@lang('app.price')</td>
                            @foreach($packages as $package)
                                <td class="@if(($package->id == $company->package->id && $company->package_type == 'monthly')) selected-plan @endif">
                                    <h3 class="panel-title price ">{{ currency_position(round($package->monthly_price),$package->currency->currency_symbol) }}</h3>
                                </td>
                            @endforeach
                        </tr>--><!--

                        <tr>
                            <td>@lang('app.menu.employees')</td>
                            @foreach($packages as $package)
                                <td class="@if(($package->id == $company->package->id && $company->package_type == 'monthly')) selected-plan @endif">{{ $package->max_employees }} @lang('modules.projects.members')</td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>@lang('app.menu.fileStorage')</td>
                            @foreach($packages as $package)
                                @if($package->max_storage_size == -1)
                                    <td class="@if(($package->id == $company->package->id && $company->package_type == 'monthly')) selected-plan @endif">@lang('app.unlimited')</td>
                                @else
                                    <td class="@if(($package->id == $company->package->id && $company->package_type == 'monthly')) selected-plan @endif">{{ $package->max_storage_size }} {{ strtoupper($package->storage_unit) }}</td>
                                @endif
                            @endforeach
                        </tr>

                        <tr>
                            @php
                                $moduleArray = [];
                                foreach($modulesData as $module) {
                                    $moduleArray[$module->module_name] = [];
                                }
                            @endphp

                            @foreach($packages as $package)
                                @foreach((array)json_decode($package->module_in_package) as $MIP)
                                    @if (array_key_exists($MIP, $moduleArray))
                                        @php $moduleArray[$MIP][] = strtoupper(trim($package->name)); @endphp
                                    @else
                                        @php $moduleArray[$MIP] = [strtoupper(trim($package->name))]; @endphp
                                    @endif
                                @endforeach
                            @endforeach
                        </tr>

                        @foreach($moduleArray as $key => $module)
                            <tr>
                                <td>{{ ucfirst($key) }}</td>
                                @foreach($packages as $package)
                                    @php $available = in_array(strtoupper(trim($package->name)), $module); @endphp
                                    <td class="@if(($package->id == $company->package->id && $company->package_type == 'monthly')) selected-plan @endif">
                                        <i class="fa {{ $available ? 'fa-check text-megna' : 'fa-times text-danger'}} fa-lg"></i>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>

                            @foreach($packages as $package)
                                <td>
                                    @if(round($package->monthly_price) > 0)
                                        @if(!($package->id == $company->package->id && $company->package_type == 'monthly')  && ($stripeSettings->paypal_status == 'active' || $stripeSettings->stripe_status == 'active' || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                            <button type="button" data-package-id="{{ $package->id }}"
                                                    data-package-type="monthly"
                                                    class="btn btn-success waves-effect waves-light selectPackage"
                                                    title="Choose Plan"><i class="icon-anchor display-small"></i><span
                                                        class="display-big">@lang('modules.billing.choosePlan')</span>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>-->
                </div>

                <!--<h1 class="m-t-20">@lang('app.annual') @lang('app.menu.packages')</h1>-->
                <div class="table-responsive table-responsive-froid annually-div" style="display:none">
				<table class="table table-hover table-bordered text-center">
						<thead>
							<tr class="active">
								<th style=" min-width:80px;">
									<h3 class="pricing_title">SINGLE</h3>
									<p class="subtitle pricing_subtitle">$35/mon</p>
									<h2 class="pricing_price_title">$385</h2>
									<div class="pricing_price_time uppercase">Annually</div>
								</th>
								<th style=" min-width:80px;">
									<h3 class="pricing_title">GROUP</h3>
									<p class="subtitle pricing_subtitle">$55/mon</p>
									<h2 class="pricing_price_title">$550</h2>
									<div class="pricing_price_time uppercase">Annually</div>
								</th>
								<th style=" min-width:80px;">
									<h3 class="pricing_title">HUB</h3>
									<p class="subtitle pricing_subtitle">$75/mon</p>
									<h2 class="pricing_price_title">$675</h2>
									<div class="pricing_price_time uppercase">Annually</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<p>For the interior designers who conquer their projects themselves.</p>
								</td>
								<td>
									<p>For small teams who just need a bit more organization in their lives.</p>
								</td>
								<td>
									<p>For the larger teams who need all the bells and whistles.</p>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Users: </b>1</span>
								</td>
								<td>
									<span class="title"><b>Users: </b>3</span>
								</td>
								<td>
									<span class="title"><b>Users: </b>Unlimited</span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Projects:</b> Unlimited</span>
								</td>
								<td>
									<span class="title"><b>Projects:</b> Unlimited</span>
								</td>
								<td>
									<span class="title"><b>Projects:</b> Unlimited</span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									
								</td>
								<td>
									<span class="title"><b>Team Members</b></span>
								</td>
								<td>
									<span class="title"><b>Team Members</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
								</td>
								<td>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
								</td>
								<td>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
								</td>
								<td>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
								</td>
								<td>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
								</td>
								<td>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
								</td>
								<td>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Manage Products</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Products</b></span>
								</td>
								<td>
									<span class="title"><b>Manage Products</b></span>	
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
								</td>
								<td>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
								</td>
								<td>
									<span class="title"><b>Moodboards (Coming soon)</b></span>	
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Reporting</b></span>
								</td>
								<td>
									<span class="title"><b>Reporting</b></span>
								</td>
								<td>
									<span class="title"><b>Reporting</b></span>	
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Zoom + QB Integrations</b></span>
								</td>
								<td>
									<span class="title"><b>Zoom + QB Integrations</b></span>
								</td>
								<td>
									<span class="title"><b>Zoom + QB Integrations</b></span>
								</td>								
							</tr>
							<tr>
								<td>
									<span class="title"><b>Support: Email</b></span>
								</td>
								<td>
									<span class="title"><b>Support: Email, Live Chat</b></span>
								</td>
								<td>
									<span class="title"><b>Support: Email, Live Chat</b></span>
								</td>								
							</tr>
							<tr>
								@foreach($packages as $package)
									<td>
										@if(round($package->annual_price) > 0)
											@if(!($package->id == $company->package->id && $company->package_type == 'annual')
											&& ($stripeSettings->paypal_status == 'active'  || $stripeSettings->stripe_status == 'active'  || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                                                                        <button style="display:none;" type="button" data-package-id="{{ $package->id }}"
														data-package-type="annual"
														class="btn btn-success waves-effect waves-light selectPackage"
														title="Choose Plan"><i class="icon-anchor display-small"></i><span
															class="display-big">@lang('modules.billing.choosePlan')</span>
												</button>
                                                                                                <a href="javascript:void(0)" class="btn btn-success waves-effect waves-light" data-cb-type="checkout" data-cb-plan-id="{{ $package->chargebee_annual_plan_id }}" >Select</a>
											@endif
										@endif
									</td>
								@endforeach
							</tr>
						</tbody>
					</table>
                   <!--<table class="table table-hover table-bordered text-center">
                        <thead>
                        <tr class="active">
                            <th style="background:#fff !important; min-width:80px;">
								<h3 class="pricing_title">LITE</h3>
								<p class="subtitle pricing_subtitle">Annual Packages</p>
								<h2 class="pricing_price_title">$0</h2>
								<div class="pricing_price_time uppercase">Annual Packages</div>
							</th>
                            @foreach($packages as $package)
                                <th style="@if(($package->id == $company->package->id && $company->package_type == 'annual'))  @endif;background:#FFF !important;"">
                                    <h3 class="pricing_title">{{ucfirst($package->name)}}</h3>
									<p class="subtitle pricing_subtitle">Annual Packages</p>
									<h2 class="pricing_price_title">{{ currency_position(round($package->annual_price),$package->currency->currency_symbol) }}</h2>
									<div class="pricing_price_time uppercase">Annual Packages</div>
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <!--<tr>
                            <td><br>@lang('app.price')</td>
                            @foreach($packages as $package)
                                <td class="@if(($package->id == $company->package->id && $company->package_type == 'annual')) selected-plan @endif">
                                    <h3 class="panel-title price"> {{ currency_position(round($package->annual_price),$package->currency->currency_symbol) }}</h3>
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>@lang('app.menu.employees')</td>
                            @foreach($packages as $package)
                                <td class="@if(($package->id == $company->package->id && $company->package_type == 'annual')) selected-plan @endif">{{ $package->max_employees }} @lang('modules.projects.members')</td>
                            @endforeach
                        </tr>


                        <tr>
                            <td>@lang('app.menu.fileStorage')</td>
                            @foreach($packages as $package)
                                @if($package->max_storage_size == -1)
                                    <td class="@if(($package->id == $company->package->id && $company->package_type == 'annual')) selected-plan @endif">@lang('app.unlimited')</td>
                                @else
                                    <td class="@if(($package->id == $company->package->id && $company->package_type == 'annual')) selected-plan @endif">{{ $package->max_storage_size }} {{ strtoupper($package->storage_unit) }}</td>
                                @endif
                           @endforeach
                        </tr>

                        @foreach($moduleArray as $key => $module)
                            <tr>
                                <td>{{ ucfirst($key) }}</td>
                                @foreach($packages as $package)
                                    @php $available = in_array(strtoupper(trim($package->name)), $module); @endphp
                                    <td class="@if(($package->id == $company->package->id && $company->package_type == 'annual')) selected-plan @endif">
                                        <i class="fa {{ $available ? 'fa-check text-megna' : 'fa-times text-danger'}} fa-lg"></i>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>

                            @foreach($packages as $package)
                                <td>
                                    @if(round($package->annual_price) > 0)
                                        @if(!($package->id == $company->package->id && $company->package_type == 'annual')
                                        && ($stripeSettings->paypal_status == 'active'  || $stripeSettings->stripe_status == 'active'  || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                            <button type="button" data-package-id="{{ $package->id }}"
                                                    data-package-type="annual"
                                                    class="btn btn-success waves-effect waves-light selectPackage"
                                                    title="Choose Plan"><i class="icon-anchor display-small"></i><span
                                                        class="display-big">@lang('modules.billing.choosePlan')</span>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>-->
                </div>
            </div>
        </div>
    </div>
	<div class="row ingration-row">
        <div class="col-md-12 bg-title">
			<div class="row m-0 monthly-div">			
				<h4>Account Add-ons</h4>
				<p> </p>
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Bookings</h5>
						<p>
							A great alternative to Calendly! Add a calendar booking page to your website for your clients to book sessions with you! Payment support coming soon.
						</p>
<!--                                                <a href="javascript::void(0)" class="integration-btn btn-danger" >Add</a>-->
						<a href="javascript::void(0)" class="integration-btn btn-danger" data-cb-type="checkout" data-cb-plan-id="bookings" >Add</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Social Media Management</h5>
						<p>
							Schedule and plan all of your social media from within indema. Post to Facebook, Instagram, Twitter, and LinkedIn.
						</p>
                                                @if($company->is_social == 1)
                                                    <a href="javascript::void(0)" class="integration-btn btn-danger" >Added</a>
                                                @else
                                                    <a href="javascript::void(0)" class="integration-btn btn-danger" data-toggle="modal" data-target="#sm-management">Add</a>
                                                @endif
						
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Showroom Management + POS</h5>
						<p>
							Manage your showroom inventory, sales, Sales Associate KPI’s and commission, and more! Includes a POS system for sales. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Order Tracking + Procurement</h5>
						<p>
							Automatically track all of your orders for material and furnishings. Connects seamlessly to multiple freight companies and delivery company systems for live tracking. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Home Staging</h5>
						<p>
							Assign furniture and accessories to homes that you are staging. Calculate Return on Investments, Sell your products you no longer need, and track your repairs and costs. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Inventory + Asset Management</h5>
						<p>
							Have inventory you need to manage? Track all of your assets, and inventory so you always know where everything is. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Specifications</h5>
						<p>
							Specifications for projects are super important. Create fully customizable specification sheets, invite users to manage or view, and get approvals, requests for quotes and more! 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Payroll</h5>
						<p>
							Manage your employee payroll. Submit direct deposit, generate pay statements, and process weekly, bi-weekly or semi-annnual payments. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				
			</div><!--end of row-->
                        
                        <div class="row m-0 annually-div" style="display:none">			
				<h4>Account Add-ons</h4>
				<p> </p>
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Bookings</h5>
						<p>
							A great alternative to Calendly! Add a calendar booking page to your website for your clients to book sessions with you! Payment support coming soon.
						</p>
<!--                                                <a href="javascript::void(0)" class="integration-btn btn-danger" >Add</a>-->
                                                <a href="javascript::void(0)" class="integration-btn btn-danger" data-cb-type="checkout" data-cb-plan-id="bookings-annually" >Add</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Social Media Management</h5>
						<p>
							Schedule and plan all of your social media from within indema. Post to Facebook, Instagram, Twitter, and LinkedIn.
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Showroom Management + POS</h5>
						<p>
							Manage your showroom inventory, sales, Sales Associate KPI’s and commission, and more! Includes a POS system for sales. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Order Tracking + Procurement</h5>
						<p>
							Automatically track all of your orders for material and furnishings. Connects seamlessly to multiple freight companies and delivery company systems for live tracking. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Home Staging</h5>
						<p>
							Assign furniture and accessories to homes that you are staging. Calculate Return on Investments, Sell your products you no longer need, and track your repairs and costs. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Inventory + Asset Management</h5>
						<p>
							Have inventory you need to manage? Track all of your assets, and inventory so you always know where everything is. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Specifications</h5>
						<p>
							Specifications for projects are super important. Create fully customizable specification sheets, invite users to manage or view, and get approvals, requests for quotes and more! 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="border p-3">
						<h5>Payroll</h5>
						<p>
							Manage your employee payroll. Submit direct deposit, generate pay statements, and process weekly, bi-weekly or semi-annnual payments. 
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-3-->
				
			</div><!--end of row-->
                        
		</div><!--end of col-12-->
	</div><!--end of row-->
        <div class="modal fade accounts-modal" id="sm-management" tabindex="-1" role="dialog" aria-labelledby="sm-management">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="sm-management-heading">Social Media Management</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row flex-row">
                            <div class="col-sm-4 col-xs-12 text-center">
                                <div class="border">
                                    <h2>Free<span>&nbsp;</span></h2>
                                    <div>
                                        <span>1 Social Profile per network</span>
                                        <span>15 posts per month</span>
                                        <span>Post Planner</span>
                                        <span>File Manager (5GB Limit)</span>
                                        <span>Up to 5 Streams</span>
                                    </div>
                                    <div class="sharing">
                                        <a href="javascript:void(0)"><i class="fa fa-pinterest-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-twitter-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-instagram"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-facebook-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-linkedin-square"></i></a>
                                    </div>
                                    <a href="javascript::void(0)" class="integration-btn" data-cb-type="checkout" data-cb-plan-id="social-free" >Select This Plan</a>
                                </div><!--end of border-->
                            </div>
                            <div class="col-sm-4 col-xs-12 text-center">
                                <div class="border">
                                    <h2>Standard<span>$15/Mo</span></h2>
                                    <div>
                                        <span>3 Social Profile per network</span>
                                        <span>150 posts per month</span>
                                        <span>Up to 3 Team Members</span>
                                        <span>Analytics (FB + IG)</span>
                                        <span>Post Planner</span>
                                        <span>File Manager (10GB Limit)</span>
                                        <span>Up to 10 Streams</span>
                                    </div>
                                    <div class="sharing">
                                        <a href="javascript:void(0)"><i class="fa fa-pinterest-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-twitter-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-instagram"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-facebook-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-linkedin-square"></i></a>
                                    </div>
                                    <a href="javascript::void(0)" class="integration-btn" data-cb-type="checkout" data-cb-plan-id="social-starter">Select This Plan</a>
                                </div><!--end of border-->
                            </div>
                            <div class="col-sm-4 col-xs-12 text-center">
                                <div class="border">
                                    <h2>PRO<span>$25/Mo</span></h2>
                                    <div>
                                        <span>5 Social Profile per network</span>
                                        <span>Unlimited posts per month</span>
                                        <span>Up to 5 Team Members</span>
                                        <span>Analytics (FB + IG)</span>
                                        <span>Post Planner</span>
                                        <span>File Manager (50GB Limit)</span>
                                        <span>Unlimited Streams</span>
                                    </div>
                                    <div class="sharing">
                                        <a href="javascript:void(0)"><i class="fa fa-pinterest-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-twitter-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-instagram"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-facebook-square"></i></a>
                                        <a href="javascript:void(0)"><i class="fa fa-linkedin-square"></i></a>
                                    </div>
                                    <a href="javascript::void(0)" class="integration-btn" data-cb-type="checkout" data-cb-plan-id="social-pro">Select This Plan</a>
                                </div><!--end of border-->
                            </div>
                        </div><!--end of row-->
                    </div>
                </div>
            </div>
        </div>
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="package-select-form" role="dialog" aria-labelledby="myModalLabel"
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
    {{--Ajax Modal Ends--}}

    {{--Ajax Modal--}}
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
    {{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        // $(document).ready(function() {
        // show when page load
        @if(\Session::has('message'))
        toastr.success({{  \Session::get('message') }});
        @endif
        // });

        $('body').on('click', '.unsubscription', function () {
            var type = $(this).data('type');
            swal({
                title: "Are you sure?",
                text: "Do you want to unsubscribe this plan!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Unsubscribe it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.billing.unsubscribe') }}";
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'POST', 'type': type},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        // Show Create Holiday Modal
        $('body').on('click', '.selectPackage', function () {
            var id = $(this).data('package-id');
            var type = $(this).data('package-type');
            var url = "{{ route('admin.billing.select-package',':id') }}?type=" + type;
            url = url.replace(':id', id);
            $.ajaxModal('#package-select-form', url);
        });
		$('#check').change(function() {

    var $check = $(this),
        $div = $check.parent();

    if ($check.prop('checked')) {

        $(".annually-div").css('display','block');
        $(".monthly-div").css('display','none');

    } else {

        
        $(".annually-div").css('display','none');
        $(".monthly-div").css('display','block');

    }

});
    </script>
@endpush
