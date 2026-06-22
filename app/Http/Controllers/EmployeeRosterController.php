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

class EmployeeRosterController extends Controller
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
    
    public function index(Request $request)
    {

        $user = auth()->user();
        $permission = $user->can('employee-roster');

        if(!$permission) {
            abort(403);
        }

         $currentMonth = \Carbon\Carbon::now();
            $months = [
                $currentMonth->copy()->subMonth(),
                $currentMonth,
                $currentMonth->copy()->addMonth(),
            ];
            
         return view('roster.monthlyemployeerosterfull', compact('months','currentMonth'));
    }

    // Load shift lidt for roster sheet 
     public function getshifts(Request $request)
     {
        $shifts = ShiftType::select('id', 'shift_code AS code')
            ->where('deleted', 0)
            ->get();
        return response()->json($shifts);
        
     }

    // public function getRosterInfo(Request $request){
    //     $user = Auth::user();
    //     $permission = $user->can('employee-roster');
    //     if (!$permission) {
    //          return response()->json(['error' => 'UnAuthorized']);
    //     }
      
    //     $departmentId = $request->get('department_id');
    //     $selectedMonth = $request->get('selectedMonth');
        
    //     // Parse the string and start at the 1st day of that month
    //     $startOfMonth = Carbon::parse($selectedMonth)->startOfMonth();
    //     $daysInMonth = $startOfMonth->daysInMonth;

    //     $datesList = [];

    //     for ($i = 0; $i < $daysInMonth; $i++) {
    //         // Clone the start date and add the current loop index days
    //         $currentDate = $startOfMonth->copy()->addDays($i);

    //         $datesList[] = [
    //             'date'     => $currentDate->toDateString(),       // e.g., "2026-06-01"
    //             'day_name' => $currentDate->format('l'),          // e.g., "Monday"
    //             'short_day'=> $currentDate->format('D'),          // e.g., "Mon" (Optional)
    //             'dateshortday'=> $currentDate->format('d D'),          // e.g., "Mon" (Optional)
    //             'datemonth'=> $currentDate->format('m_d'),          // e.g., "Mon" (Optional)
    //         ];
    //     }

    //     $employees = Employee::where('emp_department', $departmentId)
    //         ->select('emp_id AS id', 'emp_name_with_initial As fullname','calling_name As callingname')
    //         ->where('deleted', 0)
    //         ->get();

    //     $shifts = ShiftType::select('id', 'shift_code AS code')
    //         ->where('deleted', 0)
    //         ->get();

    //     $date = Carbon::parse($selectedMonth);
    //     $year = $date->year;   
    //     $month = $date->month;
    //     $yearmonthname = $date->format('Y F');

    //     $roster = DB::table('employee_roster_details')
    //         ->select(
    //             'id', 
    //             'shift_id', 
    //             'emp_id', 
    //             'work_date', 
    //             'scheduling_status', 
    //             'remark'
    //         )
    //         ->whereYear('work_date', $year)
    //         ->whereMonth('work_date', $month)
    //         ->get();

    //     // Render blade view to HTML string and return
    //     $html = view('roster.addeditrosterinfo', compact('employees', 'datesList', 'shifts', 'roster', 'yearmonthname'))->render();
    //     return response($html, 200);
    // }

    public function getRosterInfo(Request $request){
        $user = Auth::user();
        if (!$user->can('employee-roster')) {
            return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId  = $request->get('department_id');
        $selectedMonth = $request->get('selectedMonth');

        // Parse once, reuse
        $date         = Carbon::parse($selectedMonth);
        $year         = $date->year;
        $month        = $date->month;
        $yearmonthname = $date->format('Y F');
        $startOfMonth = $date->copy()->startOfMonth();
        $daysInMonth  = $startOfMonth->daysInMonth;

        // Build dates list
        $datesList = [];
        for ($i = 0; $i < $daysInMonth; $i++) {
            $currentDate = $startOfMonth->copy()->addDays($i);
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

        // Filter roster by department employees only
        $rosterRaw = DB::table('employee_roster_details')
            ->select('shift_id', 'emp_id', 'work_date')
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
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

     public function employee_list(Request $request)
     {

        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }
      

        $departmentId = $request->get('department_id');

        $employees = Employee::where('emp_department', $departmentId)
            ->select('emp_id AS id', 'emp_name_with_initial As fullname','calling_name As callingname')
            ->where('deleted', 0)
            ->get();

        return response()->json($employees);
        
     }

     public function getRosterData(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId = $request->get('department_id');
        $month = $request->get('month');

        if (!$departmentId || !$month) {
            return response()->json(['error' => 'Missing department_id or month'], 400);
        }

        $startDate = $month . '-01';
        $endDate = date("Y-m-t", strtotime($startDate));

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
    
    public function rosterView(Request $request){
         $user = auth()->user();
        $permission = $user->can('employee-roster-view');

        if(!$permission) {
            abort(403);
        }

         $currentMonth = \Carbon\Carbon::now();
            $months = [
                $currentMonth->copy()->subMonth(),
                $currentMonth,
                $currentMonth->copy()->addMonth(),
            ];
            
         return view('roster.employeesrosterview', compact('months','currentMonth'));
    }

    public function rosterapproveView(Request $request){
         $user = auth()->user();
        $permission = $user->can('employee-roster-view');

        if(!$permission) {
            abort(403);
        }

         $currentMonth = \Carbon\Carbon::now();
            $months = [
                $currentMonth->copy()->subMonth(),
                $currentMonth,
                $currentMonth->copy()->addMonth(),
            ];
            
         return view('roster.employeesrosterapprove', compact('months','currentMonth'));
    }

}
