<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Holidaydiductionapproved extends Model
{
    protected $table = 'holiday_deductions_approved';


    public function absentdayscounts($empId, $firstDate, $lastDate){

        $absentdaycount = 0;

        for ($date = Carbon::parse($firstDate); $date->lte(Carbon::parse($lastDate)); $date->addDay()) {
            $attendances = DB::table('attendances')
                ->whereDate('attendances.date', $date->format('Y-m-d'))
                ->where('emp_id', $empId)
                ->exists();

                if (!$attendances) {
                    $isHoliday = DB::table('holidays')
                    ->whereDate('date', $date->format('Y-m-d'))
                    ->exists();

                    $isSunday = $date->isSunday();

                    if (!$isHoliday && !$isSunday) {
                        $absentdaycount++;
                    }
                }
        }
        return $absentdaycount;
    }
}
