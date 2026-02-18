<?php

namespace App\Http\Controllers\Additionals;

use App\Department;
use App\Employee;
use App\Attendance;
use App\Company;
use App\LateAttendance;
use App\OtApproved;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboarddetailedController extends Controller
{
        public function attendacechart(Request $request)
    {
        // Get all companies
        $companies = Company::all();
        
        // Initialize response array
        $responseData = [];
        
        // Get today and yesterday dates
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        
        foreach ($companies as $company) {
            // Get all departments for this company
            $departments = Department::where('company_id', $company->id)->get();
            
            // Initialize company data array
            $companyData = [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'company_code' => $company->code,
                'departments' => []
            ];
            
            foreach ($departments as $department) {
                // Get all employees in this department
                $employeeIds = Employee::where('emp_department', $department->id)
                    ->where('deleted', 0)
                    ->where('is_resigned', 0)
                    ->pluck('emp_id')
                    ->toArray();
                
                if (empty($employeeIds)) {
                    continue;
                }
                
                // Today's data
                $todayAttendance = Attendance::whereIn('emp_id', $employeeIds)
                    ->where('date', $today)
                    ->get();

                // Get employees who have attendance records for today
                $presentEmployeeIds = $todayAttendance->pluck('emp_id')->toArray();
                // Employees without attendance records for today are absent
                $todayAbsent = count(array_diff($employeeIds, $presentEmployeeIds));
                // Count present employees (any attendance record for today means present)
                $todayPresent = count($presentEmployeeIds);
                
                $todayLate = LateAttendance::whereIn('emp_id', $employeeIds)
                    ->where('date', $today)
                    ->count();

                // Yesterday's data
                $yesterdayAttendance = Attendance::whereIn('emp_id', $employeeIds)
                    ->where('date', $yesterday)
                    ->get();

                // Get employees who have attendance records for yesterday
                $yesterdaypresentEmployeeIds = $yesterdayAttendance->pluck('emp_id')->toArray();
                // Employees without attendance records for yesterday are absent
                $yesterdayAbsent = count(array_diff($employeeIds, $yesterdaypresentEmployeeIds));
                // Count present employees (any attendance record for yesterday means present)
                $yesterdayPresent = count($yesterdaypresentEmployeeIds);

                $yesterdayLate = LateAttendance::whereIn('emp_id', $employeeIds)
                    ->where('date', $yesterday)
                    ->count();

                // Overtime data for yesterday
                $yesterdayOt = OtApproved::whereIn('emp_id', $employeeIds)
                    ->where('date', $yesterday)
                    ->get();
                $otPersons = $yesterdayOt->groupBy('emp_id')->count();

                $normalOtHours = $yesterdayOt->sum('hours');

                $normalOtAmount = $yesterdayOt->sum(function($ot) {
                    return $ot->hours * ($ot->hourly_rate ?? 0);
                });
                
                $doubleOtHours = $yesterdayOt->sum('double_hours');

                $doubleOtAmount = $yesterdayOt->sum(function($ot) {
                    return $ot->double_hours * ($ot->hourly_rate ?? 0) * 2;
                });
                
                $totalOtHours = $normalOtHours + $doubleOtHours;
                $totalOtAmount = $normalOtAmount + $doubleOtAmount;
                
                // Add department data to company's departments array
                $companyData['departments'][$department->name] = [
                    'department_id' => $department->id,
                    'total_employees' => count($employeeIds),
                    'attendance_today' => $todayPresent,
                    'late_attendance_today' => $todayLate,
                    'absent_today' => $todayAbsent,
                    'attendance_yesterday' => $yesterdayPresent,
                    'late_attendance_yesterday' => $yesterdayLate,
                    'absent_yesterday' => $yesterdayAbsent,
                    'ot_persons_yesterday' => $otPersons,
                    'normal_ot_hours_yesterday' => $normalOtHours,
                    'normal_ot_amount_yesterday' => $normalOtAmount,
                    'double_ot_hours_yesterday' => $doubleOtHours,
                    'double_ot_amount_yesterday' => $doubleOtAmount,
                    'total_ot_hours_yesterday' => $totalOtHours,
                    'total_ot_amount_yesterday' => $totalOtAmount,
                ];
            }
            
            // Only add company data if it has departments with data
            if (!empty($companyData['departments'])) {
                $responseData[$company->name] = $companyData;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $responseData,
            'dates' => [
                'today' => $today,
                'yesterday' => $yesterday
            ]
        ]);
    }

}