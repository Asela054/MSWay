<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\Product;
use App\ProductionModule_Opma\ProductDetail;
use App\ProductionModule_Opma\Size;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('product-list');
        if (!$permission) {
            abort(403);
        }

        $sizes= Size::orderBy('id', 'asc')->get();
        return view('Opma_Production.Daily_Production.Product',compact('sizes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'title'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'title'   =>  $request->title,
            'code' =>  $request->code,
            'from_date' =>  $request->from_date,
            'to_date' =>  $request->to_date,
            'sizes' =>  $request->sizes
        );

        $product=new Product;
        $product->title=$request->input('title');
        $product->code=$request->input('code');
        $product->from_date=$request->input('from_date');
        $product->to_date=$request->input('to_date');  
        $product->save();
        $opma_style_id=$product->id;

        if(!empty($request->sizes)) {
            foreach($request->sizes as $size_id) {
                $productDetail = new ProductDetail;
                $productDetail->opma_style_id = $opma_style_id;
                $productDetail->opma_size_id = $size_id;
                $productDetail->save();
            }
        }

        return response()->json(['success' => 'Styles Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = Product::findOrFail($id);
            $sizes = ProductDetail::where('opma_style_id', $id)->pluck('opma_size_id')->toArray();
            return response()->json(['result' => $data, 'sizes' => $sizes]);
        }
    }

    public function update(Request $request, Product $product)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $rules = array(
            'title'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'title'   =>  $request->title,
            'code' =>  $request->code,
            'from_date' =>  $request->from_date,
            'to_date' =>  $request->to_date
        );

        Product::whereId($request->hidden_id)->update($form_data);

        $new_sizes = $request->input('sizes', []);
        $existing_sizes = ProductDetail::where('opma_style_id', $request->hidden_id)
                                        ->pluck('opma_size_id')
                                        ->toArray();

        $to_add = array_diff($new_sizes, $existing_sizes);
        $to_remove = array_diff($existing_sizes, $new_sizes);
        
        if(!empty($to_remove)) {
            ProductDetail::where('opma_style_id', $request->hidden_id)
                         ->whereIn('opma_size_id', $to_remove)
                         ->delete();
        }
        
        if(!empty($to_add)) {
            foreach($to_add as $size_id){
                $product_detail = new ProductDetail;
                $product_detail->opma_style_id = $request->hidden_id;
                $product_detail->opma_size_id = $size_id;
                $product_detail->save();
            }
        }

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Product::findOrFail($id);
        ProductDetail::where('opma_style_id', $id)->delete();
        $data->delete();
        return response()->json(['success' => 'Data is successfully deleted']);
    }
}
