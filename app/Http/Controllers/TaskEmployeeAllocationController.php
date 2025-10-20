<?php

namespace App\Http\Controllers;

use App\EmpTaskAllocation;
use App\EmpTaskAllocationDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class TaskEmployeeAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('task-allocation-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tasks = DB::table('task')
            ->select('id', 'taskname')
            ->get();

        return view('Daily_Task.task_allocation', compact('tasks'));
    }
    
    public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('task-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            DB::beginTransaction();

            $EmpTaskAllocation = new EmpTaskAllocation();
            $EmpTaskAllocation->date = $request->input('date');
            $EmpTaskAllocation->task_id = $request->input('task');
            $EmpTaskAllocation->task_status = '1';
            $EmpTaskAllocation->status = '1';
            $EmpTaskAllocation->created_by = Auth::id();
            $EmpTaskAllocation->updated_by = '0';
            $EmpTaskAllocation->save();

            $requestID = $EmpTaskAllocation->id;
            $date = $request->input('date');
            $task_id = $request->input('task');

            $tableData = $request->input('tableData');

            foreach ($tableData as $rowtabledata) {
                $emp_id = $rowtabledata['col_1'];
                $empname = $rowtabledata['col_2'];

                $EmpTaskAllocationDetail = new EmpTaskAllocationDetail();
                $EmpTaskAllocationDetail->allocation_id = $requestID;
                $EmpTaskAllocationDetail->emp_id = $emp_id;
                $EmpTaskAllocationDetail->date = $date;
                $EmpTaskAllocationDetail->status = '1';
                $EmpTaskAllocationDetail->created_by = Auth::id();
                $EmpTaskAllocationDetail->updated_by = '0';
                $EmpTaskAllocationDetail->save();
            }

            DB::commit();
            return response()->json(['success' => 'Employee Task Allocation Successfully Inserted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => ['An error occurred while saving data: ' . $e->getMessage()]], 422);
        }
    }

    public function requestlist()
    {
        $types = DB::table('emp_task_allocation as eta')
            ->leftJoin('task as p', 'eta.task_id', '=', 'p.id')
            ->select('eta.*', 'p.taskname')
            ->whereIn('eta.status', [1, 2])
            ->get();

        return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();

                $btn .= ' <button name="view" id="'.$row->id.'" class="view btn btn-outline-secondary btn-sm" type="button"><i class="fas fa-eye"></i></button>';

                if($user->can('task-allocation-edit')){
                    $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="button"><i class="fas fa-pencil-alt"></i></button>';
                }

                if($user->can('task-allocation-status')){
                    if($row->status == 1){
                        $btn .= ' <a href="'.route('taskallocationstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                    }else{
                        $btn .= '&nbsp;<a href="'.route('taskallocationstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                    }
                }
                if($user->can('task-allocation-delete')){
                    $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit(Request $request)
    {
        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('emp_task_allocation')
                ->select('emp_task_allocation.*')
                ->where('emp_task_allocation.id', $id)
                ->first(); 
            
            $requestlist = $this->reqestcountlist($id); 
        
            $responseData = array(
                'mainData' => $data,
                'requestdata' => $requestlist,
            );

            return response()->json(['result' => $responseData]);
        }
    }
    
    private function reqestcountlist($id)
    {
        $recordID = $id;
        $data = DB::table('emp_task_allocation_details as ead')
            ->leftJoin('employees as e', 'ead.emp_id', '=', 'e.emp_id')
            ->select(
                'ead.*', 
                'e.emp_name_with_initial as employee_name'
            )
            ->where('ead.allocation_id', $recordID)
            ->where('ead.status', 1)
            ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . ($row->employee_name ?? $row->employee_name) . '</td>'; 
            $htmlTable .= '<td class="text-right">';
            $htmlTable .= '<button type="button" rowid="'.$row->id.'" class="btnDeletelist btn btn-danger btn-sm">';
            $htmlTable .= '<i class="fas fa-trash-alt"></i>';
            $htmlTable .= '</button>';
            $htmlTable .= '</td>'; 
            $htmlTable .= '<td class="d-none">ExistingData</td>';
            $htmlTable .= '<td class="d-none"><input type="hidden" name="hiddenid" value="'.$row->id.'"></td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;
    }
   
    public function editlist(Request $request)
    {
        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('emp_task_allocation_details')
                ->select('emp_task_allocation_details.*')
                ->where('emp_task_allocation_details.id', $id)
                ->first(); 
            return response()->json(['result' => $data]);
        }
    }

    public function deletelist(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('task-allocation-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = $request->input('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpTaskAllocationDetail::findOrFail($id)->update($form_data);

        return response()->json(['success' => 'Employee Task Allocation successfully Deleted']);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('task-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            DB::beginTransaction();
            
            $current_date_time = Carbon::now()->toDateTimeString();
            $id = $request->hidden_id;

            $form_data = array(
                'date' => $request->date,
                'task_id' => $request->task,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            EmpTaskAllocation::findOrFail($id)->update($form_data);

            $tableData = $request->input('tableData');
        
            foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['col_1'];
            $empname = $rowtabledata['col_2'];
            $actionStatus = isset($rowtabledata['col_4']) ? $rowtabledata['col_4'] : 'NewData';
            
            if($actionStatus == "Updated" || $actionStatus == "ExistingData") {
                $detailID = null;
                if(isset($rowtabledata['col_5'])) {
                    preg_match('/value="(\d+)"/', $rowtabledata['col_5'], $matches);
                    if(isset($matches[1])) {
                        $detailID = $matches[1];
                    }
                }

                if($detailID) {
                    $EmpTaskAllocationDetail = EmpTaskAllocationDetail::find($detailID);
                    if($EmpTaskAllocationDetail) {
                        $EmpTaskAllocationDetail->allocation_id = $id;
                        $EmpTaskAllocationDetail->emp_id = $emp_id;
                        $EmpTaskAllocationDetail->date = $request->date;
                        $EmpTaskAllocationDetail->status = '1';
                        $EmpTaskAllocationDetail->updated_by = Auth::id();
                        $EmpTaskAllocationDetail->updated_at = $current_date_time;
                        $EmpTaskAllocationDetail->save();
                    }
                }
            } elseif($actionStatus == "NewData") {
                $EmpTaskAllocationDetail = new EmpTaskAllocationDetail();
                $EmpTaskAllocationDetail->allocation_id = $id;
                $EmpTaskAllocationDetail->emp_id = $emp_id;
                $EmpTaskAllocationDetail->date = $request->date;
                $EmpTaskAllocationDetail->status = '1';
                $EmpTaskAllocationDetail->created_by = Auth::id();
                $EmpTaskAllocationDetail->updated_by = '0';
                $EmpTaskAllocationDetail->created_at = $current_date_time;
                $EmpTaskAllocationDetail->updated_at = $current_date_time;
                $EmpTaskAllocationDetail->save();
            }
        }
            
            DB::commit();
            return response()->json(['success' => 'Employee Task Allocation Successfully Updated']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => ['An error occurred while updating data: ' . $e->getMessage()]], 422);
        }
    }

    public function view(Request $request)
    {
        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('emp_task_allocation as eta')
                ->leftJoin('task as p', 'eta.task_id', '=', 'p.id')
                ->select('eta.*','p.taskname')
                ->where('eta.id', $id)
                ->first(); 
            
            $requestlist = $this->view_reqestcountlist($id); 

            $responseData = array(
                'mainData' => $data,
                'requestdata' => $requestlist,
            );

            return response()->json(['result' => $responseData]);
        }
    }
    
    private function view_reqestcountlist($id)
    {
        $recordID = $id;
        $data = DB::table('emp_task_allocation_details as ead')
            ->leftJoin('employees as e', 'ead.emp_id', '=', 'e.emp_id')
            ->select(
                'ead.*', 
                'e.emp_name_with_initial as employee_name'
            )
            ->where('ead.allocation_id', $recordID)
            ->where('ead.status', 1)
            ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . ($row->employee_name ?? $row->employee_name) . '</td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('task-allocation-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
        $id = $request->input('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpTaskAllocation::findOrFail($id)->update($form_data);

        return response()->json(['success' => 'Employee Task Allocation Successfully Deleted']);
    }

    public function status($id, $statusid)
    {
        $user = Auth::user();
        $permission = $user->can('task-allocation-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        if($statusid == 1){
            $form_data = array(
                'status' => '1',
                'updated_by' => Auth::id(),
            );
            EmpTaskAllocation::findOrFail($id)->update($form_data);
    
            return redirect()->route('taskallocation');
        } else {
            $form_data = array(
                'status' => '2',
                'updated_by' => Auth::id(),
            );
            EmpTaskAllocation::findOrFail($id)->update($form_data);
    
            return redirect()->route('taskallocation');
        }
    }
}