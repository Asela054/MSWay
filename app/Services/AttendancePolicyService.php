<?php

namespace App\Services;

use App\Attendance as AppAttendance;
use App\Models\Attendance;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
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
            
            return DB::table('attendances')->insert($data);
        }
        return true;

    }
    


// public function checkAndInsertLateAttendance($empId, $date, $firstCheckin, $attendanceId = null, $lastCheckout = null, $workingHours = null)
// {
//         // Validate required parameters
//         if (empty($empId) || empty($date) || empty($firstCheckin)) {
//             return [
//                 'status' => false,
//                 'message' => 'Missing required parameters: empId, date, and firstCheckin are required'
//             ];
//         }

//         // Get employee shift information
//         $employee = DB::table('employees')
//             ->select('emp_id', 'emp_shift')
//             ->where('emp_id', $empId)
//             ->first();

//         if (is_null($employee)) {
//             return [
//                 'status' => false,
//                 'message' => 'Employee not found'
//             ];
//         }

//         // Check if employee has roster for this date
//         $rosterInfo = DB::table('employee_roster_details')
//             ->select('emp_id', 'shift_id')
//             ->where('emp_id', $empId)
//             ->where('work_date', $date)
//             ->first();

//         // Determine shift ID (roster shift if exists, otherwise employee default shift)
//         $shiftId = $rosterInfo ? $rosterInfo->shift_id : $employee->emp_shift;

//         // Get shift on-duty time
//         $shiftType = DB::table('shift_types')
//             ->select('shift_types.onduty_time')
//             ->where('id', $shiftId)
//             ->first();

//         // If no shift found or no on-duty time, cannot determine late status
//         if (!$shiftType || !$shiftType->onduty_time) {
//             return [
//                 'status' => false,
//                 'message' => 'Shift not found or on-duty time not configured for employee'
//             ];
//         }

//         // Parse times
//         $ondutyTime = new DateTime($shiftType->onduty_time);
//         $checkInTime = new DateTime($firstCheckin);
        
//         $lateMinutes = 0;
//         $isLate = false;

//         // Check if check-in time is after on-duty time
//         if ($checkInTime > $ondutyTime) {
//             $isLate = true;
//             $interval = $checkInTime->diff($ondutyTime);
//             $lateMinutes = ($interval->h * 60) + $interval->i;
//         }

//         // Prepare late attendance data
//         $lateAttendanceData = [
//             'attendance_id' => $attendanceId,
//             'emp_id' => $empId,
//             'date' => $date,
//             'check_in_time' => $firstCheckin,
//             'check_out_time' => $lastCheckout,
//             'working_hours' => $workingHours,
//             'created_by' => Auth::id(),
//             'created_at' => now(),
//             'updated_at' => now()
//         ];

//         // Delete existing late attendance record for the same attendance
//         $deleteQuery = DB::table('employee_late_attendances')
//             ->where('emp_id', $empId)
//             ->where('date', $date);
        
//         if ($attendanceId) {
//             $deleteQuery->where('attendance_id', $attendanceId);
//         }
        
//         $deleteQuery->delete();

//         // Insert new late attendance record
//         DB::table('employee_late_attendances')->insert($lateAttendanceData);

//         // Handle late minutes record if employee is late
//         if ($isLate) {
//             // Delete existing late minutes record
//             $minutesDeleteQuery = DB::table('employee_late_attendance_minites')
//                 ->where('emp_id', $empId)
//                 ->where('attendance_date', $date);
            
//             if ($attendanceId) {
//                 $minutesDeleteQuery->where('attendance_id', $attendanceId);
//             }
            
//             $minutesDeleteQuery->delete();

//             // Insert new late minutes record
//             $lateMinutesData = [
//                 'attendance_id' => $attendanceId,
//                 'emp_id' => $empId,
//                 'attendance_date' => $date,
//                 'minites_count' => $lateMinutes,
//                 'created_at' => now(),
//                 'updated_at' => now()
//             ];
            
//             DB::table('employee_late_attendance_minites')->insert($lateMinutesData);
//         }

//         return [
//             'status' => true,
//             'message' => $isLate ? "Employee is late by {$lateMinutes} minutes" : "Employee is on time",
//             'is_late' => $isLate,
//             'late_minutes' => $lateMinutes,
//             'onduty_time' => $shiftType->onduty_time,
//             'check_in_time' => $firstCheckin,
//             'shift_id' => $shiftId
//         ];

    
// }


}