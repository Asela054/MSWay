<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();

/* User Role Permission*/

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('roles','RoleController');
Route::resource('permissions','PermissionController');
Route::resource('users','UserController');
Route::resource('permission','PermissionController');
Route::resource('rolepermission','RolePermissionController');
Route::resource('userrole','RoleUserController');
Route::resource('userpermission','UserPermissionController');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/getdashboard_department_attendance', 'HomeController@department_attendance')->name('getdashboard_department_attendance');
Route::get('/getdashboard_department_absent', 'HomeController@department_absent')->name('getdashboard_department_absent');
Route::get('/getdashboard_AttendentChart', 'HomeController@getAttendentChart')->name('getdashboard_AttendentChart');

Route::get('/getdashboard_department_lateattendance', 'HomeController@department_lateattendance')->name('getdashboard_department_lateattendance');
Route::get('/getdashboard_department_yesterdayattendance', 'HomeController@department_yesterdayattendance')->name('getdashboard_department_yesterdayattendance');
Route::get('/getdashboard_department_yesterdayabsent', 'HomeController@department_yesterdayabsent')->name('getdashboard_department_yesterdayabsent');
Route::get('/getdashboard_department_yesterdaylateattendance', 'HomeController@department_yesterdaylateattendance')->name('getdashboard_department_yesterdaylateattendance');

Route::get('/corporatedashboard' ,'CorporatedashboardController@index')->name('corporatedashboard');
Route::get('/employeemanagementdashboard' ,'EmployeemanagementdashboardController@index')->name('employeemanagementdashboard');
Route::get('/shiftmanagementdashboard' ,'ShiftdashboardController@index')->name('shiftmanagementdashboard');
Route::get('/attendenceleavedashboard' ,'AttendenceleavedashboardController@index')->name('attendenceleavedashboard');
Route::get('/reportdashboard' ,'ReportdashboardController@index')->name('reportdashboard');
Route::get('/payrolldashboard' ,'PayrolldashboardController@index')->name('payrolldashboard');
Route::get('/administratordashboard' ,'AdministratordashboardController@index')->name('administratordashboard');

// corparate dashboard
Route::get('/getcoparatedashboard_EmployeeChart', 'CorporatedashboardController@getcoparatedashboard_EmployeeChart')->name('getcoparatedashboard_EmployeeChart');

Route::resource('FingerprintDevice', 'FingerprintDeviceController');
//Route::post('addFingerprintDevice',['uses' => 'FingerprintDeviceController@store', 'as' => 'addFingerprintDevice']); 

Route::get('EmloyeeList',['uses' => 'EmployeeController@employeelist', 'as' => 'EmloyeeList']); 
Route::get('addEmployee',['uses' => 'EmployeeController@index', 'as' => 'addEmployee']);
Route::get('employee_list_dt',['uses' => 'EmployeeController@employee_list_dt', 'as' => 'employee_list_dt']);
Route::post('empoyeeUpdate',['uses' => 'EmployeeController@edit', 'as' => 'empoyeeUpdate']); 
Route::post('empoyeeRegister',['uses' => 'EmployeeController@store', 'as' => 'empoyeeRegister']); 
Route::post('addUserLogin',['uses' => 'EmployeeController@usercreate', 'as' => 'addUserLogin']); 
Route::get('EmployeeDestroy/destroy/{id}', 'EmployeeController@destroy');
Route::get('exportEmpoloyee', 'EmployeeController@exportempoloyee')->name('exportEmpoloyee');
Route::get('/viewEmployee/{id}',['uses' => 'EmployeeController@show', 'as' => 'viewEmployee']);
Route::get('/contactDetails/{id}',['uses' => 'EmployeeController@showcontact', 'as' => 'contactDetails']);
Route::post('contactUpdate',['uses' => 'EmployeeController@editcontact', 'as' => 'contactUpdate']);
Route::get('/viewEmergencyContacts/{id}',['uses' => 'EmployeeController@showcontact', 'as' => 'viewEmergencyContacts']);

Route::post('empoyeeAttachment',['uses' => 'EmployeeAttachmentController@create', 'as' => 'empoyeeAttachment']);
Route::post('contactAttachment',['uses' => 'EmployeeAttachmentController@createcontact', 'as' => 'contactAttachment']);
Route::post('qulificationAttachment',['uses' => 'EmployeeAttachmentController@createqulification', 'as' => 'qulificationAttachment']);
Route::post('dependetAttachment',['uses' => 'EmployeeAttachmentController@createdependent', 'as' => 'dependetAttachment']);
Route::post('immigrationAttachment',['uses' => 'EmployeeImmigrationController@immigrationattacment', 'as' => 'immigrationAttachment']);


Route::resource('EmployeeSelect', 'SelectEmployeeController');
Route::get('selectEmployee',['uses' => 'SelectEmployeeController@create', 'as' => 'selectEmployee']);
Route::post('/get_select_employee_details', 'SelectEmployeeController@get_select_employee_details')->name('get_select_employee_details');
Route::post('/select_employee_post', 'SelectEmployeeController@select_employee_post')->name('select_employee_post');
Route::get('selectEmployeeIndex',['uses' => 'SelectEmployeeController@index', 'as' => 'selectEmployeeIndex']);
Route::get('select_employee_list_dt',['uses' => 'SelectEmployeeController@select_employee_list_dt', 'as' => 'select_employee_list_dt']);
Route::get('EmployeeSelect/destroy/{id}', 'SelectEmployeeController@destroy');


Route::get('/viewEmergencyContacts', function () {
    return view('Employee.viewEmergencyContacts');
});

Route::get('getDependentDetail/{id}',['uses' => 'EmployeeDependentController@edit_json', 'as' => 'getDependentDetail']);
Route::post('dependentUpdate',['uses' => 'EmployeeDependentController@update', 'as' => 'dependentUpdate']);
Route::get('dependent_delete/{id}',['uses' => 'EmployeeDependentController@destroy', 'as' => 'dependent_delete']);
Route::get('/viewDependents/{id}',['uses' => 'EmployeeDependentController@show', 'as' => 'viewDependents']);
Route::post('dependentInsert',['uses' => 'EmployeeDependentController@create', 'as' => 'dependentInsert']);
Route::get('dependentUpdate/{id}',['uses' => 'EmployeeDependentController@edit', 'as' => 'dependentUpdate']);

Route::get('/viewEmergencyContacts/{id}',['uses' => 'EmployeeEmergencyContacts@show', 'as' => 'viewEmergencyContacts']);
Route::post('emergencyContactInsert',['uses' => 'EmployeeEmergencyContacts@create', 'as' => 'emergencyContactInsert']);
Route::get('getEmergencyContactDetail/{id}',['uses' => 'EmployeeEmergencyContacts@edit_json', 'as' => 'getEmergencyContactDetail']);
Route::post('emergencyContactUpdate',['uses' => 'EmployeeEmergencyContacts@update', 'as' => 'emergencyContactUpdate']);
Route::get('emergency_contact_delete/{id}',['uses' => 'EmployeeEmergencyContacts@destroy', 'as' => 'emergency_contact_delete']);



Route::get('/viewImmigration/{id}',['uses' => 'EmployeeImmigrationController@show', 'as' => 'viewImmigration']);
Route::post('immigrationInsert',['uses' => 'EmployeeImmigrationController@create', 'as' => 'immigrationInsert']);
Route::get('getImmigrationDetail/{id}',['uses' => 'EmployeeImmigrationController@edit_json', 'as' => 'getImmigrationDetail']);
Route::post('immigrationUpdate',['uses' => 'EmployeeImmigrationController@update', 'as' => 'immigrationUpdate']);
Route::get('immigration_delete/{id}',['uses' => 'EmployeeImmigrationController@destroy', 'as' => 'immigration_delete']);
Route::get('/viewJobDetails/{id}',['uses' => 'EmployeeImmigrationController@show', 'as' => 'viewJobDetails']);


Route::get('/viewJobDetails', function () {
    return view('Employee.viewJobDetails');
});


Route::get('/viewSalaryDetails/{id}',['uses' => 'EmployeeSalaryController@show', 'as' => 'viewSalaryDetails']);


Route::get('/viewQualifications/{id}',['uses' => 'EmployeeEducationController@show', 'as' => 'viewQualifications']);
Route::get('/viewbankDetails/{id}',['uses' => 'EmployeeBankController@show', 'as' => 'viewbankDetails']);
Route::post('BankInsert',['uses' => 'EmployeeBankController@store', 'as' => 'BankInsert']);
Route::get('empBank/destroy/{id}', 'EmployeeBankController@destroy');
Route::get('empBankReport',['uses' => 'EmployeeBankController@empBankReport', 'as' => 'empBankReport']);
Route::get('bank_report_list',['uses' => 'EmployeeBankController@bank_report_list', 'as' => 'bank_report_list']);



Route::get('/viewPassport/{id}',['uses' => 'EmployeePassportController@show', 'as' => 'viewPassport']);
Route::post('passportInsert',['uses' => 'EmployeePassportController@store', 'as' => 'passportInsert']); 
Route::post('passportAttachment',['uses' => 'EmployeePassportController@passportattacment', 'as' => 'passportAttachment']);
Route::get('passportEdit/{emp_pass_id}',['uses' => 'EmployeePassportController@Edit', 'as' => 'passportEdit']);
Route::get('passportDestroy/{emp_pass_id}',['uses' => 'EmployeePassportController@Destroy', 'as' => 'passportDestroy']);
Route::post('passportUpdate',['uses' => 'EmployeePassportController@Update', 'as' => 'passportUpdate']);


Route::get('/viewEmployeeFiles/{id}',['uses' => 'EmployeeFilesController@show', 'as' => 'viewEmployeeFiles']);
Route::post('employeeAttachmentJson',['uses' => 'EmployeeAttachmentController@employeeAttachmentJson', 'as' => 'employeeAttachmentJson']);
Route::get('/download_file/{file}',['uses' => 'EmployeeAttachmentController@download_file', 'as' => 'download_file']);
Route::get('attachment/destroy/{id}', 'EmployeeAttachmentController@destroy_attachment');

/*-- Jobs Title----*/
Route::resource('WorkExprience', 'EmployeeExperienceController');
Route::post('WorkExprienceInsert',['uses' => 'EmployeeExperienceController@create', 'as' => 'WorkExprienceInsert']); 
Route::post('WorkExprience/update', 'EmployeeExperienceController@update')->name('WorkExprience.update');
Route::get('WorkExprience/destroy/{id}', 'EmployeeExperienceController@destroy');
/*-- End Jobs Title----*/

/*-- EmployeeSkill----*/
Route::resource('EmployeeSkill', 'EmployeeSkillController');
Route::post('skillInsert',['uses' => 'EmployeeSkillController@create', 'as' => 'skillInsert']);
Route::post('EmployeeSkill/update', 'EmployeeSkillController@update')->name('EmployeeSkill.update');
Route::get('EmployeeSkill/destroy/{id}', 'EmployeeSkillController@destroy');
/*-- End EmployeeSkill----*/

/*-- Education----*/
Route::resource('EmployeeEducation', 'EmployeeEducationController');
Route::post('educationInsert',['uses' => 'EmployeeEducationController@create', 'as' => 'educationInsert']); 
Route::post('EmployeeEducation/update', 'EmployeeEducationController@update')->name('EmployeeEducation.update');
Route::get('EmployeeEducation/destroy/{id}', 'EmployeeEducationController@destroy');
/*-- End Education----*/



/*-- Jobs Title----*/
Route::resource('JobTitle', 'JobTitleController');
Route::get('JobTitle',['uses' => 'JobTitleController@index', 'as' => 'JobTitle']); 
Route::post('addJobTitle',['uses' => 'JobTitleController@store', 'as' => 'addJobTitle']); 
Route::post('JobTitle/update', 'JobTitleController@update')->name('JobTitle.update');
Route::get('JobTitle/destroy/{id}', 'JobTitleController@destroy');
/*-- End Jobs Title----*/

/*-- Pay Grade Title----*/
Route::resource('PayGrade', 'PayGradeController');
Route::get('PayGrade',['uses' => 'PayGradeController@index', 'as' => 'PayGrade']); 
Route::post('addPayGrade',['uses' => 'PayGradeController@store', 'as' => 'addPayGrade']); 
Route::post('PayGrade/update', 'PayGradeController@update')->name('PayGrade.update');
Route::get('PayGrade/destroy/{id}', 'PayGradeController@destroy');
/*-- Pay Grade Title----*/

/*-- Employment Status----*/
Route::resource('EmploymentStatus', 'EmploymentStatusController');
Route::get('EmploymentStatus',['uses' => 'EmploymentStatusController@index', 'as' => 'EmploymentStatus']); 
Route::post('addEmploymentStatus',['uses' => 'EmploymentStatusController@store', 'as' => 'addEmploymentStatus']); 
Route::post('EmploymentStatus/update', 'EmploymentStatusController@update')->name('EmploymentStatus.update');
Route::get('EmploymentStatus/destroy/{id}', 'EmploymentStatusController@destroy');

/*-- Employment Status----*/

/*--  Job Category----*/
Route::resource('JobCategory', 'JobCategoryController');
Route::get('JobCategory',['uses' => 'JobCategoryController@index', 'as' => 'JobCategory']); 
Route::post('addJobCategory',['uses' => 'JobCategoryController@store', 'as' => 'addJobCategory']); 
Route::post('JobCategory/update', 'JobCategoryController@update')->name('JobCategory.update');
Route::get('JobCategory/destroy/{id}', 'JobCategoryController@destroy');
/*-- Job Category----*/


/*-- End Jobs----*/


/*-- Start Qulification----*/
/*-- Skills----*/
Route::resource('Skill', 'SkillController');
Route::get('Skill',['uses' => 'SkillController@index', 'as' => 'Skill']); 
Route::post('addSkill',['uses' => 'SkillController@store', 'as' => 'addSkill']); 
Route::post('Skill/update', 'SkillController@update')->name('Skill.update');
Route::get('Skill/destroy/{id}', 'SkillController@destroy');
/*-- Skills----*/

/*-- Education----*/
Route::resource('Education', 'EducationController');
Route::get('Education',['uses' => 'EducationController@index', 'as' => 'Education']); 
Route::post('addEducation',['uses' => 'EducationController@store', 'as' => 'addEducation']); 
Route::post('Education/update', 'EducationController@update')->name('Education.update');
//Route::post('updateEducation', 'EducationController@update')->name('updateEducation');
Route::get('Education/destroy/{id}', 'EducationController@destroy');
/*-- Education----*/
/*-- End Qulification----*/



/*-- Jobs Title----*/
Route::resource('Branch', 'BranchController');
Route::get('Branch',['uses' => 'BranchController@index', 'as' => 'Branch']); 
Route::post('addBranch',['uses' => 'BranchController@store', 'as' => 'addBranch']); 
Route::post('Branch/update', 'BranchController@update')->name('Branch.update');
Route::get('Branch/destroy/{id}', 'BranchController@destroy');
/*-- End Jobs Title----*/


Route::resource('Attendance', 'AttendanceController');
Route::get('Attendance',['uses' => 'AttendanceController@index', 'as' => 'Attendance']);

// Route::post('late_attendance/destroy/{id}', 'AttendanceController@destroy_late_attendacne');

//Attendance.delete post
Route::post('Attendance.delete', 'AttendanceController@delete')->name('Attendance.delete');


//incomplete_attendances
Route::get('incomplete_attendances',['uses' => 'IncompleteAttendanceController@incomplete_attendances', 'as' => 'incomplete_attendances']);
Route::post('get_incomplete_attendance_by_employee_data', 'IncompleteAttendanceController@get_incomplete_attendance_by_employee_data')->name('get_incomplete_attendance_by_employee_data');
Route::post('mark_as_no_pay', 'IncompleteAttendanceController@mark_as_no_pay')->name('mark_as_no_pay');

Route::get('AttendanceEdit',['uses' => 'AttendanceController@attendanceedit', 'as' => 'AttendanceEdit']);
Route::get('attendance_list_for_edit',['uses' => 'AttendanceController@attendance_list_for_edit', 'as' => 'attendance_list_for_edit']);

Route::post('attendance_add_bulk_submit',['uses' => 'AttendanceController@attendance_add_bulk_submit', 'as' => 'attendance_add_bulk_submit']);
Route::post('attendance_add_dept_wise_submit',['uses' => 'AttendanceController@attendance_add_dept_wise_submit', 'as' => 'attendance_add_dept_wise_submit']);
//post get_attendance_monthly_summery_by_emp_id
Route::post('get_attendance_monthly_summery_by_emp_id',['uses' => 'AttendanceController@get_attendance_monthly_summery_by_emp_id', 'as' => 'get_attendance_monthly_summery_by_emp_id']);


Route::get('AttendanceEditBulk',['uses' => 'AttendanceController@AttendanceEditBulk', 'as' => 'AttendanceEditBulk']);

Route::get('attendance_list_for_bulk_edit',['uses' => 'AttendanceController@attendance_list_for_bulk_edit', 'as' => 'attendance_list_for_bulk_edit']);
Route::post('AttendanceEditBulkSubmit',['uses' => 'AttendanceController@AttendanceEditBulkSubmit', 'as' => 'AttendanceEditBulkSubmit']);
Route::post('attendance_list_for_month_edit',['uses' => 'AttendanceController@attendance_list_for_month_edit', 'as' => 'attendance_list_for_month_edit']);
Route::post('attendance_update_bulk_submit',['uses' => 'AttendanceController@attendance_update_bulk_submit', 'as' => 'attendance_update_bulk_submit']);


Route::get('/AttendentUpdate', 'AttendanceController@getAttendance');
Route::get('/AttendentView', 'AttendanceController@getAttendance');

Route::post('/AttendentUpdateLive', 'AttendanceController@attendentUpdateLive');
Route::post('/AttendentInsertLive', 'AttendanceController@attendentinsertlive');
Route::post('/AttendentDeleteLive', 'AttendanceController@attendentdeletelive');
Route::get('/getAttendentChart', 'AttendanceController@getAttendentChart');
Route::get('/getBranchAttendentChart', 'AttendanceController@getBranchAttendentChart');
Route::get('/Attendentdetails/{id}/{date}',['uses' => 'AttendanceController@attendentdetails', 'as' => 'Attendentdetails']);

//get_incomplete_attendance_by_employee_data


Route::post('Attendance/update', 'AttendanceController@update')->name('Attendance.update');
Route::get('Attendance/destroy/{id}', 'AttendanceController@destroy');

//AttendanceDeviceClear
Route::get('AttendanceDeviceClear', 'AttendanceClearController@attendance_clear_list')->name('AttendanceDeviceClear');

//attendance_clear_list
Route::get('attendance_clear_list_dt',['uses' => 'AttendanceClearController@attendance_clear_list_dt', 'as' => 'attendance_clear_list_dt']);

Route::get('exportAttendance', 'AttendanceController@exportattendances')->name('exportAttendance');


/*-- Attendent Type----*/
Route::resource('AttendanceType', 'AttendanceTypeController');
Route::get('AttendanceType',['uses' => 'AttendanceTypeController@index', 'as' => 'AttendanceType']); 
Route::post('addAttendanceType',['uses' => 'AttendanceTypeController@store', 'as' => 'addAttendanceType']); 
Route::post('AttendanceType/update', 'AttendanceTypeController@update')->name('AttendanceType.update');
Route::get('job/destroy/{id}', 'AttendanceTypeController@destroy');
/*-- End Attendent Type----*/


Route::resource('FingerprintUser', 'FingerprintUserController');
Route::post('addFingerprintUser',['uses' => 'FingerprintUserController@store', 'as' => 'addFingerprintUser']);
Route::get('FingerprintUser',['uses' => 'FingerprintUserController@index', 'as' => 'FingerprintUser']); 
Route::post('FingerprintUser/update', 'FingerprintUserController@update')->name('FingerprintUser.update');
Route::get('FingerprintUser/destroy/{id}', 'FingerprintUserController@destroy');
Route::get('exportFPUser', 'FingerprintUserController@exportfpuser')->name('exportFPUser');
Route::post('FingerprintUser/getdeviceuserdata', 'FingerprintUserController@getdeviceuserdata');


Route::resource('FingerprintDevice', 'FingerprintDeviceController');
Route::post('addFingerprintDevice',['uses' => 'FingerprintDeviceController@store', 'as' => 'addFingerprintDevice']); 
Route::get('FingerprintDevice',['uses' => 'FingerprintDeviceController@index', 'as' => 'FingerprintDevice']); 
Route::post('FingerprintDevice/update', 'FingerprintDeviceController@update')->name('FingerprintDevice.update');
Route::get('FingerprintDevice/destroy/{id}', 'FingerprintDeviceController@destroy');


Route::resource('LeaveType', 'LeaveTypeController');
Route::post('addLeaveType',['uses' => 'LeaveTypeController@store', 'as' => 'addLeaveType']); 
Route::get('LeaveType',['uses' => 'LeaveTypeController@index', 'as' => 'LeaveType']); 
Route::post('LeaveType/update', 'LeaveTypeController@update')->name('LeaveType.update');
Route::get('LeaveType/destroy/{id}', 'LeaveTypeController@destroy');
Route::get('LeaveBalance',['uses' => 'LeaveTypeController@LeaveBalance', 'as' => 'LeaveBalance']);
Route::get('leave_balance_list',['uses' => 'LeaveTypeController@leave_balance_list', 'as' => 'leave_balance_list']);




Route::resource('LeaveApply', 'LeaveController');
Route::post('addLeaveApply',['uses' => 'LeaveController@store', 'as' => 'addLeaveApply']); 
Route::get('LeaveApply',['uses' => 'LeaveController@index', 'as' => 'LeaveApply']);
Route::get('leave_list_dt',['uses' => 'LeaveController@leave_list_dt', 'as' => 'leave_list_dt']);
Route::post('LeaveApply/update', 'LeaveController@update')->name('LeaveApply.update');
Route::get('LeaveApply/destroy/{id}', 'LeaveController@destroy');
Route::post('/getEmployeeLeaveStatus', 'LeaveController@getemployeeleavestatus');

Route::post('/getEmployeeCategory', 'LeaveController@getEmployeeCategory');

Route::get('LeaveApprovel',['uses' => 'LeaveController@approvelindex', 'as' => 'LeaveApprovel']); 
Route::get('leave_approve_list_dt',['uses' => 'LeaveController@leave_approve_list_dt', 'as' => 'leave_approve_list_dt']);
Route::post('approvelupdate', 'LeaveController@approvelupdate')->name('approvelupdate');

Route::get('Leaveleavecreate', 'LeaveController@leaveleavecreate')->name('leaveleavecreate'); 


Route::get('EmpoloyeeReport',['uses' => 'Report@getemployeelist', 'as' => 'EmpoloyeeReport']);
Route::get('employee_report_list',['uses' => 'Report@employee_report_list', 'as' => 'employee_report_list']);
 
Route::get('exportEmployeeReport', 'Report@exportempoloyeereport')->name('exportEmployeeReport'); 
Route::get('attendentreportbydate', function () {
    return view('Report.attendentreportbydate');
});

//get_attendance_by_employee_data_excel
Route::post('get_attendance_by_employee_data_excel', 'Report@get_attendance_by_employee_data_excel')->name('get_attendance_by_employee_data_excel');



Route::post('/employee/fetch_data', 'Report@employee_fetch_data')->name('employee.fetch_data');
Route::post('/daterange/filter_data', 'Report@filter_data')->name('daterange.filter_data');
Route::get('/LateAttendentView', 'AttendanceController@getlateAttendance');

Route::get('/copy_att_to_employee_work_rates', 'AttendanceController@copy_att_to_employee_work_rates');


Route::resource('ShiftType', 'ShiftTypeController');
Route::post('addShiftType',['uses' => 'ShiftTypeController@store', 'as' => 'addShiftType']); 
Route::get('ShiftType',['uses' => 'ShiftTypeController@index', 'as' => 'ShiftType']); 
Route::post('ShiftType/update', 'ShiftTypeController@update')->name('ShiftType.update');
Route::get('ShiftType/destroy/{id}', 'ShiftTypeController@destroy');


Route::resource('Shift', 'ShiftController');
Route::post('addShift',['uses' => 'ShiftController@store', 'as' => 'addShift']); 
Route::get('Shift',['uses' => 'ShiftController@index', 'as' => 'Shift']); 
Route::get('shift_list_dt',['uses' => 'ShiftController@shift_list_dt', 'as' => 'shift_list_dt']);
Route::post('Shift/update', 'ShiftController@update')->name('Shift.update');
Route::get('Shift/destroy/{id}', 'ShiftController@destroy');
Route::post('/Shiftupdate', 'ShiftController@Shiftupdate');
Route::get('/Getshift', 'ShiftController@getshift');

Route::resource('AdditionalShift', 'AdditionalShiftController');
Route::post('addAdditionalShift',['uses' => 'AdditionalShiftController@store', 'as' => 'addAdditionalShift']);
Route::post('AdditionalShift/update', 'AdditionalShiftController@update')->name('AdditionalShift.update');
Route::get('AdditionalShift/destroy/{id}', 'AdditionalShiftController@destroy');
Route::get('AdditionalShiftEdit/{id}', 'AdditionalShiftController@edit');
Route::get('branch_list', 'AdditionalShiftController@branch_list');
Route::get('additional_shift_list_dt',['uses' => 'AdditionalShiftController@additional_shift_list_dt', 'as' => 'additional_shift_list_dt']);

Route::resource('Holiday', 'HolidayController');
Route::post('addHoliday',['uses' => 'HolidayController@store', 'as' => 'addHoliday']); 
Route::get('Holiday',['uses' => 'HolidayController@index', 'as' => 'Holiday']); 
Route::post('Holiday/update', 'HolidayController@update')->name('Holiday.update');
Route::get('Holiday/destroy/{id}', 'HolidayController@destroy');
Route::post('get_holidays_for_calendar','HolidayController@get_holidays_for_calendar')->name('get_holidays_for_calendar');
Route::get('HolidayCalendar','HolidayController@HolidayCalendar')->name('HolidayCalendar');
Route::post('HolidayGet','HolidayController@HolidayGet')->name('HolidayGet');

Route::resource('Worklevel', 'WorkLevelController');
Route::post('addWorklevel',['uses' => 'WorkLevelController@store', 'as' => 'addWorklevel']); 
Route::get('Worklevel',['uses' => 'WorkLevelController@index', 'as' => 'Worklevel']); 
Route::post('Worklevel/update', 'WorkLevelController@update')->name('Worklevel.update');
Route::get('Worklevel/destroy/{id}', 'WorkLevelController@destroy');

/*-- Company Info----*/
Route::resource('Company', 'CompanyController');
Route::get('Company',['uses' => 'CompanyController@index', 'as' => 'Company']); 
Route::post('addCompany',['uses' => 'CompanyController@store', 'as' => 'addCompany']); 
Route::post('Company/update', 'CompanyController@update')->name('Company.update');
Route::get('Company/destroy/{id}', 'CompanyController@destroy');
Route::get('company_list_sel2', 'CompanyController@company_list_sel2');
/*-- End Company Info----*/

/*-- Department Info----*/
Route::resource('Department', 'DepartmentController');
Route::get('DepartmentShow/{id}',['uses' => 'DepartmentController@index', 'as' => 'DepartmentShow']);
Route::post('addDepartment',['uses' => 'DepartmentController@store', 'as' => 'addDepartment']);
Route::post('Department/update', 'DepartmentController@update')->name('Department.update');
Route::get('Department/destroy/{id}', 'DepartmentController@destroy');
Route::get('department_list_sel2', 'DepartmentController@department_list_sel2');

/*-- End Department Info----*/

/*-- Bank Info----*/
Route::resource('Bank', 'BankController');
Route::get('Bank',['uses' => 'BankController@index', 'as' => 'Bank']);
Route::post('addBank',['uses' => 'BankController@store', 'as' => 'addBank']);
Route::post('Bank/update', 'BankController@update')->name('Bank.update');
Route::get('Bank/destroy/{id}', 'BankController@destroy');
Route::get('bank_list', 'BankController@bank_list');
Route::get('banks_list_dt',['uses' => 'BankController@banks_list_dt', 'as' => 'banks_list_dt']);
/*-- End Bank Info----*/

/*-- bank_branch Info----*/
Route::resource('bank_branch', 'BankBranchController');
Route::get('bank_branch_show/{id}',['uses' => 'BankBranchController@index', 'as' => 'bank_branch_show']);
Route::post('addBankBranch',['uses' => 'BankBranchController@store', 'as' => 'addBankBranch']);
Route::post('BankBranch/update', 'BankBranchController@update')->name('BankBranch.update');
Route::get('BankBranch/destroy/{id}', 'BankBranchController@destroy');
Route::get('BankBranchEdit/{id}', 'BankBranchController@edit');
Route::get('branch_list', 'BankBranchController@branch_list');
Route::get('bank_branches_list_dt',['uses' => 'BankBranchController@bank_branches_list_dt', 'as' => 'bank_branches_list_dt']);

/*-- End bank_branch Info----*/

//resource OccupationGroup
Route::resource('OccupationGroup', 'OccupationGroupController');
//occupation_group_list_dt
Route::get('occupation_group_list_dt',['uses' => 'OccupationGroupController@occupation_group_list_dt', 'as' => 'occupation_group_list_dt']);
//OccupationGroup.update_manual
Route::post('OccupationGroup.update_manual', 'OccupationGroupController@update_manual')->name('OccupationGroup.update_manual');
//OccupationGroup.fetch_single
Route::get('OccupationGroup.fetch_single', 'OccupationGroupController@fetch_single')->name('OccupationGroup.fetch_single');

/*-- common routes --*/
Route::get('employee_list_sel2', 'EmployeeController@employee_list_sel2')->name('employee_list_sel2');
Route::get('location_list_sel2', 'EmployeeController@location_list_sel2')->name('location_list_sel2');
Route::post('get_dept_emp_list', 'EmployeeController@get_dept_emp_list')->name('get_dept_emp_list');



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('attendance_upload_txt_submit',['uses' => 'AttendanceController@attendance_upload_txt_submit', 'as' => 'attendance_upload_txt_submit']);

Route::resource('JobCategory', 'JobCategoryController');
Route::get('JobCategory',['uses' => 'JobCategoryController@index', 'as' => 'JobCategory']);
Route::post('addJobCategory',['uses' => 'JobCategoryController@store', 'as' => 'addJobCategory']);
Route::post('JobCategory/update', 'JobCategoryController@update')->name('JobCategory.update');
Route::get('JobCategory/destroy/{id}', 'JobCategoryController@destroy');

Route::resource('peTask', 'PeTaskController');
Route::get('peTaskList',['uses' => 'PeTaskController@index', 'as' => 'peTaskList']);
Route::post('addPeTask',['uses' => 'PeTaskController@store', 'as' => 'addPeTask']);
Route::post('peTask/update', 'PeTaskController@update')->name('peTask.update');
Route::get('peTask/destroy/{id}', 'PeTaskController@destroy');
Route::get('pe_task_list_dt',['uses' => 'PeTaskController@pe_task_list_dt', 'as' => 'pe_task_list_dt']);
Route::post('approvePeTasks',['uses' => 'PeTaskController@approvePeTasks', 'as' => 'approvePeTasks']);

Route::get('pe_employee_list_dt',['uses' => 'PeTaskController@pe_employee_list_dt', 'as' => 'pe_employee_list_dt']);

Route::get('peTaskEmployeeList',['uses' => 'PeTaskEmployeeController@index', 'as' => 'peTaskEmployeeList']);
Route::get('pe_task_employee_list_dt',['uses' => 'PeTaskEmployeeController@pe_task_employee_list_dt', 'as' => 'pe_task_employee_list_dt']);

Route::post('/pe_emp_marks_save', 'PeTaskEmployeeController@pe_emp_marks_save')->name('pe_emp_marks_save');

Route::get('peTaskEmployeeMarksList',['uses' => 'PeTaskEmployeeController@peTaskEmployeeMarksList', 'as' => 'peTaskEmployeeMarksList']);

Route::get('pe_task_employee_marks_approve_list_dt',['uses' => 'PeTaskEmployeeController@pe_task_employee_marks_approve_list_dt', 'as' => 'pe_task_employee_marks_approve_list_dt']);

Route::post('/pe_emp_marks_approve', 'PeTaskEmployeeController@pe_emp_marks_approve')->name('pe_emp_marks_approve');

Route::post('work_hours_save', 'WorkHoursController@work_hours_save')->name('work_hours_save');

// Resignation Report
Route::get('employee_resign_report', 'EmployeeResignController@employee_resign_report')->name('employee_resign_report');
Route::get('get_resign_employees', 'EmployeeResignController@get_resign_employees')->name('get_resign_employees');


// Absent Report
Route::get('employee_absent_report', 'EmployeeAbsentController@employee_absent_report')->name('employee_absent_report');
Route::get('get_absent_employees', 'EmployeeAbsentController@get_absent_employees')->name('get_absent_employees');

Route::post('employeeresignation', 'EmployeeController@employeeresignation')->name('employeeresignation');


// employee recrument report
Route::get('employee_recirument_report', 'RecruitmentreportController@index')->name('employee_recirument_report');
Route::get('/filterrecruitment-report', 'RecruitmentReportController@filter')->name('filterRecruitmentReport');
Route::get('/filterrecruitmentinterviwer-report', 'RecruitmentReportController@interviwerfilter')->name('filterRecruitmentinterviwerReport');


// Appointment Letter Routes
Route::get('appoinementletter', 'ApointmentletterController@index')->name('appoinementletter');
Route::post('appoinementletterinsert', 'ApointmentletterController@insert')->name('appoinementletterinsert');
Route::get('appoinementletterlist', 'ApointmentletterController@letterlist')->name('appoinementletterlist');
Route::post('appoinementletteredit', 'ApointmentletterController@edit')->name('appoinementletteredit');
Route::get('/appoinementletterstatus/{id}/{status}','ApointmentletterController@status')->name('appoinementletterstatus');
Route::post('appoinementletterdelete', 'ApointmentletterController@delete')->name('appoinementletterdelete');
Route::post('appoinementletterprintdata', 'ApointmentletterController@printdata')->name('appoinementletterprintdata');



//department wise reports
Route::get('departmentwise_otreport', 'DepartmentwisereportController@index')->name('departmentwise_otreport');
Route::get('departmentwise_leavereport', 'DepartmentwisereportController@leavereport')->name('departmentwise_leavereport');
Route::get('departmentwise_attendancereport', 'DepartmentwisereportController@attendancereport')->name('departmentwise_attendancereport');

Route::get('/departmentwise_generateotreport', 'DepartmentwisereportController@generateotreport')->name('departmentwise_generateotreport');
Route::get('/departmentwise_gettotlaotemployee', 'DepartmentwisereportController@gettotlaotemployee')->name('departmentwise_gettotlaotemployee');

Route::get('/departmentwise_generateleavereport', 'DepartmentwisereportController@generateleavereport')->name('departmentwise_generateleavereport');
Route::get('/departmentwise_gettotalleaveemployee', 'DepartmentwisereportController@gettotalleaveemployee')->name('departmentwise_gettotalleaveemployee');

Route::get('/getdepartments/{company_id}', 'CommenGetrreordController@getDepartments')->name('getdepartments');

/*-- Payroll routes ----*/




/*-- Remunerations ----*/
Route::resource('Remuneration', 'RemunerationController');
Route::get('RemunerationList',['uses' => 'RemunerationController@index', 'as' => 'RemunerationList']); 
Route::post('addRemuneration',['uses' => 'RemunerationController@store', 'as' => 'addRemuneration']); 
Route::post('Remuneration/update', 'RemunerationController@update')->name('Remuneration.update');
Route::get('Remuneration/destroy/{id}', 'RemunerationController@destroy');
/*
Route::post('/RemunerationListUpdateLive', 'RemunerationController@attendentUpdateLive');
Route::post('/RemunerationListInsertLive', 'RemunerationController@attendentinsertlive');
Route::post('/RemunerationListDeleteLive', 'RemunerationController@attendentdeletelive');
*/
/*-- Remunerations ----*/

/*-- RemunerationsEligibility ----*/
Route::resource('RemunerationEligibilityDay', 'RemunerationEligibilityDayController');
Route::post('RemunerationEligibilityDay/update', 'RemunerationEligibilityDayController@update')->name('RemunerationEligibilityDay.update');
/*-- RemunerationsEligibility ----*/

/*-- PayrollProfiles ----*/
Route::resource('PayrollProfile', 'PayrollProfileController');
Route::get('PayrollProfileList',['uses' => 'PayrollProfileController@index', 'as' => 'PayrollProfileList']); 
Route::post('addPayrollProfile',['uses' => 'PayrollProfileController@store', 'as' => 'addPayrollProfile']); 
Route::post('PayrollProfile/update', 'PayrollProfileController@update')->name('PayrollProfile.update');
Route::get('employeeData/getData', 'PayrollProfileController@getEmployeeData')->name('employeeData.getData');
/*-- PayrollProfiles ----*/

/*-- RemunerationProfiles ----*/
Route::resource('RemunerationProfile', 'RemunerationProfileController');
//Route::get('RemunerationProfileList',['uses' => 'RemunerationProfileController@index', 'as' => 'RemunerationProfileList']); 
Route::post('addRemunerationProfile',['uses' => 'RemunerationProfileController@store', 'as' => 'addRemunerationProfile']); 
Route::post('RemunerationProfile/update', 'RemunerationProfileController@update')->name('RemunerationProfile.update');
Route::get('RemunerationProfile/destroy/{id}', 'RemunerationProfileController@destroy');
//2025-01-15--
Route::get('RemunerationProfile/review/{id}', 'RemunerationProfileController@reviewExtras');
Route::get('RemunerationProfile/checkout/{id}', 'RemunerationProfileController@checkoutExtras');
Route::post('addPayrollProfileExtras',['uses' => 'RemunerationProfileController@manageExtras', 'as' => 'addPayrollProfileExtras']);
//--2025-01-15
/*-- RemunerationProfiles ----*/

/*-- EmployeeLoans --*/
Route::resource('EmployeeLoan', 'EmployeeLoanController');
Route::get('EmployeeLoanList',['uses' => 'EmployeeLoanController@index', 'as' => 'EmployeeLoanList']); 
Route::post('addEmployeeLoan',['uses' => 'EmployeeLoanController@store', 'as' => 'addEmployeeLoan']); 
Route::get('EmployeeLoan/{id}/review', 'EmployeeLoanController@reviewLoanList')->name('EmployeeLoan.review');
Route::post('EmployeeLoan/update', 'EmployeeLoanController@update')->name('EmployeeLoan.update');
Route::post('/freezeEmployeeLoan', 'EmployeeLoanController@freeze');//->name('EmployeeLoan.freeze');
Route::get('EmployeeLoan/destroy/{id}', 'EmployeeLoanController@destroy');
/*-- EmployeeLoans --*/

/*-- EmployeeLoanApproval --*/
//Route::resource('EmployeeLoanApproval', 'EmployeeLoanApprovalController');
Route::get('EmployeeLoanAdmin',['uses' => 'EmployeeLoanApprovalController@index', 'as' => 'EmployeeLoanAdmin']); 
Route::post('viewLoanApplicants',['uses' => 'EmployeeLoanApprovalController@applicantsinfo', 'as' => 'viewLoanApplicants']); 
Route::get('LoanApplicationList/{id}/review', 'EmployeeLoanApprovalController@reviewApplicationList')->name('LoanApplicationList.review');
Route::post('EmployeeLoanApprove/update',['uses' => 'EmployeeLoanApprovalController@update', 'as' => 'EmployeeLoanApprove.update']); 
Route::get('EmployeeLoanApprove/{id}/edit', 'EmployeeLoanApprovalController@edit')->name('EmployeeLoanApprove.edit');
/*-- EmployeeLoanApproval --*/

/*-- EmployeeTermPayments --*/
Route::resource('EmployeeTermPayment', 'EmployeeTermPaymentController');
Route::get('EmployeeTermPaymentList',['uses' => 'EmployeeTermPaymentController@index', 'as' => 'EmployeeTermPaymentList']); 
Route::post('addTermPayment',['uses' => 'EmployeeTermPaymentController@store', 'as' => 'addTermPayment']); 
Route::get('EmployeeTermPayment/{id}/review', 'EmployeeTermPaymentController@reviewPaymentList')->name('EmployeeTermPayment.review');
Route::post('/freezeTermPayment', 'EmployeeTermPaymentController@freeze');
Route::post('/checkTermPayment', 'EmployeeTermPaymentController@checkPayment');
Route::post('EmployeeTermPayment/update', 'EmployeeTermPaymentController@update')->name('EmployeeTermPayment.update');

Route::post('uploadTermPayment',['uses' => 'EmployeeTermPaymentController@uploadFromFile', 'as' => 'uploadTermPayment']); 

Route::post('DownloadTermPaymentPdf',['uses' => 'EmployeeTermPaymentController@downloadTermPayment', 'as' => 'DownloadTermPaymentPdf']);
/*-- EmployeeTermPayments --*/
/*-- EmployeeTermPaymentExtras-15012025 --*/
Route::get('EmployeeExtraPaymentList',['uses' => 'EmployeeTermPaymentExtrasController@index', 'as' => 'EmployeeExtraPaymentList']);
Route::post('/checkExtraPayment ', 'EmployeeTermPaymentExtrasController@checkExtras');
Route::post('addExtraPayment',['uses' => 'EmployeeTermPaymentExtrasController@store', 'as' => 'addExtraPayment']);
Route::get('EmployeeTermPaymentExtras/review/{id}', 'EmployeeTermPaymentExtrasController@reviewPaymentList');
Route::post('/freezeExtrasPayment', 'EmployeeTermPaymentExtrasController@freeze');
/*-- EmployeeTermPaymentExtras-15012025 --*/

/* SalaryIncrements */
Route::resource('SalaryIncrement', 'SalaryIncrementController');
Route::get('SalaryIncrementList',['uses' => 'SalaryIncrementController@index', 'as' => 'SalaryIncrementList']);
Route::post('uploadSalaryIncrement',['uses' => 'SalaryIncrementController@uploadFromFile', 'as' => 'uploadSalaryIncrement']); 
Route::get('incrementData/getData', 'SalaryIncrementController@getIncrementData')->name('incrementData.getData');
Route::get('SalaryIncrement/destroy/{id}', 'SalaryIncrementController@destroy');
/* SalaryIncrements */

/*-- OtherFacilities ----*/
Route::resource('OtherFacility', 'OtherFacilityController');
//Route::get('OtherFacilityList',['uses' => 'OtherFacilityController@index', 'as' => 'OtherFacilityList']); 
Route::post('addOtherFacility',['uses' => 'OtherFacilityController@store', 'as' => 'addOtherFacility']); 
/*-- OtherFacilities ----*/

/* OtherFacilityPayments */
Route::resource('OtherFacilityPayment', 'OtherFacilityPaymentController');
Route::get('OtherFacilityPaymentList',['uses' => 'OtherFacilityPaymentController@index', 'as' => 'OtherFacilityPaymentList']);
Route::post('allocateOtherFacility',['uses' => 'OtherFacilityPaymentController@store', 'as' => 'allocateOtherFacility']); 
Route::post('uploadOtherFacilities',['uses' => 'OtherFacilityPaymentController@uploadFromFile', 'as' => 'uploadOtherFacilities']); 
Route::get('facilitiesData/getData', 'OtherFacilityPaymentController@getFacilityData')->name('facilitiesData.getData');
Route::post('/freezeOtherFacilityPayment', 'OtherFacilityPaymentController@freeze');
Route::get('OtherFacilities/destroy/{id}', 'OtherFacilityPaymentController@destroy');
/* OtherFacilityPayments */

/*-- EmployeeLoanInstallments --*/
Route::resource('EmployeeLoanInstallment', 'EmployeeLoanInstallmentController');
Route::get('EmployeeLoanInstallmentList',['uses' => 'EmployeeLoanInstallmentController@index', 'as' => 'EmployeeLoanInstallmentList']); 
Route::get('EmployeeLoanInstallment/{id}/review', 'EmployeeLoanInstallmentController@reviewPaymentList')->name('EmployeeLoanInstallment.review');
Route::post('EmployeeLoanInstallment/update', 'EmployeeLoanInstallmentController@update')->name('EmployeeLoanInstallment.update');
Route::post('/freezeLoanInstallment', 'EmployeeLoanInstallmentController@freeze');
Route::post('/checkLoanInstallment', 'EmployeeLoanInstallmentController@checkInstallment');
/*-- EmployeeLoanInstallments --*/

/*-- PaymentPeriods --*/
Route::resource('PaymentPeriod', 'PaymentPeriodController');
Route::get('SalaryProcessSchedule',['uses' => 'PaymentPeriodController@index', 'as' => 'SalaryProcessSchedule']); 
Route::post('addSchedule',['uses' => 'PaymentPeriodController@store', 'as' => 'addSchedule']); 
Route::post('PaymentPeriod/update', 'PaymentPeriodController@update')->name('PaymentPeriod.update');
/*-- PaymentPeriods --*/

/*-- EmployeePayslips --*/
Route::resource('EmployeePayslip', 'EmployeePayslipController');
Route::get('EmployeePayslipList',['uses' => 'EmployeePayslipController@index', 'as' => 'EmployeePayslipList']); 
Route::get('PayslipRegistry',['uses' => 'EmployeePayslipController@preview', 'as' => 'PayslipRegistry']); 
Route::post('/checkAttendance', 'EmployeePayslipController@checkAttendance');
Route::post('/checkPayslipListByDept', 'EmployeePayslipController@checkPayslipList');
Route::post('/checkEmpEpfEtf', 'EmployeePayslipController@checkPayPeriodEpfEtf');
Route::post('/checkPayslipListBankSlip', 'PaySlipBank@checkPayslipListBankSlip');
Route::post('/freezePayslip', 'EmployeePayslipController@freeze');
Route::post('/deletePayslip', 'EmployeePayslipController@clearPaidInfo');
Route::post('/holdPayment', 'EmployeePayslipController@holdPayment');
Route::post('/approvePayment', 'EmployeePayslipController@approvePayment');

Route::post('/checkPayslipList', 'PaySlipBank@checkPayslipList');
Route::post('/checkPayslipListBankSlip', 'PaySlipBank@checkPayslipListBankSlip');
Route::get('ReportSalarySheetBankSlip',['uses' => 'PaySlipBank@reportSalarySheetBankSlip', 'as' => 'ReportSalarySheetBankSlip']);
/*-- EmployeePayslips --*/

/*-- EmployeeSalaryRemarks --*/
Route::resource('EmployeeSalaryRemark', 'EmployeeSalaryRemarkController');
Route::post('setRemark',['uses' => 'EmployeeSalaryRemarkController@store', 'as' => 'setRemark']); 
/*-- EmployeeSalaryRemarks --*/

/*-- EmployeeWorkRates --*/
Route::resource('EmployeeWorkRate', 'EmployeeWorkRateController');
Route::get('EmployeeWorkSummary',['uses' => 'EmployeeWorkRateController@index', 'as' => 'EmployeeWorkSummary']); 
Route::post('addWorkSummary',['uses' => 'EmployeeWorkRateController@store', 'as' => 'addWorkSummary']);
/*-- EmployeeWorkRates --*/

//Route::get('admin/customers','CustomerController@index');
//Route::get('clist',['uses' => 'CustomerController@index', 'as' => 'clist']);
//Route::get('ajaxdata', 'AjaxdataController@index')->name('ajaxdata');
//Route::get('ajaxdata/getdata', 'AjaxdataController@getdata')->name('ajaxdata.getdata');

/*-- PayrollReports --*/
Route::get('ReportEpfEtf',['uses' => 'EmployeePayslipController@reportEpfEtf', 'as' => 'ReportEpfEtf']); 
Route::post('DownloadEpfEtf',['uses' => 'EmployeePayslipController@downloadEpfEtf', 'as' => 'DownloadEpfEtf']); 
Route::get('ReportSignatureSheet',['uses' => 'EmployeePayslipController@reportSignatureSheet', 'as' => 'ReportSignatureSheet']); 
Route::post('DownloadSignatureSheet',['uses' => 'EmployeePayslipController@downloadSignatureSheet', 'as' => 'DownloadSignatureSheet']);
Route::get('ReportSalarySheet',['uses' => 'EmployeePayslipController@reportSalarySheet', 'as' => 'ReportSalarySheet']); 
Route::post('DownloadSalarySheet',['uses' => 'EmployeePayslipController@downloadSalarySheet', 'as' => 'DownloadSalarySheet']);
Route::get('ReportPayRegister',['uses' => 'EmployeePayslipController@reportPayRegister', 'as' => 'ReportPayRegister']); 
Route::post('DownloadPayRegister',['uses' => 'EmployeePayslipController@downloadPayRegister', 'as' => 'DownloadPayRegister']);
Route::post('/checkPayRegister', 'EmployeePayslipController@checkPayRegister');
Route::post('/checkPayslipListBankSlip', 'PaySlipBank@checkPayslipListBankSlip');

Route::get('ReportSixMonth',['uses' => 'EmployeePayrollReport@reportSixMonth', 'as' => 'ReportSixMonth']); 
Route::post('/previewSixMonth', 'EmployeePayrollReport@previewSixMonth');

Route::get('ReportAddition',['uses' => 'EmployeePayrollReport@reportAddition', 'as' => 'ReportAddition']); 
//Route::post('/previewAddition', 'EmployeePayrollReport@previewAddition');//get-data-from-checkPayslipList-for-testing
Route::get('ReportHeldSalaries',['uses' => 'EmployeePayrollReport@reportHeldSalaries', 'as' => 'ReportHeldSalaries']);
Route::post('/checkHeldSalaryList', 'EmployeePayrollReport@checkHeldSalaryList');
Route::post('/DownloadHeldSalaries', 'EmployeePayrollReport@downloadHeldSalaryList');

Route::get('ReportEmpOvertime',['uses' => 'EmployeePayrollReport@reportEmpOT', 'as' => 'ReportEmpOvertime']); 
Route::post('checkEmpOvertime',['uses' => 'EmployeePayrollReport@previewEmpOT', 'as' => 'checkEmpOvertime']); 
Route::post('DownloadEmpOvertime',['uses' => 'EmployeePayrollReport@downloadEmpOT', 'as' => 'DownloadEmpOvertime']);
/*-- PayrollReports --*/
/*-- PayrollStatements --*/
Route::get('EmpSalaryPayVoucher',['uses' => 'EmployeePayrollStatement@reportEmpSalaryVoucher', 'as' => 'EmpSalaryPayVoucher']); 
Route::get('EmpIncentivePayVoucher',['uses' => 'EmployeePayrollStatement@reportEmpIncentiveVoucher', 'as' => 'EmpIncentivePayVoucher']); 

Route::get('ReportBankAdvice',['uses' => 'EmployeePayrollStatement@reportBankAdvice', 'as' => 'ReportBankAdvice']); 
Route::post('/previewBankAdvice', 'EmployeePayrollStatement@previewBankAdvice');
Route::get('ReportPaySummary',['uses' => 'EmployeePayrollStatement@reportPaySummary', 'as' => 'ReportPaySummary']); 
Route::post('/previewPaySummary', 'EmployeePayrollStatement@previewPaySummary');

Route::get('EmpSalaryJournalVoucher',['uses' => 'EmployeePayrollStatement@glEmpSalaryVoucher', 'as' => 'EmpSalaryJournalVoucher']); 
Route::get('EmpEpfEtfJournalVoucher',['uses' => 'EmployeePayrollStatement@glEmpEpfEtfVoucher', 'as' => 'EmpEpfEtfJournalVoucher']); 
/*-- PayrollStatements --*/

// Employee Recurement details 

Route::get('/viewEmployeeRequrement/{id}',['uses' => 'EmployeementrequrementController@index', 'as' => 'viewEmployeeRequrement']);
Route::post('/EmployeeRequrementinsert' ,'EmployeementrequrementController@insert')->name('EmployeeRequrementinsert');

Route::get('BranchShow/{id}',['uses' => 'BranchController@index', 'as' => 'BranchShow']);

// job location controller routes 
Route::get('joblocations', 'JoblocationController@index')->name('joblocations');
Route::post('joblocationsave', 'JoblocationController@insert')->name('joblocationsave');
Route::get('joblocationslist', 'JoblocationController@locationlist')->name('joblocationslist');
Route::post('joblocationsedit', 'JoblocationController@edit')->name('joblocationsedit');
Route::get('/joblocationsstatus/{id}/{status}','JoblocationController@status')->name('joblocationsstatus');
Route::post('joblocationsdelete', 'JoblocationController@delete')->name('joblocationsdelete');

Route::get('joballocation', 'JoballocationController@index')->name('joballocation');
Route::post('joballocationsave', 'JoballocationController@insert')->name('joballocationsave');
Route::get('joballocationslist', 'JoballocationController@allocationlist')->name('joballocationslist');
Route::post('joballocationedit', 'JoballocationController@edit')->name('joballocationedit');
Route::post('joballocationupdate', 'JoballocationController@update')->name('joballocationupdate');
Route::post('joballocationdelete', 'JoballocationController@delete')->name('joballocationdelete');

Route::get('jobattendance', 'JobattendanceController@index')->name('jobattendance');
Route::post('attendancegetemplist', 'JobattendanceController@getemplist')->name('attendancegetemplist');
Route::post('jobattendancesave', 'JobattendanceController@insert')->name('jobattendancesave');
Route::get('jobattendancelist', 'JobattendanceController@attendancelist')->name('jobattendancelist');

Route::post('jobattendanceedit', 'JobattendanceController@edit')->name('jobattendanceedit');
Route::post('jobattendanceupdate', 'JobattendanceController@update')->name('jobattendanceupdate');
Route::post('jobattendancedelete', 'JobattendanceController@delete')->name('jobattendancedelete');

// corparate dashboard
Route::get('/getemployeedashboard_RecruitmentChart', 'EmployeemanagementdashboardController@getemployeedashboard_RecruitmentChart')->name('getemployeedashboard_RecruitmentChart');
Route::get('/getshiftdashboard_EmployeeChart', 'ShiftdashboardController@getshiftdashboard_EmployeeChart')->name('getshiftdashboard_EmployeeChart');

Route::get('BranchShow/{id}',['uses' => 'BranchController@index', 'as' => 'BranchShow']);

//deparmtne wise attendance routes
Route::get('/departmentwise_generateattendancereport', 'DepartmentwisereportController@generateattendancereport')->name('departmentwise_generateattendancereport');
Route::get('/departmentwise_gettotalattendanceemployee', 'DepartmentwisereportController@gettotalattendanceemployee')->name('departmentwise_gettotalattendanceemployee');
// job allocation report routes
Route::get('/joballocationreport', 'Rptlocationcontroller@index')->name('joballocationreport');
Route::get('/getjoblocationreport', 'Rptlocationcontroller@joblocationreport')->name('getjoblocationreport');
Route::get('/getshiftdetails', 'ApointmentletterController@getShiftDetails')->name('getshiftdetails');
Route::post('appoinementletterprintdata', 'ApointmentletterPDFController@printdata')->name('appoinementletterprintdata');

// Exam Subjects controller routes
Route::get('/examsubjects' ,'ExamsubjectController@index')->name('examsubjects');
Route::get('/examsubjectlist' ,'ExamsubjectController@subjectlist')->name('examsubjectlist');
Route::post('/examsubjectinsert' ,'ExamsubjectController@insert')->name('examsubjectinsert');
Route::post('/examsubjectedit' ,'ExamsubjectController@edit')->name('examsubjectedit');
Route::post('/examsubjectupdate' ,'ExamsubjectController@update')->name('examsubjectupdate');
Route::post('/examsubjectdelete' ,'ExamsubjectController@delete')->name('examsubjectdelete');
Route::get('/examsubjectstatus/{id}/{stasus}','ExamsubjectController@status')->name('examsubjectstatus');

// employee exam result routes
Route::get('/viewemployeeexamresult/{id}',['uses' => 'EmployeeexamresultController@show', 'as' => 'viewemployeeexamresult']);
Route::post('/examresultinsert' ,'EmployeeexamresultController@insert')->name('examresultinsert');
Route::post('/examresultlist' ,'EmployeeexamresultController@resultlist')->name('examresultlist');
Route::post('/examresultedit' ,'EmployeeexamresultController@edit')->name('examresultedit');
Route::post('/examresultupdate' ,'EmployeeexamresultController@update')->name('examresultupdate');
Route::post('/examresultdelete' ,'EmployeeexamresultController@delete')->name('examresultdelete');

// Service Letter Routes
Route::get('serviceletter', 'ServiceletterController@index')->name('serviceletter');
Route::post('serviceletterinsert', 'ServiceletterController@insert')->name('serviceletterinsert');
Route::get('serviceletterlist', 'ServiceletterController@letterlist')->name('serviceletterlist');
Route::post('serviceletteredit', 'ServiceletterController@edit')->name('serviceletteredit');
Route::get('/serviceletterstatus/{id}/{status}','ServiceletterController@status')->name('serviceletterstatus');
Route::post('serviceletterdelete', 'ServiceletterController@delete')->name('serviceletterdelete');
Route::post('serviceletterprintdata', 'ServiceletterPDFController@printdata')->name('serviceletterprintdata');
Route::get('servicelettergetdepartmentfilter/{categoryId}', 'ServiceletterController@getdepartmentfilter')->name('servicelettergetdepartmentfilter');
Route::get('servicelettergetemployeefilter/{categoryId}', 'ServiceletterController@getemployeefilter')->name('servicelettergetemployeefilter');
Route::get('servicelettergetdetails/{categoryId}', 'ServiceletterController@getEmployeeDetails')->name('servicelettergetdetails');

// Warning Letter Routes
Route::get('warningletter', 'WarningletterController@index')->name('warningletter');
Route::post('warningletterinsert', 'WarningletterController@insert')->name('warningletterinsert');
Route::get('warningletterlist', 'WarningletterController@letterlist')->name('warningletterlist');
Route::post('warningletteredit', 'WarningletterController@edit')->name('warningletteredit');
Route::get('/warningletterstatus/{id}/{status}','WarningletterController@status')->name('warningletterstatus');
Route::post('warningletterdelete', 'WarningletterController@delete')->name('warningletterdelete');
Route::post('warningletterprintdata', 'WarningletterPDFController@printdata')->name('warningletterprintdata');
Route::get('warninglettergetdepartmentfilter/{categoryId}', 'WarningletterController@getdepartmentfilter')->name('warninglettergetdepartmentfilter');
Route::get('warninglettergetemployeefilter/{categoryId}', 'WarningletterController@getemployeefilter')->name('warninglettergetemployeefilter');
Route::get('warninglettergetjobfilter/{categoryId}', 'WarningletterController@getjobfilter')->name('warninglettergetjobfilter');

// Resign Letter Routes
Route::get('resignletter', 'ResignletterController@index')->name('resignletter');
Route::post('resignletterinsert', 'ResignletterController@insert')->name('resignletterinsert');
Route::get('resignletterlist', 'ResignletterController@letterlist')->name('resignletterlist');
Route::post('resignletteredit', 'ResignletterController@edit')->name('resignletteredit');
Route::get('/resignletterstatus/{id}/{status}','ResignletterController@status')->name('resignletterstatus');
Route::post('resignletterdelete', 'ResignletterController@delete')->name('resignletterdelete');
Route::post('resignletterprintdata', 'ResignletterPDFController@printdata')->name('resignletterprintdata');
Route::get('resignlettergetdepartmentfilter/{categoryId}', 'ResignletterController@getdepartmentfilter')->name('resignlettergetdepartmentfilter');
Route::get('resignlettergetemployeefilter/{categoryId}', 'ResignletterController@getemployeefilter')->name('resignlettergetemployeefilter');
Route::get('resignlettergetdetails/{categoryId}', 'ResignletterController@getEmployeeDetails')->name('resignlettergetdetails');

// Salary Increment Letter Routes
Route::get('salary_incletter', 'Salary_incletterController@index')->name('salary_incletter');
Route::post('salary_incletterinsert', 'Salary_incletterController@insert')->name('salary_incletterinsert');
Route::get('salary_incletterlist', 'Salary_incletterController@letterlist')->name('salary_incletterlist');
Route::post('salary_incletteredit', 'Salary_incletterController@edit')->name('salary_incletteredit');
Route::get('/salary_incletterstatus/{id}/{status}','Salary_incletterController@status')->name('salary_incletterstatus');
Route::post('salary_incletterdelete', 'Salary_incletterController@delete')->name('salary_incletterdelete');
Route::post('salary_incletterprintdata', 'Salary_incletterPDFController@printdata')->name('salary_incletterprintdata');
Route::get('salary_inclettergetdepartmentfilter/{categoryId}', 'Salary_incletterController@getdepartmentfilter')->name('salary_inclettergetdepartmentfilter');
Route::get('salary_inclettergetemployeefilter/{categoryId}', 'Salary_incletterController@getemployeefilter')->name('salary_inclettergetemployeefilter');
Route::get('salary_inclettergetdetails/{categoryId}', 'Salary_incletterController@getEmployeeDetails')->name('salary_inclettergetdetails');

// Promotion Letter Routes
Route::get('promotionletter', 'PromotionletterController@index')->name('promotionletter');
Route::post('promotionletterinsert', 'PromotionletterController@insert')->name('promotionletterinsert');
Route::get('promotionletterlist', 'PromotionletterController@letterlist')->name('promotionletterlist');
Route::post('promotionletteredit', 'PromotionletterController@edit')->name('promotionletteredit');
Route::get('/promotionletterstatus/{id}/{status}','PromotionletterController@status')->name('promotionletterstatus');
Route::post('promotionletterdelete', 'PromotionletterController@delete')->name('promotionletterdelete');
Route::post('promotionletterprintdata', 'PromotionletterPDFController@printdata')->name('promotionletterprintdata');
Route::get('promotionlettergetdepartmentfilter/{categoryId}', 'PromotionletterController@getdepartmentfilter')->name('promotionlettergetdepartmentfilter');
Route::get('promotionlettergetemployeefilter/{categoryId}', 'PromotionletterController@getemployeefilter')->name('promotionlettergetemployeefilter');
Route::get('promotionlettergetjobfilter/{categoryId}', 'PromotionletterController@getjobfilter')->name('promotionlettergetjobfilter');

// DS Division controller routes
Route::get('/dsdivision' ,'DSDivisionController@index')->name('dsdivision');
Route::post('/dsdivisioninsert' ,'DSDivisionController@insert')->name('dsdivisioninsert');
Route::post('/dsdivisionedit' ,'DSDivisionController@edit')->name('dsdivisionedit');
Route::post('/dsdivisionupdate' ,'DSDivisionController@update')->name('dsdivisionupdate');
Route::post('/dsdivisiondelete' ,'DSDivisionController@delete')->name('dsdivisiondelete');
Route::get('/dsdivisionstatus/{id}/{stasus}','DSDivisionController@status')->name('dsdivisionstatus');

// GNS Division controller routes
Route::get('/gnsdivision' ,'GNSDivisionController@index')->name('gnsdivision');
Route::post('/gnsdivisioninsert' ,'GNSDivisionController@insert')->name('gnsdivisioninsert');
Route::post('/gnsdivisionedit' ,'GNSDivisionController@edit')->name('gnsdivisionedit');
Route::post('/gnsdivisionupdate' ,'GNSDivisionController@update')->name('gnsdivisionupdate');
Route::post('/gnsdivisiondelete' ,'GNSDivisionController@delete')->name('gnsdivisiondelete');
Route::get('/gnsdivisionstatus/{id}/{stasus}','GNSDivisionController@status')->name('gnsdivisionstatus');

// Police station controller routes
Route::get('/policestation' ,'PolicestationController@index')->name('policestation');
Route::post('/policestationinsert' ,'PolicestationController@insert')->name('policestationinsert');
Route::post('/policestationedit' ,'PolicestationController@edit')->name('policestationedit');
Route::post('/policestationupdate' ,'PolicestationController@update')->name('policestationupdate');
Route::post('/policestationdelete' ,'PolicestationController@delete')->name('policestationdelete');
Route::get('/policestationstatus/{id}/{stasus}','PolicestationController@status')->name('policestationstatus');

// Service Letter Routes
Route::get('serviceletter', 'ServiceletterController@index')->name('serviceletter');
Route::post('serviceletterinsert', 'ServiceletterController@insert')->name('serviceletterinsert');
Route::get('serviceletterlist', 'ServiceletterController@letterlist')->name('serviceletterlist');
Route::post('serviceletteredit', 'ServiceletterController@edit')->name('serviceletteredit');
Route::get('/serviceletterstatus/{id}/{status}','ServiceletterController@status')->name('serviceletterstatus');
Route::post('serviceletterdelete', 'ServiceletterController@delete')->name('serviceletterdelete');
Route::post('serviceletterprintdata', 'ServiceletterPDFController@printdata')->name('serviceletterprintdata');
Route::get('servicelettergetdepartmentfilter/{categoryId}', 'ServiceletterController@getdepartmentfilter')->name('servicelettergetdepartmentfilter');
Route::get('servicelettergetemployeefilter/{categoryId}', 'ServiceletterController@getemployeefilter')->name('servicelettergetemployeefilter');
Route::get('servicelettergetdetails/{categoryId}', 'ServiceletterController@getEmployeeDetails')->name('servicelettergetdetails');

// Warning Letter Routes
Route::get('warningletter', 'WarningletterController@index')->name('warningletter');
Route::post('warningletterinsert', 'WarningletterController@insert')->name('warningletterinsert');
Route::get('warningletterlist', 'WarningletterController@letterlist')->name('warningletterlist');
Route::post('warningletteredit', 'WarningletterController@edit')->name('warningletteredit');
Route::get('/warningletterstatus/{id}/{status}','WarningletterController@status')->name('warningletterstatus');
Route::post('warningletterdelete', 'WarningletterController@delete')->name('warningletterdelete');
Route::post('warningletterprintdata', 'WarningletterPDFController@printdata')->name('warningletterprintdata');
Route::get('warninglettergetdepartmentfilter/{categoryId}', 'WarningletterController@getdepartmentfilter')->name('warninglettergetdepartmentfilter');
Route::get('warninglettergetemployeefilter/{categoryId}', 'WarningletterController@getemployeefilter')->name('warninglettergetemployeefilter');
Route::get('warninglettergetjobfilter/{categoryId}', 'WarningletterController@getjobfilter')->name('warninglettergetjobfilter');

// Resign Letter Routes
Route::get('resignletter', 'ResignletterController@index')->name('resignletter');
Route::post('resignletterinsert', 'ResignletterController@insert')->name('resignletterinsert');
Route::get('resignletterlist', 'ResignletterController@letterlist')->name('resignletterlist');
Route::post('resignletteredit', 'ResignletterController@edit')->name('resignletteredit');
Route::get('/resignletterstatus/{id}/{status}','ResignletterController@status')->name('resignletterstatus');
Route::post('resignletterdelete', 'ResignletterController@delete')->name('resignletterdelete');
Route::post('resignletterprintdata', 'ResignletterPDFController@printdata')->name('resignletterprintdata');
Route::get('resignlettergetdepartmentfilter/{categoryId}', 'ResignletterController@getdepartmentfilter')->name('resignlettergetdepartmentfilter');
Route::get('resignlettergetemployeefilter/{categoryId}', 'ResignletterController@getemployeefilter')->name('resignlettergetemployeefilter');
Route::get('resignlettergetdetails/{categoryId}', 'ResignletterController@getEmployeeDetails')->name('resignlettergetdetails');

// Salary Increment Letter Routes
Route::get('salary_incletter', 'Salary_incletterController@index')->name('salary_incletter');
Route::post('salary_incletterinsert', 'Salary_incletterController@insert')->name('salary_incletterinsert');
Route::get('salary_incletterlist', 'Salary_incletterController@letterlist')->name('salary_incletterlist');
Route::post('salary_incletteredit', 'Salary_incletterController@edit')->name('salary_incletteredit');
Route::get('/salary_incletterstatus/{id}/{status}','Salary_incletterController@status')->name('salary_incletterstatus');
Route::post('salary_incletterdelete', 'Salary_incletterController@delete')->name('salary_incletterdelete');
Route::post('salary_incletterprintdata', 'Salary_incletterPDFController@printdata')->name('salary_incletterprintdata');
Route::get('salary_inclettergetdepartmentfilter/{categoryId}', 'Salary_incletterController@getdepartmentfilter')->name('salary_inclettergetdepartmentfilter');
Route::get('salary_inclettergetemployeefilter/{categoryId}', 'Salary_incletterController@getemployeefilter')->name('salary_inclettergetemployeefilter');
Route::get('salary_inclettergetdetails/{categoryId}', 'Salary_incletterController@getEmployeeDetails')->name('salary_inclettergetdetails');

// Promotion Letter Routes
Route::get('promotionletter', 'PromotionletterController@index')->name('promotionletter');
Route::post('promotionletterinsert', 'PromotionletterController@insert')->name('promotionletterinsert');
Route::get('promotionletterlist', 'PromotionletterController@letterlist')->name('promotionletterlist');
Route::post('promotionletteredit', 'PromotionletterController@edit')->name('promotionletteredit');
Route::get('/promotionletterstatus/{id}/{status}','PromotionletterController@status')->name('promotionletterstatus');
Route::post('promotionletterdelete', 'PromotionletterController@delete')->name('promotionletterdelete');
Route::post('promotionletterprintdata', 'PromotionletterPDFController@printdata')->name('promotionletterprintdata');
Route::get('promotionlettergetdepartmentfilter/{categoryId}', 'PromotionletterController@getdepartmentfilter')->name('promotionlettergetdepartmentfilter');
Route::get('promotionlettergetemployeefilter/{categoryId}', 'PromotionletterController@getemployeefilter')->name('promotionlettergetemployeefilter');
Route::get('promotionlettergetjobfilter/{categoryId}', 'PromotionletterController@getjobfilter')->name('promotionlettergetjobfilter');

// Employee shift controller routes
Route::get('/employeeshift' ,'EmployeeShiftController@index')->name('employeeshift');
Route::get('/employeeshiftlist' ,'EmployeeShiftController@requestlist')->name('employeeshiftlist');
Route::post('/employeeshiftinsert' ,'EmployeeShiftController@insert')->name('employeeshiftinsert');
Route::post('/employeeshiftedit' ,'EmployeeShiftController@edit')->name('employeeshiftedit');
Route::post('/employeeshiftview' ,'EmployeeShiftController@view')->name('employeeshiftview');
Route::post('/employeeshifteditdetails' ,'EmployeeShiftController@editlist')->name('employeeshifteditdetails');
Route::post('/employeeshiftupdate' ,'EmployeeShiftController@update')->name('employeeshiftupdate');
Route::post('/employeeshiftdelete' ,'EmployeeShiftController@delete')->name('employeeshiftdelete');
Route::post('/employeeshiftdeletelist' ,'EmployeeShiftController@deletelist')->name('employeeshiftdeletelist');
Route::get('/employeeshiftstatus/{id}/{stasus}','EmployeeShiftController@status')->name('employeeshiftstatus');

Route::get('/functionalmanagementdashboard' ,'FunctionalmanagementdashboardController@index')->name('functionalmanagementdashboard');

// FunctionalKRA controller routes
Route::get('functionaltype/' ,'FunctionaltypeController@index')->name('functionaltype');
Route::get('/functionaltypelist' ,'FunctionaltypeController@requestlist')->name('functionaltypelist');
Route::post('/functionaltypeinsert' ,'FunctionaltypeController@insert')->name('functionaltypeinsert');
Route::post('/functionaltypeedit' ,'FunctionaltypeController@edit')->name('functionaltypeedit');
Route::post('/functionaltypeupdate' ,'FunctionaltypeController@update')->name('functionaltypeupdate');
Route::post('/functionaltypedelete' ,'FunctionaltypeController@delete')->name('functionaltypedelete');
Route::post('/functionaltypeapprove' ,'FunctionaltypeController@approve')->name('functionaltypeapprove');
Route::get('/functionaltypestatus/{id}/{stasus}','FunctionaltypeController@status')->name('functionaltypestatus');


// FunctionalKPI controller routes
Route::get('functionalkpi/' ,'FunctionalkpiController@index')->name('functionalkpi');
Route::get('/functionalkpilist' ,'FunctionalkpiController@requestlist')->name('functionalkpilist');
Route::post('/functionalkpiinsert' ,'FunctionalkpiController@insert')->name('functionalkpiinsert');
Route::post('/functionalkpiedit' ,'FunctionalkpiController@edit')->name('functionalkpiedit');
Route::post('/functionalkpiupdate' ,'FunctionalkpiController@update')->name('functionalkpiupdate');
Route::post('/functionalkpidelete' ,'FunctionalkpiController@delete')->name('functionalkpidelete');
Route::post('/functionalkpiapprove' ,'FunctionalkpiController@approve')->name('functionalkpiapprove');
Route::get('/functionalkpistatus/{id}/{stasus}','FunctionalkpiController@status')->name('functionalkpistatus');

// Functionalparameter controller routes
Route::get('functionalparameter/' ,'FunctionalparameterController@index')->name('functionalparameter');
Route::get('/functionalparameterlist' ,'FunctionalparameterController@requestlist')->name('functionalparameterlist');
Route::post('/functionalparameterinsert' ,'FunctionalparameterController@insert')->name('functionalparameterinsert');
Route::post('/functionalparameteredit' ,'FunctionalparameterController@edit')->name('functionalparameteredit');
Route::post('/functionalparameterupdate' ,'FunctionalparameterController@update')->name('functionalparameterupdate');
Route::post('/functionalparameterdelete' ,'FunctionalparameterController@delete')->name('functionalparameterdelete');
Route::post('/functionalparameterapprove' ,'FunctionalparameterController@approve')->name('functionalparameterapprove');
Route::get('/functionalparameterstatus/{id}/{stasus}','FunctionalparameterController@status')->name('functionalparameterstatus');
Route::get('/functionalparametergetkpifilter/{categoryId}', 'FunctionalparameterController@getkpifilter')->name('functionalparametergetkpifilter');

// Functionl Parameter weightage controller routes
Route::get('/functionalweightage' ,'FunctionalweightageController@index')->name('functionalweightage');
Route::get('/functionalweightagelist' ,'FunctionalweightageController@requestlist')->name('functionalweightagelist');
Route::post('/functionalweightageinsert' ,'FunctionalweightageController@insert')->name('functionalweightageinsert');
Route::post('/functionalweightagetedit' ,'FunctionalweightageController@edit')->name('functionalweightageedit');
Route::post('/functionalweightageupdate' ,'FunctionalweightageController@update')->name('functionalweightageupdate');
Route::post('/functionalweightagedelete' ,'FunctionalweightageController@delete')->name('functionalweightagedelete');
Route::post('/functionalweightageapprove' ,'FunctionalweightageController@approve')->name('functionalweightageapprove');
Route::get('/functionalweightagestatus/{id}/{stasus}','FunctionalweightageController@status')->name('functionalweightagestatus');
Route::get('/functionalweightagegetkpifilter/{categoryId}', 'FunctionalweightageController@getkpifilter')->name('functionalweightagegetkpifilter');
Route::get('/functionalweightagegetparameterfilter/{categoryId}', 'FunctionalweightageController@getparameterfilter')->name('functionalweightagegetparameterfilter');

// Functionalmeasurement controller routes
Route::get('functionalmeasurement/' ,'FunctionalmeasurementController@index')->name('functionalmeasurement');
Route::get('/functionalmeasurementlist' ,'FunctionalmeasurementController@requestlist')->name('functionalmeasurementlist');
Route::post('/functionalmeasurementinsert' ,'FunctionalmeasurementController@insert')->name('functionalmeasurementinsert');
Route::post('/functionalmeasurementedit' ,'FunctionalmeasurementController@edit')->name('functionalmeasurementedit');
Route::post('/functionalmeasurementupdate' ,'FunctionalmeasurementController@update')->name('functionalmeasurementupdate');
Route::post('/functionalmeasurementdelete' ,'FunctionalmeasurementController@delete')->name('functionalmeasurementdelete');
Route::post('/functionalmeasurementview' ,'FunctionalmeasurementController@view')->name('functionalmeasurementview');
Route::get('/functionalmeasurementstatus/{id}/{stasus}','FunctionalmeasurementController@status')->name('functionalmeasurementstatus');
Route::get('/functionalmeasurementgetkpifilter/{categoryId}', 'FunctionalmeasurementController@getkpifilter')->name('functionalmeasurementgetkpifilter');
Route::get('/functionalmeasurementgetparameterfilter/{categoryId}', 'FunctionalmeasurementController@getparameterfilter')->name('functionalmeasurementgetparameterfilter');

// Functionalmeasurement weightage controller routes
Route::get('functionalmeasurementweightage/' ,'FunctionalmeasurementweightageController@index')->name('functionalmeasurementweightage');
Route::get('/functionalmeasurementweightagelist' ,'FunctionalmeasurementweightageController@requestlist')->name('functionalmeasurementweightagelist');
Route::post('/functionalmeasurementweightageinsert' ,'FunctionalmeasurementweightageController@insert')->name('functionalmeasurementweightageinsert');
Route::post('/functionalmeasurementweightageedit' ,'FunctionalmeasurementweightageController@edit')->name('functionalmeasurementweightageedit');
Route::post('/functionalmeasurementweightageupdate' ,'FunctionalmeasurementweightageController@update')->name('functionalmeasurementweightageupdate');
Route::post('/functionalmeasurementweightagedelete' ,'FunctionalmeasurementweightageController@delete')->name('functionalmeasurementweightagedelete');
Route::post('/functionalmeasurementweightageapprove' ,'FunctionalmeasurementweightageController@approve')->name('functionalmeasurementweightageapprove');
Route::get('/functionalmeasurementweightagestatus/{id}/{stasus}','FunctionalmeasurementweightageController@status')->name('functionalmeasurementweightagestatus');
Route::get('/functionalmeasurementweightagegetkpifilter/{categoryId}', 'FunctionalmeasurementweightageController@getkpifilter')->name('functionalmeasurementweightagegetkpifilter');
Route::get('/functionalmeasurementweightagegetparameterfilter/{categoryId}', 'FunctionalmeasurementweightageController@getparameterfilter')->name('functionalmeasurementweightagegetparameterfilter');
Route::get('/functionalmeasurementweightagegetmeasurementfilter/{categoryId}', 'FunctionalmeasurementweightageController@getmeasurementfilter')->name('functionalmeasurementweightagegetmeasurementfilter');



// Behavioural Atribute controller routes
Route::get('behaviouraltype/' ,'BehaviouraltypeController@index')->name('behaviouraltype');
Route::get('/behaviouraltypelist' ,'BehaviouraltypeController@requestlist')->name('behaviouraltypelist');
Route::post('/behaviouraltypeinsert' ,'BehaviouraltypeController@insert')->name('behaviouraltypeinsert');
Route::post('/behaviouraltypeedit' ,'BehaviouraltypeController@edit')->name('behaviouraltypeedit');
Route::post('/behaviouraltypeupdate' ,'BehaviouraltypeController@update')->name('behaviouraltypeupdate');
Route::post('/behaviouraltypedelete' ,'BehaviouraltypeController@delete')->name('behaviouraltypedelete');
Route::post('/behaviouraltypeapprove' ,'BehaviouraltypeController@approve')->name('behaviouraltypeapprove');
Route::get('/behaviouraltypestatus/{id}/{stasus}','BehaviouraltypeController@status')->name('behaviouraltypestatus');

// Behavioural Weightage controller routes
Route::get('behaviouralweightage/' ,'BehaviouralweightageController@index')->name('behaviouralweightage');
Route::get('/behaviouralweightagelist' ,'BehaviouralweightageController@requestlist')->name('behaviouralweightagelist');
Route::post('/behaviouralweightageinsert' ,'BehaviouralweightageController@insert')->name('behaviouralweightageinsert');
Route::post('/behaviouralweightageedit' ,'BehaviouralweightageController@edit')->name('behaviouralweightageedit');
Route::post('/behaviouralweightageupdate' ,'BehaviouralweightageController@d')->name('behaviouralweightageupdate');
Route::post('/behaviouralweightagedelete' ,'BehaviouralweightageController@delete')->name('behaviouralweightagedelete');
Route::post('/behaviouralweightageapprove' ,'BehaviouralweightageController@approve')->name('behaviouralweightageapprove');
Route::get('/behaviouralweightagestatus/{id}/{stasus}','BehaviouralweightageController@status')->name('behaviouralweightagestatus');

// Kpi Year controller routes
Route::get('kpiyear/' ,'KpiyearController@index')->name('kpiyear');
Route::get('/kpiyearlist' ,'KpiyearController@requestlist')->name('kpiyearlist');
Route::post('/kpiyearinsert' ,'KpiyearController@insert')->name('kpiyearinsert');
Route::post('/kpiyearedit' ,'KpiyearController@edit')->name('kpiyearedit');
Route::post('/kpiyearupdate' ,'KpiyearController@update')->name('kpiyearupdate');
Route::post('/kpiyeardelete' ,'KpiyearController@delete')->name('kpiyeardelete');
Route::post('/kpiyearapprove' ,'KpiyearController@approve')->name('kpiyearapprove');
Route::get('/kpiyearstatus/{id}/{stasus}','KpiyearController@status')->name('kpiyearstatus');

// KPI Allocation controller routes
Route::get('kpiallocation/' ,'KpiallocationController@index')->name('kpiallocation');
Route::get('/kpiallocationlist' ,'KpiallocationController@requestlist')->name('kpiallocationlist');
Route::post('/kpiallocationinsert', 'KpiallocationController@insert')->name('kpiallocationinsert');
Route::post('/kpiallocationedit' ,'KpiallocationController@edit')->name('kpiallocationedit');
Route::post('/kpiallocationupdate' ,'KpiallocationController@d')->name('kpiallocationupdate');
Route::post('/kpiallocationdelete' ,'KpiallocationController@delete')->name('kpiallocationdelete');
Route::post('/kpiallocationview' ,'KpiallocationController@view')->name('kpiallocationview');
Route::get('/kpiallocationstatus/{id}/{stasus}','KpiallocationController@status')->name('kpiallocationstatus');
Route::get('/kpiallocationapprove/{id}/{app_stasus}','KpiallocationController@app_status')->name('kpiallocationapprove');
Route::get('/kpiallocationgetkpifilter/{categoryId}', 'KpiallocationController@getkpifilter')->name('kpiallocationgetkpifilter');
Route::get('/kpiallocationgetparameterfilter/{categoryId}', 'KpiallocationController@getparameterfilter')->name('kpiallocationgetparameterfilter');
Route::get('/kpiallocationgetmeasurementfilter/{categoryId}', 'KpiallocationController@getmeasurementfilter')->name('kpiallocationgetmeasurementfilter');
Route::get('/kpiallocationgetdepartmentfilter/{categoryId}', 'KpiallocationController@getdepartmentfilter')->name('kpiallocationgetdepartmentfilter');

// Employee Allocation controller routes
Route::get('empallocation/' ,'Emp_kpi_allocationController@index')->name('empallocation');
Route::get('/empallocationlist' ,'Emp_kpi_allocationController@requestlist')->name('empallocationlist');
Route::get('/empallocationlist2' ,'Emp_kpi_allocationController@requestlist2')->name('empallocationlist2');
Route::post('/empallocationinsert' ,'Emp_kpi_allocationController@insert')->name('empallocationinsert');
// Route::post('/empallocationedit' ,'Emp_kpi_allocationController@edit')->name('empallocationedit');
// Route::post('/empallocationupdate' ,'Emp_kpi_allocationController@d')->name('empallocationupdate');
Route::post('/empallocationdelete' ,'Emp_kpi_allocationController@delete')->name('empallocationdelete');
Route::post('/empallocationview' ,'Emp_kpi_allocationController@view')->name('empallocationview');
Route::post('/empallocationadd' ,'Emp_kpi_allocationController@add')->name('empallocationadd');
Route::get('/empallocationstatus/{id}/{stasus}','Emp_kpi_allocationController@status')->name('empallocationstatus');
Route::get('/empallocationgetfigurefilter', 'Emp_kpi_allocationController@getfigurefilter')->name('empallocationgetfigurefilter');
// Route::get('/empallocationgetdeptfilter/{categoryId}', 'Emp_kpi_allocationController@getdeptfilter')->name('empallocationgetdeptfilter');

// leave request controller routes
Route::get('/leaverequest' ,'LeaverequestController@index')->name('leaverequest');
Route::post('/leaverequestinsert' ,'LeaverequestController@insert')->name('leaverequestinsert');
Route::get('/leaverequest_list' ,'LeaverequestController@leavereuest_list')->name('leaverequest_list');
Route::post('/leaverequestedit' ,'LeaverequestController@edit')->name('leaverequestedit');
Route::post('/leaverequestupdate' ,'LeaverequestController@update')->name('leaverequestupdate');
Route::post('/leaverequestdelete' ,'LeaverequestController@delete')->name('leaverequestdelete');
Route::post('/leaverequestapprove' ,'LeaverequestController@approve')->name('leaverequestapprove');

Route::post('/employeeleaverequest' ,'LeaverequestController@getemployeeleaverequest')->name('employeeleaverequest');
Route::post('/employeeleaverequestdetails' ,'LeaverequestController@getrequestreorddetails')->name('employeeleaverequestdetails');

Route::get('/getdashboard_emp_work_days', 'HomeController@emp_work_days')->name('getdashboard_emp_work_days');
Route::get('/getdashboard_today_birthday', 'HomeController@today_birthday')->name('getdashboard_today_birthday');
Route::get('/getdashboard_thisweek_birthday', 'HomeController@thisweek_birthday')->name('getdashboard_thisweek_birthday');
Route::get('/getdashboard_thismonth_birthday', 'HomeController@thismonth_birthday')->name('getdashboard_thismonth_birthday');

//Covering details
Route::resource('Coverup', 'CoverupController');
Route::post('addCoverup',['uses' => 'CoverupController@store', 'as' => 'addCoverup']); 
Route::get('Coverup',['uses' => 'CoverupController@index', 'as' => 'Coverup']); 
Route::get('coverup_list_dt',['uses' => 'CoverupController@coverup_list_dt', 'as' => 'coverup_list_dt']);
Route::post('Coverup/update', 'CoverupController@update')->name('Coverup.update');
Route::get('Coverup/destroy/{id}', 'CoverupController@destroy');

//Ignore days
Route::resource('IgnoreDay', 'IgnoreDaysController');
Route::post('addIgnoreDay',['uses' => 'IgnoreDaysController@store', 'as' => 'addIgnoreDay']); 
Route::get('IgnoreDay',['uses' => 'IgnoreDaysController@index', 'as' => 'IgnoreDay']); 
Route::post('IgnoreDay/update', 'IgnoreDaysController@update')->name('IgnoreDay.update');
Route::get('IgnoreDay/destroy/{id}', 'IgnoreDaysController@destroy');

Route::post('import-csv', 'EmployeeUploadController@importCSV')->name('import');

// Meal Allowance approval Controller
Route::get('/mealallowanceapproval' ,'MealallowanceapproveController@index')->name('mealallowanceapproval');
Route::post('/mealallowancecreate' ,'MealallowanceapproveController@mealalowance')->name('mealallowancecreate');
Route::post('/mealallowancecreateapprove' ,'MealallowanceapproveController@approveallowances')->name('mealallowancecreateapprove');

//holiday deduction approval Controller
Route::get('/holidaydeductionapproval' ,'HolidayDeductionapproveController@index')->name('holidaydeductionapproval');
Route::post('/holidaydeductioncreate' ,'HolidayDeductionapproveController@holidaydeduction')->name('holidaydeductioncreate');
Route::post('/holidaydeductionapprove' ,'HolidayDeductionapproveController@approveldeduction')->name('holidaydeductionapprove');

/*--  Meal Allowance----*/
Route::resource('MealAllowance', 'MealAllowanceController');
Route::get('MealAllowance',['uses' => 'MealAllowanceController@index', 'as' => 'MealAllowance']); 
Route::post('addMealAllowance',['uses' => 'MealAllowanceController@store', 'as' => 'addMealAllowance']); 
Route::get('MealAllowancelist', 'MealAllowanceController@letterlist')->name('MealAllowancelist'); 
Route::post('MealAllowance/update', 'MealAllowanceController@update')->name('MealAllowance.update');
Route::post('MealAllowance/approve_update', 'MealAllowanceController@approve_update')->name('MealAllowance.approve_update');
Route::get('MealAllowance/destroy/{id}', 'MealAllowanceController@destroy');
/*--  Meal Allowance----*/

/*--  Holiday Deduction----*/
Route::resource('HolidayDeduction', 'HolidayDeductionController');
Route::get('HolidayDeduction',['uses' => 'HolidayDeductionController@index', 'as' => 'HolidayDeduction']);
Route::get('HolidayDeductionlist', 'HolidayDeductionController@letterlist')->name('HolidayDeductionlist'); 
Route::post('addHolidayDeduction',['uses' => 'HolidayDeductionController@store', 'as' => 'addHolidayDeduction']); 
Route::post('HolidayDeduction/update', 'HolidayDeductionController@update')->name('HolidayDeduction.update');
Route::get('HolidayDeduction/destroy/{id}', 'HolidayDeductionController@destroy');
/*--  Holiday Deduction----*/

/*--  Employee Resign date----*/
Route::get('/getEmployeeJoinDate', 'EmployeeController@getEmployeeJoinDate')->name('getEmployeeJoinDate');
/*--  Employee Resign date----*/

/*--  Salary Adjustment----*/
Route::resource('SalaryAdjustment', 'SalaryAdjustmentController');
Route::get('SalaryAdjustment',['uses' => 'SalaryAdjustmentController@index', 'as' => 'SalaryAdjustment']); 
Route::post('SalaryAdjustment',['uses' => 'SalaryAdjustmentController@store', 'as' => 'addSalaryAdjustment']); 
Route::get('SalaryAdjustmentlist', 'SalaryAdjustmentController@letterlist')->name('SalaryAdjustmentlist'); 
Route::post('SalaryAdjustment/update', 'SalaryAdjustmentController@update')->name('SalaryAdjustment.update');
Route::post('SalaryAdjustment/approve_update', 'SalaryAdjustmentController@approve_update')->name('SalaryAdjustment.approve_update');
Route::get('SalaryAdjustment/destroy/{id}', 'SalaryAdjustmentController@destroy');
/*--  Salary Adjustment----*/

/*--  Leave Deduction----*/
Route::resource('LeaveDeduction', 'LeaveDeductionController');
Route::get('LeaveDeduction',['uses' => 'LeaveDeductionController@index', 'as' => 'LeaveDeduction']);
Route::get('LeaveDeductionlist', 'LeaveDeductionController@letterlist')->name('LeaveDeductionlist'); 
Route::post('addLeaveDeduction',['uses' => 'LeaveDeductionController@store', 'as' => 'addLeaveDeduction']); 
Route::post('LeaveDeduction/update', 'LeaveDeductionController@update')->name('LeaveDeduction.update');
Route::get('LeaveDeduction/destroy/{id}', 'LeaveDeductionController@destroy');
/*--  Leave Deduction----*/

Route::get('/checkloanguranteemployee' ,'EmployeeLoanController@checkloangurante')->name('checkloanguranteemployee');

/*--  Attendance Import----*/
Route::post('importAttendance-csv', 'AttendanceUploadController@importCSV')->name('importAttendance');
/*--  Attendance Import----*/

// Employee attendace Report
Route::get('/employeeattendancereport' ,'EmployeeAttedanceReportContrller@index')->name('employeeattendancereport');
Route::post('/employeeattendancereportgenerate' ,'EmployeeAttedanceReportContrller@generatereport')->name('employeeattendancereportgenerate');
Route::get('/employeeotreport' ,'EmployeeAttedanceReportContrller@otreport')->name('employeeotreport');
Route::post('/employeeotreportgenerate' ,'EmployeeAttedanceReportContrller@generateOTreport')->name('employeeotreportgenerate');

Route::get('/auditattendancereport' ,'AuditAttendanceReportController@index')->name('auditattendancereport');
Route::post('/auditgeneratetimereport' ,'AuditAttendanceReportController@generatetimereport')->name('auditgeneratetimereport');
Route::get('/auditotreport' ,'AuditAttendanceReportController@auditotreport')->name('auditotreport');
Route::post('/auditgenerateOTreport' ,'AuditAttendanceReportController@generateauditOTreport')->name('auditgenerateOTreport');

Route::post('leaveapprove_batch', 'LeaveController@leave_approve_batch')->name('leaveapprove_batch');

Route::get('/auditattendancereport' ,'AuditAttendanceReportController@index')->name('auditattendancereport');
Route::post('/auditgeneratetimereport' ,'AuditAttendanceReportController@generatetimereport')->name('auditgeneratetimereport');
Route::post('/auditgeneratetimereportexcel' ,'AuditAttendanceReportController@auditgeneratetimereportexcel')->name('auditgeneratetimereportexcel');

Route::post('/auditPayslipList', 'AuditAttendanceReportController@checkPayRegister')->name('auditPayslipList');
Route::post('DownloadPayAuditRegister',['uses' => 'AuditAttendanceReportController@downloadPayAuditRegister', 'as' => 'DownloadPayAuditRegister']);

Route::get('/auditpayregister',['uses' => 'AuditAttendanceReportController@reportPayRegister', 'as' => 'auditpayregister']);

Route::get('AuditReportSalarySheet',['uses' => 'AuditAttendanceReportController@reportSalarySheet', 'as' => 'AuditReportSalarySheet']); 
Route::post('/AuditcheckPayslipListByDept', 'AuditAttendanceReportController@checkPayslipList');
Route::post('AuditDownloadSalarySheet',['uses' => 'AuditAttendanceReportController@downloadSalarySheet', 'as' => 'DownloadSalarySheet']);

// latemintes approvel controller routes
Route::get('/lateminitesapprovel' ,'LateminitesApprovelController@index')->name('lateminitesapprovel');
Route::post('/getlateminitesapprovel' ,'LateminitesApprovelController@generatelateminites')->name('getlateminitesapprovel');
Route::post('/approvelatemintes' ,'LateminitesApprovelController@approvelatemin')->name('approvelatemintes');

// User account Details
Route::get('useraccountsummery', 'UserAccountController@useraccountsummery_list')->name('useraccountsummery');
Route::post('get_employee_monthlysummery', 'UserAccountController@get_employee_monthlysummery')->name('get_employee_monthlysummery');
Route::get('user_leave_list',['uses' => 'UserAccountController@leave_list_dt', 'as' => 'user_leave_list']);
Route::get('userlogininformation', 'UserAccountController@userlogininformation_list')->name('userlogininformation');

Route::post('/get_employee_salarysheet', 'UserAccountController@downloadSalarySheet')->name('get_employee_salarysheet');
Route::post('/getEmployeeLeaveStatusSummary', 'UserAccountController@getemployeeleavestatus');
Route::post('EmployeeLeaveApply',['uses' => 'UserAccountController@leaveapply', 'as' => 'EmployeeLeaveApply']); 
Route::put('/employees/{id}/update-image', 'UserAccountController@updateImage')->name('employees.update-image');

// Employee Shift Extend controller routes
Route::get('/empshiftextend' ,'ShiftExtendController@index')->name('empshiftextend');
Route::get('/empshiftextendlist' ,'ShiftExtendController@requestlist')->name('empshiftextendlist');
Route::post('/empshiftextendinsert' ,'ShiftExtendController@insert')->name('empshiftextendinsert');
Route::post('/empshiftextendedit' ,'ShiftExtendController@edit')->name('empshiftextendedit');
Route::post('/empshiftextendview' ,'ShiftExtendController@view')->name('empshiftextendview');
Route::post('/empshiftextendeditdetails' ,'ShiftExtendController@editlist')->name('empshiftextendeditdetails');
Route::post('/empshiftextendupdate' ,'ShiftExtendController@update')->name('empshiftextendupdate');
Route::post('/empshiftextenddelete' ,'ShiftExtendController@delete')->name('empshiftextenddelete');
Route::post('/empshiftextenddeletelist' ,'ShiftExtendController@deletelist')->name('empshiftextenddeletelist');
Route::get('/empshiftextendstatus/{id}/{stasus}','ShiftExtendController@status')->name('empshiftextendstatus');
Route::get('employee_list_for_shift', 'ShiftExtendController@employee_list_for_shift')->name('employee_list_for_shift');
Route::post('/shift_extend_allocate_csv' ,'ShiftExtendController@shift_extend_allocate_csv')->name('shift_extend_allocate_csv');

Route::get('department_list_sel3', 'DepartmentController@department_list_sel3')->name('department_list_sel3');

Route::post('/calculate-working-days', 'LeaveController@calculateWorkingDays')->name('calculate-working-days');

Route::post('/exam-subjects','EmployeeexamresultController@getExamSubjects')->name('getexamsubjects');

Route::get('/dashboard/nextmonth-birthday','HomeController@nextmonth_birthday')->name('getdashboard_nextmonth_birthday');

Route::get('/password-reset', 'Auth\UserPasswordResetController@showForm')->name('password-reset');
Route::post('/password-reset', 'Auth\UserPasswordResetController@reset')->name('manual.reset.submit');

// machine Controller Routes
Route::resource('Machine', 'MachineController');
Route::get('machines', 'MachineController@index')->name('machines');
Route::post('addMachine',['uses' => 'MachineController@store', 'as' => 'addMachine']); 
Route::post('Machine/update', 'MachineController@update')->name('Machine.update');
Route::get('Machine/destroy/{id}', 'MachineController@destroy');

// Production Controller Routes
Route::resource('Product', 'ProductController');
Route::get('products', 'ProductController@index')->name('products');
Route::post('addProduct',['uses' => 'ProductController@store', 'as' => 'addProduct']); 
Route::post('Product/update', 'ProductController@update')->name('Product.update');
Route::get('Product/destroy/{id}', 'ProductController@destroy');

// Production Allocation Controller Routes
Route::get('productionallocation', 'ProductionEmployeeAllocationController@index')->name('productionallocation');
Route::get('/productallocationlist' ,'ProductionEmployeeAllocationController@requestlist')->name('productallocationlist');
Route::post('/productallocationinsert' ,'ProductionEmployeeAllocationController@insert')->name('productallocationinsert');
Route::post('/productallocationedit' ,'ProductionEmployeeAllocationController@edit')->name('productallocationedit');
Route::post('/productallocationview' ,'ProductionEmployeeAllocationController@view')->name('productallocationview');
Route::post('/productallocationeditdetails' ,'ProductionEmployeeAllocationController@editlist')->name('productallocationeditdetails');
Route::post('/productallocationupdate' ,'ProductionEmployeeAllocationController@update')->name('productallocationupdate');
Route::post('/productallocationdelete' ,'ProductionEmployeeAllocationController@delete')->name('productallocationdelete');
Route::post('/productallocationdeletelist' ,'ProductionEmployeeAllocationController@deletelist')->name('productallocationdeletelist');
Route::get('/productallocationstatus/{id}/{stasus}','ProductionEmployeeAllocationController@status')->name('productallocationstatus');

// Employee Assigned Devices Routes
Route::get('/viewAssignedDevices/{id}',['uses' => 'EmployeeDevicesController@show', 'as' => 'viewAssignedDevices']);
Route::post('assignedDeviceInsert',['uses' => 'EmployeeDevicesController@create', 'as' => 'assignedDeviceInsert']);
Route::get('getAssignedDeviceDetail/{id}',['uses' => 'EmployeeDevicesController@edit_json', 'as' => 'getAssignedDeviceDetail']);
Route::post('updateAssignedDevice',['uses' => 'EmployeeDevicesController@update', 'as' => 'updateAssignedDevice']);
Route::get('assignedDeviceDelete/{id}',['uses' => 'EmployeeDevicesController@destroy', 'as' => 'assignedDeviceDelete']);
Route::post('assignedDeviceStatusUpdate', ['uses' => 'EmployeeDevicesController@updateStatus', 'as' => 'assignedDeviceStatusUpdate']);


// End User Letter Routes
Route::get('end_user_letter', 'EndUserletterController@index')->name('end_user_letter');
Route::post('end_user_letterinsert', 'EndUserletterController@insert')->name('end_user_letterinsert');
Route::get('end_user_letterlist', 'EndUserletterController@letterlist')->name('end_user_letterlist');
Route::post('end_user_letteredit', 'EndUserletterController@edit')->name('end_user_letteredit');
Route::get('/end_user_letterstatus/{id}/{status}','EndUserletterController@status')->name('end_user_letterstatus');
Route::post('end_user_letterdelete', 'EndUserletterController@delete')->name('end_user_letterdelete');
Route::get('/get-employee-devices/{id}', 'EndUserletterController@getDevices')->name('get-employee-devices');
Route::post('end_user_letterprintdata', 'EndUserletterPDFController@printdata')->name('end_user_letterprintdata');

/*-- common employee letter routes --*/
Route::get('employee_list_letter', 'EmployeeletterController@employee_list_letter')->name('employee_list_letter');

// new routes


// Attendace Sync Controller Routes
Route::get('/attendance_list_ajax', 'AttendanceSyncController@attendance_list_ajax')->name('attendance_list_ajax');
Route::post('/Attendance/getdevicedata', 'AttendanceSyncController@getdevicedata')->name('Attendance.getdevicedata');
Route::post('/Attendance/cleardevicedata', 'AttendanceSyncController@cleardevicedata')->name('Attendance.cleardevicedata');


// Attendance Approval Controller Routes
Route::get('AttendanceApprovel',['uses' => 'AttendanceApprovalController@attendanceapprovel', 'as' => 'AttendanceApprovel']);
Route::get('attendance_list_for_approve',['uses' => 'AttendanceApprovalController@attendance_list_for_approve', 'as' => 'attendance_list_for_approve']);
Route::post('AttendentAprovelBatch', 'AttendanceApprovalController@AttendentAprovelBatch')->name('AttendentAprovelBatch');
Route::get('/getAttendanceApprovel', 'AttendanceApprovalController@getAttendanceApprovel');
Route::post('AttendentAprovel', 'AttendanceApprovalController@AttendentAprovel')->name('AttendentAprovel');


// Late Attendance Controller Routes

Route::get('late_attendance_by_time',['uses' => 'LateAttendanceController@late_attendance_by_time', 'as' => 'late_attendance_by_time']);
Route::get('attendance_by_time_report_list',['uses' => 'LateAttendanceController@attendance_by_time_report_list', 'as' => 'attendance_by_time_report_list']);
Route::get('late_types_sel2',['uses' => 'LateAttendanceController@late_types_sel2', 'as' => 'late_types_sel2']);
Route::post('lateAttendance_mark_as_late',['uses' => 'LateAttendanceController@lateAttendance_mark_as_late', 'as' => 'lateAttendance_mark_as_late']);
Route::get('late_attendance_by_time_approve',['uses' => 'LateAttendanceController@late_attendance_by_time_approve', 'as' => 'late_attendance_by_time_approve']);
Route::get('attendance_by_time_approve_report_list',['uses' => 'LateAttendanceController@attendance_by_time_approve_report_list', 'as' => 'attendance_by_time_approve_report_list']);
Route::post('lateAttendance_mark_as_late_approve',['uses' => 'LateAttendanceController@lateAttendance_mark_as_late_approve', 'as' => 'lateAttendance_mark_as_late_approve']);
Route::get('late_attendances_all',['uses' => 'LateAttendanceController@late_attendances_all', 'as' => 'late_attendances_all']);
Route::get('late_attendance_list_approved',['uses' => 'LateAttendanceController@late_attendance_list_approved', 'as' => 'late_attendance_list_approved']);
Route::post('/late_attendancedestroy', 'LateAttendanceController@destroy_late_attendacne')->name('late_attendancedestroy');

//OT Approve Controller Routes

Route::get('/ot_approve', 'OTApproveController@ot_approve')->name('ot_approve');
Route::post('/get_ot_details', 'OTApproveController@get_ot_details')->name('get_ot_details');
Route::post('/ot_approve_post', 'OTApproveController@ot_approve_post')->name('ot_approve_post');
Route::get('/ot_approved', 'OTApproveController@ot_approved')->name('ot_approved');
Route::get('/ot_approved_list', 'OTApproveController@ot_approved_list')->name('ot_approved_list');
Route::get('/ot_approved_list_monthly', 'OTApproveController@ot_approved_list_monthly')->name('ot_approved_list_monthly');
Route::post('/ot_approved_delete', 'OTApproveController@ot_approved_delete')->name('ot_approved_delete');

// RPT Attendance Controller

Route::get('attendetreportbyemployee', 'RptAttendanceController@attendentbyemployee')->name('attendetreportbyemployee'); 
Route::get('AttendenceReport', 'RptAttendanceController@empoloyeeattendentall')->name('AttendenceReport');
Route::get('exportAttendanceReport', 'RptAttendanceController@exportattendances')->name('exportAttendanceReport'); 
Route::get('/daterange', 'RptAttendanceController@daterange');
Route::post('/daterange/fetch_data', 'RptAttendanceController@fetch_data')->name('daterange.fetch_data');
Route::post('attendentfilter', 'RptAttendanceController@attendentfilter')->name('attendentfilter');  
Route::get('attendance_report_list', 'RptAttendanceController@attendance_report_list')->name('attendance_report_list');
Route::post('get_attendance_by_employee_data', 'RptAttendanceController@get_attendance_by_employee_data')->name('get_attendance_by_employee_data');
Route::get('employee_list_from_attendance_sel2', 'RptAttendanceController@employee_list_from_attendance_sel2')->name('employee_list_from_attendance_sel2');
Route::get('location_list_from_attendance_sel2', 'RptAttendanceController@location_list_from_attendance_sel2')->name('location_list_from_attendance_sel2');
Route::post('attendentbyemployeefilter', 'RptAttendanceController@attendentbyemployeefilter')->name('attendentbyemployeefilter');  
Route::get('/attendetreport', 'RptAttendanceController@attendetreport')->name('attendetreport');
Route::post('/employee/fetch_attend_data', 'RptAttendanceController@fetch_attend_data')->name('employee.fetch_attend_data');
Route::post('atenddatafilter', 'RptAttendanceController@atenddatafilter')->name('atenddatafilter');  


// RPT Leave Report Controller routes
Route::get('/leaveReport', 'RptLeaveController@leavereport')->name('leaveReport'); 
Route::get('/leave_report_list', 'RptLeaveController@leave_report_list')->name('leave_report_list');
Route::get('/employee_list_from_leaves_sel2', 'RptLeaveController@employee_list_from_leaves_sel2')->name('employee_list_from_leaves_sel2');
Route::post('/employee/fetch_leave_data', 'RptLeaveController@fetch_leave_data')->name('employee.fetch_leave_data');
Route::post('leavedatafilter', 'RptLeaveController@leavedatafilter')->name('leavedatafilter');  


// RPT Late Attendance Controller Routes
Route::get('LateAttendance',['uses' => 'RptLateattendanceController@lateattendent', 'as' => 'LateAttendance']);
Route::get('late_attendance_report_list',['uses' => 'RptLateattendanceController@late_attendance_report_list', 'as' => 'late_attendance_report_list']);
Route::get('exportLateAttend', 'RptLateattendanceController@exportLateattend')->name('exportLateAttend');


// Rpt OT Controller  Routes

Route::get('ot_report', 'RptOTController@ot_report')->name('ot_report');
Route::get('ot_report_list',['uses' => 'RptOTController@ot_report_list', 'as' => 'ot_report_list']);
Route::get('ot_report_list_month',['uses' => 'RptOTController@ot_report_list_month', 'as' => 'ot_report_list_month']);
Route::post('ot_report_list_view_more', 'RptOTController@ot_report_list_view_more')->name('ot_report_list_view_more');

//Rpt Nopay Controller

Route::get('no_pay_report', 'RptNopayController@no_pay_report')->name('no_pay_report');
Route::get('no_pay_report_list_month',['uses' => 'RptNopayController@no_pay_report_list_month', 'as' => 'no_pay_report_list_month']);
Route::post('no_pay_days_data',['uses' => 'RptNopayController@no_pay_days_data', 'as' => 'no_pay_days_data']);

// Production ending Controller Routes
Route::get('productionending', 'ProductionEndingController@index')->name('productionending');
Route::get('/productionendinglist' ,'ProductionEndingController@productionlist')->name('productionendinglist');
Route::post('/productionendingfinish' ,'ProductionEndingController@insert')->name('productionendingfinish');
Route::post('/productionendingcancel' ,'ProductionEndingController@cancelproduction')->name('productionendingcancel');
Route::get('employee_list_production', 'ProductionEndingController@employee_list_production')->name('employee_list_production');
Route::get('/employeeproductionreport' ,'ProductionEndingController@employeeproduction')->name('employeeproductionreport');

// NDA Letter Routes
Route::get('NDAletter', 'NDAletterController@index')->name('NDAletter');
Route::post('NDAletterinsert', 'NDAletterController@insert')->name('NDAletterinsert');
Route::get('NDAletterlist', 'NDAletterController@letterlist')->name('NDAletterlist');
Route::post('NDAletteredit', 'NDAletterController@edit')->name('NDAletteredit');
Route::get('/NDAletterstatus/{id}/{status}','NDAletterController@status')->name('NDAletterstatus');
Route::post('NDAletterdelete', 'NDAletterController@delete')->name('NDAletterdelete');
Route::post('NDAletterprintdata', 'NDAletterPDFController@printdata')->name('NDAletterprintdata');
Route::get('NDAlettergetdepartmentfilter/{categoryId}', 'NDAletterController@getdepartmentfilter')->name('NDAlettergetdepartmentfilter');
Route::get('NDAlettergetemployeefilter/{categoryId}', 'NDAletterController@getemployeefilter')->name('NDAlettergetemployeefilter');

// Task Controller Routes
Route::resource('Task', 'TaskController');
Route::get('tasks', 'TaskController@index')->name('tasks');
Route::post('addTask',['uses' => 'TaskController@store', 'as' => 'addTask']); 
Route::post('Task/update', 'TaskController@update')->name('Task.update');
Route::get('Task/destroy/{id}', 'TaskController@destroy');

// Task Allocation Controller Routes
Route::get('taskallocation', 'TaskEmployeeAllocationController@index')->name('taskallocation');
Route::get('/taskallocationlist' ,'TaskEmployeeAllocationController@requestlist')->name('taskallocationlist');
Route::post('/taskallocationinsert' ,'TaskEmployeeAllocationController@insert')->name('taskallocationinsert');
Route::post('/taskallocationedit' ,'TaskEmployeeAllocationController@edit')->name('taskallocationedit');
Route::post('/taskallocationview' ,'TaskEmployeeAllocationController@view')->name('taskallocationview');
Route::post('/taskallocationeditdetails' ,'TaskEmployeeAllocationController@editlist')->name('taskallocationeditdetails');
Route::post('/taskallocationupdate' ,'TaskEmployeeAllocationController@update')->name('taskallocationupdate');
Route::post('/taskallocationdelete' ,'TaskEmployeeAllocationController@delete')->name('taskallocationdelete');
Route::post('/taskallocationdeletelist' ,'TaskEmployeeAllocationController@deletelist')->name('taskallocationdeletelist');
Route::get('/taskallocationstatus/{id}/{stasus}','TaskEmployeeAllocationController@status')->name('taskallocationstatus');

// Location Attendace New Routes
Route::post('single_employeeattendance', 'JobattendanceController@single_employee')->name('single_employeeattendance'); 

// Location Attendance Approve controller

Route::get('jobattendanceapprove', 'JobAttendaceApproveController@index')->name('jobattendanceapprove');
Route::post('jobattendanceapprovesave', 'JobAttendaceApproveController@approveattendace')->name('jobattendanceapprovesave');

// Task ending Controller Routes
Route::get('taskending', 'TaskEndingController@index')->name('taskending');
Route::get('/taskendinglist' ,'TaskEndingController@tasklist')->name('taskendinglist');
Route::post('/taskendingfinish' ,'TaskEndingController@insert')->name('taskendingfinish');
Route::post('/taskendingcancel' ,'TaskEndingController@canceltask')->name('taskendingcancel');
Route::get('employee_list_task', 'TaskEndingController@employee_list_task')->name('employee_list_task');
Route::get('/employeetaskreport' ,'TaskEndingController@employeetask')->name('employeetaskreport');
// Task & Product report Controller Routes
Route::get('/employeetaskproductreport' ,'EmployeeTaskProductController@employeetaskproduct')->name('employeetaskproductreport');

// Location Attendace New Routes
Route::post('single_employeeattendance', 'JobattendanceController@single_employee')->name('single_employeeattendance'); 

// Location Attendance Approve controller

Route::get('jobattendanceapprove', 'JobAttendaceApproveController@index')->name('jobattendanceapprove');
Route::post('jobattendanceapprovesave', 'JobAttendaceApproveController@approveattendace')->name('jobattendanceapprovesave');

/*-- Product Machines Info----*/
Route::resource('Machine', 'ProductMachineController');
Route::get('MachineShow/{id}',['uses' => 'ProductMachineController@index', 'as' => 'MachineShow']);
Route::post('addMachine',['uses' => 'ProductMachineController@store', 'as' => 'addMachine']);
Route::post('Machine/update', 'ProductMachineController@update')->name('Machine.update');
Route::get('Machine/destroy/{id}', 'ProductMachineController@destroy');
Route::get('Machine_list_sel2', 'ProductMachineController@Machine_list_sel2');

// Task & Product report Controller Routes
Route::get('/employeetaskproductreport' ,'EmployeeTaskProductController@employeetaskproduct')->name('employeetaskproductreport');

Route::get('/productiontaskdashboard' ,'ProductionTaskdashboardController@index')->name('productiontaskdashboard');

//Production an Task Approve controller
Route::get('/productiontaskapprove' ,'ProductionTaskApproveController@index')->name('productiontaskapprove');
Route::post('/productiontaskapprovegenerate' ,'ProductionTaskApproveController@generateproductiontask')->name('productiontaskapprovegenerate');
Route::post('/approveproductiontask' ,'ProductionTaskApproveController@approveproductiontask')->name('approveproductiontask');

Route::resource('LocationOt', 'LocationOtController');
Route::get('LocationOt',['uses' => 'LocationOtController@index', 'as' => 'LocationOt']);
Route::get('LocationOtlist', 'LocationOtController@letterlist')->name('LocationOtlist'); 
Route::post('addLocationOt',['uses' => 'LocationOtController@store', 'as' => 'addLocationOt']); 
Route::post('LocationOt/update', 'LocationOtController@update')->name('LocationOt.update');
Route::get('LocationOt/destroy/{id}', 'LocationOtController@destroy');
/*--  Location Ot ----*/

Route::get('unauthorizejobattendanceapprove', 'JobAttendaceApproveController@unauthorizeattendace')->name('unauthorizejobattendanceapprove');

//Job location allwance Controller Routes
Route::get('/locationallwanceapprove' ,'JoblocationallwanceController@index')->name('locationallwanceapprove');
Route::post('/locationallwanceapprovegenerate' ,'JoblocationallwanceController@generatelocationallowance')->name('locationallwanceapprovegenerate');
Route::post('/approvelocationallowance' ,'JoblocationallwanceController@approvelocationallowance')->name('approvelocationallowance');

Route::get('/getattendancesummarychart' ,'DashboarddetailedController@attendacechart')->name('getattendancesummarychart');

// absent nopay controller 
Route::get('/absentnopay' ,'DepartmentviseNopayController@index')->name('absentnopay');
Route::post('/getabsetnopay' ,'DepartmentviseNopayController@getabsetnopay')->name('getabsetnopay');
Route::post('/applyabsentnopay' ,'DepartmentviseNopayController@applyabsentnopay')->name('applyabsentnopay');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    
    return "Cache cleared successfully!";
});