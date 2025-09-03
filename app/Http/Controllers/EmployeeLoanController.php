<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\Employee;

use App\EmployeeLoan;
/*
use App\PayrollProcessType;
use App\PayrollProfile;
use App\Remuneration;
*/
use DB;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Session;

class EmployeeLoanController extends Controller
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
        $companyId = Session::get('company_id');
        $branch=Company::where('id', $companyId)->orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		
		$employee = DB::table('employees')
                    //->join('branches', 'employees.emp_location', '=', 'branches.id')
					->join('companies', 'employees.emp_company', '=', 'companies.id')
                    ->join('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
					->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
                    ->select('employees.*', 'payroll_profiles.id as payroll_profile_id', 'payroll_process_types.process_name', 'payroll_profiles.basic_salary', 'companies.name as location')
                    ->get();
		
        return view('Payroll.employeeLoan.employeeLoan_list',compact('branch', 'employee'));
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
    public function store(Request $request)
    {
        $rules = array(
            'loan_name' => 'required',
			'payroll_profile_id' => 'required',
			'loan_duration' => 'required',
			'loan_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
		

		$res=DB::insert("insert into employee_loans (loan_type, loan_name, payroll_profile_id, loan_refno_prefix, loan_refno_sequence, issue_amount, loan_date, loan_amount, loan_duration, interest_rate, installment_value,  primery_guarantor, secondary_guarantor,created_by, created_at) SELECT ? AS loan_type, ? AS loan_name, ? AS `payroll_profile_id`, DATE_FORMAT(DATE(NOW()), '%Y-%m') AS loan_refno_prefix, (count(*)+1) as loan_refno_sequence, ? AS issue_amount, ? AS loan_date, ? AS loan_amount, ? AS loan_duration, ? AS interest_rate, ? as installment_value, ? AS primery_guarantor, ? AS secondary_guarantor, ? AS created_by, NOW() AS created_at FROM `employee_loans` WHERE `payroll_profile_id`=? and `loan_type`=? and `loan_refno_prefix`=DATE_FORMAT(DATE(NOW()), '%Y-%m')", [$request->input('loan_type'), $request->input('loan_name'), $request->input('payroll_profile_id'), $request->input('issue_amount'), $request->input('loan_date'), $request->input('loan_amount'), $request->input('loan_duration'), $request->input('interest_rate'), $request->input('installment_value'), $request->input('employeegarentee'), $request->input('employee_secondgarentee'), $request->user()->id, $request->input('payroll_profile_id'), $request->input('loan_type')]);
		
		$new_id=DB::getPdo()->lastInsertId();
       

        return response()->json(['success' => 'Loan Added Successfully.', 'new_id'=>$new_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeLoan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeLoan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeLoan  $loan
     * @return \Illuminate\Http\Response
     */
    public function reviewLoanList($id)
    {
        if(request()->ajax())
        {
            $data = DB::table('employees')
                    ->join('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
                    ->select('employees.emp_first_name', 'employees.emp_name_with_initial', 'payroll_profiles.*')
					->where(['payroll_profiles.id' => $id])
                    ->get();
			
			/*
			$welfare_pack = DB::table('remunerations')
							->leftjoin('remuneration_profiles', 'remunerations.id', '=', 'remuneration_profiles.remuneration_id')
							->select('remunerations.id', 'remunerations.remuneration_name', 'remuneration_profiles.new_eligible_amount', 'remuneration_profiles.remuneration_signout')
							->where(['remuneration_profiles.payroll_profile_id'=>$data[0]->id, 'remunerations.remuneration_cancel'=>0])
							->get();
			*/
			$loan_rows = DB::select("select employee_loans.id, employee_loans.loan_type, employee_loans.loan_date, employee_loans.loan_amount, employee_loans.loan_duration, IFNULL(drv_prog.loan_paid, 0) AS loan_paid, employee_loans.loan_freeze, employee_loans.loan_complete from employee_loans LEFT OUTER JOIN (SELECT employee_loan_id, SUM(installment_value) AS loan_paid FROM employee_loan_installments WHERE payroll_profile_id=? AND installment_cancel=0 GROUP BY employee_loan_id) AS drv_prog ON employee_loans.id=drv_prog.employee_loan_id where employee_loans.payroll_profile_id=? AND employee_loans.loan_cancel=0", [$id, $id]); // AND employee_loans.loan_complete=0
			$loan_pack=array();
			foreach($loan_rows as $r){
				$loan_pack[]=array('id'=>$r->id, 'loan_type'=>$r->loan_type, 'loan_date'=>$r->loan_date, 'loan_amount'=>$r->loan_amount, 'loan_paid'=>$r->loan_paid, 'loan_duration'=>$r->loan_duration, 'loan_freeze'=>$r->loan_freeze, 'loan_complete'=>$r->loan_complete);
			}
			
			/*
			$welfare_cnt = DB::select("select count(*) as cnt from remunerations left outer join (select id as profile_id, remuneration_id, new_eligible_amount, remuneration_signout from remuneration_profiles where payroll_profile_id=?) as drv_profile on remunerations.id=drv_profile.remuneration_id where remunerations.allocation_method='FIXED' AND remunerations.remuneration_cancel=?", [$data[0]->id, 0]);
			'tot_rows'=>$welfare_cnt[0]->cnt
			*/
			
            return response()->json(['pre_obj' => $data[0], 'package'=>$loan_pack]);
        }
    }
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeLoan  $loan
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		if(request()->ajax()){
			$data=DB::table('employee_loans')
				->select('id', 'loan_type', 'loan_name', 'issue_amount', 'loan_date', 'loan_amount', 'loan_duration', 'interest_rate', 'installment_value')
				->where(['id'=>$id])
				->get();
			
			return response()->json(['loan_obj'=>$data[0]]);
		}
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeLoan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeLoan $loan)
    {
        $rules = array(
            'loan_name' => 'required',
			'loan_duration' => 'required',
			'issue_amount' => 'required',
			'loan_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

		/*
		loan-type cannot be changed in order to maintain loan ref number sequence
		*/
		
		$form_data = array(
            'loan_name' => $request->loan_name,
			'loan_amount' =>  $request->loan_amount,
			'loan_duration' =>  $request->loan_duration,
			'interest_rate' =>  $request->interest_rate,
			'installment_value' =>  $request->installment_value,
			'issue_amount'  =>  $request->issue_amount,
			'loan_date' =>  $request->loan_date,
            'primery_guarantor'  =>  $request->employeegarentee,
            'secondary_guarantor'  =>  $request->employee_secondgarentee,
			'updated_by' => $request->user()->id
            
        );

        EmployeeLoan::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'new_id'=>$request->hidden_id]);
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeLoan  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*
		$data = EmployeeLoan::findOrFail($id);
        $data->delete();
		*/
		
		$affectedRows=0;
		$validate_msg='';
		
		$loan_installment = DB::select("SELECT count(*) AS cnt FROM (SELECT employee_loan_installments.employee_loan_id FROM `employee_loan_installments` LEFT OUTER JOIN employee_payslips ON (employee_loan_installments.payroll_profile_id=employee_payslips.payroll_profile_id AND employee_loan_installments.emp_payslip_no=employee_payslips.emp_payslip_no) WHERE employee_loan_installments.employee_loan_id=? AND employee_loan_installments.installment_cancel=0 AND employee_payslips.emp_payslip_no IS NULL GROUP BY employee_loan_installments.employee_loan_id, employee_loan_installments.emp_payslip_no) AS drv", [$id]);
		
		if($loan_installment[0]->cnt==0){
			$form_data = array('updated_by'=>auth()->user()->id, 'loan_cancel'=>1);
			
			$affectedRows=EmployeeLoan::where(['id'=>$id, 'loan_cancel'=>0, 'loan_complete'=>0])->update($form_data);
		}else{
			$validate_msg="Pending loan installments must be cancelled.";
		}
		
		$result = array('result'=>(($affectedRows==1)?'success':'error'), 'more_info'=>$validate_msg);
		
		return response()->json($result);
    }
	
	
	public function freeze(Request $request){
		if($request->ajax()){
			$form_data = array(
				'loan_freeze' =>  $request->loan_freeze,
				'updated_by' => $request->user()->id
				
			);
	
			$affectedRows=DB::table('employee_loans')
					->where(['id'=>$request->id, 'loan_freeze'=>(1-$request->loan_freeze)])
					->update($form_data);
			
			$result = array('result'=>(($affectedRows==1)?'success':'error'));
	
			return response()->json($result);
		}
	}

    public function checkloangurante(Request $request){

        $empId = $request->input('employee_id');

        $loans = DB::table('employee_loans as el')
        ->leftJoin('employees as primery', 'el.primery_guarantor', '=', 'primery.emp_id')
       ->leftJoin('employees as secondry', 'el.secondary_guarantor', '=', 'secondry.emp_id')
        ->select('el.id',
                'el.loan_type',
                'el.loan_name',
                'el.payroll_profile_id',
                'primery.emp_join_date as primary_guarantor_join_date',
                'secondry.emp_join_date as secondary_guarantor_join_date')

        ->where(function ($query) use ($empId) {
            $query->where('el.primery_guarantor', $empId)
              ->orWhere('el.secondary_guarantor', $empId);
        })
        ->where(function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->where('el.loan_cancel', 0)
                         ->where('el.loan_complete', 0);
            });
        })
        ->get();

        if ($loans->isNotEmpty()) {
            $loan = $loans->first();

            $primaryJoinDate = $loan->primary_guarantor_join_date ?? null;
            $secondaryJoinDate = $loan->secondary_guarantor_join_date ?? null;
            $currentDate = Carbon::now();

            if (!empty($primaryJoinDate) && Carbon::parse($primaryJoinDate)->diffInYears($currentDate) >= 5) {
                $result = 1;
            } elseif ( !empty($secondaryJoinDate) && Carbon::parse($secondaryJoinDate)->diffInYears($currentDate) >= 5) {
                $result = 1;
            }

            $result = 0;
            
        } else {
            $result = 0;
        }

        return response()->json($result);
    }

}
