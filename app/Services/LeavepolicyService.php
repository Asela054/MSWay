<?php

namespace App\Services;

use Carbon\Carbon;
use DateTime;
use DB;

class LeavepolicyService
{
    // calculate annual leaves 

     public function calculateAnnualLeaves($empJoinDate, $empId,$jobCategoryId)
    {

        $leaves = DB::table('job_categories')->where('id', $jobCategoryId)->first();

        $jobleaves = DB::table('job_category_leaves')->where('job_id', $jobCategoryId)->where('leave_id',1)->first();

        $settings = DB::table('hrm_general_settings as settings')
                    ->join('hrm_general_settings_key_list as key_list', 'settings.key_id', '=', 'key_list.id')
                    ->where('key_list.config_key', 'LEAVE')
                    ->where('settings.status', 1)
                    ->select('settings.config_value' )
                    ->first(); 

       if($settings &&  $settings->config_value == 1){
        // calculate annual leaves based on joining date
            if($jobleaves && $leaves->annual_leaves > 0){

                    $employee_join_date = Carbon::parse($empJoinDate);
                    $current_date = Carbon::now();

                    // Calculate months of service
                    $months_of_service = $employee_join_date->diffInMonths($current_date);

                    // Extract join month and date for quarter calculation
                    $join_year = Carbon::parse($empJoinDate)->year;
                    $join_month = Carbon::parse($empJoinDate)->month;
                    $join_date = Carbon::parse($empJoinDate)->day;



                    // Get dates in YYYY-MM-DD format
                    $currentdate = $current_date->toDateString(); // YYYY-MM-DD

                    // Next year from JOIN DATE (join date + 1 year)
                    $next_year_from_join = $employee_join_date->copy()->addYear();
                    $next_year_first_date = $next_year_from_join->copy()->startOfYear()->toDateString(); // YYYY-01-01
                    $next_year_end_date = $next_year_from_join->copy()->endOfYear()->toDateString(); // YYYY-12-31
                    $join_year_end_date = $employee_join_date->copy()->endOfYear()->toDateString(); // Join year's YYYY-12-31

            


                    // First Year (0-12 months) - No annual leaves
                    if ($currentdate <=  $join_year_end_date) {
                        $annual_leaves = 0;
                        $leave_msg = "Employee is in the first year of service - no annual leaves yet.";
                    }
                    // Second Year (12-24 months) - Pro-rated leaves based on first year's quarter
                    elseif ($next_year_first_date <= $currentdate && $currentdate <= $next_year_end_date) {
                        // Get the 1-year anniversary date
                        $anniversary_date = $employee_join_date->copy()->addYear();

                        // Check if current date is between anniversary and December 31
                        $year_end = Carbon::create($anniversary_date->year, 12, 31);

                        // Only calculate if current date is after anniversary but before next year
                            // Get the quarter period from the joining year (original employment quarter)
                            $full_date = '2022-'.$join_month.'-'.$join_date;

                            $q_data = DB::table('quater_leaves')
                                ->where('from_date', '<=', $full_date)
                                ->where('to_date', '>', $full_date)
                                ->first();

                            $annual_leaves = $q_data ? $q_data->leaves : 0;
                            $leave_msg = $q_data ? "Using quarter leaves value from anniversary to year-end." : "No matching quarter found for pro-rated leaves.";
                    }
                    // Third year onwards (24+ months) - Full 14 days
                    else {
                        $annual_leaves = $leaves->annual_leaves;
                        $leave_msg = "Employee is eligible for full 14 annual leaves per year.";
                    }



                    
            }else{
                $annual_leaves = 0;
                $leave_msg = "Employee is Not eligible for annual leaves.";
            }
       } 
       elseif ($settings && $settings->config_value == 2) {
        // calculate annual leaves based on job category (ignoring joining date)    
             if($jobleaves && $leaves->annual_leaves > 0){
                    $annual_leaves = $leaves->annual_leaves;
                    $leave_msg = "Employee is eligible for full 14 annual leaves per year.";
                
            }else{
                $annual_leaves = 0;
                $leave_msg = "Employee is Not eligible for annual leaves.";
            }
       }elseif ($settings && $settings->config_value == 3) {
        // calculate annual leaves based on job category (joining date type condition 2)    
            $currentYear = date('Y');
            $joiningYear = date('Y', strtotime($empJoinDate));
            $joiningMonth = (int) date('m', strtotime($empJoinDate));

             if($jobleaves && $leaves->annual_leaves > 0){
                if ($joiningYear == $currentYear) {
                    if ($joiningMonth >= 1 && $joiningMonth <= 3) {
                        $annual_leaves = 14;
                        $leave_msg = "Employee joined in Q1 (Jan-Mar) — Eligible for 14 annual leaves for the current year.";
                    } elseif ($joiningMonth >= 4 && $joiningMonth <= 6) {
                        $annual_leaves = 10;
                        $leave_msg = "Employee joined in Q2 (Apr-Jun) — Eligible for 10 annual leaves for the current year.";
                    } elseif ($joiningMonth >= 7 && $joiningMonth <= 9) {
                        $annual_leaves = 5;
                        $leave_msg = "Employee joined in Q3 (Jul-Sep) — Eligible for 5 annual leaves for the current year.";
                    } else {
                        $annual_leaves = 0;
                        $leave_msg = "Employee joined in Q4 (Oct-Dec) — Not eligible for annual leaves for the current year.";
                    }
                } else {
                    // Joined in previous years → follow job category rule
                    if ($jobleaves && $leaves->annual_leaves > 0) {
                        $annual_leaves = $leaves->annual_leaves;
                        $leave_msg = "Employee is eligible for full {$annual_leaves} annual leaves per year.";
                    } else {
                        $annual_leaves = 0;
                        $leave_msg = "Employee is not eligible for annual leaves.";
                    }
                }

            }else{
                $annual_leaves = 0;
                $leave_msg = "Employee is Not eligible for annual leaves.";
            }
       }
       else{
        $annual_leaves = 0;
        $leave_msg = "Leave settings not configured.";
       }

        return [
            'annual_leaves' => $annual_leaves,
            'leave_msg' => $leave_msg
        ];
    }

    // calculate casual leaves 
      public function calculateCasualLeaves($empJoinDate, $jobCategoryId)
    {

         $leaves = DB::table('job_categories')->where('id', $jobCategoryId)->first();

         $jobleaves = DB::table('job_category_leaves')->where('job_id', $jobCategoryId)->where('leave_id',2)->first();

          $settings = DB::table('hrm_general_settings as settings')
                    ->join('hrm_general_settings_key_list as key_list', 'settings.key_id', '=', 'key_list.id')
                    ->where('key_list.config_key', 'LEAVE')
                    ->where('settings.status', 1)
                    ->select('settings.config_value' )
                    ->first(); 
        
         if($settings &&  $settings->config_value == 1){

          // calculte casual leaves based on joining date

             if($jobleaves && $leaves->casual_leaves > 0){
                    $join_date = new DateTime($empJoinDate);
                    $current_date = new DateTime();
                    $interval = $join_date->diff($current_date);
                    
                    $years_of_service = $interval->y;
                    $months_of_service = $interval->m;
                    
                    $today_date = Carbon::now();
                    $employeejoin_date = Carbon::parse($empJoinDate);

                    $currentdate = $today_date->toDateString(); // YYYY-MM-DD
                    $join_year_end_date = $employeejoin_date->copy()->endOfYear()->toDateString(); 



                    // Casual leave calculation
                    if ($currentdate <=  $join_year_end_date) {
                        $casual_leaves = number_format((6 / 12) * $months_of_service, 2);
                    }
                    else {
                        $casual_leaves = 7;
                    }
            }else{
                $casual_leaves = 0;
            }
         } 
         elseif ($settings && $settings->config_value == 2){
                // rturn full casual leaves without considering joining date
                if($jobleaves && $leaves->casual_leaves > 0){
            
                    $casual_leaves = 7;
                }else{
                    $casual_leaves = 0;
                }
            } 
         elseif($settings && $settings->config_value == 3){
            // rturn full casual leaves without considering joining date
             if($jobleaves && $leaves->casual_leaves > 0){
                $casual_leaves = 7;
             }else{
                $casual_leaves = 0;
            }     
         }   
        else{
            $casual_leaves = 0;
        }         

        return $casual_leaves;
    }

     public function getMedicalLeaves($jobCategoryId)
    {
        $leaves = DB::table('job_categories')->where('id', $jobCategoryId)->first();
        return $leaves ? $leaves->medical_leaves : 0;
    }

      public function getweeklyLeaves($jobCategoryId)
    {
       $jobleaves = DB::table('job_category_leaves')->where('job_id', $jobCategoryId)->where('leave_id',8)->first();
        if($jobleaves){
            
             $leavescount = DB::table('leave_types')->where('id', 8)->value('assigned_leave');

             $weekly_leaves = $leavescount ?? 0;
        }else{
             $weekly_leaves = 0;
        }
        return $weekly_leaves;
    }

}