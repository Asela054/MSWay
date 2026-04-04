<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
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

          // Get accessible employee IDs based on user access rights
        $userId = Auth::id();
        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
        
        // Return empty HTML if no accessible employees
        if (empty($accessibleEmployeeIds)) {
            return response()->json(['html' => '']);
        }

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
            ->whereIn('employees.emp_id', $accessibleEmployeeIds)
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
    
    // Get accessible employee IDs based on user access rights
    $userId = Auth::id();
    $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
    
    // Return empty HTML if no accessible employees
    if (empty($accessibleEmployeeIds)) {
        return response()->json(['html' => '']);
    }

    // Get all OT approved records with employee info in a single query
    $otRecords = DB::table('ot_approved')
        ->join('employees', 'ot_approved.emp_id', '=', 'employees.emp_id')
        ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->where('employees.deleted', 0)
        ->where('employees.emp_department', $department)
        ->whereBetween('ot_approved.date', [$from_date, $to_date])
        ->whereIn('employees.emp_id', $accessibleEmployeeIds)
        ->select(
            'employees.id', 
            'employees.emp_id', 
            'employees.emp_fullname', 
            'departments.name AS departmentname',
            'ot_approved.date',
            'ot_approved.from',
            'ot_approved.to',
            'ot_approved.hours'
        )
        ->orderBy('employees.id')
        ->orderBy('ot_approved.date')
        ->get();
    
    // Group records by employee
    $employeesWithOT = [];
    foreach ($otRecords as $record) {
        $empKey = $record->emp_id;
        
        if (!isset($employeesWithOT[$empKey])) {
            $employeesWithOT[$empKey] = [
                'employee' => (object)[
                    'id' => $record->id,
                    'emp_id' => $record->emp_id,
                    'emp_fullname' => $record->emp_fullname,
                    'departmentname' => $record->departmentname
                ],
                'attendance' => []
            ];
        }
        
        // Add attendance record
        $employeesWithOT[$empKey]['attendance'][] = [
            'date' => $record->date,
            'empno' => $record->emp_id,
            'Department' => $record->departmentname,
            'in_time' => $record->from ? date('H:i', strtotime($record->from)) : ' ',
            'out_time' => $record->to ? date('H:i', strtotime($record->to)) : ' ',
            'duration' => $record->hours
        ];
    }
    
    // Convert to indexed array for view
    $pdfData = array_values($employeesWithOT);

    ini_set("memory_limit", "999M");
    ini_set("max_execution_time", "999");

    $pdf = Pdf::loadView('AuditReports.otreportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
    return $pdf->download('Employee OT Report.pdf');
}

    // public function generateOTreport(Request $request) {
    //     $department = $request->get('department');
    //     $from_date = $request->get('from_date');
    //     $to_date = $request->get('to_date');
    //     // $from_range = $request->get('from_range', 0);
    //     // $to_range = $request->get('to_range', 20);
    
    //     $period = new DatePeriod(
    //         new DateTime($from_date),
    //         new DateInterval('P1D'), 
    //         new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
    //     );
    
    //     // $from_range = max(0, (int) $from_range);
    //     // $to_range = max($from_range, (int) $to_range);
    //     // $limit = $to_range - $from_range; 

    //        // Get accessible employee IDs based on user access rights
    //     $userId = Auth::id();
    //     $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
        
    //     // Return empty HTML if no accessible employees
    //     if (empty($accessibleEmployeeIds)) {
    //         return response()->json(['html' => '']);
    //     }

    //     $employees = DB::table('employees')
    //         ->select(
    //             'employees.id', 
    //             'employees.emp_id', 
    //             'employees.emp_fullname', 
    //             'departments.name AS departmentname'
    //         )
    //         ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
    //         ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
    //         ->where('employees.deleted', 0)
    //         ->where('employees.emp_department', $department)
    //         ->whereBetween('attendances.date', [$from_date, $to_date])
    //         ->whereIn('employees.emp_id', $accessibleEmployeeIds)
    //         ->groupBy('employees.id')
    //         ->orderBy('employees.id')
    //         ->get();
    
    //     $pdfData = [];
    
    //     foreach ($employees as $employee) {
    //         $attendanceData = [];
    
    //         foreach ($period as $date) {
    //             $currentDate = $date->format('Y-m-d');
    
    //             $otapproved = DB::table('ot_approved')
    //                 ->where('emp_id', $employee->emp_id)
    //                 ->whereDate('date', $currentDate)
    //                 ->select('from','to','hours')
    //                 ->first();
    
    //                 if ($otapproved) { 
    //                     if ($otapproved->from || $otapproved->to) {
    //                         $inTime = $otapproved->from ? date('H:i', strtotime($otapproved->from)) : ' ';
    //                         $outTime = $otapproved->to ? date('H:i', strtotime($otapproved->to)) : ' ';
                    
    //                         $attendanceData[] = [
    //                             'date' => $currentDate,
    //                             'empno' => $employee->emp_id,
    //                             'Department' => $employee->departmentname,
    //                             'in_time' => $inTime,
    //                             'out_time' => $outTime,
    //                             'duration' => $otapproved->hours
    //                         ];
    //                     }
    //                 }
    //         }
    //         $pdfData[] = [
    //             'employee' => $employee,
    //             'attendance' => $attendanceData,
    //         ];
    //     }

    //     ini_set("memory_limit", "999M");
	// 	ini_set("max_execution_time", "999");

    //     $pdf = Pdf::loadView('AuditReports.otreportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
    //     return $pdf->download('Employee OT Report.pdf');
    // }
    
}
