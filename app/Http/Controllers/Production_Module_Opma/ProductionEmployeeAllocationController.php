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
        $allocation = DB::table('opma_emp_product_allocation')
            ->where('id', $recordID)
            ->where('status', '!=', 3)
            ->first();
        
        if (!$allocation) {
            return ''; 
        }
        
        $productiondate = $allocation->date;
        $shiftId = $allocation->shift_id;
        
        // Get EXISTING allocated employees for this allocation (locked)
        $existingEmployees = DB::table('opma_emp_product_allocation_details as ead')
            ->join('employees as e', 'ead.emp_id', '=', 'e.emp_id')
            ->where('ead.allocation_id', $recordID)
            ->where('ead.status', 1)
            ->select('e.emp_id', 'e.emp_name_with_initial', 'ead.id as allocatedid')
            ->get();
        
        // Get NEW available employees (not allocated for this date and shift)
        $newAvailableEmployees = DB::table('opma_machine_employees as me')
            ->join('employees as e', 'me.emp_id', '=', 'e.emp_id')
            ->where('me.opma_machine_id', $allocation->machine_id)
            ->whereNotExists(function($query) use ($productiondate, $shiftId, $recordID) {
                $query->select(DB::raw(1))
                    ->from('opma_emp_product_allocation_details as details')
                    ->join('opma_emp_product_allocation as allocation', 'allocation.id', '=', 'details.allocation_id')
                    ->whereColumn('details.emp_id', 'me.emp_id')
                    ->where('details.date', $productiondate)
                    ->where('details.status', '!=', 3)
                    ->where('allocation.shift_id', $shiftId)
                    ->where('allocation.status', '!=', 3);
            })
            ->whereNotIn('e.emp_id', $existingEmployees->pluck('emp_id'))
            ->select('e.emp_id', 'e.emp_name_with_initial')
            ->orderBy('e.emp_name_with_initial')
            ->get();
        
        $htmlTable = '';
        
        // First, add EXISTING employees (locked, checked, ExistingData)
        foreach ($existingEmployees as $row) {
            $htmlTable .= '<tr class="existing-employee">';
            $htmlTable .= '<td><input type="checkbox" class="employee-checkbox" name="employee_ids[]" value="'. $row->emp_id . '" checked disabled></td>'; 
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->emp_name_with_initial . '</td>'; 
            $htmlTable .= '<td class="text-right">';
            $htmlTable .= '<button type="button" rowid="'.$row->allocatedid.'" class="btnDeletelist btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>';
            $htmlTable .= '</td>'; 
            $htmlTable .= '<td class="d-none">ExistingData</td>';
            $htmlTable .= '<td class="d-none">'.$row->allocatedid.'</td>'; 
            $htmlTable .= '</tr>';
        }
        
        // Then, add NEW available employees (unchecked, enabled, NewData)
        foreach ($newAvailableEmployees as $row) {
            $htmlTable .= '<tr class="new-employee">';
            $htmlTable .= '<td><input type="checkbox" class="employee-checkbox" name="employee_ids[]" value="'. $row->emp_id . '"></td>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->emp_name_with_initial . '</td>'; 
            $htmlTable .= '<td class="text-right">';
            $htmlTable .= '<span class="badge badge-success"></span>';
            $htmlTable .= '</td>'; 
            $htmlTable .= '<td class="d-none">NewData</td>';
            $htmlTable .= '<td class="d-none"></td>'; 
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
            $actionStatus = isset($rowtabledata['col_5']) ? $rowtabledata['col_5'] : '';
            
            if($actionStatus == "ExistingData") {

                $detailID = null;
                 if($actionStatus == "ExistingData" && isset($rowtabledata['col_6']) && !empty($rowtabledata['col_6'])) {
                        $detailID = $rowtabledata['col_6'];
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
        $productiondate = $request->productiondate;
        $shiftId = $request->shiftId;
        
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

    public function dpt_allocation_list(Request $request)
    {
          $user = Auth::user();
        $permission = $user->can('product-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->input('department');
        $date = $request->input('allocation_date');

        

        $emp_List = DB::table('employees')
            ->select( 
                'emp_id',    
                'emp_name_with_initial'             
            )
            ->where('emp_department', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->get();

        $machines = DB::table('opma_machines')
            ->select('id', 'company_id', 'branch_id', 'machine')
             ->where('status', 1)
            ->get();

        $machine_emp = DB::table('opma_machine_employees')
            ->select('id', 'opma_machine_id', 'emp_id')
            ->get()
            ->keyBy('emp_id');

        $shifts = DB::table('shift_types')
            ->select('id', 'shift_code', 'shift_name', 'deleted')
            ->where('deleted', 0)
            ->get();
        $styles = DB::table('opma_styles')
            ->select('id', 'title', 'code')
            ->get();

            $html = '';

         foreach ($emp_List as $emp) {

            $assigned = $machine_emp->get($emp->emp_id);
            $assigned_machine_id = $assigned ? $assigned->opma_machine_id : null;

            $machine_dd = '<select class="form-control machine-select form-control-sm" name="machine_id[' . $emp->emp_id . ']" ' . ($assigned_machine_id ? 'disabled' : '') . '>';
            $machine_dd .= '<option value="">-- Select --</option>';
            foreach ($machines as $m) {
                $selected = ($assigned_machine_id == $m->id) ? 'selected' : '';
                $machine_dd .= '<option value="' . $m->id . '" ' . $selected . '>' . $m->machine . ' (' . $m->id . ')</option>';
            }
            $machine_dd .= '</select>';

            if ($assigned_machine_id) {
                $machine_dd .= '<input type="hidden" name="machine_id[' . $emp->emp_id . ']" value="' . $assigned_machine_id . '">';
            }

            $shift_dd = '<select class="form-control shift-select form-control-sm" name="shift_id[' . $emp->emp_id . ']">';
            $shift_dd .= '<option value="">-- Select --</option>';
            foreach ($shifts as $s) {
                $shift_dd .= '<option value="' . $s->id . '">' . $s->shift_name . ' (' . $s->shift_code . ')</option>';
            }
            $shift_dd .= '</select>';

            // style dropdown
            $style_dd = '<select class="form-control style-select form-control-sm" name="style_id[' . $emp->emp_id . ']" data-emp="' . $emp->emp_id . '">';
            $style_dd .= '<option value="">-- Select --</option>';
            foreach ($styles as $st) {
                $style_dd .= '<option value="' . $st->id . '">' . $st->title . ' (' . $st->code . ')</option>';
            }
            $style_dd .= '</select>';

            $size_dd = '<select class="form-control size-select form-control-sm" name="size_id[' . $emp->emp_id . ']" data-emp="' . $emp->emp_id . '">';
            $size_dd .= '<option value="">Select Size</option>';
            $size_dd .= '</select>';

            $html .= '<tr>';
            $html .= '<td>' . $emp->emp_id . '</td>';
            $html .= '<td>' . $emp->emp_name_with_initial . '</td>';
            $html .= '<td>' . $machine_dd . '</td>';
            $html .= '<td>' . $shift_dd . '</td>';
            $html .= '<td>' . $style_dd . '</td>';
            $html .= '<td>' . $size_dd . '</td>';
            $html .= '<td><input type="text" class="form-control form-control-sm" name="target[' . $emp->emp_id . ']"></td>';
            $html .= '<td><input type="text" class="form-control form-control-sm" name="scale[' . $emp->emp_id . ']"></td>';
            $html .= '<td><input type="text" class="form-control form-control-sm" name="remark[' . $emp->emp_id . ']"></td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);

    }

    public function dpt_allocation_insert(Request $request)
    {
          $user = Auth::user();
        $permission = $user->can('product-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

         try {
            DB::beginTransaction();

            $department = $request->input('department');
            $date = $request->input('allocation_date');
            $tableData = $request->input('tableData');

            foreach ($tableData as $rowtabledata) {
                    $emp_id = $rowtabledata['emp_id'];
                    $machine_id = $rowtabledata['machine_id'];
                    $shift_id = $rowtabledata['shift_id'];
                    $style_id = $rowtabledata['style_id'];
                    $size_id = $rowtabledata['size_id'];
                    $target = $rowtabledata['target'];
                    $scale = $rowtabledata['scale'];
                    $remark = $rowtabledata['remark'];
                    
                // 1. check main allocation table for this date/machine/shift/style/target combo
                $existingAllocation = DB::table('opma_emp_product_allocation')
                    ->where('date', $date)
                    ->where('machine_id', $machine_id)
                    ->where('shift_id', $shift_id)
                    ->where('product_id', $style_id)
                    ->where('target', $target)
                    ->where('production_status', 0)
                    ->where('status', 1)
                    ->first();

                if ($existingAllocation) {
                    $requestID = $existingAllocation->id;
                } else {
                    // no matching main record, insert new one
                    $EmpProductAllocation = new EmpProductAllocation();
                    $EmpProductAllocation->date = $date;
                    $EmpProductAllocation->machine_id = $machine_id;
                    $EmpProductAllocation->product_id = $style_id;
                    $EmpProductAllocation->shift_id = $shift_id;
                    $EmpProductAllocation->target = $target;
                    $EmpProductAllocation->scale = $scale;
                    $EmpProductAllocation->size = $size_id;
                    $EmpProductAllocation->remark = $remark;
                    $EmpProductAllocation->production_status = '0';
                    $EmpProductAllocation->status = '1';
                    $EmpProductAllocation->created_by = Auth::id();
                    $EmpProductAllocation->updated_by = '0';
                    $EmpProductAllocation->save();

                    $requestID = $EmpProductAllocation->id;
                }

                // 2. check if this employee is already added under that allocation details table
                $existingDetail = DB::table('opma_emp_product_allocation_details')
                    ->where('allocation_id', $requestID)
                    ->where('emp_id', $emp_id)
                    ->where('status', 1)
                    ->first();

                if ($existingDetail) {
                    // already added, update instead of duplicate insert
                    DB::table('opma_emp_product_allocation_details')
                        ->where('id', $existingDetail->id)
                        ->update([
                            'emp_id'  => $emp_id,
                            'date'  => $date,
                            'updated_by'  => Auth::id()
                        ]);
                } else {

                    // not added yet, insert new detail row
                    $EmpProductAllocationDetail = new EmpProductAllocationDetail();
                    $EmpProductAllocationDetail->allocation_id = $requestID;
                    $EmpProductAllocationDetail->emp_id = $emp_id;
                    $EmpProductAllocationDetail->date = $date;
                    $EmpProductAllocationDetail->status = '1';
                    $EmpProductAllocationDetail->created_by = Auth::id();
                    $EmpProductAllocationDetail->updated_by = '0';
                    $EmpProductAllocationDetail->save();
                }
            }
            DB::commit();
            return response()->json(['success' => 'Employee Production Allocation Successfully Inserted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => ['An error occurred while saving data: ' . $e->getMessage()]], 422);
        }
    
    }

}