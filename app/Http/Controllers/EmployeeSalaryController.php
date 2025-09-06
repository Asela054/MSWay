<?php

namespace App\Http\Controllers;

use App\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $employees = DB::table('payroll_profiles')
            ->leftJoin('remuneration_profiles AS rp1', function($join) {
                $join->on('rp1.payroll_profile_id', '=', 'payroll_profiles.id')
                     ->where('rp1.remuneration_id', '=', 2);
            })
            ->leftJoin('remuneration_profiles AS rp2', function($join) {
                $join->on('rp2.payroll_profile_id', '=', 'payroll_profiles.id')
                     ->where('rp2.remuneration_id', '=', 26);
            })
            ->select(
                'payroll_profiles.basic_salary',
                DB::raw('IFNULL(rp1.new_eligible_amount, 0) as br1'),
                DB::raw('IFNULL(rp2.new_eligible_amount, 0) as br2'),
                DB::raw('(payroll_profiles.basic_salary + IFNULL(rp1.new_eligible_amount, 0) + IFNULL(rp2.new_eligible_amount, 0)) as total')
            )
            ->where('payroll_profiles.emp_id', $id)
            ->get();

        return view('Employee.viewSalaryDetails', compact('employees', 'id'));
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