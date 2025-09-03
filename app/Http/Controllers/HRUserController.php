<?php

namespace App\Http\Controllers;

use App\HRUser;
use Illuminate\Http\Request;

class HRUserController extends Controller
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
     * @param  \App\HRUser  $hRUser
     * @return \Illuminate\Http\Response
     */
    public function show(HRUser $hRUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HRUser  $hRUser
     * @return \Illuminate\Http\Response
     */
    public function edit(HRUser $hRUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HRUser  $hRUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HRUser $hRUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HRUser  $hRUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(HRUser $hRUser)
    {
        //
    }
}
