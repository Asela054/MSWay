<?php

namespace App\Http\Controllers;

use App\Kpiyear;
use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class KpiyearController extends Controller
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

        
        return view('KPImanagement.kpiyear');
    }
    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('Functional-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $kpiyear = new Kpiyear();
        $kpiyear->year = $request->input('year');
        $kpiyear->description = $request->input('description');
        $kpiyear->status = '1';
        $kpiyear->created_by = Auth::id();
        $kpiyear->updated_by = '0';
        $kpiyear->save();
        return response()->json(['success' => 'KPI Year is Successfully Inserted']);
    }

    public function requestlist()
    {
        $types = DB::table('kpiyears')
            ->select('kpiyears.*')
            ->whereIn('kpiyears.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();
                $permission = $user->can('Functional-status');
                if (!$permission) {
                    return response()->json(['error' => 'UnAuthorized'], 401);
                } 

                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('kpiyearstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('kpiyearstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            // }

                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i></button>';

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
        $data = DB::table('kpiyears')
        ->select('kpiyears.*')
        ->where('kpiyears.id', $id)
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
                'year' => $request->year,
                'description' => $request->description,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            Kpiyear::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'KPI Year is Successfully Updated']);
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
        Kpiyear::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'KPI Year is Successfully Deleted']);

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
    //         Kpiyear::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('kpiyear');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Kpiyear::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('kpiyear');
    //     }

    // }

}
