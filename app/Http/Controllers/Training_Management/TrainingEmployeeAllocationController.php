<?php

namespace App\Http\Controllers\Training_Management;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Training_Management\TrainingEmpAllocation;
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

    public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('trainingEmpAllocation-create');
        if (!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $empData   = json_decode($request->input('empData', '[]'), true) ?? [];
        $removedIds = json_decode($request->input('removedIds', '[]'), true) ?? [];

        $allocation_id = $request->input('detailsid');

        if (!$allocation_id) {
            return response()->json(['errors' => ['Allocation ID is required']]);
        }

        try {
            DB::beginTransaction();

            // Removed(Status = 3) employees
            foreach ($removedIds as $rid) {
                TrainingEmpAllocation::where('id', $rid)->update(['status' => 3]);
            }

            // Insert new employees
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
                    $empallocation->save();
                }
            }

            DB::commit();
            return response()->json(['success' => 'Employees updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Failed to update employees: ' . $e->getMessage()]]);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('trainingEmpAllocation-delete');
        if (!$permission) {
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
