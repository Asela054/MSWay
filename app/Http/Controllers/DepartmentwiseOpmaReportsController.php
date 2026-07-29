<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use DB;

class DepartmentwiseOpmaReportsController extends Controller
{
     public function index()
    {
        $permission = Auth::user()->can('department-wise-leave-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.employee_attedance_leave_report', compact('companies'));
    }


      public function generateattendanceleavereport(Request $request)
    {
        $department = $request->get('department');
        $selectedmonth = $request->get('selectedmonth');

        $userId = Auth::id();

        // Get company IDs and branch IDs from user_has_companies
        $companyIds = DB::table('user_has_companies')
            ->where('user_id', $userId)
            ->pluck('company_id')
            ->toArray();

        $branchIds = DB::table('user_has_companies')
            ->where('user_id', $userId)
            ->pluck('branch_id')
            ->toArray();

        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);

        if (empty($accessibleEmployeeIds)) {
            return response()->json(['html' => '']);
        }

        // Query to get employee-wise late attendance minutes with month breakdown
        $query = DB::table('employees')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->where('employees.deleted', 0)
             ->where('employees.is_resigned', 0)
            ->whereIn('employees.emp_id', $accessibleEmployeeIds)
            ->when(!empty($companyIds), function ($query) use ($companyIds) {
                $query->whereIn('employees.emp_company', $companyIds);
            })
            ->when(!empty($branchIds), function ($query) use ($branchIds) {
                $query->whereIn('employees.emp_location', $branchIds);
            });

        // Filter by department if specified (not 'All')
        if ($department != 'All' && !empty($department)) {
            $query->where('employees.emp_department', '=', $department);
        }


        // Select employee details and late minutes sums with month/year breakdown
        $query->select(
            'employees.id as emp_primary_id',
            'employees.emp_id',
            'employees.emp_name_with_initial',
            'employees.calling_name',
            'departments.name as dept_name',
            'departments.id as dept_id'
        )
        ->groupBy(
            'employees.id'
        );

        $data = $query->get();
        // Get months in range for date range report
        $monthsInRange = [];
        if (!empty($selectedmonth)) {
            $monthsInRange[] = date('Y-m', strtotime($selectedmonth));
        } 

        // Organize data by employee and month
        $employeeattedance = [];

        
        foreach ($data as $row) {
            if (!isset($employeeattedance[$row->emp_primary_id])) {


        $month = date('Y-m', strtotime($selectedmonth));
        $closedate = date('Y-m-t', strtotime($selectedmonth)); // last day of month
        $formated_from_date = date('Y-m-d', strtotime($month . '-01'));
        $formated_fromto_date = $closedate;

        $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month, $closedate);

        $no_pay_days = number_format((new \App\Leave)->get_no_pay_days($row->emp_id, $month, $closedate), 1);

        $current_year_taken_a_l = (new \App\Leave)->taken_annual_leaves($row->emp_id, $formated_from_date, $formated_fromto_date);

        $current_year_taken_c_l = (new \App\Leave)->taken_casual_leaves($row->emp_id, $formated_from_date, $formated_fromto_date);

        $current_year_taken_med = (new \App\Leave)->taken_medical_leaves($row->emp_id, $formated_from_date, $formated_fromto_date);

        $total_leave = $current_year_taken_a_l + $current_year_taken_c_l;
        
        $total_leave_count = ($total_leave + $no_pay_days + $current_year_taken_med);
        
        $balance = 25 - ($total_leave_count + $work_days);

                $employeeattedance[$row->emp_primary_id] = [
                    'emp_id' => $row->emp_id,
                    'emp_name' => $row->emp_name_with_initial,
                    'calling_name' => $row->calling_name,
                    'dept_name' => $row->dept_name,
                    'dept_id' => $row->dept_id,
                    'work_days' => $work_days,
                    'no_pay_days' => $no_pay_days,
                    'current_year_taken_med' => $current_year_taken_med,
                    'total_leave' => $total_leave,
                    'balance' => $balance
                ];
            }
            
        }


        // Build HTML table
        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="late_report_dt" style="width:100%">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 100px;">EMP ID</th>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 150px;">EMPLOYEE</th>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 150px;">LEAVE DAYS</th>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 150px;">NOPAY DAYS</th>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 150px;">MEDICAL DAYS</th>';

        if (!empty($selectedmonth)) {
            $monthFormatted = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= "<th colspan='2' class='align-middle'>TOTAL ATTENDANCE FOR $monthFormatted</th>";
        }
        $table .= '</tr>';
        $table .= '<tr>';

        if (!empty($selectedmonth)) {
            $table .= '<th class="align-middle" style="width: 125px;">ATTENDANCE DATES = 25</th>';
            $table .= '<th class="align-middle" style="width: 125px;">BALANCE</th>';
        }
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        // Display employee data
        foreach ($employeeattedance as $employee) {
            $table .= '<tr>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_id']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_name']) . ' - ' . htmlspecialchars($employee['calling_name']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['total_leave']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['no_pay_days']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['current_year_taken_med']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['work_days']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['balance']) . '</td>';
            $table .= '</tr>';
        }

        // If no data found, show message
        if (empty($employeeattedance)) {
            $colspan = 3 + count($monthsInRange);
            $table .= "<tr><td colspan='{$colspan}' class='text-center'>No records found</td></tr>";
        }

        $table .= '</tbody>';
        $table .= '</table>';

        // Get department name for the title
        $deptName = 'All Departments';
        if ($department != 'All' && !empty($department)) {
            $dept = DB::table('departments')->where('id', $department)->first();
            $deptName = $dept ? $dept->name : 'Selected Department';
        }

        // Add title and date range information
        $title = '<div class="report-header mb-3">';
        $title .= '<h5>DEPARTMENT WISE ATTENDANCE & LEAVE - ' . $deptName . '</h5>';
        
        if (!empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $title .= '<p class="mb-0"><strong>Period:</strong> ' . $monthName . '</p>';
        } 
        $title .= '</div>';

       // At the end of your generatelatereport function, modify the return statement:
        return response()->json([
            'html' => $title . $table,
            'data' => [
                'monthsInRange' => $monthsInRange,
                'selectedmonth' => $selectedmonth,
                'deptName' => $deptName
            ]
        ]);
    }
}
