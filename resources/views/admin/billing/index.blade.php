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
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
    <style>
        .f-15{
            font-size: 15px !important;
        }
        
        .stripePaymentForm{transition: 0.3s;}
    .stripePaymentForm.show{display: block;}
    div#card-element{
        width: 100%;
        color: #4a5568;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        line-height: 1.25;
        border-width: 1px;
        border-radius: 0.25rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-style: solid;
        border-color: #e2e8f0;
    }
        
    </style>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush


@section('content')

   

    <div class="row bg-title">
        <div class="col-xs-12">
            <h4 class="page-title">Your Subscription</h4>
        </div><!--end of col-12-->
    </div><!--end of bg-title-->
    <div class="row flex payment">
        <div class="col-md-4">
            <div class="box">
                <span class="d-block">Current Subscription Plan</span>
                @if($company->package_type == 'monthly')
                    <div><b>{{ currency_position($company->package->monthly_price, $company->package->currency->currency_symbol) }}</b>Monthly<span class="d-block">{{strtoupper($company->package->name)}} PLAN</span></div>
                @else
                    <div><b>{{ currency_position($company->package->annual_price, $company->package->currency->currency_symbol) }}</b>Annual<span class="d-block">{{strtoupper($company->package->name)}} PLAN</span></div>
                @endif
                <a href="javascript:void(0)"data-toggle="modal" data-target="#billingplan">Change Plan</a>
            </div><!--end of box-->
        </div><!--end of col-4-->
        
        <div class="col-md-4">
            <div class="box">
                    <span class="d-block">Using Payment Method:</span>
                    <div><span class="d-block">{{strtoupper($company->card_brand)}}:{{$company->card_last_four}}</span><span class="d-block">EXP:{{$company->card_exp_month}}/{{$company->card_exp_year}}</span></div>
                    <a href="javascript:void(0)" class="changeCard">Change Card</a>
            </div><!--end of box-->
        </div><!--end of col-4-->
        
        <div class="col-md-4">
            <div class="box">
				<span class="d-block">Cancel Your Plan</span>
				<div>Contact us at <a href="mailto:support@indema.com">Support@indema.co</a> to cancel your plan.</div>
			</div><!--end of box-->
        </div><!--end of col-4-->
        <div class="col-md-8">
			<h2 class="page-title">Payment History</h2>
			<div class="payment-heading">
				<span class="amount">Amount</span>
				<span class="status">Status</span>
				<span class="date">Date</span>
				<span class="payment-method">Payment Method</span>
				<span class="details">Details</span>
			</div><!--end of payment-heading-->
			
                    @if($stripeInvoices)
                        @foreach($stripeInvoices as $invoice)
                            <div class="payment-content collapsed" data-toggle="collapse" href="#paymenttabe-{{$invoice->id}}" aria-expanded="false" aria-controls="paymenttabe-{{$invoice->id}}">
                                    <span class="amount">{{ currency_position($invoice->amount, $company->package->currency->currency_symbol) }}</span>
                                    <span class="status"><b class="completed">Completed</b></span>
                                    <span class="date">{{$invoice->created_at->format($global->date_format) }}</span>
                                    <span class="payment-method">{{$invoice->brand}} {{$invoice->last4}}</span>
                                    <span class="details"><button class="fa fa-angle-up" type="button"></button></span>
                            </div><!--end of payment-content-->

                            <div class="payment-content-details collapse" id="paymenttabe-{{$invoice->id}}">
                                    <div class="row">
                                            <div class="col-md-5">
                                                    <span>Billing Plan</span>
                                                    <h2>{{strtoupper($invoice->package->name)}}</h2>
<!--                                                    <div>
                                                            <span class="fa fa-info-circle"></span>
                                                            <ul>
                                                                    <li>5 team members ($8 / month each)</li>
                                                                    <li>+ 100 GB extra storage ($25.00)</li>
                                                                    <li>+ 8 extra hours ($2 per 1 hour)</li>
                                                            </ul>
                                                    </div>-->
                                            </div><!--end of col-5-->
                                            <div class="col-md-2">
                                                    <div class="m-b-15">
                                                            <span>Status</span>
                                                            Completed
                                                    </div>
                                                    <div>
                                                            <span>ID number</span>
                                                            {{ str_limit($invoice->transaction_id, $limit = 10 , $end = '') }}
                       
                                                    </div>
                                            </div><!--end of col-2-->
                                            <div class="col-md-2 border-right">
                                                    <div class="m-b-15">
                                                            <span>Invoice date</span>
                                                            {{$invoice->pay_date->format($global->date_format) }}
                                                    </div>
                                                    <div>
                                                            <span>Date Paid</span>
                                                            {{$invoice->pay_date->format($global->date_format) }}
                                                    </div>
                                            </div><!--end of col-2-->
                                            <div class="col-md-3 text-center">
                                                    <span>Amount</span>
                                                    <h2>{{ currency_position($invoice->amount, $company->package->currency->currency_symbol) }}</h2>
                                            </div><!--end of col-2-->
                                    </div><!--end of row-->
                            </div><!--end of payment-content-details-->
                        @endforeach
                    @endif
			
			
        </div><!--end of col-8-->
        <div class="col-md-4 optiona-addons">
			<h2 class="page-title">Optional Add-on's</h2>
            <div class="border-box">
				<h5>Bookings</h5>
				<span class="">$5/Mo</span>
				<p>A great alternative to calendly! Add a calendar booking page to your website for your clients to book sessions with you! Payment support coming soon.</p>
				<a href="javascript:void">Performing Updates</a>
			</div><!--end of box-->
            <div class="border-box">
				<h5>Social Media Management</h5>
				<span class="">Varies</span>
				<p>Schedule and plan all of your social media from within indema. Post to Facebook,  Instagram Twitter and LinkedIn.</p>
				<a href="javascript:void">Performing Updates</a>
			</div><!--end of box-->
        </div><!--end of col-4-->
    </div><!--end of bg-title-->
	<div class="modal fade" id="billingplan" tabindex="-1" role="dialog" aria-labelledby="billingplanLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header border-0 p-0">
					<button type="button" class="close m-r-5" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div><!--end of modal-header-->
				<div class="modal-body">
					<label class="switch">
						<input type="checkbox">
						<span class="slider round"></span>
						<span class="monthly">Monthly</span>
						<span class="yearly">Annual</span>
					</label>
                                    
					<div class="row monthly">
                                            @foreach($packages as $package)
                                            @if(round($package->monthly_price) > 0)
                                            @if( ($stripeSettings->paypal_status == 'active' || $stripeSettings->stripe_status == 'active' || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                                    <div class="col-md-4">
                                                            <div class="package-card d-inline-flex">
                                                                    <div class="col-auto">
                                                                            <h1>{{ucfirst(strtolower($package->name))}}</h1>
                                                                            {{ currency_position($package->monthly_price, $company->package->currency->currency_symbol) }} <span>Monthly</span>
                                                                    </div><!--end of col-auto-->
                                                                    <div class="col">
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i>  {{$package->number_users}} User account
                                                                            </span>
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i> Unlimited Projects
                                                                            </span>
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i> All Features Included
                                                                            </span>
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i> Full team support
                                                                            </span>
                                                                    </div><!--end of col-->
                                                                    <a href="{{route('admin.billing.change-plan', [$package->stripe_monthly_plan_id])}}">SELECT PLAN</a>
                                                            </div><!--end of package-card-->
                                                    </div><!--end of col-4-->
                                                    @endif
                                                @endif
						
                                            @endforeach
					</div><!--end of row-->
                                        
					<div class="row yearly" style="display:none">
                                            @foreach($packages as $package)
                                            @if(round($package->annual_price) > 0)
                                            
                                            @if(($stripeSettings->paypal_status == 'active'  || $stripeSettings->stripe_status == 'active'  || $stripeSettings->razorpay_status == 'active' || $stripeSettings->paystack_status == 'active' || $offlineMethods > 0))
                                                <div class="col-md-4">
                                                            <div class="package-card d-inline-flex">
                                                                    <div class="col-auto">
                                                                            <h1>{{ucfirst(strtolower($package->name))}}</h1>
                                                                            {{ currency_position($package->annual_price, $company->package->currency->currency_symbol) }} <span>Annual</span>
                                                                    </div><!--end of col-auto-->
                                                                    <div class="col">
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i> {{$package->number_users}} User account
                                                                            </span>
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i> Unlimited Projects
                                                                            </span>
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i> All Features Included
                                                                            </span>
                                                                            <span class="d-block">
                                                                                    <i class="fa fa-check"></i> Full team support
                                                                            </span>
                                                                    </div><!--end of col-->
                                                                    <a href="{{route('admin.billing.change-plan', [$package->stripe_annual_plan_id])}}">SELECT PLAN</a>
                                                            </div><!--end of package-card-->
                                                    </div><!--end of col-4-->
                                                 @endif
                                                @endif
                                            @endforeach
	
						
					</div><!--end of row-->
                                        
				</div><!--end of modal-body-->
			</div><!--end of modal-content-->	
		</div><!--end of modal-dialog-->
	</div><!--end of modal-->
        
        
        {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="change-card-form" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
        <div class="form-body">

        </div>
    </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
        
    <div class="modal fade bs-modal-md in" id="update-card-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading">Update Card</span>
                </div>
                <div class="modal-body">
        <div class="form-body">
            <div class="alert alert-danger">Please update their card to continue using Indema.</div>
        </div>
    </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
        
        
@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<!--    <script src="https://js.stripe.com/v3/"></script>-->
    
    
<!--    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>-->
    <script>
      
    // Show Create Holiday Modal
    $('body').on('click', '.changeCard', function () {
        var url = "{{ route('admin.billing.change-strip-card') }}";
        $.ajaxModal('#change-card-form', url);
    });
    
    <?php if(empty($company->card_last_four) && $company->status == 'license_expired') { ?>
    $(function() {
        $('#update-card-modal').modal('show');
    });
    <?php } ?>
        
{{--        {{ dd(session()->all()) }}--}}

        @if(\Session::has('message'))
        toastr.success("{{  \Session::get('message') }}");
        <?php Session::forget('message');?>
        @endif
        
         @if(\Session::has('error'))
        toastr.error("{{  \Session::get('error') }}");
        <?php Session::forget('error');?>
        @endif
        
        $(function() {
        });

        $('body').on('click', '.unsubscription', function(){
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
            }, function(isConfirm){
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
		$(document).ready(function(){			
		$(".switch input").click(function(){
			if($('.switch input').prop('checked')){$(".switch").addClass("checked")}
			else{$(".switch").removeClass("checked")}
		});		
		$('button[toggle="collapse"]').click(function(){
			if ($(this).hasClass("collapsed")) {
			  $(this).parents(".payment-content").addClass("collapsed");
			}
			else{
			  $(this).parents(".payment-content").removeClass("collapsed");
			}
		});
		});
    </script>
@endpush
