<?php

namespace App\Http\Controllers;

use App\EmployeeContract;
use Illuminate\Http\Request;

class EmployeeContractController extends Controller
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
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeContract $employeeContract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeContract $employeeContract)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeContract $employeeContract)
    {
        //
    }
}
