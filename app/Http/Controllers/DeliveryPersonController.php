<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPerson;
use App\Models\DeliveryTimeSchedule;
use App\Models\Franchise;
use App\Models\Pool;
use App\Models\Setting;
use App\Models\SubDeliveryPerson;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class DeliveryPersonController extends Controller
{
  public function adminCreate()
  {
    $data['row'] = [];
    $data['pool'] = Pool::whereNull('deleted_at')->get();
    return view('admin.deliveryperson.create', $data);
  }

  public function adminEdit($id)
  {
    $data['row'] = [];
    if ($id) {
      $data['row'] = DeliveryPerson::findOrFail($id);
    }
    $data['pool'] = Pool::whereNull('deleted_at')->get();
    $poolarray = $data['row']['dp_pool'];
    $data['poolarray'] = explode(',', $poolarray);
    return view('admin.deliveryperson.create', $data);
  }

  public function adminIndex()
  {
    $data['Franchise'] = Franchise::whereNull('deleted_at')->get();
    return view('admin.deliveryperson.list', $data);
  }

  public function deliveryPersonMap()
  {
    $data['delivery'] = DeliveryPerson::whereNull('deleted_at')->get(['dp_name', 'id', 'dp_lat', 'dp_lng']);
    return view('admin.deliveryperson.map', $data);
  }

  public function DeliveryTimeschedule()
  {
    $data['time'] = DeliveryTimeSchedule::get()->all();
    $data['time_schedule'] = Setting::find('1');
    return view('admin.deliveryperson.schedule', $data);
  }

  public function getList(Request $request)
  {
    // Initialize the query with Eloquent relationships
    $query = DeliveryPerson::with('subDeliveryPeople.franchise') // Eager load related models
      ->whereHas('subDeliveryPeople', function ($q) {
        $q->whereNull('sub_delivery_people.deleted_at');
      });

    // Filter by franchise ID if provided
    if ($request->filled('frs_id')) {
      $query->whereHas('subDeliveryPeople', function ($q) use ($request) {
        $q->where('sub_delivery_people.franchise_id', $request->frs_id);
      });
    }

    // Define columns for ordering and searching
    $column_order = ['dp_name', 'dp_email', 'dp_contact_no']; // Set column fields for ordering
    $column_search = ['dp_name', 'dp_email', 'dp_contact_no', 'dp_city']; // Set column fields for searching

    // Get pagination parameters with defaults
    $start_from = $request->start ?? 0; // Default to 0 if not set
    $per_page = $request->length ?? 10; // Default to 10 if not set

    // Search functionality
    if ($request->has('search') && !empty($request->search['value'])) {
      $search = $request->search['value'];
      $query->where(function ($q) use ($search, $column_search) {
        foreach ($column_search as $column) {
          $q->orWhere($column, 'LIKE', "%$search%");
        }
      });
    }

    // Sorting functionality
    if ($request->has('order') && isset($request->order[0]['column'])) {
      $columnIndex = $request->order[0]['column'];
      if (isset($column_order[$columnIndex])) {
        $query->orderBy($column_order[$columnIndex], $request->order[0]['dir'] ?? 'asc');
      }
    } else {
      $query->orderBy('delivery_people.id', 'DESC'); // Specify the table for the id column
    }

    // Get total count and paginated data
    $total = $query->count(); // Get total count
    $data = $query->skip($start_from)->take($per_page)->get(); // Get paginated data

    return response()->json([
      'data' => $data,
      'total' => $total,
    ]);
  }

  public function save(Request $request)
  {

    $rules = [
      'name' => 'required',
      'email' => "required|email|unique:delivery_people,dp_email," . $request->id . ',id',
      'contact_no' => 'required',
      'street' => 'required',
      'city' => 'required',
      'state' => 'required',
      'postcode' => 'required',
      // 'pool' => 'required',
    ];
    if ($request->hasFile('image_file')) {
      $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
    }
    // if ($request->dp_id != "") {
    //   $rules['email'] = "required|email|unique:deliveryperson,dp_email," . $request->dp_id . ',dp_id';
    // }
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
    dd('ss');

      return response()
        ->json([
          'status' => false,
          'type' => 'validation',
          'errors' => $validator->errors()
        ]);

    } else {
      if ($request->id) {
        $message = 'Delivery succesvol bijgewerkt';
        $delivery = DeliveryPerson::find($request->id);

        $delivery->dp_name = $request->input('name');
        $delivery->dp_email = $request->input('email');
        $delivery->dp_contact_no = $request->input('contact_no');
        $delivery->dp_street = $request->input('street');
        $delivery->dp_city = $request->input('city');
        $delivery->dp_state = $request->input('state');
        $delivery->dp_postcode = $request->input('postcode');
        // $delivery->dp_pool = implode(',',$request->input('pool'));
        if ($request->hasFile('image_file')) {
          $image = $request->file('image_file');
          $imagename = time() . '_' . $image->getClientOriginalName();
          $img = Image::read($image->path());
          $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
          })->save(public_path('uploads/deliveryperson/thumb') . '/' . $imagename);
          $image->move(public_path('uploads/deliveryperson/'), $imagename);
          $delivery->dp_image = $imagename;
        }
        $delivery->save();


        return response()
          ->json([
            'status' => true,
            'msg' => $message,
            'page' => 'admin/deliveryperson/list'
          ]);
      }
    }
  }
  public function deleteDelivery(Request $request)
  {
    $rules = [
      'id' => 'required',
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'validation',
          'errors' => $validator->errors()
        ]);
    } else {
      DeliveryPerson::where('id', $request->id)->delete();
      SubDeliveryPerson::where('delivery_person_id', $request->id)->delete();
      return response()
        ->json([
          'status' => true,
          'msg' => 'Delivery  deleted !',
        ]);
    }
  }
  public function getStartTime(Request $request)
  {

    $time = $request->time;
    if ($request->type == "start0") {
      DeliveryTimeSchedule::where('id', $request->id)->update(['start_time_0' => $time]);
    } elseif ($request->type == "start1") {
      DeliveryTimeSchedule::where('id', $request->id)->update(['start_time_1' => $time]);
    } elseif ($request->type == "end0") {
      DeliveryTimeSchedule::where('id', $request->id)->update(['end_time_0' => $time]);
    } else {
      DeliveryTimeSchedule::where('id', $request->id)->update(['end_time_1' => $time]);
    }
  }
  public function getChecked(Request $request)
  {

    $value = $request->value;
    DeliveryTimeSchedule::where('id', $request->id)->update(['is_checked' => $value]);
  }
  public function scheduleOnOff(Request $request)
  {
    $value = $request->value;
    Setting::where('id', '1')->update(['time_schedule' => $value]);
  }

  public function getFranchises(Request $request)
  {
    $id = $request->id;

    // Cari data SubDeliveryPerson berdasarkan delivery_person_id
    $delivery = DeliveryPerson::with(['subDeliveryPeople.pools', 'subDeliveryPeople.franchise'])->find($id);

    if (!$delivery) {
      return response()->json(['error' => 'Delivery person not found'], 404);
    }

    // Kumpulkan data franchises dan poolareas
    $list = $delivery->subDeliveryPeople->map(function ($subDeliveryPerson) {
      $pools = $subDeliveryPerson->pools;

      return [
        'poolareas' => $pools->pluck('area')->implode(', '), // Gabungkan area dari pools
        'franchises_name' => $subDeliveryPerson->franchise->franchises_name ?? null, // Ambil nama franchise
      ];
    });

    $data['list'] = $list;

    return view('modal.deliveryfranchiseslist', $data);
  }

  public function map(Request $request)
  {
    $data['delivery'] = DeliveryPerson::get(['dp_name', 'id', 'dp_lat', 'dp_lng']);

    return response()
      ->json([
        'status' => true,
        'data' => $data,
      ]);
  }
  public function getDocument(Request $request)
  {

    $id = $request->id;
    $data['detail'] = DeliveryPerson::select(['id', 'dp_name', 'bank_pass_no', 'bank_pass_front', 'bank_pass_back', 'statement_conduct', 'licence_front', 'licence_back', 'franchise_contract', 'extra_option', 'payroll_contract'])->find($id);

    return view('modal.deliveryDocumentlist', $data);
  }
  public function updateonoff(Request $request)
  {
    DeliveryPerson::where('id', $request->id)->update(['dp_onoff' => $request->value]);
    return response()
      ->json([
        'status' => true,
        'msg' => 'Updated!!',
        'page' => '',
      ]);
  }
}
