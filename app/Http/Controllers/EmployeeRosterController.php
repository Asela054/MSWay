<?php

namespace App\Http\Controllers;

use App\EmployeeRoster;
use App\EmployeeRosterDetails;
use App\Employee;
use App\ShiftType;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

     
}
