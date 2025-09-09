<?php

namespace App\Http\Controllers;

use App\Product;
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

        $product= Product::orderBy('id', 'asc')->get();
        return view('Daily_Production.Product',compact('product'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'productname'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'productname'   =>  $request->productname,
            'description'   =>  $request->description
            // 'semi_price'    =>  $request->semi_price,
            // 'full_price'    =>  $request->full_price
        );

        $product=new Product;
        $product->productname=$request->input('productname');
        $product->description=$request->input('description'); 
        $product->semi_price=0;
        $product->full_price=0;
        $product->status=1;      
        $product->save();

        return response()->json(['success' => 'Product Added Successfully.']);
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
            return response()->json(['result' => $data]);
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
            'productname'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'productname'    =>  $request->productname,
            'description' =>  $request->description
            // 'semi_price'    =>  $request->semi_price,
            // 'full_price'    =>  $request->full_price
        );

        Product::whereId($request->hidden_id)->update($form_data);

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
        $data->delete();
    }
}
