<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class RptOTController extends Controller
{
    public function ot_report(){
        $permission = Auth::user()->can('ot-report');
        if(!$permission){
            abort(403);
        }

        return view('Report.ot_report' );
    }

    public function ot_report_list(Request $request)
    {
        $permission = Auth::user()->can('ot-report');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $type = $request->get('type');

        $att_query = 'SELECT at1.*, 
                Min(at1.timestamp) as first_checkin,
                Max(at1.timestamp) as lasttimestamp, 
                employees.emp_shift,  
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                employees.emp_department,
                shift_types.onduty_time, 
                shift_types.offduty_time,
                shift_types.saturday_onduty_time,
                shift_types.saturday_offduty_time,
                shift_types.shift_name,
                branches.location as b_location,
                departments.name as dept_name 
                FROM `attendances`  as `at1`
                join `employees` on `employees`.`emp_id` = `at1`.`uid` 
                left join shift_types ON employees.emp_shift = shift_types.id 
                left join branches ON at1.location = branches.id 
                left join departments ON employees.emp_department = departments.id 
                WHERE 1 = 1
                ';

        if($department != ''){
            $att_query .= ' AND employees.emp_department = '.$department;
        }

        if($employee != ''){
            $att_query .= ' AND employees.emp_id = '.$employee;
        }

        if($location != ''){
            $att_query .= ' AND employees.emp_location = '.$location;
        }

        if($from_date != '' && $to_date != ''){
            $att_query .= ' AND at1.date BETWEEN "'.$from_date.'" AND "'.$to_date.'"';
        }

        $att_query .= ' group by at1.uid, at1.date ';

        //dd($att_query);

        $data = DB::select($att_query);

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('shift_details', function($row) {
                 return $shift = $row->shift_name;
            })
            ->addColumn('record_date', function($row) {
                $date_arr = explode(' ', $row->date);
                return $date_arr[0];
            })
            //check in time
            ->addColumn('check_in_time', function($row) {
                $first_time_stamp = date('h:i:s A', strtotime($row->timestamp));
                return $first_time_stamp;
            })
            ->addColumn('check_out_time', function($row) {
                if($row->timestamp != $row->lasttimestamp) {
                    $last_time_stamp = date('h:i:s A', strtotime($row->lasttimestamp));
                    return $last_time_stamp;
                }
                else{
                    return '';
                }
            })
            ->addColumn('work_hours', function($row) {
                $off_time = Carbon::parse($row->lasttimestamp);
                $on_time = Carbon::parse($row->timestamp);
                $work_hours = $off_time->diffInMinutes($on_time);
                return $work_hours_in_h = number_format($work_hours/ 60, 2) ;
            })
            ->addColumn('normal_rate_otwork_hrs', function($row) {

                $off_time = $row->lasttimestamp;
                $on_time = $row->first_checkin;
                $record_date = $row->date;

                $on_duty_time = $row->onduty_time;
                $off_duty_time = $row->offduty_time;

                //Attendance get_ot_hours_by_date()
                $ot_hours = (new \App\Attendance)->get_ot_hours_by_date($row->uid, $off_time, $on_time, $record_date, $on_duty_time, $off_duty_time, $row->emp_department);

                $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
                $ot = number_format($normal_rate_otwork_hrs, 2);
                //view more link
                $view_more = '<a href="javascript:void(0);" class="view_more text-xs " data-id="'.$row->uid.'" data-date="'.$row->date .'" >View</a>';
                return $ot.' '.$view_more;
            })
            ->addColumn('double_rate_otwork_hrs', function($row) {

                $off_time = $row->lasttimestamp;
                $on_time = $row->timestamp;
                $record_date = $row->date;

                $on_duty_time = $row->onduty_time;
                $off_duty_time = $row->offduty_time;

                //Attendance get_ot_hours_by_date()
                $ot_hours = (new \App\Attendance)->get_ot_hours_by_date($row->uid, $off_time, $on_time, $record_date, $on_duty_time, $off_duty_time, $row->emp_department);

                $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

                return number_format($double_rate_otwork_hrs, 2);
            })
            ->rawColumns(['action', 'work_hours', 'check_in_time' , 'check_out_time', 'normal_rate_otwork_hrs', 'double_rate_otwork_hrs', 'record_date', 'shift_details'])
            ->make(true);
    }

    public function ot_report_list_view_more(Request $request) {
        $permission = Auth::user()->can('ot-report');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $emp_id = $request->emp_id;
        $date = $request->date;

        $sql = "SELECT at1.*, 
                Min(at1.timestamp) as first_checkin,
                Max(at1.timestamp) as lasttimestamp, 
                employees.emp_shift,  
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                employees.emp_department,
                shift_types.onduty_time, 
                shift_types.offduty_time,
                shift_types.saturday_onduty_time,
                shift_types.saturday_offduty_time,
                shift_types.shift_name,
                branches.location as b_location,
                departments.name as dept_name 
                FROM `attendances`  as `at1`
                join `employees` on `employees`.`emp_id` = `at1`.`uid` 
                left join shift_types ON employees.emp_shift = shift_types.id 
                left join branches ON at1.location = branches.id 
                left join departments ON employees.emp_department = departments.id
                WHERE at1.uid = '$emp_id' 
                AND at1.date = '$date'
                group by at1.uid, at1.date
                ";

        $attendance_data = DB::select($sql);

        //get_ot_hours_by_date()
        $ot_hours = (new \App\Attendance)->get_ot_hours_by_date($emp_id, $attendance_data[0]->lasttimestamp, $attendance_data[0]->first_checkin, $date, $attendance_data[0]->onduty_time, $attendance_data[0]->offduty_time, $attendance_data[0]->emp_department);

        $ot_breakdown = $ot_hours['ot_breakdown'];
        $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
        $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

        $att_data = array(
            'employee' => $attendance_data[0]->emp_name_with_initial,
            'check_in_time' => $attendance_data[0]->first_checkin,
            'check_out_time' => $attendance_data[0]->lasttimestamp,
        );

        //return json  data
        return response()->json([
            'ot_breakdown' => $ot_breakdown,
            'normal_rate_otwork_hrs' => $normal_rate_otwork_hrs,
            'double_rate_otwork_hrs' => $double_rate_otwork_hrs,
            'att_data' => $att_data,
        ]);

    }

    public function ot_report_list_month(Request $request)
    {
        $permission = Auth::user()->can('ot-report');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $type = $request->get('type');
        $month = $request->get('month');

        $date = new DateTime("$month-01");
        $closingday = $date->format('Y-m-t');

        $emp_query = 'SELECT  
                employees.*,  
                employees.id as emp_auto_id, 
                shift_types.onduty_time, 
                shift_types.offduty_time,
                shift_types.shift_name,
                branches.location as b_location,
                departments.name as dept_name 
                FROM `employees`   
                left join shift_types ON employees.emp_shift = shift_types.id 
                left join branches ON employees.emp_location = branches.id 
                left join departments ON employees.emp_department = departments.id 
                WHERE employees.deleted = 0  
                ';

        if($department != ''){
            $emp_query .= ' AND employees.emp_department = '.$department;
        }

        if($employee != ''){
            $emp_query .= ' AND employees.emp_id = '.$employee;
        }

        if($location != ''){
            $emp_query .= ' AND employees.emp_location = '.$location;
        }

        $data = DB::select($emp_query);

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('month', function ($row) use ($month) {
                return $month;
            })
            ->addColumn('work_days', function($row) use ($month, $closingday) {
                $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month, $closingday);
                return $work_days;
            })
            ->addColumn('leave_days', function($row) use ($month, $closingday){
                $leave_days = (new \App\Attendance)->get_leave_days($row->emp_id, $month, $closingday);
                return $leave_days;
            })
            ->addColumn('no_pay_days', function ($row) use ($month, $closingday) {
                $no_pay_days = (new \App\Attendance)->get_no_pay_days($row->emp_id, $month, $closingday);
                return $no_pay_days;
            })
            ->addColumn('normal_rate_otwork_hrs', function ($row) use ($month) {
                $ot_hours = (new \App\Attendance)->get_ot_hours_approved($row->emp_id, $month);
                $normal_ot_hours = $ot_hours['normal_rate_otwork_hrs'];

                return number_format($normal_ot_hours, 2);
            })
            ->addColumn('double_rate_otwork_hrs', function ($row) use ($month) {

                $ot_hours = (new \App\Attendance)->get_ot_hours_approved($row->emp_id, $month);
                $double_ot_hours = $ot_hours['double_rate_otwork_hrs'];

                return number_format($double_ot_hours, 2);
            })
             ->addColumn('employee_display', function ($row) {
                   return EmployeeHelper::getDisplayName($row);
                   
            })
            ->filterColumn('employee_display', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('employees.emp_name_with_initial', 'like', "%{$keyword}%")
                    ->orWhere('employees.calling_name', 'like', "%{$keyword}%")
                    ->orWhere('employees.emp_id', 'like', "%{$keyword}%");
                });
            })

            ->rawColumns(['action',
                'work_days',
                'leave_days',
                'no_pay_days',
                'employee_display',
                'normal_rate_otwork_hrs',
                'double_rate_otwork_hrs'
            ])
            ->make(true);
    }
}
