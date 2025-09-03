<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;

use App\EmployeeLoanInstallment;
use App\EmployeePayslip;
/*
use App\PayrollProcessType;
use App\PayrollProfile;
use App\Remuneration;
*/
use DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use Validator;

class EmployeeLoanInstallmentController extends Controller
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
		/*
		$employee_loans = DB::table('employee_loans')
					->join('payroll_profiles', 'employee_loans.payroll_profile_id', '=', 'payroll_profiles.id')
					->join('employees', 'payroll_profiles.emp_id', '=', 'employees.id')
					->join('branches', 'employees.emp_location', '=', 'branches.id')
					->leftjoin('employee_loan_installments', 'employee_loans.id', '=', 'employee_loan_installments.employee_loan_id')
					->select('employee_loans.id as employee_loan_id', 'employee_loan_installments.id as installment_id', 'payroll_profiles.id as payroll_profile_id', 'employees.emp_first_name', 'branches.branch as location', 'employee_loans.loan_name', 'employee_loans.loan_amount', 'employee_loan_installments.installment_cancel')
					->get();
		*/
		/*
		$employee_loans = DB::select("SELECT employee_loans.id as employee_loan_id, employee_loan_installments.id as installment_id, payroll_profiles.id as payroll_profile_id, employees.emp_first_name, branches.branch as location, employee_loans.loan_name, employee_loans.loan_amount, 0 AS loan_paid, employee_loan_installments.installment_cancel FROM employee_loans INNER JOIN payroll_profiles ON employee_loans.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN branches ON employees.emp_location=branches.id LEFT OUTER JOIN (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips GROUP BY payroll_profile_id) AS employee_payslips ON payroll_profiles.id=employee_payslips.payroll_profile_id LEFT OUTER JOIN (SELECT id, employee_loan_id, emp_payslip_no, installment_cancel FROM employee_loan_installments) AS employee_loan_installments ON (employee_loans.id=employee_loan_installments.employee_loan_id AND IFNULL(employee_payslips.emp_payslip_no+1, 1)=employee_loan_installments.emp_payslip_no)");
		*/
		
		$employee_loans = DB::select("SELECT employee_loans.id as employee_loan_id, employee_loan_installments.id as installment_id, payroll_profiles.id as payroll_profile_id, employees.emp_name_with_initial AS emp_first_name, companies.name as location, employee_loans.loan_name, employee_loans.loan_amount, employee_loans.loan_freeze, IFNULL(drv_prog.loan_paid, 0) AS loan_paid, IFNULL(employee_loan_installments.installment_cancel, 1) AS installment_cancel FROM employee_loans INNER JOIN payroll_profiles ON employee_loans.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies ON employees.emp_company=companies.id LEFT OUTER JOIN (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips GROUP BY payroll_profile_id) AS employee_payslips ON payroll_profiles.id=employee_payslips.payroll_profile_id LEFT OUTER JOIN (SELECT employee_loan_id, MAX(emp_payslip_no) AS loan_payslip_no, SUM(installment_value) AS loan_paid FROM employee_loan_installments WHERE installment_cancel=0 GROUP BY employee_loan_id) AS drv_prog ON employee_loans.id=drv_prog.employee_loan_id LEFT OUTER JOIN (SELECT id, employee_loan_id, emp_payslip_no, installment_cancel FROM employee_loan_installments) AS employee_loan_installments ON (employee_loans.id=employee_loan_installments.employee_loan_id AND IFNULL(employee_payslips.emp_payslip_no+1, 1)=employee_loan_installments.emp_payslip_no) WHERE (employee_loans.loan_cancel=0 AND employee_loans.loan_approved=1) AND (employee_loans.loan_amount>IFNULL(drv_prog.loan_paid, 0) OR IFNULL(drv_prog.loan_payslip_no, 1)>=(IFNULL(employee_payslips.emp_payslip_no, 0)+(employee_loans.loan_complete)))");
		/*
		WHERE (employee_loans.loan_cancel=0 AND employee_loans.loan_complete=0 AND employee_loans.loan_approved=1) AND (employee_loans.loan_amount>IFNULL(drv_prog.loan_paid, 0) OR IFNULL(drv_prog.loan_payslip_no, 1)>IFNULL(employee_payslips.emp_payslip_no, 0))
		WHERE
		1. employee_loans.loan_amount>IFNULL(drv_prog.loan_paid, 0) >> value-difference
		2. IFNULL(drv_prog.loan_payslip_no, 1)>IFNULL(employee_payslips.emp_payslip_no, 0) >> 
			minimum loan-payslip-no must be at least 1 where loan-amount is completed in 1 installment
		*/
		/*
		WHERE (employee_loans.loan_cancel=0 AND employee_loans.loan_approved=1) AND (employee_loans.loan_amount>IFNULL(drv_prog.loan_paid, 0) OR IFNULL(drv_prog.loan_payslip_no, 1)>=(IFNULL(employee_payslips.emp_payslip_no, 0)+(employee_loans.loan_complete)))
		2. get all loans (including loan_freeze=1) by removing loan_complete=0 check, -
		   show loan installment when loan_amount>paid_amount or -
		   show last installment of a completed loan for listing after cancelling loan_payslip_no/emp_payslip_no in employee_payslips
		*/
		
        return view('Payroll.loanInstallment.loanInstallment_list',compact('branch', 'employee_loans'));
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
     * @param  \App\EmployeeLoanInstallment  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeLoanInstallment $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeLoanInstallment  $loan
     * @return \Illuminate\Http\Response
     */
    public function reviewPaymentList($id)
    {
        if(request()->ajax())
        {
            /*
			$welfare_pack = DB::table('remunerations')
							->leftjoin('remuneration_profiles', 'remunerations.id', '=', 'remuneration_profiles.remuneration_id')
							->select('remunerations.id', 'remunerations.remuneration_name', 'remuneration_profiles.new_eligible_amount', 'remuneration_profiles.remuneration_signout')
							->where(['remuneration_profiles.payroll_profile_id'=>$data[0]->id, 'remunerations.remuneration_cancel'=>0])
							->get();
			*/
			/*
			$payment_rows = DB::select("select id, loan_type, loan_date, loan_amount, loan_duration, loan_freeze from employee_loans where payroll_profile_id=? AND loan_cancel=?", [$id, 0]);
			*/
			
			$payment_rows = DB::table('employee_loans')
							->join('employee_loan_installments', 'employee_loans.id', '=', 'employee_loan_installments.employee_loan_id')
							->select('employee_loan_installments.id', 'employee_loan_installments.installment_value', 'employee_loan_installments.created_at', 'employee_loan_installments.updated_at')
							->where(['employee_loans.id'=>$id, 'employee_loan_installments.installment_cancel'=>0])
							->get();
							
			$payment_list=array();
			foreach($payment_rows as $r){
				$payment_date=($r->updated_at=='')?$r->created_at:$r->updated_at;
				$payment_list[]=array('id'=>$r->id, 'payment_date'=>date('Y-m-d', strtotime($payment_date)), 'installment_value'=>$r->installment_value);
			}
			
			/*
			$welfare_cnt = DB::select("select count(*) as cnt from remunerations left outer join (select id as profile_id, remuneration_id, new_eligible_amount, remuneration_signout from remuneration_profiles where payroll_profile_id=?) as drv_profile on remunerations.id=drv_profile.remuneration_id where remunerations.allocation_method='FIXED' AND remunerations.remuneration_cancel=?", [$data[0]->id, 0]);
			'tot_rows'=>$welfare_cnt[0]->cnt
			*/
			/*
			$data = DB::table('employees')
                    ->join('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
                    ->select('employees.emp_first_name', 'payroll_profiles.*')
					->where(['payroll_profiles.id' => $id])
                    ->get();
			
			*/
            return response()->json(['package'=>$payment_list]);
        }
    }
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeLoanInstallment  $loan
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		if(request()->ajax()){
			$data=DB::table('employee_loan_installments')
				->select('id', 'installment_value')
				->where(['id'=>$id])
				->get();
			
			return response()->json(['loan_obj'=>$data[0]]);
		}
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeLoanInstallment  $loan
     * @return \Illuminate\Http\Response
     */
    public function update_without_loan_complete(Request $request, EmployeeLoanInstallment $loan)
    {
        /**/
		$rules = array(
            'new_installment_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
		
		$installment_info=EmployeeLoanInstallment::whereId($request->hidden_loan_id)->get();
		/*
		check-whether-payslip-no-is-processed-or-not-before-changing-payment-info
		*/
		$payslip_info=DB::select("SELECT COUNT(*) AS payment_processed FROM employee_payslips WHERE payroll_profile_id=? AND emp_payslip_no=?", [$installment_info[0]->payroll_profile_id, $installment_info[0]->emp_payslip_no]);
		
		if($payslip_info[0]->payment_processed==1){
			return response()->json(['errors' => array('Payslip details already processed')]);
		}

		$form_data = array(
            'installment_value' =>  $request->new_installment_amount,
			'updated_by' => $request->user()->id
            
        );

        EmployeeLoanInstallment::whereId($request->hidden_loan_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'new_id'=>$request->hidden_loan_id]);
		
    }
	
	public function update(Request $request, EmployeeLoanInstallment $loan)
    {
        try{
			return DB::transaction(function() use ($request){
				/**/
				$rules = array(
					'new_installment_amount' => 'required'
				);
		
				$error = Validator::make($request->all(), $rules);
		
				if($error->fails())
				{
					return response()->json(['errors' => $error->errors()->all()]);
				}
				
				$installment_info=EmployeeLoanInstallment::whereId($request->hidden_loan_id)->get();
				$pre_installment_amount=$installment_info[0]->installment_value;
				/*
				check-whether-payslip-no-is-processed-or-not-before-changing-payment-info
				*/
				$payslip_info=DB::select("SELECT COUNT(*) AS payment_processed FROM employee_payslips WHERE payroll_profile_id=? AND emp_payslip_no=?", [$installment_info[0]->payroll_profile_id, $installment_info[0]->emp_payslip_no]);
				
				if($payslip_info[0]->payment_processed==1){
					//return response()->json(['errors' => array('Payslip details already processed')]);
					throw new \Exception('Payslip details already processed');
				}
				
				//get-loan-balance-exclude-hidden-loan-id
				$loan_getprog=DB::select("SELECT employee_loans.loan_amount, IFNULL(SUM(employee_loan_installments.installment_value), 0) AS loan_paid_amt, employee_loans.loan_complete FROM employee_loans LEFT OUTER JOIN employee_loan_installments ON employee_loans.id=employee_loan_installments.employee_loan_id WHERE employee_loans.id=? AND employee_loan_installments.installment_cancel=0 AND employee_loan_installments.id<>?", [$installment_info[0]->employee_loan_id, $request->hidden_loan_id]);
				//throw new \Exception('x='.$loan_getprog[0]->loan_paid_amt);//test-values
				$emploan_balance = $loan_getprog[0]->loan_amount-$loan_getprog[0]->loan_paid_amt;
				$revloan_balance = round($emploan_balance, 3)-round($request->new_installment_amount, 3);
				$actloan_complete = $loan_getprog[0]->loan_complete;
				
				if($revloan_balance<0){
					throw new \Exception("Total loan value exceed. Loan remaining is ".number_format((float)$emploan_balance, 3, '.', ''));
				}
				
				$form_data = array(
					'installment_value' =>  $request->new_installment_amount,
					'updated_by' => $request->user()->id
					
				);
				
				$affectedRows = 0;//EmployeeLoanInstallment::whereId($request->hidden_loan_id.'')->update($form_data);
				
				$affectedRows=DB::table('employee_loan_installments')
							->where(['id'=>$request->hidden_loan_id, 'installment_cancel'=>0])
							->update($form_data);
				
				
				$loan_complete = ($revloan_balance==0)?1:0;
				
				if($loan_complete!=$actloan_complete){
					if(($affectedRows==1)){
						$affectedRows = 0;
						
						$affectedRows=DB::table('employee_loans')
								->where(['id'=>$installment_info[0]->employee_loan_id])
								->update(['loan_complete'=>$loan_complete]);
					}
				}
				
				if($affectedRows!=1){
					throw new \Exception('Loan installment update error');
				}
				
				return response()->json(['success' => 'Data is successfully updated', 
									 'new_id'=>$request->hidden_loan_id, 'pre_installment_value'=>$pre_installment_amount]);
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'errors'=>array($e->getMessage())));
		}
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeLoanInstallment  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
		
	}
	
	
	/*
	
	*/
	
	public function freeze(Request $request){
		try{
			return DB::transaction(function() use ($request){
				if($request->ajax()){
					$employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$request->payroll_profile_id])
										->latest()
										->first();
					$emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);
					
					$form_data = array(
						'employee_loan_id' => $request->employee_loan_id,
						'payroll_profile_id' => $request->payroll_profile_id,
						'emp_payslip_no' => $emp_payslip_no,
						'installment_value' => 0,
						'installment_cancel' =>  $request->installment_cancel
					);
					
					$loan_id=$request->input('employee_loan_id');
					
					$affectedRows=0;
					$affectedMode=1;//accept-changes
					
					$installment_id='';//$request->id;
					$installment_value=0;
					$act_installment_value=0;
					
					$feedback_msg='';
					
					try{
						$loanInstallment=EmployeeLoanInstallment::where(['employee_loan_id'=>$request->input('employee_loan_id'), 
																'payroll_profile_id'=>$request->input('payroll_profile_id'),
																'emp_payslip_no'=>$emp_payslip_no])
														->firstOrFail();
						
						$installment_id=$loanInstallment->id;
						$act_installment_value=$loanInstallment->installment_value;
						
					}catch (ModelNotFoundException $e) {
						// Data not found. Here, you should make sure that the absence of $data won't break anything
						$installment_id='';//$request->id;
						/*
						if(ModelNotFound thrown for installment-cancel==0)
							there-is-no-installment-record. so, add-new-installment
						if(ModelNotFound thrown for installment-cancel==1)
							there-is-an-installment-record-exist-for-different-emp-payslip-no-previously-created. so, abort-changes
						*/
						$affectedMode=($request->installment_cancel==0)?1:0;
					}
					
					/*
					test="SELECT employee_loans.loan_amount, employee_loans.loan_duration, employee_loans.installment_value, IFNULL(drv_prog.loan_paid, 0) AS loan_paid, IFNULL(drv_prog.tot_installments,0) AS tot_installments, coalesce((employee_loans.loan_amount-IFNULL(drv_prog.loan_paid, 0))*NULLIF((employee_loans.loan_duration-IFNULL(drv_prog.tot_installments, 0))=1, 0), employee_loans.installment_value) AS new_installment_value FROM `employee_loans` LEFT OUTER JOIN (SELECT COUNT(*) AS tot_installments, employee_loan_id, SUM(installment_value) AS loan_paid FROM employee_loan_installments WHERE employee_loan_id=32 AND installment_cancel=0 GROUP BY employee_loan_id) AS drv_prog ON employee_loans.id=drv_prog.employee_loan_id WHERE employee_loans.id=32"
					*/
					$newInstallment=DB::select("SELECT employee_loans.loan_amount, IFNULL(drv_prog.loan_paid, 0) AS settle_amount, coalesce((employee_loans.loan_amount-IFNULL(drv_prog.loan_paid, 0))*NULLIF((employee_loans.loan_duration-IFNULL(drv_prog.tot_installments, 0))=1, 0), employee_loans.installment_value) AS installment_value, (employee_loans.loan_duration-IFNULL(drv_prog.tot_installments, 0)) AS installment_balance, employee_loans.loan_complete FROM `employee_loans` LEFT OUTER JOIN (SELECT COUNT(*) AS tot_installments, employee_loan_id, SUM(installment_value) AS loan_paid FROM employee_loan_installments WHERE employee_loan_id=? AND id<>? AND installment_cancel=0 GROUP BY employee_loan_id) AS drv_prog ON employee_loans.id=drv_prog.employee_loan_id WHERE employee_loans.id=?", [$loan_id, $installment_id, $loan_id]);
					
					$installment_value=($act_installment_value==0)?$newInstallment[0]->installment_value:$act_installment_value;
					$form_data['installment_value']=$newInstallment[0]->installment_value;
					
					$settled_amount=$newInstallment[0]->settle_amount; //$actloan_complete = $newInstallment[0]->loan_complete;
					
					if($installment_id==''){
						$form_data['created_by']=$request->user()->id;
						$form_data['created_at']=date('Y-m-d H:i:s');
						
						$affectedRows=DB::table('employee_loan_installments')
							->insert($form_data);
						$installment_id=DB::getPdo()->lastInsertId();
					}else{
						$form_data['updated_by']=$request->user()->id;
						$form_data['updated_at']=date('Y-m-d H:i:s');
						
						$affectedRows=DB::table('employee_loan_installments')
							->where(['id'=>$installment_id, 'installment_cancel'=>(1-$request->installment_cancel)])
							->update($form_data);
					}
					
					if($affectedRows*$affectedMode==1){
						$feedback_msg='success';
						$installment_value*=($request->installment_cancel==0?1:-1);
						
						$settled_amount+=($installment_value>0?$installment_value:0);
						
					}else{
						throw new \Exception('Loan installment update error');
					}
					
					$installment_balance=1-$newInstallment[0]->installment_balance;
					
					$loan_complete=-1;
					
					if($installment_balance==0){
						$loan_complete=($request->installment_cancel==0)?1:0;
					}
					
					if($loan_complete!=-1){
						//if($loan_complete!=$actloan_complete){
							$affectedRows=DB::table('employee_loans')
								->where(['id'=>$loan_id])
								->update(['loan_complete'=>$loan_complete]);
						//}
					}
					
					if($affectedRows!=1){
						throw new \Exception('Loan status update error');
					}
					
					$result = array('result'=>$feedback_msg, 'payment_id'=>$installment_id, 'payment_value'=>0);
					$result['payment_value'] = $settled_amount;//$installment_value
					
					return response()->json($result);
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}
	}
	
	/*
	method checkInstallment
	Salary Preperation
	------------------
	list of loan installments selected to be settled by a particular employee-payslip-no
	*/
	public function checkInstallment(Request $request){
		if($request->ajax()){
			$installment = DB::table("employee_loan_installments")
							->join("employee_loans", "employee_loan_installments.employee_loan_id", "=", "employee_loans.id")
							->select("employee_loan_installments.id", "employee_loan_installments.employee_loan_id", "employee_loans.loan_name", "employee_loan_installments.installment_value", "employee_loans.loan_complete")
							->where(["employee_loan_installments.payroll_profile_id"=>$request->payroll_profile_id, 
									 "employee_loan_installments.emp_payslip_no"=>$request->emp_payslip_no, 
									 "employee_loan_installments.installment_cancel"=>0])
							->get();
			
			$settlements = DB::select("SELECT drv_loan.loan_id, (drv_loan.loan_duration=drv_inst.installment_cnt) AS loan_complete FROM (SELECT `id` AS loan_id, `loan_amount`, `loan_duration` FROM `employee_loans` WHERE `payroll_profile_id`=?) AS drv_loan INNER JOIN (SELECT COUNT(*) AS installment_cnt, employee_loan_id, SUM(installment_value*(emp_payslip_no<=?)) AS installment_value FROM employee_loan_installments WHERE `payroll_profile_id`=? AND installment_cancel=0 GROUP BY employee_loan_id) AS drv_inst ON drv_loan.loan_id=drv_inst.employee_loan_id", [$request->payroll_profile_id, $request->emp_payslip_no, $request->payroll_profile_id]);
			
			$loan_prog = array();
			
			foreach($settlements as $p){
				$loan_prog[$p->loan_id]=$p->loan_complete;
			}
			
			$loan_list = array();
			$loan_sums = 0;
			
			foreach($installment as $r){
				$loan_complete=$loan_prog[$r->employee_loan_id];
				$more_info=($loan_complete==1)?' (final installment)':'';
				$loan_list[]=array('id'=>$r->id, 'loan_name'=>$r->loan_name, 'installment_value'=>$r->installment_value.$more_info, 'loan_complete'=>$loan_complete);
				$loan_sums+=$r->installment_value;
			}
			
			return response()->json(['loan_list'=>$loan_list, 'loan_sums'=>$loan_sums]);
		}
	}

}
