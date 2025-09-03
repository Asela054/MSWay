<?php

namespace App\Http\Controllers;

use App\WorkHour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkHoursController extends Controller
{
    public function work_hours_save(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('work-hours-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $selected_cb = $request->selected_cb;
        $emp_id = $request->emp_id;

        if (empty($selected_cb)) {
            return response()->json(['status' => false, 'msg' => 'Enter One or More records']);
        }

        foreach ($selected_cb as $ch) {

            if ($ch['work_days'] != '') {
                WorkHour::updateOrCreate(
                    ['emp_id' => $emp_id, 'date' => $ch['date'] ],
                    ['emp_id' => $emp_id, 'date' => $ch['date'], 'from' => $ch['from'], 'to' => $ch['to'], 'working_hours' => $ch['hours'], 'no_of_days' => $ch['work_days'], 'comment' => $ch['comment'] ]
                );
            }
        }

        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);
    }
}
