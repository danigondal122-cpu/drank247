<?php

namespace App\Http\Controllers\CustomerService;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CSContactUsController extends CSNotificationController
{
    public function ContactUsList()
    {
        return view('customerservice.contactus.list');
    }

    public function getList(Request $request)
    {
        $query = ContactUs::whereNull('deleted_at')->where('to_send', 'Customer Service');
        $column_order = ['name', 'email', 'contact_no', 'subject', 'message']; //set column field database for datatable orderable
        $column_search = ['name', 'email', 'contact_no', 'subject', 'message']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        $rawQuery = '';
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];
            $i = 0;
            foreach ($column_search as $key => $value) {
                // dd($value);
                if ($i === 0) { // first loop
                    $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $rawQuery .= $value.' LIKE "%'.$search.'%"';
                } else {
                    $rawQuery .= ' OR '.$value.' LIKE "%'.$search.'%"';
                }
                if (count($column_search) - 1 == $i) {
                    //last loop
                    $rawQuery .= ')'; //close bracket
                }
                $i++;
            }
            $query = $query->whereRaw($rawQuery);
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('id', 'DESC');
        }

        $total = $query->get()->count();
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
