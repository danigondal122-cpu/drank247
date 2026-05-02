<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class CSNotificationController extends Controller
{
    public function __construct()
    {
        view()->composer('*', function ($view) {
            if (auth('customer_service')->check()) {
                $data['Notification'] = Notification::where('user_type', 'customer')->where('to_id', auth('customer_service')->user()->id)->orderBy('id', 'DESC')->get();
                $data['n_count'] = Notification::where('user_type', 'customer')->where('to_id', auth('customer_service')->user()->id)->where('status', '0')->count();
                $view->with('global', $data);
            }
        });
    }
}
