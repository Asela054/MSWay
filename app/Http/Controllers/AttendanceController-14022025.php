<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\AttendanceClear;
use App\AttendanceEdited;
use App\Department;
use App\FingerprintUser;
use App\FingerprintDevice;
use App\Employee;
use App\employeeWorkRate;
use App\Holiday;
use App\LateAttendance;
use App\Leave;
use App\LeaveDetail;
use App\OtApproved;
use App\Shift;
use App\ShiftType;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use ZKLib;
use Validator;
use Excel;
use Carbon\Carbon;
use DateInterval;
use DateTime;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('attendance-sync');
        if (!$permission) {
            abort(403);
        }
        $device = DB::table('fingerprint_devices')
            ->leftjoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
            ->select('fingerprint_devices.*', 'branches.location')
            ->get();
        return view('Attendent.attendance', compact('device'));
    }

    public function attendance_list_ajax(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-sync');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        ## Read value
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

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        // Total records
        $totalRecords_array = DB::select('
            SELECT COUNT(*) as acount
                FROM
                (
                    SELECT COUNT(*)
                    from `attendances` as `at1` 
                    inner join `employees` on `at1`.`uid` = `employees`.`emp_id` 
                    left join `shift_types` on `employees`.`emp_shift` = `shift_types`.`id` 
                    left join `branches` on `at1`.`location` = `branches`.`id` 
                    WHERE at1.deleted_at IS NULL
                    group by `at1`.`uid`, `at1`.`date` 
                    having count(timestamp) < 2
                )t
            ');

        $totalRecords = $totalRecords_array[0]->acount;

        $query1 = 'SELECT COUNT(*) as acount ';
        $query1 .= 'FROM ( ';
        $query1 .= 'SELECT COUNT(*) ';
        $query2 = 'FROM `attendances` as `at1` ';
        $query2 .= 'inner join `employees` on `at1`.`uid` = `employees`.`emp_id` ';
        $query2 .= 'left join `shift_types` on `employees`.`emp_shift` = `shift_types`.`id` ';
        $query2 .= 'left join `branches` on `at1`.`location` = `branches`.`id` ';
        $query2 .= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
        $query2 .= 'WHERE 1 = 1 AND at1.deleted_at IS NULL ';
        //$searchValue = 'Breeder Farm';
        if ($searchValue != '') {
            $query2 .= 'AND ';
            $query2 .= '( ';
            $query2 .= 'employees.emp_id like "' . $searchValue . '%" ';
            $query2 .= 'OR employees.emp_name_with_initial like "' . $searchValue . '%" ';
            $query2 .= 'OR at1.timestamp like "' . $searchValue . '%" ';
            $query2 .= 'OR branches.location like "' . $searchValue . '%" ';
            $query2 .= ') ';
        }

        if ($department != '') {
            $query2 .= 'AND departments.id = "' . $department . '" ';
        }

        if ($employee != '') {
            $query2 .= 'AND employees.emp_id = "' . $employee . '" ';
        }

        if ($location != '') {
            $query2 .= 'AND at1.location = "' . $location . '" ';
        }

        if ($from_date != '' && $to_date != '') {
            $query2 .= 'AND at1.date BETWEEN "' . $from_date . '" AND "' . $to_date . '" ';
        }

        $query6 = 'group by `at1`.`uid`, `at1`.`date` ';
        $query6 .= 'having count(timestamp) < 2 ';
        $query4 = ') t ';
        $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
        $query7 = 'ORDER BY ' . $columnName . ' ' . $columnSortOrder . ' ';

        $totalRecordswithFilter_arr = DB::select($query1 . $query2 . $query6 . $query4);
        $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

        $query3 = 'select `shift_types`.*, `at1`.*, at1.id as at_id, Max(at1.timestamp) as lasttimestamp, Min(at1.timestamp) as firsttimestamp,
                `employees`.`emp_name_with_initial`, `branches`.`location` as b_location, departments.name as dep_name ';

        $records = DB::select($query3 . $query2 . $query6 . $query7 . $query5);

        $data_arr = array();
        foreach ($records as $record) {

            //get only the date from date
            $date = date('Y-m-d', strtotime($record->date));

            $first_timestamp = date('H:i', strtotime($record->firsttimestamp));
            $last_timestamp = date('H:i', strtotime($record->lasttimestamp));

            $data_arr_i = array(
                "at_id" => $record->at_id,
                "uid" => $record->uid,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "firsttimestamp" => $first_timestamp,
                "begining_checkout" => $record->begining_checkout,
                "date_row" => $record->date,
                "date" => $date,
                "lasttimestamp" => $last_timestamp,
                "ending_checkout" => $record->ending_checkout,
                "location" => $record->b_location,
                "dep_name" => $record->dep_name
            );

            if ((date('G:i', strtotime($record->firsttimestamp))) < (Carbon::parse($record->begining_checkout))) {
                $data_arr_i["btn_in"] = true;
                $data_arr_i["btn_out"] = false;
            } else {
                $data_arr_i["btn_in"] = false;
                $data_arr_i["btn_out"] = true;
            }

            if(Auth::user()->can('attendance-delete')){
                $data_arr_i["btn_delete"] = true;
            }else{
                $data_arr_i["btn_delete"] = false;
            }

            $data_arr[] = $data_arr_i;
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

    public function getdevicedata(Request $request)
    {
        ini_set('max_execution_time', 3000);
        //dd($request->device);
        $device = FingerprintDevice::where('ip', '=', $request->device)->get();
        $device = DB::table('fingerprint_devices')->where('ip', '=', $request->device)->first();
        $ip = $device->ip;
        $sync_date = $request->sync_date;

        $name = $device->name;

        $name = new ZKLib(
            $ip // '112.135.69.27' //your device IP
        );
        $ret = $name->connect();
        if ($ret) {
            $name->disableDevice();

            $attendance = $name->getAttendance();

            dd($attendance);
            //keep timestamp like sync_date% and remove the rest (applied only for jaya farm)
//            $attendance = array_filter($attendance, function ($item) use ($sync_date) {
//                return strpos($item['timestamp'], $sync_date) !== false;
//            });

            $location = $device->location;
            $serial = $name->serialNumber();
            $deviceserial = substr($serial, strpos($serial, "=") + 1, -1);

            $is_ok = true; 

            foreach ($attendance as $link) {

                $newtimestamp = $link['timestamp'];

                $Attendance = Attendance::firstOrNew(['timestamp' => $link['timestamp'], 'devicesno' => $deviceserial]);
                //$Attendance->id=$id= $link['id'];
                $Attendance->uid = $link['id'];
                $Attendance->emp_id = $link['id'];
                $Attendance->state = $link['state'];
                $Attendance->timestamp = $link['timestamp'];
                $timestamp = $link['timestamp'];
                $Attendance->date = substr($timestamp, 0, -9);
                $Attendance->type = $link['type'];
                $Attendance->devicesno = $deviceserial;
                $Attendance->location = $location;
                $is_ok = $Attendance->save(); 
                $errors = "Something went wrong";

            }

            $res = array(
                "status" => $is_ok,
                "message" => "Attendance data has been imported successfully"
            );

            return response()->json($res);
        }


        $name->enableDevice();
        $name->disconnect();

    }

    //cleardevicedata
    public function cleardevicedata(Request $request)
    {
        //dd($request->device);
        $device = FingerprintDevice::where('ip', '=', $request->device)->get();
        $device = DB::table('fingerprint_devices')->where('ip', '=', $request->device)->first();
        $ip = $device->ip;
        $sync_date = $request->sync_date;

        $name = $device->name;

//        $name = new ZKLib(
//            $ip // '112.135.69.27' //your device IP
//        );

        $location = $device->location;
        $serial = $device->conection_no;
        $deviceserial = substr($serial, strpos($serial, "=") + 1, -1);

        $attendance_clear_data = array(
            'user_id' => Auth::user()->id,
            'device_id' => $device->id,
            'location_id' => $location
        );

        $attendance_clear_data = AttendanceClear::create($attendance_clear_data);

        $res = array(
            "status" => true,
            "message" => "Attendance data has been cleared successfully"
        );
        return response()->json($res);

        die();

//        $ret = $name->connect();
//        if ($ret) {
//            $name->disableDevice();
//            $attendance = $name->clearattendance();
//
//            $location = $device->location;
//            $serial = $name->serialNumber();
//            $deviceserial = substr($serial, strpos($serial, "=") + 1, -1);
//
//            $attendance_clear_data = array(
//                'user_id' => Auth::user()->id,
//                'devicesno' => $deviceserial,
//                'location_id' => $location
//            );
//
//            $attendance_clear_data = AttendanceClear::create($attendance_clear_data);
//
//            $res = array(
//                "status" => true,
//                "message" => "Attendance data has been cleared successfully"
//            );
//            return response()->json($res);
//        }
//
//
//        $name->enableDevice();
//        $name->disconnect();

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'employee' => 'required',
            'in_time_s' => 'required',
            'out_time_s' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $employees = DB::table('employees')
            ->join('branches', 'employees.emp_location', '=', 'branches.id')
            ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
            ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
            ->groupBy('fingerprint_devices.location')
            ->where('employees.emp_id', $request->employee)
            ->get();

        //in time
        $timestamp = $request->in_time_s;
        $data = array(
            'uid' => $request->employee,
            'state' => 1,
            'timestamp' => $timestamp,
            'date' => $timestampdate = substr($timestamp, 0, -6),
            'approved' => 0,
            'type' => 255,
            'devicesno' => $employees[0]->sno,
            'location' => $employees[0]->location
        );
        $id = DB::table('attendances')->insert($data);

        //off time
        $offTimeStamp = $request->out_time_s;
        $data = array(
            'uid' => $request->employee,
            'state' => 1,
            'timestamp' => $offTimeStamp,
            'date' => substr($offTimeStamp, 0, -6),
            'approved' => 0,
            'type' => 255,
            'devicesno' => $employees[0]->sno,
            'location' => $employees[0]->location
        );
        $id = DB::table('attendances')->insert($data);

        return response()->json(['success' => 'Attendent Inserted successfully.']);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            $data = Attendance::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function attendanceedit()
    {
        $user = Auth::user();
        $permission = $user->can('attendance-create');
        if (!$permission) {
            abort(403);
        }
        $fingerprints=DB::table('fingerprint_devices')
        ->select('id','name','conection_no')
        ->where('status',1)
        ->get();

        return view('Attendent.attendanceedit',compact('fingerprints'));
    }

    /**
     * @throws \Exception
     */
    public function attendance_list_for_edit(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $department = $request->get('department');
            $employee = $request->get('employee');
            $location = $request->get('location');
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');

            $query = DB::query()
                ->select('at1.id',
                    'at1.emp_id',
                    'at1.uid',
                    'at1.state',
                    'at1.timestamp',
                    'at1.date',
                    'at1.approved',
                    'at1.type',
                    'at1.devicesno',
                    DB::raw('Min(at1.timestamp) as firsttimestamp'),
                    DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                    'employees.emp_name_with_initial',
                    'branches.location',
                    'departments.name as dep_name'
                )
                ->from('attendances as at1')
                ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
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
                $query->whereBetween('at1.date', [$from_date, $to_date]);
            }

            $query->where(['at1.deleted_at' => null]);

            $query->where(['approved' => '0'])
                ->groupBy('at1.uid', 'at1.date');

            return Datatables::of($query)
                ->addIndexColumn()
                ->orderColumn('formatted_date', 'date')
                //formatted date
                ->editColumn('formatted_date', function ($row) {
                    return date('Y-m-d', strtotime($row->date));
                })
                //first_time_stamp
                ->addColumn('first_time_stamp', function ($row) {
                    $first_timestamp = date('H:i', strtotime($row->firsttimestamp));
                    return $first_timestamp;
                })
                //last_time_stamp
                ->addColumn('last_time_stamp', function ($row) {
                    $lasttimestamp = date('H:i', strtotime($row->lasttimestamp));
                    return $lasttimestamp;
                })
                ->addColumn('action', function ($row) {

                    $btn = ' <button type="submit" name="view_button" 
                        style="margin:1px;"
                        uid="' . $row->uid . '" 
                        data-date="' . $row->date . '" 
                        data-name="' . $row->emp_name_with_initial . '"  
                        class="view_button btn btn-outline-dark btn-sm ml-1 "><i class="fas fa-eye"></i></button> ';

                    $user = Auth::user();
                    $permission = $user->can('attendance-edit');
                    if ($permission) {
                        $btn .= ' <button type="submit" name="edit_button" 
                        style="margin:1px;"
                        uid="' . $row->uid . '" 
                        data-date="' . $row->date . '" 
                        data-name="' . $row->emp_name_with_initial . '"  
                        class="edit_button btn btn-outline-primary btn-sm ml-1"><i class="fas fa-pencil-alt"></i></button> ';
                    }

                    $permission = $user->can('attendance-delete');
                    if ($permission) {
                        $btn .= ' <button type="submit" name="delete_button"
                            style="margin:1px;"
                            data-uid="' . $row->uid . '" 
                            data-date="' . $row->date . '" 
                            data-name="' . $row->emp_name_with_initial . '"  
                            class="delete_button btn btn-outline-danger btn-sm ml-1"><i class="fas fa-trash"></i></button> ';
                    }

                    return $btn;
                })
                ->rawColumns(['action',
                    'formatted_date',
                    'first_time_stamp',
                    'last_time_stamp'
                ])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }

    public function AttendanceEditBulk()
    {
        $user = Auth::user();
        $permission = $user->can('attendance-edit');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.attendanceEditBulk');
    }

    public function attendance_list_for_month_edit(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $month = $request->month;
        $emp_id = $request->emp;

        $query = DB::query()
            ->select('at1.*',
                DB::raw('Min(at1.timestamp) as firsttimestamp'),
                DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                'employees.emp_id')
            ->where(['employees.emp_id' => $emp_id])
            ->where('date', 'like', $month . '%')
            ->where(['at1.deleted_at' => null])
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
            ->groupBy('at1.uid', 'at1.date');;

        $attendances = $query->get();

        //add timestamp->format('Y-m-d\TH:i:s') to each row
        foreach ($attendances as $attendance) {
            $timestamp = Carbon::parse($attendance->firsttimestamp);
            $lasttimestamp = Carbon::parse($attendance->lasttimestamp);
            //var_dump($timestamp);
            //die();
            $attendance->firsttime_rfc = $timestamp->format('Y-m-d\TH:i');
            $attendance->lasttime_rfc = $lasttimestamp->format('Y-m-d\TH:i');

            $attendance->firsttime_24 = $timestamp->format('Y-m-d H:i');
            $attendance->lasttime_24 = $lasttimestamp->format('Y-m-d H:i');
        }


        return response()->json(['attendances' => $attendances, 'status' => true], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'uid' => 'required',
            'timestamp' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'uid' => $request->uid,
            'timestamp' => $request->timestamp,
            'date' => $attendance->date = $timestampdate = substr($request->timestamp, 0, -9)

        );

        $attendanceedited = new AttendanceEdited;
        $attendanceedited->id = $request->input('uid');
        $attendanceedited->date = $request->input('timestamp');
        $attendanceedited->edited_user_id = $request->input('userid');
        $attendanceedited->save();


        Attendance::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    public function exportattendances()
    {
        $emp_data = DB::table('attendances')
            ->join('employees', 'attendances.uid', '=', 'employees.id')
            ->select('attendances.*', 'employees.*')
            ->get();

        $emp_array[] = array('Employee Id', 'Name', 'TimeStamp', 'Date');
        foreach ($emp_data as $employee) {
            $emp_array[] = array(
                'Employee Id' => $employee->uid,
                'Name' => $employee->emp_first_name,
                'TimeStamp' => $employee->timestamp,
                'Date' => $employee->date

            );
        }
        Excel::create('Employee Data', function ($excel) use ($emp_array) {
            $excel->setTitle('Employee Data');
            $excel->sheet('Employee Data', function ($sheet) use ($emp_array) {
                $sheet->fromArray($emp_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }


    public function getAttendance(Request $request)
    {


        $data = DB::table('attendances')
            ->select('attendances.*', 'employees.emp_name_with_initial', 'employees.emp_etfno', 'shift_types.begining_checkout', 'shift_types.ending_checkout')
            ->Join('employees', 'attendances.uid', '=', 'employees.emp_id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->where([
                ['date', '=', $request->date],
                ['uid', '=', $request->id],

            ])->get();


        return response()->json($data);

        //echo json_encode($data);
    }

    public function attendentUpdateLive(Request $request)
    {
        if ($request->ajax()) {
            $data = array(
                'timestamp' => $request->timestamp,
                'date' => $timestampdate = substr($request->timestamp, 0, -6)
            );

            $employees = DB::table('employees')
                ->join('branches', 'employees.emp_location', '=', 'branches.id')
                ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
                ->where('employees.emp_id', $request->userid)
                ->groupBy('fingerprint_devices.location')
                ->get();

            $data = array(
                'uid' => $request->userid,
                'state' => 1,
                'timestamp' => $request->timestamp,
                'date' => $timestampdate = substr($request->timestamp, 0, -8),
                'approved' => 0,
                'type' => 255,
                'devicesno' => $employees[0]->sno,
                'location' => $employees[0]->location
            );
            DB::table('attendances')
                ->where('id', $request->id)
                ->update($data);

            //   return response()->json(['success' => 'Data is successfully updated']);
            echo '<div class="alert alert-success">Attendent Updated</div>';
        }
    }

    public function attendentinsertlive(Request $request)
    {

        if ($request->ajax()) // dd($employees);


        {

            $employees = DB::table('employees')
                ->join('branches', 'employees.emp_location', '=', 'branches.id')
                ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
                ->groupBy('fingerprint_devices.location')
                ->get();

            $time_stamp = $request->timestamp;

            $data = array(
                'uid' => $request->userid,
                'state' => 1,
                'timestamp' => $time_stamp,
                'date' => substr($time_stamp, 0, -6),
                'approved' => 0,
                'type' => 255,
                'devicesno' => $employees[0]->sno,
                'location' => $employees[0]->location
            );
            $id = DB::table('attendances')->insert($data);
            if ($id > 0) {
                echo '<div class="alert alert-success">Attendant Inserted</div>';
            }
        }
    }

    public function attendentdeletelive(Request $request)
    {
        if ($request->ajax()) {
            DB::table('attendances')
                ->where('id', $request->id)
                ->delete();
            echo 'Attendant Details Deleted';
        }
    }

    public function getAttendentChart(Request $request)
    {


        $data = DB::table('attendances')
            ->select('attendances.date', DB::raw('COUNT(attendances.uid) as count'))
            ->groupBy('attendances.date')
            ->limit(30)
            ->orderBy('attendances.date', 'desc')
            ->get();
        return response()->json($data);

    }


    public function getBranchAttendentChart(Request $request)
    {

        $today = Carbon::today();
        // dd($today);
        $data = DB::table('attendances')
            ->join('fingerprint_devices', 'attendances.devicesno', '=', 'fingerprint_devices.sno')
            ->join('branches', 'fingerprint_devices.location', '=', 'branches.id')
            ->select('branches.location', DB::raw('COUNT(attendances.uid) as count'))
            ->groupBy('attendances.devicesno')
            ->where('attendances.date', $today)
            ->limit(20)
            ->get();
        return response()->json($data);

    }

    public function attendanceapprovel()
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');
        if(!$permission){
            abort(403);
        }

        return view('Attendent.attendanceapprovel');
    }

    public function attendance_list_for_approve1(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        ## Read value
        $location = $request->get('company');
        $department = $request->get('department');
        $month = $request->get('month');

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
                    join `employees` on `at1`.`uid` = `employees`.`emp_id`  
                    left join `branches` on `at1`.`location` = `branches`.`id`   
                    left join shift_types ON employees.emp_shift = shift_types.id 
                    WHERE at1.deleted_at IS NULL
                    group by at1.uid 
                )t
            ');

        $totalRecords = $totalRecords_array[0]->acount;

        $query1 = 'SELECT COUNT(*) as acount ';
        $query1 .= 'FROM ( ';
        $query1 .= 'SELECT COUNT(*) ';
        $query2 = 'FROM `attendances` as `at1` ';
        $query2 .= ' join `employees` on `employees`.`emp_id` = `at1`.`uid` ';
        $query2 .= 'left join `branches` on `at1`.`location` = `branches`.`id` ';
        $query2 .= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
        $query2 .= 'left join shift_types ON employees.emp_shift = shift_types.id ';
        $query2 .= 'WHERE 1 = 1 AND at1.deleted_at IS NULL ';
        //$searchValue = 'Breeder Farm';
        if ($searchValue != '') {
            $query2 .= 'AND ';
            $query2 .= '( ';
            $query2 .= 'employees.emp_id like "' . $searchValue . '%" ';
            $query2 .= 'OR employees.emp_name_with_initial like "' . $searchValue . '%" ';
            $query2 .= 'OR at1.timestamp like "' . $searchValue . '%" ';
            $query2 .= 'OR branches.location like "' . $searchValue . '%" ';
            $query2 .= 'OR departments.name like "' . $searchValue . '%" ';
            $query2 .= ') ';
        }

        if ($department != '') {
            $query2 .= 'AND departments.id = "' . $department . '" ';
        }

        if ($location != '') {
            $query2 .= 'AND at1.location = "' . $location . '" ';
        }

        if ($month != '') {
            $query2 .= 'AND at1.date LIKE "' . $month . '%" ';
        }

        $query6 = 'group by at1.uid ';
        $query6 .= ' ';
        $query4 = ') t ';
        $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
        $query7 = 'ORDER BY ' . $columnName . ' ' . $columnSortOrder . ' ';

        $totalRecordswithFilter_arr = DB::select($query1 . $query2 . $query6 . $query4);
        $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

        // Fetch records
        $query3 = 'select 
            at1.*,
            Max(at1.timestamp) as lasttimestamp,
            Min(at1.timestamp) as firsttimestamp,
            employees.emp_id ,
            employees.emp_name_with_initial ,
            shift_types.onduty_time ,
            shift_types.offduty_time ,
            branches.location as b_location,
            departments.name as dept_name  
              ';

        $records = DB::select($query3 . $query2 . $query6 . $query7 . $query5);
        //error_log($query3.$query2.$query6.$query7.$query5);
        //var_dump(sizeof($records));
        //die();
        $data_arr = array();

        foreach ($records as $record) {

            $work_days = (new \App\Attendance)->get_work_days($record->emp_id, $month);
            $working_week_days_arr = (new \App\Attendance)->get_working_week_days($record->emp_id, $month);
            $working_week_days = $working_week_days_arr['no_of_working_workdays'];
            $leave_days = (new \App\Attendance)->get_leave_days($record->emp_id, $month);
            $no_pay_days = (new \App\Attendance)->get_no_pay_days($record->emp_id, $month);

            $rec_date = Carbon::parse($record->date)->toDateString();
            $date_c = Carbon::createFromFormat('Y-m-d', $rec_date);
            $monthName = $date_c->format('Y-m');

            $data_arr[] = array(
                "id" => $record->id,
                "uid" => $record->uid,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "date" => $monthName,
                "firsttimestamp" => $record->firsttimestamp,
                "lasttimestamp" => $record->lasttimestamp,
                "onduty_time" => $record->onduty_time,
                "offduty_time" => $record->offduty_time,
                "work_days" => $work_days,
                "working_week_days" => $working_week_days,
                "leave_days" => $leave_days,
                "no_pay_days" => $no_pay_days,
                "dept_name" => $record->dept_name,
                "location" => $record->b_location
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

    public function attendance_list_for_approve(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $location = $request->get('company');
        $department = $request->get('department');
        $month = $request->get('month');

        $query = DB::query()
            ->select('at1.id as attendance_id',
                'at1.emp_id',
                'at1.uid',
                'at1.state',
                'at1.timestamp',
                'at1.date',
                'at1.approved',
                'at1.type',
                'at1.devicesno',
                DB::raw('Min(at1.timestamp) as firsttimestamp'),
                DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                'employees.emp_name_with_initial',
                'branches.location',
                'departments.name as dept_name'
            )
            ->from('employees as employees')
            // ->leftJoin('attendances as at1', 'employees.emp_id', '=', 'at1.uid')
            ->leftJoin('attendances as at1', function ($join) use ($month) {
                $join->on('employees.emp_id', '=', 'at1.uid')
                    ->whereNull('at1.deleted_at'); 
                if (!empty($month)) {
                    $m_str = $month . "%";
                    $join->where('at1.date', 'like', $m_str); 
                }
            })
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department');

        if ($department != '') {
            $query->where(['departments.id' => $department]);
        }

        // if ($location != '') {
        //     $query->where(['at1.location' => $location]);
        // }

        $query->where('employees.deleted', 0);
        
        $query->groupBy('employees.emp_id');

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                if ($row->date) {
                    $rec_date = Carbon::parse($row->date)->toDateString();
                    $date_c = Carbon::createFromFormat('Y-m-d', $rec_date);
                    return $date_c->format('Y-m');
                }
                return '-';
            })
            ->addColumn('work_days', function ($row) use ($month) {
                if ($row->attendance_id) {
                    return $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month);
                }
                return 0;
            })
            ->addColumn('working_week_days', function ($row) use ($month) {
                if ($row->attendance_id) {
                    $working_week_days_arr = (new \App\Attendance)->get_working_week_days($row->emp_id, $month);
                    return $working_week_days_arr['no_of_working_workdays'];
                }
                return 0;
                
            })
            ->addColumn('working_hours', function ($row) use ($month) {
                if ($row->attendance_id) {
                    return $working_hours  = (new \App\Attendance)->get_working_hours($row->emp_id, $month);
                }
                return 0;
            })
            ->addColumn('leave_days', function ($row) use ($month) {
                if ($row->attendance_id) {
                    return $leave_days = (new \App\Attendance)->get_leave_days($row->emp_id, $month);
                }
                return 0;
            })
            ->addColumn('no_pay_days', function ($row) use ($month) {
                if ($row->attendance_id) {
                    return $no_pay_days = (new \App\Attendance)->get_no_pay_days($row->emp_id, $month);
                }
                return 0;
               
            })
            ->rawColumns(['date'])
            ->make(true);

    }


    public function attendance_list_for_bulk_edit(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        ## Read value
        $location = $request->get('company');
        $department = $request->get('department');
        $date = $request->get('date');
        $employee = $request->get('employee');

        $query = DB::query()
            ->select('at1.id',
                'at1.emp_id',
                'at1.uid',
                'at1.state',
                'at1.timestamp',
                'at1.date',
                'at1.approved',
                'at1.type',
                'at1.devicesno',
                DB::raw('Min(at1.timestamp) as firsttimestamp'),
                DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                'employees.emp_name_with_initial',
                'shift_types.onduty_time',
                'shift_types.offduty_time',
                'branches.location as b_location',
                'departments.name as dept_name',
                'departments.id as dept_id'
            )
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id');

        if ($department != '') {
            $query->where(['departments.id' => $department]);
        }

        if ($employee != '') {
            $query->where(['employees.emp_id' => $employee]);
        }

        if ($location != '') {
            $query->where(['at1.location' => $location]);
        }

        if ($date != '') {
            $query->where(['at1.date' => $date]);
        }

        $query->where(['at1.deleted_at' => null]);

        $query->groupBy('at1.uid', 'at1.date');

        //$data = $query->get();

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('month', function ($row) {
                $rec_date = Carbon::parse($row->date)->toDateString();
                try {
                    $date_c = Carbon::createFromFormat('Y-m-d', $rec_date);
                } catch (\Exception $e) {
                    return $e->getMessage() . ' ' . $rec_date . ' emp_id : ' . $row->emp_id . ' ' . $row->emp_name_with_initial;
                }

                return $monthName = $date_c->format('Y-m');
            })
            ->addColumn('formatted_first_timestamp', function ($row) {
                $parsed_first_time = Carbon::parse($row->firsttimestamp)->format('Y-m-d H:i');
                $first_time_arr = explode(' ', $parsed_first_time);
                return $formatted_first_timestamp = $first_time_arr[0] . "T" . $first_time_arr[1];
            })
            ->addColumn('formatted_last_timestamp', function ($row) {
                $parsed_last_time = Carbon::parse($row->lasttimestamp)->format('Y-m-d H:i');
                $last_time_arr = explode(' ', $parsed_last_time);
                return $formatted_last_timestamp = $last_time_arr[0] . "T" . $last_time_arr[1];
            })
            ->addColumn('location', function ($row) {
                return $row->b_location;
            })
            ->addColumn('first_24', function ($row) {
                return $parsed_first_time = Carbon::parse($row->firsttimestamp)->format('Y-m-d H:i');
            })
            ->addColumn('last_24', function ($row) {
                return $parsed_last_time = Carbon::parse($row->lasttimestamp)->format('Y-m-d H:i');
            })
            ->make(true);

    }

    public function AttendanceEditBulkSubmit(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $changed_records_in = $request->changed_records_in;
        $changed_records_out = $request->changed_records_out;

        if($changed_records_in){
            foreach ($changed_records_in as $cr) {

                if ($cr['existing_time_stamp'] == '' && $cr['time_stamp'] != '') {

                    $employee = DB::table('employees')
                        ->join('branches', 'employees.emp_location', '=', 'branches.id')
                        ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                        ->select('fingerprint_devices.sno', 'fingerprint_devices.location', 'employees.emp_id', 'employees.emp_name_with_initial')
                        ->groupBy('fingerprint_devices.location')
                        ->where('employees.emp_id', $cr['uid'])
                        ->first();

                    $data = array(
                        'emp_id' => $employee->emp_id,
                        'uid' => $employee->emp_id,
                        'state' => 1,
                        'timestamp' => $cr['time_stamp'],
                        'date' => $cr['date'],
                        'approved' => 0,
                        'type' => 255,
                        'devicesno' => $employee->sno,
                        'location' => $employee->location
                    );
                    $id = DB::table('attendances')->insert($data);

                } else {

                    $attendance = Attendance::where('uid', $cr['uid'])
                        ->where('date', $cr['date'])
                        ->where('timestamp', $cr['existing_time_stamp'])->first();

                    $prev_timestamp = $attendance->timestamp;

                    $attendance->timestamp = $cr['time_stamp'];

                    $attendance->save();

                    $log_data = array(
                        'attendance_id' => $attendance->id,
                        'emp_id' => $attendance->emp_id,
                        'date' => $cr['date'],
                        'prev_val' => $prev_timestamp,
                        'new_val' => $cr['time_stamp'],
                        'edited_user_id' => Auth::user()->id,
                    );

                    AttendanceEdited::create($log_data);

                    if ($cr['time_stamp'] == '') {
                        DB::table('attendances')
                            ->where('uid', $cr['uid'])
                            ->where('date', $cr['date'])
                            ->where('timestamp', $cr['time_stamp'])
                            ->delete();
                    }
                }
            }
        }

        if ($changed_records_out){
            foreach ($changed_records_out as $cr) {

                if ($cr['existing_time_stamp'] == '' && $cr['time_stamp'] != '') {

                    $employee = DB::table('employees')
                        ->join('branches', 'employees.emp_location', '=', 'branches.id')
                        ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                        ->select('fingerprint_devices.sno', 'fingerprint_devices.location', 'employees.emp_id', 'employees.emp_name_with_initial')
                        ->groupBy('fingerprint_devices.location')
                        ->where('employees.emp_id', $cr['uid'])
                        ->first();

                    $data = array(
                        'emp_id' => $employee->emp_id,
                        'uid' => $employee->emp_id,
                        'state' => 1,
                        'timestamp' => $cr['time_stamp'],
                        'date' => $cr['date'],
                        'approved' => 0,
                        'type' => 255,
                        'devicesno' => $employee->sno,
                        'location' => $employee->location
                    );
                    $id = DB::table('attendances')->insert($data);

                } else {

                    $attendance = Attendance::where('uid', $cr['uid'])
                        ->where('date', $cr['date'])
                        ->where('timestamp', $cr['existing_time_stamp'])->first();

                    $prev_timestamp = $attendance->timestamp;

                    $attendance->timestamp = $cr['time_stamp'];

                    $attendance->save();

                    $log_data = array(
                        'attendance_id' => $attendance->id,
                        'emp_id' => $attendance->emp_id,
                        'date' => $cr['date'],
                        'prev_val' => $prev_timestamp,
                        'new_val' => $cr['time_stamp'],
                        'edited_user_id' => Auth::user()->id,
                    );

                    AttendanceEdited::create($log_data);

                    if ($cr['time_stamp'] == '') {
                        DB::table('attendances')
                            ->where('uid', $cr['uid'])
                            ->where('date', $cr['date'])
                            ->where('timestamp', $cr['time_stamp'])
                            ->delete();
                    }
                }
            }
        }

        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);

    }

    public function attendance_update_bulk_submit(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $in_time = $request->in_time;
        $out_time = $request->out_time;
        $existing_time_stamp_in = $request->existing_time_stamp_in;
        $existing_time_stamp_out = $request->existing_time_stamp_out;
        $existing_time_stamp_in_rfc = $request->existing_time_stamp_in_rfc;
        $existing_time_stamp_out_rfc = $request->existing_time_stamp_out_rfc;
        $uid = $request->uid;
        $emp_id = $request->employee;
        $date = $request->date;
        $month = $request->month;
        $date_e = $request->date_e;

        for ($i = 0; $i < sizeof($in_time); $i++) {

            $full_date = $month.'-'.$date[$i];

            if (($in_time[$i] != '') && ($in_time[$i] != $existing_time_stamp_in_rfc[$i])) {

                $full_time_in = $in_time[$i];

                if ($existing_time_stamp_in[$i] != '' ) {

                    $attendance = Attendance::where('uid', $uid[$i])
                        ->where('date', $full_date)
                        ->where('timestamp', $existing_time_stamp_in[$i])->first();

                    $prev_timestamp = $attendance->timestamp;

                    $attendance->timestamp = $full_time_in;

                    $attendance->save();

                    $log_data = array(
                        'attendance_id' => $attendance->id,
                        'emp_id' => $attendance->emp_id,
                        'date' => $attendance->date,
                        'prev_val' => $prev_timestamp,
                        'new_val' => $full_time_in,
                        'edited_user_id' => Auth::user()->id,
                    );

                    AttendanceEdited::create($log_data);

                } else {
                    $employee = DB::table('employees')
                        ->join('branches', 'employees.emp_location', '=', 'branches.id')
                        ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                        ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
                        ->groupBy('fingerprint_devices.location')
                        ->where('employees.emp_id', $emp_id)
                        ->first();

                    $data = array(
                        'emp_id' => $emp_id,
                        'uid' => $emp_id,
                        'state' => 1,
                        'timestamp' => $full_time_in,
                        'date' => $full_date,
                        'approved' => 0,
                        'type' => 255,
                        'devicesno' => $employee->sno,
                        'location' => $employee->location
                    );
                    $id = DB::table('attendances')->insert($data);

                }

            }
        }

        for ($i = 0; $i < sizeof($out_time); $i++) {

            $full_date = $month.'-'.$date[$i];

            if (($out_time[$i] != '') && ($out_time[$i] != $existing_time_stamp_out_rfc[$i])) {

                $full_time_out = $out_time[$i];

                if ($existing_time_stamp_out[$i] != '') {

                    $attendance = Attendance::where('uid', $uid[$i])
                        ->where('date', $full_date)
                        ->where('timestamp', $existing_time_stamp_out[$i])->first();

                    $prev_timestamp = $attendance->timestamp;

                    $attendance->timestamp = $full_time_out;

                    $attendance->save();

                    $log_data = array(
                        'attendance_id' => $attendance->id,
                        'emp_id' => $attendance->emp_id,
                        'date' => $attendance->date,
                        'prev_val' => $prev_timestamp,
                        'new_val' => $full_time_out,
                        'edited_user_id' => Auth::user()->id,
                    );

                    AttendanceEdited::create($log_data);

                } else {
                    $employee = DB::table('employees')
                        ->join('branches', 'employees.emp_location', '=', 'branches.id')
                        ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                        ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
                        ->groupBy('fingerprint_devices.location')
                        ->where('employees.emp_id', $emp_id)
                        ->first();

                    $data = array(
                        'emp_id' => $emp_id,
                        'uid' => $emp_id,
                        'state' => 1,
                        'timestamp' => $full_time_out,
                        'date' => $full_date,
                        'approved' => 0,
                        'type' => 255,
                        'devicesno' => $employee->sno,
                        'location' => $employee->location
                    );
                    $id = DB::table('attendances')->insert($data);

                }

            }
        }

        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);

    }


    public function getAttendanceApprovel(Request $request)
    {

        // dd($request->id);
        $attendance = DB::query()
            ->select('at1.*', DB::raw('Min(at1.timestamp) as firsttimestamp'), DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'fingerprint_devices.location')
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.id')
            ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
            ->groupBy('at1.uid', 'at1.date')
            ->where([
                ['uid', '=', $request->id],
                ['approved', '=', '0'],
            ])
            ->get();


        return response()->json($attendance);


    }

    public function AttendentAprovel(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if ($request->ajax())

            $appval = 1;
        {
            $data = array(
                'approved' => $appval
            );
            DB::table('attendances')
                ->where('uid', $request->emp_id)
                ->update($data);

            $full_month = $request->month;
            //get 1st 4 characters
            $year = substr($full_month, 0, 4);
            //get last 2 characters
            $month = substr($full_month, -2);

            $startDate = new DateTime("$year-$month-01");
            $endDate = (clone $startDate)->modify('first day of next month');

            //delete existing records for the month and emp_id from employee_work_rates
            DB::table('employee_work_rates')
                ->where('emp_id', $request->emp_id)
                ->where('work_month', $month)
                ->where('work_year', $year)
                ->delete();


                $employee = DB::table('employees as e')
                ->join('job_categories as jc', 'e.job_category_id', '=', 'jc.id')
                ->select('e.id as empid', 'e.emp_id', 'jc.work_hour_date')
                ->where('e.deleted', 0)
                ->where('e.emp_id',  $request->emp_id)
                ->first();
                if ($employee) {
                    $empoyeeId = $employee->empid;
                    $empId = $employee->emp_id;
                    $workHourDate = $employee->work_hour_date;
                }

                if($workHourDate === "Hour"){

                    $totalworkHours = 0;
                    $totalweekworkshours = 0;
                    while ($startDate < $endDate) {
                        $todayDate = $startDate->format('Y-m-d');


                        $query = DB::table('attendances as at1')
                        ->select(
                            'at1.id',
                            'at1.emp_id',
                            'at1.timestamp',
                            'at1.date',
                            DB::raw('MIN(at1.timestamp) AS firsttimestamp'),
                            DB::raw('CASE WHEN MIN(at1.timestamp) = MAX(at1.timestamp) THEN "" 
                            ELSE MAX(at1.timestamp) END AS lasttimestamp'),
                            'shift_types.onduty_time',
                            'shift_types.offduty_time'
                        )
                        ->leftJoin('employees', 'at1.emp_id', '=', 'employees.emp_id')
                        ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
                        ->whereNull('at1.deleted_at')
                        ->where('employees.emp_id', $empId)
                        ->where('at1.date', $todayDate)
                        ->get();


                        if ($query->isNotEmpty()) {
                            $firsttimestamp = Carbon::parse($query->first()->firsttimestamp);
                            $lasttimestamp = Carbon::parse($query->first()->lasttimestamp);
                        
                            if ($firsttimestamp && $lasttimestamp && $firsttimestamp != $lasttimestamp) {
                                $workHours = $firsttimestamp->diffInHours($lasttimestamp);
                                $totalworkHours+= $workHours;
                            }
                        }

                        $totalweekworkshours = $totalworkHours -($request->ot + $request->dot);

                        $form_data = array(
                            'emp_id' => $request->emp_auto_id,
                            'emp_etfno' => $request->emp_id,
                            'work_year' => $year,
                            'work_month' => $month,
                            'work_days' => $request->workdays,
                            'working_week_days' => $request->working_week_days,
                            'work_hours' => $totalweekworkshours,
                            'leave_days' => $request->leavedate,
                            'nopay_days' => $request->nopay,
                            'normal_rate_otwork_hrs' => $request->ot,
                            'double_rate_otwork_hrs' => $request->dot,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        DB::table('employee_work_rates')->insert($form_data);

                        $startDate->add(new DateInterval('P1D')); // Add 1 day
                    }

                }else{
                    $form_data = array(
                        'emp_id' => $request->emp_auto_id,
                        'emp_etfno' => $request->emp_id,
                        'work_year' => $year,
                        'work_month' => $month,
                        'work_days' => $request->workdays,
                        'working_week_days' => $request->working_week_days,
                        'work_hours' => 0,
                        'leave_days' => $request->leavedate,
                        'nopay_days' => $request->nopay,
                        'normal_rate_otwork_hrs' => $request->ot,
                        'double_rate_otwork_hrs' => $request->dot,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
        
                    DB::table('employee_work_rates')->insert($form_data);
                }
                
           


            //echo '<div class="alert alert-success">Attendent Details Approved</div>';

            return response()->json(['status' => true, 'msg' => 'Attendance Details Approved']);


        }
    }


    public function getlateAttendance(Request $request)
    {


        $data = DB::table('attendances')
            ->select('attendances.*', 'employees.emp_name_with_initial', 'employees.emp_etfno', 'shift_types.onduty_time', 'shift_types.offduty_time')
            ->Join('employees', 'attendances.uid', '=', 'employees.emp_id')
            ->Join('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->where([
                ['attendances.date', '=', $request->date],
                ['attendances.uid', '=', $request->id],

            ])->get();


        return response()->json($data);

        //echo json_encode($data);
    }

    public function attendentdetails($id, $month)
    {
        $date = Carbon::now();


        $employee = DB::table('employees')
            ->where('emp_id', $id)
            ->select('id','emp_id', 'emp_name_with_initial', 'emp_etfno')->get();

        $workdays = DB::table('attendances')
            ->where('uid', $id)
            ->whereMonth('attendances.date', $date->month)
            ->groupBy('attendances.uid', 'attendances.date')
            ->count();

        $leave = DB::table('leaves')
            ->where('emp_id', $id)
            ->whereMonth('leaves.leave_from', $date->month)
            ->count();

        $nopay = DB::table('leaves')
            ->where('emp_id', $id)
            ->where('leave_type', 4)
            ->whereMonth('leaves.leave_from', $date->month)
            ->count();

        $ot = DB::table('attendances')
            ->where('uid', $id)
            ->whereDate('date', Carbon::TUESDAY)
            ->count();

        $attendance = DB::query()
            ->select('at1.*', DB::raw('count(*) as work_days'), DB::raw('Max(at1.timestamp) as lasttimestamp'), DB::raw('Min(at1.timestamp) as firsttimestamp'), 'employees.emp_name_with_initial', 'shift_types.onduty_time', 'shift_types.offduty_time')
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->groupBy('at1.uid', 'at1.date')
            ->whereMonth('at1.date', $date->month)
            ->where('uid', $id)
            ->get()->toarray();


        //dd($attendance);
        return view('Attendent.attendentdetails', compact('attendance', 'employee', 'date', 'workdays', 'leave', 'nopay', 'ot', 'month'));
    }

    public function AttendentAprovelBatch(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

            $location = $request->get('company');
            $department = $request->get('department');
            $month = $request->get('month');

            $selectedyear = substr($month, 0, 4);
            $selectedmonth = substr($month, -2);
            $startDate = new DateTime("$selectedyear-$selectedmonth-01");
            $endDate = (clone $startDate)->modify('first day of next month');
            
            $dateRange = [];
            while ($startDate < $endDate) {
                $dateRange[] = $startDate->format('Y-m-d');
                $startDate->add(new DateInterval('P1D'));
            }


            $query2 = 'FROM `attendances` as `at1` ';
            $query2 .= ' join `employees` on `employees`.`emp_id` = `at1`.`uid` ';
            $query2 .= 'left join `branches` on `at1`.`location` = `branches`.`id` ';
            $query2 .= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
            $query2 .= 'left join shift_types ON employees.emp_shift = shift_types.id ';
            $query2 .= 'WHERE 1 = 1 ';

            if ($department != '') {
                $query2 .= 'AND departments.id = "' . $department . '" ';
            }

            // if ($location != '') {
            //     $query2 .= 'AND at1.location = "' . $location . '" ';
            // }

            if ($month != '') {
                $query2 .= 'AND at1.date LIKE "' . $month . '%" ';
            }

            $query6 = 'group by at1.uid ';
            $query6 .= ' ';

            $query3 = 'select 
                at1.*,
                Max(at1.timestamp) as lasttimestamp,
                Min(at1.timestamp) as firsttimestamp,
                employees.emp_id ,
                employees.emp_name_with_initial ,
                employees.emp_status,
                shift_types.onduty_time ,
                shift_types.offduty_time ,
                branches.location as b_location,
                departments.name as dept_name,
                emp_department,
                employees.id as emp_auto_id
                ';

            $records = DB::select($query3 . $query2 . $query6);


                foreach ($records as $record) {

                    $totalworkHours = 0;
                    $totalweekworkshours = 0;
    
                    $work_days = (new \App\Attendance)->get_work_days($record->emp_id, $month);
                    $working_week_days_arr = (new \App\Attendance)->get_working_week_days($record->emp_id, $month);
                    $working_week_days = $working_week_days_arr['no_of_working_workdays'];
    
                    $working_week_days_confirmed = (new \App\Attendance)->get_working_week_days_confirmed($record->emp_id, $month);
    
                    $confirmed_wd = $working_week_days;
    
                    if($working_week_days_confirmed['no_of_days'] != null ){
                        $confirmed_wd = $working_week_days_confirmed['no_of_days'];
                    }
    
                    $leave_days = (new \App\Attendance)->get_leave_days($record->emp_id, $month);
                    $no_pay_days = (new \App\Attendance)->get_no_pay_days($record->emp_id, $month);
    
                    $normal_ot_hours = (new \App\OtApproved)->get_ot_hours_monthly($record->emp_id, $month);
    
                    $double_ot_hours = (new \App\OtApproved)->get_double_ot_hours_monthly($record->emp_id, $month);

                    $triple_ot_hours = (new \App\OtApproved)->get_triple_ot_hours_monthly($record->emp_id, $month);
    
                    $year_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->year;
                    $month_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->month;
    
                    DB::table('employee_work_rates')
                        ->where('emp_id', $record->emp_auto_id)
                        ->where('work_year', $year_rec)
                        ->where('work_month', $month_rec)
                        ->delete();
    
    
                        // Fetch employee job category and work hour data
                        $employee = DB::table('employees as e')
                            ->join('job_categories as jc', 'e.job_category_id', '=', 'jc.id')
                            ->select('e.id as empid', 'e.emp_id', 'e.emp_status', 'jc.work_hour_date','jc.salary_without_attendace')
                            ->where('e.deleted', 0)
                            ->where('e.id', $record->emp_auto_id)
                            ->first();
    
    
                        if ($employee) {
                            $empoyeeId = $employee->empid;
                            $empId = $employee->emp_id;
                            $empstatus = $employee->emp_status;
                            $workHourDate = $employee->work_hour_date;
                            $salarystatus = $employee->salary_without_attendace;
                        }
    
    
                    
                            if($workHourDate === "Hour"){

                                foreach ($dateRange as $todayDate) {
    
                                    $ignoredate = DB::table('ignore_days')
                                    ->select('ignore_days.*')
                                    ->whereDate('date', $todayDate)
                                    ->first();

                                    if(!$ignoredate){
                                        $query = DB::table('attendances as at1')
                                        ->select(
                                            'at1.id',
                                            'at1.emp_id',
                                            'at1.timestamp',
                                            'at1.date',
                                            DB::raw('MIN(at1.timestamp) AS firsttimestamp'),
                                            DB::raw('CASE WHEN MIN(at1.timestamp) = MAX(at1.timestamp) THEN NULL 
                                                    ELSE MAX(at1.timestamp) END AS lasttimestamp'),
                                            'shift_types.onduty_time',
                                            'shift_types.offduty_time'
                                        )
                                        ->leftJoin('employees', 'at1.emp_id', '=', 'employees.emp_id')
                                        ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
                                        ->whereNull('at1.deleted_at')
                                        ->where('at1.emp_id', $record->emp_id)
                                        ->where('at1.date', 'LIKE', $todayDate . '%')
                                        ->havingRaw('MIN(at1.timestamp) != MAX(at1.timestamp)')
                                        ->get();

                                        if ($query->isNotEmpty()) {
                                            $firsttimestamp = Carbon::parse($query->first()->firsttimestamp);
                                            $lasttimestamp = Carbon::parse($query->first()->lasttimestamp);
                                        
                                            if ($firsttimestamp && $lasttimestamp && $firsttimestamp != $lasttimestamp) {
                                                $diffInMinutes = $firsttimestamp->diffInMinutes($lasttimestamp);
                                                $workHours = round($diffInMinutes / 60, 2);
                                                $totalworkHours+= $workHours; 
                                            }
                                        }

                                    }
                                }
                                
                                $totalweekworkshours = $totalworkHours -($normal_ot_hours + $double_ot_hours);
        
                                if($salarystatus == 1 &&  $totalweekworkshours==0 && $empstatus == 1){
                                    $data3 = array(
                                        'emp_id' => $record->emp_auto_id,
                                        'emp_etfno' => $record->emp_id,
                                        'work_year' => $year_rec,
                                        'work_month' => $month_rec,
                                        'work_days' => 0,
                                        'working_week_days' => 0,
                                        'work_hours' => 0,
                                        'leave_days' => $leave_days,
                                        'nopay_days' => $no_pay_days,
                                        'normal_rate_otwork_hrs' => 0,
                                        'double_rate_otwork_hrs' => 0,
                                        'triple_rate_otwork_hrs' => 0,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    employeeWorkRate::create($data3);
                                }
                                else{   
                                    $datasql = array(
                                        'emp_id' => $record->emp_auto_id,
                                        'emp_etfno' => $record->emp_id,
                                        'work_year' => $year_rec,
                                        'work_month' => $month_rec,
                                        'work_days' => $work_days,
                                        'working_week_days' => $work_days,
                                        'work_hours' => $totalweekworkshours,
                                        'leave_days' => $leave_days,
                                        'nopay_days' => $no_pay_days,
                                        'normal_rate_otwork_hrs' => $normal_ot_hours,
                                        'double_rate_otwork_hrs' => $double_ot_hours,
                                        'triple_rate_otwork_hrs' => $triple_ot_hours,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    employeeWorkRate::create($datasql);
                                }
        
                            }else{

                                if($salarystatus == 1  && $work_days == 0 && $empstatus == 1 ){

                                    $data3 = array(
                                        'emp_id' => $record->emp_auto_id,
                                        'emp_etfno' => $record->emp_id,
                                        'work_year' => $year_rec,
                                        'work_month' => $month_rec,
                                        'work_days' => 0,
                                        'working_week_days' => 0,
                                        'work_hours' => 0,
                                        'leave_days' => $leave_days,
                                        'nopay_days' => $no_pay_days,
                                        'normal_rate_otwork_hrs' => 0,
                                        'double_rate_otwork_hrs' => 0,
                                        'triple_rate_otwork_hrs' => 0,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    employeeWorkRate::create($data3);

                                }else{

                                    $data2 = array(
                                        'emp_id' => $record->emp_auto_id,
                                        'emp_etfno' => $record->emp_id,
                                        'work_year' => $year_rec,
                                        'work_month' => $month_rec,
                                        'work_days' => $work_days,
                                        'working_week_days' => $work_days,
                                        'work_hours' => 0,
                                        'leave_days' => $leave_days,
                                        'nopay_days' => $no_pay_days,
                                        'normal_rate_otwork_hrs' => $normal_ot_hours,
                                        'double_rate_otwork_hrs' => $double_ot_hours,
                                        'triple_rate_otwork_hrs' => $triple_ot_hours,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    );
                                    employeeWorkRate::create($data2);
                                    
                                }
                                
                            }  
                }

            //return success msg json
            return response()->json(['success' => true, 'msg' => 'Successfully updated']);
    }

    public function AttendentAprovelBatch1(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $location = $request->get('company');
        $department = $request->get('department');
        $month = $request->get('month');

        $query2 = 'FROM `attendances` as `at1` ';
        $query2 .= ' join `employees` on `employees`.`emp_id` = `at1`.`uid` ';
        $query2 .= 'left join `branches` on `at1`.`location` = `branches`.`id` ';
        $query2 .= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
        $query2 .= 'left join shift_types ON employees.emp_shift = shift_types.id ';
        $query2 .= 'WHERE 1 = 1 ';

        if ($department != '') {
            $query2 .= 'AND departments.id = "' . $department . '" ';
        }

        if ($location != '') {
            $query2 .= 'AND at1.location = "' . $location . '" ';
        }

        if ($month != '') {
            $query2 .= 'AND at1.date LIKE "' . $month . '%" ';
        }

        $query6 = 'group by at1.uid ';
        $query6 .= ' ';

        $query3 = 'select 
            at1.*,
            Max(at1.timestamp) as lasttimestamp,
            Min(at1.timestamp) as firsttimestamp,
            employees.emp_id ,
            employees.id as emp_auto_id ,
            employees.emp_name_with_initial ,
            employees.emp_shift ,
            employees.emp_department,
            shift_types.onduty_time ,
            shift_types.offduty_time ,
            branches.location as b_location,
            departments.name as dept_name  
              ';

        $records = DB::select($query3 . $query2 . $query6);

        $data = array();

        foreach ($records as $record) {

            $normal_rate_otwork_hrs = 0;
            $double_rate_otwork_hrs = 0;

            $year = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->year;
            $month = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->month;
            $year_wildcard = $year . '-' . $month . '-%';

            //get attendances
            $att_query = 'SELECT at1.*, 
                Max(at1.timestamp) as lasttimestamp,
                Min(at1.timestamp) as firsttimestamp,
                employees.emp_shift,  
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                shift_types.onduty_time, 
                shift_types.offduty_time
                FROM `attendances`  as `at1`
                join `employees` on `employees`.`emp_id` = `at1`.`uid` 
                left join shift_types ON employees.emp_shift = shift_types.id 
                WHERE at1.emp_id = ' . $record->emp_id . ' AND date LIKE  "' . $year_wildcard . '"
                 group by at1.uid, at1.date
                ';
            $att_records = DB::select($att_query);

            foreach ($att_records as $att_record) {
                //calculate ot for last time stamp
                $double_ot_hours = 0;

                $off_time = Carbon::parse($att_record->lasttimestamp);
                $on_time = Carbon::parse($att_record->firsttimestamp);
                $record_date = Carbon::parse($att_record->date);

                $shift_start = $att_record->onduty_time;
                $shift_end = $att_record->offduty_time;

                $ot_ends_morning = Carbon::parse($record_date->year . '-' . $record_date->month . '-' . $record_date->day . ' ' . $shift_start);
                $ot_starts_evening = Carbon::parse($record_date->year . '-' . $record_date->month . '-' . $record_date->day . ' ' . $shift_end);

                $ot_in_minutes_morning = $on_time->diffInMinutes($ot_ends_morning);
                $ot_hours_morning = 0;
                if ($on_time < $ot_ends_morning) {
                    $ot_hours_morning = $ot_in_minutes_morning / 60;
                }

                $ot_in_minutes_evening = $off_time->diffInMinutes($ot_starts_evening);
                $ot_hours_evening = 0;
                if ($off_time > $ot_starts_evening) {
                    $ot_hours_evening = $ot_in_minutes_evening / 60;
                }

                $ot_hours = $ot_hours_morning + $ot_hours_evening;

                $holiday_check = Holiday::where('date', $record_date->year . '-' . $record_date->month . '-' . $record_date->day)
                    ->first();

                if (!empty($holiday_check)) {
                    if ($holiday_check->work_type == 2) {
                        $double_ot_hours = $ot_hours;
                        $ot_hours = 0;
                    }
                }

                //if driver or after sale department
                $department = Department::where('id', $record->emp_department)->first();
                if ($department->name == 'AFTER SALES SERVICE' || $department->name == 'DRIVERS') {

                    //actual ot start time = shift_end + 1 hour
                    $actual_ot_start_time = Carbon::parse($record_date->year . '-' . $record_date->month . '-' . $record_date->day . ' ' . $shift_end)->addHour();
                    if ($actual_ot_start_time <= $off_time) {
                        $normal_rate_otwork_hrs += $ot_hours;
                        $double_rate_otwork_hrs += $double_ot_hours;
                    }
                }
            }

            $year_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->year;
            $month_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->month;
            $rec_wildcard = $year_rec . '-' . $month_rec . '-%';

            $holidays = Holiday::where('date', 'like', $rec_wildcard . '%')->get();

            $filtered_holidays = array();
            foreach ($holidays as $hol) {
                $date = Carbon::parse($hol->date);
                if ($date->dayOfWeek != Carbon::SUNDAY) {
                    array_push($filtered_holidays, $date);
                }
            }

            //get work days
            $query = DB::table('attendances')
                ->select('uid', DB::raw('count(*) as total'));
            if ($month != '') {
                $query->where('date', 'like', $month . '%');
            }
            $query->where('uid', $record->uid)
                ->groupBy(DB::raw('Date(timestamp)'), DB::raw('uid'));
            $work_days = $query->get();


            $month_work_days = 26 - COUNT($filtered_holidays);

            //$leave_days = $month_work_days - COUNT($work_days);

            $query = DB::table('leaves')
                ->select(DB::raw('SUM(no_of_days) as total'))
                ->where('emp_id', $record->emp_id)
                ->where('leave_from', 'like', $month . '%');
            $leave_days_data = $query->get();

            $leave_days = (!empty($leave_days_data[0]->total)) ? $leave_days_data[0]->total : 0;

            $no_pay_days = Leave::where('leave_type', 3)->where('emp_id', $record->emp_id)->sum('no_of_days');

            DB::table('employee_work_rates')
                ->where('emp_id', $record->emp_auto_id)
                ->where('work_year', $year_rec)
                ->where('work_month', $month_rec)
                ->delete();

            $data = array(
                'emp_id' => $record->emp_auto_id,
                'emp_etfno' => $record->emp_id,
                'work_year' => $year_rec,
                'work_month' => $month_rec,
                'work_days' => COUNT($work_days),
                'leave_days' => $leave_days,
                'nopay_days' => $no_pay_days, //here
                'normal_rate_otwork_hrs' => $normal_rate_otwork_hrs,
                'double_rate_otwork_hrs' => $double_rate_otwork_hrs,
            );
            employeeWorkRate::create($data);
        }

        return response()->json(['status' => '1', 'msg' => 'Approved Successfully!']);
    }


    public function copy_att_to_employee_work_rates()
    {
        $attendances = DB::query()
            ->select('e.emp_id', 'e.emp_etfno', 'e.id', 'at1.timestamp')
            ->from('attendances as at1')
            ->join('employees as e', 'e.emp_id', 'at1.uid')
            ->whereBetween('at1.timestamp', ['2021-11-01', '2021-11-31'])
            ->groupBy('e.emp_id')
            ->get();

        $data = array();

        foreach ($attendances as $att) {
            $work_year = Carbon::createFromFormat('Y-m-d H:i:s', $att->timestamp)->year;
            $work_month = Carbon::createFromFormat('Y-m-d H:i:s', $att->timestamp)->month;

            $work_days = DB::table('attendances')
                ->select('uid', DB::raw('count(*) as total'))
                ->whereBetween('timestamp', ['2021-11-01', '2021-11-31'])
                ->where('uid', $att->emp_id)
                ->groupBy(DB::raw('Date(timestamp)'))
                ->get();

            $leave_days = 26 - COUNT($work_days);

            $data[] = array(
                'emp_id' => $att->id,
                'emp_etfno' => $att->emp_id,
                'work_year' => $work_year,
                'work_month' => $work_month,
                'work_days' => COUNT($work_days),
                'leave_days' => $leave_days,
                'nopay_days' => '',
                'normal_rate_otwork_hrs' => '',
                'double_rate_otwork_hrs' => '',
            );

        }

        DB::table('employee_work_rates')->insert($data);
    }

    public function late_attendance_by_time()
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.late_attendance_by_time');
    }

    public function attendance_by_time_report_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        ## Read value
        $department = $request->get('department');
        $company = $request->get('company');
        $location = $request->get('location');
        $date = $request->get('date');
        $late_type = $request->get('late_type');


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

        //late_type
        $late_times = DB::table('late_types')->where('id', $late_type)->first();
        // Total records
        $totalRecords_array = DB::select('
            SELECT COUNT(*) as acount
                FROM
                (
                    SELECT COUNT(*)
                    FROM attendances AS at1 
                    left join `employees` on `at1`.`uid` = `employees`.`emp_id`  
                    left join `branches` on `at1`.`location` = `branches`.`id`  
                    WHERE NOT EXISTS (
                        SELECT * FROM employee_late_attendances AS ela
                        WHERE ela.emp_id = at1.uid
                    )
                    AND at1.deleted_at IS NULL
                    group by `at1`.`uid`, `at1`.`date`  
                )t
            ');


        $totalRecords = $totalRecords_array[0]->acount;

        $query1 = 'SELECT COUNT(*) as acount ';
        $query1 .= 'FROM ( ';
        $query1 .= 'SELECT COUNT(*) ';
        $query2 = 'FROM `attendances` as `at1` ';
        $query2 .= 'join `employees` on `employees`.`emp_id` = `at1`.`uid` ';
        $query2 .= 'left join `branches` on `at1`.`location` = `branches`.`id` ';
        $query2 .= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
        $query2 .= 'left join `companies` on `companies`.`id` = `departments`.`company_id` ';


        $query2 .= 'WHERE 1 = 1 AND `at1`.`deleted_at` IS NULL ';
        //$searchValue = 'Breeder Farm';
        if ($searchValue != '') {
            $query2 .= 'AND ';
            $query2 .= '( ';
            $query2 .= 'employees.emp_id like "' . $searchValue . '%" ';
            $query2 .= 'OR employees.emp_name_with_initial like "' . $searchValue . '%" ';
            $query2 .= 'OR at1.timestamp like "' . $searchValue . '%" ';
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

        if ($location != '') {
            $query2 .= 'AND at1.location = "' . $location . '" ';
        }

        if ($date != '') {
            $query2 .= 'AND at1.date LIKE "' . $date . ' %" ';
        }

        $query_t = '';

        if ($late_type != '') {
            $query_t .= 'AND at1.timestamp > "' . ($date . ' ' . $late_times->time_from) . '" ';

        }

        $query6 = 'group by `at1`.`uid`, `at1`.`date` ';
        $query6 .= ' ';
        $query4 = ') t ';
        $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
        $query7 = 'ORDER BY ' . $columnName . ' ' . $columnSortOrder . ' ';

        $totalRecordswithFilter_arr = DB::select($query1 . $query2 . $query_t . $query6 . $query4);

        $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

        // Fetch records
        $query3 = 'select  
            at1.*,
            Min(at1.timestamp) as first_checkin,
            Max(at1.timestamp) as lasttimestamp,
            employees.emp_id ,
            employees.emp_name_with_initial ,
            branches.location as b_location,
            branches.id as b_location_id,
            departments.name as dept_name,  
            departments.id as dept_id  
              ';

        $records = DB::select($query3 . $query2 . $query6 . $query7 . $query5);

        $data_arr = array();

        foreach ($records as $record) {

            $to = \Carbon\Carbon::parse($record->lasttimestamp);
            $from = \Carbon\Carbon::parse($record->timestamp);

            $workhours = gmdate("H:i:s", $to->diffInSeconds($from));
            $rec_date = Carbon::parse($record->date)->toDateString();

            $first_time_stamp = date('H:i', strtotime($record->first_checkin));
            $last_time_stamp = date('H:i', strtotime($record->lasttimestamp));

            if ($first_time_stamp == $last_time_stamp) {
                $last_time_stamp = '';
            }

            $is_late_marked = DB::table('employee_late_attendances')
            ->where('emp_id', $record->emp_id)
            ->where('date', $rec_date)
            ->exists() ? 1 : 0;


            if ($late_type != '') {
                if ($record->timestamp > $date . ' ' . $late_times->time_from) {
                    $data_arr[] = array(
                        "id" => $record->id,
                        "uid" => $record->uid,
                        "emp_name_with_initial" => $record->emp_name_with_initial,
                        "date" => $rec_date,
                        "timestamp" => $first_time_stamp,
                        "lasttimestamp" => $last_time_stamp,
                        "workhours" => $workhours,
                        "dept_name" => $record->dept_name,
                        "dept_id" => $record->dept_id,
                        "location" => $record->b_location,
                        "location_id" => $record->b_location_id,
                        "is_late_marked" => $is_late_marked 
                    );
                }
            } else {
                $data_arr[] = array(
                    "id" => $record->id,
                    "uid" => $record->uid,
                    "emp_name_with_initial" => $record->emp_name_with_initial,
                    "date" => $rec_date,
                    "timestamp" => $first_time_stamp,
                    "lasttimestamp" => $last_time_stamp,
                    "workhours" => $workhours,
                    "dept_name" => $record->dept_name,
                    "dept_id" => $record->dept_id,
                    "location" => $record->b_location,
                    "location_id" => $record->b_location_id,
                    "is_late_marked" => $is_late_marked 
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

            $data_arr[] = array(
                "id" => $record->id,
                "emp_name_with_initial" => $record->emp_name_with_initial,
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
                            ->join('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
                            ->select('job_categories.late_attend_min')
                            ->where('employees.emp_id', $emp_data->emp_id) 
                            ->first();

            if(!empty($jobcategory->late_attend_min)){

                $minitescount = $jobcategory->late_attend_min; 

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
                
            }else{

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

        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);

    }

    public function attendance_add_bulk_submit(Request $request)
    {

        $rules = array(
            'employee' => 'required',
            'month' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $emp_id = $request->employee;
        $month = $request->month;

        $in_time = $request->in_time;
        $out_time = $request->out_time;
        $date = $request->date;

        $employees = DB::table('employees')
            ->join('branches', 'employees.emp_location', '=', 'branches.id')
            ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
            ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
            ->groupBy('fingerprint_devices.location')
            ->where('employees.emp_id', $request->employee)
            ->get();

        for ($i = 0; $i < count($date); $i++) {

            $in_timestamp = $month . '-' . $date[$i] . ' ' . $in_time[$i];
            $out_timestamp = $month . '-' . $date[$i] . ' ' . $out_time[$i];

            if ($in_time[$i] != '') {
                $data = array(
                    'emp_id' => $emp_id,
                    'uid' => $request->employee,
                    'state' => 1,
                    'timestamp' => $in_timestamp,
                    'date' => substr($in_timestamp, 0, -6),
                    'approved' => 0,
                    'type' => 255,
                    'devicesno' => $employees[0]->sno,
                    'location' => $employees[0]->location
                );
                $id = DB::table('attendances')->insert($data);
            }

            if ($out_time[$i] != '') {
                $data = array(
                    'emp_id' => $emp_id,
                    'uid' => $request->employee,
                    'state' => 1,
                    'timestamp' => $out_timestamp,
                    'date' => substr($out_timestamp, 0, -6),
                    'approved' => 0,
                    'type' => 255,
                    'devicesno' => $employees[0]->sno,
                    'location' => $employees[0]->location
                );
                $id = DB::table('attendances')->insert($data);
            }

        }

        return response()->json(['msg' => 'Attendances Inserted successfully.', 'status' => true]);

    }

    public function attendance_add_dept_wise_submit(Request $request)
    {

        $rules = array(
            'department' => 'required',
            'date' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $date = $request->date;

        $emp_id = $request->emp_id;
        $in_date = $request->in_date;
        $in_time = $request->in_time;
        $out_date = $request->out_date;
        $out_time = $request->out_time;

        for ($i = 0; $i < count($emp_id); $i++) {

            $employees = DB::table('employees')
                ->join('branches', 'employees.emp_location', '=', 'branches.id')
                ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
                ->groupBy('fingerprint_devices.location')
                ->where('employees.emp_id', $emp_id[$i])
                ->get();

            if ($in_time[$i] != '') {
                $in_timestamp = $in_date[$i] . ' ' . $in_time[$i];
                $data = array(
                    'emp_id' => $emp_id[$i],
                    'uid' => $emp_id[$i],
                    'state' => 1,
                    'timestamp' => $in_timestamp,
                    'date' => $date,
                    'approved' => 0,
                    'type' => 255,
                    'devicesno' => $employees[0]->sno,
                    'location' => $employees[0]->location
                );
                $id = DB::table('attendances')->insert($data);
            }

            if ($out_time[$i] != '') {

                $out_timestamp = $out_date[$i] . ' ' . $out_time[$i];

                $data = array(
                    'emp_id' => $emp_id[$i],
                    'uid' => $emp_id[$i],
                    'state' => 1,
                    'timestamp' => $out_timestamp,
                    'date' => $date,
                    'approved' => 0,
                    'type' => 255,
                    'devicesno' => $employees[0]->sno,
                    'location' => $employees[0]->location
                );
                $id = DB::table('attendances')->insert($data);
            }

        }

        return response()->json(['msg' => 'Attendances Inserted successfully.', 'status' => true]);

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

    /**
     * @throws \Exception
     */
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

    public function get_attendance_monthly_summery_by_emp_id()
    {

        $emp_id = request('emp_id');
        $month = request('month');

        $employee = DB::table('employees')
            ->where('emp_id', $emp_id)
            ->select('emp_id', 'emp_name_with_initial', 'emp_etfno')->get();

        $working_week_days_arr = (new \App\Attendance)->get_working_week_days($emp_id, $month);
        $work_days = $working_week_days_arr['no_of_working_workdays'];
        $working_work_days_breakdown = $working_week_days_arr['working_work_days_breakdown'];
        $leave_deductions = $working_week_days_arr['leave_deductions'];

        $working_week_days_confirmed = (new \App\Attendance)->get_working_week_days_confirmed($emp_id, $month);

        $leave_days = (new \App\Attendance)->get_leave_days($emp_id, $month);

        $no_pay_days = (new \App\Attendance)->get_no_pay_days($emp_id, $month);

        $normal_ot_hours = (new \App\OtApproved)->get_ot_hours_monthly($emp_id, $month);

        $double_ot_hours = (new \App\OtApproved)->get_double_ot_hours_monthly($emp_id, $month);

        $attendances = (new \App\Attendance)->get_attendance_details($emp_id, $month);

        $data = array(
            'employee' => $employee,
            'work_days' => $work_days,
            'working_work_days_breakdown' => $working_work_days_breakdown,
            'leave_deductions' => $leave_deductions,
            'leave_days' => $leave_days,
            'no_pay_days' => $no_pay_days,
            'normal_ot_hours' => $normal_ot_hours,
            'double_ot_hours' => $double_ot_hours,
            'attendances' => $attendances,
            'working_week_days_confirmed' => $working_week_days_confirmed['no_of_days']
        );

        return response()->json($data);


    }

    public function incomplete_attendances()
    {
        $user = Auth::user();
        $permission = $user->can('incomplete-attendance-list');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.incomplete_attendances');
    }

    //get_incomplete_attendance_by_employee_data
    public function get_incomplete_attendance_by_employee_data(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('incomplete-attendance-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

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
        }

        $departments = DB::select($dept_sql);

        $data_arr = array();
        $not_att_count = 0;

        foreach ($departments as $department_) {

            $query3 = 'select   
            employees.emp_id ,
            employees.emp_name_with_initial ,
            employees.emp_etfno,
            branches.location as b_location,
            departments.name as dept_name,
            departments.id as dept_id    
              ';

            $query3 .= 'from employees ';
            $query3 .= 'left join `branches` on `employees`.`emp_location` = `branches`.`id` ';
            $query3 .= 'left join `departments` on `employees`.`emp_department` = `departments`.`id` ';
            $query3 .= 'where 1 = 1 AND employees.deleted = 0 ';

            $query3 .= 'AND departments.id = "' . $department_->id . '" ';

            if ($employee != '') {
                $query3 .= 'AND employees.emp_id = "' . $employee . '" ';
            }

            $query3 .= 'order by employees.emp_id asc ';

            $employees = DB::select($query3);

            foreach ($employees as $record) {

                //dates of the month between from and to date
                $period = CarbonPeriod::create($from_date, $to_date);

                foreach ($period as $date) {
                    $f_date = $date->format('Y-m-d');

                    //check this is not a holiday
                    $holiday_check = Holiday::where('date', $f_date)->first();

                    if (empty($holiday_check)) {

                        //check leaves from_date to date and emp_id is not a leave
                        $leave_check = Leave::where('emp_id', $record->emp_id)
                            ->where('leave_from', '<=', $f_date)
                            ->where('leave_to', '>=', $f_date)->first();

                        if (empty($leave_check)) {

                            $sql = " SELECT *,
                                Min(attendances.timestamp) as first_checkin,
                                Max(attendances.timestamp) as lasttimestamp
                                FROM attendances WHERE uid = '" . $record->emp_id . "' AND deleted_at IS NULL ";

                            $sql .= 'AND date LIKE "' . $f_date . '%" ';

                            $sql .= 'GROUP BY uid, date ';
                            $sql .= 'ORDER BY date DESC ';

                            $attendances = DB::select($sql);

                            if (empty($attendances)) {
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['emp_id'] = $record->emp_id;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['emp_name_with_initial'] = $record->emp_name_with_initial;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['etf_no'] = $record->emp_etfno;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['b_location'] = $record->b_location;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['dept_name'] = $record->dept_name;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['dept_id'] = $record->dept_id;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['date'] = $f_date;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['timestamp'] = '-';
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['lasttimestamp'] = '-';
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['workhours'] = '-';
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['location'] = $record->b_location;

                                $not_att_count++;
                            }

                        }// leave check if

                    }//holiday if end

                }// period loop

            }//employees loop


        }//departments loop

        $department_id = 0;

        $html = '<div class="row mb-1"> 
                    <div class="col-md-4"> 
                    </div>
                    
                    <div class="col-md-4"> 
                    </div>
                     
                </div>';
        $html .= '<table class="table table-sm table-hover" id="attendance_report_table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th> </th>';
        $html .= '<th>ETF No</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Department</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>Check In Time</th>';
        $html .= '<th>Check Out Time</th>';
        $html .= '<th>Work Hours</th>';
        $html .= '<th>Location</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($data_arr as $dept_key => $department_data) {

            //if department_id is not equal to the previous department_id
            if ($department_id != $dept_key) {
                $department_id = $dept_key;
                $department_name = Department::query()->where('id', $department_id)->first()->name;
                $html .= '<tr>';
                $html .= '<td colspan="8" style="background-color: #f5f5f5;"> <strong> ' . $department_name . '</strong> </td>';
                $html .= '</tr>';
            }

            foreach ($department_data as $emp_data) {

                foreach ($emp_data as $attendance) {

                    $tr = '<tr>';

                    $html .= $tr;
                    $html .= '<td> 
                                <input type="checkbox" class="checkbox_attendance" name="checkbox[]" value="' . $attendance['etf_no'] . '"
                                    data-etf_no="' . $attendance['etf_no'] . '" 
                                    data-date = "' . $attendance['date'] . '" 
                                 />
                                </td>';

                    $first_time = date('H:i', strtotime($attendance['timestamp']));
                    $last_time = date('H:i', strtotime($attendance['lasttimestamp']));

                    $html .= '<td>' . $attendance['etf_no'] . '</td>';
                    $html .= '<td>' . $attendance['emp_name_with_initial'] . '</td>';
                    $html .= '<td>' . $attendance['dept_name'] . '</td>';
                    $html .= '<td>' . $attendance['date'] . '</td>';
                    $html .= '<td>' . $first_time . '</td>';
                    $html .= '<td>' . $last_time . '</td>';
                    $html .= '<td>' . $attendance['workhours'] . '</td>';
                    $html .= '<td>' . $attendance['location'] . '</td>';
                    $html .= '</tr>';
                    $department_id = $attendance['dept_id'];

                }

            }

        }

        $html .= '</tbody>';
        $html .= '</table>

                <div class="row mt-3"> 
                    <div class="col-md-12"> 
                        <button type="button" class="btn btn-primary btn-sm float-right" id="btn_mark_as_no_pay">Mark as NO Pay Leave</button>
                    </div>  
                </div>';


        //return json response
        echo $html;

    }

    public function mark_as_no_pay(Request $request)
    {

        $checked = $request->checked;

        foreach ($checked as $ch) {

            $data = array(
                'emp_id' => $ch['etf_no'],
                'leave_type' => '3',
                'leave_from' => $ch['date'],
                'leave_to' => $ch['date'],
                'no_of_days' => '1',
                'half_short' => '0',
                'reson' => 'No Pay Leave',
                'comment' => 'No Pay Leave',
                'emp_covering' => '0',
                'leave_approv_person' => Auth::user()->id,
                'status' => 'Approved',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            Leave::query()->insert($data);

        }

        return response()->json(['success' => 'Leaves marked as No Pay Leave']);

    }

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
                WHERE 1 = 1";

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
            $is_approved = (new \App\OtApproved)->is_exists_in_ot_approved($emp_id, $date);
            //if ot_breakdown is a key in the array
            if (array_key_exists('ot_breakdown', $ot_hours)) {

                $ot_breakdown = array('ot_breakdown'=> $ot_hours['ot_breakdown'], 'is_approved' => $is_approved);

                $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
                $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

                //push ot_breakdown to ot_data
                if (!empty($ot_breakdown)) {
                    array_push($ot_data, $ot_breakdown);
                }

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
                employees.emp_department, 
                branches.location as b_location,
                departments.name as dept_name 
                FROM ot_approved
                left join `employees` on `employees`.`emp_id` = ot_approved.emp_id
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
                return date('Y-m-d H:i', strtotime($row->from));
            })
            //to
            ->addColumn('to', function ($row) {
                return date('Y-m-d H:i', strtotime($row->to));
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

    //delete
    public function delete(Request $request)
    {
        $permission = Auth::user()->can('attendance-delete');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $uid = $request->get('uid');
        $date_time = $request->get('date');
        $date = date('Y-m-d', strtotime($date_time));

        //delete attendance
        $status = Attendance::query()->where('uid', $uid)->whereDate('date', $date)->delete();

        return response()->json([
            'success' => true,
            'status' => $status,
            'msg' => 'Deleted']);

    }

    public function attendance_clear_list_dt(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-');
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

    public function attendance_upload_txt_submit(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $date_input = $request->date;
        $machine = $request->machine;
        $txt_file_u = $request->txt_file_u;

        $content = fopen($txt_file_u,'r');

        $data = array();

      
        while(!feof($content)){
            $line = fgets($content);
            $data[] = $line;
        }

        fclose($content);

        $unique_arr = array_unique($data);

        foreach ($unique_arr as $line){

            if($machine==1){
                    // Split the line into parts using whitespace or tabs
                    $parts = preg_split('/\s+/', trim($line));

                    if (count($parts) < 2) {
                        continue; // Skip lines that don't have enough parts
                    }

                    $emp_id = trim($parts[0]);
                    $date = trim($parts[1]);
                    $time = trim($parts[2]);

                    list($time_h, $time_m, $time_s) = explode(':', $time);

                    $formatted_date_in = Carbon::parse($date_input)->format('dmY');

                    $full_emp_id = $emp_id;
            }
           

            $emp = DB::table('employees')
            ->select('emp_id', 'emp_shift')
            ->where('emp_id', $full_emp_id)
            ->first();

            if (is_null($emp)) {
                continue;
            }

            $shift = DB::table('shift_types')
                ->where('id', $emp->emp_shift)
                ->first();

            $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');
            $employeeshiftdetails = DB::table('employeeshiftdetails')
                ->where('date_from', $previousDate)
                ->where('emp_id', $full_emp_id)
                ->first();

                $period = (new DateTime($time))->format('A');
                $timestamp = null;
                $attendance_date = null;


                if ($shift && $shift->id == '1' && $date == $date_input) {
                    $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');
                    $timestamp = $date_input . ' ' . $time_h . ':' . $time_m . ':00';
                    $attendance_date = ($period === 'AM') ? $previous_day : substr($timestamp, 0, 10);
                } else if ($date == $date_input) {
                    if($employeeshiftdetails){
                        $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');
                        $timestamp = $date_input . ' ' . $time_h . ':' . $time_m . ':00';
                        $attendance_date = ($period === 'AM') ? $previous_day : substr($timestamp, 0, 10);
                    }else{
                        $timestamp = $date_input . ' ' . $time_h . ':' . $time_m . ':00';
                        $attendance_date = substr($timestamp, 0, 10);
                    }  
                }

                if($date == $date_input){
                    $Attendance = Attendance::firstOrNew(['timestamp' => $timestamp, 'emp_id' => $full_emp_id]);
                    $Attendance->uid = $full_emp_id;
                    $Attendance->emp_id = $full_emp_id;
                    $Attendance->timestamp = $timestamp;
                    $Attendance->date = $attendance_date;
                    $Attendance->location = 1;
                    $is_ok = $Attendance->save();
                }           

        }
        
        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);

    }


}
