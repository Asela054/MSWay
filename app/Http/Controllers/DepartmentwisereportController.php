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
        $companies = DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.ot_report', compact('companies'));
    }

    public function leavereport()
    {
        $permission = Auth::user()->can('department-wise-leave-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.leave_report', compact('companies'));
    }

    public function attendancereport()
    {
        $permission = Auth::user()->can('department-wise-attendance-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('departmetwise_reports.attendance_report', compact('companies'));
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
                    'dept_id' => $row->dept_id,
                    'dept_name' => $row->dept_name,
                    'ot_data' => [],
                ];
            }

            $groupedData[$row->dept_name]['ot_data'][$monthKey] = [
                'total_ot' => $row->total_ot,
                'total_double_ot' => $row->total_double_ot,
            ];
        }
        $table = '<table id="ot_report_dt" class="table table-striped table-bordered table-sm small text-center">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th rowspan="3" class="align-middle">Department</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= '<th colspan="2" class="align-middle">Total OT Hours for ' . $monthName . '</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
            $monthCount = count($monthsInRange);
            $table .= '<th colspan="' . ($monthCount * 2) . '" class="align-middle">Total OT Hours</th>';
        }

        $table .= '<th rowspan="3" class="align-middle">Action</th>';
        $table .= '</tr>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<tr>';
            $table .= '<th colspan="2" class="align-middle">' . $monthName . '</th>';
            $table .= '</tr>';
            $table .= '<tr>';
            $table .= '<th>Normal OT</th>';
            $table .= '<th>Double OT</th>';
            $table .= '</tr>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= '<th colspan="2" class="align-middle">' . $monthFormatted . '</th>';
            }
            $table .= '</tr>';
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $table .= '<th>Normal OT</th>';
                $table .= '<th>Double OT</th>';
            }
            $table .= '</tr>';
        }

        $table .= '</thead>';
        $table .= '<tbody>';

        foreach ($groupedData as $dept_name => $dept_data) {
            // Corrected the reference to department ID here
            $dept_id = $dept_data['dept_id'];

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
            $table .= "<td><button id='{$dept_id}' class='btn btn-primary btn-sm view_more'><i class='fas fa-eye'></i></button></td>";
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
                'employees.calling_name',
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

        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="empotview" style="width:100%;">'; // Add text-center class for centered text
        $table .= '<thead>';

        // First header row
        $table .= '<tr>';
        $table .= '<th rowspan="3" class="align-middle">Emp ID</th>';
        $table .= '<th rowspan="3" class="align-middle">Employee</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= '<th colspan="2" class="align-middle">Total OT Hours for ' . $monthName . '</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
            $monthCount = count($monthsInRange);
            $table .= '<th colspan="' . ($monthCount * 2) . '" class="align-middle">Total OT Hours</th>';
        }

        $table .= '</tr>';

        // Second header row for each month
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<tr>';
            $table .= '<th colspan="2" class="align-middle">' . $monthName . '</th>';
            $table .= '</tr>';
            $table .= '<tr>';
            $table .= '<th>Normal OT</th>';
            $table .= '<th>Double OT</th>';
            $table .= '</tr>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= '<th colspan="2" class="align-middle">' . $monthFormatted . '</th>';
            }
            $table .= '</tr>';
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $table .= '<th>Normal OT</th>';
                $table .= '<th>Double OT</th>';
            }
            $table .= '</tr>';
        }

        $table .= '</thead>';
        $table .= '<tbody>';

        // Populate the table body with employee data
        foreach ($data as $row) {
            $table .= '<tr>';
            $table .= '<td class="align-middle">' . htmlspecialchars($row->empid) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($row->emp_name) . ' - ' . htmlspecialchars($row->calling_name) . '</td>';

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $table .= '<td class="align-middle">' . htmlspecialchars($row->total_ot) . '</td>';
                $table .= '<td class="align-middle">' . htmlspecialchars($row->total_double_ot) . '</td>';
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $total_ot = $row->total_ot;
                    $total_double_ot = $row->total_double_ot;

                    $table .= '<td class="align-middle">' . htmlspecialchars($total_ot) . '</td>';
                    $table .= '<td class="align-middle">' . htmlspecialchars($total_double_ot) . '</td>';
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
                DB::raw('SUM(CASE WHEN leaves.leave_type = 1 THEN leaves.no_of_days ELSE 0 END) as total_annual_leaves'),
                DB::raw('SUM(CASE WHEN leaves.leave_type = 2 THEN leaves.no_of_days ELSE 0 END) as total_casual_leaves'),
                DB::raw('SUM(CASE WHEN leaves.leave_type = 3 THEN leaves.no_of_days ELSE 0 END) as total_no_pay_leaves'),
                DB::raw('SUM(CASE WHEN leaves.leave_type = 4 THEN leaves.no_of_days ELSE 0 END) as total_medical_leaves')
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
                    'dept_id' => $row->dept_id,
                    'dept_name' => $row->dept_name,
                    'leave_data' => [],
                ];
            }

            $groupedData[$row->dept_name]['leave_data'][$monthKey] = [
                'total_annual_leaves' => (int) $row->total_annual_leaves,
                'total_casual_leaves' => (int) $row->total_casual_leaves,
                'total_no_pay_leaves' => (int) $row->total_no_pay_leaves,
                'total_medical_leaves' => (int) $row->total_medical_leaves,
            ];
        }



        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="leave_report">';
        $table .= '<thead><tr>';
        $table .= '<th rowspan="3" class="align-middle">Department</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= '<th colspan="4" class="align-middle">Total Leaves for ' . $monthName . '</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
            $monthCount = count($monthsInRange);
            $table .= '<th colspan="' . ($monthCount * 4) . '" class="align-middle">Total Leaves</th>';
        }
        $table .= '<th rowspan="3" class="align-middle">Action</th>';
        $table .= '</tr>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<tr>';
            $table .= '<th colspan="4" class="align-middle">' . $monthName . '</th>';
            $table .= '</tr>';
            $table .= '<tr>';
            $table .= '<th>Annual Leaves</th>';
            $table .= '<th>Casual Leaves</th>';
            $table .= '<th>No Pay Leaves</th>';
            $table .= '<th>Medical Leaves</th>';
            $table .= '</tr>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= '<th colspan="4" class="align-middle">' . $monthFormatted . '</th>';
            }
            $table .= '</tr>';
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $table .= '<th>Annual Leaves</th>';
                $table .= '<th>Casual Leaves</th>';
                $table .= '<th>No Pay Leaves</th>';
                $table .= '<th>Medical Leaves</th>';
            }
            $table .= '</tr>';
        }
        $table .= '</thead>';
        $table .= '<tbody>';

        foreach ($groupedData as $dept_name => $dept_data) {
            $dept_id = $dept_data['dept_id'];
            $table .= "<tr>";
            $table .= "<td class='align-middle'>{$dept_name}</td>";

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $monthKey = date('Y-m', strtotime($selectedmonth));
                $total_annual_leaves = $dept_data['leave_data'][$monthKey]['total_annual_leaves'] ?? 0;
                $total_casual_leaves = $dept_data['leave_data'][$monthKey]['total_casual_leaves'] ?? 0;
                $total_no_pay_leaves = $dept_data['leave_data'][$monthKey]['total_no_pay_leaves'] ?? 0;
                $total_medical_leaves = $dept_data['leave_data'][$monthKey]['total_medical_leaves'] ?? 0;

                $table .= "<td class='align-middle'>$total_annual_leaves</td>";
                $table .= "<td class='align-middle'>$total_casual_leaves</td>";
                $table .= "<td class='align-middle'>$total_no_pay_leaves</td>";
                $table .= "<td class='align-middle'>$total_medical_leaves</td>";
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $total_annual_leaves = $dept_data['leave_data'][$month]['total_annual_leaves'] ?? 0;
                    $total_casual_leaves = $dept_data['leave_data'][$month]['total_casual_leaves'] ?? 0;
                    $total_no_pay_leaves = $dept_data['leave_data'][$month]['total_no_pay_leaves'] ?? 0;
                    $total_medical_leaves = $dept_data['leave_data'][$month]['total_medical_leaves'] ?? 0;

                    $table .= "<td class='align-middle'>$total_annual_leaves</td>";
                    $table .= "<td class='align-middle'>$total_casual_leaves</td>";
                    $table .= "<td class='align-middle'>$total_no_pay_leaves</td>";
                    $table .= "<td class='align-middle'>$total_medical_leaves</td>";
                }
            }

            $table .= "<td class='align-middle'><button id='{$dept_id}' class='btn btn-primary btn-sm view_more'><i class='fas fa-eye'></i></button></td>";
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

        $query = DB::table('leaves')
            ->join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->select(
                'departments.id as dept_id',
                'departments.name as dept_name',
                'employees.id as empid',
                'employees.calling_name',
                'employees.emp_name_with_initial as emp_name',
                DB::raw('SUM(CASE WHEN leaves.leave_type = 1 THEN leaves.no_of_days ELSE 0 END) as annual_leaves'),
                DB::raw('SUM(CASE WHEN leaves.leave_type = 2 THEN leaves.no_of_days ELSE 0 END) as casual_leaves'),
                DB::raw('SUM(CASE WHEN leaves.leave_type = 3 THEN leaves.no_of_days ELSE 0 END) as no_pay_leaves'),
                DB::raw('SUM(CASE WHEN leaves.leave_type = 4 THEN leaves.no_of_days ELSE 0 END) as medical_leaves'),
                DB::raw('YEAR(leaves.leave_from) as year'),
                DB::raw('MONTH(leaves.leave_from) as month')
            )
            ->where('leaves.status', 'Approved');

        if ($department != '' && $department != 'All') {
            $query->where('employees.emp_department', $department);
        }

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('leaves.leave_from', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('leaves.leave_from', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('leaves.leave_from', [$from_date, $to_date]);
        }

        $query->groupBy('departments.id', 'departments.name', 'employees.id', 'employees.emp_name_with_initial', 'year', 'month');
        $data = $query->get();

        $employeeLeaves = [];
        foreach ($data as $row) {
            if (!isset($employeeLeaves[$row->empid])) {
                $employeeLeaves[$row->empid] = [
                    'emp_name' => $row->emp_name,
                    'calling_name' => $row->calling_name,
                    'dept_name' => $row->dept_name,
                    'leaves' => []
                ];
            }
            $yearMonth = $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT);
            $employeeLeaves[$row->empid]['leaves'][$yearMonth] = [
                'annual' => intval($row->annual_leaves),
                'casual' => intval($row->casual_leaves),
                'no_pay' => intval($row->no_pay_leaves),
                'medical' => intval($row->medical_leaves),
            ];
        }

        $monthsInRange = [];
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthsInRange[] = date('Y-m', strtotime($selectedmonth));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
        }

        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="leave_reportemployee" style="width:100%">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th rowspan="2" class="align-middle">Department</th>';
        $table .= '<th rowspan="2" class="align-middle">Emp ID</th>';
        $table .= '<th rowspan="2" class="align-middle">Employee</th>';

        foreach ($monthsInRange as $month) {
            $monthFormatted = date('F Y', strtotime($month . '-01'));
            $table .= "<th colspan='4' class='align-middle'>$monthFormatted</th>";
        }
        $table .= '</tr>';
        $table .= '<tr>';

        foreach ($monthsInRange as $month) {
            $table .= '<th class="align-middle">Annual</th>';
            $table .= '<th class="align-middle">Casual</th>';
            $table .= '<th class="align-middle">No Pay</th>';
            $table .= '<th class="align-middle">Medical</th>';
        }
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        foreach ($employeeLeaves as $empid => $employee) {
            $table .= '<tr>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['dept_name']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($empid) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($employee['emp_name']) . ' - ' . htmlspecialchars($employee['calling_name']) .'</td>';
            foreach ($monthsInRange as $month) {
                $yearMonth = date('Y-m', strtotime($month));
                $annual = isset($employee['leaves'][$yearMonth]['annual']) ? $employee['leaves'][$yearMonth]['annual'] : 0;
                $casual = isset($employee['leaves'][$yearMonth]['casual']) ? $employee['leaves'][$yearMonth]['casual'] : 0;
                $no_pay = isset($employee['leaves'][$yearMonth]['no_pay']) ? $employee['leaves'][$yearMonth]['no_pay'] : 0;
                $medical = isset($employee['leaves'][$yearMonth]['medical']) ? $employee['leaves'][$yearMonth]['medical'] : 0;

                $table .= '<td class="align-middle">' . htmlspecialchars($annual) . '</td>';
                $table .= '<td class="align-middle">' . htmlspecialchars($casual) . '</td>';
                $table .= '<td class="align-middle">' . htmlspecialchars($no_pay) . '</td>';
                $table .= '<td class="align-middle">' . htmlspecialchars($medical) . '</td>';
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



    // Attendance Report section
    public function generateattendancereport(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
        $selectedmonth = $request->get('selectedmonth');

        $query = DB::table('attendances')
            ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->select(
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.emp_department',
                'departments.name as dept_name',
                DB::raw('YEAR(attendances.date) as year'),
                DB::raw('MONTH(attendances.date) as month'),
                DB::raw('MIN(attendances.timestamp) as first_checkin'),
                DB::raw('MAX(attendances.timestamp) as lasttimestamp')
            );

        if ($department != 'All') {
            $query->where('employees.emp_department', '=', $department);
        }

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('attendances.date', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('attendances.date', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('attendances.date', [$from_date, $to_date]);
        }

        $query->groupBy('attendances.date', 'attendances.emp_id', DB::raw('YEAR(attendances.date)'), DB::raw('MONTH(attendances.date)'));

        $data = $query->get();

        $groupedData = [];
        foreach ($data as $row) {
            $monthKey = sprintf('%04d-%02d', $row->year, $row->month);
            if (!isset($groupedData[$row->dept_name])) {
                $groupedData[$row->dept_name] = [
                    'dept_id' => $row->emp_department,
                    'dept_name' => $row->dept_name,
                    'attendance_count' => [],
                ];
            }
            if (!isset($groupedData[$row->dept_name]['attendance_count'][$monthKey])) {
                $groupedData[$row->dept_name]['attendance_count'][$monthKey] = 0;
            }

            $groupedData[$row->dept_name]['attendance_count'][$monthKey] += 1;
        }

        $table = '<table id="ot_report_dt" class="table table-striped table-bordered table-sm small text-center">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th rowspan="3" class="align-middle">Department</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= '<th class="align-middle">Total Attendance for ' . $monthName . '</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
            $monthCount = count($monthsInRange);
            $table .= '<th colspan="' . ($monthCount * 1) . '" class="align-middle">Total Attendance</th>';
        }

        $table .= '<th rowspan="3" class="align-middle">Action</th>';
        $table .= '</tr>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<tr>';
            $table .= '<th class="align-middle">' . $monthName . '</th>';
            $table .= '</tr>';
            $table .= '<tr>';
            $table .= '<th>Attendance</th>';
            $table .= '</tr>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= '<th class="align-middle">' . $monthFormatted . '</th>';
            }
            $table .= '</tr>';
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $table .= '<th>Attendance</th>';
            }
            $table .= '</tr>';
        }

        $table .= '</thead>';
        $table .= '<tbody>';

        foreach ($groupedData as $dept_name => $dept_data) {
            // Corrected the reference to department ID here
            $dept_id = $dept_data['dept_id'];

            $table .= "<tr>";
            $table .= "<td>{$dept_name}</td>";

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $monthKey = date('Y-m', strtotime($selectedmonth));
                $attendance_count = $dept_data['attendance_count'][$monthKey] ?? 0;

                $table .= "<td>$attendance_count</td>";
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $attendance_count = $dept_data['attendance_count'][$month] ?? 0;

                    $table .= "<td>$attendance_count</td>";
                }
            }
            $table .= "<td><button id='{$dept_id}' class='btn btn-primary btn-sm view_more'><i class='fas fa-eye'></i></button></td>";
            $table .= "</tr>";
        }

        $table .= '</tbody>';
        $table .= '</table>';



        return response()->json([
            'table' => $table
        ]);
    }

    public function gettotalattendanceemployee(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $reporttype = $request->get('reporttype');
        $selectedmonth = $request->get('selectedmonth');

        $query = DB::table('attendances')
            ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->select(
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'employees.emp_department',
                'departments.name as dept_name',
                DB::raw('YEAR(attendances.date) as year'),
                DB::raw('MONTH(attendances.date) as month'),
                DB::raw('MIN(attendances.timestamp) as first_checkin'),
                DB::raw('MAX(attendances.timestamp) as lasttimestamp')
            );

        if ($department != 'All') {
            $query->where('employees.emp_department', '=', $department);
        }

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $query->whereYear('attendances.date', '=', date('Y', strtotime($selectedmonth)))
                ->whereMonth('attendances.date', '=', date('m', strtotime($selectedmonth)));
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $query->whereBetween('attendances.date', [$from_date, $to_date]);
        }

        $query->groupBy('attendances.date', 'attendances.emp_id');

        $data = $query->get();


        $groupedData = [];
        foreach ($data as $row) {
            $monthKey = sprintf('%04d-%02d', $row->year, $row->month);
            if (!isset($groupedData[$row->emp_id])) {
                $groupedData[$row->emp_id] = [
                    'emp_id' => $row->emp_id,
                    'emp_name_with_initial' => $row->emp_name_with_initial,
                    'calling_name' => $row->calling_name,
                    'attendance_count' => [],
                ];
            }
            if (!isset($groupedData[$row->emp_id]['attendance_count'][$monthKey])) {
                $groupedData[$row->emp_id]['attendance_count'][$monthKey] = 0;
            }

            $groupedData[$row->emp_id]['attendance_count'][$monthKey] += 1;
        }

        $table = '<table class="table table-striped table-bordered table-sm small text-center" id="empotview" style="width:100%;">'; // Add text-center class for centered text
        $table .= '<thead>';

        // First header row
        $table .= '<tr>';
        $table .= '<th rowspan="3" class="align-middle">Emp ID</th>';
        $table .= '<th rowspan="3" class="align-middle">Employee</th>';

        if ($reporttype == '1' && !empty($selectedmonth)) {
            $monthName = date('F Y', strtotime($selectedmonth . '-01'));
            $table .= '<th class="align-middle">Total Attendance for ' . $monthName . '</th>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $monthsInRange = $this->getMonthsInRange($from_date, $to_date);
            $monthCount = count($monthsInRange);
            $table .= '<th colspan="' . ($monthCount * 1) . '" class="align-middle">Total Attendance</th>';
        }

        $table .= '</tr>';

        // Second header row for each month
        if ($reporttype == '1' && !empty($selectedmonth)) {
            $table .= '<tr>';
            $table .= '<th class="align-middle">' . $monthName . '</th>';
            $table .= '</tr>';
            $table .= '<tr>';
            $table .= '<th>Attendance</th>';
            $table .= '</tr>';
        } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $monthFormatted = date('F Y', strtotime($month . '-01'));
                $table .= '<th class="align-middle">' . $monthFormatted . '</th>';
            }
            $table .= '</tr>';
            $table .= '<tr>';
            foreach ($monthsInRange as $month) {
                $table .= '<th>Attendance</th>';
            }
            $table .= '</tr>';
        }

        $table .= '</thead>';
        $table .= '<tbody>';

        // Populate the table body with employee data
        foreach ($groupedData as $emp_id => $emp_data) {

            $table .= '<tr>';
            $table .= '<td class="align-middle">' . htmlspecialchars($emp_data['emp_id']) . '</td>';
            $table .= '<td class="align-middle">' . htmlspecialchars($emp_data['emp_name_with_initial']) . ' - ' . htmlspecialchars($emp_data['calling_name']) .'</td>';

            if ($reporttype == '1' && !empty($selectedmonth)) {
                $monthKey = date('Y-m', strtotime($selectedmonth));
                $attendance_count = $emp_data['attendance_count'][$monthKey] ?? 0;
                $table .= "<td>$attendance_count</td>";
            } elseif ($reporttype == '2' && !empty($from_date) && !empty($to_date)) {
                foreach ($monthsInRange as $month) {
                    $attendance_count = $emp_data['attendance_count'][$month] ?? 0;
                    $table .= "<td>$attendance_count</td>";
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
}
