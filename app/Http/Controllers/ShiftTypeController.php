<?php

namespace App\Http\Controllers;

use App\ShiftType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ShiftTypeController extends Controller
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
        $shifttype= ShiftType::orderBy('id', 'asc')->where('deleted', 0)->get();
        return view('Shift.shifttype',compact('shifttype'));
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
            'shiftname'    =>  '',
            'ondutytime'    =>  '',
            'offdutytime'    =>  '',
            'saturday_ondutytime'    =>  '',
            'saturday_offdutytime'    =>  '',
            'latetime'    =>  '',            
            'leaveearlytime'    =>  '',         
            'beginingcheckin'    =>  '',          
            'beginingcheckout'    =>  '',          
            'endingcheckin'    =>  '',        
            'endingcheckout'    =>  '',        
            'workdayscount'    =>  '',
            'minutecount'    =>  '',        
            'offduty_day'    =>  '',         
            'color'    =>  '',       
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'shift_name'        =>  $request->shiftname,
            'onduty_time'        =>  $request->ondutytime,
            'offduty_time'        =>  $request->offdutytime,
            'saturday_onduty_time'        =>  $request->saturday_ondutytime,
            'saturday_offduty_time'        =>  $request->saturday_offdutytime,            
            'late_time'        =>  $request->latetime,            
            'leave_early_time'        =>  $request->leaveearlytime,            
            'begining_checkin'        =>  $request->beginingcheckin,            
            'begining_checkout'        =>  $request->beginingcheckout,            
            'ending_checkin'        =>  $request->endingcheckin,            
            'ending_checkout'        =>  $request->endingcheckout,            
            'workdays_count'        =>  $request->workdayscount,            
            'minute_count'        =>  $request->minutecount,
            'offduty_day'        =>  $request->offduty_day,
            'ot_calculate_type'        =>  $request->ot_calculate_type,
            'ot_calculate_time'        =>  $request->ot_calculate_time,  
            'must_checkin'        =>  $request->mustcheckin,            
            'must_checkout'        =>  $request->mustcheckout,            
            'color'        =>  $request->color
            
        );

       $shifttype=new ShiftType;
       $shifttype->shift_name=$request->input('shiftname');       
       $shifttype->onduty_time=$request->input('ondutytime');       
       $shifttype->offduty_time=$request->input('offdutytime');
       $shifttype->saturday_onduty_time=$request->input('saturday_ondutytime');    
       $shifttype->saturday_offduty_time=$request->input('saturday_offdutytime');               
       $shifttype->late_time=$request->input('latetime');    
       $shifttype->leave_early_time=$request->input('leaveearlytime');    
       $shifttype->begining_checkin=$request->input('beginingcheckin');    
       $shifttype->begining_checkout=$request->input('beginingcheckout');    
       $shifttype->ending_checkin=$request->input('endingcheckin');    
       $shifttype->ending_checkout=$request->input('endingcheckout');    
       $shifttype->workdays_count=$request->input('workdayscount');    
       $shifttype->minute_count=$request->input('minutecount');
       $shifttype->offduty_day=$request->input('offduty_day');   
       $shifttype->ot_calculate_type=$request->input('ot_calculate_type');    
       $shifttype->ot_calculate_time=$request->input('ot_calculate_time'); 
       $shifttype->must_checkin=$request->input('mustcheckin');    
       $shifttype->must_checkout=$request->input('mustcheckout');    
       $shifttype->color=$request->input('color'); 
       $shifttype->save();

       

        return response()->json(['success' => 'Shift Details Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ShiftType  $shiftType
     * @return \Illuminate\Http\Response
     */
    public function show(ShiftType $shiftType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ShiftType  $shiftType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = ShiftType::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ShiftType  $shiftType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShiftType $shiftType)
    {
        $rules = array(
            'shiftname'    =>  'required',
            'ondutytime'    =>  'required',
            'offdutytime'    =>  'required',
            'saturday_ondutytime'    =>  'required',
            'saturday_offdutytime'    =>  'required',
            'latetime'    =>  'required',            
            'leaveearlytime'    =>  'required',         
            'beginingcheckin'    =>  'required',          
            'beginingcheckout'    =>  'required',          
            'endingcheckin'    =>  'required',        
            'endingcheckout'    =>  'required',        
            'workdayscount'    =>  'required',        
            'minutecount'    =>  'required',
            'offduty_day'    =>  '',         
            'color'    =>  'required'          
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'shift_name'        =>  $request->shiftname,
            'onduty_time'        =>  $request->ondutytime,
            'offduty_time'        =>  $request->offdutytime,
            'saturday_onduty_time'        =>  $request->saturday_ondutytime,
            'saturday_offduty_time'        =>  $request->saturday_offdutytime,            
            'late_time'        =>  $request->latetime,            
            'leave_early_time'        =>  $request->leaveearlytime,            
            'begining_checkin'        =>  $request->beginingcheckin,            
            'begining_checkout'        =>  $request->beginingcheckout,            
            'ending_checkin'        =>  $request->endingcheckin,            
            'ending_checkout'        =>  $request->endingcheckout,            
            'workdays_count'        =>  $request->workdayscount,            
            'minute_count'        =>  $request->minutecount, 
            'offduty_day'        =>  $request->offduty_day, 
            'ot_calculate_type'        =>  $request->ot_calculate_type, 
            'ot_calculate_time'        =>  $request->ot_calculate_time,
            'must_checkin'        =>  $request->mustcheckin, 
            'must_checkout'        =>  $request->mustcheckout,          
            'color'        =>  $request->color  
            
        );

        ShiftType::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Shift Details Successfully Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ShiftType  $shiftType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('shift_types')
        ->where('id', $id)
        ->update(['deleted' => 1]);
    }
}
