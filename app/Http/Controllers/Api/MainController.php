<?php

namespace App\Http\Controllers\Api;

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

class MainController extends Controller
{
    public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');
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

    public function getCustomerBranches(Request $request)
    {
        $q = "
            SELECT cb.* 
            FROM customerbranches cb 
        ";

        $data = DB::select($q);

        $data = array(
            'customer_branches' => $data
        );

        return (new BaseController)->sendResponse($data, 'customer_branches');
    }

    public function attendanceStore(Request $request)
    {
        //validate request
        $validator = \Validator::make($request->all(), [
            'emp_id' => 'required',
            'uid' => 'required',
            'state' => 'required',
            'timestamp' => 'required',
            'date' => 'required',
            'approved' => 'required',
            'type' => 'required',
            'devicesno' => 'required',
            'location' => 'required'

        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $time_stamp = date('H:i:s', strtotime($request->timestamp));
        $time_stamp =  $request->date.' '.$time_stamp;

        $attendance = new \App\Attendance;
        $attendance->emp_id = $request->emp_id;
        $attendance->uid = $request->uid;
        $attendance->state = $request->state;
        $attendance->timestamp = $time_stamp;
        $attendance->date = $request->date;
        $attendance->approved = $request->approved;
        $attendance->type = $request->type;
        $attendance->devicesno = $request->devicesno;
        $attendance->location = $request->location;

        $attendance->save();
        return (new BaseController)->sendResponse($attendance, 'Attendance Added');
    }

    public function getEmployeeInfo(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'branch_id' => 'required',
            'date' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $q = "
            SELECT * 
            FROM empallocations ea 
            LEFT JOIN empallocationdetails ead ON ead.allocation_id = ea.id 
            LEFT JOIN employees e ON e.emp_id = ead.emp_id
            LEFT JOIN shifts s ON s.id = ea.shift_id
            WHERE ea.customerbranch_id = '$request->branch_id' 
            AND ea.date = '$request->date'
        ";

        $data = DB::select($q);

        $data = array(
            'employee_info' => $data
        );

        return (new BaseController)->sendResponse($data, 'employee_info');
    }
 
    public function GetLeavesList(Request $request)
    {
        $leaves = Leave::with('leave_type')
            ->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($leaves, 'leave_types');
    }


    public function ApplyLeave(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee' => 'required',
            'leavetype' => 'required',
            'fromdate' => 'required',
            'todate' => 'required',
            'reson' => 'required',
            'no_of_days' => 'required',
            'half_short' => 'required',
            'coveringemployee' => 'required',
            'approveby' => 'required',
            'approveby_2' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        $leave = new Leave;
        $leave->emp_id = $request->input('employee');
        $leave->leave_type = $request->input('leavetype');
        $leave->leave_from = $request->input('fromdate');
        $leave->leave_to = $request->input('todate');
        $leave->no_of_days = $request->input('no_of_days');
        $leave->half_short = $half_short;
        $leave->reson = $request->input('reson');
        $leave->comment = $request->input('comment');
        $leave->emp_covering = $request->input('coveringemployee');
        $leave->leave_approv_person = $request->input('approveby');
        $leave->leave_approv_person_2 = $request->input('approveby_2');
        $leave->status = 'Pending';
        $leave->save();

        $users = DB::table('leave_details')
            ->where('emp_id', $request->employee)
            ->count();

        if ($users == 0) {
            $leavedetails = new LeaveDetail;
            $leavedetails->emp_id = $request->input('employee');
            $leavedetails->leave_type = $request->input('leavetype');
            $assign_leave = $request->input('assign_leave');
            $total_leave = $assign_leave - $diff_days;
            $leavedetails->total_leave = $total_leave;
            $leavedetails->save();

        } else {
            DB::table('leave_details')
                ->where('emp_id', $request->employee)
                ->where('leave_type', $request->leavetype)
                ->decrement('total_leave', $diff_days);
        }

        try {
            (new \App\Http\Controllers\Api\ApiLeaveEmailController)->sendLeaveApplyEmail($leave);
        } catch (\Exception $e) {
            \Log::error('ApplyLeave email error: ' . $e->getMessage());
        }

        return (new BaseController)->sendResponse($leave, 'Leave Details Successfully Insert!');
    }

      public function getLastLeaveApprovers(Request $request)
    {
        $empId = $request->input('employee');

        if (empty($empId)) {
            return response()->json(['found' => false]);
        }

        $leave = DB::table('leaves')
            ->select('leave_approv_person', 'leave_approv_person_2')
            ->where('emp_id', $empId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$leave) {
            $data = $this->getReportPersonApprovers($empId);
            return (new BaseController)->sendResponse($data, 'Report Persons Loaded');
        }

        $data = array(
            'leave_approv_person'   => (int) $leave->leave_approv_person,
            'leave_approv_person_2' => (int) $leave->leave_approv_person_2,
        );
         return (new BaseController)->sendResponse($data, 'Leave Approvers Found');
    }

        public function getReportPersonApprovers($empId)
    {
        $approver1 = '';
        $approver2 = '';

        $first = DB::table('employees')
            ->where('emp_id', $empId)
            ->value('report_person_id');

        if (!empty($first)) {
            $approver1 = (string) $first;

            $second = DB::table('employees')
                ->where('emp_id', $approver1)
                ->value('report_person_id');

            if (!empty($second)) {
                $approver2 = (string) $second;
            }
        }

        // Loop wenna epa
        if ($approver2 === (string) $empId || $approver2 === $approver1) {
            $approver2 = '';
        }

        return array(
            'leave_approv_person'   => $approver1,
            'leave_approv_person_2' => $approver2,
        );
    }

    public function editLeave(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leaves = Leave::with('employee')
            ->with(array(
                'employee' => function ($query) {
                    $query->select('emp_id', 'emp_name_with_initial', 'calling_name');
                },
                'covering_employee' => function ($query) {
                    $query->select('emp_id', 'emp_name_with_initial', 'calling_name');
                },
                'approve_by' => function ($query) {
                    $query->select('emp_id', 'emp_name_with_initial', 'calling_name');
                },
                'approve2_by' => function ($query) {
                    $query->select('emp_id', 'emp_name_with_initial', 'calling_name');
                },
            ))
            ->findOrFail($request->id);
        $data = array(
            'leave_info' => $leaves
        );

        return (new BaseController)->sendResponse($data, 'leave_info');
    }

    public function UpdateLeave(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'employee' => 'required',
            'leavetype' => 'required',
            'fromdate' => 'required',
            'todate' => 'required',
            'reson' => 'required',
            'no_of_days' => 'required',
            'half_short' => 'required',
            'coveringemployee' => 'required',
            'approveby' => 'required',
            'approveby_2' => 'required',
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leave = Leave::find($request->input('id'));

        if (!$leave) {
            return (new BaseController())->sendError('Leave Not Found.', array(), '404');
        }

        if ($leave->status !== 'Pending' || $leave->approve_01 == 1 || $leave->approve_02 == 1) {
            return (new BaseController())->sendError('This leave is already processed and cannot be edited.', array(), '400');
        }

        $leave->emp_id = $request->input('employee');
        $leave->leave_type = $request->input('leavetype');
        $leave->leave_from = $request->input('fromdate');
        $leave->leave_to = $request->input('todate');
        $leave->no_of_days = $request->input('no_of_days');
        $leave->half_short = $request->input('half_short');
        $leave->reson = $request->input('reson');
        $leave->comment = $request->input('comment');
        $leave->emp_covering = $request->input('coveringemployee');
        $leave->leave_approv_person = $request->input('approveby');
        $leave->leave_approv_person_2 = $request->input('approveby_2');
        $leave->save();

        try {
            (new \App\Http\Controllers\Api\ApiLeaveEmailController)->sendLeaveApplyEmail($leave);
        } catch (\Exception $e) {
            \Log::error('ApplyLeave email error: ' . $e->getMessage());
        }

        return (new BaseController)->sendResponse($leave, 'Leave Details Successfully Updated!');
    }

}
