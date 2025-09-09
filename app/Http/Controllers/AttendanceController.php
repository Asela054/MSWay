<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\AttendanceEdited;
use App\Helpers\EmployeeHelper;
use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
                    'employees.calling_name',
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
                'employees.calling_name',
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

        if ($department != '' && $department != 'All') {
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

    /**
     * @throws \Exception
     */
 
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


                if ($shift && $shift->off_next_day == '1' && $date == $date_input) {
                    $previous_day = (new DateTime($date_input))->modify('-1 day')->format('Y-m-d');

                    $hasRecord = DB::table('attendances')
                        ->whereDate('date', $previous_day)
                        ->where('emp_id', $full_emp_id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if($hasRecord){
                        $timestamp = $date_input . ' ' . $time_h . ':' . $time_m . ':00';
                        $attendance_date = ($period === 'AM') ? $previous_day : substr($timestamp, 0, 10);
                    }
                    else{
                        $timestamp = $date_input . ' ' . $time_h . ':' . $time_m . ':00';
                        $attendance_date = substr($timestamp, 0, 10);
                    }
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
