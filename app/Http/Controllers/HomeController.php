<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Attendance;
use DB;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $today = Carbon::now()->format('Y-m-d');
    //     $empcount = DB::table('employees')->where('deleted', 0)->where('is_resigned', 0)->count();

    //     // today attendance count
    //     $todaycount = DB::table('attendances')
    //         ->select('date', 'emp_id')
    //         ->where('date', $today)
    //         ->groupBy('date', 'emp_id')
    //         ->get()
    //         ->count();

    //     // today late attendance count
    //     $late_times = DB::table('late_types')->where('id', 1)->first();
    //     $todaylatecount = DB::table('attendances')
    //         ->select('date', 'emp_id')
    //         ->where('date', $today)
    //         ->where('timestamp','>', $today. ' ' . $late_times->time_from)
    //         ->groupBy('date', 'emp_id')
    //         ->get()
    //         ->count();

    //     // get today daybefore day on attendance
    //     $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

    //     // yesterday attendance count
    //     $yesterdaycount = DB::table('attendances')
    //         ->select('date', 'emp_id')
    //         ->where('date', $yesterdayDate)
    //         ->groupBy('date', 'emp_id')
    //         ->get()
    //         ->count();

    //     // yesterday late attendance count
    //     $yesterdaylatecount = DB::table('attendances')
    //         ->select('date', 'emp_id')
    //         ->where('date', $yesterdayDate)
    //         ->havingRaw('MIN(attendances.timestamp) > ?', [$yesterdayDate . ' ' . $late_times->time_from])
    //         ->groupBy('date', 'emp_id')
    //         ->get()
    //         ->count();

    //     // Birthday Count
    //     $currentMonth = Carbon::now()->month;
    //     $currentDay = Carbon::now()->day;
    //     $nextMonth = Carbon::now()->addMonth()->month;
    //     $nextMonthYear = Carbon::now()->addMonth()->year;

    //     // Today's Birthday Count
    //     $todayBirthdayCount = DB::table('employees')
    //         ->where('deleted', 0)
    //         ->where('is_resigned', 0)
    //         ->whereMonth('emp_birthday', $currentMonth)
    //         ->whereDay('emp_birthday', $currentDay)
    //         ->count();

    //     // This Week's Birthday Count
    //     $thisweekBirthdayCount = DB::table('employees')
    //         ->where('deleted', 0)
    //         ->where('is_resigned', 0)
    //         ->whereBetween(DB::raw('DATE_FORMAT(emp_birthday, "%m-%d")'), [
    //             Carbon::now()->startOfWeek()->format('m-d'),
    //             Carbon::now()->endOfWeek()->format('m-d'),
    //         ])
    //         ->count();

    //     // This Month's Birthday Count
    //     $thismonthBirthdayCount = DB::table('employees')
    //         ->where('deleted', 0)
    //         ->where('is_resigned', 0)
    //         ->whereMonth('emp_birthday', $currentMonth)
    //         ->count();

    //     // Next Month's Birthday Count
    //     $nextmonthBirthdayCount = DB::table('employees')
    //         ->where('deleted', 0)
    //         ->where('is_resigned', 0)
    //         ->whereMonth('emp_birthday', $nextMonth)
    //         ->count();

    //     return view('home', compact(
    //         'empcount',
    //         'todaycount',
    //         'todaylatecount',
    //         'yesterdaycount',
    //         'yesterdaylatecount',
    //         'todayBirthdayCount',
    //         'thisweekBirthdayCount',
    //         'thismonthBirthdayCount',
    //         'nextmonthBirthdayCount'
    //     ));
    // }
    public function index()
    {

       
        $userId = Auth::id();
        $user = DB::table('users')
                        ->select('users.emp_id', 'users.company_id')
                        ->where('users.id', $userId)
                        ->first();

     
        if ($user) {
            // Get the employee's company details
            $employee = DB::table('employees')
                ->select('employees.emp_company')
                ->where('employees.emp_id', $user->emp_id)
                ->first();

                
            if ($employee) {
                // Get company details
                $company = DB::table('companies')
                    ->select('id', 'name', 'code', 'address')
                    ->where('id', $employee->emp_company)
                    ->first();

                if ($company) {
                    Session::put('company_id', $company->id);
                    Session::put('company_name', $company->name);
                }
            }
        }


        $today = Carbon::now()->format('Y-m-d');
        $empcount = DB::table('employees')->where('deleted', 0)->where('is_resigned', 0)->count();

        // today attendance count
        $todaycount = DB::table('attendances')
            ->select('date', 'emp_id')
            ->where('date', $today)
            ->groupBy('date', 'emp_id')
            ->get()
            ->count();

        // today late attendance count
        $late_times = DB::table('late_types')->orderBy('id', 'desc')->first();
        $todaylatecount = DB::table('attendances')
            ->select('date', 'emp_id')
            ->where('date', $today)
            ->where('timestamp','>', $today. ' ' . $late_times->time_from)
            ->groupBy('date', 'emp_id')
            ->get()
            ->count();

        // get today daybefore day on attendance
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

        // yesterday attendance count
        $yesterdaycount = DB::table('attendances')
            ->select('date', 'emp_id')
            ->where('date', $yesterdayDate)
            ->groupBy('date', 'emp_id')
            ->get()
            ->count();

        // yesterday late attendance count
        $yesterdaylatecount = DB::table('attendances')
            ->select('date', 'emp_id')
            ->where('date', $yesterdayDate)
            ->havingRaw('MIN(attendances.timestamp) > ?', [$yesterdayDate . ' ' . $late_times->time_from])
            ->groupBy('date', 'emp_id')
            ->get()
            ->count();

        // Birthday Count
        $currentMonth = Carbon::now()->month;
        $currentDay = Carbon::now()->day;
        $nextMonth = Carbon::now()->addMonth()->month;
        $nextMonthYear = Carbon::now()->addMonth()->year;

        // Today's Birthday Count
        $todayBirthdayCount = DB::table('employees')
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->whereMonth('emp_birthday', $currentMonth)
            ->whereDay('emp_birthday', $currentDay)
            ->count();

        // This Week's Birthday Count
        $thisweekBirthdayCount = DB::table('employees')
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->whereBetween(DB::raw('DATE_FORMAT(emp_birthday, "%m-%d")'), [
                Carbon::now()->startOfWeek()->format('m-d'),
                Carbon::now()->endOfWeek()->format('m-d'),
            ])
            ->count();

        // This Month's Birthday Count
        $thismonthBirthdayCount = DB::table('employees')
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->whereMonth('emp_birthday', $currentMonth)
            ->count();

        // Next Month's Birthday Count
        $nextmonthBirthdayCount = DB::table('employees')
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->whereMonth('emp_birthday', $nextMonth)
            ->count();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $leavedatalist = DB::table('leaves')
            ->leftJoin('leave_types', 'leave_types.id', '=', 'leaves.leave_type')
            ->leftJoin('employees', 'employees.emp_id', '=', 'leaves.emp_id')
            ->leftJoin('employee_pictures', 'employee_pictures.emp_id', '=', 'employees.emp_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->whereMonth('leaves.leave_from', $currentMonth)
            ->whereYear('leaves.leave_from', $currentYear)
            ->where('leaves.status', 'Approved')
            ->select(
                'employees.emp_id',
                'leave_types.leave_type',
                'leaves.leave_from',
                'leaves.leave_to',
                'leaves.no_of_days',
                'leaves.reson',
                'employees.emp_name_with_initial',
                'employee_pictures.emp_pic_picture',
                'departments.name as department'
            )
            ->get();

        $employeesbday = DB::table('employees')
            ->select('emp_name_with_initial', 'emp_birthday')
            ->where('is_resigned', 0)
            ->where('deleted', 0)
            ->whereNotNull('emp_birthday')
            ->get();

        $holidays = DB::table('holidays')
            ->select('holiday_name', 'date')
            ->get();

        $currentYear = Carbon::now()->year;

        // Process birthdays
        $birthdayEvents = $employeesbday->map(function ($employee) use ($currentYear) {
            // Format the birthday to "mm-dd-yyyy" format
            $birthdayDate = \Carbon\Carbon::parse($employee->emp_birthday);
            
            return [
                'date' => $birthdayDate->format('m-d') . '-' . $currentYear,
                'name' => $employee->emp_name_with_initial . "'s Birthday",
                'color' => '#ff69b4' // You can customize colors as needed
            ];
        })->toArray();

        // Process holidays
        $holidayEvents = $holidays->map(function ($holiday) {
            // Format the holiday date to match the same format (mm-dd-yyyy)
            $holidayDate = \Carbon\Carbon::parse($holiday->date)->format('m-d-Y');
            
            return [
                'date' => $holidayDate,
                'name' => $holiday->holiday_name,
                'color' => '#5d4697' // Different color for holidays
            ];
        })->toArray();

        $events = array_merge($birthdayEvents, $holidayEvents);

        return view('home', compact(
            'empcount',
            'todaycount',
            'todaylatecount',
            'yesterdaycount',
            'yesterdaylatecount',
            'todayBirthdayCount',
            'thisweekBirthdayCount',
            'thismonthBirthdayCount',
            'nextmonthBirthdayCount',
            'leavedatalist',
            'events'
        ));
    }

    public function nextmonth_birthday() {
        $nextMonth = Carbon::now()->addMonth();
        $nextMonthNumber = $nextMonth->month;

        // Fetch department data
        $departmentdata = DB::table('departments')
            ->select('id', 'name')
            ->get()
            ->toArray();

        // Fetch employees with next month's birthday, sorted by day
        $employeedata = DB::table('employees')
            ->select('employees.emp_id', 'employees.emp_name_with_initial', 'employees.emp_department', 'employees.emp_birthday')
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->whereMonth('emp_birthday', $nextMonthNumber)
            ->orderBy(DB::raw('DAY(emp_birthday)'), 'asc')
            ->get();

        // Process filtered employees
        $filteredEmployees = [];
        foreach ($employeedata as $employee) {
            if (!empty($employee->emp_birthday)) {
                $birthday = Carbon::parse($employee->emp_birthday);
                $filteredEmployees[] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'emp_department' => $employee->emp_department,
                    'emp_birthday' => $birthday->format('Y-m-d'),
                    'day_sort' => $birthday->day
                ];
            }
        }

        // Sort by day of month
        usort($filteredEmployees, function($a, $b) {
            return $a['day_sort'] - $b['day_sort'];
        });

        $nextmonthBirthdayCount = count($filteredEmployees);

        // Group by department
        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($filteredEmployees as $employee) {
            $departmentId = $employee['emp_department'];
            if (isset($departmentMap[$departmentId])) {
                $departmentName = $departmentMap[$departmentId];
                if (!isset($employeesByDepartment[$departmentName])) {
                    $employeesByDepartment[$departmentName] = [];
                }
                $employeesByDepartment[$departmentName][] = $employee;
            }
        }

        // Generate HTML
        $htmlTables = '';
        foreach ($employeesByDepartment as $departmentName => $employees) {
            $count = 1;
            $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';
            $htmlTables .= '<h5>' . $departmentName . '</h5>';
            $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>Birthday</th></tr>';

            foreach ($employees as $employee) {
                $htmlTables .= '<tr>';
                $htmlTables .= '<td>' . $count . '</td>';
                $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                $htmlTables .= '<td>' . $employee['emp_birthday'] . '</td>';
                $htmlTables .= '</tr>';
                $count++;
            }

            $htmlTables .= '</table>';
            $htmlTables .= '<hr style="border-top: 1px solid black;">';
        }

        if ($nextmonthBirthdayCount == 0) {
            $htmlTables .= '<p>No employees have birthdays next month.</p>';
        }

        return response()->json([
            'result' => $htmlTables,
            'nextmonthBirthdayCount' => $nextmonthBirthdayCount
        ]);
    }

    public function department_attendance(){
        $today = Carbon::now()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $today)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the today.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_lateattendance(){
        $today = Carbon::now()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();
        $late_times = DB::table('late_types')->where('id', 1)->first();
        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $today)
        ->where('attendances.timestamp','>', $today. ' ' . $late_times->time_from)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the today.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_absent(){
        $today = Carbon::now()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('date', '=', $today)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $employeedata= DB::table('employees')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->get();

        $employeeMap = [];
        foreach ($employeedata as $employee) {
            $employeeMap[$employee->emp_id] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'emp_department' => $employee->emp_department
            ];
        }
       
        $uniqueEmployeeData = [];
        foreach ($attendance as $attendant) {
            $employeeId = $attendant->emp_id;
            if (isset($employeeMap[$employeeId])) {
                unset($employeeMap[$employeeId]);
            }
        }
    
        foreach ($employeeMap as $employeeId => $employeeData) {
            $uniqueEmployeeData[] = $employeeData;
        }

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($uniqueEmployeeData as $employee) {
            $departmentId = $employee['emp_department'];

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee['emp_id'],
                    'emp_name_with_initial' => $employee['emp_name_with_initial']
                ];
            }
        }


            $htmlTables = '';

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

//--------------------------------------------------------------------------------
    // yesterday attendance part

    public function department_yesterdayattendance(){
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $yesterdayDate)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));
            $last_time = date('H:i', strtotime($employee->lasttimestamp));

            if($first_time==$last_time){
                $last_time='00-00';
            }

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time,
                    'lasttimestamp' => $last_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th><th>Out Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '<td>' . $employee['lasttimestamp'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the yesterday.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_yesterdaylateattendance(){
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();
        $late_times = DB::table('late_types')->where('id', 1)->first();
        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $yesterdayDate)
        ->havingRaw('MIN(attendances.timestamp) > ?', [$yesterdayDate . ' ' . $late_times->time_from])
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));
            $last_time = date('H:i', strtotime($employee->lasttimestamp));

            if($first_time==$last_time){
                $last_time='00-00';
            }

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time,
                    'lasttimestamp' => $last_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th><th>Out Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '<td>' . $employee['lasttimestamp'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the yesterday.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_yesterdayabsent(){
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('date', '=', $yesterdayDate)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $employeedata= DB::table('employees')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->get();

        $employeeMap = [];
        foreach ($employeedata as $employee) {
            $employeeMap[$employee->emp_id] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'emp_department' => $employee->emp_department
            ];
        }
       
        $uniqueEmployeeData = [];
        foreach ($attendance as $attendant) {
            $employeeId = $attendant->emp_id;
            if (isset($employeeMap[$employeeId])) {
                unset($employeeMap[$employeeId]);
            }
        }
    
        foreach ($employeeMap as $employeeId => $employeeData) {
            $uniqueEmployeeData[] = $employeeData;
        }

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($uniqueEmployeeData as $employee) {
            $departmentId = $employee['emp_department'];

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee['emp_id'],
                    'emp_name_with_initial' => $employee['emp_name_with_initial']
                ];
            }
        }


            $htmlTables = '';

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

// --------------------------------------------------------------------------------------------------------------
public function emp_work_days(Request $request) {
    $emp_working_days = $request->input('emp_working_days'); // Get filter value from request
    $today = Carbon::now(); // Current date

    // Fetch department data
    $departmentdata = DB::table('departments')
        ->select('id', 'name')
        ->get()
        ->toArray();

    // Fetch employees
    $employeedata = DB::table('employees')
        ->select('employees.emp_id', 'employees.emp_name_with_initial', 'employees.emp_department', 'employees.emp_join_date')
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->get();

    // Filter employees based on working days
    $filteredEmployees = [];
    foreach ($employeedata as $employee) {
        if (!empty($employee->emp_join_date)) {
            $joinDate = Carbon::parse($employee->emp_join_date);
            $workingDays = $today->diffInDays($joinDate); // Calculate working days

            if ($workingDays >= $emp_working_days) { // Filter based on selected working days
                $filteredEmployees[] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'emp_department' => $employee->emp_department,
                    'workDays' => $workingDays // Pass calculated working days
                ];
            }
        }
    }

    // Group by department
    $departmentMap = [];
    foreach ($departmentdata as $department) {
        $departmentMap[$department->id] = $department->name;
    }

    $employeesByDepartment = [];
    foreach ($filteredEmployees as $employee) {
        $departmentId = $employee['emp_department'];

        if (isset($departmentMap[$departmentId])) {
            $departmentName = $departmentMap[$departmentId];
            if (!isset($employeesByDepartment[$departmentName])) {
                $employeesByDepartment[$departmentName] = [];
            }

            $employeesByDepartment[$departmentName][] = $employee;
        }
    }

    // Generate HTML table
    $htmlTables = '';
    foreach ($employeesByDepartment as $departmentName => $employees) {
        $count = 1;
        $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';
        $htmlTables .= '<h5>' . $departmentName . '</h5>';
        $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name</th><th>Working Days</th></tr>';

        foreach ($employees as $employee) {
            $htmlTables .= '<tr>';
            $htmlTables .= '<td>' . $count . '</td>';
            $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
            $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
            $htmlTables .= '<td>' . $employee['workDays'] . '</td>';
            $htmlTables .= '</tr>';

            $count++;
        }

        $htmlTables .= '</table>';
        $htmlTables .= '<hr style="border-top: 1px solid black;">';
    }

    return response()->json([
        'result' => $htmlTables
    ]);
}

// --------------------------------------------------------------------------------------------------------------

public function today_birthday() {
    $today = Carbon::now();
    $currentMonth = $today->month;
    $currentDay = $today->day;

    // Fetch department data
    $departmentdata = DB::table('departments')
        ->select('id', 'name')
        ->get()
        ->toArray();

    // Fetch employees with today's birthday, sorted by birthday
    $employeedata = DB::table('employees')
        ->select('employees.emp_id', 'employees.emp_name_with_initial', 'employees.emp_department', 'employees.emp_birthday')
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->whereMonth('emp_birthday', $currentMonth)
        ->whereDay('emp_birthday', $currentDay)
        ->orderBy('emp_birthday', 'asc')
        ->get();

    // Process filtered employees
    $filteredEmployees = [];
    foreach ($employeedata as $employee) {
        if (!empty($employee->emp_birthday)) {
            $birthday = Carbon::parse($employee->emp_birthday);
            $filteredEmployees[] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'emp_department' => $employee->emp_department,
                'emp_birthday' => $birthday->format('Y-m-d'),
                'birthday_sort' => $birthday->format('m-d')
            ];
        }
    }

    // Sort by birthday
    usort($filteredEmployees, function($a, $b) {
        return strcmp($a['birthday_sort'], $b['birthday_sort']);
    });

    $todayBirthdayCount = count($filteredEmployees);

    // Group by department
    $departmentMap = [];
    foreach ($departmentdata as $department) {
        $departmentMap[$department->id] = $department->name;
    }

    $employeesByDepartment = [];
    foreach ($filteredEmployees as $employee) {
        $departmentId = $employee['emp_department'];
        if (isset($departmentMap[$departmentId])) {
            $departmentName = $departmentMap[$departmentId];
            if (!isset($employeesByDepartment[$departmentName])) {
                $employeesByDepartment[$departmentName] = [];
            }
            $employeesByDepartment[$departmentName][] = $employee;
        }
    }

    // Generate HTML for Birthday Table
    $htmlTables = '';
    foreach ($employeesByDepartment as $departmentName => $employees) {
        $count = 1;
        $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';
        $htmlTables .= '<h5>' . $departmentName . '</h5>';
        $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>Birthday</th></tr>';

        foreach ($employees as $employee) {
            $htmlTables .= '<tr>';
            $htmlTables .= '<td>' . $count . '</td>';
            $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
            $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
            $htmlTables .= '<td>' . $employee['emp_birthday'] . '</td>';
            $htmlTables .= '</tr>';
            $count++;
        }

        $htmlTables .= '</table>';
        $htmlTables .= '<hr style="border-top: 1px solid black;">';
    }

    if ($todayBirthdayCount == 0) {
        $htmlTables .= '<p>No employees have birthdays today.</p>';
    }

    return response()->json([
        'result' => $htmlTables,
        'todayBirthdayCount' => $todayBirthdayCount
    ]);
}

public function thisweek_birthday() {
    $startOfWeek = Carbon::now()->startOfWeek()->format('m-d');
    $endOfWeek = Carbon::now()->endOfWeek()->format('m-d');

    $thisweekBirthdayCount = DB::table('employees')
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->whereBetween(DB::raw('DATE_FORMAT(emp_birthday, "%m-%d")'), [$startOfWeek, $endOfWeek])
        ->count();

    // Fetch employees with proper date sorting
    $employeesByDepartment = DB::table('employees')
        ->select('emp_id', 'emp_name_with_initial', 'emp_department', 'emp_birthday')
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->whereBetween(DB::raw('DATE_FORMAT(emp_birthday, "%m-%d")'), [$startOfWeek, $endOfWeek])
        ->orderBy(DB::raw('DATE_FORMAT(emp_birthday, "%m-%d")'), 'asc')
        ->get()
        ->groupBy('emp_department');

    $departmentdata = DB::table('departments')
        ->select('id', 'name')
        ->get()
        ->keyBy('id');

    $htmlTables = '';
    foreach ($employeesByDepartment as $departmentId => $employees) {
        $departmentName = isset($departmentdata[$departmentId]) ? $departmentdata[$departmentId]->name : 'Unknown Department';
        $count = 1;

        // Sort employees within department by birthday
        $employeesArray = $employees->toArray();
        usort($employeesArray, function($a, $b) {
            $dateA = Carbon::parse($a->emp_birthday)->format('m-d');
            $dateB = Carbon::parse($b->emp_birthday)->format('m-d');
            return strcmp($dateA, $dateB);
        });

        $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';
        $htmlTables .= '<h5>' . $departmentName . '</h5>';
        $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>Birthday</th></tr>';

        foreach ($employeesArray as $employee) {
            $htmlTables .= '<tr>';
            $htmlTables .= '<td>' . $count . '</td>';
            $htmlTables .= '<td>' . $employee->emp_id . '</td>';
            $htmlTables .= '<td>' . $employee->emp_name_with_initial . '</td>';
            $htmlTables .= '<td>' . Carbon::parse($employee->emp_birthday)->format('Y-m-d') . '</td>';
            $htmlTables .= '</tr>';
            $count++;
        }

        $htmlTables .= '</table>';
        $htmlTables .= '<hr style="border-top: 1px solid black;">';
    }

    if ($thisweekBirthdayCount == 0) {
        $htmlTables .= '<p>No employees have birthdays this week.</p>';
    }

    return response()->json([
        'result' => $htmlTables,
        'thisweekBirthdayCount' => $thisweekBirthdayCount
    ]);
}

public function thismonth_birthday() {
    $today = Carbon::now();
    $currentMonth = $today->month;

    // Fetch department data
    $departmentdata = DB::table('departments')
        ->select('id', 'name')
        ->get()
        ->toArray();

    // Fetch employees with this month's birthday, sorted by day
    $employeedata = DB::table('employees')
        ->select('employees.emp_id', 'employees.emp_name_with_initial', 'employees.emp_department', 'employees.emp_birthday')
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->whereMonth('emp_birthday', $currentMonth)
        ->orderBy(DB::raw('DAY(emp_birthday)'), 'asc')
        ->get();

    // Process filtered employees
    $filteredEmployees = [];
    foreach ($employeedata as $employee) {
        if (!empty($employee->emp_birthday)) {
            $birthday = Carbon::parse($employee->emp_birthday);
            $filteredEmployees[] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'emp_department' => $employee->emp_department,
                'emp_birthday' => $birthday->format('Y-m-d'),
                'day_sort' => $birthday->day
            ];
        }
    }

    // Sort by day of month
    usort($filteredEmployees, function($a, $b) {
        return $a['day_sort'] - $b['day_sort'];
    });

    $thismonthBirthdayCount = count($filteredEmployees);

    // Group by department
    $departmentMap = [];
    foreach ($departmentdata as $department) {
        $departmentMap[$department->id] = $department->name;
    }

    $employeesByDepartment = [];
    foreach ($filteredEmployees as $employee) {
        $departmentId = $employee['emp_department'];
        if (isset($departmentMap[$departmentId])) {
            $departmentName = $departmentMap[$departmentId];
            if (!isset($employeesByDepartment[$departmentName])) {
                $employeesByDepartment[$departmentName] = [];
            }
            $employeesByDepartment[$departmentName][] = $employee;
        }
    }

    // Generate HTML
    $htmlTables = '';
    foreach ($employeesByDepartment as $departmentName => $employees) {
        $count = 1;
        $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';
        $htmlTables .= '<h5>' . $departmentName . '</h5>';
        $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>Birthday</th></tr>';

        foreach ($employees as $employee) {
            $htmlTables .= '<tr>';
            $htmlTables .= '<td>' . $count . '</td>';
            $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
            $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
            $htmlTables .= '<td>' . $employee['emp_birthday'] . '</td>';
            $htmlTables .= '</tr>';
            $count++;
        }

        $htmlTables .= '</table>';
        $htmlTables .= '<hr style="border-top: 1px solid black;">';
    }

    if ($thismonthBirthdayCount == 0) {
        $htmlTables .= '<p>No employees have birthdays this month.</p>';
    }

    return response()->json([
        'result' => $htmlTables,
        'thismonthBirthdayCount' => $thismonthBirthdayCount
    ]);
}


//----------------------------------------------------------------------------------

    public function getAttendentChart(Request $request)
    {
        // $data = DB::table('attendances')
        //     ->join('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        //     ->select('attendances.date', DB::raw('COUNT(DISTINCT attendances.uid) as count'))
        //     ->where('employees.deleted', 0)
        //     ->groupBy('attendances.date')
        //     ->limit(30)
        //     ->orderBy('attendances.date', 'desc')
        //     ->get();
        $data = DB::select("
            WITH RECURSIVE date_series AS (
                SELECT CURDATE() as date
                UNION ALL
                SELECT DATE_SUB(date, INTERVAL 1 DAY)
                FROM date_series
                WHERE date > DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            )
            SELECT 
                ds.date,
                COALESCE(COUNT(DISTINCT a.uid), 0) as count
            FROM date_series ds
            LEFT JOIN attendances a ON DATE(a.date) = ds.date 
                AND a.deleted_at IS NULL
            LEFT JOIN employees e ON e.emp_id = a.emp_id 
                AND e.deleted = 0
            GROUP BY ds.date
            ORDER BY ds.date DESC
        ");
        return response()->json($data);

    }
}
