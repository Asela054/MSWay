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

class DepartmentviseNopayController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Absent-Nopay-list');
        if (!$permission) {
            abort(403);
        }
        $leavetype = LeaveType::orderBy('id', 'asc')->get();
        return view('Attendent.absentnopay', compact('leavetype'));
    }


    public function getabsetnopay(Request $request){
        $permission = Auth::user()->can('Absent-Nopay-list');
        if (!$permission) {
            abort(403);
        }
        
        $department=$request->input('department');
        // $firstDate =  $request->input('from_date');
        $fromDate =  $request->input('from_date');
        $toDate = $request->input('to_date');

        $period = CarbonPeriod::create($fromDate, $toDate)->toArray();
        
        // Get accessible employee IDs based on user access rights
            $userId = Auth::id();
            $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
            
            // Return empty data if no accessible employees
            if (empty($accessibleEmployeeIds)) {
                return response()->json(['data' => []]);
            }

            $userBranchIds = DB::table('user_has_companies')
            ->where('user_id', $userId)
            ->pluck('branch_id')
            ->toArray();



        $datareturn = [];
        
        $query =  DB::table('employees')
            ->select('emp_name_with_initial as emp_name','id as emp_autoid','emp_department','emp_id as empid','emp_location')
            ->where('emp_department', '=', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->whereIn('emp_id', $accessibleEmployeeIds)
            ->when(!empty($userBranchIds), function($q) use ($userBranchIds) {
                return $q->whereIn('emp_location', $userBranchIds);
            })
            ->get();
            
            foreach ($query as $row) {
                $empId = $row->empid;
                $empName = $row->emp_name;
                $empautoid = $row->emp_autoid;
                
                foreach ($period as $date) {
                    $firstDate = $date->format('Y-m-d');

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
                                'emp_date' => $firstDate,
                            ];     
                        }
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
        $leavetype =  $request->input('leavetype');

        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $epfno = $row['emp_name'];
            $emp_autoid = $row['emp_autoid'];
            $emp_date = $row['emp_date'];

            $jobcategory = DB::table('employees')
                ->leftjoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
                ->where('emp_id', $empid)
                ->select('job_categories.is_sat_ot_type_as_act')
                ->first();

            $isSaturday = Carbon::parse($emp_date)->isSaturday();

            if ($isSaturday && $jobcategory->is_sat_ot_type_as_act == 2) {
                $saturdaynodays = '1';
            } else if ($isSaturday && $jobcategory->is_sat_ot_type_as_act == 1) { 
                $saturdaynodays = '0.5';
            }else {
                $saturdaynodays = '1';
            }

            $no_of_days = $isSaturday ? $saturdaynodays : '1'; 

            $leave = Leave::where('emp_id', $empid)
              ->where('leave_type', $leavetype)
              ->whereDate('leave_from', '<=', $emp_date)
              ->whereDate('leave_to', '>=', $emp_date)
              ->first();
              if ($leave) {
                $leave->update([
                    'no_of_days' => $no_of_days,
                    'reson' => 'No Covering',
                    'leave_approv_person' => Auth::id(),
                    'status' => 'Approved',
                    'updated_at' =>$current_date_time,
                    'approve_01' => 1,
                    'approve_01_time' =>$current_date_time,
                    'approve_01_by' => Auth::id(),
                    'approve_02' => 1,
                    'approve_02_time' =>$current_date_time,
                    'approve_02_by' => Auth::id(),
                    
                ]);
            } else {  

            $leave = new Leave;
            $leave->emp_id = $empid;
            $leave->leave_type = $leavetype;
            $leave->leave_from = $emp_date;
            $leave->leave_to = $emp_date;
            $leave->no_of_days = $no_of_days;
            $leave->half_short = $no_of_days;
            $leave->reson = 'No Covering';
            $leave->comment = '';
            $leave->emp_covering = '';
            $leave->leave_approv_person = Auth::id();
            $leave->status = 'Approved';
            $leave->approve_01 =  1;
            $leave->approve_01_time = $current_date_time;
            $leave->approve_01_by = Auth::id();
            $leave->approve_02 =  1;
            $leave->approve_02_time = $current_date_time;
            $leave->approve_02_by = Auth::id();
            $leave->save();
            }
        }
        return response()->json(['success' => 'Absent Leave is successfully Approved']);
    }
}
