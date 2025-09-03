<?php

namespace App\Http\Controllers;

use App\EmployeeSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Validator;

class EmployeeSkillController extends Controller
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
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
      
       $rules = array(
        'skill' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'comment' => 'required|string|max:255'
    );

    $error = Validator::make($request->all(), $rules);

    if($error->fails())
    {
        return response()->json(['errors' => $error->errors()->all()]);
    }

    $form_data = array(
        'emp_id'        =>  $request->emp_id,
        'skill'        =>  $request->skill,
        'experience'        =>  $request->experience,
        'comment'        =>  $request->comment
        
    );

    $skill=new EmployeeSkill;
    $skill->emp_id=$request->input('emp_id');        
    $skill->emp_skill=$request->input('skill');
    $skill->emp_experience=$request->input('experience');
    $skill->emp_comment=$request->input('comment');
   
    $skill->save();
     

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
     * @param  \App\EmployeeSkill  $employeeSkill
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeSkill $employeeSkill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeSkill  $employeeSkill
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = EmployeeSkill::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeSkill  $employeeSkill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeSkill $employeeSkill)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'skill' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'comment' => 'required|string|max:255'
            );
    
            $error = Validator::make($request->all(), $rules);
    
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }
    
            $form_data = array(
                'emp_id'        =>  $request->emp_id,
                'emp_skill'        =>  $request->skill,
                'emp_experience'        =>  $request->experience,
                'emp_comment'        =>  $request->comment
        
                
            );
    
            EmployeeSkill::whereId($request->skill_hidden_id)->update($form_data);
    
            return response()->json(['success' => 'Employee Skill is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeSkill  $employeeSkill
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = EmployeeSkill::findOrFail($id);
        $data->delete();
    }
}
