<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPdfController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function downloadPDF($id)
  {

    $data['order'] = Order::leftJoin('franchises', function ($join) {
      $join->on('franchises.id', '=', 'orders.franchise_id');
    })->leftJoin('customers', function ($join) {
      $join->on('customers.customer_id', '=', 'orders.order_customerid');
    })->leftJoin('address', function ($join) {
      $join->on('address.address_id', '=', 'orders.order_address_id');
    })->leftJoin('deliveryperson', function ($join) {
      $join->on('deliveryperson.dp_id', '=', 'orders.od_deliverypersonid');
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

    $pdf = Pdf::loadView('pdf.orderpdf', $data)
      ->setPaper('a4', 'potrarit');
    return $pdf->stream('order_' . $id . '.pdf');
    $filename = 'order_' . $id . '.pdf';
    $pdf->save('uploads/generatepdf/' . $filename);


    $maildata['name'] = $data['order']['customer_name'];
    $maildata['email'] = $data['order']['customer_email'];
    $maildata['file'] = 'order_' . $id . '.pdf';

    // Mail::to($data['order']['customer_email'])
    //     ->send(New OrderDelivered($maildata));

  }
}
