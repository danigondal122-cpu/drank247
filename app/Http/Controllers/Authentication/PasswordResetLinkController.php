<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CustomerService;
use App\Models\Franchise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(Request $request): View
    {
        return view('auth.forgot-password', ['authGuard' => $request->segment(1)]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'VALIDATION',
                    'errors' => $validator->errors(),
                ]);
        }

        /** @var null|Admin|Franchise|CustomerService $user */
        $user = match ($request->segment(1)) {
            'admin'     => Admin::where('email', $request->email)->first(),
            'franchise' => Franchise::where('franchises_email', $request->email)->first(),
            'customer_service' => CustomerService::where('cs_email', $request->email)->first(),
            default     => null,
        };

        if (! $user) {
            return response()
                ->json([
                    'status' => false,
                    'type'  => 'VALIDATION',
                    'errors' => [
                        'email' => ['Please enter correct email'],
                    ],
                ]);
        }

        $user->sendPasswordResetNotification(str()->random(10));

        return response()
            ->json([
                'status' => true,
                'message' => 'Your Reset Password link sent to you.Please check you email',
            ]);
    }
}
