<?php

namespace App\Services;

use App\Attendance as AppAttendance;
use App\Models\Attendance;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendancePolicyService
{
    // Max allowed gap for a "seamless" shift transition (minutes).
    // e.g. night shift off at 8am, day shift on at 9am should still count as "seamless".
    private $seamlessTransitionGraceMinutes = 360;

    public function attendanceInsertcsv_txt($full_emp_id, $date_input, $timestamp, $date)
    {
        $empshift = DB::table('employees')
            ->select('emp_id', 'emp_shift', 'emp_location')
            ->where('emp_id', $full_emp_id)
            ->first();

        if (is_null($empshift)) {
            return false;
        }

        $employeeLocation = $empshift->emp_location;

        $emprosterinfo = DB::table('employee_roster_details')
            ->select('emp_id', 'shift_id')
            ->where('emp_id', $full_emp_id)
            ->where('work_date', $date_input)
            ->first();

        if ($emprosterinfo) {
            $empshiftid = $emprosterinfo->shift_id;
        } else {
            $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');
            $emprosterinfo = DB::table('employee_roster_details')
                ->select('emp_id', 'shift_id')
                ->where('emp_id', $full_emp_id)
                ->where('work_date', $previous_day)
                ->first();

            if ($emprosterinfo) {
                $empshiftid = $emprosterinfo->shift_id;
            } else {
                $empshiftid = $empshift->emp_shift;
            }
        }

        $shift = DB::table('shift_types')
            ->where('id', $empshiftid)
            ->first();

        $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');
        $employeeshiftdetails = DB::table('employeeshiftdetails')
            ->where('date_from', $previousDate)
            ->where('emp_id', $full_emp_id)
            ->first();

        $period = (new DateTime($timestamp))->format('A');
        $timestamp = $date_input . ' ' . $timestamp;
        $attendance_date = null;

        // ============================================================
        // Branch 1: off_next_day = 0, on_next_day = 1
        // (left untouched as requested - kept as-is)
        // ============================================================
        if ($shift && $shift->off_next_day == '0' && $shift->on_next_day == '1' && $date == $date_input) {
            $next_day = (new DateTime($date_input))->modify('+1 day')->format('Y-m-d');
            $shif_ontime = Carbon::parse($date . ' ' . $shift->onduty_time);
            $attendance_time = Carbon::parse($timestamp);

            if ($shif_ontime->format('H:i:s') > $attendance_time->format('H:i:s')) {
                $attendance_date = ($period === 'AM') ? substr($timestamp, 0, 10) : $next_day;
            } else {
                $attendance_date = $next_day;
            }

        // ============================================================
        // Branch 2: off_next_day = 1, on_next_day = 0
        // FIX: Instead of the AM/PM heuristic, this checks the timestamp
        // against two actual shift windows (previous day's overflow
        // window + current day's fresh window) to decide the date.
        // This fixes the issue where a night shift's tail (checkout)
        // was wrongly attributed to a different shift starting the
        // same day.
        // ============================================================
        } elseif ($shift && $shift->off_next_day == '1' && $shift->on_next_day == '0' && $date == $date_input) {

            $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');
            $next_day = (new DateTime($date_input))->modify('+1 day')->format('Y-m-d');

            // Fetch previous day's roster shift too (needed for window comparison)
            $prevRosterInfo = DB::table('employee_roster_details')
                ->select('emp_id', 'shift_id')
                ->where('emp_id', $full_emp_id)
                ->where('work_date', $previous_day)
                ->first();
            $prevShift = $prevRosterInfo
                ? DB::table('shift_types')->where('id', $prevRosterInfo->shift_id)->first()
                : $shift; // fallback to the same shift if no roster found

            $ts = Carbon::parse($timestamp);
            $matched = false;

            // (a) Previous day's shift overflow window
            //     e.g. prev_day onduty_time -> offduty_time (+ buffer)
            //     offduty_time falls on date_input ONLY if prevShift itself
            //     crosses midnight (off_next_day = 1). Otherwise it falls
            //     on previous_day itself (e.g. a normal day shift).
            if ($prevShift && $prevShift->onduty_time && $prevShift->offduty_time) {
                $prevOffDate = ($prevShift->off_next_day == '1') ? $date_input : $previous_day;

                $prevWindowStart = Carbon::parse($previous_day . ' ' . $prevShift->onduty_time)->subMinutes(60);
                $prevWindowEnd   = Carbon::parse($prevOffDate . ' ' . $prevShift->offduty_time)->addMinutes(60);

                if ($ts->between($prevWindowStart, $prevWindowEnd)) {
                    $attendance_date = $previous_day;
                    $matched = true;
                }
            }

            // (b) Current day's fresh shift window
            //     e.g. date_input onduty_time -> next_day offduty_time (+ buffer)
            if (!$matched && $shift && $shift->onduty_time && $shift->offduty_time) {
                $currWindowStart = Carbon::parse($date_input . ' ' . $shift->onduty_time)->subMinutes(60);
                $currWindowEnd   = Carbon::parse($next_day . ' ' . $shift->offduty_time)->addMinutes(60);

                if ($ts->between($currWindowStart, $currWindowEnd)) {
                    $attendance_date = $date_input;
                    $matched = true;
                }
            }

            // (c) If it doesn't fall in either window, fall back to the original AM/PM logic
            if (!$matched) {
                $shif_ontime = Carbon::parse($date . ' ' . $shift->onduty_time);
                $attendance_time = Carbon::parse($timestamp);

                if ($shif_ontime->format('H:i:s') > $attendance_time->format('H:i:s')) {
                    $attendance_date = ($period === 'AM') ? $previous_day : substr($timestamp, 0, 10);
                } else {
                    $attendance_date = substr($timestamp, 0, 10);
                }
            }

        } else if ($date == $date_input) {
            if ($employeeshiftdetails) {
                $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');
                $attendance_date = ($period === 'AM') ? $previous_day : substr($timestamp, 0, 10);
            } else {
                $attendance_date = substr($timestamp, 0, 10);
            }
        }

        if ($date == $date_input) {
            $Attendance = AppAttendance::firstOrNew(['timestamp' => $timestamp, 'emp_id' => $full_emp_id]);
            $Attendance->uid = $full_emp_id;
            $Attendance->emp_id = $full_emp_id;
            $Attendance->timestamp = $timestamp;
            $Attendance->date = $attendance_date;
            $Attendance->location = $employeeLocation;
            $Attendance->save();

            $insertId = $Attendance->id;

            // Seamless shift transition check - if the previous shift's off time
            // matches the current shift's on time within the grace period,
            // insert virtual checkout/checkin records at that boundary since
            // there's no physical punch there.
            $this->handleSeamlessShiftTransition($full_emp_id, $date_input, $employeeLocation);

            return $this->checkAndInsertLateAttendance($full_emp_id, $attendance_date, $timestamp, $insertId);
        }
        return true;
    }

    /**
     * For some employees, the previous day's shift off time coincides with
     * the current day's shift on time (within a grace period) - meaning no
     * physical punch is recorded at that transition point (e.g. night shift
     * ends at 8am and the day shift starts at that exact same time). This
     * generates a virtual checkout (previous shift) / checkin (current shift)
     * record pair at that boundary, since without a physical timestamp there
     * the system could misread both shifts as a single continuous session.
     */
    private function handleSeamlessShiftTransition($full_emp_id, $date_input, $employeeLocation = null)
    {
        $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');

        $prevRoster = DB::table('employee_roster_details')
            ->where('emp_id', $full_emp_id)
            ->where('work_date', $previous_day)
            ->first();

        $currRoster = DB::table('employee_roster_details')
            ->where('emp_id', $full_emp_id)
            ->where('work_date', $date_input)
            ->first();

        if (!$prevRoster || !$currRoster) {
            return; // doesn't apply if either roster is missing
        }

        $prevShift = DB::table('shift_types')->where('id', $prevRoster->shift_id)->first();
        $currShift = DB::table('shift_types')->where('id', $currRoster->shift_id)->first();

        if (!$prevShift || !$currShift || !$prevShift->offduty_time || !$currShift->onduty_time) {
            return;
        }

        // Previous shift's off time - resolve to the actual date
        // (if off_next_day = 1, the off time falls on date_input; otherwise on previous_day)
        $prevOffDate = ($prevShift->off_next_day == '1') ? $date_input : $previous_day;
        $prevOffTimestamp = Carbon::parse($prevOffDate . ' ' . $prevShift->offduty_time);

        // Current shift's on time
        $currOnTimestamp = Carbon::parse($date_input . ' ' . $currShift->onduty_time);

        // Diff in minutes - curr on time must come after prev off time (>= 0)
        $diffMinutes = $prevOffTimestamp->diffInMinutes($currOnTimestamp, false);

        // If negative (curr on time is before prev off time), or outside the grace period - doesn't apply
        if ($diffMinutes < 0 || $diffMinutes > $this->seamlessTransitionGraceMinutes) {
            return;
        }

        // Checkin - exactly at the current shift's on time
        $checkinTimestamp = $currOnTimestamp->format('Y-m-d H:i:s');

        // Checkout - one minute before checkin (so the order stays correct)
        $checkoutTimestamp = $currOnTimestamp->copy()->subMinute()->format('Y-m-d H:i:s');

        // Check if a physical punch already exists near this boundary
        $exists = AppAttendance::where('emp_id', $full_emp_id)
            ->whereIn('timestamp', [$checkoutTimestamp, $checkinTimestamp])
            ->exists();

        if ($exists) {
            return; // physical punches already exist near here
        }

        // 1. Previous shift's checkout (attributed to previous_day)
        $checkoutRecord = new AppAttendance();
        $checkoutRecord->uid = $full_emp_id;
        $checkoutRecord->emp_id = $full_emp_id;
        $checkoutRecord->timestamp = $checkoutTimestamp;
        $checkoutRecord->date = $previous_day;
        $checkoutRecord->location = $employeeLocation;
        $checkoutRecord->save();

        // 2. Current shift's checkin (attributed to date_input)
        $checkinRecord = new AppAttendance();
        $checkinRecord->uid = $full_emp_id;
        $checkinRecord->emp_id = $full_emp_id;
        $checkinRecord->timestamp = $checkinTimestamp;
        $checkinRecord->date = $date_input;
        $checkinRecord->location = $employeeLocation;
        $checkinRecord->save();
    }

    public function attendanceInsertsingle_dep($empid, $attendacetimestamp, $location, $attendacedate)
    {
        $datetime_parts = explode('T', $attendacetimestamp);

        $timestampdate = $datetime_parts[0];
        $time_part = $datetime_parts[1];

        $time_parts = explode(':', $time_part);
        $time_h = $time_parts[0] ?? '00';
        $time_m = $time_parts[1] ?? '00';
        $time_s = '00';

        $date_stamp = $timestampdate;

        $empshift = DB::table('employees')
            ->select('emp_id', 'emp_shift')
            ->where('emp_id', $empid)
            ->first();

        if (is_null($empshift)) {
            return false;
        }

        $emprosterinfo = DB::table('employee_roster_details')
            ->select('emp_id', 'shift_id')
            ->where('emp_id', $empid)
            ->where('work_date', $attendacedate)
            ->first();

        if ($emprosterinfo) {
            $empshiftid = $emprosterinfo->shift_id;
        } else {
            $empshiftid = $empshift->emp_shift;
        }

        $shift = DB::table('shift_types')
            ->where('id', $empshiftid)
            ->first();

        $previousDate = Carbon::parse($date_stamp)->subDay()->format('Y-m-d');
        $employeeshiftdetails = DB::table('employeeshiftdetails')
            ->where('date_from', $previousDate)
            ->where('emp_id', $empid)
            ->first();

        $time_string = $time_h . ':' . $time_m . ':' . $time_s;
        $period = (new DateTime($time_string))->format('A');
        $final_timestamp = null;
        $attendance_date = null;

        if ($shift && $shift->off_next_day == '0' && $shift->on_next_day == '1' && $date == $date_input) {
            $next_day = (new DateTime($date_input))->modify('+1 day')->format('Y-m-d');
            $shif_ontime = Carbon::parse($attendacedate . ' ' . $shift->onduty_time);
            $txt_datetime = Carbon::parse($time_h . ':' . $time_m . ':00');

            if ($shif_ontime->format('H:i:s') > $txt_datetime->format('H:i:s')) {
                $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
                $attendance_date = ($period === 'AM') ? substr($final_timestamp, 0, 10) : $next_day;
            } else {
                $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
                $attendance_date = $next_day;
            }

        // ============================================================
        // off_next_day = 1, on_next_day = 0
        // FIX: same fix as in attendanceInsertcsv_txt - instead of the
        // AM/PM heuristic, checks the timestamp against the previous
        // day's and current day's shift windows to decide the
        // attendance_date.
        // ============================================================
        } elseif ($shift && $shift->off_next_day == '1' && $shift->on_next_day == '0' && $date_stamp == $attendacedate) {
            $previous_day = (new DateTime($attendacedate))->modify('-1 day')->format('Y-m-d');
            $next_day = (new DateTime($attendacedate))->modify('+1 day')->format('Y-m-d');

            $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';

            // Fetch previous day's roster shift too (needed for window comparison)
            $prevRosterInfo = DB::table('employee_roster_details')
                ->select('emp_id', 'shift_id')
                ->where('emp_id', $empid)
                ->where('work_date', $previous_day)
                ->first();
            $prevShift = $prevRosterInfo
                ? DB::table('shift_types')->where('id', $prevRosterInfo->shift_id)->first()
                : $shift; // fallback to the same shift if no roster found

            $ts = Carbon::parse($final_timestamp);
            $matched = false;

            // (a) Previous day's shift overflow window
            //     offduty_time falls on attendacedate ONLY if prevShift
            //     itself crosses midnight (off_next_day = 1). Otherwise
            //     it falls on previous_day itself.
            if ($prevShift && $prevShift->onduty_time && $prevShift->offduty_time) {
                $prevOffDate = ($prevShift->off_next_day == '1') ? $attendacedate : $previous_day;

                $prevWindowStart = Carbon::parse($previous_day . ' ' . $prevShift->onduty_time)->subMinutes(60);
                $prevWindowEnd   = Carbon::parse($prevOffDate . ' ' . $prevShift->offduty_time)->addMinutes(60);

                if ($ts->between($prevWindowStart, $prevWindowEnd)) {
                    $attendance_date = $previous_day;
                    $matched = true;
                }
            }

            // (b) Current day's fresh shift window
            if (!$matched && $shift && $shift->onduty_time && $shift->offduty_time) {
                $currWindowStart = Carbon::parse($attendacedate . ' ' . $shift->onduty_time)->subMinutes(60);
                $currWindowEnd   = Carbon::parse($next_day . ' ' . $shift->offduty_time)->addMinutes(60);

                if ($ts->between($currWindowStart, $currWindowEnd)) {
                    $attendance_date = $attendacedate;
                    $matched = true;
                }
            }

            // (c) If it doesn't fall in either window, fall back to the original AM/PM logic
            if (!$matched) {
                $shif_ontime = Carbon::parse($attendacedate . ' ' . $shift->onduty_time);
                $txt_datetime = Carbon::parse($time_h . ':' . $time_m . ':00');

                if ($shif_ontime > $txt_datetime) {
                    $attendance_date = ($period === 'AM') ? $previous_day : substr($final_timestamp, 0, 10);
                } else {
                    $attendance_date = substr($final_timestamp, 0, 10);
                }
            }
        } else if ($date_stamp == $attendacedate) {
            if ($employeeshiftdetails) {
                $previous_day = (new DateTime($attendacedate))->modify('-1 day')->format('Y-m-d');
                $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
                $attendance_date = ($period === 'AM') ? $previous_day : substr($final_timestamp, 0, 10);
            } else {
                $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
                $attendance_date = substr($final_timestamp, 0, 10);
            }
        }

        if ($date_stamp == $attendacedate) {
            $data = array(
                'emp_id' => $empid,
                'uid' => $empid,
                'state' => 1,
                'timestamp' => $final_timestamp ?? $attendacetimestamp,
                'date' => $attendance_date ?? $attendacedate,
                'approved' => 0,
                'type' => 255,
                'devicesno' => 0,
                'location' => $location
            );

            $insertId = DB::table('attendances')->insertGetId($data);

            // Seamless shift transition check - same as attendanceInsertcsv_txt
            $this->handleSeamlessShiftTransition($empid, $attendacedate, $location);

            return $this->checkAndInsertLateAttendance($empid, $attendacedate, $attendacetimestamp, $insertId);

        }
        return true;

    }


    private function checkAndInsertLateAttendance($empId, $date, $firstCheckin, $attendanceId)
    {

   
        $latePolicyService = new LatePolicyService();

        $lateMinutes = 0;
        $isLate = false;

        // Check if a record already exists for this attendance ID
        $existingRecord = DB::table('employee_late_attendances')
            ->where('emp_id', $empId)
            ->where('date', $date)
            ->first();

        if ($existingRecord) {
            $checkInTime = Carbon::parse($existingRecord->check_in_time);
            $checkOutTime = Carbon::parse($firstCheckin);
            $workingHoursDiff = $checkOutTime->diffInSeconds($checkInTime);

            // Format working hours as H:i:s
            $workingHours = gmdate("H:i:s", $workingHoursDiff);

            DB::table('employee_late_attendances')
                ->where('id', $existingRecord->id)
                ->update([
                    'check_out_time' => $firstCheckin,
                    'working_hours' => $workingHours,
                    'updated_by' => Auth::id()
                ]);

            return true;

        } else {
            // Get employee shift information
            $employeeshift = DB::table('employees')
                ->select('emp_id', 'emp_shift')
                ->where('emp_id', $empId)
                ->first();

            if (is_null($employeeshift)) {
                return false;
            }

            // Check if employee has roster for this date
            $rosterInfo = DB::table('employee_roster_details')
                ->select('emp_id', 'shift_id')
                ->where('emp_id', $empId)
                ->where('work_date', $date)
                ->first();

            // Determine shift ID (roster shift if exists, otherwise employee default shift)
            if ($rosterInfo) {
                $shiftId = $rosterInfo->shift_id;
            } else {
                $shiftId = $employeeshift->emp_shift;
            }

            // Get shift on-duty time
            $shiftType = DB::table('shift_types')
                ->select('late_time', 'leave_early_time', 'onduty_time', 'offduty_time', 'saturday_onduty_time', 'saturday_offduty_time')
                ->where('id', $shiftId)
                ->first();

            if (!$shiftType) {
                return true;
            }

            $isSaturday = Carbon::parse($date)->isSaturday();

            if ($isSaturday && $shiftType->saturday_onduty_time && $shiftType->saturday_offduty_time) {

                $onDutyTime = Carbon::parse($date . ' ' . $shiftType->saturday_onduty_time);
                $offDutyTime = Carbon::parse($date . ' ' . $shiftType->saturday_offduty_time);
            } else {
                $onDutyTime = Carbon::parse($date . ' ' . $shiftType->onduty_time);
                $offDutyTime = Carbon::parse($date . ' ' . $shiftType->offduty_time);
            }

            $checkInTime = Carbon::parse($firstCheckin);

                         
            $checkInTime = Carbon::parse($firstCheckin);
            // Determine whether this punch is a check-in or a check-out by measuring how close the punch time is to each boundary.
            // If the punch is closer to off-duty time than on-duty time,
            // it is most likely a check-out punch — so we skip late marking to avoid incorrectly flagging a clock-out as a late arrival.
            $diffFromOnDuty = abs($checkInTime->diffInMinutes($onDutyTime));
            $diffFromOffDuty = abs($checkInTime->diffInMinutes($offDutyTime));

            if ($diffFromOffDuty < $diffFromOnDuty) {
                // This punch is closer to off-duty time → treat as check-out, not check-in Skip late attendance evaluation to prevent false late records
                return true;
            }


            if ($shiftType->late_time) {

                $ondutylateTime = new DateTime($date . ' ' . $shiftType->late_time);
                $checkInTime = new DateTime($firstCheckin);

                // Check if check-in time is after on-duty time
                if ($checkInTime > $ondutylateTime) {
                    $isLate = true;

                    $interval = $checkInTime->diff($ondutylateTime);
                    $elapsedMinutes = ($interval->h * 60) + $interval->i;

                    // Tiered rounding: every 30-min window past late_time rounds up to the next 30-min bucket
                    // e.g. 0-29 min late -> 30, 30-59 min late -> 60, 60-89 min late -> 90 ...
                    $lateMinutes = (intdiv($elapsedMinutes, 30) + 1) * 30;
                }
            }

            if ($isLate) {

                $lateAttendanceData = [
                    'attendance_id' => $attendanceId,
                    'emp_id' => $empId,
                    'date' => $date,
                    'check_in_time' => $firstCheckin,
                    'check_out_time' => 0,
                    'working_hours' => 0,
                    'created_by' => Auth::id() ?? 1,
                    'is_approved' => 1,
                    'approved_by' => Auth::id() ?? 1,
                ];

                $insertedId = DB::table('employee_late_attendances')->insertGetId($lateAttendanceData);

                // Insert new late minutes record
                $lateMinutesData = [
                    'attendance_id' => $attendanceId,
                    'emp_id' => $empId,
                    'attendance_date' => $date,
                    'minites_count' => $lateMinutes
                ];

                DB::table('employee_late_attendance_minites')->insert($lateMinutesData);


                $emp_data = DB::table('employee_late_attendances')->find($insertedId);

                if ($emp_data) {
                    $leave_type = 7;

                    $latePolicyService->processLateAttendance($emp_data, $leave_type, $date);
                }

            }
            return true;

        }

    }

}