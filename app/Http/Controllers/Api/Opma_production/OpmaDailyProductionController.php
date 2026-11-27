<?php

namespace App\Http\Controllers\Api\Opma_production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\ProductionemployeeDailyEnding;
use App\Services\Opma_Daily_approvePolicy_Service;
use Illuminate\Support\Facades\DB;
use App\ProductionModule_Opma\EmployeeProduction;
use Auth;
use Carbon\Carbon;
use Datatables;
use App\Http\Controllers\Api\BaseController;

class OpmaDailyProductionController extends Controller
{
    protected $Opma_Daily_approvePolicy_Service;

     public function __construct(Opma_Daily_approvePolicy_Service $Opma_Daily_approvePolicy_Service)
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }



        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }

        $this->Opma_Daily_approvePolicy_Service = $Opma_Daily_approvePolicy_Service;
    }

    public function Productionfinish(Request $request)
    {
        $current_date_time = Carbon::now()->toDateTimeString();

        $shift_id       = $request->input('shift_id');
        $production_id  = $request->input('production_id');
        $loged_user_id  = $request->input('user_id');
        $produtiondate  = $request->input('production_date');
        $target         = $request->input('target_quantity');
        $quntity        = $request->input('produce_quantity');
        $damage_qty     = $request->input('damage_qty', 0);
        $machine_id     = $request->input('machine_id');
        $machine_name   = $request->input('machine_name');
        $product_id     = $request->input('style_id');
        $product_name   = $request->input('style_name');

        // Machine ekata allocate karala, e dawase e shift eke roster wela inna employees
        $employees = DB::table('opma_machine_employees as me')
            ->leftjoin('employee_roster_details as r', 'r.emp_id', '=', 'me.emp_id')
            ->leftjoin('employees as e', 'e.emp_id', '=', 'me.emp_id')
            ->where('me.opma_machine_id', $machine_id)
            ->where('r.shift_id', $shift_id)
            ->whereDate('r.work_date', '=', $produtiondate)
            ->select('me.emp_id', 'e.emp_department', 'e.emp_job_code')
            ->distinct()
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No employees rostered for this machine, shift and date'
            ], 404);
        }

        $production_differnce = $quntity - $target;
        $produced_percentage  = ($target > 0) ? round(($quntity / $target) * 100, 2) : 0;
        $performance          = ($target > 0) ? round((($quntity - $damage_qty) / $target) * 100) : 0;

        $emp_ids = [];

        DB::beginTransaction();

        try {
            foreach ($employees as $employee) {
                $employee_amount = 0;

                if ($produced_percentage > 90) {
                    $amountData = DB::table('opma_production_amount')
                        ->where('department_id', $employee->emp_department)
                        ->where('jobtitle', $employee->emp_job_code)
                        ->first();

                    $employee_amount = $amountData ? $amountData->amount : 0;
                }

                $data = [
                    'allocation_id'     => $production_id,
                    'emp_id'            => $employee->emp_id,
                    'date'              => $produtiondate,
                    'machine_id'        => $machine_id,
                    'product_id'        => $product_id,
                    'target'            => $target,
                    'Produce_qty'       => $quntity,
                    'difference'        => $production_differnce,
                    'precentage'        => $produced_percentage,
                    'amount'            => $employee_amount,
                    'description'       => '',
                    'damage_precentage' => '',
                    'damage_qty'        => $damage_qty,
                    'perfomance'        => $performance,
                    'mchine_name'       => $machine_name,
                    'product_name'      => $product_name,
                    'shift_id'      => $shift_id,
                    'status'            => 1,
                    'updated_at'        => $current_date_time
                ];

                $existingRecord = EmployeeProduction::where('allocation_id', $production_id)
                    ->where('emp_id', $employee->emp_id)
                    ->where('date', $produtiondate)
                    ->first();

                if ($existingRecord) {
                    $data['updated_by'] = $loged_user_id;
                    $existingRecord->update($data);
                } else {
                    $data['created_by'] = $loged_user_id;
                    $data['created_at'] = $current_date_time;
                    EmployeeProduction::create($data);
                }

                $emp_ids[] = $employee->emp_id;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return (new BaseController)->sendResponse($emp_ids, 'Production Finishing is successfully Saved');
    }

    public function approvedailysummary(Request $request)
    {

        $dataarry = $request->input('dataarry');
        $date = $request->input('today_date');
        $loged_user_id = $request->input('loged_user_id');

        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $total_target = $row['total_target'];
            $total_produce_qty = $row['total_produce_qty'];
            $total_difference = $row['total_difference'];
            $daily_aveg = $row['daily_aveg'];
            $total_amount = str_replace([','], '', $row['total_amount']);
            $total_damage = $row['total_damage'];

            if($total_target != 0){

                $dailysummary = DB::table('opma_daily_production_summary')
                ->select('opma_daily_production_summary.*')
                ->where('emp_id', $empid)
                ->where('date',  $date)
                ->first();
            
                if($dailysummary){
                    DB::table('opma_daily_production_summary')
                    ->where('id', $dailysummary->id)
                    ->update([
                        'target' => $total_target,
                        'produce' => $total_produce_qty,
                        'difference' => $total_difference,
                        'bonus' => $total_amount,
                        'damage' => $total_damage,
                        'updated_by' => $loged_user_id,
                        'updated_at' => $current_date_time
                    ]);
                }
                else{
                    $dailyending = new ProductionemployeeDailyEnding();
                    $dailyending->emp_id = $empid;
                    $dailyending->date = $date;
                    $dailyending->target = $total_target;
                    $dailyending->produce = $total_produce_qty;
                    $dailyending->difference = $total_difference;
                    $dailyending->bonus = $total_amount;
                    $dailyending->damage = $total_damage;
                    $dailyending->created_by = $loged_user_id;
                    $dailyending->created_at = $current_date_time;
                    $dailyending->save(); 
                }

                $this->Opma_Daily_approvePolicy_Service->Production_storeDailyApprove($empid, $date, $total_target, $total_produce_qty, $daily_aveg,$total_amount);
            }


        }

        return (new BaseController)->sendResponse($dataarry, 'Production Daily Summary is successfully Approved');
    }
}
