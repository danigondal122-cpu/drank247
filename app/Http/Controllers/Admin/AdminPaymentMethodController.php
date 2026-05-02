<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class AdminPaymentMethodController extends Controller
{
    public function paymentmethodlist()
    {
        $data[] = '';
        $paymentmethods = PaymentMethod::get();
        $data['paymentmethods'] = $paymentmethods;

        return view('admin.paymentmethod.index', $data);
    }

    public function paymentmethodsave(Request $request)
    {
        $requestData = $request->all('payment_method');
        $paymentmethods = PaymentMethod::get();
        foreach ($paymentmethods as $key => $row) {
            $paymentmethod = PaymentMethod::where('method_name', $row->method_name)->first();
            $paymentmethod->status = 0;
            $paymentmethod->save();
        }
        if (! empty($requestData['payment_method'])) {
            foreach ($requestData['payment_method'] as $row) {
                $paymentmethods = PaymentMethod::where('method_name', $row)->first();
                $paymentmethods->status = 1;
                $paymentmethods->save();
            }
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Success',
            ]);
    }
}
