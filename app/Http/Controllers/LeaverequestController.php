<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use Illuminate\Support\Facades\Auth;
use App\LeaveType;
use App\Employee;
use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveRequest;
use DB;
use Yajra\Datatables\Datatables;

class LeaverequestController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('LeaveRequest-list');
        if (!$permission) {
            abort(403);
        }
        $leavetype = LeaveType::orderBy('id', 'asc')->get();
        $employee = Employee::orderBy('id', 'desc')->get();

        return view('Leave.leaverequest', compact('leavetype', 'employee'));
    }

    public function insert(Request $request){

        $permission = \Auth::user()->can('LeaveRequest-create');
        if (!$permission) {
            abort(403);
        }

        $employee   = $request->input('employee_f');
        $fromdate   = $request->input('fromdate');
        $todate     = $request->input('todate');
        $half_short = $request->input('half_short');
        $reason     = $request->input('reason');
        $leavetype  = $request->input('leavetype');
        $from_time  = $request->input('from_time');
        $to_time    = $request->input('to_time');

        $settings = DB::table('hrm_general_settings as settings')
            ->join('hrm_general_settings_key_list as key_list', 'settings.key_id', '=', 'key_list.id')
            ->where('key_list.config_key', 'OPMA_LEAVE')
            ->where('settings.status', 1)
            ->select('settings.config_value')
            ->first();

        if ($settings && $settings->config_value == 1) {

                // 1. Machine conflict - any other emp on same machine already on leave same range
                $empautoid = DB::table('employees')->where('emp_id', $employee)->value('id');

                $machines = DB::table('employee_assigned_devices')
                    ->where('emp_id', $empautoid)
                    ->where('status', 1)
                     ->get(['device_type']);

                foreach ($machines as $m) {
                    $coEmpIds = DB::table('employee_assigned_devices')
                        ->where('device_type', $m->device_type)
                        ->where('emp_id', '!=', $empautoid)
                        ->where('status', 1)
                        ->pluck('emp_id');
                        
                    if ($coEmpIds->isNotEmpty()) {
                       $conflict = DB::table('leave_request as lr')
                                    ->join('employees as e', 'e.emp_id', '=', 'lr.emp_id')
                                    ->whereIn('e.id', $coEmpIds)
                                    ->where('lr.from_date', '<=', $todate)
                                    ->where('lr.to_date', '>=', $fromdate)
                                    ->where('lr.request_approve_status', '=', 1)
                                    ->where('lr.status', '!=', 3)
                                    ->exists();

                        if ($conflict) {
                            return response()->json(['errors' => 'Another employee assigned to the same machine has already taken leave on this date.']);
                        }
                    }
                }

                // 2. Department conflict - another emp in same department already on leave same range
                $dept = DB::table('employees')->where('emp_id', $employee)->value('emp_department');

                if ($dept) {
                    $deptEmpIds = DB::table('employees')
                        ->where('emp_department', $dept)
                        ->where('emp_id', '!=', $employee)
                        ->pluck('emp_id');

                   $deptConflict = DB::table('leave_request')
                                    ->whereIn('emp_id', $deptEmpIds)
                                    ->where('from_date', '<=', $todate)
                                    ->where('to_date', '>=', $fromdate)
                                    ->where('request_approve_status', '=', 1)
                                    ->where('status', '!=', 3)
                                    ->exists();

                    if ($deptConflict) {
                        return response()->json(['errors' => 'Another employee in the same department has already taken leave on this date.']);
                    }
                }

                // 3. Covering employee conflict - emp is set as covering on an overlapping leave
                $coveringConflict = DB::table('leaves')
                    ->where('emp_covering', $employee)
                    ->where('leave_from', '<=', $todate)
                    ->where('leave_to', '>=', $fromdate)
                    ->where('status', 'Approved')
                    ->exists();

                if ($coveringConflict) {
                    return response()->json(['errors' => 'This employee is assigned as covering employee for another leave on this date.']);
                }


                $leaveRequest = new LeaveRequest();
                $leaveRequest->emp_id = $employee;
                $leaveRequest->from_date = $fromdate;
                $leaveRequest->to_date = $todate;
                $leaveRequest->leave_category = $half_short;
                $leaveRequest->reason = $reason;
                $leaveRequest->leave_type = $leavetype;
                $leaveRequest->from_time = $from_time;
                $leaveRequest->to_time = $to_time;
                $leaveRequest->status = '1';
                $leaveRequest->created_by = Auth::id();
                $leaveRequest->updated_by = '0';
                $leaveRequest->approve_status = '0';
                $leaveRequest->request_approve_status = '0';
                $leaveRequest->save();

        }else{

             $leaveRequest = new LeaveRequest();
            $leaveRequest->emp_id = $employee;
            $leaveRequest->from_date = $fromdate;
            $leaveRequest->to_date = $todate;
            $leaveRequest->leave_category = $half_short;
            $leaveRequest->reason = $reason;
            $leaveRequest->leave_type = $leavetype;
            $leaveRequest->from_time = $from_time;
            $leaveRequest->to_time = $to_time;
            $leaveRequest->status = '1';
            $leaveRequest->created_by = Auth::id();
            $leaveRequest->updated_by = '0';
            $leaveRequest->approve_status = '0';
            $leaveRequest->request_approve_status = '0';
            $leaveRequest->save();

        }

       

        return response()->json(['success' => 'Leave Request Details Successfully Insert']);
    }

    public function edit(Request $request)
    {
        $permission = \Auth::user()->can('LeaveRequest-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('leave_request')
        ->join('employees as emp', 'leave_request.emp_id', '=', 'emp.emp_id')
        ->select('leave_request.*','emp.emp_name_with_initial as emp_name')
        ->where('leave_request.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }

    public function update(Request $request){

        $permission = \Auth::user()->can('LeaveRequest-edit');
        if (!$permission) {
            abort(403);
        }

        $employee=$request->input('employee_f');
        $fromdate=$request->input('fromdate');
        $todate=$request->input('todate');
        $half_short=$request->input('half_short');
        $reason=$request->input('reason');
        $leavetype=$request->input('leavetype');
        $from_time=$request->input('from_time');
        $to_time=$request->input('to_time');

        $hidden_id=$request->input('hidden_id');

        $data = array(
            'emp_id' => $employee,
            'from_date' => $fromdate,
            'to_date' => $todate,
            'leave_category' => $half_short,
            'reason' => $reason,
            'leave_type' => $leavetype,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'request_approve_status' => 0,
            'updated_by' => Auth::id(),
        );

        LeaveRequest::where('id', $hidden_id)
        ->update($data);
        return response()->json(['success' => 'Leave Request Details Updated successfully.']);
    }

    public function delete(Request $request)
    {
        $permission = \Auth::user()->can('LeaveRequest-delete');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        LeaveRequest::where('id',$id)
        ->update($form_data);

        return response()->json(['success' => 'Leave Request Details is Successfully Deleted']);
    }

    public function getemployeeleaverequest(Request $request){

            $userId = Auth::id();
            $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
            
            // Return empty data if no accessible employees
            if (empty($accessibleEmployeeIds)) {
                return response()->json(['data' => []]);
            }

            $userBranchIds = DB::table('user_has_companies')
            ->where('user_id', $userId)
            ->pluck('branch_id')
            ->toArray();


        $data = DB::table('leave_request')
                ->select(
                    'leave_request.id',
                    'emp.emp_id',
                    'emp.emp_name_with_initial',
                    'emp.calling_name',
                    'emp.emp_location',
                    'departments.name as department_name',
                    'leave_types.leave_type',
                    'leave_request.from_date as leave_from',
                    'leave_request.to_date as leave_to',
                    'leave_request.request_approve_status as approvestatus',
                    'leave_request.leave_category',
                    'leave_request.reason',
                    'leaves.half_short',
                    'leaves.status as leave_status'
                )
                ->join('employees as emp', 'leave_request.emp_id', '=', 'emp.emp_id')
                ->leftJoin('departments', 'emp.emp_department', '=', 'departments.id')
                ->leftJoin('leaves', 'leave_request.id', '=', 'leaves.request_id')
                ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                ->where('leave_request.status', 1)
                ->where('leave_request.approve_status', 0)
                ->where('leave_request.request_approve_status', 1)
                ->when(!empty($userBranchIds), function($q) use ($userBranchIds) {
                    return $q->whereIn('emp.emp_location', $userBranchIds);
                })
                ->get();

        $html = '';

        foreach ($data as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row->emp_id . '</td>'; 
            $html .= '<td>' . $row->emp_name_with_initial. ' - ' . $row->calling_name. '</td>';  
            $html .= '<td>' . $row->department_name . '</td>';  
            $leaveType = '';
            if ($row->leave_category == 0.25) {
                $leaveType = 'Short Leave';
            } elseif ($row->leave_category == 0.5) {
                $leaveType = 'Half Day';
            } elseif ($row->leave_category == 1.0) {
                $leaveType = 'Full Day';
            }
            $html .= '<td>' . $leaveType . '</td>';
            $html .= '<td>' .$row->leave_from . '</td>';  
            $html .= '<td>' . $row->leave_to . '</td>'; 
            $html .= '<td>' . $row->reason . '</td>';  
            $html .= '<td class="text-right"><button name="addrequest" id="' . $row->id . '" class="addrequest btn btn-primary btn-sm"><i class="fas fa-plus"></i></button></td>';
            $html .= '</tr>';
        }
        
        return response() ->json(['result'=>  $html]);
    }

    public function approve(Request $request)
    {
        $permission = \Auth::user()->can('LeaveRequest-Approve');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        $form_data = array(
            'request_approve_status' =>  '1',
            'updated_by' => Auth::id()
        );
        LeaveRequest::where('id',$id)
        ->update($form_data);

        return response()->json(['success' => 'Leave Request Details is Successfully Approved']);
    }

}
