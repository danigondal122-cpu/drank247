<?php

namespace App\Http\Controllers\CustomerService;

use App\Models\DeliveryPerson;
use App\Models\Franchise;
use App\Models\Pool;
use App\Models\SubDeliveryPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class CSDeliveryPersonController extends CSNotificationController
{
    public function deliveryList()
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();

        return view('customerservice.deliveryperson.list', $data);
    }

    public function getList(Request $request)
    {
        $query = DeliveryPerson::select(
            'delivery_people.id',
            'delivery_people.dp_name',
            'delivery_people.dp_email',
            'delivery_people.dp_contact_no',
            'delivery_people.dp_city',
            DB::raw('COUNT(sub_delivery_people.id) as sub_delivery_count')
        )
            ->leftJoin('sub_delivery_people', function ($join) {
                $join->on('sub_delivery_people.delivery_person_id', '=', 'delivery_people.id')
                    ->whereNull('sub_delivery_people.deleted_at');
            })
            ->leftJoin('franchises', 'franchises.id', '=', 'sub_delivery_people.franchise_id')
            ->whereNull('delivery_people.deleted_at');

        // Filter berdasarkan franchise ID
        if ($request->filled('frs_id')) {
            $query->where('sub_delivery_people.franchise_id', $request->get('frs_id'));
        }

        $column_order = ['dp_name', 'dp_email', 'dp_contact_no', 'dp_city']; // Kolom untuk sorting
        $column_search = ['dp_name', 'dp_email', 'dp_contact_no', 'dp_city']; // Kolom untuk searching
        $start_from = $request->start;
        $per_page = $request->length;

        // Search
        if (! empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($column_search, $search) {
                foreach ($column_search as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }

        // Sorting
        if (! empty($request->order[0]['column'])) {
            $column_index = $request->order[0]['column'];
            $sort_direction = $request->order[0]['dir'];
            $query->orderBy($column_order[$column_index], $sort_direction);
        } else {
            $query->orderBy('delivery_people.id', 'DESC'); // Default sorting
        }

        // Grouping
        $query->groupBy('delivery_people.id', 'delivery_people.dp_name', 'delivery_people.dp_email', 'delivery_people.dp_contact_no', 'delivery_people.dp_city');

        $total = $query->count();
        $data = $query->skip($start_from)->take($per_page)->get();

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    /**
     * @deprecated unused
     */
    public function deliveryAdd()
    {
        $data['row'] = [];
        $data['franchise'] = Franchise::get();
        $data['pool'] = Pool::get();

        return view('customerservice.deliveryperson.create', $data);
    }

    public function deliveryEdit(DeliveryPerson $id)
    {
        $data['row'] = $id;

        // $poolarray = $data['row']['dp_pool'];

        // $data['poolarray'] = explode(',', $poolarray);
        $data['franchise'] = Franchise::get();
        $data['pool'] = Pool::get();

        return view('customerservice.deliveryperson.create', $data);
    }

    public function save(Request $request)
    {
        $rules = [
            // 'franchise'=> 'required',
            // 'pool'=> 'required',
            'name'       => 'required',
            'email'      => 'required|email|unique:delivery_people,dp_email',
            'contact_no' => 'required',
            'street'     => 'required',
            'city'       => 'required',
            'state'      => 'required',
            'postcode'   => 'required',
        ];
        if ($request->dp_id != '') {
            $rules['email'] = 'required|email|unique:delivery_people,dp_email,'.$request->dp_id.',id';
        }
        if ($request->hasFile('image_file')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
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
            if ($request->dp_id) {
                $message = 'Delivery succesvol bijgewerkt';
                $delivery = DeliveryPerson::query()->findOrFail($request->dp_id);

                // $delivery->dp_franchisesid = $request->input('franchise');
                // $delivery->dp_pool = implode(',',$request->input('pool'));
                $delivery->dp_name = $request->input('name');
                $delivery->dp_email = $request->input('email');
                $delivery->dp_contact_no = $request->input('contact_no');
                $delivery->dp_street = $request->input('street');
                $delivery->dp_city = $request->input('city');
                $delivery->dp_state = $request->input('state');
                $delivery->dp_postcode = $request->input('postcode');
                if ($request->hasFile('image_file')) {

                    $image = $request->file('image_file');
                    $imagename = time().'_'.$image->getClientOriginalName();

                    $img = Image::read($image->path());
                    $img->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(public_path('uploads/deliveryperson/thumb').'/'.$imagename);
                    $destinationPath = public_path('uploads/deliveryperson/');
                    $image->move(public_path('uploads/deliveryperson/'), $imagename);
                    $delivery->dp_image = $imagename;
                }
                $delivery->save();

                return response()
                    ->json([
                        'status' => true,
                        'msg'    => $message,
                        'page'   => 'customer_service/deliveryperson/list',
                    ]);
            }
        }
    }

    public function deleteDelivery(Request $request)
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
            DeliveryPerson::where('id', $request->id)->delete();
            SubDeliveryPerson::where('delivery_person_id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Delivery  deleted !',
                ]);
        }
    }

    public function getFranchises(Request $request)
    {

        $id = $request->id;
        $delivery = DeliveryPerson::query()->findOrFail($id);
        $data['list'] = SubDeliveryPerson::query()
            ->whereBelongsTo($delivery)
            ->with([
                'franchise:id,franchises_name',
                'pools:id,area',
            ])
            ->get(['id', 'delivery_person_id', 'franchise_id'])
            ->each(function (SubDeliveryPerson $subDeliveryPerson) {
                $subDeliveryPerson->poolareas = $subDeliveryPerson->pools->implode(function (Pool $pool) {
                    return $pool->area;
                }, ',');
            });

        return view('modal.deliveryfranchiseslist', $data);
    }
}
