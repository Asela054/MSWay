<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Joballocation;
use App\Jobattendance;
use App\Services\AttendancePolicyService;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class JobAttendaceApproveController extends Controller
{
    protected $attendancePolicyService;

    public function __construct(AttendancePolicyService $attendancePolicyService)
    {
        $this->middleware('auth');

        $this->attendancePolicyService = $attendancePolicyService;
    }


     public function index()
    {
          $permission = \Auth::user()->can('Job-Attendance-Approve-list');
        if (!$permission) {
            abort(403);
        }
        $locations=DB::table('branches')->select('*')->get();
        return view('jobmanagement.locationattendace_approve',compact('locations'));
    }

     public function unauthorizeattendace()
    {
          $permission = \Auth::user()->can('Job-Attendance-Approve-list');
        if (!$permission) {
            abort(403);
        }
        $locations=DB::table('branches')->select('*')->get();
        return view('jobmanagement.unauthorizelocationattendace_approve',compact('locations'));
    }
    public function approveattendace(Request $request)
    {

        $permission = \Auth::user()->can('MealAllowanceApprove-approve');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('records');
        $location = $request->input('location');
        $attendace_type = $request->input('attendace_type');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        $current_date_time = Carbon::now()->toDateTimeString();

         foreach ($dataarry as $row) {
            $id = $row['id'];
            $empid = $row['empid'];
            $emp_name = $row['emp_name'];
            $attendance_date = $row['date'];
            $on_time = $row['on_time'];
            $off_time = $row['off_time'];
            $location_id = $row['location_id'];
            $reason = $row['reason'];
            

            $data = array(
                'approve_status' => 1,
                'updated_by' => Auth::id(),
            );
        
            Jobattendance::where('id', $id)
            ->update($data);


             $employees = DB::table('employees')
            ->select('employees.emp_location as location')
            ->where('employees.emp_id', $empid)
            ->first();

            if ($on_time != '') {
               $this->attendancePolicyService->attendanceInsertsingle_dep($empid, $on_time,$employees->location , $attendance_date);
            }

            if ($off_time != '') {
                $this->attendancePolicyService->attendanceInsertsingle_dep($empid, $off_time,$employees->location , $attendance_date);
            
            }
            
        }

        return response()->json(['success' => 'Location Attendance is successfully Approved']);
    }

}