<?php

namespace App\Http\Controllers\CustomerService;

use App\Models\CustomerServiceHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CSHoursController extends CSNotificationController
{
    public function hoursList()
    {
        $data['row'] = CustomerServiceHour::whereNull('deleted_at')->get();

        return view('customerservice.hours.list', $data);
    }

    public function getList(Request $request)
    {
        $query = CustomerServiceHour::whereNull('deleted_at'); //$request->get('cat_id')
        if ($request->get('id') && $request->get('id') != '') {
            $query = $query->where('id', $request->get('id'));
        }
        $column_order = ['start_date', 'end_date', 'start_time', 'end_time']; //set column field database for datatable orderable
        $column_search = ['start_date', 'end_date', 'start_time', 'end_time']; //set column field database for datatable searchable
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
            $query = $query->orderBy('id', 'DESC');
        }

        $total = $query->get()->count();
        $hours = $query->sum('total_hours_in_minute');
        $data = $query->skip($start_from)->limit($per_page)->get();

        return response()
            ->json([
                'data'       => $data,
                'total'      => $total,
                'TotalHours' => gmdate('H:i:s', $hours),
            ]);
    }

    public function hoursAdd()
    {
        $data['row'] = [];

        return view('customerservice.hours.create', $data);
    }

    public function save(Request $request)
    {

        $cs_id = auth('customer_service')->user()->id;
        $rules = [
            'start_date' => 'required',
            'end_date'   => 'required|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ];
        $customMessages = [
            'end_time.after' => 'The end time must be a time after start time.',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {

            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        } else {
            if ($request->id) {
                $message = 'Hours succesvol bijgewerkt';
                $hours = CustomerServiceHour::findOrFail($request->id);
            } else {
                $message = 'Hours succesvol toegevoegd';
                $hours = new CustomerServiceHour;
            }

            /** @var CustomerServiceHour $hours */
            $start_date = str_replace('/', '-', $request->input('start_date'));
            $end_date = str_replace('/', '-', $request->input('end_date'));
            $hours->customer_service_id = $cs_id;
            $hours->start_date = date('Y-m-d', strtotime($start_date));
            $hours->end_date = date('Y-m-d', strtotime($end_date));
            $hours->start_time = $request->input('start_time');
            $hours->end_time = $request->input('end_time');

            //get total day
            $to = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($start_date)));
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($end_date)));
            $diff_in_days = $to->diffInDays($from);
            // get hours
            $per_hours = gmdate('H:i', Carbon::parse($request->input('end_time'))->diffInSeconds($request->input('start_time')));
            $per_hours_inM = Carbon::parse($request->input('end_time'))->diffInSeconds($request->input('start_time'));

            //get total hours
            $total_hours_in_minute = $per_hours_inM * ($diff_in_days + 1);
            $total_hours_inH = gmdate('H:i:s', $total_hours_in_minute);

            $hours->per_hours = $per_hours;
            $hours->per_hours_in_minute = $per_hours_inM;
            $hours->total_hours_in_minute = $total_hours_in_minute;
            $hours->total_hours_in_hour = $total_hours_inH;

            $hours->save();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'customer_service/hours/list',
                ]);
        }
    }

    public function hoursEdit($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = CustomerServiceHour::findOrFail($id);
        }

        return view('customerservice.hours.create', $data);
    }

    public function deleteHour(Request $request)
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
            CustomerServiceHour::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Hours deleted !',
                ]);
        }
    }
}
