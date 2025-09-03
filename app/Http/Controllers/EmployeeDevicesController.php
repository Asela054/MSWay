<?php

namespace App\Http\Controllers;

use App\EmployeeDevices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Session;
use Validator;

class EmployeeDevicesController extends Controller
{
    public function show($id)
    {
        $permission = Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $assigned_devices = EmployeeDevices::where('emp_id',$id)->get();
        return view('Employee.viewAssignedDevices',compact('assigned_devices','id'));
    }

    public function create(Request $request)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, array(
            'emp_id' => 'required',
            'device_type' => 'required|string|max:150',
            'model_number' => 'required|string|max:150',
            'serial_number' => 'required|string|max:150',
            'other_ref_number' => 'nullable|string|max:150',
            'assigned_date' => 'required|date',
            'returned_date' => 'nullable|date|after_or_equal:assigned_date',
        ));

        $ad=new EmployeeDevices;
        $id=$request->input('emp_id'); ;
        $ad->emp_id=$request->input('emp_id');
        $ad->device_type=$request->input('device_type');
        $ad->model_number=$request->input('model_number');
        $ad->serial_number=$request->input('serial_number');
        $ad->other_ref_number=$request->input('other_ref_number');
        $ad->assigned_date=$request->input('assigned_date');
        $ad->returned_date=$request->input('returned_date');
        $ad->status = 1; 
        $ad->save();

        Session::flash('success','The Assigned Device Details Successfully Saved');
        return redirect('viewAssignedDevices/'.$id);
    }

    public function edit_json($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = EmployeeDevices::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, EmployeeDevices $emergencyContact)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'ad_id' => 'required',
            'device_type' => 'required|string|max:150',
            'model_number' => 'required|string|max:150',
            'serial_number' => 'required|string|max:150',
            'other_ref_number' => 'nullable|string|max:150',
            'assigned_date' => 'required|date',
            'returned_date' => 'nullable|date|after_or_equal:assigned_date',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'device_type' => $request->device_type,
            'model_number' => $request->model_number,
            'serial_number' => $request->serial_number,
            'other_ref_number' => $request->other_ref_number,
            'assigned_date' => $request->assigned_date,
            'returned_date' => $request->returned_date
        );

        EmployeeDevices::whereId($request->ad_id)->update($form_data);

        return response()->json(['success' => 'The Assigned Device Details updated successfully']);
    }
    
    public function updateStatus(Request $request)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $device = EmployeeDevices::findOrFail($request->id);
        $device->status = ($device->status == 1) ? 2 : 1;
        $device->save();

        return response()->json([
            'success' => true,
            'new_status' => $device->status
        ]);
    }

    public function destroy($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = EmployeeDevices::findOrFail($id);
        $data->delete();
    }
}
