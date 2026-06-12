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

class TrainingSummaryController extends Controller
{
    public function train_summary()
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

            return view('Training_Management.trainingSummary');
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

    // Load summary details for a employee
    public function get_summary_types(Request $request)
    {
        $allocation_id = $request->get('allocation_id');
        $emp_id        = $request->get('emp_id');

        $types = DB::table('training_types_map as ttm')
            ->join('training_types as tt', 'tt.id', '=', 'ttm.type_id')
            ->where('ttm.allocation_id', $allocation_id)
            ->select('tt.id as type_id', 'tt.name as type_name')
            ->get();

        $sessions = DB::table('training_sessions')
            ->where('allocation_id', $allocation_id)
            ->select('id as session_id', 'session_name')
            ->orderBy('id')
            ->get();

        $pointsRaw = DB::table('training_defect_points')
            ->where('allocation_id', $allocation_id)
            ->where('emp_id', $emp_id)
            ->select('session_id', 'type_id', 'points', 'is_attend')
            ->get()
            ->groupBy(function ($item) {
                return $item->session_id;
            });

        // Employee info: name, emp_pic, department (for print function)
        $employeeInfo = DB::table('employees as e')
            ->leftJoin('employee_pictures as ep', 'ep.emp_id', '=', 'e.id')
            ->leftJoin('departments as d', 'd.id', '=', 'e.emp_department')
            ->where('e.emp_id', $emp_id)
            ->select(
                'e.emp_name_with_initial',
                'e.calling_name',
                'ep.emp_pic_filename',
                'd.name as department_name'
            )
            ->first();

        $allocationDate = DB::table('training_allocations')
            ->where('id', $allocation_id)
            ->value('date');

        return response()->json([
            'types'         => $types,
            'sessions'      => $sessions,
            'points'        => $pointsRaw,
            'employee'      => $employeeInfo,
            'training_date' => $allocationDate,
        ]);
    }

    //summary list with details
    public function training_summary_list(Request $request)
    {
        $allocation_id = $request->get('allocation_id');
        $from_date     = $request->get('from_date');
        $to_date       = $request->get('to_date');
        $draw          = $request->get('draw');
        $start         = $request->get('start');
        $length        = $request->get('length');

        $query = DB::table('training_emp_allocations as tea')
            ->join('employees as e',             'e.emp_id', '=', 'tea.emp_id')
            ->join('training_allocations as ta', 'ta.id',    '=', 'tea.allocation_id')
            ->where('tea.status', 1)
            ->select(
                'tea.id',
                'tea.emp_id',
                'e.emp_name_with_initial',
                'e.calling_name',
                'tea.allocation_id',
                DB::raw('ta.date as date')
            );

        $query->where('tea.allocation_id', $allocation_id);
        if (!empty($from_date))     $query->whereDate('ta.date', '>=', $from_date);
        if (!empty($to_date))       $query->whereDate('ta.date', '<=', $to_date);

        $total   = $query->count();
        $records = (clone $query)->skip($start)->take($length)->get();

        $data = $records->map(function ($r) {
            return [
                $r->id,
                $r->emp_id,
                $r->emp_name_with_initial . ' ' . $r->calling_name,
                $r->date,
                '<button class="btn btn-info btn-sm open-types-modal mr-1" data-toggle="tooltip" title="View" 
                    data-allocation="' . $r->allocation_id . '" data-employee="' . $r->emp_id . '"><i class="fas fa-eye"></i></button>
                <button class="btn btn-primary btn-sm print-row" data-toggle="tooltip" title="Print" 
                    data-allocation="' . $r->allocation_id . '" data-employee="' . $r->emp_id . '"><i class="fas fa-print mr-1"></i> Print</button>',
            ];
        });

        return response()->json([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data->values()->toArray(),
        ]);
    }
}
