<?php

namespace App\Http\Controllers;

use App\EmployeePayday;

use App\PaymentPeriod;
/*
use App\PayrollProcessType;

use App\PayrollProfile;
use App\Remuneration;
*/
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
//use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $permission = $user->can('Salary-schedule-list');
        if(!$permission) {
            abort(403);
        }
		
		$paydays = EmployeePayday::where(['payday_cancel'=>0])->get();
		
        $payroll_process_type=DB::select("SELECT payroll_process_types.id AS payroll_process_type_id, payroll_process_types.process_name, IFNULL(drv_info.payment_period_id, '') AS payment_period_id, IFNULL(drv_info.payment_period_fr, '') AS payment_period_fr, IFNULL(CONCAT(drv_info.work_period_total_days, 'days, ', drv_info.work_period_total_hours, 'Hrs'), '') AS payment_total_days_hours, IFNULL(drv_info.payment_period_to, '') AS payment_period_to, IFNULL(drv_info.payday_name, '') AS payday_name FROM payroll_process_types LEFT OUTER JOIN (SELECT drv_list.id AS payment_period_id, drv_list.payroll_process_type_id, drv_list.payment_period_fr, drv_list.payment_period_to, drv_list.work_period_total_days, drv_list.work_period_total_hours, IFNULL(employee_paydays.payday_name, 'General') AS payday_name FROM (SELECT id, payroll_process_type_id, payment_period_fr, payment_period_to, work_period_total_days, work_period_total_hours, employee_payday_id FROM payment_periods) AS drv_list INNER JOIN (SELECT max(`id`) AS last_id, `payroll_process_type_id` FROM `payment_periods` group by `payroll_process_type_id`) AS drv_key ON drv_list.id=drv_key.last_id LEFT OUTER JOIN (SELECT id, payday_name from employee_paydays WHERE payday_cancel=0) AS employee_paydays ON (drv_list.employee_payday_id=employee_paydays.id AND IFNULL(drv_list.id, 0)>0)) AS drv_info ON payroll_process_types.id=drv_info.payroll_process_type_id");
		return view('Payroll.paymentPeriod.paymentPeriod_list',compact('payroll_process_type', 'paydays'));
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
        $user = Auth::user();
        $permission = $user->can('Salary-schedule-create');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to create salary schedule.')]);
        }

		$data = $request->all();
		
		$rules = array(
            'payment_period_fr' => 'required',
			'payment_period_to' => 'required'
        );

        
		$error = Validator::make($data, $rules);

        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }else if(PaymentPeriod::where(array(['payroll_process_type_id', '=', $request->payroll_process_type_id], 
								 ['employee_payday_id', '=', $request->employee_payday_id], 
								 ['payment_period_to', '>=', $request->payment_period_fr]))->count() > 0){
			return response()->json(['errors' => ['Invalid schedule']]);
		}else if($request->input('advance_payment_date')){
			$advanceDate = Carbon::parse($request->input('advance_payment_date'));
			$schFr = Carbon::parse($request->input('payment_period_fr'));
			$schTo = Carbon::parse($request->input('payment_period_to'));
			if (!$advanceDate->between($schFr, $schTo)) {
				return response()->json(['errors' => ['Advance date should fall between selected From, To dates']]);
			}
		}
		
		$schedule=new PaymentPeriod;
        $schedule->payroll_process_type_id=$request->input('payroll_process_type_id'); 
		$schedule->payment_period_fr=$request->input('payment_period_fr');
		$schedule->payment_period_to=$request->input('payment_period_to');
		$schedule->advance_payment_date=$request->input('advance_payment_date');
		$schedule->work_period_total_days=($request->input('work_period_total_days')>0)?$request->input('work_period_total_days'):0;
		$schedule->work_period_total_hours=($request->input('work_period_total_hours')>0)?$request->input('work_period_total_hours'):0;
		$schedule->employee_payday_id=$request->input('employee_payday_id');
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
	
	public function reviewPaydayPeriods($id)
    {
        if(request()->ajax())
        {
            $payday_rows = DB::select("SELECT IFNULL(drv_info.payment_period_id, '') AS id, IFNULL(drv_info.payment_period_fr, '') AS payment_period_fr, IFNULL(CONCAT(drv_info.work_period_total_days, 'days, ', drv_info.work_period_total_hours, 'Hrs'), '') AS payment_total_days_hours, IFNULL(drv_info.payment_period_to, '') AS payment_period_to, IFNULL(drv_info.payday_name, '') AS payday_name FROM payroll_process_types LEFT OUTER JOIN (SELECT drv_list.id AS payment_period_id, drv_list.payroll_process_type_id, drv_list.payment_period_fr, drv_list.payment_period_to, drv_list.work_period_total_days, drv_list.work_period_total_hours, IFNULL(employee_paydays.payday_name, 'General') AS payday_name FROM (SELECT id, payroll_process_type_id, payment_period_fr, payment_period_to, work_period_total_days, work_period_total_hours, employee_payday_id FROM payment_periods) AS drv_list INNER JOIN (SELECT max(`id`) AS last_id, `payroll_process_type_id` FROM `payment_periods` group by `payroll_process_type_id`, employee_payday_id) AS drv_key ON drv_list.id=drv_key.last_id LEFT OUTER JOIN (select id, payday_name from employee_paydays WHERE payday_cancel=0) AS employee_paydays ON (drv_list.employee_payday_id=employee_paydays.id AND IFNULL(drv_list.id, 0)>0)) AS drv_info ON payroll_process_types.id=drv_info.payroll_process_type_id WHERE payroll_process_types.id=?", [$id]);
			$payday_list=array();
			foreach($payday_rows as $r){
				$payday_list[]=array('id'=>$r->id, 'payday_name'=>$r->payday_name, 'payment_period_fr'=>$r->payment_period_fr, 'payment_period_to'=>$r->payment_period_to, 'payment_total_days_hours'=>$r->payment_total_days_hours);
			}
			
			return response()->json(['paydays'=>$payday_list]);
        }
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
