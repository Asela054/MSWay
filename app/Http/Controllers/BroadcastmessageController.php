<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Services\Opma_Sms_policyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateInterval;
use DateTime;

class BroadcastmessageController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $permission = $user->can('broadcast-message');
        if(!$permission){
            abort(403);
        }

        return view('Organization.broadcast_sms');
        
    }

    public function sendsms(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('broadcast-message-create')) {
            abort(403);
        }

        $companyId    = $request->input('company');
        $type         = $request->input('type');    
        $departmentId = $request->input('department');
        $employeeIds  = $request->input('employees', []);
        $message      = $request->input('message');

        $current_date_time = Carbon::now()->toDateTimeString();

       
        $employees = DB::table('employees')
            ->where('emp_company', $companyId)
            ->where('is_resigned', 0)
            ->where('deleted', 0);

        if ($type == 2) {
            $employees->where('emp_department', $departmentId);
        } elseif ($type == 3) {
            $employees->whereIn('emp_id', $employeeIds);
        }

        $recipients = $employees->get(['emp_id', 'emp_name_with_initial', 'emp_mobile']);

        if ($recipients->isEmpty()) {
            return response()->json([
                'message' => 'No employees found for the selected criteria.',
            ], 422);
        }
 
         // normalize each mobile to the 9-digit format eSMS expects
        $mobiles = $recipients->pluck('emp_mobile')->filter()->map(function ($mobile) {
            $mobile = preg_replace('/[^0-9]/', '', $mobile);

            if (strlen($mobile) == 10 && $mobile[0] == '0') {
                $mobile = substr($mobile, 1);
            } elseif (strlen($mobile) == 11 && substr($mobile, 0, 2) == '94') {
                $mobile = substr($mobile, 2);
            }

            return $mobile;})->values()->all();

        $smsService = new \App\Services\Opma_Sms_policyService();
        
        $smsResult  = $smsService->sendBulkSms($mobiles, $message);

        \Log::info('eSMS broadcast result', [
            'company_id' => $companyId,
            'type'       => $type,
            'count'      => count($mobiles),
            'result'     => $smsResult,
        ]);


            DB::table('broadcast_messages')->insert([
                'date'          => Carbon::now()->toDateString(),
                'type'          => $type,
                'department_id' => $type == 2 ? $departmentId : null,
                'employee_ids'  => $type == 3 ? implode(',', $employeeIds) : null,
                'message'       => $message,
                'sent_by'       => Auth::id(),
                'created_at'    => $current_date_time,
                'updated_at'    => $current_date_time,
            ]);

            return response()->json(['success' => 'Broadcast message Successfully Sent']);
    }


}
