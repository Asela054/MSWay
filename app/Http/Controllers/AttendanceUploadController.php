<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance;
use Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceUploadController extends Controller
{

    public function importCSV(Request $request)
{
    $permission = Auth::user()->can('attendance-create');
    if (!$permission) {
        return response()->json(['errors' => 'UnAuthorized'], 401);
    }

    $validator = Validator::make($request->all(), [
        'import_csv' => 'required|file|mimes:csv,txt',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
    }

    $filename = $request->file('import_csv');
    $file = fopen($filename, 'r');

    $attendances = [];
    $firstRow = true;
    $rowNumber = 1;

    while (($datalist = fgetcsv($file)) !== FALSE) {
        if ($firstRow) {
            $firstRow = false; 
            $rowNumber++;
            continue;
        }

        $attendances[] = [
            'row' => $rowNumber,
            'emp_id' => $datalist[0],
            'date' => $datalist[1],
            'in_time' => $datalist[2],
            'out_time' => $datalist[3],
        ];
        $rowNumber++;
    }
    
    fclose($file);

    $errors = [];
    $successCount = 0;

    // Validate and insert data for each attendance
    foreach ($attendances as $attendanceData) {

        $rowValidator = Validator::make($attendanceData, [
            'emp_id' => 'required',
            'date' => 'required',
            'in_time' => 'required',
            'out_time' => 'required',
        ]);

        if ($rowValidator->fails()) {
            $errors[] = "Row {$attendanceData['row']}: " . implode(', ', $rowValidator->errors()->all());
            continue;
        }

        $employees = \App\Employee::pluck('emp_id', 'emp_id')->toArray();
        $employeeId = $employees[$attendanceData['emp_id']] ?? null;


        if (!$employeeId) {
            $errors[] = "Row {$attendanceData['row']}: Invalid Employee ID: " . $attendanceData['emp_id'];
            continue;
        }

        try {
            // Parse and format date
            $date = $this->parseDate($attendanceData['date']);
            if (!$date) {
                $errors[] = "Row {$attendanceData['row']}: Invalid date format: " . $attendanceData['date'];
                continue;
            }

            
        
            // Parse and format in_time
            $inTime = $this->parseTimestamp($attendanceData['in_time'], $date);
            if (!$inTime) {
                $errors[] = "Row {$attendanceData['row']}: Invalid in_time format: " . $attendanceData['in_time'];
                continue;
            }

           
            // Parse and format out_time
            $outTime = $this->parseTimestamp($attendanceData['out_time'], $date);
            if (!$outTime) {
                $errors[] = "Row {$attendanceData['row']}: Invalid out_time format: " . $attendanceData['out_time'];
                continue;
            }
            
            // Get employee device info
            $employee = DB::table('employees')
                ->join('branches', 'employees.emp_location', '=', 'branches.id')
                ->join('fingerprint_devices', 'branches.id', '=', 'fingerprint_devices.location')
                ->select('fingerprint_devices.sno', 'fingerprint_devices.location')
                ->groupBy('fingerprint_devices.location')
                ->where('employees.emp_id', $employeeId)
                ->first();

            $deviceSno = $employee->sno ?? '-';
            $location = $employee->location ?? '1';

            // Insert IN time
            Attendance::create([
                'emp_id' => $employeeId,
                'uid' => $employeeId,
                'state' => '1',
                'timestamp' => $inTime,
                'date' => $date,
                'approved' => '0',
                'type' => '255',
                'devicesno' => $deviceSno,
                'location' => $location,
            ]);

            // Insert OUT time
            Attendance::create([
                'emp_id' => $employeeId,
                'uid' => $employeeId,
                'state' => '1', 
                'timestamp' => $outTime,
                'date' => $date,
                'approved' => '0',
                'type' => '255',
                'devicesno' => $deviceSno,
                'location' => $location,
            ]);

            $successCount++;

        } catch (\Exception $e) {
            $errors[] = "Row {$attendanceData['row']}: " . $e->getMessage();
        }
    }

    $response = [];

  

    if ($successCount > 0) {
        $response['success'] = "Successfully imported attendance records.";
    }
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    return response()->json($response);
}

/**
 * Parse date and convert to standard format Y-m-d
 */
private function parseDate($dateString)
{
    try {
        // Remove any extra spaces
        $dateString = trim($dateString);
        
        // Try different date formats
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'd-m-Y',
            'm-d-Y',
            'Y/m/d',
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        // If none of the formats work, try Carbon parse
        return Carbon::parse($dateString)->format('Y-m-d');
        
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Parse timestamp and convert to standard format Y-m-d H:i:s
 * Handles both full timestamps and time-only values
 */
/**
 * Parse timestamp and convert to standard format Y-m-d H:i:s
 * Handles both full timestamps and time-only values
 */
private function parseTimestamp($timeString, $date)
{
    try {
        // Remove any extra spaces
        $timeString = trim($timeString);
        
        // Handle the specific format with extra slash: "9/26/2025/ 09:51"
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})\/\s*(\d{1,2}:\d{2}(?::\d{2})?)$/', $timeString, $matches)) {
            $month = $matches[1];
            $day = $matches[2];
            $year = $matches[3];
            $time = $matches[4];
            
            // Create proper timestamp
            $combined = "{$year}-{$month}-{$day} {$time}";
            return Carbon::parse($combined)->format('Y-m-d H:i:s');
        }
        
        // If it's already a full timestamp with date
        if (strtotime($timeString) !== false) {
            $fullTimestamp = Carbon::parse($timeString);
            // Check if it has a date part
            if ($fullTimestamp->format('Y-m-d') != '1970-01-01') {
                return $fullTimestamp->format('Y-m-d H:i:s');
            }
        }

        // Handle time-only formats
        $timeFormats = [
            'H:i:s',
            'H:i',
            'h:i:s A',
            'h:i A',
            'g:i:s A',
            'g:i A',
        ];

        foreach ($timeFormats as $format) {
            $time = \DateTime::createFromFormat($format, $timeString);
            if ($time !== false) {
                // Combine with the provided date
                $combined = $date . ' ' . $time->format('H:i:s');
                return Carbon::parse($combined)->format('Y-m-d H:i:s');
            }
        }

        // Try Carbon parse for time
        $timeOnly = Carbon::parse($timeString);
        $combined = $date . ' ' . $timeOnly->format('H:i:s');
        return Carbon::parse($combined)->format('Y-m-d H:i:s');
        
    } catch (\Exception $e) {
        return null;
    }
}


    // public function importCSV(Request $request)
    // {

    //     $permission = Auth::user()->can('attendance-create');
    //     if (!$permission) {
    //         return response()->json(['errors' => 'UnAuthorized'], 401);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'import_csv' => 'required|file|mimes:csv,txt',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()->all()]);
    //     }
    
    //     $filename = $request->file('import_csv');
    //     $file = fopen($filename, 'r');

    //     $attendances = [];
    //     $firstRow = true;

    //     while (($datalist = fgetcsv($file)) !== FALSE) {
    //         if ($firstRow) {
    //             $firstRow = false; 
    //             continue;
    //         }

    //         $attendances[] = [
    //             'emp_id' => $datalist[0],
    //             'date' => $datalist[1],
    //             'in_time' => $datalist[2],
    //             'out_time' => $datalist[3],
    //         ];
    //     }
        
    //     // Validate and insert data for each attendance
    //     foreach ($attendances as $attendanceData) {

    //         $rowValidator = Validator::make($attendanceData, [
    //             'emp_id' => 'required',
    //             'date' => 'required',
    //             'in_time' => 'required',
    //             'out_time' => 'required',
    //         ]);

    //         if ($rowValidator->fails()) {
    //             return response()->json(['errors' => $rowValidator->errors()->all()]);
    //         }

    //         $employees = \App\Employee::pluck('emp_id', 'emp_id')->toArray();
    //         $employeeId = $employees[$attendanceData['emp_id']] ?? null;

    //         if (!$employeeId) {
    //             return response()->json(['errors' => 'Invalid Empid:' . $attendanceData['emp_id']]);
    //         }

    //         $date = Carbon::parse($attendanceData['date'])->format('Y-m-d');
    //         if (!$date) {
    //             return response()->json(['errors' => 'Invalid date format']);
    //         }

    //         // Insert IN time
    //         Attendance::create([
    //             'emp_id' => $employeeId,
    //             'uid' => $employeeId,
    //             'state' => '1',
    //             'timestamp' => Carbon::parse($attendanceData['in_time'])->format('Y-m-d H:i:s'),
    //             'date' => $date,
    //             'approved' => '0',
    //             'type' => '255',
    //             'devicesno' => '-',
    //             'location' => '1',
    //         ]);

    //         // Insert OUT time
    //         Attendance::create([
    //             'emp_id' => $employeeId,
    //             'uid' => $employeeId,
    //             'state' => '1', 
    //             'timestamp' => Carbon::parse($attendanceData['out_time'])->format('Y-m-d H:i:s'),
    //             'date' => $date,
    //             'approved' => '0',
    //             'type' => '255',
    //             'devicesno' => '-',
    //             'location' => '1',
    //         ]);
    //     }

    //     return response()->json(['success' => 'Attendance records uploaded successfully.']);
    // }
}
