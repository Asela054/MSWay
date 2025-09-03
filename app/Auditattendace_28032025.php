<?php

namespace App;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Auditattendace extends Model
{
    protected $table = 'audit_attendance';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'employee_id',
        'attendance_date',
        'actual_ontime',
        'actual_offtime',
        'actual_ot_count',
        'actual_workhours',
        'audit_ontime',
        'audit_offtime',
        'audit_ot_from',
        'audit_ot_count',
        'audit_workhours'
    ];
    
    public function apply_audit_attedance($auto_empid,$emp_id, $month )
    {
        $employee_id = $auto_empid;
        $empid=$emp_id;

        $current_date_time = Carbon::now()->toDateTimeString();

        $startDate = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');

        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        );

        $employee = DB::table('employees')
        ->select(
            'shift_types.onduty_time AS ontime',
            'shift_types.offduty_time AS offtime',
            'shift_types.saturday_onduty_time AS saturday_ontime',
            'shift_types.saturday_offduty_time AS saturday_offtime'
        )
        ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
        ->where('employees.deleted', 0)
        ->where('employees.id', $employee_id)
        ->first();

        if ($employee) {
            $shift_ontime = $employee->ontime;
            $shift_offtime = $employee->offtime;
            $saturday_shift_ontime = $employee->saturday_ontime;
            $saturday_shift_offtime = $employee->saturday_offtime; 
        }

     



        foreach ($period  as $curentdate) {

            $date = $curentdate->format('Y-m-d');

            $holiday = DB::table('holidays')
            ->where('date', $date)
            ->first(); 

            if(empty($holiday)){
                $attendance = DB::table('attendances')
                ->where('emp_id', $empid)
                ->whereDate('date', $date)
                ->selectRaw('MIN(timestamp) as in_time, MAX(timestamp) as out_time')
                ->first();
    
                if ($attendance->in_time || $attendance->out_time){
    
                    $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
                    $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';

                    $duration = Carbon::parse($inTime)->diff(Carbon::parse($outTime))->format('%H:%I');
        
                    $dayOfWeek = Carbon::parse($date)->format('l');
    
                    // get ot amout for date
                    $otApproved = DB::table('ot_approved')
                    ->where('emp_id', $empid)
                    ->whereDate('date', $date)
                    ->select('hours', 'double_hours', 'triple_hours')
                    ->first();
    
                    $othours = $otApproved ? $otApproved->hours : 0;
                
                    $modifyedouttime = Carbon::parse($outTime);
                    $auditot = $othours;
                    $auditotfrom = $shift_offtime;
    
                    if ($modifyedouttime->lessThan(Carbon::parse($inTime))) {
                        $modifyedouttime->addDay(); // Move to the next day
                    }
    
                    if (in_array($dayOfWeek, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']) && $modifyedouttime->greaterThan(Carbon::parse('19:00'))) {
                        if($modifyedouttime->greaterThan($shift_offtime)){
                            $auditot = 2;
                        }else{
                            $auditot = 0;
                        }

                        $randomMinutes = rand(0, 15);
                        $modifyedouttime = Carbon::parse('19:00')->addMinutes($randomMinutes);
                        
                    } elseif($dayOfWeek === 'Saturday' && $modifyedouttime->greaterThan(Carbon::parse('17:00'))){

                        if($modifyedouttime->greaterThan($saturday_shift_offtime)){
                            $auditot = 3;
                        }else{
                            $auditot = 0;
                        }
                        $randomMinutes = rand(0, 15);
                        $modifyedouttime = Carbon::parse('17:00')->addMinutes($randomMinutes);
                        $auditotfrom = $saturday_shift_offtime;
                    }
                    
                    $adjustedOutTime = $modifyedouttime->format('H:i');
                    $auditduration = Carbon::parse($inTime)->diff($modifyedouttime)->format('%H:%I');
    
    
                    DB::table('audit_attendance')
                    ->where('emp_id', $empid)
                    ->whereDate('attendance_date', $date)
                    ->delete();
    
                    DB::table('audit_attendance')->insert([
                        'emp_id' => $empid,
                        'employee_id' => $auto_empid,
                        'attendance_date' => $date,
                        'actual_ontime' => $inTime, 
                        'actual_offtime' => $outTime, 
                        'actual_ot_count' => $othours , 
                        'actual_workhours' => $duration ,
                        'audit_ontime' => $inTime,
                        'audit_offtime' => $adjustedOutTime,
                        'audit_ot_from' => $auditotfrom,
                        'audit_ot_count' => $auditot,
                        'audit_workhours' => $auditduration,
                        'created_at' => $current_date_time
                    ]);
                }
            }
        }

    }




}
