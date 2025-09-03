<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Employeementrequrement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Session;

class EmployeementrequrementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $permission = \Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $employees = Employee::get();
       
        $employmentRequirement = Employeementrequrement::where('employee_id', $id)->first();

        $requrement = [];

        if ($employmentRequirement !== null) {
            $recordoption = 2;
            $requrement = [
                'id' => $employmentRequirement->id,
                'employee_id' => $employmentRequirement->employee_id,
                'first_interviewer' => $employmentRequirement->first_interviwer,
                'first_interview_date' => $employmentRequirement->first_interview_date,
                'first_interview_outcome' => $employmentRequirement->first_interview_outcome,
                'first_interview_comments' => $employmentRequirement->first_interview_comments,
                'second_interviewer' => $employmentRequirement->second_interviewer,
                'second_interview_date' => $employmentRequirement->second_interview_date,
                'second_interview_outcome' => $employmentRequirement->second_interview_outcome,
                'second_interview_comments' => $employmentRequirement->second_interview_comments,
                'third_interviewer' => $employmentRequirement->third_interviewer,
                'third_interview_date' => $employmentRequirement->third_interview_date,
                'third_interview_outcome' => $employmentRequirement->third_interview_outcome,
                'third_interview_comments' => $employmentRequirement->third_interview_comments
            ];
        } else {
            $recordoption = 1;
            $requrement = [
                'id' => null,
                'employee_id' => $id,
                'first_interviewer' => null,
                'first_interview_date' => null,
                'first_interview_outcome' => null,
                'first_interview_comments' => null,
                'second_interviewer' => null,
                'second_interview_date' => null,
                'second_interview_outcome' => null,
                'second_interview_comments' => null,
                'third_interviewer' => null,
                'third_interview_date' => null,
                'third_interview_outcome' => null,
                'third_interview_comments' => null
            ];
        }

        return view('Employee.viewRequrement', compact('id','employees','recordoption','requrement'));
    }
    
    public function insert(Request $request){

        $permission = \Auth::user()->can('employee-edit');
        if (!$permission) {
            abort(403);
        }

        $emp_id=$request->input('empid');
        $recordoption=$request->input('recordoption');
        $recordID=$request->input('recordID');

        $first_interwer=$request->input('first_interwer');
        $first_interw_date=$request->input('first_interw_date');
        $first_interw_outcome=$request->input('first_interw_outcome');
        $first_interw_comments=$request->input('first_interw_comments');

        $second_interwer=$request->input('second_interwer');
        $second_interw_date=$request->input('second_interw_date');
        $second_interw_outcome=$request->input('second_interw_outcome');
        $second_interw_comments=$request->input('second_interw_comments');

        $third_interwer=$request->input('third_interwer');
        $third_interw_date=$request->input('third_interw_date');
        $third_interw_outcome=$request->input('third_interw_outcome');
        $third_interw_comments=$request->input('third_interw_comments');

        if( $recordoption == 1){

            $requremnts = new Employeementrequrement();

            $requremnts->employee_id=$emp_id;
            $requremnts->first_interviwer=$first_interwer;
            $requremnts->first_interview_date=$first_interw_date;
            $requremnts->first_interview_outcome=$first_interw_outcome;
            $requremnts->first_interview_comments=$first_interw_comments;
            $requremnts->second_interviewer=$second_interwer;
            $requremnts->second_interview_date=$second_interw_date;
            $requremnts->second_interview_outcome=$second_interw_outcome;
            $requremnts->second_interview_comments=$second_interw_comments;
            $requremnts->third_interviewer=$third_interwer;
            $requremnts->third_interview_date=$third_interw_date;
            $requremnts->third_interview_outcome=$third_interw_outcome;
            $requremnts->third_interview_comments=$third_interw_comments;
            $requremnts->status= '1';
            $requremnts->created_by= Auth::id();
            $requremnts->save();

        Session::flash('message', 'The Requrement Details Successfully Saved');
        Session::flash('alert-class', 'alert-success');
        return redirect('viewEmployeeRequrement/'.$emp_id);
       }
        else{

            $data = array(
                'employee_id' => $emp_id,
                'first_interviwer' => $first_interwer,
                'first_interview_date' => $first_interw_date,
                'first_interview_outcome' => $first_interw_outcome,
                'first_interview_comments' => $first_interw_comments,
                'second_interviewer' => $second_interwer,
                'second_interview_date' => $second_interw_date,
                'second_interview_outcome' => $second_interw_outcome,
                'second_interview_comments' => $second_interw_comments,
                'third_interviewer' => $third_interwer,
                'third_interview_date' => $third_interw_date,
                'third_interview_outcome' => $third_interw_outcome,
                'third_interview_comments' => $third_interw_comments,
                'updated_by' => Auth::id()
            );
            
  
          Employeementrequrement::where('id', $recordID)
          ->update($data);
          
          Session::flash('message', 'The Requrement Details Successfully Saved');
          Session::flash('alert-class', 'alert-success');
          return redirect('viewEmployeeRequrement/'.$emp_id);
        }

    }

}
