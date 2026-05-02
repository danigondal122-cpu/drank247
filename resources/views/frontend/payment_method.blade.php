<link rel="stylesheet" href="{{ asset('plugins/jquery-confirm/jquery-confirm.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom_fr.css') }}">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        list-style: none;
        text-decoration: none;
        outline: none;
        border: none;
    }

    body {
        height: 100%;
    }
</style>

<main class="main-payment_method">
    <div class="drank_payment-card">

        <div class="drank_payment-header">
            <a href="{{ url('cart') }}"><img class="svg back_arrow"
                    src="{{ url('uploads/paymentMethodicon/arrow.svg') }}" alt=""></a>
            <span>Betaalmenthoden</span>
        </div>
        <div class="drank_payment-body">
            <input type="hidden" id="orderId" value="{{ isset($orderId) ? $orderId : '' }}">
            @foreach ($paymentmethods as $row)
                @if ($row->method_name == 'ideal')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('IDEAL')">
                            <img src="{{ url('uploads/paymentMethodicon/ideal.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profile_brands/100x100/1.png" alt=""> -->
                            <p>{{ __('messages.ideal') }}</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'cod')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('cash')">
                            <img src="{{ url('uploads/paymentMethodicon/coin.png') }}" alt="">
                            <p>{{ __('messages.cash_on_delivery') }}</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'credit_card')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('credit_card')">
                            <img src="{{ url('uploads/paymentMethodicon/credit_card.png') }}" alt="">
                            <p>{{ __('messages.credit_card') }}</p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'pin')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('pin')">
                            <img src="{{ url('uploads/paymentMethodicon/mobile-card.png') }}" alt="">
                            <p>{{ __('messages.pin_at_door') }}</p>
                        </a>
                    </div>
                @endif


                @if ($row->method_name == 'paypal')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('PAYPAL_EXPRESS_CHECKOUT')">
                            <img src="{{ url('uploads/paymentMethodicon/paypal.png') }}" alt="">
                            <p>{{ __('messages.paypal') }}</span></p>
                        </a>
                    </div>
                @endif



                @if ($row->method_name == 'bitpay')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('bitpay')">
                            <img src="{{ url('uploads/paymentMethodicon/Bitpaybutton.svg') }}" alt="">
                            <p>BitPay</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'gpay')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('gpay')">
                            <img src="{{ url('uploads/paymentMethodicon/gpay.png') }}" alt="">
                            <p>Google Pay</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'bancontact')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('bancontact')">
                            <img src="{{ url('uploads/paymentMethodicon/bancontact.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profile_brands/100x100/2.png" alt=""> -->
                            <p>Bancontact</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'giropay')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('giropay')">
                            <img src="{{ url('uploads/paymentMethodicon/giropay.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profile_brands/100x100/3.png" alt=""> -->
                            <p>Giropay</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'sofort_banking')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('sofort_banking')">
                            <img src="{{ url('uploads/paymentMethodicon/sofort_banking.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profile_brands/100x100/4.png" alt=""> -->
                            <p>SOFORT Banking</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'trustly')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('trustly')">
                            <img src="{{ url('uploads/paymentMethodicon/trustly.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profile_brands/100x100/213.png" alt=""> -->
                            <p>Trustly</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'eps_uberweisung')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('eps_uberweisung')">
                            <img src="{{ url('uploads/paymentMethodicon/eps_uberweisung.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profile_brands/100x100/79.png" alt=""> -->
                            <p>EPS Uberweisung</span></p>
                        </a>
                    </div>
                @endif

                @if ($row->method_name == 'przelewy24')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('przelewy24')">
                            <img src="{{ url('uploads/paymentMethodicon/przelewy24.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profile_brands/100x100/93.png" alt=""> -->
                            <p>Przelewy24</span></p>
                        </a>
                    </div>
                @endif

                <!--@if ($row->method_name == 'idin')
<div class="drank_payment-button-div">
                    <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('IDIN')">
                         <img src="{{ url('uploads/paymentMethodicon/ideal.png') }}" alt="">
                        <img src="https://static.pay.nl/payment_profile_brands/100x100/1.png" alt="">
                        <p>IDIN</span></p>
                    </a>
                </div>
@endif-->

                @if ($row->method_name == 'klarna')
                    <div class="drank_payment-button-div">
                        <a href="javascript:;" class="drank_payment-btn" onclick="Checkout('klarna')">
                            <img src="{{ url('uploads/paymentMethodicon/klarna.png') }}" alt="">
                            <!-- <img src="https://static.pay.nl/payment_profiles/100x100/1717.png" alt=""> -->
                            <p>Klarna</span></p>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</main>
<script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" data-auto-a11y="true"></script>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/page/root.js') }}"></script>
<script src="{{ asset('plugins/block-ui/jquery.blockUI.min.js') }}"></script>
<script src="{{ url('js/page/common.js') }}"></script>
<script src="{{ url('plugins/jquery-confirm/jquery-confirm.min.js') }}"></script>
<script src="https://secure.docdatapayments.com/cse/{{ env('CM_TEST_ACCOUNT_NAME') }}"></script>
<script async   src="https://pay.google.com/gp/p/js/pay.js"></script>
<script>
    if (performance.navigation.type == 2) {
        location.reload(true);
    }

    function authorizeGooglePayPayment(merchantKey, orderKey, serverUrl, environment, request) {
        const paymentsClient = getGooglePaymentsClient();
        paymentsClient.loadPaymentData(JSON.parse(JSON.stringify(request)))
            .then(function(paymentData) {
                // window.showGooglePaySpinner();
                // handle the response
                processPayment(merchantKey, orderKey, serverUrl, JSON.stringify(paymentData.paymentMethodData));
            })
            .catch(function(e) {
                console.error(e);
            });
    }

    function getGooglePaymentsClient(environment) {
        // if ( paymentsClient === null ) {
        paymentsClient = new google.payments.api.PaymentsClient({
            environment: environment
        });
        // }
        return paymentsClient;
    }

    function processPayment(merchantKey, orderKey, serverUrl, paymentMethodData) {
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4) {
                console.log(xhttp.responseText);
                console.debug("Got response from Payment Server, polling order status...");
                window.pollOrderStatus();
            }

        };

        var authorizationUrl = serverUrl + "/mobile/googlepay/merchants/" + merchantKey + "/payments/" + orderKey +
            "/authorize";

        xhttp.open("POST", authorizationUrl, true);
        xhttp.setRequestHeader("Content-type", "application/json");
        xhttp.send(paymentMethodData);
    }


    function Checkout(paymentmethod) {
        loader_show();

        if (paymentmethod == undefined || paymentmethod == '') {
            $.alert('Please Select Payment Method');
            return;
        }

        let OrderId = $('#orderId').val();
        if (paymentmethod == 'credit_card') {
            window.location.href = '<?= url('Card-details') ?>/' + OrderId;
            return;
        } else if (paymentmethod == 'gpay') {

            let request = {
                "apiVersion": 2,
                "apiVersionMinor": 0,
                "merchantInfo": {
                    "merchantId": "exampleMerchantId",
                    "merchantName": "Example Merchant"
                },
                "allowedPaymentMethods": [{
                    "type": "CARD",
                    "parameters": {
                        "allowedAuthMethods": ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                        "allowedCardNetworks": ["MASTERCARD", "VISA"]
                    },
                    "tokenizationSpecification": {
                        "type": "PAYMENT_GATEWAY",
                        "parameters": {
                            "gateway": "example",
                            "gatewayMerchantId": "exampleGatewayMerchantId"
                        }
                    }
                }],
                "transactionInfo": {
                    "totalPriceStatus": "FINAL",
                    "totalPrice": "33.20",
                    "currencyCode": "EUR",
                    "countryCode": "NL"
                }
            };

            let test = authorizeGooglePayPayment('dd34bb4e-6b25-4714-9fe0-b301347ecd09',
                'B31F79922AA87B79B31C2DD14D6110E3', 'https://testsecure.docdatapayments.com:8484', 'TEST', request);

            console.log(test);


            return
        } else if (paymentmethod == 'IDEAL') {
            window.location.href = '<?= url('idin-banks') ?>/' + OrderId;
            return;
        } else if (paymentmethod == 'bancontact') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'giropay') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'sofort_banking') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'trustly') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'eps_uberweisung') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'przelewy24') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'PAYPAL_EXPRESS_CHECKOUT') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'klarna') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'IDIN') {
            window.location.href = '<?= url('idin-banks') ?>/' + OrderId;
            return;
        } else if (paymentmethod == 'pin') {
            directPayment(paymentmethod, OrderId);
        } else if (paymentmethod == 'cash') {
            directPayment(paymentmethod, OrderId);
        }

    }

    function directPayment(paymentmethod, OrderId) {
        $.ajax({
            url: '<?= url('paynlPayment') ?>',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                paymentmethod: paymentmethod,
                OrderId: OrderId
            },
            success: function(response) {

                if (response.status == true) {

                    loader_hide();
                    console.log(response);
                    window.location.href = response.redirectUrl;

                }
                if (response.status == false) {
                    loader_hide();
                    $.alert(response.message);

                }
            }
        })
    }

    // function Checkout(paymentmethod) {
    //     loader_show();

    //     if (paymentmethod == undefined || paymentmethod == '') {
    //         $.alert('Please Select Payment Method');
    //         return;
    //     }

    //     let OrderId = $('#orderId').val();
    //     if (paymentmethod == 'credit_card') {
    //         window.location.href = '<?= url('Card-details') ?>/' + OrderId;
    //         return;
    //     } else if (paymentmethod == 'gpay') {

    //         let request = {
    //             "apiVersion": 2,
    //             "apiVersionMinor": 0,
    //             "merchantInfo": {
    //                 "merchantId": "exampleMerchantId",
    //                 "merchantName": "Example Merchant"
    //             },
    //             "allowedPaymentMethods": [{
    //                 "type": "CARD",
    //                 "parameters": {
    //                     "allowedAuthMethods": ["PAN_ONLY", "CRYPTOGRAM_3DS"],
    //                     "allowedCardNetworks": ["MASTERCARD", "VISA"]
    //                 },
    //                 "tokenizationSpecification": {
    //                     "type": "PAYMENT_GATEWAY",
    //                     "parameters": {
    //                         "gateway": "example",
    //                         "gatewayMerchantId": "exampleGatewayMerchantId"
    //                     }
    //                 }
    //             }],
    //             "transactionInfo": {
    //                 "totalPriceStatus": "FINAL",
    //                 "totalPrice": "33.20",
    //                 "currencyCode": "EUR",
    //                 "countryCode": "NL"
    //             }
    //         };

    //         let test = authorizeGooglePayPayment('dd34bb4e-6b25-4714-9fe0-b301347ecd09', 'B31F79922AA87B79B31C2DD14D6110E3', 'https://testsecure.docdatapayments.com:8484', 'TEST', request);

    //         console.log(test);


    //         return
    //     } else if (paymentmethod == 'IDEAL') {
    //         window.location.href = '<?= url('check-idin') ?>/' + OrderId;
    //         return;
    //     }


    //     $.ajax({
    //         url: '<?= url('makePayment') ?>',
    //         type: 'GET',
    //         data: {
    //             paymentmethod: paymentmethod,
    //             OrderId: OrderId
    //         },
    //         success: function(response) {

    //             if (response.status == true) {

    //                 loader_hide();
    //                 console.log(response);
    //                 window.location.href = response.redirectUrl;

    //             }
    //             if (response.status == false) {
    //                 loader_hide();
    //                 $.alert(response.message);

    //             }
    //         }
    //     })

    // }
</script>
