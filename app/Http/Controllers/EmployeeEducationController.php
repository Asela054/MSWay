<?php

namespace App\Http\Controllers;

use App\EmployeeEducation;
use App\EmployeeExperience;
use App\EmployeeSkill;
use App\EmployeeLanguages;
use Illuminate\Http\Request;
use Session;
use Validator;

class EmployeeEducationController extends Controller
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
            'level' => 'required|string|max:255',
            'instiitute' => 'required|string|max:255',
            'year' => 'required|string|max:255',
            'score' => 'required|string|max:255',                   
            'specification' => 'required|string|max:255',                   
            'startdate' => 'required|string|max:255',                   
            'enddate' => 'required|string|max:255'
        );
    
        $error = Validator::make($request->all(), $rules);
    
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
    
        $form_data = array(
            'emp_id'        =>  $request->emp_id,
            'level'        =>  $request->level,
            'instiitute'        =>  $request->instiitute,
            'year'        =>  $request->year,
            'score'        =>  $request->score,
            'specification'        =>  $request->specification,
            'startdate'        =>  $request->startdate,
            'enddate'        =>  $request->enddate
            
        );

        $rules = array(
            'skill' => 'required|string|max:255',
                'experience' => 'required|string|max:255',
                'comment' => 'required|string|max:255'
        );
    
        $education=new EmployeeEducation;
         $education->emp_id=$request->input('emp_id');
         $education->emp_level=$request->input('level');
         $education->emp_institute=$request->input('instiitute');
         $education->emp_year=$request->input('year');
         $education->emp_gpa=$request->input('score');
         $education->emp_specification=$request->input('specification');
         $education->emp_start_date=$request->input('startdate');
         $education->emp_end_date=$request->input('enddate');
       
         $education->save();
         
    
       return response()->json(['success' => 'The Employee Education Successfuly Saved.']);
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
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $education = EmployeeEducation::where('emp_id',$id)->get();
        $experience = EmployeeExperience::where('emp_id',$id)->get();
        $skill = EmployeeSkill::where('emp_id',$id)->get();
        $languages = EmployeeLanguages::where('emp_id',$id)->get();
       
        return view('Employee.viewQualification',compact('education','experience','skill','languages','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = EmployeeEducation::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeEducation $employeeEducation)
    {
        $rules = array(
            'level' => 'required|string|max:255',
            'instiitute' => 'required|string|max:255',
            'year' => 'required|string|max:255',
            'score' => 'required|string|max:255',                   
            'specification' => 'required|string|max:255',                   
            'startdate' => 'required|string|max:255',                   
            'enddate' => 'required|string|max:255'
            );
    
            $error = Validator::make($request->all(), $rules);
    
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }
    
            $form_data = array(
                'emp_level'        =>  $request->level,
                'emp_institute'        =>  $request->instiitute,
                'emp_year'        =>  $request->year,
                'emp_gpa'        =>  $request->score,
                'emp_specification'        =>  $request->specification,
                'emp_start_date'        =>  $request->startdate,
                'emp_end_date'        =>  $request->enddate
                
            );
    
            EmployeeEducation::whereId($request->edu_hidden_id)->update($form_data);
    
            return response()->json(['success' => 'Employee Education is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = EmployeeEducation::findOrFail($id);
        $data->delete();
    }
}
