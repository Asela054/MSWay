<?php

namespace App\Http\Controllers;

use App\OtherFacility;

use DB;

use Illuminate\Http\Request;
use Validator;

class OtherFacilityController extends Controller
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
    public function store(Request $request)
    {
		$data = $request->all();
		
		$rules = array(
            'facility_name' => 'required'
        );

        
		$error = Validator::make($data, $rules);

        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }
		
		$remuneration=new OtherFacility;
        $remuneration->facility_name=$request->input('facility_name'); 
		$remuneration->created_by=$request->user()->id;
        $remuneration->save();
		
		return response()->json(['success' => 'Facility Added Successfully.', 'new_obj'=>$remuneration]);
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\OtherFacility  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function show(OtherFacility $remuneration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OtherFacility  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = OtherFacility::findOrFail($id);
            return response()->json(['pre_obj' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OtherFacility  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtherFacility $remuneration)
    {
		
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OtherFacility  $remuneration
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		
	}
	
	
	/*
	
	*/
}
