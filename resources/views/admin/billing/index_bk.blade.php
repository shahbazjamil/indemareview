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
    </style>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush


@section('content')

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
                <?php Session::forget('message');?>
            @endif
            <div class="white-box">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('modules.billing.yourCurrentPlan') ({{  $company->package->name }})
                    @if(!is_null($firstInvoice) && $stripeSettings->api_key != null && $stripeSettings->api_secret != null && $firstInvoice->method == 'Stripe')
                        @if(!is_null($subscription) && $subscription->ends_at == null)
                                <button type="button" class="btn btn-danger waves-effect waves-light unsubscription" data-type="stripe" title="Unsubscribe Plan"><i class="fa fa-ban display-small"></i> <span class="display-big">@lang('modules.billing.unsubscribe')</span></button>
                        @endif
                    @elseif(!is_null($firstInvoice) && $stripeSettings->paypal_client_id != null && $stripeSettings->paypal_secret != null && $firstInvoice->method == 'Paypal')
                        @if(!is_null($paypalInvoice) && $paypalInvoice->end_on == null  && $paypalInvoice->status == 'paid')
                                <button type="button" class="btn btn-danger waves-effect waves-light unsubscription" data-type="paypal" title="Unsubscribe Plan"><i class="fa fa-ban display-small"></i> <span class="display-big">@lang('modules.billing.unsubscribe')</span></button>
                        @endif
                    @elseif(!is_null($firstInvoice) && $stripeSettings->razorpay_key != null && $stripeSettings->razorpay_secret != null && $firstInvoice->method == 'Razorpay')
                        @if(!is_null($razorPaySubscription) && $razorPaySubscription->ends_at == null)
                                <button type="button" class="btn btn-danger waves-effect waves-light unsubscription" data-type="razorpay" title="Unsubscribe Plan"><i class="fa fa-ban display-small"></i> <span class="display-big">@lang('modules.billing.unsubscribe')</span></button>
                        @endif

                    @elseif(!is_null($firstInvoice) && $stripeSettings->paystack_client_id != null && $stripeSettings->paystack_secret != null && $firstInvoice->method == 'Paystack')
                        @if(!is_null($payStackSubscription) && $payStackSubscription->ends_at == null)
                                <button type="button" class="btn btn-danger waves-effect waves-light unsubscription" data-type="paystack" title="Unsubscribe Plan"><i class="fa fa-ban display-small"></i> <span class="display-big">@lang('modules.billing.unsubscribe')</span></button>
                        @endif
                    @else

                    @endif
                    <div class="pull-right" style="margin-top: -7px;"><a href="{{ route('admin.billing.packages') }}" class="btn btn-block btn-success waves-effect text-center">@lang('modules.billing.changePlan')</a> </div>
                </div>

                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('app.annual') @lang('app.price')
                            </div>
                            <div class="col-sm-3">
                                 {{ currency_position($company->package->annual_price, $company->package->currency->currency_symbol) }}
                            </div>
                        </div>
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('app.monthly') @lang('app.price')
                            </div>
                            <div class="col-sm-3">
                                {{ currency_position($company->package->monthly_price, $company->package->currency->currency_symbol) }}
                            </div>
                        </div>
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('app.max') @lang('app.menu.employees')
                            </div>
                            <div class="col-sm-3">
                                {{ $company->package->max_employees }}
                            </div>
                        </div>
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('app.active') @lang('app.menu.employees')
                            </div>
                            <div class="col-sm-3">
                                {{ $company->employees->count() }}
                            </div>
                        </div>
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('app.maxStorageSize')
                            </div>
                            <div class="col-sm-3">
                                @if($company->package->max_storage_size == -1)
                                    Unlimited
                                @else
                                    {{ $company->package->max_storage_size }}  ({{ strtoupper($company->package->storage_unit) }})
                                @endif
                            </div>
                        </div>
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('app.usedStorage')
                            </div>
                            <div class="col-sm-3">
                                @if($company->package->storage_unit == 'mb')
                                    {{ $company->file_storage->count() > 0 ? round($company->file_storage->sum('size')/(1000*1024), 4). ' MB' : 'Not used' }}
                                @else
                                    {{ $company->file_storage->count() > 0 ? round($company->file_storage->sum('size')/(1000*1024*1024), 4). ' MB' : 'Not Used' }}
                                @endif
                            </div>
                        </div>
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('modules.billing.nextPaymentDate')
                            </div>
                            <div class="col-sm-3">
                                {{ $nextPaymentDate }}
                            </div>
                        </div>
                        <div class="row f-15 m-b-10">
                            <div class="col-sm-9">
                                @lang('modules.billing.previousPaymentDate')
                            </div>
                            <div class="col-sm-3">
                                {{ $previousPaymentDate }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="white-box">
                <h3 class="box-title">@lang('app.menu.invoices')</h3>

                <div class="table-responsive">
                    <table class="table color-table inverse-table" id="users-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('app.menu.packages')</th>
                            <th>@lang('app.amount') ({{ $global->currency->currency_symbol }})</th>
                            <th>@lang('app.date')</th>
                            <th>@lang('modules.billing.nextPaymentDate')</th>
                            <th>@lang('modules.payments.paymentGateway')</th>
                            <th>@lang('app.action')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row bg-title">
        <div class="col-xs-12">
            <h4 class="page-title">Your Subscription</h4>
        </div><!--end of col-12-->
    </div><!--end of bg-title-->
    <div class="row flex payment">
        <div class="col-md-4">
            <div class="box">
				<span class="d-block">Current Subscription Plan</span>
				<div><b>$35</b>Monthly<span class="d-block">SINGLE PLAN</span></div>
				<a href="#"data-toggle="modal" data-target="#billingplan">Change Plan</a>
			</div><!--end of box-->
        </div><!--end of col-4-->
        <div class="col-md-4">
            <div class="box">
				<span class="d-block">Using Payment Method:</span>
				<div><span class="d-block">VISA:2522</span><span class="d-block">EXP:22/22</span></div>
				<a href="#">Change Card</a>
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
			<div class="payment-content collapsed" data-toggle="collapse" href="#paymentone" aria-expanded="false" aria-controls="paymentone">
				<span class="amount">$65.00</span>
				<span class="status"><b class="pending">Pending</b></span>
				<span class="date">May 10,2020</span>
				<span class="payment-method">Visa 5432</span>
				<span class="details"><button class="fa fa-angle-up" type="button"></button></span>
			</div><!--end of payment-content-->
			<div class="payment-content-details collapse" id="paymentone">
				<div class="row">
					<div class="col-md-5">
						<span>Billing Plan</span>
						<h2>Company Start</h2>
						<div>
							<span class="fa fa-info-circle"></span>
							<ul>
								<li>5 team members ($8 / month each)</li>
								<li>+ 100 GB extra storage ($25.00)</li>
								<li>+ 8 extra hours ($2 per 1 hour)</li>
							</ul>
						</div>
					</div><!--end of col-5-->
					<div class="col-md-2">
						<div class="m-b-15">
							<span>Status</span>
							Completed
						</div>
						<div>
							<span>ID number</span>
							EKG2SJFN
						</div>
					</div><!--end of col-2-->
					<div class="col-md-2 border-right">
						<div class="m-b-15">
							<span>Invoice date</span>
							Apr 10, 2020
						</div>
						<div>
							<span>Date Paid</span>
							Apr 10, 2020
						</div>
					</div><!--end of col-2-->
					<div class="col-md-3 text-center">
						<span>Amount</span>
						<h2>$250.00M</h2>
					</div><!--end of col-2-->
				</div><!--end of row-->
			</div><!--end of payment-content-details-->
			<div class="payment-content collapsed" data-toggle="collapse" href="#paymenttwo" aria-expanded="false" aria-controls="paymenttwo">
				<span class="amount">$125.00</span>
				<span class="status"><b class="completed">Completed</b></span>
				<span class="date">Apr 10,2020</span>
				<span class="payment-method">Visa 5422</span>
				<span class="details"><button class="fa fa-angle-up" type="button"></button></span>
			</div><!--end of payment-content-->
			<div class="payment-content-details collapse" id="paymenttwo">
				<div class="row">
					<div class="col-md-5">
						<span>Billing Plan</span>
						<h2>Company Start</h2>
						<div>
							<span class="fa fa-info-circle"></span>
							<ul>
								<li>5 team members ($8 / month each)</li>
								<li>+ 100 GB extra storage ($25.00)</li>
								<li>+ 8 extra hours ($2 per 1 hour)</li>
							</ul>
						</div>
					</div><!--end of col-5-->
					<div class="col-md-2">
						<div class="m-b-15">
							<span>Status</span>
							Completed
						</div>
						<div>
							<span>ID number</span>
							EKG2SJFN
						</div>
					</div><!--end of col-2-->
					<div class="col-md-2 border-right">
						<div class="m-b-15">
							<span>Invoice date</span>
							Apr 10, 2020
						</div>
						<div>
							<span>Date Paid</span>
							Apr 10, 2020
						</div>
					</div><!--end of col-2-->
					<div class="col-md-3 text-center">
						<span>Amount</span>
						<h2>$250.00</h2>
					</div><!--end of col-2-->
				</div><!--end of row-->
			</div><!--end of payment-content-details-->
        </div><!--end of col-8-->
        <div class="col-md-4 optiona-addons">
			<h2 class="page-title">Optional Add-on's</h2>
            <div class="border-box">
				<h5>Bookings</h5>
				<span class="">$5/Mo</span>
				<p>A great alternative to calendly! Add a calendar booking page to your website for your clients to book sessions with you! Payment support coming soon.</p>
				<a href="#">Add</a>
			</div><!--end of box-->
            <div class="border-box">
				<h5>Social Media Management</h5>
				<span class="">Varies</span>
				<p>Schedule and plan all of your social media from within indema. Post to Facebook,  Instagram Twitter and LinkedIn.</p>
				<a href="#">Add</a>
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
						<span class="yearly">Annualy</span>
					</label>
					<div class="row monthly">
						<div class="col-md-4">
							<div class="package-card d-inline-flex">
								<div class="col-auto">
									<h1>Single</h1>
									$35 <span>Monthly</span>
								</div><!--end of col-auto-->
								<div class="col">
									<span class="d-block">
										<i class="fa fa-check"></i> 1 User account
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
								<a href="#">SELECT PLAN</a>
							</div><!--end of package-card-->
						</div><!--end of col-4-->
						<div class="col-md-4">
							<div class="package-card d-inline-flex popular">
								<div class="col-auto">
									<h1>Group</h1>
									$55 <span>Monthly</span>
								</div><!--end of col-auto-->
								<div class="col">
									<span class="d-block">
										<i class="fa fa-check"></i> Up to 3 Users
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
								<a href="#">SELECT PLAN</a>
							</div><!--end of package-card-->
						</div><!--end of col-4-->
						<div class="col-md-4">
							<div class="package-card d-inline-flex">
								<div class="col-auto">
									<h1>Hub</h1>
									$75 <span>Monthly</span>
								</div><!--end of col-auto-->
								<div class="col">
									<span class="d-block">
										<i class="fa fa-check"></i> Up to 10 Users*
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
								<a href="#">SELECT PLAN</a>
							</div><!--end of package-card-->
						</div><!--end of col-4-->
					</div><!--end of row-->
					<div class="row yearly" style="display:none">
						<div class="col-md-4">
							<div class="package-card d-inline-flex">
								<div class="col-auto">
									<h1>Single</h1>
									$350 <span>Annualy</span>
								</div><!--end of col-auto-->
								<div class="col">
									<span class="d-block">
										<i class="fa fa-check"></i> 1 User account
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
								<a href="#">SELECT PLAN</a>
							</div><!--end of package-card-->
						</div><!--end of col-4-->
						<div class="col-md-4">
							<div class="package-card d-inline-flex popular">
								<div class="col-auto">
									<h1>Group</h1>
									$550 <span>Annualy</span>
								</div><!--end of col-auto-->
								<div class="col">
									<span class="d-block">
										<i class="fa fa-check"></i> Up to 3 Users
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
								<a href="#">SELECT PLAN</a>
							</div><!--end of package-card-->
						</div><!--end of col-4-->
						<div class="col-md-4">
							<div class="package-card d-inline-flex">
								<div class="col-auto">
									<h1>Hub</h1>
									$750 <span>Annualy</span>
								</div><!--end of col-auto-->
								<div class="col">
									<span class="d-block">
										<i class="fa fa-check"></i> Up to 10 Users*
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
								<a href="#">SELECT PLAN</a>
							</div><!--end of package-card-->
						</div><!--end of col-4-->
					</div><!--end of row-->
				</div><!--end of modal-body-->
			</div><!--end of modal-content-->	
		</div><!--end of modal-dialog-->
	</div><!--end of modal-->
@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script>
{{--        {{ dd(session()->all()) }}--}}
        @if(\Session::has('message'))
        toastr.success("{{  \Session::get('message') }}");
        @endif
        $(function() {
            var table = $('#users-table').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                stateSave: true,
                "ordering": false,
                ajax: '{!! route('admin.billing.data') !!}',
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'paid_on', name: 'paid_on' },
                    { data: 'next_pay_date', name: 'next_pay_date' },
                    { data: 'method', name: 'method' },
                    { data: 'action', name: 'action' }
                ]
            });
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
