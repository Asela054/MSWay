<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\PaymentPeriod;
use App\PayrollProcessType;

use Illuminate\Http\Request;
use DB;
use Excel;
use PDF;
use Carbon\Carbon;

use Validator;

class EmployeePayrollStatement extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function reportBankAdvice()
    {
		$companyId = session('company_id');
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$department = DB::select("SELECT id, company_id, name FROM departments WHERE company_id = ?", [$companyId]);
		return view('Payroll.financeStatements.empSalary_bankAdvice',compact('branch', 'payroll_process_type', 'payment_period','department'));
    }
	
	public function previewBankAdvice(Request $request){
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'period_filter_id' => 'required',
				'department_filter_id' => 'required'
			);
	
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::find($request->period_filter_id);
			
			
			$payment_period_id=$paymentPeriod->id;//1;
			$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
			
			
			$payment_period_fr=$paymentPeriod->payment_period_fr;//$month_list[0]->col_month;//$request->work_date_fr;
			
			
			
			$sqlslip="SELECT drv_emp.employee_id, drv_emp.emp_first_name, drv_emp.bank_name, drv_emp.bank_ac_no, drv_info.fig_value AS fig_value FROM 
			(SELECT employees.id AS employee_id, employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, 
			CONCAT(banks.bank, ' ', bank_branches.branch) AS bank_name, employee_payslips.payment_period_fr, employee_payslips.payment_period_to, employee_banks.bank_ac_no 
			FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id 
			INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN employee_banks ON payroll_profiles.employee_bank_id=employee_banks.id 
			INNER JOIN banks ON employee_banks.bank_code=banks.code INNER JOIN bank_branches ON (employee_banks.bank_code=bank_branches.bankcode 
			AND employee_banks.branch_code=bank_branches.code) WHERE employee_payslips.payroll_process_type_id=? AND employees.emp_company=? 
			 AND (employees.emp_department = ? OR ? = 'All')
			AND employee_payslips.payslip_cancel=0 AND (employee_payslips.payment_period_id=?)) AS drv_emp 
			INNER JOIN (SELECT `employee_payslip_id`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE employee_salary_payments.payment_period_id=? 
			AND employee_salary_payments.fig_hidden=0 GROUP BY `employee_payslip_id`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id 
			ORDER BY drv_emp.employee_id";
			
			$employee = DB::select($sqlslip, [$paymentPeriod->payroll_process_type_id, $request->location_filter_id,$request->department_filter_id,$request->department_filter_id,  $payment_period_id, $payment_period_id]);
			/*
			$employee = DB::select($sqlslip);
			*/
			
			$employee_list = array();
			
			$cnt = 0;
			$total_netpay = 0;
			
			foreach($employee as $r){
				//$process_name='';//isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
				$cnt++;
				$total_netpay += $r->fig_value;
				$employee_list[]=array('id'=>$r->employee_id, 'emp_index'=>$cnt, 'emp_first_name'=>$r->emp_first_name, 'bank_name'=>$r->bank_name, 'emp_bank_acno'=>$r->bank_ac_no, 'emp_netpay'=>$r->fig_value);
			}
			
			return response()->json(['employee_detail'=>$employee_list, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to,
									 'total_netpay'=>$total_netpay]);
		}
	}
	
	
	
	
	public function reportPaySummary()
    {
		$companyId = session('company_id');
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$department = DB::select("SELECT id, company_id, name FROM departments WHERE company_id = ?", [$companyId]);
		return view('Payroll.financeStatements.orgSalary_paySummary',compact('branch', 'payroll_process_type', 'payment_period','department'));
    }
	
	public function previewPaySummary(Request $request)
	{
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'period_filter_id' => 'required',
				'department_filter_id' => 'required'
			);
	
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::find($request->period_filter_id);
			
			$payment_period_id=$paymentPeriod->id;//1;
			$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
			
			
			$payment_period_fr=$paymentPeriod->payment_period_fr;//$month_list[0]->col_month;//$request->work_date_fr;
			
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
							'PAYMENT_SUSPENSE'=>array('amt'=>0, 'cnt'=>''),
							
						);
			
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
			
			$employee = DB::select($sqlslip, [$paymentPeriod->payroll_process_type_id, $request->location_filter_id, $request->department_filter_id,$request->department_filter_id,  $payment_period_id, $payment_period_id]);
			/*
			$employee = DB::select($sqlslip);
			*/
			
			
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
		
			// Convert to words
			$wholeText = $this->convertNumberToWords($whole);
			$decimalText = $decimal > 0 ? ' and ' . $this->convertNumberToWords($decimal) . ' Cents Only' : ' Only';
			$debit_total_text= trim($wholeText . $decimalText);

			// add thousend separate
			$thousentseparate_debit_total = number_format($debit_total, 2, '.', ',');

			$fig_list['TOTAL_BANK']['amt'] = ($acc_group['7083_087_0']['grp_val'] + $acc_group['7083_209_0']['grp_val'] + $acc_group['1_1_0']['grp_val']);
			$fig_list['TOTAL_CASH']['amt'] = $acc_group['0_0_0']['grp_val'];

			$fig_list['TOTAL_ALLWITHEPF']['amt'] = $fig_list['EPF8']['amt'];
			$fig_list['TOTAL_ALLWITHEPF']['cnt'] = number_format( $fig_list['ATTBONUS_W']['amt'] + $fig_list['INCNTV_EMP']['amt'] + $fig_list['INCNTV_DIR']['amt'] + 
			                                       abs($fig_list['PAYE']['amt']) + abs($fig_list['sal_adv']['amt']) + abs($fig_list['EPF8']['amt']), 2, '.','');

			$fig_list['PAYMENT_SUSPENSE']['amt'] = ( $fig_list['tot_earn']['amt'] - $fig_list['TOTAL_ALLWITHEPF']['cnt'] );

			return response()->json(['payment_detail'=>$fig_list, 
									 'debit_total_text'=>$debit_total_text, 
									 'thousentseparate_debit_total'=>$thousentseparate_debit_total, 
									 'salary_and_wages'=>$salary_and_wages,
									 'travelling'=>$travelling,
									 'incentive_emp'=>$fig_list['INCNTV_EMP']['amt'],
									 'incentive_dir'=>$fig_list['INCNTV_DIR']['amt'],
									 'incentive_reserve'=>$incentive,
									 'payee_tax'=>$payee_tax,
									 'salary_advance'=>$salary_advance,
									 'emp_fund_reserve'=>$emp_fund_reserve,
									 'payment_suspense'=>$payment_suspense,
									 'debit_total'=>$debit_total,
									 'credit_total'=>$credit_total,
									 'br_jaela'=>$acc_group['7083_087_0']['grp_val'],
									 'br_seeduwa'=>$acc_group['7083_209_0']['grp_val'],
									 'br_other'=>$acc_group['1_1_0']['grp_val'],
									 'br_none'=>$acc_group['0_0_0']['grp_val'],
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	
	private function convertNumberToWords($number)
	{
		$words = [
			0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
			5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
			10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
			14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
			18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
			30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
			70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
		];
	
		if ($number < 21) {
			return $words[$number];
		}
		
		if ($number < 100) {
			return $words[10 * floor($number / 10)] . ($number % 10 ? ' ' . $words[$number % 10] : '');
		}
	
		if ($number < 1000) {
			return $words[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . $this->convertNumberToWords($number % 100) : '');
		}
	
		if ($number < 10000000) {
			return $this->convertNumberToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . $this->convertNumberToWords($number % 1000) : '');
		}
	
		return $this->convertNumberToWords(floor($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . $this->convertNumberToWords($number % 100000) : '');
	}
	
	
	public function reportEmpSalaryVoucher()
    {
		$companyId = session('company_id');

        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		 $department = DB::select("SELECT id, company_id, name FROM departments WHERE company_id = ?", [$companyId]);
		return view('Payroll.financeStatements.empSalary_payVoucher',compact('branch', 'payroll_process_type', 'payment_period','department'));
    }
	
	public function reportEmpIncentiveVoucher()
    {
		$companyId = session('company_id');
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$department = DB::select("SELECT id, company_id, name FROM departments WHERE company_id = ?", [$companyId]);
		return view('Payroll.financeStatements.empIncentive_payVoucher',compact('branch', 'payroll_process_type', 'payment_period','department'));
    }
	
	
	
	
	
	public function glEmpSalaryVoucher()
    {
		$companyId = session('company_id');
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$department = DB::select("SELECT id, company_id, name FROM departments WHERE company_id = ?", [$companyId]);
		return view('Payroll.financeStatements.empSalary_glVoucher',compact('branch', 'payroll_process_type', 'payment_period','department'));
    }
	
	
	
	
	
	public function glEmpEpfEtfVoucher()
    {
		$companyId = session('company_id');
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$department = DB::select("SELECT id, company_id, name FROM departments WHERE company_id = ?", [$companyId]);
		return view('Payroll.financeStatements.empEpfEtf_glVoucher',compact('branch', 'payroll_process_type', 'payment_period','department'));
    }
	
}
