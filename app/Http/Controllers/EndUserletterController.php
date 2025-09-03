<?php

namespace App\Http\Controllers;

use App\EndUserletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class EndUserletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('end-user-letter-list');
        if (!$permission) {
            abort(403);
        }
        
        return view('EmployeeLetter.end_user');
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('end-user-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $department=$request->input('department');
        $employee=$request->input('employee_f');
        $employee_r=$request->input('employee_r');
        $effect_date=$request->input('effect_date');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){

            $service = new EndUserletter();
            $service->company_id=$company;
            $service->department_id=$department;
            $service->emp_id=$employee;
            $service->effect_date=$effect_date;
            $service->rep_emp_id=$employee_r;
            $service->status= '1';
            $service->created_by=Auth::id();
            $service->created_at=Carbon::now()->toDateTimeString();
            $service->save();
            
            Session::flash('message', 'The Employee End User Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('end_user_letter');
        }else{
            $data = array(
                'company_id' => $company,
                'department_id' => $department,
                'emp_id' => $employee,
                'effect_date' => $effect_date,
                'rep_emp_id' => $employee_r,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            );
        
            EndUserletter::where('id', $recordID)
            ->update($data);

            Session::flash('message', 'The Employee End User Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('end_user_letter');
        }
    }

    public function letterlist ()
    {
        $letters = DB::table('end_user_letter')
        ->leftjoin('companies', 'end_user_letter.company_id', '=', 'companies.id')
        ->leftjoin('departments', 'end_user_letter.department_id', '=', 'departments.id')
        ->leftjoin('employees', 'end_user_letter.emp_id', '=', 'employees.id')
        ->select('end_user_letter.*','employees.emp_name_with_initial As emp_name', 'employees.emp_id As emp_id', 'companies.name As companyname','departments.name As department')
        ->whereIn('end_user_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('end-user-letter-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('end-user-letter-delete')){
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
        $permission = \Auth::user()->can('end-user-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
            $data = DB::table('end_user_letter')
            ->leftjoin('companies', 'end_user_letter.company_id', '=', 'companies.id')
            ->leftjoin('departments', 'end_user_letter.department_id', '=', 'departments.id')
            ->leftjoin('employees as emp', 'end_user_letter.emp_id', '=', 'emp.id')
            ->leftjoin('employees as rep_emp', 'end_user_letter.rep_emp_id', '=', 'rep_emp.id')
            ->select(
                'end_user_letter.*',
                'companies.name as company_name',
                'departments.name as department_name', 
                'emp.emp_name_with_initial as emp_name',
                'rep_emp.emp_name_with_initial as rep_emp_name'
            )
            ->where('end_user_letter.id', $id)
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
        EndUserletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee End User Letter is Successfully Deleted']);

    }

    public function getDevices($id)
    {
        $devices = DB::table('employee_assigned_devices')
            ->join('employees', 'employees.id', '=', 'employee_assigned_devices.emp_id')
            ->where('employee_assigned_devices.emp_id', $id)
            ->select(
                'employee_assigned_devices.device_type',
                'employee_assigned_devices.model_number',
                'employee_assigned_devices.serial_number',
                'employee_assigned_devices.assigned_date'
            )
            ->get();

        return response()->json($devices);
    }



}
