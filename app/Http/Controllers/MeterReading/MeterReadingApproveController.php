<?php

namespace App\Http\Controllers\MeterReading;

use App\EmployeeTermPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MeterReadingApproveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('meter-reading-Approve-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Addition')->get();
        return view('Meter_Reading.meter_reading_approve', compact('remunerations'));
    }

    public function generatemeterreading(Request $request){
        $user = Auth::user();
        $permission = $user->can('meter-reading-Approve-create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $company = $request->get('company');
        $department = $request->get('department');
        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query = DB::query()
            ->select('employees.id as emp_auto_id',
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.emp_join_date',
                'employees.emp_department',
                'departments.name as department_name',
                'meter_reading.reading_limit',
                'meter_reading.multiple_value'
            )
            ->from('employees as employees')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('meter_reading', 'employees.emp_department', '=', 'meter_reading.department_id');
        
        if ($employee != '') {
            $query->where(['employees.emp_id' => $employee]);
        }
        if ($company != '') {
            $query->where(['employees.emp_company' => $company]);
        }
        if ($department != '') {
            $query->where(['employees.emp_department' => $department]);
        }
        
        $query->where('employees.deleted', 0);
        $query->where('employees.is_resigned',0);
        $query->groupBy('employees.emp_id', 'employees.id', 'employees.emp_name_with_initial', 
                        'employees.emp_join_date', 'employees.emp_department', 'departments.name', 
                        'meter_reading.reading_limit', 'meter_reading.multiple_value');
        $results = $query->get();

        $data = [];

        foreach ($results as $record) {
        $readingQuery = DB::table('meter_reading_count')
            ->where('emp_id', $record->emp_id)
            ->whereBetween('date', [$from_date, $to_date]);

        $readingTotal = $readingQuery->sum('count');

        $approvedCount = DB::table('meter_reading_count')
            ->where('emp_id', $record->emp_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->where('approve_status', 1)
            ->count();

        $totalCount = DB::table('meter_reading_count')
            ->where('emp_id', $record->emp_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->count();

        if ($readingTotal == 0 ) {
            continue;
        }

        $multiple_value = $record->multiple_value ?? 1;

        $data[] = [
            'emp_auto_id' => $record->emp_auto_id,
                'emp_id' => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'department_name' => $record->department_name,
                'count' => $readingTotal, 
                'overall_total' => $readingTotal * $multiple_value,
                'is_approved' => ($approvedCount == $totalCount) ? 1 : 0
            ];

        }

        return response()->json([
            'data' => $data,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data)
        ]);
    }


    public function approvemeterreading(Request $request)
    {
        $permission = \Auth::user()->can('meter-reading-Approve-create');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('dataarry');
        $remunitiontype = $request->input('remunitiontype'); 
        
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $reading_total = $row['count'];
            $overall_total = $row['overall_total'];
            $autoid = $row['emp_auto_id'];

            DB::table('meter_reading_count')
                ->where('emp_id', $empid)
                ->whereBetween('date', [$request->input('from_date'), $request->input('to_date')])
                ->update(['approve_status' => 1]);

            $profiles = DB::table('payroll_profiles')
            ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
            ->where('payroll_profiles.emp_id', $autoid)
            ->select('payroll_profiles.id as payroll_profile_id')
            ->first();

            if ($profiles) {

                $paysliplast = DB::table('employee_payslips')
                    ->select('emp_payslip_no')
                    ->where('payroll_profile_id', $profiles->payroll_profile_id)
                    ->where('payslip_cancel', 0)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($paysliplast) {
                    $emp_payslipno = $paysliplast->emp_payslip_no;
                    $newpaylispno =  $emp_payslipno + 1;
                } else {
                    $newpaylispno = 1;
                }

                if($overall_total != 0){

                    $termpaymentcheck = DB::table('employee_term_payments')
                    ->select('id')
                    ->where('payroll_profile_id', $profiles->payroll_profile_id)
                    ->where('emp_payslip_no', $newpaylispno)
                    ->where('remuneration_id', $remunitiontype) 
                    ->first();
                
                    if($termpaymentcheck){
                        DB::table('employee_term_payments')
                        ->where('id', $termpaymentcheck->id)
                        ->update([
                            'payment_amount' => $overall_total,
                            'payment_cancel' => '0',
                            'updated_by' => Auth::id(),
                            'updated_at' => $current_date_time
                        ]);
                    } else {
                        $termpayment = new EmployeeTermPayment();
                        $termpayment->remuneration_id = $remunitiontype; 
                        $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                        $termpayment->emp_payslip_no = $newpaylispno;
                        $termpayment->payment_amount = $overall_total;
                        $termpayment->payment_cancel = 0;
                        $termpayment->created_by = Auth::id();
                        $termpayment->created_at = $current_date_time;
                        $termpayment->save(); 
                    }
                }
            } else {
                continue;
            }
        }

        return response()->json(['success' => 'Meter Reading is successfully Approved']);
    }


}
