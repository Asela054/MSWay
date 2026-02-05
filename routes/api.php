<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Api" middleware group. Enjoy building your Api!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/AuthenticateUser',['uses' => '\App\Http\Controllers\Api\AuthController@AuthenticateUser', 'as' => 'AuthenticateUser']);
Route::post('v2/UpdatePasswordFromForgotPassword', ['uses' => '\App\Http\Controllers\Api\AuthController@UpdatePasswordFromForgotPassword', 'as' => 'UpdatePasswordFromForgotPassword']);
Route::post('v2/UpdatePassword', ['uses' => '\App\Http\Controllers\Api\AuthController@UpdatePassword', 'as' => 'UpdatePassword']);
Route::post('v2/resetPasswordRequestOTP',['uses' => '\App\Http\Controllers\Api\AuthController@resetPasswordRequestOTP', 'as' => 'resetPasswordRequestOTP']);

Route::post('v1/company_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@company_list', 'as' => 'company_list']);
Route::post('v1/department_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@department_list', 'as' => 'department_list']);
Route::post('v1/allowance_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@allowance_list', 'as' => 'allowance_list']);
Route::post('v1/employee_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_list', 'as' => 'employee_list']);
Route::post('v1/attendance_store', ['uses' => '\App\Http\Controllers\Api\V1MainController@attendance_store', 'as' => 'attendance_store']);
Route::post('v1/attendance_update', ['uses' => '\App\Http\Controllers\Api\V1MainController@attendance_update', 'as' => 'attendance_update']);
Route::post('v1/attendance_delete', ['uses' => '\App\Http\Controllers\Api\V1MainController@attendance_delete', 'as' => 'attendance_delete']);
//attendance_edit
Route::post('v1/attendance_edit', ['uses' => '\App\Http\Controllers\Api\V1MainController@attendance_edit', 'as' => 'attendance_edit']);

//employee_transfers_list
Route::post('v1/employee_transfers_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_transfers_list', 'as' => 'employee_transfers_list']);
//employee_transfers_store
Route::post('v1/employee_transfers_store', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_transfers_store', 'as' => 'employee_transfers_store']);
//employee_transfers_update
Route::post('v1/employee_transfers_update', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_transfers_update', 'as' => 'employee_transfers_update']);
//employee_transfers_delete
Route::post('v1/employee_transfers_delete', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_transfers_delete', 'as' => 'employee_transfers_delete']);
//employee_transfers_approve
Route::post('v1/employee_transfers_approve', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_transfers_approve', 'as' => 'employee_transfers_approve']);
//employee_transfers_approved_list
Route::post('v1/employee_transfers_approved_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_transfers_approved_list', 'as' => 'employee_transfers_approved_list']);
//employee_transfers_not_approved_list
Route::post('v1/employee_transfers_not_approved_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_transfers_not_approved_list', 'as' => 'employee_transfers_not_approved_list']);



Route::post('v1/leave_apply_store', ['uses' => '\App\Http\Controllers\Api\V1MainController@leave_apply_store', 'as' => 'leave_apply_store']);
Route::post('v1/leave_apply_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@leave_apply_list', 'as' => 'leave_apply_list']);
Route::post('v1/leave_apply_update', ['uses' => '\App\Http\Controllers\Api\V1MainController@leave_apply_update', 'as' => 'leave_apply_update']);
Route::post('v1/leave_apply_delete', ['uses' => '\App\Http\Controllers\Api\V1MainController@leave_apply_delete', 'as' => 'leave_apply_delete']);
Route::post('v1/leave_apply_approve', ['uses' => '\App\Http\Controllers\Api\V1MainController@leave_apply_approve', 'as' => 'leave_apply_approve']);
Route::post('v1/leave_apply_approved_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@leave_apply_approved_list', 'as' => 'leave_apply_approved_list']);
Route::post('v1/employee_salary_for_month', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_salary_for_month', 'as' => 'employee_salary_for_month']);
Route::post('v1/employee_working_location', ['uses' => '\App\Http\Controllers\Api\V1MainController@employee_working_location', 'as' => 'employee_working_location']);
Route::post('v1/GetLeaveListByStatus', ['uses' => '\App\Http\Controllers\Api\V1MainController@GetLeaveListByStatus', 'as' => 'GetLeaveListByStatus']);
Route::post('v1/GetLeaveDetailsToView', ['uses' => '\App\Http\Controllers\Api\V1MainController@GetLeaveDetailsToView', 'as' => 'GetLeaveDetailsToView']);

Route::post('v1/GetEmployeeProfileDetails', ['uses' => '\App\Http\Controllers\Api\V1MainController@GetEmployeeProfileDetails', 'as' => 'GetEmployeeProfileDetails']);
Route::post('v1/UpdateLeaveStatus', ['uses' => '\App\Http\Controllers\Api\V1MainController@UpdateLeaveStatus', 'as' => 'UpdateLeaveStatus']);

Route::post('v1/attendance_list', ['uses' => '\App\Http\Controllers\Api\V1MainController@attendancelist', 'as' => 'attendance_list']); 
Route::post('v1/attendance_insert', ['uses' => '\App\Http\Controllers\Api\V1MainController@attendanceinsert', 'as' => 'attendance_insert']); 
Route::post('v1/attendancelist', ['uses' => '\App\Http\Controllers\Api\V1MainController@list_attendance', 'as' => 'attendancelist']); 


Route::middleware('auth:api')->group(function () {
    
    Route::post('v1/GetCustomerBranches', ['uses' => '\App\Http\Controllers\Api\MainController@getCustomerBranches', 'as' => 'GetCustomerBranches']);
    Route::post('v1/AttendanceStore', ['uses' => '\App\Http\Controllers\Api\MainController@attendanceStore', 'as' => 'AttendanceStore']);
    Route::post('v1/GetEmployeeInfo', ['uses' => '\App\Http\Controllers\Api\MainController@getEmployeeInfo', 'as' => 'GetEmployeeInfo']);
    Route::post('v1/GetLeavesList', ['uses' => '\App\Http\Controllers\Api\MainController@GetLeavesList', 'as' => 'GetLeavesList']);
    Route::post('v1/ApplyLeave', ['uses' => '\App\Http\Controllers\Api\MainController@ApplyLeave', 'as' => 'ApplyLeave']);
    Route::post('v1/GetApplyLeavelist', ['uses' => '\App\Http\Controllers\Api\MainController@GetApplyLeavelist', 'as' => 'GetApplyLeavelist']);
    Route::post('v3/SaveEmoji', ['uses' => '\App\Http\Controllers\Api\V3MainController@SaveEmoji', 'as' => 'SaveEmoji']);
    Route::post('v3/GetEmojies', ['uses' => '\App\Http\Controllers\Api\V3MainController@GetEmojies', 'as' => 'GetEmojies']);
    Route::post('v3/BusLogin', ['uses' => '\App\Http\Controllers\Api\V3MainController@BusLogin', 'as' => 'BusLogin']);
    Route::post('v3/GetBusEmployees', ['uses' => '\App\Http\Controllers\Api\V3MainController@GetBusEmployees', 'as' => 'GetBusEmployees']);
    Route::post('v3/EmpAvailabilitySave', ['uses' => '\App\Http\Controllers\Api\V3MainController@EmpAvailabilitySave', 'as' => 'EmpAvailabilitySave']);
    Route::post('v3/GetSavedEmojiCount', ['uses' => '\App\Http\Controllers\Api\V3MainController@GetSavedEmojiCount', 'as' => 'GetSavedEmojiCount']);
});

// Leave API List
Route::post('v1/GetLeaveTypes', ['uses' => '\App\Http\Controllers\Api\APILeaveController@GetLeaveTypes', 'as' => 'GetLeaveTypes']);
Route::post('v1/GetLeaveBalance', ['uses' => '\App\Http\Controllers\Api\APILeaveController@GetLeaveBalance', 'as' => 'GetLeaveBalance']);
Route::post('v1/leaverequest_list', ['uses' => '\App\Http\Controllers\Api\APILeaveController@leaverequest_list', 'as' => 'leaverequest_list']); 
Route::post('v1/Leaverequestdetails', ['uses' => '\App\Http\Controllers\Api\APILeaveController@getemployeeleaverequest', 'as' => 'Leaverequestdetails']); 
Route::post('v1/leaverequestinsert', ['uses' => '\App\Http\Controllers\Api\APILeaveController@leaverequestinsert', 'as' => 'leaverequestinsert']); 

// Employee API List
Route::post('v1/GetEmployeeProfileDetails', ['uses' => '\App\Http\Controllers\Api\APIEmployeeController@GetEmployeeProfileDetails', 'as' => 'GetEmployeeProfileDetails']);
Route::post('v1/GetEmployees', ['uses' => '\App\Http\Controllers\Api\APIEmployeeController@Getemployees', 'as' => 'GetEmployees']);
Route::post('v1/GetApprovepersons', ['uses' => '\App\Http\Controllers\Api\APIEmployeeController@Getapprovepersons', 'as' => 'GetApprovepersons']);

// Attendance API List
Route::post('v2/MarkEmployeeAvailability', ['uses' => '\App\Http\Controllers\Api\APIAttendanceController@MarkEmployeeAvailability', 'as' => 'MarkEmployeeAvailability']);
Route::post('v1/gettimesheet', ['uses' => '\App\Http\Controllers\Api\APIAttendanceController@attendance_list_for_month_edit', 'as' => 'gettimesheet']); 

// Location API List
Route::post('v1/GetLocations', ['uses' => '\App\Http\Controllers\Api\LocationAttendanceController@GetLocations', 'as' => 'GetLocations']);
Route::post('v1/GetShiftType', ['uses' => '\App\Http\Controllers\Api\LocationAttendanceController@GetShiftType', 'as' => 'GetShiftType']);
Route::post('v1/GetLocationEmployees', ['uses' => '\App\Http\Controllers\Api\LocationAttendanceController@GetLocationEmployees', 'as' => 'GetLocationEmployees']);
Route::post('v1/Insertlocationattendance', ['uses' => '\App\Http\Controllers\Api\LocationAttendanceController@Insertlocationattendance', 'as' => 'Insertlocationattendance']);
Route::post('v1/Getlocationpoint', ['uses' => '\App\Http\Controllers\Api\LocationAttendanceController@Getlocationpoint', 'as' => 'Getlocationpoint']);
Route::post('v1/Getattendanceshift', ['uses' => '\App\Http\Controllers\Api\LocationAttendanceController@Getattendanceshift', 'as' => 'Getattendanceshift']);
Route::post('v1/Singlelocationattendanceinsert', ['uses' => '\App\Http\Controllers\Api\LocationAttendanceController@Singlelocationattendanceinsert', 'as' => 'Singlelocationattendanceinsert']);

// Dashboard API List
Route::post('v1/GetApprovedUpcomingLeavesForDashboard', ['uses' => '\App\Http\Controllers\Api\APIDashboardController@GetApprovedUpcomingLeavesForDashboard', 'as' => 'GetApprovedUpcomingLeavesForDashboard']);
Route::post('v1/Getdetails_maindashbord', ['uses' => '\App\Http\Controllers\Api\APIDashboardController@Getdetails_maindashbord', 'as' => 'Getdetails_maindashbord']);
Route::post('v1/getdetails_attendancedashboard', ['uses' => '\App\Http\Controllers\Api\APIDashboardController@getdetails_attendancedashboard', 'as' => 'getdetails_attendancedashboard']);

// Payroll API List
Route::post('v1/getemployee_monthlysummery', ['uses' => '\App\Http\Controllers\Api\APIPayrollController@get_employee_monthlysummery', 'as' => 'getemployee_monthlysummery']);
Route::post('v1/download_payslip', ['uses' => '\App\Http\Controllers\Api\APIPayrollController@downloadEmployeeSalarySheet', 'as' => 'download_payslip']);
 

Route::post('/attendancesyncAPI', ['uses' => '\App\Http\Controllers\AttendanceSyncAPIController@index', 'as' => 'attendancesyncAPI']);

Route::get('/download-pdf/{pdfId}', function($pdfId) {
    $pdfData = session()->get($pdfId);
    
    // Remove from session after retrieval
    session()->forget($pdfId);
    
    return response($pdfData['content'], 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $pdfData['filename'] . '"',
        'Content-Length' => strlen($pdfData['content']),
        'Cache-Control' => 'no-store, no-cache, must-revalidate',
        'Pragma' => 'no-cache',
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Expose-Headers' => 'Content-Disposition'
    ]);
});