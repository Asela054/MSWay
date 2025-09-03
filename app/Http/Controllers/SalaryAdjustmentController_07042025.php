<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\SalaryAdjustment;
use Auth;
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
        $remunerations=DB::table('remunerations')->select('*')->where('allocation_method', 'TERMS')->get();
        $employees=DB::table('employees')->select('emp_id','emp_name_with_initial')->where('deleted',0)->get();
        return view('Organization.salary_adjustment', compact('salaryadjustment','job_categories','remunerations','employees'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('SalaryAdjustment-create');


        $salaryadjustment = new SalaryAdjustment;
        $salaryadjustment->emp_id = $request->input('employee');
        $salaryadjustment->job_id = $request->input('job_category');
        $salaryadjustment->remuneration_id = $request->input('remuneration_name');
        $salaryadjustment->allowance_type = $request->input('allowance_type');
        $salaryadjustment->amount = $request->input('amount');
        $salaryadjustment->approved_status = 0;
        $salaryadjustment->created_by = Auth::id();
        $salaryadjustment->created_at = Carbon::now()->toDateTimeString();

        $salaryadjustment->save();

        return response()->json(['success' => 'Salary Adjustment Added successfully.']);
    }

    public function letterlist ()
    {
        $letters = DB::table('salary_adjustments')
        ->leftjoin('job_categories', 'salary_adjustments.job_id', '=', 'job_categories.id')
        ->leftjoin('remunerations', 'salary_adjustments.remuneration_id', '=', 'remunerations.id')
        ->leftjoin('employees', 'salary_adjustments.emp_id', '=', 'employees.emp_id')
        ->select('salary_adjustments.*','job_categories.category As category','remunerations.remuneration_name As remuneration_name',
                    DB::raw("IF(salary_adjustments.emp_id = 0, 'All Employees', employees.emp_name_with_initial) AS employee") )
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('SalaryAdjustment-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('SalaryAdjustment-approval')){
                        if($row->approved_status == 0){
                                $btn .= ' <button name="approve" id="'.$row->id.'" class="approve btn btn-outline-success btn-sm mr-1"><i class="fas fa-level-up-alt"></i></button>';
                            }
                    }
                    if(Auth::user()->can('SalaryAdjustment-delete')){
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }        
            return $btn;
        })
       
        ->rawColumns(['action'])
        ->make(true);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('SalaryAdjustment-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = SalaryAdjustment::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, SalaryAdjustment $salaryadjustment)
    {
        $user = auth()->user();
        $permission = $user->can('SalaryAdjustment-edit');

        $form_data = array(
            'emp_id' => $request->employee,
            'job_id' => $request->job_category,
            'remuneration_id' => $request->remuneration_name,
            'allowance_type' => $request->allowance_type,
            'amount' => $request->amount,
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
        $user = auth()->user();
        $permission = $user->can('SalaryAdjustment-delete');

        $data = SalaryAdjustment::findOrFail($id);
        $data->delete();
    }

    
}
