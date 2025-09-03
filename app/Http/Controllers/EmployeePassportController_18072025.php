<?php

namespace App\Http\Controllers;

use App\EmployeePassport;
use App\EmployeeAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class EmployeePassportController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, array(
            
            'issue_date' => 'required|string|max:255',
            'expire_date' => 'required|string|max:255',
            'pass_comments' => 'string|max:255',  
            'pass_type' => 'string|max:255',  
            'pass_status' => 'string|max:255',  
            'pass_review' => 'string|max:255',
            'epf_no' => 'string|max:255',
         ));

         
         $emp_id=$request->input('emp_id');

         $passport=new EmployeePassport;
        
         $passport->emp_id=$request->input('emp_id');
         $passport->emp_pass_issue_date=$request->input('issue_date');
         $passport->emp_pass_expire_date=$request->input('expire_date');
         $passport->emp_pass_comments=$request->input('pass_comments');
         $passport->emp_pass_type=$request->input('pass_type');
         $passport->emp_pass_status=$request->input('pass_status');
         $passport->emp_pass_review=$request->input('pass_review');
         $passport->epf_no=$request->input('epf_no');

         $passport->save();

        Session::flash('message', 'The Passport Details Successfully Saved');
        Session::flash('alert-class', 'alert-success');
        return redirect('viewPassport/'.$emp_id);
    }

    public function passportattacment(Request $request){

        $file = $request->file('empattachment');
        $name = time().'.'.$file->getClientOriginalExtension();
        $destinationPath = public_path('/Passport');
        $file->move($destinationPath, $name);


        $employeeattachment=new EmployeeAttachment;
            $emp_id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->emp_ath_type='passport';
            $employeeattachment->save();

            return redirect('viewPassport/'.$emp_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeePassport  $employeePassport
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $passport = EmployeePassport::where('emp_id',$id)->get();

        return view('Employee.viewPassport',compact('passport','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeePassport  $employeePassport
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $passport = EmployeePassport::find($id);
        return view('Employee.updatePassport',compact('passport','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeePassport  $employeePassport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeePassport $employeePassport)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, array(
            'issue_date' => 'required|string|max:255',
            'expire_date' => 'required|string|max:255',
            'pass_comments' => 'string|max:255',
            'pass_type' => 'string|max:255',
            'pass_status' => 'string|max:255',
            'pass_review' => 'string|max:255',
            'epf_no' => 'string|max:255',
        ));

        $emp_id=$request->input('emp_id');

        $data = array(
              'emp_pass_issue_date' => $request->input('issue_date'),
              'emp_pass_expire_date' => $request->input('expire_date'),
              'emp_pass_comments' => $request->input('pass_comments'),
              'emp_pass_type' => $request->input('pass_type'),
              'emp_pass_status' => $request->input('pass_status'),
              'emp_pass_review' => $request->input('pass_review'),
              'epf_no' => $request->input('epf_no'),

        );

        $updated_records = EmployeePassport::where('emp_pass_id', $request->emp_pass_id)
            ->update($data);

        Session::flash('message', 'The Passport Details Successfully Saved');
        Session::flash('alert-class', 'alert-success');
        return redirect('viewPassport/'.$emp_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeePassport  $employeePassport
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeePassport $employeePassport)
    {
        //
    }
}
