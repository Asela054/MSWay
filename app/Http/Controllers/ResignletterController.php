<?php

namespace App\Http\Controllers;

use App\Resignletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class ResignletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Resign-letter-list');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code','emp_join_date','emp_department')->where('deleted',0)->get();
        $job_titles=DB::table('job_titles')->select('*')->get();
        $departments=DB::table('departments')->select('*')->get();
        return view('EmployeeLetter.resign',compact('companies','employees','job_titles','departments'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Resign-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee_f');
        $jobtitle=$request->input('jobtitle');
        $joindate=$request->input('emp_join_date');
        $lastdate=$request->input('last_date');
        $reason=$request->input('reason');
        $comment1=$request->input('comment1');
        $comment2=$request->input('comment2');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $resign = new Resignletter();
            $resign->company_id=$company;
            $resign->department_id=$department;
            $resign->employee_id=$employee;
            $resign->jobtitle=$jobtitle;
            $resign->join_date=$joindate;
            $resign->last_date=$lastdate;
            $resign->reason=$reason;
            $resign->comment1=$comment1;
            $resign->comment2=$comment2;
            $resign->status= '1';
            $resign->created_by=Auth::id();
            $resign->created_at=Carbon::now()->toDateTimeString();
            $resign->save();
            
            Session::flash('message', 'The Employee Resign Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('resignletter');
        }
        
    }

    public function letterlist ()
    {
        $letters = DB::table('resign_letter')
        ->leftjoin('companies', 'resign_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'resign_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'resign_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles', 'resign_letter.jobtitle', '=', 'job_titles.id')
        ->select('resign_letter.*','employees.emp_name_with_initial As emp_name','job_titles.title As emptitle','companies.name As companyname','departments.name As department')
        ->whereIn('resign_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Resign-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('Resign-letter-delete')){
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
        $permission = \Auth::user()->can('Resign-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('resign_letter')
        ->select('resign_letter.*')
        ->where('resign_letter.id', $id)
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
        Resignletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee Resign is Successfully Deleted']);

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
        $joinDate = DB::table('employees')
            ->select('emp_join_date')
            ->where('id', '=', $employee_id)
            ->get();

        return response()->json([
            'jobTitle' => $jobTitle,
            'joinDate' => $joinDate
        ]);
    }


}
