<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryAdvanceApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('salary-advance-approval-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Deduction')->get();
        return view('Payroll.salaryAdvance.salaryAdvance_approval', compact('remunerations'));
    }

    public function generatesalaryadvance(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('salary-advance-approval-create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $company    = $request->get('company');
        $department = $request->get('department');
        $employee   = $request->get('employee');
        $from_date  = $request->get('from_date');
        $to_date    = $request->get('to_date');

        $query = DB::table('employees as employees')
            ->select(
                'employees.id as emp_auto_id',
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.emp_department',
                'departments.name as department_name'
            )
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0);

        if ($employee != '') {
            $query->where('employees.emp_id', $employee);
        }
        if ($company != '') {
            $query->where('employees.emp_company', $company);
        }
        if ($department != '') {
            $query->where('employees.emp_department', $department);
        }

        
        $query->whereExists(function ($sub) use ($from_date, $to_date) {
            $sub->select(DB::raw(1))
                ->from('salary_advances')
                ->whereColumn('salary_advances.emp_id', 'employees.emp_id')
                ->whereBetween('salary_advances.date', [$from_date, $to_date])
                ->where('salary_advances.paid_status', 1); 
        });

        $query->groupBy(
            'employees.id',
            'employees.emp_id',
            'employees.emp_name_with_initial',
            'employees.emp_department',
            'departments.name'
        );

        $results = $query->get();

        $data = [];

        foreach ($results as $record) {

            $allocations = DB::table('salary_advances')
                ->whereBetween('date', [$from_date, $to_date])
                ->where('emp_id', $record->emp_id)
                ->where('paid_status', 1)
                ->get();

            $totalCount    = $allocations->count();
            $approvedCount = $allocations->where('approve_status', 1)->count();
            $request_amount = $allocations->sum('request_amount'); 
            $paid_amount    = $allocations->sum('paid_amount');

            $data[] = [
                'emp_auto_id'           => $record->emp_auto_id,
                'emp_id'                => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'department_name'       => $record->department_name,
                'request_amount'        => $request_amount,
                'paid_amount'           => $paid_amount,
                'is_approved'           => ($totalCount > 0 && $approvedCount == $totalCount) ? 1 : 0,
            ];
        }

        return response()->json([
            'data'            => $data,
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
        ]);
    }


    public function approvesalaryadvance(Request $request)
    {
        $permission = \Auth::user()->can('salary-advance-approval-create'); 
        if (!$permission) {
            abort(403);
        }

        $dataarry       = $request->input('dataarry');
        $remunitiontype = $request->input('remunitiontype');
        $from_date      = $request->input('from_date');
        $to_date        = $request->input('to_date');

        $current_date_time = Carbon::now()->toDateTimeString();
        $errors = [];

        foreach ($dataarry as $row) {

            $empid          = $row['empid'];
            $empname        = $row['emp_name'];
            $advance_payment = $row['paid_amount']; 
            $autoid         = $row['emp_auto_id'];

            DB::table('salary_advances')
                ->where('emp_id', $empid)          
                ->where('paid_status', 1)           
                ->whereBetween('date', [$from_date, $to_date])
                ->update(['approve_status' => 1]);

            $profiles = DB::table('payroll_profiles')
                ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
                ->where('payroll_profiles.emp_id', $autoid)
                ->select('payroll_profiles.id as payroll_profile_id')
                ->first();

            if (!$profiles) {
                $errors[] = "No payroll profile found for employee: {$empname}";
                continue;
            }

            $paysliplast = DB::table('employee_payslips')
                ->select('emp_payslip_no')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('payslip_cancel', 0)
                ->orderBy('id', 'desc')
                ->first();

            $newpaylispno = $paysliplast ? ($paysliplast->emp_payslip_no + 1) : 1;

            if ($advance_payment != 0) {

                $termpaymentcheck = DB::table('employee_term_payments')
                    ->select('id')
                    ->where('payroll_profile_id', $profiles->payroll_profile_id)
                    ->where('emp_payslip_no', $newpaylispno)
                    ->where('remuneration_id', $remunitiontype)
                    ->first();

                if ($termpaymentcheck) {
                    DB::table('employee_term_payments')
                        ->where('id', $termpaymentcheck->id)
                        ->update([
                            'payment_amount' => $advance_payment, 
                            'payment_cancel' => '0',
                            'updated_by'     => Auth::id(),
                            'updated_at'     => $current_date_time,
                        ]);
                } else {
                    $termpayment                     = new EmployeeTermPayment();
                    $termpayment->remuneration_id    = $remunitiontype;
                    $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                    $termpayment->emp_payslip_no     = $newpaylispno;
                    $termpayment->payment_amount     = $advance_payment; 
                    $termpayment->payment_cancel     = 0;
                    $termpayment->created_by         = Auth::id();
                    $termpayment->created_at         = $current_date_time;
                    $termpayment->save();
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => 'Salary Advance approved with some issues.',
                'errors'  => $errors,
            ]);
        }

        return response()->json(['success' => 'Salary Advance is successfully Approved']);
    }


}
