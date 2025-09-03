<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Joballocation;
use App\Jobattendance;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;


class JobattendanceController extends Controller
{
    public function index()
    {
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code')->where('deleted',0)->get();
        $locations=DB::table('job_location')->select('*')->where('status',1)->get();
        $shifts=DB::table('shift_types')->select('*')->where('deleted',0)->get();
        return view('jobmanagement.jobattendance',compact('locations','employees','shifts'));
    }

    public function getemplist(Request $request)
    {
        $location = $request->input('attlocation');
        $shift = $request->input('shift');
        $attendancedate = $request->input('attendancedate');

        $shifts = DB::table('shift_types')
        ->select('shift_types.*')
        ->where('shift_types.id','=', $shift)
        ->get(); 

        $todayTimedate = Carbon::parse($attendancedate)->format('Y-m-d');
        $todayTime = Carbon::parse($shifts[0]->onduty_time)->format('H:i');
        $offtime = Carbon::parse($shifts[0]->offduty_time)->format('H:i');

        $allocation = DB::table('job_allocation')
        ->leftjoin('employees', 'job_allocation.employee_id', '=', 'employees.id')
        ->leftjoin('shift_types', 'job_allocation.shiftid', '=', 'shift_types.id')
        ->select('job_allocation.*','job_allocation.id As allocationid','employees.emp_name_with_initial As emp_name')
        ->where('job_allocation.status',1, 2)
        ->where('job_allocation.location_id', $location)
        ->where('job_allocation.shiftid', $shift)
        ->get();

        $htmlemployee = '';

        foreach ($allocation as $row) {
            $htmlemployee .= '<tr>';
            $htmlemployee .= '<td><select name="employee" id="employee" class="employee form-control form-control-sm"><option value="' . $row->employee_id . '">'. $row->emp_name.'</option></select></td>';  
            $htmlemployee .= '<td> <input type="datetime-local" id="empontime" name="empontime" class="form-control form-control-sm"  value="' .$todayTimedate . 'T' .$todayTime. '" required></td>'; 
            $htmlemployee .= '<td><input type="datetime-local" id="empofftime" name="empofftime" class="form-control form-control-sm" value="'  .$todayTimedate . 'T' .$offtime.  '" required></td>';
            $htmlemployee .= '<td> <button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>';
            $htmlemployee .= '<td class="d-none"><input type="number" id="allocationid" name="allocationid" value="' . $row->allocationid . '" ></td>';
            $htmlemployee .= '</tr>';
        }
        return response() ->json(['result'=>  $htmlemployee]);
    }

    public function insert(Request $request)
    {
        $permission = \Auth::user()->can('Job-Attendance-create');
        if (!$permission) {
            abort(403);
        }

        $location = $request->input('allocation');
        $shift = $request->input('shift');
        $attendancedate = $request->input('attendancedate');
        $tableData = $request->input('tableData');

        foreach ($tableData as $rowtabledata) {
            $empid = $rowtabledata['col_1'];
            $ontime = $rowtabledata['col_2'];
            $offtime = $rowtabledata['col_3'];
            $allocationid = $rowtabledata['col_5'];

            $attendance = new Jobattendance();
            $attendance->attendance_date = $attendancedate;
            $attendance->employee_id = $empid;
            $attendance->shift_id = $shift;
            $attendance->on_time = $ontime;
            $attendance->off_time = $offtime;
            $attendance->location_id = $location;
            $attendance->allocation_id = $allocationid;
            $attendance->status = '1';
            $attendance->created_by = Auth::id();
            $attendance->updated_by = '0';
            $attendance->save();
        }
        return response()->json(['success' => 'Job Attendance Added successfully.']);
    }

    public function attendancelist()
    {
        $allocation = DB::table('job_attendance')
        ->leftjoin('employees', 'job_attendance.employee_id', '=', 'employees.id')
        ->leftjoin('job_location', 'job_attendance.location_id', '=', 'job_location.id')
        ->leftjoin('shift_types', 'job_attendance.shift_id', '=', 'shift_types.id')
        ->select('job_attendance.*','employees.emp_name_with_initial As emp_name','job_location.location_name','shift_types.shift_name')
        ->whereIn('job_attendance.status', [1, 2])
        ->get();
        return Datatables::of($allocation)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Job-Attendance-edit')){
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('Job-Attendance-delete')){
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
            return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function edit(Request $request)
    {
        $permission = \Auth::user()->can('Job-Attendance-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('job_attendance')
        ->select('job_attendance.*')
        ->where('job_attendance.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }
    
    public function update(Request $request){
        $permission = \Auth::user()->can('Job-Attendance-edit');
        if (!$permission) {
            abort(403);
        }

        $editemployee = $request->input('editemployee');
        $attendancedateedit = $request->input('attendancedateedit');
        $empontime = $request->input('empontime');
        $empofftime = $request->input('empofftime');
        $hidden_id = $request->input('hidden_id');

            $data = array(
                'attendance_date' => $attendancedateedit,
                'employee_id' => $editemployee,
                'on_time' => $empontime,
                'off_time' => $empofftime,
                'updated_by' => Auth::id(),
            );
        
            Jobattendance::where('id', $hidden_id)
            ->update($data);

        return response()->json(['success' => 'Job Attendance Updated successfully.']);
    }

    public function delete(Request $request){
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Jobattendance::where('id',$id)
        ->update($form_data);

          return response()->json(['success' => 'Job Attendance is Successfully Deleted']);
    }
}
