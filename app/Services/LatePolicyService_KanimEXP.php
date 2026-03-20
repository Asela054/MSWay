<?php

namespace App\Services;

use App\Employee;
use DB;
use App\Leave;
use Illuminate\Support\Facades\Auth;
use App\Services\LeavepolicyService;
use Carbon\Carbon;

class LatePolicyService
{
    /**
     * Process late attendance approval for an employee
     */

     protected $leavePolicyService;

    public function __construct(LeavepolicyService $leavePolicyService)
    {
        $this->leavePolicyService = $leavePolicyService;
    }


    public function processLateAttendance($empData, $leaveType, $date)
    {
        // Get job category late policy
        $jobcategory = DB::table('employees')
            ->leftJoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
            ->select('job_categories.late_type', 'job_categories.short_leaves', 'job_categories.half_days', 'job_categories.late_attend_min')
            ->where('employees.emp_id', $empData->emp_id)
            ->first();

        if (!$jobcategory) {
                return false; // Simply return false if job category not found
            }

            // Check each required field for null values
            if ($jobcategory->late_type === null || 
                $jobcategory->short_leaves === null || 
                $jobcategory->half_days === null || 
                $jobcategory->late_attend_min === null) {
                return false; // Simply return false if any field is null
            }


        $latetype = $jobcategory->late_type;
        $shortleave = $jobcategory->short_leaves;
        $halfday = $jobcategory->half_days;
        $minitescount = $jobcategory->late_attend_min;

        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));


        // Count late occurrences for this employee on this date
         $d_count = DB::table('employee_late_attendances')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('emp_id', $empData->emp_id)
            ->count();

        // Determine half_short value based on count
        $half_short = ($d_count == 1 || $d_count == 2) ? 0.25 : 0.5;


        // Process based on late type
        switch ($latetype) {
            case 1:
                $this->processLateType1($empData, $leaveType, $minitescount, $half_short, $date);
                break;
            case 2:
                $this->processLateType2($empData, $leaveType, $d_count, $shortleave, $halfday);
                break;
            case 3:
                $this->processLateType3($empData, $leaveType, $d_count, $shortleave, $halfday, $minitescount);
                break;
        }

         return true;
    }

    /**
     * Process Late Type 1 - Minutes based policy
     */
    private function processLateType1($empData, $leaveType, $minitescount, $half_short, $date)
    {
        if (empty($minitescount)) {
            return;
        }

        // Calculate total minutes for the month
        $totalMinutes = DB::table('employee_late_attendance_minites')
            ->where('emp_id', $empData->emp_id)
            ->whereRaw("DATE_FORMAT(attendance_date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')", [$date])
            ->where('attendance_date', '!=', $date)
            ->sum('minites_count');

        $attendanceminitesrecord = DB::table('employee_late_attendance_minites')
            ->select('id', 'attendance_id', 'emp_id', 'attendance_date', 'minites_count')
            ->where('emp_id', $empData->emp_id)
            ->where('attendance_date', '!=', $date)
            ->first();

       if ($attendanceminitesrecord) {
            $totalminitescount = $totalMinutes + $attendanceminitesrecord->minites_count;
        } else {
            $totalminitescount = $totalMinutes;
        }


        // Create leave record based on minutes threshold
        if ($minitescount < $totalminitescount) {
            $this->createLeaveRecord($empData, $leaveType, 0, 0);
        } else {
            $this->createLeaveRecord($empData, $leaveType, $half_short, $half_short);
        }
    }

    /**
     * Process Late Type 2 - Count based policy with type change
     */
    private function processLateType2($empData, $leaveType, $d_count, $shortleave, $halfday)
    {
        if ($d_count <= $shortleave) {
            $leaveamount = 0.25;
            $applyleavetype = $leaveType;
        } elseif ($d_count <= $halfday) {
            $leaveamount = 0.5;
            $applyleavetype = $leaveType;
        } else {
            $leaveamount = 0.5;
            $applyleavetype = 3;
        }

        $this->createLeaveRecord($empData, $applyleavetype, $leaveamount, $leaveamount);
    }

    /**
     * Process Late Type 3 - Count based policy with minutes check
     */

    private function processLateType3($empData, $leaveType, $d_count, $shortleave, $halfday, $minitescount)
    {
        if ($d_count <= $shortleave) {
            $leaveamount = 0.25;
            $this->createLeaveRecord($empData, $leaveType, $leaveamount, $leaveamount);

        } else {
            $leaveamount = 0.5;

            $balances = $this->getEmployeeLeaveBalances($empData->emp_id, $empData->date);
            $applyLeaveType = ($balances && $balances[$leaveType] >= $leaveamount) ? $leaveType : 3;

            $this->createLeaveRecord($empData, $applyLeaveType, $leaveamount, $leaveamount);
        } 
    }

    private function getEmployeeLeaveBalances($emp_id,$date)
    {
        $employee = Employee::where('emp_id', $emp_id)->first();
        
        if (!$employee) {
            return null;
        }
        
        $empid = $employee->emp_id;
        $job_categoryid = $employee->job_category_id;
        $emp_join_date = $employee->emp_join_date;
        
        // Set current year dates
        $formated_from_date = date('Y').'-01-01';
        $formated_to_date = date('Y').'-12-31';
        
        // Get taken leaves for current year
        $current_year_taken_a_l = (new \App\Leave)->taken_annual_leaves($empid, $formated_from_date, $formated_to_date);
        $current_year_taken_c_l = (new \App\Leave)->taken_casual_leaves($empid, $formated_from_date, $formated_to_date);
        $current_year_taken_med = (new \App\Leave)->taken_medical_leaves($empid, $formated_from_date, $formated_to_date);
        

        // Get current month's weekly leaves
        $month_from_date = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $month_to_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

        $current_weekly_taken = (new \App\Leave)->taken_weekly_leaves($empid, $month_from_date, $month_to_date);
        
        // Calculate total available leaves
        $annualData = $this->leavePolicyService->calculateAnnualLeaves($emp_join_date, $empid, $job_categoryid);
        $total_annual_leaves = $annualData['annual_leaves'];
        
        $total_casual_leaves = $this->leavePolicyService->calculateCasualLeaves($emp_join_date, $job_categoryid);
        $total_medical_leaves = $this->leavePolicyService->getMedicalLeaves($job_categoryid);
        $total_weekly_leaves = $this->leavePolicyService->getweeklyLeaves($job_categoryid);
        
        // Calculate available balances
        $available_annual = $total_annual_leaves - $current_year_taken_a_l;
        $available_casual = $total_casual_leaves - $current_year_taken_c_l;
        $available_medical = $total_medical_leaves - $current_year_taken_med;
        $available_weekly = $total_weekly_leaves - $current_weekly_taken;
        
        // Return balances based on leave type or all balances
        $balances = [
                    1 => max(0, $available_annual),
                    2 => max(0, $available_casual),
                    4 => max(0, $available_medical),
                    5 => max(0, $available_weekly),
                ];
        
        return $balances;
    }

        /**
     * Create leave record
     */
    private function createLeaveRecord($empData, $leaveType, $noOfDays, $halfShort)
    {
        $leave = new Leave;
        $leave->emp_id = $empData->emp_id;
        $leave->leave_type = $leaveType;
        $leave->leave_from = $empData->date;
        $leave->leave_to = $empData->date;
        $leave->no_of_days = $noOfDays;
        $leave->half_short = $halfShort;
        $leave->reson = 'Late';
        $leave->comment = '';
        $leave->emp_covering = '';
        $leave->leave_approv_person = Auth::id();
        $leave->status = 'Pending';
        $leave->save();
    }


}