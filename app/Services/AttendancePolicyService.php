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

    public function attendanceInsertcsv_txt($full_emp_id, $date_input, $timestamp, $date)
    { 
         $empshift = DB::table('employees')
            ->select('emp_id', 'emp_shift','emp_location')
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
                }
                else {
                    $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');
                    $emprosterinfo = DB::table('employee_roster_details')
                        ->select('emp_id', 'shift_id')
                        ->where('emp_id', $full_emp_id)
                        ->where('work_date', $previous_day)
                        ->first();

                     if ($emprosterinfo) {
                        $empshiftid = $emprosterinfo->shift_id;   
                    }
                    else {
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

                if ($shift && $shift->off_next_day == '1' && $date == $date_input) {
                    $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');

                    $shif_ontime = Carbon::parse($shift->onduty_time);
                    
                    if($shif_ontime > $timestamp){
                       
                        $attendance_date = ($period === 'AM') ? $previous_day : substr($timestamp, 0, 10);
                    }
                    else{
                        $attendance_date = substr($timestamp, 0, 10);
                    }
                    
                } else if ($date == $date_input) {
                    if($employeeshiftdetails){
                        $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');
                        $attendance_date = ($period === 'AM') ? $previous_day : substr($timestamp, 0, 10);
                    }else{
                        
                        $attendance_date = substr($timestamp, 0, 10);
                    }  
                }

                if($date == $date_input){
                    $Attendance = AppAttendance::firstOrNew(['timestamp' => $timestamp, 'emp_id' => $full_emp_id]);
                    $Attendance->uid = $full_emp_id;
                    $Attendance->emp_id = $full_emp_id;
                    $Attendance->timestamp = $timestamp;
                    $Attendance->date = $attendance_date;
                    $Attendance->location = $employeeLocation;
                    $Attendance->save();

                    $insertId = $Attendance->id;

                return $this->checkAndInsertLateAttendance($full_emp_id, $attendance_date, $timestamp,$insertId);
                }           
                return true;
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
                }
                else {
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

         if ($shift && $shift->off_next_day == '1' && $date_stamp == $attendacedate) {
        $previous_day = (new DateTime($attendacedate))->modify('-1 day')->format('Y-m-d');

        $shif_ontime = Carbon::parse($shift->onduty_time);
        $txt_datetime = Carbon::parse($time_h . ':' . $time_m . ':00');

        if($shif_ontime > $txt_datetime){
            $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
            $attendance_date = ($period === 'AM') ? $previous_day : substr($final_timestamp, 0, 10);
        } else {
            $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
            $attendance_date = substr($final_timestamp, 0, 10);
        }
        } else if ($date_stamp == $attendacedate) {
            if($employeeshiftdetails){
                $previous_day = (new DateTime($attendacedate))->modify('-1 day')->format('Y-m-d');
                $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
                $attendance_date = ($period === 'AM') ? $previous_day : substr($final_timestamp, 0, 10);
            } else {
                $final_timestamp = $attendacedate . ' ' . $time_h . ':' . $time_m . ':00';
                $attendance_date = substr($final_timestamp, 0, 10);
            }  
        }

        if($date_stamp == $attendacedate){
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

             return $this->checkAndInsertLateAttendance($empid, $attendacedate, $attendacetimestamp,$insertId);

        }
        return true;

    }
    

    private  function checkAndInsertLateAttendance($empId, $date, $firstCheckin, $attendanceId)
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

        }else{
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
                    }
                    else {
                        $shiftId = $employeeshift->emp_shift; 
                    }

                // Get shift on-duty time
                $shiftType = DB::table('shift_types')
                    ->select('late_time','leave_early_time','onduty_time', 'offduty_time','saturday_onduty_time', 'saturday_offduty_time')
                    ->where('id', $shiftId)
                    ->first();

                if (!$shiftType) {
                    return true;
                }

            $isSaturday = Carbon::parse($date)->isSaturday();

            if ($isSaturday && $shiftType->saturday_onduty_time && $shiftType->saturday_offduty_time) {

                $onDutyTime  = Carbon::parse($shiftType->saturday_onduty_time);
                $offDutyTime = Carbon::parse($shiftType->saturday_offduty_time);
            } else {
                $onDutyTime  = Carbon::parse($shiftType->onduty_time);
                $offDutyTime = Carbon::parse($shiftType->offduty_time);
            }

            $checkInTime = Carbon::parse($firstCheckin);

            // Determine whether this punch is a check-in or a check-out by measuring how close the punch time is to each boundary.
            // If the punch is closer to off-duty time than on-duty time,
            // it is most likely a check-out punch — so we skip late marking to avoid incorrectly flagging a clock-out as a late arrival.
            $diffFromOnDuty  = abs($checkInTime->diffInMinutes($onDutyTime));
            $diffFromOffDuty = abs($checkInTime->diffInMinutes($offDutyTime));

            if ($diffFromOffDuty < $diffFromOnDuty) {
                // This punch is closer to off-duty time → treat as check-out, not check-in Skip late attendance evaluation to prevent false late records
                return true;
            }


                if ($shiftType->late_time) {

                    $ondutylateTime = new DateTime($shiftType->late_time);
                    $checkInTime = new DateTime($firstCheckin);

                    $interval = $checkInTime->diff($ondutylateTime);
                    $lateMinutes = ($interval->h * 60) + $interval->i;

                        // Check if check-in time is after on-duty time
                        if ($checkInTime > $ondutylateTime) {
                            $isLate = true;
                        }
                }   
            
                if ($isLate){

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