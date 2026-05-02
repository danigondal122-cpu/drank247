<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;

use App\Mail\frontendCustomerCredential;

use App\Models\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SignUpController extends Controller
{
  public function signUpWithSocial(Request $request)
  {
    $email = $request->input('email');
    $name = $request->input('name');
    $profile = $request->input('profile');
    $device = $request->input('device');
    $social_type = $request->input('social_type');
    // $password=$request->input('password');
    $social_id = $request->input('social_id');
    $language = $request->input('language');

    $finduser = Customer::where('social_login_id', $social_id)->where('login_type', $social_type)->first();
    if ($finduser) {
      $customer_hash = time();
      $newRegister = Customer::find($finduser->customer_id);
      $newRegister->customer_hash = $customer_hash;
      if ($finduser['profile'] == "") {
        if (isset($profile) && $profile != "") {
          $profile = file_get_contents($profile);
          $imagename = time() . '.png';
          $imgthumb = public_path('uploads/customer/thumb') . '/' . $imagename;
          $img = public_path('uploads/customer') . '/' . $imagename;
          file_put_contents($img, $profile);
          file_put_contents($imgthumb, $profile);
        } else {
          $imagename = "";
        }
        $newRegister->profile = $imagename;
      }
      $newRegister->save();
      $detail = Customer::where('social_login_id', $social_id)->whereNull('deleted_at')->first(['customer_id', 'customer_name', 'customer_email', 'customer_type', 'customer_phone', 'password', 'profile', 'customer_address', 'customer_devicetoken', 'customer_device', 'customer_hash']);
      $detail['customer_address'] = $detail['customer_address'] == null ? "" : $detail['customer_address'];
      $message = ($language == 'nl') ?  'Succesvol inloggen'  : 'Login Successfully';
      return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
    } else {
      $count = Customer::where('customer_email', $email)->whereNull('deleted_at')->get()->count();
      if ($count == 0) {
        if (isset($profile) && $profile != "") {
          $profile = file_get_contents($profile);
          $imagename = time() . '.png';
          $imgthumb = public_path('uploads/customer/thumb') . '/' . $imagename;
          $img = public_path('uploads/customer') . '/' . $imagename;
          file_put_contents($img, $profile);  
          file_put_contents($imgthumb, $profile);
        } else {
          $imagename = "";
        }

        $newRegister = new Customer();
        $newRegister->social_login_id = $social_id;
        $newRegister->login_type = $social_type;
        $newRegister->customer_name = $name;
        $newRegister->customer_email = $email;
        $newRegister->password = Hash::make($social_id);
        $newRegister->customer_from = '2';
        $newRegister->profile = $imagename;
        $newRegister->save();
        $customer_hash = time();
        Customer::where('customer_email', $email)->update(['customer_device' => $device, 'customer_hash' => $customer_hash]);
        $detail = Customer::where('social_login_id', $social_id)->whereNull('deleted_at')->first(['customer_id', 'customer_name', 'customer_email', 'customer_type', 'customer_phone', 'password', 'profile', 'customer_address', 'customer_devicetoken', 'customer_device', 'customer_hash']);
        $detail['customer_address'] = $detail['customer_address'] == null ? "" : $detail['customer_address'];
        $message = ($language == 'nl') ?  'Succesvol Geregistreerd'  : 'Registered Successfully';
        return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
      } else {
        $message = ($language == 'nl') ?  'Email bestaat al'  : 'Email already exist';
        return response()->json([
          'status' => false,
          'message' => $message

        ]);
      }
    }
  }
  public function signUp(Request $request)
  {
    $email = $request->input('email');
    $name = $request->input('name');
    $password = $request->input('password');
    $customer_type = $request->input('customer_type');
    $device = $request->input('device');
    $language = $request->input('language');

    $count = Customer::where('customer_email', $email)->whereNull('deleted_at')->get()->count();
    if ($count == 0) {
      // dd($request->all());
      $customer_hash = time();
      $newRegister = new Customer();
      $newRegister->customer_name = $name;
      $newRegister->customer_email = $email;
      $newRegister->customer_type = $customer_type;
      $newRegister->login_type = 'NORMAL';
      $newRegister->customer_from = '2';
      $newRegister->customer_device = $device;
      $newRegister->customer_hash = $customer_hash;
      $newRegister->password = Hash::make($password);

      $newRegister->save();

      if ($newRegister) {
        $mail_data = [
          'name' => $name,
          'email' => $email,
          'password' => $password,
        ];
        Mail::to($request->email)
          ->send(new frontendCustomerCredential($mail_data));
      }

      $detail = Customer::where('customer_id', $newRegister->customer_id)->whereNull('deleted_at')->first(['customer_id', 'customer_name', 'customer_email', 'customer_type', 'customer_phone', 'password', 'profile', 'customer_address', 'customer_devicetoken', 'customer_device', 'customer_hash']);
      $detail['customer_address'] = $detail['customer_address'] == null ? "" : $detail['customer_address'];
      // if (Auth::guard('customer')->attempt(['email' => $newRegister->email, 'password' => $request->password])) {
      //   return response()
      //     ->json([
      //       'status' => true,
      //       'page' => 'admin/dashboard'
      //     ]);
      // } else {
      //   return response()
      //     ->json([
      //       'status' => true,
      //       'page' => '/'
      //     ]);
      // }
      $message = ($language == 'nl') ? 'Succesvol Geregistreerd'  : 'Registered successfully';
      return response()
        ->json([
          'status' => true,
          'message' => $message,
          'data' => $detail
        ]);
    } else {
      $message = ($language == 'nl') ?  'Email bestaat al' : 'Email Already Exits';
      return response()
        ->json([
          'status' => false,
          'message' => $message,
        ]);
    }
  }
}
