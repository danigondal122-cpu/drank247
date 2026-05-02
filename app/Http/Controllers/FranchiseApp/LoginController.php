<?php

namespace App\Http\Controllers\FranchiseApp;

use App\Http\Controllers\Controller;

use App\Mail\ForgotPasswordForFranchiseApp;

use App\Models\Franchise;
use App\Models\Pool; 

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
  public function login(Request $request)
  {
    $email = $request->input('email');
    $password = $request->input('password');
    $device = $request->input('device');
    $device_token = $request->input('device_token');
    $language = $request->input('language');

    $detail = Franchise::where('franchises_email', $email)->whereNull('deleted_at')->first();
    if ($detail) {
      if (!(Hash::check($password, $detail->password))) {
        $message = ($language == 'nl') ?  'incorrect Password'  : 'incorrect Password';
        return response()->json(['status' => false, 'message' => $message]);
      } else {
        $franchise_hash = time();
        Franchise::where('franchises_email', $email)->update(['franchise_devicetoken' => $device_token, 'franchise_device' => $device, 'franchise_hash' => $franchise_hash]);
        $detail = Franchise::where('franchises_email', $email)->whereNull('deleted_at')->first(['franchise_id', 'franchises_name', 'franchises_no', 'franchises_email', 'mobile_no', 'password', 'image', 'franchise_devicetoken', 'franchise_device', 'franchise_hash']);
        //$detail['customer_address'] = $detail['customer_address'] == null ? "" : $detail['customer_address'];
        $message = ($language == 'nl') ?  'Succesvol inloggen'  : 'Login Successfully!!';
        return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
      }
    } else {
      $message = ($language == 'nl') ?  'Email bestaat al'  : 'Email does not exist';
      return response()->json(['status' => false, 'message' => $message]);
    }
  }
  public function forgotPassword(Request $request)
  {
    $email = $request->input('email');
    $language = $request->input('language');
    $checkmail = Franchise::where('franchises_email', $email)->whereNull('deleted_at')->first();

    if ($checkmail) {
      $token = Str::random(6);

      $checkmail->password = Hash::make(($token));
      $checkmail->save();
      $maildata = [];
      $maildata['name'] = $checkmail['franchises_name'];
      $maildata['email'] = $email;
      $maildata['password'] = $token;
      Mail::to($email)->send(new ForgotPasswordForFranchiseApp($maildata));

      $message = ($language == 'nl') ?  'Wachtwoord verzonden naar uw e-mail'  : 'Password sent to your Email';
      return response()
        ->json(['status' => true, 'message' => $message]);
    } else {
      $message = ($language == 'nl') ?  'Email bestaat al'  : 'Email does not exist';
      return response()->json(['status' => false, 'message' => $message]);
    }
  }
  public function logout(Request $request)
  {
    $id = $request->input('id');
    $language = $request->input('language');
    Franchise::where('franchise_id', $id)->update(['franchise_devicetoken' => ""]);
    $message = ($language == 'nl') ?  'Uitloggen succesvol'  : 'Logout Successfully';
    return response()->json(['status' => true, 'message' => $message]);
  }
  public function getPools()
  {
    $detail = Pool::whereNull('deleted_at')->get()->toArray();
    if ($detail) {
        return response()->json(['status' => true, 'message' => 'data listed successfully', 'data' => $detail]);
    } else {
      return response()->json(['status' => false, 'message' => 'No data found!']);
    }
  }
}
