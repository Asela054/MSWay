<?php

namespace App\Http\Controllers\Api;

use App\Customerrequest;
use App\Empattendances;
use App\empattendances_duplicate;
use App\Employee;
use App\Leave;
use App\LeaveDetail;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AttendanceAPIController extends Controller{
    public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }



        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    public function GetShiftType(Request $request)
    {
        $q = "
        SELECT * FROM `shift_types` 
        ";

        $data = DB::select($q);

        $data = array(
            'shift_types' => $data
        );

        return (new BaseController)->sendResponse($data, 'shift_types');
    }

    public function GetJobTitles(Request $request)
    {
        $q = "
        SELECT job_titles.*
        FROM job_titles
        WHERE job_titles.id IN (3, 7, 9, 23, 26, 27, 28, 29, 30, 31)         
        ";

        $data = DB::select($q);

        $data = array(
            'job_titles' => $data
        );

        return (new BaseController)->sendResponse($data, 'job_titles');
    }

    public function GetBranchList(Request $request)
    {
        $userid = $request->input('empid');
        $q = "
        SELECT branches.* 
        FROM customerbranches AS branches LEFT JOIN sub_regionalmanagers AS srm ON branches.`subregion_id` = srm.subregion_id
        WHERE branches.approve_status = '1'
        AND branches.status = '1'
        AND srm.emp_id = '$userid' 
        AND srm.assign_status = '1'        
        ";

        $data = DB::select($q);

        $data = array(
            'branches' => $data
        );

        return (new BaseController)->sendResponse($data, 'branches');
    }

    public function CustomerrequestList(Request $request){


        $customerbranch_id = $request->input('branch');
        $date = $request->input('todate');
        $shift_id = $request->input('shift');


        // checking there is a record in customer request to given date shift and customer branch

        $matchingRecord = Customerrequest::where('customerbranch_id', $customerbranch_id)
        ->where('fromdate', '<=', $date) 
        ->where('todate', '>=', $date)   
        ->where('status', 1)
        ->where('approve_status', 1)
        ->latest() 
        ->first();

           $holidaytype ='';
           $recordtype='';

        if ($matchingRecord) {


            $data = DB::table('customerrequests')
            ->leftjoin('customerbranches', 'customerrequests.customerbranch_id', '=', 'customerbranches.id')
            ->select('customerrequests.*', 
               'customerbranches.subregion_id AS subregion')
               ->where('customerbranch_id', $customerbranch_id)
               ->where('fromdate', '<=', $date) 
               ->where('todate', '>=', $date)   
               ->where('customerrequests.status', 1)
               ->where('customerrequests.approve_status', 1)
               ->get(); 
               $subregionid = $data[0]->subregion;
               $recordtype = $data[0]->requeststatus;

               if ($recordtype === 'Normal') {

                $requestid = $matchingRecord->id;
                $Holidaylist = DB::table('holidays')
                    ->select('holidays.*')
                    ->where('date', $date)
                    ->first();
            
                if ($Holidaylist) {

                    $holidaytype = $Holidaylist->holiday_type;
                    $holidaytypelist = DB::table('holiday_types')
                        ->where('id', $holidaytype)
                        ->get();
                    $holidayname = $holidaytypelist[0]->name;
                } else {
                    $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            
                    if ($carbonDate->isWeekend()) {
                        $weekendType = $carbonDate->format('N');
            
                        if ($weekendType == 6) {
                            $holidaytype = 4;
                        } elseif ($weekendType == 7) {
                            $holidaytype = 5;
                        }
                    } else {
                        $holidaytype = 3;
                    }
                }
            } else {
                
                $holidaytype = 6;
            }
        } else {
            
            return response()->json(['error' => 'No matching record found']);
        }


     
        $detaillist = $this->allocationdetailslist($requestid, $shift_id,$holidaytype, $date); 

        $responseData = array(
            'maindata' => $requestid,
            'subregion' => $subregionid,
            'holidaytype' => $holidaytype,
            'detailslist' => $detaillist
        );


        return (new BaseController)->sendResponse($responseData, 'maindata','subregion','holidaytype','detailslist');



}
private function allocationdetailslist($requestid, $shiftid, $holidattype,$reqdate){

    $recordID =$requestid ;
    $shift =$shiftid ;
    $datetype =$holidattype ;
    $allocationDate = $reqdate;

    $data = DB::table('customerrequestdetails')
    ->leftjoin('job_titles', 'customerrequestdetails.job_title_id', '=', 'job_titles.id')
    ->leftjoin('shift_types', 'customerrequestdetails.shift_id', '=', 'shift_types.id')
    ->select('customerrequestdetails.*', 
       'job_titles.title AS jobtitle',
       'shift_types.onduty_time AS ontime',
       'shift_types.offduty_time AS offtime',
       'shift_types.saturday_onduty_time AS saturdayonduty',
       'shift_types.saturday_offduty_time AS saturdayoffduty',
       DB::raw('(SELECT SUM(count) FROM customerrequestdetails 
       WHERE customerrequestdetails.customerrequest_id = ' . $recordID . ' 
       AND customerrequestdetails.shift_id = ' . $shift . ' 
       AND customerrequestdetails.holiday_id = ' . $datetype . ' 
       AND customerrequestdetails.status = 1) AS totalreqcount')
       )
    ->where('customerrequestdetails.customerrequest_id', $recordID)
    ->where('customerrequestdetails.shift_id', $shift)
    ->where('customerrequestdetails.holiday_id', $datetype)
    ->where('customerrequestdetails.status', 1)
    ->get(); 


    $totalCount = $data[0]->totalreqcount;
   
        $shifyontime = $data[0]->ontime; 
        $shifyofftime = $data[0]->offtime; 

    $combinedontime = Carbon::parse("$allocationDate $shifyontime");
    $ontime = $combinedontime->format('Y-m-d H:i:s');

 
    $combinedofftime = Carbon::parse("$allocationDate $shifyofftime");
    $offtime = $combinedofftime->format('Y-m-d H:i:s');


    $dataArray = [];
    foreach ($data as $row) {
        $dataArray[] = [
            'jobtitle' => $row->jobtitle,
            'count' => $row->count,
        ];
    }
    
    return [
        'jobtable' => $dataArray,
        'ontime' => $ontime,
        'offtime' => $offtime,
        'totalCount' => $totalCount,
    ];
}

        public function insert(Request $request){

                $requestid = $request->input('requestid');
                $customer_id = $request->input('customer');
                $subcustomer_id = $request->input('subcustomer');
                $customerbranch_id = $request->input('branch');
                $date = $request->input('date');
                $holiday_id = $request->input('holidaytype');
                $shift_id = $request->input('shiftid');
                $tableData = json_decode($request->input('tableData'));
                $userID = $request->input('userID');


                foreach ($tableData as $rowtabledata) {

                    $empservice = $rowtabledata->col_2;
                    $title = $rowtabledata->col_3;
                    $empage= $rowtabledata->col_4;
                    $empontime = $rowtabledata->col_5;
                    $empofftime = $rowtabledata->col_6;
                    $empID = $rowtabledata->col_8;
                
                
                    $attendances = new Empattendances();
                    $attendances->request_id = $requestid;
                    $attendances->date = $date;
                    $attendances->customer_id = $customer_id;
                    $attendances->subcustomer_id = $subcustomer_id;
                    $attendances->customerbranch_id = $customerbranch_id;
                    $attendances->holiday_id =  $holiday_id;
                    $attendances->shift_id = $shift_id;
                    $attendances->emp_id = $empID;
                    $attendances->jobtitle_id = $title;
                    $attendances->emp_serviceno = $empservice;
                    $attendances->emp_age = $empage;
                    $attendances->ontime = $empontime;
                    $attendances->outtime = $empofftime;
                    $attendances->attendance_status = '1';
                    $attendances->status = '1';
                    $attendances->approve_status = '0';
                    $attendances->approve_01 = '0';
                    $attendances->approve_02 = '0';
                    $attendances->approve_03 = '0';
                    $attendances->delete_status = '0';
                    $attendances->create_by = $userID;
                    $attendances->update_by = '0';

                    $attendances->save();
                }

                return (new BaseController)->sendResponse($attendances, 'Employee Attendance Added');
        }
    

        public function edit(Request $request){
                $id = $request->input('id');

               $query = "
                 SELECT empattendances.*, 
                shift_types.shift_name AS Shift, 
                customerbranches.branch_name AS branchname, 
                customerbranches.subregion_id AS subregion, 
                employees.emp_fullname AS empfullname
            FROM empattendances
            LEFT JOIN customerbranches ON empattendances.customerbranch_id = customerbranches.id
            LEFT JOIN shift_types ON empattendances.shift_id = shift_types.id
            LEFT JOIN employees ON empattendances.emp_id = employees.id
            WHERE empattendances.id = ' $id'
            AND empattendances.status = 1
            ";

            $data = DB::select($query);

            $data = array(
                'empattendances' => $data
            );
            return (new BaseController)->sendResponse($data, 'empattendances');

        }

        public function update(Request $request){
                $tableData = $request->input('tableData');
                $recordID = $request->input('recordID');
                $userID = $request->input('userID');
                foreach ($tableData as $rowtabledata) {
    
                    $empID = $rowtabledata['col_1'];
                    $empservice = $rowtabledata['col_2'];
                    $title = $rowtabledata['col_3'];
                    $empage= $rowtabledata['col_4'];
                    $empontime = $rowtabledata['col_5'];
                    $empofftime = $rowtabledata['col_6'];
        
                    $current_date_time = Carbon::now()->toDateTimeString();
        
    
                            $originalAttendance = Empattendances::find($recordID);
        
                            $attendances = Empattendances::where('id', $recordID)->first();
                            $attendances->emp_id = $empID;
                            $attendances->jobtitle_id = $title;
                            $attendances->emp_serviceno = $empservice;
                            $attendances->emp_age = $empage;
                            $attendances->ontime = $empontime;
                            $attendances->outtime = $empofftime;
                            $attendances->update_by = $userID;
                            $attendances->updated_at = $current_date_time;
                            $attendances->approve_status = '0';
                            $attendances->approve_01 = '0';
                            $attendances->approve_02 = '0';
                            $attendances->approve_03 = '0';
                            $attendances->save();
    
                            // add old record to the duplicate table
                            $originalAttributes = $originalAttendance->toArray();
                            $originalAttributes['attendance_id'] = $recordID;
                            empattendances_duplicate::create($originalAttributes);
    
                }
                return (new BaseController)->sendResponse($attendances, 'Employee Attendance Updated');
        
        } 

        public function employeeselect(Request $request){

         
                $employee = $request->input('employeeID');
               
                $query = "
                SELECT employees.*
                 FROM employees
                 WHERE employees.id = '$employee'
                 AND employees.deleted = 0
                 AND employees.emp_category = 2;
                  ";
               
                  $data = DB::select($query);
               
                  $data = array(
                    'employees' => $data
                );
                return (new BaseController)->sendResponse($data, 'employees');
        }

        public function getlastshift(Request $request){
            $customerbranch_id = $request->input('branch');
            $date = $request->input('todate');
            $shift_id = $request->input('shift');
            

                $yesterdate = Carbon::parse($date);
                $yesterdate->subDay();

                $data = DB::table('empattendances')
                ->leftjoin('job_titles', 'empattendances.jobtitle_id', '=', 'job_titles.id')
                ->leftjoin('employees','empattendances.emp_id','=','employees.id')
                ->leftjoin('shift_types', 'empattendances.shift_id', '=', 'shift_types.id')
                ->select('empattendances.*','job_titles.title','employees.emp_fullname','shift_types.onduty_time AS ontime','shift_types.offduty_time AS offtime')
                ->where('empattendances.date', '=', $yesterdate)
                ->where('empattendances.customerbranch_id', '=', $customerbranch_id)
                ->where('empattendances.shift_id', '=', $shift_id)
                ->where('employees.deleted', '=', '0')
                ->where('employees.emp_category', '=', '2')
                ->get();

                $ontime = Carbon::parse($data[0]->ontime); 
                $todayTime = $ontime->addDay();

                $offtime = Carbon::parse($data[0]->offtime);
                $todayoffTime = $offtime->addDay();


            $responseData = array(
                'attendance' => $data,
                'todayTime' => $todayTime,
                'todayoffTime' => $todayoffTime
            );
    
            return (new BaseController)->sendResponse($responseData, 'attendance','todayTime','todayoffTime');

        }

        public function vsoemployeelist(Request $request){
            $areaId = $request->input('subregion');
            $shiftId = $request->input('shift');
            $today = $request->input('date');

            $Query = "
            SELECT empid, serviceno, empfullname, empnic, empdesignation
            FROM (
                SELECT subquery.mainid AS empid, subquery.service_no AS serviceno,subquery.subregion_id AS subregion,
                subquery.emp_name_with_initial AS empfullname,subquery.empjobcode AS empdesignation, subquery.emp_national_id AS empnic, subquery.subregion_id_to
                FROM (
                    SELECT e.*, e.emp_job_code AS empjobcode, e.id AS mainid, et.emp_id AS employeeid, et.from_date AS fromdate, et.to_date AS todate, te.subregion_id_to, te.approve_status AS approvaltransfer
                    FROM employees e
                    LEFT JOIN employeetransfer_details et ON e.id = et.emp_id
                    LEFT JOIN employeetransfers te ON et.transfer_id = te.id
                ) AS subquery
                WHERE subquery.subregion_id = '$areaId' OR subquery.subregion_id_to = '$areaId'
                  AND subquery.fromdate <= '$today' AND subquery.todate >= '$today' AND subquery.approvaltransfer = '1' AND subquery.emp_category = '2'
            ) AS subquery2 
            LEFT JOIN empattendances AS emt ON subquery2.empid = emt.emp_id AND emt.date = '$today' AND emt.shift_id = '$shiftId'
            WHERE emt.id IS null
            ";

            $data = DB::select($Query);
               
            $data = array(
              'employees' => $data
          );
          return (new BaseController)->sendResponse($data, 'employees');
        }
}