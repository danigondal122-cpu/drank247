<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\CustomerCredentialsMail;
use App\Mail\CustomerForgotPasswordMail;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerWebAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:customers,customer_email',
            'password' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status' => false,
                'type' => 'VALIDATION',
                'errors' => $validator->errors()
            ]);
        }

        $page = '';
        $customer = Customer::create([
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_type' => $request->type,
            'password' => Hash::make($request->password),
        ]);

        if (app()->environment('production'))
        {
            Mail::to($request->email)->send(new CustomerCredentialsMail($request->all()));
        }
        else
        {
            $page = 'email-render';
            session()->flash('mail', [
                'class' => CustomerCredentialsMail::class,
                'data' => $request->all()
            ]);
        }

        return response()->json([
            'status' => true,
            'page' => $page,
            'message' => 'Registered successfully!!',
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status' => false,
                'type' => 'VALIDATION',
                'errors' => $validator->errors()
            ]);
        }
        
        $credentials = [
            'customer_email' => $request->email,
            'password' => $request->password
        ];
        
        if (auth('customer')->attempt($credentials))
        {
            // if (Cart::content()->count())
            // {
            //     $this->addToCart(Cart::content());
            // }

            return response()->json([
                'status' => true,
                'page' => 'admin/dashboard'
            ]);
        }

        return response()->json([
            'status' => false,
            'type' => 'VALIDATION',
            'errors' => [
                'email' => ['Please enter correct email or password']
            ]
        ]);
    }

    public function logout(): RedirectResponse
    {
        auth('customer')->logout();

        return redirect()->route('homepage');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status' => false,
                'type' => 'VALIDATION',
                'errors' => $validator->errors()
            ]);
        }

        $customer = Customer::where('customer_email', $request->email)->first();
        
        if ($customer)
        {
            $page = '';
            $customer->update([
                'customer_reset_token' => Str::random(10)
            ]);

            if (app()->environment('production'))
            {
                Mail::to($request->email)->send(new CustomerForgotPasswordMail($customer->toArray()));
            }
            else
            {
                $page = 'email-render';
                session()->flash('mail', [
                    'class' => CustomerForgotPasswordMail::class,
                    'data' => $customer->toArray()
                ]);
            }

            return response()->json([
                'status' => true,
                'page' => $page,
                'message' => 'Reset Password link has been sent on your registered email. Please check your email!'
            ]);
        }

        return response()->json([
            'status' => false,
            'type' => 'VALIDATION',
            'errors' => [
                'email' => ['Please enter correct email']
            ]
        ]);
    }

    public function resetPassword(Request $request): View
    {
        $data = [
            'token' => $request->token,
            'id' => $request->id,
            'customer' => Customer::where('id', $request->id)
                ->where('customer_reset_token', $request->token)
                ->first()
        ];

        return view('guest.reset-password', $data);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'new_password' => [
                'required',
                'confirmed',
                Password::defaults()
            ]
        ]);
    
        if ($validator->fails())
        {
            return response()->json([
                'status' => false,
                'type' => 'VALIDATION',
                'errors' => $validator->errors()
            ]);
        }
      
        $customer = Customer::where('id', $request->id)
            ->where('customer_reset_token', $request->resetkey)
            ->first();
  
        if ($customer)
        {
            $customer->update([
                'password' => Hash::make($request->new_password),
                'customer_reset_token' => null
            ]);

            return response()->json([
                'status' => true,
                'page' => '',
                'message' => 'Your Password Changed Successfully.'
            ]);
        }
        
        return response()->json([
            'status' => false,
            'type' => 'VALIDATION',
            'errors' => 'false'
        ]);
    }
}
