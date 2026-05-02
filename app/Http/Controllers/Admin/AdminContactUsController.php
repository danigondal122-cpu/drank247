<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminContactUsController extends Controller
{
    public function index()
    {
        return view('admin.contactus.list');
    }

    public function getList(Request $request)
    {
        $query = ContactUs::whereNull('deleted_at');
        $column_order = ['name', 'email', 'contact_no', 'subject', 'message']; //set column field database for datatable orderable
        $column_search = ['name', 'email', 'contact_no', 'subject', 'message']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function deleteContactUs(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        } else {
            ContactUs::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Category deleted !',
                ]);
        }
    }
}
