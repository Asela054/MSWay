<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\EmployeeHelper;
use Excel;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class RptLateattendanceController extends Controller
{

     public function lateattendent()
    {
        $permission = Auth::user()->can('late-attendance-report');
        if (!$permission) {
            abort(403);
        }
        return view('Report.lateattendance' );
    }

    public function late_attendance_report_list(Request $request)
    {
        $permission = Auth::user()->can('late-attendance-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        // Base query
        $query = DB::table('employee_late_attendances as ela')
            ->select([
                'ela.*',
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'departments.name as dept_name',
                'shift_types.onduty_time',
                'shift_types.offduty_time'
            ])
            ->join('employees', 'ela.emp_id', '=', 'employees.emp_id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->where('employees.deleted', 0);

        // Apply filters
        if ($request->has('department') && $request->department != '') {
            $query->where('departments.id', $request->department);
        }

        if ($request->has('employee') && $request->employee != '') {
            $query->where('ela.emp_id', $request->employee);
        }

        if ($request->has('fromdate') && $request->fromdate != '' && 
            $request->has('to_date') && $request->to_date != '') {
            $query->whereBetween('ela.date', [$request->fromdate, $request->to_date]);
        }

        if ($request->has('latestatus') && $request->latestatus != '') {
            if ($request->latestatus == 1) { // Late Coming
                $query->whereRaw('TIME(ela.check_in_time) > TIME(shift_types.onduty_time)');
            } elseif ($request->latestatus == 2) { // Early Going
                $query->whereRaw('TIME(ela.check_out_time) < TIME(shift_types.offduty_time)');
            }
        }

        // Handle search value
        if ($request->has('search') && $request->search['value'] != '') {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('employees.emp_id', 'like', "{$searchValue}%")
                ->orWhere('employees.emp_name_with_initial', 'like', "%{$searchValue}%")
                ->orWhere('employees.calling_name', 'like', "%{$searchValue}%")
                ->orWhere('departments.name', 'like', "%{$searchValue}%")
                ->orWhere('ela.check_in_time', 'like', "%{$searchValue}%");
            });
        }

        // Get total records count
        $totalRecords = $query->count();

        // Apply ordering
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $request->input("columns.{$orderColumnIndex}.data");
        $orderDirection = $request->input('order.0.dir');

        // Map column names to actual database columns
        $columnMapping = [
            'uid' => 'ela.emp_id',
            'employee_display' => 'employees.emp_name_with_initial',
            'date' => 'ela.date',
            'dept_name' => 'departments.name',
            // Add other mappings as needed
        ];

        $orderColumn = $columnMapping[$orderColumn] ?? $orderColumn;
        $query->orderBy($orderColumn, $orderDirection);

        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);

        // Get filtered data
        $records = $query->get();

        // Format data
        $data_arr = [];
        foreach ($records as $record) {
            $check_in = $record->check_in_time ? date('G:i', strtotime($record->check_in_time)) : '--';
            $check_out = $record->check_out_time ? date('G:i', strtotime($record->check_out_time)) : '--';
            
            // Calculate time differences
            $late_minutes = 0;
            $early_minutes = 0;
            $late_time = '--';
            $early_time = '--';
            
            if ($record->check_in_time && $record->onduty_time) {
                $late_seconds = strtotime($record->check_in_time) - strtotime($record->onduty_time);
                if ($late_seconds > 0) {
                    $late_minutes = round($late_seconds / 60);
                    $late_time = $this->formatTimeDifference($late_seconds);
                }
            }
            
            if ($record->check_out_time && $record->offduty_time) {
                $early_seconds = strtotime($record->offduty_time) - strtotime($record->check_out_time);
                if ($early_seconds > 0) {
                    $early_minutes = round($early_seconds / 60);
                    $early_time = $this->formatTimeDifference($early_seconds);
                }
            }
            
            $data_arr[] = [
                "uid" => $record->emp_id,
                "employee_display" => EmployeeHelper::getDisplayName((object)[
                    'emp_id' => $record->emp_id,
                    'emp_name_with_initial' => $record->emp_name_with_initial,
                    'calling_name' => $record->calling_name
                ]),
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "check_in_time" => $check_in,
                "scheduled_check_in" => $record->onduty_time ? date('G:i', strtotime($record->onduty_time)) : '--',
                "check_in_status" => $record->check_in_time && $record->onduty_time ? 
                                    Carbon::parse($check_in)->diffForHumans($record->onduty_time) : '--',
                "late_minutes" => $late_minutes,
                "late_time" => $late_time,
                "check_out_time" => $check_out,
                "scheduled_check_out" => $record->offduty_time ? date('G:i', strtotime($record->offduty_time)) : '--',
                "check_out_status" => $record->check_out_time && $record->offduty_time ? 
                                    Carbon::parse($check_out)->diffForHumans($record->offduty_time) : '--',
                "early_minutes" => $early_minutes,
                "early_time" => $early_time,
                "date" => $record->date,
                "dept_name" => $record->dept_name,
                "status" => $this->getLateStatus($record->check_in_time, $record->check_out_time, 
                                            $record->onduty_time, $record->offduty_time)
            ];
        }

        $response = [
            "draw" => intval($request->input('draw')),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords, // For simplicity, use same as total records
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }


    private function formatTimeDifference($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $result = [];
        if ($hours > 0) {
            $result[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $result[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }
        
        return implode(' ', $result) ?: '0 minutes';
    }

    private function getLateStatus($check_in, $check_out, $onduty, $offduty)
    {
        if (!$check_in) return 'No Check-in';
        
        $isLate = $check_in && $onduty && (strtotime($check_in) > strtotime($onduty));
        $isEarly = $check_out && $offduty && (strtotime($check_out) < strtotime($offduty));
        
        if ($isLate && $isEarly) return 'Late & Early';
        if ($isLate) return 'Late Coming';
        if ($isEarly) return 'Early Going';
        
        return 'On Time';
    }

    public function exportLateattend()
    {

        $att_data = DB::query()
            ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), DB::raw('Min(at1.timestamp) as firsttimestamp'), 'employees.emp_name_with_initial', 'shift_types.onduty_time', 'shift_types.offduty_time')
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->groupBy('at1.uid', 'at1.date')
            ->get()->toarray();


        $att_array[] = array('Employee Id', 'Name With Initial', 'Date', 'First Checkin', 'Last Checkout', 'Location');
        foreach ($att_data as $attendents) {
            if ($timestamp = date('G:i', strtotime($attendents->timestamp)) > $onduty_time = date('G:i', strtotime($attendents->onduty_time))) {
                $att_array[] = array(
                    'Employee Id' => $attendents->uid,
                    'Name With Initial' => $attendents->emp_name_with_initial,
                    'Date' => $attendents->date,
                    'First Checkin' => $attendents->timestamp,
                    'Last Checkout' => $attendents->lasttimestamp,
                    'Location' => $attendents->location


                );
            }
        }
        Excel::create('Employee Late Attendent Data', function ($excel) use ($att_array) {
            $excel->setTitle('Employee Late Attendent Data');
            $excel->sheet('Employee Late Attendent Data', function ($sheet) use ($att_array) {
                $sheet->fromArray($att_array, null, 'A1', false, false);
            });
        })->download('xlsx');


    }




      // public function late_attendance_report_list(Request $request)
    // {
    //     $permission = Auth::user()->can('late-attendance-report');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     }

    //     // Read values
    //     $department = $request->get('department');
    //     $fromdate = $request->get('fromdate');
    //     $to_date = $request->get('to_date');
    //     $employee = $request->get('employee');
    //     $latestatus = $request->get('latestatus');

    //     $draw = $request->get('draw');
    //     $start = $request->get("start");
    //     $rowperpage = $request->get("length");

    //     $columnIndex_arr = $request->get('order');
    //     $columnName_arr = $request->get('columns');
    //     $order_arr = $request->get('order');
    //     $search_arr = $request->get('search');

    //     $columnIndex = $columnIndex_arr[0]['column'];
    //     $columnName = $columnName_arr[$columnIndex]['data'];
    //     $columnSortOrder = $order_arr[0]['dir'];
    //     $searchValue = $search_arr['value'];

    //     // Map column names to actual database columns
    //     $columnMapping = [
    //         'uid' => 'ela.emp_id', // Changed from 'uid' to 'ela.emp_id'
    //         'emp_name_with_initial' => 'employees.emp_name_with_initial',
    //         'date' => 'ela.date',
    //         'dept_name' => 'departments.name',
    //         // Add other columns as needed
    //     ];

    //     // Use mapped column name or fall back to the original
    //     $orderColumn = $columnMapping[$columnName] ?? $columnName;

    //     // Base query components
    //     $baseQuery = 'FROM `employee_late_attendances` as `ela` 
    //                 INNER JOIN `employees` ON `ela`.`emp_id` = `employees`.`emp_id` 
    //                 LEFT JOIN `shift_types` ON `employees`.`emp_shift` = `shift_types`.`id` 
    //                 LEFT JOIN `departments` ON `departments`.`id` = `employees`.`emp_department` 
    //                 WHERE employees.deleted = 0 ';

    //     // Apply filters
    //     if ($searchValue != '') {
    //         $baseQuery .= 'AND (employees.emp_id LIKE "'.$searchValue.'%" 
    //                     OR employees.emp_name_with_initial LIKE "'.$searchValue.'%" 
    //                     OR ela.check_in_time LIKE "'.$searchValue.'%" 
    //                     OR departments.name LIKE "'.$searchValue.'%") ';
    //     }

    //     if ($department != '') {
    //         $baseQuery .= 'AND departments.id = "'.$department.'" ';
    //     }

    //     if ($employee != '') {
    //         $baseQuery .= 'AND ela.emp_id = "'.$employee.'" ';
    //     }

    //     if ($fromdate != '' && $to_date != '') {
    //         $baseQuery .= 'AND ela.date BETWEEN "'.$fromdate.'" AND "'.$to_date.'" ';
    //     }

    //     if ($latestatus != '') {
    //         if ($latestatus == 1) { // Late Coming
    //             $baseQuery .= 'AND TIME(ela.check_in_time) > TIME(shift_types.onduty_time) ';
    //         } elseif ($latestatus == 2) { // Early Going
    //             $baseQuery .= 'AND TIME(ela.check_out_time) < TIME(shift_types.offduty_time) ';
    //         }
    //     }

    //     // Total records count
    //     $totalRecords = DB::table('employee_late_attendances')
    //         ->join('employees', 'employee_late_attendances.emp_id', '=', 'employees.emp_id')
    //         ->where('employees.deleted', 0)
    //         ->count();

    //     // Total records with filter
    //     $totalRecordswithFilter = DB::select('SELECT COUNT(*) as acount FROM (' . 
    //         'SELECT ela.id ' . $baseQuery . 
    //         'GROUP BY ela.id' . 
    //         ') t')[0]->acount;

    //     // Fetch records
    //     $records = DB::select('SELECT shift_types.*, ela.*, 
    //                         employees.emp_name_with_initial, 
    //                         departments.name as dept_name,
    //                         ela.emp_id as uid ' .  // Added this alias to maintain compatibility
    //                         $baseQuery . 
    //                         'ORDER BY '.$orderColumn.' '.$columnSortOrder.' ' .
    //                         'LIMIT ' . $start . ', ' . $rowperpage);

    //      $data_arr = array();

    //     foreach ($records as $record) {
    //         $check_in = date('G:i', strtotime($record->check_in_time));
    //         $check_out = $record->check_out_time ? date('G:i', strtotime($record->check_out_time)) : '--';
            
    //         // Calculate time differences
    //         $late_minutes = 0;
    //         $early_minutes = 0;
    //         $late_time = '--';
    //         $early_time = '--';
            
    //         if ($record->check_in_time && $record->onduty_time) {
    //             $late_seconds = strtotime($record->check_in_time) - strtotime($record->onduty_time);
    //             if ($late_seconds > 0) {
    //                 $late_minutes = round($late_seconds / 60);
    //                 $late_time = $this->formatTimeDifference($late_seconds);
    //             }
    //         }
            
    //         if ($record->check_out_time && $record->offduty_time) {
    //             $early_seconds = strtotime($record->offduty_time) - strtotime($record->check_out_time);
    //             if ($early_seconds > 0) {
    //                 $early_minutes = round($early_seconds / 60);
    //                 $early_time = $this->formatTimeDifference($early_seconds);
    //             }
    //         }
            
    //         $data_arr[] = array(
    //             "uid" => $record->emp_id,
    //             "emp_name_with_initial" => $record->emp_name_with_initial,
    //             "check_in_time" => $check_in,
    //             "scheduled_check_in" => $record->onduty_time ? date('G:i', strtotime($record->onduty_time)) : '--',
    //             "check_in_status" => $record->check_in_time && $record->onduty_time ? 
    //                                 Carbon::parse($check_in)->diffForHumans($record->onduty_time) : '--',
    //             "late_minutes" => $late_minutes,
    //             "late_time" => $late_time,
    //             "check_out_time" => $check_out,
    //             "scheduled_check_out" => $record->offduty_time ? date('G:i', strtotime($record->offduty_time)) : '--',
    //             "check_out_status" => $record->check_out_time && $record->offduty_time ? 
    //                                 Carbon::parse($check_out)->diffForHumans($record->offduty_time) : '--',
    //             "early_minutes" => $early_minutes,
    //             "early_time" => $early_time,
    //             "date" => $record->date,
    //             "dept_name" => $record->dept_name,
    //             "status" => $this->getLateStatus($record->check_in_time, $record->check_out_time, 
    //                                         $record->onduty_time, $record->offduty_time)
    //         );
    //     }

    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordswithFilter,
    //         "aaData" => $data_arr
    //     );

    //     return response()->json($response);
    // }
}
