<?php

namespace App\Services;

use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalaryAdvancePolicyService
{
      public function getAvailableAmount($emp_id, $request_date = null)
    {
        // If called via HTTP GET, pick up the date query param
        if ($request_date === null) {
            $request_date = request()->query('date');
        }

        // Use today's date as fallback for month/year context
        $referenceDate = $request_date ? Carbon::parse($request_date) : Carbon::now();

        // Get employee's job_category
        $employee = DB::table('employees')
            ->leftJoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
            ->select(
                'job_categories.salary_advance_type',
                'job_categories.salary_advance_value',
                'job_categories.salary_advance_min_date',
                'employees.emp_id as emp_id',
                'employees.id as employee_id'
            )
            ->where('employees.emp_id', $emp_id)
            ->first();

        if (!$employee) {
            return response()->json(['available_amount' => 0]);
        }

        // Check minimum attendance days in the requested month
        if (!is_null($employee->salary_advance_min_date) && $employee->salary_advance_min_date > 0) {
            $monthStart = $referenceDate->copy()->startOfMonth()->toDateString();
            $selectedDate = $referenceDate->toDateString();

            $attendedDays = DB::table('attendances')
                ->where('emp_id', $employee->emp_id)
                ->whereBetween('date', [$monthStart, $selectedDate])
                ->distinct('date')
                ->count('date');

            if ($attendedDays < $employee->salary_advance_min_date) {
                return response()->json([
                    'available_amount' => 0,
                    'errors' => 'Minimum attendance of ' . $employee->salary_advance_min_date . ' day(s) not reached up to the selected date. Current attendance: ' . $attendedDays . ' day(s).'
                ]);
            }
        }

        // Get basic salary
        $payroll = DB::table('payroll_profiles')
            ->leftJoin('remuneration_profiles AS rp1', function($join) {
                $join->on('rp1.payroll_profile_id', '=', 'payroll_profiles.id')
                    ->where('rp1.remuneration_id', '=', 2);
            })
            ->leftJoin('remuneration_profiles AS rp2', function($join) {
                $join->on('rp2.payroll_profile_id', '=', 'payroll_profiles.id')
                    ->where('rp2.remuneration_id', '=', 26);
            })
            ->select(
                DB::raw('(payroll_profiles.basic_salary + IFNULL(rp1.new_eligible_amount, 0) + IFNULL(rp2.new_eligible_amount, 0)) as total_basic')
            )
            ->where('payroll_profiles.emp_id', $employee->employee_id)
            ->first();

        $basic_salary = $payroll ? $payroll->total_basic : 0;

        if ($employee->salary_advance_type == 1) {
            $available_amount = ($basic_salary * $employee->salary_advance_value) / 100;
        } else {
            $available_amount = $employee->salary_advance_value;
        }

        return response()->json(['available_amount' => round($available_amount, 2)]);
    }
 
}