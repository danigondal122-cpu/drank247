<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\DeliveryImage;
use App\Models\DeliveryPerson;
use App\Models\Franchise;
use App\Models\Help;
use App\Models\Order;
use App\Models\RateandReview;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class ApiDeliveryController extends Controller
{
    public function startDelivery(Request $request)
    {
        $dp_id = $request->input('id');
        $token = $request->input('token');
        $starttime = $request->input('starttime');
        $date = $request->input('date');
        $start_odometer_number = $request->input('start_odometer_number');
        $image = $request->file('image');

        $history = new DeliveryHistory;
        $history->delivery_person_id = $dp_id;
        $history->history_start_time = $starttime;
        $history->history_date = $date;
        $history->start_odometer = $start_odometer_number;
        $history->history_end_time = '';
        $history->end_odometer = '';
        $history->save();
        if ($history) {
            if ($request->hasFile('image')) {
                $i = 0;
                $files = $request->file('image');
                foreach ($files as $file) {
                    $dimage = $file;
                    $documentimage = 'dp'.$i.time().'.'.$dimage->extension();
                    $destinationPath = public_path('uploads/deliveryhistory/start/thumb');
                    $img = Image::read($dimage->path());
                    $img->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$documentimage);
                    $destinationPath = public_path('uploads/deliveryhistory/start');
                    $dimage->move($destinationPath, $documentimage);
                    $image = new DeliveryImage;
                    $image->delivery_history_id = $history->id;
                    $image->dp_im_type = 'start';
                    $image->dp_im_name = $documentimage;
                    $image->save();
                    $i++;
                }
            }
            DeliveryPerson::where('id', $dp_id)->update(['dp_onoff' => 'online', 'dp_start_odometer_number' => $start_odometer_number, 'history_id' => $history->id]);
        }

        return response()->json(['status' => true, 'message' => 'Success', 'history_id' => $history->id]);
    }

    public function endDelivery(Request $request)
    {
        $dp_id = $request->input('id');
        $token = $request->input('token');
        $date = $request->input('date');
        $endtime = $request->input('endtime');
        $stop_odometer_number = $request->input('stop_odometer_number');
        $image = $request->file('image');
        $history_id = $request->input('history_id');

        /** @var DeliveryHistory $history */
        $history = DeliveryHistory::where('history_id', $history_id)->firstOrFail();
        $history->history_end_time = $endtime;
        $history->end_odometer = $stop_odometer_number;
        $history->save();
        if ($history) {
            if ($request->hasFile('image')) {
                $i = 0;
                $files = $request->file('image');
                foreach ($files as $file) {
                    $dimage = $file;
                    $documentimage = 'dp'.$i.time().'.'.$dimage->extension();
                    $destinationPath = public_path('uploads/deliveryhistory/end/thumb');
                    $img = Image::read($dimage->path());
                    $img->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$documentimage);
                    $destinationPath = public_path('uploads/deliveryhistory/end');
                    $dimage->move($destinationPath, $documentimage);
                    $image = new DeliveryImage;
                    $image->delivery_history_id = $history->id;
                    $image->dp_im_type = 'end';
                    $image->dp_im_name = $documentimage;
                    $image->save();
                    $i++;
                }
            }
            DeliveryPerson::where('id', $dp_id)->update(['dp_onoff' => 'offline', 'dp_stopodometer_number' => $stop_odometer_number]);
        }

        return response()->json(['status' => true, 'message' => 'Success', 'history_id' => $history->id]);
    }

    public function updatelatlng(Request $request)
    {
        $dp_id = $request->input('id');
        $token = $request->input('token');
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        DeliveryPerson::where('id', $dp_id)->update(['dp_lat' => $lat, 'dp_lng' => $lng]);

        return response()->json(['status' => true, 'message' => 'Success']);
    }

    public function workingHours(Request $request)
    {
        $dp_id = $request->input('id');
        $token = $request->input('token');

        $data = DeliveryHistory::select('dp_history.*')
            ->join('delivery_people', 'dp_history.delivery_person_id', 'delivery_people.id')
            ->where('dp_history.delivery_person_id', $dp_id)
            ->where('history_end_time', '!=', '0000-00-00 00:00:00')
            ->orderBy('history_date', 'DESC')->get();

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $data]);
    }

    public function rateAndReview(Request $request)
    {
        $dp_id = $request->input('id');
        $token = $request->input('token');

        $data = RateandReview::join('delivery_people', 'delivery_people.id', 'rate_and_reviews.delivery_person_id')
            ->join('customers', 'customers.id', 'rate_and_reviews.customer_id')
            ->where('rate_and_reviews.delivery_person_id', $dp_id)->get(['customer_name', 'dp_name', 'order_id', 'rate', 'review']);

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $data]);
    }

    public function saveHelpdata(Request $request)
    {
        $dp_id = $request->input('id');
        $token = $request->input('token');
        $order_id = $request->input('order_id');
        $type = $request->input('type');
        $message = $request->input('message');
        $f_ids = Order::where('order_id', $order_id)->get(['franchise_id']);

        $to_id = $type == 0 ? '1' : $f_ids['franchise_id'];

        // TODO!: run this instead?
        // $order = Order::query()->findOrFail($order_id);
        // $to_id = $type == 0 ? $request->input('cs_id') : $order->franchise_id;

        $help = new Help;
        $help->type = $type;
        $help->to_id = $to_id;
        $help->order_id = $order_id;
        $help->delivery_person_id = $dp_id;
        $help->message = $message;

        $help->save();

        return response()->json(['status' => true, 'message' => 'Success']);
    }

    public function helpList(Request $request)
    {
        $dp_id = $request->input('id');
        $token = $request->input('token');
        $order_id = $request->input('order_id');

        $help = Help::join('order_status', 'order_status.os_id', 'help.status')->where('order_id', $order_id)->get(['type', 'to_id', 'message', 'os_name']);
        foreach ($help as $key => $value) {
            if ($value['type'] == '0') {
                $help[$key]['to_name'] = 'Customer Service';
            } else {
                $franchise_name = Franchise::findOrFail($value['to_id']);
                $help[$key]['to_name'] = $franchise_name['franchises_name'];
            }
        }

        return response()->json(['status' => true, 'message' => 'Success', 'help' => $help]);
    }
}
