<style>
    .stripe-button-el{
        display: none;
    }
    .displayNone {
        display: none;
    }
    .checkbox-inline, .radio-inline {
        vertical-align: top !important;
    }
    .payment-type {
        border: 1px solid #e1e1e1;
        padding: 20px;
        background-color: #f3f3f3;
        border-radius: 10px;

    }
    .box-height {
        height: 78px;
    }
    .button-center{
        display: flex;
        justify-content: center;
    }
    .paymentMethods{display: none; transition: 0.3s;}
    .paymentMethods.show{display: block;}

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
    .paystack-form {
        display: inline-block;
        position: relative;
    }
    .payment-type {
        margin: 0 5px;
        width: 100%;
    }
    .payment-type button{
        margin: 0 5px;
        float: none;
    }
</style>
<div id="event-detail">
    <div class="modal-body">
        <div class="form-body">
            <div class="row stripePaymentForm">
<!--                @if($stripeSettings->api_key != null && $stripeSettings->api_secret != null  && $stripeSettings->stripe_status == 'active')-->
                    <div class="m-l-10">
                        <form id="stripe-form" action="{{ route('admin.billing.save-strip-card') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="row" style="margin-bottom:20px;">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Name On Card</label>
                                        <input type="text" id="card-holder-name" required name="card_holder_name" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-xs-12">
                                    <small>* Address country must be a valid <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank">2-alphabet ISO-3166 code</a></small>
                                </div>
                            </div>

                            <div class="flex flex-wrap mb-6">
                                <label for="card-element" class="block text-gray-700 text-sm font-bold mb-2">
                                    Card Info
                                </label>
                                <div id="card-element" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></div>
                                <div id="card-errors" class="text-red-400 text-bold mt-2 text-sm font-medium"></div>
                            </div>

                            <!-- Stripe Elements Placeholder -->
                            <div class="flex flex-wrap mt-6" style="margin-top: 15px; text-align: center">
                                <button data-secret="{{$intent->client_secret }} " type="button" id="card-button" class="btn btn-success inline-block align-middle text-center select-none border font-bold whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700">
                                    <i class="fa fa-cc-stripe"></i> Change
                                </button>
                            </div>
                            
                        </form>

                    </div>
<!--                @endif-->
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
<!--<script src="{{ asset('saas/vendor/jquery/jquery.min.js') }}"></script>-->
<script src="https://js.stripe.com/v3/"></script>

<!--@if($stripeSettings->stripe_status == 'active')-->
<script>
    
    $(document).ready(function () {
        
    setTimeout(function(){
    
    var form = document.getElementById('stripe-form');
    
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
     }, 1000);
    
     });

</script>
<!--@endif-->

