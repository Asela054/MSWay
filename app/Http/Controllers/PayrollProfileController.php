<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\Employee;

use App\JobCategory;
use App\PayrollAct;
use App\PayrollProcessType;
use App\PayrollProfile;
use App\Remuneration;

use Datatables;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class PayrollProfileController extends Controller
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
		//$payroll_acts=PayrollAct::orderBy('id', 'asc')->get();//
		$payroll_acts=DB::table('job_categories')
					->select('id', 'category as act_name', 'emp_payroll_workdays as total_work_days')
					->get();
		$remuneration = Remuneration::where(['remuneration_cancel' => 0, 'allocation_method' => "FIXED"])->orderBy('id', 'asc')->get();
		
		
		
        return view('Payroll.payrollProfile.payrollProfile_list',compact('branch', 'payroll_process_type', 'payroll_acts', 'remuneration'));
    }
	function getEmployeeData(){
		$employee = DB::table('employees')
                    //->join('branches', 'employees.emp_location', '=', 'branches.id')
					->join('companies', 'employees.emp_company', '=', 'companies.id')
                    ->leftjoin('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
					->leftjoin('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
                    ->select('employees.*', DB::raw("IFNULL(payroll_process_types.process_name, '') as process_name"), 'payroll_profiles.basic_salary', 'companies.name as location')
                    ->get();
		return Datatables::of($employee)->make(true);
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
        /*
		'emp_etfno' => 'required',
		*/
		$rules = array(
            'payroll_process_type_id' => 'required',
			'payroll_act_id' => 'required',//'min:1',//'integer',//'numeric|min:1',//
			'basic_salary' => 'required'
        );
		
		if(empty($request->payroll_act_id)){
			return response()->json(['errors' => array('Select a job category')]);
		}

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
		/*
        $form_data = array(
            'remuneration_name'        =>  $request->remuneration_name
            
        );
		*/
		
		$strip_emp_etfno = str_replace('0', '', $request->input('emp_etfno'));
		
		if($this->epfDuplicates($request->input('emp_etfno'), $request->input('emp_id'))){
			return response()->json(['errors' => array('duplicate value not allowed for epf. no')]);
		}
		
		//$emp_etfno=($request->input('emp_etfno')=='')?NULL:$request->input('emp_etfno');
		$emp_etfno=($strip_emp_etfno=='')?NULL:$request->input('emp_etfno');
		
		$employee_bank_id=($request->input('employee_bank_id')=='')?NULL:$request->input('employee_bank_id');
		
		
		$payrollProfile=NULL;
		$resMsg = 'Added';
		
		try{
			$payrollProfile=PayrollProfile::where(['emp_id'=>$request->input('emp_id')])
											->firstOrFail();
			
			$payrollProfile->updated_by=$request->user()->id;
			$resMsg='Updated';
			
		}catch (ModelNotFoundException $e) {
			$payrollProfile=new PayrollProfile;
			$payrollProfile->emp_id=$request->input('emp_id');
			$payrollProfile->created_by=$request->user()->id;
		}
		
        
        $payrollProfile->emp_etfno=$emp_etfno; 
		$payrollProfile->payroll_process_type_id=$request->input('payroll_process_type_id'); 
		$payrollProfile->payroll_act_id=$request->input('payroll_act_id');
		$payrollProfile->employee_bank_id=$employee_bank_id;
		$payrollProfile->basic_salary=$request->input('basic_salary'); 
		$payrollProfile->day_salary=$request->input('day_salary'); 
		$payrollProfile->employee_executive_level=$request->input('employee_executive_level');
		
		$payrollProfile->epfetf_contribution=$request->input('epfetf_contribution');
		
        $payrollProfile->save();

       

        return response()->json(['success' => 'Payroll Profile '.$resMsg.' Successfully.', 'new_obj'=>$payrollProfile]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PayrollProfile  $payrollProfile
     * @return \Illuminate\Http\Response
     */
    public function show(PayrollProfile $payrollProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PayrollProfile  $payrollProfile
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = DB::table('employees')
                    ->leftjoin('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
                    //->select('employees.emp_first_name', 'employees.emp_name_with_initial', 'employees.id as employee_id', 'employees.emp_etfno as employee_etfno', 'payroll_profiles.*')
					->select('employees.emp_first_name', 'employees.emp_name_with_initial', 'employees.id as employee_id', 'employees.emp_etfno as employee_etfno', 'payroll_profiles.id', DB::raw("COALESCE(employees.job_category_id, NULLIF(payroll_profiles.payroll_act_id, 0), '') as payroll_act_id"), 'payroll_profiles.payroll_process_type_id', 'payroll_profiles.employee_bank_id', 'payroll_profiles.employee_executive_level', 'payroll_profiles.basic_salary', 'payroll_profiles.day_salary', 'payroll_profiles.payroll_act_id AS payroll_profile_act_id', 'payroll_profiles.emp_etfno as payroll_profile_emp_etfno', 'payroll_profiles.epfetf_contribution')
					->where(['employees.id' => $id])
                    ->get();
			
			$bank_ac = DB::table('employee_banks')
					->where(['emp_id' => $id])
                    ->get();
			
			$spec_msg = '';
			$form_result_html = '';
			$revw_salary = '0';
			
			if($data[0]->id!=''){
				if($data[0]->payroll_profile_emp_etfno!=$data[0]->employee_etfno){
					$spec_msg = 'EPF no. of this profile has been changed. ';
					//$more_str = 'also ';
				}
				
				if($data[0]->payroll_profile_act_id!=$data[0]->payroll_act_id){
					$spec_msg .= 'Please revise the employee salary (basic, day) accoring to updated job category.';
					$revw_salary = '1';
				}
				
				if($spec_msg!=''){
					$form_result_html = '<div class="alert alert-danger">'.$spec_msg.'</div>';
				}
			}
			
			/*
			$welfare_pack = DB::table('remunerations')
							->leftjoin('remuneration_profiles', 'remunerations.id', '=', 'remuneration_profiles.remuneration_id')
							->select('remunerations.id', 'remunerations.remuneration_name', 'remuneration_profiles.new_eligible_amount', 'remuneration_profiles.remuneration_signout')
							->where(['remuneration_profiles.payroll_profile_id'=>$data[0]->id, 'remunerations.remuneration_cancel'=>0])
							->get();
			*/
			$welfare_rows = DB::select("select remunerations.id, drv_profile.profile_id, remunerations.remuneration_name, drv_profile.new_eligible_amount, drv_profile.remuneration_signout, remunerations.advanced_option_id from remunerations left outer join (select id as profile_id, remuneration_id, new_eligible_amount, remuneration_signout from remuneration_profiles where payroll_profile_id=?) as drv_profile on remunerations.id=drv_profile.remuneration_id where remunerations.allocation_method='FIXED' AND remunerations.remuneration_cancel=?", [$data[0]->id, 0]);
			$welfare_pack=array();
			foreach($welfare_rows as $r){
				$remuneration_signout='-1';
				$new_eligible_amount='0';
				$profile_id='';
				if(isset($r->profile_id)){
					$profile_id=$r->profile_id;
					$remuneration_signout=$r->remuneration_signout;
					$new_eligible_amount=$r->new_eligible_amount;
					
				}
				
				$welfare_pack[]=array($remuneration_signout, 
									  $r->remuneration_name, 
									  $new_eligible_amount, $r->id, 
									  $profile_id, 
									  $r->advanced_option_id);
			}
			
			/*
			$welfare_cnt = DB::select("select count(*) as cnt from remunerations left outer join (select id as profile_id, remuneration_id, new_eligible_amount, remuneration_signout from remuneration_profiles where payroll_profile_id=?) as drv_profile on remunerations.id=drv_profile.remuneration_id where remunerations.allocation_method='FIXED' AND remunerations.remuneration_cancel=?", [$data[0]->id, 0]);
			'tot_rows'=>$welfare_cnt[0]->cnt
			*/
			
            return response()->json(['pre_obj' => $data[0], 'package'=>$welfare_pack, 'bank_ac_list'=>$bank_ac, 
									'form_result_html'=>$form_result_html, 'revw_salary'=>$revw_salary]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PayrollProfile  $payrollProfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PayrollProfile $payrollProfile)
    {
        $rules = array(
            'payroll_act_id' => 'required',
			'basic_salary' => 'required'
        );
		
		if(empty($request->payroll_act_id)){
			return response()->json(['errors' => array('Select a job category')]);
		}

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }
		
		$strip_emp_etfno = str_replace('0', '', $request->input('emp_etfno'));
		
		if($this->epfDuplicates($request->input('emp_etfno'), $request->input('emp_id'))){
			return response()->json(['errors' => array('duplicate value not allowed for epf. no')]);
		}

		//$emp_etfno=($request->input('emp_etfno')=='')?NULL:$request->input('emp_etfno');
		$emp_etfno=($strip_emp_etfno=='')?NULL:$request->input('emp_etfno');
		
		$employee_bank_id=($request->input('employee_bank_id')=='')?NULL:$request->input('employee_bank_id');
		
		$form_data = array(
            'emp_etfno' => $emp_etfno,
			'payroll_process_type_id' =>  $request->payroll_process_type_id,
			'payroll_act_id' => $request->payroll_act_id,
			'employee_bank_id' => $employee_bank_id,
			'basic_salary' =>  $request->basic_salary,
			'day_salary' =>  $request->day_salary,
			'employee_executive_level' => $request->employee_executive_level,
			'epfetf_contribution' => $request->epfetf_contribution,
			'updated_by' => $request->user()->id
            
        );

        PayrollProfile::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'alt_obj'=>$form_data]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PayrollProfile  $payrollProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*
		$data = PayrollProfile::findOrFail($id);
        $data->delete();
		*/
		
		$form_data = array('updated_by'=>auth()->user()->id, 'remuneration_cancel'=>1);
		
		$affectedRows=PayrollProfile::where(['id'=>$id, 'remuneration_cancel'=>0])->update($form_data);
		
		$result = array('result'=>(($affectedRows==1)?'success':'error'));
		
		return response()->json($result);
    }
	
	function epfDuplicates($epfno, $empid){
		if(str_replace('0', '', $epfno)!=''){
			if(PayrollProfile::where('emp_etfno', '=', $epfno)->exists()){
				$payroll_info=PayrollProfile::where('emp_etfno', '=', $epfno)->first();
				return ($payroll_info->emp_id==$empid)?false:true;
			}
		}
	}
	
	/*
	
	*/
}
