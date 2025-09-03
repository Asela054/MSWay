<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Yajra\DataTables\Facades\DataTables;

class RecruitmentreportController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('employee-recruitment-report');
        if (!$permission) {
            abort(403);
        }
        $departments=DB::table('departments')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial')->where('deleted',0)->get();
        return view('Report.employee_recruitment_report',compact('departments','employees'));
    }


    public function filter(Request $request)
    {
        $query = DB::table('employee_requrement_details')
            ->join('employees', 'employee_requrement_details.employee_id', '=', 'employees.id')
            ->select(
                'employee_requrement_details.*', 
                'employees.emp_name_with_initial',
                DB::raw('(SELECT emp_name_with_initial FROM employees WHERE id = employee_requrement_details.first_interviwer) as first_interviewer_name'),
                DB::raw('(SELECT emp_name_with_initial FROM employees WHERE id = employee_requrement_details.second_interviewer) as second_interviewer_name'),
                DB::raw('(SELECT emp_name_with_initial FROM employees WHERE id = employee_requrement_details.third_interviewer) as third_interviewer_name')
            );

        if ($request->department != 'All') {
            $query->where('employees.emp_department', $request->department)
            ->where('employees.deleted', '0');
        }

       if ($request->reportType == "2") {
            $query->where('employee_requrement_details.employee_id', $request->employee);
        }


        $results = $query->get();

        return response()->json(['data' => $results]);
    }


    public function interviwerfilter(Request $request)
    {
        $employeeId = $request->input('employee');
    
        $interviews = DB::table('employee_requrement_details as erd')
            ->join('employees as e', function ($join) use ($employeeId) {
                $join->on('e.id', '=', 'erd.first_interviwer')
                    ->orOn('e.id', '=', 'erd.second_interviewer')
                    ->orOn('e.id', '=', 'erd.third_interviewer');
            })
            ->select(
                'erd.id as interview_id',
                'e.emp_name_with_initial',
                'e.emp_department',
                DB::raw("
                    CASE 
                        WHEN erd.first_interviwer = $employeeId THEN 'First Interviewer'
                        WHEN erd.second_interviewer = $employeeId THEN 'Second Interviewer'
                        WHEN erd.third_interviewer = $employeeId THEN 'Third Interviewer'
                    END as interviewer_role
                "),
                DB::raw("
                    CASE 
                        WHEN erd.first_interviwer = $employeeId THEN erd.first_interview_date
                        WHEN erd.second_interviewer = $employeeId THEN erd.second_interview_date
                        WHEN erd.third_interviewer = $employeeId THEN erd.third_interview_date
                    END as interview_date
                ")
            )
            ->where(function ($query) use ($employeeId) {
                $query->where('erd.first_interviwer', $employeeId)
                      ->orWhere('erd.second_interviewer', $employeeId)
                      ->orWhere('erd.third_interviewer', $employeeId);
            })
            ->get();
    
        return response()->json(['data' => $interviews]);
    }
    
    



}
