<?php

namespace App\Http\Controllers\Production_Module_Opma;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\ProductionModule_Opma\EmployeePerformance;
use Auth;
use Carbon\Carbon;
use Datatables;

class EmployeePerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('product-list');

        if(!$permission) {
            abort(403);
        }

        return view('Opma_Production.Daily_Production.employeePerformance');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-create');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $performance = new EmployeePerformance;
        $performance->emp_id = $request->input('employee');
        $performance->amount = $request->input('amount');
        $performance->created_at = Carbon::now()->toDateTimeString();

        $performance->save();

        return response()->json(['success' => 'Employee Performance Added successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = DB::table('opma_performance_amount')
                ->leftjoin('employees', 'opma_performance_amount.emp_id', '=', 'employees.emp_id')
                ->select(
                    'opma_performance_amount.*', 
                    'employees.emp_name_with_initial as employee_name'
                )
                ->where('opma_performance_amount.id', $id)
                ->first();
            
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, EmployeePerformance $performance)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $form_data = array(
            'emp_id' => $request->employee,
            'amount' => $request->amount,
            'updated_at' => Carbon::now()->toDateTimeString()
        );

        EmployeePerformance::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Employee Performance is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-delete');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = EmployeePerformance::findOrFail($id);
        $data->delete();
        
        return response()->json(['success' => 'Deleted successfully']);
    }
}
