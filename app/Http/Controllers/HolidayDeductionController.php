<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\HolidayDeduction;
use Auth;
use Carbon\Carbon;
use Datatables;

class HolidayDeductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('Holiday-Deduction-list');

        if(!$permission) {
            abort(403);
        }

        $holidaydeduction = HolidayDeduction::orderBy('id', 'asc')->get();
        $job_categories=DB::table('job_categories')->select('*')->get();
        $remunerations=DB::table('remunerations')->select('*')->get();
        return view('Holiday.holiday_deduction', compact('holidaydeduction','job_categories','remunerations'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('Holiday-Deduction-create');


        $holidaydeduction = new HolidayDeduction;
        $holidaydeduction->job_id = $request->input('job_category');
        $holidaydeduction->remuneration_id = $request->input('remuneration_name');
        $holidaydeduction->day_count = $request->input('day_count');
        $holidaydeduction->amount = $request->input('amount');
        $holidaydeduction->created_at = Carbon::now()->toDateTimeString();

        $holidaydeduction->save();

        return response()->json(['success' => 'Holiday Deduction Added successfully.']);
    }

    public function letterlist ()
    {
        $letters = DB::table('holiday_deductions')
        ->leftjoin('job_categories', 'holiday_deductions.job_id', '=', 'job_categories.id')
        ->leftjoin('remunerations', 'holiday_deductions.remuneration_id', '=', 'remunerations.id')
        ->select('holiday_deductions.*','job_categories.category As category','remunerations.remuneration_name As remuneration_name')
        ->get();
        return Datatables::of($letters)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Holiday-Deduction-edit')){
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    
                    if(Auth::user()->can('Holiday-Deduction-delete')){
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
        $permission = $user->can('Holiday-Deduction-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = HolidayDeduction::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, HolidayDeduction $holidaydeduction)
    {
        $user = auth()->user();
        $permission = $user->can('Holiday-Deduction-edit');

        $form_data = array(
            'job_id' => $request->job_category,
            'remuneration_id' => $request->remuneration_name,
            'day_count' => $request->day_count,
            'amount' => $request->amount,
            'updated_at' => Carbon::now()->toDateTimeString(),
        );

        HolidayDeduction::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Holiday Deduction is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('Holiday-Deduction-delete');

        $data = HolidayDeduction::findOrFail($id);
        $data->delete();
    }
}
