<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\Machine;
use App\ProductionModule_Opma\MachineEmployee;
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

        $machines = Machine::orderBy('id', 'asc')->get();
        return view('Opma_Production.Daily_Production.machine', compact('machines'));
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
            'machine'    =>  'required'
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
            'description'    =>  $request->description
        );

        $machine=new Machine;
        $machine->company_id=$request->input('company');
        $machine->branch_id=$request->input('location');
        $machine->machine=$request->input('machine');
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
            
            $result = $data->toArray();
            $result['company_name'] = $data->company->name ?? '';
            $result['branch_name'] = $data->branch->location ?? '';
            
            return response()->json(['result' => $result]);
        }
    }

    public function update(Request $request)   
    {
        $user = auth()->user();
        $permission = $user->can('machine-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'company'    => 'required',
            'location'   => 'required',
            'machine'    => 'required'
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'company_id'  => $request->company,
            'branch_id'   => $request->location,
            'machine'     => $request->machine,
            'description' => $request->description
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

    public function getEmployees($id)
    {
        $employees = MachineEmployee::with('employee')
            ->where('opma_machine_id', $id)
            ->get()
            ->map(function ($me) {
                return [
                    'id'       => $me->id,
                    'emp_id'   => $me->employee->emp_id ?? $me->emp_id,
                    'emp_name' => $me->employee
                        ? $me->employee->emp_name_with_initial . ' - ' . $me->employee->calling_name : 'Unknown Employee',
                ];
            });

        return response()->json(['employees' => $employees]);
    }

    public function storeEmployees(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('machine-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = [
            'machine_id' => 'required',
            'employees'  => 'required|array',
        ];

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        foreach ($request->employees as $emp_id) {
            $exists = MachineEmployee::where('opma_machine_id', $request->machine_id)
                ->where('emp_id', $emp_id)
                ->exists();

            if (!$exists) {
                MachineEmployee::create([
                    'opma_machine_id' => $request->machine_id,
                    'emp_id'          => $emp_id,
                ]);
            }
        }

        return response()->json(['success' => 'Employees added successfully.']);
    }

    public function destroyEmployee($id)
    {
        $user = auth()->user();
        $permission = $user->can('machine-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        MachineEmployee::findOrFail($id)->delete();

        return response()->json(['success' => 'Employee removed successfully.']);
    }
}
