<?php

namespace App\Http\Controllers;

use App\FingerprintDevice;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

class FingerprintDeviceController extends Controller
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
        $permission = Auth::user()->can('finger-print-device-list');
        if (!$permission) {
            abort(403);
        }
        $location= Branch::orderBy('id', 'asc')->get();
       // $device= FingerprintDevice::orderBy('id', 'asc')->get();
        $device = DB::table('fingerprint_devices')
                    ->leftjoin('branches', 'fingerprint_devices.location', '=', 'branches.id')                    
                    ->select('fingerprint_devices.*', 'branches.location')
                    ->get();
        return view('FingerprintDevice.fingerprintdevice',compact('device','location'));
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
        $permission = Auth::user()->can('finger-print-device-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'ip'    =>  'required',
            'name'    =>  'required',
            'sno'    =>  'required',
            'emi'    =>  'required',
            'location'    =>  'required',
            'connectionno'    =>  'required',           
            'status'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'ip'        =>  $request->ip,
            'name'        =>  $request->name,            
            'sno'        =>  $request->sno,
            'emi'        =>  $request->emi,
            'connectionno'        =>  $request->connectionno,
            'location'        =>  $request->location,
            'status'        =>  $request->status
            
        );

        $device=new FingerprintDevice;
       $device->ip=$request->input('ip');       
       $device->name=$request->input('name');               
       $device->sno=$request->input('sno');            
       $device->emi=$request->input('emi');            
       $device->conection_no=$request->input('connectionno');            
       $device->location=$request->input('location'); 
       $device->status=$request->input('status');       
       $device->save();

       

        return response()->json(['success' => 'Data Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FingerprintDevice  $fingerprintDevice
     * @return \Illuminate\Http\Response
     */
    public function show(FingerprintDevice $fingerprintDevice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FingerprintDevice  $fingerprintDevice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Auth::user()->can('finger-print-device-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = FingerprintDevice::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FingerprintDevice  $fingerprintDevice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FingerprintDevice $fingerprintDevice)
    {
        $permission = Auth::user()->can('finger-print-device-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'ip'    =>  'required',
            'name'    =>  'required',
            'sno'    =>  'required',
            'emi'    =>  'required',
            'location'    =>  'required',
            'connectionno'    =>  'required',      
            'status'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'ip'    =>  $request->ip,
            'name'    =>  $request->name,
            'sno'    =>  $request->sno,
            'emi'    =>  $request->emi,
            'conection_no'    =>  $request->connectionno,
            'location'    =>  $request->location,
            'status'    =>  $request->status
            
        );

        FingerprintDevice::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FingerprintDevice  $fingerprintDevice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Auth::user()->can('finger-print-device-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $data = FingerprintDevice::findOrFail($id);
        $data->delete();
    }
}
