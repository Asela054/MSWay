<?php

namespace App\Http\Controllers;

use App\Commen;
use App\DSDivision;
use App\GNSDivision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Policestation;
use Auth;
use Carbon\Carbon;
use DB;

class PolicestationController extends Controller
{
    public function index(){
        return view('Employeermasterfiles.policestation');
     }

      public function insert(Request $request)
      {
        $user = Auth::user();

        $division = new Policestation();
        $division->police_station = $request->input('poiceststion');
        $division->status = '1';
        $division->save();
        return response()->json(['success' => 'Police Station is successfully Inserted']);
      }


      public function edit(Request $request){
    
        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('employee_police_station')
        ->select('employee_police_station.*')
        ->where('employee_police_station.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
    }

    }


    public function update(Request $request)
    {

            $current_date_time = Carbon::now()->toDateTimeString();

        $id =  $request->hidden_id ;
        $form_data = array(
                'police_station' => $request->poiceststion,
                'updated_at' => $current_date_time,
            );

            Policestation::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'Police Station is Successfully Updated']);
    }

    public function delete(Request $request)
    {
            $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'updated_at' => $current_date_time,
        );
        Policestation::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'DS Division is successfully Deleted']);

    }



    public function status($id,$statusid)
    {
        
        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
            );
            Policestation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('policestation');
        } else{
            $form_data = array(
                'status' =>  '2',
            );
            Policestation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('policestation');
        }

    }
}
