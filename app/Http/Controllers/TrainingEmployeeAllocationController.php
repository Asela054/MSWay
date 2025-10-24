<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\TrainingType;
use App\TrainingAllocation;
use App\TrainingEmpAllocation;
use App\Employee;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingEmployeeAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() 
    {
        $user = Auth::user();
        $permission = $user->can('trainingEmpAllocation-list');
        if(!$permission) {
            abort(403);
        }
        
        $allocations = TrainingAllocation::where('status', 1)->get();
        return view('Training_Management.trainingList', compact('allocations'));
    }

    public function show($allocation_id)
    {
        $user = Auth::user();
        $permission = $user->can('trainingEmpAllocation-list');
        if(!$permission) {
            abort(403);
        }

        $type = TrainingType::where('status', 1)
            ->where('id', $allocation_id)
            ->first();

        if (!$type) {
            abort(404, 'Training type not found');
        }

        $allocation = TrainingAllocation::where('type_id', $type->id)
            ->where('id', $allocation_id)
            ->where('status', 1)
            ->first();

        if (!$allocation) {
            abort(404, 'Training allocation not found');
        }

        $empallocation = TrainingEmpAllocation::where('allocation_id', $allocation->id)
            ->where('status', 1)
            ->get(); 
        
        return view('Training_Management.trEmployeeAllocation', compact('empallocation', 'allocation', 'type', 'allocation_id'));
    }
    
    public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('trainingEmpAllocation-create');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $empData = json_decode($request->input('empData'), true);
        
        if (empty($empData)) {
            return response()->json(['errors' => ['No employees selected']]);
        }

        $allocation_id = $request->input('detailsid');
        
        if (!$allocation_id) {
            return response()->json(['errors' => ['Allocation ID is required']]);
        }

        try {
            DB::beginTransaction();
            
            foreach ($empData as $emp) {
                $emp_id = $emp['col_1'];
                
                $existing = TrainingEmpAllocation::where('emp_id', $emp_id)
                    ->where('allocation_id', $allocation_id)
                    ->where('status', 1)
                    ->first();

                if (!$existing) {
                    $empallocation = new TrainingEmpAllocation();
                    $empallocation->allocation_id = $allocation_id;
                    $empallocation->emp_id = $emp_id;
                    $empallocation->status = 1; 
                    $empallocation->created_by = Auth::id();
                    $empallocation->save();
                }
            }
            
            DB::commit();
            return response()->json(['success' => 'Employees allocated successfully.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Failed to allocate employees: ' . $e->getMessage()]]);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('trainingEmpAllocation-delete');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $data = TrainingEmpAllocation::findOrFail($id);
            $data->status = 3;
            $data->save(); 
            return response()->json(['success' => 'Employee deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete employee'], 500);
        }
    }
}