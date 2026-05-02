<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminPromoCodeController extends Controller
{
    public function create()
    {
        $data['row'] = [];

        return view('admin.promocode.create', $data);
    }

    public function edit($id)
    {

        $data['row'] = [];
        if ($id) {
            $data['row'] = PromoCode::findOrFail($id);
        }

        return view('admin.promocode.create', $data);
    }

    public function index()
    {
        return view('admin.promocode.list');
    }

    public function getList(Request $request)
    {
        $query = PromoCode::whereNull('deleted_at');
        $column_order = ['code_text', 'discount', 'max_users', 'max_per_user']; //set column field database for datatable orderable
        $column_search = ['code_text', 'discount', 'max_users', 'max_per_user', 'start_date', 'end_date']; //set column field database for datatable searchable
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
            'code_text'   => 'required|unique:promo_codes,code_text,'.$request->id.',id',
            'maxperusers' => 'required',
            'discount'    => 'required',
            'start_date'  => 'required',
            'maxusers'    => 'required_if:limitation_type,1',
        ];

        if ($request->expiration_type == 1) {
            $rules['end_date'] = 'required_if:expiration_type,1|after:start_date';
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
                $message = 'Promo code succesvol bijgewerkt';
                $PromoCode = PromoCode::find($request->id);
            } else {
                $message = 'Promo code succesvol toegevoegd';
                $PromoCode = new PromoCode;
            }

            $PromoCode->code_text = $request->input('code_text');
            $PromoCode->discount_type = $request->input('discount_type');
            $PromoCode->discount = $request->input('discount');
            $PromoCode->limitation_type = $request->input('limitation_type');
            $PromoCode->max_users = $request->input('maxusers');
            $PromoCode->max_per_user = $request->input('maxperusers');
            $PromoCode->expiration_type = $request->input('expiration_type');
            $PromoCode->start_date = date('Y-m-d', strtotime($request->input('start_date')));
            $PromoCode->end_date = $request->input('expiration_type') == 0 ? date('Y-m-d', strtotime($request->input('start_date'))) : date('Y-m-d', strtotime($request->input('end_date')));
            $PromoCode->save();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'admin/promocode/list',
                ]);
        }
    }

    public function deletePromocode(Request $request)
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
            PromoCode::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Promo Code deleted !',
                ]);
        }
    }

    public function ActivateCode(Request $request)
    {

        $code = PromoCode::find($request->id);
        $code->code_status = $request->code_status;
        $code->save();

        return response()
            ->json([
                'status'  => true,
                'message' => 'Dealer Enabled/Disabled !',
            ]);
    }

    public function viewPromoCodeOrder(Request $request)
    {
        $id = $request->id;

        $data['list'] = Order::select(DB::raw('*,orders.created_at as orderdate'))
            ->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })->where('promo_code_id', $id)->get();

        $data['count'] = $data['list']->count();

        return view('modal.promocodelist', $data);
    }
}
