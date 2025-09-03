<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use DB;

class DepartmentwisereportController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('department-wise-ot-report');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.ot_report',compact('companies'));
    }

    public function leavereport()
    {
        $permission = Auth::user()->can('department-wise-leave-report');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.leave_report',compact('companies'));
    }

    public function attendancereport()
    {
        $permission = Auth::user()->can('department-wise-attendance-report');
        if (!$permission) {
            abort(403);
        }
        $companies=DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.attendance_report',compact('companies'));
    }

    // OT Report section
    public function generateotreport(Request $request)
    {
            $department = $request->get('department');
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');
            $reporttype = $request->get('reporttype');
            $selectedmonth = $request->get('selectedmonth');

            $query = DB::table('ot_approved')
                ->join('employees', 'ot_approved.emp_id', '=', 'employees.emp_id')
                ->join('departments', 'employees.emp_department', '=', 'departments.id')
                ->select(
                    'departments.id as dept_id',
                    'departments.name as dept_name',
                    DB::raw('YEAR(ot_approved.date) as year'),
                    DB::raw('MONTH(ot_approved.date) as month'),
                    DB::raw('SUM(ot_approved.hours) as total_ot'),
                    DB::raw('SUM(ot_approved.double_hours) as total_double_ot')
                );
            
            if ($department != 'All') {
                $query->where('employees.emp_department', '=', $department);
            }

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $query->whereYear('ot_approved.date', '=', date('Y', strtotime($selectedmonth)))
                    ->whereMonth('ot_approved.date', '=', date('m', strtotime($selectedmonth)));
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                $query->whereBetween('ot_approved.date', [$from_date, $to_date]);
            }
            $query->groupBy('employees.emp_department', DB::raw('YEAR(ot_approved.date)'), DB::raw('MONTH(ot_approved.date)'));

            $data = $query->get();
            $groupedData = [];
            foreach ($data as $row) {
                $monthKey = sprintf('%04d-%02d', $row->year, $row->month);

                if (!isset($groupedData[$row->dept_name])) {
                    $groupedData[$row->dept_name] = [
                        'dept_name' => $row->dept_name,
                        'ot_data' => [],
                    ];
                }

                $groupedData[$row->dept_name]['ot_data'][$monthKey] = [
                    'total_ot' => $row->total_ot,
                    'total_double_ot' => $row->total_double_ot,
                ];
            }

            $table = '<table id="ot_report_dt" class="table table-striped table-bordered table-sm small">';
            $table .= '<thead><tr>';
            $table .= '<th>Department</th>';

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $monthName = date('F Y', strtotime($selectedmonth . '-01'));
                $table .= "<th>Total OT Hours for $monthName</th>";
                $table .= "<th>Double OT Hours for $monthName</th>";
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
                foreach ($monthsInRange as $month) {
                    $monthFormatted = date('F Y', strtotime($month . '-01'));
                    $table .= "<th>Total OT Hours for $monthFormatted</th>";
                    $table .= "<th>Double OT Hours for $monthFormatted</th>";
                }
            }

            $table .= '<th>Action</th>';
            $table .= '</tr></thead>';
            $table .= '<tbody>';
            
            foreach ($groupedData as $dept_name => $dept_data) {
                $table .= "<tr>";
                $table .= "<td>{$dept_name}</td>";

                if ($reporttype == '1' && !empty($selectedmonth)) {
                    $monthKey = date('Y-m', strtotime($selectedmonth));
                    $total_ot = $dept_data['ot_data'][$monthKey]['total_ot'] ?? 0;
                    $total_double_ot = $dept_data['ot_data'][$monthKey]['total_double_ot'] ?? 0;

                    $table .= "<td>$total_ot</td>";
                    $table .= "<td>$total_double_ot</td>";
                } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                    foreach ($monthsInRange as $month) {
                        $total_ot = $dept_data['ot_data'][$month]['total_ot'] ?? 0;
                        $total_double_ot = $dept_data['ot_data'][$month]['total_double_ot'] ?? 0;

                        $table .= "<td>$total_ot</td>";
                        $table .= "<td>$total_double_ot</td>";
                    }
                }

                $table .= "<td><button id='{$department}' class='btn btn-primary btn-sm view_more'><i class='fas fa-eye'></i></button></td>";
                $table .= "</tr>";
            }

            $table .= '</tbody>';
            $table .= '</table>';

            return response()->json([
                'table' => $table
            ]);
    }
   
    public function gettotlaotemployee(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
        $selectedmonth = $request->get('selectedmonth');

        $query = DB::table('ot_approved')
            ->join('employees', 'ot_approved.emp_id', '=', 'employees.emp_id')
            ->select(
                'employees.id as empid',
                'employees.emp_name_with_initial as emp_name',
                DB::raw('SUM(ot_approved.hours) as total_ot'),
                DB::raw('SUM(ot_approved.double_hours) as total_double_ot')
            );

        if ($department != 'All') {
            $query->where('employees.emp_department', '=', $department);
        }

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('ot_approved.date', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('ot_approved.date', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('ot_approved.date', [$from_date, $to_date]);
        }

        $query->groupBy('employees.id');
        $data = $query->get();

        $table = '<table class="table table-striped table-bordered table-sm small" id="empotview" style="width:100%">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th>Emp ID</th>';
        $table .= '<th>Employee</th>';
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= "<th>Total OT Hours for $monthName</th>";
            $table .= "<th>Double OT Hours for $monthName</th>";
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= "<th>Total OT Hours for $monthFormatted</th>";
                $table .= "<th>Double OT Hours for $monthFormatted</th>";
            }
        }

        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        foreach ($data as $row) {
            $table .= '<tr>';
            $table .= '<td>' . htmlspecialchars($row->empid) . '</td>';
            $table .= '<td>' . htmlspecialchars($row->emp_name) . '</td>';

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $table .= '<td>' . htmlspecialchars($row->total_ot) . '</td>';
                $table .= '<td>' . htmlspecialchars($row->total_double_ot) . '</td>';
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
                foreach ($monthsInRange as $month) {
                    $monthKey = date('Y-m', strtotime($month));
                    $total_ot = $row->total_ot; 
                    $total_double_ot = $row->total_double_ot;

                    $table .= '<td>' . htmlspecialchars($total_ot) . '</td>';
                    $table .= '<td>' . htmlspecialchars($total_double_ot) . '</td>';
                }
            }

            $table .= '</tr>';
        }

        $table .= '</tbody>';
        $table .= '</table>';

        return response()->json([
            'success' => true,
            'table' => $table
        ]);
    }
   
// Leave Report Section

    public function generateleavereport(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
        $selectedmonth = $request->get('selectedmonth');

        $query = DB::table('leaves')
        ->join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
        ->join('departments', 'employees.emp_department', '=', 'departments.id')
        ->select(
            'departments.id as dept_id',
            'departments.name as dept_name',
            DB::raw('YEAR(leaves.leave_from) as year'),
            DB::raw('MONTH(leaves.leave_from) as month'),
            DB::raw('COUNT(leaves.no_of_days) as total_leave_count')
        );
            $query->where('leaves.status', 'Approved');

            if ($department != 'All') {
                $query->where('employees.emp_department', '=', $department);
            }

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $query->whereYear('leaves.leave_from', '=', date('Y', strtotime($selectedmonth)))
                    ->whereMonth('leaves.leave_from', '=', date('m', strtotime($selectedmonth)));
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                $query->whereBetween('leaves.leave_from', [$from_date, $to_date]);
            }

            $query->groupBy('departments.id', 'departments.name', DB::raw('YEAR(leaves.leave_from)'), DB::raw('MONTH(leaves.leave_from)'));

            $data = $query->get();

            $groupedData = [];
            foreach ($data as $row) {
                $monthKey = sprintf('%04d-%02d', $row->year, $row->month);

                if (!isset($groupedData[$row->dept_name])) {
                    $groupedData[$row->dept_name] = [
                        'dept_name' => $row->dept_name,
                        'leave_data' => [],
                    ];
                }

                $groupedData[$row->dept_name]['leave_data'][$monthKey] = [
                    'total_leave_count' => $row->total_leave_count,
                ];
            }

                $table = '<table class="table table-striped table-bordered table-sm small" id="leave_report">';
                $table .= '<thead><tr>';
                $table .= '<th>Department</th>';

                if ($reporttype == '1' && !empty($selectedmonth)) {
                    $monthName = date('F Y', strtotime($selectedmonth . '-01'));
                    $table .= "<th>Total Leaves for $monthName</th>";
                } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                    $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
                    foreach ($monthsInRange as $month) {
                        $monthFormatted = date('F Y', strtotime($month . '-01'));
                        $table .= "<th>Total Leaves for $monthFormatted</th>";
                    }
                }
                $table .= '<th>Action</th>';
                $table .= '</tr></thead>';
                $table .= '<tbody>';

                foreach ($groupedData as $dept_name => $dept_data) {
                    $table .= "<tr>";
                    $table .= "<td>{$dept_name}</td>";

                    if ($reporttype == '1' && !empty($selectedmonth)) {
                        $monthKey = date('Y-m', strtotime($selectedmonth));
                        $total_leave_count = $dept_data['leave_data'][$monthKey]['total_leave_count'] ?? 0;
                        $table .= "<td>$total_leave_count</td>";
                    } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                        foreach ($monthsInRange as $month) {
                            $total_leave_count = $dept_data['leave_data'][$month]['total_leave_count'] ?? 0;
                            $table .= "<td>$total_leave_count</td>";
                        }
                    }

                    $table .= "<td><button id='{$department}' class='btn btn-primary btn-sm view_more'><i class='fas fa-eye'></i></button></td>";
                    $table .= "</tr>";
                }
             
                $table .= '</tbody>';
                $table .= '</table>';

                return response()->json(['table' => $table]);
    }

    public function gettotalleaveemployee(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
        $selectedmonth = $request->get('selectedmonth');
    
        // Initialize the query
        $query = DB::table('leaves')
            ->join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->select(
                'employees.id as empid',
                'employees.emp_name_with_initial as emp_name',
                DB::raw('COUNT(leaves.no_of_days) as total_leave_count'),
                DB::raw('YEAR(leaves.leave_from) as year'),
                DB::raw('MONTH(leaves.leave_from) as month')
            )
            ->where('leaves.status', 'Approved');
    
        // Apply filters based on request
        if ($department != '' && $department != 'All') {
            $query->where('employees.emp_department', $department);
        }
    
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('leaves.leave_from', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('leaves.leave_from', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('leaves.leave_from', [$from_date, $to_date]);
        }
    
        // Group by employee and leave date (year and month)
        $query->groupBy('employees.id', 'employees.emp_name_with_initial', 'year', 'month');
        $data = $query->get();
    
        // Prepare to collect leave data by employee and month
        $employeeLeaves = [];
        foreach ($data as $row) {
            // Create a key based on employee ID and store the leave data for each month
            if (!isset($employeeLeaves[$row->empid])) {
                $employeeLeaves[$row->empid] = [
                    'emp_name' => $row->emp_name,
                    'leaves' => []
                ];
            }
            $employeeLeaves[$row->empid]['leaves'][$row->year . '-' . $row->month] = $row->total_leave_count;
        }
    
        // Get the range of months for headers
        $monthsInRange = [];
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthsInRange[] = date('Y-m', strtotime($selectedmonth));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
        }
    
        // Prepare the table
        $table = '<table class="table table-striped table-bordered table-sm small" id="leave_reportemployee" style="width:100%">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th>Emp ID</th>';
        $table .= '<th>Employee</th>';
    
        // Add month headers
        foreach ($monthsInRange as $month) {
            $monthFormatted = date('F Y', strtotime($month . '-01'));
            $table .= "<th>Total Leaves for $monthFormatted</th>";
        }
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
    
        // Populate table rows with employee data
        foreach ($employeeLeaves as $empid => $employee) {
            $table .= '<tr>';
            $table .= '<td>' . htmlspecialchars($empid) . '</td>';
            $table .= '<td>' . htmlspecialchars($employee['emp_name']) . '</td>';
    
            // Add total leaves for each month in range, or 0 if no data
            foreach ($monthsInRange as $month) {
                $yearMonth = date('Y-m', strtotime($month)); // Correct format for matching
                $total_leave_count = isset($employee['leaves'][$yearMonth]) ? $employee['leaves'][$yearMonth] : 0;
                $table .= '<td>' . htmlspecialchars($total_leave_count) . '</td>';
            }
    
            $table .= '</tr>';
        }
    
        $table .= '</tbody>';
        $table .= '</table>';
    
        return response()->json([
            'success' => true,
            'table' => $table
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
