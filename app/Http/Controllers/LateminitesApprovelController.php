<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\DB;

class LateminitesApprovelController extends Controller
{
    public function index(){

        $user = Auth::user();
        $permission = $user->can('Lateminites-Approvel-list');
        if(!$permission){
            abort(403);
        }

        return view('Attendent.lateminitesapprovel');
    }

    public function generatelateminites(Request $request){

        $user = Auth::user();
        $permission = $user->can('Lateminites-Approvel-apprve');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $month = $request->get('month');
        $closedate = $request->get('closedate');

        $selectedyear = substr($month, 0, 4);
        $selectedmonth = substr($month, -2);
        $startDate = new DateTime("$selectedyear-$selectedmonth-01");
        $endDate = (clone $startDate)->modify('first day of next month');
        
        $closedateObj = new DateTime($closedate);
        if ($endDate > $closedateObj) {
            $endDate = $closedateObj;
        }

        $dateRange = [];
        while ($startDate < $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->add(new DateInterval('P1D'));
        }

          // If $closedate is within the month, include it in the range
          if ($closedateObj->format('Y-m-d') >= $startDate->format('Y-m-d')) {
            $dateRange[] = $closedateObj->format('Y-m-d');
        }


        $query = DB::query()
            ->select('at1.id as attendance_id',
                'employees.id as emp_auto_id',
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'employees.emp_join_date',
                'branches.location',
                'departments.name as dept_name',
                'job_categories.late_attend_min'                
            )
            ->from('employees as employees')
            ->leftJoin('attendances as at1', function ($join) use ($month) {
                $join->on('employees.emp_id', '=', 'at1.uid')
                    ->whereNull('at1.deleted_at'); 
                if (!empty($month)) {
                    $m_str = $month . "%";
                    $join->where('at1.date', 'like', $m_str); 
                }
                if (!empty($closedate)) {
                    $join->where('at1.date', '<=', $closedate);
                }
            })
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'employees.job_category_id');

        if ($department != '') {
            $query->where(['departments.id' => $department]);
        }
        $query->where('employees.deleted', 0);
        $query->groupBy('employees.emp_id');
        $results = $query->get();


        foreach ($results as $record) {

        if (empty($record->late_attend_min)) {
            continue;
        }

        $joinDate = new DateTime($record->emp_join_date);
        if ($joinDate >= $startDate && $joinDate <= $endDate) {
            continue;
        }

         $employeeObj = (object)[
            'emp_id' => $record->emp_id,
            'emp_name_with_initial' => $record->emp_name_with_initial,
            'calling_name' => $record->calling_name
        ];


            $late_day_amount = 0;
            $late_hours_total = 0;
            $nopayAmount = 0;

            $employeeid =  $record->emp_id;

            $late_minites_total = (new \App\Employeelateattenadnaceminites)->get_lateminitescount($employeeid, $month ,$closedate);
 
            if(!empty($late_minites_total)){

                $work_days = (new \App\Attendance)->get_work_days($employeeid, $month, $closedate);
                $leave_days = (new \App\Attendance)->get_leave_days($employeeid, $month,$closedate);
                $no_pay_days = (new \App\Attendance)->get_no_pay_days($employeeid, $month,$closedate);
                $normal_ot_hours = (new \App\OtApproved)->get_ot_hours_monthly($employeeid, $month ,$closedate);
                $double_ot_hours = (new \App\OtApproved)->get_double_ot_hours_monthly($employeeid, $month ,$closedate);


                $late_minites_total = $late_minites_total - $record->late_attend_min;
                $late_hours_total = $late_minites_total / 60;
                $nopayamount = (new \App\Employeelateattenadnaceminites)->NopayAmountCal($record->emp_auto_id, $work_days,$leave_days,$no_pay_days,$normal_ot_hours, $double_ot_hours);
                $nopayAmount = $nopayamount['nopay_base_rate']/8;
                if($late_minites_total > 0){
                    $late_day_amount = abs($late_hours_total * $nopayAmount);
                }
                else{
                    $late_day_amount = 0;
                }
            }


            if($late_day_amount>0){
                $data[] = [
                    'emp_id' => $record->emp_id,
                    'emp_name_with_initial' =>EmployeeHelper::getDisplayName($employeeObj),
                    'emp_autoid' => $record->emp_auto_id,
                    'late_hours_total' => number_format($late_hours_total, 2),
                    'nopayAmount' => number_format(abs($nopayAmount), 2),
                    'late_day_amount' => number_format($late_day_amount, 2)
                ];
            }
        }

        return response()->json(['data' => $data ?? []]);
    }

    public function approvelatemin(Request $request){

        $permission = \Auth::user()->can('MealAllowanceApprove-approve');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('dataarry');

        
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $late_hourstotal = str_replace([','], '', $row['late_hourstotal']);
            $nopayamount = str_replace([','], '', $row['nopayamount']);
            $late_day_amount = str_replace([','], '', $row['total_amount']);
            $autoid = $row['autoid'];





            $profiles = DB::table('payroll_profiles')
            ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
            // ->where('payroll_profiles.emp_etfno', $empid)
            ->where('payroll_profiles.emp_id', $autoid)
            ->select('payroll_profiles.id as payroll_profile_id')
            ->first();

        if ($profiles) {

            $remunerationid = 25;

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
        
            $termpaymentcheck = DB::table('employee_term_payments')
                ->select('id')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('emp_payslip_no', $newpaylispno)
                ->where('remuneration_id', $remunerationid)
                ->first();
            
            if($termpaymentcheck){
                DB::table('employee_term_payments')
                ->where('id', $termpaymentcheck->id)
                ->update([
                    'payment_amount' => $late_day_amount,
                    'payment_cancel' => '0',
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time
                ]);
            }
            else{
                $termpayment = new EmployeeTermPayment();
                $termpayment->remuneration_id = $remunerationid;
                $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                $termpayment->emp_payslip_no = $newpaylispno;
                $termpayment->payment_amount = $late_day_amount;
                $termpayment->payment_cancel = 0;
                $termpayment->created_by = Auth::id();
                $termpayment->created_at = $current_date_time;
                $termpayment->save(); 
            }
        }else{
            continue;
        }

        }

        return response()->json(['success' => 'Late Minites Deduction is successfully Approved']);
    }

}
