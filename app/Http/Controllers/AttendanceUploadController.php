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

            if(empty($datalist[2]) || empty($datalist[3])) {
                continue;
            }   

            $date = Carbon::createFromFormat('d/m/Y', $datalist[1]);
            $date = $date->format('Y-m-d');
            $inTime = Carbon::createFromFormat('H:i:s', $datalist[2]);
            $inTime = $inTime->format('H:i:s');
            $outTime = Carbon::createFromFormat('H:i:s', $datalist[3]);
            $outTime = $outTime->format('H:i:s');

            $attendances[] = [
                'row' => $rowNumber, // Store row number for error reporting
                'emp_id' => $datalist[0],
                'date' => $date,
                'in_time' => $inTime,
                'out_time' => $outTime,
            ];
            
            $rowNumber++;
        }
        
        fclose($file);
        
        // Validate and insert data for each attendance
        foreach ($attendances as $attendanceData) {
            $rowNumber = $attendanceData['row'];
            
            // Note: Since you already converted the date to Y-m-d in the while loop,
            // we validate against that format here.
            $rowValidator = Validator::make($attendanceData, [
                'emp_id'   => 'required',
                'date'     => ['required', 'date_format:Y-m-d'], 
                'in_time'  => ['required', 'regex:/^\d{1,2}:\d{2}:\d{2}$/'], 
                'out_time' => ['required', 'regex:/^\d{1,2}:\d{2}:\d{2}$/'], 
            ]);

            if ($rowValidator->fails()) {
                return response()->json(['errors' => $rowValidator->errors()->all()]);
            }

            // Optimization: Consider moving this outside the loop if the CSV is large
            $employeeExists = \App\Employee::where('emp_id', $attendanceData['emp_id'])->exists();

            if (!$employeeExists) {
                return response()->json(['errors' => "Row {$rowNumber}: Invalid Employee ID: " . $attendanceData['emp_id']]);
            }

            $date = $attendanceData['date']; // Already in Y-m-d from the while loop

            try {
                // Create Carbon instances for comparison and storage
                $inDateTime = Carbon::parse($date . ' ' . $attendanceData['in_time']);
                $outDateTime = Carbon::parse($date . ' ' . $attendanceData['out_time']);

                // Handle overnight shifts (e.g., In 22:00, Out 06:00)
                if ($outDateTime->lessThan($inDateTime)) {
                    $outDateTime->addDay();
                }

                // Insert IN time
                Attendance::create([
                    'emp_id'    => $attendanceData['emp_id'],
                    'uid'       => $attendanceData['emp_id'],
                    'state'     => '1',
                    'timestamp' => $inDateTime->format('Y-m-d H:i:s'),
                    'date'      => $date,
                    'approved'  => '0',
                    'type'      => '255',
                    'devicesno' => '-',
                    'location'  => '1',
                ]);

                // Insert OUT time
                Attendance::create([
                    'emp_id'    => $attendanceData['emp_id'],
                    'uid'       => $attendanceData['emp_id'],
                    'state'     => '1', 
                    'timestamp' => $outDateTime->format('Y-m-d H:i:s'),
                    'date'      => $outDateTime->format('Y-m-d'), // Use outDateTime date in case of overnight
                    'approved'  => '0',
                    'type'      => '255',
                    'devicesno' => '-',
                    'location'  => '1',
                ]);

            } catch (\Exception $e) {
                return response()->json(['errors' => "Row {$rowNumber}: Data processing error."]);
            }
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
