<?php

namespace App\Http\Controllers;

use App\LeaveDetail;
use Illuminate\Http\Request;

class LeaveDetailController extends Controller
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
     * @param  \App\LeaveDetail  $leaveDetail
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveDetail $leaveDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LeaveDetail  $leaveDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(LeaveDetail $leaveDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LeaveDetail  $leaveDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LeaveDetail $leaveDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LeaveDetail  $leaveDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveDetail $leaveDetail)
    {
        //
    }
}
