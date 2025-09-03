<?php

namespace App\Http\Controllers;

use App\EmployeeSalaryRemark;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class EmployeeSalaryRemarkController extends Controller
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
		$salaryRemark = EmployeeSalaryRemark::where('remuneration_cancel', 0)->orderBy('id', 'asc')->get();
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
        $rules = array(
			'employee_payslip_id' => 'required',
            'payslip_remarks' => 'required',
			'payslip_remarks_file' => 'image|mimes:jpeg,png,jpg|max:2048',
			'release_remarks_file' => 'image|mimes:jpeg,png,jpg|max:2048'
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
		
		
		$salaryRemark=NULL;
		$resMsg = 'Added';
		
		try{
			$salaryRemark=EmployeeSalaryRemark::where(['employee_payslip_id'=>$request->input('employee_payslip_id')])
											->firstOrFail();
			
			$salaryRemark->updated_by=$request->user()->id;
			$resMsg='Updated';
			
		}catch (ModelNotFoundException $e) {
			$salaryRemark=new EmployeeSalaryRemark;
			$salaryRemark->employee_payslip_id=$request->input('employee_payslip_id'); 
			
			$salaryRemark->created_by=$request->user()->id;
		}
        
		$salaryRemark->payslip_remarks=$request->input('payslip_remarks'); 
		$salaryRemark->release_remarks=$request->input('release_remarks'); 
		
		$file_comm_id = $request->input('employee_payslip_id');
		
        if ($request->hasFile('payslip_remarks_file')) {
			$image_remarks = $request->file('payslip_remarks_file');
			$remarks_file_ext = $image_remarks->getClientOriginalExtension();
			$remarks_file_name = md5($file_comm_id.'_HL') . '.' . $remarks_file_ext;
			$image_remarks->move(public_path('regdocs'), $remarks_file_name);
			
			$salaryRemark->payslip_remarks_file = $remarks_file_ext;
		}
		
		if ($request->hasFile('release_remarks_file')) {
			$image_remarks = $request->file('release_remarks_file');
			$remarks_file_ext = $image_remarks->getClientOriginalExtension();
			$remarks_file_name = md5($file_comm_id.'_AL') . '.' . $remarks_file_ext;
			$image_remarks->move(public_path('regdocs'), $remarks_file_name);
			
			$salaryRemark->release_remarks_file = $remarks_file_ext;
		}
		
		
		$salaryRemark->save();

		

        return response()->json(['success' => 'Remarks '.$resMsg.' Successfully.', 'new_obj'=>$salaryRemark]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeSalaryRemark  $salaryRemark
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeSalaryRemark $salaryRemark)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeSalaryRemark  $salaryRemark
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $pre_obj = array('payslip_remarks'=>'', 'release_remarks'=>'', 
						'payslip_remarks_file'=>'', 'release_remarks_file'=>'');
			
			try{
				$data = EmployeeSalaryRemark::where(['employee_payslip_id'=>$id])->firstOrFail();
				$pre_obj['payslip_remarks']=$data->payslip_remarks;
				$pre_obj['release_remarks']=$data->release_remarks;
				
				//$img_path = public_path('regdocs');//public-path-reveals-absolute-file-path-of-server
				
				if($data->payslip_remarks_file!=''){
					$pre_obj['payslip_remarks_file']=url('/public/regdocs/'.md5($id.'_HL').'.'.$data->payslip_remarks_file);
				}
				
				if($data->release_remarks_file!=''){
					$pre_obj['release_remarks_file']=url('/public/regdocs/'.md5($id.'_AL').'.'.$data->release_remarks_file);
				}
				
				return response()->json(['pre_obj' => $pre_obj]);//(['pre_obj' => $data]);
				
			}catch(ModelNotFoundException $e){
				return response()->json(['pre_obj' => $pre_obj]);
			}
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeSalaryRemark  $salaryRemark
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeSalaryRemark $salaryRemark)
    {
		
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeSalaryRemark  $salaryRemark
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		
	}
	
	
	/*
	
	*/
}
