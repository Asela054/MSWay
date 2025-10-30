<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobattendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LocationAttendanceController extends Controller
{
    public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }



        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    public function GetLocations(Request $request)
    {
        $locations=DB::table('branches')->select('*')->get();

        $data = array(
            'locationlist' => $locations
        );

        return (new BaseController)->sendResponse($data, 'locationlist');

    }

    public function GetShiftType(Request $request)
    {
        $q = "
        SELECT * FROM `shift_types` 
        ";

        $data = DB::select($q);

        $data = array(
            'shift_types' => $data
        );

        return (new BaseController)->sendResponse($data, 'shift_types');
    }

    public function Insertlocationattendance(Request $request)
    {
        $location = $request->input('location_id');
        $attendancedate = $request->input('attendancedate');
        $empid = $request->input('emp_id');
        $on_time = $request->input('on_time');
        $off_time = $request->input('off_time');
        $reason = $request->input('reason');
        $userID = $request->input('userID');
        $location_status = $request->input('location_status');

        if($location_status == 1){

        $attendance = new Jobattendance();
        $attendance->attendance_date = $attendancedate;
        $attendance->employee_id = $empid;
        $attendance->shift_id = null;
        $attendance->on_time = $on_time;
        $attendance->off_time = $off_time;
        $attendance->reason = $reason;
        $attendance->location_id = $location;
        $attendance->allocation_id = null;
        $attendance->status = '1';
        $attendance->location_status = '1';
        $attendance->approve_status = '1';
        $attendance->created_by = $userID;
        $attendance->updated_by = '0';
        $attendance->save();


        $data = array(
            'emp_id' =>  $empid,
            'uid' =>  $empid,
            'state' => 1,
            'timestamp' => $on_time,
            'date' => $attendancedate,
            'approved' => 0,
            'type' => 255,
            'devicesno' => '-',
            'location' => $location
        );
         DB::table('attendances')->insert($data);

        //off time
        $data = array(
            'emp_id' => $empid,
            'uid' => $empid,
            'state' => 1,
            'timestamp' => $off_time,
            'date' => $attendancedate,
            'approved' => 0,
            'type' => 255,
            'devicesno' => '-',
            'location' => $location
        );
        DB::table('attendances')->insert($data);

        }else{

        $attendance = new Jobattendance();
        $attendance->attendance_date = $attendancedate;
        $attendance->employee_id = $empid;
        $attendance->shift_id = null;
        $attendance->on_time = $on_time;
        $attendance->off_time = $off_time;
        $attendance->reason = $reason;
        $attendance->location_id = $location;
        $attendance->allocation_id = null;
        $attendance->status = '1';
        $attendance->location_status = '2';
        $attendance->approve_status = '0';
        $attendance->created_by = $userID;
        $attendance->updated_by = '0';
        $attendance->save();
        }
        
        return (new BaseController)->sendResponse($attendance, 'Location Attendance Added Successfully');
    }

    public function Getlocationpoint(Request $request){
        $userID = $request->input('userid');

        $location = DB::table('employees')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->select('branches.*')
        ->where('employees.emp_id', $userID)
        ->get();

        $data = array(
            'employeelocation' => $location
        );

        return (new BaseController)->sendResponse($data, 'employeelocation');
    }

    

    //    public function GetLocationEmployees(Request $request)
    // {

    //     $location = $request->input('selectedlocation');
    //     $shift = $request->input('shift');
    //     $attendancedate = $request->input('attendancedate');

    //     $shifts = DB::table('shift_types')
    //     ->select('shift_types.*')
    //     ->where('shift_types.id','=', $shift)
    //     ->get(); 

    //     $todayTimedate = Carbon::parse($attendancedate)->format('Y-m-d');

    //     $onTime = Carbon::parse($attendancedate)
    //         ->setTimeFromTimeString($shifts[0]->onduty_time)
    //         ->format('Y-m-d H:i:s');

    //     $offTime = Carbon::parse($attendancedate)
    //         ->setTimeFromTimeString($shifts[0]->offduty_time)
    //         ->format('Y-m-d H:i:s');

    //     $allocation = DB::table('job_allocation')
    //     ->leftjoin('employees', 'job_allocation.employee_id', '=', 'employees.id')
    //     ->leftjoin('shift_types', 'job_allocation.shiftid', '=', 'shift_types.id')
    //     ->select('job_allocation.*','job_allocation.id As allocationid','employees.emp_name_with_initial As emp_name')
    //     ->where('job_allocation.status',1, 2)
    //     ->where('job_allocation.location_id', $location)
    //     ->where('job_allocation.shiftid', $shift)
    //     ->get();

    //     $data = array(
    //         'on_Time' => $onTime,
    //         'off_Time' => $offTime,
    //         'allocation' => $allocation,
    //     );

    //     return (new BaseController)->sendResponse($data, 'on_Time','off_Time','allocation');
    // }
}
