<?php

namespace App\Http\Controllers\ProductionEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\ProductionEmployee\ProductionDetail;
use Auth;
use Carbon\Carbon;
use Datatables;

class ProductionDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('production-detail-list');

        if(!$permission) {
            abort(403);
        }

        $production = ProductionDetail::orderBy('id', 'asc')->get();
        return view('ProductionEmployee.productionDetail', compact('production'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('production-detail-create');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $production = new ProductionDetail;
        $production->department_id = $request->input('department');
        $production->men_incentive = $request->input('men_incentive');
        $production->women_incentive = $request->input('women_incentive');
        $production->remark = $request->input('remark');
        $production->created_at = Carbon::now()->toDateTimeString();

        $production->save();

        return response()->json(['success' => 'Production Detail Added successfully.']);
    }

    public function readinglist()
    {
        $letters = DB::table('emp_production_details')
            ->leftjoin('departments', 'emp_production_details.department_id', '=', 'departments.id')
            ->leftjoin('companies', 'departments.company_id', '=', 'companies.id')
            ->select('emp_production_details.*', 'departments.name As department_name')
            ->get();
        
        return Datatables::of($letters)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if(Auth::user()->can('production-detail-edit')){
                    $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                }
                
                if(Auth::user()->can('production-detail-delete')){
                    $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('production-detail-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = DB::table('emp_production_details')
                ->leftjoin('departments', 'emp_production_details.department_id', '=', 'departments.id')
                ->leftjoin('companies', 'departments.company_id', '=', 'companies.id')
                ->select(
                    'emp_production_details.*', 
                    'departments.name as department_name',
                    'departments.company_id',
                    'companies.name as company_name'
                )
                ->where('emp_production_details.id', $id)
                ->first();
            
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, ProductionDetail $production)
    {
        $user = auth()->user();
        $permission = $user->can('production-detail-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $form_data = array(
            'department_id' => $request->input('department'),
            'men_incentive' => $request->input('men_incentive'),
            'women_incentive' => $request->input('women_incentive'),
            'remark' => $request->input('remark'),
            'updated_at' => Carbon::now()->toDateTimeString(),
        );

        ProductionDetail::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Production Detail is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('production-detail-delete');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = ProductionDetail::findOrFail($id);
        $data->delete();
        
        return response()->json(['success' => 'Deleted successfully']);
    }
}
