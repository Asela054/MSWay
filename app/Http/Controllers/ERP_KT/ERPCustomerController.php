<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use App\ERP_KT\Customer;
use Illuminate\Http\Request;
use Validator;

class ERPCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-list');
        if (!$permission) {
            abort(403);
        }

        $customers = Customer::orderBy('id', 'asc')->get();
        return view('ERP_KT.customer', compact('customers'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'name'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name'=>$request->name,
            'contact_number'=>$request->contact_number,
            'email'=>$request->email,
            'remarks'=>$request->remarks,
        );

        $customer=new Customer;
        $customer->name=$request->input('name');
        $customer->contact_number=$request->input('contact_number');
        $customer->email=$request->input('email');
        $customer->remarks=$request->input('remarks');       
        $customer->save();

        return response()->json(['success' => 'Customer Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = Customer::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)   
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
          'name'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name'=>$request->name,
            'contact_number'=>$request->contact_number,
            'email'=>$request->email,
            'remarks'=>$request->remarks,
        );

        Customer::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Customer Data Updated Successfully ']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Customer::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Customer Deleted Successfully.']);
    }
}
