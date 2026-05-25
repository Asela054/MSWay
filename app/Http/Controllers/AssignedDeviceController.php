<?php

namespace App\Http\Controllers;

use App\AssignedDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AssignedDeviceController extends Controller
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
        $user = Auth::user();
        $permission = $user->can('assigned-device-list');
        if (!$permission) {
            abort(403);
        }

        $assigneddevices = AssignedDevice::orderBy('id', 'asc')->get();
        return view('Employeermasterfiles.assignedDevices', compact('assigneddevices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('assigned-device-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'device_name' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $assigneddevice = AssignedDevice::create([
            'device_name' => $request->device_name,
            'remarks' => $request->remarks
        ]);

        if ($assigneddevice) {
            return response()->json(['success' => 'Data Added successfully.']);
        }
        
        return response()->json(['errors' => ['Failed to save data']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\AssignedDevice $device
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('assigned-device-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = AssignedDevice::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\AssignedDevice $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssignedDevice $device)
    {
        $user = Auth::user();
        $permission = $user->can('assigned-device-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'device_name' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'device_name' => $request->device_name,
            'remarks' => $request->remarks
        );

        AssignedDevice::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\AssignedDevice $device
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('assigned-device-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = AssignedDevice::findOrFail($id);
        $data->delete();
    }
}
