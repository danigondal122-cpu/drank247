<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;

use App\Models\Category;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function categoryList(Request $request)
  {
    // $id = $request->input('id');
    // $token = $request->input('token');
    $page_no = $request->input('page_no');
    $start_from = ($page_no - 1) * 20;
    $per_page = 20;
    $search = $request->input('search');

    $query = Category::select('id', 'category_name', 'description', 'image')
      ->where('is_show', '1')
      ->where('category_id', '0');

    $rawQuery = '';
    $column_search = ['category_name']; //set column field database for datatable searchable 
    //Search 
    if ($request->input('search') && $request->input('search') != '') {

      $search = $request->input('search');
      $i = 0;
      foreach ($column_search as $key => $value) {
        if ($i === 0) // first loop
        {
          $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $rawQuery .= $value . ' LIKE "%' . $search . '%"';
        } else {
          $rawQuery .= ' OR ' . $value . ' LIKE "%' . $search . '%"';
        }
        if (count($column_search) - 1 == $i) {
          //last loop
          $rawQuery .= ')'; //close bracket
        }
        $i++;
      }
      $query = $query->whereRaw($rawQuery);
    }
    // $data = $query->skip($start_from)->limit($per_page)->get();
    $data = $query->get();

    $i = 0;

    foreach ($data as $val) {
      $data[$i]['subcat'] = Category::wherenull('deleted_at')
        ->where('category_id', $val['id'])->get()->count();
      $i++;
    }

    return response()
      ->json([
        'status' => true,
        'data' => $data,
      ]);
  }
  public function subcategorylist(Request $request)
  {
    // $id = $request->input('id');
    // $token = $request->input('token');
    $cat_id = $request->input('cat_id');
    $page_no = $request->input('page_no');
    $start_from = ($page_no - 1) * 20;
    $per_page = 20;
    $search = $request->input('search');
    $query = Category::select('id', 'category_name', 'description', 'image')
      ->where('is_show', '1')
      ->where('category_id', $cat_id);
    $rawQuery = '';
    $column_search = ['category_name']; //set column field database for datatable searchable 
    //Search 
    if ($request->input('search') && $request->input('search') != '') {

      $search = $request->input('search');
      $i = 0;
      foreach ($column_search as $key => $value) {
        if ($i === 0) // first loop
        {
          $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $rawQuery .= $value . ' LIKE "%' . $search . '%"';
        } else {
          $rawQuery .= ' OR ' . $value . ' LIKE "%' . $search . '%"';
        }
        if (count($column_search) - 1 == $i) {
          //last loop
          $rawQuery .= ')'; //close bracket
        }
        $i++;
      }
      $query = $query->whereRaw($rawQuery);
    }
    // $data = $query->skip($start_from)->limit($per_page)->get();
    $data = $query->get();
    $i = 0;


    return response()
      ->json([
        'status' => true,
        'data' => $data,
      ]);
  }
}
