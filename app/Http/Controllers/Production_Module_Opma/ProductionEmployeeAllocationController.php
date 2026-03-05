<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\ProductionModule_Opma\EmpProductAllocation;
use App\ProductionModule_Opma\EmpProductAllocationDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use App\ShiftType;

class ProductionEmployeeAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('product-allocation-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $machines = DB::table('opma_machines')
            ->select('id', 'machine')
            ->get();

        $products = DB::table('opma_styles')
            ->select('id', 'title','code')
            ->get();
        $sizes = DB::table('opma_sizes')
            ->select('id', 'size')
            ->get();
        $shifttype= ShiftType::orderBy('id', 'asc')->get();

        return view('Opma_Production.Daily_Production.allocation', compact('machines', 'products', 'shifttype','sizes'));
    }
    
    public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('product-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            DB::beginTransaction();

            $targetqty = $request->input('targetqty');
            $scale = $request->input('scale');
            $size = $request->input('size');
            $remark = $request->input('remark');
            $date = $request->input('date');

            $EmpProductAllocation = new EmpProductAllocation();
            $EmpProductAllocation->date = $date;
            $EmpProductAllocation->machine_id = $request->input('machine');
            $EmpProductAllocation->product_id = $request->input('product');
            $EmpProductAllocation->shift_id = $request->input('shift');
            $EmpProductAllocation->target = $targetqty;
            $EmpProductAllocation->scale = $scale;
            $EmpProductAllocation->size = $size;
            $EmpProductAllocation->remark = $remark;
            $EmpProductAllocation->production_status = '0';
            $EmpProductAllocation->status = '1';
            $EmpProductAllocation->created_by = Auth::id();
            $EmpProductAllocation->updated_by = '0';
            $EmpProductAllocation->save();

            $requestID = $EmpProductAllocation->id;
            

            $tableData = $request->input('tableData');

            foreach ($tableData as $rowtabledata) {

                $emp_id = $rowtabledata['col_2'];

                $EmpProductAllocationDetail = new EmpProductAllocationDetail();
                $EmpProductAllocationDetail->allocation_id = $requestID;
                $EmpProductAllocationDetail->emp_id = $emp_id;
                $EmpProductAllocationDetail->date = $date;
                $EmpProductAllocationDetail->status = '1';
                $EmpProductAllocationDetail->created_by = Auth::id();
                $EmpProductAllocationDetail->updated_by = '0';
                $EmpProductAllocationDetail->save();
            }

            DB::commit();
            return response()->json(['success' => 'Employee Product Allocation Successfully Inserted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => ['An error occurred while saving data: ' . $e->getMessage()]], 422);
        }
    }

    public function edit(Request $request)
    {
          $user = Auth::user();
        $permission = $user->can('product-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('opma_emp_product_allocation as epa')
                ->leftJoin('opma_machines as m', 'epa.machine_id', '=', 'm.id')
                ->leftJoin('opma_styles as p', 'epa.product_id', '=', 'p.id')
                ->leftJoin('shift_types as st', 'epa.shift_id', '=', 'st.id')
                ->leftJoin('opma_sizes as sz', 'epa.size', '=', 'sz.id')
                ->select(
                    'epa.*', 
                    'm.machine as machine_name',    
                    'p.title as title', 
                    'st.shift_name'                
                )
                ->where('epa.id', $id)
                ->first(); 
            
            $requestlist = $this->reqestcountlist($id); 
        
            $responseData = array(
                'mainData' => $data,
                'requestdata' => $requestlist,
            );

            return response()->json(['result' => $responseData]);
        }
    }
    
    private function reqestcountlist($id)
    {
        $recordID = $id;
        $data = DB::table('opma_emp_product_allocation_details as ead')
            ->leftJoin('employees as e', 'ead.emp_id', '=', 'e.emp_id')
            ->select(
                'ead.*', 
                'e.emp_name_with_initial as employee_name'
            )
            ->where('ead.allocation_id', $recordID)
            ->where('ead.status', 1)
            ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td><input type="checkbox" class="employee-checkbox" name="employee_ids[]" value="'. $row->emp_id . '" checked></td>'; 
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . ($row->employee_name ?? $row->employee_name) . '</td>'; 
            $htmlTable .= '<td class="text-right">';
            $htmlTable .= '<button type="button" rowid="'.$row->id.'" class="btnDeletelist btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>';
            $htmlTable .= '</td>'; 
            $htmlTable .= '<td class="d-none">ExistingData</td>';
            $htmlTable .= '<td class="d-none">'.$row->id.'</td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;
    }
   


   
    public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('product-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            DB::beginTransaction();
            
            $current_date_time = Carbon::now()->toDateTimeString();
            $id = $request->hidden_id;

            $form_data = array(
                'date' => $request->date,
                'machine_id' => $request->machine,
                'product_id' => $request->product,
                'shift_id' => $request->shift,
                'target' => $request->targetqty,
                'scale' => $request->scale,
                'size' => $request->size,
                'remark' => $request->remark,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            EmpProductAllocation::findOrFail($id)->update($form_data);

            $tableData = $request->input('tableData');
        
            foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['col_2'];
            $actionStatus = isset($rowtabledata['col_5']) ? $rowtabledata['col_6'] : 'NewData';
            
            if($actionStatus == "Updated" || $actionStatus == "ExistingData") {
                $detailID = null;
                if(isset($rowtabledata['col_5'])) {
                    preg_match('/value="(\d+)"/', $rowtabledata['col_5'], $matches);
                    if(isset($matches[1])) {
                        $detailID = $matches[1];
                    }
                }

                if($detailID) {
                    $EmpProductAllocationDetail = EmpProductAllocationDetail::find($detailID);
                    if($EmpProductAllocationDetail) {
                        $EmpProductAllocationDetail->allocation_id = $id;
                        $EmpProductAllocationDetail->emp_id = $emp_id;
                        $EmpProductAllocationDetail->date = $request->date;
                        $EmpProductAllocationDetail->status = '1';
                        $EmpProductAllocationDetail->updated_by = Auth::id();
                        $EmpProductAllocationDetail->updated_at = $current_date_time;
                        $EmpProductAllocationDetail->save();
                    }
                }
            } elseif($actionStatus == "NewData") {
                $EmpProductAllocationDetail = new EmpProductAllocationDetail();
                $EmpProductAllocationDetail->allocation_id = $id;
                $EmpProductAllocationDetail->emp_id = $emp_id;
                $EmpProductAllocationDetail->date = $request->date;
                $EmpProductAllocationDetail->status = '1';
                $EmpProductAllocationDetail->created_by = Auth::id();
                $EmpProductAllocationDetail->updated_by = '0';
                $EmpProductAllocationDetail->created_at = $current_date_time;
                $EmpProductAllocationDetail->updated_at = $current_date_time;
                $EmpProductAllocationDetail->save();
            }
        }
            
            DB::commit();
            return response()->json(['success' => 'Employee Product Allocation Successfully Updated']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => ['An error occurred while updating data: ' . $e->getMessage()]], 422);
        }
    }



    public function view(Request $request)
    {
        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('opma_emp_product_allocation as epa')
                ->leftJoin('opma_machines as m', 'epa.machine_id', '=', 'm.id')
                ->leftJoin('opma_styles as p', 'epa.product_id', '=', 'p.id')
                ->leftjoin('shift_types as st', 'epa.shift_id', '=', 'st.id')
                ->select('epa.*', 'm.machine', 'p.title', 'st.shift_name')
                ->where('epa.id', $id)
                ->first(); 
            
            $requestlist = $this->view_reqestcountlist($id); 

            $responseData = array(
                'mainData' => $data,
                'requestdata' => $requestlist,
            );

            return response()->json(['result' => $responseData]);
        }
    }
    
    private function view_reqestcountlist($id)
    {
        $recordID = $id;
        $data = DB::table('opma_emp_product_allocation_details as ead')
            ->leftJoin('employees as e', 'ead.emp_id', '=', 'e.emp_id')
            ->select(
                'ead.*', 
                'e.emp_name_with_initial as employee_name'
            )
            ->where('ead.allocation_id', $recordID)
            ->where('ead.status', 1)
            ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . ($row->employee_name ?? $row->employee_name) . '</td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;
    }



     public function deletelist(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('product-allocation-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = $request->input('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocationDetail::findOrFail($id)->update($form_data);

        return response()->json(['success' => 'Employee Product Allocation successfully Deleted']);
    }



    public function delete(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('product-allocation-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
        $id = $request->input('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($id)->update($form_data);

        return response()->json(['success' => 'Employee Product Allocation Successfully Deleted']);
    }

    public function status($id, $statusid)
    {
        $user = Auth::user();
        $permission = $user->can('product-allocation-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        if($statusid == 1){
            $form_data = array(
                'status' => '1',
                'updated_by' => Auth::id(),
            );
            EmpProductAllocation::findOrFail($id)->update($form_data);
    
            return redirect()->route('productionallocation');
        } else {
            $form_data = array(
                'status' => '2',
                'updated_by' => Auth::id(),
            );
            EmpProductAllocation::findOrFail($id)->update($form_data);
    
            return redirect()->route('productionallocation');
        }
    }

    

    public function getMachineEmployees(Request $request)
    {
        $machineId = $request->machine_id;
        $employees = DB::table('opma_machine_employees as me')
            ->join('employees as e', 'me.emp_id', '=', 'e.emp_id')
            ->where('me.opma_machine_id', $machineId)
            ->select('e.emp_id as emp_id', 'e.emp_name_with_initial')
            ->get();
        
        return response()->json($employees);
    }

     public function getStyleSizes(Request $request)
    {
        $styleId = $request->style_id;
        
        $sizes = DB::table('opma_style_sizes as ss')
            ->join('opma_sizes as s', 'ss.opma_size_id', '=', 's.id')
            ->where('ss.opma_style_id', $styleId)
            ->select('s.id', 's.size', 's.remark')
            ->get();
        
        return response()->json($sizes);
    }

}