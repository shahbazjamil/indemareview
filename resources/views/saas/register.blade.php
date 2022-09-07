<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title> {{ __($pageTitle) }} | {{ ucwords($setting->company_name)}}</title>

    <!-- Bootstrap CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/animate-css/animate.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick-theme.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/fonts/flaticon/flaticon.css') }}">
    <link href="{{ asset('front/plugin/froiden-helper/helper.css') }}" rel="stylesheet">
    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/css/main.css') }}">
    <!-- Template Font Family  -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&family=Rubik:wght@400;500&display=swap" rel="stylesheet">
    <link type="text/css" rel="stylesheet" media="all"
          href="{{ asset('saas/vendor/material-design-iconic-font/css/material-design-iconic-font.min.css') }}">

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <style>
        :root {
            --main-color: {{ $frontDetail->primary_color }};
        }
        .help-block {
            color: #000 !important;font-size:13px;
        }.g-recaptcha>div {
    height: auto !important;
}
.with-errors {
    color : #f00 !important;
}

    </style>
    <script src="{{ asset('js/lm.js') }}" async='async'></script>
</head>

<body id="home" class="register-page">


<!-- Topbar -->
@include('sections.saas.saas_header')
<!-- END Topbar -->

<!-- Header -->
<!-- END Header -->


<section class="sp-100- py-5 login-section bg-white" id="section-contact">
    <div class="container">
	<div class="row">
		<div class="col-md login-box mt-5">
			<h4 class="mb-0 px-0 text-left">Ready to try indema?</h4>
                        <p>You've selected the {{(strtolower($package->name))}} package.<br/>Please enter the required information, and proceed to check out.</p>
			<div class="package-card d-inline-flex">
				<div class="col-auto">
					<h1>{{ucfirst(strtolower($package->name))}}</h1>
					
                                        @if($is_annual == 0)
                                            ${{$package->monthly_price}}<span>Monthly</span>
                                        @else
                                            ${{$package->annual_price}}<span>Yearly</span>
                                        @endif
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
			</div><!--end of package-card-->
			<div class="due">
                            <!--end of form-row-->
				<div class="form-row">
					<span class="col-auto"><b>Due Today:</b></span>
                                        @if($is_annual == 0)
                                            <span class="col-auto"><b>${{$package->monthly_price}}/Mo</b></span>
                                        @else
                                            <span class="col-auto"><b>${{$package->annual_price}}/Yr</b></span>
                                        @endif
				</div><!--end of form-row-->
			</div><!--end of due-->
    <form id="register" action="{{route('front.signup.store')}}" method="POST">
        <p class="{{ $errors->has('indema_terms') ? 'has-error' : '' }}">
            By clicking "Sign up" and opening and account on indema, you agree to the indema <a href="https://indema.co/terms-of-use" target="_blank">terms of use</a>.<b class="d-block"><input type="checkbox" id="indema_terms" name="indema_terms" required="required" /> I agree to the above</b>
            @if ($errors->has('indema_terms'))
                <div style="color : #f00 !important" class="help-block with-errors">This section is required</div>
            @endif
        </p>
						
        <p class="{{ $errors->has('cancel_subscription') ? 'has-error' : '' }}">
            You are signing up for an account for indema.co. You understand and agree that there are no-refunds for your subscription. You will be charged according to your plan you have selected. If you choose to cancel your account, no refunds for payments already submitted will be approved, and your account will cancel at the end of the current billing cycle.<b class="d-block"><input type="checkbox" id="cancel_subscription" name="cancel_subscription" required="required" /> I agree to the above</b>
             @if ($errors->has('indema_terms'))
                <div style="color : #f00 !important" class="help-block with-errors">This section is required</div>
            @endif
        </p>
		    	
		</div><!--end of col-->
		
        <div class="login-box mt-5 bg-white form-section col-auto">
            <h4 class="mb-0 pb-0">
                <!--@lang('app.signup')-->Your Details
            </h4>
<!--            {!! Form::open(['id'=>'register', 'method'=>'POST', 'url'=>'https://stagin.indema.co/signup' ]) !!}-->
        
            <input type="hidden" value="" name="lm_data" id="lm_data">
             <input type="hidden" value="{{$is_annual}}" name="is_annual" id="is_annual">
            <input type="hidden" value="{{$package->id}}" name="package_id" id="package_id">
            <input type="hidden" value="{{$plan}}" name="plan" id="plan">
            {{ csrf_field() }}
            
            
            <div class="row">
                <div id="alert" class="col-lg-12 col-12">

                </div>
                
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    <?php Session::forget('error');?>
                @endif
                
                <div class="col-12 mt-3" id="form-box">
                    <div class="form-group {{ $errors->has('company_name') ? 'has-error' : '' }}">
                        <label for="company_name" class="">Business Name*</label>
                        <input type="text" name="company_name" id="company_name" placeholder="{{ __('modules.client.companyName') }}" class="form-control">
                        @if ($errors->has('company_name'))
                            <div class="help-block with-errors">{{ $errors->first('company_name') }}</div>
                        @endif
                    </div>
                    @if(module_enabled('Subdomain'))
                    <div class="form-group {{ $errors->has('sub_domain') ? 'has-error' : '' }}">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="subdomain" name="sub_domain" id="sub_domain">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">.{{ get_domain() }}</span>
                            </div>
                            @if ($errors->has('sub_domain'))
                                <div class="help-block with-errors">{{ $errors->first('sub_domain') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="form-group {{ $errors->has('company_email') ? 'has-error' : '' }}">
                        <label for="email" class="">Business Email Address*</label>
                        <input type="email" name="company_email" id="company_email" placeholder="{{ __('app.yourEmailAddress') }}" class="form-control">
                        @if ($errors->has('company_email'))
                            <div class="help-block with-errors">{{ $errors->first('company_email') }}</div>
                        @endif
                    </div>
                    <div class="form-group w-50 ml {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="password" class="">Create Password*</label>
                        <input type="password" class="form-control " id="password" name="password" placeholder="{{__('modules.client.password')}}">
                        @if ($errors->has('password'))
                            <div class="help-block with-errors">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <div class="form-group w-50 {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                        <label for="password_confirmation" class="">Confirm Password*</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{__('app.confirmPassword')}}">
                        @if ($errors->has('password_confirmation'))
                            <div class="help-block with-errors">{{ $errors->first('password_confirmation') }}</div>
                        @endif
                    </div>
                    <div class="form-group {{ $errors->has('hear_about') ? 'has-error' : '' }}">
                        <label for="hear_about" class="">How did you hear about indema?</label>
                        <input type="text" class="form-control" id="hear_about" name="hear_about" placeholder="How did you hear about indema?" />
                        @if ($errors->has('hear_about'))
                            <div class="help-block with-errors">{{ $errors->first('hear_about') }}</div>
                        @endif
                    </div>
                    <div class="form-group w-50 ml">
                        <label for="card-holder-name" class="">Name On Card*</label>
                        <input id="card-holder-name" type="text" class="form-control" />
                    </div>
                    <div class="form-group w-50">
                        <label for="coupon" class="">Promo code</label>
                        <input id="coupon" name="coupon" type="text" class="form-control" />
                    </div>
                    <div class="w-100 d-flex justify-content-between">
                        <label class="">Payment Information</label>
                    </div>
                    <div id="card-element" class="form-control form-group"></div>
                     @if ($errors->has('stripeToken'))
                        <div class="form-group">
                            <div class="help-block with-errors">Payment information wrong</div>
                        </div>
                    @endif
                    <div class="help-block" id="card-errors" role="alert"></div>
    
                    @if(!is_null($global->google_recaptcha_key) && 1!=1)
                        <div class="form-group {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                            <div class="g-recaptcha" data-sitekey="{{ $global->google_recaptcha_key }}"></div>
                            @if ($errors->has('g-recaptcha-response'))
                                <div class="help-block with-errors">{{ $errors->first('g-recaptcha-response') }}</div>
                            @endif
                        </div>
                    @endif
                    
                    <div class="form-group border-top mt-2 pt-2 f-13 mb-1 {{ $errors->has('accept') ? 'has-error' : '' }}">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" id="accept" name="accept" type="checkbox" required="required">
                                <span class="text-muted">
                                    By checking this box, you agree to our <a href="https://indema.co/terms-of-use " target="_blank">terms</a> and <a href="https://indema.co/privacy-policy" target="_blank">privacy</a> policies.
                                </span>
                            </label>
                            @if ($errors->has('accept'))
                                <div class="help-block with-errors">{{ $errors->first('accept') }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <button type="button" id="card-button" class="btn btn-lg btn-custom mt-2" data-secret="{{$intent->client_secret }} ">
                       Sign Up
                    </button>
                   
                    <span class="registerform-note"><img src="{{ asset('saas/img/shield-img.png') }}" alt="" /> Payments are secure and encrypted</span>
                </div>
            </div>
            </form>
            
<!--            {!! Form::close() !!}-->
        </div>
	</div><!--end of row-->
    </div>
</section>

<!-- END Main container -->

<!-- Cta -->
{{--@include('saas.sections.cta')--}}
<!-- End Cta -->

<!-- Footer -->
@include('sections.saas.saas_footer')
<!-- END Footer -->



<!-- Scripts -->
<script src="{{ asset('saas/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('saas/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('saas/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('saas/vendor/wowjs/wow.min.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('saas/js/main.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<!-- Global Required JS -->

<script src="https://js.stripe.com/v3/"></script>

<script>
    
    var form = document.getElementById('register');
    
    var cashier_key = "{{config('cashier.key')}}";
    const stripe = Stripe(cashier_key);

    const elements = stripe.elements();
    const cardElement = elements.create('card');

    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;

    cardButton.addEventListener('click', async (e) => {
        
        e.preventDefault();
        
        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: { name: cardHolderName.value }
                }
            }
        );

        if (error) {
            // Display "error.message" to the user...
        } else {
            // The card has been verified successfully...
            
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', setupIntent.payment_method);
            form.appendChild(hiddenInput);
            form.submit();
        }
    });
  </script>


<script>
    $(document).ready(() => {
    
        var lm_data = lmFinished(); 
        $("#lm_data").val(lm_data);
    
    });
    
//    $('#save-form').click(function () {
//
//
//        $.easyAjax({
//            url: '{{route('front.signup.store')}}',
//            container: '.form-section',
//            type: "POST",
//            data: $('#register').serialize(),
//            messagePosition: "inline",
//            success: function (response) {
//                if (response.status == 'success') {
//                    $('#form-box').remove();
//                } else if (response.status == 'fail') {
//                    @if(!is_null($global->google_recaptcha_key))
//                    grecaptcha.reset();
//                    @endif
//
//                }
//            }
//        })
//    });
</script>

</body>
</html>
