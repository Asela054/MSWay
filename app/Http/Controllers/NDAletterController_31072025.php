<?php

namespace App\Http\Controllers;

use App\NDAletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class NDAletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('NDA-letter-list');
        if (!$permission) {
            abort(403);
        }
        
        return view('EmployeeLetter.nda');
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('NDA-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee_f');
        $effect_date=$request->input('effect_date');
        // $comment1=$request->input('comment1');
        // $comment2=$request->input('comment2');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $service = new NDAletter();
            $service->company_id=$company;
            $service->department_id=$department;
            $service->employee_id=$employee;
            $service->effect_date=$effect_date;
            // $service->comment1=$comment1;
            // $service->comment2=$comment2;
            $service->status= '1';
            $service->created_by=Auth::id();
            $service->created_at=Carbon::now()->toDateTimeString();
            $service->save();
            
            Session::flash('message', 'The Employee NDA Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('NDAletter');
        }else{
            $data = array(
                'company_id' => $company,
                'department_id' => $department,
                'employee_id' => $employee,
                'effect_date' => $effect_date,
                // 'comment1'=>$comment1,
                // 'comment2'=>$comment2,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            );
        
            NDAletter::where('id', $recordID)
            ->update($data);

            Session::flash('message', 'The Employee NDA Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('NDAletter');
        }
    }

    public function letterlist ()
    {
        $letters = DB::table('nda_letter')
        ->leftjoin('companies', 'nda_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'nda_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'nda_letter.employee_id', '=', 'employees.id')
        ->select('nda_letter.*','employees.emp_name_with_initial As emp_name', 'employees.emp_id As emp_id', 'companies.name As companyname','departments.name As department')
        ->whereIn('nda_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('NDA-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('NDA-letter-delete')){
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
        $permission = \Auth::user()->can('NDA-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('nda_letter')
        ->leftjoin('companies', 'nda_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'nda_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees as emp', 'nda_letter.employee_id', '=', 'emp.id')
        ->select(
            'nda_letter.*',
            'companies.name as company_name',
            'departments.name as department_name', 
            'emp.emp_name_with_initial as emp_name'
        )
        ->where('nda_letter.id', $id)
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
        NDAletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee NDA Letter is Successfully Deleted']);

    }



}
