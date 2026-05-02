<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminChangePasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required',
            're_password'      => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'status'=> false,
                    'type'  => 'VALIDATION',
                    'errors'=> $validator->errors(),
                ]);
        }
        /** @var Admin $user */
        $user = $request->user('admin');

        if (! $user) {
            return response()->json([
                'status' => false,
                'type'   => 'SYSTEM',
                'msg'    => 'Something went wrong!',
            ]);
        }

        if (Hash::check($request->current_password, $user->password)) {
            $user->password = $request->new_password;
            $user->save();
            $response = [
                'status' => true,
                'type'   => 'SUCCESS',
                'page'   => 'admin/dashboard',
                'msg'    => 'password changed successfully',
            ];
        } else {
            $response = [
                'status' => false,
                'type'   => 'VALIDATION',
                'errors' => ['current_password'=>['password not matched']],
            ];
        }

        return response()->json($response);

    }
}
