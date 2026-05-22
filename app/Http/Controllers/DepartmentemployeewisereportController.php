<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use DB;

class DepartmentemployeewisereportController extends Controller
{
    // Leave report Generation
    public function index()
    {
        $permission = Auth::user()->can('department-wise-leave-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.employee_leave_report', compact('companies'));
    }

    public function generateleavereport(Request $request)
    {
        $department = $request->get('department');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
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

        // Query to get employee-wise leave details with month breakdown
        $query = DB::table('leaves')
            ->join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
            ->join('departments', 'employees.emp_department', '=', 'departments.id')
            ->where('employees.deleted', 0)
            ->whereIn('leaves.emp_id', $accessibleEmployeeIds)
            ->where('leaves.status', 'Approved')
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

        // Apply date filters based on report type
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('leaves.leave_from', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('leaves.leave_from', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('leaves.leave_from', [$from_date, $to_date]);
        }

        // Select employee details and leave sums with month/year breakdown
        $query->select(
            'employees.id as emp_primary_id',
            'employees.emp_id',
            'employees.emp_name_with_initial',
            'employees.calling_name',
            'departments.name as dept_name',
            'departments.id as dept_id',
            DB::raw('SUM(CASE WHEN leaves.leave_type = 1 THEN leaves.no_of_days ELSE 0 END) as total_annual_leaves'),
            DB::raw('SUM(CASE WHEN leaves.leave_type = 2 THEN leaves.no_of_days ELSE 0 END) as total_casual_leaves'),
            DB::raw('SUM(CASE WHEN leaves.leave_type = 3 THEN leaves.no_of_days ELSE 0 END) as total_no_pay_leaves'),
            DB::raw('SUM(CASE WHEN leaves.leave_type = 4 THEN leaves.no_of_days ELSE 0 END) as total_medical_leaves'),
            DB::raw('YEAR(leaves.leave_from) as year'),
            DB::raw('MONTH(leaves.leave_from) as month')
        )
       ->groupBy(
            'employees.emp_id',
            'departments.id',
            'year',
            'month'
        );

        $data = $query->get();

        // Get months in range for date range report
        $monthsInRange = [];
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthsInRange[] = date('Y-m', strtotime($selectedmonth));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
        }

        // Organize data by employee and month
        $employeeLeaves = [];
        foreach ($data as $row) {
            if (!isset($employeeLeaves[$row->emp_primary_id])) {
                $employeeLeaves[$row->emp_primary_id] = [
                    'emp_id' => $row->emp_id,
                    'emp_name' => $row->emp_name_with_initial,
                    'calling_name' => $row->calling_name,
                    'dept_name' => $row->dept_name,
                    'dept_id' => $row->dept_id,
                    'leaves' => []
                ];
            }
            
            $yearMonth = $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT);
            $employeeLeaves[$row->emp_primary_id]['leaves'][$yearMonth] = [
                'annual' => (int)$row->total_annual_leaves,
                'casual' => (int)$row->total_casual_leaves,
                'no_pay' => (int)$row->total_no_pay_leaves,
                'medical' => (int)$row->total_medical_leaves
            ];
        }

        // Calculate totals for each month
        $monthlyTotals = [];
        foreach ($monthsInRange as $month) {
            $monthlyTotals[$month] = [
                'annual' => 0,
                'casual' => 0,
                'no_pay' => 0,
                'medical' => 0
            ];
        }

        // Build HTML table
        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="leave_report" style="width:100%">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 100px; min-width: 100px;">EMP ID</th>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 250px; min-width: 200px;">EMPLOYEE</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthFormatted = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= "<th colspan='4' class='align-middle'>$monthFormatted</th>";
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= "<th colspan='4' class='align-middle'>$monthFormatted</th>";
            }
        }
        $table .= '</tr>';
        $table .= '<tr>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<th class="align-middle">ANNUAL</th>';
            $table .= '<th class="align-middle">CASUAL</th>';
            $table .= '<th class="align-middle">NOPAY</th>';
            $table .= '<th class="align-middle">MEDICAL</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            foreach ($monthsInRange as $month) {
                $table .= '<th class="align-middle">ANNUAL</th>';
                $table .= '<th class="align-middle">CASUAL</th>';
                $table .= '<th class="align-middle">NOPAY</th>';
                $table .= '<th class="align-middle">MEDICAL</th>';
            }
        }
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        // Display employee data
        foreach ($employeeLeaves as $employee) {
            $table .= '<tr>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_id']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_name']) . '</td>';

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $yearMonth = date('Y-m', strtotime($selectedmonth));
                $annual = isset($employee['leaves'][$yearMonth]['annual']) ? $employee['leaves'][$yearMonth]['annual'] : 0;
                $casual = isset($employee['leaves'][$yearMonth]['casual']) ? $employee['leaves'][$yearMonth]['casual'] : 0;
                $no_pay = isset($employee['leaves'][$yearMonth]['no_pay']) ? $employee['leaves'][$yearMonth]['no_pay'] : 0;
                $medical = isset($employee['leaves'][$yearMonth]['medical']) ? $employee['leaves'][$yearMonth]['medical'] : 0;

                // Add to monthly totals
                $monthlyTotals[$yearMonth]['annual'] += $annual;
                $monthlyTotals[$yearMonth]['casual'] += $casual;
                $monthlyTotals[$yearMonth]['no_pay'] += $no_pay;
                $monthlyTotals[$yearMonth]['medical'] += $medical;

                $table .= '<td class="align-middle">' . $annual . '</td>';
                $table .= '<td class="align-middle">' . $casual . '</td>';
                $table .= '<td class="align-middle">' . $no_pay . '</td>';
                $table .= '<td class="align-middle">' . $medical . '</td>';
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $yearMonth = date('Y-m', strtotime($month));
                    $annual = isset($employee['leaves'][$yearMonth]['annual']) ? $employee['leaves'][$yearMonth]['annual'] : 0;
                    $casual = isset($employee['leaves'][$yearMonth]['casual']) ? $employee['leaves'][$yearMonth]['casual'] : 0;
                    $no_pay = isset($employee['leaves'][$yearMonth]['no_pay']) ? $employee['leaves'][$yearMonth]['no_pay'] : 0;
                    $medical = isset($employee['leaves'][$yearMonth]['medical']) ? $employee['leaves'][$yearMonth]['medical'] : 0;

                    // Add to monthly totals
                    $monthlyTotals[$month]['annual'] += $annual;
                    $monthlyTotals[$month]['casual'] += $casual;
                    $monthlyTotals[$month]['no_pay'] += $no_pay;
                    $monthlyTotals[$month]['medical'] += $medical;

                    $table .= '<td class="align-middle">' . $annual . '</td>';
                    $table .= '<td class="align-middle">' . $casual . '</td>';
                    $table .= '<td class="align-middle">' . $no_pay . '</td>';
                    $table .= '<td class="align-middle">' . $medical . '</td>';
                }
            }
            $table .= '</tr>';
        }

        // If no data found, show message
        if (empty($employeeLeaves)) {
            $colspan = 3 + (count($monthsInRange) * 4);
            $table .= "<tr><td colspan='{$colspan}' class='text-center'>No leave records found</td></tr>";
        } else {
            // Add totals row
            $table .= "<tr style='background-color: #f2f2f2; font-weight: bold;'>";
            $table .= "<td colspan='2' class='text-right'><strong>TOTAL</strong></td>";
            
            if ($reporttype == '1' && !empty($selectedmonth)) {
                $yearMonth = date('Y-m', strtotime($selectedmonth));
                $table .= "<td class='text-center'><strong>{$monthlyTotals[$yearMonth]['annual']}</strong></td>";
                $table .= "<td class='text-center'><strong>{$monthlyTotals[$yearMonth]['casual']}</strong></td>";
                $table .= "<td class='text-center'><strong>{$monthlyTotals[$yearMonth]['no_pay']}</strong></td>";
                $table .= "<td class='text-center'><strong>{$monthlyTotals[$yearMonth]['medical']}</strong></td>";
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $table .= "<td class='text-center'><strong>{$monthlyTotals[$month]['annual']}</strong></td>";
                    $table .= "<td class='text-center'><strong>{$monthlyTotals[$month]['casual']}</strong></td>";
                    $table .= "<td class='text-center'><strong>{$monthlyTotals[$month]['no_pay']}</strong></td>";
                    $table .= "<td class='text-center'><strong>{$monthlyTotals[$month]['medical']}</strong></td>";
                }
            }
            $table .= "</tr>";
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
        $title .= '<h5>Leave Report - ' . $deptName . '</h5>';
        
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $title .= '<p class="mb-0"><strong>Period:</strong> ' . $monthName . '</p>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $title .= '<p class="mb-0"><strong>Period:</strong> ' . date('d-m-Y', strtotime($from_date)) . ' to ' . date('d-m-Y', strtotime($to_date)) . '</p>';
        }
        $title .= '</div>';

        return response()->json(['html' => $title . $table]);
    }

    // O.T report Generation
     public function otreport()
    {
        $permission = Auth::user()->can('department-wise-ot-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.employee_ot_report', compact('companies'));
    }

     public function generateotreport(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
        $selectedmonth = $request->get('selectedmonth');
        $location = $request->get('location');

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

        // Query to get employee-wise OT details with month breakdown
        $query = DB::table('ot_approved')
            ->join('employees', 'ot_approved.emp_id', '=', 'employees.emp_id')
            ->join('departments', 'employees.emp_department', '=', 'departments.id')
            ->where('employees.deleted', 0)
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

        // Apply date filters based on report type
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('ot_approved.date', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('ot_approved.date', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('ot_approved.date', [$from_date, $to_date]);
        }

        // Select employee details and OT sums with month/year breakdown
        $query->select(
            'employees.id as emp_primary_id',
            'employees.emp_id',
            'employees.emp_name_with_initial',
            'employees.calling_name',
            'departments.name as dept_name',
            'departments.id as dept_id',
            DB::raw('SUM(ot_approved.hours) as total_ot'),
            DB::raw('SUM(ot_approved.double_hours) as total_double_ot'),
            DB::raw('YEAR(ot_approved.date) as year'),
            DB::raw('MONTH(ot_approved.date) as month')
        )
        ->groupBy(
            'employees.emp_id',
            'departments.id',
            'year',
            'month'
        );

        $data = $query->get();

        // Get months in range for date range report
        $monthsInRange = [];
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthsInRange[] = date('Y-m', strtotime($selectedmonth));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
        }

        // Organize data by employee and month
        $employeeOt = [];
        foreach ($data as $row) {
            if (!isset($employeeOt[$row->emp_primary_id])) {
                $employeeOt[$row->emp_primary_id] = [
                    'emp_id' => $row->emp_id,
                    'emp_name' => $row->emp_name_with_initial,
                    'calling_name' => $row->calling_name,
                    'dept_name' => $row->dept_name,
                    'dept_id' => $row->dept_id,
                    'ot_data' => []
                ];
            }
            
            $yearMonth = $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT);
            $employeeOt[$row->emp_primary_id]['ot_data'][$yearMonth] = [
                'normal_ot' => (float)$row->total_ot,
                'double_ot' => (float)$row->total_double_ot
            ];
        }

        // Calculate totals for each month
        $monthlyTotals = [];
        foreach ($monthsInRange as $month) {
            $monthlyTotals[$month] = [
                'normal_ot' => 0,
                'double_ot' => 0
            ];
        }

        // Build HTML table
        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="ot_report_dt" style="width:100%">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 100px;">EMP ID</th>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 250px;">EMPLOYEE</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthFormatted = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= "<th colspan='2' class='align-middle'>$monthFormatted</th>";
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= "<th colspan='2' class='align-middle'>$monthFormatted</th>";
            }
        }
        $table .= '</tr>';
        $table .= '<tr>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<th class="align-middle" style="width: 100px;">NORMAL OT</th>';
            $table .= '<th class="align-middle" style="width: 100px;">DOUBLE OT</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            foreach ($monthsInRange as $month) {
                $table .= '<th class="align-middle" style="width: 100px;">NORMAL OT</th>';
                $table .= '<th class="align-middle" style="width: 100px;">DOUBLE OT</th>';
            }
        }
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        // Display employee data
        foreach ($employeeOt as $employee) {
            $table .= '<tr>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_id']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_name']) . '</td>';

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $yearMonth = date('Y-m', strtotime($selectedmonth));
                $normal_ot = isset($employee['ot_data'][$yearMonth]['normal_ot']) ? $employee['ot_data'][$yearMonth]['normal_ot'] : 0;
                $double_ot = isset($employee['ot_data'][$yearMonth]['double_ot']) ? $employee['ot_data'][$yearMonth]['double_ot'] : 0;

                // Add to monthly totals
                $monthlyTotals[$yearMonth]['normal_ot'] += $normal_ot;
                $monthlyTotals[$yearMonth]['double_ot'] += $double_ot;

                $table .= '<td class="align-middle">' . number_format($normal_ot, 2) . '</td>';
                $table .= '<td class="align-middle">' . number_format($double_ot, 2) . '</td>';
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $yearMonth = date('Y-m', strtotime($month));
                    $normal_ot = isset($employee['ot_data'][$yearMonth]['normal_ot']) ? $employee['ot_data'][$yearMonth]['normal_ot'] : 0;
                    $double_ot = isset($employee['ot_data'][$yearMonth]['double_ot']) ? $employee['ot_data'][$yearMonth]['double_ot'] : 0;

                    // Add to monthly totals
                    $monthlyTotals[$month]['normal_ot'] += $normal_ot;
                    $monthlyTotals[$month]['double_ot'] += $double_ot;

                    $table .= '<td class="align-middle">' . number_format($normal_ot, 2) . '</td>';
                    $table .= '<td class="align-middle">' . number_format($double_ot, 2) . '</td>';
                }
            }
            
            $table .= '</tr>';
        }

        // If no data found, show message
        if (empty($employeeOt)) {
            $colspan = 4 + (count($monthsInRange) * 2);
            $table .= "<tr><td colspan='{$colspan}' class='text-center'>No OT records found</td></tr>";
        } else {
            // Add totals row
            $table .= "<tr style='background-color: #f2f2f2; font-weight: bold;'>";
            $table .= "<td colspan='2' class='text-right'><strong>TOTAL</strong></td>";
            
            if ($reporttype == '1' && !empty($selectedmonth)) {
                $yearMonth = date('Y-m', strtotime($selectedmonth));
                $table .= "<td class='text-center'><strong>" . number_format($monthlyTotals[$yearMonth]['normal_ot'], 2) . "</strong></td>";
                $table .= "<td class='text-center'><strong>" . number_format($monthlyTotals[$yearMonth]['double_ot'], 2) . "</strong></td>";
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $table .= "<td class='text-center'><strong>" . number_format($monthlyTotals[$month]['normal_ot'], 2) . "</strong></td>";
                    $table .= "<td class='text-center'><strong>" . number_format($monthlyTotals[$month]['double_ot'], 2) . "</strong></td>";
                }
            }
            $table .= "</tr>";
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
        $title .= '<h5>OT Report - ' . $deptName . '</h5>';
        
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $title .= '<p class="mb-0"><strong>Period:</strong> ' . $monthName . '</p>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $title .= '<p class="mb-0"><strong>Period:</strong> ' . date('d-m-Y', strtotime($from_date)) . ' to ' . date('d-m-Y', strtotime($to_date)) . '</p>';
        }
        $title .= '</div>';

        return response()->json([
            'html' => $title . $table
        ]);
    }

     // Late report Generation
     public function latereport()
    {
        $permission = Auth::user()->can('department-wise-attendance-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.employee_late_report', compact('companies'));
    }

    public function generatelatereport(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
        $selectedmonth = $request->get('selectedmonth');
        $location = $request->get('location');

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
        $query = DB::table('employee_late_attendance_minites')
            ->join('employees', 'employee_late_attendance_minites.emp_id', '=', 'employees.emp_id')
            ->join('departments', 'employees.emp_department', '=', 'departments.id')
            ->where('employees.deleted', 0)
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

        // Apply date filters based on report type
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('employee_late_attendance_minites.attendance_date', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('employee_late_attendance_minites.attendance_date', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('employee_late_attendance_minites.attendance_date', [$from_date, $to_date]);
        }

        // Select employee details and late minutes sums with month/year breakdown
        $query->select(
            'employees.id as emp_primary_id',
            'employees.emp_id',
            'employees.emp_name_with_initial',
            'employees.calling_name',
            'departments.name as dept_name',
            'departments.id as dept_id',
            DB::raw('SUM(employee_late_attendance_minites.minites_count) as total_late_minutes'),
            DB::raw('YEAR(employee_late_attendance_minites.attendance_date) as year'),
            DB::raw('MONTH(employee_late_attendance_minites.attendance_date) as month')
        )
        ->groupBy(
            'employees.id',
            'employees.emp_id',
            'employees.emp_name_with_initial',
            'employees.calling_name',
            'departments.name',
            'departments.id',
            'year',
            'month'
        );

        $data = $query->get();

        // Get months in range for date range report
        $monthsInRange = [];
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthsInRange[] = date('Y-m', strtotime($selectedmonth));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
        }

        // Organize data by employee and month
        $employeeLate = [];
        foreach ($data as $row) {
            if (!isset($employeeLate[$row->emp_primary_id])) {
                $employeeLate[$row->emp_primary_id] = [
                    'emp_id' => $row->emp_id,
                    'emp_name' => $row->emp_name_with_initial,
                    'calling_name' => $row->calling_name,
                    'dept_name' => $row->dept_name,
                    'dept_id' => $row->dept_id,
                    'late_data' => []
                ];
            }
            
            $yearMonth = $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT);
            $employeeLate[$row->emp_primary_id]['late_data'][$yearMonth] = [
                'late_minutes' => (int)$row->total_late_minutes
            ];
        }

        // Calculate totals for each month
        $monthlyTotals = [];
        foreach ($monthsInRange as $month) {
            $monthlyTotals[$month] = [
                'late_minutes' => 0
            ];
        }

        // Build HTML table
        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="late_report_dt" style="width:100%">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 100px;">EMP ID</th>';
        $table .= '<th rowspan="2" class="align-middle" style="width: 250px;">EMPLOYEE</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthFormatted = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= "<th colspan='1' class='align-middle'>$monthFormatted</th>";
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= "<th colspan='1' class='align-middle'>$monthFormatted</th>";
            }
        }
        $table .= '</tr>';
        $table .= '<tr>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<th class="align-middle" style="width: 120px;">LATE MINUTES</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            foreach ($monthsInRange as $month) {
                $table .= '<th class="align-middle" style="width: 120px;">LATE MINUTES</th>';
            }
        }
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        // Display employee data
        foreach ($employeeLate as $employee) {
            $table .= '<tr>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_id']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_name']) . '</td>';

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $yearMonth = date('Y-m', strtotime($selectedmonth));
                $late_minutes = isset($employee['late_data'][$yearMonth]['late_minutes']) ? $employee['late_data'][$yearMonth]['late_minutes'] : 0;

                // Add to monthly totals
                $monthlyTotals[$yearMonth]['late_minutes'] += $late_minutes;

                // Convert minutes to hours and minutes for display
                $hours = floor($late_minutes / 60);
                $minutes = $late_minutes % 60;
                $display_time = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                
                $table .= '<td class="align-middle">' . $display_time . ' (' . $late_minutes . ' min)</td>';
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $yearMonth = date('Y-m', strtotime($month));
                    $late_minutes = isset($employee['late_data'][$yearMonth]['late_minutes']) ? $employee['late_data'][$yearMonth]['late_minutes'] : 0;

                    // Add to monthly totals
                    $monthlyTotals[$month]['late_minutes'] += $late_minutes;

                    // Convert minutes to hours and minutes for display
                    $hours = floor($late_minutes / 60);
                    $minutes = $late_minutes % 60;
                    
                    $table .= '<td class="align-middle">' . $late_minutes . 'min</td>';
                }
            }
            
            $table .= '</tr>';
        }

        // If no data found, show message
        if (empty($employeeLate)) {
            $colspan = 3 + count($monthsInRange);
            $table .= "<tr><td colspan='{$colspan}' class='text-center'>No late attendance records found</td></tr>";
        } else {
            // Add totals row
            $table .= "<tr style='background-color: #f2f2f2; font-weight: bold;'>";
            $table .= "<td colspan='2' class='text-right'><strong>TOTAL</strong></td>";
            
            if ($reporttype == '1' && !empty($selectedmonth)) {
                $yearMonth = date('Y-m', strtotime($selectedmonth));
                $total_minutes = $monthlyTotals[$yearMonth]['late_minutes'];
                $hours = floor($total_minutes / 60);
                $minutes = $total_minutes % 60;

                $table .= "<td class='text-center'><strong>". $total_minutes . " min</strong></td>";
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $total_minutes = $monthlyTotals[$month]['late_minutes'];
                    $hours = floor($total_minutes / 60);
                    $minutes = $total_minutes % 60;
                   
                    $table .= "<td class='text-center'><strong>"  . $total_minutes . " min</strong></td>";
                }
            }
            $table .= "</tr>";
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
        $title .= '<h5>Late Attendance Report - ' . $deptName . '</h5>';
        
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $title .= '<p class="mb-0"><strong>Period:</strong> ' . $monthName . '</p>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $title .= '<p class="mb-0"><strong>Period:</strong> ' . date('d-m-Y', strtotime($from_date)) . ' to ' . date('d-m-Y', strtotime($to_date)) . '</p>';
        }
        $title .= '</div>';

       // At the end of your generatelatereport function, modify the return statement:
        return response()->json([
            'html' => $title . $table,
            'data' => [
                'employees' => $employeeLate,
                'monthsInRange' => $monthsInRange,
                'reporttype' => $reporttype,
                'selectedmonth' => $selectedmonth,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'deptName' => $deptName
            ]
        ]);
    }

    private function getMonthsInRange($from_date, $to_date)
    {
        $start = new DateTime($from_date);
        $end = new DateTime($to_date);
        $end->modify('first day of next month');

        $months = [];
        while ($start < $end) {
            $months[] = $start->format('Y-m');
            $start->modify('first day of next month');
        }

        return $months;
    }


}
