<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;

class OTCalculation extends Model
{
    // =========================================================================
    //  PUBLIC ENTRY POINT
    // =========================================================================

    public function get_ot_hours_by_date(
        $emp_id, $off_time, $on_time, $record_date,
        $shift_start_, $shift_end_, $emp_department,
        $allocateshiftID, $shift_until_time
    ) {
        $off_time    = Carbon::parse($off_time);
        $on_time     = Carbon::parse($on_time);
        $record_date = Carbon::parse($record_date);

        if ($shift_start_ == '') {
            return ['normal_rate_otwork_hrs' => 0, 'double_rate_otwork_hrs' => 0];
        }

        $department = Department::where('id', $emp_department)->first();
        if (empty($department)) {
            return [
                'normal_rate_otwork_hrs'        => 0,
                'double_rate_otwork_hrs'        => 0,
                '15_rate_otwork_hrs'            => 0,
                'ot_breakdown'                  => [],
                'info'                          => 'Department not found',
            ];
        }

        // ── Fetch employee & shift ────────────────────────────────────────────
        $emp = DB::table('employees')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'employees.job_category_id')
            ->select('emp_shift', 'emp_etfno', 'emp_name_with_initial', 'job_category_id', 'job_categories.*')
            ->where('emp_id', $emp_id)
            ->first();

        $shift = DB::table('shift_types')->where('id', $allocateshiftID)->first();

        // ── Build duty times ──────────────────────────────────────────────────
        $ondutyTime        = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$shift->onduty_time}");
        $offdutyTime       = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$shift->offduty_time}");
        $saturdayonduty    = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$shift->saturday_onduty_time}");
        $saturdayoffduty   = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$shift->saturday_offduty_time}");

        $shiftdiffhours = $saturdayonduty->diffInHours(
            $shift->off_next_day == 1 ? $saturdayoffduty->copy()->addDay() : $saturdayoffduty
        );

        // ── Flex OT adjustment ────────────────────────────────────────────────
        if ($emp->flex_ot == 1) {
            if ($record_date->dayOfWeek == 6) {
                $ondutyTime  = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$on_time->format('H:i')}");
                $offdutyTime = $ondutyTime->copy()->addMinutes($shiftdiffhours * 60);
            } else {
                $ondutyTime  = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$on_time->format('H:i')}");
                $offdutyTime = $ondutyTime->copy()->addMinutes($emp->shift_hours * 60);
            }
            $shift_start_ = $ondutyTime->format('H:i');
            $shift_end_   = $offdutyTime->format('H:i');
        }

        // ── Half/short leave hours ────────────────────────────────────────────
        $halfshorthours = $this->_ot_halfShortHours($emp_id, $record_date, $ondutyTime, $emp);

        // ── Shift config variables ────────────────────────────────────────────
        $cfg = $this->_ot_buildConfig($emp, $shift, $ondutyTime, $offdutyTime);

        // ── Work hours totals ─────────────────────────────────────────────────
        $totalMinutes       = $on_time->diffInMinutes($off_time);
        $emplyeeworkhours   = round($totalMinutes / 60, 2);
        $totalworkinghours  = $emplyeeworkhours + $halfshorthours;
        $afterothours       = $cfg['shifthours'] + $cfg['otafterhours'];

        // ── Apply until time / max OT cap ─────────────────────────────────────
        if($emp->until_time_available == 1){
            $off_time = $this->_ot_applyMaxCap(
                $off_time, $shift_until_time, $emp->until_time_available,
                $record_date, $shift, $shift_end_, $saturdayoffduty
            );
        }

        // ── OT rounding ───────────────────────────────────────────────────────
        $off_time = $this->_ot_roundOffTime($off_time, $cfg['roundotmin']);

        // ── Totals & breakdown accumulators ──────────────────────────────────
        $totals = ['normal' => 0, 'double' => 0, 'one_five' => 0, 'triple' => 0];
        $ot_breakdown = [];

        // ── Process each day period ───────────────────────────────────────────
        $date_period = $off_time->diffInDays($on_time);   // note: original used $off_time/$on_time before re-assign; keep same order

        if ($date_period == 0) {
            [$totals, $ot_breakdown] = $this->_ot_processDay(
                $record_date, $emp_id, $emp, $shift, $on_time, $off_time,
                $shift_start_, $shift_end_, $totalworkinghours, $afterothours,
                $cfg, $shift_until_time, $totals, $ot_breakdown
            );
        } else {
            for ($i = 0; $i < $date_period; $i++) {
                $date = $record_date->copy()->addDays($i);
                [$totals, $ot_breakdown] = $this->_ot_processDay(
                    $date, $emp_id, $emp, $shift, $on_time, $off_time,
                    $shift_start_, $shift_end_, $totalworkinghours, $afterothours,
                    $cfg, $shift_until_time, $totals, $ot_breakdown
                );
            }
        }

        return [
            'normal_rate_otwork_hrs'         => $totals['normal'],
            'double_rate_otwork_hrs'         => $totals['double'],
            'one_point_five_rate_otwork_hrs' => $totals['one_five'],
            'triple_rate_otwork_hrs'         => $totals['triple'],
            'ot_breakdown'                   => $ot_breakdown,
        ];
    }

    // =========================================================================
    //  CORE DAY PROCESSOR  (called for each calendar day in the period)
    // =========================================================================

    private function _ot_processDay(
        $date, $emp_id, $emp, $shift, $on_time, $off_time,
        $shift_start_, $shift_end_, $totalworkinghours, $afterothours,
        $cfg, $shift_until_time, array $totals, array $ot_breakdown
    ) {
        $day       = $date->dayOfWeek;
        $s_date    = $date->format('Y-m-d');
        $is_holiday      = false;
        $is_double       = false;
        $is_one_point_five = false;

        $shift_start = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$shift_start_}");
        $shift_end   = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$shift_end_}");
        if ($shift->off_next_day == 1) {
            $shift_end->addDay();
        }

        // Current day accumulators
        $hrs = ['normal' => 0, 'double' => 0, 'one_five' => 0, 'triple' => 0,
                'normal_m' => 0, 'double_m' => 0, 'one_five_m' => 0, 'triple_m' => 0];
        $ot_from = $on_time;
        $ot_to   = $off_time;

        $holiday_check = Holiday::where('date', $s_date)->first();

        if (!empty($holiday_check)) {
            // ── Holiday ───────────────────────────────────────────────────────
            $is_holiday = true;
            $hrs = $this->_ot_calculateHolidayOt($holiday_check, $shift_start, $on_time, $off_time, $cfg, $hrs);

        } elseif ($day == 0) {
            // ── Sunday ────────────────────────────────────────────────────────
            $hrs = $this->_ot_calculateSundayOt(
                $emp, $shift_start, $shift_end, $on_time, $off_time,
                $date, $cfg, $totalworkinghours, $afterothours, $hrs, $ot_from, $ot_to
            );
            $is_double = ($emp->is_sun_ot_type_as_act == 1 || $emp->custom_sunday_ot_type == 2);

        } elseif ($day == 6) {
            // ── Saturday ──────────────────────────────────────────────────────
            [$hrs, $shift_start, $shift_end] = $this->_ot_calculateSaturdayOt(
                $emp, $shift, $shift_start, $shift_end, $on_time, $off_time,
                $date, $cfg, $totalworkinghours, $afterothours, $hrs, $shift_until_time
            );
            $is_double = ($emp->is_sat_ot_type_as_act == 1 ? false : ($emp->custom_saturday_ot_type == 2));

        } else {
            // ── Weekday ───────────────────────────────────────────────────────
            $hrs = $this->_ot_calculateWeekdayOt(
                $emp, $shift_start, $shift_end, $on_time, $off_time,
                $date, $cfg, $totalworkinghours, $afterothours, $hrs, $shift_start_
            );
        }

        // ── Apply covering deduction ──────────────────────────────────────────
        [$fromtime, $othours] = $this->_ot_applyCovering($emp_id, $date, $on_time, $hrs['normal']);

        // ── Accumulate totals ─────────────────────────────────────────────────
        $totals['normal']  += $hrs['normal']   + $hrs['normal_m'];
        $totals['double']  += $hrs['double']   + $hrs['double_m'];
        $totals['one_five']+= $hrs['one_five'] + $hrs['one_five_m'];
        $totals['triple']  += $hrs['triple']   + $hrs['triple_m'];

        // ── Build breakdown entries ───────────────────────────────────────────
        $ot_to = $off_time; // reset to actual

        if ($hrs['normal_m'] > 0 || $hrs['double_m'] > 0 || $hrs['triple_m'] > 0) {
            $ot_breakdown[] = $this->_ot_breakdownEntry(
                $emp_id, $emp, $date, $on_time, $shift_start,
                $hrs['normal_m'], $hrs['double_m'], $hrs['one_five_m'], $hrs['triple_m'],
                0, 0, $is_holiday
            );
        }

        if ($othours > 0 || $hrs['double'] > 0 || $hrs['triple'] > 0) {
            $ot_breakdown[] = $this->_ot_breakdownEntry(
                $emp_id, $emp, $date, $fromtime, $ot_to,
                $othours, $hrs['double'], $hrs['one_five'], $hrs['triple'],
                0, 0, $is_holiday
            );
        }

        return [$totals, $ot_breakdown];
    }

    // =========================================================================
    //  HOLIDAY OT
    // =========================================================================

    private function _ot_calculateHolidayOt($holiday_check, $shift_start, $on_time, $off_time, $cfg, $hrs)
    {
        if ($cfg['holidayotstart'] == 1) {
            $ot_minutes = $on_time->diffInMinutes($off_time);
        } else {
            $ot_minutes = $shift_start < $on_time
                ? $on_time->diffInMinutes($off_time)
                : $shift_start->diffInMinutes($off_time);
        }

        if ($cfg['lunchholidaystatus'] == 1) {
            $ot_minutes -= $cfg['lunchdeductmin'];
        }

        $ot_minutes = $this->_ot_deductSpe($ot_minutes, $cfg['spedeductpresent']);

        if ($ot_minutes < $cfg['otminimumminits']) return $hrs;

        $ot_hours = round($ot_minutes / 60, 2);
        if ($cfg['holidayworkhours'] > 0) {
            $ot_hours = max(0, round($ot_hours - $cfg['holidayworkhours'], 2));
        }

        if ($holiday_check->work_level == 1) {
            $hrs['normal'] += $ot_hours;
        } else {
            $hrs['double'] += $ot_hours;
        }

        return $hrs;
    }

    // =========================================================================
    //  SUNDAY OT
    // =========================================================================

    private function _ot_calculateSundayOt(
        $emp, $shift_start, $shift_end, $on_time, $off_time,
        $date, $cfg, $totalworkinghours, $afterothours, $hrs, $ot_from, $ot_to
    ) {
        switch ($emp->is_sun_ot_type_as_act) {

            case 1: // As act – all double
                $ot_minutes = $this->_ot_minutesWithHolidayLunch($on_time, $off_time, $cfg);
                if ($ot_minutes >= $cfg['otminimumminits']) {
                    $hrs['double'] += round($ot_minutes / 60, 2);
                }
                break;

            case 0: // As custom
                if ($emp->custom_sunday_ot_type == 1) {
                    // Normal rate (with optional split after N hours)
                    [$ot_minutes, ] = $this->_ot_customDayClip($on_time, $off_time, $date, $cfg);
                    if ($ot_minutes >= $cfg['otminimumminits']) {
                        $ot_hours = round($ot_minutes / 60, 2);
                        if ($cfg['sunafterdoublehours'] > 0) {
                            $dbl = round($ot_hours - $cfg['sunafterdoublehours'], 2);
                            $hrs['normal'] += $cfg['sunafterdoublehours'] + $dbl;
                            $hrs['double'] += $dbl; // already included via normal above; adjust if needed
                        } else {
                            $hrs['normal'] += $ot_hours;
                        }
                    }
                } else {
                    // Double rate custom
                    [$ot_minutes, ] = $this->_ot_customDayClip($on_time, $off_time, $date, $cfg);
                    if ($ot_minutes >= $cfg['otminimumminits']) {
                        $hrs['double'] += round($ot_minutes / 60, 2);
                    }
                }
                break;

            case 2: // As normal working day (morning + evening)
                $hrs = $this->_ot_morningEveningOt(
                    $emp, $shift_start, $shift_end, $on_time, $off_time,
                    $date, $cfg, $totalworkinghours, $afterothours, $hrs, false, false
                );
                break;
        }

        return $hrs;
    }

    // =========================================================================
    //  SATURDAY OT
    // =========================================================================

    private function _ot_calculateSaturdayOt(
        $emp, $shift, $shift_start, $shift_end, $on_time, $off_time,
        $date, $cfg, $totalworkinghours, $afterothours, $hrs, $shift_until_time
    ) {
        // Resolve saturday duty times (flex_ot already adjusted $shift_start_/$shift_end_ upstream)
        if ($emp->is_sat_ot_type_as_act == 1) {
            // "As act" uses saturday-specific duty times
            $sat_on  = $emp->flex_ot == 1 ? $shift_start->format('H:i:s') : $shift->saturday_onduty_time;
            $sat_off = $emp->flex_ot == 1 ? $shift_end->format('H:i:s')   : $shift->saturday_offduty_time;

            $shift_start = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$sat_on}");
            $shift_end   = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$sat_off}");
            if ($shift->off_next_day == 1) $shift_end->addDay();
        }

        switch ($emp->is_sat_ot_type_as_act) {

            case 1: // As act – morning + evening using saturday times
                $hrs = $this->_ot_morningEveningOt(
                    $emp, $shift_start, $shift_end, $on_time, $off_time,
                    $date, $cfg, $totalworkinghours, $afterothours, $hrs,
                    false, false, $shift_until_time, true /* is_saturday */
                );
                break;

            case 0: // As custom
                if ($emp->custom_saturday_ot_type == 1) {
                    [$ot_minutes, ] = $this->_ot_customDayClip($on_time, $off_time, $date, $cfg);
                    if ($ot_minutes >= $cfg['otminimumminits']) {
                        $hrs['normal'] += round($ot_minutes / 60, 2);
                    }
                } else {
                    [$ot_minutes, ] = $this->_ot_customDayClip($on_time, $off_time, $date, $cfg);
                    if ($ot_minutes >= $cfg['otminimumminits']) {
                        $hrs['double'] += round($ot_minutes / 60, 2);
                    }
                }
                break;

            case 2: // As normal working day
                $hrs = $this->_ot_morningEveningOt(
                    $emp, $shift_start, $shift_end, $on_time, $off_time,
                    $date, $cfg, $totalworkinghours, $afterothours, $hrs, false, false
                );
                break;
        }

        return [$hrs, $shift_start, $shift_end];
    }

    // =========================================================================
    //  WEEKDAY OT  (Mon–Fri, with optional special day)
    // =========================================================================

    private function _ot_calculateWeekdayOt(
        $emp, $shift_start, $shift_end, $on_time, $off_time,
        $date, $cfg, $totalworkinghours, $afterothours, $hrs, $shift_start_
    ) {
        $day = $date->dayOfWeek;

        // Special day check
        if (!empty($cfg['spe_day_1_day']) && $day == $cfg['spe_day_1_day'] && $cfg['spe_day_1_type'] == 0) {
            [$ot_minutes, ] = $this->_ot_customDayClip($on_time, $off_time, $date, $cfg);
            if ($ot_minutes >= $cfg['otminimumminits']) {
                if ($cfg['spe_day_1_rate'] == 2) {
                    $hrs['double'] += round($ot_minutes / 60, 2);
                } else {
                    $hrs['normal'] += round($ot_minutes / 60, 2);
                }
            }
            return $hrs;
        }

        // Normal weekday
        return $this->_ot_morningEveningOt(
            $emp, $shift_start, $shift_end, $on_time, $off_time,
            $date, $cfg, $totalworkinghours, $afterothours, $hrs, false, false,
            null, false, $shift_start_
        );
    }

    // =========================================================================
    //  MORNING + EVENING OT  (shared by weekday / sat as-act / sun as-normal)
    // =========================================================================

    private function _ot_morningEveningOt(
        $emp, $shift_start, $shift_end, $on_time, $off_time,
        $date, $cfg, $totalworkinghours, $afterothours, $hrs,
        $is_double, $is_one_five,
        $shift_until_time = null, $is_saturday = false, $shift_start_ = null
    ) {
        // Morning OT
        if ($on_time < $shift_start && $cfg['morningotstatus'] == 1) {
            $ot_from = $this->_ot_applyBeginCheckin($on_time, $date, $cfg);
            $ot_to   = $shift_start;

            $ot_minutes = $this->_ot_deductSpe(
                $ot_from->diffInMinutes($ot_to),
                $cfg['spedeductpresent']
            );

            if ($ot_minutes >= $cfg['otminimumminits']) {
                $key = $is_double ? 'double_m' : ($is_one_five ? 'one_five_m' : 'normal_m');
                $hrs[$key] += round($ot_minutes / 60, 2);
            }
        }

        // Evening OT
        $evening_condition = $is_saturday
            ? $off_time > $shift_end
            : ($off_time > $shift_end && $totalworkinghours >= $afterothours);

        if ($evening_condition) {
            $ot_from = $shift_end;

            // Cap to next-day shift start
            $next_date  = $date->copy()->addDay()->format('Y-m-d');
            $next_start_str = $shift_start_ ?? $shift_start->format('H:i:s');
            $next_shift_start = Carbon::parse("{$next_date} {$next_start_str}");

            if ($is_saturday) {
                $ot_to = ($next_shift_start < $off_time && empty($shift_until_time))
                    ? $next_shift_start
                    : $off_time;

                // Ensure same date on non-next-day shifts
                if ($is_saturday && isset($shift) && property_exists($shift ?? new \stdClass, 'off_next_day') && ($shift->off_next_day ?? 0) == 0) {
                    $ot_from = Carbon::parse($ot_from)->setDate($date->year, $date->month, $date->day);
                }
            } else {
                $ot_to = $next_shift_start < $off_time ? $next_shift_start : $off_time;
            }

            $ot_minutes = $this->_ot_deductSpe(
                Carbon::parse($ot_from)->diffInMinutes(Carbon::parse($ot_to)),
                $cfg['spedeductpresent']
            );

            if ($ot_minutes >= $cfg['otminimumminits']) {
                $key = $is_double ? 'double' : ($is_one_five ? 'one_five' : 'normal');
                $hrs[$key] += round($ot_minutes / 60, 2);
            }

            // Week-after-double split
            if ($cfg['weekafterdouble'] > 0 && $hrs['normal'] > $cfg['weekafterdouble']) {
                $hrs['double'] += round($hrs['normal'] - $cfg['weekafterdouble'], 2);
                $hrs['normal']  = $cfg['weekafterdouble'];
            }
        }

        return $hrs;
    }

    // =========================================================================
    //  CUSTOM DAY CLIP  (Sun/Sat/Special shared lunch & window logic)
    // =========================================================================

    /**
     * Clips $on_time / $off_time to the allowed OT window and deducts lunch.
     * Returns [$ot_minutes, $ot_to] so callers can further inspect $ot_to.
     */
    private function _ot_customDayClip($on_time, $off_time, $date, $cfg)
    {
        $ot_from = clone $on_time;
        $ot_to   = clone $off_time;

        // Clip start: between begining_checkin and onduty_time → snap to onduty
        $today_seven = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$cfg['begining_checkin']}");
        $today_eight = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$cfg['onduty_time']}");
        if ($ot_from > $today_seven && $ot_from < $today_eight) {
            $ot_from = $today_eight;
        }

        // Clip end: between earlystart and earlyend → snap to earlystart
        $today_twelve = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$cfg['earlystart']}");
        $today_one    = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$cfg['earlyend']}");
        if ($ot_to >= $today_twelve && $ot_to < $today_one) {
            $ot_to = $today_twelve;
        }

        // Lunch deduct
        $deducthours = round($cfg['lunchdeductmin'] / 60, 2);
        if ($cfg['lunchdeductstatus'] == 1 && $ot_to >= $today_one) {
            $ot_to->subHours($deducthours);
        }

        $ot_minutes = $this->_ot_deductSpe(
            $ot_from->diffInMinutes($ot_to),
            $cfg['spedeductpresent']
        );

        // Restore $ot_to after deduct (for breakdown 'to' field)
        if ($cfg['lunchdeductstatus'] == 1 && $ot_to >= $today_one) {
            $ot_to->addHours($deducthours);
        }

        return [$ot_minutes, $ot_to];
    }

    // =========================================================================
    //  HELPER: HALF/SHORT HOURS
    // =========================================================================

    private function _ot_halfShortHours($emp_id, $record_date, $ondutyTime, $emp)
    {
        $leaveinfo = DB::table('leaves')
            ->select('half_short')
            ->where('emp_id', $emp_id)
            ->where('status', 'Approved')
            ->whereDate('leave_from', $record_date)
            ->where('half_short', '<', 1)
            ->first();

        if (!empty($leaveinfo)) {
            if ($leaveinfo->half_short == '0.50') return 4;
            if ($leaveinfo->half_short == '0.25') return 2;
            return 0;
        }

        $late = DB::table('employee_late_attendances')
            ->select('check_in_time')
            ->where('emp_id', $emp_id)
            ->whereDate('date', $record_date)
            ->where('is_approved', 1)
            ->first();

        if (!empty($late)) {
            $latestart  = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$late->check_in_time}");
            $lateminits = $ondutyTime->diffInMinutes($latestart);
            return round($lateminits / 60, 2);
        }

        return 0;
    }

    // =========================================================================
    //  HELPER: BUILD CONFIG ARRAY
    // =========================================================================

    private function _ot_buildConfig($emp, $shift, $ondutyTime, $offdutyTime)
    {
        if ($emp->flex_ot == 1) {
            return [
                'shifthours'        => $emp->shift_hours,
                'otafterhours'      => $emp->ot_app_hours,
                'otminimumminits'   => $emp->holiday_ot_minimum_min,
                'begining_checkin'  => $ondutyTime->format('H:i'),
                'onduty_time'       => $ondutyTime->format('H:i'),
                'earlystart'        => $offdutyTime->format('H:i'),
                'earlyend'          => $ondutyTime->copy()->addMinutes($emp->shift_hours * 30)->format('H:i:s'),
                'lunchdeductstatus' => $emp->lunch_deduct_type,
                'lunchholidaystatus'=> $emp->holiday_lunch_deduct,
                'holidayotstart'    => $emp->holiday_ot_start,
                'lunchdeductmin'    => $emp->lunch_deduct_min,
                'spedeductpresent'  => $emp->spe_deduct_pre,
                'morningotstatus'   => $emp->morning_ot,
                'sunafterdoublehours'=> $emp->sun_after_double,
                'weekafterdouble'   => $emp->week_after_double,
                'roundotmin'        => $emp->ot_round_time,
                'holidayworkhours'  => $emp->holiday_work_hours,
                'untiltimeavable'   => $emp->until_time_available,
                'spe_day_1_day'     => $emp->spe_day_1_day,
                'spe_day_1_type'    => $emp->spe_day_1_type,
                'spe_day_1_rate'    => $emp->spe_day_1_rate,
            ];
        }

        return [
            'shifthours'        => $emp->shift_hours,
            'otafterhours'      => $emp->ot_app_hours,
            'otminimumminits'   => $emp->holiday_ot_minimum_min,
            'begining_checkin'  => $shift->begining_checkin,
            'onduty_time'       => $shift->onduty_time,
            'earlystart'        => $shift->leave_early_time,
            'earlyend'          => $shift->saturday_offduty_time,
            'lunchdeductstatus' => $emp->lunch_deduct_type,
            'lunchholidaystatus'=> $emp->holiday_lunch_deduct,
            'holidayotstart'    => $emp->holiday_ot_start,
            'lunchdeductmin'    => $emp->lunch_deduct_min,
            'spedeductpresent'  => $emp->spe_deduct_pre,
            'morningotstatus'   => $emp->morning_ot,
            'sunafterdoublehours'=> $emp->sun_after_double,
            'weekafterdouble'   => $emp->week_after_double,
            'roundotmin'        => $emp->ot_round_time,
            'holidayworkhours'  => $emp->holiday_work_hours,
            'untiltimeavable'   => $emp->until_time_available,
            'spe_day_1_day'     => $emp->spe_day_1_day,
            'spe_day_1_type'    => $emp->spe_day_1_type,
            'spe_day_1_rate'    => $emp->spe_day_1_rate,
        ];
    }

    // =========================================================================
    //  HELPER: MAX OT CAP / UNTIL TIME
    // =========================================================================

    private function _ot_applyMaxCap($off_time, $shift_until_time, $untiltimeavable, $record_date, $shift, $shift_end_, $saturdayoffduty)
    {
        if (!empty($shift_until_time)) {
            if ($shift_until_time < $off_time) {
                return Carbon::parse($shift_until_time);
            }
            return $off_time;
        }

        if ($record_date->dayOfWeek == 6) {
            $max = Carbon::parse($saturdayoffduty)->addMinutes($shift->weekend_max_normal_ot_hrs * 60);
        } else {
            $max = Carbon::parse("{$record_date->year}-{$record_date->month}-{$record_date->day} {$shift_end_}")
                ->addMinutes($shift->max_normal_ot_hrs * 60);
            if ($shift->off_next_day == 1) $max->addDay();
        }

        return $off_time > $max ? $max : $off_time;
    }

    // =========================================================================
    //  HELPER: OT ROUND
    // =========================================================================

    private function _ot_roundOffTime($off_time, $roundotmin)
    {
        if ($roundotmin <= 0) return $off_time;

        $roundInterval  = 30;
        $minutes        = (int) $off_time->format('i');
        $roundedMinutes = floor($minutes / $roundInterval) * $roundInterval;

        if ($minutes < $roundotmin) {
            $off_time->minute(0);
        } else {
            $off_time->minute($roundedMinutes);
        }

        return $off_time;
    }

    // =========================================================================
    //  HELPER: BEGIN CHECKIN CLIP  (morning OT start)
    // =========================================================================

    private function _ot_applyBeginCheckin($on_time, $date, $cfg)
    {
        $today_seven = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$cfg['begining_checkin']}");
        $today_eight = Carbon::parse("{$date->year}-{$date->month}-{$date->day} {$cfg['onduty_time']}");

        if ($on_time > $today_seven && $on_time < $today_eight) {
            return $today_eight;
        }

        return clone $on_time;
    }

    // =========================================================================
    //  HELPER: SPE DEDUCT
    // =========================================================================

    private function _ot_deductSpe($minutes, $spedeductpresent)
    {
        if ($spedeductpresent > 0) {
            $minutes -= ($minutes % $spedeductpresent);
        }
        return $minutes;
    }

    // =========================================================================
    //  HELPER: HOLIDAY LUNCH MINUTES
    // =========================================================================

    private function _ot_minutesWithHolidayLunch($on_time, $off_time, $cfg)
    {
        $minutes = $on_time->diffInMinutes($off_time);
        if ($cfg['lunchholidaystatus'] == 1) {
            $minutes -= $cfg['lunchdeductmin'];
        }
        return $this->_ot_deductSpe($minutes, $cfg['spedeductpresent']);
    }

    // =========================================================================
    //  HELPER: COVERING DEDUCTION
    // =========================================================================

    private function _ot_applyCovering($emp_id, $date, $on_time, $ot_hours)
    {
        $covering = DB::table('coverup_details')
            ->where('emp_id', $emp_id)
            ->whereDate('date', $date)
            ->first();

        if ($covering && $ot_hours > 0) {
            $coveringend = Carbon::parse($covering->end_time);
            $coveringend = $date->copy()->setTime($coveringend->hour, $coveringend->minute, $coveringend->second);
            return [$coveringend, $ot_hours - $covering->covering_hours];
        }

        return [Carbon::parse($on_time), $ot_hours];
    }

    // =========================================================================
    //  HELPER: BUILD BREAKDOWN ENTRY
    // =========================================================================

    private function _ot_breakdownEntry(
        $emp_id, $emp, $date, $from, $to,
        $hours, $double_hours, $one_five_hours, $triple_hours,
        $holiday_ot, $holiday_double, $is_holiday
    ) {
        return [
            'emp_id'                => $emp_id,
            'etf_no'                => $emp->emp_etfno,
            'name'                  => $emp->emp_name_with_initial,
            'date'                  => $date->format('Y-m-d'),
            'day_name'              => $date->format('l'),
            'from'                  => Carbon::parse($from)->format('Y-m-d h:i A'),
            'from_24'               => Carbon::parse($from)->format('Y-m-d H:i'),
            'from_rfc'              => Carbon::parse($from)->format('Y-m-d\TH:i:s'),
            'to'                    => Carbon::parse($to)->format('Y-m-d h:i A'),
            'to_24'                 => Carbon::parse($to)->format('Y-m-d H:i'),
            'to_rfc'                => Carbon::parse($to)->format('Y-m-d\TH:i:s'),
            'hours'                 => $hours,
            'double_hours'          => $double_hours,
            'one_point_five_ot_hours' => $one_five_hours,
            'triple_hours'          => $triple_hours,
            'holiday_ot_hours'      => $holiday_ot,
            'holiday_double_hours'  => $holiday_double,
            'is_holiday'            => $is_holiday,
        ];
    }

}