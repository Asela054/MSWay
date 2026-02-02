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
        $rowNumber = 1; // Track row number for error reporting

        while (($datalist = fgetcsv($file)) !== FALSE) {
            if ($firstRow) {
                $firstRow = false; 
                $rowNumber++;
                continue;
            }

            $attendances[] = [
                'row' => $rowNumber, // Store row number for error reporting
                'emp_id' => $datalist[0],
                'date' => $datalist[1],
                'in_time' => $datalist[2],
                'out_time' => $datalist[3],
            ];
            
            $rowNumber++;
        }
        
        fclose($file);

        // Validate and insert data for each attendance
        foreach ($attendances as $attendanceData) {
            $rowNumber = $attendanceData['row']; // Get row number
            
            $rowValidator = Validator::make($attendanceData, [
                'emp_id' => 'required',
                'date' => ['required', 'regex:/^\d{1,2}\/\d{1,2}\/\d{4}$/'], // mm/dd/yyyy or m/d/yyyy format
                'in_time' => ['required', 'regex:/^\d{1,2}:\d{2}:\d{2}$/'], // h:mm:ss or hh:mm:ss format
                'out_time' => ['required', 'regex:/^\d{1,2}:\d{2}:\d{2}$/'], // h:mm:ss or hh:mm:ss format
            ], [
                'date.regex' => "Row {$rowNumber}: Date must be in format MM/DD/YYYY (e.g., 12/26/2025)",
                'in_time.regex' => "Row {$rowNumber}: In time must be in format H:MM:SS (e.g., 5:30:00 or 09:30:00)",
                'out_time.regex' => "Row {$rowNumber}: Out time must be in format H:MM:SS (e.g., 17:30:00 or 09:30:00)",
            ]);

            if ($rowValidator->fails()) {
                return response()->json(['errors' => $rowValidator->errors()->all()]);
            }

            $employees = \App\Employee::pluck('emp_id', 'emp_id')->toArray();
            $employeeId = $employees[$attendanceData['emp_id']] ?? null;

            $employeeLocation = \App\Employee::where('emp_id', $attendanceData['emp_id'])
                                ->value('emp_location');

            if (!$employeeId) {
                return response()->json(['errors' => "Row {$rowNumber}: Invalid Employee ID: " . $attendanceData['emp_id']]);
            }

             if (!$employeeLocation) {
                return response()->json(['errors' => "Row {$rowNumber}: Invalid Employee Location : " . $attendanceData['emp_id']]);
            }
      
                $date = Carbon::createFromFormat('m/d/Y', $attendanceData['date'])->format('Y-m-d');

                // Parse in_time
                $inTimeParts = explode(':', $attendanceData['in_time']);
                if (count($inTimeParts) !== 3) {
                    throw new \Exception("Invalid time format");
                }
                
                $inDateTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                    $date . ' ' . sprintf('%02d:%02d:%02d', 
                        $inTimeParts[0], 
                        $inTimeParts[1], 
                        $inTimeParts[2]
                    )
                );
                
                // Parse out_time
                $outTimeParts = explode(':', $attendanceData['out_time']);
                if (count($outTimeParts) !== 3) {
                    throw new \Exception("Invalid time format");
                }
                
                $outDateTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                    $date . ' ' . sprintf('%02d:%02d:%02d', 
                        $outTimeParts[0], 
                        $outTimeParts[1], 
                        $outTimeParts[2]
                    )
                );
                

            // Validate that times are valid
            if (!checkdate(
                Carbon::createFromFormat('m/d/Y', $attendanceData['date'])->format('m'),
                Carbon::createFromFormat('m/d/Y', $attendanceData['date'])->format('d'),
                Carbon::createFromFormat('m/d/Y', $attendanceData['date'])->format('Y')
            )) {
                return response()->json(['errors' => "Row {$rowNumber}: Invalid date: " . $attendanceData['date']]);
            }


            // Handle overnight shifts
            if ($outDateTime->lessThan($inDateTime)) {
                $outDateTime->addDay();
            }

            // Insert IN time
            Attendance::create([
                'emp_id' => $employeeId,
                'uid' => $employeeId,
                'state' => '1',
                'timestamp' => $inDateTime->format('Y-m-d H:i:s'),
                'date' => $date,
                'approved' => '0',
                'type' => '255',
                'devicesno' => '-',
                'location' => $employeeLocation,
            ]);

            // Insert OUT time
            Attendance::create([
                'emp_id' => $employeeId,
                'uid' => $employeeId,
                'state' => '1', 
                'timestamp' => $outDateTime->format('Y-m-d H:i:s'),
                'date' => $outDateTime->format('Y-m-d'),
                'approved' => '0',
                'type' => '255',
                'devicesno' => '-',
                'location' => $employeeLocation,
            ]);
        }

        return response()->json(['success' => 'Attendance records uploaded successfully.']);
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
        
    //     fclose($file);

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

    //            // Combine date with in_time
    //             $inDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $attendanceData['in_time']);
                
    //             // Combine date with out_time
    //             $outDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $attendanceData['out_time']);

    //              if ($outDateTime->lessThan($inDateTime)) {
    //                     $outDateTime->addDay();
    //                 }

    //         // Insert IN time
    //         Attendance::create([
    //             'emp_id' => $employeeId,
    //             'uid' => $employeeId,
    //             'state' => '1',
    //             'timestamp' => $inDateTime->format('Y-m-d H:i:s'),
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
    //             'timestamp' => $outDateTime->format('Y-m-d H:i:s'),
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
