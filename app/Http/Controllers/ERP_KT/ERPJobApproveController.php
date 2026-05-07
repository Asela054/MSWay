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
        return view('ERP_KT.job_approve');
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
            ->select(
                'd.id',
                'jt.title as job_title',
                'm.machine_name as machine',
                'e.calling_name as employee',
                'd.approve_status',
                'c.name as customer_name',
                'idet.inquiry as inquiry',
                DB::raw('TIMESTAMPDIFF(HOUR, i.start_from, i.end_at) as reading_hours')
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
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rows   = $request->input('dataarray', []);
        $errors = [];

        foreach ($rows as $row) {
            try {
                DB::table('kt_job_details')
                    ->where('id', $row['cusid'])
                    ->update(['approve_status' => 1, 'updated_at' => Carbon::now()]);
            } catch (\Exception $e) {
                $errors[] = "Failed for customer ID {$row['cusid']}: " . $e->getMessage();
            }
        }

        if (count($errors) === 0) {
            return response()->json(['success' => 'All selected records approved successfully.']);
        }

        return response()->json([
            'success' => 'Approved with some issues.',
            'errors'  => $errors,
        ]);
    }
}
