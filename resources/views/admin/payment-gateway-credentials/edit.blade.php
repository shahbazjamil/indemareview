@extends('layouts.app')

@section('page-title')
    <div class="row bg-title p-b-0">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-6 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.settings.index') }}">@lang('app.menu.settings')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading p-t-10 p-b-10">@lang('app.menu.onlinePayment')</div>

                <div class="vtabs customvtab m-t-10">

                    @include('sections.payment_setting_menu')

                    <div class="tab-content p-0 p-t-20">
                        <div id="vhome3" class="tab-pane active">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="white-box p-0">

                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12 ">
                                                {!! Form::open(['id'=>'updateSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h3 class="box-title text-success">Paypal</h3>
                                                            <hr class="m-t-0 m-b-20">
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Paypal Client Id</label>
                                                                <input type="password" name="paypal_client_id" id="paypal_client_id"
                                                                       class="form-control" value="{{ $credentials->paypal_client_id }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Paypal Secret</label>
                                                                <input type="password" name="paypal_secret" id="paypal_secret"
                                                                       class="form-control" value="{{ $credentials->paypal_secret }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>

                                                            <div class="form-group">
                                                                <h5>Select environment</h5>
                                                                <select class="form-control" name="paypal_mode" id="paypal_mode" data-style="form-control">
                                                                    <option value="sandbox" @if($credentials->paypal_mode == 'sandbox') selected @endif>Sandbox</option>
                                                                    <option value="live" @if($credentials->paypal_mode == 'live') selected @endif>Live</option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="mail_from_name">@lang('app.webhook')</label>
                                                                <p class="text-bold">{{ route('verify-ipn') }}</p>
                                                                <p class="text-info">(@lang('messages.addPaypalWebhookUrl'))</p>
                                                            </div>
                                                        </div>
                                                        <!--/span-->

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label" >@lang('modules.payments.paypalStatus')</label>
                                                                <div class="switchery-demo">
                                                                    <input type="checkbox" name="paypal_status" @if($credentials->paypal_status == 'active') checked @endif class="js-switch " data-color="#00c292" data-secondary-color="#f96262"  />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 m-t-20">
                                                            <h3 class="box-title text-warning">Stripe</h3>
                                                            <hr class="m-t-0 m-b-20">
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Stripe Live Publishable Key</label>
                                                                <input type="text" name="stripe_client_id" id="stripe_client_id"
                                                                       class="form-control" value="{{ $credentials->stripe_client_id }}">

                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Stripe Live Secret Key</label>
                                                                <input type="text" name="stripe_secret" id="stripe_secret"
                                                                       class="form-control" value="{{ $credentials->stripe_secret }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Stripe Webhook Secret</label>
                                                                <input type="text" name="stripe_webhook_secret" id="stripe_webhook_secret"
                                                                       class="form-control" value="{{ $credentials->stripe_webhook_secret }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="mail_from_name">@lang('app.webhook')</label>
                                                                <p class="text-bold">{{ route('verify-webhook') }}</p>
                                                                <p class="text-info">(@lang('messages.addStripeWebhookUrl'))</p>
                                                            </div>
                                                        </div>
                                                        <!--/span-->

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label" >@lang('modules.payments.stripeStatus')</label>
                                                                <div class="switchery-demo">
                                                                    <input type="checkbox" name="stripe_status" @if($credentials->stripe_status == 'active') checked @endif class="js-switch " data-color="#00c292" data-secondary-color="#f96262"  />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                         <div class="col-md-12 m-t-20">
                                                            <h3 class="box-title text-warning">Plaid</h3>
                                                            <hr class="m-t-0 m-b-20">
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Plaid Live Client Id</label>
                                                                <input type="text" name="plaid_client_id" id="plaid_client_id"
                                                                       class="form-control" value="{{ $credentials->plaid_client_id }}">

                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Plaid Live Secret Key</label>
                                                                <input type="text" name="plaid_secret" id="plaid_secret"
                                                                       class="form-control" value="{{ $credentials->plaid_secret }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>
                                                        </div>

                                                        

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label" >Plaid  Status</label>
                                                                <div class="switchery-demo">
                                                                    <input type="checkbox" name="plaid_status" @if($credentials->plaid_status == 'active') checked @endif class="js-switch " data-color="#00c292" data-secondary-color="#f96262"  />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 m-t-20">
                                                            <h3 class="box-title text-info">@lang('modules.paymentSetting.razorpay')</h3>
                                                            <hr class="m-t-0 m-b-20">
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="">Razorpay Key</label>
                                                                <input type="text" name="razorpay_key" id="razorpay_key"
                                                                       class="form-control" value="{{ $credentials->razorpay_key }}">

                                                            </div>
                                                        </div>
                                                        
                                                        

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Razorpay Secret Key</label>
                                                                <input type="text" name="razorpay_secret" id="razorpay_secret"
                                                                       class="form-control" value="{{ $credentials->razorpay_secret }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Razorpay Webhook Secret Key</label>
                                                                <input type="text" name="razorpay_webhook_secret" id="razorpay_webhook_secret"
                                                                       class="form-control" value="{{ $credentials->razorpay_webhook_secret }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label" >@lang('modules.payments.razorpayStatus')</label>
                                                                <div class="switchery-demo">
                                                                    <input type="checkbox" name="razorpay_status" @if($credentials->razorpay_status == 'active') checked @endif class="js-switch " data-color="#00c292" data-secondary-color="#f96262"  />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 m-t-20">
                                                            <h3 class="box-title text-success">@lang('modules.paymentSetting.payStack')</h3>
                                                            <hr class="m-t-0 m-b-20">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="">Paystack Key</label>
                                                                <input type="text" name="paystack_client_id" id="paystack_client_id"
                                                                       class="form-control" value="{{ $credentials->paystack_client_id }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Paystack Secret Key</label>
                                                                <input type="password" name="paystack_secret" id="paystack_secret"
                                                                       class="form-control" value="{{ $credentials->paystack_secret }}">
                                                                <span class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Paystack merchant email</label>
                                                                <input type="text" name="paystack_merchant_email" id="paystack_merchant_email"
                                                                       class="form-control" value="{{ $credentials->paystack_merchant_email }}">
                                                            </div>
                                                        </div>


                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label" >@lang('modules.paymentSetting.paystackStatus')</label>
                                                                <div class="switchery-demo">
                                                                    <input type="checkbox" name="paystack_status" @if($credentials->paystack_status == 'active') checked @endif class="js-switch " data-color="#00c292" data-secondary-color="#f96262"  />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mail_from_name">@lang('app.webhook')</label>
                                                                <p class="text-bold">{{ route('save_paystack-webhook') }}</p>
                                                                <p class="text-info">(@lang('messages.addPaystackWebhookUrl')
                                                                    )</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mail_from_name">@lang('app.callback')</label>
                                                                <p class="text-bold">{{ route('client.paystack.callback') }}</p>
                                                                <p class="text-info">(@lang('messages.addPaystackCallbackUrl')
                                                                    )</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--/row-->

                                                </div>
                                                <div class="form-actions m-t-20">
                                                    <button type="submit" id="save-form-2" class="btn btn-success"><i class="fa fa-check"></i>
                                                        @lang('app.save')
                                                    </button>

                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->


    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="leadStatusModal" role="dialog" aria-labelledby="myModalLabel"
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
    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
    <script>
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());

        });
        $('#save-form-2').click(function () {
            $.easyAjax({
                url: '{{ route('admin.payment-gateway-credential.update', [$credentials->id])}}',
                container: '#updateSettings',
                type: "POST",
                redirect: true,
                data: $('#updateSettings').serialize()
            })
        });
    </script>
@endpush

