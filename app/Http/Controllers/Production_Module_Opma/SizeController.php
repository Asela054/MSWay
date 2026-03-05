<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\Size;
use Illuminate\Http\Request;
use Validator;

class SizeController extends Controller
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

        $size= Size::orderBy('id', 'asc')->get();
        return view('Opma_Production.Daily_Production.size',compact('size'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'size'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'size'        =>  $request->size,
            'remark'      =>  $request->remark
        );

        $size=new Size;
        $size->size=$request->input('size');
        $size->remark=$request->input('remark');
        $size->save();

        return response()->json(['success' => 'Size Added Successfully.']);
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
            $data = Size::findOrFail($id);
            $result = array(
                'id' => $data->id,
                'size' => $data->size,
                'remark' => $data->remark
            );
            
            return response()->json(['result' => $result]);
        }
    }

    public function update(Request $request, Size $size)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'size'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'size'    =>  $request->size,
            'remark'  =>  $request->remark
        );

        Size::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Size::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Size Deleted Successfully.']);
    }
}
