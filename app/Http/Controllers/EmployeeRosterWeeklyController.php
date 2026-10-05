<?php

namespace App\Http\Controllers;


use App\EmployeeRoster;
use App\EmployeeRosterDetails;
use App\Employee;
use App\ShiftType;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class EmployeeRosterWeeklyController extends Controller
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


    public function getRosterInfo(Request $request){
        $user = Auth::user();
        if (!$user->can('employee-roster')) {
            return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId  = $request->get('department_id');
        $fromDate     = $request->get('fromdate');
         $toDate       = $request->get('todate');

        // Parse once, reuse
        $start         = Carbon::parse($fromDate)->startOfDay();
         $end           = Carbon::parse($toDate)->startOfDay();
        $yearmonthname = $start->format('Y F') . ' - ' . $end->format('Y F');
        $daysInRange   = $start->diffInDays($end) + 1;


        // Build dates list
        $datesList = [];
        for ($i = 0; $i < $daysInRange; $i++) {
           $currentDate = $start->copy()->addDays($i);
            $datesList[] = [
                'date'         => $currentDate->toDateString(),
                'day_name'     => $currentDate->format('l'),
                'short_day'    => $currentDate->format('D'),
                'dateshortday' => $currentDate->format('d D'),
                'datemonth'    => $currentDate->format('m_d'),
            ];
        }

        // Get employee IDs for this department first
        $employees = Employee::where('emp_department', $departmentId)
            ->where('deleted', 0)
            ->select('emp_id AS id', 'emp_name_with_initial AS fullname', 'calling_name AS callingname')
            ->get();

        $employeeIds = $employees->pluck('id')->toArray();

        $shifts = ShiftType::where('deleted', 0)
            ->select('id', 'shift_code AS code')
            ->get();
        $shifts->push((object) ['id' => 100, 'code' => 'SD']);
        $shifts->push((object) ['id' => 101, 'code' => 'DO']);

        // Filter roster by department employees only
        $rosterRaw = DB::table('employee_roster_details')
            ->select('shift_id', 'emp_id', 'work_date')
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('emp_id', $employeeIds)   // only this department
            ->get();

        // Build lookup array: [emp_id][work_date] => [shift_id, shift_id, ...]
        // This replaces the slow $roster->where() in blade
        $rosterMap = [];
        foreach ($rosterRaw as $row) {
            $workDate = Carbon::parse($row->work_date)->toDateString();
            $rosterMap[$row->emp_id][$workDate][] = $row->shift_id;
        }

        $html = view('roster.addeditrosterinfo', compact(
            'employees', 'datesList', 'shifts', 'rosterMap', 'yearmonthname'
        ))->render();

        return response($html, 200);
    }

      public function getRosterData(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId = $request->get('department_id');
        $fromDate     = $request->get('fromdate');
          $toDate       = $request->get('todate');

        if (!$departmentId || !$fromDate || !$toDate) {
            return response()->json(['error' => 'Missing department_id or month'], 400);
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
                // Group by day → return array of ALL shift_ids per day
                return $records->groupBy(function ($item) {
                    return date('j', strtotime($item->work_date));
                })->map(function ($dayRecords) {
                    return $dayRecords->pluck('shift_id')->toArray();
                });
            });

        return response()->json($rosters);
    }
}
