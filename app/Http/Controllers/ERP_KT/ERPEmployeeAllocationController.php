<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use App\ERP_KT\EmployeeAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;

class ERPEmployeeAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('kt-employee-allocation-list');
        if (!$permission) {
            abort(403);
        }

        $shifts = DB::table('shift_types')->where('deleted', 0)->orderBy('id', 'asc')->get();

        return view('ERP_KT.employee_allocation', compact('shifts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('kt-employee-allocation-create');
        if (!$permission) {
            return response()->json(['errors' => ['Unauthorized']]);
        }

        $tableData = $request->input('tableData');
        $shift     = $request->input('shift');
        $datefrom  = $request->input('datefrom');

        if (empty($tableData) || count($tableData) === 0) {
            return response()->json(['errors' => ['No employee data provided.']]);
        }

        if (empty($shift)) {
            return response()->json(['errors' => ['Shift is required.']]);
        }

        if (empty($datefrom)) {
            return response()->json(['errors' => ['Date is required.']]);
        }

        DB::beginTransaction();
        try {
            foreach ($tableData as $row) {

                $emp_id   = isset($row['col_1']) ? trim($row['col_1']) : null;
                $in_time  = isset($row['col_3']) ? trim($row['col_3']) : null;
                $out_time = isset($row['col_4']) ? trim($row['col_4']) : null;

                if (empty($emp_id)) continue;

                EmployeeAllocation::create([
                    'shift_id' => $shift,
                    'emp_id'   => $emp_id,
                    'date'     => $datefrom,
                    'in_time'  => !empty($in_time)  ? $in_time  : null,
                    'out_time' => !empty($out_time) ? $out_time : null,
                    'ot_hours' => 0,
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'Employee Allocation saved successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => ['Failed: ' . $e->getMessage()]]);
        }
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('kt-employee-allocation-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id   = $request->input('id');
        $data = EmployeeAllocation::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Record Deleted Successfully.']);
    }

    // Validate Date field
    private function parseDate($dateString)
    {
        try {
            $dateString = trim($dateString);
            if (empty($dateString)) {
                return null;
            }

            $formats = [
                'm/d/Y',
                'n/j/Y',
                'd/m/Y',
                'Y-m-d',
                'Y/m/d',
                'd-m-Y',
                'm-d-Y',
                'Y.m.d',
                'd.m.Y',
                'm.d.Y',
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    $errors = \DateTime::getLastErrors();
                    if ($errors && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                        return $date->format('Y-m-d');
                    }
                }
            }

            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    // Validate Time Field
    private function parseTime($timeString)
    {
        try {
            $timeString = trim($timeString);
            if (empty($timeString)) {
                return null;
            }

            if (preg_match('/^\d{1,2}$/', $timeString)) {
                $h = intval($timeString);
                if ($h < 0 || $h > 23) return null;
                return str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
            }

            $timeString = str_replace(['.', '-'], ':', $timeString);

            $formats = ['H:i:s', 'H:i', 'h:i:s A', 'h:i A', 'H'];

            foreach ($formats as $format) {
                $dt = \DateTime::createFromFormat($format, $timeString);
                if ($dt !== false) {
                    $errors = \DateTime::getLastErrors();
                    if ($errors && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                        return $dt->format('H:i:s');
                    }
                }
            }

            if (preg_match('/^(\d{1,2}):(\d{1,2})(?::(\d{1,2}))?$/', $timeString, $matches)) {
                $hours = intval($matches[1]);
                $minutes = intval($matches[2]);
                if ($hours > 23 || $minutes > 59) return null;
                return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' .
                    str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' .
                    (isset($matches[3]) ? str_pad($matches[3], 2, '0', STR_PAD_LEFT) : '00');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // CSV Upload function
    public function upload_csv(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('kt-employee-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, [
            'csv_file_u' => 'required|file|mimes:csv,txt|max:2048',
            'csv_shift'  => 'required',
        ]);

        $shift_id = $request->input('csv_shift');
        $file     = $request->file('csv_file_u');

        try {
            $fileContents = file($file->getPathname());
            array_shift($fileContents); // remove header row

            $errors       = [];
            $successCount = 0;
            $lineNumber   = 2;

            DB::beginTransaction();

            foreach ($fileContents as $line) {
                $line = trim($line);
                if (empty($line)) {
                    $lineNumber++;
                    continue;
                }

                $data = str_getcsv($line);

                // CSV columns: emp_id, date, in_date, in_time, out_date, out_time
                if (count($data) < 4 || empty(trim($data[0]))) {
                    $errors[] = "Line {$lineNumber}: Missing required fields (emp_id, date, in_date, in_time)";
                    $lineNumber++;
                    continue;
                }

                $emp_id   = trim($data[0]);
                $date_raw = isset($data[1]) ? trim($data[1]) : '';

                // CSV column order: emp_id, date, in_date, in_time, out_date , out_time
                $in_date_raw  = isset($data[2]) ? trim($data[2]) : '';
                $in_time_raw  = isset($data[3]) ? trim($data[3]) : '';
                $out_date_raw = isset($data[4]) ? trim($data[4]) : '';
                $out_time_raw = isset($data[5]) ? trim($data[5]) : '';

                if (empty($emp_id)) {
                    $errors[] = "Line {$lineNumber}: Employee ID is missing.";
                    $lineNumber++;
                    continue;
                }

                $emp = DB::table('employees')->where('emp_id', $emp_id)->where('deleted', 0)->first();
                if (!$emp) {
                    $errors[] = "Line {$lineNumber}: Employee ID '{$emp_id}' not found or inactive.";
                    $lineNumber++;
                    continue;
                }

                $date = $this->parseDate($date_raw);
                if (empty($date)) {
                    $errors[] = "Line {$lineNumber}: Date '{$date_raw}' is invalid.";
                    $lineNumber++;
                    continue;
                }

                $in_date = !empty($in_date_raw) ? $this->parseDate($in_date_raw) : null;
                if (!empty($in_date_raw) && empty($in_date)) {
                    $errors[] = "Line {$lineNumber}: In Date '{$in_date_raw}' is invalid.";
                }

                $in_time = !empty($in_time_raw) ? $this->parseTime($in_time_raw) : null;
                if (!empty($in_time_raw) && empty($in_time)) {
                    $errors[] = "Line {$lineNumber}: In Time '{$in_time_raw}' is invalid (use format 13:00:00).";
                }

                $out_date = !empty($out_date_raw) ? $this->parseDate($out_date_raw) : null;
                if (!empty($out_date_raw) && empty($out_date)) {
                    $errors[] = "Line {$lineNumber}: Out Date '{$out_date_raw}' is invalid.";
                }

                $out_time = !empty($out_time_raw) ? $this->parseTime($out_time_raw) : null;
                if (!empty($out_time_raw) && empty($out_time)) {
                    $errors[] = "Line {$lineNumber}: Out Time '{$out_time_raw}' is invalid (use format 13:00:00).";
                }

                // Skip row if any validation error
                $lineErrors = array_filter($errors, function ($e) use ($lineNumber) {
                    return strpos($e, "Line {$lineNumber}:") === 0;
                });
                if (!empty($lineErrors)) {
                    $lineNumber++;
                    continue;
                }

                $in_datetime  = (!empty($in_date) && !empty($in_time))  ? $in_date . ' ' . $in_time  : null;
                $out_datetime = (!empty($out_date) && !empty($out_time)) ? $out_date . ' ' . $out_time : null;

                $existing = EmployeeAllocation::where('emp_id', $emp_id)
                    ->where('date', $date)
                    ->first();

                if ($existing) {
                    $errors[] = "Line {$lineNumber}: Record already exists for Employee {$emp_id} on {$date}.";
                    $lineNumber++;
                    continue;
                }

                try {
                    EmployeeAllocation::create([
                        'shift_id' => $shift_id,
                        'emp_id'   => $emp_id,
                        'date'     => $date,
                        'in_time'  => $in_datetime,
                        'out_time' => $out_datetime,
                        'ot_hours' => 0,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Line {$lineNumber}: " . $e->getMessage();
                }

                $lineNumber++;
            }

            DB::commit();

            $response = [
                'status' => $successCount > 0 ? 1 : 0,
                'msg'    => "Successfully {$successCount} Employee Allocations Added.",
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
                if ($successCount === 0) {
                    $response['status'] = 0;
                    $response['msg']    = 'No records were processed due to errors.';
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 0,
                'msg'    => 'File processing failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
