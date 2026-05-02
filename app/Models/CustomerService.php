<?php

namespace App\Models;

use App\Mail\CustomerServiceForgotPasswordMail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;

class CustomerService extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'customer_services';

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->cs_resettoken = $token;
        $this->save();
        Mail::to($this->cs_email)
            ->send(new CustomerServiceForgotPasswordMail($this));
    }
}
