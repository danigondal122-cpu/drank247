<?php

namespace App\Http\Controllers\CustomerService;

use App\Models\DeliveryPerson;
use App\Models\Help;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CSHelpController extends CSNotificationController
{
    public function helpList()
    {
        $data['deliverylist'] = DeliveryPerson::whereNull('deleted_at')->get();
        $data['status_list'] = OrderStatus::whereIn('id', [14, 13, 9])->get();

        return view('customerservice.help.list', $data);
    }

    public function getList(Request $request)
    {
        $query = Help::select([
            'helps.*',
            'delivery_people.dp_name',
            'order_statuses.os_name',
            'order_statuses.os_color',
        ])
            ->join('delivery_people', 'delivery_people.id', 'helps.delivery_person_id')
            ->join('order_statuses', 'order_statuses.id', 'helps.order_status_id');
        if ($request->get('id') && $request->get('id') != '') {
            $query = $query->where('helps.delivery_person_id', $request->get('id'));
        }
        if (isset($request->_type) && $request->_type != '' && isset($request->_id) && $request->_id != '') {
            if ($request->_type == '0') {
                $query = $query->where('type', '0')->where('to_id', $request->_id);
            } else {
                $query = $query->where('type', '1')->where('to_id', $request->_id);
            }
        }
        $column_order = ['dp_name', '_to_help', 'message']; //set column field database for datatable orderable
        $column_search = ['dp_name', 'message']; //set column field database for datatable searchable
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
            $query = $query->orderBy('helps.id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();
        foreach ($data as $key => $value) {
            // if ($value['type'] == '0') {
            //     $name ??= rescue(fn () => CustomerService::find($value['to_id'])->cs_name, fn () => true);
            //     $data[$key]['_to_help'] = $name;
            // } else {
            //     $name ??= Franchise::find($value['to_id'])->franchises_name;
            //     $data[$key]['_to_help'] = $name;
            // }
        }

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function deleteHelp(Request $request)
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
        }

        Help::where('id', $request->id)->delete();

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Help deleted !',
            ]);
    }

    public function updateStatus(Request $request)
    {
        $value = $request->value;
        $id = $request->pk;

        Help::where('id', $id)->update(['order_status_id' => $value]);
        $ordercolor = OrderStatus::find($value)?->os_color;

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Status Changed Successfully!',
                'id'     => $id,
                'color'  => $ordercolor,
            ]);
    }
}
