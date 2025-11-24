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
            ->select('emp_id', 'emp_shift')
            ->where('emp_id', $full_emp_id)
            ->first();

            if (is_null($empshift)) {
                return false;
            }

             $emprosterinfo = DB::table('employee_roster_details')
                    ->select('emp_id', 'shift_id')
                    ->where('emp_id', $full_emp_id)
                    ->where('work_date', $date_input)
                    ->first();

                if ($emprosterinfo) {
                    $empshiftid = $emprosterinfo->shift_id;   
                }
                else {
                    $empshiftid = $empshift->emp_shift; // Use the shift from employees table
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
                    $Attendance->location = 1;
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
    
}