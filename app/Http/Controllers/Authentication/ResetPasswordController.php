<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CustomerService;
use App\Models\Franchise;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request, string $id, string $resetToken): View
    {
        $user = $this->getUserQuery(
            $request->segment(1),
            $id,
            $resetToken,
        )?->exists();

        return view('auth.reset-password', [
            'user'      => $user,
            'id'        => $id,
            'token'     => $resetToken,
            'authGuard' => $request->segment(1),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'new_password'     => 'required',
            'confirm_password' => 'required|same:new_password',
            'resetkey'         => 'required',
            'id'               => 'required',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'VALIDATION',
                    'errors' => $validator->errors(),
                ]);
        }

        /** @var null|Admin|Franchise|CustomerService $reset */
        $reset = $this->getUserQuery(
            $request->segment(1),
            $request->id,
            $request->resetkey,
        )?->first();

        if (! $reset) {
            return response()
                ->json([
                    'status'  => false,
                    'type'    => 'EXPIRED',
                    'message' => 'Your reset password link Expierd!!',
                ]);
        }

        $reset_token = $reset instanceof CustomerService ? 'cs_resettoken' : 'reset_token';
        $reset->password = bcrypt($request->new_password);
        $reset->{$reset_token} = '';
        $reset->save();

        return response()
            ->json([
                'status'  => true,
                'page'    => 'admin/login',
                'message' => 'Your Password Changed Successfully.',
            ]);
    }

    /**
     * @return null|Builder|QueryBuilder|Admin|Franchise|CustomerService
     */
    protected function getUserQuery(
        ?string $segment,
        null|string|int $id,
        null|string|int $resetkey
    ) {
        return
        match ($segment) {
            'admin'            => Admin::query()->where('reset_token', $resetkey)->where('id', $id),
            'franchise'        => Franchise::where('reset_token', $resetkey)->where('id', $id),
            'customer_service' => CustomerService::where('cs_resettoken', $resetkey)->where('id', $id),
            default            => null,
        };
    }
}
