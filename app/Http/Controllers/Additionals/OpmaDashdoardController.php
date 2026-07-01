<?php

namespace App\Http\Controllers\Additionals;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Http\Controllers\Controller;

class OpmaDashdoardController extends Controller
{
    //  public function machinechart(Request $request)
    //  {
    //       $today = Carbon::today()->toDateString();

    //        $devices = DB::table('assigned_devices')
    //                     ->select('id', 'device_name', 'remarks', 'created_at', 'updated_at')
    //                     ->where('id', '>=', 15)
    //                     ->get();
    //         foreach ($devices as $device) {
    //            $employeedevices = DB::table('employee_assigned_devices')
    //                     ->select('id', 'emp_id')
    //                     ->where('device_type', '=',$device->id)
    //                     ->where('status', '=',1)
    //                     ->get();
    //                 foreach ($employeedevices as $employeedevice) {
    //                     $emp_id = $employeedevice->emp_id;

    //                     $employee = DB::table('employees')
    //                         ->select('emp_name_with_initial', 'emp_shift', 'job_titles.title as job_title')
    //                         ->leftjoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
    //                         ->where('emp_id', '=', $emp_id)
    //                         ->first();

    //                     if ($employee) {
    //                         $emp_name = $employee->emp_name_with_initial;
    //                         $emp_shift = $employee->emp_shift;
    //                         $job_title = $employee->job_title;

    //                           $emprosterinfo = DB::table('employee_roster_details')
    //                                         ->select('emp_id', 'shift_id')
    //                                         ->where('emp_id', $emp_id)
    //                                         ->where('work_date', $today)
    //                                         ->first();

    //                                     if ($emprosterinfo) {
    //                                         $empshiftid = $emprosterinfo->shift_id;   


    //                                     }else{
    //                                         $empshiftid = $emp_shift;
    //                                     }
    //                     }

    //                 }

        
    //         }
    //  }

    public function machinechart(Request $request)
{
    //$today = Carbon::today()->toDateString();
      $date = '2026-06-02';

   // $today = Carbon::today()->toDateString();
   $today = $date;
    $devices = DB::table('assigned_devices')
        ->select('id', 'device_name','remarks')
        ->where('id', '>=', 15)
        ->get();

    $chartData = [];

    
    foreach ($devices as $device) {
        $employeedevices = DB::table('employee_assigned_devices')
            ->select('id', 'emp_id')
            ->where('device_type', '=', $device->id)
            ->where('status', '=', 1)
            ->get();

        $dayShiftEmps   = [];
        $nightShiftEmps = [];
        $qaEmp = null;


        foreach ($employeedevices as $employeedevice) {
            $empid = $employeedevice->emp_id;

            $employee = DB::table('employees')
                ->select('employees.emp_id', 'emp_name_with_initial', 'emp_shift','job_titles.title as job_title','emp_job_code','employee_pictures.emp_pic_filename')
                ->leftJoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
                ->leftJoin('employee_pictures', 'employee_pictures.emp_id', '=', 'employees.id')
                ->where('employees.id', '=', $empid)
                ->first();

            if (!$employee) continue;

            $emp_id = $employee->emp_id;

            $hasAttendance = DB::table('attendances')
                ->where('emp_id', $emp_id)
                ->where('date', $today)
                ->whereNull('deleted_at')
                ->exists();

            if (!$hasAttendance) continue;

            $rosterShift = DB::table('employee_roster_details')
                ->select('shift_id')
                ->where('emp_id', $emp_id)
                ->where('work_date', $today)
                ->first();

            $shiftId = $rosterShift ? $rosterShift->shift_id : $employee->emp_shift;

            $pic =  $employee->emp_pic_filename;
            $imagePath = '';
            if (!empty($pic) && file_exists(public_path("images/{$pic}"))) {
                $imagePath = asset("public/images/{$pic}");
            } else {
                 $employeeGender = \App\Employee::where('emp_id', $emp_id)->pluck('emp_gender')->first();
                    if(empty($employeeGender)){
                        $employeeGender = "Male";
                    }
                    $imagePath = $employeeGender == "Male" 
                        ? asset("public/images/man.png") 
                        : asset("public/images/girl.png");
            }

            $empData = [
                'emp_id'    => $employee->emp_id,
                'name'      => $employee->emp_name_with_initial,
                'job_title' => $employee->job_title,
                'photo'     => $imagePath,
                'shift_id'  => $shiftId,
            ];

            if ($employee->emp_job_code == 75) {
                    $qaEmp = $empData;
                    continue;
                }

            if ($shiftId == 3) {
                $dayShiftEmps[] = $empData;
            } elseif ($shiftId == 4) {
                $nightShiftEmps[] = $empData;
            }
        }

        $chartData[] = [
            'device_id'   => $device->id,
            'device_name' => $device->device_name,
            'remarks'     => $device->remarks,
            'day'         => $dayShiftEmps,
            'night'       => $nightShiftEmps,
            'qa'          => $qaEmp,
        ];
    }

   $html = '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;text-align:center;font-family:Arial,sans-serif;font-size:10px;">';
    $html .= '<thead>
                <tr style="background:#f0f0f0;">
                    <th>SHIFT</th>
                    <th>MACHINE</th>
                    <th>EMPLOYEE 1</th>
                    <th>EMPLOYEE 2</th>
                    <th>QA</th>
                    <th></th>
                </tr>
              </thead><tbody>';

    foreach ($chartData as $machine) {
        $deviceName = $machine['device_name'] . '<br><small>' . $machine['remarks'] . '</small>';
        $day        = $machine['day'];
        $night      = $machine['night'];
        $qa         = $machine['qa'];

        // QA cell — spans 2 rows
        $qaCell = '<td rowspan="2" style="vertical-align:middle;">';
        if ($qa) {
            $qaCell .= '<img src="' . $qa['photo'] . '" style="height:60px;width:60px;border-radius:50%;"><br>' .
                       '<strong>' . $qa['name'] . '</strong><br><small>' . $qa['job_title'] . '</small>';
        } 
        $qaCell .= '</td>';

        // empty spanning cell
        $emptyCell = '<td rowspan="2"></td>';

        // helper to build emp cell
        $empCell = function($emps, $index) {
            if (isset($emps[$index])) {
                $e = $emps[$index];
                return '<td style="vertical-align:middle;">
                            <img src="' . $e['photo'] . '" style="height:60px;width:60px;border-radius:50%;"><br>
                            <strong>' . $e['name'] . '</strong><br>
                            <small>' . $e['job_title'] . '</small>
                        </td>';
            }
            return '<td style="vertical-align:middle;color:#ccc;">-</td>';
        };

        // DAY SHIFT row
        $html .= '<tr style="background:#fff3cd;">';
        $html .= '<td style="writing-mode:vertical-rl;transform:rotate(180deg);font-weight:bold;background:#03fcfc;color:#000;padding:8px;">DAY SHIFT</td>';
        $html .= '<td style="vertical-align:middle;font-weight:bold;">' . $deviceName . '</td>';
        $html .= $empCell($day, 0);
        $html .= $empCell($day, 1);
        $html .= $qaCell;
        $html .= $emptyCell;
        $html .= '</tr>';

        // NIGHT SHIFT row
        $html .= '<tr style="background:#cfe2ff;">';
        $html .= '<td style="writing-mode:vertical-rl;transform:rotate(180deg);font-weight:bold;background:#03fc77;color:#000;padding:8px;">NIGHT SHIFT</td>';
        $html .= '<td style="vertical-align:middle;font-weight:bold;">' . $deviceName . '</td>';
        $html .= $empCell($night, 0);
        $html .= $empCell($night, 1);
        // qaCell and emptyCell already added via rowspan
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return response($html);

}
}