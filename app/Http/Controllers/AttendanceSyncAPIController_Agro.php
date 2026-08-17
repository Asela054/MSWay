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

    // get attendance from Bio Foodsagro attendance server
    public function index(Request $request)
    {
        ini_set('max_execution_time', 3000);

        $payload = $request->json()->all();

        if (empty($payload) || empty($payload['user_attendance_summary'])) {
            return response()->json(['message' => 'No attendance data received'], 400);
        }

        $inserted = 0;

        foreach ($payload['user_attendance_summary'] as $user) {
            $full_emp_id = $user['user_id'];

            if (empty($user['records'])) {
                continue;
            }

            foreach ($user['records'] as $record) {
                if (empty($record['timestamp'])) {
                    continue;
                }

                $date = Carbon::parse($record['timestamp'])->format('Y-m-d');
                $time = Carbon::parse($record['timestamp'])->format('H:i:s');

                $this->attendancePolicyService->attendanceInsertcsv_txt(
                    $full_emp_id,
                    $date,
                    $time,
                    $date
                );

                $inserted++;
            }
        }

        return response()->json([
            'message' => 'Attendance synced successfully',
            'records_processed' => $inserted,
        ]);
    }
}