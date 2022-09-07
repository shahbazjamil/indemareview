@extends('layouts.app')

@section('page-title')
    <div class="row bg-title p-b-0">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
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

@section('content')

<!-- /old layout hide -->

<div style="display: none;" class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">@lang('modules.quickbooks.updateTitle')</div>
                <div class="vtabs customvtab m-t-10">
                    @include('sections.admin_setting_menu')
                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <?php 
                                        if(isset($qbo)){ ?>
                                            {!! Form::open(['id'=>'UpdateQuickBookSettings','class'=>'ajax-form','method'=>'PUT']) !!}                                               
                                         <?php }else{ ?>
                                            {!! Form::open(['id'=>'StoreQuickBookSettings','class'=>'ajax-form','method'=>'POST']) !!}                                            
                                        <?php }
                                    ?>
                                    
                                    <div class="form-body">
                                        <div class="row">

                                           <?php
                                                if(isset($qbo)  && !empty($qbo->client_secret)){

                                        

                                                    $segment1 = ''; $segment2 = ''; $segment3 = '';
                                                   $segment1 =  Request::segment(0);
                                                   $segment2 =  Request::segment(1);
                                                   $segment3 =  Request::segment(2);
                                                   $host = Request::getSchemeAndHttpHost();
                                                   $url = $host.$segment1.'/'.$segment2.'/'.$segment3.'/quickbooks-connect' ;
                                                   $value = isset($qbo) && !empty($qbo->client_secret) ? $url : '';
                                           ?>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="hidden" name="redirect_url" id="redirect_url"
                                                           class="form-control" value="{{$value}}">
                                                </div>
                                            </div>

                                            <!-- Connect button  -->
                                                           
                                            <?php 

                                            if(isset($qbo)  && !empty($qbo->client_secret) && empty($admin_settings->realmid)){ ?>
                                                <div class="col-md-12 text-center">
                                                    <a href="{{$url}}"><img src="{{ asset('img/qb.png') }}" alt="user-img"  width="150"></a>
                                                    <h2>Connect to Quickbooks</h2>
                                                </div>
                                            <?php } else if(isset($qbo_company)){ ?>
                                                 <div class="col-md-12 text-center">
                                                    <img src="{{ asset('img/success-green.png') }}" alt="user-img"  width="150">
                                                    <h2>Quickbooks is connected with {{$qbo_company}}</h2>
                                                </div>
                                            <?php  } ?>
                                            
                                    

                                        </div>

                                    </div>

                                    {!! Form::close() !!}
                                        <?php 
                                    }else{ ?>
                                        <h3>Please Contact Super Admin to enable Quickbook Settings</h3>
                                    <?php }?>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>

                </div>
            </div>    <!-- .row -->
        </div>
    </div>
	<div class="row ingration-row">
        <div class="col-md-12 bg-title">
<!--			<h4><i class="icon-settings"></i> Integrations</h4>-->
			<div class="row m-0">			
				<h4 class="border-bottom p-b-10 m-b-10">Finance</h4>
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/quickbook-logo.png') }}" alt="Quickbooks"  width="150">
						<p>
							Connecting to Quickbooks allows you to seamlessly sync all your financial data from indema.
						</p>
						<?php 
                                        if(isset($qbo)){ ?>
                                            {!! Form::open(['id'=>'UpdateQuickBookSettings','class'=>'ajax-form','method'=>'PUT']) !!}                                               
                                         <?php }else{ ?>
                                            {!! Form::open(['id'=>'StoreQuickBookSettings','class'=>'ajax-form','method'=>'POST']) !!}                                            
                                        <?php }
                                    ?>
                                           <?php
                                                if(isset($qbo)  && !empty($qbo->client_secret)){
                                                    
                                                    $segment1 = ''; $segment2 = ''; $segment3 = '';
                                                   $segment1 =  Request::segment(0);
                                                   $segment2 =  Request::segment(1);
                                                   $segment3 =  Request::segment(2);
                                                   $host = Request::getSchemeAndHttpHost();
                                                   $url = $host.$segment1.'/'.$segment2.'/'.$segment3.'/quickbooks-connect' ;
                                                   $value = isset($qbo) && !empty($qbo->client_secret) ? $url : '';
                                           ?>
                                        <input type="hidden" name="redirect_url" id="redirect_url" class="form-control" value="{{$value}}">
                                                          
                                            <?php 
                                            if(isset($qbo)  && !empty($qbo->client_secret) && empty($admin_settings->realmid)){ ?>
                                                    <a href="{{$url}}" class="integration-btn">Configure</a>
                                            <?php } else if(isset($qbo_company)){ ?>
                                                    <a href="javascript::void(0)" class="integration-btn">Quickbooks is connected with {{$qbo_company}}</a>
                                            <?php  } else { ?>
                                                    <a href="{{$url}}" class="integration-btn">Re Configure</a>
                                            <?php } ?>

                                    {!! Form::close() !!}
                                        <?php 
                                    }else{ ?>
                                        <a href="javascript::void(0)" class="integration-btn">Please Contact Super Admin to enable Quickbook Settings</a>
                                        
                                    <?php }?>
					</div><!--end of border-->
				</div><!--end of col-4-->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/xero-logo.png') }}" alt="Xero"  width="150">
						<p>
							Our integration with Xero will allow you to seamlessly sync all your financial data from indema.
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-4-->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/wave-logo.png') }}" alt="Wave"  width="150">
						<p>
							Our integration with Wave will allow you to seamlessly sync all your financial data from indema.
						</p>
						<a href="javascript::void(0)" class="integration-btn">Coming Soon</a>
					</div><!--end of border-->
				</div><!--end of col-4-->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/stripe-logo.png') }}" alt="stripe"  width="150">
						<p>
							Take payments from your clients directly through stripe. We do not take an additional fee!
						</p>
						<a href="{{ route('admin.payment-gateway-credential.index') }}" class="integration-btn">Configure</a>
					</div><!--end of border-->
				</div><!--end of col-4-->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/paypal-logo.png') }}" alt="PayPal"  width="150">
						<p>
							Take payments from your clients directly through PayPal. We do not take an additional fee!
						</p>
						<a href="{{ route('admin.payment-gateway-credential.index') }}" class="integration-btn">Configure</a>
					</div><!--end of border-->
				</div><!--end of col-4-->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/razorpay-logo.png') }}" alt="Razorpay"  width="150">
						<p>
							Take payments from your clients directly through Razorpay. We do not take an additional fee!
						</p>
						<a href="{{ route('admin.payment-gateway-credential.index') }}" class="integration-btn">Configure</a>
					</div><!--end of border-->
				</div><!--end of col-4-->
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/paystack-logo.png') }}" alt="Paystack"  width="150">
						<p>
							Take payments from your clients directly through Paystack. We do not take an additional fee!
						</p>
						<a href="{{ route('admin.payment-gateway-credential.index') }}" class="integration-btn">Configure</a>
					</div><!--end of border-->
				</div><!--end of col-4-->
			</div><!--end of row-->
                        
                        
			<div class="row m-0">			
				<h4 class="border-bottom p-b-10 m-b-10">Others</h4>	
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="border p-3">
						<img src="{{ asset('img/gc-logo.png') }}" alt="Google Calendar"  width="150">
						<p>
							Connect your goolge calendar to see all events between indema and google calendar.
						</p>
                                                <?php if($google_token) { ?>
                                                <a href="javascript:void(0)" id="disconnect_calendar" onclick="disconnectCalendar()"  class="integration-btn">Disconnect Calendar</a>
                                                <?php //} else if($client && ($company->id == 113 || $company->id == 16)){
                                                    } else if($client){ ?>
                                                    <a href="<?php echo $client->createAuthUrl(); ?>" class="integration-btn">Configure</a>
                                                 <?php } else { ?>
                                                    <a href="javascript:void(0)" class="integration-btn">Please Contact Super Admin to enable Goolge calendar Settings</a>
                                                   
                                                 <?php } ?>
						
					</div><!--end of border-->
				</div><!--end of col-4-->
			</div><!--end of row-->
		</div><!--end of col-12-->
	</div><!--end of row-->
@endsection

@push('footer-script')

<script>
    $('#save-form-2').click(function () {
        $.easyAjax({
            url: '{{route('member.quickbooks.update', [$qbo->id])}}',
            container: '#UpdateQuickBookSettings',
            type: "POST",
            redirect: true,
            
            data: $('#UpdateQuickBookSettings').serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
    
     function  disconnectCalendar(id) {

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the disconnect calendar!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, disconnect it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = '{{ route('admin.google-disconnect') }}';
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token},
                        redirect: true,
                        success: function (response) {
                            window.location.reload();
                        }
                    });
                }
            });
        }
    
    
    
</script>

@if(\Session::has('message'))
<script>
    toastr.success("{{  \Session::get('message') }}");
</script>
@endif

@if(\Session::has('qb_error'))
<script>
    toastr.error("{{  \Session::get('qb_error') }}");
</script>
@endif

@if(\Session::has('error_message'))
<script>
    toastr.error("{{  \Session::get('error_message') }}");
</script>
@endif

@endpush
