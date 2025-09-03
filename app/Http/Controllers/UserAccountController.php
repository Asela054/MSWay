<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveType;
use Auth;
use Illuminate\Support\Facades\Storage;
use Carbon;

use DB;
use App\PaymentPeriod;
use App\EmployeePicture;
use App\EmployeePayslip;
use App\Department;
use App\Holiday;
use App\Leave;
use App\Company;
use App\Employee;
use Illuminate\Support\Facades\Session;
use App\RemunerationTaxation;
use Yajra\Datatables\Datatables;
use Carbon\CarbonPeriod;

use Validator;


use Excel;
use PDF; 


class UserAccountController extends Controller
{
    public function useraccountsummery_list()
    {
		if (!auth()->check() && request()->isMethod('get')) {
            session(['url.intended' => url()->full()]);
            return redirect()->route('login');
		}
		$users_id = Session::get('users_id');
		$user = Auth::user();
		$user->hasRole('Employee');
		$user->can('user-account-summery-list');
		
        $permission = Auth::user()->can('user-account-summery-list');
        if (!$permission) {
            abort(403);
        }	

        $employee = DB::table('users')
            ->select('employees.*','employee_pictures.emp_pic_filename','departments.name AS departmentname','companies.name AS companyname','branches.location','employment_statuses.emp_status AS emp_statusname','job_categories.category','job_titles.title','users.emp_id','employees.id AS emprecordid','employees.emp_location')
            ->leftjoin('employees','employees.emp_id','users.emp_id')
            ->leftjoin('job_categories','job_categories.id','employees.job_category_id')
            ->leftjoin('job_titles','job_titles.id','employees.emp_job_code')
            ->leftjoin('employment_statuses','employment_statuses.id','employees.emp_status')
            ->leftjoin('branches','branches.id','employees.emp_location')
            ->leftjoin('companies','companies.id','employees.emp_company')
            ->leftjoin('departments','departments.id','employees.emp_department')
            ->leftjoin('employee_pictures','employee_pictures.emp_id','employees.id')
            ->where('users.id', $users_id)
            ->first();
					
        $emprecordid=$employee->emprecordid;
        $emp_id=$employee->emp_id;
		$emp_name_with_initial=$employee->emp_name_with_initial;
		$calling_name=$employee->calling_name;
        $emp_location=$employee->emp_location;
        $emp_company=$employee->emp_company;

		$leavetype = LeaveType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		
        $employees = Employee::where('leave_approve_person', 1)->get();
        return view('UserAccountSummery.useraccountsummery',compact('emprecordid','emp_id','emp_location','employee','leavetype','emp_name_with_initial','calling_name','payment_period','employees', 'emp_company'));
    }

    public function get_employee_monthlysummery(Request $request)
    {
        $salaryperiodid = $request->input('salaryperiodid');
        $selectedmonth = $request->input('selectedmonth');
		$closedate = $request->input('lastday');
        $emprecordid = $request->input('emprecordid');
        $empid = $request->input('empid');
        $empcompany = $request->input('empcompany');
	
        $monthworkingdaysdata=DB::table('employees')
            ->leftJoin('job_categories','job_categories.id','employees.job_category_id')
            ->select('employees.job_category_id','job_categories.emp_payroll_workdays')
            ->where('employees.id',$emprecordid)
            ->first();
							
        $monthworkingdays=$monthworkingdaysdata->emp_payroll_workdays;

        $sqlattendancesummery = "SELECT SUM(`employee_paid_rates`.`work_days`) AS `totworkdays`, SUM(`employee_paid_rates`.`work_hours`) AS `totworkhours`, SUM(`employee_paid_rates`.`leave_days`) AS `totleavedays`, SUM(`employee_paid_rates`.`nopay_days`) AS `totnopaydays` FROM `employee_paid_rates` LEFT JOIN `employee_payslips` ON `employee_payslips`.`id` = `employee_paid_rates`.`employee_payslip_id` LEFT JOIN `payroll_profiles` ON `payroll_profiles`.`id` = `employee_payslips`.`payroll_profile_id` WHERE `employee_payslips`.`payslip_cancel` = ? AND `employee_payslips`.`payment_period_id` = ? AND `payroll_profiles`.`emp_id` = ? GROUP BY `employee_paid_rates`.`employee_payslip_id`";
        $attendancesummery = DB::select($sqlattendancesummery, ['0', $salaryperiodid, $emprecordid]);
		
        $work_days = $attendancesummery? $attendancesummery[0]->totworkdays : 0;
        $work_hours = $attendancesummery? $attendancesummery[0]->totworkhours : 0;
		$working_week_days_arr = 0;
        $leave_days = $attendancesummery? $attendancesummery[0]->totleavedays : 0;
        $no_pay_days = $attendancesummery? $attendancesummery[0]->totnopaydays : 0;		           
	  	     
        $attendance_responseData= array(
            'workingdays'=>  $work_days,
            'workinghoursys'=>  $work_hours,
            'absentdays'=>  ($monthworkingdays-$work_days),
            'working_week_days_arr'=>  $working_week_days_arr,
            'leave_days'=>  $leave_days,
            'no_pay_days'=>  $no_pay_days,
        );

        // payroll part--------------------------------------------------------------------------------------------------------------------------------
        
        $payment_period = DB::table('employee_payslips')
        ->join('payroll_profiles','payroll_profiles.id','employee_payslips.payroll_profile_id')
        ->select('employee_payslips.id','employee_payslips.payment_period_id','employee_payslips.emp_payslip_no','employee_payslips.payroll_profile_id','employee_payslips.payment_period_fr','employee_payslips.payment_period_to')
        ->where('employee_payslips.payment_period_id', $salaryperiodid)
        ->where('payroll_profiles.emp_id', $emprecordid)
        ->where('employee_payslips.payslip_cancel', '0')
        ->orderBy('employee_payslips.id', 'desc') 
        ->first();

	
        $payment_period_fr=$payment_period->payment_period_fr;
        $payment_period_to=$payment_period->payment_period_to;
        $payslip_id=$payment_period->id;
        $payroll_profile_id=$payment_period->payroll_profile_id;

        $sqlslip="SELECT 
            drv_emp.emp_payslip_id, 
            drv_emp.emp_payroll_profile_id, 
            drv_emp.emp_epfno, 
            drv_emp.emp_first_name, 
            drv_emp.location, 
            drv_emp.payslip_held, 
            drv_emp.payslip_approved, 
            drv_info.fig_group_title, 
            drv_info.employee_payslip_id,
            drv_info.fig_group, 
            drv_info.fig_value AS fig_value, 
            drv_info.epf_payable AS epf_payable, 
            drv_info.remuneration_pssc, 
            drv_info.remuneration_tcsc 
        FROM 
            (SELECT employee_payslips.id AS emp_payslip_id, 
            employee_payslips.payroll_profile_id AS emp_payroll_profile_id,
            employees.emp_id AS emp_epfno, 
            employees.emp_name_with_initial AS emp_first_name, 
            companies.name AS location, 
            employee_payslips.payslip_held, 
            employee_payslips.payslip_approved 
        FROM `employee_payslips` 
        INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id 
        INNER JOIN employees ON payroll_profiles.emp_id=employees.id 
        INNER JOIN companies ON employees.emp_company=companies.id 
            WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.id=?  AND employee_payslips.payslip_cancel=0) AS drv_emp 
        INNER JOIN 
        (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value 
        FROM employee_salary_payments 
        WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
        $employee = DB::select($sqlslip, [$salaryperiodid, $empcompany, $emprecordid, $salaryperiodid]);


        $sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
		/*
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Before Nopay', 'Arrears', 'Total for Tax', 'Attendance', 'Transport', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'EPF-8', 'Salary Advance', 'Telephone', 'IOU', 'Funeral Fund', 'Other Deductions', 'PAYE', 'Loans', 'Total Deductions', 'Balance Pay');
		*/
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
		/*
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
		*/
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
		$cnt = 1;
		$act_payslip_id = '';
		$net_payslip_fig_value = 0;
		$emp_fig_totearn = 0;
		$emp_fig_otherearn = 0; //other-additions
		$emp_fig_totlost = 0;
		$emp_fig_otherlost = 0; //other-deductions
		$emp_fig_tottax = 0;
		
		$rem_tot_bnp = 0;
		$rem_tot_fortax = 0;
		$rem_tot_earn = 0;
		$rem_tot_ded = 0;
		$rem_net_sal = 0;
		$rem_ded_other = 0;
		
		//2023-11-07
		//keys-selected-to-calc-paye-updated-from-remuneration-taxation
		
        $conf_tl = DB::table('remuneration_taxations')
        ->where(['fig_calc_opt' => 'FIGPAYE', 'optspec_cancel' => 0])
        ->pluck('taxcalc_spec_code')
        ->toArray();
        //var_dump($conf_tl);
		//return response()->json($conf_tl);
		//-2023-11-07
		
		foreach($employee as $r){
			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
				$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
				$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
				$emp_fig_tottax = 0;
			}
			if(!isset($emp_array[$cnt-1])){
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
				$rem_tot_bnp = 0;
				$rem_tot_fortax = 0;
				$rem_tot_earn = 0;
				$rem_tot_ded = 0;
				$rem_net_sal = 0;
				$rem_ded_other = 0;
			}
			
			
			$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
			
			if(isset($emp_array[$cnt-1][$fig_key])){
				$fig_group_val=$emp_array[$cnt-1][$fig_key];
				
				if($fig_key!='OTHER_REM'){//prevent-other-rem-column-values-being-show-up-in-excel
					$emp_array[$cnt-1][$fig_key]=(abs($r->fig_value)+$fig_group_val);//number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
					$sum_array[$fig_key]+=abs($r->fig_value);
				}
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$emp_array[$cnt-1]['NETSAL']=$net_payslip_fig_value;//number_format((float)$net_payslip_fig_value, 2, '.', '');
					
					$reg_net_sal=$sum_array['NETSAL']-$rem_net_sal;
					$sum_array['NETSAL']=($reg_net_sal+$net_payslip_fig_value);
					$rem_net_sal = $net_payslip_fig_value;
					
					/*
					if(($r->epf_payable==1)||($fig_key=='NOPAY')){
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
						$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
						$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
						$rem_tot_fortax = $emp_fig_tottax;
					}
					*/
					if(in_array($r->remuneration_tcsc, $conf_tl)){
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
						$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
						$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
						$rem_tot_fortax = $emp_fig_tottax;
					}
					
					$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
					//if(($r->fig_value>=0)||($fig_key!='EPF8'))
					if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
						$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
						$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
						$rem_tot_earn = $emp_fig_totearn;
					}
					
					if($r->fig_value>=0){
						/*
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
						$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
						$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
						$rem_tot_earn = $emp_fig_totearn;
						*/
						$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
						$emp_array[$cnt-1]['add_other']=$emp_fig_otherearn;//number_format((float)$emp_fig_otherearn, 2, '.', '');
						
						
					}else{
						if($fig_key!='NOPAY'){
							$emp_fig_totlost += $r->fig_value;
							$emp_array[$cnt-1]['tot_ded']=abs($emp_fig_totlost);//number_format((float)abs($emp_fig_totlost), 2, '.', '');
							
							$reg_tot_ded=$sum_array['tot_ded']-$rem_tot_ded;
							$sum_array['tot_ded']=($reg_tot_ded+abs($emp_fig_totlost));
							$rem_tot_ded = abs($emp_fig_totlost);
						}
						$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
						$emp_array[$cnt-1]['ded_other']=$emp_fig_otherlost;//number_format((float)$emp_fig_otherlost, 2, '.', '');
						
						$reg_ded_other=$sum_array['ded_other']-$rem_ded_other;
						$sum_array['ded_other']=($reg_ded_other+$emp_fig_otherlost);
						$rem_ded_other=$emp_fig_otherlost;
					}

				}
				
				if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
					//if($emp_array[$cnt-1]['tot_bnp']==0){
						$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
						$emp_array[$cnt-1]['tot_bnp']=$emp_tot_bnp;//number_format((float)$emp_tot_bnp, 2, '.', '');
						
						$reg_tot_bnp=$sum_array['tot_bnp']-$rem_tot_bnp;
						$sum_array['tot_bnp']=($reg_tot_bnp+$emp_tot_bnp);
						$rem_tot_bnp = $emp_tot_bnp;
					//}
				}
			}
		}

        $html = '';
        $html .= '<table class="table table-striped table-sm small">
            <thead>
                <tr>
                    <th colspan="4" class="text-center">ATTENDANCE SUMMERY</th>
                    <th colspan="3" class="text-center">SALARY INFOMATION</th>
                </tr>
                <tr>
                    <th class="text-center">WORK DAYS</th>
                    <th class="text-center">ABSENT DAYS</th>
                    <th class="text-center">LEAVE DAYS</th>
                    <th class="text-center">NOPAY DAYS</th>
                    <th class="text-left">PAYSLIP TYPES</th>
                    <th class="text-right">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">'.$attendance_responseData['workingdays'].'</td>
                    <td class="text-center">'.$attendance_responseData['absentdays'].'</td>
                    <td class="text-center">'.$attendance_responseData['leave_days'].'</td>
                    <td class="text-center">'.$attendance_responseData['no_pay_days'].'</td>
                    <td class="text-left">BASIC SALARY</td>
                    <td class="text-right">'.number_format($sum_array['BASIC'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">BRA 1</td>
                    <td class="text-right">'.number_format($sum_array['BRA_I'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">BRA 2</td>
                    <td class="text-right">'.number_format($sum_array['add_bra2'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">NOPAY</td>
                    <td class="text-right">'.number_format($sum_array['NOPAY'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">TOTAL BEFORE NOPAY</td>
                    <td class="text-right">'.number_format($sum_array['tot_bnp'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">ARREARS</td>
                    <td class="text-right">'.number_format($sum_array['sal_arrears1'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">WEEKLY ATTENDANCE</td>
                    <td class="text-right">'.number_format($sum_array['ATTBONUS_W'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">INCENTIVE</td>
                    <td class="text-right">'.number_format($sum_array['INCNTV_EMP'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">DIRECTOR INCENTIVE</td>
                    <td class="text-right">'.number_format($sum_array['INCNTV_DIR'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">SALARY ARREARS</td>
                    <td class="text-right">'.number_format($sum_array['sal_arrears2'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">OT</td>
                    <td class="text-right">'.number_format($sum_array['OTHRS1'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">DOUBLE OT</td>
                    <td class="text-right">'.number_format($sum_array['OTHRS2'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">TOTAL EARN</td>
                    <th class="text-right">'.number_format($sum_array['tot_earn'], 2).'</th>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">TOTAL FOR TAX</td>
                    <th class="text-right">'.number_format($sum_array['tot_fortax'], 2).'</th>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">EPF 8</td>
                    <td class="text-right">('.number_format($sum_array['EPF8'], 2).')</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">SALARY ADVANCE</td>
                    <td class="text-right">('.number_format($sum_array['sal_adv'], 2).')</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">LOAN</td>
                    <td class="text-right">('.number_format($sum_array['LOAN'], 2).')</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">IOU DEDUCTION</td>
                    <td class="text-right">('.number_format($sum_array['ded_IOU'], 2).')</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">FUNERAL FUND</td>
                    <td class="text-right">('.number_format($sum_array['ded_fund_1'], 2).')</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">PAYE</td>
                    <td class="text-right">('.number_format($sum_array['PAYE'], 2).')</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">OTHER</td>
                    <td class="text-right">('.number_format($sum_array['ded_other'], 2).')</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">TOTAL DEDUCTION</td>
                    <th class="text-right">'.number_format($sum_array['tot_ded'], 2).'</th>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center" style="border-bottom: 1px solid #000;">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left" style="">BALANCE TO PAY</td>
                    <th class="text-right" style="border-bottom: 3px double #000;">'.number_format($sum_array['NETSAL'], 2).'</th>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">EPF 12</td>
                    <td class="text-right">'.number_format($sum_array['EPF12'], 2).'</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-left">ETF 3</td>
                    <td class="text-right">'.number_format($sum_array['ETF3'], 2).'</td>
                </tr>';
            $html .= '</tbody>  
        </table>';
        // echo $html;
        return response() ->json(['result'=>  $attendance_responseData,'salaryresult'=>$sum_array,'payslip_id'=>$payslip_id,'payroll_profile_id'=>$payroll_profile_id,'htmlcontent'=>$html]);
    }
	
    public function downloadSalarySheet(Request $request){
        $companyRegInfo = Company::find($request->rpt_location_id);
		$company_name = $companyRegInfo->name;
		$company_addr = $companyRegInfo->address;

        $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
	 	$emp_department_col = '';
	 	$emp_department_val = '';
		
	 	if(!empty($request->rpt_dept_id)){
	 		$emp_department_col = "employees.emp_department";
	 		$emp_department_val = $request->rpt_dept_id;
	 	}
        $emp_id = $request->rpt_emp_id;

        $sqlslip = "SELECT drv_emp.emp_payslip_id, drv_emp.payroll_profile_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_national_id, 
        IFNULL(employee_banks.bank_ac_no, '') AS bank_ac_no, IFNULL(employee_banks.bank_name, '') as bank_name, IFNULL(employee_banks.bank_branch_name, '') as bank_branch_name, drv_emp.emp_first_name, drv_emp.emp_designation, 
        drv_emp.location, drv_emp.department_name, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable 
        AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_nopaydays, drv_catinfo.work_days, drv_catinfo.working_week_days 
        FROM (SELECT employee_payslips.id AS emp_payslip_id, employee_payslips.payroll_profile_id, employees.emp_id AS emp_epfno, employees.emp_etfno, 
        ifnull(employees.emp_national_id, '') AS emp_national_id, employees.emp_name_with_initial AS emp_first_name, job_titles.title 
        AS emp_designation, companies.name AS location, departments.name AS department_name  
        FROM `employee_payslips` 
        INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id 
        INNER JOIN employees ON payroll_profiles.emp_id=employees.id 
        INNER JOIN companies ON employees.emp_company=companies.id 
        INNER JOIN departments ON employees.emp_department=departments.id 
        LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id 
        WHERE employee_payslips.payment_period_id=? 
        AND employees.emp_company=? 
        AND employees.emp_id=?
        AND employee_payslips.payslip_cancel=0) AS drv_emp 
        INNER JOIN (select employee_payslip_id, employee_bank_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, 
        sum(nopay_days) AS emp_nopaydays, SUM(work_days) as work_days, SUM(work_days_exclusions) as working_week_days 
        from employee_paid_rates 
        GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id 
        INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code 
        AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value 
        FROM employee_salary_payments 
        WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id 
        LEFT OUTER JOIN (select employee_banks.id, employee_banks.bank_ac_no, banks.bank as bank_name, bank_branches.branch as bank_branch_name 
        from employee_banks 
        inner join banks on employee_banks.bank_code=banks.code 
        inner join bank_branches on (employee_banks.bank_code=bank_branches.bankcode AND employee_banks.branch_code=bank_branches.code)) as employee_banks ON drv_catinfo.employee_bank_id=employee_banks.id 
        ORDER BY ifnull(nullif(drv_emp.emp_etfno, 0), 99999), drv_emp.emp_epfno, drv_info.fig_id";

        $emp_data = DB::select($sqlslip, [
            $payment_period_id, 
            $request->rpt_location_id, 
            $emp_id, // Specific employee ID
            $payment_period_id
        ]);

        $emp_array = array();
		
		
		$cnt = 0;
		$act_payslip_id = '';
		$net_payslip_fig_value = 0;
		$emp_fig_totearn = 0;
		$emp_fig_otherearn = 0; //other-additions
		$emp_fig_totlost = 0;
		$emp_fig_otherlost = 0; //other-deductions
		$emp_fig_tottax = 0;
		
		//2023-11-07
		//keys-selected-to-calc-paye-updated-from-remuneration-taxation
		$conf_tl = RemunerationTaxation::where(['fig_calc_opt'=>'FIGPAYE', 'optspec_cancel'=>0])
						->pluck('taxcalc_spec_code')->toArray(); //var_dump($conf_tl);
		//return response()->json($conf_tl);
		//-2023-11-07
		
		foreach($emp_data as $r){
			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
				$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
				$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
				$emp_fig_tottax = 0;
			}
			if(!isset($emp_array[$cnt-1])){
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno,'emp_national_id'=>$r->emp_national_id, 'bank_name'=>$r->bank_name, 'bank_branch'=>$r->bank_branch_name,'bank_accno'=>$r->bank_ac_no, 'emp_first_name'=>$r->emp_first_name, 'emp_designation'=>$r->emp_designation,'emp_department' => $r->department_name, 'Office'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTAMT1'=>$r->emp_otamt1, 'OTAMT2'=>$r->emp_otamt2,'work_tot_days'=>$r->work_days, 'work_week_days'=>$r->working_week_days, 'NOPAYCNT'=>$r->emp_nopaydays, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
				
			}
			
			$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
			
			if(isset($emp_array[$cnt-1][$fig_key])){
				$fig_group_val=$emp_array[$cnt-1][$fig_key];
				
				$emp_array[$cnt-1][$fig_key]=number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$emp_array[$cnt-1]['NETSAL']=number_format((float)$net_payslip_fig_value, 2, '.', '');
					
					if(($r->epf_payable==1)||($fig_key=='NOPAY')){
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt-1]['tot_fortax']=number_format((float)$emp_fig_tottax, 2, '.', '');
					}
					
					$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
					//if(($r->fig_value>=0)||($fig_key!='EPF8'))
					if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=number_format((float)$emp_fig_totearn, 2, '.', '');
					}
					
					if($r->fig_value>=0){
						$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
						$emp_array[$cnt-1]['add_other']=number_format((float)$emp_fig_otherearn, 2, '.', '');
					}else{
						if($fig_key!='NOPAY'){
							$emp_fig_totlost += $r->fig_value;
							$emp_array[$cnt-1]['tot_ded']=number_format((float)abs($emp_fig_totlost), 2, '.', '');
						}
						$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
						$emp_array[$cnt-1]['ded_other']=number_format((float)$emp_fig_otherlost, 2, '.', '');
					}
				}
				
				if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
						$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
						$emp_array[$cnt-1]['tot_bnp']=number_format((float)$emp_tot_bnp, 2, '.', '');
				}
			}
		}
		$more_info=$payment_period_fr.' / '.$payment_period_to;
		$sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');
		
		ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");
		
		// return view('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name', 'company_addr'));
		$pdf = PDF::loadView('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name','company_name',  'company_addr'));
        return $pdf->download('salary-list.pdf');
    }

    public function userlogininformation_list()
    {
        $permission = Auth::user()->can('user-account-summery-list');
        if (!$permission) {
            abort(403);
        }

        return view('UserAccountSummery.userlogininformation');
    }


	public function leave_list_dt(Request $request)
    {
        $permission = Auth::user()->can('user-account-summery-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $emp_id = $request->get('emp_id');

        $query =  DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
            ->join('employees as ec', 'leaves.emp_covering', '=', 'ec.emp_id')
            ->join('employees as e', 'leaves.emp_id', '=', 'e.emp_id')
            ->leftjoin('branches', 'e.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'e.emp_department', '=', 'departments.id')
            ->select('leaves.*', 'ec.emp_name_with_initial as covering_emp', 'leave_types.leave_type', 'e.emp_name_with_initial as emp_name', 'departments.name as dep_name')
            ->where(['e.emp_id' => $emp_id]);

        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('half_or_short', function($row){

                if($row->half_short == 0.25){
                    return 'Short Leave';
                }

                if($row->half_short == 0.5){
                    return 'Half Day';
                }

                if($row->half_short == 1){
                    return 'Full Day';
                }
                return '';
            })
            ->addColumn('action', function($row){
                $btn = '';

                $permission = Auth::user()->can('leave-edit');
                if ($permission) {
                    $btn = ' <button name="edit" id="'.$row->id.'"
                            class="edit btn btn-outline-primary btn-sm" style="margin:1px;" type="submit">
                            <i class="fas fa-pencil-alt"></i>
                        </button> ';
                }

                $permission = Auth::user()->can('leave-delete');
                if ($permission) {
                    $btn .= '<button type="submit" name="delete" id="'.$row->id.'"
                            class="delete btn btn-outline-danger btn-sm" style="margin:1px;" ><i
                            class="far fa-trash-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['action', 'half_or_short'])
            ->make(true);
    }
	
	public function updateImage(Request $request, $id)
    {
        $picture = EmployeePicture::where('emp_id', $id)->first();

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            
            if ($image->isValid()) {
                $name = time() . '_' . $id . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                if ($image->move($destinationPath, $name)) {
                    $employeePic = EmployeePicture::where('emp_id', $id)->first();
                   
                    
                    if ($employeePic) {
                        if ($employeePic->emp_pic_filename) {
                            $oldImagePath = public_path('/images/' . $employeePic->emp_pic_filename);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                        
                        DB::table('employee_pictures')
                            ->where('emp_id', $id)
                            ->update([
                                'emp_pic_filename' => $name,
                                'update_user' => Auth::id(),
                                'update_date' => Carbon\Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon\Carbon::now()->toDateTimeString()
                            ]);
                            
                            return response()->json([
                                'success' => true,
                                'message' => 'Profile imaged updated successfully.'
                            ]);
                    } else {
                        DB::table('employee_pictures')->insert([
                            'emp_id' => $id,
                            'emp_pic_filename' => $name,
                            'insert_user' => Auth::id(),
                            'created_at' => Carbon\Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon\Carbon::now()->toDateTimeString()
                        ]);
                        
                             return response()->json([
                                'success' => true,
                                'message' => 'Profile imaged insert successfully.'
                            ]);
                    }
                } else {
                   

                     return response()->json([
                                'error' => true,
                                'message' => 'Failed to upload image. Please try again.'
                            ]);
                }
            } else {
                  return response()->json([
                                'error' => true,
                                'message' => 'Invalid image file. Please select a valid image.'
                            ]);
            }
        }
    }

    //get_incomplete_attendance_by_employee_data
    
    public function get_attendance_by_employee_data(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('user-attendance-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $employee = $request->emp_id;
            $attendancemonth = $request->attendancemonth;
            $location = Session::get('emp_location');
            $department = Session::get('emp_department');

            $from_date = date('Y-m-01', strtotime($attendancemonth));
            $to_date = date('Y-m-t', strtotime($attendancemonth));

            $query = DB::query()
                ->select('at1.id',
                    'at1.emp_id',
                    'at1.uid',
                    'at1.state',
                    'at1.timestamp',
                    'at1.date',
                    'at1.approved',
                    'at1.type',
                    'at1.devicesno',
                    DB::raw('Min(at1.timestamp) as firsttimestamp'),
                    DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                    'employees.emp_name_with_initial',
                    'branches.location',
                    'departments.name as dep_name'
                )
                ->from('attendances as at1')
                ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                ->leftJoin('branches', 'at1.location', '=', 'branches.id')
                ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department');

            
            if ($employee != '') {
                $query->where(['employees.emp_id' => $employee]);
            }

            if ($from_date != '' && $to_date != '') {
                $query->whereBetween('at1.date', [$from_date, $to_date]);
            }

            $query->where(['at1.deleted_at' => null]);

            $query->groupBy('at1.uid', 'at1.date');

            return Datatables::of($query)
                ->addIndexColumn()
                ->orderColumn('formatted_date', 'date')
                //formatted date
                ->editColumn('formatted_date', function ($row) {
                    return date('Y-m-d', strtotime($row->date));
                })
                //first_time_stamp
                ->addColumn('first_time_stamp', function ($row) {
                    $first_timestamp = date('H:i', strtotime($row->firsttimestamp));
                    return $first_timestamp;
                })
                //last_time_stamp
                ->addColumn('last_time_stamp', function ($row) {
                    $lasttimestamp = date('H:i', strtotime($row->lasttimestamp));
                    return $lasttimestamp;
                })
               
                ->rawColumns(['action',
                    'emp_name_with_initial',
                    'location',
                    'formatted_date',
                    'first_time_stamp',
                    'last_time_stamp'
                ])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }

}
