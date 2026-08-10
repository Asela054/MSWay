<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Leave;
use App\LeaveType;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Datatables;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceadditionaltimesController extends Controller
{
     public function index()
    {
        $permission = Auth::user()->can('Additional-Time-Approval-list');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.additional_times_approve');
    }

    public function gettimestapslist(Request $request){
        $permission = Auth::user()->can('Absent-Nopay-list');
        if (!$permission) {
            abort(403);
        }
        
        $department=$request->input('department');
        $fromDate =  $request->input('from_date');
        $toDate = $request->input('to_date');
        $employee = $request->input('employee');

         $period = CarbonPeriod::create($fromDate, $toDate)->toArray();
        
           $datareturn = [];
        
            $query =  DB::table('employees')
                ->select('emp_name_with_initial as emp_name','id as emp_autoid','emp_department','emp_id as empid','emp_location')
                ->where('deleted', 0)
                ->where('is_resigned', 0);

            if ($department != '') {
                $query->where(['emp_department' => $department]);
            }

            if ($employee != '') {
                $query->where(['emp_id' => $employee]);
            }
            $employees = $query->get();

            $datareturn = [];

        foreach ($employees as $emp) {

            $timestamps = DB::table('attendances')
                ->where('emp_id', $emp->empid)
                ->whereBetween('date', [$fromDate, $toDate])
                ->whereNull('deleted_at')
                ->orderBy('date')
                ->orderBy('timestamp')
                ->get();

            $groupedByDate = $timestamps->groupBy('date');

            foreach ($groupedByDate as $date => $rows) {
                $rows = $rows->values();

                if ($rows->count() <= 2) {
                    continue;
                }

                $middleRows = $rows->slice(1, $rows->count() - 2)->values();

                for ($i = 0; $i < $middleRows->count(); $i += 2) {

                    if (!isset($middleRows[$i + 1])) {
                        break;
                    }

                    $fromTime = $middleRows[$i]->timestamp;
                    $toTime   = $middleRows[$i + 1]->timestamp;

                    $totalMinutes = Carbon::parse($fromTime)->diffInMinutes(Carbon::parse($toTime));
                    if ($totalMinutes == 0) {
                        continue; 
                    }

                   $duration = round($totalMinutes / 60, 2);

                    $exists = DB::table('attendance_additional_timestamps')
                        ->where('emp_id', $emp->empid)
                        ->where('date', $date)
                        ->where('from_time', $fromTime)
                        ->where('to_time', $toTime)
                        ->exists();

                    $datareturn[] = [
                        'emp_id'    => $emp->empid,
                        'emp_name'  => $emp->emp_name,
                        'date'      => $date,
                        'from_time' => $fromTime,
                        'to_time'   => $toTime,
                        'duration'  => round($duration, 2),
                        'status'    => $exists ? '1' : '0',
                    ];
                }
            }
        }
       return response()->json([ 'data' => $datareturn ]);
    }

     public function approvetimestaps(Request $request)
    {

        $permission = Auth::user()->can('Additional-Time-Approval-list');
        if (!$permission) {
            abort(403);
        }


        $dataarry = $request->input('dataarry');
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $epfno = $row['emp_name'];
            $date = $row['date'];
            $from_time = $row['from_time'];
            $to_time = $row['to_time'];
            $duration = $row['duration'];

            DB::table('attendance_additional_timestamps')->insert([
            'emp_id'     => $empid,
            'date'       => $date,
            'from_time'  => $from_time,
            'to_time'    => $to_time,
            'duration'   => $duration,
            'status'     => 1,
            'created_at' => $current_date_time,
            'updated_at' => $current_date_time,
        ]);

        }

        return response()->json(['success' => 'Additional Times are successfully Approved']);
    }
}
