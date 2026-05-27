<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ERPJobApproveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('kt-job-approve-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Addition')->get();
        return view('ERP_KT.job_approve', compact('remunerations'));
    }

    public function jobapprovegenerate(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('kt-job-approve-create');

        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $customer_name = $request->get('customer_name');
        $inquiry       = $request->get('inquiry');
        $machine_name  = $request->get('machine_name');
        $employee      = $request->get('employee');
        $job_title     = $request->get('job_title');
        $from_date     = $request->get('from_date');
        $to_date       = $request->get('to_date');

        $query = DB::table('kt_job_details as d')
            ->join('kt_job_inquiry as i', 'i.id', '=', 'd.job_id')
            ->leftJoin('kt_customer as c', 'c.id', '=', 'i.customer_id')
            ->leftJoin('kt_inquiry_details as idet', 'idet.id', '=', 'i.inquiry_id')
            ->leftJoin('kt_machines as m', 'm.id', '=', 'd.machine_id')
            ->leftJoin('employees as e', 'e.emp_id', '=', 'd.emp_id')
            ->leftJoin('job_titles as jt', 'jt.id', '=', 'd.job_title')
            ->leftJoin('kt_special_rate as sr', 'sr.emp_id', '=', 'e.emp_id')
            ->select(
                'd.id',
                'jt.title as job_title',
                'm.machine_name as machine',
                'e.emp_name_with_initial as employee',
                'e.id as emp_auto_id',
                'd.approve_status',
                'c.name as customer_name',
                'idet.inquiry as inquiry',
                DB::raw('ROUND(TIMESTAMPDIFF(MINUTE, i.start_from, i.end_at) / 60, 2) as reading_hours'),
                DB::raw('
                    CASE
                        WHEN EXISTS (
                            SELECT 1 FROM kt_special_rate
                            WHERE kt_special_rate.emp_id = e.emp_id
                            AND kt_special_rate.machine_id = d.machine_id
                        )
                            THEN ROUND((TIMESTAMPDIFF(MINUTE, i.start_from, i.end_at) / 60) * (
                                SELECT rate FROM kt_special_rate
                                WHERE kt_special_rate.emp_id = e.emp_id
                                AND kt_special_rate.machine_id = d.machine_id
                                LIMIT 1
                            ), 2)
                        WHEN EXISTS (
                            SELECT 1 FROM kt_special_rate
                            WHERE kt_special_rate.emp_id = e.emp_id
                            AND kt_special_rate.machine_id = 0
                        )
                            THEN ROUND((TIMESTAMPDIFF(MINUTE, i.start_from, i.end_at) / 60) * (
                                SELECT rate FROM kt_special_rate
                                WHERE kt_special_rate.emp_id = e.emp_id
                                AND kt_special_rate.machine_id = 0
                                LIMIT 1
                            ), 2)
                        WHEN jt.title = "OPERATOR"
                            THEN ROUND((TIMESTAMPDIFF(MINUTE, i.start_from, i.end_at) / 60) * m.operator_rate, 2)
                        WHEN jt.title = "HELPER"
                            THEN ROUND((TIMESTAMPDIFF(MINUTE, i.start_from, i.end_at) / 60) * m.helper_rate, 2)
                        ELSE 0
                    END as incentive
                ')
            )
            ->where('i.status', '!=', 3)

            ->when($customer_name, function ($q) use ($customer_name) {
                return $q->where('i.customer_id', $customer_name);
            })
            ->when($inquiry, function ($q) use ($inquiry) {
                return $q->where('i.inquiry_id', $inquiry);
            })
            ->when($machine_name, function ($q) use ($machine_name) {
                return $q->where('d.machine_id', $machine_name);
            })
            ->when($employee, function ($q) use ($employee) {
                return $q->where('d.emp_id', $employee);
            })
            ->when($job_title, function ($q) use ($job_title) {
                return $q->where('d.job_title', $job_title);
            })
            ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                return $q->whereBetween('i.start_from', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
            });

        $totalFiltered = $query->count();
        $data = $query->get();

        return response()->json([
            'draw'            => intval($request->get('draw')),
            'recordsTotal'    => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }


    public function jobapproveinquiry(Request $request)
    {
        $permission = \Auth::user()->can('kt-job-approve-create');
        if (!$permission) {
            abort(403);
        }

        $dataarry       = $request->input('dataarry');
        $remunitiontype = $request->input('remunitiontype');
        $current_date_time = Carbon::now()->toDateTimeString();

        $errors = [];

        // Group incentives by emp_auto_id (employees.id)
        $empIncentives = [];
        foreach ($dataarry as $row) {
            $autoId   = $row['emp_auto_id'];
            $incentive = floatval($row['incentive'] ?? 0);
            if (!isset($empIncentives[$autoId])) {
                $empIncentives[$autoId] = 0;
            }
            $empIncentives[$autoId] += $incentive;
        }

        // Process each selected row for attendance
        foreach ($dataarry as $row) {
            $cusid    = $row['cusid'];
            $autoId   = $row['emp_auto_id'];

            // Get job detail with inquiry times and employee info
            $jobDetail = DB::table('kt_job_details as d')
                ->join('kt_job_inquiry as i', 'i.id', '=', 'd.job_id')
                ->join('employees as e', 'e.emp_id', '=', 'd.emp_id')
                ->join('shift_types as st', 'st.id', '=', 'e.emp_shift')
                ->where('d.id', $cusid)
                ->select(
                    'd.id',
                    'd.emp_id',
                    'e.id as emp_auto_id',
                    'e.emp_location',
                    'i.start_from',
                    'i.end_at',
                    'st.onduty_time',
                    'st.offduty_time'
                )
                ->first();

            if (!$jobDetail) {
                $errors[] = "Job detail not found for record ID: {$cusid}";
                continue;
            }

            // Mark job as approved
            DB::table('kt_job_details')
                ->where('id', $cusid)
                ->update(['approve_status' => 1]);

            $empId     = $jobDetail->emp_id;
            $startFrom = Carbon::parse($jobDetail->start_from);
            $endAt     = Carbon::parse($jobDetail->end_at);
            $date      = $startFrom->toDateString();

            // Build shift on/off datetimes on the same date as start_from
            $shiftOn  = Carbon::parse($date . ' ' . $jobDetail->onduty_time);
            $shiftOff = Carbon::parse($date . ' ' . $jobDetail->offduty_time);

            // If shift crosses midnight, push offduty to next day
            if ($shiftOff->lt($shiftOn)) {
                $shiftOff->addDay();
            }

            $startOutsideShift = $startFrom->lt($shiftOn) || $startFrom->gt($shiftOff);
            $endOutsideShift   = $endAt->gt($shiftOff);

            $timestampsToSave = [];

            if ($startOutsideShift) {
                $timestampsToSave[] = [
                    'emp_id'    => $empId,
                    'date'      => $startFrom->toDateString(),
                    'timestamp' => $startFrom->toDateTimeString(),
                ];
                $timestampsToSave[] = [
                    'emp_id'    => $empId,
                    'date'      => $endAt->toDateString(),
                    'timestamp' => $endAt->toDateTimeString(),
                ];
            } elseif ($endOutsideShift) {
                $timestampsToSave[] = [
                    'emp_id'    => $empId,
                    'date'      => $endAt->toDateString(),
                    'timestamp' => $endAt->toDateTimeString(),
                ];
            }

            foreach ($timestampsToSave as $att) {
                $exists = DB::table('attendances')
                    ->where('emp_id', $att['emp_id'])
                    ->where('timestamp', $att['timestamp'])
                    ->exists();

                if (!$exists) {
                    DB::table('attendances')->insert([
                        'emp_id'    => $att['emp_id'],
                        'uid'       => $att['emp_id'],
                        'date'      => $att['date'],
                        'timestamp' => $att['timestamp'],
                        'location'  => $jobDetail->emp_location,
                    ]);
                }
            }
        }

        // Process payroll term payments per employee
        foreach ($empIncentives as $autoId => $overall_total) {
            $profiles = DB::table('payroll_profiles')
                ->where('emp_id', $autoId)
                ->select('id as payroll_profile_id')
                ->first();

            if (!$profiles) {
                $errors[] = "Payroll profile not found for employee ID: {$autoId}";
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
                    DB::table('employee_term_payments')->insert([
                        'remuneration_id'    => $remunitiontype,
                        'payroll_profile_id' => $profiles->payroll_profile_id,
                        'emp_payslip_no'     => $newpaylispno,
                        'payment_amount'     => $overall_total,
                        'payment_cancel'     => 0,
                        'created_by'         => Auth::id(),
                        'created_at'         => $current_date_time,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => 'Jobs successfully approved.',
            'errors'  => $errors,
        ]);
    }
}
