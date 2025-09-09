<?php

namespace App\Http\Controllers;

use App\Department;
use App\Employee;
use App\Branch;
use App\Attendance;
use App\EmployeePaySlip;
use App\EmployeeSalary;
use App\Holiday;
use App\Leave;
use App\PayrollProfile;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use stdClass;

class RptAttendanceController extends Controller
{
     public function attendentbyemployee(Request $request)
    {
        $permission = Auth::user()->can('attendance-report');
        if (!$permission) {
           abort(403);
        }
        return view('Report.attendentbyemployee');
    }

        public function empoloyeeattendentall()
    {
        $attendents = DB::query()
            ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.id')
            ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
            ->Join('branches', 'fingerprint_devices.location', '=', 'branches.id')
            ->groupBy('at1.uid', 'at1.date')
            ->get();


        return view('Report.attendentreportall', compact('attendents'));
    }


     public function exportattendances()
    {
        $att_data = DB::query()
            ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'fingerprint_devices.location')
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.id')
            ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
            ->groupBy('at1.uid', 'at1.date')
            ->get();


        $att_array[] = array('Employee Id', 'Name With Initial', 'Date', 'First Checkin', 'Last Checkout', 'Location');
        foreach ($att_data as $attendents) {
            $att_array[] = array(
                'Employee Id' => $attendents->uid,
                'Name With Initial' => $attendents->emp_name_with_initial,
                'Date' => $attendents->date,
                'First Checkin' => $attendents->timestamp,
                'Last Checkout' => $attendents->lasttimestamp,
                'Location' => $attendents->location


            );
        }
        Excel::create('Employee Attendent Data', function ($excel) use ($att_array) {
            $excel->setTitle('Employee Attendent Data');
            $excel->sheet('Employee Attendent Data', function ($sheet) use ($att_array) {
                $sheet->fromArray($att_array, null, 'A1', false, false);
            });
        })->download('xlsx');


    }

       
    function daterange()
    {
        return view('Report.attendentreport');
    }

    
    function fetch_data(Request $request)
    {
        if ($request->ajax()) {
            if ($request->from_date != '' && $request->to_date != '') {

                $data = DB::query()
                    ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                    ->from('attendances as at1')
                    ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                    ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
                    ->leftJoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
                    ->whereBetween('at1.timestamp', array($request->from_date, $request->to_date))
                    ->groupBy('at1.uid', 'at1.date')
                    ->get();


            } else {


                $data = DB::query()
                    ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'fingerprint_devices.location')
                    ->from('attendances as at1')
                    ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                    ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
                    ->groupBy('at1.uid', 'at1.date')
                    ->get();
            }
            echo json_encode($data);

        }

    }

    function attendentfilter(Request $request)
    {

        if ($request->from_date_sub != '' && $request->to_date_sub != '') {


            $att_data = DB::query()
                ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                ->from('attendances as at1')
                ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
                ->leftJoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
                ->whereBetween('at1.timestamp', [$request->from_date_sub, $request->to_date_sub])
                ->groupBy('at1.uid', 'at1.date')
                ->get();
            //dd($att_data);


            $att_array[] = array('Employee Id', 'Name With Initial', 'Date', 'First Checkin', 'Last Checkout', 'Working Hours', 'Location');
            foreach ($att_data as $attendents) {
                $startTime = Carbon::parse($attendents->timestamp);
                $finishTime = Carbon::parse($attendents->lasttimestamp);

                $totalDuration = $finishTime->diffInHours($startTime);

                $att_array[] = array(
                    'Employee Id' => $attendents->uid,
                    'Name With Initial' => $attendents->emp_name_with_initial,
                    'Date' => $attendents->date,
                    'First Checkin' => $attendents->timestamp,
                    'Last Checkout' => $attendents->lasttimestamp,
                    'Working Hours' => $totalDuration,
                    'Location' => $attendents->location


                );
            }
            Excel::create('Employee Attendent Data', function ($excel) use ($att_array) {
                $excel->setTitle('Employee Attendent Data');
                $excel->sheet('Employee Attendent Data', function ($sheet) use ($att_array) {
                    $sheet->fromArray($att_array, null, 'A1', false, false);
                });
            })->download('xlsx');

        }


    }

    public function attendance_report_list(Request $request)
    {
        $permission = Auth::user()->can('attendance-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        ## Read value
        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

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
                    from `attendances` as `at1` 
                    left join `employees` on `at1`.`uid` = `employees`.`emp_id`  
                    left join `branches` on `at1`.`location` = `branches`.`id`  
                    group by `at1`.`uid`, `at1`.`date`  
                )t
            ');

        $totalRecords = $totalRecords_array[0]->acount;

        $query1 = 'SELECT COUNT(*) as acount ';
        $query1.= 'FROM ( ';
        $query1.= 'SELECT COUNT(*) ';
        $query2= 'FROM `employees` ';
        $query2.= 'left join `branches` on `employees`.`emp_location` = `branches`.`id` ';
        $query2.= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
        $query2.= 'WHERE 1 = 1 ';
        //$searchValue = 'Breeder Farm';
        if($searchValue != ''){
            $query2.= 'AND ';
            $query2.= '( ';
            $query2.= 'employees.emp_id like "'.$searchValue.'%" ';
            $query2.= 'OR employees.emp_name_with_initial like "'.$searchValue.'%" ';
            $query2.= 'OR branches.location like "'.$searchValue.'%" ';
            $query2.= 'OR departments.name like "'.$searchValue.'%" ';
            $query2.= ') ';
        }

        if($department != ''){
            $query2.= 'AND departments.id = "'.$department.'" ';
        }

        if($employee != ''){
            $query2.= 'AND employees.emp_id = "'.$employee.'" ';
        }

        if($location != ''){
            $query2.= 'AND employees.emp_department = "'.$location.'" ';
        }

        //        if($from_date != '' && $to_date != ''){
        //            $query2.= 'AND at1.date BETWEEN "'.$from_date.'" AND "'.$to_date.'" ';
        //        }

        $query6 = '';
        $query6.= ' ';
        $query4 = ') t ';
        $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
        $query7 = ' ';

        $totalRecordswithFilter_arr = DB::select($query1.$query2.$query6.$query4);
        $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

        // Fetch records
        $query3 = 'select   
            employees.emp_id ,
            employees.emp_name_with_initial ,
            employees.emp_etfno,
            branches.location as b_location,
            departments.name as dept_name  
              ';

        $records = DB::select($query3.$query2.$query6.$query7.$query5);
        //error_log($query3.$query2.$query6.$query7.$query5);
        //var_dump(sizeof($records));
        //die();
        $data_arr = array();

        foreach ($records as $record) {

            //get attendances for each employee by emp_id
            $sql = " SELECT *,
                    Max(attendances.timestamp) as lasttimestamp
                    FROM attendances WHERE uid = '".$record->emp_id."' ";

            if($from_date != '' && $to_date != ''){
                $sql.= 'AND date BETWEEN "'.$from_date.'" AND "'.$to_date.'" ';
            }

            $sql.= 'GROUP BY uid, date ';
            $sql.= 'ORDER BY date DESC ';

            $attendances = DB::select($sql);

            foreach ($attendances as $attendance) {

                $to = \Carbon\Carbon::parse($attendance->lasttimestamp);
                $from = \Carbon\Carbon::parse($attendance->timestamp);

                $workhours = gmdate("H:i:s", $to->diffInSeconds($from));
                $rec_date =  Carbon::parse($attendance->date)->toDateString();

                $first_time_stamp = $attendance->timestamp;
                $last_time_stamp = '';

                if($attendance->timestamp != $attendance->lasttimestamp){
                    $last_time_stamp = $attendance->lasttimestamp;
                }

                $first_time_stamp = \Carbon\Carbon::parse($first_time_stamp)->format('H:i:s');

                if($last_time_stamp != ''){
                    $last_time_stamp = \Carbon\Carbon::parse($last_time_stamp)->format('H:i:s');
                }

                $data_arr[] = array(
                    'emp_id' => $record->emp_id,
                    'emp_name_with_initial' => $record->emp_name_with_initial,
                    'etf_no' => $record->emp_etfno,
                    'b_location' => $record->b_location,
                    'dept_name' => $record->dept_name,
                    'date' => $rec_date,
                    'timestamp' => $first_time_stamp,
                    'lasttimestamp' => $last_time_stamp,
                    'workhours' => $workhours,
                    'location' => $record->b_location,
                );
            }
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

    public function get_attendance_by_employee_data(Request $request)
    {
        $department = Request('department');
        $employee = Request('employee');
        $location = Request('location');
        $from_date = Request('from_date');
        $to_date = Request('to_date');

        $holidays = DB::table('holidays')->whereBetween('date', [$from_date, $to_date])->get()->keyBy('date'); 

        

        $dept_sql = "SELECT * FROM departments WHERE 1 = 1 ";
        if ($department != '') {
            $dept_sql .= ' AND id = "' . $department . '" ';
        }

        // if ($location != '') {
        //     $dept_sql .= 'AND company_id = "' . $location . '" ';
        // }

        $departments = DB::select($dept_sql);

        //  dd($departments);
        $data_arr = [];
        $not_att_count = 0;


        foreach ($departments as $department_) {
            $atte_arr = [];

            $query3 = 'SELECT   
                        employees.emp_id,
                        employees.emp_name_with_initial,
                        employees.calling_name,
                        employees.emp_etfno,
                        branches.location as b_location,
                        departments.name as dept_name,
                        departments.id as dept_id
                    FROM employees '; 
            $query3 .= 'LEFT JOIN `branches` ON `employees`.`emp_location` = `branches`.`id` ';
            $query3 .= 'LEFT JOIN `departments` ON `employees`.`emp_department` = `departments`.`id` ';
            $query3 .= 'WHERE employees.deleted = 0 AND employees.is_resigned = 0 '; 
            $query3 .= 'AND departments.id = "' . $department_->id . '" ';

            if ($employee != '') {
                $query3 .= 'AND employees.emp_id = "' . $employee . '" ';
            }

            $query3 .= 'order by employees.emp_id asc ';

            $employees = DB::select($query3);

           

            foreach ($employees as $record) {
                $period = CarbonPeriod::create($from_date, $to_date);
                foreach ($period as $date) {
                    $f_date = $date->format('Y-m-d');
                    $dayOfWeek = $date->dayOfWeek;
                    $day_type = $date->format('l');

                    if (isset($holidays[$f_date])) {
                        $day_type = 'Holiday';
                    } elseif ($dayOfWeek === Carbon::SATURDAY) {
                        $day_type = 'Saturday';
                    } elseif ($dayOfWeek === Carbon::SUNDAY) {
                        $day_type = 'Sunday';
                    }


                    $sql = " SELECT *, Max(attendances.timestamp) as lasttimestamp FROM attendances WHERE uid = '" . $record->emp_id . "' AND deleted_at IS NULL ";
                    $sql .= 'AND date LIKE "' . $f_date . '%" ';
                    $sql .= 'GROUP BY uid, date ';
                    $sql .= 'ORDER BY date DESC ';

                    $attendances = DB::select($sql);

                    if (!empty($attendances)) {
                        $to = \Carbon\Carbon::parse($attendances[0]->lasttimestamp);
                        $from = \Carbon\Carbon::parse($attendances[0]->timestamp);

                        $diff_in_minutes = $to->diffInMinutes($from);
                        $diff_in_hours = $diff_in_minutes / 60;
                        $diff_in_hours = number_format((float)$diff_in_hours, 2, '.', '');

                        $workhours = $diff_in_hours;
                        $rec_date = Carbon::parse($attendances[0]->date)->toDateString();

                        $first_time_stamp = $attendances[0]->timestamp;
                        $last_time_stamp = '';

                        if ($attendances[0]->timestamp != $attendances[0]->lasttimestamp) {
                            $last_time_stamp = $attendances[0]->lasttimestamp;
                        }

                        $first_time_stamp = \Carbon\Carbon::parse($first_time_stamp)->format('H:i');

                        if ($last_time_stamp != '') {
                            $last_time_stamp = \Carbon\Carbon::parse($last_time_stamp)->format('H:i');
                        }

                        if ($record->dept_name == null) {
                            $record->dept_name = '-';
                        }

                        $objattendance = new stdClass();
                        $objattendance->emp_id = $record->emp_id;
                        $objattendance->emp_name_with_initial = $record->emp_name_with_initial;
                        $objattendance->calling_name = $record->calling_name;
                        $objattendance->emp_etfno = $record->emp_etfno;
                        $objattendance->b_location = $record->b_location;
                        $objattendance->dept_name = $record->dept_name;
                        $objattendance->dept_id = $record->dept_id;
                        $objattendance->date = $rec_date;
                        $objattendance->timestamp = $first_time_stamp;
                        $objattendance->lasttimestamp = $last_time_stamp;
                        $objattendance->workhours = $workhours;
                        $objattendance->location = $record->b_location;
                        $objattendance->day_type = $day_type;

                        array_push($atte_arr, $objattendance);
                    } else {
                        $objattendance = new stdClass();
                        $objattendance->emp_id = $record->emp_id;
                        $objattendance->emp_name_with_initial = $record->emp_name_with_initial;
                        $objattendance->calling_name = $record->calling_name;
                        $objattendance->emp_etfno = $record->emp_etfno;
                        $objattendance->b_location = $record->b_location;
                        $objattendance->dept_name = $record->dept_name;
                        $objattendance->dept_id = $record->dept_id;
                        $objattendance->date = $f_date;
                        $objattendance->timestamp = '-';
                        $objattendance->lasttimestamp = '-';
                        $objattendance->workhours = '-';
                        $objattendance->location = $record->b_location;
                        $objattendance->day_type = $day_type;

                        array_push($atte_arr, $objattendance);
                        $not_att_count++;
                    }
                }
            }

            $obj = new stdClass();
            $obj->departmentID = $department_->id;
            $obj->attendanceinfo = $atte_arr;

            array_push($data_arr, $obj);
        }

        return response()->json([
            'data' => $data_arr
        ]);
    }

    public function employee_list_from_attendance_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;
    
            $offset = ($page - 1) * $resultCount;
    
            $breeds = DB::query()
                ->from('attendances')
                ->leftJoin('employees', 'employees.emp_id', '=', 'attendances.uid')
                ->where('employees.deleted', 0) 
                ->where('employees.is_resigned', 0) 
                ->where('employees.emp_name_with_initial', 'LIKE', '%' . Input::get("term") . '%')
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get([
                    DB::raw('DISTINCT employees.emp_id as id'),
                    DB::raw('CONCAT(employees.emp_name_with_initial, " - ", employees.calling_name) as text')
                ]);
    
            $count = DB::query()
                ->from('attendances')
                ->leftJoin('employees', 'employees.emp_id', '=', 'attendances.uid')
                ->where('employees.deleted', 0) 
                ->where('employees.is_resigned', 0) 
                ->where('employees.emp_name_with_initial', 'LIKE', '%' . Input::get("term") . '%')
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->select([
                    DB::raw('DISTINCT employees.emp_id as id'),
                    DB::raw('employees.emp_name_with_initial as text')
                ])
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
    

    public function location_list_from_attendance_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = DB::query()
                ->where('branches.location', 'LIKE',  '%' . Input::get("term"). '%')
                ->from('attendances')
                ->leftjoin('branches', 'branches.id', '=', 'attendances.location')
                ->orderBy('branches.location')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT branches.id as id'),DB::raw('branches.location as text')]);

            $count = DB::query()
                ->where('branches.location', 'LIKE',  '%' . Input::get("term"). '%')
                ->from('attendances')
                ->leftjoin('branches', 'branches.id', '=', 'attendances.location')
                ->orderBy('branches.location')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT branches.id as id'),DB::raw('branches.location as text')])
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

    function attendentbyemployeefilter(Request $request)
    {


        if ($request->employee != '') {


            $att_data = DB::query()
                ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                ->from('attendances as at1')
                ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
                ->where('employees.emp_id', $request->employee)
                ->groupBy('at1.uid', 'at1.date')
                ->get();


            $att_array[] = array('Employee Id', 'Name With Initial', 'Date', 'First Checkin', 'Last Checkout', 'Working Hours', 'Location');
            foreach ($att_data as $attendents) {

                $startTime = Carbon::parse($attendents->timestamp);
                $finishTime = Carbon::parse($attendents->lasttimestamp);

                $totalDuration = $finishTime->diffInHours($startTime);


                $att_array[] = array(
                    'Employee Id' => $attendents->uid,
                    'Name With Initial' => $attendents->emp_name_with_initial,
                    'Date' => $attendents->date,
                    'First Checkin' => $attendents->timestamp,
                    'Last Checkout' => $attendents->lasttimestamp,
                    'Working Hours' => $totalDuration,
                    'Location' => $attendents->location

                );
            }
            Excel::create('Employee Attendent Data', function ($excel) use ($att_array) {
                $excel->setTitle('Employee Attendent Data');
                $excel->sheet('Employee Attendent Data', function ($sheet) use ($att_array) {
                    $sheet->fromArray($att_array, null, 'A1', false, false);
                });
            })->download('xlsx');

        }


    }

    public function attendetreport(Request $request)
    {

        $employee = DB::table('employees')
            ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->select('employees.*', 'job_titles.title', 'branches.location')
            ->get();
        $branch = Branch::orderBy('id', 'asc')->get();

        return view('Report.attendetreport', compact('employee', 'branch'));

    }

    function fetch_attend_data(Request $request)
    {


        if ($request->ajax()) {
            if ($request->location != '') {
                $data = DB::query()
                    ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                    ->from('attendances as at1')
                    ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                    ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
                    ->where('employees.emp_location', $request->location)
                    ->groupBy('at1.uid', 'at1.date')
                    ->get();
            }

            if ($request->location != '' && $request->from_date != '' && $request->to_date != '') {

                $data = DB::query()
                    ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                    ->from('attendances as at1')
                    ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                    ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
                    ->leftJoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
                    ->whereBetween('at1.timestamp', array($request->from_date, $request->to_date))
                    ->where('branches.id', $request->location)
                    ->groupBy('at1.uid', 'at1.date')
                    ->get();
            }

            if ($request->location == '' && $request->from_date != '' && $request->to_date != '') {

                $data = DB::query()
                    ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                    ->from('attendances as at1')
                    ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                    ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
                    ->leftJoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
                    ->whereBetween('at1.timestamp', array($request->from_date, $request->to_date))
                    ->groupBy('at1.uid', 'at1.date')
                    ->get();

            }


            echo json_encode($data);

        }

    }

    function atenddatafilter(Request $request)
    {

        if ($request->location != '') {
            $att_data = DB::query()
                ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                ->from('attendances as at1')
                ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
                ->where('employees.emp_location', $request->location)
                ->groupBy('at1.uid', 'at1.date')
                ->get();
        }

        if ($request->location != '' && $request->from_date != '' && $request->to_date != '') {

            $att_data = DB::query()
                ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                ->from('attendances as at1')
                ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
                ->leftJoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
                ->whereBetween('at1.timestamp', array($request->from_date, $request->to_date))
                ->where('branches.id', $request->location)
                ->groupBy('at1.uid', 'at1.date')
                ->get();
        }

        if ($request->location == '' && $request->from_date != '' && $request->to_date != '') {

            $att_data = DB::query()
                ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'branches.location')
                ->from('attendances as at1')
                ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
                ->leftJoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
                ->whereBetween('at1.timestamp', array($request->from_date, $request->to_date))
                ->groupBy('at1.uid', 'at1.date')
                ->get();

        }
        $att_array[] = array('Employee Id', 'Name With Initial', 'Date', 'First Checkin', 'Last Checkout', 'Working Hours', 'Location');
        foreach ($att_data as $attendents) {

            $startTime = Carbon::parse($attendents->timestamp);
            $finishTime = Carbon::parse($attendents->lasttimestamp);

            $totalDuration = $finishTime->diffInHours($startTime);

            $att_array[] = array(
                'Employee Id' => $attendents->uid,
                'Name With Initial' => $attendents->emp_name_with_initial,
                'Date' => $attendents->date,
                'First Checkin' => $attendents->timestamp,
                'Last Checkout' => $attendents->lasttimestamp,
                'Working Hours' => $totalDuration,
                'Location' => $attendents->location


            );
        }
        Excel::create('Employee Attendent Data', function ($excel) use ($att_array) {
            $excel->setTitle('Employee Attendent Data');
            $excel->sheet('Employee Attendent Data', function ($sheet) use ($att_array) {
                $sheet->fromArray($att_array, null, 'A1', false, false);
            });
        })->download('xlsx');


    }
}
