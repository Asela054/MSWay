<?php

namespace App\Http\Controllers;

use App\Promotionletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class PromotionletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Promotion-letter-list');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code','emp_join_date','emp_department')->where('deleted',0)->get();
        $job_titles=DB::table('job_titles')->select('*')->get();
        $departments=DB::table('departments')->select('*')->get();
        return view('EmployeeLetter.promotion',compact('companies','employees','job_titles','departments'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Promotion-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $employee=$request->input('employee_f');
        $old_department=$request->input('old_department');
        $new_department=$request->input('new_department');
        $old_jobtitle=$request->input('old_jobtitle');
        $new_jobtitle=$request->input('new_jobtitle');
        $date=$request->input('date');
        $comment1=$request->input('comment1');
        $comment2=$request->input('comment2');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $promotion = new Promotionletter();
            $promotion->company_id=$company;
            $promotion->employee_id=$employee;
            $promotion->old_department_id=$old_department;
            $promotion->new_department_id=$new_department;
            $promotion->old_jobtitle=$old_jobtitle;
            $promotion->new_jobtitle=$new_jobtitle;
            $promotion->date=$date;
            $promotion->comment1=$comment1;
            $promotion->comment2=$comment2;
            $promotion->status= '1';
            $promotion->created_by=Auth::id();
            $promotion->created_at=Carbon::now()->toDateTimeString();
            $promotion->save();
            
            Session::flash('message', 'The Employee Promotion Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('promotionletter');
        }
    }

    public function letterlist ()
    {
        $letters = DB::table('promotion_letter')
        ->leftjoin('companies', 'promotion_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments', 'promotion_letter.old_department_id', '=', 'departments.id')
        ->leftjoin('employees', 'promotion_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles as old_titles', 'promotion_letter.old_jobtitle', '=', 'old_titles.id')
        ->leftjoin('job_titles as new_titles', 'promotion_letter.new_jobtitle', '=', 'new_titles.id')
        ->select('promotion_letter.*','employees.emp_name_with_initial As emp_name','old_titles.title As old_emptitle' ,'new_titles.title As new_emptitle','companies.name As companyname','departments.name as old_department')
        ->whereIn('promotion_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Promotion-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('Promotion-letter-delete')){
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
        $permission = \Auth::user()->can('Promotion-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('promotion_letter')
        ->select('promotion_letter.*')
        ->where('promotion_letter.id', $id)
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
        Promotionletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee Promotion details is Successfully Deleted']);

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
