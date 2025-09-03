<?php

namespace App\Http\Controllers;

use App\Serviceletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class ServiceletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Service-letter-list');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code','emp_join_date','emp_department')->where('deleted',0)->get();
        $job_titles=DB::table('job_titles')->select('*')->get();
        $departments=DB::table('departments')->select('*')->get();
        return view('EmployeeLetter.service',compact('companies','employees','job_titles','departments'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Service-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee_f');
        $jobtitle=$request->input('jobtitle');
        $joindate=$request->input('emp_join_date');
        $enddate=$request->input('end_date');
        $comment1=$request->input('comment1');
        $comment2=$request->input('comment2');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $service = new Serviceletter();
            $service->company_id=$company;
            $service->department_id=$department;
            $service->employee_id=$employee;
            $service->jobtitle=$jobtitle;
            $service->join_date=$joindate;
            $service->end_date=$enddate;
            $service->comment1=$comment1;
            $service->comment2=$comment2;
            $service->status= '1';
            $service->created_by=Auth::id();
            $service->created_at=Carbon::now()->toDateTimeString();
            $service->save();
            
            Session::flash('message', 'The Employee Service Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('serviceletter');
        }else{
            $data = array(
                'company_id' => $company,
                'department_id' => $department,
                'employee_id' => $employee,
                'jobtitle' => $jobtitle,
                'join_date'=>$joindate,
                'end_date'=>$enddate,
                'comment1'=>$comment1,
                'comment2'=>$comment2,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            );
        
            Serviceletter::where('id', $recordID)
            ->update($data);

            Session::flash('message', 'The Employee Service Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('serviceletter');
        }
    }

    public function letterlist ()
    {
        $letters = DB::table('service_letter')
        ->leftjoin('companies', 'service_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'service_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'service_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles', 'service_letter.jobtitle', '=', 'job_titles.id')
        ->select('service_letter.*','employees.emp_name_with_initial As emp_name','job_titles.title As emptitle','companies.name As companyname','departments.name As department')
        ->whereIn('service_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Service-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('Service-letter-delete')){
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
        $permission = \Auth::user()->can('Service-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('service_letter')
        ->select('service_letter.*')
        ->where('service_letter.id', $id)
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
        Serviceletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee Service is Successfully Deleted']);

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
