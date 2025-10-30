<?php

namespace App\Http\Controllers\Api;

use App\Employee;
use App\EmployeeAvailability;
use App\EmployeeTransfer;
use App\Leave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveRequest;
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class V1MainController extends Controller
{
    public function company_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $companyList = \App\Company::all();
        return (new BaseController)->sendResponse($companyList, 'Company List');
    }

    //department_list
    public function department_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $departmentList = \App\Department::all();
        return (new BaseController)->sendResponse($departmentList, 'Department List');
    }

    //allowance_list
    public function allowance_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $allowanceList = \App\employeeWorkRate::all();
        return (new BaseController)->sendResponse($allowanceList, 'Allowance List');
    }

    //employee_list
    public function employee_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $employeeList = \App\Employee::where('deleted', '0')->get();
        return (new BaseController)->sendResponse($employeeList, 'Employee List');
    }

    //attendance_store
    public function attendance_store(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

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
            'location' => 'required',
            'area_id' => 'required',

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
        $attendance->area_id = $request->area_id;

        $attendance->save();
        return (new BaseController)->sendResponse($attendance, 'Attendance Added');
    }

    //attendance_update
    public function attendance_update(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'emp_id' => 'required',
            'uid' => 'required',
            'state' => 'required',
            'timestamp' => 'required',
            'date' => 'required',
            'approved' => 'required',
            'type' => 'required',
            'devicesno' => 'required',
            'location' => 'required',
            'area_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $attendance = \App\Attendance::find($request->id);
        $attendance->emp_id = $request->emp_id;
        $attendance->uid = $request->uid;
        $attendance->state = $request->state;
        $attendance->timestamp = $request->timestamp;
        $attendance->date = $request->date;
        $attendance->approved = $request->approved;
        $attendance->type = $request->type;
        $attendance->devicesno = $request->devicesno;
        $attendance->location = $request->location;
        $attendance->area_id = $request->area_id;

        $attendance->save();
        return (new BaseController)->sendResponse($attendance, 'Attendance Updated');
    }

    //attendance_delete

    /**
     * @throws \Exception
     */
    public function attendance_delete(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $attendance = \App\Attendance::find($request->id);
        $attendance->delete();

        return (new BaseController)->sendResponse($attendance, 'Attendance Deleted');
    }

    //attendance_edit
    public function attendance_edit(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $attendance = \App\Attendance::find($request->id);
        return (new BaseController)->sendResponse($attendance, 'Attendance Found');
    }

    //employee_transfers_list
    public function employee_transfers_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $employeeList = \App\EmployeeTransfer::get();
        return (new BaseController)->sendResponse($employeeList, 'Employee Transfer List');
    }

    //employee_transfers_store
    public function employee_transfers_store(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if ($auth_status['status'] == false) {
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'emp_id' => 'required',
            'registered_location_id' => 'required',
            'transfer_location_id' => 'required',
            'record_date' => 'required',
            'in_time' => 'required',
            'out_time' => 'required',
            'charge_per_hour' => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employeeTransfer = new \App\EmployeeTransfer;
        $employeeTransfer->emp_id = $request->emp_id;
        $employeeTransfer->registered_location_id = $request->registered_location_id;
        $employeeTransfer->transfer_location_id = $request->transfer_location_id;
        $employeeTransfer->record_date = $request->record_date;
        $employeeTransfer->in_time = $request->in_time;
        $employeeTransfer->out_time = $request->out_time;
        $employeeTransfer->charge_per_hour = $request->charge_per_hour;

        $employeeTransfer->save();
        return (new BaseController)->sendResponse($employeeTransfer, 'Employee Transfer Added');

    }

    //employee_transfers_update
    public function employee_transfers_update(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'emp_id' => 'required',
            'registered_location_id' => 'required',
            'transfer_location_id' => 'required',
            'record_date' => 'required',
            'in_time' => 'required',
            'out_time' => 'required',
            'charge_per_hour' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employeeTransfer = \App\EmployeeTransfer::find($request->id);
        $employeeTransfer->emp_id = $request->emp_id;
        $employeeTransfer->registered_location_id = $request->registered_location_id;
        $employeeTransfer->transfer_location_id = $request->transfer_location_id;
        $employeeTransfer->record_date = $request->record_date;
        $employeeTransfer->in_time = $request->in_time;
        $employeeTransfer->out_time = $request->out_time;
        $employeeTransfer->charge_per_hour = $request->charge_per_hour;

        $employeeTransfer->save();
        return (new BaseController)->sendResponse($employeeTransfer, 'Employee Transfer Updated');
    }

    //employee_transfers_delete

    /**
     * @throws \Exception
     */
    public function employee_transfers_delete(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employeeTransfer = \App\EmployeeTransfer::find($request->id);
        $employeeTransfer->delete();

        return (new BaseController)->sendResponse($employeeTransfer, 'Employee Transfer Deleted');
    }

    //employee_transfers_approve
    public function employee_transfers_approve(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'approved_by' => 'required',
            'approved_at' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employeeTransfer = \App\EmployeeTransfer::find($request->id);
        $employeeTransfer->is_approved = 1;
        $employeeTransfer->approved_by = $request->approved_by;
        $employeeTransfer->approved_at = $request->approved_at;
        $employeeTransfer->save();

        return (new BaseController)->sendResponse($employeeTransfer, 'Employee Transfer Approved');
    }

    //employee_transfers_approved_list
    public function employee_transfers_approved_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $employeeList = \App\EmployeeTransfer::where('is_approved', 1)->get();
        return (new BaseController)->sendResponse($employeeList, 'Employee Transfer Approved List');
    }

    //employee_transfers_not_approved_list
    public function employee_transfers_not_approved_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $employeeList = \App\EmployeeTransfer::where('is_approved', 0)->get();
        return (new BaseController)->sendResponse($employeeList, 'Employee Transfer Not Approved List');
    }

    public function leave_apply_store(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'employee' => 'required',
            'leavetype' => 'required',
            'fromdate' => 'required',
            'todate' => 'required',
            'reson' => 'required',
            'half_short' => 'required', //0.25 OR 0.5

        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        $leave = new Leave;
        $leave->emp_id = $request->employee;
        $leave->leave_type = $request->leavetype;
        $leave->leave_from = $request->fromdate;
        $leave->leave_to = $request->todate;
        $leave->no_of_days = ($diff_days + $half_short);
        $leave->half_short = $half_short;
        $leave->reson = $request->reson;
        $leave->comment = $request->comment;
        $leave->emp_covering = $request->coveringemployee;
        $leave->leave_approv_person = $request->approveby;
        $leave->status = 'Pending';
        $leave->save();

        return (new BaseController)->sendResponse($leave, 'Leave Added');
    }

    public function leave_apply_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $leave_list = \App\Leave::get();
        return (new BaseController)->sendResponse($leave_list, 'Leaves List');
    }

    public function leave_apply_update(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'employee' => 'required',
            'leavetype' => 'required',
            'fromdate' => 'required',
            'todate' => 'required',
            'reson' => 'required',
            'half_short' => 'required', //0.25 OR 0.5
            'id' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        $no_of_days = 0;
        if($half_short != '1'){
            $no_of_days = ($diff_days + $half_short);
        }else{
            $no_of_days = $diff_days;
        }

        $form_data = array(
            'leave_type' => $request->leavetype,
            'leave_from' => $request->fromdate,
            'leave_to' => $request->todate,
            'no_of_days' => $no_of_days,
            'half_short' => $half_short,
            'reson' => $request->reson,
            'emp_covering' => $request->coveringemployee,
            'leave_approv_person' => $request->approveby,
            'status' => 'Pending'

        );

        Leave::whereId($request->id)->update($form_data);

        return (new BaseController)->sendResponse($request->id, 'Leave Updated');
    }

    public function leave_apply_delete(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leave = \App\Leave::find($request->id);
        $leave->delete();

        return (new BaseController)->sendResponse($leave, 'Leave Deleted');
    }

    public function leave_apply_approve(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        //validate request
        $validator = \Validator::make($request->all(), [
            'employee' => 'required',
            'status' => 'required', // Pending or Approved
            'leave_approv_person' => 'required',
            'id' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $form_data = array(
            'status' => $request->status,
            'leave_approv_person' => $request->leave_approv_person,
            'comment' => $request->comment,
        );

        Leave::whereId($request->id)->update($form_data);

        return (new BaseController)->sendResponse($request->id, 'Leave Updated');
    }

    public function leave_apply_approved_list(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $leave_list = \App\Leave::WHERE('status', 'approved')->get();
        return (new BaseController)->sendResponse($leave_list, 'Leaves List');
    }


    public function employee_salary_for_month(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $validator = \Validator::make($request->all(), [
            'month' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        //todo
        //if $request->employee exists return salary for the employee
        //otherwise return salary for all employees for selected month

    }

    public function employee_working_location(Request $request)
    {
        $secret_key = $request->secret_key;
        $auth_status = (new AuthController())->check_auth($secret_key);

        if($auth_status['status'] == false){
            return (new BaseController())->sendError($auth_status['error'], [], $auth_status['code']);
        }

        $validator = \Validator::make($request->all(), [
            'date' => 'required',
            'employee' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $data = EmployeeTransfer::where('record_date', $request->date)
            ->where('emp_id', $request->employee)
            ->with('registered_location')
            ->with('transfer_location')
            ->get();

        return (new BaseController)->sendResponse($data, 'Employee Work Location');

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

        $leaves = DB::table('leaves')
        ->join('leave_types', 'leaves.leave_type', '=', 'leave_types.id') 
        ->select('leaves.*', 'leave_types.leave_type as leave_type_name')
        ->where('leaves.status', $request->status)
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

    public function GetApprovedUpcomingLeavesForDashboard(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }
            $leaves = DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type', '=', 'leave_types.id') 
            ->select('leaves.*', 'leave_types.leave_type as leave_type_name')
            ->where('leaves.emp_id', $request->employee_id)
            ->where('leaves.status', 'Approved')
            ->whereDate('leaves.leave_from', '>=', $request->date)
            ->orderBy('leaves.id','DESC')
            ->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Employee ID']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leaves');
    }

    public function GetEmployeeProfileDetails(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employee = DB::table('employees')
                ->select(
                    'employees.*',
                    'companies.name as company_name',
                    'departments.name as department_name',
                    'shift_types.shift_name as shift_type_name'
                        )
            ->leftJoin('companies', 'employees.emp_company', '=', 'companies.id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->where('employees.emp_id', $request->employee_id)
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

    public function leaverequestinsert(Request $request)
    {

        $employee=$request->input('employee');
        $fromdate=$request->input('fromdate');
        $todate=$request->input('todate');
        $half_short=$request->input('half_short');
        $reason=$request->input('reason');

        $request = new LeaveRequest();
        $request->emp_id=$employee;
        $request->from_date=$fromdate;
        $request->to_date=$todate;
        $request->leave_category=$half_short;
        $request->reason=$reason;
        $request->status= '1';
        $request->created_by=$employee;
        $request->updated_by = '0';
        $request->approve_status = '0';
        $request->request_approve_status = '0';
        $request->save();

        return (new BaseController)->sendResponse($request, 'Leave Request Details Successfully Insert');
    }

    public function leaverequest_list(Request $request)
    {

        $employee = $request->get('employee');

        $query =  DB::table('leave_request')
        ->leftjoin('employees as emp', 'leave_request.emp_id', '=', 'emp.emp_id')
        ->leftjoin('departments', 'emp.emp_department', '=', 'departments.id')
        ->leftjoin('leaves', 'leave_request.id', '=', 'leaves.request_id')
        ->leftjoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
        ->select(
            'leave_request.*', 
            'emp.emp_name_with_initial as emp_name', 
            'departments.name as dep_name', 
            'leaves.leave_type as leave_type_id', 
            'leave_types.leave_type as leave_type_name', 
            'leaves.status as leave_status',
            'leaves.half_short as half_short'
        )
        ->where('leave_request.status', 1)
        ->where('leave_request.emp_id', $employee)
        ->get();

        $data = array(
            'leaverequests' => $query,
        );

        return (new BaseController)->sendResponse($data, 'leaverequests');

    }

    public function getemployeeleaverequest(Request $request)
    {
         $requestid = $request->get('requestid');

        $leaveRequests = DB::table('leave_request')
            ->select('leave_request.*')
            ->where('leave_request.id', $requestid)
            ->where('leave_request.status', 1)
            ->where('leave_request.approve_status', 0)
            ->where('leave_request.request_approve_status', 1)
            ->get()
            ->map(function ($row) {
                $leaveType = '';
                if ($row->leave_category == 0.25) {
                    $leaveType = 'Short Leave';
                } elseif ($row->leave_category == 0.5) {
                    $leaveType = 'Half Day';
                } elseif ($row->leave_category == 1.0) {
                    $leaveType = 'Full Day';
                }

                return [
                    'id' => $row->id,
                    'emp_id' => $row->emp_id,
                    'from_date' => $row->from_date,
                    'to_date' => $row->to_date,
                    'leave_category' => $row->leave_category,
                    'leave_type' => $leaveType
                ];
            });

        $data = [
            'leave_request' => $leaveRequests
        ];

        return (new BaseController)->sendResponse($data, 'Employee leave request retrieved successfully');
    }
    public function get_employee_monthlysummery(Request $request)
    {
        $selectedmonth = $request->input('selectedmonth');
        $emprecordid = $request->input('emprecordid');
        $empid = $request->input('empid');
        $empcompany = $request->input('empcompany');

        $closedate = \Carbon\Carbon::parse($selectedmonth)->endOfMonth()->format('Y-m-d');
           
        $monthworkingdaysdata=DB::table('employees')
                            ->leftJoin('job_categories','job_categories.id','employees.job_category_id')
                            ->select('employees.job_category_id','job_categories.emp_payroll_workdays')
                            ->where('employees.id',$emprecordid)
                            ->first();

        $monthworkingdays=$monthworkingdaysdata->emp_payroll_workdays;

        $work_days = (new \App\Attendance)->get_work_days($empid, $selectedmonth,$closedate);
                                       
        $working_week_days_arr = (new \App\Attendance)->get_working_week_days($empid, $selectedmonth,$closedate)['no_of_working_workdays'];
                                          
        $leave_days = (new \App\Attendance)->get_leave_days($empid, $selectedmonth,$closedate);
                                           
        $no_pay_days = (new \App\Attendance)->get_no_pay_days($empid, $selectedmonth,$closedate);
                         
                           
        $attendance_responseData= array(
            'workingdays'=>  $work_days,
            'absentdays'=>  ($monthworkingdays-$work_days),
            'working_week_days_arr'=>  $working_week_days_arr,
            'leave_days'=>  $leave_days,
            'no_pay_days'=>  $no_pay_days,
        );

        
        $payment_period = DB::table('employee_payslips')
        ->leftjoin('payroll_profiles','payroll_profiles.id','employee_payslips.payroll_profile_id')
        ->select('employee_payslips.id','employee_payslips.payment_period_id','employee_payslips.payment_period_fr','employee_payslips.payment_period_to')
        ->where('employee_payslips.payment_period_fr', 'LIKE', $selectedmonth.'-%')
        ->where('payroll_profiles.emp_id', $emprecordid)
        ->where('employee_payslips.payslip_cancel', '0')
        ->orderBy('employee_payslips.id', 'desc')  
        ->first();
        
        $payment_period_id=$payment_period->payment_period_id;
        $payment_period_fr=$payment_period->payment_period_fr;
        $payment_period_to=$payment_period->payment_period_to;

            $sqlslip="SELECT 
                            drv_emp.emp_payslip_id, 
                            drv_emp.emp_epfno, 
                            drv_emp.emp_first_name, 
                            drv_emp.location, 
                            drv_emp.payslip_held, 
                            drv_emp.payslip_approved, 
                            drv_info.fig_group_title, 
                            drv_info.fig_group, 
                            drv_info.fig_value AS fig_value, 
                            drv_info.epf_payable AS epf_payable, 
                            drv_info.remuneration_pssc, 
                            drv_info.remuneration_tcsc 
                        FROM 
                            (SELECT employee_payslips.id AS emp_payslip_id, 
                            employees.emp_id AS emp_epfno, 
                            employees.emp_name_with_initial AS emp_first_name, 
                            companies.name AS location, 
                            employee_payslips.payslip_held, 
                            employee_payslips.payslip_approved 
                        FROM `employee_payslips` 
                        INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id 
                        INNER JOIN employees ON payroll_profiles.emp_id=employees.id 
                        INNER JOIN companies ON employees.emp_company=companies.id 
                            WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.id=?  AND employee_payslips.payslip_cancel=0) AS drv_emp 
                        INNER JOIN 
                        (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value 
                        FROM employee_salary_payments 
                        WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
       
        
        $employee = DB::select($sqlslip, [$payment_period_id, $empcompany, $emprecordid, $payment_period_id]);
    

       
        $sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
		
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
		$sum_array = array('BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
		$cnt = 1;
		$act_payslip_id = '';
		$net_payslip_fig_value = 0;
		$emp_fig_totearn = 0;
		$emp_fig_otherearn = 0; 
		$emp_fig_totlost = 0;
		$emp_fig_otherlost = 0; 
		$emp_fig_tottax = 0;
		
		$rem_tot_bnp = 0;
		$rem_tot_fortax = 0;
		$rem_tot_earn = 0;
		$rem_tot_ded = 0;
		$rem_net_sal = 0;
		$rem_ded_other = 0;
		
		
		
        $conf_tl = DB::table('remuneration_taxations')
        ->where(['fig_calc_opt' => 'FIGPAYE', 'optspec_cancel' => 0])
        ->pluck('taxcalc_spec_code')
        ->toArray();

		
		foreach($employee as $r){

			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
				$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
				$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
				$emp_fig_tottax = 0;
			}

			if(!isset($emp_array[$cnt-1])){


				$emp_array[] = array('BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
				$rem_tot_bnp = 0;
				$rem_tot_fortax = 0;
				$rem_tot_earn = 0;
				$rem_tot_ded = 0;
				$rem_net_sal = 0;
				$rem_ded_other = 0;
			}
			
			
			$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
			
			if(isset($emp_array[$cnt-1][$fig_key])){
				$fig_group_val=$emp_array[$cnt-1][$fig_key];
				
				if($fig_key!='OTHER_REM'){
					$emp_array[$cnt-1][$fig_key]=(abs($r->fig_value)+$fig_group_val);
					$sum_array[$fig_key]+=abs($r->fig_value);
				}
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$emp_array[$cnt-1]['NETSAL']=$net_payslip_fig_value;
					
					$reg_net_sal=$sum_array['NETSAL']-$rem_net_sal;
					$sum_array['NETSAL']=($reg_net_sal+$net_payslip_fig_value);
					$rem_net_sal = $net_payslip_fig_value;
					
					if(in_array($r->remuneration_tcsc, $conf_tl)){
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
						$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
						$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
						$rem_tot_fortax = $emp_fig_tottax;
					}
					
					$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
					if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
						$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
						$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
						$rem_tot_earn = $emp_fig_totearn;
					}
					
					if($r->fig_value>=0){
						$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
						$emp_array[$cnt-1]['add_other']=$emp_fig_otherearn;//number_format((float)$emp_fig_otherearn, 2, '.', '');
						
						
					}else{
						if($fig_key!='NOPAY'){
							$emp_fig_totlost += $r->fig_value;
							$emp_array[$cnt-1]['tot_ded']=abs($emp_fig_totlost);//number_format((float)abs($emp_fig_totlost), 2, '.', '');
							
							$reg_tot_ded=$sum_array['tot_ded']-$rem_tot_ded;
							$sum_array['tot_ded']=($reg_tot_ded+abs($emp_fig_totlost));
							$rem_tot_ded = abs($emp_fig_totlost);
						}
						$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
						$emp_array[$cnt-1]['ded_other']=$emp_fig_otherlost;//number_format((float)$emp_fig_otherlost, 2, '.', '');
						
						$reg_ded_other=$sum_array['ded_other']-$rem_ded_other;
						$sum_array['ded_other']=($reg_ded_other+$emp_fig_otherlost);
						$rem_ded_other=$emp_fig_otherlost;
					}

				}
				
				if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
				
						$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
						$emp_array[$cnt-1]['tot_bnp']=$emp_tot_bnp;//number_format((float)$emp_tot_bnp, 2, '.', '');
						
						$reg_tot_bnp=$sum_array['tot_bnp']-$rem_tot_bnp;
						$sum_array['tot_bnp']=($reg_tot_bnp+$emp_tot_bnp);
						$rem_tot_bnp = $emp_tot_bnp;
					
				}
			}
		}
        
        $data = array(
            'result' => $attendance_responseData,
            'salaryresult'=>$sum_array
        );

        return (new BaseController)->sendResponse($data, 'result','salaryresult');
    }

    public function attendancelist(Request $request){

        $employee = $request->get('employee');
        
        $allocation = DB::table('job_attendance')
        ->leftjoin('employees', 'job_attendance.employee_id', '=', 'employees.id')
        ->leftjoin('job_location', 'job_attendance.location_id', '=', 'job_location.id')
        ->leftjoin('shift_types', 'job_attendance.shift_id', '=', 'shift_types.id')
        ->select('job_attendance.*','employees.emp_name_with_initial As emp_name','job_location.location_name','shift_types.shift_name')
        ->whereIn('job_attendance.status', [1, 2])
        ->where('job_attendance.employee_id', $employee)
        ->get();
       
        $data = array(
            'attendance' => $allocation,
        );
        return (new BaseController)->sendResponse($data, 'attendance');

    }


    public function attendanceinsert(Request $request){

        $rules = array(
            'employee' => 'required',
            'in_time' => 'required',
            'out_time' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $employees = DB::table('employees')
            ->join('branches', 'employees.emp_location', '=', 'branches.id')
            ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
            ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
            ->groupBy('fingerprint_devices.location')
            ->where('employees.emp_id', $request->employee)
            ->get();

        //in time
        $timestamp = $request->in_time;
        $data = array(
            'emp_id' => $request->employee,
            'uid' => $request->employee,
            'state' => 1,
            'timestamp' => $timestamp,
            'date' => $timestampdate = substr($timestamp, 0, -6),
            'approved' => 0,
            'type' => 255,
            'devicesno' => '-',
            'location' => 1
        );
         DB::table('attendances')->insert($data);

        //off time
        $offTimeStamp = $request->out_time;
        $data = array(
            'emp_id' => $request->employee,
            'uid' => $request->employee,
            'state' => 1,
            'timestamp' => $offTimeStamp,
            'date' => substr($offTimeStamp, 0, -6),
            'approved' => 0,
            'type' => 255,
            'devicesno' => '-',
            'location' => 1
        );
        DB::table('attendances')->insert($data);

        return (new BaseController)->sendResponse($request, 'Attendance Details Successfully Insert');
    }

    public function list_attendance(Request $request)
    {
        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query = DB::table('attendances as at1')
                    ->Join('employees', 'at1.emp_id', '=', 'employees.emp_id')
                    ->leftJoin('branches', 'at1.location', '=', 'branches.id')
                    ->select('at1.id',
                        'at1.emp_id',
                        'at1.uid',
                        'at1.state',
                        'at1.timestamp',
                        'at1.date',
                        'at1.approved',
                        'at1.type',
                        DB::raw('Min(at1.timestamp) as firsttimestamp'),
                        DB::raw('(CASE 
                            WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                            ELSE Max(at1.timestamp)
                            END) AS lasttimestamp'),
                        'employees.emp_name_with_initial',
                        'branches.location')
                       ->where(['employees.emp_id' => $employee])
                        ->whereBetween('at1.date', [$from_date, $to_date])
                       ->where(['at1.deleted_at' => null])
                       ->where(['approved' => '0'])
                       ->groupBy('at1.uid', 'at1.date')
                       ->get();
           
        $data = array(
            'attendance' => $query,
        );
        return (new BaseController)->sendResponse($data, 'attendance');
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
        
     public function Getapprovepersons(Request $request){

        $employees = DB::table('employees')
        ->select('emp_name_with_initial','emp_id','id')
        ->where('deleted', 0)
        ->where('leave_approve_person', 1)
        ->get();

        $data = array(
            'approve_persons' => $employees
        );

        return (new BaseController)->sendResponse($data, 'employees');
    }
}
