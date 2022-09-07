@extends('layouts.front-app')
@section('content')
    <section class="section bg-img" id="section-contact" style="background-image: url({{ asset('front/img/bg-cup.jpg') }})" data-overlay="8">
        <div class="container">
            <div class="row gap-y">

                <div class="col-12 col-md-8 offset-md-3 form-section">

                    {!! Form::open(['id'=>'register','class'=>'row', 'method'=>'POST']) !!}
                    <input type="hidden" value="" name="lm_data" id="lm_data">
                        <div class="col-12 col-md-10 bg-white px-30 py-45 rounded">
                            <h2 class="text-center m-b-15">Sign Up</h2>
                            <p id="alert"></p>
                            <div id="form-box">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="{{ __('modules.client.companyName') }}">
                                </div>
                                <div class="form-group">
                                    <input class="form-control form-control-lg" type="email" id="company_email" name="company_email" placeholder="{{ __('app.yourEmailAddress') }}">
                                </div>
                                @if(module_enabled('Subdomain'))
                                    <div class="form-group">
                                        <div class="sub-domain">
                                            <input type="text" class="form-control" placeholder="your-login-url" name="sub_domain">
                                            @if(function_exists('get_domain'))
                                                <span class="domain-text">.{{ get_domain() }}</span>
                                            @else
                                                <span class="domain-text">.{{ $_SERVER['SERVER_NAME'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="{{__('modules.client.password')}}">
                                </div>

                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" placeholder="{{__('app.confirmPassword')}}">
                                </div>

                                @if(!is_null($global->google_recaptcha_key))
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="{{ $global->google_recaptcha_key }}"></div>
                                </div>
                                @endif
                                
                            <div class="form-group mb-4">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" id="accept" name="accept" type="checkbox" required="required">
                                        <small class="text-muted">
                                            By checking this box, you agree to our <a href="https://indema.co/terms-of-use " target="_blank">terms</a> and <a href="https://indema.co/privacy-policy" target="_blank">privacy</a> policies.
                                        </small>
                                    </label>
                                </div>
                            </div>
                            <br>

                                <button class="btn btn-lg btn-block btn-primary" type="button" id="save-form">@lang('app.signup')</button>

                            </div>

                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
@push('footer-script')
    <script>
        
    $(document).ready(() => {
    
        var lm_data = lmFinished(); 
        $("#lm_data").val(lm_data);
    
    });
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
@endpush
