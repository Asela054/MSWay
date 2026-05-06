<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ERPInquiryApproveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('kt-inquiry-approve-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        return view('ERP_KT.inquiry_approve'); 
    }

    public function inquiryapprovegenerate(Request $request){
        $user = Auth::user();
        $permission = $user->can('kt-inquiry-approve-create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $customer_id = $request->get('customer_id');
        $from_date     = $request->get('from_date');
        $to_date       = $request->get('to_date');

        $query = DB::table('kt_inquiry_details as d')
            ->join('kt_inquiries as i', 'i.id', '=', 'd.inquiry_id')
            ->leftJoin('kt_customer as c', 'c.id', '=', 'i.customer_id')
            ->select(
                'd.id        as cus_auto_id',
                'i.id        as cus_id',
                'c.name      as customer_name',
                'i.date      as date_count',
                'd.inquiry',
                'd.quotation',
                'd.approve_status'
            )
            ->when($customer_id, function($q) use ($customer_id){
                return $q->where('i.customer_id', $customer_id);
            })
            ->when($from_date && $to_date, function($q) use ($from_date, $to_date) {
                 return $q->whereBetween('i.date', [$from_date, $to_date]);
            });

        $total = $query->count();
        $data  = $query->get();

        return response()->json([
            'draw'            => intval($request->get('draw')),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ]);
    }


    public function approveinquiry(Request $request)
    {
        $permission = \Auth::user()->can('kt-inquiry-approve-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rows   = $request->input('dataarray', []);
        $errors = [];

        foreach ($rows as $row) {
            try {
                DB::table('kt_inquiry_details')
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
