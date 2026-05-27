<?php

namespace App\Http\Controllers\Training_Management;

use App\Http\Controllers\Controller;
use App\Training_Management\TrainingAllocation;
use App\Training_Management\TrainingType;
use Illuminate\Http\Request;
use Validator;
use Datatables;
use DB;
use Carbon\Carbon;

class TrainingAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-list');
        if (!$permission) {
            abort(403);
        }

        $trainingtype = TrainingType::orderBy('id', 'asc')
            ->where('status', 1)
            ->get();

        return view('Training_Management.trainingAllocation', compact('trainingtype'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'training_name' => 'required',
            'date' => 'required',
            'venue' => 'required'
        );
        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $allocation = new TrainingAllocation;
        $allocation->training_name = $request->training_name;
        $allocation->venue = $request->venue;
        $allocation->date = $request->date;
        $allocation->status = 1;
        $allocation->created_by = auth()->user()->id;
        $allocation->save();

        if ($request->has('session_name')) {
            $sessions = [];
            foreach ($request->session_name as $i => $name) {
                $trainer = DB::table('employees')
                    ->where('id', $request->trainer_id[$i])
                    ->value('calling_name');
                $sessions[] = [
                    'allocation_id' => $allocation->id,
                    'session_name'  => $name,
                    'start_time'    => $request->start_time[$i],
                    'end_time'      => $request->end_time[$i],
                    'trainer_id'    => $request->trainer_id[$i],
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now()
                ];
            }
            DB::table('training_sessions')->insert($sessions);
        }

        return response()->json(['success' => 'Training Allocation Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = TrainingAllocation::findOrFail($id);
            $sessions = DB::table('training_sessions')
                ->leftJoin('employees', 'training_sessions.trainer_id', '=', 'employees.id')
                ->where('training_sessions.allocation_id', $id)
                ->select(
                    'training_sessions.*',
                    DB::raw("COALESCE(employees.calling_name, '') as trainer_name")
                )
                ->get();

            return response()->json(['result' => $data, 'sessions' => $sessions]);
        }
    }

    public function update(Request $request, TrainingAllocation $allocation)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $rules = array(
            'training_name' => 'required',
            'date' => 'required',
            'venue' => 'required'
        );
        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        TrainingAllocation::whereId($request->hidden_id)->update([
            'training_name' => $request->training_name,
            'date'          => $request->date,
            'venue'         => $request->venue,
            'updated_by'    => auth()->user()->id
        ]);

        DB::table('training_sessions')->where('allocation_id', $request->hidden_id)->delete();

        if ($request->has('session_name')) {
            $sessions = [];
            foreach ($request->session_name as $i => $name) {
                $sessions[] = [
                    'allocation_id' => $request->hidden_id,
                    'session_name'  => $name,
                    'start_time'    => $request->start_time[$i],
                    'end_time'      => $request->end_time[$i],
                    'trainer_id'    => $request->trainer_id[$i],
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now()
                ];
            }
            DB::table('training_sessions')->insert($sessions);
        }
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = TrainingAllocation::findOrFail($id);
        $data->status = 3;
        $data->save();
    }

    //Types modal get functionality

    public function getTypes($id)
    {
        $allTypes = TrainingType::where('status', 1)->orderBy('id', 'asc')->get();
        $selectedTypes = DB::table('training_types_map')
            ->where('allocation_id', $id)
            ->pluck('type_id');

        return response()->json([
            'allTypes' => $allTypes,
            'selectedTypes' => $selectedTypes
        ]);
    }

    //Types modal save functionality
    public function saveTypes(Request $request)
    {
        $id = $request->allocation_id;

        // Delete old, insert new (clean sync)
        DB::table('training_types_map')->where('allocation_id', $id)->delete();

        if ($request->has('type_ids')) {
            foreach ($request->type_ids as $type_id) {
                DB::table('training_types_map')->insert([
                    'allocation_id' => $id,
                    'type_id'       => $type_id,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now()
                ]);
            }
        }

        return response()->json(['success' => 'Types saved successfully.']);
    }

    public function getEmployees($id)
    {
        $employees = DB::table('training_emp_allocations')
            ->leftJoin('employees', 'training_emp_allocations.emp_id', '=', 'employees.emp_id')
            ->where('training_emp_allocations.allocation_id', $id)
            ->where('training_emp_allocations.status', 1)
            ->select(
                'training_emp_allocations.id',
                'training_emp_allocations.emp_id',
                DB::raw("CONCAT(COALESCE(training_emp_allocations.emp_id,''), ' - ', COALESCE(employees.calling_name,'')) as employee_display")
            )
            ->get();

        return response()->json($employees);
    }
}
