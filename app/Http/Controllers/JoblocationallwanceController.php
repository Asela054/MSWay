<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;

class JoblocationallwanceController extends Controller
{
     public function index()
    {
        $permission = Auth::user()->can('Location-Allowance-Approve-list');
        if (!$permission) {
            abort(403);
        }
        $locations = DB::table('branches')->select('*')->get();
        $employees=DB::table('employees')->select('emp_id','emp_name_with_initial','calling_name')
        ->where('deleted',0)
        ->where('is_resigned',0)
        ->where('outstation_payment',1)
        ->get();
        return view('jobmanagement.locationallowance', compact('locations','employees'));
    }

    public function generatelocationallowance(Request $request){
         $user = Auth::user();
        $permission = $user->can('Location-Allowance-Approve-Create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

          $query = DB::query()
            ->select('employees.id as emp_auto_id',
                'employees.calling_name',
                'employees.emp_id',
                'employees.emp_name_with_initial'            
            )
            ->from('employees as employees');
        if ($employee != '') {
            $query->where(['employees.emp_id' => $employee]);
        }
        $query->where('employees.deleted', 0);
        $query->where('employees.is_resigned',0);
        $query->where('employees.outstation_payment',1);
        $query->groupBy('employees.emp_id');
        $results = $query->get();

         foreach ($results as $record) {

            $employeeObj = (object)[
            'emp_id' => $record->emp_id,
            'emp_name_with_initial' => $record->emp_name_with_initial,
            'calling_name' => $record->calling_name
        ];

                    // Get all attendance records for this employee in the date range
                $attendanceRecords = DB::table('job_attendance')
                    ->where('employee_id', $record->emp_id)
                    ->whereBetween('attendance_date', [$from_date, $to_date])
                    ->get();
                
                $uniqueDaysWithOutsideLocation = [];
                
                foreach ($attendanceRecords as $attendance) {
                    $day = date('Y-m-d', strtotime($attendance->attendance_date));
                    
                    // Skip if we've already counted this day
                    if (in_array($day, $uniqueDaysWithOutsideLocation)) {
                        continue;
                    }
                    
                    // Check if location is outside
                    $branch = DB::table('branches')
                        ->where('id', $attendance->location_id)
                        ->where('outside_location', 1)
                        ->first();
                    
                    if ($branch) {
                        $uniqueDaysWithOutsideLocation[] = $day;
                    }
                }
                
              // Only add to array if there are visits to outside locations
                if (count($uniqueDaysWithOutsideLocation) > 0) {
                    // Get the allowance amount
                    $allowanceConfig = DB::table('amount_configuration')
                        ->where('id', 1)
                        ->first();
                    
                    $allowanceAmount = $allowanceConfig ? $allowanceConfig->pay_amount : 0;
                    $totalAllowance = count($uniqueDaysWithOutsideLocation) * $allowanceAmount;
                    
                    $data[] = [
                        'emp_auto_id' => $record->emp_auto_id,
                        'emp_id' => $record->emp_id,
                        'emp_name_with_initial' =>EmployeeHelper::getDisplayName($employeeObj),
                        'visit_count' => count($uniqueDaysWithOutsideLocation),
                        'allowance_amount' => $allowanceAmount,
                        'total_allowance' => $totalAllowance
                    ];
                }
         }
         return response()->json(['data' => $data]);
    }
   
        public function approvelocationallowance(Request $request)
    {

        $permission = \Auth::user()->can('Location-Allowance-Approve-Create');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('dataarry');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $visit_count = $row['visit_count'];
            $allowance_amount = str_replace([','], '', $row['allowance_amount']);
            $autoid = $row['emp_auto_id'];

            $profiles = DB::table('payroll_profiles')
            ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
            ->where('payroll_profiles.emp_id', $autoid)
            ->select('payroll_profiles.id as payroll_profile_id')
            ->first();

        if ($profiles) {

            $remunerationid = 32;

            $paysliplast = DB::table('employee_payslips')
                ->select('emp_payslip_no')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('payslip_cancel', 0)
                ->orderBy('id', 'desc')
                ->first();

            if ($paysliplast) {
                $emp_payslipno = $paysliplast->emp_payslip_no;
                $newpaylispno =  $emp_payslipno +1;
            }else{
                $newpaylispno = 1;
            }



             if($allowance_amount != 0){

                $termpaymentcheck = DB::table('employee_term_payments')
                ->select('id')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('emp_payslip_no', $newpaylispno)
                ->where('remuneration_id', $remunerationid)
                ->first();
            
                if($termpaymentcheck){
                    DB::table('employee_term_payments')
                    ->where('id', $termpaymentcheck->id)
                    ->update([
                        'payment_amount' => $allowance_amount,
                        'payment_cancel' => '0',
                        'updated_by' => Auth::id(),
                        'updated_at' => $current_date_time
                    ]);
                }
                else{
                    $termpayment = new EmployeeTermPayment();
                    $termpayment->remuneration_id = $remunerationid;
                    $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                    $termpayment->emp_payslip_no = $newpaylispno;
                    $termpayment->payment_amount = $allowance_amount;
                    $termpayment->payment_cancel = 0;
                    $termpayment->created_by = Auth::id();
                    $termpayment->created_at = $current_date_time;
                    $termpayment->save(); 
                }


                 $existingAllowance = DB::table('location _visit_allowances')
                    ->where('employee_id', $empid)
                    ->where('from_date', $from_date)
                    ->where('to_date', $to_date)
                    ->first();

                if ($existingAllowance) {
                    DB::table('location _visit_allowances')
                        ->where('id', $existingAllowance->id)
                        ->update([
                            'visit_count' => $visit_count,
                            'amount' => $allowance_amount,
                            'updated_at' => $current_date_time
                        ]);
                } else {

                    DB::table('location _visit_allowances')->insert([
                        'employee_id' => $empid,
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'visit_count' => $visit_count,
                        'amount' => $allowance_amount,
                        'created_at' => $current_date_time,
                        'updated_at' => $current_date_time
                    ]);
                }


            }
            
        }
        else{
            continue;
        }

        }

        return response()->json(['success' => 'Production and Task Insentive is successfully Approved']);
    }
}
