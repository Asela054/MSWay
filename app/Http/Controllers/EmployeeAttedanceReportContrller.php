<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use PDF;

class EmployeeAttedanceReportContrller extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('employee-time-in-out-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('AuditReports.employee_timeinout_report', compact('companies'));
    }

  
    public function generatereport(Request $request) {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        // $from_range = $request->get('from_range', 0);
        // $to_range = $request->get('to_range', 20);
    
        $period = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'), 
            new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );
    
        // $from_range = max(0, (int) $from_range);
        // $to_range = max($from_range, (int) $to_range);
        // $limit = $to_range - $from_range; 

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
    
                $attendance = DB::table('attendances')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('date', $currentDate)
                    ->selectRaw('MIN(timestamp) as in_time, MAX(timestamp) as out_time, MAX(date) as max_date')
                    ->first();
    
                    if ($attendance->in_time || $attendance->out_time) {
                            $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
                            $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';

                            $duration = Carbon::parse($inTime)->diff(Carbon::parse($outTime))->format('%H:%I');
                        
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
        return $pdf->download('Employee Time In-Out Report.pdf');
    }
    
    public function otreport()
    {
        $permission = Auth::user()->can('employee-actual-ot-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('AuditReports.employee_ot_report', compact('companies'));
    }

    public function generateOTreport(Request $request) {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        // $from_range = $request->get('from_range', 0);
        // $to_range = $request->get('to_range', 20);
    
        $period = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'), 
            new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );
    
        // $from_range = max(0, (int) $from_range);
        // $to_range = max($from_range, (int) $to_range);
        // $limit = $to_range - $from_range; 

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
    
                $otapproved = DB::table('ot_approved')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('date', $currentDate)
                    ->select('from','to','hours')
                    ->first();
    
                    if ($otapproved) { 
                        if ($otapproved->from || $otapproved->to) {
                            $inTime = $otapproved->from ? date('H:i', strtotime($otapproved->from)) : ' ';
                            $outTime = $otapproved->to ? date('H:i', strtotime($otapproved->to)) : ' ';
                    
                            $attendanceData[] = [
                                'date' => $currentDate,
                                'empno' => $employee->emp_id,
                                'Department' => $employee->departmentname,
                                'in_time' => $inTime,
                                'out_time' => $outTime,
                                'duration' => $otapproved->hours
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

        $pdf = Pdf::loadView('AuditReports.otreportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
        return $pdf->download('Employee OT Report.pdf');
    }

}
