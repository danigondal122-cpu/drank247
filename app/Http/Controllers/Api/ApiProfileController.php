<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Delivery;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Laravel\Facades\Image;

class ApiProfileController extends Controller
{
  public function getProfile(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $detail = DeliveryPerson::where('dp_id', $id)->whereNull('deleted_at')->first(['dp_name', 'dp_email', 'dp_contact_no', 'dp_name', 'dp_email', 'dp_image', 'dp_contact_no', 'bank_pass_no', 'bank_pass_front', 'bank_pass_back', 'statement_conduct', 'licence_front', 'licence_back', 'franchise_contract', 'extra_option', 'payroll_contract']);
    $path = asset('/uploads/deliverypersondetail') . '/' . $id . '/';
    $detail['path'] = $path;
    $detail['bank_pass_no'] = $detail['bank_pass_no'] != "" ? $detail['bank_pass_no'] : '';
    $detail['bank_pass_front'] = $detail['bank_pass_front'] != "" ? $detail['bank_pass_front'] : '';
    $detail['bank_pass_back'] = $detail['bank_pass_back'] != "" ? $detail['bank_pass_back'] : '';
    $detail['statement_conduct'] = $detail['statement_conduct'] != "" ? $detail['statement_conduct'] : '';
    $detail['licence_front'] = $detail['licence_front'] != "" ? $detail['licence_front'] : '';
    $detail['licence_back'] = $detail['licence_back'] != "" ? $detail['licence_back'] : '';
    $detail['franchise_contract'] = $detail['franchise_contract'] != "" ? $detail['franchise_contract'] : '';
    $detail['extra_option'] = $detail['extra_option'] != "" ? $detail['extra_option'] : '';
    $detail['payroll_contract'] = $detail['payroll_contract'] != "" ? $detail['payroll_contract'] : '';
    return response()->json(['status' => true, 'message' => 'Successfully!!', 'data' => $detail]);
  }

  public function updateProfileDetail(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $name = $request->input('name');
    $contact_no = $request->input('contact_no');
    $profile = $request->file('profile');
    $update = DeliveryPerson::find($id);
    $update->dp_name = $name;
    $update->dp_contact_no = $contact_no;


    if ($request->hasFile('profile')) {
      $image = $request->file('profile');
      $imagename = time() . '_' . $image->getClientOriginalName();
      $img = Image::read($image->path());
      $img->resize(100, 100, function ($constraint) {
        $constraint->aspectRatio();
      })->save(public_path('uploads/deliveryperson/thumb') . '/' . $imagename);
      $image->move(public_path('uploads/deliveryperson/'), $imagename);
      $update->dp_image = $imagename;
    }
    $update->save();
    return response()->json(['status' => true, 'message' => 'Profile Detail Updated!!']);
  }
  public function updateProfile(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $profile = $request->file('profile');
    $update = DeliveryPerson::find($id);

    if ($request->hasFile('profile')) {
      $image = $request->file('profile');
      $imagename = time() . '_' . $image->getClientOriginalName();
      $img = Image::read($image->path());
      $img->resize(100, 100, function ($constraint) {
        $constraint->aspectRatio();
      })->save(public_path('uploads/deliveryperson/thumb') . '/' . $imagename);
      $image->move(public_path('uploads/deliveryperson/'), $imagename);
      $update->dp_image = $imagename;
    }
    $update->save();
    return response()->json(['status' => true, 'message' => 'Profile image Updated!!']);
  }
  public function uploadDocument(Request $request)
  {

    $token = $request->input('token');
    $id = $request->input('id');
    $type = $request->input('type');
    $document = $request->file('document');
    // $bank_pass_no=$request->file('bank_pass_no');
    // $bank_pass_front=$request->file('bank_pass_front');
    // $bank_pass_back=$request->file('bank_pass_back');
    // $statement_conduct=$request->file('statement_conduct');
    // $licence_front=$request->file('licence_front');
    // $licence_back=$request->file('licence_back');
    // $franchise_contract=$request->file('franchise_contract');
    // $extra_option=$request->file('extra_option');

    $path = public_path('uploads/deliverypersondetail/' . $id);
    $paththumb = public_path('uploads/deliverypersondetail/' . $id . '/thumb');
    if (!File::isDirectory($path)) {
      File::makeDirectory($path, 0777, true, true);
      File::makeDirectory($paththumb, 0777, true, true);
    }
    $update = DeliveryPerson::find($id);

    if ($request->hasFile('document')) {
      $image = $request->file('document');
      $imagename = time() . '_' . $image->getClientOriginalName();
      $img = Image::read($image->path());
      $img->resize(100, 100, function ($constraint) {
        $constraint->aspectRatio();
      })->save(public_path('uploads/deliverypersondetail/' . $id . '/') . 'thumb/' . $imagename);
      $image->move(public_path('uploads/deliverypersondetail/' . $id), $imagename);
      $update->$type = $imagename;
    }


    $update->save();

    return response()->json(['status' => true, 'message' => 'Successfully!!']);
  }
  public function changePassword(Request $request)
  {

    $id = $request->input('id');
    $token = $request->input('token');
    $new_password = $request->input('new_password');
    $old_password = $request->input('old_password');

    if ($new_password != "") {
      $detail = DeliveryPerson::find($id);
      if (!(Hash::check($old_password, $detail->dp_password))) {
        $message = 'Your Current Password is incorrect';
        return response()->json(['status' => false, 'message' => $message]);
      } else {
        DeliveryPerson::where('dp_id', $id)->whereNull('deleted_at')->update(['dp_password' => bcrypt($new_password)]);
      }
      $message = 'Password Changed Successfully';
      return response()->json(['status' => true, 'message' => $message]);
    } else {
      $message = 'Please add Password';
      return response()->json(['status' => true, 'message' => $message]);
    }
  }
}
