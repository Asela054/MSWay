<?php

namespace App\Http\Controllers;

use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;

use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($company_id)
    {
        $user = Auth::user();
        $permission = $user->can('department-list');
        if(!$permission) {
            abort(403);
        }

        $department = Department::orderBy('id', 'asc')->where('company_id', $company_id)->get();
        $company = Company::where('id', $company_id)->first();
        return view('Organization.department', compact('department', 'company'))->with('id', $company_id);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('department-create');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = array(
            'name' => 'required',
            'company_id' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $department = new Department();
        $department->name = $request->input('name');
        $department->company_id = $request->input('company_id');
        $department->create_by = Auth::id();
        $department->save();

        return response()->json(['success' => 'Department Added successfully.']);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('department-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = Department::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, Department $department)
    {
        $user = Auth::user();
        $permission = $user->can('department-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = array(
            'name' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $current_date_time = Carbon::now()->toDateTimeString();

        $form_data = array(
            'name' => $request->name,
            'update_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );

        Department::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Department is successfully updated']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('department-delete');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $data = Department::findOrFail($id);
        $data->delete();
    }

    // public function department_list_sel2(Request $request){
    //     if ($request->ajax())
    //     {
    //         $page = Input::get('page');
    //         $company = Input::get('company');
    //         $resultCount = 25;

    //         $offset = ($page - 1) * $resultCount;

    //         $breeds = Department::where('name', 'LIKE',  '%' . Input::get("term"). '%')
    //             ->where('company_id', $company)
    //             ->orderBy('name')
    //             ->skip($offset)
    //             ->take($resultCount)
    //             ->get([DB::raw('id as id'),DB::raw('name as text')]);

    //         $count = Department::where('company_id', $company)->count();
    //         $endCount = $offset + $resultCount;
    //         $morePages = $endCount < $count;

    //         $results = array(
    //             "results" => $breeds,
    //             "pagination" => array(
    //                 "more" => $morePages
    //             )
    //         );

    //         return response()->json($results);
    //     }
    // }
    public function department_list_sel2(Request $request){
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
}
