<?php

namespace App\Http\Controllers\FranchiseApp;

use App\Http\Controllers\Controller;

use App\Mail\DelieveryPersonLogin;
use App\Exports\HistoryExport;

use App\Models\Delivery;
use App\Models\SubDeliveryPerson;
use App\Models\DeliveryHistory;
use App\Models\DeliveryImage;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Laravel\Facades\Image;

class DeliverypersonController extends Controller
{
    public function getList(Request $request)
    {
        $id = $request->input('id');
        $language = $request->input('language');

        $query = DeliveryPerson::Select('deliveryperson_sub.*', 'deliveryperson.*')
            ->join('deliveryperson_sub', 'deliveryperson_sub.s_dpid', 'deliveryperson.dp_id')
            ->whereNull('deliveryperson_sub.deleted_at')
            ->where('s_fid', $id);
        $detail = $query->get()->toArray();
        if ($detail) {
            $message = ($language == 'nl') ?  'Succesvol inloggen'  : 'Data listed Successfully!!';
            return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
        } else {
            $message = ($language == 'nl') ?  'Email bestaat al'  : 'No records available';
            return response()->json(['status' => false, 'message' => $message]);
        }
    }
    public function create(Request $request)
    {
        $fid = $request->input('id');
        $language = $request->input('language');
        $dp_id = $request->input('dp_id');
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'contact_no' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postcode' => 'required',
            'pool' => 'required',

        ];
        if ($request->hasFile('image_file')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type' => 'validation',
                    'errors' => $validator->errors()
                ]);
        } else {
            if ($request->dp_id) {
                $dev = DeliveryPerson::where('dp_email', $request->input('email'))->whereNotIn('dp_id', [$request->dp_id])->get()->count();
                // dd($dev);
                if ($dev == 0) {
                    $subdev = SubDeliveryPerson::where('s_dpid', $request->dp_id)->where('s_fid', $fid)->first();

                    $message = ($language == 'nl') ? 'Delivery succesvol bijgewerkt' : 'Delivery Person added successfully';

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
                        $imagename = time() . '_' . $image->getClientOriginalName();

                        $img = Image::read($image->path());
                        $img->resize(100, 100, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('uploads/deliveryperson/thumb') . '/' . $imagename);
                        $destinationPath = public_path('uploads/product/');
                        $image->move(public_path('uploads/deliveryperson/'), $imagename);
                        $delivery->dp_image = $imagename;
                    }
                    $delivery->save();
                    if ($delivery) {
                        $subdelivery =  SubDeliveryPerson::where('s_dpid', $request->dp_id)->where('s_fid', $fid)->first();
                        $subdelivery->s_pool = implode(',', $request->input('pool'));
                        $subdelivery->save();
                    }

                    return response()->json(['status' => true, 'message' => $message]);
                } else {

                    return response()
                        ->json([
                            'status' => false,
                            'type' => 'validation',
                            'errors' => [
                                'email' => ['Email Already Exists']
                            ]
                        ]);
                }
            } else {
                $dev = DeliveryPerson::where('dp_email', $request->input('email'))->get();
                $count = $dev->count();
                $message = ($language == 'nl') ? 'Delivery succesvol bijgewerkt' : 'Delivery Person added successfully';
                if ($count == 0) {
                    $token = Str::random(6);
                    $delivery = new Delivery();
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
                        $imagename = time() . '_' . $image->getClientOriginalName();

                        $img = Image::read($image->path());
                        $img->resize(100, 100, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('uploads/deliveryperson/thumb') . '/' . $imagename);
                        $destinationPath = public_path('uploads/product/');
                        $image->move(public_path('uploads/deliveryperson/'), $imagename);
                        $delivery->dp_image = $imagename;
                    }
                    $delivery->save();
                    if ($delivery) {
                        $subdelivery = new SubDeliveryPerson();

                        $subdelivery->s_dpid = $delivery->dp_id;
                        $subdelivery->s_fid = $fid;
                        $subdelivery->s_pool = implode(',', $request->input('pool'));
                        $subdelivery->save();
                    }
                    if ($delivery) {
                        if ($request->dp_id == "") {

                            $maildata = [];
                            $maildata['name'] = $delivery['dp_name'];
                            $maildata['email'] = $delivery['dp_email'];
                            $maildata['password'] = $token;
                            Mail::to($delivery['dp_email'])->send(new DelieveryPersonLogin($maildata));
                        }
                    }

                    return response()->json(['status' => true, 'message' => $message]);
                } else {

                    $subdev = SubDeliveryPerson::where('s_fid', $fid)->where('s_dpid', $dev[0]['dp_id'])->get()->count();

                    $delivery = DeliveryPerson::find($dev[0]['dp_id']);
                    $delivery->dp_name = $request->input('name');
                    $delivery->dp_email = $request->input('email');
                    $delivery->dp_contact_no = $request->input('contact_no');
                    $delivery->dp_street = $request->input('street');
                    $delivery->dp_city = $request->input('city');
                    $delivery->dp_state = $request->input('state');
                    $delivery->dp_postcode = $request->input('postcode');

                    if ($request->hasFile('image_file')) {
                        $image = $request->file('image_file');
                        $imagename = time() . '_' . $image->getClientOriginalName();

                        $img = Image::read($image->path());
                        $img->resize(100, 100, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('uploads/deliveryperson/thumb') . '/' . $imagename);
                        $destinationPath = public_path('uploads/product/');
                        $image->move(public_path('uploads/deliveryperson/'), $imagename);
                        $delivery->dp_image = $imagename;
                    }
                    $delivery->save();
                    if ($subdev == 0) {
                        $subdelivery = new SubDeliveryPerson();
                        $subdelivery->s_dpid = $dev[0]['dp_id'];
                        $subdelivery->s_fid =  $fid;
                        $subdelivery->s_pool = implode(',', $request->input('pool'));
                        $subdelivery->save();

                        return response()->json(['status' => true, 'message' => $message]);
                    } else {
                        return response()
                            ->json([
                                'status' => false,
                                'type' => 'validation',
                                'errors' => [
                                    'email' => ['You have already added this delivery person']
                                ]
                            ]);
                    }
                }
            }
        }
    }
    public function updateonoff(Request $request)
    {
        $dp_id = $request->input('dp_id');
        $value = $request->input('value');

        if (isset($dp_id)) {
            DeliveryPerson::where('dp_id', $request->dp_id)->update(['dp_onoff' => $request->value]);
            return response()
                ->json([
                    'status' => true,
                    'message' => 'Status updated successfully!',
                ]);
        } else {
            return response()
                ->json([
                    'status' => false,
                    'message' => 'Something is wrong!',
                ]);
        }
    }
    public function viewDeliveryperson(Request $request)
    {
        $dp_id = $request->input('dp_id');
        if (isset($dp_id)) {
            $query = DeliveryHistory::Select('deliveryperson.*', 'dp_history.*')->join('deliveryperson', 'dp_history.history_dpid', 'deliveryperson.dp_id')->where('dp_history.history_dpid', $dp_id);

            if ($request->input('date') && $request->input('date') != '') {
                $explode = explode('-', $request->input('date'));
                $startdate = str_replace('/', '-', $explode[0]);
                $enddate = str_replace('/', '-', $explode[1]);
                $startdate = date("Y-m-d", strtotime($startdate));
                $enddate = date("Y-m-d", strtotime($enddate));
                $query = $query->WhereBetween('history_date', [$startdate, $enddate]);
            }
            $detail = $query->get()->toArray();

            return response()
                ->json([
                    'status' => true,
                    'message' => 'Data listed',
                    'data' => $detail
                ]);
        } else {
            return response()
                ->json([
                    'status' => false,
                    'message' => 'Something is wrong!',
                ]);
        }
    }
    public function historyDetail(Request $request)
    {
        $history_id = $request->input('history_id');
        $data = [];
        if (isset($history_id)) {
            $data['date'] =  DeliveryHistory::where('history_id', $history_id)->first();
            $data['dp'] =  DeliveryPerson::where('dp_id', $data['date']['history_dpid'])->first();
            $data['start'] =  DeliveryImage::where('dp_im_historyid', $history_id)->where('dp_im_type', 'start')->get();
            $data['end'] =  DeliveryImage::where('dp_im_historyid', $history_id)->where('dp_im_type', 'end')->get();

            return response()->json(['status' => true, 'message' => 'Data listed', 'data' => $data]);
        } else {
            return response()
                ->json([
                    'status' => false,
                    'message' => 'Something is wrong!',
                ]);
        }
    }
    public function deleteDelivery(Request $request)
    {
        $rules = [
            'dp_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type' => 'validation',
                    'errors' => $validator->errors()
                ]);
        } else {
            DeliveryPerson::where('dp_id', $request->dp_id)->delete();
            SubDeliveryPerson::where('s_dpid', $request->dp_id)->delete();
            return response()
                ->json([
                    'status' => true,
                    'msg' => 'Delivery  deleted !',
                ]);
        }
    }
    public function historyHoursExport(Request $request)
    {
        $data = $request->all();
        Excel::Store(new HistoryExport($data), 'Drank247.xlsx', 'delivery_person_xls_path');
        return response()->json([
            'status' => true,
            'url' => asset('uploads/excel/Drank247.xlsx')
        ]);
    }
}
