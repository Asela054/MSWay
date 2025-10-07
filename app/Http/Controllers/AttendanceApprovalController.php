<?php

namespace App\Http\Controllers;

use App\employeeWorkRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateInterval;
use DateTime;

class AttendanceApprovalController extends Controller
{
     public function attendanceapprovel()
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');
        if(!$permission){
            abort(403);
        }

        return view('Attendent.attendanceapprovel');
    }

    public function attendance_list_for_approve(Request $request)
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

        // if ($department != '' && $department != 'All') {
        //     $query->where(['departments.id' => $department]);
        // }

        $query->where('employees.emp_id', 5);

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
            ->addColumn('working_week_days', function ($row) use ($month , $closedate) {
                if ($row->attendance_id) {
                    $working_week_days_arr = (new \App\Attendance)->get_working_week_days($row->emp_id, $month, $closedate);
                    return $working_week_days_arr['no_of_working_workdays'];
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
                    return $leave_days = (new \App\Attendance)->get_leave_days($row->emp_id, $month, $closedate);
                }
                return 0;
            })
            ->addColumn('no_pay_days', function ($row) use ($month, $closedate) {
                if ($row->attendance_id) {
                    return $no_pay_days = (new \App\Attendance)->get_no_pay_days($row->emp_id, $month, $closedate);
                }
                return 0;
               
            })
            ->rawColumns(['date'])
            ->make(true);

    }

    public function getAttendanceApprovel(Request $request)
    {
        $attendance = DB::query()
            ->select('at1.*', DB::raw('Min(at1.timestamp) as firsttimestamp'), DB::raw('Max(at1.timestamp) as lasttimestamp'), 'employees.emp_name_with_initial', 'fingerprint_devices.location')
            ->from('attendances as at1')
            ->Join('employees', 'at1.uid', '=', 'employees.id')
            ->Join('fingerprint_devices', 'at1.devicesno', '=', 'fingerprint_devices.sno')
            ->groupBy('at1.uid', 'at1.date')
            ->where([
                ['uid', '=', $request->id],
                ['approved', '=', '0'],
            ])
            ->get();


        return response()->json($attendance);


    }

    public function AttendentAprovel(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if ($request->ajax())

            $appval = 1;
        {
            $data = array(
                'approved' => $appval
            );
            DB::table('attendances')
                ->where('uid', $request->emp_id)
                ->update($data);

            $full_month = $request->month;
            //get 1st 4 characters
            $year = substr($full_month, 0, 4);
            //get last 2 characters
            $month = substr($full_month, -2);

            $startDate = new DateTime("$year-$month-01");
            $endDate = (clone $startDate)->modify('first day of next month');

            //delete existing records for the month and emp_id from employee_work_rates
            DB::table('employee_work_rates')
                ->where('emp_id', $request->emp_id)
                ->where('work_month', $month)
                ->where('work_year', $year)
                ->delete();


                $employee = DB::table('employees as e')
                ->join('job_categories as jc', 'e.job_category_id', '=', 'jc.id')
                ->select('e.id as empid', 'e.emp_id', 'jc.work_hour_date')
                ->where('e.deleted', 0)
                ->where('e.emp_id',  $request->emp_id)
                ->first();
                if ($employee) {
                    $empoyeeId = $employee->empid;
                    $empId = $employee->emp_id;
                    $workHourDate = $employee->work_hour_date;
                }

                if($workHourDate === "Hour"){

                    $totalworkHours = 0;
                    $totalweekworkshours = 0;
                    while ($startDate < $endDate) {
                        $todayDate = $startDate->format('Y-m-d');


                        $query = DB::table('attendances as at1')
                        ->select(
                            'at1.id',
                            'at1.emp_id',
                            'at1.timestamp',
                            'at1.date',
                            DB::raw('MIN(at1.timestamp) AS firsttimestamp'),
                            DB::raw('CASE WHEN MIN(at1.timestamp) = MAX(at1.timestamp) THEN "" 
                            ELSE MAX(at1.timestamp) END AS lasttimestamp'),
                            'shift_types.onduty_time',
                            'shift_types.offduty_time'
                        )
                        ->leftJoin('employees', 'at1.emp_id', '=', 'employees.emp_id')
                        ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
                        ->whereNull('at1.deleted_at')
                        ->where('employees.emp_id', $empId)
                        ->where('at1.date', $todayDate)
                        ->get();


                        if ($query->isNotEmpty()) {
                            $firsttimestamp = Carbon::parse($query->first()->firsttimestamp);
                            $lasttimestamp = Carbon::parse($query->first()->lasttimestamp);
                        
                            if ($firsttimestamp && $lasttimestamp && $firsttimestamp != $lasttimestamp) {
                                $workHours = $firsttimestamp->diffInHours($lasttimestamp);
                                $totalworkHours+= $workHours;
                            }
                        }

                        $totalweekworkshours = $totalworkHours -($request->ot + $request->dot);

                        $form_data = array(
                            'emp_id' => $request->emp_auto_id,
                            'emp_etfno' => $request->emp_id,
                            'work_year' => $year,
                            'work_month' => $month,
                            'work_days' => $request->workdays,
                            'working_week_days' => $request->working_week_days,
                            'work_hours' => $totalweekworkshours,
                            'leave_days' => $request->leavedate,
                            'nopay_days' => $request->nopay,
                            'normal_rate_otwork_hrs' => $request->ot,
                            'double_rate_otwork_hrs' => $request->dot,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        DB::table('employee_work_rates')->insert($form_data);

                        $startDate->add(new DateInterval('P1D')); // Add 1 day
                    }

                }else{
                    $form_data = array(
                        'emp_id' => $request->emp_auto_id,
                        'emp_etfno' => $request->emp_id,
                        'work_year' => $year,
                        'work_month' => $month,
                        'work_days' => $request->workdays,
                        'working_week_days' => $request->working_week_days,
                        'work_hours' => 0,
                        'leave_days' => $request->leavedate,
                        'nopay_days' => $request->nopay,
                        'normal_rate_otwork_hrs' => $request->ot,
                        'double_rate_otwork_hrs' => $request->dot,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
        
                    DB::table('employee_work_rates')->insert($form_data);
                }
                
           


            //echo '<div class="alert alert-success">Attendent Details Approved</div>';

            return response()->json(['status' => true, 'msg' => 'Attendance Details Approved']);


        }
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
        $month = $request->get('month');
        $closedate = $request->get('closedate');

        $selectedyear = substr($month, 0, 4);
        $selectedmonth = substr($month, -2);
        $startDate = new DateTime("$selectedyear-$selectedmonth-01");
        $endDate = (clone $startDate)->modify('first day of next month');
        
        $closedateObj = new DateTime($closedate);
        if ($endDate > $closedateObj) {
            $endDate = $closedateObj;
        }

        $dateRange = [];
        while ($startDate < $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->add(new DateInterval('P1D'));
        }
          // If $closedate is within the month, include it in the range
          if ($closedateObj->format('Y-m-d') >= $startDate->format('Y-m-d')) {
            $dateRange[] = $closedateObj->format('Y-m-d');
        }

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

            $work_days = (new \App\Attendance)->get_work_days($record->emp_id, $month, $closedate);
            $working_week_days_arr = (new \App\Attendance)->get_working_week_days($record->emp_id, $month, $closedate);
            $working_week_days = $working_week_days_arr['no_of_working_workdays'];

            $working_week_days_confirmed = (new \App\Attendance)->get_working_week_days_confirmed($record->emp_id, $month, $closedate);

            $confirmed_wd = $working_week_days;

            if($working_week_days_confirmed['no_of_days'] != null ){
                $confirmed_wd = $working_week_days_confirmed['no_of_days'];
            }

            $leave_days = (new \App\Attendance)->get_leave_days($record->emp_id, $month , $closedate);
            $no_pay_days = (new \App\Attendance)->get_no_pay_days($record->emp_id, $month, $closedate);

            $normal_ot_hours = (new \App\OtApproved)->get_ot_hours_monthly($record->emp_id, $month, $closedate);

            $double_ot_hours = (new \App\OtApproved)->get_double_ot_hours_monthly($record->emp_id, $month, $closedate);

            $triple_ot_hours = (new \App\OtApproved)->get_triple_ot_hours_monthly($record->emp_id, $month, $closedate);

            $auditattedance = (new \App\Auditattendace)->apply_audit_attedance($record->emp_auto_id,$record->emp_id, $month);
            
            if(!empty($record->date)){
				$year_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->year;
				$month_rec = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->month;
	
				DB::table('employee_work_rates')
					->where('emp_id', $record->emp_auto_id)
					->where('work_year', $year_rec)
					->where('work_month', $month_rec)
					->delete();
	
	
				// Fetch employee job category and work hour data
				$employee = DB::table('employees as e')
					->join('job_categories as jc', 'e.job_category_id', '=', 'jc.id')
					->select('e.id as empid', 'e.emp_id', 'e.emp_status', 'jc.work_hour_date','jc.salary_without_attendace')
					->where('e.deleted', 0)
					->where('e.id', $record->emp_auto_id)
					->first();
	
	
				if ($employee) {
					$empoyeeId = $employee->empid;
					$empId = $employee->emp_id;
					$empstatus = $employee->emp_status;
					$workHourDate = $employee->work_hour_date;
					$salarystatus = $employee->salary_without_attendace;
				}
	
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
								'at1.date',
								DB::raw('MIN(at1.timestamp) AS firsttimestamp'),
								DB::raw('CASE WHEN MIN(at1.timestamp) = MAX(at1.timestamp) THEN NULL 
										ELSE MAX(at1.timestamp) END AS lasttimestamp'),
								'shift_types.onduty_time',
								'shift_types.offduty_time'
							)
							->leftJoin('employees', 'at1.emp_id', '=', 'employees.emp_id')
							->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
							->whereNull('at1.deleted_at')
							->where('at1.emp_id', $record->emp_id)
							->where('at1.date', 'LIKE', $todayDate . '%')
							->havingRaw('MIN(at1.timestamp) != MAX(at1.timestamp)')
							->get();
	
							if ($query->isNotEmpty()) {
								$firsttimestamp = Carbon::parse($query->first()->firsttimestamp);
								$lasttimestamp = Carbon::parse($query->first()->lasttimestamp);
							
								if ($firsttimestamp && $lasttimestamp && $firsttimestamp != $lasttimestamp) {
									$diffInMinutes = $firsttimestamp->diffInMinutes($lasttimestamp);
									$workHours = round($diffInMinutes / 60, 2);
									$totalworkHours+= $workHours; 
								}
							}
	
						}
					}
	
					$totalweekworkshours = $totalworkHours -($normal_ot_hours + $double_ot_hours);
	
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
