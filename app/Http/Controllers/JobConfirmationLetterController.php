<?php

namespace App\Http\Controllers;

use App\JobConfirmationLetter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;
use App\Helpers\EmployeeHelper;

class JobConfirmationLetterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('JobConfirmation-letter-list');
        if (!$permission) {
            abort(403);
        }
        $job_titles=DB::table('job_titles')->select('*')->get();
        return view('EmployeeLetter.jobConfirmation',compact('job_titles'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('JobConfirmation-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee');
        $jobtitle=$request->input('jobtitle');
        $startdate=$request->input('start_date');
        $enddate=$request->input('end_date');
        $confirmationdate=$request->input('confirmation_date');
        $comment=$request->input('comment');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $service = new JobConfirmationLetter();
            $service->company_id=$company;
            $service->department_id=$department;
            $service->employee_id=$employee;
            $service->jobtitle=$jobtitle;
            $service->start_date=$startdate;
            $service->end_date=$enddate;
            $service->confirmation_date=$confirmationdate;
            $service->comment=$comment;
            $service->status= '1';
            $service->created_by=Auth::id();
            $service->created_at=Carbon::now()->toDateTimeString();
            $service->save();
            
            Session::flash('message', 'The Employee Job Confirmation Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('jobconfirmationletter');
        }else{
            $data = array(
                'company_id' => $company,
                'department_id' => $department,
                'employee_id' => $employee,
                'jobtitle' => $jobtitle,
                'start_date'=>$startdate,
                'end_date'=>$enddate,
                'confirmation_date'=>$confirmationdate,
                'comment'=>$comment,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            );
        
            JobConfirmationLetter::where('id', $recordID)
            ->update($data);

            Session::flash('message', 'The Employee Job Confirmation Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('jobconfirmationletter');
        }
    }

    public function letterlist ()
    {
        $letters = DB::table('job_confirmation_letter')
        ->leftjoin('companies', 'job_confirmation_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'job_confirmation_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'job_confirmation_letter.employee_id', '=', 'employees.emp_id')
        ->leftjoin('job_titles', 'job_confirmation_letter.jobtitle', '=', 'job_titles.id')
        ->select('job_confirmation_letter.*','employees.emp_name_with_initial','employees.calling_name','job_titles.title As emptitle','companies.name As companyname','departments.name As department','employees.emp_id')
        ->whereIn('job_confirmation_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('employee_display', function ($row) {
                   return EmployeeHelper::getDisplayName($row);
                   
        })
        ->filterColumn('employee_display', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('employees.emp_name_with_initial', 'like', "%{$keyword}%")
                ->orWhere('employees.calling_name', 'like', "%{$keyword}%")
                ->orWhere('employees.emp_id', 'like', "%{$keyword}%");
            });
        })
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('JobConfirmation-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('JobConfirmation-letter-delete')){
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
                    $btn .= ' <button name="print" id="'.$row->id.'" class="print btn btn-info btn-sm"><i class="fas fa-print"></i></button>';
            return $btn;
        })
       
        ->rawColumns(['action'])
        ->make(true);
    }

   

    public function edit(Request $request)
    {
        $permission = \Auth::user()->can('JobConfirmation-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
            $data = DB::table('job_confirmation_letter')
            ->leftjoin('companies', 'job_confirmation_letter.company_id', '=', 'companies.id')
            ->leftjoin('departments', 'job_confirmation_letter.department_id', '=', 'departments.id')
            ->leftjoin('employees', 'job_confirmation_letter.employee_id', '=', 'employees.emp_id')
            ->select('job_confirmation_letter.*', 
                    'companies.name as company_name',
                    'departments.name as department_name',
                    'employees.emp_name_with_initial as employee_name')
            ->where('job_confirmation_letter.id', $id)
            ->first(); 
            
            return response()->json(['result'=> $data]);
        }
    }

    public function delete(Request $request)
    {
        $permission = \Auth::user()->can('JobConfirmation-letter-delete');
        if (!$permission) {
            abort(403);
        }
        
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now()->toDateTimeString()  
        );
        JobConfirmationLetter::where('id',$id)->update($form_data);

        return response()->json(['success' => 'The Employee Job Confirmation is Successfully Deleted']);
    }

    public function getEmployeeDetails($employee_id)
    {
        // Get job title based on employee's job code
        $emp_job_code = DB::table('employees')
            ->where('emp_id', '=', $employee_id)
            ->value('emp_job_code');

        $jobTitle = DB::table('job_titles')
            ->where('id', '=', $emp_job_code)
            ->get();

        // Get join date
        $joinDate = DB::table('employees')
            ->select('emp_join_date')
            ->where('emp_id', '=', $employee_id)
            ->get();

        return response()->json([
            'jobTitle' => $jobTitle,
            'joinDate' => $joinDate
        ]);
    }



}
