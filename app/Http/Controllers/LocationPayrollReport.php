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

class LocationPayrollReport extends Controller
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
	
	public function reportPaySummary()
    {
        $branch=DB::select("select id, name as location from areas");// Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, area_id as company_id, location as name from branches");//DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$rpt_title='Payment Summary';
		$rpt_table='PAY_SUMMARY';
		return view('Payroll.paymentSummary.locationSummary',compact('rpt_title', 'rpt_table', 'branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	
	public function checkPaySummary(Request $request){
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'department_filter_id' => 'required',
				'period_filter_id_fr' => 'required',
				'period_filter_id_to' => 'required'
			);
			
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$opt_grp = '';
			
			if($request->rpt_tablename=='SALARY_REC'){
				$opt_grp = 'drv_info.pay_dura_id, ';
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriodFr=PaymentPeriod::find($request->period_filter_id_fr);
			$paymentPeriodTo=PaymentPeriod::find($request->period_filter_id_to);
			
			$payment_period_id_fr=$paymentPeriodFr->id;//1;
			$payment_period_id_to=$paymentPeriodTo->id;//1;
			$payment_period_fr=$paymentPeriodFr->payment_period_fr;//$request->work_date_fr;
			$payment_period_to=$paymentPeriodTo->payment_period_to;//$request->work_date_to;
			
			$sqlslip="SELECT branches.id AS emp_payslip_id, drv_info.pay_dura, '-' as emp_epfno, '' as emp_first_name, branches.location AS location, 0 as payslip_held, 1 as payslip_approved, drv_info.fig_group_title, drv_info.fig_group, SUM(drv_info.fig_value) AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc FROM (SELECT employee_salary_payments.`id` AS fig_id, employee_salary_payments.`emp_branch_id`, payment_periods.id as pay_dura_id, concat(payment_periods.payment_period_fr, ', ', payment_periods.payment_period_to) as pay_dura, employee_salary_payments.`fig_group_title`, employee_salary_payments.`fig_group`, employee_salary_payments.`epf_payable`, employee_salary_payments.remuneration_payslip_spec_code AS remuneration_pssc, employee_salary_payments.`fig_value` AS fig_value FROM employee_salary_payments inner join payment_periods on employee_salary_payments.payment_period_id=payment_periods.id WHERE employee_salary_payments.emp_company_area_id=? and employee_salary_payments.emp_branch_id=? and (employee_salary_payments.`payment_period_id` between ? and ?) and payment_periods.payroll_process_type_id=?) AS drv_info INNER JOIN branches ON drv_info.emp_branch_id=branches.id GROUP BY branches.id,".$opt_grp." drv_info.fig_group, drv_info.epf_payable, drv_info.remuneration_pssc ORDER BY drv_info.fig_id";
			/*
			
			*/
			$employee = DB::select($sqlslip, [$request->location_filter_id, $request->department_filter_id, 
											  $payment_period_id_fr, $payment_period_id_to,
											  $request->payroll_process_type_id]
								   );
			
			
			$employee_list = array();
			$cnt = 0;
			$act_payslip_id = '';
			$net_payslip_fig_value = 0;
			$emp_fig_totearn = 0;
			$emp_fig_otherearn = 0; //other-additions
			$emp_fig_totlost = 0;
			$emp_fig_otherlost = 0; //other-deductions
			$emp_fig_tottax = 0;
			
			foreach($employee as $r){
				//$process_name='';//isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
				if($act_payslip_id!=$r->emp_payslip_id){
					$cnt++;
					$act_payslip_id=$r->emp_payslip_id;
					$net_payslip_fig_value = 0;
					$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
					$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
					$emp_fig_tottax = 0;
				}
				if(!isset($employee_list[$cnt-1])){
					$employee_list[]=array('id'=>$r->emp_payslip_id, 'pay_dura'=>$r->pay_dura, 'emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'payslip_cancel'=>1, 'payslip_held'=>$r->payslip_held, 'payslip_approved'=>$r->payslip_approved, 'OTHER_REM'=>0);
					
					
				}
				
				$fig_key = isset($employee_list[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
				
				if(isset($employee_list[$cnt-1][$fig_key])){
					$fig_group_val=$employee_list[$cnt-1][$fig_key];
					$employee_list[$cnt-1][$fig_key]=number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
					
					if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
						$net_payslip_fig_value+=$r->fig_value;
						$employee_list[$cnt-1]['NETSAL']=number_format((float)$net_payslip_fig_value, 2, '.', '');
						
						if(($r->epf_payable==1)||($fig_key=='NOPAY')){
							$emp_fig_tottax += $r->fig_value;
							$employee_list[$cnt-1]['tot_fortax']=number_format((float)$emp_fig_tottax, 2, '.', '');
						}
						
						$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
						
						//if(($r->fig_value>=0)||($fig_key!='EPF8'))
						if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
							$emp_fig_totearn += $r->fig_value;
							$employee_list[$cnt-1]['tot_earn']=number_format((float)$emp_fig_totearn, 2, '.', '');
						}
						
						if($r->fig_value>=0){
							/*
							$emp_fig_totearn += $r->fig_value;
							$employee_list[$cnt-1]['tot_earn']=number_format((float)$emp_fig_totearn, 2, '.', '');
							*/
							$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
							$employee_list[$cnt-1]['add_other']=number_format((float)$emp_fig_otherearn, 2, '.', '');
						}else{
							if($fig_key!='NOPAY'){
								$emp_fig_totlost += $r->fig_value;
								$employee_list[$cnt-1]['tot_ded']=number_format((float)abs($emp_fig_totlost), 2, '.', '');
							}
							$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
							$employee_list[$cnt-1]['ded_other']=number_format((float)$emp_fig_otherlost, 2, '.', '');
						}
					}
					
					if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
						//if($employee_list[$cnt-1]['tot_bnp']==0){
							$emp_tot_bnp=($employee_list[$cnt-1]['BASIC']+$employee_list[$cnt-1]['BRA_I']+$employee_list[$cnt-1]['add_bra2']);
							$employee_list[$cnt-1]['tot_bnp']=number_format((float)$emp_tot_bnp, 2, '.', '');
							
						//}
					}
					
				}
			}
			
			return response()->json(['employee_detail'=>$employee_list, 
									 'payment_period_id_fr'=>$payment_period_id_fr, 'payment_period_id_to'=>$payment_period_id_to, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	public function downloadPaySummary(Request $request){
		
	}
	
	public function reportMasterSummary()
    {
		$branch=DB::select("select id, name as location from areas");// Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, area_id as company_id, location as name from branches");//DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$rpt_title='Master Summary';
		$rpt_table='MASTER_SUMMARY';
		return view('Payroll.paymentSummary.locationSummary',compact('rpt_title', 'rpt_table', 'branch', 'department', 'payroll_process_type', 'payment_period'));
	}
	
	public function reportEmpGratuity()
    {
		$branch=DB::select("select id, name as location from areas");// Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, area_id as company_id, location as name from branches");//DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=array();//PaymentPeriod::orderBy('id', 'desc')->get();
		$rpt_title='Employee Gratuity';
		$rpt_table='EMP_GRATUITY';
		return view('Payroll.paymentSummary.locationGratuity',compact('rpt_title', 'rpt_table', 'branch', 'department', 'payroll_process_type', 'payment_period'));
	}
	
	public function reportGratuityProvision()
    {
		$branch=DB::select("select id, name as location from areas");// Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, area_id as company_id, location as name from branches");//DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=array();//PaymentPeriod::orderBy('id', 'desc')->get();
		$rpt_title='Gratuity Provision';
		$rpt_table='PRO_GRATUITY';
		return view('Payroll.paymentSummary.locationGratuity',compact('rpt_title', 'rpt_table', 'branch', 'department', 'payroll_process_type', 'payment_period'));
	}
	
	public function reportSalaryReconciliation()
    {
		$branch=Company::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$rpt_title='Salary Reconciliation';
		$rpt_table='SALARY_REC';
		return view('Payroll.paymentSummary.locationSummary',compact('rpt_title', 'rpt_table','branch','department', 'payroll_process_type', 'payment_period'));
	}
	
	public function checkEmpGratuity(Request $request)
    {
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'department_filter_id' => 'required'
			);
			
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriodFr='';//PaymentPeriod::find($request->period_filter_id_fr);
			$paymentPeriodTo='';//PaymentPeriod::find($request->period_filter_id_to);
			
			$payment_period_id_fr='';//$paymentPeriodFr->id;//1;
			$payment_period_id_to='';//$paymentPeriodTo->id;//1;
			$payment_period_fr='';//$paymentPeriodFr->payment_period_fr;//$request->work_date_fr;
			$payment_period_to='';//$paymentPeriodTo->payment_period_to;//$request->work_date_to;
			
			$filter_location_col = 1;
			$filter_location_val = 1;
			
			$filter_min_year = 5;//($request->rpt_tablename=='EMP_GRATUITY')?5:1;
			
			if(!empty($request->department_filter_id)){
				$filter_location_col = 1;
				$filter_location_val = 1;
			}
			
			
			$sqlslip="select drv_emp.id, drv_emp.emp_name_with_initial, drv_emp.emp_etfno, companies.name as location_name, drv_emp.emp_join_date, drv_emp.work_yrs, payroll_profiles.basic_salary as basic_sal, ROUND((payroll_profiles.basic_salary/2), 2) as pro_gratuity, ROUND(drv_emp.work_yrs*(payroll_profiles.basic_salary/2), 2) as cur_gratuity  from (SELECT `id`, `emp_name_with_initial`, `emp_etfno`, IFNULL(`emp_join_date`, '---') as emp_join_date, emp_company, TIMESTAMPDIFF(YEAR, IFNULL(emp_join_date, CURDATE()), CURDATE()) as work_yrs FROM `employees` WHERE `emp_company`=? and `emp_department`=?) as drv_emp inner join payroll_profiles on drv_emp.id=payroll_profiles.emp_id inner join companies on drv_emp.emp_company=companies.id where payroll_profiles.payroll_process_type_id=? and drv_emp.work_yrs>=?";
			/*
			
			*/
			$emp_data = DB::select($sqlslip, [$request->location_filter_id, $request->department_filter_id, 
											  $request->payroll_process_type_id, $filter_min_year]
								   );
			
			
			$emp_array = array();
			
			
			$cnt = 0;
			$act_payslip_id = '';
			$net_payslip_fig_value = 0;
			
			foreach($emp_data as $r){
				$emp_array[]=array('id'=>$r->id, 'emp_name'=>$r->emp_name_with_initial, 'emp_epfno'=>$r->emp_etfno, 'emp_department'=>$r->location_name, 'join_date'=>$r->emp_join_date, 'tot_years'=>$r->work_yrs, 'basic_sal'=>number_format((float)$r->basic_sal, 2, '.', ''), 'pro_gratuity'=>number_format((float)$r->pro_gratuity, 2, '.', ''), 'eligible_gratuity'=>number_format((float)$r->cur_gratuity, 2, '.', ''));
			}
			
			return response()->json(['employee_detail'=>$emp_array, 
									 'payment_period_id_fr'=>$payment_period_id_fr, 'payment_period_id_to'=>$payment_period_id_to, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
}
