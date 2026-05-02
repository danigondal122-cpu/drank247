<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\AssignAllergen;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;

class AdminExtraProductController extends Controller
{
    public function create()
    {
        $data['row'] = [];
        $stockProduct = StockProduct::wherenull('deleted_at')->get();
        $data['allergense'] = Allergen::wherenull('deleted_at')->get();
        $data['product_type_id'] = ProductType::wherenull('deleted_at')->get();

        $htmlProduct = '';
        foreach ($stockProduct as $val) {
            $htmlProduct .= '<option value="'.preg_replace('/&?[a-z0-9]+;/i', '', $val->id.'#'.$val->name).'">'.$val->name.'</option>';
        }
        $data['htmlProduct'] = $htmlProduct;

        return view('admin.extraproduct.create', $data);
    }

    public function edit($id)
    {
        $data['row'] = [];

        if ($id) {
            $product = Product::find($id);
            $data['row'] = Product::findOrFail($id);
        }
        $data['allergense'] = Allergen::wherenull('deleted_at')->get();

        $data['product_type_id'] = ProductType::wherenull('deleted_at')->get();
        // $data['allergense_array'] = AssignAllergen::where('product_id', $id)->pluck('allergen_id')->toarray();
        if ($product) {
            $data['allergense_array'] = $product->allergens()->pluck('id')->toArray();
        } else {
            $data['allergense_array'] = [];
        }
        $stockProduct = StockProduct::wherenull('deleted_at')->get();
        $htmlProduct = '';
        foreach ($stockProduct as $val) {
            $htmlProduct .= '<option value="'.preg_replace('/&?[a-z0-9]+;/i', '', $val->id.'#'.$val->name).'">'.$val->name.'</option>';
        }
        $data['htmlProduct'] = $htmlProduct;

        return view('admin.extraproduct.create', $data);
    }

    public function index()
    {
        return view('admin.extraproduct.list');
    }

    public function getList(Request $request)
    {
        $query = Product::where('product_type', '1')->whereNull('deleted_at');
        $column_order = ['product_name', 'product_article_number']; //set column field database for datatable orderable
        $column_search = ['product_name', 'product_article_number', 'description']; //set column field database for datatable searchable
        $start_from = $request->start;
        $per_page = $request->length;
        $rawQuery = '';
        //Search

        if ($request->get('order_from') != null) {
            $query = $query->where('order_from', $request->get('order_from'));
        }

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
            $query = $query->orderBy('product_order', 'ASC');
        }

        $total = $query->get()->count();
        // $data = $query->skip($start_from)->limit($per_page)->get();
        $data = $query->get();

        foreach ($data as $key => $value) {
            $data[$key]['product_rowid'] = 'product_'.$data[$key]['id'];
        }

        return response()
            ->json([
                'data'  => $data,
                'total' => $total,
            ]);
    }

    public function save(Request $request)
    {

        $rules = [
            'name' => 'required',
            // 'name' => 'required|unique:products,product_name,'.$request->id.',product_id',
            'product_name' => Rule::unique('products')->where(function ($query) {
                return $query->where('is_deleted', '0');
            }),
            'price'      => 'required|numeric',
            'allergense' => 'required',
            // 'article_number' => "nullable|unique:products,product_article_number,".$request->id.',product_id',
            'product_article_number' => Rule::unique('products')->where(function ($query) {
                return $query->where('is_deleted', '0');
            }),
            'image_file.*'    => 'file|mimes:jpeg,png,jpg,svg|max:2048',
            'alcoholic_items' => 'required',
            'product_type_id' => 'required',

        ];
        // if ($request->id != "") {
        //   $rules['article_number'] = "unique:products,product_article_number,".$request->id.',product_id';
        // }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        } else {

            $name = $request->input('name');
            $explode = explode('#', $name);
            $name = (count($explode)) == 2 ? $explode[1] : $explode[0];

            if ($request->id) {
                $message = 'Producten succesvol bijgewerkt';
                $product = Product::find($request->id);
                $if_exit = Product::where('product_name', $name)->where('product_article_number', $request->input('article_number'))->whereNotIN('id', [$request->id])->where('product_type', '1')->whereNull('deleted_at')->first();
                if ($if_exit) {
                    return response()
                        ->json([
                            'status' => false,
                            'type'   => 'validation',
                            'errors' => ['name' => ['This Product is already added']],
                        ]);
                }
            } else {
                $message = 'Producten succesvol toegevoegd';
                $product = new Product;
                $if_exit = Product::where('product_name', $name)->where('product_article_number', $request->input('article_number'))->where('product_type', '1')->whereNull('deleted_at')->first();
                if ($if_exit) {
                    return response()
                        ->json([
                            'status' => false,
                            'type'   => 'validation',
                            'errors' => ['name' => ['This Product is already added']],
                        ]);
                }
            }

            $product->product_name = $name;
            $product->product_type = '1';
            $product->product_price = $request->input('price');
            $product->product_article_number = $request->input('article_number');
            $product->vat = $request->input('vat');
            $product->vat_price = $request->input('vat_price');
            $product->description = $request->input('description');
            $product->alcohol = $request->input('alcohol');
            $product->order_from = $request->input('order_from');
            $product->is_popular = ($request->input('is_popular') == 'on') ? '1' : '0';
            $product->is_show = ($request->input('is_show') == 'on') ? '1' : '0';
            $product->alcoholic_items = $request->input('alcoholic_items');
            $product->product_type_id = $request->input('product_type_id');

            if ($request->hasFile('image_file')) {

                $image = $request->file('image_file');

                $imagename = time().'_'.$image->getClientOriginalName();
                $img = Image::read($image->path());
                $img->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads/product/thumb').'/'.$imagename);
                $image->move(public_path('uploads/product/'), $imagename);
                $product->image = $imagename;
            }
            if ($request->input('old_cat_pic') == '' && ! $request->hasFile('image_file')) {
                $product->image = '';
            }
            $product->save();
            // AssignAllergen::where('product_id', $product->id)->whereNull('deleted_at')->delete();
            // if ($request->input('allergense')) {
            //   foreach ($request->input('allergense') as $key => $value) {
            //     $allergense = new AssignAllergen();
            //     $allergense->id = $product->id;
            //     $allergense->allergen_id = $value;
            //     $allergense->save();
            //   }
            // }

            if ($request->input('allergense')) {
                $product->allergens()->sync($request->input('allergense'));
            } else {
                $product->allergens()->sync([]);
            }

            return response()
                ->json([
                    'status' => true,
                    'msg'    => $message,
                    'page'   => 'admin/extraproduct/list',
                ]);
        }
    }

    public function deleteProduct(Request $request)
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
            Product::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Product deleted !',
                ]);
        }
    }
}
