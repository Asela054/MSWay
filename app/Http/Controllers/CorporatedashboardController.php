<?php

namespace App\Http\Controllers;

use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class CorporatedashboardController extends Controller
{
    public function index()
    {
        $chairman_dettails=DB::table('employees')
                        ->leftJoin('job_titles','job_titles.id','=','employees.emp_job_code')
                        ->select('employees.emp_name_with_initial','employees.calling_name','job_titles.title')
                        ->where('deleted', 0)
                        ->where('is_resigned', 0)
                        ->whereIn('emp_job_code', [1,2,3])
                        ->get();

    return view('Dashboard.organization',compact('chairman_dettails'));
    }

    public function getcoparatedashboard_EmployeeChart(Request $request)
    {
        $data = DB::table('employees')
            ->join('departments', 'employees.emp_department', '=', 'departments.id')
            ->select('departments.name', DB::raw('COUNT(DISTINCT employees.id) as count'))
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0)
            ->groupBy('employees.emp_department')
            ->get();
            
        return response()->json($data);

    }
}
