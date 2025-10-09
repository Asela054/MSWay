<?php

namespace App\Http\Controllers;

use App\Functionalkpi;
use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class FunctionalkpiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Functional-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        
        $functionaltypes = DB::table('functionaltypes')->select('functionaltypes.*')
        ->whereIn('functionaltypes.status', [1, 2])
        ->where('functionaltypes.status', 1)
        ->get();

        return view('KPImanagement.functionalkpi',compact('functionaltypes'));
    }
    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $functionalkpi = new Functionalkpi();
        $functionalkpi->type_id = $request->input('type');
        $functionalkpi->kpi = $request->input('kpi');
        $functionalkpi->status = '1';
        $functionalkpi->created_by = Auth::id();
        $functionalkpi->updated_by = '0';
        $functionalkpi->created_at = Carbon::now()->toDateTimeString();
        $functionalkpi->save();
        return response()->json(['success' => 'Functonal KRA is Successfully Inserted']);
    }

    public function requestlist()
    {
        $types = DB::table('functionalkpis')
            ->leftjoin('functionaltypes', 'functionalkpis.type_id', '=', 'functionaltypes.id')
            ->select('functionalkpis.*','functionaltypes.type AS type')
            ->whereIn('functionalkpis.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();
        $permission = $user->can('Functional-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 


                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('functionalkpistatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('functionalkpistatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            // }

                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';

                            $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
              
                return $btn;
            })
           
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('functionalkpis')
        ->select('functionalkpis.*')
        ->where('functionalkpis.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
    }
    }




    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
       
            $current_date_time = Carbon::now()->toDateTimeString();

        $id =  $request->hidden_id ;
        $form_data = array(
                'kpi' => $request->kpi,
                'type_id' => $request->type,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            Functionalkpi::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'Functional KPI is Successfully Updated']);
    }




    public function delete(Request $request){

        $user = Auth::user();
        $permission = $user->can('Functional-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
            $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        Functionalkpi::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Functional KPI is Successfully Deleted']);

    }

    // public function status($id,$statusid){

    //     $user = Auth::user();
    //     $permission = $user->can('Functional-status');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     } 

    //     if($statusid == 1){
    //         $form_data = array(
    //             'status' =>  '1',
    //             'updated_by' => Auth::id(),
    //         );
    //         Functionalkpi::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalkpi');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Functionalkpi::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('functionalkpi');
    //     }

    // }
}
