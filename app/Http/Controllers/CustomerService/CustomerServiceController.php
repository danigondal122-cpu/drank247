<?php

namespace App\Http\Controllers\CustomerService;

use App\Mail\CustomerLoginEmail;
use App\Models\CustomerService;
use App\Models\CustomerServiceHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CustomerServiceController extends CSNotificationController
{
    public function adminDashboard()
    {
        return view('customerservice.dashboard');
    }

    public function adminIndex()
    {
        return view('admin.customerservice.list');
    }

    public function adminCreate()
    {
        $data['row'] = [];

        return view('admin.customerservice.create', $data);
    }

    public function adminEdit($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = CustomerService::findOrFail($id);
        }

        return view('admin.customerservice.create', $data);
    }

    public function getList(Request $request)
    {

        $query = CustomerService::whereNull('deleted_at');
        $column_order = ['cs_name', 'cs_email', 'cs_mobileno']; //set column field database for datatable orderable
        $column_search = ['cs_name', 'cs_email', 'cs_mobileno']; //set column field database for datatable searchable
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
            'name'      => 'required',
            'email'     => 'required|email|unique:customer_services,cs_email,'.$request->id.',id',
            'mobile_no' => 'required',
            'phone'     => 'required',
            'street'    => 'required',
            'city'      => 'required',
            'state'     => 'required',
            'postcode'  => 'required',
        ];

        // if ($request->id != "") {
        //   $rules['email'] = "required|email|unique:customerservices,cs_email,".$request->id.',id';
        // }
        if ($request->hasFile('image_file')) {
            $rules['image_file'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        } else {

            $token = Str::random(6);
            if ($request->id) {
                $message = 'Customer succesvol bijgewerkt';
                $customer = CustomerService::find($request->id);
            } else {
                $message = 'Customer succesvol toegevoegd';
                $customer = new CustomerService;
                $customer->password = bcrypt($token);
            }

            $customer->cs_name = $request->input('name');
            $customer->cs_email = $request->input('email');
            $customer->cs_mobileno = $request->input('mobile_no');
            $customer->cs_phone = $request->input('phone');
            $customer->cs_street = $request->input('street');
            $customer->cs_city = $request->input('city');
            $customer->cs_state = $request->input('state');
            $customer->cs_postcode = $request->input('postcode');
            if ($request->hasFile('image_file')) {
                $image = $request->file('image_file');
                $imagename = time().'_'.$image->getClientOriginalName();
                $img = Image::read($image->path());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads/customerserviceprofile/thumb').'/'.$imagename);
                $destinationPath = public_path('uploads/customerserviceprofile/');
                $image->move(public_path('uploads/customerserviceprofile/'), $imagename);

                $customer->cs_image = $imagename;
            }
            if ($request->input('old_cat_pic') == '' && ! $request->hasFile('image_file')) {
                $customer->cs_image = '';
            }
            $customer->save();
            if ($request->id == '') {
                if ($customer) {
                    $maildata = [];
                    $maildata['name'] = $customer->cs_name;
                    $maildata['email'] = $customer->cs_email;
                    $maildata['password'] = $token;
                    Mail::to($request->email)
                        ->send(new CustomerLoginEmail($maildata));
                }
            }

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'admin/customerservice/list',
                ]);
        }
    }

    public function deleteCustomer(Request $request)
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
            CustomerService::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Customer Services deleted !',
                ]);
        }
    }

    public function getDocument(Request $request)
    {
        $id = $request->id;
        $data['detail'] = CustomerService::select(['id', 'cs_name', 'bank_pass_no', 'bank_pass_front', 'bank_pass_back', 'statement_conduct', 'licence_front', 'licence_back', 'franchise_contract', 'extra_option', 'payroll_contract'])->find($id);

        return view('modal.customerServiceDocumentlist', $data);
    }

    public function hoursList($id)
    {
        $Servicesdetail = CustomerService::find($id);
        $data['name'] = $Servicesdetail['name'];
        $data['id'] = $id;
        $data['row'] = CustomerServiceHour::whereNull('deleted_at')->get();

        return view('admin.hours.list', $data);
    }

    public function getHoursList(Request $request)
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
}
