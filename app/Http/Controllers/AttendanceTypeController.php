<?php

namespace App\Http\Controllers;

use App\AttendanceType;
use Illuminate\Http\Request;
use Validator;

class AttendanceTypeController extends Controller
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
        $attendenttypes= AttendanceType::orderBy('id', 'asc')->get();
        return view('Attendent.attendancetype',compact('attendenttypes'));
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
            'attendenttype'    =>  'required',
            'attendentvalue'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'attendenttype'        =>  $request->attendenttype,
            'attendentvalue'        =>  $request->attendentvalue
            
        );

        $attendenttype=new AttendanceType;
       $attendenttype->attendent_type=$request->input('attendenttype');       
       $attendenttype->attendent_value=$request->input('attendentvalue');       
       $attendenttype->save();

       

        return response()->json(['success' => 'Attendance Type Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AttendanceType  $attendanceType
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceType $attendanceType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AttendanceType  $attendanceType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = AttendanceType::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AttendanceType  $attendanceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttendanceType $attendanceType)
    {
        $rules = array(
            'attendenttype'    =>  'required',
            'attendentvalue'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'attendent_type'    =>  $request->attendenttype,
            'attendent_value'    =>  $request->attendentvalue
            
        );

        AttendanceType::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Attendance Type successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AttendanceType  $attendanceType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceType $attendanceType)
    {
        //
    }
}
