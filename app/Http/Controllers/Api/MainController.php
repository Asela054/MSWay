<?php

namespace App\Http\Controllers\Api;

use App\Employee;
use App\Leave;
use App\LeaveDetail;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');
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

    public function getCustomerBranches(Request $request)
    {
        $q = "
            SELECT cb.* 
            FROM customerbranches cb 
        ";

        $data = DB::select($q);

        $data = array(
            'customer_branches' => $data
        );

        return (new BaseController)->sendResponse($data, 'customer_branches');
    }

    public function attendanceStore(Request $request)
    {
        //validate request
        $validator = \Validator::make($request->all(), [
            'emp_id' => 'required',
            'uid' => 'required',
            'state' => 'required',
            'timestamp' => 'required',
            'date' => 'required',
            'approved' => 'required',
            'type' => 'required',
            'devicesno' => 'required',
            'location' => 'required'

        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $time_stamp = date('H:i:s', strtotime($request->timestamp));
        $time_stamp =  $request->date.' '.$time_stamp;

        $attendance = new \App\Attendance;
        $attendance->emp_id = $request->emp_id;
        $attendance->uid = $request->uid;
        $attendance->state = $request->state;
        $attendance->timestamp = $time_stamp;
        $attendance->date = $request->date;
        $attendance->approved = $request->approved;
        $attendance->type = $request->type;
        $attendance->devicesno = $request->devicesno;
        $attendance->location = $request->location;

        $attendance->save();
        return (new BaseController)->sendResponse($attendance, 'Attendance Added');
    }

    public function getEmployeeInfo(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'branch_id' => 'required',
            'date' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $q = "
            SELECT * 
            FROM empallocations ea 
            LEFT JOIN empallocationdetails ead ON ead.allocation_id = ea.id 
            LEFT JOIN employees e ON e.emp_id = ead.emp_id
            LEFT JOIN shifts s ON s.id = ea.shift_id
            WHERE ea.customerbranch_id = '$request->branch_id' 
            AND ea.date = '$request->date'
        ";

        $data = DB::select($q);

        $data = array(
            'employee_info' => $data
        );

        return (new BaseController)->sendResponse($data, 'employee_info');
    }

    // public function empLocationStore(Request $request)
    // {
    //     //validate request
    //     $validator = \Validator::make($request->all(), [
    //         'emp_id' => 'required',
    //         'location_id' => 'required',
    //         'longitude' => 'required',
    //         'latitude' => 'required'
    //     ]);

    //     if($validator->fails()){
    //         return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
    //     }

    //     $empLocation = new EmpLocation;
    //     $empLocation->emp_id = $request->emp_id;
    //     $empLocation->location_id = $request->location_id;
    //     $empLocation->longitude = $request->longitude;
    //     $empLocation->latitude = $request->latitude;

    //     $empLocation->save();
    //     return (new BaseController)->sendResponse($empLocation, 'Emp Location Added');
    // }

    public function GetLeaveTypes(Request $request)
    {
        $q = "
            SELECT * 
            FROM  leave_types 
        ";

        $data = DB::select($q);

        $data = array(
            'leave_types' => $data
        );

        return (new BaseController)->sendResponse($data, 'leave_types');
    }

    public function GetLeavesList(Request $request)
    {
        $leaves = Leave::with('leave_type')
            ->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($leaves, 'leave_types');
    }

    public function GetLeaveBalance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employee = Employee::where('emp_id', $request->employee_id)->first();
        if($employee == NULL){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Record ID']);
        }

        $emp_join_date = isset($employee->emp_join_date) ? $employee->emp_join_date : false;
        $join_year = Carbon::parse($emp_join_date)->year;
        $join_month = Carbon::parse($emp_join_date)->month;
        $join_date = Carbon::parse($emp_join_date)->day;
        $full_date = '2022-'.$join_month.'-'.$join_date;

        $q_data = DB::table('quater_leaves')
            ->where('from_date', '<', $full_date)
            ->where('to_date', '>', $full_date)
            ->first();

        $like_from_date = date('Y').'-01-01';
        $like_from_date2 = date('Y').'-12-31';

        $total_taken_annual_leaves = DB::table('leaves')
            ->where('leaves.emp_id', '=', $employee->emp_id)
            ->whereBetween('leaves.leave_from', [$like_from_date, $like_from_date2])
            ->where('leaves.leave_type', '=', '1')
            ->get()->toArray();

        $current_year_taken_a_l = 0;
        foreach ($total_taken_annual_leaves as $tta){
            $leave_from = $tta->leave_from;
            $leave_to = $tta->leave_to;

            $leave_from_year = Carbon::parse($leave_from)->year;
            $leave_to_year = Carbon::parse($leave_to)->year;

            if($leave_from_year != $leave_to_year){
                //get current year leaves for that record
                $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                $diff_in_days = $to->diffInDays($from);
                $current_year_taken_a_l += $diff_in_days;

                $jan_data = DB::table('leaves')
                    ->where('leaves.id', '=', $tta->id)
                    ->first();

                $firstDayOfMonth = Carbon::parse($jan_data->leave_to)->startOfMonth()->toDateString();
                $to_t = \Carbon\Carbon::createFromFormat('Y-m-d', $jan_data->leave_to);
                $from_t = \Carbon\Carbon::createFromFormat('Y-m-d', $firstDayOfMonth);

                $diff_in_days_f = $to_t->diffInDays($from_t);
                $current_year_taken_a_l += $diff_in_days_f;

            }else{
                $current_year_taken_a_l += $tta->no_of_days;
            }
        }

        $like_from_date_cas = date('Y').'-01-01';
        $like_from_date2_cas = date('Y').'-12-31';
        $total_taken_casual_leaves = DB::table('leaves')
            ->where('leaves.emp_id', '=', $request->employee_id)
            ->whereBetween('leaves.leave_from', [$like_from_date_cas, $like_from_date2_cas])
            ->where('leaves.leave_type', '=', '2')
            ->get()->toArray();

        $current_year_taken_c_l = 0;
        foreach ($total_taken_casual_leaves as $tta){
            $leave_from = $tta->leave_from;
            $leave_to = $tta->leave_to;

            $leave_from_year = Carbon::parse($leave_from)->year;
            $leave_to_year = Carbon::parse($leave_to)->year;

            if($leave_from_year != $leave_to_year){
                //get current year leaves for that record
                $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                $diff_in_days = $to->diffInDays($from);
                $current_year_taken_c_l += $diff_in_days;
            }else{
                $current_year_taken_c_l += $tta->no_of_days;
            }
        }


        $leave_msg = '';
        $casual_leaves = 0;
        if($join_year == date('Y')){
            $annual_leaves = $q_data->leaves;
            $leave_msg = "Employee can have only a half day per month in Casual Leaves. (Employee joined in current year)";
        }else{
            $annual_leaves = 14;
            $casual_leaves = 7;
        }

        $total_no_of_annual_leaves = $annual_leaves;
        $total_no_of_casual_leaves = $casual_leaves;

        $available_no_of_annual_leaves = $total_no_of_annual_leaves - $current_year_taken_a_l;
        $available_no_of_casual_leaves = $total_no_of_casual_leaves - $current_year_taken_c_l;

        if($employee->emp_status != 2){
            $emp_status = DB::table('employment_statuses')->where('id', $employee->emp_status)->first();
            $leave_msg = 'Casual Leaves - '.$emp_status->emp_status.' Employee can have only a half day per month (Not a permanent employee)';
        }

        $results = array(
            "total_no_of_annual_leaves" => $total_no_of_annual_leaves,
            "total_no_of_casual_leaves" => $total_no_of_casual_leaves,
            "total_taken_annual_leaves" => $current_year_taken_a_l,
            "total_taken_casual_leaves" => $current_year_taken_c_l,
            "available_no_of_annual_leaves" => $available_no_of_annual_leaves,
            "available_no_of_casual_leaves" => $available_no_of_casual_leaves,
            "leave_msg" => $leave_msg
        );
        $annual_arr = array(
            'leave_type_id' => 2,
            'leave_type_name' => 'Annual',
            'total' => $total_no_of_annual_leaves,
            'taken' => $current_year_taken_a_l,
            'available' => $available_no_of_annual_leaves
        );

        $casual_arr = array(
            'leave_type_id' => 1,
            'leave_type_name' => 'Casual',
            'total' => $total_no_of_casual_leaves,
            'taken' => $current_year_taken_c_l,
            'available' => $available_no_of_casual_leaves
        );

        $main_arr = array();
        array_push($main_arr, $annual_arr);
        array_push($main_arr, $casual_arr);

        //return response()->json($results);

        return (new BaseController)->sendResponse($main_arr, 'Leave Details');
    }


    public function ApplyLeave(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee' => 'required',
            'leavetype' => 'required',
            'fromdate' => 'required',
            'todate' => 'required',
            'reson' => 'required',
            'no_of_days' => 'required',
            'half_short' => 'required',
            'coveringemployee' => 'required',
            'approveby' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        $leave = new Leave;
        $leave->emp_id = $request->input('employee');
        $leave->leave_type = $request->input('leavetype');
        $leave->leave_from = $request->input('fromdate');
        $leave->leave_to = $request->input('todate');
        $leave->no_of_days = $request->input('no_of_days');
        $leave->half_short = $half_short;
        $leave->reson = $request->input('reson');
        $leave->comment = $request->input('comment');
        $leave->emp_covering = $request->input('coveringemployee');
        $leave->leave_approv_person = $request->input('approveby');
        $leave->status = 'Pending';
        $leave->save();

        $users = DB::table('leave_details')
            ->where('emp_id', $request->employee)
            ->count();

        if ($users == 0) {
            $leavedetails = new LeaveDetail;
            $leavedetails->emp_id = $request->input('employee');
            $leavedetails->leave_type = $request->input('leavetype');
            $assign_leave = $request->input('assign_leave');
            $total_leave = $assign_leave - $diff_days;
            $leavedetails->total_leave = $total_leave;
            $leavedetails->save();

        } else {
            DB::table('leave_details')
                ->where('emp_id', $request->employee)
                ->where('leave_type', $request->leavetype)
                ->decrement('total_leave', $diff_days);
        }


        return (new BaseController)->sendResponse($leave, 'Success!');
    }



}
