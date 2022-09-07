@extends('layouts.app')

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
                <li><a href="{{ route('admin.clients.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.edit')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/image-picker/image-picker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/steps-form/steps.css') }}">
    
    
    
    <style>
        #sticky-note-toggle{display:none;}
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
    
<!--    <script src="https://js.chargebee.com/v2/chargebee.js" data-cb-site="indema-test" ></script>-->
    
     <script src="https://js.chargebee.com/v2/chargebee.js" data-cb-site="indema" ></script>
    
    
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                
                    <nav class="navbar navbar-default" role="navigation">
                    <div class="container">
                        <span class="navbar-brand">{{ Auth::user()->email }}</span>
                        
                            <a class="btn navbar-btn btn-danger navbar-right pull-right" role="button"  href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();"
                            ><i class="fa fa-power-off"></i> @lang('app.logout')
                            </a>
                        
                    </div>
                    </nav> 
                
                <div class="panel-heading">@lang('app.menu.accountSetup')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                       
                        
                        
                        <div id="updateClient">
                            
                    @if (session('message'))
                        <div class="alert alert-success" id="payment_alert">{{ session('message') }}</div>
                        <?php Session::forget('message');?>
                    @endif
                        <!-- progressbar 
                            <ul id="progressbar">
                                <li ?> >Plan</li>
                                <li class="active" >@lang('modules.accountSettings.updateTitle')</li>
                                <li >Module Settings</li>
                                <li >Onboarding</li>
                                <li>@lang('modules.invoiceSettings.updateTitle')</li>
                            </ul>-->
                        
                        <?php if($company->package_id == 2) { ?>
                        <fieldset>
                            <span class="step-number">Step 1/5</span>
							<h1>Welcome to indema!</h1>
							<p>First, select the package you want your 15-day trial for. Please note, credit card is required to move forward, however you will not be billed until the end of your 15-day trial.</p>
                            <!--<h3 class="fs-subtitle">Plan</h3>-->
                            
        <div class="col-xs-12">
			<div class="tab-switch">
			<span>MONTHLY</span>
			<label class="switch">
			  <input type="checkbox" id="check">
			  <span class="slider round"></span>
			</label>
			<span>ANNUALLY</span>
			</div>
        </div>
                            
                            
                            
                            
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
									<div>
									<h3 class="pricing_title">SINGLE PLAN</h3>
									
									<!--<h2 class="pricing_price_title">$19</h2>
									<div class="pricing_price_time uppercase">Monthly</div>-->
									</div>
								</th>
								<th style="min-width:80px;">
									<div>
									<h3 class="pricing_title">GROUP PLAN</h3>
									<!--
									<h2 class="pricing_price_title">$39</h2>
									<div class="pricing_price_time uppercase">Monthly</div>-->
									</div>
								</th>
								<th style="min-width:80px;">
									<div>
									<h3 class="pricing_title">HUB PLAN</h3>
									<!--
									<h2 class="pricing_price_title">$49</h2>
									<div class="pricing_price_time uppercase">Monthly</div>-->
									</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<div>
									<p>For the interior designers who conquer their projects themselves.</p>
									</div>
								</td>
								<td>
									<div>
									<p>For small teams who just need a bit more organization in their lives.</p>
									</div>
								</td>
								<td>
									<div>
									<p>For the larger teams who need all the bells and whistles.</p>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Users: </b>1</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Users: </b>3</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Users: </b>Unlimited</span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Projects:</b> Unlimited</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Projects:</b> Unlimited</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Projects:</b> Unlimited</span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div></div>
								</td>
								<td>
									<div>
									<span class="title"><b>Team Members</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Team Members</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Manage Products</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Products</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Products</b></span>	
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Moodboards (Coming soon)</b></span>	
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Reporting</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Reporting</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Reporting</b></span>	
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Zoom + QB Integrations</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Zoom + QB Integrations</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Zoom + QB Integrations</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Support: Email</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Support: Email, Live Chat</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Support: Email, Live Chat</b></span>
									</div>
								</td>								
							</tr>
                                                        
                                                        <tr>
								<td><div><p class="subtitle pricing_subtitle">$35/mon</p></div></td>
								<td><div><p class="subtitle pricing_subtitle">$55/mon</p></div></td>
								<td><div><p class="subtitle pricing_subtitle">$75/mon</p></div></td>
							</tr>
							
							<tr>

								@foreach($packages as $package)
									<td>
									<div>
										@if(round($package->monthly_price) > 0)
											@if(!($package->id == $company->package->id && $company->package_type == 'monthly')  && ($stripeSettings->paypal_status == 'active' || $stripeSettings->stripe_status == 'active' || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                                                                        <button style="display:none;" type="button" data-package-id="{{ $package->id }}"
														data-package-type="monthly"
														class="btn btn-success waves-effect waves-light selectPackage"
														title="Choose Plan"><i class="icon-anchor display-small"></i><span
															class="display-big">@lang('modules.billing.choosePlan')</span>
												</button>
<a href="javascript:void(0)" class="btn btn-success waves-effect waves-light" data-cb-type="checkout" data-cb-plan-id="{{ $package->chargebee_monthly_plan_id }}" >SELECT PLAN</a>
											@endif
										@endif
										</div>
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
									<div>
									<h3 class="pricing_title">SINGLE PLAN</h3>
									<!--
									<h2 class="pricing_price_title">$228</h2>
									<div class="pricing_price_time uppercase">Annually</div>-->
									</div>
								</th>
								<th style=" min-width:80px;">
									<div>
									<h3 class="pricing_title">GROUP PLAN</h3>
									<!--
									<h2 class="pricing_price_title">$468</h2>
									<div class="pricing_price_time uppercase">Annually</div>-->
									</div>
								</th>
								<th style=" min-width:80px;">
									<div>
									<h3 class="pricing_title">HUB PLAN</h3>
									<!--
									<h2 class="pricing_price_title">$539</h2>
									<div class="pricing_price_time uppercase">Annually</div>-->
									</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<div>
									<p>For the interior designers who conquer their projects themselves.</p>
									</div>
								</td>
								<td>
									<div>
									<p>For small teams who just need a bit more organization in their lives.</p>
									</div>
								</td>
								<td>
									<div>
									<p>For the larger teams who need all the bells and whistles.</p>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Users: </b>1</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Users: </b>3</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Users: </b>Unlimited</span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Projects:</b> Unlimited</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Projects:</b> Unlimited</span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Projects:</b> Unlimited</span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Clients, Leads + Vendors</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div></div>
								</td>
								<td>
									<div>
									<span class="title"><b>Team Members</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Team Members</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Project + Task Management, Client Portal</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Invoices, Estimates, PO's, Expenses </b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Auto and Manual Time Tracking + Billing</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Manage Products</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Products</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Manage Products</b></span>	
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Moodboards (Coming soon)</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Moodboards (Coming soon)</b></span>	
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Reporting</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Reporting</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Reporting</b></span>	
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Zoom + QB Integrations</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Zoom + QB Integrations</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Zoom + QB Integrations</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td>
									<div>
									<span class="title"><b>Support: Email</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Support: Email, Live Chat</b></span>
									</div>
								</td>
								<td>
									<div>
									<span class="title"><b>Support: Email, Live Chat</b></span>
									</div>
								</td>								
							</tr>
							<tr>
								<td><div><p class="subtitle pricing_subtitle">$385/Yr</p></div></td>
								<td><div><p class="subtitle pricing_subtitle">$550/Yr</p></div></td>
								<td><div><p class="subtitle pricing_subtitle">$675/Yr</p></div></td>
							</tr>
							<tr>
								@foreach($packages as $package)
									<td>
										<div>
										@if(round($package->annual_price) > 0)
											@if(!($package->id == $company->package->id && $company->package_type == 'annual')
											&& ($stripeSettings->paypal_status == 'active'  || $stripeSettings->stripe_status == 'active'  || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                                                                        <button style="display:none;" type="button" data-package-id="{{ $package->id }}"
														data-package-type="annual"
														class="btn btn-success waves-effect waves-light selectPackage"
														title="Choose Plan"><i class="icon-anchor display-small"></i><span
															class="display-big">@lang('modules.billing.choosePlan')</span>
												</button>
                                                                                                <a href="javascript:void(0)" class="btn btn-success waves-effect waves-light" data-cb-type="checkout" data-cb-plan-id="{{ $package->chargebee_annual_plan_id }}" >SELECT PLAN</a>
											@endif
										@endif
										</div>
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
		</div><!--end of col-12-->
	</div><!--end of row-->
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
                            
                            
                            
<!--                            <input type="button" name="next" class="next action-button" value="Next" />-->
                            
                        </fieldset>
                        <?php } ?>
                            
                            <fieldset>
							<span class="step-number">Step 2/5</span>
							<h1>Next, Let's set up your company.</h1>
							<p>These are the basics, however, you can edit or adjust anything later in the settings.</p>
                                {!! Form::open(['id'=>'companySettings','class'=>'ajax-form','method'=>'PUT']) !!}
                                    <!--<h3 class="fs-subtitle">@lang('modules.accountSettings.updateTitle')</h3>-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name">@lang('modules.accountSettings.companyName')</label>
                                                <input type="text" class="form-control" id="company_name" name="company_name"
                                                       value="{{ $global->company_name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                                                <input type="email" class="form-control" id="company_email" name="company_email"
                                                       value="{{ $global->company_email }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                                                <input type="tel" class="form-control" id="company_phone" name="company_phone"
                                                       value="{{ $global->company_phone }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                                                <input type="text" class="form-control" id="website" name="website"
                                                       value="{{ $global->website }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">@lang('modules.accountSettings.companyLogo')</label>
                                        <div class="col-md-12">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail"
                                                     style="width: 200px; height: 150px;">
                                                    @if(is_null($global->logo))
                                                        <img src="https://via.placeholder.com/200x150.png?text={{ str_replace(' ', '+', __('modules.accountSettings.uploadLogo')) }}"
                                                             alt=""/>
                                                    @else
                                                        <img src="{{ asset_url('app-logo/'.$global->logo) }}"
                                                             alt=""/>
                                                    @endif
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail"
                                                     style="max-width: 200px; max-height: 150px;"></div>
                                                <div>
                                                    <span class="btn btn-info btn-file">
                                                        <span class="fileinput-new"> @lang('app.selectImage') </span>
                                                        <span class="fileinput-exists"> @lang('app.change') </span>
                                                        <input type="file" name="logo" id="logo">
                                                    </span>
                                                    <a href="javascript:;" class="btn btn-danger fileinput-exists"
                                                       data-dismiss="fileinput"> @lang('app.remove') </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                                                <textarea class="form-control" id="address" rows="2"
                                                          name="address">{{ $global->address }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="currency_id">@lang('modules.accountSettings.defaultCurrency')</label>
                                                <select name="currency_id" id="currency_id" class="form-control">
                                                    @foreach($currencies as $currency)
                                                        <option
                                                                @if($currency->id == $global->currency_id) selected @endif
                                                        value="{{ $currency->id }}">{{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="timezone">@lang('modules.accountSettings.defaultTimezone')</label>
                                                <select name="timezone" id="timezone" class="form-control select2">
                                                    @foreach($timezones as $tz)
                                                        <option @if($global->timezone == $tz) selected @endif>{{ $tz }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date_format">@lang('modules.accountSettings.dateFormat')</label>
                                                <select name="date_format" id="date_format" class="form-control select2">
                                                    <option value="d, M Y" @if($global->date_format == 'd, M Y') selected @endif >d, M Y ({{ $dateObject->format('d, M Y') }}) </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="time_format">@lang('modules.accountSettings.timeFormat')</label>
                                                <select name="time_format" id="time_format" class="form-control select2">
                                                    <option value="h:i A" @if($global->time_format == 'H:i A') selected @endif >12 Hour  (6:20 PM) </option>
                                                    <option value="h:i a" @if($global->time_format == 'H:i a') selected @endif >12 Hour  (6:20 pm) </option>
                                                    <option value="H:i" @if($global->time_format == 'H:i') selected @endif >24 Hour  (18:20) </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="locale">@lang('modules.accountSettings.changeLanguage')</label>
                                                <select name="locale" id="locale" class="form-control select2">
                                                    <option @if($global->locale == "en") selected @endif value="en">English
                                                    </option>
                                                    @foreach($languageSettings as $language)
                                                        <option value="{{ $language->language_code }}" @if($global->locale == $language->language_code) selected @endif >{{ $language->language_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.update')</button>
                                {!! Form::close() !!}
                                <input type="button" name="next" class="next action-button" value="Next" style="display: none" />
                            </fieldset>
                            <!-- fieldsets -->
                            <fieldset>
							
							<span class="step-number">Step 3/5</span>
							<h1>Let's customize your experience with indema.</h1>
							<p>Select the features your firm will use. We've pre-selected some for you that are our basic benefits, however, you may find that you want to have access to other included benefits. Note: This can be changed later.</p>
                                <!--<h2 class="fs-title">Module Settings</h2>-->
                                
                                        <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="white-box p-0">
                                                                            <!--<h3 class="box-title m-b-0">{{ ucfirst($type) }} @lang("modules.moduleSettings.moduleSetting")</h3>

                                                                            <p class="text-muted m-b-10 font-13">
                                                                                @lang("modules.moduleSettings.employeeSubTitle") {{ ucfirst($type) }} @lang("modules.moduleSettings.section")
                                                                            </p>-->

                                                                            <div class="row">
                                                                                <div class="col-sm-12 col-xs-12 b-t p-t-20">
                                                                                    {!! Form::open(['id'=>'editSettings','class'=>'ajax-form form-horizontal','method'=>'PUT']) !!}

                                                                                    @foreach($modulesData as $setting)
                                                                                        @if($type == 'client')

                                                                                            @if($setting->module_name != 'tickets' && $setting->module_name != 'notices' && $setting->module_name != 'asset' && $setting->module_name != 'products' && $setting->module_name != 'timelogs')
                                                                                            <div class="form-group col-md-4">

                                                                                                <label class="control-label col-xs-3" >
                                                                                                    @if($setting->module_name == 'events')
                                                                                                        Schedules
                                                                                                    @elseif($setting->module_name == 'discussions')
                                                                                                    Discussions
                                                                                                    @else
                                                                                                    @lang('modules.module.'.$setting->module_name)
                                                                                                    @endif

                                                                                                </label>
                                                                                                <div class="col-xs-9">
                                                                                                    <div class="switchery-demo">
                                                                                                        <input type="checkbox" @if($setting->status == 'active') checked @endif class="js-switch change-module-setting" data-setting-id="{{ $setting->id }}" />
                                                                                                        @lang('modules.moduleDescription.'.$setting->module_name)
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            @endif



                                                                                        @else


                                                                                            @if($setting->module_name != 'tickets' && $setting->module_name != 'notices' && $setting->module_name != 'asset' && $setting->module_name != 'discussions')
                                                                                               <div class="form-group col-md-4">

                                                                                                   <label class="control-label col-xs-3" >
                                                                                                       @if($setting->module_name == 'events')
                                                                                                           Schedules
                                                                                                       @else
                                                                                                       @lang('modules.module.'.$setting->module_name)
                                                                                                       @endif

                                                                                                   </label>
                                                                                                   <div class="col-xs-9">
                                                                                                       <div class="switchery-demo">
                                                                                                           <input type="checkbox" @if($setting->status == 'active') checked @endif class="js-switch change-module-setting" data-setting-id="{{ $setting->id }}" />
                                                                                                           @lang('modules.moduleDescription.'.$setting->module_name)
                                                                                                       </div>
                                                                                                   </div>
                                                                                               </div>
                                                                                               @endif

                                                                                        @endif






                                                                                    @endforeach

                                                                                    {!! Form::close() !!}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                               
                                <input type="button" name="next" class="next action-button" value="Next" />
                            </fieldset>
                            
                            
                            <fieldset>							
							<span class="step-number">Step 4/5</span>
                                {!! Form::open(['id'=>'invoiceSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                                    {{--   <h2 class="fs-title">@lang('modules.invoiceSettings.updateTitle')</h2>--}}
                                    <h3 class="fs-subtitle">@lang('modules.invoiceSettings.updateTitle')</h3>
                                    <div class="row m-t-20">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="invoice_prefix">@lang('modules.invoiceSettings.invoicePrefix')</label>
                                                <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix"
                                                       value="{{ $invoiceSetting->invoice_prefix }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="invoice_digit">@lang('modules.invoiceSettings.invoiceDigit')</label>
                                                <input type="number" min="2" class="form-control" id="invoice_digit" name="invoice_digit"
                                                       value="{{ $invoiceSetting->invoice_digit }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="invoice_look_like">@lang('modules.invoiceSettings.invoiceLookLike')</label>
                                                <input type="text" class="form-control" id="invoice_look_like" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="estimate_prefix">@lang('modules.invoiceSettings.estimatePrefix')</label>
                                                <input type="text" class="form-control" id="estimate_prefix" name="estimate_prefix"
                                                       value="{{ $invoiceSetting->estimate_prefix }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="estimate_digit">@lang('modules.invoiceSettings.estimateDigit')</label>
                                                <input type="number" min="2" class="form-control" id="estimate_digit" name="estimate_digit"
                                                       value="{{ $invoiceSetting->estimate_digit }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="estimate_look_like">@lang('modules.invoiceSettings.estimateLookLike')</label>
                                                <input type="text" class="form-control" id="estimate_look_like" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_note_prefix">@lang('modules.invoiceSettings.credit_notePrefix')</label>
                                                <input type="text" class="form-control" id="credit_note_prefix" name="credit_note_prefix"
                                                       value="{{ $invoiceSetting->credit_note_prefix }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_note_digit">@lang('modules.invoiceSettings.credit_noteDigit')</label>
                                                <input type="number" min="2" class="form-control" id="credit_note_digit" name="credit_note_digit"
                                                       value="{{ $invoiceSetting->credit_note_digit }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_note_look_like">@lang('modules.invoiceSettings.credit_noteLookLike')</label>
                                                <input type="text" class="form-control" id="credit_note_look_like" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div style="display: none;" class="col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label for="template">@lang('modules.invoiceSettings.template')</label>
                                                <select name="template" class="image-picker show-labels show-html">
                                                    <option data-img-src="{{ asset('invoice-template/1.png') }}"
                                                            @if($invoiceSetting->template == 'invoice-1') selected @endif
                                                            value="invoice-1">Template
                                                        1
                                                    </option>
                                                    <option data-img-src="{{ asset('invoice-template/2.png') }}"
                                                            @if($invoiceSetting->template == 'invoice-2') selected @endif
                                                            value="invoice-2">Template
                                                        2
                                                    </option>
                                                    <option data-img-src="{{ asset('invoice-template/3.png') }}"
                                                            @if($invoiceSetting->template == 'invoice-3') selected @endif
                                                            value="invoice-3">Template
                                                        3
                                                    </option>
                                                    <option data-img-src="{{ asset('invoice-template/4.png') }}"
                                                            @if($invoiceSetting->template == 'invoice-4') selected @endif
                                                            value="invoice-4">Template
                                                        4
                                                    </option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="due_after">@lang('modules.invoiceSettings.dueAfter')</label>

                                                <div class="input-group m-t-10">
                                                    <input type="number" id="due_after" name="due_after" class="form-control" value="{{ $invoiceSetting->due_after }}">
                                                    <span class="input-group-addon">@lang('app.days')</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="gst_number">@lang('app.gstNumber')</label>
                                                <input type="text" id="gst_number" name="gst_number" class="form-control" value="{{ $invoiceSetting->gst_number }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label" >@lang('app.showGst')</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" name="show_gst" @if($invoiceSetting->show_gst == 'yes') checked @endif class="js-switch " data-color="#99d683"  />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="invoice_terms">@lang('modules.invoiceSettings.invoiceTerms')</label>
                                                <textarea name="invoice_terms" id="invoice_terms" class="form-control"
                                                          rows="4">{{ $invoiceSetting->invoice_terms }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.update')</button>
                                {!! Form::close() !!}
                                <input type="button" name="next" class="next action-button" value="Next" style="display: none" />
                            </fieldset>
                            
                            <fieldset>
                                <!--<h2 class="fs-title">onboarding</h2>-->
                                <span class="step-number">Step 5/5</span>
							<h1>Lastly, let's schedule your onboarding.</h1>
							<p>Getting the most out of indema is crucial to understanding how our platform will work with your design firm. This 1-hour onboarding session is optional, but we highly recommend this as there are things that may come up later as you are exploring indema that you may not know to reach out to us about.</p>
                                <div class="row">
									<div class="col-md-7">
										<p class="center">This free onboarding session of indema gives you an in-depth overview of our all-in-one design business platform. You'll see its potential to organize and level up your design firm.</p>
										<p>Your burning questions will be answered:</p>
										<p>
											<span>What is indema?</span>
											<span>Is indema for me?</span>
											<span>Is indema easy to use?</span>
											<span>Can indema get me organized?</span>
											<span>Can indema help me systemize?</span>
											<span>What does "systemize" mean, anyway?</span>
										</p>
										<p>Learn everything you ever wanted to know about indema but were afraid to ask!</p>
									</div>
                                    <div class="col-md-5">
                                            <div class="white-box p-0">
                                                
                                            <!-- Calendly inline widget begin -->
                                            <div class="calendly-inline-widget" data-url="https://calendly.com/indema/onboard?hide_event_type_details=1&hide_gdpr_banner=1&primary_color=000000" style="min-width:320px;height:630px;"></div>
                                            <!-- Calendly inline widget end -->

                                            </div>
                                    </div>
                                </div>
                                
                                                        <input id="save-form-finish" type="button" name="next" class="action-button" style="float: right;" value="Finish" />
                            </fieldset>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->
    
    
<!--<option value="d-m-Y" @if($global->date_format == 'd-m-Y') selected @endif >d-m-Y ({{ $dateObject->format('d-m-Y') }}) </option>
<option value="m-d-Y" @if($global->date_format == 'm-d-Y') selected @endif >m-d-Y ({{ $dateObject->format('m-d-Y') }}) </option>
<option value="Y-m-d" @if($global->date_format == 'Y-m-d') selected @endif >Y-m-d ({{ $dateObject->format('Y-m-d') }}) </option>
<option value="d.m.Y" @if($global->date_format == 'd.m.Y') selected @endif >d.m.Y ({{ $dateObject->format('d.m.Y') }}) </option>
<option value="m.d.Y" @if($global->date_format == 'm.d.Y') selected @endif >m.d.Y ({{ $dateObject->format('m.d.Y') }}) </option>
<option value="Y.m.d" @if($global->date_format == 'Y.m.d') selected @endif >Y.m.d ({{ $dateObject->format('Y.m.d') }}) </option>
<option value="d/m/Y" @if($global->date_format == 'd/m/Y') selected @endif >d/m/Y ({{ $dateObject->format('d/m/Y') }}) </option>
<option value="m/d/Y" @if($global->date_format == 'm/d/Y') selected @endif >m/d/Y ({{ $dateObject->format('m/d/Y') }}) </option>
<option value="Y/m/d" @if($global->date_format == 'Y/m/d') selected @endif >Y/m/d ({{ $dateObject->format('Y/m/d') }}) </option>
<option value="d-M-Y" @if($global->date_format == 'd-M-Y') selected @endif >d-M-Y ({{ $dateObject->format('d-M-Y') }}) </option>
<option value="d/M/Y" @if($global->date_format == 'd/M/Y') selected @endif >d/M/Y ({{ $dateObject->format('d/M/Y') }}) </option>
<option value="d.M.Y" @if($global->date_format == 'd.M.Y') selected @endif >d.M.Y ({{ $dateObject->format('d.M.Y') }}) </option>
<option value="d-M-Y" @if($global->date_format == 'd-M-Y') selected @endif >d-M-Y ({{ $dateObject->format('d-M-Y') }}) </option>
<option value="d M Y" @if($global->date_format == 'd M Y') selected @endif >d M Y ({{ $dateObject->format('d M Y') }}) </option>=
<option value="d F, Y" @if($global->date_format == 'd F, Y') selected @endif >d F, Y ({{ $dateObject->format('d F, Y') }}) </option>
<option value="D/M/Y" @if($global->date_format == 'D/M/Y') selected @endif >D/M/Y ({{ $dateObject->format('D/M/Y') }}) </option>
<option value="D.M.Y" @if($global->date_format == 'D.M.Y') selected @endif >D.M.Y ({{ $dateObject->format('D.M.Y') }}) </option>
<option value="D-M-Y" @if($global->date_format == 'D-M-Y') selected @endif >D-M-Y ({{ $dateObject->format('D-M-Y') }}) </option>
<option value="D M Y" @if($global->date_format == 'D M Y') selected @endif >D M Y ({{ $dateObject->format('D M Y') }}) </option>
<option value="d D M Y" @if($global->date_format == 'd D M Y') selected @endif >d D M Y ({{ $dateObject->format('d D M Y') }}) </option>
<option value="D d M Y" @if($global->date_format == 'D d M Y') selected @endif >D d M Y ({{ $dateObject->format('D d M Y') }}) </option>
<option value="dS M Y" @if($global->date_format == 'dS M Y') selected @endif >dS M Y ({{ $dateObject->format('dS M Y') }}) </option>-->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/steps-form/steps.js') }}"></script>
    <script src="{{ asset('plugins/image-picker/image-picker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>

    <script>
        
        
        $("#payment_alert").fadeTo(2000, 500).slideUp(500, function(){
            $("#success-alert").slideUp(1000);
        });
        
        $(".image-picker").imagepicker();
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());

        });
        $('#companySettings #save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.account-setup.update', $global->id)}}',
                container: '#companySettings',
                type: "POST",
                data: $('#companySettings').serialize(),
                file:true,
                success: function (response) {
                    console.log(response);
                    if(response.status == 'success'){
                        $('#companySettings').siblings('.next').trigger('click');
                    }
                }
            })
        });
        $('#invoiceSettings #save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.account-setup.update-invoice', $invoiceSetting->id)}}',
                container: '#invoiceSettings',
                type: "POST",
                data: $('#invoiceSettings').serialize(),
                success: function (response) {
                    console.log(response);
                    if(response.status == 'success'){
                        $('#invoiceSettings').siblings('.next').trigger('click');
                    }
                }
            })
        });
        
        $('#save-form-finish').click(function () {
            
            $.easyAjax({
                url: '{{route('admin.account-setup.completed',$global->id)}}',
                type: "POST",
                 data: { 'completed' : '1', '_token': '{{ csrf_token() }}' },
                success: function (response) {
                    console.log(response);
                    if(response.status == 'success'){
                        $('#invoiceSettings').siblings('.next').trigger('click');
                    }
                }
            })
        });
        
        

        $('#invoice_prefix, #invoice_digit, #estimate_prefix, #estimate_digit, #credit_note_prefix, #credit_note_digit').on('keyup', function () {
            genrateInvoiceNumber();
        });

        genrateInvoiceNumber();

        function genrateInvoiceNumber() {
            var invoicePrefix = $('#invoice_prefix').val();
            var invoiceDigit = $('#invoice_digit').val();
            var invoiceZero = '';
            for ($i=0; $i<invoiceDigit-1; $i++){
                invoiceZero = invoiceZero+'0';
            }
            invoiceZero = invoiceZero+'1';
            var invoice_no = invoicePrefix+'#'+invoiceZero;
            $('#invoice_look_like').val(invoice_no);

            var estimatePrefix = $('#estimate_prefix').val();
            var estimateDigit = $('#estimate_digit').val();
            var estimateZero = '';
            for ($i=0; $i<estimateDigit-1; $i++){
                estimateZero = estimateZero+'0';
            }
            estimateZero = estimateZero+'1';
            var estimate_no = estimatePrefix+'#'+estimateZero;
            $('#estimate_look_like').val(estimate_no);

            var creditNotePrefix = $('#credit_note_prefix').val();
            var creditNoteDigit = $('#credit_note_digit').val();
            var creditNoteZero = '';
            for ($i=0; $i<creditNoteDigit-1; $i++){
                creditNoteZero = creditNoteZero+'0';
            }
            creditNoteZero = creditNoteZero+'1';
            var creditNote_no = creditNotePrefix+'#'+creditNoteZero;
            $('#credit_note_look_like').val(creditNote_no);
        }
    </script>
    <script>
        $(".date-picker").datepicker({
            todayHighlight: true,
            autoclose: true,
            format: '{{ $global->date_picker_format }}',
        });

        {{--$('#save-form').click(function () {--}}
        {{--    $.easyAjax({--}}
        {{--        url: '{{route('admin.clients.update', [$clientDetail->id])}}',--}}
        {{--        container: '#updateClient',--}}
        {{--        type: "POST",--}}
        {{--        redirect: true,--}}
        {{--        data: $('#updateClient').serialize()--}}
        {{--    })--}}
        {{--});--}}
    </script>
    
<script>

    $('.change-module-setting').change(function () {
        var id = $(this).data('setting-id');

        if($(this).is(':checked'))
            var moduleStatus = 'active';
        else
            var moduleStatus = 'deactive';

        var url = '{{route('admin.account-setup.module-setting-update', ':id')}}';
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "POST",
            container:".data-section",
            data: { 'id': id, 'status': moduleStatus, '_method': 'PUT', '_token': '{{ csrf_token() }}' },
            success: function(res) {
                if(res.status == 'fail' && res.error_name == 'module_dependent') {
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000)
                }
            }
        })
    });
</script>

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
