<?php

namespace App\Http\Controllers;

use App\EmployeeDependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Validator;

class EmployeeDependentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, array(
            'emp_id' => 'required|string|max:255',
            'dep_name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'birthday' => 'required|string|max:255', 
         ));



         $employeeDependent=new EmployeeDependent;
         $id=$request->input('emp_id');
         $employeeDependent->emp_id=$request->input('emp_id');
         $employeeDependent->emp_dep_name=$request->input('dep_name');
         $employeeDependent->emp_dep_relation=$request->input('relationship');
         $employeeDependent->emp_dep_birthday=$request->input('birthday');
         $employeeDependent->save();


        
        Session::flash('success','The Dependent Details Successfully Saved');
        return redirect('viewDependents/'.$id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeDependent  $employeeDependent
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }
        
        $dependent = EmployeeDependent::where('emp_id',$id)->get();  
        //dd($id);
        return view('Employee.viewDependents',compact('dependent','id'));
    }

    public function edit_json($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = EmployeeDependent::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeDependent  $employeeDependent
     * @return \Illuminate\Http\Response
     */
    public function edit(REQUEST $request)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            abort(403);
        }
        
        $id=$request->dependent_id;
        $dep_name=$request->dep_name;
        $relationship=$request->relationship;
        $birthday=$request->birthday;
        
        
      
       $employeeDependent=EmployeeDependent::find($id);
       
        $employeeDependent->emp_dep_name=$dep_name;
        $employeeDependent->emp_dep_relation=$relationship;
        $employeeDependent->emp_dep_birthday=$birthday;

        $employeeDependent->save();
        Session::flash('success','The Dependent Details Successfully Updated');
        return redirect('editDependents/'.$id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeDependent  $employeeDependent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeDependent $employeeDependent)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'dependent_id' => 'required',
            'dep_name' => 'required',
            'birthday' => 'required',
            'relationship' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'emp_dep_name' => $request->dep_name,
            'emp_dep_relation' => $request->relationship,
            'emp_dep_birthday' => $request->birthday
        );

        EmployeeDependent::whereId($request->dependent_id)->update($form_data);

        return response()->json(['success' => 'Dependent updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeDependent  $employeeDependent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = EmployeeDependent::findOrFail($id);
        $data->delete();
    }
}
