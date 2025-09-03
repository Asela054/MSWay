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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use stdClass;


class Report extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getemployeelist()
    {
        $permission = Auth::user()->can('employee-report');
        if (!$permission) {
            abort(403);
        }
        return view('Report.employeereport');
    }

    public function employee_report_list(Request $request)
    {
        $permission = Auth::user()->can('employee-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        ## Read value
        $department = $request->get('department');
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
        $totalRecords = DB::table('employees')
            ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftjoin('employment_statuses', 'employment_statuses.id', '=', 'employees.emp_status')
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0)
            ->count();

        $query = DB::table('employees');
        $query->select('count(*) as allcount');
        $query->where(function ($querysub) use ($searchValue) {
            $querysub->where('employees.id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('branches.location', 'like', '%' . $searchValue . '%')
                ->orWhere('departments.name', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_birthday', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_mobile', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_work_telephone', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_national_id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_gender', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_email', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_2', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_addressT1', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_T2', 'like', '%' . $searchValue . '%')
                ->orWhere('employment_statuses.emp_status', 'like', '%' . $searchValue . '%')
                ->orWhere('job_titles.title', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_permanent_date', 'like', '%' . $searchValue . '%');
        })
        ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->leftjoin('employment_statuses', 'employment_statuses.id', '=', 'employees.emp_status')
        ->where('employees.deleted', 0)
        ->where('employees.is_resigned', 0);

        if ($department != "" && $department != 'All') {
            $query->where('employees.emp_department', $department);
        }

        $totalRecordswithFilter = $query->count();

        // Fetch records
        $query = DB::table('employees');
        $query->where(function ($querysub) use ($searchValue) {
            $querysub->where('employees.id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('branches.location', 'like', '%' . $searchValue . '%')
                ->orWhere('departments.name', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_birthday', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_mobile', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_work_telephone', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_national_id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_gender', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_email', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_2', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_addressT1', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_T2', 'like', '%' . $searchValue . '%')
                ->orWhere('employment_statuses.emp_status', 'like', '%' . $searchValue . '%')
                ->orWhere('job_titles.title', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_permanent_date', 'like', '%' . $searchValue . '%');
        })
        ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->leftjoin('employment_statuses', 'employment_statuses.id', '=', 'employees.emp_status')
        ->where('employees.deleted', 0)
        ->where('employees.is_resigned', 0);

        if ($department != "" && $department != 'All') {
            $query->where('employees.emp_department', $department);
        }

        $query->select(
            "employees.id",
            "employees.emp_name_with_initial",
            "branches.location",
            "departments.name as dept_name",
            "employees.emp_birthday",
            "employees.emp_mobile",
            "employees.emp_work_telephone",
            "employees.emp_name_with_initial",
            "employees.emp_national_id",
            "employees.emp_gender",
            "employees.emp_email",
            "employees.emp_address",
            "employees.emp_address_2",
            "employees.emp_addressT1",
            "employees.emp_address_T2",
            "employment_statuses.emp_status as e_status",
            "job_titles.title",
            "employees.emp_permanent_date"
        );

        $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage);

        $records = $query->get();

        $data_arr = array();
        foreach ($records as $record) {
            $data_arr[] = array(
                "id" => $record->id,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "location" => $record->location,
                "dept_name" => $record->dept_name,
                "emp_birthday" => $record->emp_birthday,
                "emp_mobile" => $record->emp_mobile,
                "emp_work_telephone" => $record->emp_work_telephone,
                "emp_national_id" => $record->emp_national_id,
                "emp_gender" => $record->emp_gender,
                "emp_email" => $record->emp_email,
                "emp_address" => $record->emp_address . $record->emp_address_2,
                "emp_addressT" => $record->emp_addressT1 . $record->emp_address_T2,
                "e_status" => $record->e_status,
                "title" => $record->title,
                "emp_permanent_date" => $record->emp_permanent_date
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

    public function exportempoloyeereport()
    {

        $emp_data = DB::table('employees')
            ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->select('employees.*', 'job_titles.title', 'branches.location')
            ->get();


        $emp_array[] = array('Employee Id', 'Name with Initial', 'Home Address', 'Date of Birth', 'Mobile No', 'NIC No', 'Gender', 'Email', 'Job Category', 'Permenent Date'
        , 'Employee No', 'EPF No', 'Joined Date', 'Location');
        foreach ($emp_data as $employee) {
            $emp_array[] = array(
                'Employee Id' => $employee->id,
                'Name with Initial' => $employee->emp_name_with_initial,
                'Home Address' => $employee->emp_address,
                'Date of Birth' => $employee->emp_birthday,
                'Mobile No' => $employee->emp_mobile,
                'NIC No' => $employee->emp_national_id,
                'Gender' => $employee->emp_gender,
                'Email' => $employee->emp_email,
                'Job Category' => $employee->title,
                'Permenent Date' => $employee->emp_permanent_date,
                'Employee No' => $employee->id,
                'EPF No' => $employee->emp_etfno,
                'Joined Date' => $employee->emp_join_date,
                'Location' => $employee->location


            );
        }
        Excel::create('Employee List', function ($excel) use ($emp_array) {
            $excel->setTitle('Employee List');
            $excel->sheet('Employee List', function ($sheet) use ($emp_array) {
                $sheet->fromArray($emp_array, null, 'A1', false, false);
            });
        })->download('xlsx');
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

    public function attendentbyemployee(Request $request)
    {
        $permission = Auth::user()->can('attendance-report');
        if (!$permission) {
           abort(403);
        }
        return view('Report.attendentbyemployee');
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

        $dept_sql = "SELECT * FROM departments WHERE 1 = 1 ";
        if ($department != '') {
            $dept_sql .= ' AND id = "' . $department . '" ';
        }

        if ($location != '') {
            $dept_sql .= 'AND company_id = "' . $location . '" ';
        } else {
            $dept_sql .= 'AND company_id = "1" ';
        }

        $departments = DB::select($dept_sql);

        $data_arr = [];
        $not_att_count = 0;

        foreach ($departments as $department_) {
            $atte_arr = [];

            $query3 = 'SELECT   
                        employees.emp_id,
                        employees.emp_name_with_initial,
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
                        $objattendance->emp_etfno = $record->emp_etfno;
                        $objattendance->b_location = $record->b_location;
                        $objattendance->dept_name = $record->dept_name;
                        $objattendance->dept_id = $record->dept_id;
                        $objattendance->date = $rec_date;
                        $objattendance->timestamp = $first_time_stamp;
                        $objattendance->lasttimestamp = $last_time_stamp;
                        $objattendance->workhours = $workhours;
                        $objattendance->location = $record->b_location;

                        array_push($atte_arr, $objattendance);
                    } else {
                        $objattendance = new stdClass();
                        $objattendance->emp_id = $record->emp_id;
                        $objattendance->emp_name_with_initial = $record->emp_name_with_initial;
                        $objattendance->emp_etfno = $record->emp_etfno;
                        $objattendance->b_location = $record->b_location;
                        $objattendance->dept_name = $record->dept_name;
                        $objattendance->dept_id = $record->dept_id;
                        $objattendance->date = $f_date;
                        $objattendance->timestamp = '-';
                        $objattendance->lasttimestamp = '-';
                        $objattendance->workhours = '-';
                        $objattendance->location = $record->b_location;

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

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    // public function get_attendance_by_employee_data_excel(Request $request)
    // {

    //     $department = Request('department');
    //     $employee = Request('employee');
    //     $location = Request('location');
    //     $from_date = Request('from_date');
    //     $to_date = Request('to_date');

    //     //get all departments
    //     $dept_sql = "SELECT * FROM departments WHERE 1 = 1 ";

    //     if ($department != '') {
    //         $dept_sql.= ' AND id = "'.$department.'" ';
    //     }

    //     if($location != ''){
    //         $dept_sql.= 'AND company_id = "'.$location.'" ';
    //     }

    //     $departments = DB::select($dept_sql);

    //     $data_arr = array();
    //     $not_att_count = 0;

    //     foreach ($departments as $department_) {

    //         $query3 = 'select   
    //         employees.emp_id ,
    //         employees.emp_name_with_initial ,
    //         employees.emp_etfno,
    //         branches.location as b_location,
    //         departments.name as dept_name,
    //         departments.id as dept_id  
    //           ';

    //         $query3 .= 'from employees ';
    //         $query3 .= 'left join `branches` on `employees`.`emp_location` = `branches`.`id` ';
    //         $query3 .= 'left join `departments` on `employees`.`emp_department` = `departments`.`id` ';
    //         $query3 .= 'where 1 = 1 ';


    //         $query3.= 'AND departments.id = "'.$department_->id .'" ';


    //         if($employee != ''){
    //             $query3.= 'AND employees.emp_id = "'.$employee.'" ';
    //         }

    //         $query3.= 'order by employees.emp_id asc ';

    //         $employees = DB::select($query3);

    //         foreach($employees as $record) {

    //             //dates of the month between from and to date
    //             $period = CarbonPeriod::create($from_date, $to_date);

    //             foreach ($period as $date) {
    //                 $f_date = $date->format('Y-m-d');

    //                 $sql = " SELECT *,
    //                 Max(attendances.timestamp) as lasttimestamp
    //                 FROM attendances WHERE uid = '".$record->emp_id."' AND deleted_at IS NULL ";

    //                 $sql.= 'AND date LIKE "'.$f_date.'%" ';

    //                 $sql.= 'GROUP BY uid, date ';
    //                 $sql.= 'ORDER BY date DESC ';

    //                 $attendances = DB::select($sql);

    //                 if(!EMPTY($attendances)) {

    //                     $to = \Carbon\Carbon::parse($attendances[0]->lasttimestamp);
    //                     $from = \Carbon\Carbon::parse($attendances[0]->timestamp);

    //                     $workhours = gmdate("H:i:s", $to->diffInSeconds($from));
    //                     $rec_date =  Carbon::parse($attendances[0]->date)->toDateString();

    //                     $first_time_stamp = $attendances[0]->timestamp;
    //                     $last_time_stamp = '';

    //                     if($attendances[0]->timestamp != $attendances[0]->lasttimestamp){
    //                         $last_time_stamp = $attendances[0]->lasttimestamp;
    //                     }

    //                     $first_time_stamp = \Carbon\Carbon::parse($first_time_stamp)->format('H:i:s');

    //                     if($last_time_stamp != ''){
    //                         $last_time_stamp = \Carbon\Carbon::parse($last_time_stamp)->format('H:i:s');
    //                     }

    //                     if($record->dept_name == null){
    //                         $record->dept_name = '-';
    //                     }

    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['emp_id'] = $record->emp_id;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['emp_name_with_initial'] = $record->emp_name_with_initial;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['etf_no'] = $record->emp_etfno;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['b_location'] = $record->b_location;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['dept_name'] = $record->dept_name;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['dept_id'] = $record->dept_id;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['date'] = $rec_date;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['timestamp'] = $first_time_stamp;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['lasttimestamp'] = $last_time_stamp;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['workhours'] = $workhours;
    //                     $data_arr[$department_->id][$record->emp_id][$attendances[0]->id]['location'] = $record->b_location;


    //                 }else{
    //                     //attendance not found
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['emp_id'] = $record->emp_id;
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['emp_name_with_initial'] = $record->emp_name_with_initial;
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['etf_no'] = $record->emp_etfno;
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['b_location'] = $record->b_location;
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['dept_name'] = $record->dept_name;
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['dept_id'] = $record->dept_id;
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['date'] = $f_date;
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['timestamp'] = '-';
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['lasttimestamp'] = '-';
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['workhours'] = '-';
    //                     $data_arr[$department_->id][$record->emp_id][$not_att_count]['location'] = $record->b_location;

    //                     $not_att_count++;

    //                 }


    //             }



    //             foreach ($attendances as $attendance) {

    //                 $to = \Carbon\Carbon::parse($attendance->lasttimestamp);
    //                 $from = \Carbon\Carbon::parse($attendance->timestamp);

    //                 $workhours = gmdate("H:i:s", $to->diffInSeconds($from));
    //                 $rec_date =  Carbon::parse($attendance->date)->toDateString();

    //                 $first_time_stamp = $attendance->timestamp;
    //                 $last_time_stamp = '';

    //                 if($attendance->timestamp != $attendance->lasttimestamp){
    //                     $last_time_stamp = $attendance->lasttimestamp;
    //                 }

    //                 $first_time_stamp = \Carbon\Carbon::parse($first_time_stamp)->format('H:i:s');

    //                 if($last_time_stamp != ''){
    //                     $last_time_stamp = \Carbon\Carbon::parse($last_time_stamp)->format('H:i:s');
    //                 }

    //                 if($record->dept_name == null){
    //                     $record->dept_name = '-';
    //                 }

    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['emp_id'] = $record->emp_id;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['emp_name_with_initial'] = $record->emp_name_with_initial;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['etf_no'] = $record->emp_etfno;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['b_location'] = $record->b_location;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['dept_name'] = $record->dept_name;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['dept_id'] = $record->dept_id;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['date'] = $rec_date;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['timestamp'] = $first_time_stamp;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['lasttimestamp'] = $last_time_stamp;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['workhours'] = $workhours;
    //                 $data_arr[$department_->id][$record->emp_id][$attendance->id]['location'] = $record->b_location;

    //             }


    //         }

    //     }

    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     $i = 1;

    //     $sheet->setCellValueByColumnAndRow( 1, $i, 'Attendance Report' );

    //     $i++;
    //     $i++; // Start with the second line

    //     $titles = array(
    //         'ETF NO',
    //         'Name',
    //         'Department',
    //         'Date',
    //         'Check In Time',
    //         'Check Out Time',
    //         'Work Hours',
    //         'Location'
    //     );

    //     foreach ($titles as $key => $value) {
    //         $sheet->setCellValueByColumnAndRow($key + 1, $i, $value);
    //     }

    //     foreach ($titles as $key => $value) {
    //         $sheet->setCellValueByColumnAndRow($key + 1, $i, $value);
    //     }

    //     $row = $sheet->getRowIterator($i)->current();
    //     $cellIterator = $row->getCellIterator();
    //     $cellIterator->setIterateOnlyExistingCells(false);
    //     foreach ($cellIterator as $cell) {
    //         $cell->getStyle()->getFont()->setBold(true);
    //     }

    //     $i++; // Start with the second line

    //     //foreach $data_arr
    //     $department_id = 0;
    //     foreach ($data_arr as $dept_key => $department_data) {

    //         //if department_id is not equal to the previous department_id
    //         if($department_id != $dept_key){
    //             $department_id = $dept_key;
    //             $department_name = Department::query()->where('id', $department_id)->first()->name;
    //             $i++;
    //             $sheet->setCellValueByColumnAndRow(1, $i, $department_name);
    //             $i++;
    //         }

    //         foreach ($department_data as $emp_data) {

    //             foreach ($emp_data as $attendance)
    //             {

    //                 $sheet->setCellValueByColumnAndRow( 1, $i, $attendance['etf_no'] );
    //                 $sheet->setCellValueByColumnAndRow( 2, $i, $attendance['emp_name_with_initial'] );
    //                 $sheet->setCellValueByColumnAndRow( 3, $i, $attendance['dept_name'] );
    //                 $sheet->setCellValueByColumnAndRow( 4, $i, $attendance['date'] );
    //                 $sheet->setCellValueByColumnAndRow( 5, $i, $attendance['timestamp'] );
    //                 $sheet->setCellValueByColumnAndRow( 6, $i, $attendance['lasttimestamp'] );
    //                 $sheet->setCellValueByColumnAndRow( 7, $i, $attendance['workhours'] );
    //                 $sheet->setCellValueByColumnAndRow( 8, $i, $attendance['location'] );

    //                 $i++;

    //                 $department_id = $attendance['dept_id'];

    //             }

    //         }


    //     }

    //     $writer = new Xlsx($spreadsheet);

    //     $filename = 'Attendance_Report_'.date('Y_m_d_H_i');
    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
    //     header('Cache-Control: max-age=0');

    //     $writer->save('php://output'); // download file



    // }

    public function employee_list_from_attendance_sel2(Request $request){
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
    

    public function location_list_from_attendance_sel2(Request $request){
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


    public function leavereport(Request $request)
    {
        $permission = Auth::user()->can('leave-report');
        if (!$permission) {
            abort(403);
        }
        return view('Report.leavereport' );
    }

    public function leave_report_list(Request $request)
    {
        $permission = Auth::user()->can('leave-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        ## Read value
        $department = $request->get('department');
        $employee = $request->get('employee');
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
                    from leaves 
                    inner join `employees` on `employees`.`emp_id` = `leaves`.`emp_id` 
                    left join `leave_types` on `leave_types`.`id` = `leaves`.`leave_type` 
                    left join `departments` on `departments`.`id` = `employees`.`emp_department` 
                    where 1 = 1
                    group by employees.emp_id, leaves.leave_from   
                )t
            ');

        $totalRecords = $totalRecords_array[0]->acount;

        $query1 = 'SELECT COUNT(*) as acount ';
        $query1.= 'FROM ( ';
        $query1.= 'SELECT COUNT(*) ';
        $query2= 'FROM leaves ';
        $query2.= 'inner join `employees` on `employees`.`emp_id` = `leaves`.`emp_id` ';
        $query2.= 'left join employees as ec on ec.emp_id = leaves.emp_covering ';
        $query2.= 'left join `leave_types` on `leave_types`.`id` = `leaves`.`leave_type` ';
        $query2.= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
        $query2.= 'WHERE 1 = 1 ';
        $query2.= 'AND employees.deleted = 0 ';
        $query2.= 'AND employees.is_resigned = 0 ';
        $query2.= 'AND leaves.status = "Approved" ';
        //$searchValue = 'Breeder Farm';
        if($searchValue != ''){
            $query2.= 'AND ';
            $query2.= '( ';
            $query2.= 'employees.emp_id like "'.$searchValue.'%" ';
            $query2.= 'OR employees.emp_name_with_initial like "'.$searchValue.'%" ';
            $query2.= 'OR leaves.leave_from like "'.$searchValue.'%" ';
            $query2.= 'OR leaves.leave_to like "'.$searchValue.'%" ';
            $query2.= 'OR leaves.comment like "'.$searchValue.'%" ';
            $query2.= 'OR leaves.reson like "'.$searchValue.'%" ';
            $query2.= 'OR ec.emp_name_with_initial like "'.$searchValue.'%" ';
            $query2.= 'OR departments.name like "'.$searchValue.'%" ';
            $query2.= ') ';
        }

        if($department != ''){
            $query2.= 'AND departments.id = "'.$department.'" ';
        }

        if($employee != ''){
            $query2.= 'AND employees.emp_id = "'.$employee.'" ';
        }

        if($from_date != ''){
            $query2.= 'AND leaves.leave_from >= "'.$from_date.'" ';
        }

        if($to_date != ''){
            $query2.= 'AND leaves.leave_to <= "'.$to_date.'" ';
        }

        $query6 = 'group by employees.emp_id, leaves.leave_from ';
        $query6.= ' ';
        $query4 = ') t ';
        $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
        $query7 = 'ORDER BY '.$columnName.' '.$columnSortOrder.' ';

        $totalRecordswithFilter_arr = DB::select($query1.$query2.$query6.$query4);
        $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

        // Fetch records
        $query3 = 'select  
            leaves.*,
            employees.emp_id ,
            employees.emp_name_with_initial ,
            ec.emp_name_with_initial as emp_covering_name,
            leave_types.leave_type as leave_type_name,
            departments.name as dept_name  
              ';

        $records = DB::select($query3.$query2.$query6.$query7.$query5);

        $data_arr = array();

        foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $record->id,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "leave_from" => $record->leave_from,
                "leave_to" => $record->leave_to,
                "leave_type_name" => $record->leave_type_name,
                "status" => $record->status,
                "dept_name" => $record->dept_name,
                "emp_covering_name" => $record->emp_covering_name,
                "reson" => $record->reson
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

    public function employee_list_from_leaves_sel2(Request $request){
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = DB::query()
                ->where('employees.emp_name_with_initial', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('employees.deleted', 0)
                ->where('employees.is_resigned', 0)
                ->from('leaves')
                ->leftjoin('employees', 'employees.emp_id', '=', 'leaves.emp_id')
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT employees.emp_id as id'),DB::raw('CONCAT(employees.emp_name_with_initial, " - " ,employees.calling_name) as text')]);

            $count = DB::query()
                ->where('employees.emp_name_with_initial', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('employees.deleted', 0)
                ->where('employees.is_resigned', 0)
                ->from('leaves')
                ->leftjoin('employees', 'employees.emp_id', '=', 'leaves.emp_id')
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->select([DB::raw('DISTINCT employees.emp_id as id'),DB::raw('employees.emp_name_with_initial as text')])
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


    function employee_fetch_data(Request $request)
    {

        // dd($request->employee);
        if ($request->ajax()) {
            if ($request->employee != '') {

                $data = DB::query()
                    ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'),
                        'employees.emp_name_with_initial', 'branches.location')
                    ->from('attendances as at1')
                    ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                    ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
                    ->where('employees.emp_id', $request->employee)
                    ->groupBy('at1.uid', 'at1.date')
                    ->get();


            }

            echo json_encode($data);

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

    public function lateattendent()
    {
        $permission = Auth::user()->can('late-attendance-report');
        if (!$permission) {
            abort(403);
        }
        return view('Report.lateattendance' );
    }

    // public function late_attendance_report_list(Request $request)
    // {
    //     $permission = Auth::user()->can('late-attendance-report');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     }
    //     ## Read value
    //     $department = $request->get('department');
    //     $fromdate = $request->get('fromdate');
    //     $to_date = $request->get('to_date');
    //     $employee = $request->get('employee');
    //     $latestatus = $request->get('latestatus');

    //     $draw = $request->get('draw');
    //     $start = $request->get("start");
    //     $rowperpage = $request->get("length"); // Rows display per page

    //     $columnIndex_arr = $request->get('order');
    //     $columnName_arr = $request->get('columns');
    //     $order_arr = $request->get('order');
    //     $search_arr = $request->get('search');

    //     $columnIndex = $columnIndex_arr[0]['column']; // Column index
    //     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    //     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    //     $searchValue = $search_arr['value']; // Search value

    //     // Total records
    //     $totalRecords_array = DB::select('
    //         SELECT COUNT(*) as acount
    //             FROM
    //             (
    //                 SELECT COUNT(*)
    //                 from `attendances` as `at1` 
    //                 inner join `employees` on `at1`.`uid` = `employees`.`emp_id` 
    //                 left join `shift_types` on `employees`.`emp_shift` = `shift_types`.`id` 
    //                 left join `branches` on `at1`.`location` = `branches`.`id` 
    //                 where employees.deleted = 0
    //                 group by `at1`.`uid`, `at1`.`date`  
    //                 having (Max(at1.timestamp) > Min(at1.timestamp) ) 
    //             )t
    //         ');
    //     $totalRecords = $totalRecords_array[0]->acount;

    //     $query1 = 'SELECT COUNT(*) as acount ';
    //     $query1.= 'FROM ( ';
    //     $query1.= 'SELECT COUNT(*) ';
    //     $query2= 'FROM `attendances` as `at1` ';
    //     $query2.= 'inner join `employees` on `at1`.`uid` = `employees`.`emp_id` ';
    //     $query2.= 'left join `shift_types` on `employees`.`emp_shift` = `shift_types`.`id` ';
    //     $query2.= 'left join `branches` on `at1`.`location` = `branches`.`id` ';
    //     $query2.= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
    //     $query2.= 'WHERE employees.deleted = 0 ';
    //     //$searchValue = 'Breeder Farm';
    //     if($searchValue != ''){
    //         $query2.= 'AND ';
    //         $query2.= '( ';
    //         $query2.= 'employees.emp_id like "'.$searchValue.'%" ';
    //         $query2.= 'OR employees.emp_name_with_initial like "'.$searchValue.'%" ';
    //         $query2.= 'OR at1.timestamp like "'.$searchValue.'%" ';
    //         $query2.= 'OR branches.location like "'.$searchValue.'%" ';
    //         $query2.= 'OR departments.name like "'.$searchValue.'%" ';
    //         $query2.= ') ';
    //     }

    //     if($department != ''){
    //         $query2.= 'AND departments.id = "'.$department.'" ';
    //     }
    //     if($fromdate != '' && $to_date != ''){
    //         $query2 .= 'AND at1.date BETWEEN "'.$fromdate.'" AND "'.$to_date.'"';
    //     }

    //     $query6 = 'group by `at1`.`uid`, `at1`.`date` ';
    //     $query6.= 'having (Max(at1.timestamp) > Min(at1.timestamp) ) ';
    //     $query4 = ') t ';
    //     $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
    //     $query7 = 'ORDER BY '.$columnName.' '.$columnSortOrder.' ';

    //     $totalRecordswithFilter_arr = DB::select($query1.$query2.$query6.$query4);
    //     $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

    //     // Fetch records
    //     $query3 = 'select `shift_types`.*, `at1`.*, at1.id as at_id, Max(at1.timestamp) as lasttimestamp, Min(at1.timestamp) as firsttimestamp,
    //             `employees`.`emp_name_with_initial`, `branches`.`location` as b_location, departments.name as dept_name ';

    //     $records = DB::select($query3.$query2.$query6.$query7.$query5);
    //     //error_log($query3.$query2.$query6.$query7.$query5);

    //     $data_arr = array();

    //     foreach ($records as $record) {
    //         //if ($firstintime = date('G:i', strtotime($record->firsttimestamp)) > $onduty_time = date('G:i', strtotime($record->onduty_time))) {

    //             $firstintime = date('G:i', strtotime($record->firsttimestamp));
    //             $lastintime = date('G:i', strtotime($record->lasttimestamp));
    //             $data_arr[] = array(
    //                 "uid" => $record->uid,
    //                 "emp_name_with_initial" => $record->emp_name_with_initial,
    //                 "firstintime" => date('G:i', strtotime($record->firsttimestamp)),
    //                 "date_diff" => Carbon::parse($firstintime)->diffForHumans($record->onduty_time),
    //                 "lastintime" => date('G:i', strtotime($record->lasttimestamp)),
    //                 "date_diff_2" => Carbon::parse($lastintime)->diffForHumans($record->offduty_time),
    //                 "attendances" => Str::limit($record->date, 10, ''),
    //                 "date" => $record->date,
    //                 "dept_name" => $record->dept_name
    //             );
    //        // }
    //     }

    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordswithFilter,
    //         "aaData" => $data_arr
    //     );

    //     echo json_encode($response);
    //     exit;
    // }

    public function late_attendance_report_list(Request $request)
    {
        $permission = Auth::user()->can('late-attendance-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        // Read values
        $department = $request->get('department');
        $fromdate = $request->get('fromdate');
        $to_date = $request->get('to_date');
        $employee = $request->get('employee');
        $latestatus = $request->get('latestatus');

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        // Map column names to actual database columns
        $columnMapping = [
            'uid' => 'ela.emp_id', // Changed from 'uid' to 'ela.emp_id'
            'emp_name_with_initial' => 'employees.emp_name_with_initial',
            'date' => 'ela.date',
            'dept_name' => 'departments.name',
            // Add other columns as needed
        ];

        // Use mapped column name or fall back to the original
        $orderColumn = $columnMapping[$columnName] ?? $columnName;

        // Base query components
        $baseQuery = 'FROM `employee_late_attendances` as `ela` 
                    INNER JOIN `employees` ON `ela`.`emp_id` = `employees`.`emp_id` 
                    LEFT JOIN `shift_types` ON `employees`.`emp_shift` = `shift_types`.`id` 
                    LEFT JOIN `departments` ON `departments`.`id` = `employees`.`emp_department` 
                    WHERE employees.deleted = 0 ';

        // Apply filters
        if ($searchValue != '') {
            $baseQuery .= 'AND (employees.emp_id LIKE "'.$searchValue.'%" 
                        OR employees.emp_name_with_initial LIKE "'.$searchValue.'%" 
                        OR ela.check_in_time LIKE "'.$searchValue.'%" 
                        OR departments.name LIKE "'.$searchValue.'%") ';
        }

        if ($department != '') {
            $baseQuery .= 'AND departments.id = "'.$department.'" ';
        }

        if ($employee != '') {
            $baseQuery .= 'AND ela.emp_id = "'.$employee.'" ';
        }

        if ($fromdate != '' && $to_date != '') {
            $baseQuery .= 'AND ela.date BETWEEN "'.$fromdate.'" AND "'.$to_date.'" ';
        }

        if ($latestatus != '') {
            if ($latestatus == 1) { // Late Coming
                $baseQuery .= 'AND TIME(ela.check_in_time) > TIME(shift_types.onduty_time) ';
            } elseif ($latestatus == 2) { // Early Going
                $baseQuery .= 'AND TIME(ela.check_out_time) < TIME(shift_types.offduty_time) ';
            }
        }

        // Total records count
        $totalRecords = DB::table('employee_late_attendances')
            ->join('employees', 'employee_late_attendances.emp_id', '=', 'employees.emp_id')
            ->where('employees.deleted', 0)
            ->count();

        // Total records with filter
        $totalRecordswithFilter = DB::select('SELECT COUNT(*) as acount FROM (' . 
            'SELECT ela.id ' . $baseQuery . 
            'GROUP BY ela.id' . 
            ') t')[0]->acount;

        // Fetch records
        $records = DB::select('SELECT shift_types.*, ela.*, 
                            employees.emp_name_with_initial, 
                            departments.name as dept_name,
                            ela.emp_id as uid ' .  // Added this alias to maintain compatibility
                            $baseQuery . 
                            'ORDER BY '.$orderColumn.' '.$columnSortOrder.' ' .
                            'LIMIT ' . $start . ', ' . $rowperpage);

         $data_arr = array();

        foreach ($records as $record) {
            $check_in = date('G:i', strtotime($record->check_in_time));
            $check_out = $record->check_out_time ? date('G:i', strtotime($record->check_out_time)) : '--';
            
            // Calculate time differences
            $late_minutes = 0;
            $early_minutes = 0;
            $late_time = '--';
            $early_time = '--';
            
            if ($record->check_in_time && $record->onduty_time) {
                $late_seconds = strtotime($record->check_in_time) - strtotime($record->onduty_time);
                if ($late_seconds > 0) {
                    $late_minutes = round($late_seconds / 60);
                    $late_time = $this->formatTimeDifference($late_seconds);
                }
            }
            
            if ($record->check_out_time && $record->offduty_time) {
                $early_seconds = strtotime($record->offduty_time) - strtotime($record->check_out_time);
                if ($early_seconds > 0) {
                    $early_minutes = round($early_seconds / 60);
                    $early_time = $this->formatTimeDifference($early_seconds);
                }
            }
            
            $data_arr[] = array(
                "uid" => $record->emp_id,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "check_in_time" => $check_in,
                "scheduled_check_in" => $record->onduty_time ? date('G:i', strtotime($record->onduty_time)) : '--',
                "check_in_status" => $record->check_in_time && $record->onduty_time ? 
                                    Carbon::parse($check_in)->diffForHumans($record->onduty_time) : '--',
                "late_minutes" => $late_minutes,
                "late_time" => $late_time,
                "check_out_time" => $check_out,
                "scheduled_check_out" => $record->offduty_time ? date('G:i', strtotime($record->offduty_time)) : '--',
                "check_out_status" => $record->check_out_time && $record->offduty_time ? 
                                    Carbon::parse($check_out)->diffForHumans($record->offduty_time) : '--',
                "early_minutes" => $early_minutes,
                "early_time" => $early_time,
                "date" => $record->date,
                "dept_name" => $record->dept_name,
                "status" => $this->getLateStatus($record->check_in_time, $record->check_out_time, 
                                            $record->onduty_time, $record->offduty_time)
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    private function formatTimeDifference($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $result = [];
        if ($hours > 0) {
            $result[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $result[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }
        
        return implode(' ', $result) ?: '0 minutes';
    }

    private function getLateStatus($check_in, $check_out, $onduty, $offduty)
    {
        if (!$check_in) return 'No Check-in';
        
        $isLate = $check_in && $onduty && (strtotime($check_in) > strtotime($onduty));
        $isEarly = $check_out && $offduty && (strtotime($check_out) < strtotime($offduty));
        
        if ($isLate && $isEarly) return 'Late & Early';
        if ($isLate) return 'Late Coming';
        if ($isEarly) return 'Early Going';
        
        return 'On Time';
    }


    public function exportLateattend()
    {

        $att_data = DB::query()
            ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'), DB::raw('Min(at1.timestamp) as firsttimestamp'), 'employees.emp_name_with_initial', 'shift_types.onduty_time', 'shift_types.offduty_time')
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->groupBy('at1.uid', 'at1.date')
            ->get()->toarray();


        $att_array[] = array('Employee Id', 'Name With Initial', 'Date', 'First Checkin', 'Last Checkout', 'Location');
        foreach ($att_data as $attendents) {
            if ($timestamp = date('G:i', strtotime($attendents->timestamp)) > $onduty_time = date('G:i', strtotime($attendents->onduty_time))) {
                $att_array[] = array(
                    'Employee Id' => $attendents->uid,
                    'Name With Initial' => $attendents->emp_name_with_initial,
                    'Date' => $attendents->date,
                    'First Checkin' => $attendents->timestamp,
                    'Last Checkout' => $attendents->lasttimestamp,
                    'Location' => $attendents->location


                );
            }
        }
        Excel::create('Employee Late Attendent Data', function ($excel) use ($att_array) {
            $excel->setTitle('Employee Late Attendent Data');
            $excel->sheet('Employee Late Attendent Data', function ($sheet) use ($att_array) {
                $sheet->fromArray($att_array, null, 'A1', false, false);
            });
        })->download('xlsx');


    }


    function fetch_leave_data(Request $request)
    {


        if ($request->ajax()) {
            if ($request->employee != '') {
                $data = DB::query()
                    ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                    ->from('leaves')
                    ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                    ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                    ->where('employees.emp_id', $request->employee)
                    ->groupBy('employees.emp_id', 'leaves.leave_from')
                    ->get();
            }
            if ($request->employee != '' && $request->to_date != '' && $request->from_date != '') {
                $data = DB::query()
                    ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                    ->from('leaves')
                    ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                    ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                    ->where('employees.emp_id', $request->employee)
                    ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                    ->groupBy('employees.emp_id', 'leaves.leave_from')
                    ->get();
            }
            if ($request->to_date != '' && $request->from_date != '') {
                $data = DB::query()
                    ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                    ->from('leaves')
                    ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                    ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                    ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                    ->groupBy('employees.emp_id', 'leaves.leave_from')
                    ->get();
            }

            echo json_encode($data);

        }

    }

    function leavedatafilter(Request $request)
    {


        if ($request->employee != '') {
            $leave_data = DB::query()
                ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                ->from('leaves')
                ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                ->where('employees.emp_id', $request->employee)
                ->groupBy('employees.emp_id', 'leaves.leave_from')
                ->get();
        }
        if ($request->employee != '' && $request->to_date != '' && $request->from_date != '') {
            $leave_data = DB::query()
                ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                ->from('leaves')
                ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                ->where('employees.emp_id', $request->employee)
                ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                ->groupBy('employees.emp_id', 'leaves.leave_from')
                ->get();
        }
        if ($request->to_date != '' && $request->from_date != '') {
            $leave_data = DB::query()
                ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                ->from('leaves')
                ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                ->groupBy('employees.emp_id', 'leaves.leave_from')
                ->get();
        }


        $leave_array[] = array('Employee Id', 'Name With Initial', 'Leave From', 'Leave To', 'Leave Type', 'Status');
        foreach ($leave_data as $leaves) {


            $leave_array[] = array(
                'Employee Id' => $leaves->emp_id,
                'Name With Initial' => $leaves->emp_name_with_initial,
                'Leave From' => $leaves->leave_from,
                'Leave To' => $leaves->leave_to,
                'Leave Type' => $leaves->leave_type,
                'Status' => $leaves->status


            );
        }
        Excel::create('Employee Leave  Data', function ($excel) use ($leave_array) {
            $excel->setTitle('Employee Leave Data');
            $excel->sheet('Employee Leave Data', function ($sheet) use ($leave_array) {
                $sheet->fromArray($leave_array, null, 'A1', false, false);
            });
        })->download('xlsx');

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

            ->rawColumns(['action',
                'work_days',
                'leave_days',
                'no_pay_days',
                'normal_rate_otwork_hrs',
                'double_rate_otwork_hrs'
            ])
            ->make(true);
    }

    public function no_pay_report(){
        $permission = Auth::user()->can('no-pay-report');
        if(!$permission){
            abort(403);
        }

        return view('Report.no_pay_report' );
    }

    public function get_no_pay_amount($emp_etfno, $emp_work, $emp_leave, $emp_nopay, $emp_ot_i, $emp_ot_ii  ){

        $sql_info = "SELECT payroll_profiles.id as payroll_profile_id, payroll_profiles.basic_salary, payroll_profiles.day_salary, payroll_process_types.pay_per_day 
                FROM `payroll_profiles` inner join payroll_process_types on payroll_profiles.payroll_process_type_id=payroll_process_types.id WHERE payroll_profiles.emp_etfno=?";
        $profiles = DB::select($sql_info, [$emp_etfno]);//70

        if(empty($profiles)){
            return 0;
        }

        $employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$profiles[0]->payroll_profile_id])
            ->latest()
            ->first();
        $emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);

        //
        //-2022-12-02-exemptions
        //
        $fig_ex_grp = array('NOPAY'=>0, 'OTHRS'=>0);
        $sql_exemptions="select remuneration_exemptions.exemption_fig_group_title, sum(drv_termpay.payment_amount) as exemption_fig_group_total from (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS drv_termpay INNER JOIN remunerations ON drv_termpay.remuneration_id=remunerations.id inner join remuneration_exemptions on remunerations.id=remuneration_exemptions.remuneration_id group by remuneration_exemptions.exemption_fig_group_title";
        $exemption_list=DB::select($sql_exemptions, [$profiles[0]->payroll_profile_id, $emp_payslip_no]);
        foreach($exemption_list as $el){
            $fig_ex_grp[$el->exemption_fig_group_title]=$el->exemption_fig_group_total;
        }
        //
        //-2022-12-02-
        //

        /**/
        $sql_main="SELECT fig_name, fig_group, fig_group_title, fig_base_ratio, fig_value, fig_hidden, epf_payable, remuneration_pssc
                FROM (SELECT drv_figs.fig_name, drv_figs.fig_group, drv_figs.fig_group_title, drv_figs.fig_value AS fig_base_ratio,
                             COALESCE(NULLIF(drv_figs.fig_value*(((drv_figs.fig_group='FIXED') * ? * ?) + ((drv_figs.fig_group='FIXED') * (1 - ?)) +
                                                                 (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) +
                                                                 (? * drv_figs.nopay_payable * (drv_figs.fig_group='BASIC')) + (? * (drv_figs.fig_group='OTHRS1')) + (? * (drv_figs.fig_group='OTHRS2')))
                                                 *drv_figs.pay_per_day, 0), (drv_figs.fig_value*drv_figs.fig_revise)) AS fig_value, 
                             drv_figs.fig_hidden, drv_figs.epf_payable, drv_figs.remuneration_pssc FROM (SELECT 'Basic' AS fig_name,
                                                                'BASIC' AS fig_group, 'BASIC' AS fig_group_title, COALESCE(NULLIF(CAST(?*? AS DECIMAL(10,2)), 0), ?) AS fig_value, ? AS pay_per_day, 
                                                        1 AS fig_revise, 0 AS fig_hidden, 1 AS epf_payable, 1 AS work_payable, 1 AS nopay_payable, 'BASIC' AS remuneration_pssc
                             UNION ALL SELECT 'No pay' AS fig_name, 'BASIC' AS fig_group, 'NOPAY' AS fig_group_title, ? AS fig_value, 
                                                                                                                                                                                                                                                                                                                                                                                                                   1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 1 AS nopay_payable, 'NOPAY' AS remuneration_pssc UNION ALL SELECT 'Normal OT' AS fig_name, 'OTHRS1' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS1' AS remuneration_pssc UNION ALL SELECT 'Double OT' AS fig_name, 'OTHRS2' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS2' AS remuneration_pssc UNION ALL select drv_allfacility.remuneration_name AS fig_name, IFNULL(drv_allfacility.fig_group, 'BASIC') AS fig_group, 'FACILITY' AS fig_group_title, (IFNULL(drv_dayfacility.pre_eligible_amount, drv_empfacility.new_eligible_amount)*drv_allfacility.value_group) AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, drv_allfacility.epf_payable, 1 AS work_payable, 0 AS nopay_payable, drv_allfacility.pssc AS remuneration_pssc from (SELECT `remuneration_id`, `new_eligible_amount` FROM `remuneration_profiles` WHERE `payroll_profile_id`=? AND `remuneration_signout`=0) AS drv_empfacility INNER JOIN (SELECT id, remuneration_name, remuneration_type, value_group, epf_payable, allocation_method AS fig_group, payslip_spec_code AS pssc FROM remunerations WHERE allocation_method='FIXED' AND remuneration_cancel=0) AS drv_allfacility ON drv_empfacility.remuneration_id=drv_allfacility.id LEFT OUTER JOIN (SELECT remuneration_id, pre_eligible_amount, 'FIXED' AS fig_group FROM remuneration_eligibility_days WHERE ? BETWEEN min_days AND max_days) AS drv_dayfacility ON drv_allfacility.id=drv_dayfacility.remuneration_id) AS drv_figs UNION ALL SELECT drv_docs.fig_name, drv_docs.fig_group, drv_docs.fig_group_title, drv_docs.fig_value AS fig_base_ratio, drv_docs.fig_value, drv_docs.fig_hidden, drv_docs.epf_payable, drv_docs.remuneration_pssc FROM (SELECT remunerations.remuneration_name AS fig_name, 'ADDITION' AS fig_group, 'ADDITION' AS fig_group_title, (employee_term_payments.payment_amount*remunerations.value_group) AS fig_value, 0 AS fig_hidden, remunerations.epf_payable, remunerations.payslip_spec_code AS remuneration_pssc FROM (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS employee_term_payments INNER JOIN remunerations ON employee_term_payments.remuneration_id=remunerations.id) AS drv_docs) AS drv_main";
        $employee = DB::select($sql_main, [$emp_work, $profiles[0]->pay_per_day, $profiles[0]->pay_per_day, $emp_work, $emp_leave, $emp_nopay, $emp_ot_i, $emp_ot_ii, $profiles[0]->day_salary, $profiles[0]->pay_per_day, $profiles[0]->basic_salary, $profiles[0]->pay_per_day, ($profiles[0]->day_salary*-1), ($profiles[0]->day_salary/8), (($profiles[0]->day_salary*1)/8), $profiles[0]->payroll_profile_id, $emp_work, $profiles[0]->payroll_profile_id, $emp_payslip_no]);


        $figs_list = array();
        $epf_payable_tot = 0;

        foreach($employee as $r){
            if($r->epf_payable){
                $epf_payable_tot += $r->fig_value;
            }

            if(!isset($figs_list[$r->remuneration_pssc])){
                $figs_list[$r->remuneration_pssc]=array(
                    'fig_grp_title'=>$r->fig_group_title,
                    'fig_val'=>0,
                    'fig_base_rate'=>$r->fig_base_ratio
                );
            }

            $figs_list[$r->remuneration_pssc]['fig_val'] += $r->fig_value;
        }

        $payperiod_workdays=30; $payperiod_holidays=0;
        $payperiod_netdays=($payperiod_workdays-$payperiod_holidays)*-1;

        $reg_keys = array('NOPAY', 'OTHRS1', 'OTHRS2');
        //-2022-12-02
        /*
        $reg_cols = array('NOPAY'=>array('fig_premium'=>1, 'key_param'=>$payperiod_netdays),
            'OTHRS1'=>array('fig_premium'=>1.5, 'key_param'=>240),
            'OTHRS2'=>array('fig_premium'=>2, 'key_param'=>240)
        );
        */
        $reg_cols = array('NOPAY'=>array('fig_premium'=>1, 'key_param'=>$payperiod_netdays, 'fig_ex_key'=>'NOPAY'),
            'OTHRS1'=>array('fig_premium'=>1.5, 'key_param'=>240, 'fig_ex_key'=>'OTHRS'),
            'OTHRS2'=>array('fig_premium'=>2, 'key_param'=>240, 'fig_ex_key'=>'OTHRS')
        );
        //-2022-12-02-

        foreach($figs_list as $k=>$v){
            if(in_array($k, $reg_keys)){
                $units_tot = ($figs_list[$k]['fig_val']/$figs_list[$k]['fig_base_rate']);

                //-2022-12-02
                /*
                $new_base_rate = (($epf_payable_tot*$reg_cols[$k]['fig_premium'])/$reg_cols[$k]['key_param']);
                */
                $act_ex_key = $reg_cols[$k]['fig_ex_key'];
                $fig_ex_val = $fig_ex_grp[$act_ex_key];
                $new_base_rate = ((($epf_payable_tot-$fig_ex_val)*$reg_cols[$k]['fig_premium'])/$reg_cols[$k]['key_param']);
                //2022-12-02-

                $figs_list[$k]['fig_val']=number_format((float)($new_base_rate*$units_tot), 2, '.', '');
                $figs_list[$k]['fig_base_rate']=number_format((float)$new_base_rate, 2, '.', '');
            }
        }

        $data = array(
            'BRA_I'=> $figs_list['BRA_I']['fig_val'],
            'add_bra2'=> $figs_list['add_bra2']['fig_val'],
            'NOPAY'=> $figs_list['NOPAY']['fig_val'],
        );

        return $data;

        //return response()->json(['nopay_val' => $figs_list['NOPAY']['fig_val']]);
        //}
    }

    public function no_pay_report_list_month(Request $request)
    {
        $permission = Auth::user()->can('no-pay-report');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $month = $request->get('month');

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

        $emp_query .= ' order by employees.emp_id ';

        $data = DB::select($emp_query);

        //remove emp_id's which doesn't have no pay
        $index = 0;
        foreach ($data as $d){
            $no_pay_days = (new \App\Attendance)->get_no_pay_days($d->emp_id, $month);
            if($no_pay_days == 0){
                //remove emp_id from the array
                unset($data[$index]);
            }
            $index++;
        }

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('month', function ($row) use ($month) {
                return $month;
            })
            ->addColumn('work_days', function($row) use ($month) {
                $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month);
                return $work_days;
            })
            ->addColumn('leave_days', function($row) use ($month){
                $leave_days = (new \App\Attendance)->get_leave_days($row->emp_id, $month);
                return $leave_days;
            })
            ->addColumn('no_pay_days_data', function ($row) use ($month) {
                $no_pay_days = (new \App\Attendance)->get_no_pay_days($row->emp_id, $month);

                $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month);
                $leave_days = (new \App\Attendance)->get_leave_days($row->emp_id, $month);

                $ot_hours = (new \App\Attendance)->get_ot_hours($row->emp_id, $month);
                $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
                $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

                $no_pay_amount_data = $this->get_no_pay_amount($row->emp_id, $work_days, $leave_days, $no_pay_days, $normal_rate_otwork_hrs, $double_rate_otwork_hrs);

                $no_pay_amount = $no_pay_amount_data['NOPAY'];

                //add 2 cols, bra_1, bra_2 Capital
                $BRA_I = $no_pay_amount_data['BRA_I'];
                $add_bra2 = $no_pay_amount_data['add_bra2'];

                //convert no_pay_amount to unsigned integer
                $no_pay_amount = abs($no_pay_amount);

                $basic_salary = '0.00';

                $emp_salary_info = PayrollProfile::where('emp_etfno', $row->emp_id)->first();
                if(!empty($emp_salary_info)){
                    $basic_salary = $emp_salary_info->basic_salary;
                }

                $view_no_pay_days_btn = $no_pay_days.' <a href="javascript:void(0)" class="btn btn-xs btn-primary view_no_pay_days" data-id="'.$row->emp_id.'" data-month="'.$month.'" data-amount="'.$no_pay_amount.'" data-basic="'.$basic_salary.'">View</a>';

                return array(
                    'no_pay_days' => $no_pay_days,
                    'basic_salary' => number_format($basic_salary, 2),
                    'BRA_I' => number_format($BRA_I, 2),
                    'add_bra2' => number_format($add_bra2, 2),
                    'amount' => number_format($no_pay_amount, 2),
                    'view_no_pay_days_btn' => $view_no_pay_days_btn
                );
            })

            ->addColumn('view_no_pay_days_btn', function ($row) use ($month) {
                $no_pay_days = (new \App\Attendance)->get_no_pay_days($row->emp_id, $month);

                $view_no_pay_days_btn = $no_pay_days;

                if($no_pay_days != 0 ){
                    $view_no_pay_days_btn = $no_pay_days.' <a href="javascript:void(0)" title="View Details" class="btn btn-xs btn-default view_no_pay_days_btn float-right " data-id="'.$row->emp_id.'" data-month="'.$month.'" > <i class="fa fa-eye text-primary"></i> </a>';
                }

                return $view_no_pay_days_btn;
            })

            ->rawColumns(['action',
                'work_days',
                'month',
                'leave_days',
                'no_pay_days_data',
                'view_no_pay_days_btn'
            ])
            ->make(true);
    }

    public function no_pay_days_data(Request $request)
    {
        $permission = Auth::user()->can('no-pay-report');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $emp_id = $request->emp_id;
        $month = $request->month;

        $no_pay_days = (new \App\Attendance)->get_no_pay_days($emp_id, $month);

        $no_pay_days_data = Leave::where('leave_type', 3)
            ->where('emp_id', $emp_id)
            ->where('leave_from', 'like', $month.'%')
            ->where('status', '=', 'Approved')
            ->get();

        $employee = Employee::where('emp_id', $emp_id)->first();

        $no_pay_days_data_html = '<table> 
                                    <tr>
                                        <th> Employee </th>
                                        <td> '. $employee->emp_name_with_initial .' </td>  
                                    </tr> 
                                    <tr>
                                        <th> Month </th>
                                        <td> '. $month .' </td>  
                                    </tr> 
                                    </table>';

        $no_pay_days_data_html .= '<table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Leave From</th>
                                            <th>Leave To</th>
                                            <th>No of Days</th> 
                                        </tr>
                                    </thead>
                                    <tbody>';

        foreach ($no_pay_days_data as $no_pay_day){
            $no_pay_days_data_html .= '<tr>
                                        <td>'.Carbon::parse($no_pay_day->leave_from)->format('d-m-Y').'</td>
                                        <td>'.Carbon::parse($no_pay_day->leave_to)->format('d-m-Y').'</td>
                                        <td>'.$no_pay_day->no_of_days.'</td> 
                                    </tr>';
        }

        $no_pay_days_data_html .= '<tr>
                                        <th colspan="2" class="text-right">Total</th>
                                        <th>'.$no_pay_days.'</th> 
                                    </tr>';

        $no_pay_days_data_html .= '</tbody>
                                </table>';

        return $no_pay_days_data_html;

    }




}
