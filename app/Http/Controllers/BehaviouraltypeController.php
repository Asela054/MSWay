<?php

namespace App\Http\Controllers;

use App\Behaviouraltype;
use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class BehaviouraltypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        
        return view('KPImanagement.behaviouraltype');
    }
    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $behaviouraltype = new Behaviouraltype();
        $behaviouraltype->type = $request->input('type');
        $behaviouraltype->description = $request->input('description');
        $behaviouraltype->status = '1';
        $behaviouraltype->created_by = Auth::id();
        $behaviouraltype->updated_by = '0';
        $behaviouraltype->save();
        return response()->json(['success' => 'Behavioural Atribute is Successfully Inserted']);
    }

    public function requestlist()
    {
        $types = DB::table('behaviouraltypes')
            ->select('behaviouraltypes.*')
            ->whereIn('behaviouraltypes.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();
                $permission = $user->can('Behavioural-status');
                if (!$permission) {
                    return response()->json(['error' => 'UnAuthorized'], 401);
                } 

                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('behaviouraltypestatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('behaviouraltypestatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
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
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('behaviouraltypes')
        ->select('behaviouraltypes.*')
        ->where('behaviouraltypes.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
    }
    }




    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
       
            $current_date_time = Carbon::now()->toDateTimeString();

        $id =  $request->hidden_id ;
        $form_data = array(
                'type' => $request->type,
                'description' => $request->description,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            Behaviouraltype::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'Behavioural Atribute is Successfully Updated']);
    }




    public function delete(Request $request){
        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
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
        Behaviouraltype::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Behavioural Atribute is Successfully Deleted']);

    }




    // public function status($id,$statusid){
    //     $user = Auth::user();
    //     $permission = $user->can('Behavioural-status');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     } 

    //     if($statusid == 1){
    //         $form_data = array(
    //             'status' =>  '1',
    //             'updated_by' => Auth::id(),
    //         );
    //         Behaviouraltype::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('behaviouraltype');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Behaviouraltype::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('behaviouraltype');
    //     }

    // }

}
