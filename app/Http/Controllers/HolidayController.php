<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\WorkLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('holiday-list');
        if (!$permission) {
            abort(403);
        }

        $worklevel = WorkLevel::orderBy('id', 'asc')->get();
        $holiday = DB::table('holidays')
            ->join('work_levels', 'holidays.work_level', '=', 'work_levels.id')
            ->join('holiday_types', 'holidays.holiday_type', '=', 'holiday_types.id')
            ->select('holidays.*', 'work_levels.level', 'holiday_types.name as holiday_type_name')
            ->get();
        return view('Holiday.holiday', compact('holiday', 'worklevel'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('holiday-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'holiday_name' => 'required',
            'type' => 'required',
            'date' => 'required',
            'work_level' => 'required',
            'half_short' => 'required',
//            'start_time' => 'required_unless:half_short,1.00',
//            'end_time' => 'required_unless:half_short,1.00',

        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'holiday_name' => $request->holiday_name,
            'holiday_type' => $request->type,
            'date' => $request->date,
            'work_level' => $request->work_level,
            'half_short' => $request->half_short,
            'start_time' => $request->start_time ? $request->start_time : null,
            'end_time' => $request->end_time ? $request->end_time : null,

        );

        Holiday::create($form_data);

        return response()->json(['success' => true, 'message' => 'Data Added successfully.']);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('holiday-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = Holiday::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        $user = Auth::user();
        $permission = $user->can('holiday-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'holiday_name' => 'required',
            'type' => 'required',
            'half_short' => 'required',
//            'start_time' => 'required_unless:half_short,1.00',
//            'end_time' => 'required_unless:half_short,1.00',
            'date' => 'required',
            'work_level' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'holiday_name' => $request->holiday_name,
            'holiday_type' => $request->type,
             'half_short'        =>  $request->half_short,
            'start_time'        =>  $request->start_time ? $request->start_time : null,
             'end_time'          =>  $request->end_time ? $request->end_time : null,
            'date' => $request->date,
            'work_level' => $request->work_level
        );

        Holiday::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => true, 'message' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('holiday-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Holiday::findOrFail($id);
        $data->delete();
    }

    public function get_holidays_for_calendar()
    {
        $holiday = DB::table('holidays')
            ->join('work_levels', 'holidays.work_level', '=', 'work_levels.id')
            ->join('holiday_types', 'holidays.holiday_type', '=', 'holiday_types.id')
            ->select('holidays.*', 'work_levels.level', 'holiday_types.name as holiday_type_name')
            ->get();
        return response()->json($holiday);
    }

    public function HolidayCalendar()
    {
        $user = Auth::user();
        $permission = $user->can('holiday-list');
        if (!$permission) {
            abort(403);
        }

        $worklevel = WorkLevel::orderBy('id', 'asc')->get();
        return view('Holiday.holidayCalendar', compact('worklevel'));
    }

    public function HolidayGet(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('holiday-list');
        if (!$permission) {
            abort(403);
        }

        $holiday = DB::table('holidays')
            ->join('work_levels', 'holidays.work_level', '=', 'work_levels.id')
            ->join('holiday_types', 'holidays.holiday_type', '=', 'holiday_types.id')
            ->select('holidays.*', 'work_levels.level', 'holiday_types.name as holiday_type_name')
            ->where('holidays.id', $request->id)
            ->first();

        return response()->json($holiday);
    }

}
