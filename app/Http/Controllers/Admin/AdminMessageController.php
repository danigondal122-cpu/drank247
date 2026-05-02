<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMessage;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryPerson;
use App\Models\Franchise;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\Notification;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class AdminMessageController extends Controller
{
    public function create()
    {
        $data['row'] = [];
        $data['deliverylist'] = DeliveryPerson::whereNull('deleted_at')->get();
        // $data['customerlist'] = Customer::whereNull('deleted_at')->groupBy('customer_email')->get();
        $data['customerlist'] = Customer::select('customer_email', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('customer_email')
            ->get();
        $data['franchiselist'] = Franchise::whereNull('deleted_at')->get();

        return view('admin.message.create', $data);
    }

    public function edit($id)
    {
        $data['row'] = [];
        $data['deliverylist'] = DeliveryPerson::whereNull('deleted_at')->get();
        // $data['customerlist'] = Customer::whereNull('deleted_at')->groupBy('customer_email')->get();
        $data['customerlist'] = Customer::select('customer_email', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('customer_email')
            ->get();
        $data['franchiselist'] = Franchise::whereNull('deleted_at')->get();
        if ($id) {
            $data['row'] = Message::findOrFail($id);
            $data['user'] = MessageUser::where('message_id', $id)->pluck('m_user_id')->toArray();
        }

        return view('admin.message.create', $data);
    }

    public function index()
    {
        $data['deliverylist'] = DeliveryPerson::whereNull('deleted_at')->get();
        $data['customerlist'] = Customer::whereNull('deleted_at')->get();
        $data['franchiselist'] = Franchise::whereNull('deleted_at')->get();

        return view('admin.message.list', $data);
    }

    public function show($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = Message::findOrFail($id);
            $data['user'] = MessageUser::where('message_id', $id)->pluck('m_user_id')->toArray();

            if ($data['row']['message_to'] == 'franchise') {
                foreach ($data['user'] as $value) {
                    $data['userdata'][$value] = Franchise::select('id', 'franchises_name as name', 'franchises_email as email')->where('id', $value)->first();
                }
            }
            if ($data['row']['message_to'] == 'deliveryperson') {
                foreach ($data['user'] as $value) {
                    $data['userdata'][$value] = DeliveryPerson::select('id', 'dp_name as name', 'dp_email as email')->where('id', $value)->first();
                }
            }
            if ($data['row']['message_to'] == 'customer') {
                foreach ($data['user'] as $value) {
                    $data['userdata'][$value] = Customer::select('id', 'customer_name as name', 'customer_email as email')->where('id', $value)->first();
                }
            }
        }

        return view('admin.message.view', $data);
    }

    public function getList(Request $request)
    {
        $query = Message::whereNull('deleted_at'); //$request->get('cat_id')

        $column_order = ['message_to', 'message_text']; //set column field database for datatable orderable
        $column_search = ['message_to', 'message_text']; //set column field database for datatable searchable
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
            $query = $query->orderBy('messages.id', 'DESC');
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
            'message_to'   => 'required',
            'message_text' => 'required',
            'message_user' => 'required',
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
        } else {
            if ($request->id) {
                $message = 'Message succesvol bijgewerkt';
                $broadcast_message = Message::find($request->id);
            } else {
                $message = 'Message succesvol toegevoegd';
                $broadcast_message = new Message;
            }
            $broadcast_message->message_text = $request->input('message_text');
            $broadcast_message->message_to = $request->input('message_to');

            if ($request->hasFile('image_file')) {
                $image = $request->file('image_file');
                $imagename = time().'_'.$image->getClientOriginalName();
                $imagename = preg_replace("/\s+/", '', $imagename);
                $img = Image::read($image->path());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads/broadcastmessage/thumb').'/'.$imagename);
                $image->move(public_path('uploads/broadcastmessage/'), $imagename);
                $broadcast_message->image = $imagename;
            }

            $broadcast_message->save();
            if ($broadcast_message) {
                foreach ($request->input('message_user') as $value) {
                    $user = new MessageUser;
                    $user->message_id = $broadcast_message->id;
                    $user->m_user = $request->input('message_to');
                    $user->m_user_id = $value;
                    $user->save();
                }

                if ($request->input('message_to') == 'deliveryperson') {
                    /** notification To delivery person*/
                    foreach ($request->input('message_user') as $value) {
                        $notification = new Notification;
                        $notification->user_type = 'delivery_person';
                        $notification->to_id = $value;
                        $notification->text = $request->input('message_text');
                        $notification->save();

                        /** push notification */
                        $devpersondetail = DeliveryPerson::find($value);

                        $push = new PushNotification('fcm');
                        $push->setMessage([
                            'data' => [
                                'notification' => [
                                    'title'        => 'Broadcast Message',
                                    'message'      => $request->input('message_text'),
                                    'sound'        => 'default',
                                    'order_id'     => $broadcast_message->id,
                                    'schedule_id'  => 0,
                                    'message_type' => 'broad_cast',
                                    // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                ],
                            ],
                        ])
                            ->setUrl(env('PUSH_NOTIFICATION_URL'))
                            ->setApiKey(env('PUSH_NOTIFICATION_APIKEY'))
                            ->setDevicesToken($devpersondetail['dp_devicetoken'])
                            ->send()
                            ->getFeedback();
                    }
                }
                if ($request->input('message_to') == 'customer') {
                    /** Send Email */
                    foreach ($request->input('message_user') as $value) {
                        $customerdetail = Customer::find($value);
                        $maildata = [];
                        $maildata['name'] = $customerdetail['customer_name'];
                        $maildata['mess'] = $request->input('message_text');
                        $maildata['image'] = $broadcast_message->image;
                        if ($customerdetail['customer_email'] != '') {
                            Mail::to($customerdetail['customer_email'])
                                ->send(new BroadcastMessage($maildata));
                        }
                    }
                }
                if ($request->input('message_to') == 'franchise') {
                    /** notification To Franchise*/
                    foreach ($request->input('message_user') as $value) {
                        $notification = new Notification;
                        $notification->user_type = 'franchise';
                        $notification->to_id = $value;
                        $notification->text = $request->input('message_text');
                        $notification->save();
                    }
                }
            }

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'admin/message/list',
                ]);
        }
    }

    public function deleteMessage(Request $request)
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
            Message::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Message deleted !',
                ]);
        }
    }
}
