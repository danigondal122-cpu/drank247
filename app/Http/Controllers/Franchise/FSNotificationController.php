<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;

use App\Models\Notification;

class FSNotificationController extends Controller
{
  public function __construct()
  {
    view()->composer('*', function ($view) 
    {
      if(auth('franchise')->check()){
        $data['Notification'] = Notification::where('user_type','franchise')->where('to_id',auth('franchise')->user()->franchise_id)->orderBy('nt_id','DESC')->get();
        $data['n_count']= Notification::where('user_type','franchise')->where('to_id',auth('franchise')->user()->franchise_id)->where('nt_status','0')->get()->count();
        $view->with('global', $data);
      }
     
    });   
  }
}
