<?php

namespace App\Http\Controllers\CustomerService;

use App\Mail\FranchiseCrendential;
use App\Models\Franchise;
use App\Models\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CSFranchiseController extends CSNotificationController
{
    public function franchiseList()
    {
        $data['pool'] = Pool::all(['id', 'area']);

        return view('customerservice.franchise.list', $data);
    }

    public function getList(Request $request)
    {
        $query = Franchise::with('pools'); // Gunakan Eloquent untuk memuat relasi pools

        // Filter berdasarkan franchise pool ID
        if ($request->filled('frs_id')) {
            $query->whereHas('pools', function ($q) use ($request) {
                $q->where('pools.id', $request->get('frs_id'));
            });
        }

        $column_order = ['franchises_name', 'franchises_no', 'area', 'franchises_email', 'franchises_username', 'mobile_no', 'post_code']; // Set kolom untuk sorting
        $column_search = ['franchises_name', 'franchises_email', 'franchises_username']; // Set kolom untuk searching
        $start_from = $request->start;
        $per_page = $request->length;

        // Search
        if (! empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($column_search, $search) {
                foreach ($column_search as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }

        // Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] !== '') {
            $column_index = $request->order[0]['column'];
            $sort_direction = $request->order[0]['dir'];
            $query->orderBy($column_order[$column_index], $sort_direction);
        } else {
            $query->orderBy('franchises_name', 'ASC'); // Default sorting
        }

        $total = $query->count();

        // Ambil data dengan paginasi
        $franchises = $query->skip($start_from)->take($per_page)->get();

        // Tambahkan poolareas ke setiap franchise
        $data = $franchises->map(function ($franchise) {
            $franchise->poolareas = $franchise->pools->pluck('area')->implode(', '); // Gabungkan area dari pools

            return $franchise;
        });

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    public function franchiseAdd()
    {
        $data['row'] = [];
        $data['pool'] = Pool::all(['id', 'area']);

        return view('customerservice.franchise.create', $data);
    }

    public function franchiseEdit(Franchise $id)
    {
        $data['row'] = $id;
        $data['pool'] = Pool::all(['id', 'area']);
        $data['poolarray'] = $id->pools->pluck('id')->toArray(); // Mengambil ID pool yang terkait

        return view('customerservice.franchise.create', $data);
    }

    public function save(Request $request)
    {
        $rules = [
            'franchises_name'     => 'required',
            'franchises_email'    => 'required|email|unique:franchises,franchises_email',
            'franchises_username' => 'required|unique:franchises,franchises_username',
            'first_name'          => 'required',
            'last_name'           => 'required',
            'house_no_street'     => 'required',
            'post_code'           => 'required',
            'per_day_charges'     => 'required',
            'royalty'             => 'required',
        ];
        if ($request->franchise_id) {
            $rules = ['franchises_email' => 'required|email,unique:franchises,franchises_email,'.$request->franchises_email.',franchises_email'];
            $rules = ['franchises_username' => 'required|unique:franchises,franchises_username,'.$request->franchises_username.',franchises_username'];
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        }

        $token = Str::random(6);
        if ($request->franchise_id) {
            $message = 'Franchises succesvol bijgewerkt';
            $franchise = Franchise::findOrFail($request->franchise_id);
        } else {
            $message = 'Franchises succesvol toegevoegd';
            $franchise = new Franchise;
            $franchise->franchises_no = $this->getLastFranchiseNo();
            $franchise->password = bcrypt($token);
        }
        /** @var Franchise $franchise */
        $franchise->franchises_name = $request->input('franchises_name');
        $franchise->franchises_email = $request->input('franchises_email');
        $franchise->franchises_username = $request->input('franchises_username');
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

        $franchise->save();
        $franchise->pools()->sync($request->input('franchise_pool'));
        if ($franchise->wasRecentlyCreated) {
            $franchise_data = [
                'franchises_name'  => $request->input('franchises_name'),
                'franchises_email' => $request->input('franchises_email'),
                'password'         => $token,
            ];
            Mail::to($request->input('franchises_email'))
                ->send(new FranchiseCrendential($franchise_data));
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => $message,
                'page'   => 'customer_service/franchise/list',
            ]);
    }

    public function getLastFranchiseNo()
    {
        $latest = Franchise::latest()->first();
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
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        }
        Franchise::where('id', $request->id)->delete();

        return response()
            ->json([
                'status' => true,
                'msg'    => 'franchise deleted !',
            ]);
    }
}
