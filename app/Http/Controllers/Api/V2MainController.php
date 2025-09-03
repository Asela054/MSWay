<?php

namespace App\Http\Controllers\api;

use App\Employee;
use App\EmployeeAbsent;
use App\EmployeeAvailability;
use App\Leave;
use App\ReqGeoLoc;
use App\Routes;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class V2MainController extends Controller
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

    public function GetEmployeeProfileDetails(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employee = Employee::where('emp_id', $request->employee_id)
            ->with('country')
            ->with('company')
            ->with('area')
            ->with('location')
            ->with('department')
            ->with('shiftType')
            ->first();

        $date = date('Y-m-d');
        $employee_availability = EmployeeAvailability::where('emp_id', $request->employee_id)->where('date', $date)->first();

        $emp_avail_data = array();

        if(empty($employee_availability)){
            $emp_avail_data['session'] = '';
        }else{
            $emp_avail_data['session'] = $employee_availability->session;
        }

        $data = array(
            'employee' => $employee,
            'employee_availability' => $emp_avail_data
        );

        if(EMPTY($employee)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Employee ID']);
        }

        return (new BaseController)->sendResponse($data, 'Employee Details');
    }

    public function GetApprovedUpcomingLeavesForDashboard(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leaves = Leave::
            with('leave_type')
            ->where('emp_id', $request->employee_id)
            ->where('status', 'Approved')
            ->orderBy('id','DESC')
            ->take(3)->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Employee ID']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leaves');
    }

    public function CheckingForInstructions(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }


        $rec = ReqGeoLoc::where('is_checked', false)
            ->where('emp_id', $request->employee_id)
            ->orderBy('id','DESC')
            ->first();

        if(EMPTY($rec)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($rec, 'CheckingForInstructions');
    }

    public function SendGeoLocation(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'record_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj_geo_loc = ReqGeoLoc::find($request->record_id);

        if(empty($obj_geo_loc)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Record ID']);
        }

        $obj_geo_loc->latitude = $request->latitude;
        $obj_geo_loc->longitude = $request->longitude;
        $obj_geo_loc->is_checked = true;
        $obj_geo_loc->save();

        return (new BaseController)->sendResponse($obj_geo_loc, 'Record Updated');

    }

    public function GetLeaveTypes(Request $request)
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

    public function MarkEmployeeAbsent(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj = new EmployeeAbsent();
        $obj->emp_id = $request->employee_id;
        $obj->from_date = $request->from_date;
        $obj->to_date = $request->to_date;
        $obj->save();

        return (new BaseController)->sendResponse($obj, 'Record Inserted');

    }

    public function GetRoutesList(Request $request)
    {
        $routes = Routes::
            with('emp_route')
            ->with('vehicle_type_rel')
            ->get();

        if(EMPTY($routes)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($routes, 'Routes List');
    }

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
            $obj->session = $request->session;
            $obj->save();
        }else{
            $record->session = $request->session;
            $record->save();
        }

        return (new BaseController)->sendResponse(array(), 'Record Inserted');

    }

    public function GetLeaveListByStatus(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leaves = Leave::where('status', $request->status )-> with('leave_type')
            ->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leaves List');
    }

    public function GetLeaveDetailsToView(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leaves = Leave::where('id', $request->id )
            ->first();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leave Details');
    }

    public function UpdateLeaveStatus(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj_leave = Leave::where('id', $request->id)->first();

        if(EMPTY($obj_leave)){
            return (new BaseController)->sendError('Invalid Leave', ['error' => 'Invalid User']);
        }

        $obj_leave->status = $request->status;
        $obj_leave->save();

        return (new BaseController)->sendResponse($obj_leave, 'Leave Updated');

    }

}
