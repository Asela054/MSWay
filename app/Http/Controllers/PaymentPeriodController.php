<?php

namespace App\Http\Controllers;


use App\PaymentPeriod;
/*
use App\PayrollProcessType;

use App\PayrollProfile;
use App\Remuneration;
*/
use DB;
use Illuminate\Http\Request;
//use Illuminate\Validation\Rule;
use Validator;

class PaymentPeriodController extends Controller
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
    public function index(){
        $payroll_process_type=DB::select("SELECT payroll_process_types.id AS payroll_process_type_id, payroll_process_types.process_name, IFNULL(drv_info.payment_period_id, '') AS payment_period_id, IFNULL(drv_info.payment_period_fr, '') AS payment_period_fr, IFNULL(drv_info.payment_period_to, '') AS payment_period_to FROM payroll_process_types LEFT OUTER JOIN (SELECT drv_list.id AS payment_period_id, drv_list.payroll_process_type_id, drv_list.payment_period_fr, drv_list.payment_period_to FROM (SELECT id, payroll_process_type_id, payment_period_fr, payment_period_to FROM payment_periods) AS drv_list INNER JOIN (SELECT max(`id`) AS last_id, `payroll_process_type_id` FROM `payment_periods` group by `payroll_process_type_id`) AS drv_key ON drv_list.id=drv_key.last_id) AS drv_info ON payroll_process_types.id=drv_info.payroll_process_type_id");
		return view('Payroll.paymentPeriod.paymentPeriod_list',compact('payroll_process_type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
		$data = $request->all();
		
		$rules = array(
            'payment_period_fr' => 'required',
			'payment_period_to' => 'required'
        );

        
		$error = Validator::make($data, $rules);

        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }else if(PaymentPeriod::where(array(['payroll_process_type_id', '=', $request->payroll_process_type_id], 
								 ['payment_period_to', '>=', $request->payment_period_fr]))->count() > 0){
			return response()->json(['errors' => ['Invalid schedule']]);
		}
		
		$schedule=new PaymentPeriod;
        $schedule->payroll_process_type_id=$request->input('payroll_process_type_id'); 
		$schedule->payment_period_fr=$request->input('payment_period_fr');
		$schedule->payment_period_to=$request->input('payment_period_to');
		$schedule->created_by=$request->user()->id;
        $schedule->save();
		
		return response()->json(['success' => 'Schedule Added Successfully.', 'new_obj'=>$schedule]);
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentPeriod  $info
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentPeriod $info){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentPeriod  $info
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentPeriod  $info
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentPeriod $info){
        /*
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

        PaymentPeriod::whereId($request->remuneration_criteria)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
		
		*/
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentPeriod  $info
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
		
	}
	
	
	/*
	
	*/
	

}
