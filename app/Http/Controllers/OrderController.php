<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\DeliveryPerson;
use App\Models\Franchise;
use App\Models\InvoicePdf;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Stock;
use App\Models\UberStore;
use App\Services\Deliverect;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function orderView($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = Order::select(
                'orders.id',
                'orders.created_at',
                'customer_name',
                'franchises_name',
                'franchise_id',
                'orders.delivery_person_id',
                'dp_name',
                'order_channel_order_id',
                'order_payment_status',
                'order_status',
                'order_cancelled_reason',
                'order_price',
                'order_delivery_charge',
                'order_final_amount',
                'promo_code_id',
                'order_discount',
                'os_name',
                'order_final_with_discount',
                'order_receipt_id',
                'customer_email',
                'phone_code',
                'customer_contact_no',
                'customer_addresses.post_code',
                'address',
                'franchises_email',
                'dp_email',
                'dp_contact_no',
                'order_discount',
                'order_note',
                'orders.channel_id',
                'channel_image',
                'od_end_time',
                'od_start_time',
                'payment_method',
                'order_delivery_time',
                'failed_reason',
                'rejected_reason',
                'od_rejected_id',
                'name',
                'order_store_id',
                'order_uber_id',
                'order_uber_display_id',
                'order_takeaway_id',
                'order_takeaway_key',
                'order_takeaway_public_ref'
            )->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })->leftJoin('customer_addresses', function ($join) {
                $join->on('customer_addresses.id', '=', 'orders.order_address_id');
            })->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })->leftJoin('channels', function ($join) {
                $join->on('channels.id', '=', 'orders.channel_id');
            })->leftJoin('uber_stores', function ($join) {
                $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')
                ->whereNull('orders.deleted_at')
                ->where('orders.id', $id)->first();

            $explode = explode(',', $data['row']['od_rejectedid']);
            $data['last_dp_id'] = $explode[array_key_last($explode)];

            $data['orderdetail'] = Product::join('order_details', 'products.id', 'order_details.order_id')->where('orders.id', $id)->get();
            $data['rejected_by'] = DeliveryPerson::whereIn('id', $explode)->get();
        }

        return view('admin.order.view', $data);
    }

    public function getList(Request $request)
    {
        session()->forget('ad_frs_id');
        session()->forget('ad_status');

        $query = Order::select(
            'orders.id as order_id', // Specify the table for id
            'orders.created_at',
            'customer_name',
            'franchises_name',
            'dp_name',
            'orders.order_delivery_charge',
            'orders.order_discount',
            'order_channel_order_id',
            'order_payment_status',
            'order_status',
            'order_cancelled_reason',
            'order_statuses.id as os_id',
            'os_name',
            'order_final_with_discount',
            'channels.id as channel_id', // Specify the table for channel id
            'channel_image',
            'channel_name',
            'payment_method',
            'customers.customer_contact_no',
            'customers.customer_email',
            'order_address_id',
            'customer_addresses.address',
            'customer_addresses.post_code',
            'customer_addresses.house_no',
            'name',
            'order_store_id',
            'order_uber_id',
            'order_uber_display_id',
            'order_takeaway_id',
            'order_takeaway_key',
            'order_takeaway_public_ref'
        )
            ->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })
            ->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })
            ->leftJoin('customer_addresses', function ($join) {
                $join->on('customer_addresses.id', '=', 'orders.order_address_id');
            })
            ->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })
            ->leftJoin('channels', function ($join) {
                $join->on('channels.id', '=', 'orders.channel_id');
            })
            ->leftJoin('uber_stores', function ($join) {
                $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
            })
            ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_status') // Fixed join condition
            ->where('order_status', '!=', '0')
            ->whereNull('orders.deleted_at');

        if ($request->get('frs_id') && $request->get('frs_id') != '') {
            Session::put('ad_frs_id', $request->get('frs_id'));
            $query = $query->where('orders.franchise_id', $request->get('frs_id'));
        }
        if ($request->get('status') && $request->get('status') != '') {
            Session::put('ad_status', $request->get('status'));
            $query = $query->where('orders.order_status', $request->get('status'));
        }

        $column_order = ['orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount', 'order_final_with_discount'];
        $column_search = ['orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount', 'customers.customer_contact_no', 'customers.customer_email', 'customer_addresses.address', 'customer_addresses.post_code', 'customer_addresses.house_no'];
        $start_from = $request->start;
        $per_page = $request->length;

        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }

        // Sorting
        if (isset($request->order[0]['column']) && isset($request->order[0]['dir'])) {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('orders.id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        foreach ($data as $key => $row) {
            $products = Product::join('order_details', 'products.id', 'order_details.product_id')->where('order_details.order_id', $row->order_id)->get();
            $product_price_0 = 0;
            $product_price_9 = 0;
            $product_price_21 = 0;
            foreach ($products as $key1 => $p) {
                if ($p['vat'] == 0) {
                    $product_price_0 += $p['vat_price'] * $p['od_qty'];
                } elseif ($p['vat'] == 9) {
                    $product_price_9 += $p['vat_price'] * $p['od_qty'];
                } elseif ($p['vat'] == 21) {
                    $product_price_21 += $p['vat_price'] * $p['od_qty'];
                }
            }
            $data[$key]['product_price_0'] = number_format($product_price_0, 2);
            $data[$key]['product_price_9'] = number_format($product_price_9, 2);
            $data[$key]['product_price_21'] = number_format($product_price_21, 2);
        }

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    public function orderInvoicelist(Request $request)
    {
        session()->forget('fs_status');
        session()->forget('fs_month');
        session()->forget('fs_week');
        session()->forget('fs_year');
        session()->forget('fs_startpage');
        session()->forget('fs_perpage');
        session()->forget('fs_searchvalue');
        session()->forget('fs_id');
        session()->forget('fs_cid');

        $query = Order::select(
            'orders.id',
            'orders.created_at',
            'customer_name',
            'order_channel_order_id',
            'channels.id as channel_id', // Specify the table for channel id
            'order_uber_display_id',
            'order_takeaway_public_ref',
            'order_final_with_discount',
            'order_payment_status',
            'channel_image'
        )
            ->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })
            ->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })
            ->leftJoin('customer_addresses', function ($join) {
                $join->on('customer_addresses.id', '=', 'orders.order_address_id');
            })
            ->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })
            ->leftJoin('channels', function ($join) {
                $join->on('channels.id', '=', 'orders.channel_id');
            })
            ->leftJoin('uber_stores', function ($join) {
                $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
            })
            ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_status') // Fixed join condition
            ->where('order_status', '!=', '0')
            ->whereNull('orders.deleted_at');

        if ($request->get('year') && $request->get('year') != '' && $request->get('week') == '') {
            Session::put('fs_year', $request->get('year'));
            $query = $query->whereRaw("(YEAR(orders.created_at) = '".$request->get('year')."')");
        }

        if ($request->get('month') && $request->get('month') != '' && $request->get('week') == '') {
            Session::put('fs_month', $request->get('month'));
            $query = $query->whereRaw("(MONTH(orders.created_at) = '".$request->get('month')."')");
        }

        if ($request->get('f_id') && $request->get('f_id') != '') {
            Session::put('fs_id', $request->get('f_id'));
            $query = $query->whereRaw("franchises.id = '".$request->get('f_id')."'");
        }

        if ($request->get('cs_id') && $request->get('cs_id') != '') {
            Session::put('fs_cid', $request->get('cs_id'));
            $query = $query->whereRaw("channels.id = '".$request->get('cs_id')."'");
        }

        if ($request->get('week') && $request->get('week') != '') {
            Session::put('fs_week', $request->get('week'));
            $week = explode('/', $request->get('week'));

            $start_from = $week[0];
            $start_from = Carbon::parse($start_from, Session::get('time_zone'))
                ->startOfDay()
                ->setTimezone('UTC');
            $end_to = $week[1];

            $end_to = Carbon::parse($end_to, Session::get('time_zone'))
                ->endOfDay()
                ->setTimezone('UTC');

            $query = $query->where('orders.created_at', '>=', $start_from)->where('orders.created_at', '<=', $end_to);
            // $query = $query->whereBetween("orders.created_at", [$start_from, $end_to]);
        }

        $column_order = ['channel_image', 'orders.id', 'orders.created_at', 'customer_name', 'order_channel_order_id', 'order_final_with_discount', 'order_payment_status']; //set column field database for datatable orderable
        $column_search = ['orders.id', 'orders.created_at', 'customer_name', 'order_channel_order_id', 'order_final_with_discount', 'order_payment_status']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;

        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }

        // Sorting
        if (isset($request->order[0]['column']) && isset($request->order[0]['dir'])) {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('orders.id', 'DESC');
        }

        $total = $query->count();
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $data */
        $data = $query->skip($start_from)->limit($per_page)->get();

        $session_array = [];
        foreach ($data as $key => $row) {
            $checked = false;
            $session_array = Session::get('channel_invoice_order');
            if (! empty($session_array) && in_array($row['order_channelorder_id'], $session_array)) {
                $checked = true;
            }
            $data[$key]['checked'] = $checked;
        }

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    public function deleteOrder(Request $request)
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
            Stock::where('order_id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Order deleted !',
                ]);
        }
    }

    public function updateStatus(Request $request)
    {
        $value = $request->value;
        $order_id = $request->pk;

        /**
         * @var \App\Models\Order $orderdetail
         */
        $orderdetail = Order::findOrFail($order_id);
        $orderdetail->update(['order_status' => $value]);
        $ordercolor = OrderStatus::find($value);

        if ($orderdetail['order_deliverect_id'] != '') {
            Deliverect::deliverectOrderStatus($orderdetail->order_deliverect_id, $orderdetail->order_receipt_id, $value);
        }
        if ($orderdetail['order_uber_id'] != '' && $orderdetail['order_store_id'] != '') {
            if ($value == 6 || $value == 12 || $value == 10) {
                if ($orderdetail['uber_order_delivery_type'] == 'DELIVERY_BY_RESTAURANT') {
                    UberStore::updateUberOrderStatus($orderdetail['order_uber_id'], $value);
                }
            }
            if ($value == 11 || $value == 7 || $value == 8) {
                UberStore::cancelUberOrder($orderdetail['order_uber_id']);
            }
        }
        if ($orderdetail['order_takeaway_id'] != '') {
            if ($value == 1 || $value == 12 || $value == 4 || $value == 6 || $value == 10 || $value == 11) {
                TakeawayController::takeawayOrderStatus($orderdetail['order_takeaway_id'], $value, $orderdetail['order_takeaway_key']);
            }
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Status Changed Successfully!',
                'id'     => $order_id,
                'color'  => $ordercolor['os_color'],
            ]);
    }

    public function franchiseinvoice(Request $request)
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        $data['status_list'] = OrderStatus::whereIN('id', [5, 6, 11, 8])->get();
        $data['monthlist'] = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        for ($i = 0; $i <= 52; $i++) {
            $previous_week = strtotime('-'.$i.' week +1 day');

            $start_week = strtotime('last sunday midnight', $previous_week);
            $end_week = strtotime('next saturday', $start_week);

            $start_week = date('Y-m-d', $start_week);
            $end_week = date('Y-m-d', $end_week);
            $week[$start_week.' / '.$end_week] = $start_week.' - '.$end_week;
        }

        $data['weeklist'] = $week;

        $data['channel_list'] = Channel::get();

        return view('admin.franchise.invoice', $data);
    }

    public function orderList()
    {
        $data['status_list'] = OrderStatus::get();
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        $data['is_accountant'] = auth('admin')->user()->is_accountant;

        return view('admin.order.list', $data);
    }

    // public function orderlist(Request $request)
    // {
    //   session()->forget('fs_status');
    //   session()->forget('fs_month');
    //   session()->forget('fs_week');
    //   session()->forget('fs_year');
    //   session()->forget('fs_startpage');
    //   session()->forget('fs_perpage');
    //   session()->forget('fs_searchvalue');
    //   session()->forget('fs_id');
    //   session()->forget('fs_cid');

    //   $query = Order::select(
    //     'orders.id',
    //     'customers.customer_name',
    //     'order_channel_order_id',
    //     'order_final_with_discount',
    //     'order_payment_status',
    //     'channel_image',
    //     'name',
    //     'order_store_id',
    //     'order_uber_id',
    //     'order_uber_display_id',
    //     'order_takeaway_id',
    //     'order_takeaway_key',
    //     'order_takeaway_public_ref',
    //     'orders.created_at',
    //   )
    //     ->leftJoin('franchises', function ($join) {
    //       $join->on('franchises.id', '=', 'orders.franchise_id');
    //     })->leftJoin('customers', function ($join) {
    //       $join->on('customers.id', '=', 'orders.customer_id');
    //     })->leftJoin('delivery_people', function ($join) {
    //       $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
    //     })->leftJoin('channels', function ($join) {
    //       $join->on('channels.id', '=', 'orders.channel_id');
    //     })->leftJoin('uber_stores', function ($join) {
    //       $join->on('uber_stores.store_id', '=', 'orders.order_store_id');
    //     })->join('order_statuses', 'order_statuses.id', 'orders.order_status')
    //     ->whereNotIn('order_status', ['0', '11', '7'])->whereNull('orders.deleted_at');

    //   if ($request->get('year') && $request->get('year') != ''  && $request->get('week') == '') {
    //     Session::put('fs_year', $request->get('year'));
    //     $query = $query->whereRaw("(YEAR(orders.created_at) = '" . $request->get('year') . "')");
    //   }

    //   if ($request->get('month') && $request->get('month') != '' && $request->get('week') == '') {
    //     Session::put('fs_month', $request->get('month'));
    //     $query = $query->whereRaw("(MONTH(orders.created_at) = '" . $request->get('month') . "')");
    //   }

    //   if ($request->get('f_id') && $request->get('f_id') != '') {
    //     Session::put('fs_id', $request->get('f_id'));
    //     $query = $query->whereRaw("franchises.id = '" . $request->get('f_id') . "'");
    //   }

    //   if ($request->get('id') && $request->get('id') != '') {
    //     Session::put('fs_cid', $request->get('id'));
    //     $query = $query->whereRaw("channels.id = '" . $request->get('id') . "'");
    //   }

    //   if ($request->get('week') && $request->get('week') != '') {
    //     Session::put('fs_week', $request->get('week'));
    //     $week = explode("/", $request->get('week'));

    //     $start_from = $week[0];
    //     $start_from = Carbon::parse($start_from, Session::get('time_zone'))
    //       ->startOfDay()
    //       ->setTimezone('UTC');
    //     $end_to = $week[1];

    //     $end_to = Carbon::parse($end_to, Session::get('time_zone'))
    //       ->endOfDay()
    //       ->setTimezone('UTC');

    //     $query = $query->where('orders.created_at', '>=', $start_from)->where('orders.created_at', '<=', $end_to);
    //     // $query = $query->whereBetween("orders.created_at", [$start_from, $end_to]);
    //   }

    //   $column_order = ['channel_image', 'order_id', 'orders.created_at', 'customer_name', 'order_channel_order_id', 'order_final_with_discount', 'order_payment_status']; //set column field database for datatable orderable
    //   $column_search = ['order_id', 'orders.created_at', 'customer_name', 'order_channel_order_id', 'order_final_with_discount', 'order_payment_status']; //set column field database for datatable searchable
    //   $start_from = $request->start;
    //   $per_page = $request->length;

    //   Session::put('fs_startpage', $request->start);
    //   Session::put('fs_perpage', $request->length);
    //   $rawQuery = '';

    //   dd($query->get());

    //   //Search
    //   if ($request->search['value'] && $request->search['value'] != '') {
    //     Session::put('fs_searchvalue', $request->search['value']);
    //     $search = $request->search['value'];
    //     $i = 0;

    //     foreach ($column_search as $key => $value) {
    //       // dd($value);
    //       if ($i === 0) // first loop
    //       {
    //         $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
    //         $rawQuery .= $value . ' LIKE "%' . $search . '%"';
    //       } else {
    //         $rawQuery .= ' OR ' . $value . ' LIKE "%' . $search . '%"';
    //       }
    //       if (count($column_search) - 1 == $i) {
    //         //last loop
    //         $rawQuery .= ')'; //close bracket
    //       }
    //       $i++;
    //     }

    //     $query = $query->whereRaw($rawQuery);
    //     // print_r($query);die;
    //   }
    //   //Sorting
    //   if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
    //     $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
    //   } else {
    //     $query = $query->orderBy('order_id', 'DESC');
    //   }

    //   $total = $query->get()->count();
    //   $data = $query->skip($start_from)->limit($per_page)->get();
    //   // print_r($data);die;
    //   $session_array = [];
    //   foreach ($data as $key => $row) {
    //     $checked = false;
    //     $session_array = Session::get('channel_invoice_order');
    //     if (!empty($session_array) && in_array($row['order_channel_order_id'], $session_array)) {
    //       $checked = true;
    //     }
    //     $data[$key]['checked'] = $checked;
    //   }

    //   $order_total_amount = 0;
    //   foreach ($data as $row) {
    //     $order_total_amount += $row->order_final_with_discount;
    //   }
    //   return response()
    //     ->json([
    //       'data' => $data,
    //       'total' => $total,
    //       'total_order_amount' => $order_total_amount
    //     ]);
    // }

    public function saveOrderchannel(Request $request)
    {
        $order_array = [];

        $order_array = Session::get('channel_invoice_order');

        $order_array1 = [];

        if (! empty($order_array)) {
            if (in_array($request->orderId, $order_array)) {
                $key = array_search($request->orderId, $order_array);
                unset($order_array[$key]);
            } else {
                $order_array[] = $request->orderId;
            }
        } else {
            $order_array[] = $request->orderId;
        }

        $order_array = Session::put('channel_invoice_order', $order_array);

        return response()
            ->json([
                'status' => true,
            ]);
    }

    public function saveallOrderchannel(Request $request)
    {
        $search_value = Session::get('fs_searchvalue');
        $year = Session::get('fs_year');
        $month = Session::get('fs_month');
        $week = Session::get('fs_week');
        $fid = Session::get('fs_id');
        $cid = Session::get('fs_cid');

        $query = Order::select(
            'orders.id',
            'order_channel_order_id'
        )->leftJoin('franchises', function ($join) {
            $join->on('franchises.id', '=', 'orders.franchise_id');
        })->leftJoin('channels', function ($join) {
            $join->on('channels.id', '=', 'orders.id');
        })->join('order_statuses', 'order_statuses.id', 'orders.order_status')
            ->whereNotIn('order_status', ['0', '11', '7']);

        if ($year != '') {
            $query = $query->whereRaw("(YEAR(orders.created_at) = '".$year."')");
        }

        if ($month != '') {
            $query = $query->whereRaw("(MONTH(orders.created_at) = '".$month."')");
        }

        if ($fid != '') {
            $query = $query->whereRaw("franchises.id = '".$fid."'");
        }

        if ($cid != '') {
            $query = $query->whereRaw("channels.id = '".$cid."'");
        }

        if ($week != '') {
            $week = explode('/', $week);

            $start_from = $week[0];
            $start_from = Carbon::parse($start_from, Session::get('time_zone'))
                ->startOfDay()
                ->setTimezone('UTC');
            $end_to = $week[1];

            $end_to = Carbon::parse($end_to, Session::get('time_zone'))
                ->endOfDay()
                ->setTimezone('UTC');

            $query = $query->where('orders.created_at', '>=', $start_from)->where('orders.created_at', '<=', $end_to);
            // $query = $query->whereBetween("orders.created_at", [$start_from, $end_to]);
        }

        $column_search = ['orders.id', 'orders.created_at', 'customer_name', 'order_channel_order_id', 'order_final_with_discount', 'order_payment_status']; //set column field database for datatable searchable
        //Search
        if ($search_value) {
            $search = $search_value;

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }

        $data = $query->get();

        $order_array = Session::get('channel_invoice_order');

        if (! empty($order_array)) {
            foreach ($data as $key => $row) {
                if (! in_array($row->order_channel_order_id, $order_array)) {
                    $order_array[] = $row->order_channel_order_id;
                } else {
                    unset($row->order_channel_order_id);
                }
            }
        } else {
            foreach ($data as $key => $row) {
                $order_array[] = $row->order_channel_order_id;
            }
        }

        Session::put('channel_invoice_order', $order_array);

        $checked = Session::get('select_all_checked');

        if ($checked == 'checked') {
            session()->forget('select_all_checked');
            session()->forget('channel_invoice_order');
            Session::save();
        } else {
            Session::put('select_all_checked', 'checked');
        }

        return response()
            ->json([
                'status' => true,
            ]);
    }

    public function editInvoice(Request $request)
    {
        /** @var Franchise $franchise */
        $franchise = Franchise::findOrFail($request->franchise);

        $fid = $franchise->id;
        $fname = $franchise->franchises_name;
        $house_no_street = $franchise->house_no_street;
        $block_no = $franchise->block_no;
        $post_code = $franchise->post_code;
        $residence = $franchise->residence;
        $landmark = $franchise->landmark;
        $per_day_charges = $franchise->per_day_charges;
        $royalty = $franchise->royalty;
        $franchises_no = $franchise->franchise_number;
        $country = $franchise->country;
        $bank_account = $franchise->bank_account;

        $year = $request->year;
        $month = $request->month;

        $week = explode('/', $request->week);
        $start_from = $week[0];
        $data['start_from_date'] = $start_from;
        $start_from = Carbon::parse($start_from, Session::get('time_zone'))
            ->startOfDay()
            ->setTimezone('UTC');
        $end_to = $week[1];
        $data['end_to_date'] = $end_to;
        $end_to = Carbon::parse($end_to, Session::get('time_zone'))
            ->endOfDay()
            ->setTimezone('UTC');

        $channelOrderArray = Session::get('channel_invoice_order');

        $query = Order::select(
            'orders.id',
            'orders.created_at',
            'customer_name',
            'franchises_name',
            'dp_name',
            'order_channel_order_id',
            'order_payment_status',
            'order_status',
            'order_cancelled_reason',
            'os_name',
            'order_final_with_discount',
            'channel_image',
            'channel_name'
        )
            ->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })->leftJoin('channels', function ($join) {
                $join->on('channels.id', '=', 'orders.id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')->whereNotIn('order_status', ['0', '11', '7', '8'])->whereNull('orders.deleted_at')
            ->where('franchise_id', $fid);

        $query = $query->where('orders.created_at', '>=', $start_from)
            ->where('orders.created_at', '<=', $end_to);

        if ($request->channel) {
            $query->where('channels.id', $request->channel);
        }

        if (! empty($channelOrderArray)) {
            $query->whereIN('orders.order_channel_order_id', $channelOrderArray);
            session()->forget('channel_invoice_order');
            session()->forget('select_all_checked');
            Session::save();
        }

        $data['total'] = $query->count();
        $data['order'] = $query->get();

        $total_order_amount = 0;
        $online_pay_total = 0;
        $online_total_order = 0;
        $ondelivery_amount = 0;
        $ondelivery_total = 0;
        foreach ($data['order'] as $order) {
            $total_order_amount += $order->order_final_with_discount;
            if ($order->order_payment_status == true) {
                $online_pay_total += $order->order_final_with_discount;
                $online_total_order++;
            } else {
                $ondelivery_amount += $order->order_final_with_discount;
                $ondelivery_total++;
            }
        }
        $data['total_order_amount'] = $total_order_amount;
        $data['online_pay_total'] = $online_pay_total;
        $data['online_total_order'] = $online_total_order;
        $data['ondelivery_total'] = $ondelivery_total;
        $data['ondelivery_amount'] = $ondelivery_amount;
        // $data['start_from_date'] = $start_from;
        // $data['end_to_date'] = $end_to;
        $data['franchise_address'] = [
            'house_no_street' => $house_no_street,
            'block_no'        => $block_no,
            'post_code'       => $post_code,
            'residence'       => $residence,
            'landmark'        => $landmark,
            'franchise_name'  => $fname,
            'royalty'         => $royalty,
            'per_day_charges' => $per_day_charges,
            'franchises_no'   => $franchises_no,
            'country'         => $country,
        ];

        $today_date = Carbon::parse(date('Y-m-d H:i:s'), 'Europe/Amsterdam')
            ->setTimezone('UTC');
        $today_date = date('d-m-Y', strtotime($today_date));

        $data['bank_account'] = $bank_account;

        if ($data['total'] > 0 || $franchise->start_from_date <= $today_date) {
            $data['image_url'] = storage_path('app/public/img/').'247Drank.jpg';
            /** @var null|InvoicePdf $invoicePdf */
            $invoicePdf = InvoicePdf::where('from_date', $data['start_from_date'])->where('to_date', $data['end_to_date'])->where('franchise_id', $fid)->first();
            if (! empty($invoicePdf)) {
                $data['factuur_no'] = $invoicePdf->order_id;
                $pdf = Pdf::loadView('pdf.invoicepdf', $data)->setPaper('a4', 'potrarit');

                $filename = '247DRANK.nl_'.$franchises_no.'_'.$invoicePdf->order_id.'_invoice_and_specifications_'.date('d-m-Y', strtotime($data['start_from_date'])).'-'.date('d-m-Y', strtotime($data['end_to_date'])).'.pdf';

                InvoicePdf::where(['from_date' => $data['start_from_date'], 'to_date' => $data['end_to_date'], 'franchise_id' => $fid])
                    ->update(['amount' => $total_order_amount, 'paid_amount' => $online_pay_total, 'pdf_name' => $filename]);

                if (! empty($invoicePdf)) {
                    $pdf->stream($filename);
                    $pdf->save('uploads/generatepdf/'.$filename);
                }
            } else {
                $invoicePdf = InvoicePdf::orderBy('id', 'DESC')->first();
                if (! empty($invoicePdf)) {
                    $explodeArray = explode('-', $invoicePdf->order_id);
                    $num = $explodeArray[1] + 1;
                    if ($num <= 9) {
                        $orderId = $explodeArray[0].'-0'.$num;
                    } else {
                        $orderId = $explodeArray[0].'-'.$num;
                    }
                } else {
                    $orderId = date('Y').'-01';
                }
                $data['factuur_no'] = $orderId;
                $pdf = PDF::loadView('pdf.invoicepdf', $data)->setPaper('a4', 'potrarit');

                $filename = '247DRANK.nl_'.$franchises_no.'_'.$orderId.'_invoice_and_specifications_'.date('d-m-Y', strtotime($data['start_from_date'])).'-'.date('d-m-Y', strtotime($data['end_to_date'])).'.pdf';

                $invoicePdf = new InvoicePdf;
                $invoicePdf->order_id = $orderId;
                $invoicePdf->from_date = $data['start_from_date'];
                $invoicePdf->to_date = $data['end_to_date'];
                $invoicePdf->amount = $total_order_amount;
                $invoicePdf->paid_amount = $online_pay_total;
                $invoicePdf->franchise_id = $fid;
                $invoicePdf->pdf_name = $filename;
                $invoicePdf->save();

                $pdf->save('uploads/generatepdf/'.$filename);
            }

            return response()
                ->json([
                    'status' => true,
                    // 'file' => 'uploads/generatepdf/'.$filename, // For debugging
                ]);
        }
    }

    public function franchiseinvoicelist(Request $request)
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        $data['monthlist'] = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        for ($i = 0; $i <= 52; $i++) {
            $previous_week = strtotime('-'.$i.' week +1 day');

            $start_week = strtotime('last sunday midnight', $previous_week);
            $end_week = strtotime('next saturday', $start_week);

            $start_week = date('Y-m-d', $start_week);
            $end_week = date('Y-m-d', $end_week);
            $week[$start_week.' / '.$end_week] = $start_week.' - '.$end_week;
        }

        $data['weeklist'] = $week;

        return view('admin.franchise.invoice-list', $data);
    }

    public function getinvoicePdfList(Request $request)
    {
        session()->forget('fs_week');
        session()->forget('fs_f_id');

        $query = InvoicePdf::select('invoice_pdfs.*', 'franchises.franchises_name')->leftJoin('franchises', function ($join) {
            $join->on('franchises.id', '=', 'invoice_pdfs.franchise_id');
        });

        if ($request->get('week') && $request->get('week') != '') {
            Session::put('fs_week', $request->get('week'));
            $week = explode('/', $request->get('week'));
            $start_from = $week[0];
            $end_to = $week[1];

            $query = $query->whereDate('invoice_pdfs.created_at', '>=', $start_from)
                ->whereDate('invoice_pdfs.created_at', '<=', $end_to);
        }

        if ($request->get('f_id') && $request->get('f_id') != '') {
            Session::put('fs_f_id', $request->get('f_id'));
            $query = $query->where('invoice_pdfs.franchise_id', $request->get('f_id'));
        }

        $column_order = ['order_id', 'franchises_name', 'from_date', 'to_date', 'amount', 'paid_amount']; //set column field database for datatable orderable
        $column_search = ['order_id', 'franchises_name', 'from_date', 'to_date', 'amount', 'paid_amount']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;

        $rawQuery = '';
        //Search

        if ($request->search['value'] && $request->search['value'] != '') {
            Session::put('fs_searchvalue', $request->search['value']);
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
            $query = $query->orderBy('invoice_pdfs.id', 'DESC');
        }

        $total = $query->count();
        $data = $query->skip($start_from)->limit($per_page)->get();

        foreach ($data as $key => $row) {
            $data[$key]->amount = ' € '.$row->amount;
            $data[$key]->paid_amount = ' € '.$row->paid_amount;
            $data[$key]->date = date('Y-m-d', strtotime(' +1 day', strtotime($row->to_date)));
        }

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }
}
