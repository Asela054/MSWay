<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;

class EmployeeSelectController extends Controller
{
    public function employee_list_sel2(Request $request)
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

            $breeds = $query
                ->select(
                    DB::raw('DISTINCT employees.emp_id as id'),
                    DB::raw('CONCAT(employees.emp_name_with_initial, " - ", employees.calling_name) as text')
                )
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get();

            $count = Count($breeds); // Get count from the actual results

            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = [
                "results" => $breeds,
                "pagination" => [
                    "more" => $morePages
                ]
            ];

            return response()->json($results);
        }
    }

    public function location_list_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = \Illuminate\Support\Facades\DB::query()
                ->where('branches.location', 'LIKE',  '%' . Input::get("term"). '%')
                ->from('branches')
                ->orderBy('branches.location')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT branches.id as id'),DB::raw('branches.location as text')]);

            $count = DB::query()
                ->where('branches.location', 'LIKE',  '%' . Input::get("term"). '%')
                ->from('branches')
                ->orderBy('branches.location')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT branches.id as id'),DB::raw('branches.location as text')])
                ->count();
            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = array(
                "results" => $breeds,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

    public function get_dept_emp_list()
    {
        $dept_id = Input::get('dept');
        $emp_list = DB::table('employees')
            ->where('deleted', 0)
            ->where('emp_department', $dept_id)
            ->orderBy('emp_name_with_initial')
            ->get();
        return response()->json($emp_list, 200);
    }
}
