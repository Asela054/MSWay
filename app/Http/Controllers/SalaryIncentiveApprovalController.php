<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryIncentiveApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('salary-incentive-approval-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Addition')->get();
        return view('Payroll.salaryIncentive.salaryIncentive_approval', compact('remunerations'));
    }

    public function generatesalaryincentive(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('salary-incentive-approval-create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $company    = $request->get('company');
        $department = $request->get('department');
        $employee   = $request->get('employee');
        $month      = $request->get('month');

        // Convert yyyy-mm to first-of-month for DB comparison
        if ($month) {
            $month = Carbon::parse($month)->startOfMonth()->toDateString();
        }

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

        $query->whereExists(function ($sub) use ($month) {
            $sub->select(DB::raw(1))
                ->from('salary_incentives')
                ->whereColumn('salary_incentives.emp_id', 'employees.emp_id')
                ->where('salary_incentives.status', '!=', 3);
            if ($month) {
                $sub->where('salary_incentives.month', $month);
            }
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

            $incentives = DB::table('salary_incentives')
                ->where('emp_id', $record->emp_id)
                ->where('status', '!=', 3);

            if ($month) {
                $incentives->where('month', $month);
            }

            $incentives = $incentives->get();

            $totalCount    = $incentives->count();
            $approvedCount = $incentives->where('approve_status', 1)->count();
            $paid_amount   = $incentives->sum('paid_amount');
            $recordMonth   = $incentives->first() ? $incentives->first()->month : null;

            $data[] = [
                'emp_auto_id'           => $record->emp_auto_id,
                'emp_id'                => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'department_name'       => $record->department_name,
                'paid_amount'           => $paid_amount,
                'month'                 => $recordMonth ? Carbon::parse($recordMonth)->format('Y-m') : '',
                'is_approved'           => ($totalCount > 0 && $approvedCount == $totalCount) ? 1 : 0,
            ];
        }

        return response()->json([
            'data'            => $data,
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
        ]);
    }

    public function approvesalaryincentive(Request $request)
    {
        $permission = \Auth::user()->can('salary-incentive-approval-create'); 
        if (!$permission) {
            abort(403);
        }

        $dataarry       = $request->input('dataarry');
        $remunitiontype = $request->input('remunitiontype');
        $month          = $request->input('month');

        // Convert yyyy-mm to first-of-month
        if ($month) {
            $month = Carbon::parse($month)->startOfMonth()->toDateString();
        }

        $current_date_time = Carbon::now()->toDateTimeString();
        $errors = [];

        foreach ($dataarry as $row) {

            $empid          = $row['empid'];
            $empname        = $row['emp_name'];
            $advance_payment = $row['paid_amount']; 
            $autoid         = $row['emp_auto_id'];

            DB::table('salary_incentives')
                ->where('emp_id', $empid)
                ->where('status', '!=', 3)
                ->where('month', $month)
                ->update(['approve_status' => 1, 'approve_by' => Auth::id(), 'updated_by' => Auth::id(), 'updated_at' => $current_date_time]);

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
