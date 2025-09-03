<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use DB;

class Rptlocationcontroller extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('department-wise-ot-report');
        if (!$permission) {
            abort(403);
        }
        $locations = DB::table('job_location')->select('*')->whereIn('status',[1,2])->get();
        $employees=DB::table('employees')->select('id','emp_name_with_initial')->where('deleted',0)->get();
        return view('departmetwise_reports.joballocationreport', compact('locations','employees'));
    }

    public function joblocationreport()
    {
        $location = Request('location');
        $from_date = Request('from_date');
        $to_date = Request('to_date');
        $employee_f = Request('employee_f');

        $results = DB::table('job_attendance')
            ->join('job_location', 'job_attendance.location_id', '=', 'job_location.id')
            ->join('employees', 'job_attendance.employee_id', '=', 'employees.id')
            ->select(
                'job_attendance.*',
                'job_location.location_name',
                'employees.emp_name_with_initial'
            );

        if (!empty($location)) {
            $results->where('job_attendance.location_id', $location);
        }

        if (!empty($from_date) && !empty($to_date)) {
            $results->whereBetween('job_attendance.attendance_date', [$from_date, $to_date]);
        }

        if (!empty($employee_f)) {
            $results->where('job_attendance.employee_id', $employee_f);
        }
        $datalist = $results->get();

        return response()->json(['data' => $datalist]);

    }
}
