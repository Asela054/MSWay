<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateInterval;
use DateTime;

class RptemployeeAttendancesummaryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('attendance-report');
        if(!$permission){
            abort(403);
        }

        return view('Report.attendancesummry_report');
        
    }

      public function attendance_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $location = $request->get('company');
        $department = $request->get('department');
        $month = $request->get('month');
        $closedate = $request->get('closedate');
        
        // Get accessible employee IDs based on user access rights
        $userId = Auth::id();
        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);

        $userBranchIds = DB::table('user_has_companies')
            ->where('user_id', $userId)
            ->pluck('branch_id')
            ->toArray();

        $query = DB::query()
            ->select('at1.id as attendance_id',
                'at1.emp_id',
                'at1.uid',
                'at1.state',
                'at1.timestamp',
                'at1.date',
                'at1.approved',
                'at1.type',
                'at1.devicesno',
                DB::raw('Min(at1.timestamp) as firsttimestamp'),
                DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                'employees.emp_name_with_initial',
                'employees.emp_location',
                'branches.location',
                'departments.name as dept_name'
            )
            ->from('employees as employees')
            // ->leftJoin('attendances as at1', 'employees.emp_id', '=', 'at1.uid')
            ->leftJoin('attendances as at1', function ($join) use ($month) {
                $join->on('employees.emp_id', '=', 'at1.uid')
                    ->whereNull('at1.deleted_at'); 
                if (!empty($month)) {
                    $m_str = $month . "%";
                    $join->where('at1.date', 'like', $m_str); 
                }
                if (!empty($closedate)) {
                    $join->where('at1.date', '<=', $closedate);
                }
            })
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department');

        // Apply user access rights filter
        if (!empty($accessibleEmployeeIds)) {
            $query->whereIn('employees.emp_id', $accessibleEmployeeIds);
        }

         if (!empty($userBranchIds)) {
            $query->whereIn('employees.emp_location', $userBranchIds);
        }

        if ($department != '' && $department != 'All') {
            $query->where(['departments.id' => $department]);
        }

        $query->where('employees.deleted', 0);
        $query->where('employees.is_resigned', 0);
        
        $query->groupBy('employees.emp_id');

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                if ($row->date) {
                    $rec_date = Carbon::parse($row->date)->toDateString();
                    $date_c = Carbon::createFromFormat('Y-m-d', $rec_date);
                    return $date_c->format('Y-m');
                }
                return '-';
            })
            ->addColumn('work_days', function ($row) use ($month, $closedate) {
                if ($row->attendance_id) {
                    return $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month, $closedate);
                }
                return 0;
            })
            ->addColumn('working_hours', function ($row) use ($month, $closedate) {
                if ($row->attendance_id) {
                    return $working_hours  = (new \App\Attendance)->get_working_hours($row->emp_id, $month, $closedate);
                }
                return 0;
            })
            ->addColumn('leave_days', function ($row) use ($month, $closedate) {
                if ($row->attendance_id) {
                    return $leave_days = (new \App\Leave)->get_leave_days($row->emp_id, $month, $closedate);
                }
                return 0;
            })
            ->addColumn('no_pay_days', function ($row) use ($month, $closedate) {
                if ($row->attendance_id) {
                    return $no_pay_days = (new \App\Leave)->get_no_pay_days($row->emp_id, $month, $closedate);
                }
                return 0;
               
            })
             ->addColumn('normal_ot', function ($row) use ($month, $closedate) {
                if ($row->attendance_id) {
                    return $normal_ot_hours = (new \App\OtApproved)->get_ot_hours_monthly($row->emp_id, $month, $closedate);
                }
                return 0;
            })
             ->addColumn('double_ot', function ($row) use ($month, $closedate) {
                if ($row->attendance_id) {
                    return $double_ot_hours = (new \App\OtApproved)->get_double_ot_hours_monthly($row->emp_id, $month, $closedate);
                }
                return 0;
            })
            
            ->rawColumns(['date'])
            ->make(true);

    }
}
