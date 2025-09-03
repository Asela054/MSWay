<?php

namespace App\Http\Controllers;

use App\AttendanceEdited;
use Illuminate\Http\Request;

class AttendanceEditedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AttendanceEdited  $attendanceEdited
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceEdited $attendanceEdited)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AttendanceEdited  $attendanceEdited
     * @return \Illuminate\Http\Response
     */
    public function edit(AttendanceEdited $attendanceEdited)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AttendanceEdited  $attendanceEdited
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttendanceEdited $attendanceEdited)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AttendanceEdited  $attendanceEdited
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceEdited $attendanceEdited)
    {
        //
    }
}
