<?php

namespace App\Http\Controllers;


use App\Services\AttendancePolicyService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class AttendanceSyncAPIController extends Controller
{
    protected $attendancePolicyService;

    public function __construct(AttendancePolicyService $attendancePolicyService)
    {

        $this->attendancePolicyService = $attendancePolicyService;
    }


      // get attendance from fingerprint
    public function index(Request $request)
    {
         ini_set('max_execution_time', 3000);
       
            $attendance = $request->json()->all();

            if (!empty($attendance)) {
                foreach ($attendance as $link) {
                $full_emp_id = $link['EmpId'];
                $newtimestamp = $link['AttTime'];

                $date = Carbon::parse($link['AttTime'])->format('Y-m-d');
                $time = Carbon::parse($link['AttTime'])->format('H:i:s');

                 $this->attendancePolicyService->attendanceInsertcsv_txt( $full_emp_id,  $date, $time, $date );
                
                }
            }
    }
}
