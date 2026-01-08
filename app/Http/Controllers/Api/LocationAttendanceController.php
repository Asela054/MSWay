<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobattendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;

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
        ->first();

         // If employee has no location, get all branches
        if (!$location || is_null($location->id)) {
            $location = DB::table('branches')->get();
        } else {
            $location = collect([$location]);
        }

        $data = array(
            'employeelocation' => $location
        );

        return (new BaseController)->sendResponse($data, 'employeelocation');
    }

    public function Getattendanceshift(Request $request)
    {

         $empid = $request->input('empid');
         $date = $request->input('date');

        $attendanceinsertstatus = 1 ;
         $attendancedate =  $date;

         $empshift = DB::table('employees')
            ->select('emp_id', 'emp_shift')
            ->where('emp_id', $empid)
            ->first();

            if ($empshift) {
              
                $emprosterinfo = DB::table('employee_roster_details')
                    ->select('emp_id', 'shift_id')
                    ->where('emp_id', $empid)
                    ->where('work_date', $date)
                    ->first();

                if ($emprosterinfo) {
                    $empshiftid = $emprosterinfo->shift_id;   
                }
                else {
                    $empshiftid = $empshift->emp_shift; 
                }

                   $shift = DB::table('shift_types')
                        ->where('id', $empshiftid)
                        ->first();

                   if ($shift && $shift->off_next_day == '1') {
                    $previous_day = (new DateTime($date))->modify('-1 day')->format('Y-m-d');

                    $attendancecheckinfo = DB::table('job_attendance')
                        ->select('*')
                        ->where('employee_id', $empid)
                        ->where('attendance_date', $previous_day)
                        ->where('shift_id', $empshiftid)
                        ->first();


                         if ($attendancecheckinfo && $attendancecheckinfo->on_time != '' && $attendancecheckinfo->off_time == '') {

                            $attendanceinsertstatus = 2 ;
                            $attendancedate = $previous_day;
                         }else{
                             $attendanceinsertstatus = 1 ;

                         }

                    }else{

                          $attendancecheckinfo = DB::table('job_attendance')
                        ->select('*')
                        ->where('employee_id', $empid)
                        ->where('attendance_date', $date)
                        ->where('shift_id', $empshiftid)
                        ->first();


                         if ($attendancecheckinfo && $attendancecheckinfo->on_time != '' && $attendancecheckinfo->off_time == '') {

                            $attendanceinsertstatus = 2 ;
                         }else{
                             $attendanceinsertstatus = 1 ;
                         }
                    }

                      $data = array(
                            'attendaceshift' => $empshiftid,
                            'attendancedate' => $attendancedate,
                            'attendanceinserttype' => $attendanceinsertstatus
                        );
            }

        return (new BaseController)->sendResponse($data, 'attendaceshift','attendancedate','attendanceinserttype');

    }


      public function Singlelocationattendanceinsert(Request $request)
    {
        $location = $request->input('location_id');
        $empid = $request->input('emp_id');
        $timestamp = $request->input('timestamp');
        $attendaceshift = $request->input('attendaceshift');
        $attendancedate = $request->input('attendancedate');
        $attendanceinserttype = $request->input('attendanceinserttype');
        $reason = $request->input('reason');
        $location_status = $request->input('location_status');

         if($location_status == 1){
             if($attendanceinserttype == 1){
                $attendance = new Jobattendance();
                $attendance->attendance_date = $attendancedate;
                $attendance->employee_id = $empid;
                $attendance->shift_id =  $attendaceshift;
                $attendance->on_time = $timestamp;
                $attendance->off_time = null;
                $attendance->reason = $reason;
                $attendance->location_id = $location;
                $attendance->allocation_id = null;
                $attendance->status = '1';
                $attendance->location_status = '1';
                $attendance->approve_status = '1';
                $attendance->created_by = $empid;
                $attendance->updated_by = '0';
                $attendance->save();

                } else{

                    $attendance = DB::table('job_attendance')
                                ->select('*')
                                ->where('employee_id', $empid)
                                ->where('attendance_date', $attendancedate)
                                ->where('shift_id', $attendaceshift)
                                ->first();

                    if ($attendance) {

                            DB::table('job_attendance')
                                ->where('employee_id', $empid)
                                ->where('attendance_date', $attendancedate)
                                ->where('shift_id', $attendaceshift)
                                ->whereNull('off_time')
                                ->update([
                                    'off_time' => $timestamp,
                                    'updated_by' => $empid ]);
                    }
                }

          
                $data = array(
                'emp_id' =>  $empid,
                'uid' =>  $empid,
                'state' => 1,
                'timestamp' => $timestamp,
                'date' => $attendancedate,
                'approved' => 0,
                'type' => 255,
                'devicesno' => '-',
                'location' => $location );
                DB::table('attendances')->insert($data);



         }else{

             if($attendanceinserttype == 1){
                $attendance = new Jobattendance();
                $attendance->attendance_date = $attendancedate;
                $attendance->employee_id = $empid;
                $attendance->shift_id =  $attendaceshift;
                $attendance->on_time = $timestamp;
                $attendance->off_time = null;
                $attendance->reason = $reason;
                $attendance->location_id = $location;
                $attendance->allocation_id = null;
                $attendance->status = '1';
                $attendance->location_status = '2';
                $attendance->approve_status = '0';
                $attendance->created_by = $empid;
                $attendance->updated_by = '0';
                $attendance->save();

                } else{

                    $attendance = DB::table('job_attendance')
                                ->select('*')
                                ->where('employee_id', $empid)
                                ->where('attendance_date', $attendancedate)
                                ->where('shift_id', $attendaceshift)
                                ->first();

                    if ($attendance) {

                            DB::table('job_attendance')
                                ->where('employee_id', $empid)
                                ->where('attendance_date', $attendancedate)
                                ->where('shift_id', $attendaceshift)
                                ->whereNull('off_time')
                                ->update([
                                    'reason' =>  $reason,
                                    'off_time' => $timestamp,
                                    'location_status' => '2',
                                    'approve_status' => '0',
                                    'updated_by' => $empid ]);
                    }
                }

         }

        return (new BaseController)->sendResponse($attendance, 'Location Attendance Added Successfully');
    }


}
