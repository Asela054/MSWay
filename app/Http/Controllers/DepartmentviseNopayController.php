<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Leave;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Datatables;
use Carbon\Carbon;

class DepartmentviseNopayController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Absent-Nopay-list');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.absentnopay');
    }


    public function getabsetnopay(Request $request){
        $permission = Auth::user()->can('Absent-Nopay-list');
        if (!$permission) {
            abort(403);
        }
        
        $department=$request->input('department');
        $firstDate =  $request->input('from_date');

        $datareturn = [];

        $query =  DB::table('employees')
            ->select('emp_name_with_initial as emp_name','id as emp_autoid','emp_department','emp_id as empid')
            ->where('emp_department', '=', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->get();

            foreach ($query as $row) {
                $empId = $row->empid;
                $empName = $row->emp_name;
                $empautoid = $row->emp_autoid;

                $attendance = DB::table('attendances')
                ->select('attendances.*')
                ->where('uid', $empId)
                ->where('date', $firstDate)
                ->whereNull('deleted_at')
                ->first();

                if(!$attendance){

                    $leave = DB::table('leaves')
                    ->select('leaves.*')
                    ->where('emp_id', $empId)
                    ->where('leave_from', '<=', $firstDate)
                    ->where('leave_to', '>=', $firstDate)
                    ->where('status', 'Approved')
                    ->first();

                    if(!$leave){
                        $datareturn[] = [
                            'empid' => $empId,
                            'emp_name' => $empName,
                            'emp_autoid' => $empautoid,
                        ];     
                    }
                }
              
            } 
            return response()->json([ 'data' => $datareturn ]);

    }

    public function applyabsentnopay(Request $request)
    {

        $permission = Auth::user()->can('Absent-Nopay-list');
        if (!$permission) {
            abort(403);
        }


        $dataarry = $request->input('dataarry');
        $leavedate =  $request->input('from_date');

        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $epfno = $row['emp_name'];
            $emp_autoid = $row['emp_autoid'];


            $leave = Leave::where('emp_id', $empid)
              ->where('leave_type', '3')
              ->whereDate('leave_from', '<=', $leavedate)
              ->whereDate('leave_to', '>=', $leavedate)
              ->first();
              if ($leave) {
                $leave->update([
                    'no_of_days' => '1',
                    'reson' => 'No Covering',
                    'leave_approv_person' => Auth::id(),
                    'status' => 'Approved',
                    'updated_at' =>$current_date_time
                ]);
            } else {  

            $leave = new Leave;
            $leave->emp_id = $empid;
            $leave->leave_type = '3';
            $leave->leave_from = $leavedate;
            $leave->leave_to = $leavedate;
            $leave->no_of_days = '1';
            $leave->half_short = '0';
            $leave->reson = 'No Covering';
            $leave->comment = '';
            $leave->emp_covering = '';
            $leave->leave_approv_person = Auth::id();
            $leave->status = 'Approved';
            $leave->save();
            }
        }
        return response()->json(['success' => 'Absent Nopay is successfully Approved']);
    }
}
