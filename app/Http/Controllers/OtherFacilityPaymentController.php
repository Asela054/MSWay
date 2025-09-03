<?php

namespace App\Http\Controllers;

use App\Employee;
use App\OtherFacility;
use App\OtherFacilityPayment;
use App\PayrollProfile;
/*
use App\PayrollProcessType;
use App\PayrollProfile;
use App\Remuneration;
*/
/*
use App\RemunerationProfile;
*/
use Datatables;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class OtherFacilityPaymentController extends Controller
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
        $remuneration = OtherFacility::where(['facility_cancel'=>0])->orderBy('id', 'asc')->get();
		$employee_list=DB::select("SELECT payroll_profiles.id AS payroll_profile_id, employees.emp_name_with_initial AS emp_first_name FROM payroll_profiles INNER JOIN employees ON payroll_profiles.emp_id=employees.id");// WHERE payroll_profiles.employee_executive_level=1
		return view('Payroll.termPayment.facilityPayment_list',compact('remuneration', 'employee_list'));
    }
	
	function getFacilityData(){
		$employee = DB::table('other_facility_payments')
                    ->join('payroll_profiles', 'other_facility_payments.payroll_profile_id', '=', 'payroll_profiles.id')
					->join('employees', 'payroll_profiles.emp_id', '=', 'employees.id')
					->leftjoin('other_facilities', 'other_facility_payments.other_facility_id', '=', 'other_facilities.id')
					->select('employees.emp_name_with_initial', 'other_facility_payments.other_facility_id AS increment_type', "other_facilities.facility_name as increment_desc", 'other_facility_payments.payment_amount AS increment_value', DB::raw("DATE_FORMAT(other_facility_payments.payment_date, '%Y-%m') AS effective_month"), 'other_facility_payments.payment_date AS effective_date', 'payroll_profiles.basic_salary', 'other_facility_payments.id', 'other_facility_payments.payment_approved')
					->where(['other_facility_payments.payment_cancel'=>0])
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
    public function store(Request $request){
		$data = $request->all();
		
		$rules = array(
            'payroll_profile_id' => 'required',
			'other_facility_id' => 'required',
			'payment_date' => 'required',
			'payment_amount' => 'required'
        );

        
		$error = Validator::make($data, $rules);

        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }
		
		$remuneration=NULL;//
		
		try{
			$prev_remuneration = DB::select("select COUNT(*) AS facility_allocation, IFNULL(id, '') AS prev_id from `other_facility_payments` where (`payroll_profile_id` = ? and `other_facility_id` = ? and date_format(`payment_date`, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')) limit 1", [$request->input('payroll_profile_id'), $request->input('other_facility_id'), $request->input('payment_date')]);
			
			if($prev_remuneration[0]->facility_allocation==1){
				$remuneration=OtherFacilityPayment::find($prev_remuneration[0]->prev_id);
				$remuneration->updated_by=$request->user()->id;
			}else{
				throw new ModelNotFoundException();
			}
		}catch(ModelNotFoundException $e){
			$remuneration=new OtherFacilityPayment;
			$remuneration->created_by=$request->user()->id;
		}
		
		
        $remuneration->payroll_profile_id=$request->input('payroll_profile_id');
		$remuneration->other_facility_id=$request->input('other_facility_id');
		$remuneration->payment_date=$request->input('payment_date');
		$remuneration->payment_amount=$request->input('payment_amount');
		
        $remuneration->save();
		
		return response()->json(['success' => 'Facility Added Successfully.', 'new_obj'=>$remuneration]);
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\OtherFacilityPayment  $info
     * @return \Illuminate\Http\Response
     */
    public function show(OtherFacilityPayment $info)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OtherFacilityPayment  $info
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OtherFacilityPayment  $info
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtherFacilityPayment $info)
    {
		
	}
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OtherFacilityPayment  $info
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        /*
		$data = OtherFacilityPayment::findOrFail($id);
        $data->delete();
		*/
		
		$form_data = array('updated_by'=>auth()->user()->id, 'payment_cancel'=>1);
		
		$affectedRows=OtherFacilityPayment::where(['id'=>$id, 'payment_cancel'=>0])->update($form_data);
		
		$result = array('result'=>(($affectedRows==1)?'success':'error'));
		
		return response()->json($result);
    }
	
	
	public function freeze(Request $request){
		if($request->ajax()){
			$affectedRows=0;
			$affectedMode=1;//accept-changes
			
			$payment_id=$request->id;//$request->id;
			$payment_approved=$request->payment_approved;
			
			$form_data = array('updated_by'=>auth()->user()->id, 'payment_approved'=>$payment_approved);
			
			$affectedRows=OtherFacilityPayment::where(['id'=>$payment_id, 'payment_cancel'=>0])->update($form_data);
			
			$result = array('result'=>(($affectedRows==1)?'success':'error'));
			
			return response()->json($result);
		}
	}
	
	/*
	
	*/
	
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
								if($numcols==3){
									$payroll_profile_id = 0;
									$remuneration_id = $request->input('remuneration_file');
									$effective_date = $planData[1];
									$salaryIncrement=NULL;
									
									$act_invalidated=true;
									
									try{
										$employeeInfo = Employee::where(['emp_etfno'=>$planData[0]])
																->firstOrFail();
																
										$payrollInfo = PayrollProfile::where(['emp_id'=>$employeeInfo->id])
																	->firstOrFail();
										
										$payroll_profile_id = $payrollInfo->id;
										
										$incrementFilter = array('other_facility_id'=>$remuneration_id, 
																 'payment_date'=>$effective_date, 
																 'payroll_profile_id'=>$payroll_profile_id);
										
										
										
										$act_invalidated=false; // ready-to-insert-or-update
										
										$salaryIncrement=OtherFacilityPayment::where($incrementFilter)
																		->firstOrFail();
										$salaryIncrement->updated_by=$request->user()->id;
										
									}catch (ModelNotFoundException $e) {
										if($act_invalidated){
											// Data not found. Here, you should make sure that the absence of $data won't break anything
											//$importmsg = 'Something wrong'; // throw new \Exception('----');
										}else{
											$salaryIncrement=new OtherFacilityPayment;
											$salaryIncrement->created_by=$request->user()->id;
										}
									}
									
									if(!empty($salaryIncrement)){
										//
										$salaryIncrement->payroll_profile_id=$payroll_profile_id; 
										$salaryIncrement->other_facility_id=$remuneration_id; 
										$salaryIncrement->payment_date=$effective_date; 
										$salaryIncrement->payment_amount=$planData[2]; 
										$salaryIncrement->payment_cancel=0;
										//
										
										
										$affectedRowCnt = $salaryIncrement->save(); // true-or-false;
										
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

}
