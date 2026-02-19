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
use App\Helpers\EmployeeHelper;

class PromotionletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Promotion-letter-list');
        if (!$permission) {
            abort(403);
        }
        $job_titles=DB::table('job_titles')->select('*')->get();
        return view('EmployeeLetter.promotion',compact('job_titles'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Promotion-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $employee=$request->input('employee');
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
        elseif($recordOption == 2){
            $form_data = array(
                'company_id' => $company,
                'employee_id' => $employee,
                'old_department_id' => $old_department,
                'new_department_id' => $new_department,
                'old_jobtitle' => $old_jobtitle,
                'new_jobtitle' => $new_jobtitle,
                'date' => $date,
                'comment1' => $comment1,
                'comment2' => $comment2,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()->toDateTimeString()
            );
            
            Promotionletter::where('id', $recordID)->update($form_data);
            
            Session::flash('message', 'The Employee Promotion Details Successfully Updated');
            Session::flash('alert-class', 'alert-success');
            return redirect('promotionletter');
        }
    }

    public function letterlist ()
    {
        $letters = DB::table('promotion_letter')
        ->leftjoin('companies', 'promotion_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments', 'promotion_letter.old_department_id', '=', 'departments.id')
        ->leftjoin('employees', 'promotion_letter.employee_id', '=', 'employees.emp_id')
        ->leftjoin('job_titles as old_titles', 'promotion_letter.old_jobtitle', '=', 'old_titles.id')
        ->leftjoin('job_titles as new_titles', 'promotion_letter.new_jobtitle', '=', 'new_titles.id')
        ->select('promotion_letter.*','employees.emp_name_with_initial','employees.calling_name','old_titles.title As old_emptitle' ,'new_titles.title As new_emptitle','companies.name As companyname','departments.name as old_department','employees.emp_id as emp_id')
        ->whereIn('promotion_letter.status', [1, 2])
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
                    if(Auth::user()->can('Promotion-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('Promotion-letter-delete')){
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
        $permission = \Auth::user()->can('Promotion-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
            $data = DB::table('promotion_letter')
            ->leftJoin('companies', 'promotion_letter.company_id', '=', 'companies.id')
            ->leftJoin('departments as old_dept', 'promotion_letter.old_department_id', '=', 'old_dept.id')
            ->leftJoin('departments as new_dept', 'promotion_letter.new_department_id', '=', 'new_dept.id')
            ->leftJoin('employees', 'promotion_letter.employee_id', '=', 'employees.id')
            ->select(
                'promotion_letter.*',
                'companies.name as company_name',
                'old_dept.name as department_name',
                'new_dept.name as new_department_name',
                'employees.emp_name_with_initial as employee_name'
            )
            ->where('promotion_letter.id', $id)
            ->first(); 
            
            return response()->json(['result'=> $data]);
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

    public function getjobfilter($employee_id)
    {
        $emp_job_code = DB::table('employees')
            ->where('emp_id', '=', $employee_id)
            ->value('emp_job_code'); 

        $jobtitle = DB::table('job_titles')
            ->where('id', '=', $emp_job_code)
            ->get();

        return response()->json($jobtitle);
    }


}
