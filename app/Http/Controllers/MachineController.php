<?php

namespace App\Http\Controllers;

use App\Machine;
use Illuminate\Http\Request;
use Validator;

class MachineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('machine-list');
        if (!$permission) {
            abort(403);
        }

        $machine= Machine::orderBy('id', 'asc')->get();
        return view('Daily_Production.machine',compact('machine'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('machine-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'company'    =>  'required',
            'location'   =>  'required',
            'machine'    =>  'required',
            'semi_complete' => 'required',
            'full_complete' => 'required'
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'company_id'     =>  $request->company,
            'branch_id'      =>  $request->location,
            'machine'        =>  $request->machine,
            'semi_complete'  =>  $request->semi_complete,
            'full_complete'  =>  $request->full_complete,
            'target_count'   =>  $request->target_count,
            'description'    =>  $request->description
        );

        $machine=new Machine;
        $machine->company_id=$request->input('company');
        $machine->branch_id=$request->input('location');
        $machine->machine=$request->input('machine');
        $machine->semi_complete=$request->input('semi_complete');
        $machine->full_complete=$request->input('full_complete');
        $machine->target_count = $request->input('target_count') ?? 0;
        $machine->description=$request->input('description');       
        $machine->status=1;
        $machine->save();

        return response()->json(['success' => 'Machine Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('machine-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = Machine::with(['company:id,name', 'branch:id,location'])
                        ->findOrFail($id);
            
            // Add company and branch names to the response
            $result = $data->toArray();
            $result['company_name'] = $data->company->name ?? '';
            $result['branch_name'] = $data->branch->location ?? '';
            
            return response()->json(['result' => $result]);
        }
    }

    public function update(Request $request, Machine $machine)
    {
        $user = auth()->user();
        $permission = $user->can('machine-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'company'    =>  'required',
            'location'   =>  'required',
            'machine'    =>  'required',
            'semi_complete' => 'required',
            'full_complete' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'company_id'     =>  $request->company,
            'branch_id'      =>  $request->location,
            'machine'    =>  $request->machine,
            'semi_complete'  =>  $request->semi_complete,
            'full_complete'  =>  $request->full_complete,
            'target_count'   =>  $request->target_count,
            'description' =>  $request->description
        );

        Machine::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('machine-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Machine::findOrFail($id);
        $data->status=3;
        $data->save();

        return response()->json(['success' => 'Machine Deleted Successfully.']);
    }
}
