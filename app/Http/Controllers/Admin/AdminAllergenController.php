<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\Customer;
use App\Models\DeliveryPerson;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminAllergenController extends Controller
{
    public function create()
    {
        $data['row'] = [];

        return view('admin.allergen.create', $data);
    }

    public function edit($id)
    {
        $data['row'] = Allergen::findOrFail($id);

        return view('admin.allergen.create', $data);
    }

    public function index()
    {
        $data['deliverylist'] = DeliveryPerson::whereNull('deleted_at')->get();
        $data['customerlist'] = Customer::whereNull('deleted_at')->get();
        $data['franchiselist'] = Franchise::whereNull('deleted_at')->get();

        return view('admin.allergen.list', $data);
    }

    public function getList(Request $request)
    {
        $query = Allergen::whereNull('deleted_at'); //$request->get('cat_id')

        $column_order = ['name', 'deliverect_value']; //set column field database for datatable orderable
        $column_search = ['name', 'deliverect_value']; //set column field database for datatable searchable
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

    public function save(Request $request)
    {
        $rules = [
            'name' => 'required',
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
            if ($request->id) {
                $message = 'Allergen succesvol bijgewerkt';
                $allergen = Allergen::find($request->id);
            } else {
                $message = 'Allergen succesvol toegevoegd';
                $allergen = new Allergen;
            }
            $allergen->name = $request->input('name');
            $allergen->save();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'admin/allergen/list',
                ]);
        }
    }

    public function deleteAllergen(Request $request)
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
            Allergen::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Message deleted !',
                ]);
        }
    }
}
