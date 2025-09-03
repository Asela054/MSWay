<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateInterval;
use DatePeriod;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class AuditAttendanceReportController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('attendance-audit-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('AuditReports.attendance_report', compact('companies'));
    }
    public function auditotreport()
    {
        $permission = Auth::user()->can('attendance-audit-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('AuditReports.audit_ot_report', compact('companies'));
    }

    public function generatetimereport(Request $request) {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
    
        $period = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'), 
            new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );
    

        $employees = DB::table('employees')
            ->select(
                'employees.id', 
                'employees.emp_id', 
                'employees.emp_fullname', 
                'departments.name AS departmentname'
            )
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
            ->where('employees.deleted', 0)
            ->where('employees.emp_department', $department)
            ->whereBetween('attendances.date', [$from_date, $to_date])
            ->groupBy('employees.id')
            ->orderBy('employees.id')
            ->get();
    
        $pdfData = [];
    
        foreach ($employees as $employee) {
            $attendanceData = [];
    
            foreach ($period as $date) {
                $currentDate = $date->format('Y-m-d');
    
                $attendance = DB::table('audit_attendance')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('attendance_date', $currentDate)
                    ->selectRaw('audit_ontime as in_time, audit_offtime as out_time, audit_workhours as duration')
                    ->first();
    
                    if ($attendance) {
                        $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
                        $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';
                        $duration = $attendance->duration;

                            $attendanceData[] = [
                                'date' => $currentDate,
                                'empno' => $employee->emp_id,
                                'Department' => $employee->departmentname,
                                'in_time' => $inTime,
                                'out_time' => $outTime,
                                'duration' => $duration
                            ];
                    }
            }
            $pdfData[] = [
                'employee' => $employee,
                'attendance' => $attendanceData,
            ];
        }

        ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");

        $pdf = Pdf::loadView('AuditReports.timeinoutreportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
        return $pdf->download('Audit Time In-Out Report.pdf');
    }

    public function generateauditOTreport(Request $request) {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
    
    
        $period = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'), 
            new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );

        $employees = DB::table('employees')
            ->select(
                'employees.id', 
                'employees.emp_id', 
                'employees.emp_fullname', 
                'departments.name AS departmentname'
            )
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
            ->where('employees.deleted', 0)
            ->where('employees.emp_department', $department)
            ->whereBetween('attendances.date', [$from_date, $to_date])
            ->groupBy('employees.id')
            ->orderBy('employees.id')
            ->get();
    
        $pdfData = [];
    
        foreach ($employees as $employee) {
            $attendanceData = [];
    
            foreach ($period as $date) {
                $currentDate = $date->format('Y-m-d');
    
                $otapproved = DB::table('audit_attendance')
                    ->where('emp_id', $employee->emp_id)
                    ->where('attendance_date', $currentDate)
                    ->select('audit_ot_from','audit_offtime','audit_ot_count')
                    ->first();
    
                    if ($otapproved) { 
                        if ($otapproved->audit_ot_from || $otapproved->audit_offtime) {
                            $inTime = $otapproved->audit_ot_from ? date('H:i', strtotime($otapproved->audit_ot_from)) : ' ';
                            $outTime = $otapproved->audit_offtime ? date('H:i', strtotime($otapproved->audit_offtime)) : ' ';
                    
                            $attendanceData[] = [
                                'date' => $currentDate,
                                'empno' => $employee->emp_id,
                                'Department' => $employee->departmentname,
                                'in_time' => $inTime,
                                'out_time' => $outTime,
                                'duration' => $otapproved->audit_ot_count
                            ];
                        }
                    }
            }
            $pdfData[] = [
                'employee' => $employee,
                'attendance' => $attendanceData,
            ];
        }

        ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");

        $pdf = Pdf::loadView('AuditReports.auditotreportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
        return $pdf->download('Employee Audit OT Report.pdf');
    }

}
