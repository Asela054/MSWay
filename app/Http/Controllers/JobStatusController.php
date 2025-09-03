<?php

namespace App\Http\Controllers;

use App\JobStatus;
use Illuminate\Http\Request;

class JobStatusController extends Controller
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
     * @param  \App\JobStatus  $jobStatus
     * @return \Illuminate\Http\Response
     */
    public function show(JobStatus $jobStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JobStatus  $jobStatus
     * @return \Illuminate\Http\Response
     */
    public function edit(JobStatus $jobStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JobStatus  $jobStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobStatus $jobStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JobStatus  $jobStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobStatus $jobStatus)
    {
        //
    }
}
