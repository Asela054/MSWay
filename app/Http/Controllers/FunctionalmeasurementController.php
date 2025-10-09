<?php

namespace App\Http\Controllers;

use App\Commen;
use App\Functionalmeasurement;
use App \Functionaldepartment_detail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use PDF;

class FunctionalmeasurementController extends Controller
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

        $departments = DB::table('departments')->select('departments.*')
        ->get();

        $functionalmeasurements = DB::table('functionalmeasurements')->select('functionalmeasurements.*')
        ->whereIn('functionalmeasurements.status', [1, 2])
        ->get();

        return view('KPImanagement.functionalmeasurement', compact('functionaltypes','functionalkpis','functionalparameters','departments','functionalmeasurements'));
    }
    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
        $functionalmeasurement = new Functionalmeasurement();
        $functionalmeasurement->type_id = $request->input('type');
        $functionalmeasurement->kpi_id = $request->input('kpi');
        $functionalmeasurement->parameter_id = $request->input('parameter');
        $functionalmeasurement->measurement =  $request->input('measurement');
        $functionalmeasurement->status = '1';
        $functionalmeasurement->created_by = Auth::id();
        $functionalmeasurement->updated_by = '0';
        $functionalmeasurement->save();
        

        $requestID=$functionalmeasurement->id;
        $tableData = $request->input('tableData');
        foreach ($tableData as $rowtabledata) {
            $department = $rowtabledata['col_1'];
            $departmentweightage = $rowtabledata['col_2'];

        
        $functionaldepartment_detail = new Functionaldepartment_detail();
        $functionaldepartment_detail->measurement_id = $requestID;
        $functionaldepartment_detail->department_id = $department;
        $functionaldepartment_detail->departmentweightage = $departmentweightage;
        $functionaldepartment_detail->status = '1';
        $functionaldepartment_detail->created_by = Auth::id();
        $functionaldepartment_detail->updated_by = '0';
        $functionaldepartment_detail->save();
        
        }
        return response()->json(['success' => 'Functional Measurement is Successfully Inserted']);
    }

    public function requestlist()
    {
        $types = DB::table('functionalmeasurements')
            ->leftJoin('functionaltypes', 'functionaltypes.id', 'functionalmeasurements.type_id')
            ->leftJoin('functionalkpis', 'functionalkpis.id', 'functionalmeasurements.kpi_id')
            ->leftJoin('functionalparameters', 'functionalparameters.id', 'functionalmeasurements.parameter_id')
            ->select('functionalmeasurements.*','functionaltypes.type AS type','functionalkpis.kpi AS kpi','functionalparameters.parameter AS parameter')
            ->whereIn('functionalmeasurements.status', [1, 2])
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
                            $btn .= ' <button name="view" id="'.$row->id.'" class="view btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-eye"></i></button>';

                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('functionalmeasurementstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('functionalmeasurementstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            // }
                       
                            $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
              
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

        $data = DB::table('functionalmeasurements')
        ->leftjoin('functionaltypes', 'functionalmeasurements.type_id', '=', 'functionaltypes.id')
        ->select('functionalmeasurements.*', 'functionaltypes.id AS type_id')
        ->where('functionalmeasurements.id', $id)
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
                'measurement' => $request->measurement,
                'department_id' => $request->name,
                'departmentweightage' => $request->departmentweightage,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            Functionalmeasurement::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'Functional Measurement is Successfully Updated']);
    }

    public function view(Request $request){


        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('functionalmeasurements')
        ->select('functionalmeasurements.*')
        ->where('functionalmeasurements.id', $id)
        ->get(); 
        $requestlist = $this->view_reqestcountlist($id); 

        $responseData = array(
            'mainData' => $data[0],
            'requestdata' => $requestlist,
        );

        return response() ->json(['result'=>  $responseData]);
        }
    }
    private function view_reqestcountlist($id){

        $recordID =$id ;
        $data = DB::table('functionaldepartment_details')
        ->leftJoin('departments','departments.id','functionaldepartment_details.department_id')
        ->select('functionaldepartment_details.*','departments.name as department')
        ->where('functionaldepartment_details.measurement_id', $recordID)
        ->where('functionaldepartment_details.status', 1)
        ->get(); 


        $htmlTable = '';
        foreach ($data as $row) {
            
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->department . '</td>'; 
            $htmlTable .= '<td>' . $row->departmentweightage . '</td>'; 
            $htmlTable .= '<td class="d-none">ExistingData</td>'; 
            $htmlTable .= '<td name="detailsId" class="d-none">' . $row->id . '</td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;

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
        Functionalmeasurement::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Functional Measurement is Successfully Deleted']);

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
    //         Functionalmeasurement::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalmeasurement');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Functionalmeasurement::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalmeasurement');
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


}
