<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance;
use Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        while (($datalist = fgetcsv($file)) !== FALSE) {
            if ($firstRow) {
                $firstRow = false; 
                continue;
            }

            $attendances[] = [
                'emp_id' => $datalist[0],
                'date' => $datalist[1],
                'in_time' => $datalist[2],
                'out_time' => $datalist[3],
            ];
        }
        
        // Validate and insert data for each attendance
        foreach ($attendances as $attendanceData) {

            $rowValidator = Validator::make($attendanceData, [
                'emp_id' => 'required',
                'date' => 'required',
                'in_time' => 'required',
                'out_time' => 'required',
            ]);

            if ($rowValidator->fails()) {
                return response()->json(['errors' => $rowValidator->errors()->all()]);
            }

            $employees = \App\Employee::pluck('emp_id', 'emp_id')->toArray();
            $employeeId = $employees[$attendanceData['emp_id']] ?? null;

            if (!$employeeId) {
                return response()->json(['errors' => 'Invalid Empid:' . $attendanceData['emp_id']]);
            }

            $date = Carbon::parse($attendanceData['date'])->format('Y-m-d');
            if (!$date) {
                return response()->json(['errors' => 'Invalid date format']);
            }

            // Insert IN time
            Attendance::create([
                'emp_id' => $employeeId,
                'uid' => $employeeId,
                'state' => '1',
                'timestamp' => Carbon::parse($attendanceData['in_time'])->format('Y-m-d H:i:s'),
                'date' => $date,
                'approved' => '0',
                'type' => '255',
                'devicesno' => '-',
                'location' => '1',
            ]);

            // Insert OUT time
            Attendance::create([
                'emp_id' => $employeeId,
                'uid' => $employeeId,
                'state' => '1', 
                'timestamp' => Carbon::parse($attendanceData['out_time'])->format('Y-m-d H:i:s'),
                'date' => $date,
                'approved' => '0',
                'type' => '255',
                'devicesno' => '-',
                'location' => '1',
            ]);
        }

        return response()->json(['success' => 'Attendance records uploaded successfully.']);
    }
}
