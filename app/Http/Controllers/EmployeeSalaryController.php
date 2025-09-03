<?php

namespace App\Http\Controllers;

use App\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSalaryController extends Controller
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
     * @param  \App\EmployeeSalary  $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $employees = EmployeeSalary::where('emp_id',$id)->get();
 
        return view('Employee.viewSalaryDetails',compact('employees','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeSalary  $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeSalary $employeeSalary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeSalary  $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeSalary $employeeSalary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeSalary  $employeeSalary
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeSalary $employeeSalary)
    {
        //
    }
}
