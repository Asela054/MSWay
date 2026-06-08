<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Company;
use App\Employee;
use App\PaymentPeriod;
use App\RemunerationTaxation;
use Illuminate\Support\Facades\DB;
use PDF; 

class APIPayrollController extends Controller
{
     public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }



        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

     public function get_employee_monthlysummery(Request $request)
    {
        $selectedmonth = $request->input('selectedmonth');
        $emprecordid = $request->input('emprecordid');
        $empid = $request->input('empid');
        $empcompany = $request->input('empcompany');

        $closedate = \Carbon\Carbon::parse($selectedmonth)->endOfMonth()->format('Y-m-d');
           
        $monthworkingdaysdata=DB::table('employees')
                            ->leftJoin('job_categories','job_categories.id','employees.job_category_id')
                            ->select('employees.job_category_id','job_categories.emp_payroll_workdays')
                            ->where('employees.id',$emprecordid)
                            ->first();

        $monthworkingdays=$monthworkingdaysdata->emp_payroll_workdays;

        $work_days = (new \App\Attendance)->get_work_days($empid, $selectedmonth,$closedate);
                                       
        $working_week_days_arr = (new \App\Attendance)->get_working_week_days($empid, $selectedmonth,$closedate)['no_of_working_workdays'];
                                          
        $leave_days = (new \App\Leave)->get_leave_days($empid, $selectedmonth,$closedate);
                                           
        $no_pay_days = (new \App\Leave)->get_no_pay_days($empid, $selectedmonth,$closedate);
                         
                           
        $attendance_responseData= array(
            'workingdays'=>  $work_days,
            'absentdays'=>  ($monthworkingdays-$work_days),
            'working_week_days_arr'=>  $working_week_days_arr,
            'leave_days'=>  $leave_days,
            'no_pay_days'=>  $no_pay_days,
        );

        
        $payment_period = DB::table('employee_payslips')
        ->leftjoin('payroll_profiles','payroll_profiles.id','employee_payslips.payroll_profile_id')
        ->select('employee_payslips.id','employee_payslips.payment_period_id','employee_payslips.payment_period_fr','employee_payslips.payment_period_to')
        ->where('employee_payslips.payment_period_fr', 'LIKE', $selectedmonth.'-%')
        ->where('payroll_profiles.emp_id', $emprecordid)
        ->where('employee_payslips.payslip_cancel', '0')
        ->orderBy('employee_payslips.id', 'desc')  
        ->first();


        if (!$payment_period) {
            return (new BaseController)->sendResponse('Your salary has not been processed for this month','failed');
        }

        $payment_period_id=$payment_period->payment_period_id;
        $payment_period_fr=$payment_period->payment_period_fr;
        $payment_period_to=$payment_period->payment_period_to;



            $sqlslip="SELECT 
                            drv_emp.emp_payslip_id, 
                            drv_emp.emp_epfno, 
                            drv_emp.emp_first_name, 
                            drv_emp.location, 
                            drv_emp.payslip_held, 
                            drv_emp.payslip_approved, 
                            drv_info.fig_group_title, 
                            drv_info.fig_group, 
                            drv_info.fig_value AS fig_value, 
                            drv_info.epf_payable AS epf_payable, 
                            drv_info.remuneration_pssc, 
                            drv_info.remuneration_tcsc 
                        FROM 
                            (SELECT employee_payslips.id AS emp_payslip_id, 
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
       
        
        $employee = DB::select($sqlslip, [$payment_period_id, $empcompany, $emprecordid, $payment_period_id]);
    

       
        $sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
		
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
		$sum_array = array('BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
		$cnt = 1;
		$act_payslip_id = '';
		$net_payslip_fig_value = 0;
		$emp_fig_totearn = 0;
		$emp_fig_otherearn = 0; 
		$emp_fig_totlost = 0;
		$emp_fig_otherlost = 0; 
		$emp_fig_tottax = 0;
		
		$rem_tot_bnp = 0;
		$rem_tot_fortax = 0;
		$rem_tot_earn = 0;
		$rem_tot_ded = 0;
		$rem_net_sal = 0;
		$rem_ded_other = 0;
		
		
		
        $conf_tl = DB::table('remuneration_taxations')
        ->where(['fig_calc_opt' => 'FIGPAYE', 'optspec_cancel' => 0])
        ->pluck('taxcalc_spec_code')
        ->toArray();

		
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


				$emp_array[] = array('BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
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
				
				if($fig_key!='OTHER_REM'){
					$emp_array[$cnt-1][$fig_key]=(abs($r->fig_value)+$fig_group_val);
					$sum_array[$fig_key]+=abs($r->fig_value);
				}
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$emp_array[$cnt-1]['NETSAL']=$net_payslip_fig_value;
					
					$reg_net_sal=$sum_array['NETSAL']-$rem_net_sal;
					$sum_array['NETSAL']=($reg_net_sal+$net_payslip_fig_value);
					$rem_net_sal = $net_payslip_fig_value;
					
					if(in_array($r->remuneration_tcsc, $conf_tl)){
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
						$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
						$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
						$rem_tot_fortax = $emp_fig_tottax;
					}
					
					$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
					if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
						$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
						$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
						$rem_tot_earn = $emp_fig_totearn;
					}
					
					if($r->fig_value>=0){
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
				
						$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
						$emp_array[$cnt-1]['tot_bnp']=$emp_tot_bnp;//number_format((float)$emp_tot_bnp, 2, '.', '');
						
						$reg_tot_bnp=$sum_array['tot_bnp']-$rem_tot_bnp;
						$sum_array['tot_bnp']=($reg_tot_bnp+$emp_tot_bnp);
						$rem_tot_bnp = $emp_tot_bnp;
					
				}
			}
		}
        
        $data = array(
            'result' => $attendance_responseData,
            'salaryresult'=>$sum_array
        );

        return (new BaseController)->sendResponse($data, 'result','salaryresult');
    }

    public function downloadEmployeeSalarySheet(Request $request)
    {
        // Get employee ID and payment period ID from request
        $selectedmonth = $request->input('selectedmonth');
        $emprecordid = $request->input('emprecordid');
        $empid = $request->input('empid');
        $empcompany = $request->input('empcompany');
        
        // Get company information
        $companyRegInfo = Company::find($empcompany);
        $company_name = $companyRegInfo->name;
        $company_addr = $companyRegInfo->address;

        // Get payment period
        $payment_period = DB::table('employee_payslips')
            ->leftjoin('payroll_profiles','payroll_profiles.id','employee_payslips.payroll_profile_id')
            ->select('employee_payslips.id','employee_payslips.payment_period_id','employee_payslips.payment_period_fr','employee_payslips.payment_period_to')
            ->where('employee_payslips.payment_period_fr', 'LIKE', $selectedmonth.'-%')
            ->where('payroll_profiles.emp_id', $emprecordid)
            ->where('employee_payslips.payslip_cancel', '0')
            ->orderBy('employee_payslips.id', 'desc')  
            ->first();
            
            $payment_period_id=$payment_period->payment_period_id;
            $payment_period_fr=$payment_period->payment_period_fr;
            $payment_period_to=$payment_period->payment_period_to;

        // Get employee details
        $employee = Employee::find($emprecordid);
        $emp_company_id = $employee->emp_company;
        $emp_department_id = $employee->emp_department;

        // Build SQL query for single employee
        $sqlslip = "SELECT 
                        drv_emp.emp_payslip_id, 
                        drv_emp.payroll_profile_id, 
                        drv_emp.disp_epfno AS emp_epfno, 
                        drv_emp.emp_national_id,
                        IFNULL(employee_banks.bank_ac_no, '') AS bank_ac_no, 
                        IFNULL(employee_banks.bank_name, '') as bank_name, 
                        IFNULL(employee_banks.bank_branch_name, '') as bank_branch_name, 
                        drv_emp.emp_first_name, 
                        drv_emp.emp_designation, 
                        drv_emp.location, 
                        drv_emp.department_name, 
                        drv_info.fig_group_title, 
                        drv_info.fig_value AS fig_value, 
                        drv_info.epf_payable AS epf_payable, 
                        drv_info.remuneration_pssc, 
                        drv_info.remuneration_tcsc, 
                        drv_catinfo.emp_otamt1, 
                        drv_catinfo.emp_otamt2, 
                        drv_catinfo.emp_nopaydays, 
                        drv_catinfo.work_days, 
                        drv_catinfo.working_week_days 
                    FROM 
                        (SELECT 
                            employee_payslips.id AS emp_payslip_id, 
                            employee_payslips.payroll_profile_id, 
                            employees.emp_id AS emp_epfno, 
                            employees.emp_etfno as disp_epfno, 
                            IFNULL(employees.emp_national_id, '') AS emp_national_id, 
                            employees.emp_name_with_initial AS emp_first_name, 
                            job_titles.title AS emp_designation, 
                            companies.name AS location, 
                            departments.name AS department_name  
                        FROM `employee_payslips` 
                        INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id 
                        INNER JOIN employees ON payroll_profiles.emp_id = employees.id 
                        INNER JOIN companies ON employees.emp_company = companies.id 
                        INNER JOIN departments ON employees.emp_department = departments.id 
                        LEFT OUTER JOIN job_titles ON employees.emp_job_code = job_titles.id 
                        WHERE employee_payslips.payment_period_id = ? 
                        AND employees.emp_company = ? 
                        AND employees.id = ? 
                        AND employee_payslips.payslip_cancel = 0) AS drv_emp 
                    INNER JOIN 
                        (SELECT 
                            employee_payslip_id, 
                            employee_bank_id, 
                            SUM(normal_rate_otwork_hrs) AS emp_otamt1, 
                            SUM(double_rate_otwork_hrs) AS emp_otamt2, 
                            SUM(nopay_days) AS emp_nopaydays, 
                            SUM(work_days) as work_days, 
                            SUM(work_days_exclusions) as working_week_days 
                        FROM employee_paid_rates 
                        GROUP BY employee_payslip_id) AS drv_catinfo 
                    ON drv_emp.emp_payslip_id = drv_catinfo.employee_payslip_id 
                    INNER JOIN 
                        (SELECT 
                            `id` AS fig_id, 
                            `employee_payslip_id`, 
                            `fig_group_title`, 
                            `epf_payable`, 
                            remuneration_payslip_spec_code AS remuneration_pssc, 
                            remuneration_taxcalc_spec_code AS remuneration_tcsc, 
                            `fig_value` AS fig_value 
                        FROM employee_salary_payments 
                        WHERE `payment_period_id` = ?) AS drv_info 
                    ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id 
                    LEFT OUTER JOIN 
                        (SELECT 
                            employee_banks.id, 
                            employee_banks.bank_ac_no, 
                            banks.bank as bank_name, 
                            bank_branches.branch as bank_branch_name 
                        FROM employee_banks 
                        INNER JOIN banks ON employee_banks.bank_code = banks.code 
                        INNER JOIN bank_branches ON (employee_banks.bank_code = bank_branches.bankcode 
                        AND employee_banks.branch_code = bank_branches.code)) as employee_banks 
                    ON drv_catinfo.employee_bank_id = employee_banks.id 
                    ORDER BY drv_info.fig_id";



        // Execute query for single employee
        $emp_data = DB::select($sqlslip, [$payment_period_id, $emp_company_id, $emprecordid, $payment_period_id]);

        // Initialize employee array
        $emp_array = array();
        $cnt = 0;
        $act_payslip_id = '';
        $net_payslip_fig_value = 0;
        $emp_fig_totearn = 0;
        $emp_fig_otherearn = 0;
        $emp_fig_totlost = 0;
        $emp_fig_otherlost = 0;
        $emp_fig_tottax = 0;

        // Get tax configuration
        $conf_tl = RemunerationTaxation::where(['fig_calc_opt'=>'FIGPAYE', 'optspec_cancel'=>0])
                        ->pluck('taxcalc_spec_code')->toArray();

        foreach($emp_data as $r){
            if($act_payslip_id != $r->emp_payslip_id){
                $cnt++;
                $act_payslip_id = $r->emp_payslip_id;
                $net_payslip_fig_value = 0;
                $emp_fig_totearn = 0; 
                $emp_fig_otherearn = 0;
                $emp_fig_totlost = 0; 
                $emp_fig_otherlost = 0;
                $emp_fig_tottax = 0;
            }
            
            if(!isset($emp_array[$cnt-1])){
                $emp_array[] = array(
                    'emp_epfno' => $r->emp_epfno,
                    'emp_national_id' => $r->emp_national_id,
                    'bank_name' => $r->bank_name,
                    'bank_branch' => $r->bank_branch_name,
                    'bank_accno' => $r->bank_ac_no,
                    'emp_first_name' => $r->emp_first_name,
                    'emp_designation' => $r->emp_designation,
                    'emp_department' => $r->department_name,
                    'Office' => $r->location,
                    'BASIC' => 0,
                    'BRA_I' => '0',
                    'add_bra2' => '0',
                    'NOPAY' => 0,
                    'tot_bnp' => 0,
                    'sal_arrears1' => 0,
                    'tot_fortax' => 0,
                    'ATTBONUS' => 0,
                    'ATTBONUS_W' => 0,
                    'INCNTV_EMP' => 0,
                    'INCNTV_DIR' => 0,
                    'add_transport' => 0,
                    'add_other' => 0,
                    'sal_arrears2' => 0,
                    'OTAMT1' => $r->emp_otamt1,
                    'OTAMT2' => $r->emp_otamt2,
                    'work_tot_days' => $r->work_days,
                    'work_week_days' => $r->working_week_days,
                    'NOPAYCNT' => $r->emp_nopaydays,
                    'OTHRS1' => 0,
                    'OTHRS2' => 0,
                    'tot_earn' => 0,
                    'EPF8' => 0,
                    'EPF12' => 0,
                    'ETF3' => 0,
                    'sal_adv' => 0,
                    'ded_tp' => 0,
                    'ded_IOU' => 0,
                    'ded_fund_1' => 0,
                    'ded_other' => 0,
                    'PAYE' => 0,
                    'LOAN' => 0,
                    'tot_ded' => 0,
                    'NETSAL' => 0,
                    'OTHER_REM' => 0
                );
            }
            
            $fig_key = isset($emp_array[$cnt-1][$r->fig_group_title]) ? $r->fig_group_title : $r->remuneration_pssc;
            
            if(isset($emp_array[$cnt-1][$fig_key])){
                $fig_group_val = $emp_array[$cnt-1][$fig_key];
                
                $emp_array[$cnt-1][$fig_key] = number_format((float)(abs($r->fig_value) + $fig_group_val), 2, '.', '');
                
                if(!(($r->fig_group_title == 'EPF12') || ($r->fig_group_title == 'ETF3'))){
                    $net_payslip_fig_value += $r->fig_value;
                    $emp_array[$cnt-1]['NETSAL'] = number_format((float)$net_payslip_fig_value, 2, '.', '');
                    
                    if(($r->epf_payable == 1) || ($fig_key == 'NOPAY')){
                        $emp_fig_tottax += $r->fig_value;
                        $emp_array[$cnt-1]['tot_fortax'] = number_format((float)$emp_fig_tottax, 2, '.', '');
                    }
                    
                    $fig_otherrem = ($fig_key == 'OTHER_REM') ? 1 : 0;
                    
                    if((($r->fig_value >= 0) && ($fig_key != 'EPF8')) || ($fig_key == 'NOPAY')){
                        $emp_fig_totearn += $r->fig_value;
                        $emp_array[$cnt-1]['tot_earn'] = number_format((float)$emp_fig_totearn, 2, '.', '');
                    }
                    
                    if($r->fig_value >= 0){
                        $emp_fig_otherearn += ($r->fig_value * $fig_otherrem);
                        $emp_array[$cnt-1]['add_other'] = number_format((float)$emp_fig_otherearn, 2, '.', '');
                    } else {
                        if($fig_key != 'NOPAY'){
                            $emp_fig_totlost += $r->fig_value;
                            $emp_array[$cnt-1]['tot_ded'] = number_format((float)abs($emp_fig_totlost), 2, '.', '');
                        }
                        $emp_fig_otherlost += (abs($r->fig_value) * $fig_otherrem);
                        $emp_array[$cnt-1]['ded_other'] = number_format((float)$emp_fig_otherlost, 2, '.', '');
                    }
                }
                
                if(($fig_key == 'BASIC') || ($fig_key == 'BRA_I') || ($fig_key == 'add_bra2')){
                    $emp_tot_bnp = ($emp_array[$cnt-1]['BASIC'] + $emp_array[$cnt-1]['BRA_I'] + $emp_array[$cnt-1]['add_bra2']);
                    $emp_array[$cnt-1]['tot_bnp'] = number_format((float)$emp_tot_bnp, 2, '.', '');
                }
            }
        }

        $more_info = $payment_period_fr . ' / ' . $payment_period_to;
        $sect_name = $request->rpt_dept_name ?? $employee->department->name ?? '';
        $paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');
        
        // Set memory and execution limits
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");
        
        // Generate PDF for single employee
        $pdf = PDF::loadView('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name','company_name',  'company_addr'));
        // return $pdf->download('salary-list.pdf');

        $pdfContent = $pdf->output();
        $filename = 'salary-sheet-' . $employee->emp_id . '-' . $paymonth_name . '.pdf';

        // Return response with PDF content
            $response = response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdfContent),
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
                'X-File-Name' => $filename,
                'X-Employee-ID' => $employee->emp_id,
                'X-Payment-Period' => $paymonth_name
            ]);

            return $response;
    }


     public function get_monthlysummery(Request $request)
    {
        $salaryperiodid = $request->input('salaryperiodid');
        $emprecordid = $request->input('emprecordid');
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
            --  FROM drv_employee_payslips
            drv_emp.emp_payslip_id, 
            drv_emp.emp_payroll_profile_id, 
            drv_emp.emp_epfno, 
            drv_emp.emp_first_name, 
            drv_emp.location, 
            drv_emp.payslip_held, 
            drv_emp.payslip_approved,

            --  FROM drv_attendance 
            drv_rates.ot1dura, 
            drv_rates.ot2dura, 
            drv_rates.wk_days, 
            drv_rates.abhrs, 
            drv_rates.nopay_days, 

            -- IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays,
            -- FROM drv_jobinfo
             drv_jobinfo.emp_payroll_workdays,

            -- FROM drv_salary 
            drv_info.fig_group_title, 
            drv_info.employee_payslip_id,
            drv_info.fig_group, 
            drv_info.fig_value AS fig_value, 
            drv_info.epf_payable AS epf_payable, 
            drv_info.remuneration_pssc, 
            drv_info.remuneration_tcsc 

        FROM 
            -- SUBQUERY 1: employee + payslip
            -- Customizable: filter by company, branch, department
            (SELECT 
                employee_payslips.id AS emp_payslip_id, 
                employee_payslips.payroll_profile_id AS emp_payroll_profile_id,
                employees.emp_id AS emp_epfno, 
                employees.emp_etfno,
                employees.emp_name_with_initial AS emp_first_name, 
                companies.name AS location, 
                employee_payslips.payslip_held, 
                employee_payslips.payslip_approved,
                payroll_profiles.payroll_act_id
            FROM `employee_payslips` 
            INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id 
            INNER JOIN employees ON payroll_profiles.emp_id = employees.id 
            INNER JOIN companies ON employees.emp_company = companies.id 
            WHERE employee_payslips.payment_period_id = ? 
                AND employees.emp_company = ? 
                AND employees.id  = ? 
                AND employee_payslips.payslip_cancel = 0) 
            AS drv_emp

            -- INNER JOIN job_categories ON drv_emp.payroll_act_id=job_categories.id
            INNER JOIN (
            -- SUBQUERY 2: Job category info
            -- Customizable: working days per job type
            SELECT 
                id,
                emp_payroll_workdays
            FROM job_categories
            ) AS drv_jobinfo ON drv_emp.payroll_act_id = drv_jobinfo.id

        INNER JOIN (
            -- SUBQUERY 3: Attendance and OT data
            -- Customizable: include/exclude OT types, add late minutes, half days ...
            SELECT 
                employee_payslip_id, 
                sum(normal_rate_otwork_hrs) as ot1dura, 
                sum(double_rate_otwork_hrs) as ot2dura, 
                SUM(work_days) as wk_days, 
                0 as abhrs, 
                SUM(nopay_days) AS nopay_days 
            FROM employee_paid_rates 
            WHERE date_format(
                concat(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m'
                ) 
                IN (
                    date_format(?, '%Y-%m'), 
                    date_format(?, '%Y-%m')) 
            GROUP BY employee_payslip_id
        ) AS drv_rates ON drv_emp.emp_payslip_id = drv_rates.employee_payslip_id
        
        INNER JOIN (
            -- SUBQUERY 4: Salary figures/components
            -- Customizable: filter which fig_group_title show BASIC, EPF, OT, TRANSPORT...
            SELECT 
                `id` AS fig_id, 
                `employee_payslip_id`, 
                `fig_group_title`, 
                `fig_group`, 
                `epf_payable`, 
                remuneration_payslip_spec_code AS remuneration_pssc, 
                remuneration_taxcalc_spec_code AS remuneration_tcsc, 
                `fig_value` AS fig_value 
            FROM employee_salary_payments 
            WHERE `payment_period_id`=?
        ) AS drv_info ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id 
        ORDER BY drv_info.fig_id";

        $employee = DB::select($sqlslip, [
            $salaryperiodid,    // payment_period_id in drv_emp
            $empcompany,        // emp_company
            $emprecordid,       // employees.id
            $payment_period_fr, // date_format first IN()
            $payment_period_to, // date_format second IN()
            $salaryperiodid     // payment_period_id in drv_info
        ]);


        $sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
		/*
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Before Nopay', 'Arrears', 'Total for Tax', 'Attendance', 'Transport', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'EPF-8', 'Salary Advance', 'Telephone', 'IOU', 'Funeral Fund', 'Other Deductions', 'PAYE', 'Loans', 'Total Deductions', 'Balance Pay');
		*/
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
		/*
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
		*/
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'Opma_Night_Alw'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>0, 'OTHRS2'=>0, 'OT2DURA'=>0, 'WK_ACT_DAYS'=>0, 'WK_MAX_DAYS'=>0, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
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
						->pluck('taxcalc_spec_code')->toArray();
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
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>$r->nopay_days, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'Opma_Night_Alw'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>$r->ot1dura, 'OTHRS2'=>0, 'OT2DURA'=>$r->ot2dura, 'WK_ACT_DAYS'=>$r->wk_days, 'WK_MAX_DAYS'=>$r->emp_payroll_workdays, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
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

        $remunerationLayout = $this->getRemunerationLayout();

        // Separate into fixed-additions, fixed-deductions, non-fixed-additions, non-fixed-deductions
        $fixed_additions    = $remunerationLayout->filter(function($r) { return $r->allocation_method === 'FIXED' && $r->remuneration_type === 'addition'; });
        $fixed_deductions   = $remunerationLayout->filter(function($r) { return $r->allocation_method === 'FIXED' && $r->remuneration_type === 'deduction'; });
        $nonfixed_additions = $remunerationLayout->filter(function($r) { return $r->allocation_method !== 'FIXED' && $r->remuneration_type === 'addition'; });
        $nonfixed_deductions= $remunerationLayout->filter(function($r) { return $r->allocation_method !== 'FIXED' && $r->remuneration_type === 'deduction'; });
        
         $data = array(
            'result' => $attendance_responseData,
            'salaryresult'=>$sum_array,
            'payslip_id'=>$payslip_id,
            'payroll_profile_id'=>$payroll_profile_id,
            'fixed_additions'=>$fixed_additions,
            'fixed_deductions'=>$fixed_deductions,
            'nonfixed_additions'=>$nonfixed_additions,
            'nonfixed_deductions'=>$nonfixed_deductions
        );

        return (new BaseController)->sendResponse($data, 'result','salaryresult','payslip_id','payroll_profile_id','fixed_additions','fixed_deductions','nonfixed_additions','nonfixed_deductions');
    }

     private function getRemunerationLayout()
    {
        return DB::table('remunerations')
            ->where('remuneration_cancel', 0)
            ->select('payslip_spec_code', 'remuneration_name', 'remuneration_type', 'allocation_method')
            ->orderBy('id', 'asc')
            ->get();
    }

    

     public function GetSalaryperiods(Request $request)
    {
        $payment_period=PaymentPeriod::orderBy('id', 'desc')->get();

        $data = array(
            'payment_period' => $payment_period
        );

        return (new BaseController)->sendResponse($data, 'payment_period');
    }



}
