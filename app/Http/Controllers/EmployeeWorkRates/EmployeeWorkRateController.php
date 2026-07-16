<?php

namespace App\Http\Controllers\EmployeeWorkRates;

use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class EmployeeWorkRateController extends Controller
{
    public function empworkrate()
    {
        $user = Auth::user();
        if (!$user->can('employee-work-rate-list')) {
            abort(403);
        }

        $appName = config('app.name');
        if($appName == 'KoasisV2'){
            return view('EmployeeWorkRates.employeeWorkRate_Ko');
        }
        else{
             return view('EmployeeWorkRates.employeeWorkRate');
        }

    }

    public function emp_work_rate_list(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('employee-work-rate-list')) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $company    = $request->get('company');
        $department = $request->get('department');
        $month      = $request->get('month');

        $userId                = Auth::id();
        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
        $userBranchIds         = DB::table('user_has_companies')
            ->where('user_id', $userId)
            ->pluck('branch_id')
            ->toArray();

        $work_year  = null;
        $work_month = null;
        if (!empty($month)) {
            [$work_year, $work_month] = explode('-', $month);
        }

        $monthStr = '';
        if ($work_year && $work_month) {
            $monthStr = sprintf('%04d-%02d', (int)$work_year, (int)$work_month);
        }

        $rosterSql = "
            SELECT
                era.emp_id AS r_emp_id,
                ROUND(SUM(era.count), 2) AS roster_work_days,
                ROUND(SUM(era.count * (MOD(TIME_TO_SEC(st.offduty_time) - TIME_TO_SEC(st.onduty_time) + 86400, 86400) / 3600)), 2) AS roster_working_hours,
                MAX(era.max_work_days) AS roster_max_work_days
            FROM employee_roster_approve AS era
            INNER JOIN shift_types AS st ON st.id = era.shift_id
            " . ($monthStr ? "WHERE DATE_FORMAT(era.month, '%Y-%m') = ?" : "") . "
            GROUP BY era.emp_id
        ";

        $rosterBindings = $monthStr ? [$monthStr] : [];


        $query = DB::table('employees')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('branches',    'branches.id',    '=', 'employees.emp_location')
            ->leftJoin('companies',   'companies.id',   '=', 'employees.emp_company')
            ->leftJoin('employee_work_rates as ewr', function ($join) use ($work_year, $work_month) {
                $join->on('ewr.emp_id', '=', 'employees.id');
                if ($work_year && $work_month) {
                    $join->where('ewr.work_year',  '=', $work_year)
                        ->where('ewr.work_month', '=', $work_month);
                }
            })
            ->leftJoin(DB::raw("({$rosterSql}) AS roster"), function ($join) use ($rosterBindings) {
                $join->on('roster.r_emp_id', '=', 'employees.emp_id');
            })
            ->addBinding($rosterBindings, 'join')
            ->select([
                // Core employee fields
                'employees.emp_id              as uid',
                'employees.emp_name_with_initial',
                'employees.id                  as emp_auto_id',
                'employees.emp_id              as emp_etfno',
                'departments.name              as dept_name',
                'branches.location',
                'companies.name                as company_name',
                // Previously saved work-rate fields (right / read-only side)
                'ewr.id                        as ewr_id',
                'ewr.work_days',
                'ewr.work_hours                as working_hours',
                'ewr.leave_days',
                'ewr.nopay_days                as no_pay_days',
                'ewr.emp_late_hours            as late_hours',
                'ewr.normal_rate_otwork_hrs    as normal_ot_hours',
                'ewr.double_rate_otwork_hrs    as double_ot_hours',
                'ewr.triple_rate_otwork_hrs    as triple_ot_hours',
                'ewr.holiday_nopay_days',
                'ewr.holiday_normal_ot_hrs     as holiday_normal_ot_hours',
                'ewr.holiday_double_ot_hrs     as holiday_double_ot_hours',
                //Roster-calculated fields (left / editable side) 
                'roster.roster_work_days',
                'roster.roster_working_hours',
                'roster.roster_max_work_days',
                // LEAVE DAYS  : max_work_days − total_count  (only when count < max)
                DB::raw('CASE
                            WHEN roster.roster_work_days IS NOT NULL
                            AND roster.roster_max_work_days IS NOT NULL
                            AND roster.roster_work_days < roster.roster_max_work_days
                            THEN ROUND(roster.roster_max_work_days - roster.roster_work_days, 2)
                            ELSE NULL
                        END AS roster_leave_days'),
                // NORMAL OT HRS : excess_days × avg_hours_per_day  (only when count > max)
                DB::raw('CASE
                            WHEN roster.roster_work_days IS NOT NULL
                            AND roster.roster_max_work_days IS NOT NULL
                            AND roster.roster_work_days > roster.roster_max_work_days
                            THEN ROUND(
                                (roster.roster_work_days - roster.roster_max_work_days)
                                * (roster.roster_working_hours / NULLIF(roster.roster_work_days, 0)),
                                2)
                            ELSE NULL
                        END AS roster_normal_ot_hours'),
            ])
            ->where('employees.deleted',     0)
            ->where('employees.is_resigned', 0);

        if (!empty($accessibleEmployeeIds)) {
            $query->whereIn('employees.emp_id', $accessibleEmployeeIds);
        }
        if (!empty($userBranchIds)) {
            $query->whereIn('employees.emp_location', $userBranchIds);
        }
        if (!empty($company)) {
            $query->where('employees.emp_company', $company);
        }
        if (!empty($department) && $department !== 'All') {
            $query->where('departments.id', $department);
        }

        return Datatables::of($query)->make(true);
    }

    /**
     * Save / update a single employee's work rate row.
     */
    public function emp_work_rate_add(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('employee-work-rate-add')) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $month = $request->input('month'); 
        if (empty($month)) {
            return response()->json(['error' => 'Month is required'], 422);
        }

        [$work_year, $work_month] = explode('-', $month);

        $rows    = $request->input('rows', []); 
        $saved   = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $emp_auto_id = $row['emp_auto_id'] ?? null;
            $emp_etfno   = $row['emp_etfno']   ?? null;

            if (empty($emp_auto_id)) {
                continue;
            }

            // Numeric fields — treat empty string as null
            $work_days              = $this->numOrNull($row['work_days']              ?? '');
            $work_hours             = $this->numOrNull($row['working_hours']          ?? '');
            $leave_days             = $this->numOrNull($row['leave_days']             ?? '');
            $nopay_days             = $this->numOrNull($row['no_pay_days']            ?? '');
            $late_hours             = $this->numOrNull($row['late_hours']             ?? '');
            $normal_ot              = $this->numOrNull($row['normal_ot_hours']        ?? '');
            $double_ot              = $this->numOrNull($row['double_ot_hours']        ?? '');
            $triple_ot              = $this->numOrNull($row['triple_ot_hours']        ?? '');
            $holiday_nopay          = $this->numOrNull($row['holiday_nopay_days']     ?? '');
            $holiday_normal_ot      = $this->numOrNull($row['holiday_normal_ot_hours']?? '');
            $holiday_double_ot      = $this->numOrNull($row['holiday_double_ot_hours']?? '');

            // Skip completely empty rows
            $allEmpty = is_null($work_days) && is_null($work_hours) && is_null($leave_days)
                     && is_null($nopay_days) && is_null($late_hours) && is_null($normal_ot)
                     && is_null($double_ot)  && is_null($triple_ot)  && is_null($holiday_nopay)
                     && is_null($holiday_normal_ot) && is_null($holiday_double_ot);

            if ($allEmpty) {
                $skipped++;
                continue;
            }

            $data = [
                'emp_id'                  => $emp_auto_id,
                'emp_etfno'               => $emp_etfno,
                'work_year'               => $work_year,
                'work_month'              => $work_month,
                'work_days'               => $work_days,
                'working_week_days'       => $work_days,   
                'work_hours'              => $work_hours,
                'leave_days'              => $leave_days,
                'nopay_days'              => $nopay_days,
                'emp_late_hours'          => $late_hours,
                'normal_rate_otwork_hrs'  => $normal_ot,
                'double_rate_otwork_hrs'  => $double_ot,
                'triple_rate_otwork_hrs'  => $triple_ot,
                'holiday_nopay_days'      => $holiday_nopay,
                'holiday_normal_ot_hrs'   => $holiday_normal_ot,
                'holiday_double_ot_hrs'   => $holiday_double_ot,
                'updated_at'              => date('Y-m-d H:i:s'),
            ];

            $exists = DB::table('employee_work_rates')
                ->where('emp_id', $emp_auto_id)
                ->where('work_year', $work_year)
                ->where('work_month', $work_month)
                ->first();

            if ($exists) {
                DB::table('employee_work_rates')
                    ->where('id', $exists->id)
                    ->update($data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                DB::table('employee_work_rates')->insert($data);
            }

            $saved++;
        }

        return response()->json([
            'success' => true,
            'saved'   => $saved,
            'skipped' => $skipped,
            'message' => "$saved record(s) saved, $skipped skipped (empty).",
        ]);
    }

    /** Return numeric value or 0 if blank/non-numeric */
    private function numOrNull($value)
    {
        if ($value === '' || $value === null) return 0;
        return is_numeric($value) ? $value + 0 : 0;
    }
}