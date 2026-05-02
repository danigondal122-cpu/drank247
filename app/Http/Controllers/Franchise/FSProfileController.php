<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FSProfileController extends Controller
{
    public function profile()
    {
        $data['row'] = auth('franchise')->user();

        return view('franchise.auth.profile', $data);
    }

    public function profileUpdate(Request $request)
    {
        $f_id = auth('franchise')->id();
        $rules = [
            'email'     => 'required',
            'name'      => 'required',
            'contact_no'=> 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        $profile = Franchise::find(auth('franchise')->id());

        if ($validator->fails()) {
            return response()
                ->json([
                    'status'=> false,
                    'type'  => 'validation',
                    'errors'=> $validator->errors(),
                ]);
        }

        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $filename = 'pr'.time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/franchiseprofile'), $filename);
            $profile->image = $filename;
        }
        if ($request->input('old_cat_pic') == '' && ! $request->hasFile('image_file')) {
            $profile->image = '';
        }

        $path = public_path('uploads/franchiseDocument/'.$f_id);
        $paththumb = public_path('uploads/franchiseDocument/'.$f_id.'/thumb');
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
            File::makeDirectory($paththumb, 0777, true, true);
        }
        if ($request->hasFile('bank_pass_no')) {
            $image = $request->file('bank_pass_no');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->bank_pass_no = $filename;
        }
        if ($request->hasFile('bank_pass_front')) {
            $image = $request->file('bank_pass_front');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->bank_pass_front = $filename;
        }
        if ($request->hasFile('bank_pass_back')) {
            $image = $request->file('bank_pass_back');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->bank_pass_back = $filename;
        }
        if ($request->hasFile('statement_conduct')) {
            $image = $request->file('statement_conduct');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->statement_conduct = $filename;
        }
        if ($request->hasFile('licence_front')) {
            $image = $request->file('licence_front');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->licence_front = $filename;
        }
        if ($request->hasFile('licence_back')) {
            $image = $request->file('licence_back');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->licence_back = $filename;
        }
        if ($request->hasFile('franchise_contract')) {
            $image = $request->file('franchise_contract');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->franchise_contract = $filename;
        }
        if ($request->hasFile('extra_option')) {
            $image = $request->file('extra_option');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->extra_option = $filename;
        }
        if ($request->hasFile('payroll_contract')) {
            $image = $request->file('payroll_contract');
            $filename = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/franchiseDocument/'.$f_id), $filename);
            $profile->payroll_contract = $filename;
        }

        $profile->franchises_name = $request->name;
        $profile->franchises_email = $request->email;
        $profile->mobile_no = $request->contact_no;
        $profile->franchise_number = $request->franchise_number;
        // $profile->fs_on_off = ($request->input('customSwitch3')=='on')?'online':'offline';
        $profile->save();

        return response()
            ->json([
                'status' => true,
                'page'   => 'franchise/stock/list',
                'msg'    => 'Profile succesvol bijgewerkt',
            ]);
    }

    public function changePassword()
    {
        return view('franchise.auth.change_password');
    }

    public function updatePassword(Request $request)
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required',
            're_password'      => 'required|same:new_password',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'VALIDATION',
                    'errors' => $validator->errors(),
                ]);
        }

        /** @var null|Franchise $user */
        $user = auth('franchise')->user();
        if ($user) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                $response = [
                    'status' => true,
                    'type'   => 'SUCCESS',
                    'page'   => 'franchise/stock/list',
                    'msg'    => 'password changed successfully',
                ];
            } else {
                $response = [
                    'status' => false,
                    'type'   => 'VALIDATION',
                    'errors' => ['current_password' => ['password not matched']],
                ];
            }

            return response()->json($response);
        }

        return response()->json([
            'status' => false,
            'type'   => 'SYSTEM',
            'msg'    => 'Something went wrong!',
        ]);

    }

    public function updateOnOff(Request $request)
    {
        /** @var Franchise $franchise */
        $franchise = auth('franchise')->user();
        $franchise?->update(['fs_on_off' => $request->value]);

        return response()
            ->json([
                'status' => true, 'msg' => 'Updated!!', 'page' => '',
            ]);
    }

    public function notificationread(Request $request)
    {
        Notification::where('to_id', auth('franchise')->id())->where('user_type', 'franchise')->update(['status' => 1]);

        return response()
            ->json([
                'status' => true, 'msg' => 'Updated!!', 'page' => '',
            ]);
    }
}
