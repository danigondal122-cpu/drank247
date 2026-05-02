<?php

namespace App\Http\Controllers\FranchiseApp;

use App\Http\Controllers\Controller;

use App\Models\Franchise;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $id = $request->input('id');
        $language = $request->input('language');

        $detail = Franchise::where('franchise_id', $id)->whereNull('deleted_at')->first(['franchise_id','franchises_name', 'franchises_email', 'mobile_no', 'franchise_number', 'image','bank_pass_no','bank_pass_front','bank_pass_back','statement_conduct','licence_front','licence_back','franchise_contract','extra_option','payroll_contract',]);
        $message = ($language == 'nl') ?  'Met succes!'  : 'Successfully!';
        return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
    }
    public function updateProfileDetail(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $contact_no = $request->input('contact_no');
        $email = $request->input('email');
        $franchise_number = $request->input('franchise_number');
        $language = $request->input('language');

        $rules = [
            'email' => 'required',
            'name' => 'required',
            'contact_no'=> 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        $profile = Franchise::find($id);
        if ($validator->fails()) {
        return response()
            ->json([
            'status' => false,
            'type' => 'VALIDATION',
            'errors' => $validator->errors()
            ]);
        } else {
            if ($request->hasFile('image_file')) {
                $image =$request->file('image_file');
                $filename = 'pr'.time(). '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/franchiseprofile'), $filename);
                $profile->image=$filename;
            }
            if($request->input('old_cat_pic')=='' && !$request->hasFile('image_file')){
                $profile->image = '';
            }

            $path = public_path('uploads/franchiseDocument/'.$id);
            $paththumb = public_path('uploads/franchiseDocument/'.$id.'/thumb');
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
                File::makeDirectory($paththumb, 0777, true, true);
            }
            if ($request->hasFile('bank_pass_no')) {
                $image =$request->file('bank_pass_no');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->bank_pass_no=$filename;
            }
            if ($request->hasFile('bank_pass_front')) {
                $image =$request->file('bank_pass_front');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->bank_pass_front=$filename;
            }
            if ($request->hasFile('bank_pass_back')) {
                $image =$request->file('bank_pass_back');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->bank_pass_back=$filename;
            }
            if ($request->hasFile('statement_conduct')) {
                $image =$request->file('statement_conduct');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->statement_conduct=$filename;
            }
            if ($request->hasFile('licence_front')) {
                $image =$request->file('licence_front');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->licence_front=$filename;
            }
            if ($request->hasFile('licence_back')) {
                $image =$request->file('licence_back');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->licence_back=$filename;
            }
            if ($request->hasFile('franchise_contract')) {
                $image =$request->file('franchise_contract');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->franchise_contract=$filename;
            }
            if ($request->hasFile('extra_option')) {
                $image =$request->file('extra_option');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->extra_option=$filename;
            }
            if ($request->hasFile('payroll_contract')) {
                $image =$request->file('payroll_contract');
                $filename = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('uploads/franchiseDocument/'.$id), $filename);
                $profile->payroll_contract=$filename;
            }

            $profile->franchises_name = $request->name;
            $profile->franchises_email = $request->email;
            $profile->mobile_no = $request->contact_no;
            $profile->franchise_number = $request->franchise_number;
            $profile->save();
            
            $detail = Franchise::where('franchise_id', $id)->whereNull('deleted_at')->first(['franchise_id','franchises_name', 'franchises_email', 'mobile_no', 'franchise_number', 'image','bank_pass_no','bank_pass_front','bank_pass_back','statement_conduct','licence_front','licence_back','franchise_contract','extra_option','payroll_contract',]);
        
            $message = ($language == 'nl') ?  'Profieldetail bijgewerkt!!'  : 'Profile Detail Updated!!';
            return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);         
        }
    }
    public function changePassword(Request $request)
    {
        $id = $request->input('id');
        $new_password = $request->input('new_password');
        $old_password = $request->input('old_password');
        $language = $request->input('language');


        if ($new_password != "") {
            $detail = Franchise::find($id);
            if (!(Hash::check($old_password, $detail->password))) {
                $message = ($language == 'nl') ?  'Uw huidige wachtwoord is onjuist'  : 'Your Current Password is incorrect';
                return response()->json(['status' => false, 'message' => $message]);
            } else {
                Franchise::where('franchise_id', $id)->whereNull('deleted_at')->update(['password' => bcrypt($new_password)]);
            }
            $message = ($language == 'nl') ?  'Wachtwoord succesvol veranderd'  : 'Password Changed Successfully';
            return response()->json(['status' => true, 'message' => $message]);
        } else {
            $message = ($language == 'nl') ?  'Voeg wachtwoord toe'  : 'Please add Password';
            return response()->json(['status' => true, 'message' => $message]);
        }
    }
}
