<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateInterval;
use DatePeriod;
use DateTime;
use Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use PHPExcel;
use PHPExcel_IOFactory;
use Excel;

use App\Company;
use App\PayrollProcessType;
use App\PaymentPeriod;
use App\RemunerationTaxation;

use Validator;

class AuditAttendanceReportController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('attendance-audit-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('AuditReports.attendance_report', compact('companies'));
    }
    public function auditotreport()
    {
        $permission = Auth::user()->can('attendance-audit-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('AuditReports.audit_ot_report', compact('companies'));
    }

    public function generatetimereport(Request $request) {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
    
        $period = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'), 
            new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );
    

        $employees = DB::table('employees')
            ->select(
                'employees.id', 
                'employees.emp_id', 
                'employees.emp_fullname', 
                'departments.name AS departmentname'
            )
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
            ->where('employees.deleted', 0)
            ->where('employees.emp_department', $department)
            ->whereBetween('attendances.date', [$from_date, $to_date])
			->whereNotIn('employees.emp_id', [195, 201])
            ->groupBy('employees.id')
            ->orderBy('employees.id')
            ->get();
    
        $pdfData = [];
    
        foreach ($employees as $employee) {
            $attendanceData = [];
    
            foreach ($period as $date) {
                $currentDate = $date->format('Y-m-d');
				//get data form audit attendance table (requested from client)
                $attendance = DB::table('audit_attendance')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('attendance_date', $currentDate)
                    ->selectRaw('audit_ontime as in_time, audit_offtime as out_time, audit_workhours as duration,audit_ot_count AS ot_hours')
                    ->first();

               
    
                    if ($attendance) {
                        $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
                        $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';
                        $duration = $attendance->duration;
						$audit_ot_count=$attendance->ot_hours;
                            $attendanceData[] = [
                                'date' => $currentDate,
                                'empno' => $employee->emp_id,
                                'Department' => $employee->departmentname,
                                'in_time' => $inTime,
                                'out_time' => $outTime,
                                'duration' => $duration,
                                'ot_hours' => $audit_ot_count
                            ];
                    }
            }
            $pdfData[] = [
                'employee' => $employee,
                'attendance' => $attendanceData,
            ];
        }

        ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");

        $pdf = Pdf::loadView('AuditReports.timeinoutreportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
        return $pdf->download('Audit Time In-Out Report.pdf');
    }

    public function auditgeneratetimereportexcel(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $period = new \DatePeriod(
            new \DateTime($from_date),
            new \DateInterval('P1D'),
            new \DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );

        $employees = DB::table('employees')
            ->select('employees.id', 'employees.emp_id', 'employees.emp_fullname', 'departments.name AS departmentname')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
            ->where('employees.deleted', 0)
            ->where('employees.emp_department', $department)
            ->whereBetween('attendances.date', [$from_date, $to_date])
			->whereNotIn('employees.emp_id', [195, 201])
            ->groupBy('employees.id')
            ->orderBy('employees.id')
            ->get();

        $spreadsheet = new PHPExcel();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headings
        $sheet->fromArray(['Date', 'Employee ID', 'Full Name', 'Department', 'In Time', 'Out Time', 'Duration' , 'OT Hours'], NULL, 'A1');

        $row = 2;

        foreach ($employees as $employee) {
            foreach ($period as $date) {
                $currentDate = $date->format('Y-m-d');
				//get data form audit attendance table (requested from client)
                $attendance = DB::table('audit_attendance')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('attendance_date', $currentDate)
                    ->selectRaw('audit_ontime as in_time, audit_offtime as out_time, audit_workhours as duration ,audit_ot_count')
                    ->first();

                if ($attendance) {
                    $sheet->setCellValue("A$row", $currentDate);
                    $sheet->setCellValue("B$row", $employee->emp_id);
                    $sheet->setCellValue("C$row", $employee->emp_fullname);
                    $sheet->setCellValue("D$row", $employee->departmentname);
                    $sheet->setCellValue("E$row", $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : '');
                    $sheet->setCellValue("F$row", $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : '');
                    $sheet->setCellValue("G$row", $attendance->duration);
                    $sheet->setCellValue("H$row", $attendance->audit_ot_count);
                    $row++;
                }
            }
        }

        $filename = 'Audit_Time_In_Out_Report.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    public function reportPayRegister()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('AuditReports.pay_report',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
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
			
			$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
	
			
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
					$employee_list[]=array('id'=>$r->emp_payslip_id, 'emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'payslip_cancel'=>1, 'payslip_held'=>$r->payslip_held, 'payslip_approved'=>$r->payslip_approved, 'OTHER_REM'=>0);
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

    // public function downloadPayAuditRegister(Request $request){
        
    //     $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
	// 	$payment_period_id=$paymentPeriod->id;//1;
	// 	$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
	// 	$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
	// 	$emp_location_col = '1';
	// 	$emp_department_col = '2';
	// 	$emp_location_val = '1';
	// 	$emp_department_val = '2';
		
	// 	if(!empty($request->rpt_location_id)){
	// 		$emp_location_col = "employees.emp_company";//"employees.emp_location";
	// 		$emp_location_val = $request->rpt_location_id;
	// 	}
	// 	if(!empty($request->rpt_dept_id)){
	// 		$emp_department_col = "employees.emp_department";
	// 		$emp_department_val = $request->rpt_dept_id;
	// 	}
		
	// 	$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_rates.ot1dura, drv_rates.ot2dura, drv_rates.wk_days, drv_rates.abhrs, drv_rates.nopay_days, IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved, payroll_profiles.payroll_act_id FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp ";
	// 	$sqlslip.="INNER JOIN job_categories ON drv_emp.payroll_act_id=job_categories.id ";
	// 	$sqlslip.="INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) as ot1dura, sum(double_rate_otwork_hrs) as ot2dura, SUM(work_days) as wk_days, 0 as abhrs, SUM(nopay_days) AS nopay_days from employee_paid_rates where date_format(concat(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (date_format(?, '%Y-%m'), date_format(?, '%Y-%m')) group by employee_payslip_id) AS drv_rates ON drv_emp.emp_payslip_id=drv_rates.employee_payslip_id ";
	// 	$sqlslip.="INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_emp.emp_epfno, drv_info.fig_id";
	


	// 	// 		

	// 	$emp_data = DB::select($sqlslip, [$payment_period_id, 
	// 									  $request->rpt_location_id, $emp_department_val,
	// 									  $payment_period_fr, $payment_period_to,
	// 									  $payment_period_id]
	// 						   );

							  

	// 	$sect_name = $request->rpt_dept_name;
	// 	$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
	// 	/*
	// 	$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Before Nopay', 'Arrears', 'Total for Tax', 'Attendance', 'Transport', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'EPF-8', 'Salary Advance', 'Telephone', 'IOU', 'Funeral Fund', 'Other Deductions', 'PAYE', 'Loans', 'Total Deductions', 'Balance Pay');
	// 	*/
	// 	$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
	// 	/*
	// 	$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
	// 	*/
	// 	$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>0, 'OTHRS2'=>0, 'OT2DURA'=>0, 'WK_ACT_DAYS'=>0, 'WK_MAX_DAYS'=>0, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
	// 	$cnt = 1;
	// 	$act_payslip_id = '';
	// 	$net_payslip_fig_value = 0;
	// 	$emp_fig_totearn = 0;
	// 	$emp_fig_otherearn = 0; //other-additions
	// 	$emp_fig_totlost = 0;
	// 	$emp_fig_otherlost = 0; //other-deductions
	// 	$emp_fig_tottax = 0;
		
	// 	$rem_tot_bnp = 0;
	// 	$rem_tot_fortax = 0;
	// 	$rem_tot_earn = 0;
	// 	$rem_tot_ded = 0;
	// 	$rem_net_sal = 0;
	// 	$rem_ded_other = 0;
		
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
				

	// 			$total_audit_ot = DB::table('audit_attendance')
	// 				->where('emp_id', $r->emp_epfno)
	// 				->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 				->sum('audit_ot_count');

					
				
	// 			$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>$r->nopay_days, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>$total_audit_ot, 'OTHRS2'=>0, 'OT2DURA'=>$r->ot2dura, 'WK_ACT_DAYS'=>$r->wk_days, 'WK_MAX_DAYS'=>$r->emp_payroll_workdays, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
	// 			$rem_tot_bnp = 0;
	// 			$rem_tot_fortax = 0;
	// 			$rem_tot_earn = 0;
	// 			$rem_tot_ded = 0;
	// 			$rem_net_sal = 0;
	// 			$rem_ded_other = 0;
	// 		}
			
			
	// 		$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
			
	// 		if(isset($emp_array[$cnt-1][$fig_key])){
	// 			$fig_group_val=$emp_array[$cnt-1][$fig_key];
				
	// 			if($fig_key!='OTHER_REM'){//prevent-other-rem-column-values-being-show-up-in-excel
	// 				$emp_array[$cnt-1][$fig_key]=(abs($r->fig_value)+$fig_group_val);//number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
	// 				$sum_array[$fig_key]+=abs($r->fig_value);
	// 			}
				
	// 			if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
	// 				$net_payslip_fig_value+=$r->fig_value;
	// 				$emp_array[$cnt-1]['NETSAL']=$net_payslip_fig_value;//number_format((float)$net_payslip_fig_value, 2, '.', '');
					
	// 				$reg_net_sal=$sum_array['NETSAL']-$rem_net_sal;
	// 				$sum_array['NETSAL']=($reg_net_sal+$net_payslip_fig_value);
	// 				$rem_net_sal = $net_payslip_fig_value;
					
	// 				/*
	// 				if(($r->epf_payable==1)||($fig_key=='NOPAY')){
	// 					$emp_fig_tottax += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
	// 					$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
	// 					$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
	// 					$rem_tot_fortax = $emp_fig_tottax;
	// 				}
	// 				*/
	// 				if(in_array($r->remuneration_tcsc, $conf_tl)){
	// 					$emp_fig_tottax += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
	// 					$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
	// 					$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
	// 					$rem_tot_fortax = $emp_fig_tottax;
	// 				}
					
	// 				$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
	// 				//if(($r->fig_value>=0)||($fig_key!='EPF8'))
	// 				if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
	// 					$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
	// 					$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
	// 					$rem_tot_earn = $emp_fig_totearn;
	// 				}
					
	// 				if($r->fig_value>=0){
	// 					/*
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
	// 					$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
	// 					$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
	// 					$rem_tot_earn = $emp_fig_totearn;
	// 					*/
	// 					$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
	// 					$emp_array[$cnt-1]['add_other']=$emp_fig_otherearn;//number_format((float)$emp_fig_otherearn, 2, '.', '');
						
						
	// 				}else{
	// 					if($fig_key!='NOPAY'){
	// 						$emp_fig_totlost += $r->fig_value;
	// 						$emp_array[$cnt-1]['tot_ded']=abs($emp_fig_totlost);//number_format((float)abs($emp_fig_totlost), 2, '.', '');
							
	// 						$reg_tot_ded=$sum_array['tot_ded']-$rem_tot_ded;
	// 						$sum_array['tot_ded']=($reg_tot_ded+abs($emp_fig_totlost));
	// 						$rem_tot_ded = abs($emp_fig_totlost);
	// 					}
	// 					$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
	// 					$emp_array[$cnt-1]['ded_other']=$emp_fig_otherlost;//number_format((float)$emp_fig_otherlost, 2, '.', '');
						
	// 					$reg_ded_other=$sum_array['ded_other']-$rem_ded_other;
	// 					$sum_array['ded_other']=($reg_ded_other+$emp_fig_otherlost);
	// 					$rem_ded_other=$emp_fig_otherlost;
	// 				}

	// 			}
				
	// 			if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
	// 				//if($emp_array[$cnt-1]['tot_bnp']==0){
	// 					$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
	// 					$emp_array[$cnt-1]['tot_bnp']=$emp_tot_bnp;//number_format((float)$emp_tot_bnp, 2, '.', '');
						
	// 					$reg_tot_bnp=$sum_array['tot_bnp']-$rem_tot_bnp;
	// 					$sum_array['tot_bnp']=($reg_tot_bnp+$emp_tot_bnp);
	// 					$rem_tot_bnp = $emp_tot_bnp;
	// 				//}
	// 			}
	// 		}
	// 	}
	// 	/**/
	// 	$emp_array[] = $sum_array;
		
	// 	if($request->print_record=='1'){
	// 		$excel_rows[] = array('EPF NO', 'Employee Name', 'Working Day', 'Salary Per Day', 'OT1 Hrs.', 'OT2 Hrs.', 'OT1 Rate', 'OT2 Rate', 'Basic+BRA', 'No Pay Day', 'No Pay Amount', 'TTL For E.P.F.', 'OT1 Amount', 'OT2 Amount', 'Attendance', 'Transport Allowance', 'Production Insentive', 'Gross Salary', 'EPF-8', 'Late Deduction', 'Advance', 'Payee Tax', 'Bank Charges', 'Loan', 'Total Deduction', 'Net Salary', 'EPF-12', 'ETF-3', 'Signature');
	// 		$rpt_row_cnt = 0;//skip-title-row
	// 		foreach($emp_array as $excel_data){
	// 			if($rpt_row_cnt>0){
	// 				$basic_pay=$excel_data['BASIC']+$excel_data['BRA_I']+$excel_data['add_bra2'];
	// 				$emp_daysal=($excel_data['WK_MAX_DAYS']>0)?($basic_pay/$excel_data['WK_MAX_DAYS']):'';//
	// 				$tot_forepf = $excel_data['tot_bnp']-$excel_data['NOPAY'];//$excel_data['tot_fortax'];//
	// 				$normal_ot_rate = !empty($excel_data['OT1DURA'])?($excel_data['OTHRS1']/$excel_data['OT1DURA']):0;
	// 				$double_ot_rate = !empty($excel_data['OT2DURA'])?($excel_data['OTHRS2']/$excel_data['OT2DURA']):0;
					
	// 				$excel_rows[] = array($excel_data['emp_epfno'], $excel_data['emp_first_name'], 
	// 									$excel_data['WK_ACT_DAYS'], $emp_daysal, 
	// 									$excel_data['OT1DURA'], $excel_data['OT2DURA'], 
	// 									$normal_ot_rate, $double_ot_rate,
	// 									$basic_pay, $excel_data['nopay_days'], $excel_data['NOPAY'], 
	// 									$tot_forepf, $excel_data['OTHRS1'], 
	// 									$excel_data['OTHRS2'], $excel_data['ATTBONUS_W'], 
	// 									$excel_data['add_transport'], $excel_data['INCNTV_EMP'],
	// 									$excel_data['tot_earn'], 
	// 									$excel_data['EPF8'], 
	// 									$excel_data['ded_IOU'], 
	// 									$excel_data['sal_adv'], //
	// 									$excel_data['PAYE'], $excel_data['ded_fund_1'], $excel_data['LOAN'], 
	// 									$excel_data['tot_ded'], 
	// 									$excel_data['NETSAL'], 
	// 									$excel_data['EPF12'], $excel_data['ETF3'],
	// 									'' //'...................' // signature
	// 								);
					
	// 			}
				
	// 			$rpt_row_cnt++;
				
	// 		}
			
	// 		Excel::create('PayRegister '.$request->rpt_info, function($excel) use ($excel_rows){
	// 			$excel->setTitle('PayRegister');
	// 			$excel->sheet('SalarySheet', function($sheet) use ($excel_rows){
	// 				$sheet->fromArray($excel_rows, null, 'A1', false, false);
	// 			});
	// 		})->download('xlsx');
	// 	}else if($request->print_record=='2'){
	// 		//$emp_array[] = $sum_array;
	// 		$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
	// 		$customPaper = array(0,0,567.00,1283.80);
			
	// 		ini_set("memory_limit", "999M");
	// 		ini_set("max_execution_time", "999");
			
	// 		$pdf = PDF::loadView('Payroll.payslipProcess.PayRegister_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'))
	// 			->setPaper('legal', 'landscape');//->setPaper($customPaper, 'landscape');
			
	// 		return $pdf->download('pay-register.pdf');
	// 		/*
	// 		var_dump($emp_array);
	// 		*/
	// 	}
    // }

	public function reportSalarySheet()
    {
		
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('AuditReports.audit_salary_sheet',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }

	public function checkPayslipList(Request $request){
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
			
			$sqlslip = "
					SELECT 
						drv_emp.emp_payslip_id, 
						drv_emp.emp_first_name, 
						drv_emp.emp_etfno, 
						drv_emp.location, 
						drv_emp.payslip_held, 
						drv_emp.payslip_approved, 
						drv_info.fig_group_title, 
						ABS(drv_info.fig_value) AS fig_value,
						drv_info.fig_base_ratio
					FROM 
					(
						SELECT 
							employee_payslips.id AS emp_payslip_id, 
							employees.emp_name_with_initial AS emp_first_name, 
							employees.emp_etfno, 
							companies.name AS location, 
							employee_payslips.payslip_held, 
							employee_payslips.payslip_approved 
						FROM 
							`employee_payslips`
							INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id
							INNER JOIN employees ON payroll_profiles.emp_id = employees.id
							INNER JOIN companies ON employees.emp_company = companies.id
						WHERE 
							employee_payslips.payment_period_id = ?
							AND employees.emp_company = ?
							AND employees.emp_department = ?
							AND employee_payslips.payslip_cancel = 0
					) AS drv_emp

					INNER JOIN 
					(
						SELECT 
							employee_salary_payments.employee_payslip_id,
							employee_salary_payments.fig_group_title, 
							SUM(employee_salary_payments.fig_value) AS fig_value,
							MAX(employee_salary_payments.fig_base_ratio) AS fig_base_ratio
						FROM 
							employee_salary_payments
						WHERE 
							employee_salary_payments.payment_period_id = ?
						GROUP BY 
							employee_salary_payments.employee_payslip_id, 
							employee_salary_payments.fig_group_title

						UNION ALL

						SELECT 
							employee_salary_payments.employee_payslip_id,
							employee_salary_payments.fig_group AS fig_group_title, 
							SUM(employee_salary_payments.fig_value) AS fig_value,
							MAX(employee_salary_payments.fig_base_ratio) AS fig_base_ratio
						FROM 
							employee_salary_payments
						WHERE 
							employee_salary_payments.payment_period_id = ?
							AND employee_salary_payments.fig_group IN ('OTHRS1', 'OTHRS2')
						GROUP BY 
							employee_salary_payments.employee_payslip_id, 
							employee_salary_payments.fig_group
					) AS drv_info 
					ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id

					ORDER BY 
						drv_info.employee_payslip_id
					";
			/*
			
			*/
			$employee = DB::select($sqlslip, [$payment_period_id, 
											  $request->location_filter_id, $request->department_filter_id, 
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
				
				if ($r->fig_group_title == 'OTHRS1') {
					// Insert your custom value for OTHRS1
					// <--- you set your custom logic or value here
					$total_audit_ot = DB::table('audit_attendance')
						->where('emp_id', $r->emp_etfno)
						->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
						->sum('audit_ot_count');
						
						$other1=($total_audit_ot*$r->fig_base_ratio);

					$employee_list[$cnt - 1]['OTHRS1'] = number_format((float)$other1, 4, '.', '');
				} else {
					$employee_list[$cnt - 1][$r->fig_group_title] = number_format((float)$r->fig_value, 4, '.', '');
				}
			}
			
			return response()->json(['employee_detail'=>$employee_list, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}

	// public function downloadSalarySheet(Request $request){
        
    //     $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
	// 	$payment_period_id=$paymentPeriod->id;//1;
	// 	$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
	// 	$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
	// 	/*
	// 	$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payment_period_id=? AND employees.emp_location=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
	// 	*/
	// 	$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.emp_designation, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_nopaydays FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, job_titles.title AS emp_designation, companies.name AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, sum(nopay_days) AS emp_nopaydays from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
		
	// 	$emp_data = DB::select($sqlslip, [$payment_period_id, 
	// 									  $request->rpt_location_id, $request->rpt_dept_id,
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
				
	// 			$total_audit_ot = DB::table('audit_attendance')
	// 			->where('emp_id', $r->emp_epfno)
	// 			->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 			->sum('audit_ot_count');
				
	// 			$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'emp_designation'=>$r->emp_designation, 'Office'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTAMT1'=>$r->emp_otamt1, 'OTAMT2'=>$r->emp_otamt2, 'NOPAYCNT'=>$r->emp_nopaydays, 'OTHRS1'=>$total_audit_ot, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
				
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
		
	// 	$pdf = PDF::loadView('AuditReports.auditsalary_sheetpdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'));
    //     return $pdf->download('salary-list.pdf');
	// 	//return view('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info'));
    // }

	// //Pay Register Download
    // public function downloadPayAuditRegister(Request $request){
        
    //     $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
	// 	$payment_period_id=$paymentPeriod->id;//1;
	// 	$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
	// 	$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
	// 	$emp_location_col = '1';
	// 	$emp_department_col = '2';
	// 	$emp_location_val = '1';
	// 	$emp_department_val = '2';
		
	// 	if(!empty($request->rpt_location_id)){
	// 		$emp_location_col = "employees.emp_company";//"employees.emp_location";
	// 		$emp_location_val = $request->rpt_location_id;
	// 	}
	// 	if(!empty($request->rpt_dept_id)){
	// 		$emp_department_col = "employees.emp_department";
	// 		$emp_department_val = $request->rpt_dept_id;
	// 	}
		
	// 	// $sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_rates.ot1dura, drv_rates.ot2dura, drv_rates.wk_days, drv_rates.abhrs, drv_rates.nopay_days, IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved, payroll_profiles.payroll_act_id FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp ";
	// 	// $sqlslip.="INNER JOIN job_categories ON drv_emp.payroll_act_id=job_categories.id ";
	// 	// $sqlslip.="INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) as ot1dura, sum(double_rate_otwork_hrs) as ot2dura, SUM(work_days) as wk_days, 0 as abhrs, SUM(nopay_days) AS nopay_days from employee_paid_rates where date_format(concat(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (date_format(?, '%Y-%m'), date_format(?, '%Y-%m')) group by employee_payslip_id) AS drv_rates ON drv_emp.emp_payslip_id=drv_rates.employee_payslip_id ";
	// 	// $sqlslip.="INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_emp.emp_epfno, drv_info.fig_id";
		
	// 	$sqlslip = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.emp_id,  drv_emp.emp_etfno AS emp_epfno, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_emp.payroll_profile_id, drv_rates.ot1dura, drv_rates.ot2dura, drv_rates.wk_days, drv_rates.abhrs, drv_rates.nopay_days, IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value,  drv_info.fig_base_ratio AS fig_base_ratio, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc 
	// 	FROM (
	// 		SELECT employee_payslips.id AS emp_payslip_id, payroll_profiles.emp_id AS emp_id,  employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved, payroll_profiles.payroll_act_id, employee_payslips.payroll_profile_id 
	// 		FROM `employee_payslips` 
	// 		INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id 
	// 		INNER JOIN employees ON payroll_profiles.emp_id = employees.id 
	// 		INNER JOIN companies ON employees.emp_company = companies.id 
	// 		WHERE employee_payslips.payment_period_id = ? 
	// 		  AND employees.emp_company = ? 
	// 		  AND " . $emp_department_col . " = ? 
	// 		  AND employee_payslips.payslip_cancel = 0
	// 	) AS drv_emp 
	// 	INNER JOIN job_categories ON drv_emp.payroll_act_id = job_categories.id 
	// 	INNER JOIN (
	// 		SELECT employee_payslip_id, 
	// 			   SUM(normal_rate_otwork_hrs) AS ot1dura, 
	// 			   SUM(double_rate_otwork_hrs) AS ot2dura, 
	// 			   SUM(work_days) AS wk_days, 
	// 			   0 AS abhrs, 
	// 			   SUM(nopay_days) AS nopay_days 
	// 		FROM employee_paid_rates 
	// 		WHERE DATE_FORMAT(CONCAT(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (
	// 			DATE_FORMAT(?, '%Y-%m'), 
	// 			DATE_FORMAT(?, '%Y-%m')
	// 		) 
	// 		GROUP BY employee_payslip_id
	// 	) AS drv_rates ON drv_emp.emp_payslip_id = drv_rates.employee_payslip_id 
	// 	INNER JOIN (
	// 		SELECT `id` AS fig_id, 
	// 			   `employee_payslip_id`, 
	// 			   `fig_group_title`, 
	// 			   `fig_group`, 
	// 			   `epf_payable`, 
	// 			   remuneration_payslip_spec_code AS remuneration_pssc, 
	// 			   remuneration_taxcalc_spec_code AS remuneration_tcsc, 
	// 			   `fig_value` AS fig_value,
	// 			   `fig_base_ratio` AS fig_base_ratio 
	// 		FROM employee_salary_payments 
	// 		WHERE `payment_period_id` = ?
	// 	) AS drv_info ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id 
	// 	ORDER BY drv_emp.emp_etfno, drv_info.fig_id";
		
	// 	$emp_data = DB::select($sqlslip, [$payment_period_id, 
	// 									  $request->rpt_location_id, $emp_department_val,
	// 									  $payment_period_fr, $payment_period_to,
	// 									  $payment_period_id]
	// 						   );

							
  
	// 	$sect_name = $request->rpt_dept_name;
	// 	$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
	// 	/*
	// 	$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Before Nopay', 'Arrears', 'Total for Tax', 'Attendance', 'Transport', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'EPF-8', 'Salary Advance', 'Telephone', 'IOU', 'Funeral Fund', 'Other Deductions', 'PAYE', 'Loans', 'Total Deductions', 'Balance Pay');
	// 	*/
	// 	$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
	// 	/*
	// 	$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
	// 	*/
	// 	$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>0, 'OTHRS2'=>0, 'OT2DURA'=>0, 'WK_ACT_DAYS'=>0, 'WK_MAX_DAYS'=>0, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
	// 	$cnt = 1;
	// 	$act_payslip_id = '';
	// 	$net_payslip_fig_value = 0;
	// 	$emp_fig_totearn = 0;
	// 	$emp_fig_otherearn = 0; //other-additions
	// 	$emp_fig_totlost = 0;
	// 	$emp_fig_otherlost = 0; //other-deductions
	// 	$emp_fig_tottax = 0;
		
	// 	$rem_tot_bnp = 0;
	// 	$rem_tot_fortax = 0;
	// 	$rem_tot_earn = 0;
	// 	$rem_tot_ded = 0;
	// 	$rem_net_sal = 0;
	// 	$rem_ded_other = 0;
		
	// 	//2023-11-07
	// 	//keys-selected-to-calc-paye-updated-from-remuneration-taxation
	// 	$conf_tl = RemunerationTaxation::where(['fig_calc_opt'=>'FIGPAYE', 'optspec_cancel'=>0])
	// 					->pluck('taxcalc_spec_code')->toArray(); //var_dump($conf_tl);
	// 	//return response()->json($conf_tl);
	// 	//-2023-11-07
	// 	// dd($emp_data);
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
				
	// 			$total_audit_ot = DB::table('audit_attendance')
	// 				->where('employee_id', $r->emp_id)
	// 				->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 				->sum('audit_ot_count');

	// 				$totot1=($r->fig_base_ratio*$total_audit_ot);

				
	// 			$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>$r->nopay_days, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>$total_audit_ot, 'OTHRS2'=>0, 'OT2DURA'=>$r->ot2dura, 'WK_ACT_DAYS'=>$r->wk_days, 'WK_MAX_DAYS'=>$r->emp_payroll_workdays, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
	// 			$rem_tot_bnp = 0;
	// 			$rem_tot_fortax = 0;
	// 			$rem_tot_earn = 0;
	// 			$rem_tot_ded = 0;
	// 			$rem_net_sal = 0;
	// 			$rem_ded_other = 0;
	// 		}
			
			
	// 		$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
	
	// 		if (isset($emp_array[$cnt - 1][$fig_key])) {
	// 			$fig_group_val = $emp_array[$cnt - 1][$fig_key];
			
	// 			// Handle special case for OTHRS1
				
				
	// 			if ($fig_key == 'OTHRS1') {

	// 				$total_audit_ot = DB::table('audit_attendance')
	// 					->where('employee_id', $r->emp_id)
	// 					->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 					->sum('audit_ot_count');
				
	// 				$totot1 = $r->fig_base_ratio * $total_audit_ot;
				
	// 				// Set customized values
	// 				$emp_array[$cnt - 1][$fig_key] = $totot1;
	// 				$emp_array[$cnt - 1][$fig_key . '_base_ratio'] = $r->fig_base_ratio;
				
	// 				//Add to NETSAL
	// 				$net_payslip_fig_value += $r->fig_value;
	// 				$emp_array[$cnt - 1]['NETSAL'] = $net_payslip_fig_value;
			
	// 				$reg_net_sal = $sum_array['NETSAL'] - $rem_net_sal;
	// 				$sum_array['NETSAL'] = $reg_net_sal + $net_payslip_fig_value;
	// 				$rem_net_sal = $net_payslip_fig_value;

	// 				//dd($net_payslip_fig_value);
					
	// 			}
	// 			else {
	// 				// For all other keys, accumulate values normally
	// 				if ($fig_key != 'OTHER_REM') {
	// 					$emp_array[$cnt - 1][$fig_key] = abs($r->fig_value) + $fig_group_val;
	// 					$sum_array[$fig_key] += abs($r->fig_value);
	// 				}
	// 			}
			
	// 			// NETSAL Calculation
	// 			if (!in_array($r->fig_group_title, ['EPF12', 'ETF3','OTHRS'])) {
	// 				$net_payslip_fig_value += $r->fig_value;
	// 				$emp_array[$cnt - 1]['NETSAL'] = $net_payslip_fig_value;
			
	// 				$reg_net_sal = $sum_array['NETSAL'] - $rem_net_sal;
	// 				$sum_array['NETSAL'] = $reg_net_sal + $net_payslip_fig_value;
	// 				$rem_net_sal = $net_payslip_fig_value;
			
	// 				// TAXABLE Check
	// 				if (in_array($r->remuneration_tcsc, $conf_tl)) {
	// 					$emp_fig_tottax += $r->fig_value;
	// 					$emp_array[$cnt - 1]['tot_fortax'] = $emp_fig_tottax;
			
	// 					$reg_tot_fortax = $sum_array['tot_fortax'] - $rem_tot_fortax;
	// 					$sum_array['tot_fortax'] = $reg_tot_fortax + $emp_fig_tottax;
	// 					$rem_tot_fortax = $emp_fig_tottax;
	// 				}
			
	// 				// EARNING / DEDUCTION Handling
	// 				$fig_otherrem = ($fig_key == 'OTHER_REM') ? 1 : 0;
			
	// 				if ((($r->fig_value >= 0) && ($fig_key != 'EPF8')) || ($fig_key == 'NOPAY')) {
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt - 1]['tot_earn'] = $emp_fig_totearn;
			
	// 					$reg_tot_earn = $sum_array['tot_earn'] - $rem_tot_earn;
	// 					$sum_array['tot_earn'] = $reg_tot_earn + $emp_fig_totearn;
	// 					$rem_tot_earn = $emp_fig_totearn;
	// 				}
			
	// 				if ($r->fig_value >= 0) {
	// 					$emp_fig_otherearn += ($r->fig_value * $fig_otherrem);
	// 					$emp_array[$cnt - 1]['add_other'] = $emp_fig_otherearn;
	// 				} else {
	// 					if ($fig_key != 'NOPAY') {
	// 						$emp_fig_totlost += $r->fig_value;
	// 						$emp_array[$cnt - 1]['tot_ded'] = abs($emp_fig_totlost);
			
	// 						$reg_tot_ded = $sum_array['tot_ded'] - $rem_tot_ded;
	// 						$sum_array['tot_ded'] = $reg_tot_ded + abs($emp_fig_totlost);
	// 						$rem_tot_ded = abs($emp_fig_totlost);
	// 					}
			
	// 					$emp_fig_otherlost += abs($r->fig_value) * $fig_otherrem;
	// 					$emp_array[$cnt - 1]['ded_other'] = $emp_fig_otherlost;
			
	// 					$reg_ded_other = $sum_array['ded_other'] - $rem_ded_other;
	// 					$sum_array['ded_other'] = $reg_ded_other + $emp_fig_otherlost;
	// 					$rem_ded_other = $emp_fig_otherlost;
	// 				}
	// 			}
			
	// 			// BNP Group Total Calculation
	// 			if (in_array($fig_key, ['BASIC', 'BRA_I', 'add_bra2'])) {
	// 				$emp_tot_bnp = (
	// 					($emp_array[$cnt - 1]['BASIC'] ?? 0) +
	// 					($emp_array[$cnt - 1]['BRA_I'] ?? 0) +
	// 					($emp_array[$cnt - 1]['add_bra2'] ?? 0)
	// 				);
	// 				$emp_array[$cnt - 1]['tot_bnp'] = $emp_tot_bnp;
			
	// 				$reg_tot_bnp = $sum_array['tot_bnp'] - $rem_tot_bnp;
	// 				$sum_array['tot_bnp'] = $reg_tot_bnp + $emp_tot_bnp;
	// 				$rem_tot_bnp = $emp_tot_bnp;
	// 			}
	// 		}
			
	// 	}
	// 	/**/
		
	// 	$emp_array[] = $sum_array;
		
	// 	if($request->print_record=='1'){
	// 		$excel_rows[] = array('EPF NO', 'Employee Name', 'Working Day', 'Salary Per Day', 'OT1 Hrs.', 'OT2 Hrs.', 'OT1 Rate', 'OT2 Rate', 'Basic+BRA', 'No Pay Day', 'No Pay Amount', 'TTL For E.P.F.', 'OT1 Amount', 'OT2 Amount', 'Attendance', 'Transport Allowance', 'Production Insentive', 'Gross Salary', 'EPF-8', 'Late Deduction', 'Advance', 'Payee Tax', 'Bank Charges', 'Loan', 'Total Deduction', 'Net Salary', 'EPF-12', 'ETF-3', 'Signature');
	// 		$rpt_row_cnt = 0;//skip-title-row
	// 		foreach($emp_array as $excel_data){
				
	// 			if($rpt_row_cnt>0){
	// 				$basic_pay=$excel_data['BASIC']+$excel_data['BRA_I']+$excel_data['add_bra2'];
	// 				$emp_daysal=($excel_data['WK_MAX_DAYS']>0)?($basic_pay/$excel_data['WK_MAX_DAYS']):'';//
	// 				$tot_forepf = $excel_data['tot_bnp']-$excel_data['NOPAY'];//$excel_data['tot_fortax'];//
	// 				$normal_ot_rate = !empty($excel_data['OT1DURA'])?($excel_data['OTHRS1']/$excel_data['OT1DURA']):0;
	// 				$double_ot_rate = !empty($excel_data['OT2DURA'])?($excel_data['OTHRS2']/$excel_data['OT2DURA']):0;
					
	// 				$excel_rows[] = array($excel_data['emp_epfno'], $excel_data['emp_first_name'], 
	// 									$excel_data['WK_ACT_DAYS'], $emp_daysal, 
	// 									$excel_data['OT1DURA'], $excel_data['OT2DURA'], 
	// 									$normal_ot_rate, $double_ot_rate,
	// 									$basic_pay, $excel_data['nopay_days'], $excel_data['NOPAY'], 
	// 									$tot_forepf, $excel_data['OTHRS1'], 
	// 									$excel_data['OTHRS2'], $excel_data['ATTBONUS_W'], 
	// 									$excel_data['add_transport'], $excel_data['INCNTV_EMP'],
	// 									$excel_data['tot_earn'], 
	// 									$excel_data['EPF8'], 
	// 									$excel_data['ded_IOU'], 
	// 									$excel_data['sal_adv'], //
	// 									$excel_data['PAYE'], $excel_data['ded_fund_1'], $excel_data['LOAN'], 
	// 									$excel_data['tot_ded'], 
	// 									$excel_data['NETSAL'], 
	// 									$excel_data['EPF12'], $excel_data['ETF3'],
	// 									'' //'...................' // signature
	// 								);
					
	// 			}
				
	// 			$rpt_row_cnt++;
				
	// 		}
			
	// 		Excel::create('PayRegister '.$request->rpt_info, function($excel) use ($excel_rows){
	// 			$excel->setTitle('PayRegister');
	// 			$excel->sheet('SalarySheet', function($sheet) use ($excel_rows){
	// 				$sheet->fromArray($excel_rows, null, 'A1', false, false);
	// 			});
	// 		})->download('xlsx');
	// 	}else if($request->print_record=='2'){
	// 		//$emp_array[] = $sum_array;
	// 		$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
	// 		$customPaper = array(0,0,567.00,1283.80);
			
	// 		ini_set("memory_limit", "999M");
	// 		ini_set("max_execution_time", "999");
			
	// 		$pdf = PDF::loadView('Payroll.payslipProcess.PayRegister_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'))
	// 			->setPaper('legal', 'landscape');//->setPaper($customPaper, 'landscape');
				
	// 		return $pdf->download('pay-register.pdf');
			
			
			
	// 	}
    // }
	// //Pay Register Download End
	
	// // Audit Employee Salary Sheets Start
	// public function downloadSalarySheet(Request $request){
        
    //     $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
	// 	$payment_period_id=$paymentPeriod->id;//1;
	// 	$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
	// 	$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
	// 	/*
	// 	$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payment_period_id=? AND employees.emp_location=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
	// 	*/
	// 	//$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.emp_designation, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_nopaydays FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, job_titles.title AS emp_designation, companies.name AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, sum(nopay_days) AS emp_nopaydays from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
		

	// 	$sqlslip="SELECT 
	// 		drv_emp.emp_payslip_id, 
	// 		drv_emp.emp_etfno AS emp_epfno, 
	// 		drv_emp.emp_first_name, 
	// 		drv_emp.emp_designation, 
	// 		drv_emp.location, 
	// 		drv_info.fig_group, 
	// 		drv_info.fig_group_title, 
	// 		drv_info.fig_value AS fig_value, 
	// 		drv_info.fig_base_ratio AS fig_base_ratio, 
	// 		drv_info.epf_payable AS epf_payable, 
	// 		drv_info.remuneration_pssc, 
	// 		drv_info.remuneration_tcsc, 
	// 		drv_catinfo.emp_otamt1, 
	// 		drv_catinfo.emp_otamt2, 
	// 		drv_catinfo.emp_nopaydays 
	// 	FROM (
	// 		SELECT 
	// 			employee_payslips.id AS emp_payslip_id, 
	// 			employees.emp_id AS emp_epfno, 
	// 			employees.emp_etfno, 
	// 			employees.emp_name_with_initial AS emp_first_name, 
	// 			job_titles.title AS emp_designation, 
	// 			companies.name AS location 
	// 		FROM 
	// 			employee_payslips 
	// 			INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id 
	// 			INNER JOIN employees ON payroll_profiles.emp_id = employees.id 
	// 			INNER JOIN companies ON employees.emp_company = companies.id 
	// 			LEFT OUTER JOIN job_titles ON employees.emp_job_code = job_titles.id 
	// 		WHERE 
	// 			employee_payslips.payment_period_id = ? 
	// 			AND employees.emp_company = ? 
	// 			AND employees.emp_department = ? 
	// 			AND employee_payslips.payslip_cancel = 0 
	// 			AND employees.emp_id NOT IN (195, 201)
	// 	) AS drv_emp 
	// 	INNER JOIN (
	// 		SELECT 
	// 			employee_payslip_id, 
	// 			SUM(normal_rate_otwork_hrs) AS emp_otamt1, 
	// 			SUM(double_rate_otwork_hrs) AS emp_otamt2, 
	// 			SUM(nopay_days) AS emp_nopaydays 
	// 		FROM 
	// 			employee_paid_rates 
	// 		GROUP BY 
	// 			employee_payslip_id
	// 	) AS drv_catinfo ON drv_emp.emp_payslip_id = drv_catinfo.employee_payslip_id 
	// 	INNER JOIN (
	// 		SELECT 
	// 			id AS fig_id, 
	// 			employee_payslip_id, 
	// 			fig_group, 
	// 			fig_group_title, 
	// 			epf_payable, 
	// 			remuneration_payslip_spec_code AS remuneration_pssc, 
	// 			remuneration_taxcalc_spec_code AS remuneration_tcsc, 
	// 			fig_value AS fig_value,
	// 			fig_base_ratio AS fig_base_ratio
	// 		FROM 
	// 			employee_salary_payments 
	// 		WHERE 
	// 			payment_period_id = ?
	// 	) AS drv_info ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id 
	// 	ORDER BY 
	// 		drv_info.fig_id;
	// 	";
	// 	$emp_data = DB::select($sqlslip, [$payment_period_id, 
	// 									  $request->rpt_location_id, $request->rpt_dept_id,
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
	// 	// dd($emp_data);
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
				
	// 			$total_audit_ot = DB::table('audit_attendance')
	// 			->where('emp_id', $r->emp_epfno)
	// 			->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 			->sum('audit_ot_count');
				
	// 			$other1=($total_audit_ot*$r->fig_base_ratio);
				
	// 			$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'emp_designation'=>$r->emp_designation, 'Office'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTAMT1'=>$total_audit_ot, 'OTAMT2'=>$r->emp_otamt2, 'NOPAYCNT'=>$r->emp_nopaydays, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
				
	// 		}
			
	// 		// Custom override for specific fig_group
	// 		if ($r->fig_group == 'OTHRS1') {
	// 			$total_audit_ot = DB::table('audit_attendance')
	// 			->where('emp_id', $r->emp_epfno)
	// 			->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 			->sum('audit_ot_count');
				
	// 			$other1=($total_audit_ot*$r->fig_base_ratio);
	// 			$r->fig_value = $other1;    // ✅ Replace with your custom logic or value
	// 			//$r->emp_otamt1 = 55.5;       // ✅ Replace with your custom logic or value
	// 		}

	// 		// Determine the key to be used for the current figure
	// 		$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title]) ? $r->fig_group_title : $r->remuneration_pssc;

	// 		if (isset($emp_array[$cnt-1][$fig_key])) {
	// 			// Get the current value for this key (if already present)
	// 			$fig_group_val = $emp_array[$cnt-1][$fig_key];

	// 			// Update the current figure by adding the absolute value of the new one
	// 			$emp_array[$cnt-1][$fig_key] = number_format((float)(abs($r->fig_value) + $fig_group_val), 2, '.', '');

	// 			// Skip EPF12 and ETF3 for net salary/tax/earnings calculations
	// 			if (!(($r->fig_group_title == 'EPF12') || ($r->fig_group_title == 'ETF3') || ($r->fig_group_title == 'OTHRS'))) {

	// 				// Update net salary with the figure value (can be + or -)
	// 				$net_payslip_fig_value += $r->fig_value;
	// 				$emp_array[$cnt-1]['NETSAL'] = number_format((float)$net_payslip_fig_value, 2, '.', '');

	// 				// If figure is taxable (e.g., EPF Payable or NOPAY), add to total for tax
	// 				if (($r->epf_payable == 1) || ($fig_key == 'NOPAY')) {
	// 					$emp_fig_tottax += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_fortax'] = number_format((float)$emp_fig_tottax, 2, '.', '');
	// 				}

	// 				// Check if figure is classified under OTHER_REM group
	// 				$fig_otherrem = ($fig_key == 'OTHER_REM') ? 1 : 0;

	// 				// Add to total earnings if positive (and not EPF8) or if NOPAY
	// 				if ((($r->fig_value >= 0) && ($fig_key != 'EPF8')) || ($fig_key == 'NOPAY')) {
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_earn'] = number_format((float)$emp_fig_totearn, 2, '.', '');
	// 				}

	// 				if ($r->fig_value >= 0) {
	// 					// Add to "other earnings" only if marked as OTHER_REM
	// 					$emp_fig_otherearn += ($r->fig_value * $fig_otherrem);
	// 					$emp_array[$cnt-1]['add_other'] = number_format((float)$emp_fig_otherearn, 2, '.', '');
	// 				} else {
	// 					// Handle negative figures (deductions)
	// 					if ($fig_key != 'NOPAY') {
	// 						$emp_fig_totlost += $r->fig_value; // This will be negative
	// 						$emp_array[$cnt-1]['tot_ded'] = number_format((float)abs($emp_fig_totlost), 2, '.', '');
	// 					}

	// 					// Add to "other deductions" if marked as OTHER_REM
	// 					$emp_fig_otherlost += (abs($r->fig_value) * $fig_otherrem);
	// 					$emp_array[$cnt-1]['ded_other'] = number_format((float)$emp_fig_otherlost, 2, '.', '');
	// 				}
	// 			}

	// 			// If figure is one of the base salary components, calculate total basic + allowances
	// 			if (($fig_key == 'BASIC') || ($fig_key == 'BRA_I') || ($fig_key == 'add_bra2')) {
	// 				$emp_tot_bnp = (
	// 					$emp_array[$cnt-1]['BASIC'] +
	// 					$emp_array[$cnt-1]['BRA_I'] +
	// 					$emp_array[$cnt-1]['add_bra2']
	// 				);
	// 				$emp_array[$cnt-1]['tot_bnp'] = number_format((float)$emp_tot_bnp, 2, '.', '');
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
		
	// 	$pdf = PDF::loadView('AuditReports.auditsalary_sheetpdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'));
    //     return $pdf->download('salary-list.pdf');
	// 	//return view('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info'));
    // }
	// // Audit Employee Salary Sheets End

	// //Pay Register Download

    // public function downloadPayAuditRegister(Request $request){
        
    //     $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
	// 	$payment_period_id=$paymentPeriod->id;//1;
	// 	$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
	// 	$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		
	// 	$emp_location_col = '1';
	// 	$emp_department_col = '2';
	// 	$emp_location_val = '1';
	// 	$emp_department_val = '2';
		
	// 	if(!empty($request->rpt_location_id)){
	// 		$emp_location_col = "employees.emp_company";//"employees.emp_location";
	// 		$emp_location_val = $request->rpt_location_id;
	// 	}
	// 	if(!empty($request->rpt_dept_id)){
	// 		$emp_department_col = "employees.emp_department";
	// 		$emp_department_val = $request->rpt_dept_id;
	// 	}
		
	// 	// $sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_rates.ot1dura, drv_rates.ot2dura, drv_rates.wk_days, drv_rates.abhrs, drv_rates.nopay_days, IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved, payroll_profiles.payroll_act_id FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp ";
	// 	// $sqlslip.="INNER JOIN job_categories ON drv_emp.payroll_act_id=job_categories.id ";
	// 	// $sqlslip.="INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) as ot1dura, sum(double_rate_otwork_hrs) as ot2dura, SUM(work_days) as wk_days, 0 as abhrs, SUM(nopay_days) AS nopay_days from employee_paid_rates where date_format(concat(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (date_format(?, '%Y-%m'), date_format(?, '%Y-%m')) group by employee_payslip_id) AS drv_rates ON drv_emp.emp_payslip_id=drv_rates.employee_payslip_id ";
	// 	// $sqlslip.="INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_emp.emp_epfno, drv_info.fig_id";
		
	// 	$sqlslip = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.emp_id,  drv_emp.emp_etfno AS emp_epfno, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_emp.payroll_profile_id, drv_rates.ot1dura, drv_rates.ot2dura, drv_rates.wk_days, drv_rates.abhrs, drv_rates.nopay_days, IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value,  drv_info.fig_base_ratio AS fig_base_ratio, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc 
	// 	FROM (
	// 		SELECT employee_payslips.id AS emp_payslip_id, payroll_profiles.emp_id AS emp_id,  employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved, payroll_profiles.payroll_act_id, employee_payslips.payroll_profile_id 
	// 		FROM `employee_payslips` 
	// 		INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id 
	// 		INNER JOIN employees ON payroll_profiles.emp_id = employees.id 
	// 		INNER JOIN companies ON employees.emp_company = companies.id 
	// 		WHERE employee_payslips.payment_period_id = ? 
	// 		  AND employees.emp_company = ? 
	// 		  AND " . $emp_department_col . " = ? 
	// 		  AND employee_payslips.payslip_cancel = 0
	// 		  AND employees.emp_id NOT IN (195, 201)
	// 	) AS drv_emp 
	// 	INNER JOIN job_categories ON drv_emp.payroll_act_id = job_categories.id 
	// 	INNER JOIN (
	// 		SELECT employee_payslip_id, 
	// 			   SUM(normal_rate_otwork_hrs) AS ot1dura, 
	// 			   SUM(double_rate_otwork_hrs) AS ot2dura, 
	// 			   SUM(work_days) AS wk_days, 
	// 			   0 AS abhrs, 
	// 			   SUM(nopay_days) AS nopay_days 
	// 		FROM employee_paid_rates 
	// 		WHERE DATE_FORMAT(CONCAT(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (
	// 			DATE_FORMAT(?, '%Y-%m'), 
	// 			DATE_FORMAT(?, '%Y-%m')
	// 		) 
	// 		GROUP BY employee_payslip_id
	// 	) AS drv_rates ON drv_emp.emp_payslip_id = drv_rates.employee_payslip_id 
	// 	INNER JOIN (
	// 		SELECT `id` AS fig_id, 
	// 			   `employee_payslip_id`, 
	// 			   `fig_group_title`, 
	// 			   `fig_group`, 
	// 			   `epf_payable`, 
	// 			   remuneration_payslip_spec_code AS remuneration_pssc, 
	// 			   remuneration_taxcalc_spec_code AS remuneration_tcsc, 
	// 			   `fig_value` AS fig_value,
	// 			   `fig_base_ratio` AS fig_base_ratio 
	// 		FROM employee_salary_payments 
	// 		WHERE `payment_period_id` = ?
	// 	) AS drv_info ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id 
	// 	ORDER BY drv_emp.emp_etfno, drv_info.fig_id";
		
	// 	$emp_data = DB::select($sqlslip, [$payment_period_id, 
	// 									  $request->rpt_location_id, $emp_department_val,
	// 									  $payment_period_fr, $payment_period_to,
	// 									  $payment_period_id]
	// 						   );

							
  
	// 	$sect_name = $request->rpt_dept_name;
	// 	$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
	// 	/*
	// 	$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Before Nopay', 'Arrears', 'Total for Tax', 'Attendance', 'Transport', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'EPF-8', 'Salary Advance', 'Telephone', 'IOU', 'Funeral Fund', 'Other Deductions', 'PAYE', 'Loans', 'Total Deductions', 'Balance Pay');
	// 	*/
	// 	$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
	// 	/*
	// 	$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
	// 	*/
	// 	$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>0, 'OTHRS2'=>0, 'OT2DURA'=>0, 'WK_ACT_DAYS'=>0, 'WK_MAX_DAYS'=>0, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
	// 	$cnt = 1;
	// 	$act_payslip_id = '';
	// 	$net_payslip_fig_value = 0;
	// 	$emp_fig_totearn = 0;
	// 	$emp_fig_otherearn = 0; //other-additions
	// 	$emp_fig_totlost = 0;
	// 	$emp_fig_otherlost = 0; //other-deductions
	// 	$emp_fig_tottax = 0;
		
	// 	$rem_tot_bnp = 0;
	// 	$rem_tot_fortax = 0;
	// 	$rem_tot_earn = 0;
	// 	$rem_tot_ded = 0;
	// 	$rem_net_sal = 0;
	// 	$rem_ded_other = 0;
		
	// 	//2023-11-07
	// 	//keys-selected-to-calc-paye-updated-from-remuneration-taxation
	// 	$conf_tl = RemunerationTaxation::where(['fig_calc_opt'=>'FIGPAYE', 'optspec_cancel'=>0])
	// 					->pluck('taxcalc_spec_code')->toArray(); //var_dump($conf_tl);
	// 	//return response()->json($conf_tl);
	// 	//-2023-11-07
	// 	// dd($emp_data);
	// 	foreach($emp_data as $r){
	// 		$total_audit_ot = DB::table('audit_attendance')
	// 				->where('employee_id', $r->emp_id)
	// 				->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 				->sum('audit_ot_count');

	// 		$totot1=($r->fig_base_ratio*$total_audit_ot);


	// 		if($act_payslip_id!=$r->emp_payslip_id){
	// 			$cnt++;
	// 			$act_payslip_id=$r->emp_payslip_id;
	// 			$net_payslip_fig_value = 0;
	// 			$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
	// 			$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
	// 			$emp_fig_tottax = 0;
	// 		}
	// 		if(!isset($emp_array[$cnt-1])){
				
				
				
	// 			$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>$r->nopay_days, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>$total_audit_ot, 'OTHRS2'=>0, 'OT2DURA'=>$r->ot2dura, 'WK_ACT_DAYS'=>$r->wk_days, 'WK_MAX_DAYS'=>$r->emp_payroll_workdays, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
	// 			$rem_tot_bnp = 0;
	// 			$rem_tot_fortax = 0;
	// 			$rem_tot_earn = 0;
	// 			$rem_tot_ded = 0;
	// 			$rem_net_sal = 0;
	// 			$rem_ded_other = 0;
	// 		}
			
			
	// 		$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
	
	// 		if (isset($emp_array[$cnt - 1][$fig_key])) {
	// 			$fig_group_val = $emp_array[$cnt - 1][$fig_key];
			
	// 			// Handle special case for OTHRS1
				
				
				
	// 				// For all other keys, accumulate values normally
	// 				if ($fig_key != 'OTHER_REM' && $fig_key !='OTHRS1') {
	// 					$emp_array[$cnt - 1][$fig_key] = abs($r->fig_value) + $fig_group_val;
	// 					$sum_array[$fig_key] += abs($r->fig_value);
	// 				}
	// 				else if($fig_key =='OTHRS1'){					
				
						
	// 					$emp_array[$cnt - 1][$fig_key] = abs($totot1) + $fig_group_val;
	// 					// $emp_array[$cnt - 1][$fig_key] = $totot1;
	// 					// $emp_array[$cnt - 1][$fig_key . '_base_ratio'] = $r->fig_base_ratio;
	// 					$sum_array[$fig_key] += abs($totot1);
						
	// 				}
				
			
	// 			// NETSAL Calculation
	// 			if (!in_array($r->fig_group_title, ['EPF12', 'ETF3', 'OTHRS'])) {
	// 				$net_payslip_fig_value += $r->fig_value;
	// 				$emp_array[$cnt - 1]['NETSAL'] = $net_payslip_fig_value;
			
	// 				$reg_net_sal = $sum_array['NETSAL'] - $rem_net_sal;
	// 				$sum_array['NETSAL'] = $reg_net_sal + $net_payslip_fig_value;
	// 				$rem_net_sal = $net_payslip_fig_value;
			
	// 				// TAXABLE Check
	// 				if (in_array($r->remuneration_tcsc, $conf_tl)) {
	// 					$emp_fig_tottax += $r->fig_value;
	// 					$emp_array[$cnt - 1]['tot_fortax'] = $emp_fig_tottax;
			
	// 					$reg_tot_fortax = $sum_array['tot_fortax'] - $rem_tot_fortax;
	// 					$sum_array['tot_fortax'] = $reg_tot_fortax + $emp_fig_tottax;
	// 					$rem_tot_fortax = $emp_fig_tottax;
	// 				}
			
	// 				// EARNING / DEDUCTION Handling
	// 				$fig_otherrem = ($fig_key == 'OTHER_REM') ? 1 : 0;
			
	// 				if ((($r->fig_value >= 0) && ($fig_key != 'EPF8')) || ($fig_key == 'NOPAY')) {
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt - 1]['tot_earn'] = $emp_fig_totearn;
			
	// 					$reg_tot_earn = $sum_array['tot_earn'] - $rem_tot_earn;
	// 					$sum_array['tot_earn'] = $reg_tot_earn + $emp_fig_totearn;
	// 					$rem_tot_earn = $emp_fig_totearn;
	// 				}
			
	// 				if ($r->fig_value >= 0) {
	// 					$emp_fig_otherearn += ($r->fig_value * $fig_otherrem);
	// 					$emp_array[$cnt - 1]['add_other'] = $emp_fig_otherearn;
	// 				} else {
	// 					if ($fig_key != 'NOPAY') {
	// 						$emp_fig_totlost += $r->fig_value;
	// 						$emp_array[$cnt - 1]['tot_ded'] = abs($emp_fig_totlost);
			
	// 						$reg_tot_ded = $sum_array['tot_ded'] - $rem_tot_ded;
	// 						$sum_array['tot_ded'] = $reg_tot_ded + abs($emp_fig_totlost);
	// 						$rem_tot_ded = abs($emp_fig_totlost);
	// 					}
			
	// 					$emp_fig_otherlost += abs($r->fig_value) * $fig_otherrem;
	// 					$emp_array[$cnt - 1]['ded_other'] = $emp_fig_otherlost;
			
	// 					$reg_ded_other = $sum_array['ded_other'] - $rem_ded_other;
	// 					$sum_array['ded_other'] = $reg_ded_other + $emp_fig_otherlost;
	// 					$rem_ded_other = $emp_fig_otherlost;
	// 				}
	// 			}
	// 			else if($fig_key =='OTHRS1'){

	// 			$net_payslip_fig_value +=  $totot1;
	// 				$emp_array[$cnt - 1]['NETSAL'] = $net_payslip_fig_value;
			
	// 				$reg_net_sal = $sum_array['NETSAL'] - $rem_net_sal;
	// 				$sum_array['NETSAL'] = $reg_net_sal + $net_payslip_fig_value;
	// 				$rem_net_sal = $net_payslip_fig_value;

	// 				$emp_fig_totearn += $totot1;
	// 				$emp_array[$cnt - 1]['tot_earn'] = $emp_fig_totearn;
	// 		}
			
	// 			// BNP Group Total Calculation
	// 			if (in_array($fig_key, ['BASIC', 'BRA_I', 'add_bra2'])) {
	// 				$emp_tot_bnp = (
	// 					($emp_array[$cnt - 1]['BASIC'] ?? 0) +
	// 					($emp_array[$cnt - 1]['BRA_I'] ?? 0) +
	// 					($emp_array[$cnt - 1]['add_bra2'] ?? 0)
	// 				);
	// 				$emp_array[$cnt - 1]['tot_bnp'] = $emp_tot_bnp;
			
	// 				$reg_tot_bnp = $sum_array['tot_bnp'] - $rem_tot_bnp;
	// 				$sum_array['tot_bnp'] = $reg_tot_bnp + $emp_tot_bnp;
	// 				$rem_tot_bnp = $emp_tot_bnp;
	// 			}
	// 		}
			
	// 	}
	// 	/**/
		
	// 	$emp_array[] = $sum_array;
		
	// 	if($request->print_record=='1'){
	// 		$excel_rows[] = array('EPF NO', 'Employee Name', 'Working Day', 'Salary Per Day', 'OT1 Hrs.', 'OT2 Hrs.', 'OT1 Rate', 'OT2 Rate', 'Basic+BRA', 'No Pay Day', 'No Pay Amount', 'TTL For E.P.F.', 'OT1 Amount', 'OT2 Amount', 'Attendance', 'Transport Allowance', 'Production Insentive', 'Gross Salary', 'EPF-8', 'Late Deduction', 'Advance', 'Payee Tax', 'Bank Charges', 'Loan', 'Total Deduction', 'Net Salary', 'EPF-12', 'ETF-3', 'Signature');
	// 		$rpt_row_cnt = 0;//skip-title-row
	// 		foreach($emp_array as $excel_data){
				
	// 			if($rpt_row_cnt>0){
	// 				$basic_pay=$excel_data['BASIC']+$excel_data['BRA_I']+$excel_data['add_bra2'];
	// 				$emp_daysal=($excel_data['WK_MAX_DAYS']>0)?($basic_pay/$excel_data['WK_MAX_DAYS']):'';//
	// 				$tot_forepf = $excel_data['tot_bnp']-$excel_data['NOPAY'];//$excel_data['tot_fortax'];//
	// 				$normal_ot_rate = !empty($excel_data['OT1DURA'])?($excel_data['OTHRS1']/$excel_data['OT1DURA']):0;
	// 				$double_ot_rate = !empty($excel_data['OT2DURA'])?($excel_data['OTHRS2']/$excel_data['OT2DURA']):0;
					
	// 				$excel_rows[] = array($excel_data['emp_epfno'], $excel_data['emp_first_name'], 
	// 									$excel_data['WK_ACT_DAYS'], $emp_daysal, 
	// 									$excel_data['OT1DURA'], $excel_data['OT2DURA'], 
	// 									$normal_ot_rate, $double_ot_rate,
	// 									$basic_pay, $excel_data['nopay_days'], $excel_data['NOPAY'], 
	// 									$tot_forepf, $excel_data['OTHRS1'], 
	// 									$excel_data['OTHRS2'], $excel_data['ATTBONUS_W'], 
	// 									$excel_data['add_transport'], $excel_data['INCNTV_EMP'],
	// 									$excel_data['tot_earn'], 
	// 									$excel_data['EPF8'], 
	// 									$excel_data['ded_IOU'], 
	// 									$excel_data['sal_adv'], //
	// 									$excel_data['PAYE'], $excel_data['ded_fund_1'], $excel_data['LOAN'], 
	// 									$excel_data['tot_ded'], 
	// 									$excel_data['NETSAL'], 
	// 									$excel_data['EPF12'], $excel_data['ETF3'],
	// 									'' //'...................' // signature
	// 								);
					
	// 			}
				
	// 			$rpt_row_cnt++;
				
	// 		}
			
	// 		Excel::create('PayRegister '.$request->rpt_info, function($excel) use ($excel_rows){
	// 			$excel->setTitle('PayRegister');
	// 			$excel->sheet('SalarySheet', function($sheet) use ($excel_rows){
	// 				$sheet->fromArray($excel_rows, null, 'A1', false, false);
	// 			});
	// 		})->download('xlsx');
	// 	}else if($request->print_record=='2'){
	// 		//$emp_array[] = $sum_array;
	// 		$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
	// 		$customPaper = array(0,0,567.00,1283.80);
			
	// 		ini_set("memory_limit", "999M");
	// 		ini_set("max_execution_time", "999");
			
	// 		$pdf = PDF::loadView('Payroll.payslipProcess.PayRegister_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'))
	// 			->setPaper('legal', 'landscape');//->setPaper($customPaper, 'landscape');
				
	// 		return $pdf->download('pay-register.pdf');
			
			
			
	// 	}
    // }
	// //Pay Register Download End



	// // Audit Employee Salary Sheets Start

	// public function downloadSalarySheet(Request $request){
        
    //     $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
	// 	$payment_period_id=$paymentPeriod->id;//1;
	// 	$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
	// 	$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
	// 	/*
	// 	$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payment_period_id=? AND employees.emp_location=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
	// 	*/
	// 	//$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.emp_designation, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_nopaydays FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, job_titles.title AS emp_designation, companies.name AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, sum(nopay_days) AS emp_nopaydays from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
		

	// 	$sqlslip="SELECT 
	// 		drv_emp.emp_payslip_id, 
	// 		drv_emp.emp_etfno AS emp_epfno, 
	// 		drv_emp.emp_first_name, 
	// 		drv_emp.emp_designation, 
	// 		drv_emp.location, 
	// 		drv_info.fig_group, 
	// 		drv_info.fig_group_title, 
	// 		drv_info.fig_value AS fig_value, 
	// 		drv_info.fig_base_ratio AS fig_base_ratio, 
	// 		drv_info.epf_payable AS epf_payable, 
	// 		drv_info.remuneration_pssc, 
	// 		drv_info.remuneration_tcsc, 
	// 		drv_catinfo.emp_otamt1, 
	// 		drv_catinfo.emp_otamt2, 
	// 		drv_catinfo.emp_nopaydays 
	// 	FROM (
	// 		SELECT 
	// 			employee_payslips.id AS emp_payslip_id, 
	// 			employees.emp_id AS emp_epfno, 
	// 			employees.emp_etfno, 
	// 			employees.emp_name_with_initial AS emp_first_name, 
	// 			job_titles.title AS emp_designation, 
	// 			companies.name AS location 
	// 		FROM 
	// 			employee_payslips 
	// 			INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id 
	// 			INNER JOIN employees ON payroll_profiles.emp_id = employees.id 
	// 			INNER JOIN companies ON employees.emp_company = companies.id 
	// 			LEFT OUTER JOIN job_titles ON employees.emp_job_code = job_titles.id 
	// 		WHERE 
	// 			employee_payslips.payment_period_id = ? 
	// 			AND employees.emp_company = ? 
	// 			AND employees.emp_department = ? 
	// 			AND employee_payslips.payslip_cancel = 0 
	// 			AND employees.emp_id NOT IN (195, 201)
	// 	) AS drv_emp 
	// 	INNER JOIN (
	// 		SELECT 
	// 			employee_payslip_id, 
	// 			SUM(normal_rate_otwork_hrs) AS emp_otamt1, 
	// 			SUM(double_rate_otwork_hrs) AS emp_otamt2, 
	// 			SUM(nopay_days) AS emp_nopaydays 
	// 		FROM 
	// 			employee_paid_rates 
	// 		GROUP BY 
	// 			employee_payslip_id
	// 	) AS drv_catinfo ON drv_emp.emp_payslip_id = drv_catinfo.employee_payslip_id 
	// 	INNER JOIN (
	// 		SELECT 
	// 			id AS fig_id, 
	// 			employee_payslip_id, 
	// 			fig_group, 
	// 			fig_group_title, 
	// 			epf_payable, 
	// 			remuneration_payslip_spec_code AS remuneration_pssc, 
	// 			remuneration_taxcalc_spec_code AS remuneration_tcsc, 
	// 			fig_value AS fig_value,
	// 			fig_base_ratio AS fig_base_ratio
	// 		FROM 
	// 			employee_salary_payments 
	// 		WHERE 
	// 			payment_period_id = ?
	// 	) AS drv_info ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id 
	// 	ORDER BY 
	// 		drv_info.fig_id;
	// 	";
	// 	$emp_data = DB::select($sqlslip, [$payment_period_id, 
	// 									  $request->rpt_location_id, $request->rpt_dept_id,
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
	// 	// dd($emp_data);
	// 	foreach($emp_data as $r){
	// 		$total_audit_ot = DB::table('audit_attendance')
	// 			->where('emp_id', $r->emp_epfno)
	// 			->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 			->sum('audit_ot_count');
				
	// 			$other1=($total_audit_ot*$r->fig_base_ratio);

	// 		if($act_payslip_id!=$r->emp_payslip_id){
	// 			$cnt++;
	// 			$act_payslip_id=$r->emp_payslip_id;
	// 			$net_payslip_fig_value = 0;
	// 			$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
	// 			$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
	// 			$emp_fig_tottax = 0;
	// 		}
	// 		if(!isset($emp_array[$cnt-1])){
				
				
				
	// 			$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'emp_designation'=>$r->emp_designation, 'Office'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTAMT1'=>$total_audit_ot, 'OTAMT2'=>$r->emp_otamt2, 'NOPAYCNT'=>$r->emp_nopaydays, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
				
	// 		}
			
	// 		// Custom override for specific fig_group
	// 		if ($r->fig_group == 'OTHRS1') {
	// 			$total_audit_ot = DB::table('audit_attendance')
	// 			->where('emp_id', $r->emp_epfno)
	// 			->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
	// 			->sum('audit_ot_count');
				
	// 			$other1=($total_audit_ot*$r->fig_base_ratio);
	// 			$r->fig_value = $other1;    // ✅ Replace with your custom logic or value
	// 			//$r->emp_otamt1 = 55.5;       // ✅ Replace with your custom logic or value
	// 			$emp_fig_totearn +=  $other1;
	// 			$emp_array[$cnt-1]['tot_earn'] = number_format((float)$emp_fig_totearn, 2, '.', '');
						
	// 			$net_payslip_fig_value += $other1;
	// 			$emp_array[$cnt-1]['NETSAL'] = number_format((float)$net_payslip_fig_value, 2, '.', '');
	// 		}

	// 		// Determine the key to be used for the current figure
	// 		$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title]) ? $r->fig_group_title : $r->remuneration_pssc;

	// 		if (isset($emp_array[$cnt-1][$fig_key])) {
	// 			// Get the current value for this key (if already present)
	// 			$fig_group_val = $emp_array[$cnt-1][$fig_key];

	// 			// Update the current figure by adding the absolute value of the new one
	// 			$emp_array[$cnt-1][$fig_key] = number_format((float)(abs($r->fig_value) + $fig_group_val), 2, '.', '');

	// 			// Skip EPF12 and ETF3 for net salary/tax/earnings calculations
	// 			if (!(($r->fig_group_title == 'EPF12') || ($r->fig_group_title == 'ETF3') || ($r->fig_group_title == 'OTHRS'))) {

	// 				// Update net salary with the figure value (can be + or -)
	// 				$net_payslip_fig_value += $r->fig_value;
	// 				$emp_array[$cnt-1]['NETSAL'] = number_format((float)$net_payslip_fig_value, 2, '.', '');

	// 				// If figure is taxable (e.g., EPF Payable or NOPAY), add to total for tax
	// 				if (($r->epf_payable == 1) || ($fig_key == 'NOPAY')) {
	// 					$emp_fig_tottax += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_fortax'] = number_format((float)$emp_fig_tottax, 2, '.', '');
	// 				}

	// 				// Check if figure is classified under OTHER_REM group
	// 				$fig_otherrem = ($fig_key == 'OTHER_REM') ? 1 : 0;

	// 				// Add to total earnings if positive (and not EPF8) or if NOPAY
	// 				if ((($r->fig_value >= 0) && ($fig_key != 'EPF8')) || ($fig_key == 'NOPAY')) {
	// 					$emp_fig_totearn += $r->fig_value;
	// 					$emp_array[$cnt-1]['tot_earn'] = number_format((float)$emp_fig_totearn, 2, '.', '');
	// 				}

	// 				if ($r->fig_value >= 0) {
	// 					// Add to "other earnings" only if marked as OTHER_REM
	// 					$emp_fig_otherearn += ($r->fig_value * $fig_otherrem);
	// 					$emp_array[$cnt-1]['add_other'] = number_format((float)$emp_fig_otherearn, 2, '.', '');
	// 				} else {
	// 					// Handle negative figures (deductions)
	// 					if ($fig_key != 'NOPAY') {
	// 						$emp_fig_totlost += $r->fig_value; // This will be negative
	// 						$emp_array[$cnt-1]['tot_ded'] = number_format((float)abs($emp_fig_totlost), 2, '.', '');
	// 					}

	// 					// Add to "other deductions" if marked as OTHER_REM
	// 					$emp_fig_otherlost += (abs($r->fig_value) * $fig_otherrem);
	// 					$emp_array[$cnt-1]['ded_other'] = number_format((float)$emp_fig_otherlost, 2, '.', '');
	// 				}
	// 			}

	// 			// If figure is one of the base salary components, calculate total basic + allowances
	// 			if (($fig_key == 'BASIC') || ($fig_key == 'BRA_I') || ($fig_key == 'add_bra2')) {
	// 				$emp_tot_bnp = (
	// 					$emp_array[$cnt-1]['BASIC'] +
	// 					$emp_array[$cnt-1]['BRA_I'] +
	// 					$emp_array[$cnt-1]['add_bra2']
	// 				);
	// 				$emp_array[$cnt-1]['tot_bnp'] = number_format((float)$emp_tot_bnp, 2, '.', '');
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
		
	// 	$pdf = PDF::loadView('AuditReports.auditsalary_sheetpdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'));
    //     return $pdf->download('salary-list.pdf');
	// 	//return view('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info'));
    // }

	// // Audit Employee Salary Sheets End

	//Pay Register Download

    public function downloadPayAuditRegister(Request $request){
        
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
		
		// $sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.location, drv_emp.payslip_held, drv_emp.payslip_approved, drv_rates.ot1dura, drv_rates.ot2dura, drv_rates.wk_days, drv_rates.abhrs, drv_rates.nopay_days, IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays, drv_info.fig_group_title, drv_info.fig_group, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, companies.name AS location, employee_payslips.payslip_held, employee_payslips.payslip_approved, payroll_profiles.payroll_act_id FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND ".$emp_department_col."=? AND employee_payslips.payslip_cancel=0) AS drv_emp ";
		// $sqlslip.="INNER JOIN job_categories ON drv_emp.payroll_act_id=job_categories.id ";
		// $sqlslip.="INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) as ot1dura, sum(double_rate_otwork_hrs) as ot2dura, SUM(work_days) as wk_days, 0 as abhrs, SUM(nopay_days) AS nopay_days from employee_paid_rates where date_format(concat(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (date_format(?, '%Y-%m'), date_format(?, '%Y-%m')) group by employee_payslip_id) AS drv_rates ON drv_emp.emp_payslip_id=drv_rates.employee_payslip_id ";
		// $sqlslip.="INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_emp.emp_epfno, drv_info.fig_id";
		
		$sqlslip = "SELECT 
			drv_emp.emp_payslip_id,
			drv_emp.emp_id,	
			drv_emp.emp_etfno AS emp_epfno,
			drv_emp.emp_first_name,
			drv_emp.location,
			drv_emp.payslip_held,
			drv_emp.payslip_approved,
			drv_rates.ot1dura,
			drv_rates.ot2dura,
			drv_rates.wk_days,
			drv_rates.abhrs,
			drv_rates.nopay_days,
			IFNULL(job_categories.emp_payroll_workdays, 26) AS emp_payroll_workdays,
			drv_info.fig_group_title,
			drv_info.fig_group,
			drv_info.fig_value AS fig_value,
			drv_info.epf_payable AS epf_payable,
			drv_info.remuneration_pssc,
			drv_info.remuneration_tcsc,
			drv_info.fig_base_ratio
			
		FROM (
			SELECT 
				employee_payslips.id AS emp_payslip_id,
				employees.emp_id AS emp_epfno,
				employees.emp_etfno,
				employees.emp_id,
				employees.emp_name_with_initial AS emp_first_name,
				companies.name AS location,
				employee_payslips.payslip_held,
				employee_payslips.payslip_approved,
				payroll_profiles.payroll_act_id
			FROM employee_payslips
			INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id
			INNER JOIN employees ON payroll_profiles.emp_id = employees.id
			INNER JOIN companies ON employees.emp_company = companies.id
			WHERE 
				employee_payslips.payment_period_id = ?
				AND employees.emp_company = ?
				AND $emp_department_col = ?
				AND employee_payslips.payslip_cancel = 0
		) AS drv_emp
		INNER JOIN job_categories ON drv_emp.payroll_act_id = job_categories.id
		INNER JOIN (
			SELECT 
				employee_payslip_id,
				SUM(normal_rate_otwork_hrs) AS ot1dura,
				SUM(double_rate_otwork_hrs) AS ot2dura,
				SUM(work_days) AS wk_days,
				0 AS abhrs,
				SUM(nopay_days) AS nopay_days
			FROM employee_paid_rates
			WHERE DATE_FORMAT(CONCAT(salary_process_year, '-', salary_process_month, '-01'), '%Y-%m') IN (
				DATE_FORMAT(?, '%Y-%m'),
				DATE_FORMAT(?, '%Y-%m')
			)
			GROUP BY employee_payslip_id
		) AS drv_rates ON drv_emp.emp_payslip_id = drv_rates.employee_payslip_id
		INNER JOIN (
			SELECT 
				id AS fig_id,
				employee_payslip_id,
				fig_group_title,
				fig_group,
				epf_payable,
				remuneration_payslip_spec_code AS remuneration_pssc,
				remuneration_taxcalc_spec_code AS remuneration_tcsc,
				fig_value,
				fig_base_ratio
			FROM employee_salary_payments
			WHERE payment_period_id = ?
		) AS drv_info ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id
		ORDER BY drv_emp.emp_epfno, drv_info.fig_id;
		";
		
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
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3','PAYES');
		/*
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
		*/
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>0, 'OTHRS2'=>0, 'OT2DURA'=>0, 'WK_ACT_DAYS'=>0, 'WK_MAX_DAYS'=>0, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0, 'PAYES'=>0);
		
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
		// return response()->json($emp_data);
		//-2023-11-07
		// dd($emp_data);
		foreach($emp_data as $r){
			$total_sql = DB::select("SELECT sUM(audit_ot_count) AS audit_ot_count FROM `audit_attendance` WHERE `emp_id`=$r->emp_id AND `attendance_date` BETWEEN $payment_period_fr AND $payment_period_to ");

			$total_audit_ot = $total_sql[0]->audit_ot_count;
			
			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
				$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
				$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
				$emp_fig_tottax = 0;
			}
			if(!isset($emp_array[$cnt-1])){
				
				
				
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'nopay_days'=>$r->nopay_days, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OT1DURA'=>$total_audit_ot, 'OTHRS2'=>0, 'OT2DURA'=>$r->ot2dura, 'WK_ACT_DAYS'=>$r->wk_days, 'WK_MAX_DAYS'=>$r->emp_payroll_workdays, 'WK_DIFF_HRS'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0, 'PAYES'=>0);
				
				$rem_tot_bnp = 0;
				$rem_tot_fortax = 0;
				$rem_tot_earn = 0;
				$rem_tot_ded = 0;
				$rem_net_sal = 0;
				$rem_ded_other = 0;
			}
			
			
			$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
	
			if (isset($emp_array[$cnt - 1][$fig_key])) {
				$fig_group_val = $emp_array[$cnt - 1][$fig_key];
			
				// Handle special case for OTHRS1
				
				
				
					// For all other keys, accumulate values normally
					if ($fig_key != 'OTHER_REM' && $fig_key !='OTHRS1') {
						$emp_array[$cnt - 1][$fig_key] = abs($r->fig_value) + $fig_group_val;
						$sum_array[$fig_key] += abs($r->fig_value);
					}
					else if($fig_key =='OTHRS1'){					
				
						$tototamount=($r->fig_base_ratio*$total_audit_ot);


						$emp_array[$cnt - 1][$fig_key] = abs($tototamount) + $fig_group_val;
						// $emp_array[$cnt - 1][$fig_key] = $totot1;
						// $emp_array[$cnt - 1][$fig_key . '_base_ratio'] = $r->fig_base_ratio;
						$sum_array[$fig_key] += abs($tototamount);
						
					}
				
			
				// NETSAL Calculation
				if (!in_array($r->fig_group_title, ['EPF12', 'ETF3', 'OTHRS'])) {
					$net_payslip_fig_value += $r->fig_value;
					$emp_array[$cnt - 1]['NETSAL'] = $net_payslip_fig_value;
			
					$reg_net_sal = $sum_array['NETSAL'] - $rem_net_sal;
					$sum_array['NETSAL'] = $reg_net_sal + $net_payslip_fig_value;
					$rem_net_sal = $net_payslip_fig_value;
			
					// TAXABLE Check
					if (in_array($r->remuneration_tcsc, $conf_tl)) {
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt - 1]['tot_fortax'] = $emp_fig_tottax;
			
						$reg_tot_fortax = $sum_array['tot_fortax'] - $rem_tot_fortax;
						$sum_array['tot_fortax'] = $reg_tot_fortax + $emp_fig_tottax;
						$rem_tot_fortax = $emp_fig_tottax;
					}
			
					// EARNING / DEDUCTION Handling
					$fig_otherrem = ($fig_key == 'OTHER_REM') ? 1 : 0;
			
					if ((($r->fig_value >= 0) && ($fig_key != 'EPF8')) || ($fig_key == 'NOPAY')) {
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt - 1]['tot_earn'] = $emp_fig_totearn;
			
						$reg_tot_earn = $sum_array['tot_earn'] - $rem_tot_earn;
						$sum_array['tot_earn'] = $reg_tot_earn + $emp_fig_totearn;
						$rem_tot_earn = $emp_fig_totearn;
					}
			
					if ($r->fig_value >= 0) {
						$emp_fig_otherearn += ($r->fig_value * $fig_otherrem);
						$emp_array[$cnt - 1]['add_other'] = $emp_fig_otherearn;
					} else {
						if ($fig_key != 'NOPAY') {
							$emp_fig_totlost += $r->fig_value;
							$emp_array[$cnt - 1]['tot_ded'] = abs($emp_fig_totlost);
			
							$reg_tot_ded = $sum_array['tot_ded'] - $rem_tot_ded;
							$sum_array['tot_ded'] = $reg_tot_ded + abs($emp_fig_totlost);
							$rem_tot_ded = abs($emp_fig_totlost);
						}
			
						$emp_fig_otherlost += abs($r->fig_value) * $fig_otherrem;
						$emp_array[$cnt - 1]['ded_other'] = $emp_fig_otherlost;
			
						$reg_ded_other = $sum_array['ded_other'] - $rem_ded_other;
						$sum_array['ded_other'] = $reg_ded_other + $emp_fig_otherlost;
						$rem_ded_other = $emp_fig_otherlost;
					}
				}
				else if($fig_key =='OTHRS1'){

				$net_payslip_fig_value +=  $tototamount;
					$emp_array[$cnt - 1]['NETSAL'] = $net_payslip_fig_value;
			
					$reg_net_sal = $sum_array['NETSAL'] - $rem_net_sal;
					$sum_array['NETSAL'] = $reg_net_sal + $net_payslip_fig_value;
					$rem_net_sal = $net_payslip_fig_value;

					$emp_fig_totearn += $tototamount;
					$emp_array[$cnt - 1]['tot_earn'] = $emp_fig_totearn;
			}
			
				// BNP Group Total Calculation
				if (in_array($fig_key, ['BASIC', 'BRA_I', 'add_bra2'])) {
					$emp_tot_bnp = (
						($emp_array[$cnt - 1]['BASIC'] ?? 0) +
						($emp_array[$cnt - 1]['BRA_I'] ?? 0) +
						($emp_array[$cnt - 1]['add_bra2'] ?? 0)
					);
					$emp_array[$cnt - 1]['tot_bnp'] = $emp_tot_bnp;
			
					$reg_tot_bnp = $sum_array['tot_bnp'] - $rem_tot_bnp;
					$sum_array['tot_bnp'] = $reg_tot_bnp + $emp_tot_bnp;
					$rem_tot_bnp = $emp_tot_bnp;
				}
			}

		
			
			if($fig_key =='OTHRS1'){	
				$total_sql = DB::select("SELECT sUM(audit_ot_count) AS audit_ot_count FROM `audit_attendance` WHERE `emp_id`=$r->emp_id AND `attendance_date` BETWEEN $payment_period_fr AND $payment_period_to ");
				$total_audit_ot = $total_sql[0]->audit_ot_count;				
				$tototamount=($r->fig_base_ratio*$total_audit_ot);


						$taxlist=DB::select("SELECT 
							tax_provisions.id AS prov_k,
							IFNULL(drv_income.total_fig_value, 0) AS fig_value,
							tax_provisions.min_income,
							tax_provisions.tax_rate
						FROM (
							SELECT 
								SUM(
									CASE 
										WHEN (1 - remuneration_taxations.strict_epf_payables) = 0 
										THEN epf_payable * fig_value
										ELSE (1 - remuneration_taxations.strict_epf_payables) * fig_value
									END
								) AS total_fig_value
							FROM (
								SELECT 
									remuneration_taxcalc_spec_code AS fig_tcsc,
									fig_value,
									epf_payable,
									remuneration_payslip_spec_code
								FROM 
									employee_salary_payments 
								WHERE 
									employee_payslip_id = $r->emp_payslip_id
							) AS drv_tlfigs
							INNER JOIN remuneration_taxations 
								ON drv_tlfigs.fig_tcsc = remuneration_taxations.taxcalc_spec_code
							WHERE 
								remuneration_taxations.fig_calc_opt = 'FIGPAYE'
								AND remuneration_taxations.optspec_cancel = 0
						) AS drv_income
						CROSS JOIN tax_provisions
						WHERE 
							drv_income.total_fig_value >= tax_provisions.min_income
						ORDER BY 
							tax_provisions.id DESC
						");
				if(count($taxlist)>=1){

					$taxgrp_increment = 25;
					$mod_taxlistfig = ($taxlist[0]->fig_value%$taxgrp_increment);
					$rem_taxlistfig = ($mod_taxlistfig>0)?($taxgrp_increment-$mod_taxlistfig):0;
					$ot1fig_value=$r->fig_value;
					
					$grp_uboundary = $taxlist[0]->fig_value; // ($taxlist[0]->fig_value+$rem_taxlistfig); // 
					$tax_uboundary = $taxlist[0]->tax_rate;
					$tax_totamount = 0;
					
				
			


						$otfigdec= ($taxlist[0]->fig_value-$ot1fig_value);
						$grp_uboundary=($otfigdec+$tototamount);								
						$grp_lboundary = $taxlist[0]->min_income;
						
						$tax_grpfigval = $grp_uboundary-$grp_lboundary;
						
						if($tax_grpfigval<0){
							$tax_totamount='';
						}
						else{
							$tax_grprate = $taxlist[0]->tax_rate;
							$tax_totamount += ($tax_grpfigval)*($tax_grprate/100);
							$grp_uboundary = $grp_lboundary;
						}
					

					$emp_tax_str=number_format((float)round($tax_totamount, 0), 2, '.', '');
					$emp_array[$cnt - 1]['PAYES'] = $emp_tax_str;
					$sum_array['PAYES'] = $emp_tax_str;
				}

			}

			



			
		}
		/**/
		
		$emp_array[] = $sum_array;
		
		if($request->print_record=='1'){
			$excel_rows[] = array('EPF NO', 'Employee Name', 'Working Day', 'Salary Per Day', 'OT1 Hrs.', 'OT2 Hrs.', 'OT1 Rate', 'OT2 Rate', 'Basic+BRA', 'No Pay Day', 'No Pay Amount', 'TTL For E.P.F.', 'OT1 Amount', 'OT2 Amount', 'Attendance', 'Transport Allowance', 'Production Insentive', 'Gross Salary', 'EPF-8', 'Late Deduction', 'Advance', 'Payee Tax', 'Bank Charges', 'Loan', 'Total Deduction', 'Net Salary', 'EPF-12', 'ETF-3', 'Signature');
			$rpt_row_cnt = 0;//skip-title-row
			foreach($emp_array as $excel_data){
				
				if($rpt_row_cnt>0){
					$basic_pay=$excel_data['BASIC']+$excel_data['BRA_I']+$excel_data['add_bra2'];
					$emp_daysal=($excel_data['WK_MAX_DAYS']>0)?($basic_pay/$excel_data['WK_MAX_DAYS']):'';//
					$tot_forepf = $excel_data['tot_bnp']-$excel_data['NOPAY'];//$excel_data['tot_fortax'];//
					$normal_ot_rate = !empty($excel_data['OT1DURA'])?($excel_data['OTHRS1']/$excel_data['OT1DURA']):0;
					$double_ot_rate = !empty($excel_data['OT2DURA'])?($excel_data['OTHRS2']/$excel_data['OT2DURA']):0;
					
					$excel_rows[] = array($excel_data['emp_epfno'], $excel_data['emp_first_name'], 
										$excel_data['WK_ACT_DAYS'], $emp_daysal, 
										$excel_data['OT1DURA'], $excel_data['OT2DURA'], 
										$normal_ot_rate, $double_ot_rate,
										$basic_pay, $excel_data['nopay_days'], $excel_data['NOPAY'], 
										$tot_forepf, $excel_data['OTHRS1'], 
										$excel_data['OTHRS2'], $excel_data['ATTBONUS_W'], 
										$excel_data['add_transport'], $excel_data['INCNTV_EMP'],
										$excel_data['tot_earn'], 
										$excel_data['EPF8'], 
										$excel_data['ded_IOU'], 
										$excel_data['sal_adv'], //
										$excel_data['PAYES'], $excel_data['ded_fund_1'], $excel_data['LOAN'], 
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
			
			$pdf = PDF::loadView('AuditReports.PayRegister_pdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'))
				->setPaper('legal', 'landscape');//->setPaper($customPaper, 'landscape');
				
			return $pdf->download('pay-register.pdf');
			
			
			
		}
    }
	//Pay Register Download End

	// Audit Employee Salary Sheets Start

	public function downloadSalarySheet(Request $request){
        
        $paymentPeriod=PaymentPeriod::find($request->rpt_period_id);
			
		$payment_period_id=$paymentPeriod->id;//1;
		$payment_period_fr=$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to=$paymentPeriod->payment_period_to;//$request->work_date_to;
		/*
		$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_first_name, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_name_with_initial AS emp_first_name, branches.location AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id WHERE employee_payslips.payment_period_id=? AND employees.emp_location=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, SUM(`fig_value`) AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=? GROUP BY `employee_payslip_id`, `fig_group_title`) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
		*/
		//$sqlslip="SELECT drv_emp.emp_payslip_id, drv_emp.emp_etfno AS emp_epfno, drv_emp.emp_first_name, drv_emp.emp_designation, drv_emp.location, drv_info.fig_group_title, drv_info.fig_value AS fig_value, drv_info.epf_payable AS epf_payable, drv_info.remuneration_pssc, drv_info.remuneration_tcsc, drv_catinfo.emp_otamt1, drv_catinfo.emp_otamt2, drv_catinfo.emp_nopaydays FROM (SELECT employee_payslips.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno, employees.emp_name_with_initial AS emp_first_name, job_titles.title AS emp_designation, companies.name AS location FROM `employee_payslips` INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN job_titles ON employees.emp_job_code=job_titles.id WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.emp_department=? AND employee_payslips.payslip_cancel=0) AS drv_emp INNER JOIN (select employee_payslip_id, sum(normal_rate_otwork_hrs) AS emp_otamt1, sum(double_rate_otwork_hrs) AS emp_otamt2, sum(nopay_days) AS emp_nopaydays from employee_paid_rates GROUP BY employee_payslip_id) AS drv_catinfo ON drv_emp.emp_payslip_id=drv_catinfo.employee_payslip_id INNER JOIN (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value FROM employee_salary_payments WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
		

		$sqlslip="SELECT 
			drv_emp.emp_payslip_id, 
			drv_emp.emp_etfno AS emp_epfno, 
			drv_emp.emp_first_name, 
			drv_emp.emp_designation, 
			drv_emp.location, 
			drv_info.fig_group, 
			drv_info.fig_group_title, 
			drv_info.fig_value AS fig_value, 
			drv_info.fig_base_ratio AS fig_base_ratio, 
			drv_info.epf_payable AS epf_payable, 
			drv_info.remuneration_pssc, 
			drv_info.remuneration_tcsc, 
			drv_catinfo.emp_otamt1, 
			drv_catinfo.emp_otamt2, 
			drv_catinfo.emp_nopaydays 
		FROM (
			SELECT 
				employee_payslips.id AS emp_payslip_id, 
				employees.emp_id AS emp_epfno, 
				employees.emp_etfno, 
				employees.emp_name_with_initial AS emp_first_name, 
				job_titles.title AS emp_designation, 
				companies.name AS location 
			FROM 
				employee_payslips 
				INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id = payroll_profiles.id 
				INNER JOIN employees ON payroll_profiles.emp_id = employees.id 
				INNER JOIN companies ON employees.emp_company = companies.id 
				LEFT OUTER JOIN job_titles ON employees.emp_job_code = job_titles.id 
			WHERE 
				employee_payslips.payment_period_id = ? 
				AND employees.emp_company = ? 
				AND employees.emp_department = ? 
				AND employee_payslips.payslip_cancel = 0 
				AND employees.emp_id NOT IN (195, 201)
		) AS drv_emp 
		INNER JOIN (
			SELECT 
				employee_payslip_id, 
				SUM(normal_rate_otwork_hrs) AS emp_otamt1, 
				SUM(double_rate_otwork_hrs) AS emp_otamt2, 
				SUM(nopay_days) AS emp_nopaydays 
			FROM 
				employee_paid_rates 
			GROUP BY 
				employee_payslip_id
		) AS drv_catinfo ON drv_emp.emp_payslip_id = drv_catinfo.employee_payslip_id 
		INNER JOIN (
			SELECT 
				id AS fig_id, 
				employee_payslip_id, 
				fig_group, 
				fig_group_title, 
				epf_payable, 
				remuneration_payslip_spec_code AS remuneration_pssc, 
				remuneration_taxcalc_spec_code AS remuneration_tcsc, 
				fig_value AS fig_value,
				fig_base_ratio AS fig_base_ratio
			FROM 
				employee_salary_payments 
			WHERE 
				payment_period_id = ?
		) AS drv_info ON drv_emp.emp_payslip_id = drv_info.employee_payslip_id 
		ORDER BY 
			drv_info.fig_id;
		";
		$emp_data = DB::select($sqlslip, [$payment_period_id, 
										  $request->rpt_location_id, $request->rpt_dept_id,
										  $payment_period_id]
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
		// return response()->json($emp_data);
		//-2023-11-07
		// dd($emp_data);
		foreach($emp_data as $r){
			$total_audit_ot = DB::table('audit_attendance')
				->where('emp_id', $r->emp_epfno)
				->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
				->sum('audit_ot_count');
				
				$other1=($total_audit_ot*$r->fig_base_ratio);

			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
				$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
				$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
				$emp_fig_tottax = 0;
			}
			if(!isset($emp_array[$cnt-1])){
				
				
				
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'emp_designation'=>$r->emp_designation, 'Office'=>$r->location, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTAMT1'=>$total_audit_ot, 'OTAMT2'=>$r->emp_otamt2, 'NOPAYCNT'=>$r->emp_nopaydays, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'EPF12'=>0, 'ETF3'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0,'PAYES'=>0);
				
			}
			
			// Custom override for specific fig_group
			if ($r->fig_group == 'OTHRS1') {
				$total_audit_ot = DB::table('audit_attendance')
				->where('emp_id', $r->emp_epfno)
				->whereBetween('attendance_date', [$payment_period_fr, $payment_period_to])
				->sum('audit_ot_count');
				
				$other1=($total_audit_ot*$r->fig_base_ratio);
				$r->fig_value = $other1;    // ✅ Replace with your custom logic or value
				//$r->emp_otamt1 = 55.5;       // ✅ Replace with your custom logic or value
				$emp_fig_totearn +=  $other1;
				$emp_array[$cnt-1]['tot_earn'] = number_format((float)$emp_fig_totearn, 2, '.', '');
						
				$net_payslip_fig_value += $other1;
				$emp_array[$cnt-1]['NETSAL'] = number_format((float)$net_payslip_fig_value, 2, '.', '');
		}

			// Determine the key to be used for the current figure
			$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title]) ? $r->fig_group_title : $r->remuneration_pssc;

			if (isset($emp_array[$cnt-1][$fig_key])) {
				// Get the current value for this key (if already present)
				$fig_group_val = $emp_array[$cnt-1][$fig_key];

				// Update the current figure by adding the absolute value of the new one
				$emp_array[$cnt-1][$fig_key] = number_format((float)(abs($r->fig_value) + $fig_group_val), 2, '.', '');

				// Skip EPF12 and ETF3 for net salary/tax/earnings calculations
				if (!(($r->fig_group_title == 'EPF12') || ($r->fig_group_title == 'ETF3') || ($r->fig_group_title == 'OTHRS'))) {

					// Update net salary with the figure value (can be + or -)
					$net_payslip_fig_value += $r->fig_value;
					$emp_array[$cnt-1]['NETSAL'] = number_format((float)$net_payslip_fig_value, 2, '.', '');

					// If figure is taxable (e.g., EPF Payable or NOPAY), add to total for tax
					if (($r->epf_payable == 1) || ($fig_key == 'NOPAY')) {
						$emp_fig_tottax += $r->fig_value;
						
						$emp_array[$cnt-1]['tot_fortax'] = number_format((float)$emp_fig_tottax, 2, '.', '');
					}

					// Check if figure is classified under OTHER_REM group
					$fig_otherrem = ($fig_key == 'OTHER_REM') ? 1 : 0;

					// Add to total earnings if positive (and not EPF8) or if NOPAY
					if ((($r->fig_value >= 0) && ($fig_key != 'EPF8')) || ($fig_key == 'NOPAY')) {
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn'] = number_format((float)$emp_fig_totearn, 2, '.', '');
					}

					if ($r->fig_value >= 0) {
						// Add to "other earnings" only if marked as OTHER_REM
						$emp_fig_otherearn += ($r->fig_value * $fig_otherrem);
						$emp_array[$cnt-1]['add_other'] = number_format((float)$emp_fig_otherearn, 2, '.', '');
					} else {
						// Handle negative figures (deductions)
						if ($fig_key != 'NOPAY') {
							$emp_fig_totlost += $r->fig_value; // This will be negative
							$emp_array[$cnt-1]['tot_ded'] = number_format((float)abs($emp_fig_totlost), 2, '.', '');
						}

						// Add to "other deductions" if marked as OTHER_REM
						$emp_fig_otherlost += (abs($r->fig_value) * $fig_otherrem);
						$emp_array[$cnt-1]['ded_other'] = number_format((float)$emp_fig_otherlost, 2, '.', '');
					}
				}

				// If figure is one of the base salary components, calculate total basic + allowances
				if (($fig_key == 'BASIC') || ($fig_key == 'BRA_I') || ($fig_key == 'add_bra2')) {
					$emp_tot_bnp = (
						$emp_array[$cnt-1]['BASIC'] +
						$emp_array[$cnt-1]['BRA_I'] +
						$emp_array[$cnt-1]['add_bra2']
					);
					$emp_array[$cnt-1]['tot_bnp'] = number_format((float)$emp_tot_bnp, 2, '.', '');
				}
			}


			if($fig_key =='OTHRS1'){	
				$total_sql = DB::select("SELECT sUM(audit_ot_count) AS audit_ot_count FROM `audit_attendance` WHERE `emp_id`=$r->emp_epfno AND `attendance_date` BETWEEN $payment_period_fr AND $payment_period_to ");
				$total_audit_ot = $total_sql[0]->audit_ot_count;				
				$tototamount=($r->fig_base_ratio*$total_audit_ot);


						$taxlist=DB::select("SELECT 
							tax_provisions.id AS prov_k,
							IFNULL(drv_income.total_fig_value, 0) AS fig_value,
							tax_provisions.min_income,
							tax_provisions.tax_rate
						FROM (
							SELECT 
								SUM(
									CASE 
										WHEN (1 - remuneration_taxations.strict_epf_payables) = 0 
										THEN epf_payable * fig_value
										ELSE (1 - remuneration_taxations.strict_epf_payables) * fig_value
									END
								) AS total_fig_value
							FROM (
								SELECT 
									remuneration_taxcalc_spec_code AS fig_tcsc,
									fig_value,
									epf_payable,
									remuneration_payslip_spec_code
								FROM 
									employee_salary_payments 
								WHERE 
									employee_payslip_id = $r->emp_payslip_id
							) AS drv_tlfigs
							INNER JOIN remuneration_taxations 
								ON drv_tlfigs.fig_tcsc = remuneration_taxations.taxcalc_spec_code
							WHERE 
								remuneration_taxations.fig_calc_opt = 'FIGPAYE'
								AND remuneration_taxations.optspec_cancel = 0
						) AS drv_income
						CROSS JOIN tax_provisions
						WHERE 
							drv_income.total_fig_value >= tax_provisions.min_income
						ORDER BY 
							tax_provisions.id DESC
						");
				if(count($taxlist)>=1){

					$taxgrp_increment = 25;
					$mod_taxlistfig = ($taxlist[0]->fig_value%$taxgrp_increment);
					$rem_taxlistfig = ($mod_taxlistfig>0)?($taxgrp_increment-$mod_taxlistfig):0;
					$ot1fig_value=$r->fig_value;
					
					$grp_uboundary = $taxlist[0]->fig_value; // ($taxlist[0]->fig_value+$rem_taxlistfig); // 
					$tax_uboundary = $taxlist[0]->tax_rate;
					$tax_totamount = 0;
					
				
			


						$otfigdec= ($taxlist[0]->fig_value-$ot1fig_value);
						$grp_uboundary=($otfigdec+$tototamount);								
						$grp_lboundary = $taxlist[0]->min_income;
						
						$tax_grpfigval = $grp_uboundary-$grp_lboundary;
						
						if($tax_grpfigval<0){
							$tax_totamount='';
						}
						else{
							$tax_grprate = $taxlist[0]->tax_rate;
							$tax_totamount += ($tax_grpfigval)*($tax_grprate/100);
							$grp_uboundary = $grp_lboundary;
						}
					

					$emp_tax_str=number_format((float)round($tax_totamount, 0), 2, '.', '');
					$emp_array[$cnt - 1]['PAYES'] = $emp_tax_str;
					$sum_array['PAYES'] = $emp_tax_str;
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
		$paymonth_name = Carbon\Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');
		
		ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");
		
		$pdf = PDF::loadView('AuditReports.auditsalary_sheetpdf', compact('emp_array', 'more_info', 'sect_name', 'paymonth_name'));
        return $pdf->download('salary-list.pdf');
		//return view('Payroll.payslipProcess.SalarySheet_pdf', compact('emp_array', 'more_info'));
    }

	// Audit Employee Salary Sheets End

}
