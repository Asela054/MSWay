<?php

namespace App\Http\Controllers\Production_Module_Opma;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\OpmaDailyApprovalSummary;
use Auth;
use DateTime;

class AttendanceproductionreportController extends Controller
{
     public function index()
    {
        $permission = Auth::user()->can('attendance-timesheet');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('Report.opma_attendance_production_report', compact('companies'));
    }

    public function generatereport(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        // Step 1: Fetch Employee Data
            $employees = DB::select("
            SELECT emp.id, emp.emp_id, emp.emp_etfno, emp.emp_fullname, emp.emp_gender, 
                dept.name AS departmentname
            FROM employees emp
            LEFT JOIN departments dept ON emp.emp_department = dept.id
            WHERE emp.deleted = 0
            AND emp.is_resigned = 0
            AND emp.emp_department = ?
            ORDER BY emp.id ASC",
            [$department]
        );

        if (empty($employees)) {
            return response()->json(['data' => []]);
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

         $summaries = OpmaDailyApprovalSummary::with('details')
            ->where('emp_id', $employee->emp_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'asc')
            ->get();
           
            $attendanceRecords = [];
                
                foreach ($summaries as $summary) {

                    $mc_nos = $summary->details->map(function($d) {
                        return $d->machine ? $d->machine->machine : $d->machine_id;
                    })->implode(',');

                    $styles = $summary->details->map(function($d) {
                        return $d->style ? $d->style->title : $d->style_id;
                    })->implode(',');

                    $targets = $summary->details->pluck('target')->implode(',');
                    $produced = $summary->details->pluck('produced')->implode(',');
                    $averages = $summary->details->pluck('average')->map(function($val) { 
                        return number_format($val, 2) . '%'; 
                    })->implode(',');

                    $attendanceRecords[] = [
                        'formatted_date' => \Carbon\Carbon::parse($summary->date)->format('Y-m-d'),
                        'in_time' => \Carbon\Carbon::parse($summary->on_time)->format('H:i'),
                        'out_time' => \Carbon\Carbon::parse($summary->off_time)->format('H:i'),
                        'late_min' => $summary->late_minites ?? 0,
                        'mc_no' => $mc_nos,
                        'style_details' => $styles,
                        'target' => $targets,
                        'produced' => $produced,
                        'pro_avg' => $averages,
                        'weighted_avg' => number_format($summary->details->avg('average'), 2) . '%',
                        'pro_ins' => $summary->daily_average >= 50 ? 1 : 0,
                        'ot'      => $summary->daily_average >= 50 ? 1 : 0,
                        'trp_all' => $summary->daily_average >= 50 ? 1 : 0,
                        'att_all' => $summary->daily_average >= 50 ? 1 : 0,
                        'nig_all' => $summary->daily_average >= 50 ? 1 : 0, 
                        'trg_bo' => $summary->target_bonus ?? '',
                    ];
                }

                // Store Employee Data
                $employeeData[] = [
                    'id' => $employee->id,
                    'emp_id' => $employee->emp_id,
                    'emp_etfno' => $employee->emp_etfno,
                    'emp_fullname' => $employee->emp_fullname,
                    'departmentname' => $employee->departmentname,
                    'emp_gender' => $employee->emp_gender,
                    'attendance' => $attendanceRecords
                ];

            
        }

        $pdfData[] = ['data' => $employeeData];
        echo json_encode($pdfData);
    }
}
