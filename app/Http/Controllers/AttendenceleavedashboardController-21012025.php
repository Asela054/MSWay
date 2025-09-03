<?php

namespace App\Http\Controllers;

use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AttendenceleavedashboardController extends Controller
{
    public function index()
    {
        $selectedmonth = date('Y-m');

        // get most department wise attendance

        $mostattendance = DB::table('attendances')
            ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->select(
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.emp_department',
                'departments.name as dept_name',
                DB::raw('YEAR(attendances.date) as year'),
                DB::raw('MONTH(attendances.date) as month'),
                DB::raw('MIN(attendances.timestamp) as first_checkin'),
                DB::raw('MAX(attendances.timestamp) as lasttimestamp')
            );

        $mostattendance->whereYear('attendances.date', '=', date('Y', strtotime($selectedmonth)))
            ->whereMonth('attendances.date', '=', date('m', strtotime($selectedmonth)));


        $mostattendance->groupBy('attendances.date', 'attendances.emp_id');

        $datamostattendance = $mostattendance->get();

        $groupedDatamostattendance = [];
        foreach ($datamostattendance as $row) {
            $monthKey = sprintf('%04d-%02d', $row->year, $row->month);
            if (!isset($groupedDatamostattendance[$row->dept_name])) {
                $groupedDatamostattendance[$row->dept_name] = [
                    'dept_id' => $row->emp_department,
                    'dept_name' => $row->dept_name,
                    'attendance_count' => [],
                ];
            }
            if (!isset($groupedDatamostattendance[$row->dept_name]['attendance_count'][$monthKey])) {
                $groupedDatamostattendance[$row->dept_name]['attendance_count'][$monthKey] = 0;
            }

            $groupedDatamostattendance[$row->dept_name]['attendance_count'][$monthKey] += 1;
        }
        $departmentWithMostAttendance = collect($groupedDatamostattendance)
            ->sortByDesc('attendance_count')
            ->take(5)
            ->toArray();


        // get most leaves data
        $getmostleaves = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'), 'employees.emp_etfno', 'employees.emp_name_with_initial')
            ->leftJoin('employees', 'employees.emp_id', '=', 'leaves.emp_id')
            ->where('leaves.status', 'Approved')
            ->where('leaves.leave_from', 'like', $selectedmonth . '%')
            ->groupBy('leaves.emp_id')
            ->orderBy('total', 'desc') 
            ->limit(5) 
            ->get();


        // get most ot
        $getmostot = DB::table('ot_approved')
            ->select(DB::raw('SUM(hours) as normaltotal'),DB::raw('SUM(double_hours) as doubletotal'), 'employees.emp_etfno', 'employees.emp_name_with_initial')
            ->leftJoin('employees', 'employees.emp_id', '=', 'ot_approved.emp_id')
            ->where('ot_approved.date', 'like', $selectedmonth.'%')
            ->groupBy('ot_approved.emp_id')
            ->orderBy('normaltotal', 'desc') 
            ->limit(5) 
            ->get();

        return view('Dashboard.attendance', compact('departmentWithMostAttendance', 'getmostleaves','getmostot'));
    }
}
