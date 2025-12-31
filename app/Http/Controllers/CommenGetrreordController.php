<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Company;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Helpers\UserHelper;

class CommenGetrreordController extends Controller
{
    public function getDepartments($company_id)
    {
        $departments = Department::where('company_id', $company_id)->get();
        return response()->json($departments);
    }

    // Company List
    public function company_list_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = Company::where('name', 'LIKE',  '%' . Input::get("term"). '%')
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('id as id'),DB::raw('name as text'), DB::raw('name as name')]);

            $count = Company::count();
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


    public function department_list_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $company = Input::get('company');

            if (empty($company)) {
                $company = Session::get('emp_company');
            }
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = Department::where('name', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('company_id', $company)
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('id as id'),DB::raw('name as text')]);

            $count = Department::where('company_id', $company)->count();
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

    //All Employee Option Included
    public function department_list_sel3(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->input('page', 1);
            $company = $request->input('company');
            $term = $request->input('term', '');
            $resultCount = 25;
            $offset = ($page - 1) * $resultCount;

            $results = [];

            // Add "All Employees" option only on first page and when not searching
            if ($page == 1 && empty($term)) {
                $results[] = [
                    'id' => 'All',
                    'text' => 'All Employees'
                ];
            }

            $departments = Department::when($term, function($query, $term) {
                    $query->where('name', 'LIKE', '%' . $term . '%');
                })
                ->where('company_id', $company)
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('id as id'), DB::raw('name as text')]);

            $results = array_merge($results, $departments->toArray());

            $count = Department::where('company_id', $company)
                ->when($term, function($query, $term) {
                    $query->where('name', 'LIKE', '%' . $term . '%');
                })
                ->count();

            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            return response()->json([
                "results" => $results,
                "pagination" => [
                    "more" => $morePages
                ]
            ]);
        }
    }


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

            $query = UserHelper::applyEmployeeFilter($query);

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

            $count = Count($breeds); 

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

       public function employee_list_leaverequest(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;
            $offset = ($page - 1) * $resultCount;
            $term = Input::get("term");

          $query = DB::table('leave_request')
                    ->leftJoin('leaves', function($join) {
                        $join->on('leave_request.id', '=', 'leaves.request_id');
                    })
                    ->leftjoin('employees', 'leave_request.emp_id', '=', 'employees.emp_id')
                    ->where(function($q) use ($term) {
                        $q->where('employees.calling_name', 'LIKE', '%' . $term . '%')
                        ->orWhere('employees.emp_name_with_initial', 'LIKE', '%' . $term . '%');
                    })
                    ->where('leave_request.status', 1)
                    ->where('leave_request.request_approve_status', 1)
                    ->whereNull('leaves.id');
                  
            $query = UserHelper::applyEmployeeFilter($query);
            
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

            $count = Count($breeds); 

            
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

}
