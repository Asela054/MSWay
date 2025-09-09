<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Joballocation;
use App\Helpers\EmployeeHelper;
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
        $employees=DB::table('employees')->select('id','emp_id','emp_name_with_initial','emp_job_code')->where('deleted',0)->get();
        $locations=DB::table('branches')->select('*')->get();
        return view('jobmanagement.jobattendance',compact('locations','employees'));
    }


    public function getemplist(Request $request)
    {
        $location = $request->input('attlocation');
        $attendancedate = $request->input('attendancedate');

        $allocation = DB::table('job_allocation')
            ->leftjoin('employees', 'job_allocation.employee_id', '=', 'employees.emp_id')
            ->leftjoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id') // Changed to get shift from employees table
            ->select(
                'job_allocation.*',
                'job_allocation.id as allocationid',
                'employees.emp_name_with_initial as emp_name',
                'shift_types.onduty_time',
                'shift_types.offduty_time'
            )
            ->where('job_allocation.status', 1)
            ->where('job_allocation.location_id', $location)
            ->get();

        $htmlemployee = '';

        foreach ($allocation as $row) {
            $todayTimedate = Carbon::parse($attendancedate)->format('Y-m-d');
            $todayTime = Carbon::parse($row->onduty_time)->format('H:i');
            $offtime = Carbon::parse($row->offduty_time)->format('H:i');

            $htmlemployee .= '<tr>';
            $htmlemployee .= '<td><select name="employee" id="employee" class="employee form-control form-control-sm"><option value="' . $row->employee_id . '">'. $row->emp_name.'</option></select></td>';  
            $htmlemployee .= '<td> <input type="datetime-local" id="empontime" name="empontime" class="form-control form-control-sm"  value="' .$todayTimedate . 'T' .$todayTime. '" required></td>'; 
            $htmlemployee .= '<td><input type="datetime-local" id="empofftime" name="empofftime" class="form-control form-control-sm" value="'  .$todayTimedate . 'T' .$offtime.  '" required></td>';
            $htmlemployee .= '<td> <button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>';
            $htmlemployee .= '<td class="d-none"><input type="number" id="allocationid" name="allocationid" value="' . $row->allocationid . '" ></td>';
            $htmlemployee .= '</tr>';
        }
        return response()->json(['result' => $htmlemployee]);
    }

    public function insert(Request $request)
    {
        $permission = \Auth::user()->can('Job-Attendance-create');
        if (!$permission) {
            abort(403);
        }

        $location = $request->input('allocation');
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
            $attendance->on_time = $ontime;
            $attendance->off_time = $offtime;
            $attendance->location_id = $location;
            $attendance->allocation_id = $allocationid;
            $attendance->status = '1';
            $attendance->location_status = '1';
            $attendance->approve_status = '1';
            $attendance->created_by = Auth::id();
            $attendance->updated_by = '0';
            $attendance->save();
        }
        return response()->json(['success' => 'Job Attendance Added successfully.']);
    }

    public function attendancelist()
    {
        $allocation = DB::table('job_attendance')
        ->leftjoin('employees', 'job_attendance.employee_id', '=', 'employees.emp_id')
        ->leftjoin('branches', 'job_attendance.location_id', '=', 'branches.id')
        ->leftjoin('shift_types', 'job_attendance.shift_id', '=', 'shift_types.id')
        ->select('job_attendance.*','employees.emp_name_with_initial','employees.calling_name','branches.location','shift_types.shift_name')
        ->whereIn('job_attendance.status', [1, 2])
        ->get();
        return Datatables::of($allocation)
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
    
    public function update(Request $request)
    {
        $permission = \Auth::user()->can('Job-Attendance-edit');
        if (!$permission) {
            abort(403);
        }

        $editemployee = $request->input('editemployee');
        $attendancedateedit = $request->input('attendancedateedit');
        $empontime = $request->input('empontime');
        $empofftime = $request->input('empofftime');
        $locationedit = $request->input('locationedit');
        $hidden_id = $request->input('hidden_id');

            $data = array(
                'attendance_date' => $attendancedateedit,
                'employee_id' => $editemployee,
                'on_time' => $empontime,
                'off_time' => $empofftime,
                'location_id' => $locationedit,
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

    public function single_employee(Request $request)
    {
        $permission = Auth::user()->can('Job-Attendance-create');
        if (!$permission) {
            abort(403);
        }

        $empid = $request->input('employee_single');
        $location = $request->input('locationsingle');
        $attendancedate = $request->input('singleattendancedate');
        $ontime = $request->input('singleempontime');
        $offtime = $request->input('singleempofftime');
    
        $attendance = new Jobattendance();
        $attendance->attendance_date = $attendancedate;
        $attendance->employee_id = $empid;
        $attendance->on_time = $ontime;
        $attendance->off_time = $offtime;
        $attendance->location_id = $location;
        $attendance->status = '1';
        $attendance->location_status = '1';
        $attendance->approve_status = '0';
        $attendance->created_by = Auth::id();
        $attendance->updated_by = '0';
        $attendance->save();
        
        return response()->json(['success' => 'Job Attendance Added successfully.']);
    }


}
