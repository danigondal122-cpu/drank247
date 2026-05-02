<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminUpdateProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email'      => 'required',
            'name'       => 'required',
            // 'mobile_no' => 'required',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'postcode' => 'required',
            // 'module' => 'required',
            // 'company' => 'required',
            // 'vat' => 'required',
            // 'commerce_number' => 'required',
        ]);

        /** @var Admin $profile */
        $profile = $request->user('admin');

        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        }

        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/adminprofile'), $filename);
            $profile->image = $filename;
        }

        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->admin_mobile_no = $request->mobile_no;
        $profile->admin_street = $request->street;
        $profile->admin_city = $request->city;
        $profile->admin_state = $request->state;
        $profile->admin_postcode = $request->postcode;
        $profile->admin_company = $request->input('company');
        $profile->admin_vat = $request->input('vat');
        $profile->admin_commerce_number = $request->input('commerce_number');

        if ($request->input('old_cat_pic') == '' && ! $request->hasFile('image_file')) {
            $profile->image = '';
        }
        $profile->save();

        return response()
            ->json([
                'status' => true,
                'page'   => 'admin/dashboard',
                'msg'    => 'Profile succesvol bijgewerkt',
            ]);
    }
}
