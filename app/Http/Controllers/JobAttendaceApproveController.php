<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Joballocation;
use App\Jobattendance;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class JobAttendaceApproveController extends Controller
{
     public function index()
    {
        $locations=DB::table('branches')->select('*')->get();
        return view('jobmanagement.locationattendace_approve',compact('locations'));
    }

     public function unauthorizeattendace()
    {
        $locations=DB::table('branches')->select('*')->get();
        return view('jobmanagement.unauthorizelocationattendace_approve',compact('locations'));
    }
    public function approveattendace(Request $request){

        $permission = \Auth::user()->can('MealAllowanceApprove-approve');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('records');
        $location = $request->input('location');
        $attendace_type = $request->input('attendace_type');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        $current_date_time = Carbon::now()->toDateTimeString();

         foreach ($dataarry as $row) {
            $id = $row['id'];
            $empid = $row['empid'];
            $emp_name = $row['emp_name'];
            $date = $row['date'];
            $on_time = $row['on_time'];
            $off_time = $row['off_time'];
            $location_id = $row['location_id'];
            $reason = $row['reason'];
            

            $data = array(
                'approve_status' => 1,
                'updated_by' => Auth::id(),
            );
        
            Jobattendance::where('id', $id)
            ->update($data);

            // on time
            $data = array(
                'emp_id' =>  $empid,
                'uid' =>  $empid,
                'state' => 1,
                'timestamp' => $on_time,
                'date' => $date,
                'approved' => 0,
                'type' => 255,
                'devicesno' => '-',
                'location' => $location_id,
                'created_at' => $current_date_time,
                'updated_at' => $current_date_time
            );
            DB::table('attendances')->insert($data);

            //off time
            $data = array(
                'emp_id' => $empid,
                'uid' => $empid,
                'state' => 1,
                'timestamp' => $off_time,
                'date' => $date,
                'approved' => 0,
                'type' => 255,
                'devicesno' => '-',
                'location' => $location_id,
                'created_at' => $current_date_time,
                'updated_at' => $current_date_time
            );
            DB::table('attendances')->insert($data);
        }

        return response()->json(['success' => 'Location Attendance is successfully Approved']);
    }

}