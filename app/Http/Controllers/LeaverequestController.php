<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\LeaveType;
use App\Employee;
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
        $request->created_by=Auth::id();
        $request->updated_by = '0';
        $request->approve_status = '0';
        $request->request_approve_status = '0';
        $request->save();

        return response()->json(['success' => 'Leave Request Details Successfully Insert']);

    }

    public function leavereuest_list(Request $request){

        $department = $request->get('department');
        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query =  DB::table('leave_request')
        ->join('employees as emp', 'leave_request.emp_id', '=', 'emp.emp_id')
        ->leftjoin('departments', 'emp.emp_department', '=', 'departments.id')
        ->leftjoin('leaves', 'leave_request.id', '=', 'leaves.request_id')
        ->leftjoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
        ->select(
            'leave_request.*', 
            'emp.emp_name_with_initial as emp_name', 
            'departments.name as dep_name', 
            'leaves.leave_type as leave_type_id', 
            'leave_types.leave_type as leave_type_name', 
            'leaves.status as leave_status',
            'leaves.half_short as half_short'
        )
        ->where('leave_request.status', 1);

        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee != ''){
            $query->where(['emp.emp_id' => $employee]);
        }

        if($from_date != '' && $to_date != ''){
            $query->whereBetween('leave_request.from_date', [$from_date, $to_date]);
        }

        $data = $query->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('leave_category', function($row){

            if($row->leave_category == 0.25){
                return 'Short Leave';
            }elseif($row->leave_category == 0.5){
                return 'Half Day';
            }
            else{
                return 'Full Day';
            }
            return '';
        })
        ->addColumn('approvestatus', function($row){

            if($row->request_approve_status == 0){
                return 'Not Approved';
            }
            else{
                return 'Approved';
            }
            return '';
        })
        ->addColumn('leave_type', function($row) {
            return $row->leave_type_name ?? ' ';
        })
        ->addColumn('half_or_short', function($row){

            if($row->half_short == 0.25){
                return 'Short Leave';
            }

            if($row->half_short == 0.5){
                return 'Half Day';
            }

            if($row->half_short == 1){
                return 'Full Day';
            }
            return '';
        })
        ->addColumn('action', function($row){
            $btn = '';


            if (Auth::user()->can('LeaveRequest-Approve')) {
                if($row->request_approve_status == 0){
                    $btn .= '<button type="submit" name="approve" id="'.$row->id.'" class="approve btn btn-outline-warning btn-sm" style="margin:1px;" >
                    <i class="fas fa-check"></i></button>';  
                }
            }
            if (Auth::user()->can('LeaveRequest-edit')) {
                $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" style="margin:1px;" type="submit"><i class="fas fa-pencil-alt"></i></button>';
            }

            if (Auth::user()->can('LeaveRequest-delete')) {
                $btn .= '<button type="submit" name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm" style="margin:1px;" ><i class="far fa-trash-alt"></i></button>';
            }

            return $btn;
        })
        ->rawColumns(['action','approvestatus','leave_type','leave_category','half_or_short'])
        ->make(true);


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

        $employee=$request->input('employee');
        $fromdate=$request->input('fromdate');
        $todate=$request->input('todate');
        $half_short=$request->input('half_short');
        $reason=$request->input('reason');
        $hidden_id=$request->input('hidden_id');

        $data = array(
            'emp_id' => $employee,
            'from_date' => $fromdate,
            'to_date' => $todate,
            'leave_category' => $half_short,
            'reason' => $reason,
            'updated_by' => Auth::id(),
        );

        LeaveRequest::where('id', $hidden_id)
        ->update($data);
        return response()->json(['success' => 'Leave Request Details Updated successfully.']);
    }

    public function delete(Request $request)
    {
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
        $emp_id = Request('emp_id');

        $data = DB::table('leave_request')
        ->select('leave_request.*')
        ->where('leave_request.emp_id', $emp_id)
        ->where('leave_request.status', 1)
        ->where('leave_request.approve_status',0)
        ->where('leave_request.request_approve_status',1)
        ->get(); 

        $html = '';

        foreach ($data as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row->from_date . '</td>';  
            $html .= '<td>' . $row->to_date . '</td>'; 
            
            $leaveType = '';
            if ($row->leave_category == 0.25) {
                $leaveType = 'Short Leave';
            } elseif ($row->leave_category == 0.5) {
                $leaveType = 'Half Day';
            } elseif ($row->leave_category == 1.0) {
                $leaveType = 'Full Day';
            }
        
            $html .= '<td>' . $leaveType . '</td>'; 
            $html .= '<td class="text-right"><button name="addrequest" id="' . $row->id . '" class="addrequest btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i></button></td>';
            $html .= '</tr>';
        }
        
        return response() ->json(['result'=>  $html]);
    }

    public function approve(Request $request)
    {
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
