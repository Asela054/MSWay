<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\PayrollProfile;
use App\EmployeeBank;
use Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeUploadController extends Controller
{
    public function importCSV(Request $request)
    {

        $permission = Auth::user()->can('employee-create');
        if (!$permission) {
            return response()->json(['errors' => 'UnAuthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'import_csv' => 'required|file|mimes:csv,txt',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }
    
        $file = $request->file('import_csv');
        $filePath = $file->getRealPath();

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle); 
            $employees = [];

            while (($column = fgetcsv($handle, 1000, ',')) !== false) {
                $employees[] = [
                    'etfno' => $column[0],
                    'emp_id' => $column[1],
                    'firstname' => $column[2],
                    'middlename' => $column[3],
                    'lastname' => $column[4],
                    'fullname' => $column[5],
                    'emp_name_with_initial' => $column[6],
                    'calling_name' => $column[7],
                    'emp_national_id' => $column[8],
                    'emp_mobile' => $column[9],
                    'emp_birthday' => $column[10],
                    'emp_address' => $column[11],
                    'emp_join_date' => $column[12],
                    'department' => $column[13],
                    'status' => $column[14],
                    'basic_salary' => $column[15],
                    'bank_ac_no' => $column[16],
                    'bank_code' => $column[17],
                    'branch_code' => $column[18],
                ];
            }
            fclose($handle);

            // Validate and insert data for each employee
            foreach ($employees as $employeeData) {
                $rowValidator = Validator::make($employeeData, [
                    'emp_id' => 'required|max:15|unique:employees,emp_id',
                    'etfno' => 'required|unique:employees,emp_etfno,NULL,id,emp_etfno,!0',
                    'firstname' => 'required|string|max:255',
                    'middlename' => 'nullable|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'fullname' => 'required|string|max:255',
                    'emp_name_with_initial' => 'required|string|max:255',
                    'calling_name' => 'required|string|max:255',
                    'emp_national_id' => 'required|string|max:12',
                    'emp_mobile' => 'required|string|max:10',
                    'emp_address' => 'nullable|string|max:255',
                    'department' => 'required|string',
                    'status' => 'required|string',
                    'basic_salary' => 'required|numeric',
                    'bank_ac_no' => 'required|string|max:20',
                    'bank_code' => 'required|string|max:10',
                    'branch_code' => 'required|string|max:10',
                ]);

                if ($rowValidator->fails()) {
                    return response()->json(['errors' => $rowValidator->errors()->all()]);
                }

                $departments = \App\Department::pluck('id', 'name')->toArray();
                $departmentId = $departments[$employeeData['department']] ?? null;

                if (!$departmentId) {
                    return response()->json(['errors' => $employeeData['department']]);
                }  
                
                $emp_birthday = Carbon::parse($employeeData['emp_birthday'])->format('Y-m-d');
                $emp_join_date = Carbon::parse($employeeData['emp_join_date'])->format('Y-m-d');

                if (!$emp_birthday || !$emp_join_date) {
                    return response()->json(['errors' => 'Invalid date format']);
                }
                
                // Insert or update Employee data
                $employee = Employee::updateOrCreate(
                    ['emp_id' => $employeeData['emp_id']],
                    [
                        'emp_etfno' => $employeeData['etfno'],
                        'emp_first_name' => $employeeData['firstname'],
                        'emp_med_name' => $employeeData['middlename'],
                        'emp_last_name' => $employeeData['lastname'],
                        'emp_fullname' => $employeeData['fullname'],
                        'emp_name_with_initial' => $employeeData['emp_name_with_initial'],
                        'calling_name' => $employeeData['calling_name'],
                        'emp_national_id' => $employeeData['emp_national_id'],
                        'emp_mobile' => $employeeData['emp_mobile'],
                        'emp_birthday' => $emp_birthday,
                        'emp_address' => $employeeData['emp_address'],
                        'emp_join_date' => $emp_join_date,
                        'emp_department' => $departmentId,
                        'emp_status' => $employeeData['status'],
                    ]
                );

                // Insert or update Payroll Profile
                PayrollProfile::updateOrCreate(
                    ['emp_id' => $employee->id],
                    [
                        'emp_etfno' => $employeeData['etfno'],
                        'payroll_process_type_id' => 1,
                        'payroll_act_id' => 1,
                        'employee_bank_id' => 0,
                        'employee_executive_level' => 0,
                        'basic_salary' => $employeeData['basic_salary'],
                        'day_salary' => $employeeData['basic_salary'] / 30,
                        'epfetf_contribution' => 'ACTIVE',
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );

                // Insert or update Employee Bank
                EmployeeBank::updateOrCreate(
                    ['emp_id' => $employee->id],
                    [
                        'bank_ac_no' => $employeeData['bank_ac_no'],
                        'bank_code' => $employeeData['bank_code'],
                        'branch_code' => $employeeData['branch_code'],
                    ]
                );
            }
        }

        return response()->json(['success' => 'Employee records uploaded successfully.']);
    }
}
