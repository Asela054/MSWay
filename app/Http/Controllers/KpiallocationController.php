<?php

namespace App\Http\Controllers;

use App\Commen;
use App\Kpiallocation;
use App\Kpidepartment_detail;
use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class KpiallocationController extends Controller
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

        $kpiyears = DB::table('kpiyears')->select('kpiyears.*')
        ->whereIn('kpiyears.status', [1, 2])
        ->get();

        $departments = DB::table('departments')->select('departments.*')
        ->get();

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

        $functionalmeasurementweightages = DB::table('functionalmeasurementweightages')->select('functionalmeasurementweightages.*')
        ->whereIn('functionalmeasurementweightages.status', [1, 2])
        ->get();

        $functionaldepartment_details = DB::table('functionaldepartment_details')->select('functionaldepartment_details.*')
        ->whereIn('functionaldepartment_details.status',[1,2])
        ->get();

         
        return view('KPImanagement.kpiallocation', compact('functionaltypes','functionalkpis','functionalparameters','functionalmeasurements','functionalweightages','kpiyears','departments','functionaldepartment_details'));
    }
    public function insert(Request $request) {
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
    
        $tableData = $request->input('tableData');
        $departmentData = json_decode($request->input('hidden_department_figures'), true);

        foreach ($tableData as $rowtabledata) {
            $measurement = $rowtabledata['col_1'];
            $figure = $rowtabledata['col_2'];
    
            // Insert into kpiallocations table
            $kpiallocation = new Kpiallocation();
            $kpiallocation->year_id = $request->input('year');
            $kpiallocation->measurement_id = $measurement;
            $kpiallocation->figure = $figure;
            $kpiallocation->status = '1';
            $kpiallocation->approve_status = '1';
            $kpiallocation->created_by = Auth::id();
            $kpiallocation->updated_by = '0';
            $kpiallocation->save();
    
            // Insert into kpidepartment_details table
            if (is_array($departmentData)) {
                foreach ($departmentData as $departmentFigure) {
                    $kpidepartment = new Kpidepartment_detail();  
                    $kpidepartment->measurement_id = $measurement;
                    $kpidepartment->department_id = $departmentFigure['department_id'];  
                    $kpidepartment->departmentfigure = $departmentFigure['department_figure'];
                    $kpidepartment->status = '1';  
                    $kpidepartment->created_by = Auth::id();
                    $kpidepartment->updated_by = '0';
                    $kpidepartment->save();
                }
            }
        }
    
        return response()->json(['success' => 'KPI Allocation is Successfully Inserted']);
    }
    
    

    public function requestlist()
    {
        $types = DB::table('kpiallocations')
            ->leftjoin('kpiyears', 'kpiyears.id', 'kpiallocations.year_id')
            ->leftjoin('functionalmeasurements', 'functionalmeasurements.id', 'kpiallocations.measurement_id')
            ->leftjoin('functionalmeasurementweightages', 'functionalmeasurements.id', 'functionalmeasurementweightages.measurement_id')
            ->leftJoin('functionaltypes', 'functionaltypes.id', 'functionalmeasurements.type_id')
            ->leftJoin('functionalkpis', 'functionalkpis.id', 'functionalmeasurements.kpi_id')
            ->leftJoin('functionalparameters', 'functionalparameters.id', 'functionalmeasurements.parameter_id')
            ->leftJoin('functionalweightages', 'functionalparameters.id', 'functionalweightages.parameter_id')
            ->select('kpiallocations.*','functionaltypes.type AS type','functionalkpis.kpi AS kpi','functionalparameters.parameter AS parameter','functionalmeasurements.measurement AS measurement','functionalmeasurementweightages.measurement_weightage AS measurement_weightage','functionalweightages.weightage AS weightage','kpiyears.year AS year' )
            ->whereIn('kpiallocations.status', [1, 2])
            ->whereIn('kpiallocations.approve_status', [1])
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

                            if($row->approve_status == 1){
                                $btn .= ' <a href="'.route('kpiallocationapprove', ['id' => $row->id, 'app_stasus' => 2]) .'" onclick="return approve_confirm()" target="_self" class="appL2 btn btn-outline-warning btn-sm"><i class="fas fa-level-up-alt"></i></a>';
                            }

                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('kpiallocationstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('kpiallocationstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
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

        $data = DB::table('kpiallocations')
        ->leftjoin('functionalmeasurements', 'functionalmeasurements.id', 'kpiallocations.measurement_id')
        ->leftjoin('kpiyears', 'kpiyears.id', 'kpiallocations.year_id')
        ->select('kpiallocations.*', 'functionalmeasurements.id AS measurement_id', 'kpiyears.id AS year_id')
        ->where('kpiallocations.id', $id)
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
                'year_id' => $request->year,
                'type_id' => $request->type,
                'kpi_id' => $request->kpi,
                'parameter_id' => $request->parameter,
                'measurement' => $request->measurement,
                'department_id' => $request->name,
                'figure' => $request->figure,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            Kpiallocation::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'KPI Allocation is Successfully Updated']);
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
        Kpiallocation::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'KPI Allocation is Successfully Deleted']);

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
    //         Kpiallocation::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('kpiallocation');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Kpiallocation::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('kpiallocation');
    //     }
    // }
    
    public function app_status($id,$app_statusid){
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        if($app_statusid == 1){
            $form_data = array(
                'approve_status' =>  '1',
                'approve_by' => Auth::id(),
                'approve_time' => Carbon::now()->toDateTimeString()
                
            );
            Kpiallocation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('kpiallocation');
        } else{
            $form_data = array(
                'approve_status' =>  '2',
                'approve_by' => Auth::id(),
                'approve_time' => Carbon::now()->toDateTimeString()
            );
            Kpiallocation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('kpiallocation');
        }

    }


    public function view(Request $request){
        $id = $request->input('id');
        if ($request->ajax()){
            $data = DB::table('kpiallocations')
                ->leftJoin('functionalmeasurements', 'functionalmeasurements.id', '=', 'kpiallocations.measurement_id')
                ->select('kpiallocations.*', 'functionalmeasurements.id AS measurement_id')
                ->where('kpiallocations.id', $id)
                ->get();
    
            $requestlist = $this->view_requestcountlist($data[0]->measurement_id); // Use measurement_id from the first record
    
            $responseData = array(
                'mainData' => $data[0],
                'requestdata' => $requestlist,
            );
    
            return response()->json(['result' => $responseData]);
        }
    }
    
    private function view_requestcountlist($measurement_id){
        $data = DB::table('kpidepartment_details')
            ->leftJoin('departments', 'departments.id', '=', 'kpidepartment_details.department_id')
            ->select('kpidepartment_details.*', 'departments.name as department')
            ->where('kpidepartment_details.measurement_id', $measurement_id)
            ->where('kpidepartment_details.status', 1)
            ->get();
    
        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->department . '</td>';
            $htmlTable .= '<td>' . $row->departmentfigure . '</td>';
            $htmlTable .= '<td class="d-none">ExistingData</td>';
            $htmlTable .= '<td name="detailsId" class="d-none">' . $row->id . '</td>';
            $htmlTable .= '</tr>';
        }
    
        return $htmlTable;
    }
    


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

public function getdepartmentfilter($measurement_id)
{
    
    if (!$measurement_id) {
        return response()->json(['error' => 'Invalid measurement ID'], 400);
    }

    $departments = DB::table('functionaldepartment_details')
        ->leftjoin('departments','departments.id','functionaldepartment_details.department_id')
        ->select('functionaldepartment_details.*','departments.name as department','departments.id as department_id', 'functionaldepartment_details.departmentweightage as weightage')
        ->where('functionaldepartment_details.measurement_id', $measurement_id)
        ->get();

    if ($departments->isEmpty()) {
        return response()->json(['result' => '<tr><td colspan="2">No departments found</td></tr>']);
    }

    $tableRows = '';
    foreach ($departments as $dept) {
        $tableRows .= '<tr>';
        $tableRows .= '<td class="department_name">' . $dept->department . '</td>';
        $tableRows .= '<td class="department_id d-none">' . $dept->department_id . '</td>';
        $tableRows .= '<td class="weightage">' . $dept->weightage . '</td>';
        $tableRows .= '</tr>';
    }

    return response()->json(['result' => $tableRows]);
}



}
