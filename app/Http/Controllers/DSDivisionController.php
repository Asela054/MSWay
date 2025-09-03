<?php

namespace App\Http\Controllers;

use App\Commen;
use App\DSDivision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;

class DSDivisionController extends Controller
{
    public function index(){
    
        return view('Employeermasterfiles.dsdivision');
     }

      public function insert(Request $request){

        $user = Auth::user();

        $division = new DSDivision();
        $division->ds_division = $request->input('dsdivision');
        $division->status = '1';
        $division->save();
        return response()->json(['success' => 'DS Division is successfully Inserted']);
      }


      public function edit(Request $request){

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('employee_ds_division')
        ->select('employee_ds_division.*')
        ->where('employee_ds_division.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
    }

    }


    public function update(Request $request){
            $current_date_time = Carbon::now()->toDateTimeString();

        $id =  $request->hidden_id ;
        $form_data = array(
                'ds_division' => $request->dsdivision,
                'updated_at' => $current_date_time,
            );

            DSDivision::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'DS Division is Successfully Updated']);
    }

    public function delete(Request $request){

            $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'updated_at' => $current_date_time,
        );
        DSDivision::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'DS Division is successfully Deleted']);

    }

    public function status($id,$statusid){
        
        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
            );
            DSDivision::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('dsdivision');
        } else{
            $form_data = array(
                'status' =>  '2',
            );
            DSDivision::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('dsdivision');
        }

    }

}
