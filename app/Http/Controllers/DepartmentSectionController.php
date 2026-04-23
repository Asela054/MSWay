<?php

namespace App\Http\Controllers;

use App\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DepartmentSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;

use Illuminate\Support\Facades\Session;

class DepartmentSectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($department_id)
    {
        $user = Auth::user();
        $permission = $user->can('department-section-list');
        if(!$permission) {
            abort(403);
        }

        $section = DepartmentSection::orderBy('id', 'asc')->where('department_id', $department_id)->get();
        $department = Department::where('id', $department_id)->first();
        return view('Organization.section', compact('section', 'department'))->with('id', $department_id);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('department-section-create');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = array(
            'name' => 'required',
            'section_id' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $section = new DepartmentSection();
        $section->section = $request->input('name');
        $section->department_id = $request->input('section_id');
        $section->save();

        return response()->json(['success' => 'Section Added successfully.']);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('department-section-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = DB::table('department_sections')
                ->leftJoin('employees', 'department_sections.section_head_emp_id', '=', 'employees.emp_id')
                ->where('department_sections.id', $id)
                ->select(
                    'department_sections.*',
                    'employees.emp_id',
                    'employees.emp_name_with_initial as emp_name'  
                )
                ->first();

            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, DepartmentSection $section)
    {
        $user = Auth::user();
        $permission = $user->can('department-section-edit');
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
            'section'              => $request->name,
            'section_head_emp_id'  => $request->employee,
            'updated_at'           => $current_date_time,
        );

        DepartmentSection::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Section is successfully updated']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('department-section-delete');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $data = DepartmentSection::findOrFail($id);
        $data->delete();
    }

    public function section_list_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $department = Input::get('department');

            if (empty($department)) {
                $department = Session::get('emp_department');
            }
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = DepartmentSection::where('section', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('department_id', $department)
                ->orderBy('section')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('id as id'),DB::raw('section as text')]);

            $count = DepartmentSection::where('department_id', $department)->count();
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
  
}
