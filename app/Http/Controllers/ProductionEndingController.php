<?php

namespace App\Http\Controllers;

use App\EmployeeProduction;
use App\EmpProductAllocation;
use App\EmpProductAllocationDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Productionempattendace;
use App\Productionemptransfers;
use App\Productionstatusrecords;
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
        return view('Daily_Production.daily_ending');
    }

    public function productionlist()
    {
        $types = DB::table('emp_product_allocation')
            ->select(
                'emp_product_allocation.*',
                'product.productname as product_name',
                'machines.machine as machine_name'
            )
            ->leftJoin('product', 'emp_product_allocation.product_id', '=', 'product.id')
            ->leftJoin('machines', 'emp_product_allocation.machine_id', '=', 'machines.id')
            ->whereIn('emp_product_allocation.status', [1, 2])
            ->get();

        return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();

                    // Add Finish Production button if status is not already finished
                    if($row->production_status != 2 && $user->can('production-ending-finish')) {
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-success btn-sm" type="button" title="Finish Production"><i class="fas fa-check-circle"></i></button>';
                    }
                    // Add Cancel Production button if status is not already cancelled
                    if($row->production_status != 3 && $user->can('production-ending-cancel')) {
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm" type="button" title="Cancel Production"><i class="fas fa-times-circle"></i></button>';
                    }
            
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
        public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

         $current_date_time = Carbon::now()->toDateTimeString();

          $product_type = $request->input('product_type');
          $semiquntity = $request->input('semiquntity');
          $fullquntity = $request->input('fullquntity');
          $desription = $request->input('desription');
          $hidden_id = $request->input('hidden_id');
          $completetime = $request->input('completetime');
          $complete_status = $request->input('complete_status');

        $completdate = Carbon::parse($completetime)->format('Y-m-d');


           $maindata = DB::table('emp_product_allocation')
                ->select('emp_product_allocation.*','product.semi_price as semi_price','product.full_price as full_price')
                ->leftJoin('product', 'emp_product_allocation.product_id', '=', 'product.id')
                ->where('emp_product_allocation.id', $hidden_id)
                ->first(); 

          $produtiondate = $maindata->date;
          $machine_id = $maindata->machine_id;
          $product_id = $maindata->product_id;

          // get semi and full price from price metrix

           $prodcumachine = DB::table('machines')
                ->select('machines.*')
                ->where('machines.id', $machine_id)
                ->first(); 

        $semi_price =  $prodcumachine->semi_complete;
        $full_price =  $prodcumachine->full_complete;
        $targetcount =  $prodcumachine->target_count;

         $product_unitvalue=0;
         $productioncomplete =0;

        if($targetcount > 0){

            if($targetcount >= $fullquntity){

                $product_unitvalue = $full_price;
                $quntity =  $fullquntity;

                $productioncomplete =1;

            }else{

                $product_unitvalue = $semi_price;
                $quntity =  $fullquntity;

                $productioncomplete =0;
            }
        }else{
             $productioncomplete = $complete_status;

            if($product_type ==="Semi Completed"){

            $product_unitvalue = $semi_price;
            $quntity =  $semiquntity;

            }else if($product_type ==="Full Completed"){

                $product_unitvalue = $full_price;
                $quntity =  $fullquntity;

            }else{

                $product_unitvalue = $full_price +  $semi_price;
                $quntity =  $fullquntity + $semiquntity;
            }

        }

    
          // get employee count
           $employeeAllocations = DB::table('emp_product_allocation_details')
                            ->where('allocation_id', $hidden_id)
                             ->where('status', 1)
                             ->select('id', 'emp_id')
                            ->get();

          $employeeCount = $employeeAllocations->count();
          $employeeIds = $employeeAllocations->pluck('emp_id')->toArray();
          

        if ($employeeCount > 0) {

                     $step01 = $product_unitvalue * $quntity;
            $employee_amount = round($step01 / $employeeCount, 2);

            $employeeqty =  round($quntity / $employeeCount, 2);

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
                'Produce_qty' => $employeeqty,
                'unit_price' => $product_unitvalue,
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
                    'product_type' => $product_type,
                    'semi_amount' => $semiquntity,
                    'full_amount' => $fullquntity,
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

          $employeeDetails = DB::table('emp_product_allocation_details')
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


      public function breakdownproduction(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $current_date_time = Carbon::now()->toDateTimeString();

          $breakdowntime = $request->input('breakdowntime');
          $produceqty = $request->input('produceqty');
          $breakdown_id = $request->input('breakdown_id');

          $breakdowndate = Carbon::parse($breakdowntime)->format('Y-m-d');

          $employeeDetails = DB::table('emp_product_allocation_details')
            ->where('allocation_id', $breakdown_id)
            ->where('status', 1)
            ->select('id', 'emp_id')
            ->get();

        // Get employee count
        $employeeCount = $employeeDetails->count();

        // Create record in production_status_records table
        Productionstatusrecords::create([
            'production_id' => $breakdown_id,
            'date' => $breakdowndate, 
            'employee_count' => $employeeCount,
            'timestamp' => $breakdowntime,
            'produced_quntity' => $produceqty, 
            'production_status' => 2, 
            'created_by' => Auth::id()
        ]);


        
        $form_data = array(
            'production_status' => '2',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($breakdown_id)->update($form_data);

        return response()->json(['success' => 'Production Paused Successfully']);
    }

       public function resumeproduction(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $current_date_time = Carbon::now()->toDateTimeString();

          $resumetime = $request->input('resumetime');
          $resume_id = $request->input('resume_id');

          $resumedate = Carbon::parse($resumetime)->format('Y-m-d');

          $employeeDetails = DB::table('emp_product_allocation_details')
            ->where('allocation_id', $resume_id)
            ->where('status', 1)
            ->select('id', 'emp_id')
            ->get();

        // Get employee count
        $employeeCount = $employeeDetails->count();

         $breakdownrecord = DB::table('production_status_records')
            ->where('production_id', $resume_id)
            ->where('production_status', 2)
            ->select('id', 'produced_quntity')
            ->first();

            if ($breakdownrecord) {
                $produceqty = $breakdownrecord->produced_quntity;
            } else {
                $produceqty = 0;
            }

        // Create record in production_status_records table
        Productionstatusrecords::create([
            'production_id' => $resume_id,
            'date' => $resumedate, 
            'employee_count' => $employeeCount,
            'timestamp' => $resumetime,
            'produced_quntity' => $produceqty, 
            'production_status' => 1, 
            'created_by' => Auth::id()
        ]);


        
        $form_data = array(
            'production_status' => '1',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($resume_id)->update($form_data);

        return response()->json(['success' => 'Production Resumed Successfully']);
    }

     public function employeeproduction()
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $machines = DB::table('machines')
            ->select('id', 'machine')
            ->get();

        $products = DB::table('product')
            ->select('id', 'productname')
            ->get();

        return view('Daily_Production.employee_production', compact('machines', 'products'));
    }


     public function employee_list_production(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;
            $offset = ($page - 1) * $resultCount;
            $term = Input::get("term");

            $query = DB::table('employees')
                ->where(function($q) use ($term) {
                    $q->where('employees.calling_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('employees.emp_name_with_initial', 'LIKE', '%' . $term . '%');
                })
                ->where('deleted', 0)
                ->where('is_resigned', 0);

            $breeds = $query
                ->select(
                    DB::raw('DISTINCT employees.emp_id as id'),
                    DB::raw('CONCAT(employees.emp_name_with_initial, " - ", employees.calling_name) as text')
                )
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get();

            $count = Count($breeds); // Get count from the actual results

            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = [
                "results" => $breeds,
                "pagination" => [
                    "more" => $morePages
                ]
            ];

            return response()->json($results);
        }
    }


     public function addingproductionemployees(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            DB::beginTransaction();


            $addingtime = $request->input('addingtime');
            $currentproduceqty = $request->input('currentproduceqty');
            $allocation_id = $request->input('allocation_id');
            $tableData = $request->input('tableData');

            $addingdate = Carbon::parse($addingtime)->format('Y-m-d');

            foreach ($tableData as $rowtabledata) {
                $emp_id = $rowtabledata['col_1'];
                $empname = $rowtabledata['col_2'];

                $EmpProductAllocationDetail = new EmpProductAllocationDetail();
                $EmpProductAllocationDetail->allocation_id = $allocation_id;
                $EmpProductAllocationDetail->emp_id = $emp_id;
                $EmpProductAllocationDetail->date = $addingdate;
                $EmpProductAllocationDetail->status = '1';
                $EmpProductAllocationDetail->adding_status = '2';
                $EmpProductAllocationDetail->created_by = Auth::id();
                $EmpProductAllocationDetail->updated_by = '0';
                $EmpProductAllocationDetail->save();

                $allocation_detailed_id = $EmpProductAllocationDetail->id;

                $attendanceRecord = Productionempattendace::create([
                    'emp_id' => $emp_id,
                    'production_id' => $allocation_id,
                    'date' => $addingdate,
                    'start_timestmp' => $addingtime,
                    'finish_timestamp' => null, 
                    'status' => 1,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id()
                ]);

                 $attendance_record_id = $attendanceRecord->id;

                    Productionemptransfers::create([
                        'production_id' => $allocation_id,
                        'allocation_detailed_id' => $allocation_detailed_id,
                        'attendance_record_id' => $attendance_record_id,
                        'current_qty' => $currentproduceqty,
                        'status' => 1
                    ]);
            }

            DB::commit();
            return response()->json(['success' => 'Employee Product Allocation Successfully Inserted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => ['An error occurred while saving data: ' . $e->getMessage()]], 422);
        }
    }


    public function removeproductionemployees(Request $request)
{
    $user = Auth::user();
    $permission = $user->can('production-ending-finish');
    if (!$permission) {
        return response()->json(['error' => 'UnAuthorized'], 401);
    }

    try {
        DB::beginTransaction();

        $addingtime = $request->input('addingtime');
        $currentproduceqty = $request->input('currentproduceqty');
        $allocation_id = $request->input('allocation_id');
        $rowid = $request->input('id');

        $addingdate = Carbon::parse($addingtime)->format('Y-m-d');

         $current_date_time = Carbon::now()->toDateTimeString();


        $EmpProductAllocationDetail = EmpProductAllocationDetail::find($rowid);
        if ($EmpProductAllocationDetail) {
            $EmpProductAllocationDetail->status = '3'; 
            $EmpProductAllocationDetail->updated_by = Auth::id();
            $EmpProductAllocationDetail->updated_at = $current_date_time;
            $EmpProductAllocationDetail->save();


            $attendanceRecord = Productionempattendace::where('emp_id', $EmpProductAllocationDetail->emp_id)
                ->where('production_id', $allocation_id)
                ->where('date', $addingdate)
                ->first();

            if ($attendanceRecord) {

                $attendanceRecord->update([
                    'finish_timestamp' => $addingtime, 
                    'status' => 3, 
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time
                ]);

                $attendance_record_id = $attendanceRecord->id;

                Productionemptransfers::create([
                    'production_id' => $allocation_id,
                    'allocation_detailed_id' =>  $rowid,
                    'attendance_record_id' => $attendance_record_id,
                    'current_qty' => $currentproduceqty,
                    'status' => 3
                ]);
            }
        }

        DB::commit();
        return response()->json(['success' => 'Employee Removed from Production Successfully']);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['errors' => ['An error occurred while removing employee: ' . $e->getMessage()]], 422);
    }
}

}
