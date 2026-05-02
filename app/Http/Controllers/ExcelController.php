<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Exports\OrderExport;
use App\Exports\HistoryExport;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
  public function export(Request $request)
  {
    $data = $request->all();
    Excel::Store(new UsersExport($data), 'Drank247.xlsx','delivery_person_xls_path');
    return response()->json([
      'status'=>true,
      'url'=>asset('uploads/excel/Drank247.xlsx')
    ]);
  } 
  public function historyHoursExport(Request $request)
  {
    $data = $request->all();
    Excel::Store(new HistoryExport($data), 'Drank247.xlsx','delivery_person_xls_path');
    return response()->json([
      'status'=>true,
      'url'=>asset('uploads/excel/Drank247.xlsx')
    ]);
  } 
  public function orderExport(Request $request)
  {
    $data = $request->all();
    Excel::Store(new OrderExport($data), 'Drank247.xlsx','delivery_person_xls_path');
    return response()->json([
      'status'=>true,
      'url'=>asset('uploads/excel/Drank247.xlsx')
    ]);
  } 

}
