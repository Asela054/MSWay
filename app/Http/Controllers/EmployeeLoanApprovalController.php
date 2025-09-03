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

class EmployeeLoanApprovalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware(array('auth', 'clearance'));
    // }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
		$branch=Company::orderBy('id', 'asc')->get(); // Branch::orderBy('id', 'asc')->get();
		
		$employee = DB::table('employees')
                    //->join('branches', 'employees.emp_location', '=', 'branches.id')
					->join('companies', 'employees.emp_company', '=', 'companies.id')
                    ->join('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
					->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
                    ->select('employees.*', 'payroll_profiles.id as payroll_profile_id', 'payroll_process_types.process_name', 'payroll_profiles.basic_salary', 'companies.name as location')
                    ->get();
		*/
        return view('Payroll.employeeLoan.approveLoan_list');
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

    public function applicantsinfo(Request $request){
		if(request()->ajax()){
		$loan_rows = DB::select("select drv_loans.employee_loan_id, payroll_profiles.id, employees.emp_name_with_initial, companies.name as br_name, drv_loans.active_loan_cnt, drv_loans.loan_application_cnt, drv_loans.application_amt from (select MAX(id*(loan_approved=0)) as employee_loan_id, payroll_profile_id, sum(loan_approved*(loan_cancel=0)*(loan_complete=0)) as active_loan_cnt, sum((loan_approved=0)*(loan_cancel=0)) as loan_application_cnt, sum(issue_amount*(loan_approved=0)) as application_amt from employee_loans where loan_cancel=0 AND loan_complete=0 group by payroll_profile_id) as drv_loans inner join payroll_profiles on drv_loans.payroll_profile_id=payroll_profiles.id inner join employees on payroll_profiles.emp_id=employees.id inner join companies on employees.emp_company=companies.id WHERE drv_loans.loan_application_cnt>0");
		$loan_applicants=array();
		foreach($loan_rows as $r){
			$td_class = ($r->loan_application_cnt>1)?'details-control':'';
			$loan_applicants[]=array('loan_id'=>$r->employee_loan_id, 'profile_id'=>$r->id, 'emp_first_name'=>$r->emp_name_with_initial, 'location'=>$r->br_name, 'active_loans'=>$r->active_loan_cnt, 'loan_applications'=>$r->loan_application_cnt, 'loan_amount'=>$r->application_amt, 'td_class'=>$td_class, 'loan_approved'=>0, 'loan_rejected'=>0);
		}/**/
		//$loan_applicants[]=array('profile_id'=>1, 'emp_first_name'=>'aaa', 'location'=>'bbb', 'active_loans'=>2, 'loan_applications'=>3, 'loan_amount'=>100);
		return response()->json(['table_data'=>$loan_applicants]);
		}
	}
	
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeLoan  $loan
     * @return \Illuminate\Http\Response
     */
    public function reviewApplicationList($id)
    {
        if(request()->ajax())
        {
            $loan_rows = DB::select("select employee_loans.id, employee_loans.loan_type, employee_loans.loan_name, employee_loans.loan_date, employee_loans.loan_amount, employee_loans.loan_duration, IFNULL(drv_prog.loan_paid, 0) AS loan_paid, employee_loans.loan_freeze from employee_loans LEFT OUTER JOIN (SELECT employee_loan_id, SUM(installment_value) AS loan_paid FROM employee_loan_installments WHERE payroll_profile_id=? AND installment_cancel=0 GROUP BY employee_loan_id) AS drv_prog ON employee_loans.id=drv_prog.employee_loan_id where employee_loans.payroll_profile_id=? AND employee_loans.loan_cancel=0 AND employee_loans.loan_complete=0 AND employee_loans.loan_approved=0", [$id, $id]);
			$loan_pack=array();
			foreach($loan_rows as $r){
				$loan_pack[]=array('id'=>$r->id, 'loan_type'=>$r->loan_type, 'loan_name'=>$r->loan_name, 'loan_date'=>$r->loan_date, 'loan_amount'=>$r->loan_amount, 'loan_paid'=>$r->loan_paid, 'loan_duration'=>$r->loan_duration, 'loan_freeze'=>$r->loan_freeze);
			}
			
			return response()->json(['applications'=>$loan_pack]);
        }
    }
	
	
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeLoan  $loan
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $currentDate = Carbon::now();

		if(request()->ajax()){
			$data = DB::table('employee_loans')
                ->select(
                    'employee_loans.id',
                    'employee_loans.loan_type',
                    'employee_loans.loan_name',
                    'employee_loans.issue_amount',
                    'employee_loans.loan_date',
                    'employee_loans.loan_amount',
                    'employee_loans.loan_duration',
                    'employee_loans.interest_rate',
                    'employee_loans.installment_value',
                    'employee_loans.primery_guarantor',
                    'employee_loans.secondary_guarantor',
                    'primary_emp.emp_id as primary_emp_id',
                    'primary_emp.emp_name_with_initial as primary_emp_name',
                    'primary_emp.emp_join_date as primary_emp_join_date',
                    'secondary_emp.emp_id as secondary_emp_id',
                    'secondary_emp.emp_name_with_initial as secondary_emp_name',
                    'secondary_emp.emp_join_date as secondary_emp_join_date'
                )
    ->leftJoin('employees as primary_emp', 'employee_loans.primery_guarantor', '=', 'primary_emp.emp_id')
    ->leftJoin('employees as secondary_emp', 'employee_loans.secondary_guarantor', '=', 'secondary_emp.emp_id')
    ->where('employee_loans.id', $id)
    ->get();

    $loan = $data->first();

    $empidprimary = $loan->primery_guarantor;
    $primaryJoinDate = $loan->primary_emp_join_date;
    $empidsecondary = $loan->secondary_guarantor;
    $secondaryJoinDate = $loan->secondary_emp_join_date;

    // check loans for the primary

     $primary = DB::table('employee_loans')
     ->select('id', 'loan_type', 'loan_name', 'payroll_profile_id')
     ->where('primery_guarantor', $empidprimary)
     ->where('id', '!=', $id)
     ->where(function ($query) {
         $query->where('loan_cancel', 0)
             ->where('loan_complete', 0);
     })->get();

     if ($primary->isNotEmpty() && !empty($primaryJoinDate)) {
       

        if ( Carbon::parse($primaryJoinDate)->diffInYears($currentDate) >= 5) 
        {
           
            $primaryResult = 0; 
        }else{
            $primaryResult = 1;
        }


    }else{
        $primaryResult = 0; 
    }

      // check loans for the secondary

     $secondary = DB::table('employee_loans')
             ->select('id', 'loan_type', 'loan_name', 'payroll_profile_id')
             ->where('secondary_guarantor', $empidsecondary)
             ->where('id', '!=', $id)
             ->where(function ($query) {
                 $query->where('loan_cancel', 0)
                     ->where('loan_complete', 0);
             })->get();


             if ($secondary->isNotEmpty() && !empty($secondaryJoinDate)) {

               
                if (Carbon::parse($secondaryJoinDate)->diffInYears($currentDate) >= 5) 
                {
                    $secondaryResult = 0; 
                }else{
                    $secondaryResult = 1;
                }
            }else{
                $secondaryResult = 0; 
            }

        return response()->json([
            'loan_obj' => $loan,
            'primary_guarantor_result' => $primaryResult,
            'secondary_guarantor_result' => $secondaryResult,
        ]);
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
        /*
		approve or reject loan using modal popup buttons
		1-approve // update loan_approved = 1
		0-reject loan // update loan_cancel = 1
		*/
		$rules = array(
            'loan_name' => 'required',
			'loan_duration' => 'required',
			'issue_amount' => 'required',
			'loan_amount' => 'required'
        );
		
		if($request->act_btn==''){
			return response()->json(['errors' => array('Choose Approve or Reject')]);
		}

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

		/*
		loan-type cannot be changed in order to maintain loan ref number sequence
		*/
		
		$loan_approved = 0;
		$loan_rejected = 0;
		
		if($request->act_btn=='Approve'){
			$loan_approved = 1;
		}else if($request->act_btn=="Reject"){
			$loan_rejected = 1;
		}
		
		$form_data = array(
            'loan_name' => $request->loan_name,
			'loan_amount' =>  $request->loan_amount,
			'loan_duration' =>  $request->loan_duration,
			'interest_rate' =>  $request->interest_rate,
			'installment_value' =>  $request->installment_value,
			'issue_amount'  =>  $request->issue_amount,
			'loan_date' =>  $request->loan_date,
			'loan_approved' => $loan_approved,
			'loan_cancel' => $loan_rejected,
			'approved_by' => $request->user()->id,
            'approved_at' => Carbon::now()
        );

        EmployeeLoan::whereId($request->employee_loan_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'new_id'=>$request->hidden_id, 
								 'loan_approved'=>$loan_approved, 'loan_rejected'=>$loan_rejected]
								);
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
		
		
    }
	
	
	

}
