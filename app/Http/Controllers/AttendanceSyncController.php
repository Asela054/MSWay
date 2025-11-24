<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\AttendanceClear;
use App\FingerprintUser;
use App\FingerprintDevice;
use App\Helpers\EmployeeHelper;
use App\Services\AttendancePolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ZKLib;
use Carbon\Carbon;
use DateTime;
class AttendanceSyncController extends Controller
{
    protected $attendancePolicyService;

    public function __construct(AttendancePolicyService $attendancePolicyService)
    {
        $this->middleware('auth');

        $this->attendancePolicyService = $attendancePolicyService;
    }

    // load datatable in the attendance sync view

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
                `employees`.`emp_name_with_initial`,`employees`.`calling_name`, `branches`.`location` as b_location, departments.name as dep_name ';

        $records = DB::select($query3 . $query2 . $query6 . $query7 . $query5);

        $data_arr = array();
        foreach ($records as $record) {

              $employeeObj = (object)[
                'emp_id' => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'calling_name' => $record->calling_name
            ];
            //get only the date from date
            $date = date('Y-m-d', strtotime($record->date));

            $first_timestamp = date('H:i', strtotime($record->firsttimestamp));
            $last_timestamp = date('H:i', strtotime($record->lasttimestamp));

            $data_arr_i = array(
                "at_id" => $record->at_id,
                "uid" => $record->uid,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "employee_display" => EmployeeHelper::getDisplayName($employeeObj),
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

    // get attendance from fingerprint
    public function getdevicedata(Request $request)
    {
         $user = Auth::user();
        $permission = $user->can('attendance-sync');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        ini_set('max_execution_time', 3000);
        //dd($request->device);
        $device = FingerprintDevice::where('ip', '=', $request->device)->get();
        $device = DB::table('fingerprint_devices')->where('ip', '=', $request->device)->first();
       
        $ip = $device->ip;
        $sync_date = $request->sync_date;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $companytype = $request->companytype;
        
        $date_input = $sync_date;
        $name = $device->name;

        
        $name = new ZKLib(
            $ip // '112.135.69.27' //your device IP
        );
        $ret = $name->connect();
        if ($ret) {
            $name->disableDevice();

            $attendance = $name->getAttendance();
            $location = $device->location;
            $serial = $name->serialNumber();
            $deviceserial = substr($serial, strpos($serial, "=") + 1, -1);


            if ($companytype == 1){
                   // Filter for date range (from date to to date)
                    $attendance = array_filter($attendance, function ($item) use ($date_from, $date_to) {
                        $itemDate = date('Y-m-d', strtotime($item['timestamp']));
                        return $itemDate >= $date_from && $itemDate <= $date_to; });

            }else{
                 //keep timestamp like sync_date% and remove the rest (applied only for jaya farm)
                $attendance = array_filter($attendance, function ($item) use ($sync_date) {
                                return strpos($item['timestamp'], $sync_date) !== false; });
            }
            
            $is_ok = true; 


            foreach ($attendance as $link) {
                $newtimestamp = $link['timestamp'];
                $full_emp_id = $link['id'];
                $date = Carbon::parse($link['timestamp'])->format('Y-m-d');
                $time = Carbon::parse($link['timestamp'])->format('H:i:s');

                if($companytype == 0){
                         $this->attendancePolicyService->attendanceInsertcsv_txt( $full_emp_id,  $date_input, $time, $date );
                }else{

                         // Create date objects for from and to dates
                          $start_date = \Carbon\Carbon::parse($date_from);
                          $end_date = \Carbon\Carbon::parse($date_to);
                    
                        // Loop through each date between date_from and date_to
                        for ($current_date = $start_date; $current_date->lte($end_date); $current_date->addDay()) 
                            {
                                $current_date_string = $current_date->format('Y-m-d');
                                $this->attendancePolicyService->attendanceInsertcsv_txt($full_emp_id, $current_date_string, $time, $date);
                            }
                }
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
         $user = Auth::user();
        $permission = $user->can('attendance-sync');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
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

}
