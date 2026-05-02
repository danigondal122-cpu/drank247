<?php

namespace App\Exports;

use App\Models\Franchise;
use App\Models\DeliveryHistory;
use App\Models\Delivery;
use App\Models\DeliveryPerson;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryExport implements FromView, ShouldAutoSize, WithColumnWidths, WithStyles
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

        $query = DeliveryHistory::Select('delivery_people.*', 'delivery_histories.*')
            ->join('delivery_people', 'delivery_histories.delivery_person_id', 'delivery_people.id')
            ->where('history_end_time', '!=', '0000-00-00 00:00:00')
            ->where('end_odometer', '!=', '')
            ->where('delivery_histories.delivery_person_id', $data['delivery_id']);

        if ($data['date'] != '') {

            $explode = explode('-', $data['date']);
            $startdate = str_replace('/', '-', $explode[0]);
            $enddate = str_replace('/', '-', $explode[1]);
            $startdate = date("Y-m-d", strtotime($startdate));
            $enddate = date("Y-m-d", strtotime($enddate));
            $query = $query->WhereBetween('history_date', [$startdate, $enddate]);
        }
        $query = $query->get();
        $f_name = Franchise::find($data['f_id']);
        $d_name = DeliveryPerson::find($data['delivery_id']);
        $totalHours = 0;
        foreach ($query as $key => $value) {
            $totalHours += $value['TotalOrderTimeINM'];
        }
        return view('exports.history-hours', [
            'invoices' => $query,
            'franchise' => $f_name['franchises_name'],
            'deliveryperson' => $d_name['dp_name'],
            'TotalHours' => gmdate('H:i:s', $totalHours),
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
