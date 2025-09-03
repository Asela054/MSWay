<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Joblocation;
use Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;


class JoblocationController extends Controller
{
    public function index()
    {
        return view('jobmanagement.joblocation');
    }

    public function insert(Request $request)
    {
        $permission = \Auth::user()->can('Job-Location-create');
        if (!$permission) {
            abort(403);
        }

        $locationname=$request->input('locationname');
        $contactno=$request->input('contactno');
        $address=$request->input('address');
        $altitude=$request->input('altitude');
        $longitude=$request->input('longitude');
        $action=$request->input('action');
        $hidden_id=$request->input('hidden_id');

        if( $action == 1 ){
            $location = new Joblocation();
            $location->location_name=$locationname;
            $location->contactno=$contactno;
            $location->location_address=$address;
            $location->altitude=$altitude;
            $location->longitude=$longitude;
            $location->status= '1';
            $location->jobcomplete_status = '0';
            $location->created_by=Auth::id();
            $location->updated_by = '0';
            $location->save();
    
            return response()->json(['success' => 'Job Location Added successfully.']);
        }else{

            $data = array(
                'location_name' => $locationname,
                'contactno' => $contactno,
                'location_address' => $address,
                'altitude' => $altitude,
                'longitude' => $longitude,
                'updated_by' => Auth::id(),
            );

            Joblocation::where('id', $hidden_id)
            ->update($data);
            return response()->json(['success' => 'Job Location Updated successfully.']);
        }
       
    }

    public function locationlist(){
     
        $locations = DB::table('job_location')
        ->select('job_location.*')
        ->whereIn('job_location.status', [1, 2])
        ->get();
        return Datatables::of($locations)
        ->addIndexColumn()
        ->addColumn('jobcomplete_status', function ($row) {
            $label = '';
            if ($row->jobcomplete_status == 0) {
                $label.= '<span class="text-danger">Not Completed</span>';
            } else if($row->jobcomplete_status == 1) 
            {
                $label .= '<span class="text-success">Completed</span>';
            }
            return $label;
        })
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Job-Location-edit')){
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('Job-Location-status')){
                        if($row->status == 1){
                            $btn .= ' <a href="'.route('joblocationsstatus', ['id' => $row->id, 'status' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                        }else{
                            $btn .= '&nbsp;<a href="'.route('joblocationsstatus', ['id' => $row->id, 'status' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                        }
                    }
                    if(Auth::user()->can('Job-Location-delete')){
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
            return $btn;
        })
        ->rawColumns(['action','jobcomplete_status'])
        ->make(true);
    }

    public function edit(Request $request)
    {
        $permission = \Auth::user()->can('Job-Location-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('job_location')
        ->select('job_location.*')
        ->where('job_location.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }

    public function status($id,$statusid){
        $permission = \Auth::user()->can('Job-Location-status');
        if (!$permission) {
            abort(403);
        }

        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
                'updated_by' => Auth::id()
            );
            Joblocation::where('id',$id)
            ->update($form_data);

            return redirect()->route('joblocations');
        } else{
            $form_data = array(
                'status' =>  '2',
                'updated_by' => Auth::id()
            );
            Joblocation::where('id',$id)
            ->update($form_data);

            return redirect()->route('joblocations');
        }
    }

    public function delete(Request $request)
    {
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Joblocation::where('id',$id)
        ->update($form_data);

        return response()->json(['success' => 'Job Location is Successfully Deleted']);
    }
}
