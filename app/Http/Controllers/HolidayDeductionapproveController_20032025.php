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
        return view('Holiday.holiday_deduction_approve', compact('companies','departments'));
    }

    public function holidaydeduction(Request $request){

        $permission = \Auth::user()->can('Holiday-DeductionApprove-list');
        if (!$permission) {
            abort(403);
        }

        $department=$request->input('department');
        $selectedmonth=$request->input('selectedmonth');

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
        ->orderBy('employees.id')
        ->get();

        foreach ($query as $row) {
                $empId = $row->empid;
                $empName = $row->emp_name;
                $payrollProfileId = $row->payroll_profiles_id;

                $monthlyremain = 0;

                $absentdaycount = (new \App\Holidaydiductionapproved)->absentdayscounts($empId, $firstDate, $lastDate);

                $totalAmount = 0;
              
                $emp = DB::table('employees')
                ->leftJoin('job_categories', 'job_categories.id' , '=', 'employees.job_category_id')
                ->select('job_categories.id as job_categoryid','job_categories.emp_payroll_workdays as workingdays')
                ->where('employees.emp_id', $empId)
                ->first();

                if ($emp) {
                    $jobCategoryId = $emp->job_categoryid;
                }



                $daycount = DB::table('holiday_deductions')
                ->select('holiday_deductions.*')
                ->where('job_id', $jobCategoryId)
                ->orderBy('day_count', 'desc')
                ->first();

                if ($daycount) {
                    $allocateddaycount = $daycount->day_count;
                    $monthlytotal = $daycount->monthly_total;
                    $dayamount = $daycount->amount;
                    $remunerationid = $daycount->remuneration_id;

                   

                    if ($absentdaycount <= $allocateddaycount){

                        $currentDayAmount = DB::table('holiday_deductions')
                        ->where('job_id', $jobCategoryId)
                        ->where('day_count', $absentdaycount)
                        ->value('amount');

                        $totalAmount = $currentDayAmount;
                    }else{
                        $totalAmount =  $dayamount;
                    }
                    $monthlyremain = $monthlytotal -  $totalAmount;

                }

            $datareturn[] = [
                'empid' => $empId,
                'emp_name' => $empName,
                'payroll_Profile' => $payrollProfileId,
                'absent_Days' => $absentdaycount,
                'remuneration_id' => $remunerationid,
                'total_amount' => number_format($totalAmount, 2),
                'monthly_remain' => number_format($monthlyremain, 2)  
            ];     
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
            $monthlyremain = $row['monthly_remain'];
            $payrollProfile = $row['payroll_Profile'];
            $remunerationid = $row['remuneration_id'];


            if( $totalamount != 0){

                $allowance = DB::table('holiday_deductions_approved')
                ->where('emp_id', $empid)
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
                        'updated_at' => $current_date_time
                    ]);
    
                    $payslip = DB::table('employee_payslips')
                    ->where('payroll_profile_id', $payrollProfile)
                    ->whereBetween('payment_period_fr', [$firstDate, $lastDate]) 
                    ->whereBetween('payment_period_to', [$firstDate, $lastDate])  
                    ->where('payslip_cancel', 0)
                    ->first();
    
                    if ($payslip) {
                        $emp_payslipno = $payslip->emp_payslip_no;
                    }
    
                    DB::table('employee_term_payments')
                    ->where('remuneration_id', $remunerationid)
                    ->where('payroll_profile_id', $payrollProfile)
                    ->where('emp_payslip_no', $emp_payslipno)
                    ->where('payment_cancel', 0)
                    ->update([
                        'payment_amount' => $monthlyremain,
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
                        $holidaydeduction->created_at = $current_date_time;
                        $holidaydeduction->save();
    
    
                        // dd($totalamount, $monthlyremain);

                        $paysliplast = DB::table('employee_payslips')
                        ->where('payroll_profile_id', $payrollProfile)
                        ->where('payslip_cancel', 0)
                        ->where('payslip_approved', 1)
                        ->orderBy('id', 'desc')
                        ->first();
    
                        if ($paysliplast) {
                            $emp_payslipno = $paysliplast->emp_payslip_no;
                            $newpaylispno =  $emp_payslipno +1;
                        }else{
                            $newpaylispno = 1;
                        }

    
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
        return response()->json(['success' => 'Holiday Deduction is successfully Approved']);

    }
}
