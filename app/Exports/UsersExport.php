<?php

namespace App\Exports;

use App\Models\Franchise;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromView, ShouldAutoSize, WithColumnWidths, WithStyles
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
        $query = Order::select([
            'orders.id',
            'orders.od_start_time',
            'orders.od_end_time',
            'delivery_people.dp_name',
        ])
            ->leftJoin('franchises', function ($join) {
                $join->on('franchises.id', '=', 'orders.franchise_id');
            })->leftJoin('delivery_people', function ($join) {
                $join->on('delivery_people.id', '=', 'orders.delivery_person_id');
            })->join('order_statuses', 'order_statuses.id', 'orders.order_status')
            ->whereNull('orders.deleted_at')

            ->where('franchise_id', $data['f_id'])
            ->where('order_status', '10');

        if ($data['delivery_id'] != '') {
            $query = $query->where('orders.delivery_person_id', $data['delivery_id']);
        }
        if ($data['date'] != '') {

            $explode = explode('-', $data['date']);
            $startdate = str_replace('/', '-', $explode[0]);
            $enddate = str_replace('/', '-', $explode[1]);
            $startdate = date('Y-m-d', strtotime($startdate));
            $enddate = date('Y-m-d', strtotime($enddate));
            $query = $query->whereBetween('od_starttime', [$startdate, $enddate]);
        }
        $query = $query->get();
        $f_name = Franchise::find($data['f_id']);
        $totalHours = 0;
        foreach ($query as $key => $value) {
            $totalHours += $value['TotalOrderTimeINM'];
        }

        return view('exports.invoices', [
            'invoices'   => $query,
            'franchise'  => $f_name['franchises_name'],
            'TotalHours' => gmdate('H:i:s', $totalHours),
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'c' => 20,
            'D' => 20,
            'E' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            1    => ['font' => ['bold' => true]],
            1    => ['font' => ['italic' => true]],
            1    => ['font' => ['size' => 16]],
            2    => ['font' => ['size' => 14]],
        ];
    }
}
