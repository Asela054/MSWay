<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\PaymentPeriod;
use App\PayrollProcessType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class PaySlipBank extends Controller
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
	
	public function checkPayslipListBankSlip(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'payroll_process_type_id' => 'required',
                'period_filter_id' => 'required'
            );
			
			if($request->location_filter_id=='-1'){
				return response()->json(['errors' => array('Select a Branch')]);
			}
			
			$emp_sal_held_col = '0';
			$emp_location_col = '1';
			$emp_department_col = '2';
			$emp_location_val = '1';
			$emp_department_val = '2';
			
			$sal_epf_uploadno = substr('00'.$request->salary_submit_attemptno, -2); // 17
			
			if($request->opt_rpt=='1'){
				if(!empty($request->location_filter_id)){
					$emp_location_col = "employees.emp_company";//"employees.emp_location";
					$emp_location_val = $request->location_filter_id;
				}
				if(!empty($request->department_filter_id)){
					$emp_department_col = "employees.emp_department";
					$emp_department_val = $request->department_filter_id;
				}
				
				/*
				$rules['location_filter_id']='required';
				$rules['department_filter_id']='required';
				*/
				$emp_sal_held_col = 'employee_payslips.payslip_held';
				/*
				$emp_location_col = "employees.emp_location";
				$emp_department_col = "employees.emp_department";
				$emp_location_val = $request->location_filter_id;
				$emp_department_val = $request->department_filter_id;
				*/
				
			}else{
				if(!empty($request->location_filter_id)){
					$emp_location_col = "employees.emp_company";//"employees.emp_location";
					$emp_location_val = $request->location_filter_id;
				}
				if(!empty($request->department_filter_id)){
					$emp_department_col = "employees.emp_department";
					$emp_department_val = $request->department_filter_id;
				}
			}

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }
			
			$company_acc_info = Company::find($emp_location_val);
			$txt_acc_name = $company_acc_info->bank_account_name;
			$txt_acc_number = $company_acc_info->bank_account_number;
			$txt_acc_br = $company_acc_info->bank_account_branch_code;
			$txt_employer_no = $company_acc_info->employer_number;
			$opt_employer_no = substr($txt_employer_no, -6);
			$txt_zone_code = $company_acc_info->zone_code;

            $payroll_process_types = array('1' => 'Monthly', '2' => 'Weekly', '3' => 'Bi-weekly', '4' => 'Daily');

            $paymentPeriod = PaymentPeriod::find($request->period_filter_id);

            $payment_period_id = $paymentPeriod->id;//1;
            $payment_period_fr = $paymentPeriod->payment_period_fr;//$request->work_date_fr;
            $payment_period_to = $paymentPeriod->payment_period_to;//$request->work_date_to;

            $sqlslip = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_epfno, 
               drv_emp.emp_first_name, drv_emp.emp_national_id, drv_emp.emp_last_name, 
               drv_emp.e_id, drv_emp.occupation_group_id, 
               drv_emp.location,
               drv_emp.bank_ac_no,
               drv_emp.bank_code,
               drv_emp.branch_code,
               drv_emp.payslip_held,
               drv_emp.payslip_approved, 
			   drv_work.work_days, 
               drv_info.fig_group_title, drv_info.fig_group,
               drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc 
                    FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno,
                                 employees.emp_name_with_initial AS emp_first_name, 
								 employees.emp_national_id, employees.emp_last_name, 
                                 employees.id as e_id, ifnull(job_titles.occupation_group_id, 0) as occupation_group_id, 
                                 companies.name AS location,
                                 ebanks.bank_ac_no,
                                 ebanks.bank_code,
                                 ebanks.branch_code,
                                 employee_payslips.payslip_held, 
                                 employee_payslips.payslip_approved 
                                FROM `employee_payslips` 
                                    INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id
                                    INNER JOIN employees ON payroll_profiles.emp_id=employees.id
                                    INNER JOIN companies ON employees.emp_company=companies.id 
									inner join job_titles on employees.emp_job_code=job_titles.id 
                                    LEFT JOIN employee_banks as ebanks ON ebanks.emp_id = employees.id 
                                WHERE employee_payslips.payment_period_id=? 
                                  AND ".$emp_location_col."=? 
                                  AND ".$emp_department_col."=? 
                                  AND employee_payslips.payslip_cancel=0 AND ".$emp_sal_held_col."='0'
                        ) AS drv_emp 
                    INNER JOIN (SELECT `employee_payslip_id`, SUM(`work_days`) as work_days 
								FROM `employee_paid_rates` 
								WHERE concat(`salary_process_year`, `salary_process_month`) in (date_format(?, '%Y%c'), date_format(?, '%Y%c')) 
								group by employee_payslip_id 
								having work_days>0) as drv_work ON drv_emp.emp_payslip_id=drv_work.employee_payslip_id 
					INNER JOIN (SELECT `id` AS fig_id, 
                                `employee_payslip_id`, 
                                `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, 
                                `fig_value` AS fig_value 
                                FROM employee_salary_payments 
                                WHERE `payment_period_id`=? 
                            ) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id 
            ORDER BY drv_info.fig_id";

            $employee = DB::select($sqlslip,
                [
                    $payment_period_id,
                    $emp_location_val,
                    $emp_department_val,
					$payment_period_fr, $payment_period_to, 
                    $payment_period_id
                ]
            );

            $employee_list = array();
            $cnt = 0;
            $act_payslip_id = '';
            $net_payslip_fig_value = 0;
            $total_amount = 0;
            $date_e = 0;
            $hash_total = 0;
            $no_of_transactions = 0;
            $trx_code = '';
			$accnos=array();
			
			$emp_array = array();
			$employee_epf_list = array();
			$employee_etf_list = array();
			
			$accountno_preval = 0;
			
			$formatted_date = '';
			
			if(empty($request->salary_bank_date)){
				$year = Carbon::now()->format('y');
				$month = Carbon::now()->format('m');
				$date = Carbon::now()->format('d');
				$formatted_date = $year . $month . $date;
			}else{
				$formatted_date = Carbon::parse($request->salary_bank_date)->format('ymd');
			}
			
			$cont_period_fr = Carbon::parse($payment_period_fr)->format('Ym');
			$cont_period_to = Carbon::parse($payment_period_to)->format('Ym');
			
            foreach ($employee as $r) {
				if(!empty($r->bank_ac_no)){
					//$process_name='';//isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
					if ($act_payslip_id != $r->emp_payslip_id) {
						$cnt++;
						$act_payslip_id = $r->emp_payslip_id;
						$net_payslip_fig_value = 0;
						$accountno_preval = 0;
					}
					if (!isset($employee_list[$cnt - 1])) {
						$employee_list[] = array(
							'id' => $r->emp_payslip_id,
							'emp_first_name' => $r->emp_first_name,
							'location' => $r->location,
							'BASIC' => 0,
							'NOPAY' => 0,
							'OTHRS' => 0,
							'FACILITY' => 0,
							'LOAN' => 0,
							'ADDITION' => 0,
							'EPF8' => 0,
							'EPF12' => 0,
							'ETF3' => 0,
							'PAYE' => 0,
							'NETSAL' => 0,
							'payslip_cancel' => 1,
							'payslip_held' => $r->payslip_held,
							'payslip_approved' => $r->payslip_approved
						);
					}
					$employee_list[$cnt - 1][$r->fig_group_title] = number_format((float)$r->fig_value, 4, '.', '');
	
					if (!(($r->fig_group_title == 'EPF12') || ($r->fig_group_title == 'ETF3'))) {
						$net_payslip_fig_value += $r->fig_value;
						$employee_list[$cnt - 1]['NETSAL'] = number_format((float)$net_payslip_fig_value, 2, '.', '');
	
						$accountname = str_replace('.', ' ', $r->emp_first_name);//str_replace('.', '', $r->emp_first_name);
						$accountname = str_replace('Mrs', '', $accountname);
						$accountname = str_replace('Mr', '', $accountname);
						$accountname = str_replace('Miss', '', $accountname);
						$accountname = trim($accountname);
						$accountname = str_replace("'", '`', $accountname);
						$accountname = preg_replace("/(?![A-Z a-z 0-9])./", "", $accountname);
						$accountname = preg_replace('/\s+/', " ", $accountname);
						$strlength = strlen($accountname);
						if ($strlength < 20) {
							$accountname = str_pad($accountname, 20, ' ');
						} else {
							$accountname = substr($accountname, 0, 20);
						}
						
						$accountname = strtoupper($accountname);
						
						$accountno = '000000000000';//0;
						$accountno_intval = 0;
						
						if ($r->bank_ac_no != null) {
							$accountno = str_replace('-', '', $r->bank_ac_no);
							$accountno = str_replace('.', '', $accountno);
							$accountno = str_replace(' ', '', $accountno);
							$accountno_intval = ltrim($accountno, '0');
							$accountno = '000000000000' . $accountno;
						} //else {
							//$accountno = '000000000000';
						//}
						
						$accountno = substr($accountno, -12);
	
						$amount = str_replace('.', '', number_format((float)$net_payslip_fig_value, 2, '.', ''));
						$amount = '00000000000'. $amount;//'000000000'. $amount;
						$amount = substr($amount, -11);//substr($amount, -9);
	
						$ref_no = '00000000'.$cnt;
						$ref_no = substr($ref_no, -8);
	
						$employee_list[$cnt - 1]['ref_no'] = $ref_no;
						$employee_list[$cnt - 1]['emp_name'] = $accountname;
						$bank_br_code = $r->bank_code . $r->branch_code;
						$employee_list[$cnt - 1]['bank_br_code'] = $bank_br_code;
						$employee_list[$cnt - 1]['account_no'] = $accountno;//substr(str_pad('', 12, '0').$accountno, -12);
						$employee_list[$cnt - 1]['trx_code'] = '023';
						$employee_list[$cnt - 1]['amount'] = $amount;
						
						
						
						$employee_list[$cnt - 1]['date'] = $formatted_date;
						
						$total_amount += $r->fig_value;//$amount;
						$date_e = $formatted_date;
						$hash_total += ($accountno_intval-$accountno_preval);//$accnos[]=$hash_total;
						$accountno_preval = $accountno_intval;
						$trx_code = '023';
	
						//$no_of_transactions++;
					}
                }
            }
			
			$no_of_transactions = substr(str_pad('', 5, '0').count($employee_list), -5);
			$header_tot_payment = substr(str_pad('', 11, '0').number_format((float)$total_amount, 2, '', ''), -11);
			$header_hash_total = substr(str_pad('', 14, '0').$hash_total, -14);
			
			/*2022-05-25-epf-etf*/
			$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'emp_last_name'=>'', 'emp_nicnum'=>'', 'occu_grade'=>'', 'emp_workdays'=>0, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
			
			$etf_summary_str = '';$ei=array();
			
			$cnt = 0;//1;
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
					$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'emp_last_name'=>$r->emp_last_name, 'emp_nicnum'=>$r->emp_national_id, 'occu_grade'=>$r->occupation_group_id, 'emp_workdays'=>$r->work_days, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
					
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
						$emp_array[$cnt-1][$fig_key]=(abs($r->fig_value)+$fig_group_val);//number_format
						$sum_array[$fig_key]+=abs($r->fig_value);
					}
					
					if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
						$net_payslip_fig_value+=$r->fig_value;
						$emp_array[$cnt-1]['NETSAL']=$net_payslip_fig_value;//number_format((float)$net_payslip_fig_value, 2, '.', '');
						
						$reg_net_sal=$sum_array['NETSAL']-$rem_net_sal;
						$sum_array['NETSAL']=($reg_net_sal+$net_payslip_fig_value);
						$rem_net_sal = $net_payslip_fig_value;
						
						if(($r->epf_payable==1)||($fig_key=='NOPAY')){
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
			/*
			$emp_array[] = $sum_array;
			$employee_epf_list = $emp_array;
			*/
			
			foreach($emp_array as $epfetf_r){
				if(($epfetf_r['EPF8']+$epfetf_r['EPF12']+$epfetf_r['ETF3'])>0){
					$pad_nicnum = substr(str_pad($epfetf_r['emp_nicnum'], 20, ' '), -20);
					$reg_nicnum = substr(str_pad('', 12, '0').$epfetf_r['emp_nicnum'], -12);//etf
					
					$accountname = str_replace('.', ' ', $epfetf_r['emp_first_name']);//str_replace('.', '', $r->emp_first_name);
					$accountname = str_replace('Mrs', '', $accountname);
					$accountname = str_replace('Mr', '', $accountname);
					$accountname = str_replace('Miss', '', $accountname);
					$accountname = str_replace("'", '`', $accountname);
					$accountname = preg_replace("/(?![A-Z a-z 0-9])./", "", $accountname);
					$accountname = preg_replace('/\s+/', " ", $accountname);
					
					//$x=preg_replace('/(?:[A-Z] )*[A-Z]$/', '', $accountname);$ei[]=$x;
					//$accountname = str_ireplace($epfetf_r['emp_last_name'], '', $accountname);
					
					//$accountname = trim($accountname);
					
					/*
					$reglastname = preg_replace('/([a-zA-Z]( [a-zA-Z])+)\s/', '', $accountname);
					$memlastname = substr(str_pad(strtoupper(trim($reglastname)), 40, ' '), -40);//trim($epfetf_r['emp_last_name'])
					$memsurname = substr(str_pad(strtoupper(trim($reglastname)), 30, ' '), -30);//etf-detail
					$meminitials = substr(str_pad(strtoupper(trim(str_replace($reglastname, '', $accountname))), 20, ' '), -20);//epf-and-etf
					*/
					/*
					$input_line = $accountname;
					preg_match('/([a-zA-Z]( [a-zA-Z])+)\s|[A-Z]/', $input_line, $output_array);
					$output_str = isset($output_array[0])?$output_array[0]:'';
					$meminitials = substr(str_pad(strtoupper(trim($output_str)), 20, ' '), -20);//epf-and-etf
					$reglastname = str_replace((trim($output_str).' '), '', $input_line);
					$memlastname = substr(str_pad(strtoupper(trim($reglastname)), 40, ' '), -40);//trim($epfetf_r['emp_last_name'])
					$memsurname = substr(str_pad(strtoupper(trim($reglastname)), 30, ' '), -30);//etf-detail
					*/
					$output_str = str_replace(strtoupper(trim($epfetf_r['emp_last_name'])), '', strtoupper($accountname));
					$meminitials = substr(str_pad(strtoupper(trim($output_str)), 20, ' '), -20);//epf-and-etf
					$reglastname = strtoupper(trim($epfetf_r['emp_last_name']));
					$memlastname = substr(str_pad(trim($reglastname), 40, ' '), -40);//trim($epfetf_r['emp_last_name'])
					$memsurname = substr(str_pad(trim($reglastname), 30, ' '), -30);//etf-detail
					
					$pad_epfno = substr(str_pad('', 6, '0') . $epfetf_r['emp_epfno'], -6);
					$emp_mem_totepf = ($epfetf_r['EPF8']+$epfetf_r['EPF12']);
					$pad_totcont = substr(str_pad('', 7, '0') . number_format((float)$emp_mem_totepf, 2, '.', ''), -10);
					$pad_mem_epf = substr(str_pad('', 7, '0') . number_format((float)$epfetf_r['EPF8'], 2, '.', ''), -10);
					$pad_emp_epf = substr(str_pad('', 7, '0') . number_format((float)$epfetf_r['EPF12'], 2, '.', ''), -10);//employer
					$pad_totearn = substr(str_pad('', 9, '0') . number_format((float)$epfetf_r['tot_fortax'], 2, '.', ''), -12);
					
					$fig_workdays = $request->pay_schedule_days;//19;//$epfetf_r['emp_workdays']
					$pad_workdays = substr(str_pad('', 2, '0') . number_format((float)$fig_workdays, 2, '.', ''), -5);
					
					$pad_mem_etf = substr(str_pad('', 9, '0') . number_format((float)$epfetf_r['ETF3'], 2, '', ''), -9);
					
					$occu_grade = substr(str_pad('', 3, '0').$epfetf_r['occu_grade'], -3);
					
					$employee_epf_list[] = array('emp_epfno'=>$pad_epfno, 'emp_first_name'=>$meminitials, 'emp_last_name'=>$memlastname, 'emp_nicnum'=>$pad_nicnum, 'emp_workdays'=>$pad_workdays, 'tot_fortax'=>$pad_totearn, 'EPF8'=>$pad_mem_epf, 'EPF12'=>$pad_emp_epf, 'tot_epf'=>$pad_totcont, 'member_status'=>'E', 'zone_code'=>$txt_zone_code, 'employer_num'=>$opt_employer_no, 'cont_period'=>$cont_period_fr, 'submit_num'=>$sal_epf_uploadno, 'occu_grade'=>$occu_grade);
					$employee_etf_list[] = array('etf_rec_code'=>'D', 'employer_num'=>$txt_employer_no, 'emp_etfno'=>$pad_epfno, 'emp_first_name'=>$meminitials, 'emp_last_name'=>$memsurname, 'emp_nicnum'=>$reg_nicnum, 'ETF3'=>$pad_mem_etf, 'cont_period_fr'=>$cont_period_fr, 'cont_period_to'=>$cont_period_to);
				}
			}
			
			$pad_memcnt=substr(str_pad('', 6, '0').count($employee_etf_list), -6);
			$pad_totetf=substr(str_pad('', 14, '0').number_format((float)$sum_array['ETF3'], 2, '', ''), -14);
			$etf_summary_str = 'H'.$txt_employer_no.$cont_period_fr.$cont_period_to.$pad_memcnt.$pad_totetf.'24';
			
			/*2022-05-25-epf-etf*/

            return response()->json(['employee_detail' => $employee_list, 'ei'=>$ei, 
                'total_amount' => $header_tot_payment, //$total_amount,
                'date_e' => $date_e,
                'hash_total' => $header_hash_total, //$hash_total,
                'trx_code' => $trx_code,
                'no_of_transactions' => $no_of_transactions,
                'payment_period_id' => $payment_period_id,
                'work_date_fr' => $payment_period_fr,
                'work_date_to' => $payment_period_to,
				'employee_epf_detail' => $employee_epf_list, 
				'employee_etf_detail' => $employee_etf_list, 
				'etf_summary'=>$etf_summary_str,
				'acc_name'=>$txt_acc_name, 
				'acc_number'=>$txt_acc_number,
				'acc_br_code'=>$txt_acc_br]); // , 'accnos'=>$accnos
        }
    }

    public function checkPayslipList(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'payroll_process_type_id' => 'required',
                'location_filter_id' => 'required',
                'period_filter_id' => 'required'
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $payroll_process_types = array('1' => 'Monthly', '2' => 'Weekly', '3' => 'Bi-weekly', '4' => 'Daily');

            $paymentPeriod = PaymentPeriod::find($request->period_filter_id);

            $payment_period_id = $paymentPeriod->id;//1;
            $payment_period_fr = $paymentPeriod->payment_period_fr;//$request->work_date_fr;
            $payment_period_to = $paymentPeriod->payment_period_to;//$request->work_date_to;

            $sqlslip = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group_title, ABS(drv_info.fig_value) AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
            /*

            */
            $employee = DB::select($sqlslip, [$payment_period_id, $request->location_filter_id, $payment_period_id]);


            $employee_list = array();
            $cnt = 0;
            $act_payslip_id = '';

            foreach ($employee as $r) {
                //$process_name='';//isset($payroll_process_types[$r->process_name])?$payroll_process_types[$r->process_name]:'';
                if ($act_payslip_id != $r->emp_payslip_id) {
                    $cnt++;
                    $act_payslip_id = $r->emp_payslip_id;
                }
                if (!isset($employee_list[$cnt - 1])) {
                    $employee_list[] = array('id' => $r->emp_payslip_id, 'emp_first_name' => $r->emp_first_name, 'location' => $r->location, 'BASIC' => 0, 'NOPAY' => 0, 'OTHRS' => 0, 'FACILITY' => 0, 'LOAN' => 0, 'ADDITION' => 0, 'EPF8' => 0, 'EPF12' => 0, 'ETF3' => 0, 'PAYE' => 0, 'payslip_cancel' => 1, 'payslip_held' => $r->payslip_held, 'payslip_approved' => $r->payslip_approved);


                }

                $employee_list[$cnt - 1][$r->fig_group_title] = number_format((float)$r->fig_value, 4, '.', '');
            }

            return response()->json(['employee_detail' => $employee_list,
                'payment_period_id' => $payment_period_id,
                'work_date_fr' => $payment_period_fr,
                'work_date_to' => $payment_period_to]);
        }
    }

    public function reportSalarySheetBankSlip()
    {
        $branch = Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
        $payroll_process_type = PayrollProcessType::orderBy('id', 'asc')->get();
        $payment_period = PaymentPeriod::orderBy('id', 'desc')->get();
        $department=DB::select("select id, company_id, name from departments");
        return view('Payroll.payslipProcess.SalarySheet_list_bank_slip',
            compact('branch',
                'payroll_process_type',
                'payment_period',
                'department')
        );
    }

}
