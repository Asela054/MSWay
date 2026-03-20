<?php

namespace App\Http\Controllers;

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

        $employee=$request->input('employee_f');
        $fromdate=$request->input('fromdate');
        $todate=$request->input('todate');
        $half_short=$request->input('half_short');
        $reason=$request->input('reason');
        $leavetype=$request->input('leavetype');
        $from_time=$request->input('from_time');
        $to_time=$request->input('to_time');

        $request = new LeaveRequest();
        $request->emp_id=$employee;
        $request->from_date=$fromdate;
        $request->to_date=$todate;
        $request->leave_category=$half_short;
        $request->reason=$reason;
        $request->leave_type=$leavetype;
        $request->from_time=$from_time;
        $request->to_time=$to_time;
        $request->status= '1';
        $request->created_by=Auth::id();
        $request->updated_by = '0';
        $request->approve_status = '0';
        $request->request_approve_status = '0';
        $request->save();

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

        $data = DB::table('leave_request')
                ->select(
                    'leave_request.id',
                    'emp.emp_id',
                    'emp.emp_name_with_initial',
                    'emp.calling_name',
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
