<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
//use App\Department;

use App\Employee;
use App\EmployeePayslip;
use App\EmployeeSalaryRemark;

use App\JobCategory;

use App\PaymentPeriod;
use App\PayrollProcessType;

use App\PayrollProfile;
/*
use App\Remuneration;
*/
use App\RemunerationTaxation;

use Carbon;
use Excel;

use DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;

use PDF; 

use Validator;

class EmployeePayslipController extends Controller
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
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		
        return view('Payroll.payslipProcess.payslipProcess_list',compact('branch', 'payroll_process_type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
		
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeePayslip  $payslip
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeePayslip $payslip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeePayslip  $payslip
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeePayslip  $payslip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeePayslip $payslip)
    {
		
	}
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeePayslip  $payslip
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
		
	}
	
	
	/**/
	
	public function checkAttendance(Request $request){
		if($request->ajax()){
			$rules = array(
				'location_filter_id' => 'required'
			);
	
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			/*
			$employee = DB::select("SELECT employees.emp_first_name, payroll_profiles.id as payroll_profile_id, (IFNULL(drv_payslips.emp_payslip_no, 0)+1) AS emp_payslip_no, payroll_process_types.process_name, payroll_profiles.basic_salary, branches.branch as location, IFNULL(drv_loan.installment_value, 0) AS installment_value, '' as payslip_id, '1' AS payslip_cancel FROM employees INNER JOIN branches ON employees.emp_location=branches.id INNER JOIN payroll_profiles ON employees.id=payroll_profiles.emp_id INNER JOIN payroll_process_types ON payroll_profiles.payroll_process_type_id=payroll_process_types.id LEFT OUTER JOIN (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips GROUP BY payroll_profile_id) AS drv_payslips ON payroll_profiles.id=drv_payslips.payroll_profile_id LEFT OUTER JOIN (select drv_allpay.payroll_profile_id, SUM(drv_allpay.installment_value*(drv_allpay.emp_payslip_no=drv_newpay.last_payslip_no)) AS installment_value from (SELECT payroll_profile_id, sum(installment_value) AS installment_value, max(emp_payslip_no) as emp_payslip_no FROM `employee_loan_installments` WHERE installment_cancel=0 group by payroll_profile_id, emp_payslip_no) as drv_allpay inner join (select payroll_profile_id, max(emp_payslip_no) as last_payslip_no from employee_loan_installments where installment_cancel=0 group by payroll_profile_id) as drv_newpay on drv_allpay.payroll_profile_id=drv_newpay.payroll_profile_id GROUP BY drv_allpay.payroll_profile_id) AS drv_loan ON payroll_profiles.id=drv_loan.payroll_profile_id WHERE payroll_process_types.id=? AND branches.id=?", [$request->payroll_process_type_id, $request->location_filter_id]);
			*/
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::where(['payroll_process_type_id'=>$request->payroll_process_type_id])
							->latest()
							->first();
			
			if(empty($paymentPeriod)){
				return response()->json(['errors' => array($payroll_process_types[$request->payroll_process_type_id].' Payment schedule must be defined')]);
			}
			
			$payment_period_id=$paymentPeriod->id;//1;
			$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
			$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
			
			$sqlcols="SELECT drv_main.emp_first_name, drv_main.employee_id, drv_main.emp_etfno, drv_main.emp_job_code, drv_main.emp_status, drv_main.payroll_profile_id, drv_main.emp_payslip_no, drv_main.emp_payslip_id as emp_payslip_id, drv_main.process_name, drv_main.pay_per_day, drv_main.basic_salary, drv_main.day_salary, drv_main.location, drv_main.work_days, drv_main.leave_days, drv_main.nopay_days, drv_main.normal_rate_otwork_hrs, drv_main.double_rate_otwork_hrs, IFNULL(drv_loan.installment_value, 0) AS installment_value, IFNULL(drv_term.payment_amount, 0) AS payment_amount, drv_main.work_days_exclusions, drv_main.payslip_cancel FROM ";
			$sqlmain="(SELECT employees.emp_name_with_initial AS emp_first_name, employees.id AS employee_id, IFNULL(employees.emp_etfno, '') AS emp_etfno, employees.emp_job_code, employees.emp_status, payroll_profiles.id as payroll_profile_id, IFNULL(drv_key.emp_payslip_no, 1) AS emp_payslip_no, IFNULL(drv_payslips.emp_payslip_id, '') AS emp_payslip_id, IFNULL(drv_payslips.payroll_process_type_id, payroll_process_types.id) AS process_name, payroll_process_types.pay_per_day, payroll_profiles.basic_salary, payroll_profiles.day_salary, companies.name as location, IFNULL(drv_payslips.work_days, drv_workrate.work_days) AS work_days, IFNULL(drv_payslips.leave_days, drv_workrate.leave_days) AS leave_days, IFNULL(drv_payslips.nopay_days, drv_workrate.nopay_days) AS nopay_days, IFNULL(drv_payslips.normal_rate_otwork_hrs, drv_workrate.normal_rate_otwork_hrs) AS normal_rate_otwork_hrs, IFNULL(drv_payslips.double_rate_otwork_hrs, drv_workrate.double_rate_otwork_hrs) AS double_rate_otwork_hrs, IFNULL(drv_payslips.work_days_exclusions, drv_workrate.work_days_exclusions) AS work_days_exclusions, IFNULL(drv_payslips.payslip_cancel, 1) AS payslip_cancel FROM employees INNER JOIN companies ON employees.emp_company=companies.id INNER JOIN payroll_profiles ON employees.id=payroll_profiles.emp_id INNER JOIN payroll_process_types ON payroll_profiles.payroll_process_type_id=payroll_process_types.id ";
			
			/*
			group-by-emp-id-instead-of-emp-etfno
			*/
			$sqlmain.="INNER JOIN (SELECT emp_id, emp_etfno, SUM(work_days) AS work_days, SUM(leave_days) AS leave_days, SUM(nopay_days) AS nopay_days, SUM(normal_rate_otwork_hrs) AS normal_rate_otwork_hrs, SUM(double_rate_otwork_hrs) AS double_rate_otwork_hrs, SUM(work_days_exclusions) AS work_days_exclusions FROM (SELECT emp_id, emp_etfno, work_days, leave_days, nopay_days, normal_rate_otwork_hrs, double_rate_otwork_hrs, working_week_days AS work_days_exclusions FROM employee_work_rates WHERE (work_year IN (YEAR(?), YEAR(?)) AND work_month IN (MONTH(?), MONTH(?))) UNION ALL SELECT emp_id, emp_etfno, (work_days*-1) AS work_days, (leave_days*-1) AS leave_days, (nopay_days*-1) AS nopay_days, (normal_rate_otwork_hrs*-1) AS normal_rate_otwork_hrs, (double_rate_otwork_hrs*-1) AS double_rate_otwork_hrs, (work_days_exclusions*-1) AS work_days_exclusions FROM employee_paid_rates WHERE (salary_process_year IN (YEAR(?), YEAR(?)) AND salary_process_month IN (MONTH(?), MONTH(?)))) AS drv_workprog GROUP BY emp_id) AS drv_workrate ON employees.id=drv_workrate.emp_id ";
			
			$sqlmain.="LEFT OUTER JOIN (SELECT payroll_profile_id, (MAX(emp_payslip_no)+1) AS emp_payslip_no FROM employee_payslips WHERE payment_period_to<? GROUP BY payroll_profile_id) AS drv_key ON payroll_profiles.id=drv_key.payroll_profile_id ";
			$sqlmain.="LEFT OUTER JOIN (SELECT drv_pre.id as emp_payslip_id, drv_pre.payroll_profile_id, drv_pre.emp_payslip_no, drv_pre.payroll_process_type_id, drv_pre.basic_salary, drv_paidrate.work_days, drv_paidrate.leave_days, drv_paidrate.nopay_days, drv_paidrate.normal_rate_otwork_hrs, drv_paidrate.double_rate_otwork_hrs, drv_paidrate.work_days_exclusions, drv_pre.payslip_cancel FROM (SELECT id, payroll_profile_id, emp_payslip_no, payroll_process_type_id, basic_salary, payslip_cancel FROM employee_payslips WHERE payment_period_to>=?) AS drv_pre LEFT OUTER JOIN (SELECT employee_payslip_id, SUM(work_days) AS work_days, SUM(leave_days) AS leave_days, SUM(nopay_days) AS nopay_days, SUM(normal_rate_otwork_hrs) AS normal_rate_otwork_hrs, SUM(double_rate_otwork_hrs) AS double_rate_otwork_hrs, SUM(work_days_exclusions) AS work_days_exclusions FROM employee_paid_rates WHERE (salary_process_year IN(YEAR(?), YEAR(?)) AND salary_process_month IN (MONTH(?), MONTH(?))) GROUP BY employee_payslip_id) AS drv_paidrate ON drv_pre.id=drv_paidrate.employee_payslip_id) AS drv_payslips ON (payroll_profiles.id=drv_payslips.payroll_profile_id AND IFNULL(drv_key.emp_payslip_no, 1)=drv_payslips.emp_payslip_no) WHERE payroll_process_types.id=? AND companies.id=? AND employees.deleted=0 AND employees.is_resigned=0) AS drv_main ";
			/*
			$sqlmain.="LEFT OUTER JOIN (SELECT drv_key.payroll_profile_id, (drv_key.emp_payslip_no+1) AS emp_payslip_no, drv_pre.id as emp_payslip_id, drv_pre.payroll_process_type_id, drv_pre.basic_salary, drv_paidrate.work_days, drv_paidrate.leave_days, drv_paidrate.nopay_days, drv_paidrate.normal_rate_otwork_hrs, drv_paidrate.double_rate_otwork_hrs, drv_pre.payslip_cancel FROM (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips WHERE payment_period_to<? GROUP BY payroll_profile_id) AS drv_key LEFT OUTER JOIN (SELECT id, payroll_profile_id, emp_payslip_no, payroll_process_type_id, basic_salary, payslip_cancel FROM employee_payslips WHERE payment_period_to>=?) AS drv_pre ON (drv_key.payroll_profile_id=drv_pre.payroll_profile_id AND (drv_key.emp_payslip_no+1)=drv_pre.emp_payslip_no) LEFT OUTER JOIN (SELECT employee_payslip_id, SUM(work_days) AS work_days, SUM(leave_days) AS leave_days, SUM(nopay_days) AS nopay_days, SUM(normal_rate_otwork_hrs) AS normal_rate_otwork_hrs, SUM(double_rate_otwork_hrs) AS double_rate_otwork_hrs FROM employee_paid_rates WHERE (salary_process_year IN(YEAR(?), YEAR(?)) AND salary_process_month IN (MONTH(?), MONTH(?)))) AS drv_paidrate ON drv_pre.id=drv_paidrate.employee_payslip_id) AS drv_payslips ON payroll_profiles.id=drv_payslips.payroll_profile_id WHERE payroll_process_types.id=? AND branches.id=?) AS drv_main ";
			*/
			$sqlloan="LEFT OUTER JOIN (SELECT payroll_profile_id, sum(installment_value) AS installment_value, emp_payslip_no as emp_payslip_no FROM `employee_loan_installments` WHERE installment_cancel=0 group by payroll_profile_id, emp_payslip_no) AS drv_loan ON (drv_main.payroll_profile_id=drv_loan.payroll_profile_id AND drv_main.emp_payslip_no=drv_loan.emp_payslip_no) ";
			
			$sqlterm="LEFT OUTER JOIN (SELECT employee_term_payments.payroll_profile_id, sum(employee_term_payments.payment_amount * drv_terminfo.value_group) AS payment_amount, employee_term_payments.emp_payslip_no as emp_payslip_no FROM `employee_term_payments` INNER JOIN (SELECT id, value_group FROM remunerations WHERE allocation_method='TERMS') AS drv_terminfo ON employee_term_payments.remuneration_id = drv_terminfo.id WHERE employee_term_payments.payment_cancel=0 group by employee_term_payments.payroll_profile_id, employee_term_payments.emp_payslip_no) AS drv_term ON (drv_main.payroll_profile_id = drv_term.payroll_profile_id AND drv_main.emp_payslip_no = drv_term.emp_payslip_no)";
			
			$sqlslip=$sqlcols.$sqlmain.$sqlloan.$sqlterm;
			/*
			
			*/
			$employee = DB::select($sqlslip, [$payment_period_fr, $payment_period_to, $payment_period_fr, $payment_period_to, $payment_period_fr, $payment_period_to, $payment_period_fr, $payment_period_to, $payment_period_to, $payment_period_to, $payment_period_fr, $payment_period_to, $payment_period_fr, $payment_period_to, $request->payroll_process_type_id, $request->location_filter_id]);
			
			
			$employee_list = array();
			
			
			foreach($employee as $r){
				$process_name=isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
				
				$employee_list[]=array('id'=>$r->emp_payslip_id, 'payroll_profile_id'=>$r->payroll_profile_id, 'employee_id'=>$r->employee_id, 'emp_etfno'=>$r->emp_etfno, 'emp_job_code'=>$r->emp_job_code, 'emp_status'=>$r->emp_status, 'emp_payslip_no'=>$r->emp_payslip_no, 'emp_first_name'=>$r->emp_first_name, 'location'=>$r->location, 'basic_salary'=>$r->basic_salary, 'day_salary'=>$r->day_salary, 'process_name'=>$process_name, 'pay_per_day'=>$r->pay_per_day, 'loan_installments'=>$r->installment_value, 'term_payments'=>$r->payment_amount, 'opt_work'=>$r->work_days_exclusions, 'emp_work'=>$r->work_days, 'emp_leave'=>$r->leave_days, 'emp_nopay'=>$r->nopay_days, 'emp_ot1'=>$r->normal_rate_otwork_hrs, 'emp_ot2'=>$r->double_rate_otwork_hrs, 'payslip_cancel'=>$r->payslip_cancel);
			}
			
			return response()->json(['employee_detail'=>$employee_list, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	
	public function freeze(Request $request){
		try{
			return DB::transaction(function() use ($request){
				if($request->ajax()){
					/*
					pre-checks-to-be-done
					---------------------
					emp-payslip-no doesn't exist
					salary-process-period is active and latest
					*/
					
					$employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id, 
															   'emp_payslip_no' => $request->emp_payslip_no])
										//->get();
										->latest()
										->first();
					//
					$employee_payslip_id=empty($employeePayslip)?'':($employeePayslip->id);//$request->id;
					
					$form_data = array(
						'payroll_profile_id' => $request->payroll_profile_id,
						'emp_payslip_no' => $request->emp_payslip_no,
						'payment_period_id' => $request->payment_period_id,
						'payment_period_fr' => $request->payment_period_fr,
						'payment_period_to' => $request->payment_period_to,
						'payslip_cancel' =>  $request->payslip_cancel,
						'payroll_process_type_id' => $request->payroll_process_type_id,
						'basic_salary' => $request->basic_salary,
						'day_salary' => $request->day_salary
					);
					
					
					$affectedRows=0;
					$payperiod_workdays=0;
					$payperiod_holidays=0;
					
					$payperiod_workhrs=0;
					
					$feedback_msg='';
					
					//2025-02-25
					$salary_part_adjval=1;//calculate basic, facility part payments for monthly employee first salary
					$full_pay_restoreval=1;//calculate nopay, ot amount of monthly employees full first salary
					//-25
					
					/* 1. create-or-cancel-payslip */
					
					if($employee_payslip_id==''){
						$payperiod_workinfo = PayrollProcessType::find($request->payroll_process_type_id);
						
						$employeePayProfile = PayrollProfile::find($request->payroll_profile_id);
						$employeeJobCategory = JobCategory::findOrFail($employeePayProfile->payroll_act_id);
						
						if($request->pay_per_day==0){
							$payperiod_workdays = $employeeJobCategory->emp_payroll_workdays;//30;//$payperiod_workinfo->total_work_days;
							$payperiod_workhrs = $employeeJobCategory->emp_payroll_workhrs;
							
							//2025-02-25
							$exp_workdays = $request->emp_work+$request->emp_nopay;
							//if(($request->emp_payslip_no=='1')&&($payperiod_workdays>$exp_workdays))
							/*if(($request->emp_payslip_no=='1')&&($exp_workdays<20)){	
								$salary_part_adjval=$exp_workdays/$payperiod_workdays;
								$full_pay_restoreval=$payperiod_workdays/$exp_workdays;
							}*/
							//-25
						}else{
							$payperiod_workdays = $request->opt_work;
							$payperiod_workhrs = $payperiod_workdays*8;
						}
						
						if(($payperiod_workdays*$payperiod_workhrs)<=0){
							throw new \Exception('Job category details must be updated first');
						}
						
						$paypreiod_holidayinfo = DB::select("SELECT COUNT(*) AS holidaycnt FROM holidays WHERE `date` BETWEEN ? AND ?", [$request->payment_period_fr, $request->payment_period_to]);
						$payperiod_holidays = 0;//$paypreiod_holidayinfo[0]->holidaycnt;
						
						
						$form_data['created_by']=$request->user()->id;
						$form_data['created_at']=date('Y-m-d H:i:s');
						
						$affectedRows=DB::table('employee_payslips')
							->insert($form_data);
						$employee_payslip_id=DB::getPdo()->lastInsertId();
					}else{
						$form_data['updated_by']=$request->user()->id;
						$form_data['updated_at']=date('Y-m-d H:i:s');
						
						$affectedRows=DB::table('employee_payslips')
							->where(['id'=>$employee_payslip_id, 'payslip_cancel'=>(1-$request->payslip_cancel),
									'payslip_held'=>0, 'payslip_approved'=>0])
							->update($form_data);
					}
					/**/
					if($affectedRows==1){
						$feedback_msg='success';
						
					}else{
						throw new \Exception('Payslip update error');
					}
					
					/* -- create-or-cancel-payslip */
					
					
					
					if(empty($employeePayslip)){
						
						/* 2. calculate work-leave-nopay-ot_hrs */
						
						$affectedWork=0;
						//20240506--
						//$epf_registered=(($request->pay_per_day==0)&&($request->emp_etfno!=''))?1:0;//($request->emp_etfno=='')?0:1;
						$epf_registered=($request->emp_etfno=='')?0:1;
						/**/
						if($epf_registered==1){
							$empProfile = PayrollProfile::find($request->payroll_profile_id);//check-contribution
							$epf_contribution = ($empProfile->epfetf_contribution=="ACTIVE")?1:0;
							/*
							$empInfo = Employee::where(['emp_etfno'=>$request->emp_etfno])->first();
							if(!empty($empInfo)){
								$epf_registered=($empInfo->emp_status=='1')?0:$epf_registered;//exclude-epf-for-trainees
							}else{
								throw new \Exception('Employee information error');
							}
							*/
							$epf_registered*=$epf_contribution;
						}
						
						//--20240506
						/*
						$res=DB::insert
						*/
						
						$affectedWork=DB::affectingStatement("INSERT INTO employee_paid_rates (employee_payslip_id, emp_id, emp_etfno, payroll_profile_id, emp_payslip_no, epf_registered, emp_status, emp_job_code, payroll_act_id, employee_bank_id, salary_process_year, salary_process_month, work_days, leave_days, nopay_days, normal_rate_otwork_hrs, double_rate_otwork_hrs, work_days_exclusions, created_by, created_at) SELECT ? AS employee_payslip_id, drv_head.emp_id, ? AS emp_etfno, drv_head.payroll_profile_id, ? AS emp_payslip_no, ? AS epf_registered, ? AS emp_status, ? AS emp_job_code, drv_head.payroll_act_id, drv_head.employee_bank_id, drv_info.salary_process_year, drv_info.salary_process_month, SUM(drv_info.work_days) AS work_days, SUM(drv_info.leave_days) AS leave_days, SUM(drv_info.nopay_days) AS nopay_days, SUM(drv_info.normal_rate_otwork_hrs) AS normal_rate_otwork_hrs, SUM(drv_info.double_rate_otwork_hrs) AS double_rate_otwork_hrs, SUM(drv_info.work_days_exclusions) AS work_days_exclusions, ? AS created_by, NOW() AS created_at FROM (SELECT id AS payroll_profile_id, emp_id, payroll_act_id, employee_bank_id FROM payroll_profiles WHERE id=?) AS drv_head CROSS JOIN (SELECT `work_year` AS salary_process_year, `work_month` AS salary_process_month, `work_days`, `leave_days`, `nopay_days`, `normal_rate_otwork_hrs`, `double_rate_otwork_hrs`, working_week_days as work_days_exclusions FROM `employee_work_rates` WHERE `emp_id`=? AND (`work_year` IN (YEAR(?), YEAR(?)) AND `work_month` IN (MONTH(?), MONTH(?))) UNION ALL SELECT salary_process_year, salary_process_month, (`work_days`*-1) AS work_days, (`leave_days`*-1) AS leave_days, (`nopay_days`*-1) AS nopay_days, (`normal_rate_otwork_hrs`*-1) AS normal_rate_otwork_hrs, (`double_rate_otwork_hrs`*-1) AS double_rate_otwork_hrs, (`work_days_exclusions`*-1) AS work_days_exclusions FROM `employee_paid_rates` WHERE `emp_id`=? AND (`salary_process_year` IN (YEAR(?), YEAR(?)) AND `salary_process_month` IN (MONTH(?), MONTH(?)))) AS drv_info GROUP BY salary_process_year, salary_process_month HAVING work_days>0", [$employee_payslip_id, $request->emp_etfno, $form_data['emp_payslip_no'], $epf_registered, $request->emp_status, $request->emp_job_code, $request->user()->id, $form_data['payroll_profile_id'], $request->employee_id, $form_data['payment_period_fr'], $form_data['payment_period_to'], $form_data['payment_period_fr'], $form_data['payment_period_to'], $request->employee_id, $form_data['payment_period_fr'], $form_data['payment_period_to'], $form_data['payment_period_fr'], $form_data['payment_period_to']]);
						
						if($affectedWork==0){
							throw new \Exception('Employee work hours calculation error');
						}
						
						/* -- calculate work-leave-nopay-ot_hrs */
						
						/* 3. value-payslip-figures-for-work-leave-nopay-ot_hrs */
						
						$sql_cols="INSERT INTO employee_salary_payments (employee_payslip_id, employee_paid_rate_id, payment_period_id, payroll_profile_id, emp_payslip_no, fig_name, fig_group, fig_group_title, fig_base_ratio, fig_value, fig_hidden, epf_payable, remuneration_payslip_spec_code, remuneration_taxcalc_spec_code) SELECT ? AS employee_payslip_id, employee_paid_rate_id, ? AS payment_period_id, ? AS payroll_profile_id, ? AS emp_payslip_no, fig_name, fig_group, fig_group_title, fig_base_ratio, fig_value, fig_hidden, epf_payable, remuneration_pssc, remuneration_tcsc FROM ";
						$sql_main="(SELECT drv_work.employee_paid_rate_id, drv_figs.fig_name, drv_figs.fig_group, drv_figs.fig_group_title, drv_figs.fig_value AS fig_base_ratio, COALESCE(NULLIF(drv_figs.fig_value*(((drv_figs.fig_group='FIXED') * drv_work.work_days_exclusions * ?) + ((drv_figs.fig_group='FIXED') * (1 - ?)) + (drv_work.work_days * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (drv_work.leave_days * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (drv_work.nopay_days * drv_figs.nopay_payable * (drv_figs.fig_group='BASIC')) + (drv_work.normal_rate_otwork_hrs * (drv_figs.fig_group='OTHRS1')) + (drv_work.double_rate_otwork_hrs * (drv_figs.fig_group='OTHRS2')))*drv_figs.pay_per_day, 0), (drv_figs.fig_value*drv_figs.fig_revise))*drv_figs.salary_part_adjval AS fig_value, drv_figs.fig_hidden, drv_figs.epf_payable, drv_figs.remuneration_pssc, drv_figs.remuneration_tcsc FROM (SELECT id AS employee_paid_rate_id, `work_days`, `work_days_exclusions`, `leave_days`, `nopay_days`, `normal_rate_otwork_hrs`, `double_rate_otwork_hrs` FROM `employee_paid_rates` WHERE `employee_payslip_id`=?) AS drv_work CROSS JOIN (SELECT 'Basic' AS fig_name, 1 AS fig_norepeat, 'BASIC' AS fig_group, 'BASIC' AS fig_group_title, COALESCE(NULLIF(CAST(?*?*? AS DECIMAL(10,4)), 0), ?) AS fig_value, ? AS salary_part_adjval, 0 AS pay_per_day, 1 AS fig_revise, 0 AS fig_hidden, 1 AS epf_payable, 1 AS work_payable, 1 AS nopay_payable, 'BASIC' AS remuneration_pssc, 'BASIC_TL' AS remuneration_tcsc UNION ALL SELECT 'No pay' AS fig_name, NULL AS fig_norepeat, 'BASIC' AS fig_group, 'NOPAY' AS fig_group_title, ? AS fig_value, 1 AS salary_part_adjval, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 1 AS nopay_payable, 'NOPAY' AS remuneration_pssc, 'NOPAY_TL' AS remuneration_tcsc UNION ALL SELECT 'Normal OT' AS fig_name, NULL AS fig_norepeat, 'OTHRS1' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS salary_part_adjval, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS1' AS remuneration_pssc, 'OTHRS1_TL' AS remuneration_tcsc UNION ALL SELECT 'Double OT' AS fig_name, NULL AS fig_norepeat, 'OTHRS2' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS salary_part_adjval, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS2' AS remuneration_pssc, 'OTHRS2_TL' AS remuneration_tcsc UNION ALL ";
						/*
						$sql_main .= "select drv_allfacility.remuneration_name AS fig_name, IFNULL(drv_allfacility.fig_group, 'BASIC') AS fig_group, 'FACILITY' AS fig_group_title, (IFNULL(drv_dayfacility.pre_eligible_amount, drv_empfacility.new_eligible_amount)*drv_allfacility.value_group) AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, drv_allfacility.epf_payable, 1 AS work_payable, 0 AS nopay_payable, drv_allfacility.pssc AS remuneration_pssc, drv_allfacility.tcsc AS remuneration_tcsc from (SELECT `remuneration_id`, `new_eligible_amount` FROM `remuneration_profiles` WHERE `payroll_profile_id`=? AND `remuneration_signout`=0) AS drv_empfacility INNER JOIN (SELECT id, remuneration_name, remuneration_type, value_group, epf_payable, allocation_method AS fig_group, payslip_spec_code AS pssc, taxcalc_spec_code as tcsc FROM remunerations WHERE allocation_method='FIXED' AND remuneration_cancel=0) AS drv_allfacility ON drv_empfacility.remuneration_id=drv_allfacility.id LEFT OUTER JOIN (SELECT remuneration_id, (pre_eligible_amount+(grp_increment*(?-min_days))) AS pre_eligible_amount, 'FIXED' AS fig_group FROM remuneration_eligibility_days WHERE ? BETWEEN min_days AND max_days) AS drv_dayfacility ON drv_allfacility.id=drv_dayfacility.remuneration_id) AS drv_figs";
						*/
						$sql_main .= "select drv_allfacility.remuneration_name AS fig_name, NULLIF(drv_allfacility.advanced_option_id, 0) AS fig_norepeat, IFNULL(drv_allfacility.fig_group, 'BASIC') AS fig_group, 'FACILITY' AS fig_group_title, (COALESCE(NULLIF(SUM(IFNULL(drv_dayfacility.pre_eligible_amount, 0)*(drv_dayfacility.work_days BETWEEN drv_dayfacility.min_days AND drv_dayfacility.max_days)*(drv_allfacility.opt_exclusions=0)+(IFNULL(drv_dayfacility.pre_eligible_amount, 0)+(drv_dayfacility.grp_increment*(drv_dayfacility.opt_days-drv_dayfacility.min_days)))*(drv_dayfacility.opt_days BETWEEN drv_dayfacility.min_days AND drv_dayfacility.max_days)*(drv_allfacility.opt_exclusions=1)), 0), drv_empfacility.new_eligible_amount)*drv_allfacility.value_group)AS fig_value, ";
						$sql_main .= "IFNULL(NULLIF(drv_allfacility.advanced_option_id, 0)*?, 1) as salary_part_adjval, ";
						$sql_main .= "1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, drv_allfacility.epf_payable, 1 AS work_payable, 0 AS nopay_payable, drv_allfacility.pssc AS remuneration_pssc, drv_allfacility.tcsc AS remuneration_tcsc from (SELECT `remuneration_id`, `new_eligible_amount` FROM `remuneration_profiles` WHERE `payroll_profile_id`=? AND `remuneration_signout`=0) AS drv_empfacility INNER JOIN (SELECT id, remuneration_name, remuneration_type, value_group, epf_payable, allocation_method AS fig_group, payslip_spec_code AS pssc, taxcalc_spec_code as tcsc, employee_work_rate_work_days_exclusions AS opt_exclusions, advanced_option_id FROM remunerations WHERE allocation_method='FIXED' AND remuneration_cancel=0) AS drv_allfacility ON drv_empfacility.remuneration_id=drv_allfacility.id LEFT OUTER JOIN (SELECT remuneration_id, pre_eligible_amount, grp_increment, min_days, max_days, (1*?) AS work_days, (1*?) AS opt_days, 'FIXED' AS fig_group FROM remuneration_eligibility_days WHERE ((? BETWEEN min_days AND max_days) OR (? BETWEEN min_days AND max_days))) AS drv_dayfacility ON drv_allfacility.id=drv_dayfacility.remuneration_id GROUP BY drv_allfacility.id) AS drv_figs ";
						$sql_main .= "GROUP BY IFNULL(drv_figs.fig_norepeat, drv_work.employee_paid_rate_id), ";//fig_norepeat
						$sql_main .= "md5(concat(drv_figs.fig_name, '_', drv_figs.fig_group, '_', drv_figs.fig_group_title))";
						$sql_main .= ") AS drv_main";
						$res=DB::insert($sql_cols.$sql_main, [$employee_payslip_id, $form_data['payment_period_id'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $request->pay_per_day, $request->pay_per_day, $employee_payslip_id, $request->day_salary, $request->pay_per_day, $request->opt_work, $request->basic_salary, $salary_part_adjval, ($request->day_salary*-1), ($request->day_salary/8), (($request->day_salary*1)/8), $salary_part_adjval, $form_data['payroll_profile_id'], $request->emp_work, $request->opt_work, $request->emp_work, $request->opt_work]);
						
						if(!$res){
							throw new \Exception('Unable to save payslip details');
						}
						
						/* -- value-payslip-figures-for-work-leave-nopay-ot_hrs */
						
						/* 2023-10-03-keep-remuneration-eligible-day-rates-with-work-days */
						$fixedrates=DB::select("select count(*) as grpcnt from (SELECT remuneration_id FROM remuneration_profiles WHERE payroll_profile_id=? AND remuneration_signout=0) AS drv_fixpay INNER JOIN remunerations ON drv_fixpay.remuneration_id=remunerations.id WHERE remunerations.employee_work_rate_work_days_exclusions=1 AND remunerations.remuneration_cancel=0", [$form_data['payroll_profile_id']]);
						if(($fixedrates[0]->grpcnt)>=1){
							$sql_grpfigs = "insert into remuneration_eligible_paid_rates (employee_payslip_id, remuneration_id, remuneration_eligibility_day_id, pre_eligible_amount, grp_increment) select ? as employee_payslip_id, remuneration_profiles.remuneration_id, IFNULL(remuneration_eligibility_days.id, 0) AS eligible_day_id, IFNULL(remuneration_eligibility_days.pre_eligible_amount, 0) AS pre_eligible_amount, IFNULL(remuneration_eligibility_days.grp_increment, 0) AS grp_increment from remuneration_profiles inner join remunerations on remuneration_profiles.remuneration_id=remunerations.id left outer join remuneration_eligibility_days on (remunerations.id=remuneration_eligibility_days.remuneration_id and (? between remuneration_eligibility_days.min_days and remuneration_eligibility_days.max_days)) where remunerations.employee_work_rate_work_days_exclusions=1 and remuneration_profiles.payroll_profile_id=? and remuneration_profiles.remuneration_signout=0 and remunerations.remuneration_cancel=0";
							
							$res=DB::insert($sql_grpfigs, [$employee_payslip_id, $request->opt_work, $form_data['payroll_profile_id']]);
							
							if(!$res){
								throw new \Exception('Unable to save week days work payment details');
							}
						}
						/* -- keep-remuneration-eligible-day-rates-with-work-days */
						
						/* 4. loan-installments-and-term-payments */
						$pre_sqls="select count(*) as optcnt from (SELECT remuneration_id FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS drv_termpay INNER JOIN remunerations ON drv_termpay.remuneration_id=remunerations.id inner join remuneration_exemptions on remunerations.id=remuneration_exemptions.remuneration_id";
						$pre_exemption=DB::select($pre_sqls, [$form_data['payroll_profile_id'], $form_data['emp_payslip_no']]);
						$optcnt=$pre_exemption[0]->optcnt;
						
						if($optcnt==0){
							$sql_term="(SELECT drv_batch.employee_paid_rate_id, drv_docs.fig_name, drv_docs.fig_group, drv_docs.fig_group_title, drv_docs.fig_value AS fig_base_ratio, drv_docs.fig_value, drv_docs.fig_hidden, drv_docs.epf_payable, drv_docs.remuneration_pssc, drv_docs.remuneration_tcsc FROM (SELECT MAX(id) AS employee_paid_rate_id FROM `employee_paid_rates` WHERE `employee_payslip_id`=?) AS drv_batch CROSS JOIN (SELECT 'Loan' AS fig_name, 'LOAN' AS fig_group, 'LOAN' AS fig_group_title, (SUM(installment_value)*-1) AS fig_value, 0 AS fig_hidden, 0 AS epf_payable, 'LOAN' AS remuneration_pssc, NULL as remuneration_tcsc FROM employee_loan_installments WHERE payroll_profile_id=? AND emp_payslip_no=? AND installment_cancel=0 UNION ALL SELECT remunerations.remuneration_name AS fig_name, 'ADDITION' AS fig_group, 'ADDITION' AS fig_group_title, (employee_term_payments.payment_amount*remunerations.value_group) AS fig_value, 0 AS fig_hidden, remunerations.epf_payable, remunerations.payslip_spec_code AS remuneration_pssc, remunerations.taxcalc_spec_code AS remuneration_tcsc FROM (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS employee_term_payments INNER JOIN remunerations ON employee_term_payments.remuneration_id=remunerations.id) AS drv_docs) AS drv_term";
							$res=DB::insert($sql_cols.$sql_term, [$employee_payslip_id, $form_data['payment_period_id'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $employee_payslip_id, $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no']]);
						}else{
							$sql_recs="SELECT drv_batch.employee_paid_rate_id, drv_docs.fig_name, drv_docs.fig_group, drv_docs.fig_group_title, IFNULL(drv_docs.fig_value, 0) AS fig_base_ratio, IFNULL(drv_docs.fig_value, 0) AS fig_value, drv_docs.fig_hidden, drv_docs.epf_payable, drv_docs.remuneration_pssc, drv_docs.remuneration_id, drv_docs.reg_exemptions, drv_docs.remuneration_tcsc FROM (SELECT MAX(id) AS employee_paid_rate_id FROM `employee_paid_rates` WHERE `employee_payslip_id`=?) AS drv_batch CROSS JOIN (SELECT 0 as remuneration_id, 'Loan' AS fig_name, 'LOAN' AS fig_group, 'LOAN' AS fig_group_title, (SUM(installment_value)*-1) AS fig_value, 0 AS fig_hidden, 0 AS epf_payable, 'LOAN' AS remuneration_pssc, 0 as reg_exemptions, NULL as remuneration_tcsc FROM employee_loan_installments WHERE payroll_profile_id=? AND emp_payslip_no=? AND installment_cancel=0 UNION ALL SELECT remunerations.id as remuneration_id, remunerations.remuneration_name AS fig_name, 'ADDITION' AS fig_group, 'ADDITION' AS fig_group_title, (employee_term_payments.payment_amount*remunerations.value_group) AS fig_value, 0 AS fig_hidden, remunerations.epf_payable, remunerations.payslip_spec_code AS remuneration_pssc, COUNT(remuneration_exemptions.id) as reg_exemptions, ";
							$sql_recs.="remunerations.taxcalc_spec_code AS remuneration_tcsc FROM (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS employee_term_payments INNER JOIN remunerations ON employee_term_payments.remuneration_id=remunerations.id ";
							$sql_recs.="LEFT OUTER join remuneration_exemptions on remunerations.id=remuneration_exemptions.remuneration_id GROUP BY remunerations.id) AS drv_docs";
							$pay_info=DB::select($sql_recs, [$employee_payslip_id, $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no']]);
							
							foreach($pay_info as $figs){
								$figs_data = array(
												'employee_payslip_id'=>$employee_payslip_id,
												'employee_paid_rate_id'=>$figs->employee_paid_rate_id,
												'payment_period_id'=>$form_data['payment_period_id'],
												'payroll_profile_id'=>$form_data['payroll_profile_id'],
												'emp_payslip_no'=>$form_data['emp_payslip_no'],
												//'emp_company_id'=>$emp_company_id,
												//'emp_company_area_id'=>$emp_company_area_id,
												//'emp_branch_id'=>$emp_branch_id,
												//'emp_location_id'=>$emp_location_id,
												//'emp_department_id'=>$emp_department_id,
												'fig_name'=>$figs->fig_name,
												'fig_group'=>$figs->fig_group,
												'fig_group_title'=>$figs->fig_group_title,
												'fig_base_ratio'=>$figs->fig_base_ratio,
												'fig_value'=>$figs->fig_value,
												'fig_hidden'=>$figs->fig_hidden,
												'epf_payable'=>$figs->epf_payable,
												'remuneration_payslip_spec_code'=>$figs->remuneration_pssc,
												'remuneration_taxcalc_spec_code'=>$figs->remuneration_tcsc
											);
								/*
								$figs_data['updated_at']=date('Y-m-d H:i:s');
								*/
								$affectedFigs=DB::table('employee_salary_payments')
									->insert($figs_data);
									
								if($affectedFigs==1){
									//$res=true;
									
									if($figs->reg_exemptions>0){
										$payslip_figs_id=DB::getPdo()->lastInsertId();
										$sql_exemption="insert into employee_salary_payment_exemptions (employee_payslip_id, employee_salary_payment_id, exemption_fig_group_title, fig_calc_opt, exemption_fig_value, remuneration_id) select ? as employee_payslip_id, ? as employee_salary_payment_id, exemption_fig_group_title, fig_calc_opt, ? as exemption_fig_value, remuneration_id from remuneration_exemptions where remuneration_id=? and exemption_cancel=0"; // AND fig_calc_opt--get all fig-calc-opts to be filtered when required
										$res=DB::insert($sql_exemption, [$employee_payslip_id, $payslip_figs_id, $figs->fig_value, $figs->remuneration_id]);
									}
								}else{
									$res=false;
									
								}
								
								if(!$res){
									break;//throw-error
								}
							}
						}
						
						if(!$res){
							throw new \Exception('Unable to save loan installments and other salary additions');
						}
						/* -- loan-installments-and-term-payments */
						
						
						
						/* update nopay, ot by taking epf-payable amount into account */
						$sql_update="UPDATE employee_salary_payments INNER JOIN (SELECT drv_figs.id, drv_figs.fig_group, drv_figs.fig_group_title, ROUND(drv_figs.fig_value/drv_figs.fig_base_ratio, 2) AS units_total, round(((drv_calc.fig_total-IFNULL(drv_ext.fig_efv, 0))*drv_figs.fig_premium)/drv_figs.key_param, 2) AS new_base_ratio FROM (SELECT `id`, `fig_group`, `fig_group_title`, `fig_base_ratio`, `fig_value`, COALESCE(NULLIF((fig_group='OTHRS1')*1.5, 0), NULLIF((fig_group='OTHRS2')*2, 0), 1) AS fig_premium, COALESCE(NULLIF((fig_group_title='NOPAY')*?, 0), (fig_group_title='OTHRS')*?) AS key_param FROM `employee_salary_payments` WHERE `payroll_profile_id`=? AND `emp_payslip_no`=? AND `fig_group_title` IN ('NOPAY', 'OTHRS')) AS drv_figs ";
						$sql_update.="LEFT OUTER JOIN (select exemption_fig_group_title, sum(exemption_fig_value) as fig_efv from employee_salary_payment_exemptions where employee_payslip_id=? AND fig_calc_opt='SETNOPAYOT' GROUP BY exemption_fig_group_title) as drv_ext ON  drv_figs.fig_group_title=drv_ext.exemption_fig_group_title ";
						$sql_update.="CROSS JOIN (SELECT SUM(fig_value*IFNULL(NULLIF(ABS(ROUND(fig_base_ratio, 2))>ABS(fig_value), 0)*?, 1)) AS fig_total FROM `employee_salary_payments` WHERE `payroll_profile_id`=? AND `emp_payslip_no`=? AND (epf_payable=1 OR remuneration_payslip_spec_code IN ('ATTBONUS','INCNTV_EMP', 'INCNTV_DIR'))) AS drv_calc) AS drv_info ON employee_salary_payments.id=drv_info.id SET employee_salary_payments.fig_base_ratio=drv_info.new_base_ratio, employee_salary_payments.fig_value=(drv_info.new_base_ratio*drv_info.units_total), updated_at=NOW()";
						$payperiod_netdays=($payperiod_workdays-$payperiod_holidays)*-1;//26*-1
						$cnt_update=DB::update($sql_update, [$payperiod_netdays, $payperiod_workhrs, $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $employee_payslip_id, $full_pay_restoreval, $form_data['payroll_profile_id'], $form_data['emp_payslip_no']]);
						
						//($cnt_update!=3)
						if(($cnt_update%3)!=0){
							throw new \Exception('Unable to update no-pay and OT');
						}/**/
						/* -- update nopay, ot */
						
						
						/* 4. employee-welfare-charges */
						if($epf_registered==1){
							$sql_welfare="(SELECT drv_emp_earn.employee_paid_rate_id, employee_welfare_charges.display_name AS fig_name, employee_welfare_charges.group_name AS fig_group, employee_welfare_charges.group_name AS fig_group_title, (employee_welfare_charges.calc_percentage*employee_welfare_charges.pay_slip_value_addition) AS fig_base_ratio, ((employee_welfare_charges.calc_percentage/100)*drv_emp_earn.fig_value*employee_welfare_charges.pay_slip_value_addition) AS fig_value, employee_welfare_charges.fig_hidden, 0 AS epf_payable, employee_welfare_charges.group_name AS remuneration_pssc, NULL as remuneration_tcsc FROM employee_welfare_charges CROSS JOIN (SELECT employee_paid_rate_id, SUM(fig_value) AS fig_value FROM employee_salary_payments WHERE (epf_payable=1 OR remuneration_payslip_spec_code IN ('NOPAY')) AND payroll_profile_id=? AND emp_payslip_no=? GROUP BY employee_paid_rate_id) AS drv_emp_earn) AS drv_welfare";
							$res=DB::insert($sql_cols.$sql_welfare, [$employee_payslip_id, $form_data['payment_period_id'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no']]);
							
							if(!$res){
								throw new \Exception('Unable to save EPF and ETF details');
							}
						}
						/* -- employee-welfare-charges */
						
						
						/* 5. employee-income-tax-charges */
						/*
						$taxlist=DB::select("SELECT (COUNT(*)>0) as fig_taxable, drv_income.fig_value FROM (SELECT SUM(fig_value) AS fig_value FROM employee_salary_payments WHERE `employee_payslip_id`=? AND fig_value>0 AND fig_hidden=0) AS drv_income CROSS JOIN tax_provisions WHERE (drv_income.fig_value - tax_provisions.min_income)>=0", [$employee_payslip_id]);
						*/
						
						/*
						if($taxlist[0]->fig_taxable==1){
							$emp_income=$taxlist[0]->fig_value;
							
							
							$sql_taxrate="(SELECT drv_emp_income.employee_paid_rate_id, drv_tax.`tax_group_code` AS fig_name, drv_tax.`tax_group_code` AS fig_group, 'PAYE' AS fig_group_title, drv_tax.`tax_rate` AS fig_base_ratio, (drv_emp_income.fig_value * (drv_tax.tax_rate/100) * -1) AS fig_value, 0 AS fig_hidden, 0 AS epf_payable, 'PAYE' AS remuneration_pssc FROM (SELECT MAX(employee_paid_rate_id) AS employee_paid_rate_id, SUM(fig_value) AS fig_value FROM employee_salary_payments WHERE `employee_payslip_id`=? AND fig_value>0 AND fig_hidden=0) AS drv_emp_income CROSS JOIN (SELECT `id`, `tax_group_code`, `min_income`, `tax_rate`, (?-`min_income`) as min_income_diff FROM `tax_provisions` WHERE (?-`min_income`)>=0) as drv_tax INNER JOIN (SELECT tax_group_code, MIN(?-min_income) AS min_group_diff FROM `tax_provisions` WHERE (?-`min_income`)>=0 GROUP BY tax_group_code) AS drv_category ON (drv_tax.tax_group_code=drv_category.tax_group_code AND drv_tax.min_income_diff=drv_category.min_group_diff)) AS drv_taxrate";
							
							$res=DB::insert($sql_cols.$sql_taxrate, [$employee_payslip_id, $form_data['payment_period_id'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $employee_payslip_id, $emp_income, $emp_income, $emp_income, $emp_income]);
							
							
							
							if(!$res){
								throw new \Exception('Unable to save tax details');
							}
						}
						*/
						$taxlist=DB::select("SELECT tax_provisions.id as prov_k, ifnull(drv_income.fig_value, 0) as fig_value, tax_provisions.min_income, tax_provisions.tax_rate FROM (SELECT sum(ifnull(nullif((1-remuneration_taxations.strict_epf_payables), 0), drv_tlfigs.epf_payable)*fig_value) AS fig_value FROM (select remuneration_taxcalc_spec_code as fig_tcsc, fig_value, epf_payable from employee_salary_payments WHERE `employee_payslip_id`=?) as drv_tlfigs inner join remuneration_taxations on drv_tlfigs.fig_tcsc=remuneration_taxations.taxcalc_spec_code where remuneration_taxations.fig_calc_opt='FIGPAYE' and remuneration_taxations.optspec_cancel=0) AS drv_income CROSS JOIN tax_provisions WHERE (drv_income.fig_value - tax_provisions.min_income)>0 order by tax_provisions.id desc", [$employee_payslip_id]);
						if(count($taxlist)>=1){
							$taxgrp_increment = 25;
							$mod_taxlistfig = ($taxlist[0]->fig_value%$taxgrp_increment);
							$rem_taxlistfig = ($mod_taxlistfig>0)?($taxgrp_increment-$mod_taxlistfig):0;
							
							
							$grp_uboundary = $taxlist[0]->fig_value; // ($taxlist[0]->fig_value+$rem_taxlistfig); // 
							$tax_uboundary = $taxlist[0]->tax_rate;
							$tax_totamount = 0;
							
							foreach($taxlist as $taxgrp){
								$grp_lboundary = $taxgrp->min_income;
								$tax_grpfigval = $grp_uboundary-$grp_lboundary;
								$tax_grprate = $taxgrp->tax_rate;
								$tax_totamount += ($tax_grpfigval)*($tax_grprate/100);
								$grp_uboundary = $grp_lboundary;
							}
							
							//sumith-paye-2023-02-10
							/*
							if($form_data['payroll_profile_id']=='58'){
								$tax_totamount=3842;
								$tax_uboundary=12;
							}
							*/
							
							$emp_tax_str=number_format((float)round($tax_totamount, 0), 2, '.', '');
							
							
							$sql_taxrate="(SELECT drv_emp_income.employee_paid_rate_id, 'PAYE' AS fig_name, 'PAYE' AS fig_group, 'PAYE' AS fig_group_title, (? * 1) AS fig_base_ratio, (? * -1) AS fig_value, 0 AS fig_hidden, 0 AS epf_payable, 'PAYE' AS remuneration_pssc, NULL as remuneration_tcsc FROM (SELECT MAX(employee_paid_rate_id) AS employee_paid_rate_id, 0 AS fig_value FROM employee_salary_payments WHERE `employee_payslip_id`=? AND fig_value>0 AND fig_hidden=0) AS drv_emp_income) AS drv_taxrate";
							
							$res=DB::insert($sql_cols.$sql_taxrate, [$employee_payslip_id, $form_data['payment_period_id'], $form_data['payroll_profile_id'], $form_data['emp_payslip_no'], $tax_uboundary, $emp_tax_str, $employee_payslip_id]);
							
							
							$payslip_figpaye_id=DB::getPdo()->lastInsertId();//foreign-key-param
							
							$sql_taxfigs="insert into employee_salary_taxations (employee_payslip_id, employee_salary_payment_id, employee_salary_taxfig_id, taxcalc_spec_code, fig_calc_opt, fig_value, fig_epf_payable) select drv_payfigs.employee_payslip_id, drv_payfigs.employee_salary_payment_id, ? as employee_salary_taxfig_id, drv_payfigs.fig_tcsc as taxcalc_spec_code, 'FIGPAYE' as fig_calc_opt, drv_payfigs.fig_value, drv_payfigs.epf_payable as fig_epf_payable from (select id as employee_salary_payment_id, employee_payslip_id, remuneration_taxcalc_spec_code as fig_tcsc, fig_value, epf_payable from employee_salary_payments where `employee_payslip_id`=?) as drv_payfigs inner join remuneration_taxations on drv_payfigs.fig_tcsc=remuneration_taxations.taxcalc_spec_code where remuneration_taxations.fig_calc_opt='FIGPAYE' and remuneration_taxations.optspec_cancel=0";
							
							$res=DB::insert($sql_taxfigs, [$payslip_figpaye_id, $employee_payslip_id]);
							
							
							if(!$res){
								throw new \Exception('Unable to save tax details');
							}
						}
						/* -- employee-income-tax-charges */
						
						/* 6. net salary based payroll particulars */
						$netpayfigs = "select min(employee_paid_rate_id) as employee_paid_rate_id, sum(fig_value) as emp_netsal from employee_salary_payments where employee_payslip_id=? and fig_hidden=0";
						$netpaydata = DB::select($netpayfigs, [$employee_payslip_id]);
						$prefnetsal = $netpaydata[0]->emp_netsal;
						$proparticulars_netsal = DB::select("select id, particular_name, affected_salary_amount_group, particular_amount, remuneration_payslip_spec_code, particular_seqno from payroll_process_charges where particular_group='NET_SAL_CALC' and particular_cancel=0 order by particular_seqno");
						
						foreach($proparticulars_netsal as $figs){
							$prefnetsal+=$figs->particular_amount;
							if($prefnetsal>$figs->affected_salary_amount_group){
								$figs_data = array(
											'employee_payslip_id'=>$employee_payslip_id,
											'employee_paid_rate_id'=>$netpaydata[0]->employee_paid_rate_id,
											'payment_period_id'=>$form_data['payment_period_id'],
											'payroll_profile_id'=>$form_data['payroll_profile_id'],
											'emp_payslip_no'=>$form_data['emp_payslip_no'],
											//'emp_company_id'=>$emp_company_id,
											//'emp_company_area_id'=>$emp_company_area_id,
											//'emp_branch_id'=>$emp_branch_id,
											//'emp_location_id'=>$emp_location_id,
											//'emp_department_id'=>$emp_department_id,
											'fig_name'=>$figs->particular_name,
											'fig_group'=>'ADDITION',//$figs->fig_group,
											'fig_group_title'=>'ADDITION',//$figs->fig_group_title,
											'fig_base_ratio'=>$figs->particular_amount,
											'fig_value'=>$figs->particular_amount,
											'fig_hidden'=>0,
											'epf_payable'=>0,
											'remuneration_payslip_spec_code'=>$figs->remuneration_payslip_spec_code,
											'remuneration_taxcalc_spec_code'=>NULL//$figs->remuneration_tcsc
										);
								/*
								$figs_data['updated_at']=date('Y-m-d H:i:s');
								*/
								$affectedFigs=DB::table('employee_salary_payments')
									->insert($figs_data);
								
								if(!($affectedFigs==1)){
									$res = false;
								}
							}
						}
						
						if(!$res){
							throw new \Exception('Unable to save process charges');
						}
						/* -- net salary based payroll particulars */
					}
					
					$result = array('result'=>$feedback_msg, 'employee_payslip_id'=>$employee_payslip_id);
					
					return response()->json($result);
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}
	}
	
	public function clearPaidInfo(Request $request){
		try{
			return DB::transaction(function() use ($request){
				if($request->ajax()){
					$employee_payslip_id=$request->id;
					$affectedRows=0;
					$feedback_msg='';
					$rev_payslip_no=($request->emp_payslip_no-1);
					
					/* 1. clear pay sequence data */
					$payslip_data = array(
										'emp_payslip_no' => $rev_payslip_no,
										'payslip_cancel' => '1',
										'updated_by' => $request->user()->id,
										'updated_at' => date('Y-m-d H:i:s')
										);
					
					$affectedRows=DB::table('employee_payslips')
							->where(['id'=>$employee_payslip_id,
									'payslip_held'=>0, 'payslip_approved'=>0])
							->update($payslip_data);
							
					if($affectedRows==1){
						$feedback_msg='success';
						
					}else{
						throw new \Exception('Payslip delete error');
					}
					/* #. clear pay sequence data */
					
					/* 2. clear paid summary data */
					$paidsum_data = array(
										'emp_payslip_no' => $rev_payslip_no,
										'work_days' => '0',
										'leave_days' => '0',
										'nopay_days' => '0',
										'normal_rate_otwork_hrs' => '0',
										'double_rate_otwork_hrs' => '0',
										'work_days_exclusions' => '0',
										'updated_by' => $request->user()->id,
										'updated_at' => date('Y-m-d H:i:s')
										);
					
					$affectedRows=DB::table('employee_paid_rates')
							->where(['employee_payslip_id'=>$employee_payslip_id])
							->update($paidsum_data);
							
					if($affectedRows>=1){
						$feedback_msg='success';
						
					}else{
						throw new \Exception('Payslip delete error');
					}
					/* #. clear paid summary data */
					
					/* 3. clear payslip figs data */
					$payfigs_data = array(
										'emp_payslip_no' => $rev_payslip_no,
										'fig_value' => '0',
										'updated_at' => date('Y-m-d H:i:s')
										);
					
					$affectedRows=DB::table('employee_salary_payments')
							->where(['employee_payslip_id'=>$employee_payslip_id])
							->update($payfigs_data);
							
					if($affectedRows>=1){
						$feedback_msg='success';
						
					}else{
						throw new \Exception('Payslip delete error');
					}
					/* #. clear payslip figs data */
					
					$result = array('result'=>$feedback_msg, 'employee_payslip_id'=>'');
					
					return response()->json($result);
					
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}
	}
	
	public function holdPayment(Request $request){
		try{
			return DB::transaction(function() use ($request) {
				if($request->ajax()){
					try{
						$employeePayslip=EmployeePayslip::findOrFail($request->input('id'));
						$held_status = (1-$request->payslip_held);
						
						if($employeePayslip->payslip_approved==1){
							throw new \Exception('Payslip already approved');
						}else if($employeePayslip->payslip_cancel==1){
							throw new \Exception('Payslip is cancelled');
						}else if($held_status==$employeePayslip->payslip_held){
							throw new \Exception('Unable to apply changes. Please reload payslip list');
						}else{
							$flag=true;
							$employeePayslip->payslip_held=$held_status;
							
							$employeePayslip->payslip_held_cnt=1;//set-held-status
							
							$employeePayslip->updated_by=$request->user()->id;
							$payslipUpdate=$employeePayslip->save();
							
							if(!$payslipUpdate){
								throw new \Exception('Unable to update payslip');
							}
							
							$salaryRemark = EmployeeSalaryRemark::where(['employee_payslip_id'=>$request->input('id')])
																->first();
							
							if(!empty($salaryRemark)){
								$salaryRemark->payslip_held=$held_status;
								$salaryRemark->updated_by=$request->user()->id;
								$remarkUpdate=$salaryRemark->save();
								
								if(!$remarkUpdate){
									throw new \Exception('Unable to update the remarks');
								}
							}
							
							$feedback_msg = 'Successfully updated';
						}
					}catch (ModelNotFoundException $e) {
						// Data not found. Here, you should make sure that the absence of $data won't break anything
						throw new \Exception('Payslip details not available');
					}
					
					return response()->json(['result'=>'success', 'resmsg'=>$feedback_msg]);
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'resmsg'=>$e->getMessage()));
		}
	}
	
	public function approvePayment(Request $request){
		if($request->ajax()){
			$restype = 'error';
			$feedback_msg = '';
			
			try{
				$employeePayslip=EmployeePayslip::findOrFail($request->input('id'));
				$approve_status = (1-$request->payslip_approve);
				
				if($employeePayslip->payslip_held==1){
					$feedback_msg='Payment is blocked';
				}else if($employeePayslip->payslip_cancel==1){
					$feedback_msg='Payslip is cancelled';
				}else if($approve_status==$employeePayslip->payslip_approved){
					$feedback_msg='Unable to apply changes. Please reload payslip list';
				}else{
					$employeePayslip->payslip_approved=$approve_status;
					$employeePayslip->updated_by=$request->user()->id;
					$payslipUpdate=$employeePayslip->save();
					
					if(!$payslipUpdate){
						$feedback_msg='Unable to update payslip';
					}else{
						$restype = 'success';
						$feedback_msg = 'Successfully updated';
					}
				}
			}catch (ModelNotFoundException $e) {
				// Data not found. Here, you should make sure that the absence of $data won't break anything
				$feedback_msg = 'Payslip details not available';
			}
			
			return response()->json(['result'=>$restype, 'resmsg'=>$feedback_msg]);
		}
	}
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function preview()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('Payroll.payslipProcess.payslipPreview_list',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	
	public function reportEpfEtf()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('Payroll.payslipProcess.EpfEtf_list',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	public function reportSignatureSheet()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('Payroll.payslipProcess.SignatureSheet_list',compact('branch', 'payroll_process_type', 'payment_period'));
    }
	public function reportPayRegister()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('Payroll.payslipProcess.PayRegister_list',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	public function reportSalarySheet()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('Payroll.payslipProcess.SalarySheet_list',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	
	/*
	//route-updated
	//checkPayslipList > checkPayslipListByDept
	*/
	public function checkPayslipList(Request $request){
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				//'department_filter_id' => 'required',
				'period_filter_id' => 'required'
			);
	
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$emp_department_col = '2';
			$emp_department_val = '2';
			
			if(!empty($request->department_filter_id)){
				$emp_department_col = "employees.emp_department";
				$emp_department_val = $request->department_filter_id;
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::find($request->period_filter_id);
			
			$payment_period_id=$paymentPeriod->id;//1;
			$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
			$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
			
			$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group_title, ABS(drv_info.fig_value) AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`";
			
			$sqlslip.=" ";
			$sqlslip.="UNION ALL SELECT `id` AS fig_id, `employee_payslip_id`, fig_group as `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? and fig_group in ('OTHRS1','OTHRS2') GROUP BY `employee_payslip_id`, `fig_group`";
			
			$sqlslip.=") AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
			/*
			
			*/
			$employee = DB::select($sqlslip, [$payment_period_id, 
											  $request->location_filter_id, $emp_department_val, 
											  $payment_period_id, $payment_period_id]
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
					$employee_list[]=array('id'=>$r->emp_payslip_id, 'emp_first_name'=>$r->emp_first_name, 'location'=>$r->location, 'BASIC'=>0, 'NOPAY'=>0, 'OTHRS'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'FACILITY'=>0, 'LOAN'=>0, 'ADDITION'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'PAYE'=>0, 'payslip_cancel'=>1, 'payslip_held'=>$r->payslip_held, 'payslip_approved'=>$r->payslip_approved);
					
					
				}
				
				$employee_list[$cnt-1][$r->fig_group_title]=number_format((float)$r->fig_value, 4, '.', '');
			}
			
			return response()->json(['employee_detail'=>$employee_list, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	//checkPayslipList > checkEmpEpfEtf
	public function checkPayPeriodEpfEtf(Request $request){
		if($request->ajax()){
			$rules = array(
				'payroll_process_type_id' => 'required',
				'period_filter_id' => 'required'
			);
			
			if($request->location_filter_id=='-1'){
				return response()->json(['errors' => array('Select a Branch')]);
			}
			
			$emp_location_col = '1';
			$emp_department_col = '2';
			$emp_location_val = '1';
			$emp_department_val = '2';
			
			if(!empty($request->location_filter_id)){
				$emp_location_col = "employees.emp_company";//"employees.emp_location";
				$emp_location_val = $request->location_filter_id;
			}
			if((!empty($request->department_filter_id))&&($request->department_filter_id!='-1')){
				$emp_department_col = "employees.emp_department";
				$emp_department_val = $request->department_filter_id;
			}
			
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
			
			$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.emp_nicno, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_name_with_initial AS emp_first_name, employees.emp_national_id as emp_nicno, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND ".$emp_location_col."=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
			/*
			
			*/
			$employee = DB::select($sqlslip, [$payment_period_id, 
											  $emp_location_val, $emp_department_val, 
											  $payment_period_id]
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
					$employee_list[]=array('id'=>$r->emp_payslip_id, 'emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'emp_nicno'=>$r->emp_nicno, 'location'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'payslip_cancel'=>1, 'payslip_held'=>$r->payslip_held, 'payslip_approved'=>$r->payslip_approved, 'OTHER_REM'=>0);
					
					
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
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	
	public function checkPayRegister(Request $request){
		if($request->ajax()){
			/*
			$rules = array(
				'payroll_process_type_id' => 'required',
				'location_filter_id' => 'required',
				'department_filter_id' => 'required',
				'period_filter_id' => 'required'
			);
			*/
			$rules = array(
				'payroll_process_type_id' => 'required',
				'period_filter_id' => 'required'
			);
			
			if($request->location_filter_id=='-1'){
				return response()->json(['errors' => array('Select a Branch')]);
			}
			
			$emp_location_col = '1';
			$emp_department_col = '2';
			$emp_location_val = '1';
			$emp_department_val = '2';
			
			if(!empty($request->location_filter_id)){
				$emp_location_col = "employees.emp_company";//"employees.emp_location";
				$emp_location_val = $request->location_filter_id;
			}
			if(!empty($request->department_filter_id)){
				$emp_department_col = "employees.emp_department";
				$emp_department_val = $request->department_filter_id;
			}
			
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
			
			$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ";
			$sqlslip.="ORDER BY ifnull(nullif(drv_emp.emp_etfno, 0), 99999), drv_emp.emp_epfno, drv_info.fig_id";//ORDER BY drv_info.fig_id
			/*
			
			*/
			$employee = DB::select($sqlslip, [$payment_period_id, 
											  $request->location_filter_id, $emp_department_val, 
											  $payment_period_id]
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
			
			
			//2023-11-07
			//keys-selected-to-calc-paye-updated-from-remuneration-taxation
			$conf_tl = RemunerationTaxation::where(['fig_calc_opt'=>'FIGPAYE', 'optspec_cancel'=>0])
							->pluck('taxcalc_spec_code')->toArray(); //var_dump($conf_tl);
			//return response()->json($conf_tl);
			//-2023-11-07
			
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
					$employee_list[]=array('id'=>$r->emp_payslip_id, 'reg_indexno'=>$cnt, 'emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'payslip_cancel'=>1, 'payslip_held'=>$r->payslip_held, 'payslip_approved'=>$r->payslip_approved, 'OTHER_REM'=>0);
					
					
				}
				
				$fig_key = isset($employee_list[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
				
				if(isset($employee_list[$cnt-1][$fig_key])){
					$fig_group_val=$employee_list[$cnt-1][$fig_key];
					$employee_list[$cnt-1][$fig_key]=number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
					
					if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
						$net_payslip_fig_value+=$r->fig_value;
						$employee_list[$cnt-1]['NETSAL']=number_format((float)$net_payslip_fig_value, 2, '.', '');
						
						/*
						if(($r->epf_payable==1)||($fig_key=='NOPAY')){
							$emp_fig_tottax += $r->fig_value;
							$employee_list[$cnt-1]['tot_fortax']=number_format((float)$emp_fig_tottax, 2, '.', '');
						}
						*/
						if(in_array($r->remuneration_tcsc, $conf_tl)){
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
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	public function downloadPayRegister(Request $request){
        $companyRegInfo = Company::find($request->rpt_location_id);
		$company_name = $companyRegInfo->name;
		$company_addr = $companyRegInfo->address;
		$land_tp = $companyRegInfo->land;
		
        $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
		$emp_location_col = '1';
		$emp_department_col = '2';
		$emp_location_val = '1';
		$emp_department_val = '2';
		
		if(!empty($request->rpt_location_id)){
			$emp_location_col = "employees.emp_company";//"employees.emp_location";
			$emp_location_val = $request->rpt_location_id;
		}
		if(!empty($request->rpt_dept_id)){
			$emp_department_col = "employees.emp_department";
			$emp_department_val = $request->rpt_dept_id;
		}
		
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_rates.ot1dura, drv_rates.ot2dura, drv_rates.wk_days, drv_rates.abhrs, drv_rates.nopay_days, IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved, payroll_profiles.payroll_act_id FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp ";
		$sqlslip.="INNER JOIN job_categories ON drv_emp.payroll_act_id=job_categories.id ";
		$sqlslip.="INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) as ot1dura, sum(double_rate_otwork_hrs) as ot2dura, SUM(work_days) as wk_days, 0 as abhrs, SUM(nopay_days) AS nopay_days from employee_paid_rates where date_format(concat(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (date_format(?, '%Y-%m'), date_format(?, '%Y-%m')) group by employee_payslip_id) AS drv_rates ON drv_emp.emp_payslip_id=drv_rates.employee_payslip_id ";
		$sqlslip.="INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY ifnull(nullif(drv_emp.emp_etfno, 0), 99999), drv_emp.emp_epfno, drv_info.fig_id";
		
		$emp_data = DB::select($sqlslip, [$payment_period_id, 
										  $request->rpt_location_id, $emp_department_val,
										  $payment_period_fr, $payment_period_to,
										  $payment_period_id]
							   );
		$sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
		/*
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Before Nopay', 'Arrears', 'Total for Tax', 'Attendance', 'Transport', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'EPF-8', 'Salary Advance', 'Telephone', 'IOU', 'Funeral Fund', 'Other Deductions', 'PAYE', 'Loans', 'Total Deductions', 'Balance Pay');
		*/
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
		/*
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
		*/
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>0, 'OTHRS2'=>0, 'OT2DURA'=>0, 'WK_ACT_DAYS'=>0, 'WK_MAX_DAYS'=>0, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
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
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>$r->nopay_days, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>$r->ot1dura, 'OTHRS2'=>0, 'OT2DURA'=>$r->ot2dura, 'WK_ACT_DAYS'=>$r->wk_days, 'WK_MAX_DAYS'=>$r->emp_payroll_workdays, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
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
		/**/
		$emp_array[] = $sum_array;
		
		if($request->print_record=='1'){
			$excel_rows[] = array('EPF NO', 'Employee Name', 'Working Day', 'Salary Per Day', 'OT1 Hrs.', 'OT2 Hrs.', 'OT1 Rate', 'OT2 Rate', 'Basic+BRA', 'No Pay Day', 'No Pay Amount', 'TTL For E.P.F.', 'OT1 Amount', 'OT2 Amount', 'Attendance Allw.', 'Transport Allowance', 'Perf. Based Insentive', 'Other Allowance', 'Other Additions', 'Gross Salary', 'EPF-8', 'Late Deduction', 'Salary Advance', 'Payee Tax', 'Bank Charges', 'Other Deductions', 'Loan', 'Total Deduction', 'Net Salary', 'EPF-12', 'ETF-3', 'Signature');
			$rpt_row_cnt = 0;//skip-title-row
			foreach($emp_array as $excel_data){
				if($rpt_row_cnt>0){
					$basic_pay=$excel_data['BASIC']+$excel_data['BRA_I']+$excel_data['add_bra2'];
					$emp_daysal=($excel_data['WK_MAX_DAYS']>0)?($basic_pay/$excel_data['WK_MAX_DAYS']):'';//
					$tot_forepf = $excel_data['tot_bnp']-$excel_data['NOPAY'];//$excel_data['tot_fortax'];//
					$normal_ot_rate = !empty($excel_data['OT1DURA'])?($excel_data['OTHRS1']/$excel_data['OT1DURA']):0;
					$double_ot_rate = !empty($excel_data['OT2DURA'])?($excel_data['OTHRS2']/$excel_data['OT2DURA']):0;
					$disp_add_other = $excel_data['add_other']+$excel_data['ATTBONUS_W'];
					
					$excel_rows[] = array($excel_data['emp_epfno'], $excel_data['emp_first_name'], 
										$excel_data['WK_ACT_DAYS'], $emp_daysal, 
										$excel_data['OT1DURA'], $excel_data['OT2DURA'], 
										$normal_ot_rate, $double_ot_rate,
										$basic_pay, $excel_data['nopay_days'], $excel_data['NOPAY'], 
										$tot_forepf, $excel_data['OTHRS1'], 
										$excel_data['OTHRS2'], $excel_data['ATTBONUS'], 
										$excel_data['add_transport'], $excel_data['INCNTV_EMP'], $excel_data['INCNTV_DIR'],
										$disp_add_other, 
										$excel_data['tot_earn'], 
										$excel_data['EPF8'], 
										$excel_data['ded_IOU'], 
										$excel_data['sal_adv'], //
										$excel_data['PAYE'], $excel_data['ded_fund_1'], $excel_data['ded_other'], $excel_data['LOAN'], 
										$excel_data['tot_ded'], 
										$excel_data['NETSAL'], 
										$excel_data['EPF12'], $excel_data['ETF3'],
										'' //'...................' // signature
									);
					
				}
				
				$rpt_row_cnt++;
				
			}
			
			Excel::create('PayRegister '.$request->rpt_info, function($excel) use ($excel_rows){
				$excel->setTitle('PayRegister');
				$excel->sheet('SalarySheet', function($sheet) use ($excel_rows){
					$sheet->fromArray($excel_rows, null, 'A1', false, false);
				});
			})->download('xlsx');
		}else if($request->print_record=='2'){
			//$emp_array[] = $sum_array;
			$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
			$customPaper = array(0,0,567.00,1283.80);
			
			ini_set("memory_limit", "999M");
			ini_set("max_execution_time", "999");
			
			$pdf = PDF::loadView('Payroll.payslipProcess.PayRegister_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name', 'company_name', 'company_addr', 'land_tp'))
				->setPaper('legal', 'landscape');//->setPaper($customPaper, 'landscape');
			
			return $pdf->download('pay-register.pdf');
			/*
			var_dump($emp_array);
			*/
		}
    }
	
	public function downloadEpfEtf(Request $request){
        
        $emp_location_col = '1';
		$emp_department_col = '2';
		$emp_location_val = '1';
		$emp_department_val = '2';
		
		if(!empty($request->rpt_location_id)){
			$emp_location_col = "employees.emp_company";//"employees.emp_location";
			$emp_location_val = $request->rpt_location_id;
		}
		if((!empty($request->rpt_dept_id))&&($request->rpt_dept_id!='-1')){
			$emp_department_col = "employees.emp_department";
			$emp_department_val = $request->rpt_dept_id;
		}
		
		$paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
		
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
		$rpt_format = $request->rpt_layout_no;
		
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.emp_nicno, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, employees.emp_national_id as emp_nicno, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND ".$emp_location_col."=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_emp.emp_epfno, drv_info.fig_id";
		/*
		
		*/
		$employee = DB::select($sqlslip, [$payment_period_id, 
										  $emp_location_val, $emp_department_val, 
										  $payment_period_id]
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
		
		$rem_tot_bnp = 0;
		$rem_tot_fortax = 0;
		$rem_tot_earn = 0;
		$rem_tot_ded = 0;
		$rem_net_sal = 0;
		$rem_ded_other = 0;
		
		$sect_name = ($emp_department_val=='2')?'All Sections':$request->rpt_dept_name;
		$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
		
		$sum_array = array('id'=>'', 'emp_epfno'=>'', 'emp_first_name'=>'', 'emp_nicno'=>'', 'location'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
		
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
				$employee_list[]=array('id'=>$r->emp_payslip_id, 'emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'emp_nicno'=>$r->emp_nicno, 'location'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
				
				$rem_tot_bnp = 0;
				$rem_tot_fortax = 0;
				$rem_tot_earn = 0;
				$rem_tot_ded = 0;
				$rem_net_sal = 0;
				$rem_ded_other = 0;
				
				
			}
			
			$fig_key = isset($employee_list[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
			
			if(isset($employee_list[$cnt-1][$fig_key])){
				$fig_group_val=$employee_list[$cnt-1][$fig_key];
				
				if($fig_key!='OTHER_REM'){
					$employee_list[$cnt-1][$fig_key]=(abs($r->fig_value)+$fig_group_val);//number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
					$sum_array[$fig_key]+=abs($r->fig_value);
				}
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$employee_list[$cnt-1]['NETSAL']=$net_payslip_fig_value;//number_format((float)$net_payslip_fig_value, 2, '.', '');
					
					$reg_net_sal=$sum_array['NETSAL']-$rem_net_sal;
					$sum_array['NETSAL']=($reg_net_sal+$net_payslip_fig_value);
					$rem_net_sal = $net_payslip_fig_value;
					
					if(($r->epf_payable==1)||($fig_key=='NOPAY')){
						$emp_fig_tottax += $r->fig_value;
						$employee_list[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
						$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
						$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
						$rem_tot_fortax = $emp_fig_tottax;
					}
					
					$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
					//if(($r->fig_value>=0)||($fig_key!='EPF8'))
					if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
						$emp_fig_totearn += $r->fig_value;
						$employee_list[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
						$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
						$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
						$rem_tot_earn = $emp_fig_totearn;
					}
					
					if($r->fig_value>=0){
						/*
						$emp_fig_totearn += $r->fig_value;
						$employee_list[$cnt-1]['tot_earn']=number_format((float)$emp_fig_totearn, 2, '.', '');
						*/
						$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
						$employee_list[$cnt-1]['add_other']=$emp_fig_otherearn;//number_format((float)$emp_fig_otherearn, 2, '.', '');
					}else{
						if($fig_key!='NOPAY'){
							$emp_fig_totlost += $r->fig_value;
							$employee_list[$cnt-1]['tot_ded']=abs($emp_fig_totlost);//number_format((float)abs($emp_fig_totlost), 2, '.', '');
							
							$reg_tot_ded=$sum_array['tot_ded']-$rem_tot_ded;
							$sum_array['tot_ded']=($reg_tot_ded+abs($emp_fig_totlost));
							$rem_tot_ded = abs($emp_fig_totlost);
						}
						$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
						$employee_list[$cnt-1]['ded_other']=$emp_fig_otherlost;//number_format((float)$emp_fig_otherlost, 2, '.', '');
						
						$reg_ded_other=$sum_array['ded_other']-$rem_ded_other;
						$sum_array['ded_other']=($reg_ded_other+$emp_fig_otherlost);
						$rem_ded_other=$emp_fig_otherlost;
					}
				}
				
				if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
					//if($employee_list[$cnt-1]['tot_bnp']==0){
						$emp_tot_bnp=($employee_list[$cnt-1]['BASIC']+$employee_list[$cnt-1]['BRA_I']+$employee_list[$cnt-1]['add_bra2']);
						$employee_list[$cnt-1]['tot_bnp']=$emp_tot_bnp;//number_format((float)$emp_tot_bnp, 2, '.', '');
						
						$reg_tot_bnp=$sum_array['tot_bnp']-$rem_tot_bnp;
						$sum_array['tot_bnp']=($reg_tot_bnp+$emp_tot_bnp);
						$rem_tot_bnp = $emp_tot_bnp;
						
					//}
				}
				
			}
		}
		/**/
		$employee_list[] = $sum_array;
		
		$rpt_collist = array('Name', 'Office', 'EPF8', 'EPF12', 'ETF3');
		$rpt_paydata = array('emp_first_name' => '', 'location' => '', 'EPF8' => 0, 'EPF12' => 0, 'ETF3' => 0);
		$pdf_layout = 'Payroll.payslipProcess.epfetf_pdf';
		
		if($rpt_format==2){
			$rpt_collist = array('EPF No.', 'Name', 'NIC', 'Tax Total', 'EPF12', 'EPF8', 'Total');
			$rpt_paydata = array('emp_epfno'=>'', 'emp_first_name' => '', 'emp_nicno'=>'', 'tot_fortax' => 0, 
								 'EPF12' => 0, 'EPF8' => 0, 'Total_val'=>0);
			$pdf_layout = 'Payroll.payslipProcess.epfonly_pdf';
		}else if($rpt_format==3){
			$rpt_collist = array('EPF No.', 'Name', 'NIC', 'Tax Total', 'ETF3');
			$rpt_paydata = array('emp_epfno' => '', 'emp_first_name' => '', 'emp_nicno'=>'', 
								 'tot_fortax' => 0, 'ETF3' => 0);
			$pdf_layout = 'Payroll.payslipProcess.etfonly_pdf';
		}
		
		$emp_array[] = $rpt_collist;
		
		
		$cnt = 1;
		$act_payslip_id = '';
		
		foreach($employee_list as $r){
			$rpt_paydata_total_val=(isset($rpt_paydata['Total_val']))?array('Total_val'=>((float)$r['EPF12']+(float)$r['EPF8'])):array();
			
			$emp_array[] = array_merge(array_intersect_key($r, $rpt_paydata), $rpt_paydata_total_val);
			
		}
		
		/*
		Excel::create('EPF_ETF '.$request->rpt_info, function($excel) use ($emp_array){
			$excel->setTitle('EPF_ETF');
			$excel->sheet('EPF_ETF', function($sheet) use ($emp_array){
				$sheet->fromArray($emp_array, null, 'A1', false, false);
			});
		})->download('xlsx');
		*/
		if($request->print_record=='1'){
			Excel::create('EPF_ETF '.$request->rpt_info, function($excel) use ($emp_array){
				$excel->setTitle('EPF_ETF');
				$excel->sheet('EPF_ETF', function($sheet) use ($emp_array){
					$sheet->fromArray($emp_array, null, 'A1', false, false);
				});
			})->download('xlsx');
		}else if($request->print_record=='2'){
			//$emp_array[] = $sum_array;
			$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
			$customPaper = array(0,0,567.00,1283.80);
			
			ini_set("memory_limit", "999M");
			ini_set("max_execution_time", "999");
			
			$pdf = PDF::loadView($pdf_layout, compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'))
				->setPaper('A4', 'portrait');//->setPaper($customPaper, 'landscape');
			
			return $pdf->download('EPF_ETF.pdf');
			/*
			var_dump($emp_array);
			*/
		}
    }
	public function downloadSignatureSheet(Request $request){
        
        $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, companies.name AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
			
		$emp_data = DB::select($sqlslip, [$payment_period_id, $request->rpt_location_id, $payment_period_id]);
		
		$emp_array[] = array('Name', 'Office', 'Basic Salary', 'Nopay', 'OT', 'Facility', 'Loans', 'Additions', 'EPF8','EPF12','ETF3', 'PAYE Tax', 'Net Earn');
		
		
		$cnt = 1;
		$act_payslip_id = '';
		$net_payslip_fig_value = 0;
		
		foreach($emp_data as $r){
			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
			}
			if(!isset($emp_array[$cnt-1])){
				$emp_array[] = array('Name' => $r->emp_first_name, 'Office' => $r->location, 'BASIC'=>0, 'NOPAY'=>0, 'OTHRS'=>0, 'FACILITY'=>0, 'LOAN'=>0, 'ADDITION'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'PAYE'=>0, 'NETSAL'=>0);
				
			}
			if(isset($emp_array[$cnt-1][$r->fig_group_title])){
				$emp_array[$cnt-1][$r->fig_group_title]=number_format((float)abs($r->fig_value), 2, '.', '');
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$emp_array[$cnt-1]['NETSAL']=number_format((float)$net_payslip_fig_value, 2, '.', '');
				}
			}
		}
		
		Excel::create('SignatureSheet '.$request->rpt_info, function($excel) use ($emp_array){
			$excel->setTitle('Signature List');
			$excel->sheet('SalarySheet', function($sheet) use ($emp_array){
				$sheet->fromArray($emp_array, null, 'A1', false, false);
			});
		})->download('xlsx');
    }
	// public function downloadSalarySheet(Request $request){
    //     $companyRegInfo = Company::find($request->rpt_location_id);
	// 	$company_name = $companyRegInfo->name;
	// 	$company_addr = $companyRegInfo->address;
		
	// 	$emp_department_col = '2';
	// 	$emp_department_val = '2';
		
	// 	if(!empty($request->rpt_dept_id)){
	// 		$emp_department_col = "employees.emp_department";
	// 		$emp_department_val = $request->rpt_dept_id;
	// 	}
		
    //     $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
	// 	$payment_period_id=$paymentPeriod->id;//1;
	// 	$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
	// 	$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
	// 	/*
	// 	$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payment_period_id=? AND employees.emp_location=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
	// 	*/
	// 	$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_national_id, drv_emp.emp_first_name, drv_emp.emp_designation, drv_emp.location, IFNULL(employee_banks.bank_ac_no, '') AS bank_ac_no, IFNULL(employee_banks.bank_name, '') as bank_name, IFNULL(employee_banks.bank_branch_name, '') as bank_branch_name, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_workdays, drv_catinfo.emp_nopaydays FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, ifnull(employees.emp_national_id, '') AS emp_national_id, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, job_titles.title AS emp_designation, companies.name AS location, companies.address AS company_disp_address FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (select employee_payslip_id, employee_bank_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, sum(work_days) AS emp_workdays, sum(nopay_days) AS emp_nopaydays from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id LEFT OUTER JOIN (select employee_banks.id, employee_banks.bank_ac_no, banks.bank as bank_name, bank_branches.branch as bank_branch_name from employee_banks inner join banks on employee_banks.bank_code=banks.code inner join bank_branches on (employee_banks.bank_code=bank_branches.bankcode AND employee_banks.branch_code=bank_branches.code)) as employee_banks ON drv_catinfo.employee_bank_id=employee_banks.id ";
	// 	$sqlslip.="ORDER BY ifnull(nullif(drv_emp.emp_etfno, 0), 99999), drv_emp.emp_epfno, drv_info.fig_id";//ORDER BY drv_info.fig_id
		
	// 	$emp_data = DB::select($sqlslip, [$payment_period_id, 
	// 									  $request->rpt_location_id, $emp_department_val,
	// 									  $payment_period_id]
	// 						   );
		
	// 	$emp_array = array();
		
		
	// 	$cnt = 0;
	// 	$act_payslip_id = '';
	// 	$net_payslip_fig_value = 0;
	// 	$emp_fig_totearn = 0;
	// 	$emp_fig_otherearn = 0; //other-additions
	// 	$emp_fig_totlost = 0;
	// 	$emp_fig_otherlost = 0; //other-deductions
	// 	$emp_fig_tottax = 0;
		
	// 	//2023-11-07
	// 	//keys-selected-to-calc-paye-updated-from-remuneration-taxation
	// 	$conf_tl = RemunerationTaxation::where(['fig_calc_opt'=>'FIGPAYE', 'optspec_cancel'=>0])
	// 					->pluck('taxcalc_spec_code')->toArray(); //var_dump($conf_tl);
	// 	//return response()->json($conf_tl);
	// 	//-2023-11-07
		
	// 	foreach($emp_data as $r){
	// 		if($act_payslip_id!=$r->emp_payslip_id){
	// 			$cnt++;
	// 			$act_payslip_id=$r->emp_payslip_id;
	// 			$net_payslip_fig_value = 0;
	// 			$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
	// 			$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
	// 			$emp_fig_tottax = 0;
	// 		}
	// 		if(!isset($emp_array[$cnt-1])){
	// 			$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_national_id'=>$r->emp_national_id, 'bank_accno'=>$r->bank_ac_no, 'bank_name'=>$r->bank_name, 'bank_branch'=>$r->bank_branch_name, 'emp_first_name'=>$r->emp_first_name, 'emp_designation'=>$r->emp_designation, 'Office'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTAMT1'=>$r->emp_otamt1, 'OTAMT2'=>$r->emp_otamt2, 'WORKDAYSCNT'=>$r->emp_workdays, 'work_week_days'=>$r->emp_workdays, 'NOPAYCNT'=>$r->emp_nopaydays, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
				
	// 		}
			
	// 		$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
			
	// 		if(isset($emp_array[$cnt-1][$fig_key])){
	// 			$fig_group_val=$emp_array[$cnt-1][$fig_key];
				
	// 			$emp_array[$cnt-1][$fig_key]=number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
				
	// 			if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
	// 				$net_payslip_fig_value+=$r->fig_value;
	// 				$emp_array[$cnt-1]['NETSAL']=number_format((float)$net_payslip_fig_value, 2, '.', '');
					
	// 				if(($r->epf_payable==1)||($fig_key=='NOPAY')){
	// 					$emp_fig_tottax += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_fortax']=number_format((float)$emp_fig_tottax, 2, '.', '');
	// 				}
					
	// 				$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
	// 				//if(($r->fig_value>=0)||($fig_key!='EPF8'))
	// 				if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_earn']=number_format((float)$emp_fig_totearn, 2, '.', '');
	// 				}
					
	// 				if($r->fig_value>=0){
	// 					/*
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_earn']=number_format((float)$emp_fig_totearn, 2, '.', '');
	// 					*/
	// 					$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
	// 					$emp_array[$cnt-1]['add_other']=number_format((float)$emp_fig_otherearn, 2, '.', '');
	// 				}else{
	// 					if($fig_key!='NOPAY'){
	// 						$emp_fig_totlost += $r->fig_value;
	// 						$emp_array[$cnt-1]['tot_ded']=number_format((float)abs($emp_fig_totlost), 2, '.', '');
	// 					}
	// 					$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
	// 					$emp_array[$cnt-1]['ded_other']=number_format((float)$emp_fig_otherlost, 2, '.', '');
	// 				}
	// 			}
				
	// 			if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
	// 				//if($emp_array[$cnt-1]['tot_bnp']==0){
	// 					$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
	// 					$emp_array[$cnt-1]['tot_bnp']=number_format((float)$emp_tot_bnp, 2, '.', '');
						
	// 				//}
	// 			}
	// 		}
	// 	}
	// 	/*
	// 	$ea=$emp_array;
	// 	for($cnt=1;$cnt<26;$cnt++){
	// 		$emp_array=array_merge($emp_array, $ea);
	// 	}
	// 	*/
	// 	/*
	// 	Excel::create('SignatureSheet '.$request->rpt_info, function($excel) use ($emp_array){
	// 		$excel->setTitle('Signature List');
	// 		$excel->sheet('SalarySheet', function($sheet) use ($emp_array){
	// 			$sheet->fromArray($emp_array, null, 'A1', false, false);
	// 		});
	// 	})->download('xlsx');
	// 	*/
	// 	$more_info=$payment_period_fr.' / '.$payment_period_to;
	// 	$sect_name = $request->rpt_dept_name;
	// 	$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');
		
	// 	ini_set("memory_limit", "999M");
	// 	ini_set("max_execution_time", "999");
		
	// 	$pdf = PDF::loadView('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name', 'company_name', 'company_addr'));
    //     return $pdf->download('salary-list.pdf');
	// 	//return view('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info'));
    // }
	public function downloadSalarySheet(Request $request){
        $companyRegInfo = Company::find($request->rpt_location_id);
		$company_addr = $companyRegInfo->address;

        $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;

		// $sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_epfno,drv_emp.emp_national_id, drv_emp.emp_first_name, drv_emp.emp_designation, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_nopaydays FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno,ifnull(employees.emp_national_id, '') AS emp_national_id, employees.emp_name_with_initial AS emp_first_name, job_titles.title AS emp_designation, companies.name AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, sum(nopay_days) AS emp_nopaydays from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";


		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.payroll_profile_id, drv_emp.disp_epfno AS emp_epfno, drv_emp.emp_national_id, 
		IFNULL(employee_banks.bank_ac_no, '') AS bank_ac_no, IFNULL(employee_banks.bank_name, '') as bank_name, IFNULL(employee_banks.bank_branch_name, '') as bank_branch_name, drv_emp.emp_first_name, drv_emp.emp_designation, 
		drv_emp.location, drv_emp.department_name, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable 
		AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_nopaydays, drv_catinfo.work_days, drv_catinfo.working_week_days 
		FROM (SELECT employee_payslips.id AS emp_payslip_id, employee_payslips.payroll_profile_id, employees.emp_id AS emp_epfno, employees.emp_etfno as disp_epfno, 
		ifnull(employees.emp_national_id, '') AS emp_national_id, employees.emp_name_with_initial AS emp_first_name, job_titles.title 
		AS emp_designation, companies.name AS location, departments.name AS department_name  FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id 
		INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id INNER JOIN departments ON employees.emp_department=departments.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp 
		INNER JOIN (select employee_payslip_id, employee_bank_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, 
		sum(nopay_days) AS emp_nopaydays, SUM(work_days) as work_days, SUM(work_days_exclusions) as working_week_days from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id 
		INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code 
		AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments 
		WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id 
		LEFT OUTER JOIN (select employee_banks.id, employee_banks.bank_ac_no, banks.bank as bank_name, bank_branches.branch as bank_branch_name from employee_banks inner join banks on employee_banks.bank_code=banks.code inner join bank_branches on (employee_banks.bank_code=bank_branches.bankcode AND employee_banks.branch_code=bank_branches.code)) as employee_banks ON drv_catinfo.employee_bank_id=employee_banks.id ORDER BY drv_emp.emp_epfno";
		
		$emp_data = DB::select($sqlslip, [$payment_period_id,$request->rpt_location_id, $request->rpt_dept_id,$payment_period_id]);
		
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
		$pdf = PDF::loadView('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name', 'company_addr'));
        return $pdf->download('salary-list.pdf');

    }
}
