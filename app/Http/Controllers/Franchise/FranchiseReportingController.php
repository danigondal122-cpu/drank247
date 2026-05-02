<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\InvoicePdf;
use App\Models\Order;
use App\Models\OrderStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FranchiseReportingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function reporting()
    {
        $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        $data['status_list'] = OrderStatus::whereIn('id', [5, 6, 11, 8])->get();
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
            // echo $start_week.' '.$end_week ;
        }

        $data['weeklist'] = $week;

        return view('franchise.reporting.list', $data);
    }

    public function getStartAndEndDate(Request $request)
    {
        // print_r(Session::get('fs_week'));die;
        $month = intval($request->month);        //force month to single integer if '0x'
        $year = $request->year;

        $suff = ['st', 'nd', 'rd', 'th', 'th', 'th'];     //week suffixes
        $end = date('t', mktime(0, 0, 0, $month, 1, $year));     //last date day of month: 28 - 31
        $start = date('w', mktime(0, 0, 0, $month, 1, $year));   //1st day of month: 0 - 6 (Sun - Sat)
        $last = 7 - $start;           //get last day date (Sat) of first week
        $noweeks = ceil((($end - ($last + 1)) / 7) + 1);    //total no. weeks in month
        $output = '<option>Select Week</option>';            //initialize string
        $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);

        for ($x = 1; $x < $noweeks + 1; $x++) {
            if ($x == 1) {
                $startdate = "$year-$monthlabel-01";
                $day = $last - 6;
            } else {
                $day = $last + 1 + (($x - 2) * 7);
                $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                $startdate = "$year-$monthlabel-$day";
            }
            if ($x == $noweeks) {
                $enddate = "$year-$monthlabel-$end";
            } else {
                $dayend = $day + 6;
                $dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
                $enddate = "$year-$monthlabel-$dayend";
            }
            $output .= '<option value="'.$startdate.' / '.$enddate.'">'.$startdate.' - '.$enddate.'</option>';
            // $output .= "$startdate - $enddate <br />";
        }

        // print_r($output);die;
        return $output;
    }

    public function generateOrderPdf()
    {
        // echo Session::get('fs_week');die;
        // ini_set('memory_limit', '-1');
        $fid = auth('franchise')->id();
        // $start_page = Session::get('fs_startpage');
        // $per_page = Session::get('fs_perpage');
        $search_value = Session::get('fs_searchvalue');
        $year = Session::get('fs_year');
        $month = Session::get('fs_month');
        $week = Session::get('fs_week');

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
            'orders.channel_id',
            'channel_image',
            'channel_name',
            'channels.channel_id as channel_id_id',
        )
            ->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })->leftJoin('customers', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id');
            })->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })->leftJoin('channels', function ($join) {
                $join->on('channels.id', '=', 'orders.channel_id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')->whereNotIn('order_status', ['0', '11', '7', '8'])
            ->where('franchise_id', $fid);

        if ($year != '') {
            $query = $query->whereRaw("(YEAR(orders.created_at) = '".$year."')");
        }

        if ($month != '') {
            $query = $query->whereRaw("(MONTH(orders.created_at) = '".$month."')");
        }

        if ($week != '') {
            $week = explode('/', $week);
            $start_from = $week[0];
            $data['start_from_date'] = $start_from;
            $start_from = now()->parse($start_from, Session::get('time_zone'))
                ->startOfDay()
                ->setTimezone('UTC');
            $end_to = $week[1];
            $data['end_to_date'] = $end_to;
            $end_to = now()->parse($end_to, Session::get('time_zone'))
                ->endOfDay()
                ->setTimezone('UTC');

            // $query = $query->whereRaw("(orders.created_at between '" . $start_from . "' and '" . $end_to . "')");
            // $query = $query->whereBetween('orders.cre1ated_at', [$start_from, $end_to]);
            $query = $query->where('orders.created_at', '>=', $start_from)
                ->where('orders.created_at', '<=', $end_to);
        }

        $column_search = ['channel_image', 'orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount']; //set column field database for datatable searchable
        //Search
        if ($search_value && $search_value != '') {
            $search = $search_value;

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }

        // echo $start_from;die;
        $total = $query->get()->count();
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> */
        $data['order'] = $query->get();
        $data['total'] = $total;
        //  echo '<pre>'; print_r($data['order']);die;
        $total_order_amount = 0;
        $online_pay_total = 0;
        $online_total_order = 0;
        foreach ($data['order'] as $order) {
            $total_order_amount += $order->order_final_with_discount;
            if ($order->order_payment_status == true) {
                $online_pay_total += $order->order_final_with_discount;
                $online_total_order++;
            }
        }
        $data['total_order_amount'] = $total_order_amount;
        $data['online_pay_total'] = $online_pay_total;
        $data['online_total_order'] = $online_total_order;
        // set_time_limit(2);
        // echo '<pre>';print_r($data);die;
        $data['image_url'] = asset('img/247-Drank-Logo.png');
        $pdf = Pdf::loadView('pdf.reportingpdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption(['isRemoteEnabled' => true]);

        return $pdf->stream('order_'.$fid.'.pdf');
        $filename = 'order_'.$fid.'.pdf';
        $pdf->save('uploads/generatepdf/'.$filename);
    }

    public function generateInvoicePdf()
    {
        /** @var Franchise $franchise */
        $franchise = auth('franchise')->user();
        $fid = auth('franchise')->id();
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

        // $start_page = Session::get('fs_startpage');
        // $per_page = Session::get('fs_perpage');
        $search_value = Session::get('fs_searchvalue');
        $year = Session::get('fs_year');
        $month = Session::get('fs_month');
        $week = Session::get('fs_week');

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
                $join->on('channels.id', '=', 'orders.channel_id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')->whereNotIn('order_status', ['0', '11', '7', '8'])->whereNull('orders.deleted_at')
            ->where('franchise_id', $fid);

        if ($year != '') {
            $query = $query->whereRaw('(YEAR(orders.created_at) = ?)', [$year]);
        }

        if ($month != '') {
            $query = $query->whereRaw('(MONTH(orders.created_at) = ?)', [$month]);
        }

        $start_from = '';
        $end_to = '';
        if ($week != '') {
            $week = explode('/', $week);
            $start_from = $week[0];
            $data['start_from_date'] = $start_from;
            $start_from = now()->parse($start_from, Session::get('time_zone'))
                ->startOfDay()
                ->setTimezone('UTC');
            $end_to = $week[1];
            $data['end_to_date'] = $end_to;
            $end_to = now()->parse($end_to, Session::get('time_zone'))
                ->endOfDay()
                ->setTimezone('UTC');

            // $query = $query->whereRaw("(orders.created_at between '" . $start_from . "' and '" . $end_to . "')");
            $query = $query->where('orders.created_at', '>=', $start_from)
                ->where('orders.created_at', '<=', $end_to);
            // $query = $query->whereBetween('orders.cre1ated_at', [$start_from, $end_to]);
        }

        $column_search = ['orders.id', 'orders.created_at', 'customer_name', 'franchises_name', 'dp_name', 'order_channel_order_id', 'order_final_with_discount']; //set column field database for datatable searchable
        //Search
        if ($search_value && $search_value != '') {
            $search = $search_value;

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }

        $total = $query->count();
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> */
        $data['order'] = $query->get();
        $data['total'] = $total;
        //  echo '<pre>'; print_r($data['order']);die;
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

        // set_time_limit(2);
        // echo '<pre>';print_r($data);die;
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
        $data['image_url'] = asset('img/247-Drank-Logo.png');
        $data['bank_account'] = $bank_account;

        $pdf = Pdf::loadView('pdf.invoicepdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption(['isRemoteEnabled' => true]);

        // $filename = '247DRANK_nl_' . $franchises_no .'_'.$orderId. '_invoice_and_specifications_'.date('d-m-Y',strtotime($start_from)).'-'.date('d-m-Y',strtotime($end_to)).'.pdf';
        $filename = '247DRANK_nl_'.$franchises_no.'_'.$orderId.'_invoice_and_specifications.pdf';

        return $pdf->stream($filename);

        $pdf->save('uploads/generatepdf/'.$filename);
    }

    public function invoiceReport()
    {
        // $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
        // $data['status_list'] = OrderStatus::whereIN('os_id', [5, 6, 11, 8])->get();
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

        return view('franchise.reporting.invoice', $data);
    }

    public function getinvoicePdfList(Request $request)
    {
        // print_r(Session::get('fs_week'));die;
        session()->forget('fs_status');
        session()->forget('fs_month');
        session()->forget('fs_week');
        session()->forget('fs_year');
        session()->forget('fs_startpage');
        session()->forget('fs_perpage');
        session()->forget('fs_searchvalue');
        $fid = auth('franchise')->id();
        $query = InvoicePdf::query()->where('franchise_id', $fid);

        if ($request->get('year') && $request->get('year') != '') {
            Session::put('fs_year', $request->get('year'));
            $query = $query->whereRaw("(YEAR(created_at) = ?)", [$request->get('year')]);
        }

        if ($request->get('month') && $request->get('month') != '') {
            Session::put('fs_month', $request->get('month'));
            $query = $query->whereRaw("(MONTH(created_at) = ?)", [$request->get('month')]);
        }

        if ($request->get('week') && $request->get('week') != '') {
            Session::put('fs_week', $request->get('week'));
            $week = explode('/', $request->get('week'));
            $start_from = $week[0];
            $end_to = $week[1];

            $query = $query->whereDate('created_at', '>=', $start_from)
                ->whereDate('created_at', '<=', $end_to);
            // $query = $query->whereBetween("created_at", [$start_from, $end_to]);
        }

        $column_order = ['order_id', 'from_date', 'to_date', 'amount', 'paid_amount']; //set column field database for datatable orderable
        $column_search = ['order_id', 'from_date', 'to_date', 'amount', 'paid_amount']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;

        // Session::put('fs_startpage', $request->start);
        // Session::put('fs_perpage', $request->length);
        $rawQuery = '';
        //Search

        if ($request->search['value'] && $request->search['value'] != '') {
            Session::put('fs_searchvalue', $request->search['value']);
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('id', 'DESC');
        }

        $total = $query->get()->count();
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
