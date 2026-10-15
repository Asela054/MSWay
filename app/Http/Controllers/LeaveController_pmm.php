<?php

namespace App\Http\Controllers;

use App\Leave;
use App\LeaveType;
use App\Employee;
use App\Helpers\EmployeeHelper;
use App\LeaveDetail;
use App\LeaveRequest;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Yajra\Datatables\Datatables;
use App\Holiday;
use App\Services\LeavepolicyService;
use App\Http\Controllers\Controller;

class LeaveController_pmm extends Controller
{
     protected $leavePolicyService;

    public function __construct(LeavepolicyService $leavePolicyService)
    {
        $this->leavePolicyService = $leavePolicyService;
        $this->middleware('auth');
    }

     public function store(Request $request)
    {
        $permission = Auth::user()->can('leave-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'employee' => '',
            'leavetype' => '',
            'fromdate' => '',
            'todate' => '',
            'reson' => '',
            'approveby' => '',
            'approveby2' => '',

        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        // Process leave balance data from frontend
            $leaveBalanceData = [];
            $balanceErrors = [];

            if ($request->has('leave_balance_data')) {
                $leaveBalanceData = json_decode($request->leave_balance_data, true);
                
                // Validate if the applied leave doesn't exceed available balance
             

                    foreach ($leaveBalanceData as $balance) {
                        if ($balance['leave_type'] === 'Annual' && $request->leavetype == 1) {
                            $availableAnnual = (float) $balance['available'];
                            if ($diff_days > $availableAnnual) {
                                $balanceErrors[] = "Applied annual leave days ($diff_days) exceed available balance ($availableAnnual days)";
                            }
                        }
                        
                        if ($balance['leave_type'] === 'Casual' && $request->leavetype == 2) {
                            $availableCasual = (float) $balance['available'];
                            if ($diff_days > $availableCasual) {
                                $balanceErrors[] = "Applied casual leave days ($diff_days) exceed available balance ($availableCasual days)";
                            }
                        }
                
           } 
        }

            if (!empty($balanceErrors)) {
                    return response()->json(['errors' => $balanceErrors]);
                }

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
        $leave->leave_approv_person_2 = $request->input('approveby2');
        $leave->leave_category = $request->input('leavecat');
        $leave->status = 'Pending';
        $leave->request_id = $request->input('request_id');
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

        if(!empty($request->request_id)){
            $form_data = array(
                'approve_status' =>  '1',
            );
            LeaveRequest::where('id', $request->request_id)
            ->update($form_data);
        }

        return response()->json(['success' => 'Leave Details Successfully Insert']);

    }

    public function edit($id)
    {
         $permission = Auth::user()->can('leave-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        if (request()->ajax()) {
            $data = Leave::with('employee')
                ->with('covering_employee')
                ->with('approve_by')
                ->with('approve2_by')
                ->findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, Leave $leave)
    {
         $permission = Auth::user()->can('leave-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $rules = array(
            'hidden_id' => 'required',
            'leavetype' => 'required',
            'fromdate' => 'required',
            'todate' => 'required',
            'reson' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        $no_of_days = $request->no_of_days;

        $form_data = array(
            'leave_type' => $request->leavetype,
            'leave_from' => $request->fromdate,
            'leave_to' => $request->todate,
            'no_of_days' => $no_of_days,
            'half_short' => $half_short,
            'reson' => $request->reson,
            'emp_covering' => $request->coveringemployee,
            'leave_approv_person' => $request->approveby,
            'leave_approv_person_2' => $request->approveby2,
            'leave_category' => $request->leavecat,
            'status' => 'Pending'

        );

        Leave::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Leave Details Successfully Updated']);
    }

     public function approvelupdate(Request $request)
    {
        $permission = Auth::user()->can('leave-approve');
        if (!$permission) {
            return response()->json(['errors' => 'UnAuthorized'], 401);
        }

        $leave = Leave::find($request->id);

        if (!$leave) {
            return response()->json(['errors' => 'Leave record not found'], 404);
        }

        $user = Auth::user();
        $users_id = $user->emp_id;

        $isAdmin = Auth::user()->hasRole('Admin');
        $isApprover = ($leave->leave_approv_person == $users_id || $leave->leave_approv_person_2 == $users_id);

        if (!($isApprover || $isAdmin)) {
            return response()->json(['errors' => 'UnAuthorized. Only the assigned approver or admin can approve this leave.']);
          
        }

        $rules = array(
            'status' => 'required',
            'emp_id' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $status = $request->status;
        $applevel = $request->app_level;
        
        // Already rejected leave not reject again
        if ($status == 'Rejected' && $leave->status == 'Rejected') {
            return response()->json(['errors' => 'Leave is already rejected']);
            
        }


        $current_date_time = Carbon::now()->toDateTimeString();

        if($applevel == 1){
            $form_data = array(
               'approve_01' => 1 ,
               'approve_01_time' => $current_date_time,
               'approve_01_by' =>  Auth::id(),
             );

            if ($status == 'Rejected') {
                $form_data['status']  = $status;
                $form_data['comment'] = $request->comment;
            }

        }else if($applevel == 2){

            $form_data = array(
                'approve_02' => 1 ,
                'approve_02_time' => $current_date_time,
                'approve_02_by' =>  Auth::id(),
                'status' => $status,
                'comment' => $request->comment
            );



        }else{
            $form_data = array(
                'status' => $status,
                'comment' => $request->comment
            );
        }
       

        DB::transaction(function () use ($request, $form_data, $status, $leave) {
            Leave::whereId($request->id)->update($form_data);

            if ($status == 'Rejected') {
                DB::table('leave_details')
                    ->where('emp_id', $leave->emp_id)
                    ->where('leave_type', $leave->leave_type)
                    ->increment('total_leave', $leave->no_of_days);
            }
        });
        

        $response = ['success' => $status == 'Rejected' ? 'Leave Rejected' : 'Leave Approved'];


        if ($applevel != 1) {
            $response['mail'] = $this->buildLeaveMailData(
                $leave->fresh(),
                $status == 'Rejected' ? 'Rejected' : 'Approved',
                $request->comment
            );
        }
        return response()->json($response);
    }

     public function leave_approve_batch(Request $request){

        $permission = Auth::user()->can('leave-approve-batch');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $dataarry = $request->input('dataarry');
        $comment = $request->input('comment');
        $status = $request->input('status');
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {
         
            $empid = $row['empid'];
            $emp_name = $row['emp_name'];
            $laeaveid = $row['laeaveid'];
            $app_level = $row['app_level'];

            if($app_level == 1){
                $form_data = array(
                'approve_01' => 1 ,
                'approve_01_time' => $current_date_time,
                'approve_01_by' =>  Auth::id(),
                );

            }else if($app_level == 2){

                $form_data = array(
                    'approve_02' => 1 ,
                    'approve_02_time' => $current_date_time,
                    'approve_02_by' =>  Auth::id(),
                    'status' => $status,
                    'comment' => $request->comment
                );

            }else{
                $form_data = array(
                    'status' => $status,
                    'comment' => $request->comment
                );
            }
    
            Leave::whereId($laeaveid)->update($form_data);

            if ( $status == 'Rejected') {

                $leaves = DB::table('leaves')
                    ->where('id', $laeaveid)
                    ->get();
    
                $to = \Carbon\Carbon::createFromFormat('Y-m-d', $leaves[0]->leave_from);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leaves[0]->leave_to);
                $diff_days = $to->diffInDays($from);
    
                DB::table('leave_details')
                    ->where('emp_id', $empid)
                    ->where('leave_type', $leaves[0]->leave_type)
                    ->increment('total_leave', $diff_days);
    
                      $smsResult = null;

                 if ($app_level != 1) {
                    $this->sendLeaveStatusSms($leaves[0], 'Rejected');
                }
                
            }else{
                  $smsResult = null;
                  // Only send "Approved" SMS on final approval level
                if ($app_level != 1) {
                    $this->sendLeaveStatusSms($leaves[0], 'Approved');
                }
            }


        }
        $freshLeave = Leave::find($laeaveid);
        if ($app_level != 1 && $freshLeave) {
            $mails[] = $this->buildLeaveMailData(
                $freshLeave,
                $status == 'Rejected' ? 'Rejected' : 'Approved',
                $comment
            );
        }



       return response()->json([
            'success' => $status == 'Rejected' ? 'Leave Rejected' : 'Leave Approved',
            'mails'   => $mails
        ]);
    }

    public function getLastLeaveApprovers(Request $request)
    {
        $empId = $request->input('emp_id');

        if (empty($empId)) {
            return response()->json(['found' => false]);
        }

        $leave = DB::table('leaves')
            ->select('leave_approv_person', 'leave_approv_person_2')
            ->where('emp_id', $empId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$leave) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'                 => true,
            'leave_approv_person'   => $leave->leave_approv_person,
            'leave_approv_person_2' => $leave->leave_approv_person_2,
        ]);
    }

    public function getEmployeeCategory(Request $request)
    {
          $empid = $request->input('emp_id');

        $employeedetails = DB::table('employees')
            ->leftJoin('job_categories', 'job_categories.id',  '=', 'employees.job_category_id' )
            ->leftJoin('companies', 'companies.id',  '=', 'employees.emp_company' )
            ->leftJoin('departments', 'departments.id',  '=', 'employees.emp_department' )
            ->leftJoin('employees as dept_heads', 'dept_heads.emp_id',  '=', 'departments.dep_head_emp_id' )
            ->select('employees.job_category_id', 'category', 'employees.emp_email AS employee_email', 'companies.email AS company_email', 'companies.name AS company_name','dept_heads.emp_email AS department_head_email')
            ->where('employees.emp_id', $empid)
            ->first();

         return response()->json(['result' => $employeedetails]);
    }

     private function getCategoryResult($empId)
    {
        if (empty($empId)) {
            return null;
        }

        $res  = $this->getEmployeeCategory(new Request(array('emp_id' => $empId)));
        $data = method_exists($res, 'getData') ? $res->getData() : null;

        return ($data && isset($data->result)) ? $data->result : null;
    }

    private function buildLeaveMailData($leave, $status, $comment)
    {
        $leaveTypeName = DB::table('leave_types')->where('id', $leave->leave_type)->value('leave_type');

        $empName = function ($empId) {
            if (empty($empId)) {
                return '';
            }
            return (string) DB::table('employees')->where('emp_id', $empId)->value('emp_name_with_initial');
        };

        $emp = $this->getCategoryResult($leave->emp_id);
        $cov = $this->getCategoryResult($leave->emp_covering);
        $app = $this->getCategoryResult($leave->leave_approv_person);
        $app2 = $this->getCategoryResult($leave->leave_approv_person_2);

        $emails = array(
            $emp ? $emp->employee_email : null,          // employee
            $emp ? $emp->company_email : null,           // company
            $cov ? $cov->employee_email : null,          // covering person
            $app ? $app->employee_email : null,          // approve person
            $app2 ? $app2->employee_email : null,        // approve person 2
            $emp ? $emp->department_head_email : null    // department head
        );
        $emails = array_values(array_unique(array_filter($emails)));

        $companyName = $emp ? $emp->company_name : '';

        return array(
            'employee'     => $empName($leave->emp_id),
            'employee_id'  => $leave->emp_id,
            'leave_type'   => $leaveTypeName,
            'half_short'   => $leave->half_short,
            'from_date'    => $leave->leave_from,
            'to_date'      => $leave->leave_to,
            'no_of_days'   => $leave->no_of_days,
            'reason'       => $leave->reson,
            'covering'     => $empName($leave->emp_covering),
            'approver'     => $empName($leave->leave_approv_person),
            'approver2'     => $empName($leave->leave_approv_person_2),
            'status'       => $status,
            'comment'      => $comment,
            'company_name' => $companyName,
            'emails'       => $emails,
        );
    }
}
