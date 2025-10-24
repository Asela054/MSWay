<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use App\TrainingType;
use App\TrainingAllocation;
use App\TrainingEmpAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateTime;

class TrainingAttendanceController extends Controller
{
    public function train_attendance()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                abort(403, 'User not authenticated');
            }
            
            $permission = $user->can('trainingAttendance-create');
            if (!$permission) {
                abort(403, 'Unauthorized access');
            }
            
            return view('Training_Management.trainingAttendance');
        } catch (\Exception $e) {
            \Log::error('Training Attendance Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading the page.');
        }
    }

    public function train_Attendance_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('trainingAttendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $type = $request->get('type');
        $venue = $request->get('venue');
        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        
        if (empty($type) && empty($venue) && empty($employee) && empty($from_date) && empty($to_date)) {
            return response()->json([
                'draw' => intval($draw),
                'iTotalRecords' => 0,
                'iTotalDisplayRecords' => 0,
                'aaData' => []
            ]);
        }

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'] ?? 0;
        $columnName = $columnName_arr[$columnIndex]['data'] ?? 'id';
        $columnSortOrder = $order_arr[0]['dir'] ?? 'desc';
        $searchValue = $search_arr['value'] ?? '';

        // Base query
        $baseQuery = DB::table('training_emp_allocations as tea')
            ->select(
                'tea.id',
                'tea.emp_id',
                'tea.allocation_id',
                'tea.is_attend',
                'employees.emp_id as uid',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'tt.name as type',
                'ta.venue',
                'ta.start_time',
                'ta.end_time'
            )
            ->join('employees', 'employees.emp_id', '=', 'tea.emp_id')
            ->join('training_allocations as ta', 'ta.id', '=', 'tea.allocation_id')
            ->join('training_types as tt', 'tt.id', '=', 'ta.type_id')
            ->where('tea.status', 1);

        // Apply filters
        if ($searchValue) {
            $baseQuery->where(function($q) use ($searchValue) {
                $q->where('employees.emp_id', 'like', "%$searchValue%")
                ->orWhere('employees.emp_name_with_initial', 'like', "%$searchValue%")
                ->orWhere('employees.calling_name', 'like', "%$searchValue%")
                ->orWhere('tt.name', 'like', "%$searchValue%")
                ->orWhere('ta.venue', 'like', "%$searchValue%");
            });
        }

        if ($type) {
            $baseQuery->where('ta.type_id', $type);
        }
        if ($venue) {
            $baseQuery->where('ta.id', $venue);
        }
        if ($employee) {
            $baseQuery->where('tea.emp_id', $employee);
        }
        if ($from_date && $to_date) {
            $baseQuery->whereBetween('ta.start_time', [$from_date, $to_date]);
        } elseif ($from_date) {
            $baseQuery->where('ta.start_time', '>=', $from_date);
        } elseif ($to_date) {
            $baseQuery->where('ta.start_time', '<=', $to_date);
        }

        $totalRecords = $baseQuery->count();
        $filteredQuery = clone $baseQuery;
        $totalFiltered = $filteredQuery->count();

        $filteredQuery->orderBy($columnName, $columnSortOrder);
        $records = $filteredQuery->skip($start)->take($rowperpage)->get();

        $data = [];

        foreach ($records as $record) {
            $employeeObj = (object)[
                'emp_id' => $record->uid,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'calling_name' => $record->calling_name
            ];

            $data[] = [
                'id' => $record->id,
                'uid' => $record->uid,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'employee_display' => EmployeeHelper::getDisplayName($employeeObj),
                'type' => $record->type,
                'venue' => $record->venue,
                'start_time' => Carbon::parse($record->start_time)->format('Y-m-d H:i'),
                'end_time' => Carbon::parse($record->end_time)->format('Y-m-d H:i'),
                'is_attend' => $record->is_attend,
                'action' => '<button type="button" name="edit" id="'.$record->id.'" class="edit btn btn-primary btn-sm" title="Add Marks"><i class="fas fa-clipboard-check"></i></button>'
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data
        ]);
    }



    public function train_Attendance_mark(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('trainingAttendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $selected_cb = $request->selected_cb;

        if (empty($selected_cb)) {
            return response()->json(['status' => false, 'msg' => 'Select one or more employees']);
        }

        foreach ($selected_cb as $cr) {
            $id = $cr['id'];
            
            $data = TrainingEmpAllocation::find($id);
            if ($data) {
                $data->is_attend = 1;
                $data->updated_by = $user->id;
                $data->save();
            }
        }
        
        return response()->json(['status' => true, 'msg' => 'Attendance marked successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAttendance-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = TrainingEmpAllocation::findOrFail($id);
        return response()->json(['result' => $data]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAttendance-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $form_data = array(
                'marks'    =>  $request->marks,
                'remarks'  =>  $request->remarks,
                'updated_by' => $user->id
            );
            
            TrainingEmpAllocation::whereId($request->hidden_id)->update($form_data);

            return response()->json(['success' => 'Data is successfully updated']);
        } catch (\Exception $e) {
            \Log::error('Training Mark Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update marks'], 500);
        }
    }

}
