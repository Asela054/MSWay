<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use App\ERP_KT\Machine;
use App\ERP_KT\MachineOperator;
use App\ERP_KT\MachineHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ERPMachineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-list');
        if (!$permission) {
            abort(403);
        }

        $machines = Machine::orderBy('id', 'asc')->get();
        
        return view('ERP_KT.machine', compact('machines'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'machine_name'    =>  'required',
            'helper_rate' => 'nullable|numeric',
            'operator_rate' => 'nullable|numeric',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'machine_name'=>$request->machine_name,
            'machine_type'=>$request->machine_type,
            'helper_rate'=>$request->helper_rate,
            'operator_rate'=>$request->operator_rate,
            'status'=>$request->status,
            'date'=>$request->date,
            'remarks'=>$request->remarks,
        );

        $machine=new Machine;
        $machine->machine_name=$request->input('machine_name');
        $machine->machine_type=$request->input('machine_type');
        $machine->helper_rate=$request->input('helper_rate');
        $machine->operator_rate=$request->input('operator_rate');
        $machine->status=$request->input('status');
        $machine->date=$request->input('date');
        $machine->remarks=$request->input('remarks');       
        $machine->save();

        return response()->json(['success' => 'Machine Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = Machine::findOrFail($id);

            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)   
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'machine_name'    =>  'required',
            'helper_rate' => 'nullable|numeric',
            'operator_rate' => 'nullable|numeric',
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'machine_name'=>$request->machine_name,
            'machine_type'=>$request->machine_type,
            'helper_rate'=>$request->helper_rate,
            'operator_rate'=>$request->operator_rate,
            'status'=>$request->status,
            'date'=>$request->date,
            'remarks'=>$request->remarks,
        );

        Machine::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Machine Data Updated Successfully ']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Machine::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Machine Deleted Successfully.']);
    }

    //Operator Assign

    public function getOperators($id)
    {
        $employees = MachineOperator::with('employee')
            ->where('machine_id', $id)
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

    public function storeOperators(Request $request)
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
            $exists = MachineOperator::where('machine_id', $request->machine_id)
                ->where('emp_id', $emp_id)
                ->exists();

            if (!$exists) {
                MachineOperator::create([
                    'machine_id' => $request->machine_id,
                    'emp_id'          => $emp_id,
                ]);
            }
        }

        return response()->json(['success' => 'Operators added successfully.']);
    }

    public function destroyOperator($id)
    {
        $user = auth()->user();
        $permission = $user->can('machine-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        MachineOperator::findOrFail($id)->delete();

        return response()->json(['success' => 'Operator removed successfully.']);
    }

    //Helper Assign

    public function getHelpers($id)
    {
        $employees = MachineHelper::with('employee')
            ->where('machine_id', $id)
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

    public function storeHelpers(Request $request)
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
            $exists = MachineHelper::where('machine_id', $request->machine_id)
                ->where('emp_id', $emp_id)
                ->exists();

            if (!$exists) {
                MachineHelper::create([
                    'machine_id' => $request->machine_id,
                    'emp_id'          => $emp_id,
                ]);
            }
        }

        return response()->json(['success' => 'Helpers added successfully.']);
    }

    public function destroyHelper($id)
    {
        $user = auth()->user();
        $permission = $user->can('machine-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        MachineHelper::findOrFail($id)->delete();

        return response()->json(['success' => 'Helper removed successfully.']);
    }
}

