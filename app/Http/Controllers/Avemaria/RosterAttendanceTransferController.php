<?php

namespace App\Http\Controllers\Avemaria;

use App\Services\AttendancePolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RosterAttendanceTransferController extends Controller
{
    protected $attendancePolicyService;

      public function __construct(AttendancePolicyService $attendancePolicyService)
    {
        $this->middleware('auth');

        $this->attendancePolicyService = $attendancePolicyService;
    }

    
    public function index(){
        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
            abort(403);
        }
         $shifts = DB::table('shift_types')
                    ->where('deleted',0)
                    ->get();

      return view('Avamaria.shift_attendance', compact('shifts'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
            abort(403);
        }

        $row = DB::table('attendance_unmatch as au')
            ->leftJoin('employees as e', 'e.emp_id', '=', 'au.emp_id')
            ->where('au.id', $id)
            ->select(
                'au.id', 'au.emp_id', 'au.date', 'au.roster_shift_id',
                'au.on_timestamp', 'au.off_timestamp',
                'e.emp_name_with_initial', 'e.calling_name'
            )
            ->first();

        if (!$row) {
            return response()->json(['errors' => 'Record not found'], 404);
        }

        $row->date = $row->date ? date('Y-m-d', strtotime($row->date)) : '';
        $row->on_timestamp = $row->on_timestamp ? date('Y-m-d\TH:i', strtotime($row->on_timestamp)) : '';
        $row->off_timestamp = $row->off_timestamp ? date('Y-m-d\TH:i', strtotime($row->off_timestamp)) : '';

        return response()->json(['result' => $row]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'hidden_id'  => 'required|integer',
            'shift'      => 'nullable|integer|exists:shift_types,id',
            'in_time_s'  => 'required|date_format:Y-m-d\TH:i',
            'out_time_s' => 'required|date_format:Y-m-d\TH:i|after:in_time_s',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $id = $request->input('hidden_id');
        $row = DB::table('attendance_unmatch')->where('id', $id)->first();

        if (!$row) {
            return response()->json(['errors' => ['Record not found']]);
        }

        $shiftId  = $request->input('shift') ?: null;
        $onTime   = date('Y-m-d H:i:s', strtotime($request->input('in_time_s')));
        $offTime  = date('Y-m-d H:i:s', strtotime($request->input('out_time_s')));
        $workDate = date('Y-m-d', strtotime($row->date));
        $now      = date('Y-m-d H:i:s');

        DB::transaction(function () use ($id, $row, $shiftId, $onTime, $offTime, $workDate, $now) {

            DB::table('attendance_unmatch')->where('id', $id)->update([
                'roster_shift_id' => $shiftId,
                'on_timestamp'    => $onTime,
                'off_timestamp'   => $offTime,
                'status' => 2,
                'approve_status'   => 1,
                'updated_at'      => $now,
            ]);

            if ($shiftId) {
                $rosters = DB::table('employee_roster_details')
                    ->where('emp_id', $row->emp_id)
                    ->whereDate('work_date', $workDate)
                    ->lockForUpdate()
                    ->get();

                foreach ($rosters as $roster) {
                    if ($roster->shift_id == $shiftId) {
                        continue;
                    }

                    DB::table('roster_shift_log')->insert([
                        'emp_id'       => $roster->emp_id,
                        'work_date'    => $roster->work_date,
                        'old_shift_id' => $roster->shift_id,
                        'new_shift_id' => $shiftId,
                        'changed_by'   => Auth::id(),
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);

                    DB::table('employee_roster_details')->where('id', $roster->id)->update([
                        'shift_id'   => $shiftId,
                        'updated_at' => $now,
                    ]);
                }
            }


            $onId = $this->saveAttendance($row->emp_id, $onTime, $workDate, $now);

            $hasLateRecord = DB::table('employee_late_attendances')
                ->where('emp_id', $row->emp_id)
                ->where('date', $workDate)
                ->exists();

            if (!$hasLateRecord) {
                $this->attendancePolicyService->checkAndInsertLateAttendance($row->emp_id,$workDate,$onTime,$onId);
            }

             $this->saveAttendance($row->emp_id, $offTime, $workDate, $now);
        });


        return response()->json(['success' => 'Roster attendance updated successfully']);
    }

    private function saveAttendance($empId, $timestamp, $workDate, $now)
    {
        $existing = DB::table('attendances')
            ->where('emp_id', $empId)
            ->where('timestamp', $timestamp)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return $existing->id;
        }

        $location = DB::table('employees')->where('emp_id', $empId)->value('emp_location');

        return DB::table('attendances')->insertGetId([
            'emp_id'     => $empId,
            'uid'        => $empId,
            'state'      => ' ',
            'timestamp'  => $timestamp,
            'date'       => $workDate,
            'approved'   => 0,
            'type'       => '1',
            'devicesno'  => '-',
            'location'   => $location ?: 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function fetch(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-roster');
        if (!$permission) {
            abort(403);
        }

        $empId = $request->input('employee');
        $date = $request->input('date');

        if (!$empId || !$date) {
            return response()->json(['errors' => ['Employee and date are required']]);
        }

      if ($this->unmatchExists($empId, $date)) {
            return response()->json(['errors' => ['An unmatched attendance record already exists for this date.']]);
        }

        $times = DB::table('attendances')
            ->where('emp_id', $empId)
            ->whereDate('date', $date)
            ->whereNull('deleted_at')
            ->selectRaw('MIN(`timestamp`) as on_time, MAX(`timestamp`) as off_time')
            ->first();

        $roster = DB::table('employee_roster_details')
            ->where('emp_id', $empId)
            ->whereDate('work_date', $date)
            ->orderBy('id', 'desc')
            ->first();

        $on = $times->on_time;
        $off = $times->off_time != $times->on_time ? $times->off_time : null;

        return response()->json(['result' => [
            'shift_id' => $roster ? $roster->shift_id : '',
            'on_time'  => $on ? date('Y-m-d\TH:i', strtotime($on)) : '',
            'off_time' => $off ? date('Y-m-d\TH:i', strtotime($off)) : '',
        ]]);
    }

   public function store(Request $request)
    {
        $user = Auth::user();
            $permission = $user->can('employee-roster');
            if (!$permission) {
                abort(403);
            }

        $validator = Validator::make($request->all(), [
            'employee' => 'required|exists:employees,emp_id',
            'date'     => 'required|date_format:Y-m-d',
            'shift'    => 'nullable|integer|exists:shift_types,id',
            'in_time'  => 'required|date_format:Y-m-d\TH:i',
            'out_time' => 'required|date_format:Y-m-d\TH:i|after:in_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $empId = $request->input('employee');
        $date  = $request->input('date');

        if ($this->unmatchExists($empId, $date)) {
            return response()->json(['errors' => ['An attendance record already exists for this date.']]);
        }

        $shiftId = $request->input('shift') ?: null;
        $onTime  = date('Y-m-d H:i:s', strtotime($request->input('in_time')));
        $offTime = date('Y-m-d H:i:s', strtotime($request->input('out_time')));
        $now     = date('Y-m-d H:i:s');

        DB::transaction(function () use ($empId, $date, $shiftId, $onTime, $offTime, $now) {

            //roster backup 
            if ($shiftId) {
                $rosters = DB::table('employee_roster_details')
                    ->where('emp_id', $empId)
                    ->whereDate('work_date', $date)
                    ->lockForUpdate()
                    ->get();

                foreach ($rosters as $roster) {
                    if ($roster->shift_id == $shiftId) {
                        continue;
                    }

                    DB::table('roster_shift_log')->insert([
                        'emp_id'       => $roster->emp_id,
                        'work_date'    => $roster->work_date,
                        'old_shift_id' => $roster->shift_id,
                        'new_shift_id' => $shiftId,
                        'changed_by'   => Auth::id(),
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);

                    DB::table('employee_roster_details')->where('id', $roster->id)->update([
                        'shift_id'   => $shiftId,
                        'updated_at' => $now,
                    ]);
                }
            }

            // 3. attendances backup update / insert
            $records = DB::table('attendances')
                ->where('emp_id', $empId)
                ->whereDate('date', $date)
                ->whereNull('deleted_at')
                ->orderBy('timestamp')
                ->lockForUpdate()
                ->get();

            $first = $records->first();
            $last  = $records->count() > 1 ? $records->last() : null;

            // punch ekak witharak nam eka off time ekata langa nam off widiyata ganna
            if ($records->count() == 1
                && abs(strtotime($first->timestamp) - strtotime($offTime)) < abs(strtotime($first->timestamp) - strtotime($onTime))) {
                $last  = $first;
                $first = null;
            }

            $location = DB::table('employees')->where('emp_id', $empId)->value('emp_location');

            $onId = null;

            foreach ([[$first, $onTime, true], [$last, $offTime, false]] as $item) {
                list($record, $time, $isOn) = $item;
                $oldValue = $record ? $record->timestamp : null;

                if ($oldValue && date('Y-m-d H:i', strtotime($oldValue)) == date('Y-m-d H:i', strtotime($time))) {
                        $time = $oldValue;
                    }

                if ($record) {
                    $attendanceId = $record->id;

                    if (strtotime($oldValue) != strtotime($time)) {
                        DB::table('attendances')->where('id', $attendanceId)->update([
                            'timestamp'  => $time,
                            'updated_at' => $now,
                        ]);
                    }
                } else {
                    $attendanceId = DB::table('attendances')->insertGetId([
                        'emp_id'     => $empId,
                        'uid'        => $empId,
                        'state'      => 1,
                        'timestamp'  => $time,
                        'date'       => $date,
                        'approved'   => 0,
                        'type'       => 255,
                        'devicesno'  => 0,
                        'location'   => $location ?: 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // time eka wenas unoth hari aluthen damma nam hari log karanna
                if (!$oldValue || strtotime($oldValue) != strtotime($time)) {
                    DB::table('attendance_edits')->insert([
                        'attendance_id'  => $attendanceId,
                        'emp_id'         => $empId,
                        'date'           => $date,
                        'prev_val'       => $oldValue,
                        'new_val'        => $time,
                        'edited_user_id' => Auth::id(),
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }

                if ($isOn) {
                    $onId = $attendanceId;
                    $onTime = $time;
                }
            }

            // 4. late records delete karala on time eken aye calculate
            DB::table('employee_late_attendance_minites')
                ->where('emp_id', $empId)
                ->whereDate('attendance_date', $date)
                ->delete();

            DB::table('employee_late_attendances')
                ->where('emp_id', $empId)
                ->whereDate('date', $date)
                ->delete();

            $this->attendancePolicyService->checkAndInsertLateAttendance($empId, $date, $onTime, $onId);
        });

        return response()->json(['success' => 'Record added successfully']);
    }

    private function unmatchExists($empId, $date)
    {
        return DB::table('attendance_unmatch')
            ->where('emp_id', $empId)
            ->whereDate('date', $date)
            ->exists();
    }

}
