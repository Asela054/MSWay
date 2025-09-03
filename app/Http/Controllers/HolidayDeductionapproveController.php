<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\Holidaydiductionapproved;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HolidayDeductionapproveController extends Controller
{
    public function index(){
        $user = auth()->user();
        $permission = $user->can('Holiday-DeductionApprove-list');

        if(!$permission) {
            abort(403);
        }

        $companies=DB::table('companies')->select('*')->get();
        $departments=DB::table('departments')->select('*')->get();
        $remunerations=DB::table('remunerations')->select('*')->where('allocation_method', 'TERMS')->get();
        return view('Holiday.holiday_deduction_approve', compact('companies','departments','remunerations'));
    }

    public function holidaydeduction(Request $request){

        $permission = \Auth::user()->can('Holiday-DeductionApprove-list');
        if (!$permission) {
            abort(403);
        }

        $department=$request->input('department');
        $selectedmonth=$request->input('selectedmonth');
        $remunerationtype=$request->input('remunerationtype');

        if ($selectedmonth) {
            $firstDate = Carbon::createFromFormat('Y-m', $selectedmonth)->startOfMonth()->toDateString();
            $lastDate = Carbon::createFromFormat('Y-m', $selectedmonth)->endOfMonth()->toDateString();
        }else{
            $firstDate =  $request->input('from_date');
            $lastDate = $request->input('to_date');
        }
        

        $datareturn = [];

        $query = DB::table('employees')
        ->join('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
        ->select(
            'employees.emp_id as empid',
            'employees.emp_name_with_initial as emp_name',
            'payroll_profiles.basic_salary as basicsalary',
            'payroll_profiles.id as payroll_profiles_id')
        ->where('employees.emp_department', '=', $department)
        ->where('payroll_profiles.payroll_process_type_id', '=',1)
        ->where('employees.deleted', '=',0)
        ->where('employees.is_resigned', '=',0)
        ->orderBy('employees.id')
        ->get();

        foreach ($query as $row) {
            // if($row->empid==250){
                $empId = $row->empid;
                $empName = $row->emp_name;
                $payrollProfileId = $row->payroll_profiles_id;

                
                // $absentdaycount = (new \App\Holidaydiductionapproved)->absentdayscounts($empId, $firstDate, $lastDate);
                $totalDays = DB::table('leaves')
                    ->select(DB::raw('IFNULL(SUM(no_of_days), 0) as total_days'))
                    ->whereBetween('leave_from', [$firstDate, $lastDate])
                    ->where('emp_id', $empId)
                    ->where('status', 'Approved')
                    ->where('leave_type', '!=', '7')
                    ->first();                           
                
                $monthlytotal = 0;
                $totalAmount = 0;
                $deductionamount = 0;
                $daycount = 0;
                
                $emp = DB::table('employees')
                ->leftJoin('job_categories', 'job_categories.id' , '=', 'employees.job_category_id')
                ->select('job_categories.id as job_categoryid','job_categories.emp_payroll_workdays as workingdays')
                ->where('employees.emp_id', $empId)
                ->first();

                if ($emp) {
                    $jobCategoryId = $emp->job_categoryid;
                }

                //check holiday deductions approved on given date range
                $approveholidayductions = DB::table('holiday_deductions_approved')
                ->where('emp_id', $empId)
                ->where('remuneration_id', $remunerationtype)
                ->whereBetween('from_date', [$firstDate, $lastDate]) 
                ->whereBetween('to_date', [$firstDate, $lastDate])  
                ->first();
                $approveholidayductionsstatus = $approveholidayductions ? 1 : 0;

                $leavededutionamount = DB::table('holiday_deductions')
                ->select('amount', 'day_count')
                ->where('job_id', $jobCategoryId)
                ->where('remuneration_id', $remunerationtype)
                ->orderBy('day_count', 'desc')
                ->first();
                
                if($leavededutionamount){
                    $monthlytotal = $leavededutionamount->amount;
                    $daycount = $leavededutionamount->day_count;

                    $leavededuction = DB::table('holiday_deductions')
                    ->select('*')
                    ->where('job_id', $jobCategoryId)
                    ->where('remuneration_id', $remunerationtype)
                    ->orderBy('day_count', 'desc')
                    ->get();

                    foreach($leavededuction as $dataleavededuction){
                        if($dataleavededuction->day_count==$totalDays->total_days){
                            $totalAmount = $monthlytotal-$dataleavededuction->amount;
                            $deductionamount = $dataleavededuction->amount;
                            break;
                        }
                        else{
                            $totalAmount = 0;
                            $deductionamount = $monthlytotal;
                        }
                    }

                    // dd($totalAmount, $deductionamount);
                }
                
                if($totalAmount==0 && $totalDays->total_days==0){
                    $totalAmount = $monthlytotal;
                }

                $datareturn[] = [
                    'deductionsstatus' => $approveholidayductionsstatus,
                    'empid' => $empId,
                    'emp_name' => $empName,
                    'payroll_Profile' => $payrollProfileId,
                    'absent_Days' => $totalDays->total_days,
                    'remuneration_id' => $remunerationtype,
                    'total_amount' => number_format($deductionamount, 2),
                    'monthly_remain' => number_format($totalAmount, 2)  
                ];  
            // }   
        }
        return response()->json([ 'data' => $datareturn ]);
    }

    public function approveldeduction(Request $request)
    {
        $permission = \Auth::user()->can('Holiday-DeductionApprove-apprve');
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
            $Absent_Days = $row['Absent_Days'];
            $totalamount = $row['total_amount'];
            $totalamount = str_replace([','], '', $totalamount);
            $monthlyremain = $row['monthly_remain'];
            $monthlyremain = str_replace([','], '', $monthlyremain);
            $payrollProfile = $row['payroll_Profile'];
            $remunerationid = $row['remuneration_id'];


            if( $totalamount != 0){

                $allowance = DB::table('holiday_deductions_approved')
                ->where('emp_id', $empid)
                ->where('remuneration_id', $remunerationid)
                ->whereBetween('from_date', [$firstDate, $lastDate]) 
                ->whereBetween('to_date', [$firstDate, $lastDate])  
                ->first();
    
                if($allowance){
                    DB::table('holiday_deductions_approved')
                    ->where('emp_id', $empid)
                    ->where('from_date', [$firstDate, $lastDate])
                    ->where('to_date',[$firstDate, $lastDate])
                    ->update([
                        'total_absent_days' => $Absent_Days,
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
                    $holidaydeduction = new Holidaydiductionapproved();
                    $holidaydeduction->emp_id = $empid;
                    $holidaydeduction->from_date = $firstDate;
                    $holidaydeduction->to_date = $lastDate;
                    $holidaydeduction->total_absent_days = $Absent_Days;
                    $holidaydeduction->total_amount = $totalamount;
                    $holidaydeduction->monthly_remain = $monthlyremain;
                    $holidaydeduction->remuneration_id = $remunerationid;
                    $holidaydeduction->created_at = $current_date_time;
                    $holidaydeduction->save();

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
        return response()->json(['success' => 'Leave Deduction is successfully Approved']);
    }
}
