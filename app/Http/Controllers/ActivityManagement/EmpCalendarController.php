<?php

namespace App\Http\Controllers\ActivityManagement;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\Services\ActivityManagement\EmpCalendarService;

class EmpCalendarController extends Controller
{
    protected $empCalendarService;

    public function __construct(EmpCalendarService $empCalendarService)
    {
        $this->middleware('auth');
        $this->empCalendarService = $empCalendarService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('emp-calendar-list');
        if (!$permission) {
            abort(403);
        }

        $empCalendar = $this->empCalendarService->getAllShedules();
        return view('ActivityManagement.empCalendar', compact('empCalendar'));
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('emp-calendar-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'employee' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->empCalendarService->store($request->only(['employee', 'title', 'date', 'time', 'remark']));

        return response()->json(['success' => 'Employee Calendar Added Successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('emp-calendar-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = $this->empCalendarService->find($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('emp-calendar-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'employee' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->empCalendarService->update(
            $request->hidden_id,
            $request->only(['employee', 'title', 'date', 'time', 'remark'])
        );

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('emp-calendar-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->empCalendarService->destroy($id);
    }
}
