<?php

namespace App\Services;

use Carbon\Carbon;
use DateTime;
use DB;

class LeavepolicyService
{
    // calculate annual leaves 
        public function calculateAnnualLeaves($empJoinDate, $empId)
    {
        $employee_join_date = Carbon::parse($empJoinDate);
        $current_date = Carbon::now();

        // Calculate months of service
        $months_of_service = $employee_join_date->diffInMonths($current_date);

        // Extract join month and date for quarter calculation
        $join_year = Carbon::parse($empJoinDate)->year;
        $join_month = Carbon::parse($empJoinDate)->month;
        $join_date = Carbon::parse($empJoinDate)->day;

        // First Year (0-12 months) - No annual leaves
        if ($months_of_service < 12) {
            $annual_leaves = 0;
            $leave_msg = "Employee is in the first year of service - no annual leaves yet.";
        }
        // Second Year (12-24 months) - Pro-rated leaves based on first year's quarter
        elseif ($months_of_service < 24) {
            // Get the 1-year anniversary date
            $anniversary_date = $employee_join_date->copy()->addYear();

            // Check if current date is between anniversary and December 31
            $year_end = Carbon::create($anniversary_date->year, 12, 31);

            // Only calculate if current date is after anniversary but before next year
            if ($current_date >= $anniversary_date && $current_date <= $year_end) {
                // Get the quarter period from the joining year (original employment quarter)
                $full_date = '2022-'.$join_month.'-'.$join_date;

                $q_data = DB::table('quater_leaves')
                    ->where('from_date', '<=', $full_date)
                    ->where('to_date', '>', $full_date)
                    ->first();

                $annual_leaves = $q_data ? $q_data->leaves : 0;
                $leave_msg = $q_data ? "Using quarter leaves value from anniversary to year-end." : "No matching quarter found for pro-rated leaves.";
            }
            // After December 31, switch to standard 14 days
            elseif ($current_date > $year_end) {
                $annual_leaves = 14;
                $leave_msg = "Switched to standard 14 days from January 1st.";
            }
            // Before anniversary date
            else {
                $annual_leaves = 0;
                $leave_msg = "Waiting for 1-year anniversary date ({$anniversary_date->format('Y-m-d')})";
            }
        }
        // Third year onwards (24+ months) - Full 14 days
        else {
            $annual_leaves = 14;
            $leave_msg = "Employee is eligible for full 14 annual leaves per year.";
        }

        return [
            'annual_leaves' => $annual_leaves,
            'leave_msg' => $leave_msg
        ];
    }

 
    // calculate casual leaves 

    public function calculateCasualLeaves($empJoinDate)
    {
        $join_date = new DateTime($empJoinDate);
        $current_date = new DateTime();
        $interval = $join_date->diff($current_date);
        
        $years_of_service = $interval->y;
        $months_of_service = $interval->m;
        
        // Casual leave calculation
        if ($years_of_service == 0) {
            $casual_leaves = number_format((6 / 12) * $months_of_service, 2);
        } else {
            $casual_leaves = 7;
        }
        return $casual_leaves;
    }


     public function getMedicalLeaves($jobCategoryId)
    {
        $leaves = DB::table('job_categories')->where('id', $jobCategoryId)->first();
        return $leaves ? $leaves->medical_leaves : 0;
    }

     

}