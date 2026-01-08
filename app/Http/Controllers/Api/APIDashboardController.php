<?php

namespace App\Http\Controllers\Api;

use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\LeavepolicyService;

class APIDashboardController extends Controller
{
    protected $leavePolicyService;

     public function __construct(LeavepolicyService $leavePolicyService)
    {

         $this->leavePolicyService = $leavePolicyService;

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }



        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

      public function GetApprovedUpcomingLeavesForDashboard(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }
            $leaves = DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type', '=', 'leave_types.id') 
            ->select('leaves.*', 'leave_types.leave_type as leave_type_name')
            ->where('leaves.emp_id', $request->employee_id)
            ->where('leaves.status', 'Approved')
            ->whereDate('leaves.leave_from', '>=', $request->date)
            ->orderBy('leaves.id','DESC')
            ->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Employee ID']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leaves');
    }

    public function Getdetails_maindashbord(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'emp_id' => 'required',
        ]);

        $timezone = 'Asia/Colombo';

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

          $emp_id = $request->input('emp_id');


        // Calculate leave balance

        $employee = Employee::where('emp_id', $emp_id)->first();
        if($employee == NULL){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Record ID']);
        }

        $emp_join_date = isset($employee->emp_join_date) ? $employee->emp_join_date : false;
        $join_year = Carbon::parse($emp_join_date)->year;
        $join_month = Carbon::parse($emp_join_date)->month;
        $join_date = Carbon::parse($emp_join_date)->day;
        $full_date = '2022-'.$join_month.'-'.$join_date;
        $empid = $employee->emp_id;
        $job_categoryid = $employee->job_category_id;


        $formated_from_date = date('Y').'-01-01';
        $formated_fromto_date = date('Y').'-12-31';

        $current_year_taken_a_l = (new \App\Leave)->taken_annual_leaves($empid, $formated_from_date, $formated_fromto_date);

        $current_year_taken_c_l = (new \App\Leave)->taken_casual_leaves($empid, $formated_from_date, $formated_fromto_date);

        $current_year_taken_med = (new \App\Leave)->taken_medical_leaves($empid, $formated_from_date, $formated_fromto_date);

       

        $annualData = $this->leavePolicyService->calculateAnnualLeaves($employee->emp_join_date, $employee->emp_id, $job_categoryid);
        $annual_leaves = $annualData['annual_leaves'];

        $casual_leaves = $this->leavePolicyService->calculateCasualLeaves($employee->emp_join_date, $job_categoryid);

         // medical leave calculation
        $medical_leaves = $this->leavePolicyService->getMedicalLeaves($employee->job_category_id);


        $total_no_of_annual_leaves = $annual_leaves;
        $total_no_of_casual_leaves = $casual_leaves;
        $total_no_of_med_leaves = $medical_leaves;

        $available_no_of_annual_leaves = $total_no_of_annual_leaves - $current_year_taken_a_l;
        $available_no_of_casual_leaves = $total_no_of_casual_leaves - $current_year_taken_c_l;
        $available_no_of_med_leaves = $total_no_of_med_leaves - $current_year_taken_med;

        $total_blance = $available_no_of_annual_leaves + $available_no_of_casual_leaves +  $available_no_of_med_leaves;



        // Attendance details 

       $today = Carbon::now($timezone)->toDateTimeString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::now()->toDateString();

         $monthlyAttendance = DB::table('attendances')
        ->select('emp_id','date')
        ->where('emp_id', $emp_id)
        ->whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->orderBy('date', 'desc')
        ->get()
        ->groupBy(function ($record) {
            return \Carbon\Carbon::parse($record->date)->format('Y-m-d');
        });
    
        $totalDays = $monthlyAttendance->count();

         $lastRecord = DB::table('attendances')
        ->select('timestamp')
        ->where('emp_id', $emp_id)
        ->latest('timestamp')
        ->first();

       $lastenterd_timestamp = null;
        if ($lastRecord) {
            $lastenterd_timestamp = $lastRecord->timestamp;
        }

         $workingdays = DB::table('employees')
        ->leftjoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
        ->select('job_categories.emp_payroll_workdays as workdays')
        ->where('employees.emp_id', $emp_id)
        ->first();



        $workingHoursToday = 0;
        $lastTimestampDate = null;
       

       $timezone = 'Asia/Colombo';

          if ($lastRecord && $lastRecord->timestamp) {
                // Parse as Colombo time (if stored as local time)
                $lastTimestamp = Carbon::createFromFormat(
                    'Y-m-d H:i:s', 
                    $lastRecord->timestamp, 
                    $timezone
                );
                
                $now = Carbon::now($timezone);
                
                if ($lastTimestamp->isToday()) {
                    $workingHoursToday = round($now->diffInMinutes($lastTimestamp) / 60, 2);
                }
          }
            // Calculate attendance percentage
            $attendancePercentage = 0;
            $workdays = $workingdays->workdays ?? 0; 

            if ($workdays > 0) {
                $attendancePercentage = ($totalDays / $workdays) * 100;

                $attendancePercentage = round($attendancePercentage, 2);
            }

        
        // Get last approved leaverequest 

        $lastApprovedTimestamp = DB::table('leaves')
        ->join('leave_request', 'leaves.request_id', '=', 'leave_request.id')
        ->where('leave_request.emp_id', $emp_id)
        ->where('leave_request.status', 1)
        ->where('leaves.status', 'Approved')
        ->latest('leaves.updated_at')
        ->value('leaves.updated_at');

        $lastApproved = $lastApprovedTimestamp ?: null;

        $data_arr = array(
            'annual' => $available_no_of_annual_leaves,
            'casual' => $available_no_of_casual_leaves,
            'medical' => $available_no_of_med_leaves,
            'total_leave_balance' => $total_blance,
            'attendance_precentage' => $attendancePercentage,
            'today_workhours' => $workingHoursToday,
            'lasttimestamp' => $lastenterd_timestamp,
            'last_leave_request_approved' => $lastApproved,
        );

        return (new BaseController)->sendResponse($data_arr, 'dashborddetails');
    }

    public function getdetails_attendancedashboard(Request $request)
    {
          $emp_id = $request->input('emp_id');
          $month = $request->get('month');

            $attendances = DB::table('attendances as at1')
            ->select(
                'at1.*',
                DB::raw('Min(at1.timestamp) as firsttimestamp'),
                DB::raw('(CASE 
                        WHEN Min(at1.timestamp) = Max(at1.timestamp) THEN ""  
                        ELSE Max(at1.timestamp)
                        END) AS lasttimestamp'),
                'employees.emp_id'
            )
            ->join('employees', 'at1.uid', '=', 'employees.emp_id')
            ->where('employees.emp_id', $emp_id)
            ->where('date', 'like', $month . '%')
            ->where('at1.deleted_at', null)
            ->groupBy('at1.uid', 'at1.date')
            ->get();

              // Calculate total work hours and days
            $totalWorkSeconds = 0;
            $totalWorkDays = 0;
            
            // Process each attendance record
            foreach ($attendances as $attendance) {
                if (!empty($attendance->firsttimestamp) && !empty($attendance->lasttimestamp)) {
                    $start = \Carbon\Carbon::parse($attendance->firsttimestamp);
                    $end = \Carbon\Carbon::parse($attendance->lasttimestamp);
                    
                    // Calculate duration in seconds
                    $duration = $start->diffInSeconds($end);
                    
                    // Only count as work day if duration is reasonable (e.g., more than 1 hour)
                    if ($duration > 3600) { // Minimum 1 hour to count as work day
                        $totalWorkSeconds += $duration;
                        $totalWorkDays++;
                    }
                }
            }

            // Calculate total work hours
            $totalWorkHours = $totalWorkSeconds / 3600;

            // Calculate average daily hours
            $averageDailyHours = $totalWorkDays > 0 ? $totalWorkHours / $totalWorkDays : 0;

            // Calculate total late days count
            $lateDaysCount = DB::table('employee_late_attendances')
                                ->where('emp_id', $emp_id)
                                ->where('date', 'like', $month . '%')
                                 ->where('is_approved', 1) 
                                ->count();


            $data_arr = array(
            'total_workhours' => round($totalWorkHours, 2),
            'total_workdays' => $totalWorkDays,
            'avarage_workhours' => round($averageDailyHours, 2),
            'latedays_count' => $lateDaysCount
        );

        return (new BaseController)->sendResponse($data_arr, 'dashborddetails');
    }

}
