<?php

namespace App\Http\Controllers;

use App\EmployeeRosterDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ShiftChangeLog;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
   
    // public function fullrosterstore(Request $request)
    // {
    //     $user = Auth::user();
    //     $permission = $user->can('employee-roster');
    //     if (!$permission) {
    //          return response()->json(['error' => 'UnAuthorized']);
    //     }

    //      $shifts = $request->json('shifts');

    //     // Group incoming shifts by emp_id + date
    //         $grouped = [];
    //         foreach ($shifts as $roster) {
    //             $key = $roster['emp_id'] . '_' . $roster['date'];

    //             if (!isset($grouped[$key])) {
    //                 $grouped[$key] = [
    //                     'emp_id'    => $roster['emp_id'],
    //                     'date'      => $roster['date'],
    //                     'shift_ids' => []
    //                 ];
    //             }

    //             // Only add real shift IDs, skip null sentinels
    //             if (!is_null($roster['shift'])) {
    //                 $grouped[$key]['shift_ids'][] = $roster['shift'];
    //             }
    //         }


    //      foreach ($grouped as $item) {
    //         $empId  = $item['emp_id'];
    //         $date   = $item['date'];
    //         $newIds = $item['shift_ids'];

    //         // Get all existing records for this emp + date
    //         $existingRecords = EmployeeRosterDetails::where('emp_id', $empId)
    //             ->where('work_date', $date)
    //             ->get();

    //         $existingIds = array_map('strval', $existingRecords->pluck('shift_id')->toArray());
    //         $newIdsStr   = array_map('strval', $newIds);

    //         // Log + delete shifts that were removed
    //         $toDelete = array_diff($existingIds, $newIdsStr);
    //         foreach ($toDelete as $oldShiftId) {
    //             ShiftChangeLog::create([
    //                 'emp_id'       => $empId,
    //                 'work_date'    => $date,
    //                 'old_shift_id' => $oldShiftId,
    //                 'new_shift_id' => null,
    //                 'changed_by'   => Auth::id() ?? 1,
    //             ]);
    //            EmployeeRosterDetails::where('emp_id', $empId)
    //                                 ->where('work_date', $date)
    //                                 ->delete();
    //         }

    //         // Insert newly added shifts
    //         $toAdd = array_diff($newIdsStr, $existingIds);
    //         foreach ($toAdd as $newShiftId) {
    //             ShiftChangeLog::create([
    //                 'emp_id'       => $empId,
    //                 'work_date'    => $date,
    //                 'old_shift_id' => null,
    //                 'new_shift_id' => $newShiftId,
    //                 'changed_by'   => Auth::id() ?? 1,
    //             ]);
    //             EmployeeRosterDetails::create([
    //                 'emp_id'    => $empId,
    //                 'work_date' => $date,
    //                 'shift_id'  => $newShiftId,
    //             ]);
    //         }
    //     }
    //      return response()->json(['success' => 'Roster Inserted Successfully!']);
    // }

    public function fullrosterstore(Request $request) {
        $user = Auth::user();
        if (!$user->can('employee-roster')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $rosterData = json_decode($request->get('roster_data'), true);

        if (empty($rosterData)) {
            return response()->json(['success' => false, 'message' => 'No data received']);
        }

        try {
            $now = Carbon::now();
            $changedBy = Auth::id();

            // Group new shifts by emp_id + work_date
            // $newShiftMap[emp_id][work_date] = [shift_id, shift_id, ...]
            $newShiftMap = [];
            foreach ($rosterData as $row) {
                $newShiftMap[$row['emp_id']][$row['work_date']][] = $row['shift_id'];
            }

            // Get all affected emp_ids and dates
            $empIds    = array_keys($newShiftMap);
            $allDates  = [];
            foreach ($newShiftMap as $empId => $dates) {
                $allDates = array_merge($allDates, array_keys($dates));
            }
            $allDates = array_unique($allDates);

            // Fetch existing roster records for affected emp+date combinations
            $existingRecords = DB::table('employee_roster_details')
                ->whereIn('emp_id', $empIds)
                ->whereIn('work_date', $allDates)
                ->get();

            // Build old shift map
            // $oldShiftMap[emp_id][work_date] = [shift_id, shift_id, ...]
            $oldShiftMap = [];
            foreach ($existingRecords as $record) {
                $workDate = Carbon::parse($record->work_date)->toDateString();
                $oldShiftMap[$record->emp_id][$workDate][] = $record->shift_id;
            }

            // Build log entries — compare old vs new per emp+date
            $logData = [];
            foreach ($newShiftMap as $empId => $dates) {
                foreach ($dates as $workDate => $newShiftIds) {
                    $oldShiftIds = array_values($oldShiftMap[$empId][$workDate] ?? []);
                    $newShiftIds = array_values($newShiftIds);

                    $maxCount = max(count($oldShiftIds), count($newShiftIds));

                    for ($i = 0; $i < $maxCount; $i++) {
                        $oldShiftId = $oldShiftIds[$i] ?? null;
                        $newShiftId = $newShiftIds[$i] ?? null;

                        $logData[] = [
                            'emp_id'       => $empId,
                            'work_date'    => $workDate,
                            'old_shift_id' => $oldShiftId,
                            'new_shift_id' => $newShiftId,
                            'changed_by'   => $changedBy,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ];
                    }
                }
            }

            // Delete existing records for affected emp+date
            foreach ($newShiftMap as $empId => $dates) {
                DB::table('employee_roster_details')
                    ->where('emp_id', $empId)
                    ->whereIn('work_date', array_keys($dates))
                    ->delete();
            }

            // Bulk insert new roster
            $insertData = [];
            foreach ($rosterData as $row) {
                $insertData[] = [
                    'shift_id'          => $row['shift_id'],
                    'emp_id'            => $row['emp_id'],
                    'work_date'         => $row['work_date'],
                    'scheduling_status' => null,
                    'remark'            => null,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }

            DB::table('employee_roster_details')->insert($insertData);

            // Bulk insert log (only if there are changes)
            if (!empty($logData)) {
                DB::table('roster_shift_log')->insert($logData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Roster saved successfully.',
                'count'   => count($insertData),
                'logs'    => count($logData)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
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


    public function colnerosterstore(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster-view');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized']);
        }

        $departmentId = $request->get('department_id');
        $month = $request->get('Month');

        if (!$departmentId || !$month) {
            return response()->json(['error' => 'Missing department_id or month'], 400);
        }

        // Current month date range
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        // Next month date range
        $nextMonthStart = date('Y-m-01', strtotime('+1 month', strtotime($startDate)));
        $nextMonthEnd   = date('Y-m-t', strtotime($nextMonthStart));

        // Fetch all roster records for the given month + department
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
            return response()->json(['message' => 'No roster records found for the given month and department'], 404);
        }

        $newRecords   = [];
        $skippedDates = [];
        $current_date_time    = Carbon::now()->toDateTimeString();;

        foreach ($rosters as $roster) {
            // Calculate the day offset within the month
            $originalDay = date('d', strtotime($roster->work_date));
            $newWorkDate = date('Y-m-', strtotime($nextMonthStart)) . $originalDay;

            // Skip if this day doesn't exist in the next month (e.g., Jan 31 → Feb has no 31)
            if ($newWorkDate > $nextMonthEnd) {
                $skippedDates[] = $roster->work_date;
                continue;
            }


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

        return response()->json(['success' => 'Roster cloned successfully to next month']);
    }


    public function approverosterstore(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster-view');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized']);
        }
        
        $departmentId = $request->get('department_id');
        $month = $request->get('Month');
        $maxwork_days = $request->get('max_work_days');
        if (!$departmentId || !$month) {
            return response()->json(['error' => 'Missing department_id or month'], 400);
        }
        
        // Current month date range
        $monthDate = Carbon::createFromFormat('Y-m', $month);
        $startDate = $monthDate->startOfMonth()->toDateString();
        $endDate = $monthDate->endOfMonth()->toDateString();
        
        // Fetch all roster records for the given month + department
        $rosters = DB::table('employee_roster_details')
            ->select('employee_roster_details.emp_id')
            ->leftJoin('employees', 'employee_roster_details.emp_id', '=', 'employees.emp_id')
            ->whereBetween('employee_roster_details.work_date', [$startDate, $endDate])
            ->where('employees.emp_department', $departmentId)
            ->distinct()
            ->get();
        
        if ($rosters->isEmpty()) {
            return response()->json(['message' => 'No roster records found for the given month and department'], 404);
        }
        
        $newRecords = [];
        $current_date_time = Carbon::now()->toDateTimeString();
        
        // Get all shift types
        $shifts = DB::table('shift_types')->select('id', 'shift_code', 'shift_name')->get();
        
        foreach ($rosters as $roster) {
            $empId = $roster->emp_id;
            
            // Loop through each shift type
            foreach ($shifts as $shift) {
                // Count employee dates for this shift in the given date range
                $count = DB::table('employee_roster_details')
                    ->where('emp_id', $empId)
                    ->where('shift_id', $shift->id)
                    ->whereBetween('work_date', [$startDate, $endDate])
                    ->count();
                
                if ($count > 0) {
                    // Check if record already exists
                    $existingRecord = DB::table('employee_roster_approve')
                        ->where('emp_id', $empId)
                        ->where('shift_id', $shift->id)
                        ->where('month', $startDate)
                        ->first();
                    
                    if ($existingRecord) {
                        // Update existing record
                        DB::table('employee_roster_approve')
                            ->where('id', $existingRecord->id)
                            ->update([
                                'max_work_days' => $maxwork_days,
                                'count' => $count,
                                'updated_at' => $current_date_time
                            ]);
                    } else {
                        // Create new record
                        $newRecords[] = [
                            'emp_id' => $empId,
                            'month' => $startDate,
                            'shift_id' => $shift->id,
                            'max_work_days' => $maxwork_days,
                            'count' => $count,
                            'created_at' => $current_date_time,
                            'updated_at' => $current_date_time
                        ];
                    }
                }
            }
        }
        
        if (!empty($newRecords)) {
            DB::table('employee_roster_approve')->insert($newRecords);
        }
        
        return response()->json(['success' => 'Roster approved successfully']);
    }
}
