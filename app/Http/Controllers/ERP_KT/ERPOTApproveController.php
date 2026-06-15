<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ERPOTApproveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('kt-ot-approve-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $remunerations = DB::table('remunerations')->select('*')->where('remuneration_type', 'Addition')->get();
        return view('ERP_KT.ot_approve', compact('remunerations'));
    }

    public function otapprovegenerate(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('kt-ot-approve-create');

        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $employee      = $request->get('employee');
        $from_date     = $request->get('from_date');
        $to_date       = $request->get('to_date');

        $query = DB::table('kt_shift_ot as d')
            ->leftJoin('employees as e', 'e.emp_id', '=', 'd.emp_id')
            ->select(
                'd.id',
                'e.emp_name_with_initial as employee',
                'e.id as emp_auto_id',
                'd.approve_status',
                'd.date',
                'd.in_time',
                'd.out_time',
                'd.ot_hours'
            )
            ->when($employee, function ($q) use ($employee) {
                return $q->where('d.emp_id', $employee);
            })
            ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                return $q->whereBetween('d.date', [$from_date, $to_date]);
            });

        $totalFiltered = $query->count();
        $data = $query->get();

        foreach ($data as $row) {
            if (empty($row->ot_hours) && !empty($row->in_time) && !empty($row->out_time)) {
                $in = \Carbon\Carbon::parse($row->in_time);
                $out = \Carbon\Carbon::parse($row->out_time);
                if ($out->lessThan($in)) {
                    $out->addDay();
                }
                $row->ot_hours = round($in->diffInMinutes($out) / 60, 2);
            }
        }

        return response()->json([
            'draw'            => intval($request->get('draw')),
            'recordsTotal'    => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }


    public function otapprovesubmit(Request $request)
    {
        $permission = \Auth::user()->can('kt-ot-approve-create');
        if (!$permission) {
            abort(403);
        }

        $dataarray = $request->input('dataarray');

        foreach ($dataarray as $row) {
            DB::table('kt_shift_ot')
                ->where('id', $row['cusid'])
                ->update(
                    [
                        'approve_status' => 1,
                        'ot_hours'       => $row['ot_hours'],
                        'updated_by'     => \Auth::user()->id,
                    ]
                );
        }

        return response()->json(['success' => 'OT successfully approved.']);
    }
}
