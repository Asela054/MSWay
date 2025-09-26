<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\JobCategory;

class JobCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('company-list');

        if(!$permission) {
            abort(403);
        }

        $jobcategory = JobCategory::orderBy('id', 'asc')->get();
        return view('Organization.jobCategory', compact('jobcategory'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('company-create');


        $jobcategory = new JobCategory;
        $jobcategory->category = $request->input('category');
        $jobcategory->annual_leaves = $request->input('annual_leaves');
        $jobcategory->casual_leaves = $request->input('casual_leaves');
        $jobcategory->medical_leaves = $request->input('medical_leaves');
        //$jobcategory->otdeduct = $request->input('otdeduct');
        //$jobcategory->nopaydeduct = $request->input('nopaydeduct');

        $jobcategory->emp_payroll_workdays = $request->input('emp_payroll_workdays');
        $jobcategory->emp_payroll_workhrs = $request->input('emp_payroll_workhrs');
        $jobcategory->ot_app_hours = $request->input('ot_app_hours');
        $jobcategory->holiday_ot_minimum_min = $request->input('holiday_ot_minimum_min');
        $jobcategory->spe_deduct_pre = $request->input('spe_deduct_pre');
        $jobcategory->shift_hours = $request->input('shift_hours');
        $jobcategory->holiday_work_hours = $request->input('holiday_work_hours');
        $jobcategory->week_after_double = $request->input('week_after_double');
        $jobcategory->work_hour_date = $request->input('work_hour_date');
        $jobcategory->morning_ot = $request->input('morning_ot');
        $jobcategory->holiday_ot_start = $request->input('holiday_ot_start');
        $jobcategory->holiday_lunch_deduct = $request->input('holiday_lunch_deduct');
        
//        if($request->input('short_leave_enabled') !== null){
//            $jobcategory->short_leave_enabled = 1;
//        }else{
//            $jobcategory->short_leave_enabled = 0;
//        }

        $jobcategory->lunch_deduct_type = $request->input('lunch_deduct_type');
        $jobcategory->lunch_deduct_min = $request->input('lunch_deduct_min');

        $jobcategory->salary_without_attendace = $request->input('salary_without_attendace');

        $jobcategory->is_sat_ot_type_as_act = $request->input('is_sat_ot_type_as_act');
        $jobcategory->custom_saturday_ot_type = $request->input('custom_saturday_ot_type');

        $jobcategory->is_sun_ot_type_as_act = $request->input('is_sun_ot_type_as_act');
        $jobcategory->custom_sunday_ot_type = $request->input('custom_sunday_ot_type');
        $jobcategory->sun_after_double = $request->input('sun_after_double');

        $jobcategory->spe_day_1_day = $request->input('spe_day_1_day');
        $jobcategory->spe_day_1_type = $request->input('spe_day_1_type');
        $jobcategory->spe_day_1_rate = $request->input('spe_day_1_rate');

        $jobcategory->late_type = $request->input('late_type');
        $jobcategory->late_attend_min = $request->input('late_attend_min');
        $jobcategory->short_leaves = $request->input('short_leaves');
        $jobcategory->half_days = $request->input('half_days');

        $jobcategory->save();

        return response()->json(['success' => 'Job Category Added successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('company-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = JobCategory::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, JobCategory $jobcategory)
    {
        $user = auth()->user();
        $permission = $user->can('company-edit');

        $form_data = array(
            'category' => $request->category,
            'annual_leaves' => $request->annual_leaves,
            'casual_leaves' => $request->casual_leaves,
            'medical_leaves' => $request->medical_leaves,
            'emp_payroll_workdays' => $request->emp_payroll_workdays,
            'emp_payroll_workhrs' => $request->emp_payroll_workhrs,
            'ot_app_hours' => $request->ot_app_hours,
            'holiday_ot_minimum_min' => $request->holiday_ot_minimum_min,
            'spe_deduct_pre' => $request->spe_deduct_pre,
            'shift_hours' => $request->shift_hours,
            'holiday_work_hours' => $request->holiday_work_hours,
            'week_after_double' => $request->week_after_double,
            'work_hour_date' => $request->work_hour_date,
            'morning_ot' => $request->morning_ot,
            'holiday_ot_start' => $request->holiday_ot_start,
            'holiday_lunch_deduct' => $request->holiday_lunch_deduct,
            'lunch_deduct_type' => $request->lunch_deduct_type,
            'lunch_deduct_min' => $request->lunch_deduct_min,
            'salary_without_attendace' => $request->salary_without_attendace,
            'is_sat_ot_type_as_act' => $request->is_sat_ot_type_as_act,
            'custom_saturday_ot_type' => $request->custom_saturday_ot_type,
            'is_sun_ot_type_as_act' => $request->is_sun_ot_type_as_act,
            'custom_sunday_ot_type' => $request->custom_sunday_ot_type,
            'sun_after_double' => $request->sun_after_double,
            'spe_day_1_day' => $request->spe_day_1_day,
            'spe_day_1_type' => $request->spe_day_1_type,
            'spe_day_1_rate' => $request->spe_day_1_rate,
            'late_type' => $request->late_type,
            'late_attend_min' => $request->late_attend_min,
            'short_leaves' => $request->short_leaves,
            'half_days' => $request->half_days
        );

        JobCategory::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Job Category is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('company-delete');

        $data = JobCategory::findOrFail($id);
        $data->delete();
    }

}

?>