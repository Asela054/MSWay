<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommenGetrreordController extends Controller
{
    public function getDepartments($company_id)
    {
        $departments = Department::where('company_id', $company_id)->get();
        return response()->json($departments);
    }
}
