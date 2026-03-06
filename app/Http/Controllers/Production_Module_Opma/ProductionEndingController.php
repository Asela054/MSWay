<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\ProductionModule_Opma\EmployeeProduction;
use App\ProductionModule_Opma\EmpProductAllocation;
use App\ProductionModule_Opma\EmpProductAllocationDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\Productionempattendace;
use App\ProductionModule_Opma\Productionemptransfers;
use App\ProductionModule_Opma\Productionstatusrecords;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use Illuminate\Support\Facades\Input;

class ProductionEndingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        return view('Opma_Production.Daily_Production.daily_ending');
    }
    
     public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

         $current_date_time = Carbon::now()->toDateTimeString();

          $quntity = $request->input('quntity');
          $desription = $request->input('desription');
          $hidden_id = $request->input('hidden_id');
          $completetime = $request->input('completetime');
          $complete_status = $request->input('complete_status');

        $completdate = Carbon::parse($completetime)->format('Y-m-d');


           $maindata = DB::table('opma_emp_product_allocation')
                ->select('opma_emp_product_allocation.*')
                ->where('opma_emp_product_allocation.id', $hidden_id)
                ->first(); 

          $produtiondate = $maindata->date;
          $machine_id = $maindata->machine_id;
          $product_id = $maindata->product_id;
          $target = $maindata->target;


         $product_unitvalue=0;
         $productioncomplete =0;

          $produced_percentage = ($target > 0) ? round(($quntity / $target) * 100, 2) : 0;

          $percentage_for_query = ($produced_percentage > 100) ? 100 : $produced_percentage;

          $amountData = DB::table('opma_production_amount')
                            ->where('start_precentage', '<=', $percentage_for_query)
                            ->where('end_precentage', '>=', $percentage_for_query)
                            ->first();

           $employee_amount = $amountData ? $amountData->amount : 0;
    

          // get employee count
           $employeeAllocations = DB::table('opma_emp_product_allocation_details')
                            ->where('allocation_id', $hidden_id)
                             ->where('status', 1)
                             ->select('id', 'emp_id')
                            ->get();

          $employeeCount = $employeeAllocations->count();
          $employeeIds = $employeeAllocations->pluck('emp_id')->toArray();
          

        if ($employeeCount > 0) {


            foreach ($employeeAllocations as $allocation) {

                $existingRecord = EmployeeProduction::where('allocation_id', $hidden_id)
                                            ->where('emp_id', $allocation->emp_id)
                                            ->first();

                $data = [
                'allocation_id' => $hidden_id,
                'emp_id' => $allocation->emp_id,
                'date' => $produtiondate,
                'machine_id' => $machine_id,
                'product_id' => $product_id,
                'Produce_qty' => $quntity,
                'precentage' => $percentage_for_query,
                'amount' => $employee_amount,
                'description' => $desription,
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_at' => $current_date_time
                 ];


                    if ($existingRecord) {
                        $existingRecord->update($data);
                    } else {
                        $data['updated_by'] = Auth::id();
                        $data['created_at'] = $current_date_time;
                        EmployeeProduction::create($data);
                    }
            }


        // Create record in production_status_records table
        Productionstatusrecords::create([
            'production_id' => $hidden_id,
            'date' => $completdate, 
            'employee_count' => $employeeCount,
            'timestamp' => $completetime,
            'produced_quntity' => $quntity, 
            'production_status' => 4, 
            'created_by' => Auth::id()
        ]);


        foreach ($employeeIds as $emp_id) {
            Productionempattendace::where('emp_id', $emp_id)
                ->where('production_id', $hidden_id)
                ->where('date', $completdate)
                ->update([
                    'finish_timestamp' => $completetime,
                    'status' => 1,
                    'updated_by' => Auth::id(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]);
        }

        $form_data = array(
                    'full_amount' => $quntity,
                    'production_status' => '4',
                    'complete_status' =>  $productioncomplete,
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time,);
        
        EmpProductAllocation::findOrFail($hidden_id)->update($form_data);

        }
        
         return response()->json(['success' => 'Production Successfully Finished']);
    }

    public function cancelproduction(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-cancel');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

          $cancel_desription = $request->input('cancel_desription');
          $cancel_id = $request->input('cancel_id');


        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'cancel_description' => $cancel_desription,
            'production_status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($cancel_id)->update($form_data);

        return response()->json(['success' => 'Production Successfully Canceled']);

    }

    public function startproduction(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $current_date_time = Carbon::now()->toDateTimeString();

          $starttime = $request->input('starttime');
          $start_id = $request->input('start_id');

          $startdate = Carbon::parse($starttime)->format('Y-m-d');

          $employeeDetails = DB::table('opma_emp_product_allocation_details')
            ->where('allocation_id', $start_id)
            ->where('status', 1)
            ->select('id', 'emp_id')
            ->get();

        // Get employee count
        $employeeCount = $employeeDetails->count();
        
        // Get employee IDs as an array
        $employeeIds = $employeeDetails->pluck('emp_id')->toArray();

        // Create record in production_status_records table
        Productionstatusrecords::create([
            'production_id' => $start_id,
            'date' => $startdate, 
            'employee_count' => $employeeCount,
            'timestamp' => $starttime,
            'produced_quntity' => 0, 
            'production_status' => 1, 
            'created_by' => Auth::id()
        ]);


         foreach ($employeeIds as $emp_id) {
            Productionempattendace::create([
                'emp_id' => $emp_id,
                'production_id' => $start_id,
                'date' => $startdate,
                'start_timestmp' => $starttime,
                'finish_timestamp' => null, 
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);
        }

        
        $form_data = array(
            'production_status' => '1',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($start_id)->update($form_data);

        return response()->json(['success' => 'Production Start Successfully']);
    }


  

     public function employeeproduction()
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $machines = DB::table('opma_machines')
            ->select('id', 'machine')
            ->get();

        $products = DB::table('opma_styles')
            ->select('id', 'title','code')
            ->get();

        return view('Opma_Production.Daily_Production.employee_production', compact('machines', 'products'));
    }


}
