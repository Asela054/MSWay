<?php

namespace App\Http\Controllers;


use App\RemunerationEligibilityDay;
/*
use App\PayrollProcessType;
use App\PayrollProfile;
use App\Remuneration;
*/
use DB;
use Illuminate\Http\Request;
use Validator;

class RemunerationEligibilityDayController extends Controller
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
     * @param  \App\RemunerationEligibilityDay  $info
     * @return \Illuminate\Http\Response
     */
    public function show(RemunerationEligibilityDay $info)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RemunerationEligibilityDay  $info
     * @return \Illuminate\Http\Response
     */
	public function edit($id){
		
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RemunerationEligibilityDay  $info
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RemunerationEligibilityDay $info)
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
			'grp_increment' =>  $request->grp_increment,
			'updated_by' => $request->user()->id
            
        );

        RemunerationEligibilityDay::whereId($request->remuneration_criteria)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
		
		/**/
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RemunerationEligibilityDay  $info
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
		
	}
	
	
	/*
	
	*/
	

}
