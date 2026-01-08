<?php

namespace App\Http\Controllers\Api;

use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\LeavepolicyService;
use App\LeaveRequest;

class APILeaveController extends Controller
{
    protected $leavePolicyService;

     public function __construct(LeavepolicyService $leavePolicyService)
    {

         $this->leavePolicyService = $leavePolicyService;

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

       public function GetLeaveTypes(Request $request)
    {
        $q = "
            SELECT * 
            FROM  leave_types 
        ";

        $data = DB::select($q);

        $data = array(
            'leave_types' => $data
        );

        return (new BaseController)->sendResponse($data, 'leave_types');
    }

     public function GetLeaveBalance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employee = Employee::where('emp_id', $request->employee_id)->first();
        if($employee == NULL){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Record ID']);
        }

        $emp_join_date = isset($employee->emp_join_date) ? $employee->emp_join_date : false;
        $join_year = Carbon::parse($emp_join_date)->year;
        $join_month = Carbon::parse($emp_join_date)->month;
        $join_date = Carbon::parse($emp_join_date)->day;
        $full_date = '2022-'.$join_month.'-'.$join_date;
        $empid = $employee->emp_id;
        $job_categoryid = $employee->job_category_id;


        $formated_from_date = date('Y').'-01-01';
        $formated_fromto_date = date('Y').'-12-31';

        $current_year_taken_a_l = (new \App\Leave)->taken_annual_leaves($empid, $formated_from_date, $formated_fromto_date);

        $current_year_taken_c_l = (new \App\Leave)->taken_casual_leaves($empid, $formated_from_date, $formated_fromto_date);

       $current_year_taken_med = (new \App\Leave)->taken_medical_leaves($empid, $formated_from_date, $formated_fromto_date);

         $leave_msg = '';

        $annualData = $this->leavePolicyService->calculateAnnualLeaves($employee->emp_join_date, $employee->emp_id, $job_categoryid);
        $annual_leaves = $annualData['annual_leaves'];
        $leave_msg = $annualData['leave_msg'];

        $casual_leaves = $this->leavePolicyService->calculateCasualLeaves($employee->emp_join_date, $job_categoryid);

         // medical leave calculation
        $medical_leaves = $this->leavePolicyService->getMedicalLeaves($employee->job_category_id);


        $total_no_of_annual_leaves = $annual_leaves;
        $total_no_of_casual_leaves = $casual_leaves;
        $total_no_of_med_leaves = $medical_leaves;

        $available_no_of_annual_leaves = $total_no_of_annual_leaves - $current_year_taken_a_l;
        $available_no_of_casual_leaves = $total_no_of_casual_leaves - $current_year_taken_c_l;
        $available_no_of_med_leaves = $total_no_of_med_leaves - $current_year_taken_med;

        $casual_arr = array(
            'leave_type_id' => 1,
            'leave_type_name' => 'Casual',
            'total' => $total_no_of_casual_leaves,
            'taken' => $current_year_taken_c_l,
            'available' => $available_no_of_casual_leaves
        );

        $annual_arr = array(
            'leave_type_id' => 2,
            'leave_type_name' => 'Annual',
            'total' => $total_no_of_annual_leaves,
            'taken' => $current_year_taken_a_l,
            'available' => $available_no_of_annual_leaves
        );

        $medical_arr = array(
            'leave_type_id' => 3,
            'leave_type_name' => 'Medical',
            'total' => $total_no_of_med_leaves,
            'taken' => $current_year_taken_med,
            'available' => $available_no_of_med_leaves
        );


        $main_arr = array();
        array_push($main_arr, $casual_arr);
        array_push($main_arr, $annual_arr);
        array_push($main_arr, $medical_arr);

        return (new BaseController)->sendResponse($main_arr, 'Leave Details');
    }

      public function leaverequestinsert(Request $request)
    {

        $employee=$request->input('employee');
        $fromdate=$request->input('fromdate');
        $todate=$request->input('todate');
        $half_short=$request->input('half_short');
        $reason=$request->input('reason');

        $request = new LeaveRequest();
        $request->emp_id=$employee;
        $request->from_date=$fromdate;
        $request->to_date=$todate;
        $request->leave_category=$half_short;
        $request->reason=$reason;
        $request->status= '1';
        $request->created_by=$employee;
        $request->updated_by = '0';
        $request->approve_status = '0';
        $request->request_approve_status = '0';
        $request->save();

        return (new BaseController)->sendResponse($request, 'Leave Request Details Successfully Insert');
    }

      public function getemployeeleaverequest(Request $request)
    {
         $requestid = $request->get('requestid');

        $leaveRequests = DB::table('leave_request')
            ->select('leave_request.*')
            ->where('leave_request.id', $requestid)
            ->where('leave_request.status', 1)
            ->where('leave_request.approve_status', 0)
            ->where('leave_request.request_approve_status', 1)
            ->get()
            ->map(function ($row) {
                $leaveType = '';
                if ($row->leave_category == 0.25) {
                    $leaveType = 'Short Leave';
                } elseif ($row->leave_category == 0.5) {
                    $leaveType = 'Half Day';
                } elseif ($row->leave_category == 1.0) {
                    $leaveType = 'Full Day';
                }

                return [
                    'id' => $row->id,
                    'emp_id' => $row->emp_id,
                    'from_date' => $row->from_date,
                    'to_date' => $row->to_date,
                    'leave_category' => $row->leave_category,
                    'leave_type' => $leaveType
                ];
            });

        $data = [
            'leave_request' => $leaveRequests
        ];

        return (new BaseController)->sendResponse($data, 'Employee leave request retrieved successfully');
    }

     public function leaverequest_list(Request $request)
    {

        $employee = $request->get('employee');

        $query =  DB::table('leave_request')
        ->leftjoin('employees as emp', 'leave_request.emp_id', '=', 'emp.emp_id')
        ->leftjoin('departments', 'emp.emp_department', '=', 'departments.id')
        ->leftjoin('leaves', 'leave_request.id', '=', 'leaves.request_id')
        ->leftjoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
        ->select(
            'leave_request.*', 
            'emp.emp_name_with_initial as emp_name', 
            'departments.name as dep_name', 
            'leaves.leave_type as leave_type_id', 
            'leave_types.leave_type as leave_type_name', 
             DB::raw('CASE WHEN leaves.status IS NULL THEN "Pending" ELSE leaves.status END as leave_status'),
            'leaves.half_short as half_short'
        )
        ->where('leave_request.status', 1)
        ->where('leave_request.emp_id', $employee)
        ->get();

        $data = array(
            'leaverequests' => $query,
        );

        return (new BaseController)->sendResponse($data, 'leaverequests');

    }


}
