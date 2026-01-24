<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\RemunerationExtra;
use App\RemunerationProfile;

use App\PaymentPeriod;
use App\PayrollProfile;

use App\PayrollProfileExtra;

use Carbon\Carbon;
use DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class RemunerationProfileController extends Controller
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
        /*
		$remuneration = RemunerationProfile::where('remuneration_cancel', 0)->orderBy('id', 'asc')->get();
        return view('Payroll.remuneration.remuneration_list',compact('remuneration'));
		*/
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
        $user = Auth::user();
        $permission = $user->can('Payrollprofile-create');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to create payroll profile remunerations.')]);
        }

        $rules = array(
			'payroll_profile_id' => 'required',
            'new_eligible_amount' => 'required'
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
		
		
		$remuneration=NULL;
		$resMsg = 'Added';
		
		try{
			$remuneration=RemunerationProfile::where(['payroll_profile_id'=>$request->input('payroll_profile_id'), 
													'remuneration_id'=>$request->input('remuneration_id')])
											->firstOrFail();
			
			$remuneration->updated_by=$request->user()->id;
			$resMsg='Updated';
			
		}catch (ModelNotFoundException $e) {
			$remuneration=new RemunerationProfile;
			$remuneration->payroll_profile_id=$request->input('payroll_profile_id'); 
			$remuneration->remuneration_id=$request->input('remuneration_id'); 
			$remuneration->created_by=$request->user()->id;
		}
        
		$remuneration->new_eligible_amount=$request->input('new_eligible_amount'); 
        $remuneration->save();

       

        return response()->json(['success' => 'Remuneration '.$resMsg.' Successfully.', 'new_obj'=>$remuneration]);
    }
	
	public function manageExtras(Request $request)
    {
        try{
			return DB::transaction(function() use ($request){
				$rules = array(
					'payroll_profile_id' => 'required',
					'extra_entitle_amount' => 'required'
				);
		
				$error = Validator::make($request->all(), $rules);
		
				if($error->fails())
				{
					return response()->json(['errors' => $error->errors()->all()]);
				}
				
				$employeePayProfile = PayrollProfile::find($request->input('payroll_profile_id'));
				$paymentPeriod=PaymentPeriod::where(['payroll_process_type_id'=>$employeePayProfile->payroll_process_type_id,
													 'employee_payday_id'=>$employeePayProfile->employee_payday_id])
									->latest()
									->first();
				
				$payment_period_id = empty($paymentPeriod)?0:$paymentPeriod->id;
				$sql_adv = "SELECT drv_term.emp_term_pay_id, drv_ext.id, drv_term.payment_amount, drv_term.payment_cancel FROM (select id as emp_term_pay_id, payment_amount, payment_cancel from employee_term_payments where payroll_profile_id=? and remuneration_id=?) AS drv_term ";
				/*$sql_adv .= "LEFT OUTER JOIN (select max(id*(`remuneration_extra_id`=?)) as id, employee_term_payment_id from employee_term_payment_extras where payment_period_id=? group by employee_term_payment_id) AS drv_ext ON drv_term.emp_term_pay_id=drv_ext.employee_term_payment_id";*/
				$sql_adv .= "LEFT OUTER JOIN (select max(id*(`remuneration_extra_id`=?)) as id, employee_term_payment_id, payment_period_id from employee_term_payment_extras group by employee_term_payment_id) AS drv_ext ON drv_term.emp_term_pay_id=drv_ext.employee_term_payment_id where payment_period_id=?";
				$rows_adv = DB::select($sql_adv, [$request->input('payroll_profile_id'), $request->input('remuneration_id'), $request->input('remuneration_extra_id'), $payment_period_id]);
				
				$adv_term_payment_id = 0;
				$adv_term_payment_amount = 0;
				$adv_term_diff = 0;
				
				$extraPaymentData = RemunerationExtra::find($request->input('remuneration_extra_id'));
				$extra_value_group = $extraPaymentData->value_group;
				
				if(count($rows_adv)>0){
					if($rows_adv[0]->id=='0'){
						$advancePaymentDate = Carbon::parse($paymentPeriod->advance_payment_date);
						$revDate = Carbon::today();
						//if($advancePaymentDate->isAfter($revDate))
						if($advancePaymentDate->gte($revDate)){
							if($rows_adv[0]->payment_cancel=='0'){
								return response()->json(['errors' => array('Please cancel employee advance of '.$rows_adv[0]->payment_amount.' for '.$paymentPeriod->advance_payment_date)]);
							}else{
								$adv_term_payment_id = $rows_adv[0]->emp_term_pay_id;
								$adv_term_payment_amount = $rows_adv[0]->payment_amount;
							}
						}
					}
				}
				
				$remuneration_extra=NULL;
				$resMsg = 'Added';
				
				try{
					$remuneration_extra=PayrollProfileExtra::where(['payroll_profile_id'=>$request->input('payroll_profile_id'), 
															'remuneration_extra_id'=>$request->input('remuneration_extra_id')])
													->firstOrFail();
					
					$act_extra_entitle_amount = $remuneration_extra->extra_entitle_amount*(1-$remuneration_extra->payroll_profile_extra_signout);
					
					$remuneration_extra->payroll_profile_extra_signout=0;
					$remuneration_extra->updated_by=$request->user()->id;
					
					$adv_term_diff = ($request->input('extra_entitle_amount')-$act_extra_entitle_amount);
					
					$resMsg='Updated';
					
				}catch (ModelNotFoundException $e) {
					$remuneration_extra=new PayrollProfileExtra;
					$remuneration_extra->payroll_profile_id=$request->input('payroll_profile_id'); 
					$remuneration_extra->remuneration_id=$request->input('remuneration_id'); 
					$remuneration_extra->remuneration_extra_id=$request->input('remuneration_extra_id');
					$remuneration_extra->created_by=$request->user()->id;
					
					$adv_term_diff = $request->input('extra_entitle_amount');
				}
				
				$remuneration_extra->extra_entitle_amount=$request->input('extra_entitle_amount'); 
				$featRevised = $remuneration_extra->save();
				
				$affectedRows = 1;
				
				if($adv_term_payment_id>0){
					$adv_term_payment_amount += ($adv_term_diff*$extra_value_group);
					$form_data = array('updated_by'=>auth()->user()->id, 'payment_amount'=>$adv_term_payment_amount);
					$affectedRows = EmployeeTermPayment::where(['id'=>$adv_term_payment_id, 'payment_cancel'=>1])->update($form_data);
				}
				
				/*
				return response()->json(['success' => 'Record '.$resMsg.' Successfully.', 'new_obj'=>$remuneration_extra]);
				*/
				if(!(($affectedRows==1) && $featRevised)){
					throw new \Exception('Profile advance payment details cannot be updated.');
				}else{
					return response()->json(['success' => 'Record '.$resMsg.' Successfully.', 'new_obj'=>$remuneration_extra]);
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RemunerationProfile  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function show(RemunerationProfile $remuneration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RemunerationProfile  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = RemunerationProfile::findOrFail($id);
            return response()->json(['pre_obj' => $data]);
        }
    }
	
	public function reviewExtras($id)
    {
        if(request()->ajax())
        {
            $data = PayrollProfileExtra::findOrFail($id);
            return response()->json(['pre_obj' => $data]);
        }
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RemunerationProfile  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RemunerationProfile $remuneration)
    {
        $user = Auth::user();
        $permission = $user->can('Payrollprofile-edit');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to update payroll profile remunerations.')]);
        }

        $rules = array(
            'new_eligible_amount' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $value_group = (($request->remuneration_type=='Addition')?1:-1);
		
		$form_data = array(
            'new_eligible_amount' =>  $request->new_eligible_amount,
			'remuneration_signout' => 0,
			'updated_by' => $request->user()->id
            
        );

        RemunerationProfile::whereId($request->subscription_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated', 'alt_obj'=>$form_data]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RemunerationProfile  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('Payrollprofile-delete');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to remove payroll profile remunerations.')]);
        }
        /*
		$data = RemunerationProfile::findOrFail($id);
        $data->delete();
		*/
		
		$form_data = array('updated_by'=>auth()->user()->id, 'remuneration_signout'=>1);
		
		$affectedRows=RemunerationProfile::where(['id'=>$id, 'remuneration_signout'=>0])->update($form_data);
		
		$result = array('result'=>(($affectedRows==1)?'success':'error'));
		
		return response()->json($result);
    }
	
	
	/*
	
	*/
	public function checkoutExtras($id)
    {
		try{
			return DB::transaction(function() use ($id){
				$extrasFeatPayment = PayrollProfileExtra::find($id);
				$employeePayProfile = PayrollProfile::find($extrasFeatPayment->payroll_profile_id);
				$paymentPeriod=PaymentPeriod::where(['payroll_process_type_id'=>$employeePayProfile->payroll_process_type_id,
													 'employee_payday_id'=>$employeePayProfile->employee_payday_id])
									->latest()
									->first();
				
				$payment_period_id = empty($paymentPeriod)?0:$paymentPeriod->id;
				$sql_adv = "SELECT drv_term.emp_term_pay_id, drv_ext.id, drv_term.payment_amount, drv_term.payment_cancel FROM (select id as emp_term_pay_id, payment_amount, payment_cancel from employee_term_payments where payroll_profile_id=? and remuneration_id=?) AS drv_term ";
				/*$sql_adv .= "LEFT OUTER JOIN (select max(id*(`remuneration_extra_id`=?)) as id, employee_term_payment_id from employee_term_payment_extras where payment_period_id=? group by employee_term_payment_id) AS drv_ext ON drv_term.emp_term_pay_id=drv_ext.employee_term_payment_id";*/
				$sql_adv .= "LEFT OUTER JOIN (select max(id*(`remuneration_extra_id`=?)) as id, employee_term_payment_id, payment_period_id from employee_term_payment_extras group by employee_term_payment_id) AS drv_ext ON drv_term.emp_term_pay_id=drv_ext.employee_term_payment_id where payment_period_id=?";
				$rows_adv = DB::select($sql_adv, [$extrasFeatPayment->payroll_profile_id, $extrasFeatPayment->remuneration_id, $extrasFeatPayment->remuneration_extra_id, $payment_period_id]);
				
				$adv_term_payment_id = 0;
				$adv_term_payment_amount = 0;
				
				//only diff considered as 
				//removed $extrasFeatPayment->extra_entitle_amount
				$adv_term_diff = $extrasFeatPayment->extra_entitle_amount;//0;
				
				$extraPaymentData = RemunerationExtra::find($extrasFeatPayment->remuneration_extra_id);
				$extra_value_group = $extraPaymentData->value_group;
				
				if(count($rows_adv)>0){
					if($rows_adv[0]->id=='0'){
						$advancePaymentDate = Carbon::parse($paymentPeriod->advance_payment_date);
						$revDate = Carbon::today();
						//if($advancePaymentDate->isAfter($revDate))
						if($advancePaymentDate->gte($revDate)){
							if($rows_adv[0]->payment_cancel=='0'){
								return response()->json(array('result'=>'error'));
							}else{
								$adv_term_payment_id = $rows_adv[0]->emp_term_pay_id;
								$adv_term_payment_amount = $rows_adv[0]->payment_amount;
							}
						}
					}
				}
				/*
				$form_data = array('updated_by'=>auth()->user()->id, 'payroll_profile_extra_signout'=>1);
				$affectedRows=PayrollProfileExtra::where(['id'=>$id, 'payroll_profile_extra_signout'=>0])->update($form_data);
				*/
				$extrasFeatPayment->payroll_profile_extra_signout = 1;
				$extrasFeatPayment->updated_by = auth()->user()->id;
				$featRemoved = $extrasFeatPayment->save();
				
				$affectedRows = 1;
				
				if($adv_term_payment_id>0){
					$adv_term_payment_amount += ($adv_term_diff*$extra_value_group*-1);
					$form_data = array('updated_by'=>auth()->user()->id, 'payment_amount'=>$adv_term_payment_amount);
					$affectedRows = EmployeeTermPayment::where(['id'=>$adv_term_payment_id, 'payment_cancel'=>1])->update($form_data);
				}
				/*
				$result = array('result'=>(($affectedRows==1)?'success':'error'));
				*/
				if(!(($affectedRows==1) && $featRemoved)){
					throw new \Exception('Payment details cannot be removed.');
				}else{
					$result = array('result'=>'success');
					return response()->json($result);
				}
			});
		}catch(\Exception $e){
			return response()->json(array('result'=>'error', 'msg'=>$e->getMessage()));
		}
	}
}
