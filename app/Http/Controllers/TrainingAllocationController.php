<?php

namespace App\Http\Controllers;

use App\TrainingAllocation;
use App\TrainingType;
use Illuminate\Http\Request;
use Validator;

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
            ->where('status',1)
            ->get();

        $allocation= TrainingAllocation::orderBy('id', 'asc')
            ->leftjoin('training_types', 'training_allocations.type_id', '=', 'training_types.id')
            ->select('training_allocations.*', 'training_types.name as training_type')
            ->where('training_allocations.status',1)
            ->get();

        return view('Training_Management.trainingAllocation',compact('allocation', 'trainingtype'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'type'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'type'   =>  $request->type,
            'venue'   =>  $request->venue,
            'start_time'   =>  $request->start_time,
            'end_time'   =>  $request->end_time
        );

        $allocation = new TrainingAllocation;
        $allocation->type_id = $request->type;
        $allocation->venue = $request->venue;
        $allocation->start_time = $request->start_time;
        $allocation->end_time = $request->end_time;
        $allocation->status = 1;
        $allocation->created_by = auth()->user()->id;
        $allocation->save();

        return response()->json(['success' => 'Training Allocation Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = TrainingAllocation::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, TrainingAllocation $allocation)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $rules = array(
            'type'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'type_id'   =>  $request->type,
            'venue'   =>  $request->venue,
            'start_time'   =>  $request->start_time,
            'end_time'   =>  $request->end_time,
            'updated_by'   =>  auth()->user()->id
        );

        TrainingAllocation::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingAllocation-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = TrainingAllocation::findOrFail($id);
        $data->status = 3;
        $data->save();
    }
}
