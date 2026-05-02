<?php

namespace App\Exports;

use App\Models\Franchise;
use App\Models\Order;
use App\Models\Product;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements FromView, ShouldAutoSize, WithColumnWidths, WithStyles
{
  /** 
   * @return \Illuminate\Support\Collection
   */
  public $data;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($data)
  {
    // dd($data);
    $this->data = $data;
  }
  public function view(): View
  {
    $data = $this->data;
    $query = Order::select(
      'orders.id as order_id',
      'orders.created_at',
      'customer_name',
      'franchises_name',
      'dp_name',
      'orders.order_delivery_charge',
      'order_channel_order_id',
      'order_payment_status',
      'order_status',
      'order_statuses.id as os_id',
      'order_cancelled_reason',
      'os_name',
      'os_color',
      'order_final_with_discount',
      'channels.id as channel_id',
      'channel_image',
      'channel_name',
      'payment_method',
      'customers.customer_contact_no',
      'customers.customer_email',
      'order_address_id',
      'address',
      'customer_addresses.post_code',
      'house_no',
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
    })
      ->join('order_statuses', 'order_statuses.id', 'orders.order_status')
      ->where('order_status', '!=', '0')->whereNull('orders.deleted_at');

    if ($data['frs_id'] && $data['frs_id'] != '') {
      $query = $query->where('orders.franchise_id', $data['frs_id']);
    }

    if ($data['status'] && $data['status'] != '') {
      $query = $query->where('orders.order_status', $data['status']);
    }
    $query = $query->orderBy('order_id', 'DESC');
    $datalist = $query->get();
    foreach ($datalist as $key => $row) {
      $products = Product::join('order_details', 'products.id', 'order_details.product_id')->where('order_id', $row->order_id)->get();
      $product_price_0 = 0;
      $product_price_9 = 0;
      $product_price_21 = 0;
      foreach ($products as $key1 => $p) {
        if ($p['vat'] ==  0) {
          $product_price_0 += $p['vat_price'] * $p['od_qty'];
        } else if ($p['vat'] ==  9) {
          $product_price_9 += $p['vat_price'] * $p['od_qty'];
        } else if ($p['vat'] ==  21) {
          $product_price_21 += $p['vat_price'] * $p['od_qty'];
        }
      }
      $datalist[$key]['product_price_0'] = number_format($product_price_0, 2);
      $datalist[$key]['product_price_9'] = number_format($product_price_9, 2);
      $datalist[$key]['product_price_21'] = number_format($product_price_21, 2);
    }

    $f_name = Franchise::find($data['frs_id']);
    $is_accountant = auth('admin')->user()->is_accountant;
    return view('exports.order-list-export', [
      'orders' => $datalist,
      'is_accountant' => $is_accountant,
      'franchise' => $data['frs_id'] != '' ? $f_name['franchises_name'] : '',
    ]);
  }
  public function columnWidths(): array
  {
    return [
      'A' => 15,
      'B' => 20,
      'c' => 20,

    ];
  }
  public function styles(Worksheet $sheet)
  {
    return [

      1    => ['font' => ['bold' => true]],
      1    => ['font' => ['italic' => true]],
      1    => ['font' => ['size' => 16]],
      2    => ['font' => ['size' => 16]],
      3    => ['font' => ['size' => 12]],
    ];
  }
}
