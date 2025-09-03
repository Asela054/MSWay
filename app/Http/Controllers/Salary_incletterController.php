<?php

namespace App\Http\Controllers;

use App\Salary_incletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class Salary_incletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Salary-inc-letter-list');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code','emp_department')->where('deleted',0)->get();
        $job_titles=DB::table('job_titles')->select('*')->get();
        $departments=DB::table('departments')->select('*')->get();
        $payroll_profiles=DB::table('payroll_profiles')->select('*')->get();
        return view('EmployeeLetter.salary_inc',compact('companies','employees','job_titles','departments','payroll_profiles'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Salary-inc-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee_f');
        $jobtitle=$request->input('jobtitle');
        $pre_salary=$request->input('basic_salary');
        $new_salary=$request->input('new_salary');
        $date=$request->input('date');
        $comment1=$request->input('comment1');
        $comment2=$request->input('comment2');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $salary_inc = new Salary_incletter();
            $salary_inc->company_id=$company;
            $salary_inc->department_id=$department;
            $salary_inc->employee_id=$employee;
            $salary_inc->jobtitle=$jobtitle;
            $salary_inc->pre_salary=$pre_salary;
            $salary_inc->new_salary=$new_salary;
            $salary_inc->date=$date;
            $salary_inc->comment1=$comment1;
            $salary_inc->comment2=$comment2;
            $salary_inc->status= '1';
            $salary_inc->created_by=Auth::id();
            $salary_inc->created_at=Carbon::now()->toDateTimeString();
            $salary_inc->save();
            
            Session::flash('message', 'The Employee Salary increment Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('salary_incletter');
        }
    }

    public function letterlist ()
    {
        $letters = DB::table('salary_inc_letter')
        ->leftjoin('companies', 'salary_inc_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'salary_inc_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'salary_inc_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles', 'salary_inc_letter.jobtitle', '=', 'job_titles.id')
        ->select('salary_inc_letter.*','employees.emp_name_with_initial As emp_name','job_titles.title As emptitle','companies.name As companyname','departments.name As department')
        ->whereIn('salary_inc_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Salary-inc-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('Salary-inc-letter-delete')){
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
                    $btn .= ' <button name="print" id="'.$row->id.'" class="print btn btn-outline-info btn-sm"><i class="fas fa-print"></i></button>';
            return $btn;
        })
       
        ->rawColumns(['action'])
        ->make(true);
    }

    public function edit(Request $request)
    {
        $permission = \Auth::user()->can('Salary-inc-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('salary_inc_letter')
        ->select('salary_inc_letter.*')
        ->where('salary_inc_letter.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }

    public function delete(Request $request)
    {
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Salary_incletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee Salary increment letter is Successfully Deleted']);

    }

    public function getdepartmentfilter($company_id)
    {
        $department = DB::table('departments')
        ->select('departments.*')
        ->where('company_id', '=', $company_id)
        ->get();

        return response()->json($department);
    }

    public function getemployeefilter($emp_department)
    {
        $employee = DB::table('employees')
        ->select('employees.*')
        ->where('emp_department', '=', $emp_department)
        ->get();

        return response()->json($employee);
    }

    public function getEmployeeDetails($employee_id)
    {
        // Get job title based on employee's job code
        $emp_job_code = DB::table('employees')
            ->where('id', '=', $employee_id)
            ->value('emp_job_code');

        $jobTitle = DB::table('job_titles')
            ->where('id', '=', $emp_job_code)
            ->get();

        // Get join date
        $basic_salary = DB::table('payroll_profiles')
            ->select('basic_salary')
            ->where('emp_id', '=', $employee_id)
            ->get();

        return response()->json([
            'jobTitle' => $jobTitle,
            'basic_salary' => $basic_salary
        ]);
    }


}
