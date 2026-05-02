<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;

use App\Models\Customer;
use App\Models\Banner;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
  public function getProfile(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $language = $request->input('language');

    $detail = Customer::where('customer_id', $id)->whereNull('deleted_at')->first(['customer_name', 'customer_email', 'customer_type', 'customer_phone', 'profile']);
    $message = ($language == 'nl') ?  'Met succes!'  : 'Successfully!';
    return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
  }
  public function updateProfileDetail(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $name = $request->input('name');
    $contact_no = $request->input('contact_no');
    $profile = $request->file('profile');
    $type = $request->input('type');
    $language = $request->input('language');

    $update = Customer::find($id);
    $update->customer_name = $name;
    $update->customer_phone = $contact_no;
    $update->customer_type = $type;

    if ($request->hasFile('profile')) {
      $image = $request->file('profile');
      $imagename = time() . '_' . $image->getClientOriginalName();
      $img = Image::read($image->path());
      $img->resize(100, 100, function ($constraint) {
        $constraint->aspectRatio();
      })->save(public_path('uploads/customer/thumb') . '/' . $imagename);
      $image->move(public_path('uploads/customer/'), $imagename);
      $update->profile = $imagename;
    }
    $update->save();
    $detail = Customer::where('customer_id', $id)->whereNull('deleted_at')->first(['customer_name', 'customer_email', 'customer_type', 'customer_phone', 'profile']);

    $message = ($language == 'nl') ?  'Profieldetail bijgewerkt!!'  : 'Profile Detail Updated!!';
    return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
  }
  public function changePassword(Request $request)
  {

    $id = $request->input('id');
    $token = $request->input('token');
    $new_password = $request->input('new_password');
    $old_password = $request->input('old_password');
    $language = $request->input('language');


    if ($new_password != "") {
      $detail = Customer::find($id);
      if (!(Hash::check($old_password, $detail->password))) {
        $message = ($language == 'nl') ?  'Uw huidige wachtwoord is onjuist'  : 'Your Current Password is incorrect';
        return response()->json(['status' => false, 'message' => $message]);
      } else {
        Customer::where('customer_id', $id)->whereNull('deleted_at')->update(['password' => bcrypt($new_password)]);
      }
      $message = ($language == 'nl') ?  'Wachtwoord succesvol veranderd'  : 'Password Changed Successfully';
      return response()->json(['status' => true, 'message' => $message]);
    } else {
      $message = ($language == 'nl') ?  'Voeg wachtwoord toe'  : 'Please add Password';
      return response()->json(['status' => true, 'message' => $message]);
    }
  }
  public function bannerList(Request $request)
  {

    $data = Banner::get('image');

    return response()->json(['status' => true, 'data' => $data]);
  }
}
