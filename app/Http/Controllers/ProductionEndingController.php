<?php

namespace App\Http\Controllers;

use App\EmployeeProduction;
use App\EmpProductAllocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

           $maindata = DB::table('emp_product_allocation')
                ->select('emp_product_allocation.*','product.semi_price as semi_price','product.full_price as full_price')
                ->leftJoin('product', 'emp_product_allocation.product_id', '=', 'product.id')
                ->where('emp_product_allocation.id', $hidden_id)
                ->first(); 

          $produtiondate = $maindata->date;
          $machine_id = $maindata->machine_id;
          $product_id = $maindata->product_id;

          // get semi and full price from price metrix

           $prodcumachine = DB::table('product_machines')
                ->select('product_machines.*')
                ->where('product_machines.product_id', $product_id)
                ->where('product_machines.machine_id', $machine_id)
                ->first(); 

        $semi_price =  $prodcumachine->semi_price;
        $full_price =  $prodcumachine->full_price;


          $product_unitvalue=0;

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


          // get employee count
           $employeeAllocations = DB::table('emp_product_allocation_details')
                            ->where('allocation_id', $hidden_id)
                            ->get();


          $employeeCount = $employeeAllocations->count();

          

        if ($employeeCount > 0) {

                     $step01 = $product_unitvalue * $quntity;
            $employee_amount = round($step01 / $employeeCount, 2);

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

        $form_data = array(
                    'product_type' => $product_type,
                    'semi_amount' => $semiquntity,
                    'full_amount' => $fullquntity,
                    'production_status' => '2',
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time,
                );
        
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

}
