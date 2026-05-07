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
            $lineNumber = 1; 
            $errors = [];

            while (($column = fgetcsv($handle, 1000, ',')) !== false) {
                $lineNumber++; 
                
                $employees[] = [
                    'line_number' => $lineNumber,
                    'etfno' => $column[0] ?? '',
                    'emp_id' => $column[1] ?? '',
                    'firstname' => $column[2] ?? '',
                    'middlename' => $column[3] ?? '',
                    'lastname' => $column[4] ?? '',
                    'fullname' => $column[5] ?? '',
                    'emp_name_with_initial' => $column[6] ?? '',
                    'calling_name' => $column[7] ?? '',
                    'emp_national_id' => $column[8] ?? '',
                    'emp_mobile' => $column[9] ?? '',
                    'emp_birthday' => $column[10] ?? '',
                    'emp_address' => $column[11] ?? '',
                    'emp_join_date' => $column[12] ?? '',
                    'department' => $column[13] ?? '',
                    'branch' => $column[14] ?? '',
                    'status' => $column[15] ?? '',
                    'basic_salary' => $column[16] ?? '',
                    'bank_ac_no' => $column[17] ?? '',
                    'bank_code' => $column[18] ?? '',
                    'branch_code' => $column[19] ?? '',
                ];
            }
            fclose($handle);

            foreach ($employees as $employeeData) {
                $currentLine = $employeeData['line_number'];
                
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
                    'branch' => 'required|string',
                    'status' => 'required|string',
                    'basic_salary' => 'required|numeric',
                    'bank_ac_no' => 'required|string|max:20',
                    'bank_code' => 'required|string|max:10',
                    'branch_code' => 'required|string|max:10',
                ]);

                if ($rowValidator->fails()) {
                    foreach ($rowValidator->errors()->all() as $error) {
                        $errors[] = "Line {$currentLine}: {$error}";
                    }
                    continue; 
                }

                $departments = \App\Department::pluck('id', 'name')->toArray();
                $departmentId = $departments[$employeeData['department']] ?? null;

                if (!$departmentId) {
                    $errors[] = "Line {$currentLine}: Department '{$employeeData['department']}' not found in system";
                    continue;
                } 

                $branches = \App\Branch::pluck('id', 'location')->toArray();
                $branchId = $branches[$employeeData['branch']] ?? null;

                if (!$branchId) {
                    $errors[] = "Line {$currentLine}: Branch '{$employeeData['branch']}' not found in system";
                    continue;
                }

                $branch = \App\Branch::find($branchId);
                $companyId = $branch ? $branch->company_id : null;

                if (!$companyId) {
                    $errors[] = "Line {$currentLine}: Company not found for branch '{$employeeData['branch']}'";
                    continue;
                }
                
                $statuses = \App\EmploymentStatus::pluck('id', 'emp_status')->toArray();
                $statusId = $statuses[$employeeData['status']] ?? null;

                if (!$statusId) {
                    $errors[] = "Line {$currentLine}: Employment Status '{$employeeData['status']}' not found in system";
                    continue;
                }
                
                $emp_birthday = $this->parseDate($employeeData['emp_birthday']);
                if (!$emp_birthday) {
                    $errors[] = "Line {$currentLine}: Invalid birthday date format: " . $employeeData['emp_birthday'];
                    continue;
                }

                $emp_join_date = $this->parseDate($employeeData['emp_join_date']);
                if (!$emp_join_date) {
                    $errors[] = "Line {$currentLine}: Invalid join date format: " . $employeeData['emp_join_date'];
                    continue;
                }
                
                try {
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
                            'emp_location' => $branchId,
                            'emp_company' => $companyId,
                            'emp_status' => $statusId,
                        ]
                    );

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

                    EmployeeBank::updateOrCreate(
                        ['emp_id' => $employee->id],
                        [
                            'bank_ac_no' => $employeeData['bank_ac_no'],
                            'bank_code' => $employeeData['bank_code'],
                            'branch_code' => $employeeData['branch_code'],
                        ]
                    );
                } catch (\Exception $e) {
                    $errors[] = "Line {$currentLine}: Database error - " . $e->getMessage();
                }
            }
            
            if (!empty($errors)) {
                return response()->json(['errors' => $errors]);
            }
        }

        return response()->json(['success' => 'Employee records uploaded successfully.']);
    }

    private function parseDate($dateString)
    {
        try {
            $dateString = trim($dateString);
            
            $formats = [
                'm/d/Y',
                'n/j/Y',
                'd/m/Y',
                'Y-m-d',
                'Y/m/d',
                'd-m-Y',
                'm-d-Y',
                'Y.m.d',
                'd.m.Y',
                'm.d.Y',
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    $errors = \DateTime::getLastErrors();
                    if ($errors && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                        return $date->format('Y-m-d');
                    }
                }
            }

            return Carbon::parse($dateString)->format('Y-m-d');
            
        } catch (\Exception $e) {
            return null;
        }
    }
}