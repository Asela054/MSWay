<?php

namespace App\Http\Controllers\Production_Module_Opma;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\TimeChange;
use Carbon\Carbon;
use DB;
use Validator;
use Auth;

class TimechangingController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('production-ending-list');
        if (!$permission) {
            abort(403);
        }

       $types = DB::table('opma_timechanging_type')
            ->select('id', 'type')
            ->get();
        $machines = DB::table('opma_machines')
            ->select('id', 'machine')
            ->get();

        return view('Opma_Production.Daily_Production.TimeChanging',compact('types','machines'));
    }

      public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('production-ending-finish');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }


        $timechange=new TimeChange();
        $timechange->date=$request->input('date');
        $timechange->type_id=$request->input('type');
        $timechange->machine_id=$request->input('machine');
        $timechange->fromtime=$request->input('fromtime');  
        $timechange->totime=$request->input('totime');
        $timechange->status=  1;
        $timechange->created_by= Auth::id();
        $timechange->save();

        return response()->json(['success' => 'Downtime Log Added Successfully.']);
    }

     public function edit(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('production-ending-finish');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = $request->input('id');
        if(request()->ajax())
        {
            $data = TimeChange::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

      public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('production-ending-finish');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

          $form_data = array(
            'date'   =>  $request->date,
            'type_id' =>  $request->type,
            'machine_id' =>  $request->machine,
            'fromtime' =>  $request->fromtime,
            'totime' =>  $request->totime
        );

        TimeChange::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Downtime Log Updated Successfully.']);
    }

      public function destroy(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('production-ending-finish');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = $request->input('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        TimeChange::findOrFail($id)->update($form_data);

        return response()->json(['success' => 'Downtime Log is successfully deleted']);
    }
}
