<?php

namespace App\Http\Controllers;

use App\employeeWorkRate;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateInterval;
use DateTime;

class AttendanceApprovalController_st extends Controller
{
    public function attendance_list_for_approve(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $location = $request->get('company');
        $department = $request->get('department');
        $fromdate = $request->get('fromdate');
        $todate = $request->get('todate');
        
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
            ->leftJoin('attendances as at1', function ($join) use ($fromdate, $todate) {
                $join->on('employees.emp_id', '=', 'at1.uid')
                    ->whereNull('at1.deleted_at'); 
                if (!empty($fromdate)) {
                    $join->where('at1.date', '>=', $fromdate);
                }
                if (!empty($todate)) {
                    $join->where('at1.date', '<=', $todate);
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
            ->addColumn('work_days', function ($row) use ($fromdate, $todate) {
                if ($row->attendance_id) {
                    return $work_days = (new \App\Attendance_st)->get_work_days($row->emp_id, $fromdate, $todate);
                }
                return 0;
            })
            ->addColumn('working_week_days', function ($row) use ($fromdate, $todate) {
                if ($row->attendance_id) {
                    $working_week_days_arr = (new \App\Attendance_st)->get_working_week_days($row->emp_id, $fromdate, $todate);
                    return $working_week_days_arr['no_of_working_workdays'];
                }
                return 0;
                
            })
            ->addColumn('working_hours', function ($row) use ($fromdate, $todate) {
                if ($row->attendance_id) {
                    return $working_hours  = (new \App\Attendance_st)->get_working_hours($row->emp_id, $fromdate, $todate);
                }
                return 0;
            })
            ->addColumn('leave_days', function ($row) use ($fromdate, $todate) {
                if ($row->attendance_id) {
                    return $leave_days = (new \App\Leave_st)->get_leave_days($row->emp_id, $fromdate, $todate);
                }
                return 0;
            })
            ->addColumn('no_pay_days', function ($row) use ($fromdate, $todate) {
                if ($row->attendance_id) {
                    return $no_pay_days = (new \App\Leave_st)->get_no_pay_days($row->emp_id, $fromdate, $todate);
                }
                return 0;
               
            })
             ->addColumn('night_work_days', function ($row) use ($fromdate, $todate) {
                if ($row->attendance_id) {
                    return $night_work_days = (new \App\OtApproved)->get_night_work_days($row->emp_id, $fromdate, $todate);
                }
                return 0;
               
            })
            ->rawColumns(['date'])
            ->make(true);

    }


    public function AttendentAprovelBatch(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $current_date_time = Carbon::now()->toDateTimeString();

        $location = $request->get('company');
        $department = $request->get('department');
        $fromdate = $request->get('fromdate');
        $todate = $request->get('todate');

        
        $startDate = new DateTime($fromdate);
        $endDate = new DateTime($todate);


       $dateRange = [];
        while ($startDate <= $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->add(new DateInterval('P1D'));
        }

        $month = Carbon::parse($todate)->format('Y-m');

        $query = DB::query()
            ->select('at1.id as attendance_id',
                'employees.id as emp_auto_id',
                'employees.emp_id',
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
                'branches.location',
                'departments.name as dept_name',
                'job_categories.late_attend_min'                
            )
            ->from('employees as employees')
            // ->leftJoin('attendances as at1', 'employees.emp_id', '=', 'at1.uid')
            ->leftJoin('attendances as at1', function ($join) use ($fromdate, $todate) {
                $join->on('employees.emp_id', '=', 'at1.uid')
                    ->whereNull('at1.deleted_at'); 
                if (!empty($fromdate)) {
                    $join->where('at1.date', '>=', $fromdate);
                }
                if (!empty($todate)) {
                    $join->where('at1.date', '<=', $todate);
                }
            })
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'employees.job_category_id');

        if ($department != '' && $department != 'All') {
            $query->where(['departments.id' => $department]);
        }

        $query->where('employees.deleted', 0);
        $query->where('employees.is_resigned', 0);
        
        $query->groupBy('employees.emp_id');
        $results = $query->get();

        foreach ($results as $record) {
            $totalworkHours = 0;
            $totalweekworkshours = 0;
            $late_day_amount = 0;

            $work_days = (new \App\Attendance_st)->get_work_days($record->emp_id, $fromdate, $todate);
            $working_week_days_arr = (new \App\Attendance_st)->get_working_week_days($record->emp_id, $fromdate, $todate);
            $working_week_days = $working_week_days_arr['no_of_working_workdays'];

            $working_week_days_confirmed = (new \App\Attendance_st)->get_working_week_days_confirmed($record->emp_id, $fromdate, $todate);

            $confirmed_wd = $working_week_days;

            if($working_week_days_confirmed['no_of_days'] != null ){
                $confirmed_wd = $working_week_days_confirmed['no_of_days'];
            }

            $leave_days = (new \App\Leave_st)->get_leave_days($record->emp_id,  $fromdate, $todate);
            $no_pay_days = (new \App\Leave_st)->get_no_pay_days($record->emp_id, $fromdate, $todate);
            $duty_leaves = (new \App\Leave_st)->get_duty_leaves($record->emp_id, $fromdate, $todate);
            $dayoff_leaves = (new \App\Leave_st)->get_dayoff_leaves($record->emp_id, $fromdate, $todate);

            $normal_ot_hours = (new \App\OtApproved_st)->get_ot_hours_monthly($record->emp_id, $fromdate, $todate);

            $double_ot_hours = (new \App\OtApproved_st)->get_double_ot_hours_monthly($record->emp_id, $fromdate, $todate);

            $triple_ot_hours = (new \App\OtApproved_st)->get_triple_ot_hours_monthly($record->emp_id, $fromdate, $todate);

            $night_work_days = (new \App\OtApproved_st)->get_night_work_days($record->emp_id, $fromdate, $todate);

            $auditattedance = (new \App\Auditattendace)->apply_audit_attedance($record->emp_auto_id,$record->emp_id, $month);

             // $normal_ot_hours_additional = (new \App\OtApproved)->get_ot_hours_monthly_ktClean($record->emp_id, $month, $closedate);
              $normal_ot_hours_additional = 0;
            
            
            if(!empty($record->date)){
				$year_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->year;
				$month_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->month;

	
				// Fetch employee job category and work hour data
				$employee = DB::table('employees as e')
					->join('job_categories as jc', 'e.job_category_id', '=', 'jc.id')
					->select('e.id as empid', 'e.emp_id', 'e.emp_status', 'jc.work_hour_date','jc.salary_without_attendace','jc.shift_hours')
					->where('e.deleted', 0)
					->where('e.id', $record->emp_auto_id)
					->first();
	
	
				if ($employee) {
					$empoyeeId = $employee->empid;
					$empId = $employee->emp_id;
					$empstatus = $employee->emp_status;
					$workHourDate = $employee->work_hour_date;
					$salarystatus = $employee->salary_without_attendace;
                    $shift_hours = $employee->shift_hours;
				}
	

                $existingRecord = DB::table('employee_work_rates')
                    ->where('emp_id', $record->emp_auto_id)
                    ->where('work_year', $year_rec)
                    ->where('work_month', $month_rec)
                    ->first();

                // If existing record found, backup to backup table
                if ($existingRecord) {
                    DB::table('employee_work_rates_backup_records')->insert([
                        'record_id' => $existingRecord->id,
                        'emp_id' => $existingRecord->emp_id,
                        'emp_etfno' => $existingRecord->emp_etfno,
                        'work_year' => $existingRecord->work_year,
                        'work_month' => $existingRecord->work_month,
                        'work_days' => $existingRecord->work_days,
                        'working_week_days' => $existingRecord->working_week_days,
                        'work_hours' => $existingRecord->work_hours,
                        'leave_days' => $existingRecord->leave_days,
                        'nopay_days' => $existingRecord->nopay_days,
                        'emp_late_hours' => $existingRecord->emp_late_hours ?? 0,
                        'normal_rate_otwork_hrs' => $existingRecord->normal_rate_otwork_hrs,
                        'double_rate_otwork_hrs' => $existingRecord->double_rate_otwork_hrs,
                        'triple_rate_otwork_hrs' => $existingRecord->triple_rate_otwork_hrs ?? 0,
                        'holiday_nopay_days' => $existingRecord->holiday_nopay_days ?? 0,
                        'holiday_normal_ot_hrs' => $existingRecord->holiday_normal_ot_hrs ?? 0,
                        'holiday_double_ot_hrs' => $existingRecord->holiday_double_ot_hrs ?? 0,
                        'created_by' => Auth::user()->name,
                        'updated_by' => Auth::user()->name,
                    ]);
                }


            	DB::table('employee_work_rates')
					->where('emp_id', $record->emp_auto_id)
					->where('work_year', $year_rec)
					->where('work_month', $month_rec)
					->delete();


				//Insert Work Rate Table
				if($workHourDate === "Hour"){//Daily Or Weekly Salary
               
					foreach ($dateRange as $todayDate) {
						$ignoredate = DB::table('ignore_days')
							->select('ignore_days.*')
							->whereDate('date', $todayDate)
							->first();
                            
						if(!$ignoredate){
							 $query = DB::table('attendances as at1')
                                        ->select(
                                            'at1.id',
                                            'at1.emp_id',
                                            'at1.timestamp',
                                            'at1.date'
                                        )
                                        ->whereNull('at1.deleted_at')
                                        ->where('at1.emp_id', $record->emp_id)
                                        ->where('at1.date', 'LIKE', $todayDate . '%')
                                        ->orderBy('at1.timestamp', 'asc')
                                        ->get();
	
							if ($query->isNotEmpty()) {
								$timestamps = $query->pluck('timestamp')->toArray();
                                $count = count($timestamps);

                                // skip if odd number of timestamps
                                    if ($count % 2 === 0) {
                                        $totalMinutes = 0;

                                        for ($i = 0; $i < $count; $i += 2) {
                                        $in  = Carbon::parse($timestamps[$i])->second(0);
                                        $out = Carbon::parse($timestamps[$i + 1])->second(0);

                                            if ($in && $out && $in != $out) {
                                                $totalMinutes += $in->diffInMinutes($out);
                                            }
                                        }

                                        $totalworkHours += round($totalMinutes / 60, 2);
                                    }
							}
	
						}
					}

	
					$totalregularweekworkshours = $totalworkHours -($normal_ot_hours + $double_ot_hours);
                    
                    // this use to add duty leaves hours count to day salaries 
                    $totalweekworkshours = $totalregularweekworkshours + ($shift_hours * $duty_leaves);

                    

					if($salarystatus == 1 &&  $totalweekworkshours==0 && $empstatus == 1){
						$data3 = array(
							'emp_id' => $record->emp_auto_id,
							'emp_etfno' => $record->emp_id,
							'work_year' => $year_rec,
							'work_month' => $month_rec,
							'work_days' => 0,
							'working_week_days' => 0,
							'work_hours' => 0,
							'leave_days' => $leave_days,
							'nopay_days' => $no_pay_days,
							'normal_rate_otwork_hrs' => 0,
							'double_rate_otwork_hrs' => 0,
							'triple_rate_otwork_hrs' => 0,
                            'day_off' => 0,
                            'night_days' => 0,
                            'additional_OT' => 0,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						);
						employeeWorkRate::create($data3);
					}
					else{   
						$datasql = array(
							'emp_id' => $record->emp_auto_id,
							'emp_etfno' => $record->emp_id,
							'work_year' => $year_rec,
							'work_month' => $month_rec,
							'work_days' => $work_days,
							'working_week_days' => $work_days,
							'work_hours' => $totalweekworkshours,
							'leave_days' => $leave_days,
							'nopay_days' => $no_pay_days,
							'normal_rate_otwork_hrs' => $normal_ot_hours,
							'double_rate_otwork_hrs' => $double_ot_hours,
							'triple_rate_otwork_hrs' => $triple_ot_hours,
                            'day_off' => $dayoff_leaves,
                            'night_days' => $night_work_days,
                            'additional_OT' => $normal_ot_hours_additional,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						);
						employeeWorkRate::create($datasql);
					}
				}else{//Monthly Salary
					if($salarystatus == 1  && $work_days == 0 && $empstatus == 1 ){
						$data3 = array(
							'emp_id' => $record->emp_auto_id,
							'emp_etfno' => $record->emp_id,
							'work_year' => $year_rec,
							'work_month' => $month_rec,
							'work_days' => 0,
							'working_week_days' => 0,
							'work_hours' => 0,
							'leave_days' => $leave_days,
							'nopay_days' => $no_pay_days,
							'normal_rate_otwork_hrs' => 0,
							'double_rate_otwork_hrs' => 0,
							'triple_rate_otwork_hrs' => 0,
                            'day_off' => 0,
                            'night_days' => 0,
                            'additional_OT' => 0,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						);
						employeeWorkRate::create($data3);
	
					}else{
						$data2 = array(
							'emp_id' => $record->emp_auto_id,
							'emp_etfno' => $record->emp_id,
							'work_year' => $year_rec,
							'work_month' => $month_rec,
							'work_days' => $work_days,
							'working_week_days' => $work_days,
							'work_hours' => 0,
							'leave_days' => $leave_days,
							'nopay_days' => $no_pay_days,
							'normal_rate_otwork_hrs' => $normal_ot_hours,
							'double_rate_otwork_hrs' => $double_ot_hours,
							'triple_rate_otwork_hrs' => $triple_ot_hours,
                            'day_off' => $dayoff_leaves,
                            'night_days' => $night_work_days,
                            'additional_OT' => $normal_ot_hours_additional,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						);
						employeeWorkRate::create($data2);
					}
				}  
			}
        }

        //return success msg json
        return response()->json(['success' => true, 'message' => 'Attendance Successfully  Approved']);
    }


}
