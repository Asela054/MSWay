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
use App\Helpers\EmployeeHelper;


class WarningletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Warning-letter-list');
        if (!$permission) {
            abort(403);
        }
        $job_titles=DB::table('job_titles')->select('*')->get();
        return view('EmployeeLetter.warning',compact('job_titles'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Warning-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee');
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
        elseif($recordOption == 2){
            $form_data = array(
                'company_id' => $company,
                'department_id' => $department,
                'employee_id' => $employee,
                'jobtitle' => $jobtitle,
                'date' => $date,
                'reason' => $reason,
                'description' => $description,
                'comment1' => $comment1,
                'comment2' => $comment2,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()->toDateTimeString()
            );
            
            Warningletter::where('id', $recordID)->update($form_data);
            
            Session::flash('message', 'The Employee Warning Details Successfully Updated');
            Session::flash('alert-class', 'alert-success');
            return redirect('warningletter');
        }
        
    }

    public function letterlist ()
    {
        $letters = DB::table('warning_letter')
        ->leftjoin('companies', 'warning_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'warning_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'warning_letter.employee_id', '=', 'employees.emp_id')
        ->leftjoin('job_titles', 'warning_letter.jobtitle', '=', 'job_titles.id')
        ->select('warning_letter.*','employees.emp_name_with_initial','employees.calling_name','job_titles.title As emptitle','companies.name As companyname','departments.name As department' , 'employees.emp_id As emp_id')
        ->whereIn('warning_letter.status', [1, 2])
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
                    if(Auth::user()->can('Warning-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('Warning-letter-delete')){
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
        $permission = \Auth::user()->can('Warning-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
            $data = DB::table('warning_letter')
                ->leftjoin('companies', 'warning_letter.company_id', '=', 'companies.id')
                ->leftjoin('departments', 'warning_letter.department_id', '=', 'departments.id')
                ->leftjoin('employees', 'warning_letter.employee_id', '=', 'employees.emp_id')
                ->select(
                    'warning_letter.*',
                    'companies.name as company_name',
                    'departments.name as department_name',
                    'employees.emp_name_with_initial as employee_name'
                )
                ->where('warning_letter.id', $id)
                ->first(); 
            
            return response()->json(['result'=> $data]);
        }
    }

    public function delete(Request $request)
    {
        $permission = \Auth::user()->can('Warning-letter-delete');
        if (!$permission) {
            abort(403);
        }
        
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now()->toDateTimeString()
        );
        Warningletter::where('id',$id)
            ->update($form_data);

        return response()->json(['success' => 'The Employee Warning is Successfully Deleted']);
    }

    public function getjobfilter($employee_id)
    {
        $jobtitle = DB::table('employees')
            ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->where('employees.emp_id', '=', $employee_id)
            ->select('job_titles.id', 'job_titles.title')
            ->get();

        return response()->json($jobtitle);
    }


}
