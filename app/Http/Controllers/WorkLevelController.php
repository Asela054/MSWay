<?php

namespace App\Http\Controllers;

use App\WorkLevel;
use Illuminate\Http\Request;
use Validator;

class WorkLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
    
        $worklevel= WorkLevel::orderBy('id', 'asc')->get();
        return view('job.worklevel',compact('worklevel'));
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
            'level'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'level'        =>  $request->level
            
        );

        $workLevel=new WorkLevel;
       $workLevel->level=$request->input('level');       
       $workLevel->save();

       

        return response()->json(['success' => 'Work Level Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WorkLevel  $workLevel
     * @return \Illuminate\Http\Response
     */
    public function show(WorkLevel $workLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WorkLevel  $workLevel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = WorkLevel::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WorkLevel  $workLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkLevel $workLevel)
    {
        $rules = array(
            'level'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'level'    =>  $request->level
            
        );

        WorkLevel::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Work Level  is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WorkLevel  $workLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = WorkLevel::findOrFail($id);
        $data->delete();
    }
}
