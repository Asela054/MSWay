<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
use DatePeriod;
use DateInterval;
use DateTime;
use PDF;

class EmployeetimesheetContrller extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('attendance-timesheet');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('Report.employee_attendance_report', compact('companies'));
    }

    public function generatereport(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $lastEmpId = $request->get('last_emp_id', 0); // Start from last loaded employee

        $limit = 100; // Load one employee at a time

        // Step 1: Fetch Employee Data (unchanged)
        $employees = DB::select("
            SELECT emp.id, emp.emp_id, emp.emp_etfno, emp.emp_fullname, emp.emp_gender, 
                dept.name AS departmentname,cam.name AS companyname, job.title AS jobtitlename, emp.emp_shift, 
                COALESCE(esd_shift.shift_name, st.shift_name) AS shiftname
            FROM employees emp
            LEFT JOIN departments dept ON emp.emp_department = dept.id
            LEFT JOIN companies cam ON emp.emp_company = cam.id
            LEFT JOIN job_titles job ON emp.emp_job_code = job.id
            LEFT JOIN shift_types st ON emp.emp_shift = st.id
            LEFT JOIN employeeshiftdetails esd 
                ON esd.emp_id = emp.id
            LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
            WHERE emp.deleted = 0
            AND emp.is_resigned = 0
            AND emp.emp_department = ? 
            AND emp.id > ?
            ORDER BY emp.id ASC 
            LIMIT ?",
            [$department, $lastEmpId, $limit]
        );

        if (empty($employees)) {
            return response()->json([
                'data' => [],
                'lastEmpId' => null
            ]);
        }

        // Step 2: Generate date range in PHP (alternative to recursive CTE)
        $startDate = new DateTime($from_date);
        $endDate = new DateTime($to_date);
        $dateRange = [];
        
        while ($startDate <= $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }

        $employeeData = [];
        foreach ($employees as $employee) {
            if(!empty($employee->emp_shift)){
                // Initialize attendance records array
                $attendanceRecords = [];
                
                // Process each date in the range
                foreach ($dateRange as $date) {
                    $record = DB::select("
                        SELECT 
                            DATE_FORMAT(?, '%Y-%m-%d') AS in_date,
                            DATE_FORMAT(?, '%Y-%m-%d') AS out_date,
                            COALESCE(h.holiday_name, 
                                CASE WHEN WEEKDAY(?) IN (5,6) THEN DAYNAME(?) 
                                ELSE 'Weekday' END) AS day_type,
                            COALESCE(esd_shift.shift_name, st.shift_name) AS shift,
                            DATE_FORMAT(MIN(att.timestamp), '%h:%i %p') AS in_time, 
                            DATE_FORMAT(MAX(att.timestamp), '%h:%i %p') AS out_time,
                            ROUND(COALESCE(la.minites_count, 0), 2) AS late_min, 
                            COALESCE(leave_data.leavename, '') AS leave_type, 
                            ROUND(COALESCE(leave_data.no_of_days, 0), 2) AS leave_days,
                            ROUND(COALESCE(ot.hours, 0) + COALESCE(ot.holiday_normal_hours, 0), 2) AS ot_hours,
                            ROUND(COALESCE(ot.double_hours, 0), 2) AS double_ot,
                            ROUND(COALESCE(ot.triple_hours, 0), 2) AS triple_ot
                        FROM (SELECT ? AS date) dr
                        LEFT JOIN attendances att ON att.emp_id = ? AND att.date = ?
                        LEFT JOIN shift_types st ON st.id = ?
                        LEFT JOIN employeeshiftdetails esd 
                            ON esd.emp_id = ? AND ? BETWEEN esd.date_from AND esd.date_to
                        LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
                        LEFT JOIN employee_late_attendance_minites la 
                            ON la.emp_id = ? AND la.attendance_date = ?
                        LEFT JOIN (
                            SELECT ot.emp_id, ot.date, ot.hours, ot.double_hours, ot.triple_hours, 
                                ot.holiday_normal_hours
                            FROM ot_approved ot
                        ) ot ON ot.emp_id = ? AND ot.date = ?
                        LEFT JOIN (
                            SELECT l.emp_id, lt.leave_type AS leavename, l.no_of_days, l.leave_from, l.leave_to
                            FROM leaves l
                            LEFT JOIN leave_types lt ON l.leave_type = lt.id
                            WHERE l.status = 'Approved'
                        ) leave_data ON leave_data.emp_id = ? AND ? BETWEEN leave_data.leave_from AND leave_data.leave_to
                        LEFT JOIN holidays h ON h.date = ?
                        GROUP BY dr.date
                    ", [
                        $date, $date, $date, $date, // For date formatting and weekday check
                        $date, // For the dr alias
                        $employee->emp_id, $date, // For attendance join
                        $employee->emp_shift, // For shift_types join
                        $employee->emp_id, $date, // For employeeshiftdetails join
                        $employee->emp_id, $date, // For late attendance join
                        $employee->emp_id, $date, // For OT join
                        $employee->emp_id, $date, // For leave join
                        $date, // For holiday join
                    ]);

                    // Add the record (we take the first result since we're querying one date at a time)
                    $attendanceRecords[] = $record[0] ?? [
                        'in_date' => $date,
                        'out_date' => $date,
                        'day_type' => '',
                        'shift' => '',
                        'in_time' => '',
                        'out_time' => '',
                        'late_min' => 0,
                        'leave_type' => '',
                        'leave_days' => 0,
                        'ot_hours' => 0,
                        'double_ot' => 0,
                        'triple_ot' => 0
                    ];
                }

                // Store Employee Data
                $employeeData[] = [
                    'id' => $employee->id,
                    'emp_id' => $employee->emp_id,
                    'emp_etfno' => $employee->emp_etfno,
                    'emp_fullname' => $employee->emp_fullname,
                    'jobtitlename' => $employee->jobtitlename,
                    'departmentname' => $employee->departmentname,
                    'companyname' => $employee->companyname,
                    'emp_gender' => $employee->emp_gender,
                    'shiftname' => $employee->shiftname,
                    'attendance' => $attendanceRecords
                ];

                // Update last loaded employee ID
                $lastEmpId = $employee->id;
            }
        }

        $pdfData[] = [
            'data' => $employeeData,
            'lastEmpId' => $lastEmpId
        ];

        echo json_encode($pdfData);
    }

}