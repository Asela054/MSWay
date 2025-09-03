<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeWorkRate;
/*
use App\PayrollProcessType;
use App\PayrollProfile;
use App\Remuneration;
*/
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class EmployeeWorkRateController extends Controller
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
        $employee_list=DB::select("SELECT payroll_profiles.emp_id AS emp_etfno, employees.emp_name_with_initial AS emp_first_name FROM payroll_profiles INNER JOIN employees ON payroll_profiles.emp_id=employees.id");
		return view('Payroll.workRate.workRate_test',compact('employee_list'));
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
            'work_year' => 'required', 
			'work_month' => 'required',
			'work_days' => 'required',
			'leave_days' => 'required',
			'nopay_days' => 'required',
			'normal_rate_otwork_hrs' => 'required',
			'double_rate_otwork_hrs' => 'required'
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
		
		$workRate=NULL;
		$resMsg = 'Saved';
		$work_month = $request->input('work_month')+1;
		
		try{
			$workRate=EmployeeWorkRate::where(['emp_id'=>$request->input('emp_etfno'), 
													'work_year'=>$request->input('work_year'),
													'work_month'=>$work_month])
											->firstOrFail();
			$workRate->updated_by=$request->user()->id;
			$resMsg='Updated';
			
		}catch (ModelNotFoundException $e) {
			// Data not found. Here, you should make sure that the absence of $data won't break anything
			$workRate=new EmployeeWorkRate;
			$empRegisted = Employee::where(['id'=>$request->input('emp_etfno')])->first();
			$workRate->emp_id=$empRegisted->id;//1;
			$emp_etfno=$empRegisted->emp_etfno;
			$workRate->emp_etfno=($emp_etfno==''?NULL:$emp_etfno);//$request->input('emp_etfno'); 
			$workRate->work_year=$request->input('work_year'); 
			$workRate->work_month=$work_month; 
			$workRate->created_by=$request->user()->id;
		}
		
        //
		$workRate->work_days=$request->input('work_days');
		$workRate->leave_days=$request->input('leave_days');
		$workRate->nopay_days=$request->input('nopay_days');
		$workRate->normal_rate_otwork_hrs=$request->input('normal_rate_otwork_hrs');
		$workRate->double_rate_otwork_hrs=$request->input('double_rate_otwork_hrs');
		
		//
        $workRate->save();

       

        return response()->json(['success' => 'Work details '.$resMsg.' Successfully.', 'new_obj'=>$workRate]);
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeWorkRate  $info
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeWorkRate $info)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeWorkRate  $info
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeWorkRate  $info
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeWorkRate $info)
    {
        
		$rules = array(
            'pre_eligible_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

		$form_data = array(
            'pre_eligible_amount' =>  $request->pre_eligible_amount,
			'updated_by' => $request->user()->id
            
        );

        EmployeeWorkRate::whereId($request->remuneration_criteria)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
		
		/**/
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeWorkRate  $info
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
		
	}
	
	
	/*
	
	*/
	

}
