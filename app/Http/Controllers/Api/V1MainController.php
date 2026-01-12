<?php

namespace App\Http\Controllers\Api;

use App\Employee;
use App\EmployeeAvailability;
use App\EmployeeTransfer;
use App\Leave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

   
        
  
}
