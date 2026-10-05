<?php

namespace App\Http\Controllers;

use App\EmployeeRosterDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ShiftChangeLog;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeRosterWeeklyDetailsController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function getViewRosterData(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster-view');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId = $request->get('department_id');
        $fromDate     = $request->get('fromdate');
        $toDate       = $request->get('todate');

        if (!$departmentId || !$fromDate || !$toDate) {
            return response()->json(['error' => 'Missing department_id, fromdate or todate'], 400);
        }

        $startDate = date('Y-m-d', strtotime($fromDate));
        $endDate   = date('Y-m-d', strtotime($toDate));

        $rosters = EmployeeRosterDetails::whereBetween('work_date', [$startDate, $endDate])
            ->whereIn('emp_id', function ($query) use ($departmentId) {
                $query->select('emp_id')
                    ->from('employees')
                    ->where('emp_department', $departmentId);
            })
            ->get()
            ->groupBy('emp_id')
            ->map(function ($records) {
                return $records->groupBy(function ($item) {
                    return date('Y-m-d', strtotime($item->work_date));
                })->map(function ($dayRecords) {
                    return $dayRecords->pluck('shift_id')->toArray();
                });
            });

        
        return response()->json($rosters);
    }

     public function colnerosterstore(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster-view');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId = $request->get('department_id');
        $fromDate     = $request->get('fromdate');
        $toDate       = $request->get('todate');

        if (!$departmentId || !$fromDate || !$toDate) {
            return response()->json(['error' => 'Missing department_id, fromdate or todate'], 400);
        }

        $start = Carbon::parse($fromDate)->startOfDay();
        $end   = Carbon::parse($toDate)->startOfDay();

        if ($start->gt($end)) {
            return response()->json(['error' => 'Invalid date range'], 400);
        }

        // Range length in days; the clone goes to the next period of the same length
        $daysInRange = $start->diffInDays($end) + 1;

        $startDate = $start->toDateString();
        $endDate   = $end->toDateString();

        // Fetch all roster records for the given range + department
        $rosters = DB::table('employee_roster_details')
            ->select(
                'employee_roster_details.id',
                'employee_roster_details.shift_id',
                'employee_roster_details.emp_id',
                'employee_roster_details.work_date',
                'employee_roster_details.scheduling_status',
                'employee_roster_details.remark'
            )
            ->leftJoin('employees', 'employee_roster_details.emp_id', '=', 'employees.emp_id')
            ->whereBetween('employee_roster_details.work_date', [$startDate, $endDate])
            ->where('employees.emp_department', $departmentId)
            ->get();

        if ($rosters->isEmpty()) {
            return response()->json(['message' => 'No roster records found for the given date range and department'], 404);
        }

        $newRecords        = [];
        $skippedDates      = [];
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($rosters as $roster) {
            // Shift the date forward by the range length
            $newWorkDate = Carbon::parse($roster->work_date)->addDays($daysInRange)->toDateString();

            $exists = DB::table('employee_roster_details')
                ->where('emp_id', $roster->emp_id)
                ->where('work_date', $newWorkDate)
                ->exists();

            if ($exists) {
                $skippedDates[] = $newWorkDate;
                continue;
            }

            $newRecords[] = [
                'shift_id'           => $roster->shift_id,
                'emp_id'             => $roster->emp_id,
                'work_date'          => $newWorkDate,
                'scheduling_status'  => $roster->scheduling_status,
                'remark'             => $roster->remark,
                'created_at'         => $current_date_time,
                'updated_at'         => $current_date_time,
            ];
        }

        if (!empty($newRecords)) {
            foreach (array_chunk($newRecords, 500) as $chunk) {
                DB::table('employee_roster_details')->insert($chunk);
            }
        }

        return response()->json(['success' => 'Roster cloned successfully to the next period']);
    }

}
