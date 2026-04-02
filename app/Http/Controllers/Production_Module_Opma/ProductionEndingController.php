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

          $quntity = $request->input('quantity');
          $desription = $request->input('desription');
          $hidden_id = $request->input('hidden_id');
          $completetime = $request->input('completetime');
          $complete_status = $request->input('complete_status');
          $damage_percentage = $request->input('damage_percentage');
          $damage_qty = $request->input('damage_qty');

        $completdate = Carbon::parse($completetime)->format('Y-m-d');


        $maindata = DB::table('opma_emp_product_allocation')
            ->select('opma_emp_product_allocation.*')
            ->where('opma_emp_product_allocation.id', $hidden_id)
            ->first(); 

          $produtiondate = $maindata->date;
          $machine_id = $maindata->machine_id;
          $product_id = $maindata->product_id;
          $target = $maindata->target;

         $productioncomplete =0;

          $production_differnce = $quntity - $target; 
          $produced_percentage = ($target > 0) ? round(($quntity / $target) * 100, 2) : 0;


          $performance = ($target > 0) ? round((($quntity - $damage_qty) / $target) * 100) : 0;

    
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


                if($produced_percentage > 90){
                      $employeedetails = DB::table('employees')
                        ->where('emp_id', $allocation->emp_id)
                        ->select('emp_department','emp_job_code')
                        ->first();
                    
                    $empdepartment = $employeedetails->emp_department;
                    $emp_jobtitle = $employeedetails->emp_job_code;

                    $amountData = DB::table('opma_production_amount')
                            ->where('department_id',$empdepartment )
                            ->where('jobtitle',$emp_jobtitle )
                            ->first();

                    $employee_amount = $amountData ? $amountData->amount : 0;

                }else{
                    $employee_amount = 0;
                }
                $existingRecord = EmployeeProduction::where('allocation_id', $hidden_id)
                                            ->where('emp_id', $allocation->emp_id)
                                            ->first();

                $data = [
                'allocation_id' => $hidden_id,
                'emp_id' => $allocation->emp_id,
                'date' => $produtiondate,
                'machine_id' => $machine_id,
                'product_id' => $product_id,
                'target' => $target,
                'Produce_qty' => $quntity,
                'difference' => $production_differnce,
                'precentage' => $produced_percentage,
                'amount' => $employee_amount,
                'description' => $desription,
                'damage_precentage' => $damage_percentage,
                'damage_qty' => $damage_qty,
                'perfomance' => $performance,
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
