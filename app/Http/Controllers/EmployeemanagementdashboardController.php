<?php

namespace App\Http\Controllers;

use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class EmployeemanagementdashboardController extends Controller
{
    public function index()
    {
    
    return view('Dashboard.employee');
    }

    
    public function getemployeedashboard_RecruitmentChart(Request $request)
    {
        $data = DB::table('employee_requrement_details')
                ->join('employees', 'employees.id', '=', 'employee_requrement_details.employee_id')
                ->select(
                    'employee_requrement_details.first_interview_date',
                    'employee_requrement_details.second_interview_date',
                    'employee_requrement_details.third_interview_date',
                    DB::raw('COUNT(DISTINCT CASE WHEN employee_requrement_details.first_interview_date IS NOT NULL THEN employees.id END) as first_interview_count'),
                    DB::raw('COUNT(DISTINCT CASE WHEN employee_requrement_details.second_interview_date IS NOT NULL THEN employees.id END) as second_interview_count'),
                    DB::raw('COUNT(DISTINCT CASE WHEN employee_requrement_details.third_interview_date IS NOT NULL THEN employees.id END) as third_interview_count')
                )
                ->where('employees.deleted', 0)
                ->where('employees.is_resigned', 0)
                ->limit(30)
                ->orderBy('employee_requrement_details.first_interview_date', 'desc')
                ->get();

        return response()->json($data);

    }
}