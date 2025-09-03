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
            'machine'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'machine'        =>  $request->machine,
            'description'    =>  $request->description
        );

        $machine=new Machine;
        $machine->machine=$request->input('machine');
        $machine->description=$request->input('description');       
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
            $data = Machine::findOrFail($id);
            return response()->json(['result' => $data]);
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
            'machine'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'machine'    =>  $request->machine,
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
        $data->delete();
    }
}
