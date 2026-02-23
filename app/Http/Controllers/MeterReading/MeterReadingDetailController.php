<?php

namespace App\Http\Controllers\MeterReading;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\MeterReading\MeterReadingDetail;
use Auth;
use Carbon\Carbon;
use Datatables;

class MeterReadingDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('meter-reading-list');

        if(!$permission) {
            abort(403);
        }

        $meterReading = MeterReadingDetail::orderBy('id', 'asc')->get();
        return view('Meter_Reading.meter_reading', compact('meterReading'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('meter-reading-create');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $meterReading = new MeterReadingDetail;
        $meterReading->department_id = $request->input('department');
        $meterReading->reading_limit = $request->input('reading_limit');
        $meterReading->multiple_value = $request->input('multiple_value');
        $meterReading->created_at = Carbon::now()->toDateTimeString();

        $meterReading->save();

        return response()->json(['success' => 'Meter Reading Detail Added successfully.']);
    }

    public function readinglist()
    {
        $letters = DB::table('meter_reading')
            ->leftjoin('departments', 'meter_reading.department_id', '=', 'departments.id')
            ->leftjoin('companies', 'departments.company_id', '=', 'companies.id')
            ->select('meter_reading.*', 'departments.name As department_name')
            ->get();
        
        return Datatables::of($letters)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if(Auth::user()->can('meter-reading-edit')){
                    $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                }
                
                if(Auth::user()->can('meter-reading-delete')){
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
        $permission = $user->can('meter-reading-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = DB::table('meter_reading')
                ->leftjoin('departments', 'meter_reading.department_id', '=', 'departments.id')
                ->leftjoin('companies', 'departments.company_id', '=', 'companies.id')
                ->select(
                    'meter_reading.*', 
                    'departments.name as department_name',
                    'departments.company_id',
                    'companies.name as company_name'
                )
                ->where('meter_reading.id', $id)
                ->first();
            
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, MeterReadingDetail $meterReading)
    {
        $user = auth()->user();
        $permission = $user->can('meter-reading-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $form_data = array(
            'department_id' => $request->input('department'),
            'reading_limit' => $request->input('reading_limit'),
            'multiple_value' => $request->input('multiple_value'),
            'updated_at' => Carbon::now()->toDateTimeString(),
        );

        MeterReadingDetail::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Meter Reading Detail is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('meter-reading-delete');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = MeterReadingDetail::findOrFail($id);
        $data->delete();
        
        return response()->json(['success' => 'Deleted successfully']);
    }
}
