<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title> {{ ucwords($setting->company_name)}}</title>

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
            color: #8a1f11 !important;
        }

        {!! $setting->auth_css_theme_two !!}

    </style>
</head>

<body id="home">


<!-- Topbar -->

<!-- END Topbar -->

<!-- Header -->
<!-- END Header -->


<section class="login-section bg-white" id="section-contact">
    <div class="container">
        <div class="login-box mt-5 bg-white form-section">
            <h4 class="mb-0 border-bottom">
                @lang('app.login')
            </h4>
            <p style="display :none" class="c-red">Planned maintenance on Saturday Morning: June 19th, 2021 from 02:00 AM to 06:00 AM PST</p>
            <form class="form-horizontal form-material mt-5" id="loginform" action="{{ route('login') }}" method="POST">
                {{ csrf_field() }}


                @if (session('message'))
                    <div class="alert alert-danger m-t-10">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <div class="col-xs-12">
                        <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" autofocus required="" placeholder="@lang('app.email')">
                        @if ($errors->has('email'))
                            <div class="help-block with-errors">{{ $errors->first('email') }}</div>
                        @endif

                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" id="password" type="password" name="password" required="" placeholder="@lang('modules.client.password')">
                        @if ($errors->has('password'))
                            <div class="help-block with-errors">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                </div>
                @if($global->google_recaptcha_key)
                    <div class="form-group {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                        <div class="col-xs-12">
                            <div class="g-recaptcha"
                                 data-sitekey="{{ $global->google_recaptcha_key }}">
                            </div>
                            @if ($errors->has('g-recaptcha-response'))
                                <div class="help-block with-errors">{{ $errors->first('g-recaptcha-response') }}</div>
                            @endif
                        </div>
                    </div>
                @endif
                @if ($errors->has('already_logged'))
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="help-block with-errors">{{ $errors->first('already_logged') }}
                                <input id="checkbox-signup" type="checkbox" name="already_logged" value="1">
                            <label for="checkbox-signup" class="text-dark"> Yes </label>
                            </div>
<!--                            <div class="checkbox checkbox-primary float-left p-t-0">
                                <input id="checkbox-signup" type="checkbox" name="already_logged" }}>
                                <label for="checkbox-signup" class="text-dark"> Yes </label>
                            </div>-->
                            
                        </div>
                    </div>
                @endif
                
                <div class="form-group">
                    <div class="col-xs-12 border-top mt-5 pt-4 f-13">
                        <div class="checkbox checkbox-primary float-left p-t-0">
                            <input id="checkbox-signup" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="checkbox-signup" class="text-dark"> @lang('app.rememberMe') </label>
                        </div>
                        <a href="{{ route('password.request') }}"  class="text-dark float-right"><i class="fa fa-lock m-r-5"></i> @lang('app.forgotPassword')?</a> </div>
                </div>
                <div class="form-group text-center">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block btn-rounded text-uppercase waves-effect waves-light" type="submit">@lang('app.login')</button>
                    </div>
                </div>
                <div class="row">
                    {{--<div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">--}}
                        <script>
                            var facebook = "{{ route('social.login', 'facebook') }}";
                            var google = "{{ route('social.login', 'google') }}";
                            var twitter = "{{ route('social.login', 'twitter') }}";
                            var linkedin = "{{ route('social.login', 'linkedin') }}";
                        </script>
                        @if(isset($socialAuthSettings))
                            <div class="col-xs-12 col-sm-12 col-md-6 m-t-10 text-center mb-1">
                                @if($socialAuthSettings->facebook_status == 'enable')
                                    <a href="javascript:;" class="btn btn-primary btn-facebook" data-toggle="tooltip" title="@lang('app.loginWithFacebook')" onclick="window.location.href = facebook;" data-original-title="@lang('app.loginWithFacebook')">@lang('app.loginWithFacebook') <i aria-hidden="true" class="zmdi zmdi-facebook"></i> </a>
                                @endif
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 m-t-10 text-center mb-1">
                                @if($socialAuthSettings->google_status == 'enable')
                                    <a href="javascript:;" class="btn btn-primary btn-google" data-toggle="tooltip" title="@lang('app.loginWithGoogle')" onclick="window.location.href = google;" data-original-title="@lang('app.loginWithGoogle')">@lang('app.loginWithGoogle') <i aria-hidden="true" class="zmdi zmdi-google"></i> </a>
                                @endif
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 m-t-10 text-center mb-1">
                                    @if($socialAuthSettings->twitter_status == 'enable')
                                        <a href="javascript:;" class="btn btn-primary btn-twitter" data-toggle="tooltip" title="@lang('app.loginWithTwitter')" onclick="window.location.href = twitter;" data-original-title="@lang('app.loginWithTwitter')">@lang('app.loginWithTwitter') <i aria-hidden="true" class="zmdi zmdi-twitter"></i> </a>
                                    @endif
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 m-t-10 text-center mb-1">
                                    @if($socialAuthSettings->linkedin_status == 'enable')
                                        <a href="javascript:;" class="btn btn-primary btn-linkedin" data-toggle="tooltip" title="@lang('app.loginWithTwitter')" onclick="window.location.href = linkedin;" data-original-title="@lang('app.loginWithTwitter')">@lang('app.loginWithTwitter') <i aria-hidden="true" class="zmdi zmdi-linkedin"></i> </a>
                                    @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center signup-con">
                        <p>Don't have an account? <a href="{{ route('front.signup.index') }}"
                                                     class="text-primary m-l-5"><b>Designer Sign Up</b></a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- END Main container -->

<!-- Cta -->
{{--@include('saas.sections.cta')--}}
<!-- End Cta -->

<!-- Footer -->
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

<script>
    $('#save-form').click(function () {


        $.easyAjax({
            url: '{{route('front.signup.store')}}',
            container: '.form-section',
            type: "POST",
            data: $('#register').serialize(),
            messagePosition: "inline",
            success: function (response) {
                if(response.status == 'success'){
                    $('#form-box').remove();
                }else if (response.status == 'fail')
                {
                    @if(!is_null($global->google_recaptcha_key))
                            grecaptcha.reset();
                    @endif

                }
            }
        })
    });
</script>

</body>
</html>
