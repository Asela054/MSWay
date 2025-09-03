<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveType;
use Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

        // $loginid=Auth::user()->id;
		
	

        $employee = DB::table('users')
                        ->select('employees.*','employee_pictures.emp_pic_filename','departments.name AS departmentname','companies.name AS companyname','branches.location','employment_statuses.emp_status AS emp_statusname','job_categories.category','job_titles.title','users.emp_id','employees.id AS emprecordid','employees.emp_location')
                        ->leftjoin('employees','employees.emp_id','users.emp_id')
						->leftjoin('job_categories','job_categories.id','employees.job_category_id')
						->leftjoin('job_titles','job_titles.id','employees.emp_job_code')
						->leftjoin('employment_statuses','employment_statuses.id','employees.emp_status')
						->leftjoin('branches','branches.id','employees.emp_location')
						->leftjoin('companies','companies.id','employees.emp_company')
						->leftjoin('departments','departments.id','employees.emp_department')
						->leftjoin('employee_pictures','employee_pictures.emp_id','employees.emp_id')
                        ->where('users.id', $users_id)
                        ->first();
					
        $emprecordid=$employee->emprecordid;
        $emp_id=$employee->emp_id;
		$emp_name_with_initial=$employee->emp_name_with_initial;
		$calling_name=$employee->calling_name;
        $emp_location=$employee->emp_location;

		$leavetype = LeaveType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		
        $employees = Employee::where('leave_approve_person', 1)->get();
        return view('UserAccountSummery.useraccountsummery',compact('emprecordid','emp_id','emp_location','employee','leavetype','emp_name_with_initial','calling_name','payment_period','employees'));
    }

    public function get_employee_monthlysummery(Request $request)
    {
        $selectedmonth = $request->input('selectedmonth');
		$closedate = $request->input('lastday');
        $emprecordid = $request->input('emprecordid');
        $empid = $request->input('empid');
        $emplocation = $request->input('emplocation');
           
	
        $monthworkingdaysdata=DB::table('employees')
                            ->leftJoin('job_categories','job_categories.id','employees.job_category_id')
                            ->select('employees.job_category_id','job_categories.emp_payroll_workdays')
                            ->where('employees.id',$empid)
                            ->first();

							//dd($monthworkingdaysdata);
							
        $monthworkingdays=26;
		
		
        $work_days = (new \App\Attendance)->get_work_days($empid, $selectedmonth, $closedate);
		
		        
        $working_week_days_arr = (new \App\Attendance)->get_working_week_days($empid, $selectedmonth, $closedate)['no_of_working_workdays'];
	 
        $leave_days = (new \App\Attendance)->get_leave_days($empid, $selectedmonth, $closedate);
		                               
        $no_pay_days = (new \App\Attendance)->get_no_pay_days($empid, $selectedmonth, $closedate);
		           
	  	     
        $attendance_responseData= array(
            'workingdays'=>  $work_days,
            'absentdays'=>  ($monthworkingdays-$work_days),
            'working_week_days_arr'=>  $working_week_days_arr,
            'leave_days'=>  $leave_days,
            'no_pay_days'=>  $no_pay_days,
        );

        // payroll part--------------------------------------------------------------------------------------------------------------------------------
        
        $payment_period = DB::table('employee_payslips')
        ->join('payroll_profiles','payroll_profiles.id','employee_payslips.payroll_profile_id')
        ->select('employee_payslips.id','employee_payslips.payment_period_id','employee_payslips.emp_payslip_no','employee_payslips.payroll_profile_id','employee_payslips.payment_period_fr','employee_payslips.payment_period_to')
        ->where('employee_payslips.payment_period_fr', 'LIKE', $selectedmonth.'-%')
        ->where('payroll_profiles.emp_id', $emprecordid)
        ->where('employee_payslips.payslip_cancel', '0')
        ->orderBy('employee_payslips.id', 'desc')  // Order by payment_period_fr in descending order
        ->first();

	
        
        $payment_period_id=$payment_period->payment_period_id;
        $payment_period_fr=$payment_period->payment_period_fr;
        $payment_period_to=$payment_period->payment_period_to;
        $payslip_id=$payment_period->id;
        $payroll_profile_id=$payment_period->payroll_profile_id;

	

        //branches.location as location - branches.region as location
        //INNER JOIN branches ON employees.emp_location - INNER JOIN regions AS branches ON employees.region_id

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
       
        
        $employee = DB::select($sqlslip, [$payment_period_id, $emplocation, $emprecordid, $payment_period_id]);

// dd($employee);


        $sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
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
   
        return response() ->json(['result'=>  $attendance_responseData,'salaryresult'=>$sum_array,'payslip_id'=>$payslip_id,'payroll_profile_id'=>$payroll_profile_id]);
    }


	
public function downloadSalarySheet(Request $request){
    
        $company = Session::get('emp_company');
        $companyRegInfo = Company::find($company);
		$company_name = $companyRegInfo->name;
		$company_addr = $companyRegInfo->address;

        $emp_id = Session::get('emp_id');
		$emp_location = Session::get('emp_location');
		$emp_department = Session::get('emp_department');

        $payslip_id=$request->payslip_id;
		
        $paymentPeriod=PaymentPeriod::find($request->period);
			
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		/*
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payment_period_id=? AND employees.emp_location=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
		*/
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_national_id, drv_emp.emp_first_name, drv_emp.emp_designation, drv_emp.location, IFNULL(employee_banks.bank_ac_no, '') AS bank_ac_no, IFNULL(employee_banks.bank_name, '') as bank_name, IFNULL(employee_banks.bank_branch_name, '') as bank_branch_name, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_workdays, drv_catinfo.emp_nopaydays FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, ifnull(employees.emp_national_id, '') AS emp_national_id, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, job_titles.title AS emp_designation, companies.name AS location, companies.address AS company_disp_address FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employees.emp_id=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (select employee_payslip_id, employee_bank_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, sum(work_days) AS emp_workdays, sum(nopay_days) AS emp_nopaydays from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id LEFT OUTER JOIN (select employee_banks.id, employee_banks.bank_ac_no, banks.bank as bank_name, bank_branches.branch as bank_branch_name from employee_banks inner join banks on employee_banks.bank_code=banks.code inner join bank_branches on (employee_banks.bank_code=bank_branches.bankcode AND employee_banks.branch_code=bank_branches.code)) as employee_banks ON drv_catinfo.employee_bank_id=employee_banks.id ";
		$sqlslip.="ORDER BY ifnull(nullif(drv_emp.emp_etfno, 0), 99999), drv_emp.emp_epfno, drv_info.fig_id";//ORDER BY drv_info.fig_id
		
		$emp_data = DB::select($sqlslip, [$payment_period_id, 
										  $emp_location, $emp_department ,$emp_id,
										  $payment_period_id, $payslip_id]
							   );
		
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
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_national_id'=>$r->emp_national_id, 'bank_accno'=>$r->bank_ac_no, 'bank_name'=>$r->bank_name, 'bank_branch'=>$r->bank_branch_name, 'emp_first_name'=>$r->emp_first_name, 'emp_designation'=>$r->emp_designation, 'Office'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTAMT1'=>$r->emp_otamt1, 'OTAMT2'=>$r->emp_otamt2, 'WORKDAYSCNT'=>$r->emp_workdays, 'work_week_days'=>$r->emp_workdays, 'NOPAYCNT'=>$r->emp_nopaydays, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
				
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
						/*
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=number_format((float)$emp_fig_totearn, 2, '.', '');
						*/
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
					//if($emp_array[$cnt-1]['tot_bnp']==0){
						$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
						$emp_array[$cnt-1]['tot_bnp']=number_format((float)$emp_tot_bnp, 2, '.', '');
						
					//}
				}
			}
		}
		/*
		$ea=$emp_array;
		for($cnt=1;$cnt<26;$cnt++){
			$emp_array=array_merge($emp_array, $ea);
		}
		*/
		/*
		Excel::create('SignatureSheet '.$request->rpt_info, function($excel) use ($emp_array){
			$excel->setTitle('Signature List');
			$excel->sheet('SalarySheet', function($sheet) use ($emp_array){
				$sheet->fromArray($emp_array, null, 'A1', false, false);
			});
		})->download('xlsx');
		*/
		$more_info=$payment_period_fr.' / '.$payment_period_to;
		$sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');
		
		ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");
		
		$pdf = PDF::loadView('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name', 'company_name', 'company_addr'));
        return $pdf->download('salary-list.pdf');
		//return view('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info'));
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

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query =  DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
            ->join('employees as ec', 'leaves.emp_covering', '=', 'ec.emp_id')
            ->join('employees as e', 'leaves.emp_id', '=', 'e.emp_id')
            ->leftjoin('branches', 'e.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'e.emp_department', '=', 'departments.id')
            ->select('leaves.*', 'ec.emp_name_with_initial as covering_emp', 'leave_types.leave_type', 'e.emp_name_with_initial as emp_name', 'departments.name as dep_name');

        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee != ''){
            $query->where(['e.emp_id' => $employee]);
        }

        if($location != ''){
            $query->where(['e.emp_location' => $location]);
        }

        if($from_date != '' && $to_date != ''){
            $query->whereBetween('leaves.leave_from', [$from_date, $to_date]);
        }

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
                                'update_date' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString()
                            ]);
                    } else {
                        DB::table('employee_pictures')->insert([
                            'emp_id' => $id,
                            'emp_pic_filename' => $name,
                            'insert_user' => Auth::id(),
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString()
                        ]);
                    }
                } else {
                    Session::flash('error', 'Failed to upload image. Please try again.');
                    return redirect('/useraccountsummery');
                }
            } else {
                Session::flash('error', 'Invalid image file. Please select a valid image.');
                return redirect('/useraccountsummery');
            }
        }
    }
    // if ($request->hasFile('profile_image')) {
    //     // Store new image
    //     $imageName = time().'.'.$request->profile_image->getClientOriginalExtension();
    //     $request->profile_image->storeAs('public/images/profile/', $imageName);

    //     if ($picture) {
    //         // Delete old image if exists
    //         if (!empty($picture->emp_pic_filename) && Storage::exists('public/images/'.$picture->emp_pic_filename)) {
    //             Storage::delete('public/images/profile/'.$picture->emp_pic_filename);
    //         }

    //         // Update existing record
    //         $picture->emp_pic_filename = $imageName;
    //         $picture->save();
            
    // return response()->json([
    //     'success' => true,
    //     'message' => 'Profile image updated successfully'
    // ]);

    //     } else {
    //         // Insert new record
    //         EmployeePicture::create([
    //             'emp_id' => $id,
    //             'emp_pic_filename' => $imageName,
    //         ]);

            
    // return response()->json([
    //     'success' => true,
    //     'message' => 'Profile image Instert successfully'
    // ]);
    //     }
    // }

    


//get_incomplete_attendance_by_employee_data
    

public function get_attendance_by_employee_data(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('user-attendance-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
                $employee = Session::get('emp_id');
                $location = Session::get('emp_location');
                $department = Session::get('emp_department');

                $from_date = Request('from_date') ?? date('Y-m-01');
		        $to_date = Request('to_date') ?? date('Y-m-t');

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

            $query->where(['approved' => '1'])
                ->groupBy('at1.uid', 'at1.date');

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
