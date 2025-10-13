<?php

namespace App\Http\Controllers;

use App\Commen;
use App\Functionalmeasurementweightage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use PDF;

class FunctionalmeasurementweightageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $functionaltypes = DB::table('functionaltypes')->select('functionaltypes.*')
        ->whereIn('functionaltypes.status', [1, 2])
        ->get();
        
        $functionalkpis = DB::table('functionalkpis')->select('functionalkpis.*')
        ->whereIn('functionalkpis.status', [1, 2])
        ->get();

        $functionalparameters = DB::table('functionalparameters')->select('functionalparameters.*')
        ->whereIn('functionalparameters.status', [1, 2])
        ->get();

        $functionalmeasurements = DB::table('functionalmeasurements')->select('functionalmeasurements.*')
        ->whereIn('functionalmeasurements.status', [1, 2])
        ->get();

        $functionalweightages = DB::table('functionalweightages')->select('functionalweightages.*')
        ->whereIn('functionalweightages.status', [1, 2])
        ->get();

         
        return view('KPImanagement.functionalmesurementweightage', compact('functionaltypes','functionalkpis','functionalparameters','functionalmeasurements','functionalweightages'));
    }
    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
        $tableData = json_decode($request->input('tabledata'), true);
        
        if (!$tableData || empty($tableData)) {
            return response()->json(['errors' => 'No measurement data provided'], 400);
        }
        
        foreach ($tableData as $rowtabledata) {
            $measurement_text = $rowtabledata['col_1'];
            $measurement_weightage = $rowtabledata['col_2'];
            
            $measurement = DB::table('functionalmeasurements')
                ->where('measurement', $measurement_text)
                ->where('parameter_id', $request->input('parameter'))
                ->first();
                
            if (!$measurement) {
                continue; 
            }

            $exists = Functionalmeasurementweightage::where('type_id', $request->input('type'))
                ->where('kpi_id', $request->input('kpi'))
                ->where('parameter_id', $request->input('parameter'))
                ->where('measurement_id', $measurement->id)
                ->where('status', '1')
                ->first();
                
            if ($exists) {
                continue; 
            }

            $functionalmeasurement = new Functionalmeasurementweightage();
            $functionalmeasurement->type_id = $request->input('type');
            $functionalmeasurement->kpi_id = $request->input('kpi');
            $functionalmeasurement->parameter_id = $request->input('parameter');
            $functionalmeasurement->measurement_id = $measurement->id;
            $functionalmeasurement->measurement_weightage = $measurement_weightage;
            $functionalmeasurement->status = '1';
            $functionalmeasurement->created_by = Auth::id();
            $functionalmeasurement->updated_by = '0';
            $functionalmeasurement->save();
        }
        
        return response()->json(['success' => 'Functional Measurement Weightage is Successfully Inserted']);
    }


    public function requestlist()
    {
        $types = DB::table('functionalmeasurementweightages')
            ->leftJoin('functionaltypes', 'functionaltypes.id', 'functionalmeasurementweightages.type_id')
            ->leftJoin('functionalkpis', 'functionalkpis.id', 'functionalmeasurementweightages.kpi_id')
            ->leftJoin('functionalparameters', 'functionalparameters.id', 'functionalmeasurementweightages.parameter_id')
            ->leftjoin('functionalmeasurements', 'functionalmeasurements.id', 'functionalmeasurementweightages.measurement_id')
            ->leftJoin('functionalweightages', 'functionalparameters.id', 'functionalweightages.parameter_id')
            ->select('functionalmeasurementweightages.*','functionaltypes.type AS type','functionalkpis.kpi AS kpi','functionalparameters.parameter AS parameter','functionalmeasurements.measurement AS measurement','functionalweightages.weightage AS weightage')
            ->whereIn('functionalmeasurementweightages.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('functionalmeasurementweightagestatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('functionalmeasurementweightagestatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            // }
                       
                            $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
              
                return $btn;
            })
           
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $id = $request->id; // Add this line

        $data = DB::table('functionalmeasurementweightages')
            ->leftjoin('functionaltypes', 'functionalmeasurementweightages.type_id', '=', 'functionaltypes.id')
            ->leftjoin('functionalkpis', 'functionalmeasurementweightages.kpi_id', '=', 'functionalkpis.id')
            ->leftjoin('functionalparameters', 'functionalmeasurementweightages.parameter_id', '=', 'functionalparameters.id')
            ->leftjoin('functionalmeasurements', 'functionalmeasurementweightages.measurement_id', '=', 'functionalmeasurements.id')
            ->select('functionalmeasurementweightages.*', 
                    'functionaltypes.id AS type_id',
                    'functionalkpis.id AS kpi_id',
                    'functionalparameters.id AS parameter_id',
                    'functionalmeasurements.id AS measurement_id')
            ->where('functionalmeasurementweightages.id', $id)
            ->first(); // Use first() instead of get()

        return response()->json(['result' => $data]);
    }

    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
    
        $current_date_time = Carbon::now()->toDateTimeString();

        $id = $request->hidden_id;
        $form_data = array(
            'type_id' => $request->type,
            'kpi_id' => $request->kpi,
            'parameter_id' => $request->parameter,
            'measurement_id' => $request->measurement, 
            'measurement_weightage' => $request->measurement_weightage, 
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );

        Functionalmeasurementweightage::findOrFail($id)->update($form_data);
        
        return response()->json(['success' => 'Functional Measurement Weightage is Successfully Updated']);
    }

    public function delete(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
        
            $id = Request('id');
            $current_date_time = Carbon::now()->toDateTimeString();
            $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        Functionalmeasurementweightage::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Functional Measurement Weightage is Successfully Deleted']);

    }

    // public function status($id,$statusid){
    //     $user = Auth::user();
    //     $permission = $user->can('Functional-status');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     } 

    //     if($statusid == 1){
    //         $form_data = array(
    //             'status' =>  '1',
    //             'updated_by' => Auth::id(),
    //         );
    //         Functionalmeasurementweightage::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalmeasurementweightage');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Functionalmeasurementweightage::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalmeasurementweightage');
    //     }

    // }


public function getkpifilter($type_id)
{
    $kpi = DB::table('functionalkpis')
    ->select('functionalkpis.*')
    ->where('type_id', '=', $type_id)
    ->get();

    return response()->json($kpi);
}

public function getparameterfilter($kpi_id)
{
    $parameter = DB::table('functionalparameters')
    ->select('functionalparameters.*')
    ->where('kpi_id', '=', $kpi_id)
    ->get();

    return response()->json($parameter);
}

public function getmeasurementfilter($parameter_id)
{
    $measurement = DB::table('functionalmeasurements')
    ->select('functionalmeasurements.*')
    ->where('parameter_id', '=', $parameter_id)
    ->get();

    return response()->json($measurement);
}
}
