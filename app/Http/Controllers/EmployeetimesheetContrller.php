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
        $lastEmpId = $request->get('last_emp_id', 0);

        $limit = 100;

        // Step 1: Fetch Employee Data
         $employees = DB::select("
            SELECT emp.id, emp.emp_id, emp.emp_etfno,emp.emp_company, emp.emp_fullname, emp.emp_gender, 
                dept.name AS departmentname, cam.name AS companyname, job.title AS jobtitlename, emp.emp_shift, 
                COALESCE(esd_shift.shift_name, st.shift_name) AS shiftname,
                jc.is_sat_ot_type_as_act,
                jc.is_sun_ot_type_as_act,
                jc.full_day_work_hours,
                st.onduty_time, st.offduty_time,
                st.saturday_onduty_time, st.saturday_offduty_time
            FROM employees emp
            LEFT JOIN departments dept ON emp.emp_department = dept.id
            LEFT JOIN companies cam ON emp.emp_company = cam.id
            LEFT JOIN job_titles job ON emp.emp_job_code = job.id
            LEFT JOIN job_categories jc ON emp.job_category_id = jc.id
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

        // Step 2: Generate date range in PHP
        $startDate = new DateTime($from_date);
        $endDate = new DateTime($to_date);
        $dateRange = [];
        
        while ($startDate <= $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }

        $employeeData = [];
        foreach ($employees as $employee) {

            // Check roster first, then employeeshiftdetails, then default shift
            $hasShift = DB::selectOne("
                SELECT 1 FROM employee_roster_details erd
                WHERE erd.emp_id = ? AND erd.work_date BETWEEN ? AND ?
                UNION
                SELECT 1 FROM employeeshiftdetails esd
                WHERE esd.emp_id = ? AND esd.date_from <= ? AND esd.until_time >= ?
                UNION
                SELECT 1 FROM employees emp
                WHERE emp.id = ? AND emp.emp_shift IS NOT NULL AND emp.emp_shift != ''
                LIMIT 1
            ", [
                $employee->emp_id, $from_date, $to_date,
                $employee->emp_id, $to_date, $from_date,
                $employee->id
            ]);

            if (!empty($hasShift)) {
                // Initialize attendance records array
                // Shift hours (get_work_days eke logic ma)
                $expectedHours = 8;
                $saturdayExpectedHours = 8;

                if (!empty($employee->onduty_time) && !empty($employee->offduty_time)) {
                    $expectedHours = Carbon::parse($employee->onduty_time)
                        ->diffInHours(Carbon::parse($employee->offduty_time));
                }
                if (!empty($employee->saturday_onduty_time) && !empty($employee->saturday_offduty_time)) {
                    $saturdayExpectedHours = Carbon::parse($employee->saturday_onduty_time)
                        ->diffInHours(Carbon::parse($employee->saturday_offduty_time));
                }

                $full_day_work_hours = !empty($employee->full_day_work_hours) ? $employee->full_day_work_hours : 8;

                $attendance_days = 0;
                $work_days = 0;

                $attendanceRecords = [];
                
                // Process each date in the range
                foreach ($dateRange as $date) {
                    $record = DB::select("
                        SELECT 
                            DATE_FORMAT(?, '%Y-%m-%d') AS in_date,
                            DATE_FORMAT(?, '%Y-%m-%d') AS out_date,
                            COALESCE(h.holiday_name, 
                                CASE WHEN WEEKDAY(?) IN (5,6) THEN DAYNAME(?) 
                                ELSE 'Work' END) AS day_type,
                            COALESCE(roster_shift.shift_name, esd_shift.shift_name, st.shift_name) AS shift,
                            erd.shift_id AS shift_id,
                            DATE_FORMAT(MIN(att.timestamp), '%h:%i %p') AS in_time, 
                            DATE_FORMAT(MAX(att.timestamp), '%h:%i %p') AS out_time,
                            MIN(att.timestamp) AS first_ts,
                            MAX(att.timestamp) AS last_ts,
                            h.work_level AS holiday_work_level,
                            SEC_TO_TIME(ROUND(COALESCE(la.minites_count, 0) * 60)) AS late_min,
                            COALESCE(leave_data.leavename, '') AS leave_type, 
                            ROUND(COALESCE(leave_data.no_of_days, 0), 2) AS leave_days,
                            ROUND(COALESCE(ot.hours, 0) + COALESCE(ot.holiday_normal_hours, 0), 2) AS ot_hours,
                            ROUND(COALESCE(ot.double_hours, 0), 2) AS double_ot,
                            ROUND(COALESCE(ot.triple_hours, 0), 2) AS triple_ot,
                            ot.duration_time,
                            att.type AS attendance_type
                        FROM (SELECT ? AS date) dr
                        LEFT JOIN employee_roster_details erd
                            ON erd.emp_id = ? AND erd.work_date = ?
                        LEFT JOIN shift_types roster_shift ON roster_shift.id = erd.shift_id
                        LEFT JOIN attendances att ON att.emp_id = ? AND att.date = ? AND att.deleted_at IS NULL
                        LEFT JOIN shift_types st ON st.id = ?
                        LEFT JOIN employeeshiftdetails esd 
                            ON esd.emp_id = ? AND ? BETWEEN esd.date_from AND esd.until_time
                        LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
                        LEFT JOIN employee_late_attendance_minites la 
                            ON la.emp_id = ? AND la.attendance_date = ?
                        LEFT JOIN (
                            SELECT ot.emp_id, ot.date, ot.hours, ot.double_hours, ot.triple_hours, 
                                ot.holiday_normal_hours, TIMEDIFF(ot.to, ot.from) AS duration_time
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
                        $date, $date, $date, $date,     // For date formatting and weekday check
                        $date,                           // For the dr alias
                        $employee->emp_id, $date,        // For roster join
                        $employee->emp_id, $date,        // For attendance join
                        $employee->emp_shift,             // For shift_types join
                        $employee->emp_id, $date,        // For employeeshiftdetails join
                        $employee->emp_id, $date,        // For late attendance join
                        $employee->emp_id, $date,        // For OT join
                        $employee->emp_id, $date,        // For leave join
                        $date,                           // For holiday join
                    ]);

                    // Add the record
                    $rec = isset($record[0]) ? $record[0] : null;

                    if ($rec) {
                        $attendanceRecords[] = $rec;
                    } else {

                        $attendanceRecords[] = $record[0] ?? [
                            'in_date' => $date,
                            'out_date' => $date,
                            'day_type' => '',
                            'shift' => '',
                            'shift_id' => null,
                            'in_time' => '',
                            'out_time' => '',
                            'first_ts' => null,
                            'last_ts' => null,
                            'holiday_work_level' => null,
                            'late_min' => 0,
                            'leave_type' => '',
                            'leave_days' => 0,
                            'ot_hours' => 0,
                            'double_ot' => 0,
                            'triple_ot' => 0,
                            'attendance_type' => '',
                        ];
                    }

                    // ---- Attendance days + Work days count ----
                    if ($rec && !empty($rec->first_ts)) {

                        // Attendance thiyena dawasak
                        $attendance_days++;

                        // work_level = 2 holiday nam work day ekakata ganne na
                        if ((string)$rec->holiday_work_level !== '2') {

                            $diff = round((strtotime($rec->last_ts) - strtotime($rec->first_ts)) / 3600, 1);

                            $isSaturday = Carbon::parse($rec->first_ts)->isSaturday();
                            $required_full_hours = $isSaturday ? $saturdayExpectedHours : $full_day_work_hours;

                            if ($diff >= $required_full_hours) {
                                $work_days += 1;
                            } else {
                                $work_days += 0.5;
                            }
                        }
                    }
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
                    'emp_companyid' => $employee->emp_company,
                    'emp_gender' => $employee->emp_gender,
                    'shiftname' => $employee->shiftname,
                    'is_sat_ot_type_as_act' => $employee->is_sat_ot_type_as_act,
                    'is_sun_ot_type_as_act' => $employee->is_sun_ot_type_as_act,
                    'attendance' => $attendanceRecords,
                    'attendance_days' => $attendance_days,
                    'work_days' => $work_days
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