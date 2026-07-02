<?php

namespace App\Http\Controllers;

use App\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class RpttimestampsController extends Controller
{
     public function index(){
        $permission = Auth::user()->can('attendance-report');
        if(!$permission){
            abort(403);
        }

        return view('Report.timestamp_report');
    }


    public function generate_timestamp_report(Request $request)
{
    $permission = Auth::user()->can('attendance-report');
    if (!$permission) {
        return response()->json(['error' => 'UnAuthorized'], 401);
    }

    $department = $request->get('department');
    $location = $request->get('location');
    $company = $request->get('company');
    $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    // Parse dates
    $from = Carbon::parse($from_date);
    $to = Carbon::parse($to_date);

        // Generate date range
        $dateRange = [];
        $currentDate = $from->copy();
        
        while ($currentDate->lte($to)) {
            $dateRange[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        $htmlTables = '';

        foreach ($dateRange as $date) {
            // Get attendance data for this specific date with timestamp count
            $query = DB::table('attendances as a')
                ->leftjoin('employees as e', 'a.emp_id', '=', 'e.emp_id')
                ->leftjoin('departments as dep', 'e.emp_department', '=', 'dep.id')
                ->leftjoin('branches as loc', 'e.emp_location', '=', 'loc.id')
                ->select(
                    'a.emp_id',
                    'e.emp_name_with_initial',
                    'e.emp_department',
                    'dep.name as employee_department',
                    'loc.location as employee_location',
                    DB::raw('COUNT(a.id) as timestamp_count'),
                    DB::raw('GROUP_CONCAT(CONCAT(a.timestamp, "|", COALESCE(a.type, "N/A")) ORDER BY a.timestamp ASC SEPARATOR ",") as timestamps_data')
                )
                ->whereRaw("DATE(a.date) = ?", [$date])
                ->groupBy('a.emp_id', 'e.emp_name_with_initial', 'e.emp_department', 'e.emp_location');

            // Apply filters
            if (!empty($department)) {
                $query->where('e.emp_department', $department);
            }
            if (!empty($location)) {
                $query->where('e.emp_location', $location);
            }
            if (!empty($company)) {
                $query->where('e.company', $company);
            }

            $employeeRecords = $query->orderBy('a.emp_id')->get();

            if ($employeeRecords->isEmpty()) {
                // No records for this date
                $htmlTables .= '<div class="date-section mb-5">';
                $htmlTables .= '<h5 class="text-center rounded">';
                $htmlTables .= 'Date: ' . Carbon::parse($date)->format('d-M-Y (l)');
                $htmlTables .= '</h5>';
                $htmlTables .= '<div class="alert alert-info text-center">';
                $htmlTables .= 'No attendance records found for ' . Carbon::parse($date)->format('d-M-Y');
                $htmlTables .= '</div></div>';
                continue;
            }

            // Get maximum timestamp count for this date
            $maxCount = $employeeRecords->max('timestamp_count');

            // Start building HTML table for this date
            $htmlTables .= '<div class="date-section mb-5">';
            $htmlTables .= '<h5 class="text-center p-2 mb-3">';
            $htmlTables .= 'Date: ' . Carbon::parse($date)->format('d-M-Y (l)');
            $htmlTables .= '</h5>';

            $htmlTables .= '<div class="table-responsive">';
            $htmlTables .= '<table class="table table-bordered table-sm small" id="table-' . str_replace('-', '', $date) . '">';
            
            // Table header
            $htmlTables .= '<thead class="thead-light">';
            $htmlTables .= '<tr>';
            $htmlTables .= '<th>Emp ID</th>';
            $htmlTables .= '<th>Employee Name</th>';
            $htmlTables .= '<th>Location</th>';
            $htmlTables .= '<th>Department</th>';
            $htmlTables .= '<th>Date</th>';
            // Timestamp columns
            for ($i = 1; $i <= $maxCount; $i++) {
                $htmlTables .= '<th>Time ' . $i . '</th>';
            }
            
            $htmlTables .= '</tr>';
            $htmlTables .= '</thead>';
            
            // Table body
            $htmlTables .= '<tbody>';
            
            foreach ($employeeRecords as $record) {
                $htmlTables .= '<tr>';
                $htmlTables .= '<td>' . htmlspecialchars($record->emp_id) . '</td>';
                $htmlTables .= '<td>' . htmlspecialchars($record->emp_name_with_initial) . '</td>';
                $htmlTables .= '<td>' . htmlspecialchars($record->employee_location) . '</td>';
                $htmlTables .= '<td>' . htmlspecialchars($record->employee_department) . '</td>';
                $htmlTables .= '<td>' . htmlspecialchars($date) . '</td>';
                
                // Parse timestamps data
                $timestamps = [];
                if ($record->timestamps_data) {
                    $timestampParts = explode(',', $record->timestamps_data);
                    foreach ($timestampParts as $part) {
                        $parts = explode('|', $part);
                        if (count($parts) === 2) {
                            $timestamps[] = [
                                'time' => Carbon::parse($parts[0])->format('H:i:s'),
                                'type' => $parts[1]
                            ];
                        }
                    }
                }
                
                $timestampCount = count($timestamps);
                
                // Add timestamp cells
                for ($i = 0; $i < $maxCount; $i++) {
                    if ($i < $timestampCount) {
                        $timestamp = $timestamps[$i];
                       $htmlTables .= '<td>' . htmlspecialchars($timestamp['time']) . '</td>';
                    } else {
                        $htmlTables .= '<td class="text-muted">-</td>';
                    }
                }
                
                $htmlTables .= '</tr>';
            }
            
            $htmlTables .= '</tbody>';
            $htmlTables .= '</table>';
            $htmlTables .= '</div>'; 
            $htmlTables .= '</div>'; 
            
            if ($date !== end($dateRange)) {
                $htmlTables .= '<div class="page-break" style="page-break-after: always;"></div>';
            }
        }
        return response()->json([
            'success' => true,
            'html' => $htmlTables,
            'total_days' => count($dateRange)
        ]);

   
}


}
