<?php

namespace App\Http\Controllers;

use App\EmployeeRosterDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ShiftChangeLog;
use Auth;

class EmployeeRosterDetailsController extends Controller
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
   
    public function fullrosterstore(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        // Group incoming shifts by emp_id + date
        // payload: [{ emp_id, shift, date }, { emp_id, shift, date }, ...]
        $grouped = [];
        foreach ($request->shifts as $roster) {
            $key = $roster['emp_id'] . '_' . $roster['date'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'emp_id'    => $roster['emp_id'],
                    'date'      => $roster['date'],
                    'shift_ids' => []
                ];
            }
            $grouped[$key]['shift_ids'][] = $roster['shift'];
        }


         foreach ($grouped as $item) {
            $empId  = $item['emp_id'];
            $date   = $item['date'];
            $newIds = $item['shift_ids'];

            // Get all existing records for this emp + date
            $existingRecords = EmployeeRosterDetails::where('emp_id', $empId)
                ->where('work_date', $date)
                ->get();

            $existingIds = array_map('strval', $existingRecords->pluck('shift_id')->toArray());
            $newIdsStr   = array_map('strval', $newIds);

            // Log + delete shifts that were removed
            $toDelete = array_diff($existingIds, $newIdsStr);
            foreach ($toDelete as $oldShiftId) {
                ShiftChangeLog::create([
                    'emp_id'       => $empId,
                    'work_date'    => $date,
                    'old_shift_id' => $oldShiftId,
                    'new_shift_id' => null,
                    'changed_by'   => Auth::id() ?? 1,
                ]);
                EmployeeRosterDetails::where('emp_id', $empId)
                    ->where('work_date', $date)
                    ->where('shift_id', $oldShiftId)
                    ->delete();
            }

            // Insert newly added shifts
            $toAdd = array_diff($newIdsStr, $existingIds);
            foreach ($toAdd as $newShiftId) {
                ShiftChangeLog::create([
                    'emp_id'       => $empId,
                    'work_date'    => $date,
                    'old_shift_id' => null,
                    'new_shift_id' => $newShiftId,
                    'changed_by'   => Auth::id() ?? 1,
                ]);
                EmployeeRosterDetails::create([
                    'emp_id'    => $empId,
                    'work_date' => $date,
                    'shift_id'  => $newShiftId,
                ]);
            }
            // Shifts in both old and new → unchanged, skip
        }
         return response()->json(['success' => 'Roster Inserted Successfully!']);
    }

    public function getViewRosterData(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster-view');
        if (!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId = $request->get('department_id');
        $month = $request->get('month'); 

        if (!$departmentId || !$month) {
            return response()->json(['error' => 'Missing department_id or month'], 400);
        }
      
        $startDate = $month . '-01';
        $endDate = date("Y-m-t", strtotime($startDate)); // Get last date of the month

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
                    return date('j', strtotime($item->work_date));
                })->map(function ($dayRecords) {
                    return $dayRecords->pluck('shift_id')->toArray();
                });
            });

 

        return response()->json($rosters);
    }
}
