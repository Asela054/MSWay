<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use Session;

class EmployeeAbsentController extends Controller
{
    public function employee_absent_report()
    {
        $permission = Auth::user()->can('employee-absent-report');
        if (!$permission) {
            abort(403);
        }

        if (!Session::has('company_name')) {
            $company_name = DB::table('companies')->value('name');
            Session::put('company_name', $company_name);
        } else {
            $company_name = Session::get('company_name');
        }

        $departments=DB::table('departments')->select('*')->get();
        return view('Report.employee_absent_report',compact('departments','company_name'));
    }


    public function get_absent_employees(Request $request)
{
    $selectdatefrom = Carbon::parse($request->input('selectdatefrom'));
    $selectdateto = Carbon::parse($request->input('selectdateto'));
    $department = $request->input('department');

    $absentEmployeesByDate = [];

    if($department=='All'){

        $employeedata= DB::table('employees')
        ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->select('employees.emp_id', 'employees.emp_name_with_initial', 'employees.calling_name', 'employees.emp_department','departments.name AS departmentname','branches.location AS location') 
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->get();
        
        $employeeMap = [];
        foreach ($employeedata as $employee) {
            $employeeMap[$employee->emp_id] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'calling_name' => $employee->calling_name,
                'emp_department' => $employee->emp_department,
                'departmentname' => $employee->departmentname,
                'location' => $employee->location
            ];
        }

        for ($date = $selectdatefrom; $date->lte($selectdateto); $date->addDay()) {
            $attendances = DB::table('attendances')
                ->leftJoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
                ->select('employees.emp_id')
                ->whereDate('attendances.date', $date->format('Y-m-d'))
                ->groupBy('attendances.date', 'attendances.emp_id')
                ->pluck('employees.emp_id')
                ->toArray();
               
            $absentEmployees = array_filter($employeeMap, function ($employee) use ($attendances) {
                return !in_array($employee['emp_id'], $attendances);
            });

            $absentEmployeesByDate[$date->format('Y-m-d')] = $absentEmployees;
        }

    }else if($department!='All'){

        $employeedata= DB::table('employees')
        ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->select('employees.emp_id', 'employees.emp_name_with_initial', 'employees.calling_name', 'employees.emp_department','departments.name AS departmentname','branches.location AS location') 
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->where('employees.emp_department', '=', $department)
        ->get();

        $employeeMap = [];
        foreach ($employeedata as $employee) {
            $employeeMap[$employee->emp_id] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'calling_name' => $employee->calling_name,
                'emp_department' => $employee->emp_department,
                'departmentname' => $employee->departmentname,
                'location' => $employee->location
            ];
        }

        for ($date = $selectdatefrom; $date->lte($selectdateto); $date->addDay()) {
            $attendances = DB::table('attendances')
                ->leftJoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
                ->select('employees.emp_id')
                ->whereDate('attendances.date', $date->format('Y-m-d'))
                ->where('employees.emp_department', '=', $department)
                ->groupBy('attendances.date', 'attendances.emp_id')
                ->pluck('employees.emp_id')
                ->toArray();
               
            $absentEmployees = array_filter($employeeMap, function ($employee) use ($attendances) {
                return !in_array($employee['emp_id'], $attendances);
            });

            $absentEmployeesByDate[$date->format('Y-m-d')] = $absentEmployees;
        }
    }

    $absentEmployeesForTable = [];
    foreach ($absentEmployeesByDate as $date => $absentEmployees) {
        foreach ($absentEmployees as $absentEmployee) {
            $absentEmployeesForTable[] = [
                'date' => $date,
                'emp_id' => $absentEmployee['emp_id'],
                'emp_name_with_initial' => $absentEmployee['emp_name_with_initial'],
                'calling_name' => $absentEmployee['calling_name'],
                'departmentname' => $absentEmployee['departmentname'],
                'location' => $absentEmployee['location']
            ];
        }
    }

    return Datatables::of($absentEmployeesForTable)
        ->addIndexColumn()
        ->addColumn('employee_display', function ($row) {
            return EmployeeHelper::getDisplayName((object)$row);
        })
            ->filterColumn('employee_display', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('employees.emp_name_with_initial', 'like', "%{$keyword}%")
                    ->orWhere('employees.calling_name', 'like', "%{$keyword}%")
                    ->orWhere('employees.emp_id', 'like', "%{$keyword}%");
                });
            })
        ->addColumn('action', function ($row) {
        })
        ->rawColumns(['action'])
        ->make(true);
}
    
    // public function get_absent_employees(Request $request)
    // {
    //     // $selectdatefrom = $request->input('selectdatefrom');
    //     // $selectdateto = $request->input('selectdateto');
    //     $selectdatefrom = Carbon::parse($request->input('selectdatefrom'));
    //     $selectdateto = Carbon::parse($request->input('selectdateto'));

    //     $department = $request->input('department');

    //     $absentEmployeesByDate = [];

    //     if($department=='All'){

    //         $employeedata= DB::table('employees')
    //         ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
    //         ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
    //         ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department','departments.name AS departmentname','branches.location AS location') 
    //         ->where('deleted', 0)
    //         ->where('is_resigned', 0)
    //         ->get();
            
    //     $employeeMap = [];
    //     foreach ($employeedata as $employee) {
    //         $employeeMap[$employee->emp_id] = [
    //             'emp_id' => $employee->emp_id,
    //             'emp_name_with_initial' => $employee->emp_name_with_initial,
    //             'emp_department' => $employee->emp_department,
    //             'departmentname' => $employee->departmentname,
    //             'location' => $employee->location
    //         ];
    //     }

    //         for ($date = $selectdatefrom; $date->lte($selectdateto); $date->addDay()) {
    //             $attendances = DB::table('attendances')
    //                 ->leftJoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
    //                 ->select('employees.emp_id')
    //                 ->whereDate('attendances.date', $date->format('Y-m-d'))
    //                 ->groupBy('attendances.date', 'attendances.emp_id')
    //                 ->pluck('employees.emp_id')
    //                 ->toArray();
                   
    //             $absentEmployees = array_filter($employeeMap, function ($employee) use ($attendances) {
             
    //                 return !in_array($employee['emp_id'], $attendances);
    //             });

    //             $absentEmployeesByDate[$date->format('Y-m-d')] = $absentEmployees;
    //         }

    //     }else if($department!='All'){

    //         $employeedata= DB::table('employees')
    //         ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
    //         ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
    //         ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department','departments.name AS departmentname','branches.location AS location') 
    //         ->where('deleted', 0)
    //         ->where('is_resigned', 0)
    //         ->where('employees.emp_department', '=', $department)
    //         ->get();

    //         $employeeMap = [];
    //         foreach ($employeedata as $employee) {
    //             $employeeMap[$employee->emp_id] = [
    //                 'emp_id' => $employee->emp_id,
    //                 'emp_name_with_initial' => $employee->emp_name_with_initial,
    //                 'emp_department' => $employee->emp_department,
    //                 'departmentname' => $employee->departmentname,
    //                 'location' => $employee->location
    //             ];
    //         }
    
    //             for ($date = $selectdatefrom; $date->lte($selectdateto); $date->addDay()) {
    //                 $attendances = DB::table('attendances')
    //                     ->leftJoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
    //                     ->select('employees.emp_id')
    //                     ->whereDate('attendances.date', $date->format('Y-m-d'))
    //                     ->where('employees.emp_department', '=', $department)
    //                     ->groupBy('attendances.date', 'attendances.emp_id')
    //                     ->pluck('employees.emp_id')
    //                     ->toArray();
                       
    //                 $absentEmployees = array_filter($employeeMap, function ($employee) use ($attendances) {
                 
    //                     return !in_array($employee['emp_id'], $attendances);
    //                 });
    
    //                 $absentEmployeesByDate[$date->format('Y-m-d')] = $absentEmployees;
    //             }
    //     }
 
    //     $absentEmployeesForTable = [];
    //     foreach ($absentEmployeesByDate as $date => $absentEmployees) {
    //         foreach ($absentEmployees as $absentEmployee) {
    //             $absentEmployeesForTable[] = [
    //                 'date' => $date,
    //                 'emp_id' => $absentEmployee['emp_id'],
    //                 'emp_name_with_initial' => $absentEmployee['emp_name_with_initial'],
    //                 'departmentname' => $absentEmployee['departmentname'],
    //                 'location' => $absentEmployee['location']
    //             ];
    //         }
    //     }


    //         return Datatables::of($absentEmployeesForTable)
    //         ->addIndexColumn()
    //         ->addColumn('action', function ($row) {
    //         })
    //         ->rawColumns(['action'])
    //         ->make(true);

    //         // return response() ->json(['result'=>  $types]);
    // }
}
