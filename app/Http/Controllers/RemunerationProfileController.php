<?php

namespace App\Http\Controllers;

use App\RemunerationProfile;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RemunerationProfile  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RemunerationProfile $remuneration)
    {
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
}
