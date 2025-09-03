<?php

namespace App\Http\Controllers;

use App\Warningletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class WarningletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Warning-letter-list');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code','emp_join_date','emp_department')->where('deleted',0)->get();
        $job_titles=DB::table('job_titles')->select('*')->get();
        $departments=DB::table('departments')->select('*')->get();
        return view('EmployeeLetter.warning',compact('companies','employees','job_titles','departments'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Warning-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee_f');
        $jobtitle=$request->input('jobtitle');
        $date=$request->input('date');
        $reason=$request->input('reason');
        $description=$request->input('description');
        $comment1=$request->input('comment1');
        $comment2=$request->input('comment2');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $warning = new Warningletter();
            $warning->company_id=$company;
            $warning->department_id=$department;
            $warning->employee_id=$employee;
            $warning->jobtitle=$jobtitle;
            $warning->date=$date;
            $warning->reason=$reason;
            $warning->description=$description;
            $warning->comment1=$comment1;
            $warning->comment2=$comment2;
            $warning->status= '1';
            $warning->created_by=Auth::id();
            $warning->created_at=Carbon::now()->toDateTimeString();
            $warning->save();
            
            Session::flash('message', 'The Employee Warning Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('warningletter');
        }
        
    }

    public function letterlist ()
    {
        $letters = DB::table('warning_letter')
        ->leftjoin('companies', 'warning_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'warning_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'warning_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles', 'warning_letter.jobtitle', '=', 'job_titles.id')
        ->select('warning_letter.*','employees.emp_name_with_initial As emp_name','job_titles.title As emptitle','companies.name As companyname','departments.name As department')
        ->whereIn('warning_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Warning-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('Warning-letter-delete')){
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
        $permission = \Auth::user()->can('Warning-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('warning_letter')
        ->select('warning_letter.*')
        ->where('warning_letter.id', $id)
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
        Warningletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee Warning is Successfully Deleted']);

    }

    public function getdepartmentfilter($company_id)
    {
        $department = DB::table('departments')
        ->select('departments.*')
        ->where('company_id', '=', $company_id)
        ->get();

        return response()->json($department);
    }

    public function getemployeefilter($department_id)
    {
        $employee = DB::table('employees')
        ->select('employees.*')
        ->where('emp_department', '=', $department_id)
        ->get();

        return response()->json($employee);
    }

    public function getjobfilter($employee_id)
    {
        $emp_job_code = DB::table('employees')
            ->where('id', '=', $employee_id)
            ->value('emp_job_code'); 

        $jobtitle = DB::table('job_titles')
            ->where('id', '=', $emp_job_code)
            ->get();


        return response()->json($jobtitle);
        
    }


}
