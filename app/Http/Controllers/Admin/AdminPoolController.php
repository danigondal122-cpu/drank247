<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminPoolController extends Controller
{
    public function create()
    {
        $data['row'] = [];

        return view('admin.pool.create', $data);
    }

    public function edit($id)
    {
        $data['row'] = [];

        if ($id) {
            $data['row'] = Pool::findOrFail($id);
        }

        return view('admin.pool.create', $data);
    }

    public function index()
    {
        return view('admin.pool.list');
    }

    public function getList(Request $request)
    {
        $query = Pool::whereNull('deleted_at');
        $column_order = ['from_postcode', 'to_postcode', 'area']; //set column field database for datatable orderable
        $column_search = ['from_postcode', 'to_postcode', 'area']; //set column field database for datatable searchable
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
            $query = $query->orderBy('id', 'Desc');
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
        $id = $request->id;

        if ($request->id) {
            $rules = [
                'from_postcode'       => 'required|unique:pools,from_postcode,'.$request->id.',id,deleted_at,NULL',
                'to_postcode'         => 'required|unique:pools,to_postcode,'.$request->id.',id,deleted_at,NULL',
                'area'                => 'required',
                'delivery_charge'     => 'required',
                'delivery_start_from' => 'required',
                'delivery_free_from'  => 'required',
            ];
        } else {
            $rules = [
                // 'from_postcode' => 'required|unique:pools,from_postcode,NULL,deleted_at',
                // 'to_postcode' => 'required|unique:pools,to_postcode,NULL,deleted_at',
                'from_postcode'       => ['required', Rule::unique('pools')->whereNull('deleted_at')],
                'to_postcode'         => ['required', Rule::unique('pools')->whereNull('deleted_at')],
                'area'                => 'required',
                'delivery_charge'     => 'required',
                'delivery_start_from' => 'required',
                'delivery_free_from'  => 'required',
            ];
        }

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
                $message = 'Poolen succesvol bijgewerkt';
                $product = Pool::find($request->id);
            } else {
                $message = 'Poolen succesvol toegevoegd';
                $product = new Pool;
            }
            $product->from_postcode = $request->input('from_postcode');
            $product->to_postcode = $request->input('to_postcode');
            $product->area = $request->input('area');
            // $product->city = $request->input('city');
            $product->delivery_charge = $request->input('delivery_charge');
            $product->delivery_start_from = $request->input('delivery_start_from');
            $product->delivery_free_from = $request->input('delivery_free_from');

            $product->save();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'admin/pool/list',
                ]);
        }
    }

    public function deletePool(Request $request)
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
            Pool::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Pool deleted !',
                ]);
        }
    }
}
