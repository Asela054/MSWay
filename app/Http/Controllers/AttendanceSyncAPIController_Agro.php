<?php

namespace App\Http\Controllers;

use App\Services\AttendancePolicyService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class AttendanceSyncAPIController_Agro extends Controller
{
    protected $attendancePolicyService;
    protected $httpClient;

    // Bio Foodsagro attendance server settings
    protected $baseUrl = 'https://erp.biofoodsagro.com/api/atd';
    protected $apiKey  = 'qjMIVHQuXzte3C2wGQ6mZ0-2bGRV2faugySiAdS_JVg';

    public function __construct(AttendancePolicyService $attendancePolicyService)
    {
        $this->attendancePolicyService = $attendancePolicyService;
        $this->httpClient = new Client();
    }

    public function index(Request $request)
    {
        ini_set('max_execution_time', 3000);

        $syncDate = $request->query('date', Carbon::now()->format('Y-m-d'));

        // 1. Get list of devices
        try {
            $devicesResponse = $this->httpClient->get("{$this->baseUrl}/devices", [
                'headers' => ['X-API-Key' => $this->apiKey],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch device list',
                'error'   => $e->getMessage(),
            ], 500);
        }

        $devicesData = json_decode($devicesResponse->getBody()->getContents(), true);
        $devices = $devicesData['devices'] ?? [];

        if (empty($devices)) {
            return response()->json(['message' => 'No devices found'], 404);
        }

        $totalInserted = 0;
        $deviceResults = [];

        // 2. Loop each device, get attendance for the sync date
        foreach ($devices as $device) {
            $deviceName = $device['name'];
            $encodedDevice = rawurlencode($deviceName);

            try {
                $attendanceResponse = $this->httpClient->get("{$this->baseUrl}/attendance/{$encodedDevice}/{$syncDate}", [
                    'headers' => ['X-API-Key' => $this->apiKey],
                ]);
            } catch (\Exception $e) {
                \Log::warning("Agro attendance sync failed for device: {$deviceName}, error: " . $e->getMessage());
                $deviceResults[$deviceName] = 'failed';
                continue;
            }

            $payload = json_decode($attendanceResponse->getBody()->getContents(), true);

            if (empty($payload['attendance'])) {
                $deviceResults[$deviceName] = 0;
                continue;
            }

            $deviceInserted = 0;

            // 3. Loop each user, then each punch record
           foreach ($payload['attendance'] as $record) {
                if (empty($record['timestamp']) || empty($record['user_id'])) {
                    continue;
                }

                $full_emp_id = $record['user_id'];

                $date = Carbon::parse($record['timestamp'])->format('Y-m-d');
                $time = Carbon::parse($record['timestamp'])->format('H:i:s');

                $this->attendancePolicyService->attendanceInsertcsv_txt(
                    $full_emp_id,
                    $date,
                    $time,
                    $date
                );

                $deviceInserted++;
                $totalInserted++;
            }

            $deviceResults[$deviceName] = $deviceInserted;
        }

        return response()->json([
            'message'          => 'Attendance synced successfully',
            'sync_date'        => $syncDate,
            'total_processed'  => $totalInserted,
            'device_breakdown' => $deviceResults,
        ]);
    }
}