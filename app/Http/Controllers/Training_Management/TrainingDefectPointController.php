<?php

namespace App\Http\Controllers\Training_Management;

use App\Http\Controllers\Controller;
use App\Helpers\EmployeeHelper;
use App\Training_Management\TrainingDefectPoint;
use App\Training_Management\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;

class TrainingDefectPointController extends Controller
{
    public function train_defect_point()
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

            return view('Training_Management.trainingDefectPoint');
        } catch (\Exception $e) {
            \Log::error('Training Attendance Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading the page.');
        }
    }
    //allocation dropdown
    public function get_allocations()
    {
        $allocations = DB::table('training_allocations')
            ->where('status', 1)
            ->select('id', 'training_name')
            ->get();
        return response()->json($allocations);
    }

    public function get_employees_by_allocation(Request $request)
    {
        $allocation_id = $request->get('allocation_id');
        $employees = DB::table('training_emp_allocations as tea')
            ->join('employees', 'employees.emp_id', '=', 'tea.emp_id')
            ->where('tea.allocation_id', $allocation_id)
            ->select('employees.emp_id as id', DB::raw("CONCAT(employees.emp_name_with_initial, ' ', employees.calling_name) as text"))
            ->get();
        return response()->json(['results' => $employees]);
    }

    public function train_defect_point_list(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('trainingAttendance-create')) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $employee      = $request->get('employee');
        $allocation_id = $request->get('allocation_id');
        $draw          = $request->get('draw');
        $start         = $request->get('start');
        $rowperpage    = $request->get('length');

        if (empty($employee) || empty($allocation_id)) {
            return response()->json([
                'draw'            => intval($draw),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => []
            ]);
        }

        $baseQuery = DB::table('training_sessions as ts')
            ->select(
                'ts.id as session_id',
                'ts.session_name',
                'ta.id as allocation_id',
                'ta.training_name'
            )
            ->join('training_allocations as ta', 'ta.id', '=', 'ts.allocation_id')
            ->join('training_emp_allocations as tea', function ($join) use ($employee, $allocation_id) {
                $join->on('tea.allocation_id', '=', 'ta.id')
                    ->where('tea.emp_id', $employee)
                    ->where('tea.status', 1);
            })
            ->where('ts.allocation_id', $allocation_id)
            ->distinct();

        $totalRecords  = $baseQuery->count('ts.id');
        $totalFiltered = $totalRecords;

        $records = (clone $baseQuery)->skip($start)->take($rowperpage)->get();

        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'session_id'    => $record->session_id,
                'session_name'  => $record->session_name,
                'allocation_id' => $record->allocation_id,
                'training_name' => $record->training_name,
                'action'        =>
                '<button type="button" class="btn btn-primary btn-sm open-types-modal"
                    data-session="'    . $record->session_id    . '"
                    data-allocation="' . $record->allocation_id . '"
                    data-employee="'   . $employee              . '"
                    title="View Points">
                    <i class="fas fa-clipboard-check mr-2"></i> Points
                </button>'
            ];
        }

        return response()->json([
            'draw'            => intval($draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data
        ]);
    }

    public function train_defect_point_mark(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('trainingAttendance-create')) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $allPoints = $request->allPoints;
        if (empty($allPoints)) {
            return response()->json(['status' => false, 'msg' => 'No data to save']);
        }

        foreach ($allPoints as $row) {
            $existing = DB::table('training_defect_points')
                ->where('allocation_id', $row['allocation_id'])
                ->where('session_id',    $row['session_id'])
                ->where('type_id',       $row['type_id'])
                ->where('emp_id',        $row['emp_id'])
                ->first();

            $data = [
                'points'     => $row['points'],
                'is_attend'  => $row['is_attend'],
                'updated_at' => Carbon::now()
            ];

            if ($existing) {
                DB::table('training_defect_points')->where('id', $existing->id)->update($data);
            } else {
                DB::table('training_defect_points')->insert(array_merge($data, [
                    'allocation_id' => $row['allocation_id'],
                    'session_id'    => $row['session_id'],
                    'type_id'       => $row['type_id'],
                    'emp_id'        => $row['emp_id'],
                    'created_at'    => Carbon::now()
                ]));
            }
        }

        return response()->json(['success' => 'Points saved successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAttendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = DB::table('training_defect_points')->where('id', $id)->first();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAttendance-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = [
            'points'     => $request->points,
            'updated_at' => Carbon::now()
        ];

        $data = [
            'points'     => $request->points,
            'updated_at' => Carbon::now()
        ];

        if ($request->hidden_id) {
            DB::table('training_defect_points')
                ->where('id', $request->hidden_id)
                ->update($data);
        } else {
            DB::table('training_defect_points')->insert(array_merge($data, [
                'allocation_id' => $request->allocation_id,
                'session_id'    => $request->session_id,
                'type_id'       => $request->type_id,
                'emp_id'        => $request->emp_id,
                'created_at'    => Carbon::now()
            ]));
        }

        return response()->json(['success' => 'Points updated successfully.']);
    }

    //load types for the modal
    public function get_session_types(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('trainingAttendance-create')) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $session_id    = $request->get('session_id');
        $allocation_id = $request->get('allocation_id');
        $emp_id        = $request->get('emp_id');

        $types = DB::table('training_types_map as ttm')
            ->join('training_types as tt', 'tt.id', '=', 'ttm.type_id')
            ->leftJoin('training_defect_points as tdp', function ($join) use ($session_id, $emp_id) {
                $join->on('tdp.type_id', '=', 'ttm.type_id')
                    ->on('tdp.allocation_id', '=', 'ttm.allocation_id')
                    ->where('tdp.session_id', $session_id)
                    ->where('tdp.emp_id', $emp_id);
            })
            ->where('ttm.allocation_id', $allocation_id)
            ->select(
                'tt.id as type_id',
                'tt.name as type_name',
                'tdp.id as defect_point_id',
                'tdp.points',
                'tdp.is_attend'
            )
            ->get();

        return response()->json(['types' => $types]);
    }
}
