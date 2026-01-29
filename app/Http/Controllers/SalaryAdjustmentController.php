<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\SalaryAdjustment;
use Carbon\Carbon;
use Datatables;

class SalaryAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('SalaryAdjustment-list');

        if(!$permission) {
            abort(403);
        }

        $salaryadjustment = SalaryAdjustment::orderBy('id', 'asc')->get();
        $job_categories=DB::table('job_categories')->select('*')->get();
        $remunerations=DB::table('remunerations')->select('*')->where('remuneration_cancel', 0)->where('allocation_method', 'TERMS')->get();
        return view('Organization.salary_adjustment', compact('salaryadjustment','job_categories','remunerations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('SalaryAdjustment-create');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to create salary adjustment.')]);
        }


        $salaryadjustment = new SalaryAdjustment;
        $salaryadjustment->emp_id = $request->input('employee' , 0);
        $salaryadjustment->job_id = $request->input('job_category' , 0);
        $salaryadjustment->remuneration_id = $request->input('remuneration_name');
        $salaryadjustment->adjustment_type = $request->input('adjustment_type');
        $salaryadjustment->allowance_type = $request->input('allowance_type');
        $salaryadjustment->amount = $request->input('amount');
        $salaryadjustment->allowleave = $request->input('allowleave');
        $salaryadjustment->approved_status = 0;
        $salaryadjustment->created_by = Auth::id();
        $salaryadjustment->created_at = Carbon::now()->toDateTimeString();

        $salaryadjustment->save();

        return response()->json(['success' => 'Salary Adjustment Added successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('SalaryAdjustment-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = SalaryAdjustment::leftJoin('employees', 'salary_adjustments.emp_id', '=', 'employees.emp_id')
                ->where('salary_adjustments.id', $id)
                ->select('salary_adjustments.*', 'employees.emp_name_with_initial')
                ->first();
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, SalaryAdjustment $salaryadjustment)
    {
        $user = Auth::user();
        $permission = $user->can('SalaryAdjustment-edit');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to update salary adjustment.')]);
        }

        $form_data = array(
            'emp_id' => $request->employee,
            'job_id' => $request->job_category,
            'remuneration_id' => $request->remuneration_name,
            'adjustment_type' => $request->adjustment_type,
            'allowance_type' => $request->allowance_type,
            'amount' => $request->amount,
            'allowleave' => $request->allowleave,
            'approved_status' => 0,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        );

        SalaryAdjustment::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Salary Adjustment is successfully updated']);
    }

    public function approve_update(Request $request, SalaryAdjustment $salaryadjustment)
    {
        $user = auth()->user();
        $permission = $user->can('SalaryAdjustment-approval');

        $form_data = array(
            'approved_status' => 1,
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now()->toDateTimeString(),
        );

        SalaryAdjustment::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Salary Adjustment is successfully approved']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('SalaryAdjustment-delete');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to remove salary adjustment.')]);
        }

        $data = SalaryAdjustment::findOrFail($id);
        $data->delete();
    }

    
}
