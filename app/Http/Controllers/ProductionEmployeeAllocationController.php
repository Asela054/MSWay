<?php

namespace App\Http\Controllers;

use App\EmpProductAllocation;
use App\EmpProductAllocationDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

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

        $machines = DB::table('machines')
            ->select('id', 'machine')
            ->get();

        $products = DB::table('product')
            ->select('id', 'productname')
            ->get();

        return view('Daily_Production.allocation', compact('machines', 'products'));
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

            $EmpProductAllocation = new EmpProductAllocation();
            $EmpProductAllocation->date = $request->input('date');
            $EmpProductAllocation->status = '1';
            $EmpProductAllocation->created_by = Auth::id();
            $EmpProductAllocation->updated_by = '0';
            $EmpProductAllocation->save();

            $requestID = $EmpProductAllocation->id;
            $date = $request->input('date');
            $machine_id = $request->input('machine');
            $product_id = $request->input('product');

            $tableData = $request->input('tableData');

            foreach ($tableData as $rowtabledata) {
                $emp_id = $rowtabledata['col_1'];
                $empname = $rowtabledata['col_2'];

                $EmpProductAllocationDetail = new EmpProductAllocationDetail();
                $EmpProductAllocationDetail->allocation_id = $requestID;
                $EmpProductAllocationDetail->emp_id = $emp_id;
                $EmpProductAllocationDetail->machine_id = $machine_id;
                $EmpProductAllocationDetail->product_id = $product_id;
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

    public function requestlist()
    {
        $types = DB::table('emp_product_allocation')
            ->select('emp_product_allocation.*')
            ->whereIn('emp_product_allocation.status', [1, 2])
            ->get();

        return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();

                $btn .= ' <button name="view" id="'.$row->id.'" class="view btn btn-outline-secondary btn-sm" type="button"><i class="fas fa-eye"></i></button>';

                if($user->can('product-allocation-edit')){
                    $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="button"><i class="fas fa-pencil-alt"></i></button>';
                }

                if($user->can('product-allocation-status')){
                    if($row->status == 1){
                        $btn .= ' <a href="'.route('productallocationstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                    }else{
                        $btn .= '&nbsp;<a href="'.route('productallocationstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                    }
                }
                if($user->can('product-allocation-delete')){
                    $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }
              
                return $btn;
            })
           
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit(Request $request)
    {
        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('emp_product_allocation')
                ->select('emp_product_allocation.*')
                ->where('emp_product_allocation.id', $id)
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
        $data = DB::table('emp_product_allocation_details as ead')
            ->leftJoin('employees as e', 'ead.emp_id', '=', 'e.emp_id')
            ->leftJoin('machines as m', 'ead.machine_id', '=', 'm.id')
            ->leftJoin('product as p', 'ead.product_id', '=', 'p.id')
            ->select(
                'ead.*', 
                'e.emp_name_with_initial as employee_name',
                'm.machine as machine_name',
                'p.productname as product_name'
            )
            ->where('ead.allocation_id', $recordID)
            ->where('ead.status', 1)
            ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . ($row->employee_name ?? $row->employee_name) . '</td>'; 
            $htmlTable .= '<td>' . ($row->machine_name ?? 'N/A') . '</td>'; 
            $htmlTable .= '<td>' . ($row->product_name ?? 'N/A') . '</td>'; 
            $htmlTable .= '<td class="text-right">';
            $htmlTable .= '<button type="button" rowid="'.$row->id.'" class="btnDeletelist btn btn-danger btn-sm">';
            $htmlTable .= '<i class="fas fa-trash-alt"></i>';
            $htmlTable .= '</button>';
            $htmlTable .= '</td>'; 
            $htmlTable .= '<td class="d-none">' . $row->machine_id . '</td>';
            $htmlTable .= '<td class="d-none">' . $row->product_id . '</td>';
            $htmlTable .= '<td class="d-none">ExistingData</td>';
            $htmlTable .= '<td class="d-none"><input type="hidden" name="hiddenid" value="'.$row->id.'"></td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;
    }
   
    public function editlist(Request $request)
    {
        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('emp_product_allocation_details')
                ->select('emp_product_allocation_details.*')
                ->where('emp_product_allocation_details.id', $id)
                ->first(); 
            return response()->json(['result' => $data]);
        }
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
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            EmpProductAllocation::findOrFail($id)->update($form_data);

            $tableData = $request->input('tableData');
        
            foreach ($tableData as $rowtabledata) {
                if($rowtabledata['col_8'] == "Updated" || $rowtabledata['col_8'] == "ExistingData"){
                    $emp_id = $rowtabledata['col_1'];
                    $empname = $rowtabledata['col_2'];
                    $machine_id = $rowtabledata['col_6'];
                    $product_id = $rowtabledata['col_7'];
                    
                    // Find the detail ID from hidden input
                    $detailID = null;
                    if(isset($rowtabledata['col_9'])) {
                        // Extract ID from hidden input HTML
                        preg_match('/value="(\d+)"/', $rowtabledata['col_9'], $matches);
                        if(isset($matches[1])) {
                            $detailID = $matches[1];
                        }
                    }

                    if($detailID) {
                        $EmpProductAllocationDetail = EmpProductAllocationDetail::find($detailID);
                        if($EmpProductAllocationDetail) {
                            $EmpProductAllocationDetail->allocation_id = $id;
                            $EmpProductAllocationDetail->emp_id = $emp_id;
                            $EmpProductAllocationDetail->machine_id = $machine_id;
                            $EmpProductAllocationDetail->product_id = $product_id;
                            $EmpProductAllocationDetail->date = $request->date;
                            $EmpProductAllocationDetail->status = '1';
                            $EmpProductAllocationDetail->updated_by = Auth::id();
                            $EmpProductAllocationDetail->save();
                        }
                    }
                } else if($rowtabledata['col_8'] == "NewData") {
                    $emp_id = $rowtabledata['col_1'];
                    $empname = $rowtabledata['col_2'];
                    $machine_id = $rowtabledata['col_6'];
                    $product_id = $rowtabledata['col_7'];

                    $EmpProductAllocationDetail = new EmpProductAllocationDetail();
                    $EmpProductAllocationDetail->allocation_id = $id;
                    $EmpProductAllocationDetail->emp_id = $emp_id;
                    $EmpProductAllocationDetail->machine_id = $machine_id;
                    $EmpProductAllocationDetail->product_id = $product_id;
                    $EmpProductAllocationDetail->date = $request->date;
                    $EmpProductAllocationDetail->status = '1';
                    $EmpProductAllocationDetail->created_by = Auth::id();
                    $EmpProductAllocationDetail->updated_by = '0';
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
            $data = DB::table('emp_product_allocation')
                ->select('emp_product_allocation.*')
                ->where('emp_product_allocation.id', $id)
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
        $data = DB::table('emp_product_allocation_details as ead')
            ->leftJoin('employees as e', 'ead.emp_id', '=', 'e.emp_id')
            ->leftJoin('machines as m', 'ead.machine_id', '=', 'm.id')
            ->leftJoin('product as p', 'ead.product_id', '=', 'p.id')
            ->select(
                'ead.*', 
                'e.emp_name_with_initial as employee_name',
                'm.machine as machine_name',
                'p.productname as product_name'
            )
            ->where('ead.allocation_id', $recordID)
            ->where('ead.status', 1)
            ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . ($row->employee_name ?? $row->employee_name) . '</td>'; 
            $htmlTable .= '<td>' . ($row->machine_name ?? 'N/A') . '</td>'; 
            $htmlTable .= '<td>' . ($row->product_name ?? 'N/A') . '</td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;
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
}