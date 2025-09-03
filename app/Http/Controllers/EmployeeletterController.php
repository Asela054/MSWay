<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;



class EmployeeletterController extends Controller
{

    public function employee_list_letter(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;
            $offset = ($page - 1) * $resultCount;
            $term = Input::get("term");

            $query = DB::table('employees')
                ->where(function($q) use ($term) {
                    $q->where('employees.calling_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('employees.emp_name_with_initial', 'LIKE', '%' . $term . '%');
                })
                ->where('deleted', 0)
                ->where('is_resigned', 0);

            // Add department filter if department parameter is provided and not empty
            if ($request->has('department') && !empty($request->department)) {
                $query->where('employees.emp_department', $request->department);
            }

            $employees = $query
                ->select(
                    DB::raw('DISTINCT employees.id as id'),
                    DB::raw('CONCAT(employees.emp_name_with_initial, " - ", employees.calling_name) as text'),
                    'employees.emp_job_code'
                )
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get();

            $count = Count($employees);

            $transformedResults = [];
            foreach ($employees as $employee) {
                $transformedResults[] = [
                    'id' => $employee->id,
                    'text' => $employee->text,
                    'jobid' => $employee->emp_job_code 
                ];
            }

            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = [
                "results" => $transformedResults, 
                "pagination" => [
                    "more" => $morePages
                ]
            ];

            return response()->json($results);
        }
    }
}
