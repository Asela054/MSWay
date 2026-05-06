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

        if($jobleaves && $leaves->annual_leaves > 0){
    
            $annual_leaves = $leaves->annual_leaves;
            $leave_msg = "Employee is eligible for full 14 annual leaves per year.";
                
        }else{
            $annual_leaves = 0;
            $leave_msg = "Employee is Not eligible for annual leaves.";
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

        if($jobleaves && $leaves->casual_leaves > 0){
        
            $casual_leaves = 7;
        
        }else{
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
            
             $weekly_leaves = 4;
        }else{
             $weekly_leaves = 0;
        }
        return $weekly_leaves;
    }

}