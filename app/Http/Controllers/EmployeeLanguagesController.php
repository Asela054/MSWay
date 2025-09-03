<?php

namespace App\Http\Controllers;

use App\EmployeeLanguages;
use Illuminate\Http\Request;

class EmployeeLanguagesController extends Controller
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
     * @param  \App\EmployeeLanguages  $employeeLanguages
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeLanguages $employeeLanguages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeLanguages  $employeeLanguages
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeLanguages $employeeLanguages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeLanguages  $employeeLanguages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeLanguages $employeeLanguages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeLanguages  $employeeLanguages
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeLanguages $employeeLanguages)
    {
        //
    }
}
