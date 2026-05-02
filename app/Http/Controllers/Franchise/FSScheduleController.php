<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPerson;
use App\Models\Notification;
use App\Models\OrderStatus;
use App\Models\Pool;
use App\Models\Schedule;
use App\Models\ScheduleAbsense;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FSScheduleController extends Controller
{
    public function scheduleList()
    {
        $data['status_list'] = OrderStatus::whereIn('id', [2, 7, 9])->get();
        $data['delivery'] = DeliveryPerson::get();

        return view('franchise.schedule.list', $data);
    }

    public function getList(Request $request)
    {
        $fid = auth('franchise')->id();
        $query = Schedule::select('delivery_people.*', 'pools.*', 'schedules.*', 'order_statuses.os_name')
            ->join('pools', 'pools.id', 'schedules.pool_id')
            ->join('order_statuses', 'order_statuses.id', 'schedules.status')
            ->join('delivery_people', 'delivery_people.id', 'schedules.delivery_person_id')
            ->where('schedules.franchise_id', $fid)
            ->whereNull('pools.deleted_at')
            ->whereNull('delivery_people.deleted_at'); //$request->get('cat_id')

        if ($request->get('delivery_id') && $request->get('delivery_id') != '') {
            $query = $query->where('schedules.delivery_person_id', $request->get('delivery_id'));
        }
        if ($request->get('status') && $request->get('status') != '') {
            $query = $query->where('status', $request->get('status'));
        }
        if ($request->get('date') && $request->get('date') != '') {

            $explode = explode('-', $request->get('date'));
            $startdate = str_replace('/', '-', $explode[0]);
            $enddate = str_replace('/', '-', $explode[1]);
            $startdate = date('Y-m-d H:i:s', strtotime($startdate));
            $enddate = date('Y-m-d H:i:s', strtotime($enddate));
            $query = $query->where(function ($q) use ($startdate, $enddate) {
                $q = $q->where(function ($q2) use ($startdate, $enddate) {
                    $q2->whereBetween('start_date', [$startdate, $enddate]);
                });
                $q = $q->orWhere(function ($q3) use ($startdate, $enddate) {
                    $q3->whereBetween('end_date', [$startdate, $enddate]);
                });
            });
            // $query=$query->whereRaw('((start_date <= "'.$startdate.'" &&  end_date >= "'.$startdate.'") OR  (start_date <= "'.$enddate.'" &&  end_date >= "'.$enddate.'"))');

        }

        $column_order = ['dp_name', 'time', 'area', 'os_name']; //set column field database for datatable orderable
        $column_search = ['dp_name', 'time', 'area', 'os_name']; //set column field database for datatable searchable
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
            $query = $query->orderBy('schedules.id', 'DESC');
        }
        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function scheduleAdd()
    {
        $data['row'] = [];
        $data['pool'] = Pool::get();
        $data['delivery'] = DeliveryPerson::get();

        return view('franchise.schedule.create', $data);
    }

    public function scheduleEdit(Schedule $id)
    {

        $data['row'] = [];
        if ($id) {
            $data['row'] = $id;
        }
        $data['pool'] = Pool::get();
        $data['delivery'] = DeliveryPerson::get();

        return view('franchise.schedule.create', $data);
    }

    public function save(Request $request)
    {
        //  dd($startdate);
        //  dd($enddate);

        $fid = auth('franchise')->id();
        $rules = [
            'deliveryperson' => 'required',
            'date'           => 'required',
            'pool'           => 'required',
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

        if ($request->s_id) {
            $message = 'Schedule succesvol bijgewerkt';
            $schedule = Schedule::find($request->s_id);
        } else {
            $message = 'Schedule succesvol toegevoegd';
            $schedule = new Schedule;
        }
        $explode = explode('-', $request->input('date'));
        $startdate = str_replace('/', '-', $explode[0]);
        $enddate = str_replace('/', '-', $explode[1]);
        $startdate = date('Y-m-d H:i', strtotime($startdate));
        $enddate = date('Y-m-d H:i', strtotime($enddate));

        $schedule->franchise_id = $fid;
        $schedule->delivery_person_id = $request->input('deliveryperson');
        $schedule->time = $request->input('date');
        $schedule->start_date = $startdate;
        $schedule->end_date = $enddate;
        $schedule->pool_id = $request->input('pool');
        $dev_id = DeliveryPerson::query()->findOrFail($request->input('deliveryperson'));
        $schedule->save();

        /** notification*/
        if ($schedule) {
            // $message='test';
            $notification = new Notification;
            $notification->user_type = 'delivery_person';
            $notification->to_id = $dev_id->id;
            $notification->text = $message;
            $notification->save();

            /** push notification */
            $push = new PushNotification('fcm');
            $mess = auth('franchise')->user()->franchises_name.' has sent you request for assignment';
            $notif = $push->setMessage([
                'data' => [
                    'notification' => [
                        'title'        => 'Order Status',
                        'message'      => $mess,
                        'sound'        => 'default',
                        'order_id'     => 0,
                        'schedule_id'  => $schedule['id'],
                        'message_type' => 'schedule',

                        // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
            ])
                ->setUrl(env('PUSH_NOTIFICATION_URL'))
                ->setApiKey(env('PUSH_NOTIFICATION_APIKEY'))
                ->setDevicesToken('fsb0lhS6S52w7dIGopfjHZ:APA91bGznLK43RokRvlDMx5rd_uYJzB5isFm47ORdlHX-pCAyDGTUyxBA1fm66vCPrqwbWiWC5lkmMFd12J2zO6bJkXvJDzVEnc-5kfYGpURL7kh1RTUd7OvdlpwwybMQJJqLacvezcd')
                ->send()
                ->getFeedback();

            // logger('log notif', ['$notif' => $notif]);
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => $message,
                'page'   => 'franchise/schedule/list',
            ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'value' => Rule::in([2, 7, 9])
        ]);
        $value = $request->value;
        $s_id = $request->pk;
        $schedule = Schedule::query()->findOrFail($s_id);
        $ordercolor = OrderStatus::query()->findOrFail($value);
        $schedule->update(['status' => $value]);

        if ($value == '7') {

            // $message='test';
            $notification = new Notification;
            $notification->user_type = 'delivery_person';
            $notification->to_id = $schedule['delivery_person_id'];
            $notification->text = 'Your Schedule Rejected';
            $notification->save();

            /** push notification */
            $push = new PushNotification('fcm');
            $notif = $push->setMessage([
                'data' => [
                    'notification' => [
                        'title'        => 'Order Status',
                        'body'         => 'test',
                        'sound'        => 'default',
                        'order_id'     => 0,
                        'schedule_id'  => $schedule['id'],
                        'message_type' => 'schedule',
                        // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
            ])
                ->setUrl(env('PUSH_NOTIFICATION_URL'))
                ->setApiKey(env('PUSH_NOTIFICATION_APIKEY'))
                ->setDevicesToken('cn2u_n4URsq3GAKAav-04k:APA91bHtWRYs0tMKesyPSYqkE2lwRuHOyRqHp2BhHKkPFHXTSrGWhR8on9D88yDtAm1rtNkbCM7fifQoYAim4qWlNG4vCox62EbCP5ulauIxR7PmB5qTDPrJnCHY-sLP7sVeT6QQTaO3')
                ->send()
                ->getFeedback();

            // logger('log notif', ['$notif' => $notif]);
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Status Changed Successfully!',
                'id'     => $s_id,
                'color'  => $ordercolor['os_color'],
            ]);
    }

    public function deleteSchedule(Request $request)
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
            $schedule = Schedule::find($request->id);
            Schedule::where('id', $request->id)->delete();

            // $message='test';
            $notification = new Notification;
            $notification->user_type = 'delivery_person';
            $notification->to_id = $schedule['delivery_person_id'];
            $notification->text = 'Your Schedule Deleted';
            $notification->save();

            /** push notification */
            $push = new PushNotification('fcm');
            $notif = $push->setMessage([
                'data' => [
                    'notification' => [
                        'title'        => 'Order Status',
                        'body'         => 'test',
                        'sound'        => 'default',
                        'order_id'     => 0,
                        'schedule_id'  => $schedule['id'],
                        'message_type' => 'schedule',
                    ],
                ],
            ])
                ->setUrl(env('PUSH_NOTIFICATION_URL'))
                ->setApiKey(env('PUSH_NOTIFICATION_APIKEY'))
                ->setDevicesToken('cn2u_n4URsq3GAKAav-04k:APA91bHtWRYs0tMKesyPSYqkE2lwRuHOyRqHp2BhHKkPFHXTSrGWhR8on9D88yDtAm1rtNkbCM7fifQoYAim4qWlNG4vCox62EbCP5ulauIxR7PmB5qTDPrJnCHY-sLP7sVeT6QQTaO3')
                ->send()
                ->getFeedback();
            // logger('log notif', ['$notif' => $notif]);

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Schedule  deleted !',
                ]);
        }
    }

    public function getDeliveryPersonList(Request $request)
    {

        $explode = explode('-', $request->input('date'));
        $startdate = str_replace('/', '-', $explode[0]);
        $enddate = str_replace('/', '-', $explode[1]);

        $stdate = date('Y-m-d', strtotime($startdate));
        $endate = date('Y-m-d', strtotime($enddate));
        $starttime = date('H:i:s', strtotime($startdate));
        $endtime = date('H:i:s', strtotime($enddate));

        $leave = ScheduleAbsense::where('delivery_person_id', $request->input('deliveryperson'))
            ->whereRaw('(DATE(sa_start_time) <= "'.$stdate.'" &&  DATE(sa_end_time) >= "'.$stdate.'") OR  (DATE(sa_start_time) <= "'.$endate.'" &&  DATE(sa_end_time) >= "'.$endate.'")');
        $leave = $leave->pluck('delivery_person_id')->all();
        $leaveid = implode(',', $leave);
        // dd($leaveid);
        $sche = Schedule::leftJoin('franchises', function ($join) {
            $join->on('franchises.id', '=', 'schedules.franchise_id');
        })->whereRaw('("'.$stdate.'" >= DATE(start_date) AND "'.$stdate.'" <= DATE(end_date)) && ( CAST(start_date AS TIME) <= "'.$starttime.'" AND   CAST(end_date AS TIME) >= "'.$starttime.'")')
            ->groupBy('delivery_person_id')
            ->where('status', 2)
            ->pluck('delivery_person_id')->toArray();
        $scheid = implode(',', $sche);
        //  dd($scheid);

        $delivery = DeliveryPerson::whereNull('deleted_at');
        if (count($leave) > 0) {
            $delivery = $delivery->whereNotIn('id', [$leaveid]);
        }
        if (count($sche) > 0) {
            $delivery = $delivery->whereNotIn('id', [$scheid]);
        }
        $delivery = $delivery->groupBy('id')->get();
        $html = '';
        $html .= '<label for="first_name">*Delivery Person</label>
     <select class="form-control select2" id="deliveryperson" name="deliveryperson">
      <option value="" >Select Delivery person</option>';
        foreach ($delivery as $value) {
            $html .= '<option value="'.$value['id'].'">'.$value->dp_name.'</option> ';
        }
        $html .= '
      </select>
      <span id="deliveryperson_error"></span>';

        return response()
            ->json([
                'status' => true,
                'html'   => $html,
            ]);

        //  dd($deliveryid);

    }
}
