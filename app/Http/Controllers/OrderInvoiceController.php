<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceFranchise;

use App\Models\Franchise;
use App\Models\Order;
use App\Models\InvoicePdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class OrderInvoiceController extends Controller
{

    public function downloadInvoicePdf(Request $request)
    {
        $today_date = Carbon::parse(date('Y-m-d H:i:s'), 'Europe/Amsterdam')
            ->setTimezone('UTC');
        $today_date = date('d-m-Y', strtotime($today_date));
        // $franchise = Franchise::where('start_from_date','<=',$today_date)->get();
        $franchise = Franchise::all();
        foreach ($franchise as $key => $row) {
            $fid = $row->franchise_id;
            $fname = $row->franchises_name;
            $house_no_street = $row->house_no_street;
            $block_no = $row->block_no;
            $post_code = $row->post_code;
            $residence = $row->residence;
            $landmark = $row->landmark;
            $per_day_charges = $row->per_day_charges;
            $royalty = $row->royalty;
            $franchises_no = $row->franchise_number;
            $country = $row->country;
            $bank_account = $row->bank_account;


            $year = date('Y');
            $month = date('m');
            // $year = '2021';
            // $month = '12';
            // $ts = strtotime(date('Y-m-d'));
            $ts = strtotime("-1 week +1 day");
            $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
            $start_display_date = date('Y-m-d', $start);
            // $start_date = date('Y-m-d',strtotime('-1 day', $start));
            $end_date = date('Y-m-d', strtotime('next saturday', $start));

            $start_date = Carbon::parse($start_display_date, 'Europe/Amsterdam')
                ->startOfDay()
                ->setTimezone('UTC');

            $end_date = Carbon::parse($end_date, 'Europe/Amsterdam')
                ->endOfDay()
                ->setTimezone('UTC');

            $query = Order::select(
                'order_id',
                'orders.created_at',
                'customer_name',
                'franchises_name',
                'dp_name',
                'order_channel_order_id',
                'order_payment_status',
                'order_status',
                'order_id',
                'order_cancelled_reason',
                'os_name',
                'order_final_with_discount',
                'channel_id',
                'channel_image',
                'channel_name'
            )
                ->leftJoin('franchises', function ($join) {
                    $join->on('franchises.id', '=', 'orders.franchise_id');
                })->leftJoin('customers', function ($join) {
                    $join->on('customers.customer_id', '=', 'orders.order_customerid');
                })->leftJoin('deliveryperson', function ($join) {
                    $join->on('deliveryperson.dp_id', '=', 'orders.od_deliverypersonid');
                })->leftJoin('channel', function ($join) {
                    $join->on('channel.channel_id', '=', 'orders.order_channel_id');
                })->join('order_status', 'order_status.os_id', 'orders.order_status')->whereNotIn('order_status', ['0', '11', '7', '8'])->whereNull('orders.deleted_at')
                ->where('franchise_id', $fid);


            $query = $query->where('orders.created_at', '>=', $start_date)
                ->where('orders.created_at', '<=', $end_date);
            // $query = $query->whereRaw("(orders.created_at between '" . $start_date . "' and '" . $end_date . "')");
            // $query = $query->whereBetween('orders.cre1ated_at', [$start_from, $end_to]);


            $data['total'] = $query->get()->count();
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
            $data['start_from_date'] = $start_display_date;
            $data['end_to_date'] = $end_date;
            $data['franchise_address'] = [
                'house_no_street' => $house_no_street,
                'block_no' => $block_no,
                'post_code' => $post_code,
                'residence' => $residence,
                'landmark' => $landmark,
                'franchise_name' => $fname,
                'royalty' => $royalty,
                'per_day_charges' => $per_day_charges,
                'franchises_no' => $franchises_no,
                'country' => $country
            ];
            $data['bank_account'] = $bank_account;

            if ($data['total'] > 0 || $row['start_from_date'] <= $today_date) {
                $data['image_url'] = storage_path('app/public/img/') . '247Drank.jpg';
                $invoicePdf = InvoicePdf::orderBy('id', 'DESC')->first();

                if (!empty($invoicePdf)) {
                    $explodeArray = explode('-', $invoicePdf->orderId);
                    $num = $explodeArray[1] + 1;
                    if ($num <= 9) {
                        $orderId = $explodeArray[0] . '-0' . $num;
                    } else {
                        $orderId = $explodeArray[0] . '-' . $num;
                    }
                } else {
                    $orderId = date('Y') . '-01';
                }
                $data['factuur_no'] = $orderId;
                $pdf = Pdf::loadView('pdf.invoicepdf', $data)->setPaper('a4', 'potrarit');

                // $filename = '247DRANK_nl_' . $franchises_no .'_'.$orderId.'_invoice_and_specifications_'.date('d-m-Y',strtotime($start_display_date)).'-'.date('d-m-Y',strtotime($end_date)).'.pdf';
                $filename = '247DRANK_nl_' . $franchises_no . '_' . $orderId . '_invoice_and_specifications.pdf';
                $pdf->stream($filename);


                $invoicePdf = new InvoicePdf();
                $invoicePdf->orderId = $orderId;
                $invoicePdf->from_date = $start_display_date;
                $invoicePdf->to_date = $end_date;
                $invoicePdf->amount = $total_order_amount;
                $invoicePdf->paid_amount = $online_pay_total;
                $invoicePdf->f_id = $fid;
                $invoicePdf->pdf_name = $filename;
                $invoicePdf->save();

                $pdf->save('uploads/generatepdf/' . $filename);

                $maildata['last_name'] = $row->last_name;
                $maildata['start_date'] = date('d-m-Y', strtotime($start_display_date));
                $maildata['end_date'] = date('d-m-Y', strtotime($end_date));
                $maildata['file'] = $filename;



                Mail::to($row->franchises_email)
                    ->bcc(['legal@247drank.nl', 'finance@247drankinternational.nl', '428v1w54@inkoop.exactonline.nl', 'mital.vekariya@nexuslinkservices.in', 'hemangi.vekariya@nexuslinkservices.in', 'binal.dholakiya@nexuslinkservices.in'])
                    ->send(new InvoiceFranchise($maildata));
            }

            sleep(60);
        }
        return true;
    }
}
