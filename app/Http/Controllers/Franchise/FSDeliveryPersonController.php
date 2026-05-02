<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Mail\DelieveryPersonLogin;
use App\Models\Delivery;
use App\Models\DeliveryHistory;
use App\Models\DeliveryImage;
use App\Models\DeliveryPerson;
use App\Models\Pool;
use App\Models\SubDeliveryPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class FSDeliveryPersonController extends Controller
{
    public function deliveryList()
    {
        return view('franchise.deliveryperson.list');
    }

    public function getList(Request $request)
    {
        $fid = auth('franchise')->id();

        $query = DeliveryPerson::select('sub_delivery_people.*', 'delivery_people.*')
            ->join('sub_delivery_people', 'sub_delivery_people.delivery_person_id', 'delivery_people.id')
            ->whereNull('sub_delivery_people.deleted_at')
            ->where('franchise_id', $fid);

        // $query = DeliveryPerson::where('dp_franchisesid',$fid)->whereNull('deleted_at');
        $column_order = ['dp_name', 'dp_email', 'dp_contact_no', 'dp_city']; //set column field database for datatable orderable
        $column_search = ['dp_name', 'dp_email', 'dp_contact_no', 'dp_city']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        $rawQuery = '';

        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('delivery_people.id', 'desc');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function deliveryAdd()
    {
        $data['row'] = [];
        $data['pool'] = Pool::get();
        $data['delivery'] = DeliveryPerson::get();

        return view('franchise.deliveryperson.create', $data);
    }

    public function deliveryEdit(DeliveryPerson $id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = $id;
        }

        $data['poolarray'] = SubDeliveryPerson::query()
            ->where('delivery_person_id', $id->id)
            ->where('franchise_id', auth('franchise')->id())
            ->first()
            ?->pools()
            ?->pluck('id')
            ?->all();
        $data['pool'] = Pool::get();

        return view('franchise.deliveryperson.create', $data);
    }

    public function save(Request $request)
    {

        $fid = auth('franchise')->id();
        $rules = [
            'name' => 'required',
            // 'email' => 'required|email|unique:delivery_people,dp_email',
            'email'      => 'required',
            'contact_no' => 'required',
            'street'     => 'required',
            'city'       => 'required',
            'state'      => 'required',
            'postcode'   => 'required',
            'pool'       => 'required',

        ];

        if ($request->hasFile('image_file')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $poolIds = $request->input('pool');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        }

        if ($request->dp_id) {

            $dev = DeliveryPerson::where('dp_email', $request->input('email'))->whereNotIn('id', [$request->dp_id])->get()->count();
            // dd($dev);
            if ($dev == 0) {
                $subdev = SubDeliveryPerson::where('delivery_person_id', $request->dp_id)->where('franchise_id', $fid)->first();

                $message = 'Delivery succesvol bijgewerkt';

                $delivery = DeliveryPerson::find($request->dp_id);
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

                    $image->move(public_path('uploads/deliveryperson/'), $imagename);
                    $delivery->dp_image = $imagename;
                }
                $delivery->save();
                if ($delivery) {
                    $subdelivery = SubDeliveryPerson::where('delivery_person_id', $request->dp_id)->where('franchise_id', $fid)->first();
                    $subdelivery->save();

                    $subdelivery->pools()->sync($poolIds);
                }

                return response()
                    ->json([
                        'status' => true,
                        'msg'    => $message,
                        'page'   => 'franchise/deliveryperson/list',
                    ]);
            } else {

                return response()
                    ->json([
                        'status' => false,
                        'type'   => 'validation',
                        'errors' => [
                            'email' => ['Email Already Exists'],
                        ],

                    ]);
            }
        } else {
            $dev = DeliveryPerson::query()->where('dp_email', $request->input('email'))->get();

            $count = $dev->count();
            $message = 'Delivery succesvol toegevoegd';
            if ($count == 0) {
                $token = Str::random(6);
                $delivery = new DeliveryPerson;
                $delivery->dp_name = $request->input('name');
                $delivery->dp_email = $request->input('email');
                $delivery->dp_password = bcrypt($token);
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

                    $image->move(public_path('uploads/deliveryperson/'), $imagename);
                    $delivery->dp_image = $imagename;
                }
                $delivery->save();
                if ($delivery) {
                    $subdelivery = new SubDeliveryPerson;

                    $subdelivery->delivery_person_id = $delivery->id;
                    $subdelivery->franchise_id = $fid;
                    $subdelivery->save();

                    $subdelivery->pools()->sync($poolIds);
                }
                if ($delivery) {
                    if ($request->dp_id == '') {

                        $maildata = [];
                        $maildata['name'] = $delivery['dp_name'];
                        $maildata['email'] = $delivery['dp_email'];
                        $maildata['password'] = $token;
                        // TODO!: rename DelieveryPersonLogin -> DeliveryPersonLogin
                        Mail::to($delivery['dp_email'])
                            ->send(new DelieveryPersonLogin($maildata));
                    }
                }

                return response()
                    ->json([
                        'status' => true,
                        'msg'    => $message,
                        'page'   => 'franchise/deliveryperson/list',
                    ]);
            } else {
                $delivery = $dev->first();

                $subdev = SubDeliveryPerson::where('franchise_id', $fid)->where('delivery_person_id', $delivery->id)->get()->count();

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

                    $image->move(public_path('uploads/deliveryperson/'), $imagename);
                    $delivery->dp_image = $imagename;
                }
                $delivery->save();
                if ($subdev == 0) {
                    $subdelivery = new SubDeliveryPerson;
                    $subdelivery->delivery_person_id = $delivery->id;
                    $subdelivery->franchise_id = $fid;
                    $subdelivery->save();

                    $subdelivery->pools()->sync($poolIds);

                    return response()
                        ->json([
                            'status' => true,
                            'msg'    => $message,
                            'page'   => 'franchise/deliveryperson/list',
                        ]);
                } else {
                    return response()
                        ->json([
                            'status' => false,
                            'type'   => 'validation',
                            'errors' => [
                                'email' => ['You have already added this delivery person'],
                            ],

                        ]);
                }
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
            // DeliveryPerson::where('id',$request->id)->delete();
            $fid = auth('franchise')->id();
            SubDeliveryPerson::where('delivery_person_id', $request->id)->where('franchise_id', $fid)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Delivery  deleted !',
                ]);
        }
    }

    public function getDeliveryPersonList(Request $request)
    {

        $data = DeliveryPerson::whereNull('deleted_at')->groupBy('dp_email')->pluck('dp_email');

        echo json_encode($data);
    }

    public function getDeliveryPersonDetail(Request $request)
    {
        $data = DeliveryPerson::where('dp_email', $request->email)->whereNull('deleted_at')->first();
        // // /dd($data);
        // echo $data['dp_image'];
        $html = '';
        $html .= '
        <div class="card elevation-2 col-sm-6 p-1" id="img">
          <img src="'.$data['dp_image'].'" />
          <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
          <i class="fas fa-search-plus"></i>
          </button>
          <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
            <i class="fas fa-trash-alt"></i>
          </button>
        </div>';

        return response()
            ->json([
                'status'     => true,
                'name'       => $data['dp_name'],
                'contact_no' => $data['dp_contact_no'],
                'street'     => $data['dp_street'],
                'city'       => $data['dp_city'],
                'state'      => $data['dp_state'],
                'postcode'   => $data['dp_postcode'],
                'dp_image'   => $html,

            ]);
    }

    public function deliveryPersonView($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = DeliveryPerson::findOrFail($id);
        }

        return view('franchise.deliveryperson.view', $data);
    }

    public function getDateList(Request $request)
    {
        $dp_id = $request->input('dp_id');
        $fid = auth('franchise')->id();

        $query = DeliveryHistory::select('delivery_histories.*', 'delivery_histories.id as history_id', 'delivery_people.*')
            ->join('delivery_people', 'delivery_histories.delivery_person_id', 'delivery_people.id')
            ->where('delivery_histories.delivery_person_id', $dp_id);

        if ($request->get('date') && $request->get('date') != '') {

            $explode = explode('-', $request->get('date'));
            $startdate = str_replace('/', '-', $explode[0]);
            $enddate = str_replace('/', '-', $explode[1]);
            $startdate = date('Y-m-d', strtotime($startdate));
            $enddate = date('Y-m-d', strtotime($enddate));
            $query = $query->whereBetween('history_date', [$startdate, $enddate]);
        }
        // $query = DeliveryPerson::where('dp_franchisesid',$fid)->whereNull('deleted_at');
        $column_order = ['history_date']; //set column field database for datatable orderable
        $column_search = ['history_date']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        $rawQuery = '';
        //Search
        if ($request->input('search') != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('history_date', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function historyDetail($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['date'] = DeliveryHistory::query()->find($id);
            $data['dp'] = $data['date']?->deliveryPerson;
            $data['start'] = DeliveryImage::where('delivery_history_id', $id)->where('dp_im_type', 'start')->get();
            $data['end'] = DeliveryImage::where('delivery_history_id', $id)->where('dp_im_type', 'end')->get();
        }

        return view('franchise.deliveryperson.history-detail', $data);
    }
}
