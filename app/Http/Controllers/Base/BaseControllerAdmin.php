<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;

use App\Models\AssignModule;

/**
 * @deprecated TODO!: Remove this later. Already move the code to resources\views\admin\layout\sidebar.blade.php
 */
class BaseControllerAdmin extends Controller
{
  public function __construct()
  {

    view()->composer('*', function ($view)
    {

      $data=[];
      if(auth('admin')->check()){
        $data['assigned_module'] = AssignModule::where('admin_id',auth('admin')->user()->admin_id)->whereNull('deleted_at')->pluck('module_id')->Toarray();
      }

      $view->with('global', $data);
    });
  }

}
