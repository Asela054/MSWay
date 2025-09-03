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

class EmployeePayrollReport extends Controller
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
	
	public function reportSixMonth()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		
		$sqlcols="SELECT DATE_FORMAT(col_date, '%Y-%M') AS col_month from (select DATE_ADD(DATE(NOW()), INTERVAL -1*t0.i MONTH) AS col_date from (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5) t0) as drv ORDER BY col_date ASC";
		$month_list = DB::select($sqlcols);
		
		$payroll_months=array();
        
		foreach($month_list as $r){
			$payroll_months[] = $r->col_month;
		}
		
		return view('Payroll.periodicReport.sixMonth_salary',compact('branch', 'payroll_process_type', 'payment_period', 'payroll_months'));
    }
	
	public function previewSixMonth(Request $request){
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'period_filter_id' => 'required'
			);
	
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::find($request->period_filter_id);
			
			$cols = array();
			
			$payment_period_id=$paymentPeriod->id;//1;
			$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
			
			$sqlcols="SELECT DATE_FORMAT(col_date, '%Y-%M') AS col_month from (select DATE_ADD(?, INTERVAL -1*t0.i MONTH) AS col_date from (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5) t0) as drv ORDER BY col_date ASC";
			$month_list = DB::select($sqlcols, [$payment_period_to]);
			
			$payment_period_fr=$month_list[0]->col_month;//$paymentPeriod->payment_period_fr;//$request->work_date_fr;
			
			foreach($month_list as $r){
				$cols[] = $r->col_month;
			}
			
			/*
			month-blocks="SELECT (6-col_grp) AS col_grp, DATE_ADD(col_date, INTERVAL -1*(DAY(col_date)-1)*(col_date<>'2020-12-08') DAY) AS col_fr, DATE_ADD(max_date, INTERVAL -1*(DAY(max_date))*(col_date<>'2021-05-08') DAY) AS col_to from (select i AS col_grp, DATE_ADD(DATE(NOW()), INTERVAL -1*t0.i MONTH) AS col_date, DATE_ADD(DATE(NOW()), INTERVAL -1*t0.u MONTH) AS max_date from (select 0 i, 0 u union select 1, 0 union select 2, 1 union select 3, 2 union select 4, 3 union select 5, 4) t0) as drv ORDER BY col_date ASC"
			
			
			
			test-sql = "SELECT drv_emp.employee_id, drv_grp.col_grp, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, SUM(drv_info.fig_value) AS fig_value FROM 

			(
			
			SELECT employees.id AS employee_id, employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location, employee_payslips.payment_period_fr, employee_payslips.payment_period_to FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payroll_process_type_id=1 AND employees.emp_location=1 AND employee_payslips.payslip_cancel=0 AND (employee_payslips.payment_period_to BETWEEN '2020-08-01' AND '2020-12-01')
			
			) AS drv_emp INNER JOIN 
			
			(
			SELECT (6-col_grp) AS col_grp, DATE_ADD(col_date, INTERVAL -1*(DAY(col_date)-1)*(col_date<>'2020-08-02') DAY) AS col_fr, DATE_ADD(max_date, INTERVAL -1*(DAY(max_date))*(col_date<>'2021-01-02') DAY) AS col_to from (select i AS col_grp, DATE_ADD('2021-01-02', INTERVAL -1*t0.i MONTH) AS col_date, DATE_ADD('2021-01-02', INTERVAL -1*t0.u MONTH) AS max_date from (select 0 i, 0 u union select 1, 0 union select 2, 1 union select 3, 2 union select 4, 3 union select 5, 4) t0) as drv ORDER BY col_date ASC
) AS drv_grp ON (drv_emp.payment_period_fr>=drv_grp.col_fr AND drv_emp.payment_period_to<=drv_grp.col_to) INNER JOIN 
			
			(
			SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments GROUP BY `employee_payslip_id`, `fig_group_title`
) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id 
			
			GROUP BY drv_emp.employee_id, drv_grp.col_grp, drv_info.fig_id
			ORDER BY drv_emp.employee_id, drv_grp.col_grp, drv_info.fig_id"
			
			*/
			/*
			$sqlslip="SELECT drv_emp.employee_id, CONCAT('Col', drv_grp.col_grp) AS col_grp, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, SUM(drv_info.fig_value) AS fig_value FROM (SELECT employees.id AS employee_id, employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location, employee_payslips.payment_period_fr, employee_payslips.payment_period_to FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payroll_process_type_id=1 AND employees.emp_location=1 AND employee_payslips.payslip_cancel=0 AND (employee_payslips.payment_period_to BETWEEN '2020-08-01' AND '2020-12-01')) AS drv_emp INNER JOIN (SELECT (6-col_grp) AS col_grp, DATE_ADD(col_date, INTERVAL -1*(DAY(col_date)-1)*(col_date<>'2020-08-02') DAY) AS col_fr, DATE_ADD(max_date, INTERVAL -1*(DAY(max_date))*(col_date<>'2021-01-02') DAY) AS col_to from (select i AS col_grp, DATE_ADD('2021-01-02', INTERVAL -1*t0.i MONTH) AS col_date, DATE_ADD('2021-01-02', INTERVAL -1*t0.u MONTH) AS max_date from (select 0 i, 0 u union select 1, 0 union select 2, 1 union select 3, 2 union select 4, 3 union select 5, 4) t0) as drv ORDER BY col_date ASC) AS drv_grp ON (drv_emp.payment_period_fr>=drv_grp.col_fr AND drv_emp.payment_period_to<=drv_grp.col_to) INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id GROUP BY drv_emp.employee_id, drv_grp.col_grp, drv_info.fig_id ORDER BY drv_emp.employee_id, drv_grp.col_grp, drv_info.fig_id";
			*/
			$sqlslip="SELECT drv_emp.employee_id, CONCAT('Col', drv_grp.col_grp) AS col_grp, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, SUM(drv_info.fig_value) AS fig_value FROM (SELECT employees.id AS employee_id, employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payment_period_fr, employee_payslips.payment_period_to FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payroll_process_type_id=? AND employees.emp_company=? AND employee_payslips.payslip_cancel=0 AND (employee_payslips.payment_period_to BETWEEN ? AND ?)) AS drv_emp INNER JOIN (SELECT (6-col_grp) AS col_grp, DATE_ADD(col_date, INTERVAL -1*(DAY(col_date)-1)*(col_date<>?) DAY) AS col_fr, DATE_ADD(max_date, INTERVAL -1*(DAY(max_date))*(col_date<>?) DAY) AS col_to from (select i AS col_grp, DATE_ADD(?, INTERVAL -1*t0.i MONTH) AS col_date, DATE_ADD(?, INTERVAL -1*t0.u MONTH) AS max_date from (select 0 i, 0 u union select 1, 0 union select 2, 1 union select 3, 2 union select 4, 3 union select 5, 4) t0) as drv ORDER BY col_date ASC) AS drv_grp ON (drv_emp.payment_period_fr>=drv_grp.col_fr AND drv_emp.payment_period_to<=drv_grp.col_to) INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id GROUP BY drv_emp.employee_id, drv_grp.col_grp, drv_info.fig_id ORDER BY drv_emp.employee_id, drv_grp.col_grp, drv_info.fig_id";
			
			$employee = DB::select($sqlslip, [$paymentPeriod->payroll_process_type_id, $request->location_filter_id, $payment_period_fr, $payment_period_to, $payment_period_fr, $payment_period_to, $payment_period_to, $payment_period_to]);
			/*
			$employee = DB::select($sqlslip);
			*/
			
			$employee_list = array();
			$fig_list = array('BASIC'=>0, 'NOPAY'=>0, 'OTHRS'=>0, 'FACILITY'=>0, 'LOAN'=>0, 'ADDITION'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'PAYE'=>0);
			$cnt = 0;
			$act_employee_id = '';
			
			foreach($employee as $r){
				//$process_name='';//isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
				if($act_employee_id!=$r->employee_id){
					$cnt++;
					$act_employee_id=$r->employee_id;
				}
				if(!isset($employee_list[$cnt-1])){
					$employee_list[]=array('id'=>$r->employee_id, 'emp_first_name'=>$r->emp_first_name, 'location'=>$r->location, 'Col1'=>$fig_list, 'Col2'=>$fig_list, 'Col3'=>$fig_list, 'Col4'=>$fig_list, 'Col5'=>$fig_list, 'Col6'=>$fig_list, 'payslip_cancel'=>1);
					
					
				}
				
				$employee_list[$cnt-1][$r->col_grp][$r->fig_group_title]=number_format((float)$r->fig_value, 4, '.', '');
			}
			
			return response()->json(["colslist"=>$cols, 'employee_detail'=>$employee_list, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	
	public function reportAddition()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		
		
		return view('Payroll.periodicReport.salary_additions',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	
	public function reportHeldSalaries()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		
		
		return view('Payroll.periodicReport.salaryHeld_payment',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	
	public function checkHeldSalaryList(Request $request){
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'department_filter_id' => 'required',
				'period_filter_id' => 'required'
			);
			
			if((!$request->chk_held)&&(!$request->chk_released)){
				$rules['chk_held']='required_without:chk_released';
			}
			
			$error = Validator::make($request->all(), $rules, 
							[
							 'chk_held.required_without'=>'Choose held or released'
							]
						);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::find($request->period_filter_id);
			
			$payment_period_id=$paymentPeriod->id;//1;
			$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
			$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
			
			$filter_salary_status = '-1';
			
			if(($request->chk_held)&&($request->chk_released)){
				$filter_salary_status = '0,1';
			}else{
				$reg_held = $request->chk_held?1:0;
				$reg_released = $request->chk_released?2:0;
				$filter_salary_status = 2-($reg_held+$reg_released);
			}
			
			
			$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_payheld.payslip_remarks AS payheld_reason, drv_payheld.release_remarks AS payrelease_reason, drv_emp.payslip_held, drv_emp.payslip_approved, drv_payheld.payslip_remarks_file, drv_payheld.release_remarks_file FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0 AND employee_payslips.payslip_held_cnt=1 AND employee_payslips.payslip_held IN (?)) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id LEFT OUTER JOIN (SELECT employee_payslip_id, payslip_remarks, release_remarks, payslip_remarks_file, release_remarks_file FROM employee_salary_remarks) AS drv_payheld ON drv_emp.emp_payslip_id=drv_payheld.employee_payslip_id ORDER BY drv_info.fig_id";
			/*
			
			*/
			$emp_data = DB::select($sqlslip, [$payment_period_id, 
											  $request->location_filter_id, $request->department_filter_id, 
											  $filter_salary_status, 
											  $payment_period_id]
								   );
			
			
			$emp_array = array();
			
			
			$cnt = 0;
			$act_payslip_id = '';
			$net_payslip_fig_value = 0;
			
			foreach($emp_data as $r){
				if($act_payslip_id!=$r->emp_payslip_id){
					$cnt++;
					$act_payslip_id=$r->emp_payslip_id;
					$net_payslip_fig_value = 0;
				}
				if(!isset($emp_array[$cnt-1])){
					$payslip_remarks_file = '';
					if($r->payslip_remarks_file!=''){
						$payslip_remarks_file=url('/public/regdocs/'.md5($act_payslip_id.'_HL').'.'.$r->payslip_remarks_file);
					}
					
					$release_remarks_file = '';
					if($r->release_remarks_file!=''){
						$release_remarks_file=url('/public/regdocs/'.md5($act_payslip_id.'_AL').'.'.$r->release_remarks_file);
					}
					
					$emp_array[] = array('Name' => $r->emp_first_name, 'Office' => $r->location, 'BASIC'=>0, 'NOPAY'=>0, 'OTHRS'=>0, 'FACILITY'=>0, 'LOAN'=>0, 'ADDITION'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'PAYE'=>0, 'NETSAL'=>0, 'payslip_held'=>$r->payslip_held, 'payheld_reason'=>$r->payheld_reason, 'payrelease_reason'=>$r->payrelease_reason, 'attached_files'=>'---', 'payslip_remarks_file'=>$payslip_remarks_file, 'release_remarks_file'=>$release_remarks_file);
					
				}
				if(isset($emp_array[$cnt-1][$r->fig_group_title])){
					$emp_array[$cnt-1][$r->fig_group_title]=number_format((float)abs($r->fig_value), 2, '.', '');
					
					if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
						$net_payslip_fig_value+=$r->fig_value;
						$emp_array[$cnt-1]['NETSAL']=number_format((float)$net_payslip_fig_value, 2, '.', '');
					}
				}
			}
			
			return response()->json(['employee_detail'=>$emp_array, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	public function downloadHeldSalaryList(Request $request){
		$paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
		
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
		$filter_salary_status = '-1';
		
		if(($request->rpt_chk_held)&&($request->rpt_chk_released)){
			$filter_salary_status = '0,1';
		}else{
			$reg_held = $request->rpt_chk_held?1:0;
			$reg_released = $request->rpt_chk_released?2:0;
			$filter_salary_status = 2-($reg_held+$reg_released);
		}
		
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_payheld.payslip_remarks AS payheld_reason, drv_payheld.release_remarks AS payrelease_reason, drv_emp.payslip_held, drv_emp.payslip_approved, drv_payheld.payslip_remarks_file, drv_payheld.release_remarks_file FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0 AND employee_payslips.payslip_held_cnt=1 AND employee_payslips.payslip_held IN (?)) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id LEFT OUTER JOIN (SELECT employee_payslip_id, payslip_remarks, release_remarks, payslip_remarks_file, release_remarks_file FROM employee_salary_remarks) AS drv_payheld ON drv_emp.emp_payslip_id=drv_payheld.employee_payslip_id ORDER BY drv_info.fig_id";
		/*
		
		*/
		$emp_data = DB::select($sqlslip, [$payment_period_id, 
										  $request->rpt_location_id, $request->rpt_dept_id, 
										  $filter_salary_status, 
										  $payment_period_id]
							   );
		$sect_name = $request->rpt_dept_name;
		
		$emp_array[] = array('Name', 'Location', 'Net Amount', 'Status', 'Held Reason', 'Release Reason');
		$fig_array = array('Name', 'Office', 'NETSAL', 'payslip_held', 'payheld_reason', 'payrelease_reason');
		
		$cnt = 1;//move to new row //0;
		$act_payslip_id = '';
		$net_payslip_fig_value = 0;
		
		foreach($emp_data as $r){
			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
			}
			if(!isset($emp_array[$cnt-1])){
				$payslip_remarks_file = '';
				/*
				if($r->payslip_remarks_file!=''){
					$payslip_remarks_file=url('/public/regdocs/'.md5($act_payslip_id.'_HL').'.'.$r->payslip_remarks_file);
				}
				*/
				$release_remarks_file = '';
				/*
				if($r->release_remarks_file!=''){
					$release_remarks_file=url('/public/regdocs/'.md5($act_payslip_id.'_AL').'.'.$r->release_remarks_file);
				}
				*/
				$payslip_held_status_txt = ($r->payslip_held==1)?'Payment held':'Payment Released';
				$emp_array[] = array('Name' => $r->emp_first_name, 'Office' => $r->location, 'NETSAL'=>0, 'payslip_held'=>$payslip_held_status_txt, 'payheld_reason'=>$r->payheld_reason, 'payrelease_reason'=>$r->payrelease_reason, 'BASIC'=>0, 'NOPAY'=>0, 'OTHRS'=>0, 'FACILITY'=>0, 'LOAN'=>0, 'ADDITION'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'PAYE'=>0, 'attached_files'=>'---', 'payslip_remarks_file'=>$payslip_remarks_file, 'release_remarks_file'=>$release_remarks_file);
				
			}
			if(isset($emp_array[$cnt-1][$r->fig_group_title])){
				if(in_array($r->fig_group_title, $fig_array)){
					$emp_array[$cnt-1][$r->fig_group_title]=number_format((float)abs($r->fig_value), 2, '.', '');
				}
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$emp_array[$cnt-1]['NETSAL']=number_format((float)$net_payslip_fig_value, 2, '.', '');
				}
			}
		}
		
		if($request->print_record=='1'){
			Excel::create('HeldSalary '.$request->rpt_info, function($excel) use ($emp_array){
				$excel->setTitle('HeldSalary');
				$excel->sheet('HeldSalaries', function($sheet) use ($emp_array){
					$sheet->fromArray($emp_array, null, 'A1', false, false);
				});
			})->download('xlsx');
		}else if($request->print_record=='2'){
			/*
			$emp_array[] = $sum_array;
			*/
			$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
			$pdf = PDF::loadView('Payroll.periodicReport.PayHeld_pdf', compact('emp_array', 'more_info', 'sect_name'));
			return $pdf->download('held-salary-payment.pdf');
			
			/*
			var_dump($emp_array);die;
			*/
		}
	}
	
	public function reportEmpOT()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		
		
		return view('Payroll.periodicReport.emp_overtime',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	
	public function previewEmpOT(Request $request){
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'department_filter_id' => 'required',
				'period_filter_id' => 'required'
			);
	
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::find($request->period_filter_id);
			
			$payment_period_id=$paymentPeriod->id;//1;
			$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
			$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
			
			$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.emp_etfno, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group, drv_info.fig_group_title, ABS(drv_info.fig_base_ratio) as fig_base_ratio, ABS(drv_info.fig_value) AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, employees.emp_etfno, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, fig_group, `fig_group_title`, fig_base_ratio, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE fig_group_title='OTHRS' and `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`, fig_group) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
			/*
			
			*/
			$employee = DB::select($sqlslip, [$payment_period_id, 
											  $request->location_filter_id, $request->department_filter_id, 
											  $payment_period_id]
								   );
			
			
			$employee_list = array();
			$cnt = 0;
			$act_payslip_id = '';
			
			foreach($employee as $r){
				//$process_name='';//isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
				if($act_payslip_id!=$r->emp_payslip_id){
					$cnt++;
					$act_payslip_id=$r->emp_payslip_id;
				}
				if(!isset($employee_list[$cnt-1])){
					$employee_list[]=array('id'=>$r->emp_payslip_id, 'emp_first_name'=>$r->emp_first_name, 'emp_etfno'=>$r->emp_etfno, 'location'=>$r->location, 'OTHRS1'=>0, 'OTHRS2'=>0, 'OTVAL1'=>0, 'OTVAL2'=>0, 'payslip_cancel'=>1, 'payslip_held'=>$r->payslip_held, 'payslip_approved'=>$r->payslip_approved);
					
					
				}
				
				$employee_list[$cnt-1][$r->fig_group]=number_format((float)$r->fig_base_ratio, 4, '.', '');
				$employee_list[$cnt-1][str_replace('HRS', 'VAL', $r->fig_group)]=number_format((float)($r->fig_value+0), 4, '.', '');
			}
			
			return response()->json(['employee_detail'=>$employee_list, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	
	public function downloadEmpOT(Request $request){
		$paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.emp_etfno, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group, drv_info.fig_group_title, ABS(drv_info.fig_base_ratio) as fig_base_ratio, ABS(drv_info.fig_value) AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, employees.emp_etfno, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, fig_group, `fig_group_title`, fig_base_ratio, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE fig_group_title='OTHRS' and `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`, fig_group) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
		/*
		
		*/
		$employee = DB::select($sqlslip, [$payment_period_id, 
										  $request->rpt_location_id, $request->rpt_dept_id, 
										  $payment_period_id]
							   );
		
		
		$employee_list = array();
		
		$empot_paysums = array('id'=>'', 'emp_first_name'=>'', 'emp_etfno'=>'', 'location'=>'', 'OTHRS1'=>0, 'OTHRS2'=>0, 'OTVAL1'=>0, 'OTVAL2'=>0, 'payslip_cancel'=>1, 'payslip_held'=>0, 'payslip_approved'=>0);
		
		$cnt = 0;
		$act_payslip_id = '';
		
		foreach($employee as $r){
			//$process_name='';//isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
			}
			if(!isset($employee_list[$cnt-1])){
				$employee_list[]=array('id'=>$r->emp_payslip_id, 'emp_first_name'=>$r->emp_first_name, 'emp_etfno'=>$r->emp_etfno, 'location'=>$r->location, 'OTHRS1'=>0, 'OTHRS2'=>0, 'OTVAL1'=>0, 'OTVAL2'=>0, 'payslip_cancel'=>1, 'payslip_held'=>$r->payslip_held, 'payslip_approved'=>$r->payslip_approved);
				
				
			}
			
			$employee_list[$cnt-1][$r->fig_group]=number_format((float)$r->fig_base_ratio, 4, '.', '');
			$tot_column_id=str_replace('HRS', 'VAL', $r->fig_group);
			$employee_list[$cnt-1][$tot_column_id]=number_format((float)($r->fig_value+0), 4, '.', '');
			$emptot_actsums=$empot_paysums[$tot_column_id];
			$empot_paysums[$tot_column_id]=number_format((float)($r->fig_value+$emptot_actsums), 4, '.', '');
		}
		
		$more_info=$payment_period_fr.' / '.$payment_period_to;
		$sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');
		
		ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");
		
		$pdf = PDF::loadView('Payroll.periodicReport.EmpOvertime_pdf', compact('employee_list', 'empot_paysums', 'more_info', 'sect_name', 'paymonth_name'));
        return $pdf->download('employee-ot-list.pdf');
	}
	
}
