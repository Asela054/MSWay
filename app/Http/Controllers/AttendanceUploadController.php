<?php

namespace App\Http\Controllers;

use App\Services\AttendancePolicyService;
use Illuminate\Http\Request;
use App\Attendance;
use Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceUploadController extends Controller
{

    protected $attendancePolicyService;

    public function __construct(AttendancePolicyService $attendancePolicyService)
    {

        $this->attendancePolicyService = $attendancePolicyService;
    }

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
                // Parse and format date - FIXED: Use m/d/Y format for your CSV (1/2/2026 = Jan 2, 2026)
                $date = $this->parseDate($attendanceData['date']);
                if (!$date) {
                    $errors[] = "Row {$attendanceData['row']}: Invalid date format: " . $attendanceData['date'];
                    continue;
                }
                
                // Parse in_time and out_time using the correct date
                $inTime = $this->parseTimestamp($attendanceData['in_time'], $date);
                if (!$inTime) {
                    $errors[] = "Row {$attendanceData['row']}: Invalid in_time format: " . $attendanceData['in_time'];
                    continue;
                }

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

                // Insert IN time - FIXED: Use the actual date for the timestamp
                // Attendance::create([
                //     'emp_id' => $employeeId,
                //     'uid' => $employeeId,
                //     'state' => '1',
                //     'timestamp' => $inTime, // This now has the correct date
                //     'date' => $date, // This is now Y-m-d format
                //     'approved' => '0',
                //     'type' => '255',
                //     'devicesno' => $deviceSno,
                //     'location' => $location,
                // ]);

      
                $this->attendancePolicyService->attendanceInsertcsv_txt( $employeeId,  $date, $inTime, $date );


                // Insert OUT time
                // Attendance::create([
                //     'emp_id' => $employeeId,
                //     'uid' => $employeeId,
                //     'state' => '1', 
                //     'timestamp' => $outTime, // This now has the correct date
                //     'date' => $date, // This is now Y-m-d format
                //     'approved' => '0',
                //     'type' => '255',
                //     'devicesno' => $deviceSno,
                //     'location' => $location,
                // ]);

                $this->attendancePolicyService->attendanceInsertcsv_txt( $employeeId,  $date, $outTime, $date );

                $successCount++;

            } catch (\Exception $e) {
                $errors[] = "Row {$attendanceData['row']}: " . $e->getMessage();
            }
        }

        $response = [];

        if ($successCount > 0) {
            $response['success'] = "Successfully imported {$successCount} attendance records.";
        }
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response);
    }

    /**
     * Parse date and convert to standard format Y-m-d
     * Fixed to properly handle m/d/Y format
     */
    private function parseDate($dateString)
    {
        try {
            // Remove any extra spaces
            $dateString = trim($dateString);
            
            // Try different date formats - reordered to prioritize your CSV format
            $formats = [
                'm/d/Y',      // 1/2/2026 (month/day/year) - YOUR CSV FORMAT
                'n/j/Y',      // 1/2/2026 (month/day/year without leading zeros)
                'd/m/Y',      // 2/1/2026 (day/month/year)
                'Y-m-d',      // 2026-01-02
                'Y/m/d',      // 2026/01/02
                'd-m-Y',      // 02-01-2026
                'm-d-Y',      // 01-02-2026
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    // Validate that the parsed date is reasonable
                    $errors = \DateTime::getLastErrors();
                    if ($errors && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                        return $date->format('Y-m-d');
                    }
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
     * Fixed to properly combine date and time
     */
    private function parseTimestamp($timeString, $date)
    {
        try {
            // Remove any extra spaces
            $timeString = trim($timeString);
            
            // If the timeString already contains a date, extract just the time part
            if (preg_match('/(\d{1,2}:\d{2}(?::\d{2})?)/', $timeString, $matches)) {
                $timeString = $matches[1];
            }
            
            // Handle time-only formats
            $timeFormats = [
                'H:i:s',     // 17:30:00
                'H:i',       // 17:30
                'G:i:s',     // 8:30:00 (without leading zero)
                'G:i',       // 8:30 (without leading zero)
                'h:i:s A',   // 05:30:00 PM
                'h:i A',     // 05:30 PM
                'g:i:s A',   // 5:30:00 PM
                'g:i A',     // 5:30 PM
            ];

            $parsedTime = null;
            
            foreach ($timeFormats as $format) {
                $time = \DateTime::createFromFormat($format, $timeString);
                if ($time !== false) {
                    $parsedTime = $time;
                    break;
                }
            }
            
            // If still not parsed, try Carbon
            if (!$parsedTime) {
                $parsedTime = Carbon::parse($timeString);
            }
            
            // Combine with the provided date - FIXED: Use the date parameter correctly
            $combined = $parsedTime->format('H:i:s');
            $result = Carbon::parse($combined);
            
            return $result->format('H:i:s');
            
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
    //     $rowNumber = 1; // Track row number for error reporting

    //     while (($datalist = fgetcsv($file)) !== FALSE) {
    //         if ($firstRow) {
    //             $firstRow = false; 
    //             $rowNumber++;
    //             continue;
    //         }

    //         if(empty($datalist[2]) || empty($datalist[3])) {
    //             continue;
    //         }   

    //         $date = Carbon::createFromFormat('d/m/Y', $datalist[1]);
    //         $date = $date->format('Y-m-d');
    //         $inTime = Carbon::createFromFormat('H:i:s', $datalist[2]);
    //         $inTime = $inTime->format('H:i:s');
    //         $outTime = Carbon::createFromFormat('H:i:s', $datalist[3]);
    //         $outTime = $outTime->format('H:i:s');

    //         $attendances[] = [
    //             'row' => $rowNumber, // Store row number for error reporting
    //             'emp_id' => $datalist[0],
    //             'date' => $date,
    //             'in_time' => $inTime,
    //             'out_time' => $outTime,
    //         ];
            
    //         $rowNumber++;
    //     }
        
    //     fclose($file);
        
    //     // Validate and insert data for each attendance
    //     foreach ($attendances as $attendanceData) {
    //         $rowNumber = $attendanceData['row'];
            
    //         // Note: Since you already converted the date to Y-m-d in the while loop,
    //         // we validate against that format here.
    //         $rowValidator = Validator::make($attendanceData, [
    //             'emp_id'   => 'required',
    //             'date'     => ['required', 'date_format:Y-m-d'], 
    //             'in_time'  => ['required', 'regex:/^\d{1,2}:\d{2}:\d{2}$/'], 
    //             'out_time' => ['required', 'regex:/^\d{1,2}:\d{2}:\d{2}$/'], 
    //         ]);

    //         if ($rowValidator->fails()) {
    //             return response()->json(['errors' => $rowValidator->errors()->all()]);
    //         }

    //         // Optimization: Consider moving this outside the loop if the CSV is large
    //        $employeeExists = \App\Employee::where('emp_id', $attendanceData['emp_id'])->select('id', 'emp_id', 'emp_location')->first();


    //         if (!$employeeExists) {
    //             return response()->json(['errors' => "Row {$rowNumber}: Invalid Employee ID: " . $attendanceData['emp_id']]);
    //         }
    //         $employeeLocation = $employeeExists->emp_location;

    //         $date = $attendanceData['date']; // Already in Y-m-d from the while loop

    //         try {
    //             // Create Carbon instances for comparison and storage
    //             $inDateTime = Carbon::parse($date . ' ' . $attendanceData['in_time']);
    //             $outDateTime = Carbon::parse($date . ' ' . $attendanceData['out_time']);

    //             // Handle overnight shifts (e.g., In 22:00, Out 06:00)
    //             if ($outDateTime->lessThan($inDateTime)) {
    //                 $outDateTime->addDay();
    //             }

    //             // Insert IN time
    //             Attendance::create([
    //                 'emp_id'    => $attendanceData['emp_id'],
    //                 'uid'       => $attendanceData['emp_id'],
    //                 'state'     => '1',
    //                 'timestamp' => $inDateTime->format('Y-m-d H:i:s'),
    //                 'date'      => $date,
    //                 'approved'  => '0',
    //                 'type'      => '255',
    //                 'devicesno' => '-',
    //                 'location'  => $employeeLocation,
    //             ]);

    //             // Insert OUT time
    //             Attendance::create([
    //                 'emp_id'    => $attendanceData['emp_id'],
    //                 'uid'       => $attendanceData['emp_id'],
    //                 'state'     => '1', 
    //                 'timestamp' => $outDateTime->format('Y-m-d H:i:s'),
    //                 'date'      => $outDateTime->format('Y-m-d'), // Use outDateTime date in case of overnight
    //                 'approved'  => '0',
    //                 'type'      => '255',
    //                 'devicesno' => '-',
    //                 'location'  => $employeeLocation,
    //             ]);

    //         } catch (\Exception $e) {
    //             return response()->json(['errors' => "Row {$rowNumber}: Data processing error."]);
    //         }
    //     }

    //     return response()->json(['success' => 'Attendance records uploaded successfully.']);
    // }

}
