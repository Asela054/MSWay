<?php

namespace App\Http\Controllers;

use App\IgnoreDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Carbon\Carbon;

class IgnoreDaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('IgnoreDay-list');
        if (!$permission) {
            abort(403);
        }

        $IgnoreDays = DB::table('ignore_days')
            ->select('ignore_days.*')
            ->get();
        return view('Leave.ignore_days', compact('IgnoreDays'));
    }


    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('IgnoreDay-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = [
            'month' => 'required|date_format:Y-m',
            'selected_dates' => 'required|regex:/^\d{4}-\d{2}-\d{2}(,\d{4}-\d{2}-\d{2})*$/',
        ];

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $month = $request->month;
        $selectedDays = explode(',', $request->selected_dates);

        $monthFormatted = Carbon::createFromFormat('Y-m', $month)->format('Y.m.01');

        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        foreach ($selectedDays as $day) {
            $date = Carbon::createFromFormat('Y-m-d', $day);

            if ($date->lt($monthStart) || $date->gt($monthEnd)) {
                return response()->json(['error' => 'Invalid date selected.'], 400);
            }

            IgnoreDay::create([
                'month' => $monthFormatted, 
                'date' => $date->format('Y-m-d'),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Data Added successfully.']);
    }



    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('IgnoreDay-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = IgnoreDay::findOrFail($id);
        $data->delete();
    }
}
