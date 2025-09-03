<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Remuneration;
use App\RemunerationEligibilityDay;

use DB;

use Illuminate\Http\Request;
use Validator;

class RemunerationController extends Controller
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
        $remuneration = Remuneration::where(['remuneration_cancel'=>0, 'allocation_method'=>'FIXED'])->orderBy('id', 'asc')->get();
		$eligibleinfo = RemunerationEligibilityDay::orderBy('id', 'asc')->get();
        return view('Payroll.remuneration.remuneration_list',compact('remuneration', 'eligibleinfo'));
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
        try{
			return DB::transaction(function() use ($request) {
				$rules = array(
					'remuneration_name' => 'required',
					'employee_work_rate_work_days_exclusions' => 'required'
				);
		
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
				
				$allocation_method_list = array('M1'=>"FIXED", 'M2'=>"TERMS");
				
				$remuneration=new Remuneration;
				$remuneration->remuneration_name=$request->input('remuneration_name'); 
				$remuneration->remuneration_type=$request->input('remuneration_type'); 
				$value_group = ($request->input('remuneration_type')=='Addition')?1:-1;
				$remuneration->value_group=$value_group; 
				$remuneration->epf_payable=$request->input('epf_payable'); 
				
				$tax_spec_code = ($request->input('taxcalc_spec_code')==1)?'OTHER_REM_TL':NULL;
				$remuneration->taxcalc_spec_code=$tax_spec_code;

                $remuneration->ot_applicable = $request->input('ot_applicable') == 1 ? 1 : 0;
                $remuneration->nopay_applicable = $request->input('nopay_applicable') == 1 ? 1 : 0;

				
				$remuneration->allocation_method=$allocation_method_list[$request->input('allocation_method')];
				
				$advanced_option = 0;
				//if($request->has('action_button')){
					$advanced_option = $request->input('advanced_option_id');//($request->input('action_button')==1)?1:0;
					$remuneration->advanced_option_id = $advanced_option;
					$remuneration->employee_work_rate_work_days_exclusions = $request->input('employee_work_rate_work_days_exclusions');
				//}
				
				$remuneration->created_by=$request->user()->id;
				$remuneration->save();
				
				if($advanced_option==1){
					$eligible_criteria = new RemunerationEligibilityDay;
					$eligible_criteria->remuneration_id=$remuneration->id;
					$eligible_criteria->min_days=1;
					$eligible_criteria->max_days=30;
					$eligible_criteria->pre_eligible_amount=0;
					$eligible_criteria->created_by=$request->user()->id;
					
					if($eligible_criteria->save()){
						$remuneration->advanced_option_id = $eligible_criteria->id;
					}else{
						throw new \Exception('Something wrong');
					}
				}
				
				$new_remuneration = $remuneration;
				
				/**/
				if($remuneration->allocation_method=="TERMS"){
					$employee = DB::table('employees')
							//->join('branches', 'employees.emp_location', '=', 'branches.id')
							->join('companies', 'employees.emp_company', '=', 'companies.id')
							->join('payroll_profiles', 'employees.id', '=', 'payroll_profiles.emp_id')
							->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
							->select('employees.*', 'payroll_profiles.id as payroll_profile_id', 'payroll_process_types.process_name', 'payroll_profiles.basic_salary', 'companies.name as location')
							->get();
					$employee_list = array();
					foreach($employee as $r){
						$employee_list[]=array('id'=>'', 'payroll_profile_id'=>$r->payroll_profile_id, 'emp_first_name'=>$r->emp_name_with_initial, 'location'=>$r->location, 'basic_salary'=>$r->basic_salary, 'process_name'=>(isset($r->process_name)?$r->process_name:''), 'payment_cancel'=>1);
					}
					
					$new_remuneration = array('header'=>$remuneration, 'detail'=>$employee_list);
				}
				
		
				return response()->json(['success' => 'Remuneration Added Successfully.', 'new_obj'=>$new_remuneration]);
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Remuneration  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function show(Remuneration $remuneration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Remuneration  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = Remuneration::findOrFail($id);
			
			$tax_spec_code = $data->taxcalc_spec_code;
			$tax_spec_opts = empty($tax_spec_code)?0:1;
			$data->taxcalc_spec_code = $tax_spec_opts;
			
            return response()->json(['pre_obj' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Remuneration  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Remuneration $remuneration)
    {
        $rules = array(
            'remuneration_name' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $value_group = (($request->remuneration_type=='Addition')?1:-1);
		/*
		$form_data = array(
            'remuneration_name' =>  $request->remuneration_name,
			'remuneration_type' =>  $request->remuneration_type,
			'value_group' =>  $value_group,
			'epf_payable' =>  $request->epf_payable,
			'updated_by' => $request->user()->id
            
        );

        Remuneration::whereId($request->hidden_id)->update($form_data);
		*/
		
		$selected_remuneration = Remuneration::findOrFail($request->hidden_id);
		$tax_spec_code = ($request->taxcalc_spec_code==1)?($selected_remuneration->payslip_spec_code.'_TL'):NULL;
		
		$form_data = array(
            'remuneration_name' =>  $request->remuneration_name,
			'remuneration_type' =>  $request->remuneration_type,
			'value_group' =>  $value_group,
			'epf_payable' =>  $request->epf_payable,
			'taxcalc_spec_code' =>$tax_spec_code,
            'ot_applicable' => $request->ot_applicable,
            'nopay_applicable' => $request->nopay_applicable,
			'updated_by' => $request->user()->id
            
        );
		
		Remuneration::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'alt_obj'=>$form_data, 'alt_id'=>$request->hidden_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Remuneration  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*
		$data = Remuneration::findOrFail($id);
        $data->delete();
		*/
		
		$form_data = array('updated_by'=>auth()->user()->id, 'remuneration_cancel'=>1);
		
		$affectedRows=Remuneration::where(['id'=>$id, 'remuneration_cancel'=>0])->update($form_data);
		
		$result = array('result'=>(($affectedRows==1)?'success':'error'));
		
		return response()->json($result);
    }
	
	
	/*
	
	*/
}
