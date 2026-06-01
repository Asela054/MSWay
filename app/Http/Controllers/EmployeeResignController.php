<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EmployeeAssignBy;
use Hash;
use Auth;
use Datatables;
use DB;
use Carbon\Carbon;

class EmployeeResignController extends Controller
{
    public function employee_resign_report()
    {
        $permission = Auth::user()->can('employee-resign-report');
        if (!$permission) {
            abort(403);
        }

        $departments=DB::table('departments')->select('*')->get();
        return view('Report.employee_resign_report',compact('departments'));
    }

    
    public function get_resign_employees(Request $request)
{
    $department = $request->input('department');
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');

    $userId = Auth::id();

    // Get company and branch IDs the user has access to (same pattern as CommenGetrreordController)
    $userCompanyIds = DB::table('user_has_companies')
        ->where('user_id', $userId)
        ->pluck('company_id')
        ->toArray();

    $userBranchIds = DB::table('user_has_companies')
        ->where('user_id', $userId)
        ->whereNotNull('branch_id')
        ->pluck('branch_id')
        ->toArray();

    $types = DB::table('employees')
        ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
        ->leftJoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
        ->leftJoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->select(
            'employees.*',
            'departments.name AS department_name',
            'job_titles.title AS title',
            'branches.location AS location'
        )
        ->where('employees.deleted', '0')
        ->where('employees.is_resigned', 1);

    // Apply company filter if user has specific company permissions
    if (!empty($userCompanyIds)) {
        $types->whereIn('employees.emp_company', $userCompanyIds);
    }

    // Apply branch filter if user has specific branch permissions
    if (!empty($userBranchIds)) {
        $types->whereIn('employees.emp_location', $userBranchIds);
    }

    if (!empty($department) && $department != 'All') {
        $types->where('employees.emp_department', $department);
    }

    if (!empty($from_date) && !empty($to_date)) {
        $types->whereBetween('employees.resignation_date', [$from_date, $to_date]);
    }

    $types = $types->get();

    return Datatables::of($types)
        ->addIndexColumn()
        ->addColumn('employee_display', function ($row) {
            return EmployeeHelper::getDisplayName($row);
        })
        ->filterColumn('employee_display', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('e.emp_name_with_initial', 'like', "%{$keyword}%")
                    ->orWhere('e.calling_name', 'like', "%{$keyword}%")
                    ->orWhere('e.emp_id', 'like', "%{$keyword}%");
            });
        })
        ->addColumn('action', function ($row) {
        })
        ->rawColumns(['action'])
        ->make(true);
}

    public function addResignEmployees(Request $request)
    {
        $empId     = $request->input('empadd_userid');
        $email     = $request->input('email');
        $password  = $request->input('password');

        $employee = DB::table('employees')
            ->where('emp_id', $empId)
            ->where('deleted', '0')
            ->where('is_resigned', 1)
            ->first();

        if (!$employee) {
            return response()->json(['errors' => ['Employee not found or not resigned.']]);
        }

        $loggedInUser = Auth::user();

        if ($loggedInUser->email !== $email) {
            return response()->json(['errors' => ['Email does not match the logged-in user.']]);
        }

        if (!Hash::check($password, $loggedInUser->password)) {
            return response()->json(['errors' => ['Invalid Password.']]);
        }

        DB::table('employees')
            ->where('id', $employee->id)
            ->update([
                'is_resigned'        => 0,
                'updated_at'         => Carbon::now(),
            ]);

        EmployeeAssignBy::create([
            'emp_id'        => $employee->emp_id,
            'emp_assign_id' => Auth::id(),
            'updated_by'    => Auth::id(),
        ]);

        return response()->json(['success' => 'Employee re-assigned successfully.']);
    }
}
