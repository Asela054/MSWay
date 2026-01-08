<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\EmployeeAvailability;
use DateTime;

class APIAttendanceController extends Controller
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

    // Mark employee availability in attendance section dashboard
     public function MarkEmployeeAvailability(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'date' => 'required',
            'session' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        //check if record find
        $record = EmployeeAvailability::where('emp_id', $request->employee_id)->where('date', $request->date)->first();

        if (EMPTY($record)){
            $obj = new EmployeeAvailability();
            $obj->emp_id = $request->employee_id;
            $obj->date = $request->date;
            $obj->availability = '1';
            $obj->session = $request->session;
            $obj->save();
        }else{
            $record->session = $request->session;
            $record->save();
        }

        return (new BaseController)->sendResponse(array(), 'Record Inserted');

    }

     public function attendance_list_for_month_edit(Request $request)
    {

        $emp_id = $request->get('employee');
        $month = $request->get('month');

        $attendances = DB::table('attendances as at1')
            ->select(
                'at1.*',
                DB::raw('Min(at1.timestamp) as firsttimestamp'),
                DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                'employees.emp_id'
            )
            ->join('employees', 'at1.uid', '=', 'employees.emp_id')
            ->where('employees.emp_id', $emp_id)
            ->where('date', 'like', $month . '%')
            ->where('at1.deleted_at', null)
            ->groupBy('at1.uid', 'at1.date')
            ->get();

        $attendances->transform(function ($attendance) {
            $timestamp = Carbon::parse($attendance->firsttimestamp);
            $lasttimestamp = Carbon::parse($attendance->lasttimestamp);

              $totalMinutes = $timestamp->diffInMinutes($lasttimestamp);

            // Convert minutes to hours with 2 decimal points
            $attendance->duration_hours = round($totalMinutes / 60, 2);
            
            // Keep minutes for reference if needed
            $attendance->duration_minutes = $totalMinutes;

            $attendance->firsttime_rfc = $timestamp->format('Y-m-d\TH:i');
            $attendance->lasttime_rfc = $lasttimestamp->format('Y-m-d\TH:i');
            $attendance->firsttime_24 = $timestamp->format('Y-m-d H:i');
            $attendance->lasttime_24 = $lasttimestamp->format('Y-m-d H:i');

            return $attendance;
        });

        $data = [
            'attendances' => $attendances,
        ];

        return (new BaseController)->sendResponse($data, 'Attendances retrieved successfully');
    }

}
