<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\MealAllowance;
use Auth;
use Carbon\Carbon;
use Datatables;

class MealAllowanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('MealAllowance-list');

        if(!$permission) {
            abort(403);
        }

        $mealallowance = MealAllowance::orderBy('id', 'asc')->get();
        $job_categories=DB::table('job_categories')->select('*')->get();
        $remunerations=DB::table('remunerations')->select('*')->get();
        return view('Organization.meal_allowance', compact('mealallowance','job_categories','remunerations'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('MealAllowance-create');


        $mealallowance = new MealAllowance;
        $mealallowance->job_id = $request->input('job_category');
        $mealallowance->remuneration_id = $request->input('remuneration_name');
        $mealallowance->allowance_type = $request->input('allowance_type');
        $mealallowance->amount = $request->input('amount');
        $mealallowance->approved_status = 0;
        $mealallowance->created_by = Auth::id();
        $mealallowance->created_at = Carbon::now()->toDateTimeString();

        $mealallowance->save();

        return response()->json(['success' => 'Meal Allowance Added successfully.']);
    }

    public function letterlist ()
    {
        $letters = DB::table('meal_allowances')
        ->leftjoin('job_categories', 'meal_allowances.job_id', '=', 'job_categories.id')
        ->leftjoin('remunerations', 'meal_allowances.remuneration_id', '=', 'remunerations.id')
        ->select('meal_allowances.*','job_categories.category As category','remunerations.remuneration_name As remuneration_name')
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('MealAllowance-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('MealAllowance-approval')){
                        if($row->approved_status == 0){
                                $btn .= ' <button name="approve" id="'.$row->id.'" class="approve btn btn-outline-success btn-sm mr-1"><i class="fas fa-level-up-alt"></i></button>';
                            }
                    }
                    if(Auth::user()->can('MealAllowance-delete')){
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
        $permission = $user->can('MealAllowance-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = MealAllowance::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, MealAllowance $mealallowance)
    {
        $user = auth()->user();
        $permission = $user->can('MealAllowance-edit');

        $form_data = array(
            'job_id' => $request->job_category,
            'remuneration_id' => $request->remuneration_name,
            'allowance_type' => $request->allowance_type,
            'amount' => $request->amount,
            'approved_status' => 0,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        );

        MealAllowance::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Meal Allowance is successfully updated']);
    }

    public function approve_update(Request $request, MealAllowance $mealallowance)
    {
        $user = auth()->user();
        $permission = $user->can('MealAllowance-approval');

        $form_data = array(
            'approved_status' => 1,
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now()->toDateTimeString(),
        );

        MealAllowance::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Meal Allowance is successfully approved']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('MealAllowance-delete');

        $data = MealAllowance::findOrFail($id);
        $data->delete();
    }

    
}
