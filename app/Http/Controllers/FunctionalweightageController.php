<?php

namespace App\Http\Controllers;

use App\Commen;
use App\Functionalweightage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use PDF;

class FunctionalweightageController extends Controller
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

        return view('KPImanagement.functionalweightage', compact('functionaltypes','functionalkpis','functionalparameters' ));
    }
    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
    
        $tableData = json_decode($request->input('tabledata'), true);
        
        if (!is_array($tableData) || empty($tableData)) {
            return response()->json(['error' => 'Invalid or empty table data'], 400);
        }
        
        foreach ($tableData as $rowtabledata) {
            $parameter = $rowtabledata['parameter'] ?? null;
            $weightage = $rowtabledata['weightage'] ?? null;
            
            // Skip if parameter or weightage is missing
            if (!$parameter || !$weightage) {
                continue;
            }
            
            $functionalweightage = new Functionalweightage();
            $functionalweightage->type_id = $request->input('type');
            $functionalweightage->kpi_id = $request->input('kpi');
            $functionalweightage->parameter_id = $parameter;
            $functionalweightage->weightage = $weightage;
            $functionalweightage->status = '1';
            $functionalweightage->created_by = Auth::id();
            $functionalweightage->updated_by = '0';
            $functionalweightage->save();
        }
        
        return response()->json(['success' => 'Functional Parameter Weightage is Successfully Inserted']);
    }

    public function requestlist()
    {
        $types = DB::table('functionalweightages')
            ->leftJoin('functionaltypes', 'functionaltypes.id', 'functionalweightages.type_id')
            ->leftJoin('functionalkpis', 'functionalkpis.id', 'functionalweightages.kpi_id')
            ->leftJoin('functionalparameters', 'functionalparameters.id', 'functionalweightages.parameter_id')
            ->select('functionalweightages.*','functionaltypes.type AS type','functionalkpis.kpi AS kpi','functionalparameters.parameter AS parameter')
            ->whereIn('functionalweightages.status', [1, 2])
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
                            //     $btn .= ' <a href="'.route('functionalweightagestatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('functionalweightagestatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
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

        $data = DB::table('functionalweightages')
        ->leftjoin('functionaltypes', 'functionalweightages.type_id', '=', 'functionaltypes.id')
        ->select('functionaltypes.*', 'functionalweightages.id AS weightage_id')
        ->where('functionalweightages.id', $id)
        ->get();

    }

    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
       
            $current_date_time = Carbon::now()->toDateTimeString();

        $id =  $request->hidden_id ;
        $form_data = array(
                'type_id' => $request->type,
                'kpi_id' => $request->kpi,
                'parameter_id' => $request->parameter,
                'weightage' => $request->weightage,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            Functionalweightage::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'Functional Parameter Weightage is Successfully Updated']);
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
        Functionalweightage::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Functional Parameter Weightage is Successfully Deleted']);

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
    //         Functionalweightage::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalweightage');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Functionalweightage::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalweightage');
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
    $parameter = DB::table('functionalparameters')->select('functionalparameters.*')
    ->where('kpi_id', '=', $kpi_id)
    ->get();

    return response()->json($parameter);
}


}
