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
        $companies=DB::table('companies')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial')->where('deleted',0)->get();
        return view('Report.employee_recruitment_report',compact('companies','employees'));
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
    
                $firstInterviewRecords = DB::table('employee_requrement_details as erd')
                ->join('employees as e', 'e.id', '=', 'erd.employee_id')
                ->join('departments as d', 'd.id', '=', 'e.emp_department')
                ->select(
                    'erd.id as interview_id',
                    'e.emp_name_with_initial',
                    'd.name as empdepartment',
                    DB::raw("'First Interviewer' as interviewer_role"),
                    'erd.first_interview_date as interview_date'
                )
                ->where('erd.first_interviwer', $employeeId)
                ->get();
            
            $secondInterviewRecords = DB::table('employee_requrement_details as erd')
                ->join('employees as e', 'e.id', '=', 'erd.employee_id')
                ->join('departments as d', 'd.id', '=', 'e.emp_department')
                ->select(
                    'erd.id as interview_id',
                    'e.emp_name_with_initial',
                    'd.name as empdepartment',
                    DB::raw("'Second Interviewer' as interviewer_role"),
                    'erd.second_interview_date as interview_date'
                )
                ->where('erd.second_interviewer', $employeeId)
                ->get();
            
            $thirdInterviewRecords = DB::table('employee_requrement_details as erd')
                ->join('employees as e', 'e.id', '=', 'erd.employee_id')
                ->join('departments as d', 'd.id', '=', 'e.emp_department')
                ->select(
                    'erd.id as interview_id',
                    'e.emp_name_with_initial',
                    'd.name as empdepartment',
                    DB::raw("'Third Interviewer' as interviewer_role"),
                    'erd.third_interview_date as interview_date'
                )
                ->where('erd.third_interviewer', $employeeId)
                ->get();
            
            $interviews = $firstInterviewRecords->merge($secondInterviewRecords)->merge($thirdInterviewRecords);
            
        return response()->json(['data' => $interviews]);
    }
    

}
