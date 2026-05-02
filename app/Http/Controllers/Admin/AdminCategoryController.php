<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.category.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['row'] = [];
        $data['categories'] = Category::whereNull('category_id')->orderBy('category_order', 'ASC')->get(['category_name', 'id']);
        $data['productTypes'] = ProductType::all(['id', 'product_type']);

        return view('admin.category.create', $data);
    }

    /**
     * Save a newly created/edited resource in storage.
     */
    public function save(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|unique:categories,category_name,'.$request->id.',category_id',
            'description'     => 'required',
            'category_parent' => 'nullable|exists:'.Category::class.',id',
            'category_id'     => 'nullable|exists:'.Category::class.',id',
            'image_file.*'    => 'file|mimes:jpeg,png,jpg,svg|max:2048',
            'product_type_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        }

        $maxValue = Category::orderBy('category_order', 'desc')->value('category_order');

        if ($request->category_id) {
            $message = 'Categorie succesvol bijgewerkt';
            /** @var Category $category */
            $category = Category::findOrFail($request->category_id);
        } else {
            $message = 'Categorie succesvol toegevoegd';
            $category = new Category;
            $category->category_order = $maxValue + 1;
        }

        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $imagename = time().'_'.$image->getClientOriginalName();
            $img = Image::read($image->path());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/category/thumb').'/'.$imagename);
            $image->move(public_path('uploads/category/'), $imagename);
            $category->image = $imagename;
        }

        $category->category_name = $request->input('name');
        $category->description = $request->input('description');
        $category->category_id = $request->input('category_parent') == '' ? null : $request->input('category_parent');
        $category->is_show = ($request->input('is_show') == 'on');
        $category->product_type_id = $request->input('product_type_id');

        if ($request->input('old_cat_pic') == '' && ! $request->hasFile('image_file')) {
            $category->image = '';
        }

        $category->save();

        return response()
            ->json([
                'status' => true,
                'msg'    => $message,
                'page'   => ($request->input('category_parent') != '') ? 'admin/category/subcategorylist/'.$request->input('category_parent') : 'admin/category/list',
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        $data['row'] = $category;
        $data['categories'] = Category::whereNull('category_id')
            ->where('id', '!=', $category->id)
            ->orderBy('category_order', 'ASC')
            ->get(['category_name', 'id']);
        $data['productTypes'] = ProductType::all(['id', 'product_type']);

        return view('admin.category.create', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        DB::enableQueryLog();
        $category->delete();
        DB::commit();

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Category deleted !',
            ]);
    }

    public function updateCategoryOrder(Request $request): JsonResponse
    {
        foreach ($request->category as $key => $value) {
            $order = $key + 1;
            Category::where('id', $value)->update(['category_order' => $order]);
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Category Ordered !',
            ]);
    }

    public function getList(Request $request): JsonResponse
    {
        $query = Category::whereNull('category_id');
        $column_order = ['id', 'category_name']; //set column field database for datatable orderable
        $column_search = ['category_name']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('category_order', 'ASC');
        }

        $total = $query->get()->count();
        // $data = $query->skip($start_from)->limit($per_page)->get();
        $data = $query->get();

        foreach ($data as $key => $value) {
            $data[$key]['category_rowid'] = 'category_'.$data[$key]['id'];
        }

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function subCategoryList(Category $category): View
    {
        $data['main_category'] = $category;
        $data['count'] = Category::where('category_id', $category->id)->count();

        return view('admin.category.sublist', $data);
    }

    public function getSubList(Request $request): JsonResponse
    {
        $query = Category::where('category_id', $request->main_category);
        $column_order = ['id', 'category_name']; //set column field database for datatable orderable
        $column_search = ['category_name']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        //Search
        if ($request->search['value'] && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->whereAny($column_search, 'LIKE', "%$search%");
        }
        //Sorting
        if (isset($request->order[0]['column']) && $request->order[0]['column'] != '') {
            $query = $query->orderBy($column_order[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query = $query->orderBy('category_order', 'ASC');
        }

        $total = $query->get()->count();
        // $data = $query->skip($start_from)->limit($per_page)->get();
        $data = $query->get();

        foreach ($data as $key => $value) {
            $data[$key]['category_rowid'] = 'category_'.$data[$key]['id'];
        }

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function assignProduct(Category $category): View
    {
        $data['product'] = Product::where('product_type', 1)->get();
        $data['assigned_category'] = DB::table('category_product')->where('category_id', $category->id)->pluck('product_id')->toArray();
        $data['main_category'] = $category;

        return view('admin.category.assignproduct', $data);
    }

    public function assignProductSave(Request $request): JsonResponse
    {
        $category_id = $request->category_id;
        $category_detail = Category::findOrFail($category_id);
        DB::table('category_product')->where('category_id', $category_id)->delete();
        $to_tredirectid = $category_detail['category_id'] ? $category_detail['category_id'] : $category_id;
        $extra_productids = explode(',', $request->extra_productarray);

        DB::table('category_product')->insert(array_map(function ($value) use ($category_id) {
            return [
                'product_id'  => $value,
                'category_id' => $category_id,
            ];
        }, $extra_productids));

        return response()
            ->json([
                'status' => true,
                'page'   => 'admin/category/subcategorylist/'.$to_tredirectid,
                'msg'    => 'Product Assigned!!',
            ]);
    }
}
