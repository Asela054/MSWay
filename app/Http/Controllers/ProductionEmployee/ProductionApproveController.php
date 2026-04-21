<?php

namespace App\Http\Controllers\ProductionEmployee;

use App\EmployeeTermPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionApproveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('emp-production-Approve-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Addition')->get();
        return view('ProductionEmployee.productionApprove', compact('remunerations'));
    }

    public function generateproduction(Request $request){
        $user = Auth::user();
        $permission = $user->can('emp-production-Approve-create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $company    = $request->get('company');
        $department = $request->get('department');
        $section    = $request->get('section');
        $employee   = $request->get('employee');
        $from_date  = $request->get('from_date');
        $to_date    = $request->get('to_date');

        $query = DB::table('employees as employees')
            ->select(
                'employees.id as emp_auto_id',
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.emp_gender',
                'employees.emp_department',
                'departments.name as department_name'
            )
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('emp_production_allocation', function($join) use ($from_date, $to_date) {
                $join->on('emp_production_allocation.emp_id', '=', 'employees.emp_id')
                    ->whereBetween('emp_production_allocation.date', [$from_date, $to_date]);
            })
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0);

        if ($employee != '') {
            $query->where('employees.emp_id', $employee);
        }
        if ($company != '') {
            $query->where('employees.emp_company', $company);
        }
        if ($department != '') {
            $query->where('emp_production_allocation.department_id', $department);
        }
        if ($section != '') {
            $query->where('emp_production_allocation.section_id', $section);
        }

        $query->whereNotNull('emp_production_allocation.id');

        $query->groupBy(
            'employees.id',
            'employees.emp_id',
            'employees.emp_name_with_initial',
            'employees.emp_gender',
            'employees.emp_department',
            'departments.name'
        );

        $results = $query->get();

        $data = [];

        foreach ($results as $record) {

            $allocations = DB::table('emp_production_allocation')
                ->whereBetween('date', [$from_date, $to_date])
                ->where('emp_id', $record->emp_id)
                ->when($department != '', function($q) use ($department) { return $q->where('department_id', $department); })
                ->when($section != '', function($q) use ($section) { return $q->where('section_id', $section); })
                ->get();

            $overallTotal  = 0;
            $totalCount    = $allocations->count();
            $approvedCount = 0;

            $uniqueDates = $allocations->pluck('date')->unique()->sort()->values();
            $dateCount   = $uniqueDates->count();

            $incentiveField = ($record->emp_gender === 'Male') ? 'men_incentive' : 'women_incentive';

            foreach ($allocations as $allocation) {

                $detail = DB::table('emp_production_details')
                    ->where('section_id', $allocation->section_id)
                    ->first();

                if ($detail) {
                    $overallTotal += $detail->{$incentiveField};
                }

                if ($allocation->status == 1) {
                    $approvedCount++;
                }
            }

            $data[] = [
                'emp_auto_id'           => $record->emp_auto_id,
                'emp_id'                => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'department_name'       => $record->department_name,
                'date_count'            => $dateCount,
                'overall_total'         => $overallTotal,
                'is_approved'           => ($totalCount > 0 && $approvedCount == $totalCount) ? 1 : 0,
            ];
        }

        return response()->json([
            'data'            => $data,
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
        ]);
    }


    public function approveproduction(Request $request)
    {
        $permission = \Auth::user()->can('emp-production-Approve-create');
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

            $empid         = $row['empid'];       
            $empname       = $row['emp_name'];
            $overall_total = $row['overall_total'];
            $autoid        = $row['emp_auto_id']; 

            DB::table('emp_production_allocation')
                ->where('emp_id', $empid)
                ->whereBetween('date', [$from_date, $to_date])
                ->update(['status' => 1]);

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

            if ($overall_total != 0) {

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
                            'payment_amount' => $overall_total,
                            'payment_cancel' => '0',
                            'updated_by'     => Auth::id(),
                            'updated_at'     => $current_date_time,
                        ]);
                } else {
                    $termpayment                     = new EmployeeTermPayment();
                    $termpayment->remuneration_id    = $remunitiontype;
                    $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                    $termpayment->emp_payslip_no     = $newpaylispno;
                    $termpayment->payment_amount     = $overall_total;
                    $termpayment->payment_cancel     = 0;
                    $termpayment->created_by         = Auth::id();
                    $termpayment->created_at         = $current_date_time;
                    $termpayment->save();
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => 'Production Data approved with some issues.',
                'errors'  => $errors,
            ]);
        }

        return response()->json(['success' => 'Production Data is successfully Approved']);
    }

    public function getDateDetails(Request $request)
    {
        $permission = \Auth::user()->can('emp-production-Approve-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $emp_id    = $request->input('emp_id');
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');
        $department = $request->input('department');  
        $section    = $request->input('section');     

        $employee = DB::table('employees')
            ->where('emp_id', $emp_id)
            ->select('emp_name_with_initial', 'emp_gender')
            ->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $incentiveField = ($employee->emp_gender === 'Male') ? 'men_incentive' : 'women_incentive';

        $allocations = DB::table('emp_production_allocation')
            ->join('departments', 'emp_production_allocation.department_id', '=', 'departments.id')
            ->leftJoin('department_sections', 'emp_production_allocation.section_id', '=', 'department_sections.id')
            ->where('emp_production_allocation.emp_id', $emp_id)
            ->whereBetween('emp_production_allocation.date', [$from_date, $to_date])
            ->when($department != '', function($q) use ($department) { 
                return $q->where('emp_production_allocation.department_id', $department); 
            })
            ->when($section != '', function($q) use ($section) { 
                return $q->where('emp_production_allocation.section_id', $section); 
            })
            ->select(
                'emp_production_allocation.date',
                'departments.name as department_name',
                'department_sections.section as section_name',
                DB::raw("COALESCE((
                    SELECT {$incentiveField} 
                    FROM emp_production_details 
                    WHERE emp_production_details.section_id = emp_production_allocation.section_id 
                    LIMIT 1
                ), 0) as incentive")
            )
            ->orderBy('emp_production_allocation.date', 'asc')
            ->get();

        $totalIncentive = $allocations->sum('incentive');

        return response()->json([
            'emp_name'        => $employee->emp_name_with_initial,
            'total_incentive' => $totalIncentive,
            'dates'           => $allocations
        ]);
    }


}
