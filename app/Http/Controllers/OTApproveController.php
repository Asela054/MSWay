<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use App\OtApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateInterval;
use DateTime;

class OTApproveController extends Controller
{
     public function ot_approve(Request $request)
    {
        $permission = Auth::user()->can('ot-approve');
        if(!$permission){
            abort(403);
        }

        return view('Attendent.ot_approve');
    }

    public function get_ot_details(Request $request)
    {
        $permission = Auth::user()->can('ot-approve');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = Request('department');
        $employee = Request('employee');
        $location = Request('location');
        $from_date = Request('from_date');
        $to_date = Request('to_date');


        $sql = "SELECT at1.*,  
                Min(at1.timestamp) as first_checkin,
                Max(at1.timestamp) as lasttimestamp,
                employees.emp_shift,
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                employees.emp_department,
                employees.calling_name,
                employees.emp_id,
                shift_types.onduty_time,
                shift_types.offduty_time,
                shift_types.saturday_onduty_time,
                shift_types.saturday_offduty_time,
                shift_types.shift_name,
                branches.location as b_location,
                departments.name as dept_name
                FROM attendances  as at1
                join employees on employees.emp_id = at1.uid
                left join shift_types ON employees.emp_shift = shift_types.id
                left join branches ON at1.location = branches.id
                left join departments ON employees.emp_department = departments.id
                WHERE at1.deleted_at IS NULL "; 

        if ($department != '') {
            $sql .= " AND employees.emp_department = '$department'";
        }

        if ($employee != '') {
            $sql .= " AND employees.emp_id = '$employee'";
        }

        if ($location != '') {
            $sql .= " AND at1.location = '$location'";
        }

        if ($from_date != '') {
            $sql .= " AND at1.date >= '$from_date'";
        }

        if ($to_date != '') {
            $sql .= " AND at1.date <= '$to_date'";
        }

        $sql .= " GROUP BY at1.uid, at1.date ORDER BY at1.date ";

        $attendance_data = DB::select($sql);

        //dd($sql);

        $ot_data = array();

        foreach ($attendance_data as $att) {

             $employeeObj = (object)[
            'emp_id' => $att->emp_id,
            'emp_name_with_initial' => $att->emp_name_with_initial,
            'calling_name' => $att->calling_name
        ];

            $emp_id = $att->uid;
            $date = $att->date;


            $shift_detail = DB::table('employeeshiftdetails')
                ->join('shift_types', 'employeeshiftdetails.shift_id', '=', 'shift_types.id')
                ->select('employeeshiftdetails.*', 'shift_types.onduty_time', 'shift_types.offduty_time') 
                ->where('employeeshiftdetails.emp_id', $emp_id)
                ->whereDate('employeeshiftdetails.date_from', '<=', $date)
                ->whereDate('employeeshiftdetails.date_to', '>=', $date)
                ->first();

                if ($shift_detail) {
                    $on_duty_time = $shift_detail->onduty_time;
                    $off_duty_time = $shift_detail->offduty_time;
                }
                else{
                    $on_duty_time = $att->onduty_time;
                    $off_duty_time =  $att->offduty_time;
                }

            $ot_hours = (new \App\Attendance)->get_ot_hours_by_date($emp_id, $att->lasttimestamp, $att->first_checkin, $date,  $on_duty_time, $off_duty_time, $att->emp_department);
            //$ot_hours = (new \App\Attendance)->get_ot_hours_by_date_morning_evening($emp_id, $att->lasttimestamp, $att->first_checkin, $date, $att->onduty_time, $att->offduty_time, $att->emp_department);
            // $is_approved = (new \App\OtApproved)->is_exists_in_ot_approved($emp_id, $date);
            // //if ot_breakdown is a key in the array
            // if (array_key_exists('ot_breakdown', $ot_hours)) {

            //     $ot_breakdown = array('ot_breakdown'=> $ot_hours['ot_breakdown'], 'is_approved' => $is_approved);

            //     $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
            //     $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

            //     //push ot_breakdown to ot_data
            //     if (!empty($ot_breakdown)) {
            //         array_push($ot_data, $ot_breakdown);
            //     }

            // }
            if(empty($ot_hours['ot_breakdown'])) {
                continue; // Skip if no OT hours found
            }

            if(count($ot_hours['ot_breakdown']) == 2) {
                $OTmorningfrom = Carbon::parse($ot_hours['ot_breakdown'][0]['from_24']);
                $OTeveningfrom = Carbon::parse($ot_hours['ot_breakdown'][1]['from_24']);
                $is_approved_morning = (new \App\OtApproved)->is_exists_in_ot_approved($emp_id, $date, $OTmorningfrom);
                $is_approved_evening = (new \App\OtApproved)->is_exists_in_ot_approved($emp_id, $date, $OTeveningfrom);
            } else {
                $OTeveningfrom = Carbon::parse($ot_hours['ot_breakdown'][0]['from_24']);
                $is_approved_evening = (new \App\OtApproved)->is_exists_in_ot_approved($emp_id, $date, $OTeveningfrom);
            }
            //$ot_hours = (new \App\Attendance)->get_ot_hours_by_date_morning_evening($emp_id, $att->lasttimestamp, $att->first_checkin, $date, $att->onduty_time, $att->offduty_time, $att->emp_department);
            //if ot_breakdown is a key in the array
            if (array_key_exists('ot_breakdown', $ot_hours)) {
                if(count($ot_hours['ot_breakdown']) == 2) {
                    $ot_breakdown_morning = array('ot_breakdown'=> $ot_hours['ot_breakdown'][0], 'is_approved' => $is_approved_morning);
                    $ot_breakdown_evening = array('ot_breakdown'=> $ot_hours['ot_breakdown'][1], 'is_approved' => $is_approved_evening);
                } else {
                    $ot_breakdown_evening = array('ot_breakdown'=> $ot_hours['ot_breakdown'][0], 'is_approved' => $is_approved_evening);
                }
                
                $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
                $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

                if(count($ot_hours['ot_breakdown']) == 2) {
                    //push ot_breakdown to ot_data
                    if (!empty($ot_breakdown_morning)) {
                        array_push($ot_data, $ot_breakdown_morning);
                    }
                    if (!empty($ot_breakdown_evening)) {
                        array_push($ot_data, $ot_breakdown_evening);
                    }
                }
                else {
                    //push ot_breakdown to ot_data
                    if (!empty($ot_breakdown_evening)) {
                        array_push($ot_data, $ot_breakdown_evening);
                    }
                }
                // dd($ot_data);
            }
        }
        return response()->json(['ot_data' => $ot_data]);
    }
	
    public function ot_approve_post(Request $request)
    {
        $permission = Auth::user()->can('ot-approve');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $checked = $request->ot_data;

        foreach ($checked as $ch) {

            $data = array(
                'emp_id' => $ch['emp_id'],
                'date' => $ch['date'],
                'from' => $ch['from'],
                'to' => $ch['to'],
                'hours' => $ch['hours'],
                'double_hours' => $ch['double_hours'],
                'triple_hours' => $ch['triple_hours'],
                'is_holiday' => $ch['is_holiday'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            OtApproved::query()->insert($data);

        }

        return response()->json(['success' => 'OT Approved']);

    }

    public function ot_approved()
    {
        $permission = Auth::user()->can('ot-list');
        if(!$permission){
            abort(403);
        }
        return view('Attendent.ot_approved');
    }

    public function ot_approved_list(Request $request)
    {
        $permission = Auth::user()->can('ot-list');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $type = $request->get('type');

        $att_query = 'SELECT ot_approved.*,  
                employees.emp_shift,  
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                employees.calling_name,
                employees.emp_department, 
                branches.location as b_location,
                departments.name as dept_name 
                FROM ot_approved
                left join `employees` on `employees`.`emp_id` = ot_approved.emp_id
                left join shift_types ON employees.emp_shift = shift_types.id  
                left join departments ON employees.emp_department = departments.id 
                left join branches ON employees.emp_location = branches.id 
                WHERE employees.deleted = 0 AND employees.is_resigned = 0
                ';

        if ($department != '') {
            $att_query .= ' AND employees.emp_department = ' . $department;
        }

        if ($employee != '') {
            $att_query .= ' AND employees.emp_id = ' . $employee;
        }

        if ($location != '') {
            $att_query .= ' AND employees.emp_location = ' . $location;
        }

        if ($from_date != '' && $to_date != '') {
           $att_query .= ' AND ot_approved.date BETWEEN "' . $from_date . '" AND "' . $to_date . '"';
        }

        $data = DB::select($att_query);

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $permission = Auth::user()->can('ot-delete');
                if($permission){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Delete" class="delete_btn btn btn-danger btn-sm"><i class="fa fa-trash"></i> </a>';
                }
                return $btn;
            })
            //is_holiday
            ->addColumn('is_holiday', function ($row) {
                if ($row->is_holiday == 1) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })
            //form
            ->addColumn('from', function ($row) {
                return date('Y-m-d H:i', strtotime($row->from));
            })
            //to
            ->addColumn('to', function ($row) {
                return date('Y-m-d H:i', strtotime($row->to));
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
            ->rawColumns(['action'])
            ->make(true);
    }

    public function ot_approved_list_monthly(Request $request)
    {
        $permission = Auth::user()->can('ot-list');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $type = $request->get('type');

        $att_query = 'SELECT ot_approved.*,  
                employees.emp_shift,  
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                employees.emp_department, 
                branches.location as b_location,
                departments.name as dept_name 
                FROM ot_approved
                join `employees` on `employees`.`emp_id` = ot_approved.emp_id
                left join shift_types ON employees.emp_shift = shift_types.id  
                left join departments ON employees.emp_department = departments.id 
                left join branches ON employees.emp_location = branches.id 
                WHERE 1 = 1
                ';

        if ($department != '') {
            $att_query .= ' AND employees.emp_department = ' . $department;
        }

        if ($employee != '') {
            $att_query .= ' AND employees.emp_id = ' . $employee;
        }

        if ($location != '') {
            $att_query .= ' AND employees.emp_location = ' . $location;
        }

        if ($from_date != '' && $to_date != '') {
            $att_query .= ' AND ot_approved.date BETWEEN "' . $from_date . '" AND "' . $to_date . '"';
        }

        $data = DB::select($att_query);

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $permission = Auth::user()->can('ot-delete');
                if($permission){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Delete" class="delete_btn btn btn-danger btn-sm"><i class="fa fa-trash"></i> </a>';
                }
                return $btn;
            })
            //is_holiday
            ->addColumn('is_holiday', function ($row) {
                if ($row->is_holiday == 1) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })
            //form
            ->addColumn('from', function ($row) {
                return date('Y-m-d h:i A', strtotime($row->from));
            })
            //to
            ->addColumn('to', function ($row) {
                return date('Y-m-d h:i A', strtotime($row->to));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function ot_approved_delete(Request $request)
    {
        $permission = Auth::user()->can('ot-delete');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = $request->get('id');
        OtApproved::query()->where('id', $id)->delete();
        return response()->json([
            'success' => true,
            'msg' => 'Deleted']);
    }

    // public function get_ot_details_1402(Request $request)
    // {
    //     $permission = Auth::user()->can('ot-approve');
    //     if(!$permission){
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     }

    //     $department = Request('department');
    //     $employee = Request('employee');
    //     $location = Request('location');
    //     $from_date = Request('from_date');
    //     $to_date = Request('to_date');


    //     $sql = "SELECT at1.*,  
    //             Min(at1.timestamp) as first_checkin,
    //             Max(at1.timestamp) as lasttimestamp,
    //             employees.emp_shift,
    //             employees.id as emp_auto_id,
    //             employees.emp_name_with_initial,
    //             employees.emp_department,
    //             shift_types.onduty_time,
    //             shift_types.offduty_time,
    //             shift_types.saturday_onduty_time,
    //             shift_types.saturday_offduty_time,
    //             shift_types.shift_name,
    //             branches.location as b_location,
    //             departments.name as dept_name
    //             FROM `attendances`  as `at1`
    //             join `employees` on `employees`.`emp_id` = `at1`.`uid`
    //             left join shift_types ON employees.emp_shift = shift_types.id
    //             left join branches ON at1.location = branches.id
    //             left join departments ON employees.emp_department = departments.id
    //             WHERE 1 = 1";

    //     if ($department != '') {
    //         $sql .= " AND employees.emp_department = '$department'";
    //     }

    //     if ($employee != '') {
    //         $sql .= " AND employees.emp_id = '$employee'";
    //     }

    //     if ($location != '') {
    //         $sql .= " AND at1.location = '$location'";
    //     }

    //     if ($from_date != '') {
    //         $sql .= " AND at1.date >= '$from_date'";
    //     }

    //     if ($to_date != '') {
    //         $sql .= " AND at1.date <= '$to_date'";
    //     }

    //     $sql .= " GROUP BY at1.uid, at1.date ORDER BY at1.date ";

    //     $attendance_data = DB::select($sql);

    //     //dd($sql);

    //     $ot_data = array();

    //     foreach ($attendance_data as $att) {

    //         $emp_id = $att->uid;
    //         $date = $att->date;


    //         $shift_detail = DB::table('employeeshiftdetails')
    //             ->join('shift_types', 'employeeshiftdetails.shift_id', '=', 'shift_types.id')
    //             ->select('employeeshiftdetails.*', 'shift_types.onduty_time', 'shift_types.offduty_time') 
    //             ->where('employeeshiftdetails.emp_id', $emp_id)
    //             ->whereDate('employeeshiftdetails.date_from', '<=', $date)
    //             ->whereDate('employeeshiftdetails.date_to', '>=', $date)
    //             ->first();

    //             if ($shift_detail) {
    //                 $on_duty_time = $shift_detail->onduty_time;
    //                 $off_duty_time = $shift_detail->offduty_time;
    //             }
    //             else{
    //                 $on_duty_time = $att->onduty_time;
    //                 $off_duty_time =  $att->offduty_time;
    //             }

    //         $ot_hours = (new \App\Attendance)->get_ot_hours_by_date($emp_id, $att->lasttimestamp, $att->first_checkin, $date,  $on_duty_time, $off_duty_time, $att->emp_department);
    //         //$ot_hours = (new \App\Attendance)->get_ot_hours_by_date_morning_evening($emp_id, $att->lasttimestamp, $att->first_checkin, $date, $att->onduty_time, $att->offduty_time, $att->emp_department);
    //         $is_approved = (new \App\OtApproved)->is_exists_in_ot_approved($emp_id, $date);
    //         //if ot_breakdown is a key in the array
    //         if (array_key_exists('ot_breakdown', $ot_hours)) {

    //             $ot_breakdown = array('ot_breakdown'=> $ot_hours['ot_breakdown'], 'is_approved' => $is_approved);

    //             $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
    //             $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

    //             //push ot_breakdown to ot_data
    //             if (!empty($ot_breakdown)) {
    //                 array_push($ot_data, $ot_breakdown);
    //             }

    //         }

    //     }
    //     return response()->json(['ot_data' => $ot_data]);
    // }
}
