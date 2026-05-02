<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\PlaceOrder;
use App\Mail\PlaceOrderMail;
use App\Models\Cart as CartModel;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Franchise;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Pool;
use App\Models\UsedPromoCode;
use App\Models\PaymentMethod;
use App\Services\Cart;
use App\Services\PayNL;
use Bluem\BluemPHP\Bluem;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $customer = auth('customer')->user();

        if ($request->contact_no) {
            $customer->update([
                'customer_contact_no' => $request->contact_no
            ]);
        }

        $customerAddress = $customer->address()
            ->whereDefault()
            ->first();

        if (!$customerAddress) {
            return response()->json([
                'status' => false,
                'type' => 'NoAddress',
                'message' => 'Please add your address'
            ]);
        }

        $postcode = $customerAddress->post_code;
        $poolsArr = Franchise::poolsArray();
        $pool = Pool::whereIn('id', $poolsArr)
            ->whereAttr($postcode)
            ->first();

        if (!$pool) {
            return response()->json([
                'status' => false,
                'type' => 'invalidPostcode',
                'message' => 'We do not provide our service on your postcode area. Please call customer service for more details.',
            ]);
        }

        $paymentAmount = $request->promo_code
            ? $request->withDiscount_FinalAmount
            : $request->final_amount;

        if ($paymentAmount >= $pool->delivery_start_from) {
            // $orderdetail = CartModel::where('cart_custid', $customer_id)->whereNull('deleted_at')->get();
            // $cart_total_price = CartModel::where('cart_custid', $customer_id)->whereNull('deleted_at')->get()->sum('cart_vattotal');

            $data = [
                'uuid' => Str::uuid(),
                'order_address_id' => $customerAddress->id,
                'order_price' => Cart::payment('total'),
                'order_delivery_charge' => Str::replace(',', '', $request->delivery_charge),
                'order_final_amount' => Str::replace(',', '', $request->final_amount),
                'order_note' => $request->note,
                'order_status' => 0,
                'order_payment' => '',
                'order_channel_order_id' => strtoupper(Str::random(6)),
                'order_discount' => 0.00,
                'order_final_with_discount' => Str::replace(',', '', $request->final_amount),
                'promo_code_id' => '',
            ];

            if ($request->promo_code) {
                $data['order_discount'] = Str::replace(',', '', $request->Discount);
                $data['order_final_with_discount'] = Str::replace(',', '', $request->withDiscount_FinalAmount);
                $data['promo_code_id'] = $request->promo_code;
            }

            // $order = new Order();
            // $order->order_uuid = Str::uuid();
            // $order->order_customerid = $customer_id;
            // $order->franchise_id = "";
            // $order->order_address_id = $customerAddress->id;
            // $order->order_price = str_replace(',', '', number_format($cart_total_price, 2));
            // $order->order_delivery_charge = str_replace(',', '', $request->delivery_charge);
            // $order->order_finalamount = str_replace(',', '', $request->final_amount);
            // $order->order_note = $request->note;
            // $order->order_status = '0';
            // $order->order_payment = "";
            // $order->order_channel_order_id = strtoupper(Str::random(6));
            // if ($request->promo_code != "") {
            //     $order->order_discount = str_replace(',', '', $request->Discount);
            //     $order->order_final_with_discount = str_replace(',', '', $request->withDiscount_FinalAmount);
            //     $order->order_promocode = $request->promo_code;
            // } else {
            //     $order->order_discount = 0.00;
            //     $order->order_final_with_discount = str_replace(',', '', $request->final_amount);
            //     $order->order_promocode = "";
            // }
            // $order->save();
            $order = $customer->orders()->create($data);

            foreach (Cart::get() as $item) {
                $order->orderDetails()->create([
                    'product_id' => $item->id,
                    'od_qty' => $item->qty,
                    'od_item_price' => $item->options->original_price,
                    'od_total' => $item->total('original_price'),
                    'od_vat_price' => $item->vat_price,
                    'od_vat_total' => $item->total('vat_price'),
                ]);
            }

            // foreach ($orderdetail as $value)
            // {
            //     $od = new OrderDetail();
            //     $od->od_orderid = $order->order_id;
            //     $od->od_productid = $value['cart_itemid'];
            //     $od->od_qty = $value['cart_qty'];
            //     $od->od_itemprice = $value['cart_itemprice'];
            //     $od->od_total = $value['cart_total'];
            //     $od->od_vatprice = $value['cart_vatprice'];
            //     $od->od_vattotal = $value['cart_vattotal'];
            //     $od->save();
            // }

            ## Generate Receipt ID ##
            if ($order) {
                $token = mt_rand(1000, 9999);
                $order->update([
                    'order_receipt_id' => $order->id + $token
                ]);

                $builder = new Builder(
                    writer: new PngWriter(),
                    data: $order->order_id,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::Low,
                    size: 150,
                    margin: 10,
                );
                $result = $builder->build();
                $result->saveToFile(public_path('uploads/qrcode/qrcode' . $order->id . '.png'));
            }

            // ## Generate Qr Code ##
            // if ($order)
            // {
            //     $builder = new Builder(
            //         writer: new PngWriter(),
            //         data: $order->order_id,
            //         encoding: new Encoding('UTF-8'),
            //         errorCorrectionLevel: ErrorCorrectionLevel::Low,
            //         size: 150,
            //         margin: 10,
            //     );
            //     $result = $builder->build();
            //     $result->saveToFile(public_path('uploads/qrcode/qrcode' . $order->order_id . '.png'));
            // }

            ## Payment ##
            $bluem = new Bluem(bluemConfig('identity'));
            $request = $bluem->CreateIdentityRequest(
                [
                    'CustomerIDRequest',
                    'NameRequest',
                    'AddressRequest',
                    'BirthDateRequest',
                    'GenderRequest',
                    'TelephoneRequest',
                    'EmailRequest'
                ],
                'Identity',
                $order->id,
                route('payment', $order->uuid)
            );

            $response = $bluem->PerformRequest($request);
            $bluemIdentity = $response->IdentityTransactionResponse;
            $status_request = $bluem->IdentityStatus($bluemIdentity->TransactionID, $response->IdentityTransactionResponse[0]['entranceCode']);

            if ($status_request->IdentityStatusUpdate) {
                $orderPayment = $order->orderPayments()->create([
                    'identity_entrance_code' => $bluemIdentity[0]['entranceCode'],
                    'identity_transaction_id' => $bluemIdentity->TransactionID,
                    'identity_transaction_url' => $bluemIdentity->TransactionURL,
                    'identity_transaction_short_url' => $bluemIdentity->ShortTransactionURL,
                ]);
                $order->update([
                    'order_payment' => $orderPayment->identity_entrance_code
                ]);
                // $orderPayment = new OrderPayment();
                // $orderPayment->order_id = $order->order_id;
                // $orderPayment->identity_entrance_code = $identity_response[0]['entranceCode'];
                // $orderPayment->identity_transaction_id = $identity_response->TransactionID;
                // $orderPayment->identity_transaction_url = $identity_response->TransactionURL;
                // $orderPayment->identity_transaction_short_url = $identity_response->ShortTransactionURL;
                // $orderPayment->save();

                // Order::where('order_id', $order->order_id)->update(['order_payment' => $orderPayment->identity_entrance_code]);
            }
            // ## checked identity verified or not

            // $cus_detail =  Customer::where('customer_id', $customer_id)->first();

            if ($customer->is_verified == 'TRUE') {
                if (!empty($orderPayment)) {
                    $bluem = new Bluem(bluemConfig('payment'));
                    $response = $bluem->Payment(
                        $order->order_note ?: 'Order',
                        (int) $order->id,
                        $order->order_final_with_discount,
                        null, // set it automatically to two weeks in advance. Or, to create and perform a request together in shorthand:
                        'EUR' // if set to null, will default to EUR
                    );

                    $payment = $response->PaymentTransactionResponse;
                    $orderPayment->update([
                        'iban_entrance_code' => $payment[0]['entranceCode'],
                        'iban_transaction_id' => $payment->TransactionID,
                        'iban_transaction_url' => $payment->TransactionURL,
                        'iban_transaction_short_url' => $payment->ShortTransactionURL,
                        'iban_refrence_id' => $payment->PaymentReference,
                        'iban_debtorrefrence_id' => $payment->DebtorReference,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'identity_response' => $payment->TransactionURL ?? null
                    // 'identity_response' => $this->makeOrderPayment($order->uuid, true)
                ]);
            }

            return response()->json([
                'status' => true,
                'identity_response' => $bluemIdentity->TransactionURL,
                'message' => 'Your Order Placed Successfully!'
            ]);
        }

        return response()->json([
            'status' => false,
            'type' => 'InvalidAmount',
            'message' => 'Minimum order amount is € ' . $pool->delivery_start_from
        ]);
    }

    public function makeOrderPayment($order_id, $manualCall = false)
    {

        // $bluem_config = new stdClass();
        // // Fill in prod, test or acc for production, test or acceptance environment.
        // $bluem_config->environment = env('BLUEM_ENVIRONMENT');

        // // The sender ID, issued by BlueM. Starts with an S, followed by a number.
        // $bluem_config->senderID = env('BLUEM_SENDERID');

        // // The access token to communicate with BlueM, for the test environment.
        // $bluem_config->test_accessToken = env('BLUEM_TEST_ACCESSTOKEN');

        // // The access token to communicate with BlueM, for the production environment.
        // $bluem_config->production_accessToken = env('BLUEM_PRODUCTION_ACCESSTOKEN');

        // // the merchant ID, to be found on the contract you have with the bank for receiving direct debit mandates.
        // $bluem_config->merchantID = env('BLUEM_MERCHANTID');

        // // What's your BrandID? Set at BlueM
        // $bluem_config->brandID = '247DrankPayment';

        // $bluem_config->merchantReturnURLBase = env('MERCHANT_RETURN_URL_BASE');

        // $bluem_object = new Bluem($bluem_config);

        $bluem = new Bluem(bluemConfig('payment'));
        $order = Order::where('uuid', $order_id)->first();


        /** Payment */
        // $description =  $order->order_note ?: 'Order';
        // $amount = $order->order_final_with_discount;
        // $currency = 'EUR'; // if set to null, will default to EUR
        // $debtorReference = (int)$order->order_id;

        // $dueDateTime = null; // set it automatically to two weeks in advance.
        // Or, to create and perform a request together in shorthand:


        $response = $bluem->Payment(
            $order->order_note ?: 'Order',
            (int) $order->id,
            $order->order_final_with_discount,
            null, // set it automatically to two weeks in advance. Or, to create and perform a request together in shorthand:
            'EUR' // if set to null, will default to EUR
        );

        $payment = $response->PaymentTransactionResponse;
        // dd($payment_response);
        // $orderPayment = OrderPayment::where('identity_entrance_code', $order->order_payment)->first();
        // $orderPayment->iban_entrance_code = $payment[0]['entranceCode'];
        // $orderPayment->iban_transaction_id = $payment->TransactionID;
        // $orderPayment->iban_transaction_url = $payment->TransactionURL;
        // $orderPayment->iban_transaction_short_url = $payment->ShortTransactionURL;
        // $orderPayment->iban_refrence_id = $payment->PaymentReference;
        // $orderPayment->iban_debtorrefrence_id = $payment->DebtorReference;
        // $orderPayment->save();

        $orderPayment = $order->orderPayments()
            ->where('identity_entrance_code', $order->order_payment)
            ->first();

        if ($orderPayment) {
            $orderPayment->update([
                'iban_entrance_code' => $payment[0]['entranceCode'],
                'iban_transaction_id' => $payment->TransactionID,
                'iban_transaction_url' => $payment->TransactionURL,
                'iban_transaction_short_url' => $payment->ShortTransactionURL,
                'iban_refrence_id' => $payment->PaymentReference,
                'iban_debtorrefrence_id' => $payment->DebtorReference,
            ]);
        }
        // echo $payment_response->TransactionURL;

        if ($manualCall) {
            return $payment->TransactionURL;
        }

        return redirect($payment->TransactionURL);
    }

    public function placeorderCM(Request $request): JsonResponse
    {
        $customer = auth('customer')->user();

        if ($request->contact_no)
        {
            $customer->update([
                'customer_contact_no' => $request->country_code . '-' . $request->contact_no
            ]);
        }

        $customerAddress = $customer->address()
            ->whereDefault()
            ->first();

        if (!$customerAddress)
        {
            return response()->json([
                'status' => false,
                'type' => 'NoAddress',
                'message' => 'Please add your address'
            ]);
        }

        $postcode = $customerAddress->post_code;
        $poolsArr = Franchise::poolsArray();
        $pool = Pool::whereIn('id', $poolsArr)
            ->whereAttr($postcode)
            ->first();

        if (!$pool)
        {
            return response()->json([
                'status' => false,
                'type' => 'invalidPostcode',
                'message' => 'We do not provide our service on your postcode area. Please call customer service for more details.',
            ]);
        }

        $paymentAmount = $request->promo_code
            ? $request->withDiscount_FinalAmount
            : $request->final_amount;

        if ($paymentAmount <= $pool->delivery_start_from)
        {
            return response()->json([
                'status' => false,
                'type' => 'InvalidAmount',
                'message' => 'Minimum order amount is € ' . $pool->delivery_start_from
            ]);
        }

        $franchise = $pool->franchises()->first();
        $data = [
            'uuid' => Str::uuid(),
            'order_address_id' => $customerAddress->id,
            'order_price' => Cart::subtotal(),
            'order_delivery_charge' => Str::replace(',', '', $request->delivery_charge),
            'order_final_amount' => Str::replace(',', '', $request->final_amount),
            'order_note' => $request->note,
            'order_status' => 0,
            'order_payment' => '',
            'order_channel_order_id' => strtoupper(Str::random(6)),

            'franchise_id' => $franchise->id,
            'order_uber_id' => '',
            'uber_order_delivery_type' => '',
            'uber_order_delivery_status' => '',
            'order_deliverect_id' => '',
            'channel_link' => '',
            'order_receipt_id' => '',
            'order_approve' => 0,
            'order_service_charge' => '',
            'order_cancelled_reason' => '',
            'identity_entrance_code' => '',
            'identity_transaction_id' => '',
            'iban_entrance_code' => 0,
            'iban_transaction_id' => 0,
            'failed_reason' => '',
            'rejected_reason' => '',
            'od_rejected_id' => '',

            'order_discount' => 0.00,
            'order_final_with_discount' => Str::replace(',', '', $request->final_amount),
        ];

        if ($request->promo_code)
        {
            $data['order_discount'] = Str::replace(',', '', $request->Discount);
            $data['order_final_with_discount'] = Str::replace(',', '', $request->withDiscount_FinalAmount);
            $data['promo_code_id'] = $request->promo_code;
        }

        $order = $customer->orders()->create($data);

        foreach (Cart::get() as $item)
        {
            $order->orderDetails()->create([
                'product_id' => $item->id,
                'od_qty' => $item->qty,
                'od_item_price' => $item->options->original_price,
                'od_total' => $item->total('original_price'),
                'od_vat_price' => $item->vat_price,
                'od_vat_total' => $item->total('vat_price'),
            ]);
        }

        if ($order)
        {
            $token = mt_rand(1000, 9999);
            $order->update([
                'order_receipt_id' => $order->id + $token
            ]);
        }

        session()->put('type', 'web');
        Cart::destroy();

        return response()->json([
            'status' => true,
            'orderId' => $order->id,
            'message' => 'success'
        ]);
    }

    public function paymentmethod(Request $request): RedirectResponse|View
    {
        $order = Order::find($request->id);

        if (!$order) return redirect('/cart');

        $data = [
            'orderId' => $request->id,
            'paymentmethods' => PaymentMethod::where('status', 1)->get()
        ];

        return view('customer.payment-method', $data);
    }

    public function cardDetails(Request $request): View
    {
        if (!$request->orderId)
        {
            abort(404);
        }

        return view('customer.credit-card', [
            'orderId' => $request->orderId
        ]);
    }

    public function idinverification(Request $request)
    {
        $data = [];
        $orderId = $request->orderId;
        $order = Order::find($orderId);
        $cus_detail =  Customer::where('customer_id', $order->order_customerid)->first();
        // if ($cus_detail->is_verified == 'TRUE') {

        $api_url = env('CMTEST_API_URL');
        $merchant_key = env('MERCHANT_KEY');
        $orderCreateData = [
            'order_reference' => time(),
            'description' => $order->order_note ? $order->order_note : 'Order',
            'amount' => (float)$order->order_final_with_discount  * 100,
            // 'amount' => 100,
            'currency' => 'EUR',
            'email' => $cus_detail->customer_email,
            'language' => 'nl',
            'country' => 'NL',
            'return_urls' => [
                'success' => url('orderStatus/' . $orderId),
                'canceled' => url('orderStatus/' . $orderId),
                'pending' => url('orderStatus/' . $orderId),
                'error' => url('orderStatus/' . $orderId)
            ]
        ];

        $response = $this->callCurlApi($api_url . $merchant_key . '/orders', $orderCreateData, 'POST');
        $resultdata = json_decode($response, true);
        if (!isset($resultdata['messages'])) {
            $order_key = $resultdata["order_key"];
            $response1 = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments/methods', '', 'GET');
            $banklist = json_decode($response1, true);
            $data['orderId'] = $orderId;
            $data['banklist'] = $banklist;
            $data['order_key'] = $order_key;
            // echo '<pre>';print_R($banklist);die;
            return view('frontend.ideal-bank-select', $data);
        } else {
            $data_new['status'] = 'Error';
            $data_new['message'] = $resultdata['messages'][0];
            return view('frontend.order_status', $data_new);
        }
        //
        // }
        $data['orderId'] = $orderId;
        $postdata = "merchant_token=" . env('IDIN_MERCHANT_KEY');
        $headerData = array(
            'Content-Type: application/x-www-form-urlencoded'
        );

        $api_url = env('IDIN_TEST_URL');

        $idin_response = $this->callCurlApi($api_url . 'directory', $postdata, 'POST', $headerData);
        $idin_responseResult = json_decode($idin_response, true);
        $data['bankdetails'] = $idin_responseResult[0]['issuers'];

        $ec = sha1(rand(0, 5));


        $qrpostdata = "merchant_token=" . env('IDIN_MERCHANT_KEY') . "&entrance_code=" . $ec . "&merchant_return_url=" . url('idin-thankyou') . "&use_case=00&expiration=" . date('Y-m-d H:i:s', strtotime("+30 minutes")) . "&size=200&format=png&language=nl";
        $qr_idin_response = $this->callCurlApi($api_url . 'qr/create', $qrpostdata, 'POST', $headerData);
        $qr_idin_responseResult = json_decode($qr_idin_response, true);

        // $order = Order::find($request->orderId);
        $order->identity_entrance_code = $ec;
        $order->qr_id = $qr_idin_responseResult['qr_id'];
        $order->save();
        //Session::put('qr_id', $qr_idin_responseResult['qr_id']);
        $data['qr_data'] = $qr_idin_responseResult;

        return view('frontend.idin-selectbank', $data);
    }

    public function idinbanktransaction(Request $request)
    {

        $bank_issuer = $request->bank_issuer;

        $orderId = $request->orderId;

        $api_url = env('IDIN_TEST_URL');
        $ec = sha1(rand(0, 5));
        $postdata = "merchant_token=" . env('IDIN_MERCHANT_KEY') . "&identity=true&name=true&gender=true&address=true&date_of_birth=true&18y_or_older=true&email_address=true&telephone_number=true&issuer_id=" . $bank_issuer . "&entrance_code=" . $ec . "&merchant_return_url=" . url('checkidinstatus') . "&language=nl";
        $headerData = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        $idin_response = $this->callCurlApi($api_url . 'transaction', $postdata, 'POST', $headerData);
        $idin_responseResult = json_decode($idin_response, true);

        $order = Order::find($orderId);
        $order->identity_entrance_code = $ec;
        $order->identity_transaction_id = $idin_responseResult['transaction_id'];
        $order->merchant_reference = $idin_responseResult['merchant_reference'];
        $order->save();

        // Session::put('merchant_reference', $idin_responseResult['merchant_reference']);
        return response()->json(['status' => true, 'url' => $idin_responseResult['issuer_authentication_url'], 'message' => '']);
    }

    public function checkidinstatus(Request $request)
    {
        $data = [];
        $trxid = $request->trxid;
        $ec = $request->ec;

        $order = Order::where(['identity_entrance_code' => $ec, 'identity_transaction_id' => $trxid])->first();
        $orderId = $order['order_id'];
        $merchant_reference =  $order['merchant_reference'];

        $api_url = env('IDIN_TEST_URL');
        $postdata = "merchant_token=" . env('IDIN_MERCHANT_KEY') . "&transaction_id=" . $trxid . "&merchant_reference=" . $merchant_reference;
        $headerData = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        $idin_response = $this->callCurlApi($api_url . 'status', $postdata, 'POST', $headerData);
        $idin_responseResult = json_decode($idin_response, true);
        // echo '<pre>';print_r($idin_responseResult);die;

        if ($idin_responseResult['status'] == 'success') {
            $customer = Customer::where('customer_id', $order['order_customerid'])->first();
            $customer->is_verified = 'TRUE';
            $customer->save();
            return redirect(url('makePayment?paymentmethod=IDEAL&bank_issuer=' . $idin_responseResult['issuer_id'] . '&trxid=' . $trxid . '&ec=' . $ec . '&OrderId=' . $orderId));
        } else {

            return redirect(url('check-idin/' . $orderId));
        }
    }

    public function inserttrxid(Request $request)
    {
        $data = [];
        $trxid = $request->trxid;
        $ec = $request->ec;
        if ($ec != '') {
            $order = Order::where('identity_entrance_code', $ec)->first();
            $order->identity_transaction_id = $trxid;
            $order->save();

            $customer = Customer::where('id', $order->customer_id)->first();
            $customer->is_verified = 'TRUE';
            $customer->save();
            return response()->json(['status' => 'success', 'order_id' => $order->id, 'customer_id' => $customer->id]);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Invalid entrance code']);
        }

        return view('frontend.idin-success', $data);
    }


    public function checkqridinstatus(Request $request)
    {
        $data = [];

        $orderId = $request->orderId;
        $order = Order::find($orderId);
        $trxid = $order->identity_transaction_id;
        $ec = $order->identity_entrance_code;
        $qr_id = $order->qr_id;
        $api_url = env('IDIN_TEST_URL');
        $postdata = "merchant_token=" . env('IDIN_MERCHANT_KEY') . "&transaction_id=" . $trxid . "&qr_id=" . $qr_id;
        $headerData = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        $idin_response = $this->callCurlApi($api_url . 'qr/status', $postdata, 'POST', $headerData);
        $idin_responseResult = json_decode($idin_response, true);

        if ($idin_responseResult['status'] == 'success') {
            $redirect_url = url('makePayment?paymentmethod=IDEAL&bank_issuer=' . $idin_responseResult['issuer_id'] . '&trxid=' . $trxid . '&ec=' . $ec . '&OrderId=' . $orderId);
            return response()->json(['status' => true, 'url' => $redirect_url, 'message' => '']);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function makePayment(Request $request)
    {
        if ($request->OrderId) {
            $orderId = $request->OrderId;
        }
        // else {
        //   $orderId = Session::get('OrderId');
        // }
        $order = Order::find($orderId);

        $orderRequestData = [
            'delivery_charge' => $order->order_delivery_charge,
            'final_amount' => $order->order_finalamount,
            'Discount' =>  $order->order_discount,
            'withDiscount_FinalAmount' =>  $order->order_final_with_discount,
            'promo_code' =>  $order->order_promocode,
            'orderId' => $orderId,
            'type' => Session::get('type'),
            'cust_id' => $order->order_customerid
        ];

        $customer = Customer::find($orderRequestData['cust_id']);
        $customer_id = $orderRequestData['cust_id'];
        $customer_type = $customer->customer_type;
        $customer_email = $customer->customer_email;
        $customer_name = $customer->customer_name;

        $getaddress =  CustomerAddress::where('customer_id', $customer_id)->where('default', '1')->first();

        $orderCreateData = [
            'order_reference' => time(),
            'description' => $order->order_note ? $order->order_note : 'Order',
            'amount' => (float)$orderRequestData['withDiscount_FinalAmount'] * 100,
            // 'amount' => 100,
            'currency' => 'EUR',
            'email' => $customer_email,
            'language' => 'nl',
            'country' => 'NL',
            'return_urls' => [
                'success' => url('orderStatus/' . $orderRequestData['orderId']),
                'canceled' => url('orderStatus/' . $orderRequestData['orderId']),
                'pending' => url('orderStatus/' . $orderRequestData['orderId']),
                'error' => url('orderStatus/' . $orderRequestData['orderId'])
            ]
        ];
        $api_url = env('CMTEST_API_URL');
        $merchant_key = env('MERCHANT_KEY');

        $bitpayUrl = env('BITPAY_TESTAPI_URL');
        $bitpay_token = env('BITPAY_TOKRN');

        $payment_method = $request->paymentmethod;
        $order_key = '';
        if ($payment_method != 'bitpay' && $payment_method != 'cash' && $payment_method != 'pin' && !$request->order_key) {
            $response = $this->callCurlApi($api_url . $merchant_key . '/orders', $orderCreateData, 'POST');

            $resultdata = json_decode($response, true);
            if (isset($resultdata['messages'])) {
                return response()->json(['status' => false, 'message' => $resultdata['messages'][0]]);
            }

            $order_key = $resultdata["order_key"];
            $encrypt_key = $request->key;
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $order_key = $request->order_key;
        }

        // if(!$request->bank_issuer && $payment_method == 'IDEAL')
        // {
        //     $response1 = $this->callCurlApi($api_url . $merchant_key . '/orders/'.$order_key.'/payments/methods','', 'GET');

        //     $banklist = json_decode($response1, true);
        // }
        $is_exist = OrderPayment::where('order_id', $orderRequestData['orderId'])->first();
        if ($is_exist) {
            $orderPayment = OrderPayment::where('order_id', $orderRequestData['orderId'])->first();
        } else {
            $orderPayment = new OrderPayment();
        }
        // $orderPayment = new OrderPayment();
        $orderPayment->order_id = $orderRequestData['orderId'];
        $orderPayment->order_key = $order_key;

        switch ($payment_method) {

            case 'visa':

                $paymentData = [
                    'method' => 'VISA',
                    'card_details' => ['browser_information' => ['shopper_ip' => $ip, 'accept' => 'text/html,application/xhtml+xml,application/xml', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18363'], 'encrypted_card_details' => ['data' => $encrypt_key]]
                ];

                $payment_response = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments', $paymentData, 'POST');
                $paymentresultData = json_decode($payment_response, true);
                if (isset($paymentresultData['messages'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['messages'][0]]);
                } else {
                    $redirect_url = $paymentresultData['urls'][0]['parameters']['TermUrl'];
                    $orderPayment->paymentid = $paymentresultData['urls'][0]['parameters']['MD'];
                    $orderPayment->status_code = $paymentresultData['status'];
                    $orderPayment->save();
                    return response()->json(['status' => true, 'data' => $paymentresultData, 'message' => '']);
                }
                break;

            case 'mastercard':
                $paymentData = [
                    'method' => 'MASTERCARD',
                    'card_details' => ['browser_information' => ['shopper_ip' => $ip, 'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18363'], 'encrypted_card_details' => ['data' => $encrypt_key]]
                ];

                $payment_response = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments', $paymentData, 'POST');
                $paymentresultData = json_decode($payment_response, true);
                if (isset($paymentresultData['messages'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['messages'][0]]);
                } else {
                    $redirect_url = $paymentresultData['urls'][0]['parameters']['TermUrl'];
                    $orderPayment->paymentid = $paymentresultData['urls'][0]['parameters']['MD'];
                    $orderPayment->status_code = $paymentresultData['status'];
                    $orderPayment->save();
                    return response()->json(['status' => true, 'data' => $paymentresultData, 'message' => '']);
                }

                break;

            case 'maestro':

                $paymentData = [
                    'method' => 'MAESTRO',
                    'card_details' => ['browser_information' => ['shopper_ip' => $ip, 'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18363'], 'encrypted_card_details' => ['data' => $encrypt_key]]
                ];
                $payment_response = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments', $paymentData, 'POST');
                $paymentresultData = json_decode($payment_response, true);
                if (isset($paymentresultData['messages'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['messages'][0]]);
                } else {
                    $redirect_url = $paymentresultData['urls'][0]['parameters']['TermUrl'];
                    $orderPayment->paymentid = $paymentresultData['urls'][0]['parameters']['MD'];
                    $orderPayment->status_code = $paymentresultData['status'];
                    $orderPayment->save();
                    // die;
                    return response()->json(['status' => true, 'data' => $paymentresultData, 'message' => '']);
                }

                break;

            case 'amex':

                $paymentData = [
                    'method' => 'AMEX',
                    'card_details' => ['browser_information' => ['shopper_ip' => $ip, 'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18363'], 'encrypted_card_details' => ['data' => $encrypt_key]]
                ];
                $payment_response = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments', $paymentData, 'POST');
                $paymentresultData = json_decode($payment_response, true);
                if (isset($paymentresultData['messages'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['messages'][0]]);
                } else {
                    $redirect_url = $paymentresultData['urls'][0]['parameters']['TermUrl'];
                    $orderPayment->paymentid = $paymentresultData['urls'][0]['parameters']['MD'];
                    $orderPayment->status_code = $paymentresultData['status'];
                    $orderPayment->save();
                    // die;
                    return response()->json(['status' => true, 'data' => $paymentresultData, 'message' => '']);
                }

                break;

            case 'bancontact':

                $paymentData = [
                    'method' => 'MISTERCASH',
                    'card_details' => ['browser_information' => ['shopper_ip' => $ip, 'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.18363'], 'encrypted_card_details' => ['data' => $encrypt_key]]
                ];
                $payment_response = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments', $paymentData, 'POST');
                $paymentresultData = json_decode($payment_response, true);
                if (isset($paymentresultData['messages'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['messages'][0]]);
                } else {
                    $redirect_url = $paymentresultData['urls'][0]['parameters']['TermUrl'];
                    $orderPayment->paymentid = $paymentresultData['urls'][0]['parameters']['MD'];
                    $orderPayment->status_code = $paymentresultData['status'];
                    $orderPayment->save();
                    // die;
                    return response()->json(['status' => true, 'data' => $paymentresultData, 'message' => '']);
                }

                break;

            case 'PAYPAL_EXPRESS_CHECKOUT':

                $paymentData = ['method' => 'PAYPAL'];

                $payment_response = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments', $paymentData, 'POST');
                $paymentresultData = json_decode($payment_response, true);
                // print_r($paymentresultData);die;
                if (isset($paymentresultData['messages'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['messages'][0]]);
                } else {
                    $redirect_url = $paymentresultData['urls'][0]['url'];
                    $orderPayment->status_code = $paymentresultData['status'];
                    $orderPayment->save();
                    return response()->json(['status' => true, 'redirectUrl' => $redirect_url, 'message' => '']);
                }
                break;

            case 'IDEAL':

                $paymentData = [
                    'method' => 'IDEAL',
                    'ideal_details' => ['issuer_id' => $request->bank_issuer]
                ];

                $payment_response = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key . '/payments', $paymentData, 'POST');
                $paymentresultData = json_decode($payment_response, true);
                if (isset($paymentresultData['messages'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['messages'][0]]);
                } else {
                    // echo '<pre>'; print_r($paymentresultData);die;
                    $redirect_url = $paymentresultData['urls'][0]['url'];
                    $url_components = parse_url($redirect_url);
                    parse_str($url_components['query'], $identity_response);
                    $orderPayment->status_code = $paymentresultData['status'];
                    $orderPayment->identity_entrance_code = $request->ec;
                    $orderPayment->identity_transaction_id = $request->trxid;
                    $orderPayment->save();

                    if ($request->redirecturl == true) {
                        return response()->json(['status' => true, 'redirectUrl' => $redirect_url, 'message' => '']);
                    }

                    return redirect($redirect_url);
                }

                break;

            case 'bitpay':


                $data = "token=" . env('BITPAY_TOKRN') . "&price=" . $orderRequestData['final_amount'] . "&currency=EUR&redirectURL=" . url('orderStatus') . "/" . $orderRequestData['orderId'] . "&closeURL" . url('paymentmethod');
                $headerData =  array(
                    'x-accept-version: 2.0.0',
                    'Content-Type: application/x-www-form-urlencoded'
                );

                $order = Order::find($orderRequestData['orderId']);
                $order->payment_method = 'BitPay';
                $order->save();

                $payment_response = $this->callCurlApi($bitpayUrl . '/invoices', $data, 'POST', $headerData);
                $paymentresultData = json_decode($payment_response, true);
                if (isset($paymentresultData['error'])) {
                    return response()->json(['status' => false, 'message' => $paymentresultData['error']]);
                } else {
                    $redirect_url = $paymentresultData['data']['url'];
                    $orderPayment->paymentid = $paymentresultData['data']['id'];
                    $orderPayment->status_code = $paymentresultData['data']['status'];
                    $orderPayment->save();
                    return response()->json(['status' => true, 'redirectUrl' => $redirect_url, 'message' => '']);
                }
                break;

            case 'gpay':

                $gpay_api_url = env('CMTEST_GPAY_API_URL');

                $paymentData = [
                    'method' => 'GOOGLE_PAY',
                    "google_pay_details" => [
                        "server_url" => "http://testsecure.docdatapayments.com:8484",
                        "merchant_country" => "NL",
                        "gateway_id" => "example",
                        "environment" => "TEST",
                        "merchant_id" => "default",
                        "allowed_card_networks" => [
                            "MASTERCARD",
                            "VISA"
                        ]
                    ],
                ];

                $payment_response = $this->callCurlApi($gpay_api_url . $merchant_key . '/payments/' . $order_key . '/authorize', $paymentData, 'POST');
                //print_r($payment_response);
                // $paymentresultData = json_decode($payment_response, true);
                // echo 'prachi';

                break;

            case 'cash':
            case 'pin':
                $orderPayment->payment_status = 0;
                $orderPayment->save();
                $detail = Order::leftJoin('customers', function ($join) {
                    $join->on('customers.customer_id', '=', 'orders.order_customerid');
                })->where('order_id', $orderPayment->order_id)->first();

                // $customer_id = $detail['order_customerid'];
                // $customer_email = $detail['customer_email'];
                // $customer_name = $detail['customer_name'];
                if ($payment_method == 'cash') {
                    $insert_payment_method = 'Cash';
                } else {
                    $insert_payment_method = 'Pin at Door';
                }

                Order::where('order_id', $orderPayment->order_id)->update(['order_status' => '9', 'order_payment_status' => 'NO', 'payment_method' => $insert_payment_method]);
                CartModel::where('cart_custid', $detail['order_customerid'])->whereNull('deleted_at')->delete();
                ## promo code count
                if ($detail['order_promocode'] != "0" && $detail['order_discount'] != 0.00) {
                    $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $detail['order_promocode'])->get()->count();
                    if ($custom_used) {
                        $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->first();
                        UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->update(['used_count' => $custuser['used_count'] + 1]);
                    } else {
                        $newusedcode = new UsedPromoCode();
                        $newusedcode->pcode_id =  $detail['order_promocode'];
                        $newusedcode->c_id = auth('customer')->user()->customer_id;
                        $newusedcode->used_count = 1;
                        $newusedcode->save();
                    }
                }


                ## Send Email##

                $maildata = [];
                $maildata['order_id'] = $orderPayment->order_id;
                $maildata['name'] = $customer_name;
                $maildata['scan'] = 'qrcode' . $orderPayment->order_id . '.png';
                $maildata['order'] = Order::leftJoin('customers', function ($join) {
                    $join->on('customers.customer_id', '=', 'orders.order_customerid');
                })->leftJoin('address', function ($join) {
                    $join->on('address.address_id', '=', 'orders.order_address_id');
                })->find($orderPayment->order_id);

                $maildata['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $orderPayment->order_id)
                    ->get(['od_qty', 'od_vattotal', 'product_name', 'image']);
                foreach ($maildata['orderdetail'] as $key => $value) {
                    if ($value['image'] != "") {
                        $maildata['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
                    } else {
                        $maildata['orderdetail'][$key]['image'] = asset('img/logo.png');
                    }
                }
                Mail::to($customer_email)
                    ->send(new PlaceOrder($maildata));


                $this->OrderAssignment($orderPayment->order_id);

                $redirect_url = url('orderStatus/' . $orderPayment->order_id);
                return response()->json(['status' => true, 'redirectUrl' => $redirect_url, 'message' => '']);

                break;

            default:
                $data['status'] = 'Error';
                $data['message'] = 'Something went wrong !!';
                // unexpected status returned, show an error
                return response()->json(['status' => true, 'message' => $data['message']]);
                break;
        }
    }

    public function checkorderStatus(Request $request)
    {
        $api_url = env('CMTEST_API_URL');
        $merchant_key = env('MERCHANT_KEY');
        $orderPayment = OrderPayment::where('order_id', $request->orderId)->first();
        $order = Order::find($request->orderId);
        if ($order->payment_method == 'Cash' || $order->payment_method == 'Pin at Door') {
            $order = Order::find($request->orderId);
            // $order->order_status = 9;
            // $order->save();
            $orderPayment->status_code = 'Pending';
            $orderPayment->save();
            if ($request->session()->has('type')) {
                $data['status'] = 'Success';
                $data['message'] = 'Your Order is placed !!';
                return view('customer.order_status', $data);
            }
        } else if ($order->payment_method == 'BitPay') {

            $bitpayUrl = env('BITPAY_TESTAPI_URL');
            $bitpay_token = env('BITPAY_TOKRN');
            $data = "token=" . env('BITPAY_TOKRN');
            // $orderPayment = OrderPayment::where('order_id', $request->orderId)->first();
            $id = $orderPayment->paymentid;

            $headerData =  array(
                'x-accept-version: 2.0.0',
                'Content-Type: application/x-www-form-urlencoded'
            );

            $payment_response = $this->callCurlApi($bitpayUrl . '/invoices/' . $id, $data, 'GET', $headerData);
            $result = json_decode($payment_response, true);
            // echo '<pre>';print_r($result);die;
            $paymentStatus = $result['data']['status'];

            switch ($paymentStatus) {
                case 'paid':
                case 'confirmed':
                    $orderPayment->payment_status = 1;
                    $orderPayment->status_code = $paymentStatus;
                    // $orderPayment->payment_method = 'BitPay';
                    $orderPayment->save();

                    $order = Order::find($request->orderId);
                    $order->order_status = 9;
                    $order->order_payment_status = 'YES';
                    $order->save();


                    $detail = Order::leftJoin('customers', function ($join) {
                        $join->on('customers.customer_id', '=', 'orders.order_customerid');
                    })->where('order_id', $request->orderId)->first();

                    $customer_id = $detail['order_customerid'];
                    $customer_email = $detail['customer_email'];
                    $customer_name = $detail['customer_name'];

                    CartModel::where('cart_custid', $detail['order_customerid'])->whereNull('deleted_at')->delete();
                    ## promo code count
                    if ($detail['order_promocode'] != "0" && $detail['order_discount'] != 0.00) {
                        $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $detail['order_promocode'])->get()->count();
                        if ($custom_used) {
                            $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->first();
                            UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->update(['used_count' => $custuser['used_count'] + 1]);
                        } else {
                            $newusedcode = new UsedPromoCode();
                            $newusedcode->pcode_id =  $detail['order_promocode'];
                            $newusedcode->c_id = auth('customer')->user()->customer_id;
                            $newusedcode->used_count = 1;
                            $newusedcode->save();
                        }
                    }

                    ## Send Email##

                    $maildata = [];
                    $maildata['order_id'] = $orderPayment->order_id;
                    $maildata['name'] = $customer_name;
                    $maildata['scan'] = 'qrcode' . $orderPayment->order_id . '.png';
                    $maildata['order'] = Order::leftJoin('customers', function ($join) {
                        $join->on('customers.customer_id', '=', 'orders.order_customerid');
                    })->leftJoin('address', function ($join) {
                        $join->on('address.address_id', '=', 'orders.order_address_id');
                    })->find($orderPayment->order_id);

                    $maildata['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $orderPayment->order_id)
                        ->get(['od_qty', 'od_vattotal', 'product_name', 'image']);
                    foreach ($maildata['orderdetail'] as $key => $value) {
                        if ($value['image'] != "") {
                            $maildata['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
                        } else {
                            $maildata['orderdetail'][$key]['image'] = asset('img/logo.png');
                        }
                    }
                    Mail::to($customer_email)
                        ->send(new PlaceOrder($maildata));

                    $this->OrderAssignment($request->orderId);

                    if ($request->session()->has('type')) {
                        $data_new['status'] = 'Success';
                        $data_new['message'] = 'Your amount has been successfully paid !!';
                        return view('customer.order_status', $data_new);
                    }
                    // return redirect('/');

                    break;

                default:
                    $data['status'] = 'Error';
                    $data['message'] = 'Something went wrong !!';
                    // unexpected status returned, show an error
                    return view('customer.order_status', $data);
                    break;
            }
        } else {
            $order_key = $orderPayment->order_key;
            // echo '<pre>'; print_r($orderPayment->order_key);die;
            $reponse = $this->callCurlApi($api_url . $merchant_key . '/orders/' . $order_key, '', 'GET');
            $reponse = json_decode($reponse, true);
            if (isset($reponse['payments'])) {
                $paymentData = $reponse['payments'][0];
                // print_r($paymentData);die;
                $orderPayment->paymentid = $paymentData['id'];
                // $orderPayment->payment_method = $paymentData['method'];
                $orderPayment->save();

                $order->payment_method = $paymentData['method'];
                $order->save();

                $paymentStatus = $paymentData['authorization']['state'];
                switch ($paymentStatus) {
                    case 'AUTHORIZED':
                    case 'CAPTURED':
                    case 'NEW':

                        if ($paymentStatus == 'AUTHORIZED' && $paymentData['authorization']['confidence'] == 'ACQUIRER_APPROVED') {
                            $orderPayment->payment_status = 1;
                            $orderPayment->status_code = $paymentStatus;
                            $orderPayment->save();

                            $order = Order::find($request->orderId);
                            $order->order_status = 9;
                            $order->order_payment_status = 'YES';
                            $order->save();

                            $detail = Order::leftJoin('customers', function ($join) {
                                $join->on('customers.customer_id', '=', 'orders.order_customerid');
                            })->where('order_id', $request->orderId)->first();

                            $customer_id = $detail['order_customerid'];
                            $customer_email = $detail['customer_email'];
                            $customer_name = $detail['customer_name'];

                            CartModel::where('cart_custid', $detail['order_customerid'])->whereNull('deleted_at')->delete();
                            ## promo code count
                            if ($detail['order_promocode'] != "0" && $detail['order_discount'] != 0.00) {
                                $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $detail['order_promocode'])->get()->count();
                                if ($custom_used) {
                                    $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->first();
                                    UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->update(['used_count' => $custuser['used_count'] + 1]);
                                } else {
                                    $newusedcode = new UsedPromoCode();
                                    $newusedcode->pcode_id =  $detail['order_promocode'];
                                    $newusedcode->c_id = auth('customer')->user()->customer_id;
                                    $newusedcode->used_count = 1;
                                    $newusedcode->save();
                                }
                            }

                            ## Send Email##

                            $maildata = [];
                            $maildata['order_id'] = $orderPayment->order_id;
                            $maildata['name'] = $customer_name;
                            $maildata['scan'] = 'qrcode' . $orderPayment->order_id . '.png';
                            $maildata['order'] = Order::leftJoin('customers', function ($join) {
                                $join->on('customers.customer_id', '=', 'orders.order_customerid');
                            })->leftJoin('address', function ($join) {
                                $join->on('address.address_id', '=', 'orders.order_address_id');
                            })->find($orderPayment->order_id);

                            $maildata['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $orderPayment->order_id)
                                ->get(['od_qty', 'od_vattotal', 'product_name', 'image']);
                            foreach ($maildata['orderdetail'] as $key => $value) {
                                if ($value['image'] != "") {
                                    $maildata['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
                                } else {
                                    $maildata['orderdetail'][$key]['image'] = asset('img/logo.png');
                                }
                            }
                            Mail::to($customer_email)
                                ->send(new PlaceOrder($maildata));

                            $this->OrderAssignment($request->orderId);
                            if ($request->session()->has('type')) {
                                $data['status'] = 'Success';
                                $data['message'] = 'Your amount has been successfully paid !!';
                                return view('customer.order_status', $data);
                            }
                            // return redirect('/');
                        }

                        break;

                    case 'CANCELED':
                        $orderPayment->payment_status = 0;
                        $orderPayment->status_code = $paymentStatus;
                        $orderPayment->save();
                        $order = Order::find($request->orderId);
                        $order->order_status = 11;
                        $order->save();
                        if ($request->session()->has('type')) {
                            $data['status'] = 'Cancelled';
                            $data['message'] = 'Your order Cancelled !!';
                            return view('frontend.order_cancelled', $data);
                        }
                        break;

                    default:
                        $orderPayment->payment_status = 0;
                        $orderPayment->status_code = $paymentStatus;
                        $orderPayment->save();
                        $data['status'] = 'Error';
                        $data['message'] = 'Something went wrong !!';
                        // unexpected status returned, show an error
                        return view('frontend.order_cancelled', $data);
                        break;
                }
            }
        }
    }

    // public function CheckBitPayorderStatus(Request $request)
    // {
    //   $bitpayUrl = env('BITPAY_TESTAPI_URL');
    //   $bitpay_token = env('BITPAY_TOKRN');
    //   $data = "token=" . env('BITPAY_TOKRN');
    //   $orderPayment = OrderPayment::where('order_id', $request->orderId)->first();
    //   $id = $orderPayment->paymentid;

    //   $headerData =  array(
    //     'x-accept-version: 2.0.0',
    //     'Content-Type: application/x-www-form-urlencoded'
    //   );

    //   $payment_response = $this->callCurlApi($bitpayUrl . '/invoices/' . $id, $data, 'GET', $headerData);
    //   $result = json_decode($payment_response, true);
    //   // echo '<pre>';print_r($result);die;
    //   $paymentStatus = $result['data']['status'];

    //   switch ($paymentStatus) {
    //     case 'paid':
    //     case 'confirmed':
    //       $orderPayment->payment_status = 1;
    //       $orderPayment->status_code = $paymentStatus;
    //       // $orderPayment->payment_method = 'BitPay';
    //       $orderPayment->save();

    //       $order = Order::find($request->orderId);
    //       $order->order_status = 1;
    //       $order->save();


    //       $detail = Order::leftJoin('customers', function ($join) {
    //         $join->on('customers.customer_id', '=', 'orders.order_customerid');
    //       })->where('order_id', $request->orderId)->first();

    //       $customer_id = $detail['order_customerid'];
    //       $customer_email = $detail['customer_email'];
    //       $customer_name = $detail['customer_name'];

    //       CartModel::where('cart_custid', $detail['order_customerid'])->whereNull('deleted_at')->delete();
    //       ## promo code count
    //       if ($detail['order_promocode'] != "0" && $detail['order_discount'] != 0.00) {
    //         $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $detail['order_promocode'])->get()->count();
    //         if ($custom_used) {
    //           $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->first();
    //           UsedPromoCode::where('c_id', $customer_id)->where('pcode_id',  $detail['order_promocode'])->update(['used_count' => $custuser['used_count'] + 1]);
    //         } else {
    //           $newusedcode = new UsedPromoCode();
    //           $newusedcode->pcode_id =  $detail['order_promocode'];
    //           $newusedcode->c_id = auth('customer')->user()->customer_id;
    //           $newusedcode->used_count = 1;
    //           $newusedcode->save();
    //         }
    //       }

    //       ## Send Email##

    //       $maildata = [];
    //       $maildata['order_id'] = $orderPayment->order_id;
    //       $maildata['name'] = $customer_name;
    //       $maildata['scan'] = 'qrcode' . $orderPayment->order_id . '.png';
    //       $maildata['order'] = Order::leftJoin('customers', function ($join) {
    //         $join->on('customers.customer_id', '=', 'orders.order_customerid');
    //       })->leftJoin('address', function ($join) {
    //         $join->on('address.address_id', '=', 'orders.order_address_id');
    //       })->find($orderPayment->order_id);

    //       $maildata['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $orderPayment->order_id)
    //         ->get(['od_qty', 'od_vattotal', 'product_name', 'image']);
    //       foreach ($maildata['orderdetail'] as $key => $value) {
    //         if ($value['image'] != "") {
    //           $maildata['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
    //         } else {
    //           $maildata['orderdetail'][$key]['image'] = asset('img/logo.png');
    //         }
    //       }
    //       Mail::to($customer_email)
    //         ->send(new PlaceOrder($maildata));

    //       $this->OrderAssignment($request->orderId);

    //       $data_new['status'] = 'Success';
    //       $data_new['message'] = 'Your amount has been successfully paid !!';
    //       return view('frontend.order_status', $data_new);
    //       // return redirect('/');

    //       break;
    //   }
    // }

    public function callCurlApi($url, $data, $method, $headerData = '')
    {
        //  print_r($data);die;
        if ($headerData == '') {
            $headerData = array(
                'Content-Type:application/json',
                'Authorization:Basic ' . env('CM_AUTHORIZATION_TOKEN'),
                'Cookie:BISCUIT=chocolatechip|YepOl'
            );
            $data = json_encode($data);
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headerData,
        ));

        $response = curl_exec($curl);
        $error_msg  = '';
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        if (isset($error_msg)) {
            echo $error_msg;
        }
        curl_close($curl);
        return $response;
    }

    // PayNL Payment
    // ==========================================================================================================================================

    public function idealBanks($orderId): View
    {
        try
        {
            $response = PayNL::getBanks('ideal');
            $view = 'customer.ideal-bank-select';
            $data = [
                'orderId' => $orderId,
                'banklist' => $response
            ];
        }
        finally
        {
            if (empty($response))
            {
                $view = 'customer.order-status';
                $data = [
                    'status' => 'Error',
                    'message' => 'Something went wrong!'
                ];
            }
        }

        return view($view, $data);
    }

    public function idinBanks($orderId): View
    {
        try
        {
            $response = PayNL::getBanks('idin');
            $view = 'customer.idin-bank-select';
            $data = [
                'orderId' => $orderId,
                'banklist' => $response['issuers']
            ];
        }
        finally
        {
            if (empty($response['issuers']))
            {
                $view = 'customer.order-status';
                $data = [
                    'status' => 'Error',
                    'message' => 'Something went wrong!'
                ];
            }
        }

        return view($view, $data);
    }

    public function paynlPayment(Request $request): JsonResponse
    {
        $order = Order::find($request->OrderId);

        if (!$order)
        {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!'
            ]);
        }

        $customer = $order->customer()->first();
        $orderPayment = $order->orderPayments()->firstOrNew();
        $orderPayment->order_id = $order->id;

        $amount = (float) $order->order_final_with_discount * 100;
        $paymentMethod = Str::lower($request->paymentmethod);

        if ($paymentMethod == 'cash' || $paymentMethod == 'pin')
        {
            $orderPayment->identity_transaction_url = '';
            $orderPayment->status_code = 'Pending';
            $orderPayment->save();

            $paymentMethod = $paymentMethod == 'cash' ? 'Cash' : 'Pin at Door';
            $order->update([
                'order_status' => 9,
                'order_payment_status' => 0,
                'payment_method' => $paymentMethod
            ]);

            if ($order->promo_code_id && $order->order_discount)
            {
                $usedPromoCode = $customer->usedPromoCodes()->firstOrNew([
                    'promo_code_id' => $order->promo_code_id
                ]);

                $usedPromoCode->used_count = $usedPromoCode->exists
                    ? $usedPromoCode->used_count + 1
                    : 1;

                $usedPromoCode->save();
            }

            if (app()->environment('production'))
            {
                Mail::to($customer->customer_email)
                    ->send(new PlaceOrderMail($order->first()));
            }

            $this->OrderAssignment($order->id);

            return response()->json([
                'status' => true,
                'redirectUrl' => url('paynlOrderStatus/' . $order->id),
                'message' => ''
            ]);
        }
        else if (PayNL::isPaymentMethodExist($paymentMethod))
        {
            if ($paymentMethod == 'idin')
            {
                $paynlData = [
                    'reference' => 'REF' . $order->id,
                    'issuerId' => $request->bank_issuer,
                    'data[name]' => '1',
                    'data[address]' => '1',
                    'data[isEighteen]' => '1',
                    'data[dateOfBirth]' => '1',
                    'data[gender]' => '1',
                    'data[email]' => '1',
                    'data[phone]' => '1',
                    'returnUrl' => url('CheckAgeAuthentication/' . $order->id),
                    'exchangeUrl' => '',
                ];

                $response = PayNL::idinTransaction($paynlData);
            }
            else
            {
                $paynlData = [
                    'amount' => $amount,
                    'ipAddress' => $request->ip(),
                    'finishUrl' => url('paynlOrderStatus/' . $order->id),
                ];

                if ($paymentMethod == 'klarna')
                {
                    $paynlData['saleData[orderData][0][productId]'] = 1;
                    $paynlData['saleData[orderData][0][description]'] = $order->order_note ?: 'Order';
                }
                else
                {
                    $paynlData['transaction[description]'] = $order->order_note ?: 'Order';
                    $paynlData['enduser[emailAddress]'] = $customer->customer_email;

                    if ($paymentMethod == 'ideal')
                    {
                        $paynlData['paymentOptionSubId'] = $request->bank_issuer;
                    }
                }

                $response = PayNL::transactionCreate($paynlData, $paymentMethod);
            }

            if (
                !empty($response['transaction']['paymentURL']) &&
                !empty($response['transaction']['paymentReference']) &&
                !empty($response['transaction']['transactionId'])
            ) {
                $orderPayment->order_key = $response['transaction']['paymentReference'];
                $orderPayment->identity_entrance_code = sha1(rand(0, 5));
                $orderPayment->identity_transaction_id = $response['transaction']['transactionId'];
                $orderPayment->identity_transaction_url = $response['transaction']['paymentURL'];
                $orderPayment->save();

                $paymentMethod = in_array($paymentMethod, ['ideal', 'idin'])
                    ? Str::upper($paymentMethod)
                    : Str::headline($paymentMethod);
                $order->update(['payment_method' => $paymentMethod]);

                return response()->json([
                    'status' => true,
                    'redirectUrl' => $response['transaction']['paymentURL'],
                    'message' => ''
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $response['request']['errorMessage'] ?? 'Something went wrong!',
                'res' => $response
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong !!'
        ]);
    }

    // public function checkPaynlExchangeUrl($dbOrderId,Request $request)
    // {
    //     echo '<pre>';
    //     print_r($request->all());
    // }

    public function checkPaynlOrderStatus($dbOrderId, Request $request): View
    {
        $order = Order::find($dbOrderId);

        if (!$order) abort(404);

        $orderPayment = $order->orderPayments()->first();

        if ($order->payment_method == 'Cash' || $order->payment_method == 'Pin at Door')
        {
            $orderPayment->update(['status_code' => 'Pending']);

            return view('customer.order_status', [
                'status' => 'Success',
                'message' => 'Your Order is placed !!'
            ]);
        }

        $response = PayNL::transactionStatus([
            'transactionId' => $orderPayment->identity_transaction_id
        ]);

        if (empty($response['request']['errorId']))
        {
            $paymentStatusCode = $response['paymentDetails']['state'] ?? 0;
            $paymentUpdate = [
                'payment_id' => $request->paymentSessionId,
                'status_code' => $response['paymentDetails']['stateName']
            ];

            if ($paymentStatusCode == 100 || $paymentStatusCode > 0) {
                $paymentUpdate['payment_status'] = 1;
                $orderPayment->update($paymentUpdate);
                $order->update([
                    'order_status' => 9,
                    'order_payment_status' => 1
                ]);

                $customer = $order->customer()->first();

                if ($order->promo_code_id && $order->order_discount) {
                    $usedPromoCode = $customer->usedPromoCodes()->firstOrNew([
                        'promo_code_id' => $order->promo_code_id
                    ]);

                    $usedPromoCode->used_count = $usedPromoCode->exists
                        ? $usedPromoCode->used_count + 1
                        : 1;

                    $usedPromoCode->save();
                }

                if (app()->environment('production'))
                {
                    Mail::to($customer->customer_email)
                        ->send(new PlaceOrderMail($order->first()));
                }

                $this->OrderAssignment($order->id);
            }
            else
            {
                $order->update(['order_status' => 11]);
                $orderPayment->update($paymentUpdate);
            }

            if ($request->session()->has('type'))
            {
                if ($paymentStatusCode == 100)
                {
                    return view('customer.order-status', [
                        'status' => 'Success',
                        'message' => 'Your amount has been successfully paid !!'
                    ]);
                }
                else if ($paymentStatusCode > 0)
                {
                    return view('customer.order-status', [
                        'status' => 'Pending',
                        'message' => 'Your payment is being processed. We will send you a confirmation via email once we have processed your payment.'
                    ]);
                }
                else
                {
                    return view('customer.order-status', [
                        'status' => 'Cancelled',
                        'message' => 'Your order Cancelled !!'
                    ]);
                }
            }
        }

        return view('customer.order-status', [
            'status' => 'Error',
            'message' => 'Something went wrong !!'
        ]);
    }

    public function checkiDINPaynlOrderStatus($dbOrderId, Request $request): View
    {
        $order = Order::find($dbOrderId);

        if (!$order) abort(404);

        $orderPayment = $order->orderPayments()->first();

        $response = PayNL::idinStatus([
            'trxid' => $orderPayment->identity_transaction_id,
        ]);

        if (empty($response['request']['errorId']))
        {
            $paymentStatusCode = $response['data']['state'] == 'Cancelled' ? -90 : 100;
            $paymentUpdate = ['status_code' => $response['data']['state']];

            if ($paymentStatusCode == 100 || $paymentStatusCode > 0)
            {
                $paymentUpdate['payment_status'] = 1;
                $orderPayment->update($paymentUpdate);
                $order->update([
                    'order_status' => 9,
                    'order_payment_status' => 1
                ]);

                $customer = $order->customer()->first();

                if ($order->promo_code_id && $order->order_discount)
                {
                    $usedPromoCode = $customer->usedPromoCodes()->firstOrNew([
                        'promo_code_id' => $order->promo_code_id
                    ]);

                    $usedPromoCode->used_count = $usedPromoCode->exists
                        ? $usedPromoCode->used_count + 1
                        : 1
                    ;

                    $usedPromoCode->save();
                }

                if (app()->environment('production'))
                {
                    Mail::to($customer->customer_email)
                        ->send(new PlaceOrderMail($order->first()));
                }

                $this->OrderAssignment($order->id);
            }
            else
            {
                $order->update(['order_status' => 11]);
                $orderPayment->update($paymentUpdate);
            }

            if ($request->session()->has('type'))
            {
                if ($paymentStatusCode == 100)
                {
                    return view('customer.order-status', [
                        'status' => 'Success',
                        'message' => 'Your amount has been successfully paid !!'
                    ]);
                }
                else if ($paymentStatusCode > 0)
                {
                    return view('customer.order-status', [
                        'status' => 'Pending',
                        'message' => 'Your payment is being processed. We will send you a confirmation via email once we have processed your payment.'
                    ]);
                }
                else
                {
                    return view('customer.order-status', [
                        'status' => 'Cancelled',
                        'message' => 'Your order Cancelled !!'
                    ]);
                }
            }
        }

        return view('customer.order-status', [
            'status' => 'Error',
            'message' => 'Something went wrong !!'
        ]);
    }

    public function CheckAgeAuthentication($dbOrderId, Request $request): RedirectResponse|View
    {
        $order = Order::find($dbOrderId);

        if (!$order) abort(404);

        $orderPayment = $order->orderPayments()->first();

        $response = PayNL::idinStatus([
            'trxid' => $orderPayment->identity_transaction_id,
        ]);

        if (empty($response['request']['errorId']))
        {
            if ($response['data']['state'] != 'Cancelled' || $response['data']['state'] != 'Init')
            {
                $dateOfBirth = $response['data']['dateOfBirth'] ?? null;
                $isValidAge = $response['data']['isEighteen'] ?? false || $dateOfBirth
                    ? time() < strtotime('+18 years', strtotime($dateOfBirth))
                    : false
                ;

                if ($isValidAge)
                {
                    return redirect('ideal-banks/' . $order->id);
                }

                return view('customer.order-status', [
                    'status' => 'Error',
                    'message' => 'You are under 18 so cant place order in system!'
                ]);
            }
        }

        return view('customer.order-status', [
            'status' => 'Error',
            'message' => 'Something went wrong !!'
        ]);
    }

    public static function OrderAssignment($order_id)
    {
        try {
            // $data['oid'] = $order_id;

            $order = Order::find($order_id);
            // $orderdetail = Order::with('customer')->with('address')->where('order_id', $data['oid'])->whereNull('deleted_at')->first();

            // $postcode = $orderdetail['address']['post_code'];
            // $postcode = (int) preg_replace('/[^0-9]/i', '', $postcode);
            // $lat = $orderdetail['address']['address_latitude'];
            // $long = $orderdetail['address']['address_longitude'];
            $address = $order->address()->first();
            $postcode = (int) preg_replace('/[^0-9]/i', '', $address->post_code);
            $lat = $address->latitude;
            $long = $address->longitude;

            $pool_id = Pool::whereAttr($postcode)->pluck('id');
            $pool = Pool::whereAttr($postcode)->first();

            // $pool = Pool::whereIn('id', $poolArr)
            //     ->whereAttr($postcode)
            //     ->first();


            if (count($pool_id) > 0)
            {

                $pool_id = $pool_id['0'];
                $onfranchise = Franchise::whereNull('deleted_at')
                    ->where('fs_on_off', 'online')
                    ->get();
                if (count($onfranchise) > 0) {
                    $franchisesidon = Franchise::whereNull('deleted_at')
                        ->where('fs_on_off', 'online')
                        ->whereRaw("FIND_IN_SET(?, franchise_pool) > 0", [$pool_id])
                        ->get(['franchise_id', 'franchises_name']);

                    $stdate = date('Y-m-d', time());
                    $starttime = date('H:i:s', time());
                    if (count($franchisesidon) == 0) {
                        // $franchisesid = Franchise::whereNull('deleted_at')
                        // ->where('fs_on_off', 'online')
                        // ->get(['franchise_pool'])
                        // ->toArray();
                        // $arr = [];
                        // foreach ($franchisesid as $value) {
                        //     $arr[] = $value['franchise_pool'];
                        // }
                        // $implode = implode(',', $arr);
                        // $poolarray = array_unique(explode(',', $implode));
                        $poolsArr = Franchise::poolsArray();
                        $asspool = Pool::whereNull('deleted_at')->whereIn('pool_id', $poolarray)->get(['pool_id', 'from_postcode'])->toArray();
                        $smallest = [];
                        foreach ($asspool as $value) {
                            $smallest[$value['pool_id']] = abs($value['from_postcode'] - preg_replace('/[^0-9]/i', '', $postcode));
                        }
                        $poolarray = (array_keys($smallest, min($smallest)));
                        $franchisesid = Franchise::whereNull('deleted_at')
                            ->where('fs_on_off', 'online')
                            ->whereRaw("FIND_IN_SET(?, franchise_pool) > 0", [$poolarray[0]])
                            ->get(['franchise_id', 'franchises_name']);
                        $franchisesid = $franchisesid['0']['franchise_id'];
                    } else {

                        $franchisesid = $franchisesidon[0]['franchise_id'];
                    }


                    //Assign delievry person
                    $deliverypersonid = Schedule::whereNull('schedule.deleted_at')->where('s_status', '2')->where('s_pool', $pool_id)
                        ->leftJoin('deliveryperson', function ($join) {
                            $join->on('deliveryperson.dp_id', '=', 'schedule.s_dpid');
                        })
                        ->whereRaw('("' . $stdate . '" >= DATE(s_startdate) AND "' . $stdate . '" <= DATE(s_enddate)) && ( CAST(s_startdate AS TIME) <= "' . $starttime . '" AND   CAST(s_enddate AS TIME) >= "' . $starttime . '")')
                        ->where('s_fid', $franchisesid)
                        ->get(['dp_id', 'dp_name']);

                    if (count($deliverypersonid) == 0) {

                        $deliverypersonid = DeliveryPerson::select('deliveryperson_sub.*', 'deliveryperson.*')->where('deliveryperson.dp_onoff', 'online')->whereNull('deliveryperson_sub.deleted_at')->whereNull('deliveryperson.deleted_at')
                            ->leftJoin('deliveryperson_sub', function ($join) {
                                $join->on('deliveryperson_sub.s_dpid', '=', 'deliveryperson.dp_id')->where('deliveryperson.dp_onoff', 'online');
                            })->where('s_fid', $franchisesid)->orWhereRaw('FIND_IN_SET(?,s_pool)', [$pool_id])->groupBy('dp_id')->get(['dp_id', 'dp_name']);
                        $arr_dp = $deliverypersonid->pluck('dp_id')->toArray();
                        if (count($deliverypersonid) == 0) {
                            // if($lat!='' && $long!='' ){
                            //     $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1  * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                            //     ->where('dp_onoff', 'online')
                            //     ->orderBy('distance', 'ASC')
                            //     ->get();
                            // }

                        } else {
                            if (count($deliverypersonid) > 1) {
                                $odrer = Order::orderBy('order_id', 'desc')->whereIn('od_deliverypersonid', $arr_dp)->first();
                                $dp_order = $odrer['od_deliverypersonid'];
                                if ($lat != '' && $long != '') {
                                    $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1 *  (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                        ->where('dp_onoff', 'online')
                                        ->orderBy('distance', 'ASC')
                                        ->whereIn('dp_id', $arr_dp)
                                        ->whereNotIn('dp_id', [$dp_order])
                                        ->get();
                                } else {
                                    $deliverypersonid = DeliveryPerson::where('dp_onoff', 'online')
                                        ->whereIn('dp_id', $arr_dp)
                                        ->whereNotIn('dp_id', [$dp_order])
                                        ->get();
                                }
                            } else {

                                $deliverypersonid[0]['dp_id'];
                            }
                        }
                    }
                    $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_id'] : '0';
                    $data['franchisesid'] = $franchisesid;
                } else {

                    $deliverypersonid = DeliveryPerson::select('deliveryperson_sub.*', 'deliveryperson.*')->where('dp_onoff', 'online')->whereNull('deliveryperson_sub.deleted_at')->whereNull('deliveryperson.deleted_at')
                        ->leftJoin('deliveryperson_sub', function ($join) {
                            $join->on('deliveryperson_sub.s_dpid', '=', 'deliveryperson.dp_id')->where('deliveryperson.dp_onoff', 'online');
                        })->whereRaw('FIND_IN_SET(?,s_pool)', [$pool_id])->groupBy('dp_id')->get(['dp_id', 'dp_name']);
                    $arr_dp = $deliverypersonid->pluck('dp_id')->toArray();
                    if (count($deliverypersonid) == 0) {
                        if ($lat != '' && $long != '') {
                            $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1 * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                ->where('dp_onoff', 'online')
                                ->orderBy('distance', 'ASC')
                                ->get();
                        }
                    } else {
                        if (count($deliverypersonid) > 1) {
                            $odrer = Order::orderBy('order_id', 'desc')->whereIn('od_deliverypersonid', $arr_dp)->first();
                            $dp_order = $odrer['od_deliverypersonid'];
                            if ($lat != '' && $long != '') {
                                $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1  * (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
                                    ->where('dp_onoff', 'online')
                                    ->orderBy('distance', 'ASC')
                                    ->whereIn('dp_id', $arr_dp)
                                    ->whereNotIn('dp_id', [$dp_order])
                                    ->get();
                            } else {
                                $deliverypersonid = DeliveryPerson::where('dp_onoff', 'online')
                                    ->whereIn('dp_id', $arr_dp)
                                    ->whereNotIn('dp_id', [$dp_order])
                                    ->get();
                            }
                        } else {
                            $deliverypersonid[0]['dp_id'];
                        }
                    }
                    $data['deliverypersonid'] = count($deliverypersonid) > 0 ? $deliverypersonid[0]['dp_id'] : '0';
                    $data['franchisesid'] = '0';
                }
            } else {
                $data['deliverypersonid'] = '0';
                $data['franchisesid'] = '0';
            }

            if ($data['deliverypersonid'] != '0' || $data['franchisesid'] != "0") {

                Order::where('order_id', $order_id)->update(['order_status' => '2', 'franchise_id' => $data['franchisesid'], 'od_deliverypersonid' => $data['deliverypersonid'], 'od_assignedtime' => now()]);
                if ($data['deliverypersonid'] != '0') {
                    if ($data['franchisesid'] != "0") {

                        $frname = Franchise::find($data['franchisesid']);
                        $mess = $frname['franchises_name'] . ' has assigned order order no. ' . $order_id . ' to you';
                    } else {
                        $mess = 'order order no. ' . $order_id . ' to you';
                    }

                    /** notification To Franchise*/
                    $message = 'Order No ' . $order_id . ' Assigned to you';
                    $notification = new Notification;
                    $notification->user_type = 'franchise';
                    $notification->to_id = $data['franchisesid'];
                    $notification->text = $message;
                    $notification->save();

                    /** notification to Delivery Person*/
                    $notification = new Notification();
                    $notification->user_type = 'delivery_person';
                    $notification->to_id = $data['deliverypersonid'];
                    $notification->text = $message;
                    $notification->save();

                    /* push notification */
                    $devpersondetail = DeliveryPerson::find($data['deliverypersonid']);

                    $push = new PushNotification('fcm');
                    $push->setMessage([
                        'data' => [
                            'notification' => [
                                'title' => 'Order Status',
                                'message' => $mess,
                                'sound' => 'default',
                                'order_id' => $order_id,
                                'schedule_id' => 0,
                                'message_type' => 'order',

                                // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            ],
                        ],
                    ])

                        ->setUrl(env('PUSH_NOTIFICATION_URL'))
                        ->setApiKey(env('PUSH_NOTIFICATION_APIKEY'))
                        ->setDevicesToken($devpersondetail['dp_devicetoken'])
                        ->send()
                        ->getFeedback();
                }
            } else {
                $order->update(['order_status' => 1]);
            }
        } catch (\Exception $e) {
        }
    }
}
