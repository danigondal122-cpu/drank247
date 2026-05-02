<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminLoginEmail;
use App\Models\Admin;
use App\Models\AdminModule;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function profile()
    {

        $data['row'] = Admin::findOrFail(auth('admin')->user()->id);

        return view('admin.auth.profile', $data);
    }

    public function changePassword()
    {
        return view('admin.auth.change_password');
    }

    public function adminAdd()
    {
        $data['row'] = [];
        $data['modules'] = Module::whereNull('deleted_at')->get();
        $data['assign_module'] = [];

        return view('admin.admin.create', $data);
    }

    public function adminEdit($id)
    {
        $data['row'] = [];
        if ($id) {
            $data['row'] = Admin::findOrFail($id);
        }

        $admin = Admin::with('modules')->find($id);
        if ($admin) {
            // Mengambil module_id dari modul yang terkait
            $data['assign_module'] = $admin->modules->pluck('id')->toArray();
        } else {
            $data['assign_module'] = []; // Jika admin tidak ditemukan, set assign_module ke array kosong
        }

        $data['modules'] = Module::whereNull('deleted_at')->get();

        return view('admin.admin.create', $data);
    }

    public function adminList()
    {
        $data['admins'] = Admin::paginate(1);
        $data['modules'] = Module::whereNull('deleted_at')->get();

        return view('admin.admin.list', $data);
    }

    public function getList(Request $request)
    {
        $query = Admin::where('admin_type', 'admin')->whereNull('deleted_at');
        // $query = Admin::select('admin.id','name','email','admin_mobile_no','is_accountant','assign_id','module_id','module_name')
        // ->leftjoin('assign_module', 'assign_module.id', 'admin.id')->leftjoin('module', 'module.id', 'assign_module.module_id')->where('admin.admin_type','admin')->whereNull('admin.deleted_at')->groupBy('admin.id');
        $column_order = ['name', 'email', 'admin_mobile_no']; //set column field database for datatable orderable
        $column_search = ['name', 'email', 'admin_mobile_no']; //set column field database for datatable searchable
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

        foreach ($data as $key => $row) {
            // Mengambil admin dengan relasi modul
            $admin = Admin::with('modules')->find($row->id);

            // Memeriksa apakah admin ditemukan
            if ($admin) {
                $modules = [];
                foreach ($admin->modules as $m) {
                    $modules[] = [
                        'assign_id'   => $m->pivot->id, // ID dari tabel pivot
                        'module_name' => $m->module_name,
                        'module_id'   => $m->id,
                    ];
                }
                $data[$key]['modules'] = $modules;
            } else {
                $data[$key]['modules'] = []; // Jika admin tidak ditemukan, set modules ke array kosong
            }
        }

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function save(Request $request)
    {
        // print_r($request->all());die;
        $rules = [
            'name'  => 'required',
            'email' => 'required|email|unique:admins,email,'.$request->id.',id',
            // 'mobile_no' => 'required',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'postcode' => 'required',
            // 'module' => 'required',
            // 'company' => 'required',
            // 'vat' => 'required',
            // 'commerce_number' => 'required',
            'module' => 'required',
        ];
        $customMessages = [
            'module.required' => 'Please Select atleast one module',
        ];
        if ($request->hasFile('image_file')) {
            $rules['image_file'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        $validator = Validator::make($request->all(), $rules, $customMessages);
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
                $message = 'Admin succesvol bijgewerkt';
                $admin = Admin::find($request->id);
            } else {
                $message = 'Admin succesvol toegevoegd';
                $admin = new Admin;
                $admin->password = bcrypt($token);
            }

            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $admin->admin_mobile_no = $request->input('mobile_no');
            $admin->admin_street = $request->input('street');
            $admin->admin_city = $request->input('city');
            $admin->admin_state = $request->input('state');
            $admin->admin_postcode = $request->input('postcode');
            $admin->admin_company = $request->input('company');
            $admin->admin_vat = $request->input('vat');
            $admin->admin_commerce_number = $request->input('commerce_number');
            if ($request->is_accountant) {
                $admin->is_accountant = $request->is_accountant;
            } else {
                $admin->is_accountant = 0;
            }
            if ($request->hasFile('image_file')) {
                $image = $request->file('image_file');
                $imagename = time().'_'.$image->getClientOriginalName();
                $img = image::read($image->path());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads/adminprofile/thumb').'/'.$imagename);
                $destinationPath = public_path('uploads/adminprofile/');
                $image->move(public_path('uploads/adminprofile/'), $imagename);

                $admin->image = $imagename;
            }
            if ($request->input('old_cat_pic') == '' && ! $request->hasFile('image_file')) {
                $admin->image = '';
            }
            $admin->save();
            $admin->modules()->sync($request->module);
            if ($request->id == '') {
                if ($admin) {
                    $maildata = [];
                    $maildata['name'] = $admin->name;
                    $maildata['email'] = $admin->email;
                    $maildata['password'] = $token;
                    Mail::to($request->email)
                        ->send(new AdminLoginEmail($maildata));
                }
            }

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'admin/admin/list',
                ]);
        }
    }

    public function updateAssignmodule(Request $request)
    {
        $rules = [
            'module' => 'required',
        ];
        $customMessages = [
            'module.required' => 'Please Select atleast one module',
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
            $admin = Admin::find($request->id);
            AdminModule::where('admin_id', $admin->id)->delete();
            if ($admin) {
                foreach ($request->module as $key => $row) {
                    $module = new AdminModule;
                    $module->admin_id = $admin->id;
                    $module->module_id = $row;
                    $module->save();
                }
            }
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => '',
                'page'   => 'admin/admin/list',
            ]);
    }

    public function showassignmodule(Request $request)
    {
        $admin_id = $request->input('id');
        $modules = Module::whereNull('deleted_at')->get();
        $assign_module = AdminModule::where('admin_id', $admin_id)->pluck('module_id')->Toarray();

        $html = '';
        foreach ($modules as $key => $value) {
            $checked = '';
            if (in_array($value->id, $assign_module)) {
                $checked = 'checked';
            }
            $html .= '<div class="col-md-4" style="display:inline-block">';
            $html .= '<label><input style="margin-right: 5px;" type="checkbox" name="module[]" value="'.$value->id.'" '.$checked.'><span>'.$value->module_name.'</span></label></div>';
        }

        //  echo $html;die;

        return response()
            ->json([
                'status' => true,
                'msg'    => '',
                'html'   => $html,
            ]);
    }

    public function changeaccountant(Request $request)
    {
        $admin = Admin::find($request->id);
        $value = $request->value;
        if ($value == 'YES') {
            $admin->is_accountant = 0;
            $admin->save();
        } else {
            $admin->is_accountant = 1;
            $admin->save();
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Changed',
                'data'   => $admin,
            ]);
    }

    public function deleteAdmin(Request $request)
    {
        // Validasi input
        $rules = [
            'id' => 'required|exists:admins,id', // Memastikan ID ada di tabel admins
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'type'   => 'validation',
                'errors' => $validator->errors(),
            ]);
        } else {
            // Mencari admin berdasarkan ID
            $admin = Admin::find($request->id);

            if ($admin) {
                // Menghapus penugasan modul yang terkait
                $admin->modules()->detach(); // Menghapus semua penugasan modul

                // Menghapus admin (soft delete)
                $admin->delete();

                return response()->json([
                    'status' => true,
                    'msg'    => 'Admin deleted successfully!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg'    => 'Admin not found!',
                ]);
            }
        }
    }
}
