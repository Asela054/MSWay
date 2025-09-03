<?php

namespace App\Http\Controllers;

use App\EmployeeSelected;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class SelectEmployeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('employee-select');
        if(!$permission) {
            abort(403);
        }

        return view('Employee.select_employee_index');
    }

    public function create()
    {
        $permission = Auth::user()->can('employee-select');
        if(!$permission){
            abort(403);
        }

        return view('Employee.select_employee');
    }

    public function get_select_employee_details(Request $request)
    {
        $permission = Auth::user()->can('employee-select');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = Request('department');

        $sql = "
                SELECT employees.*,
                       employment_statuses.emp_status as status_label,
                       shift_types.shift_name
                       FROM employees 
                LEFT JOIN employment_statuses ON employees.emp_status = employment_statuses.id
                LEFT JOIN shift_types ON employees.emp_shift = shift_types.id
                WHERE 1 = 1 
        ";

        if ($department != '') {
            $sql .= " AND employees.emp_department = '$department'";
        }

        $data = DB::select($sql);

        return response()->json(['emp_data' => $data]);

    }

    public function select_employee_post(Request $request)
    {
        $permission = Auth::user()->can('employee-select');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $checked = $request->emp_data;

        foreach ($checked as $ch) {

            $data = array(
                'emp_id' => $ch['emp_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            EmployeeSelected::query()->insert($data);

        }

        return response()->json(['success' => 'Successfully Selected']);

    }

    public function select_employee_list_dt(Request $request)
    {

        $branches = DB::table('employee_selected')
            ->leftJoin('employees', 'employees.emp_etfno', '=', 'employee_selected.emp_id')
            ->select('employee_selected.*', 'employees.emp_name_with_initial')
            ->get();

        return Datatables::of($branches)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $btn = '';

                $user = Auth::user();

                $permission = $user->can('employee-select-delete');
                if($permission){
                    $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('employee-select-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = EmployeeSelected::findOrFail($id);
        $data->delete();
    }

}
