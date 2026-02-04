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
use App\Helpers\EmployeeHelper;

class ResignletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Resign-letter-list');
        if (!$permission) {
            abort(403);
        }
        $job_titles=DB::table('job_titles')->select('*')->get();
        return view('EmployeeLetter.resign',compact('job_titles'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Resign-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee');
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
        elseif($recordOption == 2){
            $form_data = array(
                'company_id' => $company,
                'department_id' => $department,
                'employee_id' => $employee,
                'jobtitle' => $jobtitle,
                'join_date' => $joindate,
                'last_date' => $lastdate,
                'reason' => $reason,
                'comment1' => $comment1,
                'comment2' => $comment2,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()->toDateTimeString()
            );
            
            Resignletter::where('id', $recordID)->update($form_data);
            
            Session::flash('message', 'The Employee Resign Details Successfully Updated');
            Session::flash('alert-class', 'alert-success');
            return redirect('resignletter');
        }
        
    }

    public function letterlist ()
    {
        $letters = DB::table('resign_letter')
        ->leftjoin('companies', 'resign_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'resign_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'resign_letter.employee_id', '=', 'employees.emp_id')
        ->leftjoin('job_titles', 'resign_letter.jobtitle', '=', 'job_titles.id')
        ->select('resign_letter.*','employees.emp_name_with_initial','employees.calling_name','job_titles.title As emptitle','companies.name As companyname','departments.name As department','employees.emp_id')
        ->whereIn('resign_letter.status', [1, 2])
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
                    if(Auth::user()->can('Resign-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('Resign-letter-delete')){
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
        $permission = \Auth::user()->can('Resign-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
            $data = DB::table('resign_letter')
                ->leftJoin('companies', 'resign_letter.company_id', '=', 'companies.id')
                ->leftJoin('departments', 'resign_letter.department_id', '=', 'departments.id')
                ->leftJoin('employees', 'resign_letter.employee_id', '=', 'employees.emp_id')
                ->select(
                    'resign_letter.*',
                    'companies.name as company_name',
                    'departments.name as department_name',
                    'employees.emp_name_with_initial as employee_name'
                )
                ->where('resign_letter.id', $id)
                ->first(); 
            return response()->json(['result'=> $data]);
        }
    }

    public function delete(Request $request)
    {
        $permission = \Auth::user()->can('Resign-letter-delete');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Resignletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee Resign is Successfully Deleted']);

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
