<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\Employee;

use App\EmployeePayday;

use App\EmployeePayslip;
use App\EmployeeTermPayment;
use App\EmployeeTermPaymentExtra;
use App\employeeWorkRate;//EmployeeWorkRate;

use App\PaymentPeriod;
use App\PayrollProcessType;

use App\PayrollProfile;
use App\PayrollProfileExtra;

use App\Remuneration;
use App\RemunerationExtra;
/*
use App\ShapeuphrmSetting;
*/
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use PDF;
use Validator;

class EmployeeTermPaymentExtrasController extends Controller
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
		$remuneration_extras=RemunerationExtra::where(['remuneration_extra_cancel'=>0])->orderBy('id', 'asc')->get();
		
		$paydays = EmployeePayday::where(['payday_cancel'=>0])->get();
		
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$remuneration_list=Remuneration::whereIn('id', [23, 24])->where(['remuneration_cancel'=>0])->orderBy('id', 'asc')->get();
		
        return view('Payroll.termPayment.extrasPayment_list',compact('branch', 'remuneration_list', 'remuneration_extras', 'payroll_process_type', 'payment_period', 'paydays'));
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
        try{
			return DB::transaction(function() use ($request){
				$rules = array(
					'remuneration_extra_id' => 'required', 
					'employee_extra_entitle_amount' => 'required'
				);
		
				$error = Validator::make($request->all(), $rules);
		
				if($error->fails())
				{
					return response()->json(['errors' => $error->errors()->all()]);
				}
				
				$paymentPeriod = PaymentPeriod::find($request->input('payment_period_id'));
				
				if(!empty($paymentPeriod)){
					if(empty($paymentPeriod->advance_payment_date)){
						return response()->json(['errors' => array('Advance payment date not specified in payment schedule')]);
					}else{
						$advancePaymentDate = Carbon::parse($paymentPeriod->advance_payment_date);
						$revDate = Carbon::today();
						//if(!$advancePaymentDate->isAfter($revDate))
						if(!$advancePaymentDate->gte($revDate)){
							return response()->json(['errors' => array('Advance payment closed on '.$paymentPeriod->advance_payment_date)]);
						}
					}
				}else{
					return response()->json(['errors' => array('Payment schedule not available')]);
				}
				
				
				//2025-11-24-check-payslip-to-disallow-advance-payment-for-periods-with-employee-salaries
				$actPeriodPayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id, 
															'payment_period_id'=>$request->input('payment_period_id')])
										->latest()
										->first();
				if(!empty($actPeriodPayslip)){
					return response()->json(['errors' => array('Advance payments cannot be issued to salary processed employees')]);
				}
				//--
				
				
				$employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id])
										->latest()
										->first();
				$emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);//pay-slip-no++ adding payment to next emp-salary
				
				$employeePaydata = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id,
														   'payslip_cancel'=>0])
										->latest()
										->first();
				$emp_payslip_id = !empty($employeePaydata)?$employeePaydata->id:0;
				
				/*
				$form_data = array(
					'remuneration_name'        =>  $request->remuneration_name
					
				);
				*/
				
				$termPayment=NULL;
				
				/*
				1. find $remuneration_id of selected $request->input('remuneration_extra_id'), 
				   $payment_period_id, $employee_work_rate_id
				2. load array1[id, value_group] of remuneration_extras where remuneration_id=$remuneration_id and remuneration_extra_cancel=0
				3. load array2[id, remuneration_extra_id, extra_entitle_amount] of payroll_profile_extras 
				   where payroll_profile_id=? and remuneration_id=$remuneration_id and payroll_profile_extra_signout=0
				
				// swap 4, 5 => 5, 4
				4. merge array1, array2 into array3[id*, employee_term_payment_id*, remuneration_extra_id*1, payroll_profile_extra_id*2, 
				   payment_period_id*$payment_period_id, employee_work_rate_id*$employee_work_rate_id, term_extra_entitle_amount*]
				4a.if($remuneration_id==23) then calculate absentHours deduction to be included end of array3
				5. select max(id*(remuneration_extra_id=$request->input('remuneration_extra_id'))) as extra_conf_id, 
				   max(id*(remuneration_extra_id=0)) as extra_rate_id, 
				   employee_term_payment_id, payment_amount 
				   from employee_term_payment_extras where employee_term_payments.payroll_profile_id=? and
				   employee_term_payment_extras.payment_period_id=$payment_period_id and 
				   employee_term_payment_extras.employee_work_rate_id=$employee_work_rate_id
				   group by employee_term_payment_id
				
				6. prepare array4 to insert/update employee_term_payments
				7. loop array3[$request->input('remuneration_extra_id')], array3[0] to insert/update employee_term_payment_extras
				*/
				
				//1 find $remuneration_id, $payment_period_id, $employee_work_rate_id
				$remunerationExtras=RemunerationExtra::find($request->input('remuneration_extra_id'));//hidden_id
				$remuneration_id=$remunerationExtras->remuneration_id;
				$remuneration_extras_id=$request->input('remuneration_extra_id');//hidden_id
				$opt_entitlement=$remunerationExtras->extra_entitlement;
				$employee_work_rate_id=$emp_payslip_id;//$request->input('employee_work_rate_id');
				$payment_period_id = $request->input('payment_period_id');
				
				//to-do: check validity of $request work_id, period_id fetched on function reviewPaymentList(...)
				
				//2 load array1[id, value_group] of remuneration_extras
				$tblExtras=DB::table('remuneration_extras')
							->select('id', 'extras_label', 'value_group')
							->where(['remuneration_id'=>$remuneration_id, 'remuneration_extra_cancel'=>0])
							->get();
				$arrayExtrasRows = array();
				foreach($tblExtras as $ae){
					$arrayExtrasRows[$ae->id]=array('id'=>$ae->id, 'extras_label'=>$ae->extras_label, 'value_group'=>$ae->value_group);
				}
				
				//3 load array2[id, remuneration_extra_id, extra_entitle_amount] of payroll_profile_extras
				//3 not possible to limit $remuneration_extras_id, getting 1 record or empty result since 
				//3 a profile may contain fixed extras; for eg. Employee Salary Advance
				$tblProfileExtras=DB::table('payroll_profile_extras')
									->select('id', 'remuneration_extra_id', 'extra_entitle_amount')
									->where(['payroll_profile_id'=>$request->payroll_profile_id, 
											 'remuneration_id'=>$remuneration_id, //'remuneration_extra_id'=>$remuneration_extras_id,
											 'payroll_profile_extra_signout'=>0])
									->get();
				$arrayProfileExtrasRows = array();
				
				foreach($tblProfileExtras as $ape){
					//profiles not include +/- for entitle values, therefore applied here
					$value_group_entitle_amount = $ape->extra_entitle_amount*$arrayExtrasRows[$ape->remuneration_extra_id]['value_group'];
					
					$arrayProfileExtrasRows[$ape->remuneration_extra_id]=array('id'=>$ape->id, 
																		'remuneration_extra_id'=>$ape->remuneration_extra_id,
																		'extra_entitle_amount'=>$value_group_entitle_amount);
				}
				
				//* swap 4, 5 sequence
				/*5. select max(id*(remuneration_extra_id=$request->input('remuneration_extra_id'))) as extra_conf_id, 
				   max(id*(remuneration_extra_id=0)) as extra_rate_id, 
				   employee_term_payment_id, payment_amount 
				   from employee_term_payment_extras where employee_term_payments.payroll_profile_id=? and
				   employee_term_payment_extras.payment_period_id=$payment_period_id and 
				   employee_term_payment_extras.employee_work_rate_id=$employee_work_rate_id
				   group by employee_term_payment_id*/
				$termPaymentHeader=DB::select("SELECT ifnull(employee_term_payments.id, 0) as id, ".
					"ifnull(max(employee_term_payment_extras.id*(employee_term_payment_extras.".
					"remuneration_extra_id=?)), 0) as extra_conf_id, ".
					"ifnull(max(employee_term_payment_extras.id*(employee_term_payment_extras.".
					"remuneration_extra_id=0)), 0) as extra_rate_id, ".
					"ifnull(employee_term_payments.payment_amount, 0) as payment_amount, ".
					"ifnull(max(ABS(employee_term_payment_extras.term_extra_entitle_amount)*(employee_term_payment_extras.".
					"remuneration_extra_id=?)), 0) as extra_conf_val, ".
					"ifnull(max(ABS(employee_term_payment_extras.term_extra_entitle_amount)*(employee_term_payment_extras.".
					"remuneration_extra_id=0)), 0) as extra_rate_val, ".
					"ifnull(employee_term_payments.payment_cancel, 0) as term_cancel ".
					"FROM (select id, payroll_profile_id, payment_amount, payment_cancel, DATE(created_at) as created_date ".
								"from employee_term_payments ".
								"where payroll_profile_id=? and emp_payslip_no=? AND remuneration_id=? ".
								"and DATE_FORMAT(created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m')) as employee_term_payments ".
					"inner join employee_term_payment_extras ".
					"on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ".
					"WHERE employee_term_payment_extras.payment_period_id=? AND employee_term_payment_extras.employee_work_rate_id=?", 
					[$remuneration_extras_id, $remuneration_extras_id, $request->payroll_profile_id, $emp_payslip_no, $remuneration_id, 
					 $payment_period_id, $employee_work_rate_id
					]);
				
				$headTermPaymentId = 0; $termCancel = 0;
				$headTermPaymentVal = 0;
				$extraConfId = 0; $extraConfVal = 0;
				$extraRateId = 0; $extraRateVal = 0;
				$absentDeduction = 0;
				if($termPaymentHeader[0]->id!=0){
					//return response()->json(['errors' => array('Payslip details already processed')]);
					$headTermPaymentId = $termPaymentHeader[0]->id; 
					$termCancel = $termPaymentHeader[0]->term_cancel;
					$headTermPaymentVal = $termPaymentHeader[0]->payment_amount;
					$extraConfId = $termPaymentHeader[0]->extra_conf_id; 
					
					//$extraConfVal = $termPaymentHeader[0]->extra_conf_val*$arrayExtrasRows[$remuneration_extras_id]['value_group'];
					$extraConfVal = $termPaymentHeader[0]->extra_conf_val;
					$extraConfGrp = $arrayExtrasRows[$remuneration_extras_id]['value_group'];
					if(isset($arrayProfileExtrasRows[$remuneration_extras_id])&&($extraConfId=='0')&&($opt_entitlement=='1')){
						$extraConfVal = $arrayProfileExtrasRows[$remuneration_extras_id]['extra_entitle_amount'];
						$extraConfGrp = 1;//arrayProfileExtrasRows values transformed to +/- earlier
					}
					/*2025-08-20 //extraConfVal may or maynot have +/-
					$extraConfVal *= $arrayExtrasRows[$remuneration_extras_id]['value_group'];
					*/
					$extraConfVal *= $extraConfGrp;
					$extraRateId = $termPaymentHeader[0]->extra_rate_id; 
					/*2025-08-20 //extraRateVal is also abs()*/
					$extraRateVal = $termPaymentHeader[0]->extra_rate_val*-1;
					
					
				}
				
				//echo $headTermPaymentId;die;
				
				//4 merge array1, array2 into array3[id*, employee_term_payment_id*, remuneration_extra_id*1, payroll_profile_extra_id*2, 
				//  payment_period_id*$payment_period_id, employee_work_rate_id*$employee_work_rate_id, term_extra_entitle_amount*]
				$arrayTermExtrasRows = array();
				$termExtrasEntitleTotal = 0;
				//2025-08-20 
				//if term-payment is new then go through all extras to prepare header total and detail records.
				//else load selected extra-id only since the header term total is adjusted whenever 
				//check-in/check-out of extra payments for employee profile take place
				$arrayExtrasObjs = ($headTermPaymentId>0)?array($arrayExtrasRows[$remuneration_extras_id]):$arrayExtrasRows;
				//--
				foreach($arrayExtrasObjs as $aer){
					$payroll_profile_extras_id = isset($arrayProfileExtrasRows[$aer['id']])?$arrayProfileExtrasRows[$aer['id']]['id']:0;
					
					$term_extras_amount = 0;
					$term_extras_conf_id = 0;
					if($aer['id']==$remuneration_extras_id){
						$term_extras_amount = $request->input('employee_extra_entitle_amount')*$aer['value_group'];
						$term_extras_conf_id = $extraConfId;
					}else{
						$term_extras_amount = isset($arrayProfileExtrasRows[$aer['id']])?$arrayProfileExtrasRows[$aer['id']]['extra_entitle_amount']:0;
					}
					
					if($term_extras_amount!=0){
						$term_extras_real_value = $term_extras_amount;//($term_extras_amount*$aer['value_group']);
						/*$arrayTermExtrasRows[$remuneration_extras_id] = array('id'=>$term_extras_conf_id, 
														'employee_term_payment_id'=>$headTermPaymentId, 
											 'remuneration_extra_id'=>$aer['id'], 
											 'payroll_profile_extra_id'=>$payroll_profile_extras_id,
											 'payment_period_id'=>$payment_period_id, 'employee_work_rate_id'=>$employee_work_rate_id,
											 'term_extra_entitle_amount'=>$term_extras_real_value
											);*/
						$arrayTermExtrasRows[$aer['id']] = array('id'=>$term_extras_conf_id, 
														'employee_term_payment_id'=>$headTermPaymentId, 
											 'remuneration_extra_id'=>$aer['id'], 
											 'payroll_profile_extra_id'=>$payroll_profile_extras_id,
											 'payment_period_id'=>$payment_period_id, 'employee_work_rate_id'=>$employee_work_rate_id,
											 'term_extra_entitle_amount'=>$term_extras_real_value
											);
						$termExtrasEntitleTotal+=$term_extras_real_value;
					}
				}
				//4 if($remuneration_id==23) then calculate absentHours deduction to be included end of array3
				if($remuneration_id==23){
					$empHrsPaidRate = 0;
					$empLateHours = 0;
					
					if($emp_payslip_id>0){
						//$employeeWork = employeeWorkRate::find($request->employee_work_rate_id);
						$employeeWork = DB::select("select IFNULL(SUM(emp_late_hours), 0) as emp_late_hours ".
																	 "from employee_paid_rates where employee_payslip_id=?", 
														[$emp_payslip_id]);
						//if(!empty($employeeWork))
						if(!empty($employeeWork[0]->emp_late_hours)){
							//$empLateHours = $employeeWork->emp_late_hours/60;
							$empLateHours = ($employeeWork[0]->emp_late_hours>90)?(($employeeWork[0]->emp_late_hours-90)/60):0;
							
							$empLastPayslip = DB::select("select IFNULL(SUM(fig_value), 0) as fig_value ".
																	 "from employee_salary_payments ".
														 "where employee_payslip_id=? and fig_hidden=0 and ".
														 "remuneration_payslip_spec_code='BASIC'", 
														[$emp_payslip_id]);
							$empLastBasicFigval = $empLastPayslip[0]->fig_value;
							$empEligibleAdvance = PayrollProfileExtra::where(['payroll_profile_id'=>$request->payroll_profile_id,
																			 'remuneration_extra_id'=>'1',
																			 'payroll_profile_extra_signout'=>0])->first();
							$empAdvanceFigval = !empty($empEligibleAdvance)?$empEligibleAdvance->extra_entitle_amount:0;
							$empAdvancePeriodWorkhrs = 30*9;//30*9*60//240
							$empHrsPaidRate = ($empLastBasicFigval+$empAdvanceFigval)/$empAdvancePeriodWorkhrs;
						}
					}
					
					$absentDeduction = round(($empHrsPaidRate*$empLateHours)*-1, 2); // to do calculation
					$termExtrasEntitleTotal+=$absentDeduction;
					
					$arrayTermExtrasRows[0] = array('id'=>$extraRateId, 
													'employee_term_payment_id'=>$headTermPaymentId, 
												 'remuneration_extra_id'=>0, 
												 'payroll_profile_extra_id'=>0,
												 'payment_period_id'=>$payment_period_id, 'employee_work_rate_id'=>$employee_work_rate_id,
												 'term_extra_entitle_amount'=>$absentDeduction
												);
				}
				
				
				$employeeTermExtrasRows = NULL;
				
				//6 prepare array4 to insert/update employee_term_payments
				if($headTermPaymentId>0){
					$termPayment=EmployeeTermPayment::find($headTermPaymentId);
					$termPayment->updated_by=$request->user()->id;
					
					$termExtrasEntitleTotal=$headTermPaymentVal;//echo $termExtrasEntitleTotal.'x'.$extraConfVal.'x'.$extraRateVal.'<br />';
					$latestConfVal = $arrayTermExtrasRows[$remuneration_extras_id]['term_extra_entitle_amount'];
					$termExtrasEntitleTotal+=(($extraConfVal*-1)+$latestConfVal);//echo $termExtrasEntitleTotal.'<br />';
					$termExtrasEntitleTotal+=(($extraRateVal*-1)+$absentDeduction);//echo $termExtrasEntitleTotal.'<br />';die;
					
					if($extraRateId!=0){
						$employeeTermExtrasRows = array($arrayTermExtrasRows[$remuneration_extras_id], $arrayTermExtrasRows[0]);
					}else{
						$employeeTermExtrasRows = array($arrayTermExtrasRows[$remuneration_extras_id]);
					}
				}else {
					// Data not found. Here, you should make sure that the absence of $data won't break anything
					$termPayment=new EmployeeTermPayment;
					$employeeTermExtrasRows = $arrayTermExtrasRows;
					$termPayment->created_by=$request->user()->id;
				}
				
				//
				$termPayment->remuneration_id=$remuneration_id; 
				$termPayment->payroll_profile_id=$request->input('payroll_profile_id'); 
				$termPayment->payment_amount=$termExtrasEntitleTotal;//$request->input('eligible_amount'); 
				$termPayment->emp_payslip_no=$emp_payslip_no; 
				$termPayment->payment_cancel=0;
				//
				$processInit = $termPayment->save();
				$affectedRows = ($processInit)?1:0;
				$processComplete = true;
				
				$ext_rows = array();
				
				//7 loop array3[$request->input('remuneration_extra_id')], array3[0] to insert/update employee_term_payment_extras
				foreach($employeeTermExtrasRows as $etr){
					$extrasPayment = NULL;
					$remuneration_txt = isset($arrayExtrasRows[$remuneration_extras_id]['extras_label'])?$arrayExtrasRows[$remuneration_extras_id]['extras_label']:'Late deduction';
					
					$etrDataSave = true;
					
					if(!empty($etr['id'])){
						$extrasPayment = EmployeeTermPaymentExtra::find($etr['id']);
						$extrasPayment->term_extra_entitle_amount = $etr['term_extra_entitle_amount'];
						$extrasPayment->employee_term_payment_extra_cancel = 0;
						$extrasPayment->updated_by=$request->user()->id;
						$etrDataSave = $extrasPayment->save();
						
						$ext_rows[] = array('id'=>$etr['id'], 'remuneration_name'=>$remuneration_txt, 
											'payment_amount'=>$etr['term_extra_entitle_amount']);
					}else{
						$extrasPayment = new EmployeeTermPaymentExtra;
						$extrasPayment->employee_term_payment_id = $termPayment->id;
						$extrasPayment->remuneration_extra_id = $etr['remuneration_extra_id'];
						$extrasPayment->payroll_profile_extra_id = $etr['payroll_profile_extra_id'];
						$extrasPayment->payment_period_id = $etr['payment_period_id'];
						$extrasPayment->employee_work_rate_id = $etr['employee_work_rate_id'];
						$extrasPayment->term_extra_entitle_amount = $etr['term_extra_entitle_amount'];
						$extrasPayment->created_by=$request->user()->id;
						$etrDataSave = $extrasPayment->save();
						
						$ext_rows[] = array('id'=>$extrasPayment->id, 
											'remuneration_name'=>$remuneration_txt, 
											'payment_amount'=>$etr['term_extra_entitle_amount']);
					}
					
					if(!$etrDataSave){
						$processComplete = false;
					}
					
				}
				
				if(!(($affectedRows==1) && $processComplete)){
					throw new \Exception('Payment details cannot be updated.');
				}else{
					return response()->json(['success' => 'Payment Added Successfully.', 'term_id'=>$termPayment->id, 
										'term_grpid'=>$remuneration_id, 'term_total'=>$termExtrasEntitleTotal, 'term_cancel'=>0, 
										'new_rows'=>$ext_rows, 'new_obj'=>$termPayment]);
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'errors'=>$e->getMessage()));
		}	
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeTermPayment  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeTermPayment $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeTermPayment  $loan
     * @return \Illuminate\Http\Response
     */
    public function reviewPaymentList($id)
    {
        if(request()->ajax())
        {
            $employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$id])
								->latest()
								->first();
			$emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);
			
			$employeePayProfile = PayrollProfile::find($id);
			$empPayPeriodId = 0;
			$empWorkRateId = 0;
			
			$paymentPeriod=PaymentPeriod::where(['payroll_process_type_id'=>$employeePayProfile->payroll_process_type_id,
												 'employee_payday_id'=>$employeePayProfile->employee_payday_id])
							->latest()
							->first();
			
			$exposeTermId = true; // based on advance date
			
			if(!empty($paymentPeriod)){
				$empPayPeriodId = $paymentPeriod->id;
				
				if(!empty($paymentPeriod->advance_payment_date)){
					$advancePaymentDate = Carbon::parse($paymentPeriod->advance_payment_date);
					$revDate = Carbon::today();
					if(!$advancePaymentDate->gte($revDate)){
						$exposeTermId = false;
					}
				}
			}
			
			/*
			$employeeWorkRate=employeeWorkRate::where(['emp_id'=>$employeePayProfile->emp_id])
							->latest()
							->first();
			
			if(!empty($employeeWorkRate)){
				$empWorkRateId = $employeeWorkRate->id;
			}
			*/
			$employeePaydata = EmployeePayslip::where(['payroll_profile_id'=>$id, 'payslip_cancel'=>0])
										->latest()
										->first();
			$empWorkRateId = !empty($employeePaydata)?$employeePaydata->id:0;
			
			$payment_rows = DB::table('employee_term_payments')
							->join('employee_term_payment_extras', 'employee_term_payments.id', '=', 'employee_term_payment_extras.employee_term_payment_id')
							->leftjoin('remuneration_extras', 'employee_term_payment_extras.remuneration_extra_id', '=', 'remuneration_extras.id')
							->select('employee_term_payments.remuneration_id', 'remuneration_extras.extras_label', 'employee_term_payment_extras.id', 'employee_term_payments.payment_amount', 'employee_term_payment_extras.employee_term_payment_id', 'employee_term_payments.payment_cancel', 'employee_term_payment_extras.term_extra_entitle_amount', 'employee_term_payment_extras.created_at', 'employee_term_payment_extras.updated_at', 'employee_term_payment_extras.remuneration_extra_id')
							->whereYear('employee_term_payments.created_at', Carbon::now()->year)
							->whereMonth('employee_term_payments.created_at', Carbon::now()->month)
							->where(['employee_term_payments.payroll_profile_id'=>$id, 
									 'employee_term_payments.emp_payslip_no'=>$emp_payslip_no, 
									 //'employee_term_payments.payment_cancel'=>0,
									 'employee_term_payment_extras.payment_period_id'=>$empPayPeriodId,
									 'employee_term_payment_extras.employee_work_rate_id'=>$empWorkRateId,
									 'employee_term_payment_extras.employee_term_payment_extra_cancel'=>0,
									 ])
							->get();
			
			$term_id = 0; $term_grpid = 0;
			$term_total = 0;
			$term_cancel = 1;
			/*
			var_dump($payment_rows->first());die;
			
			@foreach ($users as $user)
			 @if ($loop->first)
			  This is the first iteration.
			 @endif
			 @if ($loop->last)
			  This is the last iteration.
			 @endif
			 <p>This is user {{ $user->id }}</p>
			@endforeach
			*/
			/*
			if(!empty($payment_rows->first())){
				$term_prow = $payment_rows->first();
				$term_id = $term_prow->employee_term_payment_id;//$payment_rows[0]->employee_term_payment_id;
				$term_grpid = $term_prow->remuneration_id;
				$term_total = $term_prow->payment_amount;
				$term_cancel = $term_prow->payment_cancel;
			}
			*/
			
			$footer_data=array();
			
			$payment_list=array();
			foreach($payment_rows as $r){
				$payment_date=($r->updated_at=='')?$r->created_at:$r->updated_at;
				$remuneration_txt = !empty($r->extras_label)?$r->extras_label:'Late deduction';
				$payment_list[]=array('id'=>$r->id, 'remuneration_name'=>$remuneration_txt, 'payment_date'=>date('Y-m-d', strtotime($payment_date)), 'payment_amount'=>abs($r->term_extra_entitle_amount), 'payment_cancel'=>0);
				
				$footer_data[$r->remuneration_id] = array('term_id'=>($exposeTermId?$r->employee_term_payment_id:'-1'), 
														  'term_grpid'=>$r->remuneration_id, //not-necessary
														'term_total'=>$r->payment_amount, 'term_cancel'=>$r->payment_cancel);
			}
			
			/**/
			/**/
			
            return response()->json(['period_id'=>$empPayPeriodId, 'work_id'=>$empWorkRateId, 'package'=>$payment_list, 
									 'footer_data'=>$footer_data]);
									/*'term_id'=>$term_id, 'term_grpid'=>$term_grpid, 'term_total'=>$term_total, 
									 'term_cancel'=>$term_cancel*/
        }
    }
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeTermPayment  $loan
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		/*if(request()->ajax()){
			$data=DB::table('employee_term_payments')
				->select('id', 'payment_amount')
				->where(['id'=>$id])
				->get();
			
			return response()->json(['term_obj'=>$data[0]]);
		}*/
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeTermPayment  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeTermPayment $loan)
    {
		/*$rules = array(
            'new_allocated_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
		
		$payment_info=EmployeeTermPayment::whereId($request->hidden_term_id)->get();
		//
		//check-whether-payslip-no-is-processed-or-not-before-changing-payment-info
		$payslip_info=DB::select("SELECT COUNT(*) AS payment_processed FROM employee_payslips WHERE payroll_profile_id=? AND emp_payslip_no=?", [$payment_info[0]->payroll_profile_id, $payment_info[0]->emp_payslip_no]);
		
		if($payslip_info[0]->payment_processed==1){
			return response()->json(['errors' => array('Payslip details already processed')]);
		}
		
		$form_data = array(
            'payment_amount' =>  $request->new_allocated_amount,
			'updated_by' => $request->user()->id
            
        );

        EmployeeTermPayment::whereId($request->hidden_term_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'new_id'=>$request->hidden_term_id]);*/
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeTermPayment  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
		
	}
	
	
	/**/
	
	public function freeze(Request $request){
		try{
			return DB::transaction(function() use ($request){
				if($request->ajax()){
					$paymentPeriod = PaymentPeriod::find($request->input('payment_period_id'));
					
					if(!empty($paymentPeriod)){
						if(empty($paymentPeriod->advance_payment_date)){
							return response()->json(['result'=>'error', 
													 'msg' => array('Advance payment date not specified in payment schedule')]
													);
						}else{
							$advancePaymentDate = Carbon::parse($paymentPeriod->advance_payment_date);
							$revDate = Carbon::today();
							//if(!$advancePaymentDate->isAfter($revDate))
							if(!$advancePaymentDate->gte($revDate)){
								return response()->json(['result'=>'error', 
														 'msg' => array('Advance payment closed on '.$paymentPeriod->advance_payment_date)]
														);
							}
						}
					}else{
						return response()->json(['result'=>'error', 
												 'msg' => array('Payment schedule not available')]
												);
					}
					
					
					//2025-11-24-check-payslip-to-disallow-advance-payment-for-periods-with-employee-salaries
					$actPeriodPayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id, 
																'payment_period_id'=>$request->input('payment_period_id')])
											->latest()
											->first();
					if(!empty($actPeriodPayslip)){
						return response()->json(['result'=>'error', 
												 'msg' => array('Advance payments cannot be issued to salary processed employees')]
												);
					}
					//--
					
					
					$employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id])
										->latest()
										->first();
					$emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);
					
					$employeePaydata = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id,
															   'payslip_cancel'=>0])
										->latest()
										->first();
					$emp_payslip_id = !empty($employeePaydata)?$employeePaydata->id:0;
					
					//1 find $remuneration_id, $payment_period_id, $employee_work_rate_id
					$remunerationExtras=RemunerationExtra::find($request->input('remuneration_extra_id'));
					$remuneration_id=$remunerationExtras->remuneration_id;
					$remuneration_extras_id=$request->input('remuneration_extra_id');
					$opt_entitlement=$remunerationExtras->extra_entitlement;
					$employee_work_rate_id=$emp_payslip_id;//$request->input('employee_work_rate_id');
					$payment_period_id = $request->input('payment_period_id');
					
					//to-do: check validity of $request work_id, period_id fetched on function reviewPaymentList(...)
					
					//2 load array1[id, value_group] of remuneration_extras
					$tblExtras=DB::table('remuneration_extras')
								->select('id', 'extras_label', 'value_group')
								->where(['remuneration_id'=>$remuneration_id, 'remuneration_extra_cancel'=>0])
								->get();
					$arrayExtrasRows = array();
					foreach($tblExtras as $ae){
						$arrayExtrasRows[$ae->id]=array('id'=>$ae->id, 'extras_label'=>$ae->extras_label, 'value_group'=>$ae->value_group);
					}
					
					//3 load array2[id, remuneration_extra_id, extra_entitle_amount] of payroll_profile_extras
					//3 not possible to limit $remuneration_extras_id, getting 1 record or empty result since 
					//3 a profile may contain fixed extras; for eg. Employee Salary Advance
					$tblProfileExtras=DB::table('payroll_profile_extras')
										->select('id', 'remuneration_extra_id', 'extra_entitle_amount')
										->where(['payroll_profile_id'=>$request->payroll_profile_id, 
												 'remuneration_id'=>$remuneration_id, //'remuneration_extra_id'=>$remuneration_extras_id,
												 'payroll_profile_extra_signout'=>0])
										->get();
					$arrayProfileExtrasRows = array();
					
					foreach($tblProfileExtras as $ape){
						//profiles not include +/- for entitle values, therefore applied here
						$value_group_entitle_amount = $ape->extra_entitle_amount*$arrayExtrasRows[$ape->remuneration_extra_id]['value_group'];
						
						$arrayProfileExtrasRows[$ape->remuneration_extra_id]=array('id'=>$ape->id, 
																			'remuneration_extra_id'=>$ape->remuneration_extra_id,
																			'extra_entitle_amount'=>$value_group_entitle_amount);
					}
					
					//* swap 4, 5 sequence
					/*5. select max(id*(remuneration_extra_id=$request->input('remuneration_extra_id'))) as extra_conf_id, 
					   max(id*(remuneration_extra_id=0)) as extra_rate_id, 
					   employee_term_payment_id, payment_amount 
					   from employee_term_payment_extras where employee_term_payments.payroll_profile_id=? and
					   employee_term_payment_extras.payment_period_id=$payment_period_id and 
					   employee_term_payment_extras.employee_work_rate_id=$employee_work_rate_id
					   group by employee_term_payment_id*/
					$termPaymentHeader=DB::select("SELECT ifnull(employee_term_payments.id, 0) as id, ".
						"ifnull(max(employee_term_payment_extras.id*(employee_term_payment_extras.".
						"remuneration_extra_id=?)), 0) as extra_conf_id, ".
						"ifnull(max(employee_term_payment_extras.id*(employee_term_payment_extras.".
						"remuneration_extra_id=0)), 0) as extra_rate_id, ".
						"ifnull(employee_term_payments.payment_amount, 0) as payment_amount, ".
						"ifnull(max(ABS(employee_term_payment_extras.term_extra_entitle_amount)*(employee_term_payment_extras.".
						"remuneration_extra_id=?)), 0) as extra_conf_val, ".
						"ifnull(max(ABS(employee_term_payment_extras.term_extra_entitle_amount)*(employee_term_payment_extras.".
						"remuneration_extra_id=0)), 0) as extra_rate_val, ".
						"ifnull(employee_term_payments.payment_cancel, 0) as term_cancel ".
						"FROM (select id, payroll_profile_id, payment_amount, payment_cancel, DATE(created_at) as created_date ".
									"from employee_term_payments ".
									"where payroll_profile_id=? and emp_payslip_no=? AND remuneration_id=? ".
									"and DATE_FORMAT(created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m')) as employee_term_payments ".
						"inner join employee_term_payment_extras ".
						"on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ".
						"WHERE employee_term_payment_extras.payment_period_id=? AND employee_term_payment_extras.employee_work_rate_id=?", 
						[$remuneration_extras_id, $remuneration_extras_id, $request->payroll_profile_id, $emp_payslip_no, $remuneration_id, 
						 $payment_period_id, $employee_work_rate_id
						]);
					
					$headTermPaymentId = 0; $termCancel = 0;
					$headTermPaymentVal = 0;
					$extraConfId = 0; $extraConfVal = 0;
					$extraRateId = 0; $extraRateVal = 0;
					$absentDeduction = 0;
					if($termPaymentHeader[0]->id!=0){
						//return response()->json(['errors' => array('Payslip details already processed')]);
						$headTermPaymentId = $termPaymentHeader[0]->id; 
						$termCancel = $termPaymentHeader[0]->term_cancel;
						$headTermPaymentVal = $termPaymentHeader[0]->payment_amount;
						$extraConfId = $termPaymentHeader[0]->extra_conf_id; 
						
						//$extraConfVal = $termPaymentHeader[0]->extra_conf_val*$arrayExtrasRows[$remuneration_extras_id]['value_group'];
						$extraConfVal = $termPaymentHeader[0]->extra_conf_val;
						$extraConfGrp = $arrayExtrasRows[$remuneration_extras_id]['value_group'];
						if(isset($arrayProfileExtrasRows[$remuneration_extras_id])&&($extraConfId=='0')&&($opt_entitlement=='1')){
							$extraConfVal = $arrayProfileExtrasRows[$remuneration_extras_id]['extra_entitle_amount'];
							$extraConfGrp = 1;//arrayProfileExtrasRows values transformed to +/- earlier
						}
						/*2025-08-20 //extraConfVal may or maynot have +/-
						$extraConfVal *= $arrayExtrasRows[$remuneration_extras_id]['value_group'];
						*/
						$extraConfVal *= $extraConfGrp;
						$extraRateId = $termPaymentHeader[0]->extra_rate_id; 
						/*2025-08-20 //extraRateVal is also abs()*/
						$extraRateVal = $termPaymentHeader[0]->extra_rate_val*-1;
						
						
					}
					
					//4 merge array1, array2 into array3[id*, employee_term_payment_id*, remuneration_extra_id*1, payroll_profile_extra_id*2, 
					//  payment_period_id*$payment_period_id, employee_work_rate_id*$employee_work_rate_id, term_extra_entitle_amount*]
					$arrayTermExtrasRows = array();
					$termExtrasEntitleTotal = 0;
					//2025-08-20 
					//if term-payment is new then go through all extras to prepare header total and detail records.
					//else load selected extra-id only since the header term total is adjusted whenever 
					//check-in/check-out of extra payments for employee profile take place
					$arrayExtrasObjs = ($headTermPaymentId>0)?array($arrayExtrasRows[$remuneration_extras_id]):$arrayExtrasRows;
					//--
					foreach($arrayExtrasObjs as $aer){
						$payroll_profile_extras_id = isset($arrayProfileExtrasRows[$aer['id']])?$arrayProfileExtrasRows[$aer['id']]['id']:0;
						
						$term_extras_amount = 0;
						$term_extras_conf_id = 0;
						$term_extras_conf_cancel = 0;
						if($aer['id']==$remuneration_extras_id){
							$term_extras_amount = $request->input('payment_amount')*$aer['value_group'];
							$term_extras_conf_id = $extraConfId;
							$term_extras_conf_cancel = $request->payment_cancel;//2025-01-16
						}else{
							$term_extras_amount = isset($arrayProfileExtrasRows[$aer['id']])?$arrayProfileExtrasRows[$aer['id']]['extra_entitle_amount']:0;
						}
						
						if($term_extras_amount!=0){
							$term_extras_real_value = ($term_extras_amount)*(1-$term_extras_conf_cancel);
							/*$arrayTermExtrasRows[$remuneration_extras_id] = array('id'=>$term_extras_conf_id, 
															'employee_term_payment_id'=>$headTermPaymentId, 
												 'remuneration_extra_id'=>$aer['id'], 
												 'payroll_profile_extra_id'=>$payroll_profile_extras_id,
												 'payment_period_id'=>$payment_period_id, 'employee_work_rate_id'=>$employee_work_rate_id,
												 'term_extra_entitle_amount'=>$term_extras_real_value,
												 'employee_term_payment_extra_cancel'=>$term_extras_conf_cancel
												);*/
							$arrayTermExtrasRows[$aer['id']] = array('id'=>$term_extras_conf_id, 
															'employee_term_payment_id'=>$headTermPaymentId, 
												 'remuneration_extra_id'=>$aer['id'], 
												 'payroll_profile_extra_id'=>$payroll_profile_extras_id,
												 'payment_period_id'=>$payment_period_id, 'employee_work_rate_id'=>$employee_work_rate_id,
												 'term_extra_entitle_amount'=>$term_extras_real_value,
												 'employee_term_payment_extra_cancel'=>$term_extras_conf_cancel
												);
							$termExtrasEntitleTotal+=$term_extras_real_value;
						}
					}
					
					$lidd=array();
					
					//4 if($remuneration_id==23) then calculate absentHours deduction to be included end of array3
					if($remuneration_id==23){
						$empHrsPaidRate = 0;
						$empLateHours = 0;
						
						if($emp_payslip_id>0){
							//$employeeWork = employeeWorkRate::find($request->employee_work_rate_id);
							$employeeWork = DB::select("select IFNULL(SUM(emp_late_hours), 0) as emp_late_hours ".
																	 "from employee_paid_rates where employee_payslip_id=?", 
														[$emp_payslip_id]);
							//if(!empty($employeeWork))
							if(!empty($employeeWork[0]->emp_late_hours)){
								//$empLateHours = $employeeWork->emp_late_hours/60;
								$empLateHours = ($employeeWork[0]->emp_late_hours>90)?(($employeeWork[0]->emp_late_hours-90)/60):0;
								//$lidd['hrs']=$empLateHours;
								$empLastPayslip = DB::select("select IFNULL(SUM(fig_value), 0) as fig_value ".
																		 "from employee_salary_payments ".
															 "where employee_payslip_id=? and fig_hidden=0 and ".
															 "remuneration_payslip_spec_code='BASIC'", 
															[$emp_payslip_id]);
								$empLastBasicFigval = $empLastPayslip[0]->fig_value;
								//$lidd['basic']=$empLastBasicFigval;
								$empEligibleAdvance = PayrollProfileExtra::where(['payroll_profile_id'=>$request->payroll_profile_id,
																				 'remuneration_extra_id'=>'1',
																				 'payroll_profile_extra_signout'=>0])->first();
								$empAdvanceFigval = !empty($empEligibleAdvance)?$empEligibleAdvance->extra_entitle_amount:0;
								//$lidd['adv']=$empAdvanceFigval;
								$empAdvancePeriodWorkhrs = 30*9;//30*9*60//240
								$empHrsPaidRate = ($empLastBasicFigval+$empAdvanceFigval)/$empAdvancePeriodWorkhrs;
								//$lidd['rate']=$empHrsPaidRate;
							}
						}
						
						$absentDeduction = round(($empHrsPaidRate*$empLateHours)*-1, 2); // to do calculation
						$termExtrasEntitleTotal+=$absentDeduction;
						//$lidd['lateded']=$absentDeduction;
						
						$arrayTermExtrasRows[0] = array('id'=>$extraRateId, 
														'employee_term_payment_id'=>$headTermPaymentId, 
													 'remuneration_extra_id'=>0, 
													 'payroll_profile_extra_id'=>0,
													 'payment_period_id'=>$payment_period_id, 'employee_work_rate_id'=>$employee_work_rate_id,
													 'term_extra_entitle_amount'=>$absentDeduction,
													 'employee_term_payment_extra_cancel'=>0
													);
					}
					
					
					
					
					$form_data = array(
						'remuneration_id' => $remuneration_id,
						'payroll_profile_id' => $request->payroll_profile_id,
						'emp_payslip_no' => $emp_payslip_no,
						'payment_amount' => 0,
						'payment_cancel' => 0
					);
					
					$affectedRows=0;
					$affectedMode=1;//accept-changes
					
					$processComplete = true;
					
					$payment_id='';//$request->id;
					
					$employeeTermExtrasRows = NULL;
					
					//6 prepare array4 to insert/update employee_term_payments
					if($headTermPaymentId>0){
						$payment_id=$headTermPaymentId;
						
					}else {
						$payment_id='';//$request->id;
						/**/
						$affectedMode=($request->payment_cancel==0)?1:0;
					}
					
					if($affectedMode==1){
						if($payment_id==''){
							$form_data['payment_amount']=$termExtrasEntitleTotal;
							$form_data['created_by']=$request->user()->id;
							$form_data['created_at']=date('Y-m-d H:i:s');
							
							$affectedRows=DB::table('employee_term_payments')
								->insert($form_data);
							$payment_id=DB::getPdo()->lastInsertId();
							$headTermPaymentId=$payment_id;
							
							$employeeTermExtrasRows = $arrayTermExtrasRows;
						}else{
							$termExtrasEntitleTotal=$headTermPaymentVal;
							$latestConfVal = $arrayTermExtrasRows[$remuneration_extras_id]['term_extra_entitle_amount'];
							$termExtrasEntitleTotal+=(($extraConfVal*-1)+$latestConfVal);
							$termExtrasEntitleTotal+=(($extraRateVal*-1)+$absentDeduction);
							
							$form_data['payment_amount']=$termExtrasEntitleTotal;
							
							$form_data['updated_by']=$request->user()->id;
							$form_data['updated_at']=date('Y-m-d H:i:s');
							
							$affectedRows=DB::table('employee_term_payments')
								->where(['id'=>$payment_id, ]) // 'payment_cancel'=>(1-$request->payment_cancel)
								->update($form_data);
							
							
							if($extraRateId!=0){
								$employeeTermExtrasRows = array($arrayTermExtrasRows[$remuneration_extras_id], $arrayTermExtrasRows[0]);
							}else{
								$employeeTermExtrasRows = array($arrayTermExtrasRows[$remuneration_extras_id]);
							}
						}
						
						
						//7 loop array3[$request->input('remuneration_extra_id')], array3[0] to insert/update employee_term_payment_extras
						foreach($employeeTermExtrasRows as $etr){
							$extrasPayment = NULL;
							
							$etrDataSave = true;
							
							if(!empty($etr['id'])){
								$extrasPayment = EmployeeTermPaymentExtra::find($etr['id']);
								$extrasPayment->term_extra_entitle_amount = $etr['term_extra_entitle_amount'];
								$extrasPayment->employee_term_payment_extra_cancel = $etr['employee_term_payment_extra_cancel'];//2025-01-16
								$extrasPayment->updated_by=$request->user()->id;
								$etrDataSave = $extrasPayment->save();
								
								$arrayTermExtrasRows[$etr['remuneration_extra_id']]['id'] = $etr['id'];
							}else{
								$extrasPayment = new EmployeeTermPaymentExtra;
								$extrasPayment->employee_term_payment_id = $headTermPaymentId;
								$extrasPayment->remuneration_extra_id = $etr['remuneration_extra_id'];
								$extrasPayment->payroll_profile_extra_id = $etr['payroll_profile_extra_id'];
								$extrasPayment->payment_period_id = $etr['payment_period_id'];
								$extrasPayment->employee_work_rate_id = $etr['employee_work_rate_id'];
								$extrasPayment->term_extra_entitle_amount = $etr['term_extra_entitle_amount'];
								$extrasPayment->created_by=$request->user()->id;
								$etrDataSave = $extrasPayment->save();
								
								$arrayTermExtrasRows[$etr['remuneration_extra_id']]['id'] = $extrasPayment->id;
							}
							
							if(!$etrDataSave){
								$processComplete = false;
							}
						}
						
						
					}
					/*
					$result = array('result'=>(($affectedRows==1)?'success':'error'), 
										'payment_id'=>$arrayTermExtrasRows[$remuneration_extras_id]['id'], //'lp'=>$lidd,
									);
					*/
					if(!(($affectedRows==1) && $processComplete)){
						throw new \Exception('Payment details cannot be updated.');
					}else{
						$result = array('result'=>'success', 
											'payment_id'=>$arrayTermExtrasRows[$remuneration_extras_id]['id'], //'lp'=>$lidd,
										);
						return response()->json($result);
					}
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}	
	}
	
	/*
	method checkPayment
	if(filter_by){
		Salary Preperation
		------------------
		not-required
	}else{
		Salary Addition
		------------------
		prepare a list of employees eligible for a particular term payment
	}
	*/
	public function checkExtras(Request $request){
		if($request->ajax()){
			$remunerationExtra = RemunerationExtra::find($request->id);
			
			$employee_list = array();
			$sql_advlist = "SELECT employees.emp_name_with_initial AS emp_first_name, payroll_profiles.id as payroll_profile_id, payroll_process_types.process_name, payroll_profiles.basic_salary, companies.name as location, IFNULL(drv_extras.id, '') as payment_id, IFNULL(drv_extras.employee_term_payment_id, 0) as employee_term_payment_id, IFNULL(drv_workrates.id, 0) as employee_work_rate_id, IFNULL(drv_periods.id, 0) as payment_period_id, IFNULL(drv_ent.extra_entitle_amount, 0) AS ent_amount, IFNULL(drv_extras.payment_cancel, 1) AS payment_cancel FROM (select id, emp_name_with_initial, emp_company from employees where deleted=0 AND is_resigned=0) AS employees INNER JOIN companies ON employees.emp_company=companies.id INNER JOIN payroll_profiles ON employees.id=payroll_profiles.emp_id INNER JOIN payroll_process_types ON payroll_profiles.payroll_process_type_id=payroll_process_types.id LEFT OUTER JOIN (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips GROUP BY payroll_profile_id) AS employee_payslips ON payroll_profiles.id=employee_payslips.payroll_profile_id LEFT OUTER JOIN (SELECT id, payroll_profile_id, emp_payslip_no FROM employee_term_payments WHERE remuneration_id=? AND DATE_FORMAT(created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m')) as drv_term ON (payroll_profiles.id=drv_term.payroll_profile_id AND IFNULL(employee_payslips.emp_payslip_no+1, 1)=drv_term.emp_payslip_no) ";
			
			/*
			$sql_advlist .= "LEFT OUTER JOIN (select payroll_process_type_id, MAX(id) as id from payment_periods GROUP BY payroll_process_type_id) as drv_periods ON payroll_profiles.payroll_process_type_id=drv_periods.payroll_process_type_id LEFT OUTER JOIN (select emp_id, MAX(id) as id from employee_work_rates GROUP BY emp_id) as drv_workrates ON payroll_profiles.emp_id=drv_workrates.emp_id LEFT OUTER JOIN (select id, employee_term_payment_id, payment_period_id, employee_work_rate_id, employee_term_payment_extra_cancel as payment_cancel from employee_term_payment_extras where remuneration_extra_id=?) as drv_extras ON (drv_term.id=drv_extras.employee_term_payment_id AND IFNULL(drv_periods.id, 0)=drv_extras.payment_period_id AND IFNULL(drv_workrates.id, 0)=drv_extras.employee_work_rate_id)" ;
			*/
			$sql_advlist .= "LEFT OUTER JOIN (select payroll_process_type_id, employee_payday_id, MAX(id) as id from payment_periods GROUP BY payroll_process_type_id, employee_payday_id) as drv_periods ON (payroll_profiles.payroll_process_type_id=drv_periods.payroll_process_type_id AND payroll_profiles.employee_payday_id=drv_periods.employee_payday_id) ";
			
			/*
			LEFT OUTER JOIN (select emp_id, MAX(id) as id from employee_work_rates GROUP BY emp_id) as drv_workrates ON payroll_profiles.emp_id=drv_workrates.emp_id 
			*/
			$sql_advlist .= "LEFT OUTER JOIN (select payroll_profile_id, MAX(id) as id from employee_payslips WHERE payslip_cancel=0 GROUP BY payroll_profile_id) as drv_workrates ON payroll_profiles.id=drv_workrates.payroll_profile_id ";
			
			$sql_advlist .= "LEFT OUTER JOIN (select id, employee_term_payment_id, payment_period_id, employee_work_rate_id, employee_term_payment_extra_cancel as payment_cancel from employee_term_payment_extras where remuneration_extra_id=?) as drv_extras ON (drv_term.id=drv_extras.employee_term_payment_id AND IFNULL(drv_periods.id, 0)=drv_extras.payment_period_id AND IFNULL(drv_workrates.id, 0)=drv_extras.employee_work_rate_id) ";
			
			$sql_advlist .= "LEFT OUTER JOIN (select id as ent_id, payroll_profile_id, extra_entitle_amount from payroll_profile_extras where remuneration_extra_id=? and payroll_profile_extra_signout=0) AS drv_ent ON payroll_profiles.id=drv_ent.payroll_profile_id ";
			
			$sql_advlist .= "WHERE payroll_profiles.employee_payday_id=?";
			
			if($request->entopt=='1'){
				$sql_advlist .= " ";
				$sql_advlist .= "AND drv_ent.ent_id IS NOT NULL";
			}
			
			$employee = DB::select($sql_advlist, [$remunerationExtra->remuneration_id, $request->id, $request->id, $request->payment_group]);
			
			foreach($employee as $r){
				$employee_list[]=array('id'=>$r->payment_id, 'payroll_profile_id'=>$r->payroll_profile_id, 'emp_first_name'=>$r->emp_first_name, 'location'=>$r->location, 'basic_salary'=>$r->basic_salary, 'process_name'=>(isset($r->process_name)?$r->process_name:''), 'head_id'=>$r->employee_term_payment_id, 'work_id'=>$r->employee_work_rate_id, 'period_id'=>$r->payment_period_id, 'ent_amount'=>$r->ent_amount, 'payment_cancel'=>$r->payment_cancel);
			}
			
			return response()->json(['employee_detail'=>$employee_list]);
		}
	}
	
	/*
	public function uploadFromFile(Request $request){
		
	}
	
	public function downloadTermPayment(Request $request){
		
	}
	*/
	
	public function reportExtraPayment()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		$remuneration_list=Remuneration::whereIn('id', [23, 24])->where(['remuneration_cancel'=>0])->orderBy('id', 'asc')->get();
        return view('Payroll.termPayment.extrasPaysheet_list',compact('branch', 'department', 'payroll_process_type', 'payment_period', 'remuneration_list'));
    }
	
	public function reportPayRegister()
    {
        $branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		$department=DB::select("select id, company_id, name from departments");
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
        return view('Payroll.termPayment.ExtrasPayRegister_list',compact('branch', 'department', 'payroll_process_type', 'payment_period'));
    }
	
	public function checkPaymentList(Request $request){
		if ($request->ajax()) {
            $rules = array(
                'remuneration_id' => 'required',//'payroll_process_type_id' => 'required', //'period_filter_id' => 'required'
				'location_filter_id' => 'required',
				'payroll_process_type_id' => 'required',
				'period_filter_id' => 'required'
            );
			/*
			if($request->location_filter_id=='-1'){
				return response()->json(['errors' => array('Select a Branch')]);
			}
			*/
			$error = Validator::make($request->all(), $rules);
	
			if($error->fails())
			{
				return response()->json(['errors' => $error->errors()->all()]);
			}
			
			$payroll_process_types = array('1'=>'Monthly', '2'=>'Weekly', '3'=>'Bi-weekly', '4'=>'Daily');
			
			$paymentPeriod=PaymentPeriod::find($request->period_filter_id);//0;//
			
			
			$emp_sal_held_col = '0';
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
			
			$payment_period_id = 0;
			$payment_period_fr = 'X';
			$payment_period_to = 'X';
			$employee = NULL;
			
			$sqladvance = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_epfno, 
			   drv_emp.emp_first_name, drv_emp.emp_national_id, drv_emp.emp_last_name, 
			   drv_emp.e_id, drv_emp.occupation_group_id, 
			   drv_emp.location,
			   drv_emp.bank_ac_no,
			   drv_emp.bank_code,
			   drv_emp.branch_code,
			   drv_emp.payslip_held,
			   drv_emp.payslip_approved, 
			   drv_work.work_days, 
			   'ADDITION' AS fig_group_title, 'ADDITION' AS fig_group,
			   drv_emp.payment_amount AS fig_value, drv_emp.created_date,
			   0 AS epf_payable, 'OTHER_REM' AS remuneration_pssc
					FROM (SELECT payroll_profiles.id AS emp_payslip_id, employees.emp_id AS emp_epfno,
								 employees.emp_name_with_initial AS emp_first_name, 
								 employees.emp_national_id, employees.emp_last_name, 
								 employees.id as e_id, ifnull(job_titles.occupation_group_id, 0) as occupation_group_id, 
								 companies.name AS location,
								 ebanks.bank_ac_no,
								 ebanks.bank_code,
								 ebanks.branch_code, employee_term_payments.payment_amount, 
								 DATE(employee_term_payments.created_at) as created_date, 
								 0 AS payslip_held, 
								 1 AS payslip_approved 
								FROM `employee_term_payments` 
									INNER JOIN payroll_profiles ON employee_term_payments.payroll_profile_id=payroll_profiles.id
									INNER JOIN employees ON payroll_profiles.emp_id=employees.id
									INNER JOIN companies ON employees.emp_company=companies.id 
									inner join job_titles on employees.emp_job_code=job_titles.id 
									LEFT JOIN employee_banks as ebanks ON ebanks.emp_id = employees.id ".
								//"WHERE DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ".
								"WHERE (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ".
								  "and employee_term_payments.remuneration_id = ? 
								  AND ".$emp_location_col."=? 
								  AND ".$emp_department_col."=? 
								  AND employee_term_payments.payment_cancel=0 AND ".$emp_sal_held_col."='0'
						) AS drv_emp 
					CROSS JOIN (SELECT 15 as work_days) as drv_work 
			ORDER BY drv_emp.emp_epfno";

			$employee = DB::select($sqladvance, [$paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $request->remuneration_id, $emp_location_val, $emp_department_val]);
			
			$employee_list = array();
			
			foreach($employee as $r){
				$employee_list[]=array('id'=>$r->emp_payslip_id, 'ref_no'=>'-', 'emp_name'=>$r->emp_first_name, 
									   'bank_br_code'=>'', 'account_no'=>'', 'trx_code'=>'', 
									   'amount'=>$r->fig_value, 'date'=>$r->created_date);
			}
			
			return response()->json(['employee_detail'=>$employee_list, 
									 'payment_period_id'=>$payment_period_id, 
									 'work_date_fr'=>$payment_period_fr, 
									 'work_date_to'=>$payment_period_to]);
		}
	}
	
	public function downloadSalarySheet(Request $request){
		$companyRegInfo = Company::find($request->rpt_location_id);
		$company_name = $companyRegInfo->name;
		$company_addr = $companyRegInfo->address;
		
		$emp_sal_held_col = '0';
		//$emp_location_col = '1';
		$emp_department_col = '2';
		//$emp_location_val = '1';
		$emp_department_val = '2';
		
		$emp_location_col = "employees.emp_company";//"employees.emp_location";
		$emp_location_val = $request->rpt_location_id;
		
		if(!empty($request->rpt_dept_id)){
			$emp_department_col = "employees.emp_department";
			$emp_department_val = $request->rpt_dept_id;
		}
		
		$paymentPeriod=PaymentPeriod::find($request->rpt_period_id);//0;//
			
		$payment_period_id=0;//$paymentPeriod->id;//1;
		$payment_period_fr='';//$paymentPeriod->payment_period_fr;//$request->work_date_fr;
		$payment_period_to='';//$paymentPeriod->payment_period_to;//$request->work_date_to;
		
		$sqladvance = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_epfno, drv_emp.emp_disp_epfno, 
			   drv_emp.emp_first_name, drv_emp.emp_national_id, drv_emp.emp_last_name, 
			   drv_emp.emp_designation, drv_emp.dept_name, 
			   drv_emp.e_id, 
			   drv_emp.bank_ac_no,
			   drv_emp.bank_code,
			   drv_emp.branch_code,
			   drv_emp.remuneration_name,
			   drv_emp.payment_amount AS fig_value, drv_emp.created_date 
					FROM (SELECT payroll_profiles.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno AS emp_disp_epfno,
								 employees.emp_name_with_initial AS emp_first_name, 
								 employees.emp_national_id, employees.emp_last_name, 
								 job_titles.title AS emp_designation, departments.name as dept_name, 
								 employees.id as e_id, remunerations.remuneration_name, 
								 ebanks.bank_ac_no,
								 ebanks.bank_code,
								 ebanks.branch_code, employee_term_payments.payment_amount, 
								 DATE(employee_term_payments.created_at) as created_date 
								FROM `employee_term_payments` 
									INNER JOIN remunerations on employee_term_payments.remuneration_id=remunerations.id 
									INNER JOIN payroll_profiles ON employee_term_payments.payroll_profile_id=payroll_profiles.id
									INNER JOIN employees ON payroll_profiles.emp_id=employees.id
									INNER JOIN companies ON employees.emp_company=companies.id 
									inner join departments on (employees.emp_company=departments.company_id and employees.emp_department=departments.id) 
									inner join job_titles on employees.emp_job_code=job_titles.id 
									LEFT JOIN employee_banks as ebanks ON ebanks.emp_id = employees.id ".
								//"WHERE DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ".
								"WHERE (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ".
								  "and employee_term_payments.remuneration_id = ? 
								  AND ".$emp_location_col."=? 
								  AND ".$emp_department_col."=? 
								  AND employee_term_payments.payment_cancel=0 AND ".$emp_sal_held_col."='0'
						) AS drv_emp 
			ORDER BY drv_emp.emp_epfno";

		$employee = DB::select($sqladvance, [$paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $request->rpt_remuneration_id, $emp_location_val, $emp_department_val]);
		
		$employee_list = array();
		
		foreach($employee as $r){
			$employee_list[]=array('id'=>$r->emp_payslip_id, 'ref_no'=>'-', 'emp_epfno'=>$r->emp_disp_epfno, 'emp_name'=>$r->emp_first_name, 
								   'emp_department'=>$r->dept_name, 'emp_designation'=>$r->emp_designation, 
								   'bank_br_code'=>'', 'account_no'=>'', 'trx_code'=>'', 'term_payment_name'=>$r->remuneration_name, 
								   'amount'=>$r->fig_value, 'date'=>$r->created_date);
		}
		
		$sql_paydata = "SELECT payroll_profiles.id AS emp_payslip_id, employee_term_payment_extras.remuneration_extra_id, drv_p.emp_late_hours as late_mins, ifnull(remuneration_extras.extras_label, 'Late deduction') as extras_label, ifnull(remuneration_extras.value_group, -1) as value_group, employee_term_payment_extras.term_extra_entitle_amount ";
		$sql_paydata .= "FROM `employee_term_payments` ";
		$sql_paydata .= "INNER JOIN payroll_profiles ON employee_term_payments.payroll_profile_id=payroll_profiles.id ";
		$sql_paydata .= "INNER JOIN (select employee_term_payment_id, remuneration_extra_id, employee_work_rate_id, term_extra_entitle_amount from employee_term_payment_extras where employee_term_payment_extra_cancel=0 and payroll_profile_extra_id=0) AS employee_term_payment_extras ";
		$sql_paydata .= "on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ";
		$sql_paydata .= "INNER JOIN (select employee_payslip_id, SUM(emp_late_hours) AS emp_late_hours from employee_paid_rates where (salary_process_year IN (YEAR(?), YEAR(?))) AND (salary_process_month IN (MONTH(?), MONTH(?))) GROUP BY employee_payslip_id) AS drv_p ON employee_term_payment_extras.employee_work_rate_id=drv_p.employee_payslip_id ";
		$sql_paydata .= "LEFT OUTER JOIN remuneration_extras on employee_term_payment_extras.remuneration_extra_id=remuneration_extras.id ";
		//$sql_paydata .= "WHERE DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ";
		$sql_paydata .= "WHERE (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ";
		$sql_paydata .= "and employee_term_payments.remuneration_id = ? ";
		$sql_paydata .= "AND employee_term_payments.payment_cancel=0 AND ".$emp_sal_held_col."='0' ";
		$sql_paydata .= "UNION ALL ";
		$sql_paydata .= "select payroll_profiles.id AS emp_payslip_id, payroll_profile_extras.remuneration_extra_id, 0 AS late_mins, remuneration_extras.extras_label, remuneration_extras.value_group, ifnull(drv_employee_term_payment_extras.term_extra_entitle_amount, payroll_profile_extras.extra_entitle_amount) as term_extra_entitle_amount ";
		$sql_paydata .= "from payroll_profiles ";
		$sql_paydata .= "inner join payroll_profile_extras on payroll_profiles.id=payroll_profile_extras.payroll_profile_id ";
		/*
		$sql_paydata .= "left outer join (select employee_term_payments.payroll_profile_id, employee_term_payment_extras.remuneration_extra_id, employee_term_payment_extras.term_extra_entitle_amount from employee_term_payments ";
		$sql_paydata .= "inner join employee_term_payment_extras ";
		$sql_paydata .= "on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ";
		$sql_paydata .= "where DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') and employee_term_payments.remuneration_id = ? AND employee_term_payments.payment_cancel=0 AND ";
		$sql_paydata .= "employee_term_payment_extras.employee_term_payment_extra_cancel=0 and ";
		$sql_paydata .= "employee_term_payment_extras.payroll_profile_extra_id>0) as drv_employee_term_payment_extras ";
		*/
		$sql_paydata .= "inner join (select id, payroll_profile_id from employee_term_payments ";
		//$sql_paydata .= "where DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ";
		$sql_paydata .= "where (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ";
		$sql_paydata .= "and employee_term_payments.remuneration_id = ? AND employee_term_payments.payment_cancel=0) as drv_employee_term_payments on payroll_profiles.id=drv_employee_term_payments.payroll_profile_id ";
		$sql_paydata .= "left outer join (select employee_term_payment_id, payroll_profile_extra_id, term_extra_entitle_amount from employee_term_payment_extras where employee_term_payment_extra_cancel=0 and payroll_profile_extra_id>0) as drv_employee_term_payment_extras ";
		/*
		$sql_paydata .= "on (payroll_profiles.id=drv_employee_term_payment_extras.payroll_profile_id and ";
		$sql_paydata .= "payroll_profile_extras.remuneration_extra_id=drv_employee_term_payment_extras.remuneration_extra_id)";
		*/
		$sql_paydata .= "on (drv_employee_term_payments.id=drv_employee_term_payment_extras.employee_term_payment_id and ";
		$sql_paydata .= "payroll_profile_extras.id=drv_employee_term_payment_extras.payroll_profile_extra_id) ";
		$sql_paydata .= "LEFT OUTER JOIN remuneration_extras on payroll_profile_extras.remuneration_extra_id=remuneration_extras.id ";
		$sql_paydata .= "ORDER by emp_payslip_id, value_group";//"payroll_profiles.id";
		
		$term_extras = DB::select($sql_paydata, [$paymentPeriod->payment_period_fr, $paymentPeriod->payment_period_to, $paymentPeriod->payment_period_fr, $paymentPeriod->payment_period_to, $paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $request->rpt_remuneration_id, $paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $request->rpt_remuneration_id]);
		
		$emp_pay_add_details = array();
		$emp_pay_ded_details = array();
		
		foreach($term_extras as $p){
			if($p->value_group==1){
				$emp_pay_add_details[$p->emp_payslip_id][] = array('payment_desc'=>$p->extras_label, 'entitle_amount'=>$p->term_extra_entitle_amount);
			}else{
				$extra_pay_desc = $p->extras_label;
				if(($p->remuneration_extra_id=='0')&&($p->late_mins!='0')){
					$late_min_value = number_format(abs((float)$p->term_extra_entitle_amount/$p->late_mins), 2, '.', '');
					$extra_pay_desc .= '('.$p->late_mins.'mins X '.$late_min_value.')';
				}
				$emp_pay_ded_details[$p->emp_payslip_id][] = array('payment_desc'=>$extra_pay_desc, 'entitle_amount'=>$p->term_extra_entitle_amount);
			}
		}
		
		$more_info='';//$payment_period_fr.' / '.$payment_period_to;
		$sect_name = '';//$request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $paymentPeriod->payment_period_to)->format('F Y');//Carbon::now()->format('F Y');//
		
		ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");
		/*
		$salarySheetPdf = ShapeuphrmSetting::where(['res_key'=>'v_term_payment_pdf'])->first();//
		*/
		$pdf = PDF::loadView('Payroll.termPayment.ExtrasPayment_pdf', compact('employee_list', 'emp_pay_add_details', 'emp_pay_ded_details', 'more_info', 'sect_name', 'paymonth_name', 'company_name', 'company_addr'));
        return $pdf->download('advance-list.pdf');
	}
	
	public function checkPayRegister(Request $request){
		if($request->ajax()){
			$rules = array(
				'location_filter_id' => 'required',
				'payroll_process_type_id' => 'required',
				'period_filter_id' => 'required'
			);
			
			$emp_sal_held_col = '0';
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
			
			$sqladvance = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_epfno, drv_emp.emp_disp_epfno, 
				   drv_emp.emp_first_name, drv_emp.emp_national_id, drv_emp.emp_last_name, 
				   drv_emp.emp_designation, drv_emp.dept_name, 
				   drv_emp.e_id, 
				   drv_emp.bank_ac_no,
				   drv_emp.bank_code,
				   drv_emp.branch_code,
				   drv_emp.remuneration_name,
				   drv_emp.payment_amount AS fig_value, drv_emp.created_date 
						FROM (SELECT payroll_profiles.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno AS emp_disp_epfno,
									 employees.emp_name_with_initial AS emp_first_name, 
									 '' AS emp_national_id, '' AS emp_last_name, 
									 '' AS emp_designation, departments.name as dept_name, 
									 employees.id as e_id, remunerations.remuneration_name, 
									 '' AS bank_ac_no,
									 '' AS bank_code,
									 '' AS branch_code, employee_term_payments.payment_amount, 
									 DATE(employee_term_payments.created_at) as created_date 
									FROM `employee_term_payments` 
										INNER JOIN remunerations on employee_term_payments.remuneration_id=remunerations.id 
										INNER JOIN payroll_profiles ON employee_term_payments.payroll_profile_id=payroll_profiles.id
										INNER JOIN employees ON payroll_profiles.emp_id=employees.id
										INNER JOIN companies ON employees.emp_company=companies.id 
										inner join departments on (employees.emp_company=departments.company_id and employees.emp_department=departments.id) ".
									//"WHERE DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ".
									"WHERE (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ".
									  "and employee_term_payments.remuneration_id = 23 
									  AND ".$emp_location_col."=? 
									  AND ".$emp_department_col."=? 
									  AND employee_term_payments.payment_cancel=0 AND ".$emp_sal_held_col."='0'
							) AS drv_emp 
				ORDER BY drv_emp.emp_epfno";
	
			$employee = DB::select($sqladvance, [$paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $emp_location_val, $emp_department_val]);
			
			$extras_col_data = array('1'=>'col_advance',
									 '8'=>'col_dialogbill',
									 '7'=>'col_otherdeduction'
									);
			$employee_list = array();
			$employee_termpay = array();
			$employee_seqno = 0;
			$cnt = 0;
			$act_payslip_id = '';
			
			foreach($employee as $r){
				$employee_seqno++;
				$employee_termpay[$r->emp_payslip_id]=array('id'=>$r->emp_payslip_id, 'reg_indexno'=>$employee_seqno, 'ref_no'=>'-', 
									   'emp_epfno'=>$r->emp_disp_epfno, 'emp_first_name'=>$r->emp_first_name, 
									   'emp_department'=>$r->dept_name, 'emp_designation'=>$r->emp_designation, 
									   'bank_br_code'=>'', 'account_no'=>'', 'trx_code'=>'', 'term_payment_name'=>$r->remuneration_name, 
									   'col_advance' => 0, 'col_dialogbill' => 0, 'col_otherdeduction' => 0,
									   'net_amount'=>$r->fig_value, 'date'=>$r->created_date);
			}
			
			$sql_paydata = "SELECT payroll_profiles.id AS emp_payslip_id, employee_term_payment_extras.remuneration_extra_id, drv_p.emp_late_hours as late_mins, ifnull(remuneration_extras.extras_label, 'Late deduction') as extras_label, ifnull(remuneration_extras.value_group, -1) as value_group, employee_term_payment_extras.term_extra_entitle_amount ";
			$sql_paydata .= "FROM `employee_term_payments` ";
			$sql_paydata .= "INNER JOIN payroll_profiles ON employee_term_payments.payroll_profile_id=payroll_profiles.id ";
			$sql_paydata .= "INNER JOIN (select employee_term_payment_id, remuneration_extra_id, employee_work_rate_id, term_extra_entitle_amount from employee_term_payment_extras where employee_term_payment_extra_cancel=0 and payroll_profile_extra_id=0) AS employee_term_payment_extras ";
			$sql_paydata .= "on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ";
			$sql_paydata .= "INNER JOIN (select employee_payslip_id, SUM(emp_late_hours) AS emp_late_hours from employee_paid_rates where (salary_process_year IN (YEAR(?), YEAR(?))) AND (salary_process_month IN (MONTH(?), MONTH(?))) GROUP BY employee_payslip_id) AS drv_p ON employee_term_payment_extras.employee_work_rate_id=drv_p.employee_payslip_id ";
			$sql_paydata .= "LEFT OUTER JOIN remuneration_extras on employee_term_payment_extras.remuneration_extra_id=remuneration_extras.id ";
			//$sql_paydata .= "WHERE DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ";
			$sql_paydata .= "WHERE (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ";
			$sql_paydata .= "and employee_term_payments.remuneration_id = 23 ";
			$sql_paydata .= "AND employee_term_payments.payment_cancel=0 AND ".$emp_sal_held_col."='0' ";
			$sql_paydata .= "UNION ALL ";
			$sql_paydata .= "select payroll_profiles.id AS emp_payslip_id, payroll_profile_extras.remuneration_extra_id, 0 AS late_mins, remuneration_extras.extras_label, remuneration_extras.value_group, ifnull(drv_employee_term_payment_extras.term_extra_entitle_amount, payroll_profile_extras.extra_entitle_amount) as term_extra_entitle_amount ";
			$sql_paydata .= "from payroll_profiles ";
			$sql_paydata .= "inner join payroll_profile_extras on payroll_profiles.id=payroll_profile_extras.payroll_profile_id ";
			/*
			$sql_paydata .= "left outer join (select employee_term_payments.payroll_profile_id, employee_term_payment_extras.remuneration_extra_id, employee_term_payment_extras.term_extra_entitle_amount from employee_term_payments ";
			$sql_paydata .= "inner join employee_term_payment_extras ";
			$sql_paydata .= "on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ";
			$sql_paydata .= "where DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') and employee_term_payments.remuneration_id = ? AND employee_term_payments.payment_cancel=0 AND ";
			$sql_paydata .= "employee_term_payment_extras.employee_term_payment_extra_cancel=0 and ";
			$sql_paydata .= "employee_term_payment_extras.payroll_profile_extra_id>0) as drv_employee_term_payment_extras ";
			*/
			$sql_paydata .= "inner join (select id, payroll_profile_id from employee_term_payments ";
			//$sql_paydata .= "where DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ";
			$sql_paydata .= "where (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ";
			$sql_paydata .= "and employee_term_payments.remuneration_id = 23 AND employee_term_payments.payment_cancel=0) as drv_employee_term_payments on payroll_profiles.id=drv_employee_term_payments.payroll_profile_id ";
			$sql_paydata .= "left outer join (select employee_term_payment_id, payroll_profile_extra_id, term_extra_entitle_amount from employee_term_payment_extras where employee_term_payment_extra_cancel=0 and payroll_profile_extra_id>0) as drv_employee_term_payment_extras ";
			/*
			$sql_paydata .= "on (payroll_profiles.id=drv_employee_term_payment_extras.payroll_profile_id and ";
			$sql_paydata .= "payroll_profile_extras.remuneration_extra_id=drv_employee_term_payment_extras.remuneration_extra_id)";
			*/
			$sql_paydata .= "on (drv_employee_term_payments.id=drv_employee_term_payment_extras.employee_term_payment_id and ";
			$sql_paydata .= "payroll_profile_extras.id=drv_employee_term_payment_extras.payroll_profile_extra_id) ";
			$sql_paydata .= "LEFT OUTER JOIN remuneration_extras on payroll_profile_extras.remuneration_extra_id=remuneration_extras.id ";
			$sql_paydata .= "ORDER by emp_payslip_id, value_group";//"payroll_profiles.id";
			
			$term_extras = DB::select($sql_paydata, [$paymentPeriod->payment_period_fr, $paymentPeriod->payment_period_to, $paymentPeriod->payment_period_fr, $paymentPeriod->payment_period_to, $paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date]);
			
			foreach($term_extras as $p){
				if($act_payslip_id!=$p->emp_payslip_id){
					$cnt++;
					$act_payslip_id=$p->emp_payslip_id;
					
				}
				
				if(!isset($employee_list[$cnt-1])){
					$employee_list[]=$employee_termpay[$p->emp_payslip_id];
				}
				
				if(isset($extras_col_data[$p->remuneration_extra_id])){
					$employee_list[$cnt-1][$extras_col_data[$p->remuneration_extra_id]]=$p->term_extra_entitle_amount;
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
		
		$emp_sal_held_col = '0';
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
		
		$sqladvance = "SELECT drv_emp.emp_payslip_id, drv_emp.emp_epfno, drv_emp.emp_disp_epfno, 
			   drv_emp.emp_first_name, drv_emp.emp_national_id, drv_emp.emp_last_name, 
			   drv_emp.emp_designation, drv_emp.dept_name, 
			   drv_emp.e_id, 
			   drv_emp.bank_ac_no,
			   drv_emp.bank_code,
			   drv_emp.branch_code,
			   drv_emp.remuneration_name,
			   drv_emp.payment_amount AS fig_value, drv_emp.created_date 
					FROM (SELECT payroll_profiles.id AS emp_payslip_id, employees.emp_id AS emp_epfno, employees.emp_etfno AS emp_disp_epfno,
								 employees.emp_name_with_initial AS emp_first_name, 
								 '' AS emp_national_id, '' AS emp_last_name, 
								 '' AS emp_designation, departments.name as dept_name, 
								 employees.id as e_id, remunerations.remuneration_name, 
								 '' AS bank_ac_no,
								 '' AS bank_code,
								 '' AS branch_code, employee_term_payments.payment_amount, 
								 DATE(employee_term_payments.created_at) as created_date 
								FROM `employee_term_payments` 
									INNER JOIN remunerations on employee_term_payments.remuneration_id=remunerations.id 
									INNER JOIN payroll_profiles ON employee_term_payments.payroll_profile_id=payroll_profiles.id
									INNER JOIN employees ON payroll_profiles.emp_id=employees.id
									INNER JOIN companies ON employees.emp_company=companies.id 
									inner join departments on (employees.emp_company=departments.company_id and employees.emp_department=departments.id) ".
								//"WHERE DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ".
								"WHERE (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ".
								  "and employee_term_payments.remuneration_id = 23 
								  AND ".$emp_location_col."=? 
								  AND ".$emp_department_col."=? 
								  AND employee_term_payments.payment_cancel=0 AND ".$emp_sal_held_col."='0'
						) AS drv_emp 
			ORDER BY drv_emp.emp_epfno";

		$employee = DB::select($sqladvance, [$paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $emp_location_val, $emp_department_val]);
		
		$extras_col_data = array('1'=>'col_advance',
								 '8'=>'col_dialogbill',
								 '7'=>'col_otherdeduction'
								);
		$employee_list = array();
		$employee_termpay = array();
		$employee_seqno = 0;
		$cnt = 0;
		$act_payslip_id = '';
		
		foreach($employee as $r){
			$employee_seqno++;
			$employee_termpay[$r->emp_payslip_id]=array('id'=>$r->emp_payslip_id, 'reg_indexno'=>$employee_seqno, 'ref_no'=>'-', 
								   'emp_epfno'=>$r->emp_disp_epfno, 'emp_first_name'=>$r->emp_first_name, 
								   'emp_department'=>$r->dept_name, 'emp_designation'=>$r->emp_designation, 
								   'bank_br_code'=>'', 'account_no'=>'', 'trx_code'=>'', 'term_payment_name'=>$r->remuneration_name, 
								   'col_advance' => 0, 'col_dialogbill' => 0, 'col_otherdeduction' => 0,
								   'net_amount'=>$r->fig_value, 'date'=>$r->created_date);
		}
		
		$sql_paydata = "SELECT payroll_profiles.id AS emp_payslip_id, employee_term_payment_extras.remuneration_extra_id, drv_p.emp_late_hours as late_mins, ifnull(remuneration_extras.extras_label, 'Late deduction') as extras_label, ifnull(remuneration_extras.value_group, -1) as value_group, employee_term_payment_extras.term_extra_entitle_amount ";
		$sql_paydata .= "FROM `employee_term_payments` ";
		$sql_paydata .= "INNER JOIN payroll_profiles ON employee_term_payments.payroll_profile_id=payroll_profiles.id ";
		$sql_paydata .= "INNER JOIN (select employee_term_payment_id, remuneration_extra_id, employee_work_rate_id, term_extra_entitle_amount from employee_term_payment_extras where employee_term_payment_extra_cancel=0 and payroll_profile_extra_id=0) AS employee_term_payment_extras ";
		$sql_paydata .= "on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ";
		$sql_paydata .= "INNER JOIN (select employee_payslip_id, SUM(emp_late_hours) AS emp_late_hours from employee_paid_rates where (salary_process_year IN (YEAR(?), YEAR(?))) AND (salary_process_month IN (MONTH(?), MONTH(?))) GROUP BY employee_payslip_id) AS drv_p ON employee_term_payment_extras.employee_work_rate_id=drv_p.employee_payslip_id ";
		$sql_paydata .= "LEFT OUTER JOIN remuneration_extras on employee_term_payment_extras.remuneration_extra_id=remuneration_extras.id ";
		//$sql_paydata .= "WHERE DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ";
		$sql_paydata .= "WHERE (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ";
		$sql_paydata .= "and employee_term_payments.remuneration_id = 23 ";
		$sql_paydata .= "AND employee_term_payments.payment_cancel=0 AND ".$emp_sal_held_col."='0' ";
		$sql_paydata .= "UNION ALL ";
		$sql_paydata .= "select payroll_profiles.id AS emp_payslip_id, payroll_profile_extras.remuneration_extra_id, 0 AS late_mins, remuneration_extras.extras_label, remuneration_extras.value_group, ifnull(drv_employee_term_payment_extras.term_extra_entitle_amount, payroll_profile_extras.extra_entitle_amount) as term_extra_entitle_amount ";
		$sql_paydata .= "from payroll_profiles ";
		$sql_paydata .= "inner join payroll_profile_extras on payroll_profiles.id=payroll_profile_extras.payroll_profile_id ";
		/*
		$sql_paydata .= "left outer join (select employee_term_payments.payroll_profile_id, employee_term_payment_extras.remuneration_extra_id, employee_term_payment_extras.term_extra_entitle_amount from employee_term_payments ";
		$sql_paydata .= "inner join employee_term_payment_extras ";
		$sql_paydata .= "on employee_term_payments.id=employee_term_payment_extras.employee_term_payment_id ";
		$sql_paydata .= "where DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') and employee_term_payments.remuneration_id = ? AND employee_term_payments.payment_cancel=0 AND ";
		$sql_paydata .= "employee_term_payment_extras.employee_term_payment_extra_cancel=0 and ";
		$sql_paydata .= "employee_term_payment_extras.payroll_profile_extra_id>0) as drv_employee_term_payment_extras ";
		*/
		$sql_paydata .= "inner join (select id, payroll_profile_id from employee_term_payments ";
		//$sql_paydata .= "where DATE_FORMAT(employee_term_payments.created_at, '%Y%m')=DATE_FORMAT(NOW(), '%Y%m') ";
		$sql_paydata .= "where (DATE(employee_term_payments.created_at) BETWEEN ? AND ?) ";
		$sql_paydata .= "and employee_term_payments.remuneration_id = 23 AND employee_term_payments.payment_cancel=0) as drv_employee_term_payments on payroll_profiles.id=drv_employee_term_payments.payroll_profile_id ";
		$sql_paydata .= "left outer join (select employee_term_payment_id, payroll_profile_extra_id, term_extra_entitle_amount from employee_term_payment_extras where employee_term_payment_extra_cancel=0 and payroll_profile_extra_id>0) as drv_employee_term_payment_extras ";
		/*
		$sql_paydata .= "on (payroll_profiles.id=drv_employee_term_payment_extras.payroll_profile_id and ";
		$sql_paydata .= "payroll_profile_extras.remuneration_extra_id=drv_employee_term_payment_extras.remuneration_extra_id)";
		*/
		$sql_paydata .= "on (drv_employee_term_payments.id=drv_employee_term_payment_extras.employee_term_payment_id and ";
		$sql_paydata .= "payroll_profile_extras.id=drv_employee_term_payment_extras.payroll_profile_extra_id) ";
		$sql_paydata .= "LEFT OUTER JOIN remuneration_extras on payroll_profile_extras.remuneration_extra_id=remuneration_extras.id ";
		$sql_paydata .= "ORDER by emp_payslip_id, value_group";//"payroll_profiles.id";
		
		$term_extras = DB::select($sql_paydata, [$paymentPeriod->payment_period_fr, $paymentPeriod->payment_period_to, $paymentPeriod->payment_period_fr, $paymentPeriod->payment_period_to, $paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date, $paymentPeriod->created_at->format('Y-m-d'), $paymentPeriod->advance_payment_date]);
		
		foreach($term_extras as $p){
			if($act_payslip_id!=$p->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$p->emp_payslip_id;
				
			}
			
			if(!isset($employee_list[$cnt-1])){
				$employee_list[]=$employee_termpay[$p->emp_payslip_id];
			}
			
			if(isset($extras_col_data[$p->remuneration_extra_id])){
				$employee_list[$cnt-1][$extras_col_data[$p->remuneration_extra_id]]=$p->term_extra_entitle_amount;
			}
		}
		
		if($request->print_record=='2'){
			//$emp_array[] = $sum_array;
			$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
			$sect_name = $request->rpt_dept_name;
			$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');
			$customPaper = array(0,0,567.00,1283.80);
			
			ini_set("memory_limit", "999M");
			ini_set("max_execution_time", "999");
			
			$pdf = PDF::loadView('Payroll.termPayment.ExtrasPayRegister_pdf', compact('employee_list', 'more_info', 'sect_name', 'paymonth_name', 'company_name', 'company_addr', 'land_tp'))
				->setPaper('legal', 'landscape');//->setPaper($customPaper, 'landscape');
			
			return $pdf->download('advance-pay-register.pdf');
		}
	}
}
