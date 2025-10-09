<?php

namespace App\Http\Controllers;

use App\Commen;
use App\Emp_kpi_allocation;
use App\Kpiallocation;
use App\Kpidepartment_detail;
use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class Emp_kpi_allocationController extends Controller
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

        $kpiallocations = DB::table('kpiallocations')->select('kpiallocations.*')
        ->whereIn('kpiallocations.status', [1, 2])
        ->get();

        $kpidepartment_details = DB::table('kpidepartment_details')->select('kpidepartment_details.*')
        ->whereIn('kpidepartment_details.status', [1, 2])
        ->get();

        $employees = DB::table('employees')->select('employees.emp_name_with_initial','employees.emp_id','employees.emp_department')
                    ->get();
         
        return view('KPImanagement.empallocation', compact('functionaltypes','functionalkpis','functionalparameters','functionalmeasurements','functionalweightages','kpiyears','departments','functionaldepartment_details','employees','kpiallocations','kpidepartment_details'));
    }

    public function insert(Request $request) {
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
    
        $tableData = $request->input('tableData');

        foreach ($tableData as $rowtabledata) {
            $emp = $rowtabledata['col_1'];
            $empfigure = $rowtabledata['col_2'];
    
            // Insert into kpiallocations table
            $empallocation = new Emp_kpi_allocation();
            $empallocation->year_id = $request->input('year');
            $empallocation->measurement_id = $request->input('measurement');
            $empallocation->department_id = $request->input('department');
            $empallocation->emp_id = $emp;
            $empallocation->empfigure = $empfigure;
            $empallocation->status = '1';
            $empallocation->created_by = Auth::id();
            $empallocation->updated_by = '0';
            $empallocation->save();
    
        }
    
        return response()->json(['success' => 'Employee Allocation is Successfully Inserted']);
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
            ->whereIn('kpiallocations.approve_status', [2])
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
                            
                            $btn .= ' <button name="add" id="'.$row->id.'" class="add btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-plus"></i></button>';

                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('empallocationstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('empallocationstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            // }
                       
                            $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
              
                return $btn;
            })
           
            ->rawColumns(['action'])
            ->make(true);
    }

    public function requestlist2()
    {
        $types = DB::table('emp_kpi_allocations')
            ->leftjoin('kpiyears', 'kpiyears.id', 'emp_kpi_allocations.year_id')
            ->leftjoin('functionalmeasurements', 'functionalmeasurements.id', 'emp_kpi_allocations.measurement_id')
            ->leftjoin('departments', 'departments.id', 'emp_kpi_allocations.department_id')
            ->leftjoin('employees', 'employees.emp_id', 'emp_kpi_allocations.emp_id')
            ->select('emp_kpi_allocations.*','functionalmeasurements.measurement AS measurement','kpiyears.year AS year','departments.name AS department','employees.emp_name_with_initial AS empname' )
            ->whereIn('emp_kpi_allocations.status', [1, 2])
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
                            
                //             if($row->status == 1){
                //                 $btn .= ' <a href="'.route('empallocationstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                //             }else{
                //                 $btn .= '&nbsp;<a href="'.route('empallocationstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                //             }
                       
                //             $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
              
                // return $btn;
            })
           
            ->rawColumns(['action'])
            ->make(true);
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
    
    //         return redirect()->route('empallocation');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Kpiallocation::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('empallocation');
    //     }
    // }

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

    public function add(Request $request){
        $id = $request->input('id');
        
        // Fetch the main data for the allocation
        $mainData = DB::table('kpiallocations')
            ->leftjoin('kpiyears', 'kpiyears.id', 'kpiallocations.year_id')
            ->leftjoin('functionalmeasurements', 'functionalmeasurements.id', 'kpiallocations.measurement_id')
            ->leftjoin('functionaltypes', 'functionaltypes.id', 'functionalmeasurements.type_id')
            ->leftjoin('functionalkpis', 'functionalkpis.id', 'functionalmeasurements.kpi_id')
            ->leftjoin('functionalparameters', 'functionalparameters.id', 'functionalmeasurements.parameter_id')
            ->select('kpiallocations.*', 'kpiyears.year', 'functionaltypes.id as type_id', 'functionalkpis.id as kpi_id', 'functionalparameters.id as parameter_id', 'functionalmeasurements.id as measurement_id')
            ->where('kpiallocations.id', $id)
            ->first();
        
        // Fetch department details related to the measurement
        $departments = DB::table('kpidepartment_details')
            ->leftJoin('departments', 'departments.id', '=', 'kpidepartment_details.department_id')
            ->select('kpidepartment_details.department_id', 'departments.name as department')
            ->where('kpidepartment_details.measurement_id', $mainData->measurement_id)
            ->where('kpidepartment_details.status', 1)
            ->get();
    
        // Return the result as JSON for the frontend
        return response()->json([
            'result' => [
                'mainData' => $mainData,
                'departments' => $departments
            ]
        ]);
    }

    public function getfigurefilter(Request $request)
    {
        $department_id = $request->input('department_id');
        $measurement_id = $request->input('measurement_id');

        if (!$department_id || !$measurement_id) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $figure = DB::table('kpidepartment_details')
            ->select('departmentfigure') // Select the department figure
            ->where('department_id', $department_id)
            ->where('measurement_id', $measurement_id)
            ->first();

        if (!$figure) {
            return response()->json(['result' => 'No department figure found'], 404);
        }

        // Return the department figure
        return response()->json(['result' => $figure->departmentfigure]);
    }



}
