<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\EmployeeTermPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionTaskApproveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Production-Task-Approve-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

         $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Addition')->get();

        return view('Opma_Production.Daily_Production.task_production_approve',compact('remunerations'));
    }

    public function generateproductiontask(Request $request){
         $user = Auth::user();
        $permission = $user->can('Production-Task-Approve-Create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');


            $query = DB::table('employees')
        ->select(
            'employees.id as emp_auto_id',
            'employees.emp_id',
            'employees.emp_name_with_initial'
        )
        ->join('opma_daily_production_summary', 'employees.emp_id', '=', 'opma_daily_production_summary.emp_id')
        ->where('employees.deleted', 0)
        ->where('employees.is_resigned', 0);
        if ($employee != '') {
            $query->where('employees.emp_id', $employee);
        }
        $query->groupBy('employees.emp_id');
        $results = $query->get();
        

          foreach ($results as $record) {

            $productionQuery = DB::table('opma_daily_production_summary')
            ->where('emp_id', $record->emp_id)
            ->whereBetween('date', [$from_date, $to_date]);
        
            $targetTotal = $productionQuery->sum('target');
            $produceTotal = $productionQuery->sum('produce');
            $bonusTotal = $productionQuery->sum('bonus');

            $emp_perfomance = ($targetTotal > 0) ? round(($produceTotal / $targetTotal) * 100) : 0;

            if($emp_perfomance > 50){

                  $performanceAmount = DB::table('opma_performance_amount')
                            ->where('emp_id', $record->emp_id)
                            ->select('amount')
                            ->first();
                        
                        if($performanceAmount) {
                            $baseAmount = $performanceAmount->amount;
                            // Check if performance is 90% or more
                            if($emp_perfomance >= 90) {
                                // Full amount
                                $emp_perf_amount = $baseAmount;
                            } else {
                                // Calculate based on performance percentage for 50% to 89%
                                $emp_perf_amount = ($emp_perfomance / 100) * $baseAmount;
                            }
                        } else {
                            $emp_perf_amount = 0;
                        }

            }else{
                 $emp_perf_amount = 0;
            }

            $data[] = [
                        'emp_auto_id' => $record->emp_auto_id,
                        'emp_id' => $record->emp_id,
                        'emp_name_with_initial' => $record->emp_name_with_initial,
                        'target_total' =>round($targetTotal, 2),
                        'produce_total' =>round($produceTotal, 2),
                        'bonus_total' =>round($bonusTotal, 2),
                        'perfomance' =>round($emp_perfomance, 2), 
                        'perfomance_total' =>round($emp_perf_amount, 2), 
                    ];
          }

         return response()->json(['data' => $data ?? []]);
    }


      public function approveproductiontask(Request $request)
    {

        $permission = \Auth::user()->can('Production-Task-Approve-Create');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('dataarry');
         $profomanceremunerationid = $request->input('remunitiontype');
         $bonusremunerationid = $request->input('remunitiontypebonus');

        
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $perfomance_total = str_replace([','], '', $row['perfomance_total']);
            $bonus_total = str_replace([','], '', $row['bonus_total']);
            $autoid = $row['emp_auto_id'];

            $profiles = DB::table('payroll_profiles')
            ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
            ->where('payroll_profiles.emp_id', $autoid)
            ->select('payroll_profiles.id as payroll_profile_id')
            ->first();

        if ($profiles) {

            $paysliplast = DB::table('employee_payslips')
                ->select('emp_payslip_no')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('payslip_cancel', 0)
                ->orderBy('id', 'desc')
                ->first();

            if ($paysliplast) {
                $emp_payslipno = $paysliplast->emp_payslip_no;
                $newpaylispno =  $emp_payslipno +1;
            }else{
                $newpaylispno = 1;
            }


            // Apply Performance total
             if($perfomance_total != 0){

                $termpaymentcheck = DB::table('employee_term_payments')
                ->select('id')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('emp_payslip_no', $newpaylispno)
                ->where('remuneration_id', $profomanceremunerationid)
                ->first();
            
                if($termpaymentcheck){
                    DB::table('employee_term_payments')
                    ->where('id', $termpaymentcheck->id)
                    ->update([
                        'payment_amount' => $perfomance_total,
                        'payment_cancel' => '0',
                        'updated_by' => Auth::id(),
                        'updated_at' => $current_date_time
                    ]);
                }
                else{
                    $termpayment = new EmployeeTermPayment();
                    $termpayment->remuneration_id = $profomanceremunerationid;
                    $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                    $termpayment->emp_payslip_no = $newpaylispno;
                    $termpayment->payment_amount = $perfomance_total;
                    $termpayment->payment_cancel = 0;
                    $termpayment->created_by = Auth::id();
                    $termpayment->created_at = $current_date_time;
                    $termpayment->save(); 
                }
            }


             // Apply Bonus  total
             if($bonus_total != 0){

                $termpaymentcheck = DB::table('employee_term_payments')
                ->select('id')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('emp_payslip_no', $newpaylispno)
                ->where('remuneration_id', $bonusremunerationid)
                ->first();
            
                if($termpaymentcheck){
                    DB::table('employee_term_payments')
                    ->where('id', $termpaymentcheck->id)
                    ->update([
                        'payment_amount' => $bonus_total,
                        'payment_cancel' => '0',
                        'updated_by' => Auth::id(),
                        'updated_at' => $current_date_time
                    ]);
                }
                else{
                    $termpayment = new EmployeeTermPayment();
                    $termpayment->remuneration_id = $bonusremunerationid;
                    $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                    $termpayment->emp_payslip_no = $newpaylispno;
                    $termpayment->payment_amount = $bonus_total;
                    $termpayment->payment_cancel = 0;
                    $termpayment->created_by = Auth::id();
                    $termpayment->created_at = $current_date_time;
                    $termpayment->save(); 
                }
            }
            
        }
        else{
            continue;
        }

        }

        return response()->json(['success' => 'Production Insentive is successfully Approved']);
    }


}
