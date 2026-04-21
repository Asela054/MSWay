<?php

namespace App\Http\Controllers\Production_Module_Opma;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\ProductionemployeeDailyEnding;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionDailyApproveController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Production-Task-Approve-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        return view('Opma_Production.Daily_Production.daily_production_approve');
    }

    public function generatedailysummary(Request $request){
         $user = Auth::user();
        $permission = $user->can('Production-Task-Approve-Create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $employee = $request->get('employee');
        $from_date = $request->get('from_date');

        $query = DB::table('employees')
        ->select(
            'employees.id as emp_auto_id',
            'employees.emp_id',
            'employees.emp_name_with_initial'
        )
        ->leftjoin('opma_employee_production', 'employees.emp_id', '=', 'opma_employee_production.emp_id')
        ->where('employees.deleted', 0)
        ->where('employees.is_resigned', 0)
        ->where('opma_employee_production.date', $from_date);
        if ($employee != '') {
            $query->where('employees.emp_id', $employee);
        }
        $query->groupBy('employees.emp_id');
        $results = $query->get();

      foreach ($results as $record) {
                $productionRecords = DB::table('opma_employee_production')
                    ->where('emp_id', $record->emp_id)
                    ->where('date', $from_date)
                    ->get();
                
                $totalTarget = 0;
                $totalProduceQty = 0;
                $totalDifference = 0;
                $totalAmount = 0;
                $totaldamage = 0;

                foreach ($productionRecords as $production) {
                    $totalTarget += $production->target ?? 0;
                    $totalProduceQty += $production->Produce_qty ?? 0;
                    $totalDifference += $production->difference ?? 0;
                    $totalAmount += $production->amount ?? 0;
                    $totaldamage += $production->damage_qty ?? 0;
                }
                
                $dailyAverage = 0;
                    if ($totalTarget > 0) {
                        $dailyAverage = ($totalProduceQty / $totalTarget) * 100;
                    }
                
              $dailysummary = DB::table('opma_daily_production_summary')
                ->select('opma_daily_production_summary.*')
                ->where('emp_id', $record->emp_id)
                ->where('date',  $from_date)
                ->first();

                $status = $dailysummary ? 1 : 0;

                $data[] = [
                    'emp_auto_id' => $record->emp_auto_id,
                    'emp_id' => $record->emp_id,
                    'recorddate' => $from_date,
                    'emp_name_with_initial' => $record->emp_name_with_initial,
                    'total_target' => round($totalTarget, 2),
                    'total_produce_qty' => round($totalProduceQty, 2),
                    'total_difference' => round($totalDifference, 2),
                    'total_amount' => round($totalAmount, 2),
                    'total_damage' => round($totaldamage, 2),
                    'daily_aveg' => round($dailyAverage, 2),
                     'status' => $status,
                ];
            }

        return response()->json(['data' => $data ?? []]);
    }

       public function approvedailysummary(Request $request)
    {

        $permission = \Auth::user()->can('Production-Task-Approve-Create');
        if (!$permission) {
            abort(403);
        }
        $dataarry = $request->input('dataarry');
        $date = $request->input('from_date');

        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $total_target = $row['total_target'];
            $total_produce_qty = $row['total_produce_qty'];
            $total_difference = $row['total_difference'];
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
                        'updated_by' => Auth::id(),
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
                    $dailyending->created_by = Auth::id();
                    $dailyending->created_at = $current_date_time;
                    $dailyending->save(); 
                }
            }


        }

        return response()->json(['success' => 'Production Daily Summary is successfully Approved']);
    }



}
