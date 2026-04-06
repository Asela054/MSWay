<?php

namespace App\Http\Controllers\Production_Module_Opma;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\ProductionModule_Opma\ProductionAmount;
use Auth;
use Carbon\Carbon;
use Datatables;

class ProductionAmountController extends Controller
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

        $titles = DB::table('job_titles')->get();
        $departments = DB::table('departments')->get();

        return view('Opma_Production.Daily_Production.productionAmount', compact('titles', 'departments'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-create');

        if (!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $amount = new ProductionAmount;
        $amount->department_id = $request->input('department');
        $amount->jobtitle = $request->input('jobtitle');
        $amount->end_precentage = $request->input('end_precentage');
        $amount->amount = $request->input('amount');
        $amount->save();

        return response()->json(['success' => 'Production Amount Added successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');

        if (!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = DB::table('opma_production_amount')
            ->leftJoin('departments', 'opma_production_amount.department_id', '=', 'departments.id')
            ->leftJoin('job_titles', 'opma_production_amount.jobtitle', '=', 'job_titles.id')
            ->select(
                'opma_production_amount.id',
                'opma_production_amount.department_id as department_id',
                'departments.name as dept_name',
                'opma_production_amount.jobtitle as job_title_id',
                'job_titles.title as job_title',
                'opma_production_amount.end_precentage',
                'opma_production_amount.amount'
            )
            ->where('opma_production_amount.id', $id)
            ->first();

        return response()->json(['result' => $data]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');

        if (!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $form_data = [
            'department_id'  => $request->department,
            'jobtitle'       => $request->jobtitle,
            'end_precentage' => $request->end_precentage,
            'amount'         => $request->amount,
        ];

        ProductionAmount::where('id', $request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Production Amount is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-delete');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = ProductionAmount::findOrFail($id);
        $data->delete();
        
        return response()->json(['success' => 'Deleted successfully']);
    }
}
