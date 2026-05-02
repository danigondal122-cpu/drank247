<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;

use App\Mail\FranchiseCrendential;

use App\Models\Franchise;
use App\Models\Pool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FranchiseController extends Controller
{
  public function adminIndex()
  {
    $data['pool'] = Pool::whereNull('deleted_at')->get();
    return view('admin.franchise.list', $data);
  }

  public function adminCreate()
  {
    $data['row'] = [];
    $data['pool'] = Pool::whereNull('deleted_at')->get();
    return view('admin.franchise.create', $data);
  }

  public function adminEdit($id)
  {
    $data['row'] = [];
    $data['pool'] = Pool::whereNull('deleted_at')->get();

    if ($id) {
      // Mengambil franchise berdasarkan ID
      $data['row'] = Franchise::findOrFail($id);

      // Mengambil pool yang terkait dengan franchise
      $data['poolarray'] = $data['row']->pools->pluck('id')->toArray(); // Mengambil ID pool yang terkait
    } else {
      $data['poolarray'] = []; // Jika tidak ada ID, set poolarray ke array kosong
    }

    return view('admin.franchise.create', $data);
  }

  public function getList(Request $request)
  {
    /**
     * @var Builder|QueryBuilder|Franchise $query Mengambil query dasar menggunakan Eloquent
     */
    $query = Franchise::with('pools') // Eager load pools
      ->select("franchises.*")
      ->groupBy("franchises.id")
      ->orderBy("franchises.fs_on_off", "ASC")
      ->orderBy("franchises.franchises_name", "ASC");

    // Filter berdasarkan franchise ID
    if ($frsId = $request->get('frs_id')) {
      $query->whereRelation('pools', 'id', $frsId);
    }

    // Kolom untuk pencarian dan pengurutan
    $column_order = ['franchises_name', 'franchises_no', 'area', 'franchises_email', 'franchises_username', 'mobile_no', 'post_code'];
    $column_search = ['franchises_name', 'franchises_email', 'franchises_username'];

    // Pencarian
    if ($searchValue = $request->search['value']) {
      $query->where(function ($q) use ($searchValue, $column_search) {
        foreach ($column_search as $index => $column) {
          $q->orWhere($column, 'LIKE', "%{$searchValue}%");
        }
      });
    }

    // Pengurutan
    if (isset($request->order[0]['column'])) {
      $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
    }

    // Menghitung total dan mengambil data
    $total = $query->count();
    $data = $query->skip($request->start)->take($request->length)->get();

    // Menambahkan area dari pools ke dalam data
    foreach ($data as $franchise) {
      $franchise->poolareas = $franchise->pools->pluck('area')->implode(', ');
    }

    return response()->json([
      'data' => $data,
      'total' => $total
    ]);
  }

  public function save(Request $request)
  {
    $rules = [
      'franchise_name' => 'required',
      'franchise_email' => 'required|email|unique:franchises,franchises_email,' . $request->franchise_email . ',franchises_email',
      'franchise_username' => 'required|unique:franchises,franchises_username,' . $request->franchise_username . ',franchises_username',
      'first_name' => 'required',
      'last_name' => 'required',
      'house_no_street' => 'required',
      'post_code' => 'required',
      'franchise_pool' => 'required',
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
      $token = Str::random(6);
      if ($request->id) {
        $message = 'Franchises succesvol bijgewerkt';
        $franchise = Franchise::find($request->id);
      } else {
        $message = 'Franchises succesvol toegevoegd';
        $franchise = new Franchise();
        $franchise->franchises_no = $this->getLastFranchiseNo();
        $franchise->password = bcrypt($token);
      }

      $date_of_birth = str_replace('/', '-',  $request->input('date_of_birth'));
      // $start_from_date=str_replace('/', '-',  $request->input('start_from_date'));
      $franchise->franchises_name = $request->input('franchise_name');
      $franchise->franchises_email = $request->input('franchise_email');
      $franchise->franchises_username = $request->input('franchise_username');
      $franchise->first_name = $request->input('first_name');
      $franchise->last_name = $request->input('last_name');
      $franchise->mobile_no = $request->input('mobile_no');
      $franchise->date_of_birth = $request->input('date_of_birth');
      $franchise->company_name = $request->input('company_name');
      $franchise->house_no_street = $request->input('house_no_street');
      $franchise->block_no = $request->input('block_no');
      $franchise->residence = $request->input('residence');
      $franchise->landmark = $request->input('landmark');
      $franchise->post_code = $request->input('post_code');
      $franchise->bank_account = $request->input('bank_account');
      $franchise->per_day_charges = $request->input('per_day_charges');
      $franchise->royalty = $request->input('royalty');
      $franchise->franchise_number = $request->input('franchise_number');
      $franchise->city = $request->input('city');
      $franchise->country = $request->input('country');
      $franchise->start_from_date = $request->input('start_from_date');
      $franchise->save();
      $franchise->pools()->sync($request->input('franchise_pool'));

      if ($franchise) {
        $franchise_data = [
          'franchises_name' => $request->input('franchise_name'),
          'franchises_email' => $request->franchise_email,
          'password' => $token,
        ];

        if ($request->id == "") {
          Mail::to($request->franchise_email)
            ->send(new FranchiseCrendential($franchise_data));
        }
      }

      return response()
        ->json([
          'status' => true,
          'msg' => $message,
          'page' => 'admin/franchise/list'
        ]);
    }
  }
  public function getLastFranchiseNo()
  {
    $latest  = Franchise::latest()->first();
    if ($latest) {
      return $latest->franchises_no + 1;
    } else {
      return 1;
    }
  }

  public function deleteFrenchise(Request $request)
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
      Franchise::where('id', $request->id)->delete();
      return response()
        ->json([
          'status' => true,
          'msg' => 'franchise deleted !',
        ]);
    }
  }
  public function getDocument(Request $request)
  {
    $id = $request->id;
    $data['detail'] = Franchise::select(['id', 'franchises_name', 'bank_pass_no', 'bank_pass_front', 'bank_pass_back', 'statement_conduct', 'licence_front', 'licence_back', 'franchise_contract', 'extra_option', 'payroll_contract'])->find($id);
    return view('modal.franchiseDocumentlist', $data);
  }

  public function updateonoff(Request $request)
  {
    Franchise::where('id', $request->id)->update(['fs_on_off' => $request->value]);
    return response()
      ->json([
        'status' => true,
        'msg' => 'Updated!!',
        'page' => '',
      ]);
  }
}
