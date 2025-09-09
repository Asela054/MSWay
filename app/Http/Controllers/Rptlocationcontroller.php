<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use DB;
use Session;

class Rptlocationcontroller extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('department-wise-ot-report');
        if (!$permission) {
            abort(403);
        }
        $locations = DB::table('branches')->select('*')->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial')
        ->where('deleted',0)
        ->where('is_resigned',0)
        ->get();

        if (!Session::has('company_name')) {
            $company_name = DB::table('companies')->value('name');
            Session::put('company_name', $company_name);
        } else {
            $company_name = Session::get('company_name');
        }

        return view('departmetwise_reports.joballocationreport', compact('locations','employees','company_name'));
    }

    public function joblocationreport()
    {
        $location = Request('location');
        $from_date = Request('from_date');
        $to_date = Request('to_date');
        $employee_f = Request('employee_f');
        $attendacetype = Request('attendacetype');

        $results = DB::table('job_attendance')
            ->leftjoin('employees', 'job_attendance.employee_id', '=', 'employees.emp_id')
            ->leftjoin('branches', 'job_attendance.location_id', '=', 'branches.id')
            ->select('job_attendance.*','employees.emp_name_with_initial','employees.emp_id','employees.calling_name','branches.location')
            ->whereIn('job_attendance.status', [1, 2])
            ->where('job_attendance.approve_status', 1);

        if (!empty($location)) {
            $results->where('job_attendance.location_id', $location);
        }

        if (!empty($from_date) && !empty($to_date)) {
            $results->whereBetween('job_attendance.attendance_date', [$from_date, $to_date]);
        }

        if (!empty($employee_f)) {
            $results->where('job_attendance.employee_id', $employee_f);
        }
        if (!empty($attendacetype)) {
            $results->where('job_attendance.location_status', $attendacetype);
        }
        $datalist = $results->get();

        $datalist->transform(function ($row) {
        $row->employee_display = EmployeeHelper::getDisplayName($row);
            return $row;
        });

        return response()->json(['data' => $datalist]);

    }
}
