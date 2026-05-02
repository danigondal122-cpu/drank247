<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\AssignAllergen;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\StockProduct;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class AdminProductController extends Controller
{
    /** Product Methods */
    public function create()
    {
        $data['row'] = [];
        $category = Category::wherenull('deleted_at')->whereNull('category_id')->orderBy('category_order', 'ASC')->get();
        $stockProduct = StockProduct::wherenull('deleted_at')->get();
        $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();
        $data['allergense'] = Allergen::wherenull('deleted_at')->get();
        $data['product_type_id'] = ProductType::wherenull('deleted_at')->get();

        $htmlProduct = '';
        foreach ($stockProduct as $val) {
            $htmlProduct .= '<option value="'.preg_replace('/&?[a-z0-9]+;/i', '', $val->id.'#'.$val->name).'">'.$val->name.'</option>';
        }
        $data['htmlProduct'] = $htmlProduct;
        $html = '';
        foreach ($category as $val) {
            $count = Category::whereNull('deleted_at')->where('id', $val->id)->count();
            $disabled = $count == 0 ? 'disabled' : '';

            $html .= "<option value='$val->id'  $disabled>".$val->category_name.'</option>';
            $html .= Category::getAllCategories($val->id);
        }
        $data['html'] = $html;

        return view('admin.product.create', $data);
    }

    public function edit($id)
    {
        $data['row'] = [];

        if ($id) {
            $data['row'] = Product::findOrFail($id);
        }
        $data['allergense'] = Allergen::wherenull('deleted_at')->get();
        $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();
        // $data['allergense_array'] = AssignAllergen::where('product_id', $id)->pluck('allergen_id')->toarray();
        $data['allergense_array'] = Product::find($id)->allergens()->pluck('id')->toArray();
        $data['product_type_id'] = ProductType::wherenull('deleted_at')->get();

        $stockProduct = StockProduct::wherenull('deleted_at')->get();

        $htmlProduct = '';
        foreach ($stockProduct as $val) {
            $htmlProduct .= '<option value="'.preg_replace('/&?[a-z0-9]+;/i', '', $val->_id.'#'.$val->_name).'">'.$val->_name.'</option>';
        }
        $data['htmlProduct'] = $htmlProduct;
        $category = Category::wherenull('deleted_at')->whereNull('category_id')->orderBy('category_order', 'ASC')->get();
        //#category##
        $html = '';
        foreach ($category as $val) {
            $isSelectedmain = ($val->id == $data['row']['category_id']) ? 'selected' : '';
            $html .= "<option value='$val->id' $isSelectedmain>".$val->category_name.'</option>';
            $html .= Category::getAllCategories($val->id, '', '', $data['row']['category_id']);
        }
        $data['html'] = $html;

        return view('admin.product.create', $data);
    }

    public function index()
    {
        $data['categories'] = Category::orderBy('category_order', 'ASC')->whereNull('deleted_at')->get();
        $data['warehouse'] = WareHouse::whereNull('deleted_at')->get();
        $category = Category::wherenull('deleted_at')->where('category_id', '0')->orderBy('category_order', 'ASC')->get();
        $html = '';
        foreach ($category as $val) {

            $count = Category::wherenull('deleted_at')
                ->where('category_id', $val['category_id'])->get()->count();
            if ($count != 0) {
                $disabled = 'disabled';
            } else {
                $disabled = '';
            }
            $selected = '';
            if (Session::get('cat_id') && Session::get('cat_id') != '') {
                if (Session::get('cat_id') == $val->id) {
                    $selected = 'selected';
                }
            }
            $html .= "<option value='$val->id'  $disabled $selected>".$val->category_name.'</option>';
            $html .= Category::getAllCategories($val->id);
        }
        $data['html'] = $html;

        return view('admin.product.list', $data);
    }

    public function sessionDestroy()
    {
        session()->forget('cat_id');
        session()->forget('order_from');

        return redirect('admin/product/list');
    }

    public function getList(Request $request)
    {
        $query = Product::select('products.*', 'categories.category_name')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('product_type', '0')
            ->whereNull('products.deleted_at')
            ->whereNull('categories.deleted_at'); //$request->get('cat_id')
        if ($request->get('cat_id') && $request->get('cat_id') != '') {
            Session::put('cat_id', $request->get('cat_id'));
            $query = $query->where('products.category_id', $request->get('cat_id'));
        }
        if (Session::get('cat_id') && Session::get('cat_id') != '') {
            $query = $query->where('products.category_id', Session::get('cat_id'));
        }

        if ($request->get('order_from') != null) {
            Session::put('order_from', $request->get('order_from'));
            $query = $query->where('products.order_from', $request->get('order_from'));
        }
        if (Session::get('order_from') && Session::get('order_from') != '') {
            $query = $query->where('products.order_from', Session::get('order_from'));
        }
        $column_order = ['product_name', 'product_article_number', 'category_name']; //set column field database for datatable orderable
        $column_search = ['product_name', 'product_article_number', 'category_name']; //set column field database for datatable searchable
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
            if ($request->get('cat_id') && $request->get('cat_id') != '') {
                $query = $query->orderBy('product_order', 'ASC');
            } else {
                $query = $query->orderBy('id', 'DESC');
            }
        }

        $total = $query->get()->count();
        $data = $query->skip($start_from)->limit($per_page)->get();
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
            'category_name'   => 'required',
            'name'            => 'required',
            'allergense'      => 'required',
            'price'           => 'required|numeric',
            'article_number'  => 'nullable',
            'alcoholic_items' => 'required',
            'product_type_id' => 'required',
            'image_file.*'    => 'file|mimes:jpeg,png,jpg,svg|max:2048',
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

            $name = $request->input('name');
            $explode = explode('#', $name);
            $name = (count($explode)) == 2 ? $explode[1] : $explode[0];

            if ($request->id) {
                $message = 'Producten succesvol bijgewerkt';
                $product = Product::find($request->id);
                $if_exit = Product::where('product_name', $name)->where('product_article_number', $request->input('article_number'))->where('category_id', $request->input('category_name'))->whereNotIN('id', [$request->id])->whereNull('deleted_at')->first();
                if ($if_exit) {
                    return response()
                        ->json([
                            'status' => false,
                            'type'   => 'validation',
                            'errors' => ['name' => ['This Product is already added for Category']],
                        ]);
                }
            } else {
                $message = 'Producten succesvol toegevoegd';
                $product = new Product;

                $if_exit = Product::where('product_name', $name)->where('product_article_number', $request->input('article_number'))->where('category_id', $request->input('category_name'))->whereNull('deleted_at')->first();
                if ($if_exit) {
                    return response()
                        ->json([
                            'status' => false,
                            'type'   => 'validation',
                            'errors' => ['name' => ['This Product is already added for Category']],
                        ]);
                }
            }

            $product->product_name = $name;
            $product->category_id = $request->input('category_name');
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
            $product->product_type = 0;

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
            // AssignAllergen::where('product_id', $product->product_id)->whereNull('deleted_at')->delete();
            // if ($request->input('allergense')) {
            //   foreach ($request->input('allergense') as $key => $value) {
            //     $allergense = new AssignAllergen();
            //     $allergense->product_id = $product->product_id;
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
                    'page'   => 'admin/product/list',
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
            $product = Product::find($request->id);
            $product->allergens()->detach();
            $product->delete();

            // Cart::where('cart_itemid', $request->id)->delete();
            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Product deleted !',
                ]);
        }
    }

    public function updateProductOrder(Request $request)
    {

        foreach ($request->product as $key => $value) {
            $order = $key + 1;
            Product::where('id', $value)->update(['product_order' => $order]);
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Category Ordered !',
            ]);
    }
}
