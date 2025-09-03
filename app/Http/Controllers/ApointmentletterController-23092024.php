<?php

namespace App\Http\Controllers;

use App\Apointmentletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class ApointmentletterController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Appointment-letter-list');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code')->where('deleted',0)->get();
        $jobtitles=DB::table('job_titles')->select('*')->get();
        return view('AppointmentLetter.appointment',compact('companies','employees','jobtitles'));
    }

    public function insert(Request $request){
        $permission = \Auth::user()->can('Appointment-letter-create');
        if (!$permission) {
            abort(403);
        }

        $company=$request->input('company');
        $employee=$request->input('employee');
        $jobtitle=$request->input('jobtitle');
        $letterdate=$request->input('letterdate');
        $compensation=$request->input('compensation');
        $from_date=$request->input('from_date');
        $to_date=$request->input('to_date');
        $workinghours=$request->input('workinghours');
        $leaves=$request->input('leaves');
        $termination=$request->input('termination');
        $recordOption=$request->input('recordOption');
        $recordID=$request->input('recordID');

        if( $recordOption == 1){
            $appointment = new Apointmentletter();
            $appointment->employee_id=$employee;
            $appointment->date=$letterdate;
            $appointment->jobtitle=$jobtitle;
            $appointment->compensation=$compensation;
            $appointment->probation_from=$from_date;
            $appointment->probation_to=$to_date;
            $appointment->working_hours=$workinghours;
            $appointment->leaves=$leaves;
            $appointment->terminations=$termination;
            $appointment->status= '1';
            $appointment->company_id=$company;
            $appointment->created_by=Auth::id();
            $appointment->save();

            Session::flash('message', 'The Employee Appointment Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('appoinementletter');
        }else{
            $data = array(
                'employee_id' => $employee,
                'date' => $letterdate,
                'jobtitle' => $jobtitle,
                'compensation' => $compensation,
                'probation_from' => $from_date,
                'probation_to' => $to_date,
                'working_hours' => $workinghours,
                'leaves' => $leaves,
                'terminations' => $termination,
                'company_id' => $company,
                'updated_by' => Auth::id(),
            );
        
            Apointmentletter::where('id', $recordID)
            ->update($data);
            
            Session::flash('message', 'The Employee Appointment Details Successfully Saved');
            Session::flash('alert-class', 'alert-success');
            return redirect('appoinementletter');
        }
    }


    public function letterlist ()
    {
        $letters = DB::table('appointment_letter')
        ->leftjoin('employees', 'appointment_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles', 'appointment_letter.jobtitle', '=', 'job_titles.id')
        ->leftjoin('companies', 'appointment_letter.company_id', '=', 'companies.id')
        ->select('appointment_letter.*','employees.emp_name_with_initial As emp_name','job_titles.title As emptitle','companies.name As companyname')
        ->whereIn('appointment_letter.status', [1, 2])
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Appointment-letter-edit')){
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('Appointment-letter-status')){
                        if($row->status == 1){
                            $btn .= ' <a href="'.route('appoinementletterstatus', ['id' => $row->id, 'status' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                        }else{
                            $btn .= '&nbsp;<a href="'.route('appoinementletterstatus', ['id' => $row->id, 'status' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                        }
                    }
                    if(Auth::user()->can('Appointment-letter-delete')){
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
        $permission = \Auth::user()->can('Appointment-letter-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('appointment_letter')
        ->select('appointment_letter.*')
        ->where('appointment_letter.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }

    public function status($id,$statusid){
        $permission = \Auth::user()->can('Appointment-letter-status');
        if (!$permission) {
            abort(403);
        }

        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
                'updated_by' => Auth::id()
            );
            Apointmentletter::where('id',$id)
            ->update($form_data);

            return redirect()->route('appoinementletter');
        } else{
            $form_data = array(
                'status' =>  '2',
                'updated_by' => Auth::id()
            );
            Apointmentletter::where('id',$id)
            ->update($form_data);

            return redirect()->route('appoinementletter');
        }
    }

    public function delete(Request $request)
    {
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Apointmentletter::where('id',$id)
        ->update($form_data);

    return response()->json(['success' => 'The Employee Appointment is Successfully Deleted']);

    }

    public function printdata(Request $request)
    {
        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('appointment_letter')
        ->leftjoin('employees', 'appointment_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles', 'appointment_letter.jobtitle', '=', 'job_titles.id')
        ->leftjoin('companies', 'appointment_letter.company_id', '=', 'companies.id')
        ->select('appointment_letter.*','employees.*','job_titles.title As emptitle','companies.*')
        ->where('appointment_letter.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }


}
