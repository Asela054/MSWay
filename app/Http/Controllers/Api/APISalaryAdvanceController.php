<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\SalaryAdvance;
use Auth;
use Carbon\Carbon;
use App\Http\Controllers\SalaryAdvanceController;

class APISalaryAdvanceController extends Controller
{
      public function __construct()
    {

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

     public function getAvailableAmount(Request $request)
    {
        
        $emp_id = $request->emp_id;
        $request_date = $request->input('date');
        // If called via HTTP GET, pick up the date query param
        if ($request_date === null) {
            $request_date = request()->query('date');
        }

        // Use today's date as fallback for month/year context
        $referenceDate = $request_date ? Carbon::parse($request_date) : Carbon::now();

         
        // Get employee's job_category
        $employee = DB::table('employees')
            ->leftJoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
            ->select(
                'job_categories.salary_advance_type',
                'job_categories.salary_advance_value',
                'job_categories.salary_advance_min_date',
                'employees.emp_id as emp_id',
                'employees.id as employee_id'
            )
            ->where('employees.emp_id', $emp_id)
            ->first();

          

        if (!$employee) {
            return response()->json(['available_amount' => 0]);
        }

        // Check minimum attendance days in the requested month
        if (!is_null($employee->salary_advance_min_date) && $employee->salary_advance_min_date > 0) {
            $monthStart = $referenceDate->copy()->startOfMonth()->toDateString();
            $selectedDate = $referenceDate->toDateString();

            $attendedDays = DB::table('attendances')
                ->where('emp_id', $employee->emp_id)
                ->whereBetween('date', [$monthStart, $selectedDate])
                ->distinct('date')
                ->count('date');

            if ($attendedDays < $employee->salary_advance_min_date) {
                return response()->json([
                    'available_amount' => 0,
                    'errors' => 'Minimum attendance of ' . $employee->salary_advance_min_date . ' day(s) not reached up to the selected date. Current attendance: ' . $attendedDays . ' day(s).'
                ]);
            }
        }

        // Get basic salary
        $payroll = DB::table('payroll_profiles')
            ->leftJoin('remuneration_profiles AS rp1', function($join) {
                $join->on('rp1.payroll_profile_id', '=', 'payroll_profiles.id')
                    ->where('rp1.remuneration_id', '=', 2);
            })
            ->leftJoin('remuneration_profiles AS rp2', function($join) {
                $join->on('rp2.payroll_profile_id', '=', 'payroll_profiles.id')
                    ->where('rp2.remuneration_id', '=', 26);
            })
            ->select(
                DB::raw('(payroll_profiles.basic_salary + IFNULL(rp1.new_eligible_amount, 0) + IFNULL(rp2.new_eligible_amount, 0)) as total_basic')
            )
            ->where('payroll_profiles.emp_id', $employee->employee_id)
            ->first();

        $basic_salary = $payroll ? $payroll->total_basic : 0;

        if ($employee->salary_advance_type == 1) {
            $available_amount = ($basic_salary * $employee->salary_advance_value) / 100;
        } else {
            $available_amount = $employee->salary_advance_value;
        }

        $data = [
            'available_amount' => $available_amount
        ];

        return (new BaseController)->sendResponse($data, $emp_id);
    }

     public function salary_advance_create(Request $request)
    {
        $employee = $request->input('emp_id');
        $date = $request->input('date');

         $otherController = new SalaryAdvanceController();
         $availableResponse = $otherController->getAvailableAmount($employee, $date);
         $availableData = json_decode($availableResponse->getContent(), true);

        if (isset($availableData['errors'])) {
            return response()->json(['errors' => $availableData['errors']]);
        }

        $available_amount = $availableData['available_amount'] ?? 0;

        if ($request->input('request_amount') > $available_amount) {
            return response()->json(['errors' => 'Amount exceeds the available advance limit of ' . $available_amount]);
        }

        $advance = new SalaryAdvance;
        $advance->emp_id = $employee;
        $advance->date = $date;
        $advance->request_amount = $request->input('request_amount');
        $advance->paid_amount = 0;
        $advance->remark = $request->input('remark');
        $advance->status = '1';
        $advance->paid_status = '0';
        $advance->created_by = $request->input('employee');
        $advance->created_at = Carbon::now()->toDateTimeString();
        $advance->save();

        return response()->json(['success' => 'Salary Advance Added successfully.']);
    }

    public function Getsalaryadvancelist(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'emp_id' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $query = DB::table('salary_advances')
            ->join('employees as e', 'salary_advances.emp_id', '=', 'e.emp_id')
            ->select('salary_advances.*', 'e.emp_name_with_initial as emp_name')
            ->where(['salary_advances.emp_id' => $request->emp_id])
            ->get();

        $data = array(
            'salaryadvancelist' => $query
        );

        return (new BaseController)->sendResponse($data, 'salaryadvancelist');
    }
}
