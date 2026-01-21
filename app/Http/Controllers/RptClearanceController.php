<?php

namespace App\Http\Controllers;

use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;
use App\Services\LeavepolicyService;
use Session;

class RptClearanceController extends Controller
{
     protected $leavePolicyService;

      public function __construct(LeavepolicyService $leavePolicyService)
    {
        $this->leavePolicyService = $leavePolicyService;
    }

    public function clearanceReport(Request $request)
    {
        $permission = Auth::user()->can('clearance-report');
        if (!$permission) {
            abort(403);
        }

        if (!Session::has('company_name')) {
            $company_name = DB::table('companies')->value('name');
            Session::put('company_name', $company_name);
        } else {
            $company_name = Session::get('company_name');
        }

        return view('Report.clearanceReport' ,compact('company_name'));
    }


    public function clearance_report_list(Request $request)
    {
        $permission = Auth::user()->can('clearance-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
        $userId = Auth::id();
        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
        
        if (empty($accessibleEmployeeIds)) {
            return response()->json([
                "draw" => intval($request->input('draw')),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ]);
        }

        if (!$request->has('employee') || $request->employee == '') {
            return response()->json([
                "draw" => intval($request->input('draw')),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ]);
        }

        $empId = $request->employee;
        
        $employee = DB::table('employees')
            ->where('emp_id', $empId)
            ->whereIn('id', $accessibleEmployeeIds)
            ->first();
        
        if (!$employee) {
            return response()->json([
                "draw" => intval($request->input('draw')),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ]);
        }

        $data_arr = [];
        
        // 1st row - Employee Name (title row with colspan)
        $data_arr[] = [
            "description" => '<strong>' . EmployeeHelper::getDisplayName((object)[
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'calling_name' => $employee->calling_name
            ]) . '</strong>',
            "quantity_balance" => '',
            "amount" => '',
            "is_title" => true
        ];

        // 2nd row - Employee Assigned Devices header
        $data_arr[] = [
            "description" => '<strong>Employee Assigned Devices</strong>',
            "quantity_balance" => '',  
            "amount" => '',          
            "is_title" => true,
            "is_device_section" => true
        ];

        // Get assigned devices
        $devices = DB::table('employee_assigned_devices')
            ->where('emp_id', $employee->id)
            ->whereIn('status', [1, 2]) 
            ->get();

        if ($devices->count() > 0) {
            foreach ($devices as $device) {
                $data_arr[] = [
                    "description" => ($device->device_type ?? 'N/A') . ' - ' . ($device->model_number ?? 'N/A'),
                    "quantity_balance" => '1',
                    "amount" => '',
                    "is_title" => false,
                    "device_id" => $device->id,
                    "device_status" => $device->status,
                    "is_device_row" => true
                ];
            }
        } else {
            $data_arr[] = [
                "description" => 'No devices assigned',
                "quantity_balance" => '-',
                "amount" => '',
                "is_title" => false,
                "is_device_row" => false
            ];
        }

        // Space row
        $data_arr[] = [
            "description" => '',
            "quantity_balance" => '',
            "amount" => '',
            "is_title" => false,
            "is_device_row" => false
        ];

        // Employee Loan Details header
        $data_arr[] = [
            "description" => '<strong>Employee Loan Details</strong>',
            "quantity_balance" => '',  
            "amount" => '',             
            "is_title" => true,
            "is_device_section" => false
        ];

        // Get loan details with balance calculation
        $loans = DB::table('employee_loans')
            ->join('payroll_profiles', 'employee_loans.payroll_profile_id', '=', 'payroll_profiles.id')
            ->leftJoin(DB::raw('(SELECT employee_loan_id, SUM(installment_value) AS loan_paid 
                                FROM employee_loan_installments 
                                WHERE installment_cancel=0 
                                GROUP BY employee_loan_id) AS drv_prog'), 
                    'employee_loans.id', '=', 'drv_prog.employee_loan_id')
            ->select(
                'employee_loans.id',
                'employee_loans.loan_name',
                'employee_loans.loan_amount',
                DB::raw('IFNULL(drv_prog.loan_paid, 0) AS loan_paid'),
                DB::raw('(employee_loans.loan_amount - IFNULL(drv_prog.loan_paid, 0)) AS loan_balance')
            )
            ->where('payroll_profiles.emp_id', $employee->id)
            ->where(DB::raw('(employee_loans.loan_amount - IFNULL(drv_prog.loan_paid, 0))'), '>', 0)
            ->get();

        if ($loans->count() > 0) {
            foreach ($loans as $loan) {
                $data_arr[] = [
                    "description" => $loan->loan_name ?? 'N/A',
                    "quantity_balance" => number_format($loan->loan_balance, 2),
                    "amount" => number_format($loan->loan_amount, 2),
                    "is_title" => false
                ];
            }
        } else {
            $data_arr[] = [
                "description" => 'No outstanding loans',
                "quantity_balance" => '-',
                "amount" => '-',
                "is_title" => false
            ];
        }


          // Space row
        $data_arr[] = [
            "description" => '',
            "quantity_balance" => '',
            "amount" => '',
            "is_title" => false,
            "is_device_row" => false
        ];

        // Employee Loan Details header
        $data_arr[] = [
            "description" => '<strong>Employee Leave Balance</strong>',
            "quantity_balance" => '',  
            "amount" => '',             
            "is_title" => true,
            "is_device_section" => false
        ];

        $employee = Employee::where('emp_id', $empId)->first();

        $emp_join_date = isset($employee->emp_join_date) ? $employee->emp_join_date : false;
        $join_year = Carbon::parse($emp_join_date)->year;
        $join_month = Carbon::parse($emp_join_date)->month;
        $join_date = Carbon::parse($emp_join_date)->day;
        $full_date = '2022-'.$join_month.'-'.$join_date;
        $empid = $employee->emp_id;
        $job_categoryid = $employee->job_category_id;

        $formated_from_date = date('Y').'-01-01';
        $formated_fromto_date = date('Y').'-12-31';

        $current_year_taken_a_l = (new \App\Leave)->taken_annual_leaves($empid, $formated_from_date, $formated_fromto_date);
        $current_year_taken_c_l = (new \App\Leave)->taken_casual_leaves($empid, $formated_from_date, $formated_fromto_date);
        $current_year_taken_med = (new \App\Leave)->taken_medical_leaves($empid, $formated_from_date, $formated_fromto_date);

        $annualData = $this->leavePolicyService->calculateAnnualLeaves($employee->emp_join_date, $employee->emp_id, $job_categoryid);
        $annual_leaves = $annualData['annual_leaves'];

        $casual_leaves = $this->leavePolicyService->calculateCasualLeaves($employee->emp_join_date, $job_categoryid);
        $medical_leaves = $this->leavePolicyService->getMedicalLeaves($employee->job_category_id);


        $total_no_of_annual_leaves = $annual_leaves;
        $total_no_of_casual_leaves = $casual_leaves;
        $total_no_of_med_leaves = $medical_leaves;

        $available_no_of_annual_leaves = $total_no_of_annual_leaves - $current_year_taken_a_l;
        $available_no_of_casual_leaves = $total_no_of_casual_leaves - $current_year_taken_c_l;
        $available_no_of_med_leaves = $total_no_of_med_leaves - $current_year_taken_med;

        $total_balance = $available_no_of_annual_leaves + $available_no_of_casual_leaves +  $available_no_of_med_leaves;

         if ($total_balance > 0) {
            
                $data_arr[] = [
                    "description" => 'Total Leave Balance',
                    "quantity_balance" => $total_balance,
                    "amount" => '-',
                    "is_title" => false
                ];
            
        } else {
            $data_arr[] = [
                "description" => 'No outstanding Leave Balance',
                "quantity_balance" => '-',
                "amount" => '-',
                "is_title" => false
            ];
        }


        $totalRecords = count($data_arr);

        $response = [
            "draw" => intval($request->input('draw')),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }

    public function updateDeviceClearance(Request $request)
    {
        $permission = Auth::user()->can('clearance-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $checkedDeviceIds = $request->input('checked_device_ids', []);
            $uncheckedDeviceIds = $request->input('unchecked_device_ids', []);
            
            if (!empty($checkedDeviceIds)) {
                DB::table('employee_assigned_devices')
                    ->whereIn('id', $checkedDeviceIds)
                    ->update(['status' => 2]);
            }
            
            if (!empty($uncheckedDeviceIds)) {
                DB::table('employee_assigned_devices')
                    ->whereIn('id', $uncheckedDeviceIds)
                    ->update(['status' => 1]);
            }

            return response()->json(['success' => true, 'message' => 'Device clearance status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating devices: ' . $e->getMessage()], 500);
        }
    }

}
