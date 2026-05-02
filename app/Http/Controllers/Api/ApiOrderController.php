<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Base\SyncController;
use App\Http\Controllers\TakeawayController;

use App\Mail\OrderDelivered;
use App\Mail\OrderFailed;
use App\Mail\StockReminder;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Notification;
use App\Models\Franchise;
use App\Models\Stock;
use App\Models\Admin;
use App\Models\DeliveryPerson;
use App\Models\UberStore;
use App\Services\Deliverect;
use Barryvdh\DomPDF\Facade\Pdf;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ApiOrderController extends SyncController
{
  public  function listOrders(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $startpage = $request->input('startpage');
    $type = $request->input('type');
    $per_page = 10;
    $start_from = $startpage * $per_page;


    $data['order'] = Order::leftJoin('franchises', function ($join) {
      $join->on('franchises.id', '=', 'orders.franchise_id');
    })->leftJoin('customers', function ($join) {
      $join->on('customers.customer_id', '=', 'orders.order_customerid');
    })->leftJoin('address', function ($join) {
      $join->on('customer_addresses.id', '=', 'orders.order_address_id');
    })->leftJoin('deliveryperson', function ($join) {
      $join->on('deliveryperson.dp_id', '=', 'orders.delivery_person_id');
    })->join('order_status', 'order_status.os_id', 'orders.order_status')->where('delivery_person_id', $id)
      ->whereNull('orders.deleted_at')
      ->where('order_status', '!=', '0')->orderBy('orders.created_at', 'desc');
    if ($type == 1) {
      $data['order'] = $data['order']->whereIn('os_id', [1, 2, 3, 4, 5, 6, 9, 12])->skip($start_from)->limit($per_page)->get(['order_id', 'order_price', 'order_delivery_charge', 'order_final_amount', 'franchises_email', 'first_name as franchises_firstname', 'last_name as franchises_lastname', 'customer_name', 'customer_email', 'address', 'os_id', 'os_name', 'os_color']);
    } else {
      $data['order'] = $data['order']->whereIn('os_id', [8, 10])->skip($start_from)->limit($per_page)->get(['order_id', 'order_price', 'order_delivery_charge', 'order_final_amount', 'franchises_email', 'first_name as franchises_firstname', 'last_name as franchises_lastname', 'customer_name', 'customer_email', 'address', 'os_id', 'os_name', 'os_color']);
    }
    foreach ($data['order'] as $key => $value) {
      $data['order'][$key]['franchises_email'] = ($data['order'][$key]['franchises_email']) == null ? '-' : $data['order'][$key]['franchises_email'];
      $data['order'][$key]['franchises_firstname'] = ($data['order'][$key]['franchises_firstname']) == null ? '-' : $data['order'][$key]['franchises_firstname'];
      $data['order'][$key]['franchises_lastname'] = ($data['order'][$key]['franchises_lastname']) == null ? '-' : $data['order'][$key]['franchises_lastname'];
      $data['order'][$key]['count'] = OrderDetail::where('od_orderid', $value['order_id'])->get()->count();
    }
    return response()->json(['status' => true, 'data' => $data['order']]);
  }
  public function orderDetail(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $order_id = $request->input('order_id');

    $data['order'] = Order::leftJoin('franchises', function ($join) {
      $join->on('franchises.id', '=', 'orders.franchise_id');
    })->leftJoin('customers', function ($join) {
      $join->on('customers.customer_id', '=', 'orders.order_customerid');
    })->leftJoin('customer_addresses', function ($join) {
      $join->on('customer_addresses.id', '=', 'orders.order_address_id');
    })->leftJoin('deliveryperson', function ($join) {
      $join->on('deliveryperson.dp_id', '=', 'orders.delivery_person_id');
    })->join('order_status', 'order_status.os_id', 'orders.order_status')
      ->whereNull('orders.deleted_at')
      ->where('orders.order_id', $order_id)->first([
        'order_id',
        'order_price',
        'order_payment_status',
        'order_delivery_charge',
        'order_final_amount',
        'order_discount as promocode_discount',
        'order_final_with_discount',
        'order_deliverytime',
        'franchises_email',
        'franchises.image as franchises_logo',
        'first_name as franchises_firstname',
        'last_name as franchises_lastname',
        'company_name as franchises_company',
        'house_no_street as franchises_street',
        'block_no as franchises_blockno',
        'franchises.post_code as franchises_postcode',
        'residence as franchises_residence',
        'landmark as franchises_landmark',
        'customer_name',
        'customer_email',
        'customer_contact_no',
        'address',
        'os_id',
        'os_name',
        'os_color',
        'failed_reason',
        'customer_addresses.latitude',
        'customer_addresses.longitude',
        'address_longitude',
        'order_note',
        'order_payment_status',
        'post_code'
      ]);
    if ($data['order']['franchises_logo'] != "") {
      $data['order']['franchises_logo'] = asset('uploads/franchiseprofile') . '/' . $data['order']['franchises_logo'];
    } else {
      $data['order']['franchises_logo'] = asset('img/logo.png');
    }
    $data['order']['franchises_email'] = ($data['order']['franchises_email']) == null ? '-' : $data['order']['franchises_email'];
    $data['order']['franchises_firstname'] = ($data['order']['franchises_firstname']) == null ? '-' : $data['order']['franchises_firstname'];
    $data['order']['franchises_lastname'] = ($data['order']['franchises_lastname']) == null ? '-' : $data['order']['franchises_lastname'];
    $data['order']['franchises_company'] = ($data['order']['franchises_company'] == null) ? "" : $data['order']['franchises_company'];
    $data['order']['franchises_blockno'] = ($data['order']['franchises_blockno'] == null) ? "" : $data['order']['franchises_blockno'];
    $data['order']['franchises_residence'] = ($data['order']['franchises_residence'] == null) ? "" : $data['order']['franchises_residence'];
    $data['order']['franchises_landmark'] = ($data['order']['franchises_landmark'] == null) ? "" : $data['order']['franchises_landmark'];
    $data['order']['customer_contact_no'] = ($data['order']['customer_contact_no'] == null) ? "" : $data['order']['customer_contact_no'];
    $data['order']['address'] = ($data['order']['address'] == null) ? "" : $data['order']['address'];
    $data['order']['failed_reason'] = ($data['order']['failed_reason'] == null) ? "" : $data['order']['failed_reason'];
    $data['order']['order_deliverytime'] = ($data['order']['order_deliverytime'] == null) ? "" : $data['order']['order_deliverytime'];
    $data['order']['order_note'] = ($data['order']['order_note'] == null) ? "" : $data['order']['order_note'];
    $data['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $order_id)
      ->get(['od_qty', 'od_vattotal', 'od_vatprice', 'product_name', 'image']);
    foreach ($data['orderdetail'] as $key => $value) {
      $data['orderdetail'][$key]['image'] = asset('uploads/product/thumb') . '/' . $value['image'];
    }

    return response()->json(['status' => true, 'orderDetail' => $data['order'], 'orderItems' => $data['orderdetail']]);
  }

  public function updateOrderStatus(Request $request)
  {

    $token = $request->input('token');
    $id = $request->input('id');
    $order_id = $request->input('order_id');
    $type = $request->input('type');
    $reason = $request->input('reason');
    // 1 = picked up  // READY FOR PICKUP
    // 2 = Cancelled
    // 3 = Delivered
    // 4 = Rejected
    // 5 = Accepted
    // 6 = Failed
    // 7=  Finalized

    if ($type == 1) {
      $status = '5';
      $message = 'picked up';
      Order::where('order_id', $order_id)->update(['order_status' => $status]);
    }
    if ($type == 2) {
      $status = '11';
      $message = 'Cancelled';
      Order::where('order_id', $order_id)->update(['order_status' => $status]);
    }
    if ($type == 3) {
      $status = '6';
      $message = 'Delivered';
      Order::where('order_id', $order_id)->update(['order_status' => $status, 'od_endtime' => now()]);
      // $this->downloadPDF($order_id);

    }
    if ($type == 7) {
      $status = '10';
      $message = 'Finalized';
      Order::where('order_id', $order_id)->update(['order_status' => $status, 'od_endtime' => now()]);
      $this->UpdateStock($order_id);
    }
    if ($type == 4) {
      $status = '7';
      $message = 'Rejected';
      /** @var Order $orderdetail */
      $orderdetail = Order::with('customer')->with('address')->where('order_id', $order_id)->whereNull('deleted_at')->firstOrFail();

      $last_dpid = $orderdetail->delivery_person_id;
      $rejecteddp_id = $orderdetail->od_rejected_id;
      $rejected_ids = $rejecteddp_id == "" ? $last_dpid : $rejecteddp_id . ',' . $last_dpid;

      $finalrjected_id = implode(",", array_unique(explode(",", $rejected_ids)));

      $lat = $orderdetail->address?->latitude;
      $long = $orderdetail->address?->longitude;
      Order::where('order_id', $order_id)->update(['order_status' => $status, 'od_rejected_id' => $finalrjected_id, 'rejected_reason' => $reason]);

      $deliverypersonid = DeliveryPerson::select('deliveryperson_sub.*', 'deliveryperson.*')->where('deliveryperson.dp_onoff', 'online')->whereNull('deliveryperson_sub.deleted_at')->whereNull('deliveryperson.deleted_at')
        ->leftJoin('deliveryperson_sub', function ($join) {
          $join->on('deliveryperson_sub.s_dpid', '=', 'deliveryperson.dp_id')->where('deliveryperson.dp_onoff', 'online');
        })->where('s_fid', $orderdetail['franchise_id'])->whereNotIn('dp_id', explode(",", $rejected_ids))->groupBy('dp_id')->get(['dp_id', 'dp_name']);
      $arr_dp = $deliverypersonid->pluck('dp_id')->toArray();

      if (count($deliverypersonid) == 0) {
        //
        $dp_id = 0;
      } else {
        if (count($deliverypersonid) > 1) {
          $odrer = Order::orderBy('order_id', 'desc')->whereIn('delivery_person_id', $arr_dp)->first();
          $dp_order = $odrer['delivery_person_id'];
          if ($lat != '' && $long != '') {
            $deliverypersonid = DeliveryPerson::select(DB::raw("*,SQRT(POW(69.1 *  (dp_lat - ' . $lat . '), 2) + POW(69.1 * (' . $long . ' - dp_lng) * COS(dp_lat / 57.3), 2)) AS distance"))
              ->where('dp_onoff', 'online')
              ->orderBy('distance', 'ASC')
              ->whereIn('dp_id', $arr_dp)
              ->whereNotIn('dp_id', [$dp_order])
              ->get();
          } else {
            $deliverypersonid = DeliveryPerson::where('dp_onoff', 'online')
              ->whereIn('dp_id', $arr_dp)
              ->whereNotIn('dp_id', [$dp_order])
              ->get();
          }
        } else {
          $deliverypersonid[0]['dp_id'];
        }
        $dp_id = $deliverypersonid[0]['dp_id'];
      }


      if ($dp_id != '' && $dp_id != '0') {
        Order::where('order_id', $order_id)->update(['delivery_person_id' => $dp_id, 'od_assignedtime' => now(), 'order_status' => '2']);
        /** notification*/
        $mess = 'status';
        $notification = new Notification;
        $notification->user_type = 'delivery_person';
        $notification->to_id = $dp_id;
        $notification->text = $mess;
        $notification->save();

        /** push notification */
        $devpersondetail = DeliveryPerson::find($dp_id);
        $frname = Franchise::find($orderdetail['franchise_id']);
        $push = new PushNotification('fcm');
        $mess = $frname['franchises_name'] . ' has assigned order order no. ' . $order_id . ' to you';
        $push->setMessage([
          'data' => [
            'notification' => [
              'title' => 'Order Status',
              'message' => $mess,
              'sound' => 'default',
              'order_id' => $order_id,
              'schedule_id' => 0,
              'message_type' => 'order',

              // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
          ],
        ])

          ->setUrl(env('PUSH_NOTIFICATION_URL'))
          ->setApiKey(env('PUSH_NOTIFICATION_APIKEY'))
          ->setDevicesToken($devpersondetail['dp_devicetoken'])
          ->send()
          ->getFeedback();
      } else {
        Order::where('order_id', $order_id)->update(['delivery_person_id' => 0, 'od_assignedtime' => now(), 'order_status' => '1']);
        /** notification*/
        $mess = 'Please check this Order Order no. ' . $order_id . ' as Delivery person has Rejected this order.';
        $notification = new Notification;
        $notification->user_type = 'customer';
        $notification->to_id = '1';
        $notification->text = $mess;
        $notification->save();
      }
    }
    if ($type == 5) {
      $status = '12';
      $message = 'Accepted';
      Order::where('order_id', $order_id)->update(['order_status' => $status, 'od_starttime' => now()]);
    }
    if ($type == 6) {
      $status = '8';
      $message = 'Failed';

      Order::where('order_id', $order_id)->update(['order_status' => $status, 'failed_reason' => $reason]);

      $order = Order::find($order_id);
      $channel = $order->order_channel_id;

      if ($channel == '60ae1d8e416ad29e6ade8a85') {
        $maildata['order_id'] = $order->order_channel_order_id;
        $maildata['reason'] = $reason;
        Mail::to($_ENV['TAKEAWAY_EMAIL'])
          ->send(new OrderFailed($maildata));
      }
    }
    $orderdetail = Order::find($order_id);
    if ($orderdetail['order_deliverect_id'] != "") {
      Deliverect::deliverectOrderStatus($orderdetail['order_deliverect_id'], $orderdetail['order_receiptid'], $status);
    }
    if ($orderdetail['order_uber_id'] != "" && $orderdetail['order_store_id'] != "") {
      if ($type == 3 || $type == 7  ||  $type == 1) {
        if ($orderdetail['uber_order_delivery_type'] == 'DELIVERY_BY_RESTAURANT') {
          UberStore::updateUberOrderStatus($orderdetail['order_uber_id'], $status);
        }
      }
      if ($type == 2 || $type == 4 || $type == 6) {
        UberStore::cancelUberOrder($orderdetail['order_uber_id']);
      }
    }
    if ($orderdetail['order_takeaway_id'] != "") {
      if ($status == 1 || $status == 12 || $status == 4 || $status == 6 || $status == 10 || $status == 11) {
        TakeawayController::takeawayOrderStatus($orderdetail['order_takeaway_id'], $status, $orderdetail['order_takeaway_key']);
      }
    }

    return response()->json(['status' => true, 'message' => 'Order ' . $message . '!!']);
  }
  public function downloadPDF($id)
  {

    $data['order'] = Order::leftJoin('franchises', function ($join) {
      $join->on('franchises.id', '=', 'orders.franchise_id');
    })->leftJoin('customers', function ($join) {
      $join->on('customers.customer_id', '=', 'orders.order_customerid');
    })->leftJoin('address', function ($join) {
      $join->on('customer_addresses.id', '=', 'orders.order_address_id');
    })->leftJoin('deliveryperson', function ($join) {
      $join->on('deliveryperson.dp_id', '=', 'orders.delivery_person_id');
    })->join('order_status', 'order_status.os_id', 'orders.order_status')
      ->whereNull('orders.deleted_at')
      ->where('orders.order_id', $id)->first();

    $data['orderdetail'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->get();
    $data['per0_total'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->where('vat', '0')->get()->sum('od_total');
    $data['per9_total'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->where('vat', '9')->get()->sum('od_total');
    $data['per21_total'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->where('vat', '21')->get()->sum('od_total');

    $data['per0_vattotal'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->where('vat', '0')->get()->sum('od_vattotal');
    $data['per9_vattotal'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->where('vat', '9')->get()->sum('od_vattotal');
    $data['per21_vattotal'] = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->where('vat', '21')->get()->sum('od_vattotal');


    $pdf = PDF::loadView('pdf.orderpdf', $data)
      ->setPaper('a4', 'potrarit');
    // return $pdf->stream('order_'.$id.'.pdf');
    $filename = 'order_' . $id . '.pdf';
    $pdf->save('uploads/generatepdf/' . $filename);


    $maildata['name'] = $data['order']['customer_name'];
    $maildata['email'] = $data['order']['customer_email'];
    $maildata['file'] = 'order_' . $id . '.pdf';

    Mail::to($data['order']['customer_email'])
      ->send(new OrderDelivered($maildata));


    // return view('order.pdf',$data);

  }
  public function updateStock($id)
  {
    $order = Order::where('order_id', $id)->leftJoin('franchises', function ($join) {
      $join->on('franchises.id', '=', 'orders.franchise_id');
    })->first();

    $product = OrderDetail::join('products', 'products.product_id', 'order_details.od_productid')->where('od_orderid', $id)->get();
    foreach ($product as $value) {
      $currentstock = Stock::where('stock_product', $value['product_id'])->where('franchise_id', $order['franchise_id'])->first();
      $newstock = $currentstock['stock_current'] - $value['od_qty'];
      $stockid = Stock::where('stock_product', $value['product_id'])->where('franchise_id', $order['franchise_id'])->update(['stock_current' => $newstock]);
      if ($newstock <= $currentstock['stock_minimum']) {
        if ($stockid['is_reminder_set'] == '1') {
          $admin = Admin::find(1);

          $maildata['name'] = $admin['name'];
          $maildata['email'] = $admin['email'];
          $maildata['franchise'] = $order['franchises_name'];
          $maildata['product'] = $value['product_name'];
          $maildata['stock_minimum'] = $value['stock_minimum'];
          $maildata['newstock'] = $value['newstock'];
          Mail::to($admin['email'])
            ->send(new StockReminder($maildata));
        }
      }
    }
  }
}
