<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Delivery;
use App\Mail\ForgotPasswordForDelivery;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ApiLoginController extends Controller
{
  public function login(Request $request)
  {
    $email = $request->input('dp_email');
    $password = $request->input('dp_password');
    $device = $request->input('dp_device');
    $device_token = $request->input('dp_devicetoken');

    $detail = DeliveryPerson::where('dp_email', $email)->whereNull('deleted_at')->first();
    if ($detail) {
      if (!(Hash::check($password, $detail->dp_password))) {
        return response()->json(['status' => false, 'message' => 'incorrect Password']);
      } else {
        $dp_hash = time();
        DeliveryPerson::where('dp_email', $email)->update(['dp_devicetoken' => $device_token, 'dp_device' => $device, 'dp_hash' => $dp_hash]);
        $deliverypersondetail = DeliveryPerson::where('dp_email', $email)->whereNull('deleted_at')->first(['dp_id', 'dp_name', 'dp_email', 'dp_password', 'dp_contact_no', 'dp_street', 'dp_city', 'dp_state', 'dp_postcode', 'dp_image', 'dp_device', 'dp_devicetoken', 'dp_hash', 'dp_lat', 'dp_lat', 'dp_onoff', 'dp_startodometer_number', 'dp_stopodometer_number', 'history_id']);
        return response()->json(['status' => true, 'message' => 'login Successfully', 'data' => $deliverypersondetail]);
      }
    } else {
      return response()->json(['status' => false, 'message' => 'Email does not exist']);
    }
  }
  public function forgotPassword(Request $request)
  {
    $email = $request->input('dp_email');
    $checkmail = DeliveryPerson::where('dp_email', $email)->whereNull('deleted_at')->first();

    if ($checkmail) {
      $token = Str::random(6);

      $checkmail->dp_password = Hash::make(($token));
      $checkmail->save();
      $maildata = [];
      $maildata['name'] = $checkmail['dp_name'];
      $maildata['email'] = $email;
      $maildata['password'] = $token;
      Mail::to($email)
        ->send(new ForgotPasswordForDelivery($maildata));

      return response()
        ->json(['status' => true, 'message' => 'Password sent to your Email']);
    } else {
      return response()->json(['status' => false, 'message' => 'Email does not exist']);
    }
  }
  public function logout(Request $request)
  {
    $id = $request->input('id');
    DeliveryPerson::where('dp_id', $id)->update(['dp_devicetoken' => ""]);
    return response()->json(['status' => true, 'message' => 'Logout Successfully']);
  }

  public function onlineOffline(Request $request)
  {
    $token = $request->input('token');
    $dp_id = $request->input('id');
    $type = $request->input('type');

    DeliveryPerson::where('dp_id', $dp_id)->update(['dp_onoff' => $type]);
    return response()->json(['status' => true, 'message' => $type . ' Successfully!!']);
  }
}
