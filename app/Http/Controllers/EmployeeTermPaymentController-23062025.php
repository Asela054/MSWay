<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\Employee;

use App\EmployeePayslip;
use App\EmployeeTermPayment;

use App\PaymentPeriod;
use App\PayrollProcessType;

use App\PayrollProfile;

use App\Remuneration;

use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use PDF;
use Validator;

class EmployeeTermPaymentController extends Controller
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
		$remuneration=Remuneration::where(['remuneration_cancel'=>0, 'allocation_method'=>'TERMS'])->orderBy('id', 'asc')->get();
		
		$payroll_process_type=PayrollProcessType::orderBy('id', 'asc')->get();
		$payment_period=PaymentPeriod::orderBy('id', 'desc')->get();
		
        return view('Payroll.termPayment.termPayment_list',compact('branch', 'remuneration', 'payroll_process_type', 'payment_period'));
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
        $rules = array(
            'remuneration_id' => 'required', 
			'eligible_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
		
		$employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id])
								->latest()
								->first();
		$emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);//pay-slip-no++ adding payment to next emp-salary
		
		
		/*
        $form_data = array(
            'remuneration_name'        =>  $request->remuneration_name
            
        );
		*/
		
		$termPayment=NULL;
		
		try{
			$termPayment=EmployeeTermPayment::where(['remuneration_id'=>$request->input('remuneration_id'), 
													'payroll_profile_id'=>$request->input('payroll_profile_id'),
													'emp_payslip_no'=>$emp_payslip_no])
											->firstOrFail();
			$termPayment->updated_by=$request->user()->id;
			
		}catch (ModelNotFoundException $e) {
			// Data not found. Here, you should make sure that the absence of $data won't break anything
			$termPayment=new EmployeeTermPayment;
			$termPayment->created_by=$request->user()->id;
		}
		
        //
        $termPayment->remuneration_id=$request->input('remuneration_id'); 
		$termPayment->payroll_profile_id=$request->input('payroll_profile_id'); 
		$termPayment->payment_amount=$request->input('eligible_amount'); 
		$termPayment->emp_payslip_no=$emp_payslip_no; 
		$termPayment->payment_cancel=0;
		//
        $termPayment->save();

       

        return response()->json(['success' => 'Payment Added Successfully.', 'new_obj'=>$termPayment]);
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
			
			/**/
			/**/
			
			$payment_rows = DB::table('employee_term_payments')
							->join('remunerations', 'employee_term_payments.remuneration_id', '=', 'remunerations.id')
							->select('employee_term_payments.id', 'remunerations.remuneration_name', 'employee_term_payments.payment_amount', 'employee_term_payments.created_at', 'employee_term_payments.updated_at')
							->where(['employee_term_payments.payroll_profile_id'=>$id, 
									 'employee_term_payments.emp_payslip_no'=>$emp_payslip_no, 
									 'employee_term_payments.payment_cancel'=>0])
							->get();
							
			$payment_list=array();
			foreach($payment_rows as $r){
				$payment_date=($r->updated_at=='')?$r->created_at:$r->updated_at;
				$payment_list[]=array('id'=>$r->id, 'remuneration_name'=>$r->remuneration_name, 'payment_date'=>date('Y-m-d', strtotime($payment_date)), 'payment_amount'=>$r->payment_amount, 'payment_cancel'=>0);
			}
			
			/**/
			/**/
			
            return response()->json(['package'=>$payment_list]);
        }
    }
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeTermPayment  $loan
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		if(request()->ajax()){
			$data=DB::table('employee_term_payments')
				->select('id', 'payment_amount')
				->where(['id'=>$id])
				->get();
			
			return response()->json(['term_obj'=>$data[0]]);
		}
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
        
		$rules = array(
            'new_allocated_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
		
		$payment_info=EmployeeTermPayment::whereId($request->hidden_term_id)->get();
		/*
		check-whether-payslip-no-is-processed-or-not-before-changing-payment-info
		*/
		$payslip_info=DB::select("SELECT COUNT(*) AS payment_processed FROM employee_payslips WHERE payroll_profile_id=? AND emp_payslip_no=?", [$payment_info[0]->payroll_profile_id, $payment_info[0]->emp_payslip_no]);
		
		if($payslip_info[0]->payment_processed==1){
			return response()->json(['errors' => array('Payslip details already processed')]);
		}
		
		$form_data = array(
            'payment_amount' =>  $request->new_allocated_amount,
			'updated_by' => $request->user()->id
            
        );

        EmployeeTermPayment::whereId($request->hidden_term_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'new_id'=>$request->hidden_term_id]);
		/**/
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
		if($request->ajax()){
			$employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id])
								->latest()
								->first();
			$emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);
			
			$form_data = array(
				'remuneration_id' => $request->remuneration_id,
				'payroll_profile_id' => $request->payroll_profile_id,
				'emp_payslip_no' => $emp_payslip_no,
				'payment_amount' => $request->payment_amount,
				'payment_cancel' =>  $request->payment_cancel
			);
			
			$affectedRows=0;
			$affectedMode=1;//accept-changes
			
			$payment_id='';//$request->id;
			
			
			try{
				$termPayment=EmployeeTermPayment::where(['remuneration_id'=>$request->input('remuneration_id'), 
														'payroll_profile_id'=>$request->input('payroll_profile_id'),
														'emp_payslip_no'=>$emp_payslip_no])
												->firstOrFail();
				$payment_id=$termPayment->id;
				
			}catch (ModelNotFoundException $e) {
				$payment_id='';//$request->id;
				/*
				if(ModelNotFound thrown for payment-cancel==0)
					there-is-no-payment-record. so, add-new-term-payment
				if(ModelNotFound thrown for payment-cancel==1)
					there-is-an-payment-record-exist-for-different-emp-payslip-no-previously-created. so, abort-changes
				*/
				$affectedMode=($request->payment_cancel==0)?1:0;
			}
			
			if($affectedMode==1){
				if($payment_id==''){
					$form_data['created_by']=$request->user()->id;
					$form_data['created_at']=date('Y-m-d H:i:s');
					
					$affectedRows=DB::table('employee_term_payments')
						->insert($form_data);
					$payment_id=DB::getPdo()->lastInsertId();
				}else{
					$form_data['updated_by']=$request->user()->id;
					$form_data['updated_at']=date('Y-m-d H:i:s');
					
					$affectedRows=DB::table('employee_term_payments')
						->where(['id'=>$request->id, 'payment_cancel'=>(1-$request->payment_cancel)])
						->update($form_data);
				}
			}
			
			$result = array('result'=>(($affectedRows==1)?'success':'error'), 'payment_id'=>$payment_id);
	
			return response()->json($result);
		}
	}
	
	/*
	method checkPayment
	if(filter_by){
		Salary Preperation
		------------------
		list of term payments selected for a particular employee-payslip-no
	}else{
		Salary Addition
		------------------
		prepare a list of employees eligible for a particular term payment
	}
	*/
	public function checkPayment(Request $request){
		if($request->ajax()){
			$employee_list = array();
			
			if($request->has('filter_by')){
				$installment = DB::table("employee_term_payments")
							->join("remunerations", "employee_term_payments.remuneration_id", "=", "remunerations.id")
							->select("employee_term_payments.id", "remunerations.remuneration_name", "employee_term_payments.payment_amount", "remunerations.value_group")
							->where(["employee_term_payments.payroll_profile_id"=>$request->payroll_profile_id, 
									 "employee_term_payments.emp_payslip_no"=>$request->emp_payslip_no, 
									 "employee_term_payments.payment_cancel"=>0])
							->get();
				
				
				foreach($installment as $r){
					$employee_list[]=array('id'=>$r->id, 'term_name'=>$r->remuneration_name, 'payment_value'=>$r->payment_amount, 'value_group'=>$r->value_group);
				}
			}else{
				/*
				addition-status-of-employee-term-payment-within-single-employee-payslip-no
				SELECT employees.emp_first_name, payroll_profiles.id as payroll_profile_id, payroll_process_types.process_name, payroll_profiles.basic_salary, branches.branch as location, IFNULL(drv_term.id, '') as payment_id, IFNULL(drv_term.payment_cancel, 1) AS payment_cancel FROM employees INNER JOIN branches ON employees.emp_location=branches.id INNER JOIN payroll_profiles ON employees.id=payroll_profiles.emp_id INNER JOIN payroll_process_types ON payroll_profiles.payroll_process_type_id=payroll_process_types.id LEFT OUTER JOIN (SELECT id, payroll_profile_id, payment_cancel FROM employee_term_payments WHERE remuneration_id=?) as drv_term ON payroll_profiles.id=drv_term.payroll_profile_id
				*/
				$employee = DB::select("SELECT employees.emp_name_with_initial AS emp_first_name, payroll_profiles.id as payroll_profile_id, payroll_process_types.process_name, payroll_profiles.basic_salary, companies.name as location, IFNULL(drv_term.id, '') as payment_id, IFNULL(drv_term.payment_cancel, 1) AS payment_cancel FROM employees INNER JOIN companies ON employees.emp_company=companies.id INNER JOIN payroll_profiles ON employees.id=payroll_profiles.emp_id INNER JOIN payroll_process_types ON payroll_profiles.payroll_process_type_id=payroll_process_types.id LEFT OUTER JOIN (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips GROUP BY payroll_profile_id) AS employee_payslips ON payroll_profiles.id=employee_payslips.payroll_profile_id LEFT OUTER JOIN (SELECT id, payroll_profile_id, emp_payslip_no, payment_cancel FROM employee_term_payments WHERE remuneration_id=?) as drv_term ON (payroll_profiles.id=drv_term.payroll_profile_id AND IFNULL(employee_payslips.emp_payslip_no+1, 1)=drv_term.emp_payslip_no)", [$request->id]);
				
				foreach($employee as $r){
					$employee_list[]=array('id'=>$r->payment_id, 'payroll_profile_id'=>$r->payroll_profile_id, 'emp_first_name'=>$r->emp_first_name, 'location'=>$r->location, 'basic_salary'=>$r->basic_salary, 'process_name'=>(isset($r->process_name)?$r->process_name:''), 'payment_cancel'=>$r->payment_cancel);
				}
			}
			
			return response()->json(['employee_detail'=>$employee_list]);
		}
	}
	
	public function uploadFromFile(Request $request){
		$actmsg = '';
		
		try{
			$actmsg = DB::transaction(function() use ($request){
				if($request->import_file=='import'){
					$filedesc = '---';
					$flag = true;
					$importmsg = '';
					$msgclass = 'msgerror';
					
					if($_FILES["file"]["size"] > 0){
						$filename=$_FILES["file"]["tmp_name"];
						//$filesize=$_FILES["file"]["size"];
						$filedesc=$_FILES["file"]["name"];
						
						$totjobs = 0; // total-plans-in-csv
						$errjobs = 0; // duplicate-plans-in-csv
						$batchsucc = true; 
						
						$rate_feed = false; // set-player-ranking
						$save_code = 0;
						
						$file = fopen($filename, "r");
						
						while ($batchsucc){
							if(($planData = fgetcsv($file, 10000, ",")) !== FALSE){
								$numcols = count($planData);
								if($numcols==2){
									$emp_payslip_no = 0;
									$payroll_profile_id = 0;
									
									
									$termPayment=NULL;
									
									
									try{
										$employeeInfo = Employee::where(['emp_etfno'=>$planData[0]])
																->firstOrFail();
																
										$payrollInfo = PayrollProfile::where(['emp_id'=>$employeeInfo->id])
																	->firstOrFail();
										
										$payroll_profile_id = $payrollInfo->id;
										
										
										$employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$payroll_profile_id])
															->latest()
															->first();
										$emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);//pay-slip-no++
										
										
										$termPayment=EmployeeTermPayment::where(['remuneration_id'=>$request->input('remuneration_file'), 
																				'payroll_profile_id'=>$payroll_profile_id,
																				'emp_payslip_no'=>$emp_payslip_no])
																		->firstOrFail();
										$termPayment->updated_by=$request->user()->id;
										
									}catch (ModelNotFoundException $e) {
										if($emp_payslip_no==0){
											// Data not found. Here, you should make sure that the absence of $data won't break anything
											//$importmsg = 'Something wrong'; // throw new \Exception('----');
										}else{
											$termPayment=new EmployeeTermPayment;
											$termPayment->created_by=$request->user()->id;
										}
									}
									
									if(!empty($termPayment)){
										//
										$termPayment->remuneration_id=$request->input('remuneration_file'); 
										$termPayment->payroll_profile_id=$payroll_profile_id; 
										$termPayment->payment_amount=$planData[1]; 
										$termPayment->emp_payslip_no=$emp_payslip_no; 
										$termPayment->payment_cancel=0;
										//
										
										
										$affectedRowCnt = $termPayment->save(); // true-or-false;
										
										if($affectedRowCnt){
											$totjobs++; // increase-job-count
											$rate_feed = true; // declare-initial-rating-detail
											
										}else{
											$importmsg = 'Something wrong';
											
										}
									}else{
										$errjobs = 1; // increase-err-plan-count
									}
									
									
								}
							}else{
								$save_code = 1;
							}
							
							if(!$rate_feed){
								if( ($totjobs*$save_code)==0 ){
									$flag = false; // clear-file-import-batch-info-written-to-csvlog
								}
								
								$batchsucc = false; // assuming-end-of-data
								
							}else{
								$rate_feed = false; // reset-value-to-capture-upcoming-errors
								
							}
						}
						
						fclose($file);
						
					}else{
						$flag = false;
						$importmsg = "File is empty";
						$msgclass = "msginvalid";
					}
					
					if($flag){
						$importmsg = $totjobs." record(s) imported from ".$filedesc;
						$msgclass = "msgsuccess";
						
						return $importmsg;
					}else{
						if($importmsg==''){
							if($errjobs==1){
								$importmsg = "Data cannot be imported. Unable to insert line " . ($totjobs+1) . " details.";
								$msgclass = "msginvalid";
							}else{
								$importmsg = "Unable to import the CSV file. Please check file content";
								$msgclass = "msgerror";
							}
						}
						
						throw new \Exception($importmsg.' - source: '.$filedesc);
					}
				}
			});
		}catch(\Exception $e){
			$actmsg = $e->getMessage(); //return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}
		
		/*
		return back()->with('success', 'File details has been added'.$request->remuneration_file);
		return redirect('EmployeeTermPaymentList')->with('success','File details has been added'.$request->remuneration_file);
		*/
		
		return back()->with('success', $actmsg);
	}
	
	public function downloadTermPayment(Request $request){
		$employee_list = array();
		
		$employee = DB::select("SELECT employees.emp_name_with_initial AS emp_first_name, payroll_profiles.id as payroll_profile_id, payroll_profiles.emp_etfno, payroll_process_types.process_name, DATE_FORMAT(drv_dura.dura_fr, '%m-%d') as dura_fr, DATE_FORMAT(drv_dura.dura_to, '%m-%d') as dura_to, payroll_profiles.basic_salary, companies.name as location, IFNULL(drv_term.id, '') as payment_id, IFNULL(drv_term.payment_amount, 0) AS payment_amount, IFNULL(drv_term.payment_cancel, 1) AS payment_cancel FROM employees INNER JOIN companies ON employees.emp_company=companies.id INNER JOIN payroll_profiles ON employees.id=payroll_profiles.emp_id INNER JOIN payroll_process_types ON payroll_profiles.payroll_process_type_id=payroll_process_types.id INNER JOIN (select max(id) as dura_id, payroll_process_type_id, max(payment_period_fr) as dura_fr, max(payment_period_to) as dura_to from payment_periods GROUP BY payroll_process_type_id) as drv_dura ON payroll_profiles.payroll_process_type_id=drv_dura.payroll_process_type_id LEFT OUTER JOIN (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips GROUP BY payroll_profile_id) AS employee_payslips ON payroll_profiles.id=employee_payslips.payroll_profile_id LEFT OUTER JOIN (SELECT id, payroll_profile_id, emp_payslip_no, payment_amount, payment_cancel FROM employee_term_payments WHERE remuneration_id=?) as drv_term ON (payroll_profiles.id=drv_term.payroll_profile_id AND IFNULL(employee_payslips.emp_payslip_no+1, 1)=drv_term.emp_payslip_no)", [$request->term_regnum]);
		
		foreach($employee as $r){
			$employee_list[]=array('id'=>$r->payment_id, 'payroll_profile_id'=>$r->payroll_profile_id, 'emp_epfno'=>$r->emp_etfno, 'emp_first_name'=>$r->emp_first_name, 'location'=>$r->location, 'basic_salary'=>$r->basic_salary, 'process_name'=>(isset($r->process_name)?$r->process_name:''), 'payment_cancel'=>$r->payment_cancel, 'pay_dura_fr'=>$r->dura_fr, 'pay_dura_to'=>$r->dura_to, 'pay_amt'=>$r->payment_amount);
		}
		
		if($request->print_record=='2'){
			//$emp_array[] = $sum_array;
			$more_info=$request->rpt_info;//$payment_period_fr.' / '.$payment_period_to;
			$customPaper = array(0,0,567.00,1283.80);
			
			ini_set("memory_limit", "999M");
			ini_set("max_execution_time", "999");
			
			$pdf = PDF::loadView('Payroll.termPayment.TermPayment_pdf', compact('employee_list', 'more_info'))
				->setPaper('legal', 'portrait');//->setPaper($customPaper, 'landscape');
			
			return $pdf->download('term-payment.pdf');
			/*
			var_dump($emp_array);
			*/
		}
	}
	
}
