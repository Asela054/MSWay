<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mealallowanceapproved;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MealallowanceapproveController extends Controller
{
    public function index(){
        $user = auth()->user();
        $permission = $user->can('MealAllowanceApprove-list');

        if(!$permission) {
            abort(403);
        }

        $companies=DB::table('companies')->select('*')->get();
        $departments=DB::table('departments')->select('*')->get();
        $remunerations=DB::table('remunerations')->select('*')->where('allocation_method', 'TERMS')->get();
        return view('Mealandattendanceapprove.mealallowance', compact('companies','departments','remunerations'));
    }

    public function mealalowance(Request $request){
        $permission = \Auth::user()->can('MealAllowanceApprove-create');
        if (!$permission) {
            abort(403);
        }

        $department=$request->input('department');
        $remunerationtype=$request->input('remunerationtype');
        $selectedmonth=$request->input('selectedmonth');

        if ($selectedmonth) {
            $firstDate = Carbon::createFromFormat('Y-m', $selectedmonth)->startOfMonth()->toDateString();
            $lastDate = Carbon::createFromFormat('Y-m', $selectedmonth)->endOfMonth()->toDateString();
        }else{
            $firstDate =  $request->input('from_date');
            $lastDate = $request->input('to_date');
        }
        
        $datareturn = [];

        // DB::enableQueryLog();
        $query = DB::table('employees')
        ->join('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
        ->select(
            'employees.emp_id as empid',
            'employees.emp_name_with_initial as emp_name',
            'employees.calling_name',
            'payroll_profiles.basic_salary as basicsalary',
            'payroll_profiles.id as payroll_profiles_id')
        ->where('employees.emp_department', '=', $department)
        ->where('payroll_profiles.payroll_process_type_id', '=',1)
        ->where('employees.deleted', '=',0)
        ->where('employees.is_resigned', '=',0)
        ->orderBy('employees.id')
        ->get();
        // dd(DB::getQueryLog());

        foreach ($query as $row) {
            // if($row->empid==267){
                $empId = $row->empid;
                $empName = $row->emp_name;
                $payrollProfileId = $row->payroll_profiles_id;

                $employeeObj = (object)[
                    'emp_id' => $row->empid,
                    'emp_name_with_initial' => $row->emp_name,
                    'calling_name' => $row->calling_name
                ];

                $totalamount = 0;
                $monthlyremain = 0;
                $allowancetype = null;
                $allowanceamount = 0;
                $allowleave = 0;
                $totalLeaveDays = 0;

                $emp = DB::table('employees')
                ->leftJoin('job_categories', 'job_categories.id' , '=', 'employees.job_category_id')
                ->select('job_categories.id as job_categoryid','job_categories.emp_payroll_workdays as workingdays', 'employees.emp_join_date')
                ->where('employees.emp_id', $empId)
                ->first();

                if ($emp) {
                    $jobCategoryId = $emp->job_categoryid;
                    $workingDays = $emp->workingdays;
                    $joindate = $emp->emp_join_date;
                }

                $allowance = DB::table('salary_adjustments')
                    ->select('salary_adjustments.*')
                    ->where('emp_id', $empId)
                    ->where('job_id', $jobCategoryId)
                    ->where('remuneration_id', $remunerationtype)
                    ->where('approved_status', 1)
                    ->first();

                if ($allowance) {
                    $allowancetype = $allowance->allowance_type;
                    $allowanceamount = $allowance->amount;
                    $allowleave = $allowance->allowleave;
                }
                else{
                    $allowance = DB::table('salary_adjustments')
                    ->select('salary_adjustments.*')
                    ->where('emp_id', 0)
                    ->where('job_id', $jobCategoryId)
                    ->where('remuneration_id', $remunerationtype)
                    ->where('approved_status', 1)
                    ->first();
                    if ($allowance) {
                        $allowancetype = $allowance->allowance_type;
                        $allowanceamount = $allowance->amount;
                        $allowleave = $allowance->allowleave;
                    }

                }
               
                //check meal allowance approved on given date range
                $approvedallowance = DB::table('meal_allowances_approved')
                    ->where('emp_id', $empId)
                    ->where('remuneration_id', $remunerationtype)
                    ->whereBetween('from_date', [$firstDate, $lastDate]) 
                    ->whereBetween('to_date', [$firstDate, $lastDate])  
                    ->first();
                $approvedallowancestatus = $approvedallowance ? 1 : 0;

                $totalWorkingDays = DB::table('attendances')
                    ->where('emp_id', $empId)
                    ->whereBetween('date', [$firstDate, $lastDate])
                    ->whereNull('deleted_at')
                    ->distinct('date')
                    ->count('date');

                $totalWorkingDays;

                if($remunerationtype == 21){
                    $totalWeekDays = DB::table('leaves')
                        ->select(DB::raw('IFNULL(SUM(no_of_days), 0) as total_days'))
                        ->whereBetween('leave_from', [$firstDate, $lastDate])
                        ->where('emp_id', $empId)
                        ->where('status', 'Approved')
                        ->where('leave_type', '!=', '7')
                        ->where('no_of_days', '>=', 1)
                        ->whereRaw('DAYOFWEEK(leave_from) BETWEEN 2 AND 6')
                        ->first();

                    $totalWeekendDays = DB::table('leaves')
                        ->select(DB::raw("
                            IFNULL(SUM(CASE 
                                WHEN no_of_days < 1 THEN 1 
                                ELSE no_of_days 
                            END), 0) as total_days
                        "))
                        ->whereBetween('leave_from', [$firstDate, $lastDate])
                        ->where('emp_id', $empId)
                        ->where('status', 'Approved')
                        ->where('leave_type', '!=', '7')
                        ->whereRaw('DAYOFWEEK(leave_from) = 7')
                        ->first();

                    $totalLeaveDays = $totalWeekDays->total_days + $totalWeekendDays->total_days;
                }
                else if($remunerationtype == 22){
                    $totalDays = DB::table('leaves')
                        ->select(DB::raw('IFNULL(SUM(no_of_days), 0) as total_days'))
                        ->whereBetween('leave_from', [$firstDate, $lastDate])
                        ->where('emp_id', $empId)
                        ->where('status', 'Approved')
                        ->where('leave_type', '!=', '7')
                        ->where('no_of_days', '>', $allowleave)
                        ->first();

                    $totalLeaveDays = $totalDays->total_days;
                }
                // echo $totalLeaveDays."-->".$empId."<br>";

                if($allowancetype == 1){
                    $totalamount = $totalWorkingDays * $allowanceamount;
                    $monthlyremain = $totalamount;
                }else{
                    if($workingDays>0){
                        $daliyamount = $allowanceamount /  $workingDays;
                        
                        if($totalWorkingDays>0 && $totalWorkingDays<20){
                            if($joindate<$firstDate){
                                $totalamount = ($workingDays-$totalLeaveDays) * $daliyamount;
                            }
                            else{
                                $totalamount = $totalWorkingDays * $daliyamount;
                            }
                        }
                        else if($totalWorkingDays>0){$totalamount = ($workingDays-$totalLeaveDays) * $daliyamount;}
                        else{$totalamount = 0;}
                        $monthlyremain = $totalamount;
                        
                        // $totalamount = $totalDays->total_days * $daliyamount;
                        // $monthlyremain = $allowanceamount -  $totalamount;
                    }
                }
                
                $datareturn[] = [
                    'approvedallowancestatus' => $approvedallowancestatus,
                    'empid' => $empId,
                    'emp_name' => EmployeeHelper::getDisplayName($employeeObj),
                    'payroll_Profile' => $payrollProfileId,
                    'allowance_type' => $allowancetype,
                    'working_Days' => $totalWorkingDays,
                    'allowance_amount' => $allowanceamount,
                    'remuneration_id' => $remunerationtype,
                    'total_amount' => number_format($totalamount, 2),
                    'monthly_remain' => number_format($monthlyremain, 2)  
                ];   

            // }
        }

        return response()->json([ 'data' => $datareturn ]);
    }

    public function approveallowances(Request $request)
    {
        $permission = \Auth::user()->can('MealAllowanceApprove-approve');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('dataarry');
        $selectedmonth = $request->input('selectedmonth');

        $current_date_time = Carbon::now()->toDateTimeString();

        if ($selectedmonth) {
            $firstDate = Carbon::createFromFormat('Y-m', $selectedmonth)->startOfMonth()->toDateString();
            $lastDate = Carbon::createFromFormat('Y-m', $selectedmonth)->endOfMonth()->toDateString();
        }else{
            $firstDate =  $request->input('from_date');
            $lastDate = $request->input('to_date');
        }

        foreach ($dataarry as $row) {
            $empid = $row['empid'];
            $epfno = $row['emp_name'];
            $working_Days = $row['working_Days'];
            $allowanceamount = str_replace([','], '', $row['allowance_amount']);
            $totalamount = str_replace([','], '', $row['total_amount']);
            $monthlyremain = str_replace([','], '', $row['monthly_remain']);
            $payrollProfile = $row['payroll_Profile'];
            $allowancetype = $row['allowance_type'];
            $remunerationid = $row['remuneration_id'];

            if( $totalamount != 0){

                $allowance = DB::table('meal_allowances_approved')
                ->where('emp_id', $empid)
                ->where('remuneration_id', $remunerationid)
                ->whereBetween('from_date', [$firstDate, $lastDate]) 
                ->whereBetween('to_date', [$firstDate, $lastDate])  
                ->first();
    
                if($allowance){
                    DB::table('meal_allowances_approved')
                    ->where('emp_id', $empid)
                    ->where('from_date', [$firstDate, $lastDate])
                    ->where('to_date',[$firstDate, $lastDate])
                    ->update([
                        'allowance_type' => $allowancetype,
                        'total_worked_days' => $working_Days,
                        'total_amount' => $totalamount,
                        'monthly_remain' => $monthlyremain,
                        'remuneration_id' => $remunerationid,
                        'updated_at' => $current_date_time
                    ]);
    
                    $paysliplast = DB::table('employee_payslips')
                    ->select('emp_payslip_no')
                    ->where('payroll_profile_id', $payrollProfile)
                    ->where('payslip_cancel', 0)
                    ->orderBy('id', 'desc')
                    ->first();

                    if ($paysliplast) {
                        $emp_payslipno = $paysliplast->emp_payslip_no;
                        $newpaylispno =  $emp_payslipno +1;
                    }else{
                        $newpaylispno = 1;
                    }

                    $termpaymentcheck = DB::table('employee_term_payments')
                    ->select('id')
                    ->where('payroll_profile_id', $payrollProfile)
                    ->where('emp_payslip_no', $newpaylispno)
                    ->where('remuneration_id', $remunerationid)
                    ->first();
                    
                    DB::table('employee_term_payments')
                    ->where('id', $termpaymentcheck->id)
                    ->update([
                        'payment_amount' => $monthlyremain,
                        'payment_cancel' => '0',
                        'updated_by' => Auth::id(),
                        'updated_at' => $current_date_time
                    ]);    
    
                }else{
                    $approvedmeal = new Mealallowanceapproved();
                    $approvedmeal->emp_id = $empid;
                    $approvedmeal->from_date = $firstDate;
                    $approvedmeal->to_date = $lastDate;
                    $approvedmeal->allowance_type = $allowancetype;
                    $approvedmeal->total_worked_days = $working_Days;
                    $approvedmeal->total_amount = $totalamount;
                    $approvedmeal->monthly_remain = $monthlyremain;
                    $approvedmeal->remuneration_id = $remunerationid;
                    $approvedmeal->created_at = $current_date_time;
                    $approvedmeal->save();

                    $paysliplast = DB::table('employee_payslips')
                    ->select('emp_payslip_no')
                    ->where('payroll_profile_id', $payrollProfile)
                    ->where('payslip_cancel', 0)
                    ->orderBy('id', 'desc')
                    ->first();

                    if ($paysliplast) {
                        $emp_payslipno = $paysliplast->emp_payslip_no;
                        $newpaylispno =  $emp_payslipno +1;
                    }else{
                        $newpaylispno = 1;
                    }

                    $termpaymentcheck = DB::table('employee_term_payments')
                    ->select('id')
                    ->where('payroll_profile_id', $payrollProfile)
                    ->where('emp_payslip_no', $newpaylispno)
                    ->where('remuneration_id', $remunerationid)
                    ->first();
                    
                    if($termpaymentcheck){
                        DB::table('employee_term_payments')
                        ->where('id', $termpaymentcheck->id)
                        ->update([
                            'payment_amount' => $monthlyremain,
                            'payment_cancel' => '0',
                            'updated_by' => Auth::id(),
                            'updated_at' => $current_date_time
                        ]);
                    }
                    else{
                        $termpayment = new EmployeeTermPayment();
                        $termpayment->remuneration_id = $remunerationid;
                        $termpayment->payroll_profile_id = $payrollProfile;
                        $termpayment->emp_payslip_no = $newpaylispno;
                        $termpayment->payment_amount = $monthlyremain;
                        $termpayment->payment_cancel = 0;
                        $termpayment->created_by = Auth::id();
                        $termpayment->created_at = $current_date_time;
                        $termpayment->save(); 
                    }
                }
            }
        }
        return response()->json(['success' => 'Salary Adjustments is successfully Approved']);

    }
}
