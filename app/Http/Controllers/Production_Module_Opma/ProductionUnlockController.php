<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\ProductionModule_Opma\EmpProductAllocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class ProductionUnlockController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        return view('Opma_Production.Daily_Production.production_unlock');
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
            'production_status' => '0',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($id)->update($form_data);


         $form_data2 = array(
            'status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );

         DB::table('opma_employee_production')
            ->where('allocation_id', $id)
            ->update($form_data2);

        return response()->json(['success' => 'Employee Product Allocation Successfully Deleted']);
    }
}
