<?php

namespace App\Http\Controllers;

use App\Employee;
use App\PayrollProfile;
use App\Remuneration;
use App\RemunerationProfile;
use App\SalaryIncrement;
/*
use App\PayrollProcessType;
use App\PayrollProfile;
use App\Remuneration;
*/
use Datatables;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class SalaryIncrementController extends Controller
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
		return view('Payroll.salaryIncrement.salaryIncrement_list',compact('remuneration'));
    }
	
	function getIncrementData(){
		$employee = DB::table('salary_increments')
                    ->join('payroll_profiles', 'salary_increments.payroll_profile_id', '=', 'payroll_profiles.id')
					->join('employees', 'payroll_profiles.emp_id', '=', 'employees.id')
					->leftjoin('remunerations', 'salary_increments.remuneration_id', '=', 'remunerations.id')
					->leftjoin('remuneration_profiles', 'salary_increments.remuneration_profile_id', '=', 'remuneration_profiles.id')
                    ->select('employees.emp_name_with_initial', 'salary_increments.remuneration_id AS increment_type', DB::raw("IFNULL(remunerations.remuneration_name, 'Basic Salary') as increment_desc"), 'salary_increments.increment_value', DB::raw("DATE_FORMAT(salary_increments.effective_date, '%Y-%m') AS effective_month"), 'salary_increments.effective_date', DB::raw('IFNULL(remuneration_profiles.new_eligible_amount, payroll_profiles.basic_salary) AS paid_value'), 'salary_increments.id')
					->where(['salary_increments.increment_cancel'=>0])
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
		
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\SalaryIncrement  $info
     * @return \Illuminate\Http\Response
     */
    public function show(SalaryIncrement $info)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SalaryIncrement  $info
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SalaryIncrement  $info
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalaryIncrement $info)
    {
		
	}
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SalaryIncrement  $info
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        /*
		$data = SalaryIncrement::findOrFail($id);
        $data->delete();
		*/
		
		$form_data = array('updated_by'=>auth()->user()->id, 'increment_cancel'=>1);
		
		$affectedRows=SalaryIncrement::where(['id'=>$id, 'increment_cancel'=>0])->update($form_data);
		
		$result = array('result'=>(($affectedRows==1)?'success':'error'));
		
		return response()->json($result);
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
									$remuneration_profile_id = NULL;
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
										
										$incrementFilter = array('effective_date'=>$effective_date);
										
										if($remuneration_id!='0'){
											$remunerationProfile = RemunerationProfile::where(['payroll_profile_id'=>$payroll_profile_id, 
																							   'remuneration_id'=>$remuneration_id])
																->firstOrFail();
																
											$remuneration_profile_id = $remunerationProfile->id;
											
											$incrementFilter['remuneration_profile_id'] = $remuneration_profile_id;
										}else{
											$incrementFilter['remuneration_id'] = $remuneration_id;
											$incrementFilter['payroll_profile_id'] = $payroll_profile_id;
										}
										
										$act_invalidated=false; // ready-to-insert-or-update
										
										$salaryIncrement=SalaryIncrement::where($incrementFilter)
																		->firstOrFail();
										$salaryIncrement->updated_by=$request->user()->id;
										
									}catch (ModelNotFoundException $e) {
										if($act_invalidated){
											// Data not found. Here, you should make sure that the absence of $data won't break anything
											//$importmsg = 'Something wrong'; // throw new \Exception('----');
										}else{
											$salaryIncrement=new SalaryIncrement;
											$salaryIncrement->created_by=$request->user()->id;
										}
									}
									
									if(!empty($salaryIncrement)){
										//
										$salaryIncrement->payroll_profile_id=$payroll_profile_id; 
										$salaryIncrement->remuneration_id=$remuneration_id; 
										$salaryIncrement->remuneration_profile_id=$remuneration_profile_id; 
										$salaryIncrement->effective_date=$effective_date; 
										$salaryIncrement->increment_value=$planData[2]; 
										$salaryIncrement->increment_cancel=0;
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
