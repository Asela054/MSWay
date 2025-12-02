<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use DB;
use App\Company;
use App\PaymentPeriod;
use App\PayrollProcessType;
use Carbon\Carbon;
use Validator;
class PaysummaryPDFController extends Controller
{
    public function paysummarypdf(Request $request){

        $rules = array(
            'rpt_payroll_id' => 'required',
            'rpt_location_id' => 'required',
            'rpt_period_id' => 'required',
            'rpt_department_id' => 'required'
        );

        $error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}

			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			$paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			$payment_period_id=$paymentPeriod->id;
			$payment_period_to=$paymentPeriod->payment_period_to;
			$payment_period_fr=$paymentPeriod->payment_period_fr;
			
			$fig_list = array(
							'ATTBONUS_W'=>array('amt'=>0, 'cnt'=>0),
							'BASIC'=>array('amt'=>0, 'cnt'=>0),
							'BRA_I'=>array('amt'=>0, 'cnt'=>0),
							'add_bra2'=>array('amt'=>0, 'cnt'=>0),
							'BRA_I'=>array('amt'=>0, 'cnt'=>0),
							'add_bra2'=>array('amt'=>0, 'cnt'=>0),
							'NOPAY'=>array('amt'=>0, 'cnt'=>0),
							'SAL_AFT_NOPAY'=>array('amt'=>0, 'cnt'=>''),
							'OTHRS1'=>array('amt'=>0, 'cnt'=>0),
							'OTHRS2'=>array('amt'=>0, 'cnt'=>0),
							'OTHRS'=>array('amt'=>0, 'cnt'=>0),//*
							'TOTAL_WITH_OT'=>array('amt'=>0, 'cnt'=>0),
							'add_holiday_x'=>array('amt'=>0, 'cnt'=>0),//holiday
							'add_transport_x'=>array('amt'=>0, 'cnt'=>0),//reimburse traveling
							'INCNTV_EMP'=>array('amt'=>0, 'cnt'=>0),//incentive
							'INCNTV_DIR'=>array('amt'=>0, 'cnt'=>0),//directors incentive
							'add_other'=>array('amt'=>0, 'cnt'=>0),
							'tot_earn'=>array('amt'=>0, 'cnt'=>''),
							'EPF8'=>array('amt'=>0, 'cnt'=>0),
							'sal_adv'=>array('amt'=>0, 'cnt'=>0),
							'ded_fund_1'=>array('amt'=>0, 'cnt'=>0),//funeral fund
							'ded_IOU'=>array('amt'=>0, 'cnt'=>0),
							'PAYE'=>array('amt'=>0, 'cnt'=>0),
							'add_transport'=>array('amt'=>0, 'cnt'=>0),
							'LOAN'=>array('amt'=>0, 'cnt'=>0),
							'ded_other'=>array('amt'=>0, 'cnt'=>0),
							'tot_ded'=>array('amt'=>0, 'cnt'=>''),
							'bal_earn'=>array('amt'=>0, 'cnt'=>''),
							'EPF12'=>array('amt'=>0, 'cnt'=>0),
							'ETF3'=>array('amt'=>0, 'cnt'=>0),
							'tot_sal_voucher'=>array('amt'=>0, 'cnt'=>''),
							'epf_etf_res'=>array('amt'=>0, 'cnt'=>''),
							'tot_epf12etf3'=>array('amt'=>0, 'cnt'=>''),
							'TOTAL_BANK'=>array('amt'=>0, 'cnt'=>''),
							'TOTAL_CASH'=>array('amt'=>0, 'cnt'=>''),
							'TOTAL_ALLWITHEPF'=>array('amt'=>0, 'cnt'=>''),
							'PAYMENT_SUSPENSE'=>array('amt'=>0, 'cnt'=>''),);
			
			$acc_group = array(
							'7083_087_0'=>array('grp_val'=>0),//hnb ja-ela
							'7083_209_0'=>array('grp_val'=>0),//hnb seeduwa
							'1_1_0'=>array('grp_val'=>0),//other banks
							'0_0_0'=>array('grp_val'=>0)//cash, other
						);
			
						
			
            $sqlslip="SELECT IFNULL(BINARY drv_pssc.opt_pssc, drv_info.remuneration_pssc) AS fig_pssc, drv_info.fig_value AS fig_value, 
			CONCAT(drv_emp.bank_group, '_', drv_info.fig_hidden) AS acc_group_code FROM (SELECT employees.id AS employee_id, employee_payslips.id AS emp_payslip_id, 
			CONCAT(IFNULL(bank_branches.bankcode, (payroll_profiles.employee_bank_id IS NOT NULL)), '_', IFNULL(bank_branches.code, (payroll_profiles.employee_bank_id 
			IS NOT NULL))) AS bank_group FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id 
			INNER JOIN employees ON payroll_profiles.emp_id=employees.id LEFT OUTER JOIN employee_banks ON payroll_profiles.employee_bank_id=employee_banks.id 
			LEFT OUTER JOIN (SELECT bankcode, code from bank_branches WHERE bankcode='7083' AND code IN ('087','209')) AS bank_branches 
			ON (employee_banks.bank_code=bank_branches.bankcode AND employee_banks.branch_code=bank_branches.code) WHERE employee_payslips.payroll_process_type_id=? 
			AND employees.emp_company=?  AND (employees.emp_department = ? OR ? = 'All') AND employee_payslips.payslip_cancel=0 AND (employee_payslips.payment_period_id=?)) AS drv_emp 
			INNER JOIN (SELECT `employee_payslip_id`, remuneration_payslip_spec_code as remuneration_pssc, employee_salary_payments.fig_hidden, 
			(fig_value>=0) AS fig_opt, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE employee_salary_payments.payment_period_id=? 
			GROUP BY `employee_payslip_id`, remuneration_payslip_spec_code, fig_hidden, (fig_value>=0)) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id 
			LEFT OUTER JOIN (select '1' AS fig_opt, 'OTHER_REM' AS pssc, 'add_other' AS opt_pssc UNION ALL 
			select '0' AS fig_opt, 'OTHER_REM' AS pssc, 'ded_other' AS opt_pssc) AS drv_pssc ON (drv_info.remuneration_pssc=BINARY drv_pssc.pssc
			 AND drv_info.fig_opt=BINARY drv_pssc.fig_opt) ORDER BY drv_emp.employee_id";
			
			$employee = DB::select($sqlslip, [$paymentPeriod->payroll_process_type_id, $request->rpt_location_id, $request->rpt_department_id,$request->rpt_department_id,  $payment_period_id, $payment_period_id]);
			
			foreach($employee as $r){
				if(isset($fig_list[$r->fig_pssc])){
					$pay_fig_val = $fig_list[$r->fig_pssc]['amt']+$r->fig_value;
					$fig_list[$r->fig_pssc]['amt']=$pay_fig_val;
					
					$pay_fig_emp_cnt = ($r->fig_value==0)?0:1;
					$pay_fig_cnt = $fig_list[$r->fig_pssc]['cnt']+$pay_fig_emp_cnt;//1;
					$fig_list[$r->fig_pssc]['cnt']=$pay_fig_cnt;
					
					//considering fig-list keys
					if(isset($acc_group[$r->acc_group_code])){
						$acc_group_val = $acc_group[$r->acc_group_code]['grp_val']+$r->fig_value;
						$acc_group[$r->acc_group_code]['grp_val'] = $acc_group_val;
					}
				
				}
			}
			
			$act_basic = $fig_list['BASIC']['amt'];
			$act_basic += ($fig_list['BRA_I']['amt']+$fig_list['add_bra2']['amt']);
			$fig_list['BASIC']['amt'] = $act_basic;
			
			$fig_list['SAL_AFT_NOPAY']['amt']=$fig_list['BASIC']['amt']+$fig_list['NOPAY']['amt'];
			
			$fig_list['OTHRS']['amt']=$fig_list['OTHRS1']['amt']+$fig_list['OTHRS2']['amt'];
			$othrs_cnt = ($fig_list['OTHRS1']['cnt']>$fig_list['OTHRS2']['cnt'])?$fig_list['OTHRS1']['cnt']:$fig_list['OTHRS2']['cnt'];
			$fig_list['OTHRS']['cnt']=$othrs_cnt;
											
			
			$act_tot_earn=($fig_list['SAL_AFT_NOPAY']['amt']+$fig_list['OTHRS']['amt']);
			$fig_list['TOTAL_WITH_OT']['amt'] = $act_tot_earn;

			//$act_tot_earn+=$fig_list['add_holiday_x']['amt'];
			$act_tot_earn+=$fig_list['ATTBONUS_W']['amt'];//$fig_list['add_transport_x']['amt'];
			$act_tot_earn+=$fig_list['INCNTV_EMP']['amt'];
			$act_tot_earn+=$fig_list['INCNTV_DIR']['amt'];
			$act_tot_earn+=$fig_list['add_other']['amt'];
			$fig_list['tot_earn']['amt']=$act_tot_earn;
			
			$act_tot_ded=($fig_list['EPF8']['amt']+$fig_list['sal_adv']['amt']);
			$act_tot_ded+=$fig_list['ded_fund_1']['amt'];
			$act_tot_ded+=$fig_list['ded_IOU']['amt'];
			$act_tot_ded+=$fig_list['PAYE']['amt'];
			$act_tot_ded+=$fig_list['add_transport']['amt'];
			$act_tot_ded+=$fig_list['LOAN']['amt'];
			$act_tot_ded+=$fig_list['ded_other']['amt'];
			$fig_list['tot_ded']['amt']=$act_tot_ded;
			$fig_list['bal_earn']['amt']=$fig_list['tot_earn']['amt']+$fig_list['tot_ded']['amt'];
			$fig_list['tot_sal_voucher']['amt']=$fig_list['BASIC']['amt']+$fig_list['INCNTV_EMP']['amt'];
			$fig_list['epf_etf_res']['amt']=$fig_list['EPF8']['amt']+$fig_list['EPF12']['amt']+$fig_list['ETF3']['amt'];
			$fig_list['tot_epf12etf3']['amt']=$fig_list['EPF12']['amt']+$fig_list['ETF3']['amt'];

			$total_earn=$fig_list['tot_earn']['amt'];
			$salary_and_wages=$fig_list['SAL_AFT_NOPAY']['amt']+$fig_list['OTHRS']['amt'];
			$travelling=$fig_list['ATTBONUS_W']['amt'];
			$incentive=$fig_list['INCNTV_EMP']['amt']+$fig_list['INCNTV_DIR']['amt'];
			$payee_tax=abs($fig_list['PAYE']['amt']);
			$salary_advance=abs($fig_list['sal_adv']['amt']);
			$emp_fund_reserve=abs($fig_list['EPF8']['amt']);
	
			$payment_suspense=$salary_and_wages-($payee_tax+$salary_advance+$emp_fund_reserve);
			$debit_total=$salary_and_wages+$travelling+$incentive;
			$credit_total=$travelling+$incentive+$payee_tax+$salary_advance+$emp_fund_reserve+$payment_suspense;
			$formattedTotal = number_format($debit_total, 2, '.', '');
			$parts = explode('.', $formattedTotal);
			$whole = intval($parts[0]);
			$decimal = intval($parts[1]);
		

			$thousentseparate_debit_total = number_format($debit_total, 2, '.', ',');
			$fig_list['TOTAL_BANK']['amt'] = ($acc_group['7083_087_0']['grp_val'] + $acc_group['7083_209_0']['grp_val'] + $acc_group['1_1_0']['grp_val']);
			$fig_list['TOTAL_CASH']['amt'] = $acc_group['0_0_0']['grp_val'];
			$fig_list['TOTAL_ALLWITHEPF']['amt'] = $fig_list['EPF8']['amt'];
			$fig_list['TOTAL_ALLWITHEPF']['cnt'] = number_format( $fig_list['ATTBONUS_W']['amt'] + $fig_list['INCNTV_EMP']['amt'] + $fig_list['INCNTV_DIR']['amt'] + 
			                                       abs($fig_list['PAYE']['amt']) + abs($fig_list['sal_adv']['amt']) + abs($fig_list['EPF8']['amt']), 2, '.','');
			$fig_list['PAYMENT_SUSPENSE']['amt'] = ( $fig_list['tot_earn']['amt'] - $fig_list['TOTAL_ALLWITHEPF']['cnt'] );

            $paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');

	        $department = DB::select("SELECT name FROM departments WHERE id = ?", [$request->rpt_department_id]);
            $department_name = $department[0]->name ?? null;

            $data = [
                'paymonth_name' => $paymonth_name,
                'department_name' => $department_name,
                'payment_detail' => $fig_list,
                'incentive_emp' => $fig_list['INCNTV_EMP']['amt'],
                'incentive_dir' => $fig_list['INCNTV_DIR']['amt']
            ];

        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");

        $pdf = PDF::loadView('Payroll.financeStatements.paysummary_pdf', $data)->setPaper('A4','portrait');
		return $pdf->download('Pay_Summary.pdf');
}

}