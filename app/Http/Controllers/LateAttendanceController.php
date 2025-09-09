<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use App\LateAttendance;
use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateTime;

class LateAttendanceController extends Controller
{
    public function late_attendance_by_time()
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.late_attendance_by_time');
    }

    // late attendace mark data table
    public function attendance_by_time_report_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        // Read parameters
        $department = $request->get('department');
        $company = $request->get('company');
        $employee = $request->get('employee');
        $late_type = $request->get('late_type');
        $from_date = date('Y-m-d', strtotime($request->get('from_date'))) . ' 00:00:00';
        $to_date = date('Y-m-d', strtotime($request->get('to_date'))) . ' 00:00:00';


        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'] ?? 0;
        $columnName = $columnName_arr[$columnIndex]['data'] ?? 'id';
        $columnSortOrder = $order_arr[0]['dir'] ?? 'asc';
        $searchValue = $search_arr['value'] ?? '';

        // Get late time threshold if specified
        $late_time_threshold = null;
        if ($late_type) {
            $late_time = DB::table('late_types')->where('id', $late_type)->first();
            if ($late_time) {
                $late_time_threshold = $late_time->time_from;
            }
        }


        // Base query without late time filter (for counting)
        $baseQuery = DB::table('attendances as at1')
            ->select(
                'at1.id',
                'at1.uid',
                'at1.date',
                DB::raw('MIN(at1.timestamp) as first_checkin'),
                DB::raw('MAX(at1.timestamp) as last_checkout'),
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'branches.location as branch_location',
                'branches.id as branch_id',
                'departments.name as department_name',
                'departments.id as department_id'
            )
            ->join('employees', 'employees.emp_id', '=', 'at1.uid')
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('companies', 'companies.id', '=', 'departments.company_id')
            ->whereNull('at1.deleted_at')
            ->groupBy('at1.uid', 'at1.date');

        // Apply filters to base query
        if ($searchValue) {
            $baseQuery->where(function($q) use ($searchValue) {
                $q->where('employees.emp_id', 'like', "%$searchValue%")
                ->orWhere('employees.emp_name_with_initial', 'like', "%$searchValue%")
                 ->orWhere('employees.calling_name', 'like', "%$searchValue%")
                ->orWhere('at1.timestamp', 'like', "%$searchValue%")
                ->orWhere('companies.name', 'like', "%$searchValue%")
                ->orWhere('branches.location', 'like', "%$searchValue%")
                ->orWhere('departments.name', 'like', "%$searchValue%");
            });
        }

        if ($department) {
            $baseQuery->where('departments.id', $department);
        }
        if ($company) {
            $baseQuery->where('employees.emp_company', $company);
        }
        if ($employee) {
            $baseQuery->where('at1.uid', $employee);
        }
        if ($from_date && $to_date) {
            $baseQuery->whereBetween('at1.date', [$from_date, $to_date]);
        } elseif ($from_date) {
            $baseQuery->where('at1.date', '>=', $from_date);
        } elseif ($to_date) {
            $baseQuery->where('at1.date', '<=', $to_date);
        }

        $totalRecords = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
            ->mergeBindings($baseQuery)
            ->count();

        $filteredQuery = clone $baseQuery;
        if ($late_time_threshold) {
            $filteredQuery->havingRaw("TIME(MIN(at1.timestamp)) > ?", [$late_time_threshold]);
        }

        $totalFiltered = DB::table(DB::raw("({$filteredQuery->toSql()}) as sub"))
            ->mergeBindings($filteredQuery)
            ->count();



        $filteredQuery->orderBy($columnName, $columnSortOrder);
        $records = $filteredQuery->skip($start)->take($rowperpage)->get();

        $data = [];

        foreach ($records as $record) {
            $first_checkin = Carbon::parse($record->first_checkin);
            $last_checkout = Carbon::parse($record->last_checkout);
            
             $employeeObj = (object)[
            'emp_id' => $record->emp_id,
            'emp_name_with_initial' => $record->emp_name_with_initial,
            'calling_name' => $record->calling_name
        ];

             $is_late_marked = DB::table('employee_late_attendances')
            ->where('emp_id', $record->emp_id)
            ->where('date', $record->date)
            ->exists();

            $data[] = [
                'id' => $record->id,
                'uid' => $record->uid,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'employee_display' => EmployeeHelper::getDisplayName($employeeObj),
                'date' => $record->date,
                'timestamp' => $first_checkin->format('H:i'),
                'lasttimestamp' => $first_checkin->format('H:i') != $last_checkout->format('H:i') 
                                ? $last_checkout->format('H:i') 
                                : '',
                'workhours' => gmdate("H:i:s", $last_checkout->diffInSeconds($first_checkin)),
                'dept_name' => $record->department_name,
                'dept_id' => $record->department_id,
                'location' => $record->branch_location,
                'location_id' => $record->branch_id,
                'is_late_marked' => $is_late_marked ? 1 : 0
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data
        ]);
    }

    public function late_types_sel2(Request $request)
    {
        if ($request->ajax()) {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = DB::query()
                ->where('name', 'LIKE', '%' . Input::get("term") . '%')
                ->from('late_types')
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT id as id'), DB::raw('name as text')]);

            $count = DB::query()
                ->where('name', 'LIKE', '%' . Input::get("term") . '%')
                ->from('late_types')
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->select([DB::raw('DISTINCT id as id'), DB::raw('name as text')])
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

    public function lateAttendance_mark_as_late(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $selected_cb = $request->selected_cb;

        if (empty($selected_cb)) {
            return response()->json(['status' => false, 'msg' => 'Select one or more employees']);
        }

        $data_arr = array();
        foreach ($selected_cb as $cr) {

            if ($cr['lasttimestamp'] != '') {

                    $shiftType = DB::table('employees')
                                ->join('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
                                ->select('shift_types.onduty_time')
                                ->where('employees.emp_id', $cr['uid']) 
                                ->first();


                        if ($shiftType && $shiftType->onduty_time) {
                            $ondutyTime = new DateTime($shiftType->onduty_time);
                            $checkInTime = new DateTime($cr['timestamp']);

                            $interval = $checkInTime->diff($ondutyTime);
                            $minutesDifference = ($interval->h * 60) + $interval->i;

                            // Check if check-in time is after on-duty time
                            if ($checkInTime > $ondutyTime) {
                                $interval = $checkInTime->diff($ondutyTime);
                                $minutesDifference = ($interval->h * 60) + $interval->i;

                                $late_minutes_data[] = array(
                                    'attendance_id' => $cr['id'],
                                    'emp_id' => $cr['uid'],
                                    'attendance_date' => $cr['date'],
                                    'minites_count' => $minutesDifference,
                                );
                            }
                        }
                $data_arr[] = array(
                    'attendance_id' => $cr['id'],
                    'emp_id' => $cr['uid'],
                    'date' => $cr['date'],
                    'check_in_time' => $cr['timestamp'],
                    'check_out_time' => $cr['lasttimestamp'],
                    'working_hours' => $cr['workhours'],
                    'created_by' => Auth::id(),
                );

                DB::table('employee_late_attendances')
                    ->where('attendance_id', $cr['id'])
                    ->where('emp_id', $cr['uid'])
                    ->where('date', $cr['date'])
                    ->where('check_in_time', $cr['timestamp'])
                    ->where('check_out_time', $cr['lasttimestamp'])
                    ->delete();
            }
        }

        DB::table('employee_late_attendances')->insert($data_arr);

        if (!empty($late_minutes_data)) {
            DB::table('employee_late_attendance_minites')->insert($late_minutes_data);
        }
        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);
    }

    public function late_attendance_by_time_approve()
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-approve');
        if (!$permission) {
            abort(403);
        }

        $leave_types = DB::table('leave_types')->get();
        return view('Attendent.late_attendance_by_time_approve', compact('leave_types'));
    }

    public function attendance_by_time_approve_report_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        ## Read value
        $department = $request->get('department');
        $company = $request->get('company');
        $location = $request->get('location');
        $date = $request->get('date');


        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords_array = DB::select('
            SELECT COUNT(*) as acount
                FROM
                (
                    SELECT COUNT(*)
                    from `employee_late_attendances` as `ela` 
                    left join attendances as at1 on `at1`.`id` = `ela`.`attendance_id`  
                    left join `employees` on `at1`.`uid` = `employees`.`emp_id`  
                    left join `branches` on `at1`.`location` = `branches`.`id`
                    WHERE ela.is_approved = 0
                    group by `at1`.`uid`, `at1`.`date`  
                )t
            ');

        $totalRecords = $totalRecords_array[0]->acount;

        $query1 = 'SELECT COUNT(*) as acount ';
        $query2 = 'FROM `employee_late_attendances` as `ela` ';
        $query2 .= 'left join attendances as at1 on at1.`id` = `ela`.`id` ';
        $query2 .= 'join `employees` on `employees`.`emp_id` = `ela`.`emp_id` ';
        $query2 .= 'left join `branches` on `at1`.`location` = `branches`.`id` ';
        $query2 .= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
        $query2 .= 'left join `companies` on `companies`.`id` = `departments`.`company_id` ';
        $query2 .= 'WHERE 1 = 1 and ela.is_approved = 0 ';
        //$searchValue = 'Breeder Farm';
        if ($searchValue != '') {
            $query2 .= 'AND ';
            $query2 .= '( ';
            $query2 .= 'employees.emp_id like "' . $searchValue . '%" ';
            $query2 .= 'OR employees.emp_name_with_initial like "' . $searchValue . '%" ';
            $query2 .= 'OR ela.date like "' . $searchValue . '%" ';
            $query2 .= 'OR companies.name like "' . $searchValue . '%" ';
            $query2 .= 'OR branches.location like "' . $searchValue . '%" ';
            $query2 .= 'OR departments.name like "' . $searchValue . '%" ';
            $query2 .= ') ';
        }

        if ($department != '') {
            $query2 .= 'AND departments.id = "' . $department . '" ';
        }

        if ($company != '') {
            $query2 .= 'AND employees.emp_company = "' . $company . '" ';
        }

        // if ($location != '') {
        //     $query2 .= 'AND at1.location = "' . $location . '" ';
        // }

        if ($date != '') {
            $query2 .= 'AND ela.date = "' . $date . '" ';
        }

        $query6 = ' ';
        $query6 .= ' ';

        $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
        $query7 = 'ORDER BY ' . $columnName . ' ' . $columnSortOrder . ' ';

        //error_log($query1.$query2.$query6);

        $totalRecordswithFilter_arr = DB::select($query1 . $query2 . $query6);
        $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

        // Fetch records
        $query3 = 'select ela.*,   
            employees.emp_id ,
            employees.emp_name_with_initial ,
            `employees`.`calling_name`,
            branches.location as b_location,
            branches.id as b_location_id,
            departments.name as dept_name,  
            departments.id as dept_id  
              ';

        $records = DB::select($query3 . $query2 . $query6 . $query7 . $query5);
        //error_log($query3.$query2.$query6.$query7.$query5);
        //var_dump(sizeof($records));
        //die();
        $data_arr = array();

        foreach ($records as $record) {

              $employeeObj = (object)[
                'emp_id' => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'calling_name' => $record->calling_name
            ];

            $data_arr[] = array(
                "id" => $record->id,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "employee_display" => EmployeeHelper::getDisplayName($employeeObj),
                "date" => $record->date,
                "check_in_time" => date('H:i', strtotime($record->check_in_time)),
                "check_out_time" => date('H:i', strtotime($record->check_out_time)),
                "working_hours" => $record->working_hours,
                "dept_name" => $record->dept_name,
                "dept_id" => $record->dept_id,
                "location" => $record->b_location,
                "location_id" => $record->b_location_id,
                "is_approved_int" => $record->is_approved,
                "is_approved" => ($record->is_approved == 0) ? 'No' : 'Yes',
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function lateAttendance_mark_as_late_approve(Request $request)
    {
        // pubudu = 	3580.77

        $user = Auth::user();
        $permission = $user->can('late-attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $selected_cb = $request->selected_cb;
        $leave_type = $request->leave_type;

        if (empty($selected_cb)) {
            return response()->json(['status' => false, 'msg' => 'Select one or more employees']);
        }

        $id_arr = array();
        $date = '';

        foreach ($selected_cb as $cr) {
            $date = $cr['date'];

            array_push($id_arr, $cr['id']);

            DB::table('employee_late_attendances')
                ->where('id', $cr['id'])
                ->update(['is_approved' => 1]);

            $emp_data = DB::table('employee_late_attendances')
                ->find($cr['id']);

            //count this month leaves and to leaves table
            $d_count = DB::table('employee_late_attendances')
                ->where('date', $emp_data->date)
                ->where('emp_id', $emp_data->emp_id)
                ->count();

            
            switch (true) {
                case ($d_count == 1 || $d_count == 2):
                    //add short leave
                    $half_short = 0.25;
                    break;
                default:
                    //add half day
                    $half_short = 0.5;
            }



            $jobcategory = DB::table('employees')
                            ->leftjoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
                            ->select('job_categories.late_type','job_categories.short_leaves','job_categories.half_days','job_categories.late_attend_min')
                            ->where('employees.emp_id', $emp_data->emp_id) 
                            ->first();

            $latetype = $jobcategory->late_type; 
            $shortleave = $jobcategory->short_leaves; 
            $halfday = $jobcategory->half_days;   
            $minitescount = $jobcategory->late_attend_min; 

            if($latetype == 1){

                if(!empty($minitescount)){

                   
    
                    $totalMinutes = DB::table('employee_late_attendance_minites')
                                        ->where('emp_id', $emp_data->emp_id) 
                                        ->whereRaw("DATE_FORMAT(attendance_date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')", [$emp_data->date])
                                        ->where('attendance_date', '!=', $emp_data->date) 
                                        ->sum('minites_count');
                    
                    $attendanceminitesrecord = DB::table('employee_late_attendance_minites')
                                                ->select('id', 'attendance_id', 'emp_id', 'attendance_date', 'minites_count')
                                                ->where('emp_id',$emp_data->emp_id)
                                                ->where('attendance_date', '!=',$emp_data->date)
                                                ->first();
                    
                    $totalminitescount = $totalMinutes + $attendanceminitesrecord->minites_count;
    
                    if( $minitescount < $totalminitescount){
                        $leave = new Leave;
                        $leave->emp_id = $emp_data->emp_id;
                        $leave->leave_type = $leave_type;
                        $leave->leave_from = $emp_data->date;
                        $leave->leave_to = $emp_data->date;
                        $leave->no_of_days = '0';
                        $leave->half_short = '0';
                        $leave->reson = 'Late';
                        $leave->comment = '';
                        $leave->emp_covering = '';
                        $leave->leave_approv_person = Auth::id();
                        $leave->status = 'Pending';
                        $leave->save();
                    }
                    else{
    
                        $leave = new Leave;
                        $leave->emp_id = $emp_data->emp_id;
                        $leave->leave_type = $leave_type;
                        $leave->leave_from = $emp_data->date;
                        $leave->leave_to = $emp_data->date;
                        $leave->no_of_days = $half_short;
                        $leave->half_short = $half_short;
                        $leave->reson = 'Late';
                        $leave->comment = '';
                        $leave->emp_covering = '';
                        $leave->leave_approv_person = Auth::id();
                        $leave->status = 'Pending';
                        $leave->save();
        
                    }
                    
                }
              
            }
            elseif($latetype == 2){

                if($d_count <=  $shortleave)
                {
                    $leaveamount = 0.25;
                    $applyleavetype = $leave_type;
                }
                elseif($d_count <=  $halfday)
                {
                    $leaveamount = 0.5;
                    $applyleavetype = $leave_type;
                }
                else{
                    $leaveamount = 0.5;
                    $applyleavetype = 3;
                }


                $leave = new Leave;
                $leave->emp_id = $emp_data->emp_id;
                $leave->leave_type =  $applyleavetype;
                $leave->leave_from = $emp_data->date;
                $leave->leave_to = $emp_data->date;
                $leave->no_of_days = $leaveamount;
                $leave->half_short = $leaveamount;
                $leave->reson = 'Late';
                $leave->comment = '';
                $leave->emp_covering = '';
                $leave->leave_approv_person = Auth::id();
                $leave->status = 'Pending';
                $leave->save();


            }
            elseif($latetype == 3){

                if($d_count <=  $shortleave)
                {
                    $leaveamount = 0.25;
                }
                elseif($d_count <=  $halfday)
                {
                    $leaveamount = 0.5;
                }
                else{
                   
                    if(!empty($minitescount)){

                            $leave = new Leave;
                            $leave->emp_id = $emp_data->emp_id;
                            $leave->leave_type = $leave_type;
                            $leave->leave_from = $emp_data->date;
                            $leave->leave_to = $emp_data->date;
                            $leave->no_of_days = '0';
                            $leave->half_short = '0';
                            $leave->reson = 'Late';
                            $leave->comment = '';
                            $leave->emp_covering = '';
                            $leave->leave_approv_person = Auth::id();
                            $leave->status = 'Pending';
                            $leave->save();
                    }

                }
            }
        }

        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);

    }

     public function late_attendances_all(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-list');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.late_attendance_all');
    }

    public function late_attendance_list_approved(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-list');
        if (!$permission) {
            return response()->json(['error' => 'You do not have permission.']);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query = DB::query()
            ->select('ela.*',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'branches.location',
                'departments.name as dep_name')
            ->from('employee_late_attendances as ela')
            ->Join('employees', 'ela.emp_id', '=', 'employees.emp_id')
            ->leftJoin('attendances as at1', 'at1.id', '=', 'ela.attendance_id')
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department');

        if ($department != '') {
            $query->where(['departments.id' => $department]);
        }

        if ($employee != '') {
            $query->where(['employees.emp_id' => $employee]);
        }

        if ($location != '') {
            $query->where(['at1.location' => $location]);
        }

        if ($from_date != '' && $to_date != '') {
            $query->whereBetween('ela.date', [$from_date, $to_date]);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('check_in_time', function ($row) {
                return date('H:i', strtotime($row->check_in_time));
            })
            ->editColumn('check_out_time', function ($row) {
                return date('H:i', strtotime($row->check_out_time));
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
                ->filterColumn('formatted_date', function($query, $keyword) {
                    $query->where('at1.date', 'like', "%{$keyword}%");
                })
            ->addColumn('action', function ($row) {

                $btn = '';

                $permission = Auth::user()->can('late-attendance-delete');
                if ($permission) {
                    $btn = ' <button type="button" 
                        name="delete_button"
                        title="Delete"
                        data-id="' . $row->id . '"  
                        class="view_button btn btn-danger btn-sm delete_button"><i class="fas fa-trash"></i></button> ';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy_late_attendacne(Request $request)
    {
        $permission = Auth::user()->can('late-attendance-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = Request('id');

        $lateattendance = LateAttendance::findOrFail($id);

        $empid = $lateattendance->emp_id;
        $date = $lateattendance->date;
        $attendanceid = $lateattendance->attendance_id;

        DB::table('employee_late_attendance_minites')
        ->where('attendance_id', $attendanceid)
        ->delete();
        
        $emp_leave = DB::table('leaves')
        ->where('emp_id', $empid)
        ->where('leave_from', $date)
        ->first();

        $message = "";
        if($emp_leave){
            $id_leave = $emp_leave->id;
            $status = $emp_leave->status;

            if($status === "Approved"){

                $message = "There is an approved leave for this date. Please remove it before deleting the late attendance record";

            }else{
                $leaves = Leave::findOrFail($id_leave);
                $leaves->delete();

                $lateattendance->delete();

                $message = "Record deleted";
            }

        }else{
            $lateattendance->delete();
            $message = "Record deleted";
        }

        return response()->json(['message' => $message ]);
    }

}
