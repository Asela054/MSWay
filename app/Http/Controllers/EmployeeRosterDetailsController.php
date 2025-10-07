<?php

namespace App\Http\Controllers;

use App\EmployeeRosterDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeRosterDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function fullrosterstore(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('employee-roster');

        if(!$permission) {
            abort(403);
        }

        foreach ($request->shifts as $roster) {
            $existing = EmployeeRosterDetails::where('emp_id', $roster['emp_id'])
                ->where('work_date', $roster['date'])
                ->first();

            

            $newShiftId = $roster['shift'];

            if ($existing) {
                if ($existing->shift_id != $newShiftId) {
                    // Log change
                
                    ShiftChangeLog::create([
                        'emp_id' => $roster['emp_id'],
                        'work_date' => $roster['date'],
                        'old_shift_id' => $existing->shift_id,
                        'new_shift_id' => $newShiftId,
                        'changed_by' => Auth::id() ?? 1, // fallback if no auth
                    ]);
                    // Update shift
                    $existing->shift_id = $newShiftId;
                    $existing->save();
                    // return response()->json(['message' => 'Roster updated and changes logged.']);
                     return response()->json(['success' => 'Roster updated and changes logged.']);
                }
            } else {
                // Create new roster record
                EmployeeRosterDetails::create([
                    'emp_id' => $roster['emp_id'],
                    'work_date' => $roster['date'],
                    'shift_id' => $newShiftId,
                ]);

            }
        }

        // return response()->json(['message' => 'Roster Inserted Successfully!.']);
         return response()->json(['success' => 'Roster Inserted Successfully!']);
    }

    public function getViewRosterData(Request $request)
    {
        $departmentId = $request->get('department_id');
        $month = $request->get('month'); // format: YYYY-MM

        if (!$departmentId || !$month) {
            return response()->json(['error' => 'Missing department_id or month'], 400);
        }
      
        $startDate = $month . '-01';
        $endDate = date("Y-m-t", strtotime($startDate)); // Get last date of the month

       $rosters = EmployeeRosterDetails::whereBetween('work_date', [$startDate, $endDate])
            ->whereIn('emp_id', function ($query) use ($departmentId) {
                $query->select('emp_id')
                    ->from('employees') // 🔁 Replace with your actual employee table name if different
                    ->where('emp_department', $departmentId);
            })
            ->get()
            ->groupBy('emp_id')
            ->map(function ($records) {
                return $records->keyBy(function ($item) {
                    return date('j', strtotime($item->work_date)); // Day number (1–31)
                })->map(function ($item) {
                    return $item->shift_id; // or $item->shift_code if your shift model uses codes
                });
            });

 

        return response()->json($rosters);
    }
}
