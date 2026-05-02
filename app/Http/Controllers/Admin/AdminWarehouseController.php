<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class AdminWarehouseController extends Controller
{
    public function create()
    {
        $data['row'] = [];
        $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();

        return view('admin.warehouse.create', $data);
    }

    public function edit($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = WareHouse::findOrFail($id);
        }
        $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();

        return view('admin.warehouse.create', $data);
    }

    public function index()
    {
        $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();

        return view('admin.warehouse.list', $data);
    }

    public function getList(Request $request)
    {
        $query = WareHouse::whereNull('deleted_at');

        if ($request->get('id') && $request->get('id') != '') {
            $query = $query->where('id', $request->get('id'));
        }

        $column_order = ['wh_name', 'wh_name']; //set column field database for datatable orderable
        $column_search = ['wh_name', 'wh_name']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        $rawQuery = '';
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        // echo $request->order[0]['column'];
        // echo '<br/>';
        // echo  $request->order[0]['dir'];
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

    /**
     * Save a newly created/edited resource in storage.
     */
    public function save(Request $request)
    {

        $rules = [
            'wh_name'      => 'required',
            'wh_minprice'  => 'required',
            'image_file.*' => 'file|mimes:jpeg,png,jpg,svg|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        }

        if ($request->wh_id) {
            $message = 'warehouse succesvol bijgewerkt';
            /** @var WareHouse $warehouse */
            $warehouse = WareHouse::find($request->wh_id);
        } else {
            $message = 'warehouse succesvol toegevoegd';
            $warehouse = new WareHouse;
        }

        $warehouse->wh_name = $request->input('wh_name');
        $warehouse->wh_minprice = $request->input('wh_minprice');

        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $imagename = time().'_'.$image->getClientOriginalName();
            $img = Image::read($image->path());
            $img->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/warehouse/thumb').'/'.$imagename);
            $image->move(public_path('uploads/warehouse/'), $imagename);
            $warehouse->wh_logo = $imagename;
        }
        if ($request->input('old_cat_pic') == '' && ! $request->hasFile('image_file')) {
            $warehouse->wh_logo = '';
        }

        $warehouse->save();

        return response()
            ->json([
                'status' => true,
                'msg'    => $message,
                'page'   => 'admin/warehouse/list',
            ]);
    }

    public function destroy(Request $request)
    {
        $warehouse = Warehouse::find($request->id);
        $warehouse->delete();

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Warehouse deleted !',
            ]);
    }
}
