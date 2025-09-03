<?php

namespace App\Http\Controllers;

use App\EmployeeExperience;
use Illuminate\Http\Request;
use Session;
use Validator;

class EmployeeExperienceController extends Controller
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
    public function create(REQUEST $request)
    {
       


         $rules = array(
            'company' => 'required|string|max:255',
            'jobtitle' => 'required|string|max:255',
            'fromdate' => 'required|string|max:255',                   
            'todate' => 'required|string|max:255',                   
            'comment' => 'required|string|max:255'  
        );
    
        $error = Validator::make($request->all(), $rules);
    
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
    
        $form_data = array(
            'emp_id'        =>  $request->emp_id,
            'company'        =>  $request->company,
            'jobtitle'        =>  $request->jobtitle,
            'fromdate'        =>  $request->fromdate,
            'todate'        =>  $request->todate,
            'comment'        =>  $request->comment
            
        );
    
        $experience=new EmployeeExperience;
         $experience->emp_id=$request->input('emp_id');
         $experience->emp_company=$request->input('company');
         $experience->emp_jobtitle=$request->input('jobtitle');
         $experience->emp_from_date=$request->input('fromdate');
         $experience->emp_to_date=$request->input('todate');
         $experience->emp_comment=$request->input('comment');
       
         $experience->save();
    
       return response()->json(['success' => 'The Employee Experience Successfuly Saved.']);
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
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeExperience $employeeExperience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = EmployeeExperience::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeExperience $employeeExperience)
    {
        $rules = array(
            'company' => 'required|string|max:255',
            'jobtitle' => 'required|string|max:255',
            'fromdate' => 'required|string|max:255',                   
            'todate' => 'required|string|max:255',                   
            'comment' => 'required|string|max:255'  
            );
    
            $error = Validator::make($request->all(), $rules);
    
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }
    
            $form_data = array(
                'emp_id'        =>  $request->emp_id,
                'emp_company'        =>  $request->company,
                'emp_jobtitle'        =>  $request->jobtitle,
                'emp_from_date'        =>  $request->fromdate,
                'emp_to_date'        =>  $request->todate,
                'emp_comment'        =>  $request->comment
                
            );
    
            EmployeeExperience::whereId($request->hidden_id)->update($form_data);
    
            return response()->json(['success' => 'Employee Education is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $data = EmployeeExperience::findOrFail($id);
        $data->delete();
    }
}
