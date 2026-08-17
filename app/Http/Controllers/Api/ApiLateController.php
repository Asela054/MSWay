<?php

namespace App\Http\Controllers\Api;

use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;
use App\LateAttendance;
use App\Leave;
use App\Services\LatePolicyService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateTime;

class ApiLateController extends Controller
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

     public function approved_late_list(Request $request)
    {

        $department = $request->get('department');
        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query = DB::query()
            ->select('ela.*',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'branches.location',
                'departments.name as dep_name')
            ->from('employee_late_attendances as ela')
            ->Join('employees', 'ela.emp_id', '=', 'employees.emp_id')
            ->leftJoin('attendances as at1', 'at1.id', '=', 'ela.attendance_id')
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department');

        if ($department != '') {
            $query->where(['departments.id' => $department]);
        }

        if ($employee != '') {
            $query->where(['employees.emp_id' => $employee]);
        }

        if ($from_date != '' && $to_date != '') {
            $query->whereBetween('ela.date', [$from_date, $to_date]);
        }

        $sql = $query->get();


        $data = array(
            'latelist' => $sql
        );
        return (new BaseController)->sendResponse($data, 'latelist');
    }

     public function late_attendacne_delete(Request $request)
    {
        $id = Request('id');

        $lateattendance = LateAttendance::findOrFail($id);

        $empid = $lateattendance->emp_id;
        $date = $lateattendance->date;
        $attendanceid = $lateattendance->attendance_id;

        DB::table('employee_late_attendance_minites')
        ->where('attendance_id', $attendanceid)
        ->delete();
        
        $emp_leave = DB::table('leaves')
        ->where('emp_id', $empid)
        ->where('leave_from', $date)
        ->first();

        $message = "";
        if($emp_leave){
            $id_leave = $emp_leave->id;
            $status = $emp_leave->status;

            if($status === "Approved"){

                $message = "There is an approved leave for this date. Please remove it before deleting the late attendance record";

            }else{
                $leaves = Leave::findOrFail($id_leave);
                $leaves->delete();

                $deletedCount = DB::table('employee_late_attendance_minites')
                    ->where('attendance_date', $date)
                    ->where('emp_id', $empid)
                    ->delete();

                $lateattendance->delete();

                $message = "Record deleted";
            }

        }else{
            $lateattendance->delete();
            $message = "Record deleted";
        }

        return (new BaseController)->sendResponse($id, $message);
    }



}
