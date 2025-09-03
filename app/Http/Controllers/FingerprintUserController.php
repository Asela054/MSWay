<?php

namespace App\Http\Controllers;

use App\FingerprintUser;
use App\FingerprintDevice;
use App\Branch;
use App\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ZKLib;
use Validator;
use Excel;
use DB;

class FingerprintUserController extends Controller
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
        $permission = Auth::user()->can('finger-print-user-list');
        if (!$permission) {
            abort(403);
        }

        $users = DB::table('fingerprint_users')
            ->leftjoin('branches', 'fingerprint_users.location', '=', 'branches.id')
            ->select('fingerprint_users.*', 'branches.location')
            ->where('deleted', 0)
            ->get();

        $device = DB::table('fingerprint_devices')
            ->leftjoin('branches', 'fingerprint_devices.location', '=', 'branches.id')
            ->select('fingerprint_devices.*', 'branches.location')
            ->get();
        $title = JobTitle::orderBy('id', 'asc')->get();

        return view('FingerprintUser.fingerprintuser', compact('users', 'title', 'device'));
    }

    public function getdeviceuserdata(Request $request)
    {
        $permission = Auth::user()->can('finger-print-user-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $device = FingerprintDevice::orderBy('id', 'asc')->where('status', '=', 1)->get()->toArray();


        $device = FingerprintDevice::where('ip', '=', $request->device)->get();
        $device = DB::table('fingerprint_devices')->where('ip', '=', $request->device)->first();
        $ip = $device->ip;
        $location = $device->location;
        $name = $device->name;
        $name = new ZKLib(
            $ip // '112.135.69.27' //your device IP
        );
        $ret = $name->connect();
        if ($ret) {
            $name->disableDevice();

            $attendance = $name->getuser();
            $serial = $name->serialNumber();
            $deviceserial = substr($serial, strpos($serial, "=") + 1, -1);
            $name->enableDevice();
            $name->disconnect();
            //  dd($attendance);
        } else {
            return response()->json(['errors' => ' Device Doesnt  Contact, Please Check Connection']);
        }
        foreach ($attendance as $link) {

            $users = FingerprintUser::firstOrNew(['userid' => $link['userid'], 'devicesno' => $deviceserial]);
            $users->userid = $id = $link['userid'];
            $users->name = $id = $link['name'];
            $users->cardno = $id = $link['cardno'];
            $users->userid = $id = $link['userid'];
            $users->role = $id = $link['role'];
            $users->password = $id = $link['password'];
            $users->devicesno = $deviceserial;
            $users->location = $location;
            $users->save();


        }

        return response()->json(['success' => 'FigerPrint Users Added successfully.']);


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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = Auth::user()->can('finger-print-user-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(

            'userid' => 'required',
            'name' => 'required'


        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'id' => $request->id,
            'userid' => $request->userid,
            'name' => $request->name,
            'cardno' => $request->cardno,
            'role' => $request->role,
            'password' => $request->password

        );

        /*  $fpuser=new FingerprintUser;
         $fpuser->uid=$request->input('ip');
         $fpuser->userid=$request->input('userid');
         $fpuser->name=$request->input('name');
         $fpuser->cardno=$request->input('cardno');
         $fpuser->role=$request->input('role');
         $fpuser->password=$request->input('password');
         $fpuser->save();

         */

        $devices = $request->devices;
        //dd( $request->userid);
        $uid = $request->id;
        $userid = $request->input('userid');
        $name = $request->input('name');
        $cardno = $request->input('cardno');
        $role = $request->input('role');
        $password = $request->input('password');

        $enableGetDeviceInfo = true;
        $enableGetUsers = true;
        $enableGetData = true;
        $zk = new ZKLib(
            $devices // '112.135.69.27' //your device IP
        );


        $ret = $zk->connect();
        if ($ret) {
            $zk->disableDevice();
            /*
            $zk->setTime(date('Y-m-d H:i:s')); // Synchronize time
            */
            //dd($uid);
            $zk->setUser($uid, $userid, $name, $password, $role, $cardno);

            $zk->enableDevice();
            $zk->disconnect();
        }

        return response()->json(['success' => 'FigerPrint Users Added Successfully.']);
        //  return (new ZK\User())->set($this, $uid, $userid, $name, $password, $role, $cardno);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\FingerprintUser $fingerprintUser
     * @return \Illuminate\Http\Response
     */
    public function show(FingerprintUser $fingerprintUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\FingerprintUser $fingerprintUser
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Auth::user()->can('finger-print-user-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = FingerprintUser::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\FingerprintUser $fingerprintUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FingerprintUser $fingerprintUser)
    {
        $permission = Auth::user()->can('finger-print-user-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'id' => 'required',
            'userid' => 'required',
            'name' => 'required',
            'cardno' => 'required',
            'role' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'id' => $request->id,
            'userid' => $request->userid,
            'name' => $request->name,
            'cardno' => $request->cardno,
            'role' => $request->role,
            'password' => $request->password

        );

        FingerprintUser::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\FingerprintUser $fingerprintUser
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Auth::user()->can('finger-print-user-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        DB::table('fingerprint_users')
            ->where('userid', $id)
            ->update(['deleted' => 1]);

        $enableGetDeviceInfo = true;
        $enableGetUsers = true;
        $enableGetData = true;
        /*$zk = new ZKLib(
             $devices '112.135.69.27' //your device IP

        );*/
        $zk = new ZKLib("112.135.69.27", 4370);


        $ret = $zk->connect();
        if ($ret) {
            $zk->disableDevice();
            /*
            $zk->setTime(date('Y-m-d H:i:s')); // Synchronize time
            */
            // dd($id);
            $zk->deleteUser($id);

            $zk->enableDevice();
            $zk->disconnect();
        }
    }

    public function exportfpuser()
    {
        $permission = Auth::user()->can('finger-print-user-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $fp_data = FingerprintUser::orderBy('id', 'asc')->get();
//dd($fp_data);
        $fp_array[] = array('Employee Id', 'Name', 'Card No', 'Location', 'Device');
        foreach ($fp_data as $fp_datas) {
            $fp_array[] = array(
                'Employee Id' => $fp_datas->userid,
                'Name' => $fp_datas->name,
                'Card No' => $fp_datas->cardno,
                'Location' => $fp_datas->cardno,
                'Device' => $fp_datas->devicesno

            );
        }
        Excel::create('Fingerprint User Data', function ($excel) use ($fp_array) {
            $excel->setTitle('Fingerprint User  Data');
            $excel->sheet('Fingerprint User  Data', function ($sheet) use ($fp_array) {
                $sheet->fromArray($fp_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
